<?php
/**
	Този клас ще реализира сериализацията на текущата версия на 
	даден документ в xml
*/
class cdocument_serializer extends csimple {
	var $m_document_id;
	//Dom Document-а на обекта
	var $m_document_xml_dom;
	
	/*
	 * Тук ще пазим цялата информация за инстанциите на обектите.
	 * За всеки instance в ключа children ще пазим id-та на инстанциите на подобектите.
	 * За всеки instance в ключа fields ще пазим масив във формат
	 * 		id => $lFieldDataArray
	 * с информация за всичките field-ове на instance-a
	 */
	var $m_object_details;	
	
	function __construct($pFieldTempl) {			
		parent::__construct($pFieldTempl);
		$this->m_object_details = array();		
		$this->m_document_xml_dom = new DOMDocument('1.0', DEFAULT_XML_ENCODING);
	}
	
	function GetData(){
		$this->serializeDocument();
		$this->m_pubdata['xml'] = $this->getXml();
		parent::GetData();
	}
	
	function getXml(){
		return $this->m_document_xml_dom->saveXML();
	}
		
	/*
	 * 
	 * Първо запазваме информацията за всички обекти и всички field-ове.
	 * След това започваме рекурсивно да сериализираме обектите от 1-во ниво
	 */
	function serializeDocument(){		
		$lDocumentXmlNode = $this->m_document_xml_dom->documentElement->appendChild('document');
		$lDocumentXmlNode->setAttribute('id', $this->m_document_id);
		
		$lObjectsXmlNode = $lDocumentXmlNode->appendChild('objects');
		
		$lCon = new DBCn();
		$lCon->Open();
		$lObjectsSql = 'SELECT i.*, p.id as parent_id, char_length(i.pos)/2 as level  
			FROM document_object_instances i
			LEFT JOIN document_object_instances p ON p.document_id = i.document_id AND p.pos = substring(i.pos, 1, char_length(p.pos)) AND char_length(p.pos) = char_length(i.pos) - 2 
			WHERE i.document_id = ' . $this->m_document_id . ' 
			ORDER BY i.pos ';

		//Взимаме всичките обекти с една заявка.		
		$lCon->Execute($lObjectsSql);
		$lCon->MoveFirst();
		//Тук ще пазим обектите от 1во ниво		
		$lLevelOneFields = array();
		while(!$lCon->Eof()){
			$this->m_object_details[(int)$lCon->mRs['id']] = $lCon->mRs;
			$this->m_object_details[(int)$lCon->mRs['id']]['children'] = array();
			$this->m_object_details[(int)$lCon->mRs['id']]['fields'] = array();
			if((int)$lCon->mRs['level'] == 1){
				$lLevelOneFields[] = (int)$lCon->mRs['id'];
			}
			if((int)$lCon->mRs['parent_id']){
				$this->m_object_details[(int)$lCon->mRs['parent_id']]['children'][] = (int)$lCon->mRs['id'];
			}
		}
		
		$lCon->CloseRs();
		//Взимаме всичките field-ове с една заявка.	
		$lFieldsSql = 'SELECT f.id, f.type, fv.*
			FROM fields f			
			JOIN instance_field_values fv ON fv.field_id = f.id AND fv.document_id = ' . $this->m_document_id . ' 
			';
		$lCon->Execute($lFieldsSql);
		$lCon->MoveFirst();
		while(!$lCon->Eof()){
			$this->m_field_details[(int)$lCon->mRs['id']] = $lCon->mRs;
			/*
			 * Пазим field-овете към обекта, понеже id-то на field-овете не е уникално
			 * (може няколко обекта да имат field с едно и също id
			 */ 
			$this->m_object_details[(int)$lCon->mRs['instance_id']]['fields'][(int)$lCon->mRs['id']] = $lCon->mRs;			
		}
		
		//Сериализираме само главните обекти. Ще имаме рекурсия, която ще се грижи за дървото надолу
		foreach ($lLevelOneFields as $lObjectId) {
			$this->serializeObject($lObjectId, $lObjectsXmlNode);
		}
	}
	
	/**
	 * Тук ще сериализираме 1 инстанс обект. За целта подаваме id-то на инстанса 
	 * на обекта, както и парент възела в xml-a, където ще стои този обект
	 * @param $pObjectInstanceId	 
	 * @param $pParentXmlNode
	 */
	protected function serializeObject($pObjectInstanceId, &$pParentXmlNode){
		$lObjectData = $this->m_object_details[$pObjectInstanceId];
		$lObjectXmlNode = $pParentXmlNode->appendChild('object');		
		$lObjectXmlNode->setAttribute('object_id', $lObjectData['object_id']);
		$lObjectXmlNode->setAttribute('pos', $lObjectData['pos']);
		$lObjectXmlNode->setAttribute('display_in_tree', $lObjectData['display_in_tree']);
		
		//Първо сериализираме field-овете
		foreach ($lObjectData['fields'] as $lFieldId => $pFieldData) {
			$this->serializeField($lFieldId, $lObjectXmlNode, $pFieldData);
		}
		
		//След това сериализираме всички подобекти
		foreach ($lObjectData['children'] as $lChildInstanceId) {
			$this->serializeObject($lChildInstanceId, $lObjectXmlNode);
		}
		
	}
	
	/**
	 * 
	 * Тук серилиазираме 1 field. 	 
	 * @param unknown_type $pParentXmlNode - xml възела на instance-a в xml-a
	 * @param array $pFieldData - информацията за field-а	 
	 */
	protected function serializeField(&$pParentXmlNode, $pFieldData){		
		$lFieldXmlNode = $pParentXmlNode->appendChild('field');
		$lFieldXmlNode->setAttribute('id', $pFieldData['id']);
		switch($pFieldData['type']){
			default:
				$lFieldXmlNode->textContent = $pFieldData['value_str'];
		}
	}
}
?>