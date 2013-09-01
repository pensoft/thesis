<?php

/**
 * A model class to handle login request
 * @author peterg
 *
 */
class mLogin_Model extends emBase_Model {
	//@formatter->off
	/**
	 * Handle a request with specified username and password
	 *
	 * returns an array with the following format (
	 *		err_cnt => number of errors
	 *		err_msgs => an array containing the error msgs (an array containing arrays with the following format
	 *			err_msg => the msg of the current error
	 *		)
	 *		id => the id of the user (if login is successful)
	 *		state => the state of the user account
	 * 		uname => the username;
	 * 		ip => the ip of the last log in for this acc;
	 *		fullname => the fullname of the user
	 *		photo_id => the id of the photo for the current user
	 *  )
	 */
	//@formatter->on
	function HandleLoginRequestWithUsernameAndPassword($pUname, $pUpass, $pIp) {
		$lResult = array(
			'err_cnt' => 0,
			'err_msgs' => array(),
		);

		if(!$pUname){
			$lResult['err_cnt']++;
			$lResult['err_msgs'][] = array(
				'err_msg' => getstr('login.noUsernameSpecified'),
			);
		}

		if(!$pUpass){
			$lResult['err_cnt']++;
			$lResult['err_msgs'][] = array(
				'err_msg' => getstr('login.noPasswordSpecified'),
			);
		}

		if((int)$lResult['err_cnt']){
			return $lResult;
		}

		$lCon = $this->m_con;
		$lSql = 'SELECT u.*, j.journal_id, j.role_id FROM SiteLogin(\'' . q($pUname) . '\', \'' . q($pUpass) . '\', \'' . q($pIp) . '\') u
		LEFT JOIN pjs.journal_users j ON u.id = j.uid';
		$lCon->Execute($lSql);
		if(!$lCon->mRs['id']){
			$lResult['err_cnt']++;
			$lResult['err_msgs'][] = array(
				'err_msg' => getstr('login.noSuchUser'),
			);
		}
		if((int)$lResult['err_cnt']){
			return $lResult;
		}
		if(!$lCon->mRs['state']){
			$lResult['err_cnt']++;
			$lResult['err_msgs'][] = array(
				'err_msg' => getstr('login.thisUserIsNotActivated'),
			);
		}
		if((int)$lResult['err_msgs']){
			return $lResult;
		}
		$lFlag = NULL;
		$lResult['id'] = (int)$lCon->mRs['id'];
		$lResult['state'] = (int)$lCon->mRs['state'];
		$lResult['uname'] = $lCon->mRs['uname'];
		$lResult['ip'] = $lCon->mRs['ip'];
		$lResult['fullname'] = $lCon->mRs['fullname'];
		$lResult['firstname'] = $lCon->mRs['firstname'];
		$lResult['middlename'] = $lCon->mRs['middlename'];
		$lResult['lastname'] = $lCon->mRs['lastname'];
		$lResult['about'] = $lCon->mRs['about'];
		$lResult['phone'] = $lCon->mRs['phone'];
		$lResult['fax'] = $lCon->mRs['fax'];
		$lResult['homepage'] = $lCon->mRs['homepage'];
		$lResult['photo_id'] = (int)$lCon->mRs['photo_id'];
		$lResult['staff'] = (int)$lCon->mRs['staff'];
		$lResult['journals'] = array();

	//~ $lResult['journals'] = array(
		//~ 'journal_id' => array(
			//~ 1 ,2 ,3
		//~ ),
			//~ '1' => array(
				//~ 1 ,2,3 
			//~ ),
		//~ );
		
		$k = 0;
		while(!$lCon->Eof()){
			$k++;
			$Rows[$k]['journal_id'] = $lCon->mRs['journal_id'];
			$Rows[$k]['role_id'] = $lCon->mRs['role_id'];
			$lCon->MoveNext();
		}
		
		foreach($Rows as $k => $v){
			if( isset($lResult['journals'][$v['journal_id']]) ) {
				$lResult['journals'][$v['journal_id']][] = $v['role_id'];
			} else {
				$lResult['journals'][$v['journal_id']] = array();
				$lResult['journals'][$v['journal_id']][] = $v['role_id'];
			}
		}
		return $lResult;
	}
	
