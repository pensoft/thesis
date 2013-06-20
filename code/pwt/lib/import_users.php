<?php

//~ error_reporting(0);

// трябва да се сме с postgres user
define('PGDB_SRV', 'localhost');;
//~ define('PGDB_DB', 'pensoft_test1'); // ORIGINALNATA BAZA
define('PGDB_DB', 'pensoft_test1_importusr'); // TESTOVATA BAZA
define('PGDB_USR', 'postgres');
define('PGDB_PASS', '1bur_bur');
define('PGDB_PORT', '5432');
define('DEF_DBTYPE', 'postgres');

$gDontRedirectToLogin = 1;
$docroot = getenv('DOCUMENT_ROOT');
require_once($docroot . '/lib/static.php');

$lCon = Con();

// CSV FILES
$CLIENTS_CSV = $docroot . '/lib/csv/CLIENTS_table_24_11_2012.csv';
$E_ALERTS_CSV = $docroot . '/lib/csv/E_ALERTS_table_24_11_2012.csv';
//~ $J_PRAVA_CSV = $docroot . '/lib/csv/J_PRAVA_table.csv';
$J_PROCESS_CSV = $docroot . '/lib/csv/J_PROCESS_table_24_11_2012.csv';
$J_ARTICLE_CSV = $docroot . '/lib/csv/J_ARTICLE_table_24_11_2012.csv';


if (file_exists($CLIENTS_CSV)) {
 	// EMPTY TEMP TABLE
	$lCon->Execute('TRUNCATE clients_temp');
	
	// COPY CSV CONTENT TO TABLE
	$lCon->Execute('copy clients_temp from \'' . $CLIENTS_CSV . '\'  CSV QUOTE AS \'"\' escape as \'\\\';');
	
	if(!$lCon->GetLastError()) {
		echo $CLIENTS_CSV . ' CSV import successful <br />';
	} else {
		echo $lCon->GetLastError();
	}
	
}
if(file_exists($E_ALERTS_CSV)) {
    // EMPTY TEMP TABLE
	$lCon->Execute('TRUNCATE e_alerts_temp');
	
	// COPY CSV CONTENT TO TABLE
	$lCon->Execute('copy e_alerts_temp from \'' . $E_ALERTS_CSV . '\' CSV QUOTE AS \'"\' escape as \'\\\';');
	
	if(!$lCon->GetLastError()) {
		echo $E_ALERTS_CSV . ' CSV import successful <br />';
	} else {
		echo $lCon->GetLastError();
	}
	
}
if(file_exists($J_PROCESS_CSV)) {

    // EMPTY TEMP TABLE
	$lCon->Execute('TRUNCATE j_process_temp');
	
	// COPY CSV CONTENT TO TABLE
	$lCon->Execute('copy  j_process_temp from \'' . $J_PROCESS_CSV . '\'  CSV QUOTE AS \'"\' escape as \'\\\';');
	
	if(!$lCon->GetLastError()) {
		echo $J_PROCESS_CSV . ' CSV import successful <br />';
	} else {
		echo $lCon->GetLastError();
	}
}
if(file_exists($J_ARTICLE_CSV)) {

    // EMPTY TEMP TABLE
	$lCon->Execute('TRUNCATE j_article_temp');
	
	// COPY CSV CONTENT TO TABLE
	$lCon->Execute('copy j_article_temp from \'' . $J_ARTICLE_CSV . '\'  CSV QUOTE AS \'"\' escape as \'\\\';');
	
	if(!$lCon->GetLastError()) {
		echo $J_ARTICLE_CSV . ' CSV import successful <br />';
	} else {
		echo $lCon->GetLastError();
	}
}

//~ truncate usr_addresses;
//~ delete from usr where id > 727;

//~ --select * from spInsertOldPJSUsers();

//~ // Процедура която обработва информацията

//~ $lCon->Execute('SELECT * FROM spInsertOldPJSUsers()');

//~ if(!$lCon->GetLastError()) {
	//~ echo ' OLD USERS ARE INSERTED <br />';
//~ } else {
	//~ echo $lCon->GetLastError();
//~ }


exit;


?>