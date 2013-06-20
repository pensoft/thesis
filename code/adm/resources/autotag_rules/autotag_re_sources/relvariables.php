<?php
$docroot = getenv('DOCUMENT_ROOT');
 require_once($docroot . '/lib/static.php');


global $user, $gUrl;
$gSourceId = (int) $_REQUEST['source_id'];
$t = array(
	'source_id' => array(
		'VType' => 'int',
		'CType' => 'hidden',
		'DisplayName' => '',
		'AllowNulls' => true,
	),
	'variable_id' => array(
		'VType' => 'int',
		'CType' => 'hidden',
		'DisplayName' => '',
		'AllowNulls' => true,
	),
	'delete_variable' => array(
		'CType' => 'action',
		'DisplayName' => getstr('admin.deleteButton'),
		'SQL' => 'SELECT * FROM spAutotagReVariables(3, {variable_id}, null, null, null, null, null, null, null)',
		'ActionMask' => ACTION_CHECK | ACTION_EXEC | ACTION_FETCH | ACTION_REDIRECT,
		'RedirUrl' => './relvariables.php?source_id=' . $gSourceId ,
		'Hidden' => true,
	),
);
$h = '{source_id}{variable_id}';
$f = new kfor($t, $h, 'GET');
$f->Display();

HtmlStart(1);


$t = '<tr>
		<td valign="top">{id}</td>
		<td valign="top">{name}</td>
		<td valign="top">{variable_symbol}</td>
		<td valign="top">{type}</td>
		<td valign="top">{expression}</td>
		<td valign="top">{_showMultipleConcatType}</td>
		<td valign="top">{concat_separator}</td>
		<td align="right" valign="top"> 
			<a href="javascript:openw(\'/resources/autotag_rules/autotag_re_sources/variable_edit.php?id={id}&source_id=' . $gSourceId . '&tAction=showedit\', \'aa\', \'location=no,menubar=yes,width=800,height=600,scrollbars=yes,resizable=yes,top=0,left=0\')"">
				<img src="/img/edit.gif" alt="' . getstr('admin.editButton') . '" title="' . getstr('admin.editButton') . '" border="0" />
			</a>
			<a href="javascript:if (confirm(\'' . getstr('admin.autotag_re_variables.ConfirmDel') . '\')) { window.location = \'/resources/autotag_rules/autotag_re_sources/relvariables.php?variable_id={id}&source_id=' . $gSourceId . '&tAction=delete_variable\';} else {}">
				<img src="/img/trash2.gif" alt="' . getstr('admin.deleteButton') . '" title="' . getstr('admin.deleteButton') . '" border="0" />
			</a>
		</td>
	</tr>
';

$gFArr = array(
	1 => array('caption' => getstr('admin.autotag_re_variables.colId'), 'def', 'deforder' => 'asc'), 
	2 => array('caption' => getstr('admin.autotag_re_variables.colName'), 'deforder' => 'asc'), 		
	3 => array('caption' => getstr('admin.autotag_re_variables.colVariableSymbol'), 'deforder' => 'asc'), 
	4 => array('caption' => getstr('admin.autotag_re_variables.colType'), 'deforder' => 'asc'), 
	5 => array('caption' => getstr('admin.autotag_re_variables.colExpression'), 'deforder' => 'asc'), 
	6 => array('caption' => getstr('admin.autotag_re_variables.colConcatMultiple'), 'deforder' => 'asc'), 
	7 => array('caption' => getstr('admin.autotag_re_variables.colConcatSeparator'), 'deforder' => 'asc'), 
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
					<th class="gridtools" colspan="8">
						<a href="javascript:openw(\'/resources/autotag_rules/autotag_re_sources/variable_edit.php?source_id=' . $gSourceId . '\', \'aa\', \'location=no,menubar=yes,width=600,height=400,scrollbars=yes,resizable=yes,top=0,left=0\')">' . getstr('admin.autotag_re_variables.addItem') . '</a>
						' . getstr('admin.autotag_re_variables.antetka') . '
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
$l->SetQuery('SELECT r.id, r.name, r.variable_symbol, t.name as type, r.expression, r.concat_multiple, r.concat_separator 
	FROM autotag_re_variables r	
	JOIN autotag_re_variable_types t ON t.id = r.variable_type
	WHERE r.source_id = ' . $gSourceId
);

if (!$l->DisplayList($page)) {
	echo $lTableHeader . '<tr><td colspan="4"><p align="center"><b>' . getstr('admin.autotag_re_variables.noData') . '</b></p></td></tr>' . $lTableFooter;
}

HtmlEnd(1);

function showMultipleConcatType($pRs){
	if((int) $pRs['concat_multiple'])
		return getstr('global.yes');
	return getstr('global.no');
}

?>