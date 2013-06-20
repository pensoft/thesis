<?
$docroot = getenv("DOCUMENT_ROOT");
require_once($docroot . '/lib/static.php');

$gPg = $_GET['pg'];

if (!(int)$_REQUEST['storyid'] && !$gPg) {
	echo $_REQUEST['storyid'];
	echo "Невалидна статия!";
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
			'DisplayName' => 'Заглавие',
			'AddTags' => array(
				'class' => 'coolinp',
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
				2 => 'За справочник',
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
		),
	);

	//~ $frm = '
	//~ <p>{storyid}
	//~ <fieldset>
	//~ <legend>Филтрирай по:</legend>
	//~ <table>
		//~ <tr>
			//~ <td>{*title}: {title}</td>
			//~ <td>{*source}: {source}</td>
			//~ <td>{show}</td>
		//~ </tr>
	//~ </table>
	//~ </fieldset>
	//~ ';

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

if((int)$_REQUEST['product']) {
	$SrcValuesPhoto = array (
		3 => 'Галерия',
	);
} else {
	$SrcValuesPhoto =  array(
		0 => 'Не се показва в статията',
		1 => 'Горе Дясно',
		2 => 'Горе Ляво',
		3 => 'Долу',
		4 => 'Голяма снимка',
	);		
}

if (!$gPg) { 
	$t = array(
		"storyid" => array(
			"VType" => "int",
			"CType" => "hidden",
		),
		"product" => array(
			"VType" => "int",
			"CType" => "hidden",
			"DefValue" => (int)$_REQUEST['product'],
		),
		"sid" => array(
			"VType" => "int",
			"CType" => "hidden",
			'AllowNulls' => true,
		),	
		"photoid" => array(
			"VType" => "int",
			"CType" => "text",
			"DisplayName" => "",
			"AddTags" => array(
				"style" => "width: 100px",
				//~ "onfocus" => "blur();"
			),
			"AllowNulls" => false,
		),
		"place" => array (
			"VType" => "int" ,
			"CType" => "select" ,
			"SrcValues" => $SrcValuesPhoto,
			"DisplayName" => "Място",
			"AddTags" => array(
				"style" => "width: 100%",
			),
			"DefValue" => 1,
		),
		
		"valint3" => array(
			"VType" => "int",
			"CType" => "text",
			"DisplayName" => "Позиция",
			"AddTags" => array(
				"style" => "width: 100px",
			),
			"AllowNulls" => true,
		),
		
		"firstphoto" => array (
			"VType" => "int" ,
			"CType" => "checkbox" ,
			"SrcValues" => array (1=>""),
			"DisplayName" => "на първа",
			"AllowNulls" => true,
			"TransType" => MANY_TO_BIT,
		),	
		"phototext" => array(
			"VType" => "string",
			"CType" => "textarea",
			"DisplayName" => "Текст под снимка",
			"AllowNulls" => true,
			"AddTags" => array(
				"style" => "width: 95%;height: 100px;",
			),
		),	
		"showedit" => array(
			"CType" => "action",
			"Hidden" => true,
			"DisplayName" => "",
			"SQL" => "SELECT sp.valint3, (case when previewpicid = {photoid} then 1 else 0 end) as firstphoto , sp.valstr as phototext, sp.valint2 as place 
				FROM store_products s 
				LEFT JOIN storyproperties sp on (s.id = sp.guid AND sp.valint = {photoid}) 
				WHERE s.id = {storyid}",
			"ActionMask" => ACTION_CHECK | ACTION_EXEC | ACTION_FETCH | ACTION_SHOW,
		),
		"save" => array(
			"CType" => "action",
			"DisplayName" => "Запази",
			"SQL" => "SELECT * FROM AddPhotoToStory({photoid}, {storyid}, {place}, {phototext}, {firstphoto}, {sid}, {valint3})",
			"ActionMask" => ACTION_CHECK | ACTION_CCHECK | ACTION_EXEC | ACTION_FETCH,
			"AddTags" => Array(
				"class" => "frmbutton",
			)
			//"RedirUrl" => 'edit.php?tAction=showedit&guid={guid}&s=1',
		),
	);

	$kfor = new kfor($t, $h, "GET");
	$kfor->debug = false;

	$kfor->ExecAction();

	if ($kfor->lCurAction == 'save' && $kfor->lErrorCount == 0) {
		clearcacheditems('stories');
		echo '
			<script>
				window.opener.location.hash = "#snimki";
				window.opener.location.reload();
				window.close();
			</script>
		';
	}

	$kfor->lFormHtml = '{product}
		<a name="add"></a>
		<p align="center"><table width="80%" cellspacing="0" cellpadding="2" border="0" class="formtable">
		<tr>
			<th colspan="4">' . ( $kfor->lFieldArr["photoid"]["CurValue"] ? 'Редактиране' : 'Добавяне' ) . ' на снимка</th>
		</tr>
			<tr>
				<td width="20%"><b>ID:</b><br>{photoid}</td>
				<td align="right"><b>Представителна снимка:</b> {firstphoto}</td>
			</tr>
			<tr>
				<td><b>{*valint3}:</b><br>{valint3}</td>
				<td><b>{*place}:</b><br>{place}</td>
			</tr>
			<tr>
				<td colspan="2"><b>{*phototext}:</b><br>{phototext}</td>			
			</tr>
			<tr>
				<td align="right" colspan="2" valign="bottom"><hr>{save} {storyid} {sid}</td>
			</tr>
		</table>
	';

	echo $kfor->Display();
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