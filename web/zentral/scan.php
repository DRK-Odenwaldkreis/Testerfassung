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
  $val_cwa_connection_poc=S_get_entry($Db,'SELECT value FROM website_settings WHERE name="FLAG_CWA_pocreg";');


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

        $array_vorgang=S_get_multientry($Db,'SELECT Teststation, Token, Registrierungszeitpunkt, Nachname, Vorname, Testtyp_id FROM Vorgang WHERE id=CAST('.$testkarte.' AS int);');
        $testtyp_array=S_get_multientry($Db,'SELECT id, Kurzbezeichnung FROM Testtyp WHERE Aktiv=1 OR id='.$array_vorgang[0][5].';');

        echo '<form action="'.$current_site.'.php" method="post"><div class="input-group">
        <span class="input-group-addon" id="basic-addon1">S</span><input type="text" class="form-control" placeholder="" aria-describedby="basic-addon1" value="'.$array_vorgang[0][0].'" disabled></div>
        <div class="input-group">
        <span class="input-group-addon" id="basic-addon1">K</span><input type="text" class="form-control" placeholder="" aria-describedby="basic-addon1" value="'.$array_vorgang[0][1].'" disabled>
        <input type="text" name="token" value="K'.$array_vorgang[0][1].'" style="display:none;"></div>
        <div class="input-group">
        <span class="input-group-addon" id="basic-addon1">Reg</span><input type="text" class="form-control" placeholder="" aria-describedby="basic-addon1" value="'.$array_vorgang[0][2].'" disabled></div>
        <div class="input-group">
        <span class="input-group-addon" id="basic-addon1">Person</span><input type="text" class="form-control" placeholder="" aria-describedby="basic-addon1" value="'.$array_vorgang[0][3].'/'.$array_vorgang[0][4].'" disabled>
        </div><div class="input-group">
        <span class="input-group-addon" id="basic-addon1">Testtyp</span>
            <select id="select-state_typ" placeholder="Wähle den Test-Typ" class="custom-select" style="margin-top:0px;" name="testtyp">
            <option value="" selected>Wähle...</option>
                ';
                foreach($testtyp_array as $i) {
                    $display='T'.$i[0].' / '.$i[1];
                    if($array_vorgang[0][5]==$i[0]) { $selected='selected'; } else { $selected=''; }
                    echo '<option value="'.$i[0].'" '.$selected.'>'.$display.'</option>';
                }
                echo '
            </select>
        </div>';

        echo '<div class="FAIRsepdown"></div>
        <div class="FAIR-si-button">
        <input type="submit" class="btn btn-danger btn-lg" style="width:100%;" value="POSITIV" name="result_positive" />
        </div>
        <div class="FAIRsepdown"></div>
        <div class="FAIR-si-button">
        <input type="submit" class="btn btn-success btn-lg" style="width:100%;" value="NEGATIV" name="result_negative" />
        </div>
        <div class="FAIRsepdown"></div>
        <div class="FAIR-si-button">
        <input type="submit" class="btn btn-warning btn-lg" style="width:100%;" value="FEHLERHAFT" name="result_failure" />
        </div>
        </form>';
        echo '</div></div>';

      } elseif( isset($_GET['prereg']) ) {
        // ///////////////
        // Registrierung mit Voranmeldung
        // ///////////////
        // Get data
        $array_voranmeldung=S_get_multientry($Db,'SELECT id, Vorname, Nachname, Geburtsdatum, Adresse, Wohnort, Telefon, Mailadresse, CWA_request, zip_request, PCR_Grund FROM Voranmeldung WHERE id=CAST('.$_GET['prereg'].' AS int);');

        // Show data
        echo '<div class="row">';
        echo '<div class="col-sm-12">
        <h3>Kunden-Registrierung aus Voranmeldung</h3>';
        echo '<form action="'.$current_site.'.php" method="post">
        <div class="input-group"><span class="input-group-addon" id="basic-addon1">Testkarte</span><input type="text" class="form-control" placeholder="" aria-describedby="basic-addon1" value="'.$_GET['scan'].'" disabled>
        <input type="text" name="token" value="'.$_GET['scan'].'" style="display:none;">
        <input type="text" name="reg_type" value="PREREG" style="display:none;"></div>
        <div class="input-group"><input type="text" name="prereg" value="'.$array_voranmeldung[0][0].'" style="display:none;"></div>
        <div class="input-group"><span class="input-group-addon" id="basic-addon1">Vorname</span><input type="text" name="vname" class="form-control" placeholder="" aria-describedby="basic-addon1" autocomplete="off" value="'.$array_voranmeldung[0][1].'" required></div>
        <div class="input-group"><span class="input-group-addon" id="basic-addon1">Nachname</span><input type="text" name="nname" class="form-control" placeholder="" aria-describedby="basic-addon1" autocomplete="off" value="'.$array_voranmeldung[0][2].'" required></div>
        <div class="input-group"><span class="input-group-addon" id="basic-addon1">Geburtsdatum</span><input type="date" name="geburtsdatum" class="form-control" placeholder="" aria-describedby="basic-addon1" autocomplete="off" value="'.$array_voranmeldung[0][3].'" required></div>
        <div class="input-group"><span class="input-group-addon" id="basic-addon1">Wohnadresse</span><input type="text" name="adresse" class="form-control" placeholder="" aria-describedby="basic-addon1" autocomplete="off" value="'.$array_voranmeldung[0][4].'" required></div>
        <div class="input-group"><span class="input-group-addon" id="basic-addon1">Wohnort</span><input type="text" name="ort" class="form-control" placeholder="" aria-describedby="basic-addon1" autocomplete="off" value="'.$array_voranmeldung[0][5].'" required></div>
        <div class="input-group"><span class="input-group-addon" id="basic-addon1">Telefon</span><input type="text" name="telefon" class="form-control" placeholder="" aria-describedby="basic-addon1" autocomplete="off" value="'.$array_voranmeldung[0][6].'" required></div>
        <div class="input-group"><span class="input-group-addon" id="basic-addon1">E-Mail</span><input type="text" name="email" class="form-control" placeholder="" aria-describedby="basic-addon1" autocomplete="off" value="'.$array_voranmeldung[0][7].'" required></div>
        <div class="FAIRsepdown"></div>';

        if(S_get_entry($Db,'SELECT Testtyp.IsPCR Name FROM Station JOIN Testtyp ON Testtyp.id=Station.Testtyp_id WHERE Station.id='.$_SESSION['station_id'].';')==1) {
          $pcr_grund_array=S_get_multientry($Db,'SELECT id, Name FROM Kosten_PCR;');
          echo '          
            <div class="input-group"><span class="input-group-addon" id="basic-addon1">Grund für einen PCR-Test</span><select id="select-pcr" class="custom-select" style="margin-top:0px;" placeholder="Bitte wählen..." name="pcr_grund" required>
            <option value="" selected>Bitte wählen...</option>
                ';
                foreach($pcr_grund_array as $i) {
                    $display=$i[1];
                    if($array_voranmeldung[0][10]==$i[0]) { $selected='selected'; } else { $selected=''; }
                    echo '<option value="'.$i[0].'" '.$selected.'>'.$display.'</option>';
                }
                echo '
            </select></div>
            <div class="FAIRsepdown"></div>
          ';
        }

        if($array_voranmeldung[0][9]==0 && $array_voranmeldung[0][10]==null) {
          echo '<div class="FAIRsepdown"></div>
          <div class="cb_drk">
          <input type="checkbox" id="cb_print_cert" name="cb_print_cert"/>
          <label for="cb_print_cert">Papierzertifikat mit Testergebnis erstellen</label>
          </div>';
        }
        if($val_cwa_connection==1 && $array_voranmeldung[0][10]==null) {
          if($array_voranmeldung[0][8]==1) {
            echo '<div class="FAIRsepdown"></div>
          <div class="cb_drk">
          <input type="checkbox" id="cb_cwa" name="cb_cwa" checked style="display:none;"/>
          <input type="checkbox" id="cb_cwa_display" name="cb_cwa_display" checked disabled/>
          <label for="cb_cwa_display">Corona-Warn-App (CWA) - Namentlich</label>
          </div>';
          } elseif($array_voranmeldung[0][8]==2) {
            echo '<div class="FAIRsepdown"></div>
          <div class="cb_drk">
          <input type="checkbox" id="cb_cwa_anonym" name="cb_cwa_anonym" checked style="display:none;"/>
          <input type="checkbox" id="cb_cwa_anonym_display" name="cb_cwa_anonym_display" checked disabled/>
          <label for="cb_cwa_anonym_display">Corona-Warn-App (CWA) - Nicht-namentlich</label>
          </div>';
          } elseif($val_cwa_connection_poc==1) {
            echo '<div class="FAIRsepdown"></div>
            <div class="cb_drk">
            <input type="checkbox" id="cb_cwa_anonym" name="cb_cwa_anonym"/>
            <label for="cb_cwa_anonym">Corona-Warn-App (CWA) - Nicht-namentlich</label>
            </div>';
          } else {
            $cwa_selected='';
            echo '<div class="FAIRsepdown"></div>
          <div class="cb_drk">
          <input type="checkbox" id="cb_cwa" name="cb_cwa" disabled/>
          <label for="cb_cwa">Corona-Warn-App (CWA) nicht gewünscht vom Kunden</label>
          </div>';
          }
          
        } elseif($val_cwa_connection==0) {
          echo '<div class="input-group"><span class="text-sm">Derzeit keine Corona-Warn-App (CWA) Verbindung möglich</span></div>';
        }
        if($array_voranmeldung[0][9]==1) {
          echo '<div class="FAIRsepdown"></div><div class="cb_drk">
          <input type="checkbox" id="cb_zip" name="cb_zip" checked style="display:none;"/>
          <input type="checkbox" id="cb_zip_display" name="cb_zip_display" checked disabled/>
          <label for="cb_zip">Sammel-Zertifikat (Ergebnisse werden gesammelt vom Büro abgeholt)</label>
          </div>';
        }

        echo '<div class="FAIRsepdown"></div>
        <span class="input-group-btn">
          <input type="submit" class="btn btn-lg btn-danger" value="Registrieren" name="submit_person" />
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
        <input type="text" name="token" value="'.$_GET['scan'].'" style="display:none;">
        <input type="text" name="reg_type" value="POCREG" style="display:none;"></div>
        <div class="input-group"><span class="input-group-addon" id="basic-addon1">Vorname</span><input type="text" name="vname" class="form-control" placeholder="" aria-describedby="basic-addon1" autocomplete="off" required></div>
        <div class="input-group"><span class="input-group-addon" id="basic-addon1">Nachname</span><input type="text" name="nname" class="form-control" placeholder="" aria-describedby="basic-addon1" autocomplete="off" required></div>
        <div class="input-group"><span class="input-group-addon" id="basic-addon1">Geburtsdatum</span>
        <input type="number" min="1" max="31" placeholder="TT" class="form-control" name="gebdatum_d" required>
        <input type="number" min="1" max="12" placeholder="MM" class="form-control" name="gebdatum_m" required>
        <input type="number" min="1900" max="2999" placeholder="JJJJ" class="form-control" name="gebdatum_y" required>
        </div>
        <div class="input-group"><span class="input-group-addon" id="basic-addon1">Wohnadresse</span><input type="text" name="adresse" class="form-control" placeholder="" aria-describedby="basic-addon1" autocomplete="off" required></div>
        <div class="input-group"><span class="input-group-addon" id="basic-addon1">Wohnort</span><input type="text" name="ort" class="form-control" placeholder="" aria-describedby="basic-addon1" autocomplete="off" required></div>
        <div class="input-group"><span class="input-group-addon" id="basic-addon1">Telefon</span><input type="text" name="telefon" class="form-control" placeholder="" aria-describedby="basic-addon1" autocomplete="off" required></div>
        <div class="input-group"><span class="input-group-addon" id="basic-addon1">E-Mail *</span><input type="text" name="email" class="form-control" placeholder="" aria-describedby="basic-addon1" autocomplete="off"></div>
        ';
        if(S_get_entry($Db,'SELECT Testtyp.IsPCR Name FROM Station JOIN Testtyp ON Testtyp.id=Station.Testtyp_id WHERE Station.id='.$_SESSION['station_id'].';')==1) {
          $pcr_grund_array=S_get_multientry($Db,'SELECT id, Name FROM Kosten_PCR;');
          echo '          
                  <div class="input-group"><span class="input-group-addon" id="basic-addon1">Grund für einen PCR-Test</span><select id="select-pcr" class="custom-select" style="margin-top:0px;" placeholder="Bitte wählen..." name="pcr_grund" required>
                  <option value="" selected>Bitte wählen...</option>
                      ';
                      foreach($pcr_grund_array as $i) {
                          $display=$i[1];
                          echo '<option value="'.$i[0].'">'.$display.'</option>';
                      }
                      echo '
                  </select></div>
          ';
        }
        if($val_cwa_connection==1 && $val_cwa_connection_poc==1) {
          echo '<div class="FAIRsepdown"></div>
          <div class="cb_drk">
          <input type="checkbox" id="cb_cwa_anonym" name="cb_cwa_anonym"/>
          <label for="cb_cwa_anonym">Corona-Warn-App (CWA) - Nicht namentlich</label>
          </div>';
          $display_cwa_question=' und kein CWA';
        } elseif($val_cwa_connection==1) {
          echo '<div class="FAIRsepdown"></div>
          <div class="cb_drk">
          <input type="checkbox" id="cb_cwa" name="cb_cwa" disabled/>
          <label for="cb_cwa">Corona-Warn-App (CWA) nur bei Voranmeldung erlaubt</label>
          </div>';
          $display_cwa_question=' und kein CWA';
        } else {
          echo '<div class="input-group"><span class="text-sm">Derzeit keine Corona-Warn-App (CWA) Verbindung möglich</span></div>';
          $display_cwa_question='';
        }
        echo '<div class="FAIRsepdown"></div>
        <div><span class="anweisung"><span class="icon-notification"></span> ANWEISUNG:</span> (Wenn keine E-Mail'.$display_cwa_question.', dann fragen) <b>Benötigen Sie ein Papierzertifikat oder reicht eine mündliche Mitteilung?</b></div>
        <div class="FAIRsepdown"></div>
        <div class="cb_drk">
        <input type="checkbox" id="cb_print_cert" name="cb_print_cert"/>
        <label for="cb_print_cert">Papierzertifikat mit Testergebnis erstellen</label>
        </div>';
        if($_SESSION['station_business']) {
          echo '<div class="FAIRsepdown"></div>
          <div class="cb_drk">
          <input type="checkbox" id="cb_zip_req" name="cb_zip_req"/>
          <label for="cb_zip_req">Sammeltestung (Sammel-Zertifikat-Abruf)</label>
          </div>';
        } else {
          echo '<div class="FAIRsepdown"></div>
          <div class="cb_drk">
          <input type="checkbox" id="cb_zip_req" name="cb_zip_req" disabled/>
          <label for="cb_zip_req">Sammeltestung (Sammel-Zertifikat-Abruf) nur für Firmen-Test-Stationen</label>
          </div>';
        }
        echo '<div class="FAIRsepdown"></div>
        <span class="input-group-btn">
          <input type="submit" class="btn btn-lg btn-danger" value="Registrieren" name="submit_person" />
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

  } elseif( isset($_POST['token']) && ( isset($_POST['result_positive']) || isset($_POST['result_negative']) || isset($_POST['result_failure']) ) ) {
    // ///////////////
    // Ergebniseingabe bestätigen lassen (POST) mit Testtyp
    // ///////////////
      $testkarte=$_POST['token'];
      $testtyp=$_POST['testtyp'];
      if(isset($_POST['result_positive'])) {
        $ergebnis=1;
      } elseif(isset($_POST['result_negative'])) {
        $ergebnis=2;
      } elseif(isset($_POST['result_failure'])) {
        $ergebnis=9;
      }
      $testtyp_name=S_get_entry($Db,'SELECT Kurzbezeichnung FROM Testtyp WHERE id=CAST('.$testtyp.' AS int);');
      echo '<div class="row">';
      echo '<div class="col-sm-12">
      <h3>Bitte '.$testkarte.' bestätigen</h3>
      <div class="input-group">
        <span class="input-group-addon" id="basic-addon1">Testtyp</span><input type="text" class="form-control" placeholder="" aria-describedby="basic-addon1" value="'.$testtyp_name.'" disabled></div>';

      switch ($ergebnis) {
        case "2":
          // Test NEGATIV
          echo '<div class="list-group"><div class="FAIRsepdown"></div>
      <a class="list-group-item btn-lg list-group-item-success list-group-item-FAIR" href="'.$current_site.'.php?t='.$testkarte.'&typ='.$testtyp.'&e=2&c=confirmed">NEGATIV bestätigen</a><div class="FAIRsepdown"></div>
      '; break;
        case "1":
          // Test POSITIV
          echo '<div class="list-group"><div class="FAIRsepdown"></div>
      <a class="list-group-item btn-lg list-group-item-danger list-group-item-FAIR" href="'.$current_site.'.php?t='.$testkarte.'&typ='.$testtyp.'&e=1&c=confirmed">POSITIV bestätigen</a><div class="FAIRsepdown"></div>
      '; break;
        case "9":
          // Test FEHLERHAFT
          echo '<div class="list-group"><div class="FAIRsepdown"></div>
      <a class="list-group-item btn-lg list-group-item-warning list-group-item-FAIR" href="'.$current_site.'.php?t='.$testkarte.'&typ='.$testtyp.'&e=9&c=confirmed">FEHLERHAFT bestätigen</a><div class="FAIRsepdown"></div>
      '; break;
      }
      echo '<div class="FAIRsepdown"></div><a class="list-group-item btn-lg list-group-item-default list-group-item-FAIR" href="'.$current_site.'.php">Abbruch - neu scannen</a>';
      echo '</div></div>';
  
    } elseif( isset($_GET['t']) && isset($_GET['e']) && !isset($_GET['c']) ) {
  // ///////////////
  // Ergebniseingabe bestätigen lassen GET
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
    $testtyp=$_GET['typ'];
    if( $testkarte!="Not registered" && $testkarte!="Used" && $testkarte>0 && $testtyp>0) {
      $now=date("Y-m-d H:i:s",time());
      $token=S_get_entry($Db,'SELECT Token FROM Vorgang WHERE id='.$testkarte.';');
      switch ($_GET['e']) {
        case "2":
          // Test NEGATIV
          S_set_data($Db,'UPDATE Vorgang SET Ergebniszeitpunkt=\''.$now.'\', Ergebnis=2, Testtyp_id=CAST('.$testtyp.' AS int) WHERE id='.$testkarte.';'); break;
        case "1":
          // Test POSITIV
          S_set_data($Db,'UPDATE Vorgang SET Ergebniszeitpunkt=\''.$now.'\', Ergebnis=1, Testtyp_id=CAST('.$testtyp.' AS int) WHERE id='.$testkarte.';'); break;
        case "9":
          // Test FEHLERHAFT
          S_set_data($Db,'UPDATE Vorgang SET Ergebniszeitpunkt=\''.$now.'\', Ergebnis=9, Testtyp_id=CAST('.$testtyp.' AS int) WHERE id='.$testkarte.';'); break;
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
    if(isset($_POST['gebdatum_d'])) {
      $gebdatum_d = $_POST['gebdatum_d'];
      $gebdatum_m = $_POST['gebdatum_m'];
      $gebdatum_y = $_POST['gebdatum_y'];
      $k_geb=sprintf('%04d',$gebdatum_y).'-'.sprintf('%02d',$gebdatum_m).'-'.sprintf('%02d',$gebdatum_d);
    } else {
      $k_geb=$_POST['geburtsdatum'];
    }
    $k_adresse=$_POST['adresse'];
    $k_ort=$_POST['ort'];
    $k_tel=$_POST['telefon'];
    $k_email=$_POST['email'];
    $k_reg_type=$_POST['reg_type'];
    
    if( isset($_POST['pcr_grund']) && $_POST['pcr_grund']>0 ) {
      $k_pcr_grund=$_POST['pcr_grund'];
    } else {
      $k_pcr_grund='\''.null.'\'';
    }

    $k_cwa=$_POST['cb_cwa'];
    $k_cwa_anonym=$_POST['cb_cwa_anonym'];
    if($k_cwa=='on') { $k_val_cwa=1; } elseif($k_cwa_anonym=='on') { $k_val_cwa=2; } else { $k_val_cwa=0; }

    $k_print_cert=$_POST['cb_print_cert'];
    if($k_print_cert=='on') { $k_val_print_cert=1; } else { $k_val_print_cert=0; }

    $k_zip_req=$_POST['cb_zip_req'];
    $k_zip=$_POST['cb_zip'];
    if($k_zip=='on' || $k_zip_req=='on') { $k_val_zip=1; } else { $k_val_zip=0; }

    if ($k_email=='' || filter_var($k_email, FILTER_VALIDATE_EMAIL)) {
    
      if( isset($_POST['prereg']) ) {
        $k_prereg=$_POST['prereg'];
      } else {
        $k_prereg=false;
      }
      if($k_email!='' && $k_val_zip==0 ) {
        $k_privatemail_req=1;
      } else {
        $k_privatemail_req=0;
      }
      $now=date("Y-m-d H:i:s",time());
      $testtyp_default=S_get_entry($Db,'SELECT Testtyp_id FROM Station WHERE id='.$_SESSION['station_id'].';');

      if($k_val_cwa>0) {
        $cwa_salt=A_generate_cwa_salt();
      } else {
        $cwa_salt=null;
      }

      if(isset($_POST['pcr_grund']) && $_POST['pcr_grund']>0) {
        S_set_data($Db,'INSERT INTO Vorgang (Teststation,Token,reg_type,Vorname,Nachname,Geburtsdatum,Adresse,Wohnort,Telefon,Mailadresse,Testtyp_id,CWA_request,salt,handout_request,privateMail_request,zip_request,PCR_Grund) VALUES ('.$_SESSION['station_id'].',
        \''.$k_token.'\',
        \''.$k_reg_type.'\',
        \''.$k_vname.'\',
        \''.$k_nname.'\',
        \''.$k_geb.'\',
        \''.$k_adresse.'\',
        \''.$k_ort.'\',
        \''.$k_tel.'\',
        \''.$k_email.'\',
        '.$testtyp_default.',
        '.$k_val_cwa.',
        \''.$cwa_salt.'\',
        '.$k_val_print_cert.',
        '.$k_privatemail_req.',
        '.$k_val_zip.',
        '.$k_pcr_grund.'
        );');
      } else {
        S_set_data($Db,'INSERT INTO Vorgang (Teststation,Token,reg_type,Vorname,Nachname,Geburtsdatum,Adresse,Wohnort,Telefon,Mailadresse,Testtyp_id,CWA_request,salt,handout_request,privateMail_request,zip_request) VALUES ('.$_SESSION['station_id'].',
        \''.$k_token.'\',
        \''.$k_reg_type.'\',
        \''.$k_vname.'\',
        \''.$k_nname.'\',
        \''.$k_geb.'\',
        \''.$k_adresse.'\',
        \''.$k_ort.'\',
        \''.$k_tel.'\',
        \''.$k_email.'\',
        '.$testtyp_default.',
        '.$k_val_cwa.',
        \''.$cwa_salt.'\',
        '.$k_val_print_cert.',
        '.$k_privatemail_req.',
        '.$k_val_zip.'
        );');
        
      }
      
      $k_id=S_get_entry($Db,'SELECT id FROM Vorgang WHERE Token=\''.$k_token.'\'');
      $array_written=S_get_multientry($Db,'SELECT id, Teststation, Token, Vorname, Nachname, Geburtsdatum, Adresse, Wohnort, Telefon, Mailadresse, CWA_request FROM Vorgang WHERE id='.$k_id.';');
      echo '<div class="row">';
      if($array_written[0][0]>0) {
        echo '<div class="col-sm-12">
        <div class="alert alert-success" role="alert">
        <h3>Kunde registriert</h3>';
        $testtyp_name=S_get_entry($Db,'SELECT Kurzbezeichnung FROM Testtyp WHERE id='.$testtyp_default.';');
        echo "<h4>S".$array_written[0][1]." / <b>K".$array_written[0][2]."</b> / Typ: $testtyp_name</h4>
        <h4>Kunde: ".$array_written[0][4]." / ".$array_written[0][3]." / ".$array_written[0][5]." / ".$array_written[0][6]." / ".$array_written[0][7]." / ".$array_written[0][8]." / <b>".$array_written[0][9]."</b></h4>";
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
      echo '<a class="list-group-item list-group-item-action list-group-item-redtext" href="edit_person.php?id='.$k_id.'">Daten ändern</a>
      <div class="FAIRsepdown"></div>';
      echo '<a class="list-group-item list-group-item-action list-group-item-FAIR" href="'.$current_site.'.php">Neuen Scan durchführen</a>';
      echo '</div></div>';
      // SHOW QR CODE FOR CWA CONNECTION TO BE SCANNED BY CUSTOMER
      if($array_written[0][10]>0) {
        echo '<div class="FAIRsepdown"></div>
        <div class="col-sm-12 placeholders"><h3 class="imprint">CWA QR-Code für Kunde</h3>';
        $cwa_base64=S_get_cwa_qr_code ($Db,$array_written[0][0]);
        echo '<img src="qrcode.php?cwa='.$cwa_base64.'" />';
        echo '</div>';
      }
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
        <input type="text" name="token" value="K'.$k_token.'" style="display:none;">
        <input type="text" name="reg_type" value="'.$k_reg_type.'" style="display:none;"></div>
        <div class="input-group"><span class="input-group-addon" id="basic-addon1">Vorname</span><input type="text" name="vname" class="form-control" placeholder="" aria-describedby="basic-addon1" autocomplete="off" value="'.$k_vname.'" required></div>
        <div class="input-group"><span class="input-group-addon" id="basic-addon1">Nachname</span><input type="text" name="nname" class="form-control" placeholder="" aria-describedby="basic-addon1" autocomplete="off" value="'.$k_nname.'" required></div>
        <div class="input-group"><span class="input-group-addon" id="basic-addon1">Geburtsdatum</span>
        <input type="number" min="1" max="31" placeholder="TT" value="'.$gebdatum_d.'" class="form-control" name="gebdatum_d" required>
        <input type="number" min="1" max="12" placeholder="MM" value="'.$gebdatum_m.'" class="form-control" name="gebdatum_m" required>
        <input type="number" min="1900" max="2999" placeholder="JJJJ" value="'.$gebdatum_y.'" class="form-control" name="gebdatum_y" required>
        </div>
        <div class="input-group"><span class="input-group-addon" id="basic-addon1">Wohnadresse</span><input type="text" name="adresse" class="form-control" placeholder="" aria-describedby="basic-addon1" autocomplete="off" value="'.$k_adresse.'" required></div>
        <div class="input-group"><span class="input-group-addon" id="basic-addon1">Wohnort</span><input type="text" name="ort" class="form-control" placeholder="" aria-describedby="basic-addon1" autocomplete="off" value="'.$k_ort.'" required></div>
        <div class="input-group"><span class="input-group-addon" id="basic-addon1">Telefon</span><input type="text" name="telefon" class="form-control" placeholder="" aria-describedby="basic-addon1" value="'.$k_tel.'" autocomplete="off" required></div>
        <div class="input-group"><span class="input-group-addon" id="basic-addon1">E-Mail *</span><input type="text" name="email" class="form-control" placeholder="" aria-describedby="basic-addon1" value="'.$k_email.'" autocomplete="off"></div>';
        if(S_get_entry($Db,'SELECT Testtyp.IsPCR Name FROM Station JOIN Testtyp ON Testtyp.id=Station.Testtyp_id WHERE Station.id='.$_SESSION['station_id'].';')==1) {
          $pcr_grund_array=S_get_multientry($Db,'SELECT id, Name FROM Kosten_PCR;');
          echo '          
                  <div class="input-group"><span class="input-group-addon" id="basic-addon1">Grund für einen PCR-Test</span><select id="select-pcr" class="custom-select" style="margin-top:0px;" placeholder="Bitte wählen..." name="pcr_grund" required>
                  <option value="" selected>Bitte wählen...</option>
                      ';
                      foreach($pcr_grund_array as $i) {
                          $display=$i[1];
                          echo '<option value="'.$i[0].'">'.$display.'</option>';
                      }
                      echo '
                  </select></div>
          ';
        }
        if($val_cwa_connection==1) {
          // CWA allowed
          if($k_cwa=='on') {
            // CWA was selected
            echo '<div class="FAIRsepdown"></div>
            <div class="cb_drk">
            <input type="checkbox" id="cb_cwa" name="cb_cwa" checked/>
            <label for="cb_cwa">Corona-Warn-App (CWA) - Namentlich</label>
            </div>';
          } elseif($k_cwa_anonym=='on') {
            // CWA anonymous was selected
            echo '<div class="FAIRsepdown"></div>
            <div class="cb_drk">
            <input type="checkbox" id="cb_cwa_anonym" name="cb_cwa_anonym" checked/>
            <label for="cb_cwa_anonym">Corona-Warn-App (CWA) - Nicht-namentlich</label>
            </div>';
          } else {
            // CWA was not selected
            if($val_cwa_connection_poc==1) {
              // but PoC CWA is allowed
              echo '<div class="FAIRsepdown"></div>
              <div class="cb_drk">
              <input type="checkbox" id="cb_cwa_anonym" name="cb_cwa_anonym"/>
              <label for="cb_cwa_anonym">Corona-Warn-App (CWA) - Nicht-namentlich</label>
              </div>';
            } else {
              // PoC CWA is not allowed
              echo '<div class="FAIRsepdown"></div>
              <div class="cb_drk">
              <input type="checkbox" id="cb_cwa" name="cb_cwa" disabled/>
              <label for="cb_cwa">Corona-Warn-App (CWA)</label>
              </div>';
            }
          }
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
        if($k_zip_req=='on') {
          $print_zip_selected='checked';
        } else {
          $print_zip_selected='';
        }
        echo '<div class="FAIRsepdown"></div>
        <div><span class="anweisung"><span class="icon-notification"></span> ANWEISUNG:</span> (Wenn keine E-Mail'.$display_cwa_question.', dann fragen) <b>Benötigen Sie ein Papierzertifikat oder reicht eine mündliche Mitteilung?</b></div>
        <div class="FAIRsepdown"></div>
        <div class="cb_drk">
        <input type="checkbox" id="cb_print_cert" name="cb_print_cert" '.$print_cert_selected.'/>
        <label for="cb_print_cert">Papierzertifikat mit Testergebnis erstellen</label>
        </div>
        ';
        if($_SESSION['station_business']) {
          echo '<div class="FAIRsepdown"></div>
          <div class="cb_drk">
          <input type="checkbox" id="cb_zip_req" name="cb_zip_req" '.$print_zip_selected.'/>
          <label for="cb_zip_req">Sammeltestung (Sammel-Zertifikat-Abruf)</label>
          </div>';
        } else {
          echo '<div class="FAIRsepdown"></div>
          <div class="cb_drk">
          <input type="checkbox" id="cb_zip_req" name="cb_zip_req" disabled/>
          <label for="cb_zip_req">Sammeltestung (Sammel-Zertifikat-Abruf) nur für Firmen-Test-Stationen</label>
          </div>';
        }
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