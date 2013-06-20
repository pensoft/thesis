<?xml version='1.0'?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:tp="http://www.plazi.org/taxpub"  xmlns:php="http://php.net/xsl"> 
	
	<xsl:import href="./html_def.xsl"/>
	<!-- Това го слагаме понеже не може да направим трим на keyword-ите (понеже ще загубим xml-a и ще ги обработим като текст - без италик) -->
	<xsl:strip-space elements="kwd"/>
	<xsl:output method="html" encoding="UTF-8"/>

	<xsl:template match="/">
		<xsl:call-template name="common_main">				
		</xsl:call-template>	
	</xsl:template>
	
	
	<!-- 
		Темплейт, който връща html репрезентация на xml-a
	-->
	<xsl:template name="inner_template">		
		<div id="highlightWrapper" class="">
			<div id="highlightTaxonNameWrapper" class="highlightTaxonNameWrapper">
				<div id="highlightTaxonTreatmentWrapper" class="highlightTaxonTreatmentWrapper">
					<div id="highlightCountryWrapper" class="highlightCountryWrapper">
						<div id="highlightCitationWrapper" class="highlightCitationWrapper">
							<div id="highlightTypeescWrapper" class="highlightTypeescWrapper">
								<div id="highlightCollectionWrapper" class="highlightCollectionWrapper">
									<div id="highlightLocalityWrapper" class="highlightLocalityWrapper">
										<div class="wrapper">											
											<div class="headWrapper">
												<xsl:call-template name="highlight_template">					
												</xsl:call-template>
												<xsl:call-template name="link_template">					
												</xsl:call-template>
											</div>
											<div class="contentWrapper" id="contentWrapper">
												<div class="baloonWrapper" id="baloonWrapper" onmouseover="showBaloon(gActiveBaloonType, gActiveBaloonId)" onmouseout="hideBaloonEvent(gActiveBaloonId, event)">
													<div id="baloonInnerWrapper">
														<div class="baloonTop">
															<div class="baloonTopLeftCorner">
																<img src="http://www.pensoft.net/J_FILES/img/baloon_top_left.gif"></img>
															</div>
															<div class="baloonTopMiddle">
																<img src="http://www.pensoft.net/J_FILES/img/baloon_top_middle.jpg"></img>
															</div>
															<div class="baloonTopRightCorner">
																<img src="http://www.pensoft.net/J_FILES/img/baloon_top_right.gif"></img>
															</div>
															<div class="unfloat"></div>
														</div>
														<div class="baloonContent" id="baloonContent"><div class="unfloat"></div></div>
														
														<div class="baloonBottomLeftCorner">
															<img src="http://www.pensoft.net/J_FILES/img/baloon_bottom_left.gif"></img>
														</div>
														<div class="baloonBottomMiddle">
															<img src="http://www.pensoft.net/J_FILES/img/baloon_bottom_middle.jpg"></img>
														</div>
														<div class="baloonBottomRightCorner">
															<img src="http://www.pensoft.net/J_FILES/img/baloon_bottom_right.gif"></img>
														</div>
														<div class="unfloat"></div>
														<div class="arrowDiv"><img id="baloonArrowImg" src="http://www.pensoft.net/J_FILES/img/baloon_arrow_top_left.gif"></img></div>
													</div>
												</div>
												<a name="top"></a>
												<xsl:call-template name="meta_template">	
													<xsl:with-param name="parent" select="./article/front/article-meta"></xsl:with-param>
													<xsl:with-param name="journal_meta" select="./article/front/journal-meta"></xsl:with-param>
												</xsl:call-template>
												<xsl:call-template name="get_abstract_template">	
													<xsl:with-param name="parent" select="./article/front/article-meta/abstract"></xsl:with-param>
												</xsl:call-template>
												<xsl:call-template name="get_keywords_template">	
													<xsl:with-param name="parent" select="./article/front/article-meta/kwd-group"></xsl:with-param>
												</xsl:call-template>
												<xsl:call-template name="get_contents_template">					
													<xsl:with-param name="parent" select="./article/body"></xsl:with-param>
												</xsl:call-template>												
												<xsl:call-template name="get_acknowledgements_template">	
													<xsl:with-param name="parent" select="./article/back/ack"></xsl:with-param>
												</xsl:call-template>
												<xsl:call-template name="get_references_template">	
													<xsl:with-param name="parent" select="./article/back/ref-list"></xsl:with-param>
												</xsl:call-template>												
												<xsl:call-template name="get_sup_materials_template">	
													<xsl:with-param name="materials" select="./article/back/sec"></xsl:with-param>
												</xsl:call-template>												
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>		
		<script>
			initBalloons();			
		</script>
		<xsl:call-template name="build_taxon_names_list">
		</xsl:call-template>
		<script>initScrollConstants()</script>
	</xsl:template>
	
	<!-- 
		Темплейт, който строи списък с таксони, така че после да може да се земе в php-то текста на балоните им
	-->
	
	<xsl:template name="build_taxon_names_list">		
		<xsl:variable name="lAllTaxonNames" select="//tp:taxon-name" />		
		<xsl:if test="count($lAllTaxonNames) &gt; 0">
			<div id="taxonNamesList" class="taxonNamesList">
				<xsl:for-each select="$lAllTaxonNames">
					<xsl:variable name="lCurrentTaxon" select="."></xsl:variable>
					<xsl:variable name="lNodeContent">
						<xsl:call-template name="get_element_node_text_template">	
							<xsl:with-param name="node" select="$lCurrentTaxon"></xsl:with-param>
							<xsl:with-param name="put_spaces"></xsl:with-param>
							<xsl:with-param name="put_hover">0</xsl:with-param>
						</xsl:call-template>
					</xsl:variable>
					<xsl:variable name="lTaxonNormalizedName">
						<xsl:value-of select="normalize-space($lNodeContent)"></xsl:value-of>
					</xsl:variable>
					{**_<xsl:value-of select="$lTaxonNormalizedName"></xsl:value-of>_**}					
				</xsl:for-each>
				{$$__$$}
			</div>		
		</xsl:if>
	</xsl:template>
	
	<!-- 
		Темплейт, който строи меню с линкове към картата на гугъл със всички координати в статията / всички координати в даден taxon treatment
	-->
	
	<xsl:template name="build_localities_menu_template">		
		<xsl:variable name="lAllLocalities" select="//tp:location[@location-type='dwc:verbatimCoordinates']|//named-content[@content-type='dwc:verbatimCoordinates']" />		
		<xsl:if test="count($lAllLocalities) &gt; 0">
			<div class="localitiesMenu" id="localitiesMenuDiv">
				<div class="localitiesMenuRow">
					<a onclick="submitForm('all_localities_form');return false;">All</a>					
				</div>
				<form name="all_localities_form" class="localitiesForm" method="post" target="_blank">
					<xsl:attribute name="action"><xsl:value-of select="$gGoogleMapsUrl"></xsl:value-of></xsl:attribute>
					<xsl:for-each select="$lAllLocalities" >
						<xsl:variable name="lCurrentLocality" select="."></xsl:variable>						
						<input type="hidden" name="coordinates[]">
							<xsl:attribute name="value"><xsl:value-of select="$lCurrentLocality"></xsl:value-of></xsl:attribute>
						</input>
						<input type="hidden" name="labels[]">
							<xsl:attribute name="value"><xsl:value-of select="$lCurrentLocality"></xsl:value-of></xsl:attribute>
						</input>
					</xsl:for-each>
				</form>
				<xsl:variable name="lTreatmentLocalities" select="//tp:taxon-treatment[//tp:location[@location-type='dwc:verbatimCoordinates']|//named-content[@content-type='dwc:verbatimCoordinates']]" />
				<xsl:for-each select="$lTreatmentLocalities">
					<xsl:variable name="lCurrentTreatment" select="."></xsl:variable>
					<xsl:variable name="lTreatmentName">
						<xsl:value-of select="normalize-space($lCurrentTreatment/tp:nomenclature/tp:taxon-name)"></xsl:value-of>
					</xsl:variable>
					<xsl:variable name="lParsedTreatmentName">
						<xsl:call-template name="replaceSymbolTemplate">
							<xsl:with-param name="text" select="$lTreatmentName"></xsl:with-param>
							<xsl:with-param name="searchSymbol"><xsl:text> </xsl:text></xsl:with-param>
							<xsl:with-param name="replacementSymbol">_</xsl:with-param>
						</xsl:call-template>						
					</xsl:variable>
					<xsl:variable name="lCurrentLocalities" select="$lCurrentTreatment//tp:location[@location-type='dwc:verbatimCoordinates']|$lCurrentTreatment//named-content[@content-type='dwc:verbatimCoordinates']"></xsl:variable>
					<xsl:if test="count($lCurrentLocalities) &gt; 0">
						<div class="localitiesMenuRow">
							<a>
								<xsl:attribute name="onclick">submitForm('<xsl:value-of select="$lParsedTreatmentName"></xsl:value-of>_localities_form');return false;</xsl:attribute>
								<xsl:value-of select="$lTreatmentName"></xsl:value-of>
							</a>					
						</div>
						<form class="localitiesForm" method="post" target="_blank">
							<xsl:attribute name="action"><xsl:value-of select="$gGoogleMapsUrl"></xsl:value-of></xsl:attribute>
							<xsl:attribute name="name"><xsl:value-of select="$lParsedTreatmentName"></xsl:value-of>_localities_form</xsl:attribute>
							<xsl:for-each select="$lCurrentLocalities">
								<xsl:variable name="lCurrentLocality" select="."></xsl:variable>								
								<input type="hidden" name="coordinates[]">
									<xsl:attribute name="value"><xsl:value-of select="$lCurrentLocality"></xsl:value-of></xsl:attribute>
								</input>
								<input type="hidden" name="labels[]">
									<xsl:attribute name="value"><xsl:value-of select="$lCurrentLocality"></xsl:value-of></xsl:attribute>
								</input>
							</xsl:for-each>
						</form>
					</xsl:if>
				</xsl:for-each>
			</div>
		</xsl:if>
	</xsl:template>
	
	<!-- 
		Темплейт, който връща html репрезентация на мета данните - заглавие, автор ...
	-->
	
	<xsl:template name="meta_template">
		<xsl:param name="parent" />
		<xsl:param name="journal_meta" />
		
		<xsl:variable name="title" select="$parent/title-group/article-title" />
		<xsl:variable name="authors" select="$parent/contrib-group/contrib[@contrib-type='author']/name" />
		<xsl:variable name="affiliations" select="$parent/aff" />
		<xsl:variable name="uris" select="$parent/contrib-group/contrib[@contrib-type='author']/uri" />		
		<xsl:variable name="lIssue" select="$parent/issue" />
		<xsl:variable name="lFPage" select="$parent/fpage" />
		<xsl:variable name="lLPage" select="$parent/lpage" />
		<xsl:variable name="lArticleId" select="$parent/article-id" />
		<xsl:variable name="lAuthorNotes" select="$parent/author-notes" />	
		<xsl:variable name="lJournalName" select="$journal_meta/journal-title-group/journal-title"></xsl:variable>
		<div class="doiNumber">
			<xsl:value-of select="$lJournalName"></xsl:value-of><xsl:text> </xsl:text><xsl:value-of select="$lIssue"></xsl:value-of>: <xsl:value-of select="$lFPage"></xsl:value-of>&#8211;<xsl:value-of select="$lLPage"></xsl:value-of>, doi: <xsl:value-of select="$lArticleId"/>
		</div>
		<div class="mainTitle">
			<xsl:call-template name="getNodeFormattedText">
				<xsl:with-param name="node" select="$title"></xsl:with-param>
				<xsl:with-param name="put_spaces"></xsl:with-param>
				<xsl:with-param name="put_hover">0</xsl:with-param>
			</xsl:call-template>
		</div>
		<div class="authors">
			<xsl:for-each select="$authors" >				
				<xsl:variable name="first_name" select="./given-names" />
				<xsl:variable name="surname" select="./surname" />													
				<xsl:variable name="lUriSymTemp">
					<xsl:for-each select="../uri">
						<xsl:value-of select="php:function('getUriSymbol',  string(.))"></xsl:value-of>
						<xsl:choose>
							<xsl:when test="position()=last()"></xsl:when>
							<xsl:otherwise><xsl:text>,</xsl:text></xsl:otherwise>
						</xsl:choose>
					</xsl:for-each>
				</xsl:variable>
				<xsl:variable name="lUriSym"><xsl:value-of select="php:function('xslTrim',  string($lUriSymTemp))"></xsl:value-of></xsl:variable>
				<xsl:variable name="lAffNumTemp">
					<xsl:for-each select="../xref[@ref-type='aff']/@rid">
						<xsl:value-of select="php:function('parseAffSymbol',  string(.))"></xsl:value-of>
						<xsl:choose>
							<xsl:when test="position()=last()"></xsl:when>
							<xsl:otherwise><xsl:text>,</xsl:text></xsl:otherwise>
						</xsl:choose>
					</xsl:for-each>
				</xsl:variable>
				<xsl:variable name="lAffNum">
					<xsl:choose>
						<xsl:when test="$lAffNumTemp != ''">
							<xsl:value-of select="php:function('xslTrim',  string($lAffNumTemp))"></xsl:value-of>
						</xsl:when>
						<xsl:when test="count($affiliations) &gt; 0">
							1
						</xsl:when>
						<xsl:otherwise></xsl:otherwise>
					</xsl:choose>
				</xsl:variable>
				<xsl:value-of select="$first_name" /><xsl:text> </xsl:text><xsl:value-of select="$surname" /><xsl:text> </xsl:text>
				<span class="specialSymbols">
					<xsl:value-of select="php:function('xslTrim',  string($lAffNum))"></xsl:value-of>
					<xsl:if test="$lAffNum != '' and $lUriSym != ''">
						<xsl:text>,</xsl:text>
					</xsl:if>
					<xsl:value-of select="$lUriSym"></xsl:value-of>
				</span>
				<xsl:choose>
					<xsl:when test="position()=last()"></xsl:when>
					<xsl:otherwise><xsl:text>, </xsl:text></xsl:otherwise>
				</xsl:choose>					
			</xsl:for-each>
		</div>
		<div class="affilations">
			<xsl:for-each select="$affiliations" >
				<div class="affiliationRow">
					<xsl:variable name="current_affiliation" select="." />
					<xsl:call-template name="get_node_text_template">	
						<xsl:with-param name="node" select="$current_affiliation"></xsl:with-param>
						<xsl:with-param name="put_spaces"></xsl:with-param>
						<xsl:with-param name="put_hover">0</xsl:with-param>
					</xsl:call-template>
				</div>
			</xsl:for-each>
		</div>
		<div class="uris">
			<xsl:for-each select="$uris" >
				<div class="uriRow">
					<xsl:variable name="lCurrentUri" select="." />
					<xsl:variable name="lUriSym" select="php:function('getUriSymbol',  string(.))"></xsl:variable>
					<xsl:if test="$lUriSym != ''">
						<span class="uriSym">							
							<xsl:value-of select="$lUriSym"></xsl:value-of><xsl:text> </xsl:text>						
						</span>
					</xsl:if>
					<xsl:call-template name="getUriTextContent">	
						<xsl:with-param name="pUri" select="$lCurrentUri"></xsl:with-param>
					</xsl:call-template>
				</div>
			</xsl:for-each>
		</div>
		
		<div class="authorNotes">
			<xsl:for-each select="$lAuthorNotes" >
				<div class="authorNotesRow">
					<xsl:variable name="lCurrentRow" select="." />
					<xsl:call-template name="get_node_text_template">
						<xsl:with-param name="node" select="$lCurrentRow"></xsl:with-param>
						<xsl:with-param name="put_spaces"></xsl:with-param>
						<xsl:with-param name="put_hover">0</xsl:with-param>
					</xsl:call-template>
				</div>
			</xsl:for-each>
		</div>
		<xsl:call-template name="metaDatesTemplate">
			<xsl:with-param name="parent" select="$parent"></xsl:with-param>
		</xsl:call-template>
		
	</xsl:template>
	
	<!--
		Темплейт, който връща html репрезентация на информацията относно дата на приемане/публикуване/... на статията 
	-->
	<xsl:template name="metaDatesTemplate">
		<xsl:param name="parent" />
		<xsl:variable name="authors" select="$parent/contrib-group/contrib[@contrib-type='author']/name" />
		<xsl:variable name="lAcademicEditors" select="$parent/contrib-group/contrib[@contrib-type='academic-editor']" />
		<xsl:variable name="lHistoryDates" select="$parent/history//date" />
		<xsl:variable name="lPubdate" select="$parent/pub-date[@pub-type='epub']" />
		<xsl:variable name="lPubyear" select="$parent/pub-date[@pub-type='collection']/year" />
		<xsl:variable name="first_name" select="$authors/given-names" />
		<xsl:variable name="surname" select="$authors/surname" />		
		
		<div class="lMetaDates">
			<xsl:call-template name="academicEditorsTemplate">
				<xsl:with-param name="pAcademicEditors" select="$lAcademicEditors"></xsl:with-param>
			</xsl:call-template>
			<xsl:for-each select="$lHistoryDates">
				<xsl:variable name="lCurrentNumber"><xsl:value-of select="position()"></xsl:value-of></xsl:variable>
				<xsl:variable name="lLastNumber"><xsl:value-of select="last()"></xsl:value-of></xsl:variable>
				<xsl:variable name="lCurrentDate" select="."></xsl:variable>
				
				<xsl:variable name="lPutSeparatorAfter" select="number($lCurrentNumber &lt; $lLastNumber)"></xsl:variable>
				<xsl:variable name="lPutSeparatorBefore" select="number(($lCurrentNumber=1) and (count($lAcademicEditors) &gt; 0))"></xsl:variable>
				<xsl:call-template name="metaSingleDateTemplate">
					<xsl:with-param name="pDate" select="$lCurrentDate"></xsl:with-param>
					<xsl:with-param name="pLabel"><xsl:value-of  select="$lCurrentDate/@date-type"></xsl:value-of><xsl:text> </xsl:text></xsl:with-param>
					<xsl:with-param name="pPutSeparatorAfter" select="$lPutSeparatorAfter"></xsl:with-param>
					<xsl:with-param name="pPutSeparatorBefore" select="$lPutSeparatorBefore"></xsl:with-param>					
				</xsl:call-template>
			</xsl:for-each>
			<xsl:if test="count($lPubdate) &gt; 0">
				<xsl:variable name="lPutSeparatorBefore" select="number((count($lHistoryDates) &gt; 0 ) or (count($lAcademicEditors) &gt; 0))"></xsl:variable>
				<xsl:call-template name="metaSingleDateTemplate">
					<xsl:with-param name="pDate" select="$lPubdate"></xsl:with-param>
					<xsl:with-param name="pLabel"><xsl:text>Published </xsl:text></xsl:with-param>
					<xsl:with-param name="pPutSeparatorBefore" select="$lPutSeparatorBefore"></xsl:with-param>					
				</xsl:call-template>
			</xsl:if>
			<p><xsl:text> </xsl:text></p><br></br>
			<div class="rifgtsrow">
			<p><xsl:text>(C) </xsl:text><xsl:value-of select="$lPubyear" /><xsl:text> </xsl:text><xsl:value-of select="$first_name" /><xsl:text> </xsl:text><xsl:value-of select="$surname" />  <xsl:text>. This is an open access article distributed under the terms of the </xsl:text><a target="_blank" href="http://creativecommons.org/licenses/by/3.0/"><xsl:text>Creative Commons Attribution License</xsl:text></a>, <xsl:text>which permits unrestricted use, distribution, and reproduction in any medium, provided the original author and source are credited.</xsl:text></p>
			<br></br>
			<p><xsl:text>For reference, use of the paginated PDF or printed version of this article is recommended.</xsl:text> </p>
			</div>
			
		</div>
	</xsl:template>
	
	<!--
		Темплейт, който връща html репрезентация на информацията за академичните редактори на статията 
	-->
	<xsl:template name="academicEditorsTemplate">
		<xsl:param name="pAcademicEditors" />		
		<xsl:choose>
			<xsl:when test="count($pAcademicEditors) &gt; 1">
				<span class="node_label">
					Academic editors
				</span>
			</xsl:when>
			<xsl:when test="count($pAcademicEditors)=1">
				<span class="node_label">
					Academic editors
				</span>
			</xsl:when>
		</xsl:choose>
		<xsl:for-each select="$pAcademicEditors">			
			<xsl:variable name="first_name" select="./name/given-names" />
			<xsl:variable name="surname" select="./name/surname" />		
			<span class="academicEditor">						
				<xsl:value-of select="$first_name" /><xsl:text> </xsl:text><xsl:value-of select="$surname" />
				<xsl:choose>
					<xsl:when test="position()=last()"> </xsl:when>
					<xsl:otherwise><xsl:text>, </xsl:text></xsl:otherwise>
				</xsl:choose>
			</span>
		</xsl:for-each>
	</xsl:template>
	
	<!--
		Темплейт, който връща html репрезентация на дата в метаданните на статията
	-->
	<xsl:template name="metaSingleDateTemplate">
		<xsl:param name="pDate" />
		<xsl:param name="pLabel"></xsl:param>
		<xsl:param name="pPutSeparatorBefore">0</xsl:param>
		<xsl:param name="pPutSeparatorAfter">0</xsl:param>
		<xsl:param name="pSeparator"><xsl:text> | </xsl:text></xsl:param>		
		<xsl:variable name="lDay" select="$pDate/day" />
		<xsl:variable name="lMonth" select="$pDate/month" />
		<xsl:variable name="lYear" select="$pDate/year" />		
		<xsl:if test="$pPutSeparatorBefore &gt; 0">
			<span class="dateSeparator"><xsl:value-of select="$pSeparator"></xsl:value-of></span>
		</xsl:if>
		<span class="metaDate">
			<span class="dateLabel"><xsl:value-of select="$pLabel"></xsl:value-of></span>
			<xsl:value-of select="$lDay"></xsl:value-of><xsl:text> </xsl:text>
			<xsl:value-of select="php:function('getMonthName',  string($lMonth))"></xsl:value-of><xsl:text> </xsl:text>
			<xsl:value-of select="$lYear"></xsl:value-of>
		</span>
		<xsl:if test="$pPutSeparatorAfter &gt; 0">
			<span class="dateSeparator"><xsl:value-of select="$pSeparator"></xsl:value-of></span>
		</xsl:if>
	</xsl:template>
	
	<!-- 
		Темплейт, който връща html репрезентация на node-a abstract
	-->
	
	<xsl:template name="get_abstract_template">
		<xsl:param name="parent" />
		<a name="abstract"></a>
		<div class="abstractHolder">			
			<div class="abstractText">
				<xsl:call-template name="get_node_text_template">				
					<xsl:with-param name="node" select="$parent"></xsl:with-param>
				</xsl:call-template>		
			</div>
		</div>
		
	</xsl:template>
	
	<!-- 
		Темплейт, който връща html репрезентация на node-a kwd-group
	-->
	
	<xsl:template name="get_keywords_template">
		<xsl:param name="parent" />		
		<div class="keywordsHolder">
			<div class="keywordsText">
				<span class="node_label">
					<xsl:choose>
						<xsl:when test="count($parent/label) &gt; 0">
							<xsl:call-template name="getNodeFormattedText">	
								<xsl:with-param name="node" select="$parent/label"></xsl:with-param>
							</xsl:call-template>
						</xsl:when>
						<xsl:otherwise>
							Keywords
						</xsl:otherwise>
					</xsl:choose>
				</span>
				<p>
					<xsl:for-each select="$parent/kwd" >
						<xsl:variable name="current_node" select="." />
						<xsl:call-template name="get_node_text_template">	
							<xsl:with-param name="node" select="$current_node"></xsl:with-param>
						</xsl:call-template>
						<xsl:choose>
							<xsl:when test="position()=last()"> </xsl:when>
							<xsl:otherwise><xsl:text>, </xsl:text></xsl:otherwise>
						</xsl:choose>
					</xsl:for-each>
				</p>		
			</div>
		</div>
		
	</xsl:template>
	<!-- 
		Темплейт, който връща html репрезентация на съдържанието на статията
	-->
	<xsl:template name="get_contents_template">
		<xsl:param name="parent" />
		<xsl:for-each select="$parent" >
			<xsl:variable name="node" select="." />
			<xsl:variable name="taxon-treatments" select="./taxon-treatment" />			
			<div>
				<xsl:attribute name="class"> secHolder</xsl:attribute>
				<xsl:call-template name="get_node_text_template">	
					<xsl:with-param name="node" select="$node"></xsl:with-param>
				</xsl:call-template>
			</div>
			
		</xsl:for-each>		
	</xsl:template>
	
	<!-- 
		Темплейт, който връща html репрезентация на 1 taxon-treatment node
	-->
	<xsl:template name="taxon_treatment_template">
		<xsl:param name="taxon_treatment" />		
		<div class="taxonTreatmentsHolder secHolder">
			<div class="taxonTreatmentRow node_taxon-treatment">				
				<xsl:call-template name="get_element_node_text_template">	
					<xsl:with-param name="node" select="$taxon_treatment"></xsl:with-param>
					<xsl:with-param name="put_spaces"></xsl:with-param>
					<xsl:with-param name="put_hover">1</xsl:with-param>
				</xsl:call-template>									
			</div>
		</div>
		
	</xsl:template>
		
	
	<!-- 
		Темплейт, който връща html репрезентация на частта с благодарности в статията
	-->
	<xsl:template name="get_acknowledgements_template">
		<xsl:param name="parent" />
		<a name="acknowledgements"></a>		
		<div class="smallSecHolder acknowledgementsHolder">			
			<div class="acknowledgementsText">
				<xsl:call-template name="get_node_text_template">	
					<xsl:with-param name="node" select="$parent"></xsl:with-param>
				</xsl:call-template>		
			</div>
		</div>
		
	</xsl:template>
	<!-- 
		Темплейт, който връща html репрезентация на цитираните източници в статията
	-->
	<xsl:template name="get_references_template">
		<xsl:param name="parent" />
		<a name="references"></a>		
		<div class="smallSecHolder  referencesHolder">			
			<div class="referencesText">
				<span class="node_title">References</span>
				<xsl:for-each select="$parent/ref" >					
					<div class="referenceRow">						
						<xsl:variable name="current_node" select="." />
						<xsl:call-template name="get_node_text_template">	
							<xsl:with-param name="node" select="$current_node"></xsl:with-param>
							<xsl:with-param name="put_spaces"></xsl:with-param>
						</xsl:call-template>
						<div class="unfloat"></div>
					</div>
					<div class="referenceBaloonContent">
						<xsl:variable name="lReferenceId" select="./@id"></xsl:variable>
						<xsl:attribute name="id">reference_<xsl:value-of select="$lReferenceId"></xsl:value-of></xsl:attribute>
						<xsl:variable name="current_node" select="." />
						<xsl:call-template name="get_node_text_template">	
							<xsl:with-param name="node" select="$current_node"></xsl:with-param>
							<xsl:with-param name="put_spaces"></xsl:with-param>
							<xsl:with-param name="put_hover">2</xsl:with-param>
						</xsl:call-template>
						<div class="unfloat"></div>
					</div>
				</xsl:for-each>	
			</div>
		</div>
		
	</xsl:template>
	<!-- 
		Темплейт, който връща html репрезентация на допълнителни материали към статията
	-->
	<xsl:template name="get_sup_materials_template">
		<xsl:param name="materials" />
		<a name="supplementarymaterials" />
		<div class="supMaterialsHolder">			
			<xsl:for-each select="$materials" >
				<xsl:variable name="node" select="."></xsl:variable>
				<xsl:variable name="title_name" >
					<xsl:variable name="temp_name" select="$node/title"></xsl:variable>								
					<xsl:variable name="lowered_name">
						<xsl:call-template name="ToLower">
							<xsl:with-param name="inputString" select="$temp_name"/>
						</xsl:call-template>
					</xsl:variable>
					<xsl:call-template name="RemoveSpaces">
						<xsl:with-param name="inputString" select="$lowered_name"/>
					</xsl:call-template>
				</xsl:variable>			
				
				<a><xsl:attribute name="name"><xsl:value-of select="$title_name"></xsl:value-of></xsl:attribute></a>
				<div>
					<xsl:attribute name="class"><xsl:value-of select="$title_name"></xsl:value-of>Holder secHolder</xsl:attribute>
					<xsl:call-template name="get_node_text_template">	
						<xsl:with-param name="node" select="$node"></xsl:with-param>
					</xsl:call-template>
				</div>
			</xsl:for-each>	
		</div>
	</xsl:template>
	<!-- 
		Премахва taxon-treatment-ите от текста в секциите, понеже ще се парсват отделно
	-->
	<xsl:template name="get_taxon-treatment_text_template">
		<xsl:param name="node" />		
		<xsl:call-template name="taxon_treatment_template">
			<xsl:with-param name="taxon_treatment" select="$node"></xsl:with-param>
		</xsl:call-template>
	</xsl:template>
	
	<!-- Темплейт за линковете за оцветяване-->
	<xsl:template name="highlight_template">
		<table class="highlightButtonHolder" width="100%" cellspacing="1" cellpadding="0">
			<tr>
				<xsl:call-template name="get_navi_button_template_template">	
					<xsl:with-param name="pNodeCount" select="count(//*[local-name()='taxon-treatment'])"></xsl:with-param>
					<xsl:with-param name="pClass">taxon-treatmentsButton</xsl:with-param>
					<xsl:with-param name="pHref">#</xsl:with-param>
					<xsl:with-param name="pOnClick">swapDivClassname(this, 'highlightTaxonTreatmentWrapper', 'highlightTaxonTreatmentWrapper')</xsl:with-param>
					<xsl:with-param name="pText">Taxon treatments</xsl:with-param>
				</xsl:call-template>
				<xsl:call-template name="get_navi_button_template_template">	
					<xsl:with-param name="pNodeCount" select="count(//*[local-name()='taxon-name'])"></xsl:with-param>
					<xsl:with-param name="pClass">taxon-namesButton</xsl:with-param>
					<xsl:with-param name="pHref">#</xsl:with-param>
					<xsl:with-param name="pOnClick">swapDivClassname(this, 'highlightTaxonNameWrapper', 'highlightTaxonNameWrapper')</xsl:with-param>
					<xsl:with-param name="pText">Taxon names</xsl:with-param>
				</xsl:call-template>
				<xsl:call-template name="get_navi_button_template_template">	
					<xsl:with-param name="pNodeCount" select="count(//*[local-name()='country'])"></xsl:with-param>
					<xsl:with-param name="pClass">countriesButton</xsl:with-param>
					<xsl:with-param name="pHref">#</xsl:with-param>
					<xsl:with-param name="pOnClick">swapDivClassname(this, 'highlightCountryWrapper', 'highlightCountryWrapper')</xsl:with-param>
					<xsl:with-param name="pText">Countries</xsl:with-param>
				</xsl:call-template>
				<xsl:call-template name="get_navi_button_template_template">	
					<xsl:with-param name="pNodeCount" select="count(//xref[@ref-type='bibr'])"></xsl:with-param>
					<xsl:with-param name="pClass">citationsButton</xsl:with-param>
					<xsl:with-param name="pHref">#</xsl:with-param>
					<xsl:with-param name="pOnClick">swapDivClassname(this, 'highlightCitationWrapper', 'highlightCitationWrapper')</xsl:with-param>
					<xsl:with-param name="pText">Citations</xsl:with-param>
				</xsl:call-template>
				<xsl:call-template name="get_navi_button_template_template">	
					<xsl:with-param name="pNodeCount" select="count(//typesc)"></xsl:with-param>
					<xsl:with-param name="pClass">typescButton</xsl:with-param>
					<xsl:with-param name="pHref">#</xsl:with-param>
					<xsl:with-param name="pOnClick">swapDivClassname(this, 'highlightTypeescWrapper', 'highlightTypeescWrapper')</xsl:with-param>
					<xsl:with-param name="pText">Typesc</xsl:with-param>
				</xsl:call-template>
				<xsl:call-template name="get_navi_button_template_template">	
					<xsl:with-param name="pNodeCount" select="count(//collections)"></xsl:with-param>
					<xsl:with-param name="pClass">collectionsButton</xsl:with-param>
					<xsl:with-param name="pHref">#</xsl:with-param>
					<xsl:with-param name="pOnClick">swapDivClassname(this, 'highlightCollectionWrapper', 'highlightCollectionWrapper')</xsl:with-param>
					<xsl:with-param name="pText">Collections</xsl:with-param>
				</xsl:call-template>				
				<xsl:call-template name="get_navi_button_template_template">	
					<xsl:with-param name="pNodeCount" select="count(//tp:location[@location-type='dwc:verbatimCoordinates']|//named-content[@content-type='dwc:verbatimCoordinates'])"></xsl:with-param>
					<xsl:with-param name="pClass">localitiesButton</xsl:with-param>
					<xsl:with-param name="pHref">#</xsl:with-param>
					<xsl:with-param name="pOnClick">swapDivClassname(this, 'highlightLocalityWrapper', 'highlightLocalityWrapper')</xsl:with-param>
					<xsl:with-param name="pText">Map localities</xsl:with-param>
					<xsl:with-param name="pBuildLocalitiesMenu">1</xsl:with-param>
				</xsl:call-template>				
				<td class="offButton"><a href="#" class="inactive" onclick="swapDivClassname(this, 'highlightWrapper', 'highlightWrapper')">Turn highlighting On/Off</a></td>
			</tr>
		</table>
	</xsl:template>
	
	<!-- Темплейт за линковете за навигация -->
	<xsl:template name="link_template">
		<xsl:variable name="lSectionTitles" select="/article/body/sec/title"></xsl:variable>
		<div class="linksButtonWrapper">
			<div class="pathTopLeftScrollBtn">
				<a onMouseOver="startScroll(1)" onMouseOut="stopScroll()" class="LeftScrollBtnLinkActive" id="scrollLeftBtn"></a>

			</div>
			<div class="pathScrollHolder" id="scrollHolderDiv">
				<div id="scrollDiv" class="pathScrollDiv">
					<table class="linksButtonHolder" cellspacing="0" cellpadding="3" id="linksButtonHolder">
						<tr>
							<td class="linksButtonHolder"><a href="#top">Top</a></td>
							<xsl:call-template name="get_navi_button_template_template">	
								<xsl:with-param name="pNodeCount" select="count(/article/front/article-meta/abstract)"></xsl:with-param>
								<xsl:with-param name="pClass">linksButtonHolder</xsl:with-param>
								<xsl:with-param name="pHref">#abstract</xsl:with-param>
								<xsl:with-param name="pOnClick"></xsl:with-param>
								<xsl:with-param name="pText">Abstract</xsl:with-param>
							</xsl:call-template>
							<xsl:for-each select="$lSectionTitles">
								<xsl:variable name="lCurrentTitle" select="."></xsl:variable>
								<xsl:variable name="lParsedTitleName" >
									<xsl:call-template name="parseTitleTemplate">
										<xsl:with-param name="pTitle" select="$lCurrentTitle"></xsl:with-param>
									</xsl:call-template>
								</xsl:variable>
								
								
								<xsl:call-template name="get_navi_button_template_template">	
									<xsl:with-param name="pNodeCount">1</xsl:with-param>
									<xsl:with-param name="pClass">linksButtonHolder</xsl:with-param>
									<xsl:with-param name="pHref">#<xsl:value-of select="$lParsedTitleName"></xsl:value-of></xsl:with-param>
									<xsl:with-param name="pOnClick"></xsl:with-param>
									<xsl:with-param name="pText"><xsl:value-of select="$lCurrentTitle"></xsl:value-of></xsl:with-param>
								</xsl:call-template>
							</xsl:for-each>
							
							<xsl:call-template name="get_navi_button_template_template">	
								<xsl:with-param name="pNodeCount" select="count(/article/back/ack)"></xsl:with-param>
								<xsl:with-param name="pClass">linksButtonHolder</xsl:with-param>
								<xsl:with-param name="pHref">#acknowledgements</xsl:with-param>
								<xsl:with-param name="pOnClick"></xsl:with-param>
								<xsl:with-param name="pText">Acknowledgements</xsl:with-param>
							</xsl:call-template>
							<xsl:call-template name="get_navi_button_template_template">	
								<xsl:with-param name="pNodeCount" select="count(/article/back/ref-list)"></xsl:with-param>
								<xsl:with-param name="pClass">linksButtonHolder</xsl:with-param>
								<xsl:with-param name="pHref">#references</xsl:with-param>
								<xsl:with-param name="pOnClick"></xsl:with-param>
								<xsl:with-param name="pText">References</xsl:with-param>
							</xsl:call-template>
						</tr>
					</table>
				</div>
			</div>
			<div class="pathTopRightScrollBtn">
				<a onMouseOver="startScroll(0)" onMouseOut="stopScroll()" class="RightScrollBtnLinkActive" id="scrollRightBtn"></a>
			</div>
			<div class="unfloat"></div>			

		</div>
	</xsl:template>
	
	<!-- Връща бутон, ако съществуват броят на подадените възли е по-голям от 0  -->
	<xsl:template name="get_navi_button_template_template">
		<xsl:param name="pNodeCount" />
		<xsl:param name="pClass" />
		<xsl:param name="pHref" />
		<xsl:param name="pOnClick" />
		<xsl:param name="pText" />
		<xsl:param name="pBuildLocalitiesMenu">0</xsl:param>
		<xsl:if test="$pNodeCount &gt; 0">
			<td>
				
				<xsl:attribute name="class"><xsl:value-of select="$pClass"></xsl:value-of></xsl:attribute>
				<xsl:if test="$pBuildLocalitiesMenu &gt; 0">
					<xsl:attribute name="onmouseover">showLocalitiesMenu()</xsl:attribute>
					<xsl:attribute name="onmouseout">hideLocalitiesMenu()</xsl:attribute>
					<xsl:attribute name="id">mapLocalityButton</xsl:attribute>
					<xsl:call-template name="build_localities_menu_template"></xsl:call-template>
				</xsl:if>
				<a>
					<xsl:attribute name="href"><xsl:value-of select="$pHref"></xsl:value-of></xsl:attribute>
					<xsl:attribute name="onclick"><xsl:value-of select="$pOnClick"></xsl:value-of></xsl:attribute>
					
					
					<xsl:value-of select="$pText"></xsl:value-of>
				</a>
			</td>
		</xsl:if>		

	</xsl:template>
	
	<!-- Връща бутон, ако съществуват броят на подадените възли е по-голям от 0  -->
	<xsl:template name="parseTitleTemplate">
		<xsl:param name="pTitle" />		
		<xsl:variable name="lLoweredName">
			<xsl:value-of select="php:function('strtolower',  string($pTitle))"></xsl:value-of>
		</xsl:variable>
		<xsl:value-of select="php:function('preg_replace', '/[^a-z0-9]/', '',  string($lLoweredName))"></xsl:value-of>
	</xsl:template>
	
	<!-- Темплейт за javascript код, който трябва да се вкара в главата на страницата -->
	<xsl:template name="js_template">
		<xsl:param name="js_use_window_max">0</xsl:param>
		function swapDivClassname(pLink, pId, pClassName){
			var lDiv = document.getElementById(pId);
			if( lDiv ){
				if( lDiv.className == pClassName ){
					lDiv.className = '';
					pLink.className = 'inactive'
				}else{
					lDiv.className = pClassName;
					pLink.className = ''
				}
			}
		}
		function openw(url, title, options) {
			var newwin = window.open(url, title, options);
			newwin.focus();
		}
		
		
		function showBaloon(pBaloonType, pId, pEvent) {
			
			if( !pId )
				return;
			if( !gBaloon || !gBaloonWrapper)
				return;
				
			
			pEvent = pEvent || window.event;
			clearTimeout(gTimeout);			
			if( gActiveBaloonId &amp;&amp; gActiveBaloonType == pBaloonType &amp;&amp; gActiveBaloonId != pId ){
				gFlagToHideBaloon = true;
				hideBaloon(gActiveBaloonId);
			}else if(gActiveBaloonType == pBaloonType &amp;&amp; gActiveBaloonId == pId ){
				return;
			}
			
			gActiveBaloonId = pId;
			gActiveBaloonType = pBaloonType;
			gFlagToHideBaloon = false;
			var lEventTarget = pEvent.target || pEvent.srcElement;	
			if( pBaloonType == 1 ){//xref
				var lReference = document.getElementById('reference_' + pId);
				var lText = 'Reference not found!';
				if( lReference ){
					lText = lReference.innerHTML;// || lReference.innerText;
				}
				gBaloon.innerHTML = lText;
				gBaloonInnerWrapper.className = 'largeBaloon';
			}else if( pBaloonType == 2 ){//Taxoni
				var lReferenceName = 'taxon_' + pId + '_baloon_div';
				
				var lReference = document.getElementById(lReferenceName);
				gBaloonInnerWrapper.className = 'smallBaloon';
				if( lReference ){
					lText = lReference.innerHTML;// || lReference.innerText;
				}else{
					lText = 'Error during loading taxa data';
				}
				gBaloon.innerHTML = lText;
				
				
			}else if( pBaloonType == 3 ){//Figuri				
				var lFigure = document.getElementById('fig_' + pId);
				var lText = 'Figure not found!';
				if( lFigure ){
					lText = lFigure.innerHTML;// || lFigure.innerText;
				}
				gBaloon.innerHTML = lText;
				gBaloonInnerWrapper.className = 'largeBaloon';
				
				
			}else if( pBaloonType == 4 ){//Tablici				
				var lTable = document.getElementById('table_' + pId);
				var lText = 'Table not found!';
				if( lTable ){
					lText = lTable.innerHTML;// || lTable.innerText;
				}
				gBaloon.innerHTML = lText;
				gBaloonInnerWrapper.className = 'largeBaloon';
				
				
			}			
			gActiveBaloonId = pId;			
			displayBaloon(lEventTarget, pEvent);			
		}
		
		function displayBaloon(pEventTarget, pEvent){//Pozicionira i pokazva balona
			if( !gBaloon || !gBaloonWrapper)
				return;
			var lUseWindowMaxY = parseInt('<xsl:value-of select="$js_use_window_max"></xsl:value-of>');
			gBaloonWrapper.style.display="block";			
			positionBaloon(pEventTarget, pEvent, lUseWindowMaxY);
			
		}
		
		function hideBaloonEvent(pId, pEvent){			
			if( !gBaloon || !gBaloonWrapper)
				return;
			gFlagToHideBaloon = true;
			gTimeout = setTimeout("hideBaloon('" + pId + "')", 10);
			
		}
		
		function hideBaloon(pId){	
			clearTimeout(gTimeout);
			if( !gFlagToHideBaloon )
				return;
			
			gBaloonIsShown = false;
			gActiveBaloonId = 0;
			gActiveBaloonType = false;
			gBaloonWrapper.style.display="none";
		}		

		
		function initBalloons(){
			gBaloon = document.getElementById('baloonContent');
			gBaloonIsShown = false;
			gActiveBaloonId = 0;
			gTimeout = false;
			gActiveBaloonType = false;
			gBaloonWrapper = document.getElementById('baloonWrapper');
			gBaloonInnerWrapper = document.getElementById('baloonInnerWrapper');
			gBaloonArrowImg = document.getElementById('baloonArrowImg');
		}
		
		function positionBaloon(pEventTarget, pEvent, pUseWindowMaxY){
			var lMousePos = getMousePosition(pEvent);
			var lContentWrapper = document.getElementById('contentWrapper');						
			
			
			
			
			//var lY = pEventTarget.offsetTop + pEventTarget.offsetHeight;
			var lY = pEventTarget.offsetHeight;
			//var lX = pEventTarget.offsetLeft;
			var lX = 0;
			var lParent = pEventTarget;
			do {
				lY += lParent.offsetTop;
				lX += lParent.offsetLeft;
				//alert(lParent.tagName + '-' + lY);
			}
			while((lParent = lParent.offsetParent) &amp;&amp; lParent.id != 'contentWrapper')
			
			/*var lXMax = lMousePos['xMax'];*/
			var lXMax = lContentWrapper.offsetWidth - 20;//-20 zaradi scrolla
			var lYMax;
			if( pUseWindowMaxY ){
				lYMax = lMousePos['yMax'];
			}else{
				lYMax = lContentWrapper.scrollHeight;
			}
			lYMax = lYMax - 40;
			var lClassName = 'baloonWrapper';
			var lLeft = 0;
			var lTop = 0;
			var lImageName = 'http://www.pensoft.net/J_FILES/img/baloon_arrow';
			if( lY + gBaloonWrapper.offsetHeight &lt;= lYMax ){
				lTop = lY;
				lClassName = lClassName + 'Bottom';
				lImageName = lImageName + '_bottom';
			}else{
				lTop = lY - gBaloonWrapper.offsetHeight - pEventTarget.offsetHeight;
				if( lTop &lt; 0 )
					lTop = 0;				
				lClassName = lClassName + 'Top';
				lImageName = lImageName + '_top';
			}
			if( lX + gBaloonWrapper.offsetWidth &lt;= lXMax ){
				lLeft = lX;
				lClassName = lClassName + 'Right';
				lImageName = lImageName + '_right';
			}else{
				lLeft = lX -gBaloonWrapper.offsetWidth + 50;
				if( lLeft &lt; 0 )
					lLeft = 0;							
				lClassName = lClassName + 'Left';
				lImageName = lImageName + '_left';
			}
			
			lImageName = lImageName + '.gif';
			gBaloonWrapper.style.left = lLeft + 'px';
			gBaloonWrapper.style.top = lTop + 'px';
			
			gBaloonWrapper.className =  lClassName;
			gBaloonArrowImg.setAttribute('src', lImageName);
		}
		
		function submitForm( pFormName ){
			eval('document.' + pFormName + '.submit()');			
		}
		
		function showLocalitiesMenu(){
			var lMenu = document.getElementById('localitiesMenuDiv');
			if( lMenu )
				lMenu.style.display='block';
 		}
		
		function hideLocalitiesMenu(){
			var lMenu = document.getElementById('localitiesMenuDiv');
			if( lMenu )
				lMenu.style.display='none';
		}
		
		function getMousePosition(pEvent){	
		    if (document.layers) {
			// When the page scrolls in Netscape, the event's mouse position
			// reflects the absolute position on the screen. innerHight/Width
			// is the position from the top/left of the screen that the user is
			// looking at. pageX/YOffset is the amount that the user has
			// scrolled into the page. So the values will be in relation to
			// each other as the total offsets into the page, no matter if
			// the user has scrolled or not.
			xMousePos = pEvent.clientX;
			yMousePos = pEvent.clientY;
			xMousePosMax = window.innerWidth + window.pageXOffset;
			yMousePosMax = window.innerHeight + window.pageYOffset;
		    } else if (document.all) {
			// When the page scrolls in IE, the event's mouse position
			// reflects the position from the top/left of the screen the
			// user is looking at. scrollLeft/Top is the amount the user
			// has scrolled into the page. clientWidth/Height is the height/
			// width of the current page the user is looking at. So, to be
			// consistent with Netscape (above), add the scroll offsets to
			// both so we end up with an absolute value on the page, no
			// matter if the user has scrolled or not.	;
			xMousePos = window.event.x + document.body.scrollLeft;
			yMousePos = window.event.y + document.body.scrollTop;
			xMousePosMax = document.body.clientWidth + document.body.scrollLeft;
			yMousePosMax = document.body.clientHeight + document.body.scrollTop;
		    } else if (document.getElementById) {
			// Netscape 6 behaves the same as Netscape 4 in this regard
			xMousePos = pEvent.clientX;
			yMousePos = pEvent.clientY;
			xMousePosMax = window.innerWidth + window.pageXOffset;
			yMousePosMax = window.innerHeight + window.pageYOffset;
		    }
		    return {
			'x' : xMousePos,
			'y' : yMousePos,
			'xMax' : xMousePosMax,
			'yMax' : yMousePosMax
		    };
		}
		
		var gHolderDiv;
		var gScrolDiv;
		var gRightBtn;
		var gLeftBtn;
		var gLinksTable;
		var gMinLeft;
		function initScrollConstants(){
			gHolderDiv = document.getElementById('scrollHolderDiv');
			gScrolDiv = document.getElementById('scrollDiv');
			gRightBtn = document.getElementById('scrollRightBtn');
			gLeftBtn = document.getElementById('scrollLeftBtn');						
			gLinksTable = document.getElementById('linksButtonHolder');
			gScrolDiv.style.width = gLinksTable.offsetWidth + 'px';
			gHolderDiv.style.width = '';
			var gHolderWidth = gHolderDiv.offsetWidth;	
			var gScrolWidth = gScrolDiv.offsetWidth;
				
			gMinLeft = gHolderWidth - gScrolWidth;
			if(gMinLeft > 0)
				gMinLeft = 0;
			gCurrentLeftPos = 0;
			
			gInterval = null;
			changeDivPosition(gCurrentLeftPos);
		}
		function scrollDivRight(){	
			if( gMinLeft >= 0 ){
				stopScroll()
				return;
			}
			if( gCurrentLeftPos - 1 > gMinLeft )
				gCurrentLeftPos = gCurrentLeftPos - 1;
			else{
				stopScroll()
				gCurrentLeftPos = gMinLeft;
			}
			changeDivPosition(gCurrentLeftPos);
		}

		function scrollDivLeft(){
			if( gMinLeft >= 0 ){
				stopScroll()
				return;
			}
			if( gCurrentLeftPos + 1 &lt; 0 )
				gCurrentLeftPos = gCurrentLeftPos + 1;
			else{
				gCurrentLeftPos = 0;
				stopScroll()
			}
			changeDivPosition(gCurrentLeftPos);
		}

		function checkBtnClasses(){
			if( gLeftBtn ){
				if( gCurrentLeftPos == 0 || gMinLeft &gt;= 0 ){
					gLeftBtn.className = 'LeftScrollBtnLinkInactive';
				}else{
					gLeftBtn.className = 'LeftScrollBtnLinkActive';
				}
			}
			if( gRightBtn ){
				if( gCurrentLeftPos == gMinLeft || gMinLeft &gt;= 0 ){
					gRightBtn.className = 'RightScrollBtnLinkInactive';
				}else{
					gRightBtn.className = 'RightScrollBtnLinkActive';
				}
			}
		}

		function changeDivPosition(pLeft){
			gScrolDiv.style.left  = pLeft + 'px';	
			checkBtnClasses()
		}

		function stopScroll(){	
			if(gInterval)
				clearInterval(gInterval)
		}

		function startScroll(pDir){
			if( gMinLeft &gt;= 0 ){		
				return;
			}
			if( pDir == 0){//Right		
				gInterval = setInterval("scrollDivRight('right')", 20);
			}else{	
				gInterval = setInterval("scrollDivLeft('left')", 20);
			}	
		}
		
		function getfnlink(pFn, pEl) {
			
			
			lDiv = document.getElementById(pFn);
			lEl = lDiv.firstChild;
			while (lEl.nodeType != 1) {
				lEl = lEl.nextSibling;
			}
			for (var i = 0; i &lt; lEl.childNodes.length; i++) {
				if (lEl.childNodes[i].tagName &amp;&amp; lEl.childNodes[i].tagName.toLowerCase() == 'a') {
					lHref = lEl.childNodes[i].href;
					break;
				}
			}
			if (lHref)
				pEl.href = lHref;
		}

        </xsl:template>
</xsl:stylesheet>