<?php
/**
 * Този клас ще реализира показването на 1 инстанс от даден документ.
 *
 * Трябва да са му подадени и темплейти за контейнер и field.
 */
class cdocument_instance extends csimple {
	var $m_documentId;
	var $m_instanceId;
	var $m_con;
	var $m_containers;
	var $m_dontGetData;
	var $m_containerCount;
	var $m_level;
	var $m_getFieldDataFromRequest;
	var $m_getObjectModeFromRequest;
	var $m_fieldValidationInfo;
	//Това е id-то на главния инстанс който показваме. По него се определя нивото на вложеност
	var $m_rootInstanceId;
	var $m_mode;
	var $m_objectId;
	var $m_displayTitleAndTopActions;
	var $m_displayDefaultActions;
	var $m_isNew;

	var $m_documentTemplateObjectId;
	var $m_displayedActionPositions;
	var $m_allowedModes;
	var $m_displayUnconfirmedObjects;
	// За preview-тата
	var $m_templ_xsl_dir_name;
	var $m_view_xpath;
	var $m_view_mode;
	var $m_documentXML;
	var $m_fieldComments;
	var $m_xslPreview;
	var $m_xslPreviewIsCalculated;
	var $m_previewGenerator;
	var $m_usePreviewGenerator;
	var $m_returnPreviewGeneratorDisplay;
	function __construct($pFieldTempl) {
		parent::__construct( $pFieldTempl );
		$this->m_con = Con();
		$this->m_dontGetData = false;
		$this->m_containerCount = 0;
		$this->m_level = $this->m_pubdata['level'];
		$this->m_getFieldDataFromRequest = ( int ) $pFieldTempl['get_data_from_request'];
		$this->m_getObjectModeFromRequest = ( int ) $pFieldTempl['get_object_mode_from_request'];
		$this->m_fieldValidationInfo = $pFieldTempl['field_validation_info'];

		$this->m_instanceId = (int)$pFieldTempl['instance_id'];
		$this->m_rootInstanceId = (int)$pFieldTempl['root_instance_id'];
		$this->m_mode = $pFieldTempl['mode'];
		$this->m_displayUnconfirmedObjects = $pFieldTempl['display_unconfirmed_objects'];
		$this->m_xslPreviewIsCalculated = false;

		$this->m_usePreviewGenerator = $pFieldTempl['use_preview_generator'];
		$this->m_previewGenerator = $pFieldTempl['preview_generator'];
		$this->m_returnPreviewGeneratorDisplay = (bool)$pFieldTempl['return_preview_generator_display'];


		if($this->m_getObjectModeFromRequest){
			if(is_array($_REQUEST['instance_ids']) && in_array($this->m_instanceId, $_REQUEST['instance_ids'])){
				$this->m_mode = (int) INSTANCE_EDIT_MODE;
//				echo 1;
			}elseif(is_array($_REQUEST['instance_in_viewmode_ids']) && in_array($this->m_instanceId, $_REQUEST['instance_in_viewmode_ids'])){
				$this->m_mode = (int) INSTANCE_VIEW_MODE;
//				echo 2;
			}elseif(is_array($_REQUEST['instance_in_titlemode_ids']) && in_array($this->m_instanceId, $_REQUEST['instance_in_titlemode_ids'])){
				$this->m_mode = (int) INSTANCE_TITLE_MODE;
//				echo 3;
			}
		}


		$this->m_pubdata['mode'] = $this->m_mode;

		$this->m_containers = array();
		$this->LoadDefTempls();
		$this->InitBaseInfo();
		if($pFieldTempl['create_new_preview_generator']){
			$this->m_previewGenerator = new cinstance_preview_generator(array(
				'template_xsl_dirname' => $this->m_templ_xsl_dir_name,
				'document_id' => $this->m_documentId,
				'document_xml' => $this->m_documentXML,
			));
		}
		if(!$this->m_previewGenerator){
			$this->m_usePreviewGenerator = false;
			$this->m_returnPreviewGeneratorDisplay = false;
		}
		if((int)$this->m_usePreviewGenerator){
			$this->m_previewGenerator->registerInstance($this);
		}
	}

