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
						<title><xsl:value-of select="php:function('strip_tags', string(/document/objects/*[@object_id='14']/*[@object_id='9']/fields/*[@id='3']))" /></title>
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


	<!--
	this is called in ../static2.xsl
	if put here as well the Introduction disappears
	<xsl:template match="*" mode="bodySections"></xsl:template>-->

	<!-- Material and Methods -->
	<xsl:template match="*[@object_id='18']" mode="bodySections">
		<xsl:if test="./fields/*[@id='22']/value != '' or ./subsection/@object_id != ''">
			<div class="P-Article-Preview-Block">
				<xsl:attribute name="instance_id"><xsl:value-of select="./@instance_id" /></xsl:attribute>
				<!--anchor-->
				<span class="anchor" id="material_and_methods"></span>
				<h1>Material and methods</h1>
				<xsl:if test="./fields/*[@id='22']/value  != '' " >
				<div class="Section">
					<xsl:call-template name="markContentEditableField">
						<xsl:with-param name="pObjectId" select="./@object_id"></xsl:with-param>
						<xsl:with-param name="pFieldId">22</xsl:with-param>
					</xsl:call-template>
					<xsl:attribute name="field_id">22</xsl:attribute>
					<xsl:apply-templates select="./fields/*[@id='22']" mode="formatting"/>
				</div>
				</xsl:if>
				<xsl:apply-templates mode="bodySubsection" select="./subsection"/>
			</div>
		</xsl:if>
	</xsl:template>

	<!-- Data resources -->
	<xsl:template match="*[@object_id='17']" mode="bodySections">
		<xsl:if test="./fields/*[@id='21']/value != '' or ./subsection/@object_id != ''">
			<div class="P-Article-Preview-Block">
				<xsl:attribute name="instance_id"><xsl:value-of select="./@instance_id" /></xsl:attribute>
				<!--anchor-->
				<span class="anchor" id="data_resources"></span>
				<h1>Data resources</h1>
				<xsl:if test="./fields/*[@id='21']/value != ''" >
				<div class="Section">
					<xsl:attribute name="field_id">21</xsl:attribute>
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

	<!-- Analysis -->
	<xsl:template match="*[@object_id='19']" mode="bodySections">
		<xsl:if test="./fields/*[@id='23']/value != '' or ./subsection/@object_id != ''">
			<div class="P-Article-Preview-Block">
				<xsl:attribute name="instance_id"><xsl:value-of select="./@instance_id" /></xsl:attribute>
				<!--anchor-->
				<span class="anchor" id="results"></span>
				<h1>Analysis</h1>
				<xsl:if test="./fields/*[@id='23']/value != ''" >
				<div class="Section">
					<xsl:attribute name="field_id">23</xsl:attribute>
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
	
	<!-- Discussion -->
	<xsl:template match="*[@object_id='58']" mode="bodySections">
		<xsl:if test="./fields/*[@id='224']/value != '' or ./subsection/@object_id != ''">
			<div class="P-Article-Preview-Block">
				<xsl:attribute name="instance_id"><xsl:value-of select="./@instance_id" /></xsl:attribute>
				<!--anchor-->
				<span class="anchor" id="discussions"></span>
				<h1>Discussion</h1>
				<xsl:if test="./fields/*[@id='224']/value != ''">
				<div class="Section">
					<xsl:attribute name="field_id">224</xsl:attribute>
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

	<!-- Identification keys -->
	<xsl:template match="*[@object_id='24']" mode="bodySections">
		<xsl:if test="(count(./*[@object_id='23']) &gt; 0)">
			<div class="P-Article-Preview-Block">
				<xsl:attribute name="instance_id"><xsl:value-of select="./@instance_id" /></xsl:attribute>
				<h1 id="identification_keys">Identification keys</h1>
				<div class="P-Article-Preview-Block-Content">
					<xsl:for-each select="//*[@object_id='23']">
						<div class="identification-key">
							<xsl:apply-templates select="." mode="singleIdentificationKey"/>
						</div>					
					</xsl:for-each>					
				</div>
			</div>
		</xsl:if>
	</xsl:template>

	<!-- Single identification key -->
	<xsl:template match="*" mode="singleIdentificationKey">
		<table cellspacing="0" cellpadding="0" border="0" width="100%"
			style="border-collapse:collapse;" identification_key_table="1">
			<xsl:attribute name="instance_id"><xsl:value-of select="./@instance_id" /></xsl:attribute>
			<tbody>
				<tr>
					<td align="center" colspan="3" class="P-Article-Preview-Table-Header">
						<div class="Identification_Key_Title">
							<xsl:call-template name="markContentEditableField">
								<xsl:with-param name="pObjectId" select="./@object_id"></xsl:with-param>
								<xsl:with-param name="pFieldId">31</xsl:with-param>
							</xsl:call-template>
							<xsl:attribute name="field_id">31</xsl:attribute>
                            <xsl:value-of select="php:function('h_strip_tags', string(./fields/*[@id='31']/value))" />
						</div>
						<div class="KeyNotes">
							<xsl:call-template name="markContentEditableField">
								<xsl:with-param name="pObjectId" select="./@object_id"></xsl:with-param>
								<xsl:with-param name="pFieldId">32</xsl:with-param>
							</xsl:call-template>
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
				<xsl:call-template name="markContentEditableField">
					<xsl:with-param name="pObjectId" select="$pNode/@object_id"></xsl:with-param>
					<xsl:with-param name="pFieldId">34</xsl:with-param>
				</xsl:call-template>
				<xsl:attribute name="field_id">34</xsl:attribute>
				<xsl:apply-templates select="$pNode/fields/*[@id='34']" mode="formatting"/>
			</td>
			<td class="P-Article-Preview-Table-Row">
				<xsl:if test="$pNode/fields/*[@id='35']!=''">
					<xsl:call-template name="markContentEditableField">
						<xsl:with-param name="pObjectId" select="$pNode/@object_id"></xsl:with-param>
						<xsl:with-param name="pFieldId">35</xsl:with-param>
					</xsl:call-template>
					<xsl:attribute name="field_id">35</xsl:attribute>
					<xsl:apply-templates select="$pNode/fields/*[@id='35']" mode="formatting"/>
				</xsl:if>
				<xsl:if test="$pNode/fields/*[@id='36']!=''">
					<xsl:call-template name="markContentEditableField">
						<xsl:with-param name="pObjectId" select="$pNode/@object_id"></xsl:with-param>
						<xsl:with-param name="pFieldId">36</xsl:with-param>
					</xsl:call-template>
					<xsl:attribute name="field_id">36</xsl:attribute>
					<xsl:apply-templates select="$pNode/fields/*[@id='36']" mode="formatting"/>
				</xsl:if>
			</td>
		</tr>
		<tr>
			<xsl:attribute name="instance_id"><xsl:value-of select="./@instance_id" /></xsl:attribute>
			<td class="P-Article-Preview-Table-Row" align="center">–</td>
			<td class="P-Article-Preview-Table-Row">
				<xsl:call-template name="markContentEditableField">
					<xsl:with-param name="pObjectId" select="$pNode/@object_id"></xsl:with-param>
					<xsl:with-param name="pFieldId">37</xsl:with-param>
				</xsl:call-template>
				<xsl:attribute name="field_id">37</xsl:attribute>
				<xsl:apply-templates select="$pNode/fields/*[@id='37']" mode="formatting"/>
			</td>
			<td class="P-Article-Preview-Table-Row">
				<xsl:if test="$pNode/fields/*[@id='38']!=''">
					<xsl:call-template name="markContentEditableField">
						<xsl:with-param name="pObjectId" select="$pNode/@object_id"></xsl:with-param>
						<xsl:with-param name="pFieldId">38</xsl:with-param>
					</xsl:call-template>
					<xsl:attribute name="field_id">38</xsl:attribute>
					<xsl:apply-templates select="$pNode/fields/*[@id='38']" mode="formatting"/>
				</xsl:if>
				<xsl:if test="$pNode/fields/*[@id='39']!=''">
					<xsl:call-template name="markContentEditableField">
						<xsl:with-param name="pObjectId" select="$pNode/@object_id"></xsl:with-param>
						<xsl:with-param name="pFieldId">39</xsl:with-param>
					</xsl:call-template>
					<xsl:attribute name="field_id">39</xsl:attribute>
					<xsl:apply-templates select="$pNode/fields/*[@id='39']" mode="formatting"/>
				</xsl:if>
			</td>
		</tr>
	</xsl:template>

	<!-- singleIdentificationKeyCouplet v malkoto preview -->
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
		 <colgroup>
			<col width="10%"/>
			<col width="60%"/>
			<col width="30%"/>
		  </colgroup>
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
				<span>>
					<xsl:attribute name="field_id">39</xsl:attribute>
					<xsl:apply-templates select="./fields/*[@id='39']" mode="formatting"/>
				</span>
			</td>
		</tr>
	</xsl:template>

	<!-- Checklists Starts -->
	<!-- Checklists 2.0 -->
	<xsl:template match="*[@object_id='203']" mode="bodySections">
		<xsl:if test="count(./*[@object_id='204']) &gt; 0">
			<xsl:variable name="lSecTitle" select="./@display_name"></xsl:variable>
			<div class="P-Article-Preview-Block">
				<xsl:attribute name="instance_id"><xsl:value-of select="./@instance_id" /></xsl:attribute>
				<xsl:apply-templates select="./*[@object_id='204']" mode="checklist"/>
			</div>
		</xsl:if>
	</xsl:template>

	<!-- Checklist 2.0 -->
	 <xsl:template match="*" mode="checklist">
		<xsl:if test="./fields/*[@id='413']/value != ''">
			<div class="P-Article-Preview-Block">
				<xsl:attribute name="instance_id"><xsl:value-of select="./@instance_id" /></xsl:attribute>
				<xsl:variable name="checklistTitle" select="./fields/*[@id='413']" /> 
				<h1>
					<xsl:call-template name="markContentEditableField">
						<xsl:with-param name="pObjectId">204</xsl:with-param>
						<xsl:with-param name="pFieldId">413</xsl:with-param>
					</xsl:call-template>
					<xsl:attribute name="field_id">413</xsl:attribute>
					<xsl:value-of select="php:function('h_strip_tags', string($checklistTitle))" />
				</h1>
				<div class="Checklist">
					<xsl:apply-templates select="./*[@object_id='205']" mode="checklistTaxon"/>
				</div>
			</div>
		</xsl:if>
	</xsl:template>


	<!-- Checklists End -->

	<!-- Taxon Treatments start -->
	<!-- Systematics -->
	<xsl:template match="*[@object_id='54']" mode="bodySections">
		<xsl:if test="(count(./*[@object_id='41']) &gt; 0) or (./fields/*[@id='40']/value != '')">
			<div class="P-Article-Preview-Block">
				<xsl:attribute name="instance_id"><xsl:value-of select="./@instance_id" /></xsl:attribute>
				<h1>Taxon treatment<xsl:if test="(count(./*[@object_id='41']) &gt; 1)"><xsl:text>s</xsl:text></xsl:if></h1>
				<xsl:apply-templates select="./*[@object_id='41']" mode="taxonTreatment"/>
			</div>
		</xsl:if>
	</xsl:template>


	<!-- Taxon treatment -->
	<xsl:template match="*" mode="taxonTreatment">
		<xsl:variable name="lTreatmentNode" select="."/>

		<xsl:variable name="lSecTitle">
				<xsl:apply-templates select="./*/*[@object_id='70' or @object_id='43' or @object_id='180' or @object_id='181']" mode="taxonTreatmentName"/>
				<xsl:if test="count(.//*[@object_id='184'])"><xsl:text>, gen. n.</xsl:text></xsl:if><!-- New TT genus ICZN -->
				<xsl:if test="count(.//*[@object_id='179'])"><xsl:text>, sp. n.</xsl:text></xsl:if><!-- New TT species ICZN -->
				<xsl:if test="count(.//*[@object_id='192'])"><xsl:text>, gen. nov.</xsl:text></xsl:if><!-- New TT genus ICN -->
				<xsl:if test="count(.//*[@object_id='182'])"><xsl:text>, sp. nov.</xsl:text></xsl:if><!-- New TT species ICN -->

				<xsl:if test="count(.//*[@object_id='68'])">
					<xsl:apply-templates select=".//*[@object_id='63' or @object_id='69']" mode="TTNOriginalCitation"/>
				</xsl:if>
		</xsl:variable>

		<div class="taxonTreatment">
			<xsl:attribute name="instance_id"><xsl:value-of select="./@instance_id" /></xsl:attribute>
			<h2 class="h-treatment"><xsl:copy-of select="$lSecTitle"/></h2>
			<xsl:if test="./fields/*[@id='212'] != ''">
				<div class="P-Article-Preview-Block-Content" field_id="212">
					<xsl:apply-templates select="./fields/*[@id='212']" mode="formatting"/>
				</div>
			</xsl:if>
			
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

		 	<!-- Synonyms -->

			<xsl:if test="count(.//*[@object_id='200']) &gt; 0">
				<xsl:apply-templates select=".//*[@object_id='200']" mode="taxonSynonymsSections"/>
			</xsl:if>
			
			<xsl:if test="count(.//*[@object_id='210']) &gt; 0">
				<xsl:apply-templates select=".//*[@object_id='210']" mode="taxonSynonymsSections2"/>
			</xsl:if>


			<xsl:if test="count(.//*[@object_id='195']) &gt; 0 or
						  count(.//*[@object_id='186']) &gt; 0 or
						  count(.//*[@object_id='215']) &gt; 0 or
						  count(.//*[@object_id='217']) &gt; 0 ">
				<div class="treatmentSection" what="!">	
					<xsl:apply-templates select=".//*[@object_id='195' or @object_id='186']" mode="NewGenusTypeSpecies"/>
					<xsl:apply-templates select=".//*[@object_id='215' or @object_id='217']" mode="GenusRedescriptionTypeSpecies"/>
				</div>
			</xsl:if>

			<xsl:apply-templates select=".//*[@object_id='38']" mode="ttMaterials"/>

			<!-- Treatment sections -->
			
			<!-- ICZN new species -->
			<xsl:apply-templates select=".//*[@object_id='109']/*[@object_id=47]" mode="taxonTreatmentSections"/>
			<xsl:apply-templates select=".//*[@object_id='109']/*[@object_id=48]" mode="taxonTreatmentSections"/>
			<xsl:apply-templates select=".//*[@object_id='109']/*[@object_id=49]" mode="taxonTreatmentSections"/>
			<xsl:apply-templates select=".//*[@object_id='109']/*[@object_id=76]" mode="taxonTreatmentSections"/>
			<xsl:apply-templates select=".//*[@object_id='109']/*[@object_id=77]" mode="taxonTreatmentSections"/>
			<xsl:apply-templates select=".//*[@object_id='109']/*[@object_id=79]" mode="taxonTreatmentSections"/>
			<xsl:apply-templates select=".//*[@object_id='109']/*[@object_id=78]" mode="taxonTreatmentSections"/>
			<xsl:apply-templates select=".//*[@object_id='109']/*[@object_id=75]" mode="taxonTreatmentSections"/>
			<xsl:apply-templates select=".//*[@object_id='109']/*[@object_id=80]" mode="taxonTreatmentSections"/>
			<xsl:apply-templates select=".//*[@object_id='109']/*[@object_id=71]" mode="taxonTreatmentSections"/>
			
			<!-- ICZN new genus -->
			<xsl:apply-templates select=".//*[@object_id='185']/*[@object_id=47]" mode="taxonTreatmentSections"/>
			<xsl:apply-templates select=".//*[@object_id='185']/*[@object_id=48]" mode="taxonTreatmentSections"/>
			<xsl:apply-templates select=".//*[@object_id='185']/*[@object_id=49]" mode="taxonTreatmentSections"/>
			<xsl:apply-templates select=".//*[@object_id='185']/*[@object_id=76]" mode="taxonTreatmentSections"/>
			<xsl:apply-templates select=".//*[@object_id='185']/*[@object_id=77]" mode="taxonTreatmentSections"/>
			<xsl:apply-templates select=".//*[@object_id='185']/*[@object_id=79]" mode="taxonTreatmentSections"/>
			<xsl:apply-templates select=".//*[@object_id='185']/*[@object_id=78]" mode="taxonTreatmentSections"/>
			<xsl:apply-templates select=".//*[@object_id='185']/*[@object_id=75]" mode="taxonTreatmentSections"/>
			<xsl:apply-templates select=".//*[@object_id='185']/*[@object_id=80]" mode="taxonTreatmentSections"/>
			<xsl:apply-templates select=".//*[@object_id='185']/*[@object_id=71]" mode="taxonTreatmentSections"/>
			
			<!-- ICZN redescription species -->
			<xsl:apply-templates select=".//*[@object_id='198']/*[@object_id=47]" mode="taxonTreatmentSections"/>
			<xsl:apply-templates select=".//*[@object_id='198']/*[@object_id=48]" mode="taxonTreatmentSections"/>
			<xsl:apply-templates select=".//*[@object_id='198']/*[@object_id=76]" mode="taxonTreatmentSections"/>
			<xsl:apply-templates select=".//*[@object_id='198']/*[@object_id=77]" mode="taxonTreatmentSections"/>
			<xsl:apply-templates select=".//*[@object_id='198']/*[@object_id=79]" mode="taxonTreatmentSections"/>
			<xsl:apply-templates select=".//*[@object_id='198']/*[@object_id=78]" mode="taxonTreatmentSections"/>
			<xsl:apply-templates select=".//*[@object_id='198']/*[@object_id=75]" mode="taxonTreatmentSections"/>
			<xsl:apply-templates select=".//*[@object_id='198']/*[@object_id=80]" mode="taxonTreatmentSections"/>
			<xsl:apply-templates select=".//*[@object_id='198']/*[@object_id=71]" mode="taxonTreatmentSections"/>
						
			<!-- IC(Z)N redescription genus -->
			<xsl:apply-templates select=".//*[@object_id='214']/*[@object_id=47]" mode="taxonTreatmentSections"/>
			<xsl:apply-templates select=".//*[@object_id='214']/*[@object_id=48]" mode="taxonTreatmentSections"/><!-- missing -->
			<xsl:apply-templates select=".//*[@object_id='214']/*[@object_id=76]" mode="taxonTreatmentSections"/>
			<xsl:apply-templates select=".//*[@object_id='214']/*[@object_id=77]" mode="taxonTreatmentSections"/>
			<xsl:apply-templates select=".//*[@object_id='214']/*[@object_id=79]" mode="taxonTreatmentSections"/>
			<xsl:apply-templates select=".//*[@object_id='214']/*[@object_id=78]" mode="taxonTreatmentSections"/>
			<xsl:apply-templates select=".//*[@object_id='214']/*[@object_id=75]" mode="taxonTreatmentSections"/>
			<xsl:apply-templates select=".//*[@object_id='214']/*[@object_id=80]" mode="taxonTreatmentSections"/><!-- missing -->
			<xsl:apply-templates select=".//*[@object_id='214']/*[@object_id=71]" mode="taxonTreatmentSections"/>
			
			<!-- ICN new species -->
			<xsl:apply-templates select=".//*[@object_id='140']/*[@object_id=47]" mode="taxonTreatmentSections"/>
			<xsl:apply-templates select=".//*[@object_id='140']/*[@object_id=48]" mode="taxonTreatmentSections"/>
			<xsl:apply-templates select=".//*[@object_id='140']/*[@object_id=49]" mode="taxonTreatmentSections"/>
			<xsl:apply-templates select=".//*[@object_id='140']/*[@object_id=76]" mode="taxonTreatmentSections"/>
			<xsl:apply-templates select=".//*[@object_id='140']/*[@object_id=77]" mode="taxonTreatmentSections"/>
			<xsl:apply-templates select=".//*[@object_id='140']/*[@object_id=79]" mode="taxonTreatmentSections"/>
			<xsl:apply-templates select=".//*[@object_id='140']/*[@object_id=78]" mode="taxonTreatmentSections"/>
			<xsl:apply-templates select=".//*[@object_id='140']/*[@object_id=75]" mode="taxonTreatmentSections"/>
			<xsl:apply-templates select=".//*[@object_id='140']/*[@object_id=80]" mode="taxonTreatmentSections"/>
			<xsl:apply-templates select=".//*[@object_id='140']/*[@object_id=71]" mode="taxonTreatmentSections"/>
			
			<!-- ICN new genus -->
			<xsl:apply-templates select=".//*[@object_id='193']/*[@object_id=47]" mode="taxonTreatmentSections"/>
			<xsl:apply-templates select=".//*[@object_id='193']/*[@object_id=48]" mode="taxonTreatmentSections"/>
			<xsl:apply-templates select=".//*[@object_id='193']/*[@object_id=49]" mode="taxonTreatmentSections"/>
			<xsl:apply-templates select=".//*[@object_id='193']/*[@object_id=76]" mode="taxonTreatmentSections"/>
			<xsl:apply-templates select=".//*[@object_id='193']/*[@object_id=77]" mode="taxonTreatmentSections"/>
			<xsl:apply-templates select=".//*[@object_id='193']/*[@object_id=79]" mode="taxonTreatmentSections"/>
			<xsl:apply-templates select=".//*[@object_id='193']/*[@object_id=78]" mode="taxonTreatmentSections"/>
			<xsl:apply-templates select=".//*[@object_id='193']/*[@object_id=75]" mode="taxonTreatmentSections"/><!-- missing -->
			<xsl:apply-templates select=".//*[@object_id='193']/*[@object_id=80]" mode="taxonTreatmentSections"/>
			<xsl:apply-templates select=".//*[@object_id='193']/*[@object_id=71]" mode="taxonTreatmentSections"/>
			
			<!-- ICZN redescription species -->
			<xsl:apply-templates select=".//*[@object_id='199']/*[@object_id=47]" mode="taxonTreatmentSections"/>
			<xsl:apply-templates select=".//*[@object_id='199']/*[@object_id=48]" mode="taxonTreatmentSections"/>
			<xsl:apply-templates select=".//*[@object_id='199']/*[@object_id=76]" mode="taxonTreatmentSections"/>
			<xsl:apply-templates select=".//*[@object_id='199']/*[@object_id=77]" mode="taxonTreatmentSections"/>
			<xsl:apply-templates select=".//*[@object_id='199']/*[@object_id=79]" mode="taxonTreatmentSections"/>
			<xsl:apply-templates select=".//*[@object_id='199']/*[@object_id=78]" mode="taxonTreatmentSections"/>
			<xsl:apply-templates select=".//*[@object_id='199']/*[@object_id=75]" mode="taxonTreatmentSections"/>
			<xsl:apply-templates select=".//*[@object_id='199']/*[@object_id=80]" mode="taxonTreatmentSections"/>
			<xsl:apply-templates select=".//*[@object_id='199']/*[@object_id=71]" mode="taxonTreatmentSections"/>
			
		</div>
	</xsl:template>

	<!--  Taxon treatment external link -->
	<xsl:template match="*" mode="TTExternalLinks">
		<xsl:variable name="lTreatmentURLType" select="./fields/*[@id='52']/value/@value_id"/>
		<!--  Тип на линка -->
		<xsl:variable name="lTreatmentURLPrefix">
			<xsl:choose>
				<xsl:when test="$lTreatmentURLType='1'"><xsl:text>http://zoobank.org/?lsid=</xsl:text></xsl:when>
				<xsl:when test="$lTreatmentURLType='2'"><xsl:text>http://www.morphbank.net/?id=</xsl:text></xsl:when>
				<xsl:when test="$lTreatmentURLType='3'"><xsl:text>http://www.ncbi.nlm.nih.gov/nuccore/</xsl:text></xsl:when>
				<xsl:when test="$lTreatmentURLType='4'"><xsl:text></xsl:text></xsl:when>
				<xsl:when test="$lTreatmentURLType='5'"><xsl:text>http://ipni.org/</xsl:text></xsl:when>
				<xsl:when test="$lTreatmentURLType='6'"><xsl:text>http://www.mycobank.org/MB/</xsl:text></xsl:when>
				<xsl:when test="$lTreatmentURLType='7'"><xsl:text>http://www.indexfungorum.org/names/NamesRecord.asp?RecordID=</xsl:text></xsl:when>
				<xsl:when test="$lTreatmentURLType='8'"><xsl:text>http://www.barcodinglife.org/index.php/Public_RecordView?processid=</xsl:text></xsl:when>
				<xsl:otherwise><xsl:text></xsl:text></xsl:otherwise>
			</xsl:choose>
		</xsl:variable>
		<li>
			<xsl:attribute name="instance_id"><xsl:value-of select="./@instance_id" /></xsl:attribute>
			<xsl:attribute name="field_id">53</xsl:attribute>
			<xsl:variable name="label_field_id">
				<xsl:choose>
					<xsl:when test="$lTreatmentURLType ='4'">479</xsl:when>
					<xsl:otherwise>52</xsl:otherwise>
				</xsl:choose>
			</xsl:variable>
			<xsl:apply-templates select="./fields/*[@id='53']" mode="formatting_treatment_link">
				<xsl:with-param name="lLinkPrefix" select="$lTreatmentURLPrefix"/>
				<xsl:with-param name="lTextPrefix" select="./fields/*[@id=$label_field_id]/value" />
			</xsl:apply-templates>
		</li>
	</xsl:template>



	<!-- Taxon family name -->
	<xsl:template match="*[@object_id='70']" mode="taxonTreatmentName">
		<span>
			<xsl:attribute name="instance_id"><xsl:value-of select="./@instance_id" /></xsl:attribute>
			<xsl:attribute name="field_id">241</xsl:attribute>
			<xsl:apply-templates select="./fields/*[@id='241']" mode="formatting"/>
		</span>
	</xsl:template>

	<!-- Type species ICZN Taxon name species -->
	<xsl:template match="*[@object_id='180']" mode="taxonTreatmentName">
				<i><xsl:call-template name="markContentEditableField">
						<xsl:with-param name="pObjectId" select="./@object_id" />
						<xsl:with-param name="pFieldId">48</xsl:with-param>
					</xsl:call-template>
					<xsl:attribute name="field_id">48</xsl:attribute>
					<xsl:attribute name="instance_id"><xsl:value-of select="./@instance_id" /></xsl:attribute>
					<xsl:apply-templates select="./fields/*[@id='48']" mode="formatting_nospace"/></i>
	
			<xsl:if test="./fields/*[@id='417']/value != ''">
			<xsl:text>&#160;(</xsl:text>
					<i><xsl:call-template name="markContentEditableField">
						<xsl:with-param name="pObjectId" select="./@object_id" />
						<xsl:with-param name="pFieldId">417</xsl:with-param>
					</xsl:call-template>
					<xsl:attribute name="field_id">417</xsl:attribute>
					<xsl:attribute name="instance_id"><xsl:value-of select="./@instance_id" /></xsl:attribute>
					<xsl:apply-templates select="./fields/*[@id='417']" mode="formatting_nospace"/></i>
					<xsl:text>)</xsl:text>
			</xsl:if>
			
			<xsl:text>&#160;</xsl:text>
				<i><xsl:call-template name="markContentEditableField">
						<xsl:with-param name="pObjectId" select="./@object_id" />
						<xsl:with-param name="pFieldId">49</xsl:with-param>
					</xsl:call-template>
					<xsl:attribute name="field_id">49</xsl:attribute>
					<xsl:attribute name="instance_id"><xsl:value-of select="./@instance_id" /></xsl:attribute>
					<xsl:apply-templates select="./fields/*[@id='49']" mode="formatting_nospace"/></i>

		<xsl:text>&#160;</xsl:text>
		<xsl:apply-templates select="." mode="authors_and_year" />
	</xsl:template>
	
	
	<!-- Type species ICN Taxon name species -->
	<xsl:template match="*[@object_id='220']" mode="taxonTreatmentName">
				<i><xsl:call-template name="markContentEditableField">
						<xsl:with-param name="pObjectId" select="./@object_id" />
						<xsl:with-param name="pFieldId">48</xsl:with-param>
					</xsl:call-template>
					<xsl:attribute name="field_id">48</xsl:attribute>
					<xsl:attribute name="instance_id"><xsl:value-of select="./@instance_id" /></xsl:attribute>
					<xsl:apply-templates select="./fields/*[@id='48']" mode="formatting_nospace"/></i>
	
			<xsl:if test="./fields/*[@id='417']/value != ''">
			<xsl:text>&#160;(</xsl:text>
					<i><xsl:call-template name="markContentEditableField">
						<xsl:with-param name="pObjectId" select="./@object_id" />
						<xsl:with-param name="pFieldId">417</xsl:with-param>
					</xsl:call-template>
					<xsl:attribute name="field_id">417</xsl:attribute>
					<xsl:attribute name="instance_id"><xsl:value-of select="./@instance_id" /></xsl:attribute>
					<xsl:apply-templates select="./fields/*[@id='417']" mode="formatting_nospace"/></i>
					<xsl:text>)</xsl:text>
			</xsl:if>
			
			<xsl:text>&#160;</xsl:text>
				<i><xsl:call-template name="markContentEditableField">
						<xsl:with-param name="pObjectId" select="./@object_id" />
						<xsl:with-param name="pFieldId">49</xsl:with-param>
					</xsl:call-template>
					<xsl:attribute name="field_id">49</xsl:attribute>
					<xsl:attribute name="instance_id"><xsl:value-of select="./@instance_id" /></xsl:attribute>
					<xsl:apply-templates select="./fields/*[@id='49']" mode="formatting_nospace"/></i>
			
			<!-- Basionym author -->
			<xsl:if test="./fields/*[@id='478']/value != ''">
				<xsl:text> (</xsl:text>	
				<span field_id="478">
					<xsl:call-template name="markContentEditableField">
						<xsl:with-param name="pObjectId" select="./@object_id"></xsl:with-param>
						<xsl:with-param name="pFieldId">478</xsl:with-param>
					</xsl:call-template>
					<xsl:attribute name="instance_id"><xsl:value-of select="./@instance_id" /></xsl:attribute>
					<xsl:apply-templates select="./fields/*[@id='478']" mode="formatting_nospace"/> 
				</span>
				<xsl:text>)</xsl:text>
			</xsl:if>
			
		<xsl:text>&#160;</xsl:text>
		<xsl:apply-templates select="." mode="authors_and_year" />
	</xsl:template>

	<xsl:template match="*" mode="authors_and_year">
		<span field_id="50">
				<xsl:call-template name="markContentEditableField">
					<xsl:with-param name="pObjectId" select="./@object_id" />
					<xsl:with-param name="pFieldId">50</xsl:with-param>
				</xsl:call-template>
				<xsl:attribute name="instance_id"><xsl:value-of select="./@instance_id" /></xsl:attribute>
				<xsl:apply-templates select="./fields/*[@id='50']" mode="formatting_nospace"/>
		</span>		
	</xsl:template>

	<!-- Taxon name Genus -->
	<xsl:template match="*[@object_id='181']" mode="taxonTreatmentName">
		<span class="taxonTreatmentName">
			<i>
				<xsl:call-template name="markContentEditableField">
					<xsl:with-param name="pObjectId" select="./@object_id" />
					<xsl:with-param name="pFieldId">48</xsl:with-param>
				</xsl:call-template>
				<xsl:attribute name="field_id">48</xsl:attribute>
				<xsl:attribute name="instance_id"><xsl:value-of select="./@instance_id" /></xsl:attribute>
				<xsl:apply-templates select="./fields/*[@id='48']" mode="formatting"/>
			</i>
		</span>
		<xsl:text>&#160;</xsl:text>
		<xsl:apply-templates select="." mode="authors_and_year" />
	</xsl:template>

	<!-- Taxon new Genus Type species -->
	<xsl:template match="*" mode="NewGenusTypeSpecies">
		<xsl:if test="./fields/*[@id='441'] != '' and ./fields/*[@id='441'] != ''">
			<h3 class="h-treatment-section">Type species</h3>
			<div class="typeSpeciesIndent">
				<xsl:attribute name="instance_id"><xsl:value-of select="./@instance_id" /></xsl:attribute>
					<!-- Genus	 -->
					<i field_id="441">
						<xsl:call-template name="markContentEditableField">
							<xsl:with-param name="pObjectId" select="./@object_id"></xsl:with-param>
							<xsl:with-param name="pFieldId">441</xsl:with-param>
						</xsl:call-template>
						<xsl:attribute name="instance_id"><xsl:value-of select="./@instance_id" /></xsl:attribute>
						<xsl:apply-templates select="./fields/*[@id='441']" mode="formatting"/>
					</i>
				<xsl:text> </xsl:text>
				
				<!-- Species -->
					<i field_id="442">
						<xsl:call-template name="markContentEditableField">
							<xsl:with-param name="pObjectId" select="./@object_id"></xsl:with-param>
							<xsl:with-param name="pFieldId">442</xsl:with-param>
						</xsl:call-template>
						<xsl:attribute name="instance_id"><xsl:value-of select="./@instance_id" /></xsl:attribute>
						<xsl:apply-templates select="./fields/*[@id='442']" mode="formatting"/>
					</i>
				<xsl:text> </xsl:text>
				
				<!-- Taxon Author -->
				<span field_id="443">
					<xsl:call-template name="markContentEditableField">
						<xsl:with-param name="pObjectId" select="./@object_id"></xsl:with-param>
						<xsl:with-param name="pFieldId">443</xsl:with-param>
					</xsl:call-template>
					<xsl:attribute name="instance_id"><xsl:value-of select="./@instance_id" /></xsl:attribute>
					<xsl:apply-templates select="./fields/*[@id='443']" mode="formatting"/>
				</span>
				
				<!-- new species described in this paper -->
				<xsl:if test="./fields/*[@id='440']/value/@value_id = 1">
					<xsl:choose>
						<xsl:when test="../../fields/*[@id='384']/value = 6 or ../../fields/*[@id='384']/value = 7"><xsl:text>, sp. nov.</xsl:text></xsl:when>
						<xsl:otherwise>, sp. n.</xsl:otherwise>
					</xsl:choose>
				</xsl:if>
				
				<!-- Basionym author -->
				<xsl:if test="./fields/*[@id='454']/value != ''">
					<xsl:text> (</xsl:text>	
					<span field_id="454">
						<xsl:call-template name="markContentEditableField">
							<xsl:with-param name="pObjectId" select="./@object_id"></xsl:with-param>
							<xsl:with-param name="pFieldId">454</xsl:with-param>
						</xsl:call-template>
						<xsl:attribute name="instance_id"><xsl:value-of select="./@instance_id" /></xsl:attribute>
						<xsl:apply-templates select="./fields/*[@id='454']" mode="formatting_nospace"/> 
					</span>
					<xsl:text>)</xsl:text>
				</xsl:if>
				
				<!-- Citations -->
				<xsl:apply-templates mode="taxonCitations" select="." />
			</div>
		</xsl:if>
	</xsl:template>
		
	<!-- Citations -->
	<xsl:template match="*" mode="taxonCitations">
		<xsl:if test=".//*[@object_id='178']/fields/*[@id='438']/value != ''">
			<xsl:text> - </xsl:text>
			<xsl:for-each select=".//*[@object_id='201']">
				<span field_id="438">
					<xsl:apply-templates select=".//*[@object_id='178']/fields/*[@id='438']/value" mode="formatting_nospace"/> 
				</span>
				
				<xsl:if test="./fields/*[@id='461']/value != ''">
					<xsl:text> [</xsl:text>
					<span field_id="461">
						<xsl:call-template name="markContentEditableField">
							<xsl:with-param name="pObjectId" select="./@object_id"></xsl:with-param>
							<xsl:with-param name="pFieldId">461</xsl:with-param>
						</xsl:call-template>
						<xsl:attribute name="instance_id"><xsl:value-of select="./@instance_id" /></xsl:attribute>
						<xsl:apply-templates select="./fields/*[@id='461']/value" mode="formatting_nospace"/>
					</span>
					<xsl:text>]</xsl:text>
				</xsl:if>
						
				<xsl:if test="./fields/*[@id='462']/value != ''">
					<xsl:text>: </xsl:text>
					<span field_id="462">
						<xsl:call-template name="markContentEditableField">
							<xsl:with-param name="pObjectId" select="./@object_id"></xsl:with-param>
							<xsl:with-param name="pFieldId">462</xsl:with-param>
						</xsl:call-template>
						<xsl:attribute name="field_id">462</xsl:attribute>
						<xsl:attribute name="instance_id"><xsl:value-of select="./@instance_id" /></xsl:attribute>
						<xsl:apply-templates  select="./fields/*[@id='462']/value" mode="formatting_nospace"/>
					</span>
				</xsl:if>		
				<xsl:if test="position() != last()"><xsl:text>; </xsl:text></xsl:if>
				<xsl:if test="position()  = last()"><xsl:text>. </xsl:text></xsl:if>
			</xsl:for-each>
		</xsl:if>	
	</xsl:template>



	<xsl:template match="*" mode="GenusRedescriptionTypeSpecies">
			<xsl:if test="./*[@object_id='180' or @object_id='220']/fields/*[@id='48'] != '' and 
						  ./*[@object_id='180' or @object_id='220']/fields/*[@id='49'] != ''">
				<h3 class="h-treatment-section">Type species</h3>	
				<!-- species name ICZN -->
				<div class="typeSpeciesIndent"> 
					<xsl:apply-templates mode="taxonTreatmentName" select="./taxon_name" />
					<!-- species name ICN (with basyonym) --> 
					<xsl:apply-templates mode="taxonTreatmentName" select="./tt_species_name_with_basionym" />			
					<!-- citations -->
					<xsl:apply-templates mode="taxonCitations" select="*[@object_id='187']" />
				</div>	
				<!-- synonymys  -->
				<xsl:if test="count(./*[@object_id='219']/*[@object_id='218']) &gt; 0">
					<div class="treatmentSection" style="color:#404040">
						<h3 class="h-treatment-section"><i>Synonymys of the type species</i></h3>
							<xsl:for-each select="./*[@object_id='219']/*[@object_id='218']">
								<div class="typeSpeciesIndent"><xsl:apply-templates select="." mode="Synonymy" /></div>
							</xsl:for-each>
					</div>	
				</xsl:if>
			</xsl:if>
	</xsl:template>
	
	<xsl:template match="*" mode="Synonymy">
		<xsl:apply-templates select="./*[@object_id='180']" mode="taxonTreatmentName"/>
		<xsl:apply-templates mode="taxonCitations" select="." />
	</xsl:template>

	<!-- Taxon species name  -->
	<xsl:template match="*[@object_id='43']" mode="taxonTreatmentName">
		<span class="taxonTreatmentName">
			<xsl:attribute name="instance_id"><xsl:value-of select="./@instance_id" /></xsl:attribute>
			<span field_id="48"><i><xsl:apply-templates select="./fields/*[@id='48']" mode="formatting"/></i></span>
			<xsl:text> </xsl:text>
			<span field_id="49"><i><xsl:apply-templates select="./fields/*[@id='49']" mode="formatting"/></i></span>
		</span>
	</xsl:template>

	<!-- Taxon treatment author -->
	<xsl:template match="*" mode="taxonTreatmentAuthor">
		<span>
			<xsl:attribute name="instance_id"><xsl:value-of select="./@instance_id" /></xsl:attribute>
			<xsl:apply-templates select="." mode="formatting_nospace"/>
		</span>
	</xsl:template>



	<!-- Taxon Synonyms Specie Section -->
	<xsl:template match="*" mode="taxonSynonymsSections">
		<xsl:variable name="lSecTitle"/>
		<xsl:if test="./fields/*[@id='460']/value != ''">
			<div class="treatmentSection">
				<xsl:attribute name="instance_id"><xsl:value-of select="./@instance_id" /></xsl:attribute>
		
				<xsl:if test="count(./fields/*[@id='460']) &gt; 0">
					<xsl:attribute name="field_id">200</xsl:attribute>
				</xsl:if>
				<h3 class="h-treatment-section">Nomenclature</h3>
	
				<div field_id="460" class="P-Inline">
					<xsl:call-template name="markContentEditableField">
						<xsl:with-param name="pObjectId" select="./@object_id"></xsl:with-param>
						<xsl:with-param name="pFieldId">460</xsl:with-param>
					</xsl:call-template>
					<xsl:apply-templates select="./fields/*[@id='460']/value" mode="formatting"/>
				</div>
			</div>
		</xsl:if>
	</xsl:template> 
	
	<!-- Taxon Nomenclature Genus Section -->
	<xsl:template match="*" mode="taxonSynonymsSections2">
		<xsl:variable name="lSecTitle"/>
		<xsl:if test="./fields/*[@id='474']/value != ''">
			<div class="treatmentSection">
				<xsl:attribute name="instance_id"><xsl:value-of select="./@instance_id" /></xsl:attribute>

					<xsl:if test="count(./fields/*[@id='474']) &gt; 0">
						<xsl:attribute name="field_id">210</xsl:attribute>
					</xsl:if>
					<h3 class="h-treatment-section">Nomenclature</h3>

				<div field_id="474" class="P-Inline">
					<xsl:call-template name="markContentEditableField">
						<xsl:with-param name="pObjectId" select="./@object_id"></xsl:with-param>
						<xsl:with-param name="pFieldId">474</xsl:with-param>
					</xsl:call-template>
					<xsl:apply-templates select="./fields/*[@id='474']/value" mode="formatting"/>
				</div>
			</div>
		</xsl:if>
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
		<xsl:if test="./fields/*[@id='212']/value != '' or ./subsection/@object_id != ''">
			<div class="treatmentSection" what="?">
				<xsl:attribute name="instance_id"><xsl:value-of select="./@instance_id" /></xsl:attribute>
				<h3 class="h-treatment-section">
					<xsl:if test="count(./fields/*[@id='211']) &gt; 0">
						<xsl:attribute name="field_id">211</xsl:attribute>
					</xsl:if>
					<xsl:copy-of select="$lSecTitle"/>
				</h3>
				<div field_id="212" class="P-Inline">
					<xsl:call-template name="markContentEditableField">
						<xsl:with-param name="pObjectId" select="./@object_id"></xsl:with-param>
						<xsl:with-param name="pFieldId">212</xsl:with-param	>
					</xsl:call-template>
					<xsl:apply-templates select="./fields/*[@id='212']/value" mode="formatting"/>
				</div>
				<xsl:apply-templates select="./*[@object_id='50']" mode="taxonTreatmentSubSections"/>
			</div>
		</xsl:if>
	</xsl:template>

	<!-- TaxonTreatmentSubSection -->
	<xsl:template match="*" mode="taxonTreatmentSubSections">
		<xsl:variable name="lSecTitle">
			<xsl:apply-templates select="./fields/*[@id='211']/value" mode="formatting"/>
		</xsl:variable>

		<div class="myfieldHolder">
			<xsl:attribute name="instance_id"><xsl:value-of select="./@instance_id" /></xsl:attribute>
			<span class="fieldLabel">
				<xsl:attribute name="field_id">211</xsl:attribute>
				<xsl:copy-of select="$lSecTitle"/>:&#160;
			</span>
			<div class="fieldValue" field_id="212">
				<xsl:call-template name="markContentEditableField">
					<xsl:with-param name="pObjectId" select="./@object_id"></xsl:with-param>
					<xsl:with-param name="pFieldId">212</xsl:with-param>
				</xsl:call-template>
				<xsl:attribute name="class">P-Inline</xsl:attribute>
				<xsl:apply-templates select="./fields/*[@id='212']/value" mode="formatting"/>
			</div>
		</div>
	</xsl:template>
	<!-- Taxon Treatments end -->
	<!--<xsl:template match="*" mode="articleBack"></xsl:template>-->
</xsl:stylesheet>