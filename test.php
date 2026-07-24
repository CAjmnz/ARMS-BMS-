<?php

$url = "http://172.16.161.34/api/rms/monitoring/search/name?q=Christian";

echo "<h3>file_get_contents()</h3>";

$response = @file_get_contents($url);

var_dump($response);