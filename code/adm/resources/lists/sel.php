<?php
$docroot = getenv('DOCUMENT_ROOT');
require_once($docroot . '/lib/static.php');

HtmlStart(1);

echo '
<script>
	function setObject(pPos, pGUid, pName) {
		d=window.opener.document;
		d.getElementById("guid" + pPos).value = pGUid;
		d.getElementById("title" + pPos).value = pName;
		window.close();
		return false;	
	}
</script>
';

$t = array(
	'pos' => array (
		'VType' => 'int' ,
		'CType' => 'hidden' ,		
	),
	
	'obj' => array (
		'VType' => 'string' ,
		'CType' => 'hidden' ,		
	),

	'storytype' => array(
		'VType' => 'int',
		'CType' => 'select',
		'SrcValues' => array(null => '--', -1 => 'Статия', 2 => 'Продукт', 3 => 'Справочник'),
		'DisplayName' => 'Тип',
		'AddTags' => array(
			'class' => 'coolinp',
		),
		'AllowNulls' => true,
	),
	
	'rubrid' => array(
		'VType' => 'int',
		'CType' => 'select',
		'SrcValues' => 'SELECT null AS id, \'--\' as name, \'\' as pos, 0 as ord
				UNION 
			SELECT *, 1 as ord FROM (
				SELECT id, case when id = rootnode then ' . getsqlang('name') . ' else repeat(\'&nbsp;\', length(pos)) || \'- \' || ' . getsqlang('name') . ' end as name, pos
				FROM rubr WHERE sid = 1 order by rootnode, (case when id = rootnode then 0 else 1 end), pos 
			) a 
			ORDER BY ord, pos	
		',
		'DisplayName' => 'Рубрика',
		'AddTags' => array(
			'class' => 'coolinp',
		),
		'AllowNulls' => true,
	),
	
	'stext' => array(
		'VType' => 'string',
		'CType' => 'text',
		'DisplayName' => 'Ключова дума',
		'AddTags' => array(
			'class' => 'coolinp',
		),
		'AllowNulls' => true,
	),

	'language' => array(
		'VType' => 'string',
		'CType' => 'select',
		'DisplayName' => 'Език:',
		'SrcValues' => 'SELECT null AS id, \'--\' as name
					UNION
					SELECT code as id, name FROM languages ORDER BY name',
		'AddTags' => array(
			'class' => 'coolinp',
		),
	),

	'show' => array(
		'CType' => 'action',
		'DisplayName' => getstr('admin.filterButton'),
		'SQL' => '',
		'ActionMask' => ACTION_CHECK | ACTION_SHOW,
		'AddTags' => array(
			'class' => 'frmbutton',
		),	
	),
	
	'cancel' => array(
		'CType' => 'action',
		'DisplayName' => getstr('admin.closeButton'),
		'ActionMask' => ACTION_SHOW,
		'AddTags' => array(
			'onclick' => 'window.self.close();return false;',
			'class' => 'frmbutton',
		),
	),
);

$kfor = new kfor($t, null, 'GET');

if ($kfor->lFieldArr['obj']['CurValue'] == 'story') {
	$kfor->lFormHtml = '
	{pos}{obj}
	<div class="t">
	<div class="b">
	<div class="l">
	<div class="r">
		<div class="bl">
		<div class="br">
		<div class="tl">
		<div class="tr">
			<table cellspacing="0" cellpadding="5" border="0" class="formtable">
			<colgroup>
				<col width="25%" />
				<col width="25%" />
				<col width="25%" />
				<col width="25%" />
			</colgroup>
			<tr><th colspan="4">Филтрирай по</th></tr>
			<tr>
				<td><b>{*stext}</b><br/>{stext}</td>
				<td><b>{*storytype}</b><br/>{storytype}</td>
				<td><b>{*rubrid}</b><br/>{rubrid}</td>
				<td><b>{*language}</b><br/>{language}</td>
			</tr>
			<tr><td colspan="4" align="right">{show} {cancel}</td></tr>
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
} elseif ($kfor->lFieldArr['obj']['CurValue'] == 'rubr') {
	$kfor->lFormHtml = '<p align="right" style="padding: 0 1%; width: 98%;">{pos}{obj}{cancel}</p>';
}

echo $kfor->Display();

$warr = array();
$join = '';
if ($kfor->lCurAction == 'show') {
	if ((int)$kfor->lFieldArr['storytype']['CurValue']) {
		if ((int)$kfor->lFieldArr['storytype']['CurValue'] == -1) {
			$warr[] = 's.storytype IS NULL';
		} else {
			$warr[] = 's.storytype = ' . (int)$kfor->lFieldArr['storytype']['CurValue'];
		}
	}
	
	if ((int)$kfor->lFieldArr['rubrid']['CurValue']) {
		$warr[] = 'sp.valint = ' . (int)$kfor->lFieldArr['rubrid']['CurValue'];
	}

	if ($kfor->lFieldArr['language']['CurValue']) {
		$warr[] = 's.lang = \'' . q($kfor->lFieldArr['language']['CurValue']) .'\'';
	}
	
	
	if ($kfor->lFieldArr['stext']['CurValue']) {
		$join = ' JOIN storiesft ft USING(guid) ';
		$warr[] = BuildT2SearchClause($kfor->lFieldArr['stext']['CurValue'], 'bg_utf8', array('s.title', 's.description', 's.nadzaglavie', 's.subtitle', 's.author'), array('ft.body'));
	}
}

if ($kfor->lFieldArr['obj']['CurValue'] == 'story') {
	$sql = 'SELECT DISTINCT ON (s.createdate, s.guid) s.guid, s.title as name
		FROM stories s 
		' . $join . '
		JOIN storyproperties sp ON s.guid = sp.guid AND sp.propid IN (1,4)
		JOIN languages l ON l.code = s.lang
		' . (count($warr) ? ' WHERE ' . implode(' AND ', $warr) : '') . '
		ORDER BY s.createdate DESC';
		
		$lAntetka = 'Статии';
} elseif ($kfor->lFieldArr['obj']['CurValue'] == 'rubr') {
	$sql = 'SELECT id as guid, ' . getsqlang('name') . '
		FROM rubr
		ORDER BY pos DESC';
		
	$lAntetka = 'Рубрики';
}

$t = '
	<tr>
		<td>{guid}</td>
		<td>{name}</td>
		<td align="right" nowrap>
		<a href="javascript: setObject(' . (int)$kfor->lFieldArr['pos']['CurValue'] . ', {guid}, \'{_hesc}\');"><img src="/img/add.gif" alt="Добави" title="Добави" border="0" /></a>
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
					<th class="gridtools" colspan="3">
						' . $lAntetka . '
					</th>
				</tr>
				<th>id</th><th>Име</th><th>&nbsp;</th>
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
$l->SetQuery($sql);

if (!$l->DisplayList($page)) {
	if (count($warr)) {
		$lNoData = 'Няма резултати отговарящи на зададените критерии!';
	} else {
		$lNoData = 'Няма записи!';
	}
	echo $lTableHeader . '<tr><td colspan="3"><p align="center"><b>' . $lNoData . '</b></p></td></tr>' . $lTableFooter;
}


HtmlEnd(1);

function hesc($p) {
	return addslashes(htmlspecialchars($p['name']));
}
?>