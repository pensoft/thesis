<?php
$docroot = getenv("DOCUMENT_ROOT");
require_once($docroot . '/lib/static.php');
$historypagetype=HISTORY_ACTIVE;

HtmlStart();

echo GetTemplateObjectSubobjects($_REQUEST['template_object_id']);

HtmlEnd();


?>