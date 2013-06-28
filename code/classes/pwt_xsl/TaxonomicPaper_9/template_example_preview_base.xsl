<?xml version='1.0'?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:tp="http://www.plazi.org/taxpub"  xmlns:php="http://php.net/xsl" exclude-result-prefixes="php tp xlink xsl">
	<xsl:param  name="gGenerateFullHtml">1</xsl:param>
	<xsl:param  name="pDocumentId">0</xsl:param>
	<xsl:param  name="pMarkContentEditableFields">0</xsl:param>
	<xsl:param  name="pShowPreviewCommentTip">1</xsl:param>
	<xsl:param  name="pPutEditableJSAndCss">0</xsl:param>
	<xsl:param  name="pTrackFigureAndTableChanges">0</xsl:param>

	<xsl:key name="materialType" match="*[@object_id='37']" use=".//*/fields/*[@id='209']/value/@value_id"></xsl:key>
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

	<!-- Formats treatment links -->
	<xsl:template match="*" mode="formatting_treatment_link">

		<xsl:param name="lTextPrefix"/>
		<xsl:param name="lLinkPrefix"/>
		<xsl:param name="lCurrentVal" select="." />
		<xsl:variable name="lURLsuffix">
			<xsl:choose>
				<xsl:when test="contains($lCurrentVal, 'urn:lsid:indexfungorum.org:names:')">
					<xsl:value-of select="substring($lCurrentVal, 34)" />
				</xsl:when>
				<xsl:otherwise><xsl:value-of select="$lCurrentVal" /></xsl:otherwise>
			</xsl:choose>
		</xsl:variable>

		<xsl:value-of select="normalize-space($lTextPrefix)"/>
		<xsl:text> </xsl:text>
		<a>
			<xsl:attribute name="href"><xsl:value-of select="translate(normalize-space(concat($lLinkPrefix, $lURLsuffix)) , ' ', '')"/></xsl:attribute>
			<xsl:attribute name="target"><xsl:text>_blank</xsl:text></xsl:attribute>
			<xsl:value-of select="normalize-space($lCurrentVal)"/>
		</a>
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
				<div class="Section">
					<xsl:call-template name="markContentEditableField">
						<xsl:with-param name="pObjectId" select="./@object_id"></xsl:with-param>
						<xsl:with-param name="pFieldId">22</xsl:with-param>
					</xsl:call-template>
					<xsl:attribute name="field_id">22</xsl:attribute>
					<xsl:apply-templates select="./fields/*[@id='22']" mode="formatting"/>
				</div>
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
				<div class="Section">
					<xsl:attribute name="field_id">21</xsl:attribute>
					<xsl:call-template name="markContentEditableField">
						<xsl:with-param name="pObjectId" select="./@object_id"></xsl:with-param>
						<xsl:with-param name="pFieldId">21</xsl:with-param>
					</xsl:call-template>
					<xsl:apply-templates select="./fields/*[@id='21']" mode="formatting"/>
				</div>
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
				<div class="Section">
					<xsl:attribute name="field_id">23</xsl:attribute>
					<xsl:call-template name="markContentEditableField">
						<xsl:with-param name="pObjectId" select="./@object_id"></xsl:with-param>
						<xsl:with-param name="pFieldId">23</xsl:with-param>
					</xsl:call-template>
					<xsl:apply-templates select="./fields/*[@id='23']" mode="formatting"/>
				</div>
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
				<div class="Section">
					<xsl:attribute name="field_id">224</xsl:attribute>
					<xsl:call-template name="markContentEditableField">
						<xsl:with-param name="pObjectId" select="./@object_id"></xsl:with-param>
						<xsl:with-param name="pFieldId">224</xsl:with-param>
					</xsl:call-template>
					<xsl:apply-templates select="./fields/*[@id='224']" mode="formatting"/>
				</div>
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

	<xsl:template match="*[@object_id='38']" mode="ttMaterials">
		<xsl:variable name="lGroupedMaterials" select="php:function('GroupTreatmentMaterials', ./*[@object_id=37])"></xsl:variable>
		<xsl:if test="count($lGroupedMaterials/materials/material_group) &gt; 0">
			<div class="myfieldHolder otstapLeft">
				<div class="fieldLabel no-float otstapBottom materialsTitle">Materials</div>
				<xsl:for-each select="$lGroupedMaterials/materials/material_group">
					<xsl:variable name="lMaterialTypeId" select="./@value_id"></xsl:variable>
					<xsl:variable name="lMaterialTypeName" select="./value"></xsl:variable>
					<div class="materialType">
						<div class="MaterialType">
							<i>
								<xsl:value-of select="$lMaterialTypeName"></xsl:value-of>
								<xsl:if test="count(./*[@object_id='37']) &gt; 1"><xsl:text>s</xsl:text></xsl:if><xsl:text>: </xsl:text>
							</i>
						</div>
						<ol class="materialsHolder">
						<xsl:for-each select="./*[@object_id='37']">
							<li type="a">
								<xsl:apply-templates select="." mode="treatmentMaterial"></xsl:apply-templates>
								<xsl:if test="position() != last()"><xsl:text>;</xsl:text></xsl:if>
								<xsl:if test="position() = last()"><xsl:text>.</xsl:text></xsl:if>
							</li>
						</xsl:for-each>
						</ol>

					</div>
				</xsl:for-each>
			</div>
		</xsl:if>
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
				<h1><xsl:value-of select="php:function('h_strip_tags', string($checklistTitle))" /></h1>
				<div class="Checklist">
					<xsl:apply-templates select="./*[@object_id='205']" mode="checklistTaxon"/>
				</div>
			</div>
		</xsl:if>
	</xsl:template>

	<!-- Checklist 2.0 Taxon 2.0 -->
	<xsl:template match="*" mode="checklistTaxon">
		<xsl:variable name="lTreatmentNode" select="."></xsl:variable>
		<xsl:apply-templates select="." mode="checklistTaxonForm"/>
		<xsl:if test="count(.//*[@object_id='39'])">
			<ul>
				<xsl:apply-templates select=".//*[@object_id='39']" mode="TTExternalLinks"/>
			</ul>
		</xsl:if>
		<xsl:apply-templates select="*[@object_id='210']" mode="checklistTaxonFields"/>
		<xsl:apply-templates select=".//*[@object_id='38']" mode="ttMaterials"/>
		<xsl:apply-templates select="*[@object_id='209']" mode="checklistTaxonFields"/>
		<xsl:apply-templates select="*[@object_id='208']" mode="checklistTaxonFields"/>
		<xsl:apply-templates select="*[@object_id='207']" mode="checklistTaxonFields"/>
		<xsl:apply-templates select="*[@object_id='206']" mode="checklistTaxonFields"/>

	</xsl:template>

	<xsl:template match="*[@object_id='210']" mode="checklistTaxonFields">
		<xsl:if test="./fields/*[@id='474']/value != ''">
			<div class="myfieldHolder otstapLeft">
				<xsl:attribute name="instance_id"><xsl:value-of select="./@instance_id" /></xsl:attribute>
				<div class="fieldLabel no-float otstapBottom">Nomenclature</div>
				<div class="fieldValue">
					<xsl:call-template name="markContentEditableField">
						<xsl:with-param name="pObjectId" select="./@object_id"></xsl:with-param>
						<xsl:with-param name="pFieldId">474</xsl:with-param>
					</xsl:call-template>
					<xsl:attribute name="field_id">474</xsl:attribute>
					<xsl:apply-templates select="./fields/*[@id='474']" mode="formatting"/>
				</div>
			</div>
		</xsl:if>
	</xsl:template>

	<xsl:template match="*[@object_id='209']" mode="checklistTaxonFields">
	<!--	<xsl:if test="./fields/*/value != ''">
			<h4 class="h-treatment-section">Ecological interactions</h4>
		</xsl:if> -->
		<xsl:if test="./fields/*[@id='470']/value != ''">
			<div class="myfieldHolder otstapLeft">
				<xsl:attribute name="instance_id"><xsl:value-of select="./@instance_id" /></xsl:attribute>
				<span class="fieldLabel">Feeds on:&#160;</span>
				<div class="fieldValue">
					<xsl:call-template name="markContentEditableField">
						<xsl:with-param name="pObjectId" select="./@object_id"></xsl:with-param>
						<xsl:with-param name="pFieldId">470</xsl:with-param>
					</xsl:call-template>
					<xsl:attribute name="field_id">470</xsl:attribute>
					<xsl:apply-templates select="./fields/*[@id='470']" mode="formatting"/>
				</div>
			</div>
		</xsl:if>

		<xsl:if test="./fields/*[@id='469']/value != ''">
			<div class="myfieldHolder otstapLeft">
				<xsl:attribute name="instance_id"><xsl:value-of select="./@instance_id" /></xsl:attribute>
				<span class="fieldLabel">Symbiotic with:&#160;</span>
				<div  class="fieldValue">
					<xsl:call-template name="markContentEditableField">
						<xsl:with-param name="pObjectId" select="./@object_id"></xsl:with-param>
						<xsl:with-param name="pFieldId">469</xsl:with-param>
					</xsl:call-template>
					<xsl:attribute name="field_id">469</xsl:attribute>
					<xsl:apply-templates select="./fields/*[@id='469']" mode="formatting"/>
				</div>
			</div>
		</xsl:if>




		<xsl:if test="./fields/*[@id='468']/value != ''">
			<div class="myfieldHolder otstapLeft">
				<xsl:attribute name="instance_id"><xsl:value-of select="./@instance_id" /></xsl:attribute>
				<span class="fieldLabel">Parasite of:&#160;</span>
				<div  class="fieldValue">
					<xsl:call-template name="markContentEditableField">
						<xsl:with-param name="pObjectId" select="./@object_id"></xsl:with-param>
						<xsl:with-param name="pFieldId">468</xsl:with-param>
					</xsl:call-template>
					<xsl:attribute name="field_id">468</xsl:attribute>
					<xsl:apply-templates select="./fields/*[@id='468']" mode="formatting"/>
				</div>
			</div>
		</xsl:if>

		<xsl:if test="./fields/*[@id='467']/value != ''">
			<div class="myfieldHolder otstapLeft">
				<xsl:attribute name="instance_id"><xsl:value-of select="./@instance_id" /></xsl:attribute>
				<span class="fieldLabel">Host of:&#160;</span>
				<div  class="fieldValue">
					<xsl:call-template name="markContentEditableField">
						<xsl:with-param name="pObjectId" select="./@object_id"></xsl:with-param>
						<xsl:with-param name="pFieldId">467</xsl:with-param>
					</xsl:call-template>
					<xsl:attribute name="field_id">467</xsl:attribute>
					<xsl:apply-templates select="./fields/*[@id='467']" mode="formatting"/>
				</div>
			</div>
		</xsl:if>

		<xsl:if test="./fields/*[@id='466']/value != ''">
			<div class="myfieldHolder otstapLeft">
				<xsl:attribute name="instance_id"><xsl:value-of select="./@instance_id" /></xsl:attribute>
				<span class="fieldLabel">Native status:&#160;</span>
				<div  class="fieldValue">
					<xsl:call-template name="markContentEditableField">
						<xsl:with-param name="pObjectId" select="./@object_id"></xsl:with-param>
						<xsl:with-param name="pFieldId">466</xsl:with-param>
					</xsl:call-template>
					<xsl:attribute name="field_id">466</xsl:attribute>
					<xsl:apply-templates select="./fields/*[@id='466']" mode="formatting"/>
				</div>
			</div>
		</xsl:if>
		<xsl:if test="./fields/*[@id='465']/value != ''">
			<div class="myfieldHolder otstapLeft">
				<xsl:attribute name="instance_id"><xsl:value-of select="./@instance_id" /></xsl:attribute>
					<span class="fieldLabel">Conservation status:&#160;</span>
					<div class="fieldValue">
						<xsl:call-template name="markContentEditableField">
							<xsl:with-param name="pObjectId" select="./@object_id"></xsl:with-param>
							<xsl:with-param name="pFieldId">465</xsl:with-param>
						</xsl:call-template>
						<xsl:attribute name="field_id">465</xsl:attribute>
						<xsl:apply-templates select="./fields/*[@id='465']" mode="formatting"/>
					</div>
			</div>
		</xsl:if>
	</xsl:template>

	<xsl:template match="*[@object_id='208']" mode="checklistTaxonFields">
		<xsl:if test="./fields/*[@id='471']/value != ''">
			<div class="myfieldHolder otstapLeft">
				<xsl:attribute name="instance_id"><xsl:value-of select="./@instance_id" /></xsl:attribute>
				<span class="fieldLabel">Distribution:&#160;</span>
				<div class="fieldValue">
					<xsl:call-template name="markContentEditableField">
						<xsl:with-param name="pObjectId" select="./@object_id"></xsl:with-param>
						<xsl:with-param name="pFieldId">471</xsl:with-param>
					</xsl:call-template>
					<xsl:attribute name="field_id">471</xsl:attribute>
					<xsl:apply-templates select="./fields/*[@id='471']" mode="formatting"/>
				</div>
			</div>
		</xsl:if>
	</xsl:template>

	<xsl:template match="*[@object_id='207']" mode="checklistTaxonFields">
		<xsl:if test="./fields/*[@id='472']/value != ''">
			<div class="myfieldHolder otstapLeft">
				<xsl:attribute name="instance_id"><xsl:value-of select="./@instance_id" /></xsl:attribute>
				<span class="fieldLabel">Horizon:&#160;</span>
				<div class="fieldValue">
					<xsl:call-template name="markContentEditableField">
						<xsl:with-param name="pObjectId" select="./@object_id"></xsl:with-param>
						<xsl:with-param name="pFieldId">472</xsl:with-param>
					</xsl:call-template>
					<xsl:attribute name="field_id">472</xsl:attribute>
					<xsl:apply-templates select="./fields/*[@id='472']" mode="formatting"/>
				</div>
			</div>
		</xsl:if>
	</xsl:template>

	<xsl:template match="*[@object_id='206']" mode="checklistTaxonFields">
		<xsl:if test="./fields/*[@id='473']/value != ''">
			<div class="myfieldHolder otstapLeft">
				<xsl:attribute name="instance_id"><xsl:value-of select="./@instance_id" /></xsl:attribute>
				<span class="fieldLabel">Notes:&#160;</span>
				<div class="fieldValue">
					<xsl:call-template name="markContentEditableField">
						<xsl:with-param name="pObjectId" select="./@object_id"></xsl:with-param>
						<xsl:with-param name="pFieldId">473</xsl:with-param>
					</xsl:call-template>
					<xsl:attribute name="field_id">473</xsl:attribute>
					<xsl:apply-templates select="./fields/*[@id='473']" mode="formatting"/>
				</div>
			</div>
		</xsl:if>
	</xsl:template>


	<!-- Checklist Taxon Form -->
	<xsl:template match="*" mode="checklistTaxonForm">
		<xsl:variable name="lGenus" select="./fields/*[@id='48']/value"></xsl:variable>
		<xsl:variable name="lSubGenus" select="./fields/*[@id='417']/value"></xsl:variable>
		<xsl:variable name="lSpecies" select="./fields/*[@id='49']/value"></xsl:variable>
		<xsl:variable name="lRankType" select="./fields/*[@id='414']/value"></xsl:variable>


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

		<xsl:variable name="lRankValue" select="./fields/*[@id=$RankID]/value"></xsl:variable>
		<div class="P-Article-Preview-Block-Content">
			<xsl:attribute name="instance_id"><xsl:value-of select="./@instance_id" /></xsl:attribute>

			<!-- label -->
			<h3 class="h-treatment">
				<xsl:choose>
					<xsl:when test="$lRankType = 'form'"></xsl:when>
					<xsl:when test="$lRankType = 'subspecies'"></xsl:when>
					<xsl:when test="$lRankType = 'variety'"></xsl:when>
					<xsl:when test="$lRankType = 'species'"></xsl:when>
					<xsl:otherwise>
						<xsl:value-of select="concat(translate(substring($lRankType,1,1), 'abcdefghijklmnopqrstuvwxyz','ABCDEFGHIJKLMNOPQRSTUVWXYZ'), substring($lRankType,2))"/>
						<xsl:text> </xsl:text>
					</xsl:otherwise>
				</xsl:choose>

				<!-- value -->
				<xsl:if test="$lRankValue != ''">
						<xsl:choose>
							<xsl:when test="$lRankType = 'genus'"><i><xsl:apply-templates select="$lRankValue" mode="formatting"/></i></xsl:when>
							<xsl:when test="$lRankType = 'subgenus'"><i><xsl:apply-templates select="$lRankValue" mode="formatting"/></i></xsl:when>
							<xsl:when test="$lRankType = 'species' or $lRankType = 'subspecies' or $lRankType = 'variety' or $lRankType = 'form'">
								<i><xsl:apply-templates select="./fields/*[@id='48']" mode="formatting_nospace"/></i><!-- $Genus-->
								<xsl:if test="./fields/*[@id='417']/value != ''"><!-- $Subgenus-->
									<xsl:text> (</xsl:text><i>
										<xsl:apply-templates select="./fields/*[@id='417']" mode="formatting_nospace"/>
									</i><xsl:text>)</xsl:text>
								</xsl:if>
								<xsl:text> </xsl:text>
								<i><xsl:apply-templates select="./fields/*[@id='49']" mode="formatting_nospace"/></i><!-- $Species -->
								<xsl:if test="$lRankType = 'subspecies'"> subsp. <i><xsl:apply-templates select="./fields/*[@id='418']" mode="formatting_nospace"/></i></xsl:if>
								<xsl:if test="$lRankType = 'variety'"> 	 var.   <i><xsl:apply-templates select="./fields/*[@id='435']" mode="formatting_nospace"/></i></xsl:if>
								<xsl:if test="$lRankType = 'form'">		 f.     <i><xsl:apply-templates select="./fields/*[@id='436']" mode="formatting_nospace"/></i></xsl:if>
							 </xsl:when>
							<xsl:otherwise>
								<xsl:apply-templates select="$lRankValue" mode="formatting"/>
							</xsl:otherwise>
					</xsl:choose>
				</xsl:if>
				<xsl:text> </xsl:text>
				<xsl:apply-templates select="./fields/*[@id='236']" mode="formatting"/>
			</h3>
		</div>
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

			<xsl:if test="count(.//*[@object_id='195']) &gt; 0 or
						  count(.//*[@object_id='186']) &gt; 0 or
						  count(.//*[@object_id='215']) &gt; 0 or
						  count(.//*[@object_id='217']) &gt; 0 ">

				<div class="treatmentSection">
					<h3 class="h-treatment-section">Type species</h3>
					<xsl:apply-templates select=".//*[@object_id='195' or @object_id='186']" mode="NewGenusTypeSpecies"/>
					<xsl:apply-templates select=".//*[@object_id='215' or @object_id='217']" mode="GenusRedescriptionTypeSpecies"/>
				</div>
			</xsl:if>


			 <!-- Synonyms -->

			<xsl:if test="count(.//*[@object_id='200']) &gt; 0">
				<xsl:apply-templates select=".//*[@object_id='200']" mode="taxonSynonymsSections"/>
			</xsl:if>

			<xsl:apply-templates select=".//*[@object_id='38']" mode="ttMaterials"/>

			<xsl:apply-templates select=".//*[@object_id='51']/*[@object_id &gt; 0]/*[@object_id &gt; 0]" mode="taxonTreatmentSections"/>
			<xsl:apply-templates select=".//*[@object_id='74']/*[@object_id &gt; 0]" mode="taxonTreatmentSections"/>
			<xsl:apply-templates select=".//*[@object_id='109']/*[@object_id &gt; 0]" mode="taxonTreatmentSections"/>
			<xsl:apply-templates select=".//*[@object_id='140']/*[@object_id &gt; 0]" mode="taxonTreatmentSections"/>
			<xsl:apply-templates select=".//*[@object_id='185']/*[@object_id &gt; 0]" mode="taxonTreatmentSections"/>
			<xsl:apply-templates select=".//*[@object_id='198']/*[@object_id &gt; 0]" mode="taxonTreatmentSections"/>
			<xsl:apply-templates select=".//*[@object_id='193']/*[@object_id &gt; 0]" mode="taxonTreatmentSections"/>
			<xsl:apply-templates select=".//*[@object_id='199']/*[@object_id &gt; 0]" mode="taxonTreatmentSections"/>
			<!-- Redescription TT Genus IC(Z)N > Treatment sections -->
			<xsl:apply-templates select=".//*[@object_id='214']/*[@object_id &gt; 0]" mode="taxonTreatmentSections"/>
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

	<!-- Taxon name species -->
	<xsl:template match="*[@object_id='180']" mode="taxonTreatmentName">
		<span>
			<xsl:attribute name="instance_id"><xsl:value-of select="./@instance_id" /></xsl:attribute>
			<xsl:attribute name="class">taxonTreatmentName</xsl:attribute>
			<span field_id="48"><i><xsl:apply-templates select="./fields/*[@id='48']" mode="formatting_nospace"/></i></span>
			<xsl:if test="./fields/*[@id='417']/value != ''">
			<span field_id="417">&#160;(<i><xsl:apply-templates select="./fields/*[@id='417']" mode="formatting_nospace"/></i>)</span>
			</xsl:if>
			<span field_id="49">&#160;<i><xsl:apply-templates select="./fields/*[@id='49']" mode="formatting_nospace"/></i></span>
		</span>
		<xsl:text>&#160;</xsl:text>
		<span>
			<xsl:attribute name="instance_id"><xsl:value-of select="./@instance_id" /></xsl:attribute>
			<span><xsl:attribute name="field_id">50</xsl:attribute><xsl:apply-templates select="./fields/*[@id='50']" mode="formatting_nospace"/></span>
		</span>
	</xsl:template>


	<!-- Taxon name Genus -->
	<xsl:template match="*[@object_id='181']" mode="taxonTreatmentName">
		<span class="taxonTreatmentName">
			<xsl:attribute name="instance_id"><xsl:value-of select="./@instance_id" /></xsl:attribute>
			<span field_id="48"><i><xsl:apply-templates select="./fields/*[@id='48']" mode="formatting"/></i></span>
		</span><xsl:text>&#160;</xsl:text>
		<span>
			<xsl:attribute name="instance_id"><xsl:value-of select="./@instance_id" /></xsl:attribute>
			<span><xsl:attribute name="field_id">50</xsl:attribute><xsl:apply-templates select="./fields/*[@id='50']" mode="formatting"/></span>
		</span>
	</xsl:template>

	<!-- Taxon new Genus Type species -->
	<xsl:template match="*" mode="NewGenusTypeSpecies">
		
		<div class="typeSpeciesIndent">
			<xsl:attribute name="instance_id"><xsl:value-of select="./@instance_id" /></xsl:attribute>
			<span field_id="441"><i><xsl:apply-templates select="./fields/*[@id='441']" mode="formatting"/></i></span> <!-- Genus	 --> <xsl:text> </xsl:text>
			<span field_id="442"><i><xsl:apply-templates select="./fields/*[@id='442']" mode="formatting"/></i></span> <!-- Species -->

			<!-- Taxon Author -->
			<span field_id="443"><xsl:apply-templates select="./fields/*[@id='443']" mode="formatting"/></span>
			<!-- new species described in this paper -->
			<xsl:if test="./fields/*[@id='440']/value/@value_id = 1">
				<xsl:choose>
					<xsl:when test="../../fields/*[@id='384']/value = 6 or ../../fields/*[@id='384']/value = 7"><xsl:text>, sp. nov.</xsl:text></xsl:when>
					<xsl:otherwise>, sp. n.</xsl:otherwise>
				</xsl:choose>
			</xsl:if>
			<xsl:if test="./fields/*[@id='454']/value != ''">
				<xsl:text> (</xsl:text>
				<span field_id="454"><xsl:apply-templates select="./fields/*[@id='454']" mode="formatting_nospace"/> <!-- Basionym author -->
				</span>
				<xsl:text>)</xsl:text>
			</xsl:if>
			
			<!-- Citations -->
			<xsl:apply-templates mode="taxonCitations" select="." />
		</div>
		
	</xsl:template>
		
	<!-- Citations -->
	<xsl:template match="*" mode="taxonCitations">
		<xsl:if test=".//*[@object_id='178']/fields/*[@id='438']/value != ''">
				<xsl:text> - </xsl:text>
			</xsl:if>	
			<xsl:for-each select=".//*[@object_id='201']">
				<xsl:apply-templates select=".//*[@object_id='178']/fields/*[@id='438']/value" mode="formatting_nospace"/> 
				<xsl:if test="./fields/*[@id='461']/value != ''">&#160;[<xsl:apply-templates select="./fields/*[@id='461']/value" mode="formatting_nospace"/>]</xsl:if>
				<xsl:if test="./fields/*[@id='462']/value != ''">:&#160;<xsl:apply-templates  select="./fields/*[@id='462']/value" mode="formatting_nospace"/></xsl:if>			
				<xsl:if test="position() != last()"><xsl:text>; </xsl:text></xsl:if>
				<xsl:if test="position()  = last()"><xsl:text>. </xsl:text></xsl:if>
			</xsl:for-each>
	</xsl:template>



	<xsl:template match="*" mode="GenusRedescriptionTypeSpecies">
			<!-- species name -->
			<xsl:apply-templates mode="taxonTreatmentName" select="./taxon_name" />
			<!-- with basyonym -->
			<xsl:apply-templates mode="taxonTreatmentName" select="./tt_species_name_with_basionym" />			
			<!-- citations -->
			<xsl:apply-templates mode="taxonCitations" select="." />
			<!-- synonyms  -->
			<xsl:if test="count(.//*[@object_id='200']) &gt; 0">
				<xsl:apply-templates select=".//*[@object_id='200']" mode="taxonSynonymsSections"/>
			</xsl:if>
	</xsl:template>

	<!-- Taxon species name  -->
	<xsl:template match="*[@object_id='43']" mode="taxonTreatmentName">
		<span>
			<xsl:attribute name="instance_id"><xsl:value-of select="./@instance_id" /></xsl:attribute>
			<xsl:attribute name="class">taxonTreatmentName</xsl:attribute>
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

	<!-- Treatment material -->
	<xsl:template match="*" mode="treatmentMaterial">
			<span>
				<xsl:attribute name="instance_id"><xsl:value-of select="./@instance_id" /></xsl:attribute>
				<xsl:variable name="lSortedFields" select="php:function('GetSortedMaterialFields', .//fields/*[value != ''][@id != '209'])"></xsl:variable>
						<xsl:for-each select="$lSortedFields/root/field">
							<xsl:apply-templates select="." mode="treatmentMaterialFieldCustom"></xsl:apply-templates>
							<xsl:if test="position() != last()"><xsl:text>; </xsl:text></xsl:if>
						</xsl:for-each>
			</span>
	</xsl:template>

	<!-- Treatment material field -->
	<xsl:template match="*" mode="treatmentMaterialField">
				<span>
					<xsl:attribute name="class">dcLabel</xsl:attribute>
					<xsl:value-of select="./@field_name"></xsl:value-of><xsl:text>: </xsl:text>
				</span>
				<span>
					<xsl:call-template name="markContentEditableField">
						<xsl:with-param name="pObjectId" select="../../@object_id" />
						<xsl:with-param name="pFieldId" select="./@id" />
					</xsl:call-template>
					<xsl:attribute name="field_id"><xsl:value-of select="./@id" /></xsl:attribute>
					<xsl:attribute name="instance_id"><xsl:value-of select="../../@instance_id" /></xsl:attribute>
					<xsl:apply-templates select="./value" mode="formatting_nospace"/>
				</span>
	</xsl:template>


	<!-- Taxon Synonyms Section -->
	<xsl:template match="*" mode="taxonSynonymsSections">
		<xsl:variable name="lSecTitle"/>
		<xsl:if test="./fields/*[@id='460']/value != ''">
			<div class="treatmentSection">
				<xsl:attribute name="instance_id"><xsl:value-of select="./@instance_id" /></xsl:attribute>
		<!--		<div class="P-Article-Preview-Block-Subsection-Title"> -->
					<xsl:if test="count(./fields/*[@id='460']) &gt; 0">
						<xsl:attribute name="field_id">200</xsl:attribute>
					</xsl:if>
					<h3 class="h-treatment-section">Nomenclature</h3>
		<!--		</div> -->
				<div>
					<xsl:attribute name="field_id">460</xsl:attribute>
					<xsl:attribute name="class">P-Inline</xsl:attribute>
					<xsl:apply-templates select="./fields/*[@id='460']/value" mode="formatting"/>
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
			<div class="treatmentSection">
				<xsl:attribute name="instance_id"><xsl:value-of select="./@instance_id" /></xsl:attribute>
				<h3 class="h-treatment-section">
					<xsl:if test="count(./fields/*[@id='211']) &gt; 0">
						<xsl:attribute name="field_id">211</xsl:attribute>
					</xsl:if>
					<xsl:copy-of select="$lSecTitle"/>
				</h3>
				<div>
					<xsl:call-template name="markContentEditableField">
						<xsl:with-param name="pObjectId" select="./@object_id"></xsl:with-param>
						<xsl:with-param name="pFieldId">212</xsl:with-param>
					</xsl:call-template>
					<xsl:attribute name="field_id">212</xsl:attribute>
					<xsl:attribute name="class">P-Inline</xsl:attribute>
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
			<div class="fieldValue">
				<xsl:call-template name="markContentEditableField">
					<xsl:with-param name="pObjectId" select="./@object_id"></xsl:with-param>
					<xsl:with-param name="pFieldId">212</xsl:with-param>
				</xsl:call-template>
				<xsl:attribute name="field_id">212</xsl:attribute>
				<xsl:attribute name="class">P-Inline</xsl:attribute>
				<xsl:apply-templates select="./fields/*[@id='212']/value" mode="formatting"/>
			</div>
		</div>
	</xsl:template>
	<!-- Taxon Treatments end -->
	<!--<xsl:template match="*" mode="articleBack"></xsl:template>-->
</xsl:stylesheet>