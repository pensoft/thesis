<?php
$docroot = getenv("DOCUMENT_ROOT");

require_once("$docroot/lib/static.php");
$historypagetype = HISTORY_CLEAR;

HtmlStart();

$page = (int)$_GET['p'];

echo '<p><a href="./edit.php">Нов цвят</a>';

$td = '
<tr>
<td>{id}</td>
<td><a href="./edit.php?id={id}&tAction=show">{name}</a></td>
<td>
<a href="./edit.php?id={id}&tAction=show"><img src="/img/edit.gif" border="0" alt="Редактирай" title="Редактирай" /></a>
<a href="./edit.php?tAction=delete&id={id}" onclick="return confirm(\'Сигурни ли сте, че искате да изтриете този цвят</a>?\')"><img src="/img/trash2.gif" border="0" title="Изтрий" alt="Изтрий" /></a>
</td>
</tr>
';

$l = new dblist();

$l->SetQuery('SELECT id, name FROM store_products_color');

$l->SetTemplate($td);

$l->SetOrderParams((int)$_GET['ordby'], (int)$_GET['ordd']);

$gFArr = array(
	1 => array('caption' => 'ID', 'def', 'deforder' => 'asc'), 
	2 => array('caption' => 'Име', 'deforder' => 'asc'), 
	1000 => array('caption' => ' ', 'deforder' => 'asc'), 
);

$l->SetAntet($gFArr);

$l->SetAlternateColors(true);

if (!$l->DisplayList($page)) {
	echo '<p><b>Няма въведени цветове</b></p>';
}



HtmlEnd();

?>