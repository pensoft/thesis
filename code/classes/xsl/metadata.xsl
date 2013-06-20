<?xml version='1.0'?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:tp="http://www.plazi.org/taxpub"  xmlns:php="http://php.net/xsl"> 
	<xsl:output method="xml" encoding="UTF-8"/>
	
	<xsl:variable name="lFakeContribTag">fake_contribs</xsl:variable>
	<xsl:variable name="lFakeContribNameTag">fake_contrib_name</xsl:variable>
	<xsl:variable name="lFakeContribUriAffTag">fake_contrib_uriaff</xsl:variable>
	<xsl:variable name="lFakeAffWrapperTag">fake_aff</xsl:variable>
	<xsl:variable name="lFakeUriWrapperTag">fake_uri</xsl:variable>
	<xsl:variable name="lFakeSingleAffTag">aff</xsl:variable>
	<xsl:variable name="lFakeSingleAffNumTag">aff_num</xsl:variable>
	<xsl:variable name="lFakeSingleUriTag">uri</xsl:variable>
	<xsl:variable name="lFakeSingleUriSymTag">uri_sym</xsl:variable>
	<xsl:variable name="lFakeAcademicEditorsTag">fake_academic_editors</xsl:variable>
	<xsl:variable name="lFakeAcademicEditorContribTag">fake_academic_editor_contrib</xsl:variable>
	<xsl:variable name="lFakeAcademicEditorDateTag">fake_academic_editor_date</xsl:variable>
	<xsl:variable name="lFakeAcademicEditorDateTypeTag">fake_academic_editor_date_type</xsl:variable>
	<xsl:variable name="lFakeAcademicEditorPubdateTag">fake_academic_editor_pubdate</xsl:variable>
	
	<xsl:template match="/">		
		<xsl:call-template name="singleNodeTpl">
			<xsl:with-param name="pNode" select="/"></xsl:with-param>			
		</xsl:call-template>	
	</xsl:template>
	
	<xsl:template name="singleNodeTpl">
		<xsl:param name="pNode"></xsl:param>		
		<xsl:variable name="lIsTextType" select="$pNode/self::text()" />
		<xsl:variable name="lIsElementType" select="$pNode/self::*" />
		<xsl:variable name="lIsRootElement" select="count($pNode|/)=1" />
		<xsl:variable name="lIsCommentType" select="$pNode/self::comment()" />
		<xsl:variable name="lIsPIType" select="$pNode/self::processing-instruction()" />
		<xsl:variable name="lIsAttributeType" select="count($pNode|../@*)=count(../@*)" />
		<xsl:variable name="lIsNamespaceType" select="count($pNode|../namespace::*)=count(../namespace::*)" />		
		<xsl:choose>
			<xsl:when test="$lIsRootElement">							
				<xsl:call-template name="singleNodeChildTpl">
					<xsl:with-param name="pNode" select="$pNode"></xsl:with-param>
				</xsl:call-template>				
			</xsl:when>			
			<xsl:when test="($lIsTextType) or ($lIsCommentType) or ($lIsPIType) or ($lIsAttributeType) or ($lIsNamespaceType)">
				<xsl:copy-of select="$pNode"></xsl:copy-of>
			</xsl:when>
			<xsl:when test="$lIsElementType">
				<xsl:variable name="lLocalName" select="local-name($pNode)"></xsl:variable>
				<xsl:choose>
					<xsl:when test="($lLocalName=$lFakeContribTag)">
						<xsl:call-template name="parseFakeData">
							<xsl:with-param name="pMetaNode" select="$pNode/parent::*"></xsl:with-param>
						</xsl:call-template>
					</xsl:when>
					<xsl:when test="($lLocalName=$lFakeAffWrapperTag) or ($lLocalName=$lFakeUriWrapperTag) or ($lLocalName=$lFakeAcademicEditorsTag)"></xsl:when>
					<xsl:otherwise>
						<xsl:element name="{local-name($pNode)}" namespace="{namespace-uri($pNode)}">
							<xsl:call-template name="singleNodeChildTpl">
								<xsl:with-param name="pNode" select="$pNode"></xsl:with-param>
							</xsl:call-template>
						</xsl:element>
					</xsl:otherwise>
				</xsl:choose>				
			</xsl:when>			
		</xsl:choose>
	</xsl:template>
	
	<!-- 
		Темплейт, който вика темплейта singleNodeTpl за всички деца на подадения node
	-->
	<xsl:template name="singleNodeChildTpl">
		<xsl:param name="pNode" />		
		<xsl:for-each select="$pNode/@*|$pNode/namespace::*|$pNode/child::*|$pNode/child::text()|$pNode/child::comment()|$pNode/child::processing-instruction()" >
			<xsl:variable name="lCurrentNode" select="." />
			<xsl:call-template name="singleNodeTpl">	
				<xsl:with-param name="pNode" select="$lCurrentNode"></xsl:with-param>				
			</xsl:call-template>
		</xsl:for-each>
	</xsl:template>
	
	<!-- 
		Темплейт, който строи контрибуторите и датите на публикуване / приемане ...
	-->
	<xsl:template name="parseFakeData">
		<xsl:param name="pMetaNode" />	
		<xsl:variable name="lFakeContribs" select="$pMetaNode//*[local-name()=$lFakeContribTag]//*[local-name()=$lFakeContribNameTag]"></xsl:variable>
		<xsl:variable name="lFakeAffs" select="$pMetaNode//*[local-name()=$lFakeAffWrapperTag]"></xsl:variable>
		<xsl:variable name="lFakeUris" select="$pMetaNode//*[local-name()=$lFakeUriWrapperTag]"></xsl:variable>
		<xsl:variable name="lFakeAcademicEditors" select="$pMetaNode//*[local-name()=$lFakeAcademicEditorsTag]"></xsl:variable>
		<xsl:variable name="lFakeAcademicEditorContributors" select="$lFakeAcademicEditors//*[local-name()=$lFakeAcademicEditorContribTag]"></xsl:variable>
		<xsl:variable name="lFakeAcademicEditorDates" select="$lFakeAcademicEditors//*[local-name()=$lFakeAcademicEditorDateTag]"></xsl:variable>
		<xsl:variable name="lPubdate" select="$lFakeAcademicEditors//*[local-name()=$lFakeAcademicEditorPubdateTag]"></xsl:variable>
		<xsl:if test="(count($lFakeContribs) &gt; 0) or (count($lFakeAcademicEditorContributors) &gt; 0)">
			<contrib-group>
				<xsl:for-each select="$lFakeContribs">
					<xsl:variable name="lCurrentContribName" select="."></xsl:variable>
					<contrib contrib-type="author">
						<xsl:call-template name="contribNameTpl">
							<xsl:with-param name="pContribName" select="$lCurrentContribName"></xsl:with-param>
						</xsl:call-template>
						<xsl:call-template name="parseContribUriAffTpl">
							<xsl:with-param name="pUriAffTag" select="$lCurrentContribName/following-sibling::*[local-name()=$lFakeContribUriAffTag][1]"></xsl:with-param>
							<xsl:with-param name="pAffs" select="$lFakeAffs"></xsl:with-param>
							<xsl:with-param name="pUris" select="$lFakeUris"></xsl:with-param>
						</xsl:call-template>						
					</contrib>					
				</xsl:for-each>
				<xsl:for-each select="$lFakeAcademicEditorContributors">
					<xsl:variable name="lCurrentContribName" select="."></xsl:variable>
					<contrib contrib-type="academic-editor">
						<xsl:call-template name="contribNameTpl">
							<xsl:with-param name="pContribName" select="$lCurrentContribName"></xsl:with-param>
						</xsl:call-template>
					</contrib>					
				</xsl:for-each>
			</contrib-group>
			<xsl:call-template name="dateTpl">
				<xsl:with-param name="pDate" select="$lPubdate"></xsl:with-param>
				<xsl:with-param name="pElementName">pub-date</xsl:with-param>
			</xsl:call-template>
			<xsl:if test="$lFakeAcademicEditorDates">
				<history>
					<xsl:for-each select="$lFakeAcademicEditorDates">
						<xsl:variable name="lCurrentDate" select="."></xsl:variable>
						<xsl:variable name="lDateType" select="string($lCurrentDate/preceding-sibling::*[local-name()=$lFakeAcademicEditorDateTypeTag][1])"></xsl:variable>
						<xsl:call-template name="dateTpl">
							<xsl:with-param name="pDate" select="$lCurrentDate"></xsl:with-param>
							<xsl:with-param name="pElementName">date</xsl:with-param>
							<xsl:with-param name="pDateType" select="$lDateType"></xsl:with-param>
						</xsl:call-template>
					</xsl:for-each>
				</history>
			</xsl:if>
		</xsl:if>
		
	</xsl:template>
	
	<!-- 
		Темплейт, който строи таг с името на контрибутора
	-->
	<xsl:template name="contribNameTpl">
		<xsl:param name="pContribName" />
		<name>
			<xsl:variable name="lSurname" select="php:function('getContribSurname', string($pContribName))"></xsl:variable>
			<xsl:variable name="lGivenNames" select="php:function('getContribGivenNames', string($pContribName))"></xsl:variable>
			<xsl:if test="$lSurname!=''">
				<surname><xsl:value-of select="$lSurname"></xsl:value-of></surname>
			</xsl:if>
			<xsl:if test="$lGivenNames!=''">
				<given-names><xsl:value-of select="$lGivenNames"></xsl:value-of></given-names>
			</xsl:if>
		</name>
	</xsl:template>
	
	<!-- 
		Темплейт, който намира aff-то и uri-то на контрибутора
	-->
	<xsl:template name="parseContribUriAffTpl">
		<xsl:param name="pUriAffTag"></xsl:param>
		<xsl:param name="pAffs"></xsl:param>
		<xsl:param name="pUris"></xsl:param>
		<xsl:if test="count($pUriAffTag) &gt; 0">
			<xsl:variable name="lAffNum" select="php:function('getContribAffNum', string($pUriAffTag))"></xsl:variable>
			<xsl:variable name="lUriSym" select="php:function('getContribUriSym', string($pUriAffTag))"></xsl:variable>
			<xsl:call-template name="getContribAffTpl">				
				<xsl:with-param name="pAffs" select="$pAffs"></xsl:with-param>
				<xsl:with-param name="pAffNum" select="$lAffNum"></xsl:with-param>
			</xsl:call-template>
			<xsl:call-template name="getContribUriTpl">				
				<xsl:with-param name="pUris" select="$pUris"></xsl:with-param>
				<xsl:with-param name="pUriSym" select="$lUriSym"></xsl:with-param>
			</xsl:call-template>			
		</xsl:if>
	</xsl:template>
	
	<!-- 
		Темплейт, който намира aff-то на контрибутора
	-->
	<xsl:template name="getContribAffTpl">		
		<xsl:param name="pAffs"></xsl:param>
		<xsl:param name="pAffNum"></xsl:param>		
		<xsl:if test="(count($pAffs) &gt; 0) and ($pAffNum!='')">
			<xsl:for-each select="$pAffs/*[local-name()=$lFakeSingleAffNumTag]">
				<xsl:variable name="lCurrentAffNum" select="."></xsl:variable>
				<xsl:variable name="lCurrentAffNumValue" select="php:function('trim', string($lCurrentAffNum))"></xsl:variable>
				<xsl:if test="$lCurrentAffNumValue=$pAffNum">
					<xsl:variable name="lAffValueNode" select="$lCurrentAffNum/following-sibling::*[local-name()=$lFakeSingleAffTag][1]"></xsl:variable>
					<xsl:if test="count($lAffValueNode) &gt; 0">
						<aff>
							<label><xsl:value-of select="$pAffNum"></xsl:value-of></label>
							<xsl:value-of select="$lAffValueNode"></xsl:value-of>
						</aff>
					</xsl:if>
				</xsl:if>
			</xsl:for-each>
		</xsl:if>
	</xsl:template>
	
	<!-- 
		Темплейт, който намира uri-то на контрибутора
	-->
	<xsl:template name="getContribUriTpl">		
		<xsl:param name="pUris"></xsl:param>
		<xsl:param name="pUriSym"></xsl:param>		
		<xsl:if test="(count($pUris) &gt; 0) and ($pUriSym!='')">
			<xsl:for-each select="$pUris/*[local-name()=$lFakeSingleUriSymTag]">
				<xsl:variable name="lCurrentUriSym" select="."></xsl:variable>
				<xsl:variable name="lCurrentUriSymValue" select="php:function('trim', string($lCurrentUriSym))"></xsl:variable>				
				<xsl:if test="$lCurrentUriSymValue=$pUriSym">					
					<xsl:variable name="lUriValueNode" select="$lCurrentUriSym/following-sibling::*[local-name()=$lFakeSingleUriTag][1]"></xsl:variable>
					<xsl:if test="count($lUriValueNode) &gt; 0">
						<uri>
							<xsl:attribute name="xlink:title"><xsl:value-of select="$pUriSym"></xsl:value-of></xsl:attribute>
							<xsl:value-of select="$lUriValueNode"></xsl:value-of>
						</uri>
					</xsl:if>
				</xsl:if>
			</xsl:for-each>
		</xsl:if>
	</xsl:template>
		
	
	<!-- 
		Темплейт, който показва дата
	-->
	<xsl:template name="dateTpl">		
		<xsl:param name="pDate"></xsl:param>		
		<xsl:param name="pElementName">date</xsl:param>
		<xsl:param name="pDateType"></xsl:param>			
		<xsl:if test="count($pDate)=1">
			<xsl:variable name="lDay" select="php:function('getDateDay', string($pDate))"></xsl:variable>
			<xsl:variable name="lMonth" select="php:function('getDateMonth', string($pDate))"></xsl:variable>
			<xsl:variable name="lYear" select="php:function('getDateYear', string($pDate))"></xsl:variable>			
			<xsl:if test="($lDay!='') and ($lMonth!='') and ($lYear!='')">
				<xsl:element name="{$pElementName}">
					<xsl:if test="$pDateType!=''">
						<xsl:attribute name="date-type">
							<xsl:value-of select="php:function('strtolower', string($pDateType))"></xsl:value-of>
						</xsl:attribute>
					</xsl:if>
					<day><xsl:value-of select="$lDay"></xsl:value-of></day>
					<month><xsl:value-of select="$lMonth"></xsl:value-of></month>
					<year><xsl:value-of select="$lYear"></xsl:value-of></year>
				</xsl:element>
			</xsl:if>			
		</xsl:if>
	</xsl:template>

</xsl:stylesheet>