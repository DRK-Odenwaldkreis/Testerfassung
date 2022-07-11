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
$current_site="gruende";

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
        $material = 'Testgründe';
    } else {
        $material = '';
    }

    // Show entry
    $bool_staff_display=false;
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {

        // Edit entry in database
        if(isset($_POST['edit_grund'])) {
            $id=A_sanitize_input($_POST['e_id']);
            $name=A_sanitize_input_light($_POST['e_name']);
            $kurzname=A_sanitize_input_light($_POST['e_kurzname']);
            $price=A_sanitize_input($_POST['e_price']);
            $aktiv=A_sanitize_input($_POST['e_aktiv']);
            $intern=A_sanitize_input($_POST['e_intern']);
            if($aktiv=='on') {$aktiv_val=1;} else {$aktiv_val=0;}
            if($intern=='on') {$intern_val=1;} else {$intern_val=0;}
            if($GLOBALS['FLAG_MODE_MAIN'] == 1) {
                $isPCR=A_sanitize_input($_POST['e_isPCR']);
                if($isPCR=='on') {$isPCR_val=2;} else {$isPCR_val=1;}
            } 


            //  edit entry data
            if($GLOBALS['FLAG_MODE_MAIN'] == 1) {
                S_set_data($Db,'UPDATE Kosten_PCR SET Name=\''.$name.'\',Kurzbezeichnung=\''.$kurzname.'\',price='.$price.',type='.$isPCR_val.',Aktiv='.$aktiv_val.',Internal='.$intern_val.' WHERE id='.$id.';');
            } 
            $errorhtml3 =  H_build_boxinfo( 0, 'Änderungen wurden gespeichert.', 'green' );

        } elseif(isset($_POST['create_grund'])) {
            $name=A_sanitize_input_light($_POST['n_name']);
            $kurzname=A_sanitize_input_light($_POST['n_kurzname']);
            $price=A_sanitize_input($_POST['n_price']);
            $aktiv=A_sanitize_input($_POST['n_aktiv']);
            $intern=A_sanitize_input($_POST['n_intern']);
            if($intern=='on') {$intern_val=1;} else {$intern_val=0;}
            if($aktiv=='on') {$aktiv_val=1;} else {$aktiv_val=0;}
            $isPCR=A_sanitize_input($_POST['n_isPCR']);
            if($isPCR=='on') {$isPCR_val=2;} else {$isPCR_val=1;}

            if($GLOBALS['FLAG_MODE_MAIN'] == 1) {
                S_set_data($Db,'INSERT INTO Kosten_PCR (Name,Kurzbezeichnung,price,type,Aktiv,Internal) VALUES (
                    \''.$name.'\',
                    \''.$kurzname.'\',
                    '.$price.',
                    \''.$isPCR_val.'\',
                    \''.$aktiv_val.'\',
                    '.$intern_val.');');
            } 
        }

        // Search on number
        if( isset($_POST['search_grund'])  ) {
            $grund_id=A_sanitize_input( $_POST['grund_id']);

            if($GLOBALS['FLAG_MODE_MAIN'] == 1) {
                $bool_staff_display=true;
                $u_name=S_get_entry($Db,'SELECT Name FROM Kosten_PCR WHERE id=CAST('.$grund_id.' AS int);');
                $u_kurzname=S_get_entry($Db,'SELECT Kurzbezeichnung FROM Kosten_PCR WHERE id=CAST('.$grund_id.' AS int);');
                $u_aktiv=S_get_entry($Db,'SELECT Aktiv FROM Kosten_PCR WHERE id=CAST('.$grund_id.' AS int);');
                $u_intern=S_get_entry($Db,'SELECT Internal FROM Kosten_PCR WHERE id=CAST('.$grund_id.' AS int);');
                $u_price=S_get_entry($Db,'SELECT price FROM Kosten_PCR WHERE id=CAST('.$grund_id.' AS int);');
                $u_isPCR=S_get_entry($Db,'SELECT type FROM Kosten_PCR WHERE id=CAST('.$grund_id.' AS int);');
            } 
            
        }

    }

    // Get entry details

    if($GLOBALS['FLAG_MODE_MAIN'] == 1) {
        $material_array=S_get_multientry($Db,'SELECT id, Kurzbezeichnung FROM Kosten_PCR;');
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

    echo '<h1>Admin: Grund-Management</h1>';
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
    <h3>Grund wählen</h3>';

    echo'<form action="'.$current_site.'.php" method="post">
    <div class="input-group">
    <span class="input-group-addon" id="basic-addon1">Nr.</span>
    <select id="select-state" placeholder="Wähle..." name="grund_id">
    <option value="" selected>Wähle...</option>
        ';
        foreach($material_array as $i) {
            $display='Grund Nr. '.sprintf('%02d',$i[0]).' '.$i[1];
            echo '<option value="'.$i[0].'">'.$display.'</option>';
        }
        echo '
    </select>
    </div>
    <div class="FAIR-si-button">
    <input type="submit" class="btn btn-danger" value="Anzeigen" name="search_grund" />
    </div></form>';
    echo $errorhtml4;
    echo $errorhtml3;
    echo $errorhtml1;
    echo '</div>';

    if($bool_staff_display) {

        // Show gruende data
        echo '<div class="col-sm-8">
        <h3>Grund Nr. '.$grund_id.'</h3>';

        if($GLOBALS['FLAG_MODE_MAIN'] == 1){
            
            if($u_isPCR==2) {$u_isPCR_check="checked";} else {$u_isPCR_check="";}
            if($u_aktiv==1) {$u_aktiv_check="checked";} else {$u_aktiv_check="";}
            if($u_intern==1) {$u_intern_check="checked";} else {$u_intern_check="";}
            echo'<form action="'.$current_site.'.php" method="post">
            <div class="input-group">
            <input type="text" name="e_id" style="display:none;" value="'.$grund_id.'">
            <span class="input-group-addon" id="basic-addon1">Name</span>
            <input type="text" class="form-control" placeholder="" aria-describedby="basic-addon1" name="e_name" autocomplete="off" value="'.$u_name.'">
    
            <span class="input-group-addon" id="basic-addon1">Kurzbezeichnung</span>
            <input type="text" class="form-control" placeholder="" aria-describedby="basic-addon1" name="e_kurzname" autocomplete="off" value="'.$u_kurzname.'">
            </div><div class="input-group">
            <div class="input-group">
            <span class="input-group-addon" id="basic-addon1">Preis</span>
            <input type="number" step="0.01" min="0" class="form-control" placeholder="" aria-describedby="basic-addon1" name="e_price" autocomplete="off" value="'.$u_price.'">
            </div>
            
            <div class="FAIRsepdown"></div>
            <div class="cb_drk">
            <input type="checkbox" id="e_isPCR" name="e_isPCR" '.$u_isPCR_check.'><label for="e_isPCR">PCR Grund</label>
            </div>
            <div class="FAIRsepdown"></div>
            <div class="cb_drk">
            <input type="checkbox" id="e_aktiv" name="e_aktiv" '.$u_aktiv_check.'><label for="e_aktiv">Aktiv</label>
            </div>
            <div class="FAIRsepdown"></div>
            <div class="cb_drk">
            <input type="checkbox" id="e_intern" name="e_intern" '.$u_intern_check.'><label for="e_intern">Intern</label>
            </div>
            ';
    
        }


        echo '<div class="FAIR-si-button">
        <input type="submit" class="btn btn-danger" value="Änderung speichern" name="edit_grund" />
        </div></form>';

        echo '</div>';
    }

    echo '</div></div>';


    // Show Gruende list
    echo '<div class="card"><div class="row">
    <div class="col-sm-12">
    <h3>Gründe</h3>';

    echo '<table class="FAIR-data" id="maintable" data-order=\'[[ 0, "asc" ]]\' data-page-length=\'1000\'>
    <thead>
      <tr>
      <td class="FAIR-data-height2 FAIR-data-right FAIR-data-left FAIR-data-bottom FAIR-data-top"><h4>Grund Nr.</h4></td>
      <td class="FAIR-data-height2 FAIR-data-right FAIR-data-left FAIR-data-bottom FAIR-data-top"><h4>Name</h4></td>
      <td class="FAIR-data-height2 FAIR-data-right FAIR-data-left FAIR-data-bottom FAIR-data-top"><h4>Kurzbezeichnung</h4></td>';
      if($GLOBALS['FLAG_MODE_MAIN'] == 1) {
        echo '<td class="FAIR-data-height2 FAIR-data-right FAIR-data-left FAIR-data-bottom FAIR-data-top"><h4>Preis</h4></td>
        <td class="FAIR-data-height2 FAIR-data-right FAIR-data-left FAIR-data-bottom FAIR-data-top"><h4>PCR-Grund (2=Ja)</h4></td>
        <td class="FAIR-data-height2 FAIR-data-right FAIR-data-left FAIR-data-bottom FAIR-data-top"><h4>Aktiv (1=Ja)</h4></td>
        <td class="FAIR-data-height2 FAIR-data-right FAIR-data-left FAIR-data-bottom FAIR-data-top"><h4>Internal (1=Ja)</h4></td>';
      }
      echo '
      </tr>
      </thead>
      <tbody>';
    if($GLOBALS['FLAG_MODE_MAIN'] == 1) {
        $array_station=S_get_multientry($Db,'SELECT id, Name, Kurzbezeichnung, price,type, Aktiv, Internal FROM Kosten_PCR ORDER BY id ASC;');
    } 
    
    foreach($array_station as $i) {
        echo '<tr>
      <td class="FAIR-data-height2 FAIR-data-right FAIR-data-left FAIR-data-bottom FAIR-data-top">'.(sprintf("%02d", $i[0])).'</td>
      <td class="FAIR-data-height2 FAIR-data-right FAIR-data-left FAIR-data-bottom FAIR-data-top">'.$i[1].'</td>
      <td class="FAIR-data-height2 FAIR-data-right FAIR-data-left FAIR-data-bottom FAIR-data-top">'.$i[2].'</td>
      <td class="FAIR-data-height2 FAIR-data-right FAIR-data-left FAIR-data-bottom FAIR-data-top">'.$i[3].'</td>
      <td class="FAIR-data-height2 FAIR-data-right FAIR-data-left FAIR-data-bottom FAIR-data-top">'.$i[4].'</td>
      <td class="FAIR-data-height2 FAIR-data-right FAIR-data-left FAIR-data-bottom FAIR-data-top">'.$i[5].'</td>
      <td class="FAIR-data-height2 FAIR-data-right FAIR-data-left FAIR-data-bottom FAIR-data-top">'.$i[6].'</td>';
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
    // Add Grund
    //
    if(true) {
        
        if($GLOBALS['FLAG_MODE_MAIN'] == 1){
            echo '<div class="card"><div class="row">
            <div class="col-sm-12">
            <h3>Neuen Grund anlegen</h3>';

            echo'<form action="'.$current_site.'.php" method="post">
            <div class="input-group">
            <span class="input-group-addon" id="basic-addon1">Name</span>
            <input type="text" class="form-control" placeholder="" aria-describedby="basic-addon1" name="n_name" autocomplete="off" required>

            <span class="input-group-addon" id="basic-addon1">Kurzbezeichnung</span>
            <input type="text" class="form-control" placeholder="" aria-describedby="basic-addon1" name="n_kurzname" autocomplete="off" required>
            </div><div class="input-group">
            <div class="input-group">
            <span class="input-group-addon" id="basic-addon1">Preis</span>
            <input type="number" step="0.01" min="0" class="form-control" placeholder="" aria-describedby="basic-addon1" name="n_price" autocomplete="off">
            </div>
            <div class="FAIRsepdown"></div>
            <div class="cb_drk">
            <input type="checkbox" id="n_isPCR" name="n_isPCR"><label for="n_isPCR">PCR Grund</label>
            </div>
            <div class="FAIRsepdown"></div>
            <div class="cb_drk">
            <input type="checkbox" id="n_aktiv" name="n_aktiv"><label for="n_aktiv">Aktiv</label>
            </div>
            <div class="FAIRsepdown"></div>
            <div class="cb_drk">
            <input type="checkbox" id="n_internal" name="n_internal"><label for="n_internal">Intern</label>
            </div>
            ';

        } 

     
        echo '<div class="FAIR-si-button">
        <input type="submit" class="btn btn-danger" value="Erstellen" name="create_grund" />
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