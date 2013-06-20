<?php
$docroot = getenv("DOCUMENT_ROOT");
require_once($docroot . '/lib/static.php');

$id = (int) $_GET['id'];
$sid = (int)$_GET['sid'];
$gPropType = 16;

$t = array(
	'id' => array(
		'VType' => 'int',
		'CType' => 'hidden',
		'DisplayName' => '',
		'AllowNulls' => true,
	),
	'deleterelstory' => array(
		'CType' => 'action',
		'DisplayName' => 'Изтрии',
		'SQL' => 'SELECT * FROM DelProductFromStory({id}, ' . (int)$_GET['relstoryid'] . ', ' . $gPropType . ')',
		'ActionMask' => ACTION_CHECK | ACTION_EXEC | ACTION_FETCH | ACTION_REDIRECT,
		'RedirUrl' => '/store/products/relproducts.php?id={id}',
		'Hidden' => true,
	),
);

$h = '{id}';
$f = new kfor($t, $h, 'POST');
$f->Display();

HtmlStart(1);

$t = '<tr>
		<td valign="top"><a href="{_getStoryEditLink}" target="_parent">{name}</a></td>
		<td valign="top">{createdate}</td>
		<td align="right" valign="top">
			<a href="javascript:if (confirm(\'Потвърдете изтриването...\')) { window.location = \'/store/products/relproducts.php?tAction=deleterelstory&relstoryid={relstoryid}&id=' . $id . '\';} else {}">
				<img src="/img/trash2.gif" alt="Изтрий" title="Изтрий" border="0" />
			</a> 
		</td>
	</tr>
';

$gFArr = array(
	1 => array('caption' => 'Продукт', 'def', 'deforder' => 'asc'), 
	2 => array('caption' => 'Дата на създаване', 'deforder' => 'asc'), 
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
						<a href="javascript:openw(\'/store/products/index.php?relstoryid=' . $id . '&mode=rel\', \'aa\', \'location=yes,menubar=yes,width=1000,height=700,scrollbars=yes,resizable=yes,top=0,left=0\')"><b>Добави продукт</b></a>
						Свързани продукти
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

$gSqlStr = 'SELECT s.name, s.createdate, sp.valint as relstoryid
			FROM storyproperties sp 
			INNER JOIN store_products s on (sp.valint = s.id) 
			WHERE sp.guid = ' . $id . ' AND sp.propid = 16';

$l = new DBList($lTableHeader);
$l->SetCloseTag($lTableFooter);
$l->SetTemplate($t);
$l->SetOrderParams((int)$_GET['ordby'], (int)$_GET['ordd']);
$l->SetAntet($gFArr);
$l->SetQuery($gSqlStr);

if (!$l->DisplayList($page)) {
	echo $lTableHeader . '<tr><td colspan="4"><p align="center"><b>' . getstr('admin.stories.relLinksNoData') . '</b></p></td></tr>' . $lTableFooter;
}

HtmlEnd(1);

function getStoryEditLink($pRs) {
	if ($pRs['primarysite'] == 8) {
		$link = '/blog/edit.php?tAction=show&id='. $pRs['relstoryid'];
	} else {
		$link = '/store/products/edit.php?tAction=show&id='. $pRs['relstoryid'];
	}
	return $link;
}

?>