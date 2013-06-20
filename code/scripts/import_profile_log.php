<?php
$docroot = getenv('DOCUMENT_ROOT');
require_once($docroot . "/lib/conf.php");
require_once(PATH_ECMSFRCLASSES . 'static.php');
 
ReportMsg('Script started');

//~ if (is_file(PROFILE_LOG_LOCK)) {
	//~ ReportMsgAndExit('Another import is running!!!', 0);		
//~ } else {
	//~ touch(PROFILE_LOG_LOCK);
//~ }

if( !defined('PROFILE_LOG_FILE_OLD' ) ){
	ReportMsgAndExit('No Log File');	
}
$gCon = Con();

$gLogContents = file_get_contents(PROFILE_LOG_FILE_OLD);

ImportReports($gLogContents);
ReportMsg('Script finished');
//~ unlink(PROFILE_LOG_LOCK);


function ImportReports($pLogContent){//Razdelqme sydyrjanieto na reporti po opredelen regexp pattern i gi obrabotvame 1 po 1
	ini_set('pcre.backtrack_limit', mb_strlen($pLogContent));	
	//~ $lReportPattern =  preg_quote(PROFILE_LOG_START_ESCAPE) ;
	
	$lReportPattern =  preg_quote(PROFILE_LOG_START_ESCAPE . PROFILE_LOG_START_TAXON_LABEL . PROFILE_LOG_DATA_VAL_ESCAPE ) . '(?P<taxonName>.*?)' . preg_quote(PROFILE_LOG_DATA_VAL_ESCAPE 
		. PROFILE_LOG_START_FROM_IP_LABEL . PROFILE_LOG_DATA_VAL_ESCAPE ) . '(?P<ip>.*?)' . preg_quote( PROFILE_LOG_DATA_VAL_ESCAPE 
		. PROFILE_LOG_START_ON_DATE_LABEL . PROFILE_LOG_DATA_VAL_ESCAPE ) . '(?P<date>.*?)' . preg_quote( PROFILE_LOG_DATA_VAL_ESCAPE 
		. PROFILE_LOG_START_ESCAPE );
	$lReportPattern .= '(?P<reportContent>.*?)';	
	$lReportPattern .= preg_quote(PROFILE_LOG_END_ESCAPE . PROFILE_LOG_END_TAXON_LABEL . PROFILE_LOG_DATA_VAL_ESCAPE ) . '\g{taxonName}' . preg_quote( PROFILE_LOG_DATA_VAL_ESCAPE . PROFILE_LOG_END_FROM_IP_LABEL . PROFILE_LOG_DATA_VAL_ESCAPE ) . '\g{ip}' . preg_quote( PROFILE_LOG_DATA_VAL_ESCAPE . PROFILE_LOG_END_ON_DATE_LABEL . PROFILE_LOG_DATA_VAL_ESCAPE) . '\g{date}' . preg_quote(PROFILE_LOG_DATA_VAL_ESCAPE . PROFILE_LOG_END_ESCAPE);	
	
	//~ var_dump(ini_get('pcre.backtrack_limit'));
	//~ var_dump($lReportPattern);
	if( !preg_match_all('/' . $lReportPattern . '/is', $pLogContent, $lReports) ){
		ReportMsgAndExit('No Reports found');
	}	
	
	//~ var_dump(count($lReports[0]));
	//~ exit;

	foreach( $lReports[0] as $lMatchNum => $lMatchText){
		//~ var_dump($lMatchText);
		$lReportTaxon = trim($lReports['taxonName'][$lMatchNum]);		
		$lReportIp = trim($lReports['ip'][$lMatchNum]);		
		$lReportDate = trim(DisplayPostgresDate($lReports['date'][$lMatchNum]));		
		$lReportContent = $lReports['reportContent'][$lMatchNum];				
		ImportSingleReport($lReportContent, $lReportTaxon, $lReportIp, $lReportDate);
	}
}

function ImportSingleReport($pReportContent, $pTaxonName, $pIp, $pDate){//Razdelqme reporta na message-i po opredelen regexp pattern i gi obrabotvame 1 po 1
	$lMsgPattern =  preg_quote(PROFILE_LOG_MSG_START_ESCAPE . 'IP: ' . PROFILE_LOG_DATA_VAL_ESCAPE . $pIp  . PROFILE_LOG_DATA_VAL_ESCAPE);	
	$lMsgPattern .= '(?P<msgContent>.*?)';
	$lMsgPattern .= preg_quote(PROFILE_LOG_MSG_END_ESCAPE);
	
	if( !preg_match_all('/' . $lMsgPattern . '/ism', $pReportContent, $lReportMessages) ){
		return;
	}

	foreach( $lReportMessages[0] as $lMsgNum => $lSingleMsg){		
		$lMsgContent = $lReportMessages['msgContent'][$lMsgNum];		
		ImportSingleReportMsg($lMsgContent, $pTaxonName, $pIp, $pDate);
		//~ echo "\n\n\n\n";
		//~ exit;
	}
}

