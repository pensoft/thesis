<?php
$docroot = getenv("DOCUMENT_ROOT");
require_once($docroot . '/lib/static.php');
session_write_close();

$gCoordinatesArr = ($_REQUEST['coordinates']);
$gLabels = ($_REQUEST['labels']);



//~ $lPattern = '/(?P<degs>[+-]?\d+?)\s*[°°]\s*(?P<minutes>(\d)+?(\.(\d)*)?)\s*[’\'`]((?P<seconds>(\d)+?(\.(\d)*)?)\s*[’\'`]{2})?\s*(?P<coord_name>[sewn])/iu';
//~ $lUrl = GOOGLE_MAPS_QUERY_URL . urlencode($gCoordinates);
$lParsedCoordinatesArr = array();
if(is_array( $gCoordinatesArr )){
	foreach( $gCoordinatesArr as $lNum => $gCoordinates ) {
		$gCoordinates = s($gCoordinates);
		//~ var_dump($gCoordinates);
		//~ if( preg_match_all($lPattern, $gCoordinates, $lMatch)){	
			//~ $lCoordinates = array();
			//~ for( $i = 0; $i < count($lMatch[0]); ++$i){
				//~ $lDegs = (int)$lMatch['degs'][$i];
				//~ $lMinutes = (float) $lMatch['minutes'][$i];
				//~ $lCoordName = $lMatch['coord_name'][$i];		
				//~ $lCoordinates[strtolower($lCoordName)] = array('degs' => $lDegs, 'minutes' => $lMinutes);
			//~ }
			//~ $lLatitude = getLatitude($lCoordinates);
			//~ $lLongitude = getLongitude($lCoordinates);
			//~ $lLabel = s($gLabels[$lNum]);	
			//~ $lLabel = str_replace(array('\'', '"', '\n'), array('', '', ' '), $lLabel);
			//~ $lLabel = preg_replace('/\s+/', ' ', $lLabel);
			//~ $lParsedCoordinatesArr[$lLabel] = array('latitude' => $lLatitude, 'longitude' => $lLongitude);
			
		//~ }
		$lCoordObj = new ccoordinates(array(
			'coord_string' => $gCoordinates
		));
		$lCoordObj->GetData();	
		$lLabel = s($gLabels[$lNum]);	
		$lLabel = str_replace(array('\'', '"', '\n'), array('', '', ' '), $lLabel);
		$lLabel = preg_replace('/\s+/', ' ', $lLabel);
		//~ var_dump($lCoordObj->GetLatitude());
		//~ var_dump($lCoordObj->GetLongitude());
		$lParsedCoordinatesArr[$lLabel] = array('latitude' => $lCoordObj->GetLatitude(), 'longitude' => $lCoordObj->GetLongitude());
	}	
}

if(!count($lParsedCoordinatesArr ) ){//Defaultno slagame ekvatora
	$lParsedCoordinatesArr['Equator'] = array('latitude' => 0, 'longitude' => 0);
}
$lJsonCoordinates =  json_encode(
	$lParsedCoordinatesArr
);
$lJsonCoordinates = str_replace(array('\'', '"', '\n'), array('\\\'', '\"', ' '), $lJsonCoordinates);
$t = array (
	'content' => array(
		'ctype' => 'csimple',
		'coordinates' => $lJsonCoordinates,
		'templs' => array(
			G_DEFAULT => 'external_details.googleMap',
		),
	),
);		



$inst = new cpage($t, array(G_MAINBODY => 'global.googleMap'));
$inst->Display();


//~ function getCoordinateValue($pDegs, $pMinutes, $pSeconds = 0){
	//~ $lResult = (int) $pDegs;
	//~ if( $lResult > 0 )
		//~ $lResult += (float)$pMinutes / 60 + (float) $pSeconds / 3600;
	//~ else
		//~ $lResult -= (float)$pMinutes / 60 - (float) $pSeconds / 3600;
	//~ return $lResult;
//~ }


//~ function getLatitude($pCoordArr){
	//~ if(!is_array($pCoordArr))
		//~ return 0;
	//~ if( array_key_exists('s', $pCoordArr)){
		//~ return -1 * getCoordinateValue($pCoordArr['s']['degs'], $pCoordArr['s']['minutes'], $pCoordArr['s']['seconds'] );
	//~ }elseif(array_key_exists('n', $pCoordArr)){
		//~ return  getCoordinateValue($pCoordArr['n']['degs'], $pCoordArr['n']['minutes'], $pCoordArr['n']['seconds']);
	//~ }
	//~ return 0;
//~ }

//~ function getLongitude($pCoordArr){
	//~ if(!is_array($pCoordArr))
		//~ return 0;
	//~ if( array_key_exists('e', $pCoordArr)){
		//~ return getCoordinateValue($pCoordArr['e']['degs'], $pCoordArr['e']['minutes'], $pCoordArr['e']['seconds']);
	//~ }elseif(array_key_exists('w', $pCoordArr)){
		//~ return -1 * getCoordinateValue($pCoordArr['w']['degs'], $pCoordArr['w']['minutes'], $pCoordArr['w']['seconds']);
	//~ }
	//~ return 0;
//~ }
?>