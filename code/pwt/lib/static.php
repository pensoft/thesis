<?php
$x = session_start();
//error_reporting(E_ALL);
$docroot = getenv('DOCUMENT_ROOT');
require_once($docroot . '/lib/conf.php');

if (!function_exists('CustomAutoLoad')) {
	function CustomAutoLoad($class_name) {
		if (file_exists(PATH_CLASSES_FIELDS . $class_name . ".php")) {
			include_once(PATH_CLASSES_FIELDS . $class_name . ".php");
		} elseif (file_exists(PATH_CLASSES . SITE_NAME . "/" . $class_name . ".php")) {
			include_once(PATH_CLASSES . SITE_NAME . "/" . $class_name . ".php");
		} elseif (file_exists(PATH_CLASSES . $class_name . ".php")) {
			include_once(PATH_CLASSES . $class_name . ".php");
		} elseif (file_exists(PATH_ECMSFRCLASSES . $class_name . ".php")) {
			include_once(PATH_ECMSFRCLASSES . $class_name . ".php");
		} elseif (file_exists(PATH_ECMSSHOPCLASSES . $class_name . ".php")) {
			include_once(PATH_ECMSSHOPCLASSES . $class_name . ".php");
		}
	}
	spl_autoload_register('CustomAutoLoad');
}



require_once(PATH_ECMSFRCLASSES . 'static.php');
require_once($docroot . '/lib/common_conf.php');
require_once(PATH_CLASSES . '/static.php');

$COOKIE_DOMAIN = $_SERVER['SERVER_NAME'];
$user = unserialize($_SESSION['suser']);

if($gTryToChangeUserWithoutSessionChange){
	$lUsername = $_REQUEST['username'];
	$lPassword = $_REQUEST['password'];
	$lNewUser = new clogin($lUsername, $lPassword, $_SERVER['REMOTE_ADDR']);
	if ($lNewUser->state != 1) {
		echo 'Wrong user';
		exit;
	}else{
		$user = $lNewUser;
	}
}

// $lSql = 'SELECT cd.container_id, oc.type as container_type, cd.item_type, oc.ord as container_ord, cd.css_class as container_item_css_class,
// 			oc.css_class as container_css_class,
// 			if.field_id, if.name as field_name, if.type as field_type, if.control_type as field_control_type, if.allow_nulls as field_allow_nulls, if.label as field_label,
// 			if.has_help_label as field_has_help_label, if.help_label as field_help_label, if.help_label_display_style as field_help_label_display_style,
// 			if.data_src_id as field_data_src_id, if.src_query as field_src_query,
// 			if.value_str as field_value_str, if.value_int as field_value_int, if.value_arr_int as field_value_arr_int, if.value_arr_str as field_value_arr_str,
// 			if.value_date as field_value_date, if.value_arr_date as field_value_arr_date, if.value_column_name as field_value_column_name,
// 			if.display_label as field_display_label, if.css_class as field_css_class, if.autocomplete_row_templ as field_autocomplete_row_templ, if.autocomplete_onselect as field_autocomplete_onselect,
// 			if.is_read_only as field_is_read_only, if.is_html as field_is_html, if.is_array as field_is_array,
// 			if.has_example_label as field_has_example_label, if.example_label as field_example_label,
// 			iso.id as subinstance_id, toc.object_id as child_object_id_to_add, toc.display_name as child_object_name, toc.create_in_popup::int as create_in_popup,
// 			chi.id as html_item_id, chi.content as html_item_content,
// 			ti.tabbed_item_id, ti.default_active_object_id, ti.css_class as tabbed_item_object_css_class,
// 			tem.xsl_dir_name as xsl_dir_name,
// 			dto.view_xpath_sel as view_xpath_selection,
// 			dto.view_xsl_templ_mode as view_xsl_template_mode
// 		FROM pwt.object_container_details cd
// 		JOIN pwt.object_containers oc ON oc.id = cd.container_id
// 		JOIN pwt.document_object_instances di ON di.id = $1 AND di.object_id = oc.object_id
// 		JOIN pwt.document_template_objects dto ON dto.id = di.document_template_object_id
// 		JOIN pwt.templates tem ON tem.id = dto.template_id
// 		LEFT JOIN pwt.v_instance_fields if ON if.instance_id = di.id AND if.field_id = cd.item_id AND cd.item_type = ' . (int) CONTAINER_ITEM_FIELD_TYPE . '
// 		LEFT JOIN pwt.v_tabbed_items ti ON ti.tabbed_item_id = cd.item_id AND cd.item_type = ' . (int) CONTAINER_ITEM_TABBED_ITEM_TYPE . '
// 		LEFT JOIN pwt.document_object_instances iso ON iso.parent_id = di.id
// 		AND ((cd.item_type = ' . (int) CONTAINER_ITEM_OBJECT_TYPE . ' AND iso.object_id = cd.item_id)
// 		OR (cd.item_type = ' . (int) CONTAINER_ITEM_TABBED_ITEM_TYPE . ' AND iso.object_id = ti.object_id)
// 		)
// 		LEFT JOIN pwt.object_container_html_items chi ON chi.id = cd.item_id AND cd.item_type = ' . (int) CONTAINER_ITEM_CUSTOM_HTML_TYPE . '
// 		LEFT JOIN (SELECT * FROM spGetInstanceAllowedObjectsToAdd($1)) toc ON toc.instance_id = di.id
// 		AND toc.object_id =  cd.item_id AND cd.item_type = ' . (int) CONTAINER_ITEM_OBJECT_TYPE . '
// 		WHERE (iso.id IS NOT NULL OR if.field_id IS NOT NULL OR chi.id IS NOT NULL OR toc.object_id IS NOT NULL)
// 		ORDER BY oc.ord ASC, cd.ord ASC, ti.pos ASC, ti.tabbed_item_id ASC, iso.pos ASC
// ';
// $lCon = Con();
// $lCon->PrepareStatement('FieldsSelectAccepted', $lSql);

// $lSql = '
// SELECT *
// FROM pwt.msg
// WHERE (start_object_instances_id = $1 AND coalesce(start_object_field_id, 0) > 0 AND start_offset >= 0)
// OR (end_object_instances_id = $1 AND coalesce(end_object_field_id, 0) > 0 AND end_offset >= 0)
// ';
// $lCon->PrepareStatement('FieldsComments', $lSql);

// $lSql = 'SELECT * FROM spGetInstanceBaseInfo($1, $2);';
// $lCon->PrepareStatement('InstanceBaseInfo', $lSql);

/*
Тъй като на повечето страници трябва да се редиректва към логинформата
го правим директно тук. Ако една страница не трябва да редиректва -
на първия ред от нея се дава стойност 1 на $gDontRedirectToLogin ($gDontRedirectToLogin = 1)
преди да се инклудне статика
	Ако сме влезли с ajax request - директно exit-ваме
*/

if(!$gDontRedirectToLogin && !(int)$user->id){
	$lUrl = $_SERVER['REQUEST_URI'];
	$lUrl = urlencode($lUrl);

	if(isset($_REQUEST['u_autolog_hash'])){
		$lAutologHash = $_REQUEST['u_autolog_hash'];
		$lUrl = $lUrl . '&' . 'u_autolog_hash=' . $lAutologHash;
	}

	header("Location: /login.php?back_uri=$lUrl");
	exit;
}

if(isset($_COOKIE['h_cookie']) && !(int)$user->id){
	$lCurrentUrl = $_SERVER['PHP_SELF'] . '?' . $_SERVER['QUERY_STRING'];

	$lParam['autologhash'] = q($_COOKIE['h_cookie']);

	$lParam['ip'] = $_SERVER['REMOTE_ADDR'];

	$user = new clogin($lParam);
	$_SESSION['suser'] = serialize($user);
	if ($user->state == 1) {
		header("Location: $lCurrentUrl");
		exit;
	}
}


CleanCookiesFromRequest();
// crewrite instance
$rewrite =& new crewrite();

$gXMLErrors = array (
	XML_INVALID_FIELD_TYPE_ERROR => 'Wrong data type',
	XML_MISSING_FIELD_ERROR => 'Missing field(s)',
	XML_UNALLOWED_ATTRIBUTE_ERROR => 'Attribute is not allowed',
	XML_INCORRECT_FIELD_LENGTH_ERROR => 'The value has a length of "0" that interrupts the allowed minimum length of "1"',
	XML_OTHER_UNDEFINED_ERROR => 'Other',
	XML_UNCITED_FIGURES_ERROR => 'There are uncited figures',
	XML_UNCITED_TABLES_ERROR => 'There are uncited tables',
	XML_UNCITED_REFERENCES_ERROR => 'There are uncited references',
);

function CleanCookiesFromRequest() {
	foreach ($_COOKIE as $k => $v) {
		unset($_REQUEST[$k]);
	}
}

function render_if($arg, $prefix, $suffix) {
	if($arg)
		return $prefix . htmlentities($arg, ENT_NOQUOTES | ENT_XHTML, 'UTF-8', false) . $suffix;
	else
		return '';
}

function unsafe_render_if($arg, $prefix = '', $suffix = '') {
	if($arg)
		return $prefix . $arg . $suffix;
	else
		return '';
}

function DefObjTempl() {
	global $user;
	global $rewrite;
	$t = array(
		'langswitcher'=>array('ctype'=>'clang', 'templs'=>array()),

		'topmenu'=>array(
			'ctype'=>'crsrecursive',
			'templs'=>array(
				G_HEADER=>'global.topmenuheader',
				G_ROWTEMPL=>'global.topmenurowtempl',
				G_FOOTER =>'global.topmenufooter',
			),
			'recursivecolumn'=>'parentid',
			'templadd'=>'type',
			'sqlstr'=>'select id,'.getsqlang('name').','.getsqlang('href').','.getsqlang('img').',type,parentid from getMenuContents(1,0,'.CMS_SITEID.','.getlang().')',
			'cache'=>'menu'
		),

		'metadata'=>array(
			'ctype'=>'cmetadata',
			'templs'=>array(
				G_DEFAULT=>'global.metadata',
			),
		),

		'loginform' => array(
			'ctype' => 'csimple',
			'err' => GetLoginErr(),
			'fullname' => $user->fullname,
			'templs' => array(
				G_DEFAULT => ($user->id ? 'loginform.logged' : 'loginform.unlogged'),
			),
		),
	);

	return $t;
}

function showPicIfExists($pid, $pref, $class = false) {
	if ($pid) {
		return '<img src="' . SITE_URL . SHOWIMG_URL .
		$pref . '_' . $pid . '.jpg" border="0" alt="" ' .
		($class ? 'class="' . $class . '"' : '') . '/>';
	}
	return '';
}

function showPicIfExistsAOF($pid, $pref, $class = false) {
	if ($pid) {
		return '<img width="60" height="60" src="' . SITE_URL . SHOWIMG_URL .
		$pref . '_' . $pid . '.jpg" border="0" alt="" ' .
		($class ? 'class="' . $class . '"' : '') . '/>';
	}
	return '<img width="60" height="60" src="' . SITE_URL . '/i/no-photo-60.png" border="0" alt="" ' .
		($class ? 'class="' . $class . '"' : '') . '/>';
}

function showItemIfExists ($item, $leftCont, $rightCont, $nl2br = false) {
	return ($item ? $leftCont . ($nl2br ? nl2br($item) : $item) . $rightCont : '');
}

