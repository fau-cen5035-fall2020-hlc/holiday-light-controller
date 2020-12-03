<?php
$mailjetApiKey = '060e5bbb073fb3361615a71c7909d503';
$mailjetApiSecret = '5563225c93b08e0931b6efd0ead94a83';
$messageData = [
    'Messages' => [
        [
            'From' => [
                'Email' => 'no-reply@holiday-light-controller.ue.r.appspot.com',
                'Name' => 'Holiday Light Controller'
            ],
            'To' => [
                [
                    'Email' => 'lewitz@gmail.com',
                    'Name' => 'Kevin Lewitzke'
                ]
            ],
            'Subject' => 'Holiday Light Controller OTP',
            'TextPart' => 'Test'
        ]
    ]
]; 
$jsonData = json_encode($messageData);
$ch = curl_init('https://api.mailjet.com/v3.1/send');
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
curl_setopt($ch, CURLOPT_USERPWD, "{$mailjetApiKey}:{$mailjetApiSecret}");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Content-Length: ' . strlen($jsonData)
]);
$response = json_decode(curl_exec($ch));
var_dump($response);