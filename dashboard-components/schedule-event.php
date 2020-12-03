<section id="contact">
  <div class="container">
    <div class="row">
      <div class="col-lg-8 mx-auto text-center">
        <h2 class="section-heading">Schedule</h2>
        <hr class="my-4">
      </div>
    </div>

    <!--<div class="row">-->
      <!-- jQuery Calender: https://www.jqueryscript.net/time-clock/Pretty-Event-Calendar-&-Datepicker-Plugin-For-jQuery-Calendar-js.html-->

      <div class="schedule-event-section row">

        <div id = "schedule-event-left col-sm-6 col-md-6 col-lg-6" align="center"> <!--style = "justify-content: center; display: flex; flex-direction:column;"-->
          <div id="calendar-container" style = "width: 100%;"></div>
          <div id="color-picker2" class="cp-default" style="margin-top: 150px;"></div>
          <?php
            echo '<script type="text/javascript">',
                 'var hue = 0;',
                 'var bri = 200;',
                 'ColorPicker(',
                        'document.getElementById("color-picker2"),',
                        'function(hex, hsv, rgb) {',
                          'hue = 65535 * hsv.h / 360;',
                          'bri = 254 * hsv.v;',
                          'var elem = document.getElementById("color-display");',
                          'elem.style.backgroundColor = hex;',
                          'elem.value = hue;',
                 '});',
                 '</script>';

           ?>
        </div>

        <div class="schedule-event-right col-sm-6 col-md-6 col-lg-6" align="center">

          <form name = "scheduleEventForm" action="process/schedule-event-process.php" method="POST" onsubmit="return validateForm()">
            <div class="input_section">
              <input id = "selected-date" name = "selected-date" placeholder = "Date"></input><br/>
              <p id = "selected-date-error" class = "text-primary"></p>

              <input id = "datetime" name = "datetime"  type="hidden">

              <div class="time-section">
                <input id = "selected-time" name = "selected-time" placeholder = "Time hh:mm" ></input>
                <label class="switch">
                  <input type="checkbox" id = "ampm" onclick="toggle(this)">
                  <div class="slider round">
                    <span class="on">AM</span>
                    <span class="off">PM</span>
                  </div>
                </label>
                <p id = "selected-time-error" class = "text-primary"></p>
              </div>

              <input id = "event-name" name = "event-name" placeholder = "Name of Event"></input><br/>
              <p id = "selected-event-error" class = "text-primary"></p>

              <label style = "font-family:'Open Sans'; flow: left;">
                <input id = "recurrence" name = "recurrence" style = "width:180px;" value="0"> </input>
                Years Recurrence
              </label>
            </div>

            <div class = "song-section">
              <div class="random-label">
                <p class="random-label-p">Random</p>
              </div>

              <label class="switch">
                <input type="checkbox" id="song-or-url" class="togBtn" onclick="random_song(this)" >
                <div class="slider round">
                  <span class="on">ON</span>
                  <span class="off">OFF</span>
                </div>
              </label>
              <br/>
              <input class="song-input" id = "song-input" name = "song-input" placeholder = "Theme for music">
              <input class="song-input" id = "url-input" name = "url-input" type = "hidden">
              <p id = "selected-song-error" class = "text-primary"></p>
            </div>

            <div class = "color-section" style = "margin-top: 150px;">
              <div class="random-label">
                <p class="random-label-p">Random Color</p>
              </div>
              <label class="switch">
                <input type="checkbox" id="random-color" name = "random-color" value = "false" class="togBtn" checked onclick="random_color(this)">
                <div class="slider round">
                  <span class="on">ON</span>
                  <span class="off">OFF</span>
                </div>
              </label>
              <br/>

              <input id = "color-display" style = "width:200px;"></input>
              <button class="btn btn-xl js-scroll-trigger" type = "button" onclick="add_color()">Add</button><br/>
              <input id = "hue" name = "hue" class = "set_color" placeholder = "color1"></input><br/>
              <p id = "selected-color-error" class = "text-primary"></p>
              <input id = "hue2" name = "hue2" class = "set_color" placeholder = "color2"></input><br/>
              <input id = "hue3" name = "hue3" class = "set_color" placeholder = "color3"></input><br/>
              <button class="btn btn-xl js-scroll-trigger" type = "button" onclick="clear_all_color()">Clear</button><br/><br/>
            </div>
            <button class="btn btn-light btn-xl js-scroll-trigger" type = "submit">Submit</button>
          </form>
        </div>
      </div>

  </div>


</section>
<hr class="my-4">
<section id="contact">
  <div class="row">
    <div class="col-lg-8 mx-auto text-center">
    <?php
    include('./process/fetch-events-process.php');
    echo '<table class="table table-bordered">';
    echo '<tr>';
    echo '<th>Event</th><th>Date</th><th>Time</th>';
    echo '</tr>';
    foreach($_SESSION['events'] as $key => $value){
      echo '<tr>';
      echo '<td>'.$value['event'].'</td>';
      echo '<td>'.gmdate("Y/m/d", $value['datetime']).'</td>';
      echo '<td>'.gmdate("H:i", $value['datetime']).'</td>';
      //echo '<td>'.$value['time'].'</td>';
      //echo '<td>'.$value['colors'].'</td>';
      echo '</tr>';
    }
    echo '</table>';
     ?>
   </div>
  </div>
</section>
