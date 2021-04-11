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
$current_site="gesundheitsamt";

// Include functions
include_once 'tools.php';
include_once 'auth.php';
include_once 'menu.php';

// role check
if( A_checkpermission(array(0,0,3,4,0)) ) {





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
      $today=$today;
    } elseif(isset($_POST['show_sensitive_data'])) {
      $today=$_POST['date'];
      $_SESSION['display_sensitive']=1;
    } elseif(isset($_POST['unshow_sensitive_data'])) {
      $today=$_POST['date'];
      $_SESSION['display_sensitive']=0;
    }
  }

  // Create CSV export file
  $val_report_display=0;
  if ($_SERVER['REQUEST_METHOD'] == 'POST') {
      if(isset($_POST['create_export_csv'])) {
          $date=($_POST['date']);
          $uid=$_SESSION['uid'];
          $dir="/home/webservice/Testerfassung/CSVExport/";
          chdir($dir);
          $job="python3 job.py $date $uid 1";
          exec($job,$script_output);
          $file=$script_output[0];
          if( file_exists("/home/webservice/Reports/$file") ) {
              header('Content-Type: application/octet-stream');
              header('Content-Disposition: attachment; filename="'.basename($file).'"');
              header('Pragma: no-cache');
              header('Expires: 0');
              readfile("/home/webservice/Reports/$file");
              exit;
          }
          
      }
    }



    // Print html header
    echo $GLOBALS['G_html_header'];

    // Print html menu
    echo $GLOBALS['G_html_menu'];
    echo $GLOBALS['G_html_menu2'];
  
    // Print html content part A
    echo $GLOBALS['G_html_main_right_a'];

  // Open database connection
  $Db=S_open_db();

  // Get all test for today
  $array_tests=S_get_multientry($Db,'SELECT id, Teststation, Token, Registrierungszeitpunkt, Ergebniszeitpunkt, Nachname, Vorname, Adresse, Telefon, Mailadresse, Geburtsdatum, Ergebnis, Mailsend, Updated, customer_lock FROM Vorgang WHERE Ergebnis = 1 AND Date(Registrierungszeitpunkt)="'.$today.'"  ORDER BY Registrierungszeitpunkt DESC;');


  echo '<h1>Ansicht der Positivmeldungen</h1>';

  echo '<div class="row">';

  echo '<div class="card">
  <div class="col-sm-4">';
  echo '<p></p>';
  echo'<form action="'.$current_site.'.php" method="post">
  <div class="input-group">
  <span class="input-group-addon" id="basic-addonA2">Tag auswählen</span>
  <input type="date" class="form-control" placeholder="Tag wählen" aria-describedby="basic-addonA2" value="'.$today.'" name="date">
  <span class="input-group-btn">
  <input type="submit" class="btn btn-primary" value="Liste anzeigen" name="show_times" />

  <input type="submit" class="btn btn-default" value="- 1 Tag" name="show_times_minus1" />
  <input type="submit" class="btn btn-default" value="Heute" name="show_times_today" />
  <input type="submit" class="btn btn-default" value="+ 1 Tag" name="show_times_plus1" />
  </span>
  </div></form>';

    // Button to switch between sensitive data to display
    echo'<form action="'.$current_site.'.php" method="post">
    <input type="date" value="'.$today.'" name="date" style="display: none;">
    <div class="FAIR-si-button">';
    if($_SESSION['display_sensitive']==0) {
      echo '<button type="submit" class="btn btn-primary" name="show_sensitive_data"><span class="icon-eye"></span>&nbsp;Zeige personenbezogene Daten</button>';
    } else {
      echo '<button type="submit" class="btn btn-primary active" name="unshow_sensitive_data"><span class="icon-eye-blocked"></span>&nbsp;Blende personenbezogene Daten aus</button>';
    }
    echo'
      </div></form>
    </div>';

  echo '
  <div class="col-sm-12">
  <table class="FAIR-data">';
  
  echo '
    <tr>
    <td class="FAIR-data-height2 FAIR-data-right FAIR-data-left FAIR-data-bottom FAIR-data-top"><h4>Laufende Nummer</h4></td>
    <td class="FAIR-data-height2 FAIR-data-right FAIR-data-left FAIR-data-bottom FAIR-data-top"><h4>Stations-ID</h4></td>
    <td class="FAIR-data-height2 FAIR-data-right FAIR-data-left FAIR-data-bottom FAIR-data-top"><h4>Test Nummer</h4></td>
    <td class="FAIR-data-height2 FAIR-data-right FAIR-data-left FAIR-data-bottom FAIR-data-top"><h4>Registrierung</h4></td>
    <td class="FAIR-data-height2 FAIR-data-right FAIR-data-left FAIR-data-bottom FAIR-data-top"><h4>Name</h4></td>
    <td class="FAIR-data-height2 FAIR-data-right FAIR-data-left FAIR-data-bottom FAIR-data-top"><h4>Geburtsdatum</h4></td>
    <td class="FAIR-data-height2 FAIR-data-right FAIR-data-left FAIR-data-bottom FAIR-data-top"><h4>Adresse</h4></td>
    <td class="FAIR-data-height2 FAIR-data-right FAIR-data-left FAIR-data-bottom FAIR-data-top"><h4>Telefonnummer</h4></td>
    <td class="FAIR-data-height2 FAIR-data-right FAIR-data-left FAIR-data-bottom FAIR-data-top"><h4>Mail</h4></td>
    <td class="FAIR-data-height2 FAIR-data-right FAIR-data-left FAIR-data-bottom FAIR-data-top"><h4>Testergebnis</h4></td>
    <td class="FAIR-data-height2 FAIR-data-right FAIR-data-left FAIR-data-bottom FAIR-data-top"><h4>Ergebnis</h4></td>
    </tr>';


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

    // Result delivered
    if($i[9]=='' || $i[9] == null) {
      $text_result_delivered='<span class="icon-download"></span><span class="FAIR-sep"></span><span class="icon-checkmark"></span> vor Ort abgeholt';
    } elseif($i[14]==0 && $i[14]!= null) {
      $text_result_delivered='<span class="icon-download"></span><span class="FAIR-sep"></span><span class="icon-checkmark"></span> online abgeholt';
    } elseif($i[14]>0) {
      $text_result_delivered='<span class="icon-download"></span><span class="FAIR-sep"></span><span class="icon-minus"></span> nicht abgeholt';
    } else {
      $text_result_delivered='<span class="icon-download"></span><span class="FAIR-sep"></span><span class="icon-minus"></span> nicht abgeholt';
    }

    echo '
    
    <tr>
    <td class="FAIR-data-height2 FAIR-data-right FAIR-data-left FAIR-data-bottom FAIR-data-top">#'.$i[0].'</td>
    ';
    if($_SESSION['display_sensitive']==0) {
      echo '<td class="FAIR-data-height2 FAIR-data-right FAIR-data-left FAIR-data-bottom FAIR-data-top">S'.$i[1].'</td>
      <td class="FAIR-data-height2 FAIR-data-right FAIR-data-left FAIR-data-bottom FAIR-data-top">K'.$i[2].'</td>
      <td class="FAIR-data-height2 FAIR-data-right FAIR-data-left FAIR-data-bottom FAIR-data-top">'.$i[3].'</td>
      <td class="FAIR-data-height2 FAIR-data-right FAIR-data-left FAIR-data-bottom FAIR-data-top"><span class="FAIR-sep-l-black"></span></td>
      <td class="FAIR-data-height2 FAIR-data-right FAIR-data-left FAIR-data-bottom FAIR-data-top"><span class="FAIR-sep-l-black"></span></td>
      <td class="FAIR-data-height2 FAIR-data-right FAIR-data-left FAIR-data-bottom FAIR-data-top"><span class="FAIR-sep-l-black"></span></td>
      <td class="FAIR-data-height2 FAIR-data-right FAIR-data-left FAIR-data-bottom FAIR-data-top"><span class="FAIR-sep-l-black"></span></td>
      <td class="FAIR-data-height2 FAIR-data-right FAIR-data-left FAIR-data-bottom FAIR-data-top"><span class="FAIR-sep-l-black"></span></td>
      <td class="FAIR-data-height2 FAIR-data-right FAIR-data-left FAIR-data-bottom FAIR-data-top '.$class_ergebnis.'">Erg '.$text_ergebnis.' / '.$i[4].'</td>
      <td class="FAIR-data-height2 FAIR-data-right FAIR-data-left FAIR-data-bottom FAIR-data-top">'.$text_result_delivered.'</td>';
    } else {
      echo '<td class="FAIR-data-height2 FAIR-data-right FAIR-data-left FAIR-data-bottom FAIR-data-top">S'.$i[1].'</td>
    <td class="FAIR-data-height2 FAIR-data-right FAIR-data-left FAIR-data-bottom FAIR-data-top">K'.$i[2].'</td>
    <td class="FAIR-data-height2 FAIR-data-right FAIR-data-left FAIR-data-bottom FAIR-data-top">'.$i[3].'</td>
    <td class="FAIR-data-height2 FAIR-data-right FAIR-data-left FAIR-data-bottom FAIR-data-top">'.$i[5].', '.$i[6].'</td>
    <td class="FAIR-data-height2 FAIR-data-right FAIR-data-left FAIR-data-bottom FAIR-data-top">'.$i[10].'</td>
    <td class="FAIR-data-height2 FAIR-data-right FAIR-data-left FAIR-data-bottom FAIR-data-top">'.$i[7].'</td>
    <td class="FAIR-data-height2 FAIR-data-right FAIR-data-left FAIR-data-bottom FAIR-data-top">'.$i[8].'</td>
    <td class="FAIR-data-height2 FAIR-data-right FAIR-data-left FAIR-data-bottom FAIR-data-top">'.$i[9].'</td>
    <td class="FAIR-data-height2 FAIR-data-right FAIR-data-left FAIR-data-bottom FAIR-data-top '.$class_ergebnis.'">Erg '.$text_ergebnis.' / '.$i[4].'</td>
    <td class="FAIR-data-height2 FAIR-data-right FAIR-data-left FAIR-data-bottom FAIR-data-top">'.$text_result_delivered.'</td>';
    }
    echo '</tr>';
  }
  echo '</table></div></div>';

  // Get CSV file
  echo '<div class="card">
      <div class="col-sm-4">
      <h3>Export</h3>
      <p></p>';
      echo '<form action="'.$current_site.'.php" method="post">
      <div class="input-group">
        <span class="input-group-addon" id="basic-addonA2">Tag auswählen</span>
        <input type="date" class="form-control" placeholder="Tag wählen" aria-describedby="basic-addonA2" value="'.$today.'" name="date">
        </div>
          <input type="submit" class="btn btn-danger" value="Export CSV" name="create_export_csv" />
          </span>
          </div>
          </form>';
      echo $errorhtml0;
    
      echo '</div></div>';

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
