<?php
	$lXml = '<?xml version="1.0" encoding="UTF-8"?><!DOCTYPE article PUBLIC "-//NLM//DTD Journal Publishing DTD v3.0 20080202//EN" "http://pmt.pensoft.eu/lib/publishing/tax-treatment-NS0.dtd">
<article dtd-version="3.0">asd</article>';
	$lDOM = new DOMDocument("1.0");	
	$lDOM->preserveWhiteSpace = true;
	$lDOM->resolveExternals = true;
	if (!$lDOM->loadXML($lXml)){		
		echo 'Could not load xml';
	}
	echo 'Xml loaded successfully';

?>