<?xml version='1.0'?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:tp="http://www.plazi.org/taxpub"  xmlns:php="http://php.net/xsl" exclude-result-prefixes="php tp xlink xsl">
	
	<!-- Custom Single Author Template -->
	<xsl:template match="*" mode="singleAuthorCustom">
		<xsl:attribute name="instance_id"><xsl:value-of select="./@instance_id" /></xsl:attribute>
		
		<!-- Author name -->
		<b>
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
		</b>
		
		<!-- Author email -->
		<xsl:text> (</xsl:text>
		<a>
			<xsl:attribute name="href"><xsl:text>mailto:</xsl:text><xsl:apply-templates select="./fields/*[@id=4]" mode="formatting_nospace"/></xsl:attribute>		
			<xsl:attribute name="field_id" >4</xsl:attribute>
			<xsl:apply-templates select="./fields/*[@id=4]" mode="formatting_nospace"/>
		</a>
		<xsl:text>) </xsl:text>
		
		<!-- Corresponding author check -->
		<xsl:if test="count(./fields/*[@id=15]/value[@value_id='1']) &gt; 0">
			<xsl:text> - Corresponding Author</xsl:text>
		</xsl:if>
		
		<!-- Author address -->
		<xsl:for-each select="./*[@object_id='5']" >
			<xsl:variable name="lCurrentNode" select="." />
			<span class="P-Current-Author-Single-Address">
				<xsl:apply-templates select="." mode="singleAuthorAddressCustom"/>
			</span>
		</xsl:for-each>
		
		<!-- Author rights -->
		<xsl:if test="count(./fields/*[@id=14]/value[@value_id='1' or @value_id='2']) &gt; 0">
			<span class="P-Current-Author-Single-Address">
				<xsl:text>Rights: </xsl:text>
				<xsl:apply-templates select="./fields/*[@id=14]/value[@value_id='1' or @value_id='2']" mode="formatting_nospace"/>
			</span>
		</xsl:if>
	</xsl:template>
	
	<!-- Custom Single Contributor Template -->
	<xsl:template match="*" mode="singleContributorCustom">
		<xsl:attribute name="instance_id"><xsl:value-of select="./@instance_id" /></xsl:attribute>
		
		<!-- Author name -->
		<b>
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
		</b>
		
		<!-- Author email -->
		<xsl:text> (</xsl:text>
		<a>
			<xsl:attribute name="href"><xsl:text>mailto:</xsl:text><xsl:apply-templates select="./fields/*[@id=4]" mode="formatting_nospace"/></xsl:attribute>		
			<xsl:attribute name="field_id" >4</xsl:attribute>
			<xsl:apply-templates select="./fields/*[@id=4]" mode="formatting_nospace"/>
		</a>
		<xsl:text>) </xsl:text>
		
		<!-- Contributor Roles -->
		<xsl:for-each select="./fields/*[@id=16]/value[@value_id != '']" >
			<xsl:if test="position() = 1"><xsl:text> - </xsl:text></xsl:if>
			<span class="P-Current-Author-Single-Address">
				<xsl:apply-templates select="." mode="formatting_nospace"/>
			</span>
			<xsl:if test="position()!=last()"><xsl:text>, </xsl:text></xsl:if>
		</xsl:for-each>
		
		<!-- Author address -->
		<xsl:for-each select="./*[@object_id='5']" >
			<xsl:variable name="lCurrentNode" select="." />
			<span class="P-Current-Author-Single-Address">
				<xsl:apply-templates select="." mode="singleAuthorAddressCustom"/>
			</span>
		</xsl:for-each>
		
		<!-- Author rights -->
		<xsl:if test="count(./fields/*[@id=14]/value[@value_id='1']) &gt; 0">
			<span class="P-Current-Author-Single-Address">
				<xsl:text>Rights: </xsl:text>
				<xsl:apply-templates select="./fields/*[@id=14]/value[@value_id='1']" mode="formatting_nospace"/>
			</span>
		</xsl:if>
	</xsl:template>
	
	<!-- Custom Single Author Address Template -->
	<xsl:template match="*" mode="singleAuthorAddressCustom">
		<div class="P-Single-Author-Address">
			<xsl:apply-templates select="./fields/*[@id='9']" mode="formatting_nospace"/><xsl:text>, </xsl:text>
            <xsl:apply-templates select="./fields/*[@id='10']" mode="formatting_nospace"/><xsl:text>, </xsl:text>
            <xsl:apply-templates select="./fields/*[@id='11']" mode="formatting_nospace"/>
		</div>
	</xsl:template>
	
	<!-- Subsections Custom -->
	<xsl:template match="section" mode="bodySubsectionCustom">
		<xsl:variable name="lSecTitle"><xsl:apply-templates select="./fields/*[@id='211']" mode="formatting"/></xsl:variable>
		<div class="P-Article-Preview-Block-Content">	
			<xsl:attribute name="instance_id"><xsl:value-of select="./@instance_id" /></xsl:attribute>		
			<span class="P-Article-Preview-Block-Subsection-Title">
				<xsl:attribute name="field_id">211</xsl:attribute>
				<b><xsl:value-of select="$lSecTitle"></xsl:value-of></b>
			</span>
			<xsl:text> </xsl:text>
			<br />
			<br />
			<span>
				<xsl:attribute name="field_id">212</xsl:attribute>
				<xsl:apply-templates select="./fields/*[@id='212']" mode="formatting"/>
			</span>
		</div>
	</xsl:template>
	
	<!--  Taxon treatment external link csutom template -->
	<xsl:template match="*" mode="TTExternalLinksCustom">
		<xsl:variable name="lTreatmentURLType" select="./fields/*[@id='52']/value"></xsl:variable>
		<b><xsl:value-of select="$lTreatmentURLType"></xsl:value-of></b>
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
	
	<!-- Single reference
	 -->
	<xsl:template match="*[@object_id='95']" mode="SingleReferenceCustom">
		<ul>
			<xsl:apply-templates select="./*[@object_id='97']/*[@object_id &gt; 0]" mode="articleBack"/>
		</ul>
	</xsl:template>
	
	<!-- Journal Article biblio reference
	 -->
	<xsl:template match="*[@object_id='102']" mode="articleBackCustom">
		<xsl:variable name="lVolume" select="./fields/*[@id='258']/value"></xsl:variable>
		<li>	
			<xsl:attribute name="instance_id"><xsl:value-of select="./@instance_id" /></xsl:attribute>			
			<xsl:attribute name="class">P-Second-Line-Indent</xsl:attribute>			
			<span>
				<xsl:for-each select="./*[@object_id='92' or @object_id='100' or @object_id='101']/*[@object_id='90']">
					<xsl:apply-templates select="." mode="processSingleReferenceAuthorCustom" />
					<xsl:if test="position()!=last()"><xsl:text>, </xsl:text></xsl:if>
				</xsl:for-each>
			</span>
			<!-- Year -->
			<xsl:text> (</xsl:text>
			<span>
				<xsl:attribute name="field_id">254</xsl:attribute>
				<xsl:apply-templates select="./fields/*[@id='254']/value" mode="formatting"/>
			</span>
			<xsl:text>) </xsl:text>
			<!-- Article Title -->	
			<span>				
				<xsl:attribute name="field_id">276</xsl:attribute>	
				<xsl:value-of select="php:function('parseReferenceItemTitle', string(./fields/*[@id='276']/value))"/>		
			</span>
			
			<xsl:if test="normalize-space(./fields/*[@id='243']/value) != ''">
				<!-- Journal -->
				<xsl:text> </xsl:text>			
				<span>
					<xsl:attribute name="field_id">243</xsl:attribute>
					<xsl:value-of select="./fields/*[@id='243']/value"></xsl:value-of>
				</span>
			</xsl:if>
			<xsl:if test="normalize-space(./fields/*[@id='258']/value) != ''">
				<xsl:text> </xsl:text>
				<!-- Volume -->
				<span>
					<xsl:attribute name="field_id">258</xsl:attribute>
					<xsl:value-of select="./fields/*[@id='258']/value"></xsl:value-of>
				</span>
			</xsl:if>
			<xsl:if test="normalize-space(./fields/*[@id='27']/value) != ''">
				<xsl:text> (</xsl:text>
				<!-- Issue -->
				<span>
					<xsl:attribute name="field_id">27</xsl:attribute>
					<xsl:value-of select="./fields/*[@id='27']/value"></xsl:value-of>
				</span>
				<xsl:text>)</xsl:text>
			</xsl:if>	
			<xsl:text>: </xsl:text>	
			
			
			<!-- FirtsPage -->
			<span>
				<xsl:attribute name="field_id">28</xsl:attribute>
				<xsl:value-of select="./fields/*[@id='28']/value"></xsl:value-of>
			</span>
			<!-- Last Page -->
			<xsl:if test="normalize-space(./fields/*[@id='28']/value) != ''">
				<xsl:text>-</xsl:text>
				<span>
					<xsl:attribute name="field_id">29</xsl:attribute>
					<xsl:value-of select="./fields/*[@id='29']/value" />
				</span>			
			</xsl:if>
			<xsl:text>.</xsl:text>	
			<xsl:if test="normalize-space(./fields/*[@id='262']/value) != ''">
				<!-- Publication language -->
				<xsl:text>[In </xsl:text>
				<span>
					<xsl:attribute name="field_id">262</xsl:attribute>
					<xsl:value-of select="./fields/*[@id='262']/value"></xsl:value-of>
				</span>
				<xsl:text>]</xsl:text>
			</xsl:if>
			<xsl:if test="normalize-space(./fields/*[@id='263']/value) != ''">
				<!-- URL -->
				<xsl:text> </xsl:text>
				<span>
					<xsl:attribute name="field_id">263</xsl:attribute>
					<xsl:value-of select="./fields/*[@id='263']/value"></xsl:value-of>
				</span>
			</xsl:if>
			<xsl:if test="normalize-space(./fields/*[@id='264']/value) != ''">
				<!-- ISBN -->
				<xsl:text> [ISBN </xsl:text>
				<span>
					<xsl:attribute name="field_id">264</xsl:attribute>
					<xsl:value-of select="./fields/*[@id='264']/value"></xsl:value-of>
				</span>
				<xsl:text>].</xsl:text>
			</xsl:if>
			<xsl:if test="normalize-space(./fields/*[@id='30']/value) != ''">
				<!-- DOI -->
				<xsl:text> DOI: </xsl:text>
				<span>
					<xsl:attribute name="field_id">30</xsl:attribute>
					<xsl:value-of select="./fields/*[@id='30']/value"></xsl:value-of>
				</span>
			</xsl:if>
		</li>
	</xsl:template>
	
	<xsl:template match="*" mode="processSingleReferenceAuthorCustom">
		<xsl:variable name="lAuthorParsedName">
			<!-- Last name -->
			
			<xsl:value-of select="./fields/*[@id='252']/value"></xsl:value-of>
			<xsl:text> </xsl:text>
			<!-- Initials of first name -->
			<xsl:value-of select="php:function('mb_substr', string(./fields/*[@id='251']/value), 0, 1)"></xsl:value-of>			
		</xsl:variable>
		<span>
			<xsl:attribute name="instance_id"><xsl:value-of select="./@instance_id" /></xsl:attribute>
			<xsl:value-of select="normalize-space($lAuthorParsedName)"></xsl:value-of>
		</span>
	</xsl:template>
	
	<!-- Treatment Materials -->	
	<xsl:template match="*" mode="treatmentMaterialCustom">
		<xsl:variable name="lTreatmentNode" select="."></xsl:variable>
		<div class="P-Article-Preview-Block">			
		<xsl:if test="count(.) &gt; 0">
			<div class="P-Article-Preview-Block-Content">
				<xsl:for-each select=".">
					<xsl:variable name="lMaterialTypeId" select="./*/fields/*[@id='209']/value/@value_id"></xsl:variable>
					<xsl:variable name="lMaterialTypeName" select="./*/fields/*[@id='209']/value"></xsl:variable>
					<!-- Ако имаме материали от този тип -->
					<xsl:if test="count(./*/fields/*[@id='209']/value[@value_id=$lMaterialTypeId]) &gt; 0">
						<div class="P-Article-Preview-Block-Content">	
							<span class="P-Article-Preview-Block-Subsection-Title"><xsl:value-of select="$lMaterialTypeName"></xsl:value-of></span><xsl:text>: </xsl:text>
							<xsl:apply-templates select=".//*[@object_id='33' or @object_id='32']" mode="treatmentMaterialCust"></xsl:apply-templates>
						</div>
					</xsl:if>						
				</xsl:for-each>
			</div>
			</xsl:if>
		</div>
	</xsl:template>
	
	<!-- Checklist Taxon Localities -->	
	<xsl:template match="*" mode="checklistLocalityCustom">
		<div class="P-Article-Preview-Block">
			<xsl:if test="count(.) &gt; 0">
				<div class="P-Article-Preview-Block-Content">
					<div class="P-Article-Preview-Block-Content">	
						<xsl:apply-templates select="." mode="treatmentMaterialCust"></xsl:apply-templates>
					</div>
				</div>
			</xsl:if>
		</div>
	</xsl:template>
	
	<!-- Treatment material -->
	<xsl:template match="*" mode="treatmentMaterialCust">
		<xsl:variable name="lTreatmentNode" select="."></xsl:variable>
		<div class="P-Article-Preview-Block-Content P-Inline">
			<span>
				<xsl:attribute name="instance_id"><xsl:value-of select="./@instance_id" /></xsl:attribute>
				<xsl:for-each select="./fields/*[value != '' ]">
					<xsl:apply-templates select="." mode="treatmentMaterialFieldCustom"></xsl:apply-templates>
					<xsl:if test="position() != last()"><xsl:text>; </xsl:text></xsl:if>	
				</xsl:for-each>
			</span>
			
		</div>
	</xsl:template>
	
	<!-- Treatment material field -->
	<xsl:template match="*" mode="treatmentMaterialFieldCustom">					
			<span>
				<xsl:attribute name="class">dcLabel</xsl:attribute>
				<xsl:value-of select="./@field_name"></xsl:value-of><xsl:text>: </xsl:text>
			</span>	
			<xsl:variable name="lId" select="./@id"></xsl:variable>			
			<!--<xsl:if test="($lId = 58) or ($lId = 60) or ($lId = 61) or ($lId = 114) or ($lId = 116)">-->
				<span>				
					<xsl:attribute name="field_id"><xsl:value-of select="./@field_id" /></xsl:attribute>
					<xsl:apply-templates select="./value" mode="formatting"/>
				</span>
			<!--</xsl:if>-->
	</xsl:template>
	
</xsl:stylesheet>