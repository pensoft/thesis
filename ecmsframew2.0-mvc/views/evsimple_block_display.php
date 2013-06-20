<?php

/**
 * A base class that will display a single html block.
 * It gets its data from a controller and is being displayed by a view
 *
 * @author peterg
 *
 */
class evSimple_Block_Display extends evbase_view {
	/**
	 * The data provided by the controller
	 *
	 * @var array
	 */
	var $m_controllerData;


	function __construct($pData) {
		parent::__construct($pData);
		$this->m_controllerData = $pData['controller_data'];
		if(! is_array($this->m_controllerData)){
			$this->m_controllerData = array();
		}

		$this->m_pubdata = array_change_key_case($pData, CASE_LOWER);

		/**
		 * Import the data from the controller in the pubdata
		 */
		foreach($this->m_controllerData as $k => $v){
			$this->m_pubdata[$k] = $this->m_currentRecord[$k] = $v;
		}
	}

	function GetData() {

	}

	public function Display() {
		$this->GetData();

		$lRet = $this->ReplaceHtmlFields($this->getObjTemplate(G_DEFAULT));

		return $lRet;
	}

}

?>