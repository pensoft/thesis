<?xml version='1.0'?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:dcterms="http://purl.org/dc/terms/" xmlns:geo="http://www.w3.org/2003/01/geo/wgs84_pos#" xmlns:dwc="http://rs.tdwg.org/dwc/dwcore/" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.eol.org/transfer/content/0.3 http://services.eol.org/schema/content_0_3.xsd" xmlns:tp="http://www.plazi.org/taxpub" xmlns:php="http://php.net/xsl">
	<xsl:import href="./default.xsl"/>
	<xsl:output method="xml" encoding="UTF-8"/>	
	<xsl:variable name="gPicUrl">{file_name}</xsl:variable>
	<xsl:variable name="gDefaultNsUri">http://www.mediawiki.org/xml/export-0.4/</xsl:variable>
	<xsl:variable name="gEscapeStart">&lt;nowiki&gt;</xsl:variable>
	<xsl:variable name="gEscapeEnd">&lt;/nowiki&gt;</xsl:variable>
	
	<xsl:template match="/">		
		<mediawiki xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.mediawiki.org/xml/export-0.4/ http://www.mediawiki.org/xml/export-0.4.xsd" version="0.4">
			<xsl:attribute name="xmlns"><xsl:value-of select="$gDefaultNsUri"></xsl:value-of></xsl:attribute>
			<siteinfo>
				<sitename>Species-ID</sitename>
				<base>http://species-id.net/wiki/Main_Page</base>
				<generator>MediaWiki 1.18alpha</generator>
				<case>first-letter</case>
				<namespaces>
					<namespace key="-2" case="first-letter">Media</namespace>
					<namespace key="-1" case="first-letter">Special</namespace>
					<namespace key="0" case="first-letter" />
					<namespace key="1" case="first-letter">Talk</namespace>
					<namespace key="2" case="first-letter">User</namespace>
					<namespace key="3" case="first-letter">User talk</namespace>
					<namespace key="4" case="first-letter">Species-ID</namespace>
					<namespace key="5" case="first-letter">Species-ID talk</namespace>
					<namespace key="6" case="first-letter">File</namespace>
					<namespace key="7" case="first-letter">File talk</namespace>
					<namespace key="8" case="first-letter">MediaWiki</namespace>
					<namespace key="9" case="first-letter">MediaWiki talk</namespace>
					<namespace key="10" case="first-letter">Template</namespace>
					<namespace key="11" case="first-letter">Template talk</namespace>
					<namespace key="12" case="first-letter">Help</namespace>
					<namespace key="13" case="first-letter">Help talk</namespace>
					<namespace key="14" case="first-letter">Category</namespace>
					<namespace key="15" case="first-letter">Category talk</namespace>
					<namespace key="102" case="first-letter">Property</namespace>
					<namespace key="103" case="first-letter">Property talk</namespace>
					<namespace key="104" case="first-letter">Type</namespace>
					<namespace key="105" case="first-letter">Type talk</namespace>
					<namespace key="106" case="first-letter">Form</namespace>
					<namespace key="107" case="first-letter">Form talk</namespace>
					<namespace key="108" case="first-letter">Concept</namespace>
					<namespace key="109" case="first-letter">Concept talk</namespace>
					<namespace key="170" case="first-letter">Filter</namespace>
					<namespace key="171" case="first-letter">Filter talk</namespace>
					<namespace key="198" case="first-letter">Private</namespace>
					<namespace key="199" case="first-letter">Private talk</namespace>
					<namespace key="200" case="first-letter">Portal</namespace>
					<namespace key="201" case="first-letter">Portal talk</namespace>
					<namespace key="202" case="first-letter">Bibliography</namespace>
					<namespace key="203" case="first-letter">Bibliography talk</namespace>
					<namespace key="204" case="first-letter">Draft</namespace>
					<namespace key="205" case="first-letter">Draft talk</namespace>
					<namespace key="206" case="first-letter">Submission</namespace>
					<namespace key="207" case="first-letter">Submission talk</namespace>
					<namespace key="208" case="first-letter">Reviewed</namespace>
					<namespace key="209" case="first-letter">Reviewed talk</namespace>
					<namespace key="274" case="first-letter">Widget</namespace>
					<namespace key="275" case="first-letter">Widget talk</namespace>
				</namespaces>
			</siteinfo>
			
			
			
			<xsl:call-template name="parseArticles">					
			</xsl:call-template>	
		</mediawiki>
	</xsl:template>
	
	<!-- 
		Обработваме статиите една по една
	-->
	<xsl:template name="parseArticles">
		<xsl:variable name="lArticles" select="//article" />
		<xsl:for-each select="$lArticles" >				
			<xsl:variable name="lCurrentArticle" select="." />			
			<xsl:call-template name="parseTaxons">					
				<xsl:with-param name="pCurrentArticle" select="$lCurrentArticle"></xsl:with-param>								
			</xsl:call-template>				
		</xsl:for-each>
	</xsl:template>
	
	<!-- 
		За всяка от статиите обхождаме всички taxon-treatment-и
	-->
	<xsl:template name="parseTaxons">
		<xsl:param name="pCurrentArticle" />		
		
		<xsl:variable name="lTaxons" select="$pCurrentArticle/body//tp:taxon-treatment" />		
		<xsl:variable name="lArticleDoi" select="$pCurrentArticle/front/article-meta/article-id[@pub-id-type='doi']" />
		<xsl:variable name="lArticleTitle" select="$pCurrentArticle/front/article-meta/title-group/article-title" />
		<xsl:variable name="lArticlePublisher" select="$pCurrentArticle/front/journal-meta/publisher/publisher-name" />
		<xsl:variable name="lArticlePubYear" select="$pCurrentArticle/front/article-meta/pub-date[@pub-type='epub']/year" />
		<xsl:variable name="lArticleVolume" select="$pCurrentArticle/front/article-meta/issue" />
		<xsl:variable name="lArticleFPage" select="$pCurrentArticle/front/article-meta/fpage" />
		<xsl:variable name="lArticleLPage" select="$pCurrentArticle/front/article-meta/lpage" />
		<xsl:variable name="lArticlePages"><xsl:value-of select="$pCurrentArticle/front/article-meta/fpage"></xsl:value-of>-<xsl:value-of select="$pCurrentArticle/front/article-meta/lpage"></xsl:value-of></xsl:variable>		
		<xsl:variable name="lJournalTitle" select="php:function('getXslTransformedArticleJournalTitle')" />
		<xsl:variable name="lJournalUrlTitle" select="php:function('getXslTransformedArticleJournalUrlTitle')" />
		<xsl:variable name="lArticleAuthors" select="$pCurrentArticle/front/article-meta/contrib-group/contrib[@contrib-type='author']/name"></xsl:variable>
		
		<xsl:for-each select="$lTaxons" >				
			<xsl:variable name="lCurrentTaxon" select="." />
			<xsl:variable name="lTaxonStatusNode" select="$lCurrentTaxon/tp:nomenclature/tp:taxon-status" />		
			<xsl:variable name="lTaxonStatus">			
				<xsl:variable name="lNormalizedStatus" select="normalize-space($lTaxonStatusNode)" />
				<xsl:call-template name="ToLower">
					<xsl:with-param name="inputString" select="$lNormalizedStatus"/>
				</xsl:call-template>
			</xsl:variable>	
			<xsl:choose>
				<xsl:when test="$lTaxonStatus = 'incertae sedis'"></xsl:when>
				<xsl:otherwise>
					<xsl:call-template name="singleTaxonTemplate">					
						<xsl:with-param name="pTaxonNode" select="$lCurrentTaxon"></xsl:with-param>
						<xsl:with-param name="pArticleDoi"><xsl:value-of select="$lArticleDoi"></xsl:value-of></xsl:with-param>
						<xsl:with-param name="pTaxonNum"><xsl:value-of select="position()"></xsl:value-of></xsl:with-param>						
						<xsl:with-param name="pScientificName">
							<xsl:call-template name="get_node_text_template">
								<xsl:with-param name="pNode" select="$lCurrentTaxon/tp:nomenclature/tp:taxon-name"></xsl:with-param>
								<xsl:with-param name="pPutSpaces">1</xsl:with-param>
							</xsl:call-template>					
						</xsl:with-param>
						<xsl:with-param name="pArticleAuthors" select="$lArticleAuthors"></xsl:with-param>						
						<xsl:with-param name="pJournalTitle" select="$lJournalTitle"></xsl:with-param>
						<xsl:with-param name="pJournalUrlTitle" select="$lJournalUrlTitle"></xsl:with-param>
						<xsl:with-param name="pArticleTitle" select="$lArticleTitle"></xsl:with-param>
						<xsl:with-param name="pArticlePubYear" select="$lArticlePubYear"></xsl:with-param>
						<xsl:with-param name="pArticleVolume" select="$lArticleVolume"></xsl:with-param>
						<xsl:with-param name="pArticlePages" select="$lArticlePages"></xsl:with-param>
						<xsl:with-param name="pArticleFPage"  select="$lArticleFPage"/>
						<xsl:with-param name="pArticleLPage" select="$lArticleLPage"/>
						<xsl:with-param name="pArticlePublisher" select="$lArticlePublisher"></xsl:with-param>						
						<xsl:with-param name="pArticleNode" select="$pCurrentArticle"></xsl:with-param>
					</xsl:call-template>
				</xsl:otherwise>
			</xsl:choose>
		</xsl:for-each>
	</xsl:template>
		
	
	<xsl:template name="singleTaxonTemplate">
		<xsl:param name="pTaxonNode" />
		<xsl:param name="pArticleDoi" />		
		<xsl:param name="pTaxonNum" />
		<xsl:param name="pScientificName" />
		<xsl:param name="pArticleAuthors" />		
		<xsl:param name="pJournalTitle" />
		<xsl:param name="pJournalUrlTitle" />
		<xsl:param name="pArticleTitle" />
		<xsl:param name="pArticlePubYear" />
		<xsl:param name="pArticleVolume" />
		<xsl:param name="pArticlePages" />
		<xsl:param name="pArticleFPage" />
		<xsl:param name="pArticleLPage" />
		<xsl:param name="pArticlePublisher" />		
		<xsl:param name="pArticleNode" />
		
		<xsl:variable name="lCurrentDate">
			<xsl:value-of select="php:function('date',  'Y-m-d\TH:i:s')"></xsl:value-of>
		</xsl:variable>
		<xsl:variable name="lTreatmentSecs" select="$pTaxonNode/tp:treatment-sec" />
		<xsl:variable name="lTreatmentFigs" select="$pTaxonNode//xref[@ref-type='fig'][count(./ancestor::*[name()='tp:taxon-treatment'])=(count($pTaxonNode/ancestor-or-self::*[name()='tp:taxon-treatment']))]" />
		<!-- Gledame citatite da sa samo v tekushtiq treatment, a ne v nqkoi child taxon-treatment -->
		<xsl:variable name="lTreatmentBibrCitations" select="$pTaxonNode//xref[@ref-type='bibr'][count(./ancestor::*[name()='tp:taxon-treatment'])=(count($pTaxonNode/ancestor-or-self::*[name()='tp:taxon-treatment']))]" />
		<xsl:variable name="lDescription" select="$pTaxonNode/description" />
		<xsl:variable name="lTaxonStatusNode" select="$pTaxonNode/tp:nomenclature/tp:taxon-status" />		
		<xsl:variable name="lTaxonName" select="$pTaxonNode/tp:nomenclature/tp:taxon-name" />		
		<xsl:variable name="lTaxonGenus">
			<xsl:call-template name="escapeSpecialSymbols">
				<xsl:with-param name="pText" select="$pTaxonNode/tp:nomenclature/tp:taxon-name/tp:taxon-name-part[@taxon-name-part-type='genus']"></xsl:with-param>
			</xsl:call-template>
		</xsl:variable>
		<xsl:variable name="lIdentifier"><xsl:value-of select="php:function('parseArticleDoi',  string($pArticleDoi))"></xsl:value-of>.sp_<xsl:value-of select="$pTaxonNum"></xsl:value-of></xsl:variable>
		<xsl:variable name="lTaxonStatus">			
			<xsl:variable name="lNormalizedStatus" select="normalize-space($lTaxonStatusNode)" />
			<xsl:call-template name="ToLower">
				<xsl:with-param name="inputString" select="$lNormalizedStatus"/>
			</xsl:call-template>
		</xsl:variable>	
		<xsl:variable name="lFigsXml">
			<xsl:choose>
				<xsl:when test="count($lTreatmentFigs) &gt; 0">
					<xsl:text>==Images==
</xsl:text>
					<xsl:variable name="lTemp" select="php:function('checkForDuplicateFigId', '0', '1')"></xsl:variable>
					<xsl:variable name="lFigsInnerXml">
						<xsl:for-each select="$lTreatmentFigs" >	
							<xsl:sort select="php:function('createIntFromString', string(@rid))" data-type="number"/>
							<xsl:variable name="lTest" select="php:function('checkForDuplicateFigId', string(./@rid), '0')"></xsl:variable>
							<xsl:if test="$lTest &gt; 0">
								<xsl:variable name="lCurrentXref" select="." />
								<xsl:variable name="lCurrentFig">
									<xsl:call-template name="taxonTreatmentFigTemplate">					
										<xsl:with-param name="pXrefToFigNode" select="$lCurrentXref"></xsl:with-param>
										<xsl:with-param name="pArticleNode" select="$pArticleNode"></xsl:with-param>						
										<xsl:with-param name="pFigNum" select="$lTest"></xsl:with-param>
									</xsl:call-template>
								</xsl:variable>
