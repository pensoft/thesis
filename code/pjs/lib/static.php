<?php
$x = session_start();

function phpLogError($errno, $errstr, $errfile, $errline) {
    global $_logged_php_errors;
    $_logged_php_errors[] = array($errno, $errstr, $errfile, $errline);
}

function phpGetLoggedErrors() {
	global $_logged_php_errors;
	$lRet = "";
	$errortype = array (
                E_ERROR              => 'Error',
                E_WARNING            => 'Warning',
                E_PARSE              => 'Parsing Error',
                E_NOTICE             => 'Notice',
                E_CORE_ERROR         => 'Core Error',
                E_CORE_WARNING       => 'Core Warning',
                E_COMPILE_ERROR      => 'Compile Error',
                E_COMPILE_WARNING    => 'Compile Warning',
                E_USER_ERROR         => 'User Error',
                E_USER_WARNING       => 'User Warning',
                E_USER_NOTICE        => 'User Notice',
                E_STRICT             => 'Runtime Notice',
                E_RECOVERABLE_ERROR  => 'Catchable Fatal Error',
                E_DEPRECATED 		 => 'Deprecated',
				E_USER_DEPRECATED    => 'User deprecated'
                );
	sort($_logged_php_errors);
	$old = array();
	foreach ($_logged_php_errors as $key => $e) {
		//if ($old == $e) continue;
		$i = $e[0];
		$file = str_replace(PATH_CHECKOUT, '', $e[2]);
		$lRet .= "<tr>
					 <td style='padding-left: 195px;'><b>$errortype[$i]</b>: $e[1]</td>
					 <td>$file</td>
					 <td>$e[3]</td>
				  </tr>";
		$old = $e;
	}
	if ($lRet != "")
    return '<div class="P-Clear"></div>
    		<table style="margin: auto" cellspacing="10"><tr>
    			<th style="padding-left: 195px;">Description</th>
    			<th>File</th>
    			<th>Line</th></tr>
    			'. $lRet . '</table>';
}

$docroot = getenv('DOCUMENT_ROOT');
require_once($docroot . '/lib/static-conf.php');
require_once($docroot . '/lib/conf.php');
require_once(PATH_ECMSFRCLASSES . 'static.php');
require_once(PATH_CLASSES . 'static.php');
require_once(PATH_CLASSES . SITE_NAME . '/templates/static.php');

$user = unserialize($_SESSION['suser']);

CleanCookiesFromRequest();
// crewrite instance
$rewrite =& new crewrite();

function CleanCookiesFromRequest() {
	foreach ($_COOKIE as $k => $v) {
		unset($_REQUEST[$k]);
	}
}

/* ### USER SECTION BEGIN ### */
$COOKIE_DOMAIN = $_SERVER['SERVER_NAME'];
$user = unserialize($_SESSION['suser']);

if (!$user->id && $_COOKIE['rememberme']) {
	$user = new clogin($_COOKIE['rememberme'], $_SERVER['REMOTE_ADDR']);
	$_SESSION['suser'] = serialize($user);
	if ($user->id) {
		header('Location: ' . $_SERVER['REQUEST_URI']);
		exit();
	}
}
// updateUsrLastAccess();

function updateUsrLastAccess() {
	global $user;

	if ((int)$user->id) {
		$lCon = Con();
		$lCon->Execute('UPDATE usr SET access_date = CURRENT_TIMESTAMP WHERE id = ' . (int)$user->id);
	}
}
/* ### USER SECTION END ### */

$gRubrMatch = array(
	// rubrid - menuid
	//2 => , // Новини
	7 => 3, // За OLSEN & PARTNERS
	9 => 12, // Другите за нас
	//13 => , // Слайдер
	//25 => , // Може да ви е интересно
	29 => 4, // Нашият екип
	45 => 5, // Услуги
	46 => 6, // Счетоводсто
		//47 => 7, // За юристи
		//48 => 8, // За Туристически компании
		//49 => 9, // За НПО
	50 => 10, // Данъци
	51 => 11, // Право
	52 => 13, // Нашите казуси
	54 => 15,
	53 => 15,
	// запивтване, контакти, за нас /титулна страница/
);

