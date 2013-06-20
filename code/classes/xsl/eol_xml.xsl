<?xml version='1.0'?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:dcterms="http://purl.org/dc/terms/" xmlns:geo="http://www.w3.org/2003/01/geo/wgs84_pos#" xmlns:dwc="http://rs.tdwg.org/dwc/dwcore/" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.eol.org/transfer/content/0.1 http://services.eol.org/schema/content_0_1.xsd" xmlns:tp="http://www.plazi.org/taxpub">
	<xsl:import href="./default.xsl"/>
	<xsl:output method="xml" encoding="UTF-8"/>

	<xsl:template match="/">		
		<response xmlns="http://www.eol.org/transfer/content/0.1" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:dcterms="http://purl.org/dc/terms/" xmlns:geo="http://www.w3.org/2003/01/geo/wgs84_pos#" xmlns:dwc="http://rs.tdwg.org/dwc/dwcore/" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.eol.org/transfer/content/0.1 http://services.eol.org/schema/content_0_1.xsd" >
			<xsl:call-template name="parseTaxons">					
			</xsl:call-template>	
		</response>
	</xsl:template>
	
	<xsl:template name="parseTaxons">
		<xsl:variable name="lTaxons" select="./article/body//tp:taxon-treatment" />
		<xsl:variable name="lArticleTitle" select="./article/front/article-meta/title-group/article-title" />
		<xsl:variable name="lArticleDoi">1</xsl:variable>
		<xsl:for-each select="$lTaxons" >				
			<xsl:variable name="lCurrentTaxon" select="." />
			<xsl:call-template name="singleTaxonTemplate">					
				<xsl:with-param name="pTaxonNode" select="$lCurrentTaxon"></xsl:with-param>
				<xsl:with-param name="pArticleTitle" select="$lArticleTitle"></xsl:with-param>
				<xsl:with-param name="pArticleDoi" select="$lArticleDoi"></xsl:with-param>
			</xsl:call-template>				
		</xsl:for-each>
	</xsl:template>
	
	<xsl:template name="singleTaxonTemplate">
		<xsl:param name="pTaxonNode" />
		<xsl:param name="pArticleTitle" />
		<xsl:param name="pArticleDoi" />
		<xsl:variable name="lTreatmentSecs" select="$pTaxonNode/tp:treatment-sec" />
		<xsl:variable name="lDescription" select="$pTaxonNode/description" />
		<xsl:variable name="lTaxonStatusNode" select="$pTaxonNode/tp:nomenclature/tp:taxon-status" />		
		<xsl:variable name="lTaxonName" select="$pTaxonNode/tp:nomenclature/tp:taxon-name" />		
		<xsl:variable name="lIdentifier">zookeys.1.8.sp1</xsl:variable>
		<xsl:variable name="lTaxonStatus">			
			<xsl:variable name="lNormalizedStatus" select="normalize-space($lTaxonStatusNode)" />
			<xsl:call-template name="ToLower">
				<xsl:with-param name="inputString" select="$lNormalizedStatus"/>
			</xsl:call-template>
		</xsl:variable>		
		<!--xsl:if test="$lTaxonStatus='fp.n.'"-->
		<xsl:if test="$lTaxonStatus='fp.n.'">
			<taxon>
				<dc:identifier><xsl:value-of select="$lIdentifier"/></dc:identifier>
				<dc:source>TEST SOURCE</dc:source>
				<dwc:Kingdom>TEST KINGDOM</dwc:Kingdom>
				<dwc:Phylum>TEST PHYLUM</dwc:Phylum>
				<dwc:Class>TEST CLASS</dwc:Class>
				<dwc:Order>TEST ORDER</dwc:Order>
				<dwc:Family>TEST FAMILY</dwc:Family>
				<dwc:ScientificName>
					<xsl:call-template name="get_node_text_template">
						<xsl:with-param name="pNode" select="$lTaxonName"/>
						<xsl:with-param name="pPutSpaces">1</xsl:with-param>
					</xsl:call-template>
				</dwc:ScientificName>
				<dcterms:created>TEST CREATED</dcterms:created>
				<dcterms:modified>TEST MODIFIED</dcterms:modified>
				<reference><xsl:attribute name="doi"><xsl:value-of select="$pArticleDoi"/></xsl:attribute><xsl:value-of select="$pArticleTitle"/></reference>
				<xsl:if test="count($lDescription)>0">
					<xsl:call-template name="taxonDescriptionTemplate">					
						<xsl:with-param name="pDescription" select="$lDescription"></xsl:with-param>
						<xsl:with-param name="pArticleTitle" select="$pArticleTitle"></xsl:with-param>
						<xsl:with-param name="pArticleDoi" select="$pArticleDoi"></xsl:with-param>						
						<xsl:with-param name="pIdentifier" select="$lIdentifier"></xsl:with-param>
					</xsl:call-template>
				</xsl:if>
				<xsl:for-each select="$lTreatmentSecs" >				
					<xsl:variable name="lCurrentSec" select="." />
					<xsl:call-template name="taxonTreatmentSecTemplate">					
						<xsl:with-param name="pSecNode" select="$lCurrentSec"></xsl:with-param>
						<xsl:with-param name="pArticleTitle" select="$pArticleTitle"></xsl:with-param>
						<xsl:with-param name="pArticleDoi" select="$pArticleDoi"></xsl:with-param>						
						<xsl:with-param name="pIdentifier" select="$lIdentifier"></xsl:with-param>
					</xsl:call-template>				
				</xsl:for-each>
			</taxon>
		</xsl:if>
	</xsl:template>

	<!-- 
		Темплейт, който връща текстовото съдържание дадена секция
		Обработват се само distribution секциите
	-->
	<xsl:template name="taxonTreatmentSecTemplate">
		<xsl:param name="pSecNode" />
		<xsl:param name="pArticleTitle" />
		<xsl:param name="pArticleDoi" />				
		<xsl:param name="pIdentifier" />		
		<xsl:variable name="lTaxonLabelNode" select="$pSecNode//label" />		
		<xsl:variable name="lTaxonLabel">
			<xsl:variable name="lNormalizedLabel" select="normalize-space($lTaxonLabelNode)" />
			<xsl:call-template name="ToLower">
				<xsl:with-param name="inputString" select="$lNormalizedLabel"/>
			</xsl:call-template>
		</xsl:variable>				
		<xsl:if test="$lTaxonLabel='distribution'">
			<dataObject>
				<dc:identifier><xsl:value-of select="$pIdentifier"/>_distribution</dc:identifier>
				<dataType>http://purl.org/dc/dcmitype/Text</dataType>
				<mimeType>text/html</mimeType>				
				<dc:title xml:lang="en">Distribution</dc:title>
				<dc:language>en</dc:language>
				<license>http://creativecommons.org/licenses/by/3.0/</license>
				<audience>Expert users</audience>
				<audience>General public</audience>
				<subject>http://rs.tdwg.org/ontology/voc/SPMInfoItems#Distribution</subject>						
				<reference><xsl:attribute name="doi"><xsl:value-of select="$pArticleDoi"/></xsl:attribute><xsl:value-of select="$pArticleTitle"/></reference>
				<dc:description xml:lang="en">
					<xsl:call-template name="get_node_text_template">
						<xsl:with-param name="pNode" select="$pSecNode"/>
					</xsl:call-template>
				</dc:description>
			</dataObject>
		</xsl:if>
	</xsl:template>
	
	<!-- 
		Темплейт, който връща текстовото съдържание на описанието на taxon-а
	-->
	
	<xsl:template name="taxonDescriptionTemplate">
		<xsl:param name="pDescription" />
		<xsl:param name="pArticleTitle" />
		<xsl:param name="pArticleDoi" />				
		<xsl:param name="pIdentifier" />		
		<dataObject>
				<dc:identifier><xsl:value-of select="$pIdentifier"/>_description</dc:identifier>
				<dataType>http://purl.org/dc/dcmitype/Text</dataType>
				<mimeType>text/html</mimeType>
				<agent role="source"><xsl:value-of select="$pArticleTitle"></xsl:value-of></agent>
				<dc:title xml:lang="en">Description</dc:title>
				<dc:language>en</dc:language>
				<license>http://creativecommons.org/licenses/by/3.0/</license>
				<audience>Expert users</audience>
				<audience>General public</audience>
				<subject>http://rs.tdwg.org/ontology/voc/SPMInfoItems#GeneralDescription</subject>										
				<dc:description xml:lang="en">
					<xsl:call-template name="get_node_text_template">
						<xsl:with-param name="pNode" select="$pDescription"/>
					</xsl:call-template>
				</dc:description>
			</dataObject>
	</xsl:template>

	
	<!-- 
		Темплейт, който връща текстовото съдържание на подадения на даден section node, като не показва в него label-а
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
			<xsl:when test="$lTextType"><xsl:call-template name="replaceSymbolTemplate"><xsl:with-param name="text" select="normalize-space($pNode)"></xsl:with-param><xsl:with-param name="searchSymbol">,</xsl:with-param><xsl:with-param name="replacementSymbol">,&#32;</xsl:with-param></xsl:call-template><xsl:if test="$pPutSpaces"><xsl:text> </xsl:text></xsl:if>
			</xsl:when>
			<xsl:when test="$lElementType">
				<xsl:choose>
					<xsl:when test="$lLocalName='label'"></xsl:when>					
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
</xsl:stylesheet>