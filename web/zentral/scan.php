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
if( A_checkpermission(array(1,0,0,4,0)) ) {


  // Open database connection
  $Db=S_open_db();

  $val_cwa_connection=S_get_entry($Db,'SELECT value FROM website_settings WHERE name="FLAG_CWA";');


  if( isset($_GET['scan']) ) {
  // ///////////////
  // Scanergebnis verarbeiten
  // ///////////////

    // Is scanned code K=Testkarte or P=Pre-Registration
    $scan_type=substr($_GET['scan'],0,1);

    if($scan_type=='K' || $scan_type=='k') {
      // //////////////////
      // TESTKARTE
      // //////////////////
      
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
        echo '<a class="list-group-item list-group-item-action list-group-item-FAIR" href="'.$current_site.'.php">Neuen Scan durchführen</a>';
        echo '</div></div>';

      } elseif($testkarte=="Used") {
        // ///////////////
        // Karte bereits benutzt
        // ///////////////
        echo '<div class="row">';
        echo '<div class="col-sm-12">
        <h3>Testergebnis bereits eingetragen</h3>
        <div class="list-group">';
        echo '<a class="list-group-item list-group-item-action list-group-item-FAIR" href="'.$current_site.'.php">Neuen Scan durchführen</a>';
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
        <br> <br>
        <a class="list-group-item list-group-item-danger list-group-item-FAIR" id="module-positiv" href="'.$current_site.'.php?t=K'.$array_vorgang[0][1].'&e=1">POSITIV</a>
        <br>
        <br>
        <br>
        <a class="list-group-item list-group-item-success list-group-item-FAIR" id="module-positiv" href="'.$current_site.'.php?t=K'.$array_vorgang[0][1].'&e=2">NEGATIV</a>
        <br>
        <br>
        <br>
        <a class="list-group-item list-group-item-warning list-group-item-FAIR" id="module-positiv" href="'.$current_site.'.php?t=K'.$array_vorgang[0][1].'&e=9">FEHLERHAFT</a>
        ';
        echo '</div></div>';

      } elseif( isset($_GET['prereg']) ) {
        // ///////////////
        // Registrierung mit Voranmeldung
        // ///////////////
        // Get data
        $array_voranmeldung=S_get_multientry($Db,'SELECT id, Vorname, Nachname, Geburtsdatum, Adresse, Wohnort, Telefon, Mailadresse, CWA_request, zip_request FROM Voranmeldung WHERE id=CAST('.$_GET['prereg'].' AS int);');

        // Show data
        echo '<div class="row">';
        echo '<div class="col-sm-12">
        <h3>Kunden-Registrierung aus Voranmeldung</h3>';
        echo '<form action="'.$current_site.'.php" method="post">
        <div class="input-group"><span class="input-group-addon" id="basic-addon1">Testkarte</span><input type="text" class="form-control" placeholder="" aria-describedby="basic-addon1" value="'.$_GET['scan'].'" disabled>
        <input type="text" name="token" value="'.$_GET['scan'].'" style="display:none;"></div>
        <div class="input-group"><input type="text" name="prereg" value="'.$array_voranmeldung[0][0].'" style="display:none;"></div>
        <div class="input-group"><span class="input-group-addon" id="basic-addon1">Vorname</span><input type="text" name="vname" class="form-control" placeholder="" aria-describedby="basic-addon1" autocomplete="off" value="'.$array_voranmeldung[0][1].'" required></div>
        <div class="input-group"><span class="input-group-addon" id="basic-addon1">Nachname</span><input type="text" name="nname" class="form-control" placeholder="" aria-describedby="basic-addon1" autocomplete="off" value="'.$array_voranmeldung[0][2].'" required></div>
        <div class="input-group"><span class="input-group-addon" id="basic-addon1">Geburtsdatum</span><input type="date" name="geburtsdatum" class="form-control" placeholder="" aria-describedby="basic-addon1" autocomplete="off" value="'.$array_voranmeldung[0][3].'" required></div>
        <div class="input-group"><span class="input-group-addon" id="basic-addon1">Wohnadresse</span><input type="text" name="adresse" class="form-control" placeholder="" aria-describedby="basic-addon1" autocomplete="off" value="'.$array_voranmeldung[0][4].'" required></div>
        <div class="input-group"><span class="input-group-addon" id="basic-addon1">Wohnort</span><input type="text" name="ort" class="form-control" placeholder="" aria-describedby="basic-addon1" autocomplete="off" value="'.$array_voranmeldung[0][5].'" required></div>
        <div class="input-group"><span class="input-group-addon" id="basic-addon1">Telefon *</span><input type="text" name="telefon" class="form-control" placeholder="" aria-describedby="basic-addon1" autocomplete="off" value="'.$array_voranmeldung[0][6].'"></div>
        <div class="input-group"><span class="input-group-addon" id="basic-addon1">E-Mail</span><input type="text" name="email" class="form-control" placeholder="" aria-describedby="basic-addon1" autocomplete="off" value="'.$array_voranmeldung[0][7].'" required></div>
        <div class="FAIRsepdown"></div>';
        if($array_voranmeldung[0][9]==0) {
          echo '<div><span class="text-sm">Voranmeldung mit E-Mail - Ergebnis wird nicht auf Papier ausgestellt.</span></div>';
        }
        if($val_cwa_connection==1) {
          if($array_voranmeldung[0][8]==1) {
            $cwa_selected='checked';
            $cwa_selected_text='<div><span class="text-sm">CWA wurde bei Voranmeldung gewünscht.</span></div>';
          } else {
            $cwa_selected='';
            $cwa_selected_text='';
          }
          echo '<div class="FAIRsepdown"></div>
          '.$cwa_selected_text.'
          <div class="input-group">
          <span class="input-group-addon">CWA:</span>
          <span class="input-group-addon">
          <input type="checkbox" id="cb_cwa" name="cb_cwa" '.$cwa_selected.'/>
          <label for="cb_cwa">Corona-Warn-App (CWA) nutzen?</label>
          </span>
          </div>';
        } else {
          echo '<div class="input-group"><span class="text-sm">Derzeit keine Corona-Warn-App (CWA) Verbindung möglich</span></div>';
        }
        if($array_voranmeldung[0][9]==1) {
          echo '<div class="input-group">
          <span class="input-group-addon">Sammel-Zertifikat:</span>
          <span class="input-group-addon">
          <input type="checkbox" id="cb_zip_display" name="cb_zip_display" checked disabled/>
          <input type="checkbox" id="cb_zip" name="cb_zip" checked style="display:none;"/>
          <label for="cb_cwa">Ergebnisse werden gesammelt vom Büro abgeholt</label>
          </span>
          </div>';
        }
        echo '<div class="FAIRsepdown"></div>
        <span class="input-group-btn">
          <input type="submit" class="btn btn-danger" value="Registrieren" name="submit_person" />
          </span>
        </form>
        <p>* optional</p>';
        
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
        <div class="input-group"><span class="input-group-addon" id="basic-addon1">Vorname</span><input type="text" name="vname" class="form-control" placeholder="" aria-describedby="basic-addon1" autocomplete="off" required></div>
        <div class="input-group"><span class="input-group-addon" id="basic-addon1">Nachname</span><input type="text" name="nname" class="form-control" placeholder="" aria-describedby="basic-addon1" autocomplete="off" required></div>
        <div class="input-group"><span class="input-group-addon" id="basic-addon1">Geburtsdatum</span><input type="date" name="geburtsdatum" class="form-control" placeholder="" aria-describedby="basic-addon1" required></div>
        <div class="input-group"><span class="input-group-addon" id="basic-addon1">Wohnadresse</span><input type="text" name="adresse" class="form-control" placeholder="" aria-describedby="basic-addon1" autocomplete="off" required></div>
        <div class="input-group"><span class="input-group-addon" id="basic-addon1">Wohnort</span><input type="text" name="ort" class="form-control" placeholder="" aria-describedby="basic-addon1" autocomplete="off" required></div>
        <div class="input-group"><span class="input-group-addon" id="basic-addon1">Telefon *</span><input type="text" name="telefon" class="form-control" placeholder="" aria-describedby="basic-addon1" autocomplete="off"></div>
        <div class="input-group"><span class="input-group-addon" id="basic-addon1">E-Mail *</span><input type="text" name="email" class="form-control" placeholder="" aria-describedby="basic-addon1" autocomplete="off"></div>
        ';
        if($val_cwa_connection==1) {
          echo '<div class="FAIRsepdown"></div>
          <div class="input-group">
          <span class="input-group-addon">CWA:</span>
          <span class="input-group-addon">
          <input type="checkbox" id="cb_cwa" name="cb_cwa"/>
          <label for="cb_cwa">Corona-Warn-App (CWA) nutzen?</label>
          </span>
          </div>';
          $display_cwa_question=' und kein CWA';
        } else {
          echo '<div class="input-group"><span class="text-sm">Derzeit keine Corona-Warn-App (CWA) Verbindung möglich</span></div>';
          $display_cwa_question='';
        }
        echo '<div class="FAIRsepdown"></div>
        <div><span class="anweisung"><span class="icon-notification"></span> ANWEISUNG:</span> (Wenn keine E-Mail'.$display_cwa_question.', dann fragen) <b>Benötigen Sie ein Papierzertifikat oder reicht eine mündliche Mitteilung?</b></div>
        </div>
        <div class="input-group">
        <span class="input-group-addon">Zertifikat:</span>
        <span class="input-group-addon">
        <input type="checkbox" id="cb_print_cert" name="cb_print_cert"/>
        <label for="cb_print_cert">Papierzertifikat mit Testergebnis erstellen</label></span>
        </div>
        </div>';
        echo '<div class="FAIRsepdown"></div>
        <span class="input-group-btn">
          <input type="submit" class="btn btn-danger" value="Registrieren" name="submit_person" />
          </span>
        </form>
        <p>* optional</p>';
        
        echo '</div></div>';

      }
    } elseif($scan_type=='P' || $scan_type=='p') {
      // /////////////////
      // PRE-REGISTRATION
      // /////////////////
      $preregistration=S_get_entry_voranmeldung($Db,$_GET['scan']);
      if($preregistration=="Not registered") {
        // ///////////////
        // Code ungültig
        // ///////////////
        echo '<div class="row">';
        echo '<div class="col-sm-12">
        <h3>Voranmeldung ungültig - keine Daten gefunden</h3>
        <div class="list-group">';
        echo '<a class="list-group-item list-group-item-action list-group-item-FAIR" href="'.$current_site.'.php">Neuen Scan durchführen</a>';
        echo '</div></div>';

      } elseif($preregistration=="Used") {
        // ///////////////
        // Code bereits benutzt
        // ///////////////
        echo '<div class="row">';
        echo '<div class="col-sm-12">
        <h3>Voranmeldung bereits in Registrierung umgewandelt</h3>
        <div class="list-group">';
        echo '<a class="list-group-item list-group-item-action list-group-item-FAIR" href="'.$current_site.'.php">Neuen Scan durchführen</a>';
        echo '</div></div>';

      } elseif($preregistration>0) {
        // ///////////////
        // Voranmeldung in Vorgang übertragen
        // ///////////////

        $color_station_red='';
        $color_slot_red='';
        $today=date("Y-m-d",time());

        // Get person data
        $array_voranmeldung=S_get_multientry($Db,'SELECT Nachname, Vorname, Geburtsdatum, Termin_id FROM Voranmeldung WHERE id=CAST('.$preregistration.' AS int);');
        $array_termin=S_get_multientry($Db,'SELECT id_station,Tag,Stunde,Slot FROM Termine WHERE id=CAST('.$array_voranmeldung[0][3].' AS int);');
        $val_station=S_get_entry($Db,'SELECT Ort FROM Station WHERE id=CAST('.$array_termin[0][0].' AS int);');
        // Slot data
        $display_station='S'.$array_termin[0][0].' / '.$val_station;
        $display_slot=$array_termin[0][1].'';
        if($array_termin[0][0]!=$_SESSION['station_id']) {
          $color_station_red='FAIR-change-red';
        }
        if($array_termin[0][2]>0) {
          $time1=sprintf('%02d', $array_termin[0][2]).':'.sprintf('%02d', ( $array_termin[0][3]*15-15 ) );
          $display_slot.=" / $time1";
        }
        if($today!=$array_termin[0][1]) {
          $color_slot_red='FAIR-change-red';
        }
        
        // Show person data
        echo '<div class="row">';
        echo '<div class="col-sm-12">
        <h3>Termin gültig für</h3>';
        echo '<div class="input-group">
        <span class="input-group-addon '.$color_station_red.'" id="basic-addon1">Ort</span><input type="text" class="form-control" placeholder="" aria-describedby="basic-addon1" value="'.$display_station.'" disabled></div>
        <div class="input-group">
        <span class="input-group-addon '.$color_slot_red.'" id="basic-addon1">Termin</span><input type="text" class="form-control" placeholder="" aria-describedby="basic-addon1" value="'.$display_slot.'" disabled></div>
        <div class="input-group">
        <span class="input-group-addon" id="basic-addon1">Name</span><input type="text" class="form-control" placeholder="" aria-describedby="basic-addon1" value="'.$array_voranmeldung[0][0].'" disabled></div>
        <div class="input-group">
        <span class="input-group-addon" id="basic-addon1">Vorname</span><input type="text" class="form-control" placeholder="" aria-describedby="basic-addon1" value="'.$array_voranmeldung[0][1].'" disabled></div>
        <div class="input-group">
        <span class="input-group-addon" id="basic-addon1">Geboren am</span><input type="date" class="form-control" placeholder="" aria-describedby="basic-addon1" value="'.$array_voranmeldung[0][2].'" disabled></div>
        ';
        echo '</div></div>';

        // show scan window for Testkarte
        echo '
        <script type="text/javascript" src="lib/qrscan-lib/html5-qrcode.min.js"></script>
        ';


        echo '<div class="row">';
        echo '<div class="col-sm-12">
        <h3>Bitte Testkarte scannen</h3>';


        echo '<div style="width: 500px" id="reader"></div>';


      echo '<script>
    const html5QrCode = new Html5Qrcode("reader");
    const qrCodeSuccessCallback = message => { window.location.href=`?scan=${message}&prereg='.$preregistration.'`; }
    const config = { fps: 10, qrbox: 350 };

    html5QrCode.start({ facingMode: "environment" }, config, qrCodeSuccessCallback);

      </script>
      ';
      // Manuelle Eingabe
      echo '
      <h3>Manuelle Eingabe</h3>
      <form action="'.$current_site.'.php" method="get">
            <div class="input-group"><span class="input-group-addon" id="basic-addon1">Code/Nummer</span><input type="text" name="scan" value="" class="form-control" placeholder="" aria-describedby="basic-addon1" autocomplete="off" autofocus="on">
            <input type="text" name="prereg" value="'.$preregistration.'" class="form-control" aria-describedby="basic-addon1" style="display:none;">
            <span class="input-group-btn">
              <input type="submit" class="btn btn-danger" value="Senden" name="scan_send" />
              </span>
            </form>';


      }
    }

  } elseif( isset($_GET['t']) && isset($_GET['e']) && !isset($_GET['c']) ) {
  // ///////////////
  // Ergebniseingabe bestätigen lassen
  // ///////////////
    $testkarte=$_GET['t'];
    $ergebnis=$_GET['e'];
    echo '<div class="row">';
    echo '<div class="col-sm-12">
    <h3>Bitte '.$testkarte.' bestätigen</h3>';
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
    echo '<a class="list-group-item list-group-item-default list-group-item-FAIR" href="'.$current_site.'.php">Abbruch - neu scannen</a>';
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
          S_set_data($Db,'UPDATE Vorgang SET Ergebniszeitpunkt=\''.$now.'\', Ergebnis=2 WHERE id='.$testkarte.';'); break;
        case "1":
          // Test POSITIV
          S_set_data($Db,'UPDATE Vorgang SET Ergebniszeitpunkt=\''.$now.'\', Ergebnis=1 WHERE id='.$testkarte.';'); break;
        case "9":
          // Test FEHLERHAFT
          S_set_data($Db,'UPDATE Vorgang SET Ergebniszeitpunkt=\''.$now.'\', Ergebnis=9 WHERE id='.$testkarte.';'); break;
      }
      
      S_set_data($Db,'UPDATE Kartennummern SET Used=1 WHERE id='.$token.';');
      $customer_key=A_generate_token(64);
      S_set_data($Db,'UPDATE Vorgang SET Customer_key=\''.$customer_key.'\' WHERE id='.$testkarte.';');

      // PRINTED TEST CERTIFICATE REQUIRED ?
      if( S_get_entry($Db,'SELECT handout_request FROM Vorgang WHERE id='.$testkarte.';')==1 ) {
        $display_print_cert='<div class="FAIRsepdown"></div>
        <p><span class="anweisung"><span class="icon-notification"></span> ANWEISUNG:</span> Kunde benötigt Papierzertifikat. Bitte ein Papierzertifikat erstellen für Testkarte <b>'.$_GET['t'].'</b></b></p>
        <div class="FAIRsepdown"></div>';
      } else {
        $display_print_cert='';
      }
      // check if data was written
      $val_written=S_get_entry($Db,'SELECT Ergebnis FROM Vorgang WHERE id='.$testkarte.';');
      if($val_written>0){
        echo '<div class="row">';
        echo '<div class="col-sm-12">
        <h3>Ergebnis gespeichert</h3>
        '.$display_print_cert.'
        <div class="list-group">';
        echo '<a class="list-group-item list-group-item-action list-group-item-FAIR" href="'.$current_site.'.php">Neuen Scan durchführen</a>';
        echo '</div></div>';
      } else {
        echo '<div class="row"><div class="col-sm-12">
        <div class="alert alert-danger" role="alert">
        <h3>Speicherfehler</h3>
        <p>Die Daten wurden nicht gespeichert. Bitte neu scannen und Ergebnis eintragen.</p>
        </div></div>';
        echo '</div>';
        echo '<div class="list-group">';
        echo '<a class="list-group-item list-group-item-action list-group-item-FAIR" href="'.$current_site.'.php">Neuen Scan durchführen</a>';
        echo '</div></div>';
      }
      
    } else {
      echo '<div class="row">';
      echo '<div class="col-sm-12">
      <h3>Eingabe fehlerhaft - Bitte neu scannen</h3>';
      echo '<div class="list-group">';
      echo '<a class="list-group-item list-group-item-action list-group-item-FAIR" href="'.$current_site.'.php">Neuen Scan durchführen</a>';
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
    $k_ort=$_POST['ort'];
    $k_tel=$_POST['telefon'];
    $k_email=$_POST['email'];
    $k_cwa=$_POST['cb_cwa'];
    if($k_cwa=='on') { $k_val_cwa=1; } else { $k_val_cwa=0; }
    $k_print_cert=$_POST['cb_print_cert'];
    if($k_print_cert=='on') { $k_val_print_cert=1; } else { $k_val_print_cert=0; }
    $k_zip=$_POST['cb_zip'];
    if($k_zip=='on') { $k_val_zip=1; $k_privatemail_req=0; } else { $k_val_zip=0; }
    if ($k_email=='' || filter_var($k_email, FILTER_VALIDATE_EMAIL)) {
    
      if( isset($_POST['prereg']) ) {
        $k_prereg=$_POST['prereg'];
      } else {
        $k_prereg=false;
      }
      if($k_email!='' && !isset($k_privatemail_req) ) {
        $k_privatemail_req=1;
      } else {
        $k_privatemail_req=0;
      }
      $now=date("Y-m-d H:i:s",time());

      S_set_data($Db,'INSERT INTO Vorgang (Teststation,Token,Vorname,Nachname,Geburtsdatum,Adresse,Wohnort,Telefon,Mailadresse,CWA_request,handout_request,privateMail_request,zip_request) VALUES ('.$_SESSION['station_id'].',
        \''.$k_token.'\',
        \''.$k_vname.'\',
        \''.$k_nname.'\',
        \''.$k_geb.'\',
        \''.$k_adresse.'\',
        \''.$k_ort.'\',
        \''.$k_tel.'\',
        \''.$k_email.'\',
        '.$k_val_cwa.',
        '.$k_val_print_cert.',
        '.$k_privatemail_req.',
        '.$k_val_zip.'
        );');
      $k_id=S_get_entry($Db,'SELECT id FROM Vorgang WHERE Token=\''.$k_token.'\'');
      $array_written=S_get_multientry($Db,'SELECT id, Teststation, Token, Vorname, Nachname, Geburtsdatum, Adresse, Wohnort, Telefon, Mailadresse FROM Vorgang WHERE id='.$k_id.';');
      echo '<div class="row">';
      if($array_written[0][0]>0) {
        echo '<div class="col-sm-12">
        <div class="alert alert-success" role="alert">
        <h3>Kunde registriert</h3>';
        echo "<h4>Daten: S".$array_written[0][1]." / <b>K".$array_written[0][2]."</b> / ".$array_written[0][4]." / ".$array_written[0][3]." / ".$array_written[0][5]." / ".$array_written[0][6]." / ".$array_written[0][7]." / ".$array_written[0][8]." / <b>".$array_written[0][9]."</b></h4>";
        echo '<div class="FAIRsepdown"></div>
        <p><span class="anweisung"><span class="icon-notification"></span> ANWEISUNG:</span> Hat Kunde diese Daten sichtgeprüft? - Andernfalls jetzt zeigen und ggf. ändern!</p></div>';
        // Pre-registration -> set value to used
        if($k_prereg) {
          S_set_data($Db,'UPDATE Voranmeldung SET Used=1 WHERE id=CAST('.$k_prereg.' AS int);');
        }
      } else {
        echo '<div class="col-sm-12">
          <div class="alert alert-danger" role="alert">
          <h3>Speicherfehler</h3>
          <p>Die Daten wurden nicht gespeichert. Bitte neu eingeben oder Seite neu laden / F5 / refresh und ggf. Frage mit JA beantworten.</p>
          </div></div>';
          echo '</div>';
      }
      echo '<div class="list-group">';
      echo '<a class="list-group-item list-group-item-action list-group-item-redtext" href="edit_person.php?id='.$k_id.'">Daten ändern</a>';
      echo '<a class="list-group-item list-group-item-action list-group-item-FAIR" href="'.$current_site.'.php">Neuen Scan durchführen</a>';
      echo '</div></div>';
  // TODO SHOW QR CODE FOR CWA CONNECTION TO BE SCANNED BY CUSTOMER
    } else {
      // ///////////////////////////
      // Email invalid !!!
      echo '<div class="row">';
      echo '<div class="col-sm-12">
          <div class="alert alert-danger" role="alert">
          <h3>E-Mail ungültig</h3>
          <p>Die eingetragene E-Mail-Adresse entspricht keinem gültigen Format.</p>
          </div></div>';
          echo '</div>';
          echo '</div>';
        // ///////////////
        // Registrierung ändern wegen falscher Eingabe
        // ///////////////

        echo '<div class="row">';
        echo '<div class="col-sm-12">
        <h3>Kunden-Registrierung</h3>';
        echo '<form action="'.$current_site.'.php" method="post">
        <div class="input-group"><span class="input-group-addon" id="basic-addon1">Testkarte</span><input type="text" class="form-control" placeholder="" aria-describedby="basic-addon1" value="K'.$k_token.'" disabled>
        <input type="text" name="token" value="K'.$k_token.'" style="display:none;"></div>
        <div class="input-group"><span class="input-group-addon" id="basic-addon1">Vorname</span><input type="text" name="vname" class="form-control" placeholder="" aria-describedby="basic-addon1" autocomplete="off" value="'.$k_vname.'" required></div>
        <div class="input-group"><span class="input-group-addon" id="basic-addon1">Nachname</span><input type="text" name="nname" class="form-control" placeholder="" aria-describedby="basic-addon1" autocomplete="off" value="'.$k_nname.'" required></div>
        <div class="input-group"><span class="input-group-addon" id="basic-addon1">Geburtsdatum</span><input type="date" name="geburtsdatum" class="form-control" placeholder="" aria-describedby="basic-addon1" value="'.$k_geb.'" required></div>
        <div class="input-group"><span class="input-group-addon" id="basic-addon1">Wohnadresse</span><input type="text" name="adresse" class="form-control" placeholder="" aria-describedby="basic-addon1" autocomplete="off" value="'.$k_adresse.'" required></div>
        <div class="input-group"><span class="input-group-addon" id="basic-addon1">Wohnort</span><input type="text" name="ort" class="form-control" placeholder="" aria-describedby="basic-addon1" autocomplete="off" value="'.$k_ort.'" required></div>
        <div class="input-group"><span class="input-group-addon" id="basic-addon1">Telefon *</span><input type="text" name="telefon" class="form-control" placeholder="" aria-describedby="basic-addon1" value="'.$k_tel.'" autocomplete="off"></div>
        <div class="input-group"><span class="input-group-addon" id="basic-addon1">E-Mail *</span><input type="text" name="email" class="form-control" placeholder="" aria-describedby="basic-addon1" value="'.$k_email.'" autocomplete="off"></div>';
        if($val_cwa_connection==1) {
          if($k_cwa=='on') {
            $cwa_selected='checked';
          } else {
            $cwa_selected='';
          }
          echo '<div class="FAIRsepdown"></div>
          <div class="input-group">
          <span class="input-group-addon">CWA:</span>
          <span class="input-group-addon">
          <input type="checkbox" id="cb_cwa" name="cb_cwa" '.$cwa_selected.'/>
          <label for="cb_cwa">Corona-Warn-App (CWA) nutzen?</label>
          </span>
          </div>';
          $display_cwa_question=' und kein CWA';
        } else {
          echo '<div class="input-group"><span class="text-sm">Derzeit keine Corona-Warn-App (CWA) Verbindung möglich</span></div>';
          $display_cwa_question='';
        }
        if($k_print_cert=='on') {
          $print_cert_selected='checked';
        } else {
          $print_cert_selected='';
        }
        echo '<div class="FAIRsepdown"></div>
        <div><span class="anweisung"><span class="icon-notification"></span> ANWEISUNG:</span> (Wenn keine E-Mail'.$display_cwa_question.', dann fragen) <b>Benötigen Sie ein Papierzertifikat oder reicht eine mündliche Mitteilung?</b></div>
        </div>
        <div class="input-group">
        <span class="input-group-addon">Zertifikat:</span>
        <span class="input-group-addon">
        <input type="checkbox" id="cb_print_cert" name="cb_print_cert" '.$print_cert_selected.'/>
        <label for="cb_print_cert">Papierzertifikat mit Testergebnis erstellen</label></span>
        </div>
        </div>';
        echo '<div class="FAIRsepdown"></div>
        <span class="input-group-btn">
          <input type="submit" class="btn btn-danger" value="Registrieren" name="submit_person" />
          </span>
        </form>
        <p>* optional</p>';
        
        echo '</div></div>';
    }

  } else {
  // ///////////////
  // Scan Aufforderung
  // ///////////////
    echo '
    <script type="text/javascript" src="lib/qrscan-lib/html5-qrcode.min.js"></script>
    ';


    echo '<div class="row">';
    echo '<div class="col-sm-12">
    <h3>Bitte Testkarte scannen</h3>';


    echo '<div style="width: 500px" id="reader"></div>';


	echo '<script>
const html5QrCode = new Html5Qrcode("reader");
const qrCodeSuccessCallback = message => { window.location.href=`?scan=${message}`; }
const config = { fps: 50, qrbox: 350 };

html5QrCode.start({ facingMode: "environment" }, config, qrCodeSuccessCallback);

	</script>
	';
  // Manuelle Eingabe
  echo '
  <h3>Manuelle Eingabe</h3>
  <form action="'.$current_site.'.php" method="get">
        <div class="input-group"><span class="input-group-addon" id="basic-addon1">Code/Nummer</span><input type="text" name="scan" value="" class="form-control" placeholder="" aria-describedby="basic-addon1" autocomplete="off" autofocus="on">
        <span class="input-group-btn">
          <input type="submit" class="btn btn-danger" value="Senden" name="scan_send" />
          </span>
        </form>';

      echo '</div>
      <div class="FAIRsepdown"></div>
      <p><span class="anweisung"><span class="icon-notification"></span> ANWEISUNG:</span> QR-Code scannen oder manuelle Eingabe - Code startet mit <b>K</b> oder <b>P</b>.</p></div>';
    echo '</div>';
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