<?php

if (defined('PATH_REWRITE_MAP')) {
	require_once(PATH_REWRITE_MAP);
}

if( !defined(REWRITE_SPACE_REPLACEMENT) ){
	define('REWRITE_SPACE_REPLACEMENT', ' ');
}
if( !defined(REWRITE_UNDERSCORE_REPLACEMENT) ){
	define('REWRITE_UNDERSCORE_REPLACEMENT', '_');
}
if( !defined(REWRITE_DOUBLE_QUOTES_REPLACEMENT) ){
	define('REWRITE_DOUBLE_QUOTES_REPLACEMENT', '"');
}
if( !defined(REWRITE_SINGLE_QUOTES_REPLACEMENT) ){
	define('REWRITE_SINGLE_QUOTES_REPLACEMENT', '\'');
}

class crewrite {
	private $map;
	private $rwr_param;
	
	function __construct() {
		if (!defined('ENABLE_MOD_REWRITE') || (int)ENABLE_MOD_REWRITE == 0) {
			return;
		}
		global $rewrite_map;
		if (!(isset($rewrite_map) && is_array($rewrite_map))) {
			$rewrite_map = array();
		}
		$this->map = $rewrite_map;
		if (!count($this->map)) {
			die('crewrite expects array for map!!!');
		}
		$this->rwr_param = $_REQUEST['rwr'];
		if (!$this->rwr_param || $this->rwr_param == '/') return;
		$this->TranslateParam();
	}
	
	private function TranslateParam() {//Тук работим наобратно от EncodeUrl
		if (!defined('ENABLE_MOD_REWRITE') || (int)ENABLE_MOD_REWRITE == 0) {
			return;
		}
		$lUrlMap = $this->map[$_SERVER['SCRIPT_NAME']];
		if (!$lUrlMap) 
			return;		
		if (!is_array($lUrlMap['params'])) {
			die('crewrite expects array for rewrite_map!!!');
		}
		
		$lOldUrl = $this->rwr_param;//Този параметър трябва да е дошъл през htaccessa; Това е на практика query стринга преработен през EncodeUrl
		$lNewUrl = '';
		foreach ($lUrlMap['params'] as $lCurrentParamPattern => $lCurrentParamReplacement) {
			/**
				масивът $lUrlMap['params'] e
				'\/page-$1' => array(
					'page=$1', 
					'([0-9]+)'
				),
				
				
				В масива $lCurrentParamReplacement 1ят елемент($lCurrentParamReplacement[0]) е цялостния reg-exp за текущият параметър,
				докато следващите елементи са reg-exp-овете на подпараметрите в този reg exp ($1, $2 ... )
			*/
			
			/**
				Ако има подчасти те се заместват в главния reg exp
				За горния пример $lCurrentParamReplacement[0]  става page=([0-9]+)
			*/
			
			if ($lCurrentParamReplacement[1]) 
				$lCurrentParamPattern = str_replace('$1', $lCurrentParamReplacement[1], $lCurrentParamPattern);
			if (count($lCurrentParamReplacement) > 1) {
				for ($i = 1; $i < count($lCurrentParamReplacement); $i ++) {
					if ($lCurrentParamReplacement[$i]) 
						$lCurrentParamPattern = str_replace(('$' . $i), $lCurrentParamReplacement[$i], $lCurrentParamPattern);
				}
			}			
			if (preg_match('/' . $lCurrentParamPattern . '/', $lOldUrl, $m, PREG_OFFSET_CAPTURE)) {				
				$string = preg_replace('/' . $lCurrentParamPattern . '/', $lCurrentParamReplacement[0], $m[0][0]);
				$lNewUrl .= '&' . $string;
				$lOldUrl = substr($lOldUrl, 0, (int)$m[0][1]) . substr($lOldUrl, (int)$m[0][1] + (int)strlen($m[0][0]));
				if (!$lOldUrl) 
					break;
			}
		}
		if ($lNewUrl) {
			$lNewUrlParams = preg_split('/&/', $lNewUrl, -1, PREG_SPLIT_NO_EMPTY);
			if (count($lNewUrlParams)) {
				foreach ($lNewUrlParams as $lCurrentParam) {
					$lCurrentParamArr = explode('=', $lCurrentParam);//Masiv s 2 elementa - 1q e key , 2q - value
					if (count($lCurrentParamArr) == 2) {
						$lCurrentParamKey = $lCurrentParamArr[0];
						$lCurrentParamValue = $lCurrentParamArr[1];
						$_REQUEST[$lCurrentParamKey] = $lCurrentParamValue;
					}
				}
			}
			unset($_REQUEST['rwr']);
		}
	}
	
