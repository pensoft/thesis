<?xml version='1.0'?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:tp="http://www.plazi.org/taxpub"  xmlns:php="http://php.net/xsl" exclude-result-prefixes="php tp xlink xsl">	
	<xsl:template match="/document">
		<xsl:variable name="lContent">
			<div class="P-Article-Preview">
				<xsl:call-template name="journalInfo">
					<xsl:with-param name="pDocumentNode" select="/document/document_info"></xsl:with-param>
				</xsl:call-template>

				<div>
					<xsl:attribute name="instance_id">
						<xsl:value-of select="/document/objects/*[@object_id='14']/*[@object_id='9']/@instance_id"></xsl:value-of>
					</xsl:attribute>
					<xsl:apply-templates select="/document/objects/*[@object_id='14']/*[@object_id='9']/fields/*[@id='3']" mode="articleTitle"></xsl:apply-templates>
					<xsl:call-template name="authors">
						<xsl:with-param name="pDocumentNode" select="/document"></xsl:with-param>
					</xsl:call-template>
				</div>

				<xsl:apply-templates select="/document/objects/*[@object_id='14']/*[@object_id='15']" mode="abstractAndKeywords"></xsl:apply-templates>

				<xsl:apply-templates select="/document/objects/*[@object_id &gt; 0]" mode="bodySections"></xsl:apply-templates>

				<!-- 				<xsl:apply-templates select="/document/figures/figure" mode="figures"/> -->
