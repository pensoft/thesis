var gTrackers = {
	'keys' : [],
	'editors' : [],
};

var gInstanceFieldTrackerNodes = {

};
var gPreviewHolderId = 'previewHolder';
var gVersionsAjaxSrv = '/lib/ajax_srv/version_srv.php';
var gVersionId = 0;
var gDocumentId = 0;
var gAllTrackersInited = 0;
var gChangeContextMenuHideEventIsBinded = 0;
var gContextMenuHolderId = 'changeContextMenu';
var gApproveChangeContextMenuLinkId = 'approveChangeContextLink';
var gRejectChangeContextMenuLinkId = 'rejectChangeContextLink';
var gUserName = '';
var gUserId = 0;
var gTrackChanges = 1;
var gAuthorRoleViewMode = 1;
var gSERoleViewMode = 2;
var gVersionRoleMode = gAuthorRoleViewMode;
var gAcceptedInsertChangeNodeName = 'accepted-insert';
var gAcceptedDeleteChangeNodeName = 'accepted-delete';
var gFakeInsertChangeNodeName = 'fake-insert';
var gFakeDeleteChangeNodeName = 'fake-delete';
var gDeleteChangeNodeName = 'delete';
var gInsertChangeNodeName = 'insert';
var gChangeUserIdsAttrName = 'data-userid';
var gChangeUserNamesAttrName = 'data-username';
var gChangeUserIdsSeparator = ', ';

var gFieldContentEditableSelector = ' *[contenteditable="true"][field_id]';

var gVersionUserDisplayNames = {};
var gAutosaveTimeoutDuration = 30000;//In milliseconds

var gPageIsUnloading = false;

window.addEventListener("beforeunload", function (e) {
  gPageIsUnloading = true;
  return null;                                //Webkit, Safari, Chrome etc.
});

function DisableChangeTracking(){
	gTrackChanges = 0;
}

function EnableFigureTracking(){
	gTrackFigures = 0;
}


function SetVersionSERoleMode(){
	gVersionRoleMode = gSERoleViewMode;
}

function SetVersionUser(pUid, pUserName){
	gUserId = pUid;
	gUserName = pUserName;
}

function ExecuteSimpleVersionAjaxRequest(pDataToPass, pAsync, pCallbackOnError) {
	$.ajax({
		url : gVersionsAjaxSrv,
		async : pAsync ? pAsync : false,
		dataType : 'json',
		type : 'POST',
		data : pDataToPass,
		success : function(pAjaxResult) {
			if(!pAjaxResult['action_is_successful']){
				if(pCallbackOnError){
					pCallbackOnError(pAjaxResult)
				}else{
					alert('Error occurred');
				}
			}else{
			}
		},
		error : function(){
			if(pCallbackOnError){
				pCallbackOnError()
			}else{
				alert('Error occurred');
			}
		}
	});
}
function InitTrackers(pVersionId, pDocumentId) {
	gVersionId = pVersionId;
	gDocumentId = pDocumentId;	

	$('#' + gPreviewHolderId + gFieldContentEditableSelector).bind('blur', function(pEvent) {
		SaveNodeTrackerContents(this);
	});
	InitForcefullyAllTrackers();
}

function InitForcefullyAllTrackers() {
	if(gAllTrackersInited){
		return;
	}
	$('#' + gPreviewHolderId + gFieldContentEditableSelector).each(function(pIdx, pNode) {
		InitSingleNodeTracker(pNode);
	});
	gAllTrackersInited = 1;
}


function AcceptAllChanges() {
	InitForcefullyAllTrackers();
	for( var i = 0; i < gTrackers['editors'].length; ++i){
		SingleTrackerAcceptAllChanges(gTrackers['editors'][i]);
	}
	ExecuteSimpleVersionAjaxRequest({
		action : 'accept_all_changes',
		version_id : gVersionId,
		document_id : gDocumentId
	});
}

