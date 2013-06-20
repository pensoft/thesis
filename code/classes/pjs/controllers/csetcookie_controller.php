<?php

class cSetcookie_Controller extends cBase_Controller {

	function __construct() {
		global $rewrite;
		parent::__construct();
		
		$pViewPageObjectsDataArray = array();
		
		
		$lRedirUrl = $this->GetValueFromRequestWithoutChecks('redirurl');
		$lLogout = (int)$this->GetValueFromRequestWithoutChecks('logout');
		
		if(!(int)$lLogout) {
		
			$cn = Con();
			$cn->Execute('SELECT autolog_hash FROM usr WHERE id = ' . (int)$this->GetUserId());
			$cn->MoveFirst();
			$lAutoLogHash = $cn->mRs['autolog_hash'];

			if(!$lAutoLogHash) {
				header("Location: $lRedirUrl");
			}
		}
		if(!$lRedirUrl){
			$lRedirUrl = '/index.php';
		}
		
		$pViewPageObjectsDataArray['contents'] = array(
				'ctype' => 'evSimple_Block_Display',
				'name_in_viewobject' => 'setcookie',
				'hash' => $lAutoLogHash,
				'redirurl' => $lRedirUrl,
				'logout' => $lLogout,
			);

		$this->m_pageView = new pSetcookie_Page_View(array_merge($this->m_commonObjectsDefinitions, $pViewPageObjectsDataArray));

	}

	protected function InitSessionCloseVariable(){
		$this->m_closeSession = false;

	}

}

?>