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
include_once '../admin01.php';
//$GLOBALS['FLAG_SHUTDOWN_MAIN']=false;
include_once 'tools.php';
include_once 'auth.php';
include_once 'menu.php';

$current_site="index2";


// Print html header
echo $GLOBALS['G_html_header'];

// Print html menu
echo $GLOBALS['G_html_menu'];
echo $GLOBALS['G_html_menu2'];

// Print html content part A
echo $GLOBALS['G_html_main_right_a'];


if($GLOBALS['FLAG_MODE_MAIN'] == 1) {
    $name_facility='Testzentrum';
    $doing_facility='einen SARS-CoV-2 Test';
    $email_facility='testzentrum@drk-odenwaldkreis.de';
    $logo_facility='logo.png';
    $color_cal_facility='calendarred';
} elseif($GLOBALS['FLAG_MODE_MAIN'] == 2) {
    $name_facility='Impfzentrum';
    $doing_facility='eine Covid-19 Schutz-Impfung';
    $email_facility='impfzentrum@drk-odenwaldkreis.de';
    $logo_facility='impfzentrum.jpg';
    $color_cal_facility='calendarblue';
} elseif($GLOBALS['FLAG_MODE_MAIN'] == 3) {
    $name_facility='Impfzentrum';
    $doing_facility='einen SARS-CoV-2 Antikörpertest';
    $email_facility='impfzentrum@drk-odenwaldkreis.de';
    $logo_facility='impfzentrum.jpg';
    $color_cal_facility='calendarblue';
}




echo '<div class="row">';
echo '<div class="col-sm-12">
<h2>Voranmeldung für '.$doing_facility.'</h2>';