<!-- 				<xsl:apply-templates select="/document/tables/table" mode="tables"/> -->
				
				<xsl:apply-templates select="/document/objects/*[@object_id='236']" mode="figuresPreview"/>
				<xsl:apply-templates select="/document/objects/*[@object_id='237']" mode="tablesPreview"/>

				<xsl:apply-templates select="/document/objects/*[@object_id &gt; 0]" mode="articleBack"></xsl:apply-templates>
			</div>
		</xsl:variable>
		<xsl:choose>
			<xsl:when test="$gGenerateFullHtml &gt; 0">
				<html>
					<head>
						<link type="text/css" rel="stylesheet" href="/lib/css/article_preview2.css" media="all" title="default"/>
						<xsl:if test="$pMarkContentEditableFields &gt; 0 and $pPutEditableJSAndCss &gt; 0">
							<xsl:variable name="lEditPreviewHead" select="php:function('getEditPreviewHead', string($pDocumentId))"></xsl:variable>
							<xsl:for-each select="$lEditPreviewHead/script|$lEditPreviewHead/link|$lEditPreviewHead/style">
								<xsl:copy-of select="."></xsl:copy-of>
							</xsl:for-each>
						</xsl:if>
						<title></title>
					</head>
					<body>
						<xsl:comment><xsl:value-of select="$pEditableHeaderReplacementText"></xsl:value-of></xsl:comment>
						<div id="previewHolder">
							<xsl:copy-of select="$lContent"/>
						</div>
					</body>
				</html>
			</xsl:when>
			<xsl:otherwise>
				<xsl:copy-of select="$lContent"/>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>

	<!-- Project Description -->
	<xsl:template match="*[@object_id='111']" mode="bodySections">
		<xsl:variable name="lSecTitle" select="./@display_name" />
		<xsl:variable name="lSecSubTitle" select="./fields/*[@id='289']/@field_name" />
		<xsl:variable name="lStudyArea" select="./fields/*[@id='290']/@field_name" />
		<xsl:variable name="lDesignDescription" select="./fields/*[@id='291']/@field_name" />
		<xsl:variable name="lFunding" select="./fields/*[@id='292']/@field_name" />

		<xsl:if test="(./fields/*[@id='289']/value != '') or (./fields/*[@id='290']/value != '') or (./fields/*[@id='291']/value != '') or (./fields/*[@id='292']/value != '')">
			<div class="P-Article-Preview-Block">
				<xsl:attribute name="instance_id"><xsl:value-of select="./@instance_id" /></xsl:attribute>
				<div class="P-Article-Preview-Block-Title"><xsl:value-of select="$lSecTitle" /></div>
				<xsl:if test="./fields/*[@id='289']/value != ''">
					<div class="myfieldHolder">
						<span class="fieldLabel"><xsl:value-of select="$lSecSubTitle" />:&#160;</span>
						<div class="fieldValue">
							<xsl:attribute name="field_id">289</xsl:attribute>
							<!--CALL MARK CONTENT EDITABLE TEMPLATE -->
							<xsl:call-template name="markContentEditableField">
								<xsl:with-param name="pObjectId" select="./@object_id"></xsl:with-param>
								<xsl:with-param name="pFieldId">289</xsl:with-param>
							</xsl:call-template>
							<xsl:apply-templates select="./fields/*[@id='289']/value" mode="formatting"/>
						</div>
					</div>
				</xsl:if>
				<xsl:if test="./fields/*[@id='290']/value != ''">
					<div class="myfieldHolder">
						<span class="fieldLabel">Study area description:&#160;</span>
						<div class="fieldValue">
							<xsl:attribute name="field_id">290</xsl:attribute>
							<!--CALL MARK CONTENT EDITABLE TEMPLATE -->
							<xsl:call-template name="markContentEditableField">
								<xsl:with-param name="pObjectId" select="./@object_id"></xsl:with-param>
								<xsl:with-param name="pFieldId">290</xsl:with-param>
							</xsl:call-template>
							<xsl:apply-templates select="./fields/*[@id='290']/value" mode="formatting"/>
						</div>
					</div>
				</xsl:if>
				<xsl:if test="./fields/*[@id='291']/value != ''">
					<div class="myfieldHolder">
						<span class="fieldLabel">Design description:&#160;</span>
						<div class="fieldValue">
							<xsl:attribute name="field_id">291</xsl:attribute>
							<!--CALL MARK CONTENT EDITABLE TEMPLATE -->
							<xsl:call-template name="markContentEditableField">
								<xsl:with-param name="pObjectId" select="./@object_id"></xsl:with-param>
								<xsl:with-param name="pFieldId">291</xsl:with-param>
							</xsl:call-template>
							<xsl:apply-templates select="./fields/*[@id='291']/value" mode="formatting"/>
						</div>
					</div>
				</xsl:if>
				<xsl:if test="./fields/*[@id='292']/value != ''">
					<div class="myfieldHolder">
						<span class="fieldLabel">Funding:&#160;</span>
						<div class="fieldValue">
							<xsl:attribute name="field_id">292</xsl:attribute>
							<!--CALL MARK CONTENT EDITABLE TEMPLATE -->
							<xsl:call-template name="markContentEditableField">
								<xsl:with-param name="pObjectId" select="./@object_id"></xsl:with-param>
								<xsl:with-param name="pFieldId">292</xsl:with-param>
							</xsl:call-template>
							<xsl:apply-templates select="./fields/*[@id='292']/value" mode="formatting"/>
						</div>
					</div>
				</xsl:if>
			</div>
		</xsl:if>
	</xsl:template>

	
	
	<!-- Geographic Coverage -->
	<xsl:template match="*[@object_id='118']" mode="bodySections">
		<xsl:variable name="lSecTitle" select="./@display_name" />
		<xsl:variable name="lSecSubTitle" select="./fields/*[@id='316']/@field_name" />
		<xsl:variable name="lCoordinates"><xsl:text>Coordinates:</xsl:text></xsl:variable>
		<xsl:variable name="lEast" select="./fields/*[@id='318']/@field_name" />
		<xsl:variable name="lSouth" select="./fields/*[@id='319']/@field_name" />
		<xsl:variable name="lWest" select="./fields/*[@id='317']/@field_name" />
		<xsl:variable name="lNorth" select="./fields/*[@id='320']/@field_name" />

		
		<xsl:if test="(./fields/*[@id='316']/value != '') or (./fields/*[@id='319']/value != '') or (./fields/*[@id='320']/value != '') or (./fields/*[@id='317']/value != '') or (./fields/*[@id='318']/value != '')">
			<div class="P-Article-Preview-Block">
				<xsl:if test="(./fields/*[@id='316']/value != '')">
					<xsl:attribute name="instance_id"><xsl:value-of select="./@instance_id" /></xsl:attribute>
					<div class="P-Article-Preview-Block-Title"><xsl:value-of select="$lSecTitle" /></div>
						<div class="myfieldHolder">
							<span class="fieldLabel">Description:&#160;</span>
							<div class="fieldValue">
								<xsl:attribute name="field_id">316</xsl:attribute>
								<!--CALL MARK CONTENT EDITABLE TEMPLATE -->
								<xsl:call-template name="markContentEditableField">
									<xsl:with-param name="pObjectId" select="./@object_id"></xsl:with-param>
									<xsl:with-param name="pFieldId">316</xsl:with-param>
								</xsl:call-template>
								<xsl:attribute name="class">P-Inline</xsl:attribute>
								<xsl:apply-templates select="./fields/*[@id='316']/value" mode="formatting"/>
							</div>
						</div>
				</xsl:if>
				<!-- Coordinates -->
				<xsl:if test="(./fields/*[@id='319']/value != '') or (./fields/*[@id='320']/value != '') or (./fields/*[@id='317']/value != '') or (./fields/*[@id='318']/value != '')">
					<div class="myfieldHolder">
						<span class="fieldLabel">Coordinates:&#160;</span>
						<span>
							<xsl:attribute name="field_id">319</xsl:attribute>
							<xsl:call-template name="markContentEditableField">
								<xsl:with-param name="pObjectId" select="./@object_id"></xsl:with-param>
								<xsl:with-param name="pFieldId">319</xsl:with-param>
							</xsl:call-template>
							<xsl:attribute name="class">P-Inline</xsl:attribute>
							<xsl:apply-templates select="./fields/*[@id='319']/value" mode="formatting"/>
						</span>
						<xsl:text> and </xsl:text>
						<span>
							<xsl:attribute name="field_id">320</xsl:attribute>
							<xsl:call-template name="markContentEditableField">
								<xsl:with-param name="pObjectId" select="./@object_id"></xsl:with-param>
								<xsl:with-param name="pFieldId">320</xsl:with-param>
							</xsl:call-template>
							<xsl:attribute name="class">P-Inline</xsl:attribute>
							<xsl:apply-templates select="./fields/*[@id='320']/value" mode="formatting"/>
						</span>
						<xsl:text> Latitude; </xsl:text>
						<span>
							<xsl:attribute name="field_id">317</xsl:attribute>
							<xsl:call-template name="markContentEditableField">
								<xsl:with-param name="pObjectId" select="./@object_id"></xsl:with-param>
								<xsl:with-param name="pFieldId">317</xsl:with-param>
							</xsl:call-template>
							<xsl:attribute name="class">P-Inline</xsl:attribute>
							<xsl:apply-templates select="./fields/*[@id='317']/value" mode="formatting"/>
						</span>
						<xsl:text> and </xsl:text>
						<span>
							<xsl:attribute name="field_id">318</xsl:attribute>
							<xsl:call-template name="markContentEditableField">
								<xsl:with-param name="pObjectId" select="./@object_id"></xsl:with-param>
								<xsl:with-param name="pFieldId">318</xsl:with-param>
							</xsl:call-template>
							<xsl:attribute name="class">P-Inline</xsl:attribute>
							<xsl:apply-templates select="./fields/*[@id='318']/value" mode="formatting"/>
						</span>
						<xsl:text> Longitude.</xsl:text>
					</div>
				</xsl:if>
			</div>
		</xsl:if>	
	</xsl:template>

	
	
		<!-- Taxonomic Coverage -->
	<xsl:template match="*[@object_id='119']" mode="bodySections">
		<xsl:variable name="lSecTitle" select="./@display_name" />
		<xsl:variable name="lSecSubTitle"><xsl:text>Taxa included</xsl:text></xsl:variable>
		<xsl:variable name="lDescription" select="./fields/*[@id='322']/@field_name" />

		<xsl:if test="(./fields/*[@id='322']/value != '') or (count(.//*[@object_id='191']) &gt; 0)">
			<div class="P-Article-Preview-Block">
				<xsl:attribute name="instance_id"><xsl:value-of select="./@instance_id" /></xsl:attribute>
				<div class="P-Article-Preview-Block-Title"><xsl:value-of select="$lSecTitle" /></div>
				<xsl:if test="./fields/*[@id='322']/value != ''">
					<div class="myfieldHolder">
						<span class="fieldLabel">Description:&#160;</span>
						<div class="fieldValue">
							<xsl:attribute name="field_id">322</xsl:attribute>
							<!--CALL MARK CONTENT EDITABLE TEMPLATE -->
							<xsl:call-template name="markContentEditableField">
								<xsl:with-param name="pObjectId" select="./@object_id"></xsl:with-param>
								<xsl:with-param name="pFieldId">322</xsl:with-param>
							</xsl:call-template>
							<xsl:attribute name="class">P-Inline</xsl:attribute>
							<xsl:apply-templates select="./fields/*[@id='322']/value" mode="formatting"/>
						</div>
					</div>
				</xsl:if>
				<!-- If Taxa Included -->
				<xsl:if test="count(.//*[@object_id='191']) &gt; 0">
					<div class="myfieldHolder">
						<span class="fieldLabel no-float"><xsl:value-of select="$lSecSubTitle" />:</span>
					</div>
					<div class="Table-Body">
						<table>
							<tbody>
								<th><xsl:value-of select=".//*[@object_id='191']/fields/*[@id='453']/@field_name" /></th>
								<th><xsl:value-of select=".//*[@object_id='191']/fields/*[@id='451']/@field_name" /></th>
								<th><xsl:value-of select=".//*[@object_id='191']/fields/*[@id='452']/@field_name" /></th>
								<xsl:for-each select=".//*[@object_id='191']">
									<xsl:apply-templates select="." mode="singleTaxa"/>
								</xsl:for-each>
							</tbody>
						</table>
					</div>
				</xsl:if>
			</div>
		</xsl:if>
	</xsl:template>

	<!-- Single Taxa -->
	<xsl:template match="*" mode="singleTaxa">
		<xsl:variable name="lRankType" select="./fields/*[@id='453']/value" />
		<tr>
			<td>
				<xsl:if test="./fields/*[@id='453']/value != ''">
						<xsl:attribute name="field_id">453</xsl:attribute>
						<xsl:apply-templates select="$lRankType" mode="format_taxa_rank"/>						
				</xsl:if>
			</td>
			<td>
				<xsl:if test="./fields/*[@id='451']/value != ''">
						<xsl:call-template name="markContentEditableField">
							<xsl:with-param name="pObjectId" select="./@object_id"></xsl:with-param>
							<xsl:with-param name="pFieldId">451</xsl:with-param>
						</xsl:call-template>
						<xsl:attribute name="field_id">451</xsl:attribute>
						<xsl:attribute name="instance_id"><xsl:value-of select="./@instance_id"/></xsl:attribute>
						<xsl:apply-templates select="./fields/*[@id='451']/value" mode="formatting"/>
				</xsl:if>
			</td>
			<td>
				<xsl:if test="./fields/*[@id='452']/value != ''">
						<xsl:call-template name="markContentEditableField">
							<xsl:with-param name="pObjectId" select="./@object_id"></xsl:with-param>
							<xsl:with-param name="pFieldId">452</xsl:with-param>
						</xsl:call-template>
						<xsl:attribute name="field_id">452</xsl:attribute>
						<xsl:attribute name="instance_id"><xsl:value-of select="./@instance_id"/></xsl:attribute>
						<xsl:apply-templates select="./fields/*[@id='452']/value" mode="formatting"/>
				</xsl:if>
			</td>
		</tr>
	</xsl:template>
	

	
	
	<!-- Usage Rights -->
	<xsl:template match="*[@object_id='115']" mode="bodySections">
		<xsl:variable name="lSecTitle" select="./@display_name" />
		<xsl:variable name="lCollectionName" select="./fields/*[@id='311']/@field_name" />
		<xsl:variable name="lCollectionIdentifier" select="./fields/*[@id='312']/@field_name" />

		<xsl:if test="(./fields/*[@id='311']/value != '') or (./fields/*[@id='312']/value != '')">
			<div class="P-Article-Preview-Block">
				<xsl:attribute name="instance_id"><xsl:value-of select="./@instance_id" /></xsl:attribute>
				<div class="P-Article-Preview-Block-Title"><xsl:value-of select="$lSecTitle" /></div>
				<xsl:if test="(./fields/*[@id='311']/value != '')">
					<div class="myfieldHolder">
						<span class="fieldLabel">
							<xsl:value-of select="$lCollectionName" />:&#160;</span>
							
							<div class="fieldValue">
							<xsl:attribute name="field_id">311</xsl:attribute>
							<xsl:attribute name="class">P-Inline</xsl:attribute>
							<xsl:apply-templates select="./fields/*[@id='311']/value" mode="formatting"/>
						</div>
					</div>
				</xsl:if>
				<xsl:if test="(./fields/*[@id='312']/value != '')">
					<div class="myfieldHolder">
						<span class="fieldLabel">
							<xsl:value-of select="$lCollectionIdentifier" />:&#160;</span>
							<div class="fieldValue">
							<xsl:call-template name="markContentEditableField">
								<xsl:with-param name="pObjectId" select="./@object_id"></xsl:with-param>
								<xsl:with-param name="pFieldId">312</xsl:with-param>
							</xsl:call-template>
							<xsl:attribute name="field_id">312</xsl:attribute>
							<xsl:attribute name="class">P-Inline</xsl:attribute>
							<xsl:apply-templates select="./fields/*[@id='312']/value" mode="formatting"/>
						</div>
					</div>
				</xsl:if>
			</div>
		</xsl:if>
	</xsl:template>
	

	<!-- Additional Information -->
	<xsl:template match="*[@object_id='117']" mode="bodySections">
		<xsl:variable name="lSecTitle" select="./@display_name" />
		<xsl:if test="(./fields/*[@id='315']/value != '') or ./subsection/@object_id != ''">
			<div class="P-Article-Preview-Block">
				<xsl:attribute name="instance_id"><xsl:value-of select="./@instance_id" /></xsl:attribute>
				<h1><xsl:value-of select="$lSecTitle" /></h1>
				<xsl:if test="./fields/*[@id='315']/value != '' ">
					<div class="P-Article-Preview-Block-Content">
						<xsl:attribute name="field_id">315</xsl:attribute>
						<!--CALL MARK CONTENT EDITABLE TEMPLATE -->
						<xsl:call-template name="markContentEditableField">
							<xsl:with-param name="pObjectId" select="./@object_id"></xsl:with-param>
							<xsl:with-param name="pFieldId">315</xsl:with-param>
						</xsl:call-template>
						<xsl:attribute name="class">P-Inline</xsl:attribute>
						<xsl:apply-templates select="./fields/*[@id='315']/value" mode="formatting"/>
					</div>
				</xsl:if>
				<xsl:apply-templates mode="bodySubsection" select="./subsection"/>
			</div>
		</xsl:if>
	</xsl:template>
	
	<!-- Characters used in the key -->
	<xsl:template match="*[@object_id='120']" mode="bodySections">
		<xsl:if test="(./fields/*[@id='324']/value != '')">
			<xsl:variable name="lSecTitle" select="./@display_name"></xsl:variable>
			<div class="P-Article-Preview-Block">
				<xsl:attribute name="instance_id"><xsl:value-of select="./@instance_id" /></xsl:attribute>
				<div class="P-Article-Preview-Block-Title"><xsl:value-of select="$lSecTitle"></xsl:value-of></div>
				<div class="P-Article-Preview-Block-Content">
					<div class="P-Inline">
						<xsl:attribute name="field_id">324</xsl:attribute>
						<!--CALL MARK CONTENT EDITABLE TEMPLATE -->
						<xsl:call-template name="markContentEditableField">
							<xsl:with-param name="pObjectId" select="./@object_id"></xsl:with-param>
							<xsl:with-param name="pFieldId">324</xsl:with-param>
						</xsl:call-template>
						<xsl:apply-templates select="./fields/*[@id='324']/value" mode="formatting"/>
					</div>
				</div>
			</div>
		</xsl:if>
	</xsl:template>

	<!-- Software specification -->
	<xsl:template match="*[@object_id='121']" mode="bodySections">
		<xsl:if test="(count(./fields/*[@id != '' and value !='']) &gt; 0)">
			<xsl:variable name="lSecTitle" select="./@display_name"></xsl:variable>
			<div class="P-Article-Preview-Block">
				<xsl:attribute name="instance_id"><xsl:value-of select="./@instance_id" /></xsl:attribute>
				<div class="P-Article-Preview-Block-Title"><xsl:value-of select="$lSecTitle"></xsl:value-of></div>
				<xsl:for-each select="./fields/*[@id != '']">
					<xsl:if test="./value !=''">
						<div class="myfieldHolder">
							<span class="fieldLabel">
								<xsl:value-of select="./@field_name"></xsl:value-of>:&#160;</span>
							<div class="fieldValue">
															
								<xsl:attribute name="instance_id"><xsl:value-of select="../../@instance_id"></xsl:value-of></xsl:attribute>								
								<xsl:if test="./@id = '329'">
									<a>
										<xsl:call-template name="markContentEditableField">
											<xsl:with-param name="pObjectId" select="../../@object_id"></xsl:with-param>
											<xsl:with-param name="pFieldId" select="./@id"></xsl:with-param>
										</xsl:call-template>	
										<xsl:attribute name="field_id"><xsl:value-of select="./@id"></xsl:value-of></xsl:attribute>
										<xsl:attribute name="href"><xsl:value-of select="normalize-space(./value)"/></xsl:attribute>
										<xsl:attribute name="target"><xsl:text>_blank</xsl:text></xsl:attribute>
										<xsl:apply-templates select="./value" mode="formatting_nospace"/>
									</a>
								</xsl:if>
								<xsl:if test="./@id != '329'">
									<xsl:call-template name="markContentEditableField">
										<xsl:with-param name="pObjectId" select="../../@object_id"></xsl:with-param>
										<xsl:with-param name="pFieldId" select="./@id"></xsl:with-param>
									</xsl:call-template>	
									<xsl:attribute name="field_id"><xsl:value-of select="./@id"></xsl:value-of></xsl:attribute>
									<xsl:apply-templates select="./value" mode="formatting"/>
								</xsl:if>
							</div>
						</div>
					</xsl:if>
				</xsl:for-each>
			</div>
		</xsl:if>
	</xsl:template>

	<!-- Software technical features -->
	<xsl:template match="*[@object_id='122']" mode="bodySections">
		<xsl:if test="./fields/*[@id='330']/value !=''">
			<xsl:variable name="lSecTitle" select="./@display_name"></xsl:variable>
			<div class="P-Article-Preview-Block">
				<xsl:attribute name="instance_id"><xsl:value-of select="./@instance_id" /></xsl:attribute>
				<div class="P-Article-Preview-Block-Title"><xsl:value-of select="$lSecTitle"></xsl:value-of></div>
				<div class="P-Article-Preview-Block-Content">
					<div class="P-Inline">
						<xsl:attribute name="field_id">330</xsl:attribute>
						<!--CALL MARK CONTENT EDITABLE TEMPLATE -->
						<xsl:call-template name="markContentEditableField">
							<xsl:with-param name="pObjectId" select="./@object_id"></xsl:with-param>
							<xsl:with-param name="pFieldId">330</xsl:with-param>
						</xsl:call-template>
						<xsl:apply-templates select="./fields/*[@id='330']/value" mode="formatting"/>
					</div>
				</div>
			</div>
		</xsl:if>
	</xsl:template>
</xsl:stylesheet>