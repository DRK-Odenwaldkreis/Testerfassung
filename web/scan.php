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
$current_site="scan";

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
if( A_checkpermission(array(0,0,0,4)) ) {


  // Open database connection
  $Db=S_open_db();


  if( isset($_GET['scan']) ) {
  // ///////////////
  // Scanergebnis verarbeiten
  // ///////////////



    // check Testkarte frei?
    $testkarte=S_get_entry_vorgang($Db,$_GET['scan']);

    if($testkarte=="Not registered") {
      // ///////////////
      // Karte ungültig
      // ///////////////
      echo '<div class="row">';
      echo '<div class="col-sm-12">
      <h3>Karte ungültig</h3>
      <div class="list-group">';
      echo '<a class="list-group-item list-group-item-action list-group-item-FAIR" href="scan.php">Neuen Scan durchführen</a>';
      echo '</div></div>';

    } elseif($testkarte=="Used") {
      // ///////////////
      // Karte bereits benutzt
      // ///////////////
      echo '<div class="row">';
      echo '<div class="col-sm-12">
      <h3>Testergebnis bereits eingetragen</h3>
      <div class="list-group">';
      echo '<a class="list-group-item list-group-item-action list-group-item-FAIR" href="scan.php">Neuen Scan durchführen</a>';
      echo '</div></div>';

    } elseif($testkarte>0) {
      // ///////////////
      // Auswertung Testergebnis
      // ///////////////

      echo '<div class="row">';
      echo '<div class="col-sm-12">
      <h3>Testergebnis eintragen</h3>';

      $array_vorgang=S_get_multientry($Db,'SELECT Teststation, Token, Registrierungszeitpunkt, Nachname, Vorname FROM Vorgang WHERE id=CAST('.$testkarte.' AS int);');

      echo '<div class="input-group">
      <span class="input-group-addon" id="basic-addon1">S</span><input type="text" class="form-control" placeholder="" aria-describedby="basic-addon1" value="'.$array_vorgang[0][0].'" disabled></div>
      <div class="input-group">
      <span class="input-group-addon" id="basic-addon1">K</span><input type="text" class="form-control" placeholder="" aria-describedby="basic-addon1" value="'.$array_vorgang[0][1].'" disabled></div>
      <div class="input-group">
      <span class="input-group-addon" id="basic-addon1">Zeit</span><input type="text" class="form-control" placeholder="" aria-describedby="basic-addon1" value="'.$array_vorgang[0][2].'" disabled></div>
      <div class="input-group">
      <span class="input-group-addon" id="basic-addon1">Person</span><input type="text" class="form-control" placeholder="" aria-describedby="basic-addon1" value="'.$array_vorgang[0][3].'/'.$array_vorgang[0][4].'" disabled>
      </div>';

      echo '<div class="list-group">
      <a class="list-group-item list-group-item-danger list-group-item-FAIR" id="module-positiv" href="'.$current_site.'.php?t=K'.$array_vorgang[0][1].'&e=1">POSITIV</a>
      <a class="list-group-item list-group-item-success list-group-item-FAIR" id="module-positiv" href="'.$current_site.'.php?t=K'.$array_vorgang[0][1].'&e=2">NEGATIV</a>
      <a class="list-group-item list-group-item-warning list-group-item-FAIR" id="module-positiv" href="'.$current_site.'.php?t=K'.$array_vorgang[0][1].'&e=9">FEHLERHAFT</a>
      ';
      echo '</div></div>';

    } else {
        // ///////////////
        // Registrierung
        // ///////////////

        echo '<div class="row">';
        echo '<div class="col-sm-12">
        <h3>Kunden-Registrierung</h3>';
        echo '<form action="'.$current_site.'.php" method="post">
        <div class="input-group"><span class="input-group-addon" id="basic-addon1">Testkarte</span><input type="text" class="form-control" placeholder="" aria-describedby="basic-addon1" value="'.$_GET['scan'].'" disabled>
        <input type="text" name="token" value="'.$_GET['scan'].'" style="display:none;"></div>
        <div class="input-group"><span class="input-group-addon" id="basic-addon1">Vorname</span><input type="text" name="vname" class="form-control" placeholder="" aria-describedby="basic-addon1" required></div>
        <div class="input-group"><span class="input-group-addon" id="basic-addon1">Nachname</span><input type="text" name="nname" class="form-control" placeholder="" aria-describedby="basic-addon1" required></div>
        <div class="input-group"><span class="input-group-addon" id="basic-addon1">Geburtsdatum</span><input type="date" name="geburtsdatum" class="form-control" placeholder="" aria-describedby="basic-addon1" required></div>
        <div class="input-group"><span class="input-group-addon" id="basic-addon1">Wohnadresse</span><input type="text" name="adresse" class="form-control" placeholder="" aria-describedby="basic-addon1" required></div>
        <div class="input-group"><span class="input-group-addon" id="basic-addon1">Telefon</span><input type="text" name="telefon" class="form-control" placeholder="" aria-describedby="basic-addon1"></div>
        <div class="input-group"><span class="input-group-addon" id="basic-addon1">E-Mail</span><input type="text" name="email" class="form-control" placeholder="" aria-describedby="basic-addon1"></div>
        <span class="input-group-btn">
          <input type="submit" class="btn btn-danger" value="Registrieren" name="submit_person" />
          </span>
        </form>';
        
        echo '</div></div>';

    }
      

  } elseif( isset($_GET['t']) && isset($_GET['e']) && !isset($_GET['c']) ) {
  // ///////////////
  // Ergebniseingabe bestätigen lassen
  // ///////////////
    $testkarte=$_GET['t'];
    $ergebnis=$_GET['e'];
    echo '<div class="row">';
    echo '<div class="col-sm-12">
    <h3></h3>';
    switch ($_GET['e']) {
      case "2":
        // Test NEGATIV
        echo '<div class="list-group">
    <a class="list-group-item list-group-item-success list-group-item-FAIR" href="'.$current_site.'.php?t='.$testkarte.'&e=2&c=confirmed">NEGATIV bestätigen</a>
    '; break;
      case "1":
        // Test POSITIV
        echo '<div class="list-group">
    <a class="list-group-item list-group-item-danger list-group-item-FAIR" href="'.$current_site.'.php?t='.$testkarte.'&e=1&c=confirmed">POSITIV bestätigen</a>
    '; break;
      case "9":
        // Test FEHLERHAFT
        echo '<div class="list-group">
    <a class="list-group-item list-group-item-warning list-group-item-FAIR" href="'.$current_site.'.php?t='.$testkarte.'&e=9&c=confirmed">FEHLERHAFT bestätigen</a>
    '; break;
    }
    
    echo '</div></div>';

  } elseif( isset($_GET['t']) && isset($_GET['e']) && isset($_GET['c']) && $_GET['c']=="confirmed" ) {
  // ///////////////
  // Ergebniseingabe speichern
  // ///////////////
    $testkarte=S_get_entry_vorgang($Db,$_GET['t']);
    if( $testkarte!="Not registered" && $testkarte!="Used" && $testkarte>0 ) {
      $now=date("Y-m-d H:i:s",time());
      $token=S_get_entry($Db,'SELECT Token FROM Vorgang WHERE id='.$testkarte.';');
      switch ($_GET['e']) {
        case "2":
          // Test NEGATIV
          S_set_data($Db,'UPDATE Vorgang SET Token=\'\', Ergebniszeitpunkt=\''.$now.'\', Ergebnis=2 WHERE id='.$testkarte.';'); break;
        case "1":
          // Test POSITIV
          S_set_data($Db,'UPDATE Vorgang SET Token=\'\', Ergebniszeitpunkt=\''.$now.'\', Ergebnis=1 WHERE id='.$testkarte.';'); break;
        case "9":
          // Test FEHLERHAFT
          S_set_data($Db,'UPDATE Vorgang SET Token=\'\', Ergebniszeitpunkt=\''.$now.'\', Ergebnis=9 WHERE id='.$testkarte.';'); break;
      }
      
      S_set_data($Db,'UPDATE Kartennummern SET Used=1 WHERE id='.$token.';');
      echo '<div class="row">';
      echo '<div class="col-sm-12">
      <h3>Ergebnis gespeichert</h3>
      <div class="list-group">';
      echo '<a class="list-group-item list-group-item-action list-group-item-FAIR" href="scan.php">Neuen Scan durchführen</a>';
      echo '</div></div>';
    } else {
      echo '<div class="row">';
      echo '<div class="col-sm-12">
      <h3>Eingabe fehlerhaft - Bitte neu scannen</h3>
      <div class="list-group">';
      echo '<a class="list-group-item list-group-item-action list-group-item-FAIR" href="scan.php">Neuen Scan durchführen</a>';
      echo '</div></div>';
    }

  } elseif( isset($_POST['submit_person']) ){
  // ///////////////
  // Registrierung speichern
  // ///////////////
    $k_token=$_POST['token'];
    $k_token=substr($k_token, strrpos($k_token, 'K' )+1);
    $k_nname=$_POST['nname'];
    $k_vname=$_POST['vname'];
    $k_geb=$_POST['geburtsdatum'];
    $k_adresse=$_POST['adresse'];
    $k_tel=$_POST['telefon'];
    $k_email=$_POST['email'];
    $now=date("Y-m-d H:i:s",time());

    S_set_data($Db,'INSERT INTO Vorgang (Token,Vorname,Nachname,Geburtsdatum,Adresse,Telefon,Mailadresse) VALUES (
      \''.$k_token.'\',
      \''.$k_vname.'\',
      \''.$k_nname.'\',
      \''.$k_geb.'\',
      \''.$k_adresse.'\',
      \''.$k_tel.'\',
      \''.$k_email.'\'
      );');

    echo '<div class="row">';
    echo '<div class="col-sm-12">
    <h3>Kunde registriert</h3>';
    echo "<p>$k_token / $k_nname / $k_vname / $now</p>";
    echo '<div class="list-group">';
    echo '<a class="list-group-item list-group-item-action list-group-item-FAIR" href="scan.php">Neuen Scan durchführen</a>';
    echo '</div></div>';


  } else {
  // ///////////////
  // Scan Aufforderung
  // ///////////////
    echo '
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/webrtc-adapter/3.3.3/adapter.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/vue/2.1.10/vue.min.js"></script>
    <script type="text/javascript" src="lib/instascan-master/instascan.min.js"></script>
    ';


    echo '<div class="row">';
    echo '<div class="col-sm-12">
    <h3>Bitte Testkarte scannen</h3>';


    echo '
    <div class="preview-container">
        <video id="preview"></video>
    </div>
    <script type="text/javascript" src="lib/instascan-master/app.js"></script>

    <div class="btn-group btn-group-toggle mb-5" data-toggle="buttons">
  <label class="btn btn-primary active">
    <input type="radio" name="options" value="1" autocomplete="off" checked> Front Camera
  </label>
  <label class="btn btn-secondary">
    <input type="radio" name="options" value="2" autocomplete="off"> Back Camera
  </label>
  </div>
    ';
    echo '</div></div>';
  }
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