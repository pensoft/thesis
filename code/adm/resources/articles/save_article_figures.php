<?php
$docroot = getenv("DOCUMENT_ROOT");
require_once($docroot . '/lib/static.php');
ini_set('display_errors', 'Off');
session_write_close();

HtmlStart();

//~ $gArticleId = 115;
//~ $gArticleFigs = '<article_figs_and_tables><fig>fig121</fig><fig>fig2</fig><table>table1</table></article_figs_and_tables>';

$gKforFlds = array(	
	'article_id' => array(
		'VType' => 'int',
		'CType' => 'select',
		'SrcValues' => 'SELECT id, id || \' ---- \' || title as name FROM articles ORDER BY id DESC',
		'AddTags' => array(
			'class' => 'coolinp',
		),
		'AllowNulls' => false,
		'DisplayName' => getstr('admin.articles.colArticleId'),
	),
	
	'article_figs' => array(
		'VType' => 'string',
		'CType' => 'textarea',
		'AddTags' => array(
			'class' => 'coolinp contentTextArea',
		),
		'DisplayName' => getstr('admin.articles.colArticleFigures'),
		'AllowNulls' => false
	),
	
	'save' => array(
		'CType' => 'action',
		'DisplayName' => getstr('admin.saveButton'),
		'SQL' => '{article_id}',
		'ActionMask' => ACTION_CHECK | ACTION_CCHECK | ACTION_SHOW ,
		'RedirUrl' => '',
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

$gKforTpl = '
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
			<th colspan="4">' . getstr('admin.articles.editFiguresLabel') . '</th>
		</tr>
		<tr>
			<td colspan="4"><b>{*article_id}:</b><br/>{article_id}</td>
		</tr>
		<tr>
			<td colspan="4" valign="top"><b>' . getstr('file.label') . ':</b><br/><input class="width: 400px;" type="file" name="xmlfile"></td>
		</tr>
		<tr>
			<td colspan="2">&nbsp;</td>
			<td colspan="2" align="right">{save} {cancel} 
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
';
$gKfor = new kfor($gKforFlds, null, 'POST');
$gKfor->lFormHtml = $gKforTpl;

if ($gKfor->lCurAction == 'save') {
	$lArticleId = (int) $gKfor->lFieldArr['article_id']['CurValue'];
	$lArticleFigs = '';
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
				if($lArticleFigs = file_get_contents($lFile)){
					$lResult = SaveArticleFigures($lArticleId, $lArticleFigs);
					if( $lResult != '' ){
						$gKfor->SetError('save', $lResult);
					}
				}else{
					$gKfor->SetError($_FILES['xmlfile']['name'], getstr('file.error_while_retrieving_content'));
				}
			} else {
				$gKfor->SetError($_FILES['xmlfile']['name'], getstr('file.error_while_saving'));
			}
		}else{
			$gKfor->SetError($_FILES['xmlfile']['name'], getstr('file.not_valid_file'));
		}
	}else{
		$gKfor->SetError(getstr('file.fileLabel'), getstr('file.file_not_selected'));
	}
		
}

echo $gKfor->Display();


HtmlEnd();


/**
	Запазва възела с таблиците и фигурите в xml-а на статията. Ако няма такъв възел - го добавя като дете на роот-а иначе го заменя.
	Връща текста на грешката, ако има такава и празен стринг при успех.
*/
function SaveArticleFigures($pArticleId, $pFiguresXml){
	if( $pArticleId ){
		$lArticleXml = GetArticleXml($pArticleId);
		
		$lXML = new DOMDocument("1.0");
		$lFiguresXML = new DOMDocument("1.0");
		$lXML->resolveExternals = true;
		$lFiguresXML->resolveExternals = true;
		
		if (!$lXML->loadXML($lArticleXml) || !$lFiguresXML->loadXML($pFiguresXml)) {//Едно от двете не е валиден xml
			return getstr('admin.articles.WrongArticleOrFiguresXML');
		}
		
		CorrectTablesFormat($lFiguresXML);
		removeIndesignXmlFormattingTags($lFiguresXML);
		
		$lXPath = new DOMXPath($lXML);
		$lFigXPath = new DOMXPath($lFiguresXML);
		
		$lFigsXPathQuery = '//' . XML_FIGURES_AND_TABLES_NODE_NAME;
		$lFigXPathResult = $lFigXPath->query($lFigsXPathQuery);
		$lArticleFigXPathResult = $lXPath->query($lFigsXPathQuery);
		
		if( !$lFigXPathResult->length ){//Xml-а с фигурите е грешен
			return getstr('admin.articles.UnrecognisedFiguresXml');
		}
		
		$lFigXmlNode = $lFigXPathResult->item(0);
		$lImportedNode = $lXML->importNode($lFigXmlNode, true);
		if( !$lArticleFigXPathResult->length ){//Добавяме го като дете на роот-а
			$lXML->documentElement->appendChild($lImportedNode);
		}else{//Заменяме го
			$lArticleFigNode = $lArticleFigXPathResult->item(0);
			$lArticleFigNode->parentNode->replaceChild($lImportedNode, $lArticleFigNode);
		}
		
		SaveArticleXml($pArticleId, $lXML->saveXML());
		return '';
	}
}

