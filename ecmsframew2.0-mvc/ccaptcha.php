<?php

class ccaptcha {
	public $rand;
	public $angle;
	public $color;
	public $m_pubdata;
	
	function __construct($pArr) {
		$this->m_pubdata = array_change_key_case($pArr, CASE_LOWER);
		$this->rand = $this->GenerateRand();
		$this->angle = mt_rand(-40, 40);
		if (!$this->m_pubdata['bgcolor'])
			$this->color = $this->getBgColors();
		else $this->color = $this->m_pubdata['bgcolor'];
		
		if (!$this->m_pubdata['fontcolor'])
			$this->fontcolor = 'black';
		else $this->fontcolor = $this->m_pubdata['fontcolor'];
	}
	
	function CheckVals() {
		if (!$this->m_pubdata['imgsize'] || !$this->rand) return false;
		return true;
	}
	
	function GenerateRand() {
		//~ $randnum = mt_rand(0, 5000);
		//~ $randstr = substr(md5($randnum), 0, $this->m_pubdata['symb']);
		$randstr = mt_rand(100000, 999999);
		// Buhame v sesiqta
		if (!is_array($_SESSION[$this->m_pubdata['sessname']])) 
			$_SESSION[$this->m_pubdata['sessname']] = array();
		if (count($_SESSION[$this->m_pubdata['sessname']]) == (int)$this->m_pubdata['sessnum']) 
			array_shift($_SESSION[$this->m_pubdata['sessname']]);
		
		$_SESSION[$this->m_pubdata['sessname']][] = strtolower($randstr);
		
		return $randstr;
	}
	
	function GetRand() {
		return $this->rand;
	}
	
	function getBgColors() {
		$colarr = array(
			0 => 'red',
			1 => 'yellow',
			2 => 'green',
			3 => 'blue',
			4 => 'silver',
			5 => 'lightblue',
		);
		return $colarr[mt_rand(0, (count($colarr) - 1))];
	}
	
	function drawPointsAndLines() {
		$lRet = '';
		if (preg_match('/(\d+)x(\d+)/', $this->m_pubdata['imgsize'], $m)) {
			for ($x = 0; $x <= 3; $x ++) {
				$xcoord = mt_rand(0, $m[1]);
				$ycoord = mt_rand(0, $m[2]);
				$radius = $xcoord + mt_rand(1, 3);
				$lRet .= ' -fill ' . $this->fontcolor . ' -draw "Circle ' . $xcoord . ',' . $ycoord . ' ' . $radius . ',' . $ycoord . '" ';
			}
			for ($x = 0; $x <= 5; $x ++) {
				$xcoord1 = mt_rand(0, $m[1]);
				$ycoord1 = mt_rand(0, $m[2]);
				$xcoord2 = mt_rand($ycoord1, $m[1]);
				$ycoord2 = mt_rand($xcoord1, $m[2]);
				$lRet .= ' -fill ' . $this->fontcolor . ' -draw "line ' . $xcoord1 . ',' . $ycoord1 . ' ' . $xcoord2 . ',' . $ycoord2 . '" ';
			}
		}
		return $lRet;
	}
	
	function sendheaders() {
		header('Content-Type: image/jpeg');
		header('Content-Disposition: inline; filename="captcha.png"');
	}
	
	function Display() {
		if (!$this->CheckVals()) return;
		//~ ob_start();
		//~ passthru('convert -size ' . escapeshellarg($this->m_pubdata['imgsize']) . ' xc:' . $this->color . ' -pointsize ' . $this->m_pubdata['fontsize'] . $this->drawPointsAndLines() . ' -gravity center -fill ' . $this->fontcolor . ' -draw "text 0,0 \'' . $this->rand . '\'" -channel RGBA -wave 1x15 -swirl ' . $this->angle . ' png:-');
		//~ $contents = ob_get_contents();
		//~ ob_clean();
		$lCommand = '-size ' . escapeshellarg($this->m_pubdata['imgsize']) . ' xc:' . $this->color . ' -pointsize ' . $this->m_pubdata['fontsize'] . $this->drawPointsAndLines() . ' -gravity center -fill ' . $this->fontcolor . ' -draw "text 0,0 \'' . $this->rand . '\'" -channel RGBA -wave 1x15 -swirl ' . $this->angle . ' png:-';
		$contents = executeConsoleCommand('convert', array_merge(array($lCommand))); 			

		$this->sendheaders();
		echo $contents;
	}
}

?>