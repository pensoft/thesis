<?php
require_once( "db_creator.php" ) ;
/* doc

className: DBCn

Description: Class za vruzka s bazata danni

Example: nema

*/

if (!defined(port)) define(port , null);
class DBCn {
	var $mhCn;
	var $mhRs;
	var $mRs;
	var $mCurrentRecord;
	var $mLimit;
	var $mFetched;
	var $mRecordCount;
	var $mEof;
	var $mErrMsg;
	var $DB;
	var $DBtype;
	var $Win;
	var $mFetchType;
	var $mPageSize;
	
	function __construct($dbtype=DEF_DBTYPE,$win_encoding=0) {
		$this->mFetched = 0;
		$this->mEof = true;
		$this->DBtype=$dbtype;
		
		if ($win_encoding==1)
			$this->Win=1;
		$this->DB = GetConnectionByType($dbtype);
		//~ if ($dbtype == "postgres")
			//~ $this->DB = new DBCnPostgres();
		//~ if ($dbtype == "oracle")
			//~ $this->DB = new DBCnOracle();
		//~ if ($dbtype == "mysql")
			//~ $this->DB = new DBCnMySql();
		//~ echo "construct";

	}

	function __destruct() {
// 		$this->Close();
	}

	function Open($srv = PGDB_SRV, $db = PGDB_DB, $usr = PGDB_USR, $pass = PGDB_PASS, $port = PGDB_PORT) {
	//function Open($srv = "skylab.etaligent.net", $db = "ropolubo", $usr = "iusrropotamo", $pass = "db^ropotamo?") {		
		$this->mhCn = $this->DB->Connect($srv, $db, $usr, $pass, $port);
		if (!$this->mhCn) {
			trigger_error("Unable to open database connection!", E_USER_NOTICE);
			exit();
		}
		// pg_query($this->mhCn, "SET DateStyle TO 'SQL,European'");
	}

	function Close() {
		if ($this->mhCn) {
			$this->CloseRs();
			if (@$this->DB->Close($this->mhCn)) $this->mhCn = null;
		}
	}

	function CloseRs() {
		if ($this->mhRs) {
			$this->mRs = null;
			$this->mCurrentRecord = 0;
			$this->mLimit =0;
			if ($this->DB->Free_Result($this->mhRs,$this->mhCn)) $this->mhRs = null;
		}
	}

	function MoveFirst() {
			//~ trigger_error('MOVEFIRST='.$this->mCurrentRecord,E_USER_NOTICE);
		if ($this->mPageSize) {
			if ($this->mPageNum >= $this->PageCount()) {
				$this->mPageNum = 0;
			} elseif ($this->mPageNum < 0) {
				$this->mPageNum = $this->PageCount() - 1;			
			}
			return $this->MoveTo($this->mPageNum * $this->mPageSize + 1);
		} else {
			if ($this->mCurrentRecord) {
				$this->DB->Result_Seek($this->mhRs, 0);
			}
			$this->mCurrentRecord = 0;
			return $this->MoveNext();
		}
		//~ if ($this->mCurrentRecord == 1) {
			//~ return $this->mRs;
		//~ } else if ($this->mCurrentRecord > 1) {
			//~ $this->DB->Result_Seek($this->mhRs, 0);
			//~ $this->mCurrentRecord = 1;
			//~ $this->mFetched = 0;
		//~ }
		//~ return $this->MoveNext();
	}

	function MoveNext() {
		if ($this->mLimit > 0 && $this->mFetched == $this->mLimit) {
			$this->mEof = true;
			return ($this->mRs = null);
		}
		
		if ($this->Win==1)
			$this->mRs = $this->ParseResultArray(array_map("WinToUnicode",$this->DB->Fetch_Array($this->mhRs,($this->mCurrentRecord+1), $this->GetFetchReturnType())));
		else
			$this->mRs = $this->ParseResultArray($this->DB->Fetch_Array($this->mhRs,($this->mCurrentRecord+1), $this->GetFetchReturnType()));

		$this->mEof = !$this->mRs;
		if (!$this->mEof) {
			$this->mCurrentRecord++;
			$this->mFetched++;
		}
		return $this->mRs;
	}
	
	// move to zapis 
	// $cnt = 1 - purviqt
	
