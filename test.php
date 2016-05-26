<?php

include('htmlDocument.php');

$src_file_name = 'Contents.htm';
$res_file_name = 'Result.html';


$wDoc = new htmlDocument();
$wDoc->loadHTMLFile($src_file_name);
$wDoc->convertEncoding('UTF-8');
file_put_contents($res_file_name, $wDoc->saveHTML());

?>

