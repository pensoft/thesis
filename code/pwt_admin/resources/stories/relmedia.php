<?php
$docroot = getenv("DOCUMENT_ROOT");
require_once($docroot . '/lib/static.php');

$guid = (int) $_GET['guid'];

$t = array(
	'guid' => array(
		'VType' => 'int',
		'CType' => 'hidden',
		'DisplayName' => '',
		'AllowNulls' => true,
	),
	
	'delete' => array(
		'CType' => 'action',
		'DisplayName' => getstr('admin.deleteButton'),
		'SQL' => 'SELECT * FROM AddMediaToStory(' . (int)$_GET['ftype'] . ', ' . (int)$_GET['mid'] . ', {guid}, 0, null)',
		'ActionMask' => ACTION_CHECK | ACTION_EXEC | ACTION_FETCH | ACTION_REDIRECT,
		'RedirUrl' => './relmedia.php?guid={guid}',
		'Hidden' => true,
	),
);
$h = '{guid}';
$f = new kfor($t, $h, 'POST');
$f->ExecAction();
$f->Display();

HtmlStart(1);

$t = '<tr>
		<td width="150" valign="top"><a href="/resources/media/edit.php?guid={mid}&storyid=' . $guid . '&tAction=edit" target="_parent">{mtitle}</a></td>
		<td valign="top">{_mmFtype}</td>
		<td valign="top">{_checkplace}</td>
		<td align="right" valign="top">
			<a href="javascript:openw(\'/resources/media/edit.php?guid={mid}&storyid=' . $guid . '&tAction=edit\', \'aa\', \'location=no,menubar=yes,width=800,height=600,scrollbars=yes,resizable=yes,top=0,left=0\')"">
			<img src="/img/edit.gif" alt="' . getstr('admin.editButton') . '" title="' . getstr('admin.editButton') . '" border="0" />
			</a>
			<a href="javascript:if (confirm(\'' . getstr('admin.stories.relMediaConfirmDel') . '\')) { window.location = \'./relmedia.php?tAction=delete&guid=' . $guid . '&mid={mid}&ftype={ftype}\';} else {}">
				<img src="/img/trash2.gif" alt="' . getstr('admin.deleteButton') . '" title="' . getstr('admin.deleteButton') . '" border="0" />
			</a> 
		</td>
	</tr>
';

$gFArr = array(
	1 => array('caption' => getstr('admin.stories.relMediaTitle'), 'def', 'deforder' => 'asc'), 
	2 => array('caption' => getstr('admin.stories.relMediaType'), 'deforder' => 'asc'), 
	3 => array('caption' => getstr('admin.stories.relMediaPlace'), 'deforder' => 'asc'), 
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
						<a href="/resources/media/edit.php?storyid=' . $guid . '&sid=" target="_parent">' . getstr('admin.stories.addRelMedia') . '</a>
						<div class="gridtools_sep">|</div>
						<a href="javascript:openw(\'./selmedia.php?storyid=' . $guid . '\', \'aa\', \'location=no,menubar=yes,width=800,height=600,scrollbars=yes,resizable=yes,top=0,left=0\')">' . getstr('admin.stories.addRelExistingMedia') . '</a>
						' . getstr('admin.stories.relMedia') . '
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
$l->SetQuery('SELECT mtitle, ftype, place, mid, mauthor, valstr FROM GetMediaByStory(' . $guid . ')');

if (!$l->DisplayList($page)) {
	echo $lTableHeader . '<tr><td colspan="4"><p align="center"><b>' . getstr('admin.stories.relMediaNoData') . '</b></p></td></tr>' . $lTableFooter;
}

HtmlEnd(1);

?>