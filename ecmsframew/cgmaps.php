<?php

/**
 * Embed Google Maps
 * 
 * @copyright 2009 Etaligent.NET
 * @author    Boril Yordanov <boril@etaligent.net>
 * @date      24 Jun 2009
 * @version   $Id: cgmaps.php,v 1.3 2010/07/12 13:00:16 boro Exp $
 */
class cgmaps extends cbase {
	protected $mApiKey; // Generate from http://code.google.com/apis/maps/signup.html
	protected $mApiJS;
	protected $mMapCenter;
	protected $mPins;
	protected $mPinsJS;
	protected $mGlobalJS;
	protected $mMapProps;
	protected $mDivAttribsArr;
	protected $mDivAttribs;
	protected $mLanguage;
	protected $mSensor;
	protected $mDontGetData;
	protected $m_Templs;
	protected $m_defTempls;
	
	/**
	 * Class Constructor
	 * 
	 * @param  array $pFieldTempl
	 * @access public
	 * @return void
	 */
	function __construct($pFieldTempl) {
		$this->mMapCenter = array('lat' => 0, 'lng' => 0, 'zoom' => 0);
		$this->mPins = array();
		$this->mMapProps = array();
		$this->mDivAttribsArr = array();
		$this->mLanguage = 'en';
		$this->mSensor = 'false';
		$this->mDontGetData = false;
		
		$this->m_pubdata = array_change_key_case($pFieldTempl, CASE_LOWER);
		$this->m_Templs = $this->m_pubdata['templs'];
		$this->loadDefTempls();
		if (isset($this->m_pubdata['lang']) && strlen($this->m_pubdata['lang']) == 2)
			$this->mLanguage = $this->m_pubdata['lang'];
		if (isset($this->m_pubdata['sensor']))
			$this->mSensor = (int)$this->m_pubdata['sensor'] ? 'true' : 'false';
		
		$this->loadApiJS($this->m_pubdata['apikey']);
		$this->setCenter($this->m_pubdata['mapcenter']);
		$this->setPins($this->m_pubdata['pins']);
		$this->setMapProps($this->m_pubdata['mapprops']);
	}
	
	/**
	 * Load the default templates to empty one
	 * 
	 * @access public
	 * @return void
	 */
	function loadDefTempls() {
		if (!defined('D_EMPTY')) {
			define('D_EMPTY', 'global.empty');
		}
		
		$this->m_defTempls = array(G_DEFAULT => D_EMPTY);
	}
	
	/**
	 * Load the holder div attributes
	 * 
	 * @access public
	 * @return void
	 */
	function loadDivAttribs() {
		if (is_array($this->m_pubdata['divattribs'])) {
			$this->mDivAttribsArr = $this->m_pubdata['divattribs'];
		}
		if (!isset($this->mDivAttribsArr['id'])) {
			$this->mDivAttribsArr['id'] = 'map_canvas';
		}
		if (!isset($this->mDivAttribsArr['style'])) {
			$this->mDivAttribsArr['style'] = 'width: 600px; height: 300px;';
		}
		foreach ($this->mDivAttribsArr as $key => $val) {
			$this->mDivAttribs .= ' ' . $key . '="' . $val . '"';
		}
	}
	
	/**
	 * Load apikey JavaScript code
	 * 
	 * @param  string $pApiKey - an 86 characters string
	 * @access public
	 * @return void
	 */
	function loadApiJS($pApiKey) {
		if ($pApiKey && strlen($pApiKey) == 86) {
			$this->mApiKey = $pApiKey;
			$this->mApiJS = '<script src="http://maps.google.com/maps?file=api&amp;v=2&amp;key=' . $this->mApiKey . '&sensor=' . $this->mSensor . '&hl=' . $this->mLanguage . '" type="text/javascript"></script>' . "\n";
		} else {
			trigger_error('"' . $pApiKey . '" is not valid api key.', E_USER_ERROR);
		}
	}
	
