<?php

$gTemplArr = array(
	'global.empty' => '',

	'global.htmlonlyheader' => '
	<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
	<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="bg" lang="bg">	
		<head>
			<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
			<title>PENSOFT</title>
			<meta name="description" content="" />
			<meta name="keywords" content="" />
			<meta name="author" content="Etaligent.net" />

			<meta name="distribution" content="global" />
			<meta name="robots" content="index, follow, all" />
			<link rel="SHORTCUT ICON" href="/favicon.ico" />
			<link type="text/css" rel="stylesheet" href="/lib/ext_details.css" media="all" title="default" />		
			<script type="text/javascript" src="/lib/ajaxObjectsDescriptor.js"></script>
			<script type="text/javascript" src="/lib/def.js"></script>
			<script type="text/javascript" src="/lib/jquery.js"></script>
		</head>
		<body>			
	',
	
	'global.googleMapHeader' => '
	<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="bg" lang="bg">	
		<head>
			<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
			<title>PENSOFT</title>
			<meta name="description" content="" />
			<meta name="keywords" content="" />
			<meta name="author" content="Etaligent.net" />
			<meta name="distribution" content="global" />
			<meta name="robots" content="index, follow, all" />
			<link rel="SHORTCUT ICON" href="/favicon.ico" />			
			<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false"></script>
			<script type="text/javascript" src="/lib/gmaps.js"></script>			
		</head>
		<body>			
	',
	
	'global.htmlonlyfooter' => '
		<!-- Start of StatCounter Code -->
		<script type="text/javascript">
		var sc_project=6343573;
		var sc_invisible=1;
		var sc_security="9cbab243";
		</script>

		<script type="text/javascript"
		src="http://www.statcounter.com/counter/counter.js"></script><noscript><div
		class="statcounter"><a title="hits counter"
		href="http://statcounter.com/free_hit_counter.html"
		target="_blank"><img class="statcounter"
		src="http://c.statcounter.com/6343573/0/9cbab243/1/"
		alt="hits counter"></a></div></noscript>
		<!-- End of StatCounter Code -->	
	</body>
	</html>
	',
	
	'global.googleMapFooter' => '
	</body>
	</html>
	',
	
	'global.metadata' => '
			<title>{pagetitle}</title>
			<meta name="description" content="{description}" />
			<meta name="keywords" content="{keywords}" />
	',
	
	'global.htmlstartcontent' => '
	{*global.htmlonlyheader}
		<div class="wrapper">
	',
	
	'global.htmlendcontent' => '
		</div>
	{*global.htmlonlyfooter}
	',
	
	'global.topmenuheader' => '
	<div id="btmenu" class="btmenu">
	<ul>
	',
	'global.topmenurowtempl0' => '
		<li><a href="{href}">{name}</a></li>
	',
	'global.topmenurowtempl1' => '
		<li><ul>{&}</ul><a href="{href}">{name}</a></li>
	',
	'global.topmenufooter' => '
	</ul>
	</div>
	',
	
	'global.system_msg' => '
		<h2>System message</h2>
		{msg}
	',
	
	'global.externaldetails' => '
	{*global.htmlstartcontent}
		{leftcol}
		{rightcol}
	{*global.htmlendcontent}
	',
	
	'global.externalLink' => '
	{*global.htmlonlyheader}
		{content}
	{*global.htmlonlyfooter}
	',
	
	'global.externalFrameset' => '
		<html>
		{content}
		</html>
	',
	
	'global.googleMap' => '
	{*global.googleMapHeader}
		{content}
	{*global.googleMapFooter}
	',
	
	'global.taxonMenu' => '
		{content}
	',
	
	'global.simplepage' => '
	{*global.htmlstartcontent}
	{rubriki}<td><p>{loginform}</p>{contents}</td>
	{*global.htmlendcontent}
	',
);
?>