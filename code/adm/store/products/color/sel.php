<?
$docroot = getenv("DOCUMENT_ROOT");
require_once($docroot . '/lib/static.php');

HtmlStart(1);

echo '
<script>
	function addphoto(guid){
		window.opener.document.forms[\'def1\'].picid.value = guid;
		window.opener.document.getElementById(\'provlogo\').src = \'/showimg.php?filename=d115x35_\' + guid + \'.jpg&rld=\' + Math.random();
		window.close();
	}
</script>
';
	
$fld = array(
	'storyid' => array(
		'VType' => 'int',
		'CType' => 'hidden',
	),
	
	'title' => array(
		'VType' => 'string',
		'CType' => 'text',
		'DisplayName' => 'Заглавие',
		'AddTags' => array(
			'style' => 'width: 180px;',
		),
		'AllowNulls' => true,
	),
	
	'source' => array(
		'CType' => 'select',
		'VType' => 'int',
		'SrcValues' => array(
			null => '--',
			0 => 'За статия',
			1 => 'За продукт',
			2 => 'За доставчик',
		),
		'DisplayName' => 'Категория',
		'AllowNulls' => true,
		'AddTags' => array(
			'style' => 'width: 140px;',
		),
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
);

$frm = '
<p>{storyid}
<fieldset>
<legend>Филтрирай по:</legend>
<table>
	<tr>
		<td>{*title}: {title}</td>
		<td>{*source}: {source}</td>
		<td>{show}</td>
	</tr>
</table>
</fieldset>
';


$kfor = new kfor($fld, $frm, 'GET');
echo $kfor->Display();

$addWhere = '';
if ($kfor->lCurAction == 'show') {
	if ($kfor->lFieldArr['title']['CurValue']) {
		$addWhere .= ' AND lower(title) LIKE \'%' . q(mb_strtolower($kfor->lFieldArr['title']['CurValue'], 'UTF-8')) . '%\' ';
	}
	
	if (!is_null($kfor->lFieldArr['source']['CurValue'])) {
		$addWhere .= ' AND source = ' . (int)$kfor->lFieldArr['source']['CurValue'];
	}
}

$gList = new DBList( '
	<p><table width="100%" cellspacing="0" cellpadding="2" border="0" class="datatable">
		<tr bgcolor="#758AB7">
			<td colspan="3" style="color: #FFFFFF;"><b>Снимки</b></td>
		</tr>' );
$gList->SetTemplate('
	<tr>
		<td width="75"><img src="/showimg.php?filename=s_{guid}.jpg" alt="{title}" border="0" width="75" /></td>
		<td width="280">{title}</td>
		<td width="15"><a href="javascript:addphoto({guid});"><img src="/img/add.gif" alt="Добави" title="Добави" border="0" /></td>
	</tr>');
$gList->SetQuery('SELECT guid, title, filenameupl, createdate::date FROM photos WHERE ftype = 0 ' . $addWhere);
$gList->SetPageSize(10);
$gList->SetAlternateColors(true);
if (!$gList->DisplayList((int)$_GET["p"])) {
	echo 'Няма върнати записи!';
}
echo '<hr width="100%" noshade>';

HtmlEnd(1);
?>