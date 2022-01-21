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
$current_site="material";

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

    if($GLOBALS['FLAG_MODE_MAIN'] == 1) {
        $material = 'Testkits';
    }elseif($GLOBALS['FLAG_MODE_MAIN'] == 2) {
        $material = 'Impfstoff';
    }else{
        $material = '';
    }

    // Show user
    $bool_staff_display=false;
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {

        // Edit user in database
        if(isset($_POST['edit_material'])) {
            $id=($_POST['e_id']);
            $name=($_POST['e_name']);
            $kurzname=($_POST['e_kurzname']);
            $aktiv=($_POST['e_aktiv']);

            if($GLOBALS['FLAG_MODE_MAIN'] == 1) {
            $device_ID=($_POST['e_deviceID']);
            $isPCR=($_POST['e_isPCR']);
            }elseif($GLOBALS['FLAG_MODE_MAIN'] == 2){
            $min_alter=($_POST['e_min_alter']);
            $max_alter=($_POST['e_max_alter']);    
            }


            //  edit station data
            if($GLOBALS['FLAG_MODE_MAIN'] == 1) {
                S_set_data($Db,'UPDATE Testtyp SET Name=\''.$name.'\',Kurzbezeichnung=\''.$kurzname.'\',Device_ID=\''.$device_ID.'\',Aktiv=\''.$aktiv.'\',IsPCR=\''.$isPCR.'\' WHERE id='.$id.';');
            } else {
                S_set_data($Db,'UPDATE Impfstoff SET Name=\''.$name.'\',Kurzbezeichnung=\''.$kurzname.'\',Mindestalter=\''.$min_alter.'\',Maiximalalter=\''.$max_alter.'\',Aktiv=\''.$Aktiv.'\' WHERE id='.$id.';');
            }
            $errorhtml3 =  H_build_boxinfo( 0, 'Änderungen wurden gespeichert.', 'green' );

        } elseif(isset($_POST['create_material'])) {
            $name=($_POST['n_name']);
            $kurzname=($_POST['n_kurzname']);
            $aktiv=($_POST['n_aktiv']);
            if($GLOBALS['FLAG_MODE_MAIN'] == 1) {
                $IsPCR=($_POST['n_isPCR']);
                $Device_ID=($_POST['n_deviceID']);
                }elseif($GLOBALS['FLAG_MODE_MAIN'] == 2){
                $min_alter=($_POST['n_min_alter']);
                $max_alter=($_POST['n_max_alter']);
                }

                
                if($GLOBALS['FLAG_MODE_MAIN'] == 1) {
                    S_set_data($Db,'INSERT INTO Testtyp (Name,Kurzbezeichnung,Device_ID,Aktiv,IsPCR) VALUES (
                        \''.$name.'\',
                        \''.$kurzname.'\',
                        \''.$Device_ID.'\',
                        \''.$aktiv.'\',
                        '.$IsPCR.');');
                } elseif($GLOBALS['FLAG_MODE_MAIN'] == 2) {
                    S_set_data($Db,'INSERT INTO Impfstoff (Name,Kurzbezeichnung,Mindestalter,Maximalalter,Aktiv) VALUES (
                        \''.$name.'\',
                        \''.$kurzname.'\',
                        \''.$min_alter.'\',
                        \''.$max_alter.'\',
                        '.$aktiv.');');
                } 
        }

        // Search on number
        if( isset($_POST['search_material'])  ) {
            $material_id=($_POST['material_id']);

            if($GLOBALS['FLAG_MODE_MAIN'] == 1) {
            $bool_staff_display=true;
            $u_name=S_get_entry($Db,'SELECT Name FROM Testtyp WHERE id=CAST('.$material_id.' AS int);');
            $u_kurzname=S_get_entry($Db,'SELECT Kurzbezeichnung FROM Testtyp WHERE id=CAST('.$material_id.' AS int);');
            $u_aktiv=S_get_entry($Db,'SELECT Aktiv FROM Testtyp WHERE id=CAST('.$material_id.' AS int);');
            $u_device_ID=S_get_entry($Db,'SELECT Device_ID FROM Testtyp WHERE id=CAST('.$material_id.' AS int);');
            $u_isPCR=S_get_entry($Db,'SELECT IsPCR FROM Testtyp WHERE id=CAST('.$material_id.' AS int);');
            } elseif($GLOBALS['FLAG_MODE_MAIN'] == 2){
            $bool_staff_display=true;
            $u_name=S_get_entry($Db,'SELECT Name FROM Impfstoff WHERE id=CAST('.$material_id.' AS int);');
            $u_kurzname=S_get_entry($Db,'SELECT Kurzbezeichnung FROM Impfstoff WHERE id=CAST('.$material_id.' AS int);');
            $u_aktiv=S_get_entry($Db,'SELECT Aktiv FROM Impfstoff WHERE id=CAST('.$material_id.' AS int);');
            $u_min_alter=S_get_entry($Db,'SELECT Mindestalter FROM Impfstoff WHERE id=CAST('.$material_id.' AS int);');
            $u_max_alter=S_get_entry($Db,'SELECT Maximalalter FROM Impfstoff WHERE id=CAST('.$material_id.' AS int);');
            }
            
        }

    }

    // Get  details

    if($GLOBALS['FLAG_MODE_MAIN'] == 1) {
        $material_array=S_get_multientry($Db,'SELECT id, Kurzbezeichnung FROM Testtyp;');
    } elseif($GLOBALS['FLAG_MODE_MAIN'] == 2) {
        $material_array=S_get_multientry($Db,'SELECT id, Kurzbezeichnung FROM Impfstoff;');
    }

    // pre settings for DataTables
    $local_dt_language="'language': {
        'info': 'Zeige _START_ bis _END_ von _TOTAL_ Einträgen',
        'infoFiltered': '(gefiltert aus _MAX_ Einträgen)',
        'search': 'Suchen:',
        select: {
            rows: {
                _: '%d Zeilen ausgewählt',
                0: '',
                1: '1 Zeile ausgewählt'
            }
        }
    }";

    // Print html header
    echo $GLOBALS['G_html_header_start'];
    echo '

    <link rel="stylesheet" type="text/css" href="lib/datatables/datatables.min.css"/>
    <script type="text/javascript" src="lib/datatables/datatables.min.js"></script>
    ';
    echo '
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/selectize.js/0.12.6/js/standalone/selectize.min.js" integrity="sha256-+C0A5Ilqmu4QcSPxrlGpaZxJ04VjsRjKu+G82kl5UJk=" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/selectize.js/0.12.6/css/selectize.bootstrap3.min.css" integrity="sha256-ze/OEYGcFbPRmvCnrSeKbRTtjG4vGLHXgOqsyLFTRjg=" crossorigin="anonymous" />';
    echo $GLOBALS['G_html_header_end'];

    // Print html menu
    echo $GLOBALS['G_html_menu'];
    echo $GLOBALS['G_html_menu2'];

    // Print html content part A
    echo $GLOBALS['G_html_main_right_a'];

    echo '<h1>Admin: Material-Management</h1>';
    //
    // Select user
    //
    

    echo "<script>
    $(document).ready(function () {
        $('#select-state').selectize({
            sortField: 'text'
        });
    });
    </script>";


    echo '<div class="card"><div class="row">
    <div class="col-md-4">
    <h3>Material wählen</h3>';

    echo'<form action="'.$current_site.'.php" method="post">
    <div class="input-group">
    <span class="input-group-addon" id="basic-addon1">Nr.</span>
    <select id="select-state" placeholder="Wähle..." name="material_id">
    <option value="" selected>Wähle...</option>
        ';
        foreach($material_array as $i) {
            $display='S'.sprintf('%02d',$i[0]).' '.$i[1].' ('.$i[2].')';
            echo '<option value="'.$i[0].'">'.$display.'</option>';
        }
        echo '
    </select>
    </div>
    <div class="FAIR-si-button">
    <input type="submit" class="btn btn-danger" value="Anzeigen" name="search_material" />
    </div></form>';
    echo $errorhtml4;
    echo $errorhtml3;
    echo $errorhtml1;
    echo '</div>';

    if($bool_staff_display) {

        // Show material data
        echo '<div class="col-sm-8">
        <h3>'.$user_id.'</h3>
        <p>'.$u_display.'</p>';

        if($GLOBALS['FLAG_MODE_MAIN'] == 1){
            
    
            echo'<form action="'.$current_site.'.php" method="post">
            <div class="input-group">
            <span class="input-group-addon" id="basic-addon1">Name</span>
            <input type="text" class="form-control" placeholder="" aria-describedby="basic-addon1" name="n_name" autocomplete="off" value="'.$u_name.'">
    
            <span class="input-group-addon" id="basic-addon1">Kurzbezeichnung</span>
            <input type="text" class="form-control" placeholder="" aria-describedby="basic-addon1" name="n_kurzname" autocomplete="off" value="'.$u_kurzname.'">
            </div><div class="input-group">
            <div class="input-group">
            <span class="input-group-addon" id="basic-addon1">Device_ID</span>
            <input type="number" class="form-control" placeholder="" aria-describedby="basic-addon1" name="n_device_ID" autocomplete="off" value="'.$u_device_ID.'">
            <span class="input-group-addon" id="basic-addon1">PCR Test</span>
            <input type="checkbox" class="form-control" placeholder="" aria-describedby="basic-addon1" name="n_isPCR" autocomplete=" value="'.$u_isPCR.'"">
            <span class="input-group-addon" id="basic-addon1">Aktiv</span>
            <input type="checkbox" class="form-control" placeholder="" aria-describedby="basic-addon1" name="n_aktiv" autocomplete=" value="'.$u_aktiv.'"">
            </div><div class="input-group">';
    
        } elseif ($GLOBALS['FLAG_MODE_MAIN'] == 2){

            echo'<form action="'.$current_site.'.php" method="post">
            <div class="input-group">
            <span class="input-group-addon" id="basic-addon1">Name</span>
            <input type="text" class="form-control" placeholder="" aria-describedby="basic-addon1" name="n_name" autocomplete="off" value="'.$u_name.'">
    
            <span class="input-group-addon" id="basic-addon1">Kurzbezeichnung</span>
            <input type="text" class="form-control" placeholder="" aria-describedby="basic-addon1" name="n_kurzname" autocomplete="off" value="'.$u_kurzname.'">
            </div><div class="input-group">
            <div class="input-group">
            <span class="input-group-addon" id="basic-addon1">Mindestalter</span>
            <input type="number" class="form-control" placeholder="" aria-describedby="basic-addon1" name="n_device_ID" autocomplete="off" value="'.$u_min_alter.'">
            <span class="input-group-addon" id="basic-addon1">Maximalalter</span>
            <input type="number" class="form-control" placeholder="" aria-describedby="basic-addon1" name="n_isPCR" autocomplete=" value="'.$u_max_alter.'"">
            <span class="input-group-addon" id="basic-addon1">Aktiv</span>
            <input type="checkbox" class="form-control" placeholder="" aria-describedby="basic-addon1" name="n_aktiv" autocomplete=" value="'.$u_aktiv.'"">
            </div><div class="input-group">';
            }


        echo '<div class="FAIR-si-button">
        <input type="submit" class="btn btn-danger" value="Änderung speichern" name="edit_material" />
        </div></form>';

        echo '</div>';
    }

    echo '</div></div>';


    // Show Material list
    echo '<div class="card"><div class="row">
    <div class="col-sm-12">
    <h3>Material</h3>';

    echo '<table class="FAIR-data" id="maintable" data-order=\'[[ 0, "asc" ]]\' data-page-length=\'1000\'>
    <thead>
      <tr>
      <td class="FAIR-data-height2 FAIR-data-right FAIR-data-left FAIR-data-bottom FAIR-data-top"><h4>Nr.</h4></td>
      <td class="FAIR-data-height2 FAIR-data-right FAIR-data-left FAIR-data-bottom FAIR-data-top"><h4>Name</h4></td>
      <td class="FAIR-data-height2 FAIR-data-right FAIR-data-left FAIR-data-bottom FAIR-data-top"><h4>Kurzbezeichnung</h4></td>
      <td class="FAIR-data-height2 FAIR-data-right FAIR-data-left FAIR-data-bottom FAIR-data-top"><h4>Aktiv</h4></td>';
      if($GLOBALS['FLAG_MODE_MAIN'] == 1) {
        echo '<td class="FAIR-data-height2 FAIR-data-right FAIR-data-left FAIR-data-bottom FAIR-data-top"><h4>Device_ID</h4></td>
        <td class="FAIR-data-height2 FAIR-data-right FAIR-data-left FAIR-data-bottom FAIR-data-top"><h4>IsPCR</h4></td>';
      }elseif($GLOBALS['FLAG_MODE_MAIN'] == 2){
        echo '<td class="FAIR-data-height2 FAIR-data-right FAIR-data-left FAIR-data-bottom FAIR-data-top"><h4>Mindestalter</h4></td>
            <td class="FAIR-data-height2 FAIR-data-right FAIR-data-left FAIR-data-bottom FAIR-data-top"><h4>Maximalalter</h4></td>';
      }
      echo '
      </tr>
      </thead>
      <tbody>';
    if($GLOBALS['FLAG_MODE_MAIN'] == 1) {
        $array_station=S_get_multientry($Db,'SELECT id, Name, Kurzbezeichnung, Device_ID, Aktiv, IsPCR FROM Testtyp ORDER BY id ASC;');
    } elseif($GLOBALS['FLAG_MODE_MAIN'] == 2) {
        $array_station=S_get_multientry($Db,'SELECT id, Name, Kurzbezeichnung, Mindestalter, Maximalalter, Aktiv FROM Impfstoff ORDER BY id ASC;');
    }
    
    foreach($array_station as $i) {
        echo '<tr>
      <td class="FAIR-data-height2 FAIR-data-right FAIR-data-left FAIR-data-bottom FAIR-data-top">'.(sprintf("%02d", $i[0])).'</td>
      <td class="FAIR-data-height2 FAIR-data-right FAIR-data-left FAIR-data-bottom FAIR-data-top">'.$i[1].'</td>
      <td class="FAIR-data-height2 FAIR-data-right FAIR-data-left FAIR-data-bottom FAIR-data-top">'.$i[2].'</td>
      <td class="FAIR-data-height2 FAIR-data-right FAIR-data-left FAIR-data-bottom FAIR-data-top">'.$i[3].'</td>
      <td class="FAIR-data-height2 FAIR-data-right FAIR-data-left FAIR-data-bottom FAIR-data-top">'.$i[4].'</td>
      <td class="FAIR-data-height2 FAIR-data-right FAIR-data-left FAIR-data-bottom FAIR-data-top">'.$i[5].'</td>';
      echo '
      </tr>';
    }
    echo '</tbody></table>';
    echo '</div></div></div>';

    // Initialize DataTables JavaScript
    echo "
    <script>
    $(document).ready( function () {
        $('#maintable').DataTable( {
            dom: \"frti\",
            $local_dt_language
        });
    } );
    </script>
    ";

    //
    // Add Material
    //
    if(true) {
        
        if($GLOBALS['FLAG_MODE_MAIN'] == 1){
        echo '<div class="card"><div class="row">
        <div class="col-sm-12">
        <h3>Neuen Testtyp anlegen</h3>';

        echo'<form action="'.$current_site.'.php" method="post">
        <div class="input-group">
        <span class="input-group-addon" id="basic-addon1">Name</span>
        <input type="text" class="form-control" placeholder="" aria-describedby="basic-addon1" name="n_name" autocomplete="off" required>

        <span class="input-group-addon" id="basic-addon1">Kurzbezeichnung</span>
        <input type="text" class="form-control" placeholder="" aria-describedby="basic-addon1" name="n_kurzname" autocomplete="off" required>
        </div><div class="input-group">
        <div class="input-group">
        <span class="input-group-addon" id="basic-addon1">Device_ID</span>
        <input type="number" class="form-control" placeholder="" aria-describedby="basic-addon1" name="n_device_ID" autocomplete="off">
        <span class="input-group-addon" id="basic-addon1">PCR Test</span>
        <input type="checkbox" class="form-control" placeholder="" aria-describedby="basic-addon1" name="n_isPCR" autocomplete="off">
        <span class="input-group-addon" id="basic-addon1">Aktiv</span>
        <input type="checkbox" class="form-control" placeholder="" aria-describedby="basic-addon1" name="n_aktiv" autocomplete="off">
        </div><div class="input-group">';

        } elseif ($GLOBALS['FLAG_MODE_MAIN'] == 2){

        echo '<div class="card"><div class="row">
        <div class="col-sm-12">
        <h3>Neuen Impfstoff anlegen</h3>';

        echo'<form action="'.$current_site.'.php" method="post">
        <div class="input-group">
        <span class="input-group-addon" id="basic-addon1">Stations-ID</span>
        <input type="text" class="form-control" placeholder="" aria-describedby="basic-addon1" name="n_station_id" autocomplete="off" required>

        <span class="input-group-addon" id="basic-addon1">Stationsname (Ort)</span>
        <input type="text" class="form-control" placeholder="" aria-describedby="basic-addon1" name="n_username" autocomplete="off" required>
        </div><div class="input-group">
        <span class="input-group-addon" id="basic-addon1">Adresse</span>
        <input type="bool" class="form-control" placeholder="" aria-describedby="basic-addon1" name="n_address" autocomplete="off">

        <span class="input-group-addon" id="basic-addon1">Firmencode</span>
        <input type="checkbox" class="form-control" placeholder="" aria-describedby="basic-addon1" name="n_b2b" autocomplete="off">
        </div><div class="input-group">';
 
        }


     
        echo '<div class="FAIR-si-button">
        <input type="submit" class="btn btn-danger" value="Erstellen" name="create_material" />
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