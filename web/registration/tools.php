<?php

/* **************

Websystem für das Testzentrum DRK Odenwaldkreis
Author: Marc S. Duchene
March 2021

tools

** ************** */

/****************************************/
/* SQL functions */
/****************************************/

// Open DB connection
function S_open_db () {
	/* Database connection information */
	$gaSql=$GLOBALS['gaSql_server'];
	
	// Connect to DB
	$link=mysqli_connect($gaSql['server'],$gaSql['user'],$gaSql['password'],$gaSql['db']);
	if (!$link) {
		echo "<br>Fehler: konnte nicht mit MySQL verbinden.";
		echo "<br>Debug-Fehlernummer: " . mysqli_connect_errno();
		echo "<br>Debug-Fehlermeldung: " . mysqli_connect_error();
		echo "<br>";
		exit;
	}

	if (!$link) {
		header('Location: error.php?e=err80');
	}

	// Return the database object
	return $link;
}

// Close DB connection
function S_close_db ($Db) {
	mysqli_close($Db);
	return 0;
}


// Return query result from SQL database - first entry only
function S_get_entry ($Db,$sQuery) {
	$result = mysqli_query( $Db, $sQuery );
	$r = mysqli_fetch_all($result);

	// Return result of SQL query
	return $r[0][0];
}
// Only for login
function S_get_entry_login_username ($Db,$username) {
	$stmt=mysqli_prepare($Db,"SELECT id FROM li_user WHERE lower(username)=?;");
	mysqli_stmt_bind_param($stmt, "s", $username);
	mysqli_stmt_execute($stmt);
	mysqli_stmt_bind_result($stmt, $id);
	mysqli_stmt_fetch($stmt);
	mysqli_stmt_close($stmt);

	// Return result of SQL query
	return $id;
}
// Return query result from SQL database - all entries
function S_get_multientry ($Db,$sQuery) {
	$result = mysqli_query( $Db, $sQuery );
	$r = mysqli_fetch_all($result);

	// Return result of SQL query
	return $r;
}
// Write data
function S_set_data ($Db,$sQuery) {
    $r = mysqli_query( $Db, $sQuery );
	
	// Return result of SQL query
	return $r;
}


function S_get_entry_vorgang ($Db,$scanevent) {
	$token=substr($scanevent, strrpos($scanevent, 'K' )+1);
	$stmt=mysqli_prepare($Db,"SELECT id, Used FROM Kartennummern WHERE id=?;");
	mysqli_stmt_bind_param($stmt, "i", $token);
	mysqli_stmt_execute($stmt);
	mysqli_stmt_bind_result($stmt, $id_kartennummer, $used);
	mysqli_stmt_fetch($stmt);
	mysqli_stmt_close($stmt);
	// Check if number exists in table Kartennummern
	if($used==1) {
		// Karte bereits verwendet mit Ergebnis
		return "Used";
	} elseif($id_kartennummer>0) {
		$stmt=mysqli_prepare($Db,"SELECT id FROM Vorgang WHERE Token=?;");
		mysqli_stmt_bind_param($stmt, "s", $token);
		mysqli_stmt_execute($stmt);
		mysqli_stmt_bind_result($stmt, $id);
		mysqli_stmt_fetch($stmt);
		mysqli_stmt_close($stmt);

		// Return result of SQL query
		return $id;
	} else {
		return "Not registered";
	}
}

function S_set_entry_voranmeldung ($Db,$array_data) {
	$stmt=mysqli_prepare($Db,"INSERT INTO Voranmeldung (Vorname, Nachname, Geburtsdatum, Adresse, Telefon, Mailadresse, Slot) VALUES (?,?,?,?,?,?,?);");
	mysqli_stmt_bind_param($stmt, "s", $array_data[0], $array_data[1], $array_data[2], $array_data[3], $array_data[4], $array_data[5], $array_data[6]);
	mysqli_stmt_execute($stmt);
	mysqli_stmt_bind_result($stmt, $result);
	mysqli_stmt_fetch($stmt);
	mysqli_stmt_close($stmt);
	return $result; // TODO need id as a return value
}

function S_get_entry_voranmeldung ($Db,$array_data) {
	$stmt=mysqli_prepare($Db,"SELECT id_preregistration FROM Voranmeldung_Verif WHERE id=? AND Token=?;");
	mysqli_stmt_bind_param($stmt, "s", $array_data[0], $array_data[1]);
	mysqli_stmt_execute($stmt);
	mysqli_stmt_bind_result($stmt, $id);
	mysqli_stmt_fetch($stmt);
	mysqli_stmt_close($stmt);
	return $id;
}


/****************************************/
/* Auxilliary functions */
/****************************************/

// Generate random token
function A_generate_token($length = 8) {
		// without 0, O, o, z, Z, y, Y
    $characters = '123456789abcdefghijklmnpqrstuvwxABCDEFGHIJKLMNPQRSTUVWX';
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, strlen($characters) - 1)];
    }
    return $randomString;
}


/****************************************/
/* HTML code snippets */
/****************************************/

function H_build_boxhead( $w, $id, $title, $j=0 ) {
	if($w>0) {
		$w_string='width: '.$w.'px;';
		$w_string50='width: '.($w-50).'px;';
		$w_string10='width: '.($w-10).'px;';
	}
	$class_add='';
	$margin_add='';
	$margin_add='margin-right:15px;';
	$html_result = '<div style="float:none; '.$margin_add.'"><div class="FAIR-box-head'.$class_add.'" style="'.$w_string.' position: relative; z-index:'.(50-$j).';">
  <div class="FAIR-foldbox-head-left" style="display: inline; '. $w_string50.'">
  '.$title.'
  </div>
  <div class="FAIR-foldbox-head-right" style="display: inline; width: 50px;">&nbsp;
  </div></div>
  <div class="FAIR-foldbox-static '.$class_add.'" id="'.$id.'" style="display: block; '.$w_string.' position: relative; z-index: '.(49-$j).';">';
	return $html_result;
}
// return html code for info card after foldbox header
function H_build_boxinfo( $w, $text, $c='red' ) {
	if($c=='red') { $class='alert alert-danger'; }
	elseif($c=='blue') { $class='alert alert-info'; }
	elseif($c=='green') { $class='alert alert-success'; }
	else { $class='alert alert-warning'; }
	if($w==0) {
		$html_result = '<div class="'.$class.'" style="top: -5px">';
	} else {
		$html_result = '<div class="'.$class.'" style="width: '. ($w-20) .'px; left: -10px; top: -5px">';
	}
	$html_result .= '<p style="margin-right: 4px;">'.$text.'</p>';
	$html_result .= '</div>';
	return $html_result;
}
// return html code for box style
function H_build_boxheadinner( $w, $id, $title, $j=0 ) {
	$html_result = '<div style="float: none; margin-right:15px;"><div class="FAIR-box-head-inner" style="width: '.$w.'px; position: relative; z-index:'.(50-$j).';">
  <div class="FAIR-foldbox-head-left" style="display: inline; width: '. ($w-50) .'px;">
  '.$title.'
  </div>
  <div class="FAIR-foldbox-head-right" style="display: inline; width: 50px;">
  </div></div>
  <div class="FAIR-foldbox-static-inner" id="'.$id.'" style="display: block; width: '. ($w-10) .'px; position: relative; z-index: '.(49-$j).';">';
	return $html_result;
}

function H_build_boxfoot( ) {
	$html_result = '</div></div>';
	return $html_result;
}

?>