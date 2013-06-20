<?php
error_reporting((int)ERROR_REPORTING);
$docroot = getenv("DOCUMENT_ROOT");
require_once($docroot . '/lib/static.php');
$historypagetype = HISTORY_ACTIVE;
global $gEcmsLibRequest;
HtmlStart();
$gKforFlds = array(	
	'id' => array(
		'VType' => 'int',
		'CType' => 'hidden',
		'DisplayName' => '',
		'AllowNulls' => true,
	),
	
	'replace_text_content' => array(
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
		'DisplayName' => getstr('admin.articles.colTitle'),
	),
	
	
	'createuid' => array(
		'VType' => 'int',
		'CType' => 'select',
		'SrcValues' => 'SELECT id, name FROM usr',
		'DisplayName' => getstr('admin.articles.colCreator'),
	),
	
	'xml_sync_template_id' => array(
		'VType' => 'int',
		'CType' => 'select',
		'SrcValues' => 'SELECT null as id, \'----\' as name UNION SELECT id, name FROM xml_sync_templates ORDER BY id DESC',
		'AddTags' => array(
			'class' => 'coolinp',
		),
		'AllowNulls' => true,
		'DisplayName' => getstr('admin.articles.colSyncTemplateId'),
	),
	
	'lastmod' => array(
		'VType' => 'date',
		'CType' => 'text',		
		'DisplayName' => getstr('admin.articles.colLastMod'),
	),
	
	'author' => array(
		'VType' => 'string',
		'CType' => 'text',
		'AddTags' => array(
			'class' => 'coolinp',
		),
		'DisplayName' => getstr('admin.articles.colAuthor'),
		'AllowNulls' => true
	),
	
	'journal_id' => array(
		'VType' => 'int',
		'CType' => 'select',
		'AddTags' => array(
			'class' => 'coolinp',
		),
		'SrcValues' => 'SELECT null as id, \'---\' as name UNION SELECT id, name FROM journals',
		'DisplayName' => getstr('admin.articles.colJournalId'),
		'AllowNulls' => false
	),
	
	'overwrite_journal_info' => array(
		'VType' => 'int',
		'CType' => 'checkbox',
		'TransType' => MANY_TO_BIT_ONE_BOX,
		'AddTags' => array(
			'class' => 'coolinp',			
		),
		'SrcValues' => array(
			0 => getstr('admin.no'),
			1 => getstr('admin.yes'),
		),
		'DisplayName' => getstr('admin.articles.colOverwriteJournalInfo'),
		'AllowNulls' => true
	),
	
	'content' => array(
		'VType' => 'string',
		'CType' => 'textarea',
		'AddTags' => array(
			'class' => 'coolinp contentTextArea',
		),
		'DisplayName' => getstr('admin.articles.colContent'),
		'AllowNulls' => true
	),
	
	'meta_identifier' => array(
		'VType' => 'string',
		'CType' => 'text',
		'AddTags' => array(
			'class' => 'coolinp',
		),
		'DisplayName' => getstr('admin.articles.colMeta_identifier'),
		'AllowNulls' => true
	),
	
	'meta_identifier_type' => array(
		'VType' => 'string',
		'CType' => 'text',
		'AddTags' => array(
			'class' => 'coolinp',
		),
		'DisplayName' => getstr('admin.articles.colMeta_identifier_type'),
		'AllowNulls' => true
	),
	
	'meta_title' => array(
		'VType' => 'string',
		'CType' => 'text',
		'AddTags' => array(
			'class' => 'coolinp',
		),
		'DisplayName' => getstr('admin.articles.colMeta_title'),
		'AllowNulls' => true
	),
	
	'meta_authors' => array(
		'VType' => 'string',
		'CType' => 'text',
		'AddTags' => array(
			'class' => 'coolinp',
		),
		'DisplayName' => getstr('admin.articles.colMeta_authors'),
		'AllowNulls' => true
	),
	
	'meta_url_of_pdf' => array(
		'VType' => 'string',
		'CType' => 'text',
		'AddTags' => array(
			'class' => 'coolinp',
		),
		'DisplayName' => getstr('admin.articles.colMeta_url_of_pdf'),
		'AllowNulls' => true
	),
	
	'meta_book_title' => array(
		'VType' => 'string',
		'CType' => 'text',
		'AddTags' => array(
			'class' => 'coolinp',
			'id' => 'meta_book_title',
		),
		'DisplayName' => getstr('admin.articles.colMeta_book_title'),
		'AllowNulls' => true
	),
	
	'meta_journal_name' => array(
		'VType' => 'string',
		'CType' => 'text',
		'AddTags' => array(
			'class' => 'coolinp',
			'id' => 'meta_journal_name',
		),
		'DisplayName' => getstr('admin.articles.colMeta_journal_name'),
		'AllowNulls' => true
	),
	
	'meta_journal_volume_number' => array(
		'VType' => 'string',
		'CType' => 'text',
		'AddTags' => array(
			'class' => 'coolinp',
			'id' => 'meta_journal_volume_number',
		),
		'DisplayName' => getstr('admin.articles.colMeta_journal_volume_number'),
		'AllowNulls' => true
	),
	
	'meta_publisher_name' => array(
		'VType' => 'string',
		'CType' => 'text',
		'AddTags' => array(
			'class' => 'coolinp',
			'id' => 'meta_publisher_name',
		),
		'DisplayName' => getstr('admin.articles.colMeta_publisher_name'),
		'AllowNulls' => true
	),
	
	'meta_publisher_location' => array(
		'VType' => 'string',
		'CType' => 'text',
		'AddTags' => array(
			'class' => 'coolinp',
			'id' => 'meta_publisher_location',
		),
		'DisplayName' => getstr('admin.articles.colMeta_publisher_location'),
		'AllowNulls' => true
	),
	
	'meta_pub_year' => array(
		'VType' => 'int',
		'CType' => 'text',
		'AddTags' => array(
			'class' => 'coolinp',
		),
		'DisplayName' => getstr('admin.articles.colMeta_pub_year'),
		'AllowNulls' => true
	),
	
	'meta_part_of_host_publication' => array(
		'VType' => 'int',
		'CType' => 'checkbox',
		'TransType' => MANY_TO_BIT_ONE_BOX,
		'AddTags' => array(
			'class' => 'coolinp',
			'id' => 'meta_part_of_host_publication',			
		),
		'SrcValues' => array(
			0 => getstr('admin.no'),
			1 => getstr('admin.yes'),
		),
		'DisplayName' => getstr('admin.articles.colMeta_part_of_host_publication'),
		'AllowNulls' => true
	),
	
	'meta_journal_type' => array(
		'VType' => 'int',
		'CType' => 'select',
		'AddTags' => array(
			'class' => 'coolinp',
			'id' => 'meta_journal_type',			
		),
		'SrcValues' => array(
			0 => '---',
			1 => getstr('admin.articles.meta_journal_type_journal'),
			2 => getstr('admin.articles.meta_journal_type_book'),
		),
		'DisplayName' => getstr('admin.articles.colMeta_journal_type'),
		'AllowNulls' => true
	),
	
	'meta_start_page' => array(
		'VType' => 'int',
		'CType' => 'text',
		'AddTags' => array(
			'class' => 'coolinp',
			'id' => 'meta_start_page',
		),
		'DisplayName' => getstr('admin.articles.colMeta_start_page'),
		'AllowNulls' => true
	),
	
	'meta_end_page' => array(
		'VType' => 'int',
		'CType' => 'text',
		'AddTags' => array(
			'class' => 'coolinp',
			'id' => 'meta_end_page',
		),
		'DisplayName' => getstr('admin.articles.colMeta_end_page'),
		'AllowNulls' => true
	),
	
	'export_type' => array(
		'VType' => 'int',
		'CType' => 'select',
		'AddTags' => array(
			'class' => 'coolinp',
			'id' => 'meta_journal_type',			
		),
		'SrcValues' => array(			
			(int)XML_EXPORT_TYPE => getstr('admin.articles.xml_export_type'),
			(int)HTML_EXPORT_TYPE => getstr('admin.articles.html_export_type'),
			(int)EOL_XML_EXPORT_TYPE => getstr('admin.articles.eol_xml_export_type'),
			(int)HTML_OLD_EXPORT_TYPE => getstr('admin.articles.html_old_export_type'),
			(int)HTML_NEW_EXPORT_TYPE => getstr('admin.articles.html_new_export_type'),
			(int)META_EXPORT_TYPE => getstr('admin.articles.meta_export_type'),
			(int)MEDIAWIKI_EXPORT_TYPE => getstr('admin.articles.mediawiki_export_type'),
		),
		'DisplayName' => getstr('admin.articles.colExport_type'),
		'AllowNulls' => true
	),
	
	'showedit' => array(
		'CType' => 'action',
		'Hidden' => true,
		'DisplayName' => '',
		'SQL' => 'SELECT * FROM spArticlesData(0, {id}, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null)',
		'ActionMask' => ACTION_CHECK | ACTION_EXEC | ACTION_FETCH | ACTION_SHOW,
	),
	
	'save' => array(
		'CType' => 'action',
		'DisplayName' => getstr('admin.saveButton'),
		'SQL' => 'SELECT * FROM spArticlesData(1, {id}, ' . (int) $user->id . ', {title}, {author}, {content}, null,{meta_identifier},{meta_identifier_type},{meta_pub_year},{meta_title},{meta_authors},{meta_url_of_pdf},{meta_part_of_host_publication},{meta_journal_type},{meta_start_page},{meta_end_page},{meta_book_title},{meta_journal_name},{meta_journal_volume_number},{meta_publisher_name}, {meta_publisher_location}, {xml_sync_template_id}, {journal_id})',
		'ActionMask' => ACTION_CHECK | ACTION_CCHECK | ACTION_EXEC | ACTION_FETCH | ACTION_SHOW ,
		'RedirUrl' => '',
		'AddTags' => array(
			'class' => 'frmbutton',			
		), 
	),
	
	'delete' => array(
		'CType' => 'action',
		'DisplayName' => getstr('admin.deleteButton'),
		'SQL' => 'SELECT * FROM spArticlesData(3, {id}, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null)',
		'ActionMask' => ACTION_CHECK | ACTION_CCHECK | ACTION_EXEC | ACTION_FETCH | ACTION_REDIRECT ,
		'RedirUrl' => './index.php',
		'AddTags' => array(
			'class' => 'frmbutton',			
			'onclick' => 'javascript:if (!confirm(\'' . getstr('admin.articles.confirmDel') . '\')) {return false;}'
		), 
	),
	'export' => array(
		'CType' => 'action',
		'DisplayName' => getstr('admin.articles.exportButton'),
		'ActionMask' => ACTION_REDIRECT,
		'RedirUrl' => './export.php?id={id}&type={export_type}',
		'AddTags' => array(
			'class' => 'frmbutton',
		),
	),
	
	'versions' => array(
		'CType' => 'action',
		'DisplayName' => getstr('admin.articles.versionsButton'),
		'ActionMask' => ACTION_REDIRECT,
		'RedirUrl' => './relversions.php?article_id={id}',
		'AddTags' => array(
			'class' => 'frmbutton',
		),
	),
	
	
	'cancel' => array(
		'CType' => 'action',
		'DisplayName' => getstr('admin.backButton'),
		'ActionMask' => ACTION_REDIRECT,
		'RedirUrl' => './',
		'AddTags' => array(
			'class' => 'frmbutton',
		),
	),	
);
$gKfor = new kfor($gKforFlds, null, 'POST');
$gKfor->debug = 0;
if((int) $gKfor->lFieldArr['id']['CurValue'] ){
	$gKfor->lFieldArr['title']['AddTags'] = array(
		'class' => 'coolinp disabledinput',
		'readonly' => 'readonly',		
	);
	$gKfor->lFieldArr['title']['AllowNulls'] = true;

}
if ($gKfor->lCurAction == 'save') {
	// Parsvane na special quotes:
	$gKfor->lFieldArr['title']['CurValue'] = parseSpecialQuotes($gKfor->lFieldArr['title']['CurValue']);
	$gKfor->lFieldArr['author']['CurValue'] = parseSpecialQuotes($gKfor->lFieldArr['author']['CurValue']);
}

