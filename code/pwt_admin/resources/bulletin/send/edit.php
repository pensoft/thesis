<?php
$docroot = getenv("DOCUMENT_ROOT");
require_once($docroot . '/lib/static.php');
$historypagetype=HISTORY_ACTIVE;

$guid = (int)$_GET['guid'];
//~ echo $guid;
$lHide = 0;
if ($_REQUEST['mode'] == 'rel') {
	$lHide = 1;
}

HtmlStart($lHide);

$lStoryTypeArray = array(8 => 'Бюлетин');
$lStoryTypeDefValue = 8;
$lBulletinSendToArray = array('all' =>'Всички', 'viktorp@etaligent.net' => 'Виктор');
$lBulletinSendToDefValue = 'viktorp@etaligent.net';
$lBulletinSendSubject = array('Честита нова година' => 'Честита нова година', 'Честита Коледа' => 'Честита Коледа', 'Бюлетин - Правен Свят' =>'Бюлетин - Правен Свят');
$lBulletinSendSubjectDefValue = 'Бюлетин - Правен Свят';
$t = array(
	'guid' => array(
		'VType' => 'int',
		'CType' => 'hidden',
		'DisplayName' => '',
		'AllowNulls' => true,
	),
	
	'title' => array(
		'VType' => 'string',
		'CType' => 'text',
		'AddTags' => array(
			'class' => 'coolinp',
		),
		'DisplayName' => getstr('admin.stories.colTitle'),
	),
	
	'storytype' => array(
		'VType' => 'int',
		'CType' => 'select',
		'SrcValues' => $lStoryTypeArray,
		'AddTags' => array(
			'class' => 'coolinp',
		),
		'DisplayName' => getstr('admin.stories.colStoryType'),
		'DefValue' => $lStoryTypeDefValue,
		'AllowNulls' => true,
	),	
	
	'lastmod' => array(
		'VType' => 'date',
		'CType' => 'text',
		'AddTags' => array(
			'class' => 'coolinp',
		),
		'DisplayName' => getstr('admin.stories.colLastMod'),
	),
	
	'createuid' => array(
		'VType' => 'string',
		'CType' => 'text',
		'DisplayName' => 'Създал',
		'AddTags' => array(
			'style' => 'width: 120px',
		),
	),
	
	'primarysite' => array (
		'VType' => 'int',
		'CType' => 'hidden',
		'AllowNulls' => false,
		'DefValue' => 1,
	), 
	
	'emailsendto' => array(
		'VType' => 'string',
		'CType' => 'select',
		'SrcValues' => $lBulletinSendToArray,
		'DefValue' => $lBulletinSendToDefValue,
		'AddTags' => array(
			'class' => 'coolinp',
		),
		'DisplayName' => getstr('admin.bulletin.SendTo'),		
	),
	
	'subject' => array(
		'VType' => 'string',
		'CType' => 'select',
		'SrcValues' => $lBulletinSendSubject,
		'DefValue' => $lBulletinSendSubjectDefValue,
		'AddTags' => array(
			'class' => 'coolinp',
		),
		'DisplayName' => getstr('admin.bulletin.Theme'),		
	),
	
	'showforum' => array(
		'VType' => 'int',
		'CType' => 'checkbox',
		'SrcValues' => array(1 => getstr('admin.stories.colShowForum'), 0 => 0),
		'TransType' => MANY_TO_BIT_ONE_BOX,
		'AllowNulls' => true,
		'IsNull' => 0,
		'Separator' => '&nbsp;&nbsp;',
		'DefValue' => 1,
		'DisplayName' => getstr('admin.stories.colShowForum'),
	),
	
	'showedit' => array(
		'CType' => 'action',
		'Hidden' => true,
		'DisplayName' => '',
		'SQL' =>'SELECT * FROM GetBulletinBaseData({guid}, ' . (int) $lStoryTypeDefValue . ' , ' . (int) $user->id . ', \'' . getlang(1) . '\')',
		'ActionMask' => ACTION_CHECK | ACTION_EXEC | ACTION_FETCH | ACTION_SHOW,
	),
	
	'send' => array(
		'CType' => 'action',
		'DisplayName' => 'Изпрати',
		'SQL' => 'SELECT updatestoriesstate('.$guid.')',
		'ActionMask' => ACTION_CHECK | ACTION_CCHECK | ACTION_EXEC | ACTION_FETCH | ACTION_REDIRECT,
		'RedirUrl' => './edit.php?tAction=showedit&guid={guid}',
		'AddTags' => array(
			'class' => 'frmbutton',
			'style' => 'color:red',
			'onclick' => 'javascript: if (confirm(\'Сигурни ли сте, че искате да изпратите този бюлетин ?\')) { return true; } else { return false;}',
		), 	
	),	
	
	'save' => array(
		'CType' => 'action',
		'DisplayName' => 'Запази',
		'SQL' => 'SELECT SaveStoriesBaseData({guid}, {primarysite}, \''.getlang(true).'\', {title}, null, null, \''.date('d/m/Y H:i').'\', null, 
			\'' . $user->id . '\', null, 0, null, null, {storytype}, null, null, 
			1, null, 1, {showforum}, ' . (int)STORIES_DSCID . ', null) as guid',
		'ActionMask' => ACTION_CHECK | ACTION_CCHECK | ACTION_EXEC | ACTION_FETCH | ACTION_REDIRECT,
		'RedirUrl' => './edit.php?tAction=showedit&guid={guid}',
		'AddTags' => array(
			'class' => 'frmbutton',
		), 
	),
	
	'cancel' => array(
		'CType' => 'action',
		'DisplayName' => 'Назад',
		'ActionMask' => ACTION_REDIRECT,
		'RedirUrl' => './',
		'AddTags' => array(
			'class' => 'frmbutton',
		),
	)	
);
//~ $sql = 'UPDATE stories SET state=3 WHERE guid= '. $guid .'';
//~ echo $sql;
$kfor = new kfor($t, $h, 'GET');
$kfor->debug = false;

