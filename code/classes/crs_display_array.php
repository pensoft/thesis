<?php
/**
	Kato crs samo 4e vmesto da pravi konekciq izpolzva vhoden masiv kato rezultat ot izpylnenieto na zaqvkata
	T.e. vhodniq masiv trqbva da e masiv ot masivi(koito sa redovete na praktika - tuk key-a e imeto na poleto a valueto si e stoinostta)
*/
class crs_display_array extends crs_array {
	
	function GetData() {
		$this->CheckVals();
		if ($this->m_state >= 1) {
			$lInputArr = $this->m_pubdata['input_arr'];
			if( !is_array( $lInputArr ) )
				$lInputArr = array();
			$this->m_recordCount = count($lInputArr);
			
			$lCurrentRow = 0;
			$lMaxPages = 0;
			if($this->m_pageSize)
				$lMaxPages = ceil($this->m_recordCount / $this->m_pageSize);
			//Ako e podaden parametyr za stranica - po-golqma ot poslednata - otivame na poslednata
			if( $this->m_page >= $lMaxPages )
				$this->m_page = max(0, $lMaxPages - 1);
				
			foreach($lInputArr as $lRow ){//Vzimame samo rezultatite ot syotvetniq page
				$lCurrentRow++;				
				if( $this->m_pageSize ){//Ako ima stranicirane
					if( $lCurrentRow <= ($this->m_pageSize * $this->m_page))//Predhodni redove
						continue;
					if( $lCurrentRow > ($this->m_pageSize * ($this->m_page + 1)))//Sledvashti redove
						break;
				}
				
				$this->m_resultArr[] = $lRow;
			}			
			$this->m_pubdata['records'] = $this->m_recordCount;			
			$this->m_state++;
			if ($this->m_recordCount) {
				$this->m_pubdata['rownum'] = 1;
				foreach ($this->m_resultArr[0] as $k => $v) {
					$this->m_pubdata[$k] =$this->m_currentRecord[$k] = $v;
				}
			}
		}
	}	
}

?>