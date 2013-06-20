<?php
$docroot = getenv('DOCUMENT_ROOT');
require_once($docroot . '/store/lib/static.php');
$pProductid = (int) $_GET['productid'];
$lHide = 1;
HtmlStart($lHide);
if(!$pProductid)
	echo '<p>Моля изберете продукт</p>';
else{
	echo '<p><a href="./detedit.php?productid=' . $pProductid . '" target="_blank">Добави ново свойство</a></p>';
	$gFArr = array(
		1 => array('caption' => 'ID', 'deforder' => 'asc', 'def'),
		2 => array('caption' => 'Код', 'deforder' => 'asc', 'def'),
		3 => array('caption' => 'Количество', 'deforder' => 'asc'),
		4 => array('caption' => 'Статус', 'deforder' => 'asc'),
		5 => array('caption' => 'В наличност', 'deforder' => 'asc'),
		1000 => array('caption' => ' ', 'deforder' => 'asc'),
	);


	$t = '
	<tr>
		<td><a href="./detedit.php?tAction=show&id={id}" target="_blank">{id}</a></td>
		<td>{code}</td>
		<td>{quantity}</td>
		<td>{_getState(state)}</td>
		<td>{pavailable}</td>
		<td align="right">
			<a href="./detedit.php?tAction=show&id={id}" target="_blank"><img src="/img/edit.gif" border="0" title="Редактирай" alt="Редактирай" /></a>&nbsp;
			<a href="./detedit.php?tAction=delete&id={id}" onclick="javascript: if (confirm(\'Сигурни ли сте, че искате да изтриете това свойство</a></a>?\')) { return true; } else { return false;}"><img src="/img/trash2.gif" border="0" title="Изтрий" alt="Изтрий" /></a>
		</td>
	</tr>
	';

	$l = new DBlist('<table class="datatable" width="100%">');
	$l->SetTemplate($t);
	$l->SetAntet($gFArr);
	$gSqlStr = '
		SELECT spd.id as id, spd.code as code, (spq.name || \' \' || spm.name) as quantity, spd.state,
			(CASE WHEN available = 1 THEN \'Да\' ELSE \'Не\' end) as pavailable  
		FROM store_products_det spd 
		JOIN store_products_quantity spq ON spd.quantity = spq.id
		JOIN store_products sp ON sp.id = spd.productid
		JOIN store_products_measure_unit spm ON sp.measureunit = spm.id
		WHERE productid = ' . $pProductid;
	$l->SetQuery($gSqlStr);
	$l->SetPageSize(30);
	$l->SetOrderParams((int)$_GET['ordby'], (int)$_GET['ordd']);
	$l->SetAlternateColors(true);
	if (!$l->DisplayList($page)) {
		echo '<p><b>Няма въведени свойства за този продукт</b></p>';
	}
}

HtmlEnd($lHide);
?>