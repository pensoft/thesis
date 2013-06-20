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
	
	'deleteattachment' => array(
		'CType' => 'action',
		'DisplayName' => getstr('admin.deleteButton'),
		'SQL' => 'SELECT * FROM AddAttToStory(' . (int)$_GET['photoid'] . ', {guid}, null, 1, null)',
		'ActionMask' => ACTION_CHECK | ACTION_EXEC | ACTION_FETCH | ACTION_REDIRECT,
		'RedirUrl' => './relattachments.php?guid={guid}',
		'Hidden' => true,
	),	
);

$h = '{guid}';
$f = new kfor($t, $h, 'POST');
$f->Display();

HtmlStart(1);

//~ echo '
//~ <script>
	//~ function addfile(photoid){
		//~ document.def2.photoid.value = guid;
		//~ window.location.hash = "#add";
	//~ }
//~ </script>
//~ ';

$t = '<tr>
		<td valign="top"><a href="/getatt.php?filename=oo_{imgname}" title="{valstr2}">[file]</a></td>
		<td valign="top">{phototitle}</td>
		<td valign="top">{valstr}</td>
		<td align="right" valign="top">
		<a href="javascript:openw(\'./selattachments.php?tAction=show&storyid=' . $guid . '&guid={photoid}\', \'aa\', \'location=no,menubar=yes,width=600,height=400,scrollbars=yes,resizable=yes,top=0,left=0\')"">
			<img src="/img/edit.gif" alt="' . getstr('admin.editButton') . '" title="' . getstr('admin.editButton') . '" border="0" />
			</a>
			<a href="javascript:if (confirm(\'' . getstr('admin.stories.relAttConfirmDel') . '\')) { window.location = \'./relattachments.php?tAction=deleteattachment&guid=' . $guid . '&photoid={photoid}\';} else {}">
				<img src="/img/trash2.gif" alt="' . getstr('admin.deleteButton') . '" title="' . getstr('admin.deleteButton') . '" border="0" />
			</a>
		</td>
	</tr>
';

$gFArr = array(
	1000 => array('caption' => '  ', 'deforder' => 'asc'),
	1 => array('caption' => getstr('admin.stories.relAttFile'), 'def', 'deforder' => 'asc'), 
	2 => array('caption' => getstr('admin.stories.relAttTitle'), 'deforder' => 'asc'), 
	1001 => array('caption' => '  ', 'deforder' => 'asc'),
);

$lTableHeader = '
	<a name="att"></a>
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
					<a href="javascript:openw(\'/resources/attachments/edit.php?storyid=' . $guid . '&sid=' . (int)$f->lFieldArr['primarysite']['CurValue'] . '\', \'aa\', \'location=no,menubar=yes,width=600,height=400,scrollbars=yes,resizable=yes,top=0,left=0\')">' . getstr('admin.stories.addRelAtt') . '</a>
						' . getstr('admin.stories.relAtt') . '
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
$l->SetQuery('SELECT phototitle, valstr, imgname, valstr2, photoid FROM GetAttachmentsByStory(' . $guid . ')');

if (!$l->DisplayList($page)) {
	echo $lTableHeader . '<tr><td colspan="4"><p align="center"><b>Няма прикачени файлове към този елемент</b></p></td></tr>' . $lTableFooter;
}

HtmlEnd(1);

?>