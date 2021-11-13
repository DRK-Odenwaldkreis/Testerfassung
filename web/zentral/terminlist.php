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
$current_site="terminlist";

// Include functions
include_once 'tools.php';
include_once 'auth.php';
include_once 'menu.php';

// role check
if( A_checkpermission(array(1,2,0,4,5)) ) {

    $errorhtml0 ='';

    // Open database connection
    $Db=S_open_db();

    // Select station
    // Delete Termine
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        if(isset($_POST['select_station'])) {
            // Select station
            $station=$_POST['station_id'];
        } elseif(isset($_POST['delete_termin_all1slot'])) {
            // Delete all Termine in one slot
            $termin_id=$_POST['termin_id'];
            if($termin_id>0) {
                $array_del_termin=S_get_multientry($Db,'SELECT id_station,Tag,Stunde,Slot FROM Termine WHERE id=CAST('.$termin_id.' as int);');
                S_set_data($Db,'DELETE From Termine WHERE id_station='.$array_del_termin[0][0].' AND Tag=\''.$array_del_termin[0][1].'\' AND Stunde='.$array_del_termin[0][2].' AND Slot='.$array_del_termin[0][3].';');
            }
            $station=$array_del_termin[0][0];
        } elseif(isset($_POST['delete_termin_all1day'])) {
            // Delete all Termine for one day
            $termin_id=$_POST['termin_id'];
            if($termin_id>0) {
                $array_del_termin=S_get_multientry($Db,'SELECT id_station,Tag,Stunde,Slot FROM Termine WHERE id=CAST('.$termin_id.' as int);');
                S_set_data($Db,'DELETE From Termine WHERE id_station='.$array_del_termin[0][0].' AND Tag=\''.$array_del_termin[0][1].'\';');
            }
            $station=$array_del_termin[0][0];
        } elseif(isset($_POST['delete_termin_free1day'])) {
            // Delete all unused Termine for one day
            $termin_id=$_POST['termin_id'];
            if($termin_id>0) {
                $array_del_termin=S_get_multientry($Db,'SELECT id_station,Tag,Stunde,Slot FROM Termine WHERE id=CAST('.$termin_id.' as int);');
                S_set_data($Db,'DELETE From Termine WHERE id_station='.$array_del_termin[0][0].' AND Tag=\''.$array_del_termin[0][1].'\' AND Used is null;');
            }
            $station=$array_del_termin[0][0];
        } elseif(isset($_POST['delete_termin_free1slot'])) {
            // Delete Termine in one slot with no reservation
            $termin_id=$_POST['termin_id'];
            if($termin_id>0) {
                $array_del_termin=S_get_multientry($Db,'SELECT id_station,Tag,Stunde,Slot FROM Termine WHERE id=CAST('.$termin_id.' as int);');
                S_set_data($Db,'DELETE From Termine WHERE id_station='.$array_del_termin[0][0].' AND Tag=\''.$array_del_termin[0][1].'\' AND Stunde='.$array_del_termin[0][2].' AND Slot='.$array_del_termin[0][3].' AND Used is null;');
            }
            $station=$array_del_termin[0][0];
        } elseif(isset($_POST['delete_termin'])) {
            // Delete one Termin with no appointment
            $termin_id=$_POST['termin_id'];
            if($termin_id>0) {
                $station=S_get_entry($Db,'SELECT id_station FROM Termine WHERE id=CAST('.$termin_id.' as int);');
                S_set_data($Db,'DELETE From Termine WHERE id=CAST('.$termin_id.' as int);');
            } else {
                $station=$_SESSION['station_id'];
            }
        } else {
            $station=$_SESSION['station_id'];
        }
        
    }

    // Print html header
    echo $GLOBALS['G_html_header'];

    // Print html menu
    echo $GLOBALS['G_html_menu'];
    echo $GLOBALS['G_html_menu2'];

    // Print html content part A
    echo $GLOBALS['G_html_main_right_a'];

    echo '<h1>Terminüberblick</h1>';


    echo '<div class="row">';

    if($GLOBALS['FLAG_MODE_MAIN'] == 1 || $GLOBALS['FLAG_MODE_MAIN'] == 3) {
        $stations_array=S_get_multientry($Db,'SELECT id, Ort, Adresse FROM Station;');
    } else {
        $stations_array=S_get_multientry($Db,'SELECT Station.id, Station.Ort, Station.Adresse, Impfstoff.Kurzbezeichnung FROM Station JOIN Impfstoff ON Impfstoff.id=Station.Impfstoff_id;');
    }
    $today=date("Y-m-d",time());
    
    echo '<div class="col-sm-5">
      <div class="card">';
    if( A_checkpermission(array(1,0,0,0,5)) && !A_checkpermission(array(0,2,0,4,0)) ) {
        echo '<p>Eigene Station S'.$_SESSION['station_id'].'/'.$_SESSION['station_name'].'</p>';
        $station=$_SESSION['station_id'];
    } else {
        echo '<p>Station wählen:</p>';
        echo '<form action="'.$current_site.'.php" method="post">
        <div class="input-group">
        <span class="input-group-addon" id="basic-addon1">Station</span>
        <select id="select-state" placeholder="Wähle eine Station..." class="custom-select" style="margin-top:0px;" name="station_id">
        <option value="">Wähle Station...</option>
            ';
            foreach($stations_array as $i) {
                if($GLOBALS['FLAG_MODE_MAIN'] == 1 || $GLOBALS['FLAG_MODE_MAIN'] == 3) {
                    $display=$i[1].' / S'.$i[0];
                } else {
                    $display=$i[3].' ('.$i[1].' / S'.$i[0].')';
                }
                if($i[0]==$station) {$selected="selected";} else {$selected="";}
                echo '<option value="'.$i[0].'" '.$selected.'>'.$display.'</option>';
            }
            echo '
        </select>
        <span class="input-group-btn">
        <input type="submit" class="btn btn-danger" value="Anzeigen" name="select_station" />
        </span>
        </div>
        </form>';
        
    }
    echo '</div></div>';

    // Show all Termine for selected station
    // Ohne Terminbuchung
    $array_termine_free2come=S_get_multientry($Db,'SELECT id,Tag,Startzeit,Endzeit,opt_station,opt_station_adresse FROM Termine WHERE Slot is null AND id_station='.$station.' AND Date(Tag)>=\''.$today.'\';');
    echo '<div class="col-sm-12">
      <div class="card">';
    echo '<table class="FAIR-data">
      <tr><td class="FAIR-data-height1 FAIR-data-right FAIR-data-left FAIR-data-bottom FAIR-data-top FAIR-data-blue1" colspan="5"><b><i>Ohne Terminbuchung</i></b></td></tr>
      ';
    if($array_termine_free2come==NULL) {
        echo '<tr><td class="FAIR-data-height2 FAIR-data-right FAIR-data-left FAIR-data-bottom FAIR-data-top FAIR-data-blue3" colspan="4">
        Keine Termine ohne Terminbuchung gefunden
        </td></tr>';
    } else {
        echo '
      <tr>
      <td class="FAIR-data-height2 FAIR-data-right FAIR-data-left FAIR-data-bottom FAIR-data-top FAIR-data-blue2"><h4>Datum</h4></td>
      <td class="FAIR-data-height2 FAIR-data-right FAIR-data-left FAIR-data-bottom FAIR-data-top FAIR-data-blue2"><h4>Zeit</h4></td>
      <td class="FAIR-data-height2 FAIR-data-right FAIR-data-left FAIR-data-bottom FAIR-data-top FAIR-data-blue2"><h4>Reserviert</h4></td>
      <td class="FAIR-data-height2 FAIR-data-right FAIR-data-left FAIR-data-bottom FAIR-data-top FAIR-data-blue2"></td>
      <td class="FAIR-data-height2 FAIR-data-right FAIR-data-left FAIR-data-bottom FAIR-data-top FAIR-data-blue2"><h4>Ort</h4></td>
      </tr>';
        foreach($array_termine_free2come as $i) {
            if($i[4]=='') {
                $display_location_opt='Standard: '.S_get_entry($Db,'SELECT Ort FROM Station WHERE id='.$station.';').' / '.S_get_entry($Db,'SELECT Adresse FROM Station WHERE id='.$station.';');
            } else {
                $display_location_opt=$i[4].' / '.$i[5];
            }
            // How many have registered for this free2come Termin
            $value_reservation=S_get_entry($Db,'SELECT count(id) FROM Voranmeldung WHERE Termin_id='.$i[0].';');
            $display_termine='<span class="label label-danger">'.sprintf('%01d',$value_reservation).'</span>';

            echo '<tr>
            <td class="FAIR-data-height2 FAIR-data-right FAIR-data-left FAIR-data-bottom FAIR-data-top FAIR-data-blue3">'.date("d.m.Y",strtotime($i[1])).'</td>
            <td class="FAIR-data-height2 FAIR-data-right FAIR-data-left FAIR-data-bottom FAIR-data-top FAIR-data-blue3">'.date("H:i",strtotime($i[2])).' - '.date("H:i",strtotime($i[3])).'</td>
            <td class="FAIR-data-height2 FAIR-data-right FAIR-data-left FAIR-data-bottom FAIR-data-top FAIR-data-blue3">'.$display_termine.'</td>
            <td class="FAIR-data-height2 FAIR-data-right FAIR-data-left FAIR-data-bottom FAIR-data-top FAIR-data-blue3">';
            if( A_checkpermission(array(0,2,0,4,5)) ) {
                echo '<form action="'.$current_site.'.php" method="post">
                <div class="input-group">';
                echo '<input type="text" value="'.$i[0].'" name="termin_id" style="display:none;">';
                echo'<span class="input-group-btn">
                    <input type="submit" class="btn btn-danger" value="Termin löschen" name="delete_termin" />
                    </span>';
                echo '</div></form>';
            }
            echo '</td>
            
            <td class="FAIR-data-height2 FAIR-data-right FAIR-data-left FAIR-data-bottom FAIR-data-top FAIR-data-blue3">'.$display_location_opt.'</td>
            </tr>';
        }
        echo '<tr>
    <td class="FAIR-data-height2 FAIR-data-right FAIR-data-left FAIR-data-bottom FAIR-data-top FAIR-data-blue2"><h4>Datum</h4></td>
    <td class="FAIR-data-height2 FAIR-data-right FAIR-data-left FAIR-data-bottom FAIR-data-top FAIR-data-blue2"><h4>Zeit</h4></td>
    <td class="FAIR-data-height2 FAIR-data-right FAIR-data-left FAIR-data-bottom FAIR-data-top FAIR-data-blue2"><h4>Reserviert</h4></td>
    <td class="FAIR-data-height2 FAIR-data-right FAIR-data-left FAIR-data-bottom FAIR-data-top FAIR-data-blue2"></td>
    <td class="FAIR-data-height2 FAIR-data-right FAIR-data-left FAIR-data-bottom FAIR-data-top FAIR-data-blue2"><h4>Ort</h4></td>
    </tr>';
    }
    echo '</table>';
    echo '</div></div>';


    // Show all Termine for selected station
    // Mit Terminbuchung
    $array_termine_reservation=S_get_multientry($Db,'SELECT id,Tag,Stunde,Slot,opt_station,opt_station_adresse,count(Slot),sum(Used) FROM Termine WHERE Slot>0 AND id_station='.$station.' AND Date(Tag)>=\''.$today.'\' GROUP BY id_station,Tag,Stunde,Slot;');
    echo '<div class="col-sm-12">
      <div class="card">';
    echo '<table class="FAIR-data">
    <tr><td class="FAIR-data-height1 FAIR-data-right FAIR-data-left FAIR-data-bottom FAIR-data-top FAIR-data-yellow1" colspan="5"><b><i>Mit Terminbuchung</i></b></td></tr>';
    if($array_termine_reservation==NULL) {
        echo '<tr><td class="FAIR-data-height2 FAIR-data-right FAIR-data-left FAIR-data-bottom FAIR-data-top FAIR-data-yellow3" colspan="5">
        Keine Termine mit Terminbuchung gefunden
        </td></tr>';
    } else {
        echo '<tr>
    <td class="FAIR-data-height2 FAIR-data-right FAIR-data-left FAIR-data-bottom FAIR-data-top FAIR-data-yellow2"><h4>Datum</h4></td>
    <td class="FAIR-data-height2 FAIR-data-right FAIR-data-left FAIR-data-bottom FAIR-data-top FAIR-data-yellow2"><h4>Zeit</h4></td>
    <td class="FAIR-data-height2 FAIR-data-right FAIR-data-left FAIR-data-bottom FAIR-data-top FAIR-data-yellow2"><h4>Termine</h4></td>
    <td class="FAIR-data-height2 FAIR-data-right FAIR-data-left FAIR-data-bottom FAIR-data-top FAIR-data-yellow2"></td>
    <td class="FAIR-data-height2 FAIR-data-right FAIR-data-left FAIR-data-bottom FAIR-data-top FAIR-data-yellow2"><h4>Ort</h4></td>
    </tr>';
        foreach($array_termine_reservation as $i) {
            if($i[4]=='') {
                $display_location_opt='Standard: '.S_get_entry($Db,'SELECT Ort FROM Station WHERE id='.$station.';').' / '.S_get_entry($Db,'SELECT Adresse FROM Station WHERE id='.$station.';');
            } else {
                $display_location_opt=$i[4].' / '.$i[5];
            }

            $display_slot=sprintf('%02d', $i[2]).':'.sprintf('%02d', ( $i[3]*15-15 ) );
            $display_slot.=' - '.(date("H:i",strtotime($display_slot) + 60 * 15));
            $display_termine='<span class="label label-success">'.($i[6]-$i[7]).'</span>&nbsp;<span class="label label-danger">'.sprintf('%01d',$i[7]).'</span>';

            echo '<tr>
            <td class="FAIR-data-height2 FAIR-data-right FAIR-data-left FAIR-data-bottom FAIR-data-top FAIR-data-yellow3">'.date("d.m.Y",strtotime($i[1])).'</td>
            <td class="FAIR-data-height2 FAIR-data-right FAIR-data-left FAIR-data-bottom FAIR-data-top FAIR-data-yellow3">'.$display_slot.'</td>
            <td class="FAIR-data-height2 FAIR-data-right FAIR-data-left FAIR-data-bottom FAIR-data-top FAIR-data-yellow3">'.$display_termine.'</td>
            
            <td class="FAIR-data-height2 FAIR-data-right FAIR-data-left FAIR-data-bottom FAIR-data-top FAIR-data-yellow3">';
            if( A_checkpermission(array(0,2,0,4,5)) ) {
                echo '<form action="'.$current_site.'.php" method="post">
                <div class="input-group">';
                echo '<input type="text" value="'.$i[0].'" name="termin_id" style="display:none;">';
                echo'<span class="input-group-btn">
                    <input type="submit" class="btn btn-info" value="Alle freien Term. für S'.$station.' am '.date("d.m.",strtotime($i[1])).' löschen" name="delete_termin_free1day" />
                    <input type="submit" class="btn btn-success" value="Freie Term. löschen" name="delete_" />
                    <input type="submit" class="btn btn-warning" value="Alle Term. löschen" name="delete_termin_all1slot" />
                    <input type="submit" class="btn btn-danger" value="Alle Term. für S'.$station.' am '.date("d.m.",strtotime($i[1])).' löschen" name="delete_termin_all1day" />
                    </span>';
                echo '</div></form>';
            }
            echo '</td>
            <td class="FAIR-data-height2 FAIR-data-right FAIR-data-left FAIR-data-bottom FAIR-data-top FAIR-data-yellow3">'.$display_location_opt.'</td>
            </tr>';

        }
        echo '<tr>
    <td class="FAIR-data-height2 FAIR-data-right FAIR-data-left FAIR-data-bottom FAIR-data-top FAIR-data-yellow2"><h4>Datum</h4></td>
    <td class="FAIR-data-height2 FAIR-data-right FAIR-data-left FAIR-data-bottom FAIR-data-top FAIR-data-yellow2"><h4>Zeit</h4></td>
    <td class="FAIR-data-height2 FAIR-data-right FAIR-data-left FAIR-data-bottom FAIR-data-top FAIR-data-yellow2"><h4>Termine</h4></td>
    <td class="FAIR-data-height2 FAIR-data-right FAIR-data-left FAIR-data-bottom FAIR-data-top FAIR-data-yellow2"></td>
    <td class="FAIR-data-height2 FAIR-data-right FAIR-data-left FAIR-data-bottom FAIR-data-top FAIR-data-yellow2"><h4>Ort</h4></td>
    </tr>';
    }
    echo '</table>';
    echo '</div></div>';



    if( A_checkpermission(array(0,2,0,4,0)) ) {
        echo '<div class="col-sm-12">
        <div class="card">
        <h3>Alle Stationen in den nächsten Tagen (inkl. Firmen)</h3>';
        if($GLOBALS['FLAG_MODE_MAIN'] == 1 || $GLOBALS['FLAG_MODE_MAIN'] == 3) {
            echo H_build_table_testdates_all('');
        } else {
            echo H_build_table_testdates_all('vaccinate');
        }
        echo '</div></div>';
    }


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