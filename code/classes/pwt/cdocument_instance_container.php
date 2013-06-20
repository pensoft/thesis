<?php
/**
 *
 * Този клас ще реализира показването на 1 контейнер от даден инстанс на даден документ.
 *
 * Класа ще работи без да се връзва към базата и за целта ще трябва да
 * му се подават всички параметри (вкл. и нещата в контейнера)
 *
 * За да може да показва подобекти трябва да са му дадени темплейти за instance в пубдатата под ключ instance_templs,
 */
class cdocument_instance_container extends csimple {
	var $m_documentId;
	var $m_instanceId;
	var $m_containerId;
	var $m_containerType;
	var $m_itemsType;
	var $m_items;
	var $m_objectsToAdd;
	var $m_itemsCount;
	var $m_level;
	var $m_getFieldDataFromRequest;
	var $m_getObjectModeFromRequest;
	var $m_fieldValidationInfo;
	//Това е id-то на главния инстанс който показваме. По него се определя нивото на вложеност
	var $m_rootInstanceId;
	var $m_cssClass;
	var $m_displayUnconfirmedObjects;

	var $m_itemsCachedDetails;

	var $m_usePreviewGenerator;
	var $m_previewGenerator;


	function __construct($pFieldTempl){
		parent::__construct($pFieldTempl);

		$this->m_instanceId = $pFieldTempl['instance_id'];
		$this->m_documentId = $pFieldTempl['document_id'];
		$this->m_containerId = $pFieldTempl['container_id'];
		$this->m_containerType = $pFieldTempl['container_type'];
		$this->m_itemsType = $pFieldTempl['items_type'];
		$this->m_items = is_array($pFieldTempl['items']) ? $pFieldTempl['items'] : array();
		$this->m_itemsCount = count($this->m_items);
		$this->m_pubdata['items_count'] = $this->m_itemsCount;
		$this->m_level = (int)$pFieldTempl['level'];
		$this->m_getFieldDataFromRequest = (int)$pFieldTempl['get_data_from_request'];
		$this->m_getObjectModeFromRequest = (int) $pFieldTempl['get_object_mode_from_request'];
		$this->m_fieldValidationInfo = $pFieldTempl['field_validation_info'];
		$this->m_rootInstanceId = $pFieldTempl['root_instance_id'];
		$this->m_objectsToAdd = $pFieldTempl['objects_to_add'];
		$this->m_cssClass = $pFieldTempl['css_class'];
		$this->m_displayUnconfirmedObjects = $pFieldTempl['display_unconfirmed_objects'];

		$this->m_usePreviewGenerator = $pFieldTempl['use_preview_generator'];
		$this->m_previewGenerator = $pFieldTempl['preview_generator'];

		if(!is_array($this->m_objectsToAdd)){
			$this->m_objectsToAdd = array();
		}

// 		var_dump($this->m_objectsToAdd);

		$this->LoadDefTempls();


	}

	/**
	 * @return the $m_itemsCachedDetails
	 */
	public function getItemsCachedDetails() {
		return $this->m_itemsCachedDetails;
	}


	/**
	 * @return the $m_itemsCount
	 */
	public function getItemsCount () {
		return $this->m_itemsCount;
	}

	function LoadDefTempls() {
		if (!defined('D_EMPTY')) {
			define('D_EMPTY', 'global.empty');
		}

		$this->m_defTempls = array(G_HEADER => D_EMPTY, G_FOOTER => D_EMPTY, G_STARTRS => D_EMPTY, G_ENDRS => D_EMPTY, G_NODATA => D_EMPTY, G_PAGEING => D_EMPTY, G_ROWTEMPL => D_EMPTY);
	}

	//Тук няма какво да правим понеже сме получили всичко наготово в конструктора
	function GetData(){

	}