	/**
	 * Зареждаме базова информация за инстанса - напр. кой е object_id-то му и т.н.
	 */
	protected function InitBaseInfo() {
// 		var_dump('StartI  ' . $this->m_instanceId . ':' . date("Y/m/d H:i:s"). substr((string)microtime(), 1, 6) );
		$lSql = 'SELECT * FROM spGetInstanceBaseInfo('. (int) $this->m_instanceId . ', ' . (int)$this->m_mode . ');';

		$lLog = '/var/www/pensoft/log_stmnt.txt';
		$lStart = mktime() + substr((string)microtime(), 1, 6);
// 		if(!(int)USE_PREPARED_STATEMENTS){
// 			$lLog = '/var/www/pensoft/log_execute.txt';
			$this->m_con->Execute( $lSql );

// 		}else{
// 			$this->m_con->ExecutePreparedStatement('InstanceBaseInfo', array($this->m_instanceId, $this->m_mode));
// 		}
// 		$lEnd = mktime() + substr((string)microtime(), 1, 6);
// 		file_put_contents($lLog, "\n" . 'End Inst ' . $this->m_instanceId . ' ' . ($lEnd - $lStart), FILE_APPEND);
// 		$this->m_con->Execute($lSql);
		$this->m_documentId = (int)$this->m_con->mRs['document_id'];
		$this->m_pubdata['document_id'] = $this->m_documentId;
		$this->m_pubdata['instance_name'] = $this->m_con->mRs['display_name'];
		$this->m_pubdata['display_label'] = (int) $this->m_con->mRs['display_label'];
		$this->m_pubdata['label_display_style'] = (int) $this->m_con->mRs['title_display_style'];
		$this->m_pubdata['display_nesting_indicator'] = (int) $this->m_con->mRs['display_nesting_indicator'];
		$this->m_pubdata['instance_idx'] = (int)$this->m_con->mRs['idx'];
		$lAllowedModes = $this->m_con->mRs['allowed_modes'];
		$lAllowedModes = pg_unescape_array($lAllowedModes);
		$this->m_pubdata['allowed_modes'] = $lAllowedModes;
		$this->m_allowedModes = $lAllowedModes;
		$this->m_documentTemplateObjectId = (int)$this->m_con->mRs['dto_id'];
		$this->m_isNew = ( int ) $this->m_con->mRs['is_new'];
		$lInstanceHasFieldComments = (int)$this->m_con->mRs['has_field_comments'];

		if (! in_array( $this->m_mode, $lAllowedModes)) {
			if($this->m_isNew){
				$this->m_mode = ( int ) $this->m_con->mRs['default_new_mode_id'];
			}else{
				$this->m_mode = ( int ) $this->m_con->mRs['default_mode_id'];
			}
			$this->m_pubdata['mode'] = $this->m_mode;
		}


		$this->m_pubdata['css_class'] = $this->m_con->mRs['css_class'];
		$this->m_objectId = ( int ) $this->m_con->mRs['object_id'];
		$this->m_pubdata['object_id'] = $this->m_objectId;

		$this->m_templ_xsl_dir_name = $this->m_con->mRs['xsl_dir_name'];
		$this->m_view_xpath = $this->m_con->mRs['view_xpath_selection'];
		$this->m_view_mode = $this->m_con->mRs['view_xsl_template_mode'];

// 		var_dump($this->m_view_mode, $this->m_view_xpath);

//		var_dump($this->m_instanceId, $this->m_objectId, $this->m_pubdata['display_nesting_indicator']);
		$this->m_displayTitleAndTopActions = (int) $this->m_con->mRs['display_title_and_top_actions'];
		$this->m_pubdata['display_title_and_top_actions'] = $this->m_displayTitleAndTopActions;

		$this->m_displayDefaultActions = (int) $this->m_con->mRs['display_default_actions'];
		$this->m_pubdata['display_default_actions'] = $this->m_displayDefaultActions;
// 		$this->m_con->CloseRs();

		//Гледаме дали е възможно този инстанс да се мърда нагоре/надолу по дървото.
// 		$this->m_con->Execute( 'SELECT * FROM spCheckInstanceForAvailableMovement(' . (int) $this->m_instanceId . ')');
		$this->m_pubdata['allow_move_up'] = (int) $this->m_con->mRs['up'];
		$this->m_pubdata['allow_move_down'] = (int) $this->m_con->mRs['down'];
// 		$this->m_con->CloseRs();

		//Гледаме дали е възможно този инстанс да се изтрие и дали е възможно да се добавят още инстанси на този обект
// 		$this->m_con->Execute( 'SELECT * FROM spCheckInstanceForAvailableAddRemove(' . ( int ) $this->m_instanceId . ')' );
		$this->m_pubdata['allow_remove'] = (int) $this->m_con->mRs['allow_remove'];
		$this->m_pubdata['allow_add'] = (int) $this->m_con->mRs['allow_add'];

		//Гледаме какви екшъни ще показваме
// 		$this->m_con->Execute( 'SELECT dto.id, coalesce(at.displayed_positions, ARRAY[]::int[]) as displayed_positions
// 			FROM  pwt.document_template_objects dto
// 			LEFT JOIN pwt.object_displayed_actions_types_details at ON at.type_id = dto.displayed_actions_type AND at.mode = ' .(int)$this->m_mode . '
// 			WHERE dto.id = ' . ( int ) $this->m_documentTemplateObjectId );

// 		var_dump('SELECT dto.id, coalesce(at.displayed_positions, ARRAY[]::int[]) as displayed_positions
// 			FROM  pwt.document_template_objects dto
// 			LEFT JOIN pwt.object_displayed_actions_types_details at ON at.type_id = dto.displayed_actions_type AND at.mode = ' .(int)$this->m_mode . '
// 			WHERE dto.id = ' . ( int ) $this->m_documentTemplateObjectId );
		$this->m_displayedActionPositions = pg_unescape_array($this->m_con->mRs['displayed_positions']);
		if(!is_array($this->m_displayedActionPositions)){
			$this->m_displayedActionPositions = array();
		}

		$this->m_pubdata['allow_right_actions'] = in_array((int)ACTION_RIGHT_POS, $this->m_displayedActionPositions);

		$this->m_con->CloseRs();

		$this->m_fieldComments = array();
		if((int)$lInstanceHasFieldComments){
			$this->GetFieldComments();
		}
// 		var_dump('End  I  ' . $this->m_instanceId . ':' . date("Y/m/d H:i:s"). substr((string)microtime(), 1, 6) );
	}

