<?php

/* **************

Websystem für das Testzentrum DRK Odenwaldkreis
Author: Marc S. Duchene
March 2021

Edit data of registered person

** ************** */

include_once 'preload.php';
if( isset($GLOBALS['G_sessionname']) ) { session_name ($GLOBALS['G_sessionname']); }
session_start();
$sec_level=1;
$current_site="edit_person";

// Include functions
include_once 'tools.php';
include_once 'auth.php';
include_once 'menu.php';


// Print html header
echo $GLOBALS['G_html_header'];

// Print html menu
echo $GLOBALS['G_html_menu'];
echo $GLOBALS['G_html_menu2'];

// Print html content part A
echo $GLOBALS['G_html_main_right_a'];

// role check
if( A_checkpermission(array(1,2,0,4)) ) {


  // Open database connection
  $Db=S_open_db();

  if( isset($_POST['submit_person']) ){
    // ///////////////
    // Registrierungsänderung speichern
    // ///////////////
      $k_id=$_POST['id'];
      $k_nname=$_POST['nname'];
      $k_vname=$_POST['vname'];
      $k_geb=$_POST['geburtsdatum'];
      $k_adresse=$_POST['adresse'];
      $k_tel=$_POST['telefon'];
      $k_email=$_POST['email'];
  
      S_set_data($Db,'UPDATE Vorgang SET
      Vorname=\''.$k_vname.'\',
      Nachname=\''.$k_nname.'\',
      Geburtsdatum=\''.$k_geb.'\',
      Adresse=\''.$k_adresse.'\',
      Telefon=\''.$k_tel.'\',
      Mailadresse=\''.$k_email.'\'
      WHERE id=CAST('.$k_id.' AS int);');
  
      echo '<div class="row">';
      echo '<div class="col-sm-12">
      <h3>Änderung gespeichert</h3>';
      echo '<div class="list-group">';
      echo '<a class="list-group-item list-group-item-action list-group-item-FAIR" href="testlist.php">Zurück zur Testliste</a>';
      echo '</div></div>';
  
  
    } elseif( isset($_GET['id']) ) {

  // ///////////////
  // Registrierung ändern / Formular
  // ///////////////

    // Get data
    $array_vorgang=S_get_multientry($Db,'SELECT id,Teststation, Token, Registrierungszeitpunkt, Vorname,Nachname,Geburtsdatum,Adresse,Telefon,Mailadresse FROM Vorgang WHERE id=CAST('.$_GET['id'].' AS int);');

    echo '<div class="row">';
    echo '<div class="col-sm-12">
    <h3>Kunden-Registrierung ändern</h3>
    <p>Änderung des Test-Ergebnisses ist nicht möglich.</p>';
    echo '<form action="'.$current_site.'.php" method="post">
    <div class="input-group"><span class="input-group-addon" id="basic-addon1">ID</span><input type="text" class="form-control" placeholder="" aria-describedby="basic-addon1" value="'.$array_vorgang[0][0].'" disabled>
    <span class="input-group-addon" id="basic-addon1">S</span><input type="text" class="form-control" placeholder="" aria-describedby="basic-addon1" value="'.$array_vorgang[0][1].'" disabled>
    <span class="input-group-addon" id="basic-addon1">K</span><input type="text" class="form-control" placeholder="" aria-describedby="basic-addon1" value="'.$array_vorgang[0][2].'" disabled></div>
    <div class="input-group"><span class="input-group-addon" id="basic-addon1">Reg</span><input type="text" class="form-control" placeholder="" aria-describedby="basic-addon1" value="'.$array_vorgang[0][3].'" disabled>
    <input type="text" name="id" value="'.$array_vorgang[0][0].'" style="display:none;"></div>
    <div class="input-group"><span class="input-group-addon" id="basic-addon1">Vorname</span><input type="text" name="vname" class="form-control" placeholder="" aria-describedby="basic-addon1" value="'.$array_vorgang[0][4].'" required></div>
    <div class="input-group"><span class="input-group-addon" id="basic-addon1">Nachname</span><input type="text" name="nname" class="form-control" placeholder="" aria-describedby="basic-addon1" value="'.$array_vorgang[0][5].'" required></div>
    <div class="input-group"><span class="input-group-addon" id="basic-addon1">Geburtsdatum</span><input type="date" name="geburtsdatum" class="form-control" placeholder="" aria-describedby="basic-addon1" value="'.$array_vorgang[0][6].'" required></div>
    <div class="input-group"><span class="input-group-addon" id="basic-addon1">Wohnadresse</span><input type="text" name="adresse" class="form-control" placeholder="" aria-describedby="basic-addon1" value="'.$array_vorgang[0][7].'" required></div>
    <div class="input-group"><span class="input-group-addon" id="basic-addon1">Telefon</span><input type="text" name="telefon" class="form-control" placeholder="" aria-describedby="basic-addon1" value="'.$array_vorgang[0][8].'"></div>
    <div class="input-group"><span class="input-group-addon" id="basic-addon1">E-Mail</span><input type="text" name="email" class="form-control" placeholder="" aria-describedby="basic-addon1" value="'.$array_vorgang[0][9].'"></div>
    <span class="input-group-btn">
      <input type="submit" class="btn btn-danger" value="Änderung speichern" name="submit_person" />
      </span>
    </form>';
    echo '<a class="list-group-item list-group-item-action list-group-item-redtext" href="testlist.php">Zurück zur Testliste</a>';
    
    echo '</div></div>';

  }
} else {
  // Print html header
  echo $GLOBALS['G_html_header'];

  // Print html menu
  echo $GLOBALS['G_html_menu'];
  echo $GLOBALS['G_html_menu2'];

  // Print html content part A
  echo $GLOBALS['G_html_main_right_a'];
  echo '<h1>KEINE BERECHTIGUNG</h1>';
}

// Print html content part C
echo $GLOBALS['G_html_main_right_c'];
// Print html footer
echo $GLOBALS['G_html_footer'];

?>