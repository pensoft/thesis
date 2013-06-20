<?php	
$docroot = '../adm/';
require_once($docroot . "lib/conf.php");
require_once(PATH_ECMSFRCLASSES . 'static.php');


ReportMsg('Script started');
ProcessTaxons();
FixBrokenRecords();
ReportMsg('Script ended');

/**
 * 
 * Обработваме таксоните. За целта първо маркираме необработените таксони
 * като такива който се обработват от текущия процес и после за всеки от тях изпращаме мейл.
 */
function ProcessTaxons(){
	$lPid = getmypid();
	$lCon = new DBCn();
	$lCon->Open();
	/**
	 * Тази конекция ще ни трябва понеже ще ъпдейтваме
	 * всеки reminder поединично, понеже не може да връщаме изпращане на мейл
	 * @var unknown_type
	 */
	$lUpdateCon = new DBCn();
	$lUpdateCon->Open();
	//Ползваме 1 cURL конекция за да не хабим време в преинициализацията и за всеки таксон
	$lCurlConnection = curl_init();
	curl_setopt($lCurlConnection, CURLOPT_RETURNTRANSFER, 1);

	
	$lMarkTaxonsForProcessingSql = '
		UPDATE taxon_cache
		SET 
			pid = ' . (int) $lPid . ' 
		WHERE id IN (
			SELECT id 
			FROM taxon_cache
			WHERE state = 0 AND pid = 0 
			ORDER BY createdate ASC
			LIMIT ' . (int) TAXON_CACHE_SCRIPT_RECORDS_COUNT . '
		)
	';
	
	$lCon->Execute($lMarkTaxonsForProcessingSql);	
	$lCon->CloseRs();
	$lSelectTaxonsSql = 'SELECT r.id, r.taxon_name
		FROM taxon_cache r
		WHERE r.pid = ' . (int) $lPid . ' AND r.state = 0
	';
	$lCon->Execute($lSelectTaxonsSql);
	$lCon->MoveFirst();
	while(!$lCon->Eof()){
		$lRecordId = (int)$lCon->mRs['id'];
		$lTaxonName = $lCon->mRs['taxon_name'];
		$lUpdateTaxonSql = 'UPDATE taxon_cache r SET state = 1 WHERE id = ' . $lRecordId; 
		$lTaxonCacheLink = PTP_URL . '?query=' . $lTaxonName;
		
		ReportMsg('Generating cache for taxon ' . $lTaxonName);
		
		curl_setopt($lCurlConnection, CURLOPT_URL, $lTaxonCacheLink);
		$lCache = curl_exec($lCurlConnection);
		
		
		if(!$lUpdateCon->Execute($lUpdateTaxonSql)){//Ако се счупи нещо репортваме грешка и спираме
			ReportError('Could not update record #' . $lRecordId);
		}		
		$lUpdateCon->CloseRs();
		$lCon->MoveNext();
	}
	curl_close($lCurlConnection);
}



/**
 * Вадим съобщението за грешка и спираме
 */
function ReportError($pErrMsg){
	ReportMsg('Error:' . $pErrMsg);
	exit;
}

/**
 * Вадим съобщение в лога - понеже може да работят няколко скриптове ще вадим и pid-a на скрипта
 * @param unknown_type $pMsg
 */
function ReportMsg($pMsg){
	$lPid = getmypid();
	echo date('d/m/Y H:i:s') . ' Script# ' . $lPid . ' ' . $pMsg . "\n";
}

/**
 * 
 * Тази функция гледа всички pid-овете на различните негенерирани таксони
 * и ако някой pid го няма в процес лист-а, update-ва pid-овете на таксоните
 * към този pid на 0
 *
 * Освен това и трие всички стари обработени записи за да не се претрупва таблицата
 */
function FixBrokenRecords(){
	$lCon = new DBCn();
	$lCon->Open();
	$lCon2 = new DBCn();
	$lCon2->Open();
	
	$lClearTableSql = 'DELETE FROM taxon_cache WHERE state = 1 AND createdate < (CURRENT_TIMESTAMP - \'1 month\'::interval);';
	$lCon->Execute($lClearTableSql);
	$lCon->CloseRs();
	
	$lSelectAllPidsSql = 'SELECT DISTINCT pid FROM taxon_cache WHERE state = 0 AND pid <> 0;';
	$lCon->Execute($lSelectAllPidsSql);
	$lCon->MoveFirst();
	while(!$lCon->Eof()){
		$lPid = (int)$lCon->mRs['pid'];		
		$lUpdateTaxonsSql = 'UPDATE taxon_cache SET pid = 0 WHERE pid = ' . $lPid . ' AND state = 0;';
		if(!CheckIfProcessExists($lPid)){//Ако процеса не съществува - ъпдейтваме reminder-ите
			$lCon2->Execute($lUpdateTaxonsSql);
			$lCon2->CloseRs();
		}
		$lCon->MoveNext();
	}
}

/**
 * 
 * Гледа дали процес за генериране на кешове с подадения пид съществува(върви) и връща съответно true/false
 * @param unknown_type $pPid
 */
function CheckIfProcessExists($pPid){
	$lCheckCommand = '/bin/ps auxw | grep ' . REMINDERS_SCRIPT_PROCESS_NAME . ' | grep ' . $pPid;
	$lResult = exec($lCheckCommand, $lOutput); 
	
	/**
	 * Тъй като за изпълняване на exec се създава нов процес (sh -c) който съдърга целия експрешън
	 * отгоре горната команда винаго го връща като резултат(той отговаря на греп условията)
	 * Затова искаме да има поне 2 резултата за да считаме, че процеса още върви
	 */
	if( count($lOutput) < 2)
		return false;
	return true;
}
?>