var gPopupAjaxSrv = gAjaxUrlsPrefix + 'popup_srv.php';
var gPopupFormName = "newPopupForm";
var gPopupId = 'newElementPopup';
var gReferenceObjectId = 95;
var gSupFileObjectId = 55;

function CreatePopup(pParentInstanceId, pObjectId){
	showLoading();
	gActiveInstanceFormName = gPopupFormName;
	$.ajax({
		url : gPopupAjaxSrv,
		dataType : 'json',
		async : false,
		data :{
			action : 'create_new_popup',
			parent_instance_id : pParentInstanceId,
			object_id : pObjectId
		},
		error: function(){
			gActiveInstanceFormName = gDocumentFormName;
		},
		success : function(pAjaxResult){
			if(pAjaxResult['err_cnt']){
				gActiveInstanceFormName = gDocumentFormName;
				alert(pAjaxResult['err_msg']);
				return;
			}
			var lPopup = $('#' + gPopupId);
			if(lPopup.length){
				lPopup.replaceWith(pAjaxResult['html']);
			}else{
				$('.P-Wrapper-Container').append(pAjaxResult['html']);
			}
			popUp(POPUP_OPERS.open, gPopupId, gPopupId);
			hideLoading();
		}
	});
}

function CreateEditPopup(pInstanceId){
	gActiveInstanceFormName = gPopupFormName;
	showLoading();
	$.ajax({
		url : gPopupAjaxSrv,
		dataType : 'json',
		async : false,
		data :{
			action : 'open_edit_popup',
			instance_id : pInstanceId
		},
		error: function(){
			gActiveInstanceFormName = gDocumentFormName;
		},
		success : function(pAjaxResult){
			if(pAjaxResult['err_cnt']){
				gActiveInstanceFormName = gDocumentFormName;
				alert(pAjaxResult['err_msg']);
				return;
			}
			var lPopup = $('#' + gPopupId);
			var lInstanceLevel = getInstanceLevel(pInstanceId);
			$('#instance_wrapper_' + pInstanceId).replaceWith('<div id="instance_fake_wrapper_' + pInstanceId + '" style="display:none" level="' + lInstanceLevel + '" />');
			if(lPopup.length){
				lPopup.replaceWith(pAjaxResult['html']);
			}else{
				$('.P-Wrapper-Container').append(pAjaxResult['html']);
			}
			popUp(POPUP_OPERS.open, gPopupId, gPopupId);
			hideLoading();
		}
	});
}

function createPopUpForEditReference(pParentInstanceId, pObjectId, pInstanceId){
	gActiveInstanceFormName = gPopupFormName;
	$.ajax({
		url : gPopupAjaxSrv,
		dataType : 'json',
		async : false,
		data :{
			action : 'open_edit_popup',
			parent_instance_id : pParentInstanceId,
			object_id : pObjectId,
			instance_id : pInstanceId
		},
		error: function(){
			gActiveInstanceFormName = gDocumentFormName;
		},
		success : function(pAjaxResult){
			if(pAjaxResult['err_cnt']){
				gActiveInstanceFormName = gDocumentFormName;
				alert(pAjaxResult['err_msg']);
				return;
			}
			var lPopup = $('#' + gPopupId);
			if(lPopup.length){
				lPopup.replaceWith(pAjaxResult['html']);
			}else{
				$('.P-Wrapper-Container').append(pAjaxResult['html']);
			}
			popUp(POPUP_OPERS.open, gPopupId, gPopupId);
		}
	});
}

function CreateNewReferencePopup(pPopupInEditor){
	var gReferenceParentInstanceId = getReferenceParentInstanceId();
	if(!gReferenceParentInstanceId){
		alert('Could not locate references parent!');
		return;
	}
	CreateNewElementPopupInEditor(gReferenceParentInstanceId, gReferenceObjectId, pPopupInEditor);
}

function CreateNewSupFilePopup(pPopupInEditor){
	var gSupFilesParentInstanceId = getSupFilesParentInstanceId();
	if(!gSupFilesParentInstanceId){
		alert('Could not locate sup files parent!');
		return;
	}
	CreateNewElementPopupInEditor(gSupFilesParentInstanceId, gSupFileObjectId, pPopupInEditor);
}

function CreateNewElementPopupInEditor(pParentInstanceId, pElementObjectId, pPopupInEditor){
	CreatePopup(pParentInstanceId, pElementObjectId);
	if(pPopupInEditor){
		$('#' + gPopupId).attr('in_editor', 1);
	}else{
		$('#' + gPopupId).attr('in_editor', 0);
	}
}