function CutText($d, $len = 80) {
	$d = html_entity_decode(trim(strip_tags($d)), ENT_COMPAT | ENT_HTML401 , 'UTF-8');
	if (mb_strlen($d, 'UTF-8') < $len) return trim($d);
	$cut = mb_substr($d, 0, $len, 'UTF-8');
	$WidgetText = mb_substr($cut, 0, mb_strrpos($cut, ' ', 'UTF-8'), 'UTF-8');
	if($WidgetText)
		return $WidgetText . '...';
	else {
		return mb_substr($d, 0, $len, 'UTF-8') . '...';
	}
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

function displayDocumentTreeLiClass($pIsActive, $pHasWarning, $pLevel, $pHasChildren){
	$lResult = '';
	if((int)$pHasWarning){
		$lResult .= 'warning ';
	}
	if((int)$pLevel > 1){
		$lResult .= 'indent level' . ($pLevel - 1) . ' ';

	}

	if((int)$pHasChildren){
		$lResult .= 'arrowdown ';
	}

	return $lResult;
}


function initLeftRightColumns(){
	$lRet = '';
	if(isset($_SESSION['columnsstate'])){
		$lLeft  = isset($_SESSION['columnsstate'][1]) ? (int)$_SESSION['columnsstate'][1] : 1;
		$lRight = isset($_SESSION['columnsstate'][2]) ? (int)$_SESSION['columnsstate'][2] : 1;

		$lRet .= 'gLeftColHide = ' . $lLeft . ';';
		$lRet .= 'gRightColHide = ' . $lRight . ';';
		$lRet .= 'toggleLeftContainer(); toggleRightContainer();';
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
		MarkActiveTab($pInsanceId);
	}

	return $lResult;
}

function checkDocumentMenuAndColumnsState($pDocumentId){

	if(isset($_SESSION['documentid']) && (int)$_SESSION['documentid'] != (int)$pDocumentId){
// 		trigger_error('SESSION RESET static ' . $_SESSION['documentid'] . '  ' . (int)$pDocumentId, E_USER_NOTICE);
		unset($_SESSION['activemenutabids']);
		unset($_SESSION['columnsstate']);
	}
	$_SESSION['documentid'] = (int)$pDocumentId;
}

function getContainerHideClass($pLeftOrRightColumn){ // 1 - lqva, 2 - dqsna
	$lRet = '';
	if(isset($_SESSION['columnsstate'])){
		$lColumn = isset($_SESSION['columnsstate'][$pLeftOrRightColumn]) ? (int)$_SESSION['columnsstate'][$pLeftOrRightColumn] : 1;
		if( !(int)$lColumn ){
			if($pLeftOrRightColumn == 1){ // Nachi lqva
				$lRet = ' P-Wrapper-Container-Left-Hide ';
			}else{ // Inache dqsna
				$lRet = ' P-Wrapper-Container-Right-Hide ';
			}
		}
	}
	return $lRet;
}

function displayDocumentTreeLinkClass($pIsActive){
	if($pIsActive)
		return ' activated ';
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

function displayShowHideClass($pInstanceId){
	if($pIsActive || isset($_SESSION['activemenutabids'][$pInstanceId])){
		return '';
	}
	return 'P-Hidden';
}

function displayDocumentTreeDelete($pDeleteClass, $pObjectId, $pInstanceId, $pIsLocked = 0, $pLockUid = 0, $pValidatePage = 0, $pDocumentState = 1){
	global $user;
	$lResult = '';

	if(((int)$pIsLocked && (int)$user->id != (int)$pLockUid) || (int)$pValidatePage || (int)$pDocumentState == 2){
		return $lResult;
	}
	$lDeleteLinkTitle = '';
	switch ($pObjectId) {
		case SYSTEMATICS_OBJECT_ID:
			$lDeleteLinkTitle = getstr('pwt.delete_systematics_obj');
			break;
		case CHECKLIST_LOCALITY_OBJECT_ID:
			$lDeleteLinkTitle = getstr('pwt.delete_locality_obj');
			break;
		case ADD_CHECKLIST_LOCALITY_OBJECT_ID:
			$lDeleteLinkTitle = getstr('pwt.delete_locality_object');
			break;
		case IDENTIFICATION_KEYS_OBJECT_ID:
			$lDeleteLinkTitle = getstr('pwt.delete_id_keys');
			break;
		case CHECKLISTS_OBJECT_ID: // Taxonomic paper 2.0
			$lDeleteLinkTitle = getstr('pwt.delete_checklist');
			break;
		case INVENTORY_CHECKLIST_ID: //Species inventory 2.0
			$lDeleteLinkTitle = getstr('pwt.delete_locality_obj');
			break;
		case CHECKLIST2_OBJECT_ID: // Taxonomic paper 2.0
		case INVENTORY_LOCALITY_ID: //Species inventory 2.0
			$lDeleteLinkTitle = getstr('pwt.delete_checklist_obj');
			break;

	}

	if(($pObjectId == (int)SYSTEMATICS_OBJECT_ID ||
		$pObjectId == (int)CHECKLIST_LOCALITY_OBJECT_ID ||
		$pObjectId == (int)ADD_CHECKLIST_LOCALITY_OBJECT_ID ||
		$pObjectId == (int)IDENTIFICATION_KEYS_OBJECT_ID ||

		$pObjectId == (int)CHECKLISTS_OBJECT_ID ||
		$pObjectId == (int)CHECKLIST2_OBJECT_ID ||
		$pObjectId == (int)CHECKLIST_OBJECT_ID ||
		$pObjectId == (int)INVENTORY_LOCALITY_ID ||
		$pObjectId == (int)INVENTORY_CHECKLIST_ID) &&
		(!(int)$pIsLocked || ((int)$pIsLocked && (int)$user->id == (int)$pLockUid ))
	){
		$lResult = '<a href="javascript:void(0)" title="' . $lDeleteLinkTitle . '" class="' . $pDeleteClass . '" onclick="if(confirm(\'Are you sure you want to delete?\'))executeAction(3, ' . (int)$pInstanceId . ', GetRootInstanceId());"></a><div class="P-Article-Move"></div>';
	}

	return $lResult;
}

function displayDocumentTreeAdd($pAddClass, $pObjectId, $pInstanceId, $pNumChildrens, $pIsLocked = 0, $pLockUid = 0, $pValidatePage = 0, $pDocumentState = 1){

	$lResult = '';
	$lToolTip = '';

	global $user;
	if(((int)$pIsLocked && (int)$user->id != (int)$pLockUid) || (int)$pValidatePage || (int)$pDocumentState == 2){
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
			$lUseDefaultJs = 0;
			$lOnClickJs = 'CreatePopup(' . $pInstanceId . ', ' . (int)TAXON_TREATMENT_OBJECT_ID . ')';
			break;
		case (int)IDENTIFICATION_KEYS_OBJECT_ID:
			$lAction = (int)ADD_IDENTIFICATION_KEY_ID;
			$lToolTip = getstr('pwt.tooltip.addnewidentkey');
			break;
		case (int)REFERENCE_HOLDER_OBJECT_ID:
			$lUseDefaultJs = 0;
			$lOnClickJs = 'CreateNewReferencePopup(0)';
			$lToolTip = getstr('pwt.tooltip.addnewreference');
			break;
		case (int)FIGURE_HOLDER_OBJECT_ID:
			$lUseDefaultJs = 0;
			$lOnClickJs = 'CreateNewFigurePopup(0, 1)';
			$lToolTip = getstr('pwt.tooltip.addnewfigure');
			break;
		case (int)TABLE_HOLDER_OBJECT_ID:
			$lUseDefaultJs = 0;
			$lOnClickJs = 'CreateNewTablePopup(0)';
			$lToolTip = getstr('pwt.tooltip.addnewtable');
			break;
		case (int)CHECKLIST_OBJECT_ID:
			$lAction = (int)ADD_CHECKLIST_TAXON_OBJECT_ID;
			$lToolTip = getstr('pwt.tooltip.addnewchecklisttaxon');
			break;
		case (int)CHECKLIST_LOCALITY_OBJECT_ID:
			$lAction = (int)ADD_CHECKLIST_LOCALITY_OBJECT_ID;
			$lToolTip = getstr('pwt.tooltip.addnewchecklistlocality');
			break;
		case (int)SUPPLEMENTARY_FILES_OBJECT_ID:
			$lUseDefaultJs = 0;
			$lOnClickJs = 'CreatePopup(' . $pInstanceId . ', ' . (int)SUPPLEMENTARY_FILE_OBJECT_ID . ')';
			$lToolTip = getstr('pwt.tooltip.addsupplementaryfile');
			break;
		case CHECKLISTS_OBJECT_ID: //Taxonomic paper 2.0
			$lAction = CHECKLIST2_OBJECT_ID;
			$lToolTip = getstr('pwt.tooltip.addchecklist');
			break;
		case CHECKLIST2_OBJECT_ID: //Taxonomic paper 2.0
		case INVENTORY_LOCALITY_ID: //Species inventory 2.0
			$lAction = TAXON2_OBJECT_ID;
			$lToolTip = getstr('pwt.tooltip.addnewchecklisttaxon');
			break;
		case INVENTORY_CHECKLIST_ID: //Taxonomic paper 2.0
			$lAction = INVENTORY_LOCALITY_ID;
			$lToolTip = getstr('pwt.tooltip.addnewchecklistlocality');
			break;
	}

	if($lUseDefaultJs){
		$lOnClickJs = 'executeAction(4, ' . (int)$pInstanceId . ', ' . $lAction . ');';
	}
	$lResult = '<a href="javascript:void(0)" title="' . $lToolTip . '" class="' . $pAddClass . '" onclick="'. $lOnClickJs . '"></a>';


	return $lResult;
}

function getInstanceWrapperClass($pInstanceLevel, $pDisplayNestingIndicator){

	if((int)$pDisplayNestingIndicator){
			return 'P-Data-Resources-Subsection P-Level1';
	}
	return 'P-Data-Resources';
}

function getContainerClass($pContainerType){
	if((int)$pContainerType == CONTAINER_HORIZONTAL_TYPE){
		return ' horizontalContainer ';
	}
	return ' verticalContainer ';
}

/**
 * Изчисляваме широчината на item-a, ако не му е указан css клас
 * @param unknown_type $pItemCssClass
 * @param unknown_type $pContainerType
 * @param unknown_type $pItemsCount
 */
function getContainerItemStyle($pItemCssClass, $pContainerType, $pItemsCount){
	if(trim($pItemCssClass) != '')
		return ;
	if((int)$pContainerType == CONTAINER_HORIZONTAL_TYPE){
		return ';float:left;width: ' . (int)(100/$pItemsCount) . '%;';
	}
}

/**
 * Слагаме специфичен клас на последния елемент.
 * @param unknown_type $pItemsCount
 * @param unknown_type $pRownum
 */
function getContainerItemWrapperClass($pItemsCount, $pRownum){
	if((int)$pItemsCount == (int)$pRownum)
		return ' lastItem ';
}

function getFieldErrorClass($pHasValidationError){
	if ($pHasValidationError) {
		return ' fieldWithError ';
	}
}

function displayFieldHelpLabel($pHasHelpLabel, $pHelpLabel, $pDisplayStyle = FIELD_HELP_LABEL_ICON_STYLE){
	if((int)$pHasHelpLabel){
// 		var_duMP($pDisplayStyle == FIELD_HELP_LABEL_DESCRIPTION_STYLE);
		if($pDisplayStyle == FIELD_HELP_LABEL_ICON_STYLE){
			return '<div class="P-Input-Help">
						<div class="P-Baloon-Holder">
							<div class="P-Baloon-Arrow"></div>
							<div class="P-Baloon-Top"></div>
							<div class="P-Baloon-Middle">
								<div class="P-Baloon-Content">
									' . $pHelpLabel . '
								</div>
								<div class="P-Clear"></div>
							</div>
							<div class="P-Baloon-Bottom"></div>
						</div>
					</div>';
		}elseif($pDisplayStyle == FIELD_HELP_LABEL_DESCRIPTION_STYLE){
			return '<div class="P-Input-Desc-Holder">
						<div class="P-Input-Desc">
							' . $pHelpLabel . '
						</div>
					</div>
					<div class="unfloat"></div>
			';
		}
	}
}

function displayFieldExampleLabel($pHasExampleLabel, $pExampleLabel){
	if((int)$pHasExampleLabel){
		return '<div class="P-Input-Example-Holder">
					<div class="P-Input-Example-Antet">' . getstr('pwt.exampleLabel') . ':&nbsp;</div>
					<div class="P-Input-Example-Text">' . $pExampleLabel . '</div>
					<div class="P-Clear"></div>
				</div>
		';
	}
}

function displayFieldRequiredSign($pAllowNulls){
	if(!(int)$pAllowNulls){
		return '<span class="txtred">*</span>';
	}
}

function displayInstanceMoveUpLink($pAllowMoveUp, $pDocumentId, $pInstanceId, $pRootInstanceId){
	$lDisplay = 'none';
	if((int)$pAllowMoveUp && (int)$pInstanceId && (int)$pDocumentId){
		$lDisplay = '';
	}
	return '<div id="move_up_link_instance_' . $pInstanceId . '" style="display:' . $lDisplay . ';" href="javascript:void(0)" onclick="executeAction(' . 1 . ', ' . $pInstanceId . ')" class="section_arrow_up"></div>';
}
function displayInstanceMoveDownLink($pAllowMoveDown, $pDocumentId, $pInstanceId, $pRootInstanceId){
	$lDisplay = 'none';
	if((int)$pAllowMoveDown && (int)$pInstanceId && (int)$pDocumentId){
		$lDisplay = '';
	}
	return '<div id="move_down_link_instance_' . $pInstanceId . '" style="display:' . $lDisplay . ';" href="javascript:void(0)" onclick="executeAction(' . 2 . ', ' . $pInstanceId . ')" class="section_arrow_down"></div>';
}

function getActionDefaultJsAction($pActionId, $pInstanceId){
//	return 'executeAction(' . (int)$pActionId . ', ' . (int)$pInstanceId . ');';
	return getActionDefaultJsActionWithParams($pActionId, $pInstanceId);
}

function getActionDefaultJsActionWithParams(){
	$lParams = func_get_args();
	$lArgumentsCount = count($lParams);
	if($lArgumentsCount < 2)
		return;

	$pActionId = func_get_arg(0);
	$pInstanceId = func_get_arg(1);
	$lResult = 'executeAction(' . (int)$pActionId . ', ' . (int)$pInstanceId;
	for($i = 2; $i < $lArgumentsCount; ++$i){
		$lResult .= ', ' . json_encode(func_get_arg($i));
	}
	$lResult .= ');';
	return $lResult;
}

function getDocumentDefaultTempls($pHideComments = false){
	$lTemplate = 'document.wrapper';
	if($pHideComments){
		$lTemplate = 'document.wrapper_no_comments';
	}
	return array(
		G_DEFAULT => $lTemplate
	);
}

function getDocumentFiguresTempls(){
	return array(
		G_DEFAULT => 'document.figures_wrapper'
	);
}

function getDocumentTablesTempls(){
	return array(
		G_DEFAULT => 'document.tables_wrapper'
	);
}

function getDocumentXMLValidationTempls(){
	return array(
		G_DEFAULT => 'document.xml_validation_wrapper'
	);
}

function getDocumentXMLValidationTemplsNoComments(){
	return array(
		G_DEFAULT => 'document.xml_validation_wrapper_no_comments'
	);
}

function getDocumentTreeDefaultTempls(){
	return array(
		G_HEADER => 'document.tree_head',
		G_FOOTER => 'document.tree_foot',
		G_STARTRS => 'document.tree_start',
		G_ENDRS => 'document.tree_end',
		G_ROWTEMPL => 'document.tree_row',
		G_NODATA => 'document.tree_nodata',
	);
}

function getDocumentPathDefaultTempls(){
	return array(
		G_HEADER => 'document.path_head',
		G_FOOTER => 'document.path_foot',
		G_STARTRS => 'document.path_start',
		G_ENDRS => 'document.path_end',
		G_ROWTEMPL => 'document.path_row',
		G_NODATA => 'document.path_nodata',
	);
}

function getDocumentFieldDefaultTempls($pInPopup = false){
	return array(
		G_INPUT_TEMPL => 'fields.input',
		G_INPUT_VIDEO_YOUTUBE_LINK_TEMPL => 'fields.input_youtube_link',
		G_SELECT_TEMPL => 'fields.select',
		G_RADIO_TEMPL => 'fields.radio',
		G_RADIO_ROW_TEMPL => 'fields.radio_row',
		G_RADIO_PLATE_APPEARANCE_TEMPL => 'fields.radio_plate_appearance',
		G_RADIO_PLATE_APPEARANCE_ROW_TEMPL => 'fields.radio_plate_appearance_row',
		G_CHECKBOX_TEMPL => 'fields.checkbox',
		G_CHECKBOX_ROW_TEMPL => 'fields.checkbox_row',
		G_EDITOR_TEMPL => 'fields.editor',
		G_EDITOR_REFERENCE_CITATIONS_TEMPL => 'fields.editor_reference_citations',
		G_EDITOR_NO_CITATION_TEMPL => 'fields.editor_no_citation',
		G_TEXTAREA_TEMPL => 'fields.textarea',
		G_TEXTAREA_TABLE_TEMPL => 'fields.textarea_table_editor',
		G_TEXTAREA_SIMPLE_TEMPL => 'fields.textarea_simple',
		G_TEXTAREA_SIMPLE_ROUNDED_TEMPL => 'fields.textarea_rounded_simple',
		G_TEXTAREA_PLATE_DESCRIPTION_TEMPL => 'fields.textarea_plate_description',
		G_AUTOCOMPLETE_TEMPL => 'fields.input',
		G_AUTOCOMPLETE_TEMPL => 'fields.input',
		G_FACEBOOK_AUTOCOMPLETE_TEMPL => 'fields.fbautocomplete',
		G_TAXON_CLASSIFICATION_AUTOCOMPLETE_TEMPL => 'fields.taxon_classification_autocomplete',
		G_FILE_UPLOAD_TEMPL => 'fields.file_upload',
		G_FILE_UPLOAD_MATERIAL_TEMPL => 'fields.file_upload_material',
		G_FILE_UPLOAD_CHECKLIST_TAXON_TEMPL => 'fields.file_upload_checklist_taxon',
		G_FILE_UPLOAD_COVERAGE_TAXA_TEMPL => 'fields.file_upload_taxon_coverage_taxa',
		G_FILE_UPLOAD_FIGURE_IMAGE_TEMPL => 'fields.file_upload_figure_image',
		G_FILE_UPLOAD_FIGURE_PLATE_IMAGE_TEMPL => 'fields.file_upload_figure_plate_image',

		G_INPUT_LABEL_TEMPL => 'fields.label',
		G_INPUT_VIDEO_YOUTUBE_LINK_LABEL_TEMPL => 'fields.label',
		G_TEXTAREA_LABEL_TEMPL => 'fields.label_editor',
		G_TEXTAREA_TABLE_LABEL_TEMPL => 'fields.label_editor',
		G_TEXTAREA_SIMPLE_LABEL_TEMPL => 'fields.label_editor',
		G_TEXTAREA_SIMPLE_ROUNDED_LABEL_TEMPL => 'fields.texarea_simple_rounded_label',
		G_TEXTAREA_PLATE_DESCRIPTION_LABEL_TEMPL => 'fields.label_editor',
		G_SELECT_LABEL_TEMPL => 'fields.label',
		G_RADIO_LABEL_TEMPL => 'fields.label_radio',
		G_RADIO_PLATE_APPEARANCE_LABEL_TEMPL => 'fields.label_radio_plate_appearance',
		G_CHECKBOX_LABEL_TEMPL => 'fields.label_checkbox',
		G_EDITOR_LABEL_TEMPL => 'fields.label_editor',
		G_AUTOCOMPLETE_LABEL_TEMPL => 'fields.label',
		G_FACEBOOK_AUTOCOMPLETE_LABEL_TEMPL => 'fields.label',
		G_TAXON_CLASSIFICATION_AUTOCOMPLETE_LABEL_TEMPL => 'fields.label',
		G_FILE_UPLOAD_LABEL_TEMPL => 'fields.label_file_upload',

		G_HEADER => 'fields.head',
		G_FOOTER => ($pInPopup ? 'fields.foot_popup' : 'fields.foot' ),
	);
}

function getDocumentContainerDefaultTempls(){
	return array(
		G_HEADER => 'document.container_head',
		G_FOOTER => 'document.container_foot',
		G_STARTRS => 'document.container_start',
		G_ROWTEMPL => 'document.container_row',
		G_ENDRS => 'document.container_end',
		G_NODATA => 'document.container_nodata',
	);
}

function getDocumentInstanceDefaultTempls(){
	return array(
		G_HEADER => 'document.instance_head',
		G_FOOTER => 'document.instance_foot',
		G_STARTRS => 'document.instance_start',
		G_LABEL => 'document.instance_label',
		G_ENDRS => 'document.instance_end',
		G_NODATA => 'document.instance_nodata',
	);
}

function getDocumentCustomHtmlItemsDefaultTempls(){
	return array(
		G_DEFAULT => 'document.custom_html_fields',
	);
}

function getDocumentActionsDefaultTempls(){
	return array(
		G_DEFAULT => 'document.actions_row',

		G_MOVE_UP_ROW => 'actions.moveUpRow',
		G_MOVE_DOWN_ROW => 'actions.moveDownRow',
		G_TOP_RED_ROW => 'actions.topRedRow',
		G_BOTTOM_RED_ROW => 'actions.bottomRedRow',
		G_ADD_ROW => 'actions.addRow',
		G_BOTTOM_EDIT_ROW => 'actions.bottomEditRow',
		G_ADD_ALL_ROW => 'actions.addAllRow',
		G_COMMENT_ROW => 'actions.commentRow',
		G_VALIDATION_ROW => 'actions.validationRow',
		G_CHECK_NAME_AVAILABILITY_ROW => 'actions.checkNameAvailabilityRow',
		G_BOTTOM_SAVE_ROW => 'actions.bottomSaveRow',
		G_BOTTOM_CANCEL_ROW => 'actions.bottomCancelRow',
		G_TOP_CHANGE_MODE_ROW => 'actions.topChangeModeRow',
		G_RIGHT_MOVE_UP_ROW => 'actions.rightMoveUpRow',
		G_RIGHT_MOVE_DOWN_ROW => 'actions.rightMoveDownRow',
		G_RIGHT_DELETE_ROW => 'actions.rightDeleteRow',
	);
}

function getDocumentTabbedElementDefaultTempls(){
	return array(
		G_HEADER => 'document.tabbedElement_head',
		G_FOOTER => 'document.tabbedElement_foot',
		G_TAB_ROWTEMPL => 'document.tabbedElement_tabRow',
		G_STARTRS => 'document.tabbedElement_start',
		G_ROWTEMPL => 'document.tabbedElement_row',
		G_ENDRS => 'document.tabbedElement_end',
		G_NODATA => 'document.tabbedElement_nodata',
	);
}

function displayAjaxResponse($pAjaxResponse){
	echo json_encode($pAjaxResponse);
	exit;
}

/**
 *
 * Връща масив с item-ите на контейнера с подаденото id,
 * за подадения instance $pInstanceId
 * @param unknown_type $pContainerId
 * @param unknown_type $pInstanceId
 */
function getContainerItems($pContainerId, $pInstanceId, $pDisplayUnconfirmedInstances = false){
	$lSubinstancesWhere = '';
	if(!$pDisplayUnconfirmedInstances){
		$lSubinstancesWhere .= ' AND iso.is_confirmed = true ';
	}
	$lSql = 'SELECT cd.container_id, oc.type as container_type, cd.item_type, oc.ord as container_ord, cd.css_class as container_item_css_class,
				if.field_id, if.name as field_name, if.type as field_type, if.control_type as field_control_type, if.allow_nulls as field_allow_nulls, if.label as field_label,
				if.has_help_label as field_has_help_label, if.help_label as field_help_label, if.data_src_id as field_data_src_id, if.src_query as field_src_query,
				if.value_str as field_value_str, if.value_int as field_value_int, if.value_arr_int as field_value_arr_int, if.value_arr_str as field_value_arr_str,
				if.value_date as field_value_date, if.value_arr_date as field_value_arr_date, if.value_column_name as field_value_column_name,
				if.display_label as field_display_label, if.css_class as field_css_class, if.autocomplete_row_templ as field_autocomplete_row_templ,
				iso.id as subinstance_id, toc.object_id as child_object_id_to_add, toc.display_name as child_object_name, toc.create_in_popup::int as create_in_popup,
				chi.id as html_item_id, chi.content as html_item_content
			FROM object_container_details cd
			JOIN object_containers oc ON oc.id = cd.container_id AND oc.id = ' . $pContainerId . '
			JOIN document_object_instances di ON di.id = ' . $pInstanceId . ' AND di.object_id = oc.object_id
			JOIN document_template_objects dto ON dto.id = di.document_template_object_id
			LEFT JOIN v_instance_fields if ON if.instance_id = di.id AND if.field_id = cd.item_id AND cd.item_type = ' . ( int ) CONTAINER_ITEM_FIELD_TYPE . '
			LEFT JOIN document_object_instances iso ON iso.document_id = di.document_id AND char_length(iso.pos) = char_length(di.pos) + 2
				AND substring(iso.pos, 1, char_length(di.pos)) = di.pos
				AND cd.item_type = ' . ( int ) CONTAINER_ITEM_OBJECT_TYPE . ' AND iso.object_id = cd.item_id  ' . $lSubinstancesWhere . '
			LEFT JOIN object_container_html_items chi ON chi.id = cd.item_id AND cd.item_type = ' . ( int ) CONTAINER_ITEM_CUSTOM_HTML_TYPE . '
			LEFT JOIN (SELECT * FROM spGetInstanceAllowedObjectsToAdd('. (int) $pInstanceId . ')) toc ON toc.instance_id = di.id
				AND toc.object_id =  cd.item_id AND cd.item_type = ' . (int) CONTAINER_ITEM_OBJECT_TYPE . '
			WHERE (iso.id IS NOT NULL OR if.field_id IS NOT NULL OR chi.id IS NOT NULL OR toc.object_id IS NOT NULL)
			ORDER BY oc.ord ASC, cd.ord ASC, iso.pos ASC
		';


	$lCon = Con();
	$lCon->Execute($lSql);
// 	var_dump($lCon->GetLastError());

	$lItems = array();
	$lObjectsToAdd = array();
	while(!$lCon->Eof()){
		if((int)$lCon->mRs['item_type'] == (int) CONTAINER_ITEM_FIELD_TYPE){
			$lItems[] = array(
				'item_type' => $lCon->mRs['item_type'],
				'field_id' => (int)$lCon->mRs['field_id'],
				'name' => $lCon->mRs['field_name'],
				'label' => $lCon->mRs['field_label'],
				'type' => (int)$lCon->mRs['field_type'],
				'html_control_type' => (int)$lCon->mRs['field_control_type'],
				'allow_nulls' => (int)$lCon->mRs['field_allow_nulls'],
				'has_help_label' => (int)$lCon->mRs['field_has_help_label'],
				'help_label' => $lCon->mRs['field_help_label'],
				'data_src_id' => (int)$lCon->mRs['field_data_src_id'],
				'src_query' => $lCon->mRs['field_src_query'],
				'sql_value' => $lCon->mRs['field_' . $lCon->mRs['field_value_column_name']],
				'display_label' => $lCon->mRs['field_display_label'],
				'css_class' => $lCon->mRs['field_css_class'],
				'container_item_css_class' => $lCon->mRs['container_item_css_class'],
				'autocomplete_row_template' => $lCon->mRs['field_autocomplete_row_templ'],
			);
		}elseif((int)$lCon->mRs['item_type'] == (int) CONTAINER_ITEM_OBJECT_TYPE){
			if(( int ) $lCon->mRs['subinstance_id']){//Имаме да покажем инстанс
				$lItems[] = array(
					'item_type' => ( int ) $lCon->mRs['item_type'],
					'instance_id' => $lCon->mRs['subinstance_id'],
					'container_item_css_class' => $lCon->mRs['container_item_css_class']
				);
			}
			if(( int ) $lCon->mRs['child_object_id_to_add']){//Имаме тип обект, който да добавим
				$lObjectsToAdd[$lCon->mRs['child_object_id_to_add']] = array(
					'create_in_popup' => (int)$lCon->mRs['create_in_popup'],
					'name' => $lCon->mRs['child_object_name'],
				);
			}
		}elseif((int)$lCon->mRs['item_type'] == (int) CONTAINER_ITEM_CUSTOM_HTML_TYPE){
			$lItems[] = array(
				'item_type' => $lCon->mRs['item_type'],
				'id' => $lCon->mRs['html_item_id'],
				'content' => $lCon->mRs['html_item_content'],
				'container_item_css_class' => $lCon->mRs['container_item_css_class'],
			);
		}
		$lCon->MoveNext();
	}
// 	var_dump($lObjectsToAdd);
	return array('items' => $lItems, 'objects_to_add' => $lObjectsToAdd);
}

function displayInstanceChangeModeLinks($pDocumentId, $pInstanceId, $pRootInstanceId, $pLevel, $pMode, $pAllowedModes, $pDisplayDefaultActions){
	if(!$pDisplayDefaultActions)
		return;
	$lModes = array(
		INSTANCE_EDIT_MODE => getstr('pwt.instance.changeToEditMode'),
		INSTANCE_VIEW_MODE => getstr('pwt.instance.changeToViewMode'),
		INSTANCE_TITLE_MODE => getstr('pwt.instance.changeToTitleMode'),
	);
	if(!is_array($pAllowedModes))
		$pAllowedModes = array();

	foreach ($lModes as $lModeId => $lLabel){
		if($pMode == $lModeId || !in_array($lModeId, $pAllowedModes))
			continue;
		$lResult .= '
				<div class="P-Grey-Btn-Holder2 P-Edit Action-Top-Btn-Holder" onclick="ChangeInstanceMode(' . $pDocumentId . ',' . $pInstanceId . ',' .  $pRootInstanceId . ',' .  $pLevel . ',' .  (int)$lModeId . ')">
					<div class="P-Grey-Btn-Left"></div>
					<div class="P-Grey-Btn-Middle"><div class="P-Btn-Icon"></div>' . $lLabel . '</div>
					<div class="P-Grey-Btn-Right"></div>
				</div>
			';
	}
	return $lResult;
}

function getInstanceWrapperCssClass($pTopActionsCnt){
	if(!$pTopActionsCnt)
		return ' P-Data-Resources-Head-Without-Actions ';
}

function displayInstanceName($pLabel, $pDisplayLabel, $pInstanceId){
	if(!(int)$pDisplayLabel)
		return;
	return '<div class="P-Data-Resources-Head-Text floatl" style="">' . $pLabel . '</div>';
}



function getFieldSelectOptions($pQuery, $pDocumentId = 0, $pInstanceId = 0){
	if(!$pQuery)
		return array();
	$pQuery = str_replace('{document_id}', $pDocumentId, $pQuery);
	$pQuery = str_replace('{instance_id}', $pInstanceId, $pQuery);
	$gCn = new DBCn();
	$gCn->Open();


	$gCn->Execute($pQuery);
	$gCn->MoveFirst();
	$lSrcValues = array();
	while(!$gCn->Eof()) {
		if($gCn->mRs['optgroup']) {
			$lSrcValues[$gCn->mRs['optgroup']][$gCn->mRs['id']] = $gCn->mRs['name'];
		} else {
			$lSrcValues[$gCn->mRs['id']] = $gCn->mRs['name'];
		}
		$gCn->MoveNext();
	}

	return $lSrcValues;
}

function getFieldSelectOptionsById($pQuery, $pSelectedValsArray, $pDocumentId = 0, $pInstanceId = 0, $pUseExistingDbConnection = 0){
	//~ var_dump($pUseExistingDbConnection);
	if(!(int)$pUseExistingDbConnection){
		$gCn = new DBCn();
		$gCn->Open();
	}else{
		$gCn = Con();
	}
	$lSrcValues = array();
	$lSelectedValues = array();

	$pQuery = str_replace('{document_id}', $pDocumentId, $pQuery);
	$pQuery = str_replace('{instance_id}', $pInstanceId, $pQuery);
	$pQuery = str_replace('{value}', '', $pQuery);

	if((!is_array($pSelectedValsArray) || !count($pSelectedValsArray)) && !(int)$pSelectedValsArray){
		return $lSelectedValues;
	}

	if(is_array($pSelectedValsArray) && count($pSelectedValsArray)){
		$pSelectedValsArray = array_map('intval', $pSelectedValsArray);
		$pQuery = 'SELECT * FROM (' . $pQuery  . ') a WHERE id IN ( ' . implode(',', $pSelectedValsArray) . ')';
	}else{
		$pQuery = 'SELECT * FROM (' . $pQuery  . ') a WHERE id = ' . (int)$pSelectedValsArray;
	}

// 	var_dump($pQuery);

	$gCn->Execute($pQuery);
	$gCn->MoveFirst();


	if(!is_array($pSelectedValsArray) && (int)$pSelectedValsArray){
		$pSelectedValsArray = array((int)$pSelectedValsArray);
	}

	while(!$gCn->Eof()) {
		$lSrcValues[$gCn->mRs['id']] = $gCn->mRs['name'];
		$gCn->MoveNext();
	}
	if(is_array($pSelectedValsArray)){
		foreach( $pSelectedValsArray as $lCurrentId){
			$lSelectedValues[$lCurrentId] = $lSrcValues[$lCurrentId];
		}
	}else{
		if((int)$pSelectedValsArray)
			$lSelectedValues[$pSelectedValsArray] = $lSrcValues[$pSelectedValsArray];
	}
	//~ var_dump($lSrcValues);
	return $lSelectedValues;
}

function getFieldSrcQuery($pInstanceId, $pFieldId, $pUseExistingDbConnection = 0){

	if(!(int)$pUseExistingDbConnection){
		$gCn = new DBCn();
		$gCn->Open();
	}else{
		$gCn = Con();
	}
	$lSql = 'SELECT src.query as query
		FROM pwt.document_object_instances i
		JOIN pwt.object_fields of ON of.object_id = i.object_id
		JOIN pwt.data_src src ON src.id = of.data_src_id
		WHERE i.id = ' . $pInstanceId . ' AND of.field_id = ' . $pFieldId . '
	';
	$gCn->Execute($lSql);
	$gCn->MoveFirst();
	$lQuery = $gCn->mRs['query'];
	$lQuery = str_replace('{field_id}', $pFieldId, $lQuery);
	$lQuery = str_replace('{instance_id}', $pInstanceId, $lQuery);
	return $lQuery;
}

/**
 Прилага xsl трансформация в/у xml-a
 pXSL - име на файла за xsl-a
 pXML - стринг, който съдържа xml-a
 */
function transformXmlWithXsl($pXML, $pXSL, $pParameters = array(), $pFileLoad = true){
	$lXML = new DOMDocument("1.0");
	$lXSL = new DOMDocument("1.0");
// 	$lStart = mktime(). substr((string)microtime(), 1, 6);
	if($pFileLoad) {
		if (!$lXSL->load($pXSL)) {
			throw new Exception(getstr('admin.articles.xslNotValid'));
			return;
		}
	} else {
		if (!$lXSL->loadXML($pXSL)) {
			throw new Exception(getstr('admin.articles.xslNotValid'));
			return;
		}
	}
	$pParameters[] = array(
		'namespace' => null,
		'name' => 'pSiteUrl',
		'value' => SITE_URL,
	);
// 	var_dump($pXML, $pXSL);

	$lXML->resolveExternals = true;
	if (!$lXML->loadXML($pXML)) {
		throw new Exception(getstr('admin.articles.xmlNotValid'));
		return;
	}
// 	$lEnd = mktime(). substr((string)microtime(), 1, 6);
// 	trigger_error('After xml and xsl load ' .  ($lEnd - $lStart), E_USER_NOTICE);
	// Configure the transformer
	$lXslProcessor = new XSLTProcessor;
	$lXslProcessor->registerPHPFunctions();
	$lXslProcessor->importStyleSheet($lXSL);

	if(is_array($pParameters) && count($pParameters)){
		foreach ($pParameters as $lCurrentParameter){
			$lXslProcessor->setParameter($lCurrentParameter['namespace'], $lCurrentParameter['name'], $lCurrentParameter['value']);
		}
	}
// 	$lEnd = mktime(). substr((string)microtime(), 1, 6);
// 	trigger_error('Before xsl proc ' .  ($lEnd - $lStart), E_USER_NOTICE);
	$lXSLResult =  $lXslProcessor->transformToXML($lXML);
// 	$lEnd = mktime(). substr((string)microtime(), 1, 6);
// 	trigger_error('After xsl proc ' .  ($lEnd - $lStart), E_USER_NOTICE);
	return $lXSLResult;
}

/**
 * Връщаме стойността на полето, която ще слагаме в xml-a.
 *
 * @param unknown_type $pParsedValue
 * @param unknown_type $pFieldControlType
 * @param unknown_type $pDataSrcId
 * @param unknown_type $pDataSrcQuery
 * @param unknown_type $pDocumentId
 * @param unknown_type $pInstanceId
 * @return unknown|multitype:Ambigous <NULL>
 */
function getFieldValueForSerialization($pParsedValue, $pFieldControlType, $pDataSrcId, $pDataSrcQuery, $pDocumentId, $pInstanceId, $pUseExistingDbConnection = 0){
	$lResultFound = false;


	switch($pFieldControlType){
		case (int) FIELD_HTML_TAXON_CLASSIFICATION_AUTOCOMPLETE_TYPE :
		case (int) FIELD_HTML_TAXON_CLASSIFICATION_AUTOCOMPLETE_SINGLE_TYPE:
		case FIELD_HTML_TAXON_TREATMENT_CLASSIFICATION:
			$lTableName = TAXON_NOMENCLATURE_TABLE_NAME;
			$lResult = getTaxonTreeSelectedValues($lTableName, $pParsedValue, $pUseExistingDbConnection);
			$lResultFound = true;
			return $lResult;
		case (int) FIELD_HTML_SUBJECT_CLASSIFICATION_AUTOCOMPLETE_TYPE :
		case (int) FIELD_HTML_SUBJECT_CLASSIFICATION_AUTOCOMPLETE_SINGLE_TYPE :
			$lTableName = 'subject_categories';
			$lResult = getTaxonTreeSelectedValues($lTableName, $pParsedValue, $pUseExistingDbConnection);
			return $lResult;
		case (int) FIELD_HTML_CHRONOLOGICAL_CLASSIFICATION_AUTOCOMPLETE_TYPE :
		case (int) FIELD_HTML_CHRONOLOGICAL_CLASSIFICATION_AUTOCOMPLETE_SINGLE_TYPE :
			$lTableName = 'chronological_categories';
			$lResult = getTaxonTreeSelectedValues($lTableName, $pParsedValue, $pUseExistingDbConnection);
			return $lResult;
		case (int) FIELD_HTML_GEOGRAPHICAL_CLASSIFICATION_AUTOCOMPLETE_TYPE :
		case (int) FIELD_HTML_GEOGRAPHICAL_CLASSIFICATION_AUTOCOMPLETE_SINGLE_TYPE:
			$lTableName = 'geographical_categories';
			$lResult = getTaxonTreeSelectedValues($lTableName, $pParsedValue, $pUseExistingDbConnection);
			return $lResult;

	}

	if(!$pDataSrcId){
		return $pParsedValue;
	}
	if(!$lResultFound){
		$lResult = getFieldSelectOptionsById($pDataSrcQuery, $pParsedValue, $pDocumentId, $pInstanceId, $pUseExistingDbConnection);
	}

	return $lResult;

}

function getFieldAutocompleteItems($pQuery, $pTerm){
	$lSrcValues = array();
	if($pTerm == ''){
		return $lSrcValues;
	}
	$gCn = new DBCn();
	$gCn->Open();
	$pQuery = str_replace('{value}', q($pTerm), $pQuery);

	$gCn->Execute($pQuery);
	$gCn->MoveFirst();

	while(!$gCn->Eof()) {
		$lCurrentRow = array();
		foreach ($gCn->mRs as $key => $value) {
			$lCurrentRow[$key] = $value;
		}
		$lSrcValues[] = $lCurrentRow;
		$gCn->MoveNext();
	}
	return $lSrcValues;
}

function getRegFieldAutocompleteItems($pTerm, $pTable, $pFilterByDocumentJournal = 0, $pInstanceId = 0){
	$lSrcValues = array();
	if($pTerm == ''){
		return $lSrcValues;
	}
	$gCn = new DBCn();
	$gCn->Open();

	if((int)$pFilterByDocumentJournal /*&& $pTable == TAXON_NOMENCLATURE_TABLE_NAME*/ && (int)$pInstanceId){
		$pQuery = 'SELECT n.id as id, n.name as name, n.pos as pos
			FROM ' . q($pTable) . ' n
			JOIN ' . q($pTable) . ' r ON r.id = CASE WHEN coalesce(n.rootnode, 0) <> 0 THEN n.rootnode ELSE n.id END
			JOIN pwt.document_object_instances i ON i.id = ' . (int)$pInstanceId . '
			JOIN pwt.documents d ON d.id = i.document_id
			WHERE n.state = 1 AND lower(n.name) LIKE \'%' . $pTerm . '%\'
				AND d.journal_id = ANY (r.journal_ids)';
	}else{
		$pQuery = 'SELECT id, name, pos FROM ' . q($pTable) . ' WHERE state = 1 AND lower(name) LIKE \'%' . $pTerm . '%\'';
	}

	//~ var_dump($pQuery);
	$gCn->Execute($pQuery);
	$gCn->MoveFirst();
	while(!$gCn->Eof()) {
		$lCurrentRow = array();
		foreach ($gCn->mRs as $key => $value) {
			if(!is_int($key))
				$lCurrentRow[$key] = $value;
		}
		$lSrcValues[] = $lCurrentRow;
		$gCn->MoveNext();
	}
	return $lSrcValues;
}

function getEmailRecipientsItems($pTerm){
	global $user;
	$gCn = new DBCn();
	$gCn->Open();
	$pQuery = 'SELECT usr.id as id, coalesce(ut.name, \'\') || \'  \' || coalesce(usr.first_name, \'\') || \' \' || coalesce(usr.last_name, \'\') as name
						FROM pwt.document_users du
						JOIN pwt.document_users dou ON dou.document_id = du.document_id AND dou.usr_id <> ' . q($user->id) . '
						JOIN public.usr usr ON usr.id = dou.usr_id AND usr.id <> ' . q($user->id) . ' AND usr.state = 1
						LEFT JOIN public.usr_titles ut ON ut.id = usr.usr_title_id
						WHERE du.usr_id = ' . q($user->id) . ' AND lower(coalesce(ut.name, \'\') || \'  \' || coalesce(usr.first_name, \'\') || \' \' || coalesce(usr.last_name, \'\')) LIKE \'%' . $pTerm . '%\'
						GROUP BY name, usr.id';
	//~ var_dump($pQuery);
	$gCn->Execute($pQuery);
	$gCn->MoveFirst();
	$lSrcValues = array();
	while(!$gCn->Eof()) {
		$lCurrentRow = array();
		foreach ($gCn->mRs as $key => $value) {
			if(!is_int($key))
				$lCurrentRow[$key] = $value;
		}
		$lSrcValues[] = $lCurrentRow;
		$gCn->MoveNext();
	}
	return $lSrcValues;
}

function getTaxonTreeSelectedValues($pTableName, $pSelectedValues, $pUseExistingDbConnection = 0, $pFilterByDocumentJournal = 0, $pInstanceId = 0){
	if(!is_array($pSelectedValues)){
		$pSelectedValues = array((int)$pSelectedValues);
	}
	$pSelectedValues = array_map('arrmap_ret', $pSelectedValues);


	if(!count($pSelectedValues)){
		return array();
	}


	if(!(int)$pUseExistingDbConnection){
		$lCn = new DBCn();
		$lCn->Open();
	}else{
		$lCn = Con();
		$lCn->CloseRs();
	}
	if((int)$pFilterByDocumentJournal && /*$pTableName == TAXON_NOMENCLATURE_TABLE_NAME && */ (int)$pInstanceId){
		$pQuery = 'SELECT r.id, n.name, r.pos
			FROM ' . q($pTableName) . ' n
			JOIN ' . q($pTableName) . '_byjournal r ON r.id = n.id
			JOIN pwt.document_object_instances i ON i.id = ' . (int)$pInstanceId . '
			JOIN pwt.documents d ON d.id = i.document_id
			WHERE n.state = 1 AND n.id IN (' . implode(',', $pSelectedValues) . ')
				AND d.journal_id = r.journal_id';
	}else{
		$pQuery = 'SELECT id, name FROM ' . q($pTableName) . ' WHERE state = 1 AND id IN (' . implode(',', $pSelectedValues) . ')';
	}
	//~ var_dump($pQuery);
	$lCn->Execute($pQuery);
	$lCn->MoveFirst();
	$lSrcValues = array();
	while(!$lCn->Eof()) {
		$lSrcValues[$lCn->mRs['id']] = $lCn->mRs['name'];
		$lCn->MoveNext();
	}

	return $lSrcValues;
}

function arrmap_ret($pValue){
		return (int)$pValue;
}

function getTreeItemRootNodeId( $pTableName, $pKey ){
	$lCn = new DBCn();
	$lCn->Open();
	$pQuery = 'SELECT rootnode FROM ' . $pTableName . ' WHERE id = ' . (int)$pKey;
	$lCn->Execute($pQuery);
	$lCn->MoveFirst();

	return $lCn->mRs['rootnode'];
}

function getRegTreeAutocompleteItems($pTableName, $pRootNode = false, $pKey = '', $pFilterByDocumentJournal = 0, $pInstanceId = 0) {
	$lWhere = ' ';
	if($pRootNode)
		$lWhere = ' WHERE n.parentnode = 0 ';
	if($pKey && $pInstanceId){
		$lCharCount = strlen($pKey);
		$lWhere = ' WHERE r.pos like \'' . $pKey . '%\' AND char_length(r.pos) = ' . $lCharCount . ' + 2 ';
	} else {
		$lCharCount = strlen($pKey);
		$lWhere = ' WHERE pos like \'' . $pKey . '%\' AND char_length(pos) = ' . $lCharCount . ' + 2 ';
	}
	$lCn = new DBCn();
	$lCn->Open();

	if((int)$pFilterByDocumentJournal /*&& $pTableName == TAXON_NOMENCLATURE_TABLE_NAME*/ && (int)$pInstanceId){
		$pQuery = 'SELECT r.id as key, n.name as title, r.pos as pos
			FROM ' . q($pTableName) . ' n
			JOIN ' . q($pTableName) . '_byjournal r ON r.id = n.id
			JOIN pwt.document_object_instances i ON i.id = ' . (int)$pInstanceId . '
			JOIN pwt.documents d ON d.id = i.document_id
			' . $lWhere . '
				AND d.journal_id = r.journal_id
			ORDER BY n.name';
	}else{
		$pQuery = 'SELECT n.id as key, n.name as title, n.pos as pos FROM ' . q($pTableName) . ' n ' .  $lWhere . ' ORDER BY name';
	}

	//~ var_dump($pQuery);
	$lCn->Execute($pQuery);
	$lCn->MoveFirst();
	$lSrcValues = array();
	while(!$lCn->Eof()) {
		$lCurrentRow = array();
		foreach ($lCn->mRs as $key => $value) {
			if(!is_int($key)) {
				$lCurrentRow[$key] = $value;
				$lCurrentRow['isLazy'] = true;
			}
		}
		$lSrcValues[] = $lCurrentRow;
		$lCn->MoveNext();
	}
	return $lSrcValues;
}

/**
 * Мести instance-а нагоре/надолу по дървото и връща масив с резултат,
 * който е готов да се върне на ajax заявка.
 * Масива е със следния формат
 * 		'err_cnt' => Дали местенето е минало ок,
 *		'err_msg' => Съобщението за грешка, ако има такава,
 *		'swap_id' => ID на instance-а, с който са разменени местата ,
 *		'original_available_move_up' => дали след това подадения instance може да мърда нагоре,
 * 		'original_available_move_down' => дали след това подадения instance може да мърда надолу,
 * 		'swap_available_move_up' => дали след това swap instance-a може да мърда нагоре,
 * 		'swap_available_move_down' => дали след това swap instance-a може да мърда надолу,
 *
 * @param unknown_type $pInstanceId
 * @param int $pOper - 1 за надолу, 2 за нагоре
 */
function MoveInstanceInDocumentTree($pInstanceId, $pOper){
	if(!in_array($pOper, array(1, 2))){
		$pOper =1;
	}
	$lCon = Con();
	$lSql = 'SELECT * FROM spMoveInstanceInDocumentTree(' . $pOper . ', ' . $pInstanceId . ', ' . (int)$user->id . ')';
	;
	$lResult = array(
		'err_cnt' => 0,
		'err_msg' => '',
	);
	if ($lCon->Execute($lSql)) {
		$lResult['swap_id'] = $lCon->mRs['swap_instance_id'];
		$lResult['original_available_move_up'] = (int)$lCon->mRs['original_available_move_up'];
		$lResult['original_available_move_down'] = (int)$lCon->mRs['original_available_move_down'];
		$lResult['swap_available_move_up'] = (int)$lCon->mRs['swap_available_move_up'];
		$lResult['swap_available_move_down'] = (int)$lCon->mRs['swap_available_move_down'];
		$lSql = 'SELECT parent_id
			FROM pwt.document_object_instances
			WHERE id = ' . (int)$pInstanceId . '
		';
		$lCon->Execute($lSql);
		$lResult['parent_id'] = (int)$lCon->mRs['parent_id'];
	}else{
		$lResult['err_cnt']++;
		$lResult['err_msg'] = getstr($lCon->GetLastError());
	}
	return $lResult;
}

function GetDocumentXslDirName($pDocumentId){
	$lCon = new DBCn();
	$lCon->Open();
	$lSql = 'SELECT t.xsl_dir_name

	FROM pwt.documents d
	JOIN pwt.templates t ON t.id = d.template_id
	WHERE d.id = ' . $pDocumentId;
	$lCon->Execute($lSql);
	return $lCon->mRs['xsl_dir_name'];
}

/**
 * Returns information about the container of the passed instance
 * @param unknown_type $gInstanceId
 * @param unknown_type $gContainerId
 * @param unknown_type $gRootInstanceId
 *
 * @return an array with the following format
 * 		'err_cnt' => Whether there is an error or not,
 *		'err_msg' => The error message,
 *		'container_id' => The id of the container,
 * 		'previous_container_id' => The id of the preceding container, which has elements, of the passed instance,
 *		'container_html' => The html content of the container,
 *		'container_actions' => The html of the actions of the container,
 *		'container_items_cnt' => The number of elements in the container,
 *		'container_items_cached_details' => An array containing information about the elements of the container. The items are listed chronologically
 *			The format of the array is:
 *				'container_item_style' => CSS style of the element,
 *				'item_type' => type of the element(field, instance, html element),
 *				'item_html' => the html contnent of the element,
 *				'item_id' => the id of the element,
 *				'item_top_actions' => the html of the top actions of the element, if the element is an instance,
 *				'item_bottom_actions' => the html of the bottom actions of the element, if the element is an instance,
 *
 */
function GetInstanceContainerDetails($pInstanceId, $pContainerId, $pRootInstanceId){
	global $user;
	$lCon = new DBCn();
	$lCon->Open();
	$lSql = 'SELECT type FROM pwt.object_containers WHERE id = ' . $pContainerId;
	$lCon->Execute($lSql);
	$lContainerType = (int)$lCon->mRs['type'];
	if(!$pRootInstanceId){
		$pRootInstanceId = $pInstanceId;
	}

	$lSql = 'SELECT char_length(r.pos)/2 as root_level, char_length(i.pos)/2 as current_level,
		i.document_id, i.is_confirmed::int as is_confirmed, t.xsl_dir_name

	FROM pwt.document_object_instances i
	JOIN pwt.document_object_instances r ON r.id = ' . (int)$pRootInstanceId . '
	JOIN pwt.documents d ON d.id = i.document_id
	JOIN pwt.templates t ON t.id = d.template_id
	WHERE i.id = ' . $pInstanceId;

	$lCon->Execute($lSql);
	$lLevel = (int)$lCon->mRs['current_level'] - (int)$lCon->mRs['root_level'] + 1;
	$lDocumentId = (int)$lCon->mRs['document_id'];
	$lInstancesIsConfirmed = (int)$lCon->mRs['is_confirmed'];
	$lTemplateXslDirName = $lCon->mRs['xsl_dir_name'];


	if(!$lDocumentId){
		$lResult = array(
			'err_cnt' => 1,
			'err_msg' => getstr('pwt.instances.insufficientParameters'),
		);
		return $lResult;
	}

	$lContainerDetails = getContainerItems($pContainerId, $pInstanceId, !$lInstancesIsConfirmed);

	$lPreviewGenerator = new cinstance_preview_generator(array(
		'template_xsl_dirname' => $lTemplateXslDirName,
		'document_id' => $lDocumentId,
		'document_xml' => getDocumentXml($lDocumentId, SERIALIZE_INTERNAL_MODE, false, false, $pInstanceId),
	));

	$lContainer = new cdocument_instance_container(array(
		'instance_id' => $pInstanceId,
		'document_id' => $lDocumentId,
		'container_id' => $pContainerId,
		'container_type' => $lContainerType,
		'items' => $lContainerDetails['items'],
		'objects_to_add' => $lContainerDetails['objects_to_add'],
		'templs' => getDocumentContainerDefaultTempls(),
		'instance_templs' => getDocumentInstanceDefaultTempls(),
		'action_templs' => getDocumentActionsDefaultTempls(),
		'field_templs' => getDocumentFieldDefaultTempls(),
		'custom_html_templs' => getDocumentCustomHtmlItemsDefaultTempls(),
		'tabbed_element_templs' => getDocumentTabbedElementDefaultTempls(),
		'level' => $lLevel,
		'get_data_from_request' => false,
		'root_instance_id' => $pRootInstanceId,
		'use_preview_generator' => true,
		'preview_generator' => $lPreviewGenerator,
	));

	$lContainerHtml = $lContainer->Display();
	$lPreviewGenerator->SetTemplate($lContainerHtml);
	$lContainerHtml = $lPreviewGenerator->Display();

	//Това е за случая, когато сме създали нов елемент и той
	//е единствения в контейнера - т.е. трябва да видим къде да сложим контейнера
	//за това взимаме id-то на предходния контейнер, който има елементи
	$lPreviousContainerId = 0;
	$lContainerItemsCount = $lContainer->getItemsCount();
	if($lContainerItemsCount == 1){
		$lSql = 'SELECT oc.id
			FROM object_container_details cd
			JOIN object_containers oc ON oc.id = cd.container_id
			JOIN object_containers oc1 ON oc1.id = ' . (int)$pContainerId . '
			JOIN document_object_instances di ON di.id = ' . ( int ) $pInstanceId . ' AND di.object_id = oc.object_id
			LEFT JOIN v_instance_fields if ON if.instance_id = di.id AND if.field_id = cd.item_id AND cd.item_type = ' . ( int ) CONTAINER_ITEM_FIELD_TYPE . '
			LEFT JOIN document_object_instances iso ON iso.parent_id = di.id
				AND cd.item_type = ' . ( int ) CONTAINER_ITEM_OBJECT_TYPE . ' AND iso.object_id = cd.item_id
			LEFT JOIN object_container_html_items chi ON chi.id = cd.item_id AND cd.item_type = ' . ( int ) CONTAINER_ITEM_CUSTOM_HTML_TYPE . '
			WHERE (iso.id IS NOT NULL OR if.field_id IS NOT NULL OR chi.id IS NOT NULL) AND oc.ord < oc1.ord
			ORDER BY oc.ord DESC
			LIMIT 1
		';
		$lCon->Execute($lSql);
		$lPreviousContainerId = (int)$lCon->mRs['id'];
	}

	$lResult = array(
		'err_cnt' => 0,
		'err_msg' => '',
		'container_id' => $pContainerId,
		'previous_container_id' => $lPreviousContainerId,
		'container_html' => $lContainerHtml,
		'container_actions' => $lContainer->GetVal('actions'),
		'container_items_cnt' => $lContainerItemsCount,
		'container_items_cached_details' => $lContainer->getItemsCachedDetails(),
	);
	return $lResult;
}

/**
 * Removes the instance from the document tree
 * @param unknown_type $gInstanceId
 *
 * @return an array with the following format
 * 		'err_cnt' => Whether the removal was successful or not,
 *		'err_msg' => The error message,
 *		'container_id' => The id of the container which was holding the instance,
 *		'display_in_tree' => Whether the instance was displayed in the document tree or not,
 *		'parent_instance_id' => The id of the parent instance
 */
function removeInstance($pInstanceId){
	global $user;

	$lCon = new DBCn();
	$lCon->Open();
	$lSql = 'SELECT * FROM spRemoveInstance(' . (int)$pInstanceId . ', ' . (int)$user->id . ');';

	$lResult = array(
		'err_cnt' => 0,
		'err_msg' => '',
	);
	if ($lCon->Execute($lSql)) {
		$lResult['parent_instance_id'] = $lCon->mRs['parent_instance_id'];
		$lResult['display_in_tree'] = (int)$lCon->mRs['display_in_tree'];
		$lResult['container_id'] = (int)$lCon->mRs['container_id'];
		$lResult['deleted_instance_id'] = (int)$pInstanceId;
	}else{
		$lResult['err_cnt']++;
		$lResult['err_msg'] = getstr($lCon->GetLastError());
	}
	return $lResult;
}

function getInputWrapperClass($pHasHelpLabel, $pHasValidationError, $pHelpLabelDisplayStyle){
	if((int)$pHasHelpLabel){
		if($pHelpLabelDisplayStyle == FIELD_HELP_LABEL_ICON_STYLE){
			$lResult .= ' P-Input-With-Help';
		}elseif($pHelpLabelDisplayStyle == FIELD_HELP_LABEL_DESCRIPTION_STYLE){
			$lResult .= ' P-Input-With-Desc';
		}

	}
	if((int)$pHasValidationError){
		$lResult .= ' P-Input-Err';
	}
	return $lResult;
}

function getDocumentPathLink($pInstanceId, $pInstanceName, $pCurrentInstanceId){
	if((int)$pInstanceId != (int)$pCurrentInstanceId){
		return '<a href="/display_document.php?instance_id=' . $pInstanceId . '">' . CutText(trim($pInstanceName), 40) . '</a>';
	}
	return $pInstanceName;
}

function getDocumentFirstInstanceId($pDocumentId){
	global $user;
	$lDoc = new crs(
		array('ctype'=>'crs',
			'templs'=>array(
				G_HEADER=>'', G_ROWTEMPL=>'', G_FOOTER =>'', G_NODATA =>'',
			),
			'sqlstr'=>'SELECT id
				FROM pwt.document_object_instances doi
				JOIN pwt.document_users du on  (doi.document_id = du.document_id)
				WHERE
					doi.document_id = ' . $pDocumentId . '
					' . (($user->admin == 'false' || $user->admin == 'f') ? 'AND du.usr_id = ' . (int)$user->id : '') . '
					AND doi.object_id  IN (' . FIRST_INSTANCE_IDS_FOR_DIFFERENT_TEMPLATES . ')
				ORDER BY pos
				LIMIT 1',
		)
	);
	$lDoc->GetData();
	return $lDoc->GetVal('id');
}

function getDocumentMetadataInstanceId($pDocumentId){
	global $user;
	$lDoc = new crs(
		array('ctype'=>'crs',
			'sqlstr'=>'SELECT id
				FROM pwt.document_object_instances doi
				JOIN pwt.document_users du on  (doi.document_id = du.document_id)
				WHERE
					doi.document_id = ' . $pDocumentId . '
					' . (($user->admin == 'false' || $user->admin == 'f') ? 'and du.usr_id = ' . (int)$user->id : '') . '
					AND doi.object_id IN (' . METADATA_OBJECT_IDS_FOR_DIFFERENT_TEMPLATES . ')
				ORDER BY pos
				LIMIT 1',
		)
	);
	$lDoc->GetData();
	return $lDoc->GetVal('id');
}


/**
 * Изпълняваме действията на инстанса за след save
 */
function performInstanceSaveActions($pInstanceId, $pAdditionalParameters = array()){
	$lSql = 'SELECT a.action_id as action_id
		FROM pwt.document_object_instances i
		JOIN pwt.object_actions a ON a.object_id = i.object_id
		WHERE i.id = ' . (int)$pInstanceId . ' AND a.pos = ' . (int) ACTION_AFTER_SAVE_POS . '
		ORDER BY a.ord ASC
	';
	$lCon = new DBCn();
	$lCon->Open();
	$lCon->Execute($lSql);
//	var_dump($pInstanceId);

	while(!$lCon->Eof()){
//		var_dump($pInstanceId, $lCon->mRs['action_id']);
		executeInstanceAction($pInstanceId, $lCon->mRs['action_id'], $pAdditionalParameters);
		$lCon->MoveNext();
	}
}

function executeInstanceAction($pInstanceId, $pActionId, $pAdditionalParameters = array()){
	if(!is_array($pAdditionalParameters)){
		$pAdditionalParameters = array();
	}
	$lParameters = getActionParameters($pInstanceId, $pActionId);
	$lCon = new DBCn();
	$lCon->Open();
	$lFieldValues = array();
	if(count($lParameters)){
		$lSql = 'SELECT fv.*, ft.value_column_name, ft.id AS field_value_type
			FROM pwt.instance_field_values fv
			JOIN pwt.fields f ON f.id = fv.field_id
			JOIN pwt.field_types ft ON ft.id = f.type
			WHERE ( false';

		foreach ($lParameters as $lParameterName => $lParameterDetails) {
			$lSql .= 'OR (fv.field_id = ' . (int)$lParameterDetails['field_id'] . 'AND fv.instance_id = ' . (int)$lParameterDetails['instance_id'] . ')';
		}
		$lCon->Execute($lSql);
		while(!$lCon->Eof()){
			$lValueColumnName = $lCon->mRs['value_column_name'];
			$lFieldValueType = $lCon->mRs['field_value_type'];
			$lSqlValue = $lCon->mRs[$lValueColumnName];
			$lParsedValue = parseFieldValue($lSqlValue, $lFieldValueType);
			$lFieldValues[(int)$lCon->mRs['instance_id'] . INSTANCE_FIELD_NAME_SEPARATOR . (int)$lCon->mRs['field_id']] = $lParsedValue;
		}
	}

	$lPostData = array(
		'instance_id' => $pInstanceId,
		'action_id' => $pActionId
	);


	foreach ($lParameters as $lParameterName => $lParameterDetails) {
		$lPostData[$lParameterName] = $lFieldValues[$lParameterDetails['input_name']];
	}
	$lPostData = array_merge($lPostData, $pAdditionalParameters);
// 	$lPreviousGet = $_GET;
// 	$lPreviousPost = $_POST;
// 	$lPreviuosRequest = $_REQUEST;
// 	$_GET = array();
// 	$_POST = $lPostData;
// 	$_REQUEST = $lPostData;
	executeExternalQuery(ACTION_AJAX_URL, $lPostData, '', 30, true);
// 	executeAction(true);
// 	$_POST = $lPreviousPost;
// 	$_GET = $lPreviousGet;
// 	$_REQUEST = $lPreviuosRequest;
}

function parseFieldValue($pSqlValue, $pFieldType){
	if($pSqlValue === NULL)
		return NULL;
	switch ($pFieldType) {
		case FIELD_INT_TYPE:
			$lResult = $pSqlValue ;
			break;
		default:
		case FIELD_STRING_TYPE:
			$lResult = $pSqlValue ;
			break;
		case FIELD_DATE_TYPE:
			$lResult = parseFieldDateFromDb($pSqlValue);
			break;
		case FIELD_CHECKBOX_MANY_TO_STRING_TYPE:
			$lResult = explode(DEF_SQLSTR_SEPARATOR, $pSqlValue);
			break;
		case FIELD_CHECKBOX_MANY_TO_BIT_TYPE:
			$lResult = int2bitarray($pSqlValue);
			break;
		case FIELD_CHECKBOX_MANY_TO_BIT_ONE_BOX_TYPE:
			$lResult = $pSqlValue;
			break;
		case FIELD_CHECKBOX_MANY_TO_SQL_INT_ARRAY_TYPE:
			$lResult = pg_unescape_array($pSqlValue);
			break;
		case FIELD_CHECKBOX_MANY_TO_SQL_STRING_ARRAY_TYPE:
			$lResult = pg_unescape_array($pSqlValue);
			break;
		case FIELD_CHECKBOX_MANY_TO_SQL_DATE_ARRAY_TYPE:
			$lResult = pg_unescape_array($pSqlValue);
			break;
	}

	return $lResult;
}

/**
 * Here we will return a representation of the date if format d m YYYY
 * because the default db style is YYYY m d
 * @param unknown_type $pDate
 */
function parseFieldDateFromDb($pDate){
	if (!preg_match('/[\/\\\.\-]/', $pDate, $lMatches)) {
		return $pDate;
	}

	$lSeparator = $lMatches[0]; //Kato nqma skobi v reg expa v 0-q element e kakvoto e machnalo

	if(! preg_match('/^(\d{2,4})\\' . $lSeparator . '(\d{1,2})\\' . $lSeparator . '(\d{1,2})$/i', $pDate, $lMatches)){
		return $pDate;
	}
	$lTimeFormat = mktime(null, null, null, $lMatches[2], $lMatches[3], $lMatches[1]);
	return date('d/m/Y', $lTimeFormat);
}

function getActionParameters($pInstanceId, $pActionId){
	$lCon = new DBCn();
	$lCon->Open();

	$lSql = 'SELECT * FROM pwt.action_parameters WHERE action_id = ' . (int) $pActionId;
	$lCon->Execute($lSql);

	$lParameters = array();
	while(!$lCon->Eof()){
		$lParameters[$lCon->mRs['parameter_name']] = array('id' => $lCon->mRs['id']);
		$lCon->MoveNext();
	}
	$lResultParameters = array();
	foreach ($lParameters as $lParameterName => $lParameterData) {
		$lSql = 'SELECT * FROM spGetActionParameterDetails(' . $pInstanceId . ', ' . $lParameterData['id'] . ')';

		$lCon->Execute($lSql);

		if((int)$lCon->mRs['instance_id'] && (int)$lCon->mRs['field_id']){
			$lResultParameters[$lParameterName] = array(
				'instance_id' => (int)$lCon->mRs['instance_id'],
				'field_id' => (int)$lCon->mRs['field_id'],
				'input_name' => (int)$lCon->mRs['instance_id'] . INSTANCE_FIELD_NAME_SEPARATOR . (int)$lCon->mRs['field_id'],
			);
		}
	}
	return $lResultParameters;
}

function parseToInt($pArgument){
	return (int)$pArgument;
}

function displayInstanceHiddenInput($pInstanceId, $pMode){
	$lModes = array(
		INSTANCE_EDIT_MODE => 'instance_ids[]',
		INSTANCE_VIEW_MODE => 'instance_in_viewmode_ids[]',
		INSTANCE_TITLE_MODE => 'instance_in_titlemode_ids[]',
	);

	return '<input type="hidden" name="' . $lModes[$pMode] . '" value="' . (int)$pInstanceId . '" />';
}

function getRegTreeCategoriesByRootNodes($pTableName, $pRootNode = false, $pKey = '', $pFilterByDocumentJournal = 0, $pInstanceId = 0, $pSelectedValue=0) {
	$lWhere = ' ';

	if($pRootNode)
		$lWhere = ' AND char_length(r.pos) = ' . 2;
	if($pKey)
		$lWhere = ' AND n.rootnode = ' . $pKey;
	//var_dump($pSelectedValue);
	if(count($pSelectedValue) > 0)
	{
		$pSelectedValue = $pSelectedValue['1'];
		if(strlen($pSelectedValue) > 0)
		{
			$lWhere .= '
			AND ("nomenclaturalCode" = (SELECT "nomenclaturalCode" FROM taxon_categories WHERE id = '.$pSelectedValue.'))';
		}
	}

	if((int)$pFilterByDocumentJournal && /*$pTableName == TAXON_NOMENCLATURE_TABLE_NAME &&*/ (int)$pInstanceId){
		$lSql = 'SELECT r.id as id, r.pos as pos, n.name , r.root, n.parentnode
		FROM ' . q($pTableName) . ' n
		JOIN ' . q($pTableName) . '_byjournal r ON r.id = n.id
		JOIN pwt.document_object_instances i ON i.id = ' . (int)$pInstanceId . '
		JOIN pwt.documents d ON d.id = i.document_id
		WHERE n.state = 1 AND d.journal_id = r.journal_id ';
	}else{
		$lSql = 'SELECT n.id, n.name, n.rootnode, n.parentnode FROM ' . $pTableName . ' n WHERE n.state = 1 ' ;
	}

	$lSql .= $lWhere . ' ORDER BY n.name';

	$tree_chronological = new crsrecursive(array(
		'ctype'=>'crsrecursive',
		'templs'=>array(
			G_HEADER=>'treeview.treeviewtop',
			G_ROWTEMPL=>'treeview.treeviewrowtempl',
			G_FOOTER =>'treeview.treeviewfoot',
		),
		'recursivecolumn'=>'root',
		'templadd'=>'type',
		'sqlstr'=> $lSql,
	)
);

	$tree_chronological->GetData();
	$tree_chronological->DontGetData(true);
	return $tree_chronological->Display();
}

function getRegTreeCategoriesByName($pTableName, $pRootNode = false, $pKey = 0) {
	$lWhere = ' ';
	if($pRootNode)
		$lWhere = ' WHERE rootnode = 0 ';
	if($pKey)
		$lWhere = ' WHERE rootnode = ' . $pKey . ' ';
	$tree_chronological = new crsrecursive(array(
	'ctype'=>'crsrecursive',
	'templs'=>array(
		G_HEADER=>'treeview.treeviewtop',
		G_ROWTEMPL=>'treeview.treeviewrowtempl',
		G_FOOTER =>'treeview.treeviewfoot',
	),
	'recursivecolumn'=>'rootnode',
	'templadd'=>'type',
	'sqlstr'=>'SELECT id, name, rootnode, pos FROM ' . $pTableName . ' ' .  $lWhere . ' ORDER BY name',
	)
);

	$tree_chronological->GetData();
	$tree_chronological->DontGetData(true);
	return $tree_chronological->Display();
}

function UploadPic($pName, $pDir, $pPicId, $pUserId, &$pError, $pProfilePic = false) {
	//~ $gMaxSize = (int)UPLOAD_MAX_FILESIZE; // 100 MB
	$gMaxSize = 5*1024*1024; // 5 MB
	$lPicId = $pPicId;
	//~ $gMaxSize = 500 // Za testove;
	$extarray = array(".jpeg", ".jpg", ".gif", ".tif", ".tiff", ".bmp", ".png");
	$typearr = array("image/pjpeg", "image/jpeg", "image/gif", "image/tiff", "image/png", "image/bmp");
	$imgUploadErr = 1;

	if ( $_FILES[$pName]['name'] ) {

		$pFnUpl = $_FILES[$pName]['name'];
		$pTitle = $pFnUpl;

		$gFileExt = substr($_FILES[$pName]['name'], strrpos($_FILES[$pName]['name'], '.'));
		$isImageExtension = in_array(strtolower($gFileExt), $extarray);
		$isImageMime = true;

		if ($isImageExtension && $isImageMime) {
				if ($_FILES[$pName]['size'] > $gMaxSize) {
				$pError = getstr('global.uploadError_picTooBigMaxSize')  . number_format(($gMaxSize / (1024 * 1024)), 2). ' MB';
			} elseif (!$_FILES[$pName]["size"]) {
				$pError = getstr('global.uploadError_wrongFile');
			} elseif ($_FILES[$pName]['error'] == UPLOAD_ERR_OK) {
				$lCn = Con() ;
				$lSql = GetUploadPicSql($pTitle, $pFnUpl, $pPicText, $pUserId);
				$lCn->Execute($lSql);
				$lCn->MoveFirst();
				$lPicId = (int)$lCn->mRs['picid'];
				if ($lPicId) {
					if(file_exists($pDir . 'oo_' . $lPicId . '.jpg')) {
						unlink($pDir . 'oo_' . $lPicId . '.jpg');
						DeletePicFiles($lPicId);
					}
					if (!move_uploaded_file($_FILES[$pName]['tmp_name'], $pDir . $lPicId . $gFileExt)) {
						$pError = getstr('admin.articles.error_error') . $_FILES[$pName]['error'];
					} else {
						// Vsichko e ok... pravim jpg i mahame originala
						if($gFileExt == '.gif' || $gFileExt == '.png') {
							//~ trigger_error($pDir . $lPicId . $gFileExt, E_USER_NOTICE);
							// [0] след името на файла е, ако се качи анимиран gif да вземе само първия image
							if($pProfilePic) {
								exec(escapeshellcmd("convert -colorspace sRGB  " . $pDir . $lPicId . $gFileExt . "[0] " . $pDir . 'oo_' . $lPicId . '.jpg' ));
								exec("convert -colorspace sRGB -thumbnail " . escapeshellarg('1024x1024>') . " " . $pDir . $lPicId . $gFileExt . "[0] " . $pDir . 'big_' . $lPicId . '.jpg' );
							} else {
								exec(escapeshellcmd("convert -colorspace sRGB " . $pDir . $lPicId . $gFileExt . "[0] " . $pDir . 'oo_' . $lPicId . $gFileExt ));
								exec("convert -colorspace sRGB -thumbnail " . escapeshellarg('1024x1024>') . " " . $pDir . $lPicId . $gFileExt . "[0] " . $pDir . 'big_' . $lPicId . $gFileExt );
							}

						} else {
							exec(escapeshellcmd("convert -colorspace sRGB " . $pDir . $lPicId . $gFileExt . "[0] " . $pDir . 'oo_' . $lPicId . '.jpg' ));
							exec("convert -colorspace sRGB -thumbnail " . escapeshellarg('1024x1024>') . " " . $pDir . $lPicId . $gFileExt . "[0] " . $pDir . 'big_' . $lPicId . '.jpg' );
						}
						unlink($pDir . $lPicId . $gFileExt);
						$imgUploadErr = 0;
					}
				} else {
					$pError = getstr('global.uploadErrordbError');
				}
			} else {
				$pError = getstr('global.uploadError_errorWhileSavingFile');
			}
		} else {
			$pError = $gFileExt . '  ' . getstr('global.uploadError_wrongFileFormatAllowedFormatsAre') . implode(' ', $extarray);
		}
	} else {
		$pError = getstr('global.uploadError_noFileUploaded');
	}

	if (!$imgUploadErr)
		return $lPicId;

	if ($lPicId) //Mahame snimkata pri greshka
		$lCn->Execute('SELECT PicsUpload(3, ' . (int)$lPicId . ', null, null, null, null, null);');
	return false;
}

function GetUploadPicSql($pTitle, $pFnUpl, $pPicText, $pUserId){
	$lSql = 'SELECT ProfPicUpload(1, null, 0,\'' . q($pTitle) . '\', \'' . q($pFnUpl) . '\', \'' . q($PicText) . '\', ' . (int)$pUserId . ', 0) as picid';
	return $lSql;
}

function prepArrForJSAutocomplete($pArr) {
	$lRet = '';
	$numItems = count($pArr);
	$i = 0;
	foreach($pArr as $key => $val) {
		$lRet .= '{';
		$lRet .= 'id:' . $key . ', name:'  . '\'' . $val. '\'';
		if($i+1 == $numItems) {
			$lRet .= "\n" . '}';
		} else {
			$lRet .= "\n" . '},';
		}
		$i++;
	}
	return $lRet;
}

function getRegFieldAutoItems($pTable, $pData){
	$gCn = new DBCn();
	$gCn->Open();
	$pQuery = 'SELECT id, name FROM ' . q($pTable) . ' WHERE state = 1 AND id IN ' . $pData;
	//~ var_dump($pQuery);
	$gCn->Execute($pQuery);
	$gCn->MoveFirst();
	$lSrcValues = array();
	while(!$gCn->Eof()) {
		$lCurrentRow = array();
		foreach ($gCn->mRs as $key => $value) {
			if(!is_int($key))
				$lCurrentRow[$key] = $value;
		}
		$lSrcValues[] = $lCurrentRow;
		$gCn->MoveNext();
	}
	return $lSrcValues;
}

function GetProfPic($pKfor) {
	$lRet = '';
	if((int)$pKfor['photoid']){
		$lRet = '<div id="Prof-Photo" href="javascript:void(0)">
					<img class="P-Prof-Pic" src="/showimg.php?filename=c67x70y_' . (int)$pKfor['photoid'] . '.jpg" alt="" />
					<div class="P-Clear"></div>
				</div>
				' . getstr('pwt.changeProfilePicture');
	} elseif ((int)$pKfor['userid']) {
		$gCn = new DBCn();
		$gCn->Open();
		$pQuery = 'SELECT photo_id FROM public.usr WHERE id='. (int)$pKfor['userid'];
		//~ var_dump($pQuery);
		$gCn->Execute($pQuery);
		$gCn->MoveFirst();
		if((int)$gCn->mRs['photo_id']){
			 $lRet = '<div id="Prof-Photo" href="javascript:void(0)">
						<img class="P-Prof-Pic" src="/showimg.php?filename=c67x70y_' . (int)$gCn->mRs['photo_id'] . '.jpg" alt="" />
						<div class="P-Clear"></div>
					</div>
					' . getstr('pwt.changeProfilePicture');
		}else{
			$lRet = '<div id="Prof-Photo" href="javascript:void(0)">
						<img src="i/add_photo.png" alt="" />
						<div class="P-Clear"></div>
						' . getstr('pwt.addProfilePicture') . '
					</div>
					';
		}
	} else {
		$lRet = '<div id="Prof-Photo" href="javascript:void(0)">
					<img src="i/add_photo.png" alt="" />
					<div class="P-Clear"></div>
				</div>
				' . getstr('pwt.addProfilePicture');
	}
	return $lRet;
}

function showRegEditProfileBtnsStep2($pKfor) {
	if((int)$pKfor['editprof'])
		return '<div class="P-Grey-Btn-Holder P-Reg-Btn">
				<div class="P-Grey-Btn-Left"></div>
				<div class="P-Grey-Btn-Middle" onclick="SubmitRegForm(\'P-Registration-Content\', \'registerfrm\', 1, 2, 1)">Save</div>
				<div class="P-Grey-Btn-Right"></div>
			</div>
			<div class="P-Grey-Btn-Holder P-Reg-Btn-R">
				<div class="P-Grey-Btn-Left"></div>
				<div class="P-Grey-Btn-Middle" onclick="$.modal.close();">Close</div>
				<div class="P-Grey-Btn-Right"></div>
			</div>';
	return '<div class="P-Grey-Btn-Holder P-Reg-Btn">
				<div class="P-Grey-Btn-Left"></div>
				<div class="P-Grey-Btn-Middle" onclick="SubmitRegForm(\'P-Registration-Content\', \'registerfrm\', 2, 1, 0)">&laquo; Previous step 1</div>
				<div class="P-Grey-Btn-Right"></div>
			</div>
			<div class="P-Grey-Btn-Holder P-Reg-Btn-R">
				<div class="P-Grey-Btn-Left"></div>
				<div class="P-Grey-Btn-Middle" onclick="SubmitRegForm(\'P-Registration-Content\', \'registerfrm\', 1, 2, 0)">Next step 3 &raquo;</div>
				<div class="P-Grey-Btn-Right"></div>
			</div>';
}

function showRegEditProfileBtnsStep3($pKfor) {
	if((int)$pKfor['editprof'])
		return '<div class="P-Grey-Btn-Holder P-Reg-Btn">
				<div class="P-Grey-Btn-Left"></div>
				<div class="P-Grey-Btn-Middle" onclick="SubmitRegForm(\'P-Registration-Content\', \'registerfrm\', 1, 3, 1)">Save</div>
				<div class="P-Grey-Btn-Right"></div>
			</div>
			<div class="P-Grey-Btn-Holder P-Reg-Btn-R">
				<div class="P-Grey-Btn-Left"></div>
				<div class="P-Grey-Btn-Middle" onclick="$.modal.close();">Close</div>
				<div class="P-Grey-Btn-Right"></div>
			</div>';
	return '<div class="P-Grey-Btn-Holder P-Reg-Btn">
				<div class="P-Grey-Btn-Left"></div>
				<div class="P-Grey-Btn-Middle" onclick="SubmitRegForm(\'P-Registration-Content\', \'registerfrm\', 2, 2, 0)">&laquo; Previous step 2</div>
				<div class="P-Grey-Btn-Right"></div>
			</div>
			<div onclick="" class="P-Green-Btn-Holder P-Reg-Btn-R">
				<div class="P-Green-Btn-Left"></div>
				<div class="P-Green-Btn-Middle" onclick="SubmitRegForm(\'P-Registration-Content\', \'registerfrm\', 1, 3)">Finish</div>
				<div class="P-Green-Btn-Right"></div>
			</div>';
}

function showRegEditProfileBtnsStep1($pKfor) {
	if((int)$pKfor['editprof'])
		return '<div class="P-Grey-Btn-Holder P-Reg-Btn">
				<div class="P-Grey-Btn-Left"></div>
				<div class="P-Grey-Btn-Middle" onclick="SubmitRegForm(\'P-Registration-Content\', \'registerfrm\', 1, 1, 1)">Save</div>
				<div class="P-Grey-Btn-Right"></div>
			</div>
			<div class="P-Grey-Btn-Holder P-Reg-Btn-R">
				<div class="P-Grey-Btn-Left"></div>
				<div class="P-Grey-Btn-Middle" onclick="$.modal.close();">Close</div>
				<div class="P-Grey-Btn-Right"></div>
			</div>';
	return '<div class="P-Grey-Btn-Holder P-Reg-Btn">
				<div class="P-Grey-Btn-Left"></div>
				<div class="P-Grey-Btn-Middle" onclick="SubmitRegForm(\'P-Registration-Content\', \'registerfrm\', 1, 1, 0)">Next step 2 &raquo;</div>
				<div class="P-Grey-Btn-Right"></div>
			</div>';
}

function prepAutocompleteKforField($pKforFieldVals) {
	$pos = strpos($pKforFieldVals, '{');
	if($pos === false) {
		return '(' . $pKforFieldVals . ')';
	}
	else {
		$lReplChars = str_replace('{', '(', $pKforFieldVals);
		return str_replace('}', ')', $lReplChars);
	}
}

function checkEmailAddr($pFld) {
	if(!preg_match("/^[A-Za-z0-9_\.-]+@([A-Za-z0-9_\.-])+\.[A-Za-z]{2,6}$/",  $pFld ))
		return 'Not a valid email';
}

//Poneje ne e na nash syrvyr ne sa pozvoleni exec i system i polzvame perl script koito execva komandite
function executeConsoleCommand($pCommand, $pArgs, $pUsePassThru = 1){
	if( !is_array($pArgs))
		$pArgs = array();
	/**
		Масив с кодовете на операциите
	*/
	if( (int) USE_PERL_EXECS ){
		$lCommandsArr = array(
			1 => 'convert',
		);
		$lPerlPhpAddress = PERL_EXEC_ADDRESS;

		$lCommandFound = false;
		$gCommandCode = -1;
		foreach( $lCommandsArr as $lCommandCode => $lCommandName ){
			if( $lCommandName == $pCommand ){
				$lCommandFound = true;
				$gCommandCode = $lCommandCode;
			}
		}
		if( !$lCommandFound )
			return;

		$lUrl = $lPerlPhpAddress . '?command=' . $gCommandCode . '&argsCount=' . (int) count($pArgs);

		$lArgIter = 1;
		foreach( $pArgs as $lCommandArgument ){
			$lUrl .= '&arg' . $lArgIter . '=' . rawurlencode($lCommandArgument);
			$lArgIter++;
		}
		return file_get_contents($lUrl);
	}else{
		$lCommandToExecute = $pCommand;
		foreach( $pArgs as $lCommandArgument ){
			$lCommandToExecute .= ' ' .$lCommandArgument;
		}
		if( $pUsePassThru ){

			ob_start();
			passthru($lCommandToExecute);
			$lContent = ob_get_contents();
			ob_clean();
			return $lContent;
		}else{
			exec($lCommandToExecute);
		}
	}
}

function getUserName() {
	global $user;
	$gCn = new DBCn();
	$gCn->Open();
	$lSql = 'SELECT name FROM usr_titles WHERE id = ' . (int)$user->usr_title_id;
	$gCn->Execute($lSql);
	$gCn->MoveFirst();
	return $gCn->mRs['name'] . ' ' . $user->fullname;
}

function getUserNameById($pId) {
	global $user;
	$gCn = new DBCn();
	$gCn->Open();
	$lSql = 'SELECT coalesce(u.first_name || \' \' || u.last_name, u.uname) AS fullname FROM public.usr u WHERE u.id = ' . (int)$pId;
	$gCn->Execute($lSql);
	$gCn->MoveFirst();
	return $gCn->mRs['fullname'];
}

function getUserEmail($pUsrId) {
	$gCn = new DBCn();
	$gCn->Open();
	$lSql = 'SELECT uname FROM usr WHERE id = ' . (int)$pUsrId;
	$gCn->Execute($lSql);
	$gCn->MoveFirst();
	return $gCn->mRs['uname'];
}

function GetInstanceContainerId($pInstanceId){
	$lSql = 'SELECT c.id as container_id
		FROM pwt.object_container_details cd
		JOIN pwt.object_containers c ON c.id = cd.container_id
		JOIN pwt.document_object_instances i ON i.object_id = cd.item_id AND cd.item_type = ' . (int)CONTAINER_ITEM_OBJECT_TYPE . '
		JOIN pwt.document_object_instances p ON p.id = i.parent_id
		WHERE p.object_id = c.object_id AND i.id = ' . (int)$pInstanceId;
	$lCon = new DBCn();
	$lCon->Open();
	$lCon->Execute($lSql);
	return $lCon->mRs['container_id'];
}

function GetInstanceParentInstanceId($pInstanceId){
	$lSql = 'SELECT parent_id
	FROM pwt.document_object_instances i
	WHERE i.id = ' . (int)$pInstanceId;
	$lCon = new DBCn();
	$lCon->Open();
	$lCon->Execute($lSql);
	return $lCon->mRs['parent_id'];
}

function showProfilePic() {
	global $user;
	return showUserPic($user->photo_id);
}

function showUserPic($photo_id)
{
	return '<img src="' . getUserProfileImg($photo_id) . '" width="30" height="30" alt="" style="float: left" />';
}

function showFormatedPubDate($pPubdate, $pDateOnly = false, $pSwitchDateYear = false) {
	global $gMonths;
	if (!preg_match('/(\d+)[-–\/](\d+)[-–\/](\d+)/', $pPubdate, $lMatch)) {
		return '';
	}
	$lMonth = ltrim($lMatch[2], '0');
	if ($pDateOnly) {
		return (int)$lMatch[1] . ' ' . substr(ucfirst($gMonths[$lMonth]), 0, 3) . '. ';
	}
	if($pSwitchDateYear){
		return $lMatch[3] . ' ' . substr(ucfirst($gMonths[$lMonth]), 0, 3) . '. ' . (int)$lMatch[1];
	}
	return (int)$lMatch[1] . ' ' . substr(ucfirst($gMonths[$lMonth]), 0, 3) . '. ' . $lMatch[3];
}

function showCommentDate($pPubDate){
	global $gMonths;
	if (!preg_match('/(\d+)[-–\/](\d+)[-–\/](\d+)\s+(\d+:\s*\d+)/', $pPubDate, $lMatch)) {
		return '';
	}
	$lMonth = ltrim($lMatch[2], '0');
	$lMonthName = substr(ucfirst($gMonths[$lMonth]), 0, 3);

	return $lMatch[4] . ' on ' . (int)$lMatch[3] . ' ' . $lMonthName . '. ' . $lMatch[1];
}

function formatCreateDate($pPubdate) {
	global $gMonths;
	$lDate = explode(' ', $pPubdate);
	$lTime = explode(':', $lDate[1]);

	if (!preg_match('/(\d+)[-–\/](\d+)[-–\/](\d+)/', $lDate[0], $lMatch)) {
		return '';
	}
	$lMonth = ltrim($lMatch[2], '0');

	return (int)$lMatch[1] . ' ' . substr(ucfirst($gMonths[$lMonth]), 0, 3) . '. ' . $lMatch[3] . '&nbsp;&nbsp;&nbsp;' . $lTime[0] . ':' . $lTime[1];
}

function showCommentAnswerForm($pInstanceId, $pDocumentId, $pRootMsgId, $pVersionIsReadonly = false) {
	if($pVersionIsReadonly){
		return '';
	}

	$lComment = new ccomments(array(
			'ctype' => 'ccomments',
			'showtype' => 2,
			'rootmsgid' => $pRootMsgId,
			'instance_id' => $pInstanceId,
			'document_id' => $pDocumentId,
			'formaction' =>  $_SERVER['REQUEST_URI'],
			'returl' => '/display_document.php?instance_id=' . $pInstanceId,
			'templs' => array(
				G_DEFAULT => 'comments.commentanswerform',
			),
		)
	);
	$lComment->Display();
	$lResult = '

			<div id="P-Comment-Form_' . (int)$pRootMsgId . '" class="P-Comment-Reply-Form-Wrapper" style="display: none;">
				<div class="P-Comment-Reply-Form">
					' . $lComment->Display() . '
					<div class="P-Clear"></div>
					<div class="reply_btn" onmousedown="SubmitCommentForm(\'P-Root-Comment-' . (int)$pRootMsgId . '\', \'commentpost_' . (int)$pRootMsgId . '\', 1, ' . (int)$pRootMsgId . ');"></div>
					<div class="P-Comment-Reply-Form-Cancel-Btn" onmousedown="showCommentForm(' . (int)$pRootMsgId . ');"></div>
					<div class="P-Clear"></div>
				</div>
			</div>
	';
	return $lResult;
}

function displayRootCommentActions($pCommentId, $pDocumentId, $pInstanceId, $pIsResolved, $pResolveUid, $pResolveUserFullname, $pResolveDate, $pUsrId, $pVersionIsReadonly = false){
// 	$pVersionIsReadonly = 1;
	if($pVersionIsReadonly){
		return '
			<div class="P-Inline-Line"></div>
			<div class="P-Comment-Root-Action-Btns">
				' . displayResolvedInfo($pCommentId, $pIsResolved, $pResolveUid, $pResolveUserFullname, $pResolveDate, $pVersionIsReadonly) . '
				<div class="P-Clear"></div>
			</div>';
	}

	$lResult = '
			<div class="P-Inline-Line"></div>
			<div class="P-Comment-Root-Action-Btns">
				<div onclick="showCommentForm(' . (int)$pCommentId . ');" class="reply_btn P-Comment-Reply-Btn" id="P-Comment-Btn-' . (int)$pCommentId . '"></div>
				' . displayResolvedInfo($pCommentId, $pIsResolved, $pResolveUid, $pResolveUserFullname, $pResolveDate, $pVersionIsReadonly) . '
				' . displayDeleteCommentBtn($pCommentId, $pUsrId, $pVersionIsReadonly) . '

				<div class="P-Clear"></div>
				' . showCommentAnswerForm($pInstanceId, $pDocumentId, $pCommentId, $pVersionIsReadonly) . '

				<div class="P-Clear"></div>
			</div>';
	return $lResult;
}

function putCommentOnClickEvent($pCommentId, $pCommentUsrId, $pVersionIsReadonly = false){
	global $user;
	if($pVersionIsReadonly || (int)$user->id != $pCommentUsrId){
		return;
	}
	return ' onclick="displayCommentEditForm(' . (int)$pCommentId . ')"';
}

function showCommentEditForm($pCommentId, $pCommentUsrId, $pDocumentId, $pVersionIsReadonly = false){
	global $user;
	if($pVersionIsReadonly || (int)$user->id != $pCommentUsrId){
		return;
	}
	$lComment = new ccomments( array (
		'ctype' => 'ccomments',
		'showtype' => 4,
		'comment_id' => $pCommentId,
		'document_id' => $pDocumentId,
		'formaction' => $_SERVER ['REQUEST_URI'],
		'templs' => array (
			G_DEFAULT => 'comments.editform_wrapper'
		)
	) );
	$lRes = '
				<div id="P-Comment-Edit-Form_' . (int)$pCommentId . '" class="P-Comment-Edit-Form" style="display:none" >
					' . $lComment->Display() . '
				</div>
	';
	return $lRes;
}

function showCommentPic($pPhotoId, $pIsDisclosed, $pUserRealId, $pCurrentUserId) {
	$lUserIsDisclosed = CheckIfUserIsDisclosed($pIsDisclosed, $pUserRealId, $pCurrentUserId);
	if($pPhotoId && $lUserIsDisclosed)
		return '<img border="0" alt="" height="27" width="27" src="/showimg.php?filename=c27x27y_' . (int)$pPhotoId . '.jpg" />';
	return '<img src="./i/user_no_img.png" alt="" height="27" width="27" />';
}

function displaySingleCommentInfo($pCommentId, $pRootId, $pUserPhotoId, $pIsDisclosed, $pUserRealId, $pCurrentUserId, $pCommentUserRealFullName, $pCommentUserUndisclosedName, $pCommentDate, $pCommentDateInSeconds,
			$pInRoot = false, $pStartInstanceId = 0, $pStartFieldId = 0, $pEndInstanceId = 0, $pEndFieldId = 0){
	if((int)$pCommentId == (int)$pRootId && !$pInRoot){
		return false;
	}
	$lIsGeneral = true;
	$lImgSrc = '/i/general_comment_icon.png';
	if((int)$pStartInstanceId && (int)$pStartFieldId && (int)$pEndInstanceId && (int)$pEndFieldId){
		$lIsGeneral = false;
		$lImgSrc = '/i/inline_comment_icon.png';
	}
	$lResult = '
			<div class="P-Comments-Info">
				' . ($pInRoot ?
					'<div class="P-Comment-Type-Icon"><img alt="" width="19" height="27" src="' . $lImgSrc . '" /></div>'
					:
					''
				) . '
				<div class="P-Comment-User-Pic">
					' . showCommentPic($pUserPhotoId, $pIsDisclosed, $pUserRealId, $pCurrentUserId) . '
				</div>
				<div class="P-Comment-Text-Data">
					<div class="P-Comment-UserName">
						 ' . DisplayCommentUserName($pIsDisclosed, $pUserRealId, $pCurrentUserId, $pCommentUserRealFullName, $pCommentUserUndisclosedName) . '
					</div>
					<div class="P-Comment-Date">
						' . displayCommentLastModdate($pCommentId, $pCommentDate, $pCommentDateInSeconds, $pInRoot) . '
					</div>
				</div>
				<div class="P-Clear"></div>
			</div>

	';
	return $lResult;
}

function displayCommentSingleRowClass($pCommentId, $pRootId){
	if((int)$pCommentId != (int)$pRootId){
		return ' P-Comments-Single-Row-Non-Root ';
	}
}


function UploadFigurePic($pName, $pDir, $pPicId, $pDocumentId, $pPlateVal, $pPlateId,  $pTitle, $pDesc, $pPosition, &$pError) {
	global $user;
	$gMaxSize = MAX_FIGURE_PIC_FILE_SIZE; // 100 MB
	//~ $gMaxSize = 500 // Za testove;
	$extarray = array(".jpeg", ".jpg", ".gif", ".png");
	$typearr = array("image/pjpeg", "image/jpeg", "image/gif", "image/png");
	$imgUploadErr = 1;
	$lPicId = 0;
	$lData = array();
	$lCn = Con() ;
	if ( $_FILES[$pName]['name'] ) {

		$pFnUpl = $_FILES[$pName]['name'];
		$pTitle = $pFnUpl;

		$gFileExt = substr($_FILES[$pName]['name'], strrpos($_FILES[$pName]['name'], '.'));
		$isImageExtension = in_array(strtolower($gFileExt), $extarray);
		$isImageMime = in_array(strtolower($_FILES[$pName]['type']), $typearr);
		if ($isImageExtension && $isImageMime) {
			if ($_FILES[$pName]['size'] > $gMaxSize) {
				$pError = getstr('admin.articles.error_picTooBigMaxSize')  . ($gMaxSize / (1024 * 1024)). ' MB';
			} elseif (!$_FILES[$pName]["size"]) {
				$pError = getstr('admin.articles.error_wrongFile');
			} elseif ($_FILES[$pName]['error'] == UPLOAD_ERR_OK) {
				if($pPicId) {
					//~ trigger_error('SELECT * FROM pwt.spUploadFigurePhoto(2, ' . (int) $pPicId. ', ' . (int)$pDocumentId . ', ' . (!$pPlateId ? 'null' : $pPlateId) . ', \'' . q($pTitle) . '\', \'' . q($pDesc) . '\', ' . $user->id . ', \'' . q($pFnUpl) . '\', ' . $pPosition . ', ' . $pPlateVal . ') as picid', E_USER_NOTICE);
					$lResult = $lCn->Execute('SELECT * FROM pwt.spUploadFigurePhoto(2, ' . (int) $pPicId. ', ' . (int)$pDocumentId . ', ' . (!$pPlateId ? 'null' : $pPlateId) . ', \'' . q($pTitle) . '\', \'' . q($pDesc) . '\', ' . $user->id . ', \'' . q($pFnUpl) . '\', ' . $pPosition . ', ' . $pPlateVal . ')');
					//~ $lResult = $lCn->Execute('SELECT * FROM pwt.spUploadFigurePhoto(2, ' . (int) $pPicId. ', ' . (int)$pDocumentId . ', ' . (!$pPlateId ? 'null' : $pPlateId) . ', \'' . q($pTitle) . '\', \'' . q($pDesc) . '\', ' . $user->id . ', \'' . q($pFnUpl) . '\', ' . $pPosition . ', ' . $pPlateVal . ') as picid');
					DeletePicFiles( $pPicId , PATH_PWT_DL);
				} else {
					//~ if(!$pPlateId)
						//~ $pPlateId = CreatePlate((int)$pDocumentId, $pTitle, $pDesc, $pPlateVal);
						//~ trigger_error("PlateId: " . CreatePlate((int)$pDocumentId, $pTitle, $pDesc, $pPlateVal), E_USER_NOTICE);

					//~ trigger_error('SELECT * FROM pwt.spUploadFigurePhoto(1, ' . (int) $pPicId. ', ' . (int)$pDocumentId . ', ' . (!$pPlateId ? 'null' : $pPlateId) . ', \'' . q($pTitle) . '\', \'' . q($pDesc) . '\', ' . $user->id . ', \'' . q($pFnUpl) . '\', ' . $pPosition . ', ' . $pPlateVal . ') as picid', E_USER_NOTICE);
					$lResult = $lCn->Execute('SELECT * FROM pwt.spUploadFigurePhoto(1, ' . (int) $pPicId. ', ' . (int)$pDocumentId . ', ' . (!$pPlateId ? 'null' : $pPlateId) . ', \'' . q($pTitle) . '\', \'' . q($pDesc) . '\', ' . $user->id . ', \'' . q($pFnUpl) . '\', ' . $pPosition . ', ' . $pPlateVal . ')');
				}
				if( $lResult ){
					$lCn->MoveFirst();
					//~ trigger_error((int)$lCn->mRs['photo_id'] . '-' . (int)$lCn->mRs['plate_id'], E_USER_NOTICE);
					$lPicId = (int)$lCn->mRs['photo_id'];
					$lData = $lCn->mRs;
					if ($lPicId) {
						$lPref = 'oo_';
						if (!move_uploaded_file($_FILES[$pName]['tmp_name'], $pDir . $lPref . $lPicId . $gFileExt)) {
							$pError = getstr('admin.articles.error_error') . $_FILES[$pName]['error'];
						} else {
							// Vsichko e ok... pravim jpg i mahame originala
							//~ DeletePicFiles( $lPicId );
							//~ exec(escapeshellcmd("convert -colorspace rgb -quality 80 " . $pDir . $lPicId . $gFileExt . " " . $pDir . 'oo_' . $lPicId . '.jpg' ));
							exec("convert -colorspace rgb -flatten -quality 80 -thumbnail " . escapeshellarg('1024x1024>') . " " . $pDir . $lPref . $lPicId . $gFileExt . " " . $pDir . 'big_' . $lPicId . '.jpg' );
							//~ $lData['myinfo'] = "convert -colorspace rgb -quality 80 -thumbnail " . escapeshellarg('1024x1024>') . " " . $pDir . $lPref . $lPicId . $gFileExt . " " . $pDir . 'big_' . $lPicId . '.jpg';
							//~ unlink($pDir . $lPicId . $gFileExt);
							$imgUploadErr = 0;
						}
					} else {
						$pError = getstr('admin.articles.error_dbError');
					}
				}else{
					//~ var_dump($lCn->mErrMsg);
					$pError = $lCn->mErrMsg ? $lCn->mErrMsg : getstr('admin.articles.error_dbError');
				}
			} else {
				$pError = getstr('admin.articles.error_errorWhileSavingFile');
			}
		} else {
			if(!$_FILES[$pName]["size"]) {
				$pError = getstr('admin.articles.error_picTooBigMaxSize')  . ($gMaxSize / (1024 * 1024)). ' MB';
			} else {
				$pError = getstr('admin.articles.error_wrongFileFormatAllowedFormatsAre') . implode(' ', $extarray);
			}
		}
	} else {
		$pError = getstr('admin.articles.error_noFileUploaded');
	}



	if (!$imgUploadErr) {
		return $lData;
	} else {
		if ($lPicId) {//Mahame snimkata pri greshka
			$lCn->Execute('SELECT PicsUpload(3, ' . (int)$lPicId . ', null, null, null, null);');
		}
		$lData['error_msg'] = $pError;
		return $lData;
	}
}

function UploadFigurePhoto($pName, $pDir, $pDocumentId) {
	global $user;
	$gMaxSize = MAX_FIGURE_PIC_FILE_SIZE; // 100 MB
	//~ $gMaxSize = 500 // Za testove;
	$extarray = array(".jpeg", ".jpg", ".gif", ".png");
	$typearr = array("image/pjpeg", "image/jpeg", "image/gif", "image/png");
	$imgUploadErr = 1;
	$lPicId = 0;
	$lData = array();
	$lCn = Con() ;

	if ($_FILES[$pName]['name']) {
		$pFnUpl = $_FILES[$pName]['name'];
		$gFileExt = substr($_FILES[$pName]['name'], strrpos($_FILES[$pName]['name'], '.'));
		$isImageExtension = in_array(strtolower($gFileExt), $extarray);
		$isImageMime = in_array(strtolower($_FILES[$pName]['type']), $typearr);
		$lResult = array (
				'file_id' => '',
				'file_name' => '',
		);
		/*
		 $isImageExtension = in_array(strtolower($gFileExt), $extarray);
		$isImageMime = in_array(strtolower($_FILES[$name]['type']), $typearr);
		if ($isImageExtension && $isImageMime) {
		*/
		if ($isImageExtension && $isImageMime) {
			if ($_FILES[$pName]['size'] > $gMaxSize) {
				$pError = getstr('admin.articles.error_picTooBigMaxSize')  . ($gMaxSize / (1024 * 1024)). ' MB';
			} elseif (!$_FILES[$pName]["size"]) {
				$pError = getstr('admin.articles.error_wrongFile');
			} elseif ($_FILES[$pName]['error'] == UPLOAD_ERR_OK) {
				$lCn = Con() ;
				$lCn->Execute('SELECT spFileUpload(1, null, ' . (int)$user->id. ',' . (int)$pDocumentId . ', \'' . q($pFnUpl) . '\', \'' . q($pFnUpl) . '\', \'' . q(strtolower($_FILES[$pName]['type'])) . '\') as file_id');
				$lCn->MoveFirst();
				$lResult['file_id'] = (int)$lCn->mRs['file_id'];
				$lPicId = $lResult['file_id'];
				$lResult['file_name'] = $pFnUpl;
				$lPref = 'oo_';
				if ($lResult['file_id']) {
					if (!move_uploaded_file($_FILES[$pName]['tmp_name'], $pDir . $lPref . $lResult['file_id'] . $gFileExt)) {
						$pError = getstr('admin.articles.error_error') . $_FILES[$pName]['error'];
					} else {
						exec("convert -colorspace rgb -flatten -quality 80 -thumbnail " . escapeshellarg('1024x1024>') . " " . $pDir . $lPref . $lPicId . $gFileExt . " " . $pDir . 'big_' . $lPicId . '.jpg' );
						// Vsichko e ok...
						$imgUploadErr = 0;
					}
				} else {
					$pError = getstr('admin.articles.error_dbError');
				}
			} else {
				$pError = getstr('admin.articles.error_errorWhileSavingFile');
			}
		} else {
			$pError = getstr('admin.articles.error_wrongFileFormatAllowedFormatsAre') . implode(' ', $extarray);
		}
	} else {
		$pError = getstr('admin.articles.error_noFileUploaded');
	}

	$lResult['err_msg'] = $pError;
	//~ if ($picid) $lCn->Execute('SELECT AttUpload(3, ' . (int)$picid . ', null, null, null, null, null);');
	return $lResult;
}


function DeletePic($pPicId){
	$lCn = Con();
	$lCn->Execute('SELECT PicsUpload(3, ' . (int)$pPicId . ', null, null, null, null);');
	DeletePicFiles($pPicId);
}

function displayDeleteDocumentBtn($pCreateUsrId, $pDocumentId, $pIsLocked, $pDocumentState){
	global $user;
	if( (int)$user->id ==  (int)$pCreateUsrId && $pIsLocked == 0 && $pDocumentState == NEW_DOCUMENT_STATE) {
		return '<div class="P-Delete-Document" onclick="deleteDocumentById(' . (int)$pDocumentId . ', ' . (int)$_REQUEST['p'] . ')">Delete</div>';
	}
}

function displayDeleteCommentBtn($pCommentId, $pUsrId, $pVersionIsReadonly = false){
	global $user;
	if( !$pVersionIsReadonly && (int)$user->id == (int)$pUsrId )
		return '<span class="P-Delete-Comment P-Comment-Delete-Btn" onclick="deleteComment(' . (int)$pCommentId . ')">delete</span>';
}

function DeletePicFiles($pPicId, $pPath = PATH_PWT_DL){
	if( !$pPicId )
		return;
	$lFiles = glob($pPath . '*_' . $pPicId . '.*');
	foreach ($lFiles as $lFile) {
		if (is_file($lFile))
			unlink($lFile);
	}
}

function getImageDimensions($pPath, $pJSON = true) {
	if ($pJSON)
		return json_encode(getimagesize($pPath));
	return getimagesize($pPath);
}

function savePhotoTitle($pPhotoId, $pPhotoTitle) {
	$lCn = Con();
	$lCn->Execute('UPDATE pwt.media SET title = \'' . q($pPhotoTitle) . '\' WHERE id = ' . $pPhotoId);
}

function savePhotoDesc($pPhotoId, $pPhotoDesc) {
	$lCn = Con();
	$lCn->Execute('UPDATE pwt.media SET description = \'' . q($pPhotoDesc) . '\' WHERE id = ' . $pPhotoId);
}

function saveTableData($pDocId, $pTableTitle, $pTableDesc, $pTableId = 0) {
	global $user;
	$lCn = Con();
	if((int)$pTableId) {
		$lCn->Execute('SELECT tableid, move_position FROM pwt.spSaveTableData(2, ' . (int)$pTableId . ',' . (int)$pDocId . ', \'' . q($pTableTitle) . '\', \'' . q($pTableDesc) . '\', ' . (int)$user->id . ');');
		$lCn->MoveFirst();
		$lResult['tableid'] = false;
		$lResult['move_position'] = (int)$lCn->mRs['move_position'];
		$lResult['table_title'] = $pTableTitle;
	} else {
		$lCn->Execute('SELECT tableid, move_position FROM pwt.spSaveTableData(1, 0, ' . (int)$pDocId . ', \'' . q($pTableTitle) . '\', \'' . q($pTableDesc) . '\', ' . (int)$user->id . ');');
		$lCn->MoveFirst();
		$lResult['tableid'] = (int)$lCn->mRs['tableid'];
		$lResult['move_position'] = (int)$lCn->mRs['move_position'];
		$lResult['table_title'] = $pTableTitle;
	}
	return $lResult;
}

function updatePlateVal($pDocId, $pPlateId, $pPlateType) {
	global $user;
	$lCn = Con();
	$lCn->Execute('UPDATE pwt.plates SET format_type = ' . (int)$pPlateType . ' WHERE id = ' . (int)$pPlateId . ' AND document_id = ' . (int)$pDocId . ' AND usr_id = ' . (int)$user->id );
}

function savePlateDetails($pPlateDesc, $pPlateId) {
	global $user;
	$lCn = Con();
	$lCn->Execute('UPDATE pwt.plates SET description = \'' . q($pPlateDesc) . '\' WHERE id = ' . (int)$pPlateId . ' AND usr_id = ' . (int)$user->id );
}

function saveVideoDetails($pVideoId, $pVideoUrl, $pVideoTitle, $pDocumentId) {
	global $user;
	$lCn = Con();
	$lCn->Execute('SELECT * FROM pwt.spSaveVideoUrl(' . ((int)$pVideoId ? '2' : '1') . ', ' . (int)$pVideoId . ' ,' . (int)$pDocumentId . ', \'' . q($pVideoUrl) . '\'::varchar, \'' . q($pVideoTitle) . '\'::varchar,' . (int)$user->id . ', 2, null);');
	$lCn->MoveFirst();
	return (int)$lCn->mRs['video_id'];
}

function AddPlatesIfEmpty($pRecords, $pDocId, $pPlateType) {
	$lPlates = 0;
	if($pPlateType == 1 || $pPlateType == 2)
		$lPlates = 2;
	elseif($pPlateType == 3)
		$lPlates = 4;
	elseif($pPlateType == 4)
		$lPlates = 6;
	if ($pRecords < $lPlates) {
		$lAddRows = $lPlates - $pRecords;

		for ($i = 0; $i < $lAddRows; $i++) {
			$lFigForm = new csimple(
				array(
					'ctype' => 'csimple',
					'document_id' => $pDocId,
					'plate_val' => $pPlateType,
					'holder_id' => $i+$pRecords+1,
					'curr_holder_id' => $i+$lAddRows+1,
					'next_holder_id' => $i+$lAddRows+2,
					'templs' => array(
						G_DEFAULT => 'figures.empty_plate_holder_' . $pPlateType,
					),
				)
			);
			$lResult .= $lFigForm->Display();
		}
		return $lResult;
	}
	return '';
}

function ClearRowFloat($pRowId, $pType) {
	if($pType == 3) {
		if ($pRowId % 2 == 1)
			return '<div class="P-Clear"></div>';
	}
}

function showFiguresPhotoLink($pPhotoId) {
	if((int)$pPhotoId)
		return '<img id="uploaded_photo" src="/showfigure.php?filename=big_' . (int)$pPhotoId . '.jpg" alt="" />';
	return '';
}

function getYouTubeIdFromURL($pUrl) {
  $lPattern = '/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/ ]{11})/i';
  preg_match($lPattern, $pUrl, $matches);

  return isset($matches[1]) ? $matches[1] : false;
}

function showPlatePhotoSrc($pPlateId, $pPhotoId, $pPhotoPref, $pPlatePhotosArr = false, $pPlateFormatType = 1, $pPlatePhotosPositions = false, $pFileType = 0, $pVideoLink = false, $pAddCitation = 0) {
	if($pFileType == 2) { //video
		$lVideoId = getYouTubeIdFromURL($pVideoLink);
		if($lVideoId) {
			$lVideo = '<img id="' . $lVideoId . '" class="youtube_' . $lVideoId . ' youtubeVideoThumbnail" style="cursor: pointer;" title="YouTube Thumbnail Test" src="http://img.youtube.com/vi/' . $lVideoId . '/1.jpg" width="90px" height="82px" alt="" />';
			if(!(int)$pAddCitation){
				$lVideo .= '
					<script type="text/javascript">
						 $(function () {
							$("img.youtube_' . $lVideoId . '").YouTubePopup({ idAttribute: "id", clickOutsideClose: true });
						});
					</script>';
			}
			return $lVideo;
		} else {
			return '';
		}
	} elseif((int)$pPlateId) {
		if($pPlatePhotosArr != false)
			$pPlatePhotosArr = postgresToPhpArray($pPlatePhotosArr);
			$pPlatePhotosPositions = postgresToPhpArray($pPlatePhotosPositions);
			$lCombinePlateFigures = array_combine((array)$pPlatePhotosArr, (array)$pPlatePhotosPositions);
			asort($lCombinePlateFigures);
		return showPlatePhotosPreviewByFormatType($pPlateFormatType, $lCombinePlateFigures);
	} elseif((int)$pPhotoId) {
		return '<img style="float: left;" src="/showfigure.php?filename=' . $pPhotoPref . '_' . $pPhotoId . '.jpg&' . rand(1, 100) . '" alt="" />';
	}
	return '';
}

function showPlatePhotosPreviewByFormatType( $pFormatType, $pPhotosArr ){
	$lRet = '';

	switch($pFormatType){
		case 1:
			foreach(array_keys($pPhotosArr) as $photoid){
				$lRet .= '
					<img style="float: left;" src="/showfigure.php?filename=c90x41y_' . $photoid . '.jpg&' . rand(1, 100) . '" alt="" />';
			}
			break;
		case 2:
			foreach(array_keys($pPhotosArr) as $photoid){
				$lRet .= '<img style="float: left;" src="/showfigure.php?filename=c45x82y_' . $photoid . '.jpg&' . rand(1, 100) . '" alt="" />';
			}
			break;
		case 3:
			foreach(array_keys($pPhotosArr) as $photoid){
				$lRet .= '<img style="float: left;" src="/showfigure.php?filename=c45x41y_' . $photoid . '.jpg&' . rand(1, 100) . '" alt="" />';
			}
			break;
		case 4:
			foreach(array_keys($pPhotosArr) as $photoid){
				$lRet .= '<img style="float: left;" src="/showfigure.php?filename=c45x27y_' . $photoid . '.jpg&' . rand(1, 100) . '" alt="" />';
			}
			break;
		default:
			break;
	}

	return $lRet;
}

function showPlatePhotoTitle($pPlateTitle, $pPhotoTitle) {
	if($pPlateTitle) {
		//~ return $pPlateTitle;
	} elseif($pPhotoTitle) {
		return $pPhotoTitle;
	}
	return '';
}

function showPlatePhotoDesc($pPlateDesc, $pPhotoDesc) {
	if($pPlateDesc) {
		return $pPlateDesc;
	} elseif($pPhotoDesc) {
		return $pPhotoDesc;
	}
	return '';
}

function showPlatePhotoText($pPlateId) {
	if((int)$pPlateId)
		return 'Plate';
	return 'Figure';
}

function showPlatePhotoVal($pPlateId, $pPhotoId) {
	if((int)$pPlateId) {
		return (int)$pPlateId;
	} elseif((int)$pPhotoId) {
		return (int)$pPhotoId;
	}

}

function checkFigureType($pPhotoId, $pPlateId){
	if($pPhotoId)
		return 0;
	if($pPlateId)
		return 1;
}

function postgresToPhpArray($pPostgresArray){
	$lPostgresStr = trim($pPostgresArray,'{}');
	$lRetArr = explode(',',$lPostgresStr);
	return $lRetArr;
}

function displayPlatePhotos($pPlateId, $pFormatType, $pPhotoIdsArr, $pPhotoPositionsArr){
	$lPlateTemplatesArr = array(
		1 => 'a', 2 => 'b', 3 => 'c',
		4 => 'd', 5 => 'e', 6 => 'f'
	);

	$lRet = '';
	$pPhotoIdsArr = postgresToPhpArray($pPhotoIdsArr);
	$pPhotoPositionsArr = postgresToPhpArray($pPhotoPositionsArr);
	// Подреждаме ги за да може като печатаме да излизат подредени - "а b c" не "а c d"
	for($i = 0; $i < count($pPhotoIdsArr); $i++ ){
		$lPlatePhotos[$pPhotoPositionsArr[$i]] = $pPhotoIdsArr[$i];
	}
	ksort($lPlatePhotos);
	if( $pPlateId ){
		$lRet .= '<div class="P-Figure-InsertOnly"><div class="P-Figure-InsertOnly-Checkbox">' . getstr('pwt.figures.insertOnly') . ': </div>';
		for($i = 1; $i <= count($lPlatePhotos); $i++ ){
			$lRet .= '<div class="P-Figure-InsertOnly-Checkbox"><div><input type="checkbox" onclick="checkSiblingsIsChecked(this)" name="fig-' .  $lPlatePhotos[key($lPlatePhotos)] . '" figurenum="' . key($lPlatePhotos) . '" value="' .  $lPlatePhotos[key($lPlatePhotos)] . '"></input></div>' . $lPlateTemplatesArr[key($lPlatePhotos)] . ' </div>';
			next($lPlatePhotos);
		}
		$lRet .= '</div>';
	}
	return $lRet;
}

function showEditFigureAction($pPlateId, $pPhotoId, $pDocumentId, $pType) {
	if((int)$pType == YOU_TUBE_VIDEO_TYPE){
		return 'ChangeFiguresForm( \'video\', ' . (int)$pDocumentId . ', \'P-PopUp-Content-Inner\', 0, 2, ' . (int)$pPhotoId . ');popUp(POPUP_OPERS.open, \'add-figure-popup\', \'add-figure-popup\');';
	}
	if($pPlateId) {
		return 'ChangeFiguresForm( \'plate\', ' . (int)$pDocumentId . ', \'P-PopUp-Content-Inner\', 0, 1, 0, ' . (int)$pPlateId . ');popUp(POPUP_OPERS.open, \'add-figure-popup\', \'add-figure-popup\');';
	} elseif($pPhotoId) {
		return 'ChangeFiguresForm( \'image\', ' . (int)$pDocumentId . ', \'P-PopUp-Content-Inner\', 0, 2, ' . (int)$pPhotoId . ');popUp(POPUP_OPERS.open, \'add-figure-popup\', \'add-figure-popup\');';
	}
}

function showDeletePlatePhotoAction($pPlateId, $pPhotoId, $pDocumentId) {
	if($pPlateId) {
		return 'DeleteFigure(' . (int)$pDocumentId . ', ' . (int)$pPlateId . ', null);';
	} elseif($pPhotoId) {
		return 'DeleteFigure(' . (int)$pDocumentId . ', null, ' . (int)$pPhotoId . ');';
	}
}

function showUpDownMoveTableArrows($pRows, $pCurrRow, $pDocId, $pPosition, $pFlag) {
	$lHasUpBtn = false;
	$lHasDownBtn = false;
	$lRes = '';
	if ($pPosition == 1 && $pRows == 1) {
		return $lRes;
	} elseif(($pRows == $pCurrRow && $pCurrRow != 1) || ($pRows == $pCurrRow && $pFlag == 'false')) {
		$lHasUpBtn = true;
	} elseif($pCurrRow == 1 && $pPosition == 1) {
		$lHasDownBtn = true;
	}else{
		$lHasUpBtn = true;
		$lHasDownBtn = true;
	}

	if($lHasUpBtn){
		$lRes .=  '<div class="section_arrow_up" onclick="MoveTableUpDown(this, ' . (int)$pDocId . ', ' . (int)$pPosition . ', 1);"></div>';
	}
	if($lHasDownBtn){
		$lRes .= '<div class="section_arrow_down" onclick="MoveTableUpDown(this, ' . (int)$pDocId . ', ' . (int)$pPosition . ', 2);"></div>';
	}
	return $lRes;
}

function showUpDownMoveTablePositionArrows($pDocId, $pCurrPosition, $pMaxPosition, $pMinPosition) {
	$lHasUpBtn = false;
	$lHasDownBtn = false;
	$lRes = '';
	if($pCurrPosition < $pMaxPosition) {
		$lHasDownBtn = true;
	}
	if($pCurrPosition > $pMinPosition) {
		$lHasUpBtn = true;
	}

	if($lHasUpBtn){
		$lRes .= '<div class="section_arrow_up" onclick="MoveTableUpDown(this, ' . (int)$pDocId . ', ' . (int)$pCurrPosition . ', 1);"></div>';
	}
	if($lHasDownBtn){
		$lRes .= '<div class="section_arrow_down" onclick="MoveTableUpDown(this, ' . (int)$pDocId . ', ' . (int)$pCurrPosition . ', 2);"></div>';
	}

	return $lRes;
}

function showUpDownMoveFigureArrows($pRowCount, $pCurrRow, $pDocId, $pPlateId, $pPhotoId, $pPosition, $pFlag) {
	$lItemId = (int)$pPlateId ? (int)$pPlateId : (int)$pPhotoId;
	$lIsPlate = (int)$pPlateId ? 1 : 0;
	$lHasUpBtn = false;
	$lHasDownBtn = false;
	$lRes = '';
	if ($pPosition == 1 && $pRowCount == 1) {
		return $lRes;
	} elseif(($pRowCount == $pCurrRow && $pCurrRow != 1) || ($pRowCount == $pCurrRow && $pFlag == 'false')) {
		$lHasUpBtn = true;
	} elseif($pCurrRow == 1 && $pPosition == 1) {
		$lHasDownBtn = true;
	} else{
		$lHasUpBtn = true;
		$lHasDownBtn = true;
	}

	if($lHasUpBtn){
		$lRes .= '<div class="section_arrow_up" onclick="MoveFigureUpDown(this, ' . (int)$lItemId . ', ' . (int)$pDocId . ', ' . (int)$pPosition . ', 1, ' . (int)$lIsPlate . ');"></div>';
	}
	if($lHasDownBtn){
		$lRes .= '<div class="section_arrow_down" onclick="MoveFigureUpDown(this, ' . (int)$lItemId . ', ' . (int)$pDocId . ', ' . (int)$pPosition . ', 2, ' . (int)$lIsPlate . ');"></div>';;
	}
	return $lRes;
}

function showUpDownMoveFigurePositionArrows($pDocId, $pPlateId, $pPhotoId, $pCurrPosition, $pMaxPosition, $pMinPosition) {
	$lItemId = (int)$pPlateId ? (int)$pPlateId : (int)$pPhotoId;
	$lIsPlate = (int)$pPlateId ? 1 : 0;
	$lHasUpBtn = false;
	$lHasDownBtn = false;
	$lRes = '';
	if($pCurrPosition < $pMaxPosition) {
		$lHasDownBtn = true;
	}
	if($pCurrPosition > $pMinPosition) {
		$lHasUpBtn = true;
	}

	if($lHasUpBtn){
		$lRes .= '<div class="section_arrow_up" onclick="MoveFigureUpDown(this, ' . (int)$lItemId . ', ' . (int)$pDocId . ', ' . (int)$pCurrPosition . ', 1, ' . (int)$lIsPlate . ');"></div>';
	}
	if($lHasDownBtn){
		$lRes .= '<div class="section_arrow_down" onclick="MoveFigureUpDown(this, ' . (int)$lItemId . ', ' . (int)$pDocId . ', ' . (int)$pCurrPosition . ', 2, ' . (int)$lIsPlate . ');"></div>';;
	}
	return $lRes;
}

function AddChangePhotoBtnText($pPhotoId) {
	if($pPhotoId)
		return getstr('pwt.figures.changephotobtn');
	return getstr('pwt.figures.addphotobtn');
}

function EditFigurePhotoBtnText($pPhotoId, $pType) {
	if($pType == YOU_TUBE_VIDEO_TYPE)
		return getstr('pwt.figures.editvideobtn');
	if($pPhotoId)
		return getstr('pwt.figures.editfigurebtn');
	return getstr('pwt.figures.editplatebtn');
}

function getFigurePhotoDescription($pPhotoId, $pPlateId) {
	if($pPhotoId) {
		global $user;
		$lCn = Con();
		$lCn->Execute('SELECT description FROM pwt.media WHERE id = ' . (int)$pPhotoId);
		$lCn->MoveFirst();
		return $lCn->mRs['description'];
	} elseif ($pPlateId) {
		global $user;
		$lCn = Con();
		$lCn->Execute('SELECT description FROM pwt.plates WHERE id = ' . (int)$pPlateId);
		$lCn->MoveFirst();
		return $lCn->mRs['description'];
	}
}

function getVideoLink($pId){
	if((int)$pId){
		$lCn = Con();
		$lCn->Execute('SELECT link FROM pwt.media WHERE id = ' . (int)$pId);
		$lCn->MoveFirst();
		return $lCn->mRs['link'];
	}
	return '';
}

function getVideoTitle($pId){
	if((int)$pId){
		$lCn = Con();
		$lCn->Execute('SELECT description FROM pwt.media WHERE id = ' . (int)$pId);
		$lCn->MoveFirst();
		return $lCn->mRs['description'];
	}
	return '';
}

function getPlateTypeById($pPlateId) {
	if($pPlateId) {
		global $user;
		$lCn = Con();
		$lCn->Execute('SELECT format_type FROM pwt.plates WHERE id = ' . (int)$pPlateId);
		$lCn->MoveFirst();
		return $lCn->mRs['format_type'];
	}
	return 1;
}

function MoveFigure($pDirection, $pDocId, $pPhotoId, $pPosition, $pPlateFlag) {
	if($pPhotoId && $pDocId && $pDirection && $pPosition) {
		global $user;
		$lCn = Con();
		$lCn->Execute('SELECT * FROM pwt.spMoveFigure(' . (int)$pDirection . ', ' . (int)$pDocId . ', ' . (int)$pPhotoId . ', ' . (int)$pPosition . ', ' . (int)$pPlateFlag . ')');
		$lCn->MoveFirst();
		return $lCn->mRs;
	}
	return 0;
}

function MoveTable($pDirection, $pDocId, $pTableId) {
	if($pTableId && $pDocId && $pDirection) {
		global $user;
		$lCn = Con();
		$lCn->Execute('SELECT * FROM pwt.spMoveTable(' . (int)$pDirection . ', ' . (int)$pDocId . ', ' . (int)$pTableId . ')');
		$lCn->MoveFirst();
		return $lCn->mRs;
	}
	return 0;
}

function DeleteFigure($pDocId, $pPlateId, $pPhotoId) {
	if($pPlateId && $pDocId) {
		global $user;
		$lCn = Con();
		$lCn->Execute('SELECT * FROM pwt.spDeleteFigure(' . (int)$pDocId . ', ' . (int)$pPlateId . ', null)');
		$lCn->MoveFirst();
		return $lCn->mRs;
	} elseif($pPhotoId && $pDocId) {
		global $user;
		$lCn = Con();
		$lCn->Execute('SELECT * FROM pwt.spDeleteFigure(' . (int)$pDocId . ', null, ' . (int)$pPhotoId . ')');
		$lCn->MoveFirst();
		return $lCn->mRs;
	}
	return 0;
}

function DeleteTable($pDocId, $pTableId) {
	if($pTableId && $pDocId) {
		global $user;
		$lCn = Con();
		$lCn->Execute('SELECT result, move_position, max_position, min_position FROM pwt.spDeleteTable(' . (int)$pDocId . ', ' . (int)$pTableId . ', ' . (int)$user->id . ');');
		$lCn->MoveFirst();
		//var_dump($lResult);
		$lResult['result'] = $lCn->mRs['result'];
		$lResult['move_position'] = $lCn->mRs['move_position'];
		$lResult['max_position'] = $lCn->mRs['max_position'];
		$lResult['min_position'] = $lCn->mRs['min_position'];

		if( $lResult['result'] ){
			$lUpdatedTablesPos = new crs(
				array('ctype'=>'crs',
					'templs'=>array(
						G_HEADER=>'global.empty',
						G_ROWTEMPL=>'tables.document_tables_row',
						G_FOOTER =>'global.empty',
						G_NODATA =>'global.empty',
					),
					'max_position' => $lResult['max_position'],
					'min_position' => $lResult['min_position'],
					'sqlstr'=>'
						SELECT *
						FROM pwt.tables
						WHERE document_id = ' . (int)$pDocId . ' AND move_position >=' . (int)$lResult['move_position'] . '
						ORDER BY move_position ASC
					',
					'document_id' => (int)$pDocId,
				)
			);
			$lUpdatedTablesPos->GetData();
			$lResult['updated_tables'] = $lUpdatedTablesPos->Display();
		}
		return $lResult;
	}
	return 0;
}

function showResizeStyleIfPicExists($pPhotoId, $pPhotoBorder = false) {
	if($pPhotoId) {
		$lDims = getImageDimensions(PATH_PWT_DL . 'oo_' . $pPhotoId . '.jpg', 0);
		if($lDims) {
			if($pPhotoBorder) // Това е за бордъра на снимката
				return ' style="height:' . ((int)$lDims[1] + 20) . 'px; width:' . ((int)$lDims[0] + 20) . 'px"';
			return ' style="height:' . $lDims[1] . 'px; width:' . $lDims[0] . 'px"';
		}
	}
	return '';
}

function checkPlateVal($pPlateId, $pVal) {
	if($pPlateId) {
		if(getPlateTypeById($pPlateId) == $pVal)
			return ' checked="checked" ';
	} elseif($pVal == 1) {
		return ' checked="checked" ';
	}
}

function displayFiguresTablesActiveClass($pFile) {
	if($_SERVER['PHP_SELF'] == $pFile)
		return ' P-Article-Active';
}

function displayMarkForDeleteClass($pMarkForDelete){
	if($pMarkForDelete)
		return ' P-With-Close-Icon';
}

function displayMarkForDeleteIcon($pMarkForDelete){
	if($pMarkForDelete)
		return ' <div class="P-Close-Icon"></div>';
}

/**
 * Returns the id of the latest revision of the document
 * @param unknown_type $pDocumentId
 */
function getDocumentLatestRevisionId($pDocumentId){
	$lCon = new DBCn();
	$lCon->Open();
	$lSql = 'SELECT max(id) as id
		FROM pwt.document_revisions
		WHERE document_id = ' . (int)$pDocumentId . '
	';
	$lCon->Execute($lSql);
	return $lCon->mRs['id'];
}

/**
 * Checks whether the current user can edit
 * the preview of the specified version. If no revisionid is specified
 * the latest revision of the document is assumed
 * @param unknown_type $pDocumentId
 * @param unknown_type $pRevisionId
 * @param unknown_type $pUid
 */
function checkIfPreviewCanBeEdited($pDocumentId, $pRevisionId = false){
	$lLatestRevisionId = getDocumentLatestRevisionId($pDocumentId);
	if((int)$pRevisionId && $pRevisionId != $lLatestRevisionId){
		//The previous revisions are
// 		var_dump($pRevisionId);
		return false;
	}
	//We are checking the latest revision

	//Check if the document is in a readonly state
	$lCon = new DBCn();
	$lCon->Open();
	$lSql = 'SELECT s.is_readonly::int as is_readonly
		FROM pwt.document_states s
		JOIN pwt.documents d ON d.state = s.id
		WHERE d.id = ' . (int)$pDocumentId . '
	';
	$lCon->Execute($lSql);
// 	var_dump(2);
	if((int)$lCon->mRs['is_readonly']){
		return false;
	}
	//Now check if the document can be locked by the current user
	return lockDocument($pDocumentId);
}

function getDocumentPreview($pDocumentId, $pGenerateFullHtml = 0, $pTemplateXSLPath = '', $pXml = '', $pMarkContentEditableFields = false, $pRevisionPreview = false, $pPutEditableJSAndCss = false, $pInsertCommentPositionNodes = true, $pTrackFigureChanges = false, $pPDFPreviewMode = 0){
// 	$docroot = getenv('DOCUMENT_ROOT');
// 	require_once($docroot . '/lib/static_xsl_pmt.php');
// 	$lPmtXml = getDocumentXml($pDocumentId);
// 	$lHtml = transformXmlWithXsl($lPmtXml, PATH_PMT_XSL . '/html_new.xsl');
// 	return GetHtmlBallons($lHtml);

// 	error_reporting(-1);
	$docroot = getenv('DOCUMENT_ROOT');
	require_once($docroot . '/lib/static_xsl.php');
	require_once(PATH_CLASSES . 'comments.php');
	// ini_set('display_errors', 'Off');
	//Първо получаваме нашия xml
	$lDocumentXml = $pXml;
// 	$lStart = mktime(). substr((string)microtime(), 1, 6);

// 	trigger_error('Start prev ' .  date("Y/m/d H:i:s"). substr((string)microtime(), 1, 6), E_USER_NOTICE);

	if($lDocumentXml == ''){
// 		$pExplicitGeneration = false, $pPrepareCitations = false, $pInstanceId = 0, $pInsertDocumentComments = false, $pInsertCommentPositions = false
		$lDocumentXml = getDocumentXml($pDocumentId, SERIALIZE_INTERNAL_MODE, 1, 1, 0, false, $pInsertCommentPositionNodes);
	}else{
		if($pInsertCommentPositionNodes){
			$lDocumentComments = GetDocumentComments($pDocumentId);
		// 	var_dump($lDocumentComments);
			$lDocumentXml = InsertDocumentCommentPositionNodes($lDocumentXml, $lDocumentComments);
		}
		$lDocumentXml = prepareDocumentCitations($pDocumentId, $lDocumentXml, null, null);		
	}
// 	$lEnd = mktime(). substr((string)microtime(), 1, 6);
// 	trigger_error('END XML Time ' .  ($lEnd - $lStart), E_USER_NOTICE);
//	var_dump($lDocumentXml);
 	//~ var_dump($lDocumentXml);
	$lXslParameters = array();
	$lXslParameters[] = array(
		'namespace' => null,
		'name' => 'pEditableHeaderReplacementText',
		'value' => PREVIEW_EDITABLE_HEADER_REPLACEMENT_TEXT,
	);

	if($pTemplateXSLPath) {
		$pTemplateXSLPath .=  '/';
	}
	$lXslPath = PATH_XSL . '' . $pTemplateXSLPath . 'template_example_preview_full.xsl';
// 	if(!(int)$pGenerateFullHtml){
// 		$lXslParameters[] = array(
// 			'namespace' => null,
// 			'name' => 'gGenerateFullHtml',
// 			'value' => 0,
// 		);

// 		$lXslPath = PATH_XSL . '' . $pTemplateXSLPath . '/template_example_preview_fragment.xsl';
// 	}

	if((int)$pMarkContentEditableFields){
		$lXslParameters[] = array(
			'namespace' => null,
			'name' => 'pMarkContentEditableFields',
			'value' => 1,
		);
	}


	if((int)$pPutEditableJSAndCss){
		$lXslParameters[] = array(
			'namespace' => null,
			'name' => 'pPutEditableJSAndCss',
			'value' => 1,
		);
	}

	if((int)$pTrackFigureChanges){
		$lXslParameters [] = array (
			'namespace' => null,
			'name' => 'pTrackFigureAndTableChanges',
			'value' => 1
		);
	}

	if((int)$pRevisionPreview){
		$lXslParameters[] = array(
			'namespace' => null,
			'name' => 'pShowPreviewCommentTip',
			'value' => 0,
		);
	}

	$lXslParameters[] = array(
		'namespace' => null,
		'name' => 'pDocumentId',
		'value' => $pDocumentId,
	);

	if((int)$pPDFPreviewMode){
		$lXslParameters[] = array(
			'namespace' => null,
			'name' => 'pPDFPreviewMode',
			'value' => 1,
		);
	}

// 	error_reporting(-1);
// 	 echo  $lDocumentXml;
	//~ exit;
// 	$lEnd = mktime(). substr((string)microtime(), 1, 6);
// 	trigger_error('Before xsl Time ' .  ($lEnd - $lStart), E_USER_NOTICE);
	$lHtml = transformXmlWithXsl($lDocumentXml, $lXslPath, $lXslParameters);
// 	$lEnd = mktime(). substr((string)microtime(), 1, 6);
// 	trigger_error('After xsl Time ' .  ($lEnd - $lStart), E_USER_NOTICE);
// 	return $lHtml;
// 	error_reporting(0)
// 	var_dump($lHtml);
// 	file_put_contents('/tmp/preview_' . $pDocumentId . '.html', $lHtml);

	$lDomHtml = new DOMDocument('1.0', DEFAULT_XML_ENCODING);
	$lDomHtml->loadHTML($lHtml);
	$lDomHtml->normalizeDocument();
	$lDomHtml->preserveWhiteSpace = false;
	$lDomHtml->formatOutput = false;
// 	$lEnd = mktime(). substr((string)microtime(), 1, 6);
// 	trigger_error('After load Time ' .  ($lEnd - $lStart), E_USER_NOTICE);

	// позициониране на фигурите
	$lHtml = posCitations($lDomHtml, $lHtml, $pDocumentId, 'figure_position', 'fig-citation', 'fignumber');
// 	$lEnd = mktime(). substr((string)microtime(), 1, 6);
// 	trigger_error('After fig cit Time ' .  ($lEnd - $lStart), E_USER_NOTICE);

	// позициониране на таблиците
	$lHtml = posCitations($lDomHtml, $lHtml, $pDocumentId, 'table_position', 'tbls-citation', 'tblnumber');
// 	$lEnd = mktime(). substr((string)microtime(), 1, 6);
// 	trigger_error('After tbl cit Time ' .  ($lEnd - $lStart), E_USER_NOTICE);

	if(!$pGenerateFullHtml){
		$lXPath = new DOMXPath($lDomHtml);
		$lNode = $lXPath->query('//div[@class="P-Article-Preview"]');
		if($lNode->length){
			return $lDomHtml->saveHTML($lNode->item(0));
		}
	}
	return $lHtml;
}

function updateDocumentPreviewCash( $pDocumentId, $pTemplateXSLPath){
	if((int)$pDocumentId){
		$lDocumentPreviewHtml = getDocumentPreview( (int)$pDocumentId, 0, $pTemplateXSLPath);
		$lSql = 'UPDATE pwt.documents SET doc_html = \'' . q($lDocumentPreviewHtml) . '\', generated_doc_html = 1 WHERE id = ' . (int)$pDocumentId;
		$lCon = new DBCn;
		$lCon->Open();
		if( $lCon->Execute($lSql) ){
			$lCon->Close();
			return true;
		}else
			return false;
	}
	return false;
}

/*
	Изкачваме се нагоре по нодовете, докато не намерим
	подаден в $pNodeName име на node-a
*/
function upToParentNodeByTag($pNode, $pNodeName, $pEndNodeSearch = 'body') {
	$pNode = $pNode->parentNode;
	while ($pNode->nodeName != $pNodeName) {
		if($pNode->nodeName == $pEndNodeSearch)
			return null;
		$pNode = $pNode->parentNode;
	}
	return $pNode;
}

/*
	Изкачваме се нагоре по нодовете, докато не намерим
	подаден в $pAttributeName атрибут и $pNodeName име на node-a
*/
function upToParentNodeByNameAndAttribute($pNode, $pNodeName, $pAttributeName, $pEndNodeSearch = 'body') {
	$pNode = $pNode->parentNode;
	while (!$pNode->hasAttribute($pAttributeName) || $pNode->nodeName != $pNodeName) {
		if($pNode->nodeName == $pEndNodeSearch)
			return null;
		$pNode = $pNode->parentNode;
	}
	return $pNode;
}

/*
	Проверява дали има следващ обект в документа (фигура или таблица)
*/
function getFollowingFigureNode($pFigNum, $pXPath, $pDomDoc, $pObjectCitt = 'fig-citation', $pObjectNumber = 'fignumber') {
	$lFollNode = $pXPath->query('//' . $pObjectCitt . '/xref[@' . $pObjectNumber . '=' . $pFigNum . ']/following::xref[@' . $pObjectNumber . ']', $pDomDoc);
	return (int)$lFollNode->length;
}

/*
	Позиционира div-a, който съдържа обекта на правилното място
	Намира се къде точно е разположен в документа по подаден номер $pFigNum,
	премахва го и го слага след подаден $pPNodeToMove
	$pDivObjectAttr е атрибута на div-a, по който ще се ориентираме дали е фигура или таблица
*/
function moveObjectToCitationPos($pDomDoc, $pFigNum, $pNodeAfterWhichToMoveElement, $pDivObjectAttr = 'figure_position', $pIdentKeyNode, $pNode) {
	$lXpath = new DOMXPath($pDomDoc);
	$lFigureNode = $lXpath->query('//div[@' . $pDivObjectAttr . '=' . (int)$pFigNum . ']', $pDomDoc);
	if ((int)$lFigureNode->length) {
		foreach ($lFigureNode as $figurenode) {
			if($pIdentKeyNode) { // тук влизаме, ако фигурата е в identification_key
				$lTableRow = upToParentNodeByNameAndAttribute($pNode, 'tr', 'instance_id');
				$lNodeToInsertBefore = $lTableRow->nextSibling->nextSibling;
				if(!$lTableRow){
					$lTableRow = upToParentNodeByTag($pNode, 'tr');
					$lNodeToInsertBefore = $lTableRow->nextSibling;
				}
				if(!$lTableRow){
					return;
				}
				$tr = $pDomDoc->createElement('tr');
				$td = $pDomDoc->createElement('td');
				$td->setAttribute('colspan', '3');
				$td->setAttribute('class', 'P-Article-Preview-Table-Row');
				$td->appendChild($figurenode);
				$tr->appendChild($td);
				if($lNodeToInsertBefore){
					$lTableRow->parentNode->insertBefore($tr, $lNodeToInsertBefore);
				}else{
					$lTableRow->parentNode->appendChild($tr);
				}

			} else {
				if(!$pNodeAfterWhichToMoveElement){
					return;
				}
				$lFigureParentNode = $figurenode->parentNode;

				// махаме фигурата от документа
				$lFigureParentNode->removeChild($figurenode);
// 				$pPNodeToMove->insertBefore($figurenode);
				// поставяме фигурата на правилното място
				$lCitationElementsWrapper = null;

				if($pNodeAfterWhichToMoveElement->nextSibling && $pNodeAfterWhichToMoveElement->nextSibling->nodeName == CITATION_ELEMENT_CITATION_WRAPPER_NODE_NAME){
					$lCitationElementsWrapper = $pNodeAfterWhichToMoveElement->nextSibling;
// 					var_dump($pFigNum);
// 					var_dump($figurenode->ownerDocument->SaveXML($figurenode));
// 					var_dump($figurenode->ownerDocument->SaveXML($pPNodeToMove->nextSibling));
// 					$pPNodeToMove->parentNode->insertBefore($figurenode, $pPNodeToMove->nextSibling);
// 					var_dump($pPNodeToMove->ownerDocument->SaveXML($pPNodeToMove->parentNode));
				}else{
// 					$pPNodeToMove->insertBefore($figurenode);
// 					$pPNodeToMove->parentNode->appendChild($figurenode);
// 					if(!$pNodeAfterWhichToMoveElement || !$pNodeAfterWhichToMoveElement->ownerDocument){
// 						var_dump($pNodeAfterWhichToMoveElement, $pFigNum);
// 						exit;
// 					}
					$lCitationElementsWrapper = $pNodeAfterWhichToMoveElement->ownerDocument->createElement(CITATION_ELEMENT_CITATION_WRAPPER_NODE_NAME);
					if($pNodeAfterWhichToMoveElement->nextSibling){
						$lCitationElementsWrapper = $pNodeAfterWhichToMoveElement->parentNode->insertBefore($lCitationElementsWrapper, $pNodeAfterWhichToMoveElement->nextSibling);
					}else{
						$lCitationElementsWrapper = $pNodeAfterWhichToMoveElement->parentNode->appendChild($lCitationElementsWrapper);
					}
				}
				if($lCitationElementsWrapper){
					$lCitationElementsWrapper->appendChild($figurenode);
				}

			}
		}
	} else {
		return ;
	}
}

/*
	Позиционира фигурите и таблиците на мястото, където са цитирани според алгоритъма
*/
function posCitations($pDomDoc, $pHtml, $pDocumentId, $pPositionAttr = 'figure_position', $pObjectCitt = 'fig-citation', $pObjectNum = 'fignumber') {
	$lXpath = new DOMXPath($pDomDoc);

	// Xpath за взизмане на всички цитирани фигури/таблици в документа
	$lCittFiguresQuery = '//' . $pObjectCitt . '/xref[@' . $pObjectNum . ']';

	// Xpath за взизмане на всички div-ове съдържащи фигури/таблици в документа
	$lFiguresQuery = '//div[@' . $pPositionAttr . ']';

    // Взимаме цитираните фигури/таблици
	$lNodesList = $lXpath->query($lCittFiguresQuery, $pDomDoc);

	// Взимаме всички фигури/таблици
    $lAllFiguresNodes = $lXpath->query($lFiguresQuery, $pDomDoc);

	$lAllFiguresArr = array();
	foreach($lAllFiguresNodes as $fignode) {
		if ($fignode->hasAttribute($pPositionAttr)) {
			$lAllFiguresArr[] = (int)$fignode->getAttribute($pPositionAttr);
		}
	}

	// Ако няма цитирани фигури си връщаме генерирания html с обектите в края
	if (!$lNodesList->length || !count($lAllFiguresArr)){
		return $pHtml;
	}

	foreach ($lNodesList as $node) {
		if ($node->hasAttribute($pObjectNum)) {
			$lFigNum[] = (int)$node->getAttribute($pObjectNum);
		}
    }

	// Намираме най-малката цитирана фигура и най-голямата от всичките фигури
	$lMinCittFigNum = min($lFigNum);
	$lMaxFigNum = (int)max($lAllFiguresArr);
	$lMinFigNum = (int)min($lAllFiguresArr);

	$lNode = $lMinCittFigNum;
	$lFlag = 0;

	// Масив с всички вече обработени фигури/таблици
	$lVisitedFigures = array();

// 	var_dump($lCittFiguresQuery, $lAllFiguresArr);
// 	exit;
	// Обхождане на цитираните фигури и започване на подреждането им при срещане на най-малката цитирана фигура/таблица
	foreach ($lNodesList as $node) {

		if ($node->hasAttribute($pObjectNum) && $node->getAttribute($pObjectNum) == $lNode || $lFlag == 1) {
			$lCurFigNum = (int)$node->getAttribute($pObjectNum);
			$lCurNodeRef = $node;

			// Тук се гледа дали цитацията е в Identification key
			$lIdentKeyNode = upToParentNodeByNameAndAttribute($node, 'table', 'identification_key_table');

			if($lFlag == 0) {
				if(!in_array($lCurFigNum, $lVisitedFigures)) {
					$lMinNodeId = (int)$node->getAttribute($pObjectNum);
					$lMinNodeRef = $node;
					if($lMinNodeId >= $lMinFigNum){
						$lNodeAfterWhichToMoveCitationElement = GetCitationElementParentPredecessor($lMinNodeRef);
						if(!$lNodeAfterWhichToMoveCitationElement){
// 							var_dump($lNodeAfterWhichToMoveCitationElement, 4);
						}
						for($i = $lMinFigNum; $i <= $lMinNodeId;$i++) {
							moveObjectToCitationPos($pDomDoc, $i, $lNodeAfterWhichToMoveCitationElement, $pPositionAttr, 0, $node);
							$lVisitedFigures[] = $i;
						}
					}
				}

			} else {
				if(!in_array($lCurFigNum, $lVisitedFigures)) {
					if($lCurFigNum > $lMinNodeId) {
						$lNodeAfterWhichToMoveCitationElement = GetCitationElementParentPredecessor($lMinNodeRef);
						if(!$lNodeAfterWhichToMoveCitationElement){
							continue;
							var_dump($lNodeAfterWhichToMoveCitationElement, 3);
						}
						for($i = $lMinNodeId; $i < $lCurFigNum;$i++) {
							moveObjectToCitationPos($pDomDoc, $i, $lNodeAfterWhichToMoveCitationElement, $pPositionAttr, 0, $node);
							$lVisitedFigures[] = $i;
						}

						$lNodeAfterWhichToMoveCitationElement = GetCitationElementParentPredecessor($lCurNodeRef);
						if(!$lNodeAfterWhichToMoveCitationElement){
// 							var_dump('A', $node->ownerDocument->saveXml($node->parentNode), $lCurNodeRef->ownerDocument->saveXml($lCurNodeRef));
						}
						moveObjectToCitationPos($pDomDoc, $lCurFigNum, $lNodeAfterWhichToMoveCitationElement, $pPositionAttr, $lIdentKeyNode, $node);

						$lMinNodeId = $lCurFigNum;
						$lMinNodeRef = $lCurNodeRef;
					} else {
						$lNodeAfterWhichToMoveCitationElement = GetCitationElementParentPredecessor($lMinNodeRef);
						if(!$lNodeAfterWhichToMoveCitationElement){
							continue;
// 							var_dump($lNodeAfterWhichToMoveCitationElement, 1);
						}
						moveObjectToCitationPos($pDomDoc, $lMinNodeId, $lNodeAfterWhichToMoveCitationElement, $pPositionAttr, $lIdentKeyNode, $node);
					}
					//~ $lNextFigNode = getFollowingFigureNode(end($lVisitedFigures), $xpath, $pDomDoc, $pObjectCitt, $pObjectNum);
					//~ if($lNextFigNode == 1){ // последна фигура - слагаме я на мястото й
						//~ $lPTagNode = upToParentNodeByTag($lCurNodeRef, 'p');
						//~ echo $lCurNodeId;
						//~ moveObjectToCitationPos($pDomDoc, $lCurNodeId, $lPTagNode, $pPositionAttr, $lIdentKeyNode, $node);
					//~ }
				} else {
					continue;
				}

			}
			$lFlag = 1;
		}
    }

    //Remove the citation wrapper nodes
    $lCitationWrapperNodes = $lXpath->query('//' . CITATION_ELEMENT_CITATION_WRAPPER_NODE_NAME);
    foreach ($lCitationWrapperNodes as $lCitationWrapperNode){
//     	var_dump($lCitationWrapperNode->firstChild);
    	$lParentNode = $lCitationWrapperNode->parentNode;
    	while($lCitationWrapperNode->firstChild){
//     		var_dump($lCitationWrapperNode->firstChild);
    		$lParentNode->insertBefore($lCitationWrapperNode->firstChild, $lCitationWrapperNode);
    	}
    	$lParentNode->removeChild($lCitationWrapperNode);
    }
    $pDomDoc->encoding = DEFAULT_XML_ENCODING;
//     return $pHtml;
	return $pDomDoc->saveHTML();
}

/**
 * Returns the node after which the citation element should be moved
 * @param DomElement $pCitationElement
 */
function GetCitationElementParentPredecessor($pCitationElement){
	//First chech for paragraph. If there are no paragraphs try ul and ol.
	$lAllowedParentNodeNames = array('p', 'ul', 'ol');
	foreach ($lAllowedParentNodeNames as $lCurrentParentType){
		$lResult = upToParentNodeByTag($pCitationElement, $lCurrentParentType);
		if($lResult){
			return $lResult;
		}
	}
	return null;
}

/**
 * Пазим генерираните xml-и в статичен масив за да не се налага всеки път да го генерираме.
 * Има параметър за задължително генериране. Така ще избегнем документа да се генерира
 * всеки път при генериране на preview-то на даден инстанс и подинстансите му
 * @param unknown_type $pDocumentId
 * @param unknown_type $pMode
 */
function getDocumentXml($pDocumentId, $pMode = SERIALIZE_INTERNAL_MODE, $pExplicitGeneration = false, $pPrepareCitations = false, $pInstanceId = 0, $pInsertDocumentComments = false, $pInsertCommentPositions = false){
	static $lDocumentXmls = array();
	$lCon = new DBCn();
	$lCon->Open();
	$lIsModified = '';
	if(!(int)$pDocumentId)
		return;
	if($pExplicitGeneration || !array_key_exists($pDocumentId, $lDocumentXmls) || !array_key_exists($pMode, $lDocumentXmls[$pDocumentId])){
		if(!is_array($lDocumentXmls[$pDocumentId])){
			$lDocumentXmls[$pDocumentId] = array();
		}

		if((int)$pInstanceId) {
			$lSql = '
				SELECT
					doi1.is_modified::int as is_modified
				FROM pwt.document_object_instances doi
				JOIN pwt.document_object_instances doi1 ON doi1.pos = substring(doi.pos from 1 for 2) AND doi1.document_id = ' . (int)$pDocumentId . '
				WHERE doi.id = ' . (int)$pInstanceId;

			$lCon->Execute($lSql);
			$lIsModified = $lCon->mRs['is_modified'];
		}else{
			$lSql = '
			SELECT
				xml_is_dirty::int as is_modified
			FROM pwt.documents
			WHERE id = ' . (int)$pDocumentId;

			$lCon->Execute($lSql);
			$lIsModified = $lCon->mRs['is_modified'];
		}

		if($lIsModified) {
			$lDocumentSerializer = new cdocument_serializer(array(
				'document_id' => $pDocumentId,
				'mode' => (int)$pMode,
			));
			$lDocumentSerializer->GetData();
			$lDocumentXmls[(int)$pDocumentId][(int)$pMode] = $lDocumentSerializer->getXml();

// 			$lSql = 'UPDATE pwt.documents SET doc_xml = \'' . q($lDocumentXmls[(int)$pDocumentId][(int)$pMode]) . '\'::xml WHERE id = ' . (int)$pDocumentId;
// 			$lCon->Execute($lSql);

// 			$lSql = 'SELECT * FROM pwt."XmlIsDirty"(2, ' . (int)$pDocumentId . ', null)';
// 			$lCon->Execute($lSql);

		} else {
			$lXmlSel = 'SELECT doc_xml, xml_is_dirty::int as is_modified FROM pwt.documents WHERE id = ' . (int)$pDocumentId;
			$lCon->Execute($lXmlSel);
			if(!(int)$lCon->mRs['is_dirty']){
				$lXML = $lCon->mRs['doc_xml'];
			}else{
				$lDocumentSerializer = new cdocument_serializer(array(
					'document_id' => $pDocumentId,
					'mode' => (int)$pMode,
				));
				$lDocumentSerializer->GetData();
				$lXML = $lDocumentSerializer->getXml();

// 				$lSql = 'UPDATE pwt.documents SET doc_xml = \'' . q($lXML) . '\'::xml WHERE id = ' . (int)$pDocumentId;
// 				$lCon->Execute($lSql);

// 				$lSql = 'SELECT * FROM pwt."XmlIsDirty"(2, ' . (int)$pDocumentId . ', null)';
// 				$lCon->Execute($lSql);
			}
			$lDocumentXmls[(int)$pDocumentId][(int)$pMode] = $lXML;
		}
	}

	$lXml = $lDocumentXmls[(int)$pDocumentId][(int)$pMode];

	if($pInsertDocumentComments){
		$lXml = InsertDocumentComments($pDocumentId, $lXml);
	}
	if($pInsertCommentPositions){
		$lDocumentComments = GetDocumentComments($pDocumentId);
// 		var_dump($lDocumentComments);
		$lXml = StripXmlCitations($lXml);
		$lXml = InsertDocumentCommentPositionNodes($lXml, $lDocumentComments);
	}

	if($pPrepareCitations){
		$lXml = prepareDocumentCitations($pDocumentId, $lXml, null, $pInstanceId);
	}

// 	var_dump($lXml);

	return $lXml;
}

function StripXmlCitations($pXml){
	$lXmlDom = new DOMDocument('1.0', DEFAULT_XML_ENCODING);
	if(!$lXmlDom->loadXML($pXml)){
		return $pXml;
	}
	$lXPath = new DOMXPath($lXmlDom);
	$lCon = new DBCn();
	$lCon->Execute('SELECT *
			FROM pwt.citation_types');
	while(!$lCon->Eof()){
		$lQuery = '//' . $lCon->mRs['node_name'];
		foreach ($lXPath->query($lQuery) as $lCitationNode) {
			while($lCitationNode->firstChild){
				$lCitationNode->removeChild($lCitationNode->firstChild);
			}
		}
		$lCon->MoveNext();
	}
	return $lXmlDom->saveXML();
}

/**
 * Оправяме цитациите за документа - обикаляме целия xml и слагаме
 * кода на цитациите. Изтриваме цитациите, които съществуват в базата, а ги няма в xml-а.
 * @param unknown_type $pDocumentId
 * @param unknown_type $pDocumentXml
 * @param unknown_type $pMode
 * @return - обработения xml
 */
function prepareDocumentCitations($pDocumentId, $pDocumentXml = false, $pMode = SERIALIZE_INTERNAL_MODE, $pInstanceId = 0){
	$lDocumentXml = $pDocumentXml;
	if(!$pDocumentXml){
		$lDocumentXml = getDocumentXml($pDocumentId, $pMode);
	}
// 	return $lDocumentXml;

	$lXmlDom = new DOMDocument('1.0', DEFAULT_XML_ENCODING);
	if(!$lXmlDom->loadXML($lDocumentXml)){
		return $lDocumentXml;
	}

	global $user;
	$lXPath = new DOMXPath($lXmlDom);
	$lCon = new DBCn();
	$lCon->Open();
	$lCon2 = new DBCn();
	$lCon2->Open();

	$lSql = 'SELECT * FROM pwt.citation_types';
	$lCon->Execute($lSql);
	$lCitationTypes = array();
	while(!$lCon->Eof()){
		$lCitationTypes[$lCon->mRs['id']] = $lCon->mRs;
		$lCon->MoveNext();
	}

	//Обикаляме цитациите тип по тип
	foreach ($lCitationTypes as $lCitationType => $lTypeData){
		$lCitationNodeName = $lTypeData['node_name'];

		$lSql = 'SELECT * FROM spGetDocumentCitations(' . (int)$pDocumentId . ', ' . (int)$lCitationType . ', ' . (int)$pInstanceId . ') ORDER BY instance_id ASC, field_id ASC;';
		$lCon->Execute($lSql);
		$lPreviousFieldId = 0;
		$lPreviousInstanceId = 0;
		$lCurrentFieldNode = null;
		$lCitationsToDelete = array();
		while(!$lCon->Eof()){
			$lCurrentFieldId = (int)$lCon->mRs['field_id'];
			$lCurrentInstanceId = (int)$lCon->mRs['instance_id'];
			if($lPreviousFieldId != $lCurrentFieldId || $lPreviousInstanceId != $lCurrentInstanceId ){
				$lQuery = '//*[@instance_id="' . (int)$lCurrentInstanceId . '"]/fields/*[@id="' . (int)$lCurrentFieldId . '"]';
// 				var_dump($lQuery);
				$lCurrentFieldNode = $lXPath->query($lQuery);
				if(!$lCurrentFieldNode->length){
					$lCon->MoveNext();
					continue;
				}
				$lPreviousFieldId = $lCurrentFieldId;
				$lPreviousInstanceId = $lCurrentInstanceId;
				$lCurrentFieldNode = $lCurrentFieldNode->item(0);
				//var_dump($lCurrentFieldNode->parentNode->nodeName);
			}

			$lCitationNodeResult = $lXPath->query('//' . $lCitationNodeName . '[@citation_id="' . (int)$lCon->mRs['citation_id'] . '"]', $lCurrentFieldNode);

			//Ако няма такава цитация - добавяме я за триене
			if(!$lCitationNodeResult->length){
				$lCitationsToDelete[] = $lCon->mRs['citation_id'];
			}else{
				//Махаме децата и слагаме превюто от базата
				$lCitationNode = $lCitationNodeResult->item(0);
				$lCitationNode->setAttribute('is_parsed', 1);
				while($lCitationNode->hasChildNodes()){
					$lCitationNode->removeChild($lCitationNode->firstChild);
				}
				$lFragment = $lXmlDom->createDocumentFragment();
				$lPreview = $lCon->mRs['preview'];
// 				var_dump($lPreview);
				//Ако превюто е валиден xml - добавяме го. В противен случай го добавяме като текст
				if(@$lFragment->appendXML($lPreview)){
					$lCitationNode->appendChild($lFragment);
				}else{
					$lCitationNode->appendChild($lXmlDom->createTextNode($lPreview));
				}
			}
			$lCon->MoveNext();
		}

		//Обикаляме всички цитации и трием тези, които ги няма в базата
		$lCitationNodes = $lXPath->query('//' . $lCitationNodeName);
		for($i = $lCitationNodes->length - 1; $i >= 0; --$i){
			$lCurrentCitation = $lCitationNodes->item($i);
			//Ако сме я обработили - махаме атрибута
			if((int)$lCurrentCitation->getAttribute('is_parsed')){
				$lCurrentCitation->removeAttribute('is_parsed');
			}else{//В противен случай - директно я трием
				$lCurrentCitation->parentNode->removeChild($lCurrentCitation);
			}
		}

		//Трием цитациите, които ги няма в текста
		foreach ($lCitationsToDelete as $lCurrentCitationId){
			$lSql = 'SELECT * FROM spDeleteCitation(' . (int) $lCurrentCitationId . ', ' . (int) $user->id . ');';
			$lCon2->Execute($lSql);
		}

	}
	$lXmlDom->encoding = DEFAULT_XML_ENCODING;
	return $lXmlDom->saveXML();
}

/**
 * Вкарваме коментарите за документа в xml-a
 * @param unknown_type $pDocumentId
 * @param unknown_type $pDocumentXml
 */
function InsertDocumentComments($pDocumentId, $pDocumentXml = false, $pMode = SERIALIZE_INTERNAL_MODE){
	$lDocumentXml = $pDocumentXml;
	if(!$pDocumentXml){
		$lDocumentXml = getDocumentXml($pDocumentId, $pMode);
	}
	// 	return $lDocumentXml;

	$lCon = new DBCn();
	$lCon->Open();
	$lSql = 'SELECT id,  document_id, author, subject, msg, senderip, mdate, rootid, ord,
				usr_id, flags, replies, views, lastmoddate, root_object_instance_id, start_object_instances_id,
				end_object_instances_id, start_object_field_id, end_object_field_id, start_offset, end_offset, revision_id,
			start_object_instances_id as start_instance_id, end_object_instances_id as end_instance_id,
			start_object_field_id as start_field_id, end_object_field_id as end_field_id,
			is_resolved::int as is_resolved,
			resolve_uid,
			resolve_date,
			is_disclosed::int as is_disclosed,
			undisclosed_usr_id
		FROM pwt.msg
		WHERE document_id = ' . (int) $pDocumentId  . ' AND revision_id = spGetDocumentLatestCommentRevisionId(' . (int) $pDocumentId  . ', 1)
	';

	$lComments = array();
	$lCon->Execute($lSql);
	while(!$lCon->Eof()){
		$lComments[] = $lCon->mRs;
		$lCon->MoveNext();
	}
// 	var_dump($lComments);
	return InsertCommentsInDocumentXml($lDocumentXml, $lComments);
}


function displayMarkForDeleteBackgroundClass($pMarkForDelete){
	if($pMarkForDelete)
		return ' P-Input-With-Background ';
}

function displayClearDiv( $pMode ){
	if($pMode != INSTANCE_VIEW_MODE){
		return ' <div class="unfloat"></div>';
	}
}

function displayTableHeadByMode( $pMode ){
	if($pMode == INSTANCE_VIEW_MODE){
		return '<table border="0" cellspacing="0" cellpadding="0" class="P-Instance-Content-Table">
											<tr>
												<td>';
	}
}

function createHtmlEditorBase($pTextareaId, $pHeight = EDITOR_DEFAULT_HEIGHT, $pWidth = 0, $pToolbarName = EDITOR_FULL_TOOLBAR_NAME, $pUseCommonToolbar = 0, $pCommonToolbarHolderId = '', $pAutoGrow = 1, $pUseFloatingTools = 0, $pFloatingToolsToolbarName = EDITOR_FLOATING_TOOLBAR_NAME_BASIC){
	global $docroot;
	$lAutogrowPluginNames = ($pAutoGrow == 1) ? ',autogrow' : '';
	$lFloatingToolsPluginNames = ($pUseFloatingTools == 1) ? ',floating-tools' : '';	
//var editor = CKEDITOR.replace(\'' . $pTextareaId . '_textarea\', function(){
	//var_dump($pToolbarName);
	$lCitationPluginNames = '';
	$lCssFileName = 'editor_iframe.css';
	
	switch($pToolbarName){		
		case EDITOR_FULL_TOOLBAR_NAME:
		case EDITOR_FULL_TOOLBAR_NAME_NO_MAXIMIZE:
			$lCitationPluginNames = ',figs,tbls,refs,sup_files'; 
			break;
		case EDITOR_REFERENCE_CITATION_TOOLBAR_NAME:
			$lCitationPluginNames = ',refs';
			break;
	}
	
	if($pUseFloatingTools){
		switch($pFloatingToolsToolbarName){
			case EDITOR_FLOATING_TOOLBAR_NAME_MATERIAL:
			case EDITOR_FLOATING_TOOLBAR_NAME_REFERENCE:
			case EDITOR_FLOATING_TOOLBAR_NAME_SECTION_TITLE :
			case EDITOR_FLOATING_TOOLBAR_NAME_PLATE_DESCRIPTION:
				$lCssFileName = 'editor_iframe_inputlike.css';
				break;
		}
	}

	return '<script  type="text/javascript">
			//<![CDATA[
		CKEDITOR.config.contentsCss = \'/lib/css/' . $lCssFileName . '\' ;
		CKEDITOR.config.language = \'en\';


		CKEDITOR.on(\'instanceReady\', function(ev){
			ev.editor.on(\'paste\', function(evt) {
				if(typeof(evt.data["html"]) !== \'undefined\' && evt.data["html"] != null) {
					// remove empty tags, e.g. <p></p>
					evt.data["html"] = (evt.data["html"]).replace(/<[^\/>][^>]*><\/[^>]+>/, \'\');
					evt.data["html"] = cleanHTML(evt.data["html"], ' . ($pToolbarName == EDITOR_MODERATE_TABLE_TOOLBAR_NAME ? 1 : 0) . ');

				}
			}, null, null, 9);
			MarkCKEditorAsLoaded(' . json_encode($pTextareaId . '_textarea') . ');
		});

		var instance = CKEDITOR.instances[\'' . $pTextareaId . '_textarea\'];

		if(instance){
			//instance.destroy(true);
		}

		SaveCKEditorConfig(\'' . $pTextareaId . '_textarea\', {
			' . GetCKEditorExtraAllowedContent() . ',
			extraPlugins : \'autosave,sharedspace,toolbar'. $lFloatingToolsPluginNames . $lAutogrowPluginNames . $lCitationPluginNames .'\',
			on: {
				instanceReady: function( evt ) {
					var leditor = evt.editor;
					fixEditorMaximizeBtn(leditor);
				}
			},
			toolbar : \'' . $pToolbarName . '\',
			floatingtools : \'' . $pFloatingToolsToolbarName . '\',
			removePlugins: \'elementspath,resize\',
			height: ' . (int)$pHeight . ',
			autoGrow_minHeight: ' . (int)$pHeight . ',
			autoGrow_onStartup: true,
			autoGrow_maxHeight: 0
			' . ($pWidth > 0 ? (', width: ' . (int)$pWidth) : '') .
			($pUseCommonToolbar ?
				(',
				sharedSpaces : {
					top : \'' . $pCommonToolbarHolderId . '\'
				}') :
				''
			) . '

		});
		ReloadCKEditor(\'' . $pTextareaId . '_textarea\');
		//]]>
	</script>';

}

function GetCKEditorExtraAllowedContent(){
	return 'extraAllowedContent: {
				\'fig-citation\' : {
					attributes : \'*\',
					classes : \'*\',
					styles : \'*\'
				},
				\'xref\' : {
					attributes : \'*\',
					classes : \'*\',
					styles : \'*\'
				},
				\'tbls-citation\' : {
					attributes : \'*\',
					classes : \'*\',
					styles : \'*\'
				},
				\'reference-citation\' : {
					attributes : \'*\',
					classes : \'*\',
					styles : \'*\'
				},
				\'tn\' : {
					attributes : \'*\',
					classes : \'*\',
					styles : \'*\'
				},
				\'tn-part\' : {
					attributes : \'*\',
					classes : \'*\',
					styles : \'*\'
				},
				\'sup-files-citation\' : {
					attributes : \'*\',
					classes : \'*\',
					styles : \'*\'
				},
				\'' . COMMENT_START_NODE_NAME . '\' : {
					attributes : \'*\',
					classes : \'*\',
					styles : \'*\'
				},
				\'' . COMMENT_END_NODE_NAME . '\' : {
					attributes : \'*\',
					classes : \'*\',
					styles : \'*\'
				}
			}';
}

