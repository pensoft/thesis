<?php
$docroot = getenv("DOCUMENT_ROOT");
require_once($docroot . '/lib/static.php');
$historypagetype=HISTORY_ACTIVE;

HtmlStart();



$gKforFlds = array(		
	'rule_id' => array(
		'VType' => 'int',
		'CType' => 'hidden',
		'SrcValues' => 'SELECT id, name FROM autotag_rules',
		'DisplayName' => getstr('admin.autotag_rules_properties.colRuleId'),
		'AllowNulls' => false,
		'AddTags' => array(
			'class' => 'coolinp',
		),
	),
	
	'type_id' => array(
		'VType' => 'int',
		'CType' => 'select',
		'SrcValues' => 'SELECT null as id, \'----\' as name UNION SELECT id, name FROM autotag_property_types ORDER BY id',
		'DisplayName' => getstr('admin.autotag_rules_properties.colType'),
		'AllowNulls' => false,
		'AddTags' => array(
			'class' => 'coolinp',
			'id' => 'type_id',
			'onchange' => 'changeRuleSelects()',
		),
	),
	
	'property_modifier_id' => array(
		'VType' => 'int',
		'CType' => 'select',
		'SrcValues' => 'SELECT null as id, \'----\' as name, null as type_id UNION SELECT id, name, type_id FROM autotag_property_modifiers  ORDER BY id',
		'DisplayName' => getstr('admin.autotag_rules_properties.colModifier'),
		'AllowNulls' => false,
		'AddTags' => array(
			'class' => 'coolinp',
			'id' => 'property_modifier_id',
		),
	),
	
	'property_id' => array(
		'VType' => 'int',
		'CType' => 'select',
		'SrcValues' => 'SELECT null as id, \'----\' as name, null as type_id UNION SELECT id, name, 1 as type_id FROM place_rules UNION SELECT id, name, 2 as type_id FROM regular_expressions UNION SELECT id, name, 3 as type_id FROM autotag_re_sources ORDER BY type_id, id',
		'DisplayName' => getstr('admin.autotag_rules_properties.colPropertyName'),
		'AllowNulls' => false,
		'AddTags' => array(
			'class' => 'coolinp',
			'id' => 'property_id',
		),
	),
	
	'priority' => array(
		'VType' => 'int',
		'CType' => 'text',		
		'DisplayName' => getstr('admin.autotag_rules_properties.colPriority'),
		'AllowNulls' => false,
		'AddTags' => array(
			'class' => 'coolinp',
			'id' => 'priority',
		),
	),
	
	'showedit' => array(
		'CType' => 'action',
		'Hidden' => true,
		'DisplayName' => '',
		'SQL' => 'SELECT * FROM spRuleAddProperty(0, {rule_id}, {property_id}, null, null, null)',
		'ActionMask' => ACTION_CHECK | ACTION_EXEC | ACTION_FETCH | ACTION_SHOW,
	),
	
	'save' => array(
		'CType' => 'action',
		'DisplayName' => getstr('admin.saveButton'),
		'SQL' => 'SELECT * FROM spRuleAddProperty(1, {rule_id}, {property_id}, {type_id}, {property_modifier_id}, {priority})',
		'ActionMask' => ACTION_CHECK | ACTION_CCHECK | ACTION_EXEC | ACTION_FETCH  ,
		'RedirUrl' => '',
		'AddTags' => array(
			'class' => 'frmbutton',			
		), 
	),
	
);

$gKfor = new kfor($gKforFlds, null, 'POST');
$gKfor->debug = false;

if ($gKfor->lCurAction == 'save' && !$gKfor->lErrorCount) {
	if( $gKfor->lFieldArr['type_id']['CurValue'] == (int) PLACE_RULE_PROPERTY_TYPE){
		$gKfor->lFieldArr['priority']['CurValue'] = 0;
	}
	
	if( $gKfor->lFieldArr['type_id']['CurValue'] == (int) SOURCE_RULE_PROPERTY_TYPE ){
		$gKfor->lFieldArr['property_modifier_id']['CurValue'] = '';
		$gKfor->lFieldArr['property_modifier_id']['AllowNulls'] = true;
	}
}

$gKfor->ExecAction();

$gKforTpl = '
{rule_id}
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
			<col width="33%" />
			<col width="33%" />
			<col width="33%" />			
		</colgroup>
		<tr>
			<th colspan="3">' . getstr('admin.autotag_rules_properties.editLabel') . '</th>
		</tr>
		<tr>
			<td valign="top"><b>{*type_id}:</b><br/>{type_id}</td>
			<td valign="top"><b>{*property_modifier_id}:</b><br/>{property_modifier_id}</td>
			<td valign="top"><b>{*priority}:</b><br/>{priority}</td>
		</tr>	
		<tr>
			<td colspan="3" valign="top"><b>{*property_id}:</b><br/>{property_id}</td>
		</tr>			
		<tr>
			<td>&nbsp;</td>
			<td colspan="2" align="right">{save}
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
echo '<script>initRuleSelects()</script>';
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