	/**
	 * Load pin's JavaScript code
	 * 
	 * @access public
	 * @return void
	 */
	function loadPinsJS() {
		$lPinsNum = count($this->mPins);
		$lJS = '';
		if ((int)$this->m_pubdata['geocoder'] == true) {
			$lGeocoder = '
					var lGmapsObj = maps["' . $this->mDivAttribsArr['id'] . '"].gmapsobj;
					var lGeoCoder = maps["' . $this->mDivAttribsArr['id'] . '"].geocoder;
					lGeoCoder = new GClientGeocoder();
					GEvent.addListener(lGmapsObj, "click", function getAddress(overlay, latlng) {
						if (latlng != null) {
							address = latlng;
							lGeoCoder.getLocations(latlng, function showAddress(response) {
								lGmapsObj.clearOverlays();
								if (!response || response.Status.code != 200) {
									alert("Status Code:" + response.Status.code);
								} else {
									place = response.Placemark[0];
									point = new GLatLng(place.Point.coordinates[1],
									place.Point.coordinates[0]);
									marker = new GMarker(point);
									lGmapsObj.addOverlay(marker);
									marker.openInfoWindowHtml(
										"<b>orig latlng:</b>" + response.name + "<br />" + 
										"<b>latlng:</b>" + place.Point.coordinates[1] + "," + place.Point.coordinates[0] + "<br />" +
										"<b>Status Code:</b>" + response.Status.code + "<br />" +
										"<b>Status Request:</b>" + response.Status.request + "<br />" +
										"<b>Address:</b>" + place.address + "<br />" +
										"<b>Accuracy:</b>" + place.AddressDetails.Accuracy + "<br />" +
										"<b>Country code:</b> " + place.AddressDetails.Country.CountryNameCode
									);
								}
							});
						}
					});
			';
		}
		
		if ($lPinsNum >= 1) {
			$lJS = '
			<script type="text/javascript">
				var ' . $this->mDivAttribsArr['id'] . '_pins = [
			';
			foreach($this->mPins as $key => $val) {
				$lJS .= "\n\t\t\t\t\t[" . $val['lat'] .  ', ' . $val['lng'] . ', ' . '\'' . addslashes($val['title']) . '\'' . ($val['icon'] ? ', \'' . $val['icon'] .  '\'' : '') . ($key == ($lPinsNum - 1) ? ']' : '],');
			}
			
			$lJS .= '
				];
				windowOnLoad(function() {
					maps["' . $this->mDivAttribsArr['id'] . '"] = loadGMap("' . $this->mDivAttribsArr['id'] . '", ' . $this->mMapCenter['lat'] . ', ' . $this->mMapCenter['lng'] . ', ' . $this->mMapCenter['zoom'] . ', ' . $this->mDivAttribsArr['id'] . '_pins);
					' . (!empty($this->mMapProps) ? implode("\n\t\t\t\t\t", $this->mMapProps) : '') . '
					' . ($lGeocoder ? $lGeocoder : '') . '
				});
				window.onunload=GUnload;
			</script>
			';
		}
		$this->mPinsJS = $lJS;
	}
	
	/**
	 * Set map pins
	 * 
	 * @param  array $pPinsArr - should be array of arrays, even it is only one pin
	 * @param  bool $pDontLoadPinsJS - do not recreate pin's JavaScript code (default false)
	 * @access public
	 * @return void
	 */
	function setPins($pPinsArr, $pDontLoadPinsJS = false) {
		$this->mPins = array(); // Reset the pins
		$lAllowedAttribs = array(
			'lat' => 'is_numeric',
			'lng' => 'is_numeric',
			'title' => array('is_string', 'is_array'),
			'icon' => 'is_string',
		);
		
		if (is_array($pPinsArr)) {
			foreach($pPinsArr as $key => $val) {
				foreach ($val as $k => $v) {
					if (array_key_exists($k, $lAllowedAttribs)) {
						if (is_array($lAllowedAttribs[$k])) {
							$lCondition = false;
							foreach ($lAllowedAttribs[$k] as $eval) {
								eval('$lCondition = (bool)($lCondition | (bool)' . $eval . '($v));');
							}
						} else {
							eval('$lCondition = (bool)' . $lAllowedAttribs[$k] . '($v);');
						}
						if ($lCondition) {
							if (is_array($v)) {
								if (array_key_exists(getlang(true), $v)) {
									$this->mPins[$key][$k] = $v[getlang(true)];
								} else {
									$this->mPins[$key][$k] = current($v);
								}
							} else {
								$this->mPins[$key][$k] = $v;
							}
						} else {
							trigger_error('Attribute "' . $k . '" should match one of the following conditions: ' . implode(', ', $lAllowedAttribs[$k]), E_USER_ERROR);
						}
					}
				}
			}
		} else {
			trigger_error(get_class($this) . ' expects from pins to be an array.', E_USER_ERROR);
		}
		
		if (!$pDontLoadPinsJS)
			$this->loadPinsJS();
	}
	
	/**
	 * Get map pins
	 * 
	 * @access public
	 * @return array
	 */
	function getPins() {
		return $this->mPins;
	}
	
	/**
	 * Get pin's JavaScript code
	 * 
	 * @access public
	 * @return string
	 */
	function getPinsJS() {
		if (!$this->mDontGetData) {
			return $this->mPinsJS;
		}
		return '';
	}
	
