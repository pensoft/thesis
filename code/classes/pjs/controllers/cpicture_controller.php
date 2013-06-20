<?php

class cPicture_Controller extends cBase_Controller {

	function __construct() {
		parent::__construct();
		
		$pViewPageObjectsDataArray = array();

		$this->SetUploadTimeLimit();

		if(isset($_FILES['uploadfile'])) {
			$lUploadResult = $this->UploadPhoto('uploadfile');
			$lPicId = (int)$lUploadResult['photo_id'];
			
			$lUserModel = new mUser_Model();
			
			if($lUploadResult['err_cnt']){
				
			}else{
				//The pic has been updated correctly - we should update the user data in the db
				$lUserModel->ChangeUserPreviewPic((int)$_REQUEST['userid'], $lPicId);
			}
		}

		$pViewPageObjectsDataArray['picture'] = array(
			'ctype' => 'evSimple_Block_Display',
			'name_in_viewobject' => (int)$lPicId ? 'profile_picture_template' : 'default_profile_picture',
			'pic_pref' => 'c67x70y',
			'pic_id' => (int)$lPicId,
		);
		global $user;
		if ($user->id == $this->GetUserId()) {
			$user->photoid = (int)$lPicId;
			$_SESSION['suser'] = serialize($user);
		}
		$this->m_pageView = new pPicture_Page_View($pViewPageObjectsDataArray);
	}
	
	function SetUploadTimeLimit(){
		$lTimeLimit = (int)UPLOAD_TIME_LIMIT;
		if (!$lTimeLimit) 
			$lTimeLimit = 200;
		set_time_limit($lTimeLimit);
	}
	
	protected function InitSessionCloseVariable(){
		$this->m_closeSession = false;
	}
}

?>