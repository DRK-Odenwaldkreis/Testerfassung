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
    } elseif($GLOBALS['FLAG_MODE_MAIN'] == 2) {
        $material = 'Impfstoff';
    } else {
        $material = '';
    }

    // Show entry
    $bool_staff_display=false;
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {

        // Edit entry in database
        if(isset($_POST['edit_material'])) {
            $id=($_POST['e_id']);
            $name=A_sanitize_input_light($_POST['e_name']);
            $kurzname=A_sanitize_input_light($_POST['e_kurzname']);
            $aktiv=A_sanitize_input($_POST['e_aktiv']);
            if($aktiv=='on') {$aktiv_val=1;} else {$aktiv_val=0;}
            if($GLOBALS['FLAG_MODE_MAIN'] == 1) {
                $device_ID=A_sanitize_input($_POST['e_device_ID']);
                $isPCR=A_sanitize_input($_POST['e_isPCR']);
                if($isPCR=='on') {$isPCR_val=1;} else {$isPCR_val=0;}
            } elseif($GLOBALS['FLAG_MODE_MAIN'] == 2){
                $min_alter=A_sanitize_input($_POST['e_min_alter']);
                $max_alter=A_sanitize_input($_POST['e_max_alter']);
            }


            //  edit entry data
            if($GLOBALS['FLAG_MODE_MAIN'] == 1) {
                S_set_data($Db,'UPDATE Testtyp SET Name=\''.$name.'\',Kurzbezeichnung=\''.$kurzname.'\',Device_ID=\''.$device_ID.'\',Aktiv=\''.$aktiv_val.'\',IsPCR=\''.$isPCR_val.'\' WHERE id='.$id.';');
            } else {
                S_set_data($Db,'UPDATE Impfstoff SET Name=\''.$name.'\',Kurzbezeichnung=\''.$kurzname.'\',Mindestalter=\''.$min_alter.'\',Maiximalalter=\''.$max_alter.'\',Aktiv=\''.$aktiv_val.'\' WHERE id='.$id.';');
            }
            $errorhtml3 =  H_build_boxinfo( 0, 'Änderungen wurden gespeichert.', 'green' );

        } elseif(isset($_POST['create_material'])) {
            $name=A_sanitize_input_light($_POST['n_name']);
            $kurzname=A_sanitize_input_light($_POST['n_kurzname']);
            $aktiv=A_sanitize_input($_POST['n_aktiv']);
            if($aktiv=='on') {$aktiv_val=1;} else {$aktiv_val=0;}
            if($GLOBALS['FLAG_MODE_MAIN'] == 1) {
                $IsPCR=A_sanitize_input($_POST['n_isPCR']);
                if($isPCR=='on') {$isPCR_val=1;} else {$isPCR_val=0;}
                $Device_ID=A_sanitize_input($_POST['n_deviceID']);
            } elseif($GLOBALS['FLAG_MODE_MAIN'] == 2){
                $min_alter=A_sanitize_input($_POST['n_min_alter']);
                $max_alter=A_sanitize_input($_POST['n_max_alter']);
            }

                
            if($GLOBALS['FLAG_MODE_MAIN'] == 1) {
                S_set_data($Db,'INSERT INTO Testtyp (Name,Kurzbezeichnung,Device_ID,Aktiv,IsPCR) VALUES (
                    \''.$name.'\',
                    \''.$kurzname.'\',
                    \''.$Device_ID.'\',
                    \''.$aktiv_val.'\',
                    '.$IsPCR_val.');');
            } elseif($GLOBALS['FLAG_MODE_MAIN'] == 2) {
                S_set_data($Db,'INSERT INTO Impfstoff (Name,Kurzbezeichnung,Mindestalter,Maximalalter,Aktiv) VALUES (
                    \''.$name.'\',
                    \''.$kurzname.'\',
                    \''.$min_alter.'\',
                    \''.$max_alter.'\',
                    '.$aktiv_val.');');
            } 
        }

        // Search on number
        if( isset($_POST['search_material'])  ) {
            $material_id=A_sanitize_input( $_POST['material_id']);

            if($GLOBALS['FLAG_MODE_MAIN'] == 1) {
                $bool_staff_display=true;
                $u_name=S_get_entry($Db,'SELECT Name FROM Testtyp WHERE id=CAST('.$material_id.' AS int);');
                $u_kurzname=S_get_entry($Db,'SELECT Kurzbezeichnung FROM Testtyp WHERE id=CAST('.$material_id.' AS int);');
                $u_aktiv=S_get_entry($Db,'SELECT Aktiv FROM Testtyp WHERE id=CAST('.$material_id.' AS int);');
                $u_device_ID=S_get_entry($Db,'SELECT Device_ID FROM Testtyp WHERE id=CAST('.$material_id.' AS int);');
                $u_isPCR=S_get_entry($Db,'SELECT IsPCR FROM Testtyp WHERE id=CAST('.$material_id.' AS int);');
            } elseif($GLOBALS['FLAG_MODE_MAIN'] == 2) {
                $bool_staff_display=true;
                $u_name=S_get_entry($Db,'SELECT Name FROM Impfstoff WHERE id=CAST('.$material_id.' AS int);');
                $u_kurzname=S_get_entry($Db,'SELECT Kurzbezeichnung FROM Impfstoff WHERE id=CAST('.$material_id.' AS int);');
                $u_aktiv=S_get_entry($Db,'SELECT Aktiv FROM Impfstoff WHERE id=CAST('.$material_id.' AS int);');
                $u_min_alter=S_get_entry($Db,'SELECT Mindestalter FROM Impfstoff WHERE id=CAST('.$material_id.' AS int);');
                $u_max_alter=S_get_entry($Db,'SELECT Maximalalter FROM Impfstoff WHERE id=CAST('.$material_id.' AS int);');
            }
            
        }

    }

    // Get entry details

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
    // Select entry
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
            $display='Mat-Nr. '.sprintf('%02d',$i[0]).' '.$i[1];
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
        <h3>Mat-Nr. '.$material_id.'</h3>';

        if($GLOBALS['FLAG_MODE_MAIN'] == 1){
            
            if($u_isPCR==1) {$u_isPCR_check="checked";} else {$u_isPCR_check="";}
            if($u_aktiv==1) {$u_aktiv_check="checked";} else {$u_aktiv_check="";}
            echo'<form action="'.$current_site.'.php" method="post">
            <div class="input-group">
            <input type="text" name="e_id" style="display:none;" value="'.$material_id.'">
            <span class="input-group-addon" id="basic-addon1">Name</span>
            <input type="text" class="form-control" placeholder="" aria-describedby="basic-addon1" name="e_name" autocomplete="off" value="'.$u_name.'">
    
            <span class="input-group-addon" id="basic-addon1">Kurzbezeichnung</span>
            <input type="text" class="form-control" placeholder="" aria-describedby="basic-addon1" name="e_kurzname" autocomplete="off" value="'.$u_kurzname.'">
            </div><div class="input-group">
            <div class="input-group">
            <span class="input-group-addon" id="basic-addon1">Device_ID</span>
            <input type="number" class="form-control" placeholder="" aria-describedby="basic-addon1" name="e_device_ID" autocomplete="off" value="'.$u_device_ID.'">
            </div>
            
            <div class="FAIRsepdown"></div>
            <div class="cb_drk">
            <input type="checkbox" id="e_isPCR" name="e_isPCR" '.$u_isPCR_check.'><label for="e_isPCR">PCR Test</label>
            </div>
            <div class="FAIRsepdown"></div>
            <div class="cb_drk">
            <input type="checkbox" id="e_aktiv" name="e_aktiv" '.$u_aktiv_check.'><label for="e_aktiv">Aktiv</label>
            </div>
            ';
    
        } elseif ($GLOBALS['FLAG_MODE_MAIN'] == 2){

            if($u_isPCR==1) {$u_isPCR_check="checked";} else {$u_isPCR_check="";}
            if($u_aktiv==1) {$u_aktiv_check="checked";} else {$u_aktiv_check="";}
            echo'<form action="'.$current_site.'.php" method="post">
            <div class="input-group">
            <input type="text" name="e_id" style="display:none;" value="'.$material_id.'">
            <span class="input-group-addon" id="basic-addon1">Name</span>
            <input type="text" class="form-control" placeholder="" aria-describedby="basic-addon1" name="e_name" autocomplete="off" value="'.$u_name.'">
    
            <span class="input-group-addon" id="basic-addon1">Kurzbezeichnung</span>
            <input type="text" class="form-control" placeholder="" aria-describedby="basic-addon1" name="e_kurzname" autocomplete="off" value="'.$u_kurzname.'">
            </div><div class="input-group">
            <div class="input-group">
            <span class="input-group-addon" id="basic-addon1">Mindestalter</span>
            <input type="number" class="form-control" placeholder="" aria-describedby="basic-addon1" name="e_min_alter" autocomplete="off" value="'.$u_min_alter.'">
            <span class="input-group-addon" id="basic-addon1">Maximalalter</span>
            <input type="number" class="form-control" placeholder="" aria-describedby="basic-addon1" name="e_max_alter" autocomplete="off" value="'.$u_max_alter.'">
            </div>
            
            <div class="FAIRsepdown"></div>
            <div class="cb_drk">
            <input type="checkbox" id="e_aktiv" name="e_aktiv" '.$u_aktiv_check.'><label for="e_aktiv">Aktiv</label>
            </div>
            ';
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
      <td class="FAIR-data-height2 FAIR-data-right FAIR-data-left FAIR-data-bottom FAIR-data-top"><h4>Mat-Nr.</h4></td>
      <td class="FAIR-data-height2 FAIR-data-right FAIR-data-left FAIR-data-bottom FAIR-data-top"><h4>Name</h4></td>
      <td class="FAIR-data-height2 FAIR-data-right FAIR-data-left FAIR-data-bottom FAIR-data-top"><h4>Kurzbezeichnung</h4></td>
      <td class="FAIR-data-height2 FAIR-data-right FAIR-data-left FAIR-data-bottom FAIR-data-top"><h4>Aktiv (1=Ja)</h4></td>';
      if($GLOBALS['FLAG_MODE_MAIN'] == 1) {
        echo '<td class="FAIR-data-height2 FAIR-data-right FAIR-data-left FAIR-data-bottom FAIR-data-top"><h4>Device_ID</h4></td>
        <td class="FAIR-data-height2 FAIR-data-right FAIR-data-left FAIR-data-bottom FAIR-data-top"><h4>PCR (1=Ja)</h4></td>';
      } elseif($GLOBALS['FLAG_MODE_MAIN'] == 2){
        echo '<td class="FAIR-data-height2 FAIR-data-right FAIR-data-left FAIR-data-bottom FAIR-data-top"><h4>Mindestalter</h4></td>
            <td class="FAIR-data-height2 FAIR-data-right FAIR-data-left FAIR-data-bottom FAIR-data-top"><h4>Maximalalter</h4></td>';
      }
      echo '
      </tr>
      </thead>
      <tbody>';
    if($GLOBALS['FLAG_MODE_MAIN'] == 1) {
        $array_station=S_get_multientry($Db,'SELECT id, Name, Kurzbezeichnung, Aktiv, Device_ID, IsPCR FROM Testtyp ORDER BY id ASC;');
    } elseif($GLOBALS['FLAG_MODE_MAIN'] == 2) {
        $array_station=S_get_multientry($Db,'SELECT id, Name, Kurzbezeichnung, Aktiv, Mindestalter, Maximalalter FROM Impfstoff ORDER BY id ASC;');
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
            </div>
            <div class="FAIRsepdown"></div>
            <div class="cb_drk">
            <input type="checkbox" id="n_isPCR" name="n_isPCR"><label for="n_isPCR">PCR Test</label>
            </div>
            <div class="FAIRsepdown"></div>
            <div class="cb_drk">
            <input type="checkbox" id="n_aktiv" name="n_aktiv"><label for="n_aktiv">Aktiv</label>
            </div>';

        } elseif ($GLOBALS['FLAG_MODE_MAIN'] == 2){

            echo '<div class="card"><div class="row">
            <div class="col-sm-12">
            <h3>Neuen Impfstoff anlegen</h3>';

            echo'<form action="'.$current_site.'.php" method="post">
            <div class="input-group">
            <span class="input-group-addon" id="basic-addon1">Name</span>
            <input type="text" class="form-control" placeholder="" aria-describedby="basic-addon1" name="n_name" autocomplete="off" required>

            <span class="input-group-addon" id="basic-addon1">Kurzbezeichnung</span>
            <input type="text" class="form-control" placeholder="" aria-describedby="basic-addon1" name="n_kurzname" autocomplete="off" required>
            </div><div class="input-group">
            <div class="input-group">
            <span class="input-group-addon" id="basic-addon1">Mindestalter</span>
            <input type="number" class="form-control" placeholder="" aria-describedby="basic-addon1" name="n_min_alter" autocomplete="off">
            <span class="input-group-addon" id="basic-addon1">Maximalalter</span>
            <input type="number" class="form-control" placeholder="" aria-describedby="basic-addon1" name="n_max_alter" autocomplete="off">
            </div>
            <div class="FAIRsepdown"></div>
            <div class="cb_drk">
            <input type="checkbox" id="n_aktiv" name="n_aktiv"><label for="n_aktiv">Aktiv</label>
            </div>';
 
        }


     
        echo '<div class="FAIR-si-button">
        <input type="submit" class="btn btn-danger" value="Erstellen" name="create_material" />
        </div></form>';
        echo $errorhtml2;
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

// Close connection to database
S_close_db($Db);
?>