	function GetItems(){
		$this->m_pubdata['rownum'] = 0;

		/*
		 * тук ще държим списък на обектите от които ни е позволено да добавяме, за да може
		 * накрая да сложим бутони за добавяне
		 */
		$lItemsCachedDetails = array();

		foreach ($this->m_items as $lItemData) {
			$lItem = null;
			$this->m_pubdata['item_css_class'] = $lItemData['container_item_css_class'];
			if($lItemData['item_type'] == CONTAINER_ITEM_FIELD_TYPE){//Field
				$lItemData['id'] = (int)$lItemData['field_id'];
// 				var_dump('Field Start : ' . date("Y/m/d H:i:s"). substr((string)microtime(), 1, 6) . '_' .  $lItemData['id']);
				$lItem = new cinstance_field(array(
					'templs' => $this->m_pubdata['field_templs'],
					'document_id' => (int)$this->m_documentId,
					'instance_id' => (int)$this->m_instanceId,
					'field_id' => (int)$lItemData['field_id'],
					'name' => $lItemData['name'],
					'type' => (int)$lItemData['type'],
					'label' => $lItemData['label'],
					'html_control_type' => (int)$lItemData['html_control_type'],
					'sql_value' => $lItemData['sql_value'],
					'allow_nulls' => (int)$lItemData['allow_nulls'],
					'is_html' => (int)$lItemData['is_html'],
					'is_array' => (int)$lItemData['is_array'],

					'has_help_label' => (int)$lItemData['has_help_label'],
					'help_label' => $lItemData['help_label'],
					'help_label_display_style' => (int)$lItemData['help_label_display_style'],

					'has_example_label' => (int)$lItemData['has_example_label'],
					'example_label' => $lItemData['example_label'],

					'data_src_id' => (int)$lItemData['data_src_id'],
					'src_query' => $lItemData['src_query'],
					'display_label' => $lItemData['display_label'],

					'autocomplete_row_template' => $lItemData['autocomplete_row_template'],
					'autocomplete_onselect' => $lItemData['autocomplete_onselect'],


					'css_class' => $lItemData['css_class'],
					'is_read_only' => $lItemData['is_read_only'],
					'get_data_from_request' => $this->m_getFieldDataFromRequest,
					'field_validation_info' => $this->m_fieldValidationInfo,
					'comments' => $lItemData['comments'],
				));
			}elseif($lItemData['item_type'] == CONTAINER_ITEM_OBJECT_TYPE){//instance
				$lItemData['id'] = (int)$lItemData['instance_id'];
				$lItem = new cdocument_instance(array(
					'templs' => $this->m_pubdata['instance_templs'],
					'document_id' => $this->m_documentId,
					'instance_id' => $lItemData['instance_id'],
					'container_templs' => $this->m_pubdata['templs'],
					'field_templs' => $this->m_pubdata['field_templs'],
					'action_templs' => $this->m_pubdata['action_templs'],
					'custom_html_templs' => $this->m_pubdata['custom_html_templs'],
					'tabbed_element_templs' => $this->m_pubdata['tabbed_element_templs'],
					'level' => $this->m_level + 1,
					'get_data_from_request' => $this->m_getFieldDataFromRequest,
					'get_object_mode_from_request' => $this->m_getObjectModeFromRequest,
					'field_validation_info' => $this->m_fieldValidationInfo,
					'root_instance_id' => $this->m_rootInstanceId,
					'display_unconfirmed_objects' => $this->m_displayUnconfirmedObjects,
					'use_preview_generator' => $this->m_usePreviewGenerator,
					'preview_generator' => $this->m_previewGenerator,
				));
				$lItem->GetData();
				$lItemData['top_actions'] = $lItem->GetVal('top_actions');
				$lItemData['bottom_actions'] = $lItem->GetVal('bottom_actions');
				$lItemData['right_actions'] = $lItem->GetVal('right_actions');
// 				if($lItem->GetVal('allow_add')){
// 					$this->m_objectsToAdd[$lItem->m_objectId] = $lItem->GetVal('instance_name');
// 				}
			}elseif($lItemData['item_type'] == CONTAINER_ITEM_TABBED_ITEM_TYPE){//tabbed елемент
				$lItem = new cdocument_tabbed_element(array(

					'document_id' => $this->m_documentId,
					'instance_id' => (int)$this->m_instanceId,
					'id' => $lItemData['tabbed_item_id'],
					'default_active_object_id' => $lItemData['default_active_object_id'],

					'instance_templs' => $this->m_pubdata['instance_templs'],
					'container_templs' => $this->m_pubdata['templs'],
					'field_templs' => $this->m_pubdata['field_templs'],
					'action_templs' => $this->m_pubdata['action_templs'],
					'custom_html_templs' => $this->m_pubdata['custom_html_templs'],
					'templs' => $this->m_pubdata['tabbed_element_templs'],
					'level' => $this->m_level + 1,
					'get_data_from_request' => $this->m_getFieldDataFromRequest,
					'get_object_mode_from_request' => $this->m_getObjectModeFromRequest,
					'field_validation_info' => $this->m_fieldValidationInfo,
					'root_instance_id' => $this->m_rootInstanceId,
					'items' => $lItemData['items'],
					'display_unconfirmed_objects' => $this->m_displayUnconfirmedObjects,
				));
				$lItem->GetData();
			}elseif($lItemData['item_type'] == CONTAINER_ITEM_CUSTOM_HTML_TYPE){//custom html
				$lItemData['id'] = (int)$lItemData['id'];
				$lItemData['content'] = $this->ReplaceHtmlFields($lItemData['content']);
				$lItem = new csimple(array(
					'templs' => $this->m_pubdata['custom_html_templs'],
					'document_id' => $this->m_documentId,
					'root_instance_id' => $this->m_rootInstanceId,
					'content' => $lItemData['content'],
					'id' => $lItemData['id'],
					'level' => $this->m_level + 1,
					'css_class' => $lItemData['css_class'],
				));
			}
			$this->m_pubdata['rownum']++;
			$this->m_pubdata['container_item_style'] = getContainerItemStyle($this->m_pubdata['item_css_class'] , $this->m_pubdata['container_type'], $this->m_pubdata['items_count']);
			$this->m_pubdata['container_item'] = $lItem->Display();
			$this->m_pubdata['container_item_type'] =  $lItemData['item_type'];
			$this->m_pubdata['container_item_id'] =  $lItemData['id'];
			$lRowHtml = $this->ReplaceHtmlFields($this->getObjTemplate(G_ROWTEMPL));
			$lRet .= $lRowHtml;

// 			if($lItemData['item_type'] == CONTAINER_ITEM_FIELD_TYPE){//Field
// 				var_dump('Field End : ' . date("Y/m/d H:i:s"). substr((string)microtime(), 1, 6) . '_' .  $lItemData['id']);
// 			}

			$lItemsCachedDetails[] = array(
				'container_item_style' => $this->m_pubdata['container_item_style'],
				'item_type' => $lItemData['item_type'],
				'item_html' => $lRowHtml,
				'item_id' => $lItemData['id'],
				'item_top_actions' => $lItemData['top_actions'],
				'item_bottom_actions' => $lItemData['bottom_actions'],
				'item_right_actions' => $lItemData['right_actions'],
			);



		}
		$this->m_itemsCachedDetails = $lItemsCachedDetails;

		$this->GetActions();
		return $lRet;


	}

