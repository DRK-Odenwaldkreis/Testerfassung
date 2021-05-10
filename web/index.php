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

?>



<div class="row">

    <div class="col-sm-6">
        <img src="img/logo.png" style="display: block; margin-left: auto; margin-right: auto; width: 65%;"></img>
    </div>

    <div class="col-sm-6">
        <div style="text-align: center;">
            <h2>Covid-19 Testzentrum Odenwaldkreis</h2>
            <h3>Deutsches Rotes Kreuz - Kreisverband Odenwaldkreis e. V.</h3>
        </div>
    </div>
</div>

<div class="alert alert-info" role="alert">
    <h2>Coronavirus SARS-CoV-2 Testung</h2>
    <h4>Wir bieten für Sie:</h4>

    <div class="row">
    <div class="col-sm-4 col-xs-12 main-link-page" onclick="window.location='?s=ag#calendar'">
        <div class="header_icon">
        <img src="img/icon/rapid_test.svg" style="display: block; margin-left: auto; margin-right: auto; width: 30%;"></img>
        <div class="FAIRsep"></div>
        <div class="caption center_text">
        <h4>Kostenloser Bürgertest / Antigen-Schnelltest</h4>
        </div>
        </div>
    </div>
    <div class="col-sm-4 col-xs-12 main-link-page" onclick="window.location='?s=pcr#calendar'">
        <div class="header_icon">
        <img src="img/icon/certified_result.svg" style="display: block; margin-left: auto; margin-right: auto; width: 30%;"></img>
        <div class="FAIRsep"></div>
        <div class="caption center_text">
            <h4>PCR-Test *)</h4>
            <h5>*) kostenfrei für angeordnete Tests, sonst kostenpflichtig für 70 €</h5>
        </div>
        </div>
    </div>
    <div class="col-sm-4 col-xs-12 main-link-page" onclick="window.location='registration/business.php'">
        <div class="header_icon">
        <img src="img/icon/pay.svg" style="display: block; margin-left: auto; margin-right: auto; width: 30%;"></img>
        <div class="FAIRsep"></div>
        <div class="caption center_text">
        <h4>Kostenpflichtige Firmen-Testung **)</h4>
        <h5>**) wenden Sie sich für ein Angebot an das Testzentrum <a href="mailto:testzentrum@drk-odenwaldkreis.de">testzentrum@drk-odenwaldkreis.de</a></h5>
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
<div class="FAIRsepdown" id="calendar"></div><div class="FAIRsepdown"></div>
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

</div>


<div class="FAIRsepdown"></div>
<div class="FAIRsepdown"></div>


<div class="row">
    
    <div class="col-sm-12">
    <h2 style="text-align: center;">SARS-CoV-2 Testungen - Termine und Orte im Odenwaldkreis</h2>
    </div>
    <div class="col-sm-12"><div class="card">
<?php
if(!$GLOBALS['FLAG_SHUTDOWN_MAIN']) {

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
    if(isset($_GET['s']) && ($_GET['s']=='pcr' || $_GET['s']=='ag' ) ) {
        //large display
        echo '<div class="calendar-large">';
        echo $calendar[0];
        echo '</div>';
        // small display
        echo '<div class="calendar-small">
        <div class="cal-day-head-yellow"><i>Für gelbe Teststationen ist eine Voranmeldung und Terminbuchung empfohlen - bitte einen Termin wählen</i></div>
        <div class="cal-day-head-red"><i>Für rote Teststationen ist eine Voranmeldung und Terminbuchung erforderlich - bitte einen Termin wählen</i></div>
        <div class="cal-day-head-blue"><i>Für blaue Teststationen ist keine Terminbuchung notwendig, eine Voranmeldung Ihrer Daten kann gerne gemacht werden, dann geht es vor Ort schneller - bitte dafür einen Termin wählen</i></div>
        ';
        foreach($calendar[1] as $i) {
            echo $i[0].$i[1];
        }
        echo '</div>';
    }


} else {
    echo '<div class="alert alert-danger" role="alert">
    <h3>Wartungsarbeiten</h3>
    <p>Derzeit finden Arbeiten an dieser Seite statt, der Kalender und die Terminbuchung stehen momentan nicht zur Verfügung. Bald geht es wieder weiter...wir bitten um etwas Geduld.</p>
    <div class="FAIRsepdown"></div>
    <div class="FAIRsep"></div>
</div>';
}

?>
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
</div>
<div class="FAIRsepdown"></div>
<div class="FAIRsepdown"></div>
<div class="row">
    <div class="col-sm-4">
        <div class="list-group">
            <h3>Für die Teams des DRK-Kreisverband Odenwaldkreis</h3>
            <a class="list-group-item list-group-item-action list-group-item-FAIR" id="module-r1" href="zentral/index.php">Testerfassung (Intern)</a>
        </div>
    </div>
</div>

<?php
// Print html content part C
echo $GLOBALS['G_html_main_right_c'];
// Print html footer
echo $GLOBALS['G_html_footer'];

?>
