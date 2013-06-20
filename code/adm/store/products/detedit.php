<?php

$docroot = getenv('DOCUMENT_ROOT');
require_once($docroot . '/lib/static.php');

$historypagetype = HISTORY_ACTIVE;
HtmlStart();

$t = array(
	'id' => array (
		'CType' => 'hidden',
		'VType' => 'int',
		'AllowNulls' => true,
		
	),
	'store_product_id' => array(
		'CType' => 'select',
		'VType' => 'int',
		'SrcValues' => 'SELECT id, name, color from store_products',
		'DisplayName' => 'Продукт', //Glavna rubrika
		'AddTags' => array(
			'class' => 'coolinp',
		),
	),
	'colorid' => array(
		'CType' => 'select',
		'VType' => 'int',
		'SrcValues' => 'SELECT null as id, \'--\' as name UNION SELECT id, name from store_products_color',
		'DisplayName' => 'Цвят', //Glavna rubrika
		'AddTags' => array(
			'class' => 'coolinp',
		),
		'AllowNulls' => true,
	),
	
	'code' => array(
		'CType' => 'text',
		'VType' => 'string',
		'DisplayName' => 'Код',
		'AddTags' => array(
			'class' => 'coolinp',
		),
		'AllowNulls' => true,
	),
	'price' => array(
		'CType' => 'text',
		'VType' => 'float',
		'DisplayName' => 'Цена',
		'AddTags' => array(
			'class' => 'coolinp',
		),
	),
	'promoprice' => array(
		'CType' => 'text',
		'VType' => 'float',
		'DisplayName' => 'Цена в промоция',
		'AllowNulls' => true,
		'AddTags' => array(
			'class' => 'coolinp',
		),	
		'AllowNulls' => true,
	),
	'deliveryprice' => array(
		'CType' => 'text',
		'VType' => 'float',
		'DisplayName' => 'Доставна цена',
		'AddTags' => array(
			'class' => 'coolinp',
		),
		'AllowNulls' => true,
	),
	'state' => array (
		'CType' => 'select',
		'VType' => 'int',
		'SrcValues' => array(1 => 'Активен', 0 => 'Неактивен'),
		'DefValue' => 0,
		'DisplayName' => 'Статус',
		'AddTags' => array(
			'class' => 'coolinp',
		),	
	),
	'available' => array (
		'CType' => 'select',
		'VType' => 'int',
		'SrcValues' => array(1 => 'Да', 0 => 'Не'),
		'DefValue' => 0,
		'DisplayName' => 'В наличност',
		'AddTags' => array(
			'class' => 'coolinp',
		),	
	),
	
	'description' => array(
		'CType' => 'text',
		'VType' => 'string',
		'DisplayName' => 'Описание',
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
		'SQL' => 'SELECT * FROM spproductssavedetdata(0, {id}, null, null, null, null, null, null, null, null, null)',
	),
	'save' => array(
		'CType' => 'action',
		'DisplayName' => 'Запази',
		'ActionMask' => ACTION_CHECK | ACTION_CCHECK | ACTION_EXEC | ACTION_FETCH | ACTION_REDIRECT,
		'SQL' => 'SELECT * FROM spproductssavedetdata(1, {id},{store_product_id},null, null, {price}, null, null, {state}, {available}, {description})',
		'RedirUrl' => './edit.php?tAction=show&id={store_product_id}',
		'AddTags' => array(
			'class' => 'frmbutton',
		),
	),
	'delete' => array(
		'CType' => 'action',
		'DisplayName' => 'Изтрий',
		'ActionMask' => ACTION_CHECK | ACTION_EXEC | ACTION_FETCH | ACTION_REDIRECT,
			'SQL' => 'SELECT * FROM spproductssavedetdata(3, {id}, null, null, null, null, null, null, null, null, null)',
		'RedirUrl' => './edit.php?tAction=show&id={store_product_id}',
		'AddTags' => array(
			'onclick' => 'javascript: if (confirm(\'Сигурни ли сте, че искате да изтриете това свойство?\')) { return true; } else { return false;}',
			'class' => 'frmbutton',
		),		
	),
	'cancel' => array(
		'CType' => 'action',
		'DisplayName' => 'Назад',
		'ActionMask' => ACTION_REDIRECT,
		'RedirUrl' => './edit.php?tAction=show&id={store_product_id}',
		'AddTags' => array(
			'class' => 'frmbutton',
		),		
	),
);


