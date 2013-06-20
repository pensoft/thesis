<?php
$docroot = getenv('DOCUMENT_ROOT');
require_once(PATH_CLASSES . 'spreadsheetreader/SpreadsheetReader.php');
require_once(PATH_CLASSES . 'spreadsheetreader/php-excel-reader/excel_reader2.php');

/**
* Proxy class to excel reader library. 
*/

class cspreadsheetreade_proxy {
	function __construct() {
	
	}
	
	function getSpreadSheetObject($pFile) {
		return new SpreadsheetReader($pFile);
	}
}
?>