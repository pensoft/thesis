<?php
error_reporting((int)ERROR_REPORTING);
$docroot = getenv("DOCUMENT_ROOT");
require_once($docroot . '/lib/static.php');
$lXml = file_get_contents('./test.xml');
$lXml = transformXmlWithXsl($lXml, PATH_CLASSES . 'xsl/eol_export_new.xsl');
$lXml = GetFormattedXml($lXml);
header("Content-type: text/xml"); 
echo $lXml;
?>