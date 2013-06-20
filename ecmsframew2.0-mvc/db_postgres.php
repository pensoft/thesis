<?php
require_once ("db_abstract.php");
/*
 * doc className: DBCnPostgres Description: Postgres Class za vruzka s bazata
 * danni Example: nema
 */

class DBCnPostgres extends DBCnAbstract {

	function __construct() {
		// $this->mFetched = 0;
		// $this->mEof = true;
		// ~ echo "construct";
	}

	function __destruct() {
		// $this->Close();
	}

	function Connect($psrv, $pdb, $pusr, $ppass, $pport) {
		$lResult = pg_connect("host=$psrv dbname=$pdb user=$pusr password=$ppass" . ($pport ? " port=$pport" : ""));
		// ~ var_dump($lResult);
		// ~ echo '<br/>';
		return $lResult;
	}

	function Close($pmhCn) {
		return pg_close($pmhCn);
	}

	function Free_Result($pmhRs, $pmhCn = null) {
		return pg_free_result($pmhRs);
	}

	function Result_Seek($pmhRs, $ppar) {
		return pg_result_seek($pmhRs, $ppar);
	}

	function Fetch_Array($pmhRs, $pcount, $ptype) {
		return pg_fetch_array($pmhRs, NULL, $ptype);
	}

	function Num_Fields($pmhRs) {
		return pg_num_fields($pmhRs);
	}

	function Field_Name($pmhRs, $ptrue) {
		return pg_field_name($pmhRs, $ptrue);
	}

	function Field_Type($pmhRs, $ptrue) {
		return pg_field_type($pmhRs, $ptrue);
	}

	function Query($pmhCn, $psqlstr) {
		return pg_query($pmhCn, $psqlstr);
	}

	function Last_Error($pMhCn = null) {
		return pg_last_error($pMhCn);
	}

	function Num_Rows($pmhRs) {
		return pg_num_rows($pmhRs);
	}

	function Escape_String($pvalue, $pMhCn = null) {
		return pg_escape_string($pvalue);
	}

	function ResultStartIter() {
		return 0;
	}

}

class PostgresHelper extends DBCnHelperAbstract {
	function __construct() {
		// $this->mFetched = 0;
		// $this->mEof = true;
		// ~ echo "construct";
		// trigger_error("\n########## Postgres HELPER ##########\n!",
	// E_USER_NOTICE);
	}

	function __destruct() {
		// $this->Close();
	}

	function SaveSQLValue($value, $type, $name, $pCon = null, $win_encoding = 0) {
		// ~ trigger_error("\n########## Postgres HELPER SaveSQLValue
		// ##########\n!", E_USER_NOTICE);
		if($type[0] != 's' && $value === '')
			$value = null;

		if($value === null)
			return 'NULL';

		$errmsg = "";

		if($type[0] == 'n' || ($type[0] == 'i' && $type != 'interval') || $type == "multiplepower")
			if(! is_numeric($value))
				$errmsg = $name . ' - Not a numeric value: ' . $value;

		if($type[0] == 'i' && $type != 'interval')
			if($value != ((int) $value))
				$errmsg = $name . ' - Not an integer value.';

		if($type[0] == 'd' || $type[0] == 't'){
			$value = str_replace("T", " ", $value);
			$value = "'" . pg_escape_string($value) . "'";
		}

		if($type[0] == 's' || $type == "multiplestring" || $type == "interval" || $type == ""){
			$value = "'" . pg_escape_string($value) . "'";
			if($win_encoding == 1)
				$value = WinToUnicode($value, 1);
		}
		if($type[0] == 'b'){
			if($value === 0 || strtoupper($value[0]) == "F")
				return 'false';
			else
				return 'true';
		}
		if($type == "multipleint"){
			if(! preg_match("/^array\[(((null|\d+)\s*\,\s*)*(null|\d+)\]$|\]$)/", $value))
				$errmsg = $name . ' - Not a valid multipleint(postgres array of integers) value: ' . $value;
		}

		if($type == 'time'){
			if(! preg_match("/^\'[0-1][0-9]:[0-5][0-9](:[0-5][0-9])?\'$/", $value)){
				if(! preg_match("/^\'2[0-3]:[0-5][0-9](:[0-5][0-9])?\'$/", $value))
					$errmsg = $name . ' - Not a valid time value: ' . $value;
			}
		}

		if($errmsg){
			trigger_error($errmsg, E_USER_ERROR);
			exit();
		}
		return $value;
	}

