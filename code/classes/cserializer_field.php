<?php
class cserializer_field{
	var $mId;
	var $mValue;
	var $mType;
	var $mControlType;
	var $mFieldName;
	var $mDataSrcId;
	var $mDataSrcQuery;
	var $mXmlNodeName;
	var $mInstanceId;

	function __construct($pFieldData){
		$this->mId = (int)$pFieldData['id'];
		$this->mValue = $pFieldData['value'];
		$this->mType = (int)$pFieldData['type'];
		$this->mControlType = (int)$pFieldData['control_type'];
		$this->mFieldName = $pFieldData['field_name'];
		$this->mDataSrcId = (int)$pFieldData['data_src_id'];
		$this->mDataSrcQuery = $pFieldData['data_src_query'];
		$this->mXmlNodeName = $pFieldData['xml_node_name'];
		$this->mInstanceId = (int)$pFieldData['instance_id'];
	}
	/**
	 * @return the $mId
	 */
	public function getId() {
		return $this->mId;
	}

	/**
	 * @return the $mValue
	 */
	public function getValue() {
		return $this->mValue;
	}

	/**
	 * @return the $mType
	 */
	public function getType() {
		return $this->mType;
	}

	/**
	 * @return the $mControlType
	 */
	public function getControlType() {
		return $this->mControlType;
	}

	/**
	 * @return the $mFieldName
	 */
	public function getFieldName() {
		return $this->mFieldName;
	}

	/**
	 * @return the $mDataSrcId
	 */
	public function getDataSrcId() {
		return $this->mDataSrcId;
	}

	/**
	 * @return the $mDataSrcQuery
	 */
	public function getDataSrcQuery() {
		return $this->mDataSrcQuery;
	}

	/**
	 * @return the $mXmlNodeName
	 */
	public function getXmlNodeName() {
		return $this->mXmlNodeName;
	}

	public function getInstanceId(){
		return $this->mInstanceId;
	}

}


?>