<?php

/**
 * A base model class.
 * All other model class should extend this class.
 *
 * The base class creates a db connection which is to be used by
 * all the other model classes.
 * @author peterg
 *
 */
class emBase_Model{
	/**
	 * A db connection to query the database
	 */
	var $m_con;

	function __construct(){
		$this->m_con = new DBCn();
		$this->m_con->Open();
	}
}


?>