<?php
$docroot = getenv('DOCUMENT_ROOT');
require_once($docroot . '/lib/static.php');

HtmlStart();

$gGid = (int) $_GET['gid'];
$gSid = (int) $_GET['sid'];
$gType = (int) $_GET['type'];
$gOp = $_GET['op'];

$gOpArr = array('ADD' => 1, 'REMOVE' => 2);
if (!array_key_exists($gOp, $gOpArr)) $gOp = '';

$gCn = Con();

if ($gOp == 'ADD' || $gOp == 'REMOVE') {
	$gSqlStr = 'SELECT * FROM cmSetSecGrpSite (' . $gGid . ', ' . $gSid . ', ' . $gType . ', ' . $gOpArr[$gOp] . ')';
	$gCn->Execute($gSqlStr);
	header('Location: ./index.php?sid=' . $gSid);
	exit;
}

$gSqlStr = 'SELECT id, url	FROM secsites ORDER BY url asc';
$gCn->Execute($gSqlStr);
$gCn->MoveFirst();
echo '	<form action="./" method="GET">
		<div class="t">
		<div class="b">
		<div class="l">
		<div class="r">
			<div class="bl">
			<div class="br">
			<div class="tl">
			<div class="tr">
		<table cellspacing="0" cellpadding="5" border="0" class="formtable">
		<tr><th>' . getstr('admin.permissions.chooseDirLabel') . '</th></tr>
		<tr><td>
		<select name="sid" class="coolinp">';
while (!$gCn->Eof()) {
	echo '<option value="' . $gCn->mRs['id'] . '"' . (($gSid == $gCn->mRs['id']) ? ' selected' : '') . '>' . $gCn->mRs['url'] . '</option>';
	$gCn->MoveNext();
}

echo '		</select>
		</td></tr><tr><td align="right">
		<input type="submit" class="frmbutton" value="' . getstr('admin.chooseButton') . '">
		</td></tr>
		</table>
			</div>
			</div>
			</div>
			</div>
		</div>
		</div>
		</div>
		</div>
	</form>';

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

$gFArr1 = array(
	2 => array('caption' => getstr('admin.permissions.colGroupsLabel'), 'deforder' => 'asc', 'def'), 
	3 => array('caption' => getstr('admin.permissions.colPermitionsLabel'), 'deforder' => 'asc'), 
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
					<th class="gridtools" colspan="3">
						' . getstr('admin.permissions.groupsWithPermission') . '						
					</th>
				</tr>
';

$t1 = '
<tr>
	<td>{name}</td>
	<td>{type}</td>
	<td align="right">
		<a href="./?gid={id}&sid=' . $gSid . '&op=REMOVE&type=1">
			<img src="/img/remove.gif" border="0" align="absmiddle" alt="' . getstr('admin.removeLabel') . '" title="' . getstr('admin.removeLabel') . '" />
		</a>
	</td>
</tr>
';

$l = new DBList($lTableHeader1);
$l->SetCloseTag($lTableFooter);
$l->SetTemplate($t1);
$l->SetOrderParams((int)$_GET['ordby1'], (int)$_GET['ordd1']);
$l->SetOrderParamNames('ordby1', 'ordd1');
$l->SetAntet($gFArr1);
$l->SetQuery('SELECT s.id, s.name, ga.type
		FROM secgrp s
		LEFT JOIN secgrpacc ga on (s.id = ga.gid AND ga.sid = ' . $gSid . ')
		WHERE ga.sid = ' . $gSid
);

if (!$l->DisplayList()) {
	echo $lTableHeader1 . '<tr><td colspan="3"><p align="center"><b>' . getstr('admin.permissions.noGroupsHavePermissions') . '</b></p></td></tr>' . $lTableFooter;
}

$gFArr2 = array(
	2 => array('caption' => getstr('admin.permissions.colGroupLabel'), 'deforder' => 'asc', 'def'), 
	1000 => array('caption' => '', 'deforder' => 'asc'),
);

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
						' . getstr('admin.permissions.groupsWithoutPermission') . '
					</th>
				</tr>
';

$t2 = '
<tr>
	<td>{name}</td>
	<td align="right">
		<a href="./index.php?gid={id}&sid=' .  $gSid . '&op=ADD&type=1">1</a>
		<a href="./index.php?gid={id}&sid=' .  $gSid . '&op=ADD&type=2">2</a>
		<a href="./index.php?gid={id}&sid=' .  $gSid . '&op=ADD&type=3">3</a>
		<a href="./index.php?gid={id}&sid=' .  $gSid . '&op=ADD&type=4">4</a>
		<a href="./index.php?gid={id}&sid=' .  $gSid . '&op=ADD&type=5">5</a>
		<a href="./index.php?gid={id}&sid=' .  $gSid . '&op=ADD&type=6">6</a>
	</td>
</tr>
';

$l = new DBList($lTableHeader2);
$l->SetCloseTag($lTableFooter);
$l->SetTemplate($t2);
$l->SetOrderParams((int)$_GET['ordby2'], (int)$_GET['ordd2']);
$l->SetOrderParamNames('ordby2', 'ordd2');
$l->SetAntet($gFArr2);
$l->SetQuery('SELECT s.id, s.name, ga.type
		FROM secgrp s
		LEFT JOIN secgrpacc ga on (s.id = ga.gid AND ga.sid = ' . $gSid . ')
		WHERE ga.sid IS NULL'
);

if (!$l->DisplayList()) {
	echo $lTableHeader2 . '<tr><td colspan="2"><p align="center"><b>' . getstr('admin.permissions.allGroupsHavePermissions') . '</b></p></td></tr>' . $lTableFooter;
}


HtmlEnd();

?>