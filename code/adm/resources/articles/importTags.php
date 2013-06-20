<?php
$docroot = getenv("DOCUMENT_ROOT");
require_once($docroot . '/lib/static.php');

$lXmlDom = new DOMDocument();
$lXmlDom->load('/tmp/tags.xml');
echo $lXmlDom->saveXML();
exit;
$lRoot = $lXmlDom->documentElement;
$lChildren = $lRoot->childNodes;
$lCon = Con();
$lCon->Execute('DELETE FROM xml_nodes;');
for ( $i = 0; $i < $lChildren->length; ++$i){
	$lChild = $lChildren->item($i);
	if( $lChild->nodeType != 1 || $lChild->nodeName != 'tag' ){
		continue;
	}
	$lTagName = trim($lChild->getAttribute('name'));
	if( !$lTagName ){	
		continue;
	}
	$lTagInsertSql = 'SELECT * FROM spXmlNodes(1, null, \'' . q($lTagName) . '\')';
	$lCon->Execute($lTagInsertSql);
	$lCon->MoveFirst();
	$lTagId = $lCon->mRs['id'];
	if( !$lTagId ){
		continue;
	}
	echo $lTagName .  ' - ' . (int) $lTagId . '<br/>';
	$lAttributes = $lChild->childNodes;
	for ( $j = 0; $j < $lAttributes->length; ++$j){
		$lAttribute = $lAttributes->item($j);
		if( $lAttribute->nodeType != 1 || $lAttribute->nodeName != 'attribute' ){
			continue;
		}
		$lAttName = trim($lAttribute->getAttribute('name'));
		if( !$lAttName ){	
			continue;
		}
		$lAttInsertSql = 'SELECT * FROM spXmlAttributes(1, null, ' . (int)$lTagId  . ', \'' . q($lAttName) . '\')';
		$lCon->Execute($lAttInsertSql);
		$lCon->MoveFirst();
		$lAttId = $lCon->mRs['id'];
		echo '--------' . $lAttName .  ' - ' . (int) $lAttId . '<br/>';
	}
}


?>