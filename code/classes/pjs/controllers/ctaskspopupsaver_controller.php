<?php

class cTasksPopUpSaver_Controller extends cBase_Controller {
	
	function __construct() {
		parent::__construct();
		
		$lEmailTaskDetailId = $this->GetValueFromRequestWithoutChecks('email_task_detail_id');
		$lFieldValue = $this->GetValueFromRequestWithoutChecks('fld_value');
		$lFieldName = $this->GetValueFromRequestWithoutChecks('fld_name');
		
		$lTaskModel = new mTask_model();
		$lTaskModel->SaveField($lEmailTaskDetailId, $lFieldName, $lFieldValue);
	}

}

?>