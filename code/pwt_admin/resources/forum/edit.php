<?
$docroot = getenv("DOCUMENT_ROOT");
require_once($docroot . '/lib/static.php');

$historypagetype = HISTORY_ACTIVE;
HtmlStart();

$t = array(
	'id' => array(
		'CType' => 'hidden',
		'VType' => 'int',
	),
	'rootid' => array(
		'CType' => 'hidden',
		'VType' => 'int',
	),
	'author' => array(
		'CType' => 'text',
		'VType' => 'string',
		'DisplayName' => 'Автор',
		'AddTags' => array(
			'class' => 'coolinp',
		),
	),
	'subject' => array(
		'CType' => 'text',
		'VType' => 'string',
		'DisplayName' => 'Заглавие',
		'AddTags' => array(
			'class' => 'coolinp',
		),
		'AllowNulls' => true,
	),
	'msg' => array(
		'CType' => 'textarea',
		'VType' => 'string',
		'DisplayName' => 'Съобщение',
		'AddTags' => array(
			'class' => 'coolinp',
		),
	),
	'dscid' => array(
		'CType' => 'hidden',
		'VType' => 'int',
	),
	'dscid2' => array(
		'CType' => 'select',
		'VType' => 'int',
		'SrcValues' => 'SELECT id,name FROM dsc WHERE dsgid=1 AND siteid=1 AND flags=1 AND id NOT IN(1)',
		'DisplayName' => 'Дискусия',
		'AllowNulls' => true,
		'AddTags' => array(
			'class' => 'coolinp',
		),
		'DefValue' => $_REQUEST['dscid'],
	),
	
	'show' => array(
		'CType' => 'action',
		'Hidden' => true,
		'ActionMask' => ACTION_CHECK | ACTION_EXEC | ACTION_FETCH | ACTION_SHOW,
		'SQL' => 'SELECT * FROM sp_msg(0,{id},null,null,null,null,null)',
	),
	'save' => array(
		'CType' => 'action',
		'ActionMask' => ACTION_CHECK | ACTION_EXEC | ACTION_FETCH | ACTION_REDIRECT,
		'RedirUrl' => '',
		'SQL' => '',
		'DisplayName' => 'Запази',
		'AddTags' => array(
			'class' => 'frmbutton',
		), 
	),
	'back' => array(
		'CType' => 'action',
		'ActionMask' => ACTION_REDIRECT,
		'RedirUrl' => '',
		'DisplayName' => 'Назад',
		'AddTags' => array(
			'class' => 'frmbutton',
		), 
	),
);

$f = new kfor($t, null, 'POST');
if($f->lCurAction == 'save'){
		$f->lFieldArr['save']['SQL'] = 'SELECT * FROM sp_msg(1,{id},{author},{subject},\'' . linebr($f->lFieldArr['msg']['CurValue']) . '\',{msg},{dscid2})';
}



$f->ExecAction();
if(($f->lFieldArr['id']['CurValue'] == $f->lFieldArr['rootid']['CurValue']) && (in_array($f->lFieldArr['dscid']['CurValue'],array(1)))){
	header("Location: index.php");
}

$f->lFormHtml = '{id}{show}{rootid}{dscid}
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
				<tr>
					<th colspan="4">Редактиране на мнение</th>
				</tr>
				<tr>
					<td colspan="2" valign="top"><b>{*author}:</b><br/>{author}</td>
					<td colspan="2" valign="top"><b>{*subject}:</b><br/>{subject}</td>
				</tr>
				<tr>
					<td colspan="2" valign="top"><b>{*msg}:</b><br/>{msg}</td>
				</tr>
				<tr>
					<td colspan="2">&nbsp;</td>
					<td colspan="2" align="right">{save} {back}</td>
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


echo $f->Display();

HtmlEnd();


function linebr($str) {
		$cutted = '';
		$str = preg_replace('/\r*\n/', " \n ", $str);
		$words = preg_split('/[\ ]+/', $str);
		foreach ($words as $word) {
			$w = ReplaceQuotes($word);
			$cutted .= $w.' ';
		}
		return nl2br($cutted);
	}
function ReplaceQuotes($msg) {
	$msg = preg_replace('/\[quote="([^"]+)"\]/', "<fieldset class=\"quotetation\"><legend>\\1</legend>", $msg);
	$msg = preg_replace('/\[\/quote\]/', '</fieldset>', $msg);
	return ReplaceEmoticons($msg);
}
function ReplaceEmoticons($msg) {
			$emotearr = array(
				0 => array(
					'img' => '/i/icons/face-angel.png', 
					'code' => '0:-)',
					'code2' => '0:)',
				),
				1 => array(
					'img' => '/i/icons/face-crying.png', 
					'code' => ':\'(',
				),
				2 => array(
					'img' => '/i/icons/face-devil-grin.png', 
					'code' => ':devil:', 
				),
				3 => array(
					'img' => '/i/icons/face-glasses.png', 
					'code' => 'B-)',
				),
				4 => array(
					'img' => '/i/icons/face-kiss.png', 
					'code' => ':-*', 
				),
				5 => array(
					'img' => '/i/icons/face-monkey.png', 
					'code' => ':-(|)', 
					'code2' => ':(|)', 
				),
				6 => array(
					'img' => '/i/icons/face-plain.png', 
					'code' => ':-|', 
				),
				7 => array(
					'img' => '/i/icons/face-sad.png', 
					'code' => ':-(', 
					'code2' => ':(', 
				),
				8 => array(
					'img' => '/i/icons/face-smile.png', 
					'code' => ':-)', 
					'code2' => ':)', 
				),
				9 => array(
					'img' => '/i/icons/face-smile-big.png', 
					'code' => ':-D', 
					'code2' => ':D', 
				),
				10 => array(
					'img' => '/i/icons/face-surprise.png', 
					'code' => ':-0', 
				),
				11 => array(
					'img' => '/i/icons/face-wink.png', 
					'code' => ';-)', 
					'code2' => ';)', 
				),
			);
		$newmsg = '';
		foreach ($emotearr as $xxx => $emtarr) {
			// Za code da replace-va taka
			$rplwhat[] = htmlspecialchars($emtarr['code']);
			$rplwith[] = '<img src="' . $emtarr['img'] . '" alt="" border="0" />';
			if ($emtarr['code2']) {
				// Za code2 da replace-va taka
				$rplwhat[] = htmlspecialchars($emtarr['code2']);
				$rplwith[] = '<img src="' . $emtarr['img'] . '" alt="" border="0" />';
			}
		}
		$newmsg = str_replace($rplwhat, $rplwith, $msg);
		return $newmsg;
}
?>