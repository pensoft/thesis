<?php
/**
 * This is an abstract class which will be extended
 * by all other classes which will process and display model data.
 * The data should be passed as an argument to these classes by a controller
 * as these classes will not be able to communicate with the db.
 * @author peterg
 *
 */
abstract class evbase_view extends ebase{
	/**
	 * This variable will hold a reference to the view object that will display this class
	 * @var epPage_View
	 */
	var $m_viewObject;

	/**
	 * Name under which to search the metadata for the object in the view object.
	 * This name is used when looking for templates
	 * @var unknown_type
	 */
	var $m_nameInViewObject;
	var $m_dontProcessData;

	function __construct($pData){
		$this->m_dontProcessData = false;
		$this->m_pubdata = array_change_key_case($pData, CASE_LOWER);
		$this->m_viewObject = $pData['view_object'];
		$this->m_nameInViewObject = $pData['name_in_viewobject'];
	}

	/**
	 * Changes the view object which will display this object.
	 * When we change the view object we tell the object that if it
	 * has already processed the data - it should do it again.
	 * @param BaseView $pViewObject
	 */
	public function setViewObject($pViewObject){
		$this->m_viewObject = $pViewObject;
		$this->m_dontProcessData = false;
	}

	/**
	 * Returns a reference to the view object that will display this object
	 */
	public function getViewObject(){
		return $this->m_viewObject;
	}

	/**
	 * Asks the view to replace all the meaningful things in the
	 * passed string
	 * @param unknown_type $pStr
	 */
	protected function ReplaceHtmlFields($pStr) {
		if($this->m_viewObject){
			return $this->m_viewObject->ReplaceHtmlFields($pStr, $this);
		}
	}

	function DisplayTemplate($pTemplId) {
		$lmas = $this->getTemplate($pTemplId);
		return $this->ReplaceHtmlFields($lmas);
	}

	function ProcessData(){
		$this->m_dontProcessData = true;
	}

	/**
	 * We process the data first (if we haven\'t processed it already) and
	 * after that we display it.
	 * @see cbase::Display()
	 */
	function Display(){
		if(!$this->m_dontProcessData){
			$this->ProcessData();
		}
	}


	protected function HtmlPrepare($pName) {
		if($this->m_viewObject){
			return $this->m_viewObject->HtmlPrepare($pName, $this);
		}
	}

	function getTemplate($pTemplName) {
		if($this->m_viewObject){
			return $this->m_viewObject->getTemplate($pTemplName);
		}
	}

	function getObjTemplate($pTemplId, $templadd="") {
		if($this->m_viewObject){
			return $this->m_viewObject->getObjectTemplate($pTemplId, $templadd, $this->m_nameInViewObject);
		}
	}

	function objname() {
		$lViewObjectHash = '';
		if($this->m_viewObject){
			$lViewObjectHash = $this->m_viewObject->getPubdataHash();
		}
		return $this->m_pubdata['cache'] . '_' . get_class($this) . '_' . get_parent_class($this) . '_' . sprintf("%x", $this->getPubdataHash()) . '_' . sprintf("%x", $lViewObjectHash);
	}


}
?>