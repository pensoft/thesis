<?php

// EPAY imat nova sistema
define('PAY_EPAY_SUBMITURL', 'https://devep2.datamax.bg/ep2/epay2_demo/');
// danni za user etaligent
define('PAY_EPAY_MIN', 'D621470915');
define('PAY_EPAY_SECWORD', '9P9DJ62S2FV2AJK2A90UKHVIC9B77WNWVXMDUD06VRFPZIO4E9KW6S0LOMWYIGNP');
// danni za user etaligent2
//~ define('PAY_EPAY_MIN', 'D097015143');
//~ define('PAY_EPAY_SECWORD', 'ZN72OC7ZMP2ETRZ3GDZQCWULKGMXFCQVWG3SQHFWXTFIOJET6KUPC60LJB02AUAI');
//~ define('PAY_EPAY_SUBMITURL', 'https://epay.bg');
//~ define('PAY_EPAY_MIN', '6398658239');
//~ define('PAY_EPAY_SECWORD', 'TZFTO9322T1532HXT4GIVRU83N5X7OZYT6A421RPUYX1OMT1XAOBBF878UK0LMDI');
define('PAY_EPAY_TRANSPERC', '1.2');
define('PAY_EPAY_TRANSMIN', '0.20');

// Bulbank terminal
define('BULBANK_TERMINALID', '62160007');
define('BULBANK_PRIVKEY', '/var/www/askforhotel/items/bulbank/afhpriv_test.key'); 
define('BULBANK_PUBKEY', '/var/www/askforhotel/items/bulbank/APGW_test.cer'); 

define('PAY_ORDERFINISH_URL', 'http://rado.afh.etaligent.net/orderfinish.php');


if (!defined('BULBANK_TERMINALID')) {
	die('Bulbank configuration constants missing');
}

if (!defined('BULBANK_PRIVKEY')) {
	die('Bulbank configuration constants missing');
}

if (!defined('PAY_EPAY_SUBMITURL')) {
	die('Epay configuration constants missing');
}

if (!defined('PAY_EPAY_MIN')) {
	die('Epay configuration constants missing');
}

if (!defined('PAY_EPAY_SECWORD')) {
	die('Epay configuration constants missing');
}

function payEpayStatus($encoded, $checksum) {
	global $usr_obj;
	$hmac   = hmac('sha1', $encoded, PAY_EPAY_SECWORD);
	$cn = Con();
	
	if ($hmac == $checksum) { # XXX Check if the received CHECKSUM is OK
		
		$data = base64_decode($encoded);
		$lines_arr = split("\n", $data);
		$info_data = '';
		
		foreach ($lines_arr as $line) {
			if (ereg('^INVOICE=([0-9]+):STATUS=(PAID|DENIED|EXPIRED)(:PAY_TIME=([0-9]+):STAN=([0-9]+):BCODE=([0-9]+))?$', $line, $regs)) {
				$invoice  = (int)$regs[1];
				$status   = $regs[2];
				$pay_date = $regs[4]; # XXX if PAID
				$stan     = (int)$regs[5]; # XXX if PAID
				$bcode    = (int)$regs[6]; # XXX if PAID
				
				if ($status == 'DENIED' || $status == 'EXPIRED') {
					$sql = 'SELECT * FROM spPay(-1, ' . $invoice . ', null, null, null, null)';
				} elseif ($status == 'PAID') {
					$sql = '
					UPDATE payepay SET stan = ' . $stan . ', bcode = ' . $bcode . ' WHERE stan IS NULL AND payid = ' . $invoice . ';
					SELECT * FROM spPay(3, ' . $invoice . ', null, null, null, null)';
				}
				
				$cn->Execute($sql);
				if ($cn->GetLastError()) {
					$info_data .= "INVOICE=$invoice:STATUS=ERR\n";
				} else {
					$info_data .= "INVOICE=$invoice:STATUS=OK\n";
				}
			}
		}

		echo $info_data, "\n";
	}
	else {
		echo "ERR=Not valid CHECKSUM\n";
	}
	return;
}


function hmac($algo,$data,$passwd){
	/* md5 and sha1 only */
	$algo=strtolower($algo);
	$p=array('md5'=>'H32','sha1'=>'H40');
	if(strlen($passwd)>64) $passwd=pack($p[$algo],$algo($passwd));
	if(strlen($passwd)<64) $passwd=str_pad($passwd,64,chr(0));

	$ipad=substr($passwd,0,64) ^ str_repeat(chr(0x36),64);
	$opad=substr($passwd,0,64) ^ str_repeat(chr(0x5C),64);

	return($algo($opad.pack($p[$algo],$algo($ipad.$data))));
}

