<?php
// Prazno - zaradi autoloada trqbva da e taka
define('SITE_NAME', 'cmstemplate');
define('CMS_SITEID', 1);

define('SITE_URL', 'www.pmt.etaligent.net');
define('ADM_URL', 'adm.pmt.etaligent.net');

// db
define('PGDB_SRV', 'skylab.etaligent.net');
define('PGDB_DB', 'pmt_test');
define('PGDB_USR', 'iusrpmt');
define('PGDB_PASS', 'oofskldkjn4l6s8jsd22');
define('PGDB_PORT', '5431');

// path
define('PATH_ECMSFRCLASSES', '/var/www/pmt/pmt_work/rado.cmstemplate/ecmsframew/');
define('PATH_ECMSSHOPCLASSES', '/var/www/pmt/pmt_work/rado.cmstemplate/ecmsshop/');
define('PATH_CLASSES', '/var/www/pmt/pmt_work/rado.cmstemplate/code/classes/');
define('PATH_STORIES', '/var/www/pmt/pmt_work/items/stories/');
define('PATH_WEATHER', '/var/www/pmt/pmt_work/items/weather/');
define('PATH_MESSAGING', '/var/www/pmt/pmt_work/items/messaging/');
define('PATH_DL', '/var/www/pmt/pmt_work/items/photos/');
define('PATH_CACHE', '/var/www/pmt/pmt_work/items/cache/'. CMS_SITEID . "/");
define('PATH_LANGUAGES', '/var/www/pmt/pmt_work/items/languages/');

// rewrite
//define('PATH_REWRITE_MAP', '/var/www/pmt/pmt_work/rado.cmstemplate/code/www/lib/rewrite_map.php');
define('ENABLE_MOD_REWRITE', 0);

define('SHOWIMG_URL', '/showimg.php?filename=');
define('GETATT_URL', '/getatt.php?filename=');
define('CMS_SITEID', 1);

// DEFAULT EMPTY TEMPLATE
define('D_EMPTY', 'global.empty');

?>