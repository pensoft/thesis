<?xml version='1.0'?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:tp="http://www.plazi.org/taxpub"  xmlns:php="http://php.net/xsl" exclude-result-prefixes="php tp xlink xsl">
	<xsl:param  name="gGenerateFullHtml">1</xsl:param>
	<xsl:param  name="pDocumentId">0</xsl:param>
	<xsl:param  name="pMarkContentEditableFields">0</xsl:param>
	<xsl:param  name="pShowPreviewCommentTip">1</xsl:param>
	<xsl:param  name="pPutEditableJSAndCss">0</xsl:param>
	<xsl:param  name="pTrackFigureAndTableChanges">0</xsl:param>
	<xsl:param  name="pSiteUrl"></xsl:param>
	<!-- Дали да генерира целия html или само фрагмент от него
		т.е. дали да слага тагове htmk, head ...
		или само да сложи всичко в 1 див
	 -->

	<xsl:variable name="gAuthorshipEditorType">2</xsl:variable>

	<xsl:variable name="gEditorAuthorshipEditorType">1</xsl:variable>
	
	<!-- MARKING EDITABLE FIELDS TEMPLATE --> 
	<xsl:template name="markContentEditableField">
		<xsl:param name="pObjectId"></xsl:param>
		<xsl:param name="pFieldId"></xsl:param>

		<xsl:if test="$pMarkContentEditableFields &gt; 0">
			<xsl:variable name="lCheck" select="php:function('checkIfObjectFieldIsEditable', string($pObjectId), string($pFieldId))"></xsl:variable>
			<xsl:if test="$lCheck &gt; 0">
				<xsl:attribute name="contenteditable">true</xsl:attribute>
			</xsl:if>
		</xsl:if>
	</xsl:template>
	
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

	<xsl:template match="b|i|u|strong|em|sup|sub|p|ul|li|ol|insert|delete" mode="formatting">
		<xsl:copy-of select="."/>
<!-- 		<xsl:variable name="lNodeName" select="php:function('getFormattingNodeRealNameForPmt', string(local-name(.)))"></xsl:variable> -->
<!-- 		<xsl:element name="{$lNodeName}"> -->
<!-- 			<xsl:apply-templates mode="formatting"></xsl:apply-templates> -->
<!-- 		</xsl:element> -->
	</xsl:template>

	<xsl:template match="*" mode="formatting_output_escape">
		<xsl:value-of select="." disable-output-escaping="yes"/>
	</xsl:template>

	<xsl:template match="b|i|u|strong|em|sup|sub|p|ul|ol|li|table|tr|td|tbody|th" mode="table_formatting">
		<xsl:copy-of select="."/>
