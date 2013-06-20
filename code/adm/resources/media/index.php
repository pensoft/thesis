<?php

$docroot = getenv("DOCUMENT_ROOT");
require_once($docroot . '/lib/static.php');

$historypagetype = HISTORY_CLEAR;

HtmlStart();

$t = array(
	"otden" => array(
		"CType" => "text",
		"VType" => "date",
		"DateType" => DATE_TYPE_DATE,	
		"AddTags" => array(
			"size" => 11,
		),
		"DisplayName" => "От",		
		"AllowNulls" => true,
	),
	
	"ftype" => array (
		"VType" => "int",
		"CType" => "select",
		"SrcValues" => array(null => '---', 3 => 'audio', 4 => 'video'),
		"DisplayName" => "Тип",
		"AllowNulls" => true,
	),
	
	"doden" => array(
		"CType" => "text",
		"VType" => "date",
		"DateType" => DATE_TYPE_DATE,	
		"AddTags" => array(
			"size" => 11,
		),
		"DisplayName" => "До",		
		"AllowNulls" => true,	
	),
	"save" => array(
		"CType" => "action",
		"DisplayName" => "Покажи",
		"SQL" => "",
		"ActionMask" => ACTION_CHECK | ACTION_CCHECK | ACTION_SHOW,
		"RedirUrl" => '',
		"AddTags" => array(
			"class" => "frmbutton",
		),		
	),
);

$h = '<table>
	<tr>
		<td><b>Променяна от:</b> {otden} <a href="#" onclick="jscalshow(this, \'def1\', \'otden\'); return false;"><img src="/img/calico.gif" border="0" title="Изберете дата от календара"></a></td>
		<td><b>до:</b> {doden} <a href="#" onclick="jscalshow(this, \'def1\', \'doden\'); return false;"><img src="/img/calico.gif" border="0" title="Изберете дата от календара"></a></td>
		<td><b>Тип:</b> {ftype}</td>
		<td>{save}</td>
	</tr>
</table>';

$kfor = new kfor($t, $h, "GET");
$kfor->debug = false;
$kfor->ExecAction();
$kfor->Display();	

$gSqlWhere = array();
if ($kfor->lCurAction == 'save' && $kfor->lErrorCount == 0) {
	if ($kfor->lFieldArr['otden']['CurValue']) {
		$gSqlWhere[] = 'date_trunc(\'minute\', lastmod) >= \'' . $kfor->lFieldArr['otden']['CurValue'] . '\'';
	}	
	
	if ($kfor->lFieldArr['doden']['CurValue']) {
		$gSqlWhere[] = 'date_trunc(\'minute\', lastmod) <= \'' . $kfor->lFieldArr['doden']['CurValue'] . '\'';
	}
	
	if ($kfor->lFieldArr['ftype']['CurValue']) {
		$gSqlWhere[] = 'ftype = ' . (int)$kfor->lFieldArr['ftype']['CurValue'];
	}
}
$where = (count($gSqlWhere) ? ' WHERE ' . implode(' AND ', $gSqlWhere) : '');

$tableHead = '
<table class="datatable" border="0" cellpadding="2" cellspacing="0" width="100%">
	<tr bgcolor="#758AB7">
		<td colspan="2" style="color: #FFFFFF;"><b>Медия</b></td>
		<td colspan="6" align="right">
			<a href="./edit.php" style="color: #FFFFFF;"><b>Добави нова медия</b></a>&nbsp;
		</td>
	</tr>';

$gList = new DBList($tableHead) ;
		
$gList->SetTemplate('
<tr>
	<td valign="top" nowrap>
		<a href="./edit.php?guid={guid}&tAction=edit"><img src="/img/edit.gif" alt="Редактирай" title="Редактирай" border="0" /></a>
		<a href="./getmm.php?filename=o_{guid}.mp3"><img src="/img/down.gif" alt="Вземи файл" title="Вземи файл" border="0" /></a>
		{_getMMThumb}
	</td>
	<td valign="top">{_mmFtype}</td>
	<td valign="top">{title}</td>
	<td valign="top">{filenameupl}</td>
	<td valign="top">{_mmModDate}</td>
</tr>');

$gList->SetOrderParams((int)$_GET['ordby'], (int)$_GET['ordd']);

$gFArr = array(
	7 => array('caption' => ' ', 'deforder' => 'asc'),
	8 => array('caption' => 'Тип', 'deforder' => 'asc'),
	1 => array('caption' => 'Заглавие', "def", 'deforder' => 'asc'),
	4 => array('caption' => 'Ориг. име', 'deforder' => 'asc'),
	3 => array('caption' => 'Променяна', 'deforder' => 'asc'),
);
$gList->SetAntet($gFArr);
		
$gList->SetQuery("SELECT title, author, lastmod, filenameupl, length, (dim_x || 'x' || dim_y) as dim, guid, ftype
	FROM getMedia() $where");
$gList->SetPageSize(20) ;
$gList->SetAlternateColors(true);
if (!$gList->DisplayList((int)$_GET["p"])) {
	echo '<p><a href="./edit.php">Добави нова медия</a></p>
	<p><b>Няма въведена медия</b></p>';
}

HtmlEnd() ;

?>