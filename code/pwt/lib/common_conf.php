<?php
define('PATH_XSL', PATH_CLASSES . 'pwt_xsl/');
define('PATH_OBJECTS_XSL', PATH_CLASSES . 'objects_xml/');
define('PATH_PMT_XSL', PATH_CLASSES . 'xsl/');
define('PATH_PJS_DOCROOT', PATH_CHECKOUT . '/code/pjs/');

define('PATH_PHP_EXCEL', PATH_CLASSES . 'excel_reader/');

define('PATH_PWT_UPLOADED_FILES', PATH_ITEMS_COMMON . 'materials/');

define('MAX_ALLOWED_WRONG_LOGIN_ATTEMPTS', 5);
define('MIN_ALLOWED_PASSWORD_LENGTH', 6);

define('ERR_EMPTY_FIELD', getstr('kfor.emptyField'));
define('ERR_CAPTCHA_WRONG_CODE', getstr('form.errorWrongCaptchaCode'));

define('METADATA_OBJECT_ID', 14);
define('PLATE_WRAPPER_OBJECT_ID', 235);
define('METADATA_OBJECT_IDS_FOR_DIFFERENT_TEMPLATES', '14,152');
define('DATA_PAPER_RESOURCES_DATA_SET_OBJECT_ID', 141);
define('TITLE_AND_AUTHORS_OBJECT_ID', 9);
define('AUTHOR_OBJECT_ID', 8);
define('CONTRIBUTOR_OBJECT_ID', 12);
define('FIRST_INSTANCE_IDS_FOR_DIFFERENT_TEMPLATES', '9,153, 236');

define('CONTAINER_ITEM_FIELD_TYPE', 1);
define('CONTAINER_ITEM_OBJECT_TYPE', 2);
define('CONTAINER_ITEM_CUSTOM_HTML_TYPE', 3);
define('CONTAINER_ITEM_TABBED_ITEM_TYPE', 4);

