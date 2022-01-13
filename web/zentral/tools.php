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

function S_get_entry_voranmeldung ($Db,$scanevent) {
	$stmt=mysqli_prepare($Db,"SELECT id, Used FROM Voranmeldung WHERE Token=?;");
	mysqli_stmt_bind_param($stmt, "s", $scanevent);
	mysqli_stmt_execute($stmt);
	mysqli_stmt_bind_result($stmt, $id_voranmeldung, $used);
	mysqli_stmt_fetch($stmt);
	mysqli_stmt_close($stmt);
	// Check if number exists in table Voranmeldung
	if($used==1) {
		// bereits verwendet
		return "Used";
	} elseif($id_voranmeldung>0) {
		// Return result of SQL query
		return $id_voranmeldung;
	} else {
		return "Not registered";
	}
}

function S_get_cwa_qr_code ($Db,$test_id) {
	$test_array=S_get_multientry($Db,'SELECT Geburtsdatum, Vorname, Nachname, Registrierungszeitpunkt, Salt, Token, CWA_request, Testtyp.Device_ID FROM Vorgang JOIN Testtyp ON Vorgang.Testtyp_id=Testtyp.id WHERE Vorgang.id=CAST('.$test_id.' AS int);');
	if($test_array[0][6]==1) {
		// // personalized CWA
		// build hash
		$pre_hash=$test_array[0][0].'#'.$test_array[0][1].'#'.$test_array[0][2].'#'.strtotime($test_array[0][3]).'#'.$test_array[0][5].'#'.$test_array[0][4];
		$hash=hash("sha256",$pre_hash);
		// build json
		if($test_array[0][7]== null) {
			// // Check if Device_ID of RAT list exists, if not create QR Code without dgc attribute
		$json='{"dob":"'.$test_array[0][0].'","fn":"'.$test_array[0][1].'","ln":"'.$test_array[0][2].'","testid":"'.$test_array[0][5].'","timestamp":'.strtotime($test_array[0][3]).',"salt":"'.$test_array[0][4].'","hash":"'.$hash.'"}';
		}else{
		$json='{"dob":"'.$test_array[0][0].'","fn":"'.$test_array[0][1].'","ln":"'.$test_array[0][2].'","testid":"'.$test_array[0][5].'","timestamp":'.strtotime($test_array[0][3]).',"salt":"'.$test_array[0][4].'","dgc":true,"hash":"'.$hash.'"}';
		}
	} elseif($test_array[0][6]==2) {
		// // anonymous CWA
		// build hash
		$pre_hash=strtotime($test_array[0][3]).'#'.$test_array[0][4];
		$hash=hash("sha256",$pre_hash);
		// build json
		$json='{"timestamp":'.strtotime($test_array[0][3]).',"salt":"'.$test_array[0][4].'","hash":"'.$hash.'"}';
	}
	// build base64coded
	$base64=rtrim( strtr( base64_encode( $json ), '+/', '-_'), '=');
	return $base64;
}

