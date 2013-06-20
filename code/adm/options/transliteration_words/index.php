<?php
$docroot = getenv("DOCUMENT_ROOT");
require_once($docroot . '/lib/static.php');
$historypagetype = HISTORY_CLEAR;

$lHide = 0;
$page = (int)$_GET['p'];

HtmlStart($lHide);

$t = '
<tr>
	<td>{id}</td>
	<td><a href="./edit.php?tAction=show&id={id}">{word_bg}</a></td>
	<td><a href="./edit.php?tAction=show&id={id}">{word_en}</a></td>
	<td align="right" nowrap="true">
			<a href="./edit.php?tAction=show&id={id}"><img src="/img/edit.gif" alt="Редактирай" title="Редактирай" border="0" /></a>
			<a href="./edit.php?tAction=delete&id={id}" onclick="javascript: if (confirm(\'Сигурни ли сте, че искате да изтриете тази дума?\')) { return true; } else { return false;}"><img src="/img/trash2.gif" alt="Изтрий" title="Изтрий" border="0" /></a>
	</td>
</tr>
';

echo '<br />';

$gFArr = array(
	1 => array('caption' => 'ID', 'deforder' => 'asc', 'def'), 
	2 => array('caption' => 'Дума на български език', 'deforder' => 'asc'), 
	3 => array('caption' => 'Дума на английски език', 'deforder' => 'asc'), 
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
					<th class="gridtools" colspan="3">
						<a href="./edit.php">Добави нова дума</a>
						Думи
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
$l->SetQuery('SELECT * FROM transliteration_words');



if (!$l->DisplayList($page)) {
	echo $lTableHeader . '<tr><td colspan="3"><p align="center"><b>Няма въведени думи</b></p></td></tr>' . $lTableFooter;
}


HtmlEnd($lHide);
?>