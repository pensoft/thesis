<?php
$docroot = getenv('DOCUMENT_ROOT');
require_once($docroot . '/lib/static.php');
$historypagetype = HISTORY_CLEAR;
$page = (int)$_GET['p'];
HtmlStart();

$gFArr = array(
	1 => array('caption' => getstr('admin.usrs.colIDLabel'), 'deforder' => 'asc'), 
	2 => array('caption' => getstr('admin.usrs.colUsernameLabel'), 'deforder' => 'asc', 'def'), 
	3 => array('caption' => getstr('admin.usrs.colNameLabel'), 'deforder' => 'asc'), 
	4 => array('caption' => getstr('admin.usrs.colStateLabel'), 'deforder' => 'asc'), 
	5 => array('caption' => getstr('admin.usrs.colTypeLabel'), 'deforder' => 'asc'),
	1000 => array('caption' => '', 'deforder' => 'asc'),
);

$t = '
<tr>
	<td>{id}</td>
	<td>{uname}</td>
	<td>{name}</td>
	<td>{state}</td>
	<td>{utype}</td>
	<td align="right">
		<a href="./editgrps.php?uid={id}&uname={uname}&name={_urlencode(name)}">
			<img src="/img/grps_edit.gif" align="absmiddle" border="0" alt="' . getstr('admin.usrs.groupsLabel') . '" title="' . getstr('admin.usrs.groupsLabel') . '" />
		</a>
		<a href="./edit.php?id={id}&tAction=show">
			<img src="/img/edit.gif" border="0" align="absmiddle" alt="' . getstr('admin.editLabel') . '" title="' . getstr('admin.editLabel') . '" />
		</a>
	</td>
</tr>
';

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
						<a href="./edit.php">' . getstr('admin.usrs.addNewUser') . '</a>
						' . getstr('admin.usrs.antetka') . '
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
$l->SetQuery('SELECT id, uname, name, state, utype FROM usr');

if (!$l->DisplayList($page)) {
	echo $lTableHeader . '<tr><td colspan="6"><p align="center"><b>' . getstr('admin.usrs.noData') . '</b></p></td></tr>' . $lTableFooter;
}


HtmlEnd();
?>