<?php
/**
 * Клас, който ще реализира показването на 1 екшън към даден instance
 */
class caction_item extends csimple {
	var $m_id;
	var $m_instanceId;
	var $m_jsAction;
	var $m_pos;
	var $m_htmlControlType;
	var $m_displayName;

	function __construct($pFieldTempl) {

		parent::__construct($pFieldTempl);

		$this->m_id = (int)$pFieldTempl['id'];
		$this->m_instanceId = $pFieldTempl['instance_id'];
		$this->m_jsAction = $pFieldTempl['js_action'];
		$this->m_pos = (int)$pFieldTempl['pos'];
		$this->m_displayName = $pFieldTempl['display_name'];
		$this->m_htmlControlType = (int)$pFieldTempl['html_control_type'];
		if(!$this->m_jsAction){
			$this->m_jsAction = getActionDefaultJsAction($this->m_id, $this->m_instanceId);
		}
		$this->m_jsAction = $this->ReplaceHtmlFields($this->m_jsAction);
		$this->m_pubdata['js_action'] = $this->m_jsAction;

	}

	function LoadDefTempls() {
		if (!defined('D_EMPTY')) {
			define('D_EMPTY', 'global.empty');
		}

		$this->m_defTempls = array(
			G_MOVE_UP_ROW => D_EMPTY,
			G_MOVE_DOWN_ROW => D_EMPTY,
			G_TOP_RED_ROW => D_EMPTY,
			G_BOTTOM_RED_ROW => D_EMPTY,
			G_ADD_ROW => D_EMPTY,
			G_BOTTOM_EDIT_ROW => D_EMPTY,
			G_ADD_ALL_ROW => D_EMPTY,
			G_COMMENT_ROW => D_EMPTY,
			G_VALIDATION_ROW => D_EMPTY,
			G_CHECK_NAME_AVAILABILITY_ROW => D_EMPTY,
			G_BOTTOM_SAVE_ROW => D_EMPTY,
			G_BOTTOM_CANCEL_ROW => D_EMPTY,
			G_TOP_CHANGE_MODE_ROW => D_EMPTY,
			G_RIGHT_MOVE_UP_ROW => D_EMPTY,
			G_RIGHT_MOVE_DOWN_ROW => D_EMPTY,
			G_RIGHT_DELETE_ROW => D_EMPTY
		);
	}


	function GetData(){

		switch ($this->m_htmlControlType) {
			case (int)ACTION_HTML_MOVE_UP_TYPE:
				$lDisplay = 'none';
				if((int)$this->m_pubdata['allow_move_up']){
					$lDisplay = '';
				}
				$this->m_pubdata['display'] = $lDisplay;
				$this->m_pubdata['action'] = $this->ReplaceHtmlFields($this->getObjTemplate(G_MOVE_UP_ROW));
				break;
			case (int)ACTION_HTML_MOVE_DOWN_TYPE:
				$lDisplay = 'none';
				if((int)$this->m_pubdata['allow_move_down']){
					$lDisplay = '';
				}
				$this->m_pubdata['display'] = $lDisplay;
				$this->m_pubdata['action'] = $this->ReplaceHtmlFields($this->getObjTemplate(G_MOVE_DOWN_ROW));
				break;
			case (int)ACTION_HTML_TOP_RED_BTN_TYPE:
				$this->m_pubdata['action'] = $this->ReplaceHtmlFields($this->getObjTemplate(G_TOP_RED_ROW));
				break;
			case (int)ACTION_HTML_BOTTTOM_RED_BTN_TYPE:
				$this->m_pubdata['action'] = $this->ReplaceHtmlFields($this->getObjTemplate(G_BOTTOM_RED_ROW));
				break;
			case (int)ACTION_HTML_ADD_BTN_TYPE:
				$this->m_pubdata['action'] = $this->ReplaceHtmlFields($this->getObjTemplate(G_ADD_ROW));
				break;
			case (int)ACTION_HTML_BOTTOM_EDIT_TYPE:
				$this->m_pubdata['action'] = $this->ReplaceHtmlFields($this->getObjTemplate(G_BOTTOM_EDIT_ROW));
				break;
			case (int)ACTION_HTML_ADD_ALL_TYPE:
				$this->m_pubdata['action'] = $this->ReplaceHtmlFields($this->getObjTemplate(G_ADD_ALL_ROW));
				break;
			case (int)ACTION_HTML_COMMENT_TYPE:
				$this->m_pubdata['action'] = $this->ReplaceHtmlFields($this->getObjTemplate(G_COMMENT_ROW));
				break;
			case (int)ACTION_HTML_VALIDATION_TYPE:
				$this->m_pubdata['action'] = $this->ReplaceHtmlFields($this->getObjTemplate(G_VALIDATION_ROW));
				break;
			case (int)ACTION_HTML_CHECK_NAME_AVAILABILITY_TYPE:
				$this->m_pubdata['action'] = $this->ReplaceHtmlFields($this->getObjTemplate(G_CHECK_NAME_AVAILABILITY_ROW));
				break;
			case (int)ACTION_HTML_BOTTOM_SAVE_BTN_TYPE:
				$this->m_pubdata['action'] = $this->ReplaceHtmlFields($this->getObjTemplate(G_BOTTOM_SAVE_ROW));
				break;
			case (int)ACTION_HTML_BOTTOM_CANCEL_BTN_TYPE:
				$this->m_pubdata['action'] = $this->ReplaceHtmlFields($this->getObjTemplate(G_BOTTOM_CANCEL_ROW));
				break;
			case (int)ACTION_HTML_TOP_CHANGE_MODE_TYPE:
				$this->m_pubdata['action'] = $this->ReplaceHtmlFields($this->getObjTemplate(G_TOP_CHANGE_MODE_ROW));
				break;
			case (int) ACTION_HTML_RIGHT_MOVE_UP_TYPE :
				$lDisplay = 'none';
				if((int)$this->m_pubdata['allow_move_up']){
					$lDisplay = '';
				}
				$this->m_pubdata['display'] = $lDisplay;
				$this->m_pubdata['action'] = $this->ReplaceHtmlFields($this->getObjTemplate(G_RIGHT_MOVE_UP_ROW));
				break;
			case (int) ACTION_HTML_RIGHT_MOVE_DOWN_TYPE :
				$lDisplay = 'none';
				if((int)$this->m_pubdata['allow_move_down']){
					$lDisplay = '';
				}
				$this->m_pubdata['display'] = $lDisplay;
				$this->m_pubdata['action'] = $this->ReplaceHtmlFields($this->getObjTemplate(G_RIGHT_MOVE_DOWN_ROW));
				break;
			case (int) ACTION_HTML_RIGHT_DELETE_TYPE :
				$this->m_pubdata['action'] = $this->ReplaceHtmlFields($this->getObjTemplate(G_RIGHT_DELETE_ROW));
				break;
			default:
			break;
		}

	}

	function Display(){
		$this->GetData();


		return $this->ReplaceHtmlFields($this->getObjTemplate(G_DEFAULT));
	}

}


?>