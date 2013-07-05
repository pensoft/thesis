<?php
$docroot = getenv('DOCUMENT_ROOT');
require_once($docroot . '/lib/static.php');
ini_set('display_errors', 'off');

// header("Pragma: public");
// header("Expires: 0"); 
// header("Cache-Control: must-revalidate, post-check=0, pre-check=0");

header("Content-Type: application/pdf");

$lVersionid = (int)$_REQUEST['version_id'];
$lReadonly = (int)$_REQUEST['readonly_preview'];

$lURLDocVersion = SITE_URL .  'generate_pdf.php?version_id=' . (int)$lVersionid . '&readonly_preview=' . (int)$lReadonly;
$lFileName = 'pdf_preview_' . $lVersionid . '.pdf';

exec('xvfb-run --server-args=' . escapeshellarg('-screen 0, 1024x768x24') . ' wkhtmltopdf ' . escapeshellarg($lURLDocVersion) . ' /tmp/' . escapeshellarg($lFileName));

header("Content-Disposition: attachment;filename=\"" . $lFileName . "\"");

readfile('/tmp/' . $lFileName);
unlink('/tmp/' . $lFileName);
exit;

?>