<xsl:text>
</xsl:text><xsl:value-of select="normalize-space($lCurrentFig)"></xsl:value-of>
							</xsl:if>
						</xsl:for-each>
					</xsl:variable>
					<xsl:text>{{Gallery | lines=5 | width=250</xsl:text>
					<xsl:value-of select="$lFigsInnerXml"></xsl:value-of>
					<xsl:text>
}}</xsl:text>
				</xsl:when>
			</xsl:choose>
		</xsl:variable>
		<xsl:variable name="lPublicationNotice">
			<xsl:call-template name="publicationNotice">				
				<xsl:with-param name="pPubYear" select="$pArticlePubYear"></xsl:with-param>
				<xsl:with-param name="pArticleTitle" select="$pArticleTitle"></xsl:with-param>
				<xsl:with-param name="pJournalTitle" select="$pJournalTitle"></xsl:with-param>
				<xsl:with-param name="pJournalUrlTitle" select="$pJournalUrlTitle"></xsl:with-param>
				<xsl:with-param name="pVolume" select="$pArticleVolume"></xsl:with-param>
				<xsl:with-param name="pPages" select="$pArticlePages"></xsl:with-param>
				<xsl:with-param name="pArticleFPage" select="$pArticleFPage" />
				<xsl:with-param name="pArticleLPage" select="$pArticleLPage"/>
				<xsl:with-param name="pPublisher" select="$pArticlePublisher"></xsl:with-param>
				<xsl:with-param name="pArticleDoi" select="$pArticleDoi"></xsl:with-param>
				<xsl:with-param name="pArticleAuthors" select="$pArticleAuthors"></xsl:with-param>
				<xsl:with-param name="pArticleId" select="php:function('getArticleIdFromDoi', string($pArticleDoi))"></xsl:with-param>
			</xsl:call-template>
		</xsl:variable>
		<xsl:variable name="lPageTitle">
			<xsl:call-template name="buildPageTitle">
				<xsl:with-param name="pTitle" select="normalize-space($pScientificName)"></xsl:with-param>
				<xsl:with-param name="pArticleNode" select="$pArticleNode"></xsl:with-param>
			</xsl:call-template>
		</xsl:variable>
		<xsl:text>
</xsl:text><xsl:element name="page">
			<title>
				<xsl:call-template name="replaceSymbolTemplate"><xsl:with-param name="text" select="normalize-space($lPageTitle)"></xsl:with-param><xsl:with-param name="searchSymbol"><xsl:text> </xsl:text></xsl:with-param><xsl:with-param name="replacementSymbol">_</xsl:with-param></xsl:call-template>
			</title>
			<revision>
				<id>TAXON_ID</id>
				<timestamp><xsl:value-of select="$lCurrentDate"/></timestamp>
				<contributor>
					<username>Pensoft Publishers</username>
					<id>308</id>
				</contributor>
				<minor/>
				<comment>Imported from <xsl:call-template name="escapeSpecialSymbols">
					<xsl:with-param name="pText" select="normalize-space($pJournalTitle)"></xsl:with-param>
				</xsl:call-template></comment>
				
				<text xml:space="preserve"><xsl:text>
{{TOC|right}}
</xsl:text>
<xsl:value-of select="$lPublicationNotice"></xsl:value-of>					
<xsl:call-template name="getTaxonNameDetails">
	<xsl:with-param name="pTaxonNode" select="$pTaxonNode"></xsl:with-param>
	<xsl:with-param name="pArticleAuthors" select="$pArticleAuthors"></xsl:with-param>
	<xsl:with-param name="pArticlePubYear" select="$pArticlePubYear"></xsl:with-param>
</xsl:call-template><xsl:for-each select="$lTreatmentSecs" ><xsl:variable name="lCurrentSec" select="." /><xsl:call-template name="taxonTreatmentSecTemplate">					
	<xsl:with-param name="pSecNode" select="$lCurrentSec"></xsl:with-param>
	<xsl:with-param name="pArticleDoi" select="$pArticleDoi"></xsl:with-param>						
	<xsl:with-param name="pIdentifier" select="$lIdentifier"></xsl:with-param>	
	<xsl:with-param name="pArticleAuthors" select="$pArticleAuthors"></xsl:with-param>
</xsl:call-template></xsl:for-each>					
<xsl:call-template name="getOriginalDescription">					
	<xsl:with-param name="pArticleTitle" select="$pArticleTitle"></xsl:with-param>
	<xsl:with-param name="pArticleVolume" select="$pArticleVolume"></xsl:with-param>
	<xsl:with-param name="pArticlePages" select="$pArticlePages"></xsl:with-param>
	<xsl:with-param name="pArticlePubYear" select="$pArticlePubYear"></xsl:with-param>
	<xsl:with-param name="pArticleDoi" select="$pArticleDoi"></xsl:with-param>
	<xsl:with-param name="pJournalTitle" select="$pJournalTitle"></xsl:with-param>
	<xsl:with-param name="pJournalUrlTitle" select="$pJournalUrlTitle"></xsl:with-param>
	<xsl:with-param name="pArticleAuthors" select="$pArticleAuthors"></xsl:with-param>
	<xsl:with-param name="pTaxonStatusNode" select="$lTaxonStatusNode"></xsl:with-param>
</xsl:call-template>
<xsl:call-template name="getOtherReferences">					
	<xsl:with-param name="pXrefs" select="$lTreatmentBibrCitations"></xsl:with-param>
	<xsl:with-param name="pArticleNode" select="$pArticleNode"></xsl:with-param>						
</xsl:call-template>
<xsl:value-of select="$lFigsXml"></xsl:value-of>
</text>
			</revision>
		</xsl:element>
	</xsl:template>
	
	<!-- Темплейт който връща метаинформацията за таксона
	
	linka se vzema ot

			http://www.pensoft.net/inc/journals/issueExport.php?title=ZooKeys&volume=84 
			
     	<articles>
     		<article id="774">
     			<abstract>http://www.pensoft.net/journals/zookeys/article/774/abstract/revision-of-the-south-american-window-fly-genus</abstract>

Winterton SL, Gaimari SD (2011) Revision of the South American window fly genus ''Heteromphrale'' Krober, 1937 (Diptera, Scenopinidae). ZooKeys 84: 11–20. doi: 10.3897/zookeys.84.774
    
 		SE POLUCHAVA PO SLEDNIYA NACHIN:
 		   
   	 article/article-meta/contrib-group>
                <contrib contrib-type="author" xlink:type="simple">
                    <name name-style="western">
                        <surname>Winterton</surname>
                        <given-names>Shaun L.</given-names>
                    </name>
										</contrib>
                <contrib contrib-type="author" xlink:type="simple">
                    <name name-style="western">
                        <surname>Gaimari</surname>
                        <given-names>Stephen D.</given-names>
                    </name>
										</contrib>
     </contrib-group>
     
     			BELEZHKA - <given-names>Shaun L.</given-names> na avtorite se sakrashtava na parva bukva, bez tochki i intervali.     
     
     article/article-meta/pub-date pub-type="epub">
                <year>2011</year>
     
     article/article-meta/article-meta>
						<title-group>
                <article-title>
                    Revision of the South American window fly genus <italic>Heteromphrale</italic> Kröber, 1937 (Diptera, Scenopinidae)
                </article-title>
                
      front/journal-meta>
            <journal-id journal-id-type="publisher-id">ZooKeys</journal-id>
            
      article/article-meta/issue>67</issue>
      article/article-meta/fpage>1</fpage>
      article/article-meta/lpage>9</lpage>
      
      doi: = front/article-meta/<article-id pub-id-type="doi">10.3897/zookeys.84.774</article-id>
	
	-->
	<xsl:template name="publicationNotice">		
		<xsl:param name="pPubYear"></xsl:param>
		<xsl:param name="pArticleTitle"></xsl:param>
		<xsl:param name="pJournalTitle"></xsl:param>
		<xsl:param name="pJournalUrlTitle"></xsl:param>
		<xsl:param name="pVolume"></xsl:param>
		<xsl:param name="pPages"></xsl:param>
		<xsl:param name="pArticleFPage" />
		<xsl:param name="pArticleLPage" />
		<xsl:param name="pPublisher"></xsl:param>
		<xsl:param name="pArticleDoi"></xsl:param>
		<xsl:param name="pArticleAuthors"></xsl:param>
		<xsl:param name="pArticleId"></xsl:param>
		
		<xsl:variable name="lArticleAuthors">
			<xsl:call-template name="getArticleAuthors">
				<xsl:with-param name="pAuthors" select="$pArticleAuthors"></xsl:with-param>
				<xsl:with-param name="pAbbreviated">0</xsl:with-param>
			</xsl:call-template>
		</xsl:variable>
		<xsl:variable name="lParsedJournalUrlTitle">
			<xsl:call-template name="escapeSpecialSymbols">
				<xsl:with-param name="pText" select="$pJournalUrlTitle"></xsl:with-param>				
			</xsl:call-template>
		</xsl:variable>
		<xsl:variable name="lArticleAuthorsAbbreviated">
			<xsl:call-template name="getArticleAuthors">
				<xsl:with-param name="pAuthors" select="$pArticleAuthors"></xsl:with-param>
				<xsl:with-param name="pAbbreviated">1</xsl:with-param>
				<xsl:with-param name="pNameJoiner"><xsl:text> </xsl:text></xsl:with-param>
			</xsl:call-template>
		</xsl:variable>
		<xsl:variable name="lArticleTitle">
			<xsl:call-template name="get_special_parse_node_text_template">
				<xsl:with-param name="pNode" select="$pArticleTitle"></xsl:with-param>
			</xsl:call-template>
		</xsl:variable>
		
		<xsl:text>{{Publication to wiki notice
 | author = </xsl:text><xsl:value-of select="normalize-space($lArticleAuthors)"></xsl:value-of><xsl:text>
 | author_abbreviated = </xsl:text><xsl:value-of select="normalize-space($lArticleAuthorsAbbreviated)"></xsl:value-of><xsl:text>
 | year = </xsl:text><xsl:value-of select="$pPubYear"></xsl:value-of><xsl:text>
 | title = </xsl:text><xsl:value-of select="normalize-space($lArticleTitle)" /><xsl:text>
 | journal = </xsl:text><xsl:call-template name="escapeSpecialSymbols">
					<xsl:with-param name="pText" select="normalize-space($pJournalTitle)"></xsl:with-param>
				</xsl:call-template><xsl:text>
 | volume = </xsl:text><xsl:value-of select="$pVolume"></xsl:value-of><xsl:text>
 | pages = </xsl:text><xsl:value-of select="$pArticleFPage" /><xsl:text>--</xsl:text><xsl:value-of select="$pArticleLPage" /><xsl:text>
 | doi = </xsl:text><xsl:call-template name="escapeSpecialSymbols">
					<xsl:with-param name="pText" select="normalize-space($pArticleDoi)"></xsl:with-param>
				</xsl:call-template><xsl:text>
 | citationurl = http://www.pensoft.net/journals/</xsl:text><xsl:value-of select="$lParsedJournalUrlTitle"/><xsl:text>/article/</xsl:text><xsl:value-of select="$pArticleId"></xsl:value-of><xsl:text>/citation/ 
 | url = http://www.pensoft.net/journals/</xsl:text><xsl:value-of select="$lParsedJournalUrlTitle"/><xsl:text>/article/</xsl:text><xsl:value-of select="$pArticleId"></xsl:value-of><xsl:text>/abstract
 | publisher = </xsl:text><xsl:call-template name="escapeSpecialSymbols">
					<xsl:with-param name="pText" select="normalize-space($pPublisher)"></xsl:with-param>
				</xsl:call-template><xsl:text>
 | publisherurl = http://www.pensoft.net/
}}</xsl:text>
	</xsl:template>
	
	<!-- Темплейт, който връща информацията за текущия таксон
		''Heteromphrale blanca'' - idva ot:
 
	<tp:taxon-treatment>
 	<tp:nomenclature>
 		<tp:taxon-name>
 			<tp:taxon-name-part taxon-name-part-type="genus">Heteromphrale</tp:taxon-name-part>
 			<tp:taxon-name-part taxon-name-part-type="species">blanca</tp:taxon-name-part>
 		</tp:taxon-name> 
 						

 						
{{aut|Winterton &amp; Gaimari, 2011}} - idva ot:

	<tp:taxon-treatment>
 	<tp:nomenclature>
 		<tp:taxon-authority>.....</tp:taxon-authority>
 		
 	Ako nyama takav tag se sabira ot:
 	
 	<contrib-group>
 			<contrib contrib-type="author" xlink:type="simple">
 				<name name-style="western">
 					<surname>Winterton</surname>
 				</name>
 			<contrib contrib-type="author" xlink:type="simple">
 				<name name-style="western">
 					<surname>Gaimari</surname>
 				</name>
 	</contrib-group>
 	
 	+
 	
 	 <pub-date pub-type="epub">
 	 		<year>2011</year>



