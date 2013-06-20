<?php
require_once( "db_abstract.php" ) ;
/* doc

className: DBCnMySql

Description: MySql Class za vruzka s bazata danni

Example: nema

*/

class DBCnMySql extends DBCnAbstract{
	
	function __construct() {
		//$this->mFetched = 0;
		//$this->mEof = true;
		//~ trigger_error("\n########## MySQL INIT ##########\n!", E_USER_NOTICE);
	}

	function __destruct() {
		//$this->Close();
	}

	function Connect($pSrv, $pDb, $pUsr, $pPass, $pPort) {
		$lConnection = mysqli_init();
		if (!$lConnection) {
			return false;
		}
		$lTemp = mysqli_real_connect($lConnection, $pSrv, $pUsr, $pPass, $pDb);
		if (!$lTemp) {
			return false;
		}
		if(MYSQL_CONNECTION_ENCODING){
			mysqli_query($lConnection, 'SET NAMES \'' . MYSQL_CONNECTION_ENCODING . '\''); //- setvame encoding na con ako trqbva, za v budeshte da se napravi na konstanta
		}
		return $lConnection;
				
		//~ $lConnection = mysql_connect($pSrv, $pUsr, $pPass);
		//~ if( $lConnection )
			//~ mysql_select_db($pDb, $lConnection);
		//~ return $lConnection;
		
	}

	function Close($pMhCn) {
		return mysqli_close($pMhCn);
	}
	
	function Free_Result($pMhRs,$pMhCn=null) {
		$resFree = mysqli_free_result($pMhRs);
		mysqli_next_result($pMhCn);
		while (mysqli_next_result($pMhCn)) { 
			$resNext=mysqli_use_result();
			mysqli_free_result($resNext);
		}
	}
	
	function Result_Seek($pMhRs, $pPar) {
		//~ return mysql_data_seek($pMhRs, $pPar);
		return mysqli_data_seek($pMhRs, $pPar);
	}
	
	function Fetch_Array($pMhRs, $pCount, $pType) {
		return mysqli_fetch_array($pMhRs);
	}
	
	function Num_Fields($pMhRs) {
		return mysqli_num_fields($pMhRs);
	}
	
	function Field_Name($pMhRs, $pTrue) {
		return mysql_field_name($pMhRs, $pTrue);
	}
		
	function Field_Type($pMhRs, $pTrue) {
		return mysql_field_type($pMhRs, $pTrue);
	}
	
	function Query($pMhCn, $pSqlstr) {		
		if(substr($pSqlstr,0,1) == '@'){//multi
			mysqli_multi_query($pMhCn, substr($pSqlstr,1));
			do{
				$resNext = mysqli_use_result($pMhCn);
			}while(mysqli_next_result($pMhCn));
		}else
			$resNext = mysqli_query($pMhCn, $pSqlstr);
			
		return $resNext;
	}
	
	function Last_Error($pMhCn = null) {
		return mysqli_error($pMhCn);
	}
	
	function Num_Rows($pMhRs) {
		return mysqli_num_rows($pMhRs);
	}
	
	function Escape_String($pValue, $pMhCn = null) {
		return mysqli_real_escape_string($pMhCn, $pValue);
	}
	
	function ResultStartIter(){
		return 0;
	}
	
	
}

class MySqlHelper extends DBCnHelperAbstract{
	function __construct() {
		//$this->mFetched = 0;
		//$this->mEof = true;
		//~ echo "construct";
		trigger_error("\n########## MySQL HELPER ##########\n!", E_USER_NOTICE);
	}

	function __destruct() {
		//$this->Close();
	}
	