	protected function GetFieldComments(){
		$lCon = $this->m_con;
		$lSql = '
			SELECT *
			FROM pwt.msg
			WHERE (start_object_instances_id = ' . (int)$this->m_instanceId . ' AND coalesce(start_object_field_id, 0) > 0 AND start_offset >= 0)
				OR (end_object_instances_id = ' . (int)$this->m_instanceId . ' AND coalesce(end_object_field_id, 0) > 0 AND end_offset >= 0)
		';
// 		$lCon->Execute($lSql);
// 		if(!(int)USE_PREPARED_STATEMENTS){
			$this->m_con->Execute( $lSql );
// 		}else{
// 			$this->m_con->ExecutePreparedStatement('FieldsComments', array($this->m_instanceId));
// 		}

		while(!$lCon->Eof()){
			$lRes = $lCon->mRs;
			$lPositions = array(
				COMMENT_START_POS_TYPE => 'start_',
				COMMENT_END_POS_TYPE => 'end_',
			);
			foreach ($lPositions as $lPositionType => $lPrefix){
				if((int)$lRes[$lPrefix . 'object_instances_id'] == $this->m_instanceId){
					$lFieldId = $lRes[$lPrefix . 'object_field_id'];
					$lCommentId = $lRes['id'];
					$lOffset = $lRes[$lPrefix . 'offset'];
					$lOffsetKeyName = $lPrefix . 'offset';


					if(!array_key_exists($lFieldId, $this->m_fieldComments)){
						$this->m_fieldComments[$lFieldId] = array();
					}
					if(!array_key_exists($lCommentId, $this->m_fieldComments[$lFieldId])){
						$this->m_fieldComments[$lFieldId][$lCommentId] = array(
							$lOffsetKeyName => $lOffset,
							'comment_pos_type' => $lPositionType
						);
					}else{
						$this->m_fieldComments[$lFieldId][$lCommentId][$lOffsetKeyName] = $lOffset;
						$this->m_fieldComments[$lFieldId][$lCommentId]['comment_pos_type'] = $this->m_fieldComments[$lFieldId][$lCommentId]['comment_pos_type'] | $lPositionType;
					}
				}
			}
			/*
			if((int)$lRes['start_object_instances_id'] == $this->m_instanceId){
				if(!array_key_exists($lRes['start_field_id'], $this->m_fieldComments)){
					$this->m_fieldComments[$lRes['start_field_id']] = array();
				}
				if(!array_key_exists($lRes['id'], $this->m_fieldComments[$lRes['start_field_id']])){
					$this->m_fieldComments[$lRes['start_field_id']][$lRes['id']] = array(
							'start_offset' => $lRes['start_offset'],
							'comment_type' => COMMENT_START_POS_TYPE
					);
				}else{
					$this->m_fieldComments[$lRes['start_field_id']][$lRes['id']]['start_offset'] = $lRes['start_offset'];
					$this->m_fieldComments[$lRes['start_field_id']][$lRes['id']]['comment_pos_type'] = $this->m_fieldComments[$lRes['start_field_id']][$lRes['id']]['comment_pos_type'] | COMMENT_START_POS_TYPE;
				}
			}
			if((int)$lRes['end_object_instances_id'] == $this->m_instanceId){
				if(!array_key_exists($lRes['end_field_id'], $this->m_fieldComments)){
					$this->m_fieldComments[$lRes['end_field_id']] = array();
				}
				if(!array_key_exists($lRes['id'], $this->m_fieldComments[$lRes['end_field_id']])){
					$this->m_fieldComments[$lRes['end_field_id']][$lRes['id']] = array(
						'end_offset' => $lRes['end_offset'],
						'comment_type' => COMMENT_END_POS_TYPE
					);
				}else{
					$this->m_fieldComments[$lRes['end_field_id']][$lRes['id']]['end_offset'] = $lRes['end_offset'];
					$this->m_fieldComments[$lRes['end_field_id']][$lRes['id']]['comment_pos_type'] = $this->m_fieldComments[$lRes['end_field_id']][$lRes['id']]['comment_pos_type'] | COMMENT_END_POS_TYPE;
				}
			}*/
			$lCon->MoveNext();
		}
// 		var_dump($this->m_fieldComments);
	}