function HideNewPopup(){
	gActiveInstanceFormName = gDocumentFormName;
	popUp(POPUP_OPERS.close, gPopupId, gPopupId);
}

function HideEditPopup(){
	var lInstanceId = getFormFieldValueByName(gPopupFormName, 'instance_id');
	var lInstanceLevel = $('#instance_fake_wrapper_' + lInstanceId).attr('level');
	$('#instance_fake_wrapper_' + lInstanceId).replaceWith('<div id="instance_wrapper_' + lInstanceId + '" style="display:none" level="' + lInstanceLevel + '" />');
	ChangeInstanceMode(GetDocumentId(), lInstanceId, null, null, gInstanceViewMode);
	HideNewPopup();
}


function SaveNewElementPopup(pInstanceId, pParentInstanceId, pContainerId, pDisplayInTree){
	showLoading();
	var lShowEditorDialog = 0;
	if($('#' + gPopupId).attr('in_editor') > 0 ){
		lShowEditorDialog = 1;
	}
	var lCallback = function(){
		if(!lShowEditorDialog && !gPreviewMode){
			if(pDisplayInTree > 0){
				window.location.href = '/display_document.php?instance_id=' + pInstanceId;
				return;
			}
			executeAction(gActionReloadContainer, pParentInstanceId, pContainerId, GetRootInstanceId());
		}
		HideNewPopup();
		/* Tova e za show/hide na download as csv linka v maerialite */
		if($('.P-Taxon-Materials-DownLoadHolder')) {
			ShowDownloadMaterialsLink(pParentInstanceId);
		}
		if(lShowEditorDialog){
			gCurrentDialog.show();
		}
		if(gPreviewMode){
			HandlePreviewModeCreateInstance();
		}
	};
	gStopAutoSaveInstance = 1;
	SaveInstance(pInstanceId, gInstanceViewMode, lCallback, 1);
	hideLoading();
}

function SaveReferencePopup(){
	var lReferenceInstanceId = getFormFieldValueByName(gPopupFormName, 'instance_id');
	var lShowEditorDialog = 0;
	if($('#' + gPopupId).attr('in_editor') > 0 ){
		lShowEditorDialog = 1;
	}
	showLoading();
	var lCallback = function(){
		HideNewPopup();
		if(lShowEditorDialog > 0){
			gCurrentDialog.show();
		}else{
			var lRootInstanceId = GetRootInstanceId();
			var lReferencesParent = getReferenceParentInstanceId();
			//Ако сме на страницата с референциите - правим аутосейв и презареждаме обекта
			if(lRootInstanceId == lReferencesParent){
				autoSaveInstance();
				ChangeInstanceMode(GetDocumentId(), lRootInstanceId, null, null, gInstanceEditMode);
			}
		}
	};
	SaveInstance(lReferenceInstanceId, gInstanceViewMode, lCallback, 1);
	hideLoading();
}

function SaveEditPopup(){
	var lInstanceId = getFormFieldValueByName(gPopupFormName, 'instance_id');
	showLoading();
	var lCallback = function(pAjaxResult){
		var lRootInstanceId = GetRootInstanceId();
		var $lInstanceWrapper = $('#instance_fake_wrapper_' + lInstanceId);
		$lInstanceWrapper.replaceWith(pAjaxResult['instance_html']);
		HideNewPopup();
		executeAction(27, pAjaxResult['parent_instance_id'], pAjaxResult['container_id'], lRootInstanceId);
	};
	gStopAutoSaveInstance = 1;
	SaveInstance(lInstanceId, gInstanceViewMode, lCallback);
	hideLoading();
}


function CancelNewElementPopup(pInstanceId){
	var lShowEditorDialog = 0;
	if($('#' + gPopupId).attr('in_editor') > 0 ){
		lShowEditorDialog = 1;
	}
	if(pInstanceId > 0){
		executeAction(gActionRemoveInstanceWithoutContainerReload, pInstanceId, GetRootInstanceId());
	}
	HideNewPopup();
	if(lShowEditorDialog){
		gCurrentDialog.show();
	}
}

function CancelNewReferencePopup(){
	CancelNewElementPopupWithoutInstance();
}

function CancelNewElementPopupWithoutInstance(){
	var lItemInstanceId = getFormFieldValueByName(gPopupFormName, 'instance_id');
	CancelNewElementPopup(lItemInstanceId);
}
