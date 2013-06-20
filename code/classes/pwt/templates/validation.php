<?php

$gTemplArr = array(
	'validation.document_errors' => '
		<div class="P-Document-Validation-Page">
			{_DisplayValidationErrs(errors_count, xml_errors)}
		</div>
	',
	
	'validation.document_path' => '
		 » <a href="/display_document.php?document_id={document_id}">{_CutText(document_name, 60)}</a> » <span class="P-Document-Validation-Path-Err">' . getstr('pwt.xmlvalidation.didntpass') . '</span>
		 <script type="text/javascript">
			$(document).ready(function() {
				showAllWarningInstancesInMenu(\'articleMenu\',\'P-Warning-Structure\');
			});
		 </script>
	',
	
	'validation.document_path_valid' => '
		 » <a href="/display_document.php?document_id={document_id}">{_CutText(document_name, 60)}</a> » <span class="P-Document-Validation-Path-Valid">' . getstr('pwt.xmlvalidation.pass') . '</span>
		 <script type="text/javascript">
			$(document).ready(function() {
				showAllWarningInstancesInMenu(\'articleMenu\',\'P-Warning-Structure\');
			});
		 </script>
	',
);

?>