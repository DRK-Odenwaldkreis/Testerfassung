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
$current_site="edit_person";

// Include functions
include_once 'tools.php';
include_once 'auth.php';
include_once 'menu.php';

if( isset($_GET['id']) && isset($_GET['label']) && $_GET['label']=='download' ) {
  // ///////////////
  // Print label
  // ///////////////
  $token=$_GET['id'];
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
  if( A_checkpermission(array(1,2,0,4,5)) ) {


    // Open database connection
    $Db=S_open_db();
    $val_cwa_connection=S_get_entry($Db,'SELECT value FROM website_settings WHERE name="FLAG_CWA";');


    if( isset($_POST['submit_person']) ){
      // ///////////////
      // Registrierungsänderung speichern
      // ///////////////
      $k_id=$_POST['id'];
      $k_nname=$_POST['nname'];
      $k_vname=$_POST['vname'];
      $k_tel=$_POST['telefon'];
      $k_email=$_POST['email'];
      if($GLOBALS['FLAG_MODE_MAIN'] == 1) {
        $k_geb=$_POST['geburtsdatum'];
        $k_adresse=$_POST['adresse'];
        $k_ort=$_POST['ort'];
        $k_print_cert=$_POST['cb_print_cert'];
        if($k_print_cert=='on') { $k_val_print_cert=1; } else { $k_val_print_cert=0; }
        if( isset($_POST['pcr_grund']) && $_POST['pcr_grund']>0 ) {
          $k_pcr_grund=$_POST['pcr_grund'];
          $k_pcr_grund=', PCR_Grund='.$k_pcr_grund.'';
        } else {
          $k_pcr_grund='';
        }
      }


      if (filter_var($k_email, FILTER_VALIDATE_EMAIL)) {
        if($GLOBALS['FLAG_MODE_MAIN'] == 1) {
          S_set_data($Db,'UPDATE Vorgang SET
          Vorname=\''.$k_vname.'\',
          Nachname=\''.$k_nname.'\',
          Geburtsdatum=\''.$k_geb.'\',
          Adresse=\''.$k_adresse.'\',
          Wohnort=\''.$k_ort.'\',
          Telefon=\''.$k_tel.'\',
          Mailadresse=\''.$k_email.'\',
          handout_request='.$k_val_print_cert.'
          '.$k_pcr_grund.'
          WHERE id=CAST('.$k_id.' AS int);');
          $val_cwa_req=S_get_entry($Db,'SELECT CWA_request FROM Vorgang WHERE id=CAST('.$k_id.' AS int);');
        } else {
          // Impfzentrum
          S_set_data($Db,'UPDATE Voranmeldung SET
          Vorname=\''.$k_vname.'\',
          Nachname=\''.$k_nname.'\',
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
            echo '<a class="list-group-item list-group-item-action list-group-item-redtext" target="_blank" href="cwa_qr.php?i='.$array_vorgang[0][0].'"><span class="icon-qrcode"></span>&nbsp;CWA QR-Code für Kunden anzeigen</a><div class="FAIRsepdown"></div>';
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
          handout_request='.$k_val_print_cert.'
          '.$k_pcr_grund.'
          WHERE id=CAST('.$k_id.' AS int);');
          $val_cwa_req=S_get_entry($Db,'SELECT CWA_request FROM Vorgang WHERE id=CAST('.$k_id.' AS int);');
        } else {
          // Impfzentrum
          S_set_data($Db,'UPDATE Voranmeldung SET
          Vorname=\''.$k_vname.'\',
          Nachname=\''.$k_nname.'\',
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
            echo '<a class="list-group-item list-group-item-action list-group-item-redtext" target="_blank" href="cwa_qr.php?i='.$array_vorgang[0][0].'"><span class="icon-qrcode"></span>&nbsp;CWA QR-Code für Kunden anzeigen</a><div class="FAIRsepdown"></div>';
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
        // Get data
        $array_vorgang=S_get_multientry($Db,'SELECT id,Teststation, Token, Registrierungszeitpunkt,Vorname,Nachname,Geburtsdatum,Adresse,Wohnort,Telefon,Mailadresse,CWA_request,handout_request,zip_request,Ergebnis,PCR_Grund FROM Vorgang WHERE id=CAST('.$_GET['id'].' AS int);');
        if($array_vorgang[0][11]>0 && $array_vorgang[0][14]!=5) {
          // CWA aktiviert und Ergebnis bereits eingetragen
          // keine Änderung von Name, Geb-Dat. möglich
          $cwa_edit_lock=true;
        } else {
          $cwa_edit_lock=false;
        }
      } else {
        // Impfzentrum
        $array_vorgang=S_get_multientry($Db,'SELECT id, Token, Anmeldezeitpunkt,Vorname,Nachname,Telefon,Mailadresse FROM Voranmeldung WHERE id=CAST('.$_GET['id'].' AS int);');
        $cwa_edit_lock=false;
      }
        echo '<div class="row">';
        echo '<div class="col-sm-12">
        <h3>Kunden-Registrierung ändern</h3>';
        if($cwa_edit_lock) {
          echo '<div class="FAIRsepdown"></div><div><span class="anweisung"><span class="icon-notification"></span> ACHTUNG:</span>Änderungen von Name und Geb-Dat. sind nicht möglich, da Ergebnis bereits an CWA übermittelt. Bitte technischen Support kontaktieren, falls Änderung notwendig.</div><div class="FAIRsepdown"></div>';
        }
        echo '<form action="'.$current_site.'.php" method="post">
        <div class="input-group"><span class="input-group-addon" id="basic-addon1">ID</span><input type="text" class="form-control" placeholder="" aria-describedby="basic-addon1" value="'.$array_vorgang[0][0].'" disabled>';
        if($GLOBALS['FLAG_MODE_MAIN'] == 1) {
          echo '<span class="input-group-addon" id="basic-addon1">S</span><input type="text" class="form-control" placeholder="" aria-describedby="basic-addon1" value="'.$array_vorgang[0][1].'" disabled>
          <span class="input-group-addon" id="basic-addon1">K</span><input type="text" class="form-control" placeholder="" aria-describedby="basic-addon1" value="'.$array_vorgang[0][2].'" disabled></div>
          <div class="input-group"><span class="input-group-addon" id="basic-addon1">Reg</span><input type="text" class="form-control" placeholder="" aria-describedby="basic-addon1" value="'.$array_vorgang[0][3].'" disabled>
          <input type="text" name="id" value="'.$array_vorgang[0][0].'" style="display:none;"></div>';
        } else {
          // Impfzentrum
          echo '<span class="input-group-addon" id="basic-addon1">T</span><input type="text" class="form-control" placeholder="" aria-describedby="basic-addon1" value="'.$array_vorgang[0][1].'" disabled></div>
          <div class="input-group"><span class="input-group-addon" id="basic-addon1">Anmeldung</span><input type="text" class="form-control" placeholder="" aria-describedby="basic-addon1" value="'.$array_vorgang[0][2].'" disabled>
          <input type="text" name="id" value="'.$array_vorgang[0][0].'" style="display:none;"></div>';
        }
        if($GLOBALS['FLAG_MODE_MAIN'] == 1) {
          if($cwa_edit_lock) {
            echo '<div class="input-group"><span class="input-group-addon" id="basic-addon1">Vorname</span><input type="text" class="form-control" placeholder="" aria-describedby="basic-addon1" value="'.$array_vorgang[0][4].'" disabled></div>
            <div class="input-group"><span class="input-group-addon" id="basic-addon1">Nachname</span><input type="text" class="form-control" placeholder="" aria-describedby="basic-addon1" value="'.$array_vorgang[0][5].'" disabled></div>
            <div class="input-group"><span class="input-group-addon" id="basic-addon1">Geburtsdatum</span><input type="date" class="form-control" placeholder="" aria-describedby="basic-addon1" value="'.$array_vorgang[0][6].'" disabled></div>

            <input type="text" name="vname" value="'.$array_vorgang[0][4].'" style="display:none;"></div>
            <input type="text" name="nname" value="'.$array_vorgang[0][5].'" style="display:none;"></div>
            <input type="text" name="geburtsdatum" value="'.$array_vorgang[0][6].'" style="display:none;"></div>';
          } else {
            echo '<div class="input-group"><span class="input-group-addon" id="basic-addon1">Vorname</span><input type="text" name="vname" class="form-control" placeholder="" aria-describedby="basic-addon1" value="'.$array_vorgang[0][4].'" required></div>
            <div class="input-group"><span class="input-group-addon" id="basic-addon1">Nachname</span><input type="text" name="nname" class="form-control" placeholder="" aria-describedby="basic-addon1" value="'.$array_vorgang[0][5].'" required></div>
            <div class="input-group"><span class="input-group-addon" id="basic-addon1">Geburtsdatum</span><input type="date" name="geburtsdatum" class="form-control" placeholder="" aria-describedby="basic-addon1" value="'.$array_vorgang[0][6].'" required></div>';
          }
        } else {
          // Impfzentrum
          echo '<div class="input-group"><span class="input-group-addon" id="basic-addon1">Vorname</span><input type="text" name="vname" class="form-control" placeholder="" aria-describedby="basic-addon1" value="'.$array_vorgang[0][3].'" required></div>
          <div class="input-group"><span class="input-group-addon" id="basic-addon1">Nachname</span><input type="text" name="nname" class="form-control" placeholder="" aria-describedby="basic-addon1" value="'.$array_vorgang[0][4].'" required></div>';
        }
        if($GLOBALS['FLAG_MODE_MAIN'] == 1) {
          echo '<div class="input-group"><span class="input-group-addon" id="basic-addon1">Wohnadresse</span><input type="text" name="adresse" class="form-control" placeholder="" aria-describedby="basic-addon1" value="'.$array_vorgang[0][7].'" required></div>
          <div class="input-group"><span class="input-group-addon" id="basic-addon1">Wohnort</span><input type="text" name="ort" class="form-control" placeholder="" aria-describedby="basic-addon1" value="'.$array_vorgang[0][8].'" required></div>
          <div class="input-group"><span class="input-group-addon" id="basic-addon1">Telefon</span><input type="text" name="telefon" class="form-control" placeholder="" aria-describedby="basic-addon1" value="'.$array_vorgang[0][9].'" required></div>
          <div class="input-group"><span class="input-group-addon" id="basic-addon1">E-Mail *</span><input type="text" name="email" class="form-control" placeholder="" aria-describedby="basic-addon1" value="'.$array_vorgang[0][10].'"></div>';
        } else {
          // Impfzentrum
          echo '<div class="input-group"><span class="input-group-addon" id="basic-addon1">Telefon</span><input type="text" name="telefon" class="form-control" placeholder="" aria-describedby="basic-addon1" value="'.$array_vorgang[0][5].'" required></div>
          <div class="input-group"><span class="input-group-addon" id="basic-addon1">E-Mail *</span><input type="text" name="email" class="form-control" placeholder="" aria-describedby="basic-addon1" value="'.$array_vorgang[0][6].'"></div>';
        }
        if($GLOBALS['FLAG_MODE_MAIN'] == 1) {
          if($val_cwa_connection==1 && $array_vorgang[0][15]==null) {
            if($array_vorgang[0][11]>0) {
              $cwa_selected='checked';
            } else {
              $cwa_selected='';
            }
            if(!$cwa_edit_lock && $array_vorgang[0][11]==1) {
              echo '<div class="FAIRsepdown"></div>
              <div class="FAIRsepdown"></div><div><span class="anweisung"><span class="icon-notification"></span> ACHTUNG:</span><b>Bei Änderungen von Name und Geb-Dat. muss Kunde den CWA QR-Code neu scannen!</b> Den alten Test kann der Kunde dazu löschen. Die Option zum Anzeigen des QR-Codes wird nach Speichern angezeigt.</div>';
            }
            /* echo '<div class="FAIRsepdown"></div>
            <div class="input-group">
            <span class="input-group-addon">CWA:</span>
            <span class="input-group-addon">
            <input type="checkbox" id="cb_cwa" name="cb_cwa" '.$cwa_selected.' disabled/>
            <label for="cb_cwa">Corona-Warn-App (CWA) nutzen?</label>
            </span>
            </div>'; */
            echo '<div class="FAIRsepdown"></div>
            <div class="cb_drk">
            <input type="checkbox" id="cb_cwa" name="cb_cwa" '.$cwa_selected.' disabled/>
            <label for="cb_cwa">Corona-Warn-App (CWA)</label>
            </div>';
            
            $display_cwa_question=' und kein CWA';
          } elseif($val_cwa_connection==0) {
            echo '<div class="input-group"><span class="text-sm">Derzeit keine Corona-Warn-App (CWA) Verbindung möglich</span></div>';
            $display_cwa_question='';
          }
          if($array_vorgang[0][15]==null) {
            if($array_vorgang[0][12]==1) {
              $print_cert_selected='checked';
            } else {
              $print_cert_selected='';
            }
            if($array_vorgang[0][13]==1) {
              /* echo '<div class="FAIRsepdown"></div>
              <div class="input-group">
              <span class="input-group-addon">Sammel-Zertifikat:</span>
              <span class="input-group-addon">
              <input type="checkbox" id="cb_zip" name="cb_zip" checked disabled/>
              <label for="cb_cwa">Ergebnisse werden gesammelt vom Büro abgeholt</label>
              </span>
              </div>'; */
              echo '<div class="FAIRsepdown"></div>
              <div class="cb_drk">
              
              <input type="checkbox" id="cb_zip" name="cb_zip" checked disabled/>
              <label for="cb_zip">Sammel-Zertifikat (Ergebnisse werden gesammelt vom Büro abgeholt)</label>
              
              </div>';
            }

            /* echo '<div class="FAIRsepdown"></div>
            <div class="input-group">
            <span class="input-group-addon">Zertifikat:</span>
            <span class="input-group-addon">
            <input type="checkbox" id="cb_print_cert" name="cb_print_cert" '.$print_cert_selected.'/>
            <label for="cb_print_cert">Papierzertifikat mit Testergebnis erstellen</label></span>
            </div>'; */
            echo '<div class="FAIRsepdown"></div>
            <div class="cb_drk">
            <input type="checkbox" id="cb_print_cert" name="cb_print_cert" '.$print_cert_selected.'/>
            <label for="cb_print_cert">Papierzertifikat mit Testergebnis erstellen</label>
            </div>';
          } else {
              
            $pcr_grund_array=S_get_multientry($Db,'SELECT id, Name FROM Kosten_PCR;');
            echo '          
              <div class="input-group"><span class="input-group-addon" id="basic-addon1">Grund für einen PCR-Test</span><select id="select-pcr" class="custom-select" style="margin-top:0px;" placeholder="Bitte wählen..." name="pcr_grund" required>
              <option value="" selected>Bitte wählen...</option>
                  ';
                  foreach($pcr_grund_array as $i) {
                      $display=$i[1];
                      if($array_vorgang[0][15]==$i[0]) { $selected='selected'; } else { $selected=''; }
                      echo '<option value="'.$i[0].'" '.$selected.'>'.$display.'</option>';
                  }
                  echo '
              </select></div>
              <div class="FAIRsepdown"></div>
            ';

          }
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