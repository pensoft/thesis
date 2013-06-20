<?php

$gTemplArr = array(
	'identifiers.listHead' => '
		<request verb="ListIdentifiers" {_displayRequestParamIfExists(resumption_token_label, resumption_token)}
			{_displayRequestParamIfExists(from_label, from)}
			{_displayRequestParamIfExists(until_label, until)}
			{_displayRequestParamIfExists(set_label, set)}
			{_displayRequestParamIfExists(metadata_prefix_label, metadata_prefix)}
		>' . OAI_URL . '</request> 
	',
	
	'identifiers.listFoot' => '',
	
	'identifiers.listStart' => '
			<ListIdentifiers>
	',
	
	'identifiers.listEnd' => '
				{nav}
			</ListIdentifiers>
	',
	
	'identifiers.listRowoai_dc' => '
				<header>
				    <identifier>{_xmlEscape(identifier)}</identifier>
				    <datestamp>{_xmlEscape(moddate)}</datestamp>
				    {sets}				    
				</header>
	',

	'identifiers.listRowmods' => '
				{*identifiers.listRowoai_dc}
	',

	'identifiers.listSetRow' => '
		<setSpec>{_xmlEscape(name)}</setSpec>
	',

	'identifiers.pageingActiveLast' => '
		 <resumptionToken completeListSize="{records}" cursor="{_getPreviousRecordsCount(currpage, pagesize)}">{$GetResumptionToken(currpage)}</resumptionToken>
	'
);
?>