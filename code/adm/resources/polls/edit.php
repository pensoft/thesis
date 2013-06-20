<?php
$docroot = getenv("DOCUMENT_ROOT");
require_once($docroot . '/lib/static.php');
$historypagetype=HISTORY_ACTIVE;

HtmlStart();
$t = array(
	'id' => array(
		'VType' => 'int',
		'CType' => 'hidden',
		'DisplayName' => '',
		'AllowNulls' => true,
	),
	
	'question' => array(
		'VType' => 'string',
		'CType' => 'text',
		'AddTags' => array(
			'class' => 'coolinp',
		),
		'DisplayName' => getstr('admin.polls.colQuestion'),
	),
	
	'startdate' => array(
		'VType' => 'date',
		'CType' => 'text',
		'AddTags' => array(
			'class' => 'coolinp',
		),
		'DisplayName' => getstr('admin.polls.colStartdate'),
		'AllowNulls' => true
	),

	'enddate' => array(
		'VType' => 'date',
		'CType' => 'text',
		'AddTags' => array(
			'class' => 'coolinp',
		),
		'DisplayName' => getstr('admin.polls.colEnddate'),
		'AllowNulls' => true
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
		'DisplayName' => getstr('admin.polls.colDescription'),
		'AllowNulls' => true,
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
	
	'showforum' => array (
		'VType' => 'int',
		'CType' => 'hidden',
		'AllowNulls' => false,
		'DefValue' => 1,
	), 
	
	'flags' => array (
		'VType' => 'int',
		'CType' => 'select',
		'DisplayName' => getstr('admin.polls.colFlags'),
		'AllowNulls' => false,
		'AddTags' => array(
			'class' => 'coolinp',
		),
		'SrcValues' => array(
			0 => getstr('admin.no'),
			1 => getstr('admin.yes'),
		),
		'DefValue' => 0,
		
	), 
	
	'status' => array (
		'VType' => 'int',
		'CType' => 'select',
		'DisplayName' => getstr('admin.stories.colState'),
		'AllowNulls' => false,
		'AddTags' => array(
			'class' => 'coolinp',
		),
		'SrcValues' => array(
			0 => getstr('admin.polls.hide'),
			1 => getstr('admin.polls.show'),
		),
		'DefValue' => 1,
		
	), 
	
	'active' => array (
		'VType' => 'int',
		'CType' => 'select',
		'DisplayName' => getstr('admin.polls.colActive'),
		'AllowNulls' => false,
		'AddTags' => array(
			'class' => 'coolinp',
		),
		'SrcValues' => array(
			0 => getstr('admin.no'),
			1 => getstr('admin.yes'),
		),
		'DefValue' => 1,
		
	), 
	
	'pos' => array (
		'VType' => 'int',
		'CType' => 'hidden',
		'AllowNulls' => false,
		'DefValue' => 1,
	), 

	'language' => array(
		'VType' => 'string',
		'CType' => 'select',
		'SrcValues' => 'SELECT langid as id, name FROM languages ORDER BY langid',
		'DefValue' => getlang(true),
		'AddTags' => array(
			'class' => 'coolinp',
		),
		'DisplayName' => getstr('admin.stories.colLang'),
	),
	
	
	'showedit' => array(
		'CType' => 'action',
		'Hidden' => true,
		'DisplayName' => '',
		'SQL' => 'SELECT * FROM sp_poll(0,{id}, null, null, null, null, null, null, null, null, null, null, null,null)',
		'ActionMask' => ACTION_CHECK | ACTION_EXEC | ACTION_FETCH | ACTION_SHOW,
	),
	
	'save' => array(
		'CType' => 'action',
		'DisplayName' => 'Запази',
		'SQL' => 'SELECT * FROM sp_poll(1,{id}, {primarysite}, {question}, {description}, {startdate}, {enddate}, ' . $user->id . ', {showforum}, {pos}, {flags}, {language}, {active},{status})',
		'ActionMask' => ACTION_CHECK | ACTION_CCHECK | ACTION_EXEC | ACTION_FETCH | ACTION_REDIRECT,
		'RedirUrl' => '',
		'AddTags' => array(
			'class' => 'frmbutton',
		), 
	),
	
	'delete' => array(
		'CType' => 'action',
		'DisplayName' => 'Изтрий',
		'SQL' => 'SELECT * FROM sp_poll(3,{id}, null, null, null, null, null, null, null, null, null, null, null, null)',
		'ActionMask' => ACTION_CHECK | ACTION_CCHECK | ACTION_EXEC | ACTION_FETCH | ACTION_REDIRECT,
		'Hidden' => false,
		'RedirUrl' => '',
		'AddTags' => array(
			'class' => 'frmbutton',
			'onclick' => 'javascript: if (confirm(\'Сигурни ли сте, че искате да изтриете тази анкета?\')) { return true; } else { return false;}',
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

$html = '

	{id}{primarysite}{showforum}{pos}
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
				<th colspan="4">' . ((int)$kfor->lFieldArr['id']['CurValue'] ? 'Редактиране' : 'Добавяне' ) . ' на анкета</th>
			</tr>
			<tr>
				<td colspan="2" valign="top">&nbsp;</td>
				<td colspan="2" valign="top" align="right">{save} {cancel} ' . ((int)$kfor->lFieldArr['id']['CurValue'] ? '{delete}' : '') . '</td>
			</tr>
			<tr>
				<td colspan="2" valign="top"><b>{*question}:</b><br/>{question}</td>
				<td valign="top"><b>{*startdate}:</b><br/>{startdate}</td>
				<td valign="top"><b>{*enddate}:</b><br/>{enddate}</td>
			</tr>
			<tr>
				<td colspan="2" valign="top"  rowspan="2"><b>{*description}:</b><br/>{description}</td>
				<td valign="top"><b>{*flags}:</b><br/>{flags}</td>
				<td valign="top"><b>{*language}:</b><br/>{language}</td>
			</tr>
			<tr>
				
				<td  valign="top"><b>{*active}:</b><br/>{active}</td>
				<td  valign="top"><b>{*status}:</b><br/>{status}</td>
			</tr>
			<tr>
				<td colspan="2">&nbsp;</td>
				<td colspan="2" align="right">{save} {cancel} ' . ((int)$kfor->lFieldArr['id']['CurValue'] ? '{delete}' : '') . '</td>
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

$kfor->lFormHtml = $html;
echo $kfor->Display();

if((int)$kfor->lFieldArr['id']['CurValue'] ){
	$t = '
	<tr>
		<td>{id}</td>
		<td><a href="/resources/polls/answeredit.php?tAction=showedit&id={id}">{ans}</a></td>
		<td>{votes}</td>
		<td align="right" nowrap="true">
				<a href="/resources/polls/answeredit.php?tAction=showedit&id={id}"><img src="/img/edit.gif" alt="Редактирай" title="Редактирай" border="0" /></a>
		</td>
	</tr>
	';

	$gFArr = array(
		1 => array('caption' => getstr('admin.polls.colID'), 'deforder' => 'asc', 'def'), 
		2 => array('caption' => getstr('admin.polls.colAnswer'), 'deforder' => 'asc'), 
		3 => array('caption' => getstr('admin.polls.colAnswerVotes'), 'deforder' => 'asc'), 
		1000 => array('caption' => '  ', 'deforder' => 'asc'),
	);


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
					<tr>
						<th class="gridtools" colspan="8">
							<a href="./answeredit.php?pollid=' . (int)$kfor->lFieldArr['id']['CurValue']  . '">' . getstr('admin.polls.addAnswer') . '</a>
							' . getstr('admin.polls.answersantetka') . '
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
	$l->SetQuery('SELECT id, ans, votes
		FROM pans WHERE pollid = ' . (int)$kfor->lFieldArr['id']['CurValue'] . '
		');

	if (!$l->DisplayList($page)) {
		echo $lTableHeader . '<tr><td colspan="8"><p align="center"><b>' . getstr('admin.polls.noAnswers') . '</b></p></td></tr>' . $lTableFooter;
	}
}

HtmlEnd();
?>