	function SaveSQLValue($pValue, $pType, $pName, $pCon=null, $win_encoding=0) {
		if(!$pCon){
			trigger_error("No connection passed!",E_USER_NOTICE);
			exit;
		}
		if ($pType[0] != 's' && $pValue === '')
			$pValue = null;

		if ($pValue === null)
			return 'NULL';
			
		$lErrmsg = "";
		
		if ($pType[0] == 'n' || ($pType[0] == 'i' && $pType != 'interval') || $pType == "multiplepower")
			if (!is_numeric($pValue))
				$lErrmsg = $pName . ' - Not a numeric value: ' . $pValue;

		if ($pType[0] == 'i' && $pType != 'interval')
			if ($pValue != ((int)$pValue))
				$lErrmsg = $pName . ' - Not an integer value.';

		if ($pType[0] == 'd' || $pType[0] == 't') {
			$pValue = str_replace("T", " ", $pValue);
			$pValue = "'" . mysqli_real_escape_string($pCon,$pValue) . "'";
		}
		
		if ($pType[0] == 's' || $pType == "multiplestring" || $pType == "interval" || $pType == "") {
			$pValue = "'" . mysqli_real_escape_string($pCon, $pValue) . "'";
			if ($win_encoding==1)	
				$pValue=WinToUnicode($pValue,1);
		}
		if ($pType[0] == 'b') {
			if ($pValue === 0 || mysqli_real_escape_string($pCon,$pValue[0]) == "F") 
				return 'false'; 
			else return 'true'; 
		}
		if ($pType == "multipleint") {
			if (!preg_match("/^\'\{(((null|\d+)\s*\,\s*)*(null|\d+)\}\'$|\}\'$)/", $pValue))
				$lErrmsg = $pName . ' - Not a valid multipleint(mysql array of integers) value: ' . $pValue;
		}
		
		if($pType == 'time'){
			if(!preg_match("/^\'[0-1][0-9]:[0-5][0-9](:[0-5][0-9])?\'$/", $pValue)){
				if(!preg_match("/^\'2[0-3]:[0-5][0-9](:[0-5][0-9])?\'$/", $pValue))
					$lErrmsg = $pName . ' - Not a valid time value: ' . $pValue;
			}
		}
		
		if ($lErrmsg) {
			trigger_error($lErrmsg, E_USER_ERROR);
			exit();
		}
		return $pValue;
	}


	function Single_Query ($pMhCn, $pSqlstr) {
		return mysql_query($pMhCn, $pSqlstr);
	}

	function GetPrototypeSQL($pSqlname, $pActionname, $pSqltype, $pMultilang = 0) {
		
		if( $pMultilang ){
			$lLangParam = "{__lang}, ";
			$lSelectParam = "[allnames]";
		}else{
			$lLangParam = "";
			$lSelectParam = "*";
		}
		
		$lSqlarr=array (
			GENACT_TABLE =>array(
				"Get"=>"select [allnames] from [sqlname] where [pknames=values_and]", 
				"Delete"=>"delete from [sqlname] where [pknames=values_and]", 
				"Save"=>"@update [sqlname] set [allnames=values] where [pknames=values_and]; select [allnames] from [sqlname] where [pknames=values_and];", 
				"Insert"=>"@insert into [sqlname]([notidentitynames]) values ([notidentityvalues]); select [allnames] from [sqlname] where [returnkeys=values_and];", 
				//"Insert"=>"insert into [sqlname]([notidentitynames]) values ([notidentityvalues]); select [returnkeys], [notkeysnames] from [sqlname] where [returnkeys=values_and];", 
				"GetData"=>"select " . $lSelectParam . " from [sqlname] [filterfields_table]", 
				"Attach"=>"insert into [sqlname]([notidentitynames]) values ([notidentityvalues]);", 
				"Remove"=>"delete from [sqlname] where [pknames=values_and]", 
			), 
			GENACT_SP =>array(
				"Get"=>"CALL [sqlname](0, " . $lLangParam . "[pkvaluesandnull])", 
				"Delete"=>"CALL [sqlname](3, " . $lLangParam . "[pkvaluesandnull])", 
				"Save"=>"CALL [sqlname](2, " . $lLangParam . "[allvalues])", 
				"Insert"=>"CALL [sqlname](1, " . $lLangParam . "[allvaluesidentitynull])", 
				"GetData"=>"CALL [sqlname]({" . SP_OPID_NAME . "}, " . $lLangParam . "[allvalues]) [filterfields_table]", //tuka da se opravi
				"Attach"=>"CALL [sqlname](4, " . $lLangParam . "[allvaluesidentitynull])", 
				"Remove"=>"CALL [sqlname](5, " . $lLangParam . "[pkvaluesandnull])", 
			)
		);
		if (array_key_exists($pActionname, $lSqlarr[$pSqltype]))
			$lRes = str_replace("[sqlname]", $pSqlname, $lSqlarr[$pSqltype][$pActionname]);
		else 
			$lRes = "";
		return $lRes;
	}
	
	function SecurityCols($psqlstr) {
		return $psqlstr;
	}
}
?>
