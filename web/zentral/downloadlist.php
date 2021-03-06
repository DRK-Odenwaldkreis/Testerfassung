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
$current_site="downloadlist";

// Include functions
include_once 'tools.php';
include_once 'auth.php';
include_once 'menu.php';

// role check
if( A_checkpermission(array(0,0,0,4,0)) ) {


    // Print html header
    echo $GLOBALS['G_html_header'];

    // Print html menu
    echo $GLOBALS['G_html_menu'];
    echo $GLOBALS['G_html_menu2'];

    // Print html content part A
    echo $GLOBALS['G_html_main_right_a'];

    echo '<h1>Files</h1>';

    echo '<h3>Reports/</h3>';
    echo '<p>';
    //Get list of files
    $log_path="/home/webservice/Reports/";
    $array_files=scandir($log_path);
    foreach($array_files as $a) {
        if( preg_match('/.pdf/',$a) || preg_match('/.zip/',$a) || preg_match('/.csv/',$a) ) {
            echo '<a href="download.php?dir=r&file='.$a.'">'.$a.'</a><br>';
        }
        
    }
    echo '</p>';

    echo '<h3>Testkarten/</h3>';
    echo '<p>';
    //Get list of files
    $log_path="/home/webservice/Testkarten/";
    $array_files=scandir($log_path);
    foreach($array_files as $a) {
        if( preg_match('/.pdf/',$a) || preg_match('/.zip/',$a) ) {
            echo '<a href="download.php?dir=t&file='.$a.'">'.$a.'</a><br>';
        }
        
    }
    echo '</p>';


} else {
    // Print html header
    echo $GLOBALS['G_html_header'];

    // Print html menu
    echo $GLOBALS['G_html_menu'];
    echo $GLOBALS['G_html_menu2'];

    // Print html content part A
    echo $GLOBALS['G_html_main_right_a'];
    echo '<h1>KEINE BERECHTIGUNG</h1>';
}

// Print html content part C
echo $GLOBALS['G_html_main_right_c'];
// Print html footer
echo $GLOBALS['G_html_footer'];

?>