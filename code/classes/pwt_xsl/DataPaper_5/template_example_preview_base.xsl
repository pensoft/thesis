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
						<xsl:value-of select="/document/objects/*[@object_id='14']/*[@object_id='9']/@instance_id" />
					</xsl:attribute>
					<div>
						<xsl:attribute name="field_id">3</xsl:attribute>
						<xsl:apply-templates select="/document/objects/*[@object_id='14']/*[@object_id='9']/fields/*[@id='3']" mode="articleTitle"></xsl:apply-templates>
					</div>
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
							<xsl:variable name="lEditPreviewHead" select="php:function('getEditPreviewHead', string($pDocumentId))" />
							<xsl:for-each select="$lEditPreviewHead/script|$lEditPreviewHead/link|$lEditPreviewHead/style">
								<xsl:copy-of select="."></xsl:copy-of>
							</xsl:for-each>
						</xsl:if>
						<title></title>
					</head>
					<body>
						<xsl:comment><xsl:value-of select="$pEditableHeaderReplacementText" /></xsl:comment>
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


<!-- General Description -->
	<xsl:template match="*[@object_id='189']" mode="bodySections">
		<xsl:variable name="lSecTitle" select="./@display_name" />
		<xsl:variable name="lSecSubTitle" select="./fields/*[@id='449']/@field_name" />
		<xsl:variable name="lAdditionalInformation" select="./fields/*[@id='315']/@field_name" />
		<xsl:if test="(./fields/*[@id='315']/value != '') or (./fields/*[@id='449']/value != '')">
			<div class="P-Article-Preview-Block">
				<xsl:attribute name="instance_id"><xsl:value-of select="./@instance_id" /></xsl:attribute>
				<div class="P-Article-Preview-Block-Title"><xsl:value-of select="$lSecTitle" /></div>
				<xsl:if test="./fields/*[@id='449']/value != ''">
					<div class="myfieldHolder">
						<span class="fieldLabel">Purpose:&#160;</span>
						<div  class="fieldValue">
							<xsl:attribute name="field_id">449</xsl:attribute>
							<!--CALL MARK CONTENT EDITABLE TEMPLATE -->
							<xsl:call-template name="markContentEditableField">
								<xsl:with-param name="pObjectId" select="./@object_id"></xsl:with-param>
								<xsl:with-param name="pFieldId">449</xsl:with-param>
							</xsl:call-template>
							<xsl:apply-templates select="./fields/*[@id='449']/value" mode="formatting"/>
						</div>
					</div>
				</xsl:if>
				<xsl:if test="./fields/*[@id='315']/value != ''">
					<div class="myfieldHolder">
						<span class="fieldLabel">Addititional information:&#160;</span>
						<div  class="fieldValue" field_id="315">
							<xsl:call-template name="markContentEditableField">
								<xsl:with-param name="pObjectId" select="./@object_id"></xsl:with-param>
								<xsl:with-param name="pFieldId">315</xsl:with-param>
							</xsl:call-template>
							<xsl:apply-templates select="./fields/*[@id='315']/value" mode="formatting"/>
						</div>
					</div>
				</xsl:if>
			</div>
		</xsl:if>
	</xsl:template>

	<!-- Project Description -->
	<xsl:template match="*[@object_id='190']" mode="bodySections">
		<xsl:variable name="lSecTitle" select="./@display_name" />
		<xsl:variable name="lSecSubTitle" select="./fields/*[@id='289']/@field_name" />
		<xsl:variable name="lPersonnel" select="./fields/*[@id='450']/@field_name" />
		<xsl:variable name="lStudyArea" select="./fields/*[@id='290']/@field_name" />
		<xsl:variable name="lDesignDescription" select="./fields/*[@id='291']/@field_name" />
		<xsl:variable name="lFunding" select="./fields/*[@id='292']/@field_name" />

		<xsl:if test="(./fields/*[@id='289']/value != '') or (./fields/*[@id='450']/value != '') or (./fields/*[@id='290']/value != '') or (./fields/*[@id='291']/value != '') or (./fields/*[@id='292']/value != '')">
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
							<xsl:attribute name="class">P-Inline</xsl:attribute>
							<xsl:apply-templates select="./fields/*[@id='289']/value" mode="formatting"/>
						</div>
					</div>
				</xsl:if>
				<xsl:if test="./fields/*[@id='450']/value != ''">
					<div class="myfieldHolder">
						<span class="fieldLabel">Personel:&#160;</span>
						<div class="fieldValue">
							<xsl:attribute name="field_id">450</xsl:attribute>
							<!--CALL MARK CONTENT EDITABLE TEMPLATE -->
							<xsl:call-template name="markContentEditableField">
								<xsl:with-param name="pObjectId" select="./@object_id"></xsl:with-param>
								<xsl:with-param name="pFieldId">450</xsl:with-param>
							</xsl:call-template>
							<xsl:attribute name="class">P-Inline</xsl:attribute>
							<xsl:apply-templates select="./fields/*[@id='450']/value" mode="formatting"/>
						</div>
					</div>
				</xsl:if>
				<xsl:if test="./fields/*[@id='290']/value != ''">
					<div class="myfieldHolder">
						<span class="fieldLabel">Study area description:&#160;</span>
						<div class="fieldValue">
							<xsl:attribute name="field_id">290</xsl:attribute>
							<xsl:attribute name="class">P-Inline</xsl:attribute>
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
							<xsl:attribute name="class">P-Inline</xsl:attribute>
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
							<xsl:attribute name="class">P-Inline</xsl:attribute>
							<xsl:apply-templates select="./fields/*[@id='292']/value" mode="formatting"/>
						</div>
					</div>
				</xsl:if>
			</div>
		</xsl:if>
	</xsl:template>

	<!-- Data resources -->
	<xsl:template match="*[@object_id='17']" mode="bodySections">
		<xsl:variable name="lSecTitle">Data resources</xsl:variable>
		<div class="P-Article-Preview-Block">
			<xsl:attribute name="instance_id"><xsl:value-of select="./@instance_id" /></xsl:attribute>
			<!--anchor-->
			<span class="anchor" id="data_resources"></span>
			<div class="P-Article-Preview-Block-Title"><xsl:value-of select="$lSecTitle" /></div>
			<div class="P-Article-Preview-Block-Content">
				<xsl:attribute name="field_id">21</xsl:attribute>
				<!--CALL MARK CONTENT EDITABLE TEMPLATE -->
				<xsl:call-template name="markContentEditableField">
					<xsl:with-param name="pObjectId" select="./@object_id"></xsl:with-param>
					<xsl:with-param name="pFieldId">21</xsl:with-param>
				</xsl:call-template>
				<xsl:apply-templates select="./fields/*[@id='21']" mode="formatting"/>
			</div>
			<xsl:apply-templates mode="bodySubsection" select="./subsection"/>
		</div>

	</xsl:template>


	<!-- Sampling Methods -->
	<xsl:template match="*[@object_id='123']" mode="bodySections">
		<xsl:variable name="lSecTitle" select="./@display_name" />
		<xsl:variable name="lSecSubTitle" select="./fields/*[@id='331']/@field_name" />
		<xsl:variable name="lStudyAreaDescription" select="./fields/*[@id='332']/@field_name" />
		<xsl:variable name="lDesignDescription" select="./fields/*[@id='333']/@field_name" />
		<xsl:variable name="lFunding" select="./fields/*[@id='334']/@field_name" />

		<xsl:if test="(./fields/*[@id='331']/value != '') or (./fields/*[@id='332']/value != '') or (./fields/*[@id='333']/value != '') or (./fields/*[@id='334']/value != '')">
			<div class="P-Article-Preview-Block">
				<xsl:attribute name="instance_id"><xsl:value-of select="./@instance_id" /></xsl:attribute>
				<div class="P-Article-Preview-Block-Title"><xsl:value-of select="$lSecTitle" /></div>
				<xsl:if test="(./fields/*[@id='331']/value != '')">
					<div class="myfieldHolder">
						<span class="fieldLabel">Study extent:&#160;</span>
						<div class="fieldValue">
							<xsl:attribute name="field_id">331</xsl:attribute>
							<!--CALL MARK CONTENT EDITABLE TEMPLATE -->
							<xsl:call-template name="markContentEditableField">
								<xsl:with-param name="pObjectId" select="./@object_id"></xsl:with-param>
								<xsl:with-param name="pFieldId">331</xsl:with-param>
							</xsl:call-template>
							<xsl:attribute name="class">P-Inline</xsl:attribute>
							<xsl:apply-templates select="./fields/*[@id='331']/value" mode="formatting"/>
						</div>
					</div>
				</xsl:if>
				<xsl:if test="(./fields/*[@id='332']/value != '')">
					<div class="myfieldHolder">
						<span class="fieldLabel">Sampling description:&#160;</span>
						<div class="fieldValue">
							<xsl:attribute name="field_id">332</xsl:attribute>
							<xsl:attribute name="class">P-Inline</xsl:attribute>
							<!--CALL MARK CONTENT EDITABLE TEMPLATE -->
							<xsl:call-template name="markContentEditableField">
								<xsl:with-param name="pObjectId" select="./@object_id"></xsl:with-param>
								<xsl:with-param name="pFieldId">332</xsl:with-param>
							</xsl:call-template>
							<xsl:apply-templates select="./fields/*[@id='332']/value" mode="formatting"/>
						</div>
					</div>
				</xsl:if>
				<xsl:if test="(./fields/*[@id='333']/value != '')">
					<div class="myfieldHolder">
						<span class="fieldLabel">Quality control:&#160;</span>
						<div class="fieldValue">
							<xsl:attribute name="field_id">333</xsl:attribute>
							<xsl:attribute name="class">P-Inline</xsl:attribute>
							<!--CALL MARK CONTENT EDITABLE TEMPLATE -->
							<xsl:call-template name="markContentEditableField">
								<xsl:with-param name="pObjectId" select="./@object_id"></xsl:with-param>
								<xsl:with-param name="pFieldId">333</xsl:with-param>
							</xsl:call-template>
							<xsl:apply-templates select="./fields/*[@id='333']/value" mode="formatting"/>
						</div>
					</div>
				</xsl:if>
				<xsl:if test="(./fields/*[@id='334']/value != '')">
					<div class="myfieldHolder">
						<span class="fieldLabel">Step description:&#160;</span>
						<div class="fieldValue">
							<xsl:attribute name="field_id">334</xsl:attribute>
							<xsl:attribute name="class">P-Inline</xsl:attribute>
							<!--CALL MARK CONTENT EDITABLE TEMPLATE -->
							<xsl:call-template name="markContentEditableField">
								<xsl:with-param name="pObjectId" select="./@object_id"></xsl:with-param>
								<xsl:with-param name="pFieldId">334</xsl:with-param>
							</xsl:call-template>
							<xsl:apply-templates select="./fields/*[@id='334']/value" mode="formatting"/>
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
				<xsl:attribute name="instance_id"><xsl:value-of select="./@instance_id" /></xsl:attribute>
				<div class="P-Article-Preview-Block-Title"><xsl:value-of select="$lSecTitle" /></div>
				<xsl:if test="(./fields/*[@id='316']/value != '')">
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
				<div class="myfieldHolder">
					<span class="fieldLabel">Coordinates:&#160;</span>
					<span>
						<xsl:call-template name="markContentEditableField">
							<xsl:with-param name="pObjectId" select="./@object_id"></xsl:with-param>
							<xsl:with-param name="pFieldId">319</xsl:with-param>
						</xsl:call-template>
						<xsl:attribute name="field_id">319</xsl:attribute>
						<xsl:attribute name="class">P-Inline</xsl:attribute>
						<xsl:apply-templates select="./fields/*[@id='319']/value" mode="formatting"/>
					</span>
					<xsl:text> and </xsl:text>
					<span>
						<xsl:call-template name="markContentEditableField">
							<xsl:with-param name="pObjectId" select="./@object_id"></xsl:with-param>
							<xsl:with-param name="pFieldId">320</xsl:with-param>
						</xsl:call-template>
						<xsl:attribute name="field_id">320</xsl:attribute>
						<xsl:attribute name="class">P-Inline</xsl:attribute>
						<xsl:apply-templates select="./fields/*[@id='320']/value" mode="formatting"/>
					</span>
					<xsl:text> Latitude; </xsl:text>
					<span>
						<xsl:call-template name="markContentEditableField">
							<xsl:with-param name="pObjectId" select="./@object_id"></xsl:with-param>
							<xsl:with-param name="pFieldId">317</xsl:with-param>
						</xsl:call-template>
						<xsl:attribute name="field_id">317</xsl:attribute>
						<xsl:attribute name="class">P-Inline</xsl:attribute>
						<xsl:apply-templates select="./fields/*[@id='317']/value" mode="formatting"/>
					</span>
					<xsl:text> and </xsl:text>
					<span>
						<xsl:call-template name="markContentEditableField">
							<xsl:with-param name="pObjectId" select="./@object_id"></xsl:with-param>
							<xsl:with-param name="pFieldId">318</xsl:with-param>
						</xsl:call-template>
						<xsl:attribute name="field_id">318</xsl:attribute>
						<xsl:attribute name="class">P-Inline</xsl:attribute>
						<xsl:apply-templates select="./fields/*[@id='318']/value" mode="formatting"/>
					</span>
					<xsl:text> Longitude.</xsl:text>
				</div>
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
								<th>
									<xsl:value-of select=".//*[@object_id='191']/fields/*[@id='453']/@field_name" />
								</th>
								<th>
									<xsl:value-of select=".//*[@object_id='191']/fields/*[@id='451']/@field_name" />
								</th>
								<th>
									<xsl:value-of select=".//*[@object_id='191']/fields/*[@id='452']/@field_name" />
								</th>
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

	<!-- Temporal Coverage -->
	<xsl:template match="*[@object_id='194']" mode="bodySections">
		<xsl:variable name="lSecTitle" select="./@display_name" />
		<xsl:variable name="lSingleDateTitle"><xsl:text>Single date</xsl:text></xsl:variable>
		<xsl:variable name="lDataRange"><xsl:text>Data range</xsl:text></xsl:variable>
		<xsl:variable name="lFormationPeriod"><xsl:text>Formation Period</xsl:text></xsl:variable>
		<xsl:variable name="lLivingTimePeriod"><xsl:text>Living Time Period</xsl:text></xsl:variable>

		<xsl:if test="(count(.//*[@object_id='124']/fields/*[@id='335']/value[@value_id='4']) &gt; 0) or (count(.//*[@object_id='124']/fields/*[@id='335']/value[@value_id='1']) &gt; 0) or (count(.//*[@object_id='124']/fields/*[@id='335']/value[@value_id='3']) &gt; 0) or (count(.//*[@object_id='124']/fields/*[@id='335']/value[@value_id='2']) &gt; 0)">
			<div class="P-Article-Preview-Block">
				<xsl:attribute name="instance_id"><xsl:value-of select="./@instance_id" /></xsl:attribute>
				<div class="P-Article-Preview-Block-Title"><xsl:value-of select="$lSecTitle" /></div>

				<!-- Single date -->
				<xsl:if test="count(.//*[@object_id='124']/fields/*[@id='335']/value[@value_id='1']) &gt; 0">
					<div class="myfieldHolder">
						<span class="fieldLabel">Single date:&#160;</span>
						<div class="fieldValue">
							<xsl:for-each select=".//*[@object_id='124'][fields/*[@id='335']/value[@value_id='1']]">
								<xsl:apply-templates select="." mode="tempCoverageDates"/>
								<xsl:if test="position()!=last()"><xsl:text>; </xsl:text></xsl:if>
								<xsl:if test="position()=last()"><xsl:text>. </xsl:text></xsl:if>
							</xsl:for-each>
						</div>
					</div>
				</xsl:if>
				<!-- Data range -->
				<xsl:if test="count(.//*[@object_id='124']/fields/*[@id='335']/value[@value_id='4']) &gt; 0">
					<div class="myfieldHolder">
						<span class="fieldLabel">Data range:&#160;</span>
						<div class="fieldValue">
							<xsl:for-each select=".//*[@object_id='124'][fields/*[@id='335']/value[@value_id='4']]">
								<xsl:apply-templates select="." mode="tempCoverageDates"/>
								<xsl:if test="position()!=last()"><xsl:text>; </xsl:text></xsl:if>
								<xsl:if test="position()=last()"><xsl:text>. </xsl:text></xsl:if>
							</xsl:for-each>
						</div>
					</div>
				</xsl:if>
				<!-- Formation Period -->
				<xsl:if test="count(.//*[@object_id='124']/fields/*[@id='335']/value[@value_id='3']) &gt; 0">
					<div class="myfieldHolder">
						<span class="fieldLabel">Formation period:&#160;</span>
						<div class="fieldValue">
							<xsl:for-each select=".//*[@object_id='124'][fields/*[@id='335']/value[@value_id='3']]">
								<xsl:apply-templates select="." mode="tempCoverageDates"/>
								<xsl:if test="position()!=last()"><xsl:text>; </xsl:text></xsl:if>
								<xsl:if test="position()=last()"><xsl:text>. </xsl:text></xsl:if>
							</xsl:for-each>
						</div>
					</div>
				</xsl:if>
				<!-- Living Time Period -->
				<xsl:if test="count(.//*[@object_id='124']/fields/*[@id='335']/value[@value_id='2']) &gt; 0">
					<div class="myfieldHolder">
						<span class="fieldLabel">Living time period:&#160;</span>
						<div class="fieldValue">
							<xsl:for-each select=".//*[@object_id='124'][fields/*[@id='335']/value[@value_id='2']]">
								<xsl:apply-templates select="." mode="tempCoverageDates"/>
								<xsl:if test="position()!=last()"><xsl:text>; </xsl:text></xsl:if>
								<xsl:if test="position()=last()"><xsl:text>. </xsl:text></xsl:if>
							</xsl:for-each>
						</div>
					</div>
				</xsl:if>
			</div>
		</xsl:if>
	</xsl:template>

	<!-- Temporal Coverage Dates -->
	<xsl:template match="*" mode="tempCoverageDates">

		<xsl:value-of select="php:function('formatDate', string(./fields/*[@id='392']/value))" />
		<xsl:value-of select="php:function('formatDate', string(./fields/*[@id='394']/value))" />
		<xsl:value-of select="php:function('formatDate', string(./fields/*[@id='393']/value))" />
		<xsl:if test="./fields/*[@id='395']/value != '' and ./fields/*[@id='396']/value != ''">
			<xsl:value-of select="php:function('formatDate', string(./fields/*[@id='395']/value))" />
			<xsl:text> - </xsl:text>
			<xsl:value-of select="php:function('formatDate', string(./fields/*[@id='396']/value))" />
		</xsl:if>
	</xsl:template>

	<!-- Collection Data -->
	<xsl:template match="*[@object_id='125']" mode="bodySections">
		<xsl:variable name="lSecTitle" select="./@display_name" />
		<xsl:variable name="lCollectionName" select="./fields/*[@id='336']/@field_name" />
		<xsl:variable name="lCollectionIdentifier" select="./fields/*[@id='337']/@field_name" />
		<xsl:variable name="lParentCollectionIdentifier" select="./fields/*[@id='338']/@field_name" />
		<xsl:variable name="lSpecimenMethod" select="./fields/*[@id='455']/@field_name" />
		<xsl:variable name="lCuratorialUnit" select="./fields/*[@id='456']/@field_name" />

		<xsl:if test="(./fields/*[@id='336']/value != '') or (./fields/*[@id='337']/value != '') or (./fields/*[@id='338']/value != '') or (./fields/*[@id='337']/value != '') or (./fields/*[@id='455']/value != '') or (./fields/*[@id='456']/value != '')">
			<div class="P-Article-Preview-Block">
				<xsl:attribute name="instance_id"><xsl:value-of select="./@instance_id" /></xsl:attribute>
				<div class="P-Article-Preview-Block-Title"><xsl:value-of select="$lSecTitle" /></div>

				<xsl:if test="./fields/*[@id='336']/value != ''">
					<div class="myfieldHolder">
						<span class="fieldLabel">
							<xsl:value-of select="$lCollectionName" />:&#160;</span>

						<div class="fieldValue">
							<xsl:call-template name="markContentEditableField">
								<xsl:with-param name="pObjectId" select="./@object_id"></xsl:with-param>
								<xsl:with-param name="pFieldId">336</xsl:with-param>
							</xsl:call-template>
							<xsl:attribute name="field_id">336</xsl:attribute>
							<xsl:attribute name="class">P-Inline</xsl:attribute>
							<xsl:apply-templates select="./fields/*[@id='336']/value" mode="formatting"/>
						</div>
					</div>
				</xsl:if>

				<xsl:if test="./fields/*[@id='337']/value != ''">
					<div class="myfieldHolder">
						<span class="fieldLabel">
							<xsl:value-of select="$lCollectionIdentifier" />:&#160;</span>

						<div class="fieldValue">
							<xsl:call-template name="markContentEditableField">
								<xsl:with-param name="pObjectId" select="./@object_id"></xsl:with-param>
								<xsl:with-param name="pFieldId">337</xsl:with-param>
							</xsl:call-template>
							<xsl:attribute name="field_id">337</xsl:attribute>
							<xsl:attribute name="class">P-Inline</xsl:attribute>
							<xsl:apply-templates select="./fields/*[@id='337']/value" mode="formatting"/>
						</div>
					</div>
				</xsl:if>

				<xsl:if test="./fields/*[@id='338']/value != ''">
					<div class="myfieldHolder">
						<span class="fieldLabel">
							<xsl:value-of select="$lParentCollectionIdentifier" />:&#160;</span>

						<div class="fieldValue">
							<xsl:call-template name="markContentEditableField">
								<xsl:with-param name="pObjectId" select="./@object_id"></xsl:with-param>
								<xsl:with-param name="pFieldId">338</xsl:with-param>
							</xsl:call-template>
							<xsl:attribute name="field_id">338</xsl:attribute>
							<xsl:attribute name="class">P-Inline</xsl:attribute>
							<xsl:apply-templates select="./fields/*[@id='338']/value" mode="formatting"/>
						</div>
					</div>
				</xsl:if>

				<xsl:if test="./fields/*[@id='455']/value != ''">
					<div class="myfieldHolder">
						<span class="fieldLabel">
							<xsl:value-of select="$lSpecimenMethod" />:&#160;</span>

						<div class="fieldValue">
							<xsl:call-template name="markContentEditableField">
								<xsl:with-param name="pObjectId" select="./@object_id"></xsl:with-param>
								<xsl:with-param name="pFieldId">455</xsl:with-param>
							</xsl:call-template>
							<xsl:attribute name="field_id">455</xsl:attribute>
							<xsl:attribute name="class">P-Inline</xsl:attribute>
							<xsl:apply-templates select="./fields/*[@id='455']/value" mode="formatting"/>
						</div>
					</div>
				</xsl:if>

				<xsl:if test="./fields/*[@id='456']/value != ''">
					<div class="myfieldHolder">
						<span class="fieldLabel">
							<xsl:value-of select="$lCuratorialUnit" />:&#160;</span>

						<div class="fieldValue">
							<xsl:call-template name="markContentEditableField">
								<xsl:with-param name="pObjectId" select="./@object_id"></xsl:with-param>
								<xsl:with-param name="pFieldId">456</xsl:with-param>
							</xsl:call-template>
							<xsl:attribute name="field_id">456</xsl:attribute>
							<xsl:attribute name="class">P-Inline</xsl:attribute>
							<xsl:apply-templates select="./fields/*[@id='456']/value" mode="formatting"/>
						</div>
					</div>
				</xsl:if>
			</div>
		</xsl:if>
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
								<!--CALL MARK CONTENT EDITABLE TEMPLATE -->
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

	<!-- Data Resources -->
	<xsl:template match="*[@object_id='126']" mode="bodySections">
		<xsl:variable name="lSecTitle" select="./@display_name" />
		<xsl:variable name="lCollectionName" select="./fields/*[@id='339']/@field_name" />
		<xsl:variable name="lCollectionIdentifier" select="./fields/*[@id='340']/@field_name" />
		<xsl:variable name="lAlternativeIdentifiers" select="./fields/*[@id='341']/@field_name" />
		<xsl:variable name="lNumberofdatasets" select="./fields/*[@id='342']/@field_name" />
		<xsl:variable name="lTreatmentURLPrefix">URL</xsl:variable>

		<xsl:if test="(./fields/*[@id='339']/value != '') or (./fields/*[@id='340']/value != '') or (./fields/*[@id='341']/value != '') or (./fields/*[@id='342']/value != '')">
			<div class="P-Article-Preview-Block">
				<xsl:attribute name="instance_id"><xsl:value-of select="./@instance_id" /></xsl:attribute>
				<div class="P-Article-Preview-Block-Title"><xsl:value-of select="$lSecTitle" /></div>

				<xsl:if test="./fields/*[@id='339']/value != ''">
					<div class="myfieldHolder">
						<span class="fieldLabel">
							<xsl:value-of select="$lCollectionName" />:&#160;</span>

						<div class="fieldValue">
							<xsl:call-template name="markContentEditableField">
								<xsl:with-param name="pObjectId" select="./@object_id"></xsl:with-param>
								<xsl:with-param name="pFieldId">339</xsl:with-param>
							</xsl:call-template>
							<xsl:attribute name="field_id">339</xsl:attribute>
							<xsl:attribute name="class">P-Inline</xsl:attribute>
							<xsl:apply-templates select="./fields/*[@id='339']/value" mode="formatting"/>
						</div>
					</div>
				</xsl:if>
				<xsl:if test="./fields/*[@id='340']/value != ''">
					<div class="myfieldHolder">
						<span class="fieldLabel">
							<xsl:value-of select="$lCollectionIdentifier" />:&#160;</span>

						<div class="fieldValue">
							<xsl:call-template name="markContentEditableField">
								<xsl:with-param name="pObjectId" select="./@object_id"></xsl:with-param>
								<xsl:with-param name="pFieldId">340</xsl:with-param>
							</xsl:call-template>
							<xsl:attribute name="field_id">340</xsl:attribute>
							<xsl:attribute name="class">P-Inline</xsl:attribute>
							<xsl:apply-templates select="./fields/*[@id='340']/value" mode="formatting">
								<xsl:with-param name="lTreatmentUrl" select="$lTreatmentURLPrefix"/>
							</xsl:apply-templates>
						</div>
					</div>
				</xsl:if>
				<xsl:if test="./fields/*[@id='341']/value != ''">
					<div class="myfieldHolder">
						<span class="fieldLabel">
							<xsl:value-of select="$lAlternativeIdentifiers" />:&#160;</span>

						<div class="fieldValue">
							<xsl:call-template name="markContentEditableField">
								<xsl:with-param name="pObjectId" select="./@object_id"></xsl:with-param>
								<xsl:with-param name="pFieldId">341</xsl:with-param>
							</xsl:call-template>
							<xsl:attribute name="field_id">341</xsl:attribute>
							<xsl:attribute name="class">P-Inline</xsl:attribute>
							<xsl:apply-templates select="./fields/*[@id='341']/value" mode="formatting"/>
						</div>
					</div>
				</xsl:if>
				<xsl:if test="./fields/*[@id='342']/value != ''">
					<div class="myfieldHolder">
						<span class="fieldLabel">
							<xsl:value-of select="$lNumberofdatasets" />:&#160;</span>

						<div class="fieldValue">
							<xsl:attribute name="field_id">342</xsl:attribute>
							<xsl:attribute name="class">P-Inline</xsl:attribute>
							<xsl:apply-templates select="./fields/*[@id='342']/value" mode="formatting"/>
						</div>
					</div>
				</xsl:if>
				<xsl:apply-templates select="//*[@object_id='141']" mode="dataResourceDataSet"/>
			</div>
		</xsl:if>
	</xsl:template>

	<!-- Data resources Data sets -->
	<xsl:template match="*" mode="dataResourceDataSet">
		<xsl:variable name="lDataSetname" select="./fields/*[@id='397']/@field_name" />
		<xsl:variable name="lCharSet" select="./fields/*[@id='457']/@field_name" />
		<xsl:variable name="lDownUrl" select="./fields/*[@id='458']/@field_name" />
		<xsl:variable name="lDataFormat" select="./fields/*[@id='398']/@field_name" />
		<xsl:variable name="lDataFormatVersion" select="./fields/*[@id='459']/@field_name" />
		<xsl:variable name="lDescription" select="./fields/*[@id='399']/@field_name" />
		<xsl:variable name="lTreatmentURLPrefix">URL</xsl:variable>

		<div class="DataSetHolder">
			<xsl:if test="./fields/*[@id='397']/value != ''">
				<div class="myfieldHolder">
					<span class="fieldLabel">
						<xsl:value-of select="$lDataSetname" />:&#160;</span>

					<div class="fieldValue">
						<xsl:call-template name="markContentEditableField">
							<xsl:with-param name="pObjectId" select="./@object_id"></xsl:with-param>
							<xsl:with-param name="pFieldId">397</xsl:with-param>
						</xsl:call-template>
						<xsl:attribute name="instance_id"><xsl:value-of select="./@instance_id" /></xsl:attribute>
						<xsl:attribute name="field_id">397</xsl:attribute>
						<xsl:attribute name="class">P-Inline</xsl:attribute>
						<xsl:apply-templates select="./fields/*[@id='397']/value" mode="formatting"/>
					</div>
				</div>
			</xsl:if>
			<xsl:if test="./fields/*[@id='457']/value != ''">
				<div class="myfieldHolder">
					<span class="fieldLabel">
						<xsl:value-of select="$lCharSet" />:&#160;</span>

					<div class="fieldValue">
						<xsl:call-template name="markContentEditableField">
							<xsl:with-param name="pObjectId" select="./@object_id"></xsl:with-param>
							<xsl:with-param name="pFieldId">457</xsl:with-param>
						</xsl:call-template>
						<xsl:attribute name="instance_id"><xsl:value-of select="./@instance_id" /></xsl:attribute>
						<xsl:attribute name="field_id">457</xsl:attribute>
						<xsl:attribute name="class">P-Inline</xsl:attribute>
						<xsl:apply-templates select="./fields/*[@id='457']/value" mode="formatting"/>
					</div>
				</div>
			</xsl:if>
			<xsl:if test="./fields/*[@id='458']/value != ''">
				<div class="myfieldHolder">
					<span class="fieldLabel">
						<xsl:value-of select="$lDownUrl" />:&#160;</span>

					<div class="fieldValue">
						<xsl:call-template name="markContentEditableField">
							<xsl:with-param name="pObjectId" select="./@object_id"></xsl:with-param>
							<xsl:with-param name="pFieldId">458</xsl:with-param>
						</xsl:call-template>
						<xsl:attribute name="instance_id"><xsl:value-of select="./@instance_id" /></xsl:attribute>
						<xsl:attribute name="field_id">458</xsl:attribute>
						<xsl:attribute name="class">P-Inline</xsl:attribute>

						<xsl:apply-templates select="./fields/*[@id='458']/value" mode="formatting">
							<xsl:with-param name="lTreatmentUrl" select="$lTreatmentURLPrefix"/>
						</xsl:apply-templates>

					</div>
				</div>
			</xsl:if>
			<xsl:if test="./fields/*[@id='398']/value != ''">
				<div class="myfieldHolder">
					<span class="fieldLabel">
						<xsl:value-of select="$lDataFormat" />:&#160;</span>

					<div class="fieldValue">
						<xsl:call-template name="markContentEditableField">
							<xsl:with-param name="pObjectId" select="./@object_id"></xsl:with-param>
							<xsl:with-param name="pFieldId">398</xsl:with-param>
						</xsl:call-template>
						<xsl:attribute name="field_id">398</xsl:attribute>
						<xsl:attribute name="instance_id"><xsl:value-of select="./@instance_id" /></xsl:attribute>
						<xsl:attribute name="class">P-Inline</xsl:attribute>
						<xsl:apply-templates select="./fields/*[@id='398']/value" mode="formatting"/>
					</div>
				</div>
			</xsl:if>
			<xsl:if test="./fields/*[@id='459']/value != ''">
				<div class="myfieldHolder">
					<span class="fieldLabel">
						<xsl:value-of select="$lDataFormatVersion" />:&#160;</span>
						<div class="fieldValue">
							<xsl:call-template name="markContentEditableField">
								<xsl:with-param name="pObjectId" select="./@object_id"></xsl:with-param>
								<xsl:with-param name="pFieldId">459</xsl:with-param>
							</xsl:call-template>
							<xsl:attribute name="field_id">459</xsl:attribute>
							<xsl:attribute name="instance_id"><xsl:value-of select="./@instance_id" /></xsl:attribute>
							<xsl:attribute name="class">P-Inline</xsl:attribute>
							<xsl:apply-templates select="./fields/*[@id='459']/value" mode="formatting"/>
						</div>
				</div>
			</xsl:if>

			<xsl:if test="./fields/*[@id='399']/value != ''">
				<div class="myfieldHolder">
					<span class="fieldLabel">
						<xsl:value-of select="$lDescription" />:&#160;</span>
					<div class="myfieldValue">
						<xsl:call-template name="markContentEditableField">
							<xsl:with-param name="pObjectId" select="./@object_id"></xsl:with-param>
							<xsl:with-param name="pFieldId">399</xsl:with-param>
						</xsl:call-template>
						<xsl:attribute name="field_id">399</xsl:attribute>
						<xsl:attribute name="instance_id"><xsl:value-of select="./@instance_id" /></xsl:attribute>
						<xsl:attribute name="class">P-Inline</xsl:attribute>
						<xsl:apply-templates select="./fields/*[@id='399']/value" mode="formatting"/>
					</div>
				</div>
			</xsl:if>
			<!-- Number of columns -->
			<xsl:if test="./fields/*[@id='400']/value &gt; 0">
				<xsl:variable name="lColumnLabel" select="./column/fields/*[@id='401']/@field_name" />
				<xsl:variable name="lColumnDescription" select="./column/fields/*[@id='402']/@field_name" />
				<div class="Table-Body">
					<table>
						<tr>
							<th style="color:#404040;">
								<xsl:value-of select="$lColumnLabel" />
							</th>
							<th style="color:#404040;">
								<xsl:value-of select="$lColumnDescription" />
							</th>
						</tr>
							<xsl:apply-templates select="//*[@object_id='142']" mode="dataSetColumns"/>
					</table>
				</div>
			</xsl:if>
		</div>
	</xsl:template>
	
	<!-- dataSetColumns -->
	<xsl:template match="*" mode="dataSetColumns">
			<tr>		
				<xsl:if test="./fields/*[@id='401']/value != ''">
					<td>
						<xsl:call-template name="markContentEditableField">
							<xsl:with-param name="pObjectId" select="./@object_id"></xsl:with-param>
							<xsl:with-param name="pFieldId">401</xsl:with-param>
						</xsl:call-template>
						<xsl:attribute name="instance_id"><xsl:value-of select="./@instance_id" /></xsl:attribute>
						<xsl:attribute name="field_id">401</xsl:attribute>
						<xsl:attribute name="class">P-Inline</xsl:attribute>
						<xsl:apply-templates select="./fields/*[@id='401']/value" mode="formatting"/>
					</td>
				</xsl:if>	
				<xsl:if test="./fields/*[@id='402']/value != ''">
					<td>
						<xsl:call-template name="markContentEditableField">
							<xsl:with-param name="pObjectId" select="./@object_id"></xsl:with-param>
							<xsl:with-param name="pFieldId">402</xsl:with-param>
						</xsl:call-template>
						<xsl:attribute name="instance_id"><xsl:value-of select="./@instance_id" /></xsl:attribute>
						<xsl:attribute name="field_id">402</xsl:attribute>
						<xsl:attribute name="class">P-Inline</xsl:attribute>
						<xsl:apply-templates select="./fields/*[@id='402']/value" mode="formatting"/>
					</td>
				</xsl:if>
			</tr>	
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
</xsl:stylesheet>