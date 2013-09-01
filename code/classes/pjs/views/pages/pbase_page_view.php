<?php

/**
 * A base page view class - it sets the default templates for the default objects
 * All other view classes (if any) should extend (directly or not) this class.
 *
 * @author peterg
 *
 */
class pBase_Page_View extends epPage_View {
	function __construct($pData) {
		parent::__construct($pData);
		$this->m_objectsMetadata['mainmenu'] = array(
			'templs' => array(
				G_HEADER => 'menu.main-head',
				G_ROWTEMPL => 'menu.main-row',
				G_FOOTER => 'menu.main-foot'
			)
		);
		
		$this->m_objectsMetadata['journal_menu'] = array(
			'templs' => array(
				G_HEADER => 'menu.journal-head',
				G_STARTRS => 'menu.journal-startrs',
				G_ROWTEMPL => 'menu.journal-row',
				G_ENDRS => 'menu.journal-endrs',
				G_FOOTER => 'menu.journal-foot'
			)
		);
		
		$this->m_objectsMetadata['profile_template'] = array(
			'templs' => array(
				G_DEFAULT => 'global.profile_pic_and_name'
			)
		);
		
		$this->m_objectsMetadata['login_or_register'] = array(
			'templs' => array(
				G_DEFAULT => 'global.login_register'
			)
		);
		

		$this->m_objectsMetadata['journal_header_templ'] = array(
			'templs' => array(
				G_DEFAULT => 'global.journal_header'
			)
		);
		
		
		//var_dump($this->m_objectsMetadata);
		
		$this->m_objectsMetadata['dashboard_leftcol'] = array(
		'templs' => array(
			'G_DEFAULT' => 'dashboard.leftcol'
			)
		);
		
		$leftcols = array(
			'templs' => array(
				'G_STARTRS'  => 'dashboard.left.STARTRS',
				'G_ROWTEMPL' => 'dashboard.left.ROWTEMPL',
				'G_ENDRS'    => 'dashboard.endtable',
			)
		);
		
		$this->m_objectsMetadata['author_leftcol'] = $leftcols;
		$this->m_objectsMetadata['journal_editor_leftcol'] = $leftcols;
		$this->m_objectsMetadata['se_leftcol'] = $leftcols;
		$this->m_objectsMetadata['dedicated_reviewer_leftcol'] = $leftcols;
		$this->m_objectsMetadata['ce_leftcol'] = $leftcols;
		$this->m_objectsMetadata['le_leftcol'] = $leftcols;
		$this->m_objectsMetadata['journal_manager_leftcol'] = $leftcols;
		
		$this->m_objectsMetadata['your_tasks_leftcol'] = array(
			'templs' => array(
				'G_DEFAULT' => 'dashboard.your_tasks_leftcol'
			)
		);

		$this->m_defTempls[G_NODATA] = 'global.empty';
		$this->m_defTempls[G_HEADER] = 'global.empty';
		$this->m_defTempls[G_FOOTER] = 'global.empty';
		$this->m_defTempls[G_STARTRS] = 'global.empty';
		$this->m_defTempls[G_ENDRS] = 'global.empty';
		$this->m_defTempls[G_ROWTEMPL] = 'global.empty';
		$this->m_defTempls[G_PAGEING] = 'global.empty';

		// Default pageing templates
		$this->m_defTempls[G_PAGEING_STARTRS] = 'pageing.startrs';

		$this->m_defTempls[G_PAGEING_INACTIVEFIRST] = 'pageing.inactivefirst';

		$this->m_defTempls[G_PAGEING_ACTIVEFIRST] = 'pageing.activefirst';

		$this->m_defTempls[G_PAGEING_PGSTART] = 'pageing.prev';

		$this->m_defTempls[G_PAGEING_INACTIVEPAGE] = 'pageing.inactivepage';

		$this->m_defTempls[G_PAGEING_ACTIVEPAGE] = 'pageing.activepage';

		$this->m_defTempls[G_PAGEING_PGEND] = 'pageing.next';

		$this->m_defTempls[G_PAGEING_ENDRS] = 'pageing.endrs';

		$this->m_defTempls[G_PAGEING_DELIMETER] = 'pageing.delimeter';

		$this->m_defTempls[G_PAGEING_INACTIVELAST] = 'pageing.inactivelast';

		$this->m_defTempls[G_PAGEING_ACTIVELAST] = 'pageing.activelast';

		// Default form templates
		$this->m_defTempls[G_FORM_CAPTCHA_ROW] = 'form.captcha_row';

		$this->m_defTempls[G_FORM_FIELD_ERROR_ROW] = 'form.field_error_row';
		$this->m_defTempls[G_FORM_FIELD_ERROR_HEADER] = 'form.field_error_header';
		$this->m_defTempls[G_FORM_FIELD_ERROR_FOOTER] = 'form.field_error_footer';
		$this->m_defTempls[G_FORM_GLOBAL_ERROR_ROW] = 'form.global_error_row';

		$this->m_defTempls[G_FORM_SELECT_START] = 'form.select_input_start';
		$this->m_defTempls[G_FORM_MSELECT_START] = 'form.multiple_select_input_start';
		$this->m_defTempls[G_FORM_RADIO_START] = 'form.radio_input_start';
		$this->m_defTempls[G_FORM_CHECKBOX_START] = 'form.checkbox_input_start';

		$this->m_defTempls[G_FORM_SELECT_ROW] = 'form.select_input_row';

		$this->m_defTempls[G_FORM_MSELECT_ROW] = 'form.multiple_select_input_row';

		$this->m_defTempls[G_FORM_RADIO_ROW] = 'form.radio_input_row';

		$this->m_defTempls[G_FORM_CHECKBOX_ROW] = 'form.checkbox_input_row';

		$this->m_defTempls[G_FORM_SELECT_END] = 'form.select_input_end';

		$this->m_defTempls[G_FORM_MSELECT_END] = 'form.multiple_select_input_end';

		$this->m_defTempls[G_FORM_RADIO_END] = 'form.radio_input_end';

		$this->m_defTempls[G_FORM_CHECKBOX_END] = 'form.checkbox_input_end';

		$this->m_defTempls[G_FORM_CALENDAR_ROW] = 'form.calendar_row';
		$this->m_defTempls[G_FORM_RICHTEXT_EDITOR_ROW] = 'form.richtext_editor_row';
		$this->m_defTempls[G_FORM_TEXTAREA_ROW] = 'form.textarea_input_row';

		$this->m_defTempls[G_FORM_MULTILANGUAGE_FIELD_HEAD] = 'form.multilanguage_field_head';
		$this->m_defTempls[G_FORM_MULTILANGUAGE_FIELD_FOOT] = 'form.multilanguage_field_foot';
		$this->m_defTempls[G_FORM_MULTILANGUAGE_FIELD_ROW_HEAD] = 'form.multilanguage_field_row_head';
		$this->m_defTempls[G_FORM_MULTILANGUAGE_FIELD_ROW_FOOT] = 'form.multilanguage_field_row_foot';
		$this->m_defTempls[G_FORM_MULTILANGUAGE_RICHTEXT_EDITOR_ROW] = 'form.multilanguage_field_richtext_editor_row';

		$this->m_defTempls[G_FORM_MULTILANGUAGE_INPUT_ROW] = 'form.multilanguage_field_input_row';

		$this->m_defTempls[G_FORM_TEXT_INPUT_ROW] = 'form.text_input_row';
		$this->m_defTempls[G_FORM_PASSWORD_INPUT_ROW] = 'form.password_input_row';
		$this->m_defTempls[G_FORM_HIDDEN_INPUT_ROW] = 'form.hidden_input_row';
		$this->m_defTempls[G_FORM_FILE_INPUT_ROW] = 'form.file_input_row';

		$this->m_defTempls[G_FORM_ACTION_LINK_ROW] = 'form.action_link_row';
		$this->m_defTempls[G_FORM_ACTION_IMAGE_ROW] = 'form.action_image_row';
		$this->m_defTempls[G_FORM_ACTION_DEFAULT_ROW] = 'form.action_submit_btn_row';

		$this->m_defTempls[G_FORM_HEADER] = 'form.default_header';
		$this->m_defTempls[G_FORM_FOOTER] = 'form.default_footer';
		
		$this->m_defTempls[G_FORM_JS_VALIDATION] = 'form.js_validation';
		$this->m_defTempls[G_FORM_JS_ONLY] = 'form.js_only';
		
	}

}

?>