	//@formatter->off
	/**
	 * Handle a request with specified autologin hash
	 *
	 * returns an array with the following format (
	 *		err_cnt => number of errors
	 *		err_msgs => an array containing the error msgs (an array containing arrays with the following format
	 *			err_msg => the msg of the current error
	 *		)
	 *		id => the id of the user (if login is successful)
	 *		state => the state of the user account
	 * 		uname => the username;
	 * 		ip => the ip of the last log in for this acc;
	 *		fullname => the fullname of the user
	 *		photo_id => the id of the photo for the current user
	 *  )
	 */
	//@formatter->on
	function HandleAutoLoginRequestWithHash($pHash, $pIp) {
		$lResult = array(
			'err_cnt' => 0,
			'err_msgs' => array(),
		);

		if(!$pHash){
			$lResult['err_cnt']++;
			$lResult['err_msgs'][] = array(
				'err_msg' => getstr('login.noUsernameSpecified'),
			);
		}

		if((int)$lResult['err_cnt']){
			return $lResult;
		}

		$lCon = $this->m_con;
		$lSql = 'SELECT u.*, j.journal_id, j.role_id FROM siteautologin(\'' . $pHash . '\', \'' . $pIp . '\')  u
		LEFT JOIN pjs.journal_users j ON u.id = j.uid';

		$lCon->Execute($lSql);

		if(!$lCon->mRs['id']){
			$lResult['err_cnt']++;
			$lResult['err_msgs'][] = array(
				'err_msg' => getstr('login.noSuchUser'),
			);
		}
		if((int)$lResult['err_cnt']){
			return $lResult;
		}
		if(!$lCon->mRs['state']){
			$lResult['err_cnt']++;
			$lResult['err_msgs'][] = array(
				'err_msg' => getstr('login.thisUserIsNotActivated'),
			);
		}
		if((int)$lResult['err_msgs']){
			return $lResult;
		}

		$lResult['id'] = (int)$lCon->mRs['id'];
		$lResult['state'] = (int)$lCon->mRs['state'];
		$lResult['uname'] = $lCon->mRs['uname'];
		$lResult['ip'] = $lCon->mRs['ip'];
		$lResult['fullname'] = $lCon->mRs['fullname'];
		$lResult['firstname'] = $lCon->mRs['firstname'];
		$lResult['middlename'] = $lCon->mRs['middlename'];
		$lResult['lastname'] = $lCon->mRs['lastname'];
		$lResult['about'] = $lCon->mRs['about'];
		$lResult['phone'] = $lCon->mRs['phone'];
		$lResult['fax'] = $lCon->mRs['fax'];
		$lResult['homepage'] = $lCon->mRs['homepage'];
		$lResult['staff'] = (int)$lCon->mRs['staff'];
		$lResult['photo_id'] = (int)$lCon->mRs['photo_id'];
		$lResult['journals'] = array();

	//~ $lResult['journals'] = array(
		//~ 'journal_id' => array(
			//~ 1 ,2 ,3
		//~ ),
			//~ '1' => array(
				//~ 1 ,2,3 
			//~ ),
		//~ );
		
		$k = 0;
		while(!$lCon->Eof()){
			$k++;
			$Rows[$k]['journal_id'] = $lCon->mRs['journal_id'];
			$Rows[$k]['role_id'] = $lCon->mRs['role_id'];
			$lCon->MoveNext();
		}
		
		foreach($Rows as $k => $v){
			if( isset($lResult['journals'][$v['journal_id']]) ) {
				$lResult['journals'][$v['journal_id']][] = $v['role_id'];
			} else {
				$lResult['journals'][$v['journal_id']] = array();
				$lResult['journals'][$v['journal_id']][] = $v['role_id'];
			}
		}

		return $lResult;
	}

	function CheckUserAutologinHash($pUserId, $pHash) {
		$lIsUserHash = 0;
		$lCon = $this->m_con;
		$lSql = 'SELECT id FROM usr WHERE autolog_hash = \'' . $pHash . '\'';
		
		$lCon->Execute($lSql);
		if($lCon->mRs['id'] == (int)$pUserId) {
			$lIsUserHash = 1;	
		}
		return $lIsUserHash;
	}

}

?>