	function EncodeUrl($pUrl, $pTranslate = false) {
		if (!defined('ENABLE_MOD_REWRITE') || (int)ENABLE_MOD_REWRITE == 0) {
			return $pUrl;
		}
		if ($pTranslate) {
			$lTranslit = new ctransliteration();
			$pUrl = $lTranslit->bg2en($pUrl);
			$pUrl = strtolower($pUrl);			
		}
		
				
		
		//Разделяме подадения url на скрипт/параметри
		$lUrlData = explode('?', $pUrl);
		$lUrlScript = $lUrlData[0];
		if (count($lUrlData) > 2) {
			unset($lUrlData[0]);//Mahame script-a
			$lUrlQueryString = implode(urlencode('?'), $lUrlData); // %3F = ? urlencoded
		} else {
			$lUrlQueryString = $lUrlData[1];
		}
		
		$lUrlMap = $this->map[$lUrlScript];
		if (!$lUrlMap) 
			return $pUrl;
		/**
			Масивът main е във формат
				условие, което като се изпълни връща true/false => ново url
			Вървим по всички елементи на main масива, и което условие се изпълни - това е новото урл
		*/
		if (is_array($lUrlMap['main'])) {
			$params = $lUrlQueryString;//Za backward compatibility
			foreach ($lUrlMap['main'] as $condition => $main) {
				$found = false;
				eval('$found = (boolean)(' . $condition . ');');
				if ($found) {
					$newurl = $main;
					break;
				}
			}
		} else {
			$newurl = $lUrlMap['main'];
		}
		
		if ($lUrlQueryString) {
			$lCurrentParamsPos = 0;
			$new_params = array();
			if( !is_array($lUrlMap['params']))
				$lUrlMap['params'] = array();
			/**
				За всеки от параметрите се обработва реалната стоиност на този параметър до стринг
				в който ще е част от обработения url
				Пример:
				От параметрите на входния урл са ?page=365&title=Търговски обекти
				и сега обработваме параметъра page 
				масивът $lUrlMap['params'] e
				'\/page-$1' => array('page=$1', '([0-9]+)'),
				
				В случая мачваме параметъра page със reg expa ([0-9]+)
			*/
			foreach ($lUrlMap['params'] as $lCurrentParamReplacement => $lCurrentParamPattern) {
				/**
					В масива $lCurrentParamPattern 1ят елемент($lCurrentParamPattern[0]) е цялостния reg-exp за текущият параметър,
					докато следващите елементи са reg-exp-овете на подпараметрите в този reg exp ($1, $2 ... )
				*/
				
				/**
					Ако има подчасти те се заместват в главния reg exp
					За горния пример $lCurrentParamPattern[0]  става page=([0-9]+)
				*/
				if (count($lCurrentParamPattern) > 1) {
					for ($i = 1; $i < count($lCurrentParamPattern); $i ++) {
						if ($lCurrentParamPattern[$i]) 
							$lCurrentParamPattern[0] = str_replace(('$' . $i), $lCurrentParamPattern[$i], $lCurrentParamPattern[0]);
					}
				}
				
				if (count($lCurrentParamPattern) > 2) {					
					/**
						$new_params[$new_pos] - частта от стринга на която отговаря целия reg-exp;
						В примера горе 
							page=365
					*/
					if (preg_match('/' . $lCurrentParamPattern[0] . '/', $lUrlQueryString, $m)) {
						$new_params[$lCurrentParamsPos] = stripslashes(preg_replace('/' . $lCurrentParamPattern[0] . '/', $lCurrentParamReplacement, $m[0]));
					}					
				} else {//Ако няма подчасти - не се прави preg-match
					$lUrlMap['params'][$lCurrentParamReplacement][0] = $lCurrentParamPattern[0];
				}
				$lCurrentParamsPos++;
			}
			
			/**
				В new_params стоят стойностите на параметрите, които са намерени в query стринга. Това се прави ако се прави regexp на няколко параметъра от query стринга наведнъж.
				После се обработват всички параметри от query стринга един по един и за всеки от тях се гледат 1 по 1 pattern-ите за параметрите.
				Ако някой pattern мачне за един параметър се сменя стойността в $new_params. Т.е. ако pattern-а мачне в/у 1 параметър, това мачване премахва мачването върху целия
				query стринг					
			*/
			
			
			$lUrlQueryString = preg_replace('/&{2,}/', '&', $lUrlQueryString);
			
			/**
				Масив в който всеки елемент представлява параметър от query стринг-а на подаденото url
				елементите имат следния формат
					име=стойност
						където име е името параметъра от query стринг-а, а стойност - съответстващата му стойност
			*/
			$lUrlQueryStringArray = explode('&', $lUrlQueryString);
			
			/**
				$lUrlMap['replace'] - масив който определя от новото урл, кои неща да се сменят с параметри от query стринг-а на подаденото url
				Форматът е:
					pattern за мач в новото url => име на параметъра от query стринг-а
				
				Например ако 
					ново url - /users/$4_$2/admin_groups/
					query стринг uid=100&name=Test
					$lUrlMap['replace'] => array('
						'name' => '$2',						
						'uid' => '$4',
					),
				след обработката на всички елементи от масива новото url ще стане
					/users/100_Test/admin_groups/
				
				След като се намери replace за даден параметър от query стринга - той се маха от масива $lUrlQueryStringArray
			*/
			if ($lUrlMap['replace']) {
				$fordel = array();
				foreach ($lUrlQueryStringArray as $k => $par) {
					foreach ($lUrlMap['replace'] as $rep_key => $rep_val) {
						if (preg_match('/^' . $rep_key . '=(.*)/', $par, $m)) {
							$newurl = str_replace($rep_val, $m[1], $newurl);
							$fordel[$k] = $k;
						}
					}
				}
				foreach ($fordel as $k => $v) 
					unset($lUrlQueryStringArray[$k]);
			}
			
			foreach ($lUrlQueryStringArray as $k => $par) {
				$lCurrentParamsPos = 0;
				foreach ($lUrlMap['params'] as $lCurrentParamReplacement => $lCurrentParamPattern) {
					if (preg_match('/^' . $lCurrentParamPattern[0] . '/', $par, $m)) {
						$new_params[$lCurrentParamsPos] = stripslashes(preg_replace('/' . $lCurrentParamPattern[0] . '/', $lCurrentParamReplacement, $m[0]));
						break;
					}
					$lCurrentParamsPos ++;
				}
			}
			
			if (count($new_params)) {//Намерените параметри се добавят накрая към новото url, в реда на изреждането им в $lUrlMap['params']
				ksort($new_params);
				$newurl .= implode('', $new_params);
			}
		}
		// Remove duplicate slashes
		$newurl = urldecode($newurl);
		$newurl = preg_replace("/\/+/", "/", $newurl);
		
		/**
		Сменяме определени символи само ако е парснато урл-то за да може да не счупим някой от старите линкове
		*/
		$newurl = str_replace(' ', REWRITE_SPACE_REPLACEMENT, $newurl);
		$newurl = str_replace('_', REWRITE_UNDERSCORE_REPLACEMENT, $newurl);
		$newurl = str_replace('"', REWRITE_DOUBLE_QUOTES_REPLACEMENT, $newurl);
		$newurl = str_replace('\'', REWRITE_SINGLE_QUOTES_REPLACEMENT, $newurl);
		//Махаме специалните символи - тук работим с релативни url-и и спокойно можем да махнем :
		$newurl = preg_replace('/[\&\$\+\,\:\;\=\?\@\<\>\[\]|\~\^\#\%]/i', '', $newurl);
		
		return $newurl;
	}
}

?>