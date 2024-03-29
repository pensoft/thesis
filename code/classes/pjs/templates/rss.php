<?php

// @formatter:off
$gTemplArr = array(
	
	'rss.startrs' => '',
	
	'rss.row' => '
		<item>
		    <title>{_strip_tags(name)}</title>
		    <link>' . SITE_URL . 'articles.php?id={id}</link>
		    <description><![CDATA[
					<p>{journal_name} 1:e{id}</p>
					<p>DOI: {doi}</p>
					<p>Authors: {authors_list}</p>	
					<p>Abstract: {_removeFierstParagraph(abstract)}</p>
					<p><a href="' . SITE_URL . 'articles.php?id={id}">HTML</a></p>
					<p><a href="' . SITE_URL . 'lib/ajax_srv/article_elements_srv.php?action=download_xml&item_id={id}">XML</a></p>
					<p><a href="' . SITE_URL . 'lib/ajax_srv/article_elements_srv.php?action=download_pdf&item_id={id}">PDF</a></p>
			]]></description>
		    <category>{journal_section_name}</category>
		    <pubDate>{_formatDateForRSS(publish_date)}</pubDate>
		</item>
	',
	//'article_elements_srv.php' + '?action=download_pdf&item_id=' + pDocumentId;
	'rss.endrs' => '',
	
	'rss.empty' => '',

	'rss.header' => '
<rss version="0.91">
    <channel>
        <title>Latest {records} Articles from {journal_name}</title>
        <description>Latest {records} Articles from {journal_name}</description>
        <link>' . SITE_URL . '</link>
        <lastBuildDate>' . date('D j M Y G:i:s O') . '</lastBuildDate>
        <generator>Pensoft FeedCreator</generator>
        <image>
            <url>' . SITE_URL . 'i/logo.jpg</url>
            <title>Latest Articles from {journal_name}</title>
            <link>' . SITE_URL . '</link>
            <description><![CDATA[Feed provided by ' . SITE_URL . '. Click to visit.]]></description>
        </image>
	',
	'rss.footer' => '
	</channel>
</rss>
	',
);

?>