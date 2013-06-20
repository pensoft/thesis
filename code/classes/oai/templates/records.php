<?php

$gTemplArr = array(
	'records.listHead' => '
		<request verb="ListRecords" 
			{_displayRequestParamIfExists(resumption_token_label, resumption_token)}
			{_displayRequestParamIfExists(from_label, from)}
			{_displayRequestParamIfExists(until_label, until)}
			{_displayRequestParamIfExists(set_label, set)}
			{_displayRequestParamIfExists(metadata_prefix_label, metadata_prefix)}
		>' . OAI_URL . '</request> 
	',
	
	'records.listFoot' => '',
	
	'records.listStart' => '
			<ListRecords>
	',
	
	'records.listEnd' => '
				{nav}
			</ListRecords>
	',
	
	'records.listRowoai_dc' => '
				<record>
					<header>
					    <identifier>{_xmlEscape(identifier)}</identifier>
					    <datestamp>{_xmlEscape(moddate)}</datestamp>
					    {sets}				    
					</header>
					<metadata>
						<oai-dc:dc xmlns:oai-dc="http://www.openarchives.org/OAI/2.0/oai_dc/" xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.openarchives.org/OAI/2.0/oai_dc/ http://www.openarchives.org/OAI/2.0/oai_dc.xsd">
							<dc:title>{_stripXmlTags(title)}</dc:title>
							{authors}
							{keywords}
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
	
	'records.listRowmods' => '
				<record>
					<header>
					    <identifier>{_xmlEscape(identifier)}</identifier>
					    <datestamp>{_xmlEscape(moddate)}</datestamp>
					    {sets}				    
					</header>
					<metadata>
						<mods:mods xmlns:mods="http://www.loc.gov/mods/v3"
							xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
							xsi:schemaLocation="http://www.loc.gov/mods/v3 http://www.loc.gov/standards/mods/v3/mods-3-1.xsd">
							{authors}
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
							{keywords}
							<mods:titleInfo>
								<mods:title>{_stripXmlTags(title)}</mods:title>
							</mods:titleInfo>
							<mods:genre>{section_type}</mods:genre>
						</mods:mods>
					</metadata>
				</record>
	',

	'records.singleItemHead' => '
		<request verb="GetRecord" 
			{_displayRequestParamIfExists(identifier_label, identifier)}
			{_displayRequestParamIfExists(metadata_prefix_label, metadata_prefix)}
		>' . OAI_URL . '</request> 
	',
	
	'records.singleItemFoot' => '',
	
	'records.singleItemStart' => '
			<GetRecord>
	',
	
	'records.singleItemEnd' => '				
			</GetRecord>
	',


	'records.listSetRowoai_dc' => '
		<setSpec>{_xmlEscape(spec)}</setSpec>
	',
	
	'records.listSetRowmods' => '
		<setSpec>{_xmlEscape(spec)}</setSpec>
	',

	'records.listAuthorsRowoai_dc' => '<dc:creator>{_stripXmlTags(FAMILIA)},{_stripXmlTags(IME)}</dc:creator>',
	
	'records.listAuthorsRowmods' => '<mods:name>
								<mods:role>
									<mods:roleTerm type="text">author</mods:roleTerm>
								</mods:role>
								<mods:namePart>{_stripXmlTags(FAMILIA)}, {_stripXmlTags(IME)}</mods:namePart>
							</mods:name>',

	'records.listKeywordsRowoai_dc' => '<dc:subject>{_stripXmlTags(name)}</dc:subject>',
	
	'records.listKeywordsRowmods' => '<mods:subject>
								<mods:topic>{_stripXmlTags(name)}</mods:topic>
							</mods:subject>',

	'records.pageingActiveLast' => '
		 <resumptionToken completeListSize="{records}" cursor="{_getPreviousRecordsCount(currpage, pagesize)}">{$GetResumptionToken(currpage)}</resumptionToken>
	'
);
?>