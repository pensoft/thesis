<?php

function executeExternalQuery($pURL, $pPostFields = false, $pCBF = '', $pTimeout = 30, $pPassSessionCookie = false){
	$lCurlHandler = curl_init($pURL);
	$lUserAgent = "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.1) Gecko/20061204 Firefox/3.5.0.1";
	curl_setopt($lCurlHandler, CURLOPT_USERAGENT, $lUserAgent);
	curl_setopt($lCurlHandler, CURLOPT_HEADER, 0);
	curl_setopt($lCurlHandler, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($lCurlHandler, CURLOPT_FOLLOWLOCATION, 1);
	curl_setopt($lCurlHandler, CURLOPT_MAXREDIRS, 3);
	curl_setopt($lCurlHandler, CURLOPT_TIMEOUT, $pTimeout);
	//~ curl_setopt($lCurlHandler, CURLOPT_BUFFERSIZE, 512);

	if ($pCBF) {
		curl_setopt($lCurlHandler, CURLOPT_WRITEFUNCTION, $pCBF);
	}

	if($pPassSessionCookie){
		session_write_close();
		$strCookie = 'PHPSESSID=' . $_COOKIE['PHPSESSID'] . '; path=/';
		curl_setopt( $lCurlHandler, CURLOPT_COOKIE, $strCookie );
	}

	if(is_array($pPostFields)){
		curl_setopt($lCurlHandler, CURLOPT_POST, 1);
		curl_setopt($lCurlHandler, CURLOPT_POSTFIELDS, $pPostFields);
	}
	$lResult = curl_exec($lCurlHandler);
	//~ if ($_SERVER['REMOTE_ADDR'] == '193.194.140.198') {
		//~ echo $gBHLData . "!!!!" . $this->m_bulk_xml;
		//~ echo "!!!" . curl_error($lCurlHandler) . "!!!";
	//~ }
	curl_close($lCurlHandler);
	//~ var_dump($lResult);
	return $lResult;
}

function ParseTaxonExternalLink($pTaxonName, $pLink, $pAddTaxonNameToEnd = false, $pPostForm = false, $pPostFields = false){
	$pTaxonName = rawurlencode($pTaxonName);
	$lLink = TAXON_EXTERNAL_LINK_BASE_LINK . '?taxon_name=' . $pTaxonName . '&url=' . rawurlencode($pLink);
	if( $pAddTaxonNameToEnd )
		$lLink .= $pTaxonName;
	if( $pPostForm )
		$lLink .= '&postform=1';
	if( $pPostFields )
		$lLink .= '&postfields=' . rawurlencode($pPostFields);
	return  $lLink;
}

/**
 * Insert the comments xml in the document xml
 * @param unknown_type $pDocumentXml
 * @param unknown_type $pComments
 */
function InsertCommentsInDocumentXml($pDocumentXml, $pComments){
	require_once PATH_CLASSES . 'diff.php';
	if(!is_array($pComments) || !count(!$pComments)){
		return $pDocumentXml;
	}

	$lXmlDom = new DOMDocument('1.0', DEFAULT_XML_ENCODING);
	if(!$lXmlDom->loadXML($pDocumentXml)){
		return $pDocumentXml;
	}

	$lCommentsHolderNode = $lXmlDom->documentElement->appendChild($lXmlDom->createElement('comments'));
	foreach ($pComments as $lCurrentComment){
		$lCommentNode = $lCommentsHolderNode->appendChild($lXmlDom->createElement('comment'));
		$lCommentNode->setAttribute('id', (int)$lCurrentComment['id']);
		$lMsgNode = $lCommentNode->appendChild($lXmlDom->createElement('msg'));
		$lMsgNode->appendChild($lXmlDom->createTextNode(CustomHtmlEntitiesDecode($lCurrentComment['msg'])));
		$lRootIdNode = $lCommentNode->appendChild($lXmlDom->createElement('rootid', (int)$lCurrentComment['rootid']));
		$lUsrIdNode = $lCommentNode->appendChild($lXmlDom->createElement('usr_id', (int)$lCurrentComment['usr_id']));
		$lFlagsNode = $lCommentNode->appendChild($lXmlDom->createElement('flags', (int)$lCurrentComment['flags']));
		$lCreateDateNode = $lCommentNode->appendChild($lXmlDom->createElement('createdate'));
		$lCreateDateNode->appendChild($lXmlDom->createTextNode(CustomHtmlEntitiesDecode($lCurrentComment['mdate'])));
		
		$lPosNode = $lCommentNode->appendChild($lXmlDom->createElement('pos'));
		$lPosNode->appendChild($lXmlDom->createTextNode(CustomHtmlEntitiesDecode($lCurrentComment['ord'])));

		$lIsResolvedNode = $lCommentNode->appendChild($lXmlDom->createElement('is_resolved', (int)$lCurrentComment['is_resolved']));
		$lResolveUidNode = $lCommentNode->appendChild($lXmlDom->createElement('resolve_uid', (int)$lCurrentComment['resolve_uid']));		
		$lResolveDateNode = $lCommentNode->appendChild($lXmlDom->createElement('resolve_date'));
		$lResolveDateNode->appendChild($lXmlDom->createTextNode(CustomHtmlEntitiesDecode($lCurrentComment['resolve_date'])));

		$lStartInstanceIdNode = $lCommentNode->appendChild($lXmlDom->createElement('start_instance_id', (int)$lCurrentComment['start_instance_id']));
		$lStartFieldIdNode = $lCommentNode->appendChild($lXmlDom->createElement('start_field_id', (int)$lCurrentComment['start_field_id']));
		$lStartOffsetNode = $lCommentNode->appendChild($lXmlDom->createElement('start_offset', (int)$lCurrentComment['start_offset']));

		$lEndInstanceIdNode = $lCommentNode->appendChild($lXmlDom->createElement('end_instance_id', (int)$lCurrentComment['end_instance_id']));
		$lEndFieldIdNode = $lCommentNode->appendChild($lXmlDom->createElement('end_field_id', (int)$lCurrentComment['end_field_id']));
		$lEndOffsetNode = $lCommentNode->appendChild($lXmlDom->createElement('end_offset', (int)$lCurrentComment['end_offset']));

		$lIsDisclosedNode = $lCommentNode->appendChild($lXmlDom->createElement('is_disclosed', (int)$lCurrentComment['is_disclosed']));
		$lUndisclosedUidNode = $lCommentNode->appendChild($lXmlDom->createElement('undisclosed_usr_id', (int)$lCurrentComment['undisclosed_usr_id']));
	}


	$lXmlDom->encoding = DEFAULT_XML_ENCODING;
	return $lXmlDom->saveXML();
}


function GetLinksArray($pTaxonName, $pScrapSomeLinks = true){
	$pEncodedTaxonName = rawurlencode($pTaxonName);
	//~ var_dump($pTaxonName);
	//~ var_dump($pEncodedTaxonName);
	//~ echo '<br/>';
	$lResult = array(
		'gbif' => array(
			'picsrc' => '/img/ext_details/gbif_logo.jpg',
			'default_picsrc' => '/img/ext_details/gbif_logo_BW.jpg',
			'title' => 'Global Biodiversity Information Facility',
			'isubio' => false,
			'ubio_title' => '',
			'show_if_not_found' => true,
			'href' => 'http://data.gbif.org/search/' . $pEncodedTaxonName,
			'default_href' => 'http://data.gbif.org/search/' . $pEncodedTaxonName,
			'postform' => false,
			'default_postform' => false,
		),
		'eol' => array(
			'picsrc' => '/img/ext_details/eol_logo.jpg',
			'default_picsrc' => '/img/ext_details/eol_logo_BW.jpg',
			'title' => 'Encyclopedia of Life',
			'isubio' => false,
			'ubio_title' => '',
			'href' => 'http://www.eol.org/search?q=' . $pEncodedTaxonName,
			'default_href' => 'http://www.eol.org/search?q=' . $pEncodedTaxonName,
			'show_if_not_found' => true,
			'postform' => false,
			'default_postform' => false,
		),
		'col' => array(
			'picsrc' => '/img/ext_details/col_logo.jpg',
			'default_picsrc' => '/img/ext_details/col_logo_BW.jpg',
			'title' => 'Catalogue of Life',
			'isubio' => false,
			'ubio_title' => UBIO_LINK_CATALOGOFLIFE_TITLE,
			'href' => 'http://www.catalogueoflife.org/search/all/key/' . $pEncodedTaxonName,
			'default_href' => 'http://www.catalogueoflife.org/search/all/key/' . $pEncodedTaxonName,
			'show_if_not_found' => true,
			'postform' => false,
			'default_postform' => false,
		),
		'itis' => array(
			'picsrc' => '/img/ext_details/itis_logo.jpg',
			'default_picsrc' => '/img/ext_details/itis_logo_BW.jpg',
			'title' => 'Integrated Taxonomic Information System',
			'isubio' => true,
			'ubio_title' => UBIO_LINK_ITIS_TITLE,
			'href' => 'http://www.itis.gov/servlet/SingleRpt/SingleRpt',
			'postfields' => 'search_topic=all&search_kingdom=every&search_span=containing&categories=All&source=html&search_credRating=All&search_value=' . $pTaxonName,
			'default_href' => 'http://www.itis.gov/servlet/SingleRpt/SingleRpt',
			'default_postfields' => 'search_topic=all&search_kingdom=every&search_span=containing&categories=All&source=html&search_credRating=All&search_value=' . $pTaxonName,
			'show_if_not_found' => true,
			'postform' => true,
			'default_postform' => true,
		),
		'species2000' => array(
			'picsrc' => '/img/ext_details/species2000_logo.jpg',
			'default_picsrc' => '/img/ext_details/species2000_logo_BW.jpg',
			'title' => 'Species 2000',
			'isubio' => false,
			'ubio_title' => '',
			'href' => 'http://www.sp2000.org/index.php?option=com_search&Itemid=99999999&submit=Search&searchphrase=any&ordering=newest&searchword=' . $pEncodedTaxonName,
			'default_href' => 'http://www.sp2000.org/index.php?option=com_search&Itemid=99999999&submit=Search&searchphrase=any&ordering=newest&searchword=' . $pEncodedTaxonName,
			'show_if_not_found' => true,
			'postform' => false,
			'default_postform' => false,
		),
		'fa' => array(
			'picsrc' => '/img/ext_details/fa_logo.jpg',
			'default_picsrc' => '/img/ext_details/fa_logo_BW.jpg',
			'title' => 'Fauna Europaea',
			'isubio' => false,
			'ubio_title' => '',
			'href' => 'http://www.faunaeur.org',
			'default_href' => 'http://www.faunaeur.org',
			'show_if_not_found' => false,
			'postform' => false,
			'default_postform' => false,
		),
		'worms' => array(
			'picsrc' => '/img/ext_details/worms_logo.jpg',
			'default_picsrc' => '/img/ext_details/worms_logo_BW.jpg',
			'title' => 'World Register of Marine Species',
			'isubio' => false,
			'ubio_title' => '',
			'href' => 'http://www.marinespecies.org/aphia.php?p=taxlist',
			'default_href' => 'http://www.marinespecies.org/aphia.php?p=taxlist',
			'postfields' => 'tComp=contains&searchpar=0&action=search&rSkips=0&marine=1&tName=' . $pTaxonName,
			'default_postfields' => 'tComp=contains&searchpar=0&action=search&rSkips=0&marine=1&tName=' . $pTaxonName,
			'show_if_not_found' => true,
			'postform' => true,
			'default_postform' => true,
		),
		'wikipedia' => array(
			'picsrc' => '/img/ext_details/wiki_logo.jpg',
			'default_picsrc' => '/img/ext_details/wiki_logo_BW.jpg',
			'title' => 'Wikipedia',
			'isubio' => false,
			'ubio_title' => '',
			'href' => 'http://en.wikipedia.org/w/index.php?search=' . $pEncodedTaxonName,
			'default_href' => 'http://en.wikipedia.org/w/index.php?search=' . $pEncodedTaxonName,
			'show_if_not_found' => true,
			'postform' => false,
			'default_postform' => false,
		),
		'wikispecies' => array(
			'picsrc' => '/img/ext_details/wikispecies_logo.gif',
			'default_picsrc' => '/img/ext_details/wikispecies_logo_BW.gif',
			'title' => 'Wikispecies',
			'isubio' => false,
			'ubio_title' => '',
			'href' => 'http://species.wikimedia.org/wiki/' . $pEncodedTaxonName,
			'default_href' => 'http://species.wikimedia.org/wiki/' . $pEncodedTaxonName,
			'show_if_not_found' => true,
			'postform' => false,
			'default_postform' => false,
		),
		'iucn' => array(
			'picsrc' => '/img/ext_details/iucn_logo.jpg',
			'default_picsrc' => '/img/ext_details/iucn_logo_BW.jpg',
			'title' => 'IUCN',
			'isubio' => false,
			'ubio_title' => '',
			'href' => 'http://iucn.org/search.cfm?uSearchTerm=' . $pEncodedTaxonName,
			'default_href' => 'http://iucn.org/search.cfm?uSearchTerm=' . $pEncodedTaxonName,
			'show_if_not_found' => true,
			'postform' => false,
			'default_postform' => false,
		),
		'fungorum' => array(
			'picsrc' => '/img/ext_details/fungorum_logo.jpg',
			'default_picsrc' => '/img/ext_details/fungorum_logo_BW.jpg',
			'title' => 'Index Fungorum',
			'isubio' => true,
			'ubio_title' => UBIO_LINK_INDEXFUNGORUM_TITLE,
			'href' => 'http://www.indexfungorum.org/Names/Names.asp?SearchTerm=' . $pEncodedTaxonName,
			'default_href' => 'http://www.indexfungorum.org/Names/Names.asp?SearchTerm=' . $pEncodedTaxonName,
			'show_if_not_found' => false,
			'postform' => false,
			'default_postform' => false,
		),
		'ipni' => array(
			'picsrc' => '/img/ext_details/ipni_logo.jpg',
			'default_picsrc' => '/img/ext_details/ipni_logo_BW.jpg',
			'title' => 'International Plant Name Index',
			'isubio' => false,
			'ubio_title' => '',
			'href' => 'http://www.ipni.org/ipni/simplePlantNameSearch.do?find_wholeName=' . $pEncodedTaxonName,
			'default_href' => 'http://www.ipni.org/ipni/simplePlantNameSearch.do?find_wholeName=' . $pEncodedTaxonName,
			'show_if_not_found' => false,
			'postform' => false,
			'default_postform' => false,
		),
		'algaebase' => array(
			'picsrc' => '/img/ext_details/algaebase_logo.jpg',
			'default_picsrc' => '/img/ext_details/algaebase_logo_BW.jpg',
			'title' => 'Algaebase',
			'isubio' => true,
			'ubio_title' => UBIO_LINK_ALKAEBASE_TITLE,
			'href' => 'http://www.algaebase.org/search/species/',
			'default_href' => 'http://www.algaebase.org/search/species/?currentMethod=species&fromSearch=yes&sk=0&displayCount=20&sortBy=Genus&sortBy2=Species&-Search=Search&name=' . $pTaxonName,
			'postfields' => 'currentMethod=species&fromSearch=yes&sk=0&displayCount=20&sortBy=Genus&sortBy2=Species&-Search=Search&name=' . $pTaxonName,
			'default_postfields' => 'currentMethod=species&fromSearch=yes&sk=0&displayCount=20&sortBy=Genus&sortBy2=Species&-Search=Search&name=' . $pTaxonName,
			'show_if_not_found' => false,
			'postform' => true,
			'default_postform' => true,
		),
		'tropicos' => array(
			'picsrc' => '/img/ext_details/tropicos_logo.jpg',
			'default_picsrc' => '/img/ext_details/tropicos_logo_BW.jpg',
			'title' => 'Tropicos',
			'isubio' => false,
			'ubio_title' => UBIO_LINK_TROPICOS_TITLE,
			'href' => 'http://www.tropicos.org/NameSearch.aspx?name=' . $pEncodedTaxonName,
			'default_href' => 'http://www.tropicos.org/NameSearch.aspx?name=' . $pEncodedTaxonName,
			'show_if_not_found' => false,
			'postform' => false,
			'default_postform' => false,
		),
		'usda' => array(
			'picsrc' => '/img/ext_details/usda_logo.jpg',
			'default_picsrc' => '/img/ext_details/usda_logo_BW.jpg',
			'title' => 'PLANTS Database',
			'isubio' => true,
			'ubio_title' => UBIO_LINK_USDAPLANTS_TITLE,
			'href' => 'http://www.plants.usda.gov/java/nameSearch?mode=sciname&submit.x=10&submit.y=4&keywordquery=' . $pEncodedTaxonName,
			'default_href' => 'http://www.plants.usda.gov/java/nameSearch?mode=sciname&submit.x=10&submit.y=4&keywordquery=' . $pEncodedTaxonName,
			'show_if_not_found' => false,
			'postform' => false,
			'default_postform' => false,
		),
		'gymnosperm' => array(
			'picsrc' => '/img/ext_details/gymnosperm_logo.jpg',
			'default_picsrc' => '/img/ext_details/gymnosperm_logo_BW.jpg',
			'title' => 'The Gymnosperm Database',
			'isubio' => false,
			'ubio_title' => '',
			'href' => 'http://www.google.com/custom?ie=UTF-8&oe=UTF-8&cof=S%3Ahttp%3A%2F%2Fwww.conifers.org%2F%3BAH%3Acenter%3BL%3Ahttp%3A%2F%2Fwww.conifers.org%2Fzz%2Fgymn2.gif%3B&domains=conifers.org&sitesearch=conifers.org&&sitesearch=http%3A%2F%2Fwww.conifers.org&sa=Search+this+site&q=' . $pEncodedTaxonName,
			'default_href' => 'http://www.google.com/custom?ie=UTF-8&oe=UTF-8&cof=S%3Ahttp%3A%2F%2Fwww.conifers.org%2F%3BAH%3Acenter%3BL%3Ahttp%3A%2F%2Fwww.conifers.org%2Fzz%2Fgymn2.gif%3B&domains=conifers.org&sitesearch=conifers.org&&sitesearch=http%3A%2F%2Fwww.conifers.org&sa=Search+this+site&q=' . $pEncodedTaxonName,
			'show_if_not_found' => false,
			'postform' => false,
			'default_postform' => false,
		),
		'zoobank' => array(
			'picsrc' => '/img/ext_details/zoobank_logo.jpg',
			'default_picsrc' => '/img/ext_details/zoobank_logo_BW.jpg',
			'title' => 'ZooBank',
			'isubio' => false,
			'ubio_title' => '',
			'href' => 'http://www.zoobank.org/Search.aspx?search=' . $pEncodedTaxonName,
			'default_href' => 'http://www.zoobank.org/Search.aspx?search=' . $pEncodedTaxonName,
			'show_if_not_found' => false,
			'postform' => false,
			'default_postform' => false,
		),
		'tol' => array(
			'picsrc' => '/img/ext_details/tol_logo.jpg',
			'default_picsrc' => '/img/ext_details/tol_logo_BW.jpg',
			'title' => 'Tree of Life',
			'isubio' => true,
			'ubio_title' => UBIO_LINK_TOL_TITLE,
			'href' => 'http://tolweb.org/tree/home.pages/searchresults.html?cx=009557456284541951685%3A50nf_5tpvuq&cof=FORID%3A9&ie=UTF-8&sa=Search&q=' . $pEncodedTaxonName,
			'default_href' => 'http://tolweb.org/tree/home.pages/searchresults.html?cx=009557456284541951685%3A50nf_5tpvuq&cof=FORID%3A9&ie=UTF-8&sa=Search&q=' . $pEncodedTaxonName,
			'show_if_not_found' => false,
			'postform' => false,
			'default_postform' => false,
		),
		'treebase' => array(
			'picsrc' => '/img/ext_details/treebase_logo.jpg',
			'default_picsrc' => '/img/ext_details/treebase_logo_BW.jpg',
			'title' => 'TreeBase',
			'isubio' => true,
			'ubio_title' => UBIO_LINK_TREEBASE_TITLE,
			'href' => 'http://www.treebase.org/treebase-web/search/studySearch.html',
			'default_href' => 'http://www.treebase.org/treebase-web/search/studySearch.html',
			'postfields' => 'formName=searchKeyword&searchButton=textKeyword&query=&searchTerm=' . $pTaxonName,
			'default_postfields' => 'formName=searchKeyword&searchButton=textKeyword&query=&searchTerm=' . $pTaxonName,
			'show_if_not_found' => false,
			'postform' => true,
			'default_postform' => true,
		),
		'landcare' => array(
			'picsrc' => '/img/ext_details/landcare_logo.jpg',
			'default_picsrc' => '/img/ext_details/landcare_logo_BW.jpg',
			'title' => 'Landcare Research',
			'isubio' => true,
			'ubio_title' => UBIO_LINK_LANDCARE_TITLE,
			'default_href' => 'http://www.landcareresearch.co.nz/search/search.asp?zoom_cat=-1&Submit=GO&zoom_query=' . $pEncodedTaxonName,
			'show_if_not_found' => false,
			'postform' => false,
			'default_postform' => false,
		),
		'chilobase' => array(
			'picsrc' => '/img/ext_details/chilobase_logo.jpg',
			'default_picsrc' => '/img/ext_details/chilobase_logo_BW.jpg',
			'title' => 'Chilobase',
			'isubio' => false,
			'ubio_title' => '',
			'href' => 'http://chilobase.bio.unipd.it/',
			'default_href' => 'http://chilobase.bio.unipd.it/',
			'postfields' => 'TYPE=beginning+with&WORDS=' . $pTaxonName,
			'default_postfields' => 'TYPE=beginning+with&WORDS=' . $pTaxonName,
			'show_if_not_found' => false,
			'postform' => true,
			'default_postform' => true,
		),
		'hymenopterans' => array(
			'picsrc' => '/img/ext_details/hymenoptera.gif',
			'default_picsrc' => '/img/ext_details/hymenoptera_BW.gif',
			'title' => 'Hymenoptera Name Server',
			'isubio' => false,
			'ubio_title' => '',
			'href' => 'http://osuc.biosci.ohio-state.edu/hymDB/nomenclator.name_entry?Submit=Submit+Query&text_entry=' . $pEncodedTaxonName,
			'default_href' => 'http://osuc.biosci.ohio-state.edu/hymDB/nomenclator.name_entry?Submit=Submit+Query&text_entry=' . $pEncodedTaxonName,
			'show_if_not_found' => false,
			'postform' => false,
			'default_postform' => false,
		),
		'fishbase' => array(
			'picsrc' => '/img/ext_details/fishbase_logo.jpg',
			'default_picsrc' => '/img/ext_details/fishbase_logo_BW.jpg',
			'title' => 'FishBase',
			'isubio' => false,
			'ubio_title' => '',
			'href' => 'http://www.fishbase.org/NomenClature/ScientificNameSearchList.php',
			'default_href' => 'http://www.fishbase.org/NomenClature/ScientificNameSearchList.php',
			'postfields' => 'Language=English&crit1_fieldname=SYNONYMS.SynGenus&crit1_fieldtype=CHAR&crit2_fieldname=SYNONYMS.SynSpecies&crit2_fieldtype=CHAR&crit1_operator=EQUAL&crit1_value=&crit2_operator=EQUAL&crit2_value=&gs=' . $pTaxonName . '&group=summary',
			'default_postfields' => 'Language=English&crit1_fieldname=SYNONYMS.SynGenus&crit1_fieldtype=CHAR&crit2_fieldname=SYNONYMS.SynSpecies&crit2_fieldtype=CHAR&crit1_operator=EQUAL&crit1_value=&crit2_operator=EQUAL&crit2_value=&gs=' . $pTaxonName . '&group=summary',
			//~ 'dont_check_for_existence' => true,
			'show_if_not_found' => false,
			'postform' => true,
			'default_postform' => true,
		),
		'ncbi' => array(
			'picsrc' => '/img/ext_details/ncbi_logo.jpg',
			'default_picsrc' => '/img/ext_details/ncbi_logo_BW.jpg',
			'title' => 'National Center for Biotechnology Information',
			'isubio' => true,
			'ubio_title' => UBIO_LINK_NCBI_TITLE,
			'href' => 'http://www.ncbi.nlm.nih.gov/gquery/?term=' . $pEncodedTaxonName,
			'default_href' => 'http://www.ncbi.nlm.nih.gov/gquery/?term=' . $pEncodedTaxonName,
			'show_if_not_found' => true,
			'postform' => false,
			'default_postform' => false,
		),
		'bold' => array(
			'picsrc' => '/img/ext_details/bold_logo.jpg',
			'default_picsrc' => '/img/ext_details/bold_logo_BW.jpg',
			'title' => 'Barcode of Life',
			'isubio' => false,
			'ubio_title' => '',
			'href' => 'http://boldsystems.org/views/taxbrowser.php?taxon=' . $pEncodedTaxonName,
			'default_href' => 'http://boldsystems.org/views/taxbrowser.php?taxon=' . $pEncodedTaxonName,
			'show_if_not_found' => true,
			'postform' => false,
			'default_postform' => false,
		),
		'morphbank' => array(
			'picsrc' => '/img/ext_details/morphbank_logo.jpg',
			'default_picsrc' => '/img/ext_details/morphbank_logo_BW.jpg',
			'title' => 'Morphbank',
			'isubio' => false,
			'ubio_title' => '',
			'href' => 'http://www.morphbank.net/MyManager/?keywords=' . $pEncodedTaxonName,
			'default_href' => 'http://www.morphbank.net/MyManager/?keywords=' . $pEncodedTaxonName,
			'show_if_not_found' => true,
			'postform' => false,
			'default_postform' => false,
		),
		'wikimedia' => array(
			'picsrc' => '/img/ext_details/wikimedia_logo.jpg',
			'default_picsrc' => '/img/ext_details/wikimedia_logo_BW.jpg',
			'title' => 'Wikimedia',
			'isubio' => false,
			'ubio_title' => '',
			'default_href' => 'http://commons.wikimedia.org/wiki/' . $pEncodedTaxonName,
			'href' => 'http://commons.wikimedia.org/wiki/' . $pEncodedTaxonName,
			'show_if_not_found' => true,
			'postform' => false,
			'default_postform' => false,
		),
		'yahoo_images' => array(
			'picsrc' => '/img/ext_details/yahoo_logo.jpg',
			'default_picsrc' => '/img/ext_details/yahoo_logo_BW.jpg',
			'title' => 'Yahoo',
			'isubio' => false,
			'ubio_title' => '',
			'href' => 'http://images.search.yahoo.com/search/images;_ylt=A0WTb_moq.5LsmgAdzuLuLkF?ei=utf-8&iscqry=&fr=sfp&p=' . $pEncodedTaxonName,
			'default_href' => 'http://images.search.yahoo.com/search/images;_ylt=A0WTb_moq.5LsmgAdzuLuLkF?ei=utf-8&iscqry=&fr=sfp&p=' . $pEncodedTaxonName,
			'show_if_not_found' => true,
			'postform' => false,
			'default_postform' => false,
		),
		'google_images' => array(
			'picsrc' => '/img/ext_details/google_logo.jpg',
			'default_picsrc' => '/img/ext_details/google_logo_BW.jpg',
			'title' => 'Google',
			'isubio' => false,
			'ubio_title' => '',
			'href' => 'http://www.google.com/images?hl=en&source=imghp&gbv=2&aq=f&aqi=g2&aql=&oq=&gs_rfai=&q=' . $pEncodedTaxonName,
			'default_href' => 'http://www.google.com/images?hl=en&source=imghp&gbv=2&aq=f&aqi=g2&aql=&oq=&gs_rfai=&q=' . $pEncodedTaxonName,
			'show_if_not_found' => false,
			'postform' => false,
			'default_postform' => false,
		),
		'google_scholar' => array(
			'picsrc' => '/img/ext_details/google_logo.jpg',
			'default_picsrc' => '/img/ext_details/google_logo_BW.jpg',
			'title' => 'GoogleScholar',
			'isubio' => false,
			'ubio_title' => '',
			'href' => 'http://scholar.google.com/scholar?q=' . $pEncodedTaxonName,
			'default_href' => 'http://scholar.google.com/scholar?q=' . $pEncodedTaxonName,
			'show_if_not_found' => true,
			'postform' => false,
			'default_postform' => false,
		),
		'pubmed' => array(
			'picsrc' => '/img/ext_details/pubmed_logo.jpg',
			'default_picsrc' => '/img/ext_details/pubmed_logo_BW.jpg',
			'title' => 'PubMed',
			'isubio' => false,
			'ubio_title' => '',
			'href' => NCBI_SUBTREE_LINK . '&db=' . EUTILS_PUBMED_DB . '&term=' . rawurlencode(ParsePubmedTaxonName($pTaxonName)),
			'default_href' => NCBI_SUBTREE_LINK . '&db=' . EUTILS_PUBMED_DB . '&term=' . rawurlencode(ParsePubmedTaxonName($pTaxonName)),
			'show_if_not_found' => true,
			'postform' => false,
			'default_postform' => false,
		),
		'biodev' => array(
			'picsrc' => '/img/ext_details/biodev_logo.jpg',
			'default_picsrc' => '/img/ext_details/biodev_logo_BW.jpg',
			'title' => 'Biodiversity Heritage Library',
			'isubio' => false,
			'ubio_title' => '',
			'href' => 'http://www.biodiversitylibrary.org/Search.aspx?searchCat=&searchTerm=' . $pEncodedTaxonName,
			'default_href' => 'http://www.biodiversitylibrary.org/Search.aspx?searchCat=&searchTerm=' . $pEncodedTaxonName,
			'show_if_not_found' => true,
			'postform' => false,
			'default_postform' => false,
		),
		'biolib' => array(
			'picsrc' => '/img/ext_details/biolib_logo.jpg',
			'default_picsrc' => '/img/ext_details/biolib_logo_BW.jpg',
			'title' => 'BioLib',
			'isubio' => false,
			'ubio_title' => UBIO_LINK_BIOLIB_TITLE,
			'href' => 'http://www.biolib.cz/en/formsearch/?action=execute&string=' . $pEncodedTaxonName . '&searchtype=2&searchrecords=1&searchsynonyms=1&searchvnames=1&selecttaxonid=null&taxonid=',
			'href' => 'http://www.biolib.cz/en/formsearch/?action=execute&string=' . $pEncodedTaxonName . '&searchtype=2&searchrecords=1&searchsynonyms=1&searchvnames=1&selecttaxonid=null&taxonid=',
			'show_if_not_found' => true,
			'postform' => false,
			'default_postform' => false,
		),
		'ubio' => array(
			'picsrc' => '/img/ext_details/ubio_logo.jpg',
			'default_picsrc' => '/img/ext_details/ubio_logo_BW.jpg',
			'title' => 'uBio',
			'isubio' => false,
			'ubio_title' => '',
			'href' => 'http://www.ubio.org/browser/search.php?search_all=' . $pEncodedTaxonName,
			'default_href' => 'http://www.ubio.org/browser/search.php?search_all=' . $pEncodedTaxonName,
			'show_if_not_found' => false,
			'postform' => false,
			'default_postform' => false,
		),
		'plazi' => array(
			'picsrc' => '/img/ext_details/plazi_logo.jpg',
			'default_picsrc' => '/img/ext_details/plazi_logo_BW.jpg',
			'title' => 'Plazi',
			'isubio' => false,
			'href' => 'http://plazi.org:8080/GgSRS/search?taxonomicName.isNomenclature=isNomenclature&taxonomicName.exactMatch=exactMatch&taxonomicName.taxonomicName=' . $pEncodedTaxonName,
			'default_href' => 'http://plazi.org:8080/GgSRS/search?taxonomicName.isNomenclature=isNomenclature&taxonomicName.exactMatch=exactMatch&taxonomicName.taxonomicName=' . $pEncodedTaxonName,
			'show_if_not_found' => false,
			'postform' => false,
			'default_postform' => false,
		),
		'daisie' => array(
			'picsrc' => '/img/ext_details/daisie_logo.jpg',
			'default_picsrc' => '/img/ext_details/daisie_logo_BW.jpg',
			'title' => 'DAISIE',
			'isubio' => false,
			'check_url' => 'http://www.europe-aliens.org/speciesSearchResults.do?speciesPhrase=' . $pEncodedTaxonName,
			'href' => 'http://www.europe-aliens.org/speciesSearch.do?speciesPhrase=' . $pEncodedTaxonName,
			'default_href' => 'http://www.europe-aliens.org/speciesSearch.do?speciesPhrase=' . $pEncodedTaxonName,
			'show_if_not_found' => true,
			'check_postform' => false,
			'postform' => false,
			'default_postform' =>false,
		),
		'invasive' => array(
			'picsrc' => '/img/ext_details/invasive_logo.png',
			'default_picsrc' => '/img/ext_details/invasive_logo_BW.png',
			'title' => 'Invasive.org',
			'isubio' => false,
			'check_url' => 'http://www.google.com/cse?cx=004971884014326696348%3Alwck86z8tsg&ie=UTF-8&cof=FORID%3A10&q=' . $pEncodedTaxonName . '&sa=GO&siteurl=www.invasive.org%252Findex.cfm&ad=w9&num=10&rurl=http%3A%2F%2Fwww.invasive.org%2Fsearch%2Fresults.cfm%3Fcx%3D004971884014326696348%253Alwck86z8tsg%26ie%3DUTF-8%26cof%3DFORID%253A10%26ie%3DUTF-8%26q%3Dtestasd%26sa%3DGO%26siteurl%3Dwww.invasive.org%25252Findex.cfm',
			'href' => 'http://www.invasive.org/search/results.cfm?cx=004971884014326696348%3Alwck86z8tsg&ie=UTF-8&cof=FORID%3A10&ie=UTF-8&q=' . $pEncodedTaxonName . '&sa=GO&siteurl=www.invasive.org%252Fspecies.cfm',
			'default_href' => 'http://www.invasive.org/search/results.cfm?cx=004971884014326696348%3Alwck86z8tsg&ie=UTF-8&cof=FORID%3A10&ie=UTF-8&q=' . $pEncodedTaxonName . '&sa=GO&siteurl=www.invasive.org%252Fspecies.cfm',
			'show_if_not_found' => true,
			'check_postform' => false,
			'postform' => false,
			'default_postform' =>false,
		),
		'lias' => array(
			'picsrc' => '/img/ext_details/lias_logo.jpg',
			'default_picsrc' => '/img/ext_details/lias_logo_BW.jpg',
			'title' => 'Lias',
			'isubio' => false,
			'href' => LIAS_IFRAME_URL . $pEncodedTaxonName,
			'default_href' => LIAS_IFRAME_URL . $pEncodedTaxonName,
			'show_if_not_found' => true,
			'postform' => false,
			'default_postform' => false,
		),
		'diptera' => array(
			'picsrc' => '/img/ext_details/diptera_logo.jpg',
			'default_picsrc' => '/img/ext_details/diptera_logo_BW.jpg',
			'title' => 'Diptera',
			'isubio' => false,
			'ubio_title' => '',
			//~ 'href' => 'http://130.225.211.25//diptera/names/FMPro?-db=names.fp5&-lay=WWW+Detail&-format=resultNo.htm&-op=&Name=' . $pEncodedTaxonName . '&-op=&author=&-op=&year=&-op=&kind=&-op=&Family=&-op=&ValidName=&-sortfield=unsorted&-sortorder=ascending&-lop=or&-max=10&-find=Start+Search',

			//~ 'href' => 'http://130.225.211.25/diptera/names/FMPro',
			//~ 'default_href' => 'http://130.225.211.25/diptera/names/FMPro',
			//~ 'postfields' => '-db=names.fp5&-lay=WWW+Detail&-format=NomenclatorResult.html&-op=contains&Name=' . $pEncodedTaxonName . '&-op=&author=&-op=&year=&-op=&kind=&-op=&Family=&-op=&ValidName=&-sortfield=unsorted&-sortorder=ascending&-lop=and&-max=10&-find=Start+Search',
			//~ 'default_postfields' => '-db=names.fp5&-lay=WWW+Detail&-format=NomenclatorResult.html&-op=contains&Name=' . $pEncodedTaxonName . '&-op=&author=&-op=&year=&-op=&kind=&-op=&Family=&-op=&ValidName=&-sortfield=unsorted&-sortorder=ascending&-lop=and&-max=10&-find=Start+Search',
			//~ 'postform' => true,
			//~ 'default_postform' => true,
			'href' => 'http://130.225.211.25/diptera/names/FMPro?-db=names.fp5&-format=nomenclatorresult.html&-lay=www%20detail&-sortfield=unsorted&-op=cn&Name=' . $pEncodedTaxonName . '&-max=10&-find=&-lop=and',
			'default_href' => 'http://130.225.211.25/diptera/names/FMPro?-db=names.fp5&-format=nomenclatorresult.html&-lay=www%20detail&-sortfield=unsorted&-op=cn&Name=' . $pEncodedTaxonName . '&-max=10&-find=&-lop=and',
			'postform' => false,
			'default_postform' => false,
			'show_if_not_found' => false,
		),
	);
	if( $pScrapSomeLinks ){//Ako e ukazan tozi parametyr vednaga se tyrsqt saitove za nqkoi obekti - iz4isleniqta se bavqt
		$lGbifData = GetTaxonGbifLinkAndId($pTaxonName);
		$lResult['gbif']['dont_check_for_existence'] = true;
		$lResult['gbif']['isubio'] = false;
		if($lGbifData['id']){//Dobavqme gbif-a samo ako ima takyv rez v gbif
			$lResult['gbif']['href'] = $lGbifData['link'];
			$lResult['gbif']['results_exist'] = true;
		}else{
			$lResult['gbif']['picsrc'] = $lResult['gbif']['default_picsrc'];
			$lResult['gbif']['results_exist'] = false;
		}

		$lMorphRes = GetTaxonMorphbankResultCount($pTaxonName);
		$lResult['morphbank']['dont_check_for_existence'] = true;
		$lResult['morphbank']['isubio'] = false;
		if ((int)$lMorphRes > 0) {
			$lResult['morphbank']['results_exist'] = true;
		}else{
			$lResult['morphbank']['picsrc'] = $lResult['morphbank']['default_picsrc'];
			$lResult['morphbank']['results_exist'] = false;
		}

		$lTaxonId = GetTaxonNcbiId($pTaxonName);
		$lResult['ncbi']['isubio'] = false;
		$lResult['ncbi']['dont_check_for_existence'] = true;
		if ($lTaxonId) {
			$lResult['ncbi']['href'] = NCBI_TAXON_URL . $lTaxonId;
			$lResult['ncbi']['results_exist'] = true;
		}else{
			$lResult['ncbi']['picsrc'] = $lResult['ncbi']['default_picsrc'];
			$lResult['ncbi']['results_exist'] = false;
		}

		$lLiasRes = GetTaxonLiasResultCount($pTaxonName);
		$lResult['lias']['isubio'] = false;
		$lResult['lias']['dont_check_for_existence'] = true;
		if ((int)$lLiasRes > 0) {
			$lResult['lias']['results_exist'] = true;
		}else{
			$lResult['lias']['results_exist'] = false;
			$lResult['lias']['picsrc'] = $lResult['lias']['default_picsrc'];
		}

	}
	return $lResult;
}

function getSitesMatchArray($pTaxonName){
	//Za vseki sait pazim masiv ot RegExp-ove, kato ako matchnat vsi4ki - nqma rezultat
	$pTaxonName = preg_quote($pTaxonName, '/');
	$lResult = array(
		'gbif' => array(
			'<h9>A$A<\/h9>',
		),
		'eol' => array(
			'<h3>No\s+search\s+results\s+were\s+found<\/h3>',
		),
		'col' => array(
			'<div\s+class="results_counter"\s+align="right">\s+Records\s+found:\s+0<\/div>',
		),
		'species2000' => array(
			'Total\s*0\s*results\s*found\.\s*',
		),
		'worms' => array(
			'No\s+taxa\s+found\s+that\s+satisfy\s+the\s+criteria\s+specified\s+in\s+the\s+previous screen\.',
		),
		'wikipedia' => array(
			'There\s+were\s+no\s+results\s+matching\s+the\s+query\.',
		),
		'wikispecies' => array(
			'There\s+is\s+currently\s+no\s+text\s+in\s+this\s+page\.'
		),
		'iucn' => array(
			'<div\s*class="results"\s*>\s*No\s*results found!',
		),
		'ipni' => array(
			'((<b>No\s+Plant\s+Names\s+were\s+found\s+in\s+IPNI\s+matching\s+these\s+search\s+terms\.<\/b>)|(Full\s+name\s+is\s+invalid\.))',
		),
		'gymnosperm' => array(
			'<br>Your\s+search\s+-\s+<b>' . $pTaxonName . '<\/b>\s+-\s+did\s+not\s+match\s+any\s+documents\.\s+\s+<br>',
		),
		'zoobank' => array(
			'<span\s+id="ctl00_ContentPlaceHolder_ActResults"><h3>No\s+Matching\s+Nomenclatural\s+Acts\s+Found\.<\/h3><\/span>',
			'<span\s+id="ctl00_ContentPlaceHolder_PubResults"><h3>No\s+Matching\s+Publications\s+Found\.<\/h3><\/span>',
			'<span\s+id="ctl00_ContentPlaceHolder_AuthResults"><h3>No\s+Matching\s+Authors\s+Found\.<\/h3><\/span>',
		),
		'chilobase' => array(
			'<TR><TD\s+align=center\s+valign=top>\s+<TABLE\s+border="0"\s+cellspacing="0"\s+cellpadding="2"\s+width="100%">\s+<TR><TD\s+valign=top\s+nowrap>\s+<BR><BR><CENTER>No\s+results!<\/CENTER><BR><BR>\s+<\/TD><\/TR>\s+<\/TABLE>',
		),
		'hymenopterans' => array(
			//~ 'The\s+name\s+entered,\s+<strong>' . $pTaxonName . '<\/strong>,\s+was\s+not\s+found\s+in\s+the\s+database\.\s+Please\s+check\s+the\s+spelling.\s+We\s+would\s+appreciate\s+hearing\s+of\s+names\s+that\s+are\s+not\s+recorded\.',
			'(The\s+name\s+entered,\s+<strong>' . $pTaxonName . '<\/strong>,\s+was\s+not\s+found\s+in\s+the\s+database\.\s+Please\s+check\s+the\s+spelling.\s+We\s+would\s+appreciate\s+hearing\s+of\s+names\s+that\s+are\s+not\s+recorded\.)|(\<TABLE\s+BORDER\=1\s+BGCOLOR\="#AACCFF"\s+CELLPADDING\=5\>\s+\<TR\>\<TD\s+COLSPAN\=2\s+BGCOLOR\="#AACCFF"\s+ALIGN\=center\s+BORDER\=1\>\s+\<BR\>\s+\<TABLE\s+CELLPADDING\=5\s+border\=0\>\<TR\>\<TD\>\s+\<IMG\s+SRC\="http\:\/\/iris\.biosci\.ohio-state\.edu\/gifs\/wasp2\.gif"\>\<\/td\>\s+\<TD\>\<center\>\<H2\>\<FONT\s+color\="#006600"\>\<STRONG\>Hymenoptera\s+Name\s+Server\<\/STRONG\>\<\/FONT\>\<BR\>\s+\<FONT\s+SIZE\=-1\s+color\="#006600"\>\<em\>&nbsp;&nbsp;&nbsp;version\s+1\.5\s+&nbsp;&nbsp;&nbsp;19\.xii\.2007\<\/EM\>\<\/FONT\>\<\/center\>\<\/H2\>\<\/TD\>\<\/TR\>\<\/TABLE\>\<\/TD\>\<\/TR\>\s+\<\/TABLE\>)',
			//~ '\<TABLE\s+BORDER\=1\s+BGCOLOR\="#AACCFF"\s+CELLPADDING\=5\>\s+\<TR\>\<TD\s+COLSPAN\=2\s+BGCOLOR\="#AACCFF"\s+ALIGN\=center\s+BORDER\=1\>\s+\<BR\>\s+\<TABLE\s+CELLPADDING\=5\s+border\=0\>\<TR\>\<TD\>\s+\<IMG\s+SRC\="http\:\/\/iris\.biosci\.ohio-state\.edu\/gifs\/wasp2\.gif"\>\<\/td\>\s+\<TD\>\<center\>\<H2\>\<FONT\s+color\="#006600"\>\<STRONG\>Hymenoptera\s+Name\s+Server\<\/STRONG\>\<\/FONT\>\<BR\>\s+\<FONT\s+SIZE\=-1\s+color\="#006600"\>\<em\>&nbsp;&nbsp;&nbsp;version\s+1\.5\s+&nbsp;&nbsp;&nbsp;19\.xii\.2007\<\/EM\>\<\/FONT\>\<\/center\>\<\/H2\>\<\/TD\>\<\/TR\>\<\/TABLE\>\<\/TD\>\<\/TR\>\s+\<\/TABLE\>',
		),
		'bold' => array(
			'The\s+name\s+you\s+requested\s+could\s+not\s+be\s+found\.',
		),
		'wikimedia' => array(
			'(<div\s+class="noarticletext">\s+<p>This\s+page\s+does\s+not\s+currently\s+exist\.\s+You\s+can\s+)|' .
			'(\<p\>\<span\s+class\="plainlinks\s+nourlexpansion"\>This\s+page\s+does\s+not\s+currently\s+exist\.\s+You\s+can)',
		),
		'yahoo_images' => array(
			'<div\s+class=yschalrtz>We\s+did\s+not\s+find\s+results\s+for\s+"<strong>' . $pTaxonName . '<\/strong>"',
		),
		'google_images' => array(
			'Your\s+search\s+-\s+<b>' . $pTaxonName . '<\/b>\s+-\s+did\s+not\s+match\s+any\s+documents\.',
		),
		'google_scholar' => array(
			'Your\s+search\s+-\s+<b>' . $pTaxonName . '<\/b>\s+-\s+did\s+not\s+match\s+any\s+articles\.',
		),
		'biodev' => array(
			'<span\s+class="pageheader">Search\s+Results\s+for\s+"<span\s+id="ctl00_mainContentPlaceHolder_searchResultsLabel">' . $pTaxonName . '<\/span>"<\/span>',
			'<span\s+id="ctl00_mainContentPlaceHolder_spanTitleSummary">\s+<a\s+href="#Titles">Titles<\/a>\s+found\s+:\s+0<br\s+\/><\/span>',
			'<span\s+id="ctl00_mainContentPlaceHolder_spanAuthorSummary">\s+<a\s+href="#Authors">Authors<\/a>\s+found\s+:\s+0<br\s+\/><\/span>',
			'<span\s+id="ctl00_mainContentPlaceHolder_spanNameSummary">\s+<a\s+href="#Names">Names<\/a>\s+found\s+:\s+0<br\s+\/><\/span>',
			'<span\s+id="ctl00_mainContentPlaceHolder_spanSubjectSummary">\s+<a\s+href="#Subjects">Subjects<\/a>\s+found\s+:\s+0<br\s+\/><\/span>',

		),
		'ubio' => array(
			'Search\s+Results<\/span><br><br><br>&nbsp;&nbsp;&nbsp;&nbsp;No\s+Results\s+for\s+<b>' . $pTaxonName . '<\/b><br><br><br>',
		),
		'diptera' => array(
			'<P\s+ALIGN=CENTER><B><FONT\s+SIZE="\+1"\s+COLOR="#FFFFFF">No\s+Records\s+Found<\/FONT><\/B>',
		),
		'fishbase' => array(
			'<table\s+border="1"\s+cellpadding="1"\s+cellspacing="1"\s+align="center"\s+width="800">\s+<tr\s+bgcolor="EAF2F7"\s+class="t_header2">\s+<td\s+width="200"\s+height="25"><div\s+align="center"><strong>&nbsp;\s+Scientific\s+Name<\/strong><\/div><\/td>\s+<td\s+width="200"><div\s+align="center"><strong>&nbsp;\s+Author<\/strong><\/div><\/td>\s+<td\s+width="200"><div\s+align="center"><strong>&nbsp;\s+Valid\s+Name<\/strong><\/div><\/td>\s+<td\s+width="200"><div\s+align="center"><strong>&nbsp;\s+English\s+Name<\/strong><\/div><\/td>\s+<\/tr>\s+<\/table>\s+<table\s+border="0"\s+cellpadding="1"\s+cellspacing="1"\s+align="center"\s+width="800">\s+<tr>\s+<td\s+colspan=4\s+align="center"><span\s+class="t_value2">\s+[\s+<a\s+href="javascript:history.go(-1)">Go\s+Back<\/a>\s+]\s+[\s+<a\s+href="search.php">Go\s+Search<\/a>\s+]\s+[\s+<a\s+href="#GoTop">Go\s+Top\s+<\/a>]<\/span><\/td>\s+<\/tr>\s+<\/table>',
		),
		'tropicos' => array('<span\s+class="AlertText">No\s+result\s+were\s+found<\/span>'
		),
		'biolib' => array(
			'<h3>Scientific\s+names<\/h3>\s+<div\s+class="clbarl2"><div\s+class="clbarbodyl2">\s+No\s+records\s+found<\/div><\/div>',
			'<h3>Vernacular\s+names<\/h3>\s+<div\s+class="clbarl2"><div\s+class="clbarbodyl2">\s+No\s+records\s+found<\/div><\/div>'
		),
		'pubmed' => array(
			'\<li\s+class\="info"\>No\s+items\s+found\.\<\/li\>',
		),
		'plazi' => array(
			'\<td\s+class\="searchErrorMessage"\>\s*No\s+treatment\s+yet\s+on\s+plazi\:\s+But\s+you\s+can\s+help\s+to\s+make\s+it\s+accessible\!\<\/td\>\s+\<\/tr\>',
		),
		'gbif' => array(
			'\<span\s+class\="moreMatches"\>No\s+scientific\s+names\s+matching\s+\<span\s+class\="subject"\>"' . $pTaxonName . '"\<\/span\>\<\/span\>',
			'\<span\s+class\="moreMatches"\>No\s+common\s+names\s+matching\s+\<span\s+class\="subject"\>"' . $pTaxonName . '"\<\/span\>\<\/span\>',
			'\<span\s+class\="moreMatches"\>No\s+countries\s+with\s+names\s+matching\s+\<span\s+class\="subject"\>"' . $pTaxonName . '"\<\/span\>\<\/span\>',
			'\<span\s+class\="moreMatches"\>No\s+datasets\s+with\s+names\s+matching\s+\<span\s+class\="subject"\>"' . $pTaxonName . '"\<\/span\>\<\/span\>',
		),
		'morphbank' => array(
			'display\s+Results:\s+no\s+rows!\<br\/\>',
		),
		'daisie' => array(
			'No\s+Results\s+Found',
		),
		'invasive' => array(
			'Your\s+search\s+\-\s+\<b\>' . $pTaxonName .'\<\/b\>\s+\-\s+did\s+not\s+match\s+any\s+documents\.',
		),
	);
	return $lResult;
}

function GetSingleSiteLinkArray($pTaxonName, $pSiteName){
	$lResArr = GetLinksArray($pTaxonName, false);
	$lResult = $lResArr[$pSiteName];

	if( $pSiteName == 'gbif' ){
		$lGbifData = GetTaxonGbifLinkAndId($pTaxonName);
		$lResult['dont_check_for_existence'] = true;
		if($lGbifData['id']){//Dobavqme gbif-a samo ako ima takyv rez v gbif
			$lResult['href'] = $lGbifData['link'];
			$lResult['results_exist'] = true;
		}else{
			$lResult['results_exist'] = false;
			$lResult['picsrc'] = $lResult['default_picsrc'];
		}
	}elseif( $pSiteName == 'morphbank'){
		$lMorphRes = GetTaxonMorphbankResultCount($pTaxonName);
		$lResult['dont_check_for_existence'] = true;
		if ((int)$lMorphRes > 0) {
			$lResult['results_exist'] = true;
		}else{
			$lResult['results_exist'] = false;
			$lResult['picsrc'] = $lResult['default_picsrc'];
		}
	}
	elseif( $pSiteName == 'ncbi'){
		$lTaxonId = GetTaxonNcbiId($pTaxonName);
		$lResult['isubio'] = false;
		$lResult['dont_check_for_existence'] = true;
		if ($lTaxonId) {
			$lResult['href'] = NCBI_TAXON_URL . $lTaxonId;
			$lResult['results_exist'] = true;
		}else{
			$lResult['results_exist'] = false;
			$lResult['picsrc'] = $lResult['default_picsrc'];
		}
	}elseif( $pSiteName == 'lias'){
		$lLiasRes = GetTaxonLiasResultCount($pTaxonName);
		$lResult['isubio'] = false;
		$lResult['dont_check_for_existence'] = true;
		if ((int)$lLiasRes > 0) {
			$lResult['results_exist'] = true;
		}else{
			$lResult['results_exist'] = false;
			$lResult['picsrc'] = $lResult['default_picsrc'];
		}
	}

	return $lResult;
}


function GetTaxonGbifLinkAndId($pTaxonName){
	$lGbifMap = new ctaxonmap(array(
			'templs' => array(
				G_STARTRS => 'external_details.mapStart',
				G_ENDRS => 'external_details.mapEnd',
				G_ROWTEMPL => 'external_details.mapRow',
			),
			'cache' => 'extdetails_gbifmap',
			'cachetimeout' => CACHE_TIMEOUT_LENGTH,
			'taxon_name' => $pTaxonName,
		)
	);
	$lGbifMap->GetDataC();
	$lResult = array(
		'id' => $lGbifMap->GetVal('gbif_id'),
		'link' => $lGbifMap->GetVal('gbif_link')
	);
	return $lResult;
}

function GetTaxonMorphbankResultCount($pTaxonName){
	$lMorph = new ctaxon_morphbank(
		array(
			'templs' => array(),
			'taxon_name' => $pTaxonName,
			'pagesize' => 1,
			'cache' => 'extdetails_morph',
			'cachetimeout' => CACHE_TIMEOUT_LENGTH,
		)
	);
	$lMorph->GetDataC();
	$lMorphRes = (int)$lMorph->m_recordCount;
	return $lMorphRes;
}

function GetTaxonLiasResultCount($pTaxonName){
	$lLias = new ctaxon_lias(array(
		'taxon_name' => $pTaxonName,
	));
	$lLias->GetDataC();
	$lResultFound = $lLias->GetResultCount();
	return $lResultFound;
}

function GetTaxonNcbiId($pTaxonName){
	$lNCBI =new ctaxon_ncbiinfo(
		array(
			'ctype' => 'ctaxon_ncbiinfo',
			'icon_ajax_url' => AJAX_MENU_LINK_SRV . '?taxon_name=' . $pTaxonName . '&site_name=ncbi&type=2',
			'call_sub_ajax_queries' => 1,
			'templs' => array(
				G_STARTRS => 'external_details.ncbiStart',
				G_ENDRS => 'external_details.ncbiEnd',
				G_ROWTEMPL => 'external_details.ncbiRow',
			),
			'lineage_templs' => array(
				G_STARTRS => 'external_details.ncbiLineageStart',
				G_ENDRS => 'external_details.ncbiLineageEnd',
				G_ROWTEMPL => 'external_details.ncbiLineageRow',
			),
			'link_templs' => array(
				G_STARTRS => 'external_details.extLinksStart',
				G_ENDRS => 'external_details.extLinksEnd',
				G_ROWTEMPL => 'external_details.extLinksRow',
			),
			'link_ajax_templs' => array(
				G_DEFAULT => 'external_details.extLinksAjax',
			),
			'link_result_count' => 5,
			'link_database' => EUTILS_PUBMED_DB,
			'link_database_title' => 'PubMed',
			'entrezrecords_templs' => array(
				G_STARTRS => 'external_details.entrezRecordsStart',
				G_ENDRS => 'external_details.entrezRecordsEnd',
				G_ROWTEMPL => 'external_details.entrezRecordsRow',
			),
			'entrezrecords_ajax_templs' => array(
				G_DEFAULT => 'external_details.entrezRecordsAjax',
			),
			'entrez_records_allowed_databases' => array(
				EUTILS_PMC_DB => EUTILS_PMC_DISPLAY_NAME,
				EUTILS_TAXONOMY_DB => '',
				EUTILS_PROTEIN_DB => '',
				EUTILS_POPSET_DB => '',
				EUTILS_NUCCORE_DB => EUTILS_NUCCORE_DISPLAY_NAME
			),
			'taxon_name' => $pTaxonName,
			'cache' => 'extdetails_ncbiinfo',
			'cachetimeout' => CACHE_TIMEOUT_LENGTH,
		)
	);
	$lNCBI->GetDataC();
	//~ var_dump($lNCBI);
	$lTaxonId = $lNCBI->GetVal('m_taxonid');
	return $lTaxonId;
}

/**
 * Prepares the value so that it can be used as a xml node name
 * @param string $pValue
 */
 function prepareValueForXmlNodeName($pValue){
	$lSearch = array(' ', '&');
	$lReplacements = array('_', 'and');
	$pValue = str_replace($lSearch, $lReplacements, $pValue);
	$pValue = preg_replace('/[!"#$%&\'()*+,\/;<=>?@[\\]^`{|}~]/i', '', $pValue);
	return $pValue;
}

/**
 * Prepares the value so that it can be used as an attribute value of a xml node
 * @param string $pValue
 */
function prepareValueForXmlAttributeValue($pValue){
	return str_replace('"', '&quot;', $pValue);
	$lSearch = array(' ', '(', ')', '&', ',', '/', '\\');
	$lReplacements = array('_', '', '', 'and', '_', '', '');
	$pValue = str_replace($lSearch, $lReplacements, $pValue);
	return $pValue;
}

/**
 * Понеже грешките са във формат ERROR: pErrorMsg, а ни се налага да правим getstr - трябва да махнем ERROR:
 * @param unknown_type $pDbError
 */
//~ function getDbError($pDbError){
	//~ return trim(str_replace('ERROR: ', '', $pDbError));
//~ }
?>