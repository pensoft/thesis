<?xml version='1.0'?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:tp="http://www.plazi.org/taxpub"  xmlns:php="http://php.net/xsl"> 
<xsl:import href="./default.xsl"/>
<xsl:output method="html" encoding="UTF-8" media-type="text/html" doctype-public="-//W3C//DTD XHTML 1.0 Transitional//EN" doctype-system="http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd" cdata-section-elements="script style" indent="yes"/>
<!--<xsl:output method="html" encoding="UTF-8" media-type="text/html" doctype-public="-//W3C//DTD XHTML 1.0 Transitional//EN" doctype-system="http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd" cdata-section-elements="script style" indent="yes"/>-->
	<!-- 
		Темплейт, който връща head секцията на html-a;
		Приема като параметър името на css file, който се include-ва
	-->
	<xsl:template name="head_template">
		<xsl:param name="css_filename" />
		<xsl:param name="additional_css_filename" />
		<xsl:param name="js_use_window_max">0</xsl:param>
			<head>
				<title><xsl:value-of select="php:function('xslTrim',  string(/article/front/article-meta/title-group/article-title))"></xsl:value-of></title>
				<meta http-equiv="content-type" content="text/html; charset=UTF-8" />			
				<meta name="description" content="" />
				<meta name="keywords" content="" />			

				<meta name="distribution" content="global" />
				<meta name="robots" content="index, follow, all" />			
				<xsl:if test="$css_filename != ''">
					<link>			
						<xsl:attribute name="rel">stylesheet</xsl:attribute>
						<xsl:attribute name="href"><xsl:value-of select="$css_filename" /></xsl:attribute>
						<xsl:attribute name="media">all</xsl:attribute>
						<xsl:attribute name="title">default</xsl:attribute>
					</link>
				</xsl:if>
				<xsl:if test="$css_filename = ''">
					<script language="javascript">
					   if(self.location==top.location){
					     document.write('<link rel="stylesheet" href="http://www.pensoft.net/J_FILES/img/xsl-large.css" media="all" title="default"></link>');
					   }else{
					     document.write('<link rel="stylesheet" href="http://www.pensoft.net/J_FILES/img/xsl.css" media="all" title="default"></link>');
					   }
					</script>
				</xsl:if>
				
				<xsl:if test="$additional_css_filename != ''">
					<link>			
						<xsl:attribute name="rel">stylesheet</xsl:attribute>
						<xsl:attribute name="href"><xsl:value-of select="$additional_css_filename" /></xsl:attribute>
						<xsl:attribute name="media">all</xsl:attribute>
						<xsl:attribute name="title">default</xsl:attribute>
					</link>
				</xsl:if>
				<script type="text/javascript">
					<xsl:call-template name="js_template">
						<xsl:with-param name="js_use_window_max" select="$js_use_window_max"></xsl:with-param>
					</xsl:call-template>
				</script>
			</head>				
	</xsl:template>
	
	<!-- 
		Footer темплейт
	-->
	<xsl:template name="foot_template">					
	</xsl:template>
	
	
	<!-- 
		Темплейт, който строи html-a;
		Първо слага главата, след това вика темплейт inner_template, а накрая вика Footer темплейт-а
	-->
	<xsl:template name="common_main">
		<xsl:param name="css_filename" />
		<xsl:param name="additional_css_filename" />
		<xsl:param name="js_use_window_max">0</xsl:param>
		<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="bg" lang="bg">
			<xsl:call-template name="head_template">
				<xsl:with-param name="css_filename" select="$css_filename"></xsl:with-param>		
				<xsl:with-param name="additional_css_filename" select="$additional_css_filename"></xsl:with-param>		
				<xsl:with-param name="js_use_window_max" select="$js_use_window_max"></xsl:with-param>
			</xsl:call-template>
			<body>
				<xsl:call-template name="inner_template">					
				</xsl:call-template>
				<xsl:call-template name="foot_template"/>	
			</body>			
		</html>
	</xsl:template>
	
	<!-- 
		Темплейт, който връща текстовото съдържание на подадения node, като го форматира (запазва p, b, i ...) 
		Ако node-а е текстов просто му се връща стойността, а ако е елемент се слага необходимия форматиращ таг и се вика template
		get_element_node_text_template за node-a
	-->	
	<xsl:template name="get_node_text_template">
		<xsl:param name="node" />
		<xsl:param name="put_spaces"></xsl:param>
		<xsl:param name="put_hover">1</xsl:param>
		<xsl:variable name="local_name" >
			<xsl:variable name="temp_name" select="local-name($node)"></xsl:variable>
			<xsl:call-template name="ToLower">
				<xsl:with-param name="inputString" select="$temp_name"/>
			</xsl:call-template>
		</xsl:variable>
		<xsl:variable name="text_type" select="$node/self::text()" />
		<xsl:variable name="element_type" select="$node/self::*" />		
		<xsl:choose>
			<xsl:when test="$text_type"><xsl:call-template name="replaceSymbolTemplate"><xsl:with-param name="text" select="$node"></xsl:with-param><xsl:with-param name="searchSymbol">,</xsl:with-param><xsl:with-param name="replacementSymbol">,&#32;</xsl:with-param></xsl:call-template><xsl:if test="$put_spaces"><xsl:text> </xsl:text></xsl:if>
			</xsl:when>
			<xsl:when test="$element_type">
				<xsl:choose>
					<xsl:when test="$local_name='bold'">
						<b>
							<xsl:call-template name="get_element_node_text_template">	
								<xsl:with-param name="node" select="$node"></xsl:with-param>
								<xsl:with-param name="put_spaces" select="$put_spaces"></xsl:with-param>
								<xsl:with-param name="put_hover" select="$put_hover"></xsl:with-param>								
							</xsl:call-template>
						</b>
					</xsl:when>
					<xsl:when test="$local_name='italic'">
						<i>
							<xsl:call-template name="get_element_node_text_template">	
								<xsl:with-param name="node" select="$node"></xsl:with-param>
								<xsl:with-param name="put_spaces" select="$put_spaces"></xsl:with-param>
								<xsl:with-param name="put_hover" select="$put_hover"></xsl:with-param>
							</xsl:call-template>
						</i>
					</xsl:when>
					<xsl:when test="$local_name='underline'">
						<u>
							<xsl:call-template name="get_element_node_text_template">	
								<xsl:with-param name="node" select="$node"></xsl:with-param>
								<xsl:with-param name="put_spaces" select="$put_spaces"></xsl:with-param>
								<xsl:with-param name="put_hover" select="$put_hover"></xsl:with-param>
							</xsl:call-template>
						</u>
					</xsl:when>
					<xsl:when test="$local_name='sup'">
						<sup>
							<xsl:call-template name="get_element_node_text_template">	
								<xsl:with-param name="node" select="$node"></xsl:with-param>
								<xsl:with-param name="put_spaces" select="$put_spaces"></xsl:with-param>
								<xsl:with-param name="put_hover" select="$put_hover"></xsl:with-param>
							</xsl:call-template>
						</sup>
					</xsl:when>
					<xsl:when test="$local_name='sub'">
						<sub>
							<xsl:call-template name="get_element_node_text_template">	
								<xsl:with-param name="node" select="$node"></xsl:with-param>
								<xsl:with-param name="put_spaces" select="$put_spaces"></xsl:with-param>
								<xsl:with-param name="put_hover" select="$put_hover"></xsl:with-param>
							</xsl:call-template>
						</sub>
					</xsl:when>
					<xsl:when test="$local_name='p'">
						<p>
							<xsl:call-template name="get_element_node_text_template">	
								<xsl:with-param name="node" select="$node"></xsl:with-param>
								<xsl:with-param name="put_spaces" select="$put_spaces"></xsl:with-param>
								<xsl:with-param name="put_hover" select="$put_hover"></xsl:with-param>
							</xsl:call-template>
						</p>
					</xsl:when>
					<xsl:when test="$local_name='ext-link'">
						<xsl:variable name="lLinkHref" select="$node/@xlink:href"></xsl:variable>
						<a target="_blank">
							<xsl:attribute name="href"><xsl:value-of select="$lLinkHref"></xsl:value-of></xsl:attribute>
							<xsl:call-template name="get_element_node_text_template">	
								<xsl:with-param name="node" select="$node"></xsl:with-param>
								<xsl:with-param name="put_spaces" select="$put_spaces"></xsl:with-param>
								<xsl:with-param name="put_hover" select="$put_hover"></xsl:with-param>
							</xsl:call-template>
						</a>
					</xsl:when>
					<xsl:when test="$local_name='td'">
						<td>
							<xsl:call-template name="get_element_node_text_template">	
								<xsl:with-param name="node" select="$node"></xsl:with-param>
								<xsl:with-param name="put_spaces" select="$put_spaces"></xsl:with-param>
								<xsl:with-param name="put_hover" select="$put_hover"></xsl:with-param>
							</xsl:call-template>
						</td>
					</xsl:when>
					<xsl:when test="$local_name='taxon-treatment'">						
						<xsl:call-template name="get_taxon-treatment_text_template">						
							<xsl:with-param name="node" select="$node"></xsl:with-param>
							
						</xsl:call-template>						
					</xsl:when>
					<xsl:when test="$local_name='fig'">
						<xsl:variable name="lFigId" select="$node/@id"></xsl:variable>						
						<xsl:call-template name="get_element_node_text_template">	
							<xsl:with-param name="node" select="$node"></xsl:with-param>
							<xsl:with-param name="put_spaces" select="$put_spaces"></xsl:with-param>
							<xsl:with-param name="put_hover" select="$put_hover"></xsl:with-param>
						</xsl:call-template>
						<div class="figureHolder">
							<xsl:attribute name="id">fig_<xsl:value-of select="$lFigId"></xsl:value-of></xsl:attribute>
							<xsl:call-template name="get_element_node_text_template">	
								<xsl:with-param name="node" select="$node"></xsl:with-param>
								<xsl:with-param name="put_spaces" select="$put_spaces"></xsl:with-param>
								<xsl:with-param name="put_hover">0</xsl:with-param>
							</xsl:call-template>
						</div>
					</xsl:when>
					<xsl:when test="$local_name='fn' and $node/@id">
						<xsl:variable name="lFNId" select="$node/@id"></xsl:variable>
						<xsl:call-template name="get_element_node_text_template">
							<xsl:with-param name="node" select="$node"></xsl:with-param>
							<xsl:with-param name="put_spaces" select="$put_spaces"></xsl:with-param>
							<xsl:with-param name="put_hover" select="$put_hover"></xsl:with-param>
						</xsl:call-template>
						<div class="FNBaloonContent">
							<xsl:attribute name="id">reference_fn_<xsl:value-of select="$lFNId"></xsl:value-of></xsl:attribute>
							<xsl:call-template name="get_element_node_text_template">	
								<xsl:with-param name="node" select="$node"></xsl:with-param>
								<xsl:with-param name="put_spaces" select="$put_spaces"></xsl:with-param>
								<xsl:with-param name="put_hover">0</xsl:with-param>
							</xsl:call-template>
						</div>
					</xsl:when>
					<xsl:when test="$local_name='email' and $node/@xlink:type='simple'">
						<xsl:variable name="lNodeContent">
							<xsl:call-template name="get_element_node_text_template">	
								<xsl:with-param name="node" select="$node"></xsl:with-param>
								<xsl:with-param name="put_spaces" select="$put_spaces"></xsl:with-param>
								<xsl:with-param name="put_hover" select="$put_hover"></xsl:with-param>
							</xsl:call-template>
						</xsl:variable>
						<a>	
							<xsl:attribute name="href">mailto:<xsl:value-of select="php:function('xslTrim',  string($lNodeContent))" /></xsl:attribute>
							<xsl:value-of select="$lNodeContent"></xsl:value-of>
						</a>
					</xsl:when>
					<xsl:when test="$local_name='fig-group'">						
						<xsl:call-template name="get_fig_group_template">	
							<xsl:with-param name="node" select="$node"></xsl:with-param>
						</xsl:call-template>						
					</xsl:when>
					<xsl:when test="$local_name='graphic'">						
						<xsl:call-template name="get_graphic_template">	
							<xsl:with-param name="node" select="$node"></xsl:with-param>
						</xsl:call-template>						
					</xsl:when>
					<xsl:when test="($local_name='title') or ($local_name='label') or ($local_name='taxon-name') or ($local_name='taxon-treatment') or ($local_name='country')">
						<xsl:variable name="lNodeContent">
							<xsl:call-template name="get_element_node_text_template">	
								<xsl:with-param name="node" select="$node"></xsl:with-param>
								<xsl:with-param name="put_spaces" select="$put_spaces"></xsl:with-param>
								<xsl:with-param name="put_hover" select="$put_hover"></xsl:with-param>
							</xsl:call-template>
						</xsl:variable>
						<span>
							<xsl:choose>
								<xsl:when test="count(./ancestor::aff) &gt; 0 and ($local_name='label')">
									<xsl:attribute name="class">uriSym</xsl:attribute>
								</xsl:when>
								<xsl:otherwise>
									<xsl:attribute name="class">node_<xsl:value-of select="$local_name" /></xsl:attribute>
								</xsl:otherwise>
							</xsl:choose>
							
							<xsl:choose>
								<xsl:when test="$local_name='taxon-name'">
									<xsl:copy-of select="php:function('ParseTaxonNameLink', $lNodeContent, $put_hover)" />
								</xsl:when>
								<xsl:when test="$local_name='title'">
									<xsl:call-template name="getNodeFormattedText">	
										<xsl:with-param name="node" select="$node"></xsl:with-param>
										<xsl:with-param name="put_spaces" select="$put_spaces"></xsl:with-param>
										<xsl:with-param name="put_hover" select="$put_hover"></xsl:with-param>
										<xsl:with-param name="parse_taxon_name">1</xsl:with-param>
									</xsl:call-template>
								</xsl:when>
								<xsl:otherwise>
									<xsl:call-template name="getNodeFormattedText">	
										<xsl:with-param name="node" select="$node"></xsl:with-param>
										<xsl:with-param name="put_spaces" select="$put_spaces"></xsl:with-param>
										<xsl:with-param name="put_hover" select="$put_hover"></xsl:with-param>
									</xsl:call-template>
									<xsl:if test="count(./ancestor::aff) &gt; 0 and ($local_name='label')">
										<xsl:text> </xsl:text>
									</xsl:if>
								</xsl:otherwise>
							</xsl:choose>
						</span>
					</xsl:when>
					<xsl:when test="$local_name='location'">
						<xsl:variable name="lNodeContent">
							<xsl:call-template name="get_element_node_text_template">	
								<xsl:with-param name="node" select="$node"></xsl:with-param>
								<xsl:with-param name="put_spaces" select="$put_spaces"></xsl:with-param>
								<xsl:with-param name="put_hover" select="$put_hover"></xsl:with-param>
							</xsl:call-template>
						</xsl:variable>						
						<xsl:variable name="lCoordinateAttribute" select="./@location-type"></xsl:variable>						
						<xsl:choose>
							<xsl:when test="$lCoordinateAttribute='dwc:verbatimCoordinates'">
								<a target="_blank" class="localityLink">
									<xsl:attribute name="href">
										<xsl:value-of select="$gGoogleMapsUrl"></xsl:value-of>?labels[0]=<xsl:value-of select="php:function('urlencode', string($lNodeContent))"></xsl:value-of>&amp;coordinates[0]=<xsl:value-of select="php:function('urlencode', string( $lNodeContent))"></xsl:value-of>
									</xsl:attribute>
									<span>
										<xsl:attribute name="class">node_<xsl:value-of select="$local_name" /></xsl:attribute>
										<xsl:call-template name="get_element_node_text_template">	
											<xsl:with-param name="node" select="$node"></xsl:with-param>
											<xsl:with-param name="put_spaces" select="$put_spaces"></xsl:with-param>
											<xsl:with-param name="put_hover" select="$put_hover"></xsl:with-param>
										</xsl:call-template>
									</span>
								</a>
							</xsl:when>							
							<xsl:otherwise>
								<span>
									<xsl:attribute name="class">node_<xsl:value-of select="$local_name" /></xsl:attribute>
									<xsl:call-template name="get_element_node_text_template">	
										<xsl:with-param name="node" select="$node"></xsl:with-param>
										<xsl:with-param name="put_spaces" select="$put_spaces"></xsl:with-param>
										<xsl:with-param name="put_hover" select="$put_hover"></xsl:with-param>
									</xsl:call-template>
								</span>
							</xsl:otherwise>
						</xsl:choose>
					</xsl:when>	
					<xsl:when test="$local_name='named-content'">
						<xsl:variable name="lNodeContent">
							<xsl:call-template name="get_element_node_text_template">	
								<xsl:with-param name="node" select="$node"></xsl:with-param>
								<xsl:with-param name="put_spaces" select="$put_spaces"></xsl:with-param>
								<xsl:with-param name="put_hover" select="$put_hover"></xsl:with-param>
							</xsl:call-template>
						</xsl:variable>						
						<xsl:variable name="lCoordinateAttribute" select="./@content-type"></xsl:variable>						
						<xsl:choose>
							<xsl:when test="$lCoordinateAttribute='dwc:verbatimCoordinates'">
								<a target="_blank" class="localityLink">
									<xsl:attribute name="href">
										<xsl:value-of select="$gGoogleMapsUrl"></xsl:value-of>?labels[0]=<xsl:value-of select="php:function('urlencode', string($lNodeContent))"></xsl:value-of>&amp;coordinates[0]=<xsl:value-of select="php:function('urlencode', string( $lNodeContent))"></xsl:value-of>
									</xsl:attribute>
									<span>
										<xsl:attribute name="class">node_location</xsl:attribute>
										<xsl:call-template name="get_element_node_text_template">	
											<xsl:with-param name="node" select="$node"></xsl:with-param>
											<xsl:with-param name="put_spaces" select="$put_spaces"></xsl:with-param>
											<xsl:with-param name="put_hover" select="$put_hover"></xsl:with-param>
										</xsl:call-template>
									</span>
								</a>
							</xsl:when>							
							<xsl:otherwise>
								<span>
									<xsl:attribute name="class">node_<xsl:value-of select="$local_name" /></xsl:attribute>
									<xsl:call-template name="get_element_node_text_template">	
										<xsl:with-param name="node" select="$node"></xsl:with-param>
										<xsl:with-param name="put_spaces" select="$put_spaces"></xsl:with-param>
										<xsl:with-param name="put_hover" select="$put_hover"></xsl:with-param>
									</xsl:call-template>
								</span>
							</xsl:otherwise>
						</xsl:choose>
					</xsl:when>				
					<xsl:when test="($local_name='table')">						
						<xsl:call-template name="get_table_text_template">	
							<xsl:with-param name="node" select="$node"></xsl:with-param>
							<xsl:with-param name="put_spaces" select="$put_spaces"></xsl:with-param>
							<xsl:with-param name="put_hover" select="$put_hover"></xsl:with-param>
						</xsl:call-template>						
					</xsl:when>
					<xsl:when test="($local_name='table-wrap')">												
						<xsl:variable name="lClass" select="$node/@content-type"></xsl:variable>
						<div>
							<xsl:attribute name="class">tableWrapHolder <xsl:value-of select="$lClass"></xsl:value-of>Table</xsl:attribute>
							<xsl:call-template name="get_element_node_text_template">	
								<xsl:with-param name="node" select="$node"></xsl:with-param>
								<xsl:with-param name="put_spaces" select="$put_spaces"></xsl:with-param>
								<xsl:with-param name="put_hover" select="$put_hover"></xsl:with-param>							
							</xsl:call-template>						
						</div>
					</xsl:when>
					<xsl:when test="($local_name='xref')  and ($node/@ref-type='bibr')">
						<xsl:variable name="lContent">
							<xsl:call-template name="get_element_node_text_template">	
								<xsl:with-param name="node" select="$node"></xsl:with-param>
								<xsl:with-param name="put_spaces" select="$put_spaces"></xsl:with-param>
								<xsl:with-param name="put_hover" select="$put_hover"></xsl:with-param>
							</xsl:call-template>
						</xsl:variable>
						<xsl:variable name="lRefId" select="./@rid"></xsl:variable>
						<xsl:variable name="lRefCount" select="count(./@rid)"></xsl:variable>						
						<xsl:choose>
							<xsl:when test="($lRefCount=0)or($lRefId='')">
								<xsl:call-template name="get_element_node_text_template">	
									<xsl:with-param name="node" select="$node"></xsl:with-param>
									<xsl:with-param name="put_spaces" select="$put_spaces"></xsl:with-param>
									<xsl:with-param name="put_hover" select="$put_hover"></xsl:with-param>
								</xsl:call-template>
							</xsl:when>
							<xsl:otherwise>
								<span class="xrefLink node_citation">
									<xsl:if test="$put_hover &gt; 0">
										<xsl:attribute name="onmouseover">showBaloon(1, '<xsl:value-of select="$lRefId"></xsl:value-of>', event)</xsl:attribute>
										<xsl:attribute name="onmouseout">hideBaloonEvent('<xsl:value-of select="$lRefId"></xsl:value-of>')</xsl:attribute>
									</xsl:if>
									<xsl:call-template name="get_element_node_text_template">	
										<xsl:with-param name="node" select="$node"></xsl:with-param>
										<xsl:with-param name="put_spaces" select="$put_spaces"></xsl:with-param>
										<xsl:with-param name="put_hover" select="$put_hover"></xsl:with-param>
									</xsl:call-template>							
								</span>
							</xsl:otherwise>
						</xsl:choose>
					</xsl:when>
					<xsl:when test="$local_name='sec'">
						<div class="section">
							<xsl:variable name="title_name" >				
								<xsl:call-template name="parseTitleTemplate">
									<xsl:with-param name="pTitle" select="$node/title"/>
								</xsl:call-template>
							</xsl:variable>									
							<a><xsl:attribute name="name"><xsl:value-of select="$title_name"></xsl:value-of></xsl:attribute></a>
							<xsl:call-template name="get_element_node_text_template">	
								<xsl:with-param name="node" select="$node"></xsl:with-param>
								<xsl:with-param name="put_spaces" select="$put_spaces"></xsl:with-param>
								<xsl:with-param name="put_hover" select="$put_hover"></xsl:with-param>
							</xsl:call-template>
						</div>						
					</xsl:when>
					<xsl:when test="($local_name='xref')  and ($node/@ref-type='table')">
						<xsl:variable name="lContent">
							<xsl:call-template name="get_element_node_text_template">	
								<xsl:with-param name="node" select="$node"></xsl:with-param>
								<xsl:with-param name="put_spaces" select="$put_spaces"></xsl:with-param>
								<xsl:with-param name="put_hover" select="$put_hover"></xsl:with-param>
							</xsl:call-template>
						</xsl:variable>
						<xsl:variable name="lRefId" select="./@rid"></xsl:variable>
						<xsl:variable name="lRefCount" select="count(./@rid)"></xsl:variable>						
						<xsl:choose>
							<xsl:when test="($lRefCount=0)or($lRefId='')">
								<xsl:call-template name="get_element_node_text_template">	
									<xsl:with-param name="node" select="$node"></xsl:with-param>
									<xsl:with-param name="put_spaces" select="$put_spaces"></xsl:with-param>
									<xsl:with-param name="put_hover" select="$put_hover"></xsl:with-param>
								</xsl:call-template>
							</xsl:when>
							<xsl:otherwise>
								<span class="xrefLink node_citation">
									<xsl:if test="$put_hover &gt; 0">
										<xsl:attribute name="onmouseover">showBaloon(4, '<xsl:value-of select="$lRefId"></xsl:value-of>', event)</xsl:attribute>
										<xsl:attribute name="onmouseout">hideBaloonEvent('<xsl:value-of select="$lRefId"></xsl:value-of>')</xsl:attribute>
									</xsl:if>
									<xsl:call-template name="get_element_node_text_template">	
										<xsl:with-param name="node" select="$node"></xsl:with-param>
										<xsl:with-param name="put_spaces" select="$put_spaces"></xsl:with-param>
										<xsl:with-param name="put_hover" select="$put_hover"></xsl:with-param>
									</xsl:call-template>							
								</span>
							</xsl:otherwise>
						</xsl:choose>
					</xsl:when>
					<xsl:when test="($local_name='xref')  and ($node/@ref-type='fig')">
						<xsl:variable name="lContent">
							<xsl:call-template name="get_element_node_text_template">	
								<xsl:with-param name="node" select="$node"></xsl:with-param>
								<xsl:with-param name="put_spaces" select="$put_spaces"></xsl:with-param>
								<xsl:with-param name="put_hover" select="$put_hover"></xsl:with-param>
							</xsl:call-template>
						</xsl:variable>
						<xsl:variable name="lRefId" select="./@rid"></xsl:variable>
						<xsl:variable name="lRefCount" select="count(./@rid)"></xsl:variable>						
						<xsl:choose>
							<xsl:when test="($lRefCount=0)or($lRefId='')">
								<xsl:call-template name="get_element_node_text_template">	
									<xsl:with-param name="node" select="$node"></xsl:with-param>
									<xsl:with-param name="put_spaces" select="$put_spaces"></xsl:with-param>
									<xsl:with-param name="put_hover" select="$put_hover"></xsl:with-param>
								</xsl:call-template>
							</xsl:when>
							<xsl:otherwise>
								<span class="xrefLink node_fig">
									<xsl:if test="$put_hover &gt; 0">
										<xsl:attribute name="onmouseover">showBaloon(3, '<xsl:value-of select="$lRefId"></xsl:value-of>', event)</xsl:attribute>
										<xsl:attribute name="onmouseout">hideBaloonEvent('<xsl:value-of select="$lRefId"></xsl:value-of>')</xsl:attribute>
									</xsl:if>
									<xsl:call-template name="get_element_node_text_template">	
										<xsl:with-param name="node" select="$node"></xsl:with-param>
										<xsl:with-param name="put_spaces" select="$put_spaces"></xsl:with-param>
										<xsl:with-param name="put_hover" select="$put_hover"></xsl:with-param>
									</xsl:call-template>							
								</span>
							</xsl:otherwise>
						</xsl:choose>
					</xsl:when>
					<xsl:when test="($local_name='xref')  and ($node/@ref-type='fn')">
						<xsl:variable name="lContent">
							<xsl:call-template name="get_element_node_text_template">	
								<xsl:with-param name="node" select="$node"></xsl:with-param>
								<xsl:with-param name="put_spaces" select="$put_spaces"></xsl:with-param>
								<xsl:with-param name="put_hover" select="$put_hover"></xsl:with-param>
							</xsl:call-template>
						</xsl:variable>
						<xsl:variable name="lRefId" select="./@rid"></xsl:variable>
						<xsl:variable name="lRefCount" select="count(./@rid)"></xsl:variable>
						
						<xsl:choose>
							<xsl:when test="($lRefCount=0)or($lRefId='')">
								<xsl:call-template name="get_element_node_text_template">	
									<xsl:with-param name="node" select="$node"></xsl:with-param>
									<xsl:with-param name="put_spaces" select="$put_spaces"></xsl:with-param>
									<xsl:with-param name="put_hover" select="$put_hover"></xsl:with-param>
								</xsl:call-template>
							</xsl:when>
							<xsl:otherwise>
								<span class="xrefLink node_fn">
									<xsl:if test="$put_hover = 1">
										<xsl:attribute name="onmouseover">showBaloon(1, 'fn_<xsl:value-of select="$lRefId"></xsl:value-of>', event)</xsl:attribute>
										<xsl:attribute name="onmouseout">hideBaloonEvent('fn_<xsl:value-of select="$lRefId"></xsl:value-of>')</xsl:attribute>
										<sup><xsl:call-template name="get_element_node_text_template">	
											<xsl:with-param name="node" select="$node"></xsl:with-param>
											<xsl:with-param name="put_spaces" select="$put_spaces"></xsl:with-param>
											<xsl:with-param name="put_hover" select="$put_hover"></xsl:with-param>
										</xsl:call-template></sup>
									</xsl:if>
									<xsl:if test="$put_hover = 2">
										<a>
											<xsl:attribute name="onmousedown">javascript:getfnlink('reference_fn_<xsl:value-of select="$lRefId"></xsl:value-of>', this)</xsl:attribute>
											<xsl:attribute name="target">_blank</xsl:attribute>
											<sup><xsl:call-template name="get_element_node_text_template">	
												<xsl:with-param name="node" select="$node"></xsl:with-param>
												<xsl:with-param name="put_spaces" select="$put_spaces"></xsl:with-param>
												<xsl:with-param name="put_hover" select="$put_hover"></xsl:with-param>
											</xsl:call-template></sup>
										</a>
									</xsl:if>
									
								</span>
							</xsl:otherwise>
						</xsl:choose>
					</xsl:when>
					<xsl:when test="($local_name='nomenclature')">
						<div class="taxonNomenclature">
							<xsl:call-template name="get_element_node_text_template">	
								<xsl:with-param name="node" select="$node"></xsl:with-param>
								<xsl:with-param name="put_spaces" select="$put_spaces"></xsl:with-param>
								<xsl:with-param name="put_hover" select="$put_hover"></xsl:with-param>
							</xsl:call-template>
						</div>
					</xsl:when>
					<xsl:when test="($local_name='nomenclature-citation-list')">
						<div class="taxonCitations">
							<xsl:call-template name="get_element_node_text_template">	
								<xsl:with-param name="node" select="$node"></xsl:with-param>
								<xsl:with-param name="put_spaces" select="$put_spaces"></xsl:with-param>
								<xsl:with-param name="put_hover" select="$put_hover"></xsl:with-param>
							</xsl:call-template>			
						</div>
					</xsl:when>
					<xsl:when test="($local_name='nomenclature-citation')">
						<div class="taxonNomenclatureCitation">
							<xsl:call-template name="get_element_node_text_template">	
								<xsl:with-param name="node" select="$node"></xsl:with-param>
								<xsl:with-param name="put_spaces" select="$put_spaces"></xsl:with-param>
								<xsl:with-param name="put_hover" select="$put_hover"></xsl:with-param>
							</xsl:call-template>
						</div>
					</xsl:when>
					<xsl:when test="($local_name='treatment-sec')">
						<div class="taxonTreatmentSec inlineSec">
							<xsl:call-template name="get_element_node_text_template">	
								<xsl:with-param name="node" select="$node"></xsl:with-param>
								<xsl:with-param name="put_spaces" select="$put_spaces"></xsl:with-param>
								<xsl:with-param name="put_hover" select="$put_hover"></xsl:with-param>
							</xsl:call-template>
							<div class="unfloat"></div>
						</div>
					</xsl:when>
					<xsl:when test="($local_name='object-id')">
						<p class="node_object_id">
							<xsl:call-template name="getUriTextContent">	
								<xsl:with-param name="pUri" select="$node"></xsl:with-param>
							</xsl:call-template>
						</p>						
					</xsl:when>
					<xsl:otherwise>								
						<xsl:call-template name="get_element_node_text_template">	
							<xsl:with-param name="node" select="$node"></xsl:with-param>
							<xsl:with-param name="put_spaces" select="$put_spaces"></xsl:with-param>
							<xsl:with-param name="put_hover" select="$put_hover"></xsl:with-param>
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
		<xsl:param name="node" />		
		<xsl:param name="put_spaces" />		
		<xsl:param name="put_hover">1</xsl:param>		
		<xsl:choose>
			<xsl:when test="(local-name($node)='fig') and ($node/graphic)">
				<div class="imgFloatLeft">
					<xsl:for-each select="$node/child::node()" >
						<xsl:variable name="current_node" select="." />
						<xsl:if test="local-name($current_node)='graphic'">
							<xsl:call-template name="get_node_text_template">	
								<xsl:with-param name="node" select="$current_node"></xsl:with-param>
								<xsl:with-param name="put_spaces" select="$put_spaces"></xsl:with-param>
								<xsl:with-param name="put_hover" select="$put_hover"></xsl:with-param>
							</xsl:call-template>
						</xsl:if>
					</xsl:for-each>
				</div>
				<div class="ImgTextFloatRight">
					<xsl:for-each select="$node/child::node()" >
						<xsl:variable name="current_node" select="." />
						<xsl:if test="local-name($current_node)!='graphic'">
							<xsl:call-template name="get_node_text_template">	
								<xsl:with-param name="node" select="$current_node"></xsl:with-param>
								<xsl:with-param name="put_spaces" select="$put_spaces"></xsl:with-param>
								<xsl:with-param name="put_hover" select="$put_hover"></xsl:with-param>
							</xsl:call-template>
						</xsl:if>
					</xsl:for-each>
				</div>
				<div class="unfloat"></div>
			</xsl:when>
			<xsl:otherwise>
				<xsl:for-each select="$node/child::node()" >
					<xsl:variable name="current_node" select="." />
					<xsl:call-template name="get_node_text_template">	
						<xsl:with-param name="node" select="$current_node"></xsl:with-param>
						<xsl:with-param name="put_spaces" select="$put_spaces"></xsl:with-param>
						<xsl:with-param name="put_hover" select="$put_hover"></xsl:with-param>
					</xsl:call-template>
				</xsl:for-each>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>
	
	<!-- 
		Темплейт, който връща текстовото съдържание на подадения node, като в него обработва само форматиращите тагове(u, i, b, sup, sub) 
		Ако е подаден параметъра parse_taxon_name - обработват се и таксоните (т.е. слагат се линкове и балони)
		Ако node-а е текстов просто му се връща стойността, а ако е елемент се слага необходимия форматиращ таг и се вика template
		getElementNodeFormattedText за node-a
	-->	
	<xsl:template name="getNodeFormattedText">
		<xsl:param name="node" />
		<xsl:param name="put_spaces"></xsl:param>
		<xsl:param name="put_hover">1</xsl:param>
		<xsl:param name="parse_taxon_name">0</xsl:param>
		<xsl:variable name="local_name" >
			<xsl:variable name="temp_name" select="local-name($node)"></xsl:variable>
			<xsl:call-template name="ToLower">
				<xsl:with-param name="inputString" select="$temp_name"/>
			</xsl:call-template>
		</xsl:variable>
		<xsl:variable name="text_type" select="$node/self::text()" />
		<xsl:variable name="element_type" select="$node/self::*" />	
		<xsl:choose>
			<xsl:when test="$text_type"><xsl:call-template name="replaceSymbolTemplate"><xsl:with-param name="text" select="$node"></xsl:with-param><xsl:with-param name="searchSymbol">,</xsl:with-param><xsl:with-param name="replacementSymbol">,&#32;</xsl:with-param></xsl:call-template><xsl:if test="$put_spaces"><xsl:text> </xsl:text></xsl:if>
			</xsl:when>
			<xsl:when test="$element_type">
				<xsl:choose>
					<xsl:when test="$local_name='bold'">
						<b>
							<xsl:call-template name="getElementNodeFormattedText">	
								<xsl:with-param name="node" select="$node"></xsl:with-param>
								<xsl:with-param name="put_spaces" select="$put_spaces"></xsl:with-param>
								<xsl:with-param name="put_hover" select="$put_hover"></xsl:with-param>
								<xsl:with-param name="parse_taxon_name" select="$parse_taxon_name"></xsl:with-param>								
							</xsl:call-template>
						</b>
					</xsl:when>
					<xsl:when test="$local_name='italic'">
						<i>
							<xsl:call-template name="getElementNodeFormattedText">	
								<xsl:with-param name="node" select="$node"></xsl:with-param>
								<xsl:with-param name="put_spaces" select="$put_spaces"></xsl:with-param>
								<xsl:with-param name="put_hover" select="$put_hover"></xsl:with-param>
								<xsl:with-param name="parse_taxon_name" select="$parse_taxon_name"></xsl:with-param>
							</xsl:call-template>
						</i>
					</xsl:when>				
					<xsl:when test="$local_name='underline'">
						<u>
							<xsl:call-template name="getElementNodeFormattedText">	
								<xsl:with-param name="node" select="$node"></xsl:with-param>
								<xsl:with-param name="put_spaces" select="$put_spaces"></xsl:with-param>
								<xsl:with-param name="put_hover" select="$put_hover"></xsl:with-param>
								<xsl:with-param name="parse_taxon_name" select="$parse_taxon_name"></xsl:with-param>
							</xsl:call-template>
						</u>
					</xsl:when>
					<xsl:when test="$local_name='sup'">
						<sup>
							<xsl:call-template name="getElementNodeFormattedText">	
								<xsl:with-param name="node" select="$node"></xsl:with-param>
								<xsl:with-param name="put_spaces" select="$put_spaces"></xsl:with-param>
								<xsl:with-param name="put_hover" select="$put_hover"></xsl:with-param>
								<xsl:with-param name="parse_taxon_name" select="$parse_taxon_name"></xsl:with-param>
							</xsl:call-template>
						</sup>
					</xsl:when>
					<xsl:when test="$local_name='sub'">
						<sub>
							<xsl:call-template name="getElementNodeFormattedText">	
								<xsl:with-param name="node" select="$node"></xsl:with-param>
								<xsl:with-param name="put_spaces" select="$put_spaces"></xsl:with-param>
								<xsl:with-param name="put_hover" select="$put_hover"></xsl:with-param>
								<xsl:with-param name="parse_taxon_name" select="$parse_taxon_name"></xsl:with-param>
							</xsl:call-template>
						</sub>
					</xsl:when>
					<xsl:when test="($local_name='taxon-name') and (number($parse_taxon_name) &gt; 0)">
						<xsl:variable name="lNodeContent">
							<xsl:call-template name="getElementNodeFormattedText">	
								<xsl:with-param name="node" select="$node"></xsl:with-param>
								<xsl:with-param name="put_spaces" select="$put_spaces"></xsl:with-param>
								<xsl:with-param name="put_hover" select="$put_hover"></xsl:with-param>
								<xsl:with-param name="parse_taxon_name" select="$parse_taxon_name"></xsl:with-param>								
							</xsl:call-template>
						</xsl:variable>
						<span>
							<xsl:attribute name="class">node_<xsl:value-of select="$local_name" /></xsl:attribute>
							<xsl:copy-of select="php:function('ParseTaxonNameLink', $lNodeContent, $put_hover)" />
						</span>
					</xsl:when>
					<xsl:otherwise>								
						<xsl:call-template name="getElementNodeFormattedText">	
							<xsl:with-param name="node" select="$node"></xsl:with-param>
							<xsl:with-param name="put_spaces" select="$put_spaces"></xsl:with-param>
							<xsl:with-param name="put_hover" select="$put_hover"></xsl:with-param>
							<xsl:with-param name="parse_taxon_name" select="$parse_taxon_name"></xsl:with-param>
						</xsl:call-template>					
					</xsl:otherwise>
				</xsl:choose>
			</xsl:when>
		</xsl:choose>
	</xsl:template>
	
	<!-- 
		Темплейт, който вика темплейта getNodeFormattedText за всички деца на подадения node
	-->
	<xsl:template name="getElementNodeFormattedText">
		<xsl:param name="node" />		
		<xsl:param name="put_spaces" />		
		<xsl:param name="put_hover">1</xsl:param>	
		<xsl:param name="parse_taxon_name">0</xsl:param>	
		<xsl:for-each select="$node/child::node()" >
			<xsl:variable name="current_node" select="." />
			<xsl:call-template name="getNodeFormattedText">	
				<xsl:with-param name="node" select="$current_node"></xsl:with-param>
				<xsl:with-param name="put_spaces" select="$put_spaces"></xsl:with-param>
				<xsl:with-param name="put_hover" select="$put_hover"></xsl:with-param>
				<xsl:with-param name="parse_taxon_name" select="$parse_taxon_name"></xsl:with-param>
			</xsl:call-template>
		</xsl:for-each>
	</xsl:template>
	
	<!-- Default-ен темплейт за таксон трийтмънти -->
	<xsl:template name="get_taxon-treatment_text_template">
		<xsl:param name="node" />
		<xsl:call-template name="get_element_node_text_template">	
			<xsl:with-param name="node" select="$node"></xsl:with-param>
		</xsl:call-template>
	</xsl:template>
	
	<!-- Default-ен темплейт за таблици -->
	<xsl:template name="get_table_text_template">
		<xsl:param name="node" />
		<xsl:param name="put_spaces" />
		<xsl:param name="put_hover" />
		<xsl:param name="pClass" />
		<xsl:variable name="rows" select="$node/tbody/tr|$node/tr" />
		<xsl:variable name="lTableId" select="$node/@id" />		
		<div class='tableDiv'>
			<xsl:attribute name="id">table_<xsl:value-of select="$lTableId"></xsl:value-of></xsl:attribute>
			<table>
				<xsl:attribute name="class">innerTable <xsl:value-of select="$pClass"></xsl:value-of>Table</xsl:attribute>
				<xsl:for-each select="$rows">
					<xsl:variable name="currentRow" select="." />						
					<xsl:call-template name="get_tr_text_template">	
						<xsl:with-param name="node" select="$currentRow"></xsl:with-param>
						<xsl:with-param name="put_spaces" select="$put_spaces"></xsl:with-param>
						<xsl:with-param name="put_hover">0</xsl:with-param>
					</xsl:call-template>
				</xsl:for-each>
			</table>
		</div>
		<table cellspacing="0" cellpadding="0">
			<xsl:attribute name="class">innerTable <xsl:value-of select="$pClass"></xsl:value-of>Table</xsl:attribute>
			<xsl:for-each select="$rows">
				<xsl:variable name="currentRow" select="." />						
				<xsl:call-template name="get_tr_text_template">	
					<xsl:with-param name="node" select="$currentRow"></xsl:with-param>
					<xsl:with-param name="put_spaces" select="$put_spaces"></xsl:with-param>
					<xsl:with-param name="put_hover" select="$put_hover"></xsl:with-param>
				</xsl:call-template>
			</xsl:for-each>
		</table>
	</xsl:template>
	
	<xsl:template name="get_tr_text_template">
		<xsl:param name="node" />
		<xsl:param name="put_spaces" />
		<xsl:param name="put_hover" />
		<xsl:variable name="cells" select="$node/td|th" />		
		<tr>
			<xsl:call-template name="displayTableTdWithClass">	
				<xsl:with-param name="pCells" select="$cells"></xsl:with-param>
				<xsl:with-param name="pCurrentNumber">0</xsl:with-param>
				<xsl:with-param name="put_spaces" select="$put_spaces"></xsl:with-param>
				<xsl:with-param name="put_hover" select="$put_hover"></xsl:with-param>
			</xsl:call-template>
		</tr>
	</xsl:template>
	
	<xsl:template name="displayTableTdWithClass">
		<xsl:param name="pCells" />
		<xsl:param name="pCurrentNumber" />
		<xsl:param name="put_spaces" />
		<xsl:param name="put_hover" />
		<xsl:if test="count($pCells) &gt;= $pCurrentNumber">			
			<xsl:call-template name="get_td_text_template">	
				<xsl:with-param name="node" select="$pCells[$pCurrentNumber+1]"></xsl:with-param>
				<xsl:with-param name="put_spaces" select="$put_spaces"></xsl:with-param>
				<xsl:with-param name="put_hover" select="$put_hover"></xsl:with-param>
				<xsl:with-param name="pClass">cell_<xsl:value-of select="$pCurrentNumber + 1"></xsl:value-of></xsl:with-param>
			</xsl:call-template>
			<xsl:call-template name="displayTableTdWithClass">	
				<xsl:with-param name="pCells" select="$pCells"></xsl:with-param>
				<xsl:with-param name="pCurrentNumber" select="$pCurrentNumber + 1"></xsl:with-param>
				<xsl:with-param name="put_spaces" select="$put_spaces"></xsl:with-param>
				<xsl:with-param name="put_hover" select="$put_hover"></xsl:with-param>
			</xsl:call-template>
		</xsl:if>
	</xsl:template>
	
	<xsl:template name="get_td_text_template">
		<xsl:param name="node" />
		<xsl:param name="put_spaces" />
		<xsl:param name="put_hover" />
		<xsl:param name="pClass" />		
		<xsl:variable name="lColspan" select="$node/@colspan"></xsl:variable>		
		<xsl:variable name="lRowspan" select="$node/@rowspan"></xsl:variable>
		<xsl:variable name="lContent">
			<xsl:call-template name="get_node_text_template">	
				<xsl:with-param name="node" select="$node"></xsl:with-param>
				<xsl:with-param name="put_spaces" select="$put_spaces"></xsl:with-param>
				<xsl:with-param name="put_hover" select="$put_hover"></xsl:with-param>
			</xsl:call-template>
		</xsl:variable>
		<xsl:choose><xsl:when test="local-name($node)='td'">
				<td>
					<xsl:attribute name="class"><xsl:value-of select="$pClass"></xsl:value-of></xsl:attribute>
					<xsl:if test="($lColspan!='')">
						<xsl:attribute name="colspan"><xsl:value-of select="$lColspan"></xsl:value-of></xsl:attribute>
					</xsl:if>
					<xsl:if test="($lRowspan!='')">
						<xsl:attribute name="rowspan"><xsl:value-of select="$lRowspan"></xsl:value-of></xsl:attribute>
					</xsl:if>
					<xsl:call-template name="get_element_node_text_template">	
						<xsl:with-param name="node" select="$node"></xsl:with-param>
						<xsl:with-param name="put_spaces" select="$put_spaces"></xsl:with-param>
						<xsl:with-param name="put_hover" select="$put_hover"></xsl:with-param>
					</xsl:call-template>
				</td>
			</xsl:when>
			<xsl:when test="local-name($node)='th'">
				<th>
					<xsl:attribute name="class"><xsl:value-of select="$pClass"></xsl:value-of></xsl:attribute>
					<xsl:if test="($lColspan!='')">
						<xsl:attribute name="colspan"><xsl:value-of select="$lColspan"></xsl:value-of></xsl:attribute>
					</xsl:if>
					<xsl:if test="($lRowspan!='')">
						<xsl:attribute name="rowspan"><xsl:value-of select="$lRowspan"></xsl:value-of></xsl:attribute>
					</xsl:if>
					<xsl:call-template name="get_element_node_text_template">	
						<xsl:with-param name="node" select="$node"></xsl:with-param>
						<xsl:with-param name="put_spaces" select="$put_spaces"></xsl:with-param>
						<xsl:with-param name="put_hover" select="$put_hover"></xsl:with-param>
					</xsl:call-template>
				</th>
			</xsl:when>
		</xsl:choose>		
	</xsl:template>
	
	<!-- Default-ен темплейт за групи от фигури -->
	<xsl:template name="get_fig_group_template">
                <xsl:param name="node" />
		<xsl:variable name="graphics" select="$node//graphic" />			
                <xsl:variable name="number_of_graphics" select="count($graphics)" />	
		<xsl:variable name="caption" select="$node/caption" />	
		<xsl:variable name="description" select="$node/long-desc" />	
		<xsl:if test="$number_of_graphics > 0">
			<div class="figGroupHolder">
				<div class="figGraphics">
					<xsl:for-each select="$graphics">
						<xsl:variable name="currentGraphic" select="." />						
						<xsl:variable name="currentSrc" select="./@xlink:xref" />
						<xsl:variable name="currentAlt" select="./alt-text" />
						<div class="figGraphicImg">
							
							<img>
								<xsl:attribute name="src"><xsl:value-of select="$currentSrc" /></xsl:attribute>								
								<xsl:attribute name="alt"><xsl:value-of select="$currentAlt" /></xsl:attribute>
							</img>
						</div>
					</xsl:for-each>
					<div class="unfloat"></div>
				</div>
				<div class="figCaptions">
					<span class="figCaption">
						<xsl:call-template name="get_element_node_text_template">	
							<xsl:with-param name="node" select="$caption"></xsl:with-param>
						</xsl:call-template>
					</span>
					<span class="figDescription">
						<xsl:call-template name="get_element_node_text_template">	
							<xsl:with-param name="node" select="$description"></xsl:with-param>
						</xsl:call-template>
					</span>
				</div>
				<div class="unfloat"></div>
			</div>
		</xsl:if>
        </xsl:template>
	
	<!-- Default-ен темплейт за картинки -->
	<xsl:template name="get_graphic_template">
                <xsl:param name="node" />                
		<xsl:variable name="caption" select="$node/caption" />	
		<xsl:variable name="description" select="$node/long-desc" />
		<xsl:variable name="src" select="$node/@xlink:href" />
		<xsl:variable name="alt" select="$node/alt-text" />		
		<div class="graphicHolder">			
			<div class="graphicImg">
				<a target="_blank">
					<xsl:attribute name="href"><xsl:value-of select="$src"></xsl:value-of></xsl:attribute>
					<img>
						<xsl:attribute name="src"><xsl:value-of select="$src" /></xsl:attribute>					
						<xsl:attribute name="alt"><xsl:value-of select="$alt" /></xsl:attribute>
					</img>
				</a>
			</div>
			<div class="graphicCaptions">
				<span class="graphicCaption">
					<xsl:call-template name="get_element_node_text_template">	
						<xsl:with-param name="node" select="$caption"></xsl:with-param>
					</xsl:call-template>
				</span>
				<span class="graphicDescription">
					<xsl:call-template name="get_element_node_text_template">	
						<xsl:with-param name="node" select="$description"></xsl:with-param>
					</xsl:call-template>
				</span>
			</div>
			<div class="unfloat"></div>
		</div>
        </xsl:template>
	
	<!-- Default-ен темплейт за javascript код, който трябва да се вкара в главата на страницата -->
	 <xsl:template name="js_template">
		<xsl:param name="js_use_window_max">0</xsl:param>
		function openw(url, title, options) {
			var newwin = window.open(url, title, options);
			newwin.focus();
		}
        </xsl:template>
	
	<xsl:template name="parseTaxonName">
		<xsl:param name="pTaxonName"></xsl:param>
		<xsl:variable name="lTrimmedTaxonName"><xsl:value-of select="php:function('trim',  string($pTaxonName))"></xsl:value-of></xsl:variable>
		<xsl:value-of select="php:function('preg_replace', '/\.|\s+/i', '_',  string($lTrimmedTaxonName))"></xsl:value-of>
	</xsl:template>
	
	<xsl:template name="getUriTextContent">
		<xsl:param name="pUri"></xsl:param>		
		<xsl:variable name="lUriType"><xsl:value-of select="php:function('parseUriText',  string($pUri))"></xsl:value-of></xsl:variable>
		<xsl:variable name="lTrimmedUri"><xsl:value-of select="php:function('xslTrim',  string($pUri))"></xsl:value-of></xsl:variable>
		<xsl:choose>
			<xsl:when test="number($lUriType) = 1">
				<a>
					<xsl:attribute name="target">_blank</xsl:attribute>
					<xsl:attribute name="href">http://zoobank.org/?lsid=<xsl:value-of select="$lTrimmedUri"></xsl:value-of></xsl:attribute>
					<xsl:value-of select="$lTrimmedUri"></xsl:value-of>
				</a>
			</xsl:when>
			<xsl:when test="number($lUriType) = 2">
				<a>
					<xsl:attribute name="target">_blank</xsl:attribute>
					<xsl:attribute name="href"><xsl:value-of select="$lTrimmedUri"></xsl:value-of></xsl:attribute>
					<xsl:value-of select="$lTrimmedUri"></xsl:value-of>
				</a>
			</xsl:when>
			<xsl:otherwise>
				<xsl:value-of select="$pUri"></xsl:value-of>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>
	
</xsl:stylesheet>