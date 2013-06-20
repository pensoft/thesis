<?php
require_once PATH_CLASSES . 'comments.php';
/**
 *
 * Този клас ще реализира показването на 1 field на даден instance
 *
 * Класа ще се връзва към базата само за да вземе екшъните, които трябва да се поставят на field-а,
 * и за целта ще трябва да му се подават всички останали параметри
 *
 * Тъй като field-овете имат няколко полета в които се пази стойността,
 * ще имаме 1 променлива ($this->m_parsedFieldValue), в която ще пазим
 * стойността на променливата (аналогично на полето CurValue в kfor-a)
 */
class cinstance_field extends csimple {
	var $m_documentId;
	var $m_instanceId;
	var $m_fieldId;

	var $m_name;
	var $m_label;
	var $m_type;
	var $m_htmlControlType;
	var $m_sqlValue;
	var $m_allowNulls;
	var $m_hasHelpLabel;
	var $m_helpLabel;
	var $m_dataSrcId;
	var $m_srcQuery;
	var $m_getFieldDataFromRequest;
	var $m_hasValidationError;
	var $m_validationErrorMsg;
	var $m_actions;
	var $m_isReadOnly;
	var $m_comments;
	var $m_isHtml;
	var $m_isArray;


	//Отговяря на name атрибута на html полетата(input, select ...)
	var $m_fieldHtmlIdentifier;

	var $m_parsedFieldValue;

	/**
	 * Тук ще е обекта, който ще отговаря за реалното показване на field-а
	 * @var cfield_base
	 */
	var $m_field;

	function __construct($pFieldTempl){
		parent::__construct($pFieldTempl);

		$this->m_instanceId = (int)$pFieldTempl['instance_id'];
		$this->m_documentId = (int)$pFieldTempl['document_id'];
		$this->m_fieldId = (int)$pFieldTempl['field_id'];
		$this->m_name = $pFieldTempl['name'];
		$this->m_label = $pFieldTempl['label'];
		$this->m_type = (int)$pFieldTempl['type'];
		$this->m_htmlControlType = (int)$pFieldTempl['html_control_type'];
		$this->m_sqlValue = $pFieldTempl['sql_value'];
		$this->m_allowNulls = (int)$pFieldTempl['allow_nulls'];
		$this->m_hasHelpLabel = (int)$pFieldTempl['has_help_label'];
		$this->m_helpLabel = $pFieldTempl['help_label'];
		$this->m_dataSrcId = (int)$pFieldTempl['data_src_id'];
		$this->m_srcQuery = $pFieldTempl['src_query'];
		$this->m_isReadOnly = $pFieldTempl['is_read_only'];
		$this->m_getFieldDataFromRequest = (int)$pFieldTempl['get_data_from_request'];
		$this->m_comments = $pFieldTempl['comments'];
// 		var_dump($this->m_comments);

		$this->m_isArray = (int)$pFieldTempl['is_array'];
		$this->m_isHtml = (int)$pFieldTempl['is_html'];

		if($this->m_isReadOnly){
			$this->m_getFieldDataFromRequest = 0;
		}


		$this->m_hasValidationError = (int)$pFieldTempl['field_validation_info'][$this->m_instanceId][$this->m_fieldId]['has_validation_error'];
		$this->m_validationErrorMsg = (int)$pFieldTempl['field_validation_info'][$this->m_instanceId][$this->m_fieldId]['validation_err_msg'];

		$this->m_pubdata['has_validation_error'] = $this->m_hasValidationError;
		$this->m_pubdata['validation_err_msg'] = $this->m_validationErrorMsg;

		$this->m_fieldHtmlIdentifier = $this->m_instanceId . INSTANCE_FIELD_NAME_SEPARATOR . $this->m_fieldId;
		$this->parseFieldValue();
// 		var_dump('Field Actions Start ' . $this->m_fieldId . ':' . date("Y/m/d H:i:s"). substr((string)microtime(), 1, 6));
 		$this->getActionDetails();
// 		var_dump('Field Actions End ' . $this->m_fieldId . ':' . date("Y/m/d H:i:s"). substr((string)microtime(), 1, 6));


		$this->LoadDefTempls();


		$lFieldDetails = array_merge($pFieldTempl, array(
			'field_html_identifier' => $this->m_fieldHtmlIdentifier,
			'has_validation_error' => $this->m_hasValidationError,
			'validation_err_msg' => $this->m_validationErrorMsg,
			'parsed_field_value' => $this->m_parsedFieldValue,getActionDetails,
			'actions' => $this->m_actions,
		));
		$lFieldCreator = new cfield_factorycreator();
		$this->m_field = $lFieldCreator->createField($lFieldDetails);
	}