function modifyPageUrlStringParam($url, $par, $val) {
	if (preg_match('/[?&]' . $par . '=[^&]+/', $url)) {
		$res = preg_replace('/([?&])' . $par . '=[^&]+/', '${1}' . $par . '=' . $val, $url);
	}else {
		if (preg_match('/[?].*=\w+/', $url)) {
			$res = $url . '&' . $par . '=' . $val;
		}else{
			$res = $url . '?' . $par . '=' . $val;
		}
	}
	return $res;
}

function DefObjTempl() {
	global $user;
	global $rewrite;
	$t = array(
		'langswitcher'=>array('ctype'=>'clang', 'templs'=>array()),

		'mainmenu'=>array(
			'ctype'=>'crsrecursive',
			'templs'=>array(
				G_HEADER=>'menu.main-start',
				G_ROWTEMPL=>'menu.main-row',
				G_FOOTER =>'menu.main-end',
			),
			'recursivecolumn'=>'parentid',
			'templadd'=>'type',
			'sqlstr'=>'select id,'.getsqlang('name').','.getsqlang('href').','.getsqlang('img').',type,parentid from getMenuContents('.MAIN_MENU_ID.',0,'.CMS_SITEID.','.getlang().')',
			//'cache'=>'menu'
		),

		'footermenu'=>array(
			'ctype'=>'crs',
			'templs'=>array(
				G_HEADER=>'menu.footerstart',
				G_ROWTEMPL=>'menu.footerrow',
				G_FOOTER =>'menu.footerend',
			),
			'recursivecolumn'=>'parentid',
			'templadd'=>'type',
			'sqlstr'=>'select id,'.getsqlang('name').','.getsqlang('href').','.getsqlang('img').',type,parentid from getMenuContents('.MAIN_MENU_ID.',0,'.CMS_SITEID.','.getlang().') where parentid ='.MAIN_MENU_ID,
			//'cache'=>'menu'
		),

		'metadata'=>array(
			'ctype'=>'cmetadata',
			'templs'=>array(
				G_DEFAULT=>'global.metadata',
			),
		),

		'breadcrumbs' => getCpath(),

		'interesting' => array(
			'ctype' => 'crs',
			'templs' => array(
				G_HEADER => 'home.dottedRubrListStart',
				G_ROWTEMPL => 'home.dottedRubrListRow',
				G_FOOTER => 'home.dottedRubrListEnd',
			),
			//'_rewrite_' => $rewrite,

			//~ рубрика

				//~ 'sqlstr' => 'SELECT DISTINCT ON ( s.guid ) s.guid, s.title, ' . getsqlang('r.name') . ' as mainrubrname, r.id as rubrid
					//~ FROM stories s
					//~ JOIN sites si ON (si.guid = s.primarysite)
					//~ JOIN storyproperties sp ON s.guid = sp.guid
					//~ JOIN rubr r on r.id = sp.valint AND ( sp.propid = 4 OR sp.propid = 1 )
					//~ WHERE s.pubdate < now() AND s.state IN (3,4) AND r.rootnode = '. INTERESTIGN_RUBRID . ' AND s.lang=\''.getlang(true).'\'
					//~ ORDER BY s.guid, s.pubdate DESC',

			//~ нареден списък
			'sqlstr'=>'SELECT s.guid, s.title, s.description, s.previewpicid, ' . getsqlang('r.name') . ' as mainrubrname, r.id as rubrid
			FROM stories s
			JOIN storyproperties sp ON s.guid = sp.guid
			JOIN rubr r on r.id = sp.valint AND sp.propid = 4
			JOIN listdets d ON d.objid = s.guid
			JOIN listnames l ON l.listnameid = d.listnameid
			WHERE s.state IN (3, 4) AND l.listnameid = ' . LIST_INTERESTING_ID . ' AND s.lang=\''.getlang(true).'\'
			ORDER BY d.posid, s.pubdate DESC
		',
	)


		/*'loginform' => array(
			'ctype' => 'csimple',
			'err' => GetLoginErr(),
			'fullname' => $user->fullname,
			'templs' => array(
				G_DEFAULT => ($user->id ? 'loginform.logged' : 'loginform.unlogged'),
			),
		),*/
	);

	return $t;
}

