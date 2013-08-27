<?php
$docroot = getenv('DOCUMENT_ROOT');
require_once($docroot . '/lib/static.php');
ini_set('display_errors', 'off');

$lAction = $_REQUEST['action'];
$lInstanceId = (int)$_REQUEST['instance_id'];
$lDocumentId = (int)$_REQUEST['document_id'];

header("Pragma: public");
header("Expires: 0"); 
header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
if($lDocumentId) {
	header("Content-Type: application/zip");
} else {
	header("Content-Type: text/x-csv");
}


/* Това ми е за да имам имената на колоните + id-tata на филдовете */

$lMaterialsColumnsArr = array(
	'typeStatus' => array('id' => 209, 'term' => 'http://rs.tdwg.org/dwc/terms/typeStatus'),
	'scientificName' => array('id' => 83, 'term' => 'http://rs.tdwg.org/dwc/terms/scientificName'),
	'originalNameUsage' => array('id' => 86, 'term' => 'http://rs.tdwg.org/dwc/terms/originalNameUsage'),
	'originalNameUsageID' => array('id' => 79, 'term' => 'http://rs.tdwg.org/dwc/terms/originalNameUsageID'),
	'namePublishedIn' => array('id' => 88, 'term' => 'http://rs.tdwg.org/dwc/terms/namePublishedIn'),
	'namePublishedInID' => array('id' => 81, 'term' => 'http://rs.tdwg.org/dwc/terms/namePublishedInID'),
	'nameAccordingTo' => array('id' => 87, 'term' => 'http://rs.tdwg.org/dwc/terms/nameAccordingTo'),
	'nameAccordingToID' => array('id' => 80, 'term' => 'http://rs.tdwg.org/dwc/terms/nameAccordingToID'),
	'taxonConceptID' => array('id' => 82, 'term' => 'http://rs.tdwg.org/dwc/terms/taxonConceptID'),
	'acceptedNameUsage' => array('id' => 84, 'term' => 'http://rs.tdwg.org/dwc/terms/acceptedNameUsage'),
	'acceptedNameUsageID' => array('id' => 77, 'term' => 'http://rs.tdwg.org/dwc/terms/acceptedNameUsageID'),
	'taxonomicStatus' => array('id' => 104, 'term' => 'http://rs.tdwg.org/dwc/terms/taxonomicStatus'),
	'nomenclaturalStatus' => array('id' => 105, 'term' => 'http://rs.tdwg.org/dwc/terms/nomenclaturalStatus'),
	'taxonRemarks' => array('id' => 106, 'term' => 'http://rs.tdwg.org/dwc/terms/taxonRemarks'),
	'taxonID' => array('id' => 75, 'term' => 'http://rs.tdwg.org/dwc/terms/taxonID'),
	'scientificNameID' => array('id' => 76, 'term' => 'http://rs.tdwg.org/dwc/terms/scientificNameID'),
	'parentNameUsageID' => array('id' => 78, 'term' => 'http://rs.tdwg.org/dwc/terms/parentNameUsageID'),
	'parentNameUsage' => array('id' => 85, 'term' => 'http://rs.tdwg.org/dwc/terms/parentNameUsage'),
	'higherClassification' => array('id' => 89, 'term' => 'http://rs.tdwg.org/dwc/terms/higherClassification'),
	'kingdom' => array('id' => 90, 'term' => 'http://rs.tdwg.org/dwc/terms/kingdom'),
	'phylum' => array('id' => 91, 'term' => 'http://rs.tdwg.org/dwc/terms/phylum'),
	'class' => array('id' => 92, 'term' => 'http://rs.tdwg.org/dwc/terms/class'),
	'order' => array('id' => 93, 'term' => 'http://rs.tdwg.org/dwc/terms/order'),
	'family' => array('id' => 94, 'term' => 'http://rs.tdwg.org/dwc/terms/family'),
	'taxonRank' => array('id' => 99, 'term' => 'http://rs.tdwg.org/dwc/terms/taxonRank'),
	'verbatimTaxonRank' => array('id' => 100, 'term' => 'http://rs.tdwg.org/dwc/terms/verbatimTaxonRank'),
	'vernacularName' => array('id' => 102, 'term' => 'http://rs.tdwg.org/dwc/terms/vernacularName'),
	'nomenclaturalCode' => array('id' => 103, 'term' => 'http://rs.tdwg.org/dwc/terms/nomenclaturalCode'),
	'genus' => array('id' => 95, 'term' => 'http://rs.tdwg.org/dwc/terms/genus'),
	'subgenus' => array('id' => 96, 'term' => 'http://rs.tdwg.org/dwc/terms/subgenus'),
	'specificEpithet' => array('id' => 97, 'term' => 'http://rs.tdwg.org/dwc/terms/specificEpithet'),
	'infraspecificEpithet' => array('id' => 98, 'term' => 'http://rs.tdwg.org/dwc/terms/infraspecificEpithet'),
	'scientificNameAuthorship' => array('id' => 101, 'term' => 'http://rs.tdwg.org/dwc/terms/scientificNameAuthorship'),
	'locationID' => array('id' => 107, 'term' => 'http://rs.tdwg.org/dwc/terms/locationID'),
	'higherGeographyID' => array('id' => 108, 'term' => 'http://rs.tdwg.org/dwc/terms/higherGeographyID'),
	'higherGeography' => array('id' => 109, 'term' => 'http://rs.tdwg.org/dwc/terms/higherGeography'),
	'continent' => array('id' => 110, 'term' => 'http://rs.tdwg.org/dwc/terms/continent'),
	'waterBody' => array('id' => 111, 'term' => 'http://rs.tdwg.org/dwc/terms/waterBody'),
	'islandGroup' => array('id' => 112, 'term' => 'http://rs.tdwg.org/dwc/terms/islandGroup'),
	'island' => array('id' => 113, 'term' => 'http://rs.tdwg.org/dwc/terms/island'),
	'country' => array('id' => 114, 'term' => 'http://rs.tdwg.org/dwc/terms/country'),
	'countryCode' => array('id' => 115, 'term' => 'http://rs.tdwg.org/dwc/terms/countryCode'),
	'stateProvince' => array('id' => 116, 'term' => 'http://rs.tdwg.org/dwc/terms/stateProvince'),
	'county' => array('id' => 117, 'term' => 'http://rs.tdwg.org/dwc/terms/county'),
	'municipality' => array('id' => 118, 'term' => 'http://rs.tdwg.org/dwc/terms/municipality'),
	'locality' => array('id' => 119, 'term' => 'http://rs.tdwg.org/dwc/terms/locality'),
	'verbatimLocality' => array('id' => 120, 'term' => 'http://rs.tdwg.org/dwc/terms/verbatimLocality'),
	'verbatimElevation' => array('id' => 121, 'term' => 'http://rs.tdwg.org/dwc/terms/verbatimElevation'),
	'minimumElevationInMeters' => array('id' => 122, 'term' => 'http://rs.tdwg.org/dwc/terms/minimumElevationInMeters'),
	'maximumElevationInMeters' => array('id' => 123, 'term' => 'http://rs.tdwg.org/dwc/terms/maximumElevationInMeters'),
	'verbatimDepth' => array('id' => 124, 'term' => 'http://rs.tdwg.org/dwc/terms/verbatimDepth'),
	'minimumDepthInMeters' => array('id' => 125, 'term' => 'http://rs.tdwg.org/dwc/terms/minimumDepthInMeters'),
	'maximumDepthInMeters' => array('id' => 126, 'term' => 'http://rs.tdwg.org/dwc/terms/maximumDepthInMeters'),
	'minimumDistanceAboveSurfaceInMeters' => array('id' => 127, 'term' => 'http://rs.tdwg.org/dwc/terms/minimumDistanceAboveSurfaceInMeters'),
	'maximumDistanceAboveSurfaceInMeters' => array('id' => 128, 'term' => 'http://rs.tdwg.org/dwc/terms/maximumDistanceAboveSurfaceInMeters'),
	'locationAccordingTo' => array('id' => 129, 'term' => 'http://rs.tdwg.org/dwc/terms/locationAccordingTo'),
	'locationRemarks' => array('id' => 130, 'term' => 'http://rs.tdwg.org/dwc/terms/locationRemarks'),
	'verbatimCoordinates' => array('id' => 131, 'term' => 'http://rs.tdwg.org/dwc/terms/verbatimCoordinates'),
	'verbatimLatitude' => array('id' => 132, 'term' => 'http://rs.tdwg.org/dwc/terms/verbatimLatitude'),
	'verbatimLongitude' => array('id' => 133, 'term' => 'http://rs.tdwg.org/dwc/terms/verbatimLongitude'),
	'verbatimCoordinateSystem' => array('id' => 134, 'term' => 'http://rs.tdwg.org/dwc/terms/verbatimCoordinateSystem'),
	'verbatimSRS' => array('id' => 135, 'term' => 'http://rs.tdwg.org/dwc/terms/verbatimSRS'),
	'decimalLatitude' => array('id' => 136, 'term' => 'http://rs.tdwg.org/dwc/terms/decimalLatitude'),
	'decimalLongitude' => array('id' => 137, 'term' => 'http://rs.tdwg.org/dwc/terms/decimalLongitude'),
	'geodeticDatum' => array('id' => 138, 'term' => 'http://rs.tdwg.org/dwc/terms/geodeticDatum'),
	'coordinateUncertaintyInMeters' => array('id' => 139, 'term' => 'http://rs.tdwg.org/dwc/terms/coordinateUncertaintyInMeters'),
	'coordinatePrecision' => array('id' => 140, 'term' => 'http://rs.tdwg.org/dwc/terms/coordinatePrecision'),
	'pointRadiusSpatialFit' => array('id' => 141, 'term' => 'http://rs.tdwg.org/dwc/terms/pointRadiusSpatialFit'),
	'footprintWKT' => array('id' => 142, 'term' => 'http://rs.tdwg.org/dwc/terms/footprintWKT'),
	'footprintSRS' => array('id' => 143, 'term' => 'http://rs.tdwg.org/dwc/terms/footprintSRS'),
	'footprintSpatialFit' => array('id' => 144, 'term' => 'http://rs.tdwg.org/dwc/terms/footprintSpatialFit'),
	'georeferencedBy' => array('id' => 145, 'term' => 'http://rs.tdwg.org/dwc/terms/georeferencedBy'),
	'georeferenceProtocol' => array('id' => 146, 'term' => 'http://rs.tdwg.org/dwc/terms/georeferenceProtocol'),
	'georeferenceSources' => array('id' => 147, 'term' => 'http://rs.tdwg.org/dwc/terms/georeferenceSources'),
	'georeferenceVerificationStatus' => array('id' => 148, 'term' => 'http://rs.tdwg.org/dwc/terms/georeferenceVerificationStatus'),
	'georeferenceRemarks' => array('id' => 149, 'term' => 'http://rs.tdwg.org/dwc/terms/georeferenceRemarks'),
	'eventID' => array('id' => 175, 'term' => 'http://rs.tdwg.org/dwc/terms/eventID'),
	'samplingProtocol' => array('id' => 176, 'term' => 'http://rs.tdwg.org/dwc/terms/samplingProtocol'),
	'samplingEffort' => array('id' => 177, 'term' => 'http://rs.tdwg.org/dwc/terms/samplingEffort'),
	'eventDate' => array('id' => 178, 'term' => 'http://rs.tdwg.org/dwc/terms/eventDate'),
	'eventTime' => array('id' => 179, 'term' => 'http://rs.tdwg.org/dwc/terms/eventTime'),
	'startDayOfYear' => array('id' => 180, 'term' => 'http://rs.tdwg.org/dwc/terms/startDayOfYear'),
	'endDayOfYear' => array('id' => 181, 'term' => 'http://rs.tdwg.org/dwc/terms/endDayOfYear'),
	'year' => array('id' => 182, 'term' => 'http://rs.tdwg.org/dwc/terms/year'),
	'month' => array('id' => 183, 'term' => 'http://rs.tdwg.org/dwc/terms/month'),
	'day' => array('id' => 184, 'term' => 'http://rs.tdwg.org/dwc/terms/day'),
	'verbatimEventDate' => array('id' => 185, 'term' => 'http://rs.tdwg.org/dwc/terms/verbatimEventDate'),
	'habitat' => array('id' => 186, 'term' => 'http://rs.tdwg.org/dwc/terms/habitat'),
	'fieldNumber' => array('id' => 187, 'term' => 'http://rs.tdwg.org/dwc/terms/fieldNumber'),
	'fieldNotes' => array('id' => 188, 'term' => 'http://rs.tdwg.org/dwc/terms/fieldNotes'),
	'eventRemarks' => array('id' => 189, 'term' => 'http://rs.tdwg.org/dwc/terms/eventRemarks'),
	'individualID' => array('id' => 59, 'term' => 'http://rs.tdwg.org/dwc/terms/individualID'),
	'individualCount' => array('id' => 60, 'term' => 'http://rs.tdwg.org/dwc/terms/individualCount'),
	'sex' => array('id' => 61, 'term' => 'http://rs.tdwg.org/dwc/terms/sex'),
	'lifeStage' => array('id' => 62, 'term' => 'http://rs.tdwg.org/dwc/terms/lifeStage'),
	'preparations' => array('id' => 67, 'term' => 'http://rs.tdwg.org/dwc/terms/preparations'),
	'reproductiveCondition' => array('id' => 63, 'term' => 'http://rs.tdwg.org/dwc/terms/reproductiveCondition'),
	'behavior' => array('id' => 64, 'term' => 'http://rs.tdwg.org/dwc/terms/behavior'),
	'establishmentMeans' => array('id' => 65, 'term' => 'http://rs.tdwg.org/dwc/terms/establishmentMeans'),
	'catalogNumber' => array('id' => 54, 'term' => 'http://rs.tdwg.org/dwc/terms/catalogNumber'),
	'occurrenceDetails' => array('id' => 55, 'term' => 'http://rs.tdwg.org/dwc/terms/occurrenceDetails'),
	'occurrenceRemarks' => array('id' => 56, 'term' => 'http://rs.tdwg.org/dwc/terms/occurrenceRemarks'),
	'recordNumber' => array('id' => 57, 'term' => 'http://rs.tdwg.org/dwc/terms/recordNumber'),
	'recordedBy' => array('id' => 58, 'term' => 'http://rs.tdwg.org/dwc/terms/recordedBy'),
	'occurrenceStatus' => array('id' => 66, 'term' => 'http://rs.tdwg.org/dwc/terms/occurrenceStatus'),
	'disposition' => array('id' => 68, 'term' => 'http://rs.tdwg.org/dwc/terms/disposition'),
	'otherCatalogNumbers' => array('id' => 69, 'term' => 'http://rs.tdwg.org/dwc/terms/otherCatalogNumbers'),
	'previousIdentifications' => array('id' => 70, 'term' => 'http://rs.tdwg.org/dwc/terms/previousIdentifications'),
	'associatedMedia' => array('id' => 71, 'term' => 'http://rs.tdwg.org/dwc/terms/associatedMedia'),
	'associatedReferences' => array('id' => 72, 'term' => 'http://rs.tdwg.org/dwc/terms/associatedReferences'),
	'associatedOccurrences' => array('id' => 73, 'term' => 'http://rs.tdwg.org/dwc/terms/associatedOccurrences'),
	'associatedSequences' => array('id' => 74, 'term' => 'http://rs.tdwg.org/dwc/terms/associatedSequences'),
	'geologicalContextID' => array('id' => 157, 'term' => 'http://rs.tdwg.org/dwc/terms/geologicalContextID'),
	'earliestEonOrLowestEonothem' => array('id' => 158, 'term' => 'http://rs.tdwg.org/dwc/terms/earliestEonOrLowestEonothem'),
	'latestEonOrHighestEonothem' => array('id' => 159, 'term' => 'http://rs.tdwg.org/dwc/terms/latestEonOrHighestEonothem'),
	'earliestEraOrLowestErathem' => array('id' => 160, 'term' => 'http://rs.tdwg.org/dwc/terms/earliestEraOrLowestErathem'),
	'latestEraOrHighestErathem' => array('id' => 161, 'term' => 'http://rs.tdwg.org/dwc/terms/latestEraOrHighestErathem'),
	'earliestPeriodOrLowestSystem' => array('id' => 162, 'term' => 'http://rs.tdwg.org/dwc/terms/earliestPeriodOrLowestSystem'),
	'latestPeriodOrHighestSystem' => array('id' => 163, 'term' => 'http://rs.tdwg.org/dwc/terms/latestPeriodOrHighestSystem'),
	'earliestEpochOrLowestSeries' => array('id' => 164, 'term' => 'http://rs.tdwg.org/dwc/terms/earliestEpochOrLowestSeries'),
	'latestEpochOrHighestSeries' => array('id' => 165, 'term' => 'http://rs.tdwg.org/dwc/terms/latestEpochOrHighestSeries'),
	'earliestAgeOrLowestStage' => array('id' => 166, 'term' => 'http://rs.tdwg.org/dwc/terms/earliestAgeOrLowestStage'),
	'latestAgeOrHighestStage' => array('id' => 167, 'term' => 'http://rs.tdwg.org/dwc/terms/latestAgeOrHighestStage'),
	'lowestBiostratigraphicZone' => array('id' => 168, 'term' => 'http://rs.tdwg.org/dwc/terms/lowestBiostratigraphicZone'),
	'highestBiostratigraphicZone' => array('id' => 169, 'term' => 'http://rs.tdwg.org/dwc/terms/highestBiostratigraphicZone'),
	'lithostratigraphicTerms' => array('id' => 170, 'term' => 'http://rs.tdwg.org/dwc/terms/lithostratigraphicTerms'),
	'group' => array('id' => 171, 'term' => 'http://rs.tdwg.org/dwc/terms/group'),
	'formation' => array('id' => 172, 'term' => 'http://rs.tdwg.org/dwc/terms/formation'),
	'member' => array('id' => 173, 'term' => 'http://rs.tdwg.org/dwc/terms/member'),
	'bed' => array('id' => 174, 'term' => 'http://rs.tdwg.org/dwc/terms/bed'),
	'identificationID' => array('id' => 150, 'term' => 'http://rs.tdwg.org/dwc/terms/identificationID'),
	'identifiedBy' => array('id' => 151, 'term' => 'http://rs.tdwg.org/dwc/terms/identifiedBy'),
	'dateIdentified' => array('id' => 152, 'term' => 'http://rs.tdwg.org/dwc/terms/dateIdentified'),
	'identificationReferences' => array('id' => 153, 'term' => 'http://rs.tdwg.org/dwc/terms/identificationReferences'),
	'identificationRemarks' => array('id' => 154, 'term' => 'http://rs.tdwg.org/dwc/terms/identificationRemarks'),
	'identificationQualifier' => array('id' => 155, 'term' => 'http://rs.tdwg.org/dwc/terms/identificationQualifier'),
	'type' => array('id' => 190, 'term' => 'http://purl.org/dc/terms/type'),
	'modified' => array('id' => 191, 'term' => 'http://purl.org/dc/terms/modified'),
	'language' => array('id' => 192, 'term' => 'http://purl.org/dc/terms/language'),
	'rights' => array('id' => 193, 'term' => 'http://purl.org/dc/terms/rights'),
	'rightsHolder' => array('id' => 194, 'term' => 'http://purl.org/dc/terms/rightsHolder'),
	'accessRights' => array('id' => 195, 'term' => 'http://purl.org/dc/terms/accessRights'),
	'bibliographicCitation' => array('id' => 196, 'term' => 'http://purl.org/dc/terms/bibliographicCitation'),
	'institutionID' => array('id' => 197, 'term' => 'http://rs.tdwg.org/dwc/terms/institutionID'),
	'collectionID' => array('id' => 198, 'term' => 'http://rs.tdwg.org/dwc/terms/collectionID'),
	'datasetID' => array('id' => 199, 'term' => 'http://rs.tdwg.org/dwc/terms/datasetID'),
	'institutionCode' => array('id' => 200, 'term' => 'http://rs.tdwg.org/dwc/terms/institutionCode'),
	'collectionCode' => array('id' => 201, 'term' => 'http://rs.tdwg.org/dwc/terms/collectionCode'),
	'datasetName' => array('id' => 202, 'term' => 'http://rs.tdwg.org/dwc/terms/datasetName'),
	'ownerInstitutionCode' => array('id' => 203, 'term' => 'http://rs.tdwg.org/dwc/terms/ownerInstitutionCode'),
	'basisOfRecord' => array('id' => 204, 'term' => 'http://rs.tdwg.org/dwc/terms/basisOfRecord'),
	'informationWithheld' => array('id' => 205, 'term' => 'http://rs.tdwg.org/dwc/terms/informationWithheld'),
	'dataGeneralizations' => array('id' => 206, 'term' => 'http://rs.tdwg.org/dwc/terms/dataGeneralizations'),
	'dynamicProperties' => array('id' => 207, 'term' => 'http://rs.tdwg.org/dwc/terms/dynamicProperties'),
	'source' => array('id' => 208, 'term' => 'http://purl.org/dc/terms/source'),
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
$lMaterialsObjId = 38;

if($lAction == 'export_materials_as_csv') {
	$lCon = new DBCn();
	$lCon->Open();
	
	
	if($lDocumentId) {
		$lInstacesArr = array();
		$lAllMaterialInstancesByDocumentIdSql = '
			SELECT 
				id 
			FROM pwt.document_object_instances doi 
			WHERE document_id = ' . $lDocumentId . ' AND object_id = ' . $lMaterialsObjId . '
		';
		$lCon->Execute($lAllMaterialInstancesByDocumentIdSql);
		$lCon->MoveFirst();
		while(!$lCon->Eof()){
			$lInstacesArr[] = $lCon->mRs['id'];
			$lCon->MoveNext();
		}
		
		// fix za connection-a
		$lCon->Open();
		$lCon->Close();
		$lCon->Open();
	}
	
	
	$lGetMaterialsSql = '
		SELECT 
			doi.id, 
			doi.document_id,
			doi3.display_name as treatment_name
		FROM pwt.document_object_instances doi
		JOIN pwt.document_object_instances doi1 ON doi1.id = doi.parent_id
		JOIN pwt.document_object_instances doi2 ON doi2.id = doi1.parent_id
		JOIN pwt.document_object_instances doi3 ON doi3.id = doi2.parent_id
		WHERE doi.parent_id' . ($lDocumentId ? ' IN (' . implode(",", $lInstacesArr) . ')' : ' = ' . $lInstanceId)
	;
	$lCon->Execute($lGetMaterialsSql);
	
	// var_dump($lGetMaterialsSql);
	// exit;
	
	$lCon->MoveFirst();
	while(!$lCon->Eof()){
		$lMaterialsArr[] = $lCon->mRs;
		$lCon->MoveNext();
	}
	
	if($lDocumentId) {
		$lTmpDir = '/tmp/materials_' . $lDocumentId;
		mkdir($lTmpDir);
	} else {
		$lTmpDir = '/tmp';
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
				
				if(!SearchArray($lMaterialsColumnsArr, $fldval['field_id']) && !$gHeader) {
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
	
	if($lDocumentId) {
		$lFileName = 'occurrence.txt';
	} else {
		$lTreatmentName = strip_tags($lTreatmentName);
		$lTreatmentName = str_replace(' ', '_', $lTreatmentName);
		$lFileName = 'materials_' . $lTreatmentName . '_' . $lMaterialDocumentId . '.csv';
		header("Content-Disposition: attachment;filename=\"" . $lFileName . "\"");
	}
	
	$file = fopen($lTmpDir . '/' . $lFileName,"w");
	
	$lheader = array();
	ksort($lHeaderAdd);
	$lFirstColumnHeader = array();
	if($lDocumentId) {
		$lFirstColumnHeader[] = 'OccurenceID';
	}
	$lheader = array_merge($lFirstColumnHeader, array_keys($lMaterialsColumnsArr), $lHeaderAdd);

	fputcsv($file, $lheader, ';');
	fseek($file, -1, SEEK_CUR); 
	fwrite($file, "\r\n"); 
	
	$i = 1;
	foreach($lMaterialsFieldValuesArr as $mmkey => $mmval) {
		$lTempArr = $lMaterialsFieldsArr;
		$lTemp2Arr = array();
		$lResultArr = array();
		//var_dump($mmval);
		$lTempArr0 = array();
		if($lDocumentId) {
			$lTempArr0[0] = 'BDJ_' . $lDocumentId . '_' . $i;
		}
		foreach($mmval as $k => $v) {
			if(SearchArray($lMaterialsColumnsArr, $k)){
				$lTempArr[$k] = $v;
			} else {
				$lTemp2Arr[$k] = $v;
			}
		}
		
		ksort($lTemp2Arr);
		$lResultArr = $lTempArr0 + $lTempArr + $lTemp2Arr;
		
		fputcsv($file, $lResultArr, ';');
		fseek($file, -1, SEEK_CUR); 
		fwrite($file, "\r\n"); 
		$i++;
	}
	//exit;
	fclose($file);
	// var_dump($lTmpDir . '/' . $lFileName);
	// exit;
	//file_put_contents('/tmp/test_csv.csv', $data)
	if($lDocumentId) {
		MakeMetaXml($lTmpDir, $lFileName, $lMaterialsColumnsArr);
		MakeEmlXML($lTmpDir, $lDocumentId);
		
		$lFilesToZip = array(
			array(
				'location' => $lTmpDir . '/' . $lFileName,
				'name' =>  $lFileName
			),
			array(
				'location' => $lTmpDir . '/eml.xml',
				'name' =>  'eml.xml'
			),
			array(
				'location' => $lTmpDir . '/meta.xml',
				'name' =>  'meta.xml'
			)
		);
		
		//if true, good; if false, zip creation failed
		$lZipFile = 'dc_' . $lDocumentId . '.zip';
		$lResult = create_zip($lFilesToZip, '/tmp/' . $lZipFile);
		if($lResult) {
			header('Content-Length: ' . filesize('/tmp/' . $lZipFile));
			header("Content-Disposition: attachment;filename=\"" . $lZipFile . "\"");
			readfile('/tmp/' . $lZipFile);
		}
		if(file_exists('/tmp/' . $lZipFile)) {
			unlink('/tmp/' . $lZipFile);
		}
		exit;
	}
	$lContents = file_get_contents($lTmpDir . '/' . $lFileName);
	unlink($lTmpDir . '/' . $lFileName);
	
	$lCsvStr = $lContents;
	//$lCsvStr = str_replace(array("\n"), "\r\n", $lCsvStr);
	
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
	//$lContent = str_replace(chr(194), '', $lContent);
	
	$lContent = str_replace('&nbsp;', '', $lContent);
	$lTableDescription = '<body>' . $lContent . '</body>'; 
	
	$lDoc = new DOMDocument('1.0', DEFAULT_XML_ENCODING);
	$lDoc->loadXML($lTableDescription);
	$lDoc->encoding = DEFAULT_XML_ENCODING;
	
	
	$lXpath = new DOMXPath($lDoc);
	$lTable = $lXpath->query("//table[position() = 1]");
	
	$lHeader = $lXpath->query(".//th", $lTable->item(0));
	$lHeaderArr = array();
	if($lHeader->length){
		for($i = 0; $i < $lHeader->length; ++$i){
			$lHeaderArr[] = trim($lHeader->item($i)->nodeValue);
		}
	}

	$lFileName = 'table_' . (int)$lInstanceId . '.csv';
	header("Content-Disposition: attachment;filename=\"" . $lFileName . "\"");
	
	$file = fopen('/tmp/' . $lFileName,"w");
	
	if(count($lHeaderArr)){
		fputcsv($file, $lHeaderArr, ';');
		fseek($file, -1, SEEK_CUR); 
		fwrite($file, "\r\n"); 
	}
	
	$lRows = $lXpath->query(".//tr", $lTable->item(0));
	
	if($lRows->length){
		for($i = 0; $i < $lRows->length; ++$i){
			$lColsArr = array();
			$lColumns = $lXpath->query(".//td", $lRows->item($i));
			if($lColumns->length){
				for($j = 0; $j < $lColumns->length; ++$j){
					$lValue = trim($lColumns->item($j)->nodeValue);
					
					/*
					if($lValue == '+' || $lValue == '-') {
						$lValue = '\\' . $lValue;
					}*/
					
					$lColsArr[] = $lValue;
				}
				fputcsv($file, $lColsArr, ';');
				fseek($file, -1, SEEK_CUR); 
            	fwrite($file, "\r\n"); 
			}
		}
	}

	fclose($file);
	
	$lContents = file_get_contents('/tmp/' . $lFileName);
	$lCsvStr = $lContents;
	unlink('/tmp/' . $lFileName);	
	
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

function SearchArray($pArr, $pVal) {
	foreach ($pArr as $key => $value) {
		if($value['id'] == $pVal) {
			return TRUE;
		}
	}
	return FALSE;
}

function MakeMetaXml($pTmpDir, $lCsvFileName, $lDarwinCoreFieldsArr){
	$lDoc = new DOMDocument('1.0', DEFAULT_XML_ENCODING);
	$lDoc->formatOutput = true;
	
	// root element
	$lRootElement = $lDoc->createElement('archive');
	
	// archive xmlns attr
	$lDomAttribute = $lDoc->createAttribute('xmlns');
	$lRootElement->appendChild($lDomAttribute);
	$lDomAttributeValue = $lDoc->createTextNode("http://rs.tdwg.org/dwc/text/");
	$lDomAttribute->appendChild($lDomAttributeValue);
	
	// archive metadata attr
	$lDomAttribute = $lDoc->createAttribute('metadata');
	$lRootElement->appendChild($lDomAttribute);
	$lDomAttributeValue = $lDoc->createTextNode("eml.xml");
	$lDomAttribute->appendChild($lDomAttributeValue);
	
	// core node
	$lCoreElement = $lDoc->createElement('core');
	
	// core encoding attr
	$lDomAttribute = $lDoc->createAttribute('encoding');
	$lCoreElement->appendChild($lDomAttribute);
	$lDomAttributeValue = $lDoc->createTextNode(DEFAULT_XML_ENCODING);
	$lDomAttribute->appendChild($lDomAttributeValue);
	
	// core fieldsTerminatedBy attr
	$lDomAttribute = $lDoc->createAttribute('fieldsTerminatedBy');
	$lCoreElement->appendChild($lDomAttribute);
	$lDomAttributeValue = $lDoc->createTextNode(";");
	$lDomAttribute->appendChild($lDomAttributeValue);
	
	// core linesTerminatedBy attr
	$lDomAttribute = $lDoc->createAttribute('linesTerminatedBy');
	$lCoreElement->appendChild($lDomAttribute);
	$lDomAttributeValue = $lDoc->createTextNode("\\r\\n");
	$lDomAttribute->appendChild($lDomAttributeValue);
	
	// core fieldsEnclosedBy attr
	$lDomAttribute = $lDoc->createAttribute('fieldsEnclosedBy');
	$lCoreElement->appendChild($lDomAttribute);
	$lDomAttributeValue = $lDoc->createTextNode("\"");
	$lDomAttribute->appendChild($lDomAttributeValue);
	
	// core ignoreHeaderLines attr
	$lDomAttribute = $lDoc->createAttribute('ignoreHeaderLines');
	$lCoreElement->appendChild($lDomAttribute);
	$lDomAttributeValue = $lDoc->createTextNode("1");
	$lDomAttribute->appendChild($lDomAttributeValue);
	
	// core rowType attr
	$lDomAttribute = $lDoc->createAttribute('rowType');
	$lCoreElement->appendChild($lDomAttribute);
	$lDomAttributeValue = $lDoc->createTextNode("http://rs.tdwg.org/dwc/terms/Occurrence");
	$lDomAttribute->appendChild($lDomAttributeValue);
	
	
	// files node
	$lFilesElement = $lDoc->createElement('files');
	$lLocationElement = $lDoc->createElement('location');
	$lLocationElementValue = $lDoc->createTextNode($lCsvFileName);
	$lLocationElement->appendChild($lLocationElementValue);
	$lFilesElement->appendChild($lLocationElement);
	$lCoreElement->appendChild($lFilesElement);
	
	
	// id node
	$lIdElement = $lDoc->createElement('id');
	// id index attr
	$lDomAttribute = $lDoc->createAttribute('index');
	$lIdElement->appendChild($lDomAttribute);
	$lDomAttributeValue = $lDoc->createTextNode("0");
	$lDomAttribute->appendChild($lDomAttributeValue);
	$lCoreElement->appendChild($lIdElement);
	
	// field nodes
	$i = 1;
	foreach ($lDarwinCoreFieldsArr as $key => $value) {
		$lElement = $lDoc->createElement('field');
		
		// field index attr
		$lDomAttribute = $lDoc->createAttribute('index');
		$lElement->appendChild($lDomAttribute);
		$lDomAttributeValue = $lDoc->createTextNode($i);
		$lDomAttribute->appendChild($lDomAttributeValue);
		
		// field term attr
		$lDomAttribute = $lDoc->createAttribute('term');
		$lElement->appendChild($lDomAttribute);
		$lDomAttributeValue = $lDoc->createTextNode($value['term']);
		$lDomAttribute->appendChild($lDomAttributeValue);
		
		$lCoreElement->appendChild($lElement);
		
		$i++;
	} 
	
	$lRootElement->appendChild($lCoreElement);
	
	$lContent = $lDoc->saveXML($lRootElement);
	$file = fopen($pTmpDir . '/meta.xml',"w");
	fwrite($file, $lContent);
	fclose($file);
}

function MakeEmlXML($pTmpDir, $pDocumentId) {
	$lEmlData = array();
	$lEmlData = GetEmlData($pDocumentId);
	
	$lEmlXML = file_get_contents(SITE_URL . '/lib/eml.xml');
	$lDoc = new DOMDocument('1.0', DEFAULT_XML_ENCODING);
	$lDoc->formatOutput = true;
	$lDoc->loadXML($lEmlXML);
	$lDoc->encoding = DEFAULT_XML_ENCODING;
	
	$lXPath = new DOMXPath($lDoc);
	$lXPath->registerNamespace('eml', 'eml://ecoinformatics.org/eml-2.1.1');
	$lElement = $lXPath->query('/eml:eml/dataset/title');
	$lElement->item(0)->nodeValue = $lEmlData['title'];
	
	$lElement = $lXPath->query('/eml:eml/dataset/creator/individualName/surName');
	$lElement->item(0)->nodeValue = $lEmlData['cor_auth_last_name'];
	
	$lElement = $lXPath->query('/eml:eml/dataset/creator/organizationName');
	$lElement->item(0)->nodeValue = $lEmlData['cor_auth_affiliation'];
	
	$lElement = $lXPath->query('/eml:eml/dataset/creator/positionName');
	$lElement->item(0)->nodeValue = $lEmlData['cor_auth_salutation'];
	
	$lElement = $lXPath->query('/eml:eml/dataset/metadataProvider/individualName/givenName');
	$lElement->item(0)->nodeValue = $lEmlData['cor_auth_first_name'];
	
	$lElement = $lXPath->query('/eml:eml/dataset/metadataProvider/individualName/surName');
	$lElement->item(0)->nodeValue = $lEmlData['cor_auth_last_name'];
	
	$lElement = $lXPath->query('/eml:eml/dataset/metadataProvider/organizationName');
	$lElement->item(0)->nodeValue = $lEmlData['cor_auth_affiliation'];
	
	$lElement = $lXPath->query('/eml:eml/dataset/metadataProvider/positionName');
	$lElement->item(0)->nodeValue = $lEmlData['cor_auth_salutation'];
	
	$lElement = $lXPath->query('/eml:eml/dataset/metadataProvider/address/deliveryPoint');
	$lElement->item(0)->nodeValue = $lEmlData['cor_auth_country'];
	
	$lElement = $lXPath->query('/eml:eml/dataset/metadataProvider/address/city');
	$lElement->item(0)->nodeValue = $lEmlData['cor_auth_city'];
	
	$lElement = $lXPath->query('/eml:eml/dataset/pubDate');
	$lElement->item(0)->nodeValue = date('Y') . '-' . date('m') . '-' . date('d');
	
	$lElement = $lXPath->query('/eml:eml/dataset/abstract/para');
	$lElement->item(0)->nodeValue = $lEmlData['abstract'];
	
	$lElement = $lXPath->query('/eml:eml/dataset/contact/individualName/givenName');
	$lElement->item(0)->nodeValue = $lEmlData['cor_auth_first_name'];
	
	$lElement = $lXPath->query('/eml:eml/dataset/contact/individualName/surName');
	$lElement->item(0)->nodeValue = $lEmlData['cor_auth_last_name'];
	
	$lElement = $lXPath->query('/eml:eml/dataset/contact/organizationName');
	$lElement->item(0)->nodeValue = $lEmlData['cor_auth_affiliation'];
	
	$lElement = $lXPath->query('/eml:eml/dataset/contact/positionName');
	$lElement->item(0)->nodeValue = $lEmlData['cor_auth_salutation'];
	
	$lElement = $lXPath->query('/eml:eml/dataset/contact/address/city');
	$lElement->item(0)->nodeValue = $lEmlData['cor_auth_city'];
	
	$lElement = $lXPath->query('/eml:eml/dataset/contact/address/country');
	$lElement->item(0)->nodeValue = $lEmlData['cor_auth_country'];
	
	$lContent = $lDoc->saveXML();
	$file = fopen($pTmpDir . '/eml.xml',"w");
	fwrite($file, $lContent);
	fclose($file);
}

function GetEmlData($pDocumentId) {
	$lConSec = new DBCn();
	$lConSec->Open();
	
	$lObjs = '9,15,8,5';
	$lFields = '3,18,5,4,6,7,8,9,10,11,15';
	
	$lArrEmlValues = array();
	
	$lSql = '
		SELECT 
			(CASE WHEN doi.object_id = 5 THEN 8 ELSE doi.object_id END) as object_id, 
			(CASE WHEN doi.object_id = 5 THEN ifv.parent_id ELSE ifv.instance_id END) as instance_id, ifv.field_id, ifv.name, ifv.type, ifv.label, 
	    	ifv.control_type, ifv.allow_nulls, 
	    	ifv.has_help_label, ifv.help_label, ifv.data_src_id, 
	    	ifv.src_query, ifv.value_str, ifv.value_int, ifv.value_arr_int, 
	    	ifv.value_arr_str, ifv.value_date, ifv.value_arr_date, ifv.value_column_name, 
	    	ifv.display_label, ifv.css_class, 
	    	ifv.has_example_label, ifv.example_label, 
	    	ifv.help_label_display_style, ifv.is_read_only, 
	    	ifv.autocomplete_row_templ, ifv.autocomplete_onselect, 
	    	ifv.is_array, ifv.is_html
		FROM pwt.document_object_instances doi
		JOIN pwt.v_instance_fields_eml ifv ON ifv.instance_id = doi.id 
		WHERE document_id = ' . $pDocumentId . ' AND doi.object_id IN (' . $lObjs . ') AND ifv.field_id IN (' . $lFields . ')
		ORDER BY instance_id
	';
	
	$lConSec->Execute($lSql);
	//var_dump($lSql);
	$lConSec->MoveFirst();
	while (!$lConSec->Eof()) {
		$lFieldValueColumn = $lConSec->mRs['value_column_name'];
		$lFieldValue = $lConSec->mRs[$lFieldValueColumn];
		
		if($lFieldValue) {
			$lParsedValue = parseFieldValue($lFieldValue, $lConSec->mRs['type']);
			
			if($lConSec->mRs['src_query']) {
				$lParsedValue = getFieldSelectOptionsById($lConSec->mRs['src_query'], $lParsedValue, $lMaterialDocumentId, $lConSec->mRs['instance_id']);
				if(is_array($lParsedValue)) {
					$lParsedValue = implode(";", $lParsedValue);
				}
			}
			
		} else {
			$lParsedValue = '';
		}
		
		$lArrEmlValues[$lConSec->mRs['object_id']][$lConSec->mRs['instance_id']][$lConSec->mRs['field_id']] = $lParsedValue;
		
		$lConSec->MoveNext();	
	}
	
	$lResArr = array();
	foreach ($lArrEmlValues as $key => $value) {
		switch ($key) {
			// title
			case '9':
				foreach ($value as $key1 => $value1) {
					$lResArr['title'] = strip_tags($value1[3]);
				}
				break;
			// abstract
			case '15':
				foreach ($value as $key1 => $value1) {
					$lResArr['abstract'] = strip_tags($value1[18]);
				}
				break;
			// author
			case '8':
				$lBreak = 0;
				foreach ($value as $key1 => $value1) {
					if($value1[15]) {
						$lResArr['cor_auth_email'] = $value1[4];
						$lResArr['cor_auth_salutation'] = $value1[5];
						$lResArr['cor_auth_first_name'] = $value1[6];
						$lResArr['cor_auth_middle_name'] = $value1[7];
						$lResArr['cor_auth_last_name'] = $value1[8];
						$lResArr['cor_auth_affiliation'] = $value1[9];
						$lResArr['cor_auth_city'] = $value1[10];
						$lResArr['cor_auth_country'] = $value1[11];
						break;
					}
				}
				break;
			default:
				break;
		}
	}
	
	return $lResArr;
}

/* creates a compressed zip file */
function create_zip($files = array(),$destination = '',$overwrite = false) {
	//if the zip file already exists and overwrite is false, return false
	if(file_exists($destination) && !$overwrite) { return false; }
	//vars
	$valid_files = array();
	//if files were passed in...
	if(is_array($files)) {
		//cycle through each file
		foreach($files as $file) {
			//make sure the file exists
			if(file_exists($file['location'])) {
				$valid_files[] = $file;
			}
		}
	}
	//if we have good files...
	if(count($valid_files)) {
		//create the archive
		$zip = new ZipArchive();
		if($zip->open($destination,$overwrite ? ZIPARCHIVE::OVERWRITE : ZIPARCHIVE::CREATE) !== true) {
			return false;
		}
		//add the files
		foreach($valid_files as $file) {
			$zip->addFile($file['location'],$file['name']);
		}
		//debug
		//echo 'The zip archive contains ',$zip->numFiles,' files with a status of ',$zip->status;
		
		//close the zip -- done!
		$zip->close();
		
		//check to make sure the file exists
		return file_exists($destination);
	}
	else
	{
		return false;
	}
}

?>