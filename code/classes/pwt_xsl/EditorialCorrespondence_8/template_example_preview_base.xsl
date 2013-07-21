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
	<xsl:variable name="gAuthorshipEditorType">2</xsl:variable>
	<xsl:param  name="pSiteUrl"></xsl:param>

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
						<xsl:value-of select="/document/objects/*[@object_id='152']/*[@object_id='153']/@instance_id"/>
					</xsl:attribute>
					<div>
						<xsl:call-template name="markContentEditableField">
							<xsl:with-param name="pObjectId" select="./@object_id"></xsl:with-param>
							<xsl:with-param name="pFieldId">3</xsl:with-param>
						</xsl:call-template>
						<xsl:attribute name="field_id">3</xsl:attribute>
						<xsl:apply-templates select="/document/objects/*[@object_id='152']/*[@object_id='153']/fields/*[@id='3']" mode="articleTitle"/>
					</div>
					<xsl:call-template name="authors">
						<xsl:with-param name="pDocumentNode" select="/document"/>
					</xsl:call-template>
				</div>

				<xsl:apply-templates select="/document/objects/*[@object_id='14']/*[@object_id='15']" mode="abstractAndKeywords"/>
				<xsl:apply-templates select="/document/objects/*[@object_id &gt; 0]" mode="bodySections"/>
				<xsl:apply-templates select="/document/figures/figure" mode="figures"/>
				<xsl:apply-templates select="/document/tables/table" mode="tables"/>
				<xsl:apply-templates select="/document/objects/*[@object_id &gt; 0]" mode="articleBack"/>
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
		<xsl:value-of select="normalize-space()"/>
	</xsl:template>

	<!-- Main text -->
	<xsl:template match="*[@object_id='165']" mode="articleBack">
		<xsl:variable name="lSecTitle" select="./@display_name"></xsl:variable>
			<div class="P-Article-Preview-Block">
				<xsl:attribute name="instance_id"><xsl:value-of select="./@instance_id" /></xsl:attribute>
				<xsl:if test="$lSecTitle != 'Main text'">
					<div class="P-Article-Preview-Block-Title"><xsl:value-of select="$lSecTitle"></xsl:value-of></div>
				</xsl:if>
				<div class="P-Article-Preview-Block-Content">
					<xsl:attribute name="field_id">412</xsl:attribute>
					<xsl:call-template name="markContentEditableField">
						<xsl:with-param name="pObjectId" select="./@object_id"></xsl:with-param>
						<xsl:with-param name="pFieldId">412</xsl:with-param>
					</xsl:call-template>
					<xsl:apply-templates select="./fields/*[@id='412']" mode="formatting"/>
				</div>
				<xsl:apply-templates mode="bodySubsection" select="./subsection"/>
			</div>
	</xsl:template>
</xsl:stylesheet>