function ImportSingleReportMsg($pMsg, $pTaxonName, $pIp, $pDate){//Vzimame dannite ot syobhstenieto i gi vkarvame v bazata
	$lMsgPattern = preg_quote(PROFILE_LOG_OBJECTID_LABEL) . '\s*' . preg_quote(PROFILE_LOG_DATA_VAL_ESCAPE ) . '(?P<objectId>.*?)' . preg_quote(PROFILE_LOG_DATA_VAL_ESCAPE) . '\s*' ;
	$lMsgPattern .= preg_quote(PROFILE_LOG_OBJECT_NAME_LABEL) . '\s*' . preg_quote( PROFILE_LOG_DATA_VAL_ESCAPE ) . '(?P<objectName>.*?)' . preg_quote(PROFILE_LOG_DATA_VAL_ESCAPE) . '\s*' ;
	$lMsgPattern .= preg_quote(PROFILE_LOG_OBJECT_PARENTID_LABEL) . '\s*' . preg_quote( PROFILE_LOG_DATA_VAL_ESCAPE ) . '(?P<objectParentId>.*?)' . preg_quote(PROFILE_LOG_DATA_VAL_ESCAPE) . '\s*' ;
	$lMsgPattern .= preg_quote(PROFILE_LOG_OBJECT_PARAMETERS_LABEL) . '\s*' . preg_quote( PROFILE_LOG_DATA_VAL_ESCAPE ) . '(?P<objectParams>.*?)' . preg_quote(PROFILE_LOG_DATA_VAL_ESCAPE) . '\s*' ;
	
	$lMsgPattern .= preg_quote(PROFILE_LOG_STARTED_LABEL) . '\s*' . preg_quote( PROFILE_LOG_DATA_VAL_ESCAPE ) . '(?P<objectStarted>.*?)' . preg_quote(PROFILE_LOG_DATA_VAL_ESCAPE) . '.*?' ;
	$lMsgPattern .= preg_quote(PROFILE_LOG_DATA_FROM_CACHE_LABEL) . '\s*' . preg_quote( PROFILE_LOG_DATA_VAL_ESCAPE ) . '(?P<objectGetDataFromCache>.*?)' . preg_quote(PROFILE_LOG_DATA_VAL_ESCAPE) . '.*?' ;
	$lMsgPattern .= preg_quote(PROFILE_LOG_FINISHED_RETRIEVING_LABEL) . '\s*' . preg_quote( PROFILE_LOG_DATA_VAL_ESCAPE ) . '(?P<objectRetrieved>.*?)' . preg_quote(PROFILE_LOG_DATA_VAL_ESCAPE) . '.*?' ;
	$lMsgPattern .= preg_quote( PROFILE_LOG_DATA_VAL_ESCAPE ) . '(?P<objectRetrievementTime>.*?)' . preg_quote(PROFILE_LOG_DATA_VAL_ESCAPE) . '.*?' ;
	$lMsgPattern .= preg_quote(PROFILE_LOG_FINISHED_PARSING_LABEL) . '\s*' . preg_quote( PROFILE_LOG_DATA_VAL_ESCAPE ) . '(?P<objectParsed>.*?)' . preg_quote(PROFILE_LOG_DATA_VAL_ESCAPE) . '.*?' ;
	$lMsgPattern .= preg_quote( PROFILE_LOG_DATA_VAL_ESCAPE ) . '(?P<objectParsingTime>.*?)' . preg_quote(PROFILE_LOG_DATA_VAL_ESCAPE) . '.*?' ;
	
	if( !preg_match('/' . $lMsgPattern . '/ism', $pMsg, $lMsgDetails) ){
		trigger_error(date('d/m/Y H:i:s') . " Wrong msg format for taxon $pTaxonName, ip $pIp, date $pDate!\n", E_USER_NOTICE);
		return;
	}
	$lObjectId = q($lMsgDetails['objectId']);
	$lObjectName = q($lMsgDetails['objectName']);
	$lObjectParentId = q($lMsgDetails['objectParentId']);
	$lObjectParams = q($lMsgDetails['objectParams']);
	$lObjectStarted = q(DisplayPostgresDate($lMsgDetails['objectStarted']));
	$lObjectGotDataFromCache = q($lMsgDetails['objectGetDataFromCache']);
	$lObjectRetrieved = q(trim(DisplayPostgresDate($lMsgDetails['objectRetrieved'])));
	$lObjectRetrievementTime = (float)(trim($lMsgDetails['objectRetrievementTime']));
	$lObjectParsed = q(trim(DisplayPostgresDate($lMsgDetails['objectParsed'])));
	$lObjectParsingTime = (float)($lMsgDetails['objectParsingTime']);

	global $gCon;
	
	$lSql = 'SELECT * FROM spProfileLogMsg(1, null, \'' . q($pIp) . '\', \'' . q($pTaxonName) . '\', \'' . q($pDate) . '\', 
		\'' . $lObjectId . '\', \'' . $lObjectName . '\', \'' . $lObjectParentId . '\', \'' . $lObjectParams . '\',
		\'' . $lObjectStarted . '\', \'' . $lObjectGotDataFromCache . '\', 
		\'' . $lObjectRetrieved . '\', ' . $lObjectRetrievementTime . ',
		\'' . $lObjectParsed . '\', ' . $lObjectParsingTime . ')';
	$gCon->Execute($lSql);
	//~ var_dump($lSql);
	//~ echo 1;
	//~ var_dump($lObjectId);
	//~ var_dump($lObjectName);
	//~ var_dump($lObjectParams);
	//~ var_dump($lObjectStarted);
	//~ var_dump($lObjectGotDataFromCache);
	//~ var_dump($lObjectRetrieved);
	//~ var_dump($lObjectRetrievementTime);
	//~ var_dump($lObjectParsed);
	//~ var_dump($lObjectParsingTime);
	
}


function DisplayPostgresDate($pDateStr){
	$lDateTime = new DateTime( $pDateStr );
	return $lDateTime->format('d M Y H:i:s');
}

function ReportMsg($pMsg){
	$pMsg = date('d/m/Y H:i:s') . ' - '. $pMsg . " \n";
	echo $pMsg;
}

function ReportMsgAndExit($pMsg, $pUnlinkLock = 1){
	ReportMsg($pMsg);
	ReportMsg('Script finished');
	//~ if( $pUnlinkLock )
		//~ unlink(PROFILE_LOG_LOCK);
	exit(0);
}

?>