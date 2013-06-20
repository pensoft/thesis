<?php
$docroot = getenv('DOCUMENT_ROOT');
require_once($docroot . '/lib/static.php');

$historypagetype = HISTORY_CLEAR;

HtmlStart();

$page = (int)$_GET['p'];

$gFArr = array(
	1 => array('caption' => getstr('admin.staticpages.colStoryID'), 'deforder' => 'desc'), 
	3 => array('caption' => getstr('admin.staticpages.colStoryTitle'), 'deforder' => 'asc'), 
	2 => array('caption' => getstr('admin.staticpages.colSPName'), 'deforder' => 'asc', 'def'), 
	1000 => array('caption' => '  ', 'deforder' => 'desc'),
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
					<th class="gridtools" colspan="6">
						<a href="./edit.php">' . getstr('admin.staticpages.addSP') . '</a>
						' . getstr('admin.staticpages.antetka') . '
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

$t = '<tr>
	<td><a href="/resources/stories/edit.php?tAction=showedit&guid={artid}" target="_self">{artid}</a></td>
	<td>{title}</td>
	<td>{artname}</td>
	<td align="right"><a href="./edit.php?staticid={static_id}&tAction=show"><img src="/img/edit.gif" border="0" alt="Редактирай" title="Редактирай" /></a></td>
</tr>';

$l = new DBList($lTableHeader);
$l->SetCloseTag($lTableFooter);
$l->SetTemplate($t);
$l->SetPageSize(30);
$l->SetOrderParams((int)$_GET['ob1'], (int)$_GET['odd1']);
$l->SetOrderParamNames('ob1', 'odd1');
$l->SetAntet($gFArr);
$l->SetQuery('SELECT '.getsqlang('sa.artid').', sa.artname, s.title, sa.static_id FROM static_article sa LEFT JOIN stories s ON s.guid = '.getsqlang('sa.artid'));

if (!$l->DisplayList($page)) {
	echo $lTableHeader . '<tr><td colspan="6"><p align="center"><b>' . getstr('admin.photos.noData') . '</b></p></td></tr>' . $lTableFooter;
}

HtmlEnd();
?>