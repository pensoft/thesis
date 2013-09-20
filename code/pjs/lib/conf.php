<?php
// Prazno - zaradi autoloada trqbva da e taka
define('SITE_NAME', 'pjs');
define('CMS_SITEID', 1);
define('DEF_LANGID', 2);
define('STORIES_DSCID', 1);

define('PATH_PJS_DOCROOT', PATH_CHECKOUT . '/code/pjs/');

define('SHOWIMG_URL', '/showimg.php?filename=');
define('GETATT_URL', '/getatt.php?filename=');
define('DOWNLOAD_SUPPLEMENTARY_FILE_URL', PWT_URL . 'getfile.php?filename=');

define('GET_PWT_DOCUMENT_XML_URL', PWT_URL . 'get_document_xml.php?document_id=');
define('PWT_PJS_IMPORT_URL', PWT_URL . 'lib/pjs_import.php');
define('PDF_PATH', PATH_ITEMS_COMMON . 'pdf/');

// DEFAULT EMPTY TEMPLATE
define('D_EMPTY', 'global.empty');

// Registration
define('MAIL_REG_SBJ', 'Регистрация');
define('MAIL_REG_DSPL', 'Etaligent.NET');
define('MAIL_REG_ADDR', 'register@' . $_SERVER['SERVER_NAME']);

// Служебна рубрика, стаиите от която, няма да се показват при броуз
define('HIDDEN_RUBRIDS', '(9)' );

// FCK editor
define('FCK_BASEPATH', '/lib/');
define('FCK_DEFAULT_TOOLBAR', 'AllTools');
define('FCK_DEFAULT_FILE', 'fckeditor.html');
define('FCK_DEFAULT_WIDTH', '100%');
define('FCK_DEFAULT_HEIGHT', '200');

define('MAIN_MENU_ID', 9);
define('AOF_MAIN_MENU_ID', 30);

define('FILES_DOCUMENT_TYPE', 2);
define('PWT_DOCUMENT_TYPE', 1);

define('USER_ACTIVE_STATE', 1);
define('USER_INACTIVE_STATE', 0);

define('DOCUMENT_INCOMPLETE_STATE', 1);
define('DOCUMENT_WAITING_SE_ASSIGNMENT_STATE', 2);
define('DOCUMENT_IN_REVIEW_STATE', 3);
define('DOCUMENT_IN_COPY_REVIEW_STATE', 8);
define('DOCUMENT_APPROVED_FOR_PUBLISH', 11);
define('DOCUMENT_WAITING_AUTHOR_VERSION_AFTER_REVIEW_STATE', 9);
define('DOCUMENT_WAITING_AUTHOR_VERSION_AFTER_LAYOUT_STATE', 10);
define('DOCUMENT_WAITING_AUTHOR_TO_PROCEED_TO_LAYOUT_EDITING_STATE', 12);
define('DOCUMENT_WAITING_AUTHOR_TO_PROCEED_TO_COPY_EDITING_STATE', 14);
define('DOCUMENT_IN_LAYOUT_EDITING_STATE', 4);
define('DOCUMENT_READY_FOR_LAYOUT_STATE', 13);
define('DOCUMENT_WAITING_AUTHOR_TO_PROCEED_TO_LAYOUT_EDITING_AFTER_COPY_EDITING_STATE', 17);

define('DOCUMENT_IN_LAYOUT_REVIEW_STATE', (int)DOCUMENT_IN_LAYOUT_EDITING_STATE);
define('DOCUMENT_PUBLISHED_STATE', 5);
define('DOCUMENT_REJECTED_STATE', 7);
define('DOCUMENT_ARCHIVED_STATE', 6);
define('DOCUMENT_READY_FOR_COPY_REVIEW_STATE', 15);
define('DOCUMENT_REVISIONS_AFTER_REVIEW_STATE', (int)DOCUMENT_WAITING_AUTHOR_VERSION_AFTER_REVIEW_STATE);
define('DOCUMENT_REVISIONS_AFTER_LAYOUT_STATE', (int)DOCUMENT_WAITING_AUTHOR_VERSION_AFTER_LAYOUT_STATE);
define('DOCUMENT_REJECTED_BUT_RESUBMISSION', 16);