function showPicIfExists($pid, $pref, $class = false) {
	if ($pid) {
		return '<img src="' . SHOWIMG_URL .
		$pref . '_' . $pid . '.jpg" border="0" alt="" ' .
		($class ? 'class="' . $class . '"' : '') . '/>';
	}
	return '';
}

function showSlidePicIfExists($pid, $pref, $preflref, $class = false) {
	$lImgPath = PATH_DL . $pref . '_' . $pid . '.jpg';
	if ($pid) {
		// показване на големия имидж
		$lCont = '<img src="' . SHOWIMG_URL .
					$pref . '_' . $pid . '.jpg" border="0" alt="" ' .
					($class ? 'class="' . $class . '"' : '') . '/>';
		// ако е създаден успешно правим огледално изображение
		if (is_file($lImgPath))
			//~ $lCont .= '<img class="reflection" style=""
					//~ src="' . SHOWIMG_URL .
					//~ $preflref . '_' . $pid . '.jpg" border="0" alt="" ' .
					//~ ($class ? 'class="' . $class . '"' : '') . '/>';

		return $lCont;
	}
	return '';
}

function showPicIfExistsInFrame($pid, $pref, $class = false) {
	if ($pid) {
		return '<div class="' . $class . '"><img src="' . SHOWIMG_URL .
		$pref . '_' . $pid . '.jpg" border="0" alt="" ' .
		($class ? 'class=""' : '') . '/></div>';
	}
	return '';
}

function showItemIfExists ($item, $leftCont, $rightCont, $nl2br = false) {
	return ($item ? $leftCont . ($nl2br ? nl2br($item) : $item) . $rightCont : '');
}

function CutText($d, $len = 80) {
	$d = strip_tags($d);
	if (mb_strlen($d, 'UTF-8') < $len) return $d;
	$cut = mb_substr($d, 0, $len, 'UTF-8');
	return mb_substr($cut, 0, mb_strrpos($cut, ' ', 'UTF-8'), 'UTF-8') . '...';
}

function GetLoginErr() {
	global $user;
	if (!isset($user->errcode)) return '';
	if ((int)$user->errcode == 3) return '';
	$ea = array(
		0 => getstr('loginform.nosuchuser'),
		1 => getstr('loginform.noactiveuser'),
		2 => getstr('loginform.ipdenied'),
	);
	unset($_SESSION['suser']);
	return $ea[(int)$user->errcode];
}

function showCaptchaIfExists($pRs){
	//~ var_dump($pRs);
	if( (int) $pRs['hascaptcha'] )
		return '
			<div class="label">
				' . getstr('global.captchalabel') . '
			</div>
			<div class="captcha">
				<img src="/lib/frmcaptcha.php" id="cappic" border="0" alt="" class="captchaimage" /> &raquo;
				<input type="text" class="captchacode" name="captcha"></input>
				<div class="unfloat"></div>
			</div>
	';
}

