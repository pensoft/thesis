<?php
class ccoordinates extends cbase {	
	var $m_dontgetdata;
	var $m_longitude;
	var $m_longitude_raw;
	var $m_latitude;
	var $m_latitude_raw;

	function __construct($pFieldTempl) {			
		$this->m_state = 0;
		$this->m_pubdata = array_change_key_case($pFieldTempl, CASE_LOWER);
		$this->m_Templs = $this->m_pubdata['templs'];			
		$this->LoadDefTempls();
	}
	
	function DontGetData($p) {
		$this->m_dontgetdata = $p;
	}	
	
	function LoadDefTempls() {
		if (!defined('D_EMPTY')) {
			define('D_EMPTY', 'global.empty');
		}
		
		$this->m_defTempls = array(G_HEADER => D_EMPTY, G_FOOTER => D_EMPTY, G_STARTRS => D_EMPTY, G_ENDRS => D_EMPTY, G_NODATA => D_EMPTY, G_ROWTEMPL => D_EMPTY);
	}	
	
	function CheckVals() {
		if($this->m_state == 0) {			
			$this->m_state++;
		} else {
			// NOTICE
		}
	}
	
	function GetLatitude(){
		return $this->m_latitude;
	}
	
	function GetLongitude(){
		return $this->m_longitude;
	}
	
	function SplitCoordinates(){
		$lDateStr = $this->m_pubdata['coord_string'];
		$lDateStr = htmlspecialchars_decode($lDateStr);//За да може да хванем случая когато секундите идват с &quote
		$lDateStr = str_replace('−', '-', $lDateStr);//Ako vmesto minus ima −
		$lStrPos = mb_strpos($lDateStr, COORD_SPLITTER);
		if( $lStrPos === false )//Ako nqma ";" - tyrsim "," predi da failnem
			$lStrPos = mb_strpos($lDateStr, ALTERNATE_COORD_SPLITTER);
			
		if( $lStrPos !== false ){
			$lFirstCoord = mb_substr($lDateStr, 0, $lStrPos);
			$lParsedFirstCoord = $this->ParseCoordString($lFirstCoord);
			//~ echo '<br/>';
			$lSecondCoord = mb_substr($lDateStr, $lStrPos + 1);
			$lParsedSecondCoord = $this->ParseCoordString($lSecondCoord);
			/**
				Ако координатите са по подразбиране във DD без полукълбо приемамe, че
				1-та е latitude а втората - longitude
			*/	
			//~ var_dump($lParsedFirstCoord);
			//~ var_dump($lParsedSecondCoord);
			if( $lParsedFirstCoord['is_latitude'] > 0 || $lParsedFirstCoord['is_latitude'] < 0 ){				
				$this->m_latitude = $lParsedFirstCoord['coord'];
				$this->m_latitude_raw = $lFirstCoord;				
			}else{
				$this->m_longitude = $lParsedFirstCoord['coord'];
				$this->m_longitude_raw = $lFirstCoord;
			}
			
			if( $lParsedSecondCoord['is_latitude'] > 0 ){				
				$this->m_latitude = $lParsedSecondCoord['coord'];
				$this->m_latitude_raw = $lSecondCoord;				
			}else{
				$this->m_longitude = $lParsedSecondCoord['coord'];
				$this->m_longitude_raw = $lSecondCoord;
			}
			//~ var_dump($this->m_latitude);
			//~ var_dump($this->m_longitude);			
		}
	}
	
