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

	'level' => array(
		'VType' => 'int',
		'CType' => 'hidden',
		'DisplayName' => '',
		'AllowNulls' => true,
	),

	'template_id' => array(
		'VType' => 'int',
		'CType' => 'hidden',
		'DisplayName' => '',
		'AllowNulls' => false,
	),

	'object_id' => array(
		'VType' => 'int',
		'CType' => 'select',
		'AddTags' => array(
			'class' => 'coolinp',
		),
		'SrcValues' => 'SELECT null as id, \'----\' as name UNION
			SELECT id, name FROM objects ORDER BY id ASC',
		'DisplayName' => getstr('pwt_admin.templates.objects.colName'),
	),

	'default_mode_id' => array(
		'VType' => 'int',
		'CType' => 'select',
		'AddTags' => array(
			'class' => 'coolinp',
		),
		'SrcValues' => 'SELECT null as id, \'----\' as name UNION
			SELECT id, name FROM pwt.modes',
		'DisplayName' => getstr('pwt_admin.templates.objects.colDefaultMode'),
	),

	'default_new_mode_id' => array(
		'VType' => 'int',
		'CType' => 'select',
		'AddTags' => array(
			'class' => 'coolinp',
		),
		'SrcValues' => 'SELECT null as id, \'----\' as name UNION
		SELECT id, name FROM pwt.modes',
		'DisplayName' => getstr('pwt_admin.templates.objects.colDefaultNewMode'),
	),

	'allowed_modes' => array(
		'VType' => 'int',
		'CType' => 'mselect',
		'TransType' => MANY_TO_SQL_ARRAY,
		'AddTags' => array(
			'class' => 'coolinp',
		),
		'SrcValues' => '
			SELECT id, name FROM pwt.modes',
		'DisplayName' => getstr('pwt_admin.templates.objects.colAllowedModes'),
	),

	'display_name' => array(
		'VType' => 'string',
		'CType' => 'text',
		'AddTags' => array(
			'class' => 'coolinp',
		),
		'DisplayName' => getstr('pwt_admin.templates.objects.colDisplayName'),
	),


	'display_in_tree' => array(
		'VType' => 'int',
		'CType' => 'select',
		'AddTags' => array(
			'class' => 'coolinp',
		),
		'SrcValues' => array(
			0 => getstr('global.no'),
			1 => getstr('global.yes'),
		),

		'DisplayName' => getstr('pwt_admin.templates.objects.colDisplayInTree'),
	),

	'allow_movement' => array(
		'VType' => 'int',
		'CType' => 'select',
		'AddTags' => array(
			'class' => 'coolinp',
		),
		'SrcValues' => array(
			0 => getstr('global.no'),
			1 => getstr('global.yes'),
		),

		'DisplayName' => getstr('pwt_admin.templates.objects.colAllowMovement'),
	),

	'allow_add' => array(
		'VType' => 'int',
		'CType' => 'select',
		'AddTags' => array(
			'class' => 'coolinp',
		),
		'SrcValues' => array(
			0 => getstr('global.no'),
			1 => getstr('global.yes'),
		),

		'DisplayName' => getstr('pwt_admin.templates.objects.colAllowAdd'),
	),

	'title_display_style' => array(
		'VType' => 'int',
		'CType' => 'select',
		'AddTags' => array(
			'class' => 'coolinp',
		),
		'SrcValues' => 'SELECT id, name FROM pwt.object_title_display_style',

		'DisplayName' => getstr('pwt_admin.templates.objects.colTitleDisplayStyle'),
	),

	'allow_remove' => array(
		'VType' => 'int',
		'CType' => 'select',
		'AddTags' => array(
			'class' => 'coolinp',
		),
		'SrcValues' => array(
			0 => getstr('global.no'),
			1 => getstr('global.yes'),
		),

		'DisplayName' => getstr('pwt_admin.templates.objects.colAllowRemove'),
	),

	'display_title_and_top_actions' => array(
		'VType' => 'int',
		'CType' => 'select',
		'AddTags' => array(
			'class' => 'coolinp',
		),
		'SrcValues' => array(
			0 => getstr('global.no'),
			1 => getstr('global.yes'),
		),

		'DisplayName' => getstr('pwt_admin.templates.objects.colDisplayTitleAndTopActions'),
	),

	'display_default_actions' => array(
		'VType' => 'int',
		'CType' => 'select',
		'AddTags' => array(
			'class' => 'coolinp',
		),
		'SrcValues' => array(
			0 => getstr('global.no'),
			1 => getstr('global.yes'),
		),

		'DisplayName' => getstr('pwt_admin.templates.objects.colDisplayDefaultActions'),
	),

	'default_actions_type' => array(
		'VType' => 'int',
		'CType' => 'select',
		'AddTags' => array(
			'class' => 'coolinp',
		),
		'SrcValues' => 'SELECT id, name FROM pwt.object_default_actions_type',

		'DisplayName' => getstr('pwt_admin.templates.objects.colDefaultActionsType'),
	),

	'displayed_actions_type' => array(
		'VType' => 'int',
		'CType' => 'select',
		'AddTags' => array(
			'class' => 'coolinp',
		),
		'SrcValues' => 'SELECT id, name FROM pwt.object_displayed_actions_types',

		'DisplayName' => getstr('pwt_admin.templates.objects.colDisplayedActionsType'),
	),

	'limit_new_object_creation' => array(
		'VType' => 'int',
		'CType' => 'select',
		'AddTags' => array(
			'class' => 'coolinp',
		),
		'SrcValues' => array(
			0 => getstr('global.no'),
			1 => getstr('global.yes'),
		),
		'DisplayName' => getstr('pwt_admin.templates.objects.colLimitNewObjectCreation'),
	),

	'showedit' => array(
		'CType' => 'action',
		'Hidden' => true,
		'DisplayName' => '',
		'SQL' => 'SELECT * FROM spTemplateObject(0, {id}, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null)',
		'ActionMask' => ACTION_CHECK | ACTION_EXEC | ACTION_FETCH | ACTION_SHOW,
	),

	'save' => array(
		'CType' => 'action',
		'DisplayName' => getstr('admin.saveButton'),
		'SQL' => 'SELECT * FROM spTemplateObject(1, {id}, {template_id}, {object_id}, {display_in_tree}, {allow_movement}, {allow_add},
			 {allow_remove}, {display_title_and_top_actions}, {display_default_actions}, {display_name}, {default_mode_id}, {default_new_mode_id}, {allowed_modes},
			 {title_display_style}, {default_actions_type}, {displayed_actions_type}, {limit_new_object_creation},
		' . (int)$user->id . ')',
		'ActionMask' => ACTION_CHECK | ACTION_CCHECK | ACTION_EXEC | ACTION_FETCH | ACTION_REDIRECT ,
		'RedirUrl' => '',
		'AddTags' => array(
			'class' => 'frmbutton',
		),
	),

	'delete' => array(
		'CType' => 'action',
		'DisplayName' => getstr('admin.saveButton'),
		'SQL' => 'SELECT * FROM spTemplateObject(3, {id}, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null)',
		'ActionMask' => ACTION_CHECK | ACTION_CCHECK | ACTION_EXEC | ACTION_FETCH | ACTION_REDIRECT ,
		'RedirUrl' => '',
		'AddTags' => array(
			'class' => 'frmbutton',
		),
	),


	'cancel' => array(
		'CType' => 'action',
		'DisplayName' => getstr('admin.backButton'),
		'ActionMask' => ACTION_REDIRECT,
		'RedirUrl' => './',
		'AddTags' => array(
			'class' => 'frmbutton',
		),
	),
);

