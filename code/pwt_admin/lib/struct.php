<?php

function GetDefaultAccess() {
	return array(
		//~ '/' => 1,
		'/login/' => 1,
	);
}

function GetAccess() {
	global $user;
	if (!$user) {
		return GetDefaultAccess();
	}
	else {
		if (!$user->arrPerm) {
			return GetDefaultAccess();
		}
		else {
			return $user->arrPerm;
		}
	}
}

function GetSitesStruct() {
	$lCn = Con();
	$lCn->Execute('SELECT url, name, substring(url from \'(.*/)[^/]*/$\') as parent, type FROM secsites ORDER BY ord');
	$lCn->MoveFirst();
	while (!$lCn->Eof()) {
		if( $lCn->mRs['type'] == 2)
			$lCn->mRs['parent'] = $lCn->mRs['url'];
		$lTmpArr[$lCn->mRs['parent']][$lCn->mRs['url']] = array( 'name' => $lCn->mRs['name'], 'type' => (int) $lCn->mRs['type']);
		$lCn->MoveNext();
	}
	$lCn->CloseRs();
	
	return $lTmpArr;
	
}

function DisplayMenu($pUrl, $id = '') {
	global $gSiteAccess, $gSiteStruct, $user;
	$disable = false;
	if (!(int)$user->id) $disable = true;
	
	$lRet = '';
	$m = 1;
	
	if (is_array($gSiteStruct[$pUrl])) {
		foreach ($gSiteStruct[$pUrl] as $url => $name) {
			if (!$disable) {
				if ($name['name'][0] == '*' || (!$gSiteAccess[$url] && $name['type'] == 1)) 
					continue;
				$class = '';
				if (preg_match('/^_\d+[_\d+]*/', $id) && is_array($gSiteStruct[$url])) 
					$class = 'class="hassm"';
				if($name['type'] == 1)
					$lRet .= '<li><a href="' . $url . '" ' . $class . '>' . $name['name'] . ' ' . $img . '</a>' . DisplayMenu($url, $id . '_' . $m) . '</li>';
				else //razdelitel
					$lRet .= '<li class="menudelim"></li>';
			} else {
				if ($name['name'][0] == '*') 
					continue;
				$lRet .= '<li class="disable">' . $name['name'] . '</li>';
			}
			$m ++;
		}
	}
	
	if ($lRet) {
		return '<ul id="menu' . $id . '">' . $lRet . '</ul>';
	}
		
	return '';
}

?>