	function MoveTo($cnt) {
		if ($cnt > 0 && (($cnt - 1) < $this->RecordCount()) ) {
			
			if ( !$this->DB->Result_Seek($this->mhRs, ($cnt-1)) ) {
				trigger_error("moveTo non-existent record. Please contact xxx.", E_USER_NOTICE);
				exit();
			}
			
			if ($this->Win==1)
				$this->mRs = array_map("WinToUnicode",$this->ParseResultArray($this->DB->Fetch_Array($this->mhRs, ($cnt), $this->GetFetchReturnType())));
			else
				$this->mRs = $this->ParseResultArray($this->DB->Fetch_Array($this->mhRs, ($cnt), $this->GetFetchReturnType()));			
			$this->mEof = !$this->mRs;
			
			if (!$this->mEof) {
				$this->mFetched = 1;
				$this->mCurrentRecord = $cnt;
			}
			return $this->mRs;
		} else {
			return false;
		}
	}
	
	function ParseResultArray($p) {
		for ($i = $this->DB->ResultStartIter(); $i < $this->DB->Num_Fields($this->mhRs); $i++) {
			if ($this->DB->Field_Type($this->mhRs, $i)=='bool') {
				if (array_key_exists($i, $p)) {
					if ($p[$i]=='t') {
						$p[$i] = 'true';
						$p[$this->DB->Field_Name($this->mhRs, $i)] = 'true';
					} else {
						$p[$i] = 'false';
						$p[$this->DB->Field_Name($this->mhRs, $i)] = 'false';
					}
				}
			}
		}
		return $p;
	}

	function Execute($sqlstr) {
		//$t1=time();
		$this->mCurrentRecord = 0;
		$this->mFetched = 0;
		$this->mErrMsg = null;
		//~ trigger_error("Exec: " . $sqlstr , E_USER_NOTICE);
		//~ var_dump($this->mhCn);
		$this->mhRs = $this->DB->Query($this->mhCn, $sqlstr);
		
		if (!$this->mhRs) {
			$this->mErrMsg = $this->DB->Last_Error($this->mhCn) ;
			trigger_error("Error executing the query: $sqlstr \nPlease contact xxx." . $this->mErrMsg , E_USER_NOTICE);
			return false;
		}		
		$this->mRecordCount = @$this->DB->Num_Rows($this->mhRs);
		$this->mEof = (bool) !$this->mRecordCount;
		$t2=time();
		//~ trigger_error("Query: ".($t2-$t1)." sec. -  $sqlstr ", E_USER_NOTICE);
		$this->MoveFirst();
		return true;
	}
	
	function ExecutePreparedStatement($pStatementName, $pParams){
		$this->mCurrentRecord = 0;
		$this->mFetched = 0;
		$this->mErrMsg = null;
		//~ trigger_error("Exec: " . $sqlstr , E_USER_NOTICE);
		//~ var_dump($this->mhCn);
		$this->mhRs = pg_execute($this->mhCn, $pStatementName, $pParams);
		
		if (!$this->mhRs) {
			$this->mErrMsg = $this->DB->Last_Error($this->mhCn) ;
			trigger_error("Error executing the statement: $pStatementName \nPlease contact xxx." . $this->mErrMsg , E_USER_NOTICE);
			return false;
		}
		$this->mRecordCount = @$this->DB->Num_Rows($this->mhRs);
		$this->mEof = (bool) !$this->mRecordCount;
		$t2=time();
		//~ trigger_error("Query: ".($t2-$t1)." sec. -  $sqlstr ", E_USER_NOTICE);
		$this->MoveFirst();
		return true;
	}
	
	function SetLimit($limit) {
		if ($limit >= 0) {
			$this->mLimit = $limit;
		}
	}

	function Eof() {
		if ( $this->mPageSize && $this->mCurrentRecord > $this->mPageSize * ($this->mPageNum+1) ) return true ;
		return (bool) $this->mEof;
	}
	
	function RecordCount() {
		return $this->mRecordCount;
	}
	
	function FieldCount() {
		return $this->DB->Num_Fields($this->mhRs);
	}

	function GetField($a) {
		return $this->DB->Fetch_Field($this->mhRs, $a);
		//return 1;
	}
	
