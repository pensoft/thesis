<?php
$gDontRedirectToLogin = 1;
$docroot = getenv('DOCUMENT_ROOT');
require_once($docroot . '/lib/static.php');
session_write_close();

define('ACCEPT_REQUEST_BY_IPS', '192.168.83.8/128');
define('PGPASSWORD_PRODUCTION', 'pensoft123');
define('PGUSER_PRODUCTION', 'postgres');
define('PGHOST_PRODUCTION', 'psweb.pensoft.net');
define('DUMP_FILE_PATH', '/tmp/backup/');

$lIp = $_SERVER['REMOTE_ADDR'];
$lIpFilterCheck = ipfilter($lIp, array(ACCEPT_REQUEST_BY_IPS));

/*
 * dump and load of production database to test server
 * 
 * */
 
if($lIpFilterCheck) {
	$lTmpFileName = 'pensoft2_' . date('Y') . '_' . date('m') . '_' . date('d') . '__' . date('H') . '_' . date('s');
	$lDumpCommand = 'export PGPASSWORD=' . PGPASSWORD_PRODUCTION . ' && export PGUSER=' . PGUSER_PRODUCTION . ' && /usr/bin/pg_dump -h ' . PGHOST_PRODUCTION . ' pensoft2 > ' . DUMP_FILE_PATH . $lTmpFileName . '.sql && unset PGPASSWORD && unset PGUSER';
	$lRestoreCommand = 'psql -U postgres -c \'CREATE DATABASE ' . $lTmpFileName . ' OWNER = postgres\' && psql -U postgres ' . $lTmpFileName . ' -f ' . DUMP_FILE_PATH . $lTmpFileName . '.sql';
	
	exec($lDumpCommand, $lOutput1);
	exec($lRestoreCommand, $lOutput2);
	
	//var_dump($lOutput1);
	//var_dump($lOutput2);
	echo 'Database name: ' . $lTmpFileName;
	unlink('/tmp/backup/' . $lTmpFileName . '.sql');
} else {
	echo 'You don\'t have permissions to do this action';
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