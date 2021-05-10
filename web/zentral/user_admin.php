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
$current_site="user_admin";

// Include functions
include_once 'tools.php';
include_once 'auth.php';
include_once 'menu.php';

// role check
if( A_checkpermission(array(0,0,0,4,0)) ) {


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
            $old_username=($_POST['old_username']);
            $email=($_POST['e_email']);
            $username=str_replace(' ','',$_POST['e_username']);
            $station_id=($_POST['e_station_id']);

            // check unique username
            if($old_username==$username || !(S_get_entry($Db,'SELECT id FROM li_user WHERE username=\''.$username.'\';')>0) ) {

                $attempts=$_POST['e_attempts'];
                if(isset($_POST['e_r1'])) { $u_role_1=1;} else {$u_role_1=0;}
                if(isset($_POST['e_r2'])) { $u_role_2=1;} else {$u_role_2=0;}
                if(isset($_POST['e_r3'])) { $u_role_3=1;} else {$u_role_3=0;}
                if(isset($_POST['e_r4'])) { $u_role_4=1;} else {$u_role_4=0;}
                if(isset($_POST['e_r4'])) { $u_role_4=1;} else {$u_role_4=0;}
                if(isset($_POST['e_r5'])) { $u_role_5=1;} else {$u_role_5=0;}

                // write data
                if($email!='') {
                    // update email
                    S_set_data($Db,'UPDATE li_user SET email=\''.$email.'\' WHERE id='.$user_id.';');
                } else {
                    // email field is empty
                    $errorhtml4=  H_build_boxinfo( 0, 'E-Mail-Feld darf nicht leer sein.', 'red' );
                }

                //  edit staff data
                S_set_data($Db,'UPDATE li_user SET username=\''.$username.'\', login_attempts=CAST('.$attempts.' AS int), Station=CAST('.$station_id.' AS int), role_1='.$u_role_1.', role_2='.$u_role_2.', role_3='.$u_role_3.', role_4='.$u_role_4.', role_5='.$u_role_5.'  WHERE id='.$user_id.';');
                $errorhtml3 =  H_build_boxinfo( 0, 'Änderungen wurden gespeichert.', 'green' );
                
            } else {
                // Message username exists already
                $errorhtml3 =  H_build_boxinfo( 0, 'Eingetragener Benutername bereits eingetragen. Es sind keine Dopplungen erlaubt.', 'red' );
            }

        } elseif(isset($_POST['create_user'])) {
            $username_new=str_replace(' ','',$_POST['n_username']);
            $email_new=($_POST['n_email']);
            if (filter_var($email_new, FILTER_VALIDATE_EMAIL)) {
                $new_id=S_get_entry($Db,'SELECT id FROM li_user WHERE username=\''.$username_new.'\';');
                if($username_new=='' || $email_new=='' || $new_id>0) {
                    $errorhtml2 =  H_build_boxinfo( 0, 'Fehler beim Erstellen. Username existiert bereits.', 'red' );
                } else {
                    S_set_data($Db,'INSERT INTO li_user (username, email) VALUES (
                    \''.$username_new.'\',
                    \''.$email_new.'\');');
                    $errorhtml2 =  H_build_boxinfo( 0, 'User wurde erstellt. Bitte Rollen und Station setzen.', 'green' );
                }
            } else {
                $errorhtml2 =  H_build_boxinfo( 0, 'Fehler beim Erstellen. E-Mail ungültig.', 'red' );
            }
        }

        // Search on number
        if( isset($_POST['search_staff']) || isset($_POST['edit_staff']) ) {
            if( isset($_POST['search_staff']) ) {
                $user_id=($_POST['user_id']);
            }
            $bool_staff_display=true;
            $u_name=S_get_entry($Db,'SELECT username FROM li_user WHERE id=CAST('.$user_id.' AS int);');
            $u_email=S_get_entry($Db,'SELECT email FROM li_user WHERE id=CAST('.$user_id.' AS int);');
            $u_station=S_get_entry($Db,'SELECT Station FROM li_user WHERE id=CAST('.$user_id.' AS int);');
            $u_attempts=S_get_entry($Db,'SELECT login_attempts FROM li_user WHERE id=CAST('.$user_id.' AS int);');
            $u_role_1=S_get_entry($Db,'SELECT role_1 FROM li_user WHERE id=CAST('.$user_id.' AS int);');
            $u_role_2=S_get_entry($Db,'SELECT role_2 FROM li_user WHERE id=CAST('.$user_id.' AS int);');
            $u_role_3=S_get_entry($Db,'SELECT role_3 FROM li_user WHERE id=CAST('.$user_id.' AS int);');
            $u_role_4=S_get_entry($Db,'SELECT role_4 FROM li_user WHERE id=CAST('.$user_id.' AS int);');
            $u_role_5=S_get_entry($Db,'SELECT role_5 FROM li_user WHERE id=CAST('.$user_id.' AS int);');
        }

    }

    // Get user details
    $array_staff=S_get_multientry($Db,'SELECT li_user.Id, li_user.username, li_user.email FROM li_user;');
    $stations_array=S_get_multientry($Db,'SELECT id, Ort FROM Station;');

    // Print html header
    echo $GLOBALS['G_html_header'];

    // Print html menu
    echo $GLOBALS['G_html_menu'];
    echo $GLOBALS['G_html_menu2'];

    // Print html content part A
    echo $GLOBALS['G_html_main_right_a'];

    echo '<h1>Admin: User-Management</h1>';
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
    <h3>User wählen</h3>';

    echo'<form action="'.$current_site.'.php" method="post">
    <div class="input-group">
    <span class="input-group-addon" id="basic-addon1">Nr.</span>
    <select id="select-state" placeholder="Wähle eine Person..." name="user_id">
    <option value="" selected>Wähle...</option>
        ';
        foreach($array_staff as $i) {
            $display=$i[1].' ('.$i[2].')';
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
    // TODO: Extra feature list
    // Search for name and/or number

    if($bool_staff_display) {
        // Show data of staff member
        echo '<div class="col-sm-8">
        <h3>User '.$user_id.'</h3>
        <p>'.$u_display.'</p>';
        if($u_role_1==1) {$u_role_1_selected="checked";} else {$u_role_1_selected="";}
        if($u_role_2==1) {$u_role_2_selected="checked";} else {$u_role_2_selected="";}
        if($u_role_3==1) {$u_role_3_selected="checked";} else {$u_role_3_selected="";}
        if($u_role_4==1) {$u_role_4_selected="checked";} else {$u_role_4_selected="";}
        if($u_role_5==1) {$u_role_5_selected="checked";} else {$u_role_5_selected="";}

        echo'<form action="'.$current_site.'.php" method="post">
        <div class="input-group">
        <input type="text" value="'.$user_id.'" name="user_id" style="display:none;">
        <input type="text" value="'.$u_name.'" name="old_username" style="display:none;">
        <span class="input-group-addon" id="basic-addon1">Username</span>
        <input type="text" class="form-control" placeholder="Username" aria-describedby="basic-addon1" name="e_username" autocomplete="off" value="'.$u_name.'">
        <span class="input-group-addon" id="basic-addon1">E-Mail</span>
        <input type="text" class="form-control" placeholder="E-Mail-Adresse" aria-describedby="basic-addon1" name="e_email" autocomplete="off" value="'.$u_email.'">
        <span class="input-group-addon" id="basic-addon1">Login-Versuche</span>
        <input type="text" class="form-control" placeholder="Login-Versuche" aria-describedby="basic-addon2" name="e_attempts" autocomplete="off" value="'.$u_attempts.'">
        </div><div class="input-group">
        <span class="input-group-addon" id="basic-addon1">Station</span>
        <select id="select-state-2" placeholder="Wähle eine Station..." class="custom-select" style="margin-top:0px;" name="e_station_id">
            <option value="" selected>Wähle Station...</option>
                ';
                foreach($stations_array as $i) {
                    $selected='';
                    if($i[0]==$u_station) {
                        $selected='selected';
                    }
                    $display=$i[1].' / S'.$i[0];
                    echo '<option value="'.$i[0].'" '.$selected.'>'.$display.'</option>';
                }
                echo '
            </select>
        
        </div><div class="FAIRsepdown"></div><div class="cb_drk">
        <input type="checkbox" id="r1" name="e_r1" '.$u_role_1_selected.'>
        <label for="r1">Rolle Teststation</label>
        </div><div class="FAIRsepdown"></div><div class="cb_drk">
        <input type="checkbox" id="r2" name="e_r2" '.$u_role_2_selected.'>
        <label for="r2">Rolle Backoffice</label>
        </div><div class="FAIRsepdown"></div><div class="cb_drk">
        <input type="checkbox" id="r3" name="e_r3" '.$u_role_3_selected.'>
        <label for="r3">Rolle Gesundheitsamt</label>
        </div><div class="FAIRsepdown"></div><div class="cb_drk">
        <input type="checkbox" id="r4" name="e_r4" '.$u_role_4_selected.'>
        <label for="r4">Rolle Admin</label>
        </div><div class="FAIRsepdown"></div><div class="cb_drk">
        <input type="checkbox" id="r5" name="e_r5" '.$u_role_5_selected.'>
        <label for="r5">Rolle Gruppenleitung einer Station</label>
        </div>
        <div class="FAIR-si-button">
        <input type="submit" class="btn btn-danger" value="Änderung speichern" name="edit_staff" />
        </div></form>';

        echo '</div>';
    }

    echo '</div></div>';


    // Show qr code for login
    echo '<div class="card"><div class="row">';
    echo '<div class="col-sm-8">
    <h3>QR Code für Login anzeigen und Passwort setzen</h3>';

    echo'<form action="'.$current_site.'.php" method="post">
    <div class="input-group">
    <span class="input-group-addon" id="basic-addon1">Username</span>
    <input type="text" class="form-control" placeholder="Username" aria-describedby="basic-addon1" name="qr_username" value="'.$u_name.'" autocomplete="off">
    <span class="input-group-addon" id="basic-addon1">Passwort</span>
    <input type="text" class="form-control" placeholder="Passwort" aria-describedby="basic-addon1" name="qr_password" autocomplete="off">
    </div>
    <div class="FAIR-si-button">
    <input type="submit" class="btn btn-danger" value="QR anzeigen" name="show_qr" />
    </div></form>';

    echo '</div>';

    // Show QR code and change password
    if(isset($_POST['show_qr'])) {
        $token='/user/'.$_POST['qr_username'].'/password/'.$_POST['qr_password'];
        echo '<div class="col-sm-2">
        <h3>QR Code</h3>';
        echo '<img src="qrcode.php?id='.$token.'" />';
        echo '<p><code>'.$token.'</code></p>';
        echo '</div>';
        echo '</div></div>';

		$username=$_POST['qr_username'];
		$newpassword1 = $_POST['qr_password'];
		$newpasswordhash = password_hash($_POST['qr_password'], PASSWORD_BCRYPT);
		/* Is entered new password okay */
		if (!preg_match("#.*^(?=.{10,64})(?=.*[a-zA-Z])(?=.*[0-9]).*$#", $newpassword1)) {
			$errorhtml1=H_build_boxinfo( 400, '<span class="icon-warning"></span> Passwortstärke nicht ausreichend.<br>Passwort muss aus 10 bis 64 Zeichen bestehen und Ziffern wie auch Buchstaben enthalten.', 'red' );
		} else {
			S_set_data($Db,'UPDATE li_user SET password_hash=\''.$newpasswordhash.'\' WHERE username=\''.$username.'\';');
			$errorhtml1=H_build_boxinfo( 400, 'Neues Passwort wurde übernommen.', 'green' );
		}
        echo $errorhtml1;

    }
    echo '</div></div>';

    //
    // Add user
    //
    if(true) {
        echo '<div class="card"><div class="row">
        <div class="col-sm-12">
        <h3>Neuen User anlegen</h3>';

        echo'<form action="'.$current_site.'.php" method="post">
        <div class="input-group">

        <span class="input-group-addon" id="basic-addon1">Username</span>
        <input type="text" class="form-control" placeholder="" aria-describedby="basic-addon1" name="n_username" autocomplete="off" required>
        </div><div class="input-group">
        <span class="input-group-addon" id="basic-addon1">E-Mail</span>
        <input type="text" class="form-control" placeholder="" aria-describedby="basic-addon1" name="n_email" autocomplete="off" required>

        </div>
        <div class="FAIR-si-button">
        <input type="submit" class="btn btn-danger" value="Erstellen" name="create_user" />
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