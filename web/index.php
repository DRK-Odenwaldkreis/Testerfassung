<?php

/* **************

Websystem für das Impf- und Testzentrum DRK Odenwaldkreis
Author: Marc S. Duchene
October 2021

** ************** */


// Include functions
include_once 'admin01.php';
include_once 'menu.php';
//$GLOBALS['FLAG_SHUTDOWN_MAIN']=false;
if(!$GLOBALS['FLAG_SHUTDOWN_MAIN']) {
    include_once 'registration/auth.php';
    include_once 'registration/tools.php';
}

// Print html header
echo $GLOBALS['G_html_header'];

// Print html menu
echo $GLOBALS['G_html_menu'];
echo $GLOBALS['G_html_menu2'];

// Print html content part A
echo $GLOBALS['G_html_main_right_a'];


if($GLOBALS['FLAG_MODE_MAIN'] == 1) {
    $name_facility='Testzentrum';
    $doing_facility='Testungen';
    $email_facility='testzentrum@drk-odenwaldkreis.de';
    $logo_facility='logo.png';
} elseif($GLOBALS['FLAG_MODE_MAIN'] == 2) {
    $name_facility='Impfzentrum';
    $doing_facility='Impfungen';
    $email_facility='impfzentrum@drk-odenwaldkreis.de';
    $logo_facility='impfzentrum.jpg';

} elseif($GLOBALS['FLAG_MODE_MAIN'] == 3) {
    $name_facility='Impfzentrum';
    $doing_facility='Antikörper-Testungen';
    $email_facility='testzentrum@drk-odenwaldkreis.de';
    $logo_facility='impfzentrum.jpg';

}

if($GLOBALS['FLAG_MODE_MAIN'] == 2) {
    echo '
    <div class="row">

        <div class="col-sm-6" style="padding:10px;">
            <img src="img/'.$logo_facility.'" style="display: block; margin-left: auto; margin-right: auto; width: 65%; border: solid 1px #5a482d;">
        </div>

        <div class="col-sm-6" style="padding:10px;">
        <img src="https://corona.odenwaldkreis.de/wp-content/uploads/2020/04/odenwaldkreislogo.jpg" style="display: block; margin-left: auto; margin-right: auto; width: 40%; border: solid 1px #5a482d;">
            <div style="text-align: center;">
                <h2>Covid-19 '.$name_facility.'</h2>
                <h3>im Auftrag des Landkreis Odenwaldkreis</h3>
                
            </div>
        </div>
    </div>
    ';
} else {
    echo '
    <div class="row">

        <div class="col-sm-6" style="padding:10px;">
            <img src="img/'.$logo_facility.'" style="display: block; margin-left: auto; margin-right: auto; width: 65%; border: solid 1px #5a482d;">
        </div>

        <div class="col-sm-6">
            <div style="text-align: center;">
                <h2>Covid-19 '.$name_facility.' Odenwaldkreis</h2>
                <h3>Deutsches Rotes Kreuz - Kreisverband Odenwaldkreis e. V.</h3>
            </div>
        </div>
    </div>
    ';
}

if($GLOBALS['FLAG_MODE_MAIN'] == 2) {
    // Wechsel Impfzentrum
    if( date("Y-m-d") > "2021-12-03") {
        echo '
        <div class="row">
            <div class="col-lg-12 col-sm-12">
                <div class="alert alert-danger" role="alert">
                    <div style="text-align: center;">
                        <h3><b>Das Impfzentrum Erbach ist umgezogen</b></h3>
                        <p>Ab 06.12. finden Sie das Erbacher Impfzentrum in der</p>
                        <p><b>Neue Lustgartenstraße 1-3</b></p>
                    </div>
                </div>
            </div>
        </div>
        <div class="FAIRsepdown"></div>
        ';
    }
} else {
    // Wechsel Impfzentrum
    if( date("Y-m-d") > "2021-12-03") {
        echo '
        <div class="row">
            <div class="col-lg-12 col-sm-12">
                <div class="alert alert-danger" role="alert">
                    <div style="text-align: center;">
                        <h3><b>Das Impfzentrum Erbach ist umgezogen</b></h3>
                        <p>Ab 06.12. finden Sie das Erbacher Impfzentrum in der</p>
                        <p><b>Neue Lustgartenstraße 1-3</b></p>
                        <p>&nbsp;</p>
                        <p><b>Das Testzentrum Erbach ist weiterhin in der Martin-Luther-Str. 18-20</b></p>
                        <p>dort bieten wir weiterhin die Antigentests, PCR-Tests und Antikörpertests an</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="FAIRsepdown"></div>
        ';
    }
}


