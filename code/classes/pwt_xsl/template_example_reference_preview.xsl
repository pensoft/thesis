<?xml version='1.0'?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:tp="http://www.plazi.org/taxpub"  xmlns:php="http://php.net/xsl" exclude-result-prefixes="php tp xlink xsl">
	<xsl:output method="xml" encoding="UTF-8" indent="yes" omit-xml-declaration="no" >
	</xsl:output>

	<xsl:param  name="gParsedPubyear"></xsl:param>
	<xsl:param  name="gPreviewType"></xsl:param>
	<xsl:param  name="gReferenceId"></xsl:param>

	<xsl:variable name="gBasePreviewType">1</xsl:variable>
	<xsl:variable name="gPreviewMode1Type">2</xsl:variable>
	<xsl:variable name="gPreviewMode2Type">3</xsl:variable>

	<xsl:variable name="gAuthorshipEditorType">2</xsl:variable>

	<xsl:variable name="gEditorAuthorshipEditorType">1</xsl:variable>

	<xsl:template match="/document">

		<xsl:for-each select="/document/objects//*[@object_id='95']/*[@object_id &gt; 0]">
			<xsl:variable name="lReferenceId" select="./ancestor::*[@object_id='95']/@instance_id"></xsl:variable>
			<div>
				<xsl:attribute name="id">
					<xsl:text>Reference-Preview-Wrapper</xsl:text>
					<xsl:value-of select="$lReferenceId"></xsl:value-of>
				</xsl:attribute>
				<label>
					<xsl:attribute name="for">
						<xsl:text>ref-</xsl:text>
						<xsl:value-of select="$lReferenceId"></xsl:value-of>
					</xsl:attribute>
				<xsl:apply-templates select="." mode="previewBaseMode"></xsl:apply-templates>
				</label>
				<div class="hiddenElement">
					<xsl:attribute name="id">
						<xsl:text>Ref-Preview-</xsl:text>
						<xsl:value-of select="$lReferenceId"></xsl:value-of>
						<xsl:text>-Mode-1</xsl:text>
					</xsl:attribute>
					<xsl:apply-templates select="." mode="previewMode1"></xsl:apply-templates>
				</div>
				<div class="hiddenElement">
					<xsl:attribute name="id">
						<xsl:text>Ref-Preview-</xsl:text>
						<xsl:value-of select="$lReferenceId"></xsl:value-of>
						<xsl:text>-Mode-2</xsl:text>
					</xsl:attribute>
					<xsl:apply-templates select="." mode="previewMode2"></xsl:apply-templates>
				</div>
			</div>
		</xsl:for-each>
	</xsl:template>

	<xsl:template match="*" mode="processReferenceYear">
		<xsl:value-of select="./fields/*[@id='254']/value"></xsl:value-of>
		<xsl:value-of select="php:function('getReferenceYearLetter', string(./ancestor::*[@object_id='95']/@instance_id))"></xsl:value-of>
	</xsl:template>


	<xsl:template match="*" mode="processSingleAuthor">
		<xsl:variable name="lAuthorParsedName">
			<!-- Last name -->
			<xsl:value-of select="./fields/*[@id='252']/value"></xsl:value-of>
			<xsl:text> </xsl:text>
			<!-- Initials of first name -->
			<xsl:value-of select="php:function('mb_substr', string(./fields/*[@id='251']/value), 0, 1, 'utf-8')"></xsl:value-of>			
		</xsl:variable>
		<xsl:value-of select="normalize-space($lAuthorParsedName)"></xsl:value-of>
	</xsl:template>

	<xsl:template match="*" mode="processSingleReferenceAuthorFirstLast">
		<xsl:variable name="lAuthorParsedName">
			<!-- First and Last name -->

			<xsl:value-of select="./fields/*[@id='250']/value"></xsl:value-of>
		</xsl:variable>
		<span>
			<xsl:attribute name="instance_id"><xsl:value-of select="./@instance_id" /></xsl:attribute>
			<xsl:value-of select="normalize-space($lAuthorParsedName)"></xsl:value-of>
		</span>
	</xsl:template>

	<!-- Book biblio reference Base Preview
	 -->
	<xsl:template match="*[@object_id='98']" mode="previewBaseMode">
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

		<xsl:for-each select="./*[@object_id='92' or @object_id='100' or @object_id='101']/*[@object_id='90']">
			<xsl:choose>
				<xsl:when test="$lAuthorshipType = 3">
					<xsl:apply-templates select="." mode="processSingleReferenceAuthorFirstLast" />
				</xsl:when>
				<xsl:otherwise>
					<xsl:apply-templates select="." mode="processSingleAuthor" />
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
		<xsl:text> (</xsl:text><xsl:apply-templates select="." mode="processReferenceYear" /><xsl:text>) </xsl:text>
		<!-- Book Title -->
		<xsl:value-of select="php:function('parseReferenceItemTitle', string(./fields/*[@id='255']/value))"/>
		<xsl:text> </xsl:text>
		<xsl:if test="normalize-space(./fields/*[@id='257']/value) != ''">
			<!-- Translated title -->
			<xsl:text>[</xsl:text>
			<xsl:value-of select="php:function('parseReferenceItemTitle', string(./fields/*[@id='257']/value))"/>
			<xsl:text>]</xsl:text>
		</xsl:if>
		<xsl:if test="normalize-space(./fields/*[@id='256']/value) != ''">
			<xsl:text> </xsl:text>
			<!-- Edition -->
			<xsl:value-of select="./fields/*[@id='256']/value"/>
		</xsl:if>
		<!-- Volume -->
		<xsl:if test="$lVolume != ''">
			<xsl:text>, </xsl:text>
			<xsl:value-of select="$lVolume" />
		</xsl:if>
		<xsl:if test="normalize-space(./fields/*[@id='256']/value) != '' or $lVolume != ''">
			<xsl:text>. </xsl:text>
		</xsl:if>

		<!-- Publisher -->
		<xsl:value-of select="./fields/*[@id='259']/value"></xsl:value-of>
		<xsl:text>, </xsl:text>
		<!-- City -->
		<xsl:value-of select="./fields/*[@id='260']/value"></xsl:value-of>
		<xsl:text>. </xsl:text>
		<!-- # of pages -->
		<xsl:value-of select="./fields/*[@id='261']/value"></xsl:value-of>
		<xsl:text> pp. </xsl:text>
		<xsl:if test="normalize-space(./fields/*[@id='262']/value) != ''">
			<!-- Publication language -->
			<xsl:text>[In </xsl:text>
			<xsl:value-of select="./fields/*[@id='262']/value"></xsl:value-of>
			<xsl:text>]</xsl:text>
		</xsl:if>
		<xsl:if test="normalize-space(./fields/*[@id='263']/value) != ''">
			<!-- URL -->
			<xsl:text> </xsl:text>
			<xsl:value-of select="./fields/*[@id='263']/value"></xsl:value-of>
		</xsl:if>
		<xsl:if test="normalize-space(./fields/*[@id='264']/value) != ''">
			<!-- ISBN -->
			<xsl:text> [ISBN </xsl:text>
			<xsl:value-of select="./fields/*[@id='264']/value"></xsl:value-of>
			<xsl:text>].</xsl:text>
		</xsl:if>
		<xsl:if test="normalize-space(./fields/*[@id='30']/value) != ''">
			<!-- DOI -->
			<xsl:text> DOI: </xsl:text>
			<xsl:value-of select="./fields/*[@id='30']/value"></xsl:value-of>
		</xsl:if>
	</xsl:template>

	<!-- Journal Article reference Base Preview
	 -->
	<xsl:template match="*[@object_id='102']" mode="previewBaseMode">
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
		<xsl:for-each select="./*[@object_id='92' or @object_id='100' or @object_id='101']/*[@object_id='90']">
			<xsl:choose>
				<xsl:when test="$lAuthorshipType = 3">
					<xsl:apply-templates select="." mode="processSingleReferenceAuthorFirstLast" />
				</xsl:when>
				<xsl:otherwise>
					<xsl:apply-templates select="." mode="processSingleAuthor" />
				</xsl:otherwise>
			</xsl:choose>
			<xsl:if test="position()!=last()"><xsl:text>, </xsl:text></xsl:if>
		</xsl:for-each>
		<!-- Year -->
		<xsl:text> (</xsl:text><xsl:apply-templates select="." mode="processReferenceYear" /><xsl:text>) </xsl:text>
		<!-- Article Title -->
		<xsl:value-of select="php:function('parseReferenceItemTitle', string(./fields/*[@id='276']/value))"/>
		<xsl:if test="normalize-space(./fields/*[@id='243']/value) != ''">
			<!-- Journal -->
			<xsl:text> </xsl:text>
			<xsl:value-of select="./fields/*[@id='243']/value"></xsl:value-of>
		</xsl:if>
		<xsl:if test="normalize-space(./fields/*[@id='258']/value) != ''">
			<xsl:text> </xsl:text>
			<!-- Volume -->
			<xsl:value-of select="./fields/*[@id='258']/value"></xsl:value-of>
		</xsl:if>
		<xsl:if test="normalize-space(./fields/*[@id='27']/value) != ''">
			<xsl:text> (</xsl:text>
			<!-- Issue -->
			<xsl:value-of select="./fields/*[@id='27']/value"></xsl:value-of>
			<xsl:text>)</xsl:text>
		</xsl:if>
		<xsl:text>: </xsl:text>
		<!-- FirtsPage -->
		<xsl:value-of select="./fields/*[@id='28']/value"></xsl:value-of>
		<!-- Last Page -->
		<xsl:if test="normalize-space(./fields/*[@id='28']/value) != ''">
			<xsl:text>-</xsl:text>
			<xsl:value-of select="./fields/*[@id='29']/value" />
		</xsl:if>
		<xsl:text>.</xsl:text>
		<xsl:if test="normalize-space(./fields/*[@id='262']/value) != ''">
			<!-- Publication language -->
			<xsl:text> [In </xsl:text>
			<xsl:value-of select="./fields/*[@id='262']/value"></xsl:value-of>
			<xsl:text>]</xsl:text>
		</xsl:if>
		<xsl:if test="normalize-space(./fields/*[@id='263']/value) != ''">
			<!-- URL -->
			<xsl:text> </xsl:text>
			<xsl:value-of select="./fields/*[@id='263']/value"></xsl:value-of>
		</xsl:if>
		<xsl:if test="normalize-space(./fields/*[@id='30']/value) != ''">
			<!-- DOI -->
			<xsl:text> DOI: </xsl:text>
			<xsl:value-of select="./fields/*[@id='30']/value"></xsl:value-of>
		</xsl:if>
	</xsl:template>

	<!-- Book chapter reference Base Preview
	 -->
	<xsl:template match="*[@object_id='99']" mode="previewBaseMode">
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

		<xsl:for-each select="./*[@object_id='92' or @object_id='100' or @object_id='101']/*[@object_id='90']">
			<xsl:choose>
				<xsl:when test="$lAuthorshipType = 3">
					<xsl:apply-templates select="." mode="processSingleReferenceAuthorFirstLast" />
				</xsl:when>
				<xsl:otherwise>
					<xsl:apply-templates select="." mode="processSingleAuthor" />
				</xsl:otherwise>
			</xsl:choose>
			<xsl:if test="position()!=last()"><xsl:text>, </xsl:text></xsl:if>
		</xsl:for-each>


		<!-- Year -->
		<xsl:text> (</xsl:text><xsl:apply-templates select="." mode="processReferenceYear" /><xsl:text>) </xsl:text>
		<!-- Chapter Title -->
		<xsl:value-of select="php:function('parseReferenceItemTitle', string(./fields/*[@id='271']/value))"/>
		<xsl:text> </xsl:text>

		<xsl:if test="count(./*[@object_id='93']/*[@object_id='91']) &gt; 0">
			<xsl:text>In: </xsl:text>
			<xsl:for-each select="./*[@object_id='93']/*[@object_id='91']">
				<xsl:apply-templates select="." mode="processSingleAuthor" />
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
			<xsl:text>[</xsl:text>
			<xsl:value-of select="php:function('parseReferenceItemTitle', string(./fields/*[@id='255']/value))"/>
			<xsl:text>]</xsl:text>
		</xsl:if>
		<xsl:if test="normalize-space(./fields/*[@id='256']/value) != ''">
			<xsl:text> </xsl:text>
			<!-- Edition -->
			<xsl:value-of select="./fields/*[@id='256']/value"/>
		</xsl:if>
		<!-- Volume -->
		<xsl:if test="$lVolume != ''">
			<xsl:text>, </xsl:text>
			<xsl:value-of select="$lVolume" />
		</xsl:if>
		<xsl:if test="normalize-space(./fields/*[@id='256']/value) != '' or $lVolume != ''">
			<xsl:text>. </xsl:text>
		</xsl:if>

		<!-- Publisher -->
		<xsl:value-of select="./fields/*[@id='259']/value"></xsl:value-of>
		<xsl:text>, </xsl:text>
		<!-- City -->
		<xsl:value-of select="./fields/*[@id='260']/value"></xsl:value-of>
		<xsl:text>. </xsl:text>
		<!-- # of pages -->
		<xsl:value-of select="./fields/*[@id='261']/value"></xsl:value-of>
		<xsl:text> pp. </xsl:text>
		<xsl:if test="normalize-space(./fields/*[@id='262']/value) != ''">
			<!-- Publication language -->
			<xsl:text>[In </xsl:text>
			<xsl:value-of select="./fields/*[@id='262']/value"></xsl:value-of>
			<xsl:text>]</xsl:text>
		</xsl:if>
		<xsl:if test="normalize-space(./fields/*[@id='263']/value) != ''">
			<!-- URL -->
			<xsl:text> </xsl:text>
			<xsl:value-of select="./fields/*[@id='263']/value"></xsl:value-of>
		</xsl:if>
		<xsl:if test="normalize-space(./fields/*[@id='264']/value) != ''">
			<!-- ISBN -->
			<xsl:text> [ISBN </xsl:text>
			<xsl:value-of select="./fields/*[@id='264']/value"></xsl:value-of>
			<xsl:text>].</xsl:text>
		</xsl:if>
		<xsl:if test="normalize-space(./fields/*[@id='30']/value) != ''">
			<!-- DOI -->
			<xsl:text> DOI: </xsl:text>
			<xsl:value-of select="./fields/*[@id='30']/value"></xsl:value-of>
		</xsl:if>
	</xsl:template>

	<!-- Conference paper Base Preview
	 -->
	<xsl:template match="*[@object_id='103']" mode="previewBaseMode">
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

		<xsl:for-each select="./*[@object_id='92' or @object_id='100' or @object_id='101']/*[@object_id='90']">
			<xsl:choose>
				<xsl:when test="$lAuthorshipType = 3">
					<xsl:apply-templates select="." mode="processSingleReferenceAuthorFirstLast" />
				</xsl:when>
				<xsl:otherwise>
					<xsl:apply-templates select="." mode="processSingleAuthor" />
				</xsl:otherwise>
			</xsl:choose>
			<xsl:if test="position()!=last()"><xsl:text>, </xsl:text></xsl:if>
		</xsl:for-each>


		<!-- Year -->
		<xsl:text> (</xsl:text><xsl:apply-templates select="." mode="processReferenceYear" /><xsl:text>) </xsl:text>
		<!-- Title -->
		<xsl:value-of select="php:function('parseReferenceItemTitle', string(./fields/*[@id='26']/value))"/>
		<xsl:text> </xsl:text>

		<xsl:if test="count(./*[@object_id='93']/*[@object_id='91']) &gt; 0">
			<xsl:text>In: </xsl:text>
			<xsl:for-each select="./*[@object_id='93']/*[@object_id='91']">
				<xsl:apply-templates select="." mode="processSingleAuthor" />
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
			<xsl:text>[</xsl:text>
			<xsl:value-of select="php:function('parseReferenceItemTitle', string(./fields/*[@id='255']/value))"/>
			<xsl:text>] </xsl:text>
		</xsl:if>
		<!-- Volume -->
		<xsl:if test="$lVolume != ''">
			<xsl:value-of select="$lVolume" />
			<xsl:text>. </xsl:text>
		</xsl:if>

		<xsl:if test="normalize-space(./fields/*[@id='272']/value) != ''">
			<!-- Conference name -->
			<xsl:value-of select="./fields/*[@id='272']/value"></xsl:value-of>
		</xsl:if>
		<xsl:if test="normalize-space(./fields/*[@id='273']/value) != ''">
			<!-- Conference LOCATION -->
			<xsl:if test="normalize-space(./fields/*[@id='272']/value) != ''">
				<xsl:text>, </xsl:text>
			</xsl:if>
			<xsl:value-of select="./fields/*[@id='273']/value"></xsl:value-of>
		</xsl:if>
		<xsl:if test="normalize-space(./fields/*[@id='274']/value) != ''">
			<!-- Conference Date -->
			<xsl:if test="normalize-space(./fields/*[@id='272']/value) != ''">
				<xsl:text>, </xsl:text>
			</xsl:if>
			<xsl:value-of select="./fields/*[@id='274']/value"></xsl:value-of>
		</xsl:if>
		<!-- Ако има поне 1 от горните - трябва да сложим точка накрая -->
		<xsl:if test="normalize-space(./fields/*[@id='272']/value) != '' or normalize-space(./fields/*[@id='273']/value) != '' or normalize-space(./fields/*[@id='274']/value) != ''">
			<xsl:text>. </xsl:text>
		</xsl:if>
		<!-- Publisher -->
		<xsl:value-of select="./*[@object_id=104]/fields/*[@id='259']/value"></xsl:value-of>
		<xsl:text>, </xsl:text>
		<!-- City -->
		<xsl:value-of select="./*[@object_id=104]/fields/*[@id='260']/value"></xsl:value-of>
		<xsl:text>. </xsl:text>
		<!-- # of pages -->
		<xsl:value-of select="./fields/*[@id='261']/value"></xsl:value-of>
		<xsl:text> pp. </xsl:text>
		<xsl:if test="normalize-space(./fields/*[@id='262']/value) != ''">
			<!-- Publication language -->
			<xsl:text>[In </xsl:text>
			<xsl:value-of select="./fields/*[@id='262']/value"></xsl:value-of>
			<xsl:text>]</xsl:text>
		</xsl:if>
		<xsl:if test="normalize-space(./fields/*[@id='263']/value) != ''">
			<!-- URL -->
			<xsl:text> </xsl:text>
			<xsl:value-of select="./fields/*[@id='263']/value"></xsl:value-of>
		</xsl:if>
		<xsl:if test="normalize-space(./fields/*[@id='264']/value) != ''">
			<!-- ISBN -->
			<xsl:text> [ISBN </xsl:text>
			<xsl:value-of select="./fields/*[@id='264']/value"></xsl:value-of>
			<xsl:text>].</xsl:text>
		</xsl:if>
		<xsl:if test="normalize-space(./fields/*[@id='30']/value) != ''">
			<!-- DOI -->
			<xsl:text> DOI: </xsl:text>
			<xsl:value-of select="./fields/*[@id='30']/value"></xsl:value-of>
		</xsl:if>
	</xsl:template>

	<!-- Conference preceedings Base Preview
	 -->
	<xsl:template match="*[@object_id='105']" mode="previewBaseMode">
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

		<xsl:for-each select="./*[@object_id='92' or @object_id='100' or @object_id='101']/*[@object_id='90']">
			<xsl:choose>
				<xsl:when test="$lAuthorshipType = 3">
					<xsl:apply-templates select="." mode="processSingleReferenceAuthorFirstLast" />
				</xsl:when>
				<xsl:otherwise>
					<xsl:apply-templates select="." mode="processSingleAuthor" />
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
		<xsl:text> (</xsl:text><xsl:apply-templates select="." mode="processReferenceYear" /><xsl:text>) </xsl:text>

		<xsl:if test="normalize-space(./fields/*[@id='255']/value) != ''">
			<!-- Book title -->
			<xsl:text>[</xsl:text>
			<xsl:value-of select="php:function('parseReferenceItemTitle', string(./fields/*[@id='255']/value))"/>
			<xsl:text>] </xsl:text>
		</xsl:if>
		<!-- Volume -->
		<xsl:if test="$lVolume != ''">
			<xsl:value-of select="$lVolume" />
			<xsl:text>. </xsl:text>
		</xsl:if>

		<xsl:if test="normalize-space(./fields/*[@id='272']/value) != ''">
			<!-- Conference name -->
			<xsl:value-of select="./fields/*[@id='272']/value"></xsl:value-of>
		</xsl:if>
		<xsl:if test="normalize-space(./fields/*[@id='273']/value) != ''">
			<!-- Conference LOCATION -->
			<xsl:if test="normalize-space(./fields/*[@id='272']/value) != ''">
				<xsl:text>, </xsl:text>
			</xsl:if>
			<xsl:value-of select="./fields/*[@id='273']/value"></xsl:value-of>
		</xsl:if>
		<xsl:if test="normalize-space(./fields/*[@id='274']/value) != ''">
			<!-- Conference Date -->
			<xsl:if test="normalize-space(./fields/*[@id='272']/value) != ''">
				<xsl:text>, </xsl:text>
			</xsl:if>
			<xsl:value-of select="./fields/*[@id='274']/value"></xsl:value-of>
		</xsl:if>
		<!-- Ако има поне 1 от горните - трябва да сложим точка накрая -->
		<xsl:if test="normalize-space(./fields/*[@id='272']/value) != '' or normalize-space(./fields/*[@id='273']/value) != '' or normalize-space(./fields/*[@id='274']/value) != ''">
			<xsl:text>. </xsl:text>
		</xsl:if>
		<!-- Publisher -->
		<xsl:value-of select="./*[@object_id=104]/fields/*[@id='259']/value"></xsl:value-of>
		<xsl:text>, </xsl:text>
		<!-- City -->
		<xsl:value-of select="./*[@object_id=104]/fields/*[@id='260']/value"></xsl:value-of>
		<xsl:text>. </xsl:text>

		<xsl:if test="normalize-space(./*[@object_id=104]/fields/*[@id='243']/value) != ''">
			<!-- Journal -->
			<xsl:value-of select="./*[@object_id=104]/fields/*[@id='243']/value"></xsl:value-of>
			<xsl:text> </xsl:text>
		</xsl:if>
		<xsl:if test="normalize-space(./*[@object_id=104]/fields/*[@id='284']/value) != ''">
			<!-- VOLUME -->
			<xsl:value-of select="./*[@object_id=104]/fields/*[@id='284']/value"></xsl:value-of>
			<xsl:text>. </xsl:text>
		</xsl:if>

		<!-- # of pages -->
		<xsl:value-of select="./fields/*[@id='261']/value"></xsl:value-of>
		<xsl:text> pp. </xsl:text>
		<xsl:if test="normalize-space(./fields/*[@id='262']/value) != ''">
			<!-- Publication language -->
			<xsl:text>[In </xsl:text>
			<xsl:value-of select="./fields/*[@id='262']/value"></xsl:value-of>
			<xsl:text>]</xsl:text>
		</xsl:if>
		<xsl:if test="normalize-space(./fields/*[@id='263']/value) != ''">
			<!-- URL -->
			<xsl:text> </xsl:text>
			<xsl:value-of select="./fields/*[@id='263']/value"></xsl:value-of>
		</xsl:if>
		<xsl:if test="normalize-space(./fields/*[@id='264']/value) != ''">
			<!-- ISBN -->
			<xsl:text> [ISBN </xsl:text>
			<xsl:value-of select="./fields/*[@id='264']/value"></xsl:value-of>
			<xsl:text>].</xsl:text>
		</xsl:if>
		<xsl:if test="normalize-space(./fields/*[@id='30']/value) != ''">
			<!-- DOI -->
			<xsl:text> DOI: </xsl:text>
			<xsl:value-of select="./fields/*[@id='30']/value"></xsl:value-of>
		</xsl:if>
	</xsl:template>

	<!-- Thesis reference Base Preview
	 -->
	<xsl:template match="*[@object_id='106']" mode="previewBaseMode">
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

		<xsl:for-each select="./*[@object_id='92' or @object_id='100' or @object_id='101']/*[@object_id='90']">
			<xsl:choose>
				<xsl:when test="$lAuthorshipType = 3">
					<xsl:apply-templates select="." mode="processSingleReferenceAuthorFirstLast" />
				</xsl:when>
				<xsl:otherwise>
					<xsl:apply-templates select="." mode="processSingleAuthor" />
				</xsl:otherwise>
			</xsl:choose>
			<xsl:if test="position()!=last()"><xsl:text>, </xsl:text></xsl:if>
		</xsl:for-each>
		<!-- Year -->
		<xsl:text> (</xsl:text><xsl:apply-templates select="." mode="processReferenceYear" /><xsl:text>) </xsl:text>
		<!-- Book Title -->
		<xsl:value-of select="php:function('parseReferenceItemTitle', string(./fields/*[@id='255']/value))"/>
		<xsl:text> </xsl:text>
		<xsl:if test="normalize-space(./fields/*[@id='257']/value) != ''">
			<!-- Translated title -->
			<xsl:text>[</xsl:text>
			<xsl:value-of select="php:function('parseReferenceItemTitle', string(./fields/*[@id='257']/value))"/>
			<xsl:text>]</xsl:text>
		</xsl:if>
		<!-- Publisher -->
		<xsl:value-of select="./fields/*[@id='259']/value"></xsl:value-of>
		<xsl:text>, </xsl:text>
		<!-- City -->
		<xsl:value-of select="./fields/*[@id='260']/value"></xsl:value-of>
		<xsl:text>. </xsl:text>
		<!-- # of pages -->
		<xsl:value-of select="./fields/*[@id='261']/value"></xsl:value-of>
		<xsl:text> pp. </xsl:text>
		<xsl:if test="normalize-space(./fields/*[@id='262']/value) != ''">
			<!-- Publication language -->
			<xsl:text>[In </xsl:text>
			<xsl:value-of select="./fields/*[@id='262']/value"></xsl:value-of>
			<xsl:text>]</xsl:text>
		</xsl:if>
		<xsl:if test="normalize-space(./fields/*[@id='263']/value) != ''">
			<!-- URL -->
			<xsl:text> </xsl:text>
			<xsl:value-of select="./fields/*[@id='263']/value"></xsl:value-of>
		</xsl:if>
		<xsl:if test="normalize-space(./fields/*[@id='264']/value) != ''">
			<!-- ISBN -->
			<xsl:text> [ISBN </xsl:text>
			<xsl:value-of select="./fields/*[@id='264']/value"></xsl:value-of>
			<xsl:text>].</xsl:text>
		</xsl:if>
		<xsl:if test="normalize-space(./fields/*[@id='30']/value) != ''">
			<!-- DOI -->
			<xsl:text> DOI: </xsl:text>
			<xsl:value-of select="./fields/*[@id='30']/value"></xsl:value-of>
		</xsl:if>
	</xsl:template>

	<!-- Software reference Base Preview
	 -->
	<xsl:template match="*[@object_id='107']" mode="previewBaseMode">
		<xsl:for-each select="./*[@object_id='92' or @object_id='100' or @object_id='101']/*[@object_id='90']">
			<xsl:apply-templates select="." mode="processSingleAuthor" />
			<xsl:if test="position()!=last()"><xsl:text>, </xsl:text></xsl:if>
		</xsl:for-each>
		<!-- Year -->
		<xsl:text> (</xsl:text><xsl:apply-templates select="." mode="processReferenceYear" /><xsl:text>) </xsl:text>
		<!-- Title -->
		<xsl:value-of select="php:function('parseReferenceItemTitle', string(./fields/*[@id='26']/value))"/>
		<xsl:text> </xsl:text>
		<xsl:if test="normalize-space(./fields/*[@id='279']/value) != ''">
			<!-- Version -->
			<xsl:value-of select="./fields/*[@id='279']/value"/>
			<xsl:text>. </xsl:text>
		</xsl:if>
		<!-- Publisher -->
		<xsl:value-of select="./fields/*[@id='259']/value"></xsl:value-of>
		<xsl:text>. </xsl:text>
		<xsl:if test="normalize-space(./fields/*[@id='285']/value) != ''">
			<!-- Release date -->
			<xsl:text>Release date: </xsl:text>
			<xsl:value-of select="./fields/*[@id='285']/value"/>
			<xsl:text>.</xsl:text>
		</xsl:if>
		<xsl:if test="normalize-space(./fields/*[@id='263']/value) != ''">
			<!-- URL -->
			<xsl:text> </xsl:text>
			<xsl:value-of select="./fields/*[@id='263']/value"></xsl:value-of>
		</xsl:if>
	</xsl:template>

	<!-- Website reference Base Preview
	 -->
	<xsl:template match="*[@object_id='108']" mode="previewBaseMode">
		<!-- Title -->
		<xsl:value-of select="php:function('parseReferenceItemTitle', string(./fields/*[@id='26']/value))"/>
		<xsl:text> </xsl:text>
		<xsl:if test="normalize-space(./fields/*[@id='263']/value) != ''">
			<!-- URL -->
			<xsl:text> </xsl:text>
			<xsl:value-of select="./fields/*[@id='263']/value"></xsl:value-of>
		</xsl:if>
		<xsl:if test="normalize-space(./fields/*[@id='280']/value) != ''">
			<!-- Access date -->
			<xsl:text> Accession date: </xsl:text>
			<xsl:value-of select="./fields/*[@id='280']/value"/>
			<xsl:text>.</xsl:text>
		</xsl:if>

	</xsl:template>

	<!-- Общ темплейт за всички референции за цитиране от тип 1 без website (понеже нямат автор)-->
	<xsl:template match="*[@object_id='98' or @object_id='99' or @object_id='102' or @object_id='103' or @object_id='105' or @object_id='106' or @object_id='107']" mode="previewMode1">
		<xsl:variable name="lAuthors" select="./*[@object_id='92' or @object_id='100' or @object_id='101']/*[@object_id='90']"></xsl:variable>
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
		<xsl:choose>
			<!-- 3+ Автора
			2. ако статията има 3 или повече автори - се изписва 'фамилното
			име/институтионал автор' на Автор 1 и се добавя 'et al.' след него
			ПРИМЕР: Sheng et al. 2012
			-->
			<xsl:when test="count($lAuthors) &gt; 2">
				<!-- Last name -->
				<xsl:choose>
					<xsl:when test="$lAuthorshipType = 3">
						<xsl:value-of select="$lAuthors[1]/fields/*[@id='250']/value"></xsl:value-of>
					</xsl:when>
					<xsl:otherwise>
						<xsl:value-of select="$lAuthors[1]/fields/*[@id='252']/value"></xsl:value-of>
					</xsl:otherwise>
				</xsl:choose>
				<xsl:text> et al.</xsl:text>
			</xsl:when>
			<!-- 2 Автора
			2. ако статията има 2 автори - се изписва 'фамилните
			имена/институтионалните автори' и между тях се добавя 'and'
			ПРИМЕР: Sheng and Broad 2011
			-->
			<xsl:when test="count($lAuthors) &gt; 1">
				<!-- Last name -->
				<xsl:choose>
					<xsl:when test="$lAuthorshipType = 3">
						<xsl:value-of select="$lAuthors[1]/fields/*[@id='250']/value"></xsl:value-of>
					</xsl:when>
					<xsl:otherwise>
						<xsl:value-of select="$lAuthors[1]/fields/*[@id='252']/value"></xsl:value-of>
					</xsl:otherwise>
				</xsl:choose>
				<xsl:text> and </xsl:text>
				<xsl:choose>
					<xsl:when test="$lAuthorshipType = 3">
						<xsl:value-of select="$lAuthors[2]/fields/*[@id='250']/value"></xsl:value-of>
					</xsl:when>
					<xsl:otherwise>
						<xsl:value-of select="$lAuthors[2]/fields/*[@id='252']/value"></xsl:value-of>
					</xsl:otherwise>
				</xsl:choose>
			</xsl:when>
			<!-- 1 Автор
			1. ако статията има 1 автор - се изписва 'фамилното име/институтионал
			автор'
			ПРИМЕР: Penev 2004
			-->
			<xsl:when test="count($lAuthors) &gt; 0">
				<xsl:for-each select="./*[@object_id='92' or @object_id='100' or @object_id='101']/*[@object_id='90']">
					<xsl:choose>
						<xsl:when test="$lAuthorshipType = 3">
							<xsl:value-of select="$lAuthors[1]/fields/*[@id='250']/value"></xsl:value-of>
						</xsl:when>
						<xsl:otherwise>
							<!-- Last name -->
							<xsl:value-of select="$lAuthors[1]/fields/*[@id='252']/value"></xsl:value-of>
						</xsl:otherwise>
					</xsl:choose>
				</xsl:for-each>
			</xsl:when>
		</xsl:choose>
		<xsl:text> </xsl:text>
		<xsl:apply-templates select="." mode="processReferenceYear" />
	</xsl:template>

	<!-- Website citation citation mode1 -->
	<xsl:template match="*[@object_id='108']" mode="previewMode1">
		<xsl:value-of select="./fields/*[@id='263']/value"/>
	</xsl:template>

	<!-- Общ темплейт за всички референции за цитиране от тип 2 без website (понеже нямат автор)-->
	<xsl:template match="*[@object_id='98' or @object_id='99' or @object_id='102' or @object_id='103' or @object_id='105' or @object_id='106' or @object_id='107']" mode="previewMode2">
		<xsl:variable name="lAuthors" select="./*[@object_id='92' or @object_id='100' or @object_id='101']/*[@object_id='90']"></xsl:variable>
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
		<xsl:choose>
			<!-- 3+ Автора
			2. ако статията има 3 или повече автори - се изписва 'фамилното
			име/институтионал автор' на Автор 1 и се добавя 'et al.' след него
			ПРИМЕР: Sheng et al. 2012
			-->
			<xsl:when test="count($lAuthors) &gt; 2">
				<!-- Last name -->
				<xsl:choose>
					<xsl:when test="$lAuthorshipType = 3">
						<xsl:value-of select="$lAuthors[1]/fields/*[@id='250']/value"></xsl:value-of>
					</xsl:when>
					<xsl:otherwise>
						<xsl:value-of select="$lAuthors[1]/fields/*[@id='252']/value"></xsl:value-of>
					</xsl:otherwise>
				</xsl:choose>
				<xsl:text> et al.</xsl:text>
			</xsl:when>
			<!-- 2 Автора
			2. ако статията има 2 автори - се изписва 'фамилните
			имена/институтионалните автори' и между тях се добавя 'and'
			ПРИМЕР: Sheng and Broad 2011
			-->
			<xsl:when test="count($lAuthors) &gt; 1">
				<!-- Last name -->
				<xsl:choose>
					<xsl:when test="$lAuthorshipType = 3">
						<xsl:value-of select="$lAuthors[1]/fields/*[@id='250']/value"></xsl:value-of>
					</xsl:when>
					<xsl:otherwise>
						<xsl:value-of select="$lAuthors[1]/fields/*[@id='252']/value"></xsl:value-of>
					</xsl:otherwise>
				</xsl:choose>
				<xsl:text> and </xsl:text>
				<xsl:choose>
					<xsl:when test="$lAuthorshipType = 3">
						<xsl:value-of select="$lAuthors[2]/fields/*[@id='250']/value"></xsl:value-of>
					</xsl:when>
					<xsl:otherwise>
						<xsl:value-of select="$lAuthors[2]/fields/*[@id='252']/value"></xsl:value-of>
					</xsl:otherwise>
				</xsl:choose>
			</xsl:when>
			<!-- 1 Автор
			1. ако статията има 1 автор - се изписва 'фамилното име/институтионал
			автор'
			ПРИМЕР: Penev 2004
			-->
			<xsl:when test="count($lAuthors) &gt; 0">
				<!-- Last name -->
				<xsl:choose>
					<xsl:when test="$lAuthorshipType = 3">
						<xsl:value-of select="$lAuthors[1]/fields/*[@id='250']/value"></xsl:value-of>
					</xsl:when>
					<xsl:otherwise>
						<!-- Last name -->
						<xsl:value-of select="$lAuthors[1]/fields/*[@id='252']/value"></xsl:value-of>
					</xsl:otherwise>
				</xsl:choose>
			</xsl:when>
		</xsl:choose>
		<xsl:text> (</xsl:text>
		<xsl:apply-templates select="." mode="processReferenceYear" />
		<xsl:text>)</xsl:text>
	</xsl:template>
	<!-- Website citation citation mode2 -->
	<xsl:template match="*[@object_id='108']" mode="previewMode2">
		<xsl:value-of select="./fields/*[@id='263']/value"/>
	</xsl:template>


</xsl:stylesheet>