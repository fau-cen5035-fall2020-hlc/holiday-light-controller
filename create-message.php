<?php
echo(json_encode(array('messages' => array('data' => base64_encode(json_encode(array('Lights' => array('on' => true, 'hue' => 0, 'effect' => 'none', 'bri' => 100, 'sat' => 100, 'ct' => 500))))))));
?>