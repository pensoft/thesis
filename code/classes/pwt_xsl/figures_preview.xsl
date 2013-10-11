<?xml version='1.0'?>
<xsl:stylesheet version="1.0"
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:xlink="http://www.w3.org/1999/xlink"
	xmlns:tp="http://www.plazi.org/taxpub" xmlns:php="http://php.net/xsl"
	exclude-result-prefixes="php tp xlink xsl">
	<xsl:output method="xml" encoding="UTF-8" indent="yes"
		omit-xml-declaration="no">
	</xsl:output>

	<xsl:template match="/document">

		<xsl:for-each select="/document/objects//*[@object_id='221']">
			<xsl:variable name="lItemId" select="./@instance_id"></xsl:variable>
			<div>
				<xsl:attribute name="id">
					<xsl:text>Figure-Preview-Wrapper</xsl:text>
					<xsl:value-of select="$lItemId"></xsl:value-of>
				</xsl:attribute>
				<xsl:apply-templates select="." mode="previewBaseMode"></xsl:apply-templates>
			</div>
		</xsl:for-each>
	</xsl:template>

	<xsl:template match="*[@object_id='221']" mode="previewBaseMode">
		<xsl:variable name="lPlateType" select="normalize-space(./fields/*[@id='488']/value/@value_id)"></xsl:variable>
		<td class="P-PopUp-Picture-Holder" style="vertical-align:top;">
			<label>
				<xsl:attribute name="for">fig-<xsl:value-of select="@instance_id"/></xsl:attribute>
				<div>
					<xsl:apply-templates select=".//*[@object_id='222' or @object_id='223' or @object_id='224']" mode="figurePhotoPreview"></xsl:apply-templates>
					<div class="P-Clear"></div>
				</div>
			</label>
		</td>
		<td style="vertical-align:top;">
			<label>
				<xsl:attribute name="for">fig-<xsl:value-of select="@instance_id"/></xsl:attribute>
				<div class="P-Figure-Num">Figure <xsl:value-of select="./fields/*[@id='489']/value"/></div>

				<div class="P-Figure-Desc"><xsl:value-of select=".//fields/*[@id='482']/value" /></div>
			</label>
			<xsl:if test="$lPlateType = 2">
				<div class="P-Figure-InsertOnly">
					<div class="P-Figure-InsertOnly-Checkbox">Insert only: </div>									
					<xsl:for-each select=".//*[@object_id='225' or @object_id='226' or @object_id='227' or @object_id='228' or @object_id='229' or @object_id='230']">							
						<div class="P-Figure-InsertOnly-Checkbox">
							<div>
								<input type="checkbox" onclick="checkSiblingsIsChecked(this)" figurenum="1">
									<xsl:attribute name="value"><xsl:value-of select="@instance_id"></xsl:value-of></xsl:attribute>
									<xsl:attribute name="name">fig-<xsl:value-of select="@instance_id"></xsl:value-of></xsl:attribute>
								</input>
							</div>
							<xsl:choose>
								<xsl:when test="@object_id='225'">a</xsl:when>
								<xsl:when test="@object_id='226'">b</xsl:when>
								<xsl:when test="@object_id='227'">c</xsl:when>
								<xsl:when test="@object_id='228'">d</xsl:when>
								<xsl:when test="@object_id='229'">e</xsl:when>
								<xsl:when test="@object_id='230'">f</xsl:when>
							</xsl:choose><xsl:text> </xsl:text>
						</div>
					</xsl:for-each>	
				</div>
			</xsl:if>
			<div class="P-Clear"></div>
		</td>
	</xsl:template>
	<!-- Image figure -->
	<xsl:template match="*[@object_id='222']" mode="figurePhotoPreview">
		<div class="P-Picture-Holder" style="float:left">
			<div class="pointerLink">
				<img style="float: left;"  alt="">
					<!--xsl:attribute name="src">/showfigure.php?filename=c45x82y_<xsl:value-of select="./fields/*[@id='483']/value"></xsl:value-of>.jpg</xsl:attribute-->
					<xsl:attribute name="src"><xsl:value-of select="$pSiteUrl"/>/showfigure.php?filename=singlefigmini_<xsl:value-of select="./fields/*[@id='483']/value"></xsl:value-of>.jpg</xsl:attribute>
				</img>
				<div class="P-Clear"></div>
			</div>
		</div>
	</xsl:template>
	
	<!-- Video figure -->
	<xsl:template match="*[@object_id='223']" mode="figurePhotoPreview">
		<xsl:variable name="lVideoId" select="php:function('getYouTubeIdFromURL', string(./fields/*[@id='486']))"></xsl:variable>
		<div >
				<img style="float: left;"   title="YouTube Thumbnail Test"  width="90px" height="82px" alt="">
					<xsl:attribute name="src">http://img.youtube.com/vi/<xsl:value-of select="$lVideoId"></xsl:value-of>/1.jpg</xsl:attribute>
					<xsl:attribute name="id"><xsl:value-of select="$lVideoId"></xsl:value-of></xsl:attribute>
					<xsl:attribute name="class">youtube_<xsl:value-of select="$lVideoId"></xsl:value-of> youtubeVideoThumbnail</xsl:attribute>						
				</img>	
				<div class="P-Clear"></div>
		</div>		
	</xsl:template>
	
	<!-- Plate figure -->
	<xsl:template match="*[@object_id='224']" mode="figurePhotoPreview">
		
		<div class="P-Picture-Holder" style="float:left">
				<div class="pointerLink">
					<xsl:apply-templates select=".//*[@object_id='231' or @object_id='232' or @object_id='233' or @object_id='234']" mode="singleFigSmallPreview"/>
				<div class="P-Clear"></div>
			</div>
		</div>
		
		<!--div>
			<xsl:apply-templates select=".//*[@object_id='231' or @object_id='232' or @object_id='233' or @object_id='234']" mode="singleFigSmallPreview"/>	
			<div class="P-Clear"></div>
		</div-->
	</xsl:template>
	
	
	<xsl:template name="placePlateMiniImgIfNecessary">
		<xsl:param name="pInstanceNode"/>
		<xsl:param name="pPicPrefix"/>
		<xsl:param name="pPlateType"/>
		<xsl:if test="$pInstanceNode/fields/*[@id='484']/value != ''">	
			<xsl:variable name="lImg">	
				<img alt="">
					<xsl:attribute name="src"><xsl:value-of select="$pSiteUrl"/>/showfigure.php?filename=<xsl:value-of select="$pPicPrefix" />_<xsl:value-of select="$pInstanceNode/fields/*[@id='484']/value"></xsl:value-of>.jpg</xsl:attribute>
				</img>
			</xsl:variable>
			<xsl:choose>
				<xsl:when test="$pPlateType='1'">
					<div style="text-align: center; display: table; width: 90px">
						<div class="twocolumnmini fig">
							<xsl:attribute name="rid"><xsl:value-of select="$pInstanceNode/@instance_id" /></xsl:attribute>
							<xsl:copy-of select="$lImg"></xsl:copy-of>
						</div>
					</div>
				</xsl:when>
				<xsl:when test="$pPlateType='2'">
					<div class="twocolumnmini fig">
						<xsl:attribute name="rid"><xsl:value-of select="$pInstanceNode/@instance_id" /></xsl:attribute>
						<xsl:copy-of select="$lImg"></xsl:copy-of>
					</div>
				</xsl:when>
				<xsl:when test="$pPlateType='3' or $pPlateType='4'">
					<div class="twocolumnminiholder">
						<div class="twocolumnmini fig">
							<xsl:attribute name="rid"><xsl:value-of select="$pInstanceNode/@instance_id" /></xsl:attribute>
							<xsl:copy-of select="$lImg"></xsl:copy-of>
						</div>
					</div>
				</xsl:when>
			</xsl:choose>
		</xsl:if>
	</xsl:template>

	<!-- Plate type 1 image preview -->
	<xsl:template match="*[@object_id='231']" mode="singleFigSmallPreview">
		<xsl:call-template name="placePlateMiniImgIfNecessary">
			<xsl:with-param name="pInstanceNode" select="./*[@object_id='225']"></xsl:with-param>
			<xsl:with-param name="pPicPrefix">singlefigmini</xsl:with-param>
			<xsl:with-param name="pPlateType">1</xsl:with-param>
		</xsl:call-template>
		<xsl:call-template name="placePlateMiniImgIfNecessary">
			<xsl:with-param name="pInstanceNode" select="./*[@object_id='226']"></xsl:with-param>
			<xsl:with-param name="pPicPrefix">singlefigmini</xsl:with-param>
			<xsl:with-param name="pPlateType">1</xsl:with-param>
		</xsl:call-template>
	</xsl:template>

	<!-- Plate type 2 image preview -->
	<xsl:template match="*[@object_id='232']" mode="singleFigSmallPreview">
		<xsl:call-template name="placePlateMiniImgIfNecessary">
				<xsl:with-param name="pInstanceNode" select="./*[@object_id='225']"></xsl:with-param>
				<xsl:with-param name="pPicPrefix">plateportraitmini</xsl:with-param>
				<xsl:with-param name="pPlateType">2</xsl:with-param>
			</xsl:call-template>
		<xsl:call-template name="placePlateMiniImgIfNecessary">
			<xsl:with-param name="pInstanceNode" select="./*[@object_id='226']"></xsl:with-param>
			<xsl:with-param name="pPicPrefix">plateportraitmini</xsl:with-param>
			<xsl:with-param name="pPlateType">2</xsl:with-param>
		</xsl:call-template>
	</xsl:template>

	<!-- Plate type 3 image preview -->
	<xsl:template match="*[@object_id='233']" mode="singleFigSmallPreview">
		<div>
			<xsl:call-template name="placePlateMiniImgIfNecessary">
				<xsl:with-param name="pInstanceNode" select="./*[@object_id='225']"></xsl:with-param>
				<xsl:with-param name="pPicPrefix">twocolumnmini</xsl:with-param>
				<xsl:with-param name="pPlateType">3</xsl:with-param>
			</xsl:call-template>
			<xsl:call-template name="placePlateMiniImgIfNecessary">
				<xsl:with-param name="pInstanceNode" select="./*[@object_id='226']"></xsl:with-param>
				<xsl:with-param name="pPicPrefix">twocolumnmini</xsl:with-param>
				<xsl:with-param name="pPlateType">3</xsl:with-param>
			</xsl:call-template>
			<xsl:call-template name="placePlateMiniImgIfNecessary">
				<xsl:with-param name="pInstanceNode" select="./*[@object_id='227']"></xsl:with-param>
				<xsl:with-param name="pPicPrefix">twocolumnmini</xsl:with-param>
				<xsl:with-param name="pPlateType">3</xsl:with-param>
			</xsl:call-template>
			<xsl:call-template name="placePlateMiniImgIfNecessary">
				<xsl:with-param name="pInstanceNode" select="./*[@object_id='228']"></xsl:with-param>
				<xsl:with-param name="pPicPrefix">twocolumnmini</xsl:with-param>
				<xsl:with-param name="pPlateType">3</xsl:with-param>
			</xsl:call-template>
		</div>
	</xsl:template>

	<!-- Plate type 4 image preview -->
	<xsl:template match="*[@object_id='234']" mode="singleFigSmallPreview">
		<div>
			<xsl:call-template name="placePlateMiniImgIfNecessary">
				<xsl:with-param name="pInstanceNode" select="./*[@object_id='225']"></xsl:with-param>
				<xsl:with-param name="pPicPrefix">twocolumnmini</xsl:with-param>
				<xsl:with-param name="pPlateType">4</xsl:with-param>
			</xsl:call-template>
			<xsl:call-template name="placePlateMiniImgIfNecessary">
				<xsl:with-param name="pInstanceNode" select="./*[@object_id='226']"></xsl:with-param>
				<xsl:with-param name="pPicPrefix">twocolumnmini</xsl:with-param>
				<xsl:with-param name="pPlateType">4</xsl:with-param>
			</xsl:call-template>
			<xsl:call-template name="placePlateMiniImgIfNecessary">
				<xsl:with-param name="pInstanceNode" select="./*[@object_id='227']"></xsl:with-param>
				<xsl:with-param name="pPicPrefix">twocolumnmini</xsl:with-param>
				<xsl:with-param name="pPlateType">4</xsl:with-param>
			</xsl:call-template>
			<xsl:call-template name="placePlateMiniImgIfNecessary">
				<xsl:with-param name="pInstanceNode" select="./*[@object_id='228']"></xsl:with-param>
				<xsl:with-param name="pPicPrefix">twocolumnmini</xsl:with-param>
				<xsl:with-param name="pPlateType">4</xsl:with-param>
			</xsl:call-template>
			<xsl:call-template name="placePlateMiniImgIfNecessary">
				<xsl:with-param name="pInstanceNode" select="./*[@object_id='229']"></xsl:with-param>
				<xsl:with-param name="pPicPrefix">twocolumnmini</xsl:with-param>
				<xsl:with-param name="pPlateType">4</xsl:with-param>
			</xsl:call-template>
			<xsl:call-template name="placePlateMiniImgIfNecessary">
				<xsl:with-param name="pInstanceNode" select="./*[@object_id='230']"></xsl:with-param>
				<xsl:with-param name="pPicPrefix">twocolumnmini</xsl:with-param>
				<xsl:with-param name="pPlateType">4</xsl:with-param>
			</xsl:call-template>
		</div>
	</xsl:template>



</xsl:stylesheet>