	function Single_Query($pmhCn, $sqlstr) {
		// trigger_error("\n########## Postgres HELPER Single_Query
		// ##########\n!", E_USER_NOTICE);
		return pg_query($pmhCn, $sqlstr);
	}

	function CustomSQLFunction($fname, $farg, $fieldname, $fieldtype) {
		if(($fname == "groupby") && (($fieldtype == "timestamp") || ($fieldtype == "date") || ($fieldtype == "datetime"))){
			$period = ($farg == "H") ? "hour" : (($farg == "M") ? "month" : (($farg == "W") ? "week" : (($farg == "D") ? "day" : "year")));
			// ~ trigger_error($farg." - date_trunc('".$period."',
			// ".$fieldname.")",E_USER_NOTICE);
			return "date_trunc('" . $period . "', " . $fieldname . ")";
		}
		return parent::CustomSQLFunction($fname, $farg, $fieldname, $fieldtype);
	}

	function GetPrototypeSQL($sqlname, $actionname, $sqltype, $multilang = 0) {
		// trigger_error("\n########## Postgres HELPER GetPrototypeSQL $sqlname
		// ##########\n!", E_USER_NOTICE);

		$sqlarr = array(
			GENACT_TABLE => array(
				"Get" => "select [allnames] from [sqlname] where [pknames=values_and]",
				"Delete" => "delete from [sqlname] where [pknames=values_and]",
				"Save" => "update [sqlname] set [allnames=values] where [pknames=values_and]; select [allnames] from [sqlname] where [pknames=values_and];",
				"Insert" => "insert into [sqlname]([notidentitynames]) values ([notidentityvalues]); select [allnames] from [sqlname] where [returnkeys=values_and];",
				// "Insert"=>"insert into [sqlname]([notidentitynames]) values
				// ([notidentityvalues]); select [returnkeys],[notkeysnames]
				// from [sqlname] where [returnkeys=values_and];",
				"GetData" => "select " . ($multilang ? "[allnames]" : "*") . " from [sqlname] [filterfields_table]",
				"Attach" => "insert into [sqlname]([notidentitynames]) values ([notidentityvalues]);",
				"Remove" => "delete from [sqlname] where [pknames=values_and]"
			),
			GENACT_SP => array(
				"Get" => "select * from [sqlname](0," . ($multilang ? "{__lang}," : "") . "[pkvaluesandnull])",
				"Delete" => "select * from [sqlname](3," . ($multilang ? "{__lang}," : "") . "[pkvaluesandnull])",
				"Save" => "select * from [sqlname](2," . ($multilang ? "{__lang}," : "") . "[allvalues])",
				"Insert" => "select * from [sqlname](1," . ($multilang ? "{__lang}," : "") . "[allvaluesidentitynull])",
				"GetData" => "select * from [sqlname]({" . SP_OPID_NAME . "}, " . ($multilang ? "{__lang}, " : "") . "[allvalues]) [filterfields_table]",  // tuka
				                                                                                                                             // da
				                                                                                                                             // se
				                                                                                                                             // opravi
				"Attach" => "select * from [sqlname](4," . ($multilang ? "{__lang}," : "") . "[allvaluesidentitynull])",
				"Remove" => "select * from [sqlname](5," . ($multilang ? "{__lang}," : "") . "[pkvaluesandnull])"
			)
		);
		if(array_key_exists($actionname, $sqlarr[$sqltype]))
			$l = str_replace("[sqlname]", $sqlname, $sqlarr[$sqltype][$actionname]);
		else
			$l = "";

			// trigger_error("\n########## Postgres HELPER GetPrototypeSQL $l
		// ##########\n!", E_USER_NOTICE);

		return $l;
	}

	function SecurityCols($psqlstr) {
		return $psqlstr;
	}
}
?>
