<?xml version='1.0'?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:tp="http://www.plazi.org/taxpub"  xmlns:php="http://php.net/xsl" exclude-result-prefixes="php">
	<xsl:output method="xml" encoding="UTF-8" indent="yes" doctype-public="-//TaxonX//DTD Taxonomic Treatment Publishing DTD v0 20100105//EN" doctype-system="tax-treatment-NS0.dtd" />
	<xsl:key name="materialType" match="object[@object_id='37']" use="./field[@id='209']/value/@value_id"></xsl:key>
	
	<xsl:template match="/document">
		<article>
			<front>
		        <journal-meta>
		            <journal-id journal-id-type="publisher-id">PUBLISHER_ID</journal-id>
		            <journal-title-group>
		                <journal-title xml:lang="en">JOURNAL_TITLE</journal-title>
		                <abbrev-journal-title xml:lang="en">ABBREV_JOURNAL_TITLE</abbrev-journal-title>
		            </journal-title-group>
		            <issn pub-type="ppub">PPUB</issn>
		            <issn pub-type="epub">EPUB</issn>
		            <publisher>
		                <publisher-name>PUBLISHER</publisher-name>
		            </publisher>
		        </journal-meta>
		        <xsl:call-template name="metadata">
					<xsl:with-param name="pNode" select="/document/objects/object[@object_id='14']"></xsl:with-param>
				</xsl:call-template>
				
			</front>
			<body>
				<xsl:apply-templates select="/document/objects/object" mode="bodySections"></xsl:apply-templates>
				<figures>
					<xsl:apply-templates select="/document/figures/figure" mode="figures"></xsl:apply-templates>				
				</figures>
			</body>
			<back>
				<xsl:apply-templates select="/document/objects/object" mode="articleBack"></xsl:apply-templates>				
			</back>			
		</article>	
	</xsl:template>
	
	<!-- 
		Темплейт за метаданните
	 -->
	<xsl:template name="metadata">
		<xsl:param name="pNode" />
		
		<article-meta>
			<article-id pub-id-type="doi">DOI</article-id>
			<xsl:call-template name="metadataTitleGroup">
				<xsl:with-param name="pNode" select="$pNode/object[@object_id='9']/field[@id='3']"></xsl:with-param>
			</xsl:call-template>
			<contrib-group>
				<xsl:apply-templates select="$pNode//object[@object_id='8']" mode="contibutor"></xsl:apply-templates>				
			</contrib-group>
			<xsl:apply-templates select="$pNode//object[@object_id='8']/object[@object_id='5']" mode="contibutorAff"></xsl:apply-templates>
			<author-notes>
				<fn fn-type="corresp">
                    <p><xsl:text>Corresponding author:</xsl:text> 
                    	<xsl:for-each select="$pNode//object[@object_id='8' or @object_id='12'][field[@id='15']/value[@value_id='1']]" >
                    		<xsl:apply-templates select="." mode="correspondingAuthor"></xsl:apply-templates>
                    		<xsl:if test="position()!=last()">
                    			<xsl:text>, </xsl:text>
                    		</xsl:if>
                    	</xsl:for-each>
                    </p>
                </fn>
				<fn fn-type="edited-by">
                    <p>ACADEMIC EDITOR</p>
                </fn>
			</author-notes>
			<pub-date pub-type="collection">
                <year>YEAR</year>
            </pub-date>
            <pub-date pub-type="epub">
                <day>DAY</day>
                <month>MONTH</month>
                <year>YEAR</year>
            </pub-date>
            <issue>ISSUE</issue>
            <fpage>FPAGE</fpage>
            <lpage>LPAGE</lpage>
            <history>
                <date date-type="received">
                    <day>DAY</day>
                    <month>MONTH</month>
                    <year>YEAR</year>
                </date>
                <date date-type="accepted">
                    <day>DAY</day>
                    <month>MONTH</month>
                    <year>YEAR</year>
                </date>
            </history>
            <permissions>
                <copyright-statement>
					<xsl:for-each select="$pNode//object[@object_id='8']" >                   		
                   		<xsl:apply-templates select="./field[@id='6']" mode="formatting"/><xsl:text> </xsl:text>
						<xsl:apply-templates select="./field[@id='8']" mode="formatting"/>
                   		<xsl:if test="position()!=last()">
                   			<xsl:text>, </xsl:text>
                   		</xsl:if>
                   	</xsl:for-each>
				</copyright-statement>
                <license license-type="creative-commons-attribution" xlink:href="http://creativecommons.org/licenses/by/3.0" xlink:type="simple">
                    <license-p>This is an open access article distributed under the terms of the Creative Commons Attribution License, which permits unrestricted use, distribution, and reproduction in any medium, provided the original author and source are credited.</license-p>
                </license>
            </permissions>
            <self-uri content-type="lsid" xlink:type="simple">SELF_URI</self-uri>
            <abstract>
                <label>Abstract</label>
                <xsl:apply-templates select="$pNode//object[@object_id='15']/field[@id='18']" mode="formatting"/>                
            </abstract>
            <xsl:apply-templates select="$pNode//object[@object_id='15']/field[@id='19']" mode="keywords"/>
		</article-meta>	
	</xsl:template>
	
	<!-- 
		Темплейт за заглавието
	 -->
	<xsl:template name="metadataTitleGroup">
		<xsl:param name="pNode" />
		
		<title-group>
             <article-title>
                 <xsl:apply-templates select="$pNode" mode="formatting"/>
             </article-title>
         </title-group>
	</xsl:template>	
	
	<xsl:template match="b|i|u|strong|em|sup|sub" mode="formatting">
		<xsl:variable name="lNodeName" select="php:function('getFormattingNodeRealNameForPmt', string(local-name(.)))"></xsl:variable>
		<xsl:element name="{$lNodeName}">
			<xsl:apply-templates mode="formatting"></xsl:apply-templates>
		</xsl:element>
	</xsl:template>
	
	<!-- Темплейт за автор -->
	<xsl:template match="object[@object_id='8']" mode="contibutor">
		<contrib xlink:type="simple">			
			<xsl:attribute name="contrib-type">author</xsl:attribute>
			<name name-style="western">
                <surname><xsl:apply-templates select="./field[@id='8']" mode="formatting"></xsl:apply-templates></surname>
                <given-names><xsl:apply-templates select="./field[@id='6']" mode="formatting"></xsl:apply-templates></given-names>
            </name>
            <xsl:for-each select="./object[@object_id='5']" >
				<xsl:variable name="lCurrentNode" select="." />
				<xsl:variable name="lAffId" select="php:function('getContributorAffId', string($lCurrentNode/@instance_id))"></xsl:variable>
				<xref ref-type="aff" rid="A1">
					<xsl:attribute name="rid">A<xsl:value-of select="$lAffId"></xsl:value-of></xsl:attribute>
					<xsl:value-of select="$lAffId"></xsl:value-of>					
				</xref>				
			</xsl:for-each>                        
		</contrib>
	</xsl:template>
	
	<!-- Темплейт за адрес на автор -->
	<xsl:template match="*" mode="contibutorAff">
		<xsl:variable name="lAffId" select="php:function('getContributorAffId', string(./@instance_id))"></xsl:variable>
		<aff>
			<xsl:attribute name="id">A<xsl:value-of select="$lAffId"></xsl:value-of></xsl:attribute>
            <label><xsl:value-of select="$lAffId"></xsl:value-of></label>            
            <xsl:apply-templates select="./field[@id='9']" mode="formatting"/><xsl:text>, </xsl:text>
            <xsl:apply-templates select="./field[@id='10']" mode="formatting"/><xsl:text>, </xsl:text>
            <xsl:apply-templates select="./field[@id='11']" mode="formatting"/>
        </aff>
	</xsl:template>
	
	<!-- Темплейт показване на corresponding author -->
	<xsl:template match="*" mode="correspondingAuthor">
		<xsl:apply-templates select="./field[@id='6']" mode="formatting"/><xsl:text> </xsl:text>
		<xsl:apply-templates select="./field[@id='8']" mode="formatting"/><xsl:text> (</xsl:text>
		<email xlink:type="simple"><xsl:apply-templates select="./field[@id='4']" mode="formatting"/></email>
		<xsl:text>)</xsl:text>
	</xsl:template>
	
	<xsl:template match="*" mode="keywords">
		<kwd-group>
        	<label>Keywords</label>
        	<xsl:variable name="lKeywords" select="php:function('getAllKeywordsFromText', string(.))"></xsl:variable>
			<xsl:for-each select="$lKeywords/kwd">
				<kwd>
					<xsl:apply-templates select="." mode="formatting"/>
				</kwd>
			</xsl:for-each>		
        </kwd-group>
	</xsl:template>
	
	<!-- Default-ен празен темплейт.
		Секциите които искаме да мачнем ще ги специфицираме ръчно
	 -->
	<xsl:template match="*" mode="bodySections"></xsl:template>
	
	<!-- Introduction -->
	<xsl:template match="object[@object_id='16']" mode="bodySections">
		<xsl:variable name="lSecTitle">Introduction</xsl:variable>
		<sec>		
			<xsl:attribute name="sec-type"><xsl:value-of select="$lSecTitle"></xsl:value-of></xsl:attribute>
			<title><xsl:value-of select="$lSecTitle"></xsl:value-of></title>
			<xsl:apply-templates select="./field[@id='20']" mode="formatting"/>			
			<xsl:apply-templates mode="bodySubsection"/>
		</sec>
				
	</xsl:template>
	
	<!-- Material and Methods -->
	<xsl:template match="object[@object_id='18']" mode="bodySections">
		<xsl:variable name="lSecTitle">Material and methods</xsl:variable>
		<sec>		
			<xsl:attribute name="sec-type"><xsl:value-of select="$lSecTitle"></xsl:value-of></xsl:attribute>
			<title><xsl:value-of select="$lSecTitle"></xsl:value-of></title>
			<xsl:apply-templates select="./field[@id='22']" mode="formatting"/>			
			<xsl:apply-templates mode="bodySubsection"/>
		</sec>
				
	</xsl:template>
	
	<!-- Data resources -->
	<xsl:template match="object[@object_id='17']" mode="bodySections">
		<xsl:variable name="lSecTitle">Data resources</xsl:variable>
		<sec>		
			<xsl:attribute name="sec-type"><xsl:value-of select="$lSecTitle"></xsl:value-of></xsl:attribute>
			<title><xsl:value-of select="$lSecTitle"></xsl:value-of></title>
			<xsl:apply-templates select="./field[@id='21']" mode="formatting"/>			
			<xsl:apply-templates mode="bodySubsection"/>
		</sec>
				
	</xsl:template>
	
	<!-- Results -->
	<xsl:template match="object[@object_id='19']" mode="bodySections">
		<xsl:variable name="lSecTitle">Results</xsl:variable>
		<sec>		
			<xsl:attribute name="sec-type"><xsl:value-of select="$lSecTitle"></xsl:value-of></xsl:attribute>
			<title><xsl:value-of select="$lSecTitle"></xsl:value-of></title>
			<xsl:apply-templates select="./field[@id='23']" mode="formatting"/>			
			<xsl:apply-templates mode="bodySubsection"/>
		</sec>
				
	</xsl:template>
	
	
	
	<!-- Подсекции - старт -->
	<!-- Default-ен празен темплейт.
		Субсекциите които искаме да мачнем ще ги специфицираме ръчно
	 -->
	<xsl:template match="*" mode="bodySubsection"></xsl:template>
	
	<!-- При субсекциите показваме title и content
	 -->
	<xsl:template match="object[@object_id='71']" mode="bodySubsection">
		<xsl:variable name="lSecTitle"><xsl:apply-templates select="./field[@id='211']" mode="formatting"/></xsl:variable>
		<sec>
			<xsl:attribute name="sec-type"><xsl:value-of select="$lSecTitle"></xsl:value-of></xsl:attribute>
			<title><xsl:value-of select="$lSecTitle"></xsl:value-of></title>
			<xsl:apply-templates select="./field[@id='212']" mode="formatting"/>
		</sec>
	</xsl:template>
	<!-- Подсекции - край -->
	
	
	<!-- Default-ен празен темплейт.
		Обработваме само обектите, които ни трябват
	 -->
	<xsl:template match="*" mode="articleBack"></xsl:template>
	
	<!-- Аcknowledgements
	 -->
	<xsl:template match="object[@object_id='57']" mode="articleBack">
		<xsl:variable name="lSecTitle">Аcknowledgements</xsl:variable>
		<ack>			
			<title><xsl:value-of select="$lSecTitle"></xsl:value-of></title>
			<xsl:apply-templates select="./field[@id='223']" mode="formatting"/>
			<xsl:apply-templates mode="bodySubsection"/>
		</ack>
	</xsl:template>
	
	<!-- Identification keys -->
	<xsl:template match="object[@object_id='24']" mode="bodySections">
		<xsl:variable name="lSecTitle">Identification keys</xsl:variable>
		<sec>		
			<xsl:attribute name="sec-type"><xsl:value-of select="$lSecTitle"></xsl:value-of></xsl:attribute>
			<title><xsl:value-of select="$lSecTitle"></xsl:value-of></title>						
			<xsl:apply-templates select="//object[@object_id='23']" mode="singleIdentificationKey"/>
		</sec>
				
	</xsl:template>
	
	<!-- Single identification key -->
	<xsl:template match="*" mode="singleIdentificationKey">
		<xsl:variable name="lSecTitle"><xsl:apply-templates select="./field[@id='31']" mode="formatting"/></xsl:variable>
		<sec>		
			<xsl:attribute name="sec-type"><xsl:value-of select="$lSecTitle"></xsl:value-of></xsl:attribute>
			<title><xsl:value-of select="$lSecTitle"></xsl:value-of></title>						
			<xsl:apply-templates select="//object[@object_id='32']" mode="singleIdentificationKey"/>
			<table-wrap content-type="key" position="anchor" orientation="portrait">
				<table>
					<tbody>
						<xsl:for-each select="//object[@object_id='22']">
							<xsl:call-template name="identificationKeyCouplet">
								<xsl:with-param name="pNode" select="."></xsl:with-param>
								<xsl:with-param name="pNum" select="position()"></xsl:with-param>
							</xsl:call-template>
						</xsl:for-each>						
					</tbody>
				</table>
			</table-wrap>
		</sec>
	
	</xsl:template>
	
	<xsl:template match="*" mode="identificationKeyCouplet" name="identificationKeyCouplet">
		<xsl:param name="pNode"></xsl:param>
		<xsl:param name="pNum"></xsl:param>
		<tr>
			<td rowspan="1" colspan="1"><xsl:value-of select="$pNum"></xsl:value-of></td>
			<td rowspan="1" colspan="1"><xsl:apply-templates select="$pNode/field[@id='34']" mode="formatting"/></td>
			<td rowspan="1" colspan="1"><xsl:apply-templates select="$pNode/field[@id='35']" mode="formatting"/><xsl:apply-templates select="$pNode/field[@id='36']" mode="formatting"/></td>
		</tr>
		<tr>
			<td rowspan="1" colspan="1">–</td>
			<td rowspan="1" colspan="1"><xsl:apply-templates select="$pNode/field[@id='37']" mode="formatting"/></td>
			<td rowspan="1" colspan="1"><xsl:apply-templates select="$pNode/field[@id='38']" mode="formatting"/><xsl:apply-templates select="$pNode/field[@id='39']" mode="formatting"/></td>
		</tr>
	
	</xsl:template>
	
	<!-- References
	 -->
	<xsl:template match="object[@object_id='21']" mode="articleBack">
		<xsl:variable name="lSecTitle">References</xsl:variable>
		<ref-list>			
			<title><xsl:value-of select="$lSecTitle"></xsl:value-of></title>			
			<xsl:apply-templates mode="articleBack"/>
		</ref-list>
	</xsl:template>
	
	<!-- Single reference
	 -->
	<xsl:template match="object[@object_id='20']" mode="articleBack">		
		<xsl:variable name="lRefId" select="php:function('getReferenceId', string(./@instance_id))"></xsl:variable>
		<ref>	
			<xsl:attribute name="id">B<xsl:value-of select="$lRefId"></xsl:value-of></xsl:attribute>	
			<mixed-citation xlink:type="simple">
				<person-group>
					<xsl:apply-templates select="./field[@id='24']" mode="formatting"/>
				</person-group>
				<xsl:text> (</xsl:text>
				<year><xsl:apply-templates select="./field[@id='25']" mode="formatting"/></year><xsl:text>) </xsl:text>
				<article-title><xsl:apply-templates select="./field[@id='26']" mode="formatting"/></article-title><xsl:text> </xsl:text>
				<source><xsl:apply-templates select="./field[@id='243']" mode="formatting"/></source><xsl:text> </xsl:text>
				<volume><xsl:apply-templates select="./field[@id='27']" mode="formatting"/></volume><xsl:text>: </xsl:text>
				<fpage><xsl:apply-templates select="./field[@id='28']" mode="formatting"/></fpage><xsl:text>-</xsl:text>
				<lpage><xsl:apply-templates select="./field[@id='29']" mode="formatting"/></lpage><xsl:text>. </xsl:text>
				<ext-link ext-link-type="uri" xlink:type="simple">
					<xsl:variable name="lLinkContent"><xsl:apply-templates select="./field[@id='30']" mode="formatting"/></xsl:variable>
					<xsl:attribute name="xlink:href"><xsl:value-of select="$lLinkContent" disable-output-escaping="no"></xsl:value-of></xsl:attribute>
					<xsl:value-of select="$lLinkContent" disable-output-escaping="yes"></xsl:value-of>
				</ext-link>				
			</mixed-citation>
		</ref>
	</xsl:template>
	
	<!-- Supplementary files
	 -->
	<xsl:template match="object[@object_id='56']" mode="articleBack">
		<xsl:variable name="lSecTitle">Supplementary material</xsl:variable>
		<sec>			
			<xsl:attribute name="sec-type"><xsl:value-of select="$lSecTitle"></xsl:value-of></xsl:attribute>
			<title><xsl:value-of select="$lSecTitle"></xsl:value-of></title>			
			<xsl:apply-templates mode="articleBack"/>
		</sec>
	</xsl:template>
	
	<!-- Single supplementary material
	 -->
	<xsl:template match="object[@object_id='55']" mode="articleBack">		
		<p><xsl:apply-templates select="./field[@id='214']" mode="formatting"/></p>
		
		<xsl:variable name="lUrls" select="php:function('getAllUrlsFromText', string(./field[@id='217']))"></xsl:variable>
		<xsl:for-each select="$lUrls/url">
			<xsl:variable name="lLinkContent" select="."></xsl:variable>
			<p>
				<ext-link ext-link-type="uri" xlink:type="simple">					
					<xsl:attribute name="xlink:href"><xsl:value-of select="$lLinkContent" disable-output-escaping="no"></xsl:value-of></xsl:attribute>
					<xsl:value-of select="$lLinkContent" disable-output-escaping="yes"></xsl:value-of>
				</ext-link>
			</p>
		</xsl:for-each>					
	</xsl:template>
	
	<!-- Systematics -->
	<xsl:template match="object[@object_id='54']" mode="bodySections">
		<xsl:variable name="lSecTitle">Systematics</xsl:variable>
		<sec>		
			<xsl:attribute name="sec-type"><xsl:value-of select="$lSecTitle"></xsl:value-of></xsl:attribute>
			<title><xsl:value-of select="$lSecTitle"></xsl:value-of></title>
			<xsl:apply-templates select="./field[@id='40']" mode="formatting"/>			
			<xsl:apply-templates mode="bodySubsection"/>
			<xsl:apply-templates select="./object[@object_id='41']" mode="taxonTreatment"/>
		</sec>
				
	</xsl:template>

	<!-- Taxon treatment -->
	<xsl:template match="*" mode="taxonTreatment">
		<xsl:variable name="lTreatmentNode" select="."></xsl:variable>
		<tp:taxon-treatment>
			<tp:nomenclature>
				<tp:taxon-name>
					<xsl:apply-templates select=".//object[@object_id='70' or @object_id='43']" mode="taxonTreatmentName"/>
				</tp:taxon-name>
				<tp:taxon-status xlink:type="simple">sp. n.</tp:taxon-status>
				<xsl:apply-templates select=".//object[@object_id='39']" mode="TTExternalLinks"/>				
				<xsl:if test="count(.//object[@object_id='68'])">
					<tp:nomenclature-citation-list>
						<xsl:apply-templates select=".//object[@object_id='63' or @object_id='69']" mode="TTNOriginalCitation"/>
					</tp:nomenclature-citation-list>
				</xsl:if>
			</tp:nomenclature>	
			<xsl:apply-templates select=".//object[@object_id='66' or @object_id='65']" mode="TTNGenusAndGenera"/>		
			<xsl:if test="count(.//object[@object_id='37']) &gt; 0">
				<tp:treatment-sec sec-type="Type material">	
					<title>Type material</title>	
					<!-- Обикаляме всички групи материали -->
					<xsl:for-each select="//object[@object_id='37'][generate-id()=generate-id(key('materialType', ./field[@id='209']/value/@value_id))]">
						<xsl:variable name="lMaterialTypeId" select="./field[@id='209']/value/@value_id"></xsl:variable>
						<xsl:variable name="lMaterialTypeName" select="./field[@id='209']/value"></xsl:variable>
						<!-- Ако имаме материали от този тип -->
						<xsl:if test="count($lTreatmentNode//object[@object_id='37'][field[@id='209']/value[@value_id=$lMaterialTypeId]]) &gt; 0">
							<tp:treatment-sec>
								<xsl:attribute name="sec-type"><xsl:value-of select="$lMaterialTypeName"></xsl:value-of></xsl:attribute>
								<title><xsl:value-of select="$lMaterialTypeName"></xsl:value-of></title>
									<xsl:apply-templates select="$lTreatmentNode//object[@object_id='37'][field[@id='209']/value[@value_id=$lMaterialTypeId]]" mode="treatmentMaterial"></xsl:apply-templates>									
							</tp:treatment-sec>
						</xsl:if>						
					</xsl:for-each>			
				</tp:treatment-sec>
			</xsl:if>
			<xsl:apply-templates select=".//object[@object_id='51' or @object_id='74']/object" mode="taxonTreatmentSections"/>
		</tp:taxon-treatment>
	</xsl:template>
	
	<!--  Taxon treatment external link -->
	<xsl:template match="*" mode="TTExternalLinks">
		<object-id xlink:type="simple"><xsl:apply-templates select="./field[@id='53']" mode="formatting"/></object-id>
	</xsl:template>
	
	
	
	<!-- Taxon family name
	 -->
	<xsl:template match="object[@object_id='70']" mode="taxonTreatmentName">
		<tp:taxon-name-part taxon-name-part-type="family"><xsl:apply-templates select="./field[@id='241']" mode="formatting"/></tp:taxon-name-part>
	</xsl:template>
	
	<!-- Taxon species name
	 -->
	<xsl:template match="object[@object_id='43']" mode="taxonTreatmentName">
		<tp:taxon-name-part taxon-name-part-type="genus"><xsl:apply-templates select="./field[@id='48']" mode="formatting"/></tp:taxon-name-part>
		<tp:taxon-name-part taxon-name-part-type="species"><xsl:apply-templates select="./field[@id='49']" mode="formatting"/></tp:taxon-name-part>
	</xsl:template>
	
	<!-- Treatment material -->
	<xsl:template match="*" mode="treatmentMaterial">
		<material-citation>
			<xsl:apply-templates select=".//object[@object_id &gt; 24 and @object_id &lt; 32]/field[value != '']" mode="treatmentMaterialField"></xsl:apply-templates>
		</material-citation>	
	</xsl:template>
	
	<!-- Treatment material field -->
	<xsl:template match="*" mode="treatmentMaterialField">
		<named-content>
			<xsl:attribute name="content-type">dwc:<xsl:value-of select="./@field_name"></xsl:value-of></xsl:attribute>
			<xsl:apply-templates select="./value" mode="formatting"/>	
		</named-content>	
	</xsl:template>
	
	
	<!-- TaxonTreatmentSection -->
	<xsl:template match="*" mode="taxonTreatmentSections">
		<xsl:variable name="lSecTitle">
			<xsl:choose>
				<xsl:when test="count(./field[@id='211']) &gt; 0">
					<xsl:apply-templates select="./field[@id='211']/value" mode="formatting"/>					
				</xsl:when>
				<xsl:otherwise>
					<xsl:apply-templates select="./field[@id='212']/@field_name" mode="formatting"/>
				</xsl:otherwise>
			</xsl:choose>
		</xsl:variable>
		<tp:treatment-sec>
			<xsl:attribute name="sec-type"><xsl:value-of select="$lSecTitle"></xsl:value-of></xsl:attribute>
			<title><xsl:value-of select="$lSecTitle"></xsl:value-of></title>
			<xsl:apply-templates select="./field[@id='212']/value" mode="formatting"/>
			<xsl:apply-templates select="./object[@object_id='50']" mode="taxonTreatmentSubSections"/>
		</tp:treatment-sec>	
	</xsl:template>
	
	<!-- TaxonTreatmentSubSection -->
	<xsl:template match="*" mode="taxonTreatmentSubSections">
		<xsl:variable name="lSecTitle"><xsl:apply-templates select="./field[@id='211']/value" mode="formatting"/></xsl:variable>
		<tp:treatment-sec>
			<xsl:attribute name="sec-type"><xsl:value-of select="$lSecTitle"></xsl:value-of></xsl:attribute>
			<title><xsl:value-of select="$lSecTitle"></xsl:value-of></title>
			<xsl:apply-templates select="./field[@id='212']/value" mode="formatting"/>			
		</tp:treatment-sec>	
	</xsl:template>
	
	<!-- TT Nomenclature subsequent citation of the new synonym -->	
	<xsl:template match="object[@object_id='63']" mode="TTNOriginalCitation">
		<tp:nomenclature-citation>
			<tp:taxon-name>
				<xsl:apply-templates select=".//object[@object_id='60' or @object_id='61' or @object_id='62']" mode="TTNOriginalCitationName"/>
				<tp:taxon-name-part taxon-name-part-type="Taxon-author"><xsl:apply-templates select="./field[@id='230']/value" mode="formatting"/></tp:taxon-name-part>
       			<tp:taxon-name-part taxon-name-part-type="Year"><xsl:apply-templates select="./field[@id='231']/value" mode="formatting"/></tp:taxon-name-part>
       			<tp:taxon-name-part taxon-name-part-type="Status"><xsl:apply-templates select="./field[@id='232']/value" mode="formatting"/></tp:taxon-name-part> 
			</tp:taxon-name>
			<xsl:if test="count(.//object[@object_id='59'])">
				<comment>
					<xsl:apply-templates select=".//object[@object_id='59']" mode="TTNCitation"/>
				</comment>
			</xsl:if>
		</tp:nomenclature-citation>
	</xsl:template>
	
	<!-- TT Nomenclature subsequent citation tribus name -->	
	<xsl:template match="object[@object_id='60']" mode="TTNOriginalCitationName">
		<tp:taxon-name-part taxon-name-part-type="tribus"><xsl:apply-templates select="./field[@id='227']/value" mode="formatting"/></tp:taxon-name-part>
	</xsl:template>
	
	<!-- TT Nomenclature subsequent citation superfamily name -->	
	<xsl:template match="object[@object_id='61']" mode="TTNOriginalCitationName">
		<tp:taxon-name-part taxon-name-part-type="superfamily"><xsl:apply-templates select="./field[@id='228']/value" mode="formatting"/></tp:taxon-name-part>
	</xsl:template>
	
	<!-- TT Nomenclature subsequent citation subfamily name -->	
	<xsl:template match="object[@object_id='62']" mode="TTNOriginalCitationName">
		<tp:taxon-name-part taxon-name-part-type="subfamily"><xsl:apply-templates select="./field[@id='229']/value" mode="formatting"/></tp:taxon-name-part>
	</xsl:template>
	
	<!-- TT Nomenclature subsequent citation -->	
	<xsl:template match="object[@object_id='69']" mode="TTNOriginalCitation">
		<tp:nomenclature-citation>
			<tp:taxon-name>
				<tp:taxon-name-part taxon-name-part-type="family"><xsl:apply-templates select="./field[@id='240']/value" mode="formatting"/></tp:taxon-name-part>
				<tp:taxon-name-part taxon-name-part-type="Taxon-author"><xsl:apply-templates select="./field[@id='230']/value" mode="formatting"/></tp:taxon-name-part>
       			<tp:taxon-name-part taxon-name-part-type="Year"><xsl:apply-templates select="./field[@id='231']/value" mode="formatting"/></tp:taxon-name-part>
       			<tp:taxon-name-part taxon-name-part-type="Status"><xsl:apply-templates select="./field[@id='232']/value" mode="formatting"/></tp:taxon-name-part> 
			</tp:taxon-name>
			<xsl:if test="count(.//object[@object_id='59'])">
				<comment>
					<xsl:apply-templates select=".//object[@object_id='59']" mode="TTNCitation"/>
				</comment>
			</xsl:if>
		</tp:nomenclature-citation>
	</xsl:template>
	
	<!-- TTN assigned genera -->	
	<xsl:template match="object[@object_id='66']" mode="TTNGenusAndGenera">
		 <tp:treatment-sec sec-type="Assigned genera">
			<title>Assigned genera</title>
			<p>
				<tp:taxon-name>
					<tp:taxon-name-part taxon-name-part-type="genus"><xsl:apply-templates select="./field[@id='235']/value" mode="formatting"/></tp:taxon-name-part>
					<tp:taxon-name-part taxon-name-part-type="Taxon-author"><xsl:apply-templates select="./field[@id='236']/value" mode="formatting"/></tp:taxon-name-part>
	       			<tp:taxon-name-part taxon-name-part-type="Year"><xsl:apply-templates select="./field[@id='237']/value" mode="formatting"/></tp:taxon-name-part>
	       			<tp:taxon-name-part taxon-name-part-type="Status"><xsl:apply-templates select="./field[@id='234']/value" mode="formatting"/></tp:taxon-name-part>
	       			<tp:taxon-name-part taxon-name-part-type="Nomenclature-Status"><xsl:apply-templates select="./field[@id='238']/value" mode="formatting"/></tp:taxon-name-part> 
				</tp:taxon-name>
				<xsl:if test="count(.//object[@object_id='59'])">
					<comment>
						<xsl:apply-templates select=".//object[@object_id='59']" mode="TTNCitation"/>
					</comment>
				</xsl:if>
			</p>
		</tp:treatment-sec>
	</xsl:template>
	
	<!-- TTN type genus -->	
	<xsl:template match="object[@object_id='65']" mode="TTNGenusAndGenera">
		 <tp:treatment-sec sec-type="Type genus">
			<title>Type genus</title>
			<p>
				<tp:taxon-name>
					<tp:taxon-name-part taxon-name-part-type="genus"><xsl:apply-templates select="./field[@id='235']/value" mode="formatting"/></tp:taxon-name-part>
					<tp:taxon-name-part taxon-name-part-type="Taxon-author"><xsl:apply-templates select="./field[@id='236']/value" mode="formatting"/></tp:taxon-name-part>
	       			<tp:taxon-name-part taxon-name-part-type="Year"><xsl:apply-templates select="./field[@id='237']/value" mode="formatting"/></tp:taxon-name-part>
	       			<tp:taxon-name-part taxon-name-part-type="Status"><xsl:apply-templates select="./field[@id='233']/value" mode="formatting"/></tp:taxon-name-part>
				</tp:taxon-name>
				<xsl:if test="count(.//object[@object_id='59'])">
					<comment>
						<xsl:apply-templates select=".//object[@object_id='59']" mode="TTNCitation"/>
					</comment>
				</xsl:if>
			</p>
		</tp:treatment-sec>
	</xsl:template>
	
		
	<!-- TTN Citation -->	
	<xsl:template match="*" mode="TTNCitation">
		<xsl:variable name="lRefId" select="php:function('getReferenceId', string(./field[@id='225']/value/@value_id))"></xsl:variable>
		<xref ref-type="ref">
			<xsl:attribute name="rid">B<xsl:value-of select="$lRefId"></xsl:value-of></xsl:attribute>
			<xsl:value-of select="$lRefId"></xsl:value-of>
		</xref>	
		<p><xsl:apply-templates select="./field[@id='226']/value" mode="formatting"/></p>		
	</xsl:template>
	
	<!-- Single figure -->
	<xsl:template match="*" mode="figures">
		<xsl:variable name="lFigId" select="php:function('getFigureId', string(./@id))"></xsl:variable>
		<fig position="float" orientation="portrait">
			<xsl:attribute name="id">F<xsl:value-of select="$lFigId"></xsl:value-of></xsl:attribute>
           	<label>Figure <xsl:value-of select="$lFigId"/>.</label>      
			<caption>
				<xsl:apply-templates select="./caption" mode="formatting"/>
			</caption>
			<graphic position="float" orientation="portrait" xlink:type="simple">
				<xsl:attribute name="xlink:href"><xsl:value-of select="./url"/></xsl:attribute>	
			</graphic>
		</fig>
	</xsl:template>
</xsl:stylesheet>