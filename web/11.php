<?php

$a = '联合式(514)和(515)，消去尺度参数，可得';
 // $a = preg_replace('/[\x00-\x09\x0B-\x0C\x0E-x1F\x7F-\x9F]/u', '', $a);
  $a = preg_replace('/[\x1A]/u', '', $a);
echo $a;