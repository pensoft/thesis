<?php

class pView_Version_Pwt extends pView_Version {
	function __construct($pData) {
		parent::__construct($pData);
		$this->m_Templs = array(
			G_DEFAULT => 'global.version_page'
		);



		$this->m_objectsMetadata['errors'] = array(
			'templs' => array(
				G_ROWTEMPL => 'view_version_pwt.error_row'
			)
		);
		$this->m_objectsMetadata['no_premissions'] = array(
			'templs' => array(
				G_DEFAULT => 'view_version_pwt.no_premissions'
			)
		);

		$this->m_objectsMetadata['preview'] = array(
			'templs' => array(
				G_DEFAULT => 'view_version_pwt.version_preview'
			)
		);

		$this->m_objectsMetadata['user_legend'] = array(
			'templs' => array(
				G_HEADER => 'view_version_pwt.legend_head',
				G_FOOTER => 'view_version_pwt.legend_foot',
				G_ROWTEMPL => 'view_version_pwt.legend_row',
			)
		);
		$this->m_objectsMetadata['reviewerpoll'] = array(
			'templs' => array(
				G_HEADER => 'view_version_pwt.poll_head',
				G_FOOTER => 'view_version_pwt.poll_foot',
				G_ROWTEMPL => 'view_version_pwt.poll_row',
				G_STARTRS => 'view_version_pwt.poll_startrs',
				G_ENDRS => 'view_version_pwt.poll_endrs',
			)
		);

		$this->m_objectsMetadata['semode'] = array(
			'templs' => array(
				G_FORM_TEMPLATE => 'view_version_pwt.form_se',
				G_FORM_RADIO_ROW => 'form.radio_input_row_td',
				G_FORM_CHECKBOX_ROW => 'form.checkbox_input_row_no_br'
			)
		);

		$this->m_objectsMetadata['cemode'] = array(
			'templs' => array(
				G_FORM_TEMPLATE => 'view_version_pwt.form_ce',
				G_FORM_RADIO_ROW => 'form.radio_input_row_td',
				G_FORM_CHECKBOX_ROW => 'form.checkbox_input_row_no_br'
			)
		);

		$this->m_objectsMetadata['reviewermode'] = array(
			'templs' => array(
				G_FORM_TEMPLATE => 'view_version_pwt.form_reviewer',
				G_FORM_RADIO_ROW => 'form.radio_input_row_td',
				G_FORM_CHECKBOX_ROW => 'form.checkbox_input_row_no_br'
			)
		);

		$this->m_objectsMetadata['document_structure'] = array(
			'templs' => array(
				G_HEADER => 'view_version_pwt.structure_head',
				G_FOOTER => 'view_version_pwt.structure_foot',
				G_ROWTEMPL => 'view_version_pwt.structure_row',
				G_STARTRS => 'view_version_pwt.structure_start',
				G_ENDRS => 'view_version_pwt.structure_end',
			)
		);

		$this->m_objectsMetadata['comment_reply_form'] = array(
			'templs' => array(
				G_FORM_TEMPLATE => 'comments.reply_form',
			)
		);

		$this->m_objectsMetadata['new_comment_form'] = array(
			'templs' => array(
				G_FORM_TEMPLATE => 'comments.new_comment_form',
			)
		);

		$this->m_objectsMetadata['comments'] = array(
			'templs' => array(
				G_HEADER => 'comments.browseHead',
				G_STARTRS => 'comments.browseStart',
				G_SPLITHEADER => 'comments.browseSplitHead',
				G_ROWTEMPL => 'comments.browseRow',
				G_SPLITFOOTER => 'comments.browseSplitFoot',
				G_ENDRS => 'comments.browseEnd',
				G_FOOTER => 'comments.browseFoot',
				G_NODATA => 'comments.browseNoData',
			)
		);


// 		var_dump($pData['user_legend']->Display());
	}
}

?>