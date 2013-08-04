var gWindowIsLoaded = false;


var gAjaxUrlsPrefix = '/lib/ajax_srv/';
var gDocumentAjaxSrv = gAjaxUrlsPrefix + 'document_ajax_srv.php';
var gAutocompleteAjaxSrv = gAjaxUrlsPrefix + 'autocomplete_srv.php';
var gActionAjaxSrv = gAjaxUrlsPrefix + 'action_srv.php';
var gLockDocumentAjaxSrv = gAjaxUrlsPrefix + 'lock_document.php';
var gCommentsAjaxSrv = gAjaxUrlsPrefix + 'comments_srv.php';
var gFiguresAjaxSrv = gAjaxUrlsPrefix + 'figures_srv.php';
var gListFiguresAjaxSrv = gAjaxUrlsPrefix + 'list_figures_srv.php';
var gListTablesAjaxSrv = gAjaxUrlsPrefix + 'list_tables_srv.php';
var gListReferencesAjaxSrv = gAjaxUrlsPrefix + 'list_references_srv.php';
var gListSupFilesAjaxSrv = gAjaxUrlsPrefix + 'list_sup_files_srv.php';
var gDocumentTreeAjaxSrv = gAjaxUrlsPrefix + 'document_tree_srv.php';

var gDocumentAuthorsSrv = gAjaxUrlsPrefix + 'get_document_authors.php';
var gSendEmailSrv = gAjaxUrlsPrefix + 'send_email_srv.php';
var gActivitySrv = gAjaxUrlsPrefix + 'list_activity_srv.php';
var gCitationsSrv = gAjaxUrlsPrefix + 'citations_srv.php';
var gFileUploadSrv = gAjaxUrlsPrefix + 'file_upload_srv.php';
var gFileUploadMaterialSrv = gAjaxUrlsPrefix + 'file_upload_material_srv.php';
var gFileUploadChecklistTaxonSrv = gAjaxUrlsPrefix + 'file_upload_checklist_taxon_srv.php';
var gFileUploadTaxonomicCoverageTaxaSrv = gAjaxUrlsPrefix + 'file_upload_taxonomic_coverage_taxa_srv.php';
var gInstancesSrv = gAjaxUrlsPrefix + 'instance_ajax_srv.php';
var gInstanceFieldAutosaveSrv = gAjaxUrlsPrefix + 'instance_field_save_srv.php';
var gActiveMenuTabSrv = gAjaxUrlsPrefix + 'active_menu_tabs_srv.php';
var gShowOrHideColumnSrv = gAjaxUrlsPrefix + 'show_hide_site_columns_srv.php';
var gDeleteDocumentSrv = gAjaxUrlsPrefix + 'delete_document_srv.php';
var gDeleteCommentSrv = gAjaxUrlsPrefix + 'delete_comment_srv.php';
var gSaveInstanceSrv = gAjaxUrlsPrefix + 'instance_save.php';

var gPreviewMode = false;

var gDocumentFormName = 'document_form';
var gActiveInstanceFormName = gDocumentFormName;
var gContainerItemObjectType = 2;
var gDocumentLockTimeoutInterval = 15;//in seconds

//The next variables are for the display of loading icon
var gPerformingSave = false;//Whether we are currently performing save
var gLoadingIsVisible = false;
var gLoadingDivId = 'P-Ajax-Loading-Image';


var gActionReloadContainerActionsId = 5;
var gActionReloadContainer = 74;
var gActionRemoveInstanceId = 3;
var gActionRemoveInstanceWithoutContainerReload = 36;

var gLastItemClass = 'lastItem';
var gInstanceFieldNameSeparator = '__';

var gInstanceEditMode = 1;
var gInstanceViewMode = 2;

var gUnlockOperationCode = 2;

var gFocus 		 = 'JS-Focus';
var gInputHolder = 'P-Input-Holder';

var gLeftContainerClass = "P-Wrapper-Container-Left";
var gLeftContainerClassHide = "P-Wrapper-Container-Left-Hide";
var gMiddleContainerClass = "P-Wrapper-Container-Middle";
var gRightContainerClass = "P-Wrapper-Container-Right";
var gRightContainerClassHide = "P-Wrapper-Container-Right-Hide";
var gDashboardDocumentsHolderClass = "P-Content-Dashboard-Holder";

var gArticleStructuresHolderClass = "P-Article-Structures";
var gActivityFeedHolderClass = "P-Activity-Fieed-Wrapper";
var gSelectedOptionClass = 'P-Select-Value';

var gFiguresHolderId = "document_figures_holder";
var gTablesHolderId = "document_tables_holder";
var gSupFilesHolderId = "document_sup_files_holder";

var gReferencesHolderId = "document_references_holder";
var gReferencesCitationPreviewId = "document_reference_citation_preview";

var gInputRightActionsHolder = 'P-Instance-Right-Actions';
var gInputWrapHolder = 'P-Input-With-Background';

var POPUP_OPERS = { 'close': 0, 'open': 1 };

var gActiveClass = 'P-Active';

var gUploadFileNameHolderClass = 'P-File-Name';

var gPreviewIframeId = 'previewIframe';


var gCurrentDialog = null;

var gCitationFlag = 0;

var gCKEditorConfigs = {};

var gAutoSaveFlag = 0;

function SaveCKEditorConfig(pTextareaId, pConfig){
	gCKEditorConfigs[pTextareaId] = pConfig;
}

function ReloadCKEditor(pTextareaId){
	CKEDITOR.replace(pTextareaId, gCKEditorConfigs[pTextareaId]);
}

function setCommentsPreviewMode(pMode){
	gCommentsInPreviewMode = pMode;
	if(pMode){
		gPreviewMode = true;
	}
}

function reloadCaptcha() {
	var img = document.getElementById('cappic');
	img.src = '/lib/frmcaptcha.php?rld=' + Math.random();
	return false;
}

function rldContent(t, txt) {
	if (t.value == txt) {
		t.value = '';
	}
}

function rldContent2(t, txt) {
	if (t.value == '') {
		t.value = txt;
	}
}

function CheckLoginForm(frm, uname, upass) {
	if (frm.uname.value == uname) {
		frm.uname.value = '';
	}

	if (frm.upass.value == upass) {
		frm.upass.value = '';
	}

	return true;

}

function MoveInstanceInTreeCallbackWithParentRefresh(pInstanceId, pAjaxResult){
	if(pAjaxResult['err_cnt']){
		alert(pAjaxResult['err_msg']);
		return;
	}
	var lParentInstanceId = pAjaxResult['parent_id'];
	ChangeInstanceMode(GetDocumentId(), lParentInstanceId, null, null, gInstanceEditMode);
}

function MoveInstanceInTreeCallback(pInstanceId, pAjaxResult){
	if(pAjaxResult['err_cnt']){
		alert(pAjaxResult['err_msg']);
		return;
	}
	var lSwapInstanceId = pAjaxResult['swap_id'];
	var lOriginalInstanceWrapper = $('#instance_wrapper_' + pInstanceId).closest('.container_item_wrapper');
	var lSwapInstanceWrapper = $('#instance_wrapper_' + lSwapInstanceId).closest('.container_item_wrapper');

	if(lOriginalInstanceWrapper.hasClass(gLastItemClass)){
		lSwapInstanceWrapper.addClass(gLastItemClass);
		lOriginalInstanceWrapper.removeClass(gLastItemClass);
	}else if(lSwapInstanceWrapper.hasClass(gLastItemClass)){
		lSwapInstanceWrapper.removeClass(gLastItemClass);
		lOriginalInstanceWrapper.addClass(gLastItemClass);
	}

	if(!lOriginalInstanceWrapper.length || !lSwapInstanceWrapper.length)
			return;

	var lOriginalIdxLabel = $('#instance_idx_label_' + pInstanceId);
	var lSwapIdxLabel = $('#instance_idx_label_' + lSwapInstanceId);

	if(lOriginalIdxLabel.length > 0 && lSwapIdxLabel.length > 0){
		var lOriginalIdx = lOriginalIdxLabel.html();
		var lSwapIdx = lSwapIdxLabel.html();

		lOriginalIdxLabel.html(lSwapIdx);
		lSwapIdxLabel.html(lOriginalIdx);

	}

	//Mouseout и mouseover event-ите ги слагаме за показваме/скриваме екшъните отдясно
	$('#instance_wrapper_' + pInstanceId).trigger('mouseout');
	
	var lOriginalEditors = DestroyElementEditors(lOriginalInstanceWrapper);
	var lSwapEditors = DestroyElementEditors(lSwapInstanceWrapper);
	
	var lOriginalClone = lOriginalInstanceWrapper.clone(1, 1);
	var lSwapClone = lSwapInstanceWrapper.clone(1, 1);
	
	lOriginalInstanceWrapper.replaceWith(lSwapClone);
	lSwapInstanceWrapper.replaceWith(lOriginalClone);
	
	for(var i in lOriginalEditors){
		var lTextareaId = i;
		$('#' + lTextareaId).val(lOriginalEditors[lTextareaId])
//		console.log(lTextareaId + ' ' + $('#' + lTextareaId).val());
		ReloadCKEditor(lTextareaId);
	}
	for(var i in lSwapEditors){
		var lTextareaId = i;
		$('#' + lTextareaId).val(lSwapEditors[lTextareaId])
//		console.log(lTextareaId + ' ' + $('#' + lTextareaId).val());
		ReloadCKEditor(lTextareaId);
	}

	

	$('#instance_wrapper_' + lSwapInstanceId).trigger('mouseover');

	handleMovementLinksDisplay(pInstanceId, pAjaxResult['original_available_move_up'], pAjaxResult['original_available_move_down']);
	handleMovementLinksDisplay(lSwapInstanceId, pAjaxResult['swap_available_move_up'], pAjaxResult['swap_available_move_down']);


}
/**
 * Destroys all the ckeditors in the specified element and 
 * removes all the script tags in it
 * @param pJQElement
 * @returns an object with all the textareas in the element along with their contents
 * so that the ckeditors can be easily recreated
 */
function DestroyElementEditors(pJQElement){
	lResult = {};
	pJQElement.find('textarea').each(function(pIdx, pElement){
		var lTextareaId = pElement.id;
		var lEditorInstance = CKEDITOR.instances[lTextareaId];
		if(lEditorInstance){
			lEditorInstance.updateElement();
//			console.log(lTextareaId, lEditorInstance.getData())
			lEditorInstance.destroy(true);
			lResult[lTextareaId] = $(pElement).val();
			$(pElement).hide();
//			console.log(lTextareaId + 'A ' + $('#' + lTextareaId).val());
		}
	});
	
	
	pJQElement.find('script').each(function(pIdx, pElement){
		$(pElement).remove();
	});
	return lResult;
}

function handleMovementLinksDisplay(pInstanceId, pAllowMoveUp, pAllowMoveDown){
	if(pAllowMoveUp){
		$('#move_up_link_instance_' + pInstanceId).show();
		$('#move_up_right_link_instance_' + pInstanceId).show();
	}else{
		$('#move_up_link_instance_' + pInstanceId).hide();
		$('#move_up_right_link_instance_' + pInstanceId).hide();
	}

	if(pAllowMoveDown){
		$('#move_down_link_instance_' + pInstanceId).show();
		$('#move_down_right_link_instance_' + pInstanceId).show();
	}else{
		$('#move_down_link_instance_' + pInstanceId).hide();
		$('#move_down_right_link_instance_' + pInstanceId).hide();
	}
}

function ChangeInstanceMode(pDocumentId, pInstanceId, pRootInstanceId, pLevel, pMode){
	if(!pRootInstanceId){
		pRootInstanceId = GetRootInstanceId();
	}

	if(!pLevel){
		pLevel = getInstanceLevel(pInstanceId);
	}
	if(!pDocumentId || !pInstanceId || !pRootInstanceId || !pLevel){
		return;
	}
	$.ajax({
		url : gDocumentAjaxSrv,
		dataType : 'json',
		async : false,
		data :{
			action : 'display_instance_contents',
			document_id : pDocumentId,
			instance_id : pInstanceId,
			root_instance_id : pRootInstanceId,
			level : pLevel,
			mode : pMode
		},
		success : function(pAjaxResult){
			if(pAjaxResult['err_cnt']){
				alert(pAjaxResult['err_msg']);
				return;
			}
			var lInstanceWrapper = $('#instance_wrapper_' + pInstanceId);
			lInstanceWrapper.before(pAjaxResult['html']);
			lInstanceWrapper.remove();

		}
	});
}

function openReferenceForEditInPopUp(pDocumentId, pInstanceId, pRootInstanceId, pLevel, pMode){
	if(!pRootInstanceId){
		pRootInstanceId = GetRootInstanceId();
	}
	var gReferenceParentInstanceId = getReferenceParentInstanceId();
	if(!pLevel){
		pLevel = getInstanceLevel(pInstanceId);
	}
	if(!pDocumentId || !pInstanceId || !pRootInstanceId || !pLevel){
		return;
	}
	createPopUpForEditReference(gReferenceParentInstanceId, gReferenceObjectId, pInstanceId);
}

function changeFocus( pOper, pEl ){
	switch( pOper ){
		case 1:
			$( pEl ).closest( '.' + gInputHolder ).addClass( gFocus );
			//$( pEl ).attr( 'fldattr', 1 );
			break;
		case 2:
			//$( pEl ).attr( 'fldattr', 0 );
			$( pEl ).closest( '.' + gInputHolder ).removeClass( gFocus );
			break;
		default:
			break;
	}
}

/*
 * За дизайн на селектите
 * pOper:
 *	0 - НЯМА Ajax
 *	1 - ИМА  Ajax ( ако при смяна на селекта той се замества с идентичен с AJAX трябва да се инитне наново )
 */

designSelect = function( pSelectId, pOper ){
	this.lSelect = $( '#' + pSelectId );
	this.lSelectId = pSelectId;
	this.init( pOper );
};

designSelect.prototype.init = function( pOper ){
	var lThis = this;

	$('#' + lThis.lSelectId).siblings( '.' + gSelectedOptionClass ).html( $('#' + lThis.lSelectId).find( "option:selected" ).text() );
	if( !pOper ){
		lThis.lSelect.bind( 'change', function(){lThis.init(1); } );
	}
};


/**
 * Изпълняваме даден екшън за даден инстанс.
 *
 * Ако се подадат повече от 2 параметъра - те автоматично ще се добавят към заявката за
 * изпълняване на реалното (php) действие и ще отговарят
 * на ключовете addparam_1, add_param2, ...
 * @param pActionId
 * @param pInstanceId
 */
function executeAction(pActionId, pInstanceId){
	var lParameters = new Array();
	var lCallback = '';
	var lEvalReturnType = 0;
	var lHasErrors = 0;
	var lActionIsRecursive = false;

	if(gLoadingIsVisible){
		lActionIsRecursive = true;
	}
	if(!lActionIsRecursive){
		showLoading();
	}
	$.ajax({
		url : gDocumentAjaxSrv,
		dataType : 'json',
		async : false,
		data :{
			action : 'get_action_details',
			instance_id : pInstanceId,
			action_id : pActionId
		},
		success : function(pAjaxResult){
			if(pAjaxResult['err_cnt']){
				alert(pAjaxResult['err_msg']);
				lHasErrors = 1;
				return;
			}
			lParameters = pAjaxResult['parameters'];
			lCallback = pAjaxResult['callback'];
			lEvalReturnType = pAjaxResult['eval_return_type'];
		}
	});
	if(lHasErrors > 0){
		return;
		if(!lActionIsRecursive){
			hideLoading();
		}
	}
	var lDataForAjax = {
		instance_id : pInstanceId,
		action_id : pActionId
	};

	lForm = $('form[name="' + gActiveInstanceFormName + '"]:visible');
	if(!lForm.length){
		lForm = $('form[name="' + gDocumentFormName + '"]');
	}

	for(var lParameterName in lParameters){
	    var lInputName = lParameters[lParameterName]['input_name'];
	    lDataForAjax[lParameterName] = getFormFieldValueByName(lForm.attr('name'), lInputName);
	}

	for(var i = 2; i < arguments.length; ++i){
		lDataForAjax['add_param' + (i - 1)] = arguments[i];
	}

	$.ajax({
		url : gActionAjaxSrv,
		dataType : lEvalReturnType,
		async : false,
		data :lDataForAjax,
		success : function(pAjaxResult){
			var lTempFunction = new Function("pAjaxResult", "lDataForAjax", "pInstanceId", "pActionId", lCallback);
			lTempFunction(pAjaxResult, lDataForAjax, pInstanceId, pActionId);
			if(!lActionIsRecursive){
				hideLoading();
			}
			if(lDataForAjax['add_param1'] && pActionId == 3 && $('.P-Taxon-Materials-DownLoadHolder')) {
				ShowDownloadMaterialsLink(lDataForAjax['add_param1']);
			}
			//eval(lCallback);
		}
	});

}

function getInstanceLevel(pInstanceId){
	var lInstance = $('#instance_wrapper_' + pInstanceId);
	return lInstance.attr('level');
}

function GetInstanceMode(pInstanceId){
	var lInstance = $('#instance_wrapper_' + pInstanceId);
	return lInstance.attr('mode');
}

function GetDocumentId(){
	return getFormFieldValueByName(gDocumentFormName, 'document_id');
}