function createHtmlEditor($pTextareaId){
	return createHtmlEditorBase($pTextareaId);
}

function createHtmlEditorNoCitation($pTextareaId){
	return createHtmlEditorBase($pTextareaId, EDITOR_DEFAULT_HEIGHT, 0, EDITOR_MODERATE_TOOLBAR_NAME);
}

function createSmallHtmlEditor($pTextareaId, $pHeight = EDITOR_SMALL_DEFAULT_HEIGHT, $pWidth = 0, $pToolbarName = EDITOR_SMALL_TOOLBAR_NAME, $pUseCommonToolbar = 0, $pCommonToolbarHolderId = '', $pUseFloatingTools = 0, $pFloatingToolsToolbarName = EDITOR_FLOATING_TOOLBAR_NAME_BASIC){	
	return createHtmlEditorBase($pTextareaId, $pHeight, $pWidth, $pToolbarName, $pUseCommonToolbar, $pCommonToolbarHolderId, 1, $pUseFloatingTools, $pFloatingToolsToolbarName);
}

function createEditorToolbarHolder($pCreateHolder = 0, $pHolderId = ''){
	if(!(int)$pCreateHolder)
		return;
	return '<div id="' . $pHolderId . '"></div>';
}

function createHtmlEditorReferenceCitation($pTextareaId, $pHeight = 30, $pWidth = 0,
											$pToolbarName = EDITOR_REFERENCE_CITATION_TOOLBAR_NAME,
											$pUseCommonToolbar = 0, $pCommonToolbarHolderId = ''){
	global $docroot;

	return '<script  type="text/javascript">
		CKEDITOR.config.contentsCss = \'editor_iframe1.css\' ;
		CKEDITOR.config.language = \'en\';
		var instance = CKEDITOR.instances[\'' . $pTextareaId . '_textarea\'];
		if(instance){
			//instance.destroy(true);
		}

		CKEDITOR.replace(\'' . $pTextareaId . '_textarea\', {
			' . GetCKEditorExtraAllowedContent() . ',
			extraPlugins : \'refs,autosave,sharedspace,autogrow\',
			on: {
				instanceReady: function( evt ) {
					var leditor = evt.editor;
					fixEditorMaximizeBtn(leditor);
				}
			},
			toolbar : \'' . $pToolbarName . '\',
			removePlugins: \'elementspath,resize\',
			height: ' . (int)$pHeight . ',
			autoGrow_minHeight: ' . (int)$pHeight . ',
			autoGrow_onStartup: true,
			autoGrow_maxHeight: 0
			' . ($pWidth > 0 ? (', width: ' . (int)$pWidth) : '') .
			($pUseCommonToolbar ?
				(',
				sharedSpaces : {
					top: \'' . $pCommonToolbarHolderId . '\'
				}') :
				''
			) . '

		});
	</script>';

}

function displayTableFootByMode( $pMode ){
	if($pMode == INSTANCE_VIEW_MODE){
		return '</td>
											</tr>
										</table>';
	}
}

function get_html_translation_table_additional() {
	mb_internal_encoding('UTF-8');

	$trans = get_html_translation_table(HTML_ENTITIES);
	$trans[chr(130)] = '&sbquo;';    // Single Low-9 Quotation Mark
	$trans[chr(131)] = '&fnof;';    // Latin Small Letter F With Hook
	$trans[chr(132)] = '&bdquo;';    // Double Low-9 Quotation Mark
	$trans[chr(133)] = '&hellip;';    // Horizontal Ellipsis
	$trans[chr(134)] = '&dagger;';    // Dagger
	$trans[chr(135)] = '&Dagger;';    // Double Dagger
	$trans[chr(136)] = '&circ;';    // Modifier Letter Circumflex Accent
	$trans[chr(137)] = '&permil;';    // Per Mille Sign
	$trans[chr(138)] = '&Scaron;';    // Latin Capital Letter S With Caron
	$trans[chr(139)] = '&lsaquo;';    // Single Left-Pointing Angle Quotation Mark
	$trans[chr(140)] = '&OElig;    ';    // Latin Capital Ligature OE
	$trans[chr(145)] = '&lsquo;';    // Left Single Quotation Mark
	$trans[chr(146)] = '&rsquo;';    // Right Single Quotation Mark
	$trans[chr(147)] = '&ldquo;';    // Left Double Quotation Mark
	$trans[chr(148)] = '&rdquo;';    // Right Double Quotation Mark
	$trans[chr(149)] = '&bull;';    // Bullet
	$trans[chr(150)] = '&ndash;';    // En Dash
	$trans[chr(151)] = '&mdash;';    // Em Dash
	$trans[chr(152)] = '&tilde;';    // Small Tilde
	$trans[chr(153)] = '&trade;';    // Trade Mark Sign
	$trans[chr(154)] = '&scaron;';    // Latin Small Letter S With Caron
	$trans[chr(155)] = '&rsaquo;';    // Single Right-Pointing Angle Quotation Mark
	$trans[chr(156)] = '&oelig;';    // Latin Small Ligature OE
	$trans[chr(159)] = '&Yuml;';    // Latin Capital Letter Y With Diaeresis
	$trans[chr(159)] = '&le;';    // Latin Capital Letter Y With Diaeresis
	ksort($trans);
	return $trans;
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
	'ArrHTMLEntityDecode',
	$pValue);


	return $pValue;
}

