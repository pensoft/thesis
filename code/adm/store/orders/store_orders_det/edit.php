<?php

$docroot = getenv('DOCUMENT_ROOT');
require_once($docroot . '/store/lib/static.php');



$historypagetype = HISTORY_ACTIVE;

$page = (int)$_GET['p'];
$id = (int)$_GET['id'];
$orderid = (int)$_REQUEST['orderid'];

HtmlStart();

$t = array(
	'id' => array (
		'CType' => 'hidden',
		'VType' => 'int',
		'AllowNulls' => true,
	),
	'store_orders_id' => array (
		'CType' => 'select',
		'VType' => 'int',
		'DisplayName' => 'ID на поръчка', 
		'SrcValues' => 'SELECT id, id as name from store_orders',
		'DefValue' => $orderid,
		'AddTags' => array(
			'class' => 'coolinp',
		),
	),
	'store_products_id' => array (
		'CType' => 'select',
		'VType' => 'int',
		'DisplayName' => 'Име на продукт', 
		'SrcValues' => 'SELECT id, name from store_products',
		'AddTags' => array(
			'onchange' => 'ChangeProductQuantity()',
			'id'=>'productid',
			'class' => 'coolinp',
		),
	),
	'manual_product_id' => array (
		'CType' => 'text',
		'VType' => 'int',
		'DisplayName' => 'ID на продукта',
		'AddTags' => array(
			'onchange' => 'ChangeProductID()',
			'id' => 'manualproductid',
			'class' => 'coolinp',
		),
	),
	'store_products_det_id' => array (
		'CType' => 'select',
		'VType' => 'int',
		'DisplayName' => 'Вид на продукт', 
		'SrcValues' => '
			SELECT spd.id, spd.description as name, sp.id as productid 
			FROM store_products_det spd
			JOIN store_products sp ON sp.id = spd.productid
		',
		'AddTags'=>array(
			'id'=>'productquantityid',
			'class' => 'coolinp',
		),
	),
	'qty' => array (
		'CType' => 'text',
		'VType' => 'int',
		'DisplayName' => 'Количество',
		'AddTags' => array(
			//~ $id ? 'readonly' : '' => '',
			'class' => 'coolinp',
		),
		'Checks' => array(array('Expr' => 'positive({qty}) == 0', 'ErrStr' => 'Количеството трябва да е положително число')),
	),
	'price' => array (
		'CType' => 'text',
		'VType' => 'float',
		'DisplayName' => 'Цена',
		'AllowNulls' => true,
		'AddTags' => array(
			'readonly' => 'readonly',
			'class' => 'coolinp',
		),
	),
	'createdate' => array(
		'CType' => 'text',
		'VType' => 'date',
		'DisplayName' => 'Дата на създаване',
		'DefValue' => date('d/m/Y H:i:s'),
	),
	'show' => array(
		'CType' => 'action',
		'DisplayName' => '',
		'Hidden' => true,
		'ActionMask' => ACTION_CHECK | ACTION_EXEC | ACTION_FETCH | ACTION_SHOW,
		'SQL' => 'SELECT *, store_products_id AS manual_product_id from storeordersdet(
			0, {id}, null, null, null, null
		)',
		'RedirUrl' => '',
		'AddTags' => array(
			'class' => 'frmbutton',
		),
	),
	'save' => array(
		'CType' => 'action',
		'DisplayName' => 'Запази',
		'ActionMask' => ACTION_CHECK | ACTION_CCHECK | ACTION_EXEC | ACTION_FETCH | ACTION_REDIRECT,
		'SQL' => 'SELECT * from storeordersdet(
			1, 
			{id}, 
			{store_orders_id}, 
			{store_products_id}, 
			{store_products_det_id}, 
			{qty}
		)',
		'RedirUrl' => '/store/orders/edit.php?tAction=show&id={store_orders_id}',
		'AddTags' => array(
			'class' => 'frmbutton',
		),
	),
	'delete' => array(
		'CType' => 'action',
		'DisplayName' => 'Изтрий',
		'ActionMask' => ACTION_CHECK | ACTION_EXEC | ACTION_FETCH | ACTION_REDIRECT,
		'SQL' => 'SELECT * from storeordersdet(3, {id}, null, null, null, null)',
		'RedirUrl' => '/store/orders/edit.php?tAction=show&id=' . (int) $orderid,
		'AddTags' => array(
			'onclick' => 'javascript: if (confirm(\'Сигурни ли сте, че искате да изтриете тази поръчка?\')) { return true; } else { return false;}',
			'class' => 'frmbutton',
		),		
	),
	'cancel' => array(
		'CType' => 'action',
		'DisplayName' => 'Назад',
		'ActionMask' => ACTION_REDIRECT,
		'RedirUrl' => '/store/orders/edit.php?tAction=show&id={store_orders_id}',
		'AddTags' => array(
			'class' => 'frmbutton',
		),
	),
);
$kfor = new kfor($t);

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
				<th colspan="2">' . ($kfor->lFieldArr['id']['CurValue'] ? 'Редактиране' : 'Добавяне') . ' на продукт към поръчка</th>
			</tr>
			<tr>
				<td><b>{*createdate}:</b>{@createdate}</td>
				<td></td>
			</tr>
			<tr>
				<td><b>{*store_orders_id}:</b><br />{store_orders_id}</td>
				<td><b>{*store_products_id}:</b><br />{store_products_id}</td>
			</tr>
			<tr>
				<td><b>{*store_products_det_id}:</b><br />{store_products_det_id}</td>
				<td><b>{*qty}:</b><br />{qty}</td>
			</tr>
			<tr>
				<td><b>{*price}:</b><br />{price}</td>
				<td><b>{*manual_product_id}:</b>{manual_product_id}</td>
			</tr>
			<tr>
				<td colspan="2" align="right">{save} {delete} {cancel}</td>
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
$kfor->ExecAction();

echo $kfor->Display();
	echo '<script>ChangeProductQuantity(' . (int) $kfor->lFieldArr['store_products_det_id']['CurValue'] . ')</script>';

	
HtmlEnd();

?>