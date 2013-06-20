<?php
/**
 * Абстрактен базов клас за всички field-ове.
 * Ще има 2 метода - за визуализиране на field-a и за получаване на текстовата му репрезентация
 * @author peterg
 *
 */
abstract class cfield_base extends csimple {
	var $m_name;
	var $m_label;
	var $m_type;
	var $m_htmlControlType;
	var $m_allowNulls;
	var $m_hasHelpLabel;
	var $m_helpLabelDisplayStyle;
	var $m_helpLabel;
	var $m_dataSrcId;
	var $m_srcQuery;
	var $m_hasValidationError;
	var $m_validationErrorMsg;
	var $m_actions;
	var $m_isReadOnly;
	var $m_hasExampleLabel;
	var $m_exampleLabel;

	//Отговяря на name атрибута на html полетата(input, select ...)
	var $m_fieldHtmlIdentifier;

	var $m_parsedFieldValue;

	function __construct($pFieldTempl) {
		parent::__construct($pFieldTempl);

		$this->m_fieldHtmlIdentifier = $pFieldTempl['field_html_identifier'];
		$this->m_name = $pFieldTempl['name'];
		$this->m_label = $pFieldTempl['label'];
		$this->m_type = (int)$pFieldTempl['type'];
		$this->m_htmlControlType = (int)$pFieldTempl['html_control_type'];

		$this->m_sqlValue = $pFieldTempl['sql_value'];
		$this->m_allowNulls = (int)$pFieldTempl['allow_nulls'];
		$this->m_hasHelpLabel = (int)$pFieldTempl['has_help_label'];
		$this->m_helpLabel = $pFieldTempl['help_label'];
		$this->m_helpLabelDisplayStyle = $pFieldTempl['help_label_display_style'];

		$this->m_hasExampleLabel = $pFieldTempl['has_example_label'];
		$this->m_exampleLabel = $pFieldTempl['example_label'];

		$this->m_dataSrcId = (int)$pFieldTempl['data_src_id'];
		$this->m_srcQuery = $pFieldTempl['src_query'];
		$this->m_parsedFieldValue = $pFieldTempl['parsed_field_value'];


		$this->m_hasValidationError = $pFieldTempl['has_validation_error'];
		$this->m_validationErrorMsg = $pFieldTempl['validation_err_msg'];

		$this->m_isReadOnly = $pFieldTempl['is_read_only'];

		$this->m_actions = $pFieldTempl['actions'];
		if(!is_array($this->m_actions)){
			$this->m_actions = array();
		}


		$lActionsContent = '<script type="text/javascript">
								//<![CDATA[
							';
		foreach ($this->m_actions as $lKey => $lCurrentAction){
			$this->m_actions[$lKey]['js_action'] = $this->ReplaceHtmlFields($lCurrentAction['js_action']);
			$lCurrentAction = $this->m_actions[$lKey];
			if(preg_match('/classification_/i', $lCurrentAction['event'])){//Тези ивенти се слагат ръчно
				continue;
			}
			$lActionsContent .= 'addFieldAction(' . json_encode($this->m_fieldHtmlIdentifier) . ', '  . json_encode($lCurrentAction['event']) . ', ' . json_encode($lCurrentAction['js_action']) . ');';
		}

		$lActionsContent .= '
								//]]>
							 </script>';
		$this->m_pubdata['actions_content'] = $lActionsContent;
	}

	/**
	 * Връщаме текстовата репрезентация на field-а
	 */
	abstract function GetToStringRepresentation();


}


?>