	function GetLastError() {
		return $this->mErrMsg;
	}

	function SetPageSize($pPs) {
		$this->mPageSize = $pPs;
	}
	
	function SetPageNum($pPn) {
		$this->mPageNum = $pPn;
	}
	
	function SetFetchReturnType($pFetchType) {
		$this->mFetchType = $pFetchType;
	}
	
	function GetFetchReturnType() {
		if ($this->mFetchType){
			return $this->mFetchType;
		}
		return PGSQL_ASSOC;
	}
	
	function PageCount() {
		return ceil($this->RecordCount() / $this->mPageSize);
	}
	
	function SetPage($pPageSize, $pPageNum) {
		if ($pPageNum == -1) $pPageNum = ceil($this->RecordCount()/$pPageSize)-1;//kogato predadesh p = -1 te move-a do poslednata stranica
		$this->SetPageSize($pPageSize);
		if ($pPageNum > $this->PageCount()) $pPageNum = 0;
		$this->SetPageNum($pPageNum);
		$this->MoveTo($pPageNum * $pPageSize + 1);
	}

	function CustSecSqlConv($pSqlstr, $pDbtype = DEF_DBTYPE) {
										

		$t1=time();
		$this->mCurrentRecord = 0;
		$this->mFetched = 0;
		$this->mErrMsg = null;
		
		$this->mhRs = $this->DB->Query($this->mhCn, $sqlstr);
		
		if (!$this->mhRs) {
			$this->mErrMsg = $this->DB->Last_Error($this->mhCn) ;
			trigger_error("Error executing the query: $sqlstr \nPlease contact xxx." . $this->mErrMsg , E_USER_NOTICE);
			return false;
		}		
		$this->mRecordCount = @$this->DB->Num_Rows($this->mhRs);
		$this->mEof = (bool) !$this->mRecordCount;
		$t2=time();
		//~ trigger_error("Query: ".($t2-$t1)." sec. -  $sqlstr ", E_USER_NOTICE);
		$this->MoveFirst();
		return true;
	}
	
}

//Denica - za Oracle sintaksisa za kolonite ot security-to e razli4en
//Hristo - izkarvam function ot klasa za da moje da se vika bez da se pravi Con
function BuildSecCols($sqlstr,$dbtype = DEF_DBTYPE) {
	$lHelper = GetConnectionByType($dbtype, 1);
	$rescol = $lHelper->SecurityCols($sqlstr);
	if (!$rescol) $rescol = $sqlstr;
	return $rescol;
}

function GetConnectionByType($pDbtype = DEF_DBTYPE, $pHelper = 0){	
	$lDbCreator = new DBCreator($pDbtype);	
	if( (int) $pHelper)
		return $lDbCreator->createHelper();
	return $lDbCreator->createConnection();
	
}

function Single_Query ($pmhCn, $psqlstr, $dbtype=DEF_DBTYPE) {
	$lHelper = GetConnectionByType($dbtype, 1);
	return $lHelper->Single_Query($pmhCn, $psqlstr);
}

function SaveSQLValue($value,$type,$name,$pConnection,$dbtype=DEF_DBTYPE) {
	//~ trigger_error('dbtype ' . $dbtype . ' name ' . $name,E_USER_NOTICE);
	$lHelper = GetConnectionByType($dbtype, 1);
	
	return $lHelper->SaveSQLValue($value,$type,$name,$pConnection, 0);	
}

function CustomSQLFunction($fname,$farg,$fieldname,$fieldtype, $dbtype=DEF_DBTYPE,$conArr=null) {
	//~ trigger_error($fname." ".$farg." ".$fieldname." ".$fieldtype,E_USER_NOTICE);
	if ($conArr["dbtype"])
		$dbtype = $conArr["dbtype"];
	$lHelper = GetConnectionByType($dbtype, 1);
	return $lHelper->CustomSQLFunction($fname,$farg,$fieldname,$fieldtype);	
}

function GetPrototypeSQL($sqlname,$actionname,$sqltype, $multilang=0, $dbtype=DEF_DBTYPE, $conArr=null) {	
	if ($conArr["dbtype"])
		$dbtype = $conArr["dbtype"];

	$lHelper = GetConnectionByType($dbtype, 1);
	return $lHelper->GetPrototypeSQL($sqlname,$actionname,$sqltype, $multilang);	
}