function payEpayRegister($pPayId, $pMin, $pCin, $pEnc, $pChkS, $lExpDate) {
	$sql = 'INSERT INTO payepay (payid, min, cin, enc, chksum, expdate)
		VALUES(' . $pPayId . ', \'' . $pMin . '\', \'' . $pCin . '\', \'' . $pEnc . '\', \'' . $pChkS . '\', \'' . $lExpDate . '\'::timestamp)';
	
	$cn = Con();
	$cn->Execute($sql);
	if ($cn->GetLastError()) {
		return 1;
	}
	return 0;
}

function payMarkPaymentWaiting($pPayId) {
	$createPay = 'SELECT * FROM sppay(2, ' . $pPayId . ', null, null, null, null)';
	$cn = Con();
	$cn->Execute($createPay);
	$cn->MoveFirst();
	if (!$cn->Eof()) {
		return $cn->mRs['payid'];
	} else {
		return 0;
	}
}

function payCreatePayment($pPayType, $pOrderTable, $pTotalPrice, $pDescr) {
	$createPay = 'SELECT * FROM sppay(1, null, ' . $pPayType . ', \'' . q($pOrderTable) . '\', ' . $pTotalPrice . ', \'' . q($pDescr) . '\')';
	$cn = Con();
	//~ echo $createPay;
	$cn->Execute($createPay);
	$cn->MoveFirst();
	if (!$cn->Eof()) {
		return $cn->mRs['payid'];
	} else {
		return 0;
	}
}

function SetPaymentStatus($payid, $status) {
	if ($status == 1) return 0;
	$pOper = 3;
	if ($status == 5) $pOper = -1;
		
	$setState = 'SELECT * FROM spPay(' . $pOper . ', ' . $payid . ', null, null, null, null)';
	$cn = Con();
	$cn->Execute($setState);
	if ($cn->GetLastError()) {
		return 1;
	}
	return 0;
}


function payBulbankRegister($pPayId, $pDescr, $pPrice, &$pHash) {
	//~ var_dump($pPayId);
	$lMinnumber = BULBANK_TERMINALID;
	$lGdate = date('YmdHis');

	$lTransType = "10";
	$lLang = "BG";
	$lVer = "1.0";
	
	$pPrice = number_format($pPrice, 2, '.', '');
	$lTotal = (int)($pPrice * 100);
	
	$lData = $lTransType . $lGdate . str_pad($lTotal, 12, "0", STR_PAD_LEFT) . $lMinnumber . str_pad($pPayId, 15, " ", STR_PAD_RIGHT) . str_pad($pDescr, 125, " ", STR_PAD_RIGHT) . $lLang . $lVer;
	
	// Podpisvane
	$pPrivkey = file_get_contents(BULBANK_PRIVKEY);
	if (!$pKeyid = openssl_get_privatekey($pPrivkey)) {
		// Error getting privat key
		return 1;
	}
	if (!openssl_sign($lData, $lSign, $pKeyid)) {
		// Error signing 
		return 1;
	}
	openssl_free_key($pKeyid);
	// Enkodvane
	$pHash = urlencode(base64_encode($lData . $lSign));
	
	$cn = Con();
	$cn->Execute('INSERT INTO paybulbank (payid, hash) 
		VALUES (' . (int)$pPayId . ', \'' . q($pHash) . '\')');
	
	return 0;
}

function payBulbankStatus($hash) {
	$cn = Con();
	$lData = base64_decode($hash);

	$lRespCode = substr($lData, 51, 2);
	$pPayId = (int)trim(substr($lData, 36, 15));

	// Proverka
	$lPubKey = file_get_contents(BULBANK_PUBKEY);
	if (!$lPubKeyId = openssl_pkey_get_public($lPubKey)) {
		return 0;
	}
	//poletata sa s fixirana duljina... ot 56 simvol natatuk e cifrovia podpis
	$lIsSign = openssl_verify(substr($lData, 0, 56), substr($lData, 56), $lPubKeyId);

	if ($lIsSign) {
		// UPDATE na plashtaneto
		$cn->Execute('UPDATE paybulbank SET 
			response = \'' . q($lRespCode) . '\', 
			resphash = \'' . q($hash) . '\' 
			WHERE payid = ' . $pPayId);
		if ($cn->GetLastError()) {
			return 0;
		}
		
		if ($lRespCode != "00") {
			// Otkazano
			$cn->Execute('SELECT * FROM spPay(-1, ' . $pPayId . ', null, null, null, null)');
			return 2;
		} else {
			// Potvurdeno
			$cn->Execute('SELECT * FROM spPay(3, ' . $pPayId . ', null, null, null, null)');
			return 1;
		}
	} 
	
	return 0;
}


?>