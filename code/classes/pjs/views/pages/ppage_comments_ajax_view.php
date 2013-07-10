<?php

/**
 * The view class for the browse journal issues page
 */
class pPage_Comments_Ajax_View extends epPage_Json_View {
	function __construct($pData) {
		parent::__construct($pData);

		$this->m_objectsMetadata['comment_preview'] = array(
			'templs' => array(
				G_DEFAULT => 'comments.newCommentRow'
			)
		);

		$this->m_objectsMetadata['comment_reply_form'] = array(
			'templs' => array(
				G_FORM_TEMPLATE => 'comments.reply_form',
			)
		);
		
		$this->m_objectsMetadata['edit_comment_form'] = array(
			'templs' => array(
				G_FORM_TEMPLATE => 'comments.editform',
			)
		);

		$this->m_objectsMetadata['first_comment_preview'] = array(
			'templs' => array(
				G_DEFAULT => 'comments.newCommentRowFirst'
			)
		);
		
		$this->m_objectsMetadata['comment_edit_preview'] = array(
			'templs' => array(
					G_DEFAULT => 'comments.viewRow'
			)
		);

		$this->m_objectsMetadata['reply_preview'] = array(
			'templs' => array(
				G_DEFAULT => 'comments.replyCommentRow'
			)
		);

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