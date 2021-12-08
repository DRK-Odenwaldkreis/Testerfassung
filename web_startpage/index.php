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


<div class="alert alert-info" role="alert" style="border:none">
    <h2>Coronavirus SARS-CoV-2 Impfungen und Testungen</h2>
    <h4>Wir bieten für Sie:</h4>
    <div class="FAIRsep"></div>

    <div class="row">
    <div class="col-md-4 col-xs-12 main-link-page">
        <div class="header_icon" style="border: 1px solid transparent;border-radius: 4px;border-color: #776346;padding-top:12px;">
        <div class="row">
        <div class="col-lg-5 col-xs-4">
<?php
    if(!$GLOBALS['FLAG_SHUTDOWN_VACCINATE']) {
        echo '<img src="img/impfzentrum.jpg" style="display: block; margin-left: auto; margin-right: auto; width: 100%; border: solid 1px #5a482d; cursor: pointer;" onclick="window.location=\'https://www.impfzentrum-odw.de\'"></img>';
    } else {
        echo '<img src="img/impfzentrum.jpg" style="display: block; margin-left: auto; margin-right: auto; width: 100%; border: solid 1px #5a482d;"></img>';
    }
?>
        </div>
        <div class="col-lg-3 col-xs-3">
        <img src="https://corona.odenwaldkreis.de/wp-content/uploads/2020/04/odenwaldkreislogo.jpg" style="display: block; margin-left: auto; margin-right: auto; width: 100%; border: solid 1px #5a482d;">
        </div>
        <div class="col-lg-4 col-xs-5">
        <div class="caption">
        <h5>im Auftrag des<br>Landkreis<br>Odenwaldkreis</h5>
        </div>
        </div>
        </div>

        <div class="FAIRsep"></div>
        <div class="caption center_text">
            <h3><b>Impfungen</b></h3>
<?php
    if(!$GLOBALS['FLAG_SHUTDOWN_VACCINATE']) {
        echo '<p><a class="btn btn-primary btn-lg" href="https://www.impfzentrum-odw.de" role="button">Zur Terminvergabe</a></p>';
    } else {
        echo '<p>Derzeit keine Anmeldung möglich</p>';
    }
?>
        <p>
            in Erbach<br>(teilweise auch in Reichelsheim, Beerfelden, Lützelbach, Höchst, ...)
        </p>

        </div>
        </div>
        <div class="FAIRsepdown"></div>
        <div style="text-align: center;">
            <p>Bei Fragen zu den Impfmöglichkeiten: <a href="https://portal-civ.ekom21.de/civ.public/start.html?oe=00.00.LKOW&mode=cc&cc_key=Impfhotline">Kontaktformular</a></p>
            <p><b>Montag bis Freitag von 8:00 bis 16:00 Uhr: <a href="tel:+496062703346">06062 70 33 46</a></b></p>

        </div>
        <div class="FAIRsepdown"></div>
    </div>
    <div class="col-md-4 col-xs-12 main-link-page">
        <div class="header_icon" style="border: 1px solid transparent;border-radius: 4px;border-color: #776346;padding-top:12px;">
<?php
    if(!$GLOBALS['FLAG_SHUTDOWN_TESTING']) {
        echo '<img src="img/testzentrum.png" style="display: block; margin-left: auto; margin-right: auto; width: 50%; border: solid 1px #5a482d; cursor: pointer;" onclick="window.location=\'https://www.testzentrum-odw.de\'"></img>';
    } else {
        echo '<img src="img/testzentrum.png" style="display: block; margin-left: auto; margin-right: auto; width: 50%; border: solid 1px #5a482d;"></img>';
    }
?>
        <div class="FAIRsep"></div>
        <div class="caption center_text">
            <h3><b>Antigentest (Schnelltest) / PCR-Test</b></h3>
<?php
    if(!$GLOBALS['FLAG_SHUTDOWN_TESTING']) {
        echo '<p><a class="btn btn-primary btn-lg" href="https://www.testzentrum-odw.de" role="button">Zur Terminvergabe</a></p>';
    } else {
        echo '<p>Derzeit keine Anmeldung möglich</p>';
    }
?>
        <p>
            in Erbach, Reichelsheim und Beerfelden<br>(teilweise auch in Lützelbach, Vielbrunn, Würzberg und Bad König)
        </p>
        </div>
        </div>
        <div class="FAIRsepdown"></div>
        <div style="text-align: center;">
            <p>Bei Fragen zu den Testmöglichkeiten:</p>
            <p><a href="mailto:testzentrum@drk-odenwaldkreis.de">testzentrum@drk-odenwaldkreis.de</a></p>
        </div>
        <div class="FAIRsepdown"></div>
    </div>

    <div class="col-md-4 col-xs-12 main-link-page">
        <div class="header_icon" style="border: 1px solid transparent;border-radius: 4px;border-color: #776346;padding-top:12px;">
<?php
    if(!$GLOBALS['FLAG_SHUTDOWN_TESTING_ANTIKOERPER']) {
        echo '<img src="img/impfzentrum.jpg" style="display: block; margin-left: auto; margin-right: auto; width: 50%; border: solid 1px #5a482d; cursor: pointer;" onclick="window.location=\'https://www.impfzentrum-odw.de/antikoerper\'"></img>';
    } else {
        echo '<img src="img/impfzentrum.jpg" style="display: block; margin-left: auto; margin-right: auto; width: 50%; border: solid 1px #5a482d;"></img>';
    }
?>
        <div class="FAIRsep"></div>
        <div class="caption center_text">
            <h3><b>Antikörpertest</b></h3>
<?php
    if(!$GLOBALS['FLAG_SHUTDOWN_TESTING_ANTIKOERPER']) {
        echo '<p><a class="btn btn-primary btn-lg" href="https://www.impfzentrum-odw.de/antikoerper" role="button">Zur Terminvergabe</a></p>';
    } else {
        echo '<p>Derzeit keine Anmeldung möglich, geöffnet zu den Impfterminzeiten</p>';
    }
?>
        <p>
            nur in Erbach
        </p>
        </div>
        </div>
        <div class="FAIRsepdown"></div>
        <div style="text-align: center;">
            <p>Bei Fragen zu den Testmöglichkeiten:</p>
            <p><a href="mailto:testzentrum@drk-odenwaldkreis.de">testzentrum@drk-odenwaldkreis.de</a></p>
        </div>
        <div class="FAIRsepdown"></div>
    </div>

    </div>

    <div class="FAIRsepdown"></div>
    <div style="text-align: center;">
    <p>Bei Fragen können Sie sich auch an das Personal vor Ort wenden.</p>
    <p>Bitte erscheinen Sie nur, wenn Sie frei von den typischen Symptomen, wie Fieber, trockenem Husten oder plötzlichem Verlust des Geruchs- oder Geschmackssinnes sind.</p>
    </div>
    
    <div class="FAIRsepdown"></div>
    <div class="FAIRsepdown"></div>    
    <div class="FAIRsepdown"></div>
    <div class="FAIRsep"></div>
</div>


<?php
// Print html content part C
echo $GLOBALS['G_html_main_right_c'];
// Print html footer
echo $GLOBALS['G_html_footer'];

?>