define('VERSION_CHANGE_NEW_CHANGE_STATE_ID', 1);
define('VERSION_CHANGE_PROCESSED_STATE_ID', 2);
define('VERSION_CHANGE_ACCEPT_ALL_CHANGES_STATE_ID', 3);
define('VERSION_CHANGE_REJECT_ALL_CHANGES_STATE_ID', 4);
define('VERSION_USR_CSS_PATH', PATH_PJS_DOCROOT . 'lib/version_usr.css');

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
define('DIFF_TYPE', (int)DIFF_WORD_BASED_TYPE);

// SEARCHING VARIABLES
define('SEARCH_IN_ARTICLE', 1);
define('SEARCH_IN_ALL_ARTICLES', 2);


define('DASHBOARD_YOUR_TASKS_VIEWMODE', 5);
define('DASHBOARD_AUTHOR_INCOMPLETE_VIEWMODE', 1);
define('DASHBOARD_AUTHOR_PENDING_VIEWMODE', 2);
define('DASHBOARD_AUTHOR_PUBLISHED_VIEWMODE', 3);
define('DASHBOARD_AUTHOR_REJECTED_VIEWMODE', 4);

define('DASHBOARD_SE_IN_REVIEW_VIEWMODE', 23);
define('DASHBOARD_SE_IN_PRODUCTION_VIEWMODE', 24);
define('DASHBOARD_SE_PUBLISHED_VIEWMODE', 25);
define('DASHBOARD_SE_REJECTED_VIEWMODE', 26);

define('DASHBOARD_EDITOR_PENDING_ALL_VIEWMODE', 30);
define('DASHBOARD_EDITOR_PENDING_UNASSIGNED_VIEWMODE', 31);
define('DASHBOARD_EDITOR_PENDING_IN_REVIEW_VIEWMODE', 32);
define('DASHBOARD_EDITOR_PENDING_IN_COPY_EDIT_VIEWMODE', 33);
define('DASHBOARD_EDITOR_PENDING_IN_LAYOUT_VIEWMODE', 34);
define('DASHBOARD_EDITOR_PENDING_READY_FOR_PUBLISHING_VIEWMODE', 35);
define('DASHBOARD_EDITOR_PUBLISHED_VIEWMODE', 36);
define('DASHBOARD_EDITOR_REJECTED_VIEWMODE', 37);

//define('DASHBOARD_DEDICATED_REVIEWER_REQUESTS_VIEWMODE', 50);
define('DASHBOARD_DEDICATED_REVIEWER_PENDING_VIEWMODE', 51);
define('DASHBOARD_DEDICATED_REVIEWER_PENDING_ARCHIVED_VIEWMODE', 52);

define('DASHBOARD_COPY_EDITOR_PENDING_VIEWMODE', 61);
define('DASHBOARD_COPY_EDITOR_ARCHIVED_VIEWMODE', 62);

define('DASHBOARD_LAYOUT_PENDING_VIEWMODE', 71);
define('DASHBOARD_LAYOUT_READY_VIEWMODE', 72);
define('DASHBOARD_LAYOUT_PUBLISHED_VIEWMODE', 73);
define('DASHBOARD_LAYOUT_STATISTICS_VIEWMODE', 74);

define('AUTHOR_ROLE', 11);
define('SE_ROLE', 3);
define('JOURNAL_EDITOR_ROLE', 2);
define('JOURNAL_MANAGER_ROLE', 1);
define('LE_ROLE', 8);
define('CE_ROLE', 9);
define('PUBLIC_ROLE', 9999);

define('REVIEWER_INVITATION_NEW_STATE', 1);
define('REVIEWER_CONFIRMED_STATE', 2);
define('REVIEWER_CANCELLED_STATE', 3);
define('REVIEWER_TIMEDOUT_STATE', 4);
define('REVIEWER_CONFIRMED_BY_SE_STATE', 5);
define('REVIEWER_CANCELLED_BY_SE_STATE', 6);

