<?php
$docroot = getenv("DOCUMENT_ROOT");
require_once($docroot . '/lib/static.php');

$historypagetype = HISTORY_CLEAR;
HtmlStart();

if ($_POST['submit']) {
	control();
}
$h1 = '
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
					<th colspan="2">Форум</th>
				</tr>
				<tr>
					<td>{*fromdate}<br/>{fromdate}
						<a href="#" onclick="jscalshow(this, \'def1\', \'fromdate\'); return false;"><img src="/img/calico.gif" alt="Въведи дата" title="Въведи дата" border="0"/></a></td>
					<td>{*todate}<br/>{todate}
						<a href="#" onclick="jscalshow(this, \'def1\', \'todate\'); return false;"><img src="/img/calico.gif" alt="Въведи дата" title="Въведи дата" border="0"/></a>
					</td>
				</tr>
				<tr>
					<td>{*dsc}<br/>{dsc}</td>
					<td>{*msg_select}<br/>{msg_select}</td>
				</tr>
				<tr>
					<td>{*topicid}<br/>{topicid}</td>
					<td>{*title}<br/>{title}</td>
				</tr>
				<tr>
					<td>{*author}<br/>{author}</td>
					<td>{*ip}<br/>{ip}</td>
				</tr>
				<tr>
					<td>{*msg}<br/>{msg}</td>
					<td>{*showdeleted}<br/>{showdeleted}</td>
				</tr>
				<tr>
					<td>{*pagesize}<br/>{pagesize}</td>
				</tr>
				<tr>
					<td colspan="2" align="right">{search}</td>
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
$t1 = array(
	'p' => array(
		'CType' => 'hidden',
		'VType' => 'int',
		'AllowNulls' => true,
	),
	'fromdate' => array(
		'CType' => 'text',
		'VType' => 'date',
		'DateType' => DATE_TYPE_ALL,
		'DisplayName' => 'От дата',
		'AllowNulls' => true,
		'AddTags' => array(
			'class' => 'coolinp',
		),
	),
	'todate' => array(
		'CType' => 'text',
		'VType' => 'date',
		'DateType' => DATE_TYPE_ALL,
		'DisplayName' => 'До дата',
		'AllowNulls' => true,
		'AddTags' => array(
			'class' => 'coolinp',
		),
	),
	'dsc' => array(
		'CType' => 'select',
		'VType' => 'int',
		'SrcValues' => 'SELECT dsc.id as id, dsc.name as name
						FROM dsc JOIN dsg ON (dsc.dsgid = dsg.id)
						ORDER BY id',
		'DisplayName' => 'Дискусия',
		'AllowNulls' => true,
		'AddTags' => array(
			'class' => 'coolinp',
		),
	),
	'msg_select' =>array(
		'CType' => 'select',
		'VType' => 'int',
		'SrcValues' => array(
			1 => 'само основни',
			2 => 'под основните',
		),
		'DisplayName' => 'Мнения',
		'AddTags' => array(
			'class' => 'coolinp',
		),
		
	),
	'topicid' => array(
		'CType' => 'text',
		'VType' => 'int',
		'DisplayName' => 'ID на тема',
		'AllowNulls' => true,
		'AddTags' => array(
			'class' => 'coolinp',
		),
	),
	'author' => array(
		'CType' => 'text',
		'VType' => 'string',
		'DisplayName' => 'Автор',
		'AllowNulls' => true,
		'AddTags' => array(
			'class' => 'coolinp',
		),
	),
	'ip' => array(
		'CType' => 'text',
		'VType' => 'string',
		'DisplayName' => 'IP',
		'AllowNulls' => true,
		'AddTags' => array(
			'class' => 'coolinp',
		),
	),
	'title' => array(
		'CType' => 'text',
		'VType' => 'string',
		'DisplayName' => 'Заглавие',
		'AllowNulls' => true,
		'AddTags' => array(
			'class' => 'coolinp',
		),
	),
	'msg' => array(
		'CType' => 'text',
		'VType' => 'string',
		'DisplayName' => 'Съобщение',
		'AllowNulls' => true,
		'AddTags' => array(
			'class' => 'coolinp',
		),
	),
	'pagesize' => array(
		'CType' => 'select',
		'VType' => 'int',
		'SrcValues' => array(
			30 => 30,
			60 => 60,
			100 => 100,
			150 => 150,
			200 => 200,
		),
		'DisplayName' => 'Бр. резултати на страница',
		'AddTags' => array(
			'class' => 'coolinp',
		),
	),
	'showdeleted' => array(
		'CType' => 'checkbox',
		'VType' => 'int',
		'SrcValues' => array(
			1 => '',
		),
		'DisplayName' => 'Покажи изтритите',
		'AllowNulls' => true,
	),
	'search' => array(
		'CType' => 'action',
		'DisplayName' => 'Търси',
		'AddTags' => array(
			'class' => 'frmbutton',
		),	
		'ActionMask' => ACTION_CHECK | ACTION_SHOW,
		'SQL' => '{fromdate}{todate}{dsc}{msg_select}{topicid}{author}{ip}{title}{msg}{pagesize}{showdeleted}',
	),
);

