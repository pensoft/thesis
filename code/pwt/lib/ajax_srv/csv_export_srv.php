<?php
$docroot = getenv('DOCUMENT_ROOT');
require_once($docroot . '/lib/static.php');
ini_set('display_errors', 'off');

header("Pragma: public");
header("Expires: 0"); 
header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
header("Content-Type: text/x-csv");
 

$lAction = $_REQUEST['action'];
$lInstanceId = (int)$_REQUEST['instance_id'];

/* Това ми е за да имам имената на колоните + id-tata на филдовете */
$lMaterialsColumnsArr = array(
'occurrenceID' => 0,
'institutionCode' => 200,
'collectionID' => 198,
'collectionCode' => 201,
'catalogNumber' => 54,
'sex' => 61,
'kingdom' => 90,
'phylum' => 91,
'class' => 92,
'order' => 93,
'family' => 94,
'genus' => 95,
'subgenus' => 96,
'specificEpithet' => 97,
'infraspecificEpithet' => 98,
'scientificName' => 83,
'scientificNameAuthorship' => 101,
'taxonRank' => 99,
'dateIdentified' => 152,
'identifiedBy' => 151,
'typeStatus' => 209,
'continent' => 110,
'waterBody' => 111,
'country' => 114,
'stateProvince' => 116,
'locality' => 119,
'decimalLatitude' => 136,
'decimalLongitude' => 137,
'coordinatePrecision' => 140,
'minimumElevationInMeters' => 122,
'maximumElevationInMeters' => 123,
'minimumDepthInMeters' => 125,
'maximumDepthInMeters' => 126,
'basisOfRecord' => 204,
'eventDate' => 178,
'year' => 182,
'month' => 183,
'day' => 184,
'habitat' => 186,
'fieldNumber' => 187,
'recordedBy' => 58,
'samplingProtocol' => 176,
'associatedMedia' => 71,
'eventRemarks' => 189,
'modified' => 191,
);

/* Това ще ми е масива със стойностите за всеки материал за всеки филд поредността на филдовете трябва да е същата като на масива $lMaterialsColumnsArr*/
$lMaterialsFieldsArr = array(
0 => '',
200 => '',
198 => '',
201 => '',
54 => '',
61 => '',
90 => '',
91 => '',
92 => '',
93 => '',
94 => '',
95 => '',
96 => '',
97 => '',
98 => '',
83 => '',
101 => '',
99 => '',
152 => '',
151 => '',
209 => '',
110 => '',
111 => '',
114 => '',
116 => '',
119 => '',
136 => '',
137 => '',
140 => '',
122 => '',
123 => '',
125 => '',
126 => '',
204 => '',
178 => '',
182 => '',
183 => '',
184 => '',
186 => '',
187 => '',
58 => '',
176 => '',
71 => '',
189 => '',
191 => '',
);

$lCsv = '';
$gHeader = 0;
$lHeaderAdd = array();
//~ foreach($lMaterialsColumnsArr as $key => $val) {
	//~ $lCsv .= $key . ', ';
//~ }

//~ $lCsv .= "\n";

//print $lCsv;

if($lAction == 'export_materials_as_csv') {
	$lCon = new DBCn();
	$lCon->Open();
	
	$lGetMaterialsSql = '
		SELECT 
			doi.id, 
			doi.document_id,
			doi3.display_name as treatment_name
		FROM pwt.document_object_instances doi
		JOIN pwt.document_object_instances doi1 ON doi1.id = doi.parent_id
		JOIN pwt.document_object_instances doi2 ON doi2.id = doi1.parent_id
		JOIN pwt.document_object_instances doi3 ON doi3.id = doi2.parent_id
		WHERE doi.parent_id = ' . $lInstanceId
	;
	$lCon->Execute($lGetMaterialsSql);
	
	$lCon->MoveFirst();
	while(!$lCon->Eof()){
		$lMaterialsArr[] = $lCon->mRs;
		$lCon->MoveNext();
	}
	
	if(count($lMaterialsArr)){
		foreach($lMaterialsArr as $mkey => $mval) {
			$lMaterialInstanceId = $mval['id'];
			$lMaterialDocumentId = $mval['document_id'];
			$lTreatmentName = $mval['treatment_name'];
			
			$lGetMaterialDarwinCorePosSql = 'SELECT pos FROM pwt.document_object_instances where parent_id = ' . $lMaterialInstanceId . ' AND object_id = ' . DARWINCORE_OBJECT_ID;
			//~ var_dump($lGetMaterialDarwinCorePosSql);
			$lCon->Execute($lGetMaterialDarwinCorePosSql);
			$lCon->MoveFirst();
			
			$lPos = $lCon->mRs['pos'];
			
			//~ print_r($lCon->mRs);
			
			$lCon->Execute('
				SELECT ifv.*
				FROM pwt.document_object_instances doi
				JOIN pwt.v_instance_fields ifv ON ifv.instance_id = doi.id 
				WHERE doi.pos like \'' . $lPos . '%\' AND document_id = ' . $lMaterialDocumentId . '
			');
			$lCon->MoveFirst();
			//~ var_dump('
				//~ SELECT ifv.*
				//~ FROM pwt.document_object_instances doi
				//~ JOIN pwt.v_instance_fields ifv ON ifv.instance_id = doi.id 
				//~ WHERE doi.pos like \'' . $lPos . '%\' AND document_id = ' . $lMaterialDocumentId . '
			//~ ');
			
			$lMaterialsFieldValuesArr[$lMaterialInstanceId] = $lMaterialsFieldsArr;
				
			while(!$lCon->Eof()){
				$lFieldValueColumn = $lCon->mRs['value_column_name'];
				$lFieldValue = $lCon->mRs[$lFieldValueColumn];
				$lLabel = $lCon->mRs['label'];
				
				if($lFieldValue) {
					$lParsedValue = parseFieldValue($lFieldValue, $lCon->mRs['type']);
					
					if($lCon->mRs['src_query']) {
						$lParsedValue = getFieldSelectOptionsById($lCon->mRs['src_query'], $lParsedValue, $lMaterialDocumentId, $lCon->mRs['instance_id']);
						if(is_array($lParsedValue)) {
							$lParsedValue = implode(";", $lParsedValue);
						}
					}
					
				} else {
					$lParsedValue = '';
				}
			
				$lMaterialsFieldValuesArr[$lMaterialInstanceId][$lCon->mRs['field_id']] = $lParsedValue;
				
				if(!in_array($lCon->mRs['field_id'], $lMaterialsColumnsArr) && !$gHeader) {
					$lHeaderAdd[] = $lLabel;
				}
				$lCon->MoveNext();
			}
			$gHeader++;
		}
	}
	
	$lFileName = 'materials_' . $lTreatmentName . '_' . $lMaterialDocumentId . '.csv';
	header("Content-Disposition: attachment;filename=\"" . $lFileName . "\"");
	
	$file = fopen('/tmp/' . $lFileName,"w");
	
	$lheader = array();
	$lheader = array_merge(array_keys($lMaterialsColumnsArr), $lHeaderAdd);
	
	fputcsv($file, $lheader);

	foreach($lMaterialsFieldValuesArr as $mmkey => $mmval) {
		fputcsv($file, $mmval);
	}

	fclose($file);
	
	$lContents = file_get_contents('/tmp/' . $lFileName);
	unlink('/tmp/' . $lFileName);
	
	$lCsvStr = str_replace(array("\n"), "\r\n", $lContents);

	print $lCsvStr;
	exit;
	
}

?>