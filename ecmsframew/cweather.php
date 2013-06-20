<?php

class cweather extends cbase {

	var $ycodes = array(
		0 => array('tornado', 'weather-severe-alert.gif'),
		1 => array('tropical storm', 'weather-severe-alert.gif'),
		2 => array('hurricane', 'weather-severe-alert.gif'),
		3 => array('severe thunderstorms', 'weather-storm.gif'),
		4 => array('thunderstorms', 'weather-storm.gif'),
		5 => array('mixed rain and snow', 'weather-snow.gif'),
		6 => array('mixed rain and sleet', 'weather-snow.gif'),
		7 => array('mixed snow and sleet', 'weather-snow.gif'),
		8 => array('freezing drizzle', 'weather-snow.gif'),
		9 => array('drizzle', 'weather-snow.gif'),
		10 => array('freezing rain', 'weather-showers.gif'),
		11 => array('showers', 'weather-showers.gif'),
		12 => array('showers', 'weather-showers.gif'),
		13 => array('snow flurries', 'weather-snow.gif'),
		14 => array('light snow showers', 'weather-snow.gif'),
		15 => array('blowing snow', 'weather-snow.gif'),
		16 => array('snow', 'weather-snow.gif'),
		17 => array('hail', 'weather-snow.gif'),
		18 => array('sleet', 'weather-snow.gif'),
		19 => array('dust', 'weather-overcast.gif'),
		20 => array('foggy', 'weather-overcast.gif'),
		21 => array('haze', 'weather-overcast.gif'),
		22 => array('smoky', 'weather-overcast.gif'),
		23 => array('blustery', 'weather-overcast.gif'),
		24 => array('windy', 'weather-overcast.gif'),
		25 => array('cold', 'weather-overcast.gif'),
		26 => array('cloudy', 'weather-overcast.gif'),
		27 => array('mostly cloudy (night)', 'weather-overcast.gif'),
		28 => array('mostly cloudy (day)', 'weather-overcast.gif'),
		29 => array('partly cloudy (night)', 'weather-few-clouds-night.gif'),
		30 => array('partly cloudy (day)', 'weather-few-clouds.gif'),
		31 => array('clear (night)', 'weather-clear-night.gif'),
		32 => array('sunny', 'weather-clear.gif'),
		33 => array('fair (night)', 'weather-clear-night.gif'),
		34 => array('fair (day)', 'weather-clear.gif'),
		35 => array('mixed rain and hail', 'weather-few-clouds.gif'),
		36 => array('hot', 'weather-clear.gif'),
		37 => array('isolated thunderstorms', 'weather-showers.gif'),
		38 => array('scattered thunderstorms', 'weather-showers-scattered.gif'),
		39 => array('scattered thunderstorms', 'weather-showers-scattered.gif'),
		40 => array('scattered showers', 'weather-showers-scattered.gif'),
		41 => array('heavy snow', 'weather-snow.gif'),
		42 => array('scattered snow showers', 'weather-snow.gif'),
		43 => array('heavy snow', 'weather-snow.gif'),
		44 => array('partly cloudy', 'weather-few-clouds.gif'),
		45 => array('thundershowers', 'weather-showers.gif'),
		46 => array('snow showers', 'weather-snow.gif'),
		47 => array('isolated thundershowers', 'weather-showers.gif'),
		3200 => array('not available', 'weather-severe-alert.gif'),
	);

	var $citycodes = array(
		0 => 'BUXX0005', // Sofia
		2 => 'BUXX0007', // Varna
		1 => 'BUXX0004', // Plovdiv
		3 => 'BUXX0001', // Burgas
		//~ 4 => 'BUXX0009', // stara zagora
		4 => 'BUXX0009', // Vidin
		5 => 'BUXX0011', // Ruse
	);
	var $bg = array(
		'Sofia' => 'София',
		'Plovdiv' => 'Пловдив',
		'Varna' => 'Варна',
		'Burgas' => 'Бургас',
		'Vidin' => 'Видин',
		'Rousse' => 'Русе',
	);
	var $parser;
	var $content;
	var $lRet;
	
