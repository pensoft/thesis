<?php
/**
 *
 * Този клас ще реализира различни действия върху документа без да го показва - например Lock/Unlock
 *
 */
class cdocument extends csimple {
	var $m_documentId;
	function __construct($pFieldTempl) {
		$this->m_con = new DBCn();
		$this->m_con->Open();
		parent::__construct($pFieldTempl);
		$this->m_documentId = $this->m_pubdata['document_id'];
	}

	/**
	 * По зададена lock операция се опитва да промени lock-инга на документа
	 * SQL функцията връща 0 - неуспех, 1- успех, 2 - няма промяна в състоянието - в базата е такова, каквото се опитваме да го сетнем.
	 */
	function lock($pLockOperationId) {
		global $user;
		$lSqlStr = 'SELECT * FROM pwt.spLockDocument(' . q($this->m_documentId) . ', ' . q($pLockOperationId) . ', ' . 2 * (int) DOCUMENT_LOCK_TIMEOUT_INTERVAL . ', ' . q($user->id) . ') as res';
		$this->m_con->Execute($lSqlStr);
		$this->m_con->MoveFirst();
		$this->m_lock_res = (int)$this->m_con->mRs['res'];
		return $this->m_lock_res;
	}

	function  Display(){
		return parent::Display();
	}
}
?>