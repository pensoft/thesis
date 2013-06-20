<?php
$docroot = getenv("DOCUMENT_ROOT");
require_once($docroot . '/lib/static.php');
$historypagetype=HISTORY_ACTIVE;

$lHide = 0;
if ($_REQUEST['mode'] == 'rel') {
	$lHide = 1;
}

function MyStripTags($pArr) {
	if ($pArr['newstext']) {
		return '\'' . q(strip_tags($pArr['newstext'])) . '\'';
	}
	return 'null';
}

HtmlStart($lHide);

$t = array(
	'rtfchanged' => array(
		'VType' => 'int',
		'CType' => 'hidden',
		'AddTags' => array(
			'id' => 'rtfchanged',
		),
		'DefValue' => '0',
		'AllowNulls' => true,
	),
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
	
	'subtitle' => array(
		'VType' => 'string',
		'CType' => 'text',
		'AddTags' => array(
			'class' => 'coolinp',
		),
		'DisplayName' => getstr('admin.stories.colSubTitle'),
		'AllowNulls' => true
	),

	'nadzaglavie' => array(
		'VType' => 'string',
		'CType' => 'text',
		'AddTags' => array(
			'class' => 'coolinp',
		),
		'DisplayName' => getstr('admin.stories.colSupTitle'),
		'AllowNulls' => true
	),

	'link' => array(
		'VType' => 'string',
		'CType' => 'text',
		'AddTags' => array(
			'class' => 'coolinp',
			'onblur' => 'if(this.value && String(this.value).substring(0,5)!=\'http:\') this.value=\'http://\'+this.value;',
		),
		'DisplayName' => getstr('admin.stories.colLink'),
		'AllowNulls' => true
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
			'style' => 'height: 65px;',
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
	
	'newstext' => array(
		'VType' => 'string',
		'CType' => 'textarea',
		'DisplayName' => 'Текст',
		'AllowNulls' => true,
		//'RichText' => FCK_ALL_TOOLS,
		'AddTags' => array(
			'style' => 'width: 100%',
			'rows' => '30',
			'id' => 'newstext',
		),
	),	

	'keywords' => array(
		'VType' => 'string',
		'CType' => 'textarea',
		'AllowNulls' => true,
		'AddTags' => array(
			'class' => 'coolinp',
			'style' => 'height: 65px;',
		),
		'DisplayName' => getstr('admin.stories.colKeywords'),
		'Checks' => array(
			CKMAXSTRLEN('{keywords}', 4096),
		),
	),
	
	'storytype' => array(
		'VType' => 'int',
		'CType' => 'select',
		'SrcValues' => array(null => 'Статия'),
		'AddTags' => array(
			'class' => 'coolinp',
		),
		'DisplayName' => getstr('admin.stories.colStoryType'),
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
	
	'mainrubr' => array (
		'VType' => 'int' ,
		'CType' => 'select' ,
		'SrcValues' => 'SELECT null AS id, \'--\' as name, \'\' as pos, 0 as ord
				UNION 
			SELECT *, 1 as ord FROM (
				SELECT id, (case WHEN id = rootnode THEN ' . getsqlang('name') . ' 
						ELSE repeat(\'&nbsp;\', length(pos)) || \'- \' || ' . getsqlang('name') . ' 
					end) as name, pos
				FROM rubr WHERE sid = 1 
				ORDER BY rootnode, (case WHEN id = rootnode THEN 0 ELSE 1 end), pos 
			) a 
			ORDER BY ord, pos
		',
		'AllowNulls' => false,
		'AddTags' => array(
			'class' => 'coolinp',
		),
		'DisplayName' => getstr('admin.stories.colMainRubric'),
	),

	'rubr' => array (
		'VType' => 'int' ,
		'CType' => 'mselect' ,
		'SrcValues' => 'SELECT id, (case WHEN id = rootnode THEN ' . getsqlang('name') . ' 
				ELSE repeat(\'&nbsp;\', length(pos)) || \'- \' || ' . getsqlang('name') . ' end) as name 
			FROM rubr WHERE sid = 1
			ORDER BY pos
		',
		'AllowNulls' => true,
		'TransType' => MANY_TO_STRING,
		'AddTags' => array(
			'class' => 'coolinp',
			'style' => 'height: 100px;',
		),
		'DisplayName' => getstr('admin.stories.colAddRubrics'),
	),
	
	'rubrstr' => array(
		'VType' => 'text',
		'CType' => 'hidden',
		'DisplayName' => '',
		'AllowNulls' => true,
	),
	
	'priority' => array (
		'VType' => 'int' ,
		'CType' => 'select' ,
		'SrcValues' => array(1 => 1, 2 => 2, 3 => 3, 4 => 4, 5 => 5, 6 => 6, 7 => 7, 8 => 8 , 9 => 9),
		'AllowNulls' => false,
		'AddTags' => array(
			'class' => 'coolinp',
			'onchange' => 'CreateChange();',
		),
		'DisplayName' => getstr('admin.stories.colPriority'),	
	),
	
	'indexer' => array (
		'VType' => 'int',
		'CType' => 'select',
		'DefValue' => 1,
		'SrcValues'	=> array(0 => 'Не се индексира', 1 => 'Индексира се цялата статия'),
		'AddTags' => array(
			'class' => 'coolinp',
		),
		'DisplayName' => getstr('admin.stories.colIndexer'),
	),
	
	'journal_id' => array (
		'VType' => 'int' ,
		'CType' => 'select' ,
		'SrcValues' => 'SELECT null AS id, \'--\' as name, 0 as ord
				UNION 
			SELECT id, name, 1 as ord FROM journals
			ORDER BY ord
		',
		'AllowNulls' => false,
		'AddTags' => array(
			'class' => 'coolinp',
		),
		'DisplayName' => getstr('admin.stories.colJournals'),
	),
	
	'showedit' => array(
		'CType' => 'action',
		'Hidden' => true,
		'DisplayName' => '',
		'SQL' => 'SELECT * FROM GetStoriesBaseData({guid}, ' . getlang() . ')',
		'ActionMask' => ACTION_CHECK | ACTION_EXEC | ACTION_FETCH | ACTION_SHOW,
	),
	
	'save' => array(
		'CType' => 'action',
		'DisplayName' => 'Запази',
		'SQL' => 'SELECT SaveStoriesBaseData({guid}, {primarysite}, {language}, {title}, {link}, {description}, {pubdate}, {author}, 
			\'' . $user->id . '\', {keywords}, {state}, {subtitle}, {nadzaglavie}, {storytype}, {mainrubr}, {rubr}, 
			{priority}, {indexer}, {showforum}, ' . (int)STORIES_DSCID . ', {_MyStripTags}, {journal_id}) as guid',
		'ActionMask' => ACTION_CHECK | ACTION_CCHECK | ACTION_EXEC | ACTION_FETCH | ACTION_REDIRECT,
		'RedirUrl' => '',
		'AddTags' => array(
			'class' => 'frmbutton',
			'onclick' => 'Confirm = false; SaveDelButton = true;',
		), 
	),
	
	'svelements' => array(
		'CType' => 'action',
		'DisplayName' => 'Свързани елементи',
		'SQL' => '',
		'ActionMask' => ACTION_REDIRECT,
		'RedirUrl' => './extra.php?guid={guid}&title={title}',
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
			'onclick' => 'javascript: Confirm = false; SaveDelButton = true; if (confirm(\'Сигурни ли сте, че искате да изтриете тази статия?\')) { return true; } else { return false;}',
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

$kfor = new kfor($t, $h, 'POST');
$kfor->debug = false;

if ($kfor->lCurAction == 'showedit' && (int)$kfor->lFieldArr['guid']['CurValue']) 
	$kfor->lFieldArr['newstext']['CurValue'] = file_get_contents(PATH_STORIES . $kfor->lFieldArr['guid']['CurValue'] . '.html');

if ($kfor->lCurAction == 'save') {
	// Parsvane na special quotes:
	$kfor->lFieldArr['title']['CurValue'] = parseSpecialQuotes($kfor->lFieldArr['title']['CurValue']);
	$kfor->lFieldArr['subtitle']['CurValue'] = parseSpecialQuotes($kfor->lFieldArr['subtitle']['CurValue']);
	$kfor->lFieldArr['nadzaglavie']['CurValue'] = parseSpecialQuotes($kfor->lFieldArr['nadzaglavie']['CurValue']);
	$kfor->lFieldArr['description']['CurValue'] = parseSpecialQuotes($kfor->lFieldArr['description']['CurValue']);
	$kfor->lFieldArr['author']['CurValue'] = parseSpecialQuotes(replaceBom($kfor->lFieldArr['author']['CurValue']));
	
	$kw = convertKwds(parseSpecialQuotes($kfor->lFieldArr['keywords']['CurValue']));
	$kw = trim($kw);
	if (substr($kw,strlen($kw)-1,1) == ',') {
		$kfor->lFieldArr['keywords']['CurValue'] = substr($kw,0,strlen($kw)-1);
	} else {
		$kfor->lFieldArr['keywords']['CurValue'] = $kw;
	}
}

$kfor->ExecAction();

$html = '
	' . ($_GET['mode'] == 'rel' ? '<input name="mode" type="hidden" value="rel" />' : '') . '
{primarysite}{guid}{rtfchanged}
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
			<th colspan="4">' . ((int)$kfor->lFieldArr['guid']['CurValue'] ? 'Редактиране' : 'Добавяне' ) . ' на статия</th>
		</tr>
		' . ((int)$kfor->lFieldArr['guid']['CurValue'] ? 
			'
				<tr>
					<td colspan="2" valign="top">
						<b>{*lastmod}:</b> {@lastmod}<br/>
						<b>{*createuid}:</b> {@createuid}<br/>
					</td>
					<td colspan="2" valign="top" align="right">{svelements} {save} {cancel} {delete}</td>
				</tr>
			' : '
				<tr>
					<td colspan="2" valign="top">&nbsp;</td>
					<td colspan="2" valign="top" align="right">{svelements} {save} {cancel} {delete}</td>
				</tr>
			'
		) . '
		<tr>
			<td colspan="2"><b>{*mainrubr}:</b><br/>{mainrubr}</td>
			<td><b>{*priority}:</b><br/>{priority}</td>
			<td><b>{*language}:</b><br/>{language}</td>
		</tr>
		<tr>
			<td colspan="2" rowspan="3" valign="top"><b>{*rubr}:</b><br/>{rubr}<br/>{#rubrstr}</td>
			<td valign="top"><b>{*storytype}:</b><br/>{storytype}</td>
			<td valign="top"><b>{*indexer}:</b><br/>{indexer}</td>
		</tr>
		<tr>
			<td valign="top"><b>{*pubdate}:</b> <a href="javascript: void(0);" onclick="InsertCurTime(\'pubdate\');return false;"><img src="/img/clock.gif" alt="Въведи моментното време" align="absmiddle" title="Въведи моментното време" border="0" /></a><br/>{pubdate}</td>
			<td valign="top"><b>{*journal_id}:</b><br/>{journal_id}</td>
		</tr>
		<tr>
			<td valign="top"><b>{*state}:</b><br/>{state}</td>
			<td valign="top"><br/>{showforum}</td>
		</tr>
		<tr>
			<td colspan="2" valign="top"><b>{*author}:</b><br/>{author}</td>
			<td colspan="2" valign="top"><b>{*link}:</b><br/>{link}</td>
		</tr>
		<tr>
			<td colspan="4" valign="top"><b>{*title}:</b><br/>{title}</td>
		</tr>
		<tr>
			<td colspan="2" valign="top"><b>{*nadzaglavie}:</b><br/>{nadzaglavie}</td>
			<td colspan="2" valign="top"><b>{*subtitle}:</b><br/>{subtitle}</td>
		</tr>
		<tr>
			<td colspan="2" valign="top"><b>{*description}:</b><br/>{description}</td>
			<td colspan="2" valign="top"><b>{*keywords}:</b><br/>{keywords}</td>
		</tr>
		<tr>
			<td colspan="4">{newstext}</td>
		</tr>
		<tr>
			<td colspan="2">&nbsp;</td>
			<td colspan="2" align="right">{svelements} {save} {cancel} ' . ((int)$kfor->lFieldArr['guid']['CurValue'] ? '{delete}' : '') . '</td>
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
	{primarysite}{guid}{rtfchanged}
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
				<th colspan="4">' . ((int)$kfor->lFieldArr['guid']['CurValue'] ? 'Редактиране' : 'Добавяне' ) . ' на статия</th>
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
				<td colspan="2"><b>{*mainrubr}:</b><br/>{@mainrubr}</td>
				<td><b>{*priority}:</b><br/>{@priority}</td>
				<td><b>{*language}:</b><br/>{@language}</td>
			</tr>
			<tr>
				<td colspan="2" rowspan="3" valign="top"><b>{*rubr}:</b><br/>{@rubr}</td>
				<td valign="top"><b>{*storytype}:</b><br/>{@storytype}</td>
				<td valign="top"><b>{*indexer}:</b><br/>{@indexer}</td>
			</tr>
			<tr>
				<td valign="top"><b>{*pubdate}:</b><br/>{@pubdate}</td>
				<td valign="top"><br/>{@showforum}</td>
			</tr>
			<tr>
				<td colspan="2" valign="top"><b>{*state}:</b><br/>{@state}</td>
			</tr>
			<tr>
				<td colspan="2" valign="top"><b>{*author}:</b><br/>{@author}</td>
				<td colspan="2" valign="top"><b>{*link}:</b><br/>{@link}</td>
			</tr>
			<tr>
				<td colspan="4" valign="top"><b>{*title}:</b><br/>{@title}</td>
			</tr>
			<tr>
				<td colspan="2" valign="top"><b>{*nadzaglavie}:</b><br/>{@nadzaglavie}</td>
				<td colspan="2" valign="top"><b>{*subtitle}:</b><br/>{@subtitle}</td>
			</tr>
			<tr>
				<td colspan="2" valign="top"><b>{*description}:</b><br/>{@description}</td>
				<td colspan="2" valign="top"><b>{*keywords}:</b><br/>{@keywords}</td>
			</tr>
			<tr>
				<td colspan="4">{@newstext}</td>
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

	$html .= '
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
		$html .= '<p>'. getstr('admin.stories.changeLogRow', $log) . $level . '</p>';
		$lState = $log['status'];
	}
	
	$html .= '
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

if ($kfor->lCurAction == 'save' && $kfor->lErrorCount == 0) {
	if ($lSiteRights[$kfor->lFieldArr['primarysite']['CurValue']] != 'edit')
		$kfor->SetError('newstext', 'Нямате права за редактиране на тази статия');
	
	if ($kfor->lErrorCount == 0) {
		clearcacheditems2('stories', $kfor->lFieldArr['primarysite']['CurValue']);
		$gId = $kfor->lFieldArr['guid']['CurValue'];
		$fh = fopen(PATH_STORIES . $gId . '.html', 'w');
		fwrite($fh, parseUrls(parseSpecialQuotes($kfor->lFieldArr['newstext']['CurValue'])));
		fclose($fh);
	}
}

if ($kfor->lCurAction == 'delete' && (int)$kfor->lFieldArr['guid']['CurValue'] && $kfor->lErrorCount == 0) {
	delStoryFile((int)$kfor->lFieldArr['guid']['CurValue']);
}

echo $kfor->Display();

//Not saved changes
echo '
	<script language="JavaScript">
		SetEvent(\'def1\');
		window.onbeforeunload = ConfirmToExit;
	</script>
	<script>
		CKEDITOR.replace( "newstext",
		{
			skin : "office2003",
			removePlugins: \'elementspath\',
		});
	</script>
';

HtmlEnd($lHide);

?>