define('HTML_DEFAULT_RADIO_SEPARATOR', '<br/>');
define('HTML_DEFAULT_CHECKBOX_SEPARATOR', '<br/>');
define('HTML_DEFAULT_AUTOCOMPLETE_TEMPLATE', '"<a>" + item.name + "</a>"');
define('HTML_DEFAULT_AUTOCOMPLETE_ONSELECT_FUNCTION', '
						$( "#{field_html_identifier}_autocomplete" ).val( \'\' );
						$( "#{field_html_identifier}" ).val( \'\' );
						return false;');

define('FIELD_HTML_INPUT_TYPE', 2);
define('FIELD_HTML_SELECT_TYPE', 1);
define('FIELD_HTML_EDITOR_TYPE', 3);
define('FIELD_HTML_TEXTAREA_TYPE', 5);
define('FIELD_HTML_TEXTAREA_THESIS_TYPE', 33);
define('FIELD_HTML_TEXTAREA_THESIS_NEXT_COUPLET_TYPE', 34);
define('FIELD_HTML_TEXTAREA_THESIS_TAXON_NAME_TYPE', 35);
define('FIELD_HTML_TEXTAREA_ANTITHESIS_TYPE', 36);
define('FIELD_HTML_EDITOR_TYPE_NO_CITATIONS', 37);
define('FIELD_HTML_EDITOR_TYPE_ONLY_REFERENCE_CITATIONS', 43);

define('FIELD_HTML_MULTIPLE_SELECT_TYPE', 6);
define('FIELD_HTML_RADIO_TYPE', 7);
define('FIELD_HTML_CHECKBOX_TYPE', 8);
define('FIELD_HTML_AUTOCOMPLETE_TYPE', 9);
define('FIELD_HTML_TEXTAREA_SIMPLE_TYPE', 18);
define('FIELD_HTML_FACEBOOK_AUTOCOMPLETE_TYPE', 19);
define('FIELD_HTML_FILE_UPLOAD_TYPE', 21);
define('FIELD_HTML_TAXON_CLASSIFICATION_AUTOCOMPLETE_TYPE', 22);
define('FIELD_HTML_TAXON_CLASSIFICATION_AUTOCOMPLETE_SINGLE_TYPE', 26);
define('FIELD_HTML_TAXON_TREATMENT_CLASSIFICATION', 45);
define('FIELD_HTML_SUBJECT_CLASSIFICATION_AUTOCOMPLETE_TYPE', 27);
define('FIELD_HTML_SUBJECT_CLASSIFICATION_AUTOCOMPLETE_SINGLE_TYPE', 28);
define('FIELD_HTML_CHRONOLOGICAL_CLASSIFICATION_AUTOCOMPLETE_TYPE', 30);
define('FIELD_HTML_CHRONOLOGICAL_CLASSIFICATION_AUTOCOMPLETE_SINGLE_TYPE', 29);
define('FIELD_HTML_GEOGRAPHICAL_CLASSIFICATION_AUTOCOMPLETE_TYPE', 32);
define('FIELD_HTML_GEOGRAPHICAL_CLASSIFICATION_AUTOCOMPLETE_SINGLE_TYPE', 31);
define('FIELD_HTML_ROUNDED_SIMPLE_TEXTAREA', 38);
define('FIELD_HTML_FILE_UPLOAD_MATERIAL_TYPE', 44);
define('FIELD_HTML_FILE_UPLOAD_CHECKLIST_TAXON_TYPE', 46);
define('FIELD_HTML_FILE_UPLOAD_TAXONOMIC_COVERAGE_TAXA_TYPE', 47);
define('FIELD_HTML_FILE_UPLOAD_FIGURE_IMAGE', 49);
define('FIELD_HTML_FILE_UPLOAD_FIGURE_PLATE_IMAGE', 48);
define('FIELD_HTML_RADIO_PLATE_APPEARANCE_TYPE', 50);
define('FIELD_HTML_TEXTAREA_PLATE_DESCRIPTION_TYPE', 51);
define('FIELD_HTML_VIDEO_YOUTUBE_LINK_TYPE', 52);
define('FIELD_HTML_TEXTAREA_TABLE', 53);


define('FIELD_HELP_LABEL_ICON_STYLE', 1);
define('FIELD_HELP_LABEL_DESCRIPTION_STYLE', 2);

define('ACTION_HTML_MOVE_UP_TYPE', 10);
define('ACTION_HTML_MOVE_DOWN_TYPE', 11);
define('ACTION_HTML_TOP_RED_BTN_TYPE', 12);
define('ACTION_HTML_ADD_BTN_TYPE', 13);
define('ACTION_HTML_BOTTOM_EDIT_TYPE', 14);
define('ACTION_HTML_ADD_ALL_TYPE', 15);
define('ACTION_HTML_COMMENT_TYPE', 16);
define('ACTION_HTML_VALIDATION_TYPE', 17);
define('ACTION_HTML_CHECK_NAME_AVAILABILITY_TYPE', 20);
define('ACTION_HTML_BOTTOM_SAVE_BTN_TYPE', 23);
define('ACTION_HTML_BOTTOM_CANCEL_BTN_TYPE', 24);
define('ACTION_HTML_BOTTTOM_RED_BTN_TYPE', 25);
define('ACTION_HTML_TOP_CHANGE_MODE_TYPE', 39);
define('ACTION_HTML_RIGHT_MOVE_UP_TYPE', 40);
define('ACTION_HTML_RIGHT_MOVE_DOWN_TYPE', 41);
define('ACTION_HTML_RIGHT_DELETE_TYPE', 42);

define('ACTION_TOP_POS', 1);
define('ACTION_BOTTOM_POS', 2);
define('ACTION_AFTER_SAVE_POS', 3);
define('ACTION_RIGHT_POS', 7);

define('ACTIONS_REMOVE_ID', 3);
define('ACTIONS_ADD_NEW_INSTANCE_ID', 4);
define('ACTIONS_MOVE_UP_ID', 1);
define('ACTIONS_MOVE_DOWN_ID', 2);

define('FIELD_INT_TYPE', 1);
define('FIELD_STRING_TYPE', 2);

define('FIELD_CHECKBOX_MANY_TO_STRING_TYPE', 3);
define('FIELD_CHECKBOX_MANY_TO_BIT_TYPE', 4);
define('FIELD_CHECKBOX_MANY_TO_BIT_ONE_BOX_TYPE', 5);
define('FIELD_CHECKBOX_MANY_TO_SQL_INT_ARRAY_TYPE', 6);
define('FIELD_CHECKBOX_MANY_TO_SQL_STRING_ARRAY_TYPE', 7);
define('FIELD_CHECKBOX_MANY_TO_SQL_DATE_ARRAY_TYPE', 9);
define('FIELD_DATE_TYPE', 8);

define('ITEMS_STRING_REPRESENTATION_DELIMITER', ',');

define('COMMENTS_FIX_TYPE_START_POS', 1);
define('COMMENTS_FIX_TYPE_END_POS', 2);
define('COMMENT_START_NODE_NAME', 'comment-start');
define('COMMENT_END_NODE_NAME', 'comment-end');
define('COMMENT_ID_ATTRIBUTE_NAME', 'comment-id');
define('COMMENT_START_POS_TYPE', 1);
define('COMMENT_END_POS_TYPE', 2);
define('COMMENT_POS_MARKER_FAKE_TEXT', '#######COMMENT$$$$$$');

define('FAKE_ROOT_NODE_NAME', 'root');
define('CHANGE_INSERT_TYPE', 1);
define('CHANGE_DELETE_TYPE', 2);
define('CHANGE_ID_ATTRIBUTE_NAME', 'data-cid');
define('CHANGE_USER_ID_ATTRIBUTE_NAME', 'data-userid');
define('CHANGE_USER_NAME_ATTRIBUTE_NAME', 'data-username');
define('CHANGE_TITLE_ATTRIBUTE_NAME', 'title');
define('CHANGE_IS_ACCEPTED_ATTRIBUTE_NAME', 'is-accepted');
define('CHANGE_INSERT_NODE_NAME', 'insert');
define('CHANGE_DELETE_NODE_NAME', 'delete');
define('CHANGE_ACCEPTED_INSERT_NODE_NAME', 'accepted-insert');
define('CHANGE_ACCEPTED_DELETE_NODE_NAME', 'accepted-delete');

define('DIFF_CHAR_BASED_TYPE', 1);
define('DIFF_WORD_BASED_TYPE', 2);
define('DIFF_TYPE', (int)DIFF_CHAR_BASED_TYPE);



define('INSTANCE_FIELD_NAME_SEPARATOR', '__');
define('INSTANCE_EDIT_MODE', 1);
define('INSTANCE_VIEW_MODE', 2);
define('INSTANCE_TITLE_MODE', 3);
define('INSTANCE_DEFAULT_MODE', INSTANCE_EDIT_MODE);

define('CONTAINER_VERTICAL_TYPE', 1);
define('CONTAINER_HORIZONTAL_TYPE', 2);

//Lock-operations
define('LOCK_AUTO_LOCK', 1);
define('LOCK_AUTO_UNLOCK', 2);
define('LOCK_EXPLICIT_UNLOCK', 3);
define('LOCK_AUTO_EXPLICIT_UNLOCK', 4);

define('USE_PERL_EXECS', 0);
define(ACTION_AJAX_URL, AJAX_URL . '/lib/ajax_srv/action_srv.php');

define('DEFAULT_XML_ENCODING', 'utf-8');
//~ define('FCK_CUSTOMTOOLSNAME_TOOLS', 3);

// Binaries
//~ define('BINARY_FFMPEG', exec('which ffmpeg'));
define('BINARY_CONVERT', exec('which convert'));

// JS_VALIDATION_ON
define('JS_VALIDATION_ON', 1);
define('DEFAULT_ERROR_STRING', getstr('kfor.default_error'));
define('DEF_AJAXCHECK_URL', '/lib/ajaxFormValidate.php');

define('TAXON_BALOON_SRV', PTP_URL . '/getTaxonLinks.php');
define('TAXON_EXTERNAL_LINK_BASE_LINK', PTP_URL . '/externalLink.php');
define('TAXON_NAME_BASE_LINK', PTP_URL . '/external_details.php');
define('TAXON_NAME_LINK', TAXON_NAME_BASE_LINK . '?type=1&amp;query=');

define('SHOWFIGURE_URL', '/showfigure.php?filename=');

define('SERIALIZE_INTERNAL_MODE', 1);
define('SERIALIZE_INPUT_MODE', 2);

define('EDITOR_FULL_TOOLBAR_NAME', 'FullToolbar');
define('EDITOR_FULL_TOOLBAR_NAME_NO_MAXIMIZE', 'FullToolbarNoMaximize');
define('EDITOR_SMALL_TOOLBAR_NAME', 'SmallToolbar');
define('EDITOR_MODERATE_TOOLBAR_NAME', 'ModerateToolbar');
define('EDITOR_MODERATE_TABLE_TOOLBAR_NAME', 'ModerateTableToolbar');
define('EDITOR_REFERENCE_CITATION_TOOLBAR_NAME', 'ReferenceCitationToolbar');

define('EDITOR_FULL_TOOLBAR_NAME_EMPTY', 'FullToolbarEmpty');
define('EDITOR_FULL_TOOLBAR_NAME_NO_MAXIMIZE_EMPTY', 'FullToolbarNoMaximizeEmpty');
define('EDITOR_SMALL_TOOLBAR_NAME_EMPTY', 'SmallToolbarEmpty');
define('EDITOR_MODERATE_TOOLBAR_NAME_EMPTY', 'ModerateToolbarEmpty');
define('EDITOR_MODERATE_TABLE_TOOLBAR_NAME_EMPTY', 'ModerateTableToolbarEmpty');
define('EDITOR_REFERENCE_CITATION_TOOLBAR_NAME_EMPTY', 'ReferenceCitationToolbarEmpty');
define('EDITOR_BASIC_TOOLS_NAME_EMPTY', 'BasicStylesToolbarEmpty');

define('EDITOR_DEFAULT_HEIGHT', 120);
define('EDITOR_SMALL_DEFAULT_HEIGHT', 75);
define('EDITOR_THESIS_HEIGHT', 70);
define('EDITOR_NEXT_COUPLET_HEIGHT', 42);

//Link от където се взима информация за статия с дадено ид в пмц
define('PMC_FETCH_LINK', 'http://eutils.ncbi.nlm.nih.gov/entrez/eutils/efetch.fcgi?db=pmc&rettype=medline&retmode=xml&id=');
define('PUBMED_FETCH_LINK', 'http://eutils.ncbi.nlm.nih.gov/entrez/eutils/efetch.fcgi?db=pubmed&rettype=medline&retmode=xml&id=');
define('CROSSREF_PID', 'preprint@pensoft.net');
define('CROSSREF_FETCH_LINK', 'http://www.crossref.org/openurl/?noredirect=true&pid=' . CROSSREF_PID . '&format=unixref&id=doi:');

define('REFERENCE_OBJECT_ID', 95);
define('SUP_FILE_OBJECT_ID', 55);
define('FIGURE_OBJECT_ID', 221);
define('TABLE_OBJECT_ID', 238);
define('SUP_FILE_HOLDER_OBJECT_ID', 56);
define('FIGURE_HOLDER_OBJECT_ID', 236);
define('TABLE_HOLDER_OBJECT_ID', 237);
define('TAXON_EXTERNAL_LINK_OBJECT_ID', 39);
define('REFERENCE_HOLDER_OBJECT_ID', 21);

define('PLATE_PART_TYPE_A_OBJECT_ID', 225);
define('PLATE_PART_TYPE_B_OBJECT_ID', 226);
define('PLATE_PART_TYPE_C_OBJECT_ID', 227);
define('PLATE_PART_TYPE_D_OBJECT_ID', 228);
define('PLATE_PART_TYPE_E_OBJECT_ID', 229);
define('PLATE_PART_TYPE_F_OBJECT_ID', 230);

// Auto Save interval in milliseconds
define('AUTO_SAVE_INTERVAL', 1000 * 60); // 1 minute

define('DOCUMENT_TITLE_FIELD_ID', 3);
define('AUTHOR_MAIL_NOTIFICATION_FIELD_ID', 288);
define('FILE_UPLOAD_FIELD_ID', 222);
define('MATERIAL_TYPE_FIELD_ID', 209);
define('DOCUMENT_VIEW_LINK', SITE_URL . '/display_document.php?document_id=');

// тип на действие (dashboard -> activity)
define('ACTION_SAVE_DOCUMENT', 1);

define('ACTIVITY_RECORDS_PER_PAGE', 10);

// SORTABLE OBJECTS IN MENU
define('SYSTEMATICS_OBJECT_ID', 54);
define('IDENTIFICATION_KEYS_OBJECT_ID', 24);
define('ADD_TAXON_ACTION_ID', 41);
define('ADD_IDENTIFICATION_KEY_ID', 23);
define('CHECKLIST_OBJECT_ID', 173);
define('ADD_CHECKLIST_TAXON_OBJECT_ID', 174);
define('CHECKLIST_LOCALITY_OBJECT_ID', 129);
define('ADD_CHECKLIST_LOCALITY_OBJECT_ID', 183);
define('CHECKLIST_LOCALITY_TAXON_OBJECT_ID', 188);

define('TAXON_TREATMENT_OBJECT_ID', 41);
define('SUPPLEMENTARY_FILE_OBJECT_ID', 55);
define('SUPPLEMENTARY_FILES_OBJECT_ID', 56);
define('CHECKLISTS_OBJECT_ID', 203);
define('CHECKLIST2_OBJECT_ID', 204);
define('TAXON2_OBJECT_ID', 205);

define('INVENTORY_CHECKLIST_ID', 211);
define('INVENTORY_LOCALITY_ID', 212);

// Изпращане на мейли / съобщения
define('PENSOFT_SITE_URL', 'http://pwt.pensoft.net');
define('PENSOFT_MAIL_ADDR', 'pwt@pensoft.net');
define('PENSOFT_MAIL_DISPLAY', 'pwt@pensoft.net');
define('PENSOFT_MAILSUBJ_REGISTER', '[PENSOFT WRITING TOOL] Verify your account');
define('PENSOFT_MAILSUBJ_FPASS', '[PENSOFT WRITING TOOL] Forgotten Password');
define('PENSOFT_MAILSUBJ_DOC_NEW_AUTHOR_REGISTER', '[PENSOFT WRITING TOOL] New User Registration');
define('PENSOFT_MAILSUBJ_DOC_NEW_AUTHOR', '[PENSOFT WRITING TOOL] Invitation to co-author a manuscript');
define('PENSOFT_MAILSUBJ_DOC_NEW_CONTRIBUTOR', '[PENSOFT WRITING TOOL] Invitation to contribute to a manuscript');


define('CUSTOM_CHECK_VALIDATION_MODE', 1);
define('CUSTOM_CHECK_AFTER_SAVE_MODE', 2);


define('CUSTOM_CHECK_BREAKABLE_ERROR_TYPE', 2);
define('CUSTOM_CHECK_NORMAL_ERROR_TYPE', 1);
define('CUSTOM_CHECK_WARNING_TYPE', 3);

define('PENSOFT_LOGO_IMG', '/i/pwt_logo_beta.png');

define('TAXON_NOMENCLATURE_TABLE_NAME', 'taxon_categories');

define('AUTHOR_RIGHT_EDIT', 1);
define('AUTHOR_RIGHT_COMMENT', 2);

//Интервала, през който да се изпраща сигнала за подновяване на lock-а на документите (в сек).
//Документа се счита за отключен, ако не е заключен в последните 2 пъти по-това секунди
define('DOCUMENT_LOCK_TIMEOUT_INTERVAL', 15);

define('DOCUMENT_AUTHOR_TYPE_ID', 2);
define('DOCUMENT_CONTRIBUTOR_TYPE_ID', 4);

define('YOU_TUBE_VIDEO_TYPE', 2);

define('DEFAULT_DOCUMENT_PAPER_TYPE', 2);
define('DEFAULT_TT_HABITAT_TYPE_ID', 1);
define('DEFAULT_TT_STATUS_TYPE_ID', 1);
define('REFERENCE_TYPES_SRC_ID', 26);
define('REFERENCE_CUSTOM_CREATION_ID', 7);
define('TT_MATERIAL_CUSTOM_CREATION_ID', 6);
define('TT_CUSTOM_CREATION_ID', 10);
define('TT_STATUS_TYPES_SRC_ID', 13);
define('TT_TYPES_SRC_ID', 12);
define('TT_RANK_SRC_ID', 11);
define('TT_CLASSIFICATION_SRC_ID', 11);
define('TT_HABITAT_TYPES_SRC_ID', 14);

define('CITATION_FIGURE_PLATE_TYPE_ID', 1);
define('CITATION_TABLE_TYPE_ID', 2);
define('CITATION_REFERENCE_TYPE_ID', 3);

define('PWT_MAX_ALLOWED_UPLOAD_FILE_MATERIAL_COUNT', 200);

define('VERSION_USR_CSS_PATH', PATH_PJS_DOCROOT . 'lib/version_usr.css');
define('PREVIEW_EDITABLE_HEADER_REPLACEMENT_TEXT', '$$EditPreviewHead$$');

define('VERSION_INSERT_CHANGE_NODE_NAME', 'insert');
define('VERSION_DELETE_CHANGE_NODE_NAME', 'delete');

///// FRAMEWORK CONSTANTS DEFINITIONS
define('G_DEFAULT', 'G_DEFAULT');
define('G_EMPTY', 'G_EMPTY');
define('G_ENDRS', 'G_ENDRS');
define('G_FOOTER', 'G_FOOTER');
define('G_FORM_ACTION_DEFAULT_ROW', 'G_FORM_ACTION_DEFAULT_ROW');
define('G_FORM_ACTION_IMAGE_ROW', 'G_FORM_ACTION_IMAGE_ROW');
define('G_FORM_ACTION_LINK_ROW', 'G_FORM_ACTION_LINK_ROW');
define('G_FORM_CALENDAR_ROW', 'G_FORM_CALENDAR_ROW');
define('G_FORM_CAPTCHA_ROW', 'G_FORM_CAPTCHA_ROW');
define('G_FORM_CHECKBOX_END', 'G_FORM_CHECKBOX_END');
define('G_FORM_CHECKBOX_ROW', 'G_FORM_CHECKBOX_ROW');
define('G_FORM_CHECKBOX_START', 'G_FORM_CHECKBOX_START');
define('G_FORM_FIELD_ERROR_FOOTER', 'G_FORM_FIELD_ERROR_FOOTER');
define('G_FORM_FIELD_ERROR_HEADER', 'G_FORM_FIELD_ERROR_HEADER');
define('G_FORM_FIELD_ERROR_ROW', 'G_FORM_FIELD_ERROR_ROW');
define('G_FORM_FILE_INPUT_ROW', 'G_FORM_FILE_INPUT_ROW');
define('G_FORM_FOOTER', 'G_FORM_FOOTER');
define('G_FORM_GLOBAL_ERROR_ROW', 'G_FORM_GLOBAL_ERROR_ROW');
define('G_FORM_HEADER', 'G_FORM_HEADER');
define('G_FORM_HIDDEN_INPUT_ROW', 'G_FORM_HIDDEN_INPUT_ROW');
define('G_FORM_JS_ONLY', 'G_FORM_JS_ONLY');
define('G_FORM_JS_VALIDATION', 'G_FORM_JS_VALIDATION');
define('G_FORM_MSELECT_END', 'G_FORM_MSELECT_END');
define('G_FORM_MSELECT_ROW', 'G_FORM_MSELECT_ROW');
define('G_FORM_MSELECT_START', 'G_FORM_MSELECT_START');
define('G_FORM_MULTILANGUAGE_FIELD_FOOT', 'G_FORM_MULTILANGUAGE_FIELD_FOOT');
define('G_FORM_MULTILANGUAGE_FIELD_HEAD', 'G_FORM_MULTILANGUAGE_FIELD_HEAD');
define('G_FORM_MULTILANGUAGE_FIELD_ROW_FOOT', 'G_FORM_MULTILANGUAGE_FIELD_ROW_FOOT');
define('G_FORM_MULTILANGUAGE_FIELD_ROW_HEAD', 'G_FORM_MULTILANGUAGE_FIELD_ROW_HEAD');
define('G_FORM_MULTILANGUAGE_INPUT_ROW', 'G_FORM_MULTILANGUAGE_INPUT_ROW');
define('G_FORM_MULTILANGUAGE_RICHTEXT_EDITOR_ROW', 'G_FORM_MULTILANGUAGE_RICHTEXT_EDITOR_ROW');
define('G_FORM_PASSWORD_INPUT_ROW', 'G_FORM_PASSWORD_INPUT_ROW');
define('G_FORM_RADIO_END', 'G_FORM_RADIO_END');
define('G_FORM_RADIO_ROW', 'G_FORM_RADIO_ROW');
define('G_FORM_RADIO_START', 'G_FORM_RADIO_START');
define('G_FORM_RICHTEXT_EDITOR_ROW', 'G_FORM_RICHTEXT_EDITOR_ROW');
define('G_FORM_SELECT_END', 'G_FORM_SELECT_END');
define('G_FORM_SELECT_ROW', 'G_FORM_SELECT_ROW');
define('G_FORM_SELECT_START', 'G_FORM_SELECT_START');
define('G_FORM_TEXT_INPUT_ROW', 'G_FORM_TEXT_INPUT_ROW');
define('G_FORM_TEXTAREA_ROW', 'G_FORM_TEXTAREA_ROW');
define('G_HEADER', 'G_HEADER');
define('G_NODATA', 'G_NODATA');
define('G_PAGEING', 'G_PAGEING');
define('G_PAGEING_ACTIVEFIRST', 'G_PAGEING_ACTIVEFIRST');
define('G_PAGEING_ACTIVELAST', 'G_PAGEING_ACTIVELAST');
define('G_PAGEING_ACTIVEPAGE', 'G_PAGEING_ACTIVEPAGE');
define('G_PAGEING_DELIMETER', 'G_PAGEING_DELIMETER');
define('G_PAGEING_ENDRS', 'G_PAGEING_ENDRS');
define('G_PAGEING_INACTIVEFIRST', 'G_PAGEING_INACTIVEFIRST');
define('G_PAGEING_INACTIVELAST', 'G_PAGEING_INACTIVELAST');
define('G_PAGEING_INACTIVEPAGE', 'G_PAGEING_INACTIVEPAGE');
define('G_PAGEING_PGEND', 'G_PAGEING_PGEND');
define('G_PAGEING_PGSTART', 'G_PAGEING_PGSTART');
define('G_PAGEING_STARTRS', 'G_PAGEING_STARTRS');
define('G_ROWTEMPL', 'G_ROWTEMPL');
define('G_STARTRS', 'G_STARTRS');
define('port', 'port');
define('REWRITE_DOUBLE_QUOTES_REPLACEMENT', 'REWRITE_DOUBLE_QUOTES_REPLACEMENT');
define('REWRITE_SINGLE_QUOTES_REPLACEMENT', 'REWRITE_SINGLE_QUOTES_REPLACEMENT');
define('REWRITE_SPACE_REPLACEMENT', 'REWRITE_SPACE_REPLACEMENT');
define('REWRITE_UNDERSCORE_REPLACEMENT', 'REWRITE_UNDERSCORE_REPLACEMENT');
define('G_BIGPHOTO', 'G_BIGPHOTO');
define('G_GALLERY', 'G_GALLERY');
define('G_GALNAV', 'G_GALNAV');
define('G_GALNEXT', 'G_GALNEXT');
define('G_GALPHOTO', 'G_GALPHOTO');
define('G_GALPREV', 'G_GALPREV');
define('G_KEYFOOTER', 'G_KEYFOOTER');
define('G_KEYHEADER', 'G_KEYHEADER');
define('G_KEYROW', 'G_KEYROW');
define('G_NOSTORY', 'G_NOSTORY');
define('G_PHOTO', 'G_PHOTO');
define('G_RELGAL', 'G_RELGAL');
define('G_RELINKFOOTER', 'G_RELINKFOOTER');
define('G_RELINKHEADER', 'G_RELINKHEADER');
define('G_RELINKROW', 'G_RELINKROW');
define('G_RELMEDIA_FOOTER', 'G_RELMEDIA_FOOTER');
define('G_RELMEDIA_HEADER', 'G_RELMEDIA_HEADER');
define('G_RELSTFOOTER', 'G_RELSTFOOTER');
define('G_RELSTHEADER', 'G_RELSTHEADER');
define('G_RELSTROW', 'G_RELSTROW');
define('G_RESTRICTED', 'G_RESTRICTED');
define('G_RGALPHOTO', 'G_RGALPHOTO');
define('G_STORY_ATTACHMENTS', 'G_STORY_ATTACHMENTS');
define('G_STORY_ATTACHMENTSMP3', 'G_STORY_ATTACHMENTSMP3');
define('G_STORY_ATTACHMENTS_FOOTER', 'G_STORY_ATTACHMENTS_FOOTER');
define('G_STORY_ATTACHMENTS_HEADER', 'G_STORY_ATTACHMENTS_HEADER');
define('ENT_XHTML','ENT_XHTML');
define('G_FORM_TEMPLATE','G_FORM_TEMPLATE');
///// END FRAMEWORK CONSTANTS DEFINITIONS

define('NEW_DOCUMENT_STATE', 1);
define('IN_PRE_SUBMIT_REVIEW_DOCUMENT_STATE', 5);
define('READY_TO_SUBMIT_DOCUMENT_STATE', 6);
define('RETURNED_FROM_PJS_DOCUMENT_STATE', 3);

define('CHECKLIST_TAXON_TAXA_SPREADSHEET_NAME', 'Taxa');
define('CHECKLIST_TAXON_MATERIALS_SPREADSHEET_NAME', 'Materials');
define('CHECKLIST_TAXON_EXT_LINKS_SPREADSHEET_NAME', 'ExternalLinks');

define('CHECKLIST_TAXON_TAXA_SPREADSHEET_DEFAULT_IDX', 0);
define('CHECKLIST_TAXON_MATERIALS_SPREADSHEET_DEFAULT_IDX', 1);
define('CHECKLIST_TAXON_EXT_LINKS_SPREADSHEET_DEFAULT_IDX', 2);

define('CHECKLIST_TAXON_LOCAL_ID_FIELD_NAME', 'Taxon_Local_ID');
define('CHECKLIST_TAXON_RANK_FIELD_NAME', 'Rank');
define('CHECKLIST_TAXON_MATERIALS_KEY_NAME', 'Materials');
define('CHECKLIST_TAXON_EXTERNAL_LINKS_KEY_NAME', 'ExtLinks');
define('CHECKLIST_TAXON_MATERIAL_TYPE_STATUS_FIELD_NAME', 'TypeStatus');
define('CHECKLIST_TAXON_AUTHORSHIP_FIELD_NAME', 'Authorship');
define('CHECKLIST_TAXON_EXTERNAL_LINK_LINK_TYPE_FIELD_NAME', 'Link type');
define('CHECKLIST_TAXON_EXTERNAL_LINK_LINK_VALUE_FIELD_NAME', 'Link');
define('CHECKLIST_TAXON_HABITAT', 1);
define('CHECKLIST_TAXON_STATUS_TYPE', 1);
define('CHECKLIST_TAXON_OBJECT_ID', 205);

define('DATA_PAPER_TAXONOMIC_COVERAGE_TAXA_SPECIFIC_NAME_FIELD_NAME', 'Specific name');
define('DATA_PAPER_TAXONOMIC_COVERAGE_TAXA_RANK_FIELD_NAME', 'Rank');
define('DATA_PAPER_TAXONOMIC_COVERAGE_TAXA_OBJECT_ID', 191);

$gChecklistTaxonRankFields = array(
	'Kingdom',
	'Subkingdom',
	'Phylum',
	'Subphylum',
	'Superclass',
	'Class',
	'Subclass',
	'Superorder',
	'Order',
	'Suborder',
	'Infraorder',
	'Superfamily',
	'Family',
	'Subfamily',
	'Tribe',
	'Subtribe',
	'Genus',
	'Subgenus',
	'Species',
	'Subspecies',
	'Variety',
	'Form'
);

define('CHECKLIST_TAXON_NOMENCLATURE_FIELD_ID', 41);
define('CHECKLIST_SPECIES_LOCALITY_LOCALITY_OBJECT_ID', 212);
define('CHECKLIST_SPECIES_LOCALITY_LOCALITY_CHECKLIST_OBJECT_ID', 211);

define('DELETED_DOCUMENT_STATE', 4);
define('TAXON_MATERIALS_IMPORT_CONTAINER_ID', 463);

define('DARWINCORE_OBJECT_ID', 84);
define('USE_PREPARED_STATEMENTS', 0);
define('CITATION_ELEMENT_CITATION_WRAPPER_NODE_NAME', 'citation-elements');

define('OBJECTS_CACHED_XML_TYPE_ONLY_FIELDS', 1);
define('OBJECTS_CACHED_XML_TYPE_WHOLE_TREE', 2);

define('POSTGRESQL_DATESTYLE', 'SQL, DMY');

define('AUTHOR_READY_TO_SUBMIT_DOCUMENT_ACTION_TYPE', 1);
define('APPROVE_TO_SUBMIT_DOCUMENT_ACTION_TYPE', 2);
define('SUBMIT_DOCUMENT_ACTION_TYPE', 3);

// document submission emails
define('PENSOFT_MAIL_ADDR_DOCUMENT_SUBMISSION', 'preprint@pensoft.net');
define('PENSOFT_MAIL_DISPLAY_DOCUMENT_SUBMISSION', 'preprint@pensoft.net');
//define('PENSOFT_MAIL_ADDR_DOCUMENT_SUBMISSION', 'vic.penchev@gmail.com');
//define('PENSOFT_MAIL_DISPLAY_DOCUMENT_SUBMISSION', 'vic.penchev@gmail.com');
?>