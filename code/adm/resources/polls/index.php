<?php
$docroot = getenv("DOCUMENT_ROOT");
require_once($docroot . '/lib/static.php');
$historypagetype = HISTORY_CLEAR;


$page = (int)$_GET['p'];

HtmlStart();


$t = '
<tr>
	<td>{id}</td>
	<td><a href="/resources/polls/edit.php?tAction=showedit&id={id}">{question}</a></td>
	<td>{startdate}</td>
	<td>{enddate}</td>
	<td>{_GetActivePoll(active)}</td>
	<td>{_GetPollStatus(status)}</td>
	<td align="right" nowrap="true">
			<a href="/resources/polls/edit.php?tAction=showedit&id={id}"><img src="/img/edit.gif" alt="Редактирай" title="Редактирай" border="0" /></a>
			<a href="/resources/polls/edit.php?tAction=delete&id={id}" onclick="javascript: if (confirm(\'Сигурни ли сте, че искате да изтриете тази анкета?\')) { return true; } else { return false;}"><img src="/img/trash2.gif" alt="Изтрий" title="Изтрий" border="0" /></a>
	</td>
</tr>
';

$gFArr = array(
	1 => array('caption' => getstr('admin.polls.colID'), 'deforder' => 'desc', 'def'), 
	2 => array('caption' => getstr('admin.polls.colQuestion'), 'deforder' => 'asc'), 
	3 => array('caption' => getstr('admin.polls.colStartdate'), 'deforder' => 'asc'), 
	4 => array('caption' => getstr('admin.polls.colEnddate'), 'deforder' => 'asc'), 
	5 => array('caption' => getstr('admin.polls.colActive'), 'deforder' => 'asc'), 
	6 => array('caption' => getstr('admin.stories.colState'), 'deforder' => 'asc'), 
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
					<th class="gridtools" colspan="10">
						<a href="./edit.php">' . getstr('admin.polls.addPoll') . '</a>
						' . getstr('admin.polls.antetka') . '
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
$l->SetQuery('SELECT id, question, startdate, enddate, active, status
	FROM poll
	');



if (!$l->DisplayList($page)) {
	echo $lTableHeader . '<tr><td colspan="8"><p align="center"><b>' . getstr('admin.polls.noData') . '</b></p></td></tr>' . $lTableFooter;
}

HtmlEnd();

function GetActivePoll($p) {
	if ($p == 1) {
		return '<span style="color:red;">Да</span>';
	} else {
		return '<span style="color:#000;">Не</span>';
	}
}
function GetPollStatus($p) {
	if ($p == 1) {
		return '<span style="color:red;">Показва се</span>';
	} else {
		return '<span style="color:#000;">Не се показва</span>';
	}
}

?>