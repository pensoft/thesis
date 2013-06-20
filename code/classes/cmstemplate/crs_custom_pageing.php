<?php
global $user;
class crs_custom_pageing extends crs {		
	function LoadDefTempls() {
		if (!defined('D_EMPTY')) {
			define('D_EMPTY', 'global.empty');
		}
		if (!defined('D_PAGEING_STARTRS')) {
			define('D_PAGEING_STARTRS', 'pageing.startrs');
		}
		if (!defined('D_PAGEING_INACTIVEFIRST')) {
			define('D_PAGEING_INACTIVEFIRST', 'pageing.inactivefirst');
		}
		if (!defined('D_PAGEING_ACTIVEFIRST')) {
			define('D_PAGEING_ACTIVEFIRST', 'pageing.activefirst');
		}
		if (!defined('D_PAGEING_PGSTART')) {
			define('D_PAGEING_PGSTART', 'pageing.pgstart');
		}
		if (!defined('D_PAGEING_INACTIVEPAGE')) {
			define('D_PAGEING_INACTIVEPAGE', 'pageing.inactivepage');
		}
		if (!defined('D_PAGEING_ACTIVEPAGE')) {
			define('D_PAGEING_ACTIVEPAGE', 'pageing.activepage');
		}
		if (!defined('D_PAGEING_INACTIVELAST')) {
			define('D_PAGEING_INACTIVELAST', 'pageing.inactivelast');
		}
		if (!defined('D_PAGEING_ACTIVELAST')) {
			define('D_PAGEING_ACTIVELAST', 'pageing.activelast');
		}
		if (!defined('D_PAGEING_PGEND')) {
			define('D_PAGEING_PGEND', 'pageing.pgend');
		}
		if (!defined('D_PAGEING_ENDRS')) {
			define('D_PAGEING_ENDRS', 'pageing.endrs');
		}
		
		if (!defined('D_PAGEING_DELIMETER')) {
			define('D_PAGEING_DELIMETER', 'pageing.delimiter');
		}
		
		$this->m_defTempls = array(
			G_HEADER => D_EMPTY, 
			G_FOOTER => D_EMPTY, 
			G_STARTRS => D_EMPTY, 
			G_ENDRS => D_EMPTY, 
			G_NODATA => D_EMPTY, 
			G_PAGEING => D_EMPTY, 
			G_ROWTEMPL => D_EMPTY,
			G_PAGEING_STARTRS => D_PAGEING_STARTRS,
			G_PAGEING_INACTIVEFIRST => D_PAGEING_INACTIVEFIRST,
			G_PAGEING_ACTIVEFIRST => D_PAGEING_ACTIVEFIRST,
			G_PAGEING_PGSTART => D_PAGEING_PGSTART,
			G_PAGEING_INACTIVEPAGE => D_PAGEING_INACTIVEPAGE,
			G_PAGEING_ACTIVEPAGE => D_PAGEING_ACTIVEPAGE,
			G_PAGEING_INACTIVELAST => D_PAGEING_INACTIVELAST,
			G_PAGEING_ACTIVELAST => D_PAGEING_ACTIVELAST,
			G_PAGEING_PGEND => D_PAGEING_PGEND,
			G_PAGEING_DELIMETER => D_PAGEING_DELIMETER,
			G_PAGEING_ENDRS => D_PAGEING_ENDRS
		);
	}
	