if($gKfor->lCurAction == 'save' && !$gKfor->lErrorCount){
	if( $_FILES['xmlfile']['name'] ){
		$lExtarray = array(".xml", ".html");
		$lTypearr = array("text/xml", "application/xml", "text/html");
		
		$lFileName = $_FILES['xmlfile']['name'];
		$gFileExt = substr($lFileName, strrpos($lFileName, '.'));
		
		$lIsXml = in_array(strtolower($gFileExt), $lExtarray);
		$lIsXmlMime = in_array(strtolower($_FILES['xmlfile']['type']), $lTypearr);
		if( $lIsXml && $lIsXmlMime ){
			if (!$_FILES['xmlfile']["size"]) {
				$gKfor->SetError($_FILES['xmlfile']['name'], getstr('file.not_valid_file'));
			}elseif ($_FILES['xmlfile']['error'] == UPLOAD_ERR_OK) {
				$lFile = $_FILES['xmlfile']['tmp_name'];
				if($lContents = file_get_contents($lFile)){
					//~ var_dump($lContents);
					//~ exit;	
					$gKfor->lFieldArr['content']['CurValue'] = $lContents;					
				}else{
					$gKfor->SetError($_FILES['xmlfile']['name'], getstr('file.error_while_retrieving_content'));
				}
			} else {
				$gKfor->SetError($_FILES['xmlfile']['name'], getstr('file.error_while_saving'));
			}
		}else{
			$gKfor->SetError($_FILES['xmlfile']['name'], getstr('file.not_valid_file'));
		}
	}
	$lContent = trim($gKfor->lFieldArr['content']['CurValue']);
	if( $lContent != '' ){
		$lDOM = new DOMDocument("1.0");
		//~ $lContent = file_get_contents('./test.xml');
		if (!$lDOM->loadXML($lContent)) {			
			$lContent = '<article><body>' . $lContent . '</body></article>';			
		}	
	}
	
	if( (int)$gKfor->lFieldArr['replace_text_content']['CurValue'] ){		
		$lContent = str_replace("&lt;br/&gt; ", "<" . SPLIT_TAG_NAME . "/>", $lContent);
	}
	
	$lContent = parseIndesignXml($lContent, (int)$gKfor->lFieldArr['replace_text_content']['CurValue'], (int)$gKfor->lFieldArr['journal_id']['CurValue'],  (int)$gKfor->lFieldArr['overwrite_journal_info']['CurValue'] );	
	$gKfor->lFieldArr['content']['CurValue'] = $lContent;
	$lStrippedContent = stripContentFromTags($lContent);
	$gKfor->lFieldArr['save']['SQL'] = 'SELECT * FROM spArticlesData(1, {id}, ' . (int) $user->id . ', {title}, {author}, {content}, \'' . q($lStrippedContent) . '\',{meta_identifier},{meta_identifier_type},{meta_pub_year},{meta_title},{meta_authors},{meta_url_of_pdf},{meta_part_of_host_publication},{meta_journal_type},{meta_start_page},{meta_end_page},{meta_book_title},{meta_journal_name},{meta_journal_volume_number},{meta_publisher_name}, {meta_publisher_location}, {xml_sync_template_id}, {journal_id})';
}

