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
        $array_voranmeldung=S_get_multientry($Db,'SELECT id, Vorname, Nachname, Geburtsdatum, Adresse, Telefon, Mailadresse FROM Voranmeldung WHERE id=CAST('.$_GET['prereg'].' AS int);');

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
        <div class="input-group"><span class="input-group-addon" id="basic-addon1">Telefon *</span><input type="text" name="telefon" class="form-control" placeholder="" aria-describedby="basic-addon1" autocomplete="off" value="'.$array_voranmeldung[0][5].'"></div>
        <div class="input-group"><span class="input-group-addon" id="basic-addon1">E-Mail *</span><input type="text" name="email" class="form-control" placeholder="" aria-describedby="basic-addon1" autocomplete="off" value="'.$array_voranmeldung[0][6].'"></div>
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
        <div class="input-group"><span class="input-group-addon" id="basic-addon1">Telefon *</span><input type="text" name="telefon" class="form-control" placeholder="" aria-describedby="basic-addon1" autocomplete="off"></div>
        <div class="input-group"><span class="input-group-addon" id="basic-addon1">E-Mail *</span><input type="text" name="email" class="form-control" placeholder="" aria-describedby="basic-addon1" autocomplete="off"></div>
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
            <div class="input-group"><span class="input-group-addon" id="basic-addon1">Nummer</span><input type="text" name="scan" value="" class="form-control" placeholder="" aria-describedby="basic-addon1" autocomplete="off" autofocus="on">
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

      // check if data was written
      $val_written=S_get_entry($Db,'SELECT Ergebnis FROM Vorgang WHERE id='.$testkarte.';');
      if($val_written>0){
        echo '<div class="row">';
        echo '<div class="col-sm-12">
        <h3>Ergebnis gespeichert</h3>
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
    $k_tel=$_POST['telefon'];
    $k_email=$_POST['email'];
    if( isset($_POST['prereg']) ) {
      $k_prereg=$_POST['prereg'];
    } else {
      $k_prereg=false;
    }
    $now=date("Y-m-d H:i:s",time());

    S_set_data($Db,'INSERT INTO Vorgang (Teststation,Token,Vorname,Nachname,Geburtsdatum,Adresse,Telefon,Mailadresse) VALUES ('.$_SESSION['station_id'].',
      \''.$k_token.'\',
      \''.$k_vname.'\',
      \''.$k_nname.'\',
      \''.$k_geb.'\',
      \''.$k_adresse.'\',
      \''.$k_tel.'\',
      \''.$k_email.'\'
      );');
    $k_id=S_get_entry($Db,'SELECT id FROM Vorgang WHERE Token=\''.$k_token.'\'');
    $array_written=S_get_multientry($Db,'SELECT id, Teststation, Token, Vorname, Nachname, Geburtsdatum, Adresse, Telefon, Mailadresse FROM Vorgang WHERE id='.$k_id.';');
    echo '<div class="row">';
    if($array_written[0][0]>0) {
      echo '<div class="col-sm-12">
      <h3>Kunde registriert</h3>';
      echo "<h4>Daten: S".$array_written[0][1]." / K".$array_written[0][2]." / ".$array_written[0][4]." / ".$array_written[0][3]." / ".$array_written[0][5]." / ".$array_written[0][6]." / ".$array_written[0][7]." / ".$array_written[0][8]."</h4>";
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
        <div class="input-group"><span class="input-group-addon" id="basic-addon1">Nummer</span><input type="text" name="scan" value="" class="form-control" placeholder="" aria-describedby="basic-addon1" autocomplete="off" autofocus="on">
        <span class="input-group-btn">
          <input type="submit" class="btn btn-danger" value="Senden" name="scan_send" />
          </span>
        </form>';

  
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