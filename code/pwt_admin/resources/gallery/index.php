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

HtmlStart($lHide);

$warr[] = ' s.storytype = 1 ';

$fld = array(
	'fromdate' => array(
		'CType' => 'text',
		'VType' => 'date',
		'DateType' => DATE_TYPE_ALL,
		'DisplayName' => 'От дата',
		'AllowNulls' => true,
		'AddTags' => array(
			'class' => 'coolinp',
		),
	),
	'todate' => array(
		'CType' => 'text',
		'VType' => 'date',
		'DateType' => DATE_TYPE_ALL,
		'DisplayName' => 'До дата',
		'AllowNulls' => true,
		'AddTags' => array(
			'class' => 'coolinp',
		),
	),
	'title' => array(
		'CType' => 'text',
		'VType' => 'string',
		'DisplayName' => 'Заглавие',
		'AllowNulls' => true,
		'AddTags' => array(
			'class' => 'coolinp',
		),
	),
	'author' => array(
		'CType' => 'text',
		'VType' => 'string',
		'DisplayName' => 'Автор',
		'AllowNulls' => true,
		'AddTags' => array(
			'class' => 'coolinp',
		),
	),	
	'state' => array (
		'VType' => 'int' ,
		'CType' => 'select' ,
		'SrcValues' =>	array (
			0 => '---',
			1 => 'Пишеща се',
			2 => 'За коректор',
			3 => 'За редактор',
			4 => 'Публикувана',
		),
		'AddTags' => array(
			'class' => 'coolinp',
		),
		'DisplayName' => 'Статус',
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

$frm = '
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
					<col width="33%"/>
					<col width="33%"/>
					<col width="33%"/>
				</colgroup>
				<tr>
					<th colspan="3">' . getstr('admin.issues.filter') . '</th>
				</tr>
				<tr>
					<td>{*title}<br/>{title}</td>
					<td>{*author}<br/>{author}</td>
					<td>{*state}<br/>{state}</td>
				</tr>
				<tr>
					<td>{*fromdate}<br/>{fromdate}
						<a href="#" onclick="jscalshow(this, \'def1\', \'fromdate\'); return false;"><img src="/img/calico.gif" alt="Въведи дата" title="Въведи дата" border="0"/></a></td>
					<td>{*todate}<br/>{todate}
						<a href="#" onclick="jscalshow(this, \'def1\', \'todate\'); return false;"><img src="/img/calico.gif" alt="Въведи дата" title="Въведи дата" border="0"/></a>
					</td>
					<td>&nbsp;</td>
				</tr>
				<tr>
					<td colspan="3" align="right">{show}</td>
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

$kfor = new kfor($fld, $frm, 'GET');
echo $kfor->Display();

if ($kfor->lCurAction == 'show') {
	if (!is_null($kfor->lFieldArr['title']['CurValue']) && $kfor->lFieldArr['title']['CurValue'])
		$warr[] = ' s.title LIKE \'%' .  $kfor->lFieldArr['title']['CurValue'] . '%\'';
	if (!is_null($kfor->lFieldArr['author']['CurValue']) && $kfor->lFieldArr['author']['CurValue'])
		$warr[] = ' s.author=\'' .  $kfor->lFieldArr['author']['CurValue'] . '\'';
	if (!is_null($kfor->lFieldArr['state']['CurValue']) && $kfor->lFieldArr['state']['CurValue'])
		$warr[] = ' s.state = ' . ((int)$kfor->lFieldArr['state']['CurValue']-1);
	if(!is_null($kfor->lFieldArr['fromdate']['CurValue']) && $kfor->lFieldArr['fromdate']['CurValue'])
		$warr[] = ' s.pubdate>\'' . $kfor->lFieldArr['fromdate']['CurValue'] . '\'::date';
	if(!is_null($kfor->lFieldArr['todate']['CurValue']) && $kfor->lFieldArr['todate']['CurValue'])
		$warr[] = ' s.pubdate<\'' . $kfor->lFieldArr['todate']['CurValue'] . '\'::date';
}

if ((int)$_GET['add']) {
	if ((int)$pRelStoryId == (int)$guid) {
		echo '<p style="color:red;">Свързването на статия сама със себе си не е възможно.';
	} else {
		$gSqlStr = 'SELECT * FROM AddStoryToStory(' . (int)$pRelStoryId . ', ' . (int)$guid . ', 3)';
		$gCon = Con();
		$gCon->Execute($gSqlStr);
		echo '
			<script>
				window.opener.location.hash = "#snimki";
				window.opener.location.reload();
				window.location = document.referrer;
			</script>
		';
	}	
}


$t = '
<tr>
	<td>{guid}</td>
	<td><a href="/resources/gallery/edit.php?tAction=showedit&guid={guid}">{title}</a></td>
	<td>{uname}</td>
	<td>{author}</td>
	<td nowrap="true">{pubdate}</td>
	<td nowrap="true">{_GetStoryStaus(state)}</td>
	<td align="right" nowrap="true">
		' . ((int)$lHide ? '
			<a href="/resources/stories/index.php?relstoryid=' . $pRelStoryId .'&guid={guid}&add=1"><img src="/img/add.gif" alt="Свържи" title="Свържи" border="0" /></a>
		' : '
			<a href="/resources/gallery/edit.php?tAction=showedit&guid={guid}"><img src="/img/edit.gif" alt="Редактирай" title="Редактирай" border="0" /></a>
			<a href="/resources/gallery/edit.php?tAction=delete&guid={guid}" onclick="javascript: if (confirm(\'Сигурни ли сте, че искате да изтриете тази галерия?\')) { return true; } else { return false;}"><img src="/img/trash2.gif" alt="Изтрий" title="Изтрий" border="0" /></a>
		') . ' 
	</td>
</tr>
';

$gFArr = array(
	1 => array('caption' => getstr('admin.stories.colID'), 'deforder' => 'asc'), 
	2 => array('caption' => getstr('admin.stories.colTitle'), 'deforder' => 'asc'), 
	7 => array('caption' => getstr('admin.stories.colCreator'), 'deforder' => 'asc'), 
	3 => array('caption' => getstr('admin.stories.colAuthor'), 'deforder' => 'asc'), 
	4 => array('caption' => getstr('admin.stories.colPublished'), 'def', 'deforder' => 'desc'), 
	5 => array('caption' => getstr('admin.stories.colState'), 'deforder' => 'asc'),
	1000 => array('caption' => '  ', 'deforder' => 'asc'),
);

$lAddStoryUrl = ($_GET['mode'] == 'rel' ? './edit.php?relstoryid=' . $pRelStoryId . '&mode=rel' : './edit.php?tAction=showedit');

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
						<a href="' . $lAddStoryUrl . '">' . getstr('admin.gallery.addStory') . '</a>
						' . getstr('admin.gallery.antetka') . '
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
$l->SetQuery('SELECT s.guid, s.title, s.author, s.pubdate, s.state, usr.uname 
	FROM stories s 
	' . $join . ' 
	JOIN usr ON usr.id = s.createuid 
	' . (count($warr) ? ' WHERE ' . implode(' AND ', $warr) : ''));

if (!$l->DisplayList($page)) {
	echo $lTableHeader . '<tr><td colspan="8"><p align="center"><b>' . getstr('admin.gallery.noData') . '</b></p></td></tr>' . $lTableFooter;
}

HtmlEnd($lHide);
?>