function Con($pCustomDBConArr=null) {
	global $gCn,$gCnArr;
	
	if(!$pCustomDBConArr){
		if (get_class($gCn) != 'DBCn') {
			$gCn = new DBCn;
			$gCn->Open();
			if (defined("DB_CONNECTION_OPTIONS"))
				Single_Query($gCn->mhCn, DB_CONNECTION_OPTIONS);
		}
		return $gCn;
	}else{
		if(!is_array($gCnArr)) $gCnArr = array();
		foreach($gCnArr as $k => $v)
			if($v["dbconarr"]==$pCustomDBConArr){
				$key=$k;
				break;
			}
		
		if(!isset($key)){
			$key = array_push($gCnArr,array("dbobj" => null, "dbconarr" => $pCustomDBConArr));
			$key--;
		}
		 if(get_class($gCnArr[$key]["dbobj"]) != 'DBCn') {
			$lDBConArr=$gCnArr[$key]["dbconarr"];
			$lCn = new DBCn($lDBConArr["dbtype"],$lDBConArr["win_encoding"]);
			$lCn->Open($lDBConArr["srv"], $lDBConArr["db"], $lDBConArr["usr"], $lDBConArr["pass"]);
			if (isset($lDBConArr["dbconopt"]))
				Single_Query($lCn->mhCn, $lDBConArr["dbconopt"],$lDBConArr["dbtype"]);
			$gCnArr[$key]["dbobj"] = $lCn;
		}
		return $gCnArr[$key]["dbobj"];
	}
}


//Functions and Constants used by eserver

//Constants for Generating Prototypes for SQLs on Actions
define("GENACT_NO", 0);
define("GENACT_TABLE", 1);
define("GENACT_SP", 2);

function GetStringforTSquery($lstr){
	$loparr=array('&', '|', '(', ')');
	$lArr1 = array("и","или", "and", "or");
	$lArr2 = array("&", "|", "&", "|");
        if (!$lstr) return "";
	$lres="";
	$larr=split('[\,\.\\\/\; \'\"]',$lstr);
	$lprevop=1;
	foreach($larr as $v) {
		if (mb_strlen($v, 'utf8') == 0) continue;
		$i=array_search(mb_strtolower($v,'utf8'), $lArr1);
		if ($i===FALSE) ;
		else $v=$lArr2[$i];
		$lthis_oper = in_array($v, $loparr);
		if (!$lprevop && !$lthis_oper) $lres .= ' &';
		$lres.=" ".$v;
		$lprevop=$lthis_oper;
	}
        return $lres;
}

