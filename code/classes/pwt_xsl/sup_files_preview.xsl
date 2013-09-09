<?xml version='1.0'?>
<xsl:stylesheet version="1.0"
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:xlink="http://www.w3.org/1999/xlink"
	xmlns:tp="http://www.plazi.org/taxpub" xmlns:php="http://php.net/xsl"
	exclude-result-prefixes="php tp xlink xsl">
	<xsl:output method="xml" encoding="UTF-8" indent="yes"
		omit-xml-declaration="no">
	</xsl:output>

	<xsl:template match="/document">

		<xsl:for-each select="/document/objects//*[@object_id='55']">
			<xsl:variable name="lItemId" select="./@instance_id"></xsl:variable>
			<div>
				<xsl:attribute name="id">
					<xsl:text>Sup-File-Preview-Wrapper</xsl:text>
					<xsl:value-of select="$lItemId"></xsl:value-of>
				</xsl:attribute>
				<xsl:apply-templates select="." mode="previewBaseMode"></xsl:apply-templates>
			</div>
		</xsl:for-each>
	</xsl:template>

	<xsl:template match="*[@object_id='55']" mode="previewBaseMode">
		<span>
			<xsl:attribute name="field_id">214</xsl:attribute>
			<xsl:apply-templates select="./fields/*[@id='214']" mode="formatting"/>
		</span>
		<xsl:text> </xsl:text>
		<xsl:variable name="lUrls" select="php:function('getAllUrlsFromText', string(./fields/*[@id='222']))"></xsl:variable>
		<xsl:for-each select="$lUrls/url">
			<xsl:variable name="lLinkContent" select="."></xsl:variable>
			<a>
				<xsl:attribute name="href"><xsl:value-of select="$lLinkContent" disable-output-escaping="no"/></xsl:attribute>
				<xsl:value-of select="$lLinkContent" disable-output-escaping="no"/>
			</a>
			<xsl:if test="position()!=last()"><xsl:text>, </xsl:text></xsl:if>
		</xsl:for-each>
	</xsl:template>




</xsl:stylesheet>