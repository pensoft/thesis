<?php
$docroot = getenv("DOCUMENT_ROOT");
require_once($docroot . '/lib/static.php');

$guid = (int) $_GET['guid'];
$sid = (int)$_GET['sid'];
$gPropType = 3;

$t = array(
	"guid" => array(
		"VType" => "int",
		"CType" => "hidden",
		"DisplayName" => "",
		"AllowNulls" => true,
	),
	"deleterelstory" => array(
		"CType" => "action",
		"DisplayName" => "Изтрии",
		"SQL" => 'SELECT * FROM DelStoryFromStory({guid}, ' . (int)$_GET['relstoryid'] . ', ' . $gPropType . ')',
		"ActionMask" => ACTION_CHECK | ACTION_EXEC | ACTION_FETCH | ACTION_REDIRECT,
		"RedirUrl" => '/stories/relstories.php?guid={guid}',
		"Hidden" => true,
	),
);
$h = '{guid}';
$f = new kfor($t, $h, "POST");
$f->Display();

HtmlStart(1);

$t = '<tr>
		<td valign="top"><a href="{_getStoryEditLink}" target="_parent">{title}</a></td>
		<td valign="top">{author}</td>
		<td valign="top">{pubdate}</td>
		<td align="right" valign="top">
			<a href="javascript:if (confirm(\'Потвърдете изтриването...\')) { window.location = \'/resources/stories/relstories.php?tAction=deleterelstory&relstoryid={relstoryid}&guid=' . $guid . '\';} else {}">
				<img src="/img/trash2.gif" alt="Изтрий" title="Изтрий" border="0" />
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

//~ if (in_array($sid,array(2,6,7))) {
	//~ $gTableTitle = 'Свързани статии';
//~ } else {
	//~ $gTableTitle = 'Още по темата';
//~ }
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
						<a href="javascript:openw(\'/resources/stories/index.php?relstoryid=' . $guid . '&mode=rel\', \'aa\', \'location=no,menubar=yes,width=1000,height=700,scrollbars=yes,resizable=yes,top=0,left=0\')"><b>Добави статия</b></a>
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

$gSqlStr =  'SELECT s.title, s.author, s.pubdate, sp.valint as relstoryid, s.primarysite
			FROM storyproperties sp INNER JOIN stories s on (sp.valint = s.guid) 
			WHERE sp.guid = ' . $guid . ' AND sp.propid = 3';

$l = new DBList($lTableHeader);
$l->SetCloseTag($lTableFooter);
$l->SetTemplate($t);
$l->SetOrderParams((int)$_GET['ordby'], (int)$_GET['ordd']);
$l->SetAntet($gFArr);
$l->SetQuery($gSqlStr);

if (!$l->DisplayList($page)) {
	echo $lTableHeader . '<tr><td colspan="4"><p align="center"><b>' . getstr('admin.stories.relStoriesNoData') . '</b></p></td></tr>' . $lTableFooter;
}

HtmlEnd(1);

function getStoryEditLink($pRs) {
	if ($pRs['primarysite'] == 8) {
		$link = '/blog/edit.php?tAction=show&guid='. $pRs['relstoryid'];
	} else {
		$link = '/resources/stories/edit.php?tAction=showedit&guid='. $pRs['relstoryid'];
	}
	return $link;
}

?>