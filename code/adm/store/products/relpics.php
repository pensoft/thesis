<?php
$docroot = getenv("DOCUMENT_ROOT");
require_once($docroot . '/lib/static.php');

$guid = (int) $_GET['guid'];

$t = array(
	"guid" => array(
		"VType" => "int",
		"CType" => "hidden",
		"DisplayName" => "",
		"AllowNulls" => true,
	),
	"deletephoto" => array(
		"CType" => "action",
		"DisplayName" => "Изтрии",
		"SQL" => 'SELECT * FROM AddPhotoToStory(' . (int)$_GET['photoid'] . ', {guid}, 0, NULL, NULL, 3, NULL)',
		"ActionMask" => ACTION_CHECK | ACTION_EXEC | ACTION_FETCH | ACTION_REDIRECT,
		"RedirUrl" => '/store/products/relpics.php?guid={guid}',
		"Hidden" => true,
	),
);
$h = '{guid}';
$f = new kfor($t, $h, "POST");
$f->ExecAction();

if ($f->lCurAction == 'deletephoto') {
	clearcacheditems('stories');
}
$f->Display();

HtmlStart(1);

$t = '<tr>
		<td width="150" valign="top"><img border="0" width="75" src="/showimg.php?filename=s_{photoid}.jpg"></td>
		<td valign="top">{pos}</td>
		<td valign="top">{phototitle}</td>
		<td valign="top">{valstr}</td>
		<td valign="top">{_checkplace2}</td>
		<td align="right" valign="top">
			<nobr>
			<a href="javascript:openw(\'/store/products/selphoto.php?tAction=showedit&storyid=' . $guid . '&product=1&photoid={photoid}\', \'aa\', \'location=no,menubar=yes,width=600,height=400,scrollbars=yes,resizable=yes,top=0,left=0\')"">
			<img src="/img/edit.gif" alt="Редактирай" title="Редактирай" border="0" />
			</a>
			<a href="javascript:if (confirm(\'Потвърдете изтриването...\')) { window.location = \'/store/products/relpics.php?tAction=deletephoto&guid=' . $guid . '&photoid={photoid}\';} else {}">
				<img src="/img/trash2.gif" alt="Изтрий" title="Изтрий" border="0" />
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
						<a href="javascript:openw(\'/resources/photos/edit.php?storyid=' . $guid . '&product=1&stype=products&sid=' . (int)$f->lFieldArr['primarysite']['CurValue'] . '\', \'aa\', \'location=no,menubar=yes,width=600,height=400,scrollbars=yes,resizable=yes,top=0,left=0\')">' . getstr('admin.stories.addRelPic') . '</a>
						<div class="gridtools_sep">|</div>
						<a href="javascript:openw(\'/store/products/selphoto.php?storyid=' . $guid . '&product=1\', \'aa\', \'location=no,menubar=yes,width=600,height=400,scrollbars=yes,resizable=yes,top=0,left=0\')">' . getstr('admin.stories.addRelExistingPic') . '</a> 
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

$gSqlStr =  'SELECT * FROM GetPhotosByProduct(' . $guid . ', 0)';

$l = new DBList($lTableHeader);
$l->SetCloseTag($lTableFooter);
$l->SetTemplate($t);
$l->SetOrderParams((int)$_GET['ordby'], (int)$_GET['ordd']);
$l->SetAntet($gFArr);
$l->SetQuery($gSqlStr);

if (!$l->DisplayList($page)) {
	echo $lTableHeader . '<tr><td colspan="6"><p align="center"><b>Няма снимки към този елемент</b></p></td></tr>' . $lTableFooter;
}

HtmlEnd(1);

function clearcacheditems($pType, $pSiteId = null) {
	if($pSiteId == null) {
		$a = glob(PATH_CACHE . '/*');
		foreach($a as $f) {
			$f = basename($f);
			system('find ' . PATH_CACHE . '/' . $f . '/ -name ' . escapeshellarg($pType . '_*') . ' -print0 | xargs -0 touch -d 1/1/2000');
		}
	} else {
		system('find ' . PATH_CACHE . '/' . $pSiteId . '/ -name ' . escapeshellarg($pType . '_*') . ' -print0 | xargs -0 touch -d 1/1/2000');
	}
}

?>