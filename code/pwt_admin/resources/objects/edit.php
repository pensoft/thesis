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

	'name' => array(
		'VType' => 'string',
		'CType' => 'text',
		'AddTags' => array(
			'class' => 'coolinp',
		),
		'DisplayName' => getstr('pwt_admin.objects.colName'),
	),

	'default_display_name' => array(
		'VType' => 'string',
		'CType' => 'text',
		'AddTags' => array(
			'class' => 'coolinp',
		),
		'DisplayName' => getstr('pwt_admin.objects.colDefaultDisplayName'),
	),

	'default_mode_id' => array(
		'VType' => 'int',
		'CType' => 'select',
		'AddTags' => array(
			'class' => 'coolinp',
		),
		'SrcValues' => 'SELECT null as id, \'----\' as name UNION
			SELECT id, name FROM pwt.modes',
		'DisplayName' => getstr('pwt_admin.objects.colDefaultMode'),
	),

	'default_new_mode_id' => array(
		'VType' => 'int',
		'CType' => 'select',
		'AddTags' => array(
			'class' => 'coolinp',
		),
		'SrcValues' => 'SELECT null as id, \'----\' as name UNION
		SELECT id, name FROM pwt.modes',
		'DisplayName' => getstr('pwt_admin.objects.colDefaultNewMode'),
	),

	'default_allowed_modes' => array(
		'VType' => 'int',
		'CType' => 'mselect',
		'TransType' => MANY_TO_SQL_ARRAY,
		'AddTags' => array(
			'class' => 'coolinp',
		),
		'SrcValues' => '
			SELECT id, name FROM pwt.modes',
		'DisplayName' => getstr('pwt_admin.objects.colDefaultAllowedModes'),
	),

	'default_display_in_tree' => array(
		'VType' => 'int',
		'CType' => 'select',
		'AddTags' => array(
			'class' => 'coolinp',
		),
		'SrcValues' => array(
			0 => getstr('global.no'),
			1 => getstr('global.yes'),
		),
		'DisplayName' => getstr('pwt_admin.objects.colDefaultDisplayInTree'),
	),

	'default_allow_movement' => array(
		'VType' => 'int',
		'CType' => 'select',
		'AddTags' => array(
			'class' => 'coolinp',
		),
		'SrcValues' => array(
			0 => getstr('global.no'),
			1 => getstr('global.yes'),
		),
		'DisplayName' => getstr('pwt_admin.objects.colDefaultAllowMovement'),
	),

	'default_allow_add' => array(
		'VType' => 'int',
		'CType' => 'select',
		'AddTags' => array(
			'class' => 'coolinp',
		),
		'SrcValues' => array(
			0 => getstr('global.no'),
			1 => getstr('global.yes'),
		),
		'DisplayName' => getstr('pwt_admin.objects.colDefaultAllowAdd'),
	),

	'default_allow_remove' => array(
		'VType' => 'int',
		'CType' => 'select',
		'AddTags' => array(
			'class' => 'coolinp',
		),
		'SrcValues' => array(
			0 => getstr('global.no'),
			1 => getstr('global.yes'),
		),
		'DisplayName' => getstr('pwt_admin.objects.colDefaultAllowRemove'),
	),

	'default_title_display_style' => array(
		'VType' => 'int',
		'CType' => 'select',
		'AddTags' => array(
			'class' => 'coolinp',
		),
		'SrcValues' => 'SELECT id, name FROM pwt.object_title_display_style',
		'DisplayName' => getstr('pwt_admin.objects.colDefaultTitleDisplayStyle'),
	),

	'default_display_title_and_top_actions' => array(
		'VType' => 'int',
		'CType' => 'select',
		'AddTags' => array(
			'class' => 'coolinp',
		),
		'SrcValues' => array(
			0 => getstr('global.no'),
			1 => getstr('global.yes'),
		),
		'DisplayName' => getstr('pwt_admin.objects.colDefaultDisplayTitleAndTopActions'),
	),

	'default_display_default_actions' => array(
		'VType' => 'int',
		'CType' => 'select',
		'AddTags' => array(
			'class' => 'coolinp',
		),
		'SrcValues' => array(
			0 => getstr('global.no'),
			1 => getstr('global.yes'),
		),
		'DisplayName' => getstr('pwt_admin.objects.colDefaultDisplayDefaultActions'),
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

	'default_displayed_actions_type' => array(
		'VType' => 'int',
		'CType' => 'select',
		'AddTags' => array(
			'class' => 'coolinp',
		),
		'SrcValues' => 'SELECT id, name FROM pwt.object_displayed_actions_types',

		'DisplayName' => getstr('pwt_admin.templates.objects.colDefaultDisplayedActionsType'),
	),

	'default_limit_new_object_creation' => array(
		'VType' => 'int',
		'CType' => 'select',
		'AddTags' => array(
			'class' => 'coolinp',
		),
		'SrcValues' => array(
			0 => getstr('global.no'),
			1 => getstr('global.yes'),
		),
		'DisplayName' => getstr('pwt_admin.objects.colDefaultLimitNewObjectCreation'),
	),

	'showedit' => array(
		'CType' => 'action',
		'Hidden' => true,
		'DisplayName' => '',
		'SQL' => 'SELECT * FROM spObjects(0, {id}, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null)',
		'ActionMask' => ACTION_CHECK | ACTION_EXEC | ACTION_FETCH | ACTION_SHOW,
	),

	'save' => array(
		'CType' => 'action',
		'DisplayName' => getstr('admin.saveButton'),
		'SQL' => 'SELECT * FROM spObjects(1, {id}, {name}, {default_display_name}, {default_mode_id}, {default_new_mode_id}, {default_allowed_modes},
				{default_display_in_tree}, {default_allow_movement}, {default_allow_add}, {default_allow_remove}, {default_display_title_and_top_actions}, {default_display_default_actions},
				{default_title_display_style}, {default_actions_type}, {default_displayed_actions_type}, {default_limit_new_object_creation},
		' . (int)$user->id . ')',
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
			<th colspan="4">' . ((int)$gKfor->lFieldArr['id']['CurValue'] ? getstr('pwt_admin.objects.editLabel') : getstr('pwt_admin.objects.addLabel') ) . getstr('pwt_admin.objects.nameLabel') . '</th>
		</tr>
		<tr>
			<td colspan="2" valign="top"><b>{*name}:</b><br/>{name}</td>
			<td colspan="2" valign="top"><b>{*default_display_name}:</b><br/>{default_display_name}</td>
		</tr>
		<tr>
			<td colspan="2" valign="top"><b>{*default_allowed_modes}:</b><br/>{default_allowed_modes}</td>
		</tr>
		<tr>
			<td colspan="2" valign="top"><b>{*default_mode_id}:</b><br/>{default_mode_id}</td>
			<td colspan="2" valign="top"><b>{*default_new_mode_id}:</b><br/>{default_new_mode_id}</td>
		</tr>
		<tr>
			<td colspan="2" valign="top"><b>{*default_display_in_tree}:</b><br/>{default_display_in_tree}</td>
			<td colspan="2" valign="top"><b>{*default_allow_movement}:</b><br/>{default_allow_movement}</td>
		</tr>
		<tr>
			<td colspan="2" valign="top"><b>{*default_allow_add}:</b><br/>{default_allow_add}</td>
			<td colspan="2" valign="top"><b>{*default_allow_remove}:</b><br/>{default_allow_remove}</td>
		</tr>
		<tr>
			<td colspan="2" valign="top"><b>{*default_display_title_and_top_actions}:</b><br/>{default_display_title_and_top_actions}</td>
			<td colspan="2" valign="top"><b>{*default_display_default_actions}:</b><br/>{default_display_default_actions}</td>
		</tr>
		<tr>
			<td colspan="2" valign="top"><b>{*default_title_display_style}:</b><br/>{default_title_display_style}</td>
			<td colspan="2" valign="top"><b>{*default_actions_type}:</b><br/>{default_actions_type}</td>
		</tr>
		<tr>
			<td colspan="2" valign="top"><b>{*default_displayed_actions_type}:</b><br/>{default_displayed_actions_type}</td>
			<td colspan="2" valign="top"><b>{*default_limit_new_object_creation}:</b><br/>{default_limit_new_object_creation}</td>
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


if((int)$gKfor->lFieldArr['id']['CurValue']){
	echo GetObjectFields((int)$gKfor->lFieldArr['id']['CurValue']);
	echo GetObjectSubobjects((int)$gKfor->lFieldArr['id']['CurValue']);
}

HtmlEnd();


?>