function GetRootInstanceId(){
	return getFormFieldValueByName(gDocumentFormName, 'instance_id');
}

function getFormFieldValueByName(pFormName, pName){
	if($('form[name="' + pFormName + '"] input:radio[name="' + pName + '"]').length){//radio
    	return $('form[name="' + pFormName + '"] input:radio[name="' + pName + '"]:checked').val();
    }else if($('form[name="' + pFormName + '"] input:checkbox[name="' + pName + '[]"]').length){//checkbox
    	var lCheckboxes = $('form[name="' + pFormName + '"] input:checkbox[name="' + pName + '[]"]:checked');
    	var lResult = new Array();
    	for(var i = 0; i < lCheckboxes.length; ++i){
    		lResult.push($(lCheckboxes[i]).val());
    	}
    	return lResult;
    }else if($('form[name="' + pFormName + '"] input[name="' + pName + '[]"]').length){//checkbox
    	var lInputs = $('form[name="' + pFormName + '"] input[name="' + pName + '[]"]');
    	var lResult = new Array();
    	for(var i = 0; i < lInputs.length; ++i){
    		lResult.push($(lInputs[i]).val());
    	}
    	return lResult;
    }else if($('form[name="' + pFormName + '"] select[name="' + pName + '[]"]').length){//mselect
    	return $('form[name="' + pFormName + '"] select[name="' + pName + '[]"]').val();
    }else if($('form[name="' + pFormName + '"] select[name="' + pName + '"]').length){//select
    	return $('form[name="' + pFormName + '"] select[name="' + pName + '"]').val();
    }else if($('form[name="' + pFormName + '"] input[name="' + pName + '"]').length){
    	return $('form[name="' + pFormName + '"] input[name="' + pName + '"]').val();
    }else if($('form[name="' + pFormName + '"] textarea[name="' + pName + '"]').length){
    	return $('form[name="' + pFormName + '"] textarea[name="' + pName + '"]').val();
    }
}


function addFieldAction(pName, pEvent, pActionJs){
	var pFormName = gActiveInstanceFormName;
	var lSelector = false;
	if($('form[name="' + pFormName + '"] input:radio[name="' + pName + '"]').length){//radio
		lSelector = $('form[name="' + pFormName + '"] input:radio[name="' + pName + '"]');
    }else if($('form[name="' + pFormName + '"] input:checkbox[name="' + pName + '[]"]').length){//checkbox
    	lSelector = $('form[name="' + pFormName + '"] input:checkbox[name="' + pName + '[]"]');
    }else if($('form[name="' + pFormName + '"] input:hidden[name="' + pName + '[]"]').length){//checkbox
    	lSelector = $('form[name="' + pFormName + '"] input:hidden[name="' + pName + '[]"]');
    }else if($('form[name="' + pFormName + '"] select[name="' + pName + '[]"]').length){//mselect
    	lSelector = $('form[name="' + pFormName + '"] select[name="' + pName + '[]"]');
    }else if($('form[name="' + pFormName + '"] select[name="' + pName + '"]').length){//select
    	lSelector = $('form[name="' + pFormName + '"] select[name="' + pName + '"]');
    }else if($('form[name="' + pFormName + '"] input[name="' + pName + '"]').length){
    	lSelector = $('form[name="' + pFormName + '"] input[name="' + pName + '"]');
    }else if($('form[name="' + pFormName + '"] textarea[name="' + pName + '"]').length){
    	lSelector = $('form[name="' + pFormName + '"] input[name="' + pName + '"]');
    }
	if(!lSelector){
		return false;
	}
	lSelector.bind(pEvent, function(pEvent){
		var lTempFunction = new Function('pThis', 'pEvent', pActionJs);
		lTempFunction(this, pEvent);
	});
	if(pEvent == 'load'){
		$(window).ready(function () {
			lSelector.triggerHandler(pEvent);
		});

	}
}

/* Показване на формата за регистрация */
function LayerRegFrm(pElem, pOper, pReload) {
	if(pOper == 1 || !pOper) {
		$.ajax({
			url : '/register.php',
			async : false,
			//~ data :lDataForAjax,
			success : function(pAjaxResult){
				$('#' + pElem).html(pAjaxResult);

				if (!pReload) {
					$('#' + pElem).modal({
						autoResize: true,
						position: ["10%",],
						minHeight: 430,
						maxHeight: 600,
						overlayClose:true,
						onShow: function (dialog) {
							var doch = $(window).height();
							if(doch <= 430) {
								var calh = doch - 2*80;
								$('#simplemodal-container').height(calh);
							}
						}
					});
					if($('#regstep').val()) {
						var doch = $(window).height();
						var calh = doch - 2*80;
						$('#simplemodal-container').height(calh);
					}
				}
			}
		});
	}

}

/* Показване на формата за edit на профил за съответната стъпка */
function LayerProfEditFrm(pElem, pOper, pStep) {
	if(pOper == 2) {
		$.ajax({
			url : '/register.php',
			async : false,
			data : {
				step: pStep,
				showedit: 1,
				tAction: 'showedit'
			},
			success : function(pAjaxResult){
				$('#' + pElem).html(pAjaxResult);
					$('#' + pElem).modal({
						autoResize: true,
						position: ["10%",],
						minHeight: 430,
						maxHeight: 600,
						overlayClose:true,
						onShow: function (dialog) {
							var doch = $(window).height();
							if(doch <= 430) {
								var calh = doch - 2*80;
								$('#simplemodal-container').height(calh);
							}
						}
					});
					if(pStep != 1){
						var doch = $(window).height();
						var calh = doch - 2*80;
						$('#simplemodal-container').height(calh);
					}
			}
		});
	}
}

/* Събмитване на формата за регистрация */
function SubmitRegForm(pElem, pFormName, pOper, pStep, pCloseFlag) {
	var lFormData = $('form[name="' + pFormName + '"]').formSerialize();
	if (pStep) {
		lFormData += '&step=' + pStep;
	}
	if (pOper) {
		if (pOper == 1) {
			lFormData += '&tAction=register';
		}
		if (pOper == 2) {
			lFormData += '&tAction=showedit';
		}
		$.ajax({
			url: '/register.php',
			type : 'POST',
			data : lFormData,
			success: function(pAjaxResult){
				$('#' + pElem).html(pAjaxResult);
				if($('.errorHolder').length == 0 && pCloseFlag) {
				  $.modal.close();
				}
				if($('#regstep').val()) {
					var doch = $(window).height();
					var calh = doch - 2*80;
					$('#simplemodal-container').height(calh);
				} else {
					var calh = 430;
					// Calculate height
					$('#simplemodal-container').height(calh);
				}
				// Scroll to top
				$('.simplemodal-wrap').animate({ scrollTop: 0 }, 0);
			}
		});
	}
	return false;
}

gStopAutoSaveInstance = 0;
function SaveInstance(pInstanceId, pModeAfterSuccessfulSave, pCallbackOnSuccess, pInPopup){
	lForm = $('form[name="' + gActiveInstanceFormName + '"]:visible');
	if(!lForm.length){
		lForm = $('form[name="' + gDocumentFormName + '"]');
	}

	var lRootInstanceId = GetRootInstanceId();
	var lLevel = getInstanceLevel(pInstanceId);
	gPerformingSave = true;
	lForm.ajaxSubmit({
		'dataType' : 'json',
		'async' : false,
		'url' : gSaveInstanceSrv,
		'root_instance_id' : pInstanceId,
		'data' : {
			'real_instance_id' : pInstanceId,
			'root_instance_id' : lRootInstanceId,
			'level' : lLevel,
			'get_instance_html' : 1,
			'mode_after_successful_save' : pModeAfterSuccessfulSave,
			'in_popup': pInPopup
		},
		'success' : function(pAjaxResult){
			if(pAjaxResult['err_cnt']){
				alert(pAjaxResult['err_msg']);
				if(pAjaxResult['validation_err_cnt']){//Ако има грешка при валидацията - показваме наново обекта с маркираните грешки
					$('#instance_wrapper_' + pInstanceId).replaceWith(pAjaxResult['instance_html']);
				}
			}else{//Ако всичко е OK - трябва да сменим action-ите на контейнера и да сменим mode-a на обекта на view
				if(!pCallbackOnSuccess){
					//ChangeInstanceMode(lDocumentId, pInstanceId, lRootInstanceId, lLevel, pModeAfterSuccessfulSave);
					var $lInstanceWrapper = $('#instance_wrapper_' + pInstanceId);
					$lInstanceWrapper.before(pAjaxResult['instance_html']);
					$lInstanceWrapper.remove();
					executeAction(27, pAjaxResult['parent_instance_id'], pAjaxResult['container_id'], lRootInstanceId);
					gStopAutoSaveInstance = 1;
				}else{
					pCallbackOnSuccess(pAjaxResult);
				}
			}
			gPerformingSave = false;
		}
	});
}

/* Изтриване на plate/photo */
function DeleteFigure(pDocId, pPlateId, pPhotoId) {
	if (confirm("Are you sure you want to delete?")) {
		$.ajax({
			url : gFiguresAjaxSrv,
			dataType: 'json',
			data :{
				action : 'delete_plate_photo',
				plate_id : pPlateId,
				photo_id : pPhotoId,
				document_id : pDocId,
			},
			success : function(pAjaxResult){
				if(pAjaxResult['result'] == 1) {
					// hiding deleted item
					var figure = (pPhotoId) ? pPhotoId : pPlateId;

					//Махаме и предходната понеже и тя може да се е променила - ajax srv-то трябва да ни я върне и нея
					$('#P-Figures-Row-' + figure).hide('slow', function(){
						$('#P-Figures-Row-' + figure + ' ~ tr').remove(); // Махаме всички след изтритата
						$('#P-Figures-Row-' + figure).prev('tr').remove(); // Махаме всички след изтритата
						$(pAjaxResult['html']).insertAfter('#P-Figures-Row-' + figure);	// Добавяме ги отново с ъпдейтната позиция
						$('#P-Figures-Row-' + figure).remove(); // Махаме изтритата, която е скрита в момента
					});

					/*
					if($('#P-Document-Figures-Container > tbody:last').length) { // updating prev and next row before hiding
						var row = $('#P-Figures-Row-' + figure).closest("tr");
						var prevRowId = row.prev('.P-Data-Table-Holder').find("input[name*='plate_photo_id']").val();
						if(prevRowId)
							UpdateDocumentFiguresHolder(prevRowId, 'P-Figures-Row-' + prevRowId, 0, parseInt(pAjaxResult['curr_position']) - 1, pAjaxResult['max_position'], pAjaxResult['min_position']); // update moved row
						var nextRowId = row.next('.P-Data-Table-Holder').find("input[name*='plate_photo_id']").val();
						if(nextRowId)
							UpdateDocumentFiguresHolder(nextRowId, 'P-Figures-Row-' + nextRowId, 0, parseInt(pAjaxResult['curr_position']), pAjaxResult['max_position'], pAjaxResult['min_position']); // update moved row
					}
					$('#P-Figures-Row-' + figure).hide('slow', function(){
						$('#P-Figures-Row-' + figure).remove();
					});*/
				}
			}
		});
	}
}

/* Изтриване на table */
function DeleteTable(pTableId, pDocId) {
	if (confirm("Are you sure you want to delete?")) {
		$.ajax({
			url : gFiguresAjaxSrv,
			dataType: 'json',
			data :{
				action : 'delete_table',
				table_id : pTableId,
				document_id : pDocId,
			},
			success : function(pAjaxResult){
				if(pAjaxResult['result'] == 1) {
					$('#P-Table-Row-' + pTableId).hide('slow', function(){
						$('#P-Table-Row-' + pTableId + ' ~ div').remove(); // Махаме всички след изтритата
						$('#P-Table-Row-' + pTableId).prev('div').remove(); // Махаме предхподната
						$(pAjaxResult['updated_tables']).insertAfter('#P-Table-Row-' + pTableId);	// Добавяме ги отново с ъпдейтната позиция
						$('#P-Table-Row-' + pTableId).remove(); // Махаме изтритата, която е скрита в момента
					});
				}
			}
		});
	}
}

/* Местене на таблици */
function MoveTableUpDown(pThis, pDocId, pPosition, pDirection) {

	var curTableId = $(pThis).parents(".P-Data-Resources-Control:first").find("input[name*='table_id']").val();

	$.ajax({
		url : gFiguresAjaxSrv,
		dataType: 'json',
		data :{
			action : 'move_table',
			position : pPosition,
			direction : pDirection,
			document_id : pDocId,
			table_id : curTableId,
		},
		success : function(pAjaxResult){
			if(pAjaxResult) {
				if(pAjaxResult['html'] == 1) {
					var row = $(pThis).parents(".P-Data-Resources-Control:first");
					if ($(pThis).is(".section_arrow_up")) {
						var prevTableId = row.prev('.P-Data-Resources-Control').find("input[name*='table_id']").val();
						UpdateDocumentTableHolder(curTableId, 'P-Table-Row-' + curTableId, 1, pAjaxResult['new_position'], pAjaxResult['max_position'], pAjaxResult['min_position']); // update moved row
						UpdateDocumentTableHolder(prevTableId, 'P-Table-Row-' + prevTableId, 1, pAjaxResult['curr_position'], pAjaxResult['max_position'], pAjaxResult['min_position']); // update prev row

						row.fadeOut(500);
						row.prev().fadeOut(500);
						setTimeout(function(){
							row.prev().fadeIn(500);
							row.insertBefore(row.prev()).fadeIn(1000);
						}, 500);
					} else {
						var nextTableId  = row.next('.P-Data-Resources-Control').find("input[name*='table_id']").val();

						UpdateDocumentTableHolder(curTableId, 'P-Table-Row-' + curTableId, 1, pAjaxResult['new_position'], pAjaxResult['max_position'], pAjaxResult['min_position']); // update moved row
						UpdateDocumentTableHolder(nextTableId, 'P-Table-Row-' + nextTableId, 1, pAjaxResult['curr_position'], pAjaxResult['max_position'], pAjaxResult['min_position']); // update prev row

						row.fadeOut(500);
						row.next().fadeOut(500);
						setTimeout(function(){
							row.next().fadeIn(500);
							row.insertAfter(row.next()).fadeIn(1000);
						}, 500);
					}
				}
			}
		}
	});
}

/* Местене на фигурите */
function MoveFigureUpDown(pThis, pPhotoId, pDocId, pPosition, pDirection, pPlateFlag) {
	$.ajax({
		url : gFiguresAjaxSrv,
		dataType: 'json',
		data :{
			action : 'move_plate_photo',
			position : pPosition,
			direction : pDirection,
			photo_id : pPhotoId,
			document_id : pDocId,
			plate_flag : pPlateFlag
		},
		success : function(pAjaxResult){

			if(pAjaxResult['html'] == 1) {
				var row = $(pThis).parents("tr:first");
				if ($(pThis).is(".section_arrow_up")) {
					var prevRowId = row.prev('.P-Data-Table-Holder').find("input[name*='plate_photo_id']").val();
					UpdateDocumentFiguresHolder(pPhotoId, 'P-Figures-Row-' + pPhotoId, 0, pAjaxResult['new_position'], pAjaxResult['max_position'], pAjaxResult['min_position']); // update moved row
					UpdateDocumentFiguresHolder(prevRowId, 'P-Figures-Row-' + prevRowId, 0, pAjaxResult['curr_position'], pAjaxResult['max_position'], pAjaxResult['min_position']); // update prev row
					row.fadeOut(500);
					row.prev().fadeOut(500);
					setTimeout(function(){
						row.prev().fadeIn(500);
						row.insertBefore(row.prev()).fadeIn(1000);
					}, 500);
				} else {
					var nextRowId = row.next('.P-Data-Table-Holder').find("input[name*='plate_photo_id']").val();
					UpdateDocumentFiguresHolder(pPhotoId, 'P-Figures-Row-' + pPhotoId, 0, pAjaxResult['new_position'], pAjaxResult['max_position'], pAjaxResult['min_position']); // update moved row
					UpdateDocumentFiguresHolder(nextRowId, 'P-Figures-Row-' + nextRowId, 0, pAjaxResult['curr_position'], pAjaxResult['max_position'], pAjaxResult['min_position']); // update next row
					row.fadeOut(500);
					row.next().fadeOut(500);
					setTimeout(function(){
						row.next().fadeIn(500);
						row.insertAfter(row.next()).fadeIn(1000);
					}, 500);
				}
			}
		}
	});
}

var gCommentNow = 0;
/* Събмитване на коментар */
function SubmitCommentForm(pElem, pFormName, pOper, pId) {
	var lFormData = $('form[name="' + pFormName + '"]').formSerialize();
	if (pOper && !gCommentNow) {
		gCommentNow = 1;
		$.ajax({
			url: gCommentsAjaxSrv,
			type : 'POST',
			data : lFormData,
			success: function(pAjaxResult){
				$('#' + pElem).next('.P-Comments-Revisions-Item-Content').find('.P-Comments-Container').append(pAjaxResult);
				$('#P-Comment-Form_' + pId).hide();
				$('#P-Comment-Btn-' + pId).removeClass('comment_btn_inactive');
				$('#P-Comment-Btn-' + pId).addClass('comment_btn');
				$('form[name="' + pFormName + '"] textarea').val('');

				gCommentFormHide = 1;
				positionCommentsBase();
				gCommentNow = 0;
			}
		});
	}
	return false;
}

