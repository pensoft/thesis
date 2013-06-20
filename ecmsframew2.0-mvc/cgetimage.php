<?php

class cgetimage {
	public $ftst;
	public $fsize;
	public $fname;
	public $fpref;
	public $fid;
	public $fext;
	public $fpref_commands;
	private $photospath;
	private $basephotopref;
	private $hasparent;//Parenta e razlichen format ot jpg
	public $ftypes;
	function __construct($pArr) {
		// kak se pravi thumbnail za kvadratna sreda ot snimkata tuk
		// http://www.cit.gu.edu.au/~anthony/graphics/imagick6/thumbnails/#cut
		// i tuk
		// http://www.cit.gu.edu.au/~anthony/graphics/imagick6/resize/#space_fill
		
		// ako e masiv purviq element e za landscape a vtoria e za portrait
		
		$this->fpref_commands = $pArr['fpref_commands'];
		$this->parsefn($pArr['fname']);
		
		if (defined('SPLIT_PHOTO_DIR') && (int)SPLIT_PHOTO_DIR) {
			$newPicDir = getPicDirName((int)$this->fid);
			$this->photospath = $pArr['photospath'] ? $pArr['photospath'] : $newPicDir;
		} else {
			$this->photospath = $pArr['photospath'] ? $pArr['photospath'] : PATH_DL;
		}
		$this->ftypes = $pArr['imageextensions'];
		if(!is_array($this->ftypes))
			$this->ftypes= array();
		$this->hasparent = 0;
		foreach($this->ftypes as $key => $val){
			if (is_file($this->photospath . 'oo_' . $this->fid . $val)) {
				$bp = 'oo_';
				$this->hasparent = (int)$key;
				break;
			}
		}
		if(!(int)$this->hasparent) {
			$bp = 'big_';
			if (!is_file($this->photospath . 'big_' . $this->fid . '.jpg')) 
				$bp = 'gb_';
		}
		
		$this->basephotopref = $pArr['basepref'] ? $pArr['basepref'] : $bp;
	}
	
	function parsefn($p) {
		if (!preg_match('/^([\w\d]+)\_(\d+)\.(\w+)$/', $p, $arr)) {
			return;
		}
		
		if (!$this->fpref_commands[$arr[1]]) {
			return;
		}
		$this->fname = $p;
		$this->fpref = $arr[1];
		$this->fprefcommand = $this->fpref_commands[$arr[1]];
		$this->fid = (int)$arr[2];
		$this->fext = $arr[3];
	}
	
	function sendheaders($pType) {
		if ($pType == 'def') {
			header('Cache-Control:');
			header('Pragma:');
		} elseif ($pType == 'notmodified') {
			header('HTTP/1.1 304 Not Modified');
		} elseif ($pType == 'lastmodified') {
			header('Last-Modified: ' . gmdate('D, d M Y H:i:s \G\M\T', $this->ftst));
			header('Expires: Thu, 17 Sep 2011 07:55:05 GMT');
			header('Content-Type: image/jpeg');
			//~ header('Content-Type: text/plain');
			header('Content-Length: ' . $this->fsize);
			header('Content-Disposition: inline; filename="' . $this->fname . '"');
		}
	}
	

	
	function Display() {
		if (!$this->fname) return;
		$this->sendheaders('def');
		if (!is_file($this->photospath . $this->fname) && $this->fpref != 'oo') {
			
			$f = $this->photospath . $this->basephotopref . $this->fid . ($this->basephotopref =='oo_' && (int)$this->hasparent ? $this->ftypes[$this->hasparent] : '.jpg');
			
			if (is_array($this->fprefcommand)) {
				//~ $cmd = escapeshellcmd('identify ' . $f);
				//~ $res = exec($cmd);
				$res = executeConsoleCommand('identify', array($f));
				preg_match('/(\d+)x(\d+)/', $res, $res);
				//~ var_dump($res);
				//~ exit();
				if( (int)$res[1] > (int)$res[2]) { // landscape
					$this->fprefcommand = $this->fprefcommand[0];
				} else { // portrait
					$this->fprefcommand = $this->fprefcommand[1];
				}
			}
			
			//~ ob_start();
			//~ passthru('convert ' . $this->fprefcommand . ' ' . $f . ' -');
			//~ $contents = ob_get_contents();
			//~ ob_clean();

			$contents = executeConsoleCommand('convert', array_merge(array($f), array($this->fprefcommand), array('-')));
			
			$this->ftst = mktime();
			$this->fsize = file_put_contents($this->photospath . $this->fname, $contents);

			$this->sendheaders('lastmodified');
			
			echo $contents;
			
			exit;
			
		} else {
			$t = apache_request_headers();
			$this->ftst = filemtime($this->photospath . $this->fname);
			
			if ($t['If-Modified-Since']) {
				$tst = strtotime($t['If-Modified-Since']);
				
				if ($this->ftst <= $tst) {
					header('HTTP/1.1 304 Not Modified');
					exit;
				}
			}
			
			$this->fsize = filesize($this->photospath . $this->fname);
			
			$this->sendheaders('lastmodified');
			
			readfile($this->photospath . $this->fname);
			
			exit;
		}
	}
}

?>