<?php
$docroot = getenv("DOCUMENT_ROOT");
require_once($docroot . '/lib/static.php');

$historypagetype = HISTORY_CLEAR;


HtmlStart();
$gCn = Con();

if ($_GET['ordstr']) {
	$objarr[] = 0;
	$objids = explode(',', $_GET['ordstr']);

	foreach($objids as $v) {
		if ($v != '' && (int)$_GET['rubr' . $v]) {
			$objarr[] = (int)$_GET['rubr' . $v];
		}
	}
	
	$objarr = implode(', ', $objarr);
	$lSqlArrange = 'SELECT * FROM SecSitesRearrange(' . (int)$_GET['level'] . ', array[' . $objarr . '])';
	$gCn->Execute($lSqlArrange);
	//~ echo $lSqlArrange;
}

echo '
<script>

function cancelSelect(){return false;}

var startrow;
var startd;

function startdrag(e) {
	
	if (!e.target) {
		// event.cancelBubble = true;
		startd = window.event.srcElement;
		window.event.srcElement.onselectstart = cancelSelect;
	} else {
		startd = e.target;
	}
	
	return false;
}

function enddrag(e) {
	if (!e.target) {
		targtd = window.event.srcElement;
	} else {
		targtd = e.target;
	}
	
	if (!startd) return;
	
	if (targtd.parentNode.rowIndex > startd.parentNode.rowIndex) {
		targtd.parentNode.parentNode.insertBefore(startd.parentNode, targtd.parentNode.nextSibling);
	} else {
		targtd.parentNode.parentNode.insertBefore(startd.parentNode, targtd.parentNode);
	}
	// recalcorder();
	startd.parentNode.style.backgroundColor = "#c0c0c0";
	
	//~ alert(ordstr);
	
	startd = null;		
}

