<?php

/*
	Basic model for ecSearch 
	Modul params can be configured by predefining initMembers() 
	By default this class makes either Tsearch query or LIKE query, never both simultaneously.
	Results array is returned by the GetData() function;
	Params are set by predefining the setSearchParams() function. 
	If you need to build custom queries predefine the GetData().
	
	Note GetData() implements caching, so if subsequent calls will result in the same data, unless variable $this->m_State is set again to 1.
*/
class emSearch extends emBase_Model {

	var $m_UseTsearch ;
	var $m_UseLike ;

	var $m_Data ;
	var $m_State ;
	var $m_SqlStr ;
	var $m_SqlStart ;
	var $m_SqlTables ;
	var $m_SqlEnd;
	var $m_TsearchRank ;
	var $m_TsearchWhere ;
	var $m_TsearchHighlight ;
	var $m_LikeWhere ;
	
	var $m_SearchString;
	var $m_Vektors;
	var $m_Fields; 
	var $m_HightlightColumns; 
	var $m_PgSchema ;
	var $m_Mode ; // and, or, exact phrase 
	
	var $m_PageSize ;
	var $m_Page ;
	
	function __construct(){
		parent::__construct();
		
		$this->initMembers();
		
		$this->m_State = 1;
	}
	

	function GetData( $pParams ){
		$this->setSearchParams( $pParams );
	
		if( $this->m_State > 1 ) 
			return $this->m_Data;
			
		/*
			We use Tsearch by default, even if $this->m_UseLike flag is set 
		*/
		if( $this->m_UseLike && !$this->m_UseTsearch ) {
			$this->m_LikeWhere = $this->buildLikeQuery( $this->m_Fields, $this->m_SearchString );
			$lSelWhere = $this->m_LikeWhere;
		} else { 
			$lTsearchRes = $this->ParceT2Query( $this->m_Vektors, $this->m_SearchString, $this->m_PgSchema, $this->m_Mode );
			$this->m_TsearchWhere = $lTsearchRes['tsearchWhere'];
			$lSelWhere = $this->m_TsearchWhere;
		}

		$this->m_SqlStr = $this->m_SqlStart . $this->m_SqlTables .
			' WHERE (' . $lSelWhere . ( $lSelWhere == '' ? 'true' : '' ) . ') ' . $this->m_SqlEnd; 
	

		//~ $this->m_con->SetPage($this->m_PageSize, $this->m_Page); // kogato predadem -1 otiva na poslednata stranica 
		//~ $lPageNum = $this->m_con->mPageNum;
		
		$this->m_con->Execute( $this->m_SqlStr );
		while(!$this->m_con->Eof()){
			$this->m_Data[] = $this->m_con->mRs;
			$this->m_con->MoveNext();
		}
		
		$this->m_State = 2;
		return $this->m_Data;
	}
	
	/*
		Set some default search params 
	*/
	function initMembers(){
		$this->m_Data = array();
		
		$this->m_UseTsearch = true;
		$this->m_UseLike = true;
		$this->m_State = 0;
		$this->m_SqlStr = '';
		$this->m_SqlStart = '';
		$this->m_SqlEnd = '';
		$this->m_PgSchema = 'public.bg';
		$this->m_Mode = 1 ;
		
		$this->m_PageSize = 10;
		$this->m_Page = 0;
	}
	
	/*
		Predefine this function to set search params 
	*/
	function setSearchParams( $pParams ){
		$this->m_SearchString = $pParams['searchstring'];
		
		// sample values 
		$this->m_SqlStart = 'SELECT s.* FROM stories s';
		$this->m_SqlEnd = '';
		
		$this->m_SearchString = 'title das';
		$this->m_Vektors = array('s.title', 's.description');
		$this->m_Fields = array('title', 'description');
		$this->m_Mode = 1 ;
	}
	

	/*
		@pMode :
			case 1: // всички думи
			case 2: // коя да е от думите
			case 3: // точна фраза 
		Връща postgre Tsearch2 wheree clauses 
	*/
	function ParceT2Query( $pVektors, $pSearchString, $pSchema, $pMode, $pUseRanking = false, $pHighlightColumns = false) { 
		$lTsearchWhere = ' FALSE ';
		$lRank = ' NULL ';
		$lHighlighting = '';

		
		if( $this->isEmpty($pSearchString) )
			return array(
			'tsearchWhere' => $lTsearchWhere, 
			'rank' => $lRank, 
		);
			
		$lBuildFlatTsearhResult = $this->BuildFlatTsearh( $pSearchString, $pMode ); 
						
		if ( $pVektors ) {
			$lFieldClauses = array();
			$lFieldRanks = array();
			$lFieldHighlights = array();
			foreach ($pVektors as $fld) {
				$lFieldClauses[] =  $fld . ' @@ to_tsquery(\'' . $pSchema . '\', $$' . 
					$lBuildFlatTsearhResult['quotes'] . $lBuildFlatTsearhResult['vectorSearch'] . $lBuildFlatTsearhResult['quotes'] . '$$)'; 
				
				if( $pUseRanking )
					$lFieldRanks[] = ' rank( ' . $fld . ', plainto_tsquery(\'' . $pSchema . '\', $$' . 
						$lBuildFlatTsearhResult['quotes'] . $lBuildFlatTsearhResult['vectorSearch'] . $lBuildFlatTsearhResult['quotes'] . '$$) )'; 
			}
			
			if( is_array($pHighlightColumns) )
				foreach( $pHighlightColumns as $k => $v ){
					$lFieldHighlights[] = ' headline(\'bg_utf8\', ' . $v . ', plainto_tsquery(\'bg_utf8\', $$' . 
						$lBuildFlatTsearhResult['quotes'] . $lBuildFlatTsearhResult['vectorSearch'] . $lBuildFlatTsearhResult['quotes'] . '$$ ),
						\'StartSel=<em>, StopSel=</em>\' ) as headline_' .  str_replace('.', '', $v) ;
				}
			
			$lTsearchWhere = '(' . implode(' OR ', $lFieldClauses) . ')';
			
			if( $pUseRanking )
				if( count($lFieldRanks) )
					$lRank = '(' . implode(' + ', $lFieldRanks) . ')';
					
			if( count($lFieldHighlights) )
				$lHighlighting = ' ' . implode(', ', $lFieldHighlights) . ' ';
		}
			
		if( $lTsearchWhere == ''  ) $lTsearchWhere = ' TRUE ';

		//~ return $lTsearchWhere;
		return array(
			'tsearchWhere' => $lTsearchWhere, 
			'rank' => $lRank, 
			'highlight' => $lHighlighting, 
		);
		
	}
	
