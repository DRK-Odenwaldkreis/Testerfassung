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
$current_site="terminerstellung";

// Include functions
include_once 'tools.php';
include_once 'auth.php';
include_once 'menu.php';

// role check
if( A_checkpermission(array(0,2,0,4,5)) ) {

    $errorhtml0 ='';
    $errorhtml1 ='';
    $errorhtml2 ='';
    $display_creating_termin=false;

    // Create termine
    $val_report_display=0;
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {

        if(isset($_POST['create_termine1'])) {
            // Termin eintragen - ohne Terminbuchung
            $t_station=$_POST['station_id'];
            $t_datum=$_POST['date'];
            $t_start=$_POST['startzeit'];
            $t_ende=$_POST['endzeit'];
            // check values
            if( ($t_station!='') && ($t_datum!='') && ($t_start!='') && ($t_ende!='') ) {
                $t_start_date="$t_datum $t_start:00";
                $t_ende_date="$t_datum $t_ende:00";
                $t_opt_station=$_POST['opt_station'];
                $t_opt_station_adresse=$_POST['opt_station_adresse'];
                // write values
                $Db=S_open_db();
                S_set_data($Db,'INSERT Termine (id_station,Tag,Startzeit,Endzeit,opt_station,opt_station_adresse) VALUES (cast(\''.$t_station.'\' AS int),\''.$t_datum.'\',\''.$t_start_date.'\',\''.$t_ende_date.'\',\''.$t_opt_station.'\',\''.$t_opt_station_adresse.'\');');
                S_close_db($Db);

                $errorhtml0 = H_build_boxinfo( 0, "Termine wurden erstellt.", 'green' );
                $display_creating_termin=true;
            } else {
                $errorhtml1 = H_build_boxinfo( 0, "Fehler bei der Eingabe.", 'red' );
            }
            
            
        } elseif(isset($_POST['create_termine2'])) {
            // Termine eintragen - mit Terminbuchung
            $t_station=$_POST['station_id'];
            $t_datum=$_POST['date'];
            $t_start_slot=$_POST['startzeit_slot'];
            $t_ende_slot=$_POST['endzeit_slot'];
            $t_number_slot=$_POST['terminzahl_slot'];
            // check values
            if( ($t_station!='') && ($t_datum!='') && ($t_start_slot!='') && ($t_ende_slot!='') && ($t_number_slot>0) ) {
                $t_opt_station=$_POST['opt_station'];
                $t_opt_station_adresse=$_POST['opt_station_adresse'];
                // number of appointed times
                $t_number_between_start_end= 1 + ( strtotime($t_ende_slot) - strtotime($t_start_slot) ) / 15 / 60;
                // start slot and start hour
                $t_slot=(substr($t_start_slot,3,2) / 15 ) + 1;
                $t_stunde=substr($t_start_slot,0,2);
                // write values
                $Db=S_open_db();
                for($j=0;$j<$t_number_between_start_end;$j++) {
                    for($k=0;$k<$t_number_slot;$k++) {
                        S_set_data($Db,'INSERT Termine (id_station,Tag,Stunde,Slot,opt_station,opt_station_adresse) VALUES (cast(\''.$t_station.'\' AS int),\''.$t_datum.'\',\''.$t_stunde.'\',\''.$t_slot.'\',\''.$t_opt_station.'\',\''.$t_opt_station_adresse.'\');');
                    }
                    if($t_slot>=4) {$t_slot=1; $t_stunde++;} else {$t_slot++;}
                }
                S_close_db($Db);

                $errorhtml0 = H_build_boxinfo( 0, "Termine wurden erstellt.", 'green' );
                $display_creating_termin=true;
            } else {
                $errorhtml2 = H_build_boxinfo( 0, "Fehler bei der Eingabe.", 'red' );
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

    echo '<h1>Termine für eine Test-/Impf-Station erstellen</h1>';


    echo '<div class="row">';

    if($display_creating_termin) {
      echo '<div class="card">
      <div class="col-md-6">
      <h3>Neue Termine erstellt</h3>
      <p></p>';
      echo $errorhtml0;
      echo '<a class="list-group-item list-group-item-action list-group-item-redtext" href="'.$current_site.'.php">Weitere Termine erstellen</a>';
      echo '</div></div>';

    } else {

        // Get available stations
        $Db=S_open_db();
        if($GLOBALS['FLAG_MODE_MAIN'] == 1) {
            $stations_array=S_get_multientry($Db,'SELECT id, Ort FROM Station;');
        } elseif($GLOBALS['FLAG_MODE_MAIN'] == 2) {
            $stations_array=S_get_multientry($Db,'SELECT Station.id, Station.Ort, Impfstoff.Kurzbezeichnung FROM Station JOIN Impfstoff ON Impfstoff.id=Station.Impfstoff_id;');
        } elseif($GLOBALS['FLAG_MODE_MAIN'] == 3) {
            $stations_array=S_get_multientry($Db,'SELECT id, Ort FROM Station;');
        }
        S_close_db($Db);

        if($GLOBALS['FLAG_MODE_MAIN'] == 1) {
            echo '
            <div class="col-lg-6"><div class="card">
            <h3>Ohne Terminbuchung / Offener Termin</h3>
            <p class="list-group-item-text">Zum Erstellen bitte alle Felder ausfüllen.</p><p></p>';
            echo '<form action="'.$current_site.'.php" method="post">
            <div class="input-group">
            <span class="input-group-addon" id="basic-addon1">Station</span>
            <select id="select-state" placeholder="Wähle eine Station..." class="custom-select" style="margin-top:0px;" name="station_id">
            <option value="" selected>Wähle Station...</option>
                ';
                foreach($stations_array as $i) {
                    if($GLOBALS['FLAG_MODE_MAIN'] == 1 || $GLOBALS['FLAG_MODE_MAIN'] == 3) {
                        $display=$i[1].' / S'.$i[0];
                        echo '<option value="'.$i[0].'">'.$display.'</option>';
                    } else {
                        $display=$i[2].' ('.$i[1].' / S'.$i[0].')';
                        echo '<option value="'.$i[0].'">'.$display.'</option>';
                    }
                }
                echo '
            </select>
            </div>
            <div class="input-group">
            <span class="input-group-addon" id="basic-addon4">Datum</span>
            <input type="date" class="form-control" placeholder="JJJJ-MM-DD" aria-describedby="basic-addon4" value="" name="date">
            </div>

            <div class="input-group">
            <span class="input-group-addon" id="basic-addon4">Von</span>
            <input type="time" class="form-control" aria-describedby="basic-addon4" name="startzeit">
            <span class="input-group-addon" id="basic-addon4">Bis</span>
            <input type="time" class="form-control" aria-describedby="basic-addon4" name="endzeit">
            </div>
            <div class="input-group">
            <span class="input-group-addon" id="basic-addon4">Optional Name d. Ortes</span>
            <input type="text" class="form-control" aria-describedby="basic-addon4" name="opt_station">
            <span class="input-group-addon" id="basic-addon4">Optional Adresse</span>
            <input type="text" class="form-control" aria-describedby="basic-addon4" name="opt_station_adresse">
            </div>
            <div class="FAIR-si-button">
            <input type="submit" class="btn btn-danger" value="Termin erstellen" name="create_termine1" />
            </div>';
            echo H_build_boxinfo( 0, "Optional:<br>Nur eingeben, wenn der Ort und die Adresse für diesen Termin abweichend vom Standard-Wert", 'blue' );
            echo '
            </div>
            </form>';
            echo $errorhtml1;
            echo '</div>';
        } else {
            echo '
            <div class="col-lg-6"><div class="card">
            <h3>Ohne Terminbuchung / Offener Termin</h3>
            <p class="list-group-item-text">Diese Option ist gesperrt für das Impfzentrum</p><p></p>';
            echo '
            <div class="input-group">
            <span class="input-group-addon" id="basic-addon1">Station</span>
            <input type="text" id="select-state" class="form-control" aria-describedby="basic-addon1" name="station_id" disabled>
            </div>
            <div class="input-group">
            <span class="input-group-addon" id="basic-addon4">Datum</span>
            <input type="date" class="form-control" aria-describedby="basic-addon4" value="" name="date" disabled>
            </div>

            <div class="input-group">
            <span class="input-group-addon" id="basic-addon4">Von</span>
            <input type="time" class="form-control" aria-describedby="basic-addon4" name="startzeit" disabled>
            <span class="input-group-addon" id="basic-addon4">Bis</span>
            <input type="time" class="form-control" aria-describedby="basic-addon4" name="endzeit" disabled>
            </div>
            <div class="input-group">
            <span class="input-group-addon" id="basic-addon4">Optional Name d. Ortes</span>
            <input type="text" class="form-control" aria-describedby="basic-addon4" name="opt_station" disabled>
            <span class="input-group-addon" id="basic-addon4">Optional Adresse</span>
            <input type="text" class="form-control" aria-describedby="basic-addon4" name="opt_station_adresse" disabled>
            </div>
            <div class="FAIR-si-button">
            <input type="submit" class="btn btn-danger" value="Termin erstellen" name="create_termine1" disabled/>
            </div>';
            echo H_build_boxinfo( 0, "Diese Option für Termine steht nur dem Testzentrum zur Verfügung. Bitte nutzen Sie stattdessen die Terminbuchungsoption.", 'red' );
            echo '
            </div>
            </form>';
            echo $errorhtml1;
            echo '</div>';
        }

        echo '
        <div class="col-lg-6"><div class="card">
        <h3>Mit Terminbuchung und verpflichtender Voranmeldung</h3>
        <p class="list-group-item-text">Zum Erstellen bitte alle Felder ausfüllen.</p><p></p>';
        echo '<form action="'.$current_site.'.php" method="post">
        <div class="input-group">
            <span class="input-group-addon" id="basic-addon1">Station</span>
            <select id="select-state-2" placeholder="Wähle eine Station..." class="custom-select" style="margin-top:0px;" name="station_id">
            <option value="" selected>Wähle Station...</option>
                ';
                foreach($stations_array as $i) {
                    if($GLOBALS['FLAG_MODE_MAIN'] == 1 || $GLOBALS['FLAG_MODE_MAIN'] == 3) {
                        $display=$i[1].' / S'.$i[0];
                        echo '<option value="'.$i[0].'">'.$display.'</option>';
                    } else {
                        $display=$i[2].' ('.$i[1].' / S'.$i[0].')';
                        echo '<option value="'.$i[0].'">'.$display.'</option>';
                    }
                }
                echo '
            </select>
            </div>
            <div class="input-group">
            <span class="input-group-addon" id="basic-addon4">Datum</span>
            <input type="date" class="form-control" placeholder="JJJJ-MM-DD" aria-describedby="basic-addon4" value="" name="date">
            <span class="input-group-addon" id="basic-addon4">Termine pro Slot (15 min.)</span>
            <input type="number" min="1" max="99" class="form-control" aria-describedby="basic-addon4" name="terminzahl_slot">
            </div>

            <div class="input-group">
            <span class="input-group-addon" id="basic-addon4">Erster Termin</span>
            <input type="time" step="900" class="form-control" aria-describedby="basic-addon4" name="startzeit_slot">
            <span class="input-group-addon" id="basic-addon4">Letzter Termin (inklusiv)</span>
            <input type="time" step="900" class="form-control" aria-describedby="basic-addon4" name="endzeit_slot">

            
            </div>

            <div class="input-group">
            <span class="input-group-addon" id="basic-addon4">Optional Name d. Ortes</span>
            <input type="text" class="form-control" aria-describedby="basic-addon4" name="opt_station">
            <span class="input-group-addon" id="basic-addon4">Optional Adresse</span>
            <input type="text" class="form-control" aria-describedby="basic-addon4" name="opt_station_adresse">
            </div>
            <div class="FAIR-si-button">
            <input type="submit" class="btn btn-danger" value="Buchbare Termine erstellen" name="create_termine2" />
            </div>';
            echo H_build_boxinfo( 0, "Information:<br>Beispielsweise ist der erste Termin von 8:00 bis 8:15 Uhr und der letzte Termin von 16:45 bis 17:00 Uhr, muss auch 08:00 und 16:45 eingetragen werden.", 'yellow' );
            echo H_build_boxinfo( 0, "Optional:<br>Nur eingeben, wenn der Ort und die Adresse für diesen Termin abweichend vom Standard-Wert", 'blue' );
            echo '</div>
            </form>';
            echo $errorhtml2;
        echo '</div></div>';


    }

    echo '</div>';
      
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