<?
$docroot = getenv("DOCUMENT_ROOT");
require_once($docroot . '/lib/static.php');

if (!(int)$_REQUEST['storyid'] && !$gPg) {
	echo 'Невалидна статия!';
	exit;
}

HtmlStart(1);

echo '
<script>
	function addphoto(guid, ftype, t){
		document.def1.guid.value = guid;
		document.def1.ftype.value = ftype;
		document.def1.title.value = t;
		window.location.hash = "#add";
	}
</script>
';

if (!$_GET['guid']) {
	$t = '
	<tr>
		<td valign="top" nowrap>
			<a href="javascript:addphoto({guid}, {ftype}, \'{_esctitle}\');">
				<img src="/img/add.gif" alt="' . getstr('admin.addButton') . '" title="' . getstr('admin.addButton') . '" border="0" />
			</a>
			{_getMMThumb}
		</td>
		<td valign="top">{_mmFtype}</td>
		<td valign="top">{title}</td>
		<td valign="top">{filenameupl}</td>
		<td valign="top">{_mmModDate}</td>
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
						<th class="gridtools" colspan="5">
							' . getstr('admin.selmedia.antetka') . '
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
	$l->SetPageSize(30);
	$l->SetOrderParams((int)$_GET['ordby'], (int)$_GET['ordd']);
	$l->SetAntet($gFArr);
	$l->SetQuery('SELECT title, author, lastmod, filenameupl, 
			length, (dim_x || \'x\' || dim_y) as dim, guid, ftype
		FROM getMedia()
	');

	if (!$l->DisplayList($page)) {
		echo $lTableHeader . '<tr><td colspan="5"><p align="center"><b>' . getstr('admin.selmedia.noData') . '</b></p></td></tr>' . $lTableFooter;
	}
}

$t = array(
	'guid' => array(
		'VType' => 'int',
		'CType' => 'hidden',
		'AllowNulls' => true,
	),
	
	'ftype' => array (
		'VType' => 'int',
		'CType' => 'hidden',
		'AllowNulls' => true,
	),
	
	'title' => array (
		'VType' => 'string',
		'CType' => 'text',
		'AddTags' => array(
			'readonly' => 'readonly',
			'class' => 'coolinp',
		),
		'DisplayName' => getstr('admin.selmedia.TitleFld'),
		'AllowNulls' => true,
	),	
	
	'storyid' => array(
		'VType' => 'int',
		'CType' => 'hidden',
		'AllowNulls' => false,
	),
	
	'place' => array (
		'VType' => 'int',
		'CType' => 'select',
		'SrcValues' => array(
			'0' => 'Не се показва в статията',
			'1' => 'Горе Дясно',
			'2' => 'Горе Ляво',
			'3' => 'Долу',
		),
		'AddTags' => array(
			'class' => 'coolinp',
		),
		'DisplayName' => getstr('admin.selmedia.PlaceFld'),
		'AllowNulls' => true,
		'DefValue' => 1,
	),
	
	'mediatxt' => array (
		'VType' => 'string',
		'CType' => 'textarea' ,
		'DisplayName' => getstr('admin.selmedia.UnderMediaTxt'),
		'AddTags' => array (
			'rows' => '3',
			'class' => 'coolinp',
		),

		'AllowNulls' => true
	),
	
	'save' => array (
		'CType' => 'action',
		'SQL' => 'SELECT * FROM AddMediaToStory({ftype}, {guid}, {storyid}, {place}, {mediatxt})',
		'ActionMask' => ACTION_CHECK | ACTION_CCHECK | ACTION_EXEC | ACTION_FETCH,
		'DisplayName' => getstr('admin.saveButton'),
		'AddTags' => array (
			'class' => 'frmbutton'
		)
	),
	
	'cancel' => array(
		'CType'	=> 'action',
		'DisplayName' => 'cancel',
		'ActionMask' =>	ACTION_SHOW,
		'AddTags' => array (
			'class' => 'frmbutton',
			'onclick' => 'window.close();',
		),
		'DisplayName' => getstr('admin.cancelButton'),
	)
);

$kfor = new kfor($t, $h, 'GET');
$kfor->debug = false;
if ($kfor->lCurAction == 'save') {
	if (!(int)$kfor->lFieldArr['guid']['CurValue'] || !(int)$kfor->lFieldArr['ftype']['CurValue']) {
		$kfor->SetError('Error', getstr('admin.selmedia.EmptyErr'));
	}
}
$kfor->ExecAction();

if ($kfor->lCurAction == 'save' && $kfor->lErrorCount == 0) {
	clearcacheditems('stories');
	echo '
		<script>
			window.opener.location.hash = "#media";
			window.opener.location.reload();
			window.close();
		</script>
	';
}

$kfor->lFormHtml = '
	{guid}{storyid}{ftype}
	<a name="add"></a>
	<div class="t">
	<div class="b">
	<div class="l">
	<div class="r">
		<div class="bl">
		<div class="br">
		<div class="tl">
		<div class="tr">
		<table cellspacing="0" cellpadding="5" border="0" class="formtable">
			<tr>
				<th colspan="3">' . getstr('admin.selmedia.addMedia') . '</th>
			</tr>
			<tr>
				<td colspan="2"><b>{*title}:</b><br/>{title}</td>
				<td><b>{*place}</b><br/>{place}</td>
			</tr>
			<tr>
				<td valign="top" colspan="3"><b>{*mediatxt}</b><br/>{mediatxt}</td>
			</tr>
			<tr>
				<td colspan="3" align="right">{save} {cancel}</td>
			</tr>
		</table>
';

echo $kfor->Display();

HtmlEnd(1);

function esctitle($pArr) {
	return addslashes($pArr['title']);
}

?>