if($GLOBALS['FLAG_MODE_MAIN'] == 2) {
    // Impfzentrum Infos
    echo '
        </div></div>

        </div>
    </div>
    <div class="FAIRsepdown"></div>
    <div class="FAIRsepdown"></div>

    <div class="row">
        
        <div class="col-lg-12">
        <h2 style="text-align: center;">Informationen für Ihre Impfung</h2>
        </div>
        <div class="col-lg-6">
        <div class="thumbnail">
        <div style="font-size: 45px; text-align: center; padding: 10px;">
        <span class="icon-file3"></span>
        </div>
        <div class="caption">
            <h3 style="text-align: center;">Dokumente für die Impfung</h3>
            <p><ul>
            <li><a href="https://www.rki.de/DE/Content/Infekt/Impfen/Materialien/COVID-19-Aufklaerungsbogen-Tab.html" target="_blank">Aufklärungs-, Anamnese- und Einwilligungsbogen zur Impfung mit mRNA-Impfstoff (Comirnaty von BioNTech/Pfizer und Spikevax von Moderna)</a></li>
            <li><a href="https://www.rki.de/DE/Content/Infekt/Impfen/Materialien/COVID-19-Vektorimpfstoff-Tab.html" target="_blank">Aufklärungs-, Anamnese- und Einwilligungsbogen zur Impfung mit Vektorimpfstoff (Vaxzevria von AstraZeneca und Janssen von Johnson & Johnson)</a></li>
            <li><a href="https://corona.odenwaldkreis.de/wp-content/uploads/2021/05/Datenschutz-Info-Stand-15.03.2021.pdf" target="_blank">Information über die Verarbeitung personenbezogener Daten (Datenschutz) </a></li>
            </ul></p>
            <h3 style="text-align: center;">Videos mit Informationen in Gebärdensprache</h3>
            <p><ul>
            <li><a href="https://www.youtube.com/watch?v=tkidRQrbM5w&feature=youtu.be" target="_blank">Aufklärungsmerkblatt mRNA-Impfstoff</a></li>
            </ul></p>
        </div>
        </div>
        </div>
        <div class="col-lg-6">
        <div class="thumbnail">
        <div style="font-size: 45px; text-align: center; padding: 10px;">
        <span class="icon-chat"></span>
        </div>
        <div class="caption">
            <h3 style="text-align: center;">Fragen und Antworten zum Thema Impfen</h3>
            <p>Die Hessische Landesregierung hat auf ihrer Internetseite verschiedene Fragen und Antworten rund um die Impfzentren eingestellt</p>
            <p style="text-align: center;"><a href="https://corona-impfung.hessen.de/" class="btn btn-primary" role="button" target="_blank">corona-impfung.hessen.de/</a></p>
            <p></p>
            <p>Auf der Seite des Bundesgesundheitsministeriums finden sich Informationen zu den Themen Impfreihenfolge, Impfstoff und Wirksamkeit/Sicherheit</p>
            <p style="text-align: center;"><a href="https://www.zusammengegencorona.de/informieren/informationen-zum-impfen/" class="btn btn-primary" role="button" target="_blank">www.zusammengegencorona.de</a></p>
            <p></p>
            <h3 style="text-align: center;">Informationen in Gebärdensprache</h3>
            <p><ul>
            <li><a href="https://www.youtube.com/watch?v=wJkBJsz7cIM&feature=youtu.be" target="_blank">Informationen rund um die Schutzimpfung in Gebärdensprache</a></li>
            <li><a href="https://www.youtube.com/watch?v=_1sKYE4r8K0&feature=youtu.be" target="_blank">Ablauf der Impfung im Impfzentrum in Gebärdensprache</a></li>
            </ul></p>
        </div>
        </div>
        </div>

        </div>
    </div>
    <div class="FAIRsepdown"></div>';
} elseif($GLOBALS['FLAG_MODE_MAIN'] == 3) {
    // Antikörpertest Infos
    echo '
        </div></div>

        </div>
    </div>
    <div class="FAIRsepdown"></div>
    <div class="FAIRsepdown"></div>

    <div class="row">
        
        <div class="col-lg-12">
        <h2 style="text-align: center;">Informationen zum Antikörpertest SARS-CoV-2 / Covid-19</h2>
        </div>
        <div class="col-lg-6">
        <div class="thumbnail">
        <div style="font-size: 45px; text-align: center; padding: 10px;">
        <span class="icon-lab"></span>
        </div>
        <div class="caption">
            <h3 style="text-align: center;">Was ist ein Antikörpertest?</h3>
            <p>Der Antikörpertest gibt einen Anhaltspunkt über die Anwesenheit und Menge der Antikörper im Blut. Die Antikörperbildung ist Nachweis einer durchgemachten SARS-CoV-2 Infektion oder einer durch Impfung erworbenen Immunität gegen SARS-CoV-2.</p>
            <p>Der Körper bildet bei einer Infektion Abwehrstoffe, sogenannte Antikörper. Ein Antikörpertest untersucht im Blut des Patienten, ob er Antikörper gegen das Virus gebildet hat. Dies sind Eiweiß-, Zucker- oder Fett-Moleküle, die außen auf der Hülle von Viren oder Bakterien sitzen. </p>
            <p>Antikörper (Immunglobuline) werden grundsätzlich gegen alle körperfremden Stoffe gebildet, auf die unsere Immunzellen stoßen. Für eine möglichst genaue Aussage eines Antikörpertests konzentriert man sich auf hoch spezifische Antikörper, die gegen die erregertypischen Proteine gerichtet sind.</p>
        </div>
        </div>
        </div>
        <div class="col-lg-6">
        <div class="thumbnail">
        <div style="font-size: 45px; text-align: center; padding: 10px;">
        <span class="icon-chat"></span>
        </div>
        <div class="caption">
            <h3 style="text-align: center;">Fragen und Antworten zum Antikörper-Test</h3>
            <h4>1. Ab wann kann ich meinen Antikörper-Status bestimmen lassen?</h4>
            <p>Wir empfehlen eine Bestimmung der Antikörper frühestens 14 Tage nach der Infektion bzw. erfolgter letzter Impfung.</p>
            <h4>2. Wie wird das Testmaterial entnommen?</h4>
            <p>Grundsätzlich wird der Antikörpertest mittels Kapillarblut ausgewertet. Hierfür wird eine Lanzette an die Fingerkuppe gesetzt. Nach der Punktion der Haut wird ein Tropfen Blut mit einer Pipette entfernt und in die vorgesehene Testkartusche pipettiert. Das Ergebnis wird durch einen Farbumschlag angezeigt.</p>
            <h4>3. Wann erhalte ich mein Ergebnis?</h4>
            <p>Innerhalb von etwa 15-20 Minuten steht das Ergebnis fest. Sie erhalten das Ergebnis über Anwesenheit und Menge der neutralisierenden Antikörper vor Ort.</p>
            <h4>4. Was bedeutet das Ergebnis für mich? </h4>
            <p>Der sogenannte BAU/ml-Wert (BAU = binding antibody units) ist die Konzentration der neutralisierenden Antikörper im Blut. Hinweis von der Verpackung: Achtung das Ergebnis ist nur ein Hinweis, keine Garantie, zu den Abwehrkräften spielen weitere Fakrtoren eine Rolle</p>
            <p><b>Zur Orientierung:</b></p>
            <p>< 25 BAU/ml: negativer SARS-CoV2-NAb-Titer</p>
            <p>≥ 25 BAU/ml < 250 BAU/ml: geringer SARS-CoV2-NAb-Titer</p>
            <p>≥ 250 BAU/ml < 500 BAU/ml: mittlerer SARS-CoV2-NAb-Titer</p>
            <p>≥ 500 BAU/ml: hoher SARS-CoV2-NAb-Titer</p>
            <p>> 2982 BAU/ml: maximal anzuzeigender Wert</p>
            
        </div>
        </div>
        </div>

        </div>
    </div>
    <div class="FAIRsepdown"></div>';
}



