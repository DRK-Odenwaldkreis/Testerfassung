<?php

/* **************

Websystem für das Testzentrum DRK Odenwaldkreis
Author: Marc S. Duchene
March 2021

Edit data of registered person

** ************** */

include_once 'preload.php';
if( isset($GLOBALS['G_sessionname']) ) { session_name ($GLOBALS['G_sessionname']); }
session_start();
$sec_level=1;
$current_site="edit";

// Include functions
include_once 'tools.php';
include_once 'auth.php';
include_once 'menu.php';

if( isset($_GET['id']) && isset($_GET['label']) && $_GET['label']=='download' ) {
  // ///////////////
  // Print label
  // ///////////////
  $token=A_sanitize_input($_GET['id']);
  $dir="/home/webservice/Testerfassung/LabelCreationJob/";
  chdir($dir);
  $job="python3 job.py $token";

  exec($job,$script_output);
  $file=$script_output[0];
  $file=basename($file);
  if( file_exists("/home/webservice/Labels/$file") ) {
      header('Content-Type: application/octet-stream');
      header('Content-Disposition: attachment; filename="'.basename($file).'"');
      header('Pragma: no-cache');
      header('Expires: 0');
      readfile("/home/webservice/Labels/$file");
      exit;
  } else {
    echo 'FEHLER: Keine Label-Datei gefunden.';
  }
      

} else {


  // Print html header
  echo $GLOBALS['G_html_header'];

  // Print html menu
  echo $GLOBALS['G_html_menu'];
  echo $GLOBALS['G_html_menu2'];

  // Print html content part A
  echo $GLOBALS['G_html_main_right_a'];

  // role check
  if( A_checkpermission(array(0,2,0,4,0)) ) {


    // Open database connection
    $Db=S_open_db();
    $val_cwa_connection=S_get_entry($Db,'SELECT value FROM website_settings WHERE name="FLAG_CWA";');


    if( isset($_POST['submit_person']) ){
      // ///////////////
      // Registrierungsänderung speichern
      // ///////////////
      $k_id=A_sanitize_input($_POST['id']);
      $k_nname=A_sanitize_input_light($_POST['nname']);
      $k_vname=A_sanitize_input_light($_POST['vname']);
      $k_tel=A_sanitize_input_light($_POST['telefon']);
      $k_email=A_sanitize_input_light($_POST['email']);
      if($GLOBALS['FLAG_MODE_MAIN'] == 1) {
        $k_geb=A_sanitize_input_light($_POST['geburtsdatum']);
        $k_adresse=A_sanitize_input_light($_POST['adresse']);
        $k_ort=A_sanitize_input_light($_POST['ort']);
        $k_print_cert=$_POST['cb_print_cert'];
        if($k_print_cert=='on') { $k_val_print_cert=1; } else { $k_val_print_cert=0; }
        if( isset($_POST['pcr_grund']) && $_POST['pcr_grund']>0 ) {
          $k_pcr_grund=A_sanitize_input_light($_POST['pcr_grund']);
          $k_pcr_grund=', PCR_Grund='.$k_pcr_grund.'';
        } else {
          $k_pcr_grund='';
        }
        if($k_email!='' ) {
          $k_privatemail_req=1;
        } else {
          $k_privatemail_req=0;
        }
      } elseif($GLOBALS['FLAG_MODE_MAIN'] == 2) {
        $k_geb=A_sanitize_input_light($_POST['geburtsdatum']);
        $k_vacc_number=A_sanitize_input_light($_POST['vaccine_number']);
      }


      if(filter_var($k_email, FILTER_VALIDATE_EMAIL)) {
        if($GLOBALS['FLAG_MODE_MAIN'] == 1) {
          S_set_data($Db,'UPDATE Vorgang SET
          Vorname=\''.$k_vname.'\',
          Nachname=\''.$k_nname.'\',
          Geburtsdatum=\''.$k_geb.'\',
          Adresse=\''.$k_adresse.'\',
          Wohnort=\''.$k_ort.'\',
          Telefon=\''.$k_tel.'\',
          Mailadresse=\''.$k_email.'\',
          handout_request='.$k_val_print_cert.',
          privateMail_request='.$k_privatemail_req.'
          '.$k_pcr_grund.'
          WHERE id=CAST('.$k_id.' AS int);');
          $val_cwa_req=S_get_entry($Db,'SELECT CWA_request FROM Vorgang WHERE id=CAST('.$k_id.' AS int);');
        } else {
          // Impfzentrum
          if($k_vacc_number==3) {$k_vacc_booster=1;} else {$k_vacc_booster=0;}
          S_set_data($Db,'UPDATE Voranmeldung SET
          Vorname=\''.$k_vname.'\',
          Nachname=\''.$k_nname.'\',
          Geburtsdatum=\''.$k_geb.'\',
          Booster=CAST('.$k_vacc_booster.' AS int),
          Telefon=\''.$k_tel.'\',
          Mailadresse=\''.$k_email.'\'
          WHERE id=CAST('.$k_id.' AS int);');
        }
        echo '<div class="row">';
        echo '<div class="col-sm-12">
        <div class="alert alert-success" role="alert">
        <h3>Änderung gespeichert</h3>
        </div>';
        echo '<div class="list-group">';
        if($GLOBALS['FLAG_MODE_MAIN'] == 1) {
          if($val_cwa_req>0) {
            #echo '<a class="list-group-item list-group-item-action list-group-item-redtext" target="_blank" href="cwa_qr.php?i='.$array_vorgang[0][0].'"><span class="icon-qrcode"></span>&nbsp;CWA QR-Code für Kunden anzeigen</a><div class="FAIRsepdown"></div>';
          }
          echo '<a class="list-group-item list-group-item-action list-group-item-FAIR" href="testlist.php">Zurück zur Testliste</a>
          <div class="FAIRsepdown"></div>';
          echo '<a class="list-group-item list-group-item-action list-group-item-FAIR" href="scan.php">Neuen Scan durchführen</a>';
        } else {
          // Impfzentrum
          echo '<a class="list-group-item list-group-item-action list-group-item-FAIR" href="prereglist.php">Zurück zur Liste der Voranmeldungen</a>
          <div class="FAIRsepdown"></div>';
        }
        
        echo '</div></div>';
      } else {
        if($GLOBALS['FLAG_MODE_MAIN'] == 1) {
          S_set_data($Db,'UPDATE Vorgang SET
          Vorname=\''.$k_vname.'\',
          Nachname=\''.$k_nname.'\',
          Geburtsdatum=\''.$k_geb.'\',
          Adresse=\''.$k_adresse.'\',
          Wohnort=\''.$k_ort.'\',
          Telefon=\''.$k_tel.'\',
          handout_request='.$k_val_print_cert.',
          privateMail_request='.$k_privatemail_req.'
          '.$k_pcr_grund.'
          WHERE id=CAST('.$k_id.' AS int);');
          $val_cwa_req=S_get_entry($Db,'SELECT CWA_request FROM Vorgang WHERE id=CAST('.$k_id.' AS int);');
        } else {
          // Impfzentrum
          if($k_vacc_number==3) {$k_vacc_booster=1;} else {$k_vacc_booster=0;}
          S_set_data($Db,'UPDATE Voranmeldung SET
          Vorname=\''.$k_vname.'\',
          Nachname=\''.$k_nname.'\',
          Geburtsdatum=\''.$k_geb.'\',
          Booster=CAST('.$k_vacc_booster.' AS int),
          Telefon=\''.$k_tel.'\'
          WHERE id=CAST('.$k_id.' AS int);');
        }
        echo '<div class="row">';
        echo '<div class="col-sm-12">
        <div class="alert alert-warning" role="alert">
        <h3>E-Mail-Adresse ungültiges Format. Änderungen ohne E-Mail wurden gespeichert.</h3>
        <div class="FAIRsepdown"></div>
        <p><span class="anweisung"><span class="icon-notification"></span> ANWEISUNG:</span> E-Mail nochmal ändern? <a href="?id='.$k_id.'">Dazu hier klicken</a>.</p>
        </div>';
        echo '<div class="list-group">';
        if($GLOBALS['FLAG_MODE_MAIN'] == 1) {
          if($val_cwa_req>0) {
            #echo '<a class="list-group-item list-group-item-action list-group-item-redtext" target="_blank" href="cwa_qr.php?i='.$array_vorgang[0][0].'"><span class="icon-qrcode"></span>&nbsp;CWA QR-Code für Kunden anzeigen</a><div class="FAIRsepdown"></div>';
          }
          echo '<a class="list-group-item list-group-item-action list-group-item-FAIR" href="testlist.php">Zurück zur Testliste</a>
          <div class="FAIRsepdown"></div>';
          echo '<a class="list-group-item list-group-item-action list-group-item-FAIR" href="scan.php">Neuen Scan durchführen</a>';
        } else {
          // Impfzentrum
          echo '<a class="list-group-item list-group-item-action list-group-item-FAIR" href="prereglist.php">Zurück zur Liste der Voranmeldungen</a>
          <div class="FAIRsepdown"></div>';
        }
        echo '</div></div>';
      }
    
    
    } elseif( isset($_GET['id']) && isset($_GET['reset']) && $_GET['reset']=='mail' ) {
      // ///////////////
      // Reset Mailsend=NULL
      // ///////////////
        
      S_set_data($Db,'UPDATE Vorgang SET
      privateMail_lock=NULL
      WHERE id=CAST('.$_GET['id'].' AS int);');

      echo '<div class="row">';
      echo '<div class="col-sm-12">
      <h3>Kunden-Daten geändert</h3>
      <p>Benachrichtigungs-E-Mail wird nochmal verschickt</p>';      
      echo '<a class="list-group-item list-group-item-action list-group-item-redtext" href="testlist.php">Zurück zur Testliste</a>';
      
      echo '</div></div>';

    } elseif( isset($_GET['id']) && isset($_GET['reset']) && $_GET['reset']=='lock' ) {
      // ///////////////
      // Reset customer_lock=NULL
      // ///////////////
            
      S_set_data($Db,'UPDATE Vorgang SET
      customer_lock=NULL
      WHERE id=CAST('.$_GET['id'].' AS int);');

      echo '<div class="row">';
      echo '<div class="col-sm-12">
      <h3>Kunden-Daten geändert</h3>
      <p>Reset der Sperre (Abholung des Ergebnisses wieder möglich</p>';      
      echo '<a class="list-group-item list-group-item-action list-group-item-redtext" href="testlist.php">Zurück zur Testliste</a>';
      
      echo '</div></div>';
      
    } elseif( isset($_GET['id']) ) {

      // ///////////////
      // Registrierung ändern / Formular
      // ///////////////
      if($GLOBALS['FLAG_MODE_MAIN'] == 1) {
        //Testzentrum
        // Get data
        $array_vorgang=S_get_multientry($Db,'SELECT id,Teststation, Token, Registrierungszeitpunkt,Vorname,Nachname,Geburtsdatum,Adresse,Wohnort,Telefon,Mailadresse,CWA_request,handout_request,zip_request,Ergebnis,PCR_Grund,customer_lock,Ergebnis,Testtyp_id,PCR_Grund,Zustaendiger,
        privateMail_lock,privateMail_request,CWA_lock,CWA_request,zip_lock,zip_request,gaMail_lock,handout_request,
        DCC_lock,salt,HashOfHash FROM Vorgang WHERE id=CAST('.$_GET['id'].' AS int);');
        if($array_vorgang[0][11]>0 && $array_vorgang[0][16]==0) {
          // CWA aktiviert und Ergebnis bereits abgeholt
          // keine Änderung von Name, Geb-Dat. möglich
          $cwa_edit_lock=true;
        } else {
          $cwa_edit_lock=false;
        }
      } else {
        // Impfzentrum
        $array_vorgang=S_get_multientry($Db,'SELECT id, Token, Anmeldezeitpunkt,Vorname,Nachname,Telefon,Mailadresse,Geburtsdatum,Booster FROM Voranmeldung WHERE id=CAST('.$_GET['id'].' AS int);');
        $cwa_edit_lock=false;
      }
        echo '<div class="row">';
        echo '<div class="col-sm-12">
        <h3>Kunden-Registrierung ändern / Admin-Ansicht</h3>';
        if($cwa_edit_lock) {
          echo '<div class="FAIRsepdown"></div><div><span class="anweisung"><span class="icon-notification"></span> ACHTUNG:</span>Änderungen von Name und Geb-Dat. sind nicht möglich, da Ergebnis bereits an CWA übermittelt. Bitte technischen Support kontaktieren, falls Änderung notwendig.</div><div class="FAIRsepdown"></div>';
        }
        echo '<form action="'.$current_site.'.php" method="post">
        <div class="input-group"><span class="input-group-addon" id="basic-addon1">ID</span><input type="text" class="form-control" placeholder="" aria-describedby="basic-addon1" value="'.$array_vorgang[0][0].'" disabled>';
        if($GLOBALS['FLAG_MODE_MAIN'] == 1) {
          echo '<span class="input-group-addon" id="basic-addon1">S</span><input type="text" class="form-control" placeholder="" aria-describedby="basic-addon1" value="'.$array_vorgang[0][1].'" disabled>
          <span class="input-group-addon" id="basic-addon1">K</span><input type="text" class="form-control" placeholder="" aria-describedby="basic-addon1" value="'.$array_vorgang[0][2].'" disabled></div>
          <div class="input-group"><span class="input-group-addon" id="basic-addon1">Reg</span><input type="text" class="form-control" placeholder="" aria-describedby="basic-addon1" value="'.$array_vorgang[0][3].'" disabled>
          <span class="input-group-addon" id="basic-addon1">Änderung von</span><input type="text" class="form-control" placeholder="" aria-describedby="basic-addon1" value="S'.$array_vorgang[0][20].'" disabled>
          <input type="text" name="id" value="'.$array_vorgang[0][0].'" style="display:none;"></div>';
        } else {
          // Impfzentrum
          echo '<span class="input-group-addon" id="basic-addon1">T</span><input type="text" class="form-control" placeholder="" aria-describedby="basic-addon1" value="'.$array_vorgang[0][1].'" disabled></div>
          <div class="input-group"><span class="input-group-addon" id="basic-addon1">Anmeldung</span><input type="text" class="form-control" placeholder="" aria-describedby="basic-addon1" value="'.$array_vorgang[0][2].'" disabled>
          <input type="text" name="id" value="'.$array_vorgang[0][0].'" style="display:none;"></div>';
        }
        if($GLOBALS['FLAG_MODE_MAIN'] == 1) {
          if($cwa_edit_lock) {
            echo '<h4>Kundendaten</h4>
            <div class="input-group"><span class="input-group-addon" id="basic-addon1">Vorname</span><input type="text" class="form-control" placeholder="" aria-describedby="basic-addon1" value="'.$array_vorgang[0][4].'" disabled></div>
            <div class="input-group"><span class="input-group-addon" id="basic-addon1">Nachname</span><input type="text" class="form-control" placeholder="" aria-describedby="basic-addon1" value="'.$array_vorgang[0][5].'" disabled></div>
            <div class="input-group"><span class="input-group-addon" id="basic-addon1">Geburtsdatum</span><input type="date" class="form-control" placeholder="" aria-describedby="basic-addon1" value="'.$array_vorgang[0][6].'" disabled></div>

            <input type="text" name="vname" value="'.$array_vorgang[0][4].'" style="display:none;"></div>
            <input type="text" name="nname" value="'.$array_vorgang[0][5].'" style="display:none;"></div>
            <input type="text" name="geburtsdatum" value="'.$array_vorgang[0][6].'" style="display:none;"></div>';
          } else {
            echo '<h4>Kundendaten</h4>
            <div class="input-group"><span class="input-group-addon" id="basic-addon1">Vorname</span><input type="text" name="vname" class="form-control" placeholder="" aria-describedby="basic-addon1" value="'.$array_vorgang[0][4].'" required></div>
            <div class="input-group"><span class="input-group-addon" id="basic-addon1">Nachname</span><input type="text" name="nname" class="form-control" placeholder="" aria-describedby="basic-addon1" value="'.$array_vorgang[0][5].'" required></div>
            <div class="input-group"><span class="input-group-addon" id="basic-addon1">Geburtsdatum</span><input type="date" name="geburtsdatum" class="form-control" placeholder="" aria-describedby="basic-addon1" value="'.$array_vorgang[0][6].'" required></div>';
          }
        } else {
          // Impfzentrum
          echo '<div class="input-group"><span class="input-group-addon" id="basic-addon1">Vorname</span><input type="text" name="vname" class="form-control" placeholder="" aria-describedby="basic-addon1" value="'.$array_vorgang[0][3].'" required></div>
          <div class="input-group"><span class="input-group-addon" id="basic-addon1">Nachname</span><input type="text" name="nname" class="form-control" placeholder="" aria-describedby="basic-addon1" value="'.$array_vorgang[0][4].'" required></div>
          <div class="input-group"><span class="input-group-addon" id="basic-addon1">Geburtsdatum</span><input type="date" name="geburtsdatum" class="form-control" placeholder="" aria-describedby="basic-addon1" value="'.$array_vorgang[0][7].'" required></div>';
        }
        if($GLOBALS['FLAG_MODE_MAIN'] == 1) {
          echo '<div class="input-group"><span class="input-group-addon" id="basic-addon1">Wohnadresse</span><input type="text" name="adresse" class="form-control" placeholder="" aria-describedby="basic-addon1" value="'.$array_vorgang[0][7].'" required></div>
          <div class="input-group"><span class="input-group-addon" id="basic-addon1">Wohnort</span><input type="text" name="ort" class="form-control" placeholder="" aria-describedby="basic-addon1" value="'.$array_vorgang[0][8].'" required></div>
          <div class="input-group"><span class="input-group-addon" id="basic-addon1">Telefon</span><input type="text" name="telefon" class="form-control" placeholder="" aria-describedby="basic-addon1" value="'.$array_vorgang[0][9].'" required></div>
          <div class="input-group"><span class="input-group-addon" id="basic-addon1">E-Mail *</span><input type="text" name="email" class="form-control" placeholder="" aria-describedby="basic-addon1" value="'.$array_vorgang[0][10].'"></div>';

          // Test-Ergebnis
          $result_array=S_get_multientry($Db,'SELECT id,Name FROM Ergebnis;');
          echo '<div class="FAIRsepdown"></div>
          <h4>Testdaten</h4>
          <div class="input-group"><span class="input-group-addon" id="basic-addon1">Test-Ergebnis</span>
          <select id="select-state_typ" placeholder="Wähle das Ergebnis" class="custom-select" style="margin-top:0px;" name="e_result">
          <option value="" >Wähle...</option>
              ';
              foreach($result_array as $i) {
                  $display='('.$i[0].') '.$i[1];
                  if($array_vorgang[0][17]==$i[0]) {$val_selected="selected";} else {$val_selected="";}
                  echo '<option value="'.$i[0].'" '.$val_selected.'>'.$display.'</option>';
              }
              echo '
          </select></div>';
          // Test-Typ
          $result_array=S_get_multientry($Db,'SELECT id,Kurzbezeichnung,IsPCR FROM Testtyp;');
          echo '
          <div class="input-group"><span class="input-group-addon" id="basic-addon1">Test-Typ</span>
          <select id="select-state_typ" placeholder="Wähle Testtyp aus" class="custom-select" style="margin-top:0px;" name="e_testtyp">
          <option value="" >Wähle...</option>
              ';
              foreach($result_array as $i) {
                  if($i[2]==0) {$display_pcr='Antigen';} else {$display_pcr='PCR';}
                  $display='('.$i[0].') '.$i[1].' / '.$display_pcr;
                  if($array_vorgang[0][18]==$i[0]) {$val_selected="selected";} else {$val_selected="";}
                  echo '<option value="'.$i[0].'" '.$val_selected.'>'.$display.'</option>';
              }
              echo '
          </select></div>';
          // PCR-Grund
          $result_array=S_get_multientry($Db,'SELECT id,Kurzbezeichnung FROM Kosten_PCR;');
          if(is_null($array_vorgang[0][19])) {$val_selected="selected";} else {$val_selected="";}
          echo '
          <div class="input-group"><span class="input-group-addon" id="basic-addon1">PCR-Grund</span>
          <select id="select-state_typ" placeholder="Wähle PCR-Grund aus" class="custom-select" style="margin-top:0px;" name="e_kostenpcr">
          <option value="0" '.$val_selected.'>(0) kein</option>
              ';
              foreach($result_array as $i) {
                  $display='('.$i[0].') '.$i[1];
                  if(!is_null($array_vorgang[0][19]) && $array_vorgang[0][19]==$i[0]) {$val_selected="selected";} else {$val_selected="";}
                  echo '<option value="'.$i[0].'" '.$val_selected.'>'.$display.'</option>';
              }
              echo '
          </select></div>';

          // Flags / weiteres
          echo '<div class="FAIRsepdown"></div>
          <h4>Systemdaten</h4>
          <p>E-Mail an Kunden schicken</p>
          <div class="input-group">
          <span class="input-group-addon" id="basic-addon1">private mail lock</span><input type="number" step="1" min="0" class="form-control" placeholder="" aria-describedby="basic-addon1" value="'.$array_vorgang[0][21].'" name="e_privmaillock">
          <span class="input-group-addon" id="basic-addon1">private mail request</span><input type="number" step="1" min="0" max="1" class="form-control" placeholder="" aria-describedby="basic-addon1" value="'.$array_vorgang[0][22].'" name="e_privmailreq">
          </div>
          <div class="FAIRsepdown"></div>
          <p>Ergebnis in ZIP-Datei packen</p>
          <div class="input-group">
          <span class="input-group-addon" id="basic-addon1">ZIP lock</span><input type="number" class="form-control" placeholder="" aria-describedby="basic-addon1" value="'.$array_vorgang[0][25].'" disabled>
          <span class="input-group-addon" id="basic-addon1">ZIP request</span><input type="number" step="1" min="0" max="0" class="form-control" placeholder="" aria-describedby="basic-addon1" value="'.$array_vorgang[0][26].'" name="e_zipreq">
          </div>
          <div class="FAIRsepdown"></div>
          <p>Ergebnis an Gesundheitsamt (GA); Ergebnis ausdrucken für Kunden</p>
          <div class="input-group">
          <span class="input-group-addon" id="basic-addon1">ga mail lock</span><input type="number" step="1" min="0" class="form-control" placeholder="" aria-describedby="basic-addon1" value="'.$array_vorgang[0][27].'" name="e_gamaillock">
          <span class="input-group-addon" id="basic-addon1">handout req</span><input type="number" step="1" min="0" max="1" class="form-control" placeholder="" aria-describedby="basic-addon1" value="'.$array_vorgang[0][28].'" name="e_handoutreq">
          </div>
          <div class="FAIRsepdown"></div>
          <p>Ergebnis an CWA übertragen</p>
          <div class="input-group">
          <span class="input-group-addon" id="basic-addon1">CWA lock</span><input type="number" step="1" min="0" class="form-control" placeholder="" aria-describedby="basic-addon1" value="'.$array_vorgang[0][23].'" name="e_cwalock">
          <span class="input-group-addon" id="basic-addon1">CWA request</span><input type="number" class="form-control" placeholder="" aria-describedby="basic-addon1" value="'.$array_vorgang[0][24].'" disabled>
          </div>
          <div class="FAIRsepdown"></div>
          <p>DCC erstellt; CWA Transfer Daten</p>
          <div class="input-group">
          <span class="input-group-addon" id="basic-addon1">DCC lock</span><input type="number" class="form-control" placeholder="" aria-describedby="basic-addon1" value="'.$array_vorgang[0][29].'" disabled>
          <span class="input-group-addon" id="basic-addon1">salt</span><input type="text" class="form-control" placeholder="" aria-describedby="basic-addon1" value="'.$array_vorgang[0][30].'" disabled>
          <span class="input-group-addon" id="basic-addon1">hash-of-hash</span><input type="text" class="form-control" placeholder="" aria-describedby="basic-addon1" value="'.$array_vorgang[0][31].'" disabled>
          </div>
          ';

        } else {
          // Impfzentrum
          echo '<div class="input-group"><span class="input-group-addon" id="basic-addon1">Telefon</span><input type="text" name="telefon" class="form-control" placeholder="" aria-describedby="basic-addon1" value="'.$array_vorgang[0][5].'" required></div>
          <div class="input-group"><span class="input-group-addon" id="basic-addon1">E-Mail *</span><input type="text" name="email" class="form-control" placeholder="" aria-describedby="basic-addon1" value="'.$array_vorgang[0][6].'"></div>';
          if($array_vorgang[0][8]=='1') {$sel_vac_3='selected';$sel_vac_1='';} else {$sel_vac_1='selected';$sel_vac_3='';}
          echo '<div class="input-group"><span class="input-group-addon" id="basic-addon1">Art der Impfung</span><select id="select-pcr" class="custom-select" style="margin-top:0px;" placeholder="Bitte wählen..." name="vaccine_number" required>
          <option value="1" '.$sel_vac_1.'>Grundimmunisierung (1. bzw. 2. Impfung)</option>
          <option value="3" '.$sel_vac_3.'>Auffrischungs-, Booster-Impfung</option>
          </select></div>';
        }


        echo '<div class="FAIRsepdown"></div>
        <span class="input-group-btn">
          <input type="submit" class="btn btn-lg btn-danger" value="Änderung speichern" name="submit_person" />
          </span>
        </form>
        <p>* optional</p>';
        
        if($GLOBALS['FLAG_MODE_MAIN'] == 1) {
          echo '<a class="list-group-item list-group-item-action list-group-item-redtext" href="testlist.php">Zurück zur Testliste</a>';
        } else {
          echo '<a class="list-group-item list-group-item-action list-group-item-redtext" href="prereglist.php">Zurück zur Liste der Voranmeldungen</a>';
        }
        
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
}

?>