define('ROUND_DECISION_ACCEPT', 1);
define('ROUND_DECISION_REJECT', 2);
define('ROUND_DECISION_ACCEPT_WITH_MINOR_CORRECTIONS', 3);
define('ROUND_DECISION_ACCEPT_WITH_MAJOR_CORRECTIONS', 4);
define('ROUND_LAYOUT_DECISION_ACCEPT', 6);
define('ROUND_LAYOUT_DECISION_RETURN_TO_AUTHOR', 7);
define('ROUND_LAYOUT_DECISION_RETURN_TO_LAYOUT', 8);
define('ROUND_COPY_EDITING_DECISION_ACCEPT', 9);
define('ROUND_DECISION_REJECT_BUT_RESUBMISSION', 5);

define('DEDICATED_REVIEWER_ROLE', 5);
define('PUBLIC_REVIEWER_ROLE', 6);
define('COMMUNITY_REVIEWER_ROLE', 7);
define('E_ROLE', 2);
define('R_ROUND_TYPE', 1);
define('E_ROUND_TYPE', 4);
define('LE_ROUND_TYPE', 3);
define('CE_ROUND_TYPE', 2);
define('AUTHOR_ROUND_TYPE', 5);

define('DOCUMENT_VERSION_AUTHOR_SUBMITTED_TYPE', 1);
define('DOCUMENT_VERSION_AUTHOR_AFTER_REVIEW_TYPE', 4);


define('TAXON_NOMENCLATURE_TABLE_NAME', 'taxon_categories');

//ajax form validation
define('DEF_AJAXCHECK_URL', 'ajaxFormValidate.php');
define('G_FIELD_ERROR_HOLDER_CLASS', 'fld_error_class');
define('DEF_FORM_FIELD_ID', 'ajax_form_field_');
define('DEF_ERROR_ID_HOLDER', 'ajax_field_error_holder_');

define('JS_VALIDATION', 1);

// Изпращане на мейли / съобщения
define('PENSOFT_SITE_URL', 'http://www.biodiversitydatajournal.com/');
define('PENSOFT_MAIL_ADDR', 'journals@pensoft.net');
define('PENSOFT_MAIL_DISPLAY', 'journals@pensoft.net');
define('PENSOFT_MAIL_ADDR_TEST', 'bdj@pensoft.net');
define('PENSOFT_MAIL_DISPLAY_TEST', 'bdj@pensoft.net');
define('PENSOFT_MAILSUBJ_REGISTER', '[PJS] Verify your account');
define('PENSOFT_MAILSUBJ_FPASS', '[PJS] Forgotten Password');
define('MAILSUBJ_FPASS', '[PJS] Forgotten Password');
define('MAIL_DISPLAY', 'journals@pensoft.net');
define('MAIL_ADDR', 'journals@pensoft.net');

define('REVIEWER_CONFIRMED', 1);
define('REVIEWER_REMOVED', 2);

define('EDIT_JOURNAL_RIGHT_IDS', '(1, 2)' );
define('EDIT_JOURNAL_ISSUES_RIGHT_IDS', '(1, 2)' );

//~ define('G_DEFAULT',   'G_DEFAULT');
//~ define('G_STARTRS', 'G_STARTRS');
//~ define('G_HEADER',   'G_HEADER');
//~ define('G_ROWTEMPL', 'G_ROWTEMPL');
//~ define('G_FOOTER',   'G_FOOTER');
//~ define('G_ENDRS',   'G_ENDRS');
//~ define('G_NODATA',   'G_NODATA');
define('DEFAULT_LIST_PAGE_PARAMETER_NAME', 1117);
define('MT_LINK', 3547);
define('PATH_ECMSSHOPCLASSES', 'PATH_ECMSSHOPCLASSES');

define('DOCUMENT_SUBMITTING_AUTHOR_TYPE', 4 );
define('PWT_VERSION_PREVIEW_URL', PWT_URL . 'lib/ajax_srv/get_document_pjs_preview.php');

define('DEFAULT_PAGE_SIZE', 12);

