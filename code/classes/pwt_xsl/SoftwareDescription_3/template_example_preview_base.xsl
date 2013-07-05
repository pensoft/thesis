<?xml version='1.0'?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:tp="http://www.plazi.org/taxpub"  xmlns:php="http://php.net/xsl" exclude-result-prefixes="php tp xlink xsl">
	<xsl:param  name="gGenerateFullHtml">1</xsl:param>
	<xsl:param  name="pDocumentId">0</xsl:param>
	<xsl:param  name="pMarkContentEditableFields">0</xsl:param>
	<xsl:param  name="pShowPreviewCommentTip">1</xsl:param>
	<xsl:param  name="pPutEditableJSAndCss">0</xsl:param>
	<xsl:param  name="pTrackFigureAndTableChanges">0</xsl:param>
	
	<xsl:key name="materialType" match="*[@object_id='37']" use="./fields/*[@id='209']/value/@value_id"></xsl:key>
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

				<xsl:apply-templates select="/document/figures/figure" mode="figures"></xsl:apply-templates>

				<xsl:apply-templates select="/document/tables/table" mode="tables"></xsl:apply-templates>

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
									<xsl:with-param name="pObjectId">115</xsl:with-param>
									<xsl:with-param name="pFieldId">312</xsl:with-param>
								</xsl:call-template>
								<xsl:attribute name="field_id">312</xsl:attribute>
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
	

	<!-- Software Description Document Start -->

	<!-- Web Location (URIs) -->
	<xsl:template match="*[@object_id='112']" mode="bodySections">
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
									<xsl:call-template name="markContentEditableField">
										<xsl:with-param name="pObjectId" select="../../@object_id"></xsl:with-param>
										<xsl:with-param name="pFieldId" select="./@id"></xsl:with-param>
									</xsl:call-template>
									<xsl:attribute name="field_id"><xsl:value-of select="./@id"></xsl:value-of></xsl:attribute>
									<xsl:attribute name="instance_id"><xsl:value-of select="../../@instance_id"></xsl:value-of></xsl:attribute>
									<a>
										<xsl:attribute name="href"><xsl:value-of select="normalize-space(./value)"/></xsl:attribute>
										<xsl:attribute name="target"><xsl:text>_blank</xsl:text></xsl:attribute>
										<xsl:value-of select="normalize-space(./value)"/>
									</a>
									<!-- <xsl:apply-templates select="./value" mode="formatting"/> -->
								</div>
						</div>
					</xsl:if>
				</xsl:for-each>
			</div>
		</xsl:if>
	</xsl:template>

	<!-- Technical specification -->
	<xsl:template match="*[@object_id='113']" mode="bodySections">
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
									<xsl:call-template name="markContentEditableField">
										<xsl:with-param name="pObjectId" select="../../@object_id"></xsl:with-param>
										<xsl:with-param name="pFieldId" select="./@id"></xsl:with-param>
									</xsl:call-template>
									<xsl:attribute name="field_id"><xsl:value-of select="./@id"></xsl:value-of></xsl:attribute>
									<xsl:apply-templates select="./value" mode="formatting"/>
								</div>
						</div>
					</xsl:if>
				</xsl:for-each>
			</div>
		</xsl:if>
	</xsl:template>

	<!-- Repository -->
	<xsl:template match="*[@object_id='114']" mode="bodySections">
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
								<xsl:call-template name="markContentEditableField">
										<xsl:with-param name="pObjectId" select="../../@object_id"></xsl:with-param>
										<xsl:with-param name="pFieldId" select="./@id"></xsl:with-param>
								</xsl:call-template>
								<xsl:attribute name="field_id"><xsl:value-of select="./@id"></xsl:value-of></xsl:attribute>	
								<xsl:if test="./@id = '307'">
									<a>
										<xsl:attribute name="href"><xsl:value-of select="normalize-space(./value)"/></xsl:attribute>
										<xsl:attribute name="target"><xsl:text>_blank</xsl:text></xsl:attribute>
										<xsl:value-of select="normalize-space(./value)"/>
									</a>
								</xsl:if>
								<xsl:if test="./@id != '307'">
									<xsl:apply-templates select="./value" mode="formatting"/>
								</xsl:if>
							</div>
						</div>
					</xsl:if>
				</xsl:for-each>
			</div>
		</xsl:if>
	</xsl:template>

	<!-- Implementation -->
	<xsl:template match="*[@object_id='116']" mode="bodySections">
		<xsl:apply-templates select="." mode="bodyInlineSection"/>
	</xsl:template>

	<!-- Software Description Document END-->

	<xsl:template match="*" mode="bodyInlineSection">
		<xsl:if test="(count(./fields/*[@id != '' and value !='']) &gt; 0)">
			<xsl:variable name="lSecTitle" select="./@display_name"></xsl:variable>
			<div class="P-Article-Preview-Block">
				<xsl:attribute name="instance_id"><xsl:value-of select="./@instance_id" /></xsl:attribute>
				<div class="P-Article-Preview-Block-Title"><xsl:value-of select="$lSecTitle"></xsl:value-of></div>
				<xsl:for-each select="./fields/*[@id != '']">
					<xsl:if test="./value !=''">
						<div class="P-Article-Preview-Block-Content">
							<h2 class="subsection" field_id="211">
								<xsl:value-of select="./@field_name"></xsl:value-of>
							</h2>	
							<div>
								<xsl:attribute name="field_id"><xsl:value-of select="./@id"></xsl:value-of></xsl:attribute>
								<xsl:attribute name="instance_id"><xsl:value-of select="../../@instance_id"></xsl:value-of></xsl:attribute>
								<!--CALL MARK CONTENT EDITABLE TEMPLATE -->
								<xsl:call-template name="markContentEditableField">
									<xsl:with-param name="pObjectId" select="../../@object_id"></xsl:with-param>
									<xsl:with-param name="pFieldId" select="./@id"></xsl:with-param>
								</xsl:call-template>
								<xsl:apply-templates select="./value" mode="formatting"/>
							</div>
						</div>
					</xsl:if>
				</xsl:for-each>
			</div>
		</xsl:if>
	</xsl:template>


</xsl:stylesheet>