	function LoadDefTempls() {
		if (! defined( 'D_EMPTY' )) {
			define( 'D_EMPTY', 'global.empty' );
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

	function CheckIfObjectIsInPreviewModeAndHasXslPreview(){
		if($this->m_mode != INSTANCE_VIEW_MODE) {
			return false;
		}
		if($this->GetXslPreview() == ''){
			return false;
		}
		return true;
	}

	function GetData($pInitForcefully = false) {
		if ($this->m_dontGetData && !$pInitForcefully)
			return;

		$this->GetActions();
		if(!$pInitForcefully && $this->CheckIfObjectIsInPreviewModeAndHasXslPreview()){
// 			var_dump($this->m_view_xpath , $this->m_view_mode);
// 			var_dump(1);
			$this->m_dontGetData = true;
			$this->m_containerCount = 1;//So that the preview can be displayed
			return;
		}
		$lChildSubinstancesWhere = '';
		if(!$this->m_displayUnconfirmedObjects){
			$lChildSubinstancesWhere .= ' AND iso.is_confirmed = true ';
		}
		$lSql = 'SELECT cd.container_id, oc.type as container_type, cd.item_type, oc.ord as container_ord, cd.css_class as container_item_css_class,
				oc.css_class as container_css_class,
				if.field_id, if.name as field_name, if.type as field_type, if.control_type as field_control_type, if.allow_nulls as field_allow_nulls, if.label as field_label,
				if.has_help_label as field_has_help_label, if.help_label as field_help_label, if.help_label_display_style as field_help_label_display_style,
				if.data_src_id as field_data_src_id, if.src_query as field_src_query,
				if.value_str as field_value_str, if.value_int as field_value_int, if.value_arr_int as field_value_arr_int, if.value_arr_str as field_value_arr_str,
				if.value_date as field_value_date, if.value_arr_date as field_value_arr_date, if.value_column_name as field_value_column_name,
				if.display_label as field_display_label, if.css_class as field_css_class, if.autocomplete_row_templ as field_autocomplete_row_templ, if.autocomplete_onselect as field_autocomplete_onselect,
				if.is_read_only as field_is_read_only, if.is_html as field_is_html, if.is_array as field_is_array,
				if.has_example_label as field_has_example_label, if.example_label as field_example_label,
				iso.id as subinstance_id, toc.object_id as child_object_id_to_add, toc.display_name as child_object_name, toc.create_in_popup::int as create_in_popup,
				chi.id as html_item_id, chi.content as html_item_content,
				ti.tabbed_item_id, ti.default_active_object_id, ti.css_class as tabbed_item_object_css_class
			FROM pwt.object_container_details cd
			JOIN pwt.object_containers oc ON oc.id = cd.container_id
			JOIN pwt.document_object_instances di ON di.id = '. (int) $this->m_instanceId . ' AND di.object_id = oc.object_id
			LEFT JOIN pwt.v_instance_fields if ON if.instance_id = di.id AND if.field_id = cd.item_id AND cd.item_type = ' . (int) CONTAINER_ITEM_FIELD_TYPE . '
			LEFT JOIN pwt.v_tabbed_items ti ON ti.tabbed_item_id = cd.item_id AND cd.item_type = ' . (int) CONTAINER_ITEM_TABBED_ITEM_TYPE . '
			LEFT JOIN pwt.document_object_instances iso ON iso.parent_id = di.id and iso.display_in_tree = false
				AND ((cd.item_type = ' . (int) CONTAINER_ITEM_OBJECT_TYPE . ' AND iso.object_id = cd.item_id)
					OR (cd.item_type = ' . (int) CONTAINER_ITEM_TABBED_ITEM_TYPE . ' AND iso.object_id = ti.object_id)
				) ' . $lChildSubinstancesWhere . '
			LEFT JOIN pwt.object_container_html_items chi ON chi.id = cd.item_id AND cd.item_type = ' . (int) CONTAINER_ITEM_CUSTOM_HTML_TYPE . '
			LEFT JOIN (SELECT * FROM spGetInstanceAllowedObjectsToAdd('. (int) $this->m_instanceId . ')) toc ON toc.instance_id = di.id
				AND toc.object_id =  cd.item_id AND cd.item_type = ' . (int) CONTAINER_ITEM_OBJECT_TYPE . '
			WHERE (iso.id IS NOT NULL OR if.field_id IS NOT NULL OR chi.id IS NOT NULL OR toc.object_id IS NOT NULL)
			ORDER BY oc.ord ASC, cd.ord ASC, ti.pos ASC, ti.tabbed_item_id ASC, iso.pos ASC
		';
// 		var_dump('Start ' . $this->m_instanceId . ':' . date("Y/m/d H:i:s"). substr((string)microtime(), 1, 6));
//
// 		if(!(int)USE_PREPARED_STATEMENTS){
			$this->m_con->Execute( $lSql );
// 		}else{
// 			$this->m_con->ExecutePreparedStatement('FieldsSelectAccepted', array($this->m_instanceId));
// 		}
// 		var_dump('After Sql ' . $this->m_instanceId . ':' . date("Y/m/d H:i:s"). substr((string)microtime(), 1, 6));
// 		exit;
//  		var_dump($lSql);

 		//~ exit;

// 		trigger_error($lSql, E_USER_NOTICE);

		$this->m_containers = array();
		$lPreviousTabbedItemId = 0;
		while(! $this->m_con->Eof()){
// 			var_dump($this->m_con->mRs['subinstance_id']);
			$lContainerId = (int) $this->m_con->mRs['container_id'];
			if(! is_array($this->m_containers[$lContainerId])){
				$this->m_containers[$lContainerId] = array(
					'container_type' => (int) $this->m_con->mRs['container_type'],
					'ord' => (int) $this->m_con->mRs['container_ord'],
					'items_type' => (int) $this->m_con->mRs['item_type'],
					'css_class' => $this->m_con->mRs['container_css_class'],
					'items' => array(),
					'objects_to_add' => array()
				);
			}
			if((int) $this->m_con->mRs['item_type'] == (int) CONTAINER_ITEM_FIELD_TYPE){
				$this->m_containers[$lContainerId]['items'][] = array(
					'item_type' => (int) $this->m_con->mRs['item_type'],
					'field_id' => (int) $this->m_con->mRs['field_id'],
					'name' => $this->m_con->mRs['field_name'],
					'label' => $this->m_con->mRs['field_label'],
					'type' => (int) $this->m_con->mRs['field_type'],
					'html_control_type' => (int) $this->m_con->mRs['field_control_type'],
					'allow_nulls' => (int) $this->m_con->mRs['field_allow_nulls'],

					'has_help_label' => (int) $this->m_con->mRs['field_has_help_label'],
					'help_label' => $this->m_con->mRs['field_help_label'],
					'help_label_display_style' => $this->m_con->mRs['field_help_label_display_style'],

					'has_example_label' => (int) $this->m_con->mRs['field_has_example_label'],
					'example_label' => $this->m_con->mRs['field_example_label'],

					'data_src_id' => (int) $this->m_con->mRs['field_data_src_id'],
					'src_query' => $this->m_con->mRs['field_src_query'],
					'sql_value' => $this->m_con->mRs['field_' . $this->m_con->mRs['field_value_column_name']],
					'display_label' => $this->m_con->mRs['field_display_label'],
					'css_class' => $this->m_con->mRs['field_css_class'],
					'container_item_css_class' => $this->m_con->mRs['container_item_css_class'],

					'autocomplete_row_template' => $this->m_con->mRs['field_autocomplete_row_templ'],
					'autocomplete_onselect' => $this->m_con->mRs['field_autocomplete_onselect'],

					'is_read_only' => $this->m_con->mRs['field_is_read_only'],
					'is_html' => $this->m_con->mRs['field_is_html'],
					'is_array' => $this->m_con->mRs['field_is_is_array'],
					'comments' => $this->m_fieldComments[$this->m_con->mRs['field_id']],
				);

			}elseif((int) $this->m_con->mRs['item_type'] == (int) CONTAINER_ITEM_OBJECT_TYPE){
//				var_dump($this->m_con->mRs['child_object_id_to_add']);
				if((int) $this->m_con->mRs['subinstance_id']){//Имаме да покажем инстанс
					$this->m_containers[$lContainerId]['items'][] = array(
						'item_type' => ( int ) $this->m_con->mRs['item_type'],
						'instance_id' => $this->m_con->mRs['subinstance_id'],
						'container_item_css_class' => $this->m_con->mRs['container_item_css_class']
					);
				}
				if((int) $this->m_con->mRs['child_object_id_to_add']){//Имаме тип обект, който да добавим

					$this->m_containers[$lContainerId]['objects_to_add'][$this->m_con->mRs['child_object_id_to_add']] = array(
						'name' => $this->m_con->mRs['child_object_name'],
						'create_in_popup' => (int)$this->m_con->mRs['create_in_popup'],
					);
				}
			} elseif((int) $this->m_con->mRs['item_type'] == (int) CONTAINER_ITEM_TABBED_ITEM_TYPE){//Елемент с табове
				$lTabbedItemId = (int) $this->m_con->mRs['tabbed_item_id'];

				if((int) $this->m_con->mRs['subinstance_id']){//Продължаваме надолу само ако имаме instance_id, което да добавим към елемента
					if($lPreviousTabbedItemId != $lTabbedItemId){//Нов обект с табове
						$this->m_containers[$lContainerId]['items'][] = array(
							'item_type' => ( int ) $this->m_con->mRs['item_type'],
							'tabbed_item_id' => $this->m_con->mRs['tabbed_item_id'],
							'default_active_object_id' => $this->m_con->mRs['default_active_object_id'],
							'container_item_css_class' => $this->m_con->mRs['container_item_css_class'],
							'items' => array(),
						);
					}
					$lTabbedItem = &$this->m_containers[$lContainerId]['items'][count($this->m_containers[$lContainerId]['items']) - 1];

					$lTabbedItem['items'][] = array(
						'instance_id' => $this->m_con->mRs['subinstance_id'],
						'css_class' => $this->m_con->mRs['tabbed_item_object_css_class'],
					);

// 					var_dump($lTabbedItem['items']);


				}

				if((int) $this->m_con->mRs['child_object_id_to_add']){//Имаме тип обект, който да добавим
					$this->m_containers[$lContainerId]['objects_to_add'][$this->m_con->mRs['child_object_id_to_add']] = $this->m_con->mRs['child_object_name'];
				}
			} elseif ((int) $this->m_con->mRs['item_type'] == ( int ) CONTAINER_ITEM_CUSTOM_HTML_TYPE) {
				$this->m_containers[$lContainerId]['items'][] = array(
					'item_type' => ( int ) $this->m_con->mRs['item_type'],
					'id' => $this->m_con->mRs['html_item_id'],
					'content' => $this->m_con->mRs['html_item_content'],
					'container_item_css_class' => $this->m_con->mRs['container_item_css_class']
				);
			}
			$lPreviousTabbedItemId =  (int) $this->m_con->mRs['tabbed_item_id'];
			$this->m_con->MoveNext();
		}
// 		var_dump('End   ' . $this->m_instanceId . ':' . date("Y/m/d H:i:s"). substr((string)microtime(), 1, 6) );
// 		var_dump($lSql);
//		var_dump($this->m_containers);
		//		var_dump($this->m_containers);
		$this->m_containerCount = count( $this->m_containers );

// 		$this->GetActions();
// 		var_dump('End Actions  ' . $this->m_instanceId . ':' . date("Y/m/d H:i:s"). substr((string)microtime(), 1, 6) );
// 		var_dump('End A ' . $this->m_instanceId . ':' . date("Y/m/d H:i:s"). substr((string)microtime(), 1, 6) );
		$this->m_dontGetData = true;

	}

	/**
	 * Зареждаме екшъните към обекта
	 */
	function GetActions() {

		//Записваме само тези които ни вършат работа
		$this->m_pubdata['top_pos_actions'] = array();
		$this->m_pubdata['bottom_pos_actions'] = array();
		$this->m_pubdata['right_pos_actions'] = array();


		$lAllowedPos = array_intersect($this->m_displayedActionPositions, array((int)ACTION_TOP_POS , (int)ACTION_BOTTOM_POS , (int)ACTION_RIGHT_POS ));
// 		var_dump($this->m_displayedActionPositions, $lAllowedPos);
// 		var_dump(array((int)ACTION_TOP_POS . ', ' . (int)ACTION_BOTTOM_POS . ', ' . (int)ACTION_RIGHT_POS ));
		if(!count($lAllowedPos)){
			$lAllowedPos = array(0);
		}

		$lAllowedPosWhere = implode(', ', $lAllowedPos);


		$lSql = 'SELECT a.id, a.preconditions, a.display_name, a.html_control_type, a.js_action, oa.pos, oa.ord, 2 as inner_ord
		FROM pwt.actions a
		JOIN pwt.object_actions oa ON oa.action_id = a.id
		WHERE oa.object_id = ' . ( int ) $this->m_objectId . '
		AND oa.pos IN (' . $lAllowedPosWhere . ')
		ORDER BY oa.pos ASC, oa.ord ASC

		';

		//Обединяваме двете заявки в 1 и взимаме 1-во default-ните
		if($this->m_displayDefaultActions){
			$lSql = '(SELECT a.id, da.preconditions, a.display_name, a.html_control_type, a.js_action, td.pos, td.ord, 1 as inner_ord
				FROM pwt.actions a
				JOIN pwt.object_default_actions da ON da.action_id = a.id
				JOIN pwt.object_default_actions_type_details td ON td.default_action_id = da.id AND td.mode = ' . (int)$this->m_mode . '
				JOIN pwt.object_default_actions_type at ON at.id = td.type_id
				JOIN pwt.document_template_objects dto ON dto.default_actions_type = at.id
				AND td.pos IN (' . $lAllowedPosWhere . ')
				WHERE dto.id = ' . (int) $this->m_documentTemplateObjectId . '
				ORDER BY td.pos ASC, td.ord ASC
				) UNION (' . $lSql . ')
				ORDER BY inner_ord ASC, pos ASC, ord ASC
			';
		}

		$this->m_con->Execute( $lSql );

// 		var_dump($lSql);
		while ( ! $this->m_con->Eof() ) {
			$lPreconditions = trim( $this->m_con->mRs['preconditions'] );


// 			trigger_error($lPreconditions, E_USER_NOTICE);
			if ($lPreconditions != '' && eval( $lPreconditions ) !== true) {
				$this->m_con->MoveNext();
				continue;
			}

			$lCurrentAction = new caction_item(array(
				'id' => (int) $this->m_con->mRs['id'],
				'html_control_type' => (int) $this->m_con->mRs['html_control_type'],
				'js_action' => $this->m_con->mRs['js_action'],
				'pos' => (int) $this->m_con->mRs['pos'],
				'instance_id' => $this->m_instanceId,
				'document_id' => $this->m_documentId,
				'display_name' => $this->m_con->mRs['display_name'],
				'allow_move_up' => $this->m_pubdata['allow_move_up'],
				'allow_move_down' => $this->m_pubdata['allow_move_down'],
				'templs' => $this->m_pubdata['action_templs']
			));

			switch ((int) $this->m_con->mRs['pos']) {
				case (int) ACTION_TOP_POS :
					$this->m_pubdata['top_pos_actions'][] = $lCurrentAction;
					break;
				case (int) ACTION_BOTTOM_POS :
					$this->m_pubdata['bottom_pos_actions'][] = $lCurrentAction;
					break;
				case (int) ACTION_RIGHT_POS :
					$this->m_pubdata['right_pos_actions'][] = $lCurrentAction;
					break;
			}
			$this->m_con->MoveNext();

		}

		$this->m_pubdata['top_pos_actions_cnt'] = count( $this->m_pubdata['top_pos_actions'] );
		foreach ( $this->m_pubdata['top_pos_actions'] as $lCurrentAction ) {
			$this->m_pubdata['top_actions'] .= $lCurrentAction->Display();
		}

		$this->m_pubdata['bottom_pos_actions_cnt'] = count( $this->m_pubdata['bottom_pos_actions'] );
		foreach ( $this->m_pubdata['bottom_pos_actions'] as $lCurrentAction ) {
			$this->m_pubdata['bottom_actions'] .= $lCurrentAction->Display();
		}

		$this->m_pubdata['right_pos_actions_cnt'] = count( $this->m_pubdata['right_pos_actions'] );
		foreach ( $this->m_pubdata['right_pos_actions'] as $lCurrentAction ) {
			$this->m_pubdata['right_actions'] .= $lCurrentAction->Display();
		}

	}

	function GetContainers() {
		foreach ( $this->m_containers as $lContainerId => $lContainerData ) {
// 			var_dump('Container Start ' . $this->m_instanceId . ' ' . $lContainerId . ':' . date("Y/m/d H:i:s"). substr((string)microtime(), 1, 6));
			$lContainer = new cdocument_instance_container( array(
				'instance_id' => $this->m_instanceId,
				'document_id' => $this->m_documentId,
				'container_id' => $lContainerId,
				'instance_idx' => $this->m_pubdata['instance_idx'],
				'container_type' => $lContainerData['container_type'],
				'items_type' => $lContainerData['items_type'],
				'items' => $lContainerData['items'],
				'templs' => $this->m_pubdata['container_templs'],
				'instance_templs' => $this->m_pubdata['templs'],
				'action_templs' => $this->m_pubdata['action_templs'],
				'field_templs' => $this->m_pubdata['field_templs'],
				'custom_html_templs' => $this->m_pubdata['custom_html_templs'],
				'tabbed_element_templs' => $this->m_pubdata['tabbed_element_templs'],
				'level' => $this->m_level,
				'get_data_from_request' => $this->m_getFieldDataFromRequest,
				'get_object_mode_from_request' => $this->m_getObjectModeFromRequest,
				'field_validation_info' => $this->m_fieldValidationInfo,
				'root_instance_id' => $this->m_rootInstanceId,
				'objects_to_add' => $lContainerData['objects_to_add'],
				'css_class' => $lContainerData['css_class'],
				'display_unconfirmed_objects' => $this->m_displayUnconfirmedObjects,
				'use_preview_generator' => $this->m_usePreviewGenerator,
				'preview_generator' => $this->m_previewGenerator,
			) );
			$lRet .= $lContainer->Display();
			$this->m_pubdata['rownum'] ++;
// 			var_dump('Container End   ' . $this->m_instanceId . ' ' . $lContainerId . ':' . date("Y/m/d H:i:s"). substr((string)microtime(), 1, 6));
		}


		return $lRet;
	}

	function GetToStringRepresentation() {
		if (! $this->m_dontGetData)
			$this->GetData();
		$lResult = '';
		$lItemCnt = 0;
		foreach ( $this->m_containers as $lContainerId => $lContainerData ) {

			foreach ( $lContainerData['items'] as $lCurrentItem ) {
				if($lCurrentItem['item_type'] == CONTAINER_ITEM_CUSTOM_HTML_TYPE){
					continue;
				}
				$lItem = NULL;
				if ($lItemCnt > 0) {
					$lResult .= ITEMS_STRING_REPRESENTATION_DELIMITER;
				}
				$lItemCnt ++;

				if ($lCurrentItem['item_type'] == CONTAINER_ITEM_FIELD_TYPE) {

					$lItem = new cinstance_field( array(
						'document_id' => ( int ) $this->m_documentId,
						'instance_id' => ( int ) $this->m_instanceId,
						'field_id' => ( int ) $lCurrentItem['field_id'],
						'get_data_from_request' => false,
						'field_validation_info' => $this->m_fieldValidationInfo,

						'name' => $lCurrentItem['name'],
						'type' => ( int ) $lCurrentItem['type'],
						'label' => $lCurrentItem['label'],
						'html_control_type' => ( int ) $lCurrentItem['html_control_type'],
						'sql_value' => $lCurrentItem['sql_value'],
						'allow_nulls' => ( int ) $lCurrentItem['allow_nulls'],
						'has_help_label' => ( int ) $lCurrentItem['has_help_label'],
						'help_label' => $lCurrentItem['help_label'],
						'data_src_id' => ( int ) $lCurrentItem['data_src_id'],
						'src_query' => $lCurrentItem['src_query']
					) );

				} elseif ($lCurrentItem['item_type'] == CONTAINER_ITEM_OBJECT_TYPE) {

					$lItem = new cdocument_instance(array(
						'document_id' => $this->m_documentId,
						'instance_id' => $lCurrentItem['instance_id'],
						'level' => $this->m_level + 1,
						'get_data_from_request' => false,
						'get_object_mode_from_request' => false,
						'field_validation_info' => $this->m_fieldValidationInfo,
						'root_instance_id' => $this->m_rootInstanceId
					));
				}else{
					$lItem = NULL;
				}

				if($lItem)
					$lResult .= $lItem->GetToStringRepresentation();
			}
		}
		return $lResult;
	}

	function SetMode($pMode){
		if(in_array($pMode, $this->m_allowedModes)){
			$this->m_mode = $pMode;
			$this->m_pubdata['mode'] = $pMode;
		}
	}

	function GetDocumentXml() {
		if(!$this->m_documentXML){
			$this->m_documentXML = getDocumentXml($this->m_documentId, SERIALIZE_INTERNAL_MODE, 1, 1, (int)$this->m_instanceId);
		}
	}

	function Display() {
		if(! $this->m_dontGetData)
			$this->GetData();

		$lRet = '';
		if((int) $this->m_displayTitleAndTopActions){
			$this->m_pubdata['instance_label'] = $this->ReplaceHtmlFields($this->getObjTemplate(G_LABEL, $this->m_pubdata['label_display_style']));
		}else{
			$this->m_pubdata['instance_label'] = '';
		}


		$lRet .= $this->ReplaceHtmlFields($this->getObjTemplate(G_HEADER));

		if($this->m_containerCount == 0){
			$lRet .= $this->ReplaceHtmlFields($this->getObjTemplate(G_NODATA));
		}else{
			$lRet .= $this->ReplaceHtmlFields($this->getObjTemplate(G_STARTRS));
			switch ($this->m_mode) {
				case INSTANCE_EDIT_MODE :
					$lRet .= $this->GetContainers();
					break;
				case INSTANCE_VIEW_MODE :
					//~ $lRet .= $this->GetToStringRepresentation();
					$lRet .= $this->GetPreviewMode();
					break;
				case INSTANCE_TITLE_MODE :

					break;
			}
			$lRet .= $this->ReplaceHtmlFields($this->getObjTemplate(G_ENDRS));
		}
		$lRet .= $this->ReplaceHtmlFields($this->getObjTemplate(G_FOOTER));

		if($this->m_returnPreviewGeneratorDisplay && $this->m_previewGenerator){
			$this->GetDocumentXml();
			$this->m_previewGenerator->SetTemplate($lRet);
			$this->m_previewGenerator->SetDocumentXml($this->m_documentXML);
			$lRet = $this->m_previewGenerator->Display();
		}
		return $lRet;
	}


	function GetXslPreview($pForceful = false){
// 		return '';
		if($this->m_xslPreviewIsCalculated && !$pForceful){
			return $this->m_xslPreview;
		}
		$this->m_xslPreviewIsCalculated = true;		
		if(!$pForceful && $this->m_usePreviewGenerator){
			$this->m_xslPreview = '{%' . $this->m_instanceId . '%}';
			return $this->m_xslPreview;
		}
		
		if(file_exists(PATH_XSL  . $this->m_templ_xsl_dir_name . "/template_example_preview_base.xsl") && file_exists(PATH_XSL  . $this->m_templ_xsl_dir_name . "/template_example_preview_custom.xsl") && $this->m_view_xpath && $this->m_view_mode) {			
			$this->GetDocumentXml();
			$docroot = getenv('DOCUMENT_ROOT');
			require_once($docroot . '/lib/static_xsl.php');
			$lXslParameters = array();

			$lDomDoc = new DOMDocument();
			$lDomDoc->formatOutput = true;

			// XSL stylesheet node
			$lStylesheet = $lDomDoc->createElementNS("http://www.w3.org/1999/XSL/Transform", "xsl:stylesheet");
			$lStylesheet->setAttribute("xmlns:xlink", "http://www.w3.org/1999/xlink");
			$lStylesheet->setAttribute("xmlns:tp", "http://www.plazi.org/taxpub");
			$lStylesheet->setAttribute("xmlns:php", "http://php.net/xsl");
			$lStylesheet->setAttribute("xmlns:exslt", "http://exslt.org/common");
			$lStylesheet->setAttribute("exclude-result-prefixes", "php tp xlink xsl");
			$lStylesheet->setAttribute('version', '1.0');
			$lDomDoc->appendChild($lStylesheet);

			// Import base xsl file
			$lBaseXsl = $lDomDoc->createElement("xsl:import");
			$lBaseXsl->setAttribute("href", PATH_XSL  . $this->m_templ_xsl_dir_name . "/template_example_preview_base.xsl");
			$lStylesheet->appendChild($lBaseXsl);

			// Import custom xsl file
			$lCustomXsl = $lDomDoc->createElement("xsl:import");
			$lCustomXsl->setAttribute("href", PATH_XSL  . $this->m_templ_xsl_dir_name . "/template_example_preview_custom.xsl");
			$lStylesheet->appendChild($lCustomXsl);

			// Import references xsl file
			$lReferencesXsl = $lDomDoc->createElement("xsl:import");
			$lReferencesXsl->setAttribute("href", PATH_XSL  .  "common_reference_preview.xsl");
			$lStylesheet->appendChild($lReferencesXsl);
			
			$lStaticXsl = $lDomDoc->createElement("xsl:import");
			$lStaticXsl->setAttribute("href", PATH_XSL  .  "static2.xsl");
			$lStylesheet->appendChild($lStaticXsl);
			
			$lTaxonXsl = $lDomDoc->createElement("xsl:import");
			$lTaxonXsl->setAttribute("href", PATH_XSL  .  "taxon.xsl");
			$lStylesheet->appendChild($lTaxonXsl);

			// XSL matching root node
			$lRootMatch = $lDomDoc->createElement("xsl:template");
			$lRootMatch->setAttribute("match", "/document");
			$lStylesheet->appendChild($lRootMatch);

			// Applying template with xpath selection and view mode
			$lVariable = $lDomDoc->createElement("xsl:variable");
			$lVariable->setAttribute('name', 'instance_id0');
			$lTemplate = $lVariable->appendChild($lDomDoc->createElement("xsl:apply-templates"));
			$lTemplate->setAttribute("select", replaceInstancePreviewField($this->m_view_xpath, $this->m_instanceId));
			$lTemplate->setAttribute("mode", $this->m_view_mode);
			$lFunctionCall = $lDomDoc->createElement("xsl:value-of");
			$lFunctionCall->setAttribute('select', 'php:function(\'SaveInstancePreview\', 0, exslt:node-set($instance_id0))');
			$lRootMatch->appendChild($lVariable);
			$lRootMatch->appendChild($lFunctionCall);



			// Output formatting node
			$lOutput = $lDomDoc->createElement("xsl:output");
			$lOutput->setAttribute("method", "html");
			$lOutput->setAttribute("encoding", "UTF-8");
			$lOutput->setAttribute("indent", "yes");
			$lStylesheet->appendChild($lOutput);

// 			var_dump($lDomDoc->saveXML());
			//~ echo $this->m_documentXML;
			//~ exit;

			//~ $lXslPath = PATH_XSL  . $this->m_templ_xsl_dir_name . '/template_example_preview_full.xsl';
			//~ echo $lXslPath;
			//~ echo  $lDocumentSerializer->getXml();
			//~ echo $this->m_documentXML;
			//~ exit;

			$lHtml = transformXmlWithXsl($this->m_documentXML, $lDomDoc->saveXML(), $lXslParameters, 0);
// 			error_reporting(0);
			//~ $lHtml = transformXmlWithXsl($this->m_documentXML, $lXslPath, $lXslParameters, 1);
			//~ error_reporting(-1);
			//~ echo $lDomDoc->saveXML();
			// 			 var_dump($lHtml);
			//~ exit;
// 			var_dump(GetInstancePreview(0));
// 			var_dump($lDomDoc->saveXML());

			$lHtml = GetInstancePreview(0);

			if($lHtml){
				$this->m_xslPreview = $lHtml;
			}
		}
		return $this->m_xslPreview;
	}

	function GetPreviewMode() {
// 		var_dump($this->m_view_xpath , $this->m_view_mode);
		$lResult = $this->GetXslPreview();
		if(!$lResult){
			$lResult = $this->GetToStringRepresentation();
		}
		return $lResult;

	}
}

?>