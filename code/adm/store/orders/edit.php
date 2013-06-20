<?php

$docroot = getenv('DOCUMENT_ROOT');
require_once($docroot . '/store/lib/static.php');

$historypagetype = HISTORY_ACTIVE;

$page = (int)$_GET['p'];
(int)$kfor->lFieldArr['id']['CurValue'] = (int)$_GET['id'];
HtmlStart();

$t = array(
	'id' => array (
		'CType' => 'hidden',
		'VType' => 'int',
		'AllowNulls' => true,
	),
	'createdate' => array(
		'CType' => 'text',
		'VType' => 'date',
		'DisplayName' => 'Дата на създаване',
		'DefValue' => date('d/m/Y H:i:s'),
		'AddTags' => array(
			'class' => 'coolinp',
		),	
	),
	'recipient_name' => array (
		'CType' => 'text',
		'VType' => 'string',
		'DisplayName' => 'Име',
		'AddTags' => array(
			'class' => 'coolinp',
		),	
		'AllowNulls' => false,
	),
	'recipient_city_name' => array (
		'CType' => 'text',
		'VType' => 'string',
		'DisplayName' => 'Град',
		'AddTags' => array(
			'class' => 'coolinp',
		),
		'AllowNulls' => false,
	),
	'recipient_address' => array (
		'CType' => 'textarea',
		'VType' => 'string',
		'DisplayName' => 'Адрес',
		'AddTags' => array(
			'class' => 'coolinp',
		),	
		'AllowNulls' => false,
	),
	'recipient_phone' => array (
		'CType' => 'text',
		'VType' => 'string',
		'DisplayName' => 'Телефон',
		'AddTags' => array(
			'class' => 'coolinp',
		),	
		'AllowNulls' => false,
	),
	'total' => array (
		'CType' => 'text',
		'VType' => 'float',
		'DisplayName' => 'Обща Сума',
		'AllowNulls' => true,
		'AddTags' => array(
			'readonly' => 'readonly',
			'class' => 'coolinp',
		)
	),
	'state' => array (
		'CType' => 'select',
		'VType' => 'int',
		'SrcValues' => 'SELECT id, name FROM store_orders_states',
		'DisplayName' => 'Статус',
		'AddTags' => array(
			'class' => 'coolinp',
		),	
		'AllowNulls' => false,
	),
	'ip_addr' => array (
		'CType' => 'text',
		'VType' => 'string',
		'DisplayName' => 'IP адрес',
		'DefValue' => $_SERVER['REMOTE_ADDR'], 
		'AddTags' => array(
			'class' => 'coolinp',
		),	
	),
	'delivery_price' => array (
		'CType' => 'text',
		'VType' => 'float',
		'DisplayName' => 'Сума за доставка',
		'AllowNulls' => true,
		'AddTags' => array(
			'class' => 'coolinp',
		),	
		'AllowNulls' => true,
	),
	'show' => array(
		'CType' => 'action',
		'DisplayName' => '',
		'Hidden' => true,
		'ActionMask' => ACTION_CHECK | ACTION_EXEC | ACTION_FETCH | ACTION_SHOW,
		'SQL' => 'SELECT * FROM storeorders(0, {id}, null, null, null, null, null, null, null, null)',
	),
	'save' => array(
		'CType' => 'action',
		'DisplayName' => 'Запази',
		'ActionMask' => ACTION_CHECK | ACTION_CCHECK | ACTION_EXEC | ACTION_FETCH | ACTION_REDIRECT,
		'SQL' => 'SELECT * FROM storeorders(
			1, 
			{id}, 
			{recipient_name},
			{recipient_city_name},
			{recipient_address}, 
			{recipient_phone}, 
			{state}, 
			{total}, 
			{ip_addr},
			{delivery_price}
		)',
		'RedirUrl' => './index.php',
		'AddTags' => array(
			'class' => 'frmbutton',
		),
	),
	'delete' => array(
		'CType' => 'action',
		'DisplayName' => 'Изтрий',
		'ActionMask' => ACTION_CHECK | ACTION_EXEC | ACTION_FETCH | ACTION_REDIRECT,
		'SQL' => 'SELECT * FROM storeorders(3, {id}, null, null, null, null, null, null, null, null)',
		'RedirUrl' => './index.php',
		'AddTags' => array(
			'onclick' => 'javascript: if (confirm(\'Сигурни ли сте, че искате да изтриете тази поръчка?\')) { return true; } else { return false;}',
			'class' => 'frmbutton',
		),		
	),
	'cancel' => array(
		'CType' => 'action',
		'DisplayName' => 'Назад',
		'ActionMask' => ACTION_REDIRECT,
		'RedirUrl' => './index.php',
		'AddTags' => array(
			'class' => 'frmbutton',
		),
	),
);
$kfor = new kfor($t);
$kfor->ExecAction();

