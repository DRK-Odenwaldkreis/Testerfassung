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
// Only for login
function S_get_entry_login_firmencode ($Db,$firmencode) {
	$stmt=mysqli_prepare($Db,"SELECT id FROM Station WHERE Firmencode=?;");
	mysqli_stmt_bind_param($stmt, "s", $firmencode);
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
	// First, check if Termin_id is already used by same person (  to fix a bug found by N.B. <3  )
	$stmt=mysqli_prepare($Db,"SELECT id FROM Voranmeldung WHERE Vorname=? AND Nachname=? AND Geburtsdatum=? AND Adresse=? AND Wohnort=? AND Telefon=? AND Mailadresse=? AND Tag=?;");
	mysqli_stmt_bind_param($stmt, "ssssssss", $array_data[0], $array_data[1], $array_data[2], $array_data[3], $array_data[4], $array_data[5], $array_data[6], $array_data[8]);
	mysqli_stmt_execute($stmt);
	mysqli_stmt_bind_result($stmt, $double_entry_id);
	mysqli_stmt_fetch($stmt);
	mysqli_stmt_close($stmt);

	if($double_entry_id>0) {
		return 'DOUBLE_ENTRY';
	}

	// Second, check if Termin_id is already used by other person
	$stmt=mysqli_prepare($Db,"SELECT id, Slot, id_station, Tag, Stunde FROM Termine WHERE id=?;");
	mysqli_stmt_bind_param($stmt, "i", $array_data[7]);
	mysqli_stmt_execute($stmt);
	mysqli_stmt_bind_result($stmt, $termin_id, $termin_slot, $termin_station, $termin_tag, $termin_stunde);
	mysqli_stmt_fetch($stmt);
	mysqli_stmt_close($stmt);
	if($termin_slot>0) {
		$check_termin_id=S_get_entry($Db,'SELECT id FROM Voranmeldung WHERE Termin_id=CAST('.$termin_id.' as int);');
	} else {
		$check_termin_id=0;
	}

	if($check_termin_id>0) {
		// Selected Termin is used, select another in same slot if available
		$new_termin_id=S_get_entry($Db,'SELECT id FROM Termine WHERE id_station='.$termin_station.' AND Tag="'.$termin_tag.'" AND Stunde='.$termin_stunde.' AND Slot='.$termin_slot.' AND Used is null;');
		if($new_termin_id>0) {
			// is available - use new termin_id
			$termin_id=$new_termin_id;
		} else {
			// is not available
			return 0;
		}
	} 
	
	// Write data because Termin_id is not used or Termin_id has no slots
	if($termin_slot>0) {
		S_set_data($Db,'UPDATE Termine SET Used=1 WHERE id=CAST('.$termin_id.' as int);');
	}
	$stmt=mysqli_prepare($Db,"INSERT INTO Voranmeldung (Vorname, Nachname, Geburtsdatum, Adresse, Wohnort, Telefon, Mailadresse, Termin_id, Tag, CWA_request, PCR_Grund) VALUES (?,?,?,?,?,?,?,?,?,?,?);");
	mysqli_stmt_bind_param($stmt, "sssssssisii", $array_data[0], $array_data[1], $array_data[2], $array_data[3], $array_data[4], $array_data[5], $array_data[6], $termin_id, $array_data[8],$array_data[9],$array_data[10]);
	mysqli_stmt_execute($stmt);
	mysqli_stmt_bind_result($stmt, $result);
	mysqli_stmt_fetch($stmt);
	mysqli_stmt_close($stmt);



	$stmt=mysqli_prepare($Db,"SELECT id FROM Voranmeldung WHERE Vorname=? AND Nachname=? AND Geburtsdatum=? AND Adresse=? AND Wohnort=? AND Telefon=? AND Mailadresse=? AND Termin_id=? AND Tag=? ORDER BY id DESC;");
	mysqli_stmt_bind_param($stmt, "sssssssis", $array_data[0], $array_data[1], $array_data[2], $array_data[3], $array_data[4], $array_data[5], $array_data[6], $termin_id, $array_data[8]);
	mysqli_stmt_execute($stmt);
	mysqli_stmt_bind_result($stmt, $result2);
	mysqli_stmt_fetch($stmt);
	mysqli_stmt_close($stmt);
	return $result2; // need id as a return value
	
}
function S_set_entry_voranmeldung_vaccinate ($Db,$array_data) {
	// First, check if Termin_id is already used by same person (  to fix a bug found by N.B. <3  )
	if($array_data[4]=='') {
		$stmt=mysqli_prepare($Db,"SELECT id FROM Voranmeldung WHERE Vorname=? AND Nachname=? AND Telefon=? AND Tag=?;");
		mysqli_stmt_bind_param($stmt, "ssss", $array_data[0], $array_data[1], $array_data[3], $array_data[6]);
	} else {
		$stmt=mysqli_prepare($Db,"SELECT id FROM Voranmeldung WHERE Vorname=? AND Nachname=? AND Telefon=? AND Mailadresse=? AND Tag=?;");
		mysqli_stmt_bind_param($stmt, "sssss", $array_data[0], $array_data[1], $array_data[3], $array_data[4], $array_data[6]);
	}
	mysqli_stmt_execute($stmt);
	mysqli_stmt_bind_result($stmt, $double_entry_id);
	mysqli_stmt_fetch($stmt);
	mysqli_stmt_close($stmt);

	if($double_entry_id>0) {
		return 'DOUBLE_ENTRY';
	}

	// Second, check if Termin_id is already used by other person
	$stmt=mysqli_prepare($Db,"SELECT id, Slot, id_station, Tag, Stunde FROM Termine WHERE id=?;");
	mysqli_stmt_bind_param($stmt, "i", $array_data[5]);
	mysqli_stmt_execute($stmt);
	mysqli_stmt_bind_result($stmt, $termin_id, $termin_slot, $termin_station, $termin_tag, $termin_stunde);
	mysqli_stmt_fetch($stmt);
	mysqli_stmt_close($stmt);
	if($termin_slot>0) {
		$check_termin_id=S_get_entry($Db,'SELECT id FROM Voranmeldung WHERE Termin_id=CAST('.$termin_id.' as int);');
	} else {
		$check_termin_id=0;
	}

	if($check_termin_id>0) {
		// Selected Termin is used, select another in same slot if available
		$new_termin_id=S_get_entry($Db,'SELECT id FROM Termine WHERE id_station='.$termin_station.' AND Tag="'.$termin_tag.'" AND Stunde='.$termin_stunde.' AND Slot='.$termin_slot.' AND Used is null;');
		if($new_termin_id>0) {
			// is available - use new termin_id
			$termin_id=$new_termin_id;
		} else {
			// is not available
			return 'NOT_AVAILABLE';
		}
	} 
	
	// Write data because Termin_id is not used or Termin_id has no slots
	if($termin_slot>0) {
		S_set_data($Db,'UPDATE Termine SET Used=1 WHERE id=CAST('.$termin_id.' as int);');
	}
	if($array_data[4]=='') {
		$stmt=mysqli_prepare($Db,"INSERT INTO Voranmeldung (Vorname, Nachname, Geburtsdatum, Telefon, Termin_id, Tag, Booster) VALUES (?,?,?,?,?,?,?);");
		mysqli_stmt_bind_param($stmt, "ssssisi", $array_data[0], $array_data[1], $array_data[2], $array_data[3], $termin_id, $array_data[6], $array_data[7]);
	} else {
		$stmt=mysqli_prepare($Db,"INSERT INTO Voranmeldung (Vorname, Nachname, Geburtsdatum, Telefon, Mailadresse, Termin_id, Tag, Booster) VALUES (?,?,?,?,?,?,?,?);");
		mysqli_stmt_bind_param($stmt, "sssssisi", $array_data[0], $array_data[1], $array_data[2], $array_data[3], $array_data[4], $termin_id, $array_data[6], $array_data[7]);
	}
	mysqli_stmt_execute($stmt);
	mysqli_stmt_bind_result($stmt, $result);
	mysqli_stmt_fetch($stmt);
	mysqli_stmt_close($stmt);

	$stmt=mysqli_prepare($Db,"SELECT id FROM Voranmeldung WHERE Vorname=? AND Nachname=? AND Telefon=? AND Termin_id=? AND Tag=? ORDER BY id DESC;");
	mysqli_stmt_bind_param($stmt, "sssis", $array_data[0], $array_data[1], $array_data[3], $termin_id, $array_data[6]);
	mysqli_stmt_execute($stmt);
	mysqli_stmt_bind_result($stmt, $result2);
	mysqli_stmt_fetch($stmt);
	mysqli_stmt_close($stmt);
	return $result2; // need id as a return value
	
}

