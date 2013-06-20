<?php

/**
 * A form controller which doesn't close the session for writing
 * @author peterg
 *
 */
class ecForm_Controller_Open_Session extends ecForm_Controller {
	protected function InitSessionCloseVariable(){
		$this->m_closeSession = false;
	}
}

?>