function RejectAllChanges() {
	InitForcefullyAllTrackers();
	for( var i = 0; i < gTrackers['editors'].length; ++i){
		SingleTrackerRejectAllChanges(gTrackers['editors'][i]);
	}
	ExecuteSimpleVersionAjaxRequest({
		action : 'reject_all_changes',
		version_id : gVersionId,
		document_id : gDocumentId
	});
}

function GetInstanceFieldTrackerNode(pInstanceId, pFieldId){
	if(gInstanceFieldTrackerNodes[pInstanceId]){
		return gInstanceFieldTrackerNodes[pInstanceId][pFieldId];
	}
}


function InitSingleNodeTracker(pNode) {
	var lFieldId = $(pNode).attr('field_id');
	var lInstanceId = $(pNode).closest('*[instance_id]').attr('instance_id');
	if(!gInstanceFieldTrackerNodes[lInstanceId]){
		gInstanceFieldTrackerNodes[lInstanceId] = {};
	}
	if(!gInstanceFieldTrackerNodes[lInstanceId][lFieldId]){
		gInstanceFieldTrackerNodes[lInstanceId][lFieldId] = pNode;
	}	

	if(gTrackers['keys'].indexOf(pNode) == -1){
		gTrackers['keys'].push(pNode);
		var lNewTracker = new ice.InlineChangeEditor({
			element : pNode,
			handleEvents : true,
			currentUser : {
				id : gUserId,
				name : gUserName
			},
			plugins : ['IceAddTitlePlugin', 'IceSmartQuotesPlugin', 'IceEmdashPlugin', {
				name : 'IceCopyPastePlugin',
				settings : {
					pasteType : 'formattedClean',
					preserve : 'p,a[href],i,em,b,span'
				}
			}],
			mode : gVersionRoleMode,
			fake_tracking : !gTrackChanges ,
		});	
		gTrackers['editors'].push(lNewTracker);
		try{			
			lNewTracker.startTracking();			
			if(!gTrackChanges){
				lNewTracker.disableChangeTracking();
			}
			var lEmptyChangeNodes = $(pNode).find(gAcceptedInsertChangeNodeName + ':empty,' + gAcceptedDeleteChangeNodeName + ':empty,' + gFakeInsertChangeNodeName + ':empty,' + gFakeDeleteChangeNodeName + ':empty,' + gDeleteChangeNodeName + ':empty,' + gInsertChangeNodeName + ':empty');
			lEmptyChangeNodes = lEmptyChangeNodes.filter(function(pIdx){
				var lClosestContentEditableParent = $(this).closest('*[contenteditable="true"][field_id]');
				if(!lClosestContentEditableParent.length){
					return false;
				}
				if(lClosestContentEditableParent[0] !== pNode){
					return false;
				}
				return true;
			});
			if(lEmptyChangeNodes.length){
				lEmptyChangeNodes.remove();
				SaveNodeTrackerContents(pNode);
			}
			
			InitTrackerChangesEvents(pNode, lNewTracker);
		}catch(e){

		}
	}else{
//		console.log('Already inited');
	}
}

function SaveNodeTrackerContents(pNode) {
	var lIdx = gTrackers['keys'].indexOf(pNode);
	if(lIdx == -1){
		return;
	}
	
	var lFieldId = $(pNode).attr('field_id');
	var lInstanceId = $(pNode).closest('*[instance_id]').attr('instance_id');
	
//	console.log('Save real ' + lInstanceId + ' ' + lFieldId);
	var lContent = gTrackers['editors'][lIdx].getElementContents();
//	alert(lContent);
//	return;

	ExecuteSimpleVersionAjaxRequest({
		action : 'save_version_change',
		version_id : gVersionId,
		field_id : lFieldId,
		instance_id : lInstanceId,
		content : lContent,
		document_id : gDocumentId
	}, false, function(){NotifyUserForFailedSave();});

//	console.log(lFieldId, lInstanceId, lContent);
}

function NotifyUserForFailedSave(pIsGeneralSave){
	if(gPageIsUnloading){//Request aborted by user
		return;
	}
	if(pIsGeneralSave){
		alert('Save failed!');
	}else{
		alert('Autosave failed!');
	}
	
}