	function CustomPageNav($p) {
		if (!$this->m_pageSize) return '';
		$lGroupStep = ($this->m_pubdata['groupstep'] ? $this->m_pubdata['groupstep'] : 5);
		$lMaxPages = ceil($this->m_recordCount / $this->m_pageSize);
		$this->m_pubdata['maxpages'] = $lMaxPages;
		if ($p > $lMaxPages) $p = 0;
		if ($p == -1) $p = $lMaxPages - 1;//kogato predadesh p = -1 te move-a do poslednata stranica
		
		$this->m_pubdata['currpage'] = (int)$p + 1;
		
		if ($lMaxPages > 1) {
			$lPageGroup = (int)($p / $lGroupStep) * $lGroupStep;
			$lPassedForbiddenRequestParams = $this->m_pubdata['forbidden_request_params'];
			if( !is_array( $lPassedForbiddenRequestParams ) ){//Parametrite, koito sa zabraneni za tozi obekt
				$lPassedForbiddenRequestParams = array();
			}
			$lForbiddenRequestParams = array_merge(array('PHPSESSID', '__utma', '__utmz'), $lPassedForbiddenRequestParams);//Dobavqme i parametrite, koito sa zabraneni za vsi4ki obekti
			foreach ($_REQUEST as $key => $val) {
				if( in_array( $key, $lForbiddenRequestParams))//Skipvame stoinostite, koito ne trqbva da se predavat
					continue;
				if (is_array($val)) {
					foreach ($val as $v) {
						$url .= $key . '[]=' . urlencode(s($v)) . '&';
					}
				}
				else {
					if ($key != "p" && $key != "rn")
						$url .= $key . '=' . urlencode(s($val)) . '&';
				}
			}
			
			$this->m_pubdata['pageingurl'] = $url;
			$this->m_pubdata['gotopage'] = 0;
			
			$lNavStr .=  $this->ReplaceHtmlFields($this->getObjTemplate(G_PAGEING_STARTRS));
			
			if ((int)$this->m_pubdata['usefirstlast']) 
				if ($p == 0) {
					$lFirst = $this->ReplaceHtmlFields($this->getObjTemplate(G_PAGEING_INACTIVEFIRST));
				} else {
					$lFirst = $this->ReplaceHtmlFields($this->getObjTemplate(G_PAGEING_ACTIVEFIRST));
				}
			
			if ($lPageGroup + 1 > $lGroupStep) {
				$this->m_pubdata['lpagegroup'] = $lPageGroup;
				$this->m_pubdata['gotopage'] = ($lPageGroup - 1);
				$lPgStart = $this->ReplaceHtmlFields($this->getObjTemplate(G_PAGEING_PGSTART));
			}
			
			if ($this->m_pubdata['pagingstartrevord']) {
				$lNavStr .= $lPgStart . $lFirst;
			} else {
				$lNavStr .= $lFirst . $lPgStart;
			}
			
			for ($i = $lPageGroup; (($i < $lPageGroup + $lGroupStep) && ($i < $lMaxPages)); $i++) {
				$this->m_pubdata['lpagenum'] = ($i+1);
				if ($i == $p) {
					$lNavStr .= $this->ReplaceHtmlFields($this->getObjTemplate(G_PAGEING_INACTIVEPAGE));
				} else {
					$this->m_pubdata['gotopage'] = $i;
					$lNavStr .= $this->ReplaceHtmlFields($this->getObjTemplate(G_PAGEING_ACTIVEPAGE));
				}
			}
			if ((int)$this->m_pubdata['usefirstlast']) 
				if (($p + 1) == $lMaxPages) {
					$lLast = $this->ReplaceHtmlFields($this->getObjTemplate(G_PAGEING_INACTIVELAST));
				} else {
					$lLast = $this->ReplaceHtmlFields($this->getObjTemplate(G_PAGEING_ACTIVELAST));
				}
			
			if ($lPageGroup < $lMaxPages - $lGroupStep) {
				$this->m_pubdata['lpagegroup'] = ($lPageGroup + $lGroupStep + 1);
				$this->m_pubdata['gotopage'] = ($lPageGroup + $lGroupStep);
				$lPgEnd = $this->ReplaceHtmlFields($this->getObjTemplate(G_PAGEING_PGEND));
			} else 
				$lPgEnd = $this->ReplaceHtmlFields($this->getObjTemplate(G_PAGEING_DELIMETER));
			
			if((int)$this->m_pubdata['pagingendrevord']) {
				$lNavStr .= $lLast;
				$lNavStr .=$lPgEnd;
			}else{
				$lNavStr .=$lPgEnd;
				$lNavStr .= $lLast;
			}
			$lNavStr .=  $this->ReplaceHtmlFields($this->getObjTemplate(G_PAGEING_ENDRS));
		}
		
		return $lNavStr;
	}
}

?>