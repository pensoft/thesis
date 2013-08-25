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
	'Type status' => 209,
	'scientificName' => 83,
	'originalNameUsage' => 86,
	'originalNameUsageID' => 79,
	'namePublishedIn' => 88,
	'namePublishedInID' => 81,
	'nameAccordingTo' => 87,
	'nameAccordingToID' => 80,
	'taxonConceptID' => 82,
	'acceptedNameUsage' => 84,
	'acceptedNameUsageID' => 77,
	'taxonomicStatus' => 104,
	'nomenclaturalStatus' => 105,
	'taxonRemarks' => 106,
	'taxonID' => 75,
	'scientificNameID' => 76,
	'parentNameUsageID' => 78,
	'parentNameUsage' => 85,
	'higherClassification' => 89,
	'kingdom' => 90,
	'phylum' => 91,
	'class' => 92,
	'order' => 93,
	'family' => 94,
	'taxonRank' => 99,
	'verbatimTaxonRank' => 100,
	'vernacularName' => 102,
	'nomenclaturalCode' => 103,
	'genus' => 95,
	'subgenus' => 96,
	'specificEpithet' => 97,
	'infraspecificEpithet' => 98,
	'scientificNameAuthorship' => 101,
	'locationID' => 107,
	'higherGeographyID' => 108,
	'higherGeography' => 109,
	'continent' => 110,
	'waterBody' => 111,
	'islandGroup' => 112,
	'island' => 113,
	'country' => 114,
	'countryCode' => 115,
	'stateProvince' => 116,
	'county' => 117,
	'municipality' => 118,
	'locality' => 119,
	'verbatimLocality' => 120,
	'verbatimElevation' => 121,
	'minimumElevationInMeters' => 122,
	'maximumElevationInMeters' => 123,
	'verbatimDepth' => 124,
	'minimumDepthInMeters' => 125,
	'maximumDepthInMeters' => 126,
	'minimumDistanceAboveSurfaceInMeters' => 127,
	'maximumDistanceAboveSurfaceInMeters' => 128,
	'locationAccordingTo' => 129,
	'locationRemarks' => 130,
	'verbatimCoordinates' => 131,
	'verbatimLatitude' => 132,
	'verbatimLongitude' => 133,
	'verbatimCoordinateSystem' => 134,
	'verbatimSRS' => 135,
	'decimalLatitude' => 136,
	'decimalLongitude' => 137,
	'geodeticDatum' => 138,
	'coordinateUncertaintyInMeters' => 139,
	'coordinatePrecision' => 140,
	'pointRadiusSpatialFit' => 141,
	'footprintWKT' => 142,
	'footprintSRS' => 143,
	'footprintSpatialFit' => 144,
	'georeferencedBy' => 145,
	'georeferenceProtocol' => 146,
	'georeferenceSources' => 147,
	'georeferenceVerificationStatus' => 148,
	'georeferenceRemarks' => 149,
	'eventID' => 175,
	'samplingProtocol' => 176,
	'samplingEffort' => 177,
	'eventDate' => 178,
	'eventTime' => 179,
	'startDayOfYear' => 180,
	'endDayOfYear' => 181,
	'year' => 182,
	'month' => 183,
	'day' => 184,
	'verbatimEventDate' => 185,
	'habitat' => 186,
	'fieldNumber' => 187,
	'fieldNotes' => 188,
	'eventRemarks' => 189,
	'individualID' => 59,
	'individualCount' => 60,
	'sex' => 61,
	'lifeStage' => 62,
	'preparations' => 67,
	'reproductiveCondition' => 63,
	'behavior' => 64,
	'establishmentMeans' => 65,
	'catalogNumber' => 54,
	'occurrenceDetails' => 55,
	'occurrenceRemarks' => 56,
	'recordNumber' => 57,
	'recordedBy' => 58,
	'occurrenceStatus' => 66,
	'disposition' => 68,
	'otherCatalogNumbers' => 69,
	'previousIdentifications' => 70,
	'associatedMedia' => 71,
	'associatedReferences' => 72,
	'associatedOccurrences' => 73,
	'associatedSequences' => 74,
	'geologicalContextID' => 157,
	'earliestEonOrLowestEonothem' => 158,
	'latestEonOrHighestEonothem' => 159,
	'earliestEraOrLowestErathem' => 160,
	'latestEraOrHighestErathem' => 161,
	'earliestPeriodOrLowestSystem' => 162,
	'latestPeriodOrHighestSystem' => 163,
	'earliestEpochOrLowestSeries' => 164,
	'latestEpochOrHighestSeries' => 165,
	'earliestAgeOrLowestStage' => 166,
	'latestAgeOrHighestStage' => 167,
	'lowestBiostratigraphicZone' => 168,
	'highestBiostratigraphicZone' => 169,
	'lithostratigraphicTerms' => 170,
	'group' => 171,
	'formation' => 172,
	'member' => 173,
	'bed' => 174,
	'identificationID' => 150,
	'identifiedBy' => 151,
	'dateIdentified' => 152,
	'identificationReferences' => 153,
	'identificationRemarks' => 154,
	'identificationQualifier' => 155,
	'type' => 190,
	'modified' => 191,
	'language' => 192,
	'rights' => 193,
	'rightsHolder' => 194,
	'accessRights' => 195,
	'bibliographicCitation' => 196,
	'institutionID' => 197,
	'collectionID' => 198,
	'datasetID' => 199,
	'institutionCode' => 200,
	'collectionCode' => 201,
	'datasetName' => 202,
	'ownerInstitutionCode' => 203,
	'basisOfRecord' => 204,
	'informationWithheld' => 205,
	'dataGeneralizations' => 206,
	'dynamicProperties' => 207,
	'source' => 208,
);