if($gKfor->lCurAction == 'delete' && !$gKfor->lErrorCount){//Triem snimkite
	$lArticleId = (int)$gKfor->lFieldArr['id']['CurValue'];
	DeleteArticlePics($lArticleId);
}

$gKfor->ExecAction();

if($gKfor->lCurAction == 'save' && !$gKfor->lErrorCount){
	$lContent = trim($gKfor->lFieldArr['content']['CurValue']);
	$lId = (int)$gKfor->lFieldArr['id']['CurValue'];
	SyncXmlData($lId, $lContent);
}

$gKforTpl = '
{id}{replace_text_content}
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
			<th colspan="4">' . ((int)$gKfor->lFieldArr['id']['CurValue'] ? getstr('admin.articles.editLabel') : getstr('admin.articles.addLabel') ) . getstr('admin.articles.nameLabel') . '</th>
		</tr>
		' . ((int)$gKfor->lFieldArr['id']['CurValue'] ? 
			'
				<tr>
					<td colspan="2" valign="top">
						<b>{*lastmod}:</b> {@lastmod}<br/>
						<b>{*createuid}:</b> {@createuid}<br/>
					</td>
					<td colspan="2" valign="top" align="right">{save} {cancel} <input class="frmbutton" type="submit" onclick="javascript:openw(\'./match.php?id=' . (int)$gKfor->lFieldArr['id']['CurValue'] . '\', \'aa\', \'location=no,menubar=yes,width=1200,height=770,scrollbars=no,resizable=no,top=0,left=0\')" value="Match" ></input> {versions}  {delete}</td>
				</tr>
			' : '
				<tr>
					<td colspan="2" valign="top">&nbsp;</td>
					<td colspan="2" valign="top" align="right">{save} {cancel}</td>
				</tr>
			'
		) . '
		<tr>
			<td colspan="4" valign="top"><b>{*title}:</b><br/>{title}</td>
		</tr>
		<tr>
			<td colspan="4" valign="top"><b>{*author}:</b><br/>{author}</td>			
		</tr>
		<tr>
			<td colspan="3" valign="top"><b>{*journal_id}:</b><br/>{journal_id}</td>
			<td colspan="1" valign="top"><b>{*overwrite_journal_info}:</b><br/>{overwrite_journal_info}</td>
		</tr>
		<tr>
			<td colspan="2" valign="top"><b>' . getstr('file.label') . ':</b><br/><input class="width: 200px;" type="file" name="xmlfile"></td>
			<td colspan="2" valign="top"><b>{*xml_sync_template_id}:</b><br/>{xml_sync_template_id}</td>
		</tr>
		<tr>
			<td colspan="4" valign="top"><input class="frmbutton" type="button" value="Paste" onmousedown="pasteFromWordInit();return false;"></td>
		</tr>
		<tr>
			<td colspan="4"><b>{*content}:</b><br/>{content}</td>
		</tr>
		<tr>
			<td colspan="4">
				<fieldset>
					<legend>' . getstr('admin.articles.metaFieldsLabel') . '</legend>
					<table cellspacing="0" cellpadding="5" border="0" width="100%">
					<colgroup>
						<col width="25%" />
						<col width="25%" />
						<col width="25%" />
						<col width="25%" />
					</colgroup>
					<tr>
						<td colspan="2" valign="top"><b>{*meta_identifier}:</b><br/>{meta_identifier}</td>
						<td colspan="2" valign="top"><b>{*meta_identifier_type}:</b><br/>{meta_identifier_type}</td>
					</tr>
					<tr>
						<td colspan="2" valign="top"><b>{*meta_title}:</b><br/>{meta_title}</td>
						<td colspan="2" valign="top"><b>{*meta_authors}:</b><br/>{meta_authors}</td>
					</tr>
					<tr>
						<td colspan="2" valign="top"><b>{*meta_pub_year}:</b><br/>{meta_pub_year}</td>
						<td colspan="2" valign="top"><b>{*meta_url_of_pdf}:</b><br/>{meta_url_of_pdf}</td>
					</tr>
					<tr>
						<td colspan="2" valign="top"><b>{*meta_part_of_host_publication}:</b><br/>{meta_part_of_host_publication}</td>
						<td colspan="2" valign="top"><b>{*meta_journal_type}:</b><br/>{meta_journal_type}</td>
					</tr>
					<tr>
						<td colspan="2" valign="top"><b>{*meta_start_page}:</b><br/>{meta_start_page}</td>
						<td colspan="2" valign="top"><b>{*meta_end_page}:</b><br/>{meta_end_page}</td>
					</tr>
					<tr>
						<td colspan="2" valign="top"><b>{*meta_journal_name}:</b><br/>{meta_journal_name}</td>
						<td colspan="2" valign="top"><b>{*meta_journal_volume_number}:</b><br/>{meta_journal_volume_number}</td>
					</tr>
					<tr>
						<td colspan="4" valign="top"><b>{*meta_book_title}:</b><br/>{meta_book_title}</td>
					</tr>
					<tr>
						<td colspan="2" valign="top"><b>{*meta_publisher_name}:</b><br/>{meta_publisher_name}</td>
						<td colspan="2" valign="top"><b>{*meta_publisher_location}:</b><br/>{meta_publisher_location}</td>
					</tr>
					</table>
				</fieldset>
			
			</td>
		</tr>
		<tr>
			<td colspan="4">
				<fieldset>
					<legend>' . getstr('admin.articles.exportLabel') . '</legend>
					<table cellspacing="0" cellpadding="5" border="0" width="100%">
					<colgroup>
						<col width="25%" />
						<col width="25%" />
						<col width="25%" />
						<col width="25%" />
					</colgroup>
					<tr>
						<td colspan="2" valign="top"><b>{*export_type}:</b><br/>{export_type}</td>
						<td colspan="2" valign="bottom"><b>{export}</td>
					</tr>					
					</table>
				</fieldset>
			
			</td>
		</tr>	
		<tr>
			<td colspan="2">&nbsp;</td>
			<td colspan="2" align="right">{save} {cancel} 
				' . ((int)$gKfor->lFieldArr['id']['CurValue'] ? 
					'<input class="frmbutton" type="submit" onclick="javascript:openw(\'./match.php?id=' . (int)$gKfor->lFieldArr['id']['CurValue'] . '\', \'aa\', \'location=no,menubar=yes,width=1200,height=770,scrollbars=no,resizable=no,top=0,left=0\')" value="Match" ></input> {versions} {delete}'
				:
					''
				) . '
			</td>
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

