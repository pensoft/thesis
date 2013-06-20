<?php

$docroot = getenv('DOCUMENT_ROOT');
require_once($docroot . '/store/lib/static.php');

//~ $historypagetype = HISTORY_ACTIVE;

$page = (int)$_GET['p'];
$id = (int)$_GET['id'];

HtmlStart();
if ($id) {
	$gFArr = array(
		1 => array('caption' => 'ID', 'deforder' => 'asc', 'def'),
		2 => array('caption' => 'Продукт', 'deforder' => 'asc'),
		3 => array('caption' => 'ID на продукт', 'deforder' => 'asc'),
		5 => array('caption' => 'Ед. цена', 'deforder' => 'asc'),
		6 => array('caption' => 'Количество', 'deforder' => 'asc'),
		1000 => array('caption' => ' ', 'deforder' => 'asc'),
	);

	$t = '
	<tr>
		<td>{id}</td>
		<td>{name}</td>		
		<td>{store_products_id}</td>
		<td>{price} лв.</td>
		<td>{qty}</td>
		<td align="right">
		<!-- <a href="./edit.php?tAction=show&id={id}&orderid=' . $id . '"><img src="/img/edit.gif" border="0" title="Редактирай" alt="Редактирай" /></a>-->
		<a href="./edit.php?tAction=delete&id={id}&orderid=' . $id . ' onclick="javascript: if (confirm(\'Сигурни ли сте, че искате да изтриете този продукт?\')) { return true; } else { return false;}"><img src="/img/trash2.gif" border="0" title="Изтрий" alt="Изтрий" /></a>
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
						<th class="gridtools" colspan="8">
							<a href="./edit.php?orderid=' . $id . '">Добави нов продукт в поръчката</a>
							Продукти в поръчката ID=' . $id . '
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
	
	$gSqlStr = 'SELECT sod.id, sp.name, sod.qty, spd.price, r.name as catname, sod.store_products_id
			FROM store_orders_det sod
			JOIN store_orders so ON (so.id = sod.store_orders_id)	
			JOIN store_products sp ON (sp.id = sod.store_products_id)
			JOIN store_products_det spd ON spd.productid = sp.id AND spd.id = sod.store_products_det_id
			JOIN rubr r ON (r.id = sp.store_products_cat_id)
			WHERE so.id = ' . (int)$_GET['id'];
		
	$l->SetQuery($gSqlStr);

	if (!$l->DisplayList($page)) {
		echo $lTableHeader . '<tr><td colspan="8"><p align="center"><b>Няма намерени продукти към тази поръчка</b></p></td></tr>' . $lTableFooter;
	}
} else {
	echo '
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
							Продукти в поръчката ID=' . $id . '
						</th>
					</tr>
					<tr><td colspan="8"><p align="center"><b>Не сте избрали поръчка</b></p></td></tr>
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
}
HtmlEnd();

?>