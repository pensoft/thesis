<?xml version='1.0'?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:tp="http://www.plazi.org/taxpub"  xmlns:php="http://php.net/xsl" exclude-result-prefixes="php tp xlink xsl">
	<xsl:param  name="gGenerateFullHtml">1</xsl:param>
	<!--Whether to generate the whole HTML or just a fragment,
		i.e. to add tags like html, head
		or just put everything in 1 div
	 -->
	<xsl:param  name="pDocumentId">0</xsl:param>
	<xsl:param  name="pMarkContentEditableFields">0</xsl:param>
	<xsl:param  name="pShowPreviewCommentTip">1</xsl:param>
	<xsl:param  name="pPutEditableJSAndCss">0</xsl:param>
	<xsl:param  name="pTrackFigureAndTableChanges">0</xsl:param>

	<xsl:key name="materialType" match="*[@object_id='37']" use=".//fields/*[@id='209']/value/@value_id"></xsl:key>

	<xsl:variable name="gAuthorshipEditorType">2</xsl:variable>
	<xsl:variable name="gEditorAuthorshipEditorType">1</xsl:variable>
	
	<xsl:template match="/document">
		<xsl:variable name="lContent">
			<div class="P-Article-Preview">
				<xsl:call-template name="journalInfo">
					<xsl:with-param name="pDocumentNode" select="/document/document_info" />
				</xsl:call-template>
				<div>
					<xsl:attribute name="instance_id">
						<xsl:value-of select="/document/objects/*[@object_id='14']/*[@object_id='9']/@instance_id" />
					</xsl:attribute>
					<div>
						<xsl:attribute name="field_id">3</xsl:attribute>
						<xsl:apply-templates select="/document/objects/*[@object_id='14']/*[@object_id='9']/fields/*[@id='3']" mode="articleTitle" />
					</div>
					<xsl:call-template name="authors">
						<xsl:with-param name="pDocumentNode" select="/document" />
					</xsl:call-template>
				</div>
				<xsl:apply-templates select="/document/objects/*[@object_id='14']/*[@object_id='15']" mode="abstractAndKeywords" />
				<xsl:apply-templates select="/document/objects/*[@object_id &gt; 0]" mode="bodySections" />
				<xsl:apply-templates select="/document/figures/figure" mode="figures" />
				<xsl:apply-templates select="/document/tables/table" mode="tables" />
				<xsl:apply-templates select="/document/objects/*[@object_id &gt; 0]" mode="articleBack" />
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

	<xsl:template match="b|i|u|strong|em|sup|sub|p|ul|li|ol|insert|delete" mode="formatting">
		<xsl:copy-of select="."/>
<!-- 		<xsl:variable name="lNodeName" select="php:function('getFormattingNodeRealNameForPmt', string(local-name(.)))" /> -->
<!-- 		<xsl:element name="{$lNodeName}"> -->
<!-- 			<xsl:apply-templates mode="formatting" /> -->
<!-- 		</xsl:element> -->
	</xsl:template>

	<xsl:template match="b|i|u|strong|em|sup|sub|p|ul|li|ol|table|tr|td|tbody|th" mode="table_formatting">
		<xsl:copy-of select="."/>
