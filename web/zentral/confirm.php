<?php

/* **************

Websystem für das Impf- und Testzentrum DRK Odenwaldkreis
Author: Marc S. Duchene
September 2021

Confirm module - only for Impfzentrum

** ************** */

include_once 'preload.php';
if( isset($GLOBALS['G_sessionname']) ) { session_name ($GLOBALS['G_sessionname']); }
session_start();
$sec_level=1;
$current_site="confirm";

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

// role check
if( A_checkpermission(array(1,0,0,4,0)) ) {

  // Open database connection
  $Db=S_open_db();


  if( isset($_GET['id']) ) {
    // ///////////////
    // ID verarbeiten
    // ///////////////

    $token=$_GET['id'];

    if( S_get_entry($Db,'SELECT Used FROM Voranmeldung WHERE Token=\''.$token.'\';')==0 ) {

      S_set_data($Db,'UPDATE Voranmeldung SET Used=1 WHERE Token=\''.$token.'\';');
      echo '<div class="row">';
      echo '<div class="col-sm-12">
      <h3>Person wurde bestätigt. Das Fenster kann geschlossen werden.</h3>
      </div>';
      
    } else {

      // ///////////////
      // ID  ungültig
      // ///////////////
      echo '<div class="row">';
      echo '<div class="col-sm-12">
      <h3>Person wurde bereits bestätigt oder Vorgang ungültig!</h3>
      </div>';

    }
  } else {
    // ///////////////
      // ID  ungültig
      // ///////////////
      echo '<div class="row">';
      echo '<div class="col-sm-12">
      <h3>Vorgang ungültig!</h3>
      </div>';
  }

  // Close connection to database
  S_close_db($Db);

} else {
  echo '<h1>KEINE BERECHTIGUNG</h1>';
}

// Print html content part C
echo $GLOBALS['G_html_main_right_c'];
// Print html footer
echo $GLOBALS['G_html_footer'];

?>