function showPlayerByType($pFtype, $pId, $pHtml = false) {
	$lFtype = (int)$pFtype;
	$lId = (int)$pId;

	if ($lFtype && $lId) {
		switch ($lFtype) {
			case 3: // Audio
				return '
					<object style="vertical-align: middle;" classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,40,0" width="150" height="20">
						<param name="movie" value="/singlemp3player.swf?file=/getatt.php?filename=o_' . $lId . '.mp3&songVolume=80&showDownload=false" />
						<param name="quality" value="high">
						<param name="wmode" value="transparent" />
						<embed
							style="vertical-align: middle;"
							width="150"
							height="20"
							src="/singlemp3player.swf?file=/getatt.php?filename=o_' . $lId . '.mp3&songVolume=80&showDownload=false"
							quality="high"
							wmode="transparent"
							type="application/x-shockwave-flash"
							pluginspage="http://www.macromedia.com/go/getflashplayer"
						/>
					</object>
				';
				break;
			case 4: // Video
				return '
					<embed
						width="435"
						height="326"
						src="/mediaplayer.swf"
						quality="high"
						wmode="transparent"
						flashvars="width=435&amp;height=326&amp;autostart=false&amp;file=/getatt.php?filename=oo_' . $lId . '.flv&amp;type=flv&amp;repeat=false&amp;image=/showimg.php?filename=big_' . $lId . '.jpg&amp;showdownload=false&amp;link=/getatt.php?filename=oo_' . $lId . '.flv&amp;allowfullscreen=true&amp;showdigits=true&amp;shownavigation=true&amp;logo=&amp;largecontrols=false&amp;backcolor=0xffffff&amp;frontcolor=0x000000&amp;lightcolor=0x000000&amp;screencolor=0x000000"
						menu="false"
						allowfullscreen="true"
						loop="false"
						play="false"
						type="application/x-shockwave-flash"
						pluginspage="http://www.macromedia.com/go/getflashplayer"
					/>
				';
				break;
			case 5: // Embed video
				return $pHtml;
				break;
			default:
				break;

		}
	}
}


function formatDate( $pPubDate ){
	// Понеделник, 25 Юли 2011 16:17
	//29/07/2011 16:19:00

	global $gMonths;
	global $gDaysOfWeek;



	$lDatePattern = "#(\d+)/(\d+)/(\d+) (\d+):(\d+):(\d+)#isu";
	$lMatches;
	$l = preg_match( $lDatePattern, $pPubDate, $lMatches );

	// това няма откъде да го взема, така че викам дате
	$lWeekDay = $gDaysOfWeek[ (int)date(  'N' , strtotime( $lMatches[3] . '-' . $lMatches[2] . '-' . $lMatches[1] ) ) ];
	//var_dump((int)date(  'N' , strtotime( $lMatches[3] . '-' . $lMatches[2] . '-' . $lMatches[1] ) ) .  $lWeekDay );

	$lDate = $lWeekDay . ', ' . $lMatches[1] . ' ' . $gMonths[(int)$lMatches[2]] . ' ' . $lMatches[3] . ' ' . $lMatches[4]. ':' . $lMatches[5] ;

	return $lDate;
}

function formatDateSimple ( $pPubDate , $pStoryId = false ){
	// Понеделник, 25 Юли 2011 16:17
	//29/07/2011 16:19:00

	global $gMonths;
	global $gDaysOfWeek;
	global $gContactsStoryIds;

	$lDatePattern = "#(\d+)/(\d+)/(\d+) (\d+):(\d+):(\d+)#isu";
	$lMatches;
	$l = preg_match( $lDatePattern, $pPubDate, $lMatches );

	// това няма откъде да го взема, така че викам дате
	$lWeekDay = $gDaysOfWeek[ (int)date(  'N' , strtotime( $lMatches[3] . '-' . $lMatches[2] . '-' . $lMatches[1] ) ) ];

	//$lDate = $lWeekDay . ', ' . $lMatches[1] . ' ' . $gMonths[(int)$lMatches[2]] . ' ' . $lMatches[3] . ' ' . $lMatches[4]. ':' . $lMatches[5] ;
	$lDate = $lMatches[1] . ' ' . $gMonths[(int)$lMatches[2]] . ' ' . $lMatches[3];

	if( in_array($pStoryId, $gContactsStoryIds ) ) {
		return '';
	} else {
		return $lDate;
	}

}

function getOnlyDatePart($pDate){
	$lDateArr = explode(' ', $pDate);
	return $lDateArr[0];
}

function GetMainRubrid($pRubrid){
		$lCon = Con();
		//$lSql = 'SELECT rubrid FROM spGetMainRubrid(' . (int) $pRubrid . ')';
		$lSql = 'SELECT rootnode as rubrid FROM rubr WHERE id = ' . (int) $pRubrid;
		$lCon->Execute($lSql);
		$lCon->MoveFirst();
		$lRootNode = (int) $lCon->mRs['rubrid'];
		return $lRootNode;
}

