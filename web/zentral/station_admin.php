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
        if(isset($_POST['edit_staff'])) {
            $user_id=($_POST['user_id']);
            $username=($_POST['e_username']);
            $address=($_POST['e_address']);
            $b2b_code=($_POST['e_b2b']);

            //  edit station data
            S_set_data($Db,'UPDATE Station SET Ort=\''.$username.'\',Adresse=\''.$address.'\',Firmencode=\''.$b2b_code.'\' WHERE id='.$user_id.';');
            $errorhtml3 =  H_build_boxinfo( 0, 'Änderungen wurden gespeichert.', 'green' );

        }

        // Search on number
        if( isset($_POST['search_staff']) || isset($_POST['edit_staff']) || isset($_POST['create_staff']) ) {
            if( isset($_POST['search_staff']) ) {
                $user_id=($_POST['user_id']);
            }
            $bool_staff_display=true;
            $u_name=S_get_entry($Db,'SELECT Ort FROM Station WHERE id=CAST('.$user_id.' AS int);');
            $u_address=S_get_entry($Db,'SELECT Adresse FROM Station WHERE id=CAST('.$user_id.' AS int);');
            $u_b2b=S_get_entry($Db,'SELECT Firmencode FROM Station WHERE id=CAST('.$user_id.' AS int);');
        }

    }

    // Get user details
    $array_staff=S_get_multientry($Db,'SELECT id, Ort, Adresse, Firmencode FROM Station;');

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
        $('select').selectize({
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
    <input type="submit" class="btn btn-danger" value="Anzeigen" name="search_staff" />
    </div></form>';
    echo $errorhtml4;
    echo $errorhtml3;
    echo $errorhtml1;
    echo '</div>';

    if($bool_staff_display) {
        // Show data of staff member
        echo '<div class="col-sm-8">
        <h3>Station S'.$user_id.'</h3>
        <p>'.$u_display.'</p>';

        echo'<form action="'.$current_site.'.php" method="post">
        <div class="input-group">
        <input type="text" value="'.$user_id.'" name="user_id" style="display:none;">
        <span class="input-group-addon" id="basic-addon1">Stationsmane</span>
        <input type="text" class="form-control" placeholder="Username" aria-describedby="basic-addon1" name="e_username" autocomplete="off" value="'.$u_name.'">
        <span class="input-group-addon" id="basic-addon1">Adresse</span>
        <input type="text" class="form-control" placeholder="Adresse" aria-describedby="basic-addon1" name="e_address" autocomplete="off" value="'.$u_address.'">
        <span class="input-group-addon" id="basic-addon1">Firmencode</span>
        <input type="text" class="form-control" placeholder="Firmencode" aria-describedby="basic-addon1" name="e_b2b" autocomplete="off" value="'.$u_b2b.'">
        </div>
        <div class="FAIR-si-button">
        <input type="submit" class="btn btn-danger" value="Änderung speichern" name="edit_staff" />
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
      <td class="FAIR-data-height2 FAIR-data-right FAIR-data-left FAIR-data-bottom FAIR-data-top"><h4>Firmencode</h4></td>
      </tr>';
    $array_station=S_get_multientry($Db,'SELECT id, Ort, Adresse, Firmencode FROM Station;');
    foreach($array_station as $i) {
        echo '<tr>
      <td class="FAIR-data-height2 FAIR-data-right FAIR-data-left FAIR-data-bottom FAIR-data-top">S'.$i[0].'</td>
      <td class="FAIR-data-height2 FAIR-data-right FAIR-data-left FAIR-data-bottom FAIR-data-top">'.$i[1].'</td>
      <td class="FAIR-data-height2 FAIR-data-right FAIR-data-left FAIR-data-bottom FAIR-data-top">'.$i[2].'</td>
      <td class="FAIR-data-height2 FAIR-data-right FAIR-data-left FAIR-data-bottom FAIR-data-top">'.$i[3].'</td>
      
      </tr>';
    }
    echo '</table>';
    echo '</div></div>';


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