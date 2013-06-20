<?php
$docroot = getenv("DOCUMENT_ROOT");
require_once($docroot . '/lib/static.php');
$historypagetype=HISTORY_ACTIVE;

$lHide = 0;
if ($_REQUEST['mode'] == 'rel') {
	$lHide = 1;
}

HtmlStart($lHide);

$lStoryTypeArray = array(1 => 'Галерия');
$lStoryTypeDefValue = 1;
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
	
	'author' => array(
		'VType' => 'string',
		'CType' => 'text',
		'Checks' => array(
			CKMAXSTRLEN('{author}', 128),
		),
		'AddTags' => array(
			'class' => 'coolinp',
		),
		'DisplayName' => getstr('admin.stories.colAuthor'),
		'AllowNulls' => true,
	),
	
	'description' => array(
		'VType' => 'string',
		'CType' => 'textarea',
		'Checks' => array(
			CKMAXSTRLEN('{description}', 4096),
		),
		'AddTags' => array(
			'class' => 'coolinp',
			'style' => 'height: 50px;',
		),
		'DisplayName' => getstr('admin.stories.colDescription'),
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
	
	'state' => array (
		'VType' => 'int' ,
		'CType' => 'select' ,
		'SrcValues' =>	$gStoriesStates,
		'AddTags' => array(
			'class' => 'coolinp',
		),
		'DisplayName' => getstr('admin.stories.colState'),
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
	
	'pubdate' => array(
		'VType' => 'date',
		'CType' => 'text',
		'AddTags' => array(
			'class' => 'coolinp',
			'id' => 'pubdate',
		),
		'DisplayName' => getstr('admin.stories.colPubDate'),
		'DefValue' => date('d/m/Y H:i'),
	),

	'language' => array(
		'VType' => 'string',
		'CType' => 'select',
		'SrcValues' => 'SELECT code as id, name FROM languages ORDER BY langid',
		'DefValue' => getlang(true),
		'AddTags' => array(
			'class' => 'coolinp',
		),
		'DisplayName' => getstr('admin.stories.colLang'),
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
		'SQL' => 'SELECT * FROM GetGalleryBaseData({guid}, ' . (int) $lStoryTypeDefValue . ' , ' . (int) $user->id . ', \'' . getlang(1) . '\')',
		'ActionMask' => ACTION_CHECK | ACTION_EXEC | ACTION_FETCH | ACTION_SHOW,
	),
	
	'save' => array(
		'CType' => 'action',
		'DisplayName' => 'Запази',
		'SQL' => 'SELECT SaveStoriesBaseData({guid}, {primarysite}, {language}, {title}, null, {description}, {pubdate}, {author}, 
			\'' . $user->id . '\', null, {state}, null, null, {storytype}, null, null, 
			1, 1, {showforum}, ' . (int)STORIES_DSCID . ', null) as guid',
		'ActionMask' => ACTION_CHECK | ACTION_CCHECK | ACTION_EXEC | ACTION_FETCH | ACTION_REDIRECT,
		'RedirUrl' => './edit.php?tAction=showedit&guid={guid}',
		'AddTags' => array(
			'class' => 'frmbutton',
		), 
	),
	
	'delete' => array(
		'CType' => 'action',
		'DisplayName' => 'Изтрий',
		'SQL' => 'SELECT * FROM deleteStory({guid})',
		'ActionMask' => ACTION_CHECK | ACTION_CCHECK | ACTION_EXEC | ACTION_FETCH | ACTION_REDIRECT,
		'Hidden' => false,
		'RedirUrl' => '',
		'AddTags' => array(
			'class' => 'frmbutton',
			'onclick' => 'javascript: if (confirm(\'Сигурни ли сте, че искате да изтриете тази галерия?\')) { return true; } else { return false;}',
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

$gFilesRoot = PATH_DL;
$kfor = new kfor($t, null, 'POST' );
$kfor->debug = false;

if ($kfor->lCurAction == 'save') {

	$kfor->lFieldArr['title']['CurValue'] = parseSpecialQuotes($kfor->lFieldArr['title']['CurValue']);
	$kfor->lFieldArr['author']['CurValue'] = parseSpecialQuotes(replaceBom($kfor->lFieldArr['author']['CurValue']));
}

$kfor->ExecAction();


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
			<th colspan="4">' . ((int)$kfor->lFieldArr['guid']['CurValue'] ? 'Редактиране' : 'Добавяне' ) . ' на галерия</th>
		</tr>
		' . ((int)$kfor->lFieldArr['guid']['CurValue'] ? 
			'
				<tr>
					<td colspan="2" valign="top">
						<b>{*lastmod}:</b> {@lastmod}<br/>
						<b>{*createuid}:</b> {@createuid}<br/>
					</td>
					<td colspan="2" valign="top" align="right">{save} {cancel} {delete}</td>
				</tr>
			' : '
				<tr>
					<td colspan="2" valign="top">&nbsp;</td>
					<td colspan="2" valign="top" align="right">{save} {cancel}</td>
				</tr>
			'
		) . '
		<tr>
			<td colspan="2"><b>{*title}:</b><br/>{title}</td>
			<td><b>{*language}:</b><br/>{language}</td>
			<td valign="top"><b>{*pubdate}:</b> <a href="javascript: void(0);" onclick="InsertCurTime(\'pubdate\');return false;"><img src="/img/clock.gif" alt="Въведи моментното време" align="absmiddle" title="Въведи моментното време" border="0" /></a><br/>{pubdate}</td>
		</tr>
		<tr>
			<td colspan="2" valign="top"><b>{*author}:</b><br/>{author}</td>
			<td valign="top"><b>{*state}:</b><br/>{state}</td>
			<td  valign="top"><b>{*showforum}:</b><br/>{showforum}</td>
		</tr>
		<tr>
			<td colspan="2" align="left"><b>{*description}:</b><br/>{description}</td>
			<td colspan="2" align="right">{save} {cancel} ' . ((int)$kfor->lFieldArr['guid']['CurValue'] ? '{delete}' : '') . ' </td>
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
				<th colspan="4">' . ((int)$kfor->lFieldArr['guid']['CurValue'] ? 'Редактиране' : 'Добавяне' ) . ' на галерия</th>
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
				<td><b>{*language}:</b><br/>{@language}</td>
				<td valign="top"><b>{*storytype}:</b><br/>{@storytype}</td>
			</tr>
			<tr>
				<td valign="top"><b>{*pubdate}:</b><br/>{@pubdate}</td>
			</tr>
			<tr>
				<td colspan="2" valign="top"><b>{*state}:</b><br/>{@state}</td>
			</tr>
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

showRelatedPhotos($kfor->lFieldArr['guid']['CurValue'] , (int)$kfor->lFieldArr['primarysite']['CurValue']);
echo $lLog;

HtmlEnd($lHide);
?>