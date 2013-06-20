<?php
$gDontRedirectToLogin = true;
$docroot = getenv('DOCUMENT_ROOT');
require_once($docroot . '/lib/static.php');
// require_once ($docroot . '/lib/conf.php');
// require_once (PATH_ECMSFRCLASSES  . "static.php");
// require_once ($docroot . '/lib/common_conf.php');

$lDocumentId = (int)$_REQUEST['document_id'];
$lIp = $_SERVER['REMOTE_ADDR'];
$lIpFilterCheck = ipfilter($lIp, array(ACCEPT_REQUEST_BY_IP));

if($lDocumentId && $lIpFilterCheck) {
	$lDocumentXML = getDocumentXml($lDocumentId, SERIALIZE_INTERNAL_MODE, false, false, 0, true);
// 	var_dump($lDocumentXML);
	if($lDocumentXML) {
		echo $lDocumentXML;
	} else {
		echo '2';
	}
} else {
	echo '1';
}

function ipfilter($ip, $cidrs) {

	foreach ($cidrs as $line) {

		// Get the base and the bits from the CIDR
		list($base, $bits) = explode('/', $line);

		// Now split it up into it's classes
		list($a, $b, $c, $d) = explode('.', $base);

		// Now do some bit shifting/switching to convert to ints
		$i    = ($a << 24) + ($b << 16) + ( $c << 8 ) + $d;
		$mask = $bits == 0 ? 0: (~0 << (32 - $bits));

		// Here's our lowest int
		$low = $i & $mask;

		// Here's our highest int
		$high = $i | (~$mask & 0xFFFFFFFF);

		// Now split the ip we're checking against up into classes
		list($a, $b, $c, $d) = explode('.', $ip);

		// Now convert the ip we're checking against to an int
		$check = ($a << 24) + ($b << 16) + ( $c << 8 ) + $d;

		// If the ip is within the range, including highest/lowest values,
		// then it's witin the CIDR range
		if ($check >= $low && $check <= $high) {
			return 1;
		}

	}

	return 0;

}


?>