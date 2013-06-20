<?php 
//error_reporting(E_ALL);
//ini_set('display_errors', '1');
$gDontRedirectToLogin = true;

$papertypes = array(
"9" => 'Taxonomic_paper', 
"3" => 'Software_description', 
"5" => 'Data_paper', 
"4" => 'Interactive_key', 
"10" => 'Species_Inventory', 
"7" => 'General_article', 
"8" => 'Editorial_Correspondence');

$id = $_GET['id'];
$docroot = getenv('DOCUMENT_ROOT');
require_once($docroot . '/lib/static.php');
$xsd_gen = new ctemplate_xsd_generator(array(
  'template_id' => $id 
));
$xsd_gen->GetData();
header('Content-type: application/xml');
header('Content-Disposition: inline; filename="' . strtolower($papertypes[$id]) .  '.xsd"');
echo $xsd_gen->getXml();

?>