$kfor = new kfor($t);
//~ $kfor->debug = 1;

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
				<th colspan="2">' . ($kfor->lFieldArr['id']['CurValue'] ? 'Редактиране' : 'Добавяне') . ' на свойство на продукт</th>
			</tr>
			<tr><td colspan="2"  align="right">{show} {save}
				' . ($kfor->lFieldArr['id']['CurValue'] ? '
				 {delete} ' :
				'') .'
				{cancel}
			</td></tr>
			<tr>
				<td ><b>{*store_product_id}:</b><br/><b>{store_product_id}</b></td>
				<td ><b>{*available}:</b><br/><b>{available}</b></td>
			</tr>
			<tr>
				<td ><b>{*price}:</b><br/><b>{price}</b></td>
				<td ><b>{*state}:</b><br/><b>{state}</b></td>
			</tr>
			<tr>
				<td ><b>{*description}:</b><br/><b>{description}</b></td>
				<td ></td>
			</tr>
			<tr><td colspan="2" align="right">{show} {save}
				' . ($kfor->lFieldArr['id']['CurValue'] ? '
				 {delete} ' :
				'') .'
				{cancel}
			</td></tr>
		</table>
	</div>
	</div>
	</div>
	</div>
</div>
</div>
</div>
</div>';

$kfor->SetFormHtml($h);
echo $kfor->Display();

$gFArr = array(
	1 => array('caption' => 'ID', 'deforder' => 'asc', 'def'),
	4 => array('caption' => 'Статус', 'deforder' => 'asc'),
	5 => array('caption' => 'В наличност', 'deforder' => 'asc'),
	1000 => array('caption' => ' ', 'deforder' => 'asc'),
);


$t = '
<tr>
	<td><a href="./detedit.php?tAction=show&id={id}">{id}</a></td>
	<td>{_getState(state)}</td>
	<td>{pavailable}</td>
	<td align="right">
		<a href="./detedit.php?tAction=show&id={id}"><img src="/img/edit.gif" border="0" title="Редактирай" alt="Редактирай" /></a>&nbsp;
		<a href="./detedit.php?tAction=delete&id={id}" onclick="javascript: if (confirm(\'Сигурни ли сте, че искате да изтриете това свойство</a></a>?\')) { return true; } else { return false;}"><img src="/img/trash2.gif" border="0" title="Изтрий" alt="Изтрий" /></a>
	</td>
</tr>
';

	
	$lTableHeader = '
		<a name="snimki"></a>
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
							Активни свойства
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

	$gSqlStr = '
		SELECT spd.id as id, spd.state,
			(CASE WHEN available = 1 THEN \'Да\' ELSE \'Не\' end) as pavailable  
		FROM store_products_det spd 
		JOIN store_products sp ON sp.id = spd.productid
		WHERE productid = ' . (int)$kfor->lFieldArr['store_product_id']['CurValue'];
		
	$l = new DBList($lTableHeader);
	$l->SetCloseTag($lTableFooter);
	$l->SetTemplate($t);
	$l->SetOrderParams((int)$_GET['ordby'], (int)$_GET['ordd']);
	$l->SetAntet($gFArr);
	$l->SetQuery($gSqlStr);

	if (!$l->DisplayList($page)) {
		echo $lTableHeader . '<tr><td colspan="6"><p align="center"><b>Няма активни свойства</b></p></td></tr>' . $lTableFooter;
	}
HtmlEnd();

?>