function getCpath(){
	global $gRubrid;
	global $gStoryTitle;
	global $gStoryid;

	$lCpath = array(
				'ctype' => 'cpathtxt',
				'rubrid' => $gRubrid,
				'templs' => array(
					G_HEADER => 'pathtxt.header',
					G_FOOTER => 'pathtxt.footer',
					G_STARTRS => 'global.empty',
					G_ENDRS => 'global.empty',
					G_ROWTEMPL => 'pathtxt.rowtempl',
					G_NODATA => 'global.empty',
					G_PAGEING => 'global.empty'
				),
				'flag' => 0,
				//'_rewrite_' => $rewrite,
				'pathlink' => '/browse.php?rubrid=',
				//'storytitle' => $lContent->GetVal('name'),
				'storyid' => $gStoryid,
				'storytitle' => $gStoryTitle,
		);

	return $lCpath;
}

function isActiveTab( $pMenuId ){
	global $gMainRubrid;
	global $gRubrMatch;

	if( $pMenuId == HOME_MENU_ID ) return 'selected';
	if( $gRubrMatch[$gMainRubrid] == $pMenuId) return 'selected';
}

function checkEmailAddr($pFld) {
	if(!preg_match("/^[A-Za-z0-9_\.-]+@([A-Za-z0-9_\.-])+\.[A-Za-z]{2,6}$/",  $pFld ))
		return 'Not a valid email';
}

function getBaseRegError($pErrCnt){
	if((int)$pErrCnt){
		return getstr('pjs.registerBaseErrMsg');
	}
}

function prepAutocompleteKforField($pKforFieldVals) {
	$pos = strpos($pKforFieldVals, '{');
	if($pos === false) {
		return '(' . $pKforFieldVals . ')';
	}else {
		$lReplChars = str_replace('{', '(', $pKforFieldVals);
		return str_replace('}', ')', $lReplChars);
	}
}

function MyStripTags($pText) {
	if ($pText) {
		return '\'' . q(strip_tags($pText)) . '\'';
	}
}

/**
 * Приготвяме стойността за слагане в xml.
 * Тук трябва да конвертиране всички ентитита в символи с изключение на amp; lt; gt; quot; apos; (съответно и цифровите им стойности)
 * Правим html_entity_decode за да може ако има entity-та (напр &micro;) да станата символи
 *
 * @param unknown_type $pValue
 * @return unknown
 */
function prepareXmlValue($pValue){
	// 	$pValue = preg_replace('/([\<\>\'\"])/', '\$\$\$\$${1}####', $pValue);
	// 	$pValue = html_entity_decode($pValue, ENT_QUOTES, "UTF-8");
	// 	$pValue = htmlspecialchars($pValue, ENT_QUOTES);

	// 	$pValue = preg_replace_callback('/\$\$\$\$(.*)####/imsU',
	// 			function($pMatches){return htmlspecialchars_decode($pMatches[1], ENT_QUOTES);},
	// 		$pValue);


	$pValue = preg_replace_callback('/\&(?!amp|#0*38|lt|#0*60|gt|#0*62|quot|#0*34|apos|#0*39;)(.*);/imsU',
	ArrHTMLEntityDecode,
	$pValue);


	return $pValue;
}

function ArrHTMLEntityDecode($pMatches){
	return html_entity_decode($pMatches[0], ENT_NOQUOTES, "UTF-8");
}