function ShowRegSuccess() {
	$.ajax({
		url: '/register.php',
		type : 'POST',
		data : {
			success: 1
		},
		success: function(pAjaxResult){
			$.modal.close();
				$('.loginFormRightCol').html(pAjaxResult);
		}
	});
	return false;
}

function deleteDocumentById( pDocumentId, pPage ) {
	if (confirm("Are you sure you want to delete this document?")) {
		$.ajax({
			url: gDeleteDocumentSrv,
			dataType : 'json',
			type : 'POST',
			async : false,
			data : {
				document_id: pDocumentId,
				p: pPage,
			},
			success: function(pAjaxResult){
				if(pAjaxResult["result"] == 1){
					//$('.' + gDashboardDocumentsHolderClass).html(pAjaxResult["html"]);
					var lUrl = document.URL;
					window.location = lUrl;
				}else{
				}
			}
		});
	}
}

/**
 * unlock на документ
 */
function unlock_document() {
	var lDocumentId = GetDocumentId();
	if(!lDocumentId){
		return;
	}
	$.ajax({
		url : gLockDocumentAjaxSrv,
		dataType : 'json',
		async : false,
		data : {
			document_id : lDocumentId,
			action : 'unlock_document'
		},
		success : function(){

		}

	});
}

function AutoSendDocumentLockSignal(){
	var lDocumentId = GetDocumentId();
	if(!lDocumentId){
		return;
	}
	setInterval(function(){
			$.ajax({
				url : gLockDocumentAjaxSrv,
				dataType : 'json',
				async : false,
				data : {
					document_id : lDocumentId,
					lock_operation_code : gUnlockOperationCode,
					action : 'autolock_document'
				},
				success : function(pAjaxResult){
					if(pAjaxResult['err_cnt'] > 0){
						alert(pAjaxResult['err_msg']);
						window.location.href = '/preview.php?document_id=' + lDocumentId;
					}
				}
			});
		}, gDocumentLockTimeoutInterval * 1000
	);
}

function resizeMiddleContainer(){
	var lArticleHeight = $('.' + gArticleStructuresHolderClass).outerHeight();
	var lActivityFeedHeight = $('.' + gActivityFeedHolderClass).outerHeight();

	if(lActivityFeedHeight){ //Това може да е сетнато само на dashboard страницата, a там нямаме articleMenu
		lArticleHeight = lActivityFeedHeight;
	}
	$('.' + gMiddleContainerClass).css('min-height', lArticleHeight + 80 + 'px');
}

var gLeftColHide = 0;
function toggleLeftContainer(){
	if( gLeftColHide ){ //show left column
		gLeftColHide = 0;
		$('.' + gLeftContainerClass).removeClass(gLeftContainerClassHide);
		$('.' + gMiddleContainerClass).removeClass(gLeftContainerClassHide);
		$('.P-Article-Buttons.P-Bottom').show();
		setShowHideContainer( 1, 1 );
	}else{				//hide left column
		gLeftColHide = 1;
		$('.' + gLeftContainerClass).addClass(gLeftContainerClassHide);
		$('.' + gMiddleContainerClass).addClass(gLeftContainerClassHide);
		$('.P-Article-Buttons.P-Bottom').hide();
		setShowHideContainer( 1, 0);
	}
}

var gRightColHide = 0;
function toggleRightContainer(){
	if( gRightColHide ){ //show right column
		gRightColHide = 0;
		$('.' + gRightContainerClass).removeClass(gRightContainerClassHide);
		$('.' + gMiddleContainerClass).removeClass(gRightContainerClassHide);
		setShowHideContainer( 2, 1 );
	}else{				 //hide right column
		gRightColHide = 1;
		$('.' + gRightContainerClass).addClass(gRightContainerClassHide);
		$('.' + gMiddleContainerClass).addClass(gRightContainerClassHide);
		setShowHideContainer( 2, 0);
	}
}

function setShowHideContainer( pLeftOrRight, pShowOrHide ){
	$.ajax({
		url : gShowOrHideColumnSrv,
		async: false,
		dataType : 'json',
		data :{
			left_or_right : pLeftOrRight, /* 1 - Лява, 2 - Дясна */
			show_or_hide : pShowOrHide /* 1 - Hide, 0 - Showed			*/
		},
		success: function(AjaxResult){

		}
	});
}

function getInputFileValue( pInputFrom ){
	$(pInputFrom).siblings('.' + gUploadFileNameHolderClass).html($(pInputFrom).val());

}

function triggerClick( pId ){
	$('#' + pId).trigger('click');
}

/* POPUP */
function popUp(pOper, pFrameSrc, pLayerID) {
	var frame = document.getElementById(pFrameSrc);
	var frameDivShadow = document.getElementById('layerbg');
	var frameDivHolder = document.getElementById(pLayerID);


	if(pOper == POPUP_OPERS.open && typeof pFrameSrc != 'undefined') { // Open a pop-up iframe
		if(frame != 'undefined' && frame != null && frameDivShadow != 'undefined' && frameDivShadow != null && frameDivHolder != 'undefined' && frameDivHolder != null) {
			//setIframeResize(1);
			toggleLayer2('block', pLayerID);
			frame.style.display = 'block';

			var screenSize = getScreenSize();
			var frameHeight = $(frame).height();
			var cssTop = ((screenSize.height - frameHeight) / 2);
			frameDivHolder.style.position = 'fixed';
			/*frameDivHolder.style.top = 10+ 'px';*/
			frameDivHolder.style.top = cssTop + 'px';
		}
	} else if (pOper == POPUP_OPERS.close && typeof pFrameSrc != 'undefined') { // Close the pop-up iframe
		if(frameDivShadow != 'undefined' && frameDivShadow != null && frameDivHolder != 'undefined' && frameDivHolder != null) {
			frameDivShadow.style.display = 'none';
			frameDivHolder.style.display = 'none';
		}
	}
}

function getScreenSize() {
	var myHeight = 0; var myWidth = 0;
	if (window.innerWidth && window.innerHeight) {
		// Netscape & Mozilla
		myHeight = window.innerHeight;
		myWidth = window.innerWidth;
	} else if( document.documentElement && ( document.documentElement.clientWidth || document.documentElement.clientHeight ) ) {
		// IE > 6
		myHeight = document.documentElement.clientHeight;
		myWidth = document.documentElement.clientWidth;
	} else if (document.body.offsetWidth && document.body.offsetHeight) {
		// IE = 6
		myHeight = document.body.offsetHeight;
		myWidth = document.body.offsetWidth;
	} else if (document.body.clientWidth && document.body.clientHeight) {
		// IE < 6
		myHeight = document.body.clientHeight;
		myWidth = document.body.clientWidth;
	}

	return { 'width': myWidth, 'height': myHeight };
}

function toggleLayer2(disp, pLayerObj, wdth) {
	var el = $('#' + pLayerObj);
	var elJs = document.getElementById(pLayerObj);
	var elbg = $('#layerbg');
	var bgheight = 0;

	if (disp == 'block') {
		var screensize = getScreenSize();
		var scrollsize = getScrollXY();
		// Layer formata
		$(el).show();
		mosopen = true;
		if(typeof wdth != "undefined"){
			$(el).css('width', wdth+'px');
		}

		$(el).css('left', ((screensize.width - elJs.offsetWidth)/2+scrollsize.x) + 'px');
		// Siviq fon za layer formata

		$(elbg).show();

		// tva e zaradi IE-to razbira se :)
		var docheight = parseInt(document.body.parentNode.offsetHeight);
		var bodyheight = parseInt(document.body.offsetHeight);
		bgheight = Math.max(Math.max(docheight, bodyheight), (screensize.height+scrollsize.y));
		$(elbg).css('height', parseInt(bgheight)+'px');
		//$(elbg).css('top', '-100px');

		bgwidth = screensize.width+scrollsize.x;
		//$(elbg).css('width', parseInt(bgwidth)+'px');
		$(elbg).css('width', '100%');
	} else if (disp == 'none') {
		$(el).hide();
		$(elbg).hide();
		mosopen = false;
		$(el).css('width', '400px');
		$(el).html('');
		$(elbg).css('width', '10px');
	}
}

function getScrollXY() {
	var scrOfX = 0, scrOfY = 0;
	if( typeof( window.pageYOffset ) == 'number' ) {
		//Netscape compliant
		scrOfY = window.pageYOffset;
		scrOfX = window.pageXOffset;
	} else if( document.body && ( document.body.scrollLeft || document.body.scrollTop ) ) {
		//DOM compliant
		scrOfY = document.body.scrollTop;
		scrOfX = document.body.scrollLeft;
	} else if( document.documentElement && ( document.documentElement.scrollLeft || document.documentElement.scrollTop ) ) {
		//IE6 standards compliant mode
		scrOfY = document.documentElement.scrollTop;
		scrOfX = document.documentElement.scrollLeft;
	}

	return { 'x': scrOfX, 'y': scrOfY };
}

popUpMenu = function( pMenuId ){
	$('#' + pMenuId).find('li').each(function(){
		$(this).bind('click', function(){
			$(this).siblings().removeClass(gActiveClass);
			$(this).addClass(gActiveClass);
		});
	});
};


plateAppearance = function( pClass , pDocId ){
	$("input[name*='" + pClass + "']").each(function(){
		$(this).bind('click', function(){
			ChangePlateAppearance($(this).val(), pDocId);
		});
	});
};

/* Change plate figures appearance */
function ChangePlateAppearance(pVal, pDocId, pPlateId) {
	var plateid = pPlateId;
	if(pPlateId) {
		$("input[name*='plate_id']").val(plateid);
	} else {
		plateid = $("input[name*='plate_id']").val();
	}
	$.ajax({
		url : gFiguresAjaxSrv,
		dataType: 'json',
		data :{
			action : 'get_plate_apperance',
			plate_val : pVal,
			document_id: pDocId,
			plate_id: plateid
		},
		success : function(pAjaxResult){
			if(pAjaxResult['err_cnt']){
				alert(pAjaxResult['err_msg']);
				return;
			} else {
				if(plateid != 0) {
					UpdatePlateType(pDocId, plateid, pVal);
				}
				var lWrapper = $('.P-Plates-Holder');
				lWrapper.html(pAjaxResult['html']);
			}
		}
	});
}

/* List of figures form by document id */
function GetDocumentFigures( pDocId, pFiguresHolderId ) {
	if(pDocId) {
		$.ajax({
			url : gListFiguresAjaxSrv,
			dataType: 'json',
			async: false,
			data :{
				document_id: pDocId,
			},
			success : function(pAjaxResult){
				if(pAjaxResult['err_cnt']){
					alert(pAjaxResult['err_msg']);
					return;
				} else {
					var lWrapper = $('#' + pFiguresHolderId);
					lWrapper.html(pAjaxResult['html']);
				}
			}
		});
	}
}

/* List of tables form by document id */
function GetDocumentTables( pDocId, pTableHolderId ) {
	if(pDocId) {
		$.ajax({
			url : gListTablesAjaxSrv,
			dataType: 'json',
			async: false,
			data :{
				document_id: pDocId,
			},
			success : function(pAjaxResult){
				if(pAjaxResult['err_cnt']){
					alert(pAjaxResult['err_msg']);
					return;
				} else {
					var lWrapper = $('#' + pTableHolderId);
					lWrapper.html(pAjaxResult['html']);
				}
			}
		});
	}
}

function GetDocumentReferences(pDocId, pHolderId){
	if (pDocId) {
		$.ajax({
			async: false,
			url : gListReferencesAjaxSrv,
			dataType : 'json',
			data : {
				document_id : pDocId,
			},
			success : function(pAjaxResult) {
				if (pAjaxResult['err_cnt']) {
					alert(pAjaxResult['err_msg']);
					return;
				} else {
					var lWrapper = $('#' + pHolderId);
					lWrapper.html(pAjaxResult['html']);
				}
			}
		});
	}
}

function GetDocumentSupFiles(pDocId, pHolderId){
	if (pDocId) {
		$.ajax({
			async: false,
			url : gListSupFilesAjaxSrv,
			dataType : 'json',
			data : {
				document_id : pDocId,
			},
			success : function(pAjaxResult) {
				if (pAjaxResult['err_cnt']) {
					alert(pAjaxResult['err_msg']);
					return;
				} else {
					var lWrapper = $('#' + pHolderId);
					lWrapper.html(pAjaxResult['html']);
				}
			}
		});
	}
}

/* Update plate type */
function UpdatePlateType(pDocId, pPlateId, pPlateVal) {
	$.ajax({
		url : gFiguresAjaxSrv,
		dataType: 'json',
		data :{
			action : 'update_plate_type',
			plate_id : pPlateId,
			plate_val : pPlateVal,
			document_id : pDocId,
		},
		success : function(pAjaxResult){
		}
	});
}

/* Change plate figures content */
function ChangeFiguresForm(pFormName, pDocumentId, pUpdateHolder, pClear, pShowEdit, pPhotoId, pPlateId, pCitation) {
	if(pClear == 1) {
		var lWrapper = $('.' + pUpdateHolder);
		lWrapper.html('');
		$("#plate_id_value").val('');
	} else {
		if(!pPhotoId)
			pPhotoId = 0;
		if(!pPlateId)
			pPlateId = 0;
		if(!pCitation)
			pCitation = 0;
		$.ajax({
			url : gFiguresAjaxSrv,
			dataType: 'json',
			data :{
				action : 'get_figures_form',
				form_name : pFormName,
				document_id : pDocumentId,
				photo_id : pPhotoId,
				plate_id : pPlateId,
				edit : pShowEdit,
				citation : pCitation
			},
			success : function(pAjaxResult){
				if(pAjaxResult['err_cnt']){
					alert(pAjaxResult['err_msg']);
					return;
				}

				var lWrapper = $('.' + pUpdateHolder);
				lWrapper.html(pAjaxResult['html']);

				$('ul#popUp_nav li').removeClass('P-Active');
				if(pFormName == 'video') {
					$('#P-PopUp-Figures-Title').html('Add video');
					var lAddToJs = '';
					if(pCitation){
						lAddToJs = 'gCurrentDialog.show();';
					}
					$("#P-Figures-Save-Button").hide();
					$("#save_video_btn").show();
					$("#save_video_btn").unbind('click').attr('onclick', "SaveVideoData(" + pPhotoId + ", 'video_link_field', " + pDocumentId + ", 'video_title_textarea'); popUp(POPUP_OPERS.close, 'add-figure-popup', 'add-figure-popup');" + lAddToJs);

					$('ul#popUp_nav li:first').next().next().addClass('P-Active');
				}else if(pFormName == 'plate') {
					$('#P-PopUp-Figures-Title').html('Add multiple images (plate)');
					$('ul#popUp_nav li:first').next().addClass('P-Active');
					$("#P-Figures-Save-Button").show();
					$("#save_video_btn").hide();

				}else if(pFormName == 'image') {
					$('#P-PopUp-Figures-Title').html('Add image');
					$("#P-Figures-Save-Button").show();
					$("#save_video_btn").hide();
					$('ul#popUp_nav li:first').addClass('P-Active');
				}
				//~ var newclick = new Function(js);
			}
		});
	}
}

function ShowDialogOnClose(pCitationInputId) {
	var lCitationFlag = $(pCitationInputId).val();
	if(lCitationFlag == 1) {
		gCurrentDialog.show();
	}
}

