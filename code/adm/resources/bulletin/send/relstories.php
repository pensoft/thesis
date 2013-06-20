<?php
$docroot = getenv("DOCUMENT_ROOT");
require_once($docroot . '/lib/static.php');

$guid = (int) $_GET['guid'];
$sid = (int)$_GET['sid'];
$gPropType = 3;

$t = array(
	'guid' => array(
		'VType' => 'int',
		'CType' => 'hidden',
		'DisplayName' => '',
		'AllowNulls' => true,
	),
	'deleterelstory' => array(
		'CType' => 'action',
		'DisplayName' => getstr('admin.deleteButton'),
		'SQL' => 'SELECT * FROM DelStoryFromStory({guid}, ' . (int)$_GET['relstoryid'] . ', ' . $gPropType . ')',
		'ActionMask' => ACTION_CHECK | ACTION_EXEC | ACTION_FETCH | ACTION_REDIRECT,
		'RedirUrl' => './relstories.php?guid={guid}',
		'Hidden' => true,
	),
);
$h = '{guid}';
$f = new kfor($t, $h, 'POST');
$f->Display();

HtmlStart(1);

$t = '<tr>
		<td valign="top"><a href="/resources/stories/edit.php?tAction=showedit&guid={relstoryid}" target="_parent">{title}</a></td>
		<td valign="top">{author}</td>
		<td valign="top">{pubdate}</td>
		<td align="right" valign="top">
			<a href="javascript:if (confirm(\'' . getstr('admin.stories.relStoriesConfirmDel') . '\')) { window.location = \'./relstories.php?tAction=deleterelstory&relstoryid={relstoryid}&guid=' . $guid . '\';} else {}">
				<img src="/img/trash2.gif" alt="' . getstr('admin.deleteButton') . '" title="' . getstr('admin.deleteButton') . '" border="0" />
			</a> 
		</td>
	</tr>
';

$gFArr = array(
	1 => array('caption' => getstr('admin.stories.relStoriesTitle'), 'def', 'deforder' => 'asc'), 
	2 => array('caption' => getstr('admin.stories.relStoriesAuthor'), 'deforder' => 'asc'), 
	3 => array('caption' => getstr('admin.stories.relStoriesPubDate'), 'deforder' => 'asc'), 
	1000 => array('caption' => '  ', 'deforder' => 'asc'),
);

$lTableHeader = '
	<a name="stories"></a>
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
					<th class="gridtools" colspan="4">
						<a href="javascript:openw(\'/resources/stories/index.php?relstoryid=' . $guid . '&mode=rel\', \'aa\', \'location=no,menubar=yes,width=1000,height=700,scrollbars=yes,resizable=yes,top=0,left=0\')">' . getstr('admin.stories.addRelStory') . '</a>
						' . getstr('admin.stories.relStories') . '
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
$l->SetOrderParams((int)$_GET['ordby'], (int)$_GET['ordd']);
$l->SetAntet($gFArr);
$l->SetQuery('SELECT s.title, s.author, s.pubdate, sp.valint as relstoryid, s.primarysite
	FROM storyproperties sp INNER JOIN stories s on (sp.valint = s.guid) 
	WHERE sp.guid = ' . $guid . ' AND sp.propid = 3'
);

if (!$l->DisplayList($page)) {
	echo $lTableHeader . '<tr><td colspan="4"><p align="center"><b>' . getstr('admin.stories.relStoriesNoData') . '</b></p></td></tr>' . $lTableFooter;
}

HtmlEnd(1);

?>