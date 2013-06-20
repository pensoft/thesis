<?php
$docroot = getenv("DOCUMENT_ROOT");
require_once($docroot . '/lib/static.php');
$historypagetype = HISTORY_CLEAR;

$lHide = 0;
if ($_GET['mode'] == 'rel' || (int)$_GET['add']) {
	$lHide = 1;
}

$pRelStoryId = (int)$_GET['relstoryid'];
$guid = (int)$_GET['guid'];

$page = (int)$_GET['p'];
$warr[] = 's.storytype=8';
HtmlStart($lHide);

$t = '
<tr>
	<td>{guid}</td>
	<td><a href="/resources/bulletin/send/edit.php?tAction=showedit&guid={guid}">{title}</a></td>
	<td align="right" nowrap="true">
		<a href="/resources/bulletin/send/edit.php?tAction=showedit&guid={guid}"><img src="/img/edit.gif" alt="Редактирай" title="Редактирай" border="0" /></a>
	</td>
</tr>
';

$gFArr = array(
	1 => array('caption' => 'ID', 'def', 'deforder' => 'asc'), 
	2 => array('caption' => 'Заглавие', 'deforder' => 'asc'), 
	1000 => array('caption' => '  ', 'deforder' => 'asc'),
);

$lAddStoryUrl = ($_GET['mode'] == 'rel' ? './edit.php?tAction=showedit&relstoryid=' . $pRelStoryId . '&mode=rel' : './edit.php?tAction=showedit');

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
					<th class="gridtools" colspan="8">
					<a href="' . $lAddStoryUrl . '">Добавяне на бюлетин</a>
						'.getstr('admin.bulletin.bulletintittle').'
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

$l = new DBList($lTableHeader);
$l->SetCloseTag($lTableFooter);
$l->SetTemplate($t);
$l->SetPageSize(30);
$l->SetOrderParams((int)$_GET['ordby'], (int)$_GET['ordd']);
$l->SetAntet($gFArr);
$l->SetQuery('SELECT s.guid, s.title, s.pubdate, usr.uname
	FROM stories s 
	JOIN usr ON usr.id = s.createuid 
	' . (count($warr) ? ' WHERE ' . implode(' AND ', $warr) : ''));

if (!$l->DisplayList($page)) {
	echo $lTableHeader . '<tr><td colspan="8"><p align="center"><b>' . getstr('admin.stories.noData') . '</b></p></td></tr>' . $lTableFooter;
}

HtmlEnd($lHide);

function removeDoubleQuotes($pTitle){
	return str_replace("\"", "'", $pTitle);
}
?>