	/**
	 * We create the buttons for adding allowed objects
	 */
	function GetActions(){
		$this->m_pubdata['actions'] = '';
		foreach ($this->m_objectsToAdd as $lObjectId => $lObjectData) {
			$lObjectName = $lObjectData['name'];
			$lCreateInPopup = (int)$lObjectData['create_in_popup'];
			$lActionId = (int)ACTIONS_ADD_NEW_INSTANCE_ID;
			$lJsAction = getActionDefaultJsActionWithParams((int)$lActionId, $this->m_instanceId, $lObjectId, $this->m_rootInstanceId);
			if($lCreateInPopup){
				$lJsAction = 'CreatePopup(' . $this->m_instanceId . ', ' . $lObjectId . ')';
			}
			$lCurrentAction = new caction_item(array(
				'id' => (int)$lActionId,
				'html_control_type' => (int)ACTION_HTML_ADD_BTN_TYPE,
				'instance_id' => $this->m_instanceId,
				'js_action' => $lJsAction,
				'display_name' => getstr('pwt.instance.addBtnLabel') . $lObjectName,
				'templs' => $this->m_pubdata['action_templs'],
			));
			$this->m_pubdata['actions'] .= $lCurrentAction->Display();
		}

	}

	function Display() {
		if (!$this->m_dontGetData)
			$this->GetData();

		$lRet = '';

		$lRet .= $this->ReplaceHtmlFields($this->getObjTemplate(G_HEADER));
		if ($this->m_itemsCount == 0 && !count($this->m_objectsToAdd)) {
			$lRet .= $this->ReplaceHtmlFields($this->getObjTemplate(G_NODATA));
		} else {
			$lRet .= $this->ReplaceHtmlFields($this->getObjTemplate(G_STARTRS));
			$lRet .= $this->GetItems();
			$lRet .= $this->ReplaceHtmlFields($this->getObjTemplate(G_ENDRS));
		}
		$lRet .= $this->ReplaceHtmlFields($this->getObjTemplate(G_FOOTER));


		return $lRet;
	}
}