<?xml version='1.0'?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:tp="http://www.plazi.org/taxpub"  xmlns:php="http://php.net/xsl" exclude-result-prefixes="php tp xlink xsl">
	
	<!-- Custom Single Author Template -->
	<xsl:template match="*" mode="singleAuthorCustom">
		<xsl:attribute name="instance_id"><xsl:value-of select="./@instance_id" /></xsl:attribute>
		
		<!-- Author name -->
		<b>
			<span field_id="6">
				<xsl:value-of select="./fields/*[@id=6]"/>
			</span>
			<xsl:text> </xsl:text>
			<xsl:if test="count(./fields/*[@id=7]) &gt; 0">
				<span field_id="7">
					<xsl:value-of select="./fields/*[@id=7]"/>
				</span>
				<xsl:text> </xsl:text>
			</xsl:if>
			<span field_id="8">
				<xsl:value-of select="./fields/*[@id=8]"/>
			</span>
		</b>
		
		<!-- Author email -->
		<xsl:text> (</xsl:text>
		<a field_id="4">
			<xsl:attribute name="href"><xsl:text>mailto:</xsl:text><xsl:apply-templates select="./fields/*[@id=4]" mode="formatting_nospace"/></xsl:attribute>		
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
		
		<!-- Contributor name -->
		<b>
			<span field_id="6">
				<xsl:value-of select="./fields/*[@id=6]"/>
			</span>
			<xsl:text> </xsl:text>
			<xsl:if test="count(./fields/*[@id=7]) &gt; 0">
				<span field_id="7">
					<xsl:value-of select="./fields/*[@id=7]"/>
				</span>
				<xsl:text> </xsl:text>
			</xsl:if>
			<span field_id="8">
				<xsl:value-of select="./fields/*[@id=8]"/>
			</span>
		</b>
		
		<!-- Contributor email -->
		<xsl:text> (</xsl:text>
		<a field_id="4">
			<xsl:attribute name="href"><xsl:text>mailto:</xsl:text><xsl:apply-templates select="./fields/*[@id=4]" mode="formatting_nospace"/></xsl:attribute>		
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
		
		<!-- Contributor address -->
		<xsl:for-each select="./*[@object_id='5']" >
			<xsl:variable name="lCurrentNode" select="." />
			<span class="P-Current-Author-Single-Address">
				<xsl:apply-templates select="." mode="singleAuthorAddressCustom"/>
			</span>
		</xsl:for-each>
		
		<!-- Contributor rights -->
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
	
	<!-- Single reference
	 -->
	<xsl:template match="*[@object_id='95']" mode="SingleReferenceCustom">
		<ul>
			<xsl:apply-templates select="./*[@object_id='97']/*[@object_id &gt; 0]" mode="articleBack"/>
		</ul>
	</xsl:template>
</xsl:stylesheet>