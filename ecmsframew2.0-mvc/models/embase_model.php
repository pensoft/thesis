<?php

/**
 * A base model class.
 * All other model class should extend this class.
 *
 * The base class creates a db connection which is to be used by
 * all the other model classes.
 * @author peterg
 *
 */
 //error_reporting(-1);
 //ini_set('display_errors', 'On');
class emBase_Model{
	/**
	 * A db connection to query the database
	 */
	var $m_con;

	function __construct(){
		$this->m_con = new DBCn();
		$this->m_con->Open();
	}
	
	function ArrayOfRows($SQL, $debug = 0)
	{
		// debug levels: 0 - nothing, 1 - print the SQL, 2 - print the result, 3 - print both
		if ($debug % 2 == 1)
			var_dump($SQL);
		 
		$lResult = array();
		$lCon = $this->m_con;
		$lCon->SetFetchReturnType(PGSQL_ASSOC);
		$lCon->Execute($SQL);
		while(!$lCon->Eof()){
			$lResult[] = $lCon->mRs;
			$lCon->MoveNext();
		}
		
		if($debug > 1)
			var_dump($lResult);
		
		return $lResult;
	}
	
	function ArrayOfValues($SQL, $column, $debug = 0)
	{
		// debug levels: 0 - nothing, 1 - print the SQL, 2 - print the result, 3 - print both
		if ($debug % 2 == 1)
			var_dump($SQL);
		
		$lResult = array();
		$lCon = $this->m_con;
		$lCon->SetFetchReturnType(PGSQL_ASSOC);
		$lCon->Execute($SQL);
		while(!$lCon->Eof()){
			$lResult[] = $lCon->mRs[$column];
			$lCon->MoveNext();
		}
		
		if($debug > 1)
			var_dump($lResult);
		
		return $lResult;
	}
	
	function Events($SQL, $debug = 0){
		// debug levels: 0 - nothing, 1 - print the SQL, 2 - print the result, 3 - print both
		if ($debug % 2 == 1)
			var_dump($SQL);
				
		$lResult = array(
			'err_cnt' => 0,
			'err_msgs' => array()
		);
		$lCon = $this->m_con;
		try{
			if(!$lCon->Execute($SQL)){
				throw new Exception($lCon->GetLastError());
			}
			$lResult['success_msg'] = getstr('pjs.actionSuccessfullyPerformed');
			$lResult['event_id'][] = $lCon->mRs['event_id'];
			if(isset($lCon->mRs['event_id_sec'])){
				$lResult['event_id'][] = $lCon->mRs['event_id_sec'];
			}
		}catch(Exception $pException){
			$lResult['err_cnt']++;
			$lResult['err_msgs'][] = array('err_msg' => $pException->getMessage());
		}
		
		if($debug > 1)
			var_dump($lResult);
		
		return $lResult;
	}
}


?>