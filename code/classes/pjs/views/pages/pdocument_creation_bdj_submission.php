<?php
class pDocument_Creation_BDJ_Submission extends pBase_Page_View {
	function __construct($pData) {
		parent::__construct($pData);
		$this->m_Templs = array(
			G_DEFAULT => 'global.submissionpage',
		);

		$this->m_objectsMetadata['create_document_errors'] = array(

			'templs'=>array(
				G_ROWTEMPL => 'create_document.error_row',
			),

		);

		$this->m_objectsMetadata['success_msg'] = array(

			'templs'=>array(
				G_DEFAULT => 'create_document.success_msg',
			),

		);


		$this->m_objectsMetadata['form_step_1_pwt'] = array(

			'templs'=>array(
				G_FORM_TEMPLATE => 'create_document.zookeys_submission_form_step_1_pwt',
				G_FORM_CHECKBOX_ROW => 'form.checkbox_input_row_label_for',
			),

		);

		$this->m_objectsMetadata['form_step_2_pwt'] = array(

			'templs'=>array(
				G_FORM_TEMPLATE => 'create_document.zookeys_submission_form_step_2_pwt',
			),

		);
		
		$this->m_objectsMetadata['form_step_3_pwt'] = array(

			'templs'=>array(
				G_FORM_TEMPLATE => 'create_document.zookeys_submission_form_step_3_pwt',
			),

		);
		
		$this->m_objectsMetadata['form_step_4_pwt'] = array(

			'templs'=>array(
				G_FORM_TEMPLATE => 'create_document.zookeys_submission_form_step_4_pwt',
				G_FORM_RADIO_ROW => 'form.radio_input_row_label_for_with_title',
			),

		);
		
		$this->m_objectsMetadata['form_step_5_pwt'] = array(

			'templs'=>array(
				G_FORM_TEMPLATE => 'create_document.zookeys_submission_form_step_5_pwt',
			),

		);
		
		$this->m_objectsMetadata['form_step_3_pwt_document_data'] = array(

			'templs' => array(
				G_DEFAULT => 'create_document.form_step_3_pwt_document_data',
			),

		);
		
		$this->m_objectsMetadata['submission_step_title'] = array(

			'templs' => array(
				G_DEFAULT => 'create_document.submission_step_title',
			),

		);
		
		$this->m_objectsMetadata['document_reviewers'] = array(
			'templs' => array(
				G_HEADER => 'create_document.submission_document_reviewers_header',
				G_STARTRS => 'create_document.submission_document_reviewers_startrs',
				G_ROWTEMPL => 'create_document.submission_document_reviewers_row',
				G_ENDRS => 'create_document.submission_document_reviewers_endrs',
				G_FOOTER => 'create_document.submission_document_reviewers_footer',
				G_NODATA => 'create_document.submission_document_reviewers_empty',

			)
		);
	}
}

?>