	/**
	 * Build global JavaScript code
	 * 
	 * @access public
	 * @return void
	 */
	function buildGlobalJS() {
		$this->loadDivAttribs();
		
		$lGlobalJS = '
			<script type="text/javascript">
				function windowOnLoad(f) {
					var curLoad = window.onload;
					window.onload = function() {
						if (curLoad)
							curLoad();
						f();
					}
				}
				
				var maps = new Array();
				function loadGMap(canvasId, lat, lng, zoom, pins) {
					if (GBrowserIsCompatible()) {
						var gmapsobj = new GMap2(document.getElementById(canvasId));
						var geocoder;
						var markers = new Array();
						var htmls = new Array();
						
						gmapsobj.setCenter(new GLatLng(lat, lng), zoom);
						function createMarker(latlng, myHtml, markerOptions) {
							var marker = new GMarker(latlng, markerOptions);
							marker.value = myHtml;
							GEvent.addListener(marker, "click", function() {
								gmapsobj.openInfoWindowHtml(latlng, myHtml);
							});
							return marker;
						}
						
						var latLngs = new Array();
						var markerOpts = new Array();
						for (i = 0; i < pins.length; i++) {
							if (pins[i][3] !== undefined) {
								var customIcon = new GIcon(G_DEFAULT_ICON);
								customIcon.image = pins[i][3];
								markerOpts = { icon:customIcon };
							}
							
							htmls[i] = pins[i][2];
							latLngs[i] = new GLatLng(pins[i][0], pins[i][1]);
							markers[i] = createMarker(latLngs[i], pins[i][2], markerOpts);
							gmapsobj.addOverlay(markers[i]);
						}
						
						return {
							"id" : canvasId,
							"gmapsobj" : gmapsobj,
							"geocoder" : geocoder,
							"pins" : pins,
							"markers" : markers,
							"htmls" : htmls
						};
					}
					return {};
				}
				
				function locateToMarker(mapId, markerIdx) {
					maps[mapId].markers[markerIdx].openInfoWindowHtml(maps[mapId].htmls[markerIdx]);
				}
			</script>
		';
		
		$this->loadPinsJS();
		$this->mGlobalJS = $this->mApiJS . $lGlobalJS . $this->mPinsJS;
	}
	
	/**
	 * Get global JavaScript code
	 * 
	 * @access public
	 * @return string
	 */
	function getGlobalJS() {
		if (!$this->mDontGetData) {
			return $this->mGlobalJS;
		}
		return '';
	}
	
	/**
	 * Get map center
	 * 
	 * @access public
	 * @return array
	 */
	function getCenter() {
		return $this->mMapCenter;
	}
	
	/**
	 * Set map center
	 * 
	 * @param  array $pMapCenter - latitude, lngitude, zoom
	 * @access public
	 * @return void
	 */
	function setCenter($pMapCenter) {
		if (is_array($pMapCenter) && count($pMapCenter) == 3) {
			$lAllowedAttribs = array(0 => 'lat', 1 => 'lng', 2 => 'zoom');
			
			foreach ($pMapCenter as $key  => $val) {
				if (is_numeric($val)) {
					if (in_array($key, $lAllowedAttribs, true))
						$this->mMapCenter[$key] = $val;
					else if (array_key_exists($key, $lAllowedAttribs))
						$this->mMapCenter[$lAllowedAttribs[$key]] = $val;
					else
						trigger_error('Allowed attributes for mapcenter are: ' . implode(', ', $lAllowedAttribs), E_USER_ERROR);
				} else {
					trigger_error('"' . $val . '" should be a numeric value.', E_USER_ERROR);
				}
			}
		} else {
			trigger_error(__METHOD__ . ' expects array as parameter.', E_USER_ERROR);
		}
		$this->buildGlobalJS();
	}
	
	/**
	 * Set map properties
	 * 
	 * @param  array $pMapProps - additional map properties
	 * @access public
	 * @return void
	 */
	function setMapProps($pMapProps) {
		if (is_array($pMapProps)) {
			foreach ($pMapProps as $val) {
				$this->mMapProps[] = 'maps["' . $this->mDivAttribsArr['id'] . '"].gmapsobj.' . $val . ';';
			}
		}
		$this->buildGlobalJS();
	}
	
	/**
	 * Get map properties
	 * 
	 * @access public
	 * @return array
	 */
	function getMapProps() {
		return $this->mMapProps;
	}
	
	/**
	 * Prevent Display and getGlobalJS methods from getting data
	 * 
	 * @access public
	 * @return void
	 */
	function dontGetData() {
		$this->mDontGetData = true;
	}
	
	/**
	 * Abstract method inherited from cbase
	 * 
	 * @access public
	 * @return void
	 */
	function GetData() {
		$this->mDontGetData = false;
		$this->buildGlobalJS();
	}
	
	/**
	 * Abstract method inherited from cbase
	 * 
	 * @access public
	 * @return void
	 */
	function CheckVals() {
		return;
	}
	
	/**
	 * Display HTML embed code
	 * Abstract method inherited from cbase
	 * 
	 * @access public
	 * @return string
	 */
	function Display() {
		if (!$this->mDontGetData) {
			$this->GetData();
			$this->m_pubdata['googlemaps'] = '<div ' . $this->mDivAttribs . '></div>';
		}
		
		return $this->ReplaceHtmlFields($this->getObjTemplate(G_DEFAULT));
	}
}

?>