<?php
$docroot = getenv('DOCUMENT_ROOT');
require_once ($docroot . '/lib/static.php');

$gDocumentId = (int) $_REQUEST['document_id'];
$gRecipients = $_REQUEST['recipients'];
$gSubject = $_REQUEST['subject'];
$gEmailBody = $_REQUEST['email_body'];
$gSender = (int)$_REQUEST['sender'];
$gType = (int)$_REQUEST['type'];

if(!$gType) {
	$gType = 1;
}

if(!(int)$gSender) {
	$gSender = (int)$user->id;
}

$lResult = array(
	'err_msg' => '',
	'html' => '',
);

$lRecipientIds = implode(',', $gRecipients);

$lSqlStr = 'SELECT u.id,  u.uname as email
						FROM public.usr u
						WHERE u.id IN (' . $lRecipientIds . ')
						';

$lCon = Con();
$lCon->Execute($lSqlStr);

while(!$lCon->Eof()){
	if($lCon->mRs['email']) {
		$mespubdata = array(
				'siteurl' => SITE_URL,
				'mailsubject' => q($gSubject),
				'mailto' => q($lCon->mRs['email']),
				'mailbody' => q($gEmailBody),
				'charset' => 'UTF-8',
				'boundary' => '--_separator==_',
				'from' => array(
					'display' => $user->fullname,
					'email' => $user->uname,
				),
				'templs' => array(
					G_DEFAULT => 'global.sendmail',
				),
			);
		$msg = new cmessaging($mespubdata);
		$msg->Display();
	}
	$lCon->MoveNext();
}

$lSql = 'SELECT * FROM spInbox(0, null, ' . (int)$gSender . ', \'' . q($lRecipientIds) . '\', \'' . q($gSubject) . '\', \'' . q($gEmailBody) . '\', ' . (int)$gType . ')';
$lCon2 = Con();
$lCon2->Execute($lSql);

echo 1;

?>