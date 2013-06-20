<?php
$docroot = getenv("DOCUMENT_ROOT");
require_once($docroot . '/lib/static.php');

$guid = (int) $_GET['guid'];

$t = array(
	'guid' => array(
		'VType' => 'int',
		'CType' => 'hidden',
		'AllowNulls' => true,
	),
	'primarysite' => array(
		'VType' => 'int',
		'CType' => 'hidden',
		'DefValue' => 1,
		'AllowNulls' => false,
	),
	'deletephoto' => array(
		'CType' => 'action',
		'DisplayName' => getstr('admin.deleteButton'),
		'SQL' => 'SELECT * FROM AddPhotoToStory(' . (int)$_GET['photoid'] . ', {guid}, 0, NULL, NULL, 3, NULL)',
		'ActionMask' => ACTION_CHECK | ACTION_EXEC | ACTION_FETCH | ACTION_REDIRECT,
		'RedirUrl' => './relpics.php?guid={guid}',
		'Hidden' => true,
	),
);

$h = '{guid}{primarysite}';
$f = new kfor($t, $h, 'POST');
$f->ExecAction();

if ($f->lCurAction == 'deletephoto') {
	clearcacheditems2('stories', $f->lFieldArr['primarysite']['CurValue']);
}
$f->Display();

HtmlStart(1);

$t = '<tr>
		<td width="150" valign="top"><img border="0" width="75" src="/showimg.php?filename=s_{photoid}.jpg"></td>
		<td valign="top">{pos}</td>
		<td valign="top">{phototitle}</td>
		<td valign="top">{valstr}</td>
		<td valign="top">{_checkplace}</td>
		<td align="right" valign="top">
			<nobr>
			<a href="javascript:openw(\'./selphoto.php?tAction=showedit&storyid=' . $guid . '&photoid={photoid}\', \'aa\', \'location=no,menubar=yes,width=600,height=400,scrollbars=yes,resizable=yes,top=0,left=0\')"">
			<img src="/img/edit.gif" alt="' . getstr('admin.editButton') . '" title="' . getstr('admin.editButton') . '" border="0" />
			</a>
			<a href="javascript:if (confirm(\'' . getstr('admin.stories.relPicsConfirmDel') . '\')) { window.location = \'./relpics.php?tAction=deletephoto&guid=' . $guid . '&photoid={photoid}\';} else {}">
				<img src="/img/trash2.gif" alt="' . getstr('admin.deleteButton') . '" title="' . getstr('admin.deleteButton') . '" border="0" />
			</a> 
			</nobr>
		</td>
	</tr>
';

$gFArr = array(
	1000 => array('caption' => '  ', 'deforder' => 'asc'),
	1 => array('caption' => getstr('admin.stories.relPicsPos'), 'def', 'deforder' => 'asc'), 
	2 => array('caption' => getstr('admin.stories.relPicsTitle'), 'deforder' => 'asc'), 
	3 => array('caption' => getstr('admin.stories.relPicsUnderTxt'), 'deforder' => 'asc'), 
	4 => array('caption' => getstr('admin.stories.relPicsPlace'), 'deforder' => 'asc'), 
	1001 => array('caption' => '  ', 'deforder' => 'asc'),
);

$lTableHeader = '
	<a name="snimki"></a>
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
						<a href="javascript:openw(\'/resources/photos/edit.php?storyid=' . $guid . '&sid=' . (int)$f->lFieldArr['primarysite']['CurValue'] . '\', \'aa\', \'location=no,menubar=yes,width=600,height=400,scrollbars=yes,resizable=yes,top=0,left=0\')">' . getstr('admin.stories.addRelPic') . '</a>
						<div class="gridtools_sep">|</div>
						<a href="javascript:openw(\'/resources/stories/selphoto.php?storyid=' . $guid . '\', \'aa\', \'location=no,menubar=yes,width=1000,height=700,scrollbars=yes,resizable=yes,top=0,left=0\')">' . getstr('admin.stories.addRelExistingPic') . '</a> 
						' . getstr('admin.stories.relPics') . '
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
$l->SetQuery('SELECT pos, phototitle, valstr, place, photoid FROM GetPhotosByStory(' . $guid . ', 0)');

if (!$l->DisplayList($page)) {
	echo $lTableHeader . '<tr><td colspan="6"><p align="center"><b>Няма снимки към този елемент</b></p></td></tr>' . $lTableFooter;
}

HtmlEnd(1);

?>