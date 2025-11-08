<?php

// Generic flight report receiver.
// For example usage with mySQL database see mysql_pirep.php


$SERVERLOGBOOK = "serverlogbook.csv";

$user = $_REQUEST['user']; //username
$pass =  $_REQUEST['pass']; //user password
$datestamp =  $_REQUEST['datestamp']; //epoch date of flight (e.g., 1751389172)
$fhash =  $_REQUEST['fhash']; //flight hash, matches fhash sent in each position report
$version =  $_REQUEST['version']; //acars software version
$verf =  $_REQUEST['verf']; //deprecated, now a constant value = 'FSACARS'
$auth =  $_REQUEST['auth']; //server authentication code

$aircraft =  $_REQUEST['aircraft']; //aircraft titleline
$atcData  =  $_REQUEST['atcData']; //delimited atc data: "/Identifier/Flight Number/Airline/Type/"
$atcModel  = $_REQUEST['atcModel'];  //aircraft model (typically an ICAO identifier)

// This can be helpful to identify the aircraft if
// the $aircraft value is incorrect.
$sar = explode("/",$atcdata);
$atcIdentifer    = $sar[0];
$atcFlightNumber = $sar[1];
$atcAirline      = $sar[2];
$atcType         = $sar[3];

$lat1 =  $_REQUEST['lat1']; //origin latitude, decimal degrees
$lon1 =  $_REQUEST['lon1']; //origin longitude, decimal degrees
$lat2  =  $_REQUEST['lat2']; //landing latitude, decimal degrees
$lon2  =  $_REQUEST['lon2']; //landing longitude, decimal degrees
$lat3  =  $_REQUEST['lat3']; //destination latitude, decimal degrees (location wheree user presses Stop)
$lon3  =  $_REQUEST['lon3']; //destination longitude, decimal degrees  (location wheree user presses Stop)

//Replace commas, if they exist, with dots.
$latstart = floatval(str_replace(',', '.', $lat1));
$lonstart = floatval(str_replace(',', '.', $lon1));
$latland  = floatval(str_replace(',', '.', $lat2));
$lonland  = floatval(str_replace(',', '.', $lon2));
$latstop  = floatval(str_replace(',', '.', $lat3));
$lonstop  = floatval(str_replace(',', '.', $lon3));

// actual times
$timeout = $_REQUEST['timeout']; //HHmm (UTC)
$timeoff = $_REQUEST['timeoff']; //HHmm (UTC)
$timeon = $_REQUEST['timeon']; //HHmm (UTC)
$timein = $_REQUEST['timein']; //HHmm (UTC)

//flight simulator times
$fstimeout = $_REQUEST['fstimeout']; //HHmm (UTC)
$fstimeoff = $_REQUEST['fstimeoff']; //HHmm (UTC)
$fstimeon = $_REQUEST['fstimeon']; //HHmm (UTC)
$fstimein = $_REQUEST['fstimein']; //HHmm (UTC)


$blocktime =  $_REQUEST['blocktime']; //block time, decimal hours
$airtime   =  $_REQUEST['airtime'];   //enroute time, decimal hours
$directNM  =  $_REQUEST['directNM'];  //great circle distance, NM
$actualNM  =  $_REQUEST['actualNM'];  //flight path distance, NM


$dateshort = $_REQUEST['dateshort']; //Date in the following format: MM/DD/YY
$datestamp = $_REQUEST['datestamp']; //Unix epoch UTC

$fuelstart =  $_REQUEST['fuelstart']; //starting fuel weight, pounds
$fuelstop =  $_REQUEST['fuelstop']; //ending fuel weight, pounds
$fueldiff =  $fuelstart - $fuelstop;

$landingKTS =  $_REQUEST['landingKTS']; //landing speed, knots
$landingFPM =  $_REQUEST['landingFPM']; //landing vertical speed, feet per minute
$landingFR =  $_REQUEST['landingFR']; // flight rule: VFR, MVFR, IFR, or LIFR
$landingG =  $_REQUEST['landingG']; // flight rule: VFR, MVFR, IFR, or LIFR
$landingGear =  $_REQUEST['landingGear']; // 1=gear down

