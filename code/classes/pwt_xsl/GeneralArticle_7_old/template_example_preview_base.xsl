<?xml version='1.0'?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:tp="http://www.plazi.org/taxpub"  xmlns:php="http://php.net/xsl" exclude-result-prefixes="php tp xlink xsl">
	<xsl:param  name="gGenerateFullHtml">1</xsl:param>
	<xsl:param  name="pDocumentId">0</xsl:param>
	<xsl:param  name="pMarkContentEditableFields">0</xsl:param>
	<xsl:param  name="pShowPreviewCommentTip">1</xsl:param>
	<xsl:param  name="pPutEditableJSAndCss">0</xsl:param>

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
						<link type="text/css" rel="stylesheet" href="/lib/css/article_preview.css" media="all" title="default"/>
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

	<!-- Formats treatment links -->
	<xsl:template match="*" mode="formatting_treatment_link">
		<xsl:param name="lTreatmentUrl"/>
		<xsl:param name="lCurrentVal" select="."/>
		<xsl:if test="$lTreatmentUrl != 'URL'">
			<a>
				<xsl:attribute name="href"><xsl:value-of select="translate(normalize-space(concat($lTreatmentUrl, $lCurrentVal)) , ' ', '')"/></xsl:attribute>
				<xsl:attribute name="target"><xsl:text>_blank</xsl:text></xsl:attribute>
				<xsl:value-of select="$lCurrentVal"/>
			</a>
		</xsl:if>
		<xsl:if test="$lTreatmentUrl = 'URL'">
			<a>
				<xsl:attribute name="href"><xsl:value-of select="normalize-space($lCurrentVal)"/></xsl:attribute>
				<xsl:attribute name="target"><xsl:text>_blank</xsl:text></xsl:attribute>
				<xsl:value-of select="normalize-space($lCurrentVal)"/>
			</a>
		</xsl:if>
	</xsl:template>

	<!-- Removes spaces -->
	<xsl:template match="*" mode="formatting_nospace">
		<xsl:param name="lTreatmentUrl"/>
		<xsl:value-of select="normalize-space()"/>
	</xsl:template>

	<!-- Formatting uploaded files spaces -->
	<xsl:template match="*" mode="formatting_uploaded_file">
		<xsl:param name="lFileName"/>
		<xsl:param name="lUploadedFileName"/>
		<xsl:if test="$lUploadedFileName != ''">
			<span>
				<xsl:text>Filename: </xsl:text><xsl:value-of select="normalize-space($lUploadedFileName)"/>
				<xsl:text> </xsl:text>
			</span>
		</xsl:if>
		<xsl:if test="$lFileName != ''">
			<a>
				<xsl:attribute name="href"><xsl:text>getfile.php?filename=</xsl:text><xsl:value-of select="normalize-space($lFileName)"/></xsl:attribute>
				<xsl:attribute name="target"><xsl:text>_blank</xsl:text></xsl:attribute>
				<xsl:attribute name="name"><xsl:value-of select="normalize-space($lFileName)"/></xsl:attribute>
				<xsl:text>Download file</xsl:text>
			</a>
			<span>
				<xsl:text> (</xsl:text>
					<xsl:value-of select="php:function('getUploadedFileSize', string($lFileName))"></xsl:value-of>
				<xsl:text>)</xsl:text>
			</span>
		</xsl:if>
	</xsl:template>

	<!-- Темплейт за показване на информация за списанието -->
	<xsl:template name="journalInfo">
		<xsl:param name="pDocumentNode" />

		<xsl:variable name="lJournalName" select="$pDocumentNode/journal_name"></xsl:variable>
		<xsl:variable name="lDocumentType" select="$pDocumentNode/document_type"></xsl:variable>

		<div class="P-Article-Preview-Antet">
			<xsl:value-of select="$lJournalName"></xsl:value-of>
			<xsl:text> : </xsl:text>
			<xsl:value-of select="$lDocumentType"></xsl:value-of>
		</div>
		<xsl:call-template name="To-Make-Comments-Hint" />
		<div class="P-Clear"></div>
	</xsl:template>

	<!-- Темплейт за показване на заглавието -->
	<xsl:template match="*" mode="articleTitle">
		<div class="P-Article-Preview-Title">
			<!--anchor-->
			<span class="anchor" id="article_metadata"></span>
			<xsl:apply-templates select="." mode="formatting"/>
		</div>
	</xsl:template>

	<!-- Темплейт за показване на авторите -->
	<xsl:template name="authors">
		<xsl:param name="pDocumentNode" />
		<div class="P-Article-Preview-Names">
			<xsl:for-each select="$pDocumentNode/objects/*[@object_id='14']/*[@object_id='9']/*[@object_id='8']">
				<xsl:apply-templates select="." mode="singleAuthor"></xsl:apply-templates>
				<xsl:if test="position()!=last()"><xsl:text>, </xsl:text></xsl:if>
			</xsl:for-each>
		</div>
		<div class="P-Article-Preview-Addresses">
			<xsl:for-each select="$pDocumentNode/objects/*[@object_id='14']/*[@object_id='9']/*[@object_id='8']/*[@object_id='5']">
				<xsl:apply-templates select="." mode="singleAuthorAddress"></xsl:apply-templates>
			</xsl:for-each>
		</div>

		<div class="P-Article-Preview-Base-Info-Block">
			<table cellspacing="0" cellpadding="0" border="0" width="100%">
				<tbody>
					<tr>
						<td>
							<div class="P-Article-Info-Block-Row">
								<xsl:text>Corresponding Author:</xsl:text>
								<xsl:for-each select="$pDocumentNode/objects/*[@object_id='14']/*[@object_id='9']/*[@object_id='8'][fields/*[@id='15']/value[@value_id='1']]">
									<xsl:apply-templates select="." mode="singleCorrespondingAuthor"></xsl:apply-templates>
									<xsl:if test="position()!=last()"><xsl:text>, </xsl:text></xsl:if>
								</xsl:for-each>
							</div>
							<div class="P-Article-Info-Block-Row">
								Accepted by: <span>ACADEMIC EDITOR</span>
							</div>
							<div class="P-Article-Info-Block-Row">
								Recieved <span>DATE RECEIVED</span> | accepted <span>DATE ACCEPTED</span> | published <span>DATE PUBLISHED</span>
							</div>
							<div class="P-Article-Info-Block-Row">
								<xsl:text>© </xsl:text><xsl:value-of select="php:function('getYear')"/><xsl:text> </xsl:text>
								<xsl:for-each select="$pDocumentNode/objects/*[@object_id='14']/*[@object_id='9']/*[@object_id='8']">
									<xsl:apply-templates select="." mode="singleCorrespondingAuthorInLicense"></xsl:apply-templates>
									<xsl:if test="position()!=last()"><xsl:text>, </xsl:text></xsl:if>
								</xsl:for-each>
								<xsl:text>. This is an open access article distributed under the terms of the </xsl:text>
								<span>Creative Commons Attribution License 3.0 (CC-BY)</span>
								<xsl:text>, which permits unrestricted use, distribution, and reproduction in any medium, provided the original author and source are credited.</xsl:text>
							</div>
						</td>
						<td width="95px" valign="middle" align="right">
							<img src="/i/open_access.png" alt="Open Access" />
						</td>
					</tr>
				</tbody>
			</table>
		</div>
	</xsl:template>

	<xsl:template match="*" mode="singleAuthor">
		<span>
			<xsl:attribute name="field_id" >6</xsl:attribute>
			<xsl:value-of select="./fields/*[@id=6]"></xsl:value-of>
		</span>
		<xsl:text> </xsl:text>
		<xsl:if test="count(./fields/*[@id=7]) &gt; 0">
			<span>
				<xsl:attribute name="field_id" >7</xsl:attribute>
				<xsl:value-of select="./fields/*[@id=7]"></xsl:value-of>
			</span>
			<xsl:text> </xsl:text>
		</xsl:if>
		<span>
			<xsl:attribute name="field_id" >8</xsl:attribute>
			<xsl:value-of select="./fields/*[@id=8]"></xsl:value-of>
		</span>
		<sup class="P-Current-Author-Addresses">
			<xsl:for-each select="./*[@object_id='5']" >
				<xsl:variable name="lCurrentNode" select="." />
				<xsl:variable name="lAffId" select="php:function('getContributorAffId', string($lCurrentNode/@instance_id))"></xsl:variable>
				<span class="P-Current-Author-Single-Address">
					<xsl:value-of select="php:function('getUriSymbol', string($lAffId))" />
				</span>
				<xsl:if test="position()!=last()"><xsl:text> , </xsl:text></xsl:if>
			</xsl:for-each>
		</sup>
	</xsl:template>

	<xsl:template match="*" mode="singleAuthorAddress">
		<xsl:variable name="lCurrentNode" select="." />
		<xsl:variable name="lAffId" select="php:function('getContributorAffId', string($lCurrentNode/@instance_id))"></xsl:variable>

		<div class="P-Single-Author-Address">
			<span class="P-Address-Symbol">
				<xsl:value-of select="php:function('getUriSymbol', string($lAffId))" /><xsl:text> </xsl:text>
			</span>
		    <xsl:apply-templates select="./fields/*[@id='9']" mode="formatting_nospace"/><xsl:text>, </xsl:text>
            <xsl:apply-templates select="./fields/*[@id='10']" mode="formatting_nospace"/><xsl:text>, </xsl:text>
            <xsl:apply-templates select="./fields/*[@id='11']" mode="formatting_nospace"/>
		</div>
	</xsl:template>

	<xsl:template match="*" mode="singleCorrespondingAuthor">
		<span>
			<xsl:attribute name="instance_id"><xsl:value-of select="./@instance_id" /></xsl:attribute>
			<span>
				<xsl:attribute name="field_id" >6</xsl:attribute>
				<xsl:value-of select="./fields/*[@id=6]"></xsl:value-of>
			</span>
			<xsl:text> </xsl:text>
			<xsl:if test="count(./fields/*[@id=7]) &gt; 0">
				<span>
					<xsl:attribute name="field_id" >7</xsl:attribute>
					<xsl:value-of select="./fields/*[@id=7]"></xsl:value-of>
				</span>
				<xsl:text> </xsl:text>
			</xsl:if>
			<span>
				<xsl:attribute name="field_id" >8</xsl:attribute>
				<xsl:value-of select="./fields/*[@id=8]"></xsl:value-of>
			</span>
			<xsl:text> (</xsl:text>
			<a>
				<xsl:attribute name="field_id" >4</xsl:attribute>
				<xsl:attribute name="href" ><xsl:text>mailto:</xsl:text><xsl:apply-templates select="./fields/*[@id=4]" mode="formatting_nospace"/></xsl:attribute>
				<xsl:apply-templates select="./fields/*[@id=4]" mode="formatting_nospace"/>
			</a>
			<xsl:text>)</xsl:text>
		</span>
	</xsl:template>

	<xsl:template match="*" mode="singleCorrespondingAuthorInLicense">
			<span>
				<xsl:attribute name="instance_id"><xsl:value-of select="./@instance_id" /></xsl:attribute>
				<span>
					<xsl:attribute name="field_id" >6</xsl:attribute>
					<xsl:apply-templates select="./fields/*[@id=6]" mode="formatting_nospace"/>
				</span>
				<xsl:text> </xsl:text>
				<xsl:if test="count(./fields/*[@id=7]) &gt; 0">
					<span>
						<xsl:attribute name="field_id" >7</xsl:attribute>
						<xsl:apply-templates select="./fields/*[@id=7]" mode="formatting_nospace"/>
					</span>
					<xsl:text> </xsl:text>
				</xsl:if>
				<span>
					<xsl:attribute name="field_id" >8</xsl:attribute>
					<xsl:apply-templates select="./fields/*[@id=8]" mode="formatting_nospace"/>
				</span>
			</span>
	</xsl:template>

	<!-- Темплейт за показване на абстракт-а и ключовите думи
	 -->
	<xsl:template match="*" mode="abstractAndKeywords">
			<div>
				<xsl:attribute name="instance_id"><xsl:value-of select="./@instance_id" /></xsl:attribute>
				<xsl:if test="./fields/*[@id='18']/value != ''">
					<div class="P-Article-Preview-Block">
						<div class="P-Article-Preview-Block-Title">Abstract</div>
						<div class="P-Article-Preview-Block-Content">
							<xsl:attribute name="field_id" >18</xsl:attribute>
							<!--CALL MARK CONTENT EDITABLE TEMPLATE -->
							<xsl:call-template name="markContentEditableField">
								<xsl:with-param name="pObjectId" select="./@object_id"></xsl:with-param>
								<xsl:with-param name="pFieldId">18</xsl:with-param>
							</xsl:call-template>
							<xsl:apply-templates select="./fields/*[@id=18]" mode="formatting"/>
						</div>
					</div>
				</xsl:if>
				<xsl:if test="./fields/*[@id='19']/value != ''">
					<div class="P-Article-Preview-Block">
						<div class="P-Article-Preview-Block-Title">Keywords</div>
						<div class="P-Article-Preview-Block-Content">
							<xsl:attribute name="field_id">19</xsl:attribute>
							<!--CALL MARK CONTENT EDITABLE TEMPLATE -->
							<xsl:call-template name="markContentEditableField">
								<xsl:with-param name="pObjectId" select="./@object_id"></xsl:with-param>
								<xsl:with-param name="pFieldId">19</xsl:with-param>
							</xsl:call-template>
							<xsl:apply-templates select="./fields/*[@id=19]" mode="formatting"/>
						</div>
					</div>
				</xsl:if>
			</div>
	</xsl:template>

	<!-- Default-ен празен темплейт.
		Секциите които искаме да мачнем ще ги специфицираме ръчно
	 -->
	<xsl:template match="*" mode="bodySections"></xsl:template>

	<!-- Introduction -->
	<xsl:template match="*[@object_id='166']" mode="bodySections">
		<xsl:if test="./fields/*[@id='20']/value != ''">
			<xsl:variable name="lSecTitle" select="./@display_name"></xsl:variable>
			<div class="P-Article-Preview-Block">
				<xsl:attribute name="instance_id"><xsl:value-of select="./@instance_id" /></xsl:attribute>
				<!--anchor-->
				<span class="anchor" id="introduction"></span>
				<div class="P-Article-Preview-Block-Title"><xsl:value-of select="$lSecTitle"></xsl:value-of></div>
				<div class="P-Article-Preview-Block-Content">
					<xsl:attribute name="field_id">20</xsl:attribute>
					<!--CALL MARK CONTENT EDITABLE TEMPLATE -->
					<xsl:call-template name="markContentEditableField">
						<xsl:with-param name="pObjectId" select="./@object_id"></xsl:with-param>
						<xsl:with-param name="pFieldId">20</xsl:with-param>
					</xsl:call-template>
					<xsl:apply-templates select="./fields/*[@id='20']" mode="formatting"/>
				</div>
				<xsl:apply-templates mode="bodySubsection"/>
			</div>
		</xsl:if>
	</xsl:template>


	<!-- Discussions -->
	<xsl:template match="*[@object_id='171']" mode="bodySections">
		<xsl:if test="./fields/*[@id='224']/value != ''">
			<xsl:variable name="lSecTitle" select="./@display_name"></xsl:variable>
			<div class="P-Article-Preview-Block">
				<xsl:attribute name="instance_id"><xsl:value-of select="./@instance_id" /></xsl:attribute>
				<!--anchor-->
				<span class="anchor" id="discussions"></span>
				<div class="P-Article-Preview-Block-Title"><xsl:value-of select="$lSecTitle"></xsl:value-of></div>
				<div class="P-Article-Preview-Block-Content">
					<xsl:attribute name="field_id">224</xsl:attribute>
					<!--CALL MARK CONTENT EDITABLE TEMPLATE -->
					<xsl:call-template name="markContentEditableField">
						<xsl:with-param name="pObjectId" select="./@object_id"></xsl:with-param>
						<xsl:with-param name="pFieldId">224</xsl:with-param>
					</xsl:call-template>
					<xsl:apply-templates select="./fields/*[@id='224']" mode="formatting"/>
				</div>
				<xsl:apply-templates mode="bodySubsection"/>
			</div>
		</xsl:if>
	</xsl:template>

	<!-- Material and Methods -->
	<xsl:template match="*[@object_id='168']" mode="bodySections">
		<xsl:if test="./fields/*[@id='22']/value != ''">
			<xsl:variable name="lSecTitle" select="./@display_name"></xsl:variable>
			<div class="P-Article-Preview-Block">
				<xsl:attribute name="instance_id"><xsl:value-of select="./@instance_id" /></xsl:attribute>
				<!--anchor-->
				<span class="anchor" id="material_and_methods"></span>
				<div class="P-Article-Preview-Block-Title"><xsl:value-of select="$lSecTitle"></xsl:value-of></div>
				<div class="P-Article-Preview-Block-Content">
					<xsl:attribute name="field_id">22</xsl:attribute>
					<!--CALL MARK CONTENT EDITABLE TEMPLATE -->
					<xsl:call-template name="markContentEditableField">
						<xsl:with-param name="pObjectId" select="./@object_id"></xsl:with-param>
						<xsl:with-param name="pFieldId">22</xsl:with-param>
					</xsl:call-template>
					<xsl:apply-templates select="./fields/*[@id='22']" mode="formatting"/>
				</div>
				<xsl:apply-templates mode="bodySubsection"/>
			</div>
		</xsl:if>
	</xsl:template>

	<!-- Data resources -->
	<xsl:template match="*[@object_id='169']" mode="bodySections">
		<xsl:if test="./fields/*[@id='21']/value != ''">
			<xsl:variable name="lSecTitle" select="./@display_name"></xsl:variable>
			<div class="P-Article-Preview-Block">
				<xsl:attribute name="instance_id"><xsl:value-of select="./@instance_id" /></xsl:attribute>
				<!--anchor-->
				<span class="anchor" id="data_resources"></span>
				<div class="P-Article-Preview-Block-Title"><xsl:value-of select="$lSecTitle"></xsl:value-of></div>
				<div class="P-Article-Preview-Block-Content">
					<xsl:attribute name="field_id">21</xsl:attribute>
					<!--CALL MARK CONTENT EDITABLE TEMPLATE -->
					<xsl:call-template name="markContentEditableField">
						<xsl:with-param name="pObjectId" select="./@object_id"></xsl:with-param>
						<xsl:with-param name="pFieldId">21</xsl:with-param>
					</xsl:call-template>
					<xsl:apply-templates select="./fields/*[@id='21']" mode="formatting"/>
				</div>
				<xsl:apply-templates mode="bodySubsection"/>
			</div>
		</xsl:if>
	</xsl:template>

	<!-- Results -->
	<xsl:template match="*[@object_id='170']" mode="bodySections">
		<xsl:if test="./fields/*[@id='23']/value != ''">
			<xsl:variable name="lSecTitle" select="./@display_name"></xsl:variable>
			<div class="P-Article-Preview-Block">
				<xsl:attribute name="instance_id"><xsl:value-of select="./@instance_id" /></xsl:attribute>
				<!--anchor-->
				<span class="anchor" id="results"></span>
				<div class="P-Article-Preview-Block-Title"><xsl:value-of select="$lSecTitle"></xsl:value-of></div>
				<div class="P-Article-Preview-Block-Content">
					<xsl:attribute name="field_id">23</xsl:attribute>
					<!--CALL MARK CONTENT EDITABLE TEMPLATE -->
					<xsl:call-template name="markContentEditableField">
						<xsl:with-param name="pObjectId" select="./@object_id"></xsl:with-param>
						<xsl:with-param name="pFieldId">23</xsl:with-param>
					</xsl:call-template>
					<xsl:apply-templates select="./fields/*[@id='23']" mode="formatting"/>
				</div>
				<xsl:apply-templates mode="bodySubsection"/>
			</div>
		</xsl:if>
	</xsl:template>

	<!-- Подсекции - старт -->
	<!-- Default-ен празен темплейт.
		Субсекциите които искаме да мачнем ще ги специфицираме ръчно
	 -->
	<xsl:template match="*" mode="bodySubsection">
		<xsl:variable name="lSecTitle"><xsl:apply-templates select="./fields/*[@id='211']" mode="formatting"/></xsl:variable>
		<div class="P-Article-Preview-Block-Content">
			<xsl:attribute name="instance_id"><xsl:value-of select="./@instance_id" /></xsl:attribute>
			<span class="P-Article-Preview-Block-Subsection-Title">
				<xsl:attribute name="field_id">211</xsl:attribute>
				<!--CALL MARK CONTENT EDITABLE TEMPLATE -->
				<xsl:call-template name="markContentEditableField">
					<xsl:with-param name="pObjectId" select="./@object_id"></xsl:with-param>
					<xsl:with-param name="pFieldId">211</xsl:with-param>
				</xsl:call-template>
				<xsl:value-of select="$lSecTitle"></xsl:value-of>
			</span>
			<xsl:text> </xsl:text>
			<span>
				<xsl:attribute name="field_id">212</xsl:attribute>
				<!--CALL MARK CONTENT EDITABLE TEMPLATE -->
				<xsl:call-template name="markContentEditableField">
					<xsl:with-param name="pObjectId" select="./@object_id"></xsl:with-param>
					<xsl:with-param name="pFieldId">212</xsl:with-param>
				</xsl:call-template>
				<xsl:apply-templates select="./fields/*[@id='212']" mode="formatting"/>
			</span>
		</div>
	</xsl:template>

	<!-- При субсекциите показваме title и content
	 -->
	<xsl:template match="section" mode="bodySubsection">
		<xsl:variable name="lSecTitle"><xsl:apply-templates select="./fields/*[@id='211']" mode="formatting"/></xsl:variable>
		<div class="P-Article-Preview-Block-Content">
			<xsl:attribute name="instance_id"><xsl:value-of select="./@instance_id" /></xsl:attribute>
			<span class="P-Article-Preview-Block-Subsection-Title">
				<xsl:attribute name="field_id">211</xsl:attribute>
				<!--CALL MARK CONTENT EDITABLE TEMPLATE -->
				<xsl:call-template name="markContentEditableField">
					<xsl:with-param name="pObjectId" select="./@object_id"></xsl:with-param>
					<xsl:with-param name="pFieldId">211</xsl:with-param>
				</xsl:call-template>
				<xsl:value-of select="$lSecTitle"></xsl:value-of>
			</span>
			<xsl:text> </xsl:text>
			<span>
				<!--CALL MARK CONTENT EDITABLE TEMPLATE -->
				<xsl:call-template name="markContentEditableField">
					<xsl:with-param name="pObjectId" select="./@object_id"></xsl:with-param>
					<xsl:with-param name="pFieldId">212</xsl:with-param>
				</xsl:call-template>
				<xsl:attribute name="field_id">212</xsl:attribute>
				<xsl:apply-templates select="./fields/*[@id='212']" mode="formatting"/>
			</span>
		</div>
	</xsl:template>
	<!-- Подсекции - край -->

	<!-- Identification keys -->
	<xsl:template match="*[@object_id='24']" mode="bodySections">
		<xsl:variable name="lSecTitle">Identification keys</xsl:variable>
		<div class="P-Article-Preview-Block">
			<xsl:attribute name="instance_id"><xsl:value-of select="./@instance_id" /></xsl:attribute>
			<!--anchor-->
			<span class="anchor" id="identification_keys"></span>
			<div class="P-Article-Preview-Block-Title"><xsl:value-of select="$lSecTitle"></xsl:value-of></div>
			<div class="P-Article-Preview-Block-Content">
				<xsl:apply-templates select="//*[@object_id='23']" mode="singleIdentificationKey"/>
			</div>
		</div>
	</xsl:template>

	<!-- Single identification key -->
	<xsl:template match="*" mode="singleIdentificationKey">
		<table cellspacing="0" cellpadding="0" border="0" width="100%"
			style="border-collapse:collapse;" identification_key_table="1">
			<xsl:attribute name="instance_id"><xsl:value-of select="./@instance_id" /></xsl:attribute>
			<tbody>
				<tr>
					<td align="center" colspan="3" class="P-Article-Preview-Table-Header">
						<div class="P-Article-Preview-Block-Subsection-Title">
							<xsl:attribute name="field_id">31</xsl:attribute>
							<xsl:apply-templates select="./fields/*[@id='31']/value" mode="formatting"/>
						</div>
						<div class="P-Article-Ident-KeyNotes">
							<xsl:attribute name="field_id">32</xsl:attribute>
							<xsl:apply-templates select="./fields/*[@id='32']" mode="formatting"/>
						</div>
					</td>
				</tr>
				<xsl:for-each select="./*[@object_id='22']">
					<xsl:call-template name="identificationKeyCouplet">
						<xsl:with-param name="pNode" select="."></xsl:with-param>
						<xsl:with-param name="pNum" select="position()"></xsl:with-param>
					</xsl:call-template>
				</xsl:for-each>
			</tbody>
		</table>
	</xsl:template>

	<xsl:template match="*" mode="identificationKeyCouplet" name="identificationKeyCouplet">
		<xsl:param name="pNode"></xsl:param>
		<xsl:param name="pNum"></xsl:param>
		<tr>
			<xsl:attribute name="instance_id"><xsl:value-of select="./@instance_id" /></xsl:attribute>
			<td class="P-Article-Preview-Table-Row" align="center"><xsl:value-of select="$pNum"></xsl:value-of></td>
			<td class="P-Article-Preview-Table-Row">
				<xsl:attribute name="field_id">34</xsl:attribute>
				<xsl:apply-templates select="$pNode/fields/*[@id='34']" mode="formatting"/>
			</td>
			<td class="P-Article-Preview-Table-Row">
				<span>
					<xsl:attribute name="field_id">35</xsl:attribute>
					<xsl:apply-templates select="$pNode/fields/*[@id='35']" mode="formatting"/>
				</span>
				<span>
					<xsl:attribute name="field_id">36</xsl:attribute>
					<xsl:apply-templates select="$pNode/fields/*[@id='36']" mode="formatting"/>
				</span>
			</td>
		</tr>
		<tr>
			<xsl:attribute name="instance_id"><xsl:value-of select="./@instance_id" /></xsl:attribute>
			<td class="P-Article-Preview-Table-Row" align="center">–</td>
			<td class="P-Article-Preview-Table-Row">
				<xsl:attribute name="field_id">37</xsl:attribute>
				<xsl:apply-templates select="$pNode/fields/*[@id='37']" mode="formatting"/>
			</td>
			<td class="P-Article-Preview-Table-Row">
				<span>
					<xsl:attribute name="field_id">38</xsl:attribute>
					<xsl:apply-templates select="$pNode/fields/*[@id='38']" mode="formatting"/>
				</span>
				<span>
					<xsl:attribute name="field_id">39</xsl:attribute>
					<xsl:apply-templates select="$pNode/fields/*[@id='39']" mode="formatting"/>
				</span>
			</td>
		</tr>
	</xsl:template>

	<!-- singleIdentificationKeyCouplet -->
	<xsl:template match="*" mode="singleIdentificationKeyCouplet" name="singleIdentificationKeyCouplet">
		<xsl:variable name="lInstanceId" select="./@instance_id"></xsl:variable>
		<xsl:variable name="lParentInstanceId" select="./ancestor::*[@object_id='23']/@instance_id"></xsl:variable>

		<!-- Position of KeyCouplet -->
		<xsl:variable name="lPosition">
		 	<xsl:for-each select="//*[@object_id='23' and @instance_id = $lParentInstanceId]/*[@object_id='22']">
				<xsl:choose>
					<xsl:when test="./@instance_id = $lInstanceId">
						<xsl:value-of select="position()"/>
					</xsl:when>
				</xsl:choose>
			</xsl:for-each>
		</xsl:variable>
		<tr>
			<xsl:attribute name="instance_id"><xsl:value-of select="./@instance_id" /></xsl:attribute>
			<td class="P-Article-Preview-Table-Row" align="center"><xsl:value-of select="$lPosition" /></td>
			<td class="P-Article-Preview-Table-Row">
				<xsl:attribute name="field_id">34</xsl:attribute>
				<xsl:apply-templates select="./fields/*[@id='34']" mode="formatting"/>
			</td>
			<td class="P-Article-Preview-Table-Row">
				<span>
					<xsl:attribute name="field_id">35</xsl:attribute>
					<xsl:apply-templates select="./fields/*[@id='35']" mode="formatting"/>
				</span>
				<span>
					<xsl:attribute name="field_id">36</xsl:attribute>
					<xsl:apply-templates select="./fields/*[@id='36']" mode="formatting"/>
				</span>
			</td>
		</tr>
		<tr>
			<xsl:attribute name="instance_id"><xsl:value-of select="./@instance_id" /></xsl:attribute>
			<td class="P-Article-Preview-Table-Row" align="center">–</td>
			<td class="P-Article-Preview-Table-Row">
				<xsl:attribute name="field_id">37</xsl:attribute>
				<xsl:apply-templates select="./fields/*[@id='37']" mode="formatting"/>
			</td>
			<td class="P-Article-Preview-Table-Row">
				<span>
					<xsl:attribute name="field_id">38</xsl:attribute>
					<xsl:apply-templates select="./fields/*[@id='38']" mode="formatting"/>
				</span>
				<span>
					<xsl:attribute name="field_id">39</xsl:attribute>
					<xsl:apply-templates select="./fields/*[@id='39']" mode="formatting"/>
				</span>
			</td>
		</tr>
	</xsl:template>

	<!-- Taxon Treatments start -->
	<!-- Systematics -->
	<xsl:template match="*[@object_id='54']" mode="bodySections">
		<xsl:variable name="lSecTitle">Systematics</xsl:variable>
		<div class="P-Article-Preview-Block">
			<xsl:attribute name="instance_id"><xsl:value-of select="./@instance_id" /></xsl:attribute>
			<div class="P-Article-Preview-Block-Title"><xsl:value-of select="$lSecTitle"></xsl:value-of></div>
			<div class="P-Article-Preview-Block-Content">
				<xsl:attribute name="field_id">40</xsl:attribute>
				<xsl:apply-templates select="./fields/*[@id='40']" mode="formatting"/>
			</div>
			<xsl:apply-templates mode="bodySubsection"/>
		</div>

		<xsl:apply-templates select="./*[@object_id='41']" mode="taxonTreatment"/>
	</xsl:template>

	<!-- Taxon treatment -->
	<xsl:template match="*" mode="taxonTreatment">
		<xsl:variable name="lTreatmentNode" select="."></xsl:variable>

		<xsl:variable name="lSecTitle">
				<xsl:apply-templates select=".//*[@object_id='70' or @object_id='43']" mode="taxonTreatmentName"/>
				<xsl:for-each select=".//*[@object_id='42']/fields/*">
					<xsl:apply-templates select="." mode="taxonTreatmentAuthor"/>
					<xsl:if test="position()!=last()"><xsl:text>, </xsl:text></xsl:if>
					<xsl:if test="position()=last()"><xsl:text>, sp. n.</xsl:text></xsl:if>
				</xsl:for-each>
				<xsl:if test="count(.//*[@object_id='68'])">
					<xsl:apply-templates select=".//*[@object_id='63' or @object_id='69']" mode="TTNOriginalCitation"/>
				</xsl:if>
		</xsl:variable>

		<div class="P-Article-Preview-Block">
			<xsl:attribute name="instance_id"><xsl:value-of select="./@instance_id" /></xsl:attribute>
			<div class="P-Article-Preview-Block-Subsection-Title"><xsl:copy-of select="$lSecTitle"/></div>
			<div class="P-Article-Preview-Block-Content">
				<xsl:attribute name="field_id">212</xsl:attribute>
				<xsl:apply-templates select="./fields/*[@id='212']" mode="formatting"/>
			</div>
			<xsl:if test="count(.//*[@object_id='39'])">
				<ul>
					<xsl:apply-templates select=".//*[@object_id='39']" mode="TTExternalLinks"/>
				</ul>
			</xsl:if>


			<xsl:if test="count(.//*[@object_id='68'])">
				<ul>
					<xsl:apply-templates select=".//*[@object_id='63' or @object_id='69']" mode="TTNOriginalCitation"/>
				</ul>
			</xsl:if>

			<xsl:if test="count(.//*[@object_id='37']) &gt; 0">
				<div class="P-Article-Preview-Block-Content">
					<span class="P-Article-Preview-Block-Subsection-Title P-Indent">Materials.</span><xsl:text> </xsl:text>
					<xsl:for-each select="//*[@object_id='37'][generate-id()=generate-id(key('materialType', ./fields/*[@id='209']/value/@value_id))]">
						<xsl:variable name="lMaterialTypeId" select="./fields/*[@id='209']/value/@value_id"></xsl:variable>
						<xsl:variable name="lMaterialTypeName" select="./fields/*[@id='209']/value"></xsl:variable>
						<!-- Ако имаме материали от този тип -->
						<xsl:if test="count($lTreatmentNode//*[@object_id='37'][fields/*[@id='209']/value[@value_id=$lMaterialTypeId]]) &gt; 0">
							<span class="P-Article-Preview-Block-Content">
								<span class="P-Article-Preview-Block-Subsection-Title">
									<i>
										<xsl:value-of select="$lMaterialTypeName"></xsl:value-of>
									</i>
								</span>
								<xsl:text>: </xsl:text>
								<xsl:apply-templates select="$lTreatmentNode//*[@object_id='37'][fields/*[@id='209']/value[@value_id=$lMaterialTypeId]]" mode="treatmentMaterial"></xsl:apply-templates>
								<xsl:text>. </xsl:text>
							</span>
						</xsl:if>
					</xsl:for-each>
				</div>
			</xsl:if>

			<xsl:apply-templates select=".//*[@object_id='51']/*[@object_id &gt; 0]/*[@object_id &gt; 0]" mode="taxonTreatmentSections"/>
			<xsl:apply-templates select=".//*[@object_id='74']/*[@object_id &gt; 0]" mode="taxonTreatmentSections"/>
		</div>
	</xsl:template>

	<!--  Taxon treatment external link -->
	<xsl:template match="*" mode="TTExternalLinks">
		<xsl:variable name="lTreatmentURLType" select="./fields/*[@id='52']/value"></xsl:variable>
		<!--  Тип на линка -->
		<xsl:variable name="lTreatmentURLPrefix">
			<xsl:choose>
				<xsl:when test="$lTreatmentURLType='ZooBank'">
					<xsl:text>http://zoobank.org/?lsid=</xsl:text>
				</xsl:when>
				<xsl:when test="$lTreatmentURLType='MorphBank'">
					<xsl:text>http://www.morphbank.net/Browse/ByImage/?tsn=</xsl:text>
				</xsl:when>
				<xsl:when test="$lTreatmentURLType='GenBank'">
					<xsl:text>http://www.ncbi.nlm.nih.gov/nuccore/</xsl:text>
				</xsl:when>
				<xsl:when test="$lTreatmentURLType='IPNI'">
					<xsl:text>http://ipni.org/</xsl:text>
				</xsl:when>
				<xsl:otherwise>
					<xsl:text>URL</xsl:text>
				</xsl:otherwise>
			</xsl:choose>
		</xsl:variable>
		<li>
			<xsl:attribute name="instance_id"><xsl:value-of select="./@instance_id" /></xsl:attribute>
			<xsl:attribute name="field_id">53</xsl:attribute>
			<xsl:apply-templates select="./fields/*[@id='53']" mode="formatting_treatment_link">
				<xsl:with-param name="lTreatmentUrl" select="$lTreatmentURLPrefix"/>
			</xsl:apply-templates>
		</li>
	</xsl:template>



	<!-- Taxon family name
	 -->
	<xsl:template match="*[@object_id='70']" mode="taxonTreatmentName">
		<span>
			<xsl:attribute name="instance_id"><xsl:value-of select="./@instance_id" /></xsl:attribute>
			<xsl:attribute name="field_id">241</xsl:attribute>
			<xsl:apply-templates select="./fields/*[@id='241']" mode="formatting"/>
		</span>
	</xsl:template>

	<!-- Taxon species name
	 -->
	<xsl:template match="*[@object_id='43']" mode="taxonTreatmentName">
		<span>
			<xsl:attribute name="instance_id"><xsl:value-of select="./@instance_id" /></xsl:attribute>
			<xsl:attribute name="class">taxonTreatmentName</xsl:attribute>
			<span>
				<xsl:attribute name="field_id">48</xsl:attribute>
				<i>
					<xsl:apply-templates select="./fields/*[@id='48']" mode="formatting"/>
				</i>
			</span>
			<xsl:text> </xsl:text>
			<span>
				<xsl:attribute name="field_id">49</xsl:attribute>
				<i>
					<xsl:apply-templates select="./fields/*[@id='49']" mode="formatting"/>
				</i>
			</span>
		</span>
	</xsl:template>

	<!-- Taxon treatment author
	 -->
	<xsl:template match="*" mode="taxonTreatmentAuthor">
		<span>
			<xsl:attribute name="instance_id"><xsl:value-of select="./@instance_id" /></xsl:attribute>
				<xsl:apply-templates select="." mode="formatting_nospace"/>
		</span>
	</xsl:template>

	<!-- Treatment material -->
	<xsl:template match="*" mode="treatmentMaterial">
			<span>
				<xsl:attribute name="instance_id"><xsl:value-of select="./@instance_id" /></xsl:attribute>
				<xsl:for-each select=".//*[@object_id &gt; 24 and @object_id &lt; 32]/fields/*[value != '']">
					<xsl:apply-templates select="." mode="treatmentMaterialField"></xsl:apply-templates>
					<xsl:if test="position() != last()"><xsl:text>, </xsl:text></xsl:if>
				</xsl:for-each>
			</span>
	</xsl:template>

	<!-- Treatment material field -->
	<xsl:template match="*" mode="treatmentMaterialField">
			<span>
				<xsl:attribute name="field_id"><xsl:value-of select="./@field_id" /></xsl:attribute>
				<xsl:apply-templates select="./value" mode="formatting"/>
			</span>
	</xsl:template>


	<!-- TaxonTreatmentSection -->
	<xsl:template match="*" mode="taxonTreatmentSections">
		<xsl:variable name="lSecTitle">
			<xsl:choose>
				<xsl:when test="count(./fields/*[@id='211']) &gt; 0">
					<xsl:apply-templates select="./fields/*[@id='211']/value" mode="formatting"/>
				</xsl:when>
				<xsl:otherwise>
					<xsl:apply-templates select="./fields/*[@id='212']/@field_name" mode="formatting"/>
				</xsl:otherwise>
			</xsl:choose>
		</xsl:variable>
		<div class="P-Article-Preview-Block-Content">
			<xsl:attribute name="instance_id"><xsl:value-of select="./@instance_id" /></xsl:attribute>
			<span class="P-Article-Preview-Block-Subsection-Title P-Indent">
				<xsl:if test="count(./fields/*[@id='211']) &gt; 0">
					<xsl:attribute name="field_id">211</xsl:attribute>
				</xsl:if>
				<xsl:copy-of select="$lSecTitle"/>
			</span>
			<xsl:text>. </xsl:text>
			<span>
				<xsl:attribute name="field_id">212</xsl:attribute>
				<xsl:attribute name="class">P-Inline</xsl:attribute>
				<xsl:apply-templates select="./fields/*[@id='212']/value" mode="formatting"/>
			</span>

			<xsl:apply-templates select="./*[@object_id='50']" mode="taxonTreatmentSubSections"/>
		</div>
	</xsl:template>

	<!-- TaxonTreatmentSubSection -->
	<xsl:template match="*" mode="taxonTreatmentSubSections">
		<xsl:variable name="lSecTitle">
			<xsl:apply-templates select="./fields/*[@id='211']/value" mode="formatting"/>
		</xsl:variable>

		<div class="P-Article-Preview-Block-Content">
			<xsl:attribute name="instance_id"><xsl:value-of select="./@instance_id" /></xsl:attribute>
			<span class="P-Article-Preview-Block-Subsection-Title P-Indent">
				<xsl:attribute name="field_id">211</xsl:attribute>
				<i><xsl:copy-of select="$lSecTitle"/></i>
			</span>
			<xsl:text>: </xsl:text>
			<span>
				<xsl:attribute name="field_id">212</xsl:attribute>
				<xsl:attribute name="class">P-Inline</xsl:attribute>
				<xsl:apply-templates select="./fields/*[@id='212']/value" mode="formatting"/>
			</span>
		</div>
	</xsl:template>
	<!-- Taxon Treatments end -->


	<!-- Default-ен празен темплейт.
		Обработваме само обектите, които ни трябват
	 -->
	<xsl:template match="*" mode="articleBack"></xsl:template>

	<!-- Аcknowledgements
	 -->
	<xsl:template match="*[@object_id='57']" mode="articleBack">
		<xsl:if test="./fields/*[@id='223']/value != ''">
			<xsl:variable name="lSecTitle">Аcknowledgements</xsl:variable>
			<div class="P-Article-Preview-Block">
				<xsl:attribute name="instance_id"><xsl:value-of select="./@instance_id" /></xsl:attribute>
				<!--anchor-->
				<span class="anchor" id="acknowledgements"></span>
				<div class="P-Article-Preview-Block-Title"><xsl:value-of select="$lSecTitle"></xsl:value-of></div>
				<div class="P-Article-Preview-Block-Content">
					<xsl:attribute name="field_id">223</xsl:attribute>
					<xsl:call-template name="markContentEditableField">
						<xsl:with-param name="pObjectId" select="./@object_id"></xsl:with-param>
						<xsl:with-param name="pFieldId">223</xsl:with-param>
					</xsl:call-template>
					<xsl:apply-templates select="./fields/*[@id='223']" mode="formatting"/>
				</div>
				<xsl:apply-templates mode="bodySubsection"/>
			</div>
		</xsl:if>
	</xsl:template>

	<!-- Supplementary files
	 -->
	<xsl:template match="*[@object_id='56']" mode="articleBack">
		<xsl:if test="count(./*[@object_id='55']) &gt; 0">
			<xsl:variable name="lSecTitle">Supplementary material</xsl:variable>
			<div class="P-Article-Preview-Block">
				<xsl:attribute name="instance_id"><xsl:value-of select="./@instance_id" /></xsl:attribute>
				<!--anchor-->
				<span class="anchor" id="supplementary_files"></span>
				<div class="P-Article-Preview-Block-Title"><xsl:value-of select="$lSecTitle"></xsl:value-of></div>

				<xsl:for-each select="./*[@object_id='55']/fields/*[@id != '']">
					<xsl:apply-templates select="." mode="singleSupplementaryMaterial"/>
				</xsl:for-each>

			</div>
		</xsl:if>
	</xsl:template>

	<!-- Single supplementary material
	 -->
	<xsl:template match="*" mode="singleSupplementaryMaterial">
		<div class="P-Article-Preview-Block-Content">
			<xsl:choose>
				<xsl:when test="./@id = 214">
					<div class="P-Article-Preview-Block-Subsection-Title">
						<xsl:attribute name="field_id"><xsl:value-of select="./@id" /></xsl:attribute>
						<xsl:apply-templates select="./value" mode="formatting"/>
					</div>
				</xsl:when>
				<xsl:otherwise>
					<xsl:choose>
						<xsl:when test="./@id = 222">
							<xsl:variable name="lFileName" select="php:function('getFileNameById', string(./value))"></xsl:variable>
							<xsl:variable name="lUploadedFileName" select="php:function('getUploadedFileNameById', string(./value))"></xsl:variable>
							<span>
								<xsl:attribute name="field_id"><xsl:value-of select="./@id" /></xsl:attribute>
								<xsl:attribute name="class">P-Article-Preview-Block-Subsection-Title P-Indent</xsl:attribute>
								<xsl:apply-templates select="./value" mode="formatting_uploaded_file">
									<xsl:with-param name="lFileName" select="$lFileName"></xsl:with-param>
								</xsl:apply-templates>
							</span>
						</xsl:when>
						<xsl:otherwise>
							<span>
								<xsl:attribute name="field_id"><xsl:value-of select="./@id" /></xsl:attribute>
								<xsl:attribute name="class">P-Article-Preview-Block-Subsection-Title P-Indent</xsl:attribute>
								<xsl:apply-templates select="./@field_name" mode="formatting"/>
							</span>
							<xsl:text>: </xsl:text>
							<span>
								<xsl:attribute name="class">P-Inline</xsl:attribute>
								<xsl:apply-templates select="./value" mode="formatting"/>
							</span>
						</xsl:otherwise>
					</xsl:choose>

				</xsl:otherwise>
			</xsl:choose>
		</div>
	</xsl:template>

	<!-- Single supplementary material
	 -->
	<xsl:template match="*[@object_id='55']" mode="articleBack">

		<li>
			<xsl:attribute name="instance_id"><xsl:value-of select="./@instance_id" /></xsl:attribute>
			<span>
				<xsl:attribute name="field_id">214</xsl:attribute>
				<xsl:apply-templates select="./fields/*[@id='214']" mode="formatting"/>
			</span>
			<xsl:text> </xsl:text>
			<xsl:variable name="lUrls" select="php:function('getAllUrlsFromText', string(./fields/*[@id='222']))"></xsl:variable>
			<xsl:for-each select="$lUrls/url">
				<xsl:variable name="lLinkContent" select="."></xsl:variable>
				<a>
					<xsl:attribute name="href"><xsl:value-of select="$lLinkContent" disable-output-escaping="no"/></xsl:attribute>
					<xsl:value-of select="$lLinkContent" disable-output-escaping="no"/>
				</a>
				<xsl:if test="position()!=last()"><xsl:text>, </xsl:text></xsl:if>
			</xsl:for-each>
		</li>
	</xsl:template>

	<!-- Single figure or plate -->
	<xsl:template match="*" mode="figures">
		<xsl:variable name="lFigId" select="php:function('getFigureId', string(./@id))"></xsl:variable>
		<xsl:choose>
			<xsl:when test="@is_plate"> <!-- plate -->
				<xsl:variable name="lPlateType" select="./@type"></xsl:variable>
					<div class="P-Article-Preview-Base-Info-Block">
						<xsl:attribute name="plate_id">
							<xsl:value-of select="./@id"/>
						</xsl:attribute>
						<xsl:attribute name="contenteditable">false</xsl:attribute>
						<xsl:attribute name="figure_position">
							<xsl:value-of select="$lFigId"/>
						</xsl:attribute>
						<div class="P-Article-Preview-Picture-Holder">
							<xsl:choose>
								<xsl:when test="$lPlateType = 2">
									<xsl:for-each select="./url">
										<xsl:apply-templates select="." mode="plate_photo">
											<xsl:with-param name="picUrl" select="."></xsl:with-param>
											<xsl:with-param name="picId" select="./@id"></xsl:with-param>
											<xsl:with-param name="picDesc" select="following::photo_description"></xsl:with-param>
										</xsl:apply-templates>
									</xsl:for-each>
								</xsl:when>
								<xsl:when test="$lPlateType = 4">
									<xsl:for-each select="./url">
										<xsl:apply-templates select="." mode="plate_photo">
											<xsl:with-param name="picUrl" select="."></xsl:with-param>
											<xsl:with-param name="picId" select="./@id"></xsl:with-param>
											<xsl:with-param name="picDesc" select="following::photo_description"></xsl:with-param>
										</xsl:apply-templates>
										<xsl:if test="position() mod 2 = 0 and position() != last()">
											<xsl:text disable-output-escaping="yes"><![CDATA[<div class="P-Clear"></div>]]></xsl:text>
										</xsl:if>
									</xsl:for-each>
								</xsl:when>
								<xsl:otherwise>
									<div>
										<xsl:attribute name="class"><xsl:text>P-Article-Preview-Picture-Row</xsl:text></xsl:attribute>
										<xsl:for-each select="./url">
											<xsl:apply-templates select="." mode="plate_photo">
												<xsl:with-param name="picUrl" select="."></xsl:with-param>
												<xsl:with-param name="picId" select="./@id"></xsl:with-param>
												<xsl:with-param name="picDesc" select="following::photo_description"></xsl:with-param>
											</xsl:apply-templates>
											<xsl:if test="position() mod 2 = 0 and position() != last()">
												<xsl:text disable-output-escaping="yes"><![CDATA[</div><div class="P-Article-Preview-Picture-Row">]]></xsl:text>
											</xsl:if>
										</xsl:for-each>
									</div>
								</xsl:otherwise>
							</xsl:choose>
						</div>
						<div class="P-Article-Preview-Picture-Name"><xsl:text>Figure </xsl:text><xsl:value-of select="$lFigId" /></div>
						<div class="P-Article-Preview-Picture-Plus-Icon"></div>
						<div class="P-Clear"></div>
						<div class="P-Article-Preview-Picture-Desc">
							<xsl:apply-templates select="./caption" mode="formatting"/>
						</div>
					</div>
			</xsl:when>
			<xsl:when test="@is_video"> <!-- video -->
				<div class="P-Article-Preview-Base-Info-Block">
					<xsl:attribute name="figure_id">
						<xsl:value-of select="./@id"/>
					</xsl:attribute>
					<xsl:attribute name="contenteditable">false</xsl:attribute>
					<xsl:attribute name="figure_position">
						<xsl:value-of select="$lFigId"/>
					</xsl:attribute>
					<div class="P-Article-Preview-Picture-Holder">
						<iframe width="560" height="315" frameborder="0">
							<xsl:attribute name="src">
								<xsl:text>http://www.youtube.com/embed/</xsl:text>
								<xsl:value-of select="php:function('getYouTubeId', string(./url))"/>
							</xsl:attribute>
						</iframe>
					</div>
					<div class="P-Article-Preview-Picture-Desc">
						<div class="P-Article-Preview-Picture-Name">
							<xsl:text>Figure </xsl:text><xsl:value-of select="$lFigId" /><xsl:text>. </xsl:text>
						</div>
						<xsl:apply-templates select="./caption" mode="formatting"/>
					</div>
					<div class="P-Clear"></div>
				</div>
			</xsl:when>
			<xsl:otherwise> <!-- figure -->
				<div class="P-Article-Preview-Base-Info-Block">
					<xsl:attribute name="figure_id">
						<xsl:value-of select="./@id"/>
					</xsl:attribute>
					<xsl:attribute name="contenteditable">false</xsl:attribute>
					<xsl:attribute name="figure_position">
						<xsl:value-of select="$lFigId"/>
					</xsl:attribute>
					<div class="P-Article-Preview-Picture-Holder">
						<a target="_blank">
							<xsl:attribute name="href">
								<xsl:text>/display_zoomed_figure.php?fig_id=</xsl:text>
								<xsl:value-of select="./@id"/>
							</xsl:attribute>
							<img>
								<xsl:attribute name="src">
									<xsl:value-of select="./url"/>
								</xsl:attribute>
							</img>
						</a>
						<a target="_blank">
							<xsl:attribute name="href">
								<xsl:text>/display_zoomed_figure.php?fig_id=</xsl:text>
								<xsl:value-of select="./@id"/>
							</xsl:attribute>
							<div class="P-Article-Preview-Picture-Zoom"></div>
						</a>
					</div>
					<div class="P-Article-Preview-Picture-Name"><xsl:text>Figure </xsl:text><xsl:value-of select="$lFigId" /></div>
					<div class="P-Article-Preview-Picture-Plus-Icon"></div>
					<div class="P-Clear"></div>
					<div class="P-Article-Preview-Picture-Desc">
						<xsl:apply-templates select="./caption" mode="formatting"/>
					</div>
				</div>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>

	<!-- Single table -->
	<xsl:template match="*" mode="tables">
		<xsl:variable name="lTablePosition" select="./@position"></xsl:variable>
		<div class="P-Article-Preview-Block">
			<xsl:attribute name="table_id">
				<xsl:value-of select="./@id"/>
			</xsl:attribute>
			<xsl:attribute name="table_position">
				<xsl:value-of select="$lTablePosition"/>
			</xsl:attribute>
			<div class="P-Article-Preview-Table-Header">
				<b>
					<xsl:text>Table </xsl:text><xsl:value-of select="$lTablePosition" />.
				</b>
				<span>
					<xsl:attribute name="class">P-Inline</xsl:attribute>
					<xsl:apply-templates select="./title" mode="formatting"/>
				</span>
			</div>
			<div class="P-Clear"></div>
			<div class="P-Article-Preview-Table-Desc">
				<xsl:apply-templates select="./description" mode="table_formatting"/>
			</div>
		</div>
	</xsl:template>

	<!-- Single plate photo -->
	<xsl:template match="*" mode="plate_photo">
		<xsl:param name="picUrl"></xsl:param>
		<xsl:param name="picId"></xsl:param>
		<xsl:param name="picDesc"></xsl:param>
		<div class="singlePlatePhoto">
			<a target="_blank">
				<xsl:attribute name="href">
					<xsl:text>/display_zoomed_figure.php?fig_id=</xsl:text>
					<xsl:value-of select="$picId" />
				</xsl:attribute>
				<img>
					<xsl:attribute name="src">
						<xsl:value-of select="$picUrl"/>
					</xsl:attribute>
				</img>
			</a>
			<a target="_blank">
				<xsl:attribute name="href">
					<xsl:text>/display_zoomed_figure.php?fig_id=</xsl:text>
					<xsl:value-of select="$picId" />
				</xsl:attribute>
				<div class="P-Article-Preview-Picture-Zoom-Small"></div>
			</a>
			<div class="P-Article-Preview-Picture-Desc">
				<xsl:value-of select="$picDesc" />
			</div>
		</div>

	</xsl:template>
</xsl:stylesheet>