if ($kfor->lCurAction == 'save') {
	$kfor->lFieldArr['title']['CurValue'] = parseSpecialQuotes($kfor->lFieldArr['title']['CurValue']);
}

$kfor->ExecAction();

if ($kfor->lCurAction == 'send' && $kfor->lErrorCount == 0) {
$sql = 'SELECT DISTINCT ON (relstoryid) sp1.valint, s.title, s.subtitle, s.previewpicid, s.author, s.pubdate, sp.valint as relstoryid, s.primarysite
	FROM storyproperties sp
	INNER JOIN stories s on (sp.valint = s.guid)
	JOIN storyproperties sp1 on (s.guid = sp1.guid) 
	JOIN rubr r on (sp1.valint = r.id)
	WHERE sp.guid =' . $guid . ' AND sp.propid = 3;';
	if($kfor->lFieldArr['emailsendto']['CurValue']=='all'){
	
		$cn = Con();
		$cn->Execute('
			SELECT DISTINCT ON (email) * FROM newsletter
			WHERE confmail = 1 
			ORDER BY email');
			
		$pubdata = array(
			'mailsubject' => $kfor->lFieldArr['subject']['CurValue'],
			'charset' => 'UTF-8',
			'boundary' => '--_separator==_',
			'from' => array(
				'display' => SITE_MAIL_DISPL,
				'email' => SITE_MAIL_ADDR,
			),
			'url' => SITE_URL . '/newsletter.php',
			'styles' => '',
			'templs' => array(
				G_DEFAULT => 'newsletter.mailcontent',
			),
			'nwcontent' => new crs(array(
					'ctype' => 'crs',
					'sqlstr' => $sql,
					'templs' => array(
						G_HEADER => 'bulletinsend.head',
						G_FOOTER => 'bulletinsend.foot',
						G_STARTRS => 'global.empty',
						G_ENDRS => 'global.empty',
						G_ROWTEMPL => 'bulletinsend.browserow',
						G_NODATA => 'global.empty',
						G_PAGEING => 'global.empty'
					),
				)
			),
		);
		
		$messaging = new cmessaging($pubdata);
		$cn->MoveFirst();
		while (!$cn->Eof()) {
			$messaging->SetVal('mailto', $cn->mRs['email']);
			$messaging->SetVal('confhash', $cn->mRs['confhash']);
			$messaging->Display();
			$cn->MoveNext();
		}	
	}
	else{
		$cn = Con();
		$cn->Execute('
			SELECT DISTINCT ON (email) * FROM newsletter
			WHERE confmail = 1 And  email = \'' .$kfor->lFieldArr['emailsendto']['CurValue'].'\'
			ORDER BY email');
			
		$pubdata = array(
			'mailsubject' => $kfor->lFieldArr['subject']['CurValue'],
			'charset' => 'UTF-8',
			'boundary' => '--_separator==_',
			'from' => array(
				'display' => SITE_MAIL_DISPL,
				'email' => SITE_MAIL_ADDR,
			),
			'url' => SITE_URL . '/newsletter.php',
			//~ 'nwcontent' => file_get_contents(PATH_STORIES . (int)$kfor->lFieldArr['guid']['CurValue'] . '.html'),
			'styles' => '',
			'templs' => array(
				G_DEFAULT => 'newsletter.mailcontent',
			),
			'nwcontent' => new crs( array(
					'ctype' => 'crs',
					'sqlstr' => $sql,
					'templs' => array(
						G_HEADER => 'bulletinsend.head',
						G_FOOTER => 'bulletinsend.foot',
						G_STARTRS => 'global.empty',
						G_ENDRS => 'global.empty',
						G_ROWTEMPL => 'bulletinsend.browserow',
						G_NODATA => 'global.empty',
						G_PAGEING => 'global.empty'
					),
				)
			),
		);
		
		$messaging = new cmessaging($pubdata);
		$cn->MoveFirst();
			$messaging->SetVal('mailto', $cn->mRs['email']);
			$messaging->SetVal('confhash', $cn->mRs['confhash']);
			$messaging->Display();
	}
}



$html = '
	' . ($_GET['mode'] == 'rel' ? '<input name="mode" type="hidden" value="rel" />' : '') . '
{primarysite}{guid}
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
			<th colspan="4">' . ((int)$kfor->lFieldArr['guid']['CurValue'] ? 'Редактиране' : 'Добавяне' ) . ' на бюлетин</th>
		</tr>
		' . ((int)$kfor->lFieldArr['guid']['CurValue'] ? 
			'
				<tr>
					<td colspan="2" valign="top">
						<b>{*lastmod}:</b> {@lastmod}<br/>
						<b>{*createuid}:</b> {@createuid}<br/>
					</td>
					<td colspan="2" valign="top" align="right">{save} {cancel}</td>
				</tr>
			' : '
				<tr>
					<td colspan="2" valign="top">&nbsp;</td>
					<td colspan="2" valign="top" align="right">{save} {cancel}</td>
				</tr>
			'
		) . '
		<tr>
			<td colspan="2" valign="top"><b>{*title}:</b><br/>{title}</td>

		</tr>
		<tr>
			<td valign="top"><b>{*emailsendto}:</b><br/>{emailsendto}</td>
			<td valign="top"><b>{*subject}:</b><br/>{subject}</td>
		</tr>
		<tr>
			<td colspan="2">&nbsp;</td>
			<td colspan="2" align="right">{send} {save} {cancel}</td>
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

$lSiteRights = GetSiteRights();
if ($lSiteRights[$kfor->lFieldArr['primarysite']['CurValue']] != 'edit' && $kfor->lFieldArr['guid']['CurValue']) {
	$html = '
		' . ($_GET['mode'] == 'rel' ? '<input name="mode" type="hidden" value="rel" />' : '') . '
	{primarysite}{guid}
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
				<th colspan="4">' . ((int)$kfor->lFieldArr['guid']['CurValue'] ? 'Редактиране' : 'Добавяне' ) . ' на бюлетин</th>
			</tr>
			' . ((int)$kfor->lFieldArr['guid']['CurValue'] ? 
				'
					<tr>
						<td colspan="2" valign="top">
							<b>{*lastmod}:</b> {@lastmod}<br/>
							<b>{*createuid}:</b> {@createuid}<br/>
						</td>
						<td colspan="2" valign="top" align="right">{cancel}</td>
					</tr>
				' : '
					<tr>
						<td colspan="2" valign="top">&nbsp;</td>
						<td colspan="2" valign="top" align="right">{cancel}</td>
					</tr>
				'
			) . '
			<tr>
				<td colspan="4" valign="top"><b>{*title}:</b><br/>{@title}</td>
			</tr>
			<tr>
				<td colspan="2">&nbsp;</td>
				<td colspan="2" align="right">{cancel}</td>
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
}

