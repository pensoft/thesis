<?php
$docroot = getenv('DOCUMENT_ROOT');
require_once($docroot . '/lib/static.php');
$cn = Con();
$lFeedback = '';
//~ var_dump($_POST);
if ($_POST) {
//~ var_dump($_POST);
	if (preg_match("/^[A-Za-z0-9_\.-]+@([A-Za-z0-9_\.-])+\.[A-Za-z]{2,6}$/", $_POST['email'], $match)) {
		$sql = 'SELECT * FROM spnewsletter(1, \'' . $match[0] . '\', \'' . $_SERVER['REMOTE_ADDR'] . '\', 0, null)';
		//~ echo $sql;
		@$cn->Execute($sql);
		$cn->MoveFirst();
		//~ echo $sql; 
		if ($cn->GetLastError()) {
			$lFeedback = $cn->GetLastError();
		} else {
			$pubdata = array(
				'mailsubject' => NEWSLETTER_MAIL_SUBJECT,
				'charset' => 'UTF-8',
				'confhash' => $cn->mRs['confhash'],
				'user' => $_POST['name'],
				'mailto' => $match[0],
				'url' => SITE_URL . '/newsletter.php',
				'boundary' => '--_separator==_',
				'from' => array(
					'display' => SITE_MAIL_DISPL,
					'email' => SITE_MAIL_ADDR,
				),
				'templs' => array(
					G_DEFAULT => 'newsletter.activate',
				),
			);
			
			$messaging = new cmessaging($pubdata);
			$messaging->Display();
			
			$lFeedback = 'Моля потвърдете вашата заявка, като кликнете на връзката която ви изпратихме на вашият имейл.';
		}
	}
} elseif ($_GET) {
	if ((int)$_GET['unsign']) {
		$sql = 'SELECT * FROM spnewsletter(3, null, \'' . $_SERVER['REMOTE_ADDR'] . '\', null, \'' . q($_GET['hash']) . '\')';
		@$cn->Execute($sql);
		$cn->MoveFirst();
		//~ echo $cn->mRs;
		//~ echo $sql;
		if ($cn->GetLastError()) {
			$lFeedback = $cn->GetLastError();
		} else {
			$pubdata = array(
				'mailsubject' => NEWSLETTER_MAIL_SUBJECT,
				'charset' => 'UTF-8',
				'mailto' => $cn->mRs['email'],
				'url' => SITE_URL . '/newsletter.php',
				'boundary' => '--_separator==_',
				'from' => array(
					'display' => SITE_MAIL_DISPL,
					'email' => SITE_MAIL_ADDR,
				),
				'templs' => array(
					G_DEFAULT => 'newsletter.deactivated',
				),
			);
			$messaging = new cmessaging($pubdata);
			$messaging->Display();
			$lFeedback = 'Вие бяхте отабониран от нашият бюлетин.';
		}
	} elseif ((int)$_GET['validate']) {
		$sql = 'SELECT * FROM spnewsletter(2, null, \'' . $_SERVER['REMOTE_ADDR'] . '\', null, \'' . q($_GET['hash']) . '\')';
		@$cn->Execute($sql);
		$cn->MoveFirst();
		if ($cn->GetLastError()) {
			$lFeedback = $cn->GetLastError();
		} else {
			$lFeedback = 'Вие потвърдихте вашата заявка за абонамент.';
			$pubdata = array(
				'mailsubject' => NEWSLETTER_MAIL_SUBJECT,
				'confhash' => $cn->mRs['confhash'],
				'charset' => 'UTF-8',
				'mailto' => $cn->mRs['email'],
				'url' => SITE_URL . '/newsletter.php',
				'boundary' => '--_separator==_',
				'from' => array(
					'display' => SITE_MAIL_DISPL,
					'email' => SITE_MAIL_ADDR,
				),
				'templs' => array(
					G_DEFAULT => 'newsletter.mailcontent',
				),
			);
			$messaging = new cmessaging($pubdata);
			$messaging->Display();
		}
	}
} else {
	header("Location: /");
	exit;
}

$t = array (
	'contents' => array(
		'ctype' => 'csimple',
		'feedback' => $lFeedback,
		'templs' => array(
			G_DEFAULT => 'newsletter.feedback',
		),
	),
);

$inst = new cpage(array_merge($t, DefObjTempl()), array(G_MAINBODY => 'global.simplepage'));
$inst->Display();

?>