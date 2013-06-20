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

$fld = array(
	'guid' => array(
		'VType' => 'int',
		'CType' => 'hidden',
		'DisplayName' => '',
		'AllowNulls' => true,
	),
	
	'mode' => array(
		'VType' => 'string',
		'CType' => 'hidden',
		'DisplayName' => '',
		'AllowNulls' => true,
	),
	
	'relstoryid' => array(
		'VType' => 'int',
		'CType' => 'hidden',
		'DisplayName' => '',
		'AllowNulls' => true,
	),
	
	'stext' => array(
		'VType' => 'string',
		'CType' => 'text',
		'DisplayName' => getstr('admin.stories.keyword'),
		'AddTags' => array(
			'class' => 'coolinp',
		),
		'AllowNulls' => true,
	),
	
	'storytype' => array(
		'VType' => 'int',
		'CType' => 'select',
		'SrcValues' => array(
			null => '--', 
			-1 => 'Статия', 
			2 => 'Продукт', 
			3 => 'Справочник'
		),
		'DisplayName' => getstr('admin.stories.type'),
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
		'DisplayName' => getstr('admin.stories.creator'),
		'AddTags' => array(
			'class' => 'coolinp',
		),
		'AllowNulls' => true,
	),
	
	'rubrid' => array(
		'VType' => 'int',
		'CType' => 'select',
		'SrcValues' => 'SELECT null AS id, \'--\' as name, \'\' as pos, 0 as ord
				UNION 
			SELECT *, 1 as ord FROM (
				SELECT id, case when id = rootnode then name[1] else repeat(\'&nbsp;\', length(pos)) || \'- \' || name[1] end as name, pos
				FROM rubr WHERE sid = 1 order by rootnode, (case when id = rootnode then 0 else 1 end), pos 
			) a 
			ORDER BY ord, pos	
		',
		'DisplayName' => getstr('admin.stories.mainRubric'),
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

$h = '{guid}{mode}{relstoryid}
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
					<th colspan="2">' . getstr('admin.stories.filter') . '</th>
				</tr>
				<tr>
					<td>{*stext}<br/>{stext}</td>
					<td>{*rubrid}<br/>{rubrid}</td>
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

$kfor = new kfor($fld, $h, 'GET');
$kfor->debug = false;
echo $kfor->Display();

$warr = array();
if ((int)$pRelStoryId) {
	$warr[] = 's.guid <> ' . (int)$pRelStoryId;
}

$join = '';

$warr[] = 's.storytype IS NULL';
if ($kfor->lCurAction == 'show') {
	
	if ((int)$kfor->lFieldArr['createuid']['CurValue']) {
		$warr[] = 's.createuid = ' . (int)$kfor->lFieldArr['createuid']['CurValue'];
	}
	
	if ((int)$kfor->lFieldArr['rubrid']['CurValue']) {
		$warr[] = 'r.id = ' . (int)$kfor->lFieldArr['rubrid']['CurValue'];
	}
	
	if ($kfor->lFieldArr['stext']['CurValue']) {
		$join = ' JOIN storiesft ft USING(guid) ';
		$warr[] = BuildT2SearchClause($kfor->lFieldArr['stext']['CurValue'], 'bg_utf8', array('s.title', 's.description', 's.nadzaglavie', 's.subtitle', 's.author'), array('ft.body'));
	}
}

$t = '
<tr>
	<td>{guid}</td>
	<td><a href="/resources/stories/edit.php?tAction=showedit&guid={guid}">{title}</a></td>
	<td>{uname}</td>
	<td>{author}</td>
	<td nowrap="true">{pubdate}</td>
	<td nowrap="true">{_GetStoryStaus(state)}</td>
	<td>{rubrname}</td>
	<td align="right" nowrap="true">
		' . ((int)$lHide ? '
			<a href="/resources/stories/index.php?relstoryid=' . $pRelStoryId .'&guid={guid}&add=1"><img src="/img/add.gif" alt="Свържи" title="Свържи" border="0" /></a>
		' : '
			<a href="/resources/stories/extra.php?guid={guid}&title={title}"><img src="/img/gear.gif" alt="Свързани елементи" title="Свързани елементи" border="0" /></a>
			<a href="/resources/stories/edit.php?tAction=showedit&guid={guid}"><img src="/img/edit.gif" alt="Редактирай" title="Редактирай" border="0" /></a>
			<a href="/resources/stories/edit.php?tAction=delete&guid={guid}" onclick="javascript: if (confirm(\'Сигурни ли сте, че искате да изтриете тази статия?\')) { return true; } else { return false;}"><img src="/img/trash2.gif" alt="Изтрий" title="Изтрий" border="0" /></a>
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
	6 => array('caption' => getstr('admin.stories.colMainRubric'), 'deforder' => 'asc'), 
	1000 => array('caption' => '  ', 'deforder' => 'asc'),
);

$lAddStoryUrl = ($_GET['mode'] == 'rel' ? './edit.php?relstoryid=' . $pRelStoryId . '&mode=rel' : './edit.php');

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
						<a href="' . $lAddStoryUrl . '">' . getstr('admin.stories.addStory') . '</a>
						' . getstr('admin.stories.antetka') . '
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
$l->SetQuery('SELECT s.guid, s.title, s.author, s.pubdate, s.state, '.getsqlang("r.name").' as rubrname, usr.uname 
	FROM stories s 
	' . $join . ' 
	JOIN usr ON usr.id = s.createuid 
	LEFT JOIN storyproperties sp ON sp.guid = s.guid AND sp.propid = 4 
	LEFT JOIN rubr r ON sp.valint = r.id 
	' . (count($warr) ? ' WHERE ' . implode(' AND ', $warr) : ''));



if (!$l->DisplayList($page)) {
	echo $lTableHeader . '<tr><td colspan="8"><p align="center"><b>' . getstr('admin.stories.noData') . '</b></p></td></tr>' . $lTableFooter;
}

HtmlEnd($lHide);
?>