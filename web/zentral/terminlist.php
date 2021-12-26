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
        
    } else {
        if( isset($_GET['station']) && isset($_GET['date']) ) {
            $sel_date=$_GET['date'];
            $sel_station=$_GET['station'];
            $station=$sel_station;
        }
    }

    // Print html header
    echo $GLOBALS['G_html_header_start'];
    echo '
    <style>
    .nav-tab-active > a {
        background-color: #2c95c9;
        color: #000;
      }
      .nav-tab > a {
        cursor: pointer;
      }
      
      .tab-content {
        display: none;
      }
      
      .tab-content.active {
        display: block;
      }
      </style>
    <script>
    $( document ).ready(function() {
        $(\'.nav-tab\').click(function(e) {
            //Toggle tab link
            $(this).addClass(\'nav-tab-active\').siblings().removeClass(\'nav-tab-active\');
        
            //Toggle target tab
            $($(this).attr(\'href\')).addClass(\'active\').siblings().removeClass(\'active\');
        });
    });
      </script>
      ';
    echo $GLOBALS['G_html_header_end'];

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
    

    if( A_checkpermission(array(1,0,0,0,5)) && !A_checkpermission(array(0,2,0,4,0)) ) {
        $station=$_SESSION['station_id'];
    }


    // Show all Termine for selected station and date
    
    if(isset($sel_date)) {
        echo '<div class="col-sm-12">
        <div class="card">';
        $station_name=S_get_entry($Db,'SELECT Ort FROM Station WHERE id='.$sel_station.';');
        if($GLOBALS['FLAG_MODE_MAIN'] == 2) {
            $impfstoff_name=S_get_entry($Db,'SELECT Impfstoff.Kurzbezeichnung FROM Station JOIN Impfstoff ON Impfstoff.id=Station.Impfstoff_id WHERE Station.id='.$sel_station.';');
            echo '<h3>Station S'.sprintf('%02d',$sel_station).' / '.$station_name.' / '.$impfstoff_name.'</h3>';
        } else {
            echo '<h3>Station S'.sprintf('%02d',$sel_station).' / '.$station_name.'</h3>';
        }
        echo '</div></div>';

        if($GLOBALS['FLAG_MODE_MAIN'] == 1) {
            // Ohne Terminbuchung
            $array_termine_free2come=S_get_multientry($Db,'SELECT id,Tag,Startzeit,Endzeit,opt_station,opt_station_adresse FROM Termine WHERE Slot is null AND id_station='.$station.' AND Date(Tag)=\''.$sel_date.'\';');
        
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
        }


        // Show all Termine for selected station
        // Mit Terminbuchung
        $array_termine_reservation=S_get_multientry($Db,'SELECT id,Tag,Stunde,Slot,opt_station,opt_station_adresse,count(Slot),sum(Used) FROM Termine WHERE Slot>0 AND id_station='.$station.' AND Date(Tag)=\''.$sel_date.'\' GROUP BY id_station,Tag,Stunde,Slot;');
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
                        <input type="submit" class="btn btn-success" value="Freie Term. im Slot löschen" name="delete_termin_free1slot" />
                        <input type="submit" class="btn btn-warning" value="Alle Term. im Slot löschen" name="delete_termin_all1slot" />
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
    } else {
        echo '<div class="col-sm-12">
        <div class="card">';
        echo '<p><span class="label label-danger">Zum Bearbeiten von Terminen bitte zuerst einen Kalendereintrag unten auswählen.</span></p>';
        echo '</div></div>';
    }



    if( A_checkpermission(array(0,2,0,4,0)) ) {
        echo '<div class="col-sm-12">
        <div class="card">
        <h3>Alle Stationen in den nächsten Tagen (inkl. Firmen)</h3>';

        if($GLOBALS['FLAG_MODE_MAIN'] == 2) {
            // Tabs for station or vaccine summarized list
            echo '
            <ul class="nav nav-pills" role="tablist">
            <li role="presentation" class="nav-tab nav-tab-active" href="#tab-station"><a>Nach einzelnen Stationen</a></li>
            <li role="presentation" class="nav-tab" href="#tab-vacc"><a>Nach Impfstoff summiert</a></li>
            </ul>
            ';
        }



        if($GLOBALS['FLAG_MODE_MAIN'] == 1) {
            $calendar=H_build_table_testdates_new_2_0('');
        } elseif($GLOBALS['FLAG_MODE_MAIN'] == 3) {
            $calendar=H_build_table_testdates_new_2_0('antikoerper');
        } else {
            $calendar=H_build_table_testdates_new_2_0('vaccinate');
        }
        if($GLOBALS['FLAG_MODE_MAIN'] == 2) {
            echo '<div id="tab-station" class="tab-content active">';
            foreach($calendar[0] as $i) {
                //rows
                foreach($i as $print) {
                    //columns
                    if($print!='') {
                        echo $print;
                    }
                }
            }
            echo '</div>';
            echo '<div id="tab-vacc" class="tab-content">';
            foreach($calendar[1] as $i) {
                //rows
                foreach($i as $print) {
                    //columns
                    if($print!='') {
                        echo $print;
                    }
                }
            }
            echo '</div>';
        } else {
            foreach($calendar[0] as $i) {
                //rows
                foreach($i as $print) {
                    //columns
                    if($print!='') {
                        echo $print;
                    }
                }
            }
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