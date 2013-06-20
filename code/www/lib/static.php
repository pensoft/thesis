<?php
$docroot = getenv('DOCUMENT_ROOT');
require_once($docroot . '/lib/conf.php');
require_once(PATH_ECMSFRCLASSES . 'static.php');

$user = unserialize($_SESSION['suser']);

CleanCookiesFromRequest();
// crewrite instance
$rewrite =& new crewrite();

function CleanCookiesFromRequest() {
	foreach ($_COOKIE as $k => $v) {
		unset($_REQUEST[$k]);
	}
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
		return '<img src="' . SHOWIMG_URL . 
		$pref . '_' . $pid . '.jpg" border="0" alt="" ' .
		($class ? 'class="' . $class . '"' : '') . '/>';
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

?>