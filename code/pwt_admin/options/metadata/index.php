<?php
$docroot = getenv("DOCUMENT_ROOT");
require_once($docroot . '/lib/static.php');

$lHide = 0;

$page = (int)$_GET['p'];

HtmlStart($lHide);

$t = '
<tr>
	<td>{id}</td>
	<td>{title}</td>
	<td>{description}</td>
	<td>{keywords}</td>
	<td align="right" nowrap="true">
			<a href="./edit.php?tAction=showedit&id={id}"><img src="/img/edit.gif" alt="Редактирай" title="Редактирай" border="0" /></a>
			<a href="./edit.php?tAction=delete&id={id}" onclick="javascript: if (confirm(\'Сигурни ли сте, че искате да изтриете тези метаданни?\')) { return true; } else { return false;}"><img src="/img/trash2.gif" alt="Изтрий" title="Изтрий" border="0" /></a>
	</td>
</tr>
';

$gFArr = array(
	1 => array('caption' => 'ID', 'def', 'deforder' => 'asc'), 
	2 => array('caption' => getstr('admin.metadata.title'), 'deforder' => 'asc'), 
	3 => array('caption' => getstr('admin.metadata.description'), 'deforder' => 'asc'), 
	4 => array('caption' => getstr('admin.metadata.keywords'), 'deforder' => 'asc'), 
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
					<th class="gridtools" colspan="8">
						<a href="./edit.php">' . getstr('admin.metadata.addMetadata') . '</a>
						' . getstr('admin.metadata.antetka') . '
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
$l->SetQuery('SELECT id, title, description, keywords FROM metadata');



if (!$l->DisplayList($page)) {
	echo $lTableHeader . '<tr><td colspan="8"><p align="center"><b>' . getstr('admin.metadata.noData') . '</b></p></td></tr>' . $lTableFooter;
}


HtmlEnd($lHide);
?>