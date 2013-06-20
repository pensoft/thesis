<?php
$docroot = getenv('DOCUMENT_ROOT');
require_once($docroot . '/lib/static.php');

$gForm = new ctplkfor(
	array(
		'ctype' => 'ctplkfor',
		'method' => 'POST" id="createdocumentform',
		'setformname' => 'createdocumentform',
		'flds' => array(
			'document_id' => array(
				'CType' => 'hidden',
				'VType' => 'int',
			),
			'papertype_id' => array(
				'CType' => 'radio',
				'VType' => 'int',
				'DisplayName' => getstr('pwt.create_document_papertype'),
				'SrcValues' => "
				SELECT id, name, journals as journals, description as desc
					FROM pwt.papertypes
					-- WHERE is_active = true
					ORDER BY ord ASC
				",
				'AllowNulls' => false,
				'AddTags' => array(
					'class' => 'paper_type',
					//~ 'onfocus' => 'changeFocus(1, this)',
					//~ 'onblur'  => 'changeFocus(2, this)',
					'onclick' => 'enableOrDisableJournalsByPaperType(this)',
					'fldattr'  => '0',
				),
			),
			'journal_id' => array(
				'CType' => 'radio',
				'VType' => 'int',
				'DisplayName' => getstr('pwt.create_document_journal'),
				'SrcValues' => "SELECT id, name
					FROM journals WHERE pwt_state = 1
					ORDER BY id
				",
				'AllowNulls' => false,
				'AddTags' => array(
					'class' => 'journal',
					'onfocus' => 'changeFocus(1, this)',
					'onblur'  => 'changeFocus(2, this)',
					//~ 'onmouseover' => 'showActiveDataPapersForCurrentJournal(this)',
					'fldattr'  => '0',
					'disabled' => 'disabled',
				),
			),

			'edit' => array(
				'CType' => 'action',
				'DisplayName' => '',
				'SQL' => 'SELECT * FROM pwt.spGetDocumentMetaData({document_id}, ' . (int)$user->id . ')',
				'ActionMask' => ACTION_CHECK | ACTION_CCHECK | ACTION_SHOW | ACTION_FETCH | ACTION_EXEC,
			),
			'save' => array(
				'CType' => 'action',
				'DisplayName' => getstr('pwt.create.manuscript.btn'),
				'SQL' => 'SELECT * FROM pwt.spCreateDocument({papertype_id}, {journal_id}, ' . (int)$user->id . ') as document_id',
				'ActionMask' => ACTION_CHECK | ACTION_CCHECK | ACTION_SHOW | ACTION_FETCH | ACTION_EXEC,
			),
		),
		'templs' => array(
			G_DEFAULT => 'document.create_form',
		),
	)
);

$gForm->GetData();

if ($gForm->KforAction() == 'save' && $gForm->KforErrCnt() == 0) {
	if ($gForm->GetKforVal('document_id') <= 0)
		$gForm->KforSetErr('save', getstr('pwt.create_document_err'));
	else {
		header('Location:display_document.php?document_id=' . $gForm->GetKforVal('document_id'));
		exit();
	}
}

if ($gForm->KforAction() == 'edit') {//ne se pozvoliava smiana na nastroikite na dokumenta, sled kato e suzdaden
	$gForm->setProp('papertype_id', 'AddTags', array('disabled' => 'disabled', 'class' => 'P-Input-Middle-Disabled'));
	$gForm->setProp('journal_id', 'AddTags', array('disabled' => 'disabled', 'class' => 'P-Input-Middle-Disabled'));
	//~ $gForm->setProp('save', 'CType', 'hidden');
	$gForm->setProp('save', 'AddTags', array('disabled' => 'disabled'));
}

$gDocumentId = $gForm->GetKforVal('document_id');
$gPath = '';
$gTempl = 'global.createdocument_page';
if($gDocumentId && $gForm->KforAction() == 'edit'){
		$gInstanceId = getDocumentMetadataInstanceId($gDocumentId);
		$gDocument = new cdisplay_document(
			array(
				'ctype' => 'cdisplay_document',
				'instance_id' => $gInstanceId,
				'get_data_from_request' => false,
				'lock_operation_code' => LOCK_AUTO_LOCK,
				'templs' => getDocumentDefaultTempls(),
				'field_templs' => getDocumentFieldDefaultTempls(),
				'container_templs' => getDocumentContainerDefaultTempls(),
				'instance_templs' => getDocumentInstanceDefaultTempls(),
				'custom_html_templs' => getDocumentCustomHtmlItemsDefaultTempls(),
				'action_templs' => getDocumentActionsDefaultTempls(),
				'tree_templs' => getDocumentTreeDefaultTempls(),
				'path_templs' => getDocumentPathDefaultTempls(),
				'comments_templ' => 'comments',
				'comments_form_templ' => 'commentform'
			)
		);
		$gPath = $gDocument->getDocumentPath();
		$gTree = $gDocument->getDocumentTree();
		$gTempl = 'global.editdocument_page';
}

$lPageArray = array(
	'path' => $gPath,
	'tree' => $gTree,
	'content' => $gForm->Display(),
	'document_id' => $gDocumentId,
);

$inst = new cpage(array_merge($lPageArray, DefObjTempl()), array(G_MAINBODY => $gTempl));
$inst->Display();
?>