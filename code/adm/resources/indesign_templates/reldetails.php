<?php
$docroot = getenv('DOCUMENT_ROOT');
 require_once($docroot . '/lib/static.php');


global $user, $gUrl;
UserRedir($user);
ProccessHistory();
$gTemplateId = (int) $_REQUEST['template_id'];
$t = array(
	'template_id' => array(
		'VType' => 'int',
		'CType' => 'hidden',
		'DisplayName' => '',
		'AllowNulls' => true,
	),
	'detail_id' => array(
		'VType' => 'int',
		'CType' => 'hidden',
		'DisplayName' => '',
		'AllowNulls' => true,
	),
	'delete_detail' => array(
		'CType' => 'action',
		'DisplayName' => getstr('admin.deleteButton'),
		'SQL' => 'SELECT * FROM spIndesignTemplateDetails(3, {detail_id}, null, null, null, null, null, null, null, null, null, null)',
		'ActionMask' => ACTION_CHECK | ACTION_EXEC | ACTION_FETCH | ACTION_REDIRECT,
		'RedirUrl' => './reldetails.php?template_id=' . $gTemplateId ,
		'Hidden' => true,
	),
);
$h = '{template_id}{detail_id}';
$f = new kfor($t, $h, 'GET');
$f->Display();
$f->debug = 1;
//~ exit;

HtmlStart(1);


$t = '<tr>
		<td valign="top">{id}</td>
		<td valign="top">
			<a href="javascript:openw(\'/resources/indesign_templates/details_edit.php?id={id}&template_id=' . $gTemplateId . '&tAction=showedit\', \'aa\', \'location=no,menubar=yes,width=800,height=600,scrollbars=yes,resizable=yes,top=0,left=0\')"">
				{name}
			</a>
		</td>		
		<td valign="top">{style}</td>		
		<td valign="top">{type}</td>		
		<td valign="top">{xmlnode}</td>		
		<td valign="top">{parent_path}</td>
		<td valign="top">{_showNewParent}</td>
		<td valign="top">{_showChangeBefore}</td>		
		<td valign="top">{change_after}</td>
		<td valign="top">{_showSpecial}</td>		
		<td align="right" valign="top"> 
			<a href="javascript:openw(\'/resources/indesign_templates/details_edit.php?id={id}&template_id=' . $gTemplateId . '&tAction=showedit\', \'aa\', \'location=no,menubar=yes,width=800,height=600,scrollbars=yes,resizable=yes,top=0,left=0\')"">
				<img src="/img/edit.gif" alt="' . getstr('admin.editButton') . '" title="' . getstr('admin.editButton') . '" border="0" />
			</a>
			<a href="javascript:if (confirm(\'' . getstr('admin.indesign_templates_details.ConfirmDel') . '\')) { window.location = \'/resources/indesign_templates/reldetails.php?detail_id={id}&template_id=' . $gTemplateId . '&tAction=delete_detail\';} else {}">
				<img src="/img/trash2.gif" alt="' . getstr('admin.deleteButton') . '" title="' . getstr('admin.deleteButton') . '" border="0" />
			</a>
		</td>
	</tr>
';

$gFArr = array(
	1 => array('caption' => getstr('admin.indesign_templates_details.colID'), 'def', 'deforder' => 'asc'), 
	2 => array('caption' => getstr('admin.indesign_templates_details.colName'), 'deforder' => 'asc'), 
	3 => array('caption' => getstr('admin.indesign_templates_details.colStyle'), 'deforder' => 'asc'), 
	4 => array('caption' => getstr('admin.indesign_templates_details.colType'), 'deforder' => 'asc'), 
	5 => array('caption' => getstr('admin.indesign_templates_details.colXMLNode'), 'deforder' => 'asc'), 
	6 => array('caption' => getstr('admin.indesign_templates_details.colParentPath'), 'deforder' => 'asc'), 
	7 => array('caption' => getstr('admin.indesign_templates_details.colNewParent'), 'deforder' => 'asc'), 
	8 => array('caption' => getstr('admin.indesign_templates_details.colChangeBefore'), 'deforder' => 'asc'), 
	9 => array('caption' => getstr('admin.indesign_templates_details.colChangeAfter'), 'deforder' => 'asc'), 
	10 => array('caption' => getstr('admin.indesign_templates_details.colSpecial'), 'deforder' => 'asc'), 
	1000 => array('caption' => '  ', 'deforder' => 'asc'),
);

$lTableHeader = '
	<a name="details"></a>
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
					<th class="gridtools" colspan="11">
						<a href="javascript:openw(\'/resources/indesign_templates/details_edit.php?template_id=' . $gTemplateId . '\', \'aa\', \'location=no,menubar=yes,width=600,height=400,scrollbars=yes,resizable=yes,top=0,left=0\')">' . getstr('admin.indesign_templates_details.addItem') . '</a>
						' . getstr('admin.indesign_templates_details.antetka') . '
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
$l->SetQuery('SELECT a.id, a.name, a.style, a.type, n.name as xmlnode, a.parent_path, a.new_parent, a.change_before, a.change_after, a.special
	FROM indesign_template_details a
	JOIN xml_nodes n ON a.node_id= n.id
	WHERE a.indesign_templates_id = ' . $gTemplateId
);

if (!$l->DisplayList($page)) {
	echo $lTableHeader . '<tr><td colspan="4"><p align="center"><b>' . getstr('admin.indesign_templates_details.noData') . '</b></p></td></tr>' . $lTableFooter;
}

HtmlEnd(1);
?>