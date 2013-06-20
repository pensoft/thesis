<?php
$docroot = getenv("DOCUMENT_ROOT");
require_once($docroot . '/lib/static.php');

function ReplaceHtmlFields($pStr, $rStr) {
	return str_replace("{id}", $rStr, $pStr);
}
define('STORY_URL', '/show.php?storyid={id}');
define('RUBR_URL', '/prod.php?cat={id}');
define('STATIC_URL', '/st.php?page={id}');

HtmlStart(1);

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
			'style' => 'width: 200px;',
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
			'style' => 'width: 200px;',
		),
		'AllowNulls' => true,
	),
	
	"stext" => array(
		"VType" => "string",
		"CType" => "text",
		"DisplayName" => "Ключова дума",
		"AllowNulls" => true,
	),

	'language' => array(
		'VType' => 'string',
		'CType' => 'select',
		'DisplayName' => 'Език:',
		'SrcValues' => 'SELECT null AS id, \'--\' as name
					UNION
					SELECT code as id, name FROM languages ORDER BY name',
	),

	'show' => array(
		'CType' => 'action',
		'DisplayName' => 'Покажи',
		'SQL' => '',
		'ActionMask' => ACTION_CHECK | ACTION_SHOW,
		'AddTags' => array(
			'class' => 'frmbutton',
		),	
	),
	
	'cancel' => array(
		'CType' => 'action',
		'DisplayName' => 'Затвори',
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
	<fieldset>
	<legend>Филтрирай по:</legend>
	<table>
		<tr>
			<td>{*stext}</td>
			<td>{*storytype}</td>
			<td>{*rubrid}</td>
			<td>{*language}</td>
		</tr>
		<tr>
			<td>{stext}</td>
			<td>{storytype}</td>
			<td>{rubrid}</td>
			<td>{language}</td>
			<td>{obj}{show} {cancel}</td>
		</tr>
	</table>
	</fieldset>
	';
} elseif ($kfor->lFieldArr['obj']['CurValue'] == 'rubr') {
	$kfor->lFormHtml = '{obj}{cancel}';
} elseif ($kfor->lFieldArr['obj']['CurValue'] == 'static') {
	$kfor->lFormHtml = '{obj}{cancel}';
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
	function hesc($p) {
		return addslashes(htmlspecialchars($p['name'])) . '\', \'' . ReplaceHtmlFields(STORY_URL, $p['guid']);
	}
	$sql = 'SELECT DISTINCT ON (s.createdate, s.guid) s.guid, s.title as name
		FROM stories s 
		' . $join . '
		JOIN storyproperties sp ON s.guid = sp.guid AND sp.propid IN (1,4)
		JOIN languages l ON l.code = s.lang
		' . (count($warr) ? ' WHERE ' . implode(' AND ', $warr) : '') . '
		ORDER BY s.createdate DESC';
	echo '
	<script>
		function setObject(pName, pId) {
			var x=window.opener.document.getElementsByName("' . getsqlang('name') . '");
			var y=window.opener.document.getElementsByName("' . getsqlang('href') . '");
			x[0].value = pName
			y[0].value = pId
			window.close();
			return false;	
		}
	</script>';
} elseif ($kfor->lFieldArr['obj']['CurValue'] == 'rubr') {
	$sql = 'SELECT id as guid, ';
	$languages = count($_SESSION['langs']);
	foreach ($_SESSION['langs'] as $k => $v) {
		$colsarr[$k] .= 'name' . $k;
		if ($k < $languages) {
			$sql .= 'name[' . $k . '] as "name' . $k . '",';
		}
		else {
			$sql .= 'name[' . $k . '] as "name' . $k . '"';
		}
	}
	$cols = implode(', ', $colsarr);
	function hesc($p) {
		foreach ($_SESSION['langs'] as $k => $v) {
			$ret .= addslashes(htmlspecialchars($p['name' . $k . ''])) . '\', \'';
		}
		return mb_substr($ret, 0, mb_strlen($ret) -4) . '\', \'' .  ReplaceHtmlFields(RUBR_URL, $p['guid']);
	}
	 $sql .= '
		FROM rubr
		ORDER BY pos DESC';

	echo '
	<script>
		function setObject(' . $cols . ', pId) {';
	foreach ($_SESSION['langs'] as $k => $v) {
		echo '
			var x' . $k . '=window.opener.document.getElementsByName("name[' . $k . ']");
			var y' . $k . '=window.opener.document.getElementsByName("href[' . $k . ']");
			x' . $k . '[0].value = name' . $k . '
			y' . $k . '[0].value = pId';
	}
	echo '
			window.close();
			return false;	
		}
	</script>';
} elseif ($kfor->lFieldArr['obj']['CurValue'] == 'static') {
	function hesc($p) {
		foreach ($_SESSION['langs'] as $k => $v) {
			$ret .= addslashes(htmlspecialchars(trim($p['title' . $k . '']))) . '\', \'';
		}
		return mb_substr($ret, 0, mb_strlen($ret) -4) . '\', \'' .  ReplaceHtmlFields(STATIC_URL, $p['name']);
	}
	
	$sql = 'SELECT a.static_id as guid, a.artname as name, ';
	
	$languages = count($_SESSION['langs']);
	foreach ($_SESSION['langs'] as $k => $v) {
		$colsarr[$k] .= 'title' . $k;
		if ($k < $languages) {
			$sql .= 'a.title[' . $k . '] as "title' . $k . '",';
		}
		else {
			$sql .= 'a.title[' . $k . '] as "title' . $k . '"';
		}
	}
	
	$cols = implode(', ', $colsarr);
	
	$sql .= ' FROM (
		SELECT sa.static_id, sa.artname, string_to_array(aggr_concat(s.title), \';\') as title
		FROM static_article sa 
		JOIN stories s ON s.guid = ANY(sa.artid) 
		GROUP BY sa.static_id, sa.artname
	) a 
	';
	
	echo '
	<script>
		function setObject(' . $cols . ', pId) {';
	foreach ($_SESSION['langs'] as $k => $v) {
		echo '
			var x' . $k . '=window.opener.document.getElementsByName("name[' . $k . ']");
			var y' . $k . '=window.opener.document.getElementsByName("href[' . $k . ']");
			x' . $k . '[0].value = title' . $k . '
			y' . $k . '[0].value = pId';
	}
	echo '
			window.close();
			return false;	
		}
	</script>';
}

$th = '
	<p>
	<table class="datatable" cellspacing="0" cellpadding="0" width="100%">
	<th>id</th><th>Име</th><th>&nbsp;</th>
';

if ($kfor->lFieldArr['obj']['CurValue'] == 'story' || $kfor->lFieldArr['obj']['CurValue'] == 'static') {
	$fld = '{name}';
} else {
	$fld = '{name' . getlang() . '}';
}


$tr = '<tr>
		<td>{guid}</td>
		<td>' . $fld . '</td>
		<td align="right" nowrap>
		<a href="javascript: setObject(\'{_hesc}\');"><img src="/img/add.gif" alt="Добави" title="Добави" border="0" /></a>
		</td>
	</tr>
';

$page = (int)$_GET['p'];

$l = new DBList($th);
$l->SetQuery($sql);
$l->SetTemplate($tr);
$l->SetPageSize(20);
$l->SetAlternateColors(true);
if (!$l->DisplayList($page)) {
	if (count($warr)) {
		echo '<p>Няма резултати отговарящи на зададените критерии!';
	} else {
		echo '<p>Няма записи!';
	}
	
}

HtmlEnd(1);

?>