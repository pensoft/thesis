<?php
$docroot = getenv('DOCUMENT_ROOT');
 require_once($docroot . '/lib/static.php');


global $user, $gUrl;
$gRuleId = (int) $_REQUEST['rule_id'];
$t = array(
	'rule_id' => array(
		'VType' => 'int',
		'CType' => 'hidden',
		'DisplayName' => '',
		'AllowNulls' => true,
	),
	'property_id' => array(
		'VType' => 'int',
		'CType' => 'hidden',
		'DisplayName' => '',
		'AllowNulls' => true,
	),
	'delete_property' => array(
		'CType' => 'action',
		'DisplayName' => getstr('admin.deleteButton'),
		'SQL' => 'SELECT * FROM spRuleAddProperty(3, {rule_id}, {property_id}, null, null, null)',
		'ActionMask' => ACTION_CHECK | ACTION_EXEC | ACTION_FETCH | ACTION_REDIRECT,
		'RedirUrl' => './relproperties.php?rule_id=' . $gRuleId ,
		'Hidden' => true,
	),
);
$h = '{rule_id}{property_id}';
$f = new kfor($t, $h, 'GET');
$f->Display();

HtmlStart(1);


$t = '<tr>
		<td valign="top">{type}</td>
		<td valign="top">{_BuildPropertyLink}</td>
		<td valign="top">{modifier}</td>
		<td valign="top">{priority}</td>
		<td align="right" valign="top"> 
			<a href="javascript:openw(\'/resources/autotag_rules/property_edit.php?property_id={property_id}&rule_id=' . $gRuleId . '&tAction=showedit\', \'aa\', \'location=no,menubar=yes,width=800,height=600,scrollbars=yes,resizable=yes,top=0,left=0\')"">
				<img src="/img/edit.gif" alt="' . getstr('admin.editButton') . '" title="' . getstr('admin.editButton') . '" border="0" />
			</a>
			<a href="javascript:if (confirm(\'' . getstr('admin.autotag_rules_properties.ConfirmDel') . '\')) { window.location = \'/resources/autotag_rules/relproperties.php?property_id={property_id}&rule_id=' . $gRuleId . '&tAction=delete_property\';} else {}">
				<img src="/img/trash2.gif" alt="' . getstr('admin.deleteButton') . '" title="' . getstr('admin.deleteButton') . '" border="0" />
			</a>
		</td>
	</tr>
';

$gFArr = array(
	3 => array('caption' => getstr('admin.autotag_rules_properties.colType'), 'def', 'deforder' => 'asc'), 
	1 => array('caption' => getstr('admin.autotag_rules_properties.colPropertyName'), 'deforder' => 'asc'), 		
	4 => array('caption' => getstr('admin.autotag_rules_properties.colModifier'), 'deforder' => 'asc'), 
	5 => array('caption' => getstr('admin.autotag_rules_properties.colPriority'), 'deforder' => 'asc'), 
	1000 => array('caption' => '  ', 'deforder' => 'asc'),
);

$lTableHeader = '
	<a name="attributes"></a>
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
					<th class="gridtools" colspan="5">
						<a href="javascript:openw(\'/resources/autotag_rules/property_edit.php?rule_id=' . $gRuleId . '\', \'aa\', \'location=no,menubar=yes,width=600,height=400,scrollbars=yes,resizable=yes,top=0,left=0\')">' . getstr('admin.autotag_rules_properties.addProperty') . '</a>
						' . getstr('admin.autotag_rules_properties.antetka') . '
					</th>
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
$l->SetTemplate($t);
$l->SetOrderParams((int)$_GET['ordby'], (int)$_GET['ordd']);
$l->SetAntet($gFArr);
$l->SetQuery('SELECT coalesce(p1.name, p2.name, p3.name) as property_name, coalesce(p1.id, p2.id, p3.id) as property_id, t.name as type, m.name as modifier, p.priority, t.id as type_id
	FROM autotag_rules r
	JOIN autotag_rules_properties p ON p.rule_id = r.id
	JOIN autotag_property_types t ON t.id = p.property_type_id
	LEFT JOIN autotag_property_modifiers m ON m.id = p.property_modifier_id
	LEFT JOIN place_rules p1 ON p1.id = p.property_id
	LEFT JOIN regular_expressions p2 ON p2.id = p.property_id
	LEFT JOIN autotag_re_sources p3 ON p3.id = p.property_id
	WHERE r.id = ' . $gRuleId . ' AND coalesce(p1.id, p2.id, p3.id, 0) <> 0 '
);

if (!$l->DisplayList($page)) {
	echo $lTableHeader . '<tr><td colspan="4"><p align="center"><b>' . getstr('admin.autotag_rules_properties.noData') . '</b></p></td></tr>' . $lTableFooter;
}

HtmlEnd(1);
?>