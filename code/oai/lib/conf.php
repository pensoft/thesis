<?php
// Prazno - zaradi autoloada trqbva da e taka
define('SITE_NAME', 'oai');
define('CMS_SITEID', 1);

define('SITE_URL', 'http://pmt.pensoft.eu');
define('ADM_URL', 'http://pmt.pensoft.eu');
define('PTP_URL', 'http://ptp.pensoft.eu');
define('PRP_URL', 'http://prp.pensoft.eu');
define('OAI_URL', 'http://oai.pensoft.eu');

// db
define('PGDB_SRV', 'localhost');
define('PGDB_DB', 'TRIADA');
define('PGDB_USR', 'root');
define('PGDB_PASS', 'nadyadokoledasheinata');
define('DEF_DBTYPE', 'mysql');
production.

// path 
define('PATH_ECMSFRCLASSES', '/var/www/html/etalig/production.pmt/ecmsframew/');
define('PATH_ECMSSHOPCLASSES', '/var/www/html/etalig/production.pmt/ecmsshop/');
define('PATH_CLASSES', '/var/www/html/etalig/production.pmt/code/classes/');
define('PATH_STORIES', '/var/www/html/etalig/items/stories/');
define('PATH_XML', '/var/www/html/etalig/items/xml/');
define('PATH_WEATHER', '/var/www/html/etalig/items/weather/');
define('PATH_MESSAGING', '/var/www/html/etalig/items/messaging/');
define('PATH_DL', '/var/www/html/etalig/items/photos/');
define('PATH_CACHE', '/var/www/html/etalig/items/cache/'. CMS_SITEID . "/");
define('PATH_LANGUAGES', '/var/www/html/etalig/production.pmt/items/languages/');

// DEFAULT EMPTY TEMPLATE
define('D_EMPTY', 'global.empty');

define('VERB_GET_IDENTIFY', 'Identify');
define('VERB_GET_LIST_METADATA_FORMATS', 'ListMetadataFormats');
define('VERB_GET_LIST_SETS', 'ListSets');
define('VERB_GET_LIST_IDENTIFIERS', 'ListIdentifiers');
define('VERB_GET_LIST_RECORDS', 'ListRecords');
define('VERB_GET_RECORD', 'GetRecord');

define('SET_OPENAIRE_NAME', 'ec_fundedresources');

define('DATE_PHP_FORMAT', 'Y-m-d');
define('DATE_SQL_FORMAT', '\'%Y-%m-%d\'');
define('DATE_TEXT_FORMAT', 'YYYY-MM-DD');

define('REPOSITORY_NAME', 'Pensoft Publishers');
define('PROTOCOL_VERSION', '2.0');
define('ADMIN_EMAIL', 'info@pensoft.net');

define('IDENTIFIER_LABEL', 'identifier');
define('RESUMPTION_TOKEN_LABEL', 'resumptionToken');
define('SET_LABEL', 'set');
define('FROM_LABEL', 'from');
define('UNTIL_LABEL', 'until');
define('METADATA_PREFIX_LABEL', 'metadataPrefix');


//Formatite v koito shte exportvame informaciqta
$gAllowedMetadataFormats = array(
        'oai_dc' => array(
                'schema' => 'http://www.openarchives.org/OAI/2.0/oai_dc.xsd',
                'namespace' => 'http://www.openarchives.org/OAI/2.0/oai_dc/',
        ),
        'mods' => array(
                'schema' => 'http://www.loc.gov/standards/mods/v3/mods-3-1.xsd',
                'namespace' => 'http://www.loc.gov/mods/v3',
        ),
);

define('MYSQL_CONNECTION_ENCODING', 'utf8');
?>