$f1 = new kfor($t1,$h1,'GET');

$f1->ExecAction();
if($f1->lFieldArr['fromdate']['CurValue'])
	$where .= ' AND m.mdate>\'' . $f1->lFieldArr['fromdate']['CurValue'] . '\'::date';
if($f1->lFieldArr['todate']['CurValue'])
	$where .= ' AND m.mdate<\'' . $f1->lFieldArr['todate']['CurValue'] . '\'::date';
if($f1->lFieldArr['dsc']['CurValue'])
	$where .= ' AND m.dscid=' .  (int) $f1->lFieldArr['dsc']['CurValue'];
if($f1->lFieldArr['msg_select']['CurValue'] == 1)
	$where .= ' AND m.id=m.rootid';
if($f1->lFieldArr['topicid']['CurValue'])
	$where .= ' AND m.rootid=' .  (int) $f1->lFieldArr['topicid']['CurValue'];
if($f1->lFieldArr['author']['CurValue'])
	$where .= ' AND m.author=\'' .  $f1->lFieldArr['author']['CurValue'] . '\'';
if($f1->lFieldArr['ip']['CurValue'])
	$where .= ' AND m.senderip=\'' .  $f1->lFieldArr['ip']['CurValue'] . '\'';
if($f1->lFieldArr['title']['CurValue'])
	$where .= ' AND m.subject LIKE \'%' .  $f1->lFieldArr['title']['CurValue'] . '%\'';
if($f1->lFieldArr['msg']['CurValue'])
	$where .= ' AND m.msg=\'' .  $f1->lFieldArr['msg']['CurValue'] . '\'';
if(!$f1->lFieldArr['showdeleted']['CurValue'])
	$where .= ' AND m.flags & 4 <> 4';

if ($f1->lCurAction == 'new') $where = ' AND m.dscid=2 AND m.flags & 4 <> 4';

echo $f1->Display();

$form_button = '
	<p style="text-align: right; padding-right: 10px;">
	<input type="checkbox" name="markAllDel" value="" onclick="javascript: forumSearchMarkAll(1);" /> Маркирай всички като <span style="font-weight: bold;">"изтрити"</span> 
	<input type="checkbox" name="markAllHide" value="" onclick="javascript: forumSearchMarkAll(2);" />Маркирай всички като <span style="font-weight: bold;">"скрити"</span> 
	<input style="margin-left:10px;" type="submit" name="submit" value="Запази промените" class="frmbutton", />
	<input type="hidden" name="rurl" value="' . $_SERVER['QUERY_STRING'] . '">
	</p>';
	
