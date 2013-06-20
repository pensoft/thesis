<?php

class ctransliteration {
	var $Patterns = array();
	var $enReplacements = array();
	private $mExceptionWords = array();

	function __construct() {
		array_push($this->Patterns, '/а|А/'); array_push($this->enReplacements, 'a');
		array_push($this->Patterns, '/б|Б/'); array_push($this->enReplacements, 'b');
		array_push($this->Patterns, '/в|В/'); array_push($this->enReplacements, 'v');
		array_push($this->Patterns, '/г|Г/'); array_push($this->enReplacements, 'g');
		array_push($this->Patterns, '/д|Д/'); array_push($this->enReplacements, 'd');
		array_push($this->Patterns, '/е|Е/'); array_push($this->enReplacements, 'e');
		array_push($this->Patterns, '/ж|Ж/'); array_push($this->enReplacements, 'j');
		array_push($this->Patterns, '/з|З/'); array_push($this->enReplacements, 'z');
		array_push($this->Patterns, '/и|И/'); array_push($this->enReplacements, 'i');
		array_push($this->Patterns, '/й|Й/'); array_push($this->enReplacements, 'i');
		array_push($this->Patterns, '/к|К/'); array_push($this->enReplacements, 'k');
		array_push($this->Patterns, '/л|Л/'); array_push($this->enReplacements, 'l');
		array_push($this->Patterns, '/м|М/'); array_push($this->enReplacements, 'm');
		array_push($this->Patterns, '/н|Н/'); array_push($this->enReplacements, 'n');
		array_push($this->Patterns, '/о|О/'); array_push($this->enReplacements, 'o');
		array_push($this->Patterns, '/п|П/'); array_push($this->enReplacements, 'p');
		array_push($this->Patterns, '/р|Р/'); array_push($this->enReplacements, 'r');
		array_push($this->Patterns, '/с|С/'); array_push($this->enReplacements, 's');
		array_push($this->Patterns, '/т|Т/'); array_push($this->enReplacements, 't');
		array_push($this->Patterns, '/у|У/'); array_push($this->enReplacements, 'u');
		array_push($this->Patterns, '/ф|Ф/'); array_push($this->enReplacements, 'f');
		array_push($this->Patterns, '/х|Х/'); array_push($this->enReplacements, 'h');
		array_push($this->Patterns, '/ц|Ц/'); array_push($this->enReplacements, 'c');
		array_push($this->Patterns, '/ч|Ч/'); array_push($this->enReplacements, 'ch');
		array_push($this->Patterns, '/ш|Ш/'); array_push($this->enReplacements, 'sh');
		array_push($this->Patterns, '/щ|Щ/'); array_push($this->enReplacements, 'sht');
		array_push($this->Patterns, '/ъ|Ъ/'); array_push($this->enReplacements, 'y');
		array_push($this->Patterns, '/ь|Ь/'); array_push($this->enReplacements, 'y');
		array_push($this->Patterns, '/ьо|Ьо/'); array_push($this->enReplacements, 'io');
		array_push($this->Patterns, '/ю|Ю/'); array_push($this->enReplacements, 'u');
		array_push($this->Patterns, '/я|Я/'); array_push($this->enReplacements, 'ya');
		
		//Macedonian
		array_push($this->Patterns, '/ѓ|Ѓ/'); array_push($this->enReplacements, 'gy');
		array_push($this->Patterns, '/љ|Љ/'); array_push($this->enReplacements, 'ly');
		array_push($this->Patterns, '/њ|Њ/'); array_push($this->enReplacements, 'ny');
		array_push($this->Patterns, '/ќ|Ќ/'); array_push($this->enReplacements, 'ky');
		array_push($this->Patterns, '/џ|Џ/'); array_push($this->enReplacements, 'j');
		array_push($this->Patterns, '/ы|Ы/'); array_push($this->enReplacements, 'i');
		
		//Russian
		array_push($this->Patterns, '/ё|Ё/'); array_push($this->enReplacements, 'yo');
		array_push($this->Patterns, '/э|Э/'); array_push($this->enReplacements, 'e');
		
		
		$lLexWordsObj = new ctranslexwords(
			array(
				'cache' => 'translexwords',
			)
		);
		$this->mExceptionWords = unserialize($lLexWordsObj->DisplayC());
	}

	function bg2en($string) {
		$words  = explode(' ', $string);
		foreach($words as $key => $word) {
			$words[$key] = preg_replace("/(\p{Cyrillic}*)/e", "\$this->translateWord('\\1')", $word);
		}
		$string = implode(' ', $words);
		
		return $string;
	}
	
	function translateWord($pWord) {
		if (is_array($this->mExceptionWords) && array_key_exists(mb_strtolower($pWord, 'UTF-8'), $this->mExceptionWords)) {
			$lTranslatedWord = $this->mExceptionWords[mb_strtolower($pWord, 'UTF-8')];
		} else {
			$lTranslatedWord = preg_replace($this->Patterns, $this->enReplacements, $pWord);
		}
		
		return $lTranslatedWord;
	}
}
?>