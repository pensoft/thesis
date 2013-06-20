<?php
$docroot = getenv("DOCUMENT_ROOT");

require_once("$docroot/lib/static.php");

HtmlStart();

$gGid = (int) $_GET['gid'];
$gUid = (int) $_GET['uid'];
$gOp = $_GET['op'];

$gOpArr = array('ADD' => 1, 'REMOVE' => 2);
if (!array_key_exists($gOp, $gOpArr)) $gOp = '';

if ($gOp == 'ADD' || $gOp == 'REMOVE') {
	$gSqlStr = 'select * FROM cmSetSecUserGrp(' . $gGid . ', ' . $gUid . ', ' . $gOpArr[$gOp] . ')';
	$gCn = Con();
	$gCn->Execute($gSqlStr);
	header('Location: ./members.php?gid=' . $gGid);
	exit;
}

$gFArr = array(
	1 => array('caption' => getstr('admin.usrs.colUsernameLabel'), 'deforder' => 'asc', 'def'), 
	2 => array('caption' => getstr('admin.usrs.colNameLabel'), 'deforder' => 'asc'), 
	1000 => array('caption' => '', 'deforder' => 'asc'),
);

$lTableHeader1 = '
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
					<th class="gridtools" colspan="2">
						' . getstr('admin.grp.membersOfGroup') . '						
					</th>
				</tr>
';

$t1 = '
<tr>
	<td>{uname}</td>
	<td>{name}</td>
	<td align="right">
		<a href="./members.php?uid={id}&gid=' . $gGid . '&op=REMOVE">
			<img src="/img/remove.gif" border="0" align="absmiddle" alt="' . getstr('admin.removeLabel') . '" title="' . getstr('admin.removeLabel') . '" />
		</a>
	</td>
</tr>
';

$lTableHeader2 = '
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
					<th class="gridtools" colspan="2">
						' . getstr('admin.grp.nonMembersOfGroup') . '	
					</th>
				</tr>
';

$t2 = '
<tr>
	<td>{uname}</td>
	<td>{name}</td>
	<td align="right">
		<a href="./members.php?uid={id}&gid=' . $gGid . '&op=ADD">
			<img src="/img/add.gif" border="0" align="absmiddle" alt="' . getstr('admin.addLabel') . '" title="' . getstr('admin.addLabel') . '" />
		</a>
	</td>
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

$l = new DBList($lTableHeader1);
$l->SetCloseTag($lTableFooter);
$l->SetTemplate($t1);
$l->SetOrderParams((int)$_GET['ordby1'], (int)$_GET['ordd1']);
$l->SetOrderParamNames('ordby1', 'ordd1');
$l->SetAntet($gFArr);
$l->SetQuery('SELECT u.uname, u.name, u.id
	FROM usr u 
	LEFT JOIN secgrpdet gd ON (u.id = gd.uid AND gd.gid = ' . $gGid . ')
	WHERE gd.gid = ' . $gGid
);

if (!$l->DisplayList()) {
	echo $lTableHeader1 . '<tr><td colspan="2"><p align="center"><b>' . getstr('admin.grp.noMembersOfThisGroup') . '</b></p></td></tr>' . $lTableFooter;
}

$l = new DBList($lTableHeader2);
$l->SetCloseTag($lTableFooter);
$l->SetTemplate($t2);
$l->SetOrderParams((int)$_GET['ordby2'], (int)$_GET['ordd2']);
$l->SetOrderParamNames('ordby2', 'ordd2');
$l->SetAntet($gFArr);
$l->SetQuery('SELECT  u.uname, u.name, u.id
	FROM usr u 
	LEFT JOIN secgrpdet gd ON (u.id = gd.uid AND gd.gid = ' . $gGid . ')
	WHERE gd.gid IS NULL'
);

if (!$l->DisplayList()) {
	echo $lTableHeader2 . '<tr><td colspan="2"><p align="center"><b>' . getstr('admin.grp.everybodyIsMemberOfThisGroup') . '</b></p></td></tr>' . $lTableFooter;
}

HtmlEnd();

?>