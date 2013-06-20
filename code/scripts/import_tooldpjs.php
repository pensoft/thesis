<?php
exit();

$docroot = getenv('DOCUMENT_ROOT');
require_once($docroot . '/lib/static.php');
error_reporting(E_ALL);
ini_set('display_errors', '1');


$gCon = Con();
$gSqlStr = 'SELECT
				u.id, u.uname, u.first_name, u.middle_name, u.last_name, u.affiliation, u.departament, u.addr_street, 
				u.addr_postcode, u.addr_city, u.phone, u.fax, u.vat, u.website, max(t.name) as salut, max(types.name) as ctip, max(c.name) as country, max(f.name) as alertsfreq,
				(CASE WHEN product_types[1] IS NULL THEN 0 ELSE 1 END) as bookstype,
				(CASE WHEN product_types[2] IS NULL THEN 0 ELSE 1 END) as ebookstype,
				(CASE WHEN product_types[3] IS NULL THEN 0 ELSE 1 END) as journalstype
				FROM usr u
				left JOIN usr_titles t ON u.usr_title_id = t.id
				left JOIN client_types types ON u.client_type_id = types.id 
				left JOIN countries c ON u.country_id = c.id
				left JOIN usr_alerts_frequency f ON u.usr_alerts_frequency_id = f.id
				WHERE u.oldpjs_cid is null
				GROUP BY u.id';
echo $gSqlStr;

if($gCon->execute($gSqlStr)){
	echo 'Success';
}

$gCon->MoveFirst();

$lMyCon = new DbCn(MYSQL_DBTYPE);

//~ var_dump($gCon);
//~ exit();
while(!$gCon->Eof()) {
	
	$lMyCon->Open(MYSQL_DB_SRV, MYSQL_DB, MYSQL_USR, MYSQL_PASS);
	$gSql = 'CALL spRegUsrStep1(NULL, 1, \'' . q($gCon->mRs['uname']) . '\', \'123\')';
	echo $gSql . "<br>";
	$lMyCon->Execute($gSql);
	$lOldPjsCid = (int)$lMyCon->mRs['CID'];
	
	$lUpdateOld = 'UPDATE usr SET oldpjs_cid = '.$lOldPjsCid.' WHERE id = ' . $gCon->mRs['id'];
	$gCon2 = new DBCn();
	$gCon2->Open();
	$gCon2->execute($lUpdateOld);
	$gCon2->Close();
	echo $lUpdateOld . "<br>";
	$lMyCon->Close();
	$lMyCon->Open(MYSQL_DB_SRV, MYSQL_DB, MYSQL_USR, MYSQL_PASS);
	
	$gSql = 'CALL spRegUsrStep2(' . $lOldPjsCid . ', 1, \''. q($gCon->mRs['first_name']) .'\', \''. q($gCon->mRs['middle_name']) .'\', \''. q($gCon->mRs['last_name']) .'\', \''. q($gCon->mRs['salut']) .'\'
	, \''. q($gCon->mRs['ctip']) .'\', \''. q($gCon->mRs['affiliation']) .'\', \''. q($gCon->mRs['departament']) .'\', \''. q($gCon->mRs['addr_street']) .'\', \''. q($gCon->mRs['addr_postcode']) .'\', \''. q($gCon->mRs['addr_city']) .'\'
	, \''. q($gCon->mRs['country']) .'\', \''. q($gCon->mRs['phone']) .'\', \''. q($gCon->mRs['fax']) .'\', \''. q($gCon->mRs['vat']) .'\', \''. q($gCon->mRs['website']) .'\')';
	echo $gSql . "<br>";

	$lMyCon->Execute($gSql);
	$lMyCon->Close();
	$lMyCon->Open(MYSQL_DB_SRV, MYSQL_DB, MYSQL_USR, MYSQL_PASS);
		
	$gSql = 'CALL spRegUsrStep3(' . $lOldPjsCid . ', 1, \'' . ($gCon->mRs['bookstype']) . '\', \'' . ($gCon->mRs['ebookstype']) . '\', \'' . ($gCon->mRs['journalstype']) . '\', \'' . q($gCon->mRs['alertsfreq']) . '\')';
	echo $gSql . "<br>";

	$lMyCon->Execute($gSql);
	$lMyCon->Close();
	
	echo "<br><br></br></br>";
	$gCon->MoveNext();
}
$lMyCon->Close();

?>