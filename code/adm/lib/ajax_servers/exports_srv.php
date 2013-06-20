<?php
include_once(getenv("DOCUMENT_ROOT") . "/lib/static.php");

if(!(int)$user->id)
	exit;
	
ini_set('display_errors', 'Off');
$gExportType = (int)$_REQUEST['export_type'];
$gJournalId = (int)$_REQUEST['journal_id'];
$gIssue = (int)$_REQUEST['issue'];
$gAction = trim($_REQUEST['action']);

$lResult = array();
$lResult['err_cnt'] = 0;
try{
	switch($gAction){
		default:
			throw new Exception( getstr('admin.exports.srv.unknownAction') );
			break;
		case 'generate':
			GenerateExports($gExportType, $gJournalId, $gIssue);
			$lResult['msg'] = getstr('admin.exports.successful_generation');
			break;
		case 'upload':
			UploadExports($gExportType, $gJournalId, $gIssue);			
			$lResult['msg'] = getstr('admin.exports.successful_upload');
			break;		
	}
}catch(Exception $pException){
	$lResult['err_cnt'] = 1;
	$lResult['err_msg'] = $pException->getMessage();
}

returnAjaxResult($lResult);

/**
	Генерираме експортите от дадения тип за това списание и брой
*/
function GenerateExports($pExportType, $pJournalId, $pIssue){
	$lPid = getmypid();
	//Тук не трябва да ползваме Con() понеже може във някоя от функциите да се ползва Con() и да ни затрие стейта
	$lCon = new DBCn();
	$lCon->Open();
	$lUpdateSql = 'UPDATE export_common e SET
		generating_started = 1,
		generate_pid = ' . $lPid . '
	FROM finalized_articles_exports fe
	JOIN finalized_articles fa ON fa.article_id = fe.article_id
	JOIN articles a ON a.id = fa.article_id
	WHERE fe.export_id = e.id AND fe.export_type_id = ' . (int)$pExportType . ' 
		AND a.journal_id = ' . (int)$pJournalId . ' AND fa.article_journal_issue = ' . (int)$pIssue . '
		AND generating_started = 0 AND is_generated = 0
	';
	$lCon->Execute($lUpdateSql);
	$lSelectSql = 'SELECT e.id 
		FROM export_common e
		JOIN finalized_articles_exports fe ON fe.export_id = e.id AND fe.export_type_id = ' . (int)$pExportType . ' 
		JOIN finalized_articles fa ON fa.article_id = fe.article_id
		JOIN articles a ON a.id = fa.article_id
		WHERE
			a.journal_id = ' . (int)$pJournalId . ' AND fa.article_journal_issue = ' . (int)$pIssue . '
			AND generating_started = 1 AND is_generated = 0
			AND generate_pid = ' . $lPid
	;
	$lCon->Execute($lSelectSql);
	$lCon->MoveFirst();
	$lStrict = true;
	$lSaveToDb = true;
	
	while(!$lCon->Eof()){
		$lExportId = (int) $lCon->mRs['id'];	
		//~ var_dump($lExportId);
		//Ако гръмне генерирането - това е само при проверката дали някой др не генерира експорта, реално нищо не сме маркирали в базата за този експорт и продължаваме нататък
		try{
			GenerateExportXml($lExportId, $lSaveToDb, false, $lStrict);
		}catch(Exception $lException){
			//~ echo $lException->GetMessage() . $pExportId;
		}
		$lCon->MoveNext();
	}	
}

/**
	Ъплоудваме експортите от дадения тип за това списание и брой
	За целта е необходимо всички от тези експорти вече да са генерирани
*/
function UploadExports($pExportType, $pJournalId, $pIssue){	
	$lPid = getmypid();
	//Тук не трябва да ползваме Con() понеже може във някоя от функциите да се ползва Con() и да ни затрие стейта
	$lCon = new DBCn();
	$lCon->Open();
	//Първо гледаме дали всички експорти са генерирани
	$lCheckSql = 'SELECT coalesce(min(e.is_generated), 1) as generated, count(*) as count
		FROM export_common e
		JOIN finalized_articles_exports fe ON fe.export_id = e.id AND fe.export_type_id = ' . (int)$pExportType . '
		JOIN finalized_articles fa ON fa.article_id = fe.article_id
		JOIN articles a ON a.id = fa.article_id
		WHERE a.journal_id = ' . (int)$pJournalId . ' AND fa.article_journal_issue = ' . (int)$pIssue;
	$lCon->Execute($lCheckSql);
	$lCon->MoveFirst();
	if(!(int)$lCon->mRs['generated']){
		//~ throw new Exception(getstr('admin.exports.someExportsAreNotGenerated') . (int)$lCon->mRs['generated'] . $lCheckSql);
		throw new Exception(getstr('admin.exports.someExportsAreNotGenerated'));
	}
	
	$lUpdateSql = 'UPDATE export_common e SET
		upload_started = 1,
		upload_pid = ' . $lPid . '
	FROM finalized_articles_exports fe
	JOIN finalized_articles fa ON fa.article_id = fe.article_id
	JOIN articles a ON a.id = fa.article_id
	WHERE fe.export_id = e.id AND fe.export_type_id = ' . (int)$pExportType . ' 
		AND a.journal_id = ' . (int)$pJournalId . ' AND fa.article_journal_issue = ' . (int)$pIssue . '
		AND upload_started = 0 AND is_uploaded = 0 AND is_generated = 1
	';
	$lCon->Execute($lUpdateSql);
	
	
	$lSelectSql = 'SELECT e.id 
		FROM export_common e
		JOIN finalized_articles_exports fe ON fe.export_id = e.id AND fe.export_type_id = ' . (int)$pExportType . ' 
		JOIN finalized_articles fa ON fa.article_id = fe.article_id
		JOIN articles a ON a.id = fa.article_id
		WHERE
			a.journal_id = ' . (int)$pJournalId . ' AND fa.article_journal_issue = ' . (int)$pIssue . '
			AND upload_started = 1 AND is_uploaded = 0 AND is_generated = 1
			AND upload_pid = ' . $lPid
	;
	$lCon->Execute($lSelectSql);
	$lCon->MoveFirst();
	
	while(!$lCon->Eof()){
		$lExportId = (int) $lCon->mRs['id'];
		
		//Ако гръмне генерирането - това е само при проверката дали някой др не генерира експорта, реално нищо не сме маркирали в базата за този експорт и продължаваме нататък
		try{
			UploadExportXml($lExportId);
		}catch(Exception $lException){
		
		}
		$lCon->MoveNext();
	}	
}


?>