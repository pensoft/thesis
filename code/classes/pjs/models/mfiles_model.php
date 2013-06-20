<?php

/**
 * A model class to handle the management of files
 * @author peterg
 *
 */
class mFiles_Model extends emBase_Model {
	//@formatter->off
	/**
	 * Returns an id for a new pic which is being uploaded
	 *
	 * returns an array with the following format (
	 *		err_cnt => number of errors
	 *		err_msgs => an array containing the error msgs (an array containing arrays with the following format
	 *			err_msg => the msg of the current error
	 *		)
	 *		id => the id of the pic if there are no errors
	 *  )
	 */
	//@formatter->on
	function GetNewPicId($pTitle, $pDescription, $pOriginalPicFileName) {
		$lResult = array(
			'err_cnt' => 0,
			'err_msgs' => array(),
			'id' => 0,
		);
		$lCon = $this->m_con;
		$lSql = 'SELECT PicsUpload(1, null, 0,\'' . q($pTitle) . '\', \'' . q($pOriginalPicFileName) . '\', \'' . q($pDescription) . '\') as picid';


		if(!$lCon->Execute($lSql)){
			$lResult['err_cnt']++;
			$lResult['err_msgs'][] = array('err_msg' => getstr($lCon->GetLastError()));
		}

		if(!(int)$lCon->mRs['picid']){
			$lResult['err_cnt']++;
			$lResult['err_msgs'][] = array('err_msg' => getstr('global.errorDbFile'));
		}
		$lResult['id'] = (int)$lCon->mRs['picid'];
// 		var_dump($lResult);
		return $lResult;
	}

	//@formatter->off
	/**
	 * Deletes the specified photo from the db
	 *
	 * returns an array with the following format (
	 *		err_cnt => number of errors
	 *		err_msgs => an array containing the error msgs (an array containing arrays with the following format
	 *			err_msg => the msg of the current error
	 *		)
	 *  )
	 */
	function DeletePic($pPicId){
		$lResult = array(
			'err_cnt' => 0,
			'err_msgs' => array(),
		);
		$lCon = $this->m_con;
		$lSql = 'SELECT PicsUpload(3, ' . (int)$pPicId . ', null, null, null, null);';


		if(!$lCon->Execute($lSql)){
			$lResult['err_cnt']++;
			$lResult['err_msgs'][] = array('err_msg' => getstr($lCon->GetLastError()));
		}
		return $lResult;
	}

}

?>