	function __construct($pFieldTempl) {
		$this->m_pubdata = array_change_key_case($pFieldTempl, CASE_LOWER);
		$this->m_Templs = $this->m_pubdata['templs'];
	}
	
	function make() {
		$this->content .= $this->ReplaceHtmlFields($this->getObjTemplate(G_STARTRS));
		
		foreach ($this->citycodes as $i => $code) {
			if (isset($this->m_pubdata['extractcity']) && (int)$this->m_pubdata['extractcity'] != $i) continue;
			//~ $url = 'http://xml.weather.yahoo.com/forecastrss?p=' . $code . '&u=c';
			$url = PATH_WEATHER . '/forecastrss?p=' . $code . '&u=c';
			if (!is_file($url)) return;
			$xmltxt = file_get_contents($url);
			if ($xmltxt) {
				$doc = new DOMDocument();
				$doc->loadXML($xmltxt);
				
				$this->m_pubdata['city'] = $doc->getElementsByTagName('location')->item(0)->getAttribute('city');
				if ((int)$this->m_pubdata['translate'])
					$this->m_pubdata['city'] = $this->bg[$doc->getElementsByTagName('location')->item(0)->getAttribute('city')];
				
				$forecasts = $doc->getElementsByTagName('forecast');
				
				for ($i = 0; $i < $forecasts->length; $i++) {
					$tst = mktime();
					
					if (strtolower(date('D', $tst)) == strtolower($forecasts->item($i)->getAttribute('day'))) {
						// Dnes
						$this->m_pubdata['dneslow'] = $forecasts->item($i)->getAttribute('low');
						$this->m_pubdata['dneshigh'] = $forecasts->item($i)->getAttribute('high');
						$this->m_pubdata['dnescode'] = $forecasts->item($i)->getAttribute('code');
						if ($this->ycodes[$forecasts->item($i)->getAttribute('code')][1]) {
							$this->m_pubdata['dnescodeimg'] = '<img src="'. $this->m_pubdata['imgurl'] . $this->ycodes[$forecasts->item($i)->getAttribute('code')][1] .'" alt="" />';
						}
						$this->m_pubdata['dnescodelabel'] = $this->ycodes[$forecasts->item($i)->getAttribute('code')][0];
						$this->m_pubdata['dnesrazd'] = '';
						if ($this->m_pubdata['dneslow'] && $this->m_pubdata['dneshigh']) 
							$this->m_pubdata['dnesrazd'] = $this->m_pubdata['razdelitel'];
					}
					
					if (strtolower(date('D', ($tst + (24 * 60 * 60)))) == strtolower($forecasts->item($i)->getAttribute('day'))) {
						// Utre
						$this->m_pubdata['utrelow'] = $forecasts->item($i)->getAttribute('low');
						$this->m_pubdata['utrehigh'] = $forecasts->item($i)->getAttribute('high');
						$this->m_pubdata['utrecode'] = $forecasts->item($i)->getAttribute('code');
						if ($this->ycodes[$forecasts->item($i)->getAttribute('code')][1]) {
							$this->m_pubdata['utrecodeimg'] = '<img src="'. $this->m_pubdata['imgurl'] . $this->ycodes[$forecasts->item($i)->getAttribute('code')][1] .'" alt="" />';
						}
						$this->m_pubdata['utrecodelabel'] = $this->ycodes[$forecasts->item($i)->getAttribute('code')][0];
						$this->m_pubdata['utrerazd'] = '';
						if ($this->m_pubdata['utrelow'] && $this->m_pubdata['utrehigh']) 
							$this->m_pubdata['utrerazd'] = $this->m_pubdata['razdelitel'];
					}
					
				}
				$this->m_pubdata['citycode'] = $code;
				$this->content .= $this->ReplaceHtmlFields($this->getObjTemplate(G_ROW));
			}
			
		}
		$this->content .= $this->ReplaceHtmlFields($this->getObjTemplate(G_ENDRS));
	}

	function CheckVals() {

	}
	
	function GetData() {

	}
	// ---------------------------------------------------------
	
	function Display() {
		$this->make();
		return $this->content;
	}
}

?>