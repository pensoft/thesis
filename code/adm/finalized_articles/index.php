<?php
$docroot = getenv("DOCUMENT_ROOT");
require_once($docroot . '/lib/static.php');
$historypagetype = HISTORY_CLEAR;


$gListPage = (int)$_GET['p'];

HtmlStart();

$gKforFlds = array(	
		
	'title' => array(
		'VType' => 'string',
		'CType' => 'text',
		'DisplayName' => getstr('admin.articles.titleAndMetadata'),
		'AddTags' => array(
			'class' => 'coolinp',
		),
		'AllowNulls' => true,
	),
	
	'content' => array(
		'VType' => 'string',
		'CType' => 'text',
		'DisplayName' => getstr('admin.articles.content'),
		'AddTags' => array(
			'class' => 'coolinp',
		),
		'AllowNulls' => true,
	),
	
	
	
	'journal_id' => array(
		'VType' => 'int',
		'CType' => 'select',
		'SrcValues' => 'SELECT null as id, \'--\' as name, 0 as ord 
				UNION 
			SELECT id, name, 1 as ord FROM journals
			ORDER BY ord, name
		',
		'DisplayName' => getstr('admin.articles.journal'),
		'AddTags' => array(
			'class' => 'coolinp',
		),
		'AllowNulls' => false,
	),	
	
	'issue' => array(
		'VType' => 'int',
		'CType' => 'text',
		'DisplayName' => getstr('admin.articles.issue'),
		'AddTags' => array(
			'class' => 'coolinp',
		),
		'AllowNulls' => false,
	),
	
	'show' => array(
		'CType' => 'action',
		'DisplayName' => getstr('admin.filterButton'),
		'SQL' => '',
		'ActionMask' => ACTION_CHECK | ACTION_SHOW,
		'AddTags' => array(
			'class' => 'frmbutton',
		),	
	),
	
	'journal_filter' => array(
		'CType' => 'action',
		'DisplayName' => getstr('admin.filterIssueButton'),
		'SQL' => '{journal_id}, {issue}',
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
					<th colspan="2">' . getstr('admin.articles.filter') . '</th>
				</tr>
				<tr>
					<td>{*title}<br/>{title}</td>
					<td>{*content}<br/>{content}</td>
				</tr>
				<tr>
					<td colspan="2" align="right">{show}</td>
				</tr>
				<tr>
					<td>{*journal_id}<br/>{journal_id}</td>
					<td>{*issue}<br/>{issue}</td>
				</tr>
				<tr>
					<td colspan="2" align="right">{journal_filter}</td>
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
echo $gKfor->Display();

$lWhereArr = array();
$lExports = array(EOL_EXPORT_TYPE, WIKI_EXPORT_TYPE, KEYS_EXPORT_TYPE);
$lPageSize = 30;

$lExportPrefixes = array(
	EOL_EXPORT_TYPE => array(
		'prefix' => 'eol_',
		'title' => 'Eol',
	),
	WIKI_EXPORT_TYPE => array(
		'prefix' => 'wiki_',
		'title' => 'Wiki',
	),
	KEYS_EXPORT_TYPE => array(
		'prefix' => 'keys_',
		'title' => 'Keys',
	),
);

if( $gKfor->lCurAction == 'show' && !$gKfor->lErrorCount){
	if( trim($gKfor->lFieldArr['content']['CurValue'])){
		$gListSqlJoin = ' JOIN article_vectors v ON v.article_id = a.id ';
		$lWhereArr[] = ' v.all_vector@@to_tsquery(\'english\', \'' . q(trim($gKfor->lFieldArr['content']['CurValue'])) . '\') ';
	}
	if( trim($gKfor->lFieldArr['title']['CurValue'])){
		$gListSqlJoin = ' JOIN article_vectors v ON v.article_id = a.id ';
		$lWhereArr[] = ' v.title_vector@@to_tsquery(\'english\', \'' . q(trim($gKfor->lFieldArr['title']['CurValue'])) . '\') ';
	}
}

if( $gKfor->lCurAction == 'journal_filter' && !$gKfor->lErrorCount){
	$lWhereArr[] = ' a.journal_id = ' . (int)$gKfor->lFieldArr['journal_id']['CurValue'] . ' ';
	$lWhereArr[] = ' f.article_journal_issue = ' . (int)$gKfor->lFieldArr['issue']['CurValue'] . ' ';
	$lPageSize = 0;
	$lCon = Con();
	$lSql = 'SELECT array_to_string(export_types, \',\') as exports FROM journals WHERE id = ' . (int)$gKfor->lFieldArr['journal_id']['CurValue'];
	$lCon->Execute($lSql);
	$lCon->MoveFirst();
	$lExports = explode(',', $lCon->mRs['exports']);	
}

if( count( $lWhereArr )){
	$gListSqlWhere = ' WHERE ' . implode(' AND ', $lWhereArr);
}

$lListAntets = array(
	1 => array('caption' => getstr('admin.articles.colID'), 'deforder' => 'desc', 'def'), 
	3 => array('caption' => getstr('admin.articles.colTitle'), 'deforder' => 'asc'), 
	2 => array('caption' => getstr('admin.articles.colAuthors'), 'deforder' => 'asc'), 	
	4 => array('caption' => getstr('admin.articles.colJournal'), 'deforder' => 'asc'), 
	5 => array('caption' => getstr('admin.articles.colIssue'), 'deforder' => 'asc'), 
	
);

asort($lExports);
foreach($lExports as $lExportType){
	$lExportType = (int)$lExportType;
	$lFieldPrefix = $lExportPrefixes[$lExportType]['prefix'];
	$lCaption = $lExportPrefixes[$lExportType]['title'];
	if( $gKfor->lCurAction == 'journal_filter' && !$gKfor->lErrorCount){
		$lJournalId = (int)$gKfor->lFieldArr['journal_id']['CurValue'];
		$lIssue = (int)$gKfor->lFieldArr['issue']['CurValue'];
		$lCon = Con();
		$lSql = 'SELECT coalesce(min(e.upload_started), 0) as uploaded, coalesce(min(e.generating_started), 0) as generated, coalesce(min(e.is_generated), 0) as generated_complete, count(*) as count
			FROM export_common e
			JOIN finalized_articles_exports fe ON fe.export_id = e.id AND fe.export_type_id = ' . (int)$lExportType . '
			JOIN finalized_articles fa ON fa.article_id = fe.article_id
			JOIN articles a ON a.id = fa.article_id
			WHERE a.journal_id = ' . (int)$lJournalId . ' AND fa.article_journal_issue = ' . (int)$lIssue;
		
		$lCon->Execute($lSql);
		$lCon->MoveFirst();
		$lAllExportsAreStartedToBeGenerated = (int)$lCon->mRs['generated'];
		$lAllExportsAreGeneratedSuccessfully = (int)$lCon->mRs['generated_complete'];
		$lAllExportsAreUploaded = (int)$lCon->mRs['uploaded'];
		$lExportsCount = (int)$lCon->mRs['count'];
		
		$lOnclick = '';
		$lClass = '';
		if($lAllExportsAreStartedToBeGenerated || !$lExportsCount){//Vsichki exporti sa generirani - nqma kakvo da pravim
			$lClass = 'disabledCtrlLink';
		}else{//Slagame link za generiraneto na vsi4ki exporti
			$lOnclick = 'GenerateExportXmls(' . (int)$lExportType . ', ' . (int)$lJournalId . ', ' . (int)$lIssue . ');';
		}
		$lCaption .= '<div class="exportLinks"><a href="#" onclick="' . $lOnclick . 'return false" class="' . $lClass . '">' . getstr('admin.finalized_articles.generateExportXmls') . '</a>';
		
		$lOnclick = '';
		$lClass = '';
		if($lAllExportsAreGeneratedSuccessfully && !$lAllExportsAreUploaded && $lExportsCount){//Slagame link za uploadvane na vsi4ki exporti 
			$lOnclick = 'UploadExports(' . (int)$lExportType . ', ' . (int)$lJournalId . ', ' . (int)$lIssue . ');';
		}else{//Ili vsi4ko e uploadnato ili ima negenerirani exporti - nishto ne pravim						
			$lClass = 'disabledCtrlLink';
		}
		$lCaption .= '<a href="#" onclick="' . $lOnclick . 'return false" class="' . $lClass . '">' . getstr('admin.finalized_articles.uploadExports') . '</a></div>';
	}
	$lListAntets[0 - $lExportType] = array('addtags' => ' class="exportLinksHolder" ','caption' => $lCaption, 'deforder' => 'asc');
	$lExportRows .= '<td>{_show' . $lFieldPrefix . 'ExportLinkIfExists}</td>';
}

$lListAntets[1000] = array('caption' => '  ', 'deforder' => 'asc');


$lListTpl = '
<tr>
	<td>{id}</td>
	<td><a href="./edit.php?tAction=showedit&id={id}">{article_title}</a></td>
	<td>{authors}</td>
	<td>{journal_name}</td>
	<td>{issue}</td>
	' . $lExportRows . '
	<td align="right" nowrap="true">
			<a href="./edit.php?tAction=showedit&id={id}"><img src="/img/edit.gif" alt="' . getstr('admin.editLabel') . '" title="' . getstr('admin.editLabel') . '" border="0" /></a>			
			<a href="javascript:openw(\'/resources/articles/match.php?id={id}\', \'aa\', \'location=no,menubar=yes,width=1200,height=770,scrollbars=yes,resizable=no,top=0,left=0\')">Match</a>			
			<a href="javascript:if (confirm(\'' . getstr('admin.articles.confirmDel') . '\')) { window.location = \'/finalized_articles/edit.php?id={id}&tAction=delete\';} else {}">
				<img src="/img/trash2.gif" alt="' . getstr('admin.deleteButton') . '" title="' . getstr('admin.deleteButton') . '" border="0" />
	</td>
</tr>
';




$lTableHeader = '
	<div class="t">
	<div class="b">
	<div class="l">
	<div class="r">
		<div class="bl">
		<div class="br">
		<div class="tl">
		<div class="tr">
			<table cellspacing="0" cellpadding="5" border="0" class="gridtable  exportLinksTable">
				<tr>
					<th class="gridtools" colspan="10">						
						' . getstr('admin.articles.antetka') . '
					</th>
				</tr>
';

$lTableFooter = '
			</table>
		</div>
		</div>
		</div>
		</div>
	</div>
	</div>
	</div>
	</div>
	<div class="unfloat"></div>
';

$lListSql = 'SELECT a.id, f.article_authors as authors, f.article_title, j.name as journal_name, f.article_journal_issue as issue,
	eo.id as eol_export_id, eo.is_generated as eol_is_generated, eo.has_results as eol_has_results, eo.is_uploaded as eol_is_uploaded, eo.upload_has_errors as eol_upload_has_errors, \'/resources/exports/eol_export/edit.php?tAction=showedit&id=\' || eo.id as eol_link, \'Eol\' as eol_link_title,
	wo.id as wiki_export_id, wo.is_generated as wiki_is_generated, wo.has_results as wiki_has_results, wo.is_uploaded as wiki_is_uploaded, wo.upload_has_errors as wiki_upload_has_errors, \'/resources/exports/wiki_export/edit.php?tAction=showedit&id=\' || wo.id as wiki_link, \'Wiki\' as wiki_link_title,
	ko.id as keys_export_id, ko.is_generated as keys_is_generated, ko.has_results as keys_has_results, ko.is_uploaded as keys_is_uploaded, ko.upload_has_errors as keys_upload_has_errors, \'/resources/exports/keys_export/edit.php?tAction=showedit&id=\' || ko.id as keys_link, \'Keys\' as keys_link_title
FROM articles a
JOIN finalized_articles f ON f.article_id = a.id
JOIN journals j ON j.id = a.journal_id
LEFT JOIN finalized_articles_exports ee ON ee.article_id = a.id AND ee.export_type_id = ' . EOL_EXPORT_TYPE . '
LEFT JOIN eol_export eo ON eo.id = ee.export_id
LEFT JOIN finalized_articles_exports we ON we.article_id = a.id AND we.export_type_id = ' . WIKI_EXPORT_TYPE . '
LEFT JOIN wiki_export wo ON wo.id = we.export_id
LEFT JOIN finalized_articles_exports ke ON ke.article_id = a.id AND ke.export_type_id = ' . KEYS_EXPORT_TYPE . '
LEFT JOIN keys_export ko ON ko.id = ke.export_id
' . $gListSqlJoin . $gListSqlWhere;



$lList = new DBList($lTableHeader);
$lList->SetCloseTag($lTableFooter);
$lList->SetTemplate($lListTpl);
$lList->SetPageSize($lPageSize);
$lList->SetOrderParams((int)$_GET['ordby'], (int)$_GET['ordd']);
$lList->SetAntet($lListAntets);
$lList->SetQuery($lListSql);



if (!$lList->DisplayList($gListPage)) {
	echo $lTableHeader . '<tr><td colspan="8"><p align="center"><b>' . getstr('admin.articles.noData') . '</b></p></td></tr>' . $lTableFooter;
}

HtmlEnd();
?>