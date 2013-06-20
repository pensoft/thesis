<?php

$gTemplArr = array(
	'doaj.listHead' => '<?xml version="1.0"?>
<records>',
	
	'doaj.listFoot' => '
</records>
	',
	
	'doaj.listStart' => '',
	
	'doaj.listEnd' => '',
	
	'doaj.errRow' => '
		{err_msg}
	',
	
	'doaj.listRow' => '
	<record>
		<language>eng</language>
		<publisher>Pensoft Publishers</publisher>
		<journalTitle>{_xmlEscape(journal_title)}</journalTitle>
		<issn>{_xmlEscape(isbn_print)}</issn>
		<eissn>{_xmlEscape(isbn_online)}</eissn>
		<publicationDate>{_xmlEscape(pubdate)}</publicationDate>
		<volume>{_xmlEscape(issue_volume)}</volume>
		<startPage>{_xmlEscape(start_page)}</startPage>
		<endPage>{_xmlEscape(end_page)}</endPage>
		<doi>{_xmlEscape(identifier)}</doi>
		<publisherRecordId>{_xmlEscape(article_id)}</publisherRecordId>
		<documentType>{_xmlEscape(section_type)}</documentType>
		<title language="eng">{_stripXmlTags(title)}</title>
		{authors}
		<abstract language="eng">{_stripXmlTags(abstract)}</abstract>
		<fullTextUrl format="html">http://www.pensoft.net/journals/{journal_url_title}/article/{article_id}/</fullTextUrl>
		{keywords}
	</record>',
	
	'doaj.listAuthorsHead' => '<authors>',
	
	'doaj.listAuthorsFoot' => '
		</authors>',
	
	'doaj.listAuthorsRow' => '	
			<author>
				<name>{_stripXmlTags(IME)} {_stripXmlTags(FAMILIA)}</name>					
			</author>',
	
	'doaj.listKeywordsHead' => '<keywords language="eng">',
	
	'doaj.listKeywordsFoot' => '
		</keywords>',
	'doaj.listKeywordsRow' => '
			<keyword>{_stripXmlTags(name)}</keyword>',
	
	
);
?>