if($GLOBALS['FLAG_MODE_MAIN'] == 1) {
    echo '
    <div class="alert alert-info" role="alert">
        <h2>Coronavirus SARS-CoV-2 Testung</h2>
        <h4>Wir bieten für Sie:</h4>

        <div class="row">
        <div class="col-sm-4 col-xs-12 main-link-page main-link-page_2" onclick="window.location=\'?s=ag#calendar\'">
            <div class="header_icon">
            <img src="img/icon/rapid_test.svg" style="display: block; margin-left: auto; margin-right: auto; width: 30%;"></img>
            <div class="FAIRsep"></div>
            <div class="caption center_text">
            <h4>Antigen-Schnelltest *1)</h4>';
            echo '<h5>*1) aktuell kostenfrei</h5>';
            //echo '<h5>*1) kostenfrei für Bürgertest-Berechtigte, sonst kostenpflichtig für 20 €</h5>';
            echo '</div>
            </div>
        </div>
        <div class="col-sm-4 col-xs-12 main-link-page main-link-page_2" onclick="window.location=\'?s=pcr#calendar\'">
            <div class="header_icon">
            <img src="img/icon/certified_result.svg" style="display: block; margin-left: auto; margin-right: auto; width: 30%;"></img>
            <div class="FAIRsep"></div>
            <div class="caption center_text">
                <h4>PCR-Test *2)</h4>
                <h5>*2) kostenfrei für angeordnete Tests, sonst kostenpflichtig für 70 €</h5>
            </div>
            </div>
        </div>
        <div class="col-sm-4 col-xs-12 main-link-page main-link-page_2" onclick="window.location=\'registration/business.php\'">
            <div class="header_icon">
            <img src="img/icon/pay.svg" style="display: block; margin-left: auto; margin-right: auto; width: 30%;"></img>
            <div class="FAIRsep"></div>
            <div class="caption center_text">
            <h4>Kostenpflichtige Firmen-Testung *3)</h4>
            <h5>*3) wenden Sie sich für ein Angebot an das '.$name_facility.' <a href="mailto:'.$email_facility.'">'.$email_facility.'</a></h5>
            </div>
            </div>
        </div>

        </div>

        <div class="FAIRsepdown"></div>
        <div class="header_icon">
            <div class="caption">
            <h3>Informationen zum Antigen-Schnelltest</h3>
            <p>&nbsp;</p>
            <h4><b>Dieser Test ist für Sie kostenfrei, wenn Sie zu einer der folgenden Personengruppen gehören:</b></h4>';
            echo '<h4>Im Rahmen der kostenfreien Bürger-Testung hat jede*r Bürger*in mindestens einmal pro Woche Anspruch auf einen Antigen-Schnelltest (seit 13.11.2021)</h4>';
            /* echo '<h4>A) Im Rahmen der kostenfreien Bürger-Testung hat jede*r Bürger*in mindestens einmal pro Woche Anspruch (ab 13.11.2021)</h4>
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
            <p>Weitere Einzelfälle müssen aktuell im jeweiligen Fall bewertet werden. Rückfragen hierzu frühzeitig an <a href="mailto:'.$email_facility.'">'.$email_facility.'</a>.</p>'; */

            echo '</div>
        </div>

        <div class="FAIRsepdown"></div>
        <p>Bei Fragen können Sie sich an das Personal vor Ort wenden.</p>
        
        <p>Bitte erscheinen Sie nur, wenn Sie frei von den typischen Symptomen, wie Fieber, trockenem Husten oder plötzlichem Verlust des Geruchs- oder Geschmackssinnes sind.</p>
        <div class="FAIRsepdown"></div>
        <div class="FAIRsep"></div>
    </div>
    <div class="FAIRsepdown" id="calendar"></div>


    <div class="row header_icon_main">

        <div class="col-sm-2 col-xs-6">
            <div class="header_icon">
            <img src="img/icon/cal_time.svg" style="display: block; margin-left: auto; margin-right: auto; width: 30%;"></img>
                
            <div class="caption center_text">
            <h5>Termin finden</h5><h5><span class="text-sm">(nur bei Voranmeldung)</span></h5>
            </div>
            </div>
        </div>
        <div class="col-sm-2 col-xs-6">
            <div class="header_icon">
            <img src="img/icon/mask.svg" style="display: block; margin-left: auto; margin-right: auto; width: 30%;"></img>
                
            <div class="caption center_text">
                <h5>Mit Maske erscheinen</h5>
                <h5><span class="text-sm">&nbsp;</span></h5>
            </div>
            </div>
        </div>
        <div class="col-sm-2 col-xs-6">
            <div class="header_icon">
            <img src="img/icon/qr_1.svg" style="display: block; margin-left: auto; margin-right: auto; width: 30%;"></img>
                
            <div class="caption center_text">
            <h5>Ticket vorzeigen</h5><h5><span class="text-sm">(nur bei Voranmeldung)</span></h5>
            </div>
            </div>
        </div>
        <div class="col-sm-2 col-xs-6">
            <div class="header_icon">
            <img src="img/icon/swab_test.svg" style="display: block; margin-left: auto; margin-right: auto; width: 30%;"></img>
                
            <div class="caption center_text">
            <h5>Nasenabstrich</h5>
            <h5><span class="text-sm">&nbsp;</span></h5>
            </div>
            </div>
        </div>
        <div class="col-sm-2 col-xs-6">
            <div class="header_icon">
            <img src="img/icon/wait_result.svg" style="display: block; margin-left: auto; margin-right: auto; width: 30%;"></img>
                
            <div class="caption center_text">
            <h5>Etwa 20-30 min. warten</h5><h5><span class="text-sm">(PCR-Test etwa 48 h ohne Rechtsanspruch)</span></h5>
            </div>
            </div>
        </div>
        <div class="col-sm-2 col-xs-6">
            <div class="header_icon">
            <img src="img/icon/result.svg" style="display: block; margin-left: auto; margin-right: auto; width: 30%;"></img>
                
            <div class="caption center_text">
            <h5>Ergebnis digital abrufen</h5><h5><span class="text-sm">Neu: Auch mit Ihrer Corona-Warn-App</span></h5>
            </div>
            </div>
        </div>

    </div>
    
    <div class="FAIRsepdown"></div>
    <div class="row">
        <div class="col-sm-12">
            <div class="alert alert-info" role="alert">
            <div style="text-align: center;">
                <h3><b>Sie haben Fragen?</b></h3>
                <p>Schreiben Sie uns an <a href="mailto:'.$email_facility.'?subject=Fragen - '.$name_facility.'">'.$email_facility.'</a></p>
            </div>
        </div>
    </div>
    ';
} elseif($GLOBALS['FLAG_MODE_MAIN'] == 2) {
    
    echo '
    <div class="row header_icon_main">

    <div class="col-sm-4 col-xs-4">
        <div class="header_icon">
        <img src="img/icon/cal_time.svg" style="display: block; margin-left: auto; margin-right: auto; width: 30%;"></img>
            
        <div class="caption center_text">
        <h5>Termin finden</h5>
        </div>
        </div>
    </div>
    <div class="col-sm-4 col-xs-4">
        <div class="header_icon">
        <img src="img/icon/mask.svg" style="display: block; margin-left: auto; margin-right: auto; width: 30%;"></img>
            
        <div class="caption center_text">
            <h5>Mit Maske erscheinen</h5>
            <h5><span class="text-sm">&nbsp;</span></h5>
        </div>
        </div>
    </div>
    <div class="col-sm-4 col-xs-4">
        <div class="header_icon">
        <img src="img/icon/qr_1.svg" style="display: block; margin-left: auto; margin-right: auto; width: 30%;"></img>
            
        <div class="caption center_text">
        <h5>Impfunterlagen mitbringen</h5>
        </div>
        </div>
    </div>

    </div>';
    echo '
    <div class="FAIRsepdown"></div>
    <div class="row">
        <div class="col-lg-4 col-sm-12">
            <div class="alert alert-info" role="alert">
                <div style="text-align: center;">
                    <h3><b>Sie haben Fragen?</b></h3>
                    <p>Schreiben Sie uns an <p><a href="https://portal-civ.ekom21.de/civ.public/start.html?oe=00.00.LKOW&mode=cc&cc_key=Impfhotline">Kontaktformular</a></p>
                    
                    <h3><b>Zentrale Service-Hotline</b></h3>
                    <h4><b><a href="tel:+496062703346">06062 70 33 46</a></b></h4>
                    <p><b>Montag bis Freitag von 8:00 bis 16:00 Uhr</b></p>
                </div>
            </div>
        </div>';
    echo '
        <div class="col-lg-4 col-sm-6">
            <div class="alert alert-warning" role="alert">
            <div class="caption center_text">
            <h3>Auffrischungsimpfung / Booster</h3>
            <p>Eine Auffrischungsimpfung / Booster-Impfung ist frühestens fünf Monate nach vollständiger Impfung möglich!</p>
            <h3>Impfstoff Spikevax von Moderna</h3>
            <p>Der Impfstoff Spikevax von Moderna ist nur für Personen ab 30 Jahren zulässig - bitte beachten Sie dies bei der Terminwahl. Ein Wechsel auf einen anderen Impfstoff vor Ort ist nicht möglich.</p>
            </div>
            </div>
        </div>
        <div class="col-lg-4 col-sm-6">
            <div class="alert alert-info" role="alert">
            <div class="caption center_text">
            <h3>Parkmöglichkeiten für das Impfzentrum Erbach</h3>
            <p>Auf dem Parkdeck am Lustgarten sind Parkmöglichkeiten, die Sie bitte nutzen können.</p>
            </div>
            </div>
        </div>
    </div>
    <div class="FAIRsepdown"></div>
    ';
} elseif($GLOBALS['FLAG_MODE_MAIN'] == 3) {
    echo '
    <div class="FAIRsepdown"></div>
    <div class="row header_icon_main">

    <div class="col-sm-4 col-xs-4">
        <div class="header_icon">
        <img src="img/icon/cal_time.svg" style="display: block; margin-left: auto; margin-right: auto; width: 30%;"></img>
            
        <div class="caption center_text">
        <h5>Termin finden</h5>
        </div>
        </div>
    </div>
    <div class="col-sm-4 col-xs-4">
        <div class="header_icon">
        <img src="img/icon/mask.svg" style="display: block; margin-left: auto; margin-right: auto; width: 30%;"></img>
            
        <div class="caption center_text">
            <h5>Mit Maske erscheinen</h5>
            <h5><span class="text-sm">&nbsp;</span></h5>
        </div>
        </div>
    </div>
    <div class="col-sm-4 col-xs-4">
        <div class="header_icon">
        <img src="img/icon/pay.svg" style="display: block; margin-left: auto; margin-right: auto; width: 30%;"></img>
            
        <div class="caption center_text">
            <h5>Kosten: 30 €</h5>
            <h5><span class="text-sm">Bezahlung vor Ort</span></h5>
        </div>
        </div>
    </div>

    </div>
    <div class="FAIRsepdown"></div>
    <div class="row">
        <div class="col-sm-6">
            <div class="alert alert-info" role="alert">
                <div style="text-align: center;">
                    <h3><b>Sie haben Fragen?</b></h3>
                    <p>Schreiben Sie uns an <a href="mailto:'.$email_facility.'?subject=Fragen - '.$name_facility.'">'.$email_facility.'</a></p>
                </div>
            </div>
        </div>
        <div class="col-sm-6">
            <div class="alert alert-warning" role="alert">
            <h3>Parkmöglichkeiten</h3>
            <p>Am Wiesenmarktgelände gibt es ausreichend Parkmöglichkeiten, die Sie bitte nutzen können.</p>
            </div>
        </div>
    </div>
    <div class="FAIRsepdown"></div>
    ';
}

