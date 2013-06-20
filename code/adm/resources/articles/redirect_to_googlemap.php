<?php
$docroot = getenv("DOCUMENT_ROOT");
require_once($docroot . '/lib/static.php');

$lRedirectUrl = PTP_URL . '/redirect_to_googlemap.php?' . $_SERVER['QUERY_STRING'];//Url-a kym koito shte redirektnem

if( !is_array($_POST) || !count($_POST)){
	header('Location: ' . $lRedirectUrl );
	exit;
}else{
	$lResult = '<html><head></head><body><form name="pageForm" method="POST" action="' . $lRedirectUrl . '">';
	
	foreach($_POST as $lKey => $lVal ){
		if( is_array($lVal) && count($lVal)){
			foreach($lVal as $lSingleValue){
				$lResult .= '<input type="hidden" name="' . $lKey . '[]" value="' . $lSingleValue . '"></input>';
			}
		}else{
			$lResult .= '<input type="hidden" name="' . $lKey . '" value="' . $lVal . '"></input>';
		}
	}
		
	$lResult .= '</form>
		<script>			
				document.pageForm.submit();				
		</script>
		</body>
		</html>
	';
	echo $lResult;
}
?>