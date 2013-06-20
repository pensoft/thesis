<?php

$docroot = getenv("DOCUMENT_ROOT");
require_once($docroot . '/lib/static.php');

$historypagetype = HISTORY_CLEAR;
$gCn = Con();

HtmlStart();

if ($_GET['ordstr']) {
	$objarr[] = 0;
	$objids = explode(',', $_GET['ordstr']);

	foreach($objids as $v) {
		if ($v != '' && $_GET['rubr' . $v]) {
			$objarr[] = (int)$_GET['rubr' . $v];
		}
	}
	
	$objarr = implode(', ', $objarr);
	$gCn->Execute('SELECT * FROM RubrikiRearange(' . (int)$_GET['rizdanie'] . ', array[' . $objarr . '])');
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


// Putekata
if ($_GET['rootpos']) {
	$pathsql = 'SELECT *, ' . getsqlang('name') . ' 
		FROM rubr r
		WHERE 
			r.rootnode = (SELECT rr.rootnode FROM rubr rr WHERE rr.sid = 1 AND rr.pos = \'' . q($_GET['rootpos']) . '\')
			AND (char_length(pos) < char_length(\'' . q($_GET['rootpos']) . '\') OR pos = \'' . q($_GET['rootpos']) . '\')
		ORDER BY pos ASC';
		
	$gCn->Execute($pathsql);
	$gCn->MoveFirst();
	echo '<div style="font-weight: bold; padding: 0 1%;width: 98%;"><a href="?rizdanie=1">Начало</a>';
	while( ! $gCn->EoF() ) {
		echo ' &raquo; <a href="?rizdanie=1&rootpos=' . $gCn->mRs['pos'] . '">' . $gCn->mRs['name'] . '</a>';
		$gCn->MoveNext();
	}
	echo '</div>';
}

echo '
	<form name="navFrm" action="./index.php" method="GET">
	<input type="hidden" name="rizdanie" value="1" />
	<input type="hidden" name="rootpos" value="' . $_GET['rootpos'] . '" />
	<input type="hidden" name="ordstr" value="" />
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
					<th class="gridtools" colspan="6">
						<a href="./edit.php">' . getstr('admin.rubr.addRubr') . '</a>
						' . getstr('admin.rubr.antetka') . '
					</th>
				</tr>
				<tr>
					<th myhiddenname="ordstr">&nbsp;</th>
					<th>' . getstr('admin.rubr.idCol') . '</th>
					<th>' . getstr('admin.rubr.nameCol') . '</th>
					<th>' . getstr('admin.rubr.subrubrCol') . '</th>
					<th>' . getstr('admin.rubr.stateCol') . '</th>
					<th>&nbsp;</th>
				</tr>';

$sqlWhere = ' AND r1.id = r1.rootnode';
if ($_GET['rootpos']) $sqlWhere = ' AND r1.pos LIKE \'' . q($_GET['rootpos']) . '__\'';

$sql = 'SELECT DISTINCT r1.id, r1.sid, r1.state, r1.rootnode, r1.pos, '.getsqlang('r1.name').', (case WHEN r2.id IS NOT NULL THEN 1 ELSE null end) as subrubr 
	FROM rubr r1
	LEFT JOIN rubr r2 ON r1.rootnode = r2.rootnode AND r2.pos LIKE r1.pos || \'__\'
	WHERE r1.sid = 1 ' . $sqlWhere . ' ORDER BY r1.pos';

$gCn->Execute($sql);
$gCn->MoveFirst();
$counter = 1;
if ($_GET['rootpos'] && !$gCn->mRecordCount) {
	header('Location: ' . backhref(1));
} elseif (!$gCn->mRecordCount) {
	echo '<tr><td colspan="6" style="padding: 10px;"><p align="center"><b>' . getstr('admin.rubr.noData') . '</b></p></td></tr>';
} else {
	while( ! $gCn->EoF() ) {
		
		$subrubr = 'Няма';
		if ($gCn->mRs['subrubr']) $subrubr = '<a href="?rizdanie=1&rootpos=' . $gCn->mRs['pos'] . '&tAction=selectr">' . getstr('admin.rubr.getSubRubrs') . '</a>';
		
		echo '
			<tr bgcolor="#' . ( $counter % 2 == 1 ? 'ffffff' : '96b9df' ) . '" id="r' . $gCn->mRs['id'] . '">
				<td style="cursor: n-resize;" onmouseup="return enddrag(event);" onmousedown="return startdrag(event);">
					<input type="hidden" value="' . $counter . '" id="rowid' . $counter . '" name="rowid' . $counter . '" />#' . $counter . '
					<input type="hidden" id="rubr' . $counter . '" name="rubr' . $counter . '" value="' . $gCn->mRs['id'] . '"/>
				</td>
				<td nowrap>' . $gCn->mRs['id'] . '</td>
				<td nowrap>' . $gCn->mRs['name'] . '</td>
				<td nowrap>' . $subrubr . '</td>
				<td nowrap>' . $gCn->mRs['state'] . '</td>
				<td align="right">
					<a href="edit.php?tAction=show&id=' . $gCn->mRs['id'] . '">
						<img src="/img/gridedit.gif" border="0" title="' . getstr('admin.editButton') . '" alt="' . getstr('admin.editButton') . '" />
					</a>
					<a href="edit.php?tAction=delete&id=' . $gCn->mRs['id'] . '" onclick="return confirm(\'' . getstr('admin.rubr.rubrConfirmDel') . '\')">
						<img src="/img/trash2.gif" border="0" title="' . getstr('admin.deleteButton') . '" alt="' . getstr('admin.deleteButton') . '" />
					</a>
				</td>
			</tr>';
		$gCn->MoveNext();
		$counter++;
	}
}

echo '
			<tr><td colspan="6" align="right">' . backonelevel(1) . ' <input type="button" value="' . getstr('admin.saveButton') . '" class="frmbutton" onclick="recalcorder(); document.navFrm.submit()"></td></tr>
			</table>
		</div>
		</div>
		</div>
		</div>
	</div>
	</div>
	</div>
	</div>
	</form>';

function backonelevel($sid) {
	if ($_GET['rootpos']) {
		$href = backhref($sid);
		return '<input type="button" value="' . getstr('admin.backButton') . '" class="frmbutton" onclick="javascript: window.location.href = \'' . $href . '\';">';
	}
	return '';
}

function backhref($sid) {
	if ($_GET['rootpos']) {
		$np = $_GET['rootpos'];
		$np = substr($np, 0, strlen($np)-2);
		return '?rizdanie=' . $sid . ($np ? '&rootpos=' . $np : '') . '&tAction=selectr';
	}
	return '';
}

HtmlEnd();

?>