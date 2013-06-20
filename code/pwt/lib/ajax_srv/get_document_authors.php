<?php
$docroot = getenv('DOCUMENT_ROOT');
require_once ($docroot . '/lib/static.php');

$gDocumentId = (int) $_REQUEST['document_id'];

$lResult = array(
	'err_msg' => '',
	'html' => '',
);

//~ if($gDocumentId){
	//~ $lSqlStr = 'select usr.id, coalesce(ut.name, \'\') || \'  \' || coalesce(usr.first_name, \'\') || \' \' || coalesce(usr.last_name, \'\') as usernames 
						//~ FROM pwt.document_users du
						//~ JOIN public.usr usr ON usr.id = du.usr_id AND usr.id <> ' . q($user->id) . '
						//~ LEFT JOIN public.usr_titles ut ON ut.id = usr.usr_title_id
						//~ WHERE du.document_id = ' . (int) $gDocumentId . '
						//~ GROUP BY usernames, usr.id';
//~ }else{
	$lSqlStr = 'select usr.id, photo_id, coalesce(ut.name, \'\') || \'  \' || coalesce(usr.first_name, \'\') || \' \' || coalesce(usr.last_name, \'\') as usernames 
						FROM pwt.document_users du
						JOIN pwt.document_users dou ON dou.document_id = du.document_id AND dou.usr_id <> ' . q($user->id) . ' AND dou.usr_type = ' . DOCUMENT_AUTHOR_TYPE_ID . '
						JOIN public.usr usr ON usr.id = dou.usr_id AND usr.id <> ' . q($user->id) . '
						LEFT JOIN public.usr_titles ut ON ut.id = usr.usr_title_id
						WHERE du.usr_id = ' . q($user->id) . '
						GROUP BY usernames, usr.id';
//~ }
	$lAuthors = new crs(
		array(
			'ctype'=>'crs', 
			'templs'=>array(
				G_HEADER=>'global.participants_head',
				G_ROWTEMPL=>'global.participants_row',
				G_FOOTER =>'global.participants_foot',
				G_EMPTY =>'global.empty',
				G_NODATA =>'global.participants_empty',
			),
			'sqlstr'=> $lSqlStr,
		)
	);
	$lAuthors->GetData();
	$lResult['html'] = $lAuthors->Display();
	displayAjaxResponse($lResult);

?>