$crashed = $_REQUEST['crashed']; //crash detection, 1=crashed.
$warpNM = $_REQUEST['warpNM']; //unusual movement detection, NM
$pause =  $_REQUEST['pause']; // pause tally, seconds
$slew =  $_REQUEST['slew']; //slew tally, seconds
$stall =  $_REQUEST['stall']; //stall tally, seconds
$nofuel = $_REQUEST['nofuel']; //nofuel detection,1=out of fuel
$overspeed = $_REQUEST['overspeed']; //aircraft overspeed tally, seconds
$speed10K = $_REQUEST['speed10K']; //speed greater than 250 KTS below 10000 FT tally, seconds
$simrate = $_REQUEST['simrate']; //simrate not equal to 1 tally, seconds
$refuel = $_REQUEST['refuel']; //refuel detection, 1=refueled.

$takeoffLBS =  $_REQUEST['takeoffLBS']; //takeoff weight, pounds
$landingLBS =  $_REQUEST['landingLBS']; //landing weight, pounds
$payload   = $_REQUEST['payload'];  //difference landing weight and fuel remaining

$takeoffFR   =  $_REQUEST['takeoffFR'];  // flight rule: VFR, MVFR, IFR, or LIFR
$takeoffWind = $_REQUEST['windstart']; // wind direction (DDD) and speed (S) token in metar format (DDDSS)
$landingWind = $_REQUEST['windstop'];  //wind direction (DDD) and speed (S) token in metar format (DDDSS)

$fsVersion =  $_REQUEST['fsver'];  //flight sim version as reported by fsacars (note that fsuipc sends x-plane version as FSX)

$oew     =  $_REQUEST['oew'];  //operating empty weight, pounds
$zfw     =  $_REQUEST['zfw'];  //zero fuel weight, pounds

$enroutefuel  = $_REQUEST['enroutefuel'];    // from takeoff to landing, lbs
$taxifuel     = $_REQUEST['taxifuel'];       // blockfuel minus enroutefuel
$blockfuel    = $_REQUEST['blockfuel'];      // total fuel used, lbs
$rollout      =  $_REQUEST['rollout'];  //rollout from wheels down to 30 knots ground speed.
$gmax         = $_REQUEST['gmax'];           // maximum g-force
$gmin         = $_REQUEST['gmin'];           // minimum g-force

$takeoffHWC = $_REQUEST['takeoffHWC']; // takeoff headwind component
$takeoffXWC = $_REQUEST['takeoffXWC']; // takeoff crosswind component
$landingHWC = $_REQUEST['landingHWC']; // landing headwind component
$landingXWC = $_REQUEST['landingXWC']; // landing crosswind component

$wdavg      = $_REQUEST['wdavg'];      // average wind heading
$wsavg      = $_REQUEST['wsavg'];      // average windspeed
$hwavg      = $_REQUEST['hwavg'];      // average enroute wind (+headwind, -tailwind)

// Flap settings are specific to each aircraft.
// For example, 737-800 with flaps set to  5 degrees: flaps 3
// For example, 737-800 with flaps set to 40 degrees: flaps 8
$takeoffflaps = $_REQUEST['takeoffflaps']; // takeoff flap setting (0=no flaps)
$landingflaps = $_REQUEST['landingflaps']; // landing flap setting (0=no flaps)

$omaxtaxi = $_REQUEST['omaxtaxi']; // maximum taki speed prior to takeoff
$dmaxtaxi = $_REQUEST['dmaxtaxi']; // maximum taxi speed after landing

$takeoffKTS = $_REQUEST['takeoffKTS']; // takeoff airspeed, knots

$trueheading = $_REQUEST['land_truehdg']; //true heading on landing
$timoday =  $_REQUEST['timoday']; //time of day: Day, Night

$nightlog = $_REQUEST['nightlog']; //time of day: Day, Night

$pirep =  $_REQUEST['pirep']; //flight comments, string


//Get nearest airports 
//===================================================================
$orig = getICAO($latstart,$lonstart);
$dest = getICAO($latstop,$lonstop);


// Log flight report to server-side delimited file
// For example usage with mySQL database see pirep_mysql.php
//===================================================================
$logentry = sprintf("%s||%s||%s||%s||%s||%s||%s||%s,%s||%s,%s||", $datestamp, $user, $orig, $dest, $blocktime, $actualNM, $aircraft,$latstart,$lonstart,$latstop,$lonstop);	

$fname = $SERVERLOGBOOK;
$fptr = fopen($fname,"a");
fprintf($fptr,"%s\n",$logentry);
fclose($fptr);

//Return #RXOK# for fsacars to acknowledge the report was received.
//===================================================================
$server_message = "SQL server received flight report";
$url = "#RXOK#|$server_message|";

exit($url);

// ------------------------------------------------------------------------------------------
// END
// ------------------------------------------------------------------------------------------


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

