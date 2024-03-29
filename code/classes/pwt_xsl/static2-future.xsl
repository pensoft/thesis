<?xml version="1.0"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:tp="http://www.plazi.org/taxpub" xmlns:php="http://php.net/xsl" exclude-result-prefixes="php tp xlink xsl" >
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
	
	<xsl:variable name="gAuthorshipEditorType">2</xsl:variable>
	<xsl:variable name="gEditorAuthorshipEditorType">1</xsl:variable>
	
	<xsl:template match="tn|tn-part|b|i|u|strong|em|sup|sub|p|ul|li|ol|insert|delete|comment-start|comment-end|reference-citation|fig-citation|tbls-citation|sup-files-citation" mode="formatting">
		<xsl:call-template name="get_node_text_template"> 
	     <xsl:with-param name="pNode" select="."></xsl:with-param>
	    </xsl:call-template>
	</xsl:template>
	
	<xsl:template match="*" mode="formatting_output_escape">
		<xsl:value-of select="." disable-output-escaping="yes"/>
	</xsl:template>

	<xsl:template match="tn|tn-part|b|i|u|strong|em|sup|sub|p|ul|ol|li|comment-start|comment-end|table|tr|td|tbody|th|reference-citation|fig-citation|tbls-citation|sup-files-citation" mode="table_formatting">
		<xsl:call-template name="get_node_text_template"> 
	     <xsl:with-param name="pNode" select="."></xsl:with-param>
	    </xsl:call-template>
	</xsl:template>
	
	<xsl:template match="tn|tn-part|b|i|u|strong|em|sup|sub|insert|delete|comment-start|comment-end|reference-citation|fig-citation|tbls-citation|sup-files-citation" mode="title">
		<xsl:call-template name="get_node_text_template"> 
	     <xsl:with-param name="pNode" select="."></xsl:with-param>
	    </xsl:call-template>
	</xsl:template>
	
	<!-- Removes spaces -->
	<xsl:template match="*" mode="formatting_nospace">
		<xsl:param name="lTreatmentUrl"/>
		<xsl:apply-templates select="." mode="formatting"/>
	</xsl:template>
	
	<xsl:template match="*" mode="format_taxa_rank">
		<xsl:apply-templates select="." mode="formatting"/>
	</xsl:template>	
	
	
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
		      <span>
		      	 <xsl:attribute name="class"><xsl:value-of select="./@type" /></xsl:attribute>
		      	 <xsl:attribute name="rid"><xsl:value-of select="./@rid" /></xsl:attribute>
		       	 <xsl:copy-of select="$lChildContent"/>
		      </span>
	     </xsl:when>
	     <xsl:when test="$lLocalName='em'">
		      <i>
		       	 <xsl:copy-of select="$lChildContent"/>
		      </i>
	     </xsl:when>
	     <xsl:when test="$lLocalName='reference-citation' or $lLocalName='fig-citation' or $lLocalName='tbls-citation' or $lLocalName='sup-files-citation'">
		       	 <xsl:copy-of select="$lChildContent"/>
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
	
	
	<!-- MARKING EDITABLE FIELDS TEMPLATE --> 
	<xsl:template name="markContentEditableField">
		<xsl:param name="pObjectId" />
		<xsl:param name="pFieldId" />

		<xsl:if test="$pMarkContentEditableFields &gt; 0">
			<xsl:variable name="lCheck" select="php:function('checkIfObjectFieldIsEditable', string($pObjectId), string($pFieldId))" />
			<xsl:if test="$lCheck &gt; 0">
				<xsl:attribute name="contenteditable">false</xsl:attribute>
			</xsl:if>
		</xsl:if>
	</xsl:template>
	
	<xsl:template name="markContentEditableFiguresAndTables">
			<xsl:if test="$pTrackFigureAndTableChanges &gt; 0 and $pMarkContentEditableFields &gt; 0">
				<xsl:attribute name="contenteditable">true</xsl:attribute>
			</xsl:if>
	</xsl:template>

	<!-- JOURNAL INFO -->
	<xsl:template name="journalInfo">
		<xsl:param name="pDocumentNode" />

		<xsl:variable name="lJournalName" select="$pDocumentNode/journal_name" />
		<xsl:variable name="lDocumentType" select="$pDocumentNode/document_type" />
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
								<xsl:text>Corresponding Author: </xsl:text>
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
   <span>
    <xsl:attribute name="class">dcLabel</xsl:attribute>
    <xsl:value-of select="./@field_name"></xsl:value-of><xsl:text>: </xsl:text>
   </span> 
   <!--<xsl:variable name="lId" select="./@id"></xsl:variable>-->
   <!--<xsl:if test="($lId = 58) or ($lId = 60) or ($lId = 61) or ($lId = 114) or ($lId = 116)">-->
		<span>
			<xsl:call-template name="markContentEditableField">
				<xsl:with-param name="pObjectId"><xsl:value-of select="./@object_id" /></xsl:with-param>
				<xsl:with-param name="pFieldId"><xsl:value-of select="./@id" /></xsl:with-param>
			</xsl:call-template>
			<xsl:attribute name="field_id"><xsl:value-of select="./@id" /></xsl:attribute>
			<xsl:attribute name="instance_id"><xsl:value-of select="./@instance_id" /></xsl:attribute>
			<xsl:apply-templates select="./value" mode="formatting"/>
		</span>
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
	

	<!-- Formatting uploaded files spaces -->
	<xsl:template match="*" mode="formatting_uploaded_file">
		<xsl:param name="lFileName"/>
		<xsl:param name="lUploadedFileName"/>
		<xsl:if test="$lUploadedFileName != ''">
			<span class="fieldLabel">Filename:</span><xsl:text>&#160;</xsl:text>
			<xsl:value-of select="normalize-space($lUploadedFileName)"/><xsl:text> - </xsl:text>
		</xsl:if>
		<xsl:if test="$lFileName != ''">
			<a target="_blank">
				<xsl:attribute name="href"><xsl:value-of select="normalize-space($pSiteUrl)"/><xsl:text>getfile.php?filename=</xsl:text><xsl:value-of select="normalize-space($lFileName)"/></xsl:attribute>
				<xsl:attribute name="name"><xsl:value-of select="normalize-space($lFileName)"/></xsl:attribute>
				<xsl:text>Download file</xsl:text>
			</a>
			(<xsl:value-of select="php:function('getUploadedFileSize', string($lFileName))" />)
		</xsl:if>
	</xsl:template>

	

	<!-- Single supplementary material -->
	<xsl:template match="*" mode="singleSupplementaryMaterial">
		<div class="Supplemantary-Material">
			<xsl:choose>
				<xsl:when test="./@id = 214">
					<div class="Supplemantary-File-Title">
						<xsl:call-template name="markContentEditableField">
							<xsl:with-param name="pObjectId" select="../../@object_id"></xsl:with-param>
							<xsl:with-param name="pFieldId" select="./@id"></xsl:with-param>
						</xsl:call-template>
						<xsl:attribute name="field_id"><xsl:value-of select="./@id" /></xsl:attribute>
						<xsl:attribute name="instance_id"><xsl:value-of select="../../@instance_id" /></xsl:attribute>
						<xsl:apply-templates select="./value" mode="formatting"/>
					</div>
				</xsl:when>
				<xsl:otherwise>
					<xsl:choose>
						<xsl:when test="./@id = 222">
							<span class="Supplemantary-File-Section-Label">
								<xsl:attribute name="field_id"><xsl:value-of select="./@id" /></xsl:attribute>
								<xsl:apply-templates select="./value" mode="formatting_uploaded_file">
									<xsl:with-param name="lFileName" select="php:function('getFileNameById', string(./value))"></xsl:with-param>
									<xsl:with-param name="lUploadedFileName" select="php:function('getUploadedFileNameById', string(./value))" />
								</xsl:apply-templates>
							</span>
						</xsl:when>
						<xsl:otherwise>
							<span class="Supplemantary-File-Section-Label">
								<xsl:attribute name="field_id"><xsl:value-of select="./@id" /></xsl:attribute>
								<xsl:apply-templates select="./@field_name" mode="formatting"/>
							</span>
							<xsl:text>: </xsl:text>
							<span class="Supplemantary-P-Inline">
								<xsl:call-template name="markContentEditableField">
									<xsl:with-param name="pObjectId" select="../../@object_id"></xsl:with-param>
									<xsl:with-param name="pFieldId" select="./@id"></xsl:with-param>
								</xsl:call-template>
								<xsl:attribute name="instance_id"><xsl:value-of select="../../@instance_id" /></xsl:attribute>
								<xsl:attribute name="field_id"><xsl:value-of select="./@id" /></xsl:attribute>
								<xsl:apply-templates select="./value" mode="formatting"/>
							</span>
						</xsl:otherwise>
					</xsl:choose>

				</xsl:otherwise>
			</xsl:choose>
		</div>
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
					<xsl:attribute name="src">/showfigure.php?filename=singlefigmini_<xsl:value-of select="./fields/*[@id='483']/value"></xsl:value-of>.jpg</xsl:attribute>
				</img>
				<div class="P-Clear"></div>
			</div>
		</div>	
		<div class="P-Block-Title-Holder">
			<div class="P-Figure-Num">Figure <xsl:value-of select="../fields/*[@id='489']/value"/></div>
			<div class="P-Figure-Desc"><xsl:copy-of select="./fields/*[@id='482']/value"/></div>
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
			<div class="P-Figure-Desc"><xsl:copy-of select="./fields/*[@id='482']/value"/></div>
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
			<div class="P-Figure-Desc"><xsl:copy-of select="./fields/*[@id='482']/value"/></div>
		</div>
		<div class="P-Clear"></div>
	</xsl:template>
	
	<!-- Plate type 1 image preview -->
	<xsl:template match="*[@object_id='231']" mode="singleFigSmallPreview">
		<div style="text-align: center; display: table; width: 90px">
			<div class="twocolumnmini">
				<img alt="">
					<xsl:attribute name="src">/showfigure.php?filename=singlefigmini_<xsl:value-of select="./*[@object_id='225']/fields/*[@id='484']/value"></xsl:value-of>.jpg</xsl:attribute>
				</img>
			</div>
		</div>
		<div style="text-align: center; display: table; width: 90px">
			<div class="twocolumnmini">
				<img alt="">
					<xsl:attribute name="src">/showfigure.php?filename=singlefigmini_<xsl:value-of select="./*[@object_id='226']/fields/*[@id='484']/value"></xsl:value-of>.jpg</xsl:attribute>
				</img>
			</div>
		</div>
	</xsl:template>
	
	<!-- Plate type 2 image preview -->
	<xsl:template match="*[@object_id='232']" mode="singleFigSmallPreview">
		<img style="float: left;"  alt="">
			<xsl:attribute name="src">/showfigure.php?filename=plateportraitmini_<xsl:value-of select="./*[@object_id='225']/fields/*[@id='484']/value"></xsl:value-of>.jpg</xsl:attribute>
		</img>
		<img style="float: left;"  alt="">
			<xsl:attribute name="src">/showfigure.php?filename=plateportraitmini_<xsl:value-of select="./*[@object_id='226']/fields/*[@id='484']/value"></xsl:value-of>.jpg</xsl:attribute>
		</img>
	</xsl:template>
	
	<!-- Plate type 3 image preview -->
	<xsl:template match="*[@object_id='233']" mode="singleFigSmallPreview">
		<style>
			.twocolumnminiholder{width: 51px; height: 51px; float: left;  text-align: center; display: table}
			.twocolumnmini{display: table-cell; vertical-align: middle; text-align: center; padding: 3px}
		</style>
		<div style="">
			<div class="twocolumnminiholder">
				<div class="twocolumnmini">
					<img alt="">
						<xsl:attribute name="src">/showfigure.php?filename=twocolumnmini_<xsl:value-of select="./*[@object_id='225']/fields/*[@id='484']/value"></xsl:value-of>.jpg</xsl:attribute>
					</img>
				</div>
			</div>
			<div class="twocolumnminiholder">
				<div class="twocolumnmini">
					<img alt="">
						<xsl:attribute name="src">/showfigure.php?filename=twocolumnmini_<xsl:value-of select="./*[@object_id='226']/fields/*[@id='484']/value"></xsl:value-of>.jpg</xsl:attribute>
					</img>
				</div>
			</div>
			<div class="twocolumnminiholder">
				<div class="twocolumnmini">
					<img alt="">
						<xsl:attribute name="src">/showfigure.php?filename=twocolumnmini_<xsl:value-of select="./*[@object_id='227']/fields/*[@id='484']/value"></xsl:value-of>.jpg</xsl:attribute>
					</img>
				</div>
			</div>
			<div class="twocolumnminiholder">
				<div class="twocolumnmini">
					<img alt="">
						<xsl:attribute name="src">/showfigure.php?filename=twocolumnmini_<xsl:value-of select="./*[@object_id='228']/fields/*[@id='484']/value"></xsl:value-of>.jpg</xsl:attribute>
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
						<xsl:attribute name="src">/showfigure.php?filename=twocolumnmini_<xsl:value-of select="./*[@object_id='225']/fields/*[@id='484']/value"></xsl:value-of>.jpg</xsl:attribute>
					</img>
				</div>
			</div>
			<div class="twocolumnminiholder">
				<div class="twocolumnmini">
					<img alt="">
						<xsl:attribute name="src">/showfigure.php?filename=twocolumnmini_<xsl:value-of select="./*[@object_id='226']/fields/*[@id='484']/value"></xsl:value-of>.jpg</xsl:attribute>
					</img>
				</div>
			</div>
			<div class="twocolumnminiholder">
				<div class="twocolumnmini">
					<img alt="">
						<xsl:attribute name="src">/showfigure.php?filename=twocolumnmini_<xsl:value-of select="./*[@object_id='227']/fields/*[@id='484']/value"></xsl:value-of>.jpg</xsl:attribute>
					</img>
				</div>
			</div>
			<div class="twocolumnminiholder">
				<div class="twocolumnmini">
					<img alt="">
						<xsl:attribute name="src">/showfigure.php?filename=twocolumnmini_<xsl:value-of select="./*[@object_id='228']/fields/*[@id='484']/value"></xsl:value-of>.jpg</xsl:attribute>
					</img>
				</div>
			</div>
			<div class="twocolumnminiholder">
				<div class="twocolumnmini">
					<img alt="">
						<xsl:attribute name="src">/showfigure.php?filename=twocolumnmini_<xsl:value-of select="./*[@object_id='229']/fields/*[@id='484']/value"></xsl:value-of>.jpg</xsl:attribute>
					</img>
				</div>
			</div>
			<div class="twocolumnminiholder">
				<div class="twocolumnmini">
					<img alt="">
						<xsl:attribute name="src">/showfigure.php?filename=twocolumnmini_<xsl:value-of select="./*[@object_id='230']/fields/*[@id='484']/value"></xsl:value-of>.jpg</xsl:attribute>
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
				<div class="P-Block-Title"><xsl:value-of select="./fields/*[@id='482']/value"/></div>
				<div class="P-Clear"></div>
				<div class="P-Figure-Num">Table <xsl:value-of select="./fields/*[@id='489']/value"/></div>
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
</xsl:stylesheet>