function S_set_entry_voranmeldung_antikoerper ($Db,$array_data) {
	// First, check if Termin_id is already used by same person (  to fix a bug found by N.B. <3  )
	if($array_data[3]=='') {
		$stmt=mysqli_prepare($Db,"SELECT id FROM Voranmeldung WHERE Vorname=? AND Nachname=? AND Telefon=? AND Tag=?;");
		mysqli_stmt_bind_param($stmt, "ssss", $array_data[0], $array_data[1], $array_data[2], $array_data[5]);
	} else {
		$stmt=mysqli_prepare($Db,"SELECT id FROM Voranmeldung WHERE Vorname=? AND Nachname=? AND Telefon=? AND Mailadresse=? AND Tag=?;");
		mysqli_stmt_bind_param($stmt, "sssss", $array_data[0], $array_data[1], $array_data[2], $array_data[3], $array_data[5]);
	}
	mysqli_stmt_execute($stmt);
	mysqli_stmt_bind_result($stmt, $double_entry_id);
	mysqli_stmt_fetch($stmt);
	mysqli_stmt_close($stmt);
	
	if($double_entry_id>0) {
		return 'DOUBLE_ENTRY';
	}

	// Second, check if Termin_id is already used by other person
	$stmt=mysqli_prepare($Db,"SELECT id, Slot, id_station, Tag, Stunde FROM Termine WHERE id=?;");
	mysqli_stmt_bind_param($stmt, "i", $array_data[4]);
	mysqli_stmt_execute($stmt);
	mysqli_stmt_bind_result($stmt, $termin_id, $termin_slot, $termin_station, $termin_tag, $termin_stunde);
	mysqli_stmt_fetch($stmt);
	mysqli_stmt_close($stmt);
	if($termin_slot>0) {
		$check_termin_id=S_get_entry($Db,'SELECT id FROM Voranmeldung WHERE Termin_id=CAST('.$termin_id.' as int);');
	} else {
		$check_termin_id=0;
	}

	if($check_termin_id>0) {
		// Selected Termin is used, select another in same slot if available
		$new_termin_id=S_get_entry($Db,'SELECT id FROM Termine WHERE id_station='.$termin_station.' AND Tag="'.$termin_tag.'" AND Stunde='.$termin_stunde.' AND Slot='.$termin_slot.' AND Used is null;');
		if($new_termin_id>0) {
			// is available - use new termin_id
			$termin_id=$new_termin_id;
		} else {
			// is not available
			return 'NOT_AVAILABLE';
		}
	} 
	// Write data because Termin_id is not used or Termin_id has no slots
	if($termin_slot>0) {
		S_set_data($Db,'UPDATE Termine SET Used=1 WHERE id=CAST('.$termin_id.' as int);');
	}
	if($array_data[3]=='') {
		$stmt=mysqli_prepare($Db,"INSERT INTO Voranmeldung (Vorname, Nachname, Telefon, Termin_id, Tag) VALUES (?,?,?,?,?);");
		mysqli_stmt_bind_param($stmt, "sssis", $array_data[0], $array_data[1], $array_data[2], $termin_id, $array_data[5]);
	} else {
		$stmt=mysqli_prepare($Db,"INSERT INTO Voranmeldung (Vorname, Nachname, Telefon, Mailadresse, Termin_id, Tag) VALUES (?,?,?,?,?,?);");
		mysqli_stmt_bind_param($stmt, "ssssis", $array_data[0], $array_data[1], $array_data[2], $array_data[3], $termin_id, $array_data[5]);
		
	}
	mysqli_stmt_execute($stmt);
	mysqli_stmt_bind_result($stmt, $result);
	mysqli_stmt_fetch($stmt);
	mysqli_stmt_close($stmt);
	$stmt=mysqli_prepare($Db,"SELECT id FROM Voranmeldung WHERE Vorname=? AND Nachname=? AND Telefon=? AND Mailadresse=? AND Termin_id=? AND Tag=? ORDER BY id DESC;");
	mysqli_stmt_bind_param($stmt, "ssssis", $array_data[0], $array_data[1], $array_data[2], $array_data[3], $termin_id, $array_data[5]);
	mysqli_stmt_execute($stmt);
	mysqli_stmt_bind_result($stmt, $result2);
	mysqli_stmt_fetch($stmt);
	mysqli_stmt_close($stmt);
	return $result2; // need id as a return value
	
}

