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
    0=>array("text"=>'<h4 class="list-group-item-heading">Kunden-Registrierung / Test-Auswertung</h4><p class="list-group-item-text">TESTKARTE QR Code scannen</p>',"link"=>"scan.php","role"=>array(1,0,0,4),"role-disabled"=>array(0,0,0,0)),
    10=>array("text"=>'<h4 class="list-group-item-heading">Liste an Tests</h4><p class="list-group-item-text">Aktive Tests und Export CSV</p>',"link"=>"testlist.php","role"=>array(1,2,3,4),"role-disabled"=>array(0,0,0,0)),
    20=>array("text"=>'<h4 class="list-group-item-heading">Testkarten</h4><p class="list-group-item-text">Erstellung von neuen Testkarten</p>',"link"=>"testkarten.php","role"=>array(0,2,0,4),"role-disabled"=>array(0,0,0,0)),
    30=>array("text"=>'<h4 class="list-group-item-heading">Admin: Web user</h4><p class="list-group-item-text">User-Management</p>',"link"=>"user_admin.php","role"=>array(0,0,0,4),"role-disabled"=>array(0,2,0,0)),
    /*32=>array("text"=>'<h4 class="list-group-item-heading">Admin: Logs</h4><p class="list-group-item-text">Server-Logs</p>',"link"=>"log.php","role"=>array(0,0,0,4),"role-disabled"=>array(0,0,0,0)) */
    33=>array("text"=>'<h4 class="list-group-item-heading">Admin: Files</h4><p class="list-group-item-text">Dateien</p>',"link"=>"downloadlist.php","role"=>array(0,0,0,4),"role-disabled"=>array(0,0,0,0)),
    34=>array("text"=>'<h4 class="list-group-item-heading">Admin: Logs</h4><p class="list-group-item-text">Übersicht der Logs</p>',"link"=>"log.php","role"=>array(0,0,0,4),"role-disabled"=>array(0,0,0,0)),
    99=>array("text"=>'<h4 class="list-group-item-heading">Öffentliche Startseite Testzentrum</h4><p class="list-group-item-text"></p>',"link"=>"../index.php","role"=>array(1,2,3,4),"role-disabled"=>array(0,0,0,0))
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
echo '</div>';


// Test statistics

// Open database connection
$Db=S_open_db();
$today=date("Y-m-d",time());
$yesterday=date("Y-m-d",time() - 60 * 60 * 24);
$beforetwodays=date("Y-m-d",time() - 2 * 60 * 60 * 24);
$stat_val_total_day=S_get_entry($Db,'SELECT count(Ergebnis) From Vorgang WHERE Date(Ergebniszeitpunkt)=\''.$today.'\';');
$stat_val_neg_day=S_get_entry($Db,'SELECT count(Ergebnis) From Vorgang WHERE Date(Ergebniszeitpunkt)=\''.$today.'\'AND Ergebnis=2;');
$stat_val_pos_day=S_get_entry($Db,'SELECT count(Ergebnis) From Vorgang WHERE Date(Ergebniszeitpunkt)=\''.$today.'\' AND Ergebnis=1;');
$stat_val_total_yday=S_get_entry($Db,'SELECT count(Ergebnis) From Vorgang WHERE Date(Ergebniszeitpunkt)=\''.$yesterday.'\';');
$stat_val_neg_yday=S_get_entry($Db,'SELECT count(Ergebnis) From Vorgang WHERE Date(Ergebniszeitpunkt)=\''.$yesterday.'\'AND Ergebnis=2;');
$stat_val_pos_yday=S_get_entry($Db,'SELECT count(Ergebnis) From Vorgang WHERE Date(Ergebniszeitpunkt)=\''.$yesterday.'\' AND Ergebnis=1;');
$stat_val_total_2day=S_get_entry($Db,'SELECT count(Ergebnis) From Vorgang WHERE Date(Ergebniszeitpunkt)=\''.$beforetwodays.'\';');
$stat_val_neg_2day=S_get_entry($Db,'SELECT count(Ergebnis) From Vorgang WHERE Date(Ergebniszeitpunkt)=\''.$beforetwodays.'\'AND Ergebnis=2;');
$stat_val_pos_2day=S_get_entry($Db,'SELECT count(Ergebnis) From Vorgang WHERE Date(Ergebniszeitpunkt)=\''.$beforetwodays.'\' AND Ergebnis=1;');
// Close connection to database
S_close_db($Db);

echo '<div class="row">';
echo '<div class="col-sm-4">
<div class="alert alert-info" role="alert">
<p>Getestete Personen</p>
<h3>'.$stat_val_total_day.' <span class="FAIR-text-sm">(heute)</span></h3>
<h3>'.$stat_val_total_yday.' <span class="FAIR-text-sm">(gestern)</span></h3>
<h3>'.$stat_val_total_2day.' <span class="FAIR-text-sm">(vorgestern)</span></h3>
</div>';

echo '</div>';
echo '<div class="col-sm-4">
<div class="alert alert-success" role="alert">
<p>Negative Fälle</p>
<h3>'.$stat_val_neg_day.' <span class="FAIR-text-sm">(heute)</span></h3>
<h3>'.$stat_val_neg_yday.' <span class="FAIR-text-sm">(gestern)</span></h3>
<h3>'.$stat_val_neg_2day.' <span class="FAIR-text-sm">(vorgestern)</span></h3>
</div>';

echo '</div>';
echo '<div class="col-sm-4">
<div class="alert alert-danger" role="alert">
<p>Positive Fälle</p>
<h3>'.$stat_val_pos_day.' ('.(number_format(($stat_val_pos_day/$stat_val_total_day*100),2,',','.')).' %) <span class="FAIR-text-sm">(heute)</span></h3>
<h3>'.$stat_val_pos_yday.' ('.(number_format(($stat_val_pos_yday/$stat_val_total_yday*100),2,',','.')).' %) <span class="FAIR-text-sm">(gestern)</span></h3>
<h3>'.$stat_val_pos_2day.' ('.(number_format(($stat_val_pos_2day/$stat_val_total_2day*100),2,',','.')).' %) <span class="FAIR-text-sm">(vorgestern)</span></h3>
</div>';

echo '</div>';


echo '</div></div>';



// Print html content part C
echo $GLOBALS['G_html_main_right_c'];
// Print html footer
echo $GLOBALS['G_html_footer'];

?>