	function ParseCoordString($pCoordString){//Vryshta masiv s koordinata vyv format DD i buleva prom, koqto ukazva dali koord e latitude (-1 ako koordinata e direktno v DD)
		//~ var_dump($pCoordString);
		//~ echo '<br/>';
		//~ var_dump(urlencode(htmlspecialchars('36°31\'21.4"N; 114°09\'50.6"W')));
		//~ var_dump($pCoordString);
		
		$lMinSymbol = '[’\'`]';
		$lSecondsSymbol = '([’\'`]{2}|["])';
		$lDegSymbol = '[°°]';
		$lPatterns = array();
		$lPatterns[] = '/'
			. '(?P<coord_name1>[sewn])?\s*'
			. '(?P<degs>[+-]?\d+(\.(\d)*)?)\s*' . $lDegSymbol .'\s*'
			. '((?P<minutes>(\d)+(\.(\d)*)?)\s*' . $lMinSymbol . ')\s*' 
			. '((?P<seconds>(\d)+(\.(\d)*)?)\s*' . $lSecondsSymbol . ')\s*'
			. '(?P<coord_name2>[sewn])?' 
			. '/iu';//1.Example 1: “36° 31' 21.4" N   (DMS)
		$lPatterns[]  = '/'
			. '(?P<coord_name1>[sewn])?\s*'
			. '(?P<degs>[+-]?\d+(\.(\d)*)?)\s*' . $lDegSymbol .'\s*'
			. '((?P<minutes>(\d)+(\.(\d)*)?)\s*' . $lMinSymbol . ')\s*' 
			. '(?P<seconds>(\d)+(\.(\d)*)?)\s*'
			. '(?P<coord_name2>[sewn])?' 
			. '/iu';//1.Example 1: “36° 31' 21.4 N   (DMS)
		$lPatterns[]  = '/'
			. '(?P<coord_name1>[sewn])?\s*'
			. '(?P<degs>[+-]?\d+(\.(\d)*)?)\s*' . $lDegSymbol .'\s*'
			. '((?P<minutes>(\d)+(\.(\d)*)?)\s*' . $lMinSymbol . ')\s*' 
			. '(?P<coord_name2>[sewn])?' 
			. '/iu';//1.Example 1: “36° 31.5' N   (DM)
		$lPatterns[]  = '/'
			. '(?P<coord_name1>[sewn])?\s*'
			. '(?P<degs>[+-]?\d+(\.(\d)*)?)\s*' . $lDegSymbol .'\s*'
			. '((?P<minutes>(\d)+(\.(\d)*)?))\s*' 
			. '(?P<coord_name2>[sewn])?' 
			. '/iu';//1.Example 1: “36° 31.5N   (DM)
		$lPatterns[]  = '/'
			. '(?P<coord_name1>[sewn])?\s*'
			. '(?P<degs>[+-]?\d+(\.(\d)*)?)\s*' . $lDegSymbol .'\s*'
			. '(?P<coord_name2>[sewn])?' 
			. '/iu';//1.Example 1: “36.3° N   (D)
		$lPatterns[]  = '/'
			. '(?P<coord_name1>[sewn])?\s*'
			. '(?P<degs>[+-]?\d+(\.(\d)*)?)\s*'
			. '(?P<coord_name2>[sewn])?' 
			. '/iu';//1.Example 1: “36.3 N   (D)
		
		
		//~ $lPattern = '/'
			//~ . '(?P<coord_name1>[sewn])?\s*'
			//~ . '(?P<degs>[+-]?\d+?(\.(\d)*)?)\s*' . $lDegSymbol .'\s*'
			//~ . '((?P<minutes>(\d)+?(\.(\d)*)?)\s*' . $lMinSymbol . ')?\s*' 
			//~ . '((?P<seconds>(\d)+?(\.(\d)*)?)\s*)?\s*'
			//~ . '(?P<coord_name2>[sewn])?' 
			//~ . '/iu';
		/**
			Горния паттерн хваща следните формати (хваща и случаите когато полукълбото е отпред)
			1.Example 1: “36° 31' 21.4" N; 114° 09' 50.6" W“    (DMS)
			2. Example 2: “36° 31.4566’N; 114° 09.8433’W”        (DDM)
			3. Example 3: “36.524276° S; 114.164055° W”          (DD)
		*/
		foreach( $lPatterns as $lPattern ){			
			if( preg_match( $lPattern, $pCoordString, $lCoordData )){
				//~ echo 1;
				//~ var_dump($lPattern);
				//~ var_dump($pCoordString);
				//~ echo '<br/>';
				$lHemisphere = $lCoordData['coord_name1'];
				if( $lHemisphere == '' )
					$lHemisphere = $lCoordData['coord_name2'];
				$lDeg = (float)$lCoordData['degs'];
				$lMin = (float)$lCoordData['minutes'];			
				$lSec = (float)$lCoordData['seconds'];
				$lHemisphere = mb_strtolower($lHemisphere);
				$lCoordIsLatitude = -1;
				if( $lHemisphere != '' ){//Имаме полукълбо
					$lCoordIsLatitude = false;//Координатата e longitude
					if( $lHemisphere == 's' || $lHemisphere == 'n' )//Координатата е latitude
						$lCoordIsLatitude = true;
					if( $lHemisphere == 's' || $lHemisphere == 'w' )//обръщаме резултата понеже е в обратното полукълбо
						$lDeg = - $lDeg;
				}
				$lDDCoord = $this->ConvertCoordinatesToDD($lDeg, $lMin, $lSec);	
				return array('coord' => (float)$lDDCoord, 'is_latitude' => $lCoordIsLatitude);
			}	
		}			
		/**
			Хващаме случая когато работим с DD (decimal degrees); 
			Example 4: “−36.524276; −114.164055“ 
		*/		
		return array('coord' => (float) $pCoordString, 'is_latitude' => -1);		
	}
	
	function ConvertCoordinatesToDD($pDegs = 0, $pMinutes = 0, $pSeconds = 0){
		$lResult = (float) $pDegs;
		if( $lResult > 0 )
			$lResult += (float)$pMinutes / 60 + (float) $pSeconds / 3600;
		else
			$lResult -= (float)$pMinutes / 60 + (float) $pSeconds / 3600;
		return $lResult;
	}
	
	function GetData() {
		$this->CheckVals();
		if ($this->m_state >= 1) {	
			
			$this->SplitCoordinates();
			$this->m_state++;
		}
	}

	function FetchNodeDetails($pNode) {
		if( $pNode ){			
			foreach($pNode->childNodes as $lChild) {
				if( $lChild->nodeType != 1 )//Obrabotvame samo elementite
					continue;
				$lKey = strtolower($lChild->nodeName);
				$this->m_pubdata[$lKey] = $this->m_currentRecord[$lKey] = $lChild->textContent;
			}			
		}		
	}
	
	function Display() {
		
		if (!$this->m_dontgetdata)
			$this->GetData();
		
		if ($this->m_state < 2) {
			return;
		}			
				
		
		$lRet .= $this->ReplaceHtmlFields($this->getObjTemplate(G_HEADER));
		if ($this->m_recordCount == 0) {
			$lRet .= $this->ReplaceHtmlFields($this->getObjTemplate(G_NODATA));
		} else {
			$lRet .= $this->ReplaceHtmlFields($this->getObjTemplate(G_STARTRS));
			$lRet .= $this->GetRows();			
			$lRet .= $this->ReplaceHtmlFields($this->getObjTemplate(G_ENDRS));
		}
		$lRet .= $this->ReplaceHtmlFields($this->getObjTemplate(G_FOOTER));
				
		return $lRet;
	}
	
}
?>