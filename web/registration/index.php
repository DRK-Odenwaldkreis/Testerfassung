<?php

/* **************

Websystem für das Testzentrum DRK Odenwaldkreis
Author: Marc S. Duchene
March 2021

** ************** */

include_once 'preload.php';

// Include functions
include_once 'tools.php';
include_once 'auth.php';
include_once 'menu.php';


// Print html header
echo $GLOBALS['G_html_header'];

// Print html menu
echo $GLOBALS['G_html_menu'];
echo $GLOBALS['G_html_menu2'];

// Print html content part A
echo $GLOBALS['G_html_main_right_a'];



echo '<div class="row">';
echo '<div class="col-sm-12">
<h2>Voranmeldung für einen Covid-19 Test</h2>';



if(isset($_POST['submit_person'])) {
    // ///////////////
    // Registrierung speichern
    // ///////////////

    // save data
    $k_nname=$_POST['nname'];
    $k_vname=$_POST['vname'];
    $k_geb=$_POST['geburtsdatum'];
    $k_adresse=$_POST['adresse'];
    $k_telefon=$_POST['telefon'];
    $k_email=$_POST['email'];
    $k_slot_id=$_POST['slot_id'];
    $prereg_id=S_set_entry_voranmeldung($Db,array($k_vname,$k_nname,$k_geb,$k_adresse,$k_telefon,$k_email,$k_slot_id));
    // Generate verification via email
    $token_ver=A_generate_token(16);
    S_set_data($Db,'INSERT INTO Voranmeldung_Verif (Token,id_preregistration) VALUES (\''.$token_ver.'\','.$prereg_id.');');
    // Send email for verification
    $header = "From: info@testzentrum-odenwald.de\r\n";
    $header .= "Content-Type: text/plain; charset=UTF-8\nContent-Transfer-Encoding: 8bit";
    $content="Sehr geehrte Dame, sehr geehrter Herr,\n
Sie wurden für einen Termin im DRK Testzentrum eingetragen. Falls diese Anfrage von Ihnen nicht initiiert wurde, können Sie diese Nachricht ignorieren.\n
Bitte mit diesem Link den Termin bestätigen:\n";
    $content.=$FLAG_http.'://'.$hostname.($path == '/' ? '' : $path)."/index.php?confirm=confirm&t=$token_ver&i=$prereg_id";
    $content.="\n\n
Mit freundlichen Grüßen\n
Das Team vom DRK Testzentrum Odenwaldkreis";
    $title='DRK Covid-19 Testzentrum Odenwaldkreis - Termin bestätigen';
    $res=mail($k_email, $title, $content, $header, "-r info@testzentrum-odenwald.de");

    echo '<div class="alert alert-success" role="alert">
    <h3>Ihre Daten wurden gespeichert</h3>
    <p>Sie erhalten jetzt eine E-Mail, die Sie bestätigen müssen. Hierfür haben Sie 20 Minuten Zeit, andernfalls wird Ihr Termin wieder freigegeben und Ihre Daten gelöscht.</p>
    </div>';

    echo '<div class="alert alert-info" role="alert">
    <h3>Ablauf</h3>
    <p>Bitte wählen Sie einen freien Termin für jede Person, die getestet werden soll.</p>
    <p>Bitte tragen Sie Ihre Daten ein. Sie erhalten anschließend eine E-Mail, die Sie bestätigen müssen.</p>
    <p>Nach Abschluss des Registrierungsprozesses erhalten Sie auf Ihre E-Mail-Adresse einen QR-Code, den Sie bei dem Testzentrum vorzeigen müssen (gedruckt oder auf dem Display). Bitte halten Sie im Testzentrum auch einen Lichtbildausweis bereit.</p>
    <p>Das Ergbebnis Ihres Tests wird Ihnen nach dem Abstrich per E-Mail zugeschickt.</p>
    </div>';

} elseif(isset($_GET['confirm'])) {
    // ///////////////
    // Registrierung abschließen
    // ///////////////

    $prereg_id=$_GET['i'];
    $token_ver=$_GET['t'];
    $id_check=S_get_entry_voranmeldung($Db,array($prereg_id,$token_ver));

    if($id_check>0) {
        // Generate unique token for QR code
        $token=A_generate_token(8);
        while( (S_get_entry($Db,'SELECT id FROM Voranmeldung WHERE Token=\''.$token.'\'')>0) ) {
            $token=A_generate_token(8);
        }
        S_set_data($Db,'UPDATE Voranmeldung SET Token=\''.$token.'\' WHERE id=CAST('.$id_check.' AS int)');
        // Send email
        // TODO HTML Mail and include QR Code
        $header = "From: info@testzentrum-odenwald.de\r\n";
        $header .= "Content-Type: text/plain; charset=UTF-8\nContent-Transfer-Encoding: 8bit";
        $content="Ihre Anmeldung zum Covid-19 Test:\n
    \n
    Sollten Sie Ihren Termin nicht wahrnehmen können, so stornieren Sie diesen bitte. Benutzen Sie dazu diesen Link:\n";
        $content.=$FLAG_http.'://'.$hostname.($path == '/' ? '' : $path)."/index.php?cancel=cancel&t=$token&i=$id_check";
        $content.="\n\n
    Mit freundlichen Grüßen\n
    Das Team vom DRK Testzentrum Odenwaldkreis";
        $title='DRK Covid-19 Testzentrum Odenwaldkreis - QR Code';
        $res=mail($k_email, $title, $content, $header, "-r info@testzentrum-odenwald.de");

        echo '<div class="alert alert-success" role="alert">
        <h3>Ihr Termin wurde bestätigt</h3>
        <p>Sie erhalten jetzt eine E-Mail mit den Termindaten und einem QR-Code.</p>
        </div>';
    } else {
        echo '<div class="alert alert-success" role="alert">
        <h3>Ungültiger Code</h3>
        <p>Der Link ist bereits abgelaufen. Sie müssen sich neu registrieren und einen neuen Termin auswählen.</p>
        </div>';
        echo '<div class="list-group">';
        echo '<a class="list-group-item list-group-item-action list-group-item-FAIR" href="'.$current_site.'.php">Neue Registrierung starten</a>';
        echo '</div>';
    }
    echo '<div class="alert alert-info" role="alert">
    <h3>Ablauf</h3>
    <p>Bitte wählen Sie einen freien Termin für jede Person, die getestet werden soll.</p>
    <p>Bitte tragen Sie Ihre Daten ein. Sie erhalten anschließend eine E-Mail, die Sie bestätigen müssen.</p>
    <p>Nach Abschluss des Registrierungsprozesses erhalten Sie auf Ihre E-Mail-Adresse einen QR-Code, den Sie bei dem Testzentrum vorzeigen müssen (gedruckt oder auf dem Display). Bitte halten Sie im Testzentrum auch einen Lichtbildausweis bereit.</p>
    <p>Das Ergbebnis Ihres Tests wird Ihnen nach dem Abstrich per E-Mail zugeschickt.</p>
    </div>';
    
} elseif(isset($_GET['cancel'])) {
    // ///////////////
    // Termin löschen - Frage
    // ///////////////
    
} elseif(isset($_POST['cancel_slot'])) {
    // ///////////////
    // Termin löschen - Bestätigt
    // ///////////////

    echo '<div class="list-group">';
    echo '<a class="list-group-item list-group-item-action list-group-item-FAIR" href="'.$current_site.'.php">Neue Registrierung starten</a>';
    echo '</div>';
    
} else {
    // ///////////////
    // Registrierungsformular
    // ///////////////
    echo '<div class="alert alert-info" role="alert">
    <h3>Ablauf</h3>
    <p>Bitte wählen Sie einen freien Termin für jede Person, die getestet werden soll.</p>
    <p>Bitte tragen Sie Ihre Daten ein. Sie erhalten anschließend eine E-Mail, die Sie bestätigen müssen.</p>
    <p>Nach Abschluss des Registrierungsprozesses erhalten Sie auf Ihre E-Mail-Adresse einen QR-Code, den Sie bei dem Testzentrum vorzeigen müssen (gedruckt oder auf dem Display). Bitte halten Sie im Testzentrum auch einen Lichtbildausweis bereit.</p>
    <p>Das Ergbebnis Ihres Tests wird Ihnen nach dem Abstrich per E-Mail zugeschickt.</p>
    </div>';
// TODO check for valid email address
    echo '<h3>Registrierung</h3>
    <form action="'.$current_site.'.php" method="post">
            <div class="input-group"><span class="input-group-addon" id="basic-addon1">Vorname</span><input type="text" name="vname" class="form-control" placeholder="" aria-describedby="basic-addon1" required></div>
            <div class="input-group"><span class="input-group-addon" id="basic-addon1">Nachname</span><input type="text" name="nname" class="form-control" placeholder="" aria-describedby="basic-addon1" required></div>
            <div class="input-group"><span class="input-group-addon" id="basic-addon1">Geburtsdatum</span><input type="date" name="geburtsdatum" class="form-control" placeholder="" aria-describedby="basic-addon1" required></div>
            <div class="input-group"><span class="input-group-addon" id="basic-addon1">Wohnadresse</span><input type="text" name="adresse" class="form-control" placeholder="" aria-describedby="basic-addon1" required></div>
            <div class="input-group"><span class="input-group-addon" id="basic-addon1">Telefon *</span><input type="text" name="telefon" class="form-control" placeholder="" aria-describedby="basic-addon1"></div>
            <div class="input-group"><span class="input-group-addon" id="basic-addon1">E-Mail</span><input type="text" name="email" class="form-control" placeholder="" aria-describedby="basic-addon1" required></div>
            <span class="input-group-btn">
            <input type="submit" class="btn btn-danger" value="Jetzt Registrieren" name="submit_person" />
            </span>
            </form>
            <p>* optional</p>';
    echo '</div>';
    echo '</div>';
}


// Print html content part C
echo $GLOBALS['G_html_main_right_c'];
// Print html footer
echo $GLOBALS['G_html_footer'];

?>