function SingleTrackerAcceptAllChanges(pTracker) {
	pTracker.acceptAll();
}

function SingleTrackerRejectAllChanges(pTracker) {
	pTracker.rejectAll();
}

function toggleChangesDisplay(){
	var lVal = 0;
	if($('input:radio[name=changes_display]').length){
		lVal = $('input:radio[name=changes_display]:checked').val();
	}else{
		lVal = window.parent.$('input:radio[name=changes_display]:checked').val();
	}
	if(lVal > 0){
		$('#' + gPreviewHolderId).removeClass('hideChanges');
	}else{
		$('#' + gPreviewHolderId).addClass('hideChanges');
	}
}

function InitTrackerChangesEvents(pTrackerNode, pTracker){	
	var lTrackerNodeInstanceId = $(pTrackerNode).closest('*[instance_id]').attr('instance_id');
	var lTrackerNodeFieldId = $(pTrackerNode).attr('field_id');
	$(pTrackerNode).find('insert, delete').each(function(pIdx, pNode){
		var lClosestParentInstanceId = $(pNode).closest('*[instance_id]').attr('instance_id');
		var lClosestParentFieldId = $(pNode).closest('*[field_id]').attr('field_id');
		
		if(lTrackerNodeInstanceId == lClosestParentInstanceId && lTrackerNodeFieldId == lClosestParentFieldId){			
//			console.log(lTrackerNodeFieldId, lClosestParentFieldId, lTrackerNodeInstanceId, lClosestParentInstanceId);
			$(pNode).bind('contextmenu',
				function(pEvent){
//					console.log(pTrackerNode);
					var lChangeNode = this;
					var lContextMenu = $('#' + gContextMenuHolderId);
					lContextMenu.offset($(lChangeNode).offset());
	//				console.log($(lChangeNode).offset().top)
	//				console.log(lContextMenu.offset().top)
					lContextMenu.show();
					lContextMenu.offset($(lChangeNode).offset());
					pEvent.preventDefault();
	
					var lApproveLink = $('#' + gApproveChangeContextMenuLinkId);
					var lRejectLink = $('#' + gRejectChangeContextMenuLinkId);
					lApproveLink.unbind('click');
					lRejectLink.unbind('click');
					lApproveLink.click(function(){
						pTracker.acceptChange(lChangeNode);
						SaveNodeTrackerContents(pTrackerNode);
					});
					lRejectLink.click(function(){
						pTracker.rejectChange(lChangeNode);
						SaveNodeTrackerContents(pTrackerNode);
					});
				}
			);
		}
	});	
	if(!gChangeContextMenuHideEventIsBinded){
		gChangeContextMenuHideEventIsBinded = true;
		$('body').bind('click', function(){
			var lContextMenu = $('#' + gContextMenuHolderId);
			lContextMenu.hide();
		});
	}
}

function GetUserChangesInputs(){
	var lInputs = $('input[name="display_user_change"]');
	if(lInputs){
		lInputs = window.parent.$('input[name="display_user_change"]');
	}	
	return lInputs;
}
function SetDisplayUserChangeEvent(){
	var lInputs = GetUserChangesInputs();
	lInputs.bind('change', function(){		
//		console.log(1);
		for(var i = 0; i < lInputs.length; ++i){
			if($(lInputs[i]).is(':checked')){
				$('#' + gPreviewHolderId).removeClass('hideChange' + $(lInputs[i]).val());
			}else{
				$('#' + gPreviewHolderId).addClass('hideChange' + $(lInputs[i]).val());
			}
		}
	});
}

function ShowAllReviews() {
	var lInputs = GetUserChangesInputs();
	lInputs.each(function(){
		$(this).attr('checked', true);
		$(this).trigger('change');
		if(window.parent){
			window.parent.$(this).trigger('change');
		}
	});
}

