<?php

$docroot = getenv('DOCUMENT_ROOT');
require_once($docroot . '/lib/static.php');

$historypagetype = HISTORY_CLEAR;

HtmlStart();

$page = (int)$_GET['p'];

$fld = array(
	'title' => array(
		'VType' => 'string',
		'CType' => 'text',
		'DisplayName' => getstr('admin.attachments.titleCol'),
		'AddTags' => array(
			'class' => 'coolinp',
		),
		'AllowNulls' => true,
	),
	
	'source' => array(
		'CType' => 'select',
		'VType' => 'int',
		'SrcValues' => array(
			null => '--',
			0 => 'За статия',
			1 => 'За продукт',
			2 => 'За справочник',
		),
		'DisplayName' => getstr('admin.attachments.category'),
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

$frm = '
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
					<th colspan="2">' . getstr('admin.attachments.filter') . '</th>
				</tr>
				<tr>
					<td>{*title}<br/>{title}</td>
					<td>{*source}<br/>{source}</td>
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

$kfor = new kfor($fld, $frm, 'GET');
echo $kfor->Display();

$addWhere = '';
if ($kfor->lCurAction == 'show') {
	if ($kfor->lFieldArr['title']['CurValue']) {
		$addWhere .= ' AND lower(title) LIKE \'%' . q(mb_strtolower($kfor->lFieldArr['title']['CurValue'], 'UTF-8')) . '%\' ';
	}
	
	if (!is_null($kfor->lFieldArr['source']['CurValue'])) {
		$addWhere .= ' AND source = ' . (int)$kfor->lFieldArr['source']['CurValue'];
	}
}

$t = '<tr>
	<td valign="top" align="center"><a href="/getatt.php?filename=oo_{imgname}">[Свали]</a></td>
	<td valign="top">{guid}</td>
	<td valign="top">{title}</td>
	<td valign="top">{filenameupl}</td>
	<td valign="top" align="center">{createdate}</td>
	<td align="right" valign="top">
		<a href="./edit.php?guid={guid}&tAction=show"><img src="/img/edit.gif" border="0" alt="Редактирай" title="Редактирай" /></a>
	</td>
</tr>';

$gFArr = array(
	1001 => array('caption' => '  ', 'deforder' => 'desc'),
	1 => array('caption' => getstr('admin.attachments.idCol'), 'deforder' => 'desc'), 
	2 => array('caption' => getstr('admin.attachments.titleCol'), 'deforder' => 'asc'), 
	3 => array('caption' => getstr('admin.attachments.fileCol'), 'deforder' => 'asc'), 
	4 => array('caption' => getstr('admin.attachments.uploadedCol'), 'deforder' => 'desc', 'def'), 
	1000 => array('caption' => '  ', 'deforder' => 'desc'),
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
					<th class="gridtools" colspan="6">
						<a href="./edit.php">' . getstr('admin.attachments.addAtt') . '</a>
						' . getstr('admin.attachments.antetka') . '
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

$t = '<tr>
	<td valign="top">
		<a href="./edit.php?guid={guid}&tAction=show"><img src="/showimg.php?filename=mx50_{guid}.jpg" border="0" alt="" /></a>
	</td>
	<td valign="top">{guid}</td>
	<td valign="top">{title}</td>
	<td valign="top">{filenameupl}</td>
	<td valign="top">{createdate}</td>
	<td align="right" valign="top">
		<a href="./edit.php?guid={guid}&tAction=show"><img src="/img/edit.gif" border="0" alt="' . getstr('admin.editButton') . '" title="' . getstr('admin.editButton') . '" /></a>
	</td>
</tr>';

$l = new DBList($lTableHeader);
$l->SetCloseTag($lTableFooter);
$l->SetTemplate($t);
$l->SetPageSize(30);
$l->SetOrderParams((int)$_GET['ob1'], (int)$_GET['odd1']);
$l->SetOrderParamNames('ob1', 'odd1');
$l->SetAntet($gFArr);
$l->SetQuery('SELECT guid, title, filenameupl, createdate::date, imgname FROM photos WHERE ftype = 1 ' . $addWhere);

if (!$l->DisplayList($page)) {
	echo $lTableHeader . '<tr><td colspan="6"><p align="center"><b>' . getstr('admin.attachments.noData') . '</b></p></td></tr>' . $lTableFooter;
}

HtmlEnd() ;

function pubdate(&$pRs) {
	if (!$pRs['pubdate']) return '&nbsp;';
	else return $pRs['pubdate'];
}

?>