<?php

include('htmlTPC.php');

$encoding = 'Windows-1251';
$src_file = 'Contents.htm';

$app = new htmlTPC($encoding);
$app->load($src_file);
$app->analyze();
$app->convert('UTF-8');
$app->save();

?>