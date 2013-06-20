<?php
$docroot = getenv("DOCUMENT_ROOT");
require_once($docroot . '/lib/static.php');
$historypagetype = HISTORY_CLEAR;


$gListPage = (int)$_GET['p'];

HtmlStart();

$gKforFlds = array(	
		
	'name' => array(
		'VType' => 'string',
		'CType' => 'text',
		'DisplayName' => getstr('admin.indesign_templates.name'),
		'AddTags' => array(
			'class' => 'coolinp',
		),
		'AllowNulls' => true,
	),
	
	'show' => array(
		'CType' => 'action',
		'DisplayName' => getstr('admin.filterButton'),
		'SQL' => '',
		'ActionMask' => ACTION_CHECK | ACTION_SHOW,
		'AddTags' => array(
			'class' => 'frmbutton',
		),	
	),
);

$gKforTpl = '
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
					<col width="50%"/>
					<col width="50%"/>
				</colgroup>
				<tr>
					<th colspan="2">' . getstr('admin.indesign_templates.filter') . '</th>
				</tr>
				<tr>
					<td>{*name}<br/>{name}</td>					
				</tr>
				<tr>
					<td colspan="2" align="right">{show}</td>
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

$gKfor = new kfor($gKforFlds, $gKforTpl, 'GET');
$gKfor->debug = false;
//~ echo $gKfor->Display();

$lWhereArr = array();


if( $gKfor->lCurAction == 'show' && !$gKfor->lErrorCount){
	if( trim($gKfor->lFieldArr['name']['CurValue'])){		
		$lWhereArr[] = ' n.name ILIKE  \'%' . q(trim($gKfor->lFieldArr['name']['CurValue'])) . '%\'';
	}


}



$lListTpl = '
<tr>
	<td>{id}</td>
	<td><a href="./edit.php?tAction=showedit&id={id}">{name}</a></td>	
	<td>{_GetIndesignTemplateType}</td>	
	<td align="right" nowrap="true">
			<a href="./edit.php?tAction=showedit&id={id}"><img src="/img/edit.gif" alt="' . getstr('admin.editLabel') . '" title="' . getstr('admin.editLabel') . '" border="0" /></a>						
			<a href="javascript:if (confirm(\'' . getstr('admin.indesign_templates.ConfirmDel') . '\')) { window.location = \'/resources/indesign_templates/edit.php?id={id}&tAction=delete\';} else {}">
				<img src="/img/trash2.gif" alt="' . getstr('admin.deleteButton') . '" title="' . getstr('admin.deleteButton') . '" border="0" />
			</a>
	</td>
</tr>
';

$lListAntets = array(
	1 => array('caption' => getstr('admin.indesign_templates.colID'), 'deforder' => 'desc', 'def'), 
	2 => array('caption' => getstr('admin.indesign_templates.colTitle'), 'deforder' => 'asc'), 	
	3 => array('caption' => getstr('admin.indesign_templates.colType'), 'deforder' => 'asc'), 	
	1000 => array('caption' => '  ', 'deforder' => 'asc'),
);


$lTableHeader = '
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
					<th class="gridtools" colspan="10">
						<a href="./edit.php">' . getstr('admin.indesign_templates.addItem') . '</a>
						' . getstr('admin.indesign_templates.antetka') . '
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

if( count( $lWhereArr )){
	$gListSqlWhere = ' WHERE ' . implode(' AND ', $lWhereArr);
}

$lListSql = 'SELECT n.id, n.name, type
FROM indesign_templates n
' . $gListSqlWhere;
//~ echo $lListSql;
$lList = new DBList($lTableHeader);
$lList->SetCloseTag($lTableFooter);
$lList->SetTemplate($lListTpl);
$lList->SetPageSize(30);
$lList->SetOrderParams((int)$_GET['ordby'], (int)$_GET['ordd']);
$lList->SetAntet($lListAntets);
$lList->SetQuery($lListSql);



if (!$lList->DisplayList($gListPage)) {
	echo $lTableHeader . '<tr><td colspan="4"><p align="center"><b>' . getstr('admin.indesign_templates.noData') . '</b></p></td></tr>' . $lTableFooter;
}


HtmlEnd();

function GetIndesignTemplateType( $pRs ){
	$lType = (int) $pRs['type'];
	switch( $lType ){
		default:
		case 1:{
			return getstr('admin.indesign_templates.style_to_node_type');
		}
		case 2:{
			return getstr('admin.indesign_templates.node_to_style_type');
		}
	}
}
?>