function S_get_cwa_url ($Db,$test_id) {
	$test_array=S_get_multientry($Db,'SELECT Geburtsdatum, Vorname, Nachname, Registrierungszeitpunkt, Salt, Token, CWA_request, Testtyp.Device_ID FROM Vorgang JOIN Testtyp ON Vorgang.Testtyp_id=Testtyp.id WHERE Vorgang.id=CAST('.$test_id.' AS int);');
	if($test_array[0][6]==1) {
		// // personalized CWA
		// build hash
		$pre_hash=$test_array[0][0].'#'.$test_array[0][1].'#'.$test_array[0][2].'#'.strtotime($test_array[0][3]).'#'.$test_array[0][5].'#'.$test_array[0][4];
		$hash=hash("sha256",$pre_hash);
		// build json
		if($test_array[0][7]== null) {
			// // Check if Device_ID of RAT list exists, if not create QR Code without dgc attribute
		$json='{"dob":"'.$test_array[0][0].'","fn":"'.$test_array[0][1].'","ln":"'.$test_array[0][2].'","testid":"'.$test_array[0][5].'","timestamp":'.strtotime($test_array[0][3]).',"salt":"'.$test_array[0][4].'","hash":"'.$hash.'"}';
		}else{
		$json='{"dob":"'.$test_array[0][0].'","fn":"'.$test_array[0][1].'","ln":"'.$test_array[0][2].'","testid":"'.$test_array[0][5].'","timestamp":'.strtotime($test_array[0][3]).',"salt":"'.$test_array[0][4].'","dgc":true,"hash":"'.$hash.'"}';
		}
	} elseif($test_array[0][6]==2) {
		// // anonymous CWA
		// build hash
		$pre_hash=strtotime($test_array[0][3]).'#'.$test_array[0][4];
		$hash=hash("sha256",$pre_hash);
		// build json
		$json='{"timestamp":'.strtotime($test_array[0][3]).',"salt":"'.$test_array[0][4].'","hash":"'.$hash.'"}';
	}
	$base64=rtrim( strtr( base64_encode( $json ), '+/', '-_'), '=');
	$url = 'https://s.coronawarn.app?v=1#'. $base64;
	return $url; 
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
// Generate random salt
function A_generate_cwa_salt($length = 32) {
	// Generierte 128-Bit Zufallszahl in Hexadezimal-Darstellung, nur mit Großbuchstaben und fester Breite von 32 Stellen
$characters = '1234567890ABCDEF';
$randomString = '';
for ($i = 0; $i < $length; $i++) {
	$randomString .= $characters[rand(0, strlen($characters) - 1)];
}
return $randomString;
}

// Login for user with $uid
// $mode is for 'password', 'code', ...
function A_login($Db,$uid,$mode) {
    
	$_SESSION['uid'] = $uid;
	if($_SESSION['uid']=='') { die("Error in database. (Err:102)"); }
	
	$_SESSION['signedin'] = true;
	$_SESSION['username'] = S_get_entry($Db,'SELECT username FROM li_user WHERE id='.$uid.';');
	$_SESSION['station_id'] = S_get_entry($Db,'SELECT Station FROM li_user WHERE id='.$uid.';');
	if($_SESSION['station_id']>0) {
		$_SESSION['station_name'] = S_get_entry($Db,'SELECT Ort FROM Station WHERE id='.$_SESSION['station_id'].';');
		$business_code=S_get_entry($Db,'SELECT Firmencode FROM Station WHERE id='.$_SESSION['station_id'].';');
		if($business_code!='') { $_SESSION['station_business']=true; } else { $_SESSION['station_business']=false; }
	} else {
		$_SESSION['station_name']="";
		$_SESSION['station_business']=false;
	}

	/* Rollen
		1 - Teststation
		2 - Office
		3 - Gesundheitsamt
		4 - Admin
		5 - Gruppenleitung */
	$t = S_get_multientry($Db,'SELECT 0, role_1, role_2, role_3, role_4, role_5 FROM li_user WHERE id='.$uid.';');
	$_SESSION['roles']=$t[0];
	$_SESSION['display_sensitive']=0;

	if($mode!='check' && $mode!='chguserid') {
		// Cookie will expire after 12 hrs after log-in
		// PHP session will expire earlier if no site request
		$expiry = time() + 12*60*60;
		$data = (object) array( "un" => $_SESSION['username'], "pw" => S_get_entry($Db,'SELECT password_hash FROM li_user WHERE id='.$uid.'') );
		$cookieData = (object) array( "data" => $data, "expiry" => $expiry );
		setcookie('drk-cookie', json_encode( $cookieData ), $expiry);
	}
	
	//login lock reset > set failed attempts to 0
	S_set_data($Db,'UPDATE li_user SET login_attempts=0 WHERE id='.$uid.';');
	
	// Delete older login tokens
	S_set_data($Db,'DELETE FROM li_token WHERE id_user='.$uid.';');

    return true;
}

// Check if user with $uid is already logged in after standard session time is expired
function A_checkloggedin($Db,$username,$hash) {

	// Benutzername und Hash werden überprüft
	if($username!='') {
		$uid=S_get_entry($Db,'SELECT id FROM li_user WHERE lower(username)=\''.$username.'\'');
		if($uid>0) {
			$db_hash=S_get_entry($Db,'SELECT password_hash FROM li_user WHERE id='.$uid.'');
		} else {
			$db_hash='';
		}
		// Check correct password hash
		if ( $hash === $db_hash ) {
			A_login($Db,$uid,'check');
			return true;
		}
	}

    return false;
}

// Check if user role fits to requirements
function A_checkpermission($requirement) {
	$bool_permission=false;
	foreach($requirement as $b) {
		if($b>0 && $_SESSION['roles'][$b]==1) { 
			$bool_permission=true;
		}
	}
return $bool_permission;
}

function A_sanitize_input($input) {
	// strips any HTML and PHP tags
	strip_tags($input);

	// validate white listed chars in input (alphanumeric)
	$validated = "";
	if(preg_match("/^[a-zA-Z0-9\-]+$/", $input)) {
		$validated = $input;
	}
	
	return $validated;
}

function A_get_day_name($number_of_week) {
	$days=array('Sonntag','Montag','Dienstag','Mittwoch','Donnerstag','Freitag','Samstag');
	return $days[$number_of_week];
}
function A_get_day_name_2($number_of_week) {
	$days=array('So','Mo','Di','Mi','Do','Fr','Sa');
	return $days[$number_of_week];
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

// New administration calendar v2.0
function H_build_table_testdates_new_2_0($mode) {
	
	$res_l_array=array(); // for large displays - array for table [row=days][column=station]
	$res_v_array=array(); // for vaccine sum displays - array for table [row=days][column=station]
	$Db=S_open_db();
	if($mode == 'vaccinate') {
		$stations_array=S_get_multientry($Db,'SELECT Station.id, Station.Ort, Station.Adresse, Impfstoff.id, Impfstoff.Kurzbezeichnung, Station.Firmencode FROM Station
		JOIN Impfstoff ON Impfstoff.id=Station.Impfstoff_id
		ORDER BY Impfstoff.Kurzbezeichnung ASC, Station.Ort ASC;');
	} else {
		$stations_array=S_get_multientry($Db,'SELECT id, Ort, Adresse, 1, 1, Firmencode FROM Station;');
	}
	// X ist Anzahl an Tagen für Vorschau in Tabelle
	if($mode == 'vaccinate') {
		$X=42;
	} else {
		$X=28;
	}

	// Ohne Terminbuchung für nächste X Tage / free2come
	$today=date('Y-m-d');
	$in_x_days=date('Y-m-d', strtotime($today. ' + '.$X.' days'));
	$yesterday=date('Y-m-d', strtotime($today. ' -1 days'));
	
	$row_j=0;

	// Table
	$res_l_array[0][0]='
	<table class="FAIR-data" style="table-layout: fixed;">
	<tr>
	<td class="FAIR-data-height2 FAIR-data-right FAIR-data-left FAIR-data-top-noline"></td>';
	$res_v_array[0][0]=$res_l_array[0][0];
	
	$res_l_array[1][0]='
	<tr>
    <td class="FAIR-data-height2 FAIR-data-right FAIR-data-left FAIR-data-top-noline"></td>';
	
	// Print dates
	for($j=0;$j<$X+1;$j++) {
		if($j % 2) {
			$res_l_array[$j+2][0].='<tr class="FAIR-data-odd">';
		} else {
			$res_l_array[$j+2][0].='<tr>';
		}
		$string_date=A_get_day_name_2(date('w', strtotime($yesterday. ' + '.$j.' days'))).'<br>'.date('d.m.', strtotime($yesterday. ' + '.$j.' days'));
		if($j==0) {$string_date='Gestern';} elseif($j==1) {$string_date='Heute';}
		$res_l_array[$j+2][0].='<td class="FAIR-data-height2 FAIR-data-right FAIR-data-left FAIR-data-top-noline FAIR-data-center1"><h5>'.$string_date.'</h5></td>';
		$res_v_array[$j+2][0]=$res_l_array[$j+2][0];
	}

	$col_j=0;
	$col_st_j=0;
	$col_vacc_j=0;
	$count_same_type_openslot=0;
	if($mode == 'vaccinate' || $mode == 'b2b-vaccinate') {
		$cal_color='blue';
	} elseif($mode == 'antikoerper') {
		$cal_color='blue';
	} else {
		$cal_color='red';
	}
	
	$pre_vacc_string='';
	$pre_vacc_no=0;
	$count_same_type=0;
	$count_same_vaccine=0;
	$count_same_vaccine_is=array(); // counter for sum vaccine per day
	$count_same_vaccine_io=array(); // counter for open vaccine per day
	$count_same_vaccine_iu=array(); // counter for unused vaccine per day / no-show

	// START of appointments w/ slots
	foreach($stations_array as $st) {
		$col_j++;
		// check if station has appointed times
		if( S_get_entry($Db,'SELECT id_station FROM Termine WHERE Slot>0 AND Date(Tag)>="'.$yesterday.'" AND Date(Tag)<="'.$in_x_days.'" AND id_station='.$st[0].';')==$st[0]) {
			$row_j++;

			if($mode=='vaccinate' || $mode == 'b2b-vaccinate') {
				$string_location='<div><span class="text-sm"><b>'.$st[1].'</b></span></div>';
				$string_location2='(S'.sprintf('%02d',$st[0]).') '.$st[1].'<br><span class="text-sm">'.$st[2].'</span>';
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
					$count_same_vaccine=1; // Reset values for new vaccine in list
					$count_same_vaccine_is=array();
					$count_same_vaccine_io=array();
					$count_same_vaccine_iu=array();
				} else {
					//same vaccine
					$count_same_vaccine++;
				}
			} else {
				$string_location='<div><span class="text-sm"><b>'.$st[1].'</b></span></div>';
				$string_location2='(S'.sprintf('%02d',$st[0]).') '.$st[1].'<br><span class="text-sm">'.$st[2].'</span>';
				$station_color='FAIR-data-'.$cal_color.'head1';
				$station_color_head='FAIR-data-'.$cal_color.'head-t1';
				$count_same_type++;
			}

			$res_l_array[1][$col_j].='<td class="FAIR-data-height2 FAIR-data-right FAIR-data-left FAIR-data-bottom FAIR-data-top-noline '.$station_color_head.'"><div class="center_text">'.$string_location2.'</div></td>';

			// Firmencode Station
			if($st[5]!='') {
				$code_station='<div class="right-container" title="Nicht-öffentlicher Termin">
				<span class="red-square"><span class="icon-stop2"></span></span></div>';
			} else {
				$code_station='';
			}
			
			if($mode=='vaccinate' || $mode == 'b2b-vaccinate') {
				$res_l_array[0][1+3*$count_vaccine-2]='<td class="FAIR-data-height2 FAIR-data-right FAIR-data-left FAIR-data-top-noline '.$station_color_head.'" colspan="';
				$res_l_array[0][1+3*$count_vaccine-1]=$count_same_vaccine; // colspan value
				$res_l_array[0][1+3*$count_vaccine]='"><div class="center_text"><b>'.$st[4].'</b></div></td>';
				if($count_same_vaccine==1) {
					$res_v_array[0][1+$count_vaccine]='<td class="FAIR-data-height2 FAIR-data-right FAIR-data-left FAIR-data-top-noline '.$station_color_head.'"><div class="center_text"><b>'.$st[4].'</b></div></td>';
					$col_vacc_j++;
				}
			}
			$col_st_j++;

			$location_thirdline_val=S_get_entry($Db,'SELECT Oeffnungszeiten FROM Station WHERE id='.$st[0].';');
			if($location_thirdline_val!='') {
				$display_location_thirdline='<br><span class="text-sm">'.$location_thirdline_val.'</span>';
			} else {
				$display_location_thirdline='';
			}
			
			for($j=0;$j<=$X;$j++) {
				$in_j_days=date('Y-m-d', strtotime($yesterday. ' + '.$j.' days'));
				$array_termine_open=S_get_multientry($Db,'SELECT count(id), count(Used) FROM Termine WHERE Slot>0 AND id_station='.$st[0].' AND Date(Tag)="'.$in_j_days.'";');
				$count_free=$array_termine_open[0][0]-$array_termine_open[0][1];
				if( $count_free==0 ) {
					$label_free='default';
				} elseif( ($count_free/$array_termine_open[0][0])<0.2 || $count_free<3) {
					$label_free='warning';
				} else {
					$label_free='success';
				}
				$display_termine='<div style="display: block; margin-top: 5px; margin-bottom: 8px;"><span class="label label-'.$label_free.'">'.($count_free).' von '.$array_termine_open[0][0].'</span></div>';
				// How many have registered and not shown up
				if($j<2) {
					if($j==0) {
						$current_hour=24;
					} else {
						$current_hour=date('G');
					}
					$value_reservation_unused=S_get_entry($Db,'SELECT count(Voranmeldung.id) FROM Voranmeldung JOIN Termine ON Termine.id=Voranmeldung.Termin_id WHERE Termine.Slot>0 AND Termine.id_station='.$st[0].' AND Date(Termine.Tag)="'.$in_j_days.'" AND Termine.Stunde<'.$current_hour.' AND Voranmeldung.Used=0;');
					$display_termine.='<div style="display: block; margin-top: 5px;"><span class="label label-danger">'.sprintf('%01d',$value_reservation_unused).'</span></div>
					<span class="text-sm"><div style="display: block; margin-top: 5px; margin-bottom: 8px;">no-show</div></span>';
					$count_same_vaccine_iu[$j]+=$value_reservation_unused;
				}
				$count_same_vaccine_io[$j]+=$count_free;
				$count_same_vaccine_is[$j]+=$array_termine_open[0][0];
				
				if($count_free>0) {
					$string_times='';
					// opt location
					$array_location_opt=S_get_multientry($Db,'SELECT opt_station, opt_station_adresse FROM Termine WHERE Slot>0 AND id_station='.$st[0].' AND Date(Tag)="'.$in_j_days.'" ORDER BY Stunde ASC;');
					if($array_location_opt[0][0]!='') {
						$string_times.='<span class="FAIR-text-sm">'.$array_location_opt[0][0].',<br>'.$array_location_opt[0][1].'</span><br>';
					}
					// get times
					$value_termine_times1=S_get_entry($Db,'SELECT Stunde FROM Termine WHERE Slot>0 AND id_station='.$st[0].' AND Date(Tag)="'.$in_j_days.'" ORDER BY Stunde ASC;');
					$value_termine_times2=S_get_entry($Db,'SELECT Stunde FROM Termine WHERE Slot>0 AND id_station='.$st[0].' AND Date(Tag)="'.$in_j_days.'" ORDER BY Stunde DESC;');
					//$value_termine_id=S_get_entry($Db,'SELECT id FROM Termine WHERE Slot>0 AND id_station='.$st[0].' AND Date(Tag)="'.$in_j_days.'" ORDER BY Stunde ASC;');
					$string_times.='<span class="text-sm"><div style="display: block; margin-top: 5px;">'.sprintf('%02d', $value_termine_times1).':00 - '.sprintf('%02d', $value_termine_times2 + 1).':00</div></span>';


					$res_l_array[$j+2][$col_j].='<td onclick="window.location=\'terminlist.php?station='.($st[0]).'&date='.$in_j_days.'\'" class="FAIR-data-height2 FAIR-data-right FAIR-data-left FAIR-data-top-noline FAIR-data-center1 '.$station_color.' calendar'.$cal_color.'">
					'.$string_location.$string_times.$display_termine.$code_station.'</td>';
				} elseif($array_termine_open[0][0]>0) {
					$string_times='';
					// opt location
					$array_location_opt=S_get_multientry($Db,'SELECT opt_station, opt_station_adresse FROM Termine WHERE Slot>0 AND id_station='.$st[0].' AND Date(Tag)="'.$in_j_days.'" ORDER BY Stunde ASC;');
					if($array_location_opt[0][0]!='') {
						$string_times.='<span class="FAIR-text-sm">'.$array_location_opt[0][0].',<br>'.$array_location_opt[0][1].'</span><br>';
					}
					// get times
					$value_termine_times1=S_get_entry($Db,'SELECT Stunde FROM Termine WHERE Slot>0 AND id_station='.$st[0].' AND Date(Tag)="'.$in_j_days.'" ORDER BY Stunde ASC;');
					$value_termine_times2=S_get_entry($Db,'SELECT Stunde FROM Termine WHERE Slot>0 AND id_station='.$st[0].' AND Date(Tag)="'.$in_j_days.'" ORDER BY Stunde DESC;');
					//$value_termine_id=S_get_entry($Db,'SELECT id FROM Termine WHERE Slot>0 AND id_station='.$st[0].' AND Date(Tag)="'.$in_j_days.'" ORDER BY Stunde ASC;');
					$string_times.='<span class="text-sm"><div style="display: block; margin-top: 5px;">'.sprintf('%02d', $value_termine_times1).':00 - '.sprintf('%02d', $value_termine_times2 + 1).':00</div></span>';

					$res_l_array[$j+2][$col_j].='<td onclick="window.location=\'terminlist.php?station='.($st[0]).'&date='.$in_j_days.'\'" class="FAIR-data-height2 FAIR-data-right FAIR-data-left FAIR-data-center1 '.$station_color.' calendar'.$cal_color.'">'.$string_location.$string_times.$display_termine.$code_station.'</td>';
				} else {
					$res_l_array[$j+2][$col_j].='<td class="FAIR-data-height2 FAIR-data-right FAIR-data-left FAIR-data-top-noline FAIR-data-center1"></td>';
				}

				if($count_same_vaccine_is[$j]>0) {
					if( $count_same_vaccine_io[$j]==0 ) {
						$label_free='default';
					} elseif( ($count_same_vaccine_io[$j]/$count_same_vaccine_is[$j])<0.2 || $count_same_vaccine_io[$j]<3) {
						$label_free='warning';
					} else {
						$label_free='success';
					}
					if($j<2) {
						// display no-shows
						$display_noshow='<div style="display: block; margin-top: 5px;"><span class="label label-danger">'.sprintf('%01d',$count_same_vaccine_iu[$j]).'</span></div><span class="text-sm"><div style="display: block; margin-top: 5px; margin-bottom: 8px;">no-show</div></span>';
					} else {
						$display_noshow='';
					}
					// Print box entry
					$display_termine='<div style="display: block; margin-top: 5px; margin-bottom: 8px;"><span class="label label-'.$label_free.'">'.($count_same_vaccine_io[$j]).' von '.$count_same_vaccine_is[$j].'</span></div>';
					$res_v_array[$j+2][$st[3]]='<td class="FAIR-data-height2 FAIR-data-right FAIR-data-left FAIR-data-center1 '.$station_color.'">'.$display_termine.'<div class="text-sm">freie Termine</div>'.$display_noshow.'</td>';
				} else {
					$res_v_array[$j+2][$st[3]]='<td class="FAIR-data-height2 FAIR-data-right FAIR-data-left FAIR-data-top-noline FAIR-data-center1"></td>';
				}
			}
		}
	}
	
	// END of appointments w/ slots
	
	// START of appointments w/o slots
	$cal_color_wo='yellow';
	if($mode!='get_id_for_zip') {
		
		foreach($stations_array as $st) {
			// check if station has appointed times
			if( S_get_entry($Db,'SELECT id_station FROM Termine WHERE Slot is null AND Date(Tag)>="'.$yesterday.'" AND Date(Tag)<="'.$in_x_days.'" AND id_station='.$st[0].';')==$st[0]) {
				$location_thirdline_val=S_get_entry($Db,'SELECT Oeffnungszeiten FROM Station WHERE id='.$st[0].';');
				if($location_thirdline_val!='') {
					$display_location_thirdline='<br><span class="text-sm">'.$location_thirdline_val.'</span>';
				} else {
					$display_location_thirdline='';
				}
				$row_j++;
				if($mode=='vaccinate' || $mode == 'b2b-vaccinate') {
					$string_location='<b>'.$st[4].'</b><br>'.$st[1].'<br><span class="text-sm">'.$st[2].'</span>';
					$string_location2=''.$st[1].'<br><span class="text-sm">'.$st[2].'</span>';
					$station_color='FAIR-data-'.$cal_color_wo.'head1';
					$station_color_head='FAIR-data-'.$cal_color_wo.'head-t1';
				} else {
					$string_location='<div><span class="text-sm"><b>'.$st[1].'</b></span></div>';
					$string_location2='(S'.sprintf('%02d',$st[0]).') '.$st[1].'<br><span class="text-sm">'.$st[2].'</span>';
					$station_color='FAIR-data-'.$cal_color_wo.'head1';
					$station_color_head='FAIR-data-'.$cal_color_wo.'head-t1';
				}
	
				
				$count_same_type_openslot++;
	
				$res_l_array[1][$col_j].='<td class="FAIR-data-height2 FAIR-data-right FAIR-data-left FAIR-data-bottom FAIR-data-top-noline '.$station_color_head.'"><div class="center_text">'.$string_location2.$display_location_thirdline.'</div></td>';
				
				
				$col_st_j++;
	
				for($j=0;$j<=$X;$j++) {
					$in_j_days=date('Y-m-d', strtotime($yesterday. ' + '.$j.' days'));
					$array_termine_open=S_get_multientry($Db,'SELECT id,Startzeit, Endzeit, opt_station, opt_station_adresse FROM Termine WHERE Slot is null AND id_station='.$st[0].' AND Date(Tag)="'.$in_j_days.'" ORDER BY Startzeit ASC;');
					$string_times='';
	
					
	
					foreach($array_termine_open as $te) {
						if($te[3]!='') {
							$string_times.='<span class="text-sm">'.$te[3].',<br>'.$te[4].'</span><br>';
						}
						if($mode!='get_id_for_zip') {
							// How many have registered for this free2come appointment
							$value_reservation=S_get_entry($Db,'SELECT count(id) FROM Voranmeldung WHERE Termin_id='.$te[0].';');
							if($j>0) {
								$display_termine='<br><div style="display: block; margin-top: 5px;"><span class="label label-primary">'.sprintf('%01d',$value_reservation).'</span></div><span class="text-sm"><div style="display: block; margin-top: 5px;">Reservierungen</div></span>';
							} else {
								$display_termine='';
							}
							
							// How many have registered and not shown up
							if($j<2) {
								$value_reservation_used=S_get_entry($Db,'SELECT count(id) FROM Voranmeldung WHERE Termin_id='.$te[0].' AND Used=1;');
								$display_termine.='<div style="display: block; margin-top: 5px;"><span class="label label-danger">'.sprintf('%01d',$value_reservation-$value_reservation_used).'</span></div>
								<span class="text-sm"><div style="display: block; margin-top: 5px;">no-show</div></span>';
							}
						}
						if($mode=='get_id_for_zip') { $click_event='<div onclick="window.location=\'sammeltestung.php?termin='.$te[0].'\'">'; }  else { $click_event=''; }
						$string_times.=$click_event.date('H:i',strtotime($te[1])).' - '.date('H:i',strtotime($te[2])).$display_termine.'<br>';
						if($mode=='get_id_for_zip') { $click_event='</div>'; }  else { $click_event=''; }
						$string_times.=$click_event;
					}
					
					if($string_times!='') {
						if($mode=='get_id_for_zip') {
							$res_l_array[$j+2][$col_j].='<td class="FAIR-data-height2 FAIR-data-right FAIR-data-left FAIR-data-top-noline FAIR-data-center1 '.$station_color.' calendar'.$cal_color_wo.'">'.$string_times.'</td>';
						} else {
							$res_l_array[$j+2][$col_j].='<td onclick="window.location=\'terminlist.php?station='.($st[0]).'&date='.$in_j_days.'\'" class="FAIR-data-height2 FAIR-data-right FAIR-data-left FAIR-data-top-noline FAIR-data-center1 '.$station_color.' calendar'.$cal_color_wo.'">'.$string_location.$string_times.'</td>';
						}
						
					} else {
						$res_l_array[$j+2][$col_j].='<td class="FAIR-data-height2 FAIR-data-right FAIR-data-left FAIR-data-top-noline FAIR-data-center1"></td>';
					}
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
		$res_v_array[$jj][]='
		</tr>
		';
		$jj++;
	}

	if($col_st_j<5) {
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
	if($col_vacc_j<5) {
		// smaller table in width
		$res_v_array[0][0]='
		<table class="FAIR-data FAIR-data-table-md" style="table-layout: fixed;">
		<tr>
		<td class="FAIR-data-height2 FAIR-data-right FAIR-data-left FAIR-data-top-noline"></td>';
	} else {
		$res_v_array[0][0]='
		<table class="FAIR-data" style="table-layout: fixed;">
		<tr>
		<td class="FAIR-data-height2 FAIR-data-right FAIR-data-left FAIR-data-top-noline"></td>';
	}

	if($count_same_type>0) {
		$station_color_head='FAIR-data-'.$cal_color.'head-t1';
		$res_l_array[0][0].='<td class="FAIR-data-height2 FAIR-data-right FAIR-data-left FAIR-data-top-noline '.$station_color_head.'" colspan="';
		$res_l_array[0][0].=$count_same_type; // colspan value
		$res_l_array[0][0].='"><div class="center_text"><b>Terminbuchung</b></div></td>';

	}
	if($count_same_type_openslot>0) {
		$station_color_head='FAIR-data-'.$cal_color_wo.'head-t1';
		$res_l_array[0][0].='<td class="FAIR-data-height2 FAIR-data-right FAIR-data-left FAIR-data-top-noline '.$station_color_head.'" colspan="';
		$res_l_array[0][0].=$count_same_type_openslot; // colspan value
		$res_l_array[0][0].='"><div class="center_text"><b>Offene Termine, Voranmeldung möglich</b></div></td>';
	}
	
	
	$res_l_array[$jj][]='
		</table>
		';
	$res_v_array[$jj][0]='
	</table>
	';

	S_close_db($Db);
	return array($res_l_array, $res_v_array);
}

function A_qr_code($type,$param) {
	include_once 'lib/phpqrcode/full/qrlib.php';
    
	if($type == 'CWA'){
	$size=6;
	$codeText = 'https://s.coronawarn.app?v=1#'.$param;
    // // outputs image directly into browser, as PNG stream
    //return QRcode::png($codeText,false,QR_ECLEVEL_H,$size,4);
	return QRcode::svg($codeText); 
	}
	elseif($type == 'result'){
	$codeText = $param;
	return QRcode::svg($codeText); 
	}
	else{
	$codeText = $param;
	return QRcode::svg($codeText); 
	}

	
}


?>