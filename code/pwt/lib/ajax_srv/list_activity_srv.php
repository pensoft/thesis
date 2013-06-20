<?php
$docroot = getenv('DOCUMENT_ROOT');
require_once($docroot . '/lib/static.php');
ini_set('display_errors', 'off');

$lPageNum = (int)$_REQUEST['p'];

$lResult = array(
	'err_cnt' => 0,
	'err_msg' => '',
	'validation_err_cnt' => 0,
);

if( $lPageNum > 0 ){
	$lActivity = new crs(array(
		'ctype' => 'crs', 
		'templs'=> array(
			G_HEADER   => 'global.empty',
			G_ROWTEMPL => 'dashboard.activity_row',
			G_FOOTER   => 'global.empty',
			G_NODATA   => 'global.empty',
		),
		'pagesize' => ACTIVITY_RECORDS_PER_PAGE,
		'usecustompn' => 1,
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

	$lActivity->GetData();
	$lResult['html'] = $lActivity->Display();
	
	if( $lActivity->getVal('maxpages') <= ($lPageNum + 1) )
		$lResult['html'] .= '';
	else{
		$lResult['html'] .= '<div class="P-Activity-Fieed-See-All-Recent-Activity">
							<a href="javascript: void(0);" onclick="getNextActivityPage(' . ($lPageNum + 1) . ');">' . getstr('dashboard.see_more') . '<!-- See All Recent activity --></a>
						</div>
					</div>
				</div><!-- End P-Wrapper-Container-Right -->';
	}
	
	displayAjaxResponse($lResult);
}
?>