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

// Include functions
include_once 'tools.php';
include_once 'auth.php';
include_once 'menu.php';


// Print html header
echo $GLOBALS['G_html_header'];

// Print html menu
echo $GLOBALS['G_html_menu'];
echo $GLOBALS['G_html_menu2'];

// Print html content part A
echo $GLOBALS['G_html_main_right_a'];


// Menu
$_module_array=array(
    0=>array("text"=>'<h4 class="list-group-item-heading">Kunden-Registrierung / Test-Auswertung</h4><p class="list-group-item-text">TESTKARTE QR Code scannen</p>',"link"=>"scan.php","role"=>array(0,0,0,4),"role-disabled"=>array(0,0,0,0)),
    10=>array("text"=>'<h4 class="list-group-item-heading">Liste an Tests</h4><p class="list-group-item-text">Aktive Tests</p>',"link"=>"testlist.php","role"=>array(0,0,0,4),"role-disabled"=>array(0,0,0,0)),
    30=>array("text"=>'<h4 class="list-group-item-heading">Admin: Web user</h4><p class="list-group-item-text">User-Management</p>',"link"=>"user_admin.php","role"=>array(0,0,0,4),"role-disabled"=>array(0,2,3,0))/* ,
    31=>array("text"=>'<h4 class="list-group-item-heading">Admin: Files</h4><p class="list-group-item-text">Dateien von Reports</p>',"link"=>"downloadlist.php","role"=>array(0,0,0,4),"role-disabled"=>array(0,0,0,0)),
    32=>array("text"=>'<h4 class="list-group-item-heading">Admin: Logs</h4><p class="list-group-item-text">Server-Logs</p>',"link"=>"log.php","role"=>array(0,0,0,4),"role-disabled"=>array(0,0,0,0)) */
);

echo '<div class="row">';
echo '<div class="col-sm-8">
<h3>Modul wählen</h3>
<div class="list-group">';
foreach($_module_array as $key=>$a) {
    $show_entry=false;
    $show_entry_disabled=false;
    foreach($a["role"] as $b) {
        if($b>0 && $_SESSION['roles'][$b]==1) { 
            $show_entry=true;
        }
    }
    foreach($a["role-disabled"] as $b) {
        if($b>0 && $_SESSION['roles'][$b]==1) { 
            $show_entry_disabled=true;
        }
    }
    if($show_entry) { 
        echo '<a class="list-group-item list-group-item-action list-group-item-FAIR" id="module-'.$key.'" href="'.$a["link"].'">'.$a["text"].'</a>';
    } elseif($show_entry_disabled) {
        echo '<a class="list-group-item list-group-item-action list-group-item-FAIR disabled" id="module-'.$key.'" >'.$a["text"].'</a>';
    }
}
echo '</div></div>';

// Print html content part C
echo $GLOBALS['G_html_main_right_c'];
// Print html footer
echo $GLOBALS['G_html_footer'];

?>