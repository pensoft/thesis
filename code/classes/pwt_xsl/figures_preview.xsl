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
		<td class="P-PopUp-Picture-Holder" valign="top">
			<label>
				<xsl:attribute name="for">fig-<xsl:value-of select="@instance_id"/></xsl:attribute>
				<div>
					<xsl:apply-templates select=".//*[@object_id='222' or @object_id='223' or @object_id='224']" mode="figurePhotoPreview"></xsl:apply-templates>
					<div class="P-Clear"></div>
				</div>
			</label>
		</td>
		<td valign="top">
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
		<div >
				<img style="float: left;"  alt="">
					<xsl:attribute name="src">/showfigure.php?filename=c45x82y_<xsl:value-of select="./fields/*[@id='483']/value"></xsl:value-of>.jpg&amp;45</xsl:attribute>
				</img>
				<div class="P-Clear"></div>
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
		<div>
			<xsl:apply-templates select=".//*[@object_id='231' or @object_id='232' or @object_id='233' or @object_id='234']" mode="singleFigSmallPreview"/>	
			<div class="P-Clear"></div>
		</div>
	</xsl:template>
	
	
	<!-- Plate type 1 image preview -->
	<xsl:template match="*[@object_id='231']" mode="singleFigSmallPreview">
		<img style="float: left;"  alt="">
			<xsl:attribute name="src">/showfigure.php?filename=c90x41y_<xsl:value-of select="./*[@object_id='225']/fields/*[@id='484']/value"></xsl:value-of>.jpg&amp;45</xsl:attribute>
		</img>
		<img style="float: left;"  alt="">
			<xsl:attribute name="src">/showfigure.php?filename=c90x41y_<xsl:value-of select="./*[@object_id='226']/fields/*[@id='484']/value"></xsl:value-of>.jpg&amp;45</xsl:attribute>
		</img>
	</xsl:template>
	
	<!-- Plate type 2 image preview -->
	<xsl:template match="*[@object_id='232']" mode="singleFigSmallPreview">
		<img style="float: left;"  alt="">
			<xsl:attribute name="src">/showfigure.php?filename=c45x82y_<xsl:value-of select="./*[@object_id='225']/fields/*[@id='484']/value"></xsl:value-of>.jpg&amp;45</xsl:attribute>
		</img>
		<img style="float: left;"  alt="">
			<xsl:attribute name="src">/showfigure.php?filename=c45x82y_<xsl:value-of select="./*[@object_id='226']/fields/*[@id='484']/value"></xsl:value-of>.jpg&amp;45</xsl:attribute>
		</img>
	</xsl:template>
	
	<!-- Plate type 3 image preview -->
	<xsl:template match="*[@object_id='233']" mode="singleFigSmallPreview">
		<img style="float: left;"  alt="">
			<xsl:attribute name="src">/showfigure.php?filename=c45x41y_<xsl:value-of select="./*[@object_id='225']/fields/*[@id='484']/value"></xsl:value-of>.jpg&amp;45</xsl:attribute>
		</img>
		<img style="float: left;"  alt="">
			<xsl:attribute name="src">/showfigure.php?filename=c45x41y_<xsl:value-of select="./*[@object_id='226']/fields/*[@id='484']/value"></xsl:value-of>.jpg&amp;45</xsl:attribute>
		</img>
		<img style="float: left;"  alt="">
			<xsl:attribute name="src">/showfigure.php?filename=c45x41y_<xsl:value-of select="./*[@object_id='227']/fields/*[@id='484']/value"></xsl:value-of>.jpg&amp;45</xsl:attribute>
		</img>
		<img style="float: left;"  alt="">
			<xsl:attribute name="src">/showfigure.php?filename=c45x41y_<xsl:value-of select="./*[@object_id='228']/fields/*[@id='484']/value"></xsl:value-of>.jpg&amp;45</xsl:attribute>
		</img>
	</xsl:template>
	
	<!-- Plate type 4 image preview -->
	<xsl:template match="*[@object_id='234']" mode="singleFigSmallPreview">
		<img style="float: left;"  alt="">
			<xsl:attribute name="src">/showfigure.php?filename=c90x41y_<xsl:value-of select="./*[@object_id='225']/fields/*[@id='484']/value"></xsl:value-of>.jpg&amp;45</xsl:attribute>
		</img>
		<img style="float: left;"  alt="">
			<xsl:attribute name="src">/showfigure.php?filename=c90x41y_<xsl:value-of select="./*[@object_id='226']/fields/*[@id='484']/value"></xsl:value-of>.jpg&amp;45</xsl:attribute>
		</img>
		<img style="float: left;"  alt="">
			<xsl:attribute name="src">/showfigure.php?filename=c90x41y_<xsl:value-of select="./*[@object_id='227']/fields/*[@id='484']/value"></xsl:value-of>.jpg&amp;45</xsl:attribute>
		</img>
		<img style="float: left;"  alt="">
			<xsl:attribute name="src">/showfigure.php?filename=c90x41y_<xsl:value-of select="./*[@object_id='228']/fields/*[@id='484']/value"></xsl:value-of>.jpg&amp;45</xsl:attribute>
		</img>
		<img style="float: left;"  alt="">
			<xsl:attribute name="src">/showfigure.php?filename=c90x41y_<xsl:value-of select="./*[@object_id='229']/fields/*[@id='484']/value"></xsl:value-of>.jpg&amp;45</xsl:attribute>
		</img>
		<img style="float: left;"  alt="">
			<xsl:attribute name="src">/showfigure.php?filename=c90x41y_<xsl:value-of select="./*[@object_id='230']/fields/*[@id='484']/value"></xsl:value-of>.jpg&amp;45</xsl:attribute>
		</img>
	</xsl:template>



</xsl:stylesheet>