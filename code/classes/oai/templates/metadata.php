<?php

$gTemplArr = array(
	'metadata.formatsHead' => '
		<request verb="ListMetadataFormats" {_displayRequestParamIfExists(identifier_label, identifier)}>' . OAI_URL . '</request> 
	',

	'metadata.formatsFoot' => '',
	
	'metadata.formatsStart' => '
			<ListMetadataFormats>
	',
	
	'metadata.formatsEnd' => '
			</ListMetadataFormats>
	',
	
	'metadata.formatsItemRow' => '
				<metadataFormat>
				    <metadataPrefix>{_xmlEscape(prefix)}</metadataPrefix>
				    <schema>{_xmlEscape(schema)}</schema>
				    <metadataNamespace>{_xmlEscape(namespace)}</metadataNamespace>
				</metadataFormat>
	',
	
	'metadata.formatsGlobalRow' => '{*metadata.formatsItemRow}',
);
?>