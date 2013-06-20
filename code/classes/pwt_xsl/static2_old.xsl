<?xml version="1.0"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:tp="http://www.plazi.org/taxpub" xmlns:php="http://php.net/xsl" exclude-result-prefixes="php tp xlink xsl" >

	<!-- MARKING EDITABLE FIELDS TEMPLATE --> 
	<xsl:template name="markContentEditableField">
		<xsl:param name="pObjectId" />
		<xsl:param name="pFieldId" />

		<xsl:if test="$pMarkContentEditableFields &gt; 0">
			<xsl:variable name="lCheck" select="php:function('checkIfObjectFieldIsEditable', string($pObjectId), string($pFieldId))" />
			<xsl:if test="$lCheck &gt; 0">
				<xsl:attribute name="contenteditable">true</xsl:attribute>
			</xsl:if>
		</xsl:if>
	</xsl:template>

	<!-- JOURNAL INFO -->
	<xsl:template name="journalInfo">
		<xsl:param name="pDocumentNode" />

		<xsl:variable name="lJournalName" select="$pDocumentNode/journal_name" />
		<xsl:variable name="lDocumentType" select="$pDocumentNode/document_type" />
		
		<div class="P-Article-Preview-Antet">
			<xsl:value-of select="$lJournalName" /> : <xsl:value-of select="$lDocumentType" />
		</div>
	</xsl:template>
	
	<!-- ARTICLE TITLE -->
	<xsl:template match="*" mode="articleTitle">
		<div class="P-Article-Preview-Title" id="article_metadata">
			<xsl:apply-templates select="." mode="formatting"/>
		</div>
	</xsl:template>

	<!-- AUTHORS -->
	<xsl:template name="authors">
		<xsl:param name="pDocumentNode" />
		<div class="P-Article-Preview-Names">
			<xsl:for-each select="$pDocumentNode/objects/*[@object_id='14']/*[@object_id='9']/*[@object_id='8']">
				<xsl:apply-templates select="." mode="singleAuthor" />
				<xsl:if test="position()!=last()"><xsl:text>, </xsl:text></xsl:if>
			</xsl:for-each>
		</div>
		<div class="P-Article-Preview-Addresses">
			<xsl:for-each select="$pDocumentNode/objects/*[@object_id='14']/*[@object_id='9']/*[@object_id='8']/*[@object_id='5']">
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
								<xsl:for-each select="$pDocumentNode/objects/*[@object_id='14']/*[@object_id='9']/*[@object_id='8'][fields/*[@id='15']/value[@value_id='1']]">
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
								<xsl:for-each select="$pDocumentNode/objects/*[@object_id='14']/*[@object_id='9']/*[@object_id='8']">
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
			<xsl:value-of select="./fields/*[@id=6]" />
		</span>
		<xsl:text> </xsl:text>
		<xsl:if test="count(./fields/*[@id=7]) &gt; 0">
			<span field_id="7">
				<xsl:value-of select="./fields/*[@id=7]" />
			</span>
			<xsl:text> </xsl:text>
		</xsl:if>
		<span field_id="8">
			<xsl:value-of select="./fields/*[@id=8]" />
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
				<xsl:value-of select="./fields/*[@id=6]" />
			</span>
			<xsl:text> </xsl:text>
			<xsl:if test="count(./fields/*[@id=7]) &gt; 0">
				<span field_id="7">
					<xsl:value-of select="./fields/*[@id=7]" />
				</span>
				<xsl:text> </xsl:text>
			</xsl:if>
			<span field_id="8">
				<xsl:value-of select="./fields/*[@id=8]" />
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
							<p>
								<xsl:apply-templates select="./fields/*[@id=19]" mode="formatting"/>
							</p>
						</div>
					</div>
				</xsl:if>
			</div>
	</xsl:template>
	
	<!-- Default empty template.
	The sections we want to match will be specified manually -->
	<xsl:template match="*" mode="bodySections" />

	<!-- Introduction -->
	<xsl:template match="*[@object_id='16']" mode="bodySections">
		<xsl:if test="./fields/*[@id='20']/value != ''">
			<div class="P-Article-Preview-Block">
				<xsl:attribute name="instance_id"><xsl:value-of select="./@instance_id" /></xsl:attribute>
				<h1 id="introduction">Introduction</h1>
				<div class="P-Article-Preview-Block-Content" field_id="20">
					<xsl:call-template name="markContentEditableField">
						<xsl:with-param name="pObjectId" select="./@object_id" />
						<xsl:with-param name="pFieldId">20</xsl:with-param>
					</xsl:call-template>
					<xsl:apply-templates select="./fields/*[@id='20']" mode="formatting"/>
				</div>
				<xsl:apply-templates mode="bodySubsection"/>
			</div>
		</xsl:if>
	</xsl:template>
	
	
	
	<!-- SUBSECTIONS - START -->
	<!-- Default empty template.
		 The sections we want to match will be specified manually
	 -->
	<xsl:template match="*" mode="bodySubsection">
		<xsl:variable name="lSecTitle"><xsl:apply-templates select="./fields/*[@id='211']" mode="formatting" /></xsl:variable>
		<div class="P-Article-Preview-Block-Content">
			<xsl:attribute name="instance_id"><xsl:value-of select="./@instance_id" /></xsl:attribute>
			<h2 class="subsection" field_id="211">
				<xsl:call-template name="markContentEditableField">
					<xsl:with-param name="pObjectId" select="./@object_id" />
					<xsl:with-param name="pFieldId">211</xsl:with-param>
				</xsl:call-template>
				<xsl:value-of select="$lSecTitle" />
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

	<!-- При субсекциите показваме title и content
	 -->
	<xsl:template match="section" mode="bodySubsection">
		<xsl:variable name="lSecTitle"><xsl:apply-templates select="./fields/*[@id='211']" mode="formatting"/></xsl:variable>
		<div class="P-Article-Preview-Block-Content">
			<xsl:attribute name="instance_id"><xsl:value-of select="./@instance_id" /></xsl:attribute>
			<span class="P-Article-Preview-Block-Subsection-Title" field_id="211">
				<xsl:call-template name="markContentEditableField">
					<xsl:with-param name="pObjectId" select="./@object_id" />
					<xsl:with-param name="pFieldId">211</xsl:with-param>
				</xsl:call-template>
				<xsl:value-of select="$lSecTitle" />
			</span>
			<xsl:text> </xsl:text>
			<span field_id="212">
				<xsl:call-template name="markContentEditableField">
					<xsl:with-param name="pObjectId" select="./@object_id" />
					<xsl:with-param name="pFieldId">212</xsl:with-param>
				</xsl:call-template>
				<xsl:apply-templates select="./fields/*[@id='212']" mode="formatting"/>
			</span>
		</div>
	</xsl:template>
	<!-- SUBSECTIONS - END -->
	
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
				<xsl:apply-templates mode="bodySubsection"/>
			</div>
		</xsl:if>
	</xsl:template>

	<!-- Formatting uploaded files spaces -->
	<xsl:template match="*" mode="formatting_uploaded_file">
		<xsl:param name="lFileName"/>
		<xsl:param name="lUploadedFileName"/>
		<xsl:if test="$lUploadedFileName != ''">
			<span class="fieldLabel">Filename:</span><xsl:text>&#160;</xsl:text>
			<xsl:value-of select="normalize-space($lUploadedFileName)"/><xsl:text> </xsl:text>
			
		</xsl:if>
		<xsl:if test="$lFileName != ''">
			<a target="_blank">
				<xsl:attribute name="href"><xsl:text>getfile.php?filename=</xsl:text><xsl:value-of select="normalize-space($lFileName)"/></xsl:attribute>
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
								<xsl:attribute name="class">Supplemantary-File-Section-Label</xsl:attribute>
								<xsl:apply-templates select="./value" mode="formatting_uploaded_file">
									<xsl:with-param name="lFileName" select="$lFileName"></xsl:with-param>
									<xsl:with-param name="lUploadedFileName" select="$lUploadedFileName"></xsl:with-param>
								</xsl:apply-templates>
							</span>
						</xsl:when>
						<xsl:otherwise>
							<span>
								<xsl:attribute name="field_id"><xsl:value-of select="./@id" /></xsl:attribute>
								<xsl:attribute name="class">Supplemantary-File-Section-Label</xsl:attribute>
								<xsl:apply-templates select="./@field_name" mode="formatting"/>
							</span>
							<xsl:text>: </xsl:text>
							<span>
								<xsl:attribute name="class">Supplemantary-P-Inline</xsl:attribute>
								<xsl:apply-templates select="./value" mode="formatting"/>
							</span>
						</xsl:otherwise>
					</xsl:choose>

				</xsl:otherwise>
			</xsl:choose>
		</div>
	</xsl:template>


	<!-- Single figure or plate -->
	<xsl:template match="*" mode="figures">
		<xsl:variable name="lFigId" select="php:function('getFigureId', string(./@id))"></xsl:variable>
		<xsl:choose>
			<xsl:when test="@is_plate"> <!-- plate -->
				<xsl:variable name="lPlateType" select="./@type"></xsl:variable>
					<div class="figure">
						<xsl:attribute name="plate_id"><xsl:value-of select="./@id"/></xsl:attribute>
						<xsl:attribute name="contenteditable">false</xsl:attribute>
						<xsl:attribute name="figure_position"><xsl:value-of select="$lFigId"/></xsl:attribute>
						
							<xsl:choose>
								<xsl:when test="$lPlateType = 1"><!-- 2 rows 1 columns -->
									<xsl:for-each select="./url">
										<div class="">
											<xsl:apply-templates select="." mode="plate_photo">
												<xsl:with-param name="picUrl" select="."></xsl:with-param>
												<xsl:with-param name="picId" select="./@id"></xsl:with-param>
											</xsl:apply-templates>
										</div>
									</xsl:for-each>
								</xsl:when>
								<xsl:when test="$lPlateType = 2"><!-- 1 rows 2 columns -->
									<xsl:for-each select="./url">
										<div class="plate2column">
											<xsl:apply-templates select="." mode="plate_photo">
												<xsl:with-param name="picUrl" select="."></xsl:with-param>
												<xsl:with-param name="picId" select="./@id"></xsl:with-param>
											</xsl:apply-templates>
										</div>
									</xsl:for-each>
								</xsl:when>
								<xsl:when test="$lPlateType = 4"><!-- 3 rows 2 columns -->
									<xsl:for-each select="./url">
										<div class="plate2column">
											<xsl:apply-templates select="." mode="plate_photo">
												<xsl:with-param name="picUrl" select="."></xsl:with-param>
												<xsl:with-param name="picId" select="./@id"></xsl:with-param>
												<xsl:with-param name="picDesc" select="following::photo_description"></xsl:with-param>
											</xsl:apply-templates>
											<xsl:if test="position() mod 2 = 0 and position() != last()">
												<xsl:text disable-output-escaping="yes"><![CDATA[<div class="P-Clear"></div>]]></xsl:text>
											</xsl:if>
										</div>
									</xsl:for-each>
								</xsl:when>
								<xsl:otherwise>
									<div>
										<xsl:attribute name="class"><xsl:text>P-Article-Preview-Picture-Row</xsl:text></xsl:attribute>
										<xsl:for-each select="./url">
											<div class="plate2column">
												<xsl:apply-templates select="." mode="plate_photo">
													<xsl:with-param name="picUrl" select="."></xsl:with-param>
													<xsl:with-param name="picId" select="./@id"></xsl:with-param>
													<xsl:with-param name="picDesc" select="following::photo_description"></xsl:with-param>
												</xsl:apply-templates>
												<xsl:if test="position() mod 2 = 0 and position() != last()">
													<xsl:text disable-output-escaping="yes"><![CDATA[</div><div class="P-Article-Preview-Picture-Row">]]></xsl:text>
												</xsl:if>
											</div>
										</xsl:for-each>
									</div>
								</xsl:otherwise>
							</xsl:choose>
							<div style="clear: both"></div>
							<div class="description">
								<div class="name">
									<xsl:text>Figure </xsl:text><xsl:value-of select="$lFigId" /><xsl:text>. </xsl:text>
								</div>
							
								<xsl:apply-templates select="./caption" mode="formatting"/>
								<xsl:for-each select="./photo_description">
									<b><xsl:number format="a" /></b><xsl:text>:&#160;</xsl:text>
										<xsl:apply-templates  />
									<br />
								</xsl:for-each>
							</div>

					</div>
			</xsl:when>
			<xsl:when test="@is_video"> <!-- video -->
				<div class="figure">
					<xsl:attribute name="figure_id">
						<xsl:value-of select="./@id"/>
					</xsl:attribute>
					<xsl:attribute name="contenteditable">false</xsl:attribute>
					<xsl:attribute name="figure_position">
						<xsl:value-of select="$lFigId"/>
					</xsl:attribute>
					<div class="holder">
						<iframe width="696" height="522" frameborder="0">
							<xsl:attribute name="src">
								<xsl:text>http://www.youtube.com/embed/</xsl:text>
								<xsl:value-of select="php:function('getYouTubeId', string(./url))"/>
							</xsl:attribute>
						</iframe>
					</div>
					<div class="description">
						<div class="name">
							<xsl:text>Figure </xsl:text><xsl:value-of select="$lFigId" /><xsl:text>. </xsl:text>
						</div>
						<xsl:apply-templates select="./caption" mode="formatting"/>
					</div>
					<div class="P-Clear"></div>
				</div>
			</xsl:when>
			<xsl:otherwise> <!-- figure -->
				<div class="figure">
					<xsl:attribute name="figure_id"><xsl:value-of select="./@id"/></xsl:attribute>
					<xsl:attribute name="contenteditable">false</xsl:attribute>
					<xsl:attribute name="figure_position"><xsl:value-of select="$lFigId"/></xsl:attribute>
					<div class="holder">
						<a target="_blank">
							<xsl:attribute name="href">
								<xsl:text>/display_zoomed_figure.php?fig_id=</xsl:text>
								<xsl:value-of select="./@id"/>
							</xsl:attribute>
							<img>
								<xsl:attribute name="src">
									<xsl:value-of select="./url"/>
								</xsl:attribute>
								<xsl:attribute name="alt" />
							</img>
						</a>
						<a target="_blank" class="P-Article-Preview-Picture-Zoom-Small">
							<xsl:attribute name="href">
								<xsl:text>/display_zoomed_figure.php?fig_id=</xsl:text>
								<xsl:value-of select="./@id"/>
							</xsl:attribute>
							
						</a>
					</div>

					<div class="description">
						<div class="name">
							<xsl:text>Figure </xsl:text><xsl:value-of select="$lFigId" /><xsl:text>. </xsl:text>
						</div>
						<xsl:apply-templates select="./caption" mode="formatting"/>
					</div>
					<div class="P-Clear"></div>
				</div>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>

	<!-- Single table -->
	<xsl:template match="*" mode="tables">
		<div class="table">
			<xsl:attribute name="table_id"><xsl:value-of select="./@id"/></xsl:attribute>
			<xsl:attribute name="table_position"><xsl:value-of select="./@position"/></xsl:attribute>
			<div class="description">
				<div class="name">Table <xsl:value-of select="./@position" />.</div>
				<span class="P-Inline"><xsl:apply-templates select="./title" mode="formatting"/></span>
			</div>
			<div class="P-Clear"></div>
			<div class="Table-Body"><xsl:apply-templates select="./description" mode="table_formatting"/></div>
		</div>
	</xsl:template>

	<!-- Single plate photo -->
	<xsl:template match="*" mode="plate_photo">
		<xsl:param name="picUrl"></xsl:param>
		<xsl:param name="picId"></xsl:param>
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
			
			<a target="_blank" class="P-Article-Preview-Picture-Zoom-Small">
				<xsl:attribute name="href">
					<xsl:text>/display_zoomed_figure.php?fig_id=</xsl:text>
					<xsl:value-of select="$picId" />
				</xsl:attribute>
			</a>
			
			<!-- <xsl:value-of select="$picDesc" /> -->
			<div class="Plate-part-letter">
				<xsl:number format="a" />
			</div>
		</div>
	</xsl:template>
</xsl:stylesheet>