<div id="htmltextHolder" style="width:700px;">
	<div class="t">
		<div class="b">
			<div class="l">
				<div class="r">
					<div class="bl">
						<div class="br">
							<div class="tl">
								<div class="tr">
									<div  style="background-color: #EDEDED;padding:2px 2px 2px 2px;">
										<div id="htmltext"></div>
										<div style="padding:10px 2px 2px 2px;">
											<input class="frmbutton" type="button" value="OK" onclick="cleanHtmlText();return false;">
											<input class="frmbutton" type="button" value="Cancel" onclick="closeHtmlText();return false;">
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
';

$gKfor->lFormHtml = $gKforTpl;
echo $gKfor->Display();
if((int) $gKfor->lFieldArr['id']['CurValue'] ){
	showSyncedXmlDetails((int) $gKfor->lFieldArr['id']['CurValue']);
}

if(!$gEcmsLibRequest){
	echo '<script>var startpos=0;var endpos=0;var init = new HtmlTextInitializer();</script>';
	echo '<script>InitMetaFields();</script>';

	//Not saved changes
	echo '
		<script language="JavaScript">
			SetEvent(\'def1\');
			window.onbeforeunload = ConfirmToExit;
		</script>
	';
	if($gKfor->lCurAction == 'save' && !$gKfor->lErrorCount){
		header('Location: /resources/articles/edit.php?tAction=showedit&id=' .(int) $gKfor->lFieldArr['id']['CurValue']);
		exit;
	}	
}
HtmlEnd();

function stripContentFromTags($pContent){
	return strip_tags($pContent);
}

?>