function DeletePicFiles($pPicId){
	if( !$pPicId )
		return;
	$lFormats = array('gif', 'jpg', 'png');
	foreach($lFormats as $lFormat){
		$lPrefixes = array('oo', 'big', 'gb', 's', 'mx50', 'm80', 'd200x150');
		foreach( $lPrefixes as $lPrefix){
			$lFile = PATH_DL . $lPrefix . '_' . $pPicId . '.' . $lFormat;
			if( file_exists($lFile) )
				unlink($lFile);
		}
	}
}
function displayFiguresTablesActiveClass($pFile) {
	if($_SERVER['PHP_SELF'] == $pFile)
		return ' P-Article-Active';
}
function displayBottomTreeButtons( $pIsLocked = 0, $pPreviewMode = 0, $pDocumentId ){
	$lRet = '';
	$lInstanceId = (int)$_REQUEST['instance_id'];
	if( $lInstanceId ){
		$lRetLink = './display_document.php?instance_id=' . $lInstanceId;
	}else{
		$lRetLink = './display_document.php?document_id=' . $pDocumentId;
	}
	if( (int)$pPreviewMode ){
		if( !(int)$pIsLocked ){
			$lRet = '
					<div class="P-Grey-Btn-Holder P-Edit" onclick="window.location=\'' . $lRetLink . '\';return false;">
						<div class="P-Grey-Btn-Left"></div>
						<div class="P-Grey-Btn-Middle ">
							<div class="P-Btn-Icon"></div>
							Edit
						</div>
						<div class="P-Grey-Btn-Right"></div>
					</div>';
		}
	}else{
		$lRet = '
				<div class="P-Green-Btn-Holder" onclick="$(\'form#document_form\').submit();">
					<div class="P-Green-Btn-Left"></div>
					<div class="P-Green-Btn-Middle">Save</div>
					<div class="P-Green-Btn-Right"></div>
				</div>
				<div class="P-Clear"></div>
				<div class="P-VSpace-20"></div>
				<div class="P-Grey-Btn-Holder P-Preview" onclick="savePreviewDocument(' . (int)$pDocumentId	. ', ' . (int)$lInstanceId . ');return false;" />
					<div class="P-Grey-Btn-Left"></div>
					<div class="P-Grey-Btn-Middle"><div class="P-Btn-Icon"></div>Preview</div>
					<div class="P-Grey-Btn-Right"></div>
					<div class="unfloat"></div>
				</div>';
	}

	return $lRet;
}
function displayDocumentTreeDivClass($pIsActive, $pHasWarning, $pLevel, $pHasChildren, $pHasValidationError, $pInsanceId, $pDocumentId){
	$lResult = '';
	if(is_array($pHasValidationError) && in_array($pInsanceId, $pHasValidationError)) {
		$lResult .= 'P-Warning-Structure ';
	}
	if((int)$pHasWarning){
		$lResult .= 'P-Warning-Structure ';
	}
	if((int)$pLevel > 1){
		$lResult .= 'P-Sub-Article P-Article-Holder';

	}else{
		$lResult .= 'P-Article-Holder ';
	}
	if((int)$pIsActive){
		$lResult .= ' P-Article-Active ';
		$_SESSION['activemenutabids'][$pInsanceId] = $pInsanceId;
	}

	return $lResult;
}
function displayDocumentTreeArrow($pIsActive, $pHasChildren, $pInstanceId){
	$lResult = '';

	if((int)$pHasChildren){
		$lArrowClass = 'P-Right-Arrow';
		if($pIsActive || isset($_SESSION['activemenutabids'][$pInstanceId])){
			$lArrowClass = 'P-Down-Arrow';
		}
		//$lArrowClass = 'P-Down-Arrow';
		$lResult = '<div class="' . $lArrowClass . '"></div>';

	}

	return $lResult;

}
function displayDocumentTreeDelete($pDeleteClass, $pObjectId, $pInstanceId, $pIsLocked = 0){
	$lResult = '';

	if(($pObjectId == (int)SYSTEMATICS_OBJECT_ID || $pObjectId == (int)IDENTIFICATION_KEYS_OBJECT_ID) && !(int)$pIsLocked){
		$lResult = '<a href="javascript:void(0)" title="Delete treatment" class="' . $pDeleteClass . '" onclick="if(confirm(\'Are you sure you want to delete?\'))executeAction(3, ' . (int)$pInstanceId . ', GetRootInstanceId());"></a><div class="P-Article-Move"></div>';
	}

	return $lResult;
}
function displayDocumentTreeAdd($pAddClass, $pObjectId, $pInstanceId, $pNumChildrens, $pIsLocked = 0, $pLockUid = 0){

	$lResult = '';
	$lToolTip = '';

	global $user;
	if((int)$pIsLocked && (int)$user->id != (int)$pLockUid){
		return $lResult;
	}

	$lUseDefaultJs = 1;
// 	var_dump($pObjectId);
	switch($pObjectId){
		default:
			return $lResult;
		case (int)SYSTEMATICS_OBJECT_ID:
			$lAction = (int)ADD_TAXON_ACTION_ID;
			$lToolTip = getstr('pwt.tooltip.addnewtreatment');
			break;
		case (int)IDENTIFICATION_KEYS_OBJECT_ID:
			$lAction = (int)ADD_IDENTIFICATION_KEY_ID;
			$lToolTip = getstr('pwt.tooltip.addnewidentkey');
			break;
		case (int)REFERENCE_HOLDER_OBJECT_ID:
			$lUseDefaultJs = 0;
			$lToolTip = getstr('pwt.tooltip.addnewreference');
			$lOnClickJs = 'CreateNewReferencePopup(0)';
	}

	if($lUseDefaultJs){
		$lOnClickJs = 'executeAction(4, ' . (int)$pInstanceId . ', ' . $lAction . ');';
	}
	$lResult = '<a href="javascript:void(0)" title="' . $lToolTip . '" class="' . $pAddClass . '" onclick="'. $lOnClickJs . '"></a>';


	return $lResult;
}
function returnSortableMenuId($pObjectId) {
	if($pObjectId == (int)SYSTEMATICS_OBJECT_ID || $pObjectId == (int)IDENTIFICATION_KEYS_OBJECT_ID) {
		return ' id="sortable_' . $pObjectId . '" ';
	}
	return '';
}
function displayShowHideClass($pInstanceId){
	if($pIsActive || isset($_SESSION['activemenutabids'][$pInstanceId])){
		return '';
	}
	return 'P-Hidden';
}
function returnSortableMenuClass($pObjectId) {
	if($pObjectId == (int)SYSTEMATICS_OBJECT_ID || $pObjectId == (int)IDENTIFICATION_KEYS_OBJECT_ID) {
		return ' sortable ';
	}
	return '';
}
function returnSortableMenuDef($pObjectId) {
	if($pObjectId == (int)SYSTEMATICS_OBJECT_ID || $pObjectId == (int)IDENTIFICATION_KEYS_OBJECT_ID) {
		return '<script>sortableMenu(' . $pObjectId . ');</script>';
	}
	return '';
}


