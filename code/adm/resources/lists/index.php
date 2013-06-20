<?php
$docroot = getenv('DOCUMENT_ROOT');
require_once($docroot . '/lib/static.php');

$gLastPost = 20;

function build_styleopts($sel = false) {
	if (!$sel) $sel = 0;
	$html = '
		<option ' . ($sel == 0 ? 'selected ': '') . 'value="0">Без стил</option>
		<option ' . ($sel == 1 ? 'selected ': '') . 'value="1">Голям</option>
		<option ' . ($sel == 2 ? 'selected ': '') . 'value="2">Нормален</option>
	';
	return $html;
}

function make_link($objtype, $sid, $posid, $lang, $tAction) {
	$link = '';
	if ($objtype == 1) $link = './sel.php?&pos=' . $posid . '&obj=story&language=' . $lang . '&tAction=' . $tAction;
	if ($objtype == 2) $link = './sel.php?&pos=' . $posid . '&obj=rubr&language=' . $lang . '';
	
	return $link;
}

//~ var_dump($_POST);

HtmlStart();
$t = array(
	'sid' => array (
		'VType' => 'int' ,
		'CType' => 'hidden' ,		
	),
	
	'listnameid' => array (
		'VType' => 'int' ,
		'CType' => 'select' ,
		'SrcValues' => 'SELECT null as id, \'--\' as name, 0 as t
			UNION ALL SELECT listnameid as id, name, 1 FROM listnames 
			WHERE sid = 1 
			ORDER BY 3, 2',
		'DisplayName' => getstr('admin.lists.nameFld'),
		'AddTags' => array(
			'class' => 'coolinp',
		),	
	),

	'language' => array(
		'VType' => 'string',
		'CType' => 'select',
		'DisplayName' => getstr('admin.lists.langFld'),
		'SrcValues' => 'SELECT null AS id, \'--\' as name
				UNION
			SELECT code as id, name FROM languages ORDER BY name',
		'AddTags' => array(
			'class' => 'coolinp',
		),
	),

	'objtype' => array (
		'VType' => 'int' ,
		'CType' => 'hidden' ,	
	),
	
	'show' => array(
		'CType' => 'action',
		'DisplayName' => '',
		'SQL' => '',
		'AddTags' => array(
			'class' => 'frmbutton',
		),		
		'DisplayName' => getstr('admin.showButton'),
		'SQL' => 'SELECT * FROM addList(0, {listnameid}, null, null, null)/*{language}*/',
		'ActionMask' => ACTION_CHECK | ACTION_EXEC | ACTION_FETCH | ACTION_SHOW,
	),
	
	'cancel' => array(
		'CType' => 'action',
		'DisplayName' => getstr('admin.backButton'),
		'ActionMask' => ACTION_REDIRECT,
		'RedirUrl' => './index.php',
		'AddTags' => array(
			'class' => 'frmbutton',
		),
	),
);

$kfor = new kfor($t, NULL, 'POST');
$kfor->debug = false;
$kfor->ExecAction();

$kfor->lFormHtml = '
	<div class="t">
	<div class="b">
	<div class="l">
	<div class="r">
		<div class="bl">
		<div class="br">
		<div class="tl">
		<div class="tr">
			<table cellspacing="0" cellpadding="5" border="0" class="formtable">
			<tr>
				<th class="formtools" colspan="2">
					<a href="./add.php">' . getstr('admin.lists.addList') . '</a>
				</th>
			</tr>
			<tr><th colspan="2">' . getstr('admin.lists.antetka') . '</th></tr>
			<tr>
				<td><b>{*listnameid}:</b></td>
				<td>{listnameid}{sid}{objtype}</td>
			</tr>
			<tr><td><b>{*language}:</b></td><td>{language}</td></tr>
			<tr>
				<td colspan="2" align="right">{show} {cancel}</td>
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

$gCn = Con() ;

if ($_POST['ordstr']) {
	$objarr[] = 0;
	$stylearr[] = 0;
	$objids = explode(',', $_POST['ordstr']);

	foreach($objids as $v) {
		if ($v != '' && $_POST['guid' . $v]) {
			$objarr[] = $_POST['guid' . $v];
			$stylearr[] = $_POST['style' . $v];
		}
	}
	
	$objarr = implode(', ', $objarr);
	$stylearr = implode(', ', $stylearr);
	if($_POST['objtype'] == 2) {
		$lMainLang = 'bg';
	} else {
		$lMainLang = $kfor->lFieldArr['language']['CurValue'];
	}
	$gCn->Execute( 'SELECT * FROM spObjOrder(' . $kfor->lFieldArr['listnameid']['CurValue'] . ', array[' . $objarr . '], array[' . $stylearr . '], \'' . $lMainLang . '\')' );
	clearcacheditems2('stories', $kfor->lFieldArr['sid']['CurValue']);
}

