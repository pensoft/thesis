<?php

/**
 * A model class to get the contents of the menus
 * @author peterg
 *
 */
class mMenu_Model extends emBase_Model {
	/**
	 * Returns an array containing all the contents of the specified menu
	 * (an array of rows)
	 *
	 * @param $pGoRecursively -
	 *       	 if this paramater is given
	 *       	 the whole menu will be returned. Otherwise only the first
	 *        	level contents of the menu will be returned
	 */
	function GetMenuContentsList($pMenuId, $pLanguage, $pGoRecursively = 1) {
		$lResult = array();
		$lCon = $this->m_con;
		$lSql = 'SELECT id, ' . getsqlang('name', $pLanguage) . ', ' . getsqlang('href', $pLanguage) . ',' . getsqlang('img', $pLanguage) . ', type, parentid
			FROM getMenuContents(' . (int)$pMenuId . ', 0,' . (int)CMS_SITEID . ',' . $pLanguage . ')';

		if(!$pGoRecursively){
			$lSql .= 'WHERE parentid = ' . (int)$pMenuId;
		}

// 		var_dump($lSql);
		$lCon->Execute($lSql);
		while(! $lCon->Eof()){
			$lResult[] = $lCon->mRs;
			$lCon->MoveNext();
		}
// 		var_dump($lResult);
		return $lResult;
	}
	
	function GetJournalMenuContentList($pJournalId, $pLanguage, $pGoRecursively = 1){
		$lCon = $this->m_con;
		$lSql = 'SELECT pjs_menu_id as menu_id FROM journals WHERE id = ' . (int)$pJournalId;
		$lCon->Execute($lSql);
		if($lCon->mRs['menu_id']){
			return $this->GetMenuContentsList($lCon->mRs['menu_id'], $pLanguage, $pGoRecursively);
		}
	}
}

?>