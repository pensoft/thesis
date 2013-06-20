<?php
$docroot = getenv('DOCUMENT_ROOT');
 require_once($docroot . '/lib/static.php');


global $user, $gUrl;
UserRedir($user);
ProccessHistory();
$gArticleId = (int) $_REQUEST['article_id'];


HtmlStart(1);


$t = '<tr>
		<td valign="top">{name}</td>
		<td valign="top">{data}</td>				
	</tr>
';

$gFArr = array(	
	2 => array('caption' => getstr('admin.xml_sync_details.colName'), 'def', 'deforder' => 'asc'), 
	1 => array('caption' => getstr('admin.xml_sync_details.colData'), 'deforder' => 'asc'), 	
);

$lTableHeader = '
	<a name="details"></a>
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
					<th class="gridtools" colspan="4">						
						' . getstr('admin.xml_sync_details.antetka') . '
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
$l->SetOrderParams((int)$_GET['ordby'], (int)$_GET['ordd']);
$l->SetAntet($gFArr);
$l->SetQuery('SELECT n.data, a.name
	FROM xml_sync_details a
	JOIN xml_sync n ON n.xml_sync_details_id = a.id
	WHERE n.article_id = ' . $gArticleId
);

if (!$l->DisplayList($page)) {
	echo $lTableHeader . '<tr><td colspan="4"><p align="center"><b>' . getstr('admin.xml_sync_details.noData') . '</b></p></td></tr>' . $lTableFooter;
}

HtmlEnd(1);

?>