	/**
	 * Тук ще получим стойността в правилния еквивалентен php тип.
	 * В кфор-а аналогичната функционалност е в метода FetchResults.
	 * Ако е указано, че трябва да вземем стойността от пост-а - директно я взимаме
	 * от там без да правим никакви преобразувания (в случай че напр. се събмитне формата и
	 * стане грешка при save-а - в такъв случай стойностите трябва да останат каквито
	 * ги е написал юзъра).
	 */
	function parseFieldValue(){
		if($this->m_getFieldDataFromRequest){
			$this->m_parsedFieldValue = $_REQUEST[$this->m_fieldHtmlIdentifier] ;
			return;
		}
		$this->m_parsedFieldValue = parseFieldValue($this->m_sqlValue, $this->m_type);
		if($this->m_isHtml && !(int)$this->m_isArray && count($this->m_comments)){
			$this->m_parsedFieldValue = InsertFieldCommentPositionNodes($this->m_parsedFieldValue, $this->m_comments);
		}

	}

	protected function getActionDetails(){
		$lCon= new DBCn();
		$lCon->Open();
		$lSql = '
			SELECT a.id, a.js_action, fa.event
			FROM pwt.actions a
			JOIN pwt.object_field_actions fa ON fa.action_id = a.id
			JOIN pwt.object_fields of ON of.id = fa.object_field_id
			JOIN pwt.document_object_instances i ON i.object_id = of.object_id
			WHERE i.id = ' . (int)$this->m_instanceId . ' AND of.field_id = ' . $this->m_fieldId . '
		';
		$lCon->Execute($lSql);
		$this->m_actions = array();
		while(!$lCon->Eof()){
			$lJSAction = trim($lCon->mRs['js_action']);
			if(!$lJSAction){
				$lJSAction = getActionDefaultJsAction($lCon->mRs['id'], $this->m_instanceId);
			}
			$this->m_actions[] = array(
				'action_id' => (int)$lCon->mRs['id'],
				'event' => $lCon->mRs['event'],
				'js_action' =>  $lJSAction,
			);

			$lCon->MoveNext();
		}
// 		var_dump($this->m_fieldId, $this->m_actions);

	}

	function LoadDefTempls() {
		if (!defined('D_EMPTY')) {
			define('D_EMPTY', 'global.empty');
		}

		$this->m_defTempls = array(G_DEFAULT => D_EMPTY);
	}

	//Тук няма какво да правим понеже сме получили всичко наготово в конструктора
	function GetData(){

	}

	/**
	 *
	 * Връщаме текстовата репрезентация на field-а
	 */
	function GetToStringRepresentation(){
		return $this->m_field->GetToStringRepresentation();
	}

	/**
	 * Показваме field-a.
	 * Темплейта му се избира на база на html контролата на полето.
	 */
	protected function DisplayField() {
		return $this->m_field->Display();
	}


	function Display() {
		$this->GetData();

		$lRet = '';

		$lRet .= $this->ReplaceHtmlFields($this->getObjTemplate(G_HEADER));
		$lRet .= $this->DisplayField();
		$lRet .= $this->ReplaceHtmlFields($this->getObjTemplate(G_FOOTER));

		return $lRet;
	}

}

?>