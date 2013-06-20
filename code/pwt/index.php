<?php
$docroot = getenv('DOCUMENT_ROOT');
require_once($docroot . '/lib/static.php');

$lShowAll = (int)$_GET['showall'];
if((int)$lShowAll && ($user->admin == 'false' || $user->admin == 'f')) {
	header('Location: /');
}

$lActivity = new crs(array(
		'ctype' => 'crs', 
		'templs'=> array(
			G_HEADER   => 'dashboard.activity_head',
			G_ROWTEMPL => 'dashboard.activity_row',
			G_FOOTER   => 'dashboard.activity_foot',
			G_NODATA   => 'dashboard.activity_empty',
		),
		'pagesize' => ACTIVITY_RECORDS_PER_PAGE,
		'sqlstr' => 'SELECT d.id as document_id, pu.photo_id, d.name as name, coalesce(pu.first_name, \'\') || \' \' || coalesce(pu.last_name, \'\') as editedbyuser, 
							ac.action_time as editdate, at.name as activity_type
						FROM pwt.document_users du
						JOIN pwt.activity ac ON ac.document_id = du.document_id
						JOIN pwt.documents d ON du.document_id = d.id
						JOIN public.usr pu ON pu.id = ac.usr_id
						JOIN pwt.activity_types at ON ac.action_type = at.id
						LEFT JOIN public.usr_titles ut ON ut.id = pu.usr_title_id
						WHERE du.usr_id = ' . (int)$user->id . '
						ORDER BY ac.action_time DESC',
));

// Резетваме състоянието на менюто и колоните
unset($_SESSION['activemenutabids']);
unset($_SESSION['columnsstate']);

$lActivity->m_page = 0;

// if the user is admin and wants to see all documents (not only his documents)
if($lShowAll) {
	$lPageSize = 0;
	$lSqlStr = 'SELECT d.id as document_id,  d.createuid as document_creator, d.name as name, d.is_locked::integer, d.lock_usr_id as editedbyuserid, coalesce(ut.name, \'\') || \'  \' || coalesce(pu.first_name, \'\') || \' \' || coalesce(pu.last_name, \'\') as editedbyuser, 
			d.state as documentstatus, d.lastmoddate, 
			--array_to_string(array_agg(DISTINCT (coalesce(dou.first_name, \'\') || \' \' || coalesce(dou.middle_name, \'\') || \' \' || coalesce(dou.last_name, \'\'))), \', \') as fullname
(SELECT aggr_concat_coma((coalesce(a.first_name, \'\') || \' \' || coalesce(a.middle_name, \'\') || \' \' || coalesce(a.last_name, \'\'))) 
FROM (SELECT first_name, middle_name, last_name FROM pwt.document_users WHERE usr_type = ' . (int)DOCUMENT_AUTHOR_TYPE_ID . ' AND document_id = d.id ORDER BY ord) a) as fullname
	, max(pt."name") as papertype, d.createdate::date, d.lastmoddate::date as lastdate
	FROM pwt.documents d
		JOIN pwt.papertypes pt on pt.id = d.papertype_id
	JOIN public.usr pu1 ON pu1.id = d.createuid
	--JOIN pwt.document_users dou ON dou.document_id = d.id AND dou.usr_type = ' . (int)DOCUMENT_AUTHOR_TYPE_ID . ' 
	LEFT JOIN public.usr pu ON pu.id = d.lock_usr_id
	LEFT JOIN public.usr_titles ut ON ut.id = pu.usr_title_id
	WHERE pu1.staff = 0 AND d.state NOT IN (' . (int)DELETED_DOCUMENT_STATE . ')
	GROUP BY d.id, d.name, d.lastmoddate, ut.name, pu.first_name, pu.last_name
	ORDER BY d.lastmoddate DESC';
} else {
	$lPageSize = 10;
	$lSqlStr = 'SELECT d.id as document_id,  d.createuid as document_creator, d.name as name, d.is_locked::integer, d.lock_usr_id as editedbyuserid, coalesce(ut.name, \'\') || \'  \' || coalesce(pu.first_name, \'\') || \' \' || coalesce(pu.last_name, \'\') as editedbyuser, 
			d.state as documentstatus, d.lastmoddate, 
--array_to_string(array_agg(DISTINCT (coalesce(dou.first_name, \'\') || \' \' || coalesce(dou.middle_name, \'\') || \' \' || coalesce(dou.last_name, \'\'))), \', \') as fullname
(SELECT aggr_concat_coma((coalesce(a.first_name, \'\') || \' \' || coalesce(a.middle_name, \'\') || \' \' || coalesce(a.last_name, \'\'))) 
FROM (SELECT first_name, middle_name, last_name FROM pwt.document_users WHERE usr_type = ' . (int)DOCUMENT_AUTHOR_TYPE_ID . ' AND document_id = d.id ORDER BY ord) a) as fullname 
			, max(pt."name") as papertype, d.createdate::date, d.lastmoddate::date as lastdate  
	FROM pwt.document_users du
	JOIN pwt.documents d ON du.document_id = d.id
		JOIN pwt.papertypes pt on pt.id = d.papertype_id
	JOIN pwt.document_users dou ON dou.document_id = d.id AND dou.usr_type = ' . (int)DOCUMENT_AUTHOR_TYPE_ID . ' 
	LEFT JOIN public.usr pu ON pu.id = d.lock_usr_id
	LEFT JOIN public.usr_titles ut ON ut.id = pu.usr_title_id
	WHERE du.usr_id = ' . (int)$user->id . ' AND d.state NOT IN (' . (int)DELETED_DOCUMENT_STATE . ')
	GROUP BY d.id, d.name, d.lastmoddate, ut.name, pu.first_name, pu.last_name
	ORDER BY d.lastmoddate DESC';
}
//var_dump($lSqlStr);
$lPageArray = array(
	'content' => array(
			'ctype' => 'crs', 
			'templs'=> array(
				G_HEADER   => 'global.empty',
				G_STARTRS => 'dashboard.content_head',
				G_ROWTEMPL => 'dashboard.content_row',
				G_FOOTER   => 'dashboard.content_foot',
				G_NODATA   => 'global.empty',
			),
			'pagesize' => $lPageSize,
			'usecustompn' => 1,
			'sqlstr' => $lSqlStr,
			'showall' => (int)$lShowAll,
	),
	
	'activity' => $lActivity,
);

$inst = new cpage(array_merge($lPageArray, DefObjTempl()), array(G_MAINBODY => 'global.index_page'));
$inst->Display();
?>