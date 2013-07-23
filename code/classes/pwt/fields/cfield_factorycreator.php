<?php
/**
 * Този клас ще създава реалния field, който ще реализира показването на field-a.
 * Тук ползваме factory design pattern.
 * @author peterg
 *
 */
class cfield_factorycreator {
	/**
	 *
	 * Enter description here ...
	 */
	function createField($pFieldDetails) {
		switch($pFieldDetails['html_control_type']){
			default:
			case (int)FIELD_HTML_INPUT_TYPE:
			case (int)FIELD_HTML_VIDEO_YOUTUBE_LINK_TYPE:
				return new cfield_input($pFieldDetails);
			case (int)FIELD_HTML_TEXTAREA_TYPE:
			case (int)FIELD_HTML_TEXTAREA_THESIS_TYPE:
			case (int)FIELD_HTML_TEXTAREA_ANTITHESIS_TYPE:
			case (int)FIELD_HTML_TEXTAREA_THESIS_NEXT_COUPLET_TYPE:
			case (int)FIELD_HTML_TEXTAREA_THESIS_TAXON_NAME_TYPE:
			case (int)FIELD_HTML_TEXTAREA_TABLE:
				return new cfield_textarea($pFieldDetails);
			case (int)FIELD_HTML_EDITOR_TYPE:
			case (int)FIELD_HTML_EDITOR_TYPE_ONLY_REFERENCE_CITATIONS:
					return new cfield_editor($pFieldDetails);

			case (int)FIELD_HTML_EDITOR_TYPE_NO_CITATIONS:
					return new cfield_editor_no_citations($pFieldDetails);
			case (int)FIELD_HTML_TEXTAREA_SIMPLE_TYPE:
			case (int)FIELD_HTML_ROUNDED_SIMPLE_TEXTAREA:
			case (int)FIELD_HTML_TEXTAREA_PLATE_DESCRIPTION_TYPE:
				return new cfield_textarea_simple($pFieldDetails);
			case (int)FIELD_HTML_SELECT_TYPE:
			case (int)FIELD_HTML_MULTIPLE_SELECT_TYPE:
				return new cfield_select($pFieldDetails);
			case (int)FIELD_HTML_RADIO_TYPE:
			case (int)FIELD_HTML_CHECKBOX_TYPE:
			case (int)FIELD_HTML_RADIO_PLATE_APPEARANCE_TYPE:				
				return new cfield_radio($pFieldDetails);

			case (int)FIELD_HTML_AUTOCOMPLETE_TYPE:
				return new cfield_autocomplete($pFieldDetails);
			case (int)FIELD_HTML_FACEBOOK_AUTOCOMPLETE_TYPE:
				return new cfield_facebookautocomplete($pFieldDetails);
			case (int) FIELD_HTML_TAXON_CLASSIFICATION_AUTOCOMPLETE_TYPE :
			case (int) FIELD_HTML_TAXON_CLASSIFICATION_AUTOCOMPLETE_SINGLE_TYPE:
			case (int) FIELD_HTML_SUBJECT_CLASSIFICATION_AUTOCOMPLETE_TYPE :
			case (int) FIELD_HTML_SUBJECT_CLASSIFICATION_AUTOCOMPLETE_SINGLE_TYPE :
			case (int) FIELD_HTML_CHRONOLOGICAL_CLASSIFICATION_AUTOCOMPLETE_TYPE :
			case (int) FIELD_HTML_CHRONOLOGICAL_CLASSIFICATION_AUTOCOMPLETE_SINGLE_TYPE :
			case (int) FIELD_HTML_GEOGRAPHICAL_CLASSIFICATION_AUTOCOMPLETE_TYPE :
			case (int) FIELD_HTML_GEOGRAPHICAL_CLASSIFICATION_AUTOCOMPLETE_SINGLE_TYPE:
			case (int) FIELD_HTML_TAXON_TREATMENT_CLASSIFICATION:
				return new cfield_taxon_classification_autocomplete($pFieldDetails);
			case (int)FIELD_HTML_FILE_UPLOAD_TYPE:
			case (int)FIELD_HTML_FILE_UPLOAD_MATERIAL_TYPE:
			case (int)FIELD_HTML_FILE_UPLOAD_CHECKLIST_TAXON_TYPE:
			case (int)FIELD_HTML_FILE_UPLOAD_TAXONOMIC_COVERAGE_TAXA_TYPE:
			case (int)FIELD_HTML_FILE_UPLOAD_FIGURE_IMAGE:
			case (int)FIELD_HTML_FILE_UPLOAD_FIGURE_PLATE_IMAGE:
				return new cfield_file_upload($pFieldDetails);

		}
	}
}


?>