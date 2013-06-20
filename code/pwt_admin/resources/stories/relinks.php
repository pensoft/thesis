<?php
$docroot = getenv('DOCUMENT_ROOT');
require_once($docroot . '/lib/static.php');

$guid = (int) $_GET['guid'];
global $user, $gUrl;
UserRedir($user);
ProccessHistory();

$gPropType = 9;

$t = array(
	'guid' => array(
		'VType' => 'int',
		'CType' => 'hidden',
		'DisplayName' => '',
		'AllowNulls' => true,
	),
	'pos' => array(
		'VType' => 'int',
		'CType' => 'hidden',
		'DisplayName' => '',
		'AllowNulls' => true,
	),	
	'deleterelink' => array(
		'CType' => 'action',
		'DisplayName' => getstr('admin.deleteButton'),
		'SQL' => 'SELECT * FROM spMoreLinks(3, {guid}, null, null, {pos}, ' . $gPropType . ' )',
		'ActionMask' => ACTION_CHECK | ACTION_EXEC | ACTION_FETCH | ACTION_REDIRECT,
		'RedirUrl' => './relinks.php?guid={guid}',
		'Hidden' => true,
	),
);
$h = '{guid}{pos}';
$f = new kfor($t, $h, 'POST');
$f->Display();

HtmlStart(1);

$t = '<tr>
		<td valign="top">{pos}</td>
		<td valign="top"><a href="{url}" target="_blank">{title}</a></td>
		<td valign="top">{url}</td>
		<td align="right" valign="top">
			<a href="javascript:if (confirm(\'' . getstr('admin.stories.relLinksConfirmDel') . '\')) { window.location = \'./relinks.php?tAction=deleterelink&pos={pos}&guid=' . $guid . '\';} else {}">
				<img src="/img/trash2.gif" alt="' . getstr('admin.deleteButton') . '" title="' . getstr('admin.deleteButton') . '" border="0" />
			</a> 
		</td>
	</tr>
';

$gFArr = array(
	1 => array('caption' => getstr('admin.stories.relLinksPos'), 'def', 'deforder' => 'asc'), 
	3 => array('caption' => getstr('admin.stories.relLinksTitle'), 'deforder' => 'asc'), 
	2 => array('caption' => getstr('admin.stories.relLinksUrl'), 'deforder' => 'asc'), 
	1000 => array('caption' => '  ', 'deforder' => 'asc'),
);

$lTableHeader = '
	<a name="links"></a>
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
						<a href="javascript:openw(\'./selink.php?guid=' . $guid . '\', \'aa\', \'location=no,menubar=yes,width=600,height=400,scrollbars=yes,resizable=yes,top=0,left=0\')">' . getstr('admin.stories.addRelLink') . '</a>
						' . getstr('admin.stories.relLinks') . '
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
$l->SetQuery('SELECT sp.valint as pos, sp.valstr as url, sp.valstr2 as title 
	FROM storyproperties sp
	WHERE sp.guid = ' . $guid . ' AND sp.propid = ' . $gPropType
);

if (!$l->DisplayList($page)) {
	echo $lTableHeader . '<tr><td colspan="4"><p align="center"><b>' . getstr('admin.stories.relLinksNoData') . '</b></p></td></tr>' . $lTableFooter;
}

HtmlEnd(1);

?>