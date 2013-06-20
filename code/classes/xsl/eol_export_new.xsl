<?xml version='1.0'?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:dcterms="http://purl.org/dc/terms/" xmlns:geo="http://www.w3.org/2003/01/geo/wgs84_pos#" xmlns:dwc="http://rs.tdwg.org/dwc/dwcore/" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.eol.org/transfer/content/0.3 http://services.eol.org/schema/content_0_3.xsd" xmlns:tp="http://www.plazi.org/taxpub" xmlns:php="http://php.net/xsl">
	<xsl:import href="./default.xsl"/>
	<xsl:output method="xml" encoding="UTF-8"/>
	<xsl:variable name="gPicUrl">http://www.pensoft.net/J_FILES/{journal_id}/articles/{article_id}/export.php_files/{file_name}</xsl:variable>
	<xsl:variable name="gDefaultNsUri">http://www.eol.org/transfer/content/0.3</xsl:variable>

	<xsl:template match="/">
		<response xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:dcterms="http://purl.org/dc/terms/" xmlns:geo="http://www.w3.org/2003/01/geo/wgs84_pos#" xmlns:dwc="http://rs.tdwg.org/dwc/dwcore/" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">
			<xsl:attribute name="xmlns"><xsl:value-of select="$gDefaultNsUri"></xsl:value-of></xsl:attribute>
			<xsl:call-template name="parseArticles">
			</xsl:call-template>
		</response>
	</xsl:template>

	<!--
		Обработваме статиите една по една
	-->
	<xsl:template name="parseArticles">
		<xsl:variable name="lArticles" select="//article" />
		<xsl:for-each select="$lArticles" >
			<xsl:variable name="lCurrentArticle" select="." />

			<xsl:call-template name="parseTaxons">
				<xsl:with-param name="pCurrentArticle" select="$lCurrentArticle"></xsl:with-param>
			</xsl:call-template>
		</xsl:for-each>
	</xsl:template>

	<!--
		За всяка от статиите обхождаме всички taxon-treatment-и
	-->
	<xsl:template name="parseTaxons">
		<xsl:param name="pCurrentArticle" />

		<xsl:variable name="lTaxons" select="$pCurrentArticle/body//tp:taxon-treatment[tp:nomenclature]" />
		<!--<xsl:variable name="lTaxons" select="$pCurrentArticle/body//tp:taxon-treatment[tp:nomenclature[tp:taxon-status]]" />-->
		<xsl:variable name="lArticleDoi" select="$pCurrentArticle/front/article-meta/article-id[@pub-id-type='doi']" />
		<xsl:variable name="lArticleId"><xsl:value-of select="php:function('getArticleIdFromDoi',  string($lArticleDoi))"></xsl:value-of></xsl:variable>
		<xsl:variable name="lJournalId" select="php:function('getArticleJournalId',  string($lArticleId))" />
		<xsl:variable name="lArticleAuthors" select="$pCurrentArticle/front/article-meta/contrib-group/contrib[@contrib-type='author']/name"></xsl:variable>
		<xsl:variable name="lArticleLink" select="php:function('getArticleAbstractLink',  string($lArticleId))" />
		<xsl:for-each select="$lTaxons" >
			<xsl:variable name="lCurrentTaxon" select="." />
			<xsl:variable name="lTaxonStatusNode" select="$lCurrentTaxon/tp:nomenclature/tp:taxon-status" />
			<xsl:variable name="lTaxonStatus">
				<xsl:variable name="lNormalizedStatus" select="normalize-space($lTaxonStatusNode)" />
				<xsl:call-template name="ToLower">
					<xsl:with-param name="inputString" select="$lNormalizedStatus"/>
				</xsl:call-template>
			</xsl:variable>
			<!--<xsl:if test="$lTaxonStatus='sp. n.' or $lTaxonStatus='sp. nov.' or $lTaxonStatus='gen. n.' or $lTaxonStatus='gen. nov.'">-->
				<xsl:call-template name="singleTaxonTemplate">
					<xsl:with-param name="pTaxonNode" select="$lCurrentTaxon"></xsl:with-param>
					<xsl:with-param name="pArticleReference">
						<xsl:call-template name="getArticleReferenceText">
							<xsl:with-param name="pArticleNode" select="$pCurrentArticle"></xsl:with-param>
							<xsl:with-param name="pArticleAuthors" select="$lArticleAuthors"></xsl:with-param>
							<xsl:with-param name="pJournalId" select="$lJournalId"></xsl:with-param>
						</xsl:call-template>
					</xsl:with-param>
					<xsl:with-param name="pArticleDoi"><xsl:value-of select="$lArticleDoi"></xsl:value-of></xsl:with-param>
					<xsl:with-param name="pTaxonNum"><xsl:value-of select="php:function('getStaticCount')"></xsl:value-of></xsl:with-param>
					<xsl:with-param name="pArticleLink" select="$lArticleLink"></xsl:with-param>
					<xsl:with-param name="pScientificName">
						<xsl:call-template name="getTaxonScientificName">
							<xsl:with-param name="pTaxonNode" select="$lCurrentTaxon"></xsl:with-param>
							<xsl:with-param name="pArticleNode" select="$pCurrentArticle"></xsl:with-param>
							<xsl:with-param name="pArticleAuthors" select="$lArticleAuthors"></xsl:with-param>
						</xsl:call-template>
					</xsl:with-param>
					<xsl:with-param name="pArticleAuthors" select="$lArticleAuthors"></xsl:with-param>
					<xsl:with-param name="pJournalId" select="$lJournalId"></xsl:with-param>
					<xsl:with-param name="pArticleId" select="$lArticleId"></xsl:with-param>
					<xsl:with-param name="pArticleNode" select="$pCurrentArticle"></xsl:with-param>
				</xsl:call-template>
			<!--</xsl:if>-->
		</xsl:for-each>
	</xsl:template>

	<!--
		Взимаме научното име на таксона. То се взима по 2 н-на
			1. Ако съществува възела $pTaxonNode/tp:nomenclature/tp:taxon-authority - името е
				съдържанието на възела $pTaxonNode/tp:nomenclature/tp:taxon-name конкатенирано със съдържанието на възела $pTaxonNode/tp:nomenclature/tp:taxon-authority
			2. Ако не същестува горния възел се взимат авторите на статията и накрая се слага годината на публикуване на статията
				pArticleNode/article-meta/contrib-group>
					<contrib contrib-type="author" xlink:type="simple">
						<name name-style="western">
							<surname>Jager</surname>
						</name>
					</contrib>
					<contrib contrib-type="author" xlink:type="simple">
						<name name-style="western">
							<surname>Kunz</surname>
						</name>
					</contrib>
				</contrib-group>


				pArticleNode/article-meta/pub-date pub-type="epub">
					<year>2010</year>
	-->
	<xsl:template name="getTaxonScientificName">
		<xsl:param name="pTaxonNode" />
		<xsl:param name="pArticleNode" />
		<xsl:param name="pArticleAuthors" />

		<xsl:variable name="lTaxonAuthority">
			<xsl:variable name="lTaxonAuthorityNode" select="$pTaxonNode/tp:nomenclature/tp:taxon-authority"></xsl:variable>
			<xsl:choose>
				<xsl:when test="count($lTaxonAuthorityNode) &gt; 0">
					<xsl:value-of select="$lTaxonAuthorityNode"></xsl:value-of>
				</xsl:when>
				<xsl:otherwise>
					<xsl:variable name="lAuthorLastNames" select="$pArticleAuthors/surname"></xsl:variable>
					<xsl:variable name="lArticlePubYear" select="$pArticleNode/front/article-meta/pub-date[@pub-type='epub']/year"></xsl:variable>
					<xsl:for-each select="$lAuthorLastNames">
						<xsl:if test="position() &gt; 1 and position() &lt; last()">
							<xsl:text>, </xsl:text>
						</xsl:if>
						<xsl:if test="position() &gt; 1 and position() = last()">
							<xsl:text> &amp; </xsl:text>
						</xsl:if>
						<xsl:value-of select="."></xsl:value-of>
					</xsl:for-each>
					<xsl:text>, </xsl:text>
					<xsl:value-of select="$lArticlePubYear"></xsl:value-of>
				</xsl:otherwise>
			</xsl:choose>
		</xsl:variable>
		<xsl:variable name="lTaxonName">
			<xsl:variable name="lTaxonNameNode" select="$pTaxonNode/tp:nomenclature/tp:taxon-name"></xsl:variable>
			<xsl:call-template name="get_node_text_template">
				<xsl:with-param name="pNode" select="$lTaxonNameNode"/>
				<xsl:with-param name="pPutSpaces">1</xsl:with-param>
			</xsl:call-template>
		</xsl:variable>
		<xsl:value-of select="$lTaxonName"></xsl:value-of><xsl:text> </xsl:text><xsl:value-of select="$lTaxonAuthority"></xsl:value-of>
	</xsl:template>


	<!--
		Взимаме текста за референцията на статията. Освен като референция текста се показва и като dcterms:bibliographicCitation в описанието е дистрибуцията
		Текста е еднакъв за всички таксони от една статия
			ZA SADARZHANIETO NA TAGA:

			    Jager P, Kunz D (2010) Palystes kreutzmanni sp. n.  a new hunts ZooKeys 67: 1-9


				 article/article-meta/contrib-group>
					<contrib contrib-type="author" xlink:type="simple">
					    <name name-style="western">
						<surname>Jager</surname>
						<given-names>Peter</given-names>
					    </name>
													</contrib>
					<contrib contrib-type="author" xlink:type="simple">
					    <name name-style="western">
						<surname>Kunz</surname>
						<given-names>Dirk</given-names>
					    </name>
													</contrib>
			     </contrib-group>

						BELEZHKA - <given-names>Dirk</given-names> na avtorite se sakrashtava na parva bukva.


			     article/article-meta/pub-date pub-type="epub">
					<year>2010</year>

			     article/article-meta/article-meta>
									<title-group>
					<article-title>
					    <italic>Palystes kreutzmanni</italic> sp. n.  a new hunts
					</article-title>

			      front/journal-meta>
				    <journal-id journal-id-type="publisher-id">ZooKeys</journal-id>

			      article/article-meta/issue>67</issue>
			      article/article-meta/fpage>1</fpage>
			      article/article-meta/lpage>9</lpage>
	-->
	<xsl:template name="getArticleReferenceText">
		<xsl:param name="pArticleNode" />
		<xsl:param name="pArticleAuthors" />
		<xsl:param name="pJournalId" />

		<xsl:variable name="lReferenceText">
			<xsl:variable name="lAuthors" select="$pArticleAuthors"></xsl:variable>
			<xsl:variable name="lArticlePubYear" select="$pArticleNode/front/article-meta/pub-date[@pub-type='epub']/year"></xsl:variable>
			<xsl:variable name="lArticleTitle" select="$pArticleNode/front/article-meta/title-group/article-title"></xsl:variable>
			<xsl:variable name="lJournalTitle" select="$pArticleNode/front/journal-meta/journal-id[@journal-id-type='publisher-id']"></xsl:variable>
			<xsl:variable name="lIssueNumber" select="$pArticleNode/front/article-meta/issue"></xsl:variable>
			<xsl:variable name="lFirstPage" select="$pArticleNode/front/article-meta/fpage"></xsl:variable>
			<xsl:variable name="lLastPage" select="$pArticleNode/front/article-meta/lpage"></xsl:variable>

			<xsl:for-each select="$lAuthors">
				<xsl:variable name="lCurrentAuthor" select="."></xsl:variable>
				<xsl:variable name="lSurname" select="$lCurrentAuthor/surname"></xsl:variable>
				<xsl:variable name="lGivenNames" select="$lCurrentAuthor/given-names"></xsl:variable>
				<xsl:if test="position() &gt; 1">
					<xsl:text>, </xsl:text>
				</xsl:if>
				<xsl:value-of select="$lSurname"></xsl:value-of>
				<xsl:if test="count($lGivenNames) &gt; 0">
					<xsl:text> </xsl:text>
					<xsl:value-of select="php:function('getNameFirstLetter',  string($lGivenNames))"></xsl:value-of>
				</xsl:if>
			</xsl:for-each>
			<xsl:text> </xsl:text>
			(<xsl:value-of select="$lArticlePubYear"></xsl:value-of>)
			<xsl:text> </xsl:text>
			<xsl:value-of select="$lArticleTitle"></xsl:value-of>
			<xsl:text> </xsl:text>
			<xsl:value-of select="$lJournalTitle"></xsl:value-of>
			<xsl:text> </xsl:text>
			<xsl:value-of select="$lIssueNumber"></xsl:value-of>
			<xsl:text>: </xsl:text>
			<xsl:value-of select="$lFirstPage"></xsl:value-of>–<xsl:value-of select="$lLastPage"></xsl:value-of>
		</xsl:variable>
		<xsl:value-of select="normalize-space($lReferenceText)"></xsl:value-of>
	</xsl:template>


	<xsl:template name="singleTaxonTemplate">
		<xsl:param name="pTaxonNode" />
		<xsl:param name="pArticleReference" />
		<xsl:param name="pArticleDoi" />
		<xsl:param name="pArticleLink" />
		<xsl:param name="pTaxonNum" />
		<xsl:param name="pScientificName" />
		<xsl:param name="pArticleAuthors" />
		<xsl:param name="pJournalId" />
		<xsl:param name="pArticleId" />
		<xsl:param name="pArticleNode" />

		<xsl:variable name="lCurrentDate">
			<xsl:value-of select="php:function('date',  'Y-m-d\TH:i:s')"></xsl:value-of>
		</xsl:variable>
		<xsl:variable name="lTreatmentSecs" select="$pTaxonNode/tp:treatment-sec" />
		<xsl:variable name="lTreatmentFigs" select="$pTaxonNode/tp:nomenclature/xref[@ref-type='fig']" />
		<xsl:variable name="lDescription" select="$pTaxonNode/description" />
		<xsl:variable name="lTaxonStatusNode" select="$pTaxonNode/tp:nomenclature/tp:taxon-status" />
		<xsl:variable name="lTaxonName" select="$pTaxonNode/tp:nomenclature/tp:taxon-name" />
		<xsl:variable name="lIdentifier"><xsl:value-of select="php:function('parseArticleDoi',  string($pArticleDoi))"></xsl:value-of>.sp_<xsl:value-of select="$pTaxonNum"></xsl:value-of></xsl:variable>
		<xsl:variable name="lTaxonStatus">
			<xsl:variable name="lNormalizedStatus" select="normalize-space($lTaxonStatusNode)" />
			<xsl:call-template name="ToLower">
				<xsl:with-param name="inputString" select="$lNormalizedStatus"/>
			</xsl:call-template>
		</xsl:variable>

		<!--<xsl:if test="$lTaxonStatus='sp. n.' or $lTaxonStatus='sp. nov.' or $lTaxonStatus='gen. n.' or $lTaxonStatus='gen. nov.'">-->
			<xsl:element name="taxon">
				<xsl:attribute name="xmlns"><xsl:value-of select="$gDefaultNsUri"></xsl:value-of></xsl:attribute>
				<dc:identifier><xsl:value-of select="$lIdentifier"/></dc:identifier>
				<dwc:Kingdom>TAXON KINGDOM</dwc:Kingdom>
				<dwc:Family>TAXON FAMILY</dwc:Family>
				<dwc:ScientificName>
					<xsl:value-of select="normalize-space($pScientificName)"></xsl:value-of>
				</dwc:ScientificName>
				<dcterms:created><xsl:value-of select="$lCurrentDate"/></dcterms:created>
				<dcterms:modified><xsl:value-of select="$lCurrentDate"/></dcterms:modified>
				<reference>
					<xsl:attribute name="doi"><xsl:value-of select="$pArticleDoi"></xsl:value-of></xsl:attribute>
					<xsl:value-of select="$pArticleReference"/>
				</reference>
				<xsl:for-each select="$lTreatmentSecs" >
					<xsl:variable name="lCurrentSec" select="." />
					<xsl:call-template name="taxonTreatmentSecTemplate">
						<xsl:with-param name="pSecNode" select="$lCurrentSec"></xsl:with-param>
						<xsl:with-param name="pArticleReference" select="$pArticleReference"></xsl:with-param>
						<xsl:with-param name="pArticleDoi" select="$pArticleDoi"></xsl:with-param>
						<xsl:with-param name="pIdentifier" select="$lIdentifier"></xsl:with-param>
						<xsl:with-param name="pArticleLink" select="$pArticleLink"></xsl:with-param>
						<xsl:with-param name="pArticleAuthors" select="$pArticleAuthors"></xsl:with-param>
					</xsl:call-template>
				</xsl:for-each>
				<xsl:for-each select="$lTreatmentFigs" >
					<xsl:variable name="lCurrentXref" select="." />
					<xsl:call-template name="taxonTreatmentFigTemplate">
						<xsl:with-param name="pXrefToFigNode" select="$lCurrentXref"></xsl:with-param>
						<xsl:with-param name="pArticleReference" select="$pArticleReference"></xsl:with-param>
						<xsl:with-param name="pArticleDoi" select="$pArticleDoi"></xsl:with-param>
						<xsl:with-param name="pIdentifier" select="$lIdentifier"></xsl:with-param>
						<xsl:with-param name="pArticleLink" select="$pArticleLink"></xsl:with-param>
						<xsl:with-param name="pArticleAuthors" select="$pArticleAuthors"></xsl:with-param>
						<xsl:with-param name="pFigNum" select="position()"></xsl:with-param>
						<xsl:with-param name="pJournalId" select="$pJournalId"></xsl:with-param>
						<xsl:with-param name="pArticleId" select="$pArticleId"></xsl:with-param>
						<xsl:with-param name="pArticleNode" select="$pArticleNode"></xsl:with-param>
					</xsl:call-template>
				</xsl:for-each>
			</xsl:element>
		<!--</xsl:if>-->
	</xsl:template>

	<!--
		Темплейт, който връща xml-a за дадена секция
		Обработват се само distribution и description секциите
	-->
	<xsl:template name="taxonTreatmentSecTemplate">
		<xsl:param name="pSecNode" />
		<xsl:param name="pArticleReference" />
		<xsl:param name="pArticleDoi" />
		<xsl:param name="pIdentifier" />
		<xsl:param name="pArticleLink" />
		<xsl:param name="pArticleAuthors" />

		<xsl:variable name="lSecTypeNode" select="$pSecNode/@sec-type" />
		<xsl:variable name="lSecTypeLabel">
			<xsl:variable name="lNormalizedLabel" select="normalize-space($lSecTypeNode)" />
			<xsl:call-template name="ToLower">
				<xsl:with-param name="inputString" select="$lNormalizedLabel"/>
			</xsl:call-template>
		</xsl:variable>
		<xsl:if test="$lSecTypeLabel='distribution' or $lSecTypeLabel='description'">
			<dataObject>
				<dc:identifier><xsl:value-of select="$pIdentifier"/>_<xsl:value-of select="$lSecTypeLabel"/></dc:identifier>
				<dataType>http://purl.org/dc/dcmitype/Text</dataType>
				<mimeType>text/html</mimeType>
				<xsl:for-each select="$pArticleAuthors">
					<xsl:variable name="lCurrentAuthorSurname" select="./surname"></xsl:variable>
					<xsl:variable name="lCurrentAuthorGivenNames" select="./given-names"></xsl:variable>
					<agent role="author">
						<xsl:if test="count($lCurrentAuthorGivenNames) &gt; 0">
							<xsl:value-of select="$lCurrentAuthorGivenNames"></xsl:value-of>
							<xsl:text> </xsl:text>
						</xsl:if>
						<xsl:value-of select="$lCurrentAuthorSurname"></xsl:value-of>
					</agent>
				</xsl:for-each>

				<xsl:choose>
					<xsl:when test="$lSecTypeLabel='distribution'">
						<dc:title xml:lang="en">Distribution</dc:title>
					</xsl:when>
					<xsl:when test="$lSecTypeLabel='description'">
						<dc:title xml:lang="en">Description</dc:title>
					</xsl:when>
				</xsl:choose>

				<dc:language>en</dc:language>
				<license>http://creativecommons.org/licenses/by/3.0/</license>
				<dcterms:rightsHolder>
					<xsl:for-each select="$pArticleAuthors">
						<xsl:if test="position() &gt; 1">
							<xsl:text>, </xsl:text>
						</xsl:if>
						<xsl:variable name="lCurrentAuthorSurname" select="./surname"></xsl:variable>
						<xsl:variable name="lCurrentAuthorGivenNames" select="./given-names"></xsl:variable>
						<xsl:if test="count($lCurrentAuthorGivenNames) &gt; 0">
							<xsl:value-of select="$lCurrentAuthorGivenNames"></xsl:value-of>
							<xsl:text> </xsl:text>
						</xsl:if>
						<xsl:value-of select="$lCurrentAuthorSurname"></xsl:value-of>
					</xsl:for-each>
				</dcterms:rightsHolder>
				<dcterms:bibliographicCitation>
					<xsl:value-of select="$pArticleReference"/>
				</dcterms:bibliographicCitation>

				<audience>Expert users</audience>
				<audience>General public</audience>
				<dc:source><xsl:value-of select="$pArticleLink"/></dc:source>
				<xsl:choose>
					<xsl:when test="$lSecTypeLabel='distribution'">
						<subject>http://rs.tdwg.org/ontology/voc/SPMInfoItems#Distribution</subject>
					</xsl:when>
					<xsl:when test="$lSecTypeLabel='description'">
						<subject>http://rs.tdwg.org/ontology/voc/SPMInfoItems#GeneralDescription</subject>
					</xsl:when>
				</xsl:choose>
				<dc:description xml:lang="en">
					<xsl:call-template name="get_sec_node_text_template">
						<xsl:with-param name="pNode" select="$pSecNode"/>
					</xsl:call-template>
				</dc:description>
			</dataObject>
		</xsl:if>
	</xsl:template>

	<!--
		Темплейт, който връща xml-a за картинките за текущия таксон
	-->
	<xsl:template name="taxonTreatmentFigTemplate">
		<xsl:param name="pXrefToFigNode" />
		<xsl:param name="pArticleReference" />
		<xsl:param name="pArticleDoi" />
		<xsl:param name="pIdentifier" />
		<xsl:param name="pArticleLink" />
		<xsl:param name="pArticleAuthors" />
		<xsl:param name="pFigNum" />
		<xsl:param name="pJournalId" />
		<xsl:param name="pArticleId" />
		<xsl:param name="pArticleNode" />

		<xsl:variable name="lFigId" select="$pXrefToFigNode/@rid"></xsl:variable>
		<xsl:variable name="lFigNode" select="$pArticleNode//fig[@id=$lFigId]"></xsl:variable>
		<xsl:variable name="lPicFileName" select="$lFigNode//graphic/@xlink:href"></xsl:variable>

		<xsl:variable name='lFigDescription'>
			<xsl:value-of select="$lFigNode/label"></xsl:value-of>
			<xsl:value-of select="$lFigNode/caption"></xsl:value-of>
		</xsl:variable>
		<xsl:variable name='lFigPicUrl'>
			<xsl:value-of select="php:function('getFigPicUrl',  string($gPicUrl), string($pJournalId), string($pArticleId), string($lPicFileName))"></xsl:value-of>
		</xsl:variable>

		<dataObject>
			<dc:identifier><xsl:value-of select="$pIdentifier"/>_p_<xsl:value-of select="$pFigNum"/></dc:identifier>
			<dataType>http://purl.org/dc/dcmitype/StillImage</dataType>
			<mimeType>image/jpeg</mimeType>
			<dcterms:created><xsl:value-of select="php:function('date',  'Y')"></xsl:value-of></dcterms:created>
			<license>http://creativecommons.org/licenses/by/3.0/</license>
			<dcterms:rightsHolder>
				<xsl:for-each select="$pArticleAuthors">
					<xsl:if test="position() &gt; 1">
						<xsl:text>, </xsl:text>
					</xsl:if>
					<xsl:variable name="lCurrentAuthorSurname" select="./surname"></xsl:variable>
					<xsl:variable name="lCurrentAuthorGivenNames" select="./given-names"></xsl:variable>
					<xsl:if test="count($lCurrentAuthorGivenNames) &gt; 0">
						<xsl:value-of select="$lCurrentAuthorGivenNames"></xsl:value-of>
						<xsl:text> </xsl:text>
					</xsl:if>
					<xsl:value-of select="$lCurrentAuthorSurname"></xsl:value-of>
				</xsl:for-each>
			</dcterms:rightsHolder>
			<dcterms:bibliographicCitation>
				<xsl:value-of select="$pArticleReference"/>
			</dcterms:bibliographicCitation>
			<audience>Expert users</audience>
			<audience>General public</audience>
			<dc:source><xsl:value-of select="$pArticleLink"/></dc:source>
			<dc:description xml:lang="en"><xsl:value-of select="$lFigDescription"></xsl:value-of></dc:description>
			<mediaURL><xsl:value-of select="$lFigPicUrl"></xsl:value-of></mediaURL>
			<thumbnailURL><xsl:value-of select="$lFigPicUrl"></xsl:value-of></thumbnailURL>
		</dataObject>
	</xsl:template>

	<!--
		Темплейт, който връща текстовото съдържание на подадения node, като не показва в него label-а или title-a
	-->
	<xsl:template name="get_node_text_template">
		<xsl:param name="pNode" />
		<xsl:param name="pPutSpaces" />
		<xsl:variable name="lLocalName" >
			<xsl:variable name="lTempName" select="local-name($pNode)"></xsl:variable>
			<xsl:call-template name="ToLower">
				<xsl:with-param name="inputString" select="$lTempName"/>
			</xsl:call-template>
		</xsl:variable>
		<xsl:variable name="lTextType" select="$pNode/self::text()" />
		<xsl:variable name="lElementType" select="$pNode/self::*" />
		<xsl:choose>
			<xsl:when test="$lTextType">
				<xsl:choose>
					<xsl:when test="string($pNode) != '' and php:function('trim', string($pNode)) = ''">
						<xsl:text> </xsl:text>
					</xsl:when>
					<xsl:otherwise>
						<xsl:value-of select="php:function('prepareXslText', string($pNode))"></xsl:value-of>
						<xsl:if test="$pPutSpaces &gt; 0"><xsl:text> </xsl:text></xsl:if>
					</xsl:otherwise>
				</xsl:choose>
			</xsl:when>
			<xsl:when test="$lElementType">
				<xsl:choose>
					<xsl:when test="$lLocalName='label' or $lLocalName='title' or $lLocalName='object-id'"></xsl:when>
					<xsl:otherwise>
						<xsl:call-template name="get_element_node_text_template">
							<xsl:with-param name="pNode" select="$pNode"></xsl:with-param>
							<xsl:with-param name="pPutSpaces" select="$pPutSpaces"></xsl:with-param>
						</xsl:call-template>
					</xsl:otherwise>
				</xsl:choose>
			</xsl:when>
		</xsl:choose>
	</xsl:template>

	<!--
		Темплейт, който вика темплейта get_node_text_template за всички деца на подадения node
	-->
	<xsl:template name="get_element_node_text_template">
		<xsl:param name="pNode" />
		<xsl:param name="pPutSpaces" />
		<xsl:for-each select="$pNode/child::node()" >
			<xsl:variable name="lCurrentNode" select="." />
			<xsl:call-template name="get_node_text_template">
				<xsl:with-param name="pNode" select="$lCurrentNode"></xsl:with-param>
				<xsl:with-param name="pPutSpaces" select="$pPutSpaces"></xsl:with-param>
			</xsl:call-template>
		</xsl:for-each>
	</xsl:template>

	<!--
		Темплейт, който връща текстовото съдържание на подадения section node, като не показва в него label-а или title-a
	-->
	<xsl:template name="get_sec_node_text_template">
		<xsl:param name="pNode" />
		<xsl:param name="pPutSpaces" />
		<xsl:variable name="lLocalName" >
			<xsl:variable name="lTempName" select="local-name($pNode)"></xsl:variable>
			<xsl:call-template name="ToLower">
				<xsl:with-param name="inputString" select="$lTempName"/>
			</xsl:call-template>
		</xsl:variable>
		<xsl:variable name="lTextType" select="$pNode/self::text()" />
		<xsl:variable name="lElementType" select="$pNode/self::*" />
		<xsl:choose>
			<xsl:when test="$lTextType">
				<xsl:choose>
					<xsl:when test="string($pNode) != '' and php:function('trim', string($pNode)) = ''">
						<xsl:text> </xsl:text>
					</xsl:when>
					<xsl:otherwise>
						<xsl:value-of select="php:function('prepareXslText', string($pNode))"></xsl:value-of>
						<xsl:if test="$pPutSpaces &gt; 0"><xsl:text> </xsl:text></xsl:if>
					</xsl:otherwise>
				</xsl:choose>
			</xsl:when>
			<xsl:when test="$lElementType">
				<xsl:choose>
					<xsl:when test="$lLocalName='label' or $lLocalName='title' or $lLocalName='object-id' or $lLocalName='fig'"></xsl:when>
					<xsl:otherwise>
						<xsl:if test="$lLocalName='p'">
							<xsl:text>&#10;</xsl:text>
						</xsl:if>
						<xsl:call-template name="get_sec_element_node_text_template">
							<xsl:with-param name="pNode" select="$pNode"></xsl:with-param>
							<xsl:with-param name="pPutSpaces" select="$pPutSpaces"></xsl:with-param>
						</xsl:call-template>
					</xsl:otherwise>
				</xsl:choose>
			</xsl:when>
		</xsl:choose>
	</xsl:template>



	<!--
		Темплейт, който вика темплейта get_sec_node_text_template за всички деца на подадения node
	-->
	<xsl:template name="get_sec_element_node_text_template">
		<xsl:param name="pNode" />
		<xsl:param name="pPutSpaces" />
		<xsl:for-each select="$pNode/child::node()" >
			<xsl:variable name="lCurrentNode" select="." />
			<xsl:call-template name="get_sec_node_text_template">
				<xsl:with-param name="pNode" select="$lCurrentNode"></xsl:with-param>
				<xsl:with-param name="pPutSpaces" select="$pPutSpaces"></xsl:with-param>
			</xsl:call-template>
		</xsl:for-each>
	</xsl:template>
</xsl:stylesheet>