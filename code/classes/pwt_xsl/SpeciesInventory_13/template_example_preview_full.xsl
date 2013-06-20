<?xml version='1.0'?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:tp="http://www.plazi.org/taxpub"  xmlns:php="http://php.net/xsl" exclude-result-prefixes="php tp xlink xsl">	
	<xsl:import href="../static2.xsl"/>
	
	<xsl:import href="./template_example_preview_base.xsl"/>
	<xsl:import href="../taxon.xsl"/>

	<xsl:import href="../common_reference_preview.xsl"/>
	<xsl:output method="xml" omit-xml-declaration="yes" encoding="UTF-8" indent="yes" doctype-public="-//W3C//DTD XHTML 1.0 Transitional//EN" doctype-system="http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd" >		
	</xsl:output>
</xsl:stylesheet>