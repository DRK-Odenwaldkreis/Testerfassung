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
$current_site="station_admin";

// Include functions
include_once 'tools.php';
include_once 'auth.php';
include_once 'menu.php';

// role check
if( A_checkpermission(array(0,2,0,4,0)) ) {


    // Open database connection
    $Db=S_open_db();

    $errorhtml1 ='';
    $errorhtml2 ='';
    $errorhtml3 ='';
    $errorhtml4 ='';


    // Show user
    $bool_staff_display=false;
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {

        // Edit user in database
        if(isset($_POST['edit_station'])) {
            $user_id=($_POST['user_id']);
            $station_ort=($_POST['e_username']);
            $address=($_POST['e_address']);
            $opening=($_POST['e_opening']);
            $b2b_code=($_POST['e_b2b']);
            $testtyp=($_POST['e_testtyp']);

            //  edit station data
            if($GLOBALS['FLAG_MODE_MAIN'] == 1) {
                S_set_data($Db,'UPDATE Station SET Ort=\''.$station_ort.'\',Adresse=\''.$address.'\',Oeffnungszeiten=\''.$opening.'\',Firmencode=\''.$b2b_code.'\',Testtyp_id=\''.$testtyp.'\' WHERE id='.$user_id.';');
            } else {
                S_set_data($Db,'UPDATE Station SET Ort=\''.$station_ort.'\',Adresse=\''.$address.'\',Oeffnungszeiten=\''.$opening.'\',Firmencode=\''.$b2b_code.'\',Impfstoff_id=\''.$testtyp.'\' WHERE id='.$user_id.';');
            }
            $errorhtml3 =  H_build_boxinfo( 0, 'Änderungen wurden gespeichert.', 'green' );

        } elseif(isset($_POST['create_station'])) {
            $stationid_new=($_POST['n_station_id']);
            $station_ort=($_POST['n_username']);
            $address=($_POST['n_address']);
            $b2b_code=($_POST['n_b2b']);
            $testtyp=($_POST['n_testtyp']);
            $new_id=S_get_entry($Db,'SELECT id FROM Station WHERE id=CAST('.$stationid_new.' as int);');
            if($station_ort=='' || $new_id>0) {
                $errorhtml2 =  H_build_boxinfo( 0, 'Fehler beim Erstellen, möglicherweise ist die gewählte Stations-ID bereits vergeben.', 'red' );
            } else {
                
                if($GLOBALS['FLAG_MODE_MAIN'] == 1) {
                    S_set_data($Db,'INSERT INTO Station (id, Ort, Adresse, Firmencode, Testtyp_id) VALUES (
                        CAST('.$stationid_new.' AS int),
                        \''.$station_ort.'\',
                        \''.$address.'\',
                        \''.$b2b_code.'\',
                        '.$testtyp.');');
                } elseif($GLOBALS['FLAG_MODE_MAIN'] == 2) {
                    S_set_data($Db,'INSERT INTO Station (id, Ort, Adresse, Firmencode, Impfstoff_id) VALUES (
                        CAST('.$stationid_new.' AS int),
                        \''.$station_ort.'\',
                        \''.$address.'\',
                        \''.$b2b_code.'\',
                        '.$testtyp.');');
                } elseif($GLOBALS['FLAG_MODE_MAIN'] == 3) {
                    S_set_data($Db,'INSERT INTO Station (id, Ort, Adresse, Firmencode) VALUES (
                        CAST('.$stationid_new.' AS int),
                        \''.$station_ort.'\',
                        \''.$address.'\',
                        \''.$b2b_code.'\');');
                }
                // Create web user for new station
                $new_pw=A_generate_token(14);
                $new_username='team_'.str_replace(' ','',$station_ort);
                $newpasswordhash = password_hash($new_pw, PASSWORD_BCRYPT);
                S_set_data($Db,'INSERT INTO li_user (username,password_hash,Station) VALUES (
                    \''.$new_username.'\',
                    \''.$newpasswordhash.'\',
                    CAST('.$stationid_new.' AS int));');
                $new_user_id=S_get_entry($Db,'SELECT id FROM li_user WHERE Username=\''.('team_'.$station_ort).'\'');

                // Send job for PDF with QR code for scanning password
                $dir="/home/webservice/Testerfassung/LoginSheetJob/";
                chdir($dir);
                $station_ort_space=str_replace(' ','',$station_ort);
                $job="python3 job.py ".$_SESSION['uid']." $new_username $new_pw $station_ort_space";
                exec($job,$script_output);
                $file=$script_output[0];
                if( file_exists("/home/webservice/LoginSheet/$file") ) {
                    header('Content-Type: application/octet-stream');
                    header('Content-Disposition: attachment; filename="'.basename($file).'"');
                    header('Pragma: no-cache');
                    header('Expires: 0');
                    readfile("/home/webservice/LoginSheet/$file");
                    exit;
                }

                $errorhtml2 =  H_build_boxinfo( 0, 'Station S'.$stationid_new.' - '.$station_ort.' wurde erstellt. Ein Stations-Benutzer wurde angelegt, die PDF wurde erstellt und zum Download angeboten.', 'green' );
            }
        }

        // Search on number
        if( isset($_POST['search_station']) || isset($_POST['edit_station']) || isset($_POST['create_station']) ) {
            if( isset($_POST['search_station']) ) {
                $user_id=($_POST['user_id']);
            } elseif(isset($_POST['create_station'])) {
                $user_id=$stationid_new;
            }
            $bool_staff_display=true;
            $u_name=S_get_entry($Db,'SELECT Ort FROM Station WHERE id=CAST('.$user_id.' AS int);');
            $u_address=S_get_entry($Db,'SELECT Adresse FROM Station WHERE id=CAST('.$user_id.' AS int);');
            $u_opening=S_get_entry($Db,'SELECT Oeffnungszeiten FROM Station WHERE id=CAST('.$user_id.' AS int);');
            $u_b2b=S_get_entry($Db,'SELECT Firmencode FROM Station WHERE id=CAST('.$user_id.' AS int);');
            if($GLOBALS['FLAG_MODE_MAIN'] == 1) {
                $u_testtyp_id=S_get_entry($Db,'SELECT Testtyp_id FROM Station WHERE id=CAST('.$user_id.' AS int);');
            } elseif($GLOBALS['FLAG_MODE_MAIN'] == 2) {
                $u_testtyp_id=S_get_entry($Db,'SELECT Impfstoff_id FROM Station WHERE id=CAST('.$user_id.' AS int);');
            }
        }

    }

    // Get user details
    $array_staff=S_get_multientry($Db,'SELECT id, Ort, Adresse, Firmencode FROM Station;');
    if($GLOBALS['FLAG_MODE_MAIN'] == 1) {
        $testtyp_array=S_get_multientry($Db,'SELECT id, Kurzbezeichnung FROM Testtyp;');
    } elseif($GLOBALS['FLAG_MODE_MAIN'] == 2) {
        $testtyp_array=S_get_multientry($Db,'SELECT id, Kurzbezeichnung FROM Impfstoff;');
    }

    // Print html header
    echo $GLOBALS['G_html_header'];

    // Print html menu
    echo $GLOBALS['G_html_menu'];
    echo $GLOBALS['G_html_menu2'];

    // Print html content part A
    echo $GLOBALS['G_html_main_right_a'];

    echo '<h1>Admin: Stations-Management</h1>';
    //
    // Select user
    //
    echo '
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/selectize.js/0.12.6/js/standalone/selectize.min.js" integrity="sha256-+C0A5Ilqmu4QcSPxrlGpaZxJ04VjsRjKu+G82kl5UJk=" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/selectize.js/0.12.6/css/selectize.bootstrap3.min.css" integrity="sha256-ze/OEYGcFbPRmvCnrSeKbRTtjG4vGLHXgOqsyLFTRjg=" crossorigin="anonymous" />';

    echo "<script>
    $(document).ready(function () {
        $('#select-state').selectize({
            sortField: 'text'
        });
    });
    </script>";


    echo '<div class="card"><div class="row">
    <div class="col-sm-4">
    <h3>Station wählen</h3>';

    echo'<form action="'.$current_site.'.php" method="post">
    <div class="input-group">
    <span class="input-group-addon" id="basic-addon1">Nr.</span>
    <select id="select-state" placeholder="Wähle eine Station..." name="user_id">
    <option value="" selected>Wähle...</option>
        ';
        foreach($array_staff as $i) {
            $display='S'.sprintf('%02d',$i[0]).' '.$i[1].' ('.$i[2].')';
            echo '<option value="'.$i[0].'">'.$display.'</option>';
        }
        echo '
    </select>
    </div>
    <div class="FAIR-si-button">
    <input type="submit" class="btn btn-danger" value="Anzeigen" name="search_station" />
    </div></form>';
    echo $errorhtml4;
    echo $errorhtml3;
    echo $errorhtml1;
    echo '</div>';

    if($bool_staff_display) {
        // Show data of station
        echo '<div class="col-sm-8">
        <h3>Station S'.$user_id.'</h3>
        <p>'.$u_display.'</p>';

        echo'<form action="'.$current_site.'.php" method="post">
        <div class="input-group">
        <input type="text" value="'.$user_id.'" name="user_id" style="display:none;">
        <span class="input-group-addon" id="basic-addon1">Stationsname (Ort)</span>
        <input type="text" class="form-control" placeholder="" aria-describedby="basic-addon1" name="e_username" autocomplete="off" value="'.$u_name.'">
        <span class="input-group-addon" id="basic-addon1">Adresse</span>
        <input type="text" class="form-control" placeholder="" aria-describedby="basic-addon1" name="e_address" autocomplete="off" value="'.$u_address.'">
        </div><div class="input-group">
        <span class="input-group-addon" id="basic-addon1">Öffnungsz.</span>
        <input type="text" class="form-control" placeholder="" aria-describedby="basic-addon1" name="e_opening" autocomplete="off" value="'.$u_opening.'">
        <span class="input-group-addon" id="basic-addon1">Firmencode</span>
        <input type="text" class="form-control" placeholder="" aria-describedby="basic-addon1" name="e_b2b" autocomplete="off" value="'.$u_b2b.'">
        </div><div class="input-group">';
        if($GLOBALS['FLAG_MODE_MAIN'] == 1 || $GLOBALS['FLAG_MODE_MAIN'] == 2) {
            echo '<span class="input-group-addon" id="basic-addon1">Testtyp/Impfstoff</span>
            <select id="select-state_typ" placeholder="Wähle einen Standard-Typ..." class="custom-select" style="margin-top:0px;" name="e_testtyp">
            <option value="" selected>Wähle...</option>
                ';
                foreach($testtyp_array as $i) {
                    $display='T'.$i[0].' / '.$i[1];
                    if($i[0]==$u_testtyp_id) { $selected='selected'; } else { $selected=''; }
                    echo '<option value="'.$i[0].'" '.$selected.'>'.$display.'</option>';
                }
                echo '
            </select>
        </div>';
        }
        echo '<div class="FAIR-si-button">
        <input type="submit" class="btn btn-danger" value="Änderung speichern" name="edit_station" />
        </div></form>';

        echo '</div>';
    }

    echo '</div></div>';


    // Show station list
    echo '<div class="card"><div class="row">
    <div class="col-sm-12">
    <h3>Stationen</h3>';

    echo '<table class="FAIR-data">
      <tr>
      <td class="FAIR-data-height2 FAIR-data-right FAIR-data-left FAIR-data-bottom FAIR-data-top"><h4>Nr.</h4></td>
      <td class="FAIR-data-height2 FAIR-data-right FAIR-data-left FAIR-data-bottom FAIR-data-top"><h4>Ort</h4></td>
      <td class="FAIR-data-height2 FAIR-data-right FAIR-data-left FAIR-data-bottom FAIR-data-top"><h4>Adresse</h4></td>
      <td class="FAIR-data-height2 FAIR-data-right FAIR-data-left FAIR-data-bottom FAIR-data-top"><h4>Öffnungsz.</h4></td>
      <td class="FAIR-data-height2 FAIR-data-right FAIR-data-left FAIR-data-bottom FAIR-data-top"><h4>Testtyp/Impfstoff</h4></td>
      <td class="FAIR-data-height2 FAIR-data-right FAIR-data-left FAIR-data-bottom FAIR-data-top"><h4>Firmencode</h4></td>
      </tr>';
    if($GLOBALS['FLAG_MODE_MAIN'] == 1) {
        $array_station=S_get_multientry($Db,'SELECT Station.id, Station.Ort, Station.Adresse, Station.Firmencode, Testtyp.id, Testtyp.Kurzbezeichnung, Station.Oeffnungszeiten FROM Station JOIN Testtyp ON Testtyp.id=Station.Testtyp_id ORDER BY Station.id ASC;');
    } elseif($GLOBALS['FLAG_MODE_MAIN'] == 2) {
        $array_station=S_get_multientry($Db,'SELECT Station.id, Station.Ort, Station.Adresse, Station.Firmencode, Impfstoff.id, Impfstoff.Kurzbezeichnung, Station.Oeffnungszeiten FROM Station JOIN Impfstoff ON Impfstoff.id=Station.Impfstoff_id ORDER BY Station.id ASC;');
    } elseif($GLOBALS['FLAG_MODE_MAIN'] == 3) {
        $array_station=S_get_multientry($Db,'SELECT Station.id, Station.Ort, Station.Adresse, Station.Firmencode, 0,0, Station.Oeffnungszeiten FROM Station ORDER BY Station.id ASC;');
    }
    
    foreach($array_station as $i) {
        echo '<tr>
      <td class="FAIR-data-height2 FAIR-data-right FAIR-data-left FAIR-data-bottom FAIR-data-top">S'.$i[0].'</td>
      <td class="FAIR-data-height2 FAIR-data-right FAIR-data-left FAIR-data-bottom FAIR-data-top">'.$i[1].'</td>
      <td class="FAIR-data-height2 FAIR-data-right FAIR-data-left FAIR-data-bottom FAIR-data-top">'.$i[2].'</td>
      <td class="FAIR-data-height2 FAIR-data-right FAIR-data-left FAIR-data-bottom FAIR-data-top">'.$i[6].'</td>
      <td class="FAIR-data-height2 FAIR-data-right FAIR-data-left FAIR-data-bottom FAIR-data-top">T'.$i[4].' / '.$i[5].'</td>
      <td class="FAIR-data-height2 FAIR-data-right FAIR-data-left FAIR-data-bottom FAIR-data-top">'.$i[3].'</td>
      
      </tr>';
    }
    echo '</table>';
    echo '</div></div></div>';

    //
    // Add station
    //
    if(true) {
        
        echo '<div class="card"><div class="row">
        <div class="col-sm-12">
        <h3>Neue Station anlegen</h3>';

        echo'<form action="'.$current_site.'.php" method="post">
        <div class="input-group">
        <span class="input-group-addon" id="basic-addon1">Stations-ID</span>
        <input type="number" class="form-control" placeholder="" aria-describedby="basic-addon1" name="n_station_id" autocomplete="off" required>

        <span class="input-group-addon" id="basic-addon1">Stationsname (Ort)</span>
        <input type="text" class="form-control" placeholder="" aria-describedby="basic-addon1" name="n_username" autocomplete="off" required>
        </div><div class="input-group">
        <span class="input-group-addon" id="basic-addon1">Adresse</span>
        <input type="text" class="form-control" placeholder="" aria-describedby="basic-addon1" name="n_address" autocomplete="off">

        <span class="input-group-addon" id="basic-addon1">Firmencode</span>
        <input type="text" class="form-control" placeholder="" aria-describedby="basic-addon1" name="n_b2b" autocomplete="off">
        </div><div class="input-group">';
        if($GLOBALS['FLAG_MODE_MAIN'] == 1 || $GLOBALS['FLAG_MODE_MAIN'] == 2) {
        echo '<span class="input-group-addon" id="basic-addon1">Testtyp/Impfstoff</span>
            <select id="select-state_typnew" placeholder="Wähle einen Standard-Typ..." class="custom-select" style="margin-top:0px;" name="n_testtyp">
            <option value="" selected>Wähle...</option>
                ';
                foreach($testtyp_array as $i) {
                    $display='T'.$i[0].' / '.$i[1];
                    echo '<option value="'.$i[0].'">'.$display.'</option>';
                }
                echo '
            </select>
        </div>';
        }   
        echo '<div class="FAIR-si-button">
        <input type="submit" class="btn btn-danger" value="Erstellen" name="create_station" />
        </div></form>';
        echo $errorhtml2;
        echo '</div></div></div>';
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

// Close connection to database
S_close_db($Db);
?>