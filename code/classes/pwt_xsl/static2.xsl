<?xml version="1.0"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:tp="http://www.plazi.org/taxpub" xmlns:php="http://php.net/xsl" xmlns:exslt="http://exslt.org/common" exclude-result-prefixes="php tp xlink xsl exslt" >
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
	<xsl:param  name="pSiteUrl"></xsl:param>
	<xsl:param  name="pPDFPreviewMode">0</xsl:param>
	<!-- This parameter will be passed when we generate the previews for article of the future -->
	<xsl:param  name="pInArticleMode">0</xsl:param>

	<xsl:variable name="gAuthorshipEditorType">2</xsl:variable>
	<xsl:variable name="gEditorAuthorshipEditorType">1</xsl:variable>

	<xsl:template match="tn|tn-part|b|i|u|a|strong|em|sup|sub|p|ul|li|ol|insert|delete|comment-start|comment-end|reference-citation|fig-citation|tbls-citation|sup-files-citation|locality-coordinates" mode="formatting">
		<xsl:choose>
			<xsl:when test="$pInArticleMode = 0 and $pPDFPreviewMode = 0">
				<xsl:copy-of select="."/>
			</xsl:when>
			<xsl:otherwise>
				<xsl:call-template name="get_node_text_template">
				    <xsl:with-param name="pNode" select="."></xsl:with-param>
				</xsl:call-template>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>

	<xsl:template match="*" mode="formatting_output_escape">
		<xsl:value-of select="." disable-output-escaping="yes"/>
	</xsl:template>

	<xsl:template match="tn|tn-part|b|i|u|a|strong|em|sup|sub|p|ul|ol|li|comment-start|comment-end|table|tr|td|tbody|th|reference-citation|fig-citation|tbls-citation|sup-files-citation|locality-coordinates" mode="table_formatting">
		<xsl:choose>
			<xsl:when test="$pInArticleMode = 0">
				<xsl:copy-of select="."/>
			</xsl:when>
			<xsl:otherwise>
				<xsl:call-template name="get_node_text_template">
				    <xsl:with-param name="pNode" select="."></xsl:with-param>
				</xsl:call-template>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>

	<xsl:template match="tn|tn-part|b|i|u|a|strong|em|sup|sub|insert|delete|comment-start|comment-end|reference-citation|fig-citation|tbls-citation|sup-files-citation|locality-coordinates" mode="title">
		<xsl:choose>
			<xsl:when test="$pInArticleMode = 0">
				<xsl:copy-of select="."/>
			</xsl:when>
			<xsl:otherwise>
				<xsl:call-template name="get_node_text_template">
				    <xsl:with-param name="pNode" select="."></xsl:with-param>
				</xsl:call-template>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>

	<!-- Removes spaces -->
	<xsl:template match="*" mode="formatting_nospace">
		<xsl:param name="lTreatmentUrl"/>
		<xsl:apply-templates select="." mode="formatting"/>
	</xsl:template>

	<xsl:template match="*" mode="format_taxa_rank">
		<xsl:apply-templates select="." mode="formatting"/>
	</xsl:template>

	<!-- MARKING EDITABLE FIELDS TEMPLATE -->
	<xsl:template name="markContentEditableField">
		<xsl:param name="pObjectId" />
		<xsl:param name="pFieldId" />

		<xsl:if test="$pMarkContentEditableFields &gt; 0">
			<xsl:variable name="lCheck" select="php:function('checkIfObjectFieldIsEditable', string($pObjectId), string($pFieldId))" />
			<xsl:if test="$lCheck &gt; 0">
				<xsl:choose>
					<xsl:when test="$pInArticleMode = 0">
						<xsl:attribute name="contenteditable">true</xsl:attribute>
					</xsl:when>
					<xsl:otherwise>
						<xsl:attribute name="contenteditable">false</xsl:attribute>
					</xsl:otherwise>
				</xsl:choose>
			</xsl:if>
		</xsl:if>
	</xsl:template>

	<xsl:template name="markContentEditableFiguresAndTables">
			<xsl:if test="$pTrackFigureAndTableChanges &gt; 0 and $pMarkContentEditableFields &gt; 0">
				<xsl:choose>
					<xsl:when test="$pInArticleMode = 0">
						<xsl:attribute name="contenteditable">true</xsl:attribute>
					</xsl:when>
					<xsl:otherwise>
						<xsl:attribute name="contenteditable">false</xsl:attribute>
					</xsl:otherwise>
				</xsl:choose>
			</xsl:if>
	</xsl:template>

	<!-- JOURNAL INFO -->
	<xsl:template name="journalInfo">
		<xsl:param name="pDocumentNode" />

		<xsl:variable name="lJournalName" select="$pDocumentNode/journal_name" />
		<xsl:variable name="lDocumentType" select="$pDocumentNode/document_type" />
		<xsl:if test="$pPDFPreviewMode = 0">
			<table cellpadding="0" cellspacing="0" width="100%">
				<colgroup>
					<col width="90%"></col>
					<col width="10%"></col>
				</colgroup>
				<tr>
					<td>
						<div class="P-Article-Preview-Antet">
							<xsl:value-of select="$lJournalName" /> : <xsl:value-of select="$lDocumentType" />
						</div>
					</td>
					<td>
						<div class="P-Article-Preview-Antet" style="text-align:right;font-size:12px;line-height:18px;">
							<a href="javascript:void(0);" onclick="window.print()" style="background:transparent url('/i/printer.jpg') no-repeat 0px 0px; padding-left:20px;">Print</a>
						</div>
					</td>
				</tr>
			</table>
		</xsl:if>
		<xsl:if test="$pInArticleMode = 1">
			<div class="PaperType">
				<xsl:value-of select="$lDocumentType" />
			</div>
		</xsl:if>
	</xsl:template>




	<!-- ARTICLE TITLE -->
	<xsl:template match="*" mode="articleTitle">
		<div class="P-Article-Preview-Title" id="article_metadata">
			<xsl:attribute name="field_id"><xsl:value-of select="./@id" /></xsl:attribute>
			<xsl:call-template name="markContentEditableField">
					<xsl:with-param name="pObjectId" select="../../@object_id" />
					<xsl:with-param name="pFieldId">3</xsl:with-param>
			</xsl:call-template>
			<xsl:apply-templates select="." mode="formatting"/>
		</div>
	</xsl:template>

	<!-- AUTHORS -->
	<xsl:template name="authors">
		<xsl:param name="pDocumentNode" />

		<div class="P-Article-Preview-Names">
			<xsl:if test="$pInArticleMode = 1">
				<a onclick="toogleArticleInfo()">
					<img style="padding-right:6px" alt="expand article info" id="arrow">
						<xsl:attribute name="src"><xsl:value-of select="$pSiteUrl"/>/i/arrow-down-icon.png</xsl:attribute>
					</img>
				</a>
			</xsl:if>
				<xsl:for-each select="$pDocumentNode/objects/*[@object_id='14' or @object_id = '152']/*[@object_id='9' or @object_id='153']/*[@object_id='8']">
					<xsl:apply-templates select="." mode="singleAuthor" />
					<xsl:if test="position()!=last()"><xsl:text>, </xsl:text></xsl:if>
				</xsl:for-each>
			</div>
			<div class="P-Article-Preview-Addresses">
				<xsl:for-each select="$pDocumentNode/objects/*[@object_id='14' or @object_id = '152']/*[@object_id='9' or @object_id='153']/*[@object_id='8']/*[@object_id='5']">
					<xsl:apply-templates select="." mode="singleAuthorAddress" />
				</xsl:for-each>
			</div>

			<div class="P-Article-Preview-Base-Info-Block">
				<table cellspacing="0" cellpadding="0" border="0" width="100%">
					<tbody>
						<tr>
							<td>
								<div class="P-Article-Info-Block-Row">
									<xsl:text>Corresponding author: </xsl:text>
									<xsl:for-each select="$pDocumentNode/objects/*[@object_id='14' or @object_id = '152']/*[@object_id='9' or @object_id='153']/*[@object_id='8'][fields/*[@id='15']/value[@value_id='1']]">
										<xsl:apply-templates select="." mode="singleCorrespondingAuthor" />
										<xsl:if test="position()!=last()"><xsl:text>, </xsl:text></xsl:if>
									</xsl:for-each>
								</div>
								<div class="P-Article-Info-Block-Row">
									<xsl:value-of select="php:function('getSE', $pDocumentId)"/>
								</div>
								<div class="P-Article-Info-Block-Row">
									<xsl:value-of select="php:function('getDates', $pDocumentId)"/>
								</div>
								<div class="P-Article-Info-Block-Row copyright">
									<xsl:text>© </xsl:text><xsl:value-of select="php:function('getYear')"/><xsl:text> </xsl:text>
									<xsl:for-each select="$pDocumentNode/objects/*[@object_id='14' or @object_id = '152']/*[@object_id='9' or @object_id='153']/*[@object_id='8']">
										<xsl:apply-templates select="." mode="singleCorrespondingAuthorInLicense" />
										<xsl:if test="position()!=last()"><xsl:text>, </xsl:text></xsl:if>
									</xsl:for-each>
									<xsl:text>.</xsl:text> <br /><xsl:text>This is an open access article distributed under the terms of the </xsl:text>
									<a border="0" target="_blank" href="http://creativecommons.org/licenses/by/3.0/" rel="license">Creative Commons Attribution 3.0 (CC-BY)</a>
									<xsl:text> which permits unrestricted use, distribution, and reproduction in any medium, provided the original author and source are credited.</xsl:text>
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
			<xsl:attribute name="data-author-id"><xsl:value-of select="normalize-space(./fields/*[@id=13])" /></xsl:attribute>
			<span field_id="6">
				<xsl:apply-templates select="./fields/*[@id=6]" mode="formatting"/>
			</span>
			<xsl:text> </xsl:text>
			<xsl:if test="count(./fields/*[@id=7]) &gt; 0">
				<span field_id="7">
					<xsl:apply-templates select="./fields/*[@id=7]" mode="formatting"/>
				</span>
				<xsl:text> </xsl:text>
			</xsl:if>
			<span field_id="8">
				<xsl:apply-templates select="./fields/*[@id=8]" mode="formatting"/>
			</span>
		</span>
		<sup class="P-Current-Author-Addresses">
			<xsl:for-each select="./*[@object_id='5']" >
				<xsl:variable name="lCurrentNode" select="." />
				<xsl:variable name="affiliation" select="normalize-space($lCurrentNode/fields/affiliation/value)" />
				<xsl:variable name="city" select="normalize-space($lCurrentNode/fields/city/value)" />
				<xsl:variable name="country" select="$lCurrentNode/fields/country/value" />
				<xsl:variable name="fullAffiliation" select="concat($affiliation, ', ', $city, ', ', $country)" />
				<xsl:variable name="lAffId" select="php:function('getContributorAffId', 'asd')"></xsl:variable>
				<span class="P-Current-Author-Single-Address">
					<xsl:value-of select="php:function('getUriSymbol', string($lAffId))" />
				</span>
				<xsl:if test="position()!=last()"><xsl:text>,</xsl:text></xsl:if>
			</xsl:for-each>
		</sup>
	</xsl:template>

	<xsl:template match="*" mode="singleAuthorAddress">
		<xsl:variable name="lCurrentNode" select="." />
		<xsl:variable name="affiliation" select="normalize-space($lCurrentNode/fields/affiliation/value)" />
		<xsl:variable name="city" select="normalize-space($lCurrentNode/fields/city/value)" />
		<xsl:variable name="country" select="$lCurrentNode/fields/country/value" />
		<xsl:variable name="fullAffiliation" select="concat($affiliation, ', ', $city, ', ', $country)" />
		<xsl:variable name="lAffId" select="php:function('getContributorAffId', $fullAffiliation)"></xsl:variable>

		<div class="P-Single-Author-Address">
		    <xsl:value-of select="php:function('getAffiliation', $fullAffiliation)" />
		</div>
	</xsl:template>

	<xsl:template match="*" mode="singleCorrespondingAuthor">
		<span>
			<xsl:attribute name="instance_id"><xsl:value-of select="./@instance_id" /></xsl:attribute>
			<span field_id="6">
				<xsl:apply-templates select="./fields/*[@id=6]" mode="formatting"/>
			</span>
			<xsl:text> </xsl:text>
			<xsl:if test="count(./fields/*[@id=7]) &gt; 0">
				<span field_id="7">
					<xsl:apply-templates select="./fields/*[@id=7]" mode="formatting"/>
				</span>
				<xsl:text> </xsl:text>
			</xsl:if>
			<span field_id="8">
				<xsl:apply-templates select="./fields/*[@id=8]" mode="formatting"/>
			</span>
			<xsl:text> (</xsl:text>
			<a field_id="4">
				<xsl:attribute name="href" ><xsl:text>mailto:</xsl:text><xsl:apply-templates select="./fields/*[@id=4]" mode="formatting_nospace"/></xsl:attribute>
				<xsl:apply-templates select="./fields/*[@id=4]" mode="formatting_nospace"/>
			</a>
			<xsl:text>)</xsl:text>
		</span>
	</xsl:template>

	<xsl:template match="*" mode="singleCorrespondingAuthorInLicense">
			<span>
				<xsl:attribute name="instance_id"><xsl:value-of select="./@instance_id" /></xsl:attribute>
				<span field_id="6">
					<xsl:apply-templates select="./fields/*[@id=6]" mode="formatting_nospace"/>
				</span>
				<xsl:text> </xsl:text>
				<xsl:if test="count(./fields/*[@id=7]) &gt; 0">
					<span field_id="7">
						<xsl:apply-templates select="./fields/*[@id=7]" mode="formatting_nospace"/>
					</span>
					<xsl:text> </xsl:text>
				</xsl:if>
				<span field_id="8">
					<xsl:apply-templates select="./fields/*[@id=8]" mode="formatting_nospace"/>
				</span>
			</span>
	</xsl:template>

	<!-- ABSTRACT AND KEYWORDS -->
	<xsl:template match="*" mode="abstractAndKeywords">
			<div>
				<xsl:attribute name="instance_id"><xsl:value-of select="./@instance_id" /></xsl:attribute>
				<xsl:if test="./fields/*[@id='18']/value != ''">
					<div class="P-Article-Preview-Block">
						<h1 id="abstract">Abstract</h1>
						<div class="P-Article-Preview-Block-Content" field_id="18">
							<xsl:call-template name="markContentEditableField">
								<xsl:with-param name="pObjectId" select="./@object_id" />
								<xsl:with-param name="pFieldId">18</xsl:with-param>
							</xsl:call-template>
							<xsl:apply-templates select="./fields/*[@id=18]" mode="formatting"/>
						</div>
					</div>
				</xsl:if>
				<xsl:if test="./fields/*[@id='19']/value != ''">
					<div class="P-Article-Preview-Block">
						<h1 id="keywords">Keywords</h1>
						<div class="P-Article-Preview-Block-Content" field_id="19">
							<xsl:call-template name="markContentEditableField">
								<xsl:with-param name="pObjectId" select="./@object_id" />
								<xsl:with-param name="pFieldId">19</xsl:with-param>
							</xsl:call-template>
							<xsl:apply-templates select="./fields/*[@id=19]" mode="formatting"/>
						</div>
					</div>
				</xsl:if>
			</div>
	</xsl:template>

	<!-- Default empty template.
	The sections we want to match will be specified manually -->
	<xsl:template match="*" mode="bodySections" />

	<!-- Introduction -->
	<xsl:template match="*[@object_id='16' or @object_id='166']" mode="bodySections">
		<xsl:if test="./fields/*[@id='20']/value != '' or ./subsection/@object_id != '' ">
			<div class="P-Article-Preview-Block">
				<xsl:attribute name="instance_id"><xsl:value-of select="./@instance_id" /></xsl:attribute>
				<h1 id="introduction"><xsl:value-of select="./@display_name" /></h1>
				<xsl:if test="./fields/*[@id='20']/value != ''" >
				<div class="P-Article-Preview-Block-Content" field_id="20">
					<xsl:call-template name="markContentEditableField">
						<xsl:with-param name="pObjectId" select="./@object_id" />
						<xsl:with-param name="pFieldId">20</xsl:with-param>
					</xsl:call-template>
					<xsl:apply-templates select="./fields/*[@id='20']" mode="formatting"/>
				</div>
				</xsl:if>
				<xsl:apply-templates mode="bodySubsection" select="./subsection"/>
			</div>
		</xsl:if>
	</xsl:template>

	<!-- Treatment material field -->
 <xsl:template match="*" mode="treatmentMaterialFieldCustom">
   <xsl:variable name="lContent">
   		<span>
			<xsl:call-template name="markContentEditableField">
				<xsl:with-param name="pObjectId"><xsl:value-of select="./@object_id" /></xsl:with-param>
				<xsl:with-param name="pFieldId"><xsl:value-of select="./@id" /></xsl:with-param>
			</xsl:call-template>
			<xsl:attribute name="field_id"><xsl:value-of select="./@id" /></xsl:attribute>
			<xsl:attribute name="instance_id"><xsl:value-of select="./@instance_id" /></xsl:attribute>
			<xsl:apply-templates select="./value" mode="formatting"/>
		</span>
   </xsl:variable>
   <xsl:variable name="lFieldId"><xsl:value-of select="./@id" /></xsl:variable>
   <span>
    <xsl:attribute name="class">dcLabel</xsl:attribute>
    <xsl:value-of select="./@field_name"></xsl:value-of><xsl:text>: </xsl:text>
   </span>
   <xsl:choose>
   		<xsl:when test="$pInArticleMode = 1 and ($lFieldId = 132 or $lFieldId = 133)">
   			<!-- verbatimLocality or verbatimLongitude -->
   			<xsl:choose>
	   			<xsl:when test="count(../field[@id=132]) = 0 or count(../field[@id=133]) = 0">
	   				<!-- One of longitude and latitude is not present -->
	   				<xsl:copy-of select="$lContent"></xsl:copy-of>
	   			</xsl:when>
	   			<xsl:otherwise>
	   				<!-- Both longitude and latitude are present -->
	   				<xsl:variable name="lLongitude"><xsl:value-of select="php:function('parseLocalityCoordinate', string(../field[@id=133]))"/></xsl:variable>
	   				<xsl:variable name="lLatitude"><xsl:value-of select="php:function('parseLocalityCoordinate', string(../field[@id=132]))"/></xsl:variable>
	   				<span class="locality-coordinate">
				      	<xsl:attribute name="data-longitude"><xsl:value-of select="$lLongitude"></xsl:value-of></xsl:attribute>
				      	<xsl:attribute name="data-latitude"><xsl:value-of select="$lLatitude"></xsl:value-of></xsl:attribute>
				      	<xsl:attribute name="data-is-locality-coordinate">1</xsl:attribute>
				      	<xsl:copy-of select="$lContent"/>
				    </span>
	   			</xsl:otherwise>
   			</xsl:choose>
   		</xsl:when>

   		<xsl:when test="$pInArticleMode = 1 and ($lFieldId = 136 or $lFieldId = 137)">
   			<!-- verbatimLocality or verbatimLongitude -->
   			<xsl:choose>
	   			<xsl:when test="count(../field[@id=136]) = 0 or count(../field[@id=137]) = 0">
	   				<!-- One of longitude and latitude is not present -->
	   				<xsl:copy-of select="$lContent"></xsl:copy-of>
	   			</xsl:when>
	   			<xsl:otherwise>
	   				<!-- Both longitude and latitude are present -->
	   				<xsl:variable name="lLongitude"><xsl:value-of select="php:function('parseLocalityCoordinate', string(../field[@id=137]))"/></xsl:variable>
	   				<xsl:variable name="lLatitude"><xsl:value-of select="php:function('parseLocalityCoordinate', string(../field[@id=136]))"/></xsl:variable>
	   				<span class="locality-coordinate">
				      	<xsl:attribute name="data-longitude"><xsl:value-of select="$lLongitude"></xsl:value-of></xsl:attribute>
				      	<xsl:attribute name="data-latitude"><xsl:value-of select="$lLatitude"></xsl:value-of></xsl:attribute>
				      	<xsl:attribute name="data-is-locality-coordinate">1</xsl:attribute>
				      	<xsl:copy-of select="$lContent"/>
				    </span>
	   			</xsl:otherwise>
   			</xsl:choose>
   		</xsl:when>


   		<xsl:otherwise>
   			<xsl:copy-of select="$lContent"></xsl:copy-of>
   		</xsl:otherwise>
   </xsl:choose>
   <!--<xsl:variable name="lId" select="./@id"></xsl:variable>-->
   <!--<xsl:if test="($lId = 58) or ($lId = 60) or ($lId = 61) or ($lId = 114) or ($lId = 116)">-->

   <!--</xsl:if>-->
 </xsl:template>

	<!-- SUBSECTIONS - START -->
	<!-- Default empty template.
		 The sections we want to match will be specified manually
	 -->
	<xsl:template match="subsection" mode="bodySubsection">
		<xsl:variable name="lSecTitle"><xsl:apply-templates select="./fields/*[@id='211']" mode="formatting" /></xsl:variable>
		<div class="P-Article-Preview-Block-Content">
			<xsl:attribute name="instance_id"><xsl:value-of select="./@instance_id" /></xsl:attribute>
			<h2 class="subsection" field_id="211">
				<xsl:call-template name="markContentEditableField">
					<xsl:with-param name="pObjectId" select="./@object_id" />
					<xsl:with-param name="pFieldId">211</xsl:with-param>
				</xsl:call-template>
				<xsl:copy-of select="$lSecTitle" />
			</h2>
			<div field_id="212">
				<xsl:call-template name="markContentEditableField">
					<xsl:with-param name="pObjectId" select="./@object_id" />
					<xsl:with-param name="pFieldId">212</xsl:with-param>
				</xsl:call-template>
				<xsl:apply-templates select="./fields/*[@id='212']" mode="formatting"/>
			</div>
		</div>
	</xsl:template>
	<!-- SUBSECTIONS - END -->

	<!-- Default empty template.
		 The sections we want to match will be specified manually
	 -->
	<xsl:template match="*" mode="articleBack" />

	<!-- Acknowledgements -->
	<xsl:template match="*[@object_id='57']" mode="articleBack">
		<xsl:if test="./fields/*[@id='223']/value != ''">
			<div class="P-Article-Preview-Block">
				<xsl:attribute name="instance_id"><xsl:value-of select="./@instance_id" /></xsl:attribute>
				<h1 id="acknowledgements">Acknowledgements</h1>
				<div class="P-Article-Preview-Block-Content" field_id="223">
					<xsl:call-template name="markContentEditableField">
						<xsl:with-param name="pObjectId" select="./@object_id" />
						<xsl:with-param name="pFieldId">223</xsl:with-param>
					</xsl:call-template>
					<xsl:apply-templates select="./fields/*[@id='223']" mode="formatting"/>
				</div>
			</div>
		</xsl:if>
	</xsl:template>


	<!-- Author contributions -->
	<xsl:template match="*[@object_id='202']" mode="articleBack">
		<xsl:if test="./fields/*[@id='464']/value != ''">
			<div class="P-Article-Preview-Block">
				<xsl:attribute name="instance_id"><xsl:value-of select="./@instance_id" /></xsl:attribute>
				<h1 id="authorcontribution">Author contributions</h1>
				<div class="P-Article-Preview-Block-Content" field_id="464">
					<xsl:call-template name="markContentEditableField">
						<xsl:with-param name="pObjectId" select="./@object_id" />
						<xsl:with-param name="pFieldId">464</xsl:with-param>
					</xsl:call-template>
					<xsl:apply-templates select="./fields/*[@id='464']" mode="formatting"/>
				</div>
			</div>
		</xsl:if>
	</xsl:template>

	<!-- Supplementary files -->
	<xsl:template match="*[@object_id='56']" mode="articleBack">
		<xsl:if test="count(./*[@object_id='55']) &gt; 0">
			<div class="P-Article-Preview-Block">
				<xsl:attribute name="instance_id"><xsl:value-of select="./@instance_id" /></xsl:attribute>
				<h1 id="supplementary_files">Supplementary material<xsl:if test="count(./*[@object_id='55']) &gt; 1">s</xsl:if></h1>
					<xsl:for-each select="//*[@object_id='55']">
						<div class="Supplemantary-Material">
							<xsl:apply-templates select="." mode="singleSupplementaryMaterial" />
						</div>
					</xsl:for-each>
			</div>
		</xsl:if>
	</xsl:template>

	<!-- Formatting uploaded files spaces -->
	<xsl:template match="*" mode="formatting_uploaded_file">
		<xsl:param name="lFileName"/>
		<xsl:param name="lUploadedFileName"/>
		<xsl:param name="lSupplFileInstanceId"/>

		<xsl:if test="$lUploadedFileName != ''">
			<span class="fieldLabel">Filename:</span><xsl:text>&#160;</xsl:text>
			<xsl:value-of select="normalize-space($lUploadedFileName)"/>
			<xsl:if test="$pInArticleMode = 0">
				<xsl:text> - </xsl:text>
			</xsl:if>
		</xsl:if>
		<xsl:if test="$lFileName != ''">
			<xsl:if test="$pInArticleMode = 1">
				<br/>
			</xsl:if>
			<a class="download" target="_blank">
				<xsl:attribute name="href"><xsl:value-of select="php:function('GetSupplFileDownloadLink', normalize-space($pSiteUrl), normalize-space($lFileName), string($lSupplFileInstanceId), $pInArticleMode)" /></xsl:attribute>
				<xsl:attribute name="name"><xsl:value-of select="normalize-space($lFileName)"/></xsl:attribute>
				<xsl:text>Download file</xsl:text>
			</a>
			(<xsl:value-of select="php:function('getUploadedFileSize', string($lFileName))" />)
		</xsl:if>
	</xsl:template>


	<!-- Single supplementary material -->
	<xsl:template match="*[@object_id='55']" mode="singleSupplementaryMaterial">
		<xsl:variable name="instance" select="./@instance_id" />

			<xsl:if test="./fields/*[@id='214']/value != ''">
				<div class="Supplemantary-File-Title">
					<span class="fig-label-RC">
						<xsl:text>Suppl. material </xsl:text>
						<xsl:for-each select="../*[@object_id='55']">
							<xsl:if test="./@instance_id = $instance">
								<xsl:value-of select="position()" />
							</xsl:if>
						</xsl:for-each>
						<xsl:text>: </xsl:text>
					</span>
					<span field_id="214">
						<xsl:call-template name="markContentEditableField">
							<xsl:with-param name="pObjectId">55</xsl:with-param>
							<xsl:with-param name="pFieldId">214</xsl:with-param>
						</xsl:call-template>
						<xsl:attribute name="instance_id"><xsl:value-of select="./@instance_id" /></xsl:attribute>
						<xsl:apply-templates select="./fields/*[@id='214']/value" mode="formatting"/>
					</span>
				</div>
			</xsl:if>

		<xsl:if test="./fields/*[@id='215']/value != '' or ./fields/*[@id='216']/value != '' or ./fields/*[@id='217']/value != '' or ./fields/*[@id='222']/value != ''">
			<div class="suppl-section-holder">
				<xsl:if test="./fields/*[@id='215']/value != ''">
					<div class="myfieldHolder">
						<span class="fieldLabel">
							<xsl:value-of select="./fields/*[@id='215']/@field_name" />:&#160;</span>
						<span class="fieldValue" field_id="215">
							<xsl:call-template name="markContentEditableField">
								<xsl:with-param name="pObjectId">55</xsl:with-param>
								<xsl:with-param name="pFieldId">215</xsl:with-param>
							</xsl:call-template>
							<xsl:attribute name="instance_id"><xsl:value-of select="./@instance_id" /></xsl:attribute>
							<xsl:apply-templates select="./fields/*[@id='215']/value" mode="formatting"/>
						</span>
					</div>
				</xsl:if>
				<xsl:if test="./fields/*[@id='216']/value != ''">
					<div class="myfieldHolder">
						<span class="fieldLabel">
							<xsl:value-of select="./fields/*[@id='216']/@field_name" />:&#160;</span>
						<span class="fieldValue" field_id="216">
							<xsl:call-template name="markContentEditableField">
								<xsl:with-param name="pObjectId">55</xsl:with-param>
								<xsl:with-param name="pFieldId">216</xsl:with-param>
							</xsl:call-template>
							<xsl:attribute name="instance_id"><xsl:value-of select="./@instance_id" /></xsl:attribute>
							<xsl:apply-templates select="./fields/*[@id='216']/value" mode="formatting"/>
						</span>
					</div>
				</xsl:if>
				<xsl:if test="./fields/*[@id='217']/value != ''">
					<div class="myfieldHolder">
						<span class="fieldLabel">
							<xsl:value-of select="./fields/*[@id='217']/@field_name" />:&#160;</span>
						<div class="fieldValue" field_id="217">
							<xsl:call-template name="markContentEditableField">
								<xsl:with-param name="pObjectId">55</xsl:with-param>
								<xsl:with-param name="pFieldId">217</xsl:with-param>
							</xsl:call-template>
							<xsl:attribute name="instance_id"><xsl:value-of select="./@instance_id" /></xsl:attribute>
							<xsl:apply-templates select="./fields/*[@id='217']/value" mode="formatting"/>
						</div>
					</div>
				</xsl:if>
				<xsl:if test="./fields/*[@id='222']/value != ''">
					<span class="Supplemantary-File-Section-Label">
						<xsl:attribute name="field_id"><xsl:value-of select="./fields/file/@id" /></xsl:attribute>
						<xsl:apply-templates select="./fields/file/value" mode="formatting_uploaded_file">
							<xsl:with-param name="lFileName" select="php:function('getFileNameById', string(./fields/file/value))"></xsl:with-param>
							<xsl:with-param name="lUploadedFileName" select="php:function('getUploadedFileNameById', string(./fields/file/value))" />
							<xsl:with-param name="lSupplFileInstanceId" select="./@instance_id"></xsl:with-param>
						</xsl:apply-templates>
					</span>
				</xsl:if>
			</div>
		</xsl:if>
	</xsl:template>


	<xsl:template match="*[@object_id='236']" mode="figuresPreview">
		<xsl:apply-templates select="./*[@object_id='221']" mode="singleFigNormalPreview"/>
	</xsl:template>

	<!-- Figure normal previews -->
	<xsl:template match="*[@object_id='221']" mode="singleFigNormalPreview">
		<xsl:apply-templates select="./*[@object_id &gt; 0]" mode="singleFigNormalPreview"/>
	</xsl:template>

	<xsl:template name="imagePicPreview">
		<xsl:param name="pInstanceId" />
		<xsl:param name="pPicId" />
		<xsl:param name="pImageType" >1</xsl:param>
		<xsl:param name="pPlateNum" />

		<xsl:variable name="pImageLink"><xsl:value-of select="$pSiteUrl"/>/showfigure.php?filename=<xsl:choose>
			<xsl:when test="$pImageType = 1" >singlefig</xsl:when>
			<xsl:otherwise>twocolumn</xsl:otherwise>
		</xsl:choose>_<xsl:value-of select="$pPicId"/>.jpg</xsl:variable>
		<xsl:variable name="lImageZoomLink"><xsl:value-of select="php:function('GetFigureZoomLink', string($pSiteUrl), $pInstanceId, $pInArticleMode)" /></xsl:variable>
		<xsl:variable name="lImageDownloadLink"><xsl:value-of select="php:function('GetFigureDownloadLink', string($pSiteUrl), $pInstanceId, $pPicId, $pInArticleMode)" /></xsl:variable>

		<xsl:variable name="lContent">
				<a target="_blank">
					<xsl:attribute name="href">
						<xsl:value-of select="$lImageZoomLink"/>
					</xsl:attribute>
					<img>
						<xsl:attribute name="src">
							<xsl:value-of select="$pImageLink"/>
						</xsl:attribute>
						<xsl:attribute name="alt" />
					</img>
				</a>
				<a target="_blank" class="P-Article-Preview-Picture-Zoom-Small">
					<xsl:attribute name="href">
						<xsl:value-of select="$lImageZoomLink"/>
					</xsl:attribute>
				</a>
				<a target="_blank" class="P-Article-Preview-Picture-Download-Small" title="Download image">
					<xsl:attribute name="href"><xsl:value-of select="$lImageDownloadLink" /></xsl:attribute>
					<img src="/i/download-icon-30.png" alt=""/>
				</a>
		</xsl:variable>
		<div>
			<xsl:attribute name="class">
				<xsl:choose>
					<xsl:when test="$pImageType = 1">holder</xsl:when><!-- Image pic -->
					<xsl:when test="$pImageType = 2">plate2column</xsl:when><!-- Plate Type 2/3/4 pic-->
				</xsl:choose>
			</xsl:attribute>
			<xsl:choose>
				<!-- Image pic -->
				<xsl:when test="$pImageType = 1" >
					<xsl:copy-of select="$lContent"/>
				</xsl:when>
				<!-- Plate-->
				<xsl:otherwise >
					<div class="singlePlatePhoto">
						<xsl:copy-of select="$lContent"/>
						<div class="Plate-part-letter fig">
							<xsl:attribute name="rid"><xsl:value-of select="./@instance_id" /></xsl:attribute>
							<xsl:value-of select="$pPlateNum"/>
						</div>
					</div>
				</xsl:otherwise>
			</xsl:choose>

		</div>
	</xsl:template>

	<!-- Image figure -->
	<xsl:template match="*[@object_id='222']" mode="singleFigNormalPreview">
		<xsl:variable name="lFigNumber"><xsl:value-of select="../fields/*[@id='489']/value"/></xsl:variable>
		<div class="figure">
			<xsl:attribute name="contenteditable">false</xsl:attribute>
			<xsl:attribute name="figure_position"><xsl:value-of select="$lFigNumber"/></xsl:attribute>
			<xsl:attribute name="figure_id"><xsl:value-of select="@instance_id"/></xsl:attribute>
			<xsl:call-template name="imagePicPreview">
				<xsl:with-param name="pInstanceId"><xsl:value-of select="../@instance_id"/></xsl:with-param>
				<xsl:with-param name="pPicId"><xsl:value-of select="./fields/*[@id='483']/value"/></xsl:with-param>
			</xsl:call-template>
			<div class="description jb">
				<div class="name">
					<xsl:text>Figure </xsl:text><xsl:value-of select="$lFigNumber"/><xsl:text>. </xsl:text>
				</div>
				<div class="figureCaption">
					<xsl:call-template name="markContentEditableField">
						<xsl:with-param name="pObjectId" select="@object_id"></xsl:with-param>
						<xsl:with-param name="pFieldId" select="482"></xsl:with-param>
					</xsl:call-template>
					<xsl:attribute name="instance_id"><xsl:value-of select="@instance_id" /></xsl:attribute>
					<xsl:attribute name="field_id"><xsl:value-of select="482" /></xsl:attribute>
					<xsl:apply-templates select="./fields/*[@id='482']/value" mode="formatting"/>
				</div>
			</div>
			<div class="P-Clear"></div>
		</div>
	</xsl:template>

	<!-- Video figure -->
	<xsl:template match="*[@object_id='223']" mode="singleFigNormalPreview">
		<xsl:variable name="lFigNumber"><xsl:value-of select="../fields/*[@id='489']/value"/></xsl:variable>
		<div class="figure">
			<xsl:attribute name="contenteditable">false</xsl:attribute>
			<xsl:attribute name="figure_position">
				<xsl:value-of select="$lFigNumber"/>
			</xsl:attribute>
			<xsl:attribute name="figure_id"><xsl:value-of select="@instance_id"/></xsl:attribute>
			<div class="holder">
				<iframe width="620" height="400" frameborder="0">
					<xsl:attribute name="src">
						<xsl:text>http://www.youtube.com/embed/</xsl:text>
						<xsl:value-of select="php:function('getYouTubeId', string(./fields/*[@id='486']/value))"/>
					</xsl:attribute>
				</iframe>
			</div>
			<div class="description">
				<div class="name">
					<xsl:text>Figure </xsl:text><xsl:value-of select="$lFigNumber"/><xsl:text>. </xsl:text>
				</div>
				<div class="figureCaption">
					<xsl:call-template name="markContentEditableField">
						<xsl:with-param name="pObjectId" select="@object_id"></xsl:with-param>
						<xsl:with-param name="pFieldId" select="482"></xsl:with-param>
					</xsl:call-template>
					<xsl:attribute name="instance_id"><xsl:value-of select="@instance_id" /></xsl:attribute>
					<xsl:attribute name="field_id"><xsl:value-of select="482" /></xsl:attribute>
					<xsl:apply-templates select="./fields/*[@id='482']/value" mode="formatting"/>
				</div>
			</div>
			<div class="P-Clear"></div>
		</div>
	</xsl:template>

	<!-- Plate figure -->
	<xsl:template match="*[@object_id='224']" mode="singleFigNormalPreview">
		<xsl:variable name="lFigNumber"><xsl:value-of select="../fields/*[@id='489']/value"/></xsl:variable>
		<xsl:variable name="lPlateType"><xsl:value-of select="./fields/*[@id='485']/value/@value_id"/></xsl:variable>
		<xsl:variable name="lImageType">
			<xsl:choose>
				<xsl:when test="$lPlateType = 1">3</xsl:when>
				<xsl:otherwise>2</xsl:otherwise>
			</xsl:choose>
		</xsl:variable>
		<div class="figure">
			<xsl:attribute name="contenteditable">false</xsl:attribute>
			<xsl:attribute name="figure_position">
				<xsl:value-of select="$lFigNumber"/>
			</xsl:attribute>
			<xsl:attribute name="figure_id"><xsl:value-of select="@instance_id"/></xsl:attribute>
			<div class="plate">
				<xsl:if test="$lPlateType = 1"><!-- 2 rows 1 columns -->
					<xsl:for-each select=".//*[@object_id='225' or @object_id='226']">
						<xsl:call-template name="imagePicPreview">
							<xsl:with-param name="pInstanceId"><xsl:value-of select="@instance_id"/></xsl:with-param>
							<xsl:with-param name="pPicId"><xsl:value-of select="./fields/*[@id='484']/value"/></xsl:with-param>
							<xsl:with-param name="pImageType"><xsl:value-of select="$lImageType"/></xsl:with-param>
							<xsl:with-param name="pPlateNum">
								<xsl:choose>
									<xsl:when test="@object_id='225'">a</xsl:when>
									<xsl:when test="@object_id='226'">b</xsl:when>
								</xsl:choose>
							</xsl:with-param>
						</xsl:call-template>
					</xsl:for-each>
				</xsl:if>
				<xsl:if test="$lPlateType > 1 "><!-- 1 rows 2 columns -->
					<div class="plateRow">
						<xsl:for-each select=".//*[@object_id='225' or @object_id='226']">
							<xsl:call-template name="imagePicPreview">
								<xsl:with-param name="pInstanceId"><xsl:value-of select="@instance_id"/></xsl:with-param>
								<xsl:with-param name="pPicId"><xsl:value-of select="./fields/*[@id='484']/value"/></xsl:with-param>
								<xsl:with-param name="pImageType"><xsl:value-of select="$lImageType"/></xsl:with-param>
								<xsl:with-param name="pPlateNum">
									<xsl:choose>
										<xsl:when test="@object_id='225'">a</xsl:when>
										<xsl:when test="@object_id='226'">b</xsl:when>
									</xsl:choose>
								</xsl:with-param>
							</xsl:call-template>
						</xsl:for-each>
					</div>
				</xsl:if>
				<xsl:if test="$lPlateType > 2"><!-- 2 rows 2 columns -->
					<div class="plateRow">
						<xsl:for-each select=".//*[@object_id='227' or @object_id='228']">
							<xsl:call-template name="imagePicPreview">
								<xsl:with-param name="pInstanceId"><xsl:value-of select="@instance_id"/></xsl:with-param>
								<xsl:with-param name="pPicId"><xsl:value-of select="./fields/*[@id='484']/value"/></xsl:with-param>
								<xsl:with-param name="pImageType"><xsl:value-of select="$lImageType"/></xsl:with-param>
								<xsl:with-param name="pPlateNum">
									<xsl:choose>
										<xsl:when test="@object_id='227'">c</xsl:when>
										<xsl:when test="@object_id='228'">d</xsl:when>
									</xsl:choose>
								</xsl:with-param>
							</xsl:call-template>
						</xsl:for-each>
					</div>
				</xsl:if>
				<xsl:if test="$lPlateType > 3"><!-- 3 rows 2 columns -->
					<div class="plateRow">
						<xsl:for-each select=".//*[@object_id='229' or @object_id='230']">
							<xsl:call-template name="imagePicPreview">
								<xsl:with-param name="pInstanceId"><xsl:value-of select="@instance_id"/></xsl:with-param>
								<xsl:with-param name="pPicId"><xsl:value-of select="./fields/*[@id='484']/value"/></xsl:with-param>
								<xsl:with-param name="pImageType"><xsl:value-of select="$lImageType"/></xsl:with-param>
								<xsl:with-param name="pPlateNum">
									<xsl:choose>
										<xsl:when test="@object_id='229'">e</xsl:when>
										<xsl:when test="@object_id='230'">f</xsl:when>
									</xsl:choose>
								</xsl:with-param>
							</xsl:call-template>
						</xsl:for-each>
					</div>
				</xsl:if>
			</div>
			<div style="clear: both"></div>
			<div class="description">
				<div class="name">
					<xsl:text>Figure </xsl:text><xsl:value-of select="$lFigNumber"/><xsl:text>. </xsl:text>
				</div>
				<div class="figureCaption">
					<xsl:call-template name="markContentEditableField">
						<xsl:with-param name="pObjectId" select="@object_id"></xsl:with-param>
						<xsl:with-param name="pFieldId" select="482"></xsl:with-param>
					</xsl:call-template>
					<xsl:attribute name="instance_id"><xsl:value-of select="@instance_id" /></xsl:attribute>
					<xsl:attribute name="field_id"><xsl:value-of select="482" /></xsl:attribute>
					<xsl:apply-templates select="./fields/*[@id='482']/value" mode="formatting"/>
				</div>
				<xsl:for-each select=".//*[@object_id='225' or @object_id='226' or @object_id='227' or @object_id='228' or @object_id='229' or @object_id='230']">
					<xsl:if test="./fields/*[@id='487']/value != ''">
					<b>
						<xsl:choose>
							<xsl:when test="@object_id='225'">a</xsl:when>
							<xsl:when test="@object_id='226'">b</xsl:when>
							<xsl:when test="@object_id='227'">c</xsl:when>
							<xsl:when test="@object_id='228'">d</xsl:when>
							<xsl:when test="@object_id='229'">e</xsl:when>
							<xsl:when test="@object_id='230'">f</xsl:when>
						</xsl:choose>
					</b><xsl:text>:&#160;</xsl:text>
					<span class="figureCaption plateCaption">
						<xsl:call-template name="markContentEditableField">
							<xsl:with-param name="pObjectId" select="@object_id"></xsl:with-param>
							<xsl:with-param name="pFieldId" select="487"></xsl:with-param>
						</xsl:call-template>
						<xsl:attribute name="instance_id"><xsl:value-of select="@instance_id" /></xsl:attribute>
						<xsl:attribute name="field_id"><xsl:value-of select="487" /></xsl:attribute>
						<xsl:apply-templates select="./fields/*[@id='487']/value" mode="formatting"/>
					</span>
					<br/>
					</xsl:if>
				</xsl:for-each>
			</div>
			<div class="P-Clear"></div>
		</div>
	</xsl:template>



	<!-- Figure small previews -->
	<xsl:template match="*[@object_id='221']" mode="singleFigSmallPreview">
		<xsl:apply-templates select="./*[@object_id &gt; 0]" mode="singleFigSmallPreview"/>
	</xsl:template>

	<!-- Image figure -->
	<xsl:template match="*[@object_id='222']" mode="singleFigSmallPreview">
		<div class="P-Picture-Holder" style="float:left">
				<div class="pointerLink">
				<img style="float: left;"  alt="">
					<xsl:attribute name="src"><xsl:value-of select="$pSiteUrl"/>/showfigure.php?filename=singlefigmini_<xsl:value-of select="./fields/*[@id='483']/value"></xsl:value-of>.jpg</xsl:attribute>
				</img>
				<div class="P-Clear"></div>
			</div>
		</div>
		<div class="P-Block-Title-Holder">
			<div class="P-Figure-Num">Figure <xsl:value-of select="../fields/*[@id='489']/value"/></div>
			<div class="P-Figure-Desc"><xsl:apply-templates select="./fields/*[@id='482']/value" mode="formatting"/></div>
		</div>
		<div class="P-Clear"></div>
	</xsl:template>

	<!-- Video figure -->
	<xsl:template match="*[@object_id='223']" mode="singleFigSmallPreview">
		<xsl:variable name="lVideoId" select="php:function('getYouTubeIdFromURL', string(./fields/*[@id='486']))"></xsl:variable>
		<div class="P-Picture-Holder" style="float:left">
			<div class="pointerLink">
				<img style="float: left;"   title="YouTube Thumbnail Test"  width="90px" height="82px" alt="">
					<xsl:attribute name="src">http://img.youtube.com/vi/<xsl:value-of select="$lVideoId"></xsl:value-of>/1.jpg</xsl:attribute>
					<xsl:attribute name="id"><xsl:value-of select="$lVideoId"></xsl:value-of></xsl:attribute>
					<xsl:attribute name="class">youtube_<xsl:value-of select="$lVideoId"></xsl:value-of> youtubeVideoThumbnail</xsl:attribute>
				</img>
				<div class="P-Clear"></div>
			</div>
		</div>
		<div class="P-Block-Title-Holder">
			<div class="P-Figure-Num">Figure <xsl:value-of select="../fields/*[@id='489']/value"/></div>
			<div class="P-Figure-Desc"><xsl:apply-templates select="./fields/*[@id='482']/value" mode="formatting"/></div>
		</div>
		<div class="P-Clear"></div>
	</xsl:template>

	<!-- Plate figure -->
	<xsl:template match="*[@object_id='224']" mode="singleFigSmallPreview">
		<div class="P-Picture-Holder" style="float:left">
				<div class="pointerLink">
					<xsl:apply-templates select=".//*[@object_id='231' or @object_id='232' or @object_id='233' or @object_id='234']" mode="singleFigSmallPreview"/>
				<div class="P-Clear"></div>
			</div>
		</div>
		<div class="P-Block-Title-Holder">
			<div class="P-Figure-Num">Figure <xsl:value-of select="../fields/*[@id='489']/value"/></div>
			<div class="P-Figure-Desc"><xsl:apply-templates select="./fields/*[@id='482']/value" mode="formatting"/></div>
		</div>
		<div class="P-Clear"></div>
	</xsl:template>

	<!-- Plate type 1 image preview -->
	<xsl:template match="*[@object_id='231']" mode="singleFigSmallPreview">
		<div style="text-align: center; display: table; width: 90px">
			<div class="twocolumnmini fig">
				<xsl:attribute name="rid"><xsl:value-of select="./*[@object_id='225']/@instance_id" /></xsl:attribute>
				<img alt="">
					<xsl:attribute name="src"><xsl:value-of select="$pSiteUrl"/>/showfigure.php?filename=singlefigmini_<xsl:value-of select="./*[@object_id='225']/fields/*[@id='484']/value"></xsl:value-of>.jpg</xsl:attribute>
				</img>
			</div>
		</div>
		<div style="text-align: center; display: table; width: 90px">
			<div class="twocolumnmini fig">
				<xsl:attribute name="rid"><xsl:value-of select="./*[@object_id='226']/@instance_id" /></xsl:attribute>
				<img alt="">
					<xsl:attribute name="src"><xsl:value-of select="$pSiteUrl"/>/showfigure.php?filename=singlefigmini_<xsl:value-of select="./*[@object_id='226']/fields/*[@id='484']/value"></xsl:value-of>.jpg</xsl:attribute>
				</img>
			</div>
		</div>
	</xsl:template>

	<!-- Plate type 2 image preview -->
	<xsl:template match="*[@object_id='232']" mode="singleFigSmallPreview">
		<div class="twocolumnmini fig">
			<xsl:attribute name="rid"><xsl:value-of select="./*[@object_id='225']/@instance_id" /></xsl:attribute>
			<img style="float: left;"  alt="">
				<xsl:attribute name="src"><xsl:value-of select="$pSiteUrl"/>/showfigure.php?filename=plateportraitmini_<xsl:value-of select="./*[@object_id='225']/fields/*[@id='484']/value"></xsl:value-of>.jpg</xsl:attribute>
			</img>
		</div>
		<div class="twocolumnmini fig">
			<xsl:attribute name="rid"><xsl:value-of select="./*[@object_id='226']/@instance_id" /></xsl:attribute>
			<img style="float: left;"  alt="">
				<xsl:attribute name="src"><xsl:value-of select="$pSiteUrl"/>/showfigure.php?filename=plateportraitmini_<xsl:value-of select="./*[@object_id='226']/fields/*[@id='484']/value"></xsl:value-of>.jpg</xsl:attribute>
			</img>
		</div>
	</xsl:template>

	<!-- Plate type 3 image preview -->
	<xsl:template match="*[@object_id='233']" mode="singleFigSmallPreview">
		<div>
			<div class="twocolumnminiholder">
				<div class="twocolumnmini fig">
					<xsl:attribute name="rid"><xsl:value-of select="./*[@object_id='225']/@instance_id" /></xsl:attribute>
					<img alt="">
						<xsl:attribute name="src"><xsl:value-of select="$pSiteUrl"/>/showfigure.php?filename=twocolumnmini_<xsl:value-of select="./*[@object_id='225']/fields/*[@id='484']/value"></xsl:value-of>.jpg</xsl:attribute>
					</img>
				</div>
			</div>
			<div class="twocolumnminiholder">
				<div class="twocolumnmini fig">
					<xsl:attribute name="rid"><xsl:value-of select="./*[@object_id='226']/@instance_id" /></xsl:attribute>
					<img alt="">
						<xsl:attribute name="src"><xsl:value-of select="$pSiteUrl"/>/showfigure.php?filename=twocolumnmini_<xsl:value-of select="./*[@object_id='226']/fields/*[@id='484']/value"></xsl:value-of>.jpg</xsl:attribute>
					</img>
				</div>
			</div>
			<div class="twocolumnminiholder">
				<div class="twocolumnmini fig">
					<xsl:attribute name="rid"><xsl:value-of select="./*[@object_id='227']/@instance_id" /></xsl:attribute>
					<img alt="">
						<xsl:attribute name="src"><xsl:value-of select="$pSiteUrl"/>/showfigure.php?filename=twocolumnmini_<xsl:value-of select="./*[@object_id='227']/fields/*[@id='484']/value"></xsl:value-of>.jpg</xsl:attribute>
					</img>
				</div>
			</div>
			<div class="twocolumnminiholder">
				<div class="twocolumnmini fig">
					<xsl:attribute name="rid"><xsl:value-of select="./*[@object_id='228']/@instance_id" /></xsl:attribute>
					<img alt="">
						<xsl:attribute name="src"><xsl:value-of select="$pSiteUrl"/>/showfigure.php?filename=twocolumnmini_<xsl:value-of select="./*[@object_id='228']/fields/*[@id='484']/value"></xsl:value-of>.jpg</xsl:attribute>
					</img>
				</div>
			</div>
		</div>
	</xsl:template>

	<!-- Plate type 4 image preview -->
	<xsl:template match="*[@object_id='234']" mode="singleFigSmallPreview">
		<div>
			<div class="twocolumnminiholder">
				<div class="twocolumnmini fig">
					<xsl:attribute name="rid"><xsl:value-of select="./*[@object_id='225']/@instance_id" /></xsl:attribute>
					<img alt="">
						<xsl:attribute name="src"><xsl:value-of select="$pSiteUrl"/>/showfigure.php?filename=twocolumnmini_<xsl:value-of select="./*[@object_id='225']/fields/*[@id='484']/value"></xsl:value-of>.jpg</xsl:attribute>
					</img>
				</div>
			</div>
			<div class="twocolumnminiholder">
				<div class="twocolumnmini fig">
					<xsl:attribute name="rid"><xsl:value-of select="./*[@object_id='226']/@instance_id" /></xsl:attribute>
					<img alt="">
						<xsl:attribute name="src"><xsl:value-of select="$pSiteUrl"/>/showfigure.php?filename=twocolumnmini_<xsl:value-of select="./*[@object_id='226']/fields/*[@id='484']/value"></xsl:value-of>.jpg</xsl:attribute>
					</img>
				</div>
			</div>
			<div class="twocolumnminiholder">
				<div class="twocolumnmini fig">
					<xsl:attribute name="rid"><xsl:value-of select="./*[@object_id='227']/@instance_id" /></xsl:attribute>
					<img alt="">
						<xsl:attribute name="src"><xsl:value-of select="$pSiteUrl"/>/showfigure.php?filename=twocolumnmini_<xsl:value-of select="./*[@object_id='227']/fields/*[@id='484']/value"></xsl:value-of>.jpg</xsl:attribute>
					</img>
				</div>
			</div>
			<div class="twocolumnminiholder">
				<div class="twocolumnmini fig">
					<xsl:attribute name="rid"><xsl:value-of select="./*[@object_id='228']/@instance_id" /></xsl:attribute>
					<img alt="">
						<xsl:attribute name="src"><xsl:value-of select="$pSiteUrl"/>/showfigure.php?filename=twocolumnmini_<xsl:value-of select="./*[@object_id='228']/fields/*[@id='484']/value"></xsl:value-of>.jpg</xsl:attribute>
					</img>
				</div>
			</div>
			<div class="twocolumnminiholder">
				<div class="twocolumnmini fig">
					<xsl:attribute name="rid"><xsl:value-of select="./*[@object_id='229']/@instance_id" /></xsl:attribute>
					<img alt="">
						<xsl:attribute name="src"><xsl:value-of select="$pSiteUrl"/>/showfigure.php?filename=twocolumnmini_<xsl:value-of select="./*[@object_id='229']/fields/*[@id='484']/value"></xsl:value-of>.jpg</xsl:attribute>
					</img>
				</div>
			</div>
			<div class="twocolumnminiholder">
				<div class="twocolumnmini fig">
					<xsl:attribute name="rid"><xsl:value-of select="./*[@object_id='230']/@instance_id" /></xsl:attribute>
					<img alt="">
						<xsl:attribute name="src"><xsl:value-of select="$pSiteUrl"/>/showfigure.php?filename=twocolumnmini_<xsl:value-of select="./*[@object_id='230']/fields/*[@id='484']/value"></xsl:value-of>.jpg</xsl:attribute>
					</img>
				</div>
			</div>
		</div>
	</xsl:template>

	<!-- Table small preview -->
	<xsl:template match="*[@object_id='238']" mode="singleTableSmallPreview">
			<div class="P-Picture-Holder" style="float:left">
				<img src="/i/table_pic.png"/>
			</div>
			<div class="P-Block-Title-Holder">
				<div class="P-Figure-Num">Table <xsl:value-of select="./fields/*[@id='489']/value"/></div>
				<div class="P-Figure-Desc"><xsl:copy-of select="./fields/*[@id='482']/value"/></div>
				<div class="P-Figure-Download-Link">
					<a class="download-table-link">
						<xsl:attribute name="href">
							<xsl:value-of select="php:function('GetTableDownloadLink', string($pSiteUrl), string(./@instance_id), $pInArticleMode)" />
						</xsl:attribute>
						Download as CSV
					</a>
				</div>
			</div>
			<div class="P-Clear"></div>
	</xsl:template>

	<xsl:template match="*[@object_id='237']" mode="tablesPreview">
		<xsl:apply-templates select="./*[@object_id='238']" mode="singleTableNormalPreview"/>
	</xsl:template>

	<!-- Table regular preview -->
	<xsl:template match="*[@object_id='238']" mode="singleTableNormalPreview">
			<xsl:variable name="lFigNumber"><xsl:value-of select="./fields/*[@id='489']/value"/></xsl:variable>
			<div class="table">
				<xsl:attribute name="contenteditable">false</xsl:attribute>
				<xsl:attribute name="table_position"><xsl:value-of select="$lFigNumber"/></xsl:attribute>
				<xsl:attribute name="table_id"><xsl:value-of select="@instance_id"/></xsl:attribute>
				<div class="description">
					<div class="name">Table <xsl:value-of select="$lFigNumber" />.
						<span class="downloadmaterials">
							<a class="download-table-link">
								<xsl:attribute name="href">
									<xsl:value-of select="php:function('GetTableDownloadLink', string($pSiteUrl), string(./@instance_id), $pInArticleMode)" />
								</xsl:attribute>
								<xsl:text>Download as CSV&#160;</xsl:text>
								<img width="22" heigth="22" alt="" title="Download table">
									<xsl:attribute name="src"><xsl:value-of select="$pSiteUrl"/>/i/download_materials_icon.png</xsl:attribute>
								</img>

							</a>
						</span>
					</div>
					<div class="P-Inline">
						<div class="tableCaption">
							<xsl:call-template name="markContentEditableField">
								<xsl:with-param name="pObjectId" select="@object_id"></xsl:with-param>
								<xsl:with-param name="pFieldId" select="482"></xsl:with-param>
							</xsl:call-template>
							<xsl:attribute name="instance_id"><xsl:value-of select="@instance_id" /></xsl:attribute>
							<xsl:attribute name="field_id"><xsl:value-of select="482" /></xsl:attribute>
							<xsl:apply-templates select="./fields/*[@id='482']/value" mode="formatting"/>
						</div>
					</div>
				</div>
				<div class="P-Clear"></div>
				<div class="Table-Body">
					<div class="tableCaption">
						<xsl:call-template name="markContentEditableField">
							<xsl:with-param name="pObjectId" select="@object_id"></xsl:with-param>
							<xsl:with-param name="pFieldId" select="490"></xsl:with-param>
						</xsl:call-template>
						<xsl:attribute name="instance_id"><xsl:value-of select="@instance_id" /></xsl:attribute>
						<xsl:attribute name="field_id"><xsl:value-of select="490" /></xsl:attribute>
						<xsl:apply-templates select="./fields/*[@id='490']/value" mode="table_formatting"/>
					</div>
				</div>
				<div class="P-Clear"></div>
			</div>
	</xsl:template>

	<!-- Article of the future GENERAL REPLACE -->
	<xsl:template name="get_node_text_template">
	  <xsl:param name="pNode" />
	  <xsl:variable name="lLocalName" ><xsl:value-of select="php:function('strtolower', local-name($pNode))" /></xsl:variable>
	  <xsl:variable name="lNodeIsTextNode" select="$pNode/self::text()" />
	  <xsl:variable name="lNodeIsElement" select="$pNode/self::*" />
	  <xsl:variable name="lChildContent">
	   <xsl:for-each select="$pNode/child::node()" >
	    <xsl:variable name="lCurrentNode" select="." />
	    <xsl:call-template name="get_node_text_template">
	     <xsl:with-param name="pNode" select="$lCurrentNode"></xsl:with-param>
	    </xsl:call-template>
	   </xsl:for-each>
	  </xsl:variable>
	  <xsl:choose>
	   <xsl:when test="$lNodeIsTextNode"><xsl:value-of select="$pNode"/></xsl:when>
	   <xsl:when test="$lNodeIsElement">
	    <xsl:choose>
	     <xsl:when test="$lLocalName='locality-coordinates'">
		      <span class="locality-coordinate">
		      	<xsl:attribute name="data-longitude"><xsl:value-of select="php:function('parseLocalityCoordinate', string(@longitude))"></xsl:value-of></xsl:attribute>
		      	<xsl:attribute name="data-latitude"><xsl:value-of select="php:function('parseLocalityCoordinate', string(@latitude))"></xsl:value-of></xsl:attribute>
		      	<xsl:attribute name="data-is-locality-coordinate">1</xsl:attribute>
		      	<xsl:copy-of select="$lChildContent"/>
		      </span>
	     </xsl:when>
	     <xsl:when test="$lLocalName='tn'">
		      <span class="tn">
		      	<xsl:copy-of select="$lChildContent"/>
		      </span>
	     </xsl:when>
	     <xsl:when test="$lLocalName='tn-part'">
		      <span>
		      	 <xsl:attribute name="class"><xsl:value-of select="./@type" /></xsl:attribute>
		      	 <xsl:attribute name="full-name"><xsl:value-of select="./@full-name" /></xsl:attribute>
		       	 <xsl:copy-of select="$lChildContent"/>
		      </span>
	     </xsl:when>
	     <xsl:when test="$lLocalName='xref'">
		     <xsl:element name="{$lLocalName}">
		       <xsl:for-each select="$pNode/attribute::*">
		        <xsl:attribute name="{local-name(.)}"><xsl:value-of select="." /></xsl:attribute>
		       </xsl:for-each>
		       <xsl:attribute name="class"><xsl:value-of select="./@type" /></xsl:attribute>
		       <xsl:copy-of select="$lChildContent"/>
		      </xsl:element>
	     </xsl:when>
	     <xsl:when test="$lLocalName='em'">
		      <i>
		       	 <xsl:copy-of select="$lChildContent"/>
		      </i>
	     </xsl:when>
	     <xsl:when test="$lLocalName='reference-citation' or $lLocalName='fig-citation' or $lLocalName='tbls-citation' or $lLocalName='sup-files-citation'">
		       	 <xsl:element name="{$lLocalName}">
			       <xsl:for-each select="$pNode/attribute::*">
			        <xsl:attribute name="{local-name(.)}"><xsl:value-of select="." /></xsl:attribute>
			       </xsl:for-each>
			       <xsl:attribute name="class">citations-holder</xsl:attribute>
			       <xsl:copy-of select="$lChildContent"/>
			      </xsl:element>
	     </xsl:when>
	     <xsl:otherwise>
			  <xsl:element name="{$lLocalName}">
		       <xsl:for-each select="$pNode/attribute::*">
		        <xsl:attribute name="{local-name(.)}"><xsl:value-of select="." /></xsl:attribute>
		       </xsl:for-each>
		       <xsl:copy-of select="$lChildContent"/>
		      </xsl:element>
	     </xsl:otherwise>
	    </xsl:choose>
	   </xsl:when>
	  </xsl:choose>
	 </xsl:template>


	<xsl:template name="goodIMG">
		<xsl:param name="filename" />
		<img alt="">
			<xsl:attribute name="src"   ><xsl:value-of select="$filename"/></xsl:attribute>
			<xsl:attribute name="width" ><xsl:value-of select="php:function('getimageW', string($filename))" /></xsl:attribute>
			<xsl:attribute name="height"><xsl:value-of select="php:function('getimageH', string($filename))" /></xsl:attribute>
		</img>
	</xsl:template>

	<!-- Article of the future SINGLE ELEMENT PREVIEWS START -->

	<!-- Article of the future preview template of a single figure -->
	<xsl:template match="*" mode="article_preview_figure">
		<xsl:if test="./fields/figure_type/value/@value_id = '1'">
			<div class="figure">
				<div class="holder">
					<xsl:call-template name="goodIMG">
						<xsl:with-param name="filename"><xsl:value-of select="$pSiteUrl"/>/showfigure.php?filename=singlefigAOF_<xsl:value-of select="./image/fields/photo_select/value"></xsl:value-of>.jpg</xsl:with-param>
					</xsl:call-template>
				</div>
				<a target="_blank" class="P-Article-Preview-Picture-Zoom-Small">
					<xsl:attribute name="href">
						<xsl:value-of select="php:function('GetFigureZoomLink', string($pSiteUrl), string(@instance_id), $pInArticleMode)" />
					</xsl:attribute>
				</a>
				<a target="_blank" class="P-Article-Preview-Picture-Download-Small" title="Download image">
					<xsl:attribute name="href"><xsl:value-of select="php:function('GetFigureDownloadLink', string($pSiteUrl), string(@instance_id), string(./image/fields/photo_select/value), $pInArticleMode)" /></xsl:attribute>
					<img src="/i/download-icon-30.png" alt=""/>
				</a>
				<div class="description">
					<span class="fig-label-RC">
						<xsl:value-of select="./@display_name"></xsl:value-of>
							<xsl:text> </xsl:text>
						<xsl:value-of select="./fields/figure_number"></xsl:value-of>
					</span>
					<div class="list-caption">
						<xsl:apply-templates select="./image/fields/figure_caption/value" mode="formatting"/>
					</div>
				</div>
			</div>
		</xsl:if>
		<xsl:if test="./fields/figure_type/value/@value_id = '2'">
			<xsl:apply-templates select="./multiple_images_plate" mode="singleFigNormalPreview" />
		</xsl:if>
		<xsl:if test="./fields/figure_type/value/@value_id = '3'">
			<xsl:apply-templates select="./video" mode="VideoNormalPreview" />
		</xsl:if>
	</xsl:template>
	
	<xsl:template match="*" mode="VideoNormalPreview">
		<xsl:variable name="lFigNumber"><xsl:value-of select="../fields/*[@id='489']/value"/></xsl:variable>
		<div class="figure">
			<xsl:attribute name="contenteditable">false</xsl:attribute>
			<xsl:attribute name="figure_position">
				<xsl:value-of select="$lFigNumber"/>
			</xsl:attribute>
			<xsl:attribute name="figure_id"><xsl:value-of select="@instance_id"/></xsl:attribute>
			<div class="holder">
				<iframe width="384" height="240" frameborder="0">
					<xsl:attribute name="src">
						<xsl:text>http://www.youtube.com/embed/</xsl:text>
						<xsl:value-of select="php:function('getYouTubeId', string(./fields/*[@id='486']/value))"/>
					</xsl:attribute>
				</iframe>
			</div>
			<div class="description">
				<div class="name">
					<xsl:text>Figure </xsl:text><xsl:value-of select="$lFigNumber"/><xsl:text>. </xsl:text>
				</div>
				<div class="figureCaption">
					<xsl:call-template name="markContentEditableField">
						<xsl:with-param name="pObjectId" select="@object_id"></xsl:with-param>
						<xsl:with-param name="pFieldId" select="482"></xsl:with-param>
					</xsl:call-template>
					<xsl:attribute name="instance_id"><xsl:value-of select="@instance_id" /></xsl:attribute>
					<xsl:attribute name="field_id"><xsl:value-of select="482" /></xsl:attribute>
					<xsl:apply-templates select="./fields/*[@id='482']/value" mode="formatting"/>
				</div>
			</div>
			<div class="P-Clear"></div>
		</div>
	</xsl:template>
	
	<!-- Article of the future preview template of a single plate part -->
	<xsl:template match="*" mode="article_preview_plate">
		<xsl:variable name="platePart">
			<xsl:choose>
				<xsl:when test="@object_id='225'">a</xsl:when>
				<xsl:when test="@object_id='226'">b</xsl:when>
				<xsl:when test="@object_id='227'">c</xsl:when>
				<xsl:when test="@object_id='228'">d</xsl:when>
				<xsl:when test="@object_id='229'">e</xsl:when>
				<xsl:when test="@object_id='230'">f</xsl:when>
			</xsl:choose>
		</xsl:variable>

		<div class="figure">
			<div class="holder">
				<xsl:call-template name="goodIMG">
					<xsl:with-param name="filename"><xsl:value-of select="$pSiteUrl"/>/showfigure.php?filename=singlefigAOF_<xsl:value-of select="./fields/image_id/value"/>.jpg</xsl:with-param>
				</xsl:call-template>
			</div>
			<a target="_blank" class="P-Article-Preview-Picture-Zoom-Small">
				<xsl:attribute name="href">
					<xsl:value-of select="php:function('GetFigureZoomLink', string($pSiteUrl), string(@instance_id), $pInArticleMode)" />
				</xsl:attribute>
			</a>
			<a target="_blank" class="P-Article-Preview-Picture-Download-Small" title="Download image">
				<xsl:attribute name="href"><xsl:value-of select="php:function('GetFigureDownloadLink', string($pSiteUrl), @instance_id, string(./fields/image_id/value), $pInArticleMode)" /></xsl:attribute>
				<img src="/i/download-icon-30.png" alt=""/>
			</a>
			<div class="Plate-part-letter fig">
				<xsl:attribute name="rid"><xsl:value-of select="./@instance_id" /></xsl:attribute>
				<xsl:value-of select="$platePart"/>
			</div>

			<div class="description">
				<span class="fig-label-RC">
					Figure
					<xsl:value-of select="../../../../fields/figure_number/value" /><xsl:text> </xsl:text>
					<xsl:value-of select="$platePart"/>
				</span>
				<div class="list-caption">
					<xsl:apply-templates select="../../../../multiple_images_plate/fields/plate_caption/value" mode="formatting"/>
					<p><xsl:value-of select="./fields/plate_desc/value" /></p>
				</div>
			</div>
			<div class="plate_link">
				<a class="plate_link fig">
					<xsl:attribute name="rid"><xsl:value-of select="../../../../@instance_id" /></xsl:attribute>
					See whole plate
				</a>
			</div>
		</div>
	</xsl:template>

	<!-- Article of the future preview template of a single table -->
	<xsl:template match="*" mode="article_preview_table">
		<xsl:apply-templates select="." mode="singleTableNormalPreview"/>
		<!-- <script type="text/javascript">
			<![CDATA[
			document.getElementById("P-Article-Info-Bar").className +=" ST";
			]]>
			</script> -->

		<!-- The node of the specific table -->
		<xsl:variable name="lCurrentNode" select="."></xsl:variable>
	</xsl:template>

	<!-- Article of the future preview template of a single reference -->
	<xsl:template match="*" mode="article_preview_reference">
		<xsl:apply-templates select="." mode="articleBack"/>
		<xsl:call-template name="AOF-Place-Cited-Element-Navigation">
		    <xsl:with-param name="pInstanceId" select="@instance_id"></xsl:with-param>
		</xsl:call-template>
		<xsl:apply-templates select="." mode="RefinderLinks"/>
		<xsl:apply-templates select="." mode="RefinderFormat"/>
		
	</xsl:template>

		<xsl:template match="*" mode="RefinderLinks">
			<xsl:variable name="lArticleType">
				<xsl:choose>
					<xsl:when test="./*[@object_id='97']/*[@object_id='102']">1</xsl:when> <!-- Journal article -->
					<xsl:when test="./*[@object_id='97']/*[@object_id='98']">2</xsl:when> <!-- Book -->
					<xsl:when test="./*[@object_id='97']/*[@object_id='99']">3</xsl:when> <!-- Book chapter -->
					<xsl:when test="./*[@object_id='97']/*[@object_id='103']">4</xsl:when> <!-- Conference paper -->
					<xsl:when test="./*[@object_id='97']/*[@object_id='105']">5</xsl:when> <!-- Conference proceedings -->
					<xsl:when test="./*[@object_id='97']/*[@object_id='106']">6</xsl:when> <!-- Thesis -->
					<xsl:when test="./*[@object_id='97']/*[@object_id='107']">7</xsl:when> <!-- Software reference -->
					<xsl:when test="./*[@object_id='97']/*[@object_id='108']">8</xsl:when> <!-- Website reference -->
				</xsl:choose>
			</xsl:variable>

			<xsl:choose>
				<xsl:when test="$lArticleType = 1">
					<xsl:apply-templates select="." mode="RefinderLinksAdvanced"/>
				</xsl:when>
				<xsl:when test="$lArticleType = 2">
					<xsl:apply-templates select="." mode="RefinderLinksAdvanced"/>
				</xsl:when>
				<xsl:when test="$lArticleType = 3">
					<xsl:apply-templates select="." mode="RefinderLinksSimple"/>
				</xsl:when>
				<xsl:when test="$lArticleType = 4">
					<xsl:apply-templates select="." mode="RefinderLinksSimple"/>
				</xsl:when>
				<xsl:when test="$lArticleType = 5">
					<xsl:apply-templates select="." mode="RefinderLinksSimple"/>
				</xsl:when>
				<xsl:when test="$lArticleType = 6">
					<xsl:apply-templates select="." mode="RefinderLinksSimple"/>
				</xsl:when>
				<xsl:when test="$lArticleType = 7">
					<xsl:apply-templates select="." mode="RefinderLinksSimple"/>
				</xsl:when>
			</xsl:choose>
		</xsl:template>

		<xsl:template match="*" mode="RefinderLinksSimple">
				<div class="refinder-link-holder">
					<a class="refinder-link" target="_blank">
						<xsl:attribute name="href">
							<xsl:text>http://dev.refinder.org/?search=simple</xsl:text>
							<xsl:text>&amp;text=</xsl:text>
							<xsl:apply-templates select="." mode="articleBack"/>
						</xsl:attribute>
						Search via ReFinder
					</a>
				</div>
		</xsl:template>

		<xsl:template match="*" mode="RefinderLinksAdvanced">
			<xsl:variable name="lArticleType">
				<xsl:choose>
					<xsl:when test="./*[@object_id='97']/*[@object_id='102']">1</xsl:when> <!-- Journal article -->
					<xsl:when test="./*[@object_id='97']/*[@object_id='98']">2</xsl:when> <!-- Book -->
					<xsl:when test="./*[@object_id='97']/*[@object_id='99']">3</xsl:when> <!-- Book chapter -->
					<xsl:when test="./*[@object_id='97']/*[@object_id='103']">4</xsl:when> <!-- Conference paper -->
					<xsl:when test="./*[@object_id='97']/*[@object_id='105']">5</xsl:when> <!-- Conference proceedings -->
					<xsl:when test="./*[@object_id='97']/*[@object_id='106']">6</xsl:when> <!-- Thesis -->
					<xsl:when test="./*[@object_id='97']/*[@object_id='107']">7</xsl:when> <!-- Software reference -->
					<xsl:when test="./*[@object_id='97']/*[@object_id='108']">8</xsl:when> <!-- Website reference -->
				</xsl:choose>
			</xsl:variable>

			<xsl:variable name="lAuthorshipType">
					<xsl:choose>
						<xsl:when test="count(.//*[@object_id='92']) &gt; 0">
							<xsl:value-of select=".//*[@object_id='92']/fields/*[@id='265']/value/@value_id"></xsl:value-of>
						</xsl:when>
						<xsl:when test="count(.//*[@object_id='100']) &gt; 0">
							<xsl:value-of select="//*[@object_id='100']/fields/*[@id='281']/value/@value_id"></xsl:value-of>
						</xsl:when>
						<xsl:when test="count(.//*[@object_id='101']) &gt; 0">
							<xsl:value-of select=".//*[@object_id='101']/fields/*[@id='282']/value/@value_id"></xsl:value-of>
						</xsl:when>
					</xsl:choose>
				</xsl:variable>

				<xsl:variable name="Authors">
					<xsl:for-each select=".//*[@object_id='92' or @object_id='100' or @object_id='101']/*[@object_id='90']">
						<xsl:choose>
							<xsl:when test="$lAuthorshipType = 3">
								<xsl:apply-templates select="." mode="processSingleReferenceAuthorLastName" />
							</xsl:when>
							<xsl:otherwise>
								<xsl:apply-templates select="." mode="processSingleReferenceAuthorFullNames" />
							</xsl:otherwise>
						</xsl:choose>
						<xsl:if test="position()!=last()"><xsl:text>, </xsl:text></xsl:if>
					</xsl:for-each>
				</xsl:variable>

				<xsl:variable name="refTitle">
					<xsl:apply-templates select=".//fields/*[@id='276' or @id='255']/value" mode="formatting_nospace"/>
				</xsl:variable>

				<xsl:variable name="refYear">
					<xsl:apply-templates select=".//fields/*[@id='254']/value" mode="formatting_nospace"/>
				</xsl:variable>

				<xsl:variable name="journal">
					<xsl:apply-templates select=".//fields/*[@id='243']/value" mode="formatting_nospace"/>
				</xsl:variable>

				<xsl:variable name="refType">
					<xsl:if test="$lArticleType = 1">
						<xsl:text>article</xsl:text></xsl:if>
					<xsl:if test="$lArticleType = 2"><xsl:text>book</xsl:text></xsl:if>
				</xsl:variable>

				<div class="refinder-link-holder">
					<a class="refinder-link" target="_blank">
						<xsl:attribute name="href">
							<xsl:text>http://dev.refinder.org/?search=advanced</xsl:text>
							<xsl:text>&amp;author=</xsl:text><xsl:value-of select="normalize-space($Authors)" />
							<xsl:text>&amp;year=</xsl:text><xsl:value-of select="normalize-space($refYear)" />
							<xsl:text>&amp;title=</xsl:text><xsl:value-of select="normalize-space($refTitle)" />
							<xsl:text>&amp;origin=</xsl:text><xsl:value-of select="normalize-space($journal)" />
							<xsl:text>&amp;refType=</xsl:text><xsl:value-of select="$refType" />
						</xsl:attribute>
						Search via ReFinder
					</a>
				</div>

			</xsl:template>


		<xsl:template match="*" mode="processSingleReferenceAuthorFullNames">
			<xsl:variable name="lAuthorParsedName">
				<!-- First name
				<xsl:value-of select="./fields/*[@id='251']/value"></xsl:value-of>
				<xsl:text> </xsl:text> -->
				<!-- Last name -->
				<xsl:value-of select="./fields/*[@id='252']/value"></xsl:value-of>
			</xsl:variable>
			<span>
				<xsl:attribute name="instance_id"><xsl:value-of select="./@instance_id" /></xsl:attribute>
				<xsl:value-of select="normalize-space($lAuthorParsedName)"></xsl:value-of>
			</span>
		</xsl:template>

		<xsl:template match="*" mode="processSingleReferenceAuthorFullNamesJSON">
			<xsl:variable name="lAuthorParsedName">
				<!-- First name -->
				<xsl:text>['</xsl:text>
				<xsl:value-of select="./fields/*[@id='251']/value"></xsl:value-of>
				<xsl:text>', '</xsl:text>
				<!-- Last name -->
				<xsl:value-of select="./fields/*[@id='252']/value"></xsl:value-of>
				<xsl:text>']</xsl:text>
			</xsl:variable>
			<span>
				<xsl:attribute name="instance_id"><xsl:value-of select="./@instance_id" /></xsl:attribute>
				<xsl:value-of select="normalize-space($lAuthorParsedName)"></xsl:value-of>
			</span>
		</xsl:template>


	<!-- Article of the future preview template of a single sup file -->
	<xsl:template match="*" mode="article_preview_sup_file">
		<div class="item-holder-RC">
			<xsl:apply-templates select="." mode="singleSupplementaryMaterialAOF" />
		</div>
		<!-- The node of the specific sup file -->
		<xsl:variable name="lCurrentNode" select="."></xsl:variable>
	</xsl:template>

	<!-- Article of the future SINGLE ELEMENT PREVIEWS END -->

	<!-- Article of the future LIST PREVIEWS START -->
	<!-- Article of the future preview template of the figures list -->
	<xsl:template match="*" mode="article_figures_list">
		<!-- The node of the figures holder -->
		<xsl:variable name="lCurrentNode" select="."></xsl:variable>

			<xsl:for-each select="//figure">
				<div class="item-holder-RC">
					<xsl:attribute name="rid"><xsl:value-of select="./@instance_id" /></xsl:attribute>
					<span class="fig-label-RC fig">
						<xsl:attribute name="rid"><xsl:value-of select="./@instance_id" /></xsl:attribute>
						<xsl:value-of select="./@display_name"></xsl:value-of>
							<xsl:text> </xsl:text>
						<xsl:value-of select="./fields/figure_number"></xsl:value-of>
					</span>
					<xsl:apply-templates select="image" mode="Figures" />
					<xsl:apply-templates select="multiple_images_plate" mode="Figures" />
					<xsl:apply-templates select="video" mode="Video" />
				</div>
			<xsl:if test="position()!=last()">
				<div class="P-Clear" />
			</xsl:if>
			</xsl:for-each>
	</xsl:template>
	
	<xsl:template match="video" mode="Video">
		<div class="P-Picture-Holder">
			<div class="singlefigmini fig">
				<xsl:attribute name="rid"><xsl:value-of select="../@instance_id" /></xsl:attribute>
				<img alt="" width="96" height="72">
					<xsl:attribute name="src">http://i1.ytimg.com/vi/<xsl:value-of select="php:function('getYouTubeId', string(./fields/*[@id='486']/value))"/>/default.jpg</xsl:attribute>
				</img>
			</div>
		</div>
		<div class="list-caption">
			<xsl:apply-templates select="./fields/video_caption/value" mode="formatting"/>
		</div>
		<div class="P-Clear" />
	</xsl:template>
	
	<xsl:template match="image" mode="Figures">
		<div class="P-Picture-Holder">
			<div class="singlefigmini fig">
				<xsl:attribute name="rid"><xsl:value-of select="../@instance_id" /></xsl:attribute>
				<img alt="">
					<xsl:attribute name="src"><xsl:value-of select="$pSiteUrl"/>/showfigure.php?filename=singlefigmini_<xsl:value-of select="./fields/photo_select/value"></xsl:value-of>.jpg</xsl:attribute>
				</img>
			</div>
		</div>
		<div class="list-caption">
			<xsl:apply-templates select="./fields/figure_caption/value" mode="formatting"/>
		</div>
		<div class="P-Clear" />
	</xsl:template>

		<xsl:template match="multiple_images_plate" mode="Figures">
				<div class="P-Picture-Holder">
					<xsl:apply-templates select="plate_type_wrapper/*[@object_id='231']" mode="singleFigSmallPreview" />
					<xsl:apply-templates select="plate_type_wrapper/*[@object_id='232']" mode="singleFigSmallPreview" />
					<xsl:apply-templates select="plate_type_wrapper/*[@object_id='233']" mode="singleFigSmallPreview" />
					<xsl:apply-templates select="plate_type_wrapper/*[@object_id='234']" mode="singleFigSmallPreview" />
				</div>
				<div class="list-caption">
					<xsl:apply-templates select="./fields/plate_caption/value" mode="formatting"/>
					<xsl:for-each select="./plate_type_wrapper/*/*/fields">
						<span class="list-caption-letter">
							<xsl:choose>
								<xsl:when test="../@object_id='225'"> a</xsl:when>
								<xsl:when test="../@object_id='226'"> b</xsl:when>
								<xsl:when test="../@object_id='227'"> c</xsl:when>
								<xsl:when test="../@object_id='228'"> d</xsl:when>
								<xsl:when test="../@object_id='229'"> e</xsl:when>
								<xsl:when test="../@object_id='230'"> f</xsl:when>
							</xsl:choose>
							<xsl:text>: </xsl:text>
						</span>
							<xsl:apply-templates select="./plate_desc" mode="formatting"/>
					</xsl:for-each>
				</div>
				<div class="P-Clear" />
		</xsl:template>

	<!-- Article of the future preview template of the tables list -->
	<xsl:template match="*" mode="article_tables_list">
		<xsl:variable name="lCurrentNode" select="."></xsl:variable>
		<!-- The node of the tables holder -->
		<xsl:for-each select="table">
			<div class="item-holder-RC table">
			<!-- <xsl:attribute name="rid"><xsl:value-of select="@instance_id"></xsl:value-of></xsl:attribute> -->
				<div class="P-table-tump-holder table">
					<xsl:attribute name="rid"><xsl:value-of select="@instance_id"></xsl:value-of></xsl:attribute>
					<img width="60" heigth="48" alt="">
						<xsl:attribute name="src"><xsl:value-of select="$pSiteUrl"/>/i/table_pic-60.png</xsl:attribute>
					</img>
				</div>
				<span class="fig-label-RC table">
					<xsl:attribute name="rid"><xsl:value-of select="@instance_id"></xsl:value-of></xsl:attribute>
					Table <xsl:value-of select="position()"></xsl:value-of>.

					<span class="downloadmaterials">
						<a class="download-table-link">
							<xsl:attribute name="href">
								<xsl:value-of select="$pSiteUrl" />
								<xsl:text>/lib/ajax_srv/csv_export_srv.php?action=export_table_as_csv&amp;instance_id=</xsl:text>
								<xsl:value-of select="./@instance_id" />
							</xsl:attribute>
							<xsl:text>Download as CSV&#160;</xsl:text>
							<img width="22" heigth="22" alt="" title="Download table">
								<xsl:attribute name="src"><xsl:value-of select="$pSiteUrl"/>/i/download_materials_icon.png</xsl:attribute>
							</img>
						</a>
					</span>
				</span>


			<!--		<span class="fig-label-RC">
						<xsl:value-of select="./@display_name"></xsl:value-of>
							<xsl:text> </xsl:text>
						<xsl:value-of select="position()"></xsl:value-of>
		</span> -->

				<div class="list-caption">
					<xsl:apply-templates select="./fields/table_caption/value" mode="formatting"/>
				</div>
			</div>
		</xsl:for-each>

	</xsl:template>

	<!-- Article of the future preview template of the references list -->
	<xsl:template match="*" mode="article_references_list">
		<div class="AOF-ref-list">
			<xsl:for-each select="*[@object_id='95']">
				<div class="ref-list-AOF-holder-holder">
					<xsl:apply-templates select="." mode="articleBack"/>					
					<xsl:call-template name="AOF-Place-Cited-Element-Navigation">
					    <xsl:with-param name="pInstanceId" select="@instance_id"></xsl:with-param>
					</xsl:call-template>
					<xsl:apply-templates select="." mode="RefinderLinks"/>
				</div>
				
			</xsl:for-each>
		</div>		
	</xsl:template>

	<!-- Article of the future preview template of the sup files list -->
	<xsl:template match="*" mode="article_sup_files_list">
		<div class="suppl-list-AOF">
			<div class="data-help"><img width="24" height="24" style="float: left; margin: -4px 4px 0 0" alt="Note:" src="/i/lightbulb.png" /><span>Tables and Figures, if present, can be downloaded from the article.</span></div>

			<xsl:if test="count(//*[@object_id='37']) > 0">
				<div class="DwC">
					<a class="download-table-link">
						<xsl:attribute name="href">
							<xsl:value-of select="$pSiteUrl" />
							<xsl:text>/lib/ajax_srv/csv_export_srv.php?action=export_materials_as_csv&amp;document_id=</xsl:text>
							<xsl:value-of select="$pDocumentId" />
						</xsl:attribute>
						<img width="18" heigth="18" alt="" style="vertical-align: top;">
							<xsl:attribute name="src"><xsl:value-of select="$pSiteUrl"/>/i/download-icon-small-18.png</xsl:attribute>
						</img>
						Download all occurrences as Darwin Core Archive
					</a>
				</div>
			</xsl:if>

			<xsl:for-each select="//*[@object_id='55']">
				<div class="item-holder-RC suppl">
					<xsl:apply-templates select="." mode="singleSupplementaryMaterialAOF" />
				</div>
			</xsl:for-each>
		</div>
		<!-- The node of the sup files holder -->
		<xsl:variable name="lCurrentNode" select="."></xsl:variable>
	</xsl:template>

	<xsl:template match="*" mode="singleSupplementaryMaterialAOF">
		<xsl:variable name="instance" select="./@instance_id" />

		<xsl:if test="./fields/*[@id='214']/value != ''">
				<span class="fig-label-RC suppl">
					<xsl:attribute name="rid"><xsl:value-of select="./@instance_id"></xsl:value-of></xsl:attribute>
					<xsl:text>Supplementary material </xsl:text>
					<xsl:for-each select="../*[@object_id='55']">
						<xsl:if test="./@instance_id = $instance">
							<xsl:value-of select="position()" />
						</xsl:if>
					</xsl:for-each>
				</span>
				<div class="Supplemantary-File-Title">
					<xsl:attribute name="instance_id"><xsl:value-of select="./@instance_id" /></xsl:attribute>
					<xsl:apply-templates select="./fields/*[@id='214']/value" mode="formatting"/>
				</div>
		</xsl:if>

		<xsl:if test="./fields/*[@id='215']/value != '' or ./fields/*[@id='216']/value != '' or ./fields/*[@id='217']/value != '' or ./fields/*[@id='222']/value != ''">
			<div class="suppl-section-holder">
				<xsl:if test="./fields/*[@id='215']/value != ''">
					<div class="myfieldHolder">
						<span class="fieldLabel">
							<xsl:value-of select="./fields/*[@id='215']/@field_name" />:&#160;</span>
						<span class="fieldValue" field_id="215">
							<xsl:attribute name="instance_id"><xsl:value-of select="./@instance_id" /></xsl:attribute>
							<xsl:apply-templates select="./fields/*[@id='215']/value" mode="formatting"/>
						</span>
					</div>
				</xsl:if>
				<xsl:if test="./fields/*[@id='216']/value != ''">
					<div class="myfieldHolder">
						<span class="fieldLabel">
							<xsl:value-of select="./fields/*[@id='216']/@field_name" />:&#160;</span>
						<span class="fieldValue" field_id="216">
							<xsl:attribute name="instance_id"><xsl:value-of select="./@instance_id" /></xsl:attribute>
							<xsl:apply-templates select="./fields/*[@id='216']/value" mode="formatting"/>
						</span>
					</div>
				</xsl:if>
				<xsl:if test="./fields/*[@id='217']/value != ''">
					<div class="myfieldHolder">
						<span class="fieldLabel"><xsl:value-of select="./fields/*[@id='217']/@field_name" />:&#160;</span>
						<div class="fieldValue" field_id="217">
							<xsl:attribute name="instance_id"><xsl:value-of select="./@instance_id" /></xsl:attribute>
							<xsl:apply-templates select="./fields/*[@id='217']/value" mode="formatting"/>
						</div>
					</div>
				</xsl:if>
				<xsl:if test="./fields/*[@id='222']/value != ''">
					<div class="myfieldHolder">
						<xsl:attribute name="field_id"><xsl:value-of select="./fields/file/@id" /></xsl:attribute>
						<xsl:apply-templates select="./fields/file/value" mode="formatting_uploaded_file">
							<xsl:with-param name="lFileName" select="php:function('getFileNameById', string(./fields/file/value))"></xsl:with-param>
							<xsl:with-param name="lUploadedFileName" select="php:function('getUploadedFileNameById', string(./fields/file/value))" />
							<xsl:with-param name="lSupplFileInstanceId" select="./@instance_id"></xsl:with-param>
						</xsl:apply-templates>
					</div>
				</xsl:if>
			</div>
		</xsl:if>
	</xsl:template>

	<!-- Article of the future LIST PREVIEWS END -->
		<xsl:template match="*" mode="RefinderFormat">
		<div id="style-choser">
			<div id="format-head">Format via ReFinder</div>
			<select class="chosen-select" id="chosen-select" onchange="callFormattingService()" data-placeholder="-- select a citation style --">
				<option selected="selected" disabled="disabled" style="display: none">-- select a citation style --</option>
				<option>Academy of management review</option>
				<option>Acm sigchi proceedings</option>
				<option>Acm siggraph</option>
				<option>Acm sig proceedings</option>
				<option>Acm sig proceedings long author list</option>
				<option>Acs chemical biology</option>
				<option>Acs nano</option>
				<option>Acta materialia</option>
				<option>Acta naturae</option>
				<option>Acta neurochirurgica</option>
				<option>Acta ophthalmologica</option>
				<option>Acta palaeontologica polonica</option>
				<option>Acta pharmaceutica</option>
				<option>Acta polytechnica</option>
				<option>Acta societatis botanicorum poloniae</option>
				<option>Acta universitatis agriculturae sueciae</option>
				<option>Administrative science quarterly</option>
				<option>Advanced engineering materials</option>
				<option>Advanced functional materials</option>
				<option>Advanced materials</option>
				<option>Advances in complex systems</option>
				<option>African zoology</option>
				<option>Aging cell</option>
				<option>Aids</option>
				<option>Allergy</option>
				<option>Alternatives to animal experimentation</option>
				<option>American anthropological association</option>
				<option>American association for cancer research</option>
				<option>American association of petroleum geologists</option>
				<option>American chemical society</option>
				<option>American chemical society with titles</option>
				<option>American chemical society with titles brackets</option>
				<option>American geophysical union</option>
				<option>American heart association</option>
				<option>American institute of aeronautics and astronautics</option>
				<option>American institute of physics</option>
				<option>American journal of agricultural economics</option>
				<option>American journal of archaeology</option>
				<option>American journal of botany</option>
				<option>American journal of epidemiology</option>
				<option>American journal of human genetics</option>
				<option>American journal of medical genetics</option>
				<option>American journal of neuroradiology</option>
				<option>American journal of orthodontics and dentofacial orthopedics</option>
				<option>American journal of physical anthropology</option>
				<option>American journal of political science</option>
				<option>American journal of respiratory and critical care medicine</option>
				<option>American medical association</option>
				<option>American medical association alphabetical</option>
				<option>American medical association no et al</option>
				<option>American medical association no url</option>
				<option>American meteorological society</option>
				<option>American physics society</option>
				<option>American physiological society</option>
				<option>American phytopathological society</option>
				<option>American phytopathological society numeric</option>
				<option>American political science association</option>
				<option>American society for microbiology</option>
				<option>American society of civil engineers</option>
				<option>American society of mechanical engineers</option>
				<option>American sociological association</option>
				<option>American veterinary medical association</option>
				<option>Analytica chimica acta</option>
				<option>Anesthesia and analgesia</option>
				<option>Anesthesiology</option>
				<option>Angewandte chemie</option>
				<option>Animal behaviour</option>
				<option>Annalen des naturhistorischen museums in wien</option>
				<option>Annales</option>
				<option>Annals of biomedical engineering</option>
				<option>Annals of botany</option>
				<option>Annals of neurology</option>
				<option>Annals of oncology</option>
				<option>Annals of the association of american geographers</option>
				<option>Annual review of astronomy and astrophysics</option>
				<option>Annual review of medicine</option>
				<option>Annual review of nuclear and particle science</option>
				<option>Annual reviews</option>
				<option>Annual reviews alphabetical</option>
				<option>Annual reviews author date</option>
				<option>Annual reviews without titles</option>
				<option>Antarctic science</option>
				<option>Apa</option>
				<option>Apa 5th edition</option>
				<option>Apa annotated bibliography</option>
				<option>Apa cv</option>
				<option>Apa no doi no issue</option>
				<option>Apa tr</option>
				<option>Applied spectroscopy</option>
				<option>Aquatic conservation</option>
				<option>Aquatic living resources</option>
				<option>Archives of physical medicine and rehabilitation</option>
				<option>Arthritis and rheumatism</option>
				<option>Arzneimitteltherapie</option>
				<option>Asa cssa sssa</option>
				<option>Asian studies review</option>
				<option>Associacao brasileira de normas tecnicas</option>
				<option>Associacao brasileira de normas tecnicas ipea</option>
				<option>Associacao brasileira de normas tecnicas note</option>
				<option>Associacao brasileira de normas tecnicas ufmg face full</option>
				<option>Associacao brasileira de normas tecnicas ufmg face initials</option>
				<option>Associacao brasileira de normas tecnicas ufpr</option>
				<option>Associacao nacional de pesquisa e ensino em transportes</option>
				<option>Association for computing machinery</option>
				<option>Ausonius editions</option>
				<option>Austral ecology</option>
				<option>Australian guide to legal citation</option>
				<option>Australian journal of earth sciences</option>
				<option>Australian journal of grape and wine research</option>
				<option>Austrian legal</option>
				<option>Avian diseases</option>
				<option>Avian pathology</option>
				<option>Aviation space and environmental medicine</option>
				<option>Basic and applied ecology</option>
				<option>Bibtex</option>
				<option>Biochemical journal</option>
				<option>Biochemistry</option>
				<option>Biochimica et biophysica acta</option>
				<option>Bioconjugate chemistry</option>
				<option>Bioelectromagnetics</option>
				<option>Bioessays</option>
				<option>Bioinformatics</option>
				<option>Biological journal of the linnean society</option>
				<option>Biological psychiatry</option>
				<option>Biological reviews</option>
				<option>Biomed central</option>
				<option>Bioorganic and medicinal chemistry letters</option>
				<option>Biophysical journal</option>
				<option>Bioresource technology</option>
				<option>Biotechniques</option>
				<option>Biotechnology advances</option>
				<option>Biotechnology and bioengineering</option>
				<option>Biotropica</option>
				<option>Blood</option>
				<option>Bluebook2</option>
				<option>Bluebook inline</option>
				<option>Bluebook law review</option>
				<option>Bmc bioinformatics</option>
				<option>Bmj</option>
				<option>Body and society</option>
				<option>Bone</option>
				<option>Bone marrow transplantation</option>
				<option>Boreal environment research</option>
				<option>Brain</option>
				<option>Brazilian journal of botany</option>
				<option>Briefings in bioinformatics</option>
				<option>British ecological society</option>
				<option>British journal of anaesthesia</option>
				<option>British journal of cancer</option>
				<option>British journal of haematology</option>
				<option>British journal of industrial relations</option>
				<option>British journal of pharmacology</option>
				<option>British journal of political science</option>
				<option>Building structure</option>
				<option>Bulletin de la societe prehistorique francaise</option>
				<option>Bulletin of marine science</option>
				<option>Byzantina symmeikta</option>
				<option>Canadian journal of dietetic practice and research</option>
				<option>Canadian journal of fisheries and aquatic sciences</option>
				<option>Catholic biblical association</option>
				<option>Cell</option>
				<option>Cell calcium</option>
				<option>Cell numeric</option>
				<option>Cell research</option>
				<option>Cell transplantation</option>
				<option>Cellular and molecular bioengineering</option>
				<option>Cellular reprogramming</option>
				<option>Centaurus</option>
				<option>Cerebral cortex</option>
				<option>Chemical research in toxicology</option>
				<option>Chemical reviews</option>
				<option>Chemical senses</option>
				<option>Chest</option>
				<option>Chicago annotated bibliography</option>
				<option>Chicago author date</option>
				<option>Chicago author date basque</option>
				<option>Chicago author date de</option>
				<option>Chicago figures</option>
				<option>Chicago fullnote bibliography</option>
				<option>Chicago fullnote bibliography no ibid</option>
				<option>Chicago library list</option>
				<option>Chicago note bibliography</option>
				<option>Chicago note biblio no ibid</option>
				<option>Chinese gb7714 1987 numeric</option>
				<option>Chinese gb7714 2005 numeric</option>
				<option>Circulation</option>
				<option>Cities</option>
				<option>Clinical cancer research</option>
				<option>Clinical infectious diseases</option>
				<option>Clinical neurophysiology</option>
				<option>Clinical orthopaedics and related research</option>
				<option>Clinical otolaryngology</option>
				<option>Clinical pharmacology and therapeutics</option>
				<option>Clio medica</option>
				<option>Cns and neurological disorders drug targets</option>
				<option>Cold spring harbor laboratory press</option>
				<option>Comision economica para america latina y el caribe</option>
				<option>Conservation biology</option>
				<option>Conservation letters</option>
				<option>Copernicus publications</option>
				<option>Coral reefs</option>
				<option>Cortex</option>
				<option>Council of science editors</option>
				<option>Council of science editors author date</option>
				<option>Critical care medicine</option>
				<option>Cuadernos de filologia clasica</option>
				<option>Culture medicine and psychiatry</option>
				<option>Current opinion</option>
				<option>Current protocols</option>
				<option>Currents in biblical research</option>
				<option>Cytometry</option>
				<option>De buck</option>
				<option>Decision sciences</option>
				<option>Dendrochronologia</option>
				<option>Deutsche gesellschaft fur psychologie</option>
				<option>Digestive and liver disease</option>
				<option>Din 1505 2</option>
				<option>Din 1505 2 alphanumeric</option>
				<option>Din 1505 2 numeric</option>
				<option>Din 1505 2 numeric alphabetical</option>
				<option>Diplo</option>
				<option>Disability and rehabilitation</option>
				<option>Drug development research</option>
				<option>Drugs of today</option>
				<option>Ear and hearing</option>
				<option>Early medieval europe</option>
				<option>Earth surface processes and landforms</option>
				<option>Ecological entomology</option>
				<option>Ecology</option>
				<option>Ecology letters</option>
				<option>Economic commission for latin america and the caribbean</option>
				<option>Economie et statistique</option>
				<option>Ecoscience</option>
				<option>Ecosystems</option>
				<option>El profesional de la informacion</option>
				<option>Elsevier harvard</option>
				<option>Elsevier harvard2</option>
				<option>Elsevier harvard without titles</option>
				<option>Elsevier vancouver</option>
				<option>Elsevier without titles</option>
				<option>Elsevier with titles</option>
				<option>Elsevier with titles alphabetical</option>
				<option>Embo reports</option>
				<option>Emerald harvard</option>
				<option>Emu austral ornithology</option>
				<option>Energy policy</option>
				<option>Entomologia experimentalis et applicata</option>
				<option>Entomological society of america</option>
				<option>Environmental and engineering geoscience</option>
				<option>Environmental and experimental botany</option>
				<option>Environmental conservation</option>
				<option>Environmental health perspectives</option>
				<option>Environmental microbiology</option>
				<option>Environmental toxicology and chemistry</option>
				<option>Environment and planning</option>
				<option>Epidemiologie et sante animale</option>
				<option>Equine veterinary education</option>
				<option>Ergoscience</option>
				<option>Ethics book reviews</option>
				<option>Ethnobiology and conservation</option>
				<option>European cells and materials</option>
				<option>European journal of clinical microbiology and infectious diseases</option>
				<option>European journal of emergency medicine</option>
				<option>European journal of immunology</option>
				<option>European journal of information systems</option>
				<option>European journal of neuroscience</option>
				<option>European journal of ophthalmology</option>
				<option>European journal of radiology</option>
				<option>European journal of soil science</option>
				<option>European respiratory journal</option>
				<option>European retail research</option>
				<option>European society of cardiology</option>
				<option>European union interinstitutional style guide</option>
				<option>Evolution</option>
				<option>Evolution and development</option>
				<option>Evolutionary anthropology</option>
				<option>Experimental eye research</option>
				<option>Eye</option>
				<option>Fachhochschule vorarlberg</option>
				<option>Federation of european microbiological societies</option>
				<option>Fertility and sterility</option>
				<option>First monday</option>
				<option>Fish and fisheries</option>
				<option>Flavour and fragrance journal</option>
				<option>Foerster geisteswissenschaft</option>
				<option>Fold and r</option>
				<option>Free radical biology and medicine</option>
				<option>French1</option>
				<option>French2</option>
				<option>French3</option>
				<option>French4</option>
				<option>French politics</option>
				<option>Freshwater biology</option>
				<option>Frontiers</option>
				<option>Frontiers in optics</option>
				<option>Fungal ecology</option>
				<option>Future science group</option>
				<option>G3</option>
				<option>Gallia</option>
				<option>Gastroenterology</option>
				<option>Geistes und kulturwissenschaften teilmann</option>
				<option>Geneses</option>
				<option>Genetics</option>
				<option>Genome biology and evolution</option>
				<option>Geoarchaeology</option>
				<option>Geochimica et cosmochimica acta</option>
				<option>Geoderma</option>
				<option>Geografie sbornik cgs</option>
				<option>Geological magazine</option>
				<option>Geology</option>
				<option>Geopolitics</option>
				<option>Georg august universitat gottingen institut fur ethnologie und ethnologische sammlung</option>
				<option>Global change biology</option>
				<option>Global ecology and biogeography</option>
				<option>Gost r 7 0 5 2008</option>
				<option>Gost r 7 0 5 2008 numeric</option>
				<option>Hamburg school of food science</option>
				<option>Hand</option>
				<option>Harvard1</option>
				<option>Harvard7de</option>
				<option>Harvard anglia ruskin university</option>
				<option>Harvard cardiff university</option>
				<option>Harvard coventry university</option>
				<option>Harvard durham university business school</option>
				<option>Harvard european archaeology</option>
				<option>Harvard gesellschaft fur bildung und forschung in europa</option>
				<option>Harvard imperial college london</option>
				<option>Harvard institut fur praxisforschung de</option>
				<option>Harvard kings college london</option>
				<option>Harvard leeds metropolitan university</option>
				<option>Harvard limerick</option>
				<option>Harvard manchester business school</option>
				<option>Harvard north west university</option>
				<option>Harvard oxford brookes university</option>
				<option>Harvard oxford brookes university faculty of health and life sciences</option>
				<option>Harvard staffordshire university</option>
				<option>Harvard swinburne university of technology</option>
				<option>Harvard the university of melbourne</option>
				<option>Harvard the university of northampton</option>
				<option>Harvard the university of sheffield school of east asian studies</option>
				<option>Harvard the university of sheffield town and regional planning</option>
				<option>Harvard university of abertay dundee</option>
				<option>Harvard university of birmingham</option>
				<option>Harvard university of gloucestershire</option>
				<option>Harvard university of greenwich</option>
				<option>Harvard university of leeds</option>
				<option>Harvard university of sunderland</option>
				<option>Harvard university of the west of england</option>
				<option>Harvard university of west london</option>
				<option>Harvard university of wolverhampton</option>
				<option>Hawaii international conference on system sciences proceedings</option>
				<option>Health services research</option>
				<option>Heart rhythm</option>
				<option>Hepatology</option>
				<option>Heredity</option>
				<option>Histoire at politique</option>
				<option>Histoire et mesure</option>
				<option>History and theory</option>
				<option>History of the human sciences</option>
				<option>Hochschule fur wirtschaft und recht berlin</option>
				<option>Hong kong journal of radiology</option>
				<option>Human mutation</option>
				<option>Human reproduction</option>
				<option>Human reproduction update</option>
				<option>Human resource management journal</option>
				<option>Hydrobiologia</option>
				<option>Hydrological sciences journal</option>
				<option>Hypotheses in the life sciences</option>
				<option>Ices journal of marine science</option>
				<option>Ieee</option>
				<option>Ieee with url</option>
				<option>Iica catie</option>
				<option>Immunological reviews</option>
				<option>Inflammatory bowel diseases</option>
				<option>Infoclio de</option>
				<option>Infoclio fr nocaps</option>
				<option>Infoclio fr smallcaps</option>
				<option>Information systems research</option>
				<option>Insectes sociaux</option>
				<option>Institute of physics harvard</option>
				<option>Institute of physics numeric</option>
				<option>International journal of audiology</option>
				<option>International journal of cancer</option>
				<option>International journal of epidemiology</option>
				<option>International journal of exercise science</option>
				<option>International journal of humanoid robotics</option>
				<option>International journal of lexicography</option>
				<option>International journal of occupational medicine and environmental health</option>
				<option>International journal of production economics</option>
				<option>International journal of radiation oncology biology physics</option>
				<option>International journal of solids and structures</option>
				<option>International journal of sports medicine</option>
				<option>International journal of wildland fire</option>
				<option>International labour organization</option>
				<option>International microbiology</option>
				<option>International organization</option>
				<option>International pig veterinary society congress proceedings</option>
				<option>International studies association</option>
				<option>International union of crystallography</option>
				<option>Inter research science center</option>
				<option>Inter ro</option>
				<option>Investigative radiology</option>
				<option>Invisu</option>
				<option>Irish historical studies</option>
				<option>Iso690 author date cs</option>
				<option>Iso690 author date en</option>
				<option>Iso690 author date fr</option>
				<option>Iso690 author date fr no abstract</option>
				<option>Iso690 full note sk</option>
				<option>Iso690 note cs</option>
				<option>Iso690 numeric brackets cs</option>
				<option>Iso690 numeric cs</option>
				<option>Iso690 numeric en</option>
				<option>Iso690 numeric fr</option>
				<option>Iso690 numeric lt</option>
				<option>Iso690 numeric sk</option>
				<option>Jahrbuch fur evangelikale theologie</option>
				<option>Javnost the public</option>
				<option>Journalistica</option>
				<option>Journal of alzheimers disease</option>
				<option>Journal of animal physiology and animal nutrition</option>
				<option>Journal of antimicrobial chemotherapy</option>
				<option>Journal of applied animal science</option>
				<option>Journal of applied ecology</option>
				<option>Journal of applied philosophy</option>
				<option>Journal of archaeological research</option>
				<option>Journal of atrial fibrillation</option>
				<option>Journal of basic microbiology</option>
				<option>Journal of biogeography</option>
				<option>Journal of biological chemistry</option>
				<option>Journal of biomedical materials research part a</option>
				<option>Journal of bone and mineral research</option>
				<option>Journal of chemical ecology</option>
				<option>Journal of chemistry and chemical engineering</option>
				<option>Journal of clinical oncology</option>
				<option>Journal of combinatorics</option>
				<option>Journal of computational chemistry</option>
				<option>Journal of dental research</option>
				<option>Journal of elections public opinion and parties</option>
				<option>Journal of evolutionary biology</option>
				<option>Journal of experimental botany</option>
				<option>Journal of field ornithology</option>
				<option>Journal of finance</option>
				<option>Journal of financial economics</option>
				<option>Journal of fish diseases</option>
				<option>Journal of food protection</option>
				<option>Journal of forensic sciences</option>
				<option>Journal of health economics</option>
				<option>Journal of hearing science</option>
				<option>Journal of hepatology</option>
				<option>Journal of hypertension</option>
				<option>Journal of industrial ecology</option>
				<option>Journal of infectious diseases</option>
				<option>Journal of information technology</option>
				<option>Journal of integrated omics</option>
				<option>Journal of investigative dermatology</option>
				<option>Journal of lipid research</option>
				<option>Journal of mammalogy</option>
				<option>Journal of management</option>
				<option>Journal of management information systems</option>
				<option>Journal of marketing</option>
				<option>Journal of medical genetics</option>
				<option>Journal of medical internet research</option>
				<option>Journal of molecular biology</option>
				<option>Journal of molecular endocrinology</option>
				<option>Journal of morphology</option>
				<option>Journal of neurophysiology</option>
				<option>Journal of neurosurgery</option>
				<option>Journal of neurotrauma</option>
				<option>Journal of oral and maxillofacial surgery</option>
				<option>Journal of orthopaedic research</option>
				<option>Journal of orthopaedic trauma</option>
				<option>Journal of paleontology</option>
				<option>Journal of perinatal medicine</option>
				<option>Journal of petrology</option>
				<option>Journal of pollination ecology</option>
				<option>Journal of pragmatics</option>
				<option>Journal of psychiatric and mental health nursing</option>
				<option>Journal of psychiatry and neuroscience</option>
				<option>Journal of roman archaeology a</option>
				<option>Journal of roman archaeology b</option>
				<option>Journal of separation science</option>
				<option>Journal of shoulder and elbow surgery</option>
				<option>Journal of simulation</option>
				<option>Journal of social archaeology</option>
				<option>Journal of spinal disorders and techniques</option>
				<option>Journal of studies on alcohol and drugs</option>
				<option>Journal of the academy of nutrition and dietetics</option>
				<option>Journal of the air and waste management association</option>
				<option>Journal of the american academy of orthopaedic surgeons</option>
				<option>Journal of the american association of laboratory animal science</option>
				<option>Journal of the american college of cardiology</option>
				<option>Journal of the american society of brewing chemists</option>
				<option>Journal of the american society of nephrology</option>
				<option>Journal of the american water resources association</option>
				<option>Journal of the brazilian chemical society</option>
				<option>Journal of the electrochemical society</option>
				<option>Journal of the royal anthropological institute</option>
				<option>Journal of thrombosis and haemostasis</option>
				<option>Journal of tropical ecology</option>
				<option>Journal of vegetation science</option>
				<option>Journal of vertebrate paleontology</option>
				<option>Journal of visualized experiments</option>
				<option>Journal of wildlife diseases</option>
				<option>Journal of zoology</option>
				<option>Juristische zitierweise</option>
				<option>Karger journals</option>
				<option>Karger journals author date</option>
				<option>Kidney international</option>
				<option>Kindheit und entwicklung</option>
				<option>Knee surgery sports traumatology arthroscopy</option>
				<option>Kolner zeitschrift fur soziologie und sozialpsychologie</option>
				<option>Korean journal of anesthesiology</option>
				<option>Kritische ausgabe</option>
				<option>Kth royal institute of technology school of computer science and communication</option>
				<option>Kth royal institute of technology school of computer science and communication sv</option>
				<option>Landes bioscience journals</option>
				<option>Language</option>
				<option>Language in society</option>
				<option>Le mouvement social</option>
				<option>Les journees de la recherche avicole</option>
				<option>Les journees de la recherche porcine</option>
				<option>Lethaia</option>
				<option>Lettres et sciences humaines fr</option>
				<option>Leviathan</option>
				<option>Limnology and oceanography</option>
				<option>Liver international</option>
				<option>Livestock science</option>
				<option>Macromolecular reaction engineering</option>
				<option>Magnetic resonance in medicine</option>
				<option>Mammal review</option>
				<option>Manchester university press</option>
				<option>Marine policy</option>
				<option>Mcgill guide v7</option>
				<option>Mcrj7</option>
				<option>Medecine sciences</option>
				<option>Media culture and society</option>
				<option>Medical history</option>
				<option>Medical physics</option>
				<option>Medicine and science in sports and exercise</option>
				<option>Melbourne school of theology</option>
				<option>Memorias do instituto oswaldo cruz</option>
				<option>Metallurgical and materials transactions</option>
				<option>Meteoritics and planetary science</option>
				<option>Methods in ecology and evolution</option>
				<option>Methods of information in medicine</option>
				<option>Metropolitiques</option>
				<option>Microbial drug resistance</option>
				<option>Microscopy and microanalysis</option>
				<option>Mis quarterly</option>
				<option>Modern humanities research association</option>
				<option>Modern humanities research association author date</option>
				<option>Modern language association</option>
				<option>Modern language association 6th edition note</option>
				<option>Modern language association underline</option>
				<option>Modern language association with url</option>
				<option>Mohr siebeck recht</option>
				<option>Molecular and biochemical parasitology</option>
				<option>Molecular and cellular proteomics</option>
				<option>Molecular biology and evolution</option>
				<option>Molecular biology of the cell</option>
				<option>Molecular ecology</option>
				<option>Molecular microbiology</option>
				<option>Molecular phylogenetics and evolution</option>
				<option>Molecular plant</option>
				<option>Molecular plant microbe interactions</option>
				<option>Molecular psychiatry</option>
				<option>Molecular psychiatry letters</option>
				<option>Molecular therapy</option>
				<option>Moore theological college</option>
				<option>Moorlands college</option>
				<option>Multidisciplinary digital publishing institute</option>
				<option>Multiple sclerosis journal</option>
				<option>Myrmecological news</option>
				<option>Nano biomedicine and engineering</option>
				<option>National archives of australia</option>
				<option>National library of medicine grant proposals</option>
				<option>National science foundation grant proposals</option>
				<option>Nature</option>
				<option>Nature neuroscience brief communications</option>
				<option>Nature no superscript</option>
				<option>Natureza e conservacao</option>
				<option>Navigation</option>
				<option>Neurology</option>
				<option>Neurology india</option>
				<option>Neuropsychologia</option>
				<option>Neuropsychopharmacology</option>
				<option>Neurorehabilitation and neural repair</option>
				<option>Neuroreport</option>
				<option>New phytologist</option>
				<option>New solutions</option>
				<option>New zealand plant protection</option>
				<option>New zealand veterinary journal</option>
				<option>Norma portuguesa 405</option>
				<option>Northeastern naturalist</option>
				<option>Nucleic acids research</option>
				<option>Obesity</option>
				<option>Occupational medicine</option>
				<option>Oikos</option>
				<option>Oncogene</option>
				<option>Ophthalmology</option>
				<option>Oral oncology</option>
				<option>Organic geochemistry</option>
				<option>Organization</option>
				<option>Organization science</option>
				<option>Ornitologia neotropical</option>
				<option>Oryx</option>
				<option>Oscola</option>
				<option>Oscola no ibid</option>
				<option>Osterreichische zeitschrift fur politikwissenschaft</option>
				<option>Owbarth verlag</option>
				<option>Oxford art journal</option>
				<option>Oxford centre for mission studies harvard</option>
				<option>Oxford studies on the roman economy</option>
				<option>Oxford the university of new south wales</option>
				<option>Padagogische hochschule heidelberg</option>
				<option>Pain</option>
				<option>Palaeontologia electronica</option>
				<option>Palaeontology</option>
				<option>Palaios</option>
				<option>Paleobiology</option>
				<option>Pediatric anesthesia</option>
				<option>Pediatric blood and cancer</option>
				<option>Pediatric research</option>
				<option>Permafrost and periglacial processes</option>
				<option>Philosophia scientiae</option>
				<option>Phyllomedusa</option>
				<option>Physiological and biochemical zoology</option>
				<option>Pisa university press</option>
				<option>Plant biology</option>
				<option>Plant physiology</option>
				<option>Plos</option>
				<option>Pm and r</option>
				<option>Pnas</option>
				<option>Polish legal</option>
				<option>Political studies</option>
				<option>Politische vierteljahresschrift</option>
				<option>Pontifical athenaeum regina apostolorum</option>
				<option>Pontifical biblical institute</option>
				<option>Poultry science</option>
				<option>Presses universitaires de rennes</option>
				<option>Proceedings of the royal society b</option>
				<option>Progress in retinal and eye research</option>
				<option>Proinflow</option>
				<option>Protein science</option>
				<option>Proteomics</option>
				<option>Psychiatry and clinical neurosciences</option>
				<option>Psychological medicine</option>
				<option>Public health nutrition</option>
				<option>Quaderni degli avogadro colloquia</option>
				<option>Quaternary research</option>
				<option>Radiographics</option>
				<option>Radiopaedia</option>
				<option>Research policy</option>
				<option>Resources conservation and recycling</option>
				<option>Revista argentina de antropologia biologica</option>
				<option>Revista de biologia tropical</option>
				<option>Revue archeologique</option>
				<option>Revue de medecine veterinaire</option>
				<option>Revue dhistoire moderne et contemporaine</option>
				<option>Rofo</option>
				<option>Romanian humanities</option>
				<option>Rose school</option>
				<option>Royal society of chemistry</option>
				<option>Rtf scan</option>
				<option>Sage harvard</option>
				<option>Sage vancouver</option>
				<option>Scandinavian journal of infectious diseases</option>
				<option>Scandinavian journal of work environment and health</option>
				<option>Scandinavian political studies</option>
				<option>Science</option>
				<option>Science of the total environment</option>
				<option>Science translational medicine</option>
				<option>Science without titles</option>
				<option>Seminars in pediatric neurology</option>
				<option>Sexual development</option>
				<option>Small</option>
				<option>Social science and medicine</option>
				<option>Social studies of science</option>
				<option>Sociedade brasileira de computacao</option>
				<option>Societe nationale des groupements techniques veterinaires</option>
				<option>Society for american archaeology</option>
				<option>Society for general microbiology</option>
				<option>Society for historical archaeology</option>
				<option>Society of biblical literature fullnote bibliography</option>
				<option>Socio economic review</option>
				<option>Soil biology and biochemistry</option>
				<option>Soziale welt</option>
				<option>Sozialpadagogisches institut berlin walter may</option>
				<option>Sozialwissenschaften teilmann</option>
				<option>Soziologie</option>
				<option>Spanish legal</option>
				<option>Spie bios</option>
				<option>Spie journals</option>
				<option>Spip cite</option>
				<option>Springer basic author date</option>
				<option>Springer basic author date no et al</option>
				<option>Springer basic brackets</option>
				<option>Springer basic brackets no et al</option>
				<option>Springer humanities author date</option>
				<option>Springer humanities brackets</option>
				<option>Springer lecture notes in computer science</option>
				<option>Springer lecture notes in computer science alphabetical</option>
				<option>Springer mathphys author date</option>
				<option>Springer mathphys brackets</option>
				<option>Springer physics author date</option>
				<option>Springer physics brackets</option>
				<option>Springerprotocols</option>
				<option>Springer socpsych author date</option>
				<option>Springer socpsych brackets</option>
				<option>Springer vancouver</option>
				<option>Springer vancouver author date</option>
				<option>Springer vancouver brackets</option>
				<option>Standards in genomic sciences</option>
				<option>Stavebni obzor</option>
				<option>Stem cells</option>
				<option>Stem cells and development</option>
				<option>St patricks college</option>
				<option>Strahlentherapie und onkologie</option>
				<option>Strategic management journal</option>
				<option>Stroke</option>
				<option>Studii teologice</option>
				<option>Stuttgart media university</option>
				<option>Surgical neurology international</option>
				<option>Swedish legal</option>
				<option>Systematic biology</option>
				<option>Taylor and francis chicago f</option>
				<option>Taylor and francis council of science editors author date</option>
				<option>Taylor and francis harvard x</option>
				<option>Taylor and francis national library of medicine</option>
				<option>Technische universitat munchen controlling</option>
				<option>Technische universitat wien</option>
				<option>Teologia catalunya</option>
				<option>Terra nova</option>
				<option>Tgm wien diplom</option>
				<option>The accounting review</option>
				<option>The american journal of cardiology</option>
				<option>The american journal of gastroenterology</option>
				<option>The american journal of geriatric pharmacotherapy</option>
				<option>The american journal of pathology</option>
				<option>The american journal of psychiatry</option>
				<option>The american naturalist</option>
				<option>The astrophysical journal</option>
				<option>The auk</option>
				<option>The bone and joint journal</option>
				<option>The british journal of psychiatry</option>
				<option>The british journal of sociology</option>
				<option>The company of biologists</option>
				<option>The condor</option>
				<option>The design journal</option>
				<option>The embo journal</option>
				<option>The febs journal</option>
				<option>The geological society of america</option>
				<option>The historical journal</option>
				<option>The holocene</option>
				<option>The institute of electronics information and communication engineers</option>
				<option>The international journal of psychoanalysis</option>
				<option>The isme journal</option>
				<option>The journal of adhesive dentistry</option>
				<option>The journal of clinical endocrinology and metabolism</option>
				<option>The journal of clinical investigation</option>
				<option>The journal of comparative neurology</option>
				<option>The journal of eukaryotic microbiology</option>
				<option>The journal of hellenic studies</option>
				<option>The journal of immunology</option>
				<option>The journal of juristic papyrology</option>
				<option>The journal of neuropsychiatry and clinical neurosciences</option>
				<option>The journal of neuroscience</option>
				<option>The journal of pain</option>
				<option>The journal of pharmacology and experimental therapeutics</option>
				<option>The journal of physiology</option>
				<option>The journal of the acoustical society of america</option>
				<option>The journal of the torrey botanical society</option>
				<option>The journal of urology</option>
				<option>The journal of wildlife management</option>
				<option>The lancet</option>
				<option>The lichenologist</option>
				<option>The neuroscientist</option>
				<option>The new england journal of medicine</option>
				<option>Theologie und philosophie</option>
				<option>The oncologist</option>
				<option>The open university a251</option>
				<option>The open university harvard</option>
				<option>The open university m801</option>
				<option>The open university numeric</option>
				<option>The open university numeric superscript</option>
				<option>The optical society</option>
				<option>Theory culture and society</option>
				<option>The pharmacogenomics journal</option>
				<option>The plant cell</option>
				<option>The plant journal</option>
				<option>The review of financial studies</option>
				<option>The rockefeller university press</option>
				<option>The scandinavian journal of clinical and laboratory investigation</option>
				<option>The world journal of biological psychiatry</option>
				<option>Thieme e journals vancouver</option>
				<option>Thrombosis and haemostasis</option>
				<option>Tissue engineering</option>
				<option>Toxicon</option>
				<option>Traces</option>
				<option>Traffic</option>
				<option>Traffic injury prevention</option>
				<option>Transactions of the american philological association</option>
				<option>Transportation research record</option>
				<option>Trends journals</option>
				<option>Triangle</option>
				<option>Turabian fullnote bibliography</option>
				<option>Ugeskrift for laeger</option>
				<option>Unified style linguistics</option>
				<option>United nations conference on trade and development</option>
				<option>Universidad evangelica del paraguay</option>
				<option>Universita cattolica del sacro cuore</option>
				<option>Universita di bologna lettere</option>
				<option>Universitat freiburg geschichte</option>
				<option>Universitat heidelberg historisches seminar</option>
				<option>Universite de liege histoire</option>
				<option>Universite de picardie jules verne ufr de medecine</option>
				<option>Universite de sherbrooke faculte d education</option>
				<option>Universite du quebec a montreal</option>
				<option>Universiteit utrecht onderzoeksgids geschiedenis</option>
				<option>Universite laval departement dinformation et de communication</option>
				<option>Universite laval faculte de theologie et de sciences religieuses</option>
				<option>University college dublin school of history and archives</option>
				<option>University of south australia harvard 2011</option>
				<option>University of south australia harvard 2013</option>
				<option>Urban habitats</option>
				<option>Urban studies</option>
				<option>User modeling and user adapted interaction</option>
				<option>Us geological survey</option>
				<option>Vancouver</option>
				<option>Vancouver author date</option>
				<option>Vancouver brackets</option>
				<option>Vancouver brackets no et al</option>
				<option>Vancouver brackets only year no issue</option>
				<option>Vancouver superscript</option>
				<option>Vancouver superscript brackets only year</option>
				<option>Vancouver superscript only year</option>
				<option>Veterinary medicine austria</option>
				<option>Veterinary radiology and ultrasound</option>
				<option>Vienna legal</option>
				<option>Vingtieme siecle</option>
				<option>Virology</option>
				<option>Vision research</option>
				<option>Water environment research</option>
				<option>Water research</option>
				<option>Water science and technology</option>
				<option>Weed science society of america</option>
				<option>Wheaton college phd in biblical and theological studies</option>
				<option>Who europe harvard</option>
				<option>Who europe numeric</option>
				<option>Wissenschaftlicher industrielogistik dialog</option>
				<option>World congress on engineering asset management</option>
				<option>Xenotransplantation</option>
				<option>Yeast</option>
				<option>Zdravniski vestnik</option>
				<option>Zeitschrift fur medienwissenschaft</option>
				<option>Zeitschrift fur soziologie</option>
				<option>Zookeys</option>
				<option>Zootaxa</option>
			</select>
			<xsl:variable name="lAuthorshipType">
				<xsl:choose>
					<xsl:when test="count(.//*[@object_id='92']) &gt; 0">
						<xsl:value-of select=".//*[@object_id='92']/fields/*[@id='265']/value/@value_id"></xsl:value-of>
					</xsl:when>
					<xsl:when test="count(.//*[@object_id='100']) &gt; 0">
						<xsl:value-of select="//*[@object_id='100']/fields/*[@id='281']/value/@value_id"></xsl:value-of>
					</xsl:when>
					<xsl:when test="count(.//*[@object_id='101']) &gt; 0">
						<xsl:value-of select=".//*[@object_id='101']/fields/*[@id='282']/value/@value_id"></xsl:value-of>
					</xsl:when>
				</xsl:choose>
			</xsl:variable>
			<xsl:variable name="Authors">
				<xsl:text>[</xsl:text>
				<xsl:for-each select=".//*[@object_id='92' or @object_id='100' or @object_id='101']/*[@object_id='90']">
					<xsl:choose>
						<xsl:when test="$lAuthorshipType = 3">
							<xsl:apply-templates select="." mode="processSingleReferenceAuthorFullNamesJSON" />
						</xsl:when>
						<xsl:otherwise>
							<xsl:apply-templates select="." mode="processSingleReferenceAuthorFullNamesJSON" />
						</xsl:otherwise>
					</xsl:choose>
					<xsl:if test="position()!=last()"><xsl:text>, </xsl:text></xsl:if>
				</xsl:for-each>
				<xsl:text>]</xsl:text>
			</xsl:variable>

			<xsl:variable name="refTitle"><xsl:apply-templates select=".//fields/*[@id='276' or @id='255']/value" mode="formatting_nospace"/></xsl:variable>
			<xsl:variable name="refYear" ><xsl:apply-templates select=".//fields/*[@id='254']/value" mode="formatting_nospace"/></xsl:variable>
			<xsl:variable name="journal" ><xsl:apply-templates select=".//fields/*[@id='243']/value" mode="formatting_nospace"/></xsl:variable>
			<xsl:variable name="volume"  ><xsl:apply-templates select=".//fields/*[@id='258']/value" mode="formatting_nospace"/></xsl:variable>
			<xsl:variable name="issue"   ><xsl:apply-templates select=".//fields/*[@id='27']/value"  mode="formatting_nospace"/></xsl:variable>
			<xsl:variable name="spage"   ><xsl:apply-templates select=".//fields/*[@id='28']/value"  mode="formatting_nospace"/></xsl:variable>
			<xsl:variable name="epage"   ><xsl:apply-templates select=".//fields/*[@id='29']/value"  mode="formatting_nospace"/></xsl:variable>
			<xsl:variable name="doi"     ><xsl:apply-templates select=".//fields/*[@id='30']/value"  mode="formatting_nospace"/></xsl:variable>


			<div id="formattedRef"></div>
			<script src="/lib/js/chosen.jquery.min.js"></script>




			<script type="text/javascript">
				<![CDATA[
				var server = 'http://192.168.83.187:5000';
				var ref   = encodeURIComponent(JSON.stringify(
				{
				]]>
					<xsl:text>year: </xsl:text><xsl:value-of select="normalize-space($refYear)" /><xsl:text>,
						authors: </xsl:text><xsl:value-of select="$Authors" /><xsl:text>,
						title: '</xsl:text><xsl:value-of select="normalize-space($refTitle)" /><xsl:text>',
						doi:   '</xsl:text><xsl:value-of select="normalize-space($doi)" /><xsl:text>',
						journal: '</xsl:text><xsl:value-of select="normalize-space($journal)" /><xsl:text>',
						volume: '</xsl:text><xsl:value-of select="normalize-space($volume)" /><xsl:text>',
						issue: '</xsl:text><xsl:value-of select="normalize-space($issue)" /><xsl:text>',
						spage: '</xsl:text><xsl:value-of select="normalize-space($spage)" /><xsl:text>',
						epage: '</xsl:text><xsl:value-of select="normalize-space($epage)" /><xsl:text>'
					</xsl:text>

				<![CDATA[
				}));

				$('.chosen-select').chosen();


				]]>
			</script>


		</div>
	</xsl:template>
	
	<xsl:template name="AOF-Place-Cited-Element-Navigation">
		<xsl:param name="pInstanceId">0</xsl:param>		
		<xsl:if test="$pInstanceId &gt; 0 and $pInArticleMode &gt; 0">			
			<xsl:variable name="lCitationsCnt" select="php:function('GetElementCitationsCnt', string($pInstanceId))" />
			<xsl:if test="$lCitationsCnt &gt; 0">
				<div class="P-Element-Citations-Navigation" >
					<xsl:attribute name="data-cited-element-instance-id"><xsl:value-of select="$pInstanceId" /></xsl:attribute>
					<div class="P-Citation-Navigation-Link-First">
						First						
					</div>
					<xsl:if test="$lCitationsCnt &gt; 1">
						<div class="P-Citation-Navigation-Link-Prev">
							Prev
						</div>
						<div class="P-Citation-Navigation-Link-Next">
							Next
						</div>
					</xsl:if>
				</div>
			</xsl:if>
		</xsl:if>	
	</xsl:template>

</xsl:stylesheet>