define('REVIEW_ROUND_ONE', 1);
define('REVIEW_ROUND_TWO', 2);
define('REVIEW_ROUND_THREE', 3);
define('MAX_REVIEW_ROUNDS', 3);
define('DOCUMENT_NON_PEER_REVIEW', 1);
define('DOCUMENT_PUBLIC_PEER_REVIEW', 4);
define('DOCUMENT_COMMUNITY_PEER_REVIEW', 3);
define('DOCUMENT_CLOSED_PEER_REVIEW', 2);
define('SYSTEMATICS_OBJECT_ID', 54);

define('GET_CURSTATE_MANUSCRIPT_SECTION', 1);
define('GET_METADATA_SECTION', 2);
define('GET_SUBMITTED_FILES_SECTION', 3);
define('GET_HISTORY_SECTION', 4);
define('GET_DISCOUNTS_SECTION', 5);
define('GET_SCHEDULING_SECTION', 6);
define('GET_VIEW_SOURCE_SECTION', 7);

define('EVENT_DOCUMENT_ID_DATA_TYPE', 2);
define('EVENT_USER_ID_DATA_TYPE', 3);
define('EVENT_USER_EVENT_TO_ID_DATA_TYPE', 4);
define('EVENT_USER_EVENT_ROLE_ID_DATA_TYPE', 6);
define('EVENT_JOURNAL_ID_DATA_TYPE', 7);

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

define('TASK_DETAIL_SKIP_STATE_ID', 3);
define('TASK_DETAIL_NEW_STATE_ID', 1);
define('TASK_DETAIL_READY_STATE_ID', 2);

define('MANUAL_TASKS_EMAIL_OFFSET', '12 hours');
define('EMAIL_TASK_DETAIL_SENDED_STATE_ID', 4);

define('DOCUMENT_REVIEW_AUTO_SUBMIT_DECISION_ID', 10);

define('DOCUMENT_VERSION_AUTHOR_SUBMIT_TYPE', 1);
define('DOCUMENT_VERSION_REVIEWER_TYPE', 2);
define('DOCUMENT_VERSION_SE_TYPE', 3);
define('DOCUMENT_VERSION_LE_TYPE', 5);
define('DOCUMENT_VERSION_CE_TYPE', 7);
define('DOCUMENT_VERSION_E_TYPE', 9);
define('DOCUMENT_VERSION_PUBLIC_REVIEWER_TYPE', 10);

define('USER_REGISTRATION', 1);
define('REVIEW_ROUND_ONE_NEEDED_DEDICATED_REVIEWERS', 1);
define('REVIEW_ROUND_ONE_NEEDED_PANEL_REVIEWERS', 1);
define('REVIEW_ROUND_TWO_NEEDED_DEDICATED_REVIEWERS', 1);

define('DEFAULT_XML_ENCODING', 'utf-8');

define('COMMENTS_FIX_TYPE_START_POS', 1);
define('COMMENTS_FIX_TYPE_END_POS', 2);
define('COMMENT_START_NODE_NAME', 'comment-start');
define('COMMENT_END_NODE_NAME', 'comment-end');
define('COMMENT_ID_ATTRIBUTE_NAME', 'comment-id');
define('COMMENT_START_POS_TYPE', 1);
define('COMMENT_END_POS_TYPE', 2);
define('COMMENT_POS_MARKER_FAKE_TEXT', '#######COMMENT$$$$$$');

define('HIDDEN_EMAIL_ELEMENT', 'hidden_email_element');
define('MAX_ALLOWED_WRONG_LOGIN_ATTEMPTS', 5);
define('MIN_ALLOWED_PASSWORD_LENGTH', 6);