define('TABLE_TAG', 'table');
define('TABLE_CELL_TAG', 'Cell');
define('aid_NAMESPACE_URI', 'http://ns.adobe.com/AdobeInDesign/4.0/');

/**
	Оправя структурата на таблиците, понеже идват грешно от индизайна
*/
function CorrectTablesFormat(&$pXmlDom){
	define('TABLE_TAG', 'table');
	define('TABLE_CELL_TAG', 'Cell');
	define('aid_NAMESPACE_URI', 'http://ns.adobe.com/AdobeInDesign/4.0/');

	
	$lXPath = new DOMXPath($pXmlDom);
	$lTableXPath = '//' . TABLE_TAG;
	$lTables = $lXPath->query($lTableXPath);
	
	for( $i = 0; $i < $lTables->length; ++$i ){//Parsvame vsichki tablici
		$lCurrentTable = $lTables->item($i);
		$lHeaderRows = (int)$lCurrentTable->getAttribute('headerRowCount');//Broq na header redovete
		$lBodyRows = (int)$lCurrentTable->getAttribute('bodyRowCount');//Broq na obiknovenite redove
		$lRowCellCount = (int)$lCurrentTable->getAttribute('columnCount');//Broq kletki na red
		
		$lTableCellsQuery = './' . TABLE_CELL_TAG;
		$lTableCells = $lXPath->query($lTableCellsQuery, $lCurrentTable);
		$lTableTbody = $lCurrentTable->appendChild($pXmlDom->createElement('tbody'));
		$lCurrentCellCount = 0;
		$lRowSpanArr = array();//Masiv v koito pazim rowspanovete za da moje da broim kletkite korektno
		
		for( $lCurrentRowNum = 0; $lCurrentRowNum < $lHeaderRows + $lBodyRows; $lCurrentRowNum++){			
			//Broq na postavenite kletki na reda
			$lRowCells = (int)$lRowSpanArr[$lCurrentRowNum];//Vzimame broq na rowspanovete ot prednite redove za tekushtiq red
			
			$lCurrentRow = $lTableTbody->appendChild($pXmlDom->createElement('tr'));
			
			while( $lRowCells < $lRowCellCount ){
				//~ echo 1;
				if( $lCurrentCellCount >= $lTableCells->length){//Ako sa svyrshili kletkite
					break 2;//Endvame while-a i for-a i otivame direktno na sledvashtata tablica
				}
				$lCurrentCell = $lTableCells->item($lCurrentCellCount++);				
				$lCellRowspan = (int)$lCurrentCell->getAttributeNS(aid_NAMESPACE_URI, 'crows');//Rowspan
				$lCellColspan = (int)$lCurrentCell->getAttributeNS(aid_NAMESPACE_URI, 'ccols');//Colspan
				
				if( $lCurrentRowNum < $lHeaderRows ){
					$lReplacementCell = $lCurrentRow->appendChild($pXmlDom->createElement('th'));
				}else{
					$lReplacementCell = $lCurrentRow->appendChild($pXmlDom->createElement('td'));
				}
				
				$lReplacementCell->setAttribute('rowspan', $lCellRowspan);
				$lReplacementCell->setAttribute('colspan', $lCellColspan);
				
				$lCellChildNodes = $lCurrentCell->childNodes;
				for( $lTempChildCounter = 0; $lTempChildCounter < $lCellChildNodes->length; ++$lTempChildCounter){
					$lChild = $lCellChildNodes->item($lTempChildCounter);
					if( $lChild->nodeType == 1 || $lChild->nodeType == 2 || $lChild->nodeType == 3){//Kopirame elementite, atributite i teksta
						$lReplacementCell->appendChild($lChild->cloneNode(true));//Kopirame child-a v novata kletka
					}
				}
				
				//Mahame kletkata ot mqstoto i poneje ve4e sme kopirali vajnite neshta
				$lCurrentCell->parentNode->removeChild($lCurrentCell);
				
				$lRowCells += (int) $lCellColspan;//Uvelichavame go s colspan-a za da broim korektno				
				for( $lTempRowNum = $lCurrentRowNum + 1; $lTempRowNum < $lCurrentRowNum + $lCellRowspan; $lTempRowNum++){//Ako ima rowspan go otbelqzvame v masiva za sledvashtite redove
					if(isset($lRowSpanArr[$lTempRowNum] )){
						$lRowSpanArr[$lTempRowNum] += (int)$lCellColspan;
					}else{
						$lRowSpanArr[$lTempRowNum] = (int)$lCellColspan;
					}
				}				
			}
			
		}
	}
	
}
?>