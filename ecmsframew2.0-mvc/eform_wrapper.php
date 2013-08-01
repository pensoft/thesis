<?php

class eForm_Wrapper extends ebase{
	/**
	 * The controller for the form
	 * @var ecForm_Controller
	 */
	protected $m_formController;
	/**
	 * The form model
	 * @var emForm_Model
	 */
	protected $m_formModel;
	/**
	 * The view object for the form
	 * @var evForm_View
	 */
	protected $m_formView;

	function __construct($pData){
		if((int)$pData['dont_close_session']){
			$this->m_formController = new ecForm_Controller_Open_Session($pData);
		}else{
			$this->m_formController = new ecForm_Controller($pData);
		}

		$this->m_formModel = new emForm_Model($this->m_formController->GetFieldsMetadata());

		//This is needed because the model fills the src values of the fields
		$this->m_formController->SetFieldsMetadata($this->m_formModel->GetFieldsMetadata());

		//We set the form controller for the view
// 		$pData['form_controller'] = $this->m_formController;
// 		$this->m_formView = new evForm_View($pData);

		$this->m_formController->SetFormModel($this->m_formModel);
// 		$this->m_formController->SetFormView($this->m_formView);

		$this->PreActionProcessing();
		$this->m_formController->PerformAction();
		$this->PostActionProcessing();

		$this->m_formView = $this->m_formController->CreateFormView();
	}

	function GetCurrentAction(){
		return $this->m_formController->GetCurrentAction();
	}

	function GetFieldValue($pFieldName){
		return $this->m_formController->GetFieldValue($pFieldName);
	}
	
	function GetFormGlobalErrors(){
		return $this->m_formController->m_globalErrors;
	}

	function GetErrorCount(){
		return $this->m_formController->GetErrorCount();
	}

	/**
	 * Changes the view object which will display this object.
	 * (delegates to the formView)
	 *
	 * @param BaseView $pViewObject
	 */
	public function setViewObject($pViewObject){
		$this->m_formView->setViewObject($pViewObject);
	}

	/**
	 * Processing to be executed BEFORE the form executes its action.
	 * (e.g. We may perform additional checks here)
	 * This method is to be overwritten when we want a form to do
	 * some pre action processing
	 */
	protected function PreActionProcessing(){

	}

	/**
	 * Processing to be executed AFTER the form executes its action.
	 * (e.g. We may change the form field values after we execute the action)
	 * This method is to be overwritten when we want a form to do
	 * some post action processing
	 */
	protected function PostActionProcessing(){

	}

	/**
	 * Returns a reference to the view object that will display this object
	 * (delegates to the formView)
	 */
	public function getViewObject(){
		return $this->m_formView->getViewObject();
	}

	function ProcessData(){
	}

	function GetFormValidationRespond($pField){
		$lErrResult = '';
		$lErrResult .= $this->m_formView->GetFieldErrorsTemplate($pField);

		$lElemId = (array_key_exists('id', $this->m_formController->m_fieldsMetadata[$pField]['AddTags']) ? $this->m_formController->m_fieldsMetadata[$pField]['AddTags']['id'] : DEF_FORM_FIELD_ID . $pField);

		$lArrErrors = array (
			'error_string' => $lErrResult,
			'error_holder' => DEF_ERROR_ID_HOLDER . $pField,
			'error_field' => $lElemId,
			'error_field_class' => ($this->m_formController->m_fieldsMetadata[$pField]["error_class"] ? $this->m_formController->m_fieldsMetadata[$pField]["error_class"] : DEF_ERROR_FIELD_CLASS),
			'error_js' => ($this->m_formController->m_fieldsMetadata[$pField]["error_js"] ? $this->m_formController->m_fieldsMetadata[$pField]["error_js"] : ''),
			'ajax_error_js' => ($this->m_formController->m_fieldsMetadata[$pField]["ajax_error_js"] ? $this->m_formController->m_fieldsMetadata[$pField]["ajax_error_js"] : ''),
			'valid_js' => ($this->m_formController->m_fieldsMetadata[$pField]["valid_js"] ? $this->m_formController->m_fieldsMetadata[$pField]["valid_js"] : ''),
			'req_js' => ($this->m_formController->m_fieldsMetadata[$pField]["req_js"] ? $this->m_formController->m_fieldsMetadata[$pField]["req_js"] : ''),
		);

		return $lArrErrors;
	}

	function Display(){
		return $this->m_formController->Display();
	}

}


?>