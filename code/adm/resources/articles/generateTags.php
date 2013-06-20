<?php
$docroot = getenv("DOCUMENT_ROOT");
require_once($docroot . '/lib/static.php');



$lXmlDom = new DOMDocument();
//~ $doc->formatOutput = true;

$lCn = Con();

$lSql = '
	SELECT n.id, n.name as node_name, a.name as attribute_name, n.autotag_annotate_show
	FROM xml_nodes n
	LEFT JOIN xml_attributes a ON a.node_id = n.id
	ORDER BY n.id
';
$lCn->Execute($lSql);
$lCn->MoveFirst();

$lRoot = $lXmlDom->createElement('nodes');
$lRoot = $lXmlDom->appendChild($lRoot);
$lPreviousId = 0;
while( !$lCn->Eof()){
	$lCurrentId = (int) $lCn->mRs['id'];
	if( $lCurrentId != $lPreviousId ){//Dobavqme nov nod
		$lCurrentNode = $lRoot->appendChild($lXmlDom->createElement('node'));
		$lNodeName = trim($lCn->mRs['node_name']);
		$lAutotagAnnotateTag = (int)$lCn->mRs['autotag_annotate_show'];
		
		$lCurrentNode->setAttribute('name', $lNodeName);
		$lCurrentNode->setAttribute('autotag_annotate_show', $lAutotagAnnotateTag);
		
		//~ $lCurrentNode->appendChild($lXmlDom->createTextNode($lNodeName));
	}
	$lAttributeName = trim($lCn->mRs['attribute_name']);
	if( $lAttributeName ){
		$lAttribute = $lCurrentNode->appendChild($lXmlDom->createElement('attribute'));
		$lAttribute->setAttribute('name', $lAttributeName);
		//~ $lAttribute->appendChild($lXmlDom->createTextNode($lAttributeName));
	}
	$lCn->MoveNext();
	$lPreviousId = $lCurrentId;
}

header("Content-type: text/xml"); 

echo $lXmlDom->saveXML();
?>

