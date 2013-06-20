<?php
	//~ echo file_get_contents("http://www.w3.org/1998/Math/MathML/");
	$lXml = file_get_contents("1.xml");
	$lDOM = new DOMDocument("1.0");	
	$lDOM->preserveWhiteSpace = true;
	$lDOM->resolveExternals = true;
	if (!$lDOM->loadXML($lXml)){		
		echo 'Could not load xml';
	}
	echo 'Xml loaded successfully111111';

?>