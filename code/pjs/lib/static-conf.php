<?php

//~ define('SITE_URL', 'http://biodiversitydatajournal.com/');
//~ define('PWT_SITE_URL', 'http://pwt.pensoft.net/');
define('SITE_URL', 'http://biodiversitydatajournal.com/');
define('PWT_SITE_URL', 'http://pwt.pensoft.net/');
define('PWT_URL', PWT_SITE_URL);
define('OLD_PJS_SITE_URL', 'http://www.pensoft.net/');
//define('ADM_URL', 'http://nedko.adm.pensoft.etaligent.net');

// db
define('PGDB_SRV', 'localhost');
define('PGDB_DB', 'pensoft2');
define('PGDB_USR', 'iusrpmt');
define('PGDB_PASS', 'oofskldkjn4l6s8jsd22');
define('PGDB_PORT', '5432');
define('DEF_DBTYPE', 'postgres');
define('CONTACT_GROUP_ID', 106);
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
define('MYSQL_CONNECTION_ENCODING', 'utf8');

// path
define('PATH_SHARE', '/var/www/pensoft/');
define('PATH_CHECKOUT', PATH_SHARE . 'production.pmt/');
define('PATH_ITEMS_COMMON', '/var/www/pensoft/pwt_items/');
define('PATH_ITEMS_PRIVATE', PATH_CHECKOUT . 'items/');

define('PATH_ECMSFRCLASSES', PATH_CHECKOUT . 'ecmsframew2.0-mvc/');
define('PATH_CLASSES', PATH_CHECKOUT . 'code/classes/');
define('PATH_STORIES', PATH_ITEMS_COMMON . '/stories/');
define('PATH_MESSAGING', PATH_ITEMS_COMMON . 'messaging/');
define('PATH_DL', PATH_ITEMS_COMMON . 'photos/');
define('PATH_CACHE', PATH_ITEMS_COMMON . 'cache/'. CMS_SITEID . "/");
define('PATH_LANGUAGES', PATH_ITEMS_PRIVATE . 'languages/');

// rewrite
//define('PATH_REWRITE_MAP', PATH_CHECKOUT . 'code/www/lib/rewrite_map.php');
define('ENABLE_MOD_REWRITE', 0);

define('USE_PERL_EXECS', 0);
define('PERL_EXEC_ADDRESS', SITE_URL . '/cgi-bin/exec_console_command.pl');

?>