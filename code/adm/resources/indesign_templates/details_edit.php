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
	'template_id' => array(
		'VType' => 'int',
		'CType' => 'select',
		'SrcValues' => 'SELECT id, name FROM indesign_templates',
		'DisplayName' => getstr('admin.indesign_templates_details.colTemplateId'),
		'AllowNulls' => false,
		'AddTags' => array(
			'class' => 'coolinp',
		),
	),
	
	'type' => array(
		'VType' => 'int',
		'CType' => 'select',
		'SrcValues' => array(
			1 => getstr('admin.indesign_templates_details.character_type'),
			2 => getstr('admin.indesign_templates_details.paragraph_type'),
		),
		'DisplayName' => getstr('admin.indesign_templates_details.colTemplateId'),
		'AllowNulls' => false,
		'AddTags' => array(
			'class' => 'coolinp',
		),
	),
	
	'new_parent' => array(
		'VType' => 'int',
		'CType' => 'select',
		'SrcValues' => array(
			0 => getstr('global.no'),
			1 => getstr('global.yes'),
		),
		'DisplayName' => getstr('admin.indesign_templates_details.colNewParent'),
		'AllowNulls' => false,
		'AddTags' => array(
			'class' => 'coolinp',
		),
	),
	
	'change_before' => array(
		'VType' => 'int',
		'CType' => 'select',
		'SrcValues' => array(
			0 => getstr('global.no'),
			1 => getstr('global.yes'),
		),		
		'DisplayName' => getstr('admin.indesign_templates_details.colChangeBefore'),
		'AllowNulls' => false,
		'AddTags' => array(
			'class' => 'coolinp',
		),
	),
	
	'change_after' => array(
		'VType' => 'int',
		'CType' => 'text',		
		'DisplayName' => getstr('admin.indesign_templates_details.colChangeAfter'),
		'AllowNulls' => false,
		'AddTags' => array(
			'class' => 'coolinp',
		),
	),
	
	'special' => array(
		'VType' => 'int',
		'CType' => 'select',
		'SrcValues' => array(
			0 => getstr('admin.indesign_templates_details.specialNo'),
			1 => getstr('admin.indesign_templates_details.specialTable'),
			2 => getstr('admin.indesign_templates_details.specialFigure'),
		),		
		'DisplayName' => getstr('admin.indesign_templates_details.colSpecial'),
		'AllowNulls' => false,
		'DefValue' => 0,
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
		'DisplayName' => getstr('admin.indesign_templates_details.colName'),
	),
	
	'parent_path' => array(
		'VType' => 'string',
		'CType' => 'text',
		'AddTags' => array(
			'class' => 'coolinp',
		),
		'AllowNulls' => true,
		'DisplayName' => getstr('admin.indesign_templates_details.colParentPath'),
	),
	
	'style' => array(
		'VType' => 'string',
		'CType' => 'text',
		'AddTags' => array(
			'class' => 'coolinp',
		),
		'AllowNulls' => true,
		'DisplayName' => getstr('admin.indesign_templates_details.colStyle'),
	),
	
	'node_id' => array(
		'VType' => 'int',
		'CType' => 'select',
		'AddTags' => array(
			'class' => 'coolinp',
		),
		'SrcValues' => 'SELECT id, name FROM xml_nodes',
		'DisplayName' => getstr('admin.indesign_templates_details.colNodeId'),
	),
	
	'showedit' => array(
		'CType' => 'action',
		'Hidden' => true,
		'DisplayName' => '',
		'SQL' => 'SELECT * FROM spIndesignTemplateDetails(0, {id}, null, null, null, null, null, null, null, null, null, null)',
		'ActionMask' => ACTION_CHECK | ACTION_EXEC | ACTION_FETCH | ACTION_SHOW,
	),
	
	'save' => array(
		'CType' => 'action',
		'DisplayName' => getstr('admin.saveButton'),
		'SQL' => 'SELECT * FROM spIndesignTemplateDetails(1, {id}, {template_id}, {name}, {node_id}, {style}, {type}, {parent_path}, {new_parent}, {change_before}, {change_after}, {special})',
		'ActionMask' => ACTION_CHECK | ACTION_CCHECK | ACTION_EXEC | ACTION_FETCH  ,
		'RedirUrl' => '',
		'AddTags' => array(
			'class' => 'frmbutton',			
		), 
	),
	
	'delete' => array(
		'CType' => 'action',
		'DisplayName' => getstr('admin.deleteButton'),
		'SQL' => 'SELECT * FROM spIndesignTemplateDetails(3, {id}, null, null, null, null, null, null, null, null, null, null)',
		'ActionMask' => ACTION_CHECK | ACTION_CCHECK | ACTION_EXEC | ACTION_FETCH | ACTION_REDIRECT  ,
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
			<th colspan="4">' . ((int)$gKfor->lFieldArr['id']['CurValue'] ? getstr('admin.indesign_templates_details.editLabel') : getstr('admin.indesign_templates_details.addLabel') ) . getstr('admin.indesign_templates_details.nameLabel') . '</th>
		</tr>
		<tr>
			<td colspan="4" valign="top"><b>{*name}:</b><br/>{name}</td>
		</tr>	
		<tr>
			<td colspan="2" valign="top"><b>{*template_id}:</b><br/>{template_id}</td>
			<td colspan="2" valign="top"><b>{*node_id}:</b><br/>{node_id}</td>
		</tr>
		<tr>
			<td colspan="2" valign="top"><b>{*type}:</b><br/>{type}</td>
			<td colspan="2" valign="top"><b>{*style}:</b><br/>{style}</td>
		</tr>
		<tr>			
			<td colspan="2" valign="top"><b>{*change_before}:</b><br/>{change_before}</td>			
			<td colspan="2" valign="top"><b>{*change_after}:</b><br/>{change_after}</td>			
		</tr>
		<tr>
			<td colspan="2" valign="top"><b>{*new_parent}:</b><br/>{new_parent}</td>
			<td colspan="2" valign="top"><b>{*special}:</b><br/>{special}</td>						
		</tr>
		<tr>
			<td colspan="4" valign="top"><b>{*parent_path}:</b><br/>{parent_path}</td>			
		</tr>		
		<tr>
			<td colspan="2">&nbsp;</td>
			<td colspan="2" align="right">{save} ' . ((int)$gKfor->lFieldArr['id']['CurValue'] ? '{delete}' : '') . '				
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