echo $kfor->Display();

if ($kfor->lCurAction == 'show'	&& $kfor->lErrorCount == 0) {

	echo '
	<script>
	
	var gLastPost = ' . $gLastPost . ' ;
	function delStory( pPos ) {
		document.getElementById( "guid" + pPos ).value = "" ;
		document.getElementById( "title" + pPos ).value = "" ;
	}
	
	function cancelSelect(){return false;}
	
	var startrow;
	var startd;
	
	function startdrag(e) {
		
		if (!e.target) {
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
		startd.parentNode.style.backgroundColor = "#EED8AE";
		
		startd = null;
	}
	
	function recalcorder() {
		var ordstr = "";
		var stylestr = "";
		
		var ordtbl = document.getElementById("ordtable");
		
		for(x=0; x<ordtbl.rows[1].cells.length; x++) {
			hidname = ordtbl.rows[1].cells[x].getAttribute("myhiddenname")
			if (hidname) {
				document.forms["storyFrm"].elements[hidname].value = "";
			}
		}
		
		for(i = 2; i < ordtbl.rows.length; i++) {
			for(x=0; x<ordtbl.rows[i].cells.length; x++) {
				hidname = ordtbl.rows[1].cells[x].getAttribute("myhiddenname");
				if (hidname && ordtbl.rows[i].cells[x].firstChild && ordtbl.rows[i].cells[x].firstChild.value) {
					document.forms["storyFrm"].elements[hidname].value += ordtbl.rows[i].cells[x].firstChild.value + ",";
				}
			}
		}
	}
	
	function editstory(t, type) {
		if(type == 1 && t.value) {
			document.location = "/resources/stories/edit.php?tAction=showedit&guid=" + t.value;
		}
	}
	
	</script>' ;
	//~ var_dump($kfor->lFieldArr['language']['CurValue']);
	echo '
	<form name="storyFrm" action="./index.php" method="POST">
	<input type="hidden" name="objtype" value="' . $kfor->lFieldArr['objtype']['CurValue'] . '" />
	<input type="hidden" name="listnameid" value="' . $kfor->lFieldArr['listnameid']['CurValue'] . '" />
	<input type="hidden" name="sid" value="' . $kfor->lFieldArr['sid']['CurValue'] . '" />
	<input type="hidden" name="language" value="' . $kfor->lFieldArr['language']['CurValue'] . '" />
	<input type="hidden" name="tAction" value="show" />
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
				<th class="gridtools" colspan="5">
					' . getstr('admin.lists.antetka') . '
				</th>
			</tr>
			<tr>
				<th myhiddenname="ordstr">&nbsp;</th>
				<th>' . getstr('admin.lists.idCol') . '</th>
				<th>' . getstr('admin.lists.titleCol') . '</th>
				<th>' . getstr('admin.lists.styleCol') . '</th>
				<th>&nbsp;</th>
			</tr>' ;
	
	if ($kfor->lFieldArr['objtype']['CurValue'] == 1) {
			if ($kfor->lFieldArr['language']['CurValue']) {
				$lJoin = 'JOIN languages ln ON ln.code = s.lang';
				$lWarr = 'AND s.lang = \'' . q($kfor->lFieldArr['language']['CurValue']) .'\'';
			}
		$sql = 'SELECT s.guid as objid, s.title, s.pubdate, ld.styletype
		FROM listnames l 
			JOIN listdets ld using(listnameid)
			JOIN stories s ON ld.objid = s.guid 
			' . $lJoin . '
		WHERE
			ld.listnameid = ' . $kfor->lFieldArr['listnameid']['CurValue'] . '
			AND l.sid = ' . $kfor->lFieldArr['sid']['CurValue'] . '
			' . $lWarr . '
		ORDER BY
			ld.posid';	
			
	} elseif ($kfor->lFieldArr['objtype']['CurValue'] == 2) {
		$sql = 'SELECT r.id as objid, ' . getsqlang('r.name') . ' as title, null as pubdate, ld.styletype
		FROM listnames l 
			JOIN listdets ld using(listnameid)
			JOIN rubr r ON ld.objid = r.id AND l.sid = r.sid
		WHERE
			ld.listnameid = ' . $kfor->lFieldArr['listnameid']['CurValue'] . '
			AND l.sid = ' . $kfor->lFieldArr['sid']['CurValue'] . '
		ORDER BY
			ld.posid';
	} elseif ($kfor->lFieldArr['objtype']['CurValue'] == 3) { 
		$sql = 'SELECT pr.provider_id as objid, pr.name as title, null as pubdate, ld.styletype
		FROM listnames l 
			JOIN listdets ld using(listnameid)
			JOIN provider pr ON ld.objid = pr.provider_id
		WHERE
			ld.listnameid = ' . $kfor->lFieldArr['listnameid']['CurValue'] . '
			AND l.sid = ' . $kfor->lFieldArr['sid']['CurValue'] . '
		ORDER BY
			ld.posid';
	} elseif ($kfor->lFieldArr['objtype']['CurValue'] == 4) { 
		$sql = 'SELECT pr.product_id as objid, pr.name as title, null as pubdate, ld.styletype
		FROM listnames l 
			JOIN listdets ld using(listnameid)
			JOIN product pr ON ld.objid = pr.product_id
		WHERE
			ld.listnameid = ' . $kfor->lFieldArr['listnameid']['CurValue'] . '
			AND l.sid = ' . $kfor->lFieldArr['sid']['CurValue'] . '
		ORDER BY
			ld.posid';
	}
	$gCn->Execute($sql);
	$gCn->MoveFirst();

	$counter = 1 ;
	while( ! $gCn->EoF() ) {
		echo '
			<tr bgcolor="#' . ( $counter % 2 == 1 ? 'ffffff' : '96b9df' ) . '" id="g' . $gCn->mRs["objid"] . '">
				<td style="cursor: n-resize;" align="right" onmouseup="return enddrag(event);" onmousedown="return startdrag(event);"><input type="hidden" value="' . $counter . '" id="rowid' . $counter . '" name="rowid' . $counter . '" />' . $counter . '</td>
				<td nowrap><input readonly onclick="return editstory(this, ' . $kfor->lFieldArr['objtype']['CurValue'] . ');" type="text" class="coolinp" style="width: 60px;cursor: pointer; cursor: hand;" type="text" id="guid' . $counter . '" name="guid' . $counter . '" value="' . $gCn->mRs["objid"] . '"/>&nbsp;</td>
				<td style="width: 100%;"><input readonly class="coolinp" style="width: 100%;" type="text" id="title' . $counter . '" name="title' . $counter . '" value="' . h($gCn->mRs["title"]) . '"/></td>
				<td>
					<select class="coolinp" style="width: 100px;" id="style' . $counter . '" name="style' . $counter . '" />
					' . build_styleopts($gCn->mRs['styletype']) . '
					</select>
				</td>
				<td nowrap>
				<a href="javascript: delStory(' . $counter . ');">
					<img src="/img/trash2.gif" alt="' . getstr('admin.deleteButton') . '" title="' . getstr('admin.deleteButton') . '" border="0" />
				</a>&nbsp;
				<a href="javascript:openw(\'' . make_link($kfor->lFieldArr['objtype']['CurValue'], $kfor->lFieldArr['sid']['CurValue'], $counter, $kfor->lFieldArr['language']['CurValue'], 'show') . '\', \'aa\', \'location=no,menubar=yes,width=855,height=600,scrollbars=yes,resizable=yes,top=0,left=0\')">
					' . getstr('admin.lists.choose') . '
				</a></td>
			</tr>' ;
		$gCn->MoveNext() ;
		$counter++ ;
	}

	while ( $counter <= $gLastPost ) {
		echo '
			<tr bgcolor="#' . ( $counter % 2 == 1 ? 'ffffff' : '96b9df' ) . '">
				<td align="right" onmouseup="return enddrag(event);" onmousedown="return startdrag(event);"><input type="hidden" value="' . $counter . '" id="rowid' . $counter . '" name="rowid' . $counter . '" />' . $counter . '</td>
				<td nowrap><input type="text" readonly class="coolinp" style="width: 60px;cursor: pointer; cursor: hand;" type="text" id="guid' . $counter . '" name="guid' . $counter . '" value=""/>&nbsp;</td>
				<td style="width: 100%;"><input readonly class="coolinp" type="text" id="title' . $counter . '" name="title' . $counter . '" value=""/></td>			
				<td>
					<select class="coolinp" style="width: 100px;" id="style' . $counter . '" name="style' . $counter . '" />
					' . build_styleopts() . '
					</select>
				</td>
				<td align="right">
				<a href="javascript:openw(\'' . make_link($kfor->lFieldArr['objtype']['CurValue'], $kfor->lFieldArr['sid']['CurValue'], $counter, $kfor->lFieldArr['language']['CurValue'], 'show') . '\', \'aa\', \'location=no,menubar=yes,width=855,height=600,scrollbars=yes,resizable=yes,top=0,left=0\')">
				' . getstr('admin.lists.choose') . '
				</a></td>
			</tr>' ;
		$counter++ ;
	}

	echo ($kfor->lCurAction == 'show' ? '<tr><td colspan="5" align="right">
			<input type="button" value="' . getstr('admin.saveButton') . '" class="frmbutton" onclick="recalcorder(); document.storyFrm.submit()">
		</td></tr>' : '') . '
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
}

HtmlEnd();

?>