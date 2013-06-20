<?php
$docroot = getenv("DOCUMENT_ROOT");
require_once($docroot . '/lib/static.php');
session_write_close();
set_time_limit(60*60*5);//Za da ne timeoutne - 5 chasa
ini_set('memory_limit', '300M');
error_reporting((int)ERROR_REPORTING);

$lArticleId = (int) $_REQUEST['id'];
$lExportType = (int) $_REQUEST['type'];


if( !$lArticleId ){
	header('Location: /');
	exit;
}
$lCn = Con();
$lSql = '
	SELECT xml_content as xml, id
	FROM articles
	WHERE id = ' . (int) $lArticleId . '
';
$lCn->Execute($lSql);
$lCn->MoveFirst();

$lXml = $lCn->mRs['xml'];
$lId  = (int)$lCn->mRs['id'];

if( !$lId ){
	reportError( getstr('admin.articles.wrongArticle') );
	return;
}

switch($lExportType){
	default:
	case((int)XML_EXPORT_TYPE):{
		header("Content-type: text/xml");
		echo $lXml;
		break;
	}
	case((int)HTML_EXPORT_TYPE):{
		parseXmlThroughXsl($lXml, PATH_CLASSES . 'xsl/html.xsl');


		break;
	}
	case((int)EOL_XML_EXPORT_TYPE):{
		parseXmlThroughXsl($lXml, PATH_CLASSES . 'xsl/eol_xml.xsl', true);

		break;
	}
	case((int)HTML_OLD_EXPORT_TYPE):{
		parseXmlThroughXsl($lXml, PATH_CLASSES . 'xsl/html_old.xsl');


		break;
	}
	case((int)META_EXPORT_TYPE):{
		parseXmlThroughXsl($lXml, PATH_CLASSES . 'xsl/metadata.xsl', true);


		break;
	}
	case((int)MEDIAWIKI_EXPORT_TYPE):{
		parseXmlThroughXsl($lXml, PATH_CLASSES . 'xsl/mediawiki.xsl', true);


		break;
	}
	case((int)HTML_NEW_EXPORT_TYPE):{
		parseXmlThroughXsl($lXml, PATH_CLASSES . 'xsl/html_new.xsl');


		break;
	}
}

function parseXmlThroughXsl($pXML, $pXSL, $pSendXmlHeader = false, $pEscapeMediaWikiCharacters = false){
	$lXML = new DOMDocument("1.0");
	$lXSL = new DOMDocument("1.0");


	if (!$lXSL->load($pXSL)) {
		reportError( getstr('admin.articles.xslNotValid') );
		return;
	}


	$lXML->resolveExternals = true;
	if (!$lXML->loadXML($pXML)) {
		reportError( getstr('admin.articles.xmlNotValid') );
		return;
	}
	// Configure the transformer
	$lXslProcessor = new XSLTProcessor;
	$lXslProcessor->registerPHPFunctions();
	$lXslProcessor->importStyleSheet($lXSL);

	header("Content-type: text/html");
	$lXSLResult =  $lXslProcessor->transformToXML($lXML);
	$lXSLResult = GetHtmlBallons($lXSLResult);
	if( $pSendXmlHeader )
		header("Content-type: text/xml");
	if( $pEscapeMediaWikiCharacters ){
		$lXSLResult = preg_replace('/' . MEDIAWIKI_ESCAPE_START . '(.*)' . MEDIAWIKI_ESCAPE_END . '/Ums', '<nowiki>$1</nowiki>', $lXSLResult);
	}
	echo $lXSLResult;
}

function reportError($pError){
	HtmlStart();
	echo '<div class="error">' . $pError . '</div>';
	HtmlEnd();
}

?>