<?php

/* **************

Websystem für das Testzentrum DRK Odenwaldkreis
Author: Marc S. Duchene
March 2021

** ************** */

include_once 'preload.php';
if( isset($GLOBALS['G_sessionname']) ) { session_name ($GLOBALS['G_sessionname']); }
session_start();

// Include functions
include_once 'tools.php';
include_once 'auth.php';
include_once 'menu.php';

$current_site="index";


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

// Open database connection
$Db=S_open_db();


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
    $k_slot_id=$_POST['termin_id'];
    $k_date=$_POST['date'];
    $prereg_id=S_set_entry_voranmeldung($Db,array($k_vname,$k_nname,$k_geb,$k_adresse,$k_telefon,$k_email,$k_slot_id,$k_date));

    if($prereg_id>0) {
        // Generate verification via email
        $token_ver=A_generate_token(16);
        S_set_data($Db,'INSERT INTO Voranmeldung_Verif (Token,id_preregistration) VALUES (\''.$token_ver.'\','.$prereg_id.');');
        // Send email for verification
        $header = "From: no-reply@testzentrum-odenwald.de\r\n";
        $header .= "Content-Type: text/plain; charset=UTF-8\nContent-Transfer-Encoding: 8bit";
        $content="Guten Tag,\n
    Sie wurden soeben für einen Termin im DRK Testzentrum Odenwaldkreis eingetragen. Falls diese Anfrage von Ihnen nicht initiiert wurde, können Sie diese Nachricht ignorieren.\n
    Bitte mit diesem Link den Termin bestätigen:\n";
        $content.=$FLAG_http.'://'.$hostname.($path == '/' ? '' : $path)."/index.php?confirm=confirm&t=$token_ver&i=$prereg_id";
        $content.="\n\n
    Mit freundlichen Grüßen\n
    Das Team vom DRK Testzentrum Odenwaldkreis";
        $title='DRK Covid-19 Testzentrum Odenwaldkreis - Termin bestätigen';
        $res=mail($k_email, $title, $content, $header, "-r no-reply@testzentrum-odenwald.de");

        echo '<div class="alert alert-success" role="alert">
        <h3>Ihre Daten wurden gespeichert</h3>
        <p>Sie erhalten jetzt eine E-Mail, die Sie bestätigen müssen. Hierfür haben Sie 20 Minuten Zeit, andernfalls wird Ihr Termin wieder freigegeben und Ihre Daten gelöscht.</p>
        </div>';

        echo '<div class="alert alert-info" role="alert">
        <h3>Ablauf</h3>
        <p>Bitte wählen Sie einen freien Termin für jede Person, die getestet werden soll.</p>
        <p>Bitte tragen Sie Ihre Daten ein. Sie erhalten anschließend eine E-Mail, die Sie bestätigen müssen.</p>
        <p>Nach Abschluss des Registrierungsprozesses erhalten Sie auf Ihre E-Mail-Adresse einen QR-Code, den Sie bei dem Testzentrum vorzeigen müssen (gedruckt oder auf dem Display). Bitte halten Sie im Testzentrum auch einen Lichtbildausweis bereit.</p>
        <p>Das Ergebnis Ihres Tests wird Ihnen nach dem Abstrich per E-Mail zugeschickt.</p>
        </div>';
        
    } else {
        echo '<div class="alert alert-danger" role="alert">
        <h3>Termin bereits gebucht</h3>
        <p>Ihr gewählter Termin ist in der Zwischenzeit vergeben worden. Bitte wählen Sie einen neuen Termin aus.</p>
        </div>';
        echo '<div class="list-group">';
        echo '<a class="list-group-item list-group-item-action list-group-item-FAIR" href="../index.php">Neue Registrierung starten</a>';
        echo '</div>';
    }

    

} elseif(isset($_GET['confirm'])) {
    // ///////////////
    // Registrierung abschließen mit E-Mail Code
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
        S_set_data($Db,'UPDATE Voranmeldung SET Token=\'P'.$token.'\' WHERE id=CAST('.$id_check.' AS int)');
        S_set_data($Db,'DELETE From Voranmeldung_Verif WHERE id_preregistration=CAST('.$id_check.' AS int)');
        // Send mail with QR code will be done from different process of server - not from this Web UI

        echo '<div class="alert alert-success" role="alert">
        <h3>Ihr Termin wurde bestätigt</h3>
        <p>Sie erhalten jetzt eine E-Mail mit den Termindaten und einem QR-Code.</p>
        <p>Der Versand dieser E-Mail kann ein paar Minuten in Anspruch nehmen - bitte haben Sie etwas Geduld.</p>
        </div>';
    } else {
        echo '<div class="alert alert-success" role="alert">
        <h3>Ungültiger Code</h3>
        <p>Der Link ist bereits abgelaufen. Sie müssen sich neu registrieren und einen neuen Termin auswählen.</p>
        </div>';
        echo '<div class="list-group">';
        echo '<a class="list-group-item list-group-item-action list-group-item-FAIR" href="../index.php">Neue Registrierung starten</a>';
        echo '</div>';
    }
    
} elseif(isset($_GET['cancel'])) {
    // ///////////////
    // Termin löschen - Frage
    // ///////////////

    // check pre registration data
    $k_prereg_id=$_GET['i'];
    $k_token=$_GET['t'];
    $stmt=mysqli_prepare($Db,"SELECT Termin_id, Nachname, Vorname FROM Voranmeldung WHERE id=? AND Token=? AND Used!=1;");
    mysqli_stmt_bind_param($stmt, "is", $k_prereg_id, $k_token);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $k_termin_id, $k_name, $k_vorname);
    mysqli_stmt_fetch($stmt);
    mysqli_stmt_close($stmt);

    if($k_termin_id>0) {
        // get Termin data
        $array_appointment=S_get_multientry($Db,'SELECT id, Tag, Startzeit, Endzeit, Slot, Stunde FROM Termine WHERE id=CAST('.$k_termin_id.' as int);');
        if($array_appointment[0][0]>0) {
            $date=date("d.m.Y",strtotime($array_appointment[0][1]));
            if($array_appointment[0][4]>0) {
                $time1=sprintf('%02d', $array_appointment[0][5]).':'.sprintf('%02d', ( $array_appointment[0][4]*15-15 ) );
                $time2=(date("H:i",strtotime($time1) + 60 * 15));
            } else {
                $time1=date("H:i",strtotime($array_appointment[2]));
                $time2=date("H:i",strtotime($array_appointment[3]));
            }
            $valid_appointment=true;
        } else {
            $valid_appointment=false;
        }
    } else {
        $valid_appointment=false;
    }

    if($valid_appointment) {
        echo '<div class="panel panel-primary">
        <div class="panel-heading">
        <b>Termin stornieren / Voranmeldung löschen</b>
        </div>
        <div class="panel-body">
        
        <div class="row calendar_selection">
        <div class="col-sm-4"><b>Datum</b> <span class="calendarblue">'.$date.'</span></div>
        <div class="col-sm-4"><b>Uhrzeit</b> <span class="calendarblue">'.$time1.' - '.$time2.' Uhr</span></div>
        <div class="col-sm-4"><b>Name</b> <span class="calendarblue">'.$k_name.', '.$k_vorname.'</span></div>
        </div>

        <p>Sie möchten Ihren Termin stornieren bzw. die Voranmeldung löschen?</p>
        <form action="'.$current_site.'.php" method="post">
        <input type="text" value="'.$k_prereg_id.'" name="prereg_id" style="display:none;">
        <input type="text" value="'.$k_termin_id.'" name="termin_id" style="display:none;">
        <span class="input-group-btn">
        <input type="submit" class="btn btn-danger" value="Jetzt stornieren" name="cancel_slot" />
        </span>
        </form>

        </div></div>';
    } else {
        echo '<div class="alert alert-warning" role="alert">
        <h3>Fehler</h3>
        <p>Ihr Link ist fehlerhaft. Vielleicht wurde der Termin bereits von Ihnen storniert oder der Termin wurde bereits wahrgenommen.</p>
        </div>';

        echo '</div>';
        echo '</div>';
    }


    echo '<div class="list-group">';
    echo '<a class="list-group-item list-group-item-action list-group-item-FAIR" href="../index.php">Zur Startseite</a>';
    echo '</div>';
    
} elseif(isset($_POST['cancel_slot'])) {
    // ///////////////
    // Termin löschen - Bestätigt
    // ///////////////

    // check pre registration data
    $k_prereg_id=$_POST['prereg_id'];
    $k_termin_id=$_POST['termin_id'];
    $stmt=mysqli_prepare($Db,"SELECT id, Termin_id FROM Voranmeldung WHERE id=? AND Termin_id=? AND Used!=1;");
    mysqli_stmt_bind_param($stmt, "ii", $k_prereg_id, $k_termin_id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $k_prereg_id_check, $k_termin_id_check);
    mysqli_stmt_fetch($stmt);
    mysqli_stmt_close($stmt);
    if($k_termin_id_check==$k_termin_id) {
        // Delete data
        S_set_data($Db,'DELETE From Voranmeldung WHERE id=CAST('.$k_prereg_id_check.' AS int)');
        S_set_data($Db,'UPDATE Termine SET Used=Null WHERE id=CAST('.$k_termin_id_check.' AS int)');

        echo '<div class="alert alert-success" role="alert">
        <h3>Termin stornieren / Voranmeldung löschen</h3>
        <p>Ihr Termin wurde storniert und Ihre Voranmeldungsdaten gelöscht. Vielen Dank für Ihre Mithilfe.</p>
        </div>';
    } else {
        echo '<div class="alert alert-warning" role="alert">
        <h3>Fehler</h3>
        <p>Ihr Link ist fehlerhaft. Vielleicht wurde der Termin bereits von Ihnen storniert oder der Termin wurde bereits wahrgenommen.</p>
        </div>';
    }
    

    echo '<div class="list-group">';
    echo '<a class="list-group-item list-group-item-action list-group-item-FAIR" href="../index.php">Neuen Termin auswählen</a>';
    echo '</div>';
    
} elseif( isset($_GET['appointment']) || isset($_GET['appointment_more']) ) {
    $display_single_termin=false;
    $display_slot_termin=false;

    // Termin selected from slot booking
    if( isset($_GET['slot']) ) {
        $display_single_termin=true;
    }

    if( isset($_GET['appointment']) ) {
        $val_termin_id=$_GET['appointment'];

        $stmt=mysqli_prepare($Db,"SELECT id, Tag, Startzeit, Endzeit, Slot, opt_station, opt_station_adresse, id_station, Stunde FROM Termine WHERE id=?;");
        mysqli_stmt_bind_param($stmt, "i", $val_termin_id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_bind_result($stmt, $array_appointment[0], $array_appointment[1], $array_appointment[2], $array_appointment[3], $array_appointment[4], $array_appointment[5], $array_appointment[6], $array_appointment[7], $array_appointment[8]);
        mysqli_stmt_fetch($stmt);
        mysqli_stmt_close($stmt);

        // Check if Termin is b2b
        $b2b_code=S_get_multientry($Db,'SELECT Station.id, Station.Firmencode FROM Station JOIN Termine ON Termine.id_station=Station.id WHERE Termine.id=CAST('.$array_appointment[0].' as int);');
        if($b2b_code[0][1]!='') {
            if(isset($_SESSION) && $_SESSION['b2b_signedin'] && $_SESSION['b2b_id']==$b2b_code[0][0]) {
                $b2b_check=true;
                $b2b_termin=true;
            } else {
                $b2b_check=false;
                $b2b_termin=false;
            }
        } else {
            $b2b_check=true;
            $b2b_termin=false;
        }

        // Slot booking or single Termin
        $date=date("d.m.Y",strtotime($array_appointment[1]));
        $date_sql=date("Y-m-d",strtotime($array_appointment[1]));
        if($array_appointment[4]>0 && !$display_single_termin) {
            $display_slot_termin=true;
            $array_termine_slot=S_get_multientry($Db,'SELECT id,Stunde,Slot,count(id) FROM Termine WHERE Slot>0 AND id_station='.$array_appointment[7].' AND Date(Tag)=\''.$array_appointment[1].'\' AND Used is null GROUP BY Stunde,Slot;');
        } elseif(isset($_GET['slot'])) {
            $time1=sprintf('%02d', $array_appointment[8]).':'.sprintf('%02d', ( $array_appointment[4]*15-15 ) );
            $time2=(date("H:i",strtotime($time1) + 60 * 15));
        } else {
            $display_single_termin=true;
            $time1=date("H:i",strtotime($array_appointment[2]));
            $time2=date("H:i",strtotime($array_appointment[3]));
        }

        // Adresse
        $stations_array=S_get_multientry($Db,'SELECT id, Ort, Adresse FROM Station WHERE id="'.$array_appointment[7].'";');
        if($array_appointment[5]!='') {
            $location=$stations_array[0][1].', '.$array_appointment[5].', '.$array_appointment[6];
        } else {
            $location=$stations_array[0][1].', '.$stations_array[0][2];
        }
    } else {
        $val_station_id=$_GET['appointment_more'];
    }

    if($b2b_check) {
        // ///////////////
        // Registrierungsformular
        // ///////////////
        
        if($b2b_termin) {
            echo '<div class="alert alert-info" role="alert">
            <h3>Ablauf und Information</h3><p>Bitte tragen Sie Ihre Daten ein. Sie erhalten anschließend eine E-Mail, die Sie bestätigen müssen.</p>
            <p>Nach Abschluss des Registrierungsprozesses erhalten Sie auf Ihre E-Mail-Adresse einen QR-Code, den Sie bei dem Test vorzeigen müssen (gedruckt oder auf dem Display). Bitte halten Sie beim Test auch einen Lichtbildausweis oder Mitarbeiterausweis bereit.</p>
            <p>Das Ergebnis Ihres Tests wird Ihnen nach dem Abstrich per E-Mail zugeschickt.</p>
            </div>';
            echo '<div class="alert alert-danger" role="alert">
            <p>Ihr Arbeitgeber hat keinen Zugriff auf Ihre eingegebenen Daten und auch nicht auf Ihr Testergebnis.</p>
            </div>';
        } else {
            echo '<div class="alert alert-info" role="alert">
            <h3>Ablauf</h3>';
            if(!$display_single_termin){
                echo '<p>Bitte wählen Sie einen freien Termin für jede Person, die getestet werden soll.</p>';
            }
            echo '<p>Bitte tragen Sie Ihre Daten ein. Sie erhalten anschließend eine E-Mail, die Sie bestätigen müssen.</p>
            <p>Nach Abschluss des Registrierungsprozesses erhalten Sie auf Ihre E-Mail-Adresse einen QR-Code, den Sie bei dem Testzentrum vorzeigen müssen (gedruckt oder auf dem Display). Bitte halten Sie im Testzentrum auch einen Lichtbildausweis bereit.</p>
            <p>Das Ergebnis Ihres Tests wird Ihnen nach dem Abstrich per E-Mail zugeschickt.</p>
            </div>';
        }
        if($display_single_termin) {
            echo '<div class="panel panel-primary">
            <div class="panel-heading">
            <b>Gewählter Termin</b>
            </div>
            <div class="panel-body">
            <div class="row">
            <div class="col-sm-6"><b>Datum</b> <span class="calendarblue">'.$date.'</span> / <b>Uhrzeit</b> <span class="calendarblue">'.$time1.' - '.$time2.' Uhr</span></div>
            <div class="col-sm-6"><b>Ort</b> <span class="calendarblue">'.$location.'</span></div>
            </div>
            </div>
            </div>';

    // TODO check for valid email address
            echo '<h3>Registrierung</h3>
            <form action="'.$current_site.'.php" method="post">
                <input type="text" value="'.$date_sql.'" name="date" style="display:none;">
                <input type="text" value="'.$val_termin_id.'" name="termin_id" style="display:none;">

                <div class="input-group"><span class="input-group-addon" id="basic-addon1">Vorname</span><input type="text" name="vname" class="form-control" placeholder="" aria-describedby="basic-addon1" required></div>
                <div class="input-group"><span class="input-group-addon" id="basic-addon1">Nachname</span><input type="text" name="nname" class="form-control" placeholder="" aria-describedby="basic-addon1" required></div>
                <div class="input-group"><span class="input-group-addon" id="basic-addon1">Geburtsdatum</span><input type="date" name="geburtsdatum" class="form-control" placeholder="" aria-describedby="basic-addon1" required></div>
                <div class="input-group"><span class="input-group-addon" id="basic-addon1">Wohnadresse</span><input type="text" name="adresse" class="form-control" placeholder="" aria-describedby="basic-addon1" required></div>
                <div class="input-group"><span class="input-group-addon" id="basic-addon1">Telefon *</span><input type="text" name="telefon" class="form-control" placeholder="" aria-describedby="basic-addon1"></div>
                <div class="input-group"><span class="input-group-addon" id="basic-addon1">E-Mail</span><input type="text" name="email" class="form-control" placeholder="" aria-describedby="basic-addon1" required></div>
                <div class="input-group">
                <input type="checkbox" id="cb1" name="cb1" required/>
                <label for="cb1">Ich habe derzeit <b>keine</b> grippeähnlichen Symptome wie Husten, Fieber oder plötzlichen Verlust des Geruchs- oder Geschmackssinnes.</label>
                </div>
                <div class="input-group">
                <input type="checkbox" id="cb2" name="cb2" required/>
                <label for="cb2">Ich bestätige die wahrheitsgemäße Angabe der Selbsteinschätzung und der angegebenen Daten. Falls sich an den obigen Antworten bis zum Testzeitpunkt etwas ändert, verpflichte ich mich, dies dem Testzentrum vor dem Abstrich mitzuteilen.</label>
                </div>
                <div class="input-group">
                <input type="checkbox" id="cb3" name="cb3" required/>
                <label for="cb3">Ich bin mit dem oben genannten Ablauf einverstanden und akzeptiere die Erklärung zum Datenschutz 
                (<a href="../impressum.php" target="_blank">Datenschutzerklärung in neuem Fenster öffnen</a>).</label>
                </div>
                <span class="input-group-btn">
                <input type="submit" class="btn btn-danger" value="Jetzt Registrieren" name="submit_person" />
                </span>
                </form>
                <p>* optional</p>';
            echo '</div>';
            echo '</div>';
        } elseif($display_slot_termin) {
            // Show available slots
            echo '<div class="panel panel-primary">
            <div class="panel-heading">
            <b>Gewählte Station</b>
            </div>
            <div class="panel-body">
            <div class="row">
            <div class="col-sm-6"><b>Datum</b> <span class="calendarblue">'.$date.'</span></div>
            <div class="col-sm-6"><b>Ort</b> <span class="calendarblue">'.$location.'</span></div>
            </div>
            </div>
            </div>';
            echo '<h3>Termin auswählen</h3>
            <div class="row"><div class="col-sm-12 calendar_selection">';
            foreach($array_termine_slot as $k) {
                $display_slot=sprintf('%02d', $k[1]).':'.sprintf('%02d', ( $k[2]*15-15 ) );
                $display_slot.='&nbsp;-&nbsp;'.(date("H:i",strtotime($display_slot) + 60 * 15));
                if($k[3]>2) {
                    $display_free='<span class="label label-success">'.$k[3].'</span>';
                } else {
                    $display_free='<span class="label label-warning">'.$k[3].'</span>';
                }
                echo '<div style="float: left;"><a class="calendaryellow" href="?appointment='.($k[0]).'&slot=100">'.$display_slot.'
                '.$display_free.'</a></div>';
            }
            echo '</div>';
            echo '</div>';
        } else {
            // ///////////////
            // Kein Ort/Termin ausgewählt
            // ///////////////
            echo '<div class="alert alert-warning" role="alert">
            <h3>Warnung</h3>
            <p>Sie haben keinen Ort/Termin ausgewählt!</p>
            <p>Bitte wählen Sie im <a href="../index.php">Kalender</a> einen Tag und eine Teststation aus.</p>
            </div>';

            echo '</div>';
            echo '</div>';
        }
    } else {
        echo '<div class="alert alert-warning" role="alert">
        <h3>Fehler</h3>';
        echo '<p>Sie haben einen Termin ohne Berechtigung gewählt.</p>
        <p>Bitte nutzen Sie die <a href="business.php">Firmenanmeldung</a>.</p>
        </div>';
    }
} else {
    // ///////////////
    // Kein Termin ausgewählt
    // ///////////////
    echo '<div class="alert alert-warning" role="alert">
    <h3>Warnung</h3>
    <p>Sie haben keinen Termin ausgewählt!</p>
    <p>Bitte wählen Sie im <a href="../index.php">Kalender</a> einen Tag und eine Teststation aus.</p>
    </div>';

    echo '</div>';
    echo '</div>';
}
// Close connection to database
S_close_db($Db);


// Print html content part C
echo $GLOBALS['G_html_main_right_c'];
// Print html footer
echo $GLOBALS['G_html_footer'];

?>