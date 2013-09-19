<?php
$gTemplArr = array (
	'oai.page' => '<?xml version="1.0"?>
		<OAI-PMH xmlns="http://www.openarchives.org/OAI/2.0/" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.openarchives.org/OAI/2.0/OAI-PMH.xsd">
			<responseDate>' . date('Y-m-d\TH:m:i\Z') . '</responseDate>
			{result_object}
		</OAI-PMH>
	',
	
	'oai.err_row' => '
 		<error code="{_GetOaiErrCode(err_code)}">{_xmlEscape(err_msg)}</error>
	',
	
	'oai.identity' => '		
		<request verb="Identify">{oai_url}</request>
		<Identify>
			<repositoryName>{repository_name}</repositoryName>
			<baseURL>{oai_url}</baseURL>
			<protocolVersion>{protocol_version}</protocolVersion>
			<adminEmail>{admin_email}</adminEmail>
			<earliestDatestamp>{min_date}</earliestDatestamp>
			<deletedRecord>no</deletedRecord>
			<granularity>{date_format}</granularity>		
		</Identify>	
	',
	
	'oai.setsHead' => '
		<request verb="ListSets" {_displayRequestParamIfExists(resumption_token_label, resumption_token_in_request)}>{oai_url}</request>
	',
	
	'oai.setsFoot' => '',
	
	'oai.setsNoData' => '{*oai.err_row}',
	
	'oai.setsStart' => '
			<ListSets>
	',
	
	'oai.setsEnd' => '
				{nav}
			</ListSets>
	',
	
	'oai.setsRow' => '
				<set>
				    <setSpec>{_xmlEscape(spec)}</setSpec>
				    <setName>{_xmlEscape(name)}</setName>
				</set>
	',
	
	'oai.pageingActiveLast' => '
		 <resumptionToken completeListSize="{records}" cursor="{_getPreviousRecordsCount(currpage, pagesize)}">{new_resumption_token}</resumptionToken>
	',
	
	'oai.recordsHead' => '
		<request verb="ListRecords"
			{_displayRequestParamIfExists(resumption_token_label, resumption_token_in_request)}
			{_displayRequestParamIfExists(from_label, from_in_request)}
			{_displayRequestParamIfExists(until_label, until_in_request)}
			{_displayRequestParamIfExists(set_label, set_in_request)}
			{_displayRequestParamIfExists(metadata_prefix_label, metadata_prefix_in_request)}
		>' . OAI_URL . '</request>
	',
	
	'oai.recordsFoot' => '',
	
	'oai.recordsStart' => '
			<ListRecords>
	',
	
	'oai.recordsEnd' => '
				{nav}
			</ListRecords>
	',
	
	'oai.recordsRowOai' => '
				<record>
					<header>
					    <identifier>{_xmlEscape(identifier)}</identifier>
					    <datestamp>{_xmlEscape(moddate)}</datestamp>
					    {_displayOaiRecordSets(set_specs, metadata_prefix, view_object)}
					</header>
					<metadata>
						<oai-dc:dc xmlns:oai-dc="http://www.openarchives.org/OAI/2.0/oai_dc/" xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.openarchives.org/OAI/2.0/oai_dc/ http://www.openarchives.org/OAI/2.0/oai_dc.xsd">
							<dc:title>{_stripXmlTags(title)}</dc:title>
							{_displayOaiRecordAuthors(authors, metadata_prefix, view_object)}
							{_displayOaiRecordKeywords(keywords, metadata_prefix, view_object)}							
							<dc:source>{journal_title} {issue_volume}: {start_page}-{end_page}</dc:source>
							<dc:description>{_stripXmlTags(abstract)}</dc:description>
							<dc:relation>{relation}</dc:relation>
							<dc:rights>info:eu-repo/semantics/openAccess</dc:rights>
							<dc:publisher>Pensoft Publishers</dc:publisher>
							<dc:date>{_stripXmlTags(pubyear)}</dc:date>
							<dc:type>{_stripXmlTags(section_type)}</dc:type>
							<dc:format>text/html</dc:format>
							<dc:identifier>http://dx.doi.org/{_xmlEscape(identifier)}</dc:identifier>
							<dc:identifier>http://www.pensoft.net/journals/{journal_url_title}/article/{article_id}/</dc:identifier>
							<dc:language>en</dc:language>
						</oai-dc:dc>
					</metadata>
				</record>
	',
	
	'oai.recordsRowMods' => '
				<record>
					<header>
					    <identifier>{_xmlEscape(identifier)}</identifier>
					    <datestamp>{_xmlEscape(moddate)}</datestamp>
					    {_displayOaiRecordSets(set_specs, metadata_prefix, view_object)}
					</header>
					<metadata>
						<mods:mods xmlns:mods="http://www.loc.gov/mods/v3"
							xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
							xsi:schemaLocation="http://www.loc.gov/mods/v3 http://www.loc.gov/standards/mods/v3/mods-3-1.xsd">
							{_displayOaiRecordAuthors(authors, metadata_prefix, view_object)}
							<mods:extension>
								<mods:dateAccessioned encoding="iso8601">{_xmlEscape(moddate)}</mods:dateAccessioned>
							</mods:extension>
							<mods:extension>
								<mods:dateAvailable encoding="iso8601">{_xmlEscape(moddate)}</mods:dateAvailable>
							</mods:extension>
							<mods:originInfo>
								<mods:dateIssued encoding="iso8601">{_xmlEscape(pubyear)}</mods:dateIssued>
							</mods:originInfo>
							<mods:relatedItem type="host">
								<mods:titleInfo>
									<mods:title>{_xmlEscape(journal_title)}</mods:title>
								</mods:titleInfo>
								<mods:part>
									<mods:detail type="volume">
										<mods:number>{_xmlEscape(issue_volume)}</mods:number>
									</mods:detail>
									{_displayModsIssueNumber(issue_number, show_issue_number)}
									<mods:extent unit="pages">
										<mods:start>{_xmlEscape(start_page)}</mods:start>
										<mods:end>{_xmlEscape(end_page)}</mods:end>
									</mods:extent>
									<mods:date>{_xmlEscape(pubyear)}</mods:date>
								</mods:part>
							</mods:relatedItem>
							<mods:identifier type="uri">http://dx.doi.org/{_xmlEscape(identifier)}</mods:identifier>
							<mods:identifier type="uri">http://www.pensoft.net/journals/{journal_url_title}/article/{article_id}/</mods:identifier>
							<mods:abstract>{_stripXmlTags(abstract)}</mods:abstract>
							<mods:physicalDescription>
								<mods:internetMediaType>text/html</mods:internetMediaType>
							</mods:physicalDescription>
							<mods:language>
								<mods:languageTerm authority="rfc3066">en_US</mods:languageTerm>
							</mods:language>
							<mods:originInfo>
								<mods:publisher>Pensoft Publishers</mods:publisher>
							</mods:originInfo>
							{_displayOaiRecordKeywords(keywords, metadata_prefix, view_object)}
							<mods:titleInfo>
								<mods:title>{_stripXmlTags(title)}</mods:title>
							</mods:titleInfo>
							<mods:genre>{section_type}</mods:genre>
						</mods:mods>
					</metadata>
				</record>
	',
	
	'oai.identifiersHead' => '
		{*oai.recordsHead}
	',
	
	'oai.identifiersFoot' => '',
	
	'oai.identifiersStart' => '
			<ListIdentifiers>
	',
	
	'oai.identifiersEnd' => '
				{nav}
			</ListIdentifiers>
	',
	
	'oai.identifiersRow' => '
				<header>
				    <identifier>{_xmlEscape(identifier)}</identifier>
				    <datestamp>{_xmlEscape(moddate)}</datestamp>
				     {_displayOaiRecordSets(set_specs, metadata_prefix, view_object)}
				</header>
	',
	
	'oai.singleRecordHead' => '
		<request verb="GetRecord"
			{_displayRequestParamIfExists(identifier_label, identifier_in_request)}
			{_displayRequestParamIfExists(metadata_prefix_label, metadata_prefix_in_request)}
		>{oai_url}</request>
	',
	
	'oai.singleRecordFoot' => '',
	
	'oai.singleRecordStart' => '
			<GetRecord>
	',
	
	'oai.singleRecordEnd' => '
			</GetRecord>
	',
	
	'oai.recordsSetRowOai' => '
		<setSpec>{_xmlEscape(spec)}</setSpec>
	',
	
	'oai.recordsSetRowMods' => '
		<setSpec>{_xmlEscape(spec)}</setSpec>
	',
	
	'oai.recordsAuthorsRowOai' => '<dc:creator>{_stripXmlTags(last_name)},{_stripXmlTags(first_name)}</dc:creator>',
	
	'oai.recordsAuthorsRowMods' => '<mods:name>
								<mods:role>
									<mods:roleTerm type="text">author</mods:roleTerm>
								</mods:role>
								<mods:namePart>{_stripXmlTags(last_name)}, {_stripXmlTags(first_name)}</mods:namePart>
							</mods:name>',
	
	'oai.recordsKeywordsRowOai' => '<dc:subject>{_stripXmlTags(name)}</dc:subject>',
	
	'oai.recordsKeywordsRowMods' => '<mods:subject>
								<mods:topic>{_stripXmlTags(name)}</mods:topic>
							</mods:subject>' ,
	
	'oai.metadataFormatsHead' => '
		<request verb="ListMetadataFormats" {_displayRequestParamIfExists(identifier_label, identifier_in_request)}>' . OAI_URL . '</request> 
	',

	'oai.metadataFormatsFoot' => '',
	
	'oai.metadataFormatsStart' => '
			<ListMetadataFormats>
	',
	
	'oai.metadataFormatsEnd' => '
			</ListMetadataFormats>
	',
	
	'oai.metadataFormatsRow' => '
				<metadataFormat>
				    <metadataPrefix>{_xmlEscape(prefix)}</metadataPrefix>
				    <schema>{_xmlEscape(schema)}</schema>
				    <metadataNamespace>{_xmlEscape(namespace)}</metadataNamespace>
				</metadataFormat>
	',
);

?>
