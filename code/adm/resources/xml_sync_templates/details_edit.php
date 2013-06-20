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
		'SrcValues' => 'SELECT id, name FROM xml_sync_templates',
		'DisplayName' => getstr('admin.xml_sync_details.colTemplateId'),
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
		'DisplayName' => getstr('admin.xml_sync_details.colName'),
	),
	
	'sync_column_name' => array(
		'VType' => 'string',
		'CType' => 'select',
		'SrcValues' => ' SELECT null as id, \'----\' as name UNION (SELECT
			     a.attname as id,
			     a.attname as name
			 FROM
			     pg_catalog.pg_attribute a
			 WHERE
				a.attnum > 0
				AND NOT a.attisdropped
				AND a.attrelid = (
					SELECT c.oid
					FROM pg_catalog.pg_class c
					LEFT JOIN pg_catalog.pg_namespace n ON n.oid = c.relnamespace
					WHERE c.relname =\'articles\'
					AND pg_catalog.pg_table_is_visible(c.oid)
				)
				AND a.attname != \'id\'
				AND a.attname != \'createuid\'
				AND a.attname != \'createdate\'
				AND a.attname != \'lastmod\'
				AND a.attname != \'xml_sync_template_id\'
				AND a.attname != \'xml_content\')
				ORDER BY id DESC
			;
		',
		'AddTags' => array(
			'class' => 'coolinp',
		),
		'AllowNulls' => true,
		'DisplayName' => getstr('admin.xml_sync_details.colSyncColumnName'),
	),
	
	'sync_column_default_value' => array(
		'VType' => 'string',
		'CType' => 'text',		
		'AddTags' => array(
			'class' => 'coolinp',
		),
		'AllowNulls' => true,
		'DisplayName' => getstr('admin.xml_sync_details.colSyncColumnDefaultValue'),
	),
	
	'xpath' => array(
		'VType' => 'string',
		'CType' => 'text',
		'AddTags' => array(
			'class' => 'coolinp',
		),
		'DisplayName' => getstr('admin.xml_sync_details.colXPath'),
	),
	
	'sync_type' => array(
		'VType' => 'int',
		'CType' => 'select',
		'AddTags' => array(
			'class' => 'coolinp',
		),
		'SrcValues' => 'SELECT id, name FROM xml_sync_types',
		'DisplayName' => getstr('admin.xml_sync_details.colSyncType'),
	),
	
	'showedit' => array(
		'CType' => 'action',
		'Hidden' => true,
		'DisplayName' => '',
		'SQL' => 'SELECT * FROM spXmlSyncDetails(0, {id}, null, null, null, null, null, null)',
		'ActionMask' => ACTION_CHECK | ACTION_EXEC | ACTION_FETCH | ACTION_SHOW,
	),
	
	'save' => array(
		'CType' => 'action',
		'DisplayName' => getstr('admin.saveButton'),
		'SQL' => 'SELECT * FROM spXmlSyncDetails(1, {id}, {template_id}, {name}, {xpath}, {sync_type}, {sync_column_name}, {sync_column_default_value})',
		'ActionMask' => ACTION_CHECK | ACTION_CCHECK | ACTION_EXEC | ACTION_FETCH  ,
		'RedirUrl' => '',
		'AddTags' => array(
			'class' => 'frmbutton',			
		), 
	),
	
	'delete' => array(
		'CType' => 'action',
		'DisplayName' => getstr('admin.deleteButton'),
		'SQL' => 'SELECT * FROM spXmlSyncDetails(3, {id}, null, null, null, null, null, null)',
		'ActionMask' => ACTION_CHECK | ACTION_CCHECK | ACTION_EXEC | ACTION_FETCH | ACTION_REDIRECT  ,
		'RedirUrl' => '',
		'AddTags' => array(
			'class' => 'frmbutton',			
		), 
	),
	
);

$gKfor = new kfor($gKforFlds, null, 'POST');
$gKfor->debug = false;

if( $gKfor->lFieldArr['sync_type']['CurValue'] == (int) XML_SYNC_COLUMN_TYPE){	
	$gKfor->lFieldArr['sync_column_name']['AllowNulls'] = false;
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
			<th colspan="4">' . ((int)$gKfor->lFieldArr['id']['CurValue'] ? getstr('admin.xml_sync_details.editLabel') : getstr('admin.xml_sync_details.addLabel') ) . getstr('admin.xml_sync_details.nameLabel') . '</th>
		</tr>
		<tr>
			<td colspan="4" valign="top"><b>{*name}:</b><br/>{name}</td>
		</tr>	
		<tr>
			<td colspan="4" valign="top"><b>{*template_id}:</b><br/>{template_id}</td>
		</tr>
		<tr>
			<td colspan="4" valign="top"><b>{*xpath}:</b><br/>{xpath}</td>
		</tr>		
		<tr>
			<td colspan="4" valign="top"><b>{*sync_type}:</b><br/>{sync_type}</td>			
		</tr>
		<tr>
			<td colspan="2" valign="top"><b>{*sync_column_name}:</b><br/>{sync_column_name}</td>
			<td colspan="2" valign="top"><b>{*sync_column_default_value}:</b><br/>{sync_column_default_value}</td>
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