define('ARTICLE_MENU_ELEMENT_TYPE_CONTENTS', 1);
define('ARTICLE_MENU_ELEMENT_TYPE_FIGURES', 2);
define('ARTICLE_MENU_ELEMENT_TYPE_TABLES', 3);
define('ARTICLE_MENU_ELEMENT_TYPE_REFERENCES', 4);
define('ARTICLE_MENU_ELEMENT_TYPE_SUP_FILES', 5);
define('ARTICLE_MENU_ELEMENT_TYPE_LOCALITIES', 6);
define('ARTICLE_MENU_ELEMENT_TYPE_TAXON', 7);
define('ARTICLE_MENU_ELEMENT_TYPE_AUTHORS', 8);
define('ARTICLE_MENU_ELEMENT_TYPE_CITATION', 9);
define('ARTICLE_MENU_ELEMENT_TYPE_RELATED', 10);
define('ARTICLE_MENU_ELEMENT_TYPE_METRICS', 11);
define('ARTICLE_MENU_ELEMENT_TYPE_SHARE', 12);

//define('PWT_VERSION_PREVIEW_URL', PWT_URL . 'lib/ajax_srv/get_document_pjs_preview.php');
define('PWT_AOF_CACHE_URL', PWT_URL . 'lib/ajax_srv/generate_aof_cache.php');

define('AOF_METRIC_DETAIL_TYPE_VIEW', 1);
define('AOF_METRIC_DETAIL_TYPE_DOWNLOAD', 2);
define('AOF_METRIC_TYPE_HTML', 1);
define('AOF_METRIC_TYPE_PDF', 2);
define('AOF_METRIC_TYPE_XML', 3);
define('AOF_METRIC_TYPE_FIGURE', 4);
define('AOF_METRIC_TYPE_TABLE', 5);
define('AOF_METRIC_TYPE_SUP_FILE', 6);
define('AOF_METRIC_TYPE_NLM_XML', 7);

define('PWT_SUPPLEMENTARY_FILE_DOWNLOAD_SRV', PWT_URL . '/getfile.php?filename={file_name}');
define('PWT_TABLE_CSV_DOWNLOAD_SRV', PWT_URL . '/lib/ajax_srv/csv_export_srv.php?action=export_table_as_csv&instance_id={instance_id}');
define('PWT_FIGURE_ZOOM_SRV', PWT_URL . '/display_zoomed_figure.php?fig_id={instance_id}');
define('PWT_FIGURE_DOWNLOAD_SRV', PWT_URL . '/showfigure.php?filename=big_{pic_id}.jpg&download=1');
define('PWT_PLATE_DOWNLOAD_SRV', PWT_URL . '/lib/ajax_srv/plate_download.php?instance_id={instance_id}');

define('RSS_LIMIT', 100);
define('NLM_XML_ITEM_TYPE', 19);


define('OAI_VERB_GET_IDENTIFY', 'Identify');
define('OAI_VERB_GET_LIST_METADATA_FORMATS', 'ListMetadataFormats');
define('OAI_VERB_GET_LIST_SETS', 'ListSets');
define('OAI_VERB_GET_LIST_IDENTIFIERS', 'ListIdentifiers');
define('OAI_VERB_GET_LIST_RECORDS', 'ListRecords');
define('OAI_VERB_GET_RECORD', 'GetRecord');
define('OAI_REPOSITORY_NAME', 'Biodiversity Data Journal');
define('OAI_URL', SITE_URL . 'oai.php');
define('OAI_PROTOCOL_VERSION', '2.0');
define('OAI_ADMIN_EMAIL', 'development@pensoft.net');
define('OAI_PAGE_SIZE', 10);
define('OAI_ERR_CODE_BAD_RESUMPTION_TOKEN', 1);
define('OAI_ERR_CODE_BAD_ARGUMENT', 2);
define('OAI_ERR_CODE_NO_SET_HEIRARCHY', 3);
define('OAI_ERR_CODE_NO_RECORDS', 4);
define('OAI_ERR_CODE_ID_DOES_NOT_EXIST', 5);
define('OAI_IDENTIFIER_LABEL', 'identifier');
define('OAI_RESUMPTION_TOKEN_LABEL', 'resumptionToken');
define('OAI_SET_LABEL', 'set');
define('OAI_FROM_LABEL', 'from');
define('OAI_UNTIL_LABEL', 'until');
define('OAI_METADATA_PREFIX_LABEL', 'metadataPrefix');
define('OAI_IDENTIFIER_LABEL', 'identifier');
?>