function GetVersionUserDisplayNames(){
	$.ajax({
		url : gVersionsAjaxSrv,
		async : false,
		dataType : 'json',
		type : 'POST',
		data : {
			action : 'get_version_user_display_names',
			version_id : gVersionId,
			document_id : gDocumentId

		},
		success : function(pAjaxResult) {
			if(pAjaxResult['err_cnt']){
			}else{
				gVersionUserDisplayNames = pAjaxResult['result'];
				GenerateChangesTitle();
			}
		}
	});
}

function ClearChangesTitle(){
	$(gAcceptedInsertChangeNodeName + ',' + gAcceptedDeleteChangeNodeName + ',' + gDeleteChangeNodeName + ',' + gInsertChangeNodeName).attr('title', '');
	$(gAcceptedInsertChangeNodeName + ',' + gAcceptedDeleteChangeNodeName + ',' + gDeleteChangeNodeName + ',' + gInsertChangeNodeName).attr(gChangeUserNamesAttrName, '');
}

function GenerateChangesTitle(){
	$(gAcceptedInsertChangeNodeName + ',' + gAcceptedDeleteChangeNodeName + ',' + gDeleteChangeNodeName + ',' + gInsertChangeNodeName).each(
			function(pIndex, pChangeNode){
				var lChangeUserIds = $(pChangeNode).attr(gChangeUserIdsAttrName);
				if(lChangeUserIds == ''){
					return;
				}
				var lChangeUserIdArr = lChangeUserIds.split(gChangeUserIdsSeparator);
				var lChangeUserNames = '';
				var lAuthorCnt = 0;
				for(var i = 0; i < lChangeUserIdArr.length; ++i){
					var lUserId = parseInt(lChangeUserIdArr[i]);
					var lAuthorName = gVersionUserDisplayNames[lUserId];
					if(lAuthorName != ''){
						if(lAuthorCnt > 0){
							lChangeUserNames += gChangeUserIdsSeparator;
						}
						lChangeUserNames += lAuthorName;
						lAuthorCnt++;
					}
					$(pChangeNode).attr(gChangeUserNamesAttrName, lChangeUserNames);
				}
				var lChangeIsInsert = false;
				if(pChangeNode.nodeName.toLowerCase() == gAcceptedInsertChangeNodeName || pChangeNode.nodeName.toLowerCase() == gInsertChangeNodeName){
					lChangeIsInsert = true;
				}
				var lTitle = lChangeUserNames;
				if(lChangeIsInsert){
					lTitle += ' inserted this text.';
				}else{
					lTitle += ' deleted this text.';
				}
				$(pChangeNode).attr('title', lTitle);
			}
	);
}

function AcceptRejectChange(pChangeNode, pAccept){
	var lClosestParentInstanceId = $(pChangeNode).closest('*[instance_id]').attr('instance_id');
	var lClosestParentFieldId = $(pChangeNode).closest('*[field_id]').attr('field_id');
	var lTrackerNode = GetInstanceFieldTrackerNode(lClosestParentInstanceId,lClosestParentFieldId );
	if(!lTrackerNode){
		return;
	}
	var lIdx = gTrackers['keys'].indexOf(lTrackerNode);
	if(lIdx == -1){
		return;
	}
	var lTracker = gTrackers['editors'][lIdx];
	if(pAccept){
		lTracker.acceptChange(pChangeNode);
	}else{
		lTracker.rejectChange(pChangeNode);
	}
	SaveNodeTrackerContents(lTrackerNode);
}

function SetAutosaveTimeout(){
	setTimeout("PerformAutoSave();SetAutosaveTimeout()", gAutosaveTimeoutDuration);
}

function PerformAutoSave(){
//	console.log('Autosave');
	var lActiveElement = GetActiveElement();
	if(!lActiveElement){
		return;
	}
	var lClosestContentEditable = $(lActiveElement).closest(gFieldContentEditableSelector);
	if(!lClosestContentEditable.length){
		return;
	}
	SaveNodeTrackerContents(lClosestContentEditable[0]);
}

function GetActiveElement(){
	return (document.activeElement);	
}