<?php
$docroot = getenv('DOCUMENT_ROOT');
require_once($docroot . '/lib/static.php');
$gVerb = $_REQUEST['verb'];
$gIdentifier = $_REQUEST['identifier'];
$gResumptionToken = $_REQUEST['resumptionToken'];
$gFrom = $_REQUEST['from'];
$gUntil = $_REQUEST['until'];
$gMetadataPrefix = $_REQUEST['metadataPrefix'];
$gSet = $_REQUEST['set'];

$gPageSize = 10;

switch($gVerb){
	default:
	case VERB_GET_IDENTIFY:{
		$gContent = new coai_identify(array(
			'templs' => array(
				G_DEFAULT => 'identify.default'
			)
		));
		break;
	}
	case VERB_GET_LIST_SETS:{
		$gContent = new coai_sets(array(
			'templs' => array(
				G_HEADER => 'sets.listHead',
				G_FOOTER => 'sets.listFoot',
				G_STARTRS => 'sets.listStart',
				G_ENDRS => 'sets.listEnd',
				G_ERR_TEMPL => 'global.errRow',
				G_ROWTEMPL => 'sets.listRow',
				
				G_PAGEING_STARTRS => 'global.empty', 
				G_PAGEING_INACTIVEFIRST => 'global.empty',
				G_PAGEING_ACTIVEFIRST => 'global.empty',
				G_PAGEING_PGSTART => 'global.empty',
				G_PAGEING_INACTIVEPAGE => 'global.empty',
				G_PAGEING_ACTIVEPAGE => 'global.empty',
				G_PAGEING_PGEND => 'global.empty',
				G_PAGEING_ENDRS => 'global.empty',
				G_PAGEING_DELIMETER => 'global.empty',
				G_PAGEING_INACTIVELAST => 'global.empty',
				G_PAGEING_ACTIVELAST => 'sets.pageingActiveLast'				
			),
			'pagesize' => $gPageSize,
			'usecustompn' => 1,
			'usefirstlast' => 1,
			'resumption_token' => $gResumptionToken,
		));
		break;
	}
	case VERB_GET_LIST_IDENTIFIERS:{
		$gContent = new coai_records(array(
			'templs' => array(
				G_HEADER => 'identifiers.listHead',
				G_FOOTER => 'identifiers.listFoot',
				G_STARTRS => 'identifiers.listStart',
				G_ENDRS => 'identifiers.listEnd',
				G_ERR_TEMPL => 'global.errRow',
				G_ROWTEMPL => 'identifiers.listRow',
				
				
				G_PAGEING_STARTRS => 'global.empty', 
				G_PAGEING_INACTIVEFIRST => 'global.empty',
				G_PAGEING_ACTIVEFIRST => 'global.empty',
				G_PAGEING_PGSTART => 'global.empty',
				G_PAGEING_INACTIVEPAGE => 'global.empty',
				G_PAGEING_ACTIVEPAGE => 'global.empty',
				G_PAGEING_PGEND => 'global.empty',
				G_PAGEING_ENDRS => 'global.empty',
				G_PAGEING_DELIMETER => 'global.empty',
				G_PAGEING_INACTIVELAST => 'global.empty',
				G_PAGEING_ACTIVELAST => 'identifiers.pageingActiveLast'				
			),
			'sets_templs' => array(
				G_ROWTEMPL => 'identifiers.listSetRow',
			),
			'parse_sets' => 1,
			'pagesize' => $gPageSize,
			'usecustompn' => 1,
			'usefirstlast' => 1,
			'resumption_token' => $gResumptionToken,
			'from' => $gFrom,
			'until' => $gUntil,
			'metadata_prefix' => $gMetadataPrefix,
			'set' => $gSet,
		));
		break;
	}
	case VERB_GET_LIST_RECORDS:{
		$gContent = new coai_records(array(
			'templs' => array(
				G_HEADER => 'records.listHead',
				G_FOOTER => 'records.listFoot',
				G_STARTRS => 'records.listStart',
				G_ENDRS => 'records.listEnd',
				G_ERR_TEMPL => 'global.errRow',
				G_ROWTEMPL => 'records.listRow',
				
				
				G_PAGEING_STARTRS => 'global.empty', 
				G_PAGEING_INACTIVEFIRST => 'global.empty',
				G_PAGEING_ACTIVEFIRST => 'global.empty',
				G_PAGEING_PGSTART => 'global.empty',
				G_PAGEING_INACTIVEPAGE => 'global.empty',
				G_PAGEING_ACTIVEPAGE => 'global.empty',
				G_PAGEING_PGEND => 'global.empty',
				G_PAGEING_ENDRS => 'global.empty',
				G_PAGEING_DELIMETER => 'global.empty',
				G_PAGEING_INACTIVELAST => 'global.empty',
				G_PAGEING_ACTIVELAST => 'records.pageingActiveLast'				
			),
			
			'sets_templs' => array(
				G_ROWTEMPL => 'records.listSetRow',
			),
			'parse_sets' => 1,
			
			'parse_keywords' => 1,
			'keywords_templs' => array(
				G_ROWTEMPL => 'records.listKeywordsRow',
			),
			
			'parse_authors' => 1,
			'authors_templs' => array(
				G_ROWTEMPL => 'records.listAuthorsRow',
			),
			
			'pagesize' => $gPageSize,
			'usecustompn' => 1,
			'usefirstlast' => 1,
			'resumption_token' => $gResumptionToken,
			'from' => $gFrom,
			'until' => $gUntil,
			'metadata_prefix' => $gMetadataPrefix,
			'set' => $gSet,
		));
		break;
	}
	case VERB_GET_RECORD:{
		$gContent = new coai_single_record(array(
			'templs' => array(
				G_HEADER => 'records.singleItemHead',
				G_FOOTER => 'records.singleItemFoot',
				G_STARTRS => 'records.singleItemStart',
				G_ENDRS => 'records.singleItemEnd',
				G_ERR_TEMPL => 'global.errRow',
				G_ROWTEMPL => 'records.listRow',
				
				
				G_PAGEING_STARTRS => 'global.empty', 
				G_PAGEING_INACTIVEFIRST => 'global.empty',
				G_PAGEING_ACTIVEFIRST => 'global.empty',
				G_PAGEING_PGSTART => 'global.empty',
				G_PAGEING_INACTIVEPAGE => 'global.empty',
				G_PAGEING_ACTIVEPAGE => 'global.empty',
				G_PAGEING_PGEND => 'global.empty',
				G_PAGEING_ENDRS => 'global.empty',
				G_PAGEING_DELIMETER => 'global.empty',
				G_PAGEING_INACTIVELAST => 'global.empty',
				G_PAGEING_ACTIVELAST => 'global.empty'				
			),
			
			'sets_templs' => array(
				G_ROWTEMPL => 'records.listSetRow',
			),
			'parse_sets' => 1,
			
			'parse_keywords' => 1,
			'keywords_templs' => array(
				G_ROWTEMPL => 'records.listKeywordsRow',
			),
			
			'parse_authors' => 1,
			'authors_templs' => array(
				G_ROWTEMPL => 'records.listAuthorsRow',
			),
			
			'pagesize' => $gPageSize,
			'usecustompn' => 1,
			'usefirstlast' => 1,
			'identifier' => $gIdentifier,
			'metadata_prefix' => $gMetadataPrefix,
			'set' => $gSet,
		));
		break;
	}
	case VERB_GET_LIST_METADATA_FORMATS:{
		$gContent = new coai_metadata_formats(array(
			'identifier' => $gIdentifier,
			'templs' => array(
				G_HEADER => 'metadata.formatsHead',
				G_FOOTER => 'metadata.formatsFoot',
				G_STARTRS => 'metadata.formatsStart',
				G_ENDRS => 'metadata.formatsEnd',
				G_ERR_TEMPL => 'global.errRow',
				G_ITEM_ROW_TEMPL => 'metadata.formatsItemRow',
				G_GLOBAL_ROW_TEMPL => 'metadata.formatsGlobalRow',
			)
		));
		break;
	}
}


$t = array(
	'content' => $gContent,
);
$inst = new cxml_cpage(array_merge($t, DefObjTempl()), array(G_MAINBODY => 'global.indexPage'));
$inst->Display();
?>