if(!$f1->lErrorCount && ($f1->lCurAction == 'search' || $f1->lCurAction == 'new')){
	$h = '
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
					<th class="gridtools" colspan="9">
						Мнения
					</th>
				</tr>
				<tr>
					<td colspan="9">
						'.$form_button.'
					</td>
				</tr>
		';
	$t = '
				<tr>
					<td>{_EditLink}</td>
					<td>{_showMainMsgIfComment}</td>
					<td>{msghtml}</td>
					<td>{author}</td>
					<td>{senderip}</td>
					<td>{mdate}</td>
					<td>{_getMsgFlags}</td>
				</tr>
	';
	
	$foot = '
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

	$f = new DBList($h);
	$f->SetCloseTag($foot);
	$f->SetTemplate($t);
	$f->SetOrderParams((int)$_GET['ob1'], (int)$_GET['odd1']);
	$f->SetOrderParamNames('ob1', 'odd1');
	if($f1->lFieldArr['pagesize']['CurValue'])$f->SetPageSize($f1->lFieldArr['pagesize']['CurValue']);
	$f->SetAntet(
			array(
				1 => array('caption' => 'Тема'),
				2 => array('caption' => 'Осн.съобщение'),
				3 => array('caption' => 'Съобщение'),
				4 => array('caption' => 'Автор'), 
				5 => array('caption' => 'IP адрес'),
				6 => array('caption' => 'Дата / час', "def"),
				0 => array('caption' => '&nbsp;'),
			)
		);
	$f->SetQuery('SELECT coalesce(m.subject, (CASE m.dscid WHEN 2 THEN \'Отговор\' ELSE \'Коментар\'  END)) as subject, m.msghtml, m.author, m.senderip, 
			date_trunc(\'second\', m.mdate) as mdate, m.id , m.dscid, m.itemid,  m.msg , m.rootid , m.ord , m.points ,  m.uid ,  m.flags,  m.replies ,  m.views,  
			m.lastmoddate , m.uname ,  m.fighttype, m2.subject as mainsubject
			FROM msg m
			LEFT JOIN msg m2 ON m2.id = m.rootid and m2.rootid = m2.id
			WHERE true ' . $where);
	
	//~ echo 'SELECT *  FROM msg WHERE true ' . $where;
	echo '<form name="forumsearchsubmit" method="POST" action="' . $_SERVER['SCRIPT_NAME'] . '">';
	
	$noResults = $h . '<tr><td colspan="8"><p align="center"><b>Няма намерени съобщения по зададения критерий:</b> &nbsp;'. $filters_str.'.</p></td></tr>' . $foot;
	
	if(!$f->DisplayList((int)$_GET['p'])){
		echo $noResults ;
	}else echo $form_button;
		echo '</form>';
		  
}
HtmlEnd() ;


function getMsgFlags($pRs) {
	$res = '';
	if ($pRs['id'] == $pRs['rootid']) {
		$res .= 'Затворена:<input '. setchkclose($pRs) .' type="checkbox" name="closereply[]" value="'. $pRs['id'] .'">';
		$res .= 'Изтрито:<input '. setchkdel($pRs) .' type="checkbox" name="delreply[]" value="'. $pRs['id'] .'">
			<input type="hidden" name="control[]" value="'. $pRs['id'] .'_'. getFlagClose($pRs)  . '_'. getFlagDel($pRs) . '_root">';		
			
		
	} else {
		$res .= 'Изтрито:<input '. setchkdel($pRs) .' type="checkbox" name="delreply[]" value="'. $pRs['id'] .'"><br />
				Скрито:<input '. setchkhide($pRs) .' type="checkbox" name="hidereply[]" value="'. $pRs['id'] .'">
				<input type="hidden" name="control[]" value="'.$pRs['id'] .'_'. getFlagHide($pRs) .'_'. getFlagDel($pRs) .'"> ';		
	}	
	return $res;
}
function setchkhide($pRs) {
	if ((int)$pRs['flags'] & 2)
		return "checked";
	else 
		return "";
}

function setchkdel($pRs) {
	if ((int)$pRs['flags'] & 4)
		return "checked";
	else 
		return "";
}

function setchkclose($pRs) {
	if ((int)$pRs['flags'] & 1)
		return 'checked';
	else 
		return '';
}
function getFlagClose($pRs) {
	if ((int)$pRs['flags'] & 1)
		return "1";
	else 
		return "0";
}

function getFlagHide($pRs) {
	if ((int)$pRs['flags'] & 2)
		return "2";
	else 
		return "0";
}

function getFlagDel($pRs) {
	if ((int)$pRs['flags'] & 4)
		return "4";
	else 
		return "0";
}

function control() {
	$gSqlStr = '';
	foreach($_POST['control'] as $v) {
		if (!is_array($_POST['closereply'])) $_POST['closereply'] = array();
		if (!is_array($_POST['hidereply'])) $_POST['hidereply'] = array();
		if (!is_array($_POST['delreply'])) $_POST['delreply'] = array();
		var_dump($v);
		$gTmpArr = split("_", $v);
		$gMsgId = (int)$gTmpArr[0];
		
		if (count($gTmpArr) == 4) {
			$gCloseFlag = (int)$gTmpArr[1];
			$gDelFlag = (int)$gTmpArr[2];;
			$gHideFlag = 0;
		} else {
			$gHideFlag = (int)$gTmpArr[1];
			$gDelFlag = (int)$gTmpArr[2];
			$gCloseFlag = 0;
		}
		var_dump($gDelFlag);
		echo '<br/><br/>';
		var_dump($_POST);
		
		$gTmpFlags = 0;
			
		if ((!in_array($gMsgId, $_POST['closereply']) && $gCloseFlag == 1) || (in_array($gMsgId, $_POST['closereply']) && $gCloseFlag == 0)) {
			$gTmpFlags |= 1;
		}
		
		if ((!in_array($gMsgId, $_POST['hidereply']) && $gHideFlag == 2) || (in_array($gMsgId, $_POST['hidereply']) && $gHideFlag == 0)) {
			$gTmpFlags |= 2;
		}
			
		if ((!in_array($gMsgId, $_POST['delreply']) && $gDelFlag == 4) || (in_array($gMsgId, $_POST['delreply']) && $gDelFlag == 0)) {
			$gTmpFlags |= 4;
			//~ $gDelUndel = 1;
		//~ } else {
			//~ $gDelUndel = 0;
		}
		echo '<br/>' . $gTmpFlags;
		
		if ($gTmpFlags) {
			$gSqlStr .= 'SELECT * FROM ForumSetFlags(' . $gMsgId . ', '.$gTmpFlags . ');';
		}
			
		/*if ($gDelUndel) {
			IndexerFilesForum($gMsgId, $gSiteId);
		}*/
	}
	//~ exit();
	$gCn = Con();
		$gCn->Execute($gSqlStr);
	header("Location: http://" . $_SERVER['SERVER_NAME'] . $_SERVER['SCRIPT_NAME'] . "?" . $_POST['rurl']);	
}

function EditLink($pRs){
	if(in_array($pRs['dscid'],array(1,3))&&($pRs['id']==$pRs['rootid'])){
		return ($pRs['showOrdStory'] ? getOrdNum($pRs['ord'] ) : ($pRs['showOrdTopic'] ? getOrdNum1($pRs['ord']) : '')) . $pRs['subject'] ;
	}else{
		return '<a href="edit.php?id=' . $pRs['id'] . '&dscid=' . $pRs['dscid'] . '&tAction=show">
				'. ($pRs['showOrdStory'] ? getOrdNum($pRs['ord'] ) : ($pRs['showOrdTopic'] ? getOrdNum1($pRs['ord']) : '')) . $pRs['subject'] . '</a>';
	}

}

function showMainMsgIfComment($pRs){
	if($pRs['id'] != $pRs['rootid']){
		return 'към "'.$pRs['mainsubject'].'"';
	}else{
		return '"'.$pRs['mainsubject'].'"';;
	}

}

//~ function EditLink($pRs){
	//~ if(in_array($pRs['rootid'],array(1))&&($pRs['id']==$pRs['rootid'])){
		//~ return ($pRs['showOrdStory'] ? getOrdNum($pRs['ord'] ) : ($pRs['showOrdTopic'] ? getOrdNum1($pRs['ord']) : '')) . $pRs['subject'] ;
	//~ }else{
		//~ return '<a href="edit.php?id=' . $pRs['id'] . '&dscid=' . $pRs['dscid'] . '&tAction=show">
				//~ '. ($pRs['showOrdStory'] ? getOrdNum($pRs['ord'] ) : ($pRs['showOrdTopic'] ? getOrdNum1($pRs['ord']) : '')) . $pRs['subject'] . '</a>';
	//~ }

//~ }
?>