function ipfilter($ip, $cidrs) {

	foreach ($cidrs as $line) {
	

		// Get the base and the bits from the CIDR
		list($base, $bits) = explode('/', $line);
	   
		// Now split it up into it's classes
		list($a, $b, $c, $d) = explode('.', $base);
	   
		// Now do some bit shifting/switching to convert to ints
		$i    = ($a << 24) + ($b << 16) + ( $c << 8 ) + $d;
		$mask = $bits == 0 ? 0: (~0 << (32 - $bits));
	   
		// Here's our lowest int
		$low = $i & $mask;
	   
		// Here's our highest int
		$high = $i | (~$mask & 0xFFFFFFFF);
	   
		// Now split the ip we're checking against up into classes
		list($a, $b, $c, $d) = explode('.', $ip);
	   
		// Now convert the ip we're checking against to an int
		$check = ($a << 24) + ($b << 16) + ( $c << 8 ) + $d;
	   
		// If the ip is within the range, including highest/lowest values,
		// then it's witin the CIDR range
		if ($check >= $low && $check <= $high) {
			return 1;
		}

	}

	return 0;

}

function CustomHtmlEntitiesDoubleEncode($pXml){
	//The order here is important. If amps are last there we will have tripple encode
	$lHtmlEntities = array(
		'&amp;' => '&amp;amp;',
		'&lt;' => '&amp;lt;',
		'&gt;' => '&amp;gt;',
		'&quot;' => '&amp;quot;',
	);

	$pXml = str_replace(array_keys($lHtmlEntities), array_values($lHtmlEntities), $pXml);
	return $pXml;
}

?>