/* image uploading with ajax */
function ajaxFileUpload(pBtnId, pImgDesc, pDocId, pUpdateHolder, pPicIdHolder, pPlateVal, pPref, pResize, pPosition) {

	var photoid = $("input[name*='" + pPicIdHolder + "']").val();
	var plateid = $("input[name*='plate_id']").val();
	var btnUpload = $('#' + pBtnId);

	var AjaxFileUpload = new AjaxUpload(btnUpload, {
		action: '/lib/UploadPhoto.php',
		responseType: 'json',
		name: 'uploadfile',
		hoverClass: 'UploadHover',
		data: {
			document_id: pDocId,
			//description: ($('#' + pImgDesc).val() ? $('#' + pImgDesc).val() : ''),
			photo_id: photoid,
			image_pref: pPref,
			plateval: pPlateVal,
			position: pPosition,
			plateid: plateid,
		},
		onSubmit: function(file, ext){
			showLoading();
			var newphotoid = $("input[name*='" + pPicIdHolder + "']").val(); // get uploaded pic_id

			var plateid = 0;
			if(pResize) {
				plateid = 0;
			} else {
				plateid = $("input[name*='plate_id']").val();
			}
			var desc = $('#' + pImgDesc + '_photo').val();
			if(!desc || typeof(desc) == 'undefined') {
				desc = '';
			}
			AjaxFileUpload.setData({
					document_id: pDocId,
					description: desc,
					photo_id: newphotoid,
					image_pref: pPref,
					plateval: pPlateVal,
					position: pPosition,
					plateid: plateid,
			});

			 if (! (ext && /^(jpg|png|jpeg|gif)$/i.test(ext))){
				hideLoading();
				$('#' + pUpdateHolder).text('Only JPG, PNG or GIF files are allowed');
				return false;
			}
		},
		onComplete: function(file, response){
			hideLoading();
			if(response != 0 && !response['err_cnt']){
				$("input[name*='" + pPicIdHolder + "']").val(response['pic_id']);
				if(response['plate_id'] != 0)
					$("input[name*='plate_id']").val(response['plate_id']);
				if(pResize){

					var dims = jQuery.parseJSON(response['img_dims']); // get picture dimensions and resize container
					$('.P-Plate-Part').width(dims[0] + 20);
					$('.P-Plate-Part-WithPic').width(dims[0] + 20);
					$('.P-Plate-Part').height(dims[1] + 20);
					$('.P-Plate-Part-WithPic').height(dims[1] + 20);
					$('.P-Add-Plate-Holder').width(dims[0]);
					$('.P-Add-Plate-Holder').height(dims[1]);
					$("#uploaded_photo").attr("src", "/showfigure.php?filename=" + response['html'] + ".jpg");
					$('#' + pUpdateHolder).closest('.P-Plate-Part').removeClass('P-Plate-Part').addClass('P-Plate-Part-WithPic');
					$('#' + pUpdateHolder).html('<img id="uploaded_photo" src="/showfigure.php?filename=' + response['html'] + '.jpg"></img>');
					$("#uploaded_photo").attr("src","/showfigure.php?filename=" + response['html'] + ".jpg&" + Math.random()); // za da se refreshne snimkata
					if(response['pic_id']) { //update pic holder
						$('#P-Figures-Row-' + response['pic_id'] + ' .P-Picture-Holder').html('<img src="/showfigure.php?filename=c90x82y_' + response['pic_id'] + '.jpg&' + Math.random() + '"></img>');
					}
				} else {
					$('#' + pUpdateHolder).closest('.P-Plate-Part').removeClass('P-Plate-Part').addClass('P-Plate-Part-WithPic');
					$('#' + pUpdateHolder).html('<img  id="uploaded_photo_' + response['pic_id'] + '" src="/showfigure.php?filename=' + response['html'] + '.jpg"></img>');
					$("#uploaded_photo_" + response['pic_id']).attr("src","/showfigure.php?filename=" + response['html'] + ".jpg&" + Math.random()); // za da se refreshne snimkata
				}
			} else{
				if(response['err_msg']) {
					$('#' + pUpdateHolder).html(response['err_msg']);
				} else {
					$('#' + pUpdateHolder).html('error uploading file');
				}
			}
		}
	});
}

/* Change image title */
function SavePicDetails(pTitleHolder, pPicIdHolder) {
	var photoid = $("input[name*='" + pPicIdHolder + "']").val();
	var title = $('#' + pTitleHolder).val();
	$.ajax({
		url : gFiguresAjaxSrv,
		dataType: 'json',
		data :{
			action : 'save_pic_description',
			photo_id : photoid,
			photo_title : title,
		},
	});
}

/* Change plate description and photos text */
function SavePlateData(pDescHolder, pPlateHolder, pPhotoHolder, pFormName, pClear) {
	SyncCKEditors();
	var desc = $('#' + pDescHolder).val();
	var plateid = $("input[name*='" + pPlateHolder + "']").val();
	var photo_id = $("input[name*='" + pPhotoHolder + "']").val();

	if(!desc || desc == ''){
		alert('Caption is required');
		return false;
	}

	// updating all photo texts
	if(plateid) {
		var lFormData = $('form[name="' + pFormName + '"] textarea');
		jQuery.each(lFormData, function() {
			var textarea_photo_holder_id = $(this).attr("id");
			var textarea_photo_id = $(this).attr("name");
			SavePicDetails(textarea_photo_holder_id, 'picture_id_' + textarea_photo_id);
		});
	}

	$.ajax({
		url : gFiguresAjaxSrv,
		dataType: 'json',
		data :{
			action : 'save_plate_photo_details',
			plate_desc : desc,
			plate_id : plateid,
			photo_id : photo_id,
		},
		success : function(pAjaxResult){
			if(pAjaxResult) {
				//console.log((gPreviewMode == true));
				if(gPreviewMode == true){
					HandlePreviewModeCreateInstance();
					return;
				}
				gCitationFlag = $('#citation_flag').val();
				if(pClear == 1) {
					var lWrapper = $('.P-PopUp-Content-Inner');
					lWrapper.html('');
					$("input[name*='plate_id']").val('');
				}
				$('#P-Figures-Row-' + pAjaxResult + ' .P-Figure-Desc').html(desc);
				if(photo_id) {
					UpdateDocumentFiguresHolder(pAjaxResult, 'P-Document-Figures-Container', 1);
					//alert('The Figure is saved!');
					//TODO: implement the alert as a 'yellow fade' http://37signals.com/svn/archives/000558.php
				} else if (plateid) {
					$("#plate_id_value").val('');
					UpdateDocumentFiguresHolder(pAjaxResult, 'P-Document-Figures-Container', 1);
					//alert('The Plate is saved!');
					//TODO: implement the alert as a 'yellow fade' http://37signals.com/svn/archives/000558.php
				}

				if(gCitationFlag == 1) {
					gCurrentDialog.show();
				}
				$('.P-PopUp-Content-Inner').html('');
				popUp(POPUP_OPERS.close, 'add-figure-popup', 'add-figure-popup');
			}
		}
	});
}

/* Change plate desc */
function SavePlateDataAndUpdateFiguresPopUp(pDescHolder, pPlateHolder, pPhotoHolder, pFormName) {
	SyncCKEditors();
	var desc = $('#' + pDescHolder).val();
	var plateid = $("input[name*='" + pPlateHolder + "']").val();
	var photo_id = $("input[name*='" + pPhotoHolder + "']").val();

	// updating all photo texts
	if(plateid) {
		var lFormData = $('form[name="' + pFormName + '"] textarea');
		jQuery.each(lFormData, function() {
			var textarea_photo_holder_id = $(this).attr("id");
			var textarea_photo_id = $(this).attr("name");
			SavePicDetails(textarea_photo_holder_id, 'picture_id_' + textarea_photo_id);
		});
	}
	$.ajax({
		url : gFiguresAjaxSrv,
		dataType: 'json',
		data :{
			action : 'save_plate_photo_details',
			plate_desc : desc,
			plate_id : plateid,
			photo_id : photo_id,
		},
		success : function(pAjaxResult){
			if(pAjaxResult) {
				if(gPreviewMode == true){
					HandlePreviewModeCreateInstance();
					return;
				}
				$('#P-Figures-Row-' + pAjaxResult + ' .P-Figure-Desc').html(desc);
				if(photo_id) {
					//alert('The Figure is saved!');
					//TODO: implement the alert as a 'yellow fade' http://37signals.com/svn/archives/000558.php
				} else if (plateid) {
						$("#plate_id_value").val('');
					//alert('The Plate is saved!');
					//TODO: implement the alert as a 'yellow fade' http://37signals.com/svn/archives/000558.php
				}
				//GetDocumentFigures(GetDocumentId());
				ShowDialogOnClose('#citation_flag');
				popUp(POPUP_OPERS.close, 'add-figure-popup', 'add-figure-popup');
				$('.P-PopUp-Content-Inner').html('');
				//gCurrentDialog.show();
			}
		}
	});
}

/* Show edit popup for table */
function ShowEditTablePopup(pTableId, pTableHolder) {
	$.ajax({
		url : gFiguresAjaxSrv,
		dataType: 'json',
		data :{
			action : 'show_table_popup',
			table_id : pTableId,
		},
		success : function(pAjaxResult){
			if(pAjaxResult['err_cnt']){
				alert(pAjaxResult['err_msg']);
				return;
			}
			// Update popup content
			$('.' + pTableHolder).html(pAjaxResult['html']);
			// Show tables popup
			popUp(POPUP_OPERS.open, 'add-table-popup', 'add-table-popup');
		}
	});
}

function ShowAddTablePopup(pDocumentId, pPopUpHolder, pShowInCitationControl) {
	$.ajax({
		url : gFiguresAjaxSrv,
		dataType: 'json',
		data :{
			action : 'show_table_popup',
			document_id : pDocumentId,
			show_in_citation: pShowInCitationControl
		},
		success : function(pAjaxResult){
			if(pAjaxResult['err_cnt']){
				alert(pAjaxResult['err_msg']);
				return;
			}
			// Update popup content
			$('#' + pPopUpHolder).find('.P-PopUp-Main-Holder').html(pAjaxResult['html']);
			// Show tables popup
			popUp(POPUP_OPERS.open, 'add-table-popup', 'add-table-popup');
		}
	});
}

/* Add inserted table to content */
function UpdateDocumentTableHolder(pTableId, pTableHolder, pHtmlReplace, pCurrPosition, pMaxPosition, pMinPosition) {
	if(!pCurrPosition && !pMaxPosition && !pMinPosition)
		pCurrPosition = pMaxPosition = pMinPosition = 0;

	if(!pHtmlReplace)
		pHtmlReplace = 0;

	$.ajax({
		url : gFiguresAjaxSrv,
		dataType: 'json',
		data :{
			action : 'update_table_holder',
			table_id : pTableId,
			curr_position : pCurrPosition,
			max_position : pMaxPosition,
			full_html : pHtmlReplace ? 0 : 1,
			min_position : pMinPosition
		},
		success : function(pAjaxResult){
			if(pAjaxResult['err_cnt']){
				alert(pAjaxResult['err_msg']);
				return;
			}
			if(!pHtmlReplace) {
				if($('#' + pTableHolder).length) {

					if(!$('#P-Table-Row-' + pTableId).length)
						$('#' + pTableHolder).append(pAjaxResult['html']); //append to table
					else
						$('#P-Table-Row-' + pTableId).replaceWith(pAjaxResult['html']);


					//var row = $(pThis).parents(".P-Data-Resources-Control:first");

					var row = $('#P-Table-Row-' + pTableId).closest(".P-Data-Resources-Control");
					var prevRowId = row.prev('.P-Data-Resources-Control').find("input[name*='table_id']").val();
					if(prevRowId)
						UpdateDocumentTableHolder(prevRowId, 'P-Table-Row-' + prevRowId, 1, 0, 0, 0); // update moved row
				}
			} else {
				$('#' + pTableHolder).html(pAjaxResult['html']);
			}
		}
	});
}

/* Add created plate to content */
function UpdateDocumentFiguresHolder(pPhotoId, pFiguresHolder, pAppend, pCurrPosition, pMaxPosition, pMinPosition) {
	if(!pCurrPosition && !pMaxPosition && !pMinPosition)
		pCurrPosition = pMaxPosition = pMinPosition = 0;
	$.ajax({
		url : gFiguresAjaxSrv,
		dataType: 'json',
		data :{
			action : 'update_figures_holder',
			photo_id : pPhotoId,
			append : pAppend,
			curr_position : pCurrPosition,
			max_position : pMaxPosition,
			min_position : pMinPosition
		},
		success : function(pAjaxResult){
			if(pAjaxResult['err_cnt']){
				alert(pAjaxResult['err_msg']);
				return;
			}
			if (pAppend) {
				if($('#' + pFiguresHolder + ' > tbody:last').length) {
					if(!$('#P-Figures-Row-' + pPhotoId).length)
						$('#' + pFiguresHolder + ' > tbody:last').append(pAjaxResult['html']); //append to table
					else
						$('#P-Figures-Row-' + pPhotoId).replaceWith(pAjaxResult['html']);

					var row = $('#P-Figures-Row-' + pPhotoId).closest("tr");
					var prevRowId = row.prev('.P-Data-Table-Holder').find("input[name*='plate_photo_id']").val();
					if(prevRowId)
						UpdateDocumentFiguresHolder(prevRowId, 'P-Figures-Row-' + prevRowId, 0, 0, 0, 0); // update moved row
				}
			} else {
				$('#' + pFiguresHolder).html(pAjaxResult['html']);
			}
		}
	});
}

function CloseTablePopUp(pTablePopUpHolder, pTableTitleHolder) {
	$('#' + pTableTitleHolder).val('');
	popUp(POPUP_OPERS.close, pTablePopUpHolder, pTablePopUpHolder);
}

function SyncCKEditors(){
	for(var lInstanceName in CKEDITOR.instances){
	    CKEDITOR.instances[lInstanceName].updateElement();
	}
}

function HandlePreviewModeDeleteInstance(pAjaxResult){
	var lQueryParams = getQueryParams(window.location.search);
	var lDeletedInstanceId = pAjaxResult['deleted_instance_id'];
	var lCurrentInstanceId = lQueryParams['instance_id'];
	var lCurrentDocumentId = lQueryParams['document_id'];
	if(lDeletedInstanceId == lCurrentInstanceId){
		var lUrl = '/preview.php?document_id=' + lCurrentDocumentId;
		window.location.href = lUrl;
	}else{
		window.location.reload();
	}
}

function HandlePreviewModeCreateInstance(pAjaxResult){
	if(pAjaxResult){
		var lNewInstanceId = pAjaxResult['new_instance_id'];
		if(lNewInstanceId){
			var lQueryParams = getQueryParams(window.location.search);
			var lCurrentDocumentId = lQueryParams['document_id'];
			var lUrl = '/preview.php?document_id=' + lCurrentDocumentId + '&instance_id=' + lNewInstanceId;
			window.location.href = lUrl;
			return;
		}	
	}
	window.location.reload();
}

function HandlePreviewModeMoveInstance(){
	window.location.reload();
}

function HandleActiveMenuAfterInstanceCreation(pAjaxResult){
	if(pAjaxResult){
		var lParentInstanceId = pAjaxResult['parent_instance_id'];
		if(lParentInstanceId){
			setMenuTabAsActive(1, lParentInstanceId);
		}
	}
}

/* Save table title */
function SaveTableData(pTitleHolder, pFormName, pDocumentId, pTableId) {
	var lFormData = $('form[name="' + pFormName + '"]');
	if($('#' + pTitleHolder).val() == ''){
		alert('Table caption is required');
		return false;
	}

	lFormData.ajaxSubmit({
		'dataType' : 'json',
		'url' : gFiguresAjaxSrv,
		'data' : {
			'action' : 'save_table_details',
			'document_id' : pDocumentId,
			'table_id' : pTableId,
		},
		'success' : function(pAjaxResult){
			if(gPreviewMode == true){
				HandlePreviewModeCreateInstance();
				return;
			}
			if(pAjaxResult['tableid']) { //Add
				UpdateDocumentTableHolder(pAjaxResult['tableid'], 'P-Document-Tables-Container');
			} else {  // Edit
				$('#P-Table-Row-' + pTableId + ' .P-Block-Title').html(pAjaxResult['table_title']);
			}
			//alert('Table is saved!');
			//TODO: implement the alert as a 'yellow fade' http://37signals.com/svn/archives/000558.php
			$('#' + pTitleHolder).val('');
			popUp(POPUP_OPERS.close, 'add-table-popup', 'add-table-popup');
		}
	});
}

/* Save table */
function SaveTableDataAndRefreshTablesBaloon(pDocumentId, pTitleHolder, pDescHolder, pTableId) {
	var title = $('#' + pTitleHolder).val();
	var desc = $('#' + pDescHolder).val();
	if(!title || !desc) {
		//~ alert('Content is empty');
		//~ return;
	}
	if(!pTableId)
		pTableId = 0;
	$.ajax({
		url : gFiguresAjaxSrv,
		dataType: 'json',
		data :{
			action : 'save_table_details',
			document_id : pDocumentId,
			table_title : title,
			table_id: pTableId,
			table_desc : desc,
		},
		success : function(pAjaxResult){
			if(pAjaxResult) { //Add
				//alert('Table is saved!');
				//TODO: implement the alert as a 'yellow fade' http://37signals.com/svn/archives/000558.php
				//GetDocumentTables(GetDocumentId());
				$('#' + pTitleHolder).val('');
				popUp(POPUP_OPERS.close, 'add-table-popup', 'add-table-popup');
				gCurrentDialog.show();
			}
		}
	});
}


/* Save reference */
function SaveReferenceBaloon(pDocumentId) {
	$.ajax({
		url : gFiguresAjaxSrv,
		dataType: 'json',
		data :{
			action : 'save_table_details',
			document_id : pDocumentId,
			table_title : title,
			table_id: pTableId,
			//~ table_desc : desc,
		},
		success : function(pAjaxResult){
			if(pAjaxResult) { //Add
				//alert('Table is saved!');
				//TODO: implement the alert as a 'yellow fade' http://37signals.com/svn/archives/000558.php
				gCurrentDialog.show();
				GetDocumentTables(GetDocumentId());
				$('#' + pTitleHolder).val('');
				popUp(POPUP_OPERS.close, 'add-table-popup', 'add-table-popup');
			}
		}
	});
}

/* add required field class */
function addReqClass(pEl, pClass, pValidClass, pReqClass) {

	$('#' + pEl).closest('div').removeClass(pClass);
	if(pValidClass) {
		$('#' + pEl).closest('div').removeClass(pValidClass);
	}

	$('#' + pEl).closest('div').addClass(pReqClass);

}

function addReqClassById(pEl, pClass, pValidClass, pReqClass) {
	$('#' + pEl).removeClass(pClass);
	$('#' + pEl).removeClass(pValidClass);
	$('#' + pEl).addClass(pReqClass);
}

/* add error field class */
function addErrorClass(pEl, pClass, pValidClass, pReqClass) {
	if(pValidClass) {
		$('#' + pEl).closest('div').removeClass(pValidClass);
	}

	if(pReqClass) {
		$('#' + pEl).closest('div').removeClass(pReqClass);
	}

	$('#' + pEl).closest('div').addClass(pClass);
}

