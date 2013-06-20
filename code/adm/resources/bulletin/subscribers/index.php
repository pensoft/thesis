<?php
$docroot = getenv("DOCUMENT_ROOT");
require_once($docroot . '/lib/static.php');
$historypagetype = HISTORY_CLEAR;
$page = (int)$_GET['p'];

HtmlStart();

$fld = array(
	'email' => array(
		'VType' => 'string',
		'CType' => 'text',
		'DisplayName' => getstr('admin.bulletin.email'),
		'AddTags' => array(
			'class' => 'coolinp',
		),
		'AllowNulls' => true,
	),
	
	'confmail' => array(
		'VType' => 'int',
		'CType' => 'select',
		'SrcValues' => array(3 => '--', 0 => 'Не', 1 => 'Да'),
		'DisplayName' => getstr('admin.bulletin.active'),
		'DefValue' => 3,
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

$h = '<div class="t">
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
					<th colspan="2">' . getstr('admin.stories.filter') . '</th>
				</tr>
				<tr>
					<td>{*email}<br/>{email}</td>
					<td>{*confmail}<br/>{confmail}</td>
				</tr>
				<tr>
					<td></td>
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

$kfor = new kfor($fld, $h, 'GET');
$kfor->debug = false;
echo $kfor->Display();

$warr = array();

if ($kfor->lCurAction == 'show') {
	
	if ((int)$kfor->lFieldArr['confmail']['CurValue'] <>3) {
		$warr[] = 'confmail = ' . (int)$kfor->lFieldArr['confmail']['CurValue'];
	}
	
	if ($kfor->lFieldArr['email']['CurValue']) {
		$warr[] = 'email LIKE \'%'. $kfor->lFieldArr['email']['CurValue'] .'%\'';
	}
}

$t = '
<tr>
	<td>{id}</td>
	<td>{email}</td>
	<td>{confmail}</td>
	<td>{tstamp}</td>
	<td>{ip}</td>
</tr>
';

$gFArr = array(
	1 => array('caption' => getstr('admin.bulletin.idabon'), 'def', 'deforder' => 'asc'), 
	2 => array('caption' => getstr('admin.bulletin.email'), 'deforder' => 'asc'), 
	3 => array('caption' => getstr('admin.bulletin.active'), 'deforder' => 'asc'), 
	4 => array('caption' =>getstr('admin.bulletin.date'), 'deforder' => 'desc'), 
	5 => array('caption' => getstr('admin.bulletin.ip'), 'deforder' => 'asc'),
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
					<th class="gridtools" colspan="8">
						' . getstr('admin.bulletin.subscribers') . '
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
$l->SetPageSize(30);
$l->SetOrderParams((int)$_GET['ordby'], (int)$_GET['ordd']);
$l->SetAntet($gFArr);
$l->SetQuery('SELECT id, email, confmail, tstamp, ip FROM newsletter
	' . (count($warr) ? ' WHERE ' . implode(' AND ', $warr) : ''));

if (!$l->DisplayList($page)) {
	echo $lTableHeader . '<tr><td colspan="8"><p align="center"><b>' . getstr('admin.stories.noData') . '</b></p></td></tr>' . $lTableFooter;
}

HtmlEnd($lHide);
?>