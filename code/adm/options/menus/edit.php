<?php

$docroot = getenv("DOCUMENT_ROOT");
require_once($docroot . '/lib/static.php');

$historypagetype = HISTORY_ACTIVE;

function make_link($objtype) {
	$link = '';
	if ($objtype == 1) $link = './sel.php?obj=story';
	if ($objtype == 2) $link = './sel.php?obj=rubr';
	if ($objtype == 3) $link = './sel.php?obj=static';
	
	return $link;
}

HtmlStart();

$t = array(
	'id' => array (
		'CType' => 'hidden',
		'VType' => 'int',
		'DisplayName' => '',
		'AllowNulls' => true,
	),
	
	'sid' => array (
		'CType' => 'hidden',
		'VType' => 'int',
		'DefValue' => 1,
	),
	'name' => array(
		'CType' => 'text',
		'VType' => 'mlstring',
		'AddTags' => array(
			'class' => 'coolinp',
		),
		'DisplayName' => 'Текст',
		'TransType' => MANY_TO_SQL_ARRAY,
		'DisplayFormat' => MLSTR_D_NOTABLE,
	),
	'img' => array(
		'CType' => 'text',
		'VType' => 'mlstring',
		'AddTags' => array(
			'class' => 'coolinp',
		),
		'DisplayName' => 'Картинка',
		'TransType' => MANY_TO_SQL_ARRAY,
		'DisplayFormat' => MLSTR_D_NOTABLE,
		'AllowNulls' => true,
	),
	'type' => array(
		'CType' => 'select',
		'VType' => 'int',
		'SrcValues' => array(0 => 'Линк', 1 => 'Подменю', 2 => 'Разделител'),
		'DefValue' => 0,
		'DisplayName' => 'Тип',
		'AddTags' => array(
			'class' => 'coolinp',
		),
	),
	'active' => array(
		'CType' => 'select',
		'VType' => 'int',
		'SrcValues' => array(0 => 'неактивен', 1 => 'активен'),
		'DefValue' => 1,
		'DisplayName' => 'Статус',
		'AddTags' => array(
			'class' => 'coolinp',
		),
	),
	'parentid' => array (
		'VType' => 'int' ,
		'CType' => 'select' ,
		'SrcValues' => 'SELECT 0 as id, \'--- корен ---\' as name, 0 as t
			UNION ALL SELECT id as id, cast(repeat(\'&nbsp;&nbsp;\',mlevel) as varchar) || '.getsqlang('name').' as name, 1 as t FROM getmenucontents(0,1,{sid},0)
			WHERE type = 1
			--ORDER BY 3, 2',
		'DisplayName' => 'В меню',
		'AddTags' => array(
			'class' => 'coolinp',
		),
	),
	'ord' => array(
		'CType' => 'text',
		'VType' => 'int',
		'DisplayName' => 'Позиция',
		'AddTags' => array(
			'class' => 'coolinp',
		),
	),
	'href' => array(
		'CType' => 'text',
		'VType' => 'mlstring',
		'AddTags' => array(
			'class' => 'coolinp',
		),
		'DisplayName' => 'Хиперлинк',
		'TransType' => MANY_TO_SQL_ARRAY,
		'DisplayFormat' => MLSTR_D_NOTABLE,
		'AllowNulls' => true,
	),
	
	'show' => array(
		'CType' => 'action',
		'DisplayName' => '',
		'Hidden' => true,
		'ActionMask' => ACTION_CHECK | ACTION_EXEC | ACTION_FETCH | ACTION_SHOW,
		'SQL' => 'SELECT * FROM spsitemenu(0, {id}, null, null, null, null, null, null, null, null)',
	),
	'save' => array(
		'CType' => 'action',
		'DisplayName' => 'Запази',
		'ActionMask' => ACTION_CHECK | ACTION_CCHECK | ACTION_EXEC | ACTION_FETCH | ACTION_REDIRECT,
		'SQL' => 'SELECT * FROM spsitemenu(1, {id}, {name}, {sid}, {parentid}, {type}, {active}, {ord}, {href} , {img})',
		'RedirUrl' => '',
		'AddTags' => array(
			'class' => 'frmbutton',
		),
	),
	'delete' => array(
		'CType' => 'action',
		'DisplayName' => 'Изтрий',
		'ActionMask' => ACTION_CHECK | ACTION_EXEC | ACTION_FETCH | ACTION_REDIRECT,
		'SQL' => 'SELECT * FROM spsitemenu(3, {id}, null, null, null, null, null, null, null, null)',
		'RedirUrl' => '',
		'AddTags' => array(
			'class' => 'frmbutton',
		),		
	),
	'cancel' => array(
		'CType' => 'action',
		'DisplayName' => 'Назад',
		'ActionMask' => ACTION_REDIRECT,
		'RedirUrl' => '',
		'AddTags' => array(
			'class' => 'frmbutton',
		),		
	),
);

$f = new kfor($t);
$f->ExecAction();

$html = '{sid}{id}
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
				<th colspan="2" class="formtools">
					<a href="javascript:openw(\'' . make_link(1) . '\', \'aa\', \'location=no,menubar=yes,width=855,height=600,scrollbars=yes,resizable=yes,top=0,left=0\')">Добавяне на статия</a>
					<div class="formtools_sep">|</div>
					<a href="javascript:openw(\'' . make_link(2) . '\', \'aa\', \'location=no,menubar=yes,width=855,height=600,scrollbars=yes,resizable=yes,top=0,left=0\')">Добавяне на рубрика</a>
					<div class="formtools_sep">|</div>
					<a href="javascript:openw(\'' . make_link(3) . '\', \'aa\', \'location=no,menubar=yes,width=855,height=600,scrollbars=yes,resizable=yes,top=0,left=0\')">Добавяне на статична</a>
				</th>
			</tr>
			<tr>
				<th colspan="2">' . ( $f->lFieldArr["id"]["CurValue"] ? 'Редактиране' : 'Добавяне' ) . ' на меню</th>
			</tr>
			{name}
			{img}
			<tr>
				<td><b>{*type}:</b></td>
				<td>';
if ($f->lFieldArr['parentid']['CurValue']) {
	$f->lFieldArr['type']['DefValue'] = 1;
	$html .='{type}';
}
else $html .='{type}';
$html .=	'</td>
			</tr>
			<tr>
				<td><b>{*active}:</b></td>
				<td>{active}</td>
			</tr>
			<tr>
				<td><b>{*parentid}:</b></td>
				<td>{parentid}</td>
			</tr>
			<tr>
				<td><b>{*ord}:</b></td>
				<td>{ord}</td>
			</tr>
			{href}
			<tr><td colspan="2">&nbsp;</td></tr>
			<tr><td colspan="2" align="right">{show} {save} {delete} {cancel}</td></tr>
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
$f->lFormHtml = $html;
	

if ($f->lCurAction != 'new') {
	$f->lFieldArr['id']['AddTags'] = array('readonly' => 'readonly', 'style' => 'background: #ccc');
}

if ((!$f->lErrorCount) && (($f->lCurAction == 'save')  || ($f->lCurAction == 'delete'))) {
	 clearcacheditems2('menu', $f->lFieldArr['sid']['CurValue']);
}
echo $f->Display();

HtmlEnd();

?>