<?php

$gTemplArr = array(
	'identify.default' => '
	<request verb="Identify">' . OAI_URL . '</request>
	<Identify>
		<repositoryName>' . REPOSITORY_NAME . '</repositoryName>
		<baseURL>' . OAI_URL . '</baseURL>
		<protocolVersion>' . PROTOCOL_VERSION . '</protocolVersion>
		<adminEmail>' . ADMIN_EMAIL . '</adminEmail>
		<earliestDatestamp>{min_date}</earliestDatestamp>
		<deletedRecord>no</deletedRecord>
		<granularity>' . DATE_TEXT_FORMAT . '</granularity>		
	</Identify>
	',
);
?>