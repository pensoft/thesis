<?php
$gDontRedirectToLogin = 1;
$docroot = getenv('DOCUMENT_ROOT');
require_once($docroot . '/lib/static.php');
ini_set('display_errors', 'off');
// header("Pragma: public");
// header("Expires: 0"); 
// header("Cache-Control: must-revalidate, post-check=0, pre-check=0");

header("Content-Type: application/pdf");

$lDocumentid = (int)$_REQUEST['document_id'];
$lReadonly = (int)$_REQUEST['readonly_preview'];

$lURLDocVersion = SITE_URL .  'generate_pdf.php?document_id=' . (int)$lDocumentid . '&readonly_preview=' . (int)$lReadonly;
$lFileName = 'pdf_preview_' . $lDocumentid . '.pdf';
// var_dump($lURLDocVersion);
// exit;
// load stylesheets
$lArgs = '-s ' . escapeshellarg($docroot . '/lib/pdf.css');

//weasyprint 'http://victorp.pjs.pensoft.net/generate_pdf.php?version_id=3550&readonly_preview=1' '/tmp/pdf_preview_3550.pdf' -s '/home/steeler/mnt/pensoft/victorp.pmt/code/pjs/lib/pdf.css'
// var_dump('weasyprint ' . escapeshellarg($lURLDocVersion) . ' ' . escapeshellarg('/tmp/' . $lFileName) . ' ' . $lArgs);
// exit;
exec('weasyprint ' . escapeshellarg($lURLDocVersion) . ' ' . escapeshellarg('/tmp/' . $lFileName) . ' ' . $lArgs);

header("Content-Disposition: attachment;filename=\"" . $lFileName . "\"");

readfile('/tmp/' . $lFileName);
unlink('/tmp/' . $lFileName);
exit;

?>