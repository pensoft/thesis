<?php
require_once ("db_postgres.php");
// ~ require_once( "db_oracle.php" ) ;
require_once ("db_mysql.php");

define('DB_TYPE_POSTGRES', 'postgres');
define('DB_TYPE_MYSQL', 'mysql');
define('DB_TYPE_ORACLE', 'oracle');

class DBCreator {
	var $m_DbType;
	function __construct($pDbType) {
		if(trim($pDbType) == '')
			$pDbType = DEF_DBTYPE;
		$this->m_DbType = $pDbType;
	}

	function createHelper() {
		switch ($this->m_DbType) {
			default :
				trigger_error("Unknown DB type: " . $this->m_DbType . "!Please define in site_ini!", E_USER_ERROR);
				return null;
			case DB_TYPE_POSTGRES :
				return new PostgresHelper();
			case DB_TYPE_MYSQL :
				return new MySqlHelper();
		}
	}

	function createConnection() {
		switch ($this->m_DbType) {
			default :
				trigger_error("Unknown DB type: " . $this->m_DbType . "!Please define in site_ini!", E_USER_ERROR);
				return null;
			case DB_TYPE_POSTGRES :
				return new DBCnPostgres();
			case DB_TYPE_MYSQL :
				return new DBCnMySql();
		}
	}
}

?>