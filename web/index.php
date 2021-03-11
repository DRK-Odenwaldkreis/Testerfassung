<?php

/* **************

Websystem für das Testzentrum DRK Odenwaldkreis
Author: Marc S. Duchene
March 2021

** ************** */


// Include functions
include_once 'menu.php';


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
    <h3>Covid-19 Testung</h3>
    Für einen Covid-19 Test ist derzeit eine Anmeldung vorab nicht notwendig. Bei Fragen können Sie sich an das Personal vor Ort wenden.
    <div class="FAIRsepdown"></div>
    <div class="FAIRsep"></div>
</div>



<div class="row">
    
    <div class="col-sm-12">
    <h2 style="text-align: center;">Liegt das Testergebnis schon vor...</h2>
    </div>
    <div class="col-sm-4">
    <div class="thumbnail">
      <img style="height:231px; object-fit: contain;" src="img/covid-19-5057462_640.jpg" alt="">
      <div class="caption">
        <h3>Negativ getestet?</h3>
        <p><a href="#" class="btn btn-primary" role="button">Download Informationen</a></p>
      </div>
    </div>
    </div>
    <div class="col-sm-4">
    <div class="thumbnail">
      <img style="height:231px; object-fit: contain;" src="img/test-tube-5065426_1280.jpg" alt="">
      <div class="caption">
        <h3>Positiv getestet?</h3>
        <p><a href="#" class="btn btn-primary" role="button">Download Informationen</a></p>
      </div>
    </div>
    </div>
    <div class="col-sm-4">
    <div class="thumbnail">
      <img style="height:231px; object-fit: contain;" src="img/laboratory-3827743_1280.jpg" alt="">
      <div class="caption">
        <h3>Mein Test war fehlerhaft?</h3>
        <p><a href="#" class="btn btn-primary" role="button">Download Informationen</a></p>
      </div>
    </div>

    </div>
</div>

<div class="row">
    <div class="col-sm-4">
        <div class="list-group">
            <h3>Für den DRK</h3>
            <a class="list-group-item list-group-item-action list-group-item-FAIR" id="module-r1" href="zentral/index.php">Test-System (intern)</a>
        </div>
    </div>
</div>

<?php
// Print html content part C
echo $GLOBALS['G_html_main_right_c'];
// Print html footer
echo $GLOBALS['G_html_footer'];

?>