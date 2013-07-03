<?xml version="1.0"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:tp="http://www.plazi.org/taxpub" xmlns:php="http://php.net/xsl" exclude-result-prefixes="php tp xlink xsl">
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
	
	
	<xsl:template match="*[@object_id='38']" mode="ttMaterials">
		<xsl:variable name="lGroupedMaterials" select="php:function('GroupTreatmentMaterials', ./*[@object_id=37])"></xsl:variable>
		<xsl:if test="count($lGroupedMaterials/materials/material_group) &gt; 0">
			<div class="myfieldHolder otstapLeft">
				<div class="fieldLabel no-float otstapBottom materialsTitle">Materials</div>
				<xsl:for-each select="$lGroupedMaterials/materials/material_group">
					<div class="materialType">
						<div class="MaterialType">
							<i>
								<xsl:value-of select="./value"/>
								<xsl:if test="count(./*[@object_id='37']) &gt; 1"><xsl:text>s</xsl:text></xsl:if><xsl:text>: </xsl:text>
							</i>
						</div>
						<ol class="materialsHolder">
							<xsl:for-each select="./*[@object_id='37']">
							<li type="a">
								<xsl:apply-templates select="." mode="treatmentMaterial"/>
								<xsl:if test="position() != last()"><xsl:text>;</xsl:text></xsl:if>
								<xsl:if test="position()  = last()"><xsl:text>.</xsl:text></xsl:if>
							</li>
							</xsl:for-each>
						</ol>
					</div>
				</xsl:for-each>
			</div>
		</xsl:if>
	</xsl:template>


	<!--  Taxon treatment external link -->
	<xsl:template match="*" mode="TTExternalLinks">
		<xsl:variable name="lTreatmentURLType" select="./fields/*[@id='52']/value/@value_id"></xsl:variable>
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
		<a target="_blank">
			<xsl:attribute name="href"><xsl:value-of select="translate(normalize-space(concat($lLinkPrefix, $lURLsuffix)) , ' ', '')"/></xsl:attribute>
			<xsl:value-of select="normalize-space($lCurrentVal)"/>
		</a>
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
				<span class="dcLabel">
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
							<xsl:when test="$lRankType = 'genus'">
								<i>
									<xsl:call-template name="markContentEditableField">
										<xsl:with-param name="pObjectId" select="./@object_id" />
										<xsl:with-param name="pFieldId">48</xsl:with-param>
									</xsl:call-template>
									<xsl:attribute name="field_id">48</xsl:attribute>
									<xsl:attribute name="instance_id"><xsl:value-of select="./@instance_id" /></xsl:attribute>
									<xsl:apply-templates select="$lRankValue" mode="formatting"/>
								</i>
							</xsl:when>
							<xsl:when test="$lRankType = 'subgenus'">
								<i>
									<xsl:call-template name="markContentEditableField">
										<xsl:with-param name="pObjectId" select="./@object_id" />
										<xsl:with-param name="pFieldId">417</xsl:with-param>
									</xsl:call-template>
									<xsl:attribute name="field_id">417</xsl:attribute>
									<xsl:attribute name="instance_id"><xsl:value-of select="./@instance_id" /></xsl:attribute>
									<xsl:apply-templates select="$lRankValue" mode="formatting"/>
								</i>
							</xsl:when>
							<xsl:when test="$lRankType = 'species' or $lRankType = 'subspecies' or $lRankType = 'variety' or $lRankType = 'form'">	
								<!-- $Genus-->
								<i>
									<xsl:call-template name="markContentEditableField">
										<xsl:with-param name="pObjectId" select="./@object_id" />
										<xsl:with-param name="pFieldId">48</xsl:with-param>
									</xsl:call-template>
									<xsl:attribute name="field_id">48</xsl:attribute>
									<xsl:attribute name="instance_id"><xsl:value-of select="./@instance_id" /></xsl:attribute>
									<xsl:apply-templates select="./fields/*[@id='48']" mode="formatting_nospace"/>
								</i>
								<xsl:if test="./fields/*[@id='417']/value != ''">	
									<xsl:text> (</xsl:text>
									<!-- $Subgenus-->
									<i>
										<xsl:call-template name="markContentEditableField">
											<xsl:with-param name="pObjectId" select="./@object_id" />
											<xsl:with-param name="pFieldId">417</xsl:with-param>
										</xsl:call-template>
										<xsl:attribute name="field_id">417</xsl:attribute>
										<xsl:attribute name="instance_id"><xsl:value-of select="./@instance_id" /></xsl:attribute>
										<xsl:apply-templates select="./fields/*[@id='417']" mode="formatting_nospace"/>
									</i><xsl:text>)</xsl:text>
								</xsl:if>
								<xsl:text> </xsl:text>
								<!-- $Species -->
								<i>
									<xsl:call-template name="markContentEditableField">
										<xsl:with-param name="pObjectId" select="./@object_id" />
										<xsl:with-param name="pFieldId">49</xsl:with-param>
									</xsl:call-template>
									<xsl:attribute name="field_id">49</xsl:attribute>
									<xsl:attribute name="instance_id"><xsl:value-of select="./@instance_id" /></xsl:attribute>
									<xsl:apply-templates select="./fields/*[@id='49']" mode="formatting_nospace"/>
								</i>
								<xsl:if test="$lRankType = 'subspecies'"> subsp. 
									<i>
										<xsl:call-template name="markContentEditableField">
											<xsl:with-param name="pObjectId" select="./@object_id" />
											<xsl:with-param name="pFieldId">418</xsl:with-param>
										</xsl:call-template>
										<xsl:attribute name="field_id">418</xsl:attribute>
										<xsl:attribute name="instance_id"><xsl:value-of select="./@instance_id" /></xsl:attribute>
										<xsl:apply-templates select="./fields/*[@id='418']" mode="formatting_nospace"/>
									</i>
								</xsl:if>
								<xsl:if test="$lRankType = 'variety'"> 	 var.   
									<i>
										<xsl:call-template name="markContentEditableField">
											<xsl:with-param name="pObjectId" select="./@object_id" />
											<xsl:with-param name="pFieldId">435</xsl:with-param>
										</xsl:call-template>
										<xsl:attribute name="field_id">435</xsl:attribute>
										<xsl:attribute name="instance_id"><xsl:value-of select="./@instance_id" /></xsl:attribute>
										<xsl:apply-templates select="./fields/*[@id='435']" mode="formatting_nospace"/>
									</i>
								</xsl:if>
								<xsl:if test="$lRankType = 'form'">		 f.     
									<i>
										<xsl:call-template name="markContentEditableField">
											<xsl:with-param name="pObjectId" select="./@object_id" />
											<xsl:with-param name="pFieldId">436</xsl:with-param>
										</xsl:call-template>
										<xsl:attribute name="field_id">436</xsl:attribute>
										<xsl:attribute name="instance_id"><xsl:value-of select="./@instance_id" /></xsl:attribute>
										<xsl:apply-templates select="./fields/*[@id='436']" mode="formatting_nospace"/>
									</i>
								</xsl:if>	
							 </xsl:when>	
							<xsl:otherwise>	
								<span id="sad">
									<xsl:call-template name="markContentEditableField">
										<xsl:with-param name="pObjectId" select="./@object_id" />
										<xsl:with-param name="pFieldId" select="$RankID" />
									</xsl:call-template>
									<xsl:attribute name="field_id"><xsl:value-of select="$RankID" /></xsl:attribute>
									<xsl:attribute name="instance_id"><xsl:value-of select="./@instance_id" /></xsl:attribute>
									<xsl:apply-templates select="$lRankValue" mode="formatting"/>
								</span>
							</xsl:otherwise>
					</xsl:choose>
				</xsl:if>
				<xsl:text> </xsl:text>
				<span>
					<xsl:call-template name="markContentEditableField">
						<xsl:with-param name="pObjectId" select="./@object_id" />
						<xsl:with-param name="pFieldId">236</xsl:with-param>
					</xsl:call-template>
					<xsl:attribute name="field_id">236</xsl:attribute>
					<xsl:attribute name="instance_id"><xsl:value-of select="./@instance_id" /></xsl:attribute>
					<xsl:apply-templates select="./fields/*[@id='236']" mode="formatting"/>
				</span>
			</h3>
		</div>
	</xsl:template>
</xsl:stylesheet>
