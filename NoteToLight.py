#Original code: https://github.com/Virtualan/musical-note-trainer/blob/master/NoteTrainer.py
#Modified by Eva
import sys
import random
import math
import os
import pyaudio
from scipy import signal
from socket import *
from random import *
import numpy
from scipy.signal import blackmanharris, fftconvolve
from numpy import argmax, sqrt, mean, diff, log
import time

# See http://www.swharden.com/blog/2013-05-09-realtime-fft-audio-visualization-with-python/
class SoundRecorder:

    def __init__(self):
        self.RATE=48000
        self.BUFFERSIZE=3072 #1024 is a good buffer size 3072 works for Pi
        self.secToRecord=.05
        self.threadsDieNow=False
        self.newAudio=False

    def setup(self):
        self.buffersToRecord=int(self.RATE*self.secToRecord/self.BUFFERSIZE)
        if self.buffersToRecord==0: self.buffersToRecord=1
        self.samplesToRecord=int(self.BUFFERSIZE*self.buffersToRecord)
        self.chunksToRecord=int(self.samplesToRecord/self.BUFFERSIZE)
        self.secPerPoint=1.0/self.RATE
        self.p = pyaudio.PyAudio()
        self.inStream = self.p.open(format=pyaudio.paInt16,channels=1,rate=self.RATE,input=True,frames_per_buffer=self.BUFFERSIZE)
        self.xsBuffer=numpy.arange(self.BUFFERSIZE)*self.secPerPoint
        self.xs=numpy.arange(self.chunksToRecord*self.BUFFERSIZE)*self.secPerPoint
        self.audio=numpy.empty((self.chunksToRecord*self.BUFFERSIZE),dtype=numpy.int16)

    def close(self):
        self.p.close(self.inStream)

    def getAudio(self):
        audioString=self.inStream.read(self.BUFFERSIZE)
        self.newAudio=True
        return numpy.fromstring(audioString,dtype=numpy.int16)


def find(condition):
    res, = numpy.nonzero(numpy.ravel(condition))
    return res

# See https://github.com/endolith/waveform-analyzer/blob/master/frequency_estimator.py
def parabolic(f, x):
    xv = 1/2. * (f[x-1] - f[x+1]) / (f[x-1] - 2 * f[x] + f[x+1]) + x
    yv = f[x] - 1/4. * (f[x-1] - f[x+1]) * (xv - x)
    return (xv, yv)

# See https://github.com/endolith/waveform-analyzer/blob/master/frequency_estimator.py
def freq_from_autocorr(raw_data_signal, fs):
    corr = fftconvolve(raw_data_signal, raw_data_signal[::-1], mode='full')
    corr = corr[int(len(corr)/2):]
    d = diff(corr)
    start = find(d > 0)[0]
    peak = argmax(corr[start:]) + start
    px, py = parabolic(corr, peak)
    return fs / px

def loudness(chunk):
    data = numpy.array(chunk, dtype=float) / 32768.0
    ms = math.sqrt(numpy.sum(data ** 2.0) / len(data))
    if ms < 10e-8: ms = 10e-8
    return 10.0 * math.log(ms, 10.0)



def find_nearest(array, value):
    index = (numpy.abs(array - value)).argmin()
    return array[index]

def closest_value_index(array, guessValue):
    # Find closest element in the array, value wise
    closestValue = find_nearest(array, guessValue)
    # Find indices of closestValue
    indexArray = numpy.where(array==closestValue)
    # Numpys 'where' returns a 2D array with the element index as the value
    return indexArray[0][0]

def build_default_tuner_range():

    return {65.41:'C2',
            69.30:'C2#',
            73.42:'D2',
            77.78:'E2b',
            82.41:'E2',
            87.31:'F2',
            92.50:'F2#',
            98.00:'G2',
            103.80:'G2#',
            110.00:'A2',
            116.50:'B2b',
            123.50:'B2',
            130.80:'C3',
            138.60:'C3#',
            146.80:'D3',
            155.60:'E3b',
            164.80:'E3',
            174.60:'F3',
            185.00:'F3#',
            196.00:'G3',
            207.70:'G3#',
            220.00:'A3',
            233.10:'B3b',
            246.90:'B3',
            261.60:'C4',
            277.20:'C4#',
            293.70:'D4',
            311.10:'E4b',
            329.60:'E4',
            349.20:'F4',
            370.00:'F4#',
            392.00:'G4',
            415.30:'G4#',
            440.00:'A4',
            466.20:'B4b',
            493.90:'B4',
            523.30:'C5',
            554.40:'C5#',
            587.30:'D5',
            622.30:'E5b',
            659.30:'E5',
            698.50:'F5',
            740.00:'F5#',
            784.00:'G5',
            830.60:'G5#',
            880.00:'A5',
            932.30:'B5b',
            987.80:'B5',
            1047.00:'C6',
            1109.0:'C6#',
            1175.0:'D6',
            1245.0:'E6b',
            1319.0:'E6',
            1397.0:'F6',
            1480.0:'F6#',
            1568.0:'G6',
            1661.0:'G6#',
            1760.0:'A6',
            1865.0:'B6b',
            1976.0:'B6',
            2093.0:'C7'
            }

def note_to_brightness(freq_index):
    ''' return Hue API brightness
    '''
    # There are 61 music notes in the dictionary
    # The API has brightness range 1-254
    brightness = 100+ round((freq_index+1)*4.16) #most of music has node lower than C5, so add 100 to bump up brightness
    return min(254, brightness)

class NoteToLight:
    def main(self):
        """ Output ligh control JSON every n second
        This function take audio and analyze sound frequency.
        It takes average frequency in n second and adjust the brightness of light accordingly.
        """
        n = 5
        inputnote = 1
        music_end = False

        # Build frequency, noteName dictionary
        tunerNotes = build_default_tuner_range()

        # Sort the keys and turn into a numpy array for logical indexing
        frequencies = numpy.array(sorted(tunerNotes.keys()))

        soundgate = 19                             # zero is loudest possible input level
        SR=SoundRecorder() #recording audio

        print('Start recording...')

        start_time = time.time()
        targetnote_list = []

        while True:

            SR.setup()
            raw_data_signal = SR.getAudio()
            signal_level = round(abs(loudness(raw_data_signal)),2)
            try:
                inputnote = round(freq_from_autocorr(raw_data_signal,SR.RATE),2)    #### find the freq from the audio sample

            except:
                inputnote == 0

            if SR.inStream.is_stopped():
                break
            SR.close()



            if signal_level > soundgate:                                 #### basic noise gate to stop it guessing ambient noises
                continue

            if inputnote > frequencies[len(tunerNotes)-1]:                        #### not interested in notes above the notes list
                continue

            if inputnote < frequencies[0]:                                     #### not interested in notes below the notes list
                continue

            current_time = time.time()
            if current_time - start_time > n:       #### every n seconds
                start_time = current_time           #### reset start time,

                if targetnote_list:
                    avg_note = closest_value_index(frequencies, round(sum(targetnote_list)/len(targetnote_list), 2))   #### get average note over n sec,
                    targetnote_list.clear()             #### clear note list,
                    print(note_to_brightness(avg_note))
                    #print(tunerNotes[frequencies[avg_note]])

            # targetnote = closest_value_index(frequencies, round(inputnote, 2))
            # print(time.time()-start_time)
            targetnote_list.append(inputnote)


NoteToLight().main()