[http://species.wikimedia.org/w/index.php?title=Heteromphrale_blanca Wikispecies link]

Vzema se ot i razdelitelya e "_"

<tp:taxon-treatment>
 	<tp:nomenclature>
 		<tp:taxon-name>
 			<tp:taxon-name-part taxon-name-part-type="genus">Heteromphrale</tp:taxon-name-part>
 			<tp:taxon-name-part taxon-name-part-type="species">blanca</tp:taxon-name-part>
 		</tp:taxon-name> 




Ako ima tag:

	<tp:taxon-treatment>
		<tp:nomenclature>
			<tp:taxon-name>
				<object-id xlink:type="simple">urn:lsid:zoobank.org:pub:xxxx</object-id> 
				
	togava se generira:
	 			
	[http://zoobank.org/?id=urn:lsid:zoobank.org:pub:xxxx ZooBank]
	-->
	<xsl:template name="getTaxonNameDetails">
		<xsl:param name="pTaxonNode" />
		<xsl:param name="pArticleAuthors" />
		<xsl:param name="pArticlePubYear" />
		
		<xsl:variable name="lTaxonNameNode" select="$pTaxonNode/tp:nomenclature/tp:taxon-name"></xsl:variable>
		<xsl:variable name="lTaxonName"><xsl:call-template name="get_node_text_template"><xsl:with-param name="pNode" select="$lTaxonNameNode" /><xsl:with-param name="pPutSpaces">1</xsl:with-param></xsl:call-template></xsl:variable>
		<xsl:variable name="lTaxonOrder"><xsl:call-template name="get_node_text_template"><xsl:with-param name="pNode" select="$lTaxonNameNode/tp:taxon-name-part[@taxon-name-part-type='order']" /><xsl:with-param name="pPutSpaces">1</xsl:with-param></xsl:call-template></xsl:variable>
		<xsl:variable name="lTaxonFamilia"><xsl:call-template name="get_node_text_template"><xsl:with-param name="pNode" select="$lTaxonNameNode/tp:taxon-name-part[@taxon-name-part-type='family']" /><xsl:with-param name="pPutSpaces">1</xsl:with-param></xsl:call-template></xsl:variable>
		<xsl:variable name="lTaxonGenus"><xsl:call-template name="get_node_text_template"><xsl:with-param name="pNode" select="$lTaxonNameNode/tp:taxon-name-part[@taxon-name-part-type='genus']" /><xsl:with-param name="pPutSpaces">1</xsl:with-param></xsl:call-template></xsl:variable>
		<xsl:variable name="lTaxonSubGenus"><xsl:call-template name="get_node_text_template"><xsl:with-param name="pNode" select="$lTaxonNameNode/tp:taxon-name-part[@taxon-name-part-type='subgenus']" /><xsl:with-param name="pPutSpaces">1</xsl:with-param></xsl:call-template></xsl:variable>
		<xsl:variable name="lTaxonSpecies"><xsl:call-template name="get_node_text_template"><xsl:with-param name="pNode" select="$lTaxonNameNode/tp:taxon-name-part[@taxon-name-part-type='species']" /><xsl:with-param name="pPutSpaces">1</xsl:with-param></xsl:call-template></xsl:variable>
		<xsl:variable name="lTaxonSubSpecies"><xsl:call-template name="get_node_text_template"><xsl:with-param name="pNode" select="$lTaxonNameNode/tp:taxon-name-part[@taxon-name-part-type='subsubspecies']" /><xsl:with-param name="pPutSpaces">1</xsl:with-param></xsl:call-template></xsl:variable>
		<xsl:variable name="lParsedTaxonName"><xsl:call-template name="replaceSymbolTemplate"><xsl:with-param name="text" select="normalize-space($lTaxonName)"></xsl:with-param><xsl:with-param name="searchSymbol"><xsl:text> </xsl:text></xsl:with-param><xsl:with-param name="replacementSymbol">_</xsl:with-param></xsl:call-template></xsl:variable>
		
		<xsl:variable name="lTaxonStatus"><xsl:call-template name="get_node_text_template"><xsl:with-param name="pNode" select="$pTaxonNode/tp:nomenclature/tp:taxon-status" /><xsl:with-param name="pPutSpaces">1</xsl:with-param></xsl:call-template></xsl:variable>
		<xsl:variable name="lTaxonAuthority">
			<xsl:call-template name="escapeSpecialSymbols">
				<xsl:with-param name="pText" select="$pTaxonNode/tp:nomenclature/tp:taxon-authority"></xsl:with-param>
			</xsl:call-template>
		</xsl:variable>
		<xsl:variable name="lTaxonObjectId">
			<xsl:call-template name="escapeSpecialSymbols">
				<xsl:with-param name="pText" select="$pTaxonNode/tp:nomenclature/tp:taxon-name/object-id"></xsl:with-param>
			</xsl:call-template>
		</xsl:variable>
		<xsl:variable name="lNomenclatureCitations" select="$pTaxonNode//tp:nomenclature-citation"></xsl:variable>
		<xsl:variable name="lArticleAuthors">
			<xsl:call-template name="getArticleAuthors">
				<xsl:with-param name="pAuthors" select="$pArticleAuthors"></xsl:with-param>
				<xsl:with-param name="pDisplayFirstName">0</xsl:with-param>
				<xsl:with-param name="pSeparator"> &amp; </xsl:with-param>
			</xsl:call-template>
		</xsl:variable>
		
		<xsl:variable name="lTaxonAuthors">
			<xsl:choose>
				<xsl:when test="$lTaxonAuthority != ''"><xsl:value-of select="$lTaxonAuthority" /></xsl:when>
				<xsl:otherwise><xsl:value-of select="normalize-space($lArticleAuthors)" /><xsl:text>, </xsl:text><xsl:value-of select="normalize-space($pArticlePubYear)" /></xsl:otherwise>
			</xsl:choose>
		</xsl:variable>
		<xsl:text>
{{Treatment start
 | Ordo = </xsl:text><xsl:value-of select="normalize-space($lTaxonOrder)" /><xsl:text>
 | Familia = </xsl:text><xsl:value-of select="normalize-space($lTaxonFamilia)" /><xsl:text>
 | Genus = </xsl:text><xsl:value-of select="normalize-space($lTaxonGenus)" />
 <xsl:if test="count($lTaxonNameNode/tp:taxon-name-part[@taxon-name-part-type='subgenus']) &gt; 0">
	<xsl:text>
| Subgenus = </xsl:text><xsl:value-of select="normalize-space($lTaxonSubGenus)" />
</xsl:if>
<xsl:text>
 | Specific name = </xsl:text><xsl:value-of select="normalize-space($lTaxonSpecies)" /><xsl:text>
 | Infraspecific name = </xsl:text><xsl:value-of select="normalize-space($lTaxonSubSpecies)" /><xsl:text>
 | Taxon rank = 	
 | Taxon authority = </xsl:text><xsl:value-of select="normalize-space($lTaxonAuthors)" /><xsl:text>
 | Taxon status = </xsl:text><xsl:value-of select="normalize-space($lTaxonStatus)" />
 <xsl:if test="count($lNomenclatureCitations) &gt; 0"><xsl:text>
 | Nomenclature citation =</xsl:text></xsl:if>
 <xsl:for-each select="$lNomenclatureCitations">
		<xsl:variable name="lCurrentCitationText">
			<xsl:call-template name="get_special_parse_node_text_template">
				<xsl:with-param name="pNode" select="."></xsl:with-param>
			</xsl:call-template>
		</xsl:variable>
		<xsl:text> {{Nomenclature citation |</xsl:text><xsl:value-of select="php:function('trim', string($lCurrentCitationText))"></xsl:value-of><xsl:text>}}</xsl:text>
</xsl:for-each>
<xsl:text>
 | Wikispecies page name = </xsl:text><xsl:value-of select="normalize-space($lParsedTaxonName)" />
 <xsl:if test="$lTaxonObjectId != ''">
 <xsl:text>
 | ZooBank ID = </xsl:text><xsl:value-of select="$lTaxonObjectId" />
 </xsl:if>
 <xsl:text>
 | Pensoft Profile = </xsl:text><xsl:value-of select="php:function('preg_replace', '/\s/', '_', normalize-space($lTaxonName))" /><xsl:text>
}}</xsl:text>
</xsl:template>
	
	<!-- 
		Темплейт, който стринг с имената на авторите.
		Ако е подаден pAbbreviated - първото име се съкращава до 1ва буква
		pSeparator - стринг между авторите
	-->
	<xsl:template name="getArticleAuthors">
		<xsl:param name="pAuthors"></xsl:param>
		<xsl:param name="pAbbreviated">0</xsl:param>
		<xsl:param name="pDisplayFirstName">1</xsl:param>
		<xsl:param name="pNameJoiner">, </xsl:param>
		<xsl:param name="pSeparator" select="' AND '" />
		<xsl:param name="pGivenNamesFirst">0</xsl:param>
		
		<xsl:for-each select="$pAuthors">
			<xsl:variable name="lCurrentAuthor" select="."></xsl:variable>
			<xsl:variable name="lFirstName">
				<xsl:call-template name="escapeSpecialSymbols">
					<xsl:with-param name="pText" select="$lCurrentAuthor//given-names"></xsl:with-param>
				</xsl:call-template>
			</xsl:variable>
			<xsl:variable name="lLastName">
				<xsl:call-template name="escapeSpecialSymbols">
					<xsl:with-param name="pText" select="$lCurrentAuthor//surname"></xsl:with-param>
				</xsl:call-template>
			</xsl:variable>
			<xsl:choose>
				<xsl:when test="$pGivenNamesFirst &gt; 0 and $pDisplayFirstName &gt; 0">
					<xsl:choose>
						<xsl:when test="$pAbbreviated &gt; 0"><xsl:value-of select="substring($lFirstName, 1, 1)" /></xsl:when>
						<xsl:otherwise><xsl:value-of select="$lFirstName" /></xsl:otherwise>
					</xsl:choose>
					<xsl:value-of select="$pNameJoiner" />
					<xsl:value-of select="$lLastName"/>						
					<xsl:if test="position() &lt; last()"><xsl:value-of select="$pSeparator"></xsl:value-of></xsl:if>
				</xsl:when>
				<xsl:otherwise>
					<xsl:value-of select="$lLastName"/><xsl:if test="$pDisplayFirstName &gt; 0"><xsl:value-of select="$pNameJoiner" />
						<xsl:choose>
							<xsl:when test="$pAbbreviated &gt; 0"><xsl:value-of select="substring($lFirstName, 1, 1)" /></xsl:when>
							<xsl:otherwise><xsl:value-of select="$lFirstName" /></xsl:otherwise>
						</xsl:choose>
					</xsl:if>
					<xsl:if test="position() &lt; last()"><xsl:value-of select="$pSeparator"></xsl:value-of></xsl:if>
				</xsl:otherwise>
			</xsl:choose>
		</xsl:for-each>
	</xsl:template>
	
	<!-- 
		Темплейт, който връща xml-a за дадена секция
		Не се показва key секцията
	-->
	<xsl:template name="taxonTreatmentSecTemplate">
		<xsl:param name="pSecNode" />
		<xsl:param name="pArticleDoi" />				
		<xsl:param name="pIdentifier" />		
		<xsl:param name="pArticleAuthors" />
						
						<xsl:variable name="lSecTitle">
							<xsl:call-template name="get_special_parse_node_text_template">
								<xsl:with-param name="pNode" select="$pSecNode/title"></xsl:with-param>
								<xsl:with-param name="pDontIgnoreLabels">1</xsl:with-param>
							</xsl:call-template>
						</xsl:variable>	
						<xsl:variable name="lSecContent">
							<xsl:call-template name="get_sec_node_text_template">
									<xsl:with-param name="pNode" select="$pSecNode"/>
									<xsl:with-param name="pPutSpaces">0</xsl:with-param>
									<xsl:with-param name="pArticleAuthors" select="$pArticleAuthors"></xsl:with-param>
							</xsl:call-template>
						</xsl:variable>
						<xsl:text>

==</xsl:text><xsl:value-of select="php:function('parseMediaWikiSecTitle', normalize-space($lSecTitle))"></xsl:value-of><xsl:text>==
</xsl:text><xsl:value-of select="php:function('parseMediaWikiSecContent', string($lSecContent))" />
			
	</xsl:template>
	
	<!-- 
		Темплейт, който връща xml-a за картинките за текущия таксон		
	-->
	<xsl:template name="taxonTreatmentFigTemplate">
		<xsl:param name="pXrefToFigNode" />
		<xsl:param name="pArticleNode" />	
		<xsl:param name="pFigNum"/>
		<xsl:variable name="lFigId" select="$pXrefToFigNode/@rid"></xsl:variable>
		<xsl:variable name="lFigNode" select="$pArticleNode//fig[@id=$lFigId]"></xsl:variable>
		<xsl:variable name="lPicFileName">
			<xsl:call-template name="escapeSpecialSymbols">
				<xsl:with-param name="pText" select="$lFigNode//graphic/@xlink:href"></xsl:with-param>
			</xsl:call-template>
		</xsl:variable>
		<xsl:variable name="lFigLabel">
			<xsl:call-template name="escapeSpecialSymbols">
				<xsl:with-param name="pText" select="normalize-space($lFigNode/label)"></xsl:with-param>
			</xsl:call-template>
		</xsl:variable>
		<xsl:variable name="lFigCaption" select="$lFigNode/caption"></xsl:variable>		
		<xsl:variable name='lFigDescription'>
			<xsl:call-template name="get_special_parse_node_text_template">
				<xsl:with-param name="pNode" select="$lFigCaption"></xsl:with-param>
			</xsl:call-template>
		</xsl:variable>
		<xsl:variable name='lFigPicUrl'>
			<xsl:value-of select="$lPicFileName"></xsl:value-of>
		</xsl:variable>
		<xsl:text>|</xsl:text><xsl:value-of select="number($pFigNum * 2 - 1)"/><xsl:text>= File:</xsl:text><xsl:value-of select="$lPicFileName"></xsl:value-of><xsl:text>|</xsl:text><xsl:value-of select="number($pFigNum * 2)"/><xsl:text>= '''</xsl:text><xsl:value-of select="$lFigLabel"></xsl:value-of>''' <xsl:value-of select="$lFigDescription"></xsl:value-of>
	</xsl:template>
	
	<!-- 
		Темплейт, който връща xml-a за bibr цитатите
		
		Referencite tryabva da se convertirat po slednata chema kato v tozi primer:
<ref id="B4">
		<mixed-citation xlink:type="simple">
				<person-group>   					//<person-group> SAMO predi <year> se convertira kato {{aut|Hardy DE}}//
						<name name-style="western">
							<surname>Hardy</surname>
							<given-names>DE</given-names>
						</name>
				</person-group> (
				<year>1966</year>)
				<article-title> Family Scenopinidae (Omphralidae)</article-title>. In: 
				<person-group>
					<name name-style="western">
						<surname>Papavero</surname>
						<given-names>N</given-names>
					</name>
				</person-group> (
				<role>Ed</role>) 
				<source>A Catalogue of the Diptera of the Americas south of the United States</source>. <publisher-name>Departamento de Zoologia, Secretaria da Agricultura</publisher-name>, 
				<publisher-loc>São Paulo</publisher-loc>, 
				<fpage>32.1</fpage>–
				<lpage>32.5</lpage>
				<ext-link ext-link-type="uri" xlink:href="http://www.mapress.com/zootaxa/2008/f/zt01908p067.pdf" xlink:type="simple">http://www.mapress.com/zootaxa/2008/f/zt01908p067.pdf</ext-link>.   	//AKO ima <ext-link> se pravi taka: [http://www.mapress.com/zootaxa/2008/f/zt01908p067.pdf http://www.mapress.com/zootaxa/2008/f/zt01908p067.pdf]//
		</mixed-citation>  				//Vsichko ostanalo v rakite na <ref> se striptag-va//
</ref>

za da se poluchi:

* {{aut|Hardy DE}} (1966)  Family Scenopinidae (Omphralidae). In: Papavero N (Ed) A Catalogue of the Diptera of the Americas south of the United States, Departamento de Zoologia, Secretaria da Agricultura, São Paulo: 32.1-32.5 [http://www.mapress.com/zootaxa/2008/f/zt01908p067.pdf http://www.mapress.com/zootaxa/2008/f/zt01908p067.pdf].
	-->
	<xsl:template name="getOtherReferences">
		<xsl:param name="pXrefs" />
		<xsl:param name="pArticleNode" />
		
		<xsl:if test="count($pXrefs) &gt; 0">
			<xsl:text>
==Other References==

&lt;references /&gt;
</xsl:text>	</xsl:if>
	</xsl:template>
	
	
	<!-- 
		Темплейт, който връща текстовото съдържание на подадения mixed-citation node, за да отговаря на горното условие
	-->	
	<xsl:template name="otherReferenceTemplate">
		<xsl:param name="pNode" />
		<xsl:param name="pPutSpaces"></xsl:param>
		
		<xsl:variable name="lLocalName" >
			<xsl:variable name="lTempName" select="local-name($pNode)"></xsl:variable>
			<xsl:call-template name="ToLower">
				<xsl:with-param name="inputString" select="$lTempName"/>
			</xsl:call-template>
		</xsl:variable>
		<xsl:variable name="lTextType" select="$pNode/self::text()" />
		<xsl:variable name="lElementType" select="$pNode/self::*" />		
		<xsl:choose>
			<xsl:when test="$lTextType">
				<xsl:choose>
					<xsl:when test="string($pNode) != '' and php:function('trim', string($pNode)) = ''">
						<xsl:text> </xsl:text>
					</xsl:when>
					<xsl:otherwise>
						<xsl:value-of select="$pNode"/>
						<xsl:if test="$pPutSpaces &gt; 0"><xsl:text> </xsl:text></xsl:if>
					</xsl:otherwise>
				</xsl:choose>						
			</xsl:when>
			<xsl:when test="$lElementType">
				<xsl:variable name="lNodeContent">
					<xsl:call-template name="otherReferenceElementTemplate">	
						<xsl:with-param name="pNode" select="$pNode"></xsl:with-param>
						<xsl:with-param name="pPutSpaces" select="$pPutSpaces"></xsl:with-param>
					</xsl:call-template>
				</xsl:variable>
				<xsl:choose>
					<xsl:when test="$lLocalName='ext-link'">
						<xsl:choose>
							<xsl:when test="$pNode/@ext-link-type='uri'">
								<xsl:variable name="lLinkHref" select="$pNode/@xlink:href"></xsl:variable>
								<xsl:text>[</xsl:text><xsl:value-of select="$lLinkHref"/><xsl:text> </xsl:text>
								<xsl:value-of select="$lNodeContent"></xsl:value-of>
								<xsl:text>]</xsl:text>
							</xsl:when>
							<xsl:otherwise>
								<xsl:value-of select="$lNodeContent"></xsl:value-of>
							</xsl:otherwise>
						</xsl:choose>
					</xsl:when>
					<xsl:when test="$lLocalName='person-group' and count($pNode/following-sibling::*[name()='year']) &gt; 0">
						<xsl:variable name="lRefAuthors" select="$pNode//name"></xsl:variable>
						<xsl:for-each select="$lRefAuthors">
							<xsl:variable name="lFirstName" select=".//given-names"></xsl:variable>
							<xsl:variable name="lLastName" select=".//surname"></xsl:variable>
							<xsl:text>{{aut|</xsl:text><xsl:value-of select="$lLastName"/><xsl:text> </xsl:text><xsl:value-of select="substring($lFirstName, 1, 1)" /><xsl:text>}}</xsl:text>
							<xsl:if test="position() &lt; last()"><xsl:text>, </xsl:text></xsl:if>
						</xsl:for-each>
					</xsl:when>					
					<xsl:when test="$lLocalName='person-group'">
						<xsl:variable name="lRefAuthors" select="$pNode//name"></xsl:variable>
						<xsl:for-each select="$lRefAuthors">
							<xsl:variable name="lFirstName" select=".//given-names"></xsl:variable>
							<xsl:variable name="lLastName" select=".//surname"></xsl:variable>
							<xsl:text></xsl:text><xsl:value-of select="$lLastName"/><xsl:text> </xsl:text><xsl:value-of select="substring($lFirstName, 1, 1)" /><xsl:text></xsl:text>
							<xsl:if test="position() &lt; last()"><xsl:text> </xsl:text></xsl:if>
						</xsl:for-each>
					</xsl:when>				
					<xsl:otherwise>
						<xsl:if test="$lLocalName='taxon-name'">
							<xsl:text>{{Taxon name|</xsl:text>
						</xsl:if>
						<xsl:if test="$lLocalName='bold'">
							<xsl:text>'''</xsl:text>
						</xsl:if>
						<xsl:if test="$lLocalName='italic'">
							<xsl:text>''</xsl:text>
						</xsl:if>
						<xsl:value-of select="$lNodeContent"></xsl:value-of>
						<xsl:if test="$lLocalName='taxon-name'">
							<xsl:text>}}</xsl:text>
						</xsl:if>						
						<xsl:if test="$lLocalName='bold'">
							<xsl:text>'''</xsl:text>
						</xsl:if>
						<xsl:if test="$lLocalName='italic'">
							<xsl:text>''</xsl:text>
						</xsl:if>
					</xsl:otherwise>
				</xsl:choose>
			</xsl:when>
		</xsl:choose>
	</xsl:template>
	
	
	
	<!-- 
		Темплейт, който вика темплейта otherReferenceTemplate за всички деца на подадения node
	-->
	<xsl:template name="otherReferenceElementTemplate">
		<xsl:param name="pNode" />		
		<xsl:param name="pPutSpaces" />		
		<xsl:for-each select="$pNode/child::node()" >
			<xsl:variable name="lCurrentNode" select="." />
			<xsl:call-template name="otherReferenceTemplate">	
				<xsl:with-param name="pNode" select="$lCurrentNode"></xsl:with-param>
				<xsl:with-param name="pPutSpaces" select="$pPutSpaces"></xsl:with-param>
			</xsl:call-template>
		</xsl:for-each>
	</xsl:template>
	
	<!-- Тук ще се показва секцията key
	
	==Key==
{{Key Start
| title = ''Heteromphrale''
| description =
| flags = jkey-hidekeymetadata 
| taxon name = ''Heteromphrale''
| common names =
| taxon rank = Genus
| parent taxon =
| parent key =
| parent key text =
| geoscope = global
| category =
| source =
| audience =
| status =
| creators = S. Winterton & S. Gaimari
}}

{{Lead | 1   | Scutum with glabrous, glossy dorsocentral area (circular in male, linear in female) (Fig. 5C, D); basal antennal flagellomere abruptly pear-shaped; mouthparts tiny, much smaller than oral cavity; female frons with extensive pile (Fig. 4C); abdomen distinctly matte white with brown suffusion laterally and ventrally (Fig. 7A, B), transverse brown line anterior to dark brown spot encompassing tergite 2 sensory patch 
       | result text = ''H. chilensis'' (Krober)}}
{{Lead | 1-  | Scutum with uniform covering of pubescence, lacking a glabrous or glossy mark (Fig. 5A, B, E, F); basal antennal flagellomere more conical-shaped, tapering evenly; mouthparts usually normal-sized, nearly filling oral cavity; female frons with less extensive pile; abdomen in both sexes more extensively dark brown, with white only on posterior margins of tergites (Figs 6A–B, 8A–B)  
       | 2}}
{{Lead | 2   | Wing with vein R4 diverging from vein R5 at point in basal quarter of cell r5 (Fig. 8B); tergite 2 sensory patch as relatively small single patch, slightly narrowed; male epandrium enlarged, bulbous, without distal fringe of long, white setae on posterior edge (Figs 3A–B, 8A, 10C); female sternite 8 shallowly emarginate posteriorly and without a fringe of long setae; acanthophorite spines stout (Fig. 9G–H) 
       | result text = ''H. cyanops'' (Edwards)}}
{{Lead | 2-  | Wing with vein R4 diverging from vein R5 at point between one-quarter and one-half of cell r5 (Figs 6A, B); tergite 2 sensory patch large and distinct, divided into two small patches with setae directed medially; male epandrium not bulbous, size subequal to preceding abdominal segment, with distal fringe of long white setae on posterior edge (Fig. 2A, B, 10A); female sternite 8 distally rounded with dense long thin setae apicolaterally and distally; acanthophorite spines thin and wispy (Fig. 9A–C)  
       | result text = ''H. blanca'' sp. n.}}
{{Key End}}
	Vzema se ot tag-a <table-wrap content-type="key" position="anchor" orientation="portrait">

<tp:treatment-sec sec-type="Key to Heteromphrale species"><title>Key to <tp:taxon-name>Heteromphrale</tp:taxon-name> species</title>
                    <table-wrap content-type="key" position="anchor" orientation="portrait">
                        <table>
                            <tbody>
                                <tr>
                                    <td rowspan="1" colspan="1">1</td>
                                    <td rowspan="1" colspan="1">Scutum with glabrous, glossy dorsocentral area (circular in male, linear in female) (<xref ref-type="fig" rid="F5">Fig. 5C, D</xref>); basal antennal flagellomere abruptly pear-shaped; mouthparts tiny, much smaller than oral cavity; female frons with extensive pile (<xref ref-type="fig" rid="F4">Fig. 4C</xref>); abdomen distinctly matte white with brown suffusion laterally and ventrally (<xref ref-type="fig" rid="F7">Fig. 7A, B</xref>), transverse brown line anterior to dark brown spot encompassing tergite 2 sensory patch</td>
                                    <td rowspan="1" colspan="1"><tp:taxon-name>Heteromphrale chilensis</tp:taxon-name> (Kröber)</td>
                                </tr>
                                <tr>
                                    <td rowspan="1" colspan="1">–</td>
                                    <td rowspan="1" colspan="1">Scutum with uniform covering of pubescence, lacking a glabrous or glossy mark (<xref ref-type="fig" rid="F5">Fig. 5A, B, E, F</xref>); basal antennal flagellomere more conical-shaped, tapering evenly; mouthparts usually normal-sized, nearly filling oral cavity; female frons with less extensive pile; abdomen in both sexes more extensively dark brown, with white only on posterior margins of tergites (<xref ref-type="fig" rid="F6">Figs 6A–B</xref>, <xref ref-type="fig" rid="F8">8A–B</xref>)</td>
                                    <td rowspan="1" colspan="1">2</td>
                                </tr>
                                <tr>
                                    <td rowspan="1" colspan="1">2</td>
                                    <td rowspan="1" colspan="1">Wing with vein R4 diverging from vein R5 at point in basal quarter of cell r5 (<xref ref-type="fig" rid="F8">Fig. 8B</xref>); tergite 2 sensory patch as relatively small single patch, slightly narrowed; male epandrium enlarged, bulbous, without distal fringe of long, white setae on posterior edge (<xref ref-type="fig" rid="F3">Figs 3A–B</xref>, <xref ref-type="fig" rid="F8">8A</xref>, <xref ref-type="fig" rid="F10">10C</xref>); female sternite 8 shallowly emarginate posteriorly and without a fringe of long setae; acanthophorite spines stout (<xref ref-type="fig" rid="F9">Fig. 9G–H</xref>)</td>
                                    <td rowspan="1" colspan="1"><tp:taxon-name>Heteromphrale cyanops</tp:taxon-name> (Edwards)</td>
                                </tr>
                                <tr>
                                    <td rowspan="1" colspan="1">–</td>
                                    <td rowspan="1" colspan="1">Wing with vein R4 diverging from vein R5 at point between one-quarter and one-half of cell r5 (<xref ref-type="fig" rid="F6">Figs 6A, B</xref>); tergite 2 sensory patch large and distinct, divided into two small patches with setae directed medially; male epandrium not bulbous, size subequal to preceding abdominal segment, with distal fringe of long white setae on posterior edge (<xref ref-type="fig" rid="F2">Fig. 2A, B</xref>, <xref ref-type="fig" rid="F10">10A</xref>); female sternite 8 distally rounded with dense long thin setae apicolaterally and distally; acanthophorite spines thin and wispy (<xref ref-type="fig" rid="F9">Fig. 9A–C</xref>)</td>
                                    <td rowspan="1" colspan="1"><tp:taxon-name>Heteromphrale blanca</tp:taxon-name> sp. n.</td>
                                </tr>
                            </tbody>
                        </table>
                    </table-wrap>
                </tp:treatment-sec> 
	
	-->
	<xsl:template name="getTaxonKey">
		<xsl:param name="pSecNode" />
		<xsl:param name="pArticleAuthors" />	
		
		<xsl:variable name="lTableNode" select="$pSecNode/table-wrap[@content-type='key']"></xsl:variable>
		<xsl:variable name="lTaxonName" select="normalize-space($pSecNode/title/tp:taxon-name)"></xsl:variable>
		<xsl:variable name="lSecTitle">
			<xsl:call-template name="get_special_parse_node_text_template">
				<xsl:with-param name="pNode" select="$pSecNode/title"></xsl:with-param>
				<xsl:with-param name="pDontIgnoreLabels">1</xsl:with-param>
			</xsl:call-template>
		</xsl:variable>	
		<xsl:variable name="lAuthors">
			<xsl:call-template name="getArticleAuthors">
				<xsl:with-param name="pAuthors" select="$pArticleAuthors"></xsl:with-param>
				<xsl:with-param name="pAbbreviated">1</xsl:with-param>
				<xsl:with-param name="pSeparator"> &amp; </xsl:with-param>
				<xsl:with-param name="pGivenNamesFirst">1</xsl:with-param>
				<xsl:with-param name="pNameJoiner">. </xsl:with-param>
			</xsl:call-template>
		</xsl:variable>
		<xsl:variable name="lTableRows" select="$lTableNode//tr"></xsl:variable>
		<xsl:text>
		
{{Key Start
| title = </xsl:text><xsl:value-of select="php:function('parseMediaWikiSecTitle', normalize-space($lSecTitle))" /><xsl:text>
| description =
| flags = jkey-hidekeymetadata 
| taxon name = 
| common names =
| taxon rank = 
| parent taxon =
| parent key =
| parent key text =
| geoscope = 
| category =
| source =
| audience =
| status =
| creators = </xsl:text><xsl:value-of select="normalize-space($lAuthors)" /><xsl:text>
}}		
</xsl:text>
		<xsl:for-each select="$lTableRows">
			<xsl:variable name="lCurrentRow" select="."></xsl:variable>
			<xsl:variable name="lNextRowIdx" select="number(position() + 1)"></xsl:variable>			
			<xsl:variable name="lLastRow">
				<xsl:choose>
					<xsl:when test="position() &lt; last()">0</xsl:when>
					<xsl:otherwise>1</xsl:otherwise>
				</xsl:choose>
			</xsl:variable>
			<xsl:variable name="lNextRowContent">
				<xsl:if test="position() &lt; last()">
					<xsl:call-template name="get_key_parse_node_text_template">
						<xsl:with-param name="pNode" select="$lTableRows[$lNextRowIdx]"></xsl:with-param>
					</xsl:call-template>
				</xsl:if>
			</xsl:variable>
			<xsl:variable name="lNextRowGraphicContent">
				<xsl:if test="position() &lt; last() and count($lTableRows[$lNextRowIdx]/td[2]/graphic) &gt; 0">
					<xsl:call-template name="get_key_parse_node_text_template">
						<xsl:with-param name="pNode" select="$lTableRows[$lNextRowIdx]/td[2]/graphic"></xsl:with-param>
					</xsl:call-template>
				</xsl:if>
			</xsl:variable>
			<xsl:variable name="lCurrentRowContent">
				<xsl:call-template name="get_key_parse_node_text_template">
					<xsl:with-param name="pNode" select="$lCurrentRow"></xsl:with-param>
				</xsl:call-template>
			</xsl:variable>
			<xsl:variable name="lCurrentRowGraphicContent">
				<xsl:if test="count($lCurrentRow/td[2]/graphic) &gt; 0">
					<xsl:call-template name="get_key_parse_node_text_template">
						<xsl:with-param name="pNode" select="$lCurrentRow/td[2]/graphic"></xsl:with-param>
					</xsl:call-template>
				</xsl:if>
			</xsl:variable>
			<xsl:choose>
				<xsl:when test="position() &gt; 1 and normalize-space($lCurrentRowContent) = normalize-space($lCurrentRowGraphicContent)"></xsl:when>
				<xsl:otherwise>
					<xsl:variable name="lRowCells" select="$lCurrentRow/td"></xsl:variable>
					<xsl:text>{{Lead</xsl:text>
					<xsl:for-each select="$lRowCells">
						<xsl:choose>
							<xsl:when test="position() = last()">
								<xsl:text>
	| result text = </xsl:text>								
							</xsl:when>
							<xsl:otherwise>
								<xsl:text> | </xsl:text>
							</xsl:otherwise>
						</xsl:choose>
						<xsl:variable name="lCellContent">
							<xsl:call-template name="get_key_parse_node_text_template">
								<xsl:with-param name="pNode" select="."></xsl:with-param>
							</xsl:call-template>
						</xsl:variable>
						<xsl:value-of select="php:function('trim', string($lCellContent))" /><xsl:value-of select="$lNextRowGraphicContent"></xsl:value-of><xsl:if test="$lLastRow = 0 and normalize-space($lNextRowContent) = normalize-space(lNextRowGraphicContent)"><xsl:value-of select="php:function('trim', string($lNextRowGraphicContent))" /></xsl:if>
					</xsl:for-each>
					<xsl:text>}}
</xsl:text>
				</xsl:otherwise>
			</xsl:choose>
		</xsl:for-each>
		<xsl:text>{{Key End}}</xsl:text>
	</xsl:template>
	
	<!-- В този темплейт ще показваме общото описание
		* {{aut|Winterton, SL}}; {{aut|Gaimari, SD}}; 2011: Revision of the South American window fly genus ''Heteromphrale'' Krober, 1937 (Diptera, Scenopinidae). [http://www.pensoft.net/journals/zookeys/ ''ZooKeys'',] '''XX''': XX-XX. {{doi|10.3897/zookeys.XX.774}} [http://zoobank.org/?lsid=xxx ZooBank]
		
		Winterton SL, Gaimari SD (2011) Revision of the South American window fly genus ''Heteromphrale'' Krober, 1937 (Diptera, Scenopinidae). ZooKeys 84: 11–20. doi: 10.3897/zookeys.84.774
    
 		SE POLUCHAVA PO SLEDNIYA NACHIN:
 		   
   	 article/article-meta/contrib-group>
                <contrib contrib-type="author" xlink:type="simple">
                    <name name-style="western">
                        <surname>Winterton</surname>
                        <given-names>Shaun L.</given-names>
                    </name>
										</contrib>
                <contrib contrib-type="author" xlink:type="simple">
                    <name name-style="western">
                        <surname>Gaimari</surname>
                        <given-names>Stephen D.</given-names>
                    </name>
										</contrib>
     </contrib-group>
     
     			BELEZHKA - <given-names>Shaun L.</given-names> na avtorite se sakrashtava na parva bukva, bez tochki i intervali.     
     
     article/article-meta/pub-date pub-type="epub">
                <year>2011</year>
     
     article/article-meta/article-meta>
						<title-group>
                <article-title>
                    Revision of the South American window fly genus <italic>Heteromphrale</italic> Kröber, 1937 (Diptera, Scenopinidae)
                </article-title>
                
      front/journal-meta>
            <journal-id journal-id-type="publisher-id">ZooKeys</journal-id>
            
      article/article-meta/issue>67</issue>
      article/article-meta/fpage>1</fpage>
      article/article-meta/lpage>9</lpage>
      
      doi: = front/article-meta/<article-id pub-id-type="doi">10.3897/zookeys.84.774</article-id>
	-->
	<xsl:template name="getOriginalDescription">
		<xsl:param name="pArticleTitle" />
		<xsl:param name="pArticleVolume" />
		<xsl:param name="pArticlePages" />
		<xsl:param name="pArticlePubYear" />
		<xsl:param name="pArticleDoi" />
		<xsl:param name="pJournalTitle" />
		<xsl:param name="pJournalUrlTitle" />
		<xsl:param name="pArticleAuthors" />
		<xsl:param name="pTaxonStatusNode" />
		
		<xsl:variable name="lTaxonStatus" select="normalize-space($pTaxonStatusNode)"></xsl:variable>
		<xsl:variable name="lArticleDoi">
			<xsl:call-template name="escapeSpecialSymbols">
				<xsl:with-param name="pText" select="$pArticleDoi"></xsl:with-param>
			</xsl:call-template>
		</xsl:variable>
		<xsl:variable name="lJournalTitle">
			<xsl:call-template name="escapeSpecialSymbols">
				<xsl:with-param name="pText" select="$pJournalTitle"></xsl:with-param>
			</xsl:call-template>
		</xsl:variable>
		<xsl:variable name="lJournalUrlTitle">
			<xsl:call-template name="escapeSpecialSymbols">
				<xsl:with-param name="pText" select="$pJournalUrlTitle"></xsl:with-param>
			</xsl:call-template>
		</xsl:variable>
		<xsl:variable name="lParsedArticleTitle">
			<xsl:call-template name="get_special_parse_node_text_template">
				<xsl:with-param name="pNode" select="$pArticleTitle" />
			</xsl:call-template>
		</xsl:variable>
		<xsl:text>
==</xsl:text>
		<xsl:choose>
			<xsl:when test="$lTaxonStatus='sp. n.' or $lTaxonStatus='sp. nov.' or $lTaxonStatus='gen. n.' or $lTaxonStatus='gen. nov.'">
				<xsl:text>Original Description</xsl:text>
			</xsl:when>
			<xsl:otherwise>
				<xsl:text>Taxon Treatment</xsl:text>
			</xsl:otherwise>
		</xsl:choose>
		<xsl:text>==
*</xsl:text>
		<xsl:for-each select="$pArticleAuthors">
			<xsl:variable name="lFirstName">
				<xsl:call-template name="escapeSpecialSymbols">
					<xsl:with-param name="pText" select=".//given-names"></xsl:with-param>
				</xsl:call-template>
			</xsl:variable>
			<xsl:variable name="lLastName">
				<xsl:call-template name="escapeSpecialSymbols">
					<xsl:with-param name="pText" select=".//surname"></xsl:with-param>
				</xsl:call-template>
			</xsl:variable>
			<xsl:text>{{aut|</xsl:text><xsl:value-of select="$lLastName"/><xsl:text>, </xsl:text><xsl:value-of select="substring($lFirstName, 1, 1)" /><xsl:text>}}; </xsl:text>
		</xsl:for-each>
		<xsl:value-of select="$pArticlePubYear" /><xsl:text>: </xsl:text>
		<xsl:value-of select="normalize-space($lParsedArticleTitle)"></xsl:value-of>
		<xsl:text> [http://www.pensoft.net/journals/</xsl:text><xsl:value-of select="$lJournalUrlTitle"></xsl:value-of><xsl:text>/ ''</xsl:text><xsl:value-of select="$lJournalTitle"></xsl:value-of><xsl:text>'',]</xsl:text>
		<xsl:text> '''</xsl:text><xsl:value-of select="$pArticleVolume"></xsl:value-of><xsl:text>''': </xsl:text><xsl:value-of select="$pArticlePages"></xsl:value-of><xsl:text>. {{doi|</xsl:text><xsl:value-of select="$lArticleDoi"></xsl:value-of><xsl:text>}}</xsl:text>
	</xsl:template>
		
	<!-- 
		Темплейт, който връща текстовото съдържание на подадения node, като не показва в него label-а или title-a
	-->	
	<xsl:template name="get_node_text_template">
		<xsl:param name="pNode" />
		<xsl:param name="pPutSpaces" />
		<xsl:variable name="lLocalName" >
			<xsl:variable name="lTempName" select="local-name($pNode)"></xsl:variable>
			<xsl:call-template name="ToLower">
				<xsl:with-param name="inputString" select="$lTempName"/>
			</xsl:call-template>
		</xsl:variable>
		<xsl:variable name="lTextType" select="$pNode/self::text()" />
		<xsl:variable name="lElementType" select="$pNode/self::*" />		
		<xsl:choose>
			<xsl:when test="$lTextType">
				<xsl:choose>
					<xsl:when test="string($pNode) != '' and php:function('trim', string($pNode)) = ''">
						<xsl:text> </xsl:text>
					</xsl:when>
					<xsl:otherwise>
						<xsl:call-template name="escapeSpecialSymbols">
							<xsl:with-param name="pText" select="php:function('prepareXslText', string($pNode))"></xsl:with-param>
						</xsl:call-template>
						<xsl:if test="$pPutSpaces &gt; 0"><xsl:text> </xsl:text></xsl:if>
					</xsl:otherwise>
				</xsl:choose>									
			</xsl:when>
			<xsl:when test="$lElementType">
				<xsl:choose>
					<xsl:when test="$lLocalName='label' or $lLocalName='title' or $lLocalName='object-id'"></xsl:when>					
					<xsl:otherwise>								
						<xsl:call-template name="get_element_node_text_template">	
							<xsl:with-param name="pNode" select="$pNode"></xsl:with-param>
							<xsl:with-param name="pPutSpaces" select="$pPutSpaces"></xsl:with-param>
						</xsl:call-template>					
					</xsl:otherwise>
				</xsl:choose>
			</xsl:when>
		</xsl:choose>
	</xsl:template>
	
	<!-- 
		Темплейт, който вика темплейта get_node_text_template за всички деца на подадения node
	-->
	<xsl:template name="get_element_node_text_template">
		<xsl:param name="pNode" />		
		<xsl:param name="pPutSpaces" />		
		<xsl:for-each select="$pNode/child::node()" >
			<xsl:variable name="lCurrentNode" select="." />
			<xsl:call-template name="get_node_text_template">	
				<xsl:with-param name="pNode" select="$lCurrentNode"></xsl:with-param>
				<xsl:with-param name="pPutSpaces" select="$pPutSpaces"></xsl:with-param>
			</xsl:call-template>
		</xsl:for-each>
	</xsl:template>
	
	<!-- 
		Темплейт, който връща текстовото съдържание на подадения section node, като не показва в него label-а или title-a
	-->	
	<xsl:template name="get_sec_node_text_template">
		<xsl:param name="pNode" />
		<xsl:param name="pPutSpaces">0</xsl:param>
		<xsl:param name="pDontEscapeText">0</xsl:param>
		<xsl:param name="pArticleAuthors"></xsl:param>
		<xsl:variable name="lLocalName" >
			<xsl:variable name="lTempName" select="local-name($pNode)"></xsl:variable>
			<xsl:call-template name="ToLower">
				<xsl:with-param name="inputString" select="$lTempName"/>
			</xsl:call-template>
		</xsl:variable>
		<xsl:variable name="lTextType" select="$pNode/self::text()" />
		<xsl:variable name="lElementType" select="$pNode/self::*" />		
		<xsl:choose>
			<xsl:when test="$lTextType">
				<xsl:choose>
					<xsl:when test="string($pNode) != '' and php:function('trim', string($pNode)) = ''">
						<xsl:text> </xsl:text>
					</xsl:when>
					<xsl:otherwise>
						<xsl:choose>
							<xsl:when test="$pDontEscapeText &gt; 0"><xsl:value-of select="php:function('prepareXslText', string($pNode))"></xsl:value-of></xsl:when>
							<xsl:otherwise>
								<xsl:call-template name="escapeSpecialSymbols">
									<xsl:with-param name="pText" select="php:function('prepareXslText', string($pNode))"></xsl:with-param>
								</xsl:call-template>
							</xsl:otherwise>
						</xsl:choose>
						<xsl:if test="$pPutSpaces &gt; 0"><xsl:text> </xsl:text></xsl:if>
					</xsl:otherwise>
				</xsl:choose>						
			</xsl:when>
			<xsl:when test="$lElementType">
				<xsl:variable name="lNodeContent">
					<xsl:call-template name="get_sec_element_node_text_template">	
						<xsl:with-param name="pNode" select="$pNode"></xsl:with-param>
						<xsl:with-param name="pPutSpaces" select="$pPutSpaces"></xsl:with-param>
						<xsl:with-param name="pArticleAuthors" select="$pArticleAuthors"></xsl:with-param>
						<xsl:with-param name="pDontEscapeText" select="$pDontEscapeText"></xsl:with-param>
					</xsl:call-template>
				</xsl:variable>		
				<xsl:variable name="lUnescapedNodeContent">
					<xsl:call-template name="get_sec_element_node_text_template">	
						<xsl:with-param name="pNode" select="$pNode"></xsl:with-param>
						<xsl:with-param name="pPutSpaces" select="$pPutSpaces"></xsl:with-param>
						<xsl:with-param name="pArticleAuthors" select="$pArticleAuthors"></xsl:with-param>
						<xsl:with-param name="pDontEscapeText">1</xsl:with-param>
					</xsl:call-template>
				</xsl:variable>		
				<xsl:choose>
					<xsl:when test="$lLocalName='label' or $lLocalName='title' or $lLocalName='object-id' or $lLocalName='fig'"></xsl:when>					
					<xsl:when test="$lLocalName='table-wrap' and $pNode/@content-type='key'">
						<xsl:call-template name="getTaxonKey">
							<xsl:with-param name="pSecNode" select="$pNode/parent::*"></xsl:with-param>
							<xsl:with-param name="pArticleAuthors" select="$pArticleAuthors"></xsl:with-param>
						</xsl:call-template>
					</xsl:when>
					<xsl:when test="$lLocalName='table-wrap'">
						<xsl:call-template name="getTableTemplate">
							<xsl:with-param name="pNode" select="$pNode"></xsl:with-param>							
						</xsl:call-template>
					</xsl:when>
					<xsl:when test="$lLocalName='p' and count($pNode/following-sibling::*) &gt; 0 and name($pNode/following-sibling::*[1])='p'">
						<xsl:value-of select="normalize-space($lNodeContent)" /><xsl:text>&lt;br /&gt;
</xsl:text>
					</xsl:when>
					<xsl:when test="$lLocalName='ext-link'">
						<xsl:choose>
							<xsl:when test="$pNode/@ext-link-type='uri'">
								<xsl:variable name="lLinkHref" select="$pNode/@xlink:href"></xsl:variable>
								<xsl:text>[</xsl:text><xsl:value-of select="$lLinkHref"/><xsl:text> </xsl:text>
								<xsl:value-of select="$lNodeContent"></xsl:value-of>
								<xsl:text>]</xsl:text>
							</xsl:when>
							<xsl:otherwise>
								<xsl:value-of select="$lNodeContent"></xsl:value-of>
							</xsl:otherwise>
						</xsl:choose>
					</xsl:when>
					<xsl:when test="$lLocalName='xref'">
						<xsl:choose>
							<xsl:when test="$pNode/@ref-type='bibr'">
								<xsl:variable name="lRefId" select="$pNode/@rid"></xsl:variable>
								<xsl:variable name="lReferenceNode" select="$pNode/ancestor::*[name()='article']//ref[@id=$lRefId]/mixed-citation"></xsl:variable>				
								<xsl:variable name="lRefContent">
									<xsl:call-template name="otherReferenceTemplate">
										<xsl:with-param name="pNode" select="$lReferenceNode"></xsl:with-param>
									</xsl:call-template>
								</xsl:variable>
								<xsl:value-of select="$lNodeContent"></xsl:value-of>
								<xsl:text>&lt;ref name="</xsl:text><xsl:value-of select="$lRefId"/><xsl:text>"&gt;</xsl:text><xsl:value-of select="$lRefContent" /><xsl:text>&lt;/ref &gt;</xsl:text>
							</xsl:when>
							<xsl:otherwise>
								<xsl:value-of select="$lNodeContent"></xsl:value-of>
							</xsl:otherwise>
						</xsl:choose>
					</xsl:when>
					<xsl:when test="$lLocalName='location'">
						<xsl:variable name="lCoordinateAttribute" select="./@location-type"></xsl:variable>						
						<xsl:choose>
							<xsl:when test="$lCoordinateAttribute='dwc:verbatimCoordinates'">
								<xsl:text>[</xsl:text>
									<xsl:value-of select="$gGoogleMapsUrl"></xsl:value-of>?labels%5B0%5D=<xsl:value-of select="php:function('urlencode', string($lUnescapedNodeContent))"></xsl:value-of>&amp;coordinates%5B0%5D=<xsl:value-of select="php:function('urlencode', string( $lUnescapedNodeContent))"></xsl:value-of>
								<xsl:text> </xsl:text>
								<xsl:value-of select="$lNodeContent"/>
								<xsl:text>]</xsl:text>
							</xsl:when>							
							<xsl:otherwise>
								<xsl:value-of select="$lNodeContent"/>
							</xsl:otherwise>
						</xsl:choose>
					</xsl:when>
					<xsl:when test="$lLocalName='named-content'">
						<xsl:variable name="lCoordinateAttribute" select="./@content-type"></xsl:variable>						
						<xsl:choose>
							<xsl:when test="$lCoordinateAttribute='dwc:verbatimCoordinates'">
								<xsl:text>[</xsl:text>
									<xsl:value-of select="$gGoogleMapsUrl"></xsl:value-of>?labels%5B0%5D=<xsl:value-of select="php:function('urlencode', string($lUnescapedNodeContent))"></xsl:value-of>&amp;coordinates%5B0%5D=<xsl:value-of select="php:function('urlencode', string( $lUnescapedNodeContent))"></xsl:value-of>
								<xsl:text> </xsl:text>
								<xsl:value-of select="$lNodeContent"/>
								<xsl:text>]</xsl:text>
							</xsl:when>							
							<xsl:otherwise>
								<xsl:value-of select="$lNodeContent"/>
							</xsl:otherwise>
						</xsl:choose>
					</xsl:when>	
					<xsl:when test="$lLocalName='sec'">
<xsl:variable name="lSecTitle" select="$pNode/title"></xsl:variable>
<xsl:variable name="lContent">
	<xsl:value-of select="$lNodeContent"></xsl:value-of>
</xsl:variable>
<xsl:text>
'''</xsl:text><xsl:value-of select="$lSecTitle" /><xsl:text>''' </xsl:text>
<xsl:value-of select="php:function('trim', string($lContent))" />&lt;br /&gt;</xsl:when>
					<xsl:otherwise>
						<xsl:if test="$lLocalName='taxon-name'">
							<xsl:text>{{Taxon name|</xsl:text>
						</xsl:if>
						<xsl:if test="$lLocalName='bold'">
							<xsl:text>'''</xsl:text>
						</xsl:if>
						<xsl:if test="$lLocalName='italic'">
							<xsl:text>''</xsl:text>
						</xsl:if>
						<xsl:value-of select="$lNodeContent"></xsl:value-of>
						<xsl:if test="$lLocalName='taxon-name'">
							<xsl:text>}}</xsl:text>
						</xsl:if>						
						<xsl:if test="$lLocalName='bold'">
							<xsl:text>'''</xsl:text>
						</xsl:if>
						<xsl:if test="$lLocalName='italic'">
							<xsl:text>''</xsl:text>
						</xsl:if>
					</xsl:otherwise>
				</xsl:choose>
			</xsl:when>
		</xsl:choose>
	</xsl:template>
	
	
	
	<!-- 
		Темплейт, който вика темплейта get_sec_node_text_template за всички деца на подадения node
	-->
	<xsl:template name="get_sec_element_node_text_template">
		<xsl:param name="pNode" />		
		<xsl:param name="pPutSpaces" />	
		<xsl:param name="pArticleAuthors" />
		<xsl:param name="pDontEscapeText">0</xsl:param>
		<xsl:for-each select="$pNode/child::node()" >
			<xsl:variable name="lCurrentNode" select="." />
			<xsl:call-template name="get_sec_node_text_template">	
				<xsl:with-param name="pNode" select="$lCurrentNode"></xsl:with-param>
				<xsl:with-param name="pPutSpaces" select="$pPutSpaces"></xsl:with-param>
				<xsl:with-param name="pArticleAuthors" select="$pArticleAuthors"></xsl:with-param>
				<xsl:with-param name="pDontEscapeText" select="$pDontEscapeText"></xsl:with-param>
			</xsl:call-template>
		</xsl:for-each>
	</xsl:template>
	
	
	
	
	<!-- 
		Темплейт, който връща текстовото съдържание на подадения section node, като не показва в него label-а или title-a
	-->	
	<xsl:template name="get_special_parse_node_text_template">
		<xsl:param name="pNode" />
		<xsl:param name="pPutSpaces"></xsl:param>
		<xsl:param name="pDontIgnoreLabels">0</xsl:param>
		<xsl:param name="pDontEscapeText">0</xsl:param>
		<xsl:variable name="lLocalName" >
			<xsl:variable name="lTempName" select="local-name($pNode)"></xsl:variable>
			<xsl:call-template name="ToLower">
				<xsl:with-param name="inputString" select="$lTempName"/>
			</xsl:call-template>
		</xsl:variable>
		<xsl:variable name="lTextType" select="$pNode/self::text()" />
		<xsl:variable name="lElementType" select="$pNode/self::*" />		
		<xsl:choose>
			<xsl:when test="$lTextType">
				<xsl:choose>
					<xsl:when test="string($pNode) != '' and php:function('trim', string($pNode)) = ''">
						<xsl:text> </xsl:text>
					</xsl:when>
					<xsl:otherwise>
						<xsl:choose>
							<xsl:when test="$pDontEscapeText &gt; 0"><xsl:value-of select="php:function('prepareXslText', string($pNode))"></xsl:value-of></xsl:when>
							<xsl:otherwise>
								<xsl:call-template name="escapeSpecialSymbols">
									<xsl:with-param name="pText" select="php:function('prepareXslText', string($pNode))"></xsl:with-param>
								</xsl:call-template>
							</xsl:otherwise>
						</xsl:choose>
						<xsl:if test="$pPutSpaces &gt; 0"><xsl:text> </xsl:text></xsl:if>
					</xsl:otherwise>
				</xsl:choose>						
			</xsl:when>
			<xsl:when test="$lElementType">
				<xsl:variable name="lNodeContent">
					<xsl:call-template name="get_special_parse_element_node_text_template">	
						<xsl:with-param name="pNode" select="$pNode"></xsl:with-param>
						<xsl:with-param name="pPutSpaces" select="$pPutSpaces"></xsl:with-param>
						<xsl:with-param name="pDontIgnoreLabels" select="$pDontIgnoreLabels"></xsl:with-param>
						<xsl:with-param name="pDontEscapeText" select="$pDontEscapeText"></xsl:with-param>
					</xsl:call-template>
				</xsl:variable>	
				<xsl:variable name="lUnescapedNodeContent">
					<xsl:call-template name="get_special_parse_element_node_text_template">	
						<xsl:with-param name="pNode" select="$pNode"></xsl:with-param>
						<xsl:with-param name="pPutSpaces" select="$pPutSpaces"></xsl:with-param>
						<xsl:with-param name="pDontIgnoreLabels" select="$pDontIgnoreLabels"></xsl:with-param>
						<xsl:with-param name="pDontEscapeText">1</xsl:with-param>
					</xsl:call-template>
				</xsl:variable>	
				<xsl:choose>
					<xsl:when test="number($pDontIgnoreLabels) = 0 and ($lLocalName='label' or $lLocalName='title')"></xsl:when>
					<xsl:when test="$lLocalName='object-id' or $lLocalName='fig'"></xsl:when>
					<xsl:when test="$lLocalName='table-wrap'">
						<xsl:call-template name="getTableTemplate">
							<xsl:with-param name="pNode" select="$pNode"></xsl:with-param>							
						</xsl:call-template>
					</xsl:when>					
					<xsl:when test="$lLocalName='location'">
						<xsl:variable name="lCoordinateAttribute" select="./@location-type"></xsl:variable>						
						<xsl:choose>
							<xsl:when test="$lCoordinateAttribute='dwc:verbatimCoordinates'">
								<xsl:text>[</xsl:text>
									<xsl:value-of select="$gGoogleMapsUrl"></xsl:value-of>?labels%5B0%5D=<xsl:value-of select="php:function('urlencode', string($lUnescapedNodeContent))"></xsl:value-of>&amp;coordinates%5B0%5D=<xsl:value-of select="php:function('urlencode', string( $lUnescapedNodeContent))"></xsl:value-of>
								<xsl:text> </xsl:text>
								<xsl:value-of select="$lNodeContent"/>
								<xsl:text>]</xsl:text>
							</xsl:when>							
							<xsl:otherwise>
								<xsl:value-of select="$lNodeContent"/>
							</xsl:otherwise>
						</xsl:choose>
					</xsl:when>
					<xsl:when test="$lLocalName='named-content'">
						<xsl:variable name="lCoordinateAttribute" select="./@content-type"></xsl:variable>						
						<xsl:choose>
							<xsl:when test="$lCoordinateAttribute='dwc:verbatimCoordinates'">
								<xsl:text>[</xsl:text>
									<xsl:value-of select="$gGoogleMapsUrl"></xsl:value-of>?labels%5B0%5D=<xsl:value-of select="php:function('urlencode', string($lUnescapedNodeContent))"></xsl:value-of>&amp;coordinates%5B0%5D=<xsl:value-of select="php:function('urlencode', string( $lUnescapedNodeContent))"></xsl:value-of>
								<xsl:text> </xsl:text>
								<xsl:value-of select="$lNodeContent"/>
								<xsl:text>]</xsl:text>
							</xsl:when>							
							<xsl:otherwise>
								<xsl:value-of select="$lNodeContent"/>
							</xsl:otherwise>
						</xsl:choose>
					</xsl:when>	
					<xsl:when test="$lLocalName='ext-link'">
						<xsl:choose>
							<xsl:when test="$pNode/@ext-link-type='uri'">
								<xsl:variable name="lLinkHref" select="$pNode/@xlink:href"></xsl:variable>
								<xsl:text>[</xsl:text><xsl:value-of select="$lLinkHref"/><xsl:text> </xsl:text>
								<xsl:value-of select="$lNodeContent"/>
								<xsl:text>]</xsl:text>
							</xsl:when>
							<xsl:otherwise>
								<xsl:value-of select="$lNodeContent"/>
							</xsl:otherwise>
						</xsl:choose>
					</xsl:when>	
					<xsl:when test="$lLocalName='xref'">
						<xsl:choose>
							<xsl:when test="$pNode/@ref-type='bibr'">
								<xsl:variable name="lRefId" select="$pNode/@rid"></xsl:variable>
								<xsl:variable name="lReferenceNode" select="$pNode/ancestor::*[name()='article']//ref[@id=$lRefId]/mixed-citation"></xsl:variable>				
								<xsl:variable name="lRefContent">
									<xsl:call-template name="otherReferenceTemplate">
										<xsl:with-param name="pNode" select="$lReferenceNode"></xsl:with-param>
									</xsl:call-template>
								</xsl:variable>
								<xsl:value-of select="$lNodeContent"></xsl:value-of>
								<xsl:text>&lt;ref name="</xsl:text><xsl:value-of select="$lRefId"/><xsl:text>"&gt;</xsl:text><xsl:value-of select="$lRefContent" /><xsl:text>&lt;/ref &gt;</xsl:text>
							</xsl:when>
							<xsl:otherwise>
								<xsl:value-of select="$lNodeContent"></xsl:value-of>
							</xsl:otherwise>
						</xsl:choose>
					</xsl:when>
					<xsl:otherwise>
						<xsl:if test="$lLocalName='taxon-name'">
							<xsl:text>{{Taxon name|</xsl:text>
						</xsl:if>
						<xsl:if test="$lLocalName='bold'">
							<xsl:text>'''</xsl:text>
						</xsl:if>
						<xsl:if test="$lLocalName='italic'">
							<xsl:text>''</xsl:text>
						</xsl:if>
						<xsl:value-of select="$lNodeContent"/>
						<xsl:if test="$lLocalName='taxon-name'">
							<xsl:text>}}</xsl:text>
						</xsl:if>						
						<xsl:if test="$lLocalName='bold'">
							<xsl:text>'''</xsl:text>
						</xsl:if>
						<xsl:if test="$lLocalName='italic'">
							<xsl:text>''</xsl:text>
						</xsl:if>
					</xsl:otherwise>
				</xsl:choose>
			</xsl:when>
		</xsl:choose>
	</xsl:template>
	
	
	
	<!-- 
		Темплейт, който вика темплейта get_sec_node_text_template за всички деца на подадения node
	-->
	<xsl:template name="get_special_parse_element_node_text_template">
		<xsl:param name="pNode" />		
		<xsl:param name="pPutSpaces" />		
		<xsl:param name="pDontIgnoreLabels">0</xsl:param>
		<xsl:param name="pDontEscapeText">0</xsl:param>
		<xsl:for-each select="$pNode/child::node()" >
			<xsl:variable name="lCurrentNode" select="." />
			<xsl:call-template name="get_special_parse_node_text_template">	
				<xsl:with-param name="pNode" select="$lCurrentNode"></xsl:with-param>
				<xsl:with-param name="pPutSpaces" select="$pPutSpaces"></xsl:with-param>
				<xsl:with-param name="pDontIgnoreLabels" select="$pDontIgnoreLabels" ></xsl:with-param>
				<xsl:with-param name="pDontEscapeText" select="$pDontEscapeText"></xsl:with-param>
			</xsl:call-template>
		</xsl:for-each>
	</xsl:template>
	
	
	
	
	<!-- 
		Темплейт, който връща текстовото съдържание на подадената клетка от key таблицата
	-->	
	<xsl:template name="get_key_parse_node_text_template">
		<xsl:param name="pNode" />
		<xsl:param name="pPutSpaces"></xsl:param>
		<xsl:param name="pDontIgnoreLabels">0</xsl:param>
		<xsl:param name="pDontEscapeText">0</xsl:param>
		<xsl:variable name="lLocalName" >
			<xsl:variable name="lTempName" select="local-name($pNode)"></xsl:variable>
			<xsl:call-template name="ToLower">
				<xsl:with-param name="inputString" select="$lTempName"/>
			</xsl:call-template>
		</xsl:variable>
		<xsl:variable name="lTextType" select="$pNode/self::text()" />
		<xsl:variable name="lElementType" select="$pNode/self::*" />		
		<xsl:choose>
			<xsl:when test="$lTextType">
				<xsl:choose>
					<xsl:when test="string($pNode) != '' and php:function('trim', string($pNode)) = ''">
						<xsl:text> </xsl:text>
					</xsl:when>
					<xsl:otherwise>
						<xsl:choose>
							<xsl:when test="$pDontEscapeText &gt; 0"><xsl:value-of select="php:function('prepareXslText', string($pNode))"></xsl:value-of></xsl:when>
							<xsl:otherwise>
								<xsl:call-template name="escapeSpecialSymbols">
									<xsl:with-param name="pText" select="php:function('prepareXslText', string($pNode))"></xsl:with-param>
								</xsl:call-template>
							</xsl:otherwise>
						</xsl:choose>
						<xsl:if test="$pPutSpaces &gt; 0"><xsl:text> </xsl:text></xsl:if>
					</xsl:otherwise>
				</xsl:choose>						
			</xsl:when>
			<xsl:when test="$lElementType">
				<xsl:variable name="lNodeContent">
					<xsl:call-template name="get_key_parse_element_node_text_template">	
						<xsl:with-param name="pNode" select="$pNode"></xsl:with-param>
						<xsl:with-param name="pPutSpaces" select="$pPutSpaces"></xsl:with-param>
						<xsl:with-param name="pDontIgnoreLabels" select="$pDontIgnoreLabels" ></xsl:with-param>
						<xsl:with-param name="pDontEscapeText" select="$pDontEscapeText" ></xsl:with-param>
					</xsl:call-template>
				</xsl:variable>
				<xsl:variable name="lUnescapedNodeContent">
					<xsl:call-template name="get_key_parse_element_node_text_template">	
						<xsl:with-param name="pNode" select="$pNode"></xsl:with-param>
						<xsl:with-param name="pPutSpaces" select="$pPutSpaces"></xsl:with-param>
						<xsl:with-param name="pDontIgnoreLabels" select="$pDontIgnoreLabels" ></xsl:with-param>
						<xsl:with-param name="pDontEscapeText">1</xsl:with-param>
					</xsl:call-template>
				</xsl:variable>
				<xsl:choose>
					<xsl:when test="number($pDontIgnoreLabels) = 0 and ($lLocalName='label' or $lLocalName='title')"></xsl:when>
					<xsl:when test="$lLocalName='object-id' or $lLocalName='fig'"></xsl:when>					
					<xsl:when test="$lLocalName='graphic'">
												
						<xsl:variable name="lHrefAttribute" select="$pNode/@xlink:href"></xsl:variable>						
						<xsl:text>| image m = File:</xsl:text><xsl:value-of select="$lHrefAttribute" />
					</xsl:when>
					<xsl:when test="$lLocalName='table-wrap'">
						<xsl:call-template name="getTableTemplate">
							<xsl:with-param name="pNode" select="$pNode"></xsl:with-param>							
						</xsl:call-template>
					</xsl:when>
					<xsl:when test="$lLocalName='location'">
						<xsl:variable name="lCoordinateAttribute" select="./@location-type"></xsl:variable>						
						<xsl:choose>
							<xsl:when test="$lCoordinateAttribute='dwc:verbatimCoordinates'">
								<xsl:text>[</xsl:text>
									<xsl:value-of select="$gGoogleMapsUrl"></xsl:value-of>?labels%5B0%5D=<xsl:value-of select="php:function('urlencode', string($lUnescapedNodeContent))"></xsl:value-of>&amp;coordinates%5B0%5D=<xsl:value-of select="php:function('urlencode', string( $lUnescapedNodeContent))"></xsl:value-of>
								<xsl:text> </xsl:text>
								<xsl:value-of select="$lNodeContent"/>
								<xsl:text>]</xsl:text>
							</xsl:when>							
							<xsl:otherwise>
								<xsl:value-of select="$lNodeContent"/>
							</xsl:otherwise>
						</xsl:choose>
					</xsl:when>
					<xsl:when test="$lLocalName='named-content'">
						<xsl:variable name="lType" select="./@content-type"></xsl:variable>						
						<xsl:choose>
							<xsl:when test="$lType='dwc:verbatimCoordinates'">
								<xsl:text>[</xsl:text>
									<xsl:value-of select="$gGoogleMapsUrl"></xsl:value-of>?labels%5B0%5D=<xsl:value-of select="php:function('urlencode', string($lUnescapedNodeContent))"></xsl:value-of>&amp;coordinates%5B0%5D=<xsl:value-of select="php:function('urlencode', string( $lUnescapedNodeContent))"></xsl:value-of>
								<xsl:text> </xsl:text>
								<xsl:value-of select="$lNodeContent"/>
								<xsl:text>]</xsl:text>
							</xsl:when>
							<xsl:when test="$lType='remarks'">
								<xsl:text>| remarks = </xsl:text><xsl:value-of select="$lNodeContent"/>
							</xsl:when>							
							<xsl:otherwise>
								<xsl:value-of select="$lNodeContent"/>
							</xsl:otherwise>
						</xsl:choose>
					</xsl:when>	
					<xsl:when test="$lLocalName='ext-link'">
						<xsl:choose>
							<xsl:when test="$pNode/@ext-link-type='uri'">
								<xsl:variable name="lLinkHref" select="$pNode/@xlink:href"></xsl:variable>
								<xsl:text>[</xsl:text><xsl:value-of select="$lLinkHref"/><xsl:text> </xsl:text>
								<xsl:value-of select="$lNodeContent"/>
								<xsl:text>]</xsl:text>
							</xsl:when>
							<xsl:otherwise>
								<xsl:value-of select="$lNodeContent"/>
							</xsl:otherwise>
						</xsl:choose>
					</xsl:when>
					<xsl:when test="$lLocalName='xref'">
						<xsl:choose>
							<xsl:when test="$pNode/@ref-type='bibr'">
								<xsl:variable name="lRefId" select="$pNode/@rid"></xsl:variable>
								<xsl:variable name="lReferenceNode" select="$pNode/ancestor::*[name()='article']//ref[@id=$lRefId]/mixed-citation"></xsl:variable>				
								<xsl:variable name="lRefContent">
									<xsl:call-template name="otherReferenceTemplate">
										<xsl:with-param name="pNode" select="$lReferenceNode"></xsl:with-param>
									</xsl:call-template>
								</xsl:variable>
								<xsl:value-of select="$lNodeContent"></xsl:value-of>
								<xsl:text>&lt;ref name="</xsl:text><xsl:value-of select="$lRefId"/><xsl:text>"&gt;</xsl:text><xsl:value-of select="$lRefContent" /><xsl:text>&lt;/ref &gt;</xsl:text>
							</xsl:when>
							<xsl:otherwise>
								<xsl:value-of select="$lNodeContent"></xsl:value-of>
							</xsl:otherwise>
						</xsl:choose>
					</xsl:when>
					<xsl:otherwise>
						<xsl:if test="$lLocalName='taxon-name'">
							<xsl:text>{{Taxon name|</xsl:text>
						</xsl:if>
						<xsl:if test="$lLocalName='bold'">
							<xsl:text>'''</xsl:text>
						</xsl:if>
						<xsl:if test="$lLocalName='italic'">
							<xsl:text>''</xsl:text>
						</xsl:if>
						<xsl:value-of select="$lNodeContent"/>
						<xsl:if test="$lLocalName='taxon-name'">
							<xsl:text>}}</xsl:text>
						</xsl:if>						
						<xsl:if test="$lLocalName='bold'">
							<xsl:text>'''</xsl:text>
						</xsl:if>
						<xsl:if test="$lLocalName='italic'">
							<xsl:text>''</xsl:text>
						</xsl:if>
					</xsl:otherwise>
				</xsl:choose>
			</xsl:when>
		</xsl:choose>
	</xsl:template>
	
	
	
	<!-- 
		Темплейт, който вика темплейта get_key_parse_node_text_template за всички деца на подадения node
	-->
	<xsl:template name="get_key_parse_element_node_text_template">
		<xsl:param name="pNode" />		
		<xsl:param name="pPutSpaces" />		
		<xsl:param name="pDontIgnoreLabels">0</xsl:param>
		<xsl:param name="pDontEscapeText">0</xsl:param>
		<xsl:for-each select="$pNode/child::node()" >
			<xsl:variable name="lCurrentNode" select="." />
			<xsl:call-template name="get_key_parse_node_text_template">	
				<xsl:with-param name="pNode" select="$lCurrentNode"></xsl:with-param>
				<xsl:with-param name="pPutSpaces" select="$pPutSpaces"></xsl:with-param>
				<xsl:with-param name="pDontIgnoreLabels" select="$pDontIgnoreLabels" ></xsl:with-param>
				<xsl:with-param name="pDontEscapeText" select="$pDontEscapeText"></xsl:with-param>
			</xsl:call-template>
		</xsl:for-each>
	</xsl:template>
	
	<!--
		Тук ще ескейпваме специалните символи за да не чупят уики кода
		otkrih oshte edin problem - kogato v XML-a ima nyakoi ot slednite symboli:

[ ] = { } | '

te tryabva da se tagnat kato <nowiki>[</nowiki>, za6toto chupayta koda na Mediawiki
	-->
	<xsl:template name="escapeSpecialSymbols">
		<xsl:param name="pText" />
		<xsl:variable name="lTemp1">
			<xsl:call-template name="replaceSpecialSymbolTemplate">
				<xsl:with-param name="text" select="$pText"/>
				<xsl:with-param name="searchSymbol">[</xsl:with-param>
			</xsl:call-template>
		</xsl:variable>
		<xsl:variable name="lTemp2">
			<xsl:call-template name="replaceSpecialSymbolTemplate">
				<xsl:with-param name="text" select="$lTemp1"/>
				<xsl:with-param name="searchSymbol">]</xsl:with-param>
			</xsl:call-template>
		</xsl:variable>
		<xsl:variable name="lTemp3">
			<xsl:call-template name="replaceSpecialSymbolTemplate">
				<xsl:with-param name="text" select="$lTemp2"/>
				<xsl:with-param name="searchSymbol">=</xsl:with-param>
			</xsl:call-template>
		</xsl:variable>
		<xsl:variable name="lTemp4">
			<xsl:call-template name="replaceSpecialSymbolTemplate">
				<xsl:with-param name="text" select="$lTemp3"/>
				<xsl:with-param name="searchSymbol">{</xsl:with-param>				
			</xsl:call-template>
		</xsl:variable>
		<xsl:variable name="lTemp5">
			<xsl:call-template name="replaceSpecialSymbolTemplate">
				<xsl:with-param name="text" select="$lTemp4"/>
				<xsl:with-param name="searchSymbol">|</xsl:with-param>
			</xsl:call-template>
		</xsl:variable>
		<xsl:variable name="lTemp6">
			<xsl:call-template name="replaceSpecialSymbolTemplate">
				<xsl:with-param name="text" select="$lTemp5"/>
				<xsl:with-param name="searchSymbol">}</xsl:with-param>				
			</xsl:call-template>
		</xsl:variable>
		<xsl:variable name="lSearchPattern"><xsl:text>/''+/</xsl:text></xsl:variable>
		<xsl:variable name="lReplacePattern"><xsl:value-of select="$gEscapeStart" /><xsl:text>$0</xsl:text><xsl:value-of select="$gEscapeEnd" /></xsl:variable>
		<xsl:variable name="lTemp7" select="php:function('preg_replace', $lSearchPattern, $lReplacePattern, string($lTemp6))"></xsl:variable>
		<xsl:value-of select="$lTemp7"></xsl:value-of>
	</xsl:template>
	
	<!--
		Ограждаме подадения символ в специалните символи
		за да може накрая да го заменим в php-то, тъй като в xsl-a е прекалено сложно
	-->
	<xsl:template name="replaceSpecialSymbolTemplate">
		<xsl:param name="text" select="."/>
		<xsl:param name="searchSymbol"/>
		<xsl:choose>			
			<xsl:when test="contains($text, $searchSymbol)">
				<xsl:value-of select="substring-before($text, $searchSymbol)"/><xsl:value-of select="$gEscapeStart"/><xsl:value-of select="$searchSymbol"/><xsl:value-of select="$gEscapeEnd"/>
				<xsl:call-template name="replaceSpecialSymbolTemplate">
					  <xsl:with-param name="text" select="substring-after($text, $searchSymbol)"/>
					  <xsl:with-param name="searchSymbol" select="$searchSymbol"/>
				</xsl:call-template>
			</xsl:when>			
			<xsl:otherwise>
				<xsl:value-of select="$text"/>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>
	
	<!--
		Темплейт за таблица
		
		Tablicite ot XML-a:

		<table-wrap id="T1" position="float" orientation="portrait"> 
		<label>Table 1.</label>
		<caption><p>Chaetotaxy of anterior PT in <italic><tp:taxon-name>Sinocallipus jaegeri</tp:taxon-name></italic> sp. n.</p></caption>
			<table> 
				<tbody> 
					<tr> 
						<th> </th> 
						<th>Anterior setae</th> 
						<th>Posterior setae</th> 
					</tr> 
					<tr> 
						<td>Collum</td> 
						<td>5+5</td> 
						<td>-</td> 
					</tr> 
					<tr> 
						<td>PT 2</td> 
						<td>5+5</td> 
						<td>-</td> 
					</tr> 
					<tr> 
						<td>PT 3</td> 
						<td>5+5</td> 
						<td>-</td> 
					</tr> 
					<tr> 
						<td>PT 4</td> 
						<td>5+5</td> 
						<td>-</td> 
					</tr> 
					<tr> 
						<td>PT 5</td> 
						<td>5+5</td> 
						<td>-</td> 
					</tr> 
					<tr> 
						<td>PT 6</td> 
						<td>5+5</td> 
						<td>-</td> 
					</tr> 
				</tbody>
			</table>
		</table-wrap>


		da se konvertirat po sledniya nachin, kato ostavya na myastoto na koeto sa bili v XML-a s po edin prazen red otgore i otdolu:


		{| class="wikitable" ; style="width: 100%"
		|+ '''Table 1.''' Chaetotaxy of anterior PT in ''{{Taxon name|Sinocallipus jaegeri}}'' sp. n.
		|-
		!  !! Anterior setae !! Posterior setae
		|-
		| Collum || 5+5 || -
		|-
		| PT 2 || 5+5 || -
		|-
		| PT 3 || 5+5 || -
		|-
		| PT 4 || 5+5 || -
		|-
		| PT 5 || 5+5 || -
		|-
		| PT 6 || 5+5 || -
		|}
	-->
	<xsl:template name="getTableTemplate">
		<xsl:param name="pNode"></xsl:param>
		<xsl:variable name="lLabel">
			<xsl:call-template name="get_special_parse_node_text_template">
				<xsl:with-param name="pNode" select="$pNode/label"></xsl:with-param>
				<xsl:with-param name="pDontIgnoreLabels">1</xsl:with-param>
			</xsl:call-template>
		</xsl:variable>
		<xsl:variable name="lCaption">
			<xsl:call-template name="get_special_parse_node_text_template">
				<xsl:with-param name="pNode" select="$pNode/caption"></xsl:with-param>
			</xsl:call-template>
		</xsl:variable>
		<xsl:variable name="lRows" select="$pNode//tr"></xsl:variable>
		<xsl:text>{| class="wikitable" ; style="width: 100%"
|+ '''</xsl:text><xsl:value-of select="normalize-space($lLabel)" /><xsl:text>''' </xsl:text><xsl:value-of select="normalize-space($lCaption)" />
		<xsl:for-each select="$lRows">
			<xsl:text>
|-
</xsl:text>
			<xsl:for-each select="./th">
				<xsl:variable name="lCellContent">
					<xsl:call-template name="get_special_parse_node_text_template">
						<xsl:with-param name="pNode" select="."></xsl:with-param>
					</xsl:call-template>
				</xsl:variable>
				<xsl:text>! </xsl:text><xsl:value-of select="$lCellContent" />
				<xsl:if test="position() &lt; last()"><xsl:text> !</xsl:text></xsl:if>
			</xsl:for-each>
			<xsl:for-each select="./td">
				<xsl:variable name="lCellContent">
					<xsl:call-template name="get_special_parse_node_text_template">
						<xsl:with-param name="pNode" select="."></xsl:with-param>
					</xsl:call-template>
				</xsl:variable>
				<xsl:text>| </xsl:text><xsl:value-of select="$lCellContent" />
				<xsl:if test="position() &lt; last()"><xsl:text> |</xsl:text></xsl:if>
			</xsl:for-each>
		</xsl:for-each><xsl:text>
|}</xsl:text>
	</xsl:template>
	
	<!-- 
		Темплейт за взимане на заглавието
		Ako ima sashtestvuvashta stranica s tova ime sled nego se dobavya "\according_to_Stoev_et_al_2011":
	
	Taka title shte stane:
	
	<title>Sinocallipus\according_to_Stoev_et_al_2011</title>
	
	
	Algoritama na obrazuvane na strunga e sledniya:
	
	ako statiyata ima samo edin avtor (samo edin tag <article-meta><contrib-group><contrib>)
										
		\according_to_<article-meta><contrib-group><contrib><name><surname>Stoev</surname>_<pub-date pub-type="epub"><year>2011</year></pub-date>
	
	ako statiyata ima dvama ili poveche avtori (t.e. dva ili povecha taga <article-meta><contrib-group><contrib>)
	
		\according_to_<article-meta><contrib-group><contrib><name><surname>Stoev</surname>_et_al_<pub-date pub-type="epub"><year>2011</year></pub-date>
	-->
	<xsl:template name="buildPageTitle">
		<xsl:param name="pTitle"></xsl:param>
		<xsl:param name="pArticleNode"></xsl:param>
		
		<xsl:variable name="lTemp" select="php:function('checkForDuplicatePageTitle', string($pTitle))"></xsl:variable>
		<xsl:variable name="lArticlePubYear" select="$pArticleNode/front/article-meta/pub-date[@pub-type='epub']/year" />
		<xsl:variable name="lArticleAuthorsSurnames" select="$pArticleNode/front/article-meta/contrib-group/contrib[@contrib-type='author']/name/surname"></xsl:variable>		
		<xsl:choose>
			<xsl:when test="$lTemp &gt; 0">
				<xsl:value-of select="$pTitle"></xsl:value-of>
			</xsl:when>
			<xsl:otherwise>
				<xsl:value-of select="$pTitle"></xsl:value-of><xsl:text>\according_to_</xsl:text><xsl:value-of select="$lArticleAuthorsSurnames[1]" />
				<xsl:if test="count($lArticleAuthorsSurnames) &gt; 1"><xsl:text>_et_al</xsl:text></xsl:if>
				<xsl:text>_</xsl:text><xsl:value-of select="$lArticlePubYear" />
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>
</xsl:stylesheet>