function ArrHTMLEntityDecode($pMatches){
	return html_entity_decode($pMatches[0], ENT_NOQUOTES, "UTF-8");
}

function getTabbedElementActiveClass($pActiveElementId, $pCurrentElementId){
	if($pActiveElementId == $pCurrentElementId)
			return 'P-Active';
}

function getTabbedElementDisplayClass($pActiveElementId, $pCurrentElementId){
	if($pActiveElementId != $pCurrentElementId)
			return 'hiddenElement';
	return '';
}

function prepareXMLErrors($pXMLArr, $pDocumentId) {
	if(is_array($pXMLArr)) {
		global $gXMLErrors;
		foreach($pXMLArr as $err=>$val) {
			$lStr .= '<div class="P-Document-Validation-Errs"><div class="P-Document-Validation-ErrType-Txt">' . $gXMLErrors[$err] .'</div>
					<ul>';
			foreach($val as $v) {
				if($v['node_instance_name'] == 'figures') {
					//$lStr .= '<li>- <a href="/figures.php?document_id=' . $v['document_id'] . '">' . $v['node_attribute_field_name'] . ' in  "' . $v['node_instance_name'] . '"</a></li>';
					$lStr .= '<li>- <a href="/display_document.php?instance_id=' . $v['node_instance_id'] . '">' . $v['node_attribute_field_name'] . '</a></li>';
				} elseif($v['node_instance_name'] == 'tables') {
					//$lStr .= '<li>- <a href="/tables.php?document_id=' . $v['document_id'] . '">' . $v['node_attribute_field_name'] . ' in  "' . $v['node_instance_name'] . '"</a></li>';
					$lStr .= '<li>- <a href="/display_document.php?instance_id=' . $v['node_instance_id'] . '">' . $v['node_attribute_field_name'] . '</a></li>';
				} elseif($v['node_instance_name'] == 'reference') {
					$lReferenceDisplayName = getReferenceDisplayNameByInstanceId($v['node_instance_id']);
					//$lStr .= '<li>- <a href="/display_document.php?instance_id=' . $v['node_instance_id'] . '">' . $v['node_attribute_field_name'] . ' in  "' . $lReferenceDisplayName . '"</a></li>';
					$lStr .= '<li>- <a href="/display_document.php?instance_id=' . $v['node_instance_id'] . '">' . '"' . $lReferenceDisplayName . '" ' . $v['node_attribute_field_name'] . '</a></li>';
				} else {
					//var_dump($v['node_instance_id']);
					//var_dump(checkIsReferenceByInstanceId($v['node_instance_id'], $pDocumentId));
					$lRefId = checkIsReferenceByInstanceId($v['node_instance_id'], $pDocumentId);
					if($lRefId){
						$lReferenceDisplayName = getReferenceDisplayNameByInstanceId($lRefId);
						$lStr .= '<li>- <a href="/display_document.php?instance_id=' . $lRefId . '">' . $v['node_attribute_field_name'] . ' in reference "' . $lReferenceDisplayName . '"</a></li>';
					} else {
						$lInstanceIdDisplayErr = getInstanceDisplayErr($v['node_instance_id']);
						if ($v['node_instance_name'] == 'Taxon treatments'){
							$lStr .= '<li>- ' . $v['node_attribute_field_name'] . '</li>';
						} else {
							$lStr .= '<li>- <a href="/display_document.php?instance_id=' . $lInstanceIdDisplayErr . '">' . $v['node_attribute_field_name'] . ' in  "' . $v['node_instance_name'] . '"</a></li>';
						}
					}
				}
			}
			$lStr .= '</ul></div>';
		}
		return $lStr;
	}
	return '';
}

