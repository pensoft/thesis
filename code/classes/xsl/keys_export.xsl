<?xml version='1.0'?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:dcterms="http://purl.org/dc/terms/" xmlns:geo="http://www.w3.org/2003/01/geo/wgs84_pos#" xmlns:dwc="http://rs.tdwg.org/dwc/dwcore/" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.eol.org/transfer/content/0.3 http://services.eol.org/schema/content_0_3.xsd" xmlns:tp="http://www.plazi.org/taxpub" xmlns:php="http://php.net/xsl" exclude-result-prefixes="xsl xlink xsd dc dcterms geo dwc xsi tp php">
	<xsl:import href="./default.xsl"/>
	<xsl:output method="xml" encoding="UTF-8"/>		
	<xsl:template match="/">		
		<keys>
			<xsl:call-template name="parseKeys">					
			</xsl:call-template>	
		</keys>
	</xsl:template>
	
	
	<!-- 
		Za vseki <table-wrap content-type="key"> ot XML na statiya da se generira <key>
	-->
	<xsl:template name="parseKeys">
		<xsl:variable name="lCurrentArticle" select="//article" />
		<xsl:variable name="lKeys" select="$lCurrentArticle//table-wrap[@content-type='key']" />		
		<xsl:variable name="lArticleDoi" select="$lCurrentArticle/front/article-meta/article-id[@pub-id-type='doi']" />
		<xsl:variable name="lArticleId" select="php:function('getArticleIdFromDoi', string($lArticleDoi))" />
		<xsl:variable name="lJournalTitle" select="$lCurrentArticle//journal-meta/journal-title-group/journal-title[@xml:lang='en']" />
		<xsl:variable name="lArticleAuthors" select="$lCurrentArticle/front/article-meta/contrib-group/contrib[@contrib-type='author']/name"></xsl:variable>
		<xsl:variable name="lPubYear" select="$lCurrentArticle/front/article-meta/pub-date[@pub-type='epub']/year"></xsl:variable>
		<xsl:variable name="lJournalUrlTitle" select="php:function('getArticleJournalUrlTitle', string($lArticleId))"></xsl:variable>
		<xsl:variable name="lArticleLink">http://www.pensoft.net/journals/<xsl:value-of select="$lJournalUrlTitle"></xsl:value-of>/article/<xsl:value-of select="$lArticleId"></xsl:value-of>/abstract</xsl:variable>
		<xsl:variable name="lAbstract">
			<xsl:call-template name="getAbstractText">
				<xsl:with-param name="pNode" select="$lCurrentArticle/front/article-meta/abstract"></xsl:with-param>				
			</xsl:call-template>
		</xsl:variable>
		<xsl:variable name="lArticleReference">
			<xsl:call-template name="getArticleReferenceText">
				<xsl:with-param name="pArticleNode" select="$lCurrentArticle"></xsl:with-param>
				<xsl:with-param name="pArticleAuthors" select="$lArticleAuthors"></xsl:with-param>
				<xsl:with-param name="pArticleDoi" select="$lArticleDoi"></xsl:with-param>				
			</xsl:call-template>
		</xsl:variable>
		<xsl:variable name="lCreator">			
			<xsl:choose>
				<xsl:when test="(count($lArticleAuthors[1]/given-names) &gt; 0) or (count($lArticleAuthors[1]/surname) &gt; 0)">
					<xsl:value-of select="$lArticleAuthors[1]/given-names"></xsl:value-of><xsl:text> </xsl:text><xsl:value-of select="$lArticleAuthors[1]/surname"></xsl:value-of>
				</xsl:when>
				<xsl:otherwise>
					<xsl:value-of select="$lArticleAuthors[1]"></xsl:value-of>					
				</xsl:otherwise>
			</xsl:choose>
			<xsl:if test="count($lArticleAuthors) &gt; 1">
				et al.
			</xsl:if>
		</xsl:variable>
		<xsl:variable name="lContributor">
			<xsl:for-each select="$lArticleAuthors">
				<xsl:variable name="lCurrentAuthor" select="." />
				<xsl:choose>
					<xsl:when test="(count($lCurrentAuthor/given-names) &gt; 0) or (count($lCurrentAuthor/surname) &gt; 0)">
						<xsl:value-of select="$lCurrentAuthor/given-names"></xsl:value-of><xsl:text> </xsl:text><xsl:value-of select="$lCurrentAuthor/surname"></xsl:value-of>
					</xsl:when>
					<xsl:otherwise>
						<xsl:value-of select="$lCurrentAuthor"></xsl:value-of>					
					</xsl:otherwise>
				</xsl:choose>
				
				<xsl:if test="position() != last()">
					<xsl:text>, </xsl:text>
				</xsl:if>
			</xsl:for-each>
		</xsl:variable>
		<xsl:variable name="lTaxonomicScope" select="php:function('getKeysExportTaxonomicScope', string($lArticleId))" />
		<xsl:variable name="lGeographicScope" select="php:function('getKeysExportGeographicScope', string($lArticleId))" />
		
		<xsl:for-each select="$lKeys" >				
			<xsl:variable name="lCurrentKey" select="." />
			<xsl:call-template name="singleKeyTemplate">					
				<xsl:with-param name="pKeyNode" select="$lCurrentKey"></xsl:with-param>
				<xsl:with-param name="pJournalTitle" select="$lJournalTitle"></xsl:with-param>
				<xsl:with-param name="pPubYear" select="$lPubYear"></xsl:with-param>
				<xsl:with-param name="pArticleLink" select="$lArticleLink"></xsl:with-param>
				<xsl:with-param name="pCreator" select="$lCreator"></xsl:with-param>
				<xsl:with-param name="pContributor" select="$lContributor"></xsl:with-param>
				<xsl:with-param name="pTaxonomicScope" select="$lTaxonomicScope"></xsl:with-param>
				<xsl:with-param name="pGeographicScope" select="$lGeographicScope"></xsl:with-param>
				<xsl:with-param name="pAbstract" select="$lAbstract"></xsl:with-param>
				<xsl:with-param name="pArticleReference" select="$lArticleReference"></xsl:with-param>
			</xsl:call-template>				
		</xsl:for-each>
	</xsl:template>
	
	<!-- 
		Взимаме текста за референцията на статията. Освен като референция текста се показва и като dcterms:bibliographicCitation в описанието е дистрибуцията
		Текста е еднакъв за всички таксони от една статия
			ZA SADARZHANIETO NA TAGA:
			    
			    Jager P, Kunz D (2010) Palystes kreutzmanni sp. n.  a new hunts ZooKeys 67: 1-9
			    
			    
				 article/article-meta/contrib-group>
					<contrib contrib-type="author" xlink:type="simple">
					    <name name-style="western">
						<surname>Jager</surname>
						<given-names>Peter</given-names>
					    </name>
													</contrib>
					<contrib contrib-type="author" xlink:type="simple">
					    <name name-style="western">
						<surname>Kunz</surname>
						<given-names>Dirk</given-names>
					    </name>
													</contrib>
			     </contrib-group>
			     
						BELEZHKA - <given-names>Dirk</given-names> na avtorite se sakrashtava na parva bukva.
			     
			     
			     article/article-meta/pub-date pub-type="epub">
					<year>2010</year>
			     
			     article/article-meta/article-meta>
									<title-group>
					<article-title>
					    <italic>Palystes kreutzmanni</italic> sp. n.  a new hunts
					</article-title>
					
			      front/journal-meta>
				    <journal-id journal-id-type="publisher-id">ZooKeys</journal-id>
				    
			      article/article-meta/issue>67</issue>
			      article/article-meta/fpage>1</fpage>
			      article/article-meta/lpage>9</lpage>
	-->
	<xsl:template name="getArticleReferenceText">
		<xsl:param name="pArticleNode" />
		<xsl:param name="pArticleAuthors" />
		<xsl:param name="pArticleDoi" />		
		
		<xsl:variable name="lReferenceText">
			<xsl:variable name="lAuthors" select="$pArticleAuthors"></xsl:variable>
			<xsl:variable name="lArticlePubYear" select="$pArticleNode/front/article-meta/pub-date[@pub-type='epub']/year"></xsl:variable>
			<xsl:variable name="lArticleTitle" select="$pArticleNode/front/article-meta/title-group/article-title"></xsl:variable>
			<xsl:variable name="lJournalTitle" select="$pArticleNode/front/journal-meta/journal-id[@journal-id-type='publisher-id']"></xsl:variable>
			<xsl:variable name="lIssueNumber" select="$pArticleNode/front/article-meta/issue"></xsl:variable>
			<xsl:variable name="lFirstPage" select="$pArticleNode/front/article-meta/fpage"></xsl:variable>
			<xsl:variable name="lLastPage" select="$pArticleNode/front/article-meta/lpage"></xsl:variable>
			
			<xsl:for-each select="$lAuthors">
				<xsl:variable name="lCurrentAuthor" select="."></xsl:variable>
				<xsl:variable name="lSurname" select="$lCurrentAuthor/surname"></xsl:variable>
				<xsl:variable name="lGivenNames" select="$lCurrentAuthor/given-names"></xsl:variable>
				<xsl:if test="position() &gt; 1">
					<xsl:text>, </xsl:text>
				</xsl:if>
				<xsl:value-of select="$lSurname"></xsl:value-of>
				<xsl:if test="count($lGivenNames) &gt; 0">
					<xsl:text> </xsl:text>
					<xsl:value-of select="php:function('getNameFirstLetter',  string($lGivenNames))"></xsl:value-of>
				</xsl:if>
			</xsl:for-each>
			<xsl:text> </xsl:text>
			(<xsl:value-of select="$lArticlePubYear"></xsl:value-of>)
			<xsl:text> </xsl:text>
			<xsl:value-of select="normalize-space($lArticleTitle)"></xsl:value-of><xsl:text>. </xsl:text>			
			<xsl:value-of select="$lJournalTitle"></xsl:value-of>
			<xsl:text> </xsl:text>
			<xsl:value-of select="$lIssueNumber"></xsl:value-of>
			<xsl:text>: </xsl:text>
			<xsl:value-of select="$lFirstPage"></xsl:value-of>–<xsl:value-of select="$lLastPage"></xsl:value-of>
			<xsl:text>. doi: &lt;a href="http://dx.doi.org/</xsl:text><xsl:value-of select="$pArticleDoi"></xsl:value-of>
			<xsl:text>" target="_blank"&gt;</xsl:text><xsl:value-of select="$pArticleDoi"></xsl:value-of>
			<xsl:text>&lt;/a&gt;</xsl:text>
		</xsl:variable>
		<xsl:value-of select="normalize-space($lReferenceText)"></xsl:value-of>
	</xsl:template>
	
	<xsl:template name="getAbstractText">
		<xsl:param name="pNode" />
		
		<xsl:variable name="local_name" >
			<xsl:variable name="temp_name" select="local-name($pNode)"></xsl:variable>
			<xsl:call-template name="ToLower">
				<xsl:with-param name="inputString" select="$temp_name"/>
			</xsl:call-template>
		</xsl:variable>
		<xsl:variable name="text_type" select="$pNode/self::text()" />
		<xsl:variable name="element_type" select="$pNode/self::*" />		
		<xsl:choose>
			<xsl:when test="$text_type"><xsl:value-of select="$pNode"></xsl:value-of></xsl:when>
			<xsl:when test="$element_type">
				<xsl:choose>
					<xsl:when test="($local_name='title') or ($local_name='label')"></xsl:when>					
					<xsl:otherwise>								
						<xsl:call-template name="getAbstractElementText">	
							<xsl:with-param name="pNode" select="$pNode"></xsl:with-param>							
						</xsl:call-template>					
					</xsl:otherwise>
				</xsl:choose>
			</xsl:when>
		</xsl:choose>
	</xsl:template>
	
	<xsl:template name="getAbstractElementText">
		<xsl:param name="pNode" />	
			
		<xsl:for-each select="$pNode/child::node()" >
			<xsl:variable name="lCurrentNode" select="." />
			<xsl:call-template name="getAbstractText">	
				<xsl:with-param name="pNode" select="$lCurrentNode"></xsl:with-param>				
			</xsl:call-template>
		</xsl:for-each>
	</xsl:template>
	
	
	<!-- 
		Структурата на key-а може да се види в https://projects.etaligent.net/attachments/461/Keys_-_Mapping.xml
		в issue https://projects.etaligent.net/issues/956
	 -->
	<xsl:template name="singleKeyTemplate">
		<xsl:param name="pKeyNode" />
		<xsl:param name="pJournalTitle" />
		<xsl:param name="pPubYear" />
		<xsl:param name="pArticleLink" />
		<xsl:param name="pCreator" />
		<xsl:param name="pContributor" />
		<xsl:param name="pTaxonomicScope" />
		<xsl:param name="pGeographicScope" />
		<xsl:param name="pAbstract" />
		<xsl:param name="pArticleReference" />
		
		<xsl:variable name="lKeyTitle" select="$pKeyNode/preceding-sibling::title" />
		<xsl:variable name="lGraphics" select="$pKeyNode//graphic" />
		<xsl:variable name="lImagery" >
			<xsl:choose>
				<xsl:when test="count($lGraphics) &gt; 3">Richly illustrated</xsl:when>
				<xsl:when test="count($lGraphics) &gt; 0">Some illustrations</xsl:when>
				<xsl:otherwise>No illustrations (text only)</xsl:otherwise>
			</xsl:choose>
		</xsl:variable>
		<xsl:text>&#xa;</xsl:text>
		<xsl:element name="key">	
			<title><xsl:value-of select="normalize-space($lKeyTitle)"/></title>
		    <url><xsl:value-of select="normalize-space($pArticleLink)"/></url>
		    <description><xsl:value-of select="normalize-space($pAbstract)"/></description>
		    <creator><xsl:value-of select="normalize-space($pCreator)"/></creator>
		    <contributor><xsl:value-of select="normalize-space($pContributor)"/></contributor>
		    <publisher><xsl:value-of select="normalize-space($pJournalTitle)"/></publisher>
		    <publishedyear><xsl:value-of select="normalize-space($pPubYear)"/></publishedyear>
		    <taxonomicscope><xsl:value-of select="normalize-space($pTaxonomicScope)"/></taxonomicscope>
		    <geographicscope><xsl:value-of select="normalize-space($pGeographicScope)"/></geographicscope>
		    <submitterisowner>true</submitterisowner>
		    <CCRights href="http://creativecommons.org/licenses/by/3.0/">
		      <text>Creative Commons Attribution 3.0 (CC-BY).</text>
		    </CCRights>
		    <keytype>Html key (static)</keytype>
		    <accessibility>Freely accessible</accessibility>
		    <vocabulary>Much complex, technical language</vocabulary>
		    <technicalskills>High technical skills required</technicalskills>
		    <imagery><xsl:value-of select="normalize-space($lImagery)"/></imagery>
		    <citation><xsl:value-of select="$pArticleReference"/></citation>
		</xsl:element>
	</xsl:template>

</xsl:stylesheet>