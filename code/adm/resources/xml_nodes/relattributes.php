<?php
$docroot = getenv('DOCUMENT_ROOT');
 require_once($docroot . '/lib/static.php');


global $user, $gUrl;
UserRedir($user);
ProccessHistory();
$gXmlNodeId = (int) $_REQUEST['node_id'];
$t = array(
	'node_id' => array(
		'VType' => 'int',
		'CType' => 'hidden',
		'DisplayName' => '',
		'AllowNulls' => true,
	),
	'attribute_id' => array(
		'VType' => 'int',
		'CType' => 'hidden',
		'DisplayName' => '',
		'AllowNulls' => true,
	),
	'delete_attribute' => array(
		'CType' => 'action',
		'DisplayName' => getstr('admin.deleteButton'),
		'SQL' => 'SELECT * FROM spXmlAttributes(3, {attribute_id}, null, null)',
		'ActionMask' => ACTION_CHECK | ACTION_EXEC | ACTION_FETCH | ACTION_REDIRECT,
		'RedirUrl' => './relattributes.php?node_id=' . $gXmlNodeId ,
		'Hidden' => true,
	),
);
$h = '{node_id}{attribute_id}';
$f = new kfor($t, $h, 'GET');
$f->Display();


HtmlStart(1);


$t = '<tr>
		<td valign="top">{id}</td>
		<td valign="top"><a href="{url}" target="_blank">{name}</a></td>
		<td valign="top">{createdate}</td>
		<td align="right" valign="top"> 
			<a href="javascript:openw(\'/resources/xml_nodes/attributes_edit.php?id={id}&node_id=' . $gXmlNodeId . '&tAction=showedit\', \'aa\', \'location=no,menubar=yes,width=800,height=600,scrollbars=yes,resizable=yes,top=0,left=0\')"">
				<img src="/img/edit.gif" alt="' . getstr('admin.editButton') . '" title="' . getstr('admin.editButton') . '" border="0" />
			</a>
			<a href="javascript:if (confirm(\'' . getstr('admin.xml_attributes.xml_attributeConfirmDel') . '\')) { window.location = \'/resources/xml_nodes/relattributes.php?attribute_id={id}&node_id=' . $gXmlNodeId . '&tAction=delete_attribute\';} else {}">
				<img src="/img/trash2.gif" alt="' . getstr('admin.deleteButton') . '" title="' . getstr('admin.deleteButton') . '" border="0" />
			</a>
		</td>
	</tr>
';

$gFArr = array(
	1 => array('caption' => getstr('admin.xml_attributes.colID'), 'def', 'deforder' => 'asc'), 
	3 => array('caption' => getstr('admin.xml_attributes.colName'), 'deforder' => 'asc'), 
	2 => array('caption' => getstr('admin.xml_attributes.colCreateDate'), 'deforder' => 'asc'), 
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
					<th class="gridtools" colspan="4">
						<a href="javascript:openw(\'/resources/xml_nodes/attributes_edit.php?node_id=' . $gXmlNodeId . '\', \'aa\', \'location=no,menubar=yes,width=600,height=400,scrollbars=yes,resizable=yes,top=0,left=0\')">' . getstr('admin.xml_attributes.addAttribute') . '</a>
						' . getstr('admin.xml_attributes.antetka') . '
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
$l->SetQuery('SELECT a.id, date_trunc(\'seconds\', a.createdate) as createdate, a.name
	FROM xml_attributes a
	JOIN xml_nodes n ON a.node_id = n.id
	WHERE n.id = ' . $gXmlNodeId
);

if (!$l->DisplayList($page)) {
	echo $lTableHeader . '<tr><td colspan="4"><p align="center"><b>' . getstr('admin.xml_attributes.noData') . '</b></p></td></tr>' . $lTableFooter;
}

HtmlEnd(1);

?>