<?
$docroot = getenv("DOCUMENT_ROOT");
require_once($docroot . '/lib/static.php');

$gPg = $_GET['pg'];
$page = (int)$_GET['p'];

if (!(int)$_REQUEST['storyid'] && !$gPg) {
	echo 'Невалидна статия!';
	exit;
}

HtmlStart(1);

echo '
<script>
	function addphoto(guid){
		document.def2.photoid.value = guid;
		window.location.hash = "#add";
	}
</script>
';

if (!$_GET['photoid']) {
	
	$fld = array(
		'storyid' => array(
			'VType' => 'int',
			'CType' => 'hidden',
		),
		
		'title' => array(
			'VType' => 'string',
			'CType' => 'text',
			'DisplayName' => getstr('admin.selphoto.TitleFlt'),
			'AddTags' => array(
				'class' => 'coolinp',
			),
			'AllowNulls' => true,
		),
		
		'source' => array(
			'CType' => 'hidden',
			'VType' => 'int',
			'SrcValues' => array(
				null => '--',
				0 => 'За статия',
				1 => 'За продукт',
				2 => 'За справочник',
			),
			'DisplayName' => getstr('admin.selphoto.SourceFlt'),
			'AllowNulls' => true,
			'DefValue' => 0,
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
	);
	
	$frm = '{storyid}
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
						<col width="50%"/>
						<col width="50%"/>
					</colgroup>
					<tr>
						<th colspan="2">' . getstr('admin.stories.filter') . '</th>
					</tr>
					<tr>
						<td>{*title}<br/>{title}</td>
					</tr>
					<tr>
						<td colspan="2" align="right">{show}</td>
					</tr>
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

	
	$kfor = new kfor($fld, $frm, 'GET');
	echo $kfor->Display();
	
	$addWhere = '';
	if ($kfor->lCurAction == 'show') {
		if ($kfor->lFieldArr['title']['CurValue']) {
			$addWhere .= ' AND lower(title) LIKE \'%' . q(mb_strtolower($kfor->lFieldArr['title']['CurValue'], 'UTF-8')) . '%\' ';
		}
		$addWhere .= ' AND source = 0 ';
		
	}
	
	$gFArr = array(
		1000 => array('caption' => '  ', 'deforder' => 'asc'),
		2 => array('caption' => getstr('admin.selphoto.TitleFlt'), 'deforder' => 'asc'), 
		4 => array('caption' => getstr('admin.selphoto.CreateDate'), 'def', 'deforder' => 'desc'), 
		1001 => array('caption' => '  ', 'deforder' => 'asc'),
	);
	
	$t = '
		<tr>
			<td><img src="/showimg.php?filename=s_{guid}.jpg" alt="{title}" border="0" width="75" /></td>
			<td>{title}</td>
			<td>{createdate}</td>
			<td align="right">
				<a href="javascript:addphoto({guid});">
					<img src="/img/add.gif" alt="' . getstr('admin.addButton') . '" title="' . getstr('admin.addButton') . '" border="0" />
				</a>
				</td>
		</tr>'
	;
	
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
					<colgroup>
						<col width="10%" />
						<col width="40%" />
						<col width="40%" />
						<col width="10%" />
					</colgroup>
					<tr>
						<th class="gridtools" colspan="4">
							' . getstr('admin.selphoto.antetka') . '
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
	$l->SetQuery('SELECT guid, title, filenameupl, createdate::date FROM photos WHERE ftype = 0 ' . $addWhere);

	if (!$l->DisplayList($page)) {
		echo $lTableHeader . '<tr><td colspan="4"><p align="center"><b>' . getstr('admin.selphoto.noData') . '</b></p></td></tr>' . $lTableFooter;
	}
}

if (!$gPg) { 
	$t = array(
		'storyid' => array(
			'VType' => 'int',
			'CType' => 'hidden',
		),
		
		'sid' => array(
			'VType' => 'int',
			'CType' => 'hidden',
			'AllowNulls' => true,
		),	
		
		'photoid' => array(
			'VType' => 'int',
			'CType' => 'text',
			'DisplayName' => 'ID',
			'AddTags' => array(
				'class' => 'coolinp',
			),
			'AllowNulls' => false,
		),
		
		'valint3' => array(
			'VType' => 'int',
			'CType' => 'text',
			'DisplayName' => getstr('admin.selphoto.PosFld'),
			'AddTags' => array(
				'class' => 'coolinp',
			),
			'AllowNulls' => true,
		),
		
		'firstphoto' => array (
			'VType' => 'int' ,
			'CType' => 'checkbox' ,
			'SrcValues' => array (1=>''),
			'DisplayName' => 'на първа',
			'DisplayName' => getstr('admin.selphoto.FirstPhotoFld'),
			'AllowNulls' => true,
			'TransType' => MANY_TO_BIT,
		),
		
		'phototext' => array(
			'VType' => 'string',
			'CType' => 'textarea',
			'DisplayName' => getstr('admin.selphoto.UnderPicTxt'),
			'AllowNulls' => true,
			'AddTags' => array(
				'class' => 'coolinp',
			),
		),
		
		'showedit' => array(
			'CType' => 'action',
			'Hidden' => true,
			'DisplayName' => '',
			'SQL' => 'SELECT sp.valint3, (case when previewpicid = {photoid} then 1 else 0 end) as firstphoto , sp.valstr as phototext, sp.valint2 as place 
				FROM stories s 
				LEFT JOIN storyproperties sp on (s.guid = sp.guid AND sp.valint = {photoid}) 
				WHERE s.guid = {storyid}',
			'ActionMask' => ACTION_CHECK | ACTION_EXEC | ACTION_FETCH | ACTION_SHOW,
		),
		
		'save' => array(
			'CType' => 'action',
			'DisplayName' => getstr('admin.saveButton'),
			'SQL' => 'SELECT * FROM AddPhotoToStory({photoid}, {storyid}, 3, {phototext}, {firstphoto}, {sid}, {valint3})',
			'ActionMask' => ACTION_CHECK | ACTION_CCHECK | ACTION_EXEC | ACTION_FETCH,
			'AddTags' => Array(
				'class' => 'frmbutton',
			)
		),
	);

	$kfor = new kfor($t, $h, 'GET');
	$kfor->debug = false;

	$kfor->ExecAction();

	if ($kfor->lCurAction == 'save' && $kfor->lErrorCount == 0) {
		clearcacheditems2('stories', ($kfor->lFieldArr['sid']['CurValue'] ? $kfor->lFieldArr['sid']['CurValue'] : 1));
		echo '
			<script>
				window.opener.location.hash = "#snimki";
				window.opener.location.reload();
				window.close();
			</script>
		';
	}
	
	$addLabel = getstr('admin.selphoto.addLabel');
	$editLabel = getstr('admin.selphoto.editLabel');
	
	$kfor->lFormHtml = '
		{storyid}{sid}
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
					<th colspan="4">' . getstr('admin.selphoto.addPic', array('addoredit' => ($kfor->lFieldArr['photoid']['CurValue'] ? $editLabel : $addLabel))) . '</th>
				</tr>
					<tr>
						<td width="20%"><b>{*photoid}:</b><br>{photoid}</td>
						<td align="right"><b>{*firstphoto}:</b> {firstphoto}</td>
					</tr>
					<tr>
						<td><b>{*valint3}:</b><br>{valint3}</td>
					</tr>
					<tr>
						<td colspan="2"><b>{*phototext}:</b><br>{phototext}</td>			
					</tr>
					<tr>
						<td align="right" colspan="2" valign="bottom">{save}</td>
					</tr>
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

	echo $kfor->Display();
}

HtmlEnd(1);
?>