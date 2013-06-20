<?php
$docroot = getenv('DOCUMENT_ROOT');
require_once($docroot . '/lib/static.php');

$historypagetype = HISTORY_CLEAR;

HtmlStart();

$page = (int)$_GET['p'];

$t = '
<tr>
	<td>{langid}</td>
	<td>{code}</td>
	<td>{name}</td>
	<td align="right">
	<a href="./edit.php?code={code}&tAction=show"><img src="/img/edit.gif" border="0" title="Редактирай" alt="Редактирай" /></a>
	<a href="./edit.php?code={code}&tAction=delete" onclick="javascript: if (confirm(\'Сигурни ли сте, че искате да изтриете този език?\')) { return true; } else { return false;}"><img src="/img/trash2.gif" border="0" title="Изтрий" alt="Изтрий" /></a>
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
					<th class="gridtools" colspan="4">
						<a href="./edit.php">Добави нов език</a>
						Езици
					</th>
				</tr>
				<tr>
					<th>ID</th>
					<th>Код</th>
					<th>Име</th>
					<th>&nbsp;</th>
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
$l->SetQuery('SELECT * FROM languages ORDER BY langid');

if (!$l->DisplayList($page)) {
	echo $lTableHeader . '<tr><td colspan="4"><p align="center"><b>Няма въведени езици</b></p></td></tr>' . $lTableFooter;
}
HtmlEnd();

?>