function recalcorder() {
	var ordstr = "";
	var stylestr = "";
	
	var ordtbl = document.getElementById("ordtable");
	
	for(x=0; x<ordtbl.rows[1].cells.length; x++) {
		hidname = ordtbl.rows[1].cells[x].getAttribute("myhiddenname")
		if (hidname) {
			document.forms["navFrm"].elements[hidname].value = "";
		}
	}
	
	for(i = 2; i < (ordtbl.rows.length - 1); i++) {
		for(x=0; x<ordtbl.rows[i].cells.length; x++) {
			hidname = ordtbl.rows[1].cells[x].getAttribute("myhiddenname");
			if (hidname && ordtbl.rows[i].cells[x].getElementsByTagName(\'INPUT\')[0] && ordtbl.rows[i].cells[x].getElementsByTagName(\'INPUT\')[0].value) {
				
				document.forms["navFrm"].elements[hidname].value += ordtbl.rows[i].cells[x].getElementsByTagName(\'INPUT\')[0].value + ",";
			}
		}
	}
}

</script>';

$fld = array(
	'level' => array (
		'VType' => 'int' ,
		'CType' => 'select' ,
		'SrcValues' =>	'
		SELECT null as id, \'Основни + *\' as name UNION
		SELECT DISTINCT s.id, s.name FROM secsites s
			JOIN secsites s1 ON s1.url ILIKE (s.url || \'%\') AND s1.cnt = s.cnt + 1
		WHERE s.url NOT LIKE \'*%\' AND s.type = 1',
		'AddTags' => array(
			'class' => 'coolinp',
		),
		'DefValue' => (int) $_GET['rootpos'],
		'DisplayName' => 'Ниво',
	),
	
	'show' => array(
		'CType' => 'action',
		'DisplayName' => getstr('admin.filterButton'),
		'SQL' => '',
		'ActionMask' => ACTION_CHECK | ACTION_SHOW,
		'AddTags' => array(
			'class' => 'frmbutton',
		),
	),
);


$frm = '
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
					<col width="33%"/>
					<col width="33%"/>
				</colgroup>
				<tr>
					<th colspan="2">' . getstr('admin.issues.filter') . '</th>
				</tr>
				<tr>
					<td>{*level}<br/>{level}</td>
					<td></td>
				</tr>
				<tr>
					<td colspan="2" align="right">{show}</td>
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

$kfor = new kfor($fld, $frm, 'GET');
echo $kfor->Display();

if ($kfor->lCurAction == 'show') {
	if ((int)($kfor->lFieldArr['level']['CurValue'])){
		$lJoin = ' JOIN secsites s1 ON ((s.url ILIKE (s1.url || \'%\') AND s.cnt = 1 + s1.cnt AND s.type =1 ) OR (s.url = s1.url AND s.type = 2)) AND s1.id = ' . (int)$kfor->lFieldArr['level']['CurValue'] ;
	}else{
		$lWhere = ' WHERE s.cnt <= 2 AND s.type = 1';
	}
}else{
	$lWhere = ' WHERE s.cnt <= 2 AND s.type = 1';
}



echo '<form name="navFrm" action="./index.php" method="GET">
	<input type="hidden" name="level" value="' . $_GET['level'] . '" />
	<input type="hidden" name="ordstr" value="" />
	<input type="hidden" name="tAction" value="show" />
	<div class="t">
	<div class="b">
	<div class="l">
	<div class="r">
		<div class="bl">
		<div class="br">
		<div class="tl">
		<div class="tr">
			<table cellspacing="0" cellpadding="5" border="0" class="gridtable" id="ordtable">
				<tr>
					<th class="gridtools" colspan="7">
						<a href="./edit.php">Добави нов сайт</a>
						Сайтове
					</th>
				</tr>
				<tr>
					<th myhiddenname="ordstr">&nbsp;</th>
					<th>ID</th>
					<th>Сайт</th>
					<th>Поддиректория</th>
					<th>Поднива</th>
					<th>Тип</th>
					<th>&nbsp;</th>
				</tr>
';

$lSql = '
	SELECT DISTINCT s.id, s.url, s.name, s.cnt, s.ord, s.type, max(CASE WHEN s2.id IS NULL THEN 0 ELSE 1 END) as sublevel FROM secsites s ' . $lJoin . '
	LEFT JOIN secsites s2 ON ((s2.url ILIKE (s.url || \'%\') AND s2.cnt = 1 + s.cnt AND s2.type =1 ) OR (s2.url = s.url AND s2.type = 2)) AND s2.id <> s.id AND s.type = 1
' . $lWhere . ' GROUP BY s.id, s.url, s.name, s.cnt, s.ord, s.type ORDER BY s.ord';
//~ echo $lSql;
$gCn = Con();
$gCn->Execute($lSql);
$gCn->MoveFirst();
$lCounter = 1;

if (!$gCn->mRecordCount) {
	echo '<tr><td colspan="8"><p align="center"><b>Няма въведени сайтове</b></p></td></tr>';
} else {
	while( ! $gCn->EoF() ) {
		
		$subrubr = 'Няма';
		if ($gCn->mRs['sublevel']) $subrubr = '<a href="?level=' . $gCn->mRs['id'] . '&tAction=show">Виж</a>';
		
		echo '
			<tr bgcolor="#' . ( $lCounter % 2 == 1 ? 'ffffff' : '96b9df' ) . '" id="r' . $gCn->mRs['id'] . '">
				<td style="cursor: n-resize;" onmouseup="return enddrag(event);" onmousedown="return startdrag(event);">
					<input type="hidden" value="' . $lCounter . '" id="rowid' . $lCounter . '" name="rowid' . $lCounter . '" />#' . $lCounter . '
					<input type="hidden" id="rubr' . $lCounter . '" name="rubr' . $lCounter . '" value="' . $gCn->mRs['id'] . '"/>
				</td>
				<td nowrap>' . $gCn->mRs['id'] . '</td>
				<td nowrap>' . $gCn->mRs['name'] . '</td>
				<td nowrap>' . $gCn->mRs['url'] . '</td>
				<td nowrap>' . $subrubr . '</td>
				<td nowrap>' . getLinkType($gCn->mRs['type']) . '</td>
				<td align="right"><a href="./edit.php?id=' . $gCn->mRs['id'] . '&tAction=show"><img src="/img/edit.gif" border="0" alt="Редактирай" title="Редактирай" /></a></td>
			</tr>';
		$gCn->MoveNext();
		$lCounter++;
	}
}

echo '
			<tr><td colspan="7" align="right"><input type="button" value="' . getstr('admin.saveButton') . '" class="frmbutton" onclick="recalcorder(); document.navFrm.submit()"></td></tr>
			</table>
		</div>
		</div>
		</div>
		</div>
	</div>
	</div>
	</div>
	</div>
	</form>
';


HtmlEnd();

function getLinkType($pType){
	if( (int) $pType == 1)
		return 'Линк';
	return 'Разделител';
}

?>