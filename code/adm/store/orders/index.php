<?php

$docroot = getenv('DOCUMENT_ROOT');
require_once($docroot . '/store/lib/static.php');

$historypagetype = HISTORY_CLEAR;

HtmlStart();
$page = (int)$_GET['p'];

$fld = array(
	'id' => array(
		'CType' => 'hidden',
		'VType' => 'int',
		'AllowNulls' => true,
	),
	'orderid' => array(
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
	
	'ip_addr' => array (
		'CType' => 'text',
		'VType' => 'string',
		'DisplayName' => 'IP адрес',
		'Checks' => array(
			CKIPADDR('{ip_addr}'),
		),
		'AddTags' => array(
			'class' => 'coolinp',
		),
		'AllowNulls' => true,
	),

	'state' => array(
		'CType' => 'select',
		'VType' => 'int',
		'SrcValues' => 'SELECT 0 as id, \'---\' as name
						UNION
						SELECT id, name FROM store_orders_states',
		'DefValue' => 0,
		'DisplayName' => 'Статус',
		'AddTags' => array(
			'class' => 'coolinp',
		),
		'AllowNulls' => true,
	),

	'show' => array(
		'CType' => 'action',
		'DisplayName' => 'Покажи',
		'SQL' => '{orderid}{name}{ip_addr}',
		'ActionMask' => ACTION_CHECK | ACTION_CCHECK| ACTION_SHOW,
		'AddTags' => array(
			'class' => 'frmbutton',
		),	
	),
);

$h = '
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
					<td>{*orderid}:<br />{orderid}</td>
					<td>{*name}:<br />{name}</td>
				</tr>
				<tr>
					<td>{*ip_addr}:<br />{ip_addr}</td>
					<td>{*state}:<br />{state}</td>
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
echo $kfor->Display();


$warr = array();

if ($kfor->lCurAction == 'show' && $kfor->lErrorCount == 0) {
	if ((int)$kfor->lFieldArr['orderid']['CurValue']) {
		$warr[] = 'so.id = ' . (int)$kfor->lFieldArr['orderid']['CurValue'];
	}
	if ((int)$kfor->lFieldArr['state']['CurValue']) {
		$warr[] = 'sos.id = ' . (int)$kfor->lFieldArr['state']['CurValue'];
	}
	if ($kfor->lFieldArr['name']['CurValue']) {
		$warr[] = 'lower(so.recipient_name) like \'%' . q(mb_strtolower($kfor->lFieldArr['name']['CurValue'], 'UTF-8')) . '%\'';
	}
	if ($kfor->lFieldArr['ip_addr']['CurValue']) {
		$warr[] = 'so.ip_addr = \'' . q(gethostbyname($kfor->lFieldArr['ip_addr']['CurValue'])) . '\'';
	}
}

echo '<br />';

$gFArr = array(
	1 => array('caption' => 'ID', 'deforder' => 'desc', 'def'),
	2 => array('caption' => 'Дата на създаване', 'deforder' => 'asc'),
	3 => array('caption' => 'Име', 'deforder' => 'asc'),
	4 => array('caption' => 'Статус', 'deforder' => 'asc'),
	5 => array('caption' => 'Сума за плащане', 'deforder' => 'asc'),
	1000 => array('caption' => ' ', 'deforder' => 'asc'),
);


$t = '
<tr>
	<td>{id}</td>
	<td>{createdate}</td>
	<td><a href="./edit.php?tAction=show&id={id}">{name}</а></td>
	<td>{statename}</td>
	<td>{total} лв.</td>
	<td align="right">
		<a href="./edit.php?tAction=show&id={id}"><img src="/img/edit.gif" border="0" title="Редактирай" alt="Редактирай" /></a>
		<a href="./edit.php?tAction=delete&id={id}" onclick="javascript: if (confirm(\'Сигурни ли сте, че искате да изтриете тази поръчка?\')) { return true; } else { return false;}"><img src="/img/trash2.gif" border="0" title="Изтрий" alt="Изтрий" /></a>
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
					<th class="gridtools" colspan="6">
						Поръчки
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
	SELECT 
		so.id, 
		so.createdate, 
		so.uid, 
		so.recipient_name as name, 
		so.recipient_city_name as city, 
		sos.name as statename, 
		so.total, 
		so.ip_addr as ip
	FROM store_orders so
	JOIN pays p ON p.payid = so.payid
	JOIN store_orders_states sos ON sos.id = p.state
	' . (count($warr) ? ' WHERE ' . implode(' AND ', $warr) : '')
;
//~ echo '<pre>';
//~ echo $gSqlStr;
//~ echo '</pre>';

$l->SetQuery($gSqlStr);

if (!$l->DisplayList($page)) {
	echo $lTableHeader . '<tr><td colspan="8"><p align="center"><b>Няма направени поръчки</b></p></td></tr>' . $lTableFooter;
}

HtmlEnd();

?>