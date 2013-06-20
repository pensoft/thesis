<?php
error_reporting(E_ALL);
$docroot = getenv('DOCUMENT_ROOT');
require_once($docroot . '/lib/static.php');
// ini_set('display_errors', 'Off');

$gDocumentId = (int)$_REQUEST['document_id'];
if(!$gDocumentId)
	$gDocumentId = 1411;

//~ $gMode = (int)$_REQUEST['mode'];
//~ if(!in_array($gMode, array(1, 2))){
	//~ $gMode = 1;
//~ }

//~ $lDocumentSerializer = new cdocument_serializer(array(
//~ // 	'document_id' => 183,
	//~ 'document_id' => $gDocumentId,
	//~ 'mode' => 2,
//~ ));
//~ $lDocumentSerializer->GetData();
//~ $lDocumentXml = $lDocumentSerializer->getXml();

//~ header("Content-type: text/xml");
//~ echo $lDocumentXml;
//~ exit;

 $lDocumentSerializer = new ctemplate_xsd_generator(array(
  	'document_id' => 1825,
	 'template_id' => 2,
	 'mode' => 1 
 ));

 $lDocumentSerializer->GetData();
 $lDocumentXsd = $lDocumentSerializer->getXml();

 header("Content-type: text/xml");
 echo $lDocumentXsd;
 exit;

//~ $lApi = new capi(array(
	//~ 'action' => 'process_document',
	//~ 'xml' => file_get_contents('./research_article_import.xml'),
//~ ));

//~ var_dump($lApi->GetResult());
//~ exit;

//~ // header("Content-type: text/xml");
//~ echo $lDocumentSerializer->getXml();

?>