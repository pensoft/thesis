<?php

class pView_Version_Pwt_Ajax extends pView_Version_Pwt {
	public function Display() {
		$lResult = array(
			'form' => '',
			'form_has_errors' => 0,
		);
		$lForm = $this->GetVal('form');
		if($lForm instanceof viewVersion_Form_Wrapper){
			$lResult['form'] = $lForm->Display();
		}
		$lResult['form_has_errors'] = $this->GetVal('form_has_errors');		
		
		$this->SetPageContentType('application/json');
		return json_encode($lResult);
	}
}

?>