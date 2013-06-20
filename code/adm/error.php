<?php

$docroot = getenv('DOCUMENT_ROOT');
require_once($docroot . '/lib/static.php');

HtmlStart();

echo '<p style="color:#FF0000;"><b>Нямате необходимите права за достъп</b></p>';

HtmlEnd();

?>