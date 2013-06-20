var gPopupAjaxSrv = gAjaxUrlsPrefix + 'popup_srv.php';
var gPopupFormName = "newPopupForm";
var gPopupId = 'newElementPopup';
var gReferenceObjectId = 95;

function CreatePopup(pParentInstanceId, pObjectId){
	$.ajax({
		url : gPopupAjaxSrv,
		dataType : 'json',
		async : false,
		data :{
			action : 'create_new_popup',
			parent_instance_id : pParentInstanceId,
			object_id : pObjectId
		},
		success : function(pAjaxResult){
			if(pAjaxResult['err_cnt']){
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
			gActiveInstanceFormName = gPopupFormName;
		}
	});
}

function CreateNewReferencePopup(pPopupInEditor){
	var gReferenceParentInstanceId = getReferenceParentInstanceId();
	if(!gReferenceParentInstanceId){
		alert('Could not locate references parent!');
		return;
	}
	CreatePopup(gReferenceParentInstanceId, gReferenceObjectId);
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


function SaveNewElementPopup(pInstanceId, pParentInstanceId, pContainerId, pDisplayInTree ){
	var lCallback = function(){
		if(pDisplayInTree > 0){
			window.location.href = '/display_document.php?instance_id=' + pInstanceId;
			return;
		}
		executeAction(gActionReloadContainer, pParentInstanceId, pContainerId, GetRootInstanceId());
		HideNewPopup();
	};
	SaveInstance(pInstanceId, gInstanceViewMode, lCallback);

}

function SaveReferencePopup(){
	var lReferenceInstanceId = getFormFieldValueByName(gPopupFormName, 'instance_id');
	var lShowEditorDialog = 0;
	if($('#' + gPopupId).attr('in_editor') > 0 ){
		lShowEditorDialog = 1;
	}

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
	SaveInstance(lReferenceInstanceId, gInstanceViewMode, lCallback);
}


function CancelNewElementPopup(pInstanceId){
	if(pInstanceId > 0){
		executeAction(gActionRemoveInstanceWithoutContainerReload, pInstanceId, GetRootInstanceId());
	}
	HideNewPopup();
}

function CancelNewReferencePopup(){
	var lReferenceInstanceId = getFormFieldValueByName(gPopupFormName, 'instance_id');
	var lShowEditorDialog = 0;
	if($('#' + gPopupId).attr('in_editor') > 0 ){
		lShowEditorDialog = 1;
	}
	CancelNewElementPopup(lReferenceInstanceId);
	if(lShowEditorDialog){
		gCurrentDialog.show();
	}
}