function checkIsReferenceByInstanceId($pInstanceId, $pDocumentId){
	$lCon = new DBCn();
	$lCon->Open();
	$lSql = '
		SELECT
			doi1.id
		FROM pwt.document_object_instances doi
		JOIN pwt.document_object_instances doi1 ON doi1.pos = substring(doi.pos FROM 1 for 4) AND doi1.document_id = doi.document_id
		WHERE doi.id = ' . $pInstanceId . '
			AND doi.document_id = ' . $pDocumentId . '
			AND doi1.object_id = ' . REFERENCE_OBJECT_ID . '
	';
	//var_dump($lSql);
	$lCon->Execute($lSql);
	$lReferenceId = (int)$lCon->mRs['id'];
	$lCon->Close();
	return $lReferenceId;
}

function getInstanceDisplayErr($pInstanceId){
	$lCon = new DBCn();
	$lCon->Open();
	$lSql = 'SELECT instance_id FROM pwt."spGetInstanceDisplayErr"(' . (int)$pInstanceId . ')';
	$lCon->Execute($lSql);
	$lCon->MoveFirst();
	$lInstanceIdDisplayErr = $lCon->mRs['instance_id'];
	$lCon->Close();
	return $lInstanceIdDisplayErr;
}

function getReferenceDisplayNameByInstanceId($pInstanceId){
	$lCon = new DBCn();
	$lCon->Open();
	$lSql = 'SELECT document_id FROM pwt.document_object_instances WHERE id = ' . $pInstanceId;
	$lCon->Execute($lSql);
	$lCon->MoveFirst();
	$lDocumentId = $lCon->mRs['document_id'];
	$lCon->Close();

	$lUnparsedData = new cdocument_references(array(
		'ctype' => 'cdocument_references',
		'document_id' => $lDocumentId,
		'templs' => array(
			G_HEADER => 'global.empty',
			G_ROWTEMPL => 'references.single_reference_preview',
			G_FOOTER => 'global.empty',
			G_NODATA => 'global.empty'
		),
		'sqlstr' => '
				SELECT *, reference_instance_id as id
				FROM spGetDocumentReferences(' . (int) $lDocumentId . ')
				ORDER BY is_website_citation ASC, first_author_combined_name ASC, authors_count ASC, authors_combined_names ASC, pubyear ASC
			'
	));
	$lUnparsedData->GetData();
	$lUnparsedDataArr = $lUnparsedData->m_resultArr;
	$lDom = new DOMDocument('1.0', DEFAULT_XML_ENCODING);

	foreach($lUnparsedDataArr as $lCurrentRow) {
		//var_dump($lCurrentRow);
		if($lCurrentRow['reference_instance_id'] == $pInstanceId){
			if(@$lDom->loadHTML($lCurrentRow['preview'])) {
				$lXPath = new DOMXPath($lDom);
				$lPreviewNode = $lXPath->query('//div/div[@id="Ref-Preview-' . $pInstanceId . '-Mode-1"]');
				if($lPreviewNode->length){
					return $lPreviewNode->item(0)->nodeValue;
				}
				return '';
			}
		}
	}
	return '';
}



function getInstanceDisplayInTree($pInstanceId){
	$lCon = new DBCn();
	$lCon->Open();
	$lSql = 'SELECT instance_id FROM pwt."spGetInstanceDisplayInTree"(' . (int)$pInstanceId . ')';
	$lCon->Execute($lSql);
	$lCon->MoveFirst();
	$lInstanceIdDisplayInTree = $lCon->mRs['instance_id'];
	$lCon->Close();
	return $lInstanceIdDisplayInTree;
}