if ($kfor->lFieldArr['guid']['CurValue']) {
	$chngLogArr = getStoryChangeLog($kfor->lFieldArr['guid']['CurValue']);

	$lLog .= '
	<div class="t">
	<div class="b">
	<div class="l">
	<div class="r">
		<div class="bl">
		<div class="br">
		<div class="tl">
		<div class="tr">
			<fieldset class="changelog">
				<legend>' . getstr('admin.stories.changeLog') . ':</legend>
	';

	foreach($chngLogArr as $log) {
		$log['inittxt'] = ($log['init'] == 1 ? 'създава' : 'променя');
		$log['statustxt'] = $gStoriesStates[$log['status']];
		$level = ($log['status'] != $lState ? getstr('admin.stories.changeLogLevel', $log) : '');
		$lLog .= '<p>'. getstr('admin.stories.changeLogRow', $log) . $level . '</p>';
		$lState = $log['status'];
	}
	
	$lLog .= '
			</fieldset>
		</div>
		</div>
		</div>
		</div>
	</div>
	</div>
	</div>
	</div>
	<br/>
	';
}


$kfor->lFormHtml = $html;

echo $kfor->Display();

showRelatedStories($kfor->lFieldArr['guid']['CurValue'] , (int)$kfor->lFieldArr['primarysite']['CurValue']);
echo $lLog;

HtmlEnd($lHide);
?>