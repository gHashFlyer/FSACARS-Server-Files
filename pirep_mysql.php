<?php

// Generic example of how to populate an sql database with data from fsACARS
// Note - this has not been thoroughly tested and should be used only as a guide.
//==============================================================================
$SERVERLOGBOOK = "serverlogbook.csv"; // this is for backup logging

// mySQL Credentials
include("sql_creds.php");

//Initialize mySQL
//===================================================================
$hostname = $sql_creds_HOSTNAME;
$database = $sql_creds_DATABASE;
$username = $sql_creds_USERNAME;
$password = $sql_creds_PASSWORD;
$sqlicnxn = mysqli_connect($hostname, $username, $password, $database);

if (mysqli_connect_errno()) {
   die("Connect failed: %s\n" + mysqli_connect_error());
   exit;
}

$test = false;

if($test){

	$datestamp = 111;
	$user = "FDX873";
	$orig_icao = "ABCD";
	$dest_icao = "EFGH";
	$blockminutes = 234;
	$actualNM = 123;
	$aircraft = "Mower 718";
	//Add flight data to the database (uses PDO)
	//===================================================================
	$sqlcmd = "INSERT INTO logbook (datestamp, user, orig, dest, blocktime, distance, aircraft) VALUES (?,?,?,?,?,?,?)";
	$sqli = $sqlicnxn->prepare($sqlcmd);
	$sqli->bind_param("sssssss", $datestamp, $user, $orig_icao, $dest_icao, $blockminutes, $actualNM, $aircraft);
	$sqli->execute();
	$sqli->close();


	// Log flight report to server-side delimited file
	//===================================================================
	$logentry = sprintf("%s||%s||%s||%s||%s||%s||%s||", $datestamp, $user, $orig, $dest, $blocktime, $actualNM, $aircraft);	

	$fname = $SERVERLOGBOOK;
	$fptr = fopen($fname,"a");
	fprintf($fptr,"%s\n",$logentry);
	fclose($fptr);


	//Return #RXOK# for fsacars to acknowledge the report was received.
	//===================================================================
	$server_message = "Server received flight report";
	$url = "#RXOK#|$server_message|";

	exit($url);	
}
// Collect data from fsACARS
// For a complete list of datapoints sent from acars see pirep.php
//===================================================================
$user      =  $_REQUEST['user'];      //username
$datestamp =  $_REQUEST['datestamp']; //epoch date of flight (e.g., 1751389172)

$lat1  =  $_REQUEST['lat1'];      //origin latitude, decimal degrees
$lon1  =  $_REQUEST['lon1'];      //origin longitude, decimal degrees
$lat2   =  $_REQUEST['lat3'];      //destination latitude, decimal degrees
$lon2   =  $_REQUEST['lon3'];      //destination longitude, decimal degrees

//Replace commas, if they exist, with dots.
$latstart  = floatval(str_replace(',', '.', $lat1));
$lonstart  = floatval(str_replace(',', '.', $lon1));
$latstop   = floatval(str_replace(',', '.', $lat2));
$lonstop   = floatval(str_replace(',', '.', $lon2));

// Geolocate nearest airports
//===================================================================
$orig = getICAO($latstart,$lonstart);
$dest = getICAO($latstop,$lonstop);

$actualNM  =  $_REQUEST['actualNM'];  //flight path distance, NM
$aircraft  =  $_REQUEST['aircraft'];  //aircraft titleline
$blocktime =  $_REQUEST['blocktime']; //block time, decimal hours
$blockminutes = intval(60 * $blocktime); // block time, minutes


// Log flight report to server-side delimited file
//===================================================================
$logentry = sprintf("%s||%s||%s||%s||%s||%s||%s||%s,%s||%s,%s||", $datestamp, $user, $orig, $dest, $blocktime, $actualNM, $aircraft,$latstart,$lonstart,$latstop,$lonstop);

$fname = $SERVERLOGBOOK;
$fptr = fopen($fname,"a");
fprintf($fptr,"%s\n",$logentry);
fclose($fptr);

//Add flight data to the database (uses PDO)
//===================================================================
$sqlcmd = "INSERT INTO logbook (datestamp, user, orig, dest, blocktime, distance, aircraft) VALUES (?,?,?,?,?,?,?)";
$sqli = $sqlicnxn->prepare($sqlcmd);
$sqli->bind_param("sssssss", $datestamp, $user, $orig, $dest, $blockminutes, $actualNM, $aircraft);
$sqli->execute();
$sqli->close();





//Return #RXOK# for fsacars to acknowledge the report was received.
//===================================================================
$server_message = "Server received flight report";
$url = "#RXOK#|$server_message|";

exit($url);

//This function will return the closest ICAO code, and append the distance
// in nautical miles from the closest ICAO code if greater than 10 NM.
//-----------------------------------------------------------------------
function getICAO($lat, $lon)
{

	$mindist = 999;
	$minicao = "UNKN";

	$handle = fopen("airports.csv", "r");
	if ($handle) {
		while (($line = fgets($handle)) !== false) {
			$sar = explode(",",$line);
			if($sar[0] == 'ident') continue;
			$d = GetDistance($lat, $lon, $sar[1], $sar[2]);

			if($d < $mindist){
				$mindist = $d;
				$minicao = $sar[0];
			}			
		}

		fclose($handle);
	}

	// if($mindist > 10) $minicao = sprintf("CVN/%s%d",$minicao,$mindist);

	return $minicao;
}


function GetDistance($lat1,$lon1,$lat2,$lon2)
{
  $lat1 = floatval($lat1) / 180 * pi();
  $lon1 = floatval($lon1) / 180 * pi();
  $lat2 = floatval($lat2) / 180 * pi();
  $lon2 = floatval($lon2) / 180 * pi();
  $dlon = $lon2 - $lon1;
  $dlat = $lat2 - $lat1;
  $a = sin($dlat/2) * sin($dlat/2) + cos($lat1) * cos($lat2) * sin($dlon/2) * sin($dlon/2);
  $b = 2 * atan2(sqrt($a), sqrt(1-$a));
  $nm = 0.54 * 6366.707 * $b;
  return $nm;
}

?>