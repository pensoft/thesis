<?php
$docroot = getenv("DOCUMENT_ROOT");
require_once($docroot . '/lib/static.php');
$historypagetype = HISTORY_CLEAR;


$gListPage = (int)$_GET['p'];
$gArticleId = (int) $_GET['article_id'];

HtmlStart();

$lCon = Con();
$lSql = 'SELECT id, title FROM articles WHERE id = ' . (int) $gArticleId;
$lCon->Execute($lSql);
$lCon->MoveFirst();
$gArticleId = (int) $lCon->mRs['id'];
$gArticleTitle = $lCon->mRs['title'];

if( !$gArticleId ){
	header('Location: /resources/articles/');
	exit;
}

$lListTpl = '
<tr>	
	<td><a href="./version_edit.php?tAction=showedit&id={id}">{version}</a></td>
	<td>{createdate}</td>		
	<td align="right" nowrap="true">
		<a href="./version_edit.php?tAction=showedit&id={id}"><img src="/img/gridedit.gif" alt="' . getstr('admin.viewLabel') . '" title="' . getstr('admin.viewLabel') . '" border="0" /></a>						
	</td>
</tr>
';

$lListAntets = array(
	1 => array('caption' => getstr('admin.article_versions.colVersion'), 'deforder' => 'desc', 'def'), 	
	2 => array('caption' => getstr('admin.article_versions.colCreateDate'), 'deforder' => 'asc'), 
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
						' . getstr('admin.article_versions.antetka') . '<a class="unfloat" href="/resources/articles/edit.php?id=' . (int) $gArticleId . '&tAction=showedit">' . $gArticleTitle . '</a>
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

$lListSql = 'SELECT v.id, v.version, date_trunc(\'seconds\', v.createdate) as createdate
FROM article_versions v
JOIN articles a ON a.id = v.article_id
WHERE a.id = ' . (int) $gArticleId;

$lList = new DBList($lTableHeader);
$lList->SetCloseTag($lTableFooter);
$lList->SetTemplate($lListTpl);
$lList->SetPageSize(30);
$lList->SetOrderParams((int)$_GET['ordby'], (int)$_GET['ordd']);
$lList->SetAntet($lListAntets);
$lList->SetQuery($lListSql);



if (!$lList->DisplayList($gListPage)) {
	echo $lTableHeader . '<tr><td colspan="8"><p align="center"><b>' . getstr('admin.article_versions.noData') . '</b></p></td></tr>' . $lTableFooter;
}

HtmlEnd();
?>