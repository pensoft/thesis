<?xml version='1.0'?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:dcterms="http://purl.org/dc/terms/" xmlns:geo="http://www.w3.org/2003/01/geo/wgs84_pos#" xmlns:dwc="http://rs.tdwg.org/dwc/dwcore/" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.eol.org/transfer/content/0.3 http://services.eol.org/schema/content_0_3.xsd" xmlns:tp="http://www.plazi.org/taxpub" xmlns:php="http://php.net/xsl">
	<xsl:import href="./default.xsl"/>
	<xsl:output method="xml" encoding="UTF-8"/>	
	<xsl:variable name="gPicUrl">http://www.pensoft.net/J_FILES/{journal_id}/articles/{article_id}/export.php_files/{file_name}</xsl:variable>
	
	<xsl:template match="/">		
		<images xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">						
			<xsl:call-template name="parseArticles">					
			</xsl:call-template>	
		</images>
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
		<xsl:variable name="lJournalTitle" select="$pCurrentArticle/front/journal-meta/journal-id[@journal-id-type='publisher-id']" />		
		<xsl:variable name="lArticleDoi" select="$pCurrentArticle/front/article-meta/article-id[@pub-id-type='doi']" />
		<xsl:variable name="lArticleId"><xsl:value-of select="php:function('getArticleIdFromDoi',  string($lArticleDoi))" /></xsl:variable>
		<xsl:variable name="lJournalId"><xsl:value-of select="php:function('getJournalIdFromJournalTitle',  string($lJournalTitle))" /></xsl:variable>
		
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
						<xsl:with-param name="pJournalTitle" select="$lJournalTitle"></xsl:with-param>
						<xsl:with-param name="pArticleNode" select="$pCurrentArticle"></xsl:with-param>
						<xsl:with-param name="pJournalId" select="$lJournalId"></xsl:with-param>						
						<xsl:with-param name="pArticleId" select="$lArticleId"></xsl:with-param>
					</xsl:call-template>
				</xsl:otherwise>
			</xsl:choose>
		</xsl:for-each>
	</xsl:template>
		
	<!-- 
		Темплейт, който връща xml-a за картинките за текущия таксон		
	-->
	<xsl:template name="singleTaxonTemplate">
		<xsl:param name="pTaxonNode" />
		<xsl:param name="pJournalTitle" />		
		<xsl:param name="pArticleNode" />
		<xsl:param name="pJournalId"/>
		<xsl:param name="pArticleId"/>
		
		<xsl:variable name="lTreatmentFigs" select="$pTaxonNode//xref[@ref-type='fig'][count(./ancestor::*[name()='tp:taxon-treatment'])=(count($pTaxonNode/ancestor-or-self::*[name()='tp:taxon-treatment']))]" />		
		<xsl:choose>
			<xsl:when test="count($lTreatmentFigs) &gt; 0">									
				<xsl:for-each select="$lTreatmentFigs" >	
					<xsl:variable name="lCurrentXref" select="." />
					<xsl:call-template name="taxonTreatmentFigTemplate">					
						<xsl:with-param name="pXrefToFigNode" select="$lCurrentXref"></xsl:with-param>
						<xsl:with-param name="pArticleNode" select="$pArticleNode"></xsl:with-param>												
						<xsl:with-param name="pJournalId" select="$pJournalId"></xsl:with-param>						
						<xsl:with-param name="pArticleId" select="$pArticleId"></xsl:with-param>
					</xsl:call-template>					
				</xsl:for-each>				
			</xsl:when>
		</xsl:choose>
	</xsl:template>	
	
	<!-- 
		Темплейт, който връща xml-a за текущата картинка
		Изкарваме картинката само ако до сега не сме добавяли картинка със същия URL
	-->
	<xsl:template name="taxonTreatmentFigTemplate">
		<xsl:param name="pXrefToFigNode" />
		<xsl:param name="pArticleNode" />			
		<xsl:param name="pJournalId"/>
		<xsl:param name="pArticleId"/>
		
		<xsl:variable name="lFigId" select="$pXrefToFigNode/@rid"></xsl:variable>
		<xsl:variable name="lFigNode" select="$pArticleNode//fig[@id=$lFigId]"></xsl:variable>
		<xsl:variable name="lPicFileName" select="$lFigNode//graphic/@xlink:href" />
		<xsl:variable name='lFigPicUrl'>
			<xsl:value-of select="php:function('getFigPicUrl',  string($gPicUrl), string($pJournalId), string($pArticleId), string($lPicFileName))"></xsl:value-of>
		</xsl:variable>
		<xsl:variable name="lTest" select="php:function('checkForDuplicatePicUrl', string($lFigPicUrl))"></xsl:variable>
		<xsl:if test="$lPicFileName != '' and $lTest &gt; 0">
			<image>
				<xsl:attribute name="name"><xsl:value-of select="$lPicFileName" /></xsl:attribute>
				<xsl:value-of select="$lFigPicUrl"></xsl:value-of>
			</image>
		</xsl:if>
	</xsl:template>
	
</xsl:stylesheet>