<?php

$docroot = getenv('DOCUMENT_ROOT');
require_once($docroot . '/lib/static.php');

$historypagetype = HISTORY_CLEAR;
$sid=1; //da se opravi za mnogo site-ove
$v = (int) $_GET['parentid'];

HtmlStart();

$t = array(
	'sid' => array (
		'CType' => 'hidden',
		'VType' => 'int',
		'DefValue' => $sid,
	),
	'menuid' => array (
		'VType' => 'int' ,
		'CType' => 'select' ,
		'SrcValues' => 'SELECT 0 as id, \'--\' as name, 0 as t, 0 as p, 0 as o
			UNION ALL SELECT id as id, cast(repeat(\'&nbsp;&nbsp;\',mlevel) as character varying) || '.getsqlang('name').' as name, 1 as t, parentid as p, ord as o 
			FROM getmenucontents(0,1,{sid},0) 
			WHERE type=1',
		'DisplayName' => 'Меню',
		'AddTags' => array(
			'class' => 'coolinp',
		),
	),
	'show' => array(
		'CType' => 'action',
		'DisplayName' => 'Филтриране',
		'SQL' => '',
		'ActionMask' => ACTION_CHECK | ACTION_SHOW,
		'AddTags' => array(
			'class' => 'frmbutton',
		),
	),
);

$html = '{sid}
	<div class="t">
	<div class="b">
	<div class="l">
	<div class="r">
		<div class="bl">
		<div class="br">
		<div class="tl">
		<div class="tr">
			<table cellspacing="0" cellpadding="5" border="0" class="formtable">
			<tr>
				<th>Филтриране по</th>
			</tr>
			<tr>
				<td><b>{*menuid}:</b><br/>{menuid}</td>
			</tr>
			<tr>
				<td align="right">{show}</td>
			</tr>
			</table>
		</div>
		</div>
		</div>
		</div>
	</div>
	</div>
	</div>
	</div>';

$f = new kfor($t, $html, 'POST');

echo $f->Display();

$page = (int)$_GET['p'];

$templ = '
<tr>
	<td>{name}</td>
	<td>{img}</td>
	<td>{href}</td>
	<td>{_typename}</td>
	<td>{_getState(active)}</td>
	<td align="right">
		{_addplus}
		<a href="./edit.php?tAction=show&id={id}"><img src="/img/edit.gif" border="0" title="Редактирай" alt="Редактирай" /></a>
		<a href="./edit.php?tAction=delete&id={id}" onclick="javascript: if (confirm(\'Сигурни ли сте, че искате да изтриете това меню?\')) { return true; } else { return false;}"><img src="/img/trash2.gif" border="0" title="Изтрий" alt="Изтрий" /></a>
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
						<a href="./edit.php?parentid=' . $f->lFieldArr['menuid']['CurValue'] . '">Добави подменю</a>
						Менюта
					</th>
				</tr>
				<tr>
					<th>Текст</th>
					<th>Картинка</th>
					<th>Хиперлинк</th>
					<th>Тип</th>
					<th>Статус</th>
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
$l->SetTemplate($templ);
$l->SetPageSize(30);
$gSqlstr = 'SELECT id AS id, cast(repeat(\'&nbsp;&nbsp;\',mlevel) AS character varying) || ' . getsqlang('name') . ' AS name, parentid, type, active, ' . getsqlang('href') . ',' . getsqlang('img') . '
			FROM getmenucontents(' . ($f->lFieldArr['menuid']['CurValue'] ?  $f->lFieldArr['menuid']['CurValue'] : '0'). ', 0, ' . $f->lFieldArr['sid']['CurValue'] . ',0)';
$l->SetQuery($gSqlstr);

if (!$l->DisplayList($page)) {
	echo $lTableHeader . '<tr><td colspan="6"><p align="center"><b>Няма дефинирани елементи на това меню!</b></p></td></tr>' . $lTableFooter;
}

HtmlEnd();


function addplus($rs) {
	if ($rs['type'] == 1) {
		return '<a href="/options/menus/edit.php?tAction=new&parentid={id}"><img src="/img/add.gif" alt="Добави подменю" title="Добави подменю" border="0" /></a>';
	}
	return '';
}

function typename($r) {
	switch($r['type'] ) {
		case 1: return 'Подменю';
		case 2: return 'Разделител';
		default: return 'Линк';
	};
}

?>