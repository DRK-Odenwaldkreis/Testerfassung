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
$current_site="sammeltestung";

// Include functions
include_once 'tools.php';
include_once 'auth.php';
include_once 'menu.php';

// role check
if( A_checkpermission(array(0,2,0,4,0)) ) {

    $errorhtml0 ='';
    $errorhtml1 ='';
    $errorhtml2 ='';

    // Get termin_id
    $val_report_display=0;
    if ($_SERVER['REQUEST_METHOD'] == 'GET') {
        if(isset($_GET['termin'])) {
            $termin_id=$_GET['termin'];
        }
        
    }

    // Create certificates for ZIP
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        if(isset($_POST['create_certicates_zip'])) {
            $date=$_POST['date'];
            $station_id=$_POST['station_id'];
            $uid=$_SESSION['uid'];
            if($date!='' && $station_id!='') {
                $dir="/home/webservice/Testerfassung/CertificationBatchJob/";
                chdir($dir);
                $job="python3 job.py $date $station_id $uid > /dev/null &";
                exec($job,$script_output);
                $errorhtml2 .= H_build_boxinfo( 0, "Zertifikate werden erzeugt und ein Downloadlink per E-Mail versendet.", 'green' );
            } else {
                $errorhtml2 .= H_build_boxinfo( 0, "Falsche Dateneingabe - bitte Station und Datum wählen.", 'red' );
            }
        }
        
    }


    // Save file from upload
    if (!empty($_FILES)) {
        $uploaddir = '/home/webservice/ImportCSV_Sammeltestung/';
        $filename = pathinfo($_FILES['userfile']['name'], PATHINFO_FILENAME);
        $extension = strtolower(pathinfo($_FILES['userfile']['name'], PATHINFO_EXTENSION));
        $termin_id=$_POST['termin_id'];
        $uid=$_POST['uid'];
        if($termin_id>0 && $uid>0) {
            $new_file = $uploaddir.'sammel_tid'.$termin_id.'_uid'.$uid.'.'.$extension;

            if (move_uploaded_file($_FILES['userfile']['tmp_name'], $new_file)) {
                $errorhtml1 = H_build_boxinfo( 0, "Datei wurde erfolgreich hochgeladen.", 'green' );
                $dir="/home/webservice/Testerfassung/CSVImport/";
                chdir($dir);
                $job="python3 job.py $new_file $termin_id $uid";
                exec($job,$script_output);
                if($script_output[0]>0) {
                    $errorhtml1 .= H_build_boxinfo( 0, "Import wurde ausgeführt...mit ".$script_output[0]." Voranmeldungen.", 'green' );
                } else {
                    $errorhtml1 = H_build_boxinfo( 0, "Fehler Error-Code 60. Kein Import der Daten.", 'red' );
                }
            } else {
                $errorhtml1 = H_build_boxinfo( 0, "Fehler Error-Code 63. Keine Datei.", 'red' );
            }
        } else {
            $errorhtml1 = H_build_boxinfo( 0, "Fehler Error-Code 66. Kein Termin oder User-ID.", 'red' );
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


    echo '<h1>Sammel-Testung</h1>';

    // Get certificates from background job
    echo '<div class="row">';
    $stations_array=S_get_multientry($Db,'SELECT id, Ort FROM Station;');
    echo '<div class="card">
    <div class="col-sm-4">
    <h3>Test-Zertifikate abholen</h3>';

    echo '<form action="'.$current_site.'.php" method="post">
          <div class="input-group">
          <span class="input-group-addon" id="basic-addon1">Station</span>
            <select id="select-state" placeholder="Wähle eine Station..." class="custom-select" style="margin-top:0px;" name="station_id">
            <option value="" selected>Wähle Station...</option>
                ';
                foreach($stations_array as $i) {
                    $display='S'.$i[0].' / '.$i[1];
                    echo '<option value="'.$i[0].'">'.$display.'</option>';
                }
                echo '
            </select>
            </div>
            <div class="input-group">
          <span class="input-group-addon" id="basic-addon4">Datum</span>
            <input type="date" class="form-control" placeholder="JJJJ-MM-DD" aria-describedby="basic-addon4" value="" name="date">
          <span class="input-group-btn">
          <input type="submit" class="btn btn-primary" value="Zertifikate als ZIP erstellen" name="create_certicates_zip" />
          </span>
          </div>
          </form>
          <div class="FAIRsepdown"></div>';
    echo $errorhtml2;
    echo '</div>';

    echo '<div class="row">';

    // Add new customers from list - UI
    echo '<div class="card">
    <div class="col-sm-12">
    <h3>Neue Personen voranmelden</h3>';
    echo $errorhtml1;

    

    if($termin_id>0) {
        // upload file
        $stmt=mysqli_prepare($Db,"SELECT id, Tag, Startzeit, Endzeit, Slot, opt_station, opt_station_adresse, id_station, Stunde FROM Termine WHERE id=?;");
        mysqli_stmt_bind_param($stmt, "i", $termin_id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_bind_result($stmt, $array_appointment[0], $array_appointment[1], $array_appointment[2], $array_appointment[3], $array_appointment[4], $array_appointment[5], $array_appointment[6], $array_appointment[7], $array_appointment[8]);
        mysqli_stmt_fetch($stmt);
        mysqli_stmt_close($stmt);
        $date=date("d.m.Y",strtotime($array_appointment[1]));
        $time1=date("H:i",strtotime($array_appointment[2]));
        $time2=date("H:i",strtotime($array_appointment[3]));
        // Adresse
        $stations_array=S_get_multientry($Db,'SELECT id, Ort, Adresse FROM Station WHERE id="'.$array_appointment[7].'";');
        if($array_appointment[5]!='') {
            $location=$stations_array[0][1].', '.$array_appointment[5].', '.$array_appointment[6];
        } else {
            $location=$stations_array[0][1].', '.$stations_array[0][2];
        }
        echo '
        <div class="panel panel-primary">
        <div class="panel-heading">
        <b>Gewählter Termin</b>
        </div>
        <div class="panel-body">
        <div class="row">
        <div class="col-sm-4 calendar-col"><b>Datum</b> <span class="calendarblue">'.$date.'</span></div>
        <div class="col-sm-4 calendar-col"><b>Uhrzeit</b> <span class="calendarblue">'.$time1.' - '.$time2.' Uhr</span></div>
        <div class="col-sm-4 calendar-col"><b>Ort</b> <span class="calendarblue">'.$location.'</span></div>
        </div>
        <div class="row">
        <div class="col-sm-12">
        <h4>Import</h4>
        <p></p><form enctype="multipart/form-data" action="'.$current_site.'.php" method="POST">
        <!-- MAX_FILE_SIZE muss vor dem Dateiupload Input Feld stehen -->
        <input type="hidden" name="MAX_FILE_SIZE" value="30000000" />
        <!-- Der Name des Input Felds bestimmt den Namen im $_FILES Array -->
        <div class="input-group">
        <span class="input-group-addon" id="basic-addon1">CSV-Datei importieren (bitte Format-Vorlage beachten)</span>
        <input name="userfile" type="file" class="form-control" />
        <input name="termin_id" type="number" class="form-control" value="'.$termin_id.'" style="display:none;"/>
        <input name="uid" type="number" class="form-control" value="'.$_SESSION['uid'].'" style="display:none;"/>
        </div><div class="input-group">
        <div class="FAIR-si-button">
        <input type="submit" class="btn btn-primary" value="Hochladen" name="upload_file" />
        </div>
        </div>
        </form>
        </div>
        </div>
        </div>
        </div>';
    } else {
        echo '
        <h4>Termin auswählen</h4>
        <p>Uhrzeit anklicken / Es stehen nur "blaue" Termine zur Verfügung. Auf Terminbuchung nicht möglich.</p>';
        echo H_build_table_testdates_all('get_id_for_zip');
    }
    echo $errorhtml0;
    echo '</div></div>';

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