function addErrorClassById(pEl, pClass, pValidClass, pReqClass) {
	$('#' + pEl).closest('div.' + pReqClass).addClass(pClass);
}

/* add validate field class */
function removeErrorClass(pEl, pClass, pValidClass, pReqClass) {
	$('#' + pEl).closest('div.' + pReqClass).removeClass(pClass);
}

function addValidClassById(pEl, pClass, pValidClass, pReqClass) {
	$('#' + pEl).closest('div').removeClass(pClass);
	$('#' + pEl).closest('div').removeClass(pReqClass);
	$('#' + pEl).closest('div').addClass(pValidClass);
}


articleMenu = function( pMenuId, pShowBtnClass, pHideBtnClass ){
	this.m_Menu = $('#' + pMenuId);
	this.m_Show = pShowBtnClass;
	this.m_Hide = pHideBtnClass;

	this.init();
};

articleMenu.prototype.init = function(){
	var lThis = this;

	// Показваме
	$(this.m_Menu).find('.' + lThis.m_Show).each(function(){
		$(this).bind('click', function(){
			lThis.showSubMenu(this);
		});
	});
	// Крием
	$(this.m_Menu).find('.' + lThis.m_Hide).each(function(){
		$(this).bind('click', function(){
			lThis.hideSubMenu(this);
		});
	});
};

articleMenu.prototype.hideSubMenu = function( pObj ){
	var lThis = this;
	$(pObj).closest('li').children('ul').hide();
	$(pObj).removeClass(lThis.m_Hide)
		   .addClass(lThis.m_Show)
		   .unbind('click')
		   .bind('click', function(){
				lThis.showSubMenu(pObj);
			});
	resizeMiddleContainer();
	setMenuTabAsActive( 0, $(pObj).closest('li').attr('id') );
};

articleMenu.prototype.showSubMenu = function( pObj ){
	var lThis = this;

	$(pObj).closest('li').children('ul').show();
	$(pObj).removeClass(lThis.m_Show)
		   .addClass(lThis.m_Hide)
		   .unbind('click')
		   .bind('click', function(){
				lThis.hideSubMenu(pObj);
			});
	resizeMiddleContainer();
	setMenuTabAsActive( 1, $(pObj).closest('li').attr('id') );
};

function setMenuTabAsActive( pIsActive, pTabId ){
	$.ajax({
		url : gActiveMenuTabSrv,
		async: false,
		dataType : 'json',
		data :{
			tab_id : pTabId,
			is_active : pIsActive
		},
		success: function(AjaxResult){

		}
	});
}

function selectAuthor(pActionId, pAuthorNameSearchId, pAuthorId){
	executeAction(pActionId, pAuthorNameSearchId, pAuthorId);
	return;

	var lAuthorNameSearch = $('#instance_wrapper_' + pAuthorNameSearchId);
	var lAuthorWrapper = lAuthorNameSearch.parent().closest('*[id^="instance_wrapper_"]');


	var lInputs = lAuthorWrapper.find('input[type=text]').not(lAuthorNameSearch.find('input[type=text]'));
	var lSelects = lAuthorWrapper.find('select').not(lAuthorNameSearch.find('select'));
	var lAuthorIsModified = false;
	for(var i = 0; i < lInputs.length; ++i){
		if($(lInputs[i]).val() != ''){
			lAuthorIsModified = true;
			break;
		}
	}
	if(!lAuthorIsModified){
		for(var i = 0; i < lSelects.length; ++i){
			if($(lSelects[i]).val() != ''){
				lAuthorIsModified = true;
				break;
			}
		}
	}
	if(lAuthorIsModified){
		if(confirm('Are you sure you want to overwrite the current data?')){
			executeAction(pActionId, pAuthorNameSearchId, pAuthorId);
		}
	}else{
		executeAction(pActionId, pAuthorNameSearchId, pAuthorId);
	}
}



function resizePreviewIframe(pIframeId){
	var lIframe = $('#'+ pIframeId);
	lIframe.height(lIframe.contents().find('body').height() + 20);
	lIframe.show();
}

// Този обект ще реализира сърч селекта от подаден скрит селект
var gSearchOpened = 0;
searchDropDown = function( pObjId, pOptionsHolder ){
	this.m_Search = $('#' + pObjId);
	this.m_Select = $('#' + pObjId).find('select');
	this.m_SelectedOption = $('#' + pObjId).find('.P-Option-Selected');
	this.m_SelectOptionsHolder = $('#' + pOptionsHolder);
	this.m_SelectOptionClass = 'P-Search-Option';
	this.m_SelectOptionsHolderMiddle = 'P-Options-Middle';
	this.init();
};

searchDropDown.prototype.init = function(){
	var lThis = this;
	var lOption = '';
	lThis.m_SelectedOption.html( lThis.m_Select.find( 'option:selected' ).text() );

	lThis.m_Select.find( 'option' ).each(function(){
		lOption = '<div class="' + lThis.m_SelectOptionClass + '">' + $(this).text() + '</div>';
		lThis.m_SelectOptionsHolder.find('.' + lThis.m_SelectOptionsHolderMiddle).append(lOption);
	});

	$(document).click(function(){
		if(gSearchOpened)
			lThis.hideDropDown();
	});

	lThis.m_SelectOptionsHolder.find('.' + lThis.m_SelectOptionsHolderMiddle).find('.' + lThis.m_SelectOptionClass).each(function(){
		$(this).bind('click', function(event){
			event.stopPropagation();
			var lOptionText = $(this).text();
			lThis.m_SelectedOption.text(lOptionText);
			lThis.m_Select.find( 'option:selected' ).attr('selected', false);
			lThis.m_Select.find( 'option' ).each(function(){
				if( $(this).text() == lOptionText ){
					$(this).attr('selected', 'selected');
				}
			});
			lThis.hideDropDown();
		});
	});

	lThis.m_Search.bind('click', function(event){
		event.stopPropagation();
		lThis.showDropDown();
	});
};

searchDropDown.prototype.showDropDown = function(){
	gSearchOpened = 1;
	this.m_SelectOptionsHolder.show();
};

searchDropDown.prototype.hideDropDown = function(){
	gSearchOpened = 0;
	this.m_SelectOptionsHolder.hide();
};

function toggleClassOnMiddleContainer(pOper, pClass){
	if( pOper == 1 )
		$('.' + gMiddleContainerClass ).addClass(pClass);
	else
		$('.' + gMiddleContainerClass ).removeClass(pClass);
}

function fixEditorMaximizeBtn(pEditor){
	var lJqueryContainer = $(pEditor.container.$);
	var lMaximizeButton = lJqueryContainer.find('.cke_button_maximize');
	var lToolbar = lMaximizeButton.closest('.cke_toolbar');
	lToolbar.addClass('cke_maximizeToolbox');
}

function changeTabbedElementActiveTab(pInstanceId, pTabbedElementId, pActiveItemId){
	if(!pActiveItemId){
		return;
	}
	var lTabbedElement = $('#tabbed_element_' + pInstanceId + '_' + pTabbedElementId);

	var lPrevActiveItemId = lTabbedElement.attr('active_item_id');
	lTabbedElement.attr('active_item_id', pActiveItemId);
	$('#tabbed_element_' + pInstanceId + '_' + pTabbedElementId + '_active_item').val(pActiveItemId);

	var lActiveItem = lTabbedElement.find('#tabbed_element_item_wrapper_' + pInstanceId + '_' + pTabbedElementId + '_' + pActiveItemId);
	var lElementItems = lTabbedElement.find('.tabbedElementItem');

	//Правим смяната на предишния активен елемент с колбек за да няма примигване
	//(за да може докато се извърши зареждането на новия активен елемент да показваме
	//предишния активен елемент вместо да не показваме нищо
	if(lPrevActiveItemId){
		if(lPrevActiveItemId == pActiveItemId){
			return;
		}


		var lSaveCallbackFunction = function(pAjaxResult){
			ChangeInstanceMode(GetDocumentId(), pActiveItemId, null, null, gInstanceEditMode);
			lElementItems.not(lActiveItem).hide();
			var $lInstanceWrapper = $('#instance_wrapper_' + lPrevActiveItemId);
			$lInstanceWrapper.before(pAjaxResult['instance_html']);
			$lInstanceWrapper.remove();
		};
		SaveInstance(lPrevActiveItemId, gInstanceViewMode, lSaveCallbackFunction);
	}else{
		lElementItems.not(lActiveItem).hide();
		ChangeInstanceMode(GetDocumentId(), pActiveItemId, null, null, gInstanceEditMode);
	}





	lActiveItem.show();

	var lTabs = lTabbedElement.find('.tabbedElementTab');
	var lActiveTab = lTabbedElement.find('#tabbed_element_tab_holder_' + pInstanceId + '_' + pTabbedElementId + '_' + pActiveItemId);
	lTabs.not(lActiveTab).removeClass('P-Active');
	lActiveTab.addClass('P-Active');
}

function scrollToTabbedElementField(pTabActiveInstanceId, pInstanceId, pFieldId){
	var lInstanceWrapper = $('#instance_wrapper_' + pTabActiveInstanceId);

	var lTabbedParents = lInstanceWrapper.parents('.tabbedElementItem');
	//Обикаляме всички parent-и, които са item-и на tabbed елемент
	for(var i = 0; i < lTabbedParents.length; ++i){
		var lTabbedItem = $(lTabbedParents[i]);
		var lTabbedItemParentInstanceId, lTabbedElementId, lTabbedItemInstanceId;
		var lPattern = new RegExp("^tabbed_element_item_wrapper_(\\d+)_(\\d+)_(\\d+)$","i");
		var lMatch = lPattern.exec(lTabbedItem.attr('id'));
		if(lMatch !== null){
			lTabbedItemParentInstanceId = lMatch[1];
			lTabbedElementId = lMatch[2];
			lTabbedItemInstanceId = lMatch[3];
			changeTabbedElementActiveTab(lTabbedItemParentInstanceId, lTabbedElementId, lTabbedItemInstanceId);
		}
	}

	scrollToField(pInstanceId, pFieldId);
}

function scrollToField(pInstanceId, pFieldId){
	var lItemWrapper = $('#field_wrapper_' + pInstanceId + '_' + pFieldId);
	if(!lItemWrapper.length)//Ако случайно не сме намерили елемента - край
			return;
//	//Гледаме дали е в елемент с табове. Ако да - трябва да се уверим че този елемент се вижда
//	var lTabbedParents = lItemWrapper.parents('.tabbedElementItem');
//	//Обикаляме всички parent-и, които са item-и на tabbed елемент
//	for(var i = 0; i < lTabbedParents.length; ++i){
//		var lTabbedItem = $(lTabbedParents[i]);
//		var lTabbedItemParentInstanceId, lTabbedElementId, lTabbedItemInstanceId;
//		var lPattern = new RegExp("^tabbed_element_item_wrapper_(\\d+)_(\\d+)_(\\d+)$","i");
//		var lMatch = lPattern.exec(lTabbedItem.attr('id'));
//		if(lMatch !== null){
//			lTabbedItemParentInstanceId = lMatch[1];
//			lTabbedElementId = lMatch[2];
//			lTabbedItemInstanceId = lMatch[3];
//			changeTabbedElementActiveTab(lTabbedItemParentInstanceId, lTabbedElementId, lTabbedItemInstanceId);
//		}
//	}

	//Накрая скролваме до търсения елемент
	if(gActiveInstanceFormName == gPopupFormName){
		$('.P-PopUp-Content').animate({
		    scrollTop: lItemWrapper.offset().top - $('#newElementPopup').offset().top - parseInt($('#newElementPopup').css("border-top-width"))
		}, 2000);
	}else{
		$('html, body').animate({
		    scrollTop: lItemWrapper.offset().top
		}, 2000);
	}
	return false;
}



/**
 * Връща първия DOM node, който съдържа и 2та възела
 * @param pNodeA
 * @param pNodeB
 */
function getFirstCommonParent(pNodeA, pNodeB){
	if(!pNodeA || !pNodeB){
		return false;
	}
	if (checkIfNodesAreParentAndDescendent(pNodeA, pNodeB))// pNodeB е подвъзел на pNodeA
		return pNodeA;

	if (checkIfNodesAreParentAndDescendent(pNodeB, pNodeA))// pNodeA е подвъзел на pNodeB
		return pNodeB;

	var lParentsA = $(pNodeA).parents();
	var lParentsB = $(pNodeB).parents();
	// if( this.in_array(lParentsA, pNodeB ))
	// return pNodeB;
	for ( var i = 0; i < lParentsA.length; ++i) {
//		if (jQuery.inArray(lParentsA[i], lParentsB) > -1)
//			return lParentsA[i];
		if(lParentsB.filter(lParentsA.get(i)).length){
			return lParentsA.get(i);
		}
	}
}

/**
 * Връща 1 ако pNodeA е преди pNodeB в DOM-a.
 * Ако двата node-a съвпадат - 0
 * и -1 в противен случай
 * @param pNodeA
 * @param pNodeB
 */
function compareNodesOrder(pNodeA, pNodeB){
	if (pNodeA === pNodeB)// Двата възела съвпадат
		return 0;
	var lCommonParent = getFirstCommonParent(pNodeA, pNodeB);

	// Гледаме дали някой от възлите не е подвъзел на другия
	if (lCommonParent === pNodeA) {
		return 1;
	} else if (lCommonParent === pNodeB) {
		return -1;
	}

	var lChildren = $(lCommonParent).contents();
	/*
	 * Проверката е коректна понеже lCommonParent е 1ят общ родител на 2та
	 * възела => едно от децата му съдържа pNodeA, а друго - pNodeB
	 */

	for ( var i = 0; i < lChildren.length; ++i) {
		var lCurrentChild = lChildren.get(i);
		if (lCurrentChild === pNodeA) {// TextNode
			return 1;
		}
		if (lCurrentChild === pNodeB) {
			return -1;
		}
		if (lCurrentChild.nodeType == 1) {
			if (checkIfNodesAreParentAndDescendent(lCurrentChild, pNodeA)) {
				return 1;
			}
			if (checkIfNodesAreParentAndDescendent(lCurrentChild, pNodeB)) {
				return -1;
			}
		}
	}
}

function hideElement(pElemClass) {
	$('.' + pElemClass).hide();
}


// Loop through elements in menu and show the list if it has warning class
function showAllWarningInstancesInMenu(pMenuId, pWarningClass) {
	$("#articleMenu li div").each(function(index) {
		if($(this).hasClass(pWarningClass)) {
			$(this).parents("ul").show();
		}
	});
}


var getDomNodeStyleProperty = function(pNode, pProperty){
	// Get the style of the node.
	// Assumptions are made here based on tagName.
	if(pNode.style[pProperty]){
		return pNode.style[pProperty];
	}
	var lStyle = pNode.currentStyle || pNode.ownerDocument.defaultView.getComputedStyle(pNode, null);
	if(pNode.tagName == "SCRIPT"){
		return "none";
	}
	if(!lStyle[pProperty]){
		return "LI,P,TR".indexOf(pNode.tagName) > -1
			? "block"
			: pNode.style[pProperty];
	}
	if(lStyle[pProperty] =="block" && pNode.tagName=="TD"){
		return "feaux-inline";
	}
	return lStyle[pProperty];
};

function checkIfDomNodeHasBlockDisplay(pDomNode){
	var blockTypeNodes = "table-row,block,list-item";
	// diaply:block or something else
	var lStyleDisplay = getDomNodeStyleProperty(pDomNode, "display") || "feaux-inline";
	if(blockTypeNodes.indexOf(lStyleDisplay) > -1){
		return true;
	}
	return false;
}


function checkIfNodesAreParentAndDescendent(pNodeA, pNodeB){
	if(pNodeA === pNodeB)
		return false;
	var lParent = pNodeB.parentNode;
	while(lParent){
		if(lParent === pNodeA)
			return true;
		lParent = lParent.parentNode;
	}
	return false;
}

/**
 * Тази функция ще е аналогична на jquery функцията addClass, но ще работи за всички атрибути, не само за клас.
 * Ако подадената стойност вече е добавена - не правим нищо.
 * @param pJQueryObject - jquery обекта, на който ще променяме атрибута
 * @param pAttributeName - името на атрибута
 * @param pValue - стойността, която добавяме
 */
function addAttributeValue(pJQueryObject, pAttributeName, pValue, pSeparator){
	if (typeof pSeparator == 'undefined'){
		pSeparator = ' ';
	}
	var lPreviousAttributeValue = pJQueryObject.attr(pAttributeName);
	if(!lPreviousAttributeValue){
		lPreviousAttributeValue = '';
	}
	//Ако стойността е вече добавенa - не правим нищо

	//Добавяме separator-a отпред и отзад за по-лесно търсене
	var lTemp = pSeparator + lPreviousAttributeValue + pSeparator;

	if(lTemp.indexOf(pSeparator + pValue + pSeparator) > -1){
		return;
	}



	//Добавяме стойността
	if(lPreviousAttributeValue.length == 0){
		pJQueryObject.attr(pAttributeName, pValue);
	}else{
		pJQueryObject.attr(pAttributeName, lPreviousAttributeValue + pSeparator + pValue);
	}

}

