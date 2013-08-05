<?php
/**
 * A controller used to manage Emails
 * @author viktorp
 *
 */
class cEmail_Messanger_Controller extends cBase_Controller {
	/**
	 * Email Messanger Model Object
	 * 
	 * @var object
	 * 
	 */
	var $m_EmailMessangerModel;
	
	function __construct($pFieldTempl) {
		parent::__construct();
		$this->m_EmailMessangerModel = new mEmail_Messanger_Model();
	}

	function Display(){
		$lEmails = array();
		$lEmails = $this->m_EmailMessangerModel->GetTaskEmails();
		
		foreach ($lEmails as $key => $value) {
			
			$lMessageDate = array(
				'siteurl' => SITE_URL,
				'mailsubject' => $value['subject'],
				'mailto' => $value['to'],
				'content' => ($value['template_notes'] ? $value['template_notes'] . '<br><br><hr><br>' : '') . $value['template'],
				'bcc' => $value['bcc'],
				'cc' => $value['cc'],
				'charset' => 'UTF-8',
				'boundary' => '--_separator==_',
				'from' => array(
					'display' => PENSOFT_MAIL_DISPLAY_TEST,
					'email' => PENSOFT_MAIL_ADDR_TEST,
				),
				'templs' => array(
					G_DEFAULT => 'email_messages.mailcontent',
				),
			);
			$msg = new cmessaging($lMessageDate);
			$msg->Display();
			
			$this->m_EmailMessangerModel->UpdateEmailTaskDetail($value['id']);
		}
	}
}

?>