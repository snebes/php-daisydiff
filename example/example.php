<?php

require __DIR__ . '/../vendor/autoload.php';

//use DaisyDiff\DaisyDiff;
//
//$oldText = file_get_contents('old.html');
//$newText = file_get_contents('new.html');
//
//$differ = new DaisyDiff();
//$result = $differ->diff($oldText, $newText);
//
//file_put_contents('output.html', $result);

$isEven = (101 & 1) === 1 ? false : true;
var_dump($isEven);