function GetFilterValueforFilter($value, $type, $name, $customstr, $dbtype=DEF_DBTYPE, $win_encoding=0) {
	if ($dbtype=="") $dbtype=DEF_DBTYPE;
	if ($win_encoding=="") $win_encoding=0;

	global $gCnArr;
	if ($value==='')
		return '';
       $lop='=';
       $typeorg=$type;
	if ($type[0]=='s' || $type=="multiplestring" || $type=="") {
		if ($value[0]=='%') {
			$lop=' like ';
			$value=substr($value,1);
		} else if (substr($value,0,2)=='<>') {
			$lop=' <> ';
			$value=substr($value,2);
		} else if ($type[0]=='s' ) {
			if ($value[0]=='=')  
				$value=substr($value,1);
			else {$lop=' like ';$value='%'.$value.'%';}
		}
		if ($win_encoding==1)	
			$value=WinToUnicode($value,1);
	} else {
		if (($type=="tsvector") || ($type=="totsvector")) {
			$lop="to_tsquery";
			$type="string";
		} else if (substr($value,0,2)=='<>') {
			$lop=' <> ';
			$value=substr($value,2);
		} else if ($value[0]=='<' || $value[0]=='>') {
		   $lop=$value[0];
		   $value=substr($value,1);
		} else if ($value[0]=='&') {
			$lop='&';
			$value=substr($value,1);
			$valarr=split(';',$value);
			if (count($valarr)!=2) {
				trigger_error("Valid format for field '".$name."' is '&...;...'  !",E_USER_ERROR);
				exit();
			}
		}
	}
	if (!$customstr){
		if ($lop==' like ')
			return "(upper(" . $name .")". $lop ."upper(". SaveSQLValue($value, $type, $name, $dbtype) . "))"; //postgres bug v ilike i unicode
		else if ($lop=='to_tsquery') {
			return "(" . (($typeorg=="totsvector") ? "to_tsvector('bg_utf8', $name )" :$name ). " @@ to_tsquery('bg_utf8', " . SaveSQLValue(GetStringforTSquery($value), $type, $name, $dbtype) . "))";
		} else if ($lop=='&')
			return "(" . $name ." >= ".  SaveSQLValue($valarr[0], $type, $name, $dbtype) . " and ".$name ." <= ".  SaveSQLValue($valarr[1], $type, $name, $dbtype) .")"; 
		else if ($type=="multipleint") 
				$lop="@>";
		else if ((($type=="timestamp") || ($type=="date") || ($type=="datetime")) && (($value[0]=='D') || ($value[0]=='H') || ($value[0]=='W') || ($value[0]=='M') || ($value[0]=='Y'))) {
				$df=$value[0];//TUKA
				switch ($df) {
					case 'H': $lstr=0;break;
					case 'D': $lstr=1;break;
					case 'W': $lstr=2;break;
					case 'M': $lstr=3;break;
					case 'Y': $lstr=4;break;
				}
				$valarr=GetCurrentInterval($lstr);
				//~ $value=int (substr($value,1));
				//~ if ($value >=0) {
					//~ $lop=">";
					//~ $lvalue= new DateTime();
					//~ $lvalue->add(new DateInterval('P'+$value + $df);
					//~ $value=$lvalue->format('Y-m-d\TH:i:s');
				//~ } else return ;
				return "(" . $name ." >= ".  SaveSQLValue($valarr[0], $type, $name, $dbtype) . " and ".$name ." < ".  SaveSQLValue($valarr[1], $type, $name, $dbtype) .")"; 
			}
			return "(" . $name . $lop . SaveSQLValue($value, $type, $name, $dbtype) . ")";
	} else {
		$res= str_replace( '$VALUE', SaveSQLValue($value, $type, $name, $dbtype),$customstr);
		$res= str_replace( '$OP', $lop,$res);
		return "(" . $res . ")";
	}
}



function sql_unescape_array($val) {
	$resarr = array();
	if (!$val) return $resarr;
	$val = substr($val, 1, -1);
	$valarr = explode(',', $val);
	$rk = 1;
	$buf = null;
	//trigger_error("VAL:" . $val,E_USER_NOTICE);
	foreach($valarr as $k => $v) {
		if ((substr($v,0,1) == '"') &&  (substr($v,-1) == '"')) {
			$resarr[$rk] = str_replace('\"', '"', substr($v, 1, -1));
			$rk++;
		} else if (substr($v,0,1) == '"') {
			$buf = substr($v,1) . ",";
		} else if (substr($v,-1) == '"') {
			$resarr[$rk] = str_replace('\"', '"', $buf.substr($v, 0, strlen($v)-1));
			$rk ++;
			$buf = null;
		} else if (!$buf) {
			$resarr[$rk] = $v;
			$rk ++;
		} else $buf .= "," . $v;
	}
	
	return $resarr;
}


//Ime na sluzhebno pole za Stored Procedure ID
define("SP_OPID_NAME", "__SP_OPID_NAME");


function now() {
   $date = getDate();
   foreach($date as $item=>$value) {
       if ($value < 10)
           $date[$item] = "0".$value;
   }
   return $date['year']."-".$date['mon']."-".$date['mday']." ".$date['hours'].":".$date['minutes'].":".$date['seconds'];
}