function S_get_entry_voranmeldung ($Db,$array_data) {
	$stmt=mysqli_prepare($Db,"SELECT id_preregistration FROM Voranmeldung_Verif WHERE id_preregistration=? AND Token=?;");
	mysqli_stmt_bind_param($stmt, "is", $array_data[0], $array_data[1]);
	mysqli_stmt_execute($stmt);
	mysqli_stmt_bind_result($stmt, $id);
	mysqli_stmt_fetch($stmt);
	mysqli_stmt_close($stmt);
	return $id;
}
function S_get_entry_voranmeldung_debug ($Db,$data) {
	$stmt=mysqli_prepare($Db,"SELECT Token FROM Voranmeldung WHERE id=?;");
	mysqli_stmt_bind_param($stmt, "i", $data);
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
		// without 0, O, o, z, Z, y, Y, l
    $characters = '123456789abcdefghijkmnpqrstuvwxABCDEFGHIJKLMNPQRSTUVWX';
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, strlen($characters) - 1)];
    }
    return $randomString;
}

// Login for user with $uid
function A_login_firmencode($Db,$sid) {
    
	$_SESSION['b2b_id'] = $sid;
	if($_SESSION['b2b_id']=='') { die("Error in database. (Err:102)"); }
	
	$_SESSION['b2b_signedin'] = true;
	$_SESSION['b2b_username'] = S_get_entry($Db,'SELECT Ort FROM Station WHERE id='.$sid.';');
	$_SESSION['b2b_code'] = S_get_entry($Db,'SELECT Firmencode FROM Station WHERE id='.$sid.';');

    return true;
}

function A_get_day_name($number_of_week) {
	$days=array('Sonntag','Montag','Dienstag','Mittwoch','Donnerstag','Freitag','Samstag');
	return $days[$number_of_week];
}
function A_get_day_name_2($number_of_week) {
	$days=array('So','Mo','Di','Mi','Do','Fr','Sa');
	return $days[$number_of_week];
}

function A_sanitize_input_light($input) {
	// strips any HTML and PHP tags
	strip_tags($input);

	// validate white listed chars in input (alphanumeric)
	$validated = "";
	/* if(preg_match("/^[a-zA-Z0-9\-\.@\,\+äöüÄÖÜßéèêóòôíìîáàâúùû&\/]+$/", $input)) {
		$validated = $input;
	} */
	$whitelist=array('/^[a-zA-Z0-9äöüÄÖÜßéèêóòôíìîáàâúùû&\ \_\-\.@\,\:\+\/]+$/');
	// Check if each character of input is in white list
	foreach($whitelist as $k => $v) {
		if(preg_match($v, $input)) {
			$validated=$input;
			break;
		}
	}
	
	return $validated;
}

function A_sanitize_input($input) {
	// strips any HTML and PHP tags
	strip_tags($input);

	// validate white listed chars in input (alphanumeric)
	$validated = "";
	/* if(preg_match("/^[a-zA-Z0-9\-\.@\,\+äöüÄÖÜßéèêóòôíìîáàâúùû&\/]+$/", $input)) {
		$validated = $input;
	} */
	$whitelist=array('/^[a-zA-Z0-9äöüÄÖÜßéèêóòôíìîáàâúùû&\ \_\-\.@\,\+\/]+$/');
	// Check if each character of input is in white list
	foreach($whitelist as $k => $v) {
		if(preg_match($v, $input)) {
			$validated=$input;
			break;
		}
	}
	
	return $validated;
}