function displayEditedByRow( $pIsLocked, $pEditedByUser, $pEditedByUserId){
	global $user;
	if( $pIsLocked && (int)$pEditedByUserId <> (int)$user->id){
		// тук трябва да се оправи линка $pEditedByUser като се направи страница на user-a
		return '<div class="P-Document-Edited-By">' . getstr('dashboard.currently_edited_by') . ' <a href="javascript: void(0);">' . $pEditedByUser . '</a></div>
				<div class="P-Clear"></div>';
	}
}

function getCurrentDocumentStatus( $pDocumentStatus, $pIsLocked, $pEditedByUserId){
	global $user;
	if( $pIsLocked && (int)$pEditedByUserId <> (int)$user->id){
		return getstr('status.inedit');
	}
	// Тук трябва да се добавят останалите статуси, когато стане ясно с какви id-та ще бъдат
	$statuses = array(1=>'status.draft', 'status.submitted_to_pjs', 'status.returned', 5 => 'status.Pre-submit-review', 6 => 'status.Ready-to-submit', 7 => 'status.published');
	return '<span>' . getstr($statuses[$pDocumentStatus]) . '</span>';
}

function displayClassByDocumentStatus( $pDocumentStatus, $pIsLocked ){
	if( $pIsLocked ){
		return ' P-Locked-Document';
	}
	// Тук трябва да се добавят останалите класове, когато стане ясно статусите с какви id-та ще бъдат
	switch($pDocumentStatus) {
		case 1:
			return ' P-Status-Draft';
			break;
		default:
			break;
	}
}

