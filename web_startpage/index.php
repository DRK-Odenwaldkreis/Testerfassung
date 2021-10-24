<?php

/* **************

Websystem für das Impf- und Testzentrum DRK Odenwaldkreis
Author: Marc S. Duchene
September 2021

** ************** */


// Include functions
include_once 'admin01.php';
include_once 'menu.php';
//$GLOBALS['FLAG_SHUTDOWN_MAIN']=false;
if(!$GLOBALS['FLAG_SHUTDOWN_MAIN']) {
    include_once '../registration/auth.php';
    include_once '../registration/tools.php';
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

    <div class="col-sm-12">
        <div style="text-align: center;">
            <h2>Covid-19 Impf- und Testzentrum Odenwaldkreis</h2>
            <h3>Deutsches Rotes Kreuz - Kreisverband Odenwaldkreis e. V.</h3>
        </div>
    </div>
</div>


<div class="alert alert-info" role="alert">
    <h2>Coronavirus SARS-CoV-2 Impfungen und Testungen</h2>
    <h4>Wir bieten für Sie:</h4>

    <div class="row">
    <div class="col-md-4 col-xs-12 main-link-page">
        <div class="header_icon">
<?php
    if(!$GLOBALS['FLAG_SHUTDOWN_VACCINATE']) {
        echo '<img src="img/impfzentrum.jpg" style="display: block; margin-left: auto; margin-right: auto; width: 50%; border: solid 1px #5a482d; cursor: pointer;" onclick="window.location=\'https://www.impfzentrum-odw.de\'"></img>';
    } else {
        echo '<img src="img/impfzentrum.jpg" style="display: block; margin-left: auto; margin-right: auto; width: 50%; border: solid 1px #5a482d;"></img>';
    }
?>
        
        <div class="FAIRsep"></div>
        <div class="caption center_text">
            <h4><b>Impfungen</b></h4>
<?php
    if(!$GLOBALS['FLAG_SHUTDOWN_VACCINATE']) {
        echo '<p><a class="btn btn-primary btn-lg" href="https://www.impfzentrum-odw.de" role="button">Zur Terminvergabe</a></p>';
    } else {
        echo '<p>Derzeit keine Anmeldung möglich</p>';
    }
?>
            
        </div>
        </div>
    </div>
    <div class="col-md-4 col-xs-12 main-link-page">
        <div class="header_icon">
<?php
    if(!$GLOBALS['FLAG_SHUTDOWN_TESTING']) {
        echo '<img src="img/testzentrum.png" style="display: block; margin-left: auto; margin-right: auto; width: 50%; border: solid 1px #5a482d; cursor: pointer;" onclick="window.location=\'https://www.testzentrum-odw.de\'"></img>';
    } else {
        echo '<img src="img/testzentrum.png" style="display: block; margin-left: auto; margin-right: auto; width: 50%; border: solid 1px #5a482d;"></img>';
    }
?>
        <div class="FAIRsep"></div>
        <div class="caption center_text">
            <h4><b>Antigentest (Schnelltest) / PCR-Test</b></h4>
<?php
    if(!$GLOBALS['FLAG_SHUTDOWN_TESTING']) {
        echo '<p><a class="btn btn-primary btn-lg" href="https://www.testzentrum-odw.de" role="button">Zur Terminvergabe</a></p>';
    } else {
        echo '<p>Derzeit keine Anmeldung möglich</p>';
    }
?>
        </div>
        </div>
    </div>

    <div class="col-md-4 col-xs-12 main-link-page">
        <div class="header_icon">
<?php
    if(!$GLOBALS['FLAG_SHUTDOWN_TESTING_ANTIKOERPER']) {
        echo '<img src="img/testzentrum.png" style="display: block; margin-left: auto; margin-right: auto; width: 50%; border: solid 1px #5a482d; cursor: pointer;" onclick="window.location=\'https://www.testzentrum-odw.de/antikoerper\'"></img>';
    } else {
        echo '<img src="img/testzentrum.png" style="display: block; margin-left: auto; margin-right: auto; width: 50%; border: solid 1px #5a482d;"></img>';
    }
?>
        <div class="FAIRsep"></div>
        <div class="caption center_text">
            <h4><b>Antikörpertest</b></h4>
<?php
    if(!$GLOBALS['FLAG_SHUTDOWN_TESTING_ANTIKOERPER']) {
        echo '<p><a class="btn btn-primary btn-lg" href="https://www.testzentrum-odw.de/antikoerper" role="button">Zur Terminvergabe</a></p>';
    } else {
        echo '<p>Derzeit keine Anmeldung möglich, geöffnet zu den Impfterminzeiten</p>';
    }
?>
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


<?php
// Print html content part C
echo $GLOBALS['G_html_main_right_c'];
// Print html footer
echo $GLOBALS['G_html_footer'];

?>
