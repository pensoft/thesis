<?xml version='1.0'?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:tp="http://www.plazi.org/taxpub">
	<xsl:import href="./html.xsl"/>
	<xsl:output method="html" encoding="UTF-8"/>

	<xsl:template match="/">
		<xsl:call-template name="common_main">	
			<xsl:with-param name="css_filename"><xsl:value-of select="$gSiteUrl"/>/lib/xsl.css</xsl:with-param>
			<xsl:with-param name="additional_css_filename"><xsl:value-of select="$gSiteUrl"/>/lib/xsl_html_old.css</xsl:with-param>			
			<xsl:with-param name="js_use_window_max">1</xsl:with-param>
		</xsl:call-template>	
	</xsl:template>
	
</xsl:stylesheet>