/*
function GetCurrentInterval($ptype) - vryshta masiv s start i end na intervala
param: ptype - 0 chas, 1 den, 2 sedmcia, 3 mesec, 4 godina
*/
function GetCurrentInterval($ptype) {
	$larr=array(new DateTime(), new DateTime());
	if($larr[1]->add) {
		switch ($ptype) {
			case 0: $lstr='H';break;
			case 1: $lstr='D';break;
			case 2: $lstr='W';break;
			case 3: $lstr='M';break;
			case 4: $lstr='Y';break;
		}
		$larr[1]->add(new DateInterval('P1'.$lstr));
		if ($ptype==2) {
			$didx=$larr[0]->format("w");
			if (!$didx) $didx=7;
			if ($didx>1) {
				$larr[0]->sub(new DateInterval('P'.($didx-1).'D'));
				$larr[1]->sub(new DateInterval('P'.($didx-1).'D'));
			}
		}
	} else {
		switch ($ptype) {
			case 0: $lstr='hour';break;
			case 1: $lstr='day';break;
			case 2: $lstr='week';break;
			case 3: $lstr='month';break;
			case 4: $lstr='year';break;
		}
		$larr[1]->modify('+1 '.$lstr);
		if ($ptype==2) {
			$didx=$larr[0]->format("w");
			if (!$didx) $didx=7;
			if ($didx>1) {
				$larr[0]->modify('-'.($didx-1).' day');
				$larr[1]->modify('-'.($didx-1).' day');
			}
		}
	}
	$lformat='Y-'.(($ptype==4) ? '1':'m').'-'.((($ptype==3) || ($ptype==4)) ? '1':'d').' '.(!$ptype ? 'H':'00').':00:00';
	$res=array($larr[0]->format($lformat), $larr[1]->format($lformat));
	
	return $res;
}

function now4hours() {
   $date = getDate();
   foreach($date as $item=>$value) {
       if ($value < 10)
           $date[$item] = "0".$value;
   }
   $ltime=mktime(($date['hours']+4),$date['minutes'],$date['seconds'],$date['mon'],$date['mday'],$date['year']);
   //return $date['year']."-".$date['mon']."-".$date['mday']." ".($date['hours']+4).":".$date['minutes'].":".$date['seconds'];
   return date('Y-m-d H:i:s',$ltime);
}

function date1() { // Nikola: tva mi trqbva za da filtriram deinosti, koito sa s nacahlo dnes, no po-golqmo ot now() 
   $date = getDate();
   foreach($date as $item=>$value) {
       if ($value < 10)
           $date[$item] = "0".$value;
   }
   return $date['year']."-".$date['mon']."-".$date['mday']." 23:59";
}
function date2() { //tva e za deinosti s krai dneska, ama predi now() 
   $date = getDate();
   foreach($date as $item=>$value) {
       if ($value < 10)
           $date[$item] = "0".$value;
   }
   return $date['year']."-".$date['mon']."-".$date['mday']." 00:00";
}
function now1() {
   $date = getDate();
   foreach($date as $item=>$value) {
       if ($value < 10)
           $date[$item] = "0".$value;
   }
   return $date['year']."-".$date['mon']."-".$date['mday'];
}  
//Denitsa - da polu4avam razni stringove za def.xml (v spravkite - proekta = teku6tata godina i 1(20091)
function nowconcat($arrstr, $delimiter){
	$ret = '';
	foreach($arrstr as $k=>$value) {
		$ret .= $value.$delimiter;
	}
 return $ret;
};


function SaveMailfile($pContents, $pMailsubject, $pMailto, $pFromdisplay, $pFromemail, $pMid) {
		$preferences = array(
			"input-charset" => "UTF-8",
			"line-break-chars" => "\n"
		);
		$lEncodedSubject = iconv_mime_encode("Subject", $pMailsubject, $preferences);
		$lHeaders = "To: " . $pMailto. "\n";
		$lHeaders .= "From: " . $pFromdiplay . " <" . $pFromemail . ">" . "\n";
		$lHeaders .= $lEncodedSubject . "\n";
		$lHeaders .= "MIME-Version: 1.0\n";
		$lHeaders .= "Content-Type: text/html; charset=\"UTF-8\"\n";
		$lHeaders .= "Content-Transfer-Encoding: base64\n";
		$lHeaders .= "\n";
		$lHeaders .= chunk_split(base64_encode($pContents));
		$lFRet = file_put_contents(PATH_MESSAGING . $pMid. ".txt", $lHeaders);
		if ($lFRet && $pMid) {
			$gCn1 = new DBCn;
			$gCn1 ->Open(srv, db, usr , pass);
			$gCn1->Execute("UPDATE messaging SET state = 0 WHERE id = " . $pMid.";");
			$gCn1 ->Close();
		}
}

?>
