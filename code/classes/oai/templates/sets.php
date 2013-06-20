<?php

$gTemplArr = array(
	'sets.listHead' => '
		<request verb="ListSets" {_displayRequestParamIfExists(resumption_token_label, resumption_token)}>' . OAI_URL . '</request> 
	',
	
	'sets.listFoot' => '',
	
	'sets.listStart' => '
			<ListSets>
	',
	
	'sets.listEnd' => '
				{nav}
			</ListSets>
	',
	
	'sets.listRow' => '
				<set>
				    <setSpec>{_xmlEscape(spec)}</setSpec>
				    <setName>{_xmlEscape(name)}</setName>				    
				</set>
	',

	'sets.pageingActiveLast' => '
		 <resumptionToken completeListSize="{records}" cursor="{_getPreviousRecordsCount(currpage, pagesize)}">{$GetResumptionToken(currpage)}</resumptionToken>
	'
);
?>