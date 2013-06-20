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
	'node_id' => array(
		'VType' => 'int',
		'CType' => 'select',
		'SrcValues' => 'SELECT id, name FROM xml_nodes',
		'DisplayName' => getstr('admin.xml_attributes.colNodeId'),
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
		'DisplayName' => getstr('admin.xml_attributes.colName'),
	),
	
	'showedit' => array(
		'CType' => 'action',
		'Hidden' => true,
		'DisplayName' => '',
		'SQL' => 'SELECT * FROM spXmlAttributes(0, {id}, null, null)',
		'ActionMask' => ACTION_CHECK | ACTION_EXEC | ACTION_FETCH | ACTION_SHOW,
	),
	
	'save' => array(
		'CType' => 'action',
		'DisplayName' => getstr('admin.saveButton'),
		'SQL' => 'SELECT * FROM spXmlAttributes(1, {id}, {node_id}, {name})',
		'ActionMask' => ACTION_CHECK | ACTION_CCHECK | ACTION_EXEC | ACTION_FETCH  ,
		'RedirUrl' => '',
		'AddTags' => array(
			'class' => 'frmbutton',			
		), 
	),
	
	'delete' => array(
		'CType' => 'action',
		'DisplayName' => getstr('admin.deleteButton'),
		'SQL' => 'SELECT * FROM spXmlAttributes(3, {id}, null, null)',
		'ActionMask' => ACTION_CHECK | ACTION_CCHECK | ACTION_EXEC | ACTION_FETCH | ACTION_REDIRECT  ,
		'RedirUrl' => '',
		'AddTags' => array(
			'class' => 'frmbutton',			
		), 
	),
	
);

$gKfor = new kfor($gKforFlds, null, 'POST');
$gKfor->debug = false;


if ($gKfor->lCurAction == 'save') {
	// Parsvane na special quotes:
	$lStartMatch = 'A-Z:_a-z';
	$lOtherMatch = $lStartMatch . '0-9\-\.';
	if( !preg_match('/^[' . $lStartMatch . '][' . $lOtherMatch . ']*$/', $gKfor->lFieldArr['name']['CurValue']) ){
		$gKfor->SetError('name', getstr('admin.xml_attributes.wrong_node_name'));
	}	
}

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
			<th colspan="4">' . ((int)$gKfor->lFieldArr['id']['CurValue'] ? getstr('admin.xml_attributes.editLabel') : getstr('admin.xml_attributes.addLabel') ) . getstr('admin.xml_attributes.nameLabel') . '</th>
		</tr>
		<tr>
			<td colspan="4" valign="top"><b>{*name}:</b><br/>{name}</td>
		</tr>	
		<tr>
			<td colspan="4" valign="top"><b>{*node_id}:</b><br/>{node_id}</td>
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