$gKfor = new kfor($gKforFlds, null, 'GET');
$gKfor->debug = false;



$gKfor->ExecAction();

if($gKfor->lFieldArr['level']['CurValue'] > 1){
//	$gKfor->lFieldArr['object_id']['AddTags']['disabled'] = 'disabled';
}

$gKforTpl = '
{id}{template_id}
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
			<th colspan="4">' . ((int)$gKfor->lFieldArr['id']['CurValue'] ? getstr('pwt_admin.templates.objects.editLabel') : getstr('pwt_admin.templates.objects.addLabel') ) . getstr('pwt_admin.templates.objects.nameLabel') . '</th>
		</tr>
		<tr>
			<td colspan="2" valign="top"><b>{*object_id}:</b><br/>{object_id}</td>
			<td colspan="2" valign="top"><b>{*display_name}:</b><br/>{display_name}</td>
		</tr>
		<tr>
			<td colspan="2" valign="top"><b>{*allowed_modes}:</b><br/>{allowed_modes}</td>
		</tr>
		<tr>
			<td colspan="2" valign="top"><b>{*default_mode_id}:</b><br/>{default_mode_id}</td>
			<td colspan="2" valign="top"><b>{*default_new_mode_id}:</b><br/>{default_new_mode_id}</td>
		</tr>
		<tr>
			<td colspan="2" valign="top"><b>{*display_in_tree}:</b><br/>{display_in_tree}</td>
			<td colspan="2" valign="top"><b>{*allow_movement}:</b><br/>{allow_movement}</td>
		</tr>
		<tr>
			<td colspan="2" valign="top"><b>{*allow_add}:</b><br/>{allow_add}</td>
			<td colspan="2" valign="top"><b>{*allow_remove}:</b><br/>{allow_remove}</td>
		</tr>
		<tr>
			<td colspan="2" valign="top"><b>{*display_title_and_top_actions}:</b><br/>{display_title_and_top_actions}</td>
			<td colspan="2" valign="top"><b>{*display_default_actions}:</b><br/>{display_default_actions}</td>
		</tr>
		<tr>
			<td colspan="2" valign="top"><b>{*title_display_style}:</b><br/>{title_display_style}</td>
			<td colspan="2" valign="top"><b>{*default_actions_type}:</b><br/>{default_actions_type}</td>
		</tr>
		<tr>
			<td colspan="2" valign="top"><b>{*displayed_actions_type}:</b><br/>{displayed_actions_type}</td>
			<td colspan="2" valign="top"><b>{*limit_new_object_creation}:</b><br/>{limit_new_object_creation}</td>
		</tr>
		<tr>
			<td colspan="2">&nbsp;</td>
			<td colspan="2" align="right">{save} {cancel}
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

HtmlEnd();


?>