<?xml version='1.0'?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:tp="http://www.plazi.org/taxpub"  xmlns:php="http://php.net/xsl" exclude-result-prefixes="php tp xlink xsl">

		<!-- Supplementary files -->
	<xsl:template match="*[@object_id='56']" mode="articleBack">
		<xsl:if test="count(./*[@object_id='55']) &gt; 0">
			<xsl:variable name="lSecTitle">Supplementary materials</xsl:variable>
			<div class="P-Article-Preview-Block">
				<xsl:attribute name="instance_id"><xsl:value-of select="./@instance_id" /></xsl:attribute>
				<h1 id="supplementary_materials"><xsl:value-of select="$lSecTitle"></xsl:value-of></h1>
				<xsl:for-each select="./*[@object_id='55']/fields/*[@id != '']">
					<xsl:apply-templates select="." mode="singleSupplementaryMaterial"/>
				</xsl:for-each>
			</div>
		</xsl:if>
	</xsl:template>


	<!-- References
	 -->
	<xsl:template match="*[@object_id='21']" mode="articleBack">
		<xsl:if test="count(./*[@object_id='95']) &gt; 0">
			<xsl:variable name="lSecTitle">References</xsl:variable>
			<div class="P-Article-Preview-Block">
				<xsl:attribute name="instance_id"><xsl:value-of select="./@instance_id" /></xsl:attribute>
				<h1 id="references"><xsl:value-of select="$lSecTitle"></xsl:value-of></h1>
				<xsl:value-of select="php:function('getReferenceYearLetter', 0, string(/document/@id), 1)"></xsl:value-of>
				<ul class="references">
					<xsl:apply-templates mode="articleBack"/>
				</ul>
			</div>
		</xsl:if>
	</xsl:template>

	<!-- Single reference -OLD
	 -->
	<xsl:template match="*[@object_id='20']" mode="articleBack">
		<xsl:variable name="lRefId" select="php:function('getReferenceId', string(./@instance_id))"></xsl:variable>
		<li class="reference">
			<xsl:attribute name="instance_id"><xsl:value-of select="./@instance_id" /></xsl:attribute>
			<xsl:attribute name="id">B<xsl:value-of select="$lRefId" /></xsl:attribute>
			  <span  field_id="24"><xsl:apply-templates select="./fields/*[@id='24']"  mode="formatting" /></span>
			 (<span  field_id="25"><xsl:apply-templates select="./fields/*[@id='25']"  mode="formatting" /></span>) 
			  <span  field_id="26"><xsl:apply-templates select="./fields/*[@id='26']"  mode="formatting" /></span>
			  <span field_id="243"><xsl:apply-templates select="./fields/*[@id='243']" mode="formatting" /></span>
			  <span  field_id="27"><xsl:apply-templates select="./fields/*[@id='27']"  mode="formatting" /></span>:
			  <span  field_id="28"><xsl:apply-templates select="./fields/*[@id='28']"  mode="formatting" /></span>-
			  <span  field_id="29"><xsl:apply-templates select="./fields/*[@id='29']"  mode="formatting" /></span>.
		</li>
	</xsl:template>

	<!-- Single reference
	 -->
	<xsl:template match="*[@object_id='95']" mode="articleBack">
		<xsl:apply-templates select="./*[@object_id='97']/*[@object_id &gt; 0]" mode="articleBack"/>
	</xsl:template>

	<xsl:template match="*" mode="processSingleReferenceAuthor">
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

	<!-- Single reference year -->
	<xsl:template match="*" mode="processSingleReferenceYear">
		<xsl:value-of select="php:function('getReferenceYearLetter', 0, string(/document/@id), 1)"></xsl:value-of>
		<xsl:text> (</xsl:text>
		<span>
			<xsl:attribute name="field_id">254</xsl:attribute>
			<xsl:apply-templates select="./fields/*[@id='254']/value" mode="formatting"/>
		</span>
		<xsl:value-of select="php:function('getReferenceYearLetter', string(./ancestor::*[@object_id='95']/@instance_id))"></xsl:value-of>
		<xsl:text>) </xsl:text>
	</xsl:template>


	<xsl:template match="*" mode="processSingleReferenceAuthorFirstLast">
		<xsl:variable name="lAuthorParsedName">
			<!-- First and Last name -->
			
			<xsl:value-of select="./fields/*[@id='250']/value"></xsl:value-of>
		</xsl:variable>
		<span>
			<xsl:attribute name="instance_id"><xsl:value-of select="./@instance_id" /></xsl:attribute>
			<xsl:apply-templates select="$lAuthorParsedName" mode="formatting_nospace"/>			
		</span>
	</xsl:template>

	<!-- Book biblio reference
	 -->
	<xsl:template match="*[@object_id='98']" mode="articleBack">
		<xsl:variable name="lAuthorshipType">
			<xsl:choose>
				<xsl:when test="count(./*[@object_id='92']) &gt; 0">
					<xsl:value-of select="./*[@object_id='92']/fields/*[@id='265']/value/@value_id"></xsl:value-of>
				</xsl:when>
				<xsl:when test="count(./*[@object_id='100']) &gt; 0">
					<xsl:value-of select="./*[@object_id='100']/fields/*[@id='281']/value/@value_id"></xsl:value-of>
				</xsl:when>
				<xsl:when test="count(./*[@object_id='101']) &gt; 0">
					<xsl:value-of select="./*[@object_id='101']/fields/*[@id='282']/value/@value_id"></xsl:value-of>
				</xsl:when>
			</xsl:choose>
		</xsl:variable>
		<xsl:variable name="lVolume" select="./fields/*[@id='258']/value"></xsl:variable>

		<li>
			<xsl:attribute name="instance_id"><xsl:value-of select="./@instance_id" /></xsl:attribute>
			<xsl:for-each select="./*[@object_id='92' or @object_id='100' or @object_id='101']/*[@object_id='90']">
				<xsl:choose>
					<xsl:when test="$lAuthorshipType = 3">
						<xsl:apply-templates select="." mode="processSingleReferenceAuthorFirstLast" />
					</xsl:when>
					<xsl:otherwise>
						<xsl:apply-templates select="." mode="processSingleReferenceAuthor" />
					</xsl:otherwise>
				</xsl:choose>
				<xsl:if test="position()!=last()"><xsl:text>, </xsl:text></xsl:if>
			</xsl:for-each>
			<xsl:if test="normalize-space($lAuthorshipType)=$gAuthorshipEditorType">
				<xsl:text> </xsl:text>
				<xsl:choose>
					<xsl:when test="count(./*[@object_id='92' or @object_id='100' or @object_id='101']/*[@object_id='90']) &gt; 1">
						<xsl:text>(Ed.)</xsl:text>
					</xsl:when>
					<xsl:otherwise>
						<xsl:text>(Eds)</xsl:text>
					</xsl:otherwise>
				</xsl:choose>
			</xsl:if>
			<!-- Year -->
			<xsl:apply-templates select="." mode="processSingleReferenceYear"/>
			<!-- Book Title -->
			<span>
				<xsl:call-template name="markContentEditableField">
					<xsl:with-param name="pObjectId" select="./@object_id" />
					<xsl:with-param name="pFieldId">255</xsl:with-param>
				</xsl:call-template>
				<xsl:attribute name="field_id">255</xsl:attribute>
				<!--  <xsl:value-of select="php:function('parseReferenceItemTitle', string(./fields/*[@id='255']/value))"/>-->
				<xsl:apply-templates select="./fields/*[@id='255']/value" mode="formatting_nospace"/>
			</span>
			<xsl:text> </xsl:text>
			<xsl:if test="normalize-space(./fields/*[@id='257']/value) != ''">
				<!-- Translated title -->
				<xsl:text>[</xsl:text>
				<span>
					<xsl:call-template name="markContentEditableField">
						<xsl:with-param name="pObjectId" select="./@object_id" />
						<xsl:with-param name="pFieldId">257</xsl:with-param>
					</xsl:call-template>
					<xsl:attribute name="field_id">257</xsl:attribute>
					<!-- <xsl:value-of select="php:function('parseReferenceItemTitle', string(./fields/*[@id='257']/value))"/> -->
					<xsl:apply-templates select="./fields/*[@id='257']/value" mode="formatting_nospace"/>
				</span>
				<xsl:text>]</xsl:text>
			</xsl:if>

			<xsl:if test="normalize-space(./fields/*[@id='256']/value) != ''">
				<xsl:text> </xsl:text>
				<!-- Edition -->
				<span>
					<xsl:call-template name="markContentEditableField">
						<xsl:with-param name="pObjectId" select="./@object_id" />
						<xsl:with-param name="pFieldId">256</xsl:with-param>
					</xsl:call-template>
					<xsl:attribute name="field_id">256</xsl:attribute>
					<xsl:apply-templates select="./fields/*[@id='256']/value" mode="formatting"/>
				</span>
			</xsl:if>
			<!-- Volume -->
			<xsl:if test="$lVolume != ''">
				<xsl:text>, </xsl:text>
				<span>
					<xsl:call-template name="markContentEditableField">
						<xsl:with-param name="pObjectId" select="./@object_id" />
						<xsl:with-param name="pFieldId">258</xsl:with-param>
					</xsl:call-template>
					<xsl:attribute name="field_id">258</xsl:attribute>
					<xsl:apply-templates select="$lVolume" mode="formatting"/>					
				</span>
			</xsl:if>
			<xsl:if test="normalize-space(./fields/*[@id='256']/value) != '' or $lVolume != ''">
				<xsl:text>.</xsl:text>
			</xsl:if>

			<!-- Publisher -->
			<xsl:text> </xsl:text>
			<span>
				<xsl:call-template name="markContentEditableField">
						<xsl:with-param name="pObjectId" select="./@object_id" />
						<xsl:with-param name="pFieldId">259</xsl:with-param>
					</xsl:call-template>
				<xsl:attribute name="field_id">259</xsl:attribute>
				<xsl:apply-templates select="./fields/*[@id='259']/value" mode="formatting"/>
			</span>
			<xsl:text>, </xsl:text>
			<!-- City -->
			<span>
				<xsl:call-template name="markContentEditableField">
						<xsl:with-param name="pObjectId" select="./@object_id" />
						<xsl:with-param name="pFieldId">260</xsl:with-param>
					</xsl:call-template>
				<xsl:attribute name="field_id">260</xsl:attribute>
				<xsl:apply-templates select="./fields/*[@id='260']/value" mode="formatting"/>
			</span>
			<!-- # of pages -->
			<xsl:text>, </xsl:text>
			<span>
				<xsl:call-template name="markContentEditableField">
						<xsl:with-param name="pObjectId" select="./@object_id" />
						<xsl:with-param name="pFieldId">261</xsl:with-param>
					</xsl:call-template>
				<xsl:attribute name="field_id">261</xsl:attribute>
				<xsl:apply-templates select="./fields/*[@id='261']/value" mode="formatting"/>
			</span>
			<xsl:text> pp. </xsl:text>
			<xsl:if test="normalize-space(./fields/*[@id='262']/value) != ''">
				<!-- Publication language -->
				<xsl:text>[In </xsl:text>
				<span>
					<xsl:call-template name="markContentEditableField">
						<xsl:with-param name="pObjectId" select="./@object_id" />
						<xsl:with-param name="pFieldId">262</xsl:with-param>
					</xsl:call-template>
					<xsl:attribute name="field_id">262</xsl:attribute>
					<xsl:apply-templates select="./fields/*[@id='262']/value" mode="formatting"/>					
				</span>
				<xsl:text>]</xsl:text>
			</xsl:if>
			<xsl:if test="normalize-space(./fields/*[@id='263']/value) != ''">
				<!-- URL -->
				<xsl:text> </xsl:text>
				<a>
					<xsl:attribute name="field_id">263</xsl:attribute>
					<xsl:attribute name="href">
						<xsl:value-of select="php:function('checkIfLinkContainsHttp', string(./fields/*[@id='263']/value))"/>
					</xsl:attribute>
					<xsl:attribute name="target"><xsl:text>_blank</xsl:text></xsl:attribute>
					<xsl:apply-templates select="./fields/*[@id='263']/value" mode="formatting_nospace"/>
				</a>
			</xsl:if>
			<xsl:if test="normalize-space(./fields/*[@id='264']/value) != ''">
				<!-- ISBN -->
				<xsl:text> [ISBN </xsl:text>
				<span>
					<xsl:call-template name="markContentEditableField">
						<xsl:with-param name="pObjectId" select="./@object_id" />
						<xsl:with-param name="pFieldId">264</xsl:with-param>
					</xsl:call-template>
					<xsl:attribute name="field_id">264</xsl:attribute>
					<xsl:apply-templates select="./fields/*[@id='264']/value" mode="formatting"/>
				</span>
				<xsl:text>].</xsl:text>
			</xsl:if>
			<xsl:if test="normalize-space(./fields/*[@id='30']/value) != ''">
				<!-- DOI -->
				<xsl:text> DOI: </xsl:text>
				<a>
					<xsl:attribute name="field_id">30</xsl:attribute>
					<xsl:attribute name="href"><xsl:text>http://dx.doi.org/</xsl:text><xsl:value-of select="./fields/*[@id='30']/value"></xsl:value-of></xsl:attribute>
					<xsl:attribute name="target"><xsl:text>_blank</xsl:text></xsl:attribute>
					<xsl:apply-templates select="./fields/*[@id='30']/value" mode="formatting"/>
				</a>
			</xsl:if>
		</li>
	</xsl:template>

	<!-- Book chapter biblio reference
	 -->
	<xsl:template match="*[@object_id='99']" mode="articleBack">
		<xsl:variable name="lAuthorshipType">
			<xsl:choose>
				<xsl:when test="count(./*[@object_id='92']) &gt; 0">
					<xsl:value-of select="./*[@object_id='92']/fields/*[@id='265']/value/@value_id"></xsl:value-of>
				</xsl:when>
				<xsl:when test="count(./*[@object_id='100']) &gt; 0">
					<xsl:value-of select="./*[@object_id='100']/fields/*[@id='281']/value/@value_id"></xsl:value-of>
				</xsl:when>
				<xsl:when test="count(./*[@object_id='101']) &gt; 0">
					<xsl:value-of select="./*[@object_id='101']/fields/*[@id='282']/value/@value_id"></xsl:value-of>
				</xsl:when>
			</xsl:choose>
		</xsl:variable>

		<xsl:variable name="lEditorAuthorshipType">
			<xsl:choose>
				<xsl:when test="count(./*[@object_id='93']) &gt; 0">
					<xsl:value-of select="./*[@object_id='93']/fields/*[@id='283']/value/@value_id"></xsl:value-of>
				</xsl:when>
			</xsl:choose>
		</xsl:variable>
		<xsl:variable name="lVolume" select="./fields/*[@id='258']/value"></xsl:variable>

		<li>
			<xsl:attribute name="instance_id"><xsl:value-of select="./@instance_id" /></xsl:attribute>
			<xsl:for-each select="./*[@object_id='92' or @object_id='100' or @object_id='101']/*[@object_id='90']">
				<xsl:choose>
					<xsl:when test="$lAuthorshipType = 3">
						<xsl:apply-templates select="." mode="processSingleReferenceAuthorFirstLast" />
					</xsl:when>
					<xsl:otherwise>
						<xsl:apply-templates select="." mode="processSingleReferenceAuthor" />
					</xsl:otherwise>
				</xsl:choose>
				<xsl:if test="position()!=last()"><xsl:text>, </xsl:text></xsl:if>
			</xsl:for-each>
			<!-- Year -->
			<xsl:apply-templates select="." mode="processSingleReferenceYear"/>
			<!-- Book chapter Title -->
			<span>
				<xsl:call-template name="markContentEditableField">
						<xsl:with-param name="pObjectId" select="./@object_id" />
						<xsl:with-param name="pFieldId">271</xsl:with-param>
					</xsl:call-template>
				<xsl:attribute name="field_id">271</xsl:attribute>
				<!--  <xsl:value-of select="php:function('parseReferenceItemTitle', string(./fields/*[@id='271']/value))"/>-->
				<xsl:apply-templates select="./fields/*[@id='271']/value" mode="formatting_nospace"/>
			</span>

			<xsl:if test="count(./*[@object_id='93']/*[@object_id='91']) &gt; 0">
				<xsl:text> In: </xsl:text>
				<xsl:for-each select="./*[@object_id='93']/*[@object_id='91']">
					<xsl:choose>
						<xsl:when test="$lEditorAuthorshipType = 3">
							<xsl:apply-templates select="." mode="processSingleReferenceAuthorFirstLast" />
						</xsl:when>
						<xsl:otherwise>
							<xsl:apply-templates select="." mode="processSingleReferenceAuthor" />
						</xsl:otherwise>
					</xsl:choose>
					<xsl:if test="position()!=last()"><xsl:text>, </xsl:text></xsl:if>
				</xsl:for-each>
				<xsl:if test="normalize-space($lEditorAuthorshipType)=$gEditorAuthorshipEditorType">
					<xsl:text> </xsl:text>
					<xsl:choose>
						<xsl:when test="count(./*[@object_id='93']/*[@object_id='91']) &gt; 1">
							<xsl:text>(Ed.)</xsl:text>
						</xsl:when>
						<xsl:otherwise>
							<xsl:text>(Eds)</xsl:text>
						</xsl:otherwise>
					</xsl:choose>
				</xsl:if>
			</xsl:if>

			<xsl:text>. </xsl:text>
			<!-- Book Title -->
			<xsl:if test="normalize-space(./fields/*[@id='255']/value) != ''">
				<!-- Book title -->
				<span>
					<xsl:call-template name="markContentEditableField">
						<xsl:with-param name="pObjectId" select="./@object_id" />
						<xsl:with-param name="pFieldId">255</xsl:with-param>
					</xsl:call-template>
					<xsl:attribute name="field_id">255</xsl:attribute>
					<!--  <xsl:value-of select="php:function('parseReferenceItemTitle', string(./fields/*[@id='255']/value))"/>-->
					<xsl:apply-templates select="./fields/*[@id='255']/value" mode="formatting_nospace"/>
				</span>
				<xsl:text> </xsl:text>
			</xsl:if>
			<xsl:if test="normalize-space(./fields/*[@id='256']/value) != ''">
				<!-- Edition -->
				<span>
					<xsl:call-template name="markContentEditableField">
						<xsl:with-param name="pObjectId" select="./@object_id" />
						<xsl:with-param name="pFieldId">256</xsl:with-param>
					</xsl:call-template>
					<xsl:attribute name="field_id">256</xsl:attribute>
					<xsl:apply-templates select="./fields/*[@id='256']/value" mode="formatting_nospace"/>
				</span>
				<xsl:text> </xsl:text>
			</xsl:if>
			<!-- Volume -->
			<xsl:if test="$lVolume != ''">
				<xsl:text>, </xsl:text>
				<span>
					<xsl:call-template name="markContentEditableField">
						<xsl:with-param name="pObjectId" select="./@object_id" />
						<xsl:with-param name="pFieldId">258</xsl:with-param>
					</xsl:call-template>
					<xsl:attribute name="field_id">258</xsl:attribute>
					<xsl:apply-templates select="$lVolume" mode="formatting"/>
				</span>
			</xsl:if>
			<xsl:if test="normalize-space(./fields/*[@id='256']/value) != '' or $lVolume != ''">
				<xsl:text>. </xsl:text>
			</xsl:if>


			<!-- Publisher -->
			<span>
				<xsl:call-template name="markContentEditableField">
						<xsl:with-param name="pObjectId" select="./@object_id" />
						<xsl:with-param name="pFieldId">259</xsl:with-param>
					</xsl:call-template>
				<xsl:attribute name="field_id">259</xsl:attribute>
				<xsl:apply-templates select="./fields/*[@id='259']/value" mode="formatting"/>
			</span>
			<xsl:text>, </xsl:text>
			<!-- City -->
			<span>
				<xsl:call-template name="markContentEditableField">
						<xsl:with-param name="pObjectId" select="./@object_id" />
						<xsl:with-param name="pFieldId">260</xsl:with-param>
					</xsl:call-template>
				<xsl:attribute name="field_id">260</xsl:attribute>
				<xsl:apply-templates select="./fields/*[@id='260']/value" mode="formatting"/>
			</span>
			<!-- # of pages -->
			<xsl:text>, </xsl:text>
			<span>
				<xsl:call-template name="markContentEditableField">
						<xsl:with-param name="pObjectId" select="./@object_id" />
						<xsl:with-param name="pFieldId">261</xsl:with-param>
					</xsl:call-template>
				<xsl:attribute name="field_id">261</xsl:attribute>
				<xsl:apply-templates select="./fields/*[@id='261']/value" mode="formatting"/>
			</span>
			<xsl:text> pp. </xsl:text>

			<xsl:if test="normalize-space(./fields/*[@id='262']/value) != ''">
				<!-- Publication language -->
				<xsl:text>[In </xsl:text>
				<span>
					<xsl:call-template name="markContentEditableField">
						<xsl:with-param name="pObjectId" select="./@object_id" />
						<xsl:with-param name="pFieldId">262</xsl:with-param>
					</xsl:call-template>
					<xsl:attribute name="field_id">262</xsl:attribute>
					<xsl:apply-templates select="./fields/*[@id='262']/value" mode="formatting_nospace"/>					
				</span>
				<xsl:text>]</xsl:text>
			</xsl:if>
			<xsl:if test="normalize-space(./fields/*[@id='263']/value) != ''">
				<!-- URL -->
				<xsl:text> </xsl:text>
				<a>
					<xsl:attribute name="field_id">263</xsl:attribute>
					<xsl:attribute name="href">
						<xsl:value-of select="php:function('checkIfLinkContainsHttp', string(./fields/*[@id='263']/value))"/>
					</xsl:attribute>
					<xsl:attribute name="target"><xsl:text>_blank</xsl:text></xsl:attribute>
					<xsl:apply-templates select="./fields/*[@id='263']/value" mode="formatting_nospace"/>					
				</a>
			</xsl:if>
			<xsl:if test="normalize-space(./fields/*[@id='264']/value) != ''">
				<!-- ISBN -->
				<xsl:text> [ISBN </xsl:text>
				<span>
					<xsl:call-template name="markContentEditableField">
						<xsl:with-param name="pObjectId" select="./@object_id" />
						<xsl:with-param name="pFieldId">264</xsl:with-param>
					</xsl:call-template>
					<xsl:attribute name="field_id">264</xsl:attribute>
					<xsl:apply-templates select="./fields/*[@id='264']/value" mode="formatting_nospace"/>
				</span>
				<xsl:text>].</xsl:text>
			</xsl:if>
			<xsl:if test="normalize-space(./fields/*[@id='30']/value) != ''">
				<!-- DOI -->
				<xsl:text> DOI: </xsl:text>
				<span>
					<xsl:attribute name="field_id">30</xsl:attribute>
					<xsl:apply-templates select="./fields/*[@id='30']/value" mode="formatting_nospace"/>
				</span>
			</xsl:if>
		</li>
	</xsl:template>

	<!-- Journal Article biblio reference
	 -->
	<xsl:template match="*[@object_id='102']" mode="articleBack">
		<xsl:variable name="lVolume" select="./fields/*[@id='258']/value"></xsl:variable>
		<xsl:variable name="lAuthorshipType">
			<xsl:choose>
				<xsl:when test="count(./*[@object_id='92']) &gt; 0">
					<xsl:value-of select="./*[@object_id='92']/fields/*[@id='265']/value/@value_id"></xsl:value-of>
				</xsl:when>
				<xsl:when test="count(./*[@object_id='100']) &gt; 0">
					<xsl:value-of select="./*[@object_id='100']/fields/*[@id='281']/value/@value_id"></xsl:value-of>
				</xsl:when>
				<xsl:when test="count(./*[@object_id='101']) &gt; 0">
					<xsl:value-of select="./*[@object_id='101']/fields/*[@id='282']/value/@value_id"></xsl:value-of>
				</xsl:when>
			</xsl:choose>
		</xsl:variable>
		<li>
			<xsl:attribute name="instance_id"><xsl:value-of select="./@instance_id" /></xsl:attribute>
			<xsl:for-each select="./*[@object_id='92' or @object_id='100' or @object_id='101']/*[@object_id='90']">
				<xsl:choose>
					<xsl:when test="$lAuthorshipType = 3">
						<xsl:apply-templates select="." mode="processSingleReferenceAuthorFirstLast" />
					</xsl:when>
					<xsl:otherwise>
						<xsl:apply-templates select="." mode="processSingleReferenceAuthor" />
					</xsl:otherwise>
				</xsl:choose>
				<xsl:if test="position()!=last()"><xsl:text>, </xsl:text></xsl:if>
			</xsl:for-each>
			<!-- Year -->
			<xsl:apply-templates select="." mode="processSingleReferenceYear"/>
			<!-- Article Title -->
			<span>
				<xsl:call-template name="markContentEditableField">
						<xsl:with-param name="pObjectId" select="./@object_id" />
						<xsl:with-param name="pFieldId">276</xsl:with-param>
					</xsl:call-template>
				<xsl:attribute name="field_id">276</xsl:attribute>
				<!--xsl:apply-templates select="./fields/*[@id='276']/value" mode="formatting"/-->
				<!--  <xsl:value-of select="php:function('parseReferenceItemTitle', string(./fields/*[@id='276']/value))"/>-->
				<xsl:apply-templates select="./fields/*[@id='276']/value" mode="formatting_nospace"/>
			</span>

			<xsl:if test="normalize-space(./fields/*[@id='243']/value) != ''">
				<!-- Journal -->
				<xsl:text> </xsl:text>
				<span>
					<xsl:call-template name="markContentEditableField">
						<xsl:with-param name="pObjectId" select="./@object_id" />
						<xsl:with-param name="pFieldId">243</xsl:with-param>
					</xsl:call-template>
					<xsl:attribute name="field_id">243</xsl:attribute>
					<xsl:apply-templates select="./fields/*[@id='243']/value" mode="formatting"/>
				</span>
			</xsl:if>
			<xsl:if test="normalize-space(./fields/*[@id='258']/value) != ''">
				<xsl:text> </xsl:text>
				<!-- Volume -->
				<span>
					<xsl:call-template name="markContentEditableField">
						<xsl:with-param name="pObjectId" select="./@object_id" />
						<xsl:with-param name="pFieldId">258</xsl:with-param>
					</xsl:call-template>
					<xsl:attribute name="field_id">258</xsl:attribute>
					<xsl:apply-templates select="./fields/*[@id='258']/value" mode="formatting_nospace"/>					
				</span>
			</xsl:if>
			<xsl:if test="normalize-space(./fields/*[@id='27']/value) != ''">
				<xsl:text> (</xsl:text>
				<!-- Issue -->
				<span>
					<xsl:call-template name="markContentEditableField">
						<xsl:with-param name="pObjectId" select="./@object_id" />
						<xsl:with-param name="pFieldId">27</xsl:with-param>
					</xsl:call-template>
					<xsl:attribute name="field_id">27</xsl:attribute>
					<xsl:apply-templates select="./fields/*[@id='27']/value" mode="formatting"/>
				</span>
				<xsl:text>)</xsl:text>
			</xsl:if>
			<xsl:text>: </xsl:text>


			<!-- FirtsPage -->
			<span>
				<xsl:call-template name="markContentEditableField">
						<xsl:with-param name="pObjectId" select="./@object_id" />
						<xsl:with-param name="pFieldId">28</xsl:with-param>
					</xsl:call-template>
				<xsl:attribute name="field_id">28</xsl:attribute>
				<xsl:apply-templates select="./fields/*[@id='28']/value" mode="formatting_nospace"/>				
			</span>
			<!-- Last Page -->
			<xsl:if test="normalize-space(./fields/*[@id='29']/value) != ''">
				<xsl:text>-</xsl:text>
				<span>
					<xsl:call-template name="markContentEditableField">
						<xsl:with-param name="pObjectId" select="./@object_id" />
						<xsl:with-param name="pFieldId">29</xsl:with-param>
					</xsl:call-template>
					<xsl:attribute name="field_id">29</xsl:attribute>
					<xsl:apply-templates select="./fields/*[@id='29']/value" mode="formatting_nospace"/>
				</span>
			</xsl:if>
			<xsl:text>. </xsl:text>
			<xsl:if test="normalize-space(./fields/*[@id='262']/value) != ''">
				<!-- Publication language -->
				<xsl:text>[In </xsl:text>
				<span>
					<xsl:call-template name="markContentEditableField">
						<xsl:with-param name="pObjectId" select="./@object_id" />
						<xsl:with-param name="pFieldId">262</xsl:with-param>
					</xsl:call-template>
					<xsl:attribute name="field_id">262</xsl:attribute>
					<xsl:apply-templates select="./fields/*[@id='262']/value" mode="formatting_nospace"/>
				</span>
				<xsl:text>]</xsl:text>
			</xsl:if>
			<xsl:if test="normalize-space(./fields/*[@id='263']/value) != ''">
				<!-- URL -->
				<xsl:text> </xsl:text>
				<a>
					<xsl:attribute name="field_id">263</xsl:attribute>
					<xsl:attribute name="href">
						<xsl:value-of select="php:function('checkIfLinkContainsHttp', string(./fields/*[@id='263']/value))"/>
					</xsl:attribute>
					<xsl:attribute name="target"><xsl:text>_blank</xsl:text></xsl:attribute>
					<xsl:apply-templates select="./fields/*[@id='263']/value" mode="formatting_nospace"/>
				</a>
			</xsl:if>
			<xsl:if test="normalize-space(./fields/*[@id='264']/value) != ''">
				<!-- ISBN -->
				<xsl:text> [ISBN </xsl:text>
				<span>
					<xsl:call-template name="markContentEditableField">
						<xsl:with-param name="pObjectId" select="./@object_id" />
						<xsl:with-param name="pFieldId">264</xsl:with-param>
					</xsl:call-template>
					<xsl:attribute name="field_id">264</xsl:attribute>
					<xsl:apply-templates select="./fields/*[@id='264']/value" mode="formatting_nospace"/>
				</span>
				<xsl:text>].</xsl:text>
			</xsl:if>
			<xsl:if test="normalize-space(./fields/*[@id='30']/value) != ''">
				<!-- DOI -->
				<xsl:text> DOI: </xsl:text>
				<a>
					<xsl:attribute name="href"><xsl:text>http://dx.doi.org/</xsl:text><xsl:value-of select="./fields/*[@id='30']/value"></xsl:value-of></xsl:attribute>
					<xsl:attribute name="target"><xsl:text>_blank</xsl:text></xsl:attribute>
					<xsl:attribute name="field_id">30</xsl:attribute>
					<xsl:apply-templates select="./fields/*[@id='30']/value" mode="formatting_nospace"/>
				</a>
			</xsl:if>
		</li>
	</xsl:template>


	<!-- Conference paper biblio reference
	 -->
	<xsl:template match="*[@object_id='103']" mode="articleBack">
		<xsl:variable name="lAuthorshipType">
			<xsl:choose>
				<xsl:when test="count(./*[@object_id='92']) &gt; 0">
					<xsl:value-of select="./*[@object_id='92']/fields/*[@id='265']/value/@value_id"></xsl:value-of>
				</xsl:when>
				<xsl:when test="count(./*[@object_id='100']) &gt; 0">
					<xsl:value-of select="./*[@object_id='100']/fields/*[@id='281']/value/@value_id"></xsl:value-of>
				</xsl:when>
				<xsl:when test="count(./*[@object_id='101']) &gt; 0">
					<xsl:value-of select="./*[@object_id='101']/fields/*[@id='282']/value/@value_id"></xsl:value-of>
				</xsl:when>
			</xsl:choose>
		</xsl:variable>

		<xsl:variable name="lEditorAuthorshipType">
			<xsl:choose>
				<xsl:when test="count(./*[@object_id='93']) &gt; 0">
					<xsl:value-of select="./*[@object_id='93']/fields/*[@id='283']/value/@value_id"></xsl:value-of>
				</xsl:when>
			</xsl:choose>
		</xsl:variable>
		<xsl:variable name="lVolume" select="./fields/*[@id='258']/value"></xsl:variable>
		<li>
			<xsl:attribute name="instance_id"><xsl:value-of select="./@instance_id" /></xsl:attribute>
			<xsl:for-each select="./*[@object_id='92' or @object_id='100' or @object_id='101']/*[@object_id='90']">
				<xsl:choose>
					<xsl:when test="$lAuthorshipType = 3">
						<xsl:apply-templates select="." mode="processSingleReferenceAuthorFirstLast" />
					</xsl:when>
					<xsl:otherwise>
						<xsl:apply-templates select="." mode="processSingleReferenceAuthor" />
					</xsl:otherwise>
				</xsl:choose>
				<xsl:if test="position()!=last()"><xsl:text>, </xsl:text></xsl:if>
			</xsl:for-each>

			<!-- Year -->
			<xsl:apply-templates select="." mode="processSingleReferenceYear"/>
			<!-- Title -->
			<span>
				<xsl:call-template name="markContentEditableField">
						<xsl:with-param name="pObjectId" select="./@object_id" />
						<xsl:with-param name="pFieldId">26</xsl:with-param>
					</xsl:call-template>
				<xsl:attribute name="field_id">26</xsl:attribute>
				<xsl:value-of select="php:function('parseReferenceItemTitle', string(./fields/*[@id='26']/value))"/>
			</span>

			<xsl:if test="count(./*[@object_id='93']/*[@object_id='91']) &gt; 0">
				<xsl:text> In: </xsl:text>
				<xsl:for-each select="./*[@object_id='93']/*[@object_id='91']">
					<xsl:choose>
						<xsl:when test="$lEditorAuthorshipType = 3">
							<xsl:apply-templates select="." mode="processSingleReferenceAuthorFirstLast" />
						</xsl:when>
						<xsl:otherwise>
							<xsl:apply-templates select="." mode="processSingleReferenceAuthor" />
						</xsl:otherwise>
					</xsl:choose>
					<xsl:if test="position()!=last()"><xsl:text>, </xsl:text></xsl:if>
				</xsl:for-each>
				<xsl:if test="normalize-space($lEditorAuthorshipType)=$gEditorAuthorshipEditorType">
					<xsl:text> </xsl:text>
					<xsl:choose>
						<xsl:when test="count(./*[@object_id='93']/*[@object_id='91']) &gt; 1">
							<xsl:text>(Ed.)</xsl:text>
						</xsl:when>
						<xsl:otherwise>
							<xsl:text>(Eds)</xsl:text>
						</xsl:otherwise>
					</xsl:choose>
				</xsl:if>
			</xsl:if>

			<xsl:if test="normalize-space(./fields/*[@id='255']/value) != ''">
				<!-- Book title -->
				<xsl:text> </xsl:text>
				<span>
					<xsl:call-template name="markContentEditableField">
						<xsl:with-param name="pObjectId" select="./@object_id" />
						<xsl:with-param name="pFieldId">255</xsl:with-param>
					</xsl:call-template>
					<xsl:attribute name="field_id">255</xsl:attribute>
					<!-- <xsl:value-of select="php:function('parseReferenceItemTitle', string(./fields/*[@id='255']/value))"/> -->
					<xsl:apply-templates select="./fields/*[@id='255']/value" mode="formatting_nospace"/>
				</span>
			</xsl:if>
			<!-- Volume -->
			<xsl:if test="$lVolume != ''">
				<span>
					<xsl:call-template name="markContentEditableField">
						<xsl:with-param name="pObjectId" select="./@object_id" />
						<xsl:with-param name="pFieldId">258</xsl:with-param>
					</xsl:call-template>
					<xsl:attribute name="field_id">258</xsl:attribute>
					<xsl:apply-templates select="$lVolume" mode="formatting_nospace"/>
				</span>
				<xsl:text>. </xsl:text>
			</xsl:if>

			<xsl:if test="normalize-space(./fields/*[@id='272']/value) != ''">
				<!-- Conference name -->
				<span>
					<xsl:call-template name="markContentEditableField">
						<xsl:with-param name="pObjectId" select="./@object_id" />
						<xsl:with-param name="pFieldId">272</xsl:with-param>
					</xsl:call-template>
					<xsl:attribute name="field_id">272</xsl:attribute>
					<xsl:apply-templates select="./fields/*[@id='272']/value" mode="formatting"/>
				</span>
			</xsl:if>
			<xsl:if test="normalize-space(./fields/*[@id='273']/value) != ''">
				<!-- Conference LOCATION -->
				<xsl:if test="normalize-space(./fields/*[@id='272']/value) != ''">
					<xsl:text>, </xsl:text>
				</xsl:if>
				<span>
					<xsl:call-template name="markContentEditableField">
						<xsl:with-param name="pObjectId" select="./@object_id" />
						<xsl:with-param name="pFieldId">273</xsl:with-param>
					</xsl:call-template>
					<xsl:attribute name="field_id">273</xsl:attribute>
					<xsl:apply-templates select="./fields/*[@id='273']/value" mode="formatting"/>
				</span>
			</xsl:if>
			<xsl:if test="normalize-space(./fields/*[@id='274']/value) != ''">
				<!-- Conference Date -->
				<xsl:if test="normalize-space(./fields/*[@id='272']/value) != ''">
					<xsl:text>, </xsl:text>
				</xsl:if>
				<span>
					<xsl:call-template name="markContentEditableField">
						<xsl:with-param name="pObjectId" select="./@object_id" />
						<xsl:with-param name="pFieldId">274</xsl:with-param>
					</xsl:call-template>
					<xsl:attribute name="field_id">274</xsl:attribute>
					<xsl:apply-templates select="./fields/*[@id='274']/value" mode="formatting_nospace"/>
				</span>
			</xsl:if>
			<!-- Ако има поне 1 от горните - трябва да сложим точка накрая -->
			<xsl:if test="normalize-space(./fields/*[@id='272']/value) != '' or normalize-space(./fields/*[@id='273']/value) != '' or normalize-space(./fields/*[@id='274']/value) != ''">
				<xsl:text>. </xsl:text>
			</xsl:if>
			<!-- Publisher -->
			<xsl:if test="normalize-space(./*[@object_id=104]/fields/*[@id='259']/value) != ''">
				<span>
					<xsl:call-template name="markContentEditableField">
						<xsl:with-param name="pObjectId" select="./@object_id" />
						<xsl:with-param name="pFieldId">259</xsl:with-param>
					</xsl:call-template>
					<xsl:attribute name="instance_id"><xsl:value-of select="./*[@object_id=104]/@instance_id" /></xsl:attribute>
					<xsl:attribute name="field_id">259</xsl:attribute>
					<xsl:apply-templates select="./*[@object_id=104]/fields/*[@id='259']/value" mode="formatting"/>
				</span>
				<xsl:text>, </xsl:text>
			</xsl:if>
			<!-- City -->
			<xsl:if test="normalize-space(./*[@object_id=104]/fields/*[@id='260']/value) != ''">
				<span>
					<xsl:call-template name="markContentEditableField">
						<xsl:with-param name="pObjectId" select="./@object_id" />
						<xsl:with-param name="pFieldId">260</xsl:with-param>
					</xsl:call-template>
					<xsl:attribute name="instance_id"><xsl:value-of select="./*[@object_id=104]/@instance_id" /></xsl:attribute>
					<xsl:attribute name="field_id">260</xsl:attribute>
					<xsl:apply-templates select="./*[@object_id=104]/fields/*[@id='260']/value" mode="formatting"/>
				</span>
				<xsl:text>, </xsl:text>
			</xsl:if>
			<!-- # of pages -->
			<span>
				<xsl:call-template name="markContentEditableField">
						<xsl:with-param name="pObjectId" select="./@object_id" />
						<xsl:with-param name="pFieldId">261</xsl:with-param>
					</xsl:call-template>
				<xsl:attribute name="field_id">261</xsl:attribute>
				<xsl:apply-templates select="./fields/*[@id='261']/value" mode="formatting"/>
			</span>
			<xsl:text> pp. </xsl:text>

			<xsl:if test="normalize-space(./fields/*[@id='262']/value) != ''">
				<!-- Publication language -->
				<xsl:text>[In </xsl:text>
				<span>
					<xsl:call-template name="markContentEditableField">
						<xsl:with-param name="pObjectId" select="./@object_id" />
						<xsl:with-param name="pFieldId">262</xsl:with-param>
					</xsl:call-template>
					<xsl:attribute name="field_id">262</xsl:attribute>
					<xsl:apply-templates select="./fields/*[@id='262']/value" mode="formatting_nospace"/>					
				</span>
				<xsl:text>]</xsl:text>
			</xsl:if>
			<xsl:if test="normalize-space(./fields/*[@id='263']/value) != ''">
				<!-- URL -->
				<xsl:text> </xsl:text>
				<a>
					<xsl:attribute name="field_id">263</xsl:attribute>
					<xsl:attribute name="href">
						<xsl:value-of select="php:function('checkIfLinkContainsHttp', string(./fields/*[@id='263']/value))"/>
					</xsl:attribute>
					<xsl:attribute name="target"><xsl:text>_blank</xsl:text></xsl:attribute>
					<xsl:apply-templates select="./fields/*[@id='263']/value" mode="formatting_nospace"/>
				</a>
			</xsl:if>
			<xsl:if test="normalize-space(./fields/*[@id='264']/value) != ''">
				<!-- ISBN -->
				<xsl:text> [ISBN </xsl:text>
				<span>
					<xsl:call-template name="markContentEditableField">
						<xsl:with-param name="pObjectId" select="./@object_id" />
						<xsl:with-param name="pFieldId">264</xsl:with-param>
					</xsl:call-template>
					<xsl:attribute name="field_id">264</xsl:attribute>
					<xsl:apply-templates select="./fields/*[@id='264']/value" mode="formatting_nospace"/>
				</span>
				<xsl:text>].</xsl:text>
			</xsl:if>
			<xsl:if test="normalize-space(./fields/*[@id='30']/value) != ''">
				<!-- DOI -->
				<xsl:text> DOI: </xsl:text>
				<span>
					<xsl:attribute name="field_id">30</xsl:attribute>
					<xsl:apply-templates select="./fields/*[@id='30']/value" mode="formatting_nospace"/>
				</span>
			</xsl:if>
		</li>
	</xsl:template>

	<!-- Conference Proceedings biblio reference
	 -->
	<xsl:template match="*[@object_id='105']" mode="articleBack">
		<xsl:variable name="lAuthorshipType">
			<xsl:choose>
				<xsl:when test="count(./*[@object_id='92']) &gt; 0">
					<xsl:value-of select="./*[@object_id='92']/fields/*[@id='265']/value/@value_id"></xsl:value-of>
				</xsl:when>
				<xsl:when test="count(./*[@object_id='100']) &gt; 0">
					<xsl:value-of select="./*[@object_id='100']/fields/*[@id='281']/value/@value_id"></xsl:value-of>
				</xsl:when>
				<xsl:when test="count(./*[@object_id='101']) &gt; 0">
					<xsl:value-of select="./*[@object_id='101']/fields/*[@id='282']/value/@value_id"></xsl:value-of>
				</xsl:when>
			</xsl:choose>
		</xsl:variable>

		<xsl:variable name="lVolume" select="./fields/*[@id='258']/value"></xsl:variable>

		<li>
			<xsl:attribute name="instance_id"><xsl:value-of select="./@instance_id" /></xsl:attribute>
			<xsl:for-each select="./*[@object_id='92' or @object_id='100' or @object_id='101']/*[@object_id='90']">
				<xsl:choose>
					<xsl:when test="$lAuthorshipType = 3">
						<xsl:apply-templates select="." mode="processSingleReferenceAuthorFirstLast" />
					</xsl:when>
					<xsl:otherwise>
						<xsl:apply-templates select="." mode="processSingleReferenceAuthor" />
					</xsl:otherwise>
				</xsl:choose>
				<xsl:if test="position()!=last()"><xsl:text>, </xsl:text></xsl:if>
			</xsl:for-each>

			<xsl:if test="normalize-space($lAuthorshipType)=$gAuthorshipEditorType">
				<xsl:text> </xsl:text>
				<xsl:choose>
					<xsl:when test="count(./*[@object_id='92' or @object_id='100' or @object_id='101']/*[@object_id='90']) &gt; 1">
						<xsl:text>(Ed.)</xsl:text>
					</xsl:when>
					<xsl:otherwise>
						<xsl:text>(Eds)</xsl:text>
					</xsl:otherwise>
				</xsl:choose>
			</xsl:if>
			<!-- Year -->
			<xsl:apply-templates select="." mode="processSingleReferenceYear"/>
			<xsl:if test="normalize-space(./fields/*[@id='255']/value) != ''">
				<!-- Book title -->
				<span>
					<xsl:call-template name="markContentEditableField">
						<xsl:with-param name="pObjectId" select="./@object_id" />
						<xsl:with-param name="pFieldId">255</xsl:with-param>
					</xsl:call-template>
					<xsl:attribute name="field_id">255</xsl:attribute>
					<!-- <xsl:value-of select="php:function('parseReferenceItemTitle', string(./fields/*[@id='255']/value))"/> -->
					<xsl:apply-templates select="./fields/*[@id='255']/value" mode="formatting_nospace"/>
				</span>
				<xsl:text> </xsl:text>
			</xsl:if>
			<!-- Volume -->
			<xsl:if test="$lVolume != ''">
				<span>
					<xsl:call-template name="markContentEditableField">
						<xsl:with-param name="pObjectId" select="./@object_id" />
						<xsl:with-param name="pFieldId">258</xsl:with-param>
					</xsl:call-template>
					<xsl:attribute name="field_id">258</xsl:attribute>
					<xsl:apply-templates select="$lVolume" mode="formatting"/>
				</span>
				<xsl:text>. </xsl:text>
			</xsl:if>

			<xsl:if test="normalize-space(./fields/*[@id='272']/value) != ''">
				<!-- Conference name -->
				<span>
					<xsl:call-template name="markContentEditableField">
						<xsl:with-param name="pObjectId" select="./@object_id" />
						<xsl:with-param name="pFieldId">272</xsl:with-param>
					</xsl:call-template>
					<xsl:attribute name="field_id">272</xsl:attribute>
					<xsl:apply-templates select="./fields/*[@id='272']/value" mode="formatting"/>
				</span>
			</xsl:if>
			<xsl:if test="normalize-space(./fields/*[@id='273']/value) != ''">
				<!-- Conference LOCATION -->
				<xsl:if test="normalize-space(./fields/*[@id='272']/value) != ''">
					<xsl:text>, </xsl:text>
				</xsl:if>
				<span>
					<xsl:call-template name="markContentEditableField">
						<xsl:with-param name="pObjectId" select="./@object_id" />
						<xsl:with-param name="pFieldId">273</xsl:with-param>
					</xsl:call-template>
					<xsl:attribute name="field_id">273</xsl:attribute>
					<xsl:apply-templates select="./fields/*[@id='273']/value" mode="formatting"/>
				</span>
			</xsl:if>
			<xsl:if test="normalize-space(./fields/*[@id='274']/value) != ''">
				<!-- Conference Date -->
				<xsl:if test="normalize-space(./fields/*[@id='272']/value) != ''">
					<xsl:text>, </xsl:text>
				</xsl:if>
				<span>
					<xsl:call-template name="markContentEditableField">
						<xsl:with-param name="pObjectId" select="./@object_id" />
						<xsl:with-param name="pFieldId">274</xsl:with-param>
					</xsl:call-template>
					<xsl:attribute name="field_id">274</xsl:attribute>
					<xsl:apply-templates select="./fields/*[@id='274']/value" mode="formatting"/>
				</span>
			</xsl:if>
			<!-- Ако има поне 1 от горните - трябва да сложим точка накрая -->
			<xsl:if test="normalize-space(./fields/*[@id='272']/value) != '' or normalize-space(./fields/*[@id='273']/value) != '' or normalize-space(./fields/*[@id='274']/value) != ''">
				<xsl:text>. </xsl:text>
			</xsl:if>
			<!-- Publisher -->
			<span>
				<xsl:call-template name="markContentEditableField">
						<xsl:with-param name="pObjectId" select="./@object_id" />
						<xsl:with-param name="pFieldId">259</xsl:with-param>
					</xsl:call-template>
				<xsl:attribute name="instance_id"><xsl:value-of select="./*[@object_id=104]/@instance_id" /></xsl:attribute>
				<xsl:attribute name="field_id">259</xsl:attribute>
				<xsl:apply-templates select="./*[@object_id=104]/fields/*[@id='259']/value" mode="formatting"/>
			</span>
			<xsl:text>, </xsl:text>
			<!-- City -->
			<span>
				<xsl:call-template name="markContentEditableField">
						<xsl:with-param name="pObjectId" select="./@object_id" />
						<xsl:with-param name="pFieldId">260</xsl:with-param>
					</xsl:call-template>
				<xsl:attribute name="instance_id"><xsl:value-of select="./*[@object_id=104]/@instance_id" /></xsl:attribute>
				<xsl:attribute name="field_id">260</xsl:attribute>
				<xsl:apply-templates select="./*[@object_id=104]/fields/*[@id='260']/value" mode="formatting"/>
			</span>
			<xsl:text>. </xsl:text>
			<xsl:if test="normalize-space(./*[@object_id=104]/fields/*[@id='243']/value) != ''">
				<!-- Journal -->
				<span>
					<xsl:call-template name="markContentEditableField">
						<xsl:with-param name="pObjectId" select="./@object_id" />
						<xsl:with-param name="pFieldId">243</xsl:with-param>
					</xsl:call-template>
					<xsl:attribute name="instance_id"><xsl:value-of select="./*[@object_id=104]/@instance_id" /></xsl:attribute>
					<xsl:attribute name="field_id">243</xsl:attribute>
					<xsl:apply-templates select="./*[@object_id=104]/fields/*[@id='243']/value" mode="formatting"/>										
				</span>
				<xsl:text> </xsl:text>
			</xsl:if>
			<xsl:if test="normalize-space(./*[@object_id=104]/fields/*[@id='284']/value) != ''">
				<!-- VOLUME -->
				<span>
					<xsl:call-template name="markContentEditableField">
						<xsl:with-param name="pObjectId" select="./@object_id" />
						<xsl:with-param name="pFieldId">284</xsl:with-param>
					</xsl:call-template>
					<xsl:attribute name="instance_id"><xsl:value-of select="./*[@object_id=104]/@instance_id" /></xsl:attribute>
					<xsl:attribute name="field_id">284</xsl:attribute>
					<xsl:apply-templates select="./*[@object_id=104]/fields/*[@id='284']/value" mode="formatting"/>						
				</span>
				<xsl:text>. </xsl:text>
			</xsl:if>

			<!-- # of pages -->
			<span>
				<xsl:call-template name="markContentEditableField">
						<xsl:with-param name="pObjectId" select="./@object_id" />
						<xsl:with-param name="pFieldId">261</xsl:with-param>
					</xsl:call-template>
				<xsl:attribute name="field_id">261</xsl:attribute>
				<xsl:apply-templates select="./fields/*[@id='261']/value" mode="formatting"/>
			</span>
			<xsl:text> pp. </xsl:text>

			<xsl:if test="normalize-space(./fields/*[@id='262']/value) != ''">
				<!-- Publication language -->
				<xsl:text>[In </xsl:text>
				<span>
					<xsl:call-template name="markContentEditableField">
						<xsl:with-param name="pObjectId" select="./@object_id" />
						<xsl:with-param name="pFieldId">262</xsl:with-param>
					</xsl:call-template>
					<xsl:attribute name="field_id">262</xsl:attribute>
					<xsl:apply-templates select="./fields/*[@id='262']/value" mode="formatting_nospace"/>
				</span>
				<xsl:text>]</xsl:text>
			</xsl:if>
			<xsl:if test="normalize-space(./fields/*[@id='263']/value) != ''">
				<!-- URL -->
				<xsl:text> </xsl:text>
				<a>
					<xsl:attribute name="field_id">263</xsl:attribute>
					<xsl:attribute name="href">
						<xsl:value-of select="php:function('checkIfLinkContainsHttp', string(./fields/*[@id='263']/value))"/>
					</xsl:attribute>
					<xsl:attribute name="target"><xsl:text>_blank</xsl:text></xsl:attribute>
					<xsl:apply-templates select="./fields/*[@id='263']/value" mode="formatting_nospace"/>
				</a>
			</xsl:if>
			<xsl:if test="normalize-space(./fields/*[@id='264']/value) != ''">
				<!-- ISBN -->
				<xsl:text> [ISBN </xsl:text>
				<span>
					<xsl:call-template name="markContentEditableField">
						<xsl:with-param name="pObjectId" select="./@object_id" />
						<xsl:with-param name="pFieldId">264</xsl:with-param>
					</xsl:call-template>
					<xsl:attribute name="field_id">264</xsl:attribute>
					<xsl:apply-templates select="./fields/*[@id='264']/value" mode="formatting"/>
				</span>
				<xsl:text>].</xsl:text>
			</xsl:if>
			<xsl:if test="normalize-space(./fields/*[@id='30']/value) != ''">
				<!-- DOI -->
				<xsl:text> DOI: </xsl:text>
				<span>
					<xsl:attribute name="field_id">30</xsl:attribute>
					<xsl:apply-templates select="./fields/*[@id='30']/value" mode="formatting_nospace"/>
				</span>
			</xsl:if>
		</li>
	</xsl:template>

	<!-- Thesis biblio reference
	 -->
	<xsl:template match="*[@object_id='106']" mode="articleBack">
		<xsl:variable name="lAuthorshipType">
			<xsl:choose>
				<xsl:when test="count(./*[@object_id='92']) &gt; 0">
					<xsl:value-of select="./*[@object_id='92']/fields/*[@id='265']/value/@value_id"></xsl:value-of>
				</xsl:when>
				<xsl:when test="count(./*[@object_id='100']) &gt; 0">
					<xsl:value-of select="./*[@object_id='100']/fields/*[@id='281']/value/@value_id"></xsl:value-of>
				</xsl:when>
				<xsl:when test="count(./*[@object_id='101']) &gt; 0">
					<xsl:value-of select="./*[@object_id='101']/fields/*[@id='282']/value/@value_id"></xsl:value-of>
				</xsl:when>
			</xsl:choose>
		</xsl:variable>
		<xsl:variable name="lVolume" select="./fields/*[@id='258']/value"></xsl:variable>

		<li>
			<xsl:attribute name="instance_id"><xsl:value-of select="./@instance_id" /></xsl:attribute>
			<xsl:for-each select="./*[@object_id='92' or @object_id='100' or @object_id='101']/*[@object_id='90']">
				<xsl:choose>
					<xsl:when test="$lAuthorshipType = 3">
						<xsl:apply-templates select="." mode="processSingleReferenceAuthorFirstLast" />
					</xsl:when>
					<xsl:otherwise>
						<xsl:apply-templates select="." mode="processSingleReferenceAuthor" />
					</xsl:otherwise>
				</xsl:choose>
				<xsl:if test="position()!=last()"><xsl:text>, </xsl:text></xsl:if>
			</xsl:for-each>
			<!-- Year -->
			<xsl:apply-templates select="." mode="processSingleReferenceYear"/>
			<xsl:if test="normalize-space(./fields/*[@id='255']/value) != ''">
				<!-- Book title -->
				<span>
					<xsl:call-template name="markContentEditableField">
						<xsl:with-param name="pObjectId" select="./@object_id" />
						<xsl:with-param name="pFieldId">255</xsl:with-param>
					</xsl:call-template>
					<xsl:attribute name="field_id">255</xsl:attribute>
					<!-- <xsl:value-of select="php:function('parseReferenceItemTitle', string(./fields/*[@id='255']/value))"/> -->
					<xsl:apply-templates select="./fields/*[@id='255']/value" mode="formatting_nospace"/>
				</span>
				<xsl:text> </xsl:text>
			</xsl:if>
			<xsl:if test="normalize-space(./fields/*[@id='257']/value) != ''">
				<!-- Translated title -->
				<xsl:text>[</xsl:text>
				<span>
					<xsl:call-template name="markContentEditableField">
						<xsl:with-param name="pObjectId" select="./@object_id" />
						<xsl:with-param name="pFieldId">257</xsl:with-param>
					</xsl:call-template>
					<xsl:attribute name="field_id">257</xsl:attribute>
					<!-- <xsl:value-of select="php:function('parseReferenceItemTitle', string(./fields/*[@id='257']/value))"/> -->
					<xsl:apply-templates select="./fields/*[@id='257']/value" mode="formatting_nospace"/>
				</span>
				<xsl:text>] </xsl:text>
			</xsl:if>

			<!-- Publisher -->
			<span>
				<xsl:call-template name="markContentEditableField">
						<xsl:with-param name="pObjectId" select="./@object_id" />
						<xsl:with-param name="pFieldId">259</xsl:with-param>
					</xsl:call-template>
				<xsl:attribute name="field_id">259</xsl:attribute>
				<xsl:apply-templates select="./fields/*[@id='259']/value" mode="formatting"/>
			</span>
			<xsl:text>, </xsl:text>
			<!-- City -->
			<span>
				<xsl:call-template name="markContentEditableField">
						<xsl:with-param name="pObjectId" select="./@object_id" />
						<xsl:with-param name="pFieldId">260</xsl:with-param>
					</xsl:call-template>
				<xsl:attribute name="field_id">260</xsl:attribute>
				<xsl:apply-templates select="./fields/*[@id='260']/value" mode="formatting"/>
			</span>
			<!-- # of pages -->
			<xsl:text>, </xsl:text>
			<span>
				<xsl:call-template name="markContentEditableField">
						<xsl:with-param name="pObjectId" select="./@object_id" />
						<xsl:with-param name="pFieldId">261</xsl:with-param>
					</xsl:call-template>
				<xsl:attribute name="field_id">261</xsl:attribute>
				<xsl:apply-templates select="./fields/*[@id='261']/value" mode="formatting"/>
			</span>
			<xsl:text> pp. </xsl:text>

			<xsl:if test="normalize-space(./fields/*[@id='262']/value) != ''">
				<!-- Publication language -->
				<xsl:text>[In </xsl:text>
				<span>
					<xsl:call-template name="markContentEditableField">
						<xsl:with-param name="pObjectId" select="./@object_id" />
						<xsl:with-param name="pFieldId">262</xsl:with-param>
					</xsl:call-template>
					<xsl:attribute name="field_id">262</xsl:attribute>
					<xsl:apply-templates select="./fields/*[@id='262']/value" mode="formatting"/>					
				</span>
				<xsl:text>]</xsl:text>
			</xsl:if>
			<xsl:if test="normalize-space(./fields/*[@id='263']/value) != ''">
				<!-- URL -->
				<xsl:text> </xsl:text>
				<a>
					<xsl:attribute name="field_id">263</xsl:attribute>
					<xsl:attribute name="href">
						<xsl:value-of select="php:function('checkIfLinkContainsHttp', string(./fields/*[@id='263']/value))"/>
					</xsl:attribute>
					<xsl:attribute name="target"><xsl:text>_blank</xsl:text></xsl:attribute>
					<xsl:apply-templates select="./fields/*[@id='263']/value" mode="formatting_nospace"/>					
				</a>
			</xsl:if>
			<xsl:if test="normalize-space(./fields/*[@id='264']/value) != ''">
				<!-- ISBN -->
				<xsl:text> [ISBN </xsl:text>
				<span>
					<xsl:call-template name="markContentEditableField">
						<xsl:with-param name="pObjectId" select="./@object_id" />
						<xsl:with-param name="pFieldId">264</xsl:with-param>
					</xsl:call-template>
					<xsl:attribute name="field_id">264</xsl:attribute>
					<xsl:apply-templates select="./fields/*[@id='264']/value" mode="formatting"/>
				</span>
				<xsl:text>].</xsl:text>
			</xsl:if>
			<xsl:if test="normalize-space(./fields/*[@id='30']/value) != ''">
				<!-- DOI -->
				<xsl:text> DOI: </xsl:text>
				<span>
					<xsl:attribute name="field_id">30</xsl:attribute>
					<xsl:apply-templates select="./fields/*[@id='30']/value" mode="formatting"/>
				</span>
			</xsl:if>
		</li>
	</xsl:template>

	<!-- Software reference
	 -->
	<xsl:template match="*[@object_id='107']" mode="articleBack">
		<xsl:variable name="lRefId" select="php:function('getReferenceId', string(./@instance_id))"></xsl:variable>
		<xsl:variable name="lAuthorshipType">
			<xsl:choose>
				<xsl:when test="count(./*[@object_id='92']) &gt; 0">
					<xsl:value-of select="./*[@object_id='92']/fields/*[@id='265']/value/@value_id"></xsl:value-of>
				</xsl:when>
				<xsl:when test="count(./*[@object_id='100']) &gt; 0">
					<xsl:value-of select="./*[@object_id='100']/fields/*[@id='281']/value/@value_id"></xsl:value-of>
				</xsl:when>
				<xsl:when test="count(./*[@object_id='101']) &gt; 0">
					<xsl:value-of select="./*[@object_id='101']/fields/*[@id='282']/value/@value_id"></xsl:value-of>
				</xsl:when>
			</xsl:choose>
		</xsl:variable>
		<li>
			<xsl:attribute name="instance_id"><xsl:value-of select="./@instance_id" /></xsl:attribute>
			<xsl:for-each select="./*[@object_id='92' or @object_id='100' or @object_id='101']/*[@object_id='90']">
				<xsl:choose>
					<xsl:when test="$lAuthorshipType = 3">
						<xsl:apply-templates select="." mode="processSingleReferenceAuthorFirstLast" />
					</xsl:when>
					<xsl:otherwise>
						<xsl:apply-templates select="." mode="processSingleReferenceAuthor" />
					</xsl:otherwise>
				</xsl:choose>
				<xsl:if test="position()!=last()"><xsl:text>, </xsl:text></xsl:if>
			</xsl:for-each>
			<!-- Year -->
			<xsl:apply-templates select="." mode="processSingleReferenceYear"/>
			<!-- Title -->
			<span>
				<xsl:call-template name="markContentEditableField">
						<xsl:with-param name="pObjectId" select="./@object_id" />
						<xsl:with-param name="pFieldId">26</xsl:with-param>
					</xsl:call-template>
				<xsl:attribute name="field_id">26</xsl:attribute>
				<!-- <xsl:value-of select="php:function('parseReferenceItemTitle', string(./fields/*[@id='26']/value))"/> -->
				<xsl:apply-templates select="./fields/*[@id='26']/value" mode="formatting_nospace"/>
			</span>
			<xsl:text> </xsl:text>
			<xsl:if test="normalize-space(./fields/*[@id='279']/value) != ''">
				<!-- Version -->
				<span>
					<xsl:call-template name="markContentEditableField">
						<xsl:with-param name="pObjectId" select="./@object_id" />
						<xsl:with-param name="pFieldId">279</xsl:with-param>
					</xsl:call-template>
					<xsl:attribute name="field_id">279</xsl:attribute>
					<xsl:apply-templates select="./fields/*[@id='279']/value" mode="formatting"/>					
				</span>
				<xsl:text>. </xsl:text>
			</xsl:if>
			<!-- Publisher -->
			<span>
				<xsl:call-template name="markContentEditableField">
						<xsl:with-param name="pObjectId" select="./@object_id" />
						<xsl:with-param name="pFieldId">259</xsl:with-param>
				</xsl:call-template>
				<xsl:attribute name="field_id">259</xsl:attribute>
				<xsl:apply-templates select="./fields/*[@id='259']/value" mode="formatting"/>
			</span>
			<xsl:if test="normalize-space(./fields/*[@id='285']/value) != ''">
				<!-- Release date -->
				<xsl:text>Release date: </xsl:text>
				<span>
					<xsl:attribute name="field_id">285</xsl:attribute>
					<xsl:value-of select="php:function('customDateFormat', string(./fields/*[@id='285']/value))"/>
				</span>
				<xsl:text>.</xsl:text>
			</xsl:if>
			<xsl:if test="normalize-space(./fields/*[@id='263']/value) != ''">
				<!-- URL -->
				<xsl:text> </xsl:text>
				<a>
					<xsl:attribute name="field_id">263</xsl:attribute>
					<xsl:attribute name="href">
						<xsl:value-of select="php:function('checkIfLinkContainsHttp', string(./fields/*[@id='263']/value))"/>
					</xsl:attribute>
					<xsl:attribute name="target"><xsl:text>_blank</xsl:text></xsl:attribute>
					<xsl:apply-templates select="./fields/*[@id='263']/value" mode="formatting_nospace"/>					
				</a>
			</xsl:if>
		</li>
	</xsl:template>

	<!-- Website reference
	 -->
	<xsl:template match="*[@object_id='108']" mode="articleBack">
		<li>
			<xsl:attribute name="instance_id"><xsl:value-of select="./@instance_id" /></xsl:attribute>
			<!-- Title -->
			<span>
				<xsl:call-template name="markContentEditableField">
						<xsl:with-param name="pObjectId" select="./@object_id" />
						<xsl:with-param name="pFieldId">26</xsl:with-param>
					</xsl:call-template>
				<xsl:attribute name="field_id">26</xsl:attribute>
				<!-- <xsl:value-of select="php:function('parseReferenceItemTitle', string(./fields/*[@id='26']/value))"/> -->
				<xsl:apply-templates select="./fields/*[@id='26']/value" mode="formatting_nospace"/>
			</span>
			<xsl:text> </xsl:text>
			<xsl:if test="normalize-space(./fields/*[@id='263']/value) != ''">
				<!-- URL -->
				<xsl:text> </xsl:text>
				<a>
					<xsl:attribute name="field_id">263</xsl:attribute>
					<xsl:attribute name="href">
						<xsl:value-of select="php:function('checkIfLinkContainsHttp', string(./fields/*[@id='263']/value))"/>
					</xsl:attribute>
					<xsl:attribute name="target"><xsl:text>_blank</xsl:text></xsl:attribute>					
					<xsl:apply-templates select="./fields/*[@id='263']/value" mode="formatting_nospace"/>
				</a>
			</xsl:if>
			<xsl:if test="normalize-space(./fields/*[@id='280']/value) != ''">
				<!-- Access date -->
				<xsl:text> Accession date: </xsl:text>
				<span>
					<xsl:attribute name="field_id">280</xsl:attribute>
					<xsl:value-of select="php:function('customDateFormat', string(./fields/*[@id='280']/value))"/>
				</span>
				<xsl:text>.</xsl:text>
			</xsl:if>
		</li>
	</xsl:template>

</xsl:stylesheet>