<?php

/* **************

Websystem für das Testzentrum DRK Odenwaldkreis
Author: Marc S. Duchene
March 2021

** ************** */

include_once 'preload.php';
if( isset($GLOBALS['G_sessionname']) ) { session_name ($GLOBALS['G_sessionname']); }
session_start();
$sec_level=1;
$current_site="cwa_qr";

// Include functions
include_once 'tools.php';
include_once 'auth.php';

// role check
$bool_no_permission=false;
if( A_checkpermission(array(1,2,0,4,5)) ) {
    $bool_no_permission=false;
    if ($_SERVER['REQUEST_METHOD'] == 'GET') {
        $test_id=$_GET['i'];
        // Open database connection
        $Db=S_open_db();
        $cwa_base64=S_get_cwa_qr_code($Db,$test_id);
        $vname=S_get_entry($Db,'SELECT Vorname FROM Vorgang WHERE id=CAST('.$test_id.' AS int);');
        $nname=S_get_entry($Db,'SELECT Nachname FROM Vorgang WHERE id=CAST('.$test_id.' AS int);');
        // Close connection to database
        S_close_db($Db);
    }
} else {
    $bool_no_permission=true;
}


// Print html header
echo $GLOBALS['G_html_header'];
  
// Print html menu
echo $GLOBALS['G_html_menu'];
echo $GLOBALS['G_html_menu2'];

// Print html content part A
echo $GLOBALS['G_html_main_right_a'];


if($bool_no_permission) {
    echo '<h1>KEINE BERECHTIGUNG</h1>';
} else {
    echo '<div class="row">
    <div class="col-sm-12"><h4>CWA QR-Code für Kunde</h4>';
    echo '<img src="qrcode.php?cwa='.$cwa_base64.'" />';
    echo '<h4>Kunde: <b>'.$vname.' '.$nname.'</b></h4>';
    echo '</div></div>';
}

// Print html content part C
echo $GLOBALS['G_html_main_right_c'];
// Print html footer
echo $GLOBALS['G_html_footer'];
?>