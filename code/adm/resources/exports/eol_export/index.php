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
		'DisplayName' => getstr('admin.eol_export.colTitle'),
		'AddTags' => array(
			'class' => 'coolinp',
		),
		'AllowNulls' => true,
	),
	
	'article_id' => array(
		'VType' => 'int',
		'CType' => 'select',
		'DisplayName' => getstr('admin.eol_export.colArticleId'),
		'SrcValues' => 'SELECT null as id, \'--\' as name UNION SELECT id, id || \' -- \' || title as name FROM articles ORDER BY id DESC',
		'AddTags' => array(
			'class' => 'coolinp',
		),
		'AllowNulls' => true,
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
					<th colspan="2">' . getstr('admin.eol_export.filter') . '</th>
				</tr>
				<tr>
					<td>{*title}<br/>{title}</td>
					<td>{*article_id}<br/>{article_id}</td>
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
echo $gKfor->Display();

$lWhereArr = array();


if( $gKfor->lCurAction == 'show' && !$gKfor->lErrorCount){
	if( trim($gKfor->lFieldArr['title']['CurValue'])){		
		$lWhereArr[] = ' to_tsvector(a.title, \'english\')@@to_tsquery(\'english\', \'' . q(trim($gKfor->lFieldArr['title']['CurValue'])) . '\') ';
	}

	if( (int)$gKfor->lFieldArr['article_id']['CurValue']){	
		$lWhereArr[] = ' a.article_id = ' . (int)$gKfor->lFieldArr['article_id']['CurValue'];
	}

	if( count( $lWhereArr )){
		$gListSqlWhere = ' WHERE ' . implode(' AND ', $lWhereArr);
	}


}



$lListTpl = '
<tr>
	<td>{id}</td>
	<td><a href="./edit.php?tAction=showedit&id={id}">{title}</a></td>
	<td>{createdate}</td>
	<td>{lastmod}</td>
	<td>{author}</td>	
	<td align="right" nowrap="true">
			<a href="/resources/exports/eol_export/edit.php?tAction=showedit&id={id}"><img src="/img/edit.gif" alt="' . getstr('admin.editLabel') . '" title="' . getstr('admin.editLabel') . '" border="0" /></a>						
			<a href="javascript:if (confirm(\'' . getstr('admin.eol_export.confirmDel') . '\')) { window.location = \'/resources/exports/eol_export/edit.php?id={id}&tAction=delete\';} else {}">
				<img src="/img/trash2.gif" alt="' . getstr('admin.deleteButton') . '" title="' . getstr('admin.deleteButton') . '" border="0" />
	</td>
</tr>
';

$lListAntets = array(
	1 => array('caption' => getstr('admin.eol_export.colID'), 'deforder' => 'desc', 'def'), 
	2 => array('caption' => getstr('admin.eol_export.colTitle'), 'deforder' => 'asc'), 
	3 => array('caption' => getstr('admin.eol_export.colCreateDate'), 'deforder' => 'asc'), 
	4 => array('caption' => getstr('admin.eol_export.colLastMod'), 'deforder' => 'asc'), 
	5 => array('caption' => getstr('admin.eol_export.colAuthor'), 'deforder' => 'asc'), 	
	1000 => array('caption' => '  ', 'deforder' => 'asc'),
);


$lTableHeader = '
	<div class="t">
	<div class="b">
	<div class="l">
	<div class="r">
		<div class="bl">
		<div class="br">
		<div class="tl">
		<div class="tr">
			<table cellspacing="0" cellpadding="5" border="0" class="gridtable">
				<tr>
					<th class="gridtools" colspan="10">
						<a href="/resources/exports/eol_export/edit.php">' . getstr('admin.eol_export.addLabel') . '</a>
						' . getstr('admin.eol_export.antetka') . '
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
';

$lListSql = 'SELECT a.id, date_trunc(\'seconds\', a.createdate) as createdate, date_trunc(\'seconds\', a.lastmod) as lastmod, a.title, u.name as author
FROM eol_export a
JOIN usr u ON u.id = a.createuid
' . $gListSqlJoin . $gListSqlWhere;

$lList = new DBList($lTableHeader);
$lList->SetCloseTag($lTableFooter);
$lList->SetTemplate($lListTpl);
$lList->SetPageSize(30);
$lList->SetOrderParams((int)$_GET['ordby'], (int)$_GET['ordd']);
$lList->SetAntet($lListAntets);
$lList->SetQuery($lListSql);



if (!$lList->DisplayList($gListPage)) {
	echo $lTableHeader . '<tr><td colspan="8"><p align="center"><b>' . getstr('admin.eol_export.noData') . '</b></p></td></tr>' . $lTableFooter;
}

HtmlEnd();
?>