<?php

/**
 * A model to implement register functionality
 */
class mRegister_Model extends emBase_Model {
	
	function SetUserConfHash($pUsrEmail, $pConfHash) {
		$lResult = array(
			'err_cnt' => 0,
			'err_msgs' => array(),
			'id' => 0,
		);
		
		$lCon = $this->m_con;
		
		$lSql = 'SELECT * FROM SetConfHash(\'' . q($pUsrEmail) . '\', \'' . q($pUsrEmail) . '\', \'' . q($pConfHash) . '\')';

		if(!$lCon->Execute($lSql)){
			$lResult['err_cnt']++;
			$lResult['err_msgs'][] = array('err_msg' => getstr($lCon->GetLastError()));
		}
		
		return $lResult;
	}
	
	function ConfirmUserEmail($pHash){
		$lCon = $this->m_con;
		$lSql = 'SELECT ConfMail(\'' . q($pHash) . '\') as cm';
		$lCon->Execute($lSql);
		$lCon->MoveFirst();
		return (int)$lCon->mRs['cm'];
	}
}
?>