<?php
	$lXml = '<?xml version="1.0" encoding="UTF-8"?><!DOCTYPE PUBLIC "-//NLM//DTD Journal Publishing DTD v3.0 20080202//EN" "http://pmt.pensoft.eu/lib/publishing/tax-treatment-NS0.dtd">
<article dtd-version="3.0" xml:lang="en" xmlns:xml="http://www.w3.org/XML/1998/namespace" xmlns:mml="http://www.w3.org/1998/Math/MathML" xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:tp="http://www.plazi.org/taxpub">asd</article>';
	$lDOM = new DOMDocument("1.0");	
	$lDOM->preserveWhiteSpace = true;
	$lDOM->resolveExternals = true;
	if (!$lDOM->loadXML($lXml)){		
		echo 'Could not load xml';
	}
	echo 'Xml loaded successfully';

?>