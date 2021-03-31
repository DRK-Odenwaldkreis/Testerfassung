<?php

/* **************

Websystem für das Testzentrum DRK Odenwaldkreis
Author: Marc S. Duchene
March 2021

** ************** */


// Include functions
include_once 'preload.php';
include_once 'menu.php';
include_once 'registration/auth.php';
include_once 'registration/tools.php';

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


<div class="row">
    
    <div class="col-sm-12">
    <h2 style="text-align: center;">Covid-19 Schnelltest - Termine und Orte im Landkreis Odenwald</h2>
    </div>
    <div class="col-sm-12"><div class="card">
<?php

  // Show table of available dates
  echo H_build_table_testdates();

?>
    </div></div>

    </div>
</div>

<div class="alert alert-info" role="alert">
    <h3>Covid-19 Testung</h3>
    Bei Fragen können Sie sich an das Personal vor Ort wenden.
    <br>
    Bitte erscheinen Sie nur wenn Sie frei von den typischen Symptomen, wie Fieber, trockenem Husten oder plötzlichem Verlust des Geruchs- oder Geschmackssinnes sind.
    <div class="FAIRsepdown"></div>
    <div class="FAIRsep"></div>
</div>

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
