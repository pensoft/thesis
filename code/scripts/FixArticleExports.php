<?php	
$docroot = '../adm/';
require_once($docroot . "lib/conf.php");
require_once(PATH_ECMSFRCLASSES . 'static.php');


ReportMsg('Script started');
FixBrokenRecords();
ReportMsg('Script ended');

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
 * Тази функция гледа всички pid-овете на различните негенерирани/неъплоуднати експорти 
 * и ако някой pid го няма в процес лист-а, update-ва pid-овете на експортите
 * към този pid на 0
 * 
 */
function FixBrokenRecords(){
	$lCon = new DBCn();
	$lCon->Open();
	$lCon2 = new DBCn();
	$lCon2->Open();
		
	//Първо фиксваме генерирането
	$lSelectAllPidsSql = 'SELECT DISTINCT generate_pid FROM export_common WHERE generating_started = 1 AND is_generated = 0 AND generate_pid <> 0;';
	$lCon->Execute($lSelectAllPidsSql);
	$lCon->MoveFirst();
	while(!$lCon->Eof()){
		$lPid = (int)$lCon->mRs['pid'];		
		$lUpdateTaxonsSql = 'UPDATE export_common SET generate_pid = 0, generating_started = 0 WHERE generate_pid = ' . $lPid . ' AND generating_started = 1 AND is_generated = 0 ;';
		if(!CheckIfProcessExists($lPid)){//Ако процеса не съществува - ъпдейтваме reminder-ите
			$lCon2->Execute($lUpdateTaxonsSql);
			$lCon2->CloseRs();
		}
		$lCon->MoveNext();
	}
	
	//След това фиксваме ъплоудването
	$lCon->CloseRs();
	$lSelectAllPidsSql = 'SELECT DISTINCT upload_pid FROM export_common WHERE upload_started = 1 AND is_uploaded = 0 AND upload_pid <> 0;';
	$lCon->Execute($lSelectAllPidsSql);
	$lCon->MoveFirst();
	while(!$lCon->Eof()){
		$lPid = (int)$lCon->mRs['pid'];		
		$lUpdateTaxonsSql = 'UPDATE export_common SET upload_pid = 0, upload_started = 0 WHERE upload_pid = ' . $lPid . ' AND upload_started = 1 AND is_uploaded = 0 ;';
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
	$lCheckCommand = '/bin/ps auxw | grep ' . EXPORTS_SCRIPT_PROCESS_NAME . ' | grep ' . $pPid;
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