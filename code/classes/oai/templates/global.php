<?php

$gTemplArr = array(
	'global.empty' => '',

	'global.xmlOnlyHeader' => '',

	'global.xmlOnlyFooter' => '',
	
	
	'global.xmlStartContent' => '
		{*global.xmlOnlyHeader}
		<OAI-PMH xmlns="http://www.openarchives.org/OAI/2.0/" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.openarchives.org/OAI/2.0/OAI-PMH.xsd"> 
			<responseDate>' . date('Y-m-d\TH:m:i\Z') . '</responseDate>',
	
	'global.xmlEndContent' => '
		</OAI-PMH>
		{*global.xmlOnlyFooter}	
	',
	
	'global.indexPage' => '
		{*global.xmlStartContent}
		{content}
		{*global.xmlEndContent}
	',

	'global.errRow' => '
		<error code="{_xmlEscape(err_code)}">{_xmlEscape(err_msg)}</error>	
	',
);
?>