/* Това ще ми е масива със стойностите за всеки материал за всеки филд поредността на филдовете трябва да е същата като на масива $lMaterialsColumnsArr*/
$lMaterialsFieldsArr = array(
209 => '',
83 => '',
86 => '',
79 => '',
88 => '',
81 => '',
87 => '',
80 => '',
82 => '',
84 => '',
77 => '',
104 => '',
105 => '',
106 => '',
75 => '',
76 => '',
78 => '',
85 => '',
89 => '',
90 => '',
91 => '',
92 => '',
93 => '',
94 => '',
99 => '',
100 => '',
102 => '',
103 => '',
95 => '',
96 => '',
97 => '',
98 => '',
101 => '',
107 => '',
108 => '',
109 => '',
110 => '',
111 => '',
112 => '',
113 => '',
114 => '',
115 => '',
116 => '',
117 => '',
118 => '',
119 => '',
120 => '',
121 => '',
122 => '',
123 => '',
124 => '',
125 => '',
126 => '',
127 => '',
128 => '',
129 => '',
130 => '',
131 => '',
132 => '',
133 => '',
134 => '',
135 => '',
136 => '',
137 => '',
138 => '',
139 => '',
140 => '',
141 => '',
142 => '',
143 => '',
144 => '',
145 => '',
146 => '',
147 => '',
148 => '',
149 => '',
175 => '',
176 => '',
177 => '',
178 => '',
179 => '',
180 => '',
181 => '',
182 => '',
183 => '',
184 => '',
185 => '',
186 => '',
187 => '',
188 => '',
189 => '',
59 => '',
60 => '',
61 => '',
62 => '',
67 => '',
63 => '',
64 => '',
65 => '',
54 => '',
55 => '',
56 => '',
57 => '',
58 => '',
66 => '',
68 => '',
69 => '',
70 => '',
71 => '',
72 => '',
73 => '',
74 => '',
157 => '',
158 => '',
159 => '',
160 => '',
161 => '',
162 => '',
163 => '',
164 => '',
165 => '',
166 => '',
167 => '',
168 => '',
169 => '',
170 => '',
171 => '',
172 => '',
173 => '',
174 => '',
150 => '',
151 => '',
152 => '',
153 => '',
154 => '',
155 => '',
190 => '',
191 => '',
192 => '',
193 => '',
194 => '',
195 => '',
196 => '',
197 => '',
198 => '',
199 => '',
200 => '',
201 => '',
202 => '',
203 => '',
204 => '',
205 => '',
206 => '',
207 => '',
208 => '',
);

// coma separated
$lForbiddenFields = '249';

$lCsv = '';
$gHeader = 0;
$lHeaderAdd = array();

