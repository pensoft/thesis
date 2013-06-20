<?php

/**
 * A model to implement journal prices functionality
 */
class mJournal_Prices_Model extends emBase_Model {

	function GetJournalPrices($pJournalId) {
		$lResult = array();
		$lCon = $this->m_con;
		$lSql = 'SELECT * FROM pjs.journal_prices 
					WHERE journal_id = ' . (int)$pJournalId . 
					'ORDER BY range_start';
		$lCon->Execute($lSql);
		while(!$lCon->Eof()){
			$lResult[] = $lCon->mRs;
			$lCon->MoveNext();
		}
		return $lResult;
	}
	function CalculateAutomaticPrice($pJournalId, $pStartPage, $pEndPage, $pColorPage) {
		$lResult = array();
		$lCon = $this->m_con;
		$lSql = 'SELECT price FROM pjs.spManageDocumentPrices(3, null, ' . $pStartPage . ', ' . $pEndPage . ', null, null, ' . $pJournalId . ', null) as price';
		//~ echo $lSql;
		if(!$lCon->Execute($lSql)){
			$lResult['err_cnt']++;
			$lResult['err_msgs'][] = array('err_msg' => $lCon->GetLastError());
		} else {
			$lResult['price'] = $lCon->mRs['price'];
		}
		return $lResult;
	}
	
	function SaveJournalPrices($pJournalId, $pRangesStart, $pRangesEnd, $pPrices) {
		$lRangesStart = array();
		$lRangesEnd = array();
		$lPrices = array();
		if( is_array($pRangesStart) && is_array($pRangesEnd) && is_array($pPrices)){
			$lIsLast = 0;
			for($i = 0; $i < count($pRangesStart); $i++){//mahame seki red koito ima pone 1 prazen element ili takyv koito ne minava validaciq
				if(count($pRangesStart) - 1 == $i)
					$lIsLast = 1;
				if(!(int)$pRangesStart[$i] || !(int)$pRangesEnd[$i] || !is_numeric($pPrices[$i]))
					continue;
				$lArray[] = array((int)$pRangesStart[$i], (int)$pRangesEnd[$i], $pPrices[$i], $lIsLast);
			}
			// Sortirame masiva po range start
			$lSortedArray = $this->MultiSort($lArray, 0, 1, 2, 3);
			
			$lPrevElement = 0;
			$lIsNewElement = 0;
			foreach($lSortedArray as $k => $v){ // Proverqvame za elementi, koito se zasichat (start 1, end 3) se zasicha s (start 2, end 8)
				if($v[0] <= $lPrevElement || $v[0] > $v[1]){
					if((int)$lIsNewElement){
						array_pop($lRangesStart);
						array_pop($lRangesEnd);
						array_pop($lPrices);
					}else{
						continue;
					}
				}
				$lPrevElement = $v[1];
				$lIsNewElement = $v[3];
				
				$lRangesStart[] = (int)$v[0];
				$lRangesEnd[]   = (int)$v[1];
				$lPrices[]      = $v[2];
			}
			if(!(count($lRangesStart) > 0)){
				$lRangesStart = 'null';
				$lRangesEnd = 'null';
				$lPrices = 'null';
			}else{
				$lRangesStart = 'ARRAY[' . implode(',', $lRangesStart) . ']';
				$lRangesEnd = 'ARRAY[' . implode(',', $lRangesEnd) . ']';
				$lPrices = 'ARRAY[' . implode(',', $lPrices) . ']';
			}
			$lResult = array();
			$lCon = $this->m_con;
			$lSql = 'SELECT * FROM spsavejournalstorydata(' . (int)$pJournalId . ', ' . $lRangesStart . ', ' . $lRangesEnd . ', ' . $lPrices . ')';
			$lCon->Execute($lSql);
		}
	}
	
	function MultiSort($pArray, $pSortBy, $pKey1, $pKey2, $pKey3){
		foreach ($pArray as $pos =>  $val)
			$tmp_array[$pos] = $val[$pSortBy];
		asort($tmp_array);
		
		foreach ($tmp_array as $pos =>  $val){
			$return_array[$pos][$pSortBy] = $pArray[$pos][$pSortBy];
			$return_array[$pos][$pKey1] = $pArray[$pos][$pKey1];
			$return_array[$pos][$pKey2] = $pArray[$pos][$pKey2];
			$return_array[$pos][$pKey3] = $pArray[$pos][$pKey3];
		}	
		return $return_array;
    }
}
?>