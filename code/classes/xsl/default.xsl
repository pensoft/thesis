<?xml version='1.0'?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:xlink="http://www.w3.org/1999/xlink">
	<xsl:variable name="gSiteUrl">http://pmt.pensoft.eu</xsl:variable>
	<xsl:variable name="gGoogleMapsUrl">http://ptp.pensoft.eu/redirect_to_googlemap.php</xsl:variable>
	<!-- Функция която връща lowerCase репрезентация на подадения стринг -->
	 <xsl:template name="ToLower">
                <xsl:param name="inputString"/>
                <xsl:variable name="smallCase" select="'abcdefghijklmnopqrstuvwxyz'"/>

                <xsl:variable name="upperCase" select="'ABCDEFGHIJKLMNOPQRSTUVWXYZ'"/>

                <xsl:value-of select="translate($inputString,$upperCase,$smallCase)"/>
        </xsl:template>
	
	<!-- Функция която връща upperCase репрезентация на подадения стринг -->
        <xsl:template name="ToUpper">
                <xsl:param name="inputString"/>
                <xsl:variable name="smallCase" select="'abcdefghijklmnopqrstuvwxyz'"/>

                <xsl:variable name="upperCase" select="'ABCDEFGHIJKLMNOPQRSTUVWXYZ'"/>

                <xsl:value-of select="translate($inputString,$smallCase,$upperCase)"/>

        </xsl:template>
	
	<!-- Функция която връща репрезентация на подадения стринг в която са премахнати интервалите-->
	<xsl:template name="RemoveSpaces">
                <xsl:param name="inputString"/>
                <xsl:variable name="inputStr" select="' '"/>

                <xsl:variable name="replacementStr" select="''"/>

                <xsl:value-of select="translate($inputString,$inputStr,$replacementStr)"/>

        </xsl:template>
	
	<!-- Функция която връща репрезентация на подадения стринг, в която searchSymbol е заменен с replacementSymbol -->
	
	<xsl:template name="replaceSymbolTemplate">
		<xsl:param name="text" select="."/>
		<xsl:param name="searchSymbol"/>
		<xsl:param name="replacementSymbol"/>
		<xsl:choose>
			<xsl:when test="contains($text, $searchSymbol)">
				<xsl:value-of select="substring-before($text, $searchSymbol)"/><xsl:value-of select="$replacementSymbol"/>
				<xsl:call-template name="replaceSymbolTemplate">
					  <xsl:with-param name="text" select="substring-after($text, $searchSymbol)"/>
					  <xsl:with-param name="searchSymbol" select="$searchSymbol"/>
					  <xsl:with-param name="replacementSymbol" select="$replacementSymbol"/>
				</xsl:call-template>
			</xsl:when>			
			<xsl:otherwise>
				<xsl:value-of select="$text"/>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>

</xsl:stylesheet>