$h = '{id}
<div class="t">
<div class="b">
<div class="l">
<div class="r">
	<div class="bl">
	<div class="br">
	<div class="tl">
	<div class="tr">
		<table width="100%" cellspacing="0" cellpadding="2" border="0" class="formtable" >
			<colgroup>
				<col width="50%"/>
				<col width="50%"/>
			</colgroup>
			<tr>
				<th colspan="2">' . ((int)$kfor->lFieldArr['id']['CurValue'] ? 'Редактиране' : 'Добавяне') . ' на продукт</th>
			</tr>
			<tr>
				<td colspan="2" align="right">
					{show} {save} {delete} {cancel}
				</td>
			</tr>
			<tr>
				<td colspan="2">
					<b>{*createdate}:</b>{@createdate}<b>&nbsp;' . ((int)$kfor->lFieldArr['id']['CurValue'] ? ('ID: ' . (int)$kfor->lFieldArr['id']['CurValue']) : '') . '</b>
				</td>
			</tr>
			<tr>
				<td>
					<b>{*state}:</b><br />{state}
				</td>
				<td>
					<b>{*recipient_name}:</b><br />{recipient_name}
				</td>
			</tr>
			<tr>
				<td>
					<b>{*recipient_city_name}:</b><br />{recipient_city_name}
				</td>
				<td>
					<b>{*recipient_address}:</b><br />{recipient_address}
				</td>
			</tr>
			<tr>
				<td>
					<b>{*ip_addr}:</b><br />{ip_addr}
				</td>
				<td>
					<b>{*recipient_phone}:</b><br />{recipient_phone}
				</td>
			</tr>
			<tr>
				<td>
					<b>{*total}:</b><br />' . ((int)$kfor->lFieldArr['id']['CurValue'] ? '{@total} лв.' : '') .'
				</td>
				<td>
					<b>{*delivery_price}:</b><br />{delivery_price}
				</td>
			</tr>
			<tr>
				<td colspan="2" align="right">
				{show} {save} {delete} {cancel}
				</td>
			</tr>
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

$kfor->SetFormHtml($h);

echo $kfor->Display();

if ((int)$kfor->lFieldArr['id']['CurValue']) {
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
		<!-- <a href="./store_orders_det/edit.php?tAction=show&id={id}&orderid=' . (int)$kfor->lFieldArr['id']['CurValue'] . '"><img src="/img/edit.gif" border="0" title="Редактирай" alt="Редактирай" /></a>-->
		<a href="./store_orders_det/edit.php?tAction=delete&id={id}&orderid=' . (int)$kfor->lFieldArr['id']['CurValue'] . '" onclick="javascript: if (confirm(\'Сигурни ли сте, че искате да изтриете този продукт?\')) { return true; } else { return false;}"><img src="/img/trash2.gif" border="0" title="Изтрий" alt="Изтрий" /></a>
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
							<a href="./store_orders_det/edit.php?orderid=' . (int)$kfor->lFieldArr['id']['CurValue'] . '">Добави нов продукт в поръчката</a>
							Продукти в поръчката
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
			WHERE so.id = ' . (int)$kfor->lFieldArr['id']['CurValue'];
		
	$l->SetQuery($gSqlStr);

	if (!$l->DisplayList($page)) {
		echo $lTableHeader . '<tr><td colspan="8"><p align="center"><b>Няма намерени продукти към тази поръчка</b></p></td></tr>' . $lTableFooter;
	}
}

HtmlEnd();

?>