function displayTopRightButtons( $pIsLocked = 0, $pPreviewMode = 0, $pDocumentId, $pXmlValidation = 0){
	$lRet = '';
	$lInstanceId = (int)$_REQUEST['instance_id'];
	if( $lInstanceId ){
		$lRetLink = './display_document.php?instance_id=' . $lInstanceId;
	}else{
		$lRetLink = './display_document.php?document_id=' . $pDocumentId;
	}
	//var_dump($pXmlValidation);
	if($pXmlValidation == 1) {
		$lRet = '<span class="P-PreviewBtn"><input onclick="showLoading();savePreviewDocument(' . (int)$pDocumentId	. ', ' . (int)$lInstanceId . ');return false;" class="preview_btn" type="submit" value="" /></span>';
	} elseif( (int)$pPreviewMode ){
		if( !(int)$pIsLocked ){
			$lRet = '<div class="P-Grey-Btn-Holder P-Edit" onclick="window.location=\'' . $lRetLink . '\';return false;"><div class="P-Grey-Btn-Left"></div><div class="P-Grey-Btn-Middle" style="width: 110px;"><div class="P-Btn-Icon"></div>Advanced edit</div><div class="P-Grey-Btn-Right"></div></div>';
		}
	}else{
		$lRet = '<span class="P-SaveBtn"><input class="save_btn" type="submit" value="" onclick="showLoading();$(\'form#document_form\').submit();" /></span>
		<span class="P-PreviewBtn"><input onclick="showLoading();savePreviewDocument(' . (int)$pDocumentId	. ', ' . (int)$lInstanceId . ');return false;" class="preview_btn" type="submit" value="" /></span>';
	}

	return $lRet;
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

function showValidationErrorDiv($pErrors, $pValidation = 0) {
	if((int)$pErrors) {
		return '<div class="P-Document-Validation-Err-Notification"><img src="/i/excl_ico.png" alt="" />' . getstr('pwt.xmlvalidation.errnotification') . '</div>';
	}
	if((int)$pValidation && !(int)$pErrors) {
		return '
			<div class="P-Document-Validation-Err-Notification P-Document-Validation-Valid-Notification">
				<img src="/i/valid_icon.png" alt="" />' . getstr('pwt.xmlvalidation.validnotification') . '
			</div>';
	}
}

function showValidationErrorClass($pErrors, $pValidation = 0) {
	if((int)$pErrors || ((int)$pValidation && !(int)$pErrors)) {
		return 'P-Bread-Crumbs-Validation';
	}
}

function showValidationErrorClassMain($pErrors, $pValidation = 0) {
	if((int)$pErrors || ((int)$pValidation && !(int)$pErrors)) {
		return 'P-Wrapper-With-Validation';
	}
}

function showDocumentLockWarning($pIsLocked, $pLockedUser, $pWithoutWarning = 0, $pVersionIsReadonly = false) {
	global $user;
	if(checkIfDocumentIsLockedByAnotherUser($pIsLocked, $pLockedUser, $pWithoutWarning)) {
		$lDocLockedUserFullName = getUserNameById((int)$pLockedUser);
		$lStr = str_replace('{full_username}', $lDocLockedUserFullName, getstr('pwt.document.locked'));
		return '<div class="P-Document-Locked-Warning">
					<img src="/i/document_locked_warning_icon.png" alt="" />' . $lStr . '
				</div>';
	}elseif($pVersionIsReadonly){
		return '
				<div class="P-Document-Validation-Err-Notification">
					<img src="/i/excl_ico.png" alt="" />' . getstr('pwt.documentIsReadonly') . '
				</div>';
	}
	return '';
}

function showLockedErrorClass($pIsLocked, $pLockedUser, $pWithoutWarning = 0, $pVersionIsReadonly = false){
	if(checkIfDocumentIsLockedByAnotherUser($pIsLocked, $pLockedUser, $pWithoutWarning) || $pVersionIsReadonly) {
		return ' P-Bread-Crumbs-With-Lock-Warning ';
	}
}

function showLockedErrorClassMain($pIsLocked, $pLockedUser, $pWithoutWarning = 0, $pVersionIsReadonly = false){
	if(checkIfDocumentIsLockedByAnotherUser($pIsLocked, $pLockedUser, $pWithoutWarning) || $pVersionIsReadonly) {
		return ' P-Wrapper-With-Lock-Warning ';
	}
}

function checkIfDocumentIsLockedByAnotherUser($pIsLocked, $pLockedUser, $pWithoutWarning = 0){
	global $user;
	if((int)$pIsLocked && (int)$user->id != (int)$pLockedUser && !(int)$pWithoutWarning) {
		return true;
	}
	return false;
}

function hideTreeIfDocumentIslocked($pIsLocked, $pTreeHolderToHide, $pLockedUser) {
	global $user;
	if((int)$pIsLocked && $pTreeHolderToHide && (int)$user->id != (int)$pLockedUser) {
		return '<script type="text/javascript">
					hideElement(\'' . $pTreeHolderToHide . '\');
					toggleLeftContainer();
				</script>';
	}
	return '';
}

function sendMailToAuthorApiRegister($pUserEmail, $pUserPass, $pUserFullName){
	$lMessageData = array(
		'new_user_pass' => $pUserPass,
		'new_user_mail' => $pUserEmail,
		'user_fullname' => $pUserFullName,
		'siteurl' => SITE_URL,
		'requestdate' => date('d/m/Y H:i'),
		'mailsubject' => PENSOFT_MAILSUBJ_DOC_NEW_AUTHOR_REGISTER,
		'mailto' => $pUserEmail,
		'charset' => 'UTF-8',
		'boundary' => '--_separator==_',
		'from' => array(
			'display' => PENSOFT_MAIL_DISPLAY,
			'email' => PENSOFT_MAIL_ADDR,
		),
		'templs' => array(
			G_DEFAULT => 'document.mail_document_add_newauthor_register_api',
		),
	);

	$lMsg = new cmessaging($lMessageData);
	$lMsg->Display();
}

function sendMailToAuthor($pRootInstanceId, $pUserExistsId, $pNewUserId, $pInstanceId, $pDocumentId, $pUpass, $pContributorFlag = 0) {
	global $user;
	$lCon = new DBCn();
	$lCon->Open();

	$lInstanceId = (int)$pRootInstanceId;
	if(!$lInstanceId){
		$lInstanceId = $pInstanceId;
	}
	$lSql = 'SELECT u.id as createuid, coalesce(ut.name || \' \' || u.first_name || \' \' || u.last_name, u.uname) as fullname,
					u.uname as user_email, u.autolog_hash as autolog_hash
		FROM pwt.document_object_instances i
		JOIN pwt.documents d ON d.id = i.document_id
		JOIN public.usr u ON u.id = d.createuid
		LEFT JOIN public.usr_titles ut ON ut.id = u.usr_title_id
		WHERE i.id = ' . (int)$lInstanceId . '
	';

	$lCon->Execute($lSql);
	$lDocumentCreatorUid = (int)$lCon->mRs['createuid'];
	$lDocumentCreatorFullName = $lCon->mRs['fullname'];
	$lDocumentCreatorEmail = $lCon->mRs['user_email'];


	$lSql = 'SELECT if.value_int,
					if2.value_int as curr_user,
					coalesce( if3.value_str, \'Untitled\')  as document_title,
					if4.data_src_id as contributor_role_query_id,
					array_to_string(if4.value_arr_int, \',\', \'0\') as contributor_role_ids,
					if4.query as contributor_role_query,
					if5.value_int as author_type
			FROM pwt.instance_field_values if
			LEFT JOIN (SELECT value_int ,instance_id
						FROM pwt.instance_field_values
						WHERE field_id = 13 AND value_int <> ' . (int)$lDocumentCreatorUid . '
						) if2 ON if2.instance_id = if.instance_id
			LEFT JOIN (SELECT value_str ,document_id
						FROM pwt.instance_field_values
						WHERE field_id = 3 AND document_id = ' . (int)$pDocumentId . '
						) if3 ON if3.document_id = if.document_id
			LEFT JOIN (SELECT ifv.value_arr_int, ifv.document_id, ifv.data_src_id, ds.query
						FROM pwt.instance_field_values ifv
						JOIN pwt.data_src ds ON ifv.data_src_id = ds.id
						WHERE ifv.field_id = 16 AND ifv.document_id = ' . (int)$pDocumentId . ' AND ifv.instance_id = ' . (int)$pInstanceId . '
						) if4 ON if4.document_id = if.document_id
			LEFT JOIN (SELECT ifv.value_int, ifv.document_id, ifv.data_src_id
						FROM pwt.instance_field_values ifv
						WHERE ifv.field_id = 14 AND ifv.document_id = ' . (int)$pDocumentId . ' AND ifv.instance_id = ' . (int)$pInstanceId . '
						) if5 ON if5.document_id = if.document_id
			WHERE if.instance_id = ' . (int)$pInstanceId . ' AND if.field_id = ' . (int) AUTHOR_MAIL_NOTIFICATION_FIELD_ID;
// 	var_dump('A' . $pInstanceId);
	$lCon->Execute($lSql);
	$lCon->MoveFirst();
	$lVal_id = $lCon->mRs['value_int'];
	$lCur_user = (int)$lCon->mRs['curr_user'];
	$lDocumentTitle = $lCon->mRs['document_title'];
	$lContributorRolesQueryId = $lCon->mRs['contributor_role_query_id'];
	$lContributorRolesIds = $lCon->mRs['contributor_role_ids'];
	$lContributorRoleQuery = $lCon->mRs['contributor_role_query'];
	$lAuthorRights = (int)$lCon->mRs['author_type'];
	$lCon->Close();

	$lDocumentTitle = strip_tags($lDocumentTitle);
	$lDocumentTitle = ltrim($lDocumentTitle);
	$lDocumentTitle = rtrim($lDocumentTitle);

	if($lContributorRoleQuery && $lContributorRolesIds) {
		$lQuery = 'SELECT roles.name, roles.id
					FROM
					(' . $lContributorRoleQuery . ') as roles
					WHERE roles.id IN (' . $lContributorRolesIds . ')';
		$lCon = new DBCn();
		$lCon->Open();
		$lCon->Execute($lQuery);
		$lCon->MoveFirst();
		$lRoles = array();
		while(!$lCon->Eof()){
			$lRoles[] = $lCon->mRs['name'];
			$lCon->MoveNext();
		}
		if(!empty($lRoles)) {
			$lColaborateRole = implode(', ', $lRoles);
		}
		$lCon->Close();
	}
	if((int)$pNewUserId && $lVal_id == 0 && $lCur_user <> 0) {
		$lCon = new DBCn();
		$lCon->Open();
		$lCon->Execute('SELECT coalesce(ut.name || \' \' || u.first_name || \' \' || u.last_name, u.uname) as fullname,
								u.uname as user_email, u.autolog_hash as autolog_hash
						FROM public.usr u
						LEFT JOIN public.usr_titles ut ON ut.id = u.usr_title_id
						WHERE u.id = ' . (int)$pNewUserId . '');
		$lCon->MoveFirst();
		$lUserFullName = $lCon->mRs['fullname'];
		$lUserEmail = $lCon->mRs['user_email'];
		$lUserAutologHash = $lCon->mRs['autolog_hash'];
		$lCon->Close();
		$mespubdata = array(
			'document_title' => $lDocumentTitle,
			'new_user_pass' => $pUpass,
			'new_user_mail' => $lUserEmail,
			'user_fullname' => $lUserFullName,
			'document_id' => (int)$pDocumentId,
			'usrfrom' => $lDocumentCreatorFullName,
			'autolog_hash' => $lUserAutologHash,
			'siteurl' => SITE_URL,
			'usrfrom_mail' => $lDocumentCreatorEmail,
			'requestdate' => date('d/m/Y H:i'),
			'mailsubject' => PENSOFT_MAILSUBJ_DOC_NEW_AUTHOR_REGISTER,
			'mailto' => getUserEmail((int)$pNewUserId),
			'charset' => 'UTF-8',
			'boundary' => '--_separator==_',
			'from' => array(
				'display' => PENSOFT_MAIL_DISPLAY,
				'email' => PENSOFT_MAIL_ADDR,
			),
			'templs' => array(
				G_DEFAULT => 'document.mail_document_add_newauthor_register',
			),
		);

		$msg = new cmessaging($mespubdata);
		$msg->Display();

		$mespubdata2 = array(
			'document_title' => $lDocumentTitle,
			'colaborate_role' => $lColaborateRole,
			'user_fullname' => $lUserFullName,
			'document_id' => (int)$pDocumentId,
			'usrfrom' => $lDocumentCreatorFullName,
			'siteurl' => SITE_URL,
			'usrfrom_mail' => $lDocumentCreatorEmail,
			'autolog_hash' => $lUserAutologHash,
			'requestdate' => date('d/m/Y H:i'),
			'mailsubject' => ((int)$pContributorFlag ? PENSOFT_MAILSUBJ_DOC_NEW_CONTRIBUTOR: PENSOFT_MAILSUBJ_DOC_NEW_AUTHOR),
			'mailto' => getUserEmail((int)$pNewUserId),
			'charset' => 'UTF-8',
			'boundary' => '--_separator==_',
			'from' => array(
				'display' => PENSOFT_MAIL_DISPLAY,
				'email' => PENSOFT_MAIL_ADDR,
			),
			'templs' => array(
				G_DEFAULT => ( (int)$pContributorFlag ? 'document.mail_document_add_contributor' :
										((int)$lAuthorRights == (int)AUTHOR_RIGHT_EDIT ? 'document.mail_document_add_author_edit' : 'document.mail_document_add_author_comment') ),
			),
		);

		$msg2 = new cmessaging($mespubdata2);
		$msg2->Display();
		$lSql = 'UPDATE pwt.instance_field_values SET value_int = 1 WHERE instance_id = ' . (int)$pInstanceId . ' AND field_id = ' . (int) AUTHOR_MAIL_NOTIFICATION_FIELD_ID;
		$lCon = new DBCn();
		$lCon->Open();
		$lCon->Execute($lSql);
		$lCon->Close();

	} elseif((int)$pUserExistsId && $lVal_id == 0 && $lCur_user <> 0) {
		$lCon = new DBCn();
		$lCon->Open();
		$lCon->Execute('SELECT coalesce(ut.name || \' \' || u.first_name || \' \' || u.last_name, u.uname) as fullname, u.autolog_hash as autolog_hash
						FROM public.usr u
						LEFT JOIN public.usr_titles ut ON ut.id = u.usr_title_id
						WHERE u.id = ' . (int)$pUserExistsId . '');
		$lUserFullName = $lCon->mRs['fullname'];
		$lUserAutologHash = $lCon->mRs['autolog_hash'];
		$lCon->Close();
		$mespubdata = array(
			'document_title' => $lDocumentTitle,
			'colaborate_role' => $lColaborateRole,
			'user_fullname' => $lUserFullName,
			'document_id' => (int)$pDocumentId,
			'usrfrom' => $lDocumentCreatorFullName,
			'siteurl' => SITE_URL,
			'usrfrom_mail' => $lDocumentCreatorEmail,
			'autolog_hash' => $lUserAutologHash,
			'requestdate' => date('d/m/Y H:i'),
			'mailsubject' => ((int)$pContributorFlag ? PENSOFT_MAILSUBJ_DOC_NEW_CONTRIBUTOR: PENSOFT_MAILSUBJ_DOC_NEW_AUTHOR),
			'mailto' => getUserEmail((int)$pUserExistsId),
			'charset' => 'UTF-8',
			'boundary' => '--_separator==_',
			'from' => array(
				'display' => PENSOFT_MAIL_DISPLAY,
				'email' => PENSOFT_MAIL_ADDR,
			),
			'templs' => array(
				G_DEFAULT => ( (int)$pContributorFlag ? 'document.mail_document_add_contributor' :
										((int)$lAuthorRights == (int)AUTHOR_RIGHT_EDIT ? 'document.mail_document_add_author_edit' : 'document.mail_document_add_author_comment') ),
			),
		);

		$msg = new cmessaging($mespubdata);
		$msg->Display();

		$lSql = 'UPDATE pwt.instance_field_values SET value_int = 1 WHERE instance_id = ' . (int)$pInstanceId . ' AND field_id = ' . (int) AUTHOR_MAIL_NOTIFICATION_FIELD_ID;
		$lCon = new DBCn();
		$lCon->Open();
		$lCon->Execute($lSql);
		$lCon->Close();
	}
}

/**
 * Попълва данните за дадеда референция от тип Journal Article
 * @param unknown_type $pInstanceId
 * @param unknown_type $pApiParsedResult - масив с информацията взета от апито
 * @throws Exception
 */
function fillJournalReferenceData($pReferenceInstanceId, $pApiParsedResult){
	function executeSqlQueryWithCurrentConnection($pSql){
		global $gCon;
		if(!$gCon->Execute($pSql)){
			throw new Exception(getstr($gCon->GetLastError()));
			$gCon->Execute('ROLLBACK TRANSACTION;');
			return false;
		}
	}

	$lPubYear = (int)$pApiParsedResult['pub_year'] ?: 'NULL';
	$lIssue = (int)$pApiParsedResult['issue'] ?: 'NULL';
	$uid = (int)$user->id;

	$lResult = array();
	global $gCon;
	$gCon = new DBCn();
	$gCon->Open();
	// Ще правим всичко в транзакция

	executeSqlQueryWithCurrentConnection('BEGIN TRANSACTION;');

	//Първо попълва field-овете
	$lSql = 'SELECT * FROM pwt."spUpdateJournalArticleReferenceFields"('
		. (int)$pReferenceInstanceId . ', '
		. $lPubYear . ', '
		. "'" . q($pApiParsedResult['article_title'])	. "', "
		. "'" . q($pApiParsedResult['journal'])			. "', "
		. "'" . q($pApiParsedResult['volume'])  		. "', "
		. $lIssue . ', '
		. "'" . q($pApiParsedResult['first_page'])		. "', "
		. "'" . q($pApiParsedResult['end_page'])		. "', "
		. "'" . q($pApiParsedResult['url']) 			. "', "
		. "'" . q($pApiParsedResult['doi']) 			. "'  "
	    . ')';
// 	var_dump($lSql);
	executeSqlQueryWithCurrentConnection($lSql);

	/*
	 * No longer necessary, editors are filtered at the XPath level.
	//Махаме едиторите
	$lSql = 'SELECT * FROM spRemoveJournalArticleReferenceEditors(' . (int)$pReferenceInstanceId . ', ' . $uid . ')';
	executeSqlQueryWithCurrentConnection($lSql);
	 *
	*/

	//Сега трябва да попълним авторите
	//За целта първо махаме старите
	$lSql = 'SELECT * FROM spRemoveJournalArticleReferenceAuthors(' . (int)$pReferenceInstanceId . ', ' . $uid . ')';
	executeSqlQueryWithCurrentConnection($lSql);
	//След това добавяме новите 1 х 1
	$lCurrentAuthorIdx = 1;
	foreach($pApiParsedResult['authors'] as $lCurrentAuthor){
		$lSql = 'SELECT * FROM spAddJournalArticleReferenceSingleAuthor(' . (int)$pReferenceInstanceId . ', '
		. '\'' . q($lCurrentAuthor['combined_name']) . '\'' . ', ' . (int)$lCurrentAuthorIdx++ . ', '
		. $uid . ')';

		executeSqlQueryWithCurrentConnection($lSql);
	}


	executeSqlQueryWithCurrentConnection('COMMIT TRANSACTION;');

	$lSql = 'SELECT p.id AS parent_instance_id, c.id as container_id
	FROM pwt.document_object_instances i
	JOIN pwt.document_object_instances p ON p.id = i.parent_id
	LEFT JOIN pwt.object_containers c ON c.object_id = p.object_id
	LEFT JOIN pwt.object_container_details cd ON cd.container_id = c.id AND cd.item_id = i.object_id AND cd.item_type = ' . (int) CONTAINER_ITEM_OBJECT_TYPE . '
	WHERE i.id = ' . (int) $pReferenceInstanceId . '

	';
	executeSqlQueryWithCurrentConnection($lSql);

	$gCon->MoveFirst();
	$lResult['parent_instance_id'] = (int)$gCon->mRs['parent_instance_id'];
	$lResult['container_id'] = (int)$gCon->mRs['container_id'];
	$lResult['reference_instance_id'] = $pReferenceInstanceId;

	return $lResult;
}

/**
 * Връщаме pmid-то, което отговаря на указаното pmcid
 * За повече информация - http://stackoverflow.com/questions/9229175/transform-pmc-id-pmid
 * @param unknown_type $pPmcId
 */
function getPubmedIdFromPMCId($pPmcId){
	$lUrl = PMC_FETCH_LINK . $pPmcId;
	$lApiResult = executeExternalQuery($lUrl);

	$lXml = new DOMDocument("1.0");


	if(!$lXml->loadXML($lApiResult)){
		return false;
	}

	$lXPath = new DOMXPath($lXml);
	$lArticleNode = $lXPath->query('/pmc-articleset/article/front/article-meta/article-id[@pub-id-type="pmid"]');
	if(!$lArticleNode->length){
		return false;
	}
	return $lArticleNode->item(0)->nodeValue;

}

/**
 * Връщаме instance_id-то на референцията, която съдържа обекта за търсене в pubmed/crossref
 * @param unknown_type $pSearchInstanceId
 * @throws Exception
 */
function getReferenceIdBySearchObjectInstanceId($pSearchInstanceId){
	$lCon = new DBCn();
	$lCon->Open();
	$lSql = 'SELECT p.id
	FROM pwt.document_object_instances i
	JOIN pwt.document_object_instances p ON p.object_id = ' . (int) REFERENCE_OBJECT_ID . ' AND substring(i.pos, 1, char_length(p.pos)) = p.pos AND p.document_id = i.document_id
	WHERE i.id = ' . (int) $pSearchInstanceId . '
	';
// 	var_dump($lSql);
	if(!$lCon->Execute($lSql)){
		return false;
	}
// 	var_dump($lCon->mRs['id']);
	return (int)$lCon->mRs['id'];
}

function getInstanceDocumentId($pInstanceId){
	$lCon = new DBCn();
	$lCon->Open();
	$lCon->Execute('SELECT document_id FROM pwt.document_object_instances WHERE id = ' . (int)$pInstanceId);
	return $lCon->mRs['document_id'];
}


function getDocumentReferencesPreview($pDocumentId){
	$lDocumentXml = getDocumentXml($pDocumentId);

	$lXslPath = PATH_XSL . '/template_example_reference_preview.xsl';

	$lXslParameters = array();
	$lXml = trim(transformXmlWithXsl($lDocumentXml, $lXslPath, $lXslParameters));
	return $lXml;
}

function getDocumentSupFilesPreview($pDocumentId){
	$lDocumentXml = getDocumentXml($pDocumentId);

	$lXslPath = PATH_XSL . '/sup_files_preview.xsl';

	$lXslParameters = array();
	$lXml = trim(transformXmlWithXsl($lDocumentXml, $lXslPath, $lXslParameters));
	return $lXml;
}

function getDocumentFiguresPreview($pDocumentId){
	$lDocumentXml = getDocumentXml($pDocumentId);

	$lXslPath = PATH_XSL . '/figures_preview.xsl';

	$lXslParameters = array();
	$lXml = trim(transformXmlWithXsl($lDocumentXml, $lXslPath, $lXslParameters));
	return $lXml;
}

function getDocumentTablesPreview($pDocumentId){
	$lDocumentXml = getDocumentXml($pDocumentId);

	$lXslPath = PATH_XSL . '/tables_preview.xsl';

	$lXslParameters = array();
	$lXml = trim(transformXmlWithXsl($lDocumentXml, $lXslPath, $lXslParameters));
	return $lXml;
}

function getReferencePreview($pReferenceInstanceId, $pParsedPubyear){
// 	echo 'Before ref ' . $pReferenceInstanceId . ' ' .  date("Y/m/d H:i:s"). substr((string)microtime(), 1, 6) . "\n";
// 	$lDocumentSerializer = new cdocument_serializer(array(
// 		'document_id' => getInstanceDocumentId($pReferenceInstanceId),
// 		'mode' => SERIALIZE_INTERNAL_MODE,
// 		'instance_id' => $pReferenceInstanceId
// 	));

// 	$lDocumentSerializer->GetData();

	$lReferenceXml = getDocumentXml(getInstanceDocumentId($pReferenceInstanceId));
// 	var_dump($lReferenceXml);
// 	$lReferenceXml = $lDocumentSerializer->getXml();
// 	echo 'After refxml ' . $pReferenceInstanceId . ' ' .  date("Y/m/d H:i:s"). substr((string)microtime(), 1, 6) . "\n";
	$docroot = getenv('DOCUMENT_ROOT');
	require_once($docroot . '/lib/static_xsl.php');

// 	return '<span>preview' . $pReferenceInstanceId . ' (' . $pParsedPubyear . ')</span>';

	$lXslParameters = array();

	$lXslPath = PATH_XSL . '/template_example_reference_preview.xsl';
	$lXslParameters[] = array(
		'namespace' => null,
		'name' => 'gParsedPubyear',
		'value' => $pParsedPubyear,
	);

	$lXslParameters[] = array(
		'namespace' => null,
		'name' => 'gReferenceId',
		'value' => $pReferenceInstanceId,
	);

// 	$lMode1Parameters = $lXslParameters;
// 	$lMode1Parameters[] = array(
// 		'namespace' => null,
// 		'name' => 'gPreviewType',
// 		'value' => 2,
// 	);

// 	$lMode2Parameters = $lXslParameters;
// 	$lMode2Parameters[] = array(
// 		'namespace' => null,
// 		'name' => 'gPreviewType',
// 		'value' => 3,
// 	);
// 	error_reporting(-1);


	// 	return $lDocumentSerializer->getXml();
	$lBasePreview = trim(transformXmlWithXsl($lReferenceXml, $lXslPath, $lXslParameters));
// 	$lCitationStyle1Preview = trim(transformXmlWithXsl($lReferenceXml, $lXslPath, $lMode1Parameters));
// 	$lCitationStyle2Preview = trim(transformXmlWithXsl($lReferenceXml, $lXslPath, $lMode2Parameters));

	$lResult = $lBasePreview;
// 	$lResult .= '<div class="hiddenElement" id="Ref-Preview-' . $pReferenceInstanceId . '-Mode-1">' . $lCitationStyle1Preview . '</div>' ;
// 	$lResult .= '<div class="hiddenElement" id="Ref-Preview-' . $pReferenceInstanceId . '-Mode-2">' . $lCitationStyle2Preview . '</div>' ;
// 	echo 'After ref ' . $pReferenceInstanceId . ' ' .  date("Y/m/d H:i:s"). substr((string)microtime(), 1, 6) . "\n";
	return $lResult;
}

function showMessageLink($pShowLabel, $pShowNum, $pShow, $pLinkTitle) {
	preg_match( '/t(\d+)/', $pShowNum, $lMatch );
	$lShow = (int) $lMatch[1];
	if((int)$pShow == (int)$lShow) {
		return $pLinkTitle;
	} else {
		return '<a href="/inbox.php?show=' . $lShow  . '">' . $pLinkTitle . '</a>';
	}
}

function getDocumentIdByInstanceId( $pInstanceId ){
	$lSql = 'SELECT document_id FROM pwt.document_object_instances WHERE id = ' . (int)$pInstanceId;
	$lCon = new DBCn();
	$lCon->Open();
	$lCon->Execute($lSql);
	return (int)$lCon->mRs['document_id'];
}

/*
 * Тази функция запазва xml-a на даден документ
 * Трябва да се използва навсякъде където правим някакъв екшън
*/
function saveDocumentXML( $pDocumentId ){
	if( (int)$pDocumentId ){
		$lCon = new DBCn();
		$lCon->Open();

		$lSql = 'SELECT xml_is_dirty::int as xml_is_dirty FROM pwt.documents WHERE id = ' . (int)$pDocumentId;

		$lCon->Execute($lSql);
		$lIsDirty = (int)$lCon->mRs['xml_is_dirty'];

		if($lIsDirty) {
			 $lDocumentSerializer = new cdocument_serializer(array(
					'document_id' => (int)$pDocumentId,
					'mode' => (int)SERIALIZE_INTERNAL_MODE,
			));
			$lDocumentSerializer->GetData();
			$lDocumentXml = $lDocumentSerializer->getXml();


// 			$lSql = 'UPDATE pwt.documents SET doc_xml = \'' . q($lDocumentXml) . '\'::xml WHERE id = ' . (int)$pDocumentId;
// 			$lCon->Execute($lSql);

// 			$lSql = 'SELECT * FROM pwt."XmlIsDirty"(2, ' . (int)$pDocumentId . ', null)';
// 			$lCon->Execute($lSql);
		}
	}
}

function getSearchSelectItems( $pDocumentId ){
	$lRet = '';

	if( (int)$pDocumentId ){
		$lRet .= '
			<option selected="selected" value="' . SEARCH_IN_ARTICLE . '">current article</option>
			<option value="2">All manuscripts</option>
			';
	}else{
		$lRet .= '
			<option selected="selected" value="' . SEARCH_IN_ALL_ARTICLES . '">All manuscripts</option>
			';
	}

	return $lRet;
}

function getMoreActivity( $pRecordCnt ){
	if( $pRecordCnt > ACTIVITY_RECORDS_PER_PAGE ){
		return '<div class="P-Activity-Fieed-See-All-Recent-Activity">
							<a href="javascript: void(0);" onclick="getNextActivityPage(1);">' . getstr('dashboard.see_more') . '<!-- See All Recent activity --></a>
				</div>';
	}
}

function pasteInstanceRightActionsCoverJS($pInstanceId, $pAllowRightActions){
	if($pAllowRightActions){
		return '<script type="text/javascript">initInstanceRightActionsEvents(' . $pInstanceId . ');
		</script>';
	}
}

function returnSortableMenuId($pObjectId, $pInstanceId) {
	if($pObjectId == (int)SYSTEMATICS_OBJECT_ID ||
		$pObjectId == (int)IDENTIFICATION_KEYS_OBJECT_ID ||
		$pObjectId == (int)ADD_CHECKLIST_LOCALITY_OBJECT_ID ||
		$pObjectId == (int)CHECKLIST_OBJECT_ID ||
		$pObjectId == (int)CHECKLIST_LOCALITY_OBJECT_ID ||
		$pObjectId == (int)CHECKLISTS_OBJECT_ID ||
		$pObjectId == (int)CHECKLIST2_OBJECT_ID ||
		$pObjectId == (int)INVENTORY_CHECKLIST_ID ||
		$pObjectId == (int)INVENTORY_LOCALITY_ID) {
		return ' id="sortable_' . $pObjectId . '_' . $pInstanceId . '" ';
	}
	return '';
}

function returnSortableMenuClass($pObjectId) {
	if($pObjectId == (int)SYSTEMATICS_OBJECT_ID ||
		$pObjectId == (int)IDENTIFICATION_KEYS_OBJECT_ID ||
		$pObjectId == (int)ADD_CHECKLIST_LOCALITY_OBJECT_ID ||
		$pObjectId == (int)CHECKLIST_LOCALITY_OBJECT_ID ||
		$pObjectId == (int)CHECKLIST_OBJECT_ID ||
		$pObjectId == (int)CHECKLISTS_OBJECT_ID ||
		$pObjectId == (int)CHECKLIST2_OBJECT_ID ||
		$pObjectId == (int)INVENTORY_CHECKLIST_ID ||
		$pObjectId == (int)INVENTORY_LOCALITY_ID) {
		return ' sortable ';
	}
	return '';
}

function returnSortableMenuDef($pObjectId, $pInstanceId) {
	if($pObjectId == (int)SYSTEMATICS_OBJECT_ID ||
		$pObjectId == (int)CHECKLIST_LOCALITY_OBJECT_ID ||
		$pObjectId == (int)ADD_CHECKLIST_LOCALITY_OBJECT_ID ||
		$pObjectId == (int)IDENTIFICATION_KEYS_OBJECT_ID ||
		$pObjectId == (int)CHECKLIST_OBJECT_ID ||
		$pObjectId == (int)CHECKLISTS_OBJECT_ID ||
		$pObjectId == (int)CHECKLIST2_OBJECT_ID ||
		$pObjectId == (int)INVENTORY_CHECKLIST_ID ||
		$pObjectId == (int)INVENTORY_LOCALITY_ID) {
		return '<script type="text/javascript">sortableMenu(' . $pObjectId . ', ' . $pInstanceId . ');</script>';
	}
	return '';
}

function replaceInstancePreviewField($pQuery, $pInstanceId) {
	if($pQuery && (int)$pInstanceId)
		return str_replace('{instance_id}', $pInstanceId, $pQuery);
	return '';
}

function getSearchStr( $pSearchStr ){
	if( $pSearchStr == '' || $pSearchStr == 'search_str' ){
		return getstr('pwt.defaultSearchLabel');
	}else
		return $pSearchStr;
}

function rm_url_param($pParam_rm, $pQuery=''){
	empty($pQuery)? $pQuery=$_SERVER['QUERY_STRING'] : '';
	parse_str($pQuery, $lParams);
	unset($lParams[$pParam_rm]);
	$lNewquery = '';
	foreach($lParams as $k => $v){
		$lNewquery .= '&'.$k.'='.$v;
	}
	return substr($lNewquery,1);
}


function getUserProfileImg( $pPhotoId ){
	if( (int)$pPhotoId ){
		return '/showimg.php?filename=c30x30y_' . (int)$pPhotoId . '.jpg';
	}
	return './i/user_no_img.png';
}

function UploadFile($pName, $pDir, $pDocumentId) {
	$gMaxSize = 5*1024*1024; // 5 MB
	/*
	$extarray = array(".doc", ".rtf", ".pdf", ".txt", ".zip", ".rar", ".xls", ".csv", ".tar");
	$typearr = array("text/plain", "text/richtext", "text/tab-separeted-values", "application/pdf", "application/rtf", "application/word", "application/zip", "application/x-tar", "application/x-rar-compressed", "application/x-rar", "application/msword", "application/vnd.ms-excel");
	*/
	global $user;

	if ($_FILES[$pName]['name']) {
		$pFnUpl = $_FILES[$pName]['name'];
		$pTitle = $pTitle;
		$gFileExt = substr($_FILES[$pName]['name'], strrpos($_FILES[$pName]['name'], '.'));
		$lResult = array (
			'file_id' => '',
			'file_name' => '',
		);
		/*
		$isImageExtension = in_array(strtolower($gFileExt), $extarray);
		$isImageMime = in_array(strtolower($_FILES[$name]['type']), $typearr);
		if ($isImageExtension && $isImageMime) {
		*/
		if (true) {
			if ($_FILES[$pName]['size'] > $gMaxSize) {
				$pError = 'Документът е твърде голям! Максимален размер: 5MB'  . ($gMaxSize / (1024 * 1024)). ' MB';
			} elseif (!$_FILES[$pName]["size"]) {
				$pError = 'Невалиден документ!';
			} elseif ($_FILES[$pName]['error'] == UPLOAD_ERR_OK) {
				$lCn = Con() ;
				$lCn->Execute('SELECT spFileUpload(1, null, ' . (int)$user->id. ',' . (int)$pDocumentId . ', \'' . q($pFnUpl) . '\', \'' . q($pFnUpl) . '\', \'' . q(strtolower($_FILES[$pName]['type'])) . '\') as file_id');
				$lCn->MoveFirst();
				$lResult['file_id'] = (int)$lCn->mRs['file_id'];
				$lResult['file_name'] = $pFnUpl;
				if ($lResult['file_id']) {
					if (!move_uploaded_file($_FILES[$pName]['tmp_name'], $pDir . 'oo_' . $lResult['file_id'] . $gFileExt)) {
						$pError = 'Грешка: ' . $_FILES[$pName]['error'];
					} else {
						$lCn = Con() ;
						$lCn->Execute('UPDATE pwt.media SET original_name = \'' . q('oo_' . $lResult['file_id'] . $gFileExt) . '\' WHERE id = ' . $lResult['file_id']);
						$lCn->MoveFirst();
						// Vsichko e ok...
						$imgUploadErr = 0;
					}
				} else {
					$pError = 'Неочаквана грешка в базата данни!';
				}
				return $lResult;
			} else {
				$pError = $_FILES[$pName]['name'] . ' Грешка при запазването на документа!';
			}
		} else {
			//~ $kfor->SetError($_FILES[$name]['name'], 'Невалиден тип документа! Позволените типове са: .doc, .rtf, .pdf, .txt, .zip, .rar, .xls, .csv, .tar');
		}
	} else {
		//~ $kfor->SetError('Документ', 'Не сте добавили документ');
	}


	//~ if ($picid) $lCn->Execute('SELECT AttUpload(3, ' . (int)$picid . ', null, null, null, null, null);');
	return false;
}

function updateInstanceFieldValue($pInstanceId, $pFieldId, $pValue, $pFieldType) {
	if((int)$pValue) {
		$lSql = 'UPDATE pwt.instance_field_values SET value_int = ' . (int)$pValue . ' WHERE instance_id = ' . (int)$pInstanceId . ' AND field_id = ' . (int) $pFieldId;
		$lCon = new DBCn();
		$lCon->Open();
		$lCon->Execute($lSql);
		$lCon->Close();
	}
}

function getAddParticipantsButton($pRowsNum = 0){
	if($pRowsNum){
		return '
		<div style="margin: 30px;">
			<div class="P-Grey-Btn-Holder P-Add" onclick="toggleChecked(\'P-Project-Participants-Holder\');">
				<div class="P-Grey-Btn-Left"></div>
				<div class="P-Grey-Btn-Middle"><div class="P-Btn-Icon"></div>Add all</div>
				<div class="P-Grey-Btn-Right"></div>
			</div>
			<div class="P-Clear"></div>
		</div>';
	}
}

function getUploadedFileIdAndName($pDocumentId, $pInstanceId, $pFieldType = FILE_UPLOAD_FIELD_ID) {
	$lQuery = 'SELECT ifv.value_int as file_id , m.title as file_name
				FROM pwt.instance_field_values ifv
				JOIN pwt.media m ON  m.id = ifv.value_int
				WHERE ifv.document_id = ' . (int)$pDocumentId . ' and ifv.field_id = ' . (int)$pFieldType . ' and ifv.instance_id = ' . (int)$pInstanceId;
	$gCn = new DBCn();
	$gCn->Open();
	$gCn->Execute($lQuery);
	$gCn->MoveFirst();
	$lSrcValues = array();
	while(!$gCn->Eof()) {
		$lSrcValues['file_id'] = $gCn->mRs['file_id'];
		$lSrcValues['file_name'] = $gCn->mRs['file_name'];
		$gCn->MoveNext();
	}
	return $lSrcValues;
}

function saveInstanceFieldValue($pInstanceId, $pFieldId, $pValue) {
	// Взимаме типа на полето от базата за да знаем кво да проверяваме
	$lSql = 'SELECT ft.* FROM pwt.fields f
				JOIN pwt.field_types ft ON ft.id = f.type
				WHERE f.id = ' . (int)$pFieldId;

	$lCon = new DBCn();
	$lCon->Open();
	$lCon->Execute($lSql);
	$lCon->MoveFirst();
	$lFieldTypeId = (int)$lCon->mRs['id'];
	$lFieldValueColumnName = $lCon->mRs['value_column_name'];
	$lCon->Close();

	// Проверяваме стойността дали е от типа който трябва и я подготвяме за заявката
	$pValue = prepareFieldValueForQuery($lFieldTypeId, $pValue);

	$lSql = 'UPDATE pwt.instance_field_values SET ' . $lFieldValueColumnName . ' = ' . $pValue . ' WHERE instance_id = ' . (int)$pInstanceId . ' AND field_id = ' . (int) $pFieldId;
	$lCon->Open();
	$lResult = $lCon->Execute($lSql);
	$lCon->Close();
	return $lResult;
}

function prepareFieldValueForQuery($pFieldTypeId, $pFieldValue) {
	$lBaseValue = $pFieldValue;
	$lFieldType = $pFieldTypeId;

	if (is_array( $lBaseValue )) {

		if ($lFieldType == FIELD_CHECKBOX_MANY_TO_STRING_TYPE) {
			$lRetStr = "'" . implode( DEF_SQLSTR_SEPARATOR, array_map( 'q', $lBaseValue ) ) . DEF_SQLSTR_SEPARATOR . "'";
		} elseif ($lFieldType == FIELD_CHECKBOX_MANY_TO_SQL_STRING_ARRAY_TYPE) {
			$lRetStr = "array[" . implode( DEF_SQLSTR_SEPARATOR, array_map( 'arrstr_q', $lBaseValue ) ) . "]::varchar[]";
		} elseif ($lFieldType == FIELD_CHECKBOX_MANY_TO_SQL_INT_ARRAY_TYPE) {
			$lRetStr = "array[" . implode( DEF_SQLSTR_SEPARATOR, array_map( 'intThis', $lBaseValue ) ) . "]::int[]";
			var_dump($lRetStr);
		} elseif ($lFieldType == FIELD_CHECKBOX_MANY_TO_SQL_DATE_ARRAY_TYPE) {
			$lTmpArr = array();
			foreach ( $lBaseValue as $k => $v ) {
				$lTmpArr [] = manageckdate( $v, DATE_TYPE_DATE, 0 );
			}
			$lRetStr = "array[" . implode( DEF_SQLSTR_SEPARATOR, $lTmpArr ) . "]::date[]";
		} elseif ($lFieldType == FIELD_CHECKBOX_MANY_TO_BIT_TYPE) {
			$lRetStr = array2bitint( $lBaseValue );
		} elseif ($lFieldType == FIELD_CHECKBOX_MANY_TO_BIT_ONE_BOX_TYPE) {
			if (is_null( $lBaseValue ) || $lBaseValue === '') {
				$lRetStr = 0;
			} else
				$lRetStr = $lBaseValue;
		}
	} else {
		if ($lFieldType == FIELD_INT_TYPE) {
			$lRetStr = (int)$lBaseValue;
		} else if ($lFieldType == FIELD_DATE_TYPE) {
			$lRetStr = "'" . q( manageckdate( $lBaseValue, DATE_TYPE_DATE, 0 ) ) . "'";
		} else if (in_array($lFieldType,
			array(FIELD_CHECKBOX_MANY_TO_SQL_DATE_ARRAY_TYPE, FIELD_CHECKBOX_MANY_TO_SQL_INT_ARRAY_TYPE, FIELD_CHECKBOX_MANY_TO_SQL_STRING_ARRAY_TYPE ))) {//Невалидна array стойност
			$lRetStr = 'NULL';
		} else {
			$lRetStr = "'" . q( $lBaseValue ) . "'";
		}
	}

	return $lRetStr;
}

function intThis($pValue){
	return (int)$pValue;
}

function countDocumentComments( $pDocumentId ){
	$lQuery = 'SELECT count(id) as countcomments FROM pwt.msg WHERE  document_id = ' . (int)$pDocumentId;
	$gCn = new DBCn();
	$gCn->Open();
	$gCn->Execute($lQuery);
	$gCn->MoveFirst();
	return (int)$gCn->mRs['countcomments'];
}

function checkProfileEdit($pKforFieldVals) {
	if((int)$pKforFieldVals['editprof'])
		return 'style="display:none;"';
	return '';
}

function getProfileEditStepOneMessage($pKforFieldVals) {
	if((int)$pKforFieldVals['editprof'])
		return getstr('pwt.profile.editstepone');
	return getstr('pwt.registration.stepone');
}

function getProfileEditStepTwoMessage($pKforFieldVals) {
	if((int)$pKforFieldVals['editprof'])
		return getstr('pwt.profile.editsteptwo');
	return getstr('pwt.registration.steptwo');
}

function getProfileEditStepThreeMessage($pKforFieldVals) {
	if((int)$pKforFieldVals['editprof'])
		return getstr('pwt.profile.editstepthree');
	return getstr('pwt.registration.stepthree');
}

function getInstanceIdByObjectId($pDocumentId, $pObjectId) {
	$lQuery = 'SELECT id FROM pwt.document_object_instances WHERE document_id = ' . (int)$pDocumentId . ' AND object_id = ' . (int)$pObjectId;
	$gCn = new DBCn();
	$gCn->Open();
	$gCn->Execute($lQuery);
	$gCn->MoveFirst();
	if((int)$gCn->mRs['id'])
		return $gCn->mRs['id'];
	return '';
}

/**
 * Проверява дали документа е заключен от текущия потребител.
 * Ако документа е отключен или е последно заключен от този потребител - заключва го наново.
 * @param unknown_type $pInstanceId
 * @param unknown_type $pDocumentId
 * @return връща true ако документа е успешно заключен от текущия потребител и false в противен случай.
 */
function checkIfDocumentIsLockedByTheCurrentUser($pInstanceId = 0, $pDocumentId = 0){
	return true;
	if(!(int)$pInstanceId && !(int)$pDocumentId){
		return false;
	}
	if(!(int)$pDocumentId){
		$pDocumentId = getDocumentIdByInstanceId($pInstanceId);
	}

	return lockDocument($pDocumentId);
}


function checkIfDocumentIsLockedByTheCurrentUserForAjax($pInstanceId = 0, $pDocumentId = 0){
	if(! checkIfDocumentIsLockedByTheCurrentUser($pInstanceId, $pDocumentId)){
		$lResult = array(
			'err_cnt' => 1,
			'err_msg' => getstr('pwt.thisDocumentIsNotLockedByYou')
		);
		displayAjaxResponse($lResult);
	}
}

function lockDocument($pDocumentId){
	if(!(int)$pDocumentId){
		return false;
	}
	global $user;

	$lSql = 'SELECT * FROM pwt.spLockDocument(' . q($pDocumentId) . ', ' . (int)LOCK_AUTO_LOCK . ', ' . 2 * (int) DOCUMENT_LOCK_TIMEOUT_INTERVAL . ', ' . (int) DOCUMENT_AUTO_UNLOCK_INTERVAL . ', ' . q($user->id) . ') as res';
	$lCon = new DBCn();
	$lCon->Open();
	$lResult = array(
		'err_cnt' => 0,
		'err_msg' => ''
	);
	if(!$lCon->Execute($lSql) || !(int)$lCon->mRs['res']){
		return false;
	}
	return true;
}

function initAutocompleteAndBuildTree( $pTreeType, $pHtmlIdentifier ){
	$t = array(
			'html_control_type' => $pTreeType,
			'field_html_identifier' => $pHtmlIdentifier,
		);
	$lTree = new cfield_taxon_classification_autocomplete_script($t);
	return $lTree->Display();
}

function getRevisionXML($pDocumnetId, $pRevisionId){
	$lSql = 'SELECT * FROM pwt.document_revisions WHERE id = ' . (int)$pRevisionId . ' AND document_id = ' . (int)$pDocumnetId;
	$lCon = new DBCn();
	$lCon->Open();
	if(!$lCon->Execute($lSql) || !(int)$lCon->mRs['id']){
		$lSql = 'SELECT *
					FROM pwt.document_revisions
					WHERE document_id = ' . (int)$pDocumnetId . '
					ORDER BY createdate DESC
					LIMIT 1';
		$lCon->Execute($lSql);
	}

	return $lCon->mRs['doc_xml'];
}

function RetOldPjsLogoutImg($pLogout) {
	if((int)$pLogout) {
		return '<img src="' . OLD_PJS_SITE_URL . '/logout.php" width="1" height="1" border="0" alt="" />';
	}
	return '';
}

function to_xhtml($str)
{
	return htmlspecialchars($str, ENT_XHTML | ENT_SUBSTITUTE | ENT_QUOTES, 'UTF-8', false);
}

function strim($pwtTitle)
{
		return trim(strip_tags($pwtTitle, '<em>'));
}

function CreateNewUserToMysql($pUserId, $pUpass) {
	if((int)$pUserId && $pUpass) {

		$cn = new DbCn();
		$cn->Open();

		$cn->Execute('SELECT u.*, ut.name as salut, ct.name as ctip, c.name as country
					FROM usr u
					LEFT JOIN usr_titles ut ON  ut.id = u.usr_title_id
					LEFT JOIN client_types ct ON ct.id = u.client_type_id
					LEFT JOIN countries c ON c.id = u.country_id
					WHERE u.id = ' . (int)$pUserId);
		$cn->MoveFirst();

		$lCon = new DbCn(MYSQL_DBTYPE);
		$lCon->Open(MYSQL_DB_SRV, MYSQL_DB, MYSQL_USR, MYSQL_PASS);

		$lCon->Execute('CALL spRegUsrStep1(NULL, 1, \'' . q($cn->mRs['uname']) . '\', \'' . q($pUpass) . '\')');
		$lCon->MoveFirst();
		$lOldPjsCid = (int)$lCon->mRs['CID'];

		$lCon->Close();

		$lCon = new DbCn(MYSQL_DBTYPE);
		$lCon->Open(MYSQL_DB_SRV, MYSQL_DB, MYSQL_USR, MYSQL_PASS);
		$lCon->Execute('CALL spRegUsrStep2(
											' . (int)$lOldPjsCid . ',
											1,
											\'' . q($cn->mRs['first_name']) . '\',
											\'' . q($cn->mRs['middle_name']) . '\',
											\'' . q($cn->mRs['last_name']) . '\',
											\'' . q($cn->mRs['salut']) . '\',
											\'' . q($cn->mRs['ctip']) . '\',
											\'' . q($cn->mRs['affiliation']) . '\',
											\'' . q($cn->mRs['department']) . '\',
											\'' . q($cn->mRs['addr_street']) . '\',
											\'' . q($cn->mRs['addr_postcode']) . '\',
											\'' . q($cn->mRs['addr_city']) . '\',
											\'' . q($cn->mRs['country']) . '\',
											\'' . q($cn->mRs['phone']) . '\',
											\'' . q($cn->mRs['fax']) . '\',
											\'' . q($cn->mRs['vat']) . '\',
											\'' . q($cn->mRs['webiste']) . '\')');
		$lCon->MoveFirst();
		$lCon->Close();

		$cn->Execute('SELECT * FROM spSaveOldPJSId(\'' . q($cn->mRs['uname']) . '\', ' . (int)$lOldPjsCid . ')');

		$cn->Close();
	}
}

function prepareDateFieldForXSDValidation($pDate){
	if (!preg_match('/[\/\\\.\-]/', $pDate, $lMatches)) {
		return $pDate;
	}

	$lMatchFlag = 0;
	$lSeparator = $lMatches[0]; //Kato nqma skobi v reg expa v 0-q element e kakvoto e machnalo

	if(preg_match('/^(\d{2,4})\\' . $lSeparator . '(\d{1,2})\\' . $lSeparator . '(\d{1,2})$/i', $pDate, $lMatches)){
		$lMatchFlag = 1;
	}

	if(!$lMatchFlag) {
		if(preg_match('/^(\d{1,2})\\' . $lSeparator . '(\d{1,2})\\' . $lSeparator . '(\d{2,4})$/i', $pDate, $lMatches)){
			$lMatchFlag = 2;
		}
	}

	if(!$lMatchFlag){
		return $pDate;
	}

	if($lMatchFlag == 1) {
		$lTimeFormat = mktime(null, null, null, $lMatches[2], $lMatches[3], $lMatches[1]);
	} else {
		$lTimeFormat = mktime(null, null, null, $lMatches[2], $lMatches[1], $lMatches[3]);
	}

	// Форматът, в който трябва да бъде датата, за да мине валидацията
	// 2001-10-26T19:32:52Z

	return date('c', $lTimeFormat);
}

function CreateNewTTMaterialFromSpreadSheet($pName, $pDir, $pDocumentId) {
	return CreateTempUploadedFile($pName, $pDir, $pDocumentId, '_material_');
}

function CreateNewChecklistTaxonFromSpreadSheet($pName, $pDir, $pDocumentId) {
	return CreateTempUploadedFile($pName, $pDir, $pDocumentId, '_checklist_taxon_');
}

function CreateNewTaxonomicCoverageTaxaFromSpreadSheet($pName, $pDir, $pDocumentId) {
	return CreateTempUploadedFile($pName, $pDir, $pDocumentId, '_taxonomic_coverage_taxa_');
}

function CreateTempUploadedFile($pName, $pDir, $pDocumentId, $pPrefix = ''){
	$gMaxSize = 5*1024*1024; // 5 MB

	if ($_FILES[$pName]['name']) {
		$pFnUpl = $_FILES[$pName]['name'];
		$gFileExt = substr($_FILES[$pName]['name'], strrpos($_FILES[$pName]['name'], '.'));
		$lResult = array (
			'file_name' => '',
			'err' => '',
		);
		/*
			$isImageExtension = in_array(strtolower($gFileExt), $extarray);
		$isImageMime = in_array(strtolower($_FILES[$name]['type']), $typearr);
		if ($isImageExtension && $isImageMime) {
		*/
		if ($_FILES[$pName]['size'] > $gMaxSize) {
			$lResult['err'] = getstr('pwt.fileTooLarge') . getstr('pwt.maxAllowedSizeIs') . ($gMaxSize / (1024 * 1024)). ' MB';
		} elseif (!$_FILES[$pName]["size"]) {
			$lResult['err'] = getstr('pwt.wrongFile');
		} elseif ($_FILES[$pName]['error'] == UPLOAD_ERR_OK) {

			$lDateStr = date("Y_m_d_H_i_s");
			$lResult['file_name'] = $pDocumentId . $pPrefix . $lDateStr . $gFileExt;

			if (!move_uploaded_file($_FILES[$pName]['tmp_name'], $pDir . $lResult['file_name'])) {
				$lResult['err'] = 'Грешка: ' . $_FILES[$pName]['error'];
			}

		} else {
			$lResult['err'] = $_FILES[$pName]['name'] . getstr('pwt.couldNotSaveFile');
		}

	} else {
		$lResult['err'] = getstr('pwt.noFileSelected');
	}

	return $lResult;
}

function array_empty($pArray) {
    if (is_array($pArray)) {
        foreach ($pArray as $value) {
            if (!array_empty($value)) {
                return false;
            }
        }
    }
    elseif (!empty($pArray)) {
        return false;
    }
    return true;
}

function recursive_array_search($needle, $haystack) {
    foreach($haystack as $key => $value) {
        $current_key = $key;
        if((!is_array($value) && $needle === strtolower($value)) OR (is_array($value) && recursive_array_search($needle, $value) !== false)) {
            return $current_key;
        }
    }
    return false;
}
function CheckCaptcha ($pCaptcha) {
	if (!in_array(strtolower($pCaptcha), $_SESSION['frmcapt'])) {
		return getstr('regprof.captchaerr');
	}
}

function displayEditPreviewHeader($pDocumentId, $pRevisionId, $pHeaderInIframe = true){
	$lCanEditPreview = checkIfPreviewCanBeEdited($pDocumentId, $pRevisionId);
// 	$lCanEditPreview = true;
	$lResult = '';

	if(!$lCanEditPreview){
		return $lResult;
	}
	$lTemplate = 'preview.editHeader';

	if(!checkIfDocumentHasUnprocessedChangesSimple($pDocumentId)){
		$lTemplate = 'preview.editHeaderWithoutChanges';
	}

	if($pHeaderInIframe){
		$lTemplate .= 'Iframe';
	}

// 	var_dump($pDocumentId, $pRevisionId);
	$lResult = new csimple(array(
		'templs' => array(
			G_DEFAULT => $lTemplate,
		),
		'document_id' => $pDocumentId,
		'revision_id' => $pRevisionId,
		'legend' => GetVersionUserLegend($pDocumentId, $pRevisionId),
	));

	return $lResult->Display();
}

function GetVersionUserLegend($pDocumentId, $pVersionId){
	global $user;
	$lSql = '
		SELECT DISTINCT ON (id) * FROM (
			(SELECT DISTINCT u.id, coalesce(u.first_name, \'\') || \' \' || coalesce(u.last_name, \'\') as user_name,
				u.id as undisclosed_real_usr_id, null as undisclosed_user_fullname, 1 as is_disclosed
				FROM pwt.pjs_revision_details rd
				JOIN public.usr u ON u.id = ANY (rd.change_user_ids)
			WHERE rd.revision_id = ' . (int) $pVersionId . ')
		UNION
			(SELECT DISTINCT uu.id, coalesce(u.first_name, \'\') || \' \' || coalesce(u.last_name, \'\') as user_name,
				u.id as undisclosed_real_usr_id, uu.name as undisclosed_user_fullname, 0 as is_disclosed
			FROM pwt.pjs_revision_details rd
			JOIN public.undisclosed_users uu ON uu.id = ANY (rd.change_user_ids)
			JOIN public.usr u ON u.id = uu.uid
			WHERE rd.revision_id = ' . (int) $pVersionId . ')
		UNION
			(SELECT DISTINCT coalesce(uu.id, u.id) as id, coalesce(u.first_name, \'\') || \' \' || coalesce(u.last_name, \'\') as user_name,
				u.id as undisclosed_real_usr_id, uu.name as undisclosed_user_fullname, m.is_disclosed::int as is_disclosed
			FROM pwt.msg m
			LEFT JOIN public.undisclosed_users uu ON uu.id = m.undisclosed_usr_id
			JOIN public.usr u ON u.id = m.usr_id
			WHERE m.revision_id = spGetDocumentLatestCommentRevisionId(' .  (int)$pDocumentId . ', 0))
		UNION
			(SELECT DISTINCT u.id as id, coalesce(u.first_name, \'\') || \' \' || coalesce(u.last_name, \'\') as user_name,
				u.id as undisclosed_real_usr_id, null as undisclosed_user_fullname, 0 as is_disclosed
			FROM public.usr u
			WHERE u.id = ' . (int)$user->id . ')
	) a
		';
// 	var_dump($lSql);

	$lLegend = new crs(array(
		'sqlstr' => $lSql,
		'current_user_id' => $user->id,
		'templs' => array(
			G_STARTRS => 'preview.user_legend_start',
			G_ROWTEMPL => 'preview.user_legend_row',
			G_ENDRS => 'preview.user_legend_end',
		),
	));
// 	var_dump($lLegend->Display());
	return $lLegend->Display();
}

function GetVersionUserDisplayNames($pVersionId = 0, $pDocumentId = 0){
	global $user;
	$lCon = new DBCn();
	$lCon->Open();

	if(!$pVersionId){
		$lSql = 'SELECT max(revision_id) as revision_id
			FROM pwt.pjs_revision_details
			WHERE document_id = ' . (int)$pDocumentId;
		$lCon->Execute($lSql);
		$pVersionId = (int)$lCon->mRs['revision_id'];
	}

// 	var_dump($pVersionId);
	$lSql = '
			(SELECT DISTINCT u.id, coalesce(u.first_name, \'\') || \' \' || coalesce(u.last_name, \'\') as user_name,
				u.id as undisclosed_real_usr_id, null as undisclosed_user_fullname, 1 as is_disclosed
				FROM pwt.pjs_revision_details rd
				JOIN public.usr u ON u.id = ANY (rd.change_user_ids)
			WHERE rd.revision_id = ' . (int) $pVersionId . ')
		UNION
			(SELECT DISTINCT uu.id, coalesce(u.first_name, \'\') || \' \' || coalesce(u.last_name, \'\') as user_name,
				u.id as undisclosed_real_usr_id, uu.name as undisclosed_user_fullname, 0 as is_disclosed
			FROM pwt.pjs_revision_details rd
			JOIN public.undisclosed_users uu ON uu.id = ANY (rd.change_user_ids)
			JOIN public.usr u ON u.id = uu.uid
			WHERE rd.revision_id = ' . (int) $pVersionId . ')
		';
	$lResult = array();
	$lCon->Execute($lSql);
	while(!$lCon->Eof()){
		$lId = (int)$lCon->mRs['id'];
		$lIsDisclosed = (int)$lCon->mRs['is_disclosed'];
		$lUnDisclosedUserName = $lCon->mRs['undisclosed_user_fullname'];
		$lRealUserId = (int)$lCon->mRs['undisclosed_real_usr_id'];
		$lRealUserName = $lCon->mRs['user_name'];
		$lDisplayName = DisplayCommentUserName($lIsDisclosed, $lRealUserId, $user->id, $lRealUserName, $lUnDisclosedUserName);
		$lResult[$lId] = $lDisplayName;
		$lCon->MoveNext();
	}
// 	var_dump($lResult);
// 	var_dump($lSql);
	return $lResult;
}

/**
 * Accept or reject all the changes in the specified document.
 * If an error occurs an exception will be thrown
 * @param unknown_type $pDocumentId
 * @param unknown_type $pAccept
 * @throws Exception
 */
function AcceptRejectAllChanges($pDocumentId, $pAccept = true){
	require_once PATH_CLASSES . 'comments.php';
	global $user;
	$lCon = new DBCn();
	$lCon->Open();

	$lDocumentXml = getDocumentXml($pDocumentId, SERIALIZE_INTERNAL_MODE, false, false, 0, false, true);
	$lDom = new DOMDocument('1.0', DEFAULT_XML_ENCODING);
	$lDomCopy = new DOMDocument('1.0', DEFAULT_XML_ENCODING);
	if(!$lDom->loadXML($lDocumentXml) || !$lDomCopy->loadXML($lDocumentXml)){
		throw new Exception(getstr('pwt.couldNotLoadDocumentXml'));
	}
	$lXPath = new DOMXPath($lDom);
	$lXPathCopy = new DOMXPath($lDomCopy);
	$lChangeNodesQuery = '//' . VERSION_DELETE_CHANGE_NODE_NAME . '|//' . VERSION_INSERT_CHANGE_NODE_NAME . '';
	$lChangeNodes = $lXPath->query($lChangeNodesQuery);
	//An array which keeps track of all the modified fields of the modified instances
	$lModifiedInstances = array();


	//First process all changes and log which fields have been changed
	for($i = $lChangeNodes->length - 1; $i >= 0; --$i){
		$lChangeNode = $lChangeNodes->item($i);
		$lInstanceParent = $lXPath->query('./ancestor::*[@instance_id][1]', $lChangeNode);//The first ancestor with instance_id
		$lFieldParent = $lXPath->query('./ancestor::*[@id][value][1]', $lChangeNode);//The first ancestor which has an id (field id) and a value subnode
		$lInstanceId = 0;
		$lFieldId = 0;
		if($lInstanceParent->length && $lFieldParent->length){
			$lInstanceId = $lInstanceParent->item(0)->getAttribute('instance_id');
			$lFieldId = $lFieldParent->item(0)->getAttribute('id');
		}
		if($lInstanceId && $lFieldId){
			if(!is_array($lModifiedInstances[$lInstanceId])){
				$lModifiedInstances[$lInstanceId] = array();
			}
			if(!in_array($lFieldId, $lModifiedInstances[$lInstanceId])){
				$lModifiedInstances[$lInstanceId][] = $lFieldId;
			}
		}
		if($pAccept){//Accept
			if($lChangeNode->nodeName == VERSION_DELETE_CHANGE_NODE_NAME){//Accept a delete change - remove the node
				$lChangeNode->parentNode->removeChild($lChangeNode);
			}else{//Accept an insert change - remove the tag and keep the children
				while($lChangeNode->hasChildNodes()){
					$lChangeNode->parentNode->insertBefore($lChangeNode->firstChild, $lChangeNode);
				}
				$lChangeNode->parentNode->removeChild($lChangeNode);
			}
		}else{//Reject
			if($lChangeNode->nodeName == VERSION_INSERT_CHANGE_NODE_NAME){//Reject an insert change - remove the node
				$lChangeNode->parentNode->removeChild($lChangeNode);
			}else{//Reject an delete change - remove the tag and keep the children
				while($lChangeNode->hasChildNodes()){
					$lChangeNode->parentNode->insertBefore($lChangeNode->firstChild, $lChangeNode);
				}
				$lChangeNode->parentNode->removeChild($lChangeNode);
			}
		}

	}
	$lInstanceFieldComments = array();
	foreach ($lModifiedInstances as $lInstanceId => $lInstanceFields){
		foreach ($lInstanceFields as $lFieldId){
			if(!array_key_exists($lInstanceId, $lInstanceFieldComments)){
				$lInstanceFieldComments[$lInstanceId] = array();
			}
			$lInstanceFieldComments[$lInstanceId][$lFieldId] = GetFieldComments($lInstanceId, $lFieldId);
		}
	}

	if(!$lCon->Execute('BEGIN TRANSACTION;')){
		throw new Exception(getstr('pwt.couldNotStartTransaction'));
	}
// 	echo 1;
	//Update the values of the changed fields
	foreach ($lModifiedInstances as $lInstanceId => $lInstanceFields){
		foreach ($lInstanceFields as $lFieldId){
			$lFieldQuery = '//*[@instance_id=\'' . $lInstanceId . '\']/fields/*[@id=\'' . $lFieldId .'\']';
			$lFieldValueQuery = '//*[@instance_id=\'' . $lInstanceId . '\']/fields/*[@id=\'' . $lFieldId .'\']/value';
			$lFieldXmlNode = $lXPath->query($lFieldQuery);
			$lFieldOrigValueXmlNode = $lXPathCopy->query($lFieldValueQuery);
			if($lFieldXmlNode->length){
				$lFieldValueNode = $lXPath->query($lFieldValueQuery);

				$lFieldNewValue = getFieldInnerXML($lFieldValueNode->item(0));
				$lFieldPreviousValue = '';
				if($lFieldOrigValueXmlNode->length){
					$lFieldPreviousValue = getFieldInnerXML($lFieldOrigValueXmlNode->item(0));
				}
				$lModifiedCommentPositions = GetModifiedCommentPositions($lFieldPreviousValue, $lFieldNewValue, $lInstanceFieldComments[$lInstanceId][$lFieldId]);
				foreach ($lModifiedCommentPositions as $lCommentId => $lCommentData) {
					$lSql = 'UPDATE pwt.msg SET ';

					if($lCommentData['position_fix_type'] & COMMENTS_FIX_TYPE_START_POS){
						$lSql .= 'start_offset = ' . (int)$lCommentData['new_start_offset'];
					}
					if($lCommentData['position_fix_type'] & COMMENTS_FIX_TYPE_END_POS){
						if($lCommentData['position_fix_type'] & COMMENTS_FIX_TYPE_START_POS){
							$lSql .= ', ';
						}
						$lSql .= 'end_offset = ' . (int)$lCommentData['new_end_offset'];
					}

					$lSql .= 'WHERE id = ' . (int)$lCommentId;
					if(!$lCon->Execute($lSql)){
						throw new Exception(getstr('pwt.couldNotSaveCommentPosition'));
					}
				}

				RemoveFieldNodeCommentNodes($lFieldXmlNode->item(0));
// 				var_dump($lInstanceFieldComments[$lInstanceId][$lFieldId], $lFieldPreviousValue, $lFieldNewValue, $lModifiedCommentPositions);
				$lFieldXml = $lDom->saveXML($lFieldXmlNode->item(0));


				$lSql = 'SELECT * FROM spSaveInstanceFieldFromXml(' . (int)$lInstanceId . ', ' . (int)$lFieldId . ', \'' . q($lFieldXml) . '\', ' . (int)$user->id . ')';
				if(!$lCon->Execute($lSql)){
					$lCon->Execute('ROLLBACK TRANSACTION;');
					throw new Exception(getstr($lCon->GetLastError()));
				}
			}
		}
	}
// 	$lCon->Execute('ROLLBACK TRANSACTION;');
// 	throw new Exception('asd');


// 	var_dump($lModifiedInstances);
	//Store the previous revision
	$lSql = 'SELECT * FROM pwt.spSaveDocumentRevision(' . (int)$pDocumentId . ', ' . (int)$user->id . ') as revision_id';
	if(!$lCon->Execute($lSql)){
		$lCon->Execute('ROLLBACK TRANSACTION;');
		throw new Exception(getstr($lCon->GetLastError()));
	}
	//Save the document xml
	$lDocumentXml = $lDom->saveXML();
// 	var_dump($lDocumentXml);
// 	$lCon->Execute('ROLLBACK TRANSACTION;');
// 	return;
	$lSql = 'UPDATE pwt.documents SET
			doc_xml = \'' . q($lDocumentXml) . '\'::xml,
			generated_doc_html = 0,
			has_unprocessed_changes = false
		WHERE id = ' . (int)$pDocumentId . ';';
	if(!$lCon->Execute($lSql)){
		$lCon->Execute('ROLLBACK TRANSACTION;');
		throw new Exception(getstr($lCon->GetLastError()));
	}

	if(!$lCon->Execute('COMMIT TRANSACTION;')){
		$lCon->Execute('ROLLBACK TRANSACTION;');
		throw new Exception(getstr('pwt.couldNotCommitTransaction'));
	}
}

function GetFieldComments($pInstanceId, $pFieldId, $pCon = false){
	$lCon = $pCon;
	if(!$lCon){
		$lCon = new DBCn();
		$lCon->Open();
	}
	$lSql = '
		SELECT ms.id as start_comment_id, ms.start_object_instances_id as start_instance_id, ms.start_object_field_id as start_field_id, ms.start_offset,
		ms.id as end_comment_id, ms.end_object_instances_id as end_instance_id, ms.start_object_field_id as end_field_id, ms.end_offset
	FROM pwt.document_object_instances i
	JOIN pwt.msg ms ON (ms.start_object_instances_id = i.id AND coalesce(ms.start_object_field_id, 0) = ' . (int)$pFieldId . ' AND ms.start_offset >= 0	)
		OR (ms.end_object_instances_id = i.id AND coalesce(ms.end_object_field_id, 0) = ' . (int)$pFieldId . ' AND ms.end_offset >= 0	)
	WHERE i.id = ' . (int)$pInstanceId . '
	ORDER BY i.id
	';
// 	var_dump($lSql);
	$lResult = array();
	$lCon->Execute($lSql);
	// 		trigger_error('SQL FIELDS BEFComm  ' . var_export($this->m_fields, 1));
	while(!$lCon->Eof()){
		$lRes = $lCon->mRs;
		$lCommentPrefixTypes = array(
			COMMENTS_FIX_TYPE_START_POS => 'start_',
			COMMENTS_FIX_TYPE_END_POS => 'end_',
		);

		foreach ($lCommentPrefixTypes as $lFixType => $lPrefix) {
			$lInstanceId = (int)$lRes[$lPrefix . 'instance_id'];
			$lFieldId = (int)$lRes[$lPrefix . 'field_id'];
			$lCommentId = (int)$lRes[$lPrefix . 'comment_id'];
			// 				var_dump($lInstanceId, $lFieldId, $lCommentId);
			if($lInstanceId == $pInstanceId && $lFieldId == $pFieldId){
				if(!array_key_exists($lCommentId, $lResult)){
					$lResult[$lCommentId] = array(
						'previous_' . $lPrefix . 'offset' => $lRes[$lPrefix . 'offset'],
						'position_fix_type' => $lFixType,
					);
				}else{
					$lResult[$lCommentId]['previous_' . $lPrefix . 'offset'] = $lRes[$lPrefix . 'offset'];
					$lResult[$lCommentId]['position_fix_type'] = $lResult[$lCommentId]['position_fix_type'] | $lFixType;
				}
			}
		}

		$lCon->MoveNext();
	}
// 	var_dump($lResult);
	return $lResult;
}

function displayIframePreviewHasUnprocessedChangesClass($pDocumentHasUnprocessedChanges){
	if((int)$pDocumentHasUnprocessedChanges){
		return ' previewIframeWithUnprocessedChanges ';
	}
}

function checkIfDocumentHasUnprocessedChangesSimple($pDocumentId){
	$lCon = new DBCn();
	$lCon->Open();
	$lSql = '
		SELECT has_unprocessed_changes::int as has_unprocessed_changes, doc_xml
		FROM pwt.documents
		WHERE id = ' . (int)$pDocumentId;
	$lCon->Execute($lSql);
	$lDocumentHasUnprocessedChanges = (int)$lCon->mRs['has_unprocessed_changes'];
	$lDocumentXml = $lCon->mRs['doc_xml'];
	return checkIfDocumentHasUnprocessedChanges($pDocumentId, $lDocumentHasUnprocessedChanges, $lDocumentXml);
}

/**
 * Check if the specified document has unprocessed changes.
 * @param unknown_type $pDocumentId
 * @param unknown_type $pDocumentHasUnprocessedChanges - if true the document may have unprocessed changes.
 * @param unknown_type $pDocumentXml - check the document xml for change insert/delete tags
 */
function checkIfDocumentHasUnprocessedChanges($pDocumentId, $pDocumentHasUnprocessedChanges, $pDocumentXml){
	if(!$pDocumentHasUnprocessedChanges){
		return false;
	}
	$lDom = new DOMDocument('1.0', DEFAULT_XML_ENCODING);
	if(!$lDom->loadXML($pDocumentXml)){
		//some error
		return false;
	}
	$lXPath = new DOMXPath($lDom);
	$lChangeNodesQuery = '//' . VERSION_DELETE_CHANGE_NODE_NAME . '|//' . VERSION_INSERT_CHANGE_NODE_NAME . '';
	$lChangeNodes = $lXPath->query($lChangeNodesQuery);
	if($lChangeNodes->length){
		return true;
	}
	//Mark the document as document which has no unprocessed changes
	$lCon = new DBCn();
	$lCon->Open();
	$lSql = 'UPDATE pwt.documents SET
			has_unprocessed_changes = false
		WHERE id = ' . $pDocumentId . '
	';
	$lCon->Execute($lSql);
	return false;
}

function objHasIcon($obj_id)
{
	switch ($obj_id) {
		case 21:
			return 'nav-references';
		case 56:
			return 'nav-supplementary';
		case (int)FIGURE_HOLDER_OBJECT_ID:
			return 'P-Article-Figures';
		case (int)TABLE_HOLDER_OBJECT_ID:
			return 'P-Article-Tables';
		default:
			return '';
	}
}

function GetPlateImageLetterByHolder($pHolderId) {
	switch ((int)$pHolderId) {
		case 1: return 'A';
				break;
		case 2: return 'B';
				break;
		case 3: return 'C';
				break;
		case 4: return 'D';
				break;
		case 5: return 'E';
				break;
		case 6: return 'F';
				break;
		default: return 'A';
				break;

	}
}

function showDashboardAdminFilter($pShowAll){
	global $user;

	if ($user->admin == 'true' || $user->admin == 't') {
		return '
			<table class="P-Data-Resources-Head" cellspacing="0" cellpadding="0">
				<tbody>
					<tr>
					<td class="P-Data-Resources-Head-Text">' . ((int)$pShowAll ? '<a href="/dashboard.php">' : '') . getstr('dashboard.my_manuscripts') . ((int)$pShowAll ? '</a>' : '') .'</td>
					<td class="P-Data-Resources-Head-Text">|</td>
					<td class="P-Data-Resources-Head-Text">' . (!(int)$pShowAll ? '<a href="/dashboard.php?showall=1">' : '') . getstr('dashboard.all_manuscripts') . (!(int)$pShowAll ? '</a>' : '') . '</td>
					</tr>
				</tbody>
			</table>
		';
	} else {
		return '
			<table class="P-Data-Resources-Head" cellspacing="0" cellpadding="0">
				<tbody>
					<tr>
					<td class="P-Data-Resources-Head-Text">' . getstr('dashboard.my_manuscripts') . '</td>
					<td class="P-Inline-Line"></td>
					</tr>
				</tbody>
			</table>
		';
	}
}


/**
 * @formatter:off
 * Returns an array of all the comments for the specific document
 * the format of the array is the following:
 *		array(
 * 			instance_id => array(
 * 				non_field_comments => array(//Comments which are in the beginning/end of the instance - not in a specific field
 *	 				comment_id => array(
 * 						start_offset => val,
 *	 					end_offset => val,
 * 						comment_pos_type => val
 * 					),
 * 				),
 * 				field_comments => array(
 * 					field_id => array(
 * 						comment_id => array(
 * 							start_offset => val,
 *	 						end_offset => val,
 * 							comment_pos_type => val
 * 						),
 * 					),
 * 				),
 * 			),
 * 		)
 * @param int $pDocumentId
 * @formatter:on
 */
function GetDocumentComments($pDocumentId){
	$lCon = new DBCn();
	$lCon->Open();
	$lSql = '
		SELECT m.*
		FROM pwt.msg m
		WHERE m.document_id = ' . (int)$pDocumentId . ' AND m.start_object_instances_id > 0 AND m.end_object_instances_id > 0
			AND m.revision_id = spGetDocumentLatestCommentRevisionId(' . (int)$pDocumentId . ', 0)
	';
	$lResult = array();
	$lCon->Execute($lSql);
	$lPositions = array(
		COMMENT_START_POS_TYPE => 'start_',
		COMMENT_END_POS_TYPE => 'end_',
	);
	while(!$lCon->Eof()){
// 		var_dump($lCon->mRs);
		foreach ($lPositions as $lType => $lPrefix) {
			if(!is_array($lResult[$lCon->mRs[$lPrefix . 'object_instances_id']])){
				$lResult[$lCon->mRs[$lPrefix . 'object_instances_id']] = array(
					'non_field_comments' => array(),
					'field_comments' => array(),
				);
			}
			$lCommentId = $lCon->mRs['id'];
			$lInstanceComments = &$lResult[$lCon->mRs[$lPrefix . 'object_instances_id']];
			$lCommentsSubArray = &$lInstanceComments['non_field_comments'];
			if((int)$lCon->mRs[$lPrefix . 'object_field_id']){
				$lCommentsSubArray = &$lInstanceComments['field_comments'][(int)$lCon->mRs[$lPrefix . 'object_field_id']];
			}
			if(!array_key_exists($lCommentId, $lCommentsSubArray)){
				$lCommentsSubArray[$lCommentId] = array(
					$lPrefix . 'offset' => $lCon->mRs[$lPrefix . 'offset'],
					'comment_pos_type' => $lType,
				);
			}else{
				$lCommentsSubArray[$lCommentId][$lPrefix . 'offset'] = $lCon->mRs[$lPrefix . 'offset'];
				$lCommentsSubArray[$lCommentId]['comment_pos_type'] = $lCommentsSubArray[$lCommentId]['comment_pos_type'] | $lType;
			}
		}
		$lCon->MoveNext();
	}
	return $lResult;
}


function displayDocumentTreeAdd_Tables_Figures($pDocument, $pType, $pIsLocked = 0, $pLockUid = 0, $pValidatePage = 0, $pDocumentState = 1) {
	global $user;

	if(((int)$pIsLocked && (int)$user->id != (int)$pLockUid) || (int)$pValidatePage || $pDocumentState == 2){
		return $lResult;
	}

	$pType = str_replace('_', '', $pType);

	if ((int)$pType == 1) {//figures
		return '<a class="P-Article-Add" onclick="ChangeFiguresForm( \'image\', ' . $pDocument . ', \'P-PopUp-Content-Inner\', 0, 2);popUp(POPUP_OPERS.open, \'add-figure-popup\', \'add-figure-popup\');" title="Add figure" href="javascript:void(0)"></a>';
	}
	else {
		return '<a class="P-Article-Add" onclick="ShowAddTablePopup(' . $pDocument . ', \'add-table-popup\')" title="Add table" href="javascript:void(0)"></a>';

	}
}

function DisplayValidationErrs($pErrCount, $pErrors) {
	if((int)$pErrCount) {
		return '
			<div class="P-Document-Validation-Submit-Txt">' . getstr('pwt.xmlvalidation.submitdocerrmsg') . '</div>
			<div class="P-Document-Validation-ClickOnErr-Txt">' . getstr('pwt.xmlvalidation.clickonerror') . '</div>
			' . getstr('pwt.xmlvalidation.errors') . ' (' . $pErrCount . ') <br />
			' . $pErrors . '
		';
	} else {
		return '
			<div class="P-Document-Validation-Submit-Txt"><span class="P-Document-Validation-Path-Valid">' . getstr('pwt.xmlvalidation.submitdocvalid') . '</span></div>
		';
	}
}


function displayResolvedInfo($pCommentId, $pIsResolved, $pResolveUid, $pResolveUserFullname, $pResolveDate, $pVersionIsReadonly = false){
	$lResult = '<div class="Comment-Resolve-Info" id="P-Comment-Resolve-' . $pCommentId . '">';

	if(!$pVersionIsReadonly){
		$lResult .= '<input type="checkbox" onclick="ResolveComment(' . $pCommentId . ')" name="is_resolved_' . $pCommentId . '" id="is_resolved_' . $pCommentId . '" value="1" ' . ($pIsResolved ? 'checked="checked"' : '') . '>';
		$lResult .= '<label id="label_is_resolved_' . $pCommentId . '" class="' . ($pIsResolved ? ('Resolved-Comment-Label') : '') . '">' . ($pIsResolved ? ('Resolved by: <br/>' . $pResolveUserFullname) : 'Resolve') . '</label>';
	}else{
		if($pIsResolved){
			$lResult .= '<div class="P-Comment-Resolved-Read-Only-Info">Resolved by: ' . $pResolveUserFullname . '</div>';
		}
	}

	$lResult .= '</div>';
	return $lResult;
}

function displayCommentLastModdate($pCommentId, $pDate, $pDateInSeconds, $pIsRoot = false){
	$lResult = '';
	$pDate = showCommentDate($pDate);
	$lSpanId = 'comment_date_';
	if($pIsRoot){
		$lSpanId .= 'root_';
	}
	$lSpanId .= $pCommentId;
	$lCurrentSeconds = time();
	$lDiff = $lCurrentSeconds - $pDateInSeconds;
	$lResult = '<span id="' . $lSpanId . '" title="' . $pDate . '">
					<script>SetCommentDateLabel(' . json_encode($lSpanId) . ', ' . (int)$pDateInSeconds . ', ' . json_encode($pDate) . ')</script>
				</span>';
	return $lResult;
}

function showRightPicMargin($pRowNum) {
	if($pRowNum%2 != 0) {
		return 'P-Plate-Part-Holder-Margin-Roght';
	}
}
/**
	* get document state
*/
function getDocumentState($pDocumentID, $pInstanceID = 0) {
	if (!(int)$pDocumentID && $pInstanceID) {
		$pDocumentID = getInstanceDocumentId($pInstanceID);
	}

	if($pDocumentID) {
		$lCon = new DBCn();
		$lCon->Open();
		$lSql = 'SELECT state FROM pwt.documents where id = ' . (int)$pDocumentID;
		$lCon->Execute($lSql);
		$lState = (int)$lCon->mRs['state'];
		return $lState;
	}

	return 0;

}

function CheckIfLoginFormCaptchaShouldBeDisplayed(){
	return (int)$_SESSION['wrong_login_attempts'] >= MAX_ALLOWED_WRONG_LOGIN_ATTEMPTS;
}

function GetLoginFormCaptchaTemplate(){
	if(!CheckIfLoginFormCaptchaShouldBeDisplayed()){
		return '';
	}
	return '		<div class="capholder">
							<div class="loginFormRowHolder">
								<div class="capcode">
									<img src="/lib/frmcaptcha.php" id="cappic" border="0" alt="" /><br/>
									<a href="javascript: void(0);" onclick="return reloadCaptcha();">' . getstr('register.php.generatenew') . '</a>
								</div><br/>
								<div class="loginFormLabel"><label for="captcha">' . getstr('register.php.spamconfirm') . '</label></b><span class="asterisk">*</span></div>
								<div class="P-Input-Full-Width">
									<div class="P-Input-Inner-Wrapper">
										<div class="P-Input-Holder">
											<div class="P-Input-Left"></div>
											<div class="P-Input-Middle">
												<input type="text" name="captcha" id="captcha" onfocus="changeFocus(1, this);" onblur="changeFocus(2, this);" id="P-Login-Password" tabindex="2">
											</div>
											<div class="P-Input-Right"></div>
											<div class="P-Clear"></div>
										</div>
									</div>
								</div>
								<div class="P-Clear"></div>
							</div>
						</div>';
}

function DisplayCommentUserName($pIsDisclosed, $pUserRealId, $pCurrentUserId, $pCommentUserRealFullName, $pCommentUserUndisclosedName){
// 	var_dump($pIsDisclosed);
	if(CheckIfUserIsDisclosed($pIsDisclosed, $pUserRealId, $pCurrentUserId)){
		return $pCommentUserRealFullName;
	}
	return $pCommentUserUndisclosedName;
}

function CheckIfUserIsDisclosed($pIsDisclosed, $pUserRealId, $pCurrentUserId){
	if($pIsDisclosed || $pCurrentUserId == $pUserRealId){
		return true;
	}
	return false;
}

function GetPlateDesc($pPlateType){
	$lResult = new csimple(array (
		'templs' => array (
			G_DEFAULT => 'figures.plate_appearance_' . $pPlateType
		)
	));
	return $lResult->Display();
}

function EnableJSTracksFigures($pTrackFigures){
	if(!(int)$pTrackFigures){
		return;
	}
	return ' EnableFigureTracking(); ';
}

function displayPlateManagementBtns($pPlateInstanceId){
	$lCon = new DBCn();
	$lCon->Open();
	$lSql = '
			SELECT i.id
			FROM pwt.document_object_instances i
			JOIN pwt.document_object_instances p ON p.id = i.parent_id AND p.parent_id = ' . (int)$pPlateInstanceId . ' AND p.object_id = '
					. (int)PLATE_WRAPPER_OBJECT_ID . '
	';
// 	var_dump($lSql);
	$lCon->Execute($lSql);
	$lPlateExists = false;
	if($lCon->mRs['id']){
		$lPlateExists = true;
	}
	$lCreateBtnStyle = '';
	$lDeleteBtnStyle = 'display:none';
	if($lPlateExists){
		$lCreateBtnStyle = 'display:none';
		$lDeleteBtnStyle = '';
	}
	$lResult = '
		<div onclick="CreatePlateDetails(' . $pPlateInstanceId . ')" class="P-Grey-Btn-Holder" style="' . $lCreateBtnStyle . '" id="P-Create-Plate-Details-Btn-' . $pPlateInstanceId . '">
			<div class="P-Grey-Btn-Left"></div>
			<div class="P-Grey-Btn-Middle"><div class="P-Btn-Icon"></div>Create plate details</div>
			<div class="P-Grey-Btn-Right"></div>
		</div>
		<div onclick="DeletePlateDetails(' . $pPlateInstanceId . ')" class="P-Grey-Btn-Holder" style="' . $lDeleteBtnStyle . '" id="P-Delete-Plate-Details-Btn-' . $pPlateInstanceId . '">
			<div class="P-Grey-Btn-Left"></div>
			<div class="P-Grey-Btn-Middle"><div class="P-Btn-Icon"></div>Delete plate details</div>
			<div class="P-Grey-Btn-Right"></div>
		</div>
	';
	return $lResult;
}

function SaveFigCaption($pDocumentId, $pFigId, $pIsPlate, $pPlateNum, $pContent){
	if(!$pDocumentId){
		throw new Exception(getstr('pwt.noDocumentSpecified'));
	}
	if(!$pFigId){
		throw new Exception(getstr('pwt.noFigureSpecified'));
	}
	$lCon = new DBCn();
	$lCon->Open();
	$lSql = '
		UPDATE pwt.media SET
			description = \'' . q($pContent) . '\',
			title = \'' . q($pContent) . '\'
		WHERE id = ' . (int)$pFigId . '	AND document_id = ' . (int)$pDocumentId . '
	';
	if($pIsPlate){
		if((int)$pPlateNum){
			$lSql = '
				UPDATE pwt.media SET
					description = \'' . q($pContent) . '\'
				WHERE plate_id = ' . (int)$pFigId . '	AND document_id = ' . (int)$pDocumentId . ' AND position = ' . (int)$pPlateNum . '
			';
		}else{
			$lSql = '
				UPDATE pwt.plates SET
					description = \'' . q($pContent) . '\'
				WHERE id = ' . (int)$pFigId . '	AND document_id = ' . (int)$pDocumentId . '
			';
		}
	}
	if(!$lCon->Execute($lSql)){
		throw new Exception($lCon->GetLastError());
	}
	//Mark the xml as modified so that the figure changes can be applied to the document xml
// 	$lCon->Execute('SELECT * FROM pwt."XmlIsDirty"(1, ' . (int)$pDocumentId . ', null)');

}

function SaveTableChange($pDocumentId, $pTableId, $pModifiedElementIsTitle, $pContent){
	if(!$pDocumentId){
		throw new Exception(getstr('pwt.noDocumentSpecified'));
	}
	if(!$pTableId){
		throw new Exception(getstr('pwt.noTableSpecified'));
	}
	$lCon = new DBCn();
	$lCon->Open();
	$lFieldColumnName = 'title';
	if(!$pModifiedElementIsTitle){
		$lFieldColumnName = 'description';
	}
	$lSql = '
		UPDATE pwt.tables SET
			' . $lFieldColumnName . ' = \'' . q($pContent) . '\'
		WHERE id = ' . (int)$pTableId . '	AND document_id = ' . (int)$pDocumentId . '
	';
// 	var_dump($lSql);
	if(!$lCon->Execute($lSql)){
		throw new Exception($lCon->GetLastError());
	}
	//Mark the xml as modified so that the table changes can be applied to the document xml
// 	$lCon->Execute('SELECT * FROM pwt."XmlIsDirty"(1, ' . (int)$pDocumentId . ', null)');

}

function InitActiveTabs(){
// 	var_dump($_SESSION['activemenutabids']);
// 	$_SESSION['activemenutabids'] = array();
// 	trigger_error('SESSION ' . var_export($_SESSION['activemenutabids'], 1), E_USER_NOTICE);
	if(!isset($_SESSION['activemenutabids'])){
		$_SESSION['activemenutabids'] = array();
	}
}

function MarkActiveTab($pTabId){
	InitActiveTabs();
// 	var_dump(2);
	$lInstanceId = $pTabId;
	$lSql = '
		SELECT p.id
		FROM pwt.document_object_instances i
		JOIN pwt.document_object_instances p ON p.document_id = i.document_id AND i.pos ILIKE p.pos || \'%\' AND p.id <> i.id
		WHERE i.id = ' . (int)$lInstanceId;
	$lCon = new DBCn();
	$lCon->Open();
	$lCon->Execute($lSql);
// 	var_dump($lSql);
	while(!$lCon->Eof()){
		$lParentId = (int)$lCon->mRs['id'];
		$_SESSION['activemenutabids'][$lParentId] = $lParentId;
		$lCon->MoveNext();
	}


	$_SESSION['activemenutabids'][$pTabId] = $pTabId;
}

function MarkInactiveTab($pTabId){
	InitActiveTabs();
	if(array_key_exists($pTabId, $_SESSION['activemenutabids'])){
		unset($_SESSION['activemenutabids'][$pTabId]);
	}
}

function checkIfPasswordIsSecure($pPassword){
	return mb_strlen($pPassword) >= (int)MIN_ALLOWED_PASSWORD_LENGTH;
}

function displayPlateImageTempl($pDocumentId, $pInstanceId, $pFieldId, $pPicId, $pLabel){
	$lTemplate = 'fields.file_upload_figure_plate_image_without_pic';
	if((int)$pPicId){
		$lTemplate = 'fields.file_upload_figure_plate_image_with_pic';
	}
	$lResult = new csimple(array (
		'templs' => array (
			G_DEFAULT => $lTemplate
		),
		'instance_id' => $pInstanceId,
		'field_id' => $pFieldId,
		'photo_id' => $pPicId,
		'label' => $pLabel,
		'document_id' => $pDocumentId,
		'pref' => 'c288x206y',
	));
	return $lResult->Display();
}

function getDocumentCreatorData($pDocumentId) {
	$lRes = array();

	$lCon = new DBCn();
	$lCon->Open();
	$lSql = '
		SELECT u.*, d.name as document_name, d.has_unprocessed_changes::int as has_unprocessed_changes
		FROM pwt.documents d
		JOIN usr u ON u.id = d.createuid
		WHERE d.id = ' . (int)$pDocumentId;
	$lCon->Execute($lSql);
	$lCon->MoveFirst();
	$lRes = $lCon->mRs;
	$lCon->Close();

	return $lRes;
}

function showPJSSubmitButton($pDocumentId, $pDocumentState){
	global $user;
	$lRes = '';
	$lCreatorData = getDocumentCreatorData($pDocumentId);
	$lCheckIfPreviewCanBeEdited = checkIfPreviewCanBeEdited($pDocumentId);
	if($lCheckIfPreviewCanBeEdited) {
		if($pDocumentState == NEW_DOCUMENT_STATE){
			if($user->staff == 1){
				$lRes = '
					<div class="P-Green-Btn-Holder' . ((int)ENABLE_FEATURES ? '': ' P-Inactive-Button') . '"' . ((int)ENABLE_FEATURES ? 'onclick="ShowconfirmAndExec(\'' . getstr('pwt.approve_to_submit_confirm_text') . '\', function(){showLoading(); SubmitDocumentAction(\'/xml_validate.php?document_id=' . (int)$pDocumentId . '&action_type=' . APPROVE_TO_SUBMIT_DOCUMENT_ACTION_TYPE . '\');})"' : '') . '>
					<div class="P-Green-Btn-Left"></div>
						<div class="P-Green-Btn-Middle P-Green-Btn-Middle-Big_One">' . getstr('pwt.approve_documentfor_submission_btn') . '</div>
						<div class="P-Green-Btn-Right"></div>
					</div>
					<div class="P-Clear"></div>
				';
			} else {
				$lRes = '
					<div class="P-Green-Btn-Holder' . ((int)ENABLE_FEATURES ? '': ' P-Inactive-Button') . '"' . ((int)ENABLE_FEATURES ? 'onclick="ShowconfirmAndExec(\'' . getstr('pwt.ready_to_submit_confirm_text') . '\', function(){showLoading(); SubmitDocumentAction(\'/xml_validate.php?document_id=' . (int)$pDocumentId . '&action_type=' . AUTHOR_READY_TO_SUBMIT_DOCUMENT_ACTION_TYPE . '\');})"' : '') . '>
					<div class="P-Green-Btn-Left"></div>
						<div class="P-Green-Btn-Middle P-Green-Btn-Middle-Big_One">' . getstr('pwt.ready_to_submit_documentfor_submission_btn') . '</div>
						<div class="P-Green-Btn-Right"></div>
					</div>
					<div class="P-Clear"></div>
				';
			}
		} elseif ($pDocumentState == IN_PRE_SUBMIT_REVIEW_DOCUMENT_STATE) {
			if($user->staff == 1){
				$lRes = '
					<div style="margin-bottom:20px;" class="P-Green-Btn-Holder' . ((int)ENABLE_FEATURES ? '': ' P-Inactive-Button') . '"' . ((int)ENABLE_FEATURES ? 'onclick="ShowconfirmAndExec(\'' . getstr('pwt.approve_to_submit_confirm_text') . '\', function(){showLoading(); SubmitDocumentAction(\'/xml_validate.php?document_id=' . (int)$pDocumentId . '&action_type=' . APPROVE_TO_SUBMIT_DOCUMENT_ACTION_TYPE . '\');})"' : '') . '>
					<div class="P-Green-Btn-Left"></div>
						<div class="P-Green-Btn-Middle P-Green-Btn-Middle-Big_One">' . getstr('pwt.approve_documentfor_submission_btn') . '</div>
						<div class="P-Green-Btn-Right"></div>
					</div>
					<div class="P-Clear"></div>
					<div class="P-Green-Btn-Holder' . ((int)ENABLE_FEATURES ? '': ' P-Inactive-Button') . '"' . ((int)ENABLE_FEATURES ? 'onclick="ShowconfirmAndExec(\'' . getstr('pwt.reject_to_submit_confirm_text') . '\', function(){showLoading(); SubmitDocumentAction(\'/xml_validate.php?document_id=' . (int)$pDocumentId . '&action_type=' . REJECT_TO_APPROVE_DOCUMENT_ACTION_TYPE . '\');})"' : '') . '>
					<div class="P-Green-Btn-Left"></div>
						<div class="P-Green-Btn-Middle P-Green-Btn-Middle-Big_One">' . getstr('pwt.reject_documentfor_submission_btn') . '</div>
						<div class="P-Green-Btn-Right"></div>
					</div>
					<div class="P-Clear"></div>
				';
			} else {
				if((int)$lCreatorData['id'] == (int)$user->id){
					$lRes = '
						<div class="P-Green-Btn-Holder P-Inactive-Button">
						<div class="P-Green-Btn-Left"></div>
							<div class="P-Green-Btn-Middle P-Green-Btn-Middle-Big_One">' . getstr('pwt.submit_document_btn') . '</div>
							<div class="P-Green-Btn-Right"></div>
						</div>
						<div class="P-Clear"></div>
					';
				}

			}
		} elseif ($pDocumentState == READY_TO_SUBMIT_DOCUMENT_STATE || $pDocumentState == RETURNED_FROM_PJS_DOCUMENT_STATE) {
			if((int)$lCreatorData['id'] == (int)$user->id){
				$lRes = '
					<div class="P-Green-Btn-Holder' . ((int)ENABLE_FEATURES ? '': ' P-Inactive-Button') . '"' . ((int)ENABLE_FEATURES ? 'onclick="ShowconfirmAndExec(\'' . getstr('pwt.can_submit_confirm_text') . '\', function(){showLoading(); SubmitDocumentAction(\'/xml_validate.php?document_id=' . (int)$pDocumentId . '&action_type=' . SUBMIT_DOCUMENT_ACTION_TYPE . '\');})"' : '') . '>
					<div class="P-Green-Btn-Left"></div>
						<div class="P-Green-Btn-Middle P-Green-Btn-Middle-Big_One">' . getstr('pwt.submit_document_btn') . '</div>
						<div class="P-Green-Btn-Right"></div>
					</div>
					<div class="P-Clear"></div>
				';
			}
		}
	}

	return $lRes;

}

function ExecActionType($pDocumentId, $pActionType) {
	global $user;
	$lCreatorData = getDocumentCreatorData($pDocumentId);
	$lCheckIfPreviewCanBeEdited = checkIfPreviewCanBeEdited($pDocumentId);
	$lDocumentState = getDocumentState($pDocumentId);

	$lActionResult = false;

	$lCon = new DBCn();
	$lCon->Open();

	switch ($pActionType) {
		case AUTHOR_READY_TO_SUBMIT_DOCUMENT_ACTION_TYPE:

			if($lCheckIfPreviewCanBeEdited && $lDocumentState == NEW_DOCUMENT_STATE) {
				$lSql = 'UPDATE pwt.documents SET state = ' . IN_PRE_SUBMIT_REVIEW_DOCUMENT_STATE . ' WHERE id = ' . (int)$pDocumentId;
				$lCon->Execute($lSql);
				$lActionResult = true;

				// send a message to
				$lMessageData = array(
					'siteurl' => SITE_URL,
					'mailsubject' => getstr('pwt.document_mail_subject_prefix') . $pDocumentId . getstr('pwt.document_ready_for_review_mail_subject'),
					'mailto' => PENSOFT_MAIL_ADDR_DOCUMENT_SUBMISSION,
					'charset' => 'UTF-8',
					'boundary' => '--_separator==_',
					'document_id' => $pDocumentId,
					'first_name' => $lCreatorData['first_name'],
					'last_name' => $lCreatorData['last_name'],
					'document_name' => strip_tags($lCreatorData['document_name'], '<i><em><b><strong><sub><sup><u>'),
					'autolog_hash' => $lCreatorData['autolog_hash'],
					'from' => array(
						'display' => PENSOFT_MAIL_DISPLAY,
						'email' => PENSOFT_MAIL_ADDR,
					),
					'templs' => array(
						G_DEFAULT => 'document.author_submission_mail',
					),
				);
				$msg = new cmessaging($lMessageData);
				$msg->Display();

				// send a message to
				$lMessageData = array(
					'siteurl' => SITE_URL,
					'mailsubject' => getstr('pwt.document_mail_subject_prefix') . $pDocumentId . getstr('pwt.thanks_document_ready_for_review_mail_subject'),
					'mailto' => $lCreatorData['uname'],
					'charset' => 'UTF-8',
					'boundary' => '--_separator==_',
					'document_id' => $pDocumentId,
					'first_name' => $lCreatorData['first_name'],
					'last_name' => $lCreatorData['last_name'],
					'document_name' => strip_tags($lCreatorData['document_name'], '<i><em><b><strong><sub><sup><u>'),
					'autolog_hash' => $lCreatorData['autolog_hash'],
					'from' => array(
						'display' => PENSOFT_MAIL_DISPLAY,
						'email' => PENSOFT_MAIL_ADDR,
					),
					'templs' => array(
						G_DEFAULT => 'document.author_thanks',
					),
				);
				$msg = new cmessaging($lMessageData);
				$msg->Display();
				
				header('Location: /preview.php?document_id=' . $pDocumentId);
				exit;

			}
			break;
		case APPROVE_TO_SUBMIT_DOCUMENT_ACTION_TYPE:
			if($user->staff == 1 && in_array($lDocumentState, array(IN_PRE_SUBMIT_REVIEW_DOCUMENT_STATE, NEW_DOCUMENT_STATE))) {
				$lSql = 'UPDATE pwt.documents SET state = ' . READY_TO_SUBMIT_DOCUMENT_STATE . ' WHERE id = ' . (int)$pDocumentId;
				$lCon->Execute($lSql);
				$lActionResult = true;

				// send a message to
				$lMessageData = array(
					'siteurl' => SITE_URL,
					'mailsubject' => getstr('pwt.document_mail_subject_prefix') . $pDocumentId . getstr('pwt.document_ready_for_submit_mail_subject'),
					'mailto' => $lCreatorData['uname'],
					//'mailto' => 'vic.penchev@gmail.com',
					'charset' => 'UTF-8',
					'boundary' => '--_separator==_',
					'document_id' => $pDocumentId,
					'first_name' => $lCreatorData['first_name'],
					'last_name' => $lCreatorData['last_name'],
					'document_name' => strip_tags($lCreatorData['document_name'], '<i><em><b><strong><sub><sup><u>'),
					'autolog_hash' => $lCreatorData['autolog_hash'],
					'from' => array(
						'display' => PENSOFT_MAIL_DISPLAY,
						'email' => PENSOFT_MAIL_ADDR,
					),
					'templs' => array(
						G_DEFAULT => 'document.staff_approve_mail',
					),
				);
				$msg = new cmessaging($lMessageData);
				$msg->Display();

				header('Location: /preview.php?document_id=' . $pDocumentId);
				exit;
			}
			break;
		case REJECT_TO_APPROVE_DOCUMENT_ACTION_TYPE:
			if($user->staff == 1 && in_array($lDocumentState, array(IN_PRE_SUBMIT_REVIEW_DOCUMENT_STATE))) {
				$lSql = 'UPDATE pwt.documents SET state = ' . NEW_DOCUMENT_STATE . ' WHERE id = ' . (int)$pDocumentId;
				$lCon->Execute($lSql);
				$lActionResult = true;

				// send a message to
				$lMessageData = array(
					'siteurl' => SITE_URL,
					'mailsubject' => getstr('pwt.document_mail_subject_prefix') . $pDocumentId . getstr('pwt.document_reject_for_submit_mail_subject'),
					'mailto' => $lCreatorData['uname'],
					//'mailto' => 'vic.penchev@gmail.com',
					'charset' => 'UTF-8',
					'boundary' => '--_separator==_',
					'document_id' => $pDocumentId,
					'first_name' => $lCreatorData['first_name'],
					'last_name' => $lCreatorData['last_name'],
					'document_name' => strip_tags($lCreatorData['document_name'], '<i><em><b><strong><sub><sup><u>'),
					'autolog_hash' => $lCreatorData['autolog_hash'],
					'from' => array(
						'display' => PENSOFT_MAIL_DISPLAY,
						'email' => PENSOFT_MAIL_ADDR,
					),
					'templs' => array(
						G_DEFAULT => 'document.staff_reject_mail',
					),
				);
				$msg = new cmessaging($lMessageData);
				$msg->Display();

				header('Location: /preview.php?document_id=' . $pDocumentId);
				exit;
			}
			break;
		case SUBMIT_DOCUMENT_ACTION_TYPE:
			if(
				$user->id == $lCreatorData['id']
				&& !$lCreatorData['has_unprocessed_changes']
				&& in_array($lDocumentState, array(READY_TO_SUBMIT_DOCUMENT_STATE, RETURNED_FROM_PJS_DOCUMENT_STATE))
			) {
				header('Location: /pjs_submit_document.php?document_id=' . $pDocumentId);
				exit;
			}
			break;
		default:
			break;
	}
	$lCon->Close();

	return $lActionResult;
}

function  displayNewCommentBtn($pVersionIsReadonly){
	if((int)$pVersionIsReadonly){
		return;
	}

	return '<div style="margin-right:8px" class="comment_btn floatLeft P-Comment-Inline-Main-Btn" id="P-Comment-Btn-Inline" onmousedown="submitPreviewNewComment();return false"></div>
			<div class="comment_btn floatLeft " id="P-Comment-Btn-General" title="Comment issues related to the whole manuscript." onmousedown="submitPreviewNewComment(1);return false"></div>';
}

function  displayCommentsHelp($pVersionIsReadonly){
	if((int)$pVersionIsReadonly){
		return;
	}

	return '<div class="P-Input-Help" style="float: left; left: 100px;">
				<div class="P-Baloon-Holder" style="top: 22px; left: -87px; position: absolute; z-index: 999;">
					<div class="P-Baloon-Arrow" style="top: -4px; background-image: url(\'/i/boloon_arrow_top.png\'); height: 13px; left: 42px; position: absolute; width: 22px;"></div>
					<div class="P-Baloon-Top"></div>
					<div class="P-Baloon-Middle" style="width:280px;">
						<div class="P-Baloon-Content" style="font-weight:normal; color:#333;">
							There are two kinds of comments you can make on a manuscript.<br><br><b>Inline comments</b> are linked to a text selected in an editable field  (orange/gray outline on click/hover), but not to selected template texts, such as titles of the manuscript sections.<br><br><b>General comments</b> should be associated with the whole manuscript and not with selected parts parts of it.
						</div>
						<div class="P-Clear"></div>
					</div>
					<div class="P-Baloon-Bottom"></div>
				</div>
			</div>';
}

function displayPrevCommentVersionReadonlyClass($pVersionIsReadonly = false){
	if((int)$pVersionIsReadonly){
		return ' Comment-Prev-Readonly ';
	}
}

function displayCommentUnavailableText($pVersionIsReadonly = false, $pCommentForm){
	if((int)$pVersionIsReadonly){
		return;
	}
	return '<div id="P-Comment-Unavailable-Text" style="display:none">
				' . getstr('comments.currentSelectionCommentIsUnavailable') . '
			</div>';
}

function displayNewCommentForm($pVersionIsReadonly, $pForm){
	if((int)$pVersionIsReadonly){
		return;
	}
	return '<div id="P-Comment-Unavailable-Text" style="display:none">
				' . getstr('comments.currentSelectionCommentIsUnavailable') . '
			</div>
			<div id="P-Comment-Form_" style="display: none;">
				' . $pForm . '
			</div>';
}

function ShowEntrezRecordsDbSubtreeLink($pTaxonName, $pTaxonId, $pDbName, $pCount){
	if(!(int) $pCount ){
		return '-';
	}
	return '<a href="' . ParseTaxonExternalLink($pTaxonName, NCBI_SUBTREE_LINK . '&term=txid' . $pTaxonId . '[Organism:exp]&db=' . $pDbName) . '">' . (int)$pCount . '</a>';

}

function bhl_showimage($pTaxonName, $pImgUrl, $pImg, $pNodata) {
	if ($pNodata)
		return '';
	else
		return '<a href="' . ParseTaxonExternalLink($pTaxonName, $pImgUrl) . '"><img class="bhl-img" border="0" align="right" src="' . $pImg . '"></img></a>';
}

function displayBHLItems($pItems, $pTaxonName){
	$lItems = new crs_display_array(array(
		'input_arr' => $pItems,
		'taxon_name' => $pTaxonName,
		'pagesize' => 1,
		'templs' => array(
			G_HEADER => 'article.bhl_items_head',
			G_FOOTER => 'article.bhl_items_foot',
			G_STARTRS => 'article.bhl_items_start',
			G_ENDRS => 'article.bhl_items_end',
			G_ROWTEMPL => 'article.bhl_items_row',
			G_NODATA => 'article.bhl_items_nodata',
		)
	));
	return $lItems->Display();
}

function displayBHLPages($pPages, $pTaxonName){
	$lPages = new crs_display_array(array(
		'input_arr' => $pPages,
		'taxon_name' => $pTaxonName,
		'templs' => array(
			G_HEADER => 'article.bhl_pages_head',
			G_FOOTER => 'article.bhl_pages_foot',
			G_STARTRS => 'article.bhl_pages_start',
			G_ENDRS => 'article.bhl_pages_end',
			G_ROWTEMPL => 'article.bhl_pages_row',
			G_NODATA => 'article.bhl_pages_nodata',
		)
	));
	return $lPages->Display();
}

function bhl_showvolume($pVolume) {
	if ($pVolume)
		return $pVolume . ":";
	else
		return '';
}

function bhl_writecomma($pRownum, $pRecords){
	if ($pRownum < $pRecords)
		return ', ';
	else
		return '';
}

function ParsePubmedTaxonName($pTaxonName){//Parsva taxona taka 4e v pubmed da go tyrsi s AND, a ne s OR
	return str_replace(' ', ' AND ', $pTaxonName);//Zamenq intervalite s AND
}

function showImageIfSrcExists($pSrc, $pClass = 'noBorder'){
	if( trim($pSrc)){
		return '<img class="' . $pClass . '" src="' . PTP_URL . $pSrc . '"></img>';
	}
}

function GetAuthorFirstNameFirstLetter($pFirstName){
	return strtoupper(mb_substr(($pFirstName), 0, 1));
}

function displayCitationsAuthorSeparator($pRecords, $pRownum, $pSeparator = ', '){
	if((int)$pRecords > (int)$pRownum){
		return $pSeparator;
	}
}

function showTaxaNameUsage($pUsage, $pTreatmentId){
	$lResult = '<span class="taxon-usage-holder">
					<span class="taxon-usage-caption">&nbsp;&nbsp;</span>';
	$lRow = 0;
	sort($pUsage);
	foreach ($pUsage as $lUsage){
		if($lRow++){
		//	$lResult .= ' | ';
		}
		$lTitle = '';
		$lImgSrc = '';
		switch($lUsage){
			case TAXON_NAME_USAGE_TREATMENT :
				$lTitle = 'Taxon treatment';
				$lImgSrc = '/i/TTR.png';
				break;
			case TAXON_NAME_USAGE_CHECKLIST_TREATMENT :
				$lTitle = 'Checklist';
				$lImgSrc = '/i/CHL.png';
				break;
			case TAXON_NAME_USAGE_ID_KEY :
				$lTitle = 'Identification key';
				$lImgSrc = '/i/IKey.png';
				break;
			case TAXON_NAME_USAGE_FIGURE:
				$lTitle = 'Figure';
				$lImgSrc = '/i/FI.png';
				break;
			case TAXON_NAME_USAGE_INLINE:
				$lTitle = 'In text';
				$lImgSrc = '/i/InText.png';
				break;
		}
		$lResult .= '
			<span class="taxon-usage" title="' . $lTitle . '" data-usage-type=" ' . (int)$lUsage . '">
				<img width="32" heigth="20" alt="" src="' . $lImgSrc . '" style="vertical-align: middle;" />
			</span>';
	}

	$lResult .= '</span>';
	return $lResult;
}

function placeTaxonNamesAttributes($pTaxonNamesArr){
	$lResult = '';
	foreach ($pTaxonNamesArr as $lIdx => $lName) {
		$lResult .= ' data-taxon-parsed-name-' . $lIdx . '="' . htmlspecialchars($lName) . '"';
	}
	return $lResult;
}

function GetArticleTitleForCitation($pTitle){
	$pTitle = trim($pTitle);
	$lLastSymbol = mb_substr($pTitle, -1);
	if(!in_array($lLastSymbol, array('.', '?', '!'))){
		$pTitle .= '.';
	}
	return $pTitle;
}

function showTextIfErrors($pArr) {
	if(!$pArr['errs']) {
		return '<p class="message">If you are already registered with a Pensoft journal, please use your credentials to sign in.</p>';
	}
	return '';
}

function displayRegularSiteHasResultsClass($pSiteHasResults){
	if(!(int)$pSiteHasResults){
		return ' P-Regular-Site-Row-Without-Results';
	}
}

function displayRegularSiteLastRowClass($pRownum, $pRecords, $pItemsOnRow){
	if((int)$pRecords != (int)$pRownum){
		return;
	}
	if($pRownum % $pItemsOnRow != 0){//The row contains less rows than it has capacity for
		return ' P-Regular-Site-Row-Last-Larger';
	}
	return ' P-Regular-Site-Row-Last-Regular';
}

function placeTaxonNavigationLinks($pOccurrences){
	if($pOccurrences <= 1){
		return ;
	}
	$lResult = '<span class="P-Taxon-Navigation-Link-Next"><img src="/i/docrightarrow.png" alt="" title="Next"/></span>
				<span class="P-Taxon-Navigation-Link-Prev"><img src="/i/docleftarrow.png" alt="" title="Prev"/></span>
	';

	return $lResult;
}

function DisplayTreeObjectName($pDisplayName){
	$lAllowedLength = 40;
	if(mb_strlen($pDisplayName) > $lAllowedLength){
		return mb_substr($pDisplayName, 0, $lAllowedLength) . '...';
	}
	return $pDisplayName;
}

function TrimAndCutText($pText) {
	$pText = strim($pText);
	return CutText($pText, 120);
}

?>
