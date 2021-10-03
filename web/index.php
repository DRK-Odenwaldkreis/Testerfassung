<?php

/* **************

Websystem für das Testzentrum DRK Odenwaldkreis
Author: Marc S. Duchene
March 2021

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
} else {
    $name_facility='Impfzentrum';
    $doing_facility='Impfungen';
    $email_facility='testzentrum@drk-odenwaldkreis.de';
    $logo_facility='impfzentrum.jpg';

}

echo '
<div class="row">

    <div class="col-sm-6" style="padding:10px;">
        <img src="img/'.$logo_facility.'" style="display: block; margin-left: auto; margin-right: auto; width: 65%; border: solid 1px #5a482d;"></img>
    </div>

    <div class="col-sm-6">
        <div style="text-align: center;">
            <h2>Covid-19 '.$name_facility.' Odenwaldkreis</h2>
            <h3>Deutsches Rotes Kreuz - Kreisverband Odenwaldkreis e. V.</h3>
        </div>
    </div>
</div>
';



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
            <li><a href="https://portal-civ-qs.ekom21.de/civ-qs.public/start.html?oe=00.00.LKOW&mode=cc&cc_key=CoronaImpfzentrumAstra" target="_blank">Impfstoffauswahl für die zweite Impfung (nur für Personen, die bei der ersten Impfung den Wirkstoff von AstraZeneca erhalten haben, aber noch unter 60 Jahre alt sind)</a></li>
            <li><a href="https://corona.odenwaldkreis.de/wp-content/uploads/2021/05/Datenschutz-Info-Stand-15.03.2021.pdf" target="_blank">Information über die Verarbeitung personenbezogener Daten (Datenschutz) </a></li>
            </ul></p>
            <h3 style="text-align: center;">Videos mit Informationen in Gebärdensprache</h3>
            <p><ul>
            <li><a href="https://www.youtube.com/watch?v=tkidRQrbM5w&feature=youtu.be" target="_blank">Aufklärungsmerkblatt mRNA-Impfstoff</a></li>
            <li><a href="https://www.youtube.com/watch?v=u0kGKOe5Xco&feature=youtu.be" target="_blank">Anamnese und Einwilligung mRNA-Impfstoff</a></li>
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
            <h4>Kostenloser Bürgertest / Antigen-Schnelltest</h4>
            </div>
            </div>
        </div>
        <div class="col-sm-4 col-xs-12 main-link-page main-link-page_2" onclick="window.location=\'?s=pcr#calendar\'">
            <div class="header_icon">
            <img src="img/icon/certified_result.svg" style="display: block; margin-left: auto; margin-right: auto; width: 30%;"></img>
            <div class="FAIRsep"></div>
            <div class="caption center_text">
                <h4>PCR-Test *)</h4>
                <h5>*) kostenfrei für angeordnete Tests, sonst kostenpflichtig für 70 €</h5>
            </div>
            </div>
        </div>
        <div class="col-sm-4 col-xs-12 main-link-page main-link-page_2" onclick="window.location=\'registration/business.php\'">
            <div class="header_icon">
            <img src="img/icon/pay.svg" style="display: block; margin-left: auto; margin-right: auto; width: 30%;"></img>
            <div class="FAIRsep"></div>
            <div class="caption center_text">
            <h4>Kostenpflichtige Firmen-Testung **)</h4>
            <h5>**) wenden Sie sich für ein Angebot an das '.$name_facility.' <a href="mailto:'.$email_facility.'">'.$email_facility.'</a></h5>
            </div>
            </div>
        </div>

        </div>

        <div class="FAIRsepdown"></div>
        <p>Bei Fragen können Sie sich an das Personal vor Ort wenden.</p>
        
        <p>Bitte erscheinen Sie nur, wenn Sie frei von den typischen Symptomen, wie Fieber, trockenem Husten oder plötzlichem Verlust des Geruchs- oder Geschmackssinnes sind.</p>
        <div class="FAIRsepdown"></div>
        <div class="FAIRsep"></div>
    </div>
    <div class="FAIRsepdown" id="calendar"></div>

    <div class="row">

        <div class="col-sm-12">
            <div class="alert alert-warning" role="alert">
            <h3>Sie haben Fragen?</h3>
            <p>Schreiben Sie uns an <a href="mailto:'.$email_facility.'?subject=Fragen - '.$name_facility.'">'.$email_facility.'</a></p>
            </div>
        </div>
    </div>
    <div class="FAIRsepdown"></div>

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
            <h5>Etwa 20-30 min. warten</h5><h5><span class="text-sm">(PCR-Test etwa 1-2 Tage)</span></h5>
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

    </div>';
} else {
    echo '
    <div class="row">
        <div class="col-sm-12">
            <div class="alert alert-warning" role="alert">
            <h3>Sie haben Fragen?</h3>
            <p>Schreiben Sie uns an <a href="mailto:'.$email_facility.'?subject=Fragen - '.$name_facility.'">'.$email_facility.'</a></p>
            </div>
        </div>
    </div>
    <div class="FAIRsepdown"></div>
    <div class="row header_icon_main">

    <div class="col-sm-4 col-xs-4">
        <div class="header_icon">
        <img src="img/icon/cal_time.svg" style="display: block; margin-left: auto; margin-right: auto; width: 30%;"></img>
            
        <div class="caption center_text">
        <h5>Termin finden</h5><h5><span class="text-sm">bis max. 2 Wochen im voraus</span></h5>
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
        <h5>Impfausweis mitbringen</h5>
        </div>
        </div>
    </div>

    </div>
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
        $calendar=H_build_table_testdates2('vaccinate');
        //large display
        echo '<div class="calendar-large">';
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
            <p><a href="download/2021-03-11Anhang_Gesundheitsamt.pdf" class="btn btn-primary" role="button">Download PDF</a></p>
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
    <div class="col-sm-4">
        <div class="list-group">
            <h3>Für die Teams des DRK-Kreisverband Odenwaldkreis</h3>
            <a class="list-group-item list-group-item-action list-group-item-FAIR" id="module-r1" href="zentral/index.php">MA-Portal (Intern)</a>
        </div>
    </div>
</div>';


// Print html content part C
echo $GLOBALS['G_html_main_right_c'];
// Print html footer
echo $GLOBALS['G_html_footer'];

?>
