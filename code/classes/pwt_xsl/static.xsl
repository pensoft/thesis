<?xml version="1.0"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:tp="http://www.plazi.org/taxpub" xmlns:php="http://php.net/xsl" exclude-result-prefixes="php tp xlink xsl">
	<xsl:template name="To-Make-Comments-Hint">
		<xsl:choose>
			<xsl:when test="$pShowPreviewCommentTip &gt; 0">	
				<div class="P-Article-Preview-Hint">
					<img src="/i/lightbulb.png" alt="Tip" width="24" height="24" style="float: left; margin: -4px 2px 0 0" />
					<xsl:text> Select any text to comment on it.</xsl:text>
				</div>
			</xsl:when>
		</xsl:choose>
	</xsl:template>
</xsl:stylesheet>
