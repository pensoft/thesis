<?php
$docroot = getenv("DOCUMENT_ROOT");
require_once($docroot . '/lib/static.php');
$historypagetype=HISTORY_ACTIVE;

HtmlStart();


$gKforFlds = array(	
	'id' => array(
		'VType' => 'int',
		'CType' => 'hidden',
		'DisplayName' => '',
		'AllowNulls' => true,
	),
	
	'source_id' => array(
		'VType' => 'int',
		'CType' => 'select',		
		'SrcValues' => 'SELECT id, name FROM autotag_re_sources',
		'DisplayName' => getstr('admin.autotag_re_variables.colSourceId'),
		'AllowNulls' => false,
		'AddTags' => array(
			'class' => 'coolinp',
		),
	),
	
	'name' => array(
		'VType' => 'string',
		'CType' => 'text',
		'AddTags' => array(
			'class' => 'coolinp',
		),
		'DisplayName' => getstr('admin.autotag_re_variables.colName'),
	),
	
	'variable_symbol' => array(
		'VType' => 'string',
		'CType' => 'text',
		'AddTags' => array(
			'class' => 'coolinp',
		),
		'DisplayName' => getstr('admin.autotag_re_variables.colVariableSymbol'),
	),
	
	
	'variable_type' => array(
		'VType' => 'int',
		'CType' => 'select',
		'SrcValues' => 'SELECT id, name FROM autotag_re_variable_types ORDER BY id',
		'DisplayName' => getstr('admin.autotag_re_variables.colType'),
		'AllowNulls' => false,
		'AddTags' => array(
			'class' => 'coolinp',
			'id' => 'type_id',			
		),
	),
	
	'expression' => array(
		'VType' => 'string',
		'CType' => 'text',
		'AddTags' => array(
			'class' => 'coolinp',
		),
		'DisplayName' => getstr('admin.autotag_re_variables.colExpression'),
	),
	
	'concat_multiple' => array(
		'VType' => 'int',
		'CType' => 'select',
		'SrcValues' => array(
			0 => getstr('global.no'),
			1 => getstr('global.yes'),
		),
		'DisplayName' => getstr('admin.autotag_re_variables.colConcatMultiple'),		
		'AddTags' => array(
			'class' => 'coolinp',
		),
	),
	
	'concat_separator' => array(
		'VType' => 'string',
		'CType' => 'text',
		'AddTags' => array(
			'class' => 'coolinp',
		),
		'AllowNulls' => true,
		'DisplayName' => getstr('admin.autotag_re_variables.colConcatSeparator'),
	),
	
	'showedit' => array(
		'CType' => 'action',
		'Hidden' => true,
		'DisplayName' => '',
		'SQL' => 'SELECT * FROM spAutotagReVariables(0, {id}, null, null, null, null, null, null, null)',
		'ActionMask' => ACTION_CHECK | ACTION_EXEC | ACTION_FETCH | ACTION_SHOW,
	),
	
	'save' => array(
		'CType' => 'action',
		'DisplayName' => getstr('admin.saveButton'),
		'SQL' => 'SELECT * FROM spAutotagReVariables(1, {id}, {source_id}, {name}, {variable_symbol}, {variable_type}, {expression}, {concat_multiple}, {concat_separator})',
		'ActionMask' => ACTION_CHECK | ACTION_CCHECK | ACTION_EXEC | ACTION_FETCH  ,
		'RedirUrl' => '',
		'AddTags' => array(
			'class' => 'frmbutton',			
		), 
	),
	
);

$gKfor = new kfor($gKforFlds, null, 'POST');
$gKfor->debug = false;
$gKfor->ExecAction();

$gKforTpl = '
{id} 
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
			<col width="25%" />
			<col width="25%" />
			<col width="25%" />			
			<col width="25%" />			
		</colgroup>
		<tr>
			<th colspan="4">' . getstr('admin.autotag_re_variables.editLabel') . '</th>
		</tr>
		<tr>
			<td valign="top" colspan="2"><b>{*source_id}:</b><br/>{source_id}</td>
			<td valign="top" colspan="2"><b>{*name}:</b><br/>{name}</td>
		</tr>
		<tr>
			<td valign="top" colspan="2"><b>{*variable_symbol}:</b><br/>{variable_symbol}</td>
			<td valign="top" colspan="2"><b>{*variable_type}:</b><br/>{variable_type}</td>
		</tr>
		<tr>
			<td valign="top" colspan="4"><b>{*expression}:</b><br/>{expression}</td>			
		</tr>
		<tr>
			<td valign="top" colspan="2"><b>{*concat_multiple}:</b><br/>{concat_multiple}</td>
			<td valign="top" colspan="2"><b>{*concat_separator}:</b><br/>{concat_separator}</td>
		</tr>			
		<tr>
			<td>&nbsp;</td>
			<td colspan="4" align="right">{save}
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

$gKfor->lFormHtml = $gKforTpl;


echo $gKfor->Display();

if ($gKfor->lCurAction == 'save' && !$gKfor->lErrorCount) {
	echo '
		<script>
			window.opener.location.reload();
			window.close();
		</script>
	';
}

/*
//Not saved changes
echo '
	<script language="JavaScript">
		SetEvent(\'def1\');
		window.onbeforeunload = ConfirmToExit;
	</script>
';
*/
HtmlEnd();


?>