$lArrToFillValsIfEmpty = array(48, 417, 49, 50);

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
	
	// var_dump($lGetMaterialsSql);
	// exit;
	
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
			//var_dump($lGetMaterialDarwinCorePosSql);
			
			// tva e typ fix za connection-a
			$lCon->Open();
			$lCon->Close();
			$lCon->Open();
			$lCon->Execute($lGetMaterialDarwinCorePosSql);
			$lCon->MoveFirst();
			
			$lPos = $lCon->mRs['pos'];
			
			$lCon->Execute('
				SELECT ifv.*
				FROM pwt.document_object_instances doi
				JOIN pwt.v_instance_fields ifv ON ifv.instance_id = doi.id 
				WHERE doi.pos like \'' . $lPos . '%\' AND document_id = ' . $lMaterialDocumentId . '
					AND field_id NOT IN (' . $lForbiddenFields . ')
			');
			$lCon->MoveFirst();
			// var_dump('
				// SELECT ifv.*
				// FROM pwt.document_object_instances doi
				// JOIN pwt.v_instance_fields ifv ON ifv.instance_id = doi.id 
				// WHERE doi.pos like \'' . $lPos . '%\' AND document_id = ' . $lMaterialDocumentId . '
					// AND field_id NOT IN (' . $lForbiddenFields . ')
			// ');
			 // exit;
			$lMaterialsFieldValuesArr[$lMaterialInstanceId] = $lMaterialsFieldsArr;
			$lMaterialFieldsArr = array();
			while(!$lCon->Eof()){
				$lMaterialFieldsArr[] = $lCon->mRs;
				$lCon->MoveNext();
			}
			
			foreach ($lMaterialFieldsArr as $fldkey => $fldval) {
				$lFieldValueColumn = $fldval['value_column_name'];
				$lFieldValue = $fldval[$lFieldValueColumn];
				$lLabel = $fldval['label'];
				
				if($lFieldValue) {
					$lParsedValue = parseFieldValue($lFieldValue, $fldval['type']);
					
					if($fldval['src_query']) {
						$lParsedValue = getFieldSelectOptionsById($fldval['src_query'], $lParsedValue, $lMaterialDocumentId, $fldval['instance_id']);
						if(is_array($lParsedValue)) {
							$lParsedValue = implode(";", $lParsedValue);
						}
					}
					
				} else {
					$lParsedValue = '';
				}
			
				$lMaterialsFieldValuesArr[$lMaterialInstanceId][$fldval['field_id']] = $lParsedValue;
				
				if(!in_array($fldval['field_id'], $lMaterialsColumnsArr) && !$gHeader) {
					//$lHeaderAdd['field_id'] = $lLabel;
					$lHeaderAdd[$fldval['field_id']] = $lLabel;
				}
			}
			
			if(
				!$lMaterialsFieldValuesArr[$lMaterialInstanceId][95] &&
				!$lMaterialsFieldValuesArr[$lMaterialInstanceId][96] &&
				!$lMaterialsFieldValuesArr[$lMaterialInstanceId][97] &&
				!$lMaterialsFieldValuesArr[$lMaterialInstanceId][101]
			) {
				$lArrVals = fixEmptyValues($lMaterialDocumentId, $lPos, 42, $lArrToFillValsIfEmpty);
				if(count($lArrVals)) {
					$lMaterialsFieldValuesArr[$lMaterialInstanceId][95] = $lArrVals[48];
					$lMaterialsFieldValuesArr[$lMaterialInstanceId][96] = $lArrVals[417];
					$lMaterialsFieldValuesArr[$lMaterialInstanceId][97] = $lArrVals[49];
					$lMaterialsFieldValuesArr[$lMaterialInstanceId][101] = $lArrVals[50];
				}
			}
			$gHeader++;
		}
	}
	
	$lFileName = 'materials_' . $lTreatmentName . '_' . $lMaterialDocumentId . '.csv';
	header("Content-Disposition: attachment;filename=\"" . $lFileName . "\"");
	
	$file = fopen('/tmp/' . $lFileName,"w");
	
	$lheader = array();
	ksort($lHeaderAdd);
	$lheader = array_merge(array_keys($lMaterialsColumnsArr), $lHeaderAdd);
	
	fputcsv($file, $lheader);
	
	foreach($lMaterialsFieldValuesArr as $mmkey => $mmval) {
		$lTempArr = $lMaterialsFieldsArr;
		$lTemp2Arr = array();
		$lResultArr = array();
	
		foreach($mmval as $k => $v) {
			if(in_array($k, $lMaterialsColumnsArr)){
				$lTempArr[$k] = $v;
			} else {
				$lTemp2Arr[$k] = $v;
			}
		}
		
		ksort($lTemp2Arr);
		$lResultArr = $lTempArr + $lTemp2Arr;
		
		fputcsv($file, $lResultArr);
	}
	
	fclose($file);
	
	$lContents = file_get_contents('/tmp/' . $lFileName);
	unlink('/tmp/' . $lFileName);
	
	$lCsvStr = $lContents;
	$lCsvStr = str_replace(array("\n"), "\r\n", $lCsvStr);
	
	print chr(239) . chr(187) . chr(191) . $lCsvStr;
	exit;
	
} elseif ($lAction == 'export_table_as_csv') {
	$lCon = new DBCn();
	$lCon->Open();	
	
	$lTableDescriptionFieldId = 490;
	$lSql = 'select value_str from pwt.instance_field_values where instance_id = ' . (int)$lInstanceId . ' AND field_id = ' . (int)$lTableDescriptionFieldId; 
	$lCon->Execute($lSql);
	
	$lCon->MoveFirst();
	
	$lContent = strip_tags($lCon->mRs['value_str'], '<table><tr><th><td>');
	// tova se slaga kogato imam prazno td, neznam za6to (ot kontrolkata e...)
	$lContent = str_replace(chr(194), '', $lContent);
	$lTableDescription = '<body>' . $lContent . '</body>'; 
	
	$lDoc = new DOMDocument('1.0', DEFAULT_XML_ENCODING);
	$lDoc->loadHTML($lTableDescription);
	
	$lXpath = new DOMXPath($lDoc);
	$lTable = $lXpath->query("//table[position() = 1]");
	
	$lHeader = $lXpath->query(".//th", $lTable->item(0));
	//var_dump($lHeader);
	$lHeaderArr = array();
	
	if($lHeader->length){
		for($i = 0; $i < $lHeader->length; ++$i){
			$lHeaderArr[] = $lHeader->item($i)->nodeValue;
		}
	}
	
	$lFileName = 'table_' . (int)$lInstanceId . '.csv';
	header("Content-Disposition: attachment;filename=\"" . $lFileName . "\"");
	
	$file = fopen('/tmp/' . $lFileName,"w");
	
	if(count($lHeaderArr)){
		fputcsv($file, $lHeaderArr);
	}
	
	
	$lRows = $lXpath->query(".//tr", $lTable->item(0));
	
	if($lRows->length){
		for($i = 0; $i < $lRows->length; ++$i){
			$lColsArr = array();
			$lColumns = $lXpath->query(".//td", $lRows->item($i));
			if($lColumns->length){
				for($j = 0; $j < $lColumns->length; ++$j){
					$lColsArr[] = trim($lColumns->item($j)->nodeValue);
				}
				fputcsv($file, $lColsArr);
			}
		}
	}

	fclose($file);
	
	$lContents = file_get_contents('/tmp/' . $lFileName);
	unlink('/tmp/' . $lFileName);
	
	$lCsvStr = $lContents;
	$lCsvStr = str_replace(array("\n"), "\r\n", $lCsvStr);
	
	print chr(239) . chr(187) . chr(191) . $lCsvStr;
	exit;
}

