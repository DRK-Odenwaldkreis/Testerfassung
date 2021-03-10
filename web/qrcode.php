<?php
/* **************

Websystem für das Testzentrum DRK Odenwaldkreis
Author: Marc S. Duchene
March 2021

QR code generator module

** ************** */

// Include library for QR codes
include_once 'lib/phpqrcode/qrlib.php';

$param = $_GET['id']; // remember to sanitize that - it is user input!
    
// we need to be sure ours script does not output anything!!!
// otherwise it will break up PNG binary!

ob_start("callback");

// here DB request or some processing
$codeText = $param;

// end of processing here
$debugLog = ob_get_contents();
ob_end_clean();

// outputs image directly into browser, as PNG stream
QRcode::png($codeText,false,QR_ECLEVEL_H,6,4);

?>