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
	'store_products_cat_id' => array(
		'CType' => 'select',
		'VType' => 'int',
		'SrcValues' => 'SELECT id, CASE WHEN id = rootnode THEN name[1] ELSE repeat(\'&nbsp;\', length(pos)) || \'- \' || name[1] END as name FROM rubr WHERE sid = 1 ORDER BY pos',
		'DisplayName' => 'Категория', //Glavna rubrika
		'AddTags' => array(
			'class' => 'coolinp',
		),
	),
	'rubr' => array(
		'CType' => 'mselect',
		'VType' => 'int',
		'SrcValues' => 'SELECT id, CASE WHEN id = rootnode THEN name[1] ELSE repeat(\'&nbsp;\', length(pos)) || \'- \' || name[1] END as name FROM rubr WHERE sid = 1 ORDER BY pos',
		'TransType' => MANY_TO_STRING,
		'DisplayName' => 'Подкатегория', //Podrubriki
		'AddTags' => array(
			'class' => 'coolinp',
		),
		'AllowNulls' => true,
	),
	'rubrnames' => array(
		'CType' => 'text',
		'VType' => 'string',
		'DisplayName' => 'Категории',
		'AddTags' => array(
			'class' => 'coolinp',
		),
	),
	'name' => array(
		'CType' => 'text',
		'VType' => 'string',
		'DisplayName' => 'Име',
		'AddTags' => array(
			'class' => 'coolinp',
		),
	),
	
	'small_description' => array(
		'VType' => 'string',
		'CType' => 'textarea',
		'DisplayName' => 'Описание',
		'AllowNulls' => true,
		'RichText' => FCK_ALL_TOOLS,
		'AddTags' => array(
			'style' => 'width: 100%',
			'rows' => '30',
			'id' => 'small_description',
		),
	),

	'big_description' => array(
		'VType' => 'string',
		'CType' => 'textarea',
		'DisplayName' => 'Текст',
		'AllowNulls' => true,
		'RichText' => FCK_ALL_TOOLS,
		'AddTags' => array(
			'style' => 'width: 100%',
			'rows' => '30',
			'id' => 'big_description',
		),
	),

	'manufacturer' => array(
		'CType' => 'select',
		'VType' => 'int',
		'SrcValues' => 'SELECT id, name from store_products_manufacturer',
		'DisplayName' => 'Производител',
		'AddTags' => array(
			'class' => 'coolinp',
		),
		'AllowNulls' => true,
	),
	'measureunit' => array(
		'CType' => 'select',
		'VType' => 'int',
		'SrcValues' => 'SELECT id, name from store_products_measure_unit',
		'DisplayName' => 'Мерни единици',
		'AddTags' => array(
			'class' => 'coolinp',
		),
		'AllowNulls' => true,
	),
	
	'pubdate' => array(
		'VType' => 'date',
		'CType' => 'text',
		'DisplayName' => 'Дата на публикуване',
		'AddTags' => array(
			'class' => 'coolinp',
			'id' => 'pubdate',
			'style' => 'width:95%',
		),
		'DefValue' => date('d/m/Y H:i'),
	),
	
	'supplier' => array(
		'CType' => 'select',
		'VType' => 'int',
		'SrcValues' => 'SELECT id, name from store_products_supplier',
		'DisplayName' => 'Доставчик',
		'AddTags' => array(
			'class' => 'coolinp',
		),
		'AllowNulls' => true,
	),
	'promo' => array (
		'CType' => 'select',
		'VType' => 'int',
		'SrcValues' => array(1 => 'Да', 0 => 'Не'),
		'DefValue' => 0,
		'DisplayName' => 'В промоция',
		'AddTags' => array(
			'class' => 'coolinp',
		),
		'AllowNulls' => true,
	),
	
	'propertytype' => array(
		'CType' => 'select',
		'VType' => 'int',
		'SrcValues' => 'SELECT id, name from store_products_property',
		'DisplayName' => 'Тип на собствеността',
		'AddTags' => array(
			'class' => 'coolinp',
		),
		'AllowNulls' => true,
	),
	'newproduct' => array (
		'CType' => 'select',
		'VType' => 'int',
		'SrcValues' => array(1 => 'Да', 0 => 'Не'),
		'DefValue' => 0,
		'DisplayName' => 'Нов продукт',
		'AddTags' => array(
			'class' => 'coolinp',
		),	
	),
	'color' => array (
		'CType' => 'select',
		'VType' => 'int',
		'SrcValues' => array(1 => 'Да', 0 => 'Не'),
		'DefValue' => 0,
		'DisplayName' => 'Има цвят',
		'AddTags' => array(
			'class' => 'coolinp',
		),
		'AllowNulls' => true,
	),
	'createdate' => array(
		'CType' => 'text',
		'VType' => 'date',
		'DisplayName' => 'Дата на създаване',
		'DefValue' => date('d/m/Y H:i:s'),
		'AddTags' => array(
			'class' => 'textbutton',
		),	
	),
	'ord' => array(
		'CType' => 'hidden',
		'VType' => 'int',
		'DisplayName' => 'Позиция',
		'DefValue'=>1,
		'AddTags' => array(
			'class' => 'textbutton',
		),	
		'AllowNulls' => true,
	),
	
	'state' => array (
		'CType' => 'select',
		'VType' => 'int',
		'SrcValues' => array(1 => 'Активен', 0 => 'Неактивен'),
		'DefValue' => 1,
		'DisplayName' => 'Статус',
		'AddTags' => array(
			'class' => 'coolinp',
		),	
	),
	
	'show' => array(
		'CType' => 'action',
		'DisplayName' => '',
		'Hidden' => true,
		'ActionMask' => ACTION_CHECK | ACTION_EXEC | ACTION_FETCH | ACTION_SHOW,
		'SQL' => 'SELECT * FROM spproductssavedata(
			0, 
			{id}, 
			null, 
			null, 
			null, 
			null, 
			null, 
			null, 
			null, 
			null, 
			null, 
			null, 
			null, 
			null, 
			null,
			null,
			null
		)',
	),
	'save' => array(
		'CType' => 'action',
		'DisplayName' => 'Запази',
		'ActionMask' => ACTION_CHECK | ACTION_CCHECK | ACTION_EXEC | ACTION_FETCH | ACTION_REDIRECT,
		'SQL' => 'SELECT * FROM spproductssavedata(
			1, 
			{id},
			{store_products_cat_id},
			{name}, 
			null, 
			null, 
			null, 
			null, 
			null, 
			{newproduct}, 
			null, 
			null, 
			{rubr}, 
			{pubdate}, 
			{big_description},
			{state},
			{small_description},
		)',
		'RedirUrl' => './edit.php?tAction=show&id={id}',
		'AddTags' => array(
			'class' => 'frmbutton',
		),
	),
	'delete' => array(
		'CType' => 'action',
		'DisplayName' => 'Изтрий',
		'ActionMask' => ACTION_CHECK | ACTION_EXEC | ACTION_FETCH | ACTION_REDIRECT,
		'SQL' => 'SELECT * FROM spproductssavedata(3, {id}, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null)',
		'RedirUrl' => './index.php',
		'AddTags' => array(
			'onclick' => 'javascript: if (confirm(\'Сигурни ли сте, че искате да изтриете този продукт?\')) { return true; } else { return false;}',
			'class' => 'frmbutton',
		),		
	),
	"svelements" => array(
		"CType" => "action",
		"DisplayName" => "Свързани елементи",
		"SQL" => "",
		"ActionMask" => ACTION_REDIRECT,
		"RedirUrl" => '/store/products/extra.php?guid={id}&name={name}',
		"AddTags" => array(
			"class" => "frmbutton",
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

echo '
<script language="JavaScript">
	function InsertCurTime(id) {
		var Stamp = new Date();
		var d = (Stamp.getDate() < 10 ? \'0\' + Stamp.getDate() : Stamp.getDate());
		var m = ((Stamp.getMonth() + 1) < 10 ? \'0\' + (Stamp.getMonth() + 1) : (Stamp.getMonth() + 1));
		var Y = Stamp.getFullYear();
		var H = (Stamp.getHours() < 10 ? \'0\' + Stamp.getHours() : Stamp.getHours());
		var i = (Stamp.getMinutes() < 10 ? \'0\' + Stamp.getMinutes() : Stamp.getMinutes());
		var datestr = d + \'/\' + m + \'/\' + Y  + \' \' + H  + \':\' + i;
		document.getElementById(id).value = datestr;
	}
</script>
';
$kfor = new kfor($t);
//~ $kfor->debug = 1;

if( $kfor->lCurAction == "save" ) {	
	$kfor->lFieldArr['save']['SQL'] = 'SELECT * FROM spproductssavedata(
		1, 
		{id},
		{store_products_cat_id},
		{name}, 
		null, 
		null, 
		null, 
		null, 
		null, 
		{newproduct}, 
		null, 
		null, 
		{rubr}, 
		{pubdate}, 
		{big_description},
		{state},
		{small_description}
	)';
}

$kfor->ExecAction();

$h = '{id}{ord}
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
				<th colspan="2">' . ($kfor->lFieldArr['id']['CurValue'] ? 'Редактиране' : 'Добавяне') . ' на продукт</th>
			</tr>
			<tr><td colspan="2" align="right">{show} {save} {cancel}
			' . ($kfor->lFieldArr['id']['CurValue'] ? '
			 {delete} {svelements} ' :
			'') .'
			</td></tr>
			<tr>
				<td valign="top"><b>{*store_products_cat_id}:</b><br/><b>{store_products_cat_id}</b></td>
				<td valign="top" rowspan="5" style="vertical-align:top"><b>{*rubr}:</b><br/><b>{rubr}</b><br/>{@rubrnames}</td>
			</tr>
			<tr>
				<td valign="top"><b>{*name}:</b><br/><b>{name}</b></td>
				<td valign="top"></td>
			</tr>
			<tr>
				<td valign="top"><b>{*newproduct}:</b><br/><b>{newproduct}</b></td>
				<td valign="top"></td>
			</tr>
			<tr>
				<td valign="top"><b>{*pubdate}:</b><br/><nobr>{pubdate} <a href="javascript: void(0);" onclick="InsertCurTime(\'pubdate\');return false;"><img src="/img/clock.gif" alt="Въведи моментното време" title="Въведи моментното време" border="0" /></a></nobr></td>
				<td valign="top" ></td>
			</tr>
			<tr>
				<td valign="top"><b>{*state}:</b><br/><b>{state}</b></td>
				<td valign="top"></td>
			</tr>
			<tr>
				<td valign="top"><b>{*small_description}:</b><br/>{small_description}</td>
				<td valign="top"><b>{*big_description}:</b><br/>{big_description}</td>
			</tr>
			<tr><td colspan="2">&nbsp;</td></tr>
			<tr><td colspan="2" align="right">{show} {save} {cancel}
			' . ($kfor->lFieldArr['id']['CurValue'] ? '
			 {delete} {svelements} ' :
			'') .'
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
if ((int)$kfor->lFieldArr['id']['CurValue']) {
	$t = '
	<tr>
		<td><a href="./detedit.php?tAction=show&id={id}">{id}</a></td>
		<td>{price}</td>
		<td>{description}</td>
		<td>{_getState(state)}</td>
		<td>{pavailable}</td>
		<td align="right">
			<a href="./detedit.php?tAction=show&id={id}"><img src="/img/edit.gif" border="0" title="Редактирай" alt="Редактирай" /></a>&nbsp;
			<a href="./detedit.php?tAction=delete&id={id}" onclick="javascript: if (confirm(\'Сигурни ли сте, че искате да изтриете това свойство\')) { return true; } else { return false;}"><img src="/img/trash2.gif" border="0" title="Изтрий" alt="Изтрий" /></a>
		</td>
	</tr>
	';

	$gFArr = array(
		1 => array('caption' => 'ID', 'deforder' => 'asc', 'def'),
		2 => array('caption' => 'Цена', 'deforder' => 'asc', 'def'),
		3 => array('caption' => 'Описание', 'deforder' => 'asc'),
		4 => array('caption' => 'Статус', 'deforder' => 'asc'),
		5 => array('caption' => 'В наличност', 'deforder' => 'asc'),
		1000 => array('caption' => ' ', 'deforder' => 'asc'),
	);
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
							<a href="./detedit.php?store_product_id=' . (int)$kfor->lFieldArr['id']['CurValue'] . '">Добави ново свойство</a>
							Свойства
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
		SELECT spd.id as id, spd.state, spd.description,
			(CASE WHEN available = 1 THEN \'Да\' ELSE \'Не\' end) as pavailable, spd.price as price  
		FROM store_products_det spd 
		JOIN store_products sp ON sp.id = spd.productid
		WHERE productid = ' . (int)$kfor->lFieldArr['id']['CurValue'];
		
	$l = new DBList($lTableHeader);
	$l->SetCloseTag($lTableFooter);
	$l->SetTemplate($t);
	$l->SetOrderParams((int)$_GET['ordby'], (int)$_GET['ordd']);
	$l->SetAntet($gFArr);
	$l->SetQuery($gSqlStr);

	if (!$l->DisplayList($page)) {
		echo $lTableHeader . '<tr><td colspan="6"><p align="center"><b>Няма свързани продукти към този продукт</b></p></td></tr>' . $lTableFooter;
	}
}
HtmlEnd();

?>