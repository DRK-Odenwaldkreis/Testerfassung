<?php

/* **************

Websystem für das Testzentrum DRK Odenwaldkreis
Author: Marc S. Duchene
March 2021

** ************** */


// Include functions
include_once 'admin01.php';
include_once 'preload.php';
include_once 'menu.php';
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

<div class="row header_icon_main">

    <div class="col-sm-2 col-xs-4">
        <div class="header_icon">
        <img src="img/icon/cal_time.svg" style="display: block; margin-left: auto; margin-right: auto; width: 30%;"></img>
            
        <div class="caption center_text">
        <h5>Termin finden<br><span class="text-sm">(nur bei Voranmeldung)</span></h5>
        </div>
        </div>
    </div>
    <div class="col-sm-2 col-xs-4">
        <div class="header_icon">
        <img src="img/icon/mask.svg" style="display: block; margin-left: auto; margin-right: auto; width: 30%;"></img>
            
        <div class="caption center_text">
            <h5>Mit Maske erscheinen</h5>
        </div>
        </div>
    </div>
    <div class="col-sm-2 col-xs-4">
        <div class="header_icon">
        <img src="img/icon/qr_1.svg" style="display: block; margin-left: auto; margin-right: auto; width: 30%;"></img>
            
        <div class="caption center_text">
        <h5>Ticket vorzeigen<br><span class="text-sm">(nur bei Voranmeldung)</span></h5>
        </div>
        </div>
    </div>
    <div class="col-sm-2 col-xs-4">
        <div class="header_icon">
        <img src="img/icon/swab_test.svg" style="display: block; margin-left: auto; margin-right: auto; width: 30%;"></img>
            
        <div class="caption center_text">
        <h5>Nasenabstrich</h5>
        </div>
        </div>
    </div>
    <div class="col-sm-2 col-xs-4">
        <div class="header_icon">
        <img src="img/icon/wait_result.svg" style="display: block; margin-left: auto; margin-right: auto; width: 30%;"></img>
            
        <div class="caption center_text">
        <h5>Etwa 20 min. warten</h5>
        </div>
        </div>
    </div>
    <div class="col-sm-2 col-xs-4">
        <div class="header_icon">
        <img src="img/icon/result.svg" style="display: block; margin-left: auto; margin-right: auto; width: 30%;"></img>
            
        <div class="caption center_text">
        <h5>Ergebnis digital abrufen</h5>
        </div>
        </div>
    </div>

</div>

<div class="alert alert-info" role="alert">
    <h3>Covid-19 Testung</h3>
    Bei Fragen können Sie sich an das Personal vor Ort wenden.
    <br>
    Bitte erscheinen Sie nur, wenn Sie frei von den typischen Symptomen, wie Fieber, trockenem Husten oder plötzlichem Verlust des Geruchs- oder Geschmackssinnes sind.
    <div class="FAIRsepdown"></div>
    <div class="FAIRsep"></div>
</div>
<div class="FAIRsepdown"></div>
<div class="FAIRsepdown"></div>


<div class="row">
    
    <div class="col-sm-12">
    <h2 style="text-align: center;">Covid-19 Schnelltest - Termine und Orte im Landkreis Odenwald</h2>
    </div>
    <div class="col-sm-12"><div class="card">
<?php
if(!$GLOBALS['FLAG_SHUTDOWN_MAIN']) {

  // Show table of available dates
  echo H_build_table_testdates2('');

  echo '<div class="row">
    <div class="col-sm-4">
        <div class="list-group">
            <a class="btn btn-primary" id="module-b2b" href="registration/business.php">Firmenanmeldung</a>
        </div>
    </div>
</div>';
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