function A_sanitize_input_light2($input) {
	// strips any HTML and PHP tags
	strip_tags($input);
	
	return $input;
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

function H_build_table_testdates( ) {
	$res='';
	$Db=S_open_db();
	$flag_prereg=S_get_entry($Db,'SELECT value FROM website_settings WHERE name="FLAG_Pre_registration";');
	$stations_array=S_get_multientry($Db,'SELECT id, Ort, Adresse FROM Station WHERE Firmencode is null OR Firmencode="";');
	// X ist Anzahl an Tagen für Vorschau in Tabelle
	$X=14;
	// Ohne Terminbuchung für nächste X Tage
	$today=date('Y-m-d');
	$in_x_days=date('Y-m-d', strtotime($today. ' + '.$X.' days'));
	

	// Table
	$res.='
	<table class="FAIR-data">
	<tr>
    <td class="FAIR-data-height2 FAIR-data-right FAIR-data-left FAIR-data-bottom FAIR-data-top FAIR-data-gray"><h4>Ort</h4></td>';
	for($j=0;$j<$X;$j++) {
		$string_date=date('d.m.', strtotime($today. ' + '.$j.' days'));
		$res.='<td class="FAIR-data-height2 FAIR-data-right FAIR-data-left FAIR-data-bottom FAIR-data-top FAIR-data-center1 FAIR-data-gray"><h4>'.$string_date.'</h4></td>';
	}
	$res.='<td class="FAIR-data-height2 FAIR-data-right FAIR-data-left FAIR-data-bottom FAIR-data-top FAIR-data-gray"></td></tr>';

	$res.='<tr>
    <td class="FAIR-data-height1 FAIR-data-right FAIR-data-left FAIR-data-bottom FAIR-data-top FAIR-data-blue1" colspan="'.($X+2).'"><b><i>';
	if($flag_prereg!=0) {
		//$res.='Für folgende Teststationen ist keine Terminbuchung möglich, eine Voranmeldung kann gerne gemacht werden';
		$res.='Für folgende Teststationen ist keine Voranmeldung notwendig';
	} else {
		$res.='Für folgende Teststationen ist keine Voranmeldung notwendig';
	}
	$res.='</i></b></td>
	</tr>';
	foreach($stations_array as $st) {
		// check if station has appointed times
		if( S_get_entry($Db,'SELECT id_station FROM Termine WHERE Slot is null AND Tag>="'.$today.'" AND Tag<="'.$in_x_days.'" AND id_station='.$st[0].';')==$st[0]) {
			/* $location_thirdline_val=S_get_entry($Db,'SELECT Oeffnungszeiten FROM Station WHERE id='.$st[0].';');
			if($location_thirdline_val!='') {
				$display_location_thirdline='<br><span class="text-sm">'.$location_thirdline_val.'</span>';
			} else {
				$display_location_thirdline='';
			} */$display_location_thirdline='';
			$res.='<tr>';
			$string_location='<b>'.$st[1].'</b><br>'.$st[2].'';
			$res.='<td class="FAIR-data-height2 FAIR-data-right FAIR-data-left FAIR-data-bottom FAIR-data-top FAIR-data-blue2">'.$string_location.$display_location_thirdline.'</td>';
			for($j=0;$j<$X;$j++) {
				$in_j_days=date('Y-m-d', strtotime($today. ' + '.$j.' days'));
				$array_termine_open=S_get_multientry($Db,'SELECT Startzeit, Endzeit, opt_station, opt_station_adresse FROM Termine WHERE Slot is null AND id_station='.$st[0].' AND Tag="'.$in_j_days.'" ORDER BY Startzeit ASC;');
				$string_times='';
				$string_location_opt='';
				foreach($array_termine_open as $te) {
					if($te[2]!='') {
						$string_times.='<span class="text-sm">'.$te[2].',<br>'.$te[3].'</span><br>';
					}
					$string_times.=date('H:i',strtotime($te[0])).' - '.date('H:i',strtotime($te[1])).'<br>';
				}
				if($string_times!='') {
					$res.='<td class="FAIR-data-height2 FAIR-data-right FAIR-data-left FAIR-data-bottom FAIR-data-top FAIR-data-center1 FAIR-data-blue2">'.$string_times.'</td>';
				} else {
					$res.='<td class="FAIR-data-height2 FAIR-data-right FAIR-data-left FAIR-data-bottom FAIR-data-top FAIR-data-center1 FAIR-data-blue3"></td>';
				}
			}
			$res.='<td class="FAIR-data-height2 FAIR-data-right FAIR-data-left FAIR-data-bottom FAIR-data-top FAIR-data-center1 FAIR-data-blue3"></td>';
			$res.='</tr>';
		}
		
	}

	
	$res.='<tr>
    <td class="FAIR-data-height2 FAIR-data-right FAIR-data-left FAIR-data-bottom FAIR-data-top FAIR-data-gray"><h4>Ort</h4></td>';
	for($j=0;$j<$X;$j++) {
		$string_date=date('d.m.', strtotime($today. ' + '.$j.' days'));
		$res.='<td class="FAIR-data-height2 FAIR-data-right FAIR-data-left FAIR-data-bottom FAIR-data-top FAIR-data-center1 FAIR-data-gray"><h4>'.$string_date.'</h4></td>';
	}
	$res.='<td class="FAIR-data-height2 FAIR-data-right FAIR-data-left FAIR-data-bottom FAIR-data-top FAIR-data-gray"></td></tr>';
    
	$res.='</table>
	';

	S_close_db($Db);
	return $res;
}

// for usage on public sites (not for administration)
function H_build_table_testdates2( $mode ) {
	if($mode=='b2b') {
		$query_b2b='Station.id='.$_SESSION['b2b_id'];
		$path_to_reg='';
	} elseif($mode=='pcr') {
		$query_b2b='(Firmencode is null OR Firmencode="") AND Testtyp.IsPCR=1';
		$path_to_reg='registration/';
	} elseif($mode=='ag') {
		$query_b2b='(Firmencode is null OR Firmencode="") AND Testtyp.IsPCR=0';
		$path_to_reg='registration/';
	} elseif($mode=='vaccinate') {
		$query_b2b='(Firmencode is null OR Firmencode="")';
		$path_to_reg='registration/';
	} elseif($mode=='b2b-vaccinate') {
		$query_b2b='Station.Firmencode = (SELECT Station.Firmencode FROM Station WHERE Station.id='.$_SESSION['b2b_id'].')';
		$path_to_reg='';
	} elseif($mode=='antikoerper') {
		$query_b2b='(Firmencode is null OR Firmencode="")';
		$path_to_reg='registration/';
	} else {
		$query_b2b='Firmencode is null OR Firmencode=""';
		$path_to_reg='registration/';
		
	}
	$res_s_array=array(); // for small displays - array with one element per day
	$res_l_array=array(); // for large displays - array for table [row=days][column=station]
	$Db=S_open_db();
	$flag_prereg=S_get_entry($Db,'SELECT value FROM website_settings WHERE name="FLAG_Pre_registration" LIMIT 1;');
	if($mode == 'vaccinate' || $mode == 'b2b-vaccinate') {
		$stations_array=S_get_multientry($Db,'SELECT Station.id, Station.Ort, Station.Adresse, 1, Impfstoff.Kurzbezeichnung, Impfstoff.Mindestalter, Impfstoff.Maximalalter FROM Station 
		JOIN Impfstoff ON Impfstoff.id=Station.Impfstoff_id WHERE '.$query_b2b.' ORDER BY Impfstoff.Kurzbezeichnung ASC, Station.Ort ASC;');
	} elseif($mode == 'antikoerper') {
		$stations_array=S_get_multientry($Db,'SELECT Station.id, Station.Ort, Station.Adresse FROM Station WHERE '.$query_b2b.';');
	} else {
		$stations_array=S_get_multientry($Db,'SELECT Station.id, Station.Ort, Station.Adresse, Testtyp.IsPCR FROM Station JOIN Testtyp ON Testtyp.id=Station.Testtyp_id WHERE '.$query_b2b.';');
	}
	// X ist Anzahl an Tagen für Vorschau in Tabelle
	if($mode == 'vaccinate') {
		$last_date_for_calendar=S_get_entry($Db,'SELECT Termine.Tag FROM Termine 
		JOIN Station ON Station.id=Termine.id_station
		WHERE Station.Firmencode=""
		ORDER BY Termine.Tag DESC LIMIT 1;');
		$diff=( strtotime($last_date_for_calendar) - strtotime(date('Y-m-d')) ) /(3600*24);
		$X=$diff+2;
		$Xx=28;
		if($X>28) {$X=$Xx;} // max. Xx days
	} elseif($mode == 'b2b-vaccinate') {
		$Xx=35;
		$X=$Xx;
	} else {
		$Xx=14;
		$X=$Xx;
	}
	// Ohne Terminbuchung für nächste X Tage
	$today=date('Y-m-d');
	$in_x_days=date('Y-m-d', strtotime($today. ' + '.$X.' days'));
	
	$bool_valid_appointments_found=false;
	$row_j=0;

	// Table
	$res_l_array[0][0]='
		<table class="FAIR-data" style="table-layout: fixed;">
		<tr>
		<td class="FAIR-data-height2 FAIR-data-right FAIR-data-left FAIR-data-top-noline"></td>';
	
	$res_l_array[1][0].='
	<tr>
    <td class="FAIR-data-height2 FAIR-data-right FAIR-data-left FAIR-data-top-noline"></td>';
	for($j=0;$j<$X;$j++) {
		if($j % 2) {
			$res_l_array[$j+2][0].='<tr class="FAIR-data-odd">';
		} else {
			$res_l_array[$j+2][0].='<tr>';
		}
		$string_date=A_get_day_name_2(date('w', strtotime($today. ' + '.$j.' days'))).'<br>'.date('d.m.', strtotime($today. ' + '.$j.' days'));
		$res_l_array[$j+2][0].='<td class="FAIR-data-height2 FAIR-data-right FAIR-data-left FAIR-data-top-noline FAIR-data-center1"><h5>'.$string_date.'</h5></td>';
		$string_date=A_get_day_name(date('w', strtotime($today. ' + '.$j.' days'))).' '.date('d.m.', strtotime($today. ' + '.$j.' days'));
		$res_s_array[$j][0]='<div class="cal-day-head">'.$string_date.'</div>';
	}

	$col_j=0;
	$col_st_j=0;
	$count_same_type_openslot=0;
	
	// START of appointments w/ slots
	if($flag_prereg!=0) {

		$pre_vacc_string='';
		$pre_vacc_no=0;
		$count_same_type=0;
		//$col_j=0;
		$count_same_vaccine=0;
		foreach($stations_array as $st) {
			if($mode == 'vaccinate' || $mode == 'b2b-vaccinate') {
				$cal_color='blue';
			} elseif($mode == 'antikoerper') {
				$cal_color='blue';
			} elseif($st[3]==1) {
				$cal_color='blue';
			} else {
				$cal_color='red';
			}
			$col_j++;
			// check if station has appointed times
			if( S_get_entry($Db,'SELECT id_station FROM Termine WHERE Slot>0 AND Tag>="'.$today.'" AND Tag<="'.$in_x_days.'" AND id_station='.$st[0].' LIMIT 1;')==$st[0]) {
				$location_thirdline_val=S_get_entry($Db,'SELECT Oeffnungszeiten FROM Station WHERE id='.$st[0].' LIMIT 1;');
				if($location_thirdline_val!='') {
					$display_location_thirdline='<br><span class="text-sm">Öffnungszeiten '.$location_thirdline_val.'</span>';
				} else {
					$display_location_thirdline='';
				}

				$row_j++;

				if($mode=='vaccinate' || $mode == 'b2b-vaccinate') {
					$string_location='<b>'.$st[4].'</b><br>'.$st[1].'<br><span class="text-sm">'.$st[2].'</span>';
					$string_location2=''.$st[1].'<br><span class="text-sm">'.$st[2].'</span>';
				} else {
					$string_location='<b>'.$st[1].'</b><br>'.$st[2].'';
					$string_location2=''.$st[1].'<br><span class="text-sm">'.$st[2].'</span>';
				}
				
				if($mode=='vaccinate' || $mode == 'b2b-vaccinate') {
					if($st[4]!=$pre_vacc_string) {
						$pre_vacc_string=$st[4];
						if($pre_vacc_no==5) {
							$pre_vacc_no=1;
						} else {
							$pre_vacc_no++;
						}
						$count_vaccine=$col_j;
						$station_color_head='FAIR-data-'.$cal_color.'head-t'.$pre_vacc_no;
						$station_color='FAIR-data-'.$cal_color.'head'.$pre_vacc_no;
						$count_same_vaccine=1;
					} else {
						//same vaccine
						$count_same_vaccine++;
					}
				} else {
					$station_color='FAIR-data-'.$cal_color.'head1';
					$station_color_head='FAIR-data-'.$cal_color.'head-t1';
					$count_same_type++;
				}

				$res_l_array[1][$col_j].='<td class="FAIR-data-height2 FAIR-data-right FAIR-data-left FAIR-data-bottom FAIR-data-top-noline '.$station_color_head.'"><div class="center_text">'.$string_location2.$display_location_thirdline.'</div></td>';
				if($mode=='vaccinate' || $mode == 'b2b-vaccinate') {
					$res_l_array[0][1+3*$count_vaccine-2]='<td class="FAIR-data-height2 FAIR-data-right FAIR-data-left FAIR-data-top-noline '.$station_color_head.'" colspan="';
					$res_l_array[0][1+3*$count_vaccine-1]=$count_same_vaccine; // colspan value
					if($st[6]>0) {
						$label_age='<div style="text-align: center; font-size: 14px;">(nur im Alter '.$st[5].' - '.$st[6].' Jahre)</div>';
					} else {
						$label_age='<div style="text-align: center; font-size: 14px;">(Mindestalter '.$st[5].' Jahre)</div>';
					}
					$res_l_array[0][1+3*$count_vaccine]='"><div class="center_text"><b>'.$st[4].'</b></div>'.$label_age.'</td>';
				}
				$col_st_j++;
				
				for($j=0;$j<$X;$j++) {
					$in_j_days=date('Y-m-d', strtotime($today. ' + '.$j.' days'));
					if($j==0) {
							// TODAY do not show past entries
							$current_hour=date('G');
							$current_slot=intval(date('i')/15)+1;
							$array_termine_open=S_get_multientry($Db,'SELECT count(id), count(Used) FROM Termine WHERE Slot>0 AND id_station='.$st[0].' AND Tag="'.$in_j_days.'" AND (Stunde>'.$current_hour.' OR (Stunde='.$current_hour.' AND Slot>'.$current_slot.'));');

					} else {
						$array_termine_open=S_get_multientry($Db,'SELECT count(id), count(Used) FROM Termine WHERE Slot>0 AND id_station='.$st[0].' AND Tag="'.$in_j_days.'";');
					}

					$count_free=$array_termine_open[0][0]-$array_termine_open[0][1];
					$display_termine='<div style="display: block; margin-top: 5px;"><span class="label label-success">'.($count_free).'</span></div><span class="text-sm"><div style="display: block; margin-top: 5px;">freie&nbsp;Termine</div></span>';
					if($count_free>0) {
						$string_times='';
						if($mode=='vaccinate' || $mode == 'b2b-vaccinate') {
							$string_times.=''.$st[1].'<br><span class="text-sm">'.$st[2].'</span>';
						}
						// opt location
						$array_location_opt=S_get_multientry($Db,'SELECT opt_station, opt_station_adresse FROM Termine WHERE Slot>0 AND id_station='.$st[0].' AND Tag="'.$in_j_days.'" ORDER BY Stunde ASC;');
						$string_location3='';
						if($mode=='vaccinate' || $mode == 'b2b-vaccinate') {
							$string_location3.='<b>'.$st[4].'</b><br>';
						}
						if($array_location_opt[0][0]!='') {
							$string_times='<span class="text-sm">'.$array_location_opt[0][0].',<br>'.$array_location_opt[0][1].'</span><br>';
							$string_location3.='<span class="text-sm">'.$array_location_opt[0][0].',<br>'.$array_location_opt[0][1].'</span><br>';
						} else {
							$string_location3.=$string_location;
						}
						
						// get times
						$value_termine_id=S_get_entry($Db,'SELECT id FROM Termine WHERE Slot>0 AND id_station='.$st[0].' AND Tag="'.$in_j_days.'" ORDER BY Stunde ASC LIMIT 1;');

						$res_l_array[$j+2][$col_j].='<td onclick="window.location=\''.$path_to_reg.'index.php?appointment='.($value_termine_id).'\'" class="FAIR-data-height2 FAIR-data-right FAIR-data-left FAIR-data-top-noline FAIR-data-center1 '.$station_color.' calendar'.$cal_color.'">'.$string_times.$display_termine.'</td>';
						$res_s_array[$j][1].='<div class="cal-element '.$station_color.' calendar'.$cal_color.'" onclick="window.location=\''.$path_to_reg.'index.php?appointment='.($value_termine_id).'\'">'.$string_location3.''.$display_termine.$label_age.'</div>';

						$bool_valid_appointments_found=true;
					} elseif($array_termine_open[0][0]>0) {
						// opt location
						$array_location_opt=S_get_multientry($Db,'SELECT opt_station, opt_station_adresse FROM Termine WHERE Slot>0 AND id_station='.$st[0].' AND Tag="'.$in_j_days.'" ORDER BY Stunde ASC;');
						if($array_location_opt[0][0]!='') {
							$string_location=$array_location_opt[0][0];
							if($mode=='vaccinate' || $mode == 'b2b-vaccinate') {
								$string_location4=$string_location;
							}
						} else {
							$string_location='<b>'.$st[1].'</b><br>'.$st[2].'';
							if($mode=='vaccinate' || $mode == 'b2b-vaccinate') {
								$string_location4=$st[1];
							} else {
								$string_location4='';
							}
						}
						$string_location3='';
						if($mode=='vaccinate' || $mode == 'b2b-vaccinate') {
							$string_location3.='<b>'.$st[4].'</b><br>';
						}
						if(!($mode=='vaccinate' || $mode == 'b2b-vaccinate')) {
							//$string_location3=$string_location;
							$string_location3='';
						}

						$res_l_array[$j+2][$col_j].='<td class="FAIR-data-height2 FAIR-data-right FAIR-data-left FAIR-data-top-noline FAIR-data-center1 FAIR-data-'.$cal_color.'3"><span class="text-sm"><div style="display: block; margin-bottom: 5px;"><b>'.$string_location4.'</b><br>alle Termine ausgebucht</div></span><div style="display: block; margin-top: 5px; margin-bottom: 7px;"><span class="label label-default">'.($array_termine_open[0][0]).'</span></div></td>';
						$res_s_array[$j][1].='<div class="cal-element"><div style="display: block; margin-top: 5px;">'.$string_location3.''.$string_location.'</div><span class="text-sm"><div style="display: block; margin-top: 5px;">Alle Termine ausgebucht</div></span><div style="display: block; margin-top: 5px; margin-bottom: 5px;"><span class="label label-default">'.($array_termine_open[0][0]).'</span></div></div>';
					} else {
						$res_l_array[$j+2][$col_j].='<td class="FAIR-data-height2 FAIR-data-right FAIR-data-left FAIR-data-top-noline FAIR-data-center1"></td>';
					}
				}
			}
		}
	}
	// END of appointments w/ slots



	// START of appointments w/o slots
	foreach($stations_array as $st) {
		if($mode == 'vaccinate' || $mode == 'b2b-vaccinate') {
			$cal_color='blue';
		}if($mode == 'antikoerper') {
			$cal_color='blue';
		} elseif($st[3]==1) {
			$cal_color='blue';
		} else {
			$cal_color='yellow';
		}
		$col_j++;
		// check if station has appointed times
		if( S_get_entry($Db,'SELECT id_station FROM Termine WHERE Slot is null AND Tag>="'.$today.'" AND Tag<="'.$in_x_days.'" AND id_station='.$st[0].' LIMIT 1;')==$st[0]) {

			$row_j++;
			if($mode=='vaccinate' || $mode == 'b2b-vaccinate') {
				$string_location='<b>'.$st[4].'</b><br>'.$st[1].'<br><span class="text-sm">'.$st[2].'</span>';
			} else {
				$string_location='<b>'.$st[1].'</b><br>'.$st[2].'';
				$string_location2=''.$st[1].'<br><span class="text-sm">'.$st[2].'</span>';
				$station_color='FAIR-data-'.$cal_color.'head1';
				$station_color_head2='FAIR-data-'.$cal_color.'head-t1';
			}
			$res_l_array[1][$col_j].='<td class="FAIR-data-height2 FAIR-data-right FAIR-data-left FAIR-data-bottom FAIR-data-top-noline '.$station_color_head2.'"><div class="center_text">'.$string_location2.'</div></td>';

			$count_same_type_openslot++;
			$col_st_j++;

			for($j=0;$j<$X;$j++) {
				$in_j_days=date('Y-m-d', strtotime($today. ' + '.$j.' days'));
				$array_termine_open=S_get_multientry($Db,'SELECT id,Startzeit, Endzeit, opt_station, opt_station_adresse FROM Termine WHERE Slot is null AND id_station='.$st[0].' AND Tag="'.$in_j_days.'" ORDER BY Startzeit ASC;');
				$string_times='';
				$string_times_small='';
				foreach($array_termine_open as $te) {
					if($te[3]!='') {
						if($mode == 'vaccinate' || $mode == 'b2b-vaccinate') {
							$string_times.='<span class="text-sm">'.$st[4].'<br>'.$te[3].'<br>'.$te[4].'</span><br>';
							$string_location_small='<b>'.$st[4].'</b><br>'.$te[3].', '.$te[4].'';
						} else {
							$string_times.='<span class="text-sm">'.$te[3].'<br>'.$te[4].'</span><br>';
							$string_location_small='<b>'.$te[3].'</b><br>'.$te[4].'';
						}
					} else {
						$string_location_small=$string_location;
					}
					$string_times.=date('H:i',strtotime($te[1])).' - '.date('H:i',strtotime($te[2])).'<br>';
					$string_times_small.=date('H:i',strtotime($te[1])).' - '.date('H:i',strtotime($te[2])).'<br>';
					if($mode=='b2b') {
						$string_times.='<span class="text-sm">Offener Termin</span><br>';
						$string_times_small.='<span class="text-sm">Offener Termin</span><br>';
					}
					$bool_valid_appointments_found=true;
				}
				
				if($string_times!='') {
					if($flag_prereg==0) {
						$res_l_array[$j+2][$col_j].='<td class="FAIR-data-height2 FAIR-data-right FAIR-data-left FAIR-data-top-noline FAIR-data-center1 '.$station_color.'">
						'.$string_times.'</td>';
						$res_s_array[$j][1].='<div class="cal-element calendar'.$cal_color.'">'.$string_location_small.'<br>'.$string_times_small.'</div>';

					} else {
						$res_l_array[$j+2][$col_j].='<td onclick="window.location=\''.$path_to_reg.'index.php?appointment='.$te[0].'\'" class="FAIR-data-height2 FAIR-data-right FAIR-data-left FAIR-data-center1 '.$station_color.' calendar'.$cal_color.'">'.$string_times.'</td>';
						$res_s_array[$j][1].='<div class="cal-element calendar'.$cal_color.'" onclick="window.location=\''.$path_to_reg.'index.php?appointment='.$te[0].'\'">'.$string_location_small.'<br>'.$string_times_small.'</div>';
					}
					
				} else {
					$res_l_array[$j+2][$col_j].='<td class="FAIR-data-height2 FAIR-data-right FAIR-data-left FAIR-data-top-noline FAIR-data-center1"></td>';
				}
			}
		}
		
		
	}
	// END of appointments w/o slots



	
	$jj=0;
	foreach($res_l_array as $u) {
		$res_l_array[$jj][]='
		</tr>
		';
		$jj++;
	}
	

	if($col_st_j<4) {
		// smaller table in width
		$res_l_array[0][0]='
		<table class="FAIR-data FAIR-data-table-md" style="table-layout: fixed;">
		<tr>
		<td class="FAIR-data-height2 FAIR-data-right FAIR-data-left FAIR-data-top-noline"></td>';
	} else {
		$res_l_array[0][0]='
		<table class="FAIR-data" style="table-layout: fixed;">
		<tr>
		<td class="FAIR-data-height2 FAIR-data-right FAIR-data-left FAIR-data-top-noline"></td>';
	}

	if($count_same_type>0) {
		if($mode == 'antikoerper' || $mode=='pcr') {
			$res_l_array[0][0].='<td class="FAIR-data-height2 FAIR-data-right FAIR-data-left FAIR-data-top-noline '.$station_color_head.'" colspan="';
			$res_l_array[0][0].=$count_same_type; // colspan value
			$res_l_array[0][0].='"><div class="center_text"><b>Terminbuchung erforderlich</b></div></td>';
		} else {
			$res_l_array[0][0].='<td class="FAIR-data-height2 FAIR-data-right FAIR-data-left FAIR-data-top-noline '.$station_color_head.'" colspan="';
			$res_l_array[0][0].=$count_same_type; // colspan value
			$res_l_array[0][0].='"><div class="center_text"><b>Terminbuchung empfohlen</b></div></td>';
		}
	}
	if($count_same_type_openslot>0) {
		$res_l_array[0][0].='<td class="FAIR-data-height2 FAIR-data-right FAIR-data-left FAIR-data-top-noline '.$station_color_head2.'" colspan="';
		$res_l_array[0][0].=$count_same_type_openslot; // colspan value
		$res_l_array[0][0].='"><div class="center_text"><b>Offene Termine, Voranmeldung möglich</b></div></td>';
	}

	

	if(!$bool_valid_appointments_found) {
		$res_l_array[2][0]='<tr><td class="FAIR-data-height1 FAIR-data-right FAIR-data-left FAIR-data-top FAIR-data-bottom FAIR-data-center1" colspan="'.($col_st_j+1).'"><b>Keine freien Termine in den nächsten '.$Xx.' Tagen gefunden</b></td></tr>'.$res_l_array[2][0];
		$res_s_array[$j][0].='<div class="cal-element"><div style="display: block; margin-top: 5px;"><b>Keine freien Termine in den nächsten '.$Xx.' Tagen gefunden</b></div></div>';
	}
	

	for($j=0;$j<$X;$j++) {
		$string_date=A_get_day_name_2(date('w', strtotime($today. ' + '.$j.' days'))).' '.date('d.m.', strtotime($today. ' + '.$j.' days'));
	}
    
	$res_l_array[$jj][]='
		</table>
		';
	

	S_close_db($Db);
	return array('',$res_s_array,$res_l_array);
}

?>