/**
 * Тази функция ще е аналогична на jquery функцията removeClass, но ще работи за всички атрибути, не само за клас.
 * Ако подадената стойност не е активна - не правим нищо.
 * @param pJQueryObject - jquery обекта, на който ще променяме атрибута
 * @param pAttributeName - името на атрибута
 * @param pValue - стойността, която махаме
 * @param pSeparator - разделителя между стойностите. По подразбиране, а и в removeClass е интервал
 */
function removeAttributeValue(pJQueryObject, pAttributeName, pValue, pSeparator){
	if (typeof pSeparator == 'undefined'){
		pSeparator = ' ';
	}
	var lPreviousAttributeValue = pJQueryObject.attr(pAttributeName);
	if(!lPreviousAttributeValue){
		lPreviousAttributeValue = '';
	}
	//Ако стойността я няма - не правим нищо
	//Добавяме separator-a отпред и отзад за по-лесно търсене
	var lTemp = pSeparator + lPreviousAttributeValue + pSeparator;

	if(lTemp.indexOf(pSeparator + pValue + pSeparator) == -1){
		return;
	}

	//Изчисляваме новата стойност
	var lValues = lPreviousAttributeValue.split(pSeparator);
	var lNewValue = '';
	for(var i = 0; i < lValues.length; ++i){
		var lCurrentValue = lValues[i];
		if(lCurrentValue == pValue){
			continue;
		}
		if(lNewValue != ''){
			lNewValue += pSeparator;
		}
		lNewValue += lCurrentValue;
	}
	pJQueryObject.attr(pAttributeName, lNewValue);
}

function getIframeSelection(pIframeId){
	var lIframe = $('#' + pIframeId)[0];
	if(!lIframe)
		return false;
	var lSelection = rangy.getIframeSelection(lIframe);
	return lSelection;
}

function checkAllSubPhotos(pChkObj){
	if( $(pChkObj).is(':checked') ){
		$(pChkObj).closest('tr').find('.P-Figure-InsertOnly').find(':checkbox').each(function(){
			$(this).attr('checked', 'checked').change();
		});
	}else{
		$(pChkObj).closest('tr').find('.P-Figure-InsertOnly').find(':checkbox').each(function(){
			$(this).removeAttr('checked').change();
		});
	}
}

function checkSiblingsIsChecked(pChkObj){
	var checked = 0;
	if( $(pChkObj).is(':checked') ){
		$(pChkObj).closest('tr').find('.P-PopUp-Checkbox-Holder').find(':checkbox').attr('checked', 'checked').change();
	}else{
		$(pChkObj).closest('tr').find('.P-Figure-InsertOnly').find(':checkbox').each(function(){
			if($(this).is(':checked'))
				checked = 1;
		});
		if(!checked){
			$(pChkObj).closest('tr').find('.P-PopUp-Checkbox-Holder').find(':checkbox').removeAttr('checked').change();
		}
	}
}

/**
 * Връща 1я възел от ДОМ-а, който е след подадения възел.
 * Ако няма такъв възел 0 връша null;
 * @param pNode
 */
function getNextNode(pNode){
	while(pNode && !pNode.nextSibling){
		pNode = pNode.parentNode;
	}
	if(pNode)
		return pNode.nextSibling;
	return null;
}

function CancelNewReference(){
	var lNewReferenceId = $('form[name="new_reference_form"] input[name="instance_id"]').val();
	if(lNewReferenceId){
		executeAction(gActionRemoveInstanceWithoutContainerReload, lNewReferenceId, lNewReferenceId);
	}
	gActiveInstanceFormName = gDocumentFormName;
	gCurrentDialog.show();
	popUp(POPUP_OPERS.close, 'add-reference-popup', 'add-reference-popup');
}

function autoSaveDocument(pAutoSaveInterval){
	setTimeout("autoSaveInstance();autoSaveDocument("+pAutoSaveInterval+")", pAutoSaveInterval);
}

function autoSaveInstance(){
	lForm = $('form[name="' + gDocumentFormName + '"]');
	var lRootInstanceId = GetRootInstanceId();
	var lLevel = getInstanceLevel(lRootInstanceId);
	lForm.ajaxSubmit({
		'dataType' : 'json',
		'url' : gSaveInstanceSrv,
		'root_instance_id' : lRootInstanceId,
		'data' : {
			'real_instance_id' : lRootInstanceId,
			'root_instance_id' : lRootInstanceId,
			'level' : lLevel,
			'auto_save_on' : 1
		},
		'success' : function(pAjaxResult){
			if(pAjaxResult['err_cnt']){
				/*
				alert(pAjaxResult['err_msg']);
				if(pAjaxResult['validation_err_cnt']){//Ако има грешка при валидацията - показваме наново обекта с маркираните грешки
					$('#instance_wrapper_' + pInstanceId).replaceWith(pAjaxResult['instance_html']);
				}*/
			}else{
				//alert('saved');
			}
		}
	});
}

function PerformSingleFieldAutosave(pInstanceId, pFieldId){
	var lRootInstanceId = GetRootInstanceId();
	var lLevel = getInstanceLevel(lRootInstanceId);
	var lForm = $('form[name="' + gDocumentFormName + '"]');
	lForm.ajaxSubmit({
		'dataType' : 'json',
		'url' : gSaveInstanceSrv,
		'root_instance_id' : lRootInstanceId,
		'data' : {
			'real_instance_id' : pInstanceId,
			'root_instance_id' : lRootInstanceId,
			'document_id' : GetDocumentId(),
			'level' : lLevel,
			'explicit_field_id' : pFieldId,
			'auto_save_on' : 1
		},
		'success' : function(pAjaxResult){
			if(pAjaxResult['err_cnt']){

			}else{

			}
		}
	});

}

function autoSaveField(pField){
	var lPattern = new RegExp("^(\\d+)__(\\d+)","i"); // С това зимаме ид на инстанса и на филда

	$('#' + pField + ' :input').each(function(){
		$(this).bind('blur', function(){
			var lFieldName = $(this).attr('name');
			var lMatch = lPattern.exec(lFieldName);
			if(lMatch !== null){
				PerformSingleFieldAutosave(lMatch[1], lMatch[2]);
			}
		});
	});

	$('#' + pField + ' select').each(function(){
		$(this).bind('change', function(){
			var lFieldName = $(this).attr('name');
			var lMatch = lPattern.exec(lFieldName);
			if(lMatch !== null){
				PerformSingleFieldAutosave(lMatch[1], lMatch[2]);
			}
		});
	});

	$('#' + pField + ' :checkbox, :radio').each(function(){
		$(this).bind('click', function(){
			var lFieldName = $(this).attr('name');
			var lMatch = lPattern.exec(lFieldName);
			if(lMatch !== null){
				PerformSingleFieldAutosave(lMatch[1], lMatch[2]);
			}
		});
	});
}

function openEmailPopup( pAppendToClass ){
	$.ajax({
		url : gDocumentAuthorsSrv,
		dataType : 'json',
		data :{
			document_id : GetDocumentId(),
		},
		success : function(pAjaxResult){
			if(pAjaxResult['err_cnt']){
				alert(pAjaxResult['err_msg']);
				return;
			}else{
				$('.' + pAppendToClass).html(pAjaxResult['html']);
				popUp(POPUP_OPERS.open, 'compose-new-message', 'compose-new-message');
			}
		}
	});
}

function AddMailRecipients(pThis, pUsernameContainer, pTokenInputId) {
	var $this = $(pThis);

	if ($this.is(':checked')) {
		if(pThis.value) {
		var lUsername = $("#" + pUsernameContainer).html();
			$("#" + pTokenInputId).tokenInput("add", {id: pThis.value, name: lUsername});
		}

    } else {
		if(pThis.value) {
			var lUsername = $("#" + pUsernameContainer).html();
			$("#" + pTokenInputId).tokenInput("remove", {id: pThis.value, name: lUsername});
		}
    }
}

function toggleChecked(pHolder) {
	$("." + pHolder + " input").each( function() {
		$(this).attr("checked", true);
		$(this).trigger('onchange');
	});

}

function MarkInstanceRightActionsHolderAsNotInited(pInstanceId){
	$('#instance_right_actions_' + pInstanceId).attr('is_inited', 0);
}

function initInstanceRightActionsEvents(pInstanceId){
	$('#instance_wrapper_' + pInstanceId).hover(function(event){
		var lIsInited = $(this).attr('is_inited');
		if(!lIsInited){
			resizeInputByRightActions('instance_right_actions_' + pInstanceId);
			$(this).attr('is_inited', 1);
		}
		$(this).children('.P-Input-With-Background').children('.' + gInputRightActionsHolder).show();
	}, function(){
		$(this).children('.P-Input-With-Background').children('.' + gInputRightActionsHolder).hide();
	});
}

function resizeInputByRightActions( pRightActionsHolderId ){

	var lRightActions = $('#' + pRightActionsHolderId);
	var lActionsWidth = 0;

	lRightActions.find('div').each(function(){
		lActionsWidth += $(this).outerWidth();
	});

	if(lActionsWidth < 57)
		lActionsWidth = 57;

	var lActionsRightPos = lActionsWidth - 7;

	lRightActions.parent().parent().find('.'+gInputWrapHolder).css('margin-right', '-' + lActionsWidth + 'px');
	lRightActions.parent().parent().find('.'+gInputWrapHolder).css( 'padding-right', lActionsWidth + 'px');
	//~ lRightActions.parent().parent().find('.' + gInputRightActionsHolder).css( 'right', '-' + lActionsRightPos + 'px');
}

var gActivityFeedFooterHolder = 'P-Activity-Fieed-See-All-Recent-Activity';

function getNextActivityPage( pPageNum ){
	$.ajax({
		url : gActivitySrv,
		dataType : 'json',
		data :{
			p : pPageNum
		},
		success : function(pAjaxResult){
			if(!pAjaxResult){

			}else{
				$('.' + gActivityFeedFooterHolder).replaceWith(pAjaxResult['html']);
				resizeMiddleContainer();
			}
		}
	});
}

function displayDataPaperGeoCoverageCoodinatesFields(pInstanceId, pGlobalCoverageFieldHtmlIdentifier){
	var lFieldValue = getFormFieldValueByName(gDocumentFormName, pGlobalCoverageFieldHtmlIdentifier);
	if(lFieldValue.length > 0){
		$('#container_' + pInstanceId + '_456').hide();
		$('#container_' + pInstanceId + '_457').hide();
	}else{
		$('#container_' + pInstanceId + '_456').show();
		$('#container_' + pInstanceId + '_457').show();
	}
}

function displayDataPaperTemporalCoverageFields(pInstanceId, pTemporalCoverageTypeFieldHtmlIdentifier){
	var lFieldValue = getFormFieldValueByName(gDocumentFormName, pTemporalCoverageTypeFieldHtmlIdentifier);
	var lContainerIds = new Array(609, 610, 611, 612);
	var lTypes = {
		1 : 609,
		2 : 610,
		3 : 611,
		4 : 612
	};
	for(var i = 0; i < lContainerIds.length; ++i){
		var lContainerId = lContainerIds[i];
		if(lTypes[lFieldValue] != lContainerId){
			$('#container_' + pInstanceId + '_' + lContainerId).hide();
			$('#container_' + pInstanceId + '_' + lContainerId).find('input').val('');
		}

	}
	$('#container_' + pInstanceId + '_' + lTypes[lFieldValue]).show();
}

/**
 * Тук получаваме масива с избраните стойности
 * От него взимаме 1я елемент и връщаме неговия руут
 * @param pClassification
 * @returns {Number}
 */
function getTaxonClassificationRoot(pClassification){
	if(!pClassification || !pClassification.length)
		return 0;
	var lSelectedClassification = parseInt(pClassification[0]);
	if(!lSelectedClassification){
		return 0;
	}
	var lRoot = 0;
	$.ajax({
		url : gDocumentAjaxSrv,
		async: false,
		dataType : 'json',
		data :{
			action : 'get_classification_root',
			selected_value : lSelectedClassification
		},
		success : function(pAjaxResult){
			lRoot = pAjaxResult['root'];
		}
	});
	return lRoot;
}


function displayTaxonTreatmentTerrestrialTypeFields(pTreatmentInstanceId){
	var lClassification = getFormFieldValueByName(gActiveInstanceFormName, pTreatmentInstanceId + gInstanceFieldNameSeparator + 41);
	var lRank = getFormFieldValueByName(gActiveInstanceFormName, pTreatmentInstanceId + gInstanceFieldNameSeparator + 42);
	var lTreatmentType = getFormFieldValueByName(gActiveInstanceFormName, pTreatmentInstanceId + gInstanceFieldNameSeparator + 43);

	var lContainerId = 613;
	if(lRank == 2){
		//Genus treatment dont have all the following fields
		$('#container_' + pTreatmentInstanceId + '_' + 269).hide();
		$('#container_' + pTreatmentInstanceId + '_' + 613).hide();
		$('#container_' + pTreatmentInstanceId + '_' + 625).hide();
		$('#container_' + pTreatmentInstanceId + '_' + 614).hide();
	}else{
		$('#container_' + pTreatmentInstanceId + '_' + 269).show();
		$('#container_' + pTreatmentInstanceId + '_' + 625).show();
		$('#container_' + pTreatmentInstanceId + '_' + 614).show();
		if(lRank == 1 && lTreatmentType == 1){
			lClassificationRoot = getTaxonClassificationRoot(lClassification);
			if(lClassificationRoot == 7){
				$('#container_' + pTreatmentInstanceId + '_' + lContainerId).hide();
				var lRadioButtons = $('#container_' + pTreatmentInstanceId + '_' + lContainerId).find('input:radio');
				for(var i = 0; i < lRadioButtons.length; ++i){
					$(lRadioButtons[i]).prop('checked', false);
				}
				return;
			}
		}
		$('#container_' + pTreatmentInstanceId + '_' + lContainerId).show();
	}
}

function handleDataPaperResourcesDataSetCreation(pInstanceId){
	lDataSetsCount = getFormFieldValueByName(gDocumentFormName, pInstanceId + gInstanceFieldNameSeparator + 342);
	lCurrentDataSetsCount = getFormFieldValueByName(gDocumentFormName, pInstanceId + gInstanceFieldNameSeparator + 404);

	if(lCurrentDataSetsCount > lDataSetsCount){
		if(!confirm('Are you sure you want to delete data sets?')){
			//Връщаме старата стойност
			$('form[name="' + gDocumentFormName + '"] select[name="' + pInstanceId + gInstanceFieldNameSeparator + 342 + '"]').val(lCurrentDataSetsCount);
			return;
		}
	}
	executeAction(64, pInstanceId, lDataSetsCount);
}

function handleDataPaperDataSetColumnsCreation(pInstanceId){
	lColumnsCount = getFormFieldValueByName(gDocumentFormName, pInstanceId + gInstanceFieldNameSeparator + 400);
	lCurrentColumnsCount = getFormFieldValueByName(gDocumentFormName, pInstanceId + gInstanceFieldNameSeparator + 403);

	if(lCurrentColumnsCount > lColumnsCount){
		if(!confirm('Are you sure you want to delete columns?')){
			//Връщаме старата стойност
			$('form[name="' + gDocumentFormName + '"] select[name="' + pInstanceId + gInstanceFieldNameSeparator + 400 + '"]').val(lCurrentColumnsCount);
			return;
		}
	}
	executeAction(66, pInstanceId, lColumnsCount);
}
function ajaxSendMessage(pSubject, pRecipients, pEmailText) {
	var recipients = [];
	var email_text = $("#" + pEmailText).val();
	var email_subject = $("#" + pSubject).val();
	$("." + pRecipients).each(function(){
		recipients.push(this.value);
	});

	if(!email_text || !(recipients.length) || !email_subject) {
		alert('content is empty');
		return;
	} else {
		$.ajax({
			url : gSendEmailSrv,
			dataType : 'json',
			data :{
				recipients : recipients,
				email_body : email_text,
				subject : email_subject,
			},
			success : function(pAjaxResult){
				if(!pAjaxResult){
					alert('Error has occured!');
					return;
				}else{
					alert('Your message has been sent!');
					popUp(POPUP_OPERS.close, 'compose-new-message', 'compose-new-message');
				}
			}
		});

	}
}

function sortableMenu(pListId, pInstanceId) {
	$("#sortable_" + pListId + "_" + pInstanceId).sortable({
		axis: 'y',
		start: function(event, ui) {
			var start_pos_index = ui.item.index();
			ui.item.data('start_pos_index', start_pos_index);
		},
		update: function(event, ui) {
			var start_pos_index = ui.item.data('start_pos_index');
			var end_pos = $(ui.item).index();
			var end_pos_id = $(ui.item).attr("id");

			if(start_pos_index > end_pos) {
				if((start_pos_index - end_pos) == 1) {
					 //~ mestim 1 put nagore
					executeAction(1, end_pos_id);
					HandlePreviewModeMoveInstance();
				} else if((start_pos_index - end_pos) > 1) {
					for(var i = 0; i<start_pos_index - end_pos;i++ ) {
						//~ mestim 2 puti nagore
						executeAction(1, end_pos_id);
					}
					HandlePreviewModeMoveInstance();
				}
			} else {
				if((end_pos - start_pos_index) == 1) {
					//~ mestim 1 put nadolu
					executeAction(2, end_pos_id);
					HandlePreviewModeMoveInstance();
				} else if((end_pos - start_pos_index) > 1) {
					for(var i = 0; i<end_pos - start_pos_index;i++ ) {
						//~ mestim 2 puti nadolu
						executeAction(2, end_pos_id);
					}
					HandlePreviewModeMoveInstance();
				}
			}
		}
	});
}