echo '
<div class="FAIRsepdown"></div>
<div class="FAIRsepdown"></div>


<div class="row">
    
    <div class="col-sm-12">
    <h2 style="text-align: center;">SARS-CoV-2 '.$doing_facility.' - Termine und Orte im Odenwaldkreis</h2>';
    if($GLOBALS['FLAG_MODE_MAIN'] == 2) {
        echo '<p style="text-align: center;">
        Auch die niedergelassenen Ärztinnen und Ärzte bieten SARS-CoV-2 Schutzimpfungen an. Für eine dortige Impfung wenden Sie sich bitte an Ihre Ärztin oder Ihren Arzt.
        </p>';
    }

    echo '
    </div>
    <div class="col-sm-12"><div class="card">';




if(!$GLOBALS['FLAG_SHUTDOWN_MAIN']) {
    if($GLOBALS['FLAG_MODE_MAIN'] == 1) {
        // Show table of available dates
        if(isset($_GET['s']) && $_GET['s']=='pcr') {
            echo '
            <ul class="nav nav-pills" role="tablist">
            <li role="presentation" class=""><a href="?s=ag#calendar">Antigen-Schnelltest</a></li>
            <li role="presentation" class="active"><a href="?s=pcr#calendar">PCR-Test</a></li>
            <li role="presentation" class=""><a href="registration/business.php">Firmenanmeldung</a></li>
            </ul>
            ';
            $calendar=H_build_table_testdates2('pcr');
        } elseif(isset($_GET['s']) && $_GET['s']=='ag') {
            echo '
            <ul class="nav nav-pills" role="tablist">
            <li role="presentation" class="active"><a href="?s=ag#calendar">Antigen-Schnelltest</a></li>
            <li role="presentation" class=""><a href="?s=pcr#calendar">PCR-Test</a></li>
            <li role="presentation" class=""><a href="registration/business.php">Firmenanmeldung</a></li>
            </ul>
            ';
            $calendar=H_build_table_testdates2('ag');
        } else {
            echo '<h4>Für die Kalenderansicht müssen Sie zuerst eine Test-Art wählen</h4>
            <ul class="nav nav-pills" role="tablist">
            <li role="presentation" class=""><a href="?s=ag#calendar">Antigen-Schnelltest</a></li>
            <li role="presentation" class=""><a href="?s=pcr#calendar">PCR-Test</a></li>
            <li role="presentation" class=""><a href="registration/business.php">Firmenanmeldung</a></li>
            </ul>
            ';
        }
    }
    if($GLOBALS['FLAG_MODE_MAIN'] == 1) {
        if(isset($_GET['s']) && ($_GET['s']=='pcr' || $_GET['s']=='ag' ) ) {
            //large display
            echo '<div class="calendar-large">';
            echo $calendar[0];
            echo '</div>';
            // small display
            echo '<div class="calendar-small">
            <div class="cal-day-head-red"><i>Für rote Teststationen ist eine Voranmeldung und Terminbuchung empfohlen - bitte einen Termin wählen</i></div>
            <div class="cal-day-head-blue"><i>Für blaue Teststationen ist eine Voranmeldung und Terminbuchung erforderlich - bitte einen Termin wählen</i></div>
            <div class="cal-day-head-yellow"><i>Für gelbe Teststationen ist keine Terminbuchung notwendig, eine Voranmeldung Ihrer Daten kann gerne gemacht werden, dann geht es vor Ort schneller - bitte dafür einen Termin wählen</i></div>
            ';
            foreach($calendar[1] as $i) {
                echo $i[0].$i[1];
            }
            echo '</div>';
        }
    } else {
        if($GLOBALS['FLAG_MODE_MAIN'] == 2) {
            $calendar=H_build_table_testdates2('vaccinate');
        } elseif($GLOBALS['FLAG_MODE_MAIN'] == 3) {
            $calendar=H_build_table_testdates2('antikoerper');
        }
        //large display
        echo '<div class="calendar-large" style="overflow-x: scroll;">';
        echo $calendar[0];
        echo '</div>';
        // small display
        echo '<div class="calendar-small">
        ';
        foreach($calendar[1] as $i) {
            echo $i[0].$i[1];
        }
        echo '</div>';
        echo '</div>';
    }


} else {
    echo '<div class="alert alert-danger" role="alert">
    <h3>Wartungsarbeiten</h3>
    <p>Derzeit finden Arbeiten an dieser Seite statt, der Kalender und die Terminbuchung stehen momentan nicht zur Verfügung. Bald geht es wieder weiter...wir bitten um etwas Geduld.</p>
    </div>
    <div class="alert alert-info" role="alert">
    <h2>Aktuelle Öffnungszeiten</h2>
    <h3>ohne Terminanmeldung derzeit</h3>
    <p></p>
    <h3>Impfzentrum Erbach</h3>
    <p>derzeit keine Informationen zu Impfmöglichkeiten
    </p>
    <p></p>
    <h3>Testzentrum Erbach</h3>
    <p>Montag bis Freitag                                         6-20 Uhr
    <br>
    Samstag, Sonntag und Feiertag                 9-19 Uhr
    </p>
    <p></p>
    <h3>Testzentrum Beerfelden </h3>
    <p>Montag bis Freitag                                         16-20 Uhr 
    <br>
    Montag, Mittwoch, Freitag                         6-10 Uhr 
    <br>
    Samstag, Sonntag, Feiertag                         9-19 Uhr 
    </p>
    <p></p>
    <h3>Testzentrum Reichelsheim </h3>
    <p>Dienstag und Donnerstag                            6-10 Uhr und 16-20 Uhr 
    <br>
    Sonntag                                                              9-19 Uhr 
    </p>
    <p></p>
    <h3>PCR Test im Testzentrum Erbach </h3>
    <p>Montag bis Freitag                                         8:30 – 09:30 Uhr und
    <br>
    Montag bis Donnerstag                                18:30 – 19:30 Uhr 
    </p>
    <p></p>
    <div class="FAIRsepdown"></div>
    <div class="FAIRsep"></div>
</div>';
}