function fixEmptyValues($pDocumentId, $pPos, $pTaxonRankFieldId, $pFieldIds) {
	$lResArr = array();
	$lConSec = new DBCn();
	$lConSec->Open();
	$lPosMain = substr($pPos, 0, 4);
	
	$lConSec->Execute('
		SELECT ifv.value_int
		FROM pwt.document_object_instances doi
		JOIN pwt.v_instance_fields ifv ON ifv.instance_id = doi.id 
		WHERE doi.pos = \'' . $lPosMain . '\' AND document_id = ' . $pDocumentId . '
			AND field_id = ' . $pTaxonRankFieldId
	);
	$lConSec->MoveFirst();
	// var_dump('
		// SELECT ifv.value_int
		// FROM pwt.document_object_instances doi
		// JOIN pwt.v_instance_fields ifv ON ifv.instance_id = doi.id 
		// WHERE doi.pos = \'' . $lPosMain . '\' AND document_id = ' . $pDocumentId . '
			// AND field_id = ' . $pTaxonRankFieldId);
	$lRank = (int)$lConSec->mRs['value_int'];
	
	$lPosInner = substr($pPos, 0, 6);

	if($lRank == 1) {
		$lConSec->Execute('
			SELECT ifv.*
			FROM pwt.document_object_instances doi
			JOIN pwt.v_instance_fields ifv ON ifv.instance_id = doi.id 
			WHERE doi.pos like \'' . $lPosInner . '%\' AND document_id = ' . $pDocumentId . '
				AND field_id IN (' . implode(',', $pFieldIds) . ')'
		);
		$lConSec->MoveFirst();
		while(!$lConSec->Eof()){
			$lResArr[$lConSec->mRs['field_id']] = $lConSec->mRs['value_str']; 
			$lConSec->MoveNext();
		}
	}
	$lConSec->Close();
	
	return $lResArr;
	
}

?>