	function BuildFlatTsearh( $pSearchString, $pMode ){
		//~ static $prev_oper = false;
		$prev_oper = false;
		$lResult = array(
			'vectorSearch' => '',
			'quotes' => '',
		);
	
		switch(  (int)$pMode ){
			case 2: // коя да е от думите
				$lLogOper = ' | ';
				$lResult['quotes'] = '"';
			break;
			case 3: // точна фраза 
				$lLogOper = ''; // просто разделя думите с интервал
				$lResult['quotes'] = "'";
			break;
			default :
			case 1: // всички думи
				$lLogOper = ' & ';
				$lResult['quotes'] = '"';
			break;
		}
		
		setlocale(LC_CTYPE, 'bg_BG'); 
		
		// Buildvame tsearch stringa za tursene na dumi
		$lArr1 = array(UnicodeToWin(" и "), UnicodeToWin(" или "), " and ", " or ");
		$lArr2 = array(" & ", " | ", " & ", " | ");
		$lArr3 = array('&', '|', '(', ')');
			
		$txt = WinToUnicode(str_ireplace($lArr1, $lArr2, UnicodeToWin($pSearchString)));
		setlocale(LC_CTYPE, '');
		$res = explode(' ', $txt);
		
			//~ var_dump( $res );
			//~ exit;
							
		$lTmpStr = '';
		if (count($res)) {
			$i = 0;
			foreach ($res as $rrr) {
				if (mb_strlen($rrr, 'UTF-8') == 0) continue;
			
				// vrushta true ako e operaciq
				$this_oper = in_array($rrr, $lArr3);
				if( $this_oper )
					continue;
				if (!$prev_oper && !$this_oper) {
					if ($i) $lTmpStr .= $lLogOper; // ' &'
				}
				if (!$this_oper) {
					// mahame neshtata deto ne sa dumi
					$rrr = str_replace(array('"', ',', ';', '.', "'"), '', $rrr); // махам и single quotes
					// Proverqvame za kusi dumi
					if (mb_strlen($rrr, 'UTF-8') < (int)T2STOPWORD_LEN) {
						// Ako dumata e kusa mahame neq i operaciata predi neq
						$lTmpStr = mb_substr($lTmpStr, 0, (mb_strlen($lTmpStr, 'UTF-8') - 2), 'UTF-8');
						//~ $this->skipwarr[$rrr] = $rrr;
						continue;
					}
				}
				
				$lTmpStr .= ' ' . $rrr;
				$i++;
			}
			
			$lResult['vectorSearch'] = trim(q($lTmpStr));
		}
		
		return $lResult;
	}
	
	function buildLikeQuery($pFields, $pSearchString){
		$lIlikeWhere = '';
		$lLikeStr = array();
		
		if( $this->isEmpty($pSearchString) )
			return $this->actionOnEmptyString();
				
		// Buildvame like stringa za tursene na frazi
		if (preg_match_all('/"[^"]+"/', $pSearchString, $a)) {
			//~ var_dump($a);
			foreach ($a[0] as $stext) {
				$lLikeStr[] = mb_strtolower(str_replace('"', '', $stext), 'UTF-8');
			}
		}
	
		if( is_array($pFields) && !empty($pFields) ){
			foreach ($pFields as $fld) {
				foreach ($lLikeStr as $k => $like) {
					$res2[] = 'lower(' . $fld . ') LIKE \'%' . $like . '%\'';
				}
				if (count($res2))
					$res1[] = '(' . implode(' AND ', $res2) . ')';
			}
			if (count($res1))
				$lIlikeWhere = ' (' . implode(' OR ', $res1) . ')';
		}
				
		return $lIlikeWhere ;
				
	}
	
	// condition for empty string 
	function isEmpty($pString){
		if($pString == '')
			return true;
		
		if(!preg_match('/[\d\w]/isu', $pString))
			return true;
			
		return false;
	}
	
	// default action on empty string 
	function actionOnEmptyString(){
		//~ return 'true';
		return 'FALSE';
	}
	
}


?>