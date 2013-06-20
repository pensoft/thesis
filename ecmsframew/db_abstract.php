<?php
abstract class DBCnAbstract {
	abstract function Connect($pSrv, $pDb, $pUsr, $pPass, $pPort);

	abstract function Close($pMhCn);
	
	abstract function Free_Result($pMhRs, $pMhCn = null);
	
	abstract function Result_Seek($pMhRs, $pPar);
	
	abstract function Fetch_Array($pMhRs, $pCount, $pType);
	
	abstract function Num_Fields($pMhRs);
	
	abstract function Field_Name($pMhRs, $pTrue);
		
	abstract function Field_Type($pMhRs, $pTrue);
	
	abstract function Query($pMhCn, $pSqlstr);
	
	abstract function Last_Error($pMhCn = null);
	
	abstract function Num_Rows($pMhRs);
	
	abstract function Escape_String($pValue, $pMhCn = null);
	
	abstract function ResultStartIter();
	
};


abstract class DBCnHelperAbstract {
	abstract function SaveSQLValue($pValue, $pType, $pName, $pCon=null);

	abstract function Single_Query ($pMhCn, $pSqlstr);

	abstract function GetPrototypeSQL($pSqlname, $pActionname, $pSqltype, $pMultilang = 0);
	
	protected function CustomSQLFunction($fname,$farg,$fieldname,$fieldtype) {
		trigger_error("Unimplemented custom SQL Function: ".$fname." for field ".$fieldname."!" ,E_USER_ERROR);
		exit();
	}
};



















?>