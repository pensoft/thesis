<?php

$docroot = getenv("DOCUMENT_ROOT");
require_once($docroot . '/lib/static.php');

$historypagetype = HISTORY_ACTIVE;

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
		'DisplayName' => getstr('admin.rubr.nameCol'),
		'TransType' => MANY_TO_SQL_ARRAY,
		'DisplayFormat' => MLSTR_D_NOTABLE,
	),
	
	'state' => array(
		'CType' => 'text',
		'VType' => 'int',
		'AllowNulls' => true,
		'DisplayName' => getstr('admin.rubr.stateCol'),
		'AddTags' => array(
			'class' => 'coolinp',
		),		
	),
	
	'parentnode' => array(
		'CType' => 'select',
		'VType' => 'int',
		'AddTags' => array(
			'class' => 'coolinp',
		),
		'AllowNulls' => true,
		'DisplayName' => getstr('admin.rubr.parentCol'),
		'SrcValues' => 'SELECT null as id, \'-- Изберете --\' as name, 1 as ord, null as pos 
				UNION 
			SELECT id, (CASE WHEN id = rootnode THEN '.getsqlang('name').' ELSE repeat(\'&nbsp;\', length(pos) + length(pos)/6) || \'- \' || '.getsqlang('name').' END) as name, 2 as ord, pos 
			FROM rubr 
			WHERE sid = 1
			ORDER by ord, pos',	
	),
	
	'new' => array(
		'CType' => 'action',
		'DisplayName' => '',
		'Hidden' => true,
		'ActionMask' => ACTION_CHECK | ACTION_EXEC | ACTION_FETCH | ACTION_SHOW,
		'SQL' => 'SELECT * FROM spsiterubr(2, null, null, null, null, null)',
	),
	
	'show' => array(
		'CType' => 'action',
		'DisplayName' => '',
		'Hidden' => true,
		'ActionMask' => ACTION_CHECK | ACTION_EXEC | ACTION_FETCH | ACTION_SHOW,
		'SQL' => 'SELECT * FROM spsiterubr(0, {id}, null, null, null, null)',
	),
	
	'save' => array(
		'CType' => 'action',
		'DisplayName' => 'Запази',
		'ActionMask' => ACTION_CHECK | ACTION_CCHECK | ACTION_EXEC | ACTION_FETCH | ACTION_REDIRECT,
		'SQL' => 'SELECT * FROM spsiterubr(1, {id}, {sid}, {name}, {state}, {parentnode})',
		'RedirUrl' => '',
		'AddTags' => array(
			'class' => 'frmbutton',
		),
	),
	
	'delete' => array(
		'CType' => 'action',
		'DisplayName' => 'Изтрий',
		'ActionMask' => ACTION_CHECK | ACTION_EXEC | ACTION_FETCH | ACTION_REDIRECT,
		'SQL' => 'SELECT * FROM spsiterubr(3, {id}, null, null, null, null)',
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
				<colgroup>
					<col width="200"></col>
					<col width="*"></col>
				</colgroup>
			</tr>
			<tr>
				<th colspan="2">' . ($f->lFieldArr['id']['CurValue'] ? 'Редактиране' : 'Добавяне' ) . ' на рубрика</th>
			</tr>
			{name}
			<tr>
				<td><b>{*state}:</b></td>
				<td>{state}</td>
			</tr>
			<tr>
				<td><b>{*parentnode}:</b></td>
				<td>{parentnode}</td>
			</tr>
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

echo $f->Display();
HtmlEnd();

?>