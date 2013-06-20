<?php
$docroot = getenv('DOCUMENT_ROOT');
require_once($docroot . '/lib/static.php');

$lPageSize = 6;
$lSText = urldecode($_REQUEST['stext']);
$lSearchCategory = (int) $_REQUEST['catsearch'];

if(!$lSearchCategory){
	header('Location:/index.php');
}

switch ( $lSearchCategory ){
	case SEARCH_IN_ARTICLE : {
		
			$gInstanceId = (int) $_REQUEST['instance_id'];
			$gDocumentId = (int) $_REQUEST['document_id'];

			$gDontRedirectAgain = 1;

			if(!$gDocumentId){
				header('Location:/index.php');
			}

			if(! $gInstanceId && $gDocumentId){
				$gInstanceId = getDocumentFirstInstanceId($gDocumentId);
			}
		
			$gDocument = new cdisplay_document(
				array(
					'ctype' => 'cdisplay_document',
					'instance_id' => $gInstanceId,
					'get_data_from_request' => false,
					'get_object_mode_from_request' => false,
					'lock_operation_code' => LOCK_AUTO_LOCK,
					'templs' => getDocumentDefaultTempls(),
					'field_templs' => getDocumentFieldDefaultTempls(),
					'container_templs' => getDocumentContainerDefaultTempls(),
					'instance_templs' => getDocumentInstanceDefaultTempls(),
					'custom_html_templs' => getDocumentCustomHtmlItemsDefaultTempls(),
					'action_templs' => getDocumentActionsDefaultTempls(),
					'tabbed_element_templs' => getDocumentTabbedElementDefaultTempls(),
					'tree_templs' => getDocumentTreeDefaultTempls(),
					'path_templs' => getDocumentPathDefaultTempls(),
					'dont_redir_to_view' => $gDontRedirectAgain,
					'comments_templ' => 'comments',
					'comments_in_preview_mode' => 1,
					'comments_form_templ' => 'commentform',
				)
			);
		
						
			$gDocumentId = $gDocument->getDocumentId();
			$gInstanceId = $gDocument->getInstanceId();

			if($gInstanceId == getDocumentMetadataInstanceId($gDocumentId)){
				header("Location: /create_document.php?tAction=edit&document_id=" . (int) $gDocumentId);
				exit();
			}
		
			$lSearchResults = new crs(
				array(
					'ctype' => 'crs',
					'templs' => array(
						G_HEADER => 'global.empty',
						G_FOOTER => 'global.empty',
						G_ROWTEMPL => 'search.articleRowInDocument',
						G_NODATA => 'search.nodata_article',
					),
					'sqlstr' => 'SELECT
									ts_headline(\'english\', d.doc_html, to_tsquery(\'english\', \'' . q(urlencode($lSText)) . '\'), 
										\'StartSel=\'\'<span class="P-Search-Result-Highlighted">\'\', StopSel=</span>, HighlightAll=TRUE\') as searchresult
								FROM pwt.documents d
								JOIN pwt.document_users du ON du.document_id = d.id AND du.usr_type = ' . DOCUMENT_AUTHOR_TYPE_ID . '
								WHERE du.usr_id = ' . (int)$user->id . ' 
									AND (to_tsquery(\'english\', \'' . q(urlencode($lSText)) . '\') @@ to_tsvector(\'english\', d.doc_html)) 
									AND d.id = ' . (int) $gDocumentId,
				)
			);
			
			$t = array (
				'content' => array(
					'ctype' => 'csimple',
					'searchcontent' => $lSearchResults,
					'templs' => array(
						G_DEFAULT => 'search.holder_in_document',
					),
					'document_id' => $gDocumentId,
				),
				'commentform' => $gDocument->GetVal('commentform'),
				'comments' => $gDocument->GetVal('comments'),
				'document_id' => $gDocumentId,
				'path' => $gDocument->getDocumentPath(),
				'tree' =>  $gDocument->getDocumentTree(),
				'document_id' => $gDocumentId,
				'document_lock_usr_id' => $gDocument->getDocumentLockUserId(),
				'document_is_locked' => $gDocument->getDocumentIsLock(),
				'preview_mode' => 1,
				'without_warning' => 1,
				'search_str' => urlencode($lSText),
			);
			
			$inst = new cpage(array_merge($t, DefObjTempl()), array(G_MAINBODY => 'global.search_in_document_page'));
			$inst->Display();
		break;
	}
	case SEARCH_IN_ALL_ARTICLES : {
		$lSearchResults = new csearch(
			array(
				'ctype' => 'csearch',
				'allownull' => 1,
				'templs' => array(
					G_HEADER => 'search.headCommon',
					G_FOOTER => 'search.footCommon',
					G_STARTRS => 'search.startCommon',
					G_ROWTEMPL => 'search.articleRow',
					G_ENDRS => 'search.endCommon',
					G_NODATA => 'search.nodata',
				),
				'label' => getstr('search.typeUsers'),
				'encoded_stext' => urlencode($lSText),
				'usecustompn' => 1,
				'pagesize' => $lPageSize,
				'dontuseliketable' => 0,
				'schema' => 'bg_utf8',
				'shema' => 'bg_utf8',
				'sqlstr' => 'SELECT 
								d.id as document_id, 
								d.name as name, 
								d.lastmoddate, 
								array_to_string(array_agg(du.first_name), \', \') as fullname 
							FROM pwt.documents d
							JOIN pwt.document_users du ON du.document_id = d.id AND du.usr_type = ' . DOCUMENT_AUTHOR_TYPE_ID . '
							',
				//~ 'toparse' => array('title', 'description'), // koi poleta da ocvetqva... trqbva da sa selektnati!!!
				'vectors' => array('doc_html'), // v koi vektori da tursi tsearch2
				'fields' => array('name'),
				'toparse' => array('name'), // koi poleta da ocvetqva... trqbva da sa selektnati!!!
			)
		);
		$t = array (
			'content' => array(
				'ctype' => 'csimple',
				'searchcontent' => $lSearchResults,
				'templs' => array(
					G_DEFAULT => 'search.holder',
				),
			),
			'search_str' => urlencode($lSText),
		);
		$inst = new cpage(array_merge($t, DefObjTempl()), array(G_MAINBODY => 'global.search_page'));
		$inst->Display();
		break;
	}
}
?>