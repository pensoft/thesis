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
	
	
	
	'createuid' => array(
		'VType' => 'int',
		'CType' => 'select',
		'SrcValues' => 'SELECT null as id, \'--\' as name, 0 as ord 
				UNION 
			SELECT id, uname as name, 1 as ord FROM usr 
			ORDER BY ord, name
		',
		'DisplayName' => getstr('admin.articles.creator'),
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
					<th colspan="2">' . getstr('admin.articles.filter') . '</th>
				</tr>
				<tr>
					<td>{*title}<br/>{title}</td>
					<td>{*content}<br/>{content}</td>
				</tr>
				<tr>
					<td>{*createuid}<br/>{createuid}</td>
					
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
	if( trim($gKfor->lFieldArr['content']['CurValue'])){
		$gListSqlJoin = ' JOIN article_vectors v ON v.article_id = a.id ';
		$lWhereArr[] = ' v.all_vector@@to_tsquery(\'english\', \'' . q(trim($gKfor->lFieldArr['content']['CurValue'])) . '\') ';
	}
	if( trim($gKfor->lFieldArr['title']['CurValue'])){
		$gListSqlJoin = ' JOIN article_vectors v ON v.article_id = a.id ';
		$lWhereArr[] = ' v.title_vector@@to_tsquery(\'english\', \'' . q(trim($gKfor->lFieldArr['title']['CurValue'])) . '\') ';
	}

	if( (int)$gKfor->lFieldArr['createuid']['CurValue']){	
		$lWhereArr[] = ' a.createuid = ' . (int)$gKfor->lFieldArr['createuid']['CurValue'];
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
	<td align="center"><a href="#" onclick="DisplayArticleFinalizationForm({id});return false">' . getstr('admin.articles.finalizeArticle'). '</a></td>
	<td align="right" nowrap="true">
			<a href="./edit.php?tAction=showedit&id={id}"><img src="/img/edit.gif" alt="' . getstr('admin.editLabel') . '" title="' . getstr('admin.editLabel') . '" border="0" /></a>			
			<a href="javascript:openw(\'./match.php?id={id}\', \'aa\', \'location=no,menubar=yes,width=1200,height=770,scrollbars=yes,resizable=no,top=0,left=0\')">Match</a>			
			<a href="javascript:if (confirm(\'' . getstr('admin.articles.confirmDel') . '\')) { window.location = \'/resources/articles/edit.php?id={id}&tAction=delete\';} else {}">
				<img src="/img/trash2.gif" alt="' . getstr('admin.deleteButton') . '" title="' . getstr('admin.deleteButton') . '" border="0" />
	</td>
</tr>
';

$lListAntets = array(
	1 => array('caption' => getstr('admin.articles.colID'), 'deforder' => 'desc', 'def'), 
	2 => array('caption' => getstr('admin.articles.colTitle'), 'deforder' => 'asc'), 
	3 => array('caption' => getstr('admin.articles.colCreateDate'), 'deforder' => 'asc'), 
	4 => array('caption' => getstr('admin.articles.colLastMod'), 'deforder' => 'asc'), 
	5 => array('caption' => getstr('admin.articles.colAuthor'), 'deforder' => 'asc'), 	
	1001 => array('caption' => '  ', 'deforder' => 'asc'),
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
						<a href="./edit.php">' . getstr('admin.articles.addArticle') . '</a>
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

if($gListSqlWhere){
	$gListSqlWhere .= ' AND a.is_finalized = 0 ';
}else{
	$gListSqlWhere = ' WHERE a.is_finalized = 0 ';
}

$lListSql = 'SELECT a.id, date_trunc(\'seconds\', a.createdate) as createdate, date_trunc(\'seconds\', a.lastmod) as lastmod, a.title, a.author, u.name as username
FROM articles a
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
	echo $lTableHeader . '<tr><td colspan="7"><p align="center"><b>' . getstr('admin.articles.noData') . '</b></p></td></tr>' . $lTableFooter;
}

HtmlEnd();
?>