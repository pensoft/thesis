<?php
$docroot = getenv('DOCUMENT_ROOT');
ini_set('display_errors', 'Off');
require_once($docroot . '/lib/static.php');
	$gKforFlds = array(	
		
	'date' => array(
		'VType' => 'date',
		'CType' => 'text',
		'DisplayName' => getstr('admin.doaj.colDate'),
		'AddTags' => array(
			'class' => 'coolinp',
		),
		'DateType' => DATE_TYPE_DATETIME,
		'AllowNulls' => false,
	),
	
	
	'journal_id' => array(
		'VType' => 'int',
		'CType' => 'select',
		'SrcValues' => 'SELECT null as id, \'--\' as name, 0 as ord 
				UNION 
			SELECT pensoft_id as id, pensoft_title as name, 1 as ord FROM journals 
			ORDER BY ord, name
		',
		'DisplayName' => getstr('admin.doaj.colJournalId'),
		'AddTags' => array(
			'class' => 'coolinp',
		),
		'AllowNulls' => false,
	),	
	
	'show' => array(
		'CType' => 'action',
		'DisplayName' => getstr('admin.showButton'),
		'SQL' => '{date}{journal_id}',
		'ActionMask' => ACTION_CHECK | ACTION_SHOW,
		'AddTags' => array(
			'class' => 'frmbutton',
		),	
	),
);

$gKforTpl = '
	<div class="t">
	<div class="b">
	<div class="l">
	<div class="r">
		<div class="bl">
		<div class="br">
		<div class="tl">
		<div class="tr">
			<table cellspacing="0" cellpadding="5" border="0" class="formtable">
				<colgroup>
					<col width="50%"/>
					<col width="50%"/>
				</colgroup>
				<tr>
					<th colspan="2">' . getstr('admin.doaj.title') . '</th>
				</tr>
				<tr>
					<td>{*date}<br/>{date}</td>
					<td>{*journal_id}<br/>{journal_id}</td>
				</tr>
				
				<tr>
					<td colspan="2" align="right">{show}</td>
				</tr>
			</table>
		</div>
		</div>
		</div>
		</div>
	</div>
	</div>
	</div>
	</div>
';

$gKfor = new kfor($gKforFlds, $gKforTpl, 'GET');
$gKfor->debug = false;

$gKfor->ExecAction();
if($gKfor->lCurAction == 'show' && !$gKfor->lErrorCount){
	$gContent = new cdoaj_exporter(array(
		'date' => $gKfor->lFieldArr['date']['CurValue'],
		'journal_id' => $gKfor->lFieldArr['journal_id']['CurValue'],
		'templs' => array(
			G_HEADER => 'doaj.listHead',
			G_FOOTER => 'doaj.listFoot',
			G_STARTRS => 'doaj.listStart',
			G_ENDRS => 'doaj.listEnd',
			G_ERR_TEMPL => 'doaj.errRow',
			G_ROWTEMPL => 'doaj.listRow',
		),
		'parse_keywords' => 1,
		'keywords_templs' => array(
			G_HEADER => 'doaj.listKeywordsHead',
			G_FOOTER => 'doaj.listKeywordsFoot',
			G_ROWTEMPL => 'doaj.listKeywordsRow',
		),
		
		'parse_authors' => 1,
		'authors_templs' => array(
			G_HEADER => 'doaj.listAuthorsHead',
			G_FOOTER => 'doaj.listAuthorsFoot',
			G_ROWTEMPL => 'doaj.listAuthorsRow',
		),
	));
	ProccessHistory();
	UserRedir($user);
	$gContent->GetData();
	if(!$gContent->m_err_count){
		header("Content-type: text/xml"); 
	}
	echo $gContent->Display();
}else{
	HtmlStart();
	echo $gKfor->Display();
	HtmlEnd();
}


	
?>