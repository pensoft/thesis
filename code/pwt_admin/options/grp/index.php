<?php
$docroot = getenv('DOCUMENT_ROOT');
require_once($docroot . '/lib/static.php');
$historypagetype = HISTORY_CLEAR;
$page = (int)$_GET['p'];
HtmlStart();

$gFArr = array(
	1 => array('caption' => 'ID', 'deforder' => 'asc'), 
	2 => array('caption' => 'Група', 'deforder' => 'asc', 'def'), 
	1000 => array('caption' => '', 'deforder' => 'asc'),
);

$t = '
<tr>
	<td>{id}</td>
	<td>{name}</td>
	<td align="right">
		<a href="./members.php?gid={id}">
			<img src="/img/grps_edit.gif" align="absmiddle" border="0" alt="потребители" title="потребители" />
		</a>
		<a href="./edit.php?id={id}&tAction=show">
			<img src="/img/edit.gif" border="0" align="absmiddle" alt="Редактирай" title="Редактирай" />
		</a>
	</td>
</tr>
';

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
						<a href="./edit.php">Добави нова група</a>
						Групи
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
$l->SetQuery('SELECT id, name FROM secgrp');

if (!$l->DisplayList($page)) {
	echo $lTableHeader . '<tr><td colspan="6"><p align="center"><b>Няма въведени групи</b></p></td></tr>' . $lTableFooter;
}


HtmlEnd();
?>