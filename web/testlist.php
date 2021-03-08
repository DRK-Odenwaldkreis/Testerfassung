<?php

/* **************

Websystem für das Testzentrum DRK Odenwaldkreis
Author: Marc S. Duchene
March 2021

Scan module

** ************** */

include_once 'preload.php';
if( isset($GLOBALS['G_sessionname']) ) { session_name ($GLOBALS['G_sessionname']); }
session_start();
$sec_level=1;
$current_site="testlist";

// Include functions
include_once 'tools.php';
include_once 'auth.php';
include_once 'menu.php';

// role check
if( A_checkpermission(array(0,0,0,4)) ) {


  // Print html header
  echo $GLOBALS['G_html_header'];

  // Print html menu
  echo $GLOBALS['G_html_menu'];
  echo $GLOBALS['G_html_menu2'];

  // Print html content part A
  echo $GLOBALS['G_html_main_right_a'];

  $today=date("Y-m-d",time());

  if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // show different date
    if(isset($_POST['show_times'])) {
      $u_date=($_POST['date']);
      $today=$u_date; // for overwriting default search value in box
    } elseif(isset($_POST['show_times_minus1'])){
      $u_date=($_POST['date']);
      $today=date("Y-m-d",strtotime($u_date.' - 1 days'));
    } elseif(isset($_POST['show_times_plus1'])){
      $u_date=($_POST['date']);
      $today=date("Y-m-d",strtotime($u_date.' + 1 days'));
    } elseif(isset($_POST['show_times_today'])){
      
    }
  }


  // Open database connection
  $Db=S_open_db();

  // Get all test for today
  $array_tests=S_get_multientry($Db,'SELECT id, Teststation, Token, Registrierungszeitpunkt, Ergebniszeitpunkt, Nachname, Vorname, Adresse, Telefon, Mailadresse, Geburtsdatum, Ergebnis, Mailsend, Updated FROM Vorgang WHERE Date(Registrierungszeitpunkt)="'.$today.'";');


  echo '<h1>Ansicht Registrierte Test</h1>';

  echo '<div class="row">';

  echo '<div class="card">
  <div class="col-sm-4">';
  echo '<p></p>';
  echo'<form action="'.$current_site.'.php" method="post">
  <div class="input-group">
  <span class="input-group-addon" id="basic-addonA2">Tag auswählen</span>
  <input type="date" class="form-control" placeholder="Tag wählen" aria-describedby="basic-addonA2" value="'.$today.'" name="date">
  <span class="input-group-btn">
  <input type="submit" class="btn btn-default" value="- 1 Tag" name="show_times_minus1" />
  <input type="submit" class="btn btn-default" value="Heute" name="show_times_today" />
  <input type="submit" class="btn btn-default" value="+ 1 Tag" name="show_times_plus1" />
  </span>
  </div>
  <div class="FAIR-si-button">
    <input type="submit" class="btn btn-danger" value="Liste anzeigen" name="show_times" />
    </div></form>
  </div>';

  echo '
  <div class="col-sm-12">
  <table class="FAIR-data">';

  //Get list of times
  foreach($array_tests as $i) {
    if($i[11]==1) {
      // Test POSITIV
      $class_ergebnis='FAIR-text-red';
      $text_ergebnis='POS';
    } elseif($i[11]==2) {
      // Test NEGATIV
      $class_ergebnis='FAIR-text-green';
      $text_ergebnis='NEG';
    } elseif($i[11]==9) {
      // Test FEHLERHAFT
      $class_ergebnis='FAIR-change-red';
      $text_ergebnis='ERR';
    } else {
      $class_ergebnis='';
      $text_ergebnis='---';
    }
    echo '
    
    <tr>
    <td class="FAIR-data-height2 FAIR-data-right FAIR-data-left FAIR-data-bottom FAIR-data-top">#'.$i[0].'</td>
    <td class="FAIR-data-height2 FAIR-data-right FAIR-data-left FAIR-data-bottom FAIR-data-top">S '.$i[1].'</td>
    <td class="FAIR-data-height2 FAIR-data-right FAIR-data-left FAIR-data-bottom FAIR-data-top">K '.$i[2].'</td>
    <td class="FAIR-data-height2 FAIR-data-right FAIR-data-left FAIR-data-bottom FAIR-data-top"d>Reg '.$i[3].'</td>
    <td class="FAIR-data-height2 FAIR-data-right FAIR-data-left FAIR-data-bottom FAIR-data-top">Person '.$i[5].'/'.$i[6].'</td>
    <td class="FAIR-data-height2 FAIR-data-right FAIR-data-left FAIR-data-bottom FAIR-data-top">'.$i[10].'</td>
    <td class="FAIR-data-height2 FAIR-data-right FAIR-data-left FAIR-data-bottom FAIR-data-top">'.$i[7].'</td>
    <td class="FAIR-data-height2 FAIR-data-right FAIR-data-left FAIR-data-bottom FAIR-data-top">'.$i[8].'</td>
    <td class="FAIR-data-height2 FAIR-data-right FAIR-data-left FAIR-data-bottom FAIR-data-top">'.$i[9].'</td>
    <td class="FAIR-data-height2 FAIR-data-right FAIR-data-left FAIR-data-bottom FAIR-data-top '.$class_ergebnis.'">Erg '.$text_ergebnis.' / '.$i[4].'</td>
    </tr>';
  }
  echo '</table></div></div>';

  echo '</div>';



  // Close connection to database
  S_close_db($Db);

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