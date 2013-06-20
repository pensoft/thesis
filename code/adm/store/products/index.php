<?php
$docroot = getenv("DOCUMENT_ROOT");
require_once($docroot . '/lib/static.php');
$historypagetype = HISTORY_CLEAR;

$lHide = 0;
if ($_GET['mode'] == 'rel' || (int)$_GET['add']) {
	$lHide = 1;
}

$pRelStoryId = (int)$_GET['relstoryid'];
//~ $guid = (int)$_GET['guid'];
$id = (int)$_GET['id'];
$page = (int)$_GET['p'];

HtmlStart($lHide);

if ((int)$_GET['add']) {
	if ((int)$pRelStoryId == (int)$id) {
		echo '<p style="color:red;">Свързването на продукт сам със себе си не е възможно.';
	} else {
		$gSqlStr = 'SELECT * FROM AddProductToStory(' . (int)$pRelStoryId . ', ' . (int)$id . ', 16)';
		$gCon = Con();
		$gCon->Execute($gSqlStr);
		echo '
			<script>
				window.opener.location.hash = "#snimki";
				window.opener.location.reload();
				window.location = document.referrer;
			</script>
		';
	}	
}

$fld = array(
	'id' => array(
		'CType' => 'hidden',
		'VType' => 'int',
		'AllowNulls' => true,
	),
	'mode' => array(
		'VType' => 'string',
		'CType' => 'hidden',
		'DisplayName' => '',
		'AllowNulls' => true,
	),
	'relstoryid' => array(
		'VType' => 'int',
		'CType' => 'hidden',
		'DisplayName' => '',
		'AllowNulls' => true,
	),
	'productid' => array(
		'CType' => 'text',
		'VType' => 'int',
		'DisplayName' => 'ID',
		'AddTags' => array(
			'class' => 'coolinp',
		),
		'AllowNulls' => true,
	),
	'name' => array(
		'CType' => 'text',
		'VType' => 'string',
		'DisplayName' => 'Име',
		'AddTags' => array(
			'class' => 'coolinp',
		),
		'AllowNulls' => true,
	),
	
	'subrubr' => array(
		'CType' => 'select',
		'VType' => 'int',
		'SrcValues' => 'SELECT null as id, \'--- Изберете ---\' as name, null as srchtypeid, 0 as ord, \'AA\' as pos, \'\' as class
							UNION
						SELECT id, CASE WHEN id = rootnode THEN name[1] ELSE REPEAT(\'&nbsp;\', length(pos)) || \'-\' || name[1] END as name, state as srchtypeid, 1 as ord, pos as pos, (CASE WHEN char_length(pos)>2 THEN \'smaller\' ELSE \'\' END) as class
						FROM rubr WHERE sid = 1
						ORDER BY ord, pos ',
		'DisplayName' => 'Категория',
		'AddTags' => array(
			'class' => 'coolinp',
		),
		'AllowNulls' => true,
	),
	
	'show' => array(
		'CType' => 'action',
		'DisplayName' => 'Покажи',
		'SQL' => '{productid}{name}{state}',
		'ActionMask' => ACTION_CHECK | ACTION_SHOW,
		'AddTags' => array(
			'class' => 'frmbutton',
		),	
	),
);

$h = '{id}{mode}{relstoryid}
	<div class="t">
	<div class="b">
	<div class="l">
	<div class="r">
		<div class="bl">
		<div class="br">
		<div class="tl">
		<div class="tr">
			<table cellspacing="0" cellpadding="5" border="0" class="formtable">
				<colgroup>
					<col width="50%"/>
					<col width="50%"/>
				</colgroup>
				<tr>
					<th colspan="2">' . getstr('admin.stories.filter') . '</th>
				</tr>
				<tr>
					<td>{*productid}<br/>{productid}</td>
					<td>{*name}<br/>{name}</td>
				</tr>
				<tr>
					<td>{*subrubr}<br/>{subrubr}</td>
					<td></td>
				</tr>
				<tr>
					<td colspan="2" align="right">{show}</td>
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

$kfor = new kfor($fld, $h, 'GET');
$kfor->debug = false;
echo $kfor->Display();

$warr = array();
if ((int)$pRelStoryId) {
	$warr[] = 'sp.id <> ' . (int)$pRelStoryId;
}
if ($kfor->lCurAction == 'show' && $kfor->lErrorCount == 0) {
	if ((int)$kfor->lFieldArr['productid']['CurValue']) {
		$warr[] = 'sp.id = ' . (int)$kfor->lFieldArr['productid']['CurValue'];
	}
	if ($kfor->lFieldArr['name']['CurValue']) {
		$warr[] = 'lower(sp.name) like \'%' . q(mb_strtolower($kfor->lFieldArr['name']['CurValue'], 'UTF-8')) . '%\'';
	}
	
	if ((int)$kfor->lFieldArr['subrubr']['CurValue']) {
		$warr[] = 'EXISTS ( SELECT * FROM storyproperties WHERE guid = sp.id AND propid = 1 AND valint = ' . (int)$kfor->lFieldArr['subrubr']['CurValue'] . ' ) ';
	}
}

$t = '
<tr>
	<td><a href="./edit.php?tAction=show&id={id}">{id}</a></td>
	<td>{category}</td>
	<td><a href="./edit.php?tAction=show&id={id}">{name}</a></td>
	<td>{createdate}</td>
	<td align="right">'
		 . ((int)$lHide ? '
		 <a href="/store/products/index.php?relstoryid=' . $pRelStoryId .'&id={id}&add=1"><img src="/img/add.gif" alt="Свържи" title="Свържи" border="0" /></a>
		' : '
		<a href="./extra.php?guid={id}&name={name}"><img src="/img/gear.gif" alt="Свързани елементи" title="Свързани елементи" border="0" /></a>&nbsp;
		<a href="./edit.php?tAction=show&id={id}"><img src="/img/edit.gif" border="0" title="Редактирай" alt="Редактирай" /></a>&nbsp;
		<a href="./edit.php?tAction=delete&id={id}" onclick="javascript: if (confirm(\'Сигурни ли сте, че искате да изтриете този продукт?\')) { return true; } else { return false;}"><img src="/img/trash2.gif" border="0" title="Изтрий" alt="Изтрий" /></a>
		') . ' 
	</td>
</tr>
';

$gFArr = array(
	1 => array('caption' => 'ID', 'deforder' => 'dsc', 'def'),
	2 => array('caption' => 'Категория', 'deforder' => 'asc'),
	3 => array('caption' => 'Име', 'deforder' => 'asc'),
	4 => array('caption' => 'Дата на създаване', 'deforder' => 'asc'),
	1000 => array('caption' => ' ', 'deforder' => 'asc'),
);

$lAddStoryUrl = ($_GET['mode'] == 'rel' ? './edit.php?relstoryid=' . $pRelStoryId . '&mode=rel' : './edit.php');

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
						' . ((int)$lHide ? '' : '<a href="' . $lAddStoryUrl . '">Добави нов продукт</a>') . '
						Продукти
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
$gSqlStr = '
	SELECT sp.id, r.name[1] as category, sp.name, sp.createdate
		FROM store_products sp
		JOIN rubr r on sp.store_products_cat_id = r.id
		' . (count($warr) ? ' WHERE ' . implode(' AND ', $warr) : '');
$l->SetQuery($gSqlStr);

if (!$l->DisplayList($page)) {
	echo $lTableHeader . '<tr><td colspan="8"><p align="center"><b>Няма въведени продукти</b></p></td></tr>' . $lTableFooter;
}

HtmlEnd($lHide);
?>