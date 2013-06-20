<?php
require_once('common_conf.php');
// Prazno - zaradi autoloada trqbva da e taka
define('SITE_NAME', 'pwt_admin');
define('CMS_SITEID', 1);
define('STORIES_DSCID', 1);

define('SITE_URL', 'http://admpwt-real.pensoft.etaligent.net');

// db
/*
define('PGDB_SRV', 'phase.etaligent.net');
//~ define('PGDB_DB', 'pmt_test');
//~ define('PGDB_DB', 'pmt_test2');
define('PGDB_DB', 'pensoft');
define('PGDB_USR', 'iusrpmt');
define('PGDB_PASS', 'oofskldkjn4l6s8jsd22');
define('PGDB_PORT', '5432');
define('DEF_DBTYPE', 'postgres');
*/

// db
define('PGDB_SRV', 'localhost');
define('PGDB_DB', 'pensoft2');
define('PGDB_USR', 'iusrpmt');
define('PGDB_PASS', 'oofskldkjn4l6s8jsd22');
define('PGDB_PORT', '5432');
define('DEF_DBTYPE', 'postgres');

// path 
define('PATH_ECMSFRCLASSES', '/var/www/pensoft/production.pmt/ecmsframew/');
define('PATH_ECMSSHOPCLASSES', '/var/www/pensoft/production.pmt/ecmsshop/');
define('PATH_CLASSES', '/var/www/pensoft/production.pmt/code/classes/');
define('PATH_STORIES', '/var/www/pensoft/production.pmt/items/stories/');
define('PATH_XML', '/var/www/pensoft/production.pmt/items/xml/');
define('PATH_WEATHER', '/var/www/pensoft/production.pmt/items/weather/');
define('PATH_MESSAGING', '/var/www/pensoft/production.pmt/items/messaging/');
define('PATH_DL', '/var/www/pensoft/production.pmt/items/photos/');
define('PATH_CACHE', '/var/www/pensoft/production.pmt/items/cache/'. CMS_SITEID . "/");
define('PATH_LANGUAGES', '/var/www/pensoft/production.pmt/items/languages/');

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
//~ define('FCK_CUSTOMTOOLSNAME_TOOLS', 3);

// Binaries
define('BINARY_FFMPEG', exec('which ffmpeg'));
define('BINARY_CONVERT', exec('which convert'));



?>