function setFieldValueNullByInstanceId(pInstanceId, pFieldId) {
	if(pInstanceId && pFieldId) {
		$('#sel_' + pInstanceId + '__' + pFieldId).val('');
		$('#sel_' + pInstanceId + '__' + pFieldId).siblings( '.' + gSelectedOptionClass ).html( $('#sel_' + pInstanceId + '__' + pFieldId).find( "option:selected" ).text() );
	}
	return;
}

function getInstanceFieldCitations(pInstanceId, pFieldId, pCitationsType){
	var lResult = new Array();
	$.ajax({
		url : gCitationsSrv,
		dataType : 'json',
		async : false,
		data :{
			'instance_id' : pInstanceId,
			'field_id' : pFieldId,
			'citation_type' : pCitationsType,
			'action' : 'get_instance_field_citations'
		},
		success : function(pAjaxResult){
			lResult = pAjaxResult;
		}
	});
	return lResult;
}

function PerformCitationSave(pInstanceId, pFieldId, pCitationId, pCitationType, pCitationObjects, pCitationMode){
	var lResult = new Array();
	$.ajax({
		url : gCitationsSrv,
		dataType : 'json',
		async : false,
		data :{
			'instance_id' : pInstanceId,
			'field_id' : pFieldId,
			'citation_type' : pCitationType,
			'citation_id' : pCitationId,
			'citation_objects' : pCitationObjects,
			'citation_mode' : pCitationMode,
			'action' : 'save_citation'
		},
		success : function(pAjaxResult){
			lResult = pAjaxResult;
		}
	});
	if(!lResult['citation_id'] || !lResult['citation_id'] > 0 || lResult['err_cnt']){
		alert('Could not save citation: ' + lResult['err_msg']);
		return false;
	}
	//Ако сме направили insert не правим autosave, понеже елемента ни в дом-а още няма id атрибут и ще го затрием
	if(pCitationId > 0){
		autoSaveInstance();
	}
	return lResult;
}

function PerformRemoveCitation(pCitationId){
	$.ajax({
		url : gCitationsSrv,
		dataType : 'json',
		data :{
			'citation_id' : pCitationId,
			'action' : 'delete_citation'
		},success : function(pAjaxResult){
			autoSaveInstance();
		}
	});
}

function UploadFile(pBtnId, pDocId, pInstanceId, pFieldId) {
	var btnUpload = $('#' + pBtnId);
	var AjaxFileUpload = new AjaxUpload(btnUpload, {
		action: gFileUploadSrv,
		responseType: 'json',
		name: 'uploadfile',
		hoverClass: 'UploadHover',
		data: {
			document_id: pDocId,
		},
		onSubmit: function(file, ext){

		},
		onComplete: function(file, response){
			if(response['file_id'] && response['file_name']) {
				$('#field_' + pInstanceId + '__' + pFieldId).siblings('.' + gUploadFileNameHolderClass).html(response['file_name']);
				$('#field_' + pInstanceId + '__' + pFieldId).val(response['file_id']);
				UpdateInstanceFieldValue(pInstanceId, 222, response['file_id'], 1);
			}

		}
	});
}

function UpdateInstanceFieldValue(pInstanceId, pFieldId, pFieldValue, pFieldType) {
	$.ajax({
		url : gInstancesSrv,
		dataType: 'json',
		data :{
			action : 'update_instance_field_value',
			instance_id : pInstanceId,
			field_id : pFieldId,
			field_value : pFieldValue,
			field_type : pFieldType,
		},
	});
}

function toggleRadioCheck(pHolderId) {
	var lRadioIsDisabled = $('#' + pHolderId).is(':disabled');
	if(lRadioIsDisabled){
		return;
	}
	var radioChecked = $('#' + pHolderId).is(':checked');
	if(radioChecked){
		$('#' + pHolderId).attr('checked', 'false');
	} else {
		$('#' + pHolderId).attr('checked', 'true');
		$('#' + pHolderId).trigger('click');
	}
}

function getReferenceParentInstanceId(){
	var lResult = 0;
	$.ajax({
		url : gDocumentAjaxSrv,
		dataType: 'json',
		async: false,
		data :{
			action : 'get_reference_parent_instance_id',
			document_id : GetDocumentId(),
		},
		success : function(pAjaxResult){
			lResult = pAjaxResult['instance_id'];
		}
	});
	return lResult;
}

function getSupFilesParentInstanceId(){
	var lResult = 0;
	$.ajax({
		url : gDocumentAjaxSrv,
		dataType: 'json',
		async: false,
		data :{
			action : 'get_sup_files_parent_instance_id',
			document_id : GetDocumentId(),
		},
		success : function(pAjaxResult){
			lResult = pAjaxResult['instance_id'];
		}
	});
	return lResult;
}

function getFiguresParentInstanceId(){
	var lResult = 0;
	$.ajax({
		url : gDocumentAjaxSrv,
		dataType: 'json',
		async: false,
		data :{
			action : 'get_figures_parent_instance_id',
			document_id : GetDocumentId(),
		},
		success : function(pAjaxResult){
			lResult = pAjaxResult['instance_id'];
		}
	});
	return lResult;
}

function getTablesParentInstanceId(){
	var lResult = 0;
	$.ajax({
		url : gDocumentAjaxSrv,
		dataType: 'json',
		async: false,
		data :{
			action : 'get_tables_parent_instance_id',
			document_id : GetDocumentId(),
		},
		success : function(pAjaxResult){
			lResult = pAjaxResult['instance_id'];
		}
	});
	return lResult;
}

function showAddFigurePopUp() {
	gCurrentDialog = CKEDITOR.dialog.getCurrent();
	gCurrentDialog.hide();
	ChangeFiguresForm('image', GetDocumentId(), 'P-PopUp-Content-Inner', 0, 2);
	popUp(POPUP_OPERS.open, 'add-figure-popup', 'add-figure-popup');
}

function showTablePopUp() {
	gCurrentDialog = CKEDITOR.dialog.getCurrent();
	gCurrentDialog.hide();
	ShowAddTablePopup(GetDocumentId(), 'add-table-popup', 1);
}

function showAddReferencePopUp() {
	gCurrentDialog = CKEDITOR.dialog.getCurrent();
	gCurrentDialog.hide();
	CreateNewReferencePopup(1);
}

function savePreviewDocument(pDocumentId, pInstanceId){
	lForm = $('form[name="' + gDocumentFormName + '"]');
	var lRootInstanceId = GetRootInstanceId();
	var lLevel = getInstanceLevel(lRootInstanceId);
	lForm.ajaxSubmit({
		'dataType' : 'json',
		'async' : 'true',
		'url' : '/lib/ajax_srv/instance_save.php',
		'root_instance_id' : lRootInstanceId,
		'data' : {
			'real_instance_id' : lRootInstanceId,
			'root_instance_id' : lRootInstanceId,
			'level' : lLevel,
		},
		'success' : function(pAjaxResult){

			if(pAjaxResult['err_cnt']){
				/*
				alert(pAjaxResult['err_msg']);
				if(pAjaxResult['validation_err_cnt']){//Ако има грешка при валидацията - показваме наново обекта с маркираните грешки
					$('#instance_wrapper_' + pInstanceId).replaceWith(pAjaxResult['instance_html']);
				}*/
			}else{
				//alert('saved');
			}
		}
	});

	window.location='/preview.php?document_id=' + pDocumentId + '&instance_id=' + pInstanceId;
}

function initVideoLinkDetection(pHolderId, pIframeId, pDocumentId, pEdit){
	if($("#" + pHolderId).val() != ''){
		processVideoLinkDetection($("#" + pHolderId).val(), pIframeId, pEdit);
	}
	$("#" + pHolderId).live("input paste",function(e){
		if(this.value){
			var lValue = this.value;
			setTimeout(function() {
				processVideoLinkDetection(lValue, pIframeId, pEdit);
			}, 2000);
		}
	});
}

function processVideoLinkDetection(pValue, pIframeId, pEdit) {
	var lVideoId = getVideoId(pValue);
	if(lVideoId) {
		getYouTubeInfo(function(title){
			if(title && pEdit != 2)
				$("#video_title_textarea").val(title);
		}, lVideoId);

		document.getElementById(pIframeId).src = 'http://www.youtube.com/embed/' + lVideoId;
	} else {
		alert('The url you have pasted is not a valid youtube url!');
	}
}

// Get youtube video id
function getVideoId(url){
    if(url.indexOf('?') != -1 ) {
        var query = decodeURI(url).split('?')[1];
        var params = query.split('&');
        for(var i=0,l = params.length;i<l;i++)
            if(params[i].indexOf('v=') === 0)
                return params[i].replace('v=','');
    } else if (url.indexOf('youtu.be') != -1) {
        return decodeURI(url).split('youtu.be/')[1];
    }
    return null;
}

function SaveVideoData(pVideoId, pVideoUrl, pDocumentId, pVideoTitle) {
	SyncCKEditors();
	var pVideoUrl = $("#" + pVideoUrl).val();
	var pVideoTitle = $("#" + pVideoTitle).val();
	$.ajax({
		url : gFiguresAjaxSrv,
		dataType: 'json',
		data :{
			action : 'save_video_details',
			video_url : pVideoUrl,
			video_title : pVideoTitle,
			video_id : pVideoId,
			document_id : pDocumentId,
		},
		success : function(pAjaxResult){
			if(pAjaxResult) {
				//alert('The Video is saved!');
				//TODO: implement the alert as a 'yellow fade' http://37signals.com/svn/archives/000558.php
				UpdateDocumentFiguresHolder(pAjaxResult, 'P-Document-Figures-Container', 1);
				$('.P-PopUp-Content-Inner').html('');
			}
		}
	});
}

function getYouTubeInfo(handleData, pVideoId) {
	$.ajax({
		url: "https://gdata.youtube.com/feeds/api/videos/" + pVideoId + "?v=2&alt=json",
		dataType: "jsonp",
		async : false,
		success: function (data) {
			handleData(data.entry.title.$t);
		}
	});
}

function setDesignSelectValue(pHolderId, pThis) {
	var lValue = $("#" + pHolderId + " option[value='" + pThis.value + "']").text();
	if(lValue)
		$("#" + pHolderId).siblings( '.' + gSelectedOptionClass ).html( lValue );
}

function showRevision(pFrameId, pRevisionId, pDocumentId, pTemplateXSL){
	document.getElementById(pFrameId).setAttribute('src', '/preview_src.php?document_id=' + pDocumentId +
																	'&template_xsl_path=' + pTemplateXSL +
																	'&revision_id=' + pRevisionId +
																	'&show_revision=1');
}

function showLinkExample(pHtmlIdentifier, pInstanceId){
	var other = 4;
	var selected = $('#sel_' + pHtmlIdentifier + ' option:selected').val();
	$('#container_item_wrapper_' + pInstanceId + '_260_1_479').css('visibility' , selected == other ? 'visible' : 'hidden');
	$('#instance_wrapper_' + pInstanceId).find('div[id^="taxon_treatment_links_example_' + pInstanceId + '"]').hide();
	$('#instance_wrapper_' + pInstanceId).find($('#taxon_treatment_links_example_' + pInstanceId + '_' +  selected)).show();
}

function showHideChecklistTaxonFields(pInstanceId){
	var lSelectedVal = $('#sel_' + pInstanceId + '__414 option:selected').val();
	var lSelectFieldIdsByValue = new Array();
	lSelectFieldIdsByValue[1] = new Array([419]); // Kingdom
	lSelectFieldIdsByValue[2] = new Array([420]); // SubKingdom
	lSelectFieldIdsByValue[3] = new Array([421]); // phylum
	lSelectFieldIdsByValue[4] = new Array([422]); // subphylum
	lSelectFieldIdsByValue[5] = new Array([423]); // superclass
	lSelectFieldIdsByValue[6] = new Array([424]); // class
	lSelectFieldIdsByValue[7] = new Array([425]); // subclass
	lSelectFieldIdsByValue[8] = new Array([426]); // superorder
	lSelectFieldIdsByValue[9] = new Array([427]); // order
	lSelectFieldIdsByValue[10] = new Array([428]); // suborder
	lSelectFieldIdsByValue[11] = new Array([429]); // infraorder
	lSelectFieldIdsByValue[12] = new Array([430]); // superfamily
	lSelectFieldIdsByValue[13] = new Array([431]); // family
	lSelectFieldIdsByValue[14] = new Array([432]); // subfamily
	lSelectFieldIdsByValue[15] = new Array([433]); // tribe
	lSelectFieldIdsByValue[16] = new Array([434]); // subtribe
	lSelectFieldIdsByValue[17] = new Array([48]); // genus
	lSelectFieldIdsByValue[18] = new Array([417]); // subgenus
	lSelectFieldIdsByValue[19] = new Array(48, 417, 49); // species
	lSelectFieldIdsByValue[20] = new Array(48, 417, 49, 418); // subspecies
	lSelectFieldIdsByValue[21] = new Array(48, 417, 49, 435); // variety
	lSelectFieldIdsByValue[22] = new Array(48, 417, 49, 436); // form

	$('#container_' + pInstanceId + '_676').find('input').each(function(){
		$(this).closest('.fieldWrapper').hide();
	});
	for( var i = 0; i < lSelectFieldIdsByValue[lSelectedVal].length; i++ ) {
		$('#container_' + pInstanceId + '_676 input[name="' + pInstanceId + '__' + lSelectFieldIdsByValue[lSelectedVal][i] + '"]').closest('.fieldWrapper').show();
	}

}

function showHideChecklistLocalityTaxonFields(pInstanceId){
	var lSelectedVal = $('#sel_' + pInstanceId + '__414 option:selected').val();
	var lSelectFieldIdsByValue = new Array();
	lSelectFieldIdsByValue[1] = new Array([419]); // Kingdom
	lSelectFieldIdsByValue[2] = new Array([420]); // SubKingdom
	lSelectFieldIdsByValue[3] = new Array([421]); // phylum
	lSelectFieldIdsByValue[4] = new Array([422]); // subphylum
	lSelectFieldIdsByValue[5] = new Array([423]); // superclass
	lSelectFieldIdsByValue[6] = new Array([424]); // class
	lSelectFieldIdsByValue[7] = new Array([425]); // subclass
	lSelectFieldIdsByValue[8] = new Array([426]); // superorder
	lSelectFieldIdsByValue[9] = new Array([427]); // order
	lSelectFieldIdsByValue[10] = new Array([428]); // suborder
	lSelectFieldIdsByValue[11] = new Array([429]); // infraorder
	lSelectFieldIdsByValue[12] = new Array([430]); // superfamily
	lSelectFieldIdsByValue[13] = new Array([431]); // family
	lSelectFieldIdsByValue[14] = new Array([432]); // subfamily
	lSelectFieldIdsByValue[15] = new Array([433]); // tribe
	lSelectFieldIdsByValue[16] = new Array([434]); // subtribe
	lSelectFieldIdsByValue[17] = new Array([48]); // genus
	lSelectFieldIdsByValue[18] = new Array([417]); // subgenus
	lSelectFieldIdsByValue[19] = new Array(48, 417, 49); // species
	lSelectFieldIdsByValue[20] = new Array(48, 417, 49, 418); // subspecies
	lSelectFieldIdsByValue[21] = new Array(48, 417, 49, 435); // variety
	lSelectFieldIdsByValue[22] = new Array(48, 417, 49, 436); // form

	$('#container_' + pInstanceId + '_707').find('input').each(function(){
		$(this).closest('.fieldWrapper').hide();
	});
	for( var i = 0; i < lSelectFieldIdsByValue[lSelectedVal].length; i++ ) {
		$('#container_' + pInstanceId + '_707 input[name="' + pInstanceId + '__' + lSelectFieldIdsByValue[lSelectedVal][i] + '"]').closest('.fieldWrapper').show();
	}
}


function showHideChecklist2Taxon2Fields(pInstanceId){
	var genus = 48;
	var subgenus = 417;
	var species = 49;
	var subspecies = 418;
	var variety = 435;
	var form = 436;
	var authorship = 236;
	var translateRank = function(rg)
	{
		r = parseInt(rg);
		if (r == 17)
			return 48;
		if (r == 18)
			return 417;
		if (r == 19)
			return 49;
		if (r == 20)
			return 418;
		if (r == 21)
			return 435;
		if (r == 22)
			return 436;
		return r + 418;
	}
	var additionalFields = function(field)
	{
		//console.log(field);
		fields = [genus, subgenus, species];
		if (field == species)
			return fields;
		if (field == subspecies || field == variety || field == form)
		{
			fields.push(field);
			return fields;
		}
		else
		{
			return [field];
		}
	}

	var rank = $('#sel_' + pInstanceId + '__414 option:selected').val();
	var field = translateRank(rank);
	var visibleFields = additionalFields(field);
	visibleFields.push(authorship);
	var n = visibleFields.length;

	//hide
	for( var i = 417; i < 437; i++ ) {
		$('#field_wrapper_' + pInstanceId + '_' +  i).css('display', 'none');
	}
	$('#field_wrapper_' + pInstanceId + '_' +  48).css('display', 'none');
	$('#field_wrapper_' + pInstanceId + '_' +  49).css('display', 'none');

	for( var i = 0; i < n; i++ ) {
		$('#field_wrapper_' + pInstanceId + '_' +  visibleFields[i]).css('display', 'block');
	}
}