if(!$GLOBALS['FLAG_SHUTDOWN_MAIN']) {

    // Open database connection
    $Db=S_open_db();


    if(isset($_POST['submit_person'])) {
        // ///////////////
        // Registrierung speichern
        // ///////////////

        // save data
        $age_verif=true;
        $k_nname=A_sanitize_input_light($_POST['nname']);
        $k_vname=A_sanitize_input_light($_POST['vname']);
        if($GLOBALS['FLAG_MODE_MAIN'] == 1) {
            $gebdatum_d = A_sanitize_input_light($_POST['gebdatum_d']);
            $gebdatum_m = A_sanitize_input_light($_POST['gebdatum_m']);
            $gebdatum_y = A_sanitize_input_light($_POST['gebdatum_y']);
            $k_geb=sprintf('%04d',$gebdatum_y).'-'.sprintf('%02d',$gebdatum_m).'-'.sprintf('%02d',$gebdatum_d);
            $k_adresse=A_sanitize_input_light($_POST['adresse']);
            $k_ort=A_sanitize_input_light($_POST['ort']);
        } elseif($GLOBALS['FLAG_MODE_MAIN'] == 2) {
            $gebdatum_d = A_sanitize_input_light($_POST['gebdatum_d']);
            $gebdatum_m = A_sanitize_input_light($_POST['gebdatum_m']);
            $gebdatum_y = A_sanitize_input_light($_POST['gebdatum_y']);
            $city_id = A_sanitize_input_light($_POST['city']);
        }
        $k_telefon=A_sanitize_input_light($_POST['telefon']);
        $k_email=A_sanitize_input_light($_POST['email']);
        $k_slot_id=A_sanitize_input_light($_POST['termin_id']);
        $k_date=A_sanitize_input_light($_POST['date']);
        $k_int_date=A_sanitize_input_light($_POST['int_date']);
        $k_int_time1=A_sanitize_input_light($_POST['int_time1']);
        $k_int_time2=A_sanitize_input_light($_POST['int_time2']);
        $k_int_location=A_sanitize_input_light($_POST['int_location']);
        if($GLOBALS['FLAG_MODE_MAIN'] == 2) {
            $k_int_vaccine=A_sanitize_input_light($_POST['int_vaccine']);
            $min_age=A_sanitize_input_light($_POST['min_age']);
            if(isset($_POST['vaccine_number'])) { $k_vaccine_number=intval($_POST['vaccine_number']); } else { $k_vaccine_number=null; }
            if($k_vaccine_number==3) {$k_vaccine_booster=1;} else {$k_vaccine_booster=0;}
            // check min age of person for vaccine
            $timestamp_date=strtotime(substr($k_int_date,6,4).'-'.substr($k_int_date,3,2).'-'.substr($k_int_date,0,2));
            $age = (date("md", date("U", mktime(0, 0, 0, $gebdatum_m, $gebdatum_d, $gebdatum_y))) > date("md",$timestamp_date)
                ? ((date("Y",$timestamp_date) - $gebdatum_y) - 1)
                : (date("Y",$timestamp_date) - $gebdatum_y));
            if($age<$min_age) {
                $age_verif=false;
            } else {
                $age_verif=true;
            }
        }
        $k_cwa_req=$_POST['cb_cwa'];
        if($k_cwa_req=='on') { $k_cwa_req=1; } else { $k_cwa_req=0; }
        $k_cwa_anonym_req=$_POST['cb_cwa_anonym'];
        if($k_cwa_anonym_req=='on') { $k_cwa_req=2; }
        if(isset($_POST['pcr_grund'])) { $k_pcr_grund=intval($_POST['pcr_grund']); } else { $k_pcr_grund=null; }

        // Check if Termin date is in past
        if( ( strtotime($k_date)+60*60*23 ) < time() ) {
            $display_termin_past=true;
        } else {
            $display_termin_past=false;
        }
        if($display_termin_past) {
            echo '<div class="alert alert-warning" role="alert">
            <h3>Fehler</h3>';
            echo '<p>Ihr gewählter Termin liegt in der Vergangenheit.</p>
            <p>Bitte wählen Sie neu auf der <a href="../index.php">Startseite</a>.</p>
            </div>';
        } elseif(!$age_verif) {
            // Age verification not passed - person is too young
            echo '<div class="alert alert-warning" role="alert">
            <h3>Altersverifikation</h3>';
            echo '<p>Ihr gewählter Impfstoff hat ein Mindestalter, die Person muss mindestens '.$min_age.' Jahre alt sein.</p>
            <p>Die eingetragene Person ist allerdings am Tag der Impfung erst '.$age.' Jahre alt - entsprechend Ihrer Eingabe.</p>
            <p>Bitte wählen Sie einen anderen Impfstoff auf der <a href="../index.php">Startseite</a>.</p>
            </div>';
        } elseif ( ($k_email=='' && $GLOBALS['FLAG_MODE_MAIN'] == 2 && $_SESSION['b2b_signedin']) || filter_var($k_email, FILTER_VALIDATE_EMAIL)) {
            if($GLOBALS['FLAG_MODE_MAIN'] == 1) {
                $prereg_id=S_set_entry_voranmeldung($Db,array($k_vname,$k_nname,$k_geb,$k_adresse,$k_ort,$k_telefon,$k_email,$k_slot_id,$k_date,$k_cwa_req,$k_pcr_grund));
            } elseif($GLOBALS['FLAG_MODE_MAIN'] == 2) {
                $prereg_id=S_set_entry_voranmeldung_vaccinate($Db,array($k_vname,$k_nname,$k_geb,$k_telefon,$k_email,$k_slot_id,$k_date,$k_vaccine_booster));
            } else {
                $prereg_id=S_set_entry_voranmeldung_vaccinate($Db,array($k_vname,$k_nname,'',$k_telefon,$k_email,$k_slot_id,$k_date,0));
            }
            if($prereg_id=='DOUBLE_ENTRY') {
                echo '<div class="alert alert-danger" role="alert">
                <h3>Ungültiger Vorgang</h3>
                <p>Sie haben bereits einen Termin für diesen Tag gewählt.</p>
                </div>';
                echo '<div class="list-group">';
                echo '<a class="list-group-item list-group-item-action list-group-item-FAIR" href="../index.php">Zur Startseite</a>';
                echo '</div>';
            } elseif($prereg_id>0) {
                
                
                
                if($GLOBALS['FLAG_MODE_MAIN'] == 2 && $_SESSION['b2b_signedin']) {
                    // No verification via email - is internal usage w/o email verification - direct token
                    $token_ver=A_generate_token(8);
                    while( (S_get_entry($Db,'SELECT id FROM Voranmeldung WHERE Token=\''.$token.'\'')>0) ) {
                        $token=A_generate_token(8);
                    }
                    S_set_data($Db,'UPDATE Voranmeldung SET Token="P'.$token_ver.'" WHERE id='.$prereg_id.';');
                    echo '<div class="alert alert-success" role="alert">
                    <h3>Ihre Daten wurden gespeichert</h3>
                    <p>Die Person wurde für den Termin eingetragen. Der Anmeldevorgang ist beendet.</p>
                    </div>';
                } else {
                    // Generate verification via email
                    $token_ver=A_generate_token(16);
                    S_set_data($Db,'INSERT INTO Voranmeldung_Verif (Token,id_preregistration) VALUES (\''.$token_ver.'\','.$prereg_id.');');
                    // Send email for verification
                    $header = "From: no-reply@testzentrum-odenwald.de\r\n";
                    $header .= "Content-Type: text/plain; charset=UTF-8\nContent-Transfer-Encoding: 8bit";
                    $content="Guten Tag,\n
    Sie wurden soeben für einen Termin im DRK $name_facility Odenwaldkreis eingetragen. Falls diese Anfrage von Ihnen nicht initiiert wurde, können Sie diese Nachricht ignorieren.\n
    Bitte mit diesem Link den Termin bestätigen:\n";
                    $content.=$FLAG_http.'://'.$hostname.($path == '/' ? '' : $path)."/index.php?confirm=confirm&t=$token_ver&i=$prereg_id";
                    $content.="\n\n
    Mit freundlichen Grüßen\n
    Das Team vom DRK $name_facility Odenwaldkreis";
                    $title='DRK Covid-19 '.$name_facility.' Odenwaldkreis - Termin bestätigen';
                    $res=mail($k_email, $title, $content, $header, "-r no-reply@testzentrum-odenwald.de");

                    echo '<div class="alert alert-success" role="alert">
                    <h3>Ihre Daten wurden gespeichert</h3>
                    <p>Sie erhalten jetzt eine E-Mail, die Sie bestätigen müssen. Hierfür haben Sie 20 Minuten Zeit, andernfalls wird Ihr Termin wieder freigegeben und Ihre Daten gelöscht.</p>
                    <p><i>Schauen Sie auch in Ihrem Spam-Ordner, falls die E-Mail nicht ankommt.</i></p>
                    </div>';
                }

                if($GLOBALS['FLAG_MODE_MAIN'] == 1) {
                    echo '<div class="alert alert-info" role="alert">
                    <h3>Ablauf</h3>
                    <p>Bitte wählen Sie einen freien Termin für jede Person, die getestet werden soll.</p>
                    <p>Bitte tragen Sie Ihre Daten ein. Sie erhalten anschließend eine E-Mail, die Sie bestätigen müssen.</p>
                    <p>Nach Abschluss des Registrierungsprozesses erhalten Sie auf Ihre E-Mail-Adresse einen QR-Code, den Sie bei dem Testzentrum vorzeigen müssen (gedruckt oder auf dem Display). Bitte halten Sie im Testzentrum auch einen Lichtbildausweis bereit.</p>
                    <p>Das Ergebnis Ihres Tests wird Ihnen nach dem Abstrich per E-Mail zugeschickt.</p>
                    </div>';
                } elseif($GLOBALS['FLAG_MODE_MAIN'] == 2 && $_SESSION['b2b_signedin']) {
                    echo '<div class="alert alert-info" role="alert">
                    <h3>Ablauf</h3>
                    <p>Bitte wählen Sie einen freien Termin für jede Person, die geimpft werden soll.</p>
                    <p>Bitte tragen Sie Ihre Daten ein.</p>
                    </div>
                    <div class="alert alert-info" role="alert">
                    <h3>Auffrischungsimpfung / Booster</h3>
                    <p>Eine Auffrischungsimpfung / Booster-Impfung ist frühestens sechs Monate nach vollständiger Impfung möglich!</p>
                    </div>';
                } elseif($GLOBALS['FLAG_MODE_MAIN'] == 2) {
                    echo '<div class="alert alert-info" role="alert">
                    <h3>Ablauf</h3>
                    <p>Bitte wählen Sie einen freien Termin für jede Person, die geimpft werden soll.</p>
                    <p>Bitte tragen Sie Ihre Daten ein. Sie erhalten anschließend eine E-Mail, die Sie bestätigen müssen.</p>
                    </div>
                    <div class="alert alert-info" role="alert">
                    <h3>Auffrischungsimpfung / Booster</h3>
                    <p>Eine Auffrischungsimpfung / Booster-Impfung ist frühestens fünf Monate nach vollständiger Impfung möglich!</p>
                    </div>';
                } elseif($GLOBALS['FLAG_MODE_MAIN'] == 3) {
                    echo '<div class="alert alert-info" role="alert">
                    <h3>Ablauf</h3>
                    <p>Bitte wählen Sie einen freien Termin für jede Person, die getestet werden soll.</p>
                    <p>Bitte tragen Sie Ihre Daten ein. Sie erhalten anschließend eine E-Mail, die Sie bestätigen müssen.</p>
                    <p>Bitte bezahlen Sie vor Ort für die Testung <b>30 €</b>.</p>
                    </div>';
                }

                if($_SESSION['b2b_signedin']) {
                    echo '<div class="FAIRsepdown"></div>
                    <div class="list-group">';
                    echo '<a class="list-group-item list-group-item-action list-group-item-FAIR" href="business.php">Neue Registrierung starten</a>';
                    echo '</div>';
                }
                
            } else {
                echo '<div class="alert alert-danger" role="alert">
                <h3>Termin bereits ausgebucht</h3>
                <p>Ihr gewählter Termin ist in der Zwischenzeit ausgebucht und nicht mehr verfügbar. Bitte wählen Sie einen neuen Termin aus.</p>
                </div>';
                echo '<div class="list-group">';
                echo '<a class="list-group-item list-group-item-action list-group-item-FAIR" href="../index.php">Neue Registrierung starten</a>';
                echo '</div>';
            }
        } else {
             // ///////////////////////////
            // Email invalid !!!
            $val_cwa_connection=S_get_entry($Db,'SELECT value FROM website_settings WHERE name="FLAG_CWA_prereg";');
            // Check if Termin is PCR
            if($GLOBALS['FLAG_MODE_MAIN'] == 1) {
                $pcr_test=S_get_entry($Db,'SELECT Testtyp.IsPCR FROM Testtyp JOIN Station ON Station.Testtyp_id=Testtyp.id JOIN Termine ON Termine.id_station=Station.id WHERE Termine.id=CAST('.$k_slot_id.' as int);');
            } else {
                $pcr_test=0;
            }

            echo '
            <div class="alert alert-danger" role="alert">
            <h3>E-Mail ungültig</h3>
            <p>Die eingetragene E-Mail-Adresse entspricht keinem gültigen Format.</p>
            <p>Bitte tragen Sie die Daten korrekt ein.</p>
            </div></div></div>';

            echo '<div class="row">
            <div class="col-sm-12">';
            if($GLOBALS['FLAG_MODE_MAIN'] == 1) {
                echo '<div class="panel panel-danger">';
            } else {
                echo '<div class="panel panel-primary">';
            }

                echo '<div class="panel-heading">
                <b>Gewählter Termin</b>
                </div>
                <div class="panel-body">
                <div class="row">';


                if($GLOBALS['FLAG_MODE_MAIN'] == 1) {
                    echo '<div class="col-sm-4 calendar-col"><b>Datum</b> <span class="'.$color_cal_facility.'">'.$k_int_date.'</span></div>
                    <div class="col-sm-4 calendar-col"><b>Uhrzeit</b> <span class="'.$color_cal_facility.'">'.$k_int_time1.' - '.$k_int_time2.' Uhr</span></div>
                    <div class="col-sm-4 calendar-col"><b>Ort</b> <span class="'.$color_cal_facility.'">'.$k_int_location.'</span></div>';
                } elseif($GLOBALS['FLAG_MODE_MAIN'] == 2) {
                    echo '<div class="col-sm-6 col-lg-2 calendar-col"><b>Datum</b> <span class="'.$color_cal_facility.'">'.$k_int_date.'</span></div>
                    <div class="col-sm-6 col-lg-3 calendar-col"><b>Uhrzeit</b> <span class="'.$color_cal_facility.'">'.$k_int_time1.' - '.$k_int_time2.' Uhr</span></div>
                    <div class="col-sm-6 col-lg-4 calendar-col"><b>Ort</b> <span class="'.$color_cal_facility.'">'.$k_int_location.'</span></div>
                    <div class="col-sm-6 col-lg-3 calendar-col"><b>Impfstoff</b> <span class="'.$color_cal_facility.'">'.$k_int_vaccine.'</span></div>';
                } elseif($GLOBALS['FLAG_MODE_MAIN'] == 3) {
                    echo '<div class="col-sm-4 calendar-col"><b>Datum</b> <span class="'.$color_cal_facility.'">'.$k_int_date.'</span></div>
                    <div class="col-sm-4 calendar-col"><b>Uhrzeit</b> <span class="'.$color_cal_facility.'">'.$k_int_time1.' - '.$k_int_time2.' Uhr</span></div>
                    <div class="col-sm-4 calendar-col"><b>Ort</b> <span class="'.$color_cal_facility.'">'.$k_int_location.'</span></div>';
                }

                echo '
                </div>
                </div>
                </div>';

                echo '<h3>Registrierung</h3>
                <form action="'.$current_site.'.php" method="post">
                    <input type="text" value="'.$k_date.'" name="date" style="display:none;">
                    <input type="text" value="'.$k_slot_id.'" name="termin_id" style="display:none;">
                    <input type="text" value="'.$k_int_date.'" name="int_date" style="display:none;">
                    <input type="text" value="'.$k_int_time1.'" name="int_time1" style="display:none;">
                    <input type="text" value="'.$k_int_time2.'" name="int_time2" style="display:none;">
                    <input type="text" value="'.$k_int_location.'" name="int_location" style="display:none;">';
                if($GLOBALS['FLAG_MODE_MAIN'] == 2) {
                    echo '<input type="text" value="'.$k_int_vaccine.'" name="int_vaccine" style="display:none;">';
                    echo '<input type="text" value="'.$min_age.'" name="min_age" style="display:none;">';
                }

                    echo '<div class="input-group"><span class="input-group-addon" id="basic-addon1">Vorname</span><input type="text" name="vname" class="form-control" placeholder="" aria-describedby="basic-addon1" value="'.$k_vname.'" required></div>
                    <div class="input-group"><span class="input-group-addon" id="basic-addon1">Nachname</span><input type="text" name="nname" class="form-control" placeholder="" aria-describedby="basic-addon1" value="'.$k_nname.'" required></div>';
                    if($GLOBALS['FLAG_MODE_MAIN'] == 1) {
                        echo '<div class="input-group"><span class="input-group-addon" id="basic-addon1">Geburtsdatum</span>
                        <input type="number" min="1" max="31" placeholder="TT" class="form-control" name="gebdatum_d" value="'.$gebdatum_d.'" required>
                        <input type="number" min="1" max="12" placeholder="MM" class="form-control" name="gebdatum_m" value="'.$gebdatum_m.'" required>
                        <input type="number" min="1900" max="2999" placeholder="JJJJ" class="form-control" name="gebdatum_y" value="'.$gebdatum_y.'" required>
                        </div>

                        <div class="input-group"><span class="input-group-addon" id="basic-addon1">Wohnadresse</span><input type="text" name="adresse" class="form-control" placeholder="" aria-describedby="basic-addon1" value="'.$k_adresse.'" required></div>
                        <div class="input-group"><span class="input-group-addon" id="basic-addon1">Wohnort</span><input type="text" name="ort" class="form-control" placeholder="" aria-describedby="basic-addon1" value="'.$k_ort.'" required></div>';
                    } elseif($GLOBALS['FLAG_MODE_MAIN'] == 2) {
                        $city_array=S_get_multientry($Db,'SELECT ID, PLZ, Gemeinde FROM Gemeinden;');
                        echo '<div class="FAIRsepdown"></div>
                        <div class="input-group"><span class="input-group-addon" id="basic-addon1">Geburtsdatum *1)</span>
                        <input type="number" min="1" max="31" placeholder="TT" class="form-control" name="gebdatum_d" value="'.$gebdatum_d.'"  required>
                        <input type="number" min="1" max="12" placeholder="MM" class="form-control" name="gebdatum_m" value="'.$gebdatum_m.'"  required>
                        <input type="number" min="1900" max="2999" placeholder="JJJJ" class="form-control" name="gebdatum_y" value="'.$gebdatum_y.'"  required>
                        </div>
                        <p>*1) Die zu impfende Person muss zum Zeitpunkt der Impfung <b>mindestens '.$min_age.' Jahre</b> alt sein. Das Mindestalter für den gewählten Impfstoff beträgt '.$min_age.' Jahre.</p>

                        <div class="FAIRsepdown"></div>
                        <div class="input-group"><span class="input-group-addon" id="basic-addon1">Gemeinde *2)</span><select id="select-pcr" class="custom-select" style="margin-top:0px;" placeholder="Bitte wählen..." name="city" required>
                        <option value="" selected>Bitte wählen...</option>
                            ';
                            foreach($city_array as $i) {
                                $display=$i[1].' '.$i[2];
                                if($i[0]==$city_id) {$selected='selected';} else {$selected='';}
                                echo '<option value="'.$i[0].'" '.$selected.'>'.$display.'</option>';
                            }
                            echo '
                        </select></div>
                        <p>*2) Nur Personen aus dem Lk Odenwaldkreis werden für eine Impfung akzeptiert.</p>
                        <div class="FAIRsepdown"></div>';
                    }
                    
                    echo '
                    <div class="input-group"><span class="input-group-addon" id="basic-addon1">Telefon</span><input type="text" name="telefon" class="form-control" placeholder="" aria-describedby="basic-addon1" value="'.$k_telefon.'" required></div>
                    <div class="input-group"><span class="input-group-addon" id="basic-addon1">E-Mail</span><input type="text" name="email" class="form-control" placeholder="" aria-describedby="basic-addon1" value="'.$k_email.'" required></div>
                    ';
                    if($GLOBALS['FLAG_MODE_MAIN'] == 1) {
                        if($val_cwa_connection==1 && $pcr_test==0) {
                            if($k_cwa_req==1) {
                                $cwa_selected='checked';
                            } else {
                                $cwa_selected='';
                            }
                            echo '<div class="FAIRsepdown"></div>
                            <div class="header_icon_main">
                            <div class="row ">
                                <div class="col-sm-12">
                                    <div class="header_icon">
                                        <img src="../img/BPA_Corona-Warn-App_Wortbildmarke_B_RGB_RZ01.png" style="display: block; margin-left: auto; margin-right: auto; width: 200px;"></img>
                                        <div class="caption center_text">
                                        <h5>Sie erhalten das Testergebnis unabhängig von der Corona-Warn-App auch per E-Mail zum Abruf. Den QR-Code für die App bekommen Sie bei der Testung vor Ort.</h5>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="FAIRsepdown"></div>
                            <div class="cb_drk">
                            <input type="checkbox" id="cb_cwa_anonym" name="cb_cwa_anonym" '.$cwa_anonym_selected.'/>
                            <label for="cb_cwa_anonym">Einwilligung zur pseudonymisierten Übermittlung (Nicht-namentliche Anzeige) über die Corona-Warn-App (CWA) <span class="text-sm">*optional</span>
                            <br><span class="text-sm">
                            Hiermit erkläre ich mein Einverständnis zum Übermitteln meines Testergebnisses und meines pseudonymen Codes an das Serversystem des RKI, damit ich mein Testergebnis mit der Corona Warn App abrufen kann. Das Testergebnis in der App kann hierbei nicht als namentlicher Testnachweis verwendet werden. Mir wurden Hinweise zum Datenschutz ausgehändigt.
                            </span><br>
                            (<a href="../impressum_test.php#datenschutz_cwa" target="_blank">Datenschutzerklärung in neuem Fenster öffnen</a>)</label>
                            </div>
                            <div class="FAIRsepdown"></div>
                            <div class="cb_drk">
                            <input type="checkbox" id="cb_cwa" name="cb_cwa" '.$cwa_selected.'/>
                            <label for="cb_cwa">Einwilligung zur personalisierten Übermittlung (Namentlicher Testnachweis) über die Corona-Warn-App (CWA) <span class="text-sm">*optional</span>
                            <br><span class="text-sm">
                            Hiermit erkläre ich mein Einverständnis zum Übermitteln des Testergebnisses und meines pseudonymen Codes an das Serversystem des RKI, damit ich mein Testergebnis mit der Corona Warn App abrufen kann. Ich willige außerdem in die Übermittlung meines Namens und Geburtsdatums an die App ein, damit mein Testergebnis in der App als namentlicher Testnachweis angezeigt werden kann. Mir wurden Hinweise zum Datenschutz ausgehändigt.
                            </span><br>
                            (<a href="../impressum_test.php#datenschutz_cwa" target="_blank">Datenschutzerklärung in neuem Fenster öffnen</a>)</label>
                            </div>
                            </div><div class="FAIRsepdown"></div>';

                            echo "

                            <script>
                                var input_anonym = document.getElementById('cb_cwa_anonym');
                                var input_pers = document.getElementById('cb_cwa');
                            
                                input_anonym.addEventListener('change',function(){
                                    if(this.checked) {
                                        input_pers.checked = false;
                                    }
                                });
                                input_pers.addEventListener('change',function(){
                                    if(this.checked) {
                                        input_anonym.checked = false;
                                    }
                                });
                            </script>
                            ";
                        
                        } elseif($pcr_test==1) {
                            $pcr_grund_array=S_get_multientry($Db,'SELECT id, Name FROM Kosten_PCR;');
                            echo '<div class="FAIRsepdown"></div>
                            <div class="alert alert-warning" role="alert">
                                    <div class="header_icon">
                                        <img src="../img/icon/certified_result.svg" style="display: block; margin-left: auto; margin-right: auto; width: 100px;"></img>
                                        <div class="caption center_text">
                                        <h3>Sie haben einen PCR-Test ausgewählt.</h3>
                                        <h4>Das Ergebnis erhalten Sie nach ca. 48 Stunden (ohne Rechtsanspruch, falls es mal länger dauert).</h4>
                                        <h4>Wurde dieser Test angeordnet nach positivem Schnelltest oder aufgrund Kontakt zu einer positiv getesteten Person, so ist die Testung kostenfrei. Bitte bringen Sie dafür eine Bestätigung zum Testzentrum mit (z. B. ein Schnelltest-Zertifikat mit positivem Ergebnis).</h4>
                                        <h4>Andernfalls fallen für den PCR-Test Gebühren an, die Sie im Testzentrum entrichten müssen.</h4>
                                        </div>
                                    </div>
                                    <div class="input-group"><span class="input-group-addon" id="basic-addon1">Grund für einen PCR-Test</span><select id="select-pcr" class="custom-select" style="margin-top:0px;" placeholder="Bitte wählen..." name="pcr_grund" required>
                                    <option value="" selected>Bitte wählen...</option>
                                        ';
                                        foreach($pcr_grund_array as $i) {
                                            $display=$i[1];
                                            echo '<option value="'.$i[0].'">'.$display.'</option>';
                                        }
                                        echo '
                                    </select></div>
                            </div>
                            <div class="FAIRsepdown"></div>';
                        }
                    } elseif($GLOBALS['FLAG_MODE_MAIN'] == 2) {
                        if($k_vaccine_number==1) {$sel_vac_1='selected';$sel_vac_3='';} else {$sel_vac_3='selected';$sel_vac_1='';}
                        echo '<div class="FAIRsepdown"></div>
                        <div class="alert alert-warning" role="alert">
                            <div class="header_icon">
                                <img src="../img/icon/vaccine.png" style="display: block; margin-left: auto; margin-right: auto; width: 100px;"></img>
                                <div class="caption center_text">
                                    <h3>Impfung</h3>
                                    <h4>Wurden Sie bereits geimpft und dies ist eine Auffrischungsimpfung / Booster-Impfung?</h4>
                                </div>
                            </div>
                            <div class="input-group"><span class="input-group-addon" id="basic-addon1">Art der Impfung</span><select id="select-pcr" class="custom-select" style="margin-top:0px;" placeholder="Bitte wählen..." name="vaccine_number" required>
                            <option value="" selected>Bitte wählen...</option>
                            <option value="1" '.$sel_vac_1.'>Grundimmunisierung (1. bzw. 2. Impfung)</option>
                            <option value="3" '.$sel_vac_3.'>Auffrischungs-, Booster-Impfung</option>
                            </select></div>
                        </div>
                        <div class="FAIRsepdown"></div>';
                    }

                    if($GLOBALS['FLAG_MODE_MAIN'] == 1 && $pcr_test!=1) {
                        echo '<div class="FAIRsepdown"></div>
                        <div class="alert alert-warning" role="alert">
                                <div class="header_icon">
                                    <img src="../img/icon/pay.svg" style="display: block; margin-left: auto; margin-right: auto; width: 100px;"></img>
                                    <div class="caption">
                                    <h3>Sie haben einen Antigen-Schnelltest ausgewählt.</h3>
                                    <p>&nbsp;</p>
                                    <h4><b>Dieser Test ist für Sie kostenfrei, wenn Sie zu einer der folgenden Personengruppen gehören:</b></h4>
                                    <h4>A) Personen vor Vollendung des zwölften Lebensjahres bzw. solche die das zwölfte Lebensjahr erst in den letzten drei Monaten vollendet haben</h4>
                                    <h4>B) Schülerinnen und Schüler mit gültigem Schülerausweis</h4>
                                    <h4>C) Personen, die aufgrund einer medizinischen Kontraindikation (insbesondere Schwangerschaft im ersten Schwangerschaftsdrittel) nicht bzw. in den letzten drei Monaten vor der Testung nicht geimpft werden konnten</h4>
                                    <h4>D) Personen, die zum Zeitpunkt der Testung an klinischen Studien zur Wirksamkeit von Impfstoffen teilnehmen bzw. in den letzten drei Monaten vor der Testung teilgenommen haben</h4>
                                    <h4>E) Personen, die sich zum Zeitpunkt der Testung aufgrund einer nachgewiesenen Infektion mit dem Coronavirus SARS-CoV-2 in Absonderung befinden, wenn die Testung zur Beendigung der Absonderung erforderlich ist</h4>
                                    <p>&nbsp;</p>
                                    <h4><b>Dieser Test ist für Sie auch kostenfrei, wenn folgende Bedingungen erfüllt sind (Übergangsregelung bis zum 31. Dezember 2021):</b></h4>
                                    <h4>Bisher nicht vollständig geimpft mit einem vom PEI zugelassenen Impfstoff</h4>
                                    <h4><b>und</b> zu einer der folgenden Personengruppen zugehörig</h4>
                                    <h4>F) Schwangere oder Stillende</h4>
                                    <h4>G) Studierende mit gültigem Studienausweis</h4>
                                    <h4>H) Kinder und Jugendliche im Alter von 12 bis 17 Jahren</h4>
                                    <p>&nbsp;</p>
                                    <p>Personen der Gruppen C bis F benötigen für einen kostenfreien Test ein ärztliches Attest. Nach §1 Abs. 1 der aktuell gültigen Testverordnung des Bundes sind die Ärzte verpflichtet ein solches Attest auszustellen. Die Kosten hierfür trägt der Bund.</p>
                                    <p>&nbsp;</p>
                                    <h4>Andernfalls fallen für den Schnelltest Gebühren in Höhe von <b>20 €</b> an, die Sie im Testzentrum entrichten müssen.</h4>
                                    <p>&nbsp;</p>
                                    <p>Weitere Einzelfälle müssen aktuell im jeweiligen Fall bewertet werden. Rückfragen hierzu frühzeitig an <a href="mailto:testzentrum@drk-odenwaldkreis.de">testzentrum@drk-odenwaldkreis.de</a>.</p>
                                    </div>
                                </div>
                        </div>
                        <div class="FAIRsepdown"></div>';
                    }

                    if($GLOBALS['FLAG_MODE_MAIN'] == 1) {
                        echo '<div class="FAIRsepdown"></div><div class="cb_drk">
                        <input type="checkbox" id="cb1" name="cb1" required checked/>
                        <label for="cb1">Ich habe derzeit <b>keine</b> grippeähnlichen Symptome wie Husten, Fieber oder plötzlichen Verlust des Geruchs- oder Geschmackssinnes.</label>
                        </div>
                        <div class="FAIRsepdown"></div><div class="cb_drk">
                        <input type="checkbox" id="cb2" name="cb2" required checked/>
                        <label for="cb2">Ich bestätige die wahrheitsgemäße Angabe der Selbsteinschätzung und der angegebenen Daten. Falls sich an den obigen Antworten bis zum Testzeitpunkt etwas ändert, verpflichte ich mich, dies dem Testzentrum vor dem Abstrich mitzuteilen.</label>
                        </div>
                        <div class="FAIRsepdown"></div><div class="cb_drk">
                        <input type="checkbox" id="cb3" name="cb3" required checked/>
                        <label for="cb3">Ich bin mit dem oben genannten Ablauf einverstanden und akzeptiere die Erklärung zum Datenschutz 
                        (<a href="../impressum_test.php#datenschutz" target="_blank">Datenschutzerklärung in neuem Fenster öffnen</a>).</label>
                        </div>
                        <div class="FAIRsepdown"></div>
                        <span class="input-group-btn">
                        <input type="submit" class="btn btn-lg btn-primary" value="Jetzt Registrieren" name="submit_person" />
                        </span>
                        </form>
                        <div class="FAIRsepdown"></div>
                        ';
                    } else {
                        echo '<div class="FAIRsepdown"></div><div class="cb_drk">
                        <input type="checkbox" id="cb1" name="cb1" required checked/>
                        <label for="cb1">Ich habe derzeit <b>keine</b> grippeähnlichen Symptome wie Husten, Fieber oder plötzlichen Verlust des Geruchs- oder Geschmackssinnes.</label>
                        </div>
                        <div class="FAIRsepdown"></div><div class="cb_drk">
                        <input type="checkbox" id="cb3" name="cb3" required checked/>
                        <label for="cb3">Ich bin mit dem oben genannten Ablauf einverstanden und akzeptiere die Erklärung zum Datenschutz 
                        (<a href="../impressum_impf.php#datenschutz" target="_blank">Datenschutzerklärung in neuem Fenster öffnen</a>).</label>
                        </div>
                        <div class="FAIRsepdown"></div>
                        <span class="input-group-btn">
                        <input type="submit" class="btn btn-lg btn-primary" value="Jetzt Registrieren" name="submit_person" />
                        </span>
                        </form>
                        <div class="FAIRsepdown"></div>
                        ';
                    }
                echo '</div>';
                echo '</div>';

        }

        

    } elseif(isset($_GET['confirm'])) {
        // ///////////////
        // Registrierung abschließen mit E-Mail Code
        // ///////////////

        $prereg_id=A_sanitize_input($_GET['i']);
        $token_ver=A_sanitize_input($_GET['t']);
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
            <p>Sie erhalten jetzt eine E-Mail mit den Termindaten und ggf. einem QR-Code.</p>
            <p>Der Versand dieser E-Mail kann ein paar Minuten in Anspruch nehmen - bitte haben Sie etwas Geduld.</p>
            </div>';
        } else {
            $token_check=S_get_entry_voranmeldung_debug($Db,$prereg_id);
            if($token_check==null) {
                echo '<div class="alert alert-warning" role="alert">
                <h3>Ungültiger Code</h3>
                <p>Der Link ist bereits abgelaufen. Sie müssen sich neu registrieren und einen neuen Termin auswählen.</p>
                </div>';
                echo '<div class="list-group">';
                echo '<a class="list-group-item list-group-item-action list-group-item-FAIR" href="../index.php">Neue Registrierung starten</a>';
                echo '</div>';
            } else {
                echo '<div class="alert alert-success" role="alert">
                <h3>Ihr Termin wurde bereits bestätigt</h3>
                <p>Sie sollten eine E-Mail mit den Termindaten und ggf. einem QR-Code bereits erhalten haben.</p>
                <p>Der Versand dieser E-Mail kann ein paar Minuten in Anspruch nehmen - bitte haben Sie etwas Geduld.</p>
                </div>';
                echo '<div class="list-group">';
                echo '<a class="list-group-item list-group-item-action list-group-item-FAIR" href="../index.php">Neue Registrierung starten</a>';
                echo '</div>';
            }
            
        }
        
    } elseif(isset($_GET['cancel'])) {
        // ///////////////
        // Termin löschen - Frage
        // ///////////////

        // check pre registration data
        $k_prereg_id=A_sanitize_input($_GET['i']);
        $k_token=A_sanitize_input($_GET['t']);
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
                if($array_appointment[0][4]>=1) {
                    $time1=sprintf('%02d', $array_appointment[0][5]).':'.sprintf('%02d', ( $array_appointment[0][4]*15-15 ) );
                    $time2=(date("H:i",strtotime($time1) + 60 * 15));
                } else {
                    $time1=date("H:i",strtotime($array_appointment[0][2]));
                    $time2=date("H:i",strtotime($array_appointment[0][3]));
                }
                $valid_appointment=true;
            } else {
                $valid_appointment=false;
            }
        } else {
            $valid_appointment=false;
        }

        if($valid_appointment) {
            if($GLOBALS['FLAG_MODE_MAIN'] == 1) {
                echo '<div class="panel panel-danger">';
            } else {
                echo '<div class="panel panel-primary">';
            }
            echo '<div class="panel-heading">
            <b>Termin stornieren / Voranmeldung löschen</b>
            </div>
            <div class="panel-body">
            
            <div class="row calendar_selection">
            <div class="col-sm-4 calendar-col"><b>Datum</b> <span class="'.$color_cal_facility.'">'.$date.'</span></div>
            <div class="col-sm-4 calendar-col"><b>Uhrzeit</b> <span class="'.$color_cal_facility.'">'.$time1.' - '.$time2.' Uhr</span></div>
            <div class="col-sm-4 calendar-col"><b>Name</b> <span class="'.$color_cal_facility.'">'.$k_name.', '.$k_vorname.'</span></div>
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
        $k_prereg_id=A_sanitize_input($_POST['prereg_id']);
        $k_termin_id=A_sanitize_input($_POST['termin_id']);
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
            $val_termin_id=A_sanitize_input($_GET['appointment']);

            $stmt=mysqli_prepare($Db,"SELECT id, Tag, Startzeit, Endzeit, Slot, opt_station, opt_station_adresse, id_station, Stunde FROM Termine WHERE id=?;");
            mysqli_stmt_bind_param($stmt, "i", $val_termin_id);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_bind_result($stmt, $array_appointment[0], $array_appointment[1], $array_appointment[2], $array_appointment[3], $array_appointment[4], $array_appointment[5], $array_appointment[6], $array_appointment[7], $array_appointment[8]);
            mysqli_stmt_fetch($stmt);
            mysqli_stmt_close($stmt);

            // Check if Termin is b2b
            $b2b_code=S_get_multientry($Db,'SELECT Station.id, Station.Firmencode FROM Station JOIN Termine ON Termine.id_station=Station.id WHERE Termine.id=CAST('.$array_appointment[0].' as int);');
            if($b2b_code[0][1]!='') {
                if(isset($_SESSION) && $_SESSION['b2b_signedin'] && ( $_SESSION['b2b_id']==$b2b_code[0][0] || $_SESSION['b2b_code']==$b2b_code[0][1] ) ) {
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

            // Check if Termin is PCR
            if($GLOBALS['FLAG_MODE_MAIN'] == 1) {
                $pcr_test=S_get_entry($Db,'SELECT Testtyp.IsPCR FROM Testtyp JOIN Station ON Station.Testtyp_id=Testtyp.id JOIN Termine ON Termine.id_station=Station.id WHERE Termine.id=CAST('.$array_appointment[0].' as int);');
            } else {
                $pcr_test=0;
            }

            // Slot booking or single Termin
            $date=date("d.m.Y",strtotime($array_appointment[1]));
            $date_sql=date("Y-m-d",strtotime($array_appointment[1]));
            if($array_appointment[4]>0 && !$display_single_termin) {
                $display_slot_termin=true;
                //$array_termine_slot=S_get_multientry($Db,'SELECT id,Stunde,Slot,count(id) FROM Termine WHERE Slot>0 AND id_station='.$array_appointment[7].' AND Date(Tag)=\''.$array_appointment[1].'\' AND Used is null GROUP BY Stunde,Slot;');
                $array_termine_slot=S_get_multientry($Db,'SELECT id,Stunde,Slot,count(id),count(Used) FROM Termine WHERE Slot>0 AND id_station='.$array_appointment[7].' AND Date(Tag)=\''.$array_appointment[1].'\' GROUP BY Stunde,Slot;');
            } elseif(isset($_GET['slot'])) {
                $time1=sprintf('%02d', $array_appointment[8]).':'.sprintf('%02d', ( $array_appointment[4]*15-15 ) );
                $time2=(date("H:i",strtotime($time1) + 60 * 15));
            } else {
                $display_single_termin=true;
                $time1=date("H:i",strtotime($array_appointment[2]));
                $time2=date("H:i",strtotime($array_appointment[3]));
            }

            // Check if Termin date is in past
            if( ( $display_slot_termin ) && ( strtotime($array_appointment[1])+60*60*23 ) < time() ) {
                $display_termin_past=true;
            } elseif( isset($_GET['slot']) && ( ( strtotime($array_appointment[1])+60*60*($array_appointment[8]+1) ) < time() ) ) {
                $display_termin_past=true;
            } elseif( ( $display_single_termin ) && ( strtotime($array_appointment[1])+60*60*23 ) < time() ) {
                $display_termin_past=true;
            } else {
                $display_termin_past=false;
            }

            // Adresse
            if($GLOBALS['FLAG_MODE_MAIN'] == 1) {
                $stations_array=S_get_multientry($Db,'SELECT id, Ort, Adresse FROM Station WHERE id="'.$array_appointment[7].'";');
                if($array_appointment[5]!='') {
                    $location=$stations_array[0][1].', '.$array_appointment[5].', '.$array_appointment[6];
                } else {
                    $location=$stations_array[0][1].', '.$stations_array[0][2];
                }
            } elseif($GLOBALS['FLAG_MODE_MAIN'] == 2) {
                $stations_array=S_get_multientry($Db,'SELECT Station.id, Station.Ort, Station.Adresse, Impfstoff.Kurzbezeichnung, Impfstoff.Mindestalter FROM Station 
                JOIN Impfstoff ON Impfstoff.id=Station.Impfstoff_id WHERE Station.id="'.$array_appointment[7].'";');
                if($array_appointment[5]!='') {
                    $location=$array_appointment[5].', '.$array_appointment[6];
                    $vaccine=$stations_array[0][3];
                    
                } else {
                    $location=$stations_array[0][1].', '.$stations_array[0][2];
                    $vaccine=$stations_array[0][3];
                }
                $min_age=$stations_array[0][4];
            } elseif($GLOBALS['FLAG_MODE_MAIN'] == 3) {
                $stations_array=S_get_multientry($Db,'SELECT id, Ort, Adresse FROM Station WHERE id="'.$array_appointment[7].'";');
                if($array_appointment[5]!='') {
                    $location=$stations_array[0][1].', '.$array_appointment[5].', '.$array_appointment[6];
                } else {
                    $location=$stations_array[0][1].', '.$stations_array[0][2];
                }
            }
        } else {
            $val_station_id=A_sanitize_input($_GET['appointment_more']);
        }

        if($display_termin_past) {

            echo '<div class="alert alert-warning" role="alert">
            <h3>Fehler</h3>';
            echo '<p>Ihr gewählter Termin liegt in der Vergangenheit.</p>
            <p>Bitte wählen Sie neu auf der <a href="../index.php">Startseite</a>.</p>
            </div>';

        } else {

            if($b2b_check) {
                // ///////////////
                // Registrierungsformular
                // ///////////////

                $val_cwa_connection=S_get_entry($Db,'SELECT value FROM website_settings WHERE name="FLAG_CWA_prereg";');
                
                if($b2b_termin) {
                    if($GLOBALS['FLAG_MODE_MAIN'] == 2) {
                        echo '<div class="alert alert-info" role="alert">
                        <h3>Ablauf und Information</h3><p>Bitte tragen Sie Ihre Daten ein. Sie erhalten anschließend eine E-Mail, die Sie bestätigen müssen.</p>
                        <p>Nach Abschluss des Registrierungsprozesses erhalten Sie auf Ihre E-Mail-Adresse eine Terminbestätigung.</p>
                        </div>';
                    } else {
                        echo '<div class="alert alert-info" role="alert">
                        <h3>Ablauf und Information</h3><p>Bitte tragen Sie Ihre Daten ein. Sie erhalten anschließend eine E-Mail, die Sie bestätigen müssen.</p>
                        <p>Nach Abschluss des Registrierungsprozesses erhalten Sie auf Ihre E-Mail-Adresse einen QR-Code, den Sie bei dem Test vorzeigen müssen (gedruckt oder auf dem Display). Bitte halten Sie beim Test auch einen Lichtbildausweis oder Mitarbeiterausweis bereit.</p>
                        <p>Das Ergebnis Ihres Tests wird Ihnen nach dem Abstrich per E-Mail zugeschickt.</p>
                        </div>';
                        echo '<div class="alert alert-danger" role="alert">
                        <p>Ihr Arbeitgeber hat keinen Zugriff auf Ihre eingegebenen Daten und auch nicht auf Ihr Testergebnis.</p>
                        </div>';
                    }
                } elseif($GLOBALS['FLAG_MODE_MAIN'] == 1) {
                    echo '<div class="alert alert-info" role="alert">
                    <h3>Ablauf</h3>';
                    if(!$display_single_termin){
                        echo '<p>Bitte wählen Sie einen freien Termin für jede Person, die getestet werden soll.</p>';
                    }
                    echo '<p>Bitte tragen Sie Ihre Daten ein. Sie erhalten anschließend eine E-Mail, die Sie bestätigen müssen.</p>
                    <p>Nach Abschluss des Registrierungsprozesses erhalten Sie auf Ihre E-Mail-Adresse einen QR-Code, den Sie bei dem Testzentrum vorzeigen müssen (gedruckt oder auf dem Display). Bitte halten Sie im Testzentrum auch einen Lichtbildausweis bereit.</p>
                    <p>Das Ergebnis Ihres Tests wird Ihnen nach dem Abstrich per E-Mail zugeschickt.</p>
                    </div>';
                } elseif($GLOBALS['FLAG_MODE_MAIN'] == 2) {
                    echo '<div class="alert alert-info" role="alert">
                    <h3>Ablauf</h3>';
                    if(!$display_single_termin){
                        echo '<p>Bitte wählen Sie einen freien Termin für jede Person, die geimpft werden soll.</p>';
                    }
                    echo '<p>Bitte tragen Sie Ihre Daten ein. Sie erhalten anschließend eine E-Mail, die Sie bestätigen müssen.</p>
                    <p>Nach Abschluss des Registrierungsprozesses erhalten Sie auf Ihre E-Mail-Adresse eine Terminbestätigung.</p>
                    </div>
                    <div class="alert alert-info" role="alert">
                    <h3>Auffrischungsimpfung / Booster</h3>
                    <p>Eine Auffrischungsimpfung / Booster-Impfung ist frühestens fünf Monate nach vollständiger Impfung möglich!</p>
                    </div>';
                } elseif($GLOBALS['FLAG_MODE_MAIN'] == 3) {
                    echo '<div class="alert alert-info" role="alert">
                    <h3>Ablauf</h3>';
                    if(!$display_single_termin){
                        echo '<p>Bitte wählen Sie einen freien Termin für jede Person, die getestet werden soll.</p>';
                    }
                    echo '<p>Bitte tragen Sie Ihre Daten ein. Sie erhalten anschließend eine E-Mail, die Sie bestätigen müssen.</p>
                    <p>Nach Abschluss des Registrierungsprozesses erhalten Sie auf Ihre E-Mail-Adresse eine Terminbestätigung.</p>
                    <p>Bitte bezahlen Sie vor Ort für die Testung <b>30 €</b>.</p>
                    </div>';
                }
                if($display_single_termin) {
                    if($GLOBALS['FLAG_MODE_MAIN'] == 1) {
                        echo '<div class="panel panel-danger">';
                    } else {
                        echo '<div class="panel panel-primary">';
                    }
                    echo '
                    <div class="panel-heading">
                    <b>Gewählter Termin</b>
                    </div>
                    <div class="panel-body">
                    <div class="row">
                    ';
                    if($GLOBALS['FLAG_MODE_MAIN'] == 1) {
                        echo '<div class="col-sm-4 calendar-col"><b>Datum</b> <span class="'.$color_cal_facility.'">'.$date.'</span></div>
                        <div class="col-sm-4 calendar-col"><b>Uhrzeit</b> <span class="'.$color_cal_facility.'">'.$time1.' - '.$time2.' Uhr</span></div>
                        <div class="col-sm-4 calendar-col"><b>Ort</b> <span class="'.$color_cal_facility.'">'.$location.'</span></div>';
                    } elseif($GLOBALS['FLAG_MODE_MAIN'] == 2) {
                        echo '<div class="col-sm-6 col-lg-2 calendar-col"><b>Datum</b> <span class="'.$color_cal_facility.'">'.$date.'</span></div>
                        <div class="col-sm-6 col-lg-3 calendar-col"><b>Uhrzeit</b> <span class="'.$color_cal_facility.'">'.$time1.' - '.$time2.' Uhr</span></div>
                        <div class="col-sm-6 col-lg-4 calendar-col"><b>Ort</b> <span class="'.$color_cal_facility.'">'.$location.'</span></div>
                        <div class="col-sm-6 col-lg-3 calendar-col"><b>Impfstoff</b> <span class="'.$color_cal_facility.'">'.$vaccine.'</span></div>';
                    } elseif($GLOBALS['FLAG_MODE_MAIN'] == 3) {
                        echo '<div class="col-sm-4 calendar-col"><b>Datum</b> <span class="'.$color_cal_facility.'">'.$date.'</span></div>
                        <div class="col-sm-4 calendar-col"><b>Uhrzeit</b> <span class="'.$color_cal_facility.'">'.$time1.' - '.$time2.' Uhr</span></div>
                        <div class="col-sm-4 calendar-col"><b>Ort</b> <span class="'.$color_cal_facility.'">'.$location.'</span></div>';
                    }
                    echo '</div>
                    </div>
                    </div>';

                    echo '<h3>Registrierung</h3>
                    <form action="'.$current_site.'.php" method="post">
                        <input type="text" value="'.$date_sql.'" name="date" style="display:none;">
                        <input type="text" value="'.$val_termin_id.'" name="termin_id" style="display:none;">
                        <input type="text" value="'.$date.'" name="int_date" style="display:none;">
                        <input type="text" value="'.$time1.'" name="int_time1" style="display:none;">
                        <input type="text" value="'.$time2.'" name="int_time2" style="display:none;">
                        <input type="text" value="'.$location.'" name="int_location" style="display:none;">';
                        if($GLOBALS['FLAG_MODE_MAIN'] == 2) {
                            echo '<input type="text" value="'.$vaccine.'" name="int_vaccine" style="display:none;">';
                            echo '<input type="text" value="'.$min_age.'" name="min_age" style="display:none;">';
                        }

                        echo '<div class="input-group"><span class="input-group-addon" id="basic-addon1">Vorname</span><input type="text" name="vname" class="form-control" placeholder="" aria-describedby="basic-addon1" required></div>
                        <div class="input-group"><span class="input-group-addon" id="basic-addon1">Nachname</span><input type="text" name="nname" class="form-control" placeholder="" aria-describedby="basic-addon1" required></div>';

                        if($GLOBALS['FLAG_MODE_MAIN'] == 1) {
                            echo '
                            <div class="input-group"><span class="input-group-addon" id="basic-addon1">Geburtsdatum</span>
                            <input type="number" min="1" max="31" placeholder="TT" class="form-control" name="gebdatum_d" required>
                            <input type="number" min="1" max="12" placeholder="MM" class="form-control" name="gebdatum_m" required>
                            <input type="number" min="1900" max="2999" placeholder="JJJJ" class="form-control" name="gebdatum_y" required>
                            </div>

                            <div class="input-group"><span class="input-group-addon" id="basic-addon1">Wohnadresse</span><input type="text" name="adresse" class="form-control" placeholder="" aria-describedby="basic-addon1" required></div>
                            <div class="input-group"><span class="input-group-addon" id="basic-addon1">Wohnort</span><input type="text" name="ort" class="form-control" placeholder="" aria-describedby="basic-addon1" required></div>';
                        } elseif($GLOBALS['FLAG_MODE_MAIN'] == 2) {
                            $city_array=S_get_multientry($Db,'SELECT ID, PLZ, Gemeinde FROM Gemeinden;');

                            echo '<div class="FAIRsepdown"></div>
                            <div class="input-group"><span class="input-group-addon" id="basic-addon1">Geburtsdatum</span>
                            <input type="number" min="1" max="31" placeholder="TT" class="form-control" name="gebdatum_d" required>
                            <input type="number" min="1" max="12" placeholder="MM" class="form-control" name="gebdatum_m" required>
                            <input type="number" min="1900" max="2999" placeholder="JJJJ" class="form-control" name="gebdatum_y" required>
                            </div>
                            <p>*1) Die zu impfende Person muss zum Zeitpunkt der Impfung <b>mindestens '.$min_age.' Jahre</b> alt sein. Das Mindestalter für den gewählten Impfstoff beträgt '.$min_age.' Jahre.</p>

                            <div class="FAIRsepdown"></div>
                            <div class="input-group"><span class="input-group-addon" id="basic-addon1">Gemeinde *2)</span><select id="select-pcr" class="custom-select" style="margin-top:0px;" placeholder="Bitte wählen..." name="city" required>
                            <option value="" selected>Bitte wählen...</option>
                                ';
                                foreach($city_array as $i) {
                                    $display=$i[1].' '.$i[2];
                                    echo '<option value="'.$i[0].'">'.$display.'</option>';
                                }
                                echo '
                            </select></div>
                            <p>*2) Nur Personen aus dem Lk Odenwaldkreis werden für eine Impfung akzeptiert.</p>

                            <div class="FAIRsepdown"></div>';
                        }

                        echo '
                        <div class="input-group"><span class="input-group-addon" id="basic-addon1">Telefon</span><input type="text" name="telefon" class="form-control" placeholder="" aria-describedby="basic-addon1" required></div>';
                        if($GLOBALS['FLAG_MODE_MAIN'] == 2 && $_SESSION['b2b_signedin']) {
                            echo '<div class="input-group"><span class="input-group-addon" id="basic-addon1">E-Mail *</span><input type="text" name="email" class="form-control" placeholder="" aria-describedby="basic-addon1"></div>
                            <p>* optional</p>
                            ';
                        } else {
                            echo '<div class="input-group"><span class="input-group-addon" id="basic-addon1">E-Mail</span><input type="text" name="email" class="form-control" placeholder="" aria-describedby="basic-addon1" required></div>
                            ';
                        }
                        if($GLOBALS['FLAG_MODE_MAIN'] == 1) {
                            if($val_cwa_connection==1 && $pcr_test==0) {
                                echo '<div class="FAIRsepdown"></div>
                                <div class="header_icon_main">
                                <div class="row ">
                                    <div class="col-sm-12">
                                        <div class="header_icon">
                                            <img src="../img/BPA_Corona-Warn-App_Wortbildmarke_B_RGB_RZ01.png" style="display: block; margin-left: auto; margin-right: auto; width: 200px;"></img>
                                            <div class="caption center_text">
                                            <h5>Sie erhalten das Testergebnis unabhängig von der Corona-Warn-App auch per E-Mail zum Abruf. Den QR-Code für die App bekommen Sie bei der Testung vor Ort.</h5>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="FAIRsepdown"></div>
                                <div class="cb_drk">
                                <input type="checkbox" id="cb_cwa_anonym" name="cb_cwa_anonym"/>
                                <label for="cb_cwa_anonym">Einwilligung zur pseudonymisierten Übermittlung (Nicht-namentliche Anzeige) über die Corona-Warn-App (CWA) <span class="text-sm">*optional</span>
                                <br><span class="text-sm">
                                Hiermit erkläre ich mein Einverständnis zum Übermitteln meines Testergebnisses und meines pseudonymen Codes an das Serversystem des RKI, damit ich mein Testergebnis mit der Corona Warn App abrufen kann. Das Testergebnis in der App kann hierbei nicht als namentlicher Testnachweis verwendet werden. Mir wurden Hinweise zum Datenschutz ausgehändigt.
                                </span><br>
                                (<a href="../impressum_test.php#datenschutz_cwa" target="_blank">Datenschutzerklärung in neuem Fenster öffnen</a>)</label>
                                </div>
                                <div class="FAIRsepdown"></div>
                                <div class="cb_drk">
                                <input type="checkbox" id="cb_cwa" name="cb_cwa"/>
                                <label for="cb_cwa">Einwilligung zur personalisierten Übermittlung (Namentlicher Testnachweis) über die Corona-Warn-App (CWA) <span class="text-sm">*optional</span>
                                <br><span class="text-sm">
                                Hiermit erkläre ich mein Einverständnis zum Übermitteln des Testergebnisses und meines pseudonymen Codes an das Serversystem des RKI, damit ich mein Testergebnis mit der Corona Warn App abrufen kann. Ich willige außerdem in die Übermittlung meines Namens und Geburtsdatums an die App ein, damit mein Testergebnis in der App als namentlicher Testnachweis angezeigt werden kann. Mir wurden Hinweise zum Datenschutz ausgehändigt.
                                </span><br>
                                (<a href="../impressum_test.php#datenschutz_cwa" target="_blank">Datenschutzerklärung in neuem Fenster öffnen</a>)</label>
                                </div>
                                </div><div class="FAIRsepdown"></div>';
                            

                                echo "

                                <script>
                                    var input_anonym = document.getElementById('cb_cwa_anonym');
                                    var input_pers = document.getElementById('cb_cwa');
                                
                                    input_anonym.addEventListener('change',function(){
                                        if(this.checked) {
                                            input_pers.checked = false;
                                        }
                                    });
                                    input_pers.addEventListener('change',function(){
                                        if(this.checked) {
                                            input_anonym.checked = false;
                                        }
                                    });
                                </script>
                                ";
                            
                            } elseif($pcr_test==1) {
                                $pcr_grund_array=S_get_multientry($Db,'SELECT id, Name FROM Kosten_PCR;');
                                echo '<div class="FAIRsepdown"></div>
                                <div class="alert alert-warning" role="alert">
                                        <div class="header_icon">
                                            <img src="../img/icon/certified_result.svg" style="display: block; margin-left: auto; margin-right: auto; width: 100px;"></img>
                                            <div class="caption center_text">
                                            <h3>Sie haben einen PCR-Test ausgewählt.</h3>
                                            <h4>Das Ergebnis erhalten Sie nach ca. 48 Stunden (ohne Rechtsanspruch, falls es mal länger dauert).</h4>
                                            <h4>Wurde dieser Test angeordnet nach positivem Schnelltest oder aufgrund Kontakt zu einer positiv getesteten Person, so ist die Testung kostenfrei. Bitte bringen Sie dafür eine Bestätigung zum Testzentrum mit (z. B. ein Schnelltest-Zertifikat mit positivem Ergebnis).</h4>
                                            <h4>Andernfalls fallen für den PCR-Test Gebühren an, die Sie im Testzentrum entrichten müssen.</h4>
                                            </div>
                                        </div>
                                        <div class="input-group"><span class="input-group-addon" id="basic-addon1">Grund für einen PCR-Test</span><select id="select-pcr" class="custom-select" style="margin-top:0px;" placeholder="Bitte wählen..." name="pcr_grund" required>
                                        <option value="" selected>Bitte wählen...</option>
                                            ';
                                            foreach($pcr_grund_array as $i) {
                                                $display=$i[1];
                                                echo '<option value="'.$i[0].'">'.$display.'</option>';
                                            }
                                            echo '
                                        </select></div>
                                </div>
                                <div class="FAIRsepdown"></div>';
                            }
                        } elseif($GLOBALS['FLAG_MODE_MAIN'] == 2) {
                            echo '<div class="FAIRsepdown"></div>
                            <div class="alert alert-warning" role="alert">
                                <div class="header_icon">
                                    <img src="../img/icon/vaccine.png" style="display: block; margin-left: auto; margin-right: auto; width: 100px;"></img>
                                    <div class="caption center_text">
                                        <h3>Impfung</h3>
                                        <h4>Wurden Sie bereits geimpft und dies ist eine Auffrischungsimpfung / Booster-Impfung?</h4>
                                    </div>
                                </div>
                                <div class="input-group"><span class="input-group-addon" id="basic-addon1">Art der Impfung</span><select id="select-pcr" class="custom-select" style="margin-top:0px;" placeholder="Bitte wählen..." name="vaccine_number" required>
                                <option value="" selected>Bitte wählen...</option>
                                <option value="1">Grundimmunisierung (1. bzw. 2. Impfung)</option>
                                <option value="3">Auffrischungs-, Booster-Impfung</option>
                                </select></div>
                            </div>
                            <div class="FAIRsepdown"></div>';
                        }

                        if($GLOBALS['FLAG_MODE_MAIN'] == 1 && $pcr_test!=1) {
                            /* echo '<div class="FAIRsepdown"></div>
                            <div class="alert alert-warning" role="alert">
                                    <div class="header_icon">
                                        <img src="../img/icon/pay.svg" style="display: block; margin-left: auto; margin-right: auto; width: 100px;"></img>
                                        <div class="caption">
                                        <h3>Sie haben einen Antigen-Schnelltest ausgewählt.</h3>
                                        <p>&nbsp;</p>
                                        <h4><b>Dieser Test ist für Sie kostenfrei, wenn Sie zu einer der folgenden Personengruppen gehören:</b></h4>
                                        <h4>A) Im Rahmen der kostenfreien Bürger-Testung hat jede*r Bürger*in mindestens einmal pro Woche Anspruch (ab 13.11.2021)</h4>
                                        <h4>B) Personen vor Vollendung des zwölften Lebensjahres bzw. solche die das zwölfte Lebensjahr erst in den letzten drei Monaten vollendet haben</h4>
                                        <h4>C) Schülerinnen und Schüler mit gültigem Schülerausweis</h4>
                                        <h4>D) Personen, die aufgrund einer medizinischen Kontraindikation (insbesondere Schwangerschaft im ersten Schwangerschaftsdrittel) nicht bzw. in den letzten drei Monaten vor der Testung nicht geimpft werden konnten</h4>
                                        <h4>E) Personen, die zum Zeitpunkt der Testung an klinischen Studien zur Wirksamkeit von Impfstoffen teilnehmen bzw. in den letzten drei Monaten vor der Testung teilgenommen haben</h4>
                                        <h4>F) Personen, die sich zum Zeitpunkt der Testung aufgrund einer nachgewiesenen Infektion mit dem Coronavirus SARS-CoV-2 in Absonderung befinden, wenn die Testung zur Beendigung der Absonderung erforderlich ist</h4>
                                        <p>&nbsp;</p>
                                        <h4><b>Dieser Test ist für Sie auch kostenfrei, wenn folgende Bedingungen erfüllt sind (Übergangsregelung bis zum 31. Dezember 2021):</b></h4>
                                        <h4>Bisher nicht vollständig geimpft mit einem vom PEI zugelassenen Impfstoff</h4>
                                        <h4><b>und</b> zu einer der folgenden Personengruppen zugehörig</h4>
                                        <h4>G) Schwangere oder Stillende</h4>
                                        <h4>H) Studierende mit gültigem Studienausweis</h4>
                                        <h4>I) Kinder und Jugendliche im Alter von 12 bis 17 Jahren</h4>
                                        <p>&nbsp;</p>
                                        <p>Personen der Gruppen D bis G benötigen für einen kostenfreien Test ein ärztliches Attest. Nach §1 Abs. 1 der aktuell gültigen Testverordnung des Bundes sind die Ärzte verpflichtet ein solches Attest auszustellen. Die Kosten hierfür trägt der Bund.</p>
                                        <p>&nbsp;</p>
                                        <h4>Andernfalls fallen für den Schnelltest Gebühren in Höhe von <b>20 €</b> an, die Sie im Testzentrum entrichten müssen.</h4>
                                        <p>&nbsp;</p>
                                        <p>Weitere Einzelfälle müssen aktuell im jeweiligen Fall bewertet werden. Rückfragen hierzu frühzeitig an <a href="mailto:testzentrum@drk-odenwaldkreis.de">testzentrum@drk-odenwaldkreis.de</a>.</p>
                                        </div>
                                    </div>
                            </div>
                            <div class="FAIRsepdown"></div>'; */
                        }

                        if($GLOBALS['FLAG_MODE_MAIN'] == 1) {
                            echo '<div class="FAIRsepdown"></div><div class="cb_drk">
                            <input type="checkbox" id="cb1" name="cb1" required/>
                            <label for="cb1">Ich habe derzeit <b>keine</b> grippeähnlichen Symptome wie Husten, Fieber oder plötzlichen Verlust des Geruchs- oder Geschmackssinnes.</label>
                            </div>
                            <div class="FAIRsepdown"></div><div class="cb_drk">
                            <input type="checkbox" id="cb2" name="cb2" required/>
                            <label for="cb2">Ich bestätige die wahrheitsgemäße Angabe der Selbsteinschätzung und der angegebenen Daten. Falls sich an den obigen Antworten bis zum Testzeitpunkt etwas ändert, verpflichte ich mich, dies dem Testzentrum vor dem Abstrich mitzuteilen.</label>
                            </div>
                            <div class="FAIRsepdown"></div><div class="cb_drk">
                            <input type="checkbox" id="cb3" name="cb3" required/>
                            <label for="cb3">Ich bin mit dem oben genannten Ablauf einverstanden und akzeptiere die Erklärung zum Datenschutz 
                            (<a href="../impressum_test.php#datenschutz" target="_blank">Datenschutzerklärung in neuem Fenster öffnen</a>).</label>
                            </div>
                            <div class="FAIRsepdown"></div>
                            <span class="input-group-btn">
                            <input type="submit" class="btn btn-lg btn-primary" value="Jetzt Registrieren" name="submit_person" />
                            </span>
                            </form>
                            <div class="FAIRsepdown"></div>
                            ';
                        } else {
                            echo '<div class="FAIRsepdown"></div><div class="cb_drk">
                            <input type="checkbox" id="cb1" name="cb1" required/>
                            <label for="cb1">Ich habe derzeit <b>keine</b> grippeähnlichen Symptome wie Husten, Fieber oder plötzlichen Verlust des Geruchs- oder Geschmackssinnes.</label>
                            </div>
                            <div class="FAIRsepdown"></div><div class="cb_drk">
                            <input type="checkbox" id="cb3" name="cb3" required/>
                            <label for="cb3">Ich bin mit dem oben genannten Ablauf einverstanden und akzeptiere die Erklärung zum Datenschutz 
                            (<a href="../impressum_impf.php#datenschutz" target="_blank">Datenschutzerklärung in neuem Fenster öffnen</a>).</label>
                            </div>
                            <div class="FAIRsepdown"></div>
                            <span class="input-group-btn">
                            <input type="submit" class="btn btn-lg btn-primary" value="Jetzt Registrieren" name="submit_person" />
                            </span>
                            </form>
                            <div class="FAIRsepdown"></div>
                            ';
                        }
                    echo '</div>';
                    echo '</div>';
                } elseif($display_slot_termin) {
                    // Show available slots
                    $current_time=time();
                    if($GLOBALS['FLAG_MODE_MAIN'] == 1) {
                        echo '<div class="panel panel-danger">';
                    } else {
                        echo '<div class="panel panel-primary">';
                    }
                    echo '
                    <div class="panel-heading">
                    <b>Gewählte Station</b>
                    </div>
                    <div class="panel-body">
                    <div class="row">
                    <div class="col-sm-3 calendar-col"><b>Datum</b> <span class="'.$color_cal_facility.'">'.$date.'</span></div>';
                    if($GLOBALS['FLAG_MODE_MAIN'] == 1) {
                        echo '<div class="col-sm-6 calendar-col"><b>Ort</b> <span class="'.$color_cal_facility.'">'.$location.'</span></div>';
                    } elseif($GLOBALS['FLAG_MODE_MAIN'] == 2) {
                        echo '<div class="col-sm-5 calendar-col"><b>Ort</b> <span class="'.$color_cal_facility.'">'.$location.'</span></div>
                        <div class="col-sm-4 calendar-col"><b>Impfstoff</b> <span class="'.$color_cal_facility.'">'.$vaccine.'</span></div>';
                    } elseif($GLOBALS['FLAG_MODE_MAIN'] == 3) {
                        echo '<div class="col-sm-6 calendar-col"><b>Ort</b> <span class="'.$color_cal_facility.'">'.$location.'</span></div>';
                    }

                    echo '</div>
                    </div>
                    </div>';
                    echo '<h3>Termin auswählen</h3>
                    <div class="row"><div class="col-sm-12 calendar_selection">';
                    $at_least_one=false;
                    foreach($array_termine_slot as $k) {
                        if( $date==date('d.m.Y') && $current_time > strtotime(sprintf('%02d', $k[1]).':'.sprintf('%02d', ( $k[2]*15-15 )).':00') ) {
                            // time over
                            
                        } elseif(($k[3]<=$k[4])) {
                            $display_slot=sprintf('%02d', $k[1]).':'.sprintf('%02d', ( $k[2]*15-15 ) );
                            $display_slot.='&nbsp;-&nbsp;'.(date("H:i",strtotime($display_slot) + 60 * 15));
                            if(($k[3]-$k[4])>2) {
                                $display_free='<span class="label label-success">'.($k[3]-$k[4]).'</span>';
                            } else {
                                $display_free='<span class="label label-warning">'.($k[3]-$k[4]).'</span>';
                            }
                            echo '<div style="float: left;"><a class="calendaryellow-dis">'.$display_slot.' ausgebucht</a></div>';
                            $at_least_one=true;
                        } else {
                            $display_slot=sprintf('%02d', $k[1]).':'.sprintf('%02d', ( $k[2]*15-15 ) );
                            $display_slot.='&nbsp;-&nbsp;'.(date("H:i",strtotime($display_slot) + 60 * 15));
                            if(($k[3]-$k[4])>2) {
                                $display_free='<span class="label label-success">'.($k[3]-$k[4]).'</span>';
                            } else {
                                $display_free='<span class="label label-warning">'.($k[3]-$k[4]).'</span>';
                            }
                            echo '<div style="float: left;"><a class="calendaryellow" href="?appointment='.($k[0]).'&slot=100">'.$display_slot.'
                            '.$display_free.'</a></div>';
                            $at_least_one=true;
                        }
                    }
                    if(!$at_least_one) {
                        echo '<div class="alert alert-warning" role="alert">
                    <p>Die Station hat heute keine Termine mehr</p>
                    </div>';
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
        }
    } else {
        // ///////////////
        // Kein Termin ausgewählt
        // ///////////////
        echo '<div class="alert alert-warning" role="alert">
        <h3>Warnung</h3>
        <p>Sie haben keinen Termin ausgewählt!</p>
        <p>Bitte wählen Sie im <a href="../index.php">Kalender</a> einen Tag und eine Teststation bzw. Impfstoff aus.</p>
        </div>';

        echo '</div>';
        echo '</div>';
    }
    // Close connection to database
    S_close_db($Db);

} else {

    echo '<div class="alert alert-danger" role="alert">
    <h3>Wartungsarbeiten</h3>
    <p>Derzeit finden Arbeiten an dieser Seite statt, die Terminbuchung und alle Services damit stehen momentan nicht zur Verfügung. Bald geht es wieder weiter...wir bitten um etwas Geduld.</p>
    <div class="FAIRsepdown"></div>
    <div class="FAIRsep"></div>
</div>';
    echo '</div>';
    echo '</div>';
}


// Print html content part C
echo $GLOBALS['G_html_main_right_c'];
// Print html footer
echo $GLOBALS['G_html_footer'];

?>