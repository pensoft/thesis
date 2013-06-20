<?php
$docroot = getenv('DOCUMENT_ROOT');
require_once($docroot . '/lib/static.php');

HtmlStart();

$gGid = (int) $_GET['gid'];
$gUid = (int) $_GET['uid'];
$gUName = $_GET['uname'];
$gUserName = $_GET['name'];
$gOp = $_GET['op'];

$gOpArr = array('ADD' => 1, 'REMOVE' => 2);
if (!array_key_exists($gOp, $gOpArr)) $gOp = '';


if ($gOp == 'ADD' || $gOp == 'REMOVE') {
	$gSqlStr = 'SELECT * FROM cmSetSecUserGrp(' . $gGid . ', ' . $gUid . ', ' . $gOpArr[$gOp] . ')';
	$gCn = Con();
	$gCn->Execute($gSqlStr);
	header('Location: ./editgrps.php?uid=' . $gUid);
	exit;
}

$gFArr = array(
	2 => array('caption' => 'Група', 'deforder' => 'asc', 'def'), 
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
						Групи, в които потребителят е включен
					</th>
				</tr>
';

$t1 = '
<tr>
	<td>{grpname}</td>
	<td align="right">
		<a href="./editgrps.php?gid={grpid}&uid=' . $gUid . '&op=REMOVE&uname='. $gUName .'&name='. urlencode($gUserName) .'">
			<img src="/img/remove.gif" border="0" align="absmiddle" alt="Премахване" title="Премахване" />
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
						Групи, в които потребителят не е включен
					</th>
				</tr>
';

$t2 = '
<tr>
	<td>{grpname}</td>
	<td align="right">
		<a href="./editgrps.php?gid={grpid}&uid=' . $gUid . '&op=ADD&uname='. $gUName .'&name='. urlencode($gUserName) .'">
			<img src="/img/add.gif" border="0" align="absmiddle" alt="Добавяне" title="Добавяне" />
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
$l->SetQuery('SELECT * FROM (
		SELECT DISTINCT ON (g.name, g.id) g.id as grpid, g.name as grpname
		FROM secgrp g
		LEFT JOIN secgrpdet sg ON (sg.gid = g.id AND sg.uid = '. $gUid .')
		WHERE sg.uid = ' . $gUid . '
	) a'
);

if (!$l->DisplayList()) {
	echo $lTableHeader1 . '<tr><td colspan="2"><p align="center"><b>Потребителя не е включен в нито една група</b></p></td></tr>' . $lTableFooter;
}

$l = new DBList($lTableHeader2);
$l->SetCloseTag($lTableFooter);
$l->SetTemplate($t2);
$l->SetOrderParams((int)$_GET['ordby2'], (int)$_GET['ordd2']);
$l->SetOrderParamNames('ordby2', 'ordd2');
$l->SetAntet($gFArr);
$l->SetQuery('SELECT * FROM (
		SELECT DISTINCT ON (g.name, g.id) g.id as grpid, g.name as grpname
		FROM secgrp g
		LEFT JOIN secgrpdet sg ON (sg.gid = g.id AND sg.uid = '. $gUid .')
		WHERE sg.uid IS NULL
	) a'
);

if (!$l->DisplayList()) {
	echo $lTableHeader2 . '<tr><td colspan="2"><p align="center"><b>Потребителя е включен във всички възможни групи</b></p></td></tr>' . $lTableFooter;
}

HtmlEnd();

?>