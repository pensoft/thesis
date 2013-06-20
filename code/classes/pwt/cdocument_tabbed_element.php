<?php
/**
 *
 * Този клас ще реализира показването на 1 tabbed елемент
 */
class cdocument_tabbed_element extends csimple {
	var $m_documentId;
	var $m_instanceId;

	var $m_name;
	var $m_getFieldDataFromRequest;
	var $m_getObjectModeFromRequest;
	var $m_fieldValidationInfo;
	// Това е id-то на главния инстанс който показваме. По него се определя
	// нивото на вложеност
	var $m_rootInstanceId;
	var $m_defaultActiveItemObjectId;
	var $m_defaultActiveItemId;

	var $m_tabbedElementId;

	var $m_itemsCachedDetails;
	var $m_activeItemId;
	var $m_displayUnconfirmedObjects;

	function __construct($pFieldTempl) {
		parent::__construct($pFieldTempl);

		$this->m_instanceId = $pFieldTempl['instance_id'];
		$this->m_documentId = $pFieldTempl['document_id'];
		$this->m_items = is_array($pFieldTempl['items']) ? $pFieldTempl['items'] : array();
		$this->m_itemsCount = count($this->m_items);
		$this->m_pubdata['items_count'] = $this->m_itemsCount;
		$this->m_level = (int) $pFieldTempl['level'];
		$this->m_getFieldDataFromRequest = (int) $pFieldTempl['get_data_from_request'];
		$this->m_getObjectModeFromRequest = (int) $pFieldTempl['get_object_mode_from_request'];
		$this->m_fieldValidationInfo = $pFieldTempl['field_validation_info'];
		$this->m_rootInstanceId = $pFieldTempl['root_instance_id'];
		$this->m_cssClass = $pFieldTempl['css_class'];
		$this->m_defaultActiveItemObjectId = $pFieldTempl['default_active_object_id'];
		$this->m_tabbedElementId = $pFieldTempl['id'];
		$this->LoadDefTempls();
		$this->m_pubdata['tabbed_element_id'] = $this->m_tabbedElementId;
		$this->m_activeItemId = $pFieldTempl['active_item_id'];
		$this->m_displayUnconfirmedObjects = $pFieldTempl['display_unconfirmed_objects'];

		if((int)$this->m_getFieldDataFromRequest){
			$this->m_activeItemId = $_REQUEST['tabbed_element_' . $this->m_instanceId . '_' . $this->m_tabbedElementId . '_active_item'];
		}
		// var_dump($pFieldTempl);

	}

	/**
	 *
	 * @return the $m_itemsCachedDetails
	 */
	public function getItemsCachedDetails() {
		return $this->m_itemsCachedDetails;
	}

	/**
	 *
	 * @return the $m_itemsCount
	 */
	public function getItemsCount() {
		return $this->m_itemsCount;
	}

	function LoadDefTempls() {
		if(! defined('D_EMPTY')){
			define('D_EMPTY', 'global.empty');
		}

		$this->m_defTempls = array(
			G_HEADER => D_EMPTY,
			G_FOOTER => D_EMPTY,
			G_STARTRS => D_EMPTY,
			G_ENDRS => D_EMPTY,
			G_NODATA => D_EMPTY,
			G_PAGEING => D_EMPTY,
			G_ROWTEMPL => D_EMPTY
		);
	}

	// Тук няма какво да правим понеже сме получили всичко наготово в
	// конструктора
	function GetData() {

	}