if($GLOBALS['FLAG_MODE_MAIN'] == 1) {
    // Testzentrum Infos
    echo '
        </div></div>

        </div>
    </div>
    <div class="FAIRsepdown"></div>
    <div class="FAIRsepdown"></div>

    <div class="row">
        
        <div class="col-sm-12">
        <h2 style="text-align: center;">Informationen zu Ihrem Testergebnis</h2>
        </div>
        <div class="col-sm-6">
        <div class="thumbnail">
        <img style="height:231px; object-fit: contain;" src="img/covid-19-5057462_640.jpg" alt="">
        <div class="caption">
            <h3>Allgemeine Info des Gesundheitsamtes Odenwaldkreis</h3>
            <br>
            <p><a href="download/Informationen_Gesundheitsamt.pdf" class="btn btn-primary" role="button">Download PDF</a></p>
        </div>
        </div>
        </div>
        <div class="col-sm-6">
        <div class="thumbnail">
        <img style="height:231px; object-fit: contain;" src="img/test-tube-5065426_1280.jpg" alt="">
        <div class="caption">
            <h3>Positiv getestet?</h3>
            <p>Sie wurden positiv getestet, dann haben wir hier einige Informationen für Sie vom Hessischen Ministerium für Soziales und Integration:</p>
            <p><a href="download/HMSI-Informationen.pdf" class="btn btn-primary" role="button">Download PDF</a></p>
        </div>
        </div>
        </div>

        </div>
    </div>';
}

echo '
<div class="FAIRsepdown"></div>
<div class="FAIRsepdown"></div>
<div class="row">
    <div class="col-sm-12">
    <h3>Für die Teams des DRK-Kreisverband Odenwaldkreis</h3>
    </div>
    <div class="col-sm-4">
        <div class="list-group">
            <a class="list-group-item list-group-item-action list-group-item-FAIR" id="module-r1" href="zentral/index.php">MA-Portal (Intern)</a>
        </div>
    </div>';
    if($GLOBALS['FLAG_MODE_MAIN'] == 2) {
        echo '<div class="col-sm-4">
            <div class="list-group">
                <a class="list-group-item list-group-item-action list-group-item-FAIR" id="module-r1" href="registration/business.php">Firmenanmeldung (Intern)</a>
            </div>
        </div>';
    }


echo '</div>';


// Print html content part C
echo $GLOBALS['G_html_main_right_c'];
// Print html footer
echo $GLOBALS['G_html_footer'];

?>
