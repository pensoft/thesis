<?php

/**
 * The view class for the browse journal issues page
 */
class pArticles_Ajax_Srv extends epPage_Json_View {
	function __construct($pData) {
		parent::__construct($pData);

		$this->m_objectsMetadata['related_list'] = array(
			'templs' => array(
				G_DEFAULT => 'articles.related'
			)
		);

		$this->m_objectsMetadata['metrics_list'] = array(
			'templs' => array(
				G_DEFAULT => 'articles.metrics',
			)
		);
		
		$this->m_objectsMetadata['metrics_figures_list'] = array(
			'templs' => array(
				G_HEADER => 'article.figures_metrics_head',
				G_FOOTER => 'article.figures_metrics_foot',
				G_STARTRS => 'article.figures_metrics_start',
				G_ENDRS => 'article.figures_metrics_end',
				G_NODATA => 'article.figures_metrics_nodata',
				G_ROWTEMPL => 'article.figures_metrics_row' 
			)
		);
		
		$this->m_objectsMetadata['metrics_tables_list'] = array(
			'templs' => array(
				G_HEADER => 'article.tables_metrics_head',
				G_FOOTER => 'article.tables_metrics_foot',
				G_STARTRS => 'article.tables_metrics_start',
				G_ENDRS => 'article.tables_metrics_end',
				G_NODATA => 'article.tables_metrics_nodata',
				G_ROWTEMPL => 'article.tables_metrics_row' 
			)
		);
		
		$this->m_objectsMetadata['metrics_suppl_files_list'] = array(
			'templs' => array(
				G_HEADER => 'article.suppl_files_metrics_head',
				G_FOOTER => 'article.suppl_files_metrics_foot',
				G_STARTRS => 'article.suppl_files_metrics_start',
				G_ENDRS => 'article.suppl_files_metrics_end',
				G_NODATA => 'article.suppl_files_metrics_nodata',
				G_ROWTEMPL => 'article.suppl_files_metrics_row' 
			)
		);
		
		$this->m_objectsMetadata['share_list'] = array(
			'templs' => array(
				G_DEFAULT => 'articles.share',
			)
		);
		
		$this->m_objectsMetadata['forum'] = array(
			'templs' => array(
				G_DEFAULT => 'articles.forum',
			)
		);
		
		$this->m_objectsMetadata['forum_list'] = array(
			'templs' => array(
				G_HEADER => 'article.forum_list_head',
				G_FOOTER => 'article.forum_list_foot',
				G_STARTRS => 'article.forum_list_start',
				G_ENDRS => 'article.forum_list_end',
				G_NODATA => 'article.forum_list_nodata',
				G_ROWTEMPL => 'article.forum_list_row' 
			)
		);
		
		$this->m_objectsMetadata['forum_no_logged_user'] = array(
			'templs' => array(
				G_DEFAULT => 'articles.forum_no_logged_user',
			)
		);
		
		$this->m_objectsMetadata['forum_show_comment_link'] = array(
			'templs' => array(
				G_DEFAULT => 'articles.forum_show_comment_link',
			)
		);
		
		$this->m_objectsMetadata['forum_list_only'] = array(
			'templs' => array(
				G_DEFAULT => 'articles.forum_list_only',
			)
		);
		
		$this->m_objectsMetadata['forum_wrapper'] = array(
			'templs' => array(
				G_DEFAULT => 'articles.forum_wrapper',
			)
		);
		
		
		
		$this->m_objectsMetadata['comments_form_templ'] = array(
			'templs' => array(
				G_FORM_TEMPLATE => 'article.comment_form',
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

		$this->m_defTempls[G_FORM_RADIO_ROW] = 'form.radio_aof_comment_poll_input_row';

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