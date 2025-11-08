<?php

$POSITION_FILE = "posreps.csv";       // name of the file where position reports will be kept.
$tmnow = time();
$newData = "";
$fname = realpath('')."/".$POSITION_FILE;

$markers = getLeafletMarkers($fname);

if($markers == ""){
    exit("NO DATA");
}

print<<<_HTML
<!DOCTYPE html>
<html lang="en">
<head>
	<base target="_top">
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="refresh" content="20"> <!-- refresh page every 20 seconds -->
	
	<title>Map of Flights</title>
	
	<link rel="shortcut icon" type="image/x-icon" href="docs/images/favicon.ico" />

    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

	<style>
		html, body {
			height: 100%;
			margin: 0;
		}
		.leaflet-container {
			height: 400px;
			width: 600px;
			max-width: 100%;
			max-height: 100%;
		}
	</style>

	
</head>
<body>



<div id="map" style="width: 600px; height: 400px;"></div>
<script>

// Map and markers 
$markers

// Openstreet map tile definitions
const tiles = L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
	maxZoom: 19,
	attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
}).addTo(map);

</script>


</body>
</html>

_HTML
;

// This returns the leaflet markers and the map centered at the average
function getLeafletMarkers($fname){
    if(file_exists($fname)){
        
        $fdata = file($fname);
        $n = count($fdata);
        $markers = "";

        $sumlat = 0;
        $sumlon = 0;
        $numpos = 0;

        for($i = 0; $i < $n; $i++){
            $sar = explode(",",$fdata[$i]);
            $user = $sar[1];
            $lat =  $sar[2];
            $lon =  $sar[3];

            if(is_numeric($lat) && is_numeric($lon)){
                $marker = sprintf("const marker%d = L.marker([%s, %s]).addTo(map);\n",$i,$lat,$lon);
                $markers .= $marker;

                $sumlat += $lat;
                $sumlon += $lon;
                $numpos ++;
            }
    
        }

        if($numpos >= 1){
            $avglat = $sumlat / $numpos;
            $avglon = $sumlon / $numpos;

            $map = sprintf("const map = L.map('map').setView([%s, %s], 13);\n",$avglat,$avglon);

        }

        return $map.$markers;

    }else{
        return "";
    }
}





?>
