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
	
	<xsl:variable name="figBaseURL">http://teodor.pwt.pensoft.dev</xsl:variable>
	
	<xsl:variable name="gAuthorshipEditorType">2</xsl:variable>
	<xsl:variable name="gEditorAuthorshipEditorType">1</xsl:variable>
	
	<xsl:template match="tn|tn-part|b|i|u|strong|em|sup|sub|p|ul|li|ol|insert|delete|comment-start|comment-end|reference-citation|fig-citation|tbls-citation|sup-files-citation" mode="formatting">
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
	
	<xsl:template match="*" mode="formatting_output_escape">
		<xsl:value-of select="." disable-output-escaping="yes"/>
	</xsl:template>

	<xsl:template match="tn|tn-part|b|i|u|strong|em|sup|sub|p|ul|ol|li|comment-start|comment-end|table|tr|td|tbody|th|reference-citation|fig-citation|tbls-citation|sup-files-citation" mode="table_formatting">
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
	
	<xsl:template match="tn|tn-part|b|i|u|strong|em|sup|sub|insert|delete|comment-start|comment-end|reference-citation|fig-citation|tbls-citation|sup-files-citation" mode="title">
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
		<xsl:if test="$pPDFPreviewMode = 0">
			<div class="P-Article-Preview-Names">
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
									Accepted by: <span>ACADEMIC EDITOR</span>
								</div>
								<div class="P-Article-Info-Block-Row">
									Recieved <span>DATE RECEIVED</span> | accepted <span>DATE ACCEPTED</span> | published <span>DATE PUBLISHED</span>
								</div>
								<div class="P-Article-Info-Block-Row">
									<xsl:text>© </xsl:text><xsl:value-of select="php:function('getYear')"/><xsl:text> </xsl:text>
									<xsl:for-each select="$pDocumentNode/objects/*[@object_id='14' or @object_id = '152']/*[@object_id='9' or @object_id='153']/*[@object_id='8']">
										<xsl:apply-templates select="." mode="singleCorrespondingAuthorInLicense" />
										<xsl:if test="position()!=last()"><xsl:text>, </xsl:text></xsl:if>
									</xsl:for-each>
									<xsl:text>.</xsl:text> <br /><xsl:text>This is an open access article distributed under the terms of the </xsl:text>
									<span>Creative Commons Attribution License 3.0 (CC-BY),</span> <br />
									<xsl:text>which permits unrestricted use, distribution, and reproduction in any medium, provided the original author and source are credited.</xsl:text>
								</div>
							</td>
							<td width="95px" valign="middle" align="right">
								<img src="/i/open_access.png" alt="Open Access" />
							</td>
						</tr>
					</tbody>
				</table>
			</div>
		</xsl:if>
	</xsl:template>

	<xsl:template match="*" mode="singleAuthor">
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
		<sup class="P-Current-Author-Addresses">
			<xsl:for-each select="./*[@object_id='5']" >
				<xsl:variable name="lCurrentNode" select="." />
				<xsl:variable name="affiliation" select="normalize-space($lCurrentNode/fields/affiliation/value)" />
				<xsl:variable name="city" select="normalize-space($lCurrentNode/fields/city/value)" />
				<xsl:variable name="country" select="$lCurrentNode/fields/country/value" />
				<xsl:variable name="fullAffiliation" select="concat($affiliation, ', ', $city, ', ', $country)" />
				<xsl:variable name="lAffId" select="php:function('getContributorAffId', $fullAffiliation)"></xsl:variable>
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
		<xsl:if test="$lUploadedFileName != ''">
			<span class="fieldLabel">Filename:</span><xsl:text>&#160;</xsl:text>
			<xsl:value-of select="normalize-space($lUploadedFileName)"/><xsl:text> - </xsl:text>
		</xsl:if>
		<xsl:if test="$lFileName != ''">
			<a class="download" target="_blank">
				<xsl:attribute name="href"><xsl:value-of select="normalize-space($pSiteUrl)"/><xsl:text>getfile.php?filename=</xsl:text><xsl:value-of select="normalize-space($lFileName)"/></xsl:attribute>
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
						<xsl:text>Suppl. file </xsl:text>
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
		<xsl:variable name="lContent">
				<a target="_blank">
					<xsl:attribute name="href">
						<xsl:value-of select="$pSiteUrl"/><xsl:text>display_zoomed_figure.php?fig_id=</xsl:text>
						<xsl:value-of select="$pInstanceId"/>
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
						<xsl:value-of select="$pSiteUrl"/><xsl:text>display_zoomed_figure.php?fig_id=</xsl:text>
						<xsl:value-of select="$pInstanceId"/>
					</xsl:attribute>
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
						<div class="Plate-part-letter"><xsl:value-of select="$pPlateNum"/></div>
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
				<xsl:with-param name="pInstanceId"><xsl:value-of select="@instance_id"/></xsl:with-param>
				<xsl:with-param name="pPicId"><xsl:value-of select="./fields/*[@id='483']/value"/></xsl:with-param>
			</xsl:call-template>
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
				<iframe width="696" height="522" frameborder="0">
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
		<xsl:variable name="lPlateType"><xsl:value-of select="./fields/*[@id='485']/value"/></xsl:variable>
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
			<xsl:choose>
				<xsl:when test="$lPlateType = 3"><!-- 2 rows 1 columns -->
					<xsl:for-each select=".//*[@object_id='225' or @object_id='226' or @object_id='227' or @object_id='228' or @object_id='229' or @object_id='230']">
						<xsl:call-template name="imagePicPreview">
							<xsl:with-param name="pInstanceId"><xsl:value-of select="@instance_id"/></xsl:with-param>
							<xsl:with-param name="pPicId"><xsl:value-of select="./fields/*[@id='484']/value"/></xsl:with-param>
							<xsl:with-param name="pImageType"><xsl:value-of select="$lImageType"/></xsl:with-param>
							<xsl:with-param name="pPlateNum">
								<xsl:choose>
									<xsl:when test="@object_id='225'">a</xsl:when>
									<xsl:when test="@object_id='226'">b</xsl:when>
									<xsl:when test="@object_id='227'">c</xsl:when>
									<xsl:when test="@object_id='228'">d</xsl:when>
									<xsl:when test="@object_id='229'">e</xsl:when>
									<xsl:when test="@object_id='230'">f</xsl:when>
								</xsl:choose>
							</xsl:with-param>
						</xsl:call-template>
					</xsl:for-each>
				</xsl:when>
				<xsl:otherwise>
					<xsl:for-each select=".//*[@object_id='225' or @object_id='226' or @object_id='227' or @object_id='228' or @object_id='229' or @object_id='230']">
						<div>
							<xsl:attribute name="class"><xsl:text>P-Article-Preview-Picture-Row</xsl:text></xsl:attribute>
							<xsl:call-template name="imagePicPreview">
								<xsl:with-param name="pInstanceId"><xsl:value-of select="@instance_id"/></xsl:with-param>
								<xsl:with-param name="pPicId"><xsl:value-of select="./fields/*[@id='484']/value"/></xsl:with-param>
								<xsl:with-param name="pImageType"><xsl:value-of select="$lImageType"/></xsl:with-param>
								<xsl:with-param name="pPlateNum">
									<xsl:choose>
										<xsl:when test="@object_id='225'">a</xsl:when>
										<xsl:when test="@object_id='226'">b</xsl:when>
										<xsl:when test="@object_id='227'">c</xsl:when>
										<xsl:when test="@object_id='228'">d</xsl:when>
										<xsl:when test="@object_id='229'">e</xsl:when>
										<xsl:when test="@object_id='230'">f</xsl:when>
									</xsl:choose>
								</xsl:with-param>
							</xsl:call-template>
						</div>
					</xsl:for-each>
				</xsl:otherwise>
			</xsl:choose>
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
					<xsl:attribute name="src"><xsl:value-of select="$figBaseURL"/>/showfigure.php?filename=singlefigmini_<xsl:value-of select="./fields/*[@id='483']/value"></xsl:value-of>.jpg</xsl:attribute>
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
			<div class="twocolumnmini">
				<img alt="">
					<xsl:attribute name="src"><xsl:value-of select="$figBaseURL"/>/showfigure.php?filename=singlefigmini_<xsl:value-of select="./*[@object_id='225']/fields/*[@id='484']/value"></xsl:value-of>.jpg</xsl:attribute>
				</img>
			</div>
		</div>
		<div style="text-align: center; display: table; width: 90px">
			<div class="twocolumnmini">
				<img alt="">
					<xsl:attribute name="src"><xsl:value-of select="$pSiteUrl"/>/showfigure.php?filename=singlefigmini_<xsl:value-of select="./*[@object_id='226']/fields/*[@id='484']/value"></xsl:value-of>.jpg</xsl:attribute>
				</img>
			</div>
		</div>
	</xsl:template>
	
	<!-- Plate type 2 image preview -->
	<xsl:template match="*[@object_id='232']" mode="singleFigSmallPreview">
		<div class="twocolumnmini">
			<img style="float: left;"  alt="">
				<xsl:attribute name="src"><xsl:value-of select="$figBaseURL"/>/showfigure.php?filename=plateportraitmini_<xsl:value-of select="./*[@object_id='225']/fields/*[@id='484']/value"></xsl:value-of>.jpg</xsl:attribute>
			</img>
		</div>
		<div class="twocolumnmini">
			<img style="float: left;"  alt="">
				<xsl:attribute name="src"><xsl:value-of select="$figBaseURL"/>/showfigure.php?filename=plateportraitmini_<xsl:value-of select="./*[@object_id='226']/fields/*[@id='484']/value"></xsl:value-of>.jpg</xsl:attribute>
			</img>
		</div>
	</xsl:template>
	
	<!-- Plate type 3 image preview -->
	<xsl:template match="*[@object_id='233']" mode="singleFigSmallPreview">
		<div>
			<div class="twocolumnminiholder">
				<div class="twocolumnmini">
					<img alt="">
						<xsl:attribute name="src"><xsl:value-of select="$figBaseURL"/>/showfigure.php?filename=twocolumnmini_<xsl:value-of select="./*[@object_id='225']/fields/*[@id='484']/value"></xsl:value-of>.jpg</xsl:attribute>
					</img>
				</div>
			</div>
			<div class="twocolumnminiholder">
				<div class="twocolumnmini">
					<img alt="">
						<xsl:attribute name="src"><xsl:value-of select="$figBaseURL"/>/showfigure.php?filename=twocolumnmini_<xsl:value-of select="./*[@object_id='226']/fields/*[@id='484']/value"></xsl:value-of>.jpg</xsl:attribute>
					</img>
				</div>
			</div>
			<div class="twocolumnminiholder">
				<div class="twocolumnmini">
					<img alt="">
						<xsl:attribute name="src"><xsl:value-of select="$figBaseURL"/>/showfigure.php?filename=twocolumnmini_<xsl:value-of select="./*[@object_id='227']/fields/*[@id='484']/value"></xsl:value-of>.jpg</xsl:attribute>
					</img>
				</div>
			</div>
			<div class="twocolumnminiholder">
				<div class="twocolumnmini">
					<img alt="">
						<xsl:attribute name="src"><xsl:value-of select="$figBaseURL"/>/showfigure.php?filename=twocolumnmini_<xsl:value-of select="./*[@object_id='228']/fields/*[@id='484']/value"></xsl:value-of>.jpg</xsl:attribute>
					</img>
				</div>
			</div>
		</div>
	</xsl:template>
	
	<!-- Plate type 4 image preview -->
	<xsl:template match="*[@object_id='234']" mode="singleFigSmallPreview">
		<div>
			<div class="twocolumnminiholder">
				<div class="twocolumnmini">
					<img alt="">
						<xsl:attribute name="src"><xsl:value-of select="$figBaseURL"/>/showfigure.php?filename=twocolumnmini_<xsl:value-of select="./*[@object_id='225']/fields/*[@id='484']/value"></xsl:value-of>.jpg</xsl:attribute>
					</img>
				</div>
			</div>
			<div class="twocolumnminiholder">
				<div class="twocolumnmini">
					<img alt="">
						<xsl:attribute name="src"><xsl:value-of select="$figBaseURL"/>/showfigure.php?filename=twocolumnmini_<xsl:value-of select="./*[@object_id='226']/fields/*[@id='484']/value"></xsl:value-of>.jpg</xsl:attribute>
					</img>
				</div>
			</div>
			<div class="twocolumnminiholder">
				<div class="twocolumnmini">
					<img alt="">
						<xsl:attribute name="src"><xsl:value-of select="$figBaseURL"/>/showfigure.php?filename=twocolumnmini_<xsl:value-of select="./*[@object_id='227']/fields/*[@id='484']/value"></xsl:value-of>.jpg</xsl:attribute>
					</img>
				</div>
			</div>
			<div class="twocolumnminiholder">
				<div class="twocolumnmini">
					<img alt="">
						<xsl:attribute name="src"><xsl:value-of select="$figBaseURL"/>/showfigure.php?filename=twocolumnmini_<xsl:value-of select="./*[@object_id='228']/fields/*[@id='484']/value"></xsl:value-of>.jpg</xsl:attribute>
					</img>
				</div>
			</div>
			<div class="twocolumnminiholder">
				<div class="twocolumnmini">
					<img alt="">
						<xsl:attribute name="src"><xsl:value-of select="$figBaseURL"/>/showfigure.php?filename=twocolumnmini_<xsl:value-of select="./*[@object_id='229']/fields/*[@id='484']/value"></xsl:value-of>.jpg</xsl:attribute>
					</img>
				</div>
			</div>
			<div class="twocolumnminiholder">
				<div class="twocolumnmini">
					<img alt="">
						<xsl:attribute name="src"><xsl:value-of select="$figBaseURL"/>/showfigure.php?filename=twocolumnmini_<xsl:value-of select="./*[@object_id='230']/fields/*[@id='484']/value"></xsl:value-of>.jpg</xsl:attribute>
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
					<div class="name">Table <xsl:value-of select="$lFigNumber" />.</div>
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
	
	<!-- Article of the future SINGLE ELEMENT PREVIEWS START -->
	
	<!-- Article of the future preview template of a single figure -->
	<xsl:template match="*" mode="article_preview_figure">
		<xsl:for-each select=".">	
			<div class="item-holder-RC">
					<span class="fig-label-RC">
						<xsl:value-of select="./@display_name"></xsl:value-of>
							<xsl:text> </xsl:text>
						<xsl:value-of select="./fields/figure_number"></xsl:value-of>
					</span>
				<xsl:apply-templates select="image" mode="Figures" />
				<xsl:apply-templates select="multiple_images_plate" mode="Figures" />
			</div>
		<xsl:if test="position()!=last()">
			<div class="P-Clear" />
		</xsl:if>	
		</xsl:for-each>
			
		<!-- The node of the specific figure -->
		<xsl:variable name="lCurrentNode" select="."></xsl:variable>
	</xsl:template>
	
	<!-- Article of the future preview template of a single plate -->
	<xsl:template match="*" mode="article_preview_plate">
		<xsl:for-each select=".">	
			<div class="item-holder-RC">
					<span class="fig-label-RC">
						<xsl:value-of select="./@display_name"></xsl:value-of>
							<xsl:text> </xsl:text>
						<xsl:value-of select="./fields/figure_number"></xsl:value-of>
					</span>
				<xsl:apply-templates select="image" mode="Figures" />
				<xsl:apply-templates select="multiple_images_plate" mode="Figures" />
			</div>
		<xsl:if test="position()!=last()">
			<div class="P-Clear" />
		</xsl:if>	
		</xsl:for-each>
		<!-- The node of the specific plate part (i.e. that is na instance which has an object id IN (225, 226, 227, 228, 229, 230) -->
		<xsl:variable name="lCurrentNode" select="."></xsl:variable>
	</xsl:template>
	
	<!-- Article of the future preview template of a single table -->
	<xsl:template match="*" mode="article_preview_table">	
		<xsl:apply-templates select="." mode="singleTableNormalPreview"/>
		<!-- The node of the specific table -->
		<xsl:variable name="lCurrentNode" select="."></xsl:variable>
	</xsl:template>
	
	<!-- Article of the future preview template of a single reference -->
	<xsl:template match="*" mode="article_preview_reference">
		
		<xsl:apply-templates select="." mode="articleBack"/>
		
		<xsl:apply-templates select="." mode="RefinderLinks"/>
				
		<!-- The node of the specific reference -->
		<xsl:variable name="lCurrentNode" select="."></xsl:variable>
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
					Simple	
				</xsl:when>
				<xsl:when test="$lArticleType = 5">
					Simple	
				</xsl:when>
				<xsl:when test="$lArticleType = 6">
					Simple	
				</xsl:when>
				<xsl:when test="$lArticleType = 7">
					Simple	
				</xsl:when>
			</xsl:choose>	
		</xsl:template>
		
		<xsl:template match="*" mode="RefinderLinksSimple">
			
			
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
								<xsl:apply-templates select="." mode="processSingleReferenceAuthorFirstLast" />
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
							<xsl:text>http://192.168.83.8:5000/?search=advanced</xsl:text>
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
				<!-- First name -->
				<xsl:value-of select="./fields/*[@id='251']/value"></xsl:value-of>
				<xsl:text> </xsl:text>
				<!-- Last name -->
				<xsl:value-of select="./fields/*[@id='252']/value"></xsl:value-of>
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
						<span class="fig-label-RC">
							<xsl:value-of select="./@display_name"></xsl:value-of>
								<xsl:text> </xsl:text>
							<xsl:value-of select="./fields/figure_number"></xsl:value-of>
						</span>
					<xsl:apply-templates select="image" mode="Figures" />
					<xsl:apply-templates select="multiple_images_plate" mode="Figures" />
				</div>
			<xsl:if test="position()!=last()">
				<div class="P-Clear" />
			</xsl:if>	
			</xsl:for-each>
	</xsl:template>
	
	<xsl:template match="image" mode="Figures">
			<div class="P-Picture-Holder">
				<div class="singlefigmini">
					<img alt="">
						<xsl:attribute name="src"><xsl:value-of select="$figBaseURL"/>/showfigure.php?filename=singlefigmini_<xsl:value-of select="./fields/photo_select/value"></xsl:value-of>.jpg</xsl:attribute> 
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
			<div class="item-holder-RC">
				<div class="P-table-tump-holder">
					<img width="60" heigth="48" alt="">
						<xsl:attribute name="src"><xsl:value-of select="$figBaseURL"/>/i/table_pic-60.png</xsl:attribute> 
					</img> 
				</div>		
					<span class="fig-label-RC">
						<xsl:value-of select="./@display_name"></xsl:value-of>
							<xsl:text> </xsl:text>
						<xsl:value-of select="position()"></xsl:value-of>
					</span>			
				<div class="list-caption"> 
					<xsl:apply-templates select="./fields/table_caption/value" mode="formatting"/>
				</div>
			</div>
		</xsl:for-each>
		
	</xsl:template>
	
	<!-- Article of the future preview template of the references list -->
	<xsl:template match="*" mode="article_references_list">
		<xsl:apply-templates select="*[@object_id='95']" mode="articleBack"/>
		<!-- The node of the references holder -->
		<xsl:variable name="lCurrentNode" select="."></xsl:variable>
	</xsl:template>
	
	<!-- Article of the future preview template of the sup files list -->
	<xsl:template match="*" mode="article_sup_files_list">
		<div class="suppl-list-AOF">
			<xsl:for-each select="//*[@object_id='55']">
				<div class="item-holder-RC">
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
					<span class="fig-label-RC">
						<xsl:text>Supplementary file </xsl:text>
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
						<span class="fieldLabel">							
							<xsl:value-of select="./fields/*[@id='217']/@field_name" />:&#160;</span>
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
						</xsl:apply-templates>
					</div>
				</xsl:if>
			</div>
		</xsl:if>
	</xsl:template>
	
	<!-- Article of the future preview template of the taxon list -->
	<xsl:template match="*" mode="article_taxon_list">

		<xsl:variable name="lOutputFirst">
			<xsl:apply-templates select="." mode="trans1" />	
		</xsl:variable>		
		<xsl:apply-templates select="exslt:node-set($lOutputFirst)" mode="trans2" />
		
		<!-- The document node -->
		<xsl:variable name="lCurrentNode" select="."></xsl:variable>
	</xsl:template>
	
	<xsl:template match="*" mode="trans1">		
		<xsl:for-each select="//*[@object_id=182 or @object_id=179 or @object_id=196 or @object_id=197 or @object_id=184 or @object_id=192 or @object_id=213 or @object_id=216]">
			<div class="taxon" tnu="TT">
				<xsl:apply-templates select="./*[@object_id='180' or @object_id='181']" mode="taxonTreatmentNameAOF"/>
			</div>					
		</xsl:for-each>
				
		<xsl:for-each select="//checklist_taxon">
			<div class="taxon" tnu="CHK">
				<xsl:apply-templates select="fields" mode="TaxaChecklistAOF"/>
			</div>					
		</xsl:for-each>
		
		<xsl:for-each select="//tn">
			<div class="taxon" tnu="INL">
				<xsl:apply-templates select="." mode="TaxaInline"/>
			</div>		
		</xsl:for-each>
	</xsl:template>
	
	<xsl:template match="*" mode="TaxaInline">
		<xsl:for-each select="tn-part">
			<span>
				 <xsl:attribute name="class"><xsl:value-of select="./@type" /></xsl:attribute>
				 <xsl:value-of select="."/>
			  </span>
			  <xsl:if test="position() != last()">
					 <xsl:text> </xsl:text>
			  </xsl:if>
		</xsl:for-each>			
	</xsl:template>
	
	<!-- Taxon treatments -->
	<xsl:template match="*[@object_id='180']" mode="taxonTreatmentNameAOF" xml:space="default">
			<span class="genus">
				<xsl:apply-templates select="./fields/*[@id='48']" mode="formatting_nospace"/>
			</span>
			<xsl:if test="./fields/*[@id='417']/value != ''">
				<xsl:text> </xsl:text>			
				<span class="x">(</span>
				<span class="subgenus">
					<xsl:apply-templates select="./fields/*[@id='417']" mode="formatting_nospace"/>
				</span>
				<span class="x">)</span>
			</xsl:if>
			<xsl:text> </xsl:text>
			<span class="species">
				<xsl:apply-templates select="./fields/*[@id='49']" mode="formatting_nospace"/>
			</span>
	</xsl:template>	
	
	<xsl:template match="*[@object_id='181']" mode="taxonTreatmentName">
			<span class="genus">
					<xsl:apply-templates select="./fields/*[@id='48']" mode="formatting"/>
			</span>
	</xsl:template>

	<!-- checklist shits -->
	<xsl:template match="fields" mode="TaxaChecklistAOF">
		<xsl:variable name="lRankType" select="./*[@id='414']/value"></xsl:variable>					
		<xsl:variable name="RankID">
			<xsl:choose>
				<xsl:when test="$lRankType = 'kingdom'">    <xsl:text>419</xsl:text></xsl:when>				
				<xsl:when test="$lRankType = 'subkingdom'"> <xsl:text>420</xsl:text></xsl:when>
				<xsl:when test="$lRankType = 'phylum'">     <xsl:text>421</xsl:text></xsl:when>
				<xsl:when test="$lRankType = 'subphylum'">  <xsl:text>422</xsl:text></xsl:when>
				<xsl:when test="$lRankType = 'superclass'"> <xsl:text>423</xsl:text></xsl:when>
				<xsl:when test="$lRankType = 'class'">		<xsl:text>424</xsl:text></xsl:when>
				<xsl:when test="$lRankType = 'subclass'">   <xsl:text>425</xsl:text></xsl:when>
				<xsl:when test="$lRankType = 'superorder'"> <xsl:text>426</xsl:text></xsl:when>
				<xsl:when test="$lRankType = 'order'"> 		<xsl:text>427</xsl:text></xsl:when>
				<xsl:when test="$lRankType = 'suborder'"> 	<xsl:text>428</xsl:text></xsl:when>
				<xsl:when test="$lRankType = 'infraorder'"> <xsl:text>429</xsl:text></xsl:when>
				<xsl:when test="$lRankType = 'superfamily'"><xsl:text>430</xsl:text></xsl:when>
				<xsl:when test="$lRankType = 'family'"> 	<xsl:text>431</xsl:text></xsl:when>
				<xsl:when test="$lRankType = 'subfamily'">	<xsl:text>432</xsl:text></xsl:when>
				<xsl:when test="$lRankType = 'tribe'">		<xsl:text>433</xsl:text></xsl:when>
				<xsl:when test="$lRankType = 'subtribe'">	<xsl:text>434</xsl:text></xsl:when>
				<xsl:when test="$lRankType = 'genus'"> 		<xsl:text>48</xsl:text></xsl:when>
				<xsl:when test="$lRankType = 'subgenus'"> 	<xsl:text>417</xsl:text></xsl:when>
				<xsl:when test="$lRankType = 'species'"> 	<xsl:text>49</xsl:text></xsl:when>
				<xsl:when test="$lRankType = 'subspecies'"> <xsl:text>418</xsl:text></xsl:when>
				<xsl:when test="$lRankType = 'variety'"> 	<xsl:text>435</xsl:text></xsl:when>
				<xsl:when test="$lRankType = 'form'"> 		<xsl:text>436</xsl:text></xsl:when>
				<xsl:otherwise> </xsl:otherwise>
			</xsl:choose>
		</xsl:variable>

	<xsl:variable name="lRankValue" select="./*[@id=$RankID]/value"></xsl:variable>
		<!-- value -->
		<xsl:if test="$lRankValue != ''">
			<xsl:choose>
				<xsl:when test="$lRankType = 'genus'">
					<span class="genus">
						<xsl:apply-templates select="$lRankValue" mode="formatting"/>
					</span>
				</xsl:when>
				<xsl:when test="$lRankType = 'subgenus'">
					<span class="subgenus">>
						<xsl:apply-templates select="$lRankValue" mode="formatting"/>
					</span>
				</xsl:when>
				<xsl:when test="$lRankType = 'species' or $lRankType = 'subspecies' or $lRankType = 'variety' or $lRankType = 'form'">	
					<!-- $Genus-->
					<span class="genus">
						<xsl:apply-templates select="./*[@id='48']" mode="formatting_nospace"/>
					</span>
					<xsl:if test="./*[@id='417']/value != ''">	
						<xsl:text> (</xsl:text>
						<!-- $Subgenus-->
						<span class="subgenus">
							<xsl:apply-templates select="./*[@id='417']" mode="formatting_nospace"/>
						</span><xsl:text>)</xsl:text>
					</xsl:if>
					<xsl:text> </xsl:text>
					<!-- $Species -->
					<span class="species">
						<xsl:apply-templates select="./*[@id='49']" mode="formatting_nospace"/>
					</span>
					<xsl:if test="$lRankType = 'subspecies'"> subsp. 
						<span class="subspecies">
							<xsl:apply-templates select="./*[@id='418']" mode="formatting_nospace"/>
						</span>
					</xsl:if>
					<xsl:if test="$lRankType = 'variety'"> 	 var.   
						<span class="variety">
							<xsl:apply-templates select="./*[@id='435']" mode="formatting_nospace"/>
						</span>
					</xsl:if>
					<xsl:if test="$lRankType = 'form'">		 f.     
						<span class="form">
							<xsl:apply-templates select="./*[@id='436']" mode="formatting_nospace"/>
						</span>
					</xsl:if>	
				 </xsl:when>	
				<xsl:otherwise>	
					<span>
						<xsl:attribute name="class"><xsl:value-of select="$lRankType" /></xsl:attribute>
						<xsl:apply-templates select="$lRankValue" mode="formatting"/>
					</span>
				</xsl:otherwise>
			</xsl:choose>
		</xsl:if>
	</xsl:template>
		
	<xsl:key name="taxon" match="//div" use="." />
			
	<xsl:template match="/" mode="trans2">
		  <xsl:for-each select="//div[generate-id()=generate-id(key('taxon',.))]">
			<xsl:sort select="." order="ascending"></xsl:sort>
			<div class="taxalistAOF">
				<xsl:attribute name="tnu"><xsl:value-of select="./@tnu"/></xsl:attribute>
				<xsl:if test="./@tnu = 'TT'">
						<xsl:for-each select="span">
							<xsl:copy-of select="."/>
							  <xsl:if test="position() != last()">
									 <xsl:text> </xsl:text>
							  </xsl:if>
						</xsl:for-each>
						<span></span>
				</xsl:if>
				<xsl:if test="./@tnu = 'CHK'">
						<xsl:for-each select="span">
							<xsl:copy-of select="."/>
							  <xsl:if test="position() != last()">
									 <xsl:text> </xsl:text>
							  </xsl:if>
						</xsl:for-each>
						<span></span>
				</xsl:if>
				<xsl:if test="./@tnu = 'INL'">
						<xsl:for-each select="span">
							<xsl:copy-of select="."/>
							  <xsl:if test="position() != last()">
									 <xsl:text> </xsl:text>
							  </xsl:if>
						</xsl:for-each>
						<span></span>
				</xsl:if>
			</div>
		</xsl:for-each>
	</xsl:template>	

	<!-- Article of the future LIST PREVIEWS END -->
</xsl:stylesheet>