<!-- 		<xsl:variable name="lNodeName" select="php:function('getFormattingNodeRealNameForPmt', string(local-name(.)))" /> -->
<!-- 		<xsl:element name="{$lNodeName}"> -->
<!-- 			<xsl:apply-templates mode="formatting" /> -->
<!-- 		</xsl:element> -->
	</xsl:template>

	<!-- Removes spaces -->
	<xsl:template match="*" mode="formatting_nospace">
		<xsl:value-of select="normalize-space()"/>
	</xsl:template>

	<!-- Discussion -->
	<xsl:template match="*[@object_id='58']" mode="bodySections">
		<xsl:if test="./fields/*[@id='224']/value != '' or ./subsection/@object_id != ''">
			<xsl:variable name="lSecTitle">Discussion</xsl:variable>
			<div class="P-Article-Preview-Block">
				<xsl:attribute name="instance_id"><xsl:value-of select="./@instance_id" /></xsl:attribute>
				<!--anchor-->
				<span class="anchor" id="discussions"></span>
				<div class="P-Article-Preview-Block-Title"><xsl:value-of select="$lSecTitle" /></div>
				<div class="P-Article-Preview-Block-Content">
					<xsl:attribute name="field_id">224</xsl:attribute>
					<!--CALL MARK CONTENT EDITABLE TEMPLATE -->
					<xsl:call-template name="markContentEditableField">
						<xsl:with-param name="pObjectId" select="./@object_id" />
						<xsl:with-param name="pFieldId">224</xsl:with-param>
					</xsl:call-template>
					<xsl:apply-templates select="./fields/*[@id='224']" mode="formatting"/>
				</div>
				<xsl:apply-templates mode="bodySubsection" select="./subsection"/>
			</div>
		</xsl:if>
	</xsl:template>

	<!-- Material and Methods -->
	<xsl:template match="*[@object_id='18']" mode="bodySections">
		<xsl:if test="./fields/*[@id='22']/value != '' or ./subsection/@object_id != ''">
			<xsl:variable name="lSecTitle">Material and methods</xsl:variable>
			<div class="P-Article-Preview-Block">
				<xsl:attribute name="instance_id"><xsl:value-of select="./@instance_id" /></xsl:attribute>
				<!--anchor-->
				<span class="anchor" id="material_and_methods"></span>
				<div class="P-Article-Preview-Block-Title"><xsl:value-of select="$lSecTitle" /></div>
				<div class="P-Article-Preview-Block-Content">
					<xsl:attribute name="field_id">22</xsl:attribute>
					<!--CALL MARK CONTENT EDITABLE TEMPLATE -->
					<xsl:call-template name="markContentEditableField">
						<xsl:with-param name="pObjectId" select="./@object_id" />
						<xsl:with-param name="pFieldId">22</xsl:with-param>
					</xsl:call-template>
					<xsl:apply-templates select="./fields/*[@id='22']" mode="formatting"/>
				</div>
				<xsl:apply-templates mode="bodySubsection" select="./subsection"/>
			</div>
		</xsl:if>
	</xsl:template>

	<!-- Checklist -->
	<xsl:template match="*[@object_id='211']" mode="bodySections">
		<xsl:if test="count(./*[@object_id='212']) &gt; 0">
			<div>
				<xsl:attribute name="instance_id"><xsl:value-of select="./@instance_id" /></xsl:attribute>
				<h1 id="checklist">
					<xsl:call-template name="markContentEditableField">
						<xsl:with-param name="pObjectId" select="./@object_id" />
						<xsl:with-param name="pFieldId">413</xsl:with-param>
					</xsl:call-template>
					<xsl:attribute name="field_id">413</xsl:attribute>
					<xsl:value-of select="./fields/*[@id='413']/value" />
				</h1>
					<xsl:for-each select="//*[@object_id='212']">
						<xsl:apply-templates select="." mode="checklistLocality"/>
					</xsl:for-each>
			</div>
		</xsl:if>
	</xsl:template>

	<!-- Checklist Locality new -->
	<xsl:template match="*" mode="checklistLocality">
		<xsl:variable name="lChecklistLocalityTypeId" select=".//*[@id='445']/value/@value_id" />
		<div class="P-Article-Preview-Block">
			<xsl:attribute name="instance_id"><xsl:value-of select="./@instance_id" /></xsl:attribute>
			<h2>
				<xsl:call-template name="markContentEditableField">
					<xsl:with-param name="pObjectId" select="./@object_id" />
					<xsl:with-param name="pFieldId">357</xsl:with-param>
				</xsl:call-template>
				<xsl:attribute name="field_id">357</xsl:attribute>
				<xsl:value-of select="./fields/*[@id='357']" />
			</h2>
			<xsl:choose>
					<!-- Locality/Region -->
					<xsl:when test="$lChecklistLocalityTypeId = 1"> 
						<xsl:apply-templates select="." mode="localityType"/>
					</xsl:when>
					<!-- Natura 2000 -->
					<xsl:when test="$lChecklistLocalityTypeId = 3"> 
						<xsl:if test="./fields/*[@id='448']/value != ''">
							<div class="myfieldHolder">
								<xsl:attribute name="instance_id"><xsl:value-of select="./@instance_id" /></xsl:attribute>
								<span class="fieldLabel">Natura 2000 code:&#160;</span>
								<span class="fieldValue">
									<xsl:attribute name="field_id">448</xsl:attribute>
									<a target="_blank">
										<xsl:attribute name="href"><xsl:value-of select="concat('http://natura2000.eea.europa.eu/natura2000/SDFPublic.aspx?site=', ./fields/*[@id='448']/value)" /></xsl:attribute>
										<xsl:apply-templates select="./fields/*[@id='448']" mode="formatting"/>
									</a>
								</span>
							</div>
						</xsl:if>
					</xsl:when>
					<!-- Habitat -->
					<xsl:when test="$lChecklistLocalityTypeId = 2"> 
						<xsl:if test="./fields/*[@id='446']/value != ''">
							<div class="myfieldHolder">
								<xsl:attribute name="instance_id"><xsl:value-of select="./@instance_id" /></xsl:attribute>
								<span class="fieldLabel">Habitat code:&#160;</span>
								<span class="fieldValue">
									<xsl:call-template name="markContentEditableField">
										<xsl:with-param name="pObjectId" select="./@object_id" />
										<xsl:with-param name="pFieldId">446</xsl:with-param>
									</xsl:call-template>
									<xsl:attribute name="field_id">446</xsl:attribute>
									<xsl:apply-templates select="./fields/*[@id='446']" mode="formatting"/>
								</span>
							</div>
						</xsl:if>
						<xsl:if test="./fields/*[@id='447']/value != ''">
							<div class="myfieldHolder">
								<xsl:attribute name="instance_id"><xsl:value-of select="./@instance_id" /></xsl:attribute>
								<span class="fieldLabel">Habitat classification:&#160;</span>
								<span class="fieldValue">
									<xsl:call-template name="markContentEditableField">
										<xsl:with-param name="pObjectId" select="./@object_id" />
										<xsl:with-param name="pFieldId">447</xsl:with-param>
									</xsl:call-template>
									<xsl:attribute name="field_id">447</xsl:attribute>
									<xsl:apply-templates select="./fields/*[@id='447']" mode="formatting"/>
								</span>
							</div>
						</xsl:if>
					</xsl:when>
					<xsl:otherwise>

					</xsl:otherwise>
			</xsl:choose>
			<xsl:if test="./fields/*[@id='379']/value != ''">
				<div class="myfieldHolder">
					<xsl:attribute name="instance_id"><xsl:value-of select="./@instance_id" /></xsl:attribute>
					<span class="fieldLabel">Description:&#160;</span>
					<div class="fieldValue">
						<xsl:call-template name="markContentEditableField">
							<xsl:with-param name="pObjectId" select="./@object_id" />
							<xsl:with-param name="pFieldId">379</xsl:with-param>
						</xsl:call-template>
						<xsl:attribute name="field_id">379</xsl:attribute>
						<xsl:attribute name="instance_id"><xsl:value-of select="./@instance_id" /></xsl:attribute>
						<xsl:apply-templates select="./fields/*[@id='379']" mode="formatting"/>
					</div>
				</div>
			</xsl:if>
			<xsl:apply-templates select="./*[@object_id='205']" mode="checklistTaxon"/>
		</div>
	</xsl:template>
	
	<!-- Locality type -->
	<xsl:template match="*" mode="localityType">
		<div class="myfieldHolder materials">
			<xsl:attribute name="instance_id"><xsl:value-of select="./@instance_id" /></xsl:attribute>
			<span class="fieldLabel">Locality:&#160;</span>
			<span class="fieldValue">
				<xsl:for-each select="./fields/*[(@id &gt; 108 and @id &lt; 127) or (@id &gt; 131 and @id &lt; 135)]">
					<xsl:sort select="@id" order="ascending" />
					<xsl:if test="./value != '' ">
						<xsl:apply-templates select="." mode="treatmentMaterialField" />
						<xsl:if test="position() != last()"><xsl:text>, </xsl:text></xsl:if>
					</xsl:if>
				</xsl:for-each>
			</span>
		</div>
	</xsl:template>
</xsl:stylesheet>