<!-- 		<xsl:variable name="lNodeName" select="php:function('getFormattingNodeRealNameForPmt', string(local-name(.)))"></xsl:variable> -->
<!-- 		<xsl:element name="{$lNodeName}"> -->
<!-- 			<xsl:apply-templates mode="formatting"></xsl:apply-templates> -->
<!-- 		</xsl:element> -->
	</xsl:template>


	<!-- Removes spaces -->
	<xsl:template match="*" mode="formatting_nospace">
		<xsl:param name="lTreatmentUrl"/>
		<xsl:value-of select="normalize-space()"/>
	</xsl:template>


	<!-- Default-ен празен темплейт.
		Секциите които искаме да мачнем ще ги специфицираме ръчно
	
	<xsl:template match="*" mode="bodySections"></xsl:template>
 -->



	<!-- Discussions -->
	<xsl:template match="*[@object_id='171']" mode="bodySections">
		<xsl:if test="./fields/*[@id='224']/value != '' or ./subsection/@object_id != ''">
			<xsl:variable name="lSecTitle" select="./@display_name"></xsl:variable>
			<div class="P-Article-Preview-Block">
				<xsl:attribute name="instance_id"><xsl:value-of select="./@instance_id" /></xsl:attribute>
				<!--anchor-->
				<span class="anchor" id="discussions"></span>
				<h1><xsl:value-of select="$lSecTitle"></xsl:value-of></h1>
				<xsl:if test="./fields/*[@id='224']/value != ''" >
				<div class="P-Article-Preview-Block-Content">
					<xsl:attribute name="field_id">224</xsl:attribute>
					<!--CALL MARK CONTENT EDITABLE TEMPLATE -->
					<xsl:call-template name="markContentEditableField">
						<xsl:with-param name="pObjectId" select="./@object_id"></xsl:with-param>
						<xsl:with-param name="pFieldId">224</xsl:with-param>
					</xsl:call-template>
					<xsl:apply-templates select="./fields/*[@id='224']" mode="formatting"/>
				</div>
				</xsl:if>
				<xsl:apply-templates mode="bodySubsection" select="./subsection"/>
			</div>
		</xsl:if>
	</xsl:template>

	<!-- Material and Methods -->
	<xsl:template match="*[@object_id='168']" mode="bodySections">
		<xsl:if test="./fields/*[@id='22']/value != '' or ./subsection/@object_id != ''">
			<xsl:variable name="lSecTitle" select="./@display_name"></xsl:variable>
			<div class="P-Article-Preview-Block">
				<xsl:attribute name="instance_id"><xsl:value-of select="./@instance_id" /></xsl:attribute>
				<!--anchor-->
				<span class="anchor" id="material_and_methods"></span>
				<h1><xsl:value-of select="$lSecTitle"></xsl:value-of></h1>
				<xsl:if test="./fields/*[@id='22']/value != '' ">
				<div class="P-Article-Preview-Block-Content">
					<xsl:attribute name="field_id">22</xsl:attribute>
					<!--CALL MARK CONTENT EDITABLE TEMPLATE -->
					<xsl:call-template name="markContentEditableField">
						<xsl:with-param name="pObjectId" select="./@object_id"></xsl:with-param>
						<xsl:with-param name="pFieldId">22</xsl:with-param>
					</xsl:call-template>
					<xsl:apply-templates select="./fields/*[@id='22']" mode="formatting"/>
				</div>
				</xsl:if>
				<xsl:apply-templates mode="bodySubsection" select="./subsection"/>
			</div>
		</xsl:if>
	</xsl:template>

	<!-- Data resources -->
	<xsl:template match="*[@object_id='169']" mode="bodySections">
		<xsl:if test="./fields/*[@id='21']/value != '' or ./subsection/@object_id != ''">
			<xsl:variable name="lSecTitle" select="./@display_name"></xsl:variable>
			<div class="P-Article-Preview-Block">
				<xsl:attribute name="instance_id"><xsl:value-of select="./@instance_id" /></xsl:attribute>
				<!--anchor-->
				<span class="anchor" id="data_resources"></span>
				<h1><xsl:value-of select="$lSecTitle"></xsl:value-of></h1>
				<xsl:if test="./fields/*[@id='21']/value != ''">
				<div class="P-Article-Preview-Block-Content">
					<xsl:attribute name="field_id">21</xsl:attribute>
					<!--CALL MARK CONTENT EDITABLE TEMPLATE -->
					<xsl:call-template name="markContentEditableField">
						<xsl:with-param name="pObjectId" select="./@object_id"></xsl:with-param>
						<xsl:with-param name="pFieldId">21</xsl:with-param>
					</xsl:call-template>
					<xsl:apply-templates select="./fields/*[@id='21']" mode="formatting"/>
				</div>
				</xsl:if>
				<xsl:apply-templates mode="bodySubsection" select="./subsection"/>
			</div>
		</xsl:if>
	</xsl:template>

	<!-- Results -->
	<xsl:template match="*[@object_id='170']" mode="bodySections">
		<xsl:if test="./fields/*[@id='23']/value != '' or ./subsection/@object_id != ''">
			<xsl:variable name="lSecTitle" select="./@display_name"></xsl:variable>
			<div class="P-Article-Preview-Block">
				<xsl:attribute name="instance_id"><xsl:value-of select="./@instance_id" /></xsl:attribute>
				<!--anchor-->
				<span class="anchor" id="results"></span>
				<h1><xsl:value-of select="$lSecTitle"></xsl:value-of></h1>
				<xsl:if test="./fields/*[@id='23']/value != ''">
				<div class="P-Article-Preview-Block-Content">
					<xsl:attribute name="field_id">23</xsl:attribute>
					<!--CALL MARK CONTENT EDITABLE TEMPLATE -->
					<xsl:call-template name="markContentEditableField">
						<xsl:with-param name="pObjectId" select="./@object_id"></xsl:with-param>
						<xsl:with-param name="pFieldId">23</xsl:with-param>
					</xsl:call-template>
					<xsl:apply-templates select="./fields/*[@id='23']" mode="formatting"/>
				</div>
				</xsl:if>
				<xsl:apply-templates mode="bodySubsection" select="./subsection"/>
			</div>
		</xsl:if>
	</xsl:template>
</xsl:stylesheet>