	function GetItems() {
		$this->m_pubdata['rownum'] = 0;

		$lItemsCachedDetails = array();
		$lActiveElementFound = 0;

		// Взимаме всички елементи. Обикаляме ги на 2 пъти за да може
		// при показването да покажем коректно активния
		$lItems = array();
		foreach($this->m_items as $lItemData){

			$lItem = null;
			$this->m_pubdata['item_css_class'] = $lItemData['css_class'];
			$lItem = new cdocument_instance(array(
				'templs' => $this->m_pubdata['instance_templs'],
				'document_id' => $this->m_documentId,
				'instance_id' => $lItemData['instance_id'],
				'container_templs' => $this->m_pubdata['container_templs'],
				'field_templs' => $this->m_pubdata['field_templs'],
				'action_templs' => $this->m_pubdata['action_templs'],
				'custom_html_templs' => $this->m_pubdata['custom_html_templs'],
				'tabbed_element_templs' => $this->m_pubdata['tabbed_element_templs'],
				'level' => $this->m_level,
				'get_data_from_request' => $this->m_getFieldDataFromRequest,
				'get_object_mode_from_request' => $this->m_getObjectModeFromRequest,
				'field_validation_info' => $this->m_fieldValidationInfo,
				'root_instance_id' => $this->m_rootInstanceId,
				'display_unconfirmed_objects' => $this->m_displayUnconfirmedObjects,
			));
			$lItem->GetData();
			$lItems[] = $lItem;
// 			var_dump($lItem->Display());
			// Взимаме за default-ен първият инстанс с подаденото object_id
			if(! $this->m_defaultActiveItemId && $lItem->m_objectId == $this->m_defaultActiveItemObjectId){
				$this->m_defaultActiveItemId = $lItemData['instance_id'];
				if(! (int) $this->m_activeItemId){
					$this->m_activeItemId = $this->m_defaultActiveItemId;
				}
			}

			if($lItemData['instance_id'] == $this->m_activeItemId){
				$lActiveElementFound = 1;
			}

		}
// 		var_dump($lItems);
		// Ако не е избран активен, или активния го няма - слагаме 1я
// 		var_dump((int) $this->m_activeItemId , ! (int) $this->m_activeItemId || ! $lActiveElementFound, $lActiveElementFound);
		if((! (int) $this->m_activeItemId || ! $lActiveElementFound) && count($lItems)){
			$this->m_activeItemId = $lItems[0]->m_instanceId;
// 			var_dump($this->m_activeItemId);
		}
		$this->m_pubdata['active_element_id'] = $this->m_activeItemId;
// 		var_dump($this->m_pubdata['active_element_id']);

		foreach($lItems as $lCurrentItem){
			$this->m_pubdata['rownum'] ++;
			//Важно е тук обектите да могат да имат и Edit и View mode
			if($lCurrentItem->m_instanceId == $this->m_activeItemId){
				$lCurrentItem->SetMode(INSTANCE_EDIT_MODE);
			}else{
				$lCurrentItem->SetMode(INSTANCE_VIEW_MODE);
			}
			$this->m_pubdata['item'] = $lCurrentItem->Display();

			$this->m_pubdata['item_id'] = $lCurrentItem->m_instanceId;
			$lRowHtml = $this->ReplaceHtmlFields($this->getObjTemplate(G_ROWTEMPL));
			$lRet .= $lRowHtml;

			$lItemsCachedDetails[] = array(
				'container_item_style' => $this->m_pubdata['container_item_style'],
				'item_type' => CONTAINER_ITEM_OBJECT_TYPE,
				'item_html' => $lRowHtml,
				'item_id' => $lCurrentItem->m_instanceId,
				'item_title' => $lCurrentItem->GetVal('instance_name'),
				'item_top_actions' => $lCurrentItem->GetVal('top_actions'),
				'item_bottom_actions' => $lCurrentItem->GetVal('bottom_actions')
			);

		}

		$this->m_itemsCachedDetails = $lItemsCachedDetails;

		return $lRet;

	}

	function GetTabs() {
		$lTabs = '';

		foreach($this->m_itemsCachedDetails as $lCurrentItem){
			$this->m_pubdata['current_tab_element_id'] = $lCurrentItem['item_id'];
			$this->m_pubdata['current_tab_element_title'] = $lCurrentItem['item_title'];
			$lTabs .= $this->ReplaceHtmlFields($this->getObjTemplate(G_TAB_ROWTEMPL));
		}
		$this->m_pubdata['tabs'] = $lTabs;
	}

	function Display() {
		if(! $this->m_dontGetData)
			$this->GetData();

		$lRet = '';
		$lItems = $this->GetItems();
		$lRet .= $this->ReplaceHtmlFields($this->getObjTemplate(G_HEADER));
		if($this->m_itemsCount == 0){
			$lRet .= $this->ReplaceHtmlFields($this->getObjTemplate(G_NODATA));
		}else{

			$this->GetTabs();
			$lRet .= $this->ReplaceHtmlFields($this->getObjTemplate(G_STARTRS));
			$lRet .= $lItems;
			$lRet .= $this->ReplaceHtmlFields($this->getObjTemplate(G_ENDRS));
		}
		$lRet .= $this->ReplaceHtmlFields($this->getObjTemplate(G_FOOTER));

		return $lRet;
	}
}