function showHideChecklistLocalityFields(pInstanceId){
	var lSelectedVal = $('#sel_' + pInstanceId + '__445 option:selected').val();
	var lSelectFieldIdsByValue = new Array();

	lSelectFieldIdsByValue[1] = new Array(109, 110, 111, 112, 113, 114, 115, 116, 117, 118, 119, 120, 121, 122, 123, 124, 125, 126, 129, 132, 133, 134); // Locality/Region
	lSelectFieldIdsByValue[2] = new Array(446, 447); // Habitat
	lSelectFieldIdsByValue[3] = new Array([448]); // Natura 2000 zone

	$('#container_' + pInstanceId + '_698').find('input').each(function(){
		$(this).closest('.fieldWrapper').hide();
	});
	for( var i = 0; i < lSelectFieldIdsByValue[lSelectedVal].length; i++ ) {
		$('#container_' + pInstanceId + '_698 input[name="' + pInstanceId + '__' + lSelectFieldIdsByValue[lSelectedVal][i] + '"]').closest('.fieldWrapper').show();
	}
}

function showHideChecklistLocality2Fields(pInstanceId){
	var lSelectedVal = $('#sel_' + pInstanceId + '__445 option:selected').val();
		// 1 -> Locality/Region
		// 2 -> Habitat
		// 3 -> Natura 2000 zone
	var containers = [0, 763, 765, 766];
	var n = containers.length;
	for( var i = 1; i < n; i++ ) {
		if (i == lSelectedVal)
			$('#container_' + pInstanceId + '_' + containers[i]).show();
		else
			$('#container_' + pInstanceId + '_' + containers[i]).hide();
	}
}

function processTaxonTreatmentRankChange(pInstanceId){
	var lRankFieldId = 42;
	var lTreatmentTypeFieldId = 43;
	var lSpeciesValueId = 1;
	var lRedescriptionValueId = 5;

	var rank = getFormFieldValueByName(gActiveInstanceFormName, pInstanceId + '__' + lRankFieldId );
	var redescription  = ((rank == lSpeciesValueId) ? 'Redescription or species observation' : 'Redescription');
	$('#sel_' + pInstanceId + '__' + lTreatmentTypeFieldId).children('option[value="' + lRedescriptionValueId + '"]').text(redescription);
}

function clearFieldsIfNotChecked(pInstanceId, pHtmlIdentifier){
	if(!$('#' + pHtmlIdentifier + '_1').is(':checked')){
		$('#container_' + pInstanceId + '_456').find('input').each(function(){
			$(this).val('');
		});
		$('#container_' + pInstanceId + '_457').find('input').each(function(){
			$(this).val('');
		});
	}
}

function UploadMaterialFile(pBtnId, pDocId, pInstanceId) {
	UploadImportFile(pBtnId, pDocId, pInstanceId, gFileUploadMaterialSrv);
}

function UploadChecklistTaxonFile(pBtnId, pDocId, pInstanceId) {
	UploadImportFile(pBtnId, pDocId, pInstanceId, gFileUploadChecklistTaxonSrv);
}

function UploadTaxonomicCoverageTaxaFile(pBtnId, pDocId, pInstanceId) {
	UploadImportFile(pBtnId, pDocId, pInstanceId, gFileUploadTaxonomicCoverageTaxaSrv);
}

function UploadImportFile(pBtnId, pDocId, pInstanceId, pAjaxUrl){
	var btnUpload = $('#' + pBtnId);
	var AjaxFileUpload = new AjaxUpload(btnUpload, {
		action: pAjaxUrl,
		responseType: 'json',
		name: 'uploadfile',
		hoverClass: 'UploadHover',
		data: {
			document_id: pDocId,
			instance_id: pInstanceId,
		},
		onSubmit: function(file, ext){
			$('#P-Ajax-Loading-Image').show();
			if (! (ext && /^(xls|ods|xlsx|)$/.test(ext))){
				// extension is not allowed
				alert('Only xls|ods|xlsx files are allowed');
				$('#P-Ajax-Loading-Image').hide();
				return false;
			}
		},
		onComplete: function(file, response){
			if(response['error']) {
				alert(response['error']);
			} else {
				window.location.href = 'display_document.php?instance_id=' + pInstanceId;
			}
			$('#P-Ajax-Loading-Image').hide();
		}
	});
}
function enableOrDisableJournalsByPaperType(pObj){
		var lPaperJournals = $(pObj).attr("journals");
		lPaperJournals = lPaperJournals.replace("{","");
		lPaperJournals = lPaperJournals.replace("}","");
		var lJournalIdsArray = lPaperJournals.split(',');
		$('#papertypes label').removeClass('boldedFont');
		$(pObj).next('label').addClass('boldedFont');

		$('#journals input').prop('checked', false);
		$('.journal').attr('disabled', 'disabled');
		$('#journals label').removeClass('enabled');
		$('#journals label').addClass('disabled');
		var inputsCount = $('#papertypes input').length;
		for(var i=0;i<lJournalIdsArray.length;i++){
			$('.journal[value=' + lJournalIdsArray[i] + ']').removeAttr('disabled');
			$('.journal[value=' + lJournalIdsArray[i] + ']').next('label').removeClass('disabled');
			$('.journal[value=' + lJournalIdsArray[i] + ']').next('label').addClass('enabled');
		}
		var lPaperDesc = $(pObj).attr("desc");
		showPaperTypeDescription(lPaperDesc);
		//~ select first active journal
		if ($('input[disabled*="disabled"]'))
			$('#journals input:first').prop('checked', true);
	}
function showActiveDataPapersForCurrentJournal(pObj){
	var lCurrObjValue = $(pObj).attr("value");
	var inputsCount = $('#papertypes input').length;
	var lTitlesArray = new Array();
	var lPapersJournal;
	for(var k=0;k<inputsCount;k++){
		lPapersJournal = $('#papertypes input').eq(k).attr('journals');
		lPapersJournal = lPapersJournal.replace("{","");
		lPapersJournal = lPapersJournal.replace("}","");
		lCurrElemJournalsArray = lPapersJournal.split(',');
		for(var i=0;i<lCurrElemJournalsArray.length;i++){
			if (lCurrObjValue == lCurrElemJournalsArray[i]){
				lTitlesArray[k] = '• ' + $('#papertypes input').eq(k).next('label').text();
			}
		}
	}
	var lTitlesArray = lTitlesArray.join('\n');
	$(pObj).next('label').attr('title', 'Accepts the following paper types: \n' + lTitlesArray);
}
function showPaperTypeDescription(pDescription){
	$('#docDescr').html(pDescription);
}
function leftColFullHeight(){
	$(function(){
		var lDocumentHeight = $(document).height();
		lDocumentHeight = lDocumentHeight - 81;
		$('.P-Wrapper-Container-Left').css('height', lDocumentHeight + 'px');
	});
}
function selectPrevInput(pObj){
	$(pObj).prev('input').trigger('click');
}
function cleanHTML(pHTML, pTableFlag) {

	//~ var reAllowedAttributes = /^(face|size|style|dir|color|id|class|alignment|align|valign|rowspan|colspan|width|height|background|cellspacing|cellpadding|border|href|src|target|alt|title)$/i
	//~ var reAllowedHTMLTags = /^(h1|h2|a|img|b|em|li|ol|p|pre|strong|ul|font|span|div|u|sub|sup|table|tbody|blockquote|tr|td)$/i

	// Set up regular expressions that will match the HTML tags and attributes that I want to allow
	if (pTableFlag) {
		var reAllowedAttributes = /^(href|title|target|alt|type|start|rowspan|colspan|cellspacing|cellpadding|citation_id)$/i
		var reAllowedHTMLTags = /^(p|strong|em|b|i|sup|sub|br|blockquote|a|ul|ol|li|table|tbody|tr|td|th|xref|fig-citation|tbls-citation|reference-citation)$/i
	} else {
		var reAllowedAttributes = /^(href|title|target|alt|type|start|citation_id)$/i
		var reAllowedHTMLTags = /^(p|strong|em|b|i|sup|sub|br|blockquote|a|ul|ol|li|xref|fig-citation|tbls-citation|reference-citation)$/i
	}

	// Start of with a test to match all HTML tags and a group for the tag name which we pass in as an extra parameter
	theHTML = pHTML.replace(/<[/]?([^> ]+)[^>]*>/g, function (match, HTMLTag) {
		// if the HTML tag does not match our list of allowed tags return empty string which will be used as a
		// a replacement for the pattern in our inital test.
		if (!reAllowedHTMLTags.test(HTMLTag)) {
			return "";
		} else {
			// The HTML tag is allowed so check attributes with the tag

			// Certain attributes are allowed so we do another replace statement looking for attributes and using another
			// function for the replacement value.
			match = match.replace(/ ([^=]+)="[^"]*"/g, function (match2, attributeName) {
				// If the attribute matches our list of allowed attributes we return the whole match string
				// so we replace our match with itself basically allowing the attribute.
				if (reAllowedAttributes.test(attributeName)) {
					return match2;
				} else {
					return ""; // not allowed so return blank string to wipe out the attribute value pair
				}
			});

		}
		return match;

	}); //end of the first replace

	//return our cleaned HTML
	return theHTML;
}

function showLoading(){
	if(gPerformingSave || gLoadingIsVisible){
		return;
	}
	gLoadingIsVisible = true;
	$('#' + gLoadingDivId).show();
}

function hideLoading(){
	if(gPerformingSave || !gLoadingIsVisible){
		return;
	}
	gLoadingIsVisible = false;
	$('#' + gLoadingDivId).hide();
}

function SubmitDocumentToPjs(pDocumentId, pRedirUrl) {
	$.ajax({
		url : gAjaxUrlsPrefix + 'update_pwt_document_xml.php',
		data : {
			document_id : pDocumentId
		},
		success : function(pAjaxResult){
			if(pAjaxResult == 'ok') {
				window.location = pRedirUrl;
			} else {
				hideLoading();
				alert('Error Submitting the document');
			}
		}
	});
}

function ScrollToInstance(pInstanceId){
	if(!$('#instance_wrapper_' + pInstanceId).length){
		return;
	}
	$('html, body').animate({
	    scrollTop: $('#instance_wrapper_' + pInstanceId).offset().top
	}, 2000);
}

function ShowHideProfileMenu() {
	$('.P-Head-Profile-Menu').click(function(){
		if($('#userLoggedMenu').is(':visible')) {
			$('#userLoggedMenu').hide();
			$('.P-Head-Profile-Menu').removeClass('P-Head-Profile-Menu-Opened');
		} else {
			$('#userLoggedMenu').show();
			$('.P-Head-Profile-Menu').addClass('P-Head-Profile-Menu-Opened');
		}
	});
}

function ShowDownloadMaterialsLink(pInstanceId) {
	$.ajax({
		url : gAjaxUrlsPrefix + 'materials_ajax_srv.php',
		data : {
			action : 'check_has_materials',
			instance_id : pInstanceId
		},
		success : function(pAjaxResult){
			if(pAjaxResult == 'ok') {
				$('.P-Taxon-Materials-DownLoadHolder').show();
			} else {
				$('.P-Taxon-Materials-DownLoadHolder').hide();
			}
		}
	});
}

function DownloadMaterialsAsCSV(pInstanceId) {
	document.location.href = '/lib/ajax_srv/csv_export_srv.php?action=export_materials_as_csv&instance_id=' + pInstanceId;
	return;
}

function LoadDocumentTree(pTreeHolderId, pDocumentId, pInstanceId){
//	return;
	$.ajax({
		url : gDocumentTreeAjaxSrv,
		dataType : 'json',
		data : {
			document_id : pDocumentId,
			instance_id : pInstanceId
		},
		success : function(pAjaxResult){
			$('#' + pTreeHolderId).html(pAjaxResult['html'])
		}
	});
}

function getQueryParams(qs) {
    qs = qs.split("+").join(" ");

    var params = {}, tokens,
        re = /[?&]?([^=]+)=([^&]*)/g;

    while (tokens = re.exec(qs)) {
        params[decodeURIComponent(tokens[1])]
            = decodeURIComponent(tokens[2]);
    }

    return params;
}

function ScrollToSelectedTreeElement(){
	var lHolderElement = $('.P-Article-Structures');
	var lSelectedElement = lHolderElement.find('.P-Article-Active');
	if(!lSelectedElement.length){
		return;
	}
	if(!lSelectedElement.is(':visible')){
		lSelectedElement = lSelectedElement.parents(':visible');
		if(lSelectedElement.length){
			lSelectedElement = lSelectedElement.first();
		}
	}
	if(!lSelectedElement.length){
		return;
	}
	var lSelectedElementParents = lSelectedElement.parents();
	var lOffsetParent = lSelectedElement.offsetParent();

	var lTopOffset = 0;
	var lCurrentElement = lSelectedElement.prev() ;
	if(!lCurrentElement.length){
		lCurrentElement = lSelectedElement.parent();
	}
	while(lCurrentElement.length && lCurrentElement[0] != lOffsetParent[0]){
//		console.log(lCurrentElement, lTopOffset);
		if(lSelectedElementParents.filter(lCurrentElement).length){
//			lTopOffset += lCurrentElement.outerHeight();
		}else{
			if(lCurrentElement.is(':visible')){
				lTopOffset += lCurrentElement.outerHeight();
			}
		}
		if(lCurrentElement.prev().length){
			lCurrentElement = lCurrentElement.prev();
		}else{
			lCurrentElement = lCurrentElement.parent();
		}
	}
	$('.P-Article-Structures').animate({
	    scrollTop: lTopOffset
	}, 2000);
}

function checkIfFunctionExists(pPossibleFunctionName) {
  return (typeof(pPossibleFunctionName) == typeof(Function));
}

/**
 * Returns the first text node which is following the specified node or false if
 * there is no such node
 *
 * @param $pNode DomNode
 */
function GetNextTextNode(pNode) {
	var lNextSibling = false;
	var lParent = pNode;
	while(lParent){
		lNextSibling = lParent.nextSibling;
		while(lNextSibling){
			if(lNextSibling.nodeType == 3)
				return lNextSibling;
			if(lNextSibling.nodeType == 1){
				var lTextNode = GetFirstTextNodeDescendant(lNextSibling);
				if(lTextNode)
					return lTextNode;
			}
			lNextSibling = lNextSibling.nextSibling;
		}
		lParent = lParent.parentNode;
	}
	return false;
}

/**
 * Returns the first text node which is a child of the passed node or false if
 * there is no such node
 * If the node is a text node itself - it will be returned
 *
 * @param $pNode DomNode
 */
function GetFirstTextNodeDescendant(pNode) {
	if(pNode.nodeType == 3){
		return pNode;
	}
	for(var i = 0; i < pNode.childNodes.length; ++i){
		var lChild = pNode.childNodes[i];
		if(lChild.nodeType == 3){
			return lChild;
		}
		if(lChild.nodeType == 1){
			var lChildFirstTextNode = GetFirstTextNodeDescendant(lChild);
			if(lChildFirstTextNode !== false){
				return lChildFirstTextNode;
			}
		}
	}
	return false;
}

/**
 * Returns the first text node which is preceding the specified node or false if
 * there is no such node
 *
 * @param $pNode DomNode
 */
function GetPreviousTextNode(pNode) {
	var lPreviuosSibling = false;
	var lParent = pNode;
	while(lParent){
		lPreviuosSibling = lParent.previousSibling;
		while(lPreviuosSibling){
			if(lPreviuosSibling.nodeType == 3)
				return lPreviuosSibling;
			if(lPreviuosSibling.nodeType == 1){
				var lTextNode = GetLastTextNodeDescendant(lPreviuosSibling);
				if(lTextNode)
					return lTextNode;
			}
			lPreviuosSibling = lPreviuosSibling.previousSibling;
		}
		lParent = lParent.parentNode;
	}
	return false;
}

/**
 * Returns the last text node which is a descendent of the passed node or false if
 * there is no such node
 * If the node is a text node itself - it will be returned
 *
 * @param $pNode DomNode
 */
function GetLastTextNodeDescendant(pNode) {
	if(pNode.nodeType == 3){
		return pNode;
	}
	for(var i = pNode.childNodes.length - 1; i >= 0; --i){
		var lChild = pNode.childNodes[i];
		if(lChild.nodeType == 3){
			return lChild;
		}
		if(lChild.nodeType == 1){
			var lChildLastTextNode = GetLastTextNodeDescendant(lChild);
			if(lChildLastTextNode !== false){
				return lChildLastTextNode;
			}
		}
	}
	return false;
}

var gIntervalVariable;
function SubmitDocumentAction(pUrl) {
	gIntervalVariable = setInterval(function(){checkAutosaveAndRedirect(pUrl)},300);
}

function checkAutosaveAndRedirect(pUrl) {
	if(!gAutoSaveFlag) {
		clearTimeout(gIntervalVariable);
		window.location = pUrl;
	}
}

function GetPreviewContent(){
	return $('#' + gPreviewIframeId).contents();
}

function GetPreviewSelection(){
	return getIframeSelection(gPreviewIframeId);
}
