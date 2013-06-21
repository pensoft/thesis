<?php
// Prazno - zaradi autoloada trqbva da e taka
define('SITE_NAME', 'pwt');
define('CMS_SITEID', 1);

//~ define('SITE_URL', 'http://pwt.pensoft.etaligent.net');
//~ define('AJAX_URL', 'http://pensoft:pensoft123@pwt.pensoft.etaligent.net');

define('SITE_URL', 'http://jordan.pwt.pensoft.dev');
define('PJS_SITE_URL', 'http://biodiversitydatajournal.com');
define('OLD_PJS_SITE_URL', 'http://www.pensoft.net');
define('AJAX_URL', 'http://pwt.pensoft.net');


define('PTP_URL', 'http://ptp.pmt.etaligent.net');

// db
define('PGDB_SRV', 'localhost');
define('PGDB_DB', 'pensoft_import_test6');
define('PGDB_USR', 'iusrpmt');
define('PGDB_PASS', 'oofskldkjn4l6s8jsd22');
define('PGDB_PORT', '5432');
define('DEF_DBTYPE', 'postgres');

//pmt db
define('PMT_PGDB_SRV', 'skylab.etaligent.net');
//~ define('PGDB_DB', 'pmt_test');
//~ define('PGDB_DB', 'pmt_test2');
define('PMT_PGDB_DB', 'pmt');
define('PMT_PGDB_USR', 'iusrpmt');
define('PMT_PGDB_PASS', 'oofskldkjn4l6s8jsd22');
define('PMT_PGDB_PORT', '5431');
define('PMT_DEF_DBTYPE', 'postgres');


// Db MYSQL
define('MYSQL_DBTYPE', 'mysql');
define('MYSQL_PORT', 3306);

//e-back.etaligent.net
//~ define('MYSQL_DB_SRV', 'e-back.etaligent.net'); 
//~ define('MYSQL_DB', 'seyhan');
//~ define('MYSQL_USR', 'seyhan');
//~ define('MYSQL_PASS', 'test78');

//pensoft.net
define('MYSQL_DB_SRV', 'pensoft.net'); 
define('MYSQL_DB', 'TRIADA');
define('MYSQL_USR', 'etal');
define('MYSQL_PASS', 'nadyadokoledasheinata');


// path 
define('PATH_CHECKOUT', '/var/www/pensoft/jordan.main/');
define('PATH_ITEMS_COMMON', '/var/www/pensoft/items/');
define('PATH_ITEMS_PRIVATE', PATH_CHECKOUT . 'items/');
define('PATH_PWT_ITEMS_COMMON', '/var/www/pensoft/pwt_items/');

define('PATH_ECMSFRCLASSES', PATH_CHECKOUT . 'ecmsframew/');
define('PATH_ECMSSHOPCLASSES', PATH_CHECKOUT . 'ecmsshop/');
define('PATH_CLASSES', PATH_CHECKOUT . 'code/classes/');
define('PATH_CLASSES_FIELDS', PATH_CLASSES . SITE_NAME . '/fields/');
define('PATH_STORIES', PATH_PWT_ITEMS_COMMON . 'stories/');
define('PATH_MESSAGING', PATH_PWT_ITEMS_COMMON . 'messaging/');
define('PATH_DL', PATH_PWT_ITEMS_COMMON . 'photos/');
define('PATH_PWT_DL', PATH_PWT_ITEMS_COMMON . 'photos/');
define('PATH_CACHE', PATH_PWT_ITEMS_COMMON . 'cache/'. CMS_SITEID . "/");
define('PATH_LANGUAGES', PATH_ITEMS_PRIVATE . 'languages/');

// URLs
define('SHOWIMG_URL', '/showimg.php?filename=');
define('GETATT_URL', '/getatt.php?filename=');

// FCK editor
define('FCK_BASEPATH', '/lib/');
define('FCK_DEFAULT_TOOLBAR', 'AllTools');
define('FCK_DEFAULT_FILE', 'fckeditor.html');
define('FCK_DEFAULT_WIDTH', '100%');
define('FCK_DEFAULT_HEIGHT', '200');

// Administrator
define('ADMIN_UID', 1);

// DEFAULT EMPTY TEMPLATE
define('D_EMPTY', 'global.empty');

// Tova e za custom toolbar-i na FCK editora
// Nai malkia kluch trqbva da e 3
$FCK_Custom_Toolbars = array(
	//~ 3 => 'CustomToolsName',
);

define('MAIL_ADDR', 'PenSoft');
define('MAIL_DISPLAY', 'PenSoft');
define('MAILSUBJ_REGISTER', 'Pensoft Registration Confirm');
define('MAILSUBJ_FPASS', 'Pensoft Forgotten Password');
define('MAILSUBJ_DOC_NEW_AUTHOR', 'Pensoft - New Document Author');

// JS_VALIDATION_ON
define('JS_VALIDATION_ON', 1);
//~ define('DEFAULT_ERROR_STRING', 'Error');
define('DEF_AJAXCHECK_URL', '/lib/ajaxFormValidate.php');

// SEARCHING VARIABLES
define('SEARCH_IN_ARTICLE', 1);
define('SEARCH_IN_ALL_ARTICLES', 2);

define('USE_PERL_EXECS', 0);
define('PERL_EXEC_ADDRESS', SITE_URL . '/cgi-bin/exec_console_command.pl');

//~ define('ENABLE_FEATURES', 0);
// 15MB maximum figure picture file size
define('MAX_FIGURE_PIC_FILE_SIZE', 15*1024*1024);

define('ACCEPT_REQUEST_BY_IP', '195.189.81.192/32');
if ($_SERVER['REMOTE_ADDR'] == '193.194.140.163'){
	define('debugging', 0);
}else{
	define('debugging', 0);
}

?>