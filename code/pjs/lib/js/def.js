var gFocus = 'JS-Focus';
var gInputHolder = 'P-Input-Holder';
var gActiveClass = 'P-Active';

var gAjaxUrlsPrefix = '/lib/ajax_srv/';
var gManageJournalAboutPagesPageUrl = '/manage_journal_about_pages.php';
var gEditPageUrl = '/edit.php';
var gEditGroupUrl = '/edit_journal_group.php';
var gJournalUsersPageUrl = '/manage_journal_users.php';
var gCreateUserPageUrl = '/create_user.php';
var gGetStoryChildrensPageUrl = gAjaxUrlsPrefix + 'get_story_childrens_srv.php';
var gGeneratePDFAjaxSrv = gAjaxUrlsPrefix + 'generate_pdf.php';
var gLeftContainerClass = "P-Wrapper-Container-Left";
var gLeftContainerClassHide = "P-Wrapper-Container-Left-Hide";
var gRightContainerClass = "P-Wrapper-Container-Right";
var gRightContainerClassHide = "P-Wrapper-Container-Right-Hide";
var gDocumentAjaxSrv = gAjaxUrlsPrefix + 'document_srv.php';
var gSubmissionNotestextareaID = '#ed_notes';
var gSubmissionNotesHolderOpen = '#collapse_smb_notes_open';
var gSubmissionNotesHolderClosed = '#collapse_smb_notes_closed';

var gMiddleContainerClass = "P-Article-Content";

var gWindowIsLoaded = false;


function getAjaxObject() {
	try{
		var xmlhttp = new XMLHttpRequest();
	}catch(err1){
		var ieXmlHttpVersions = new Array();
		ieXmlHttpVersions[ieXmlHttpVersions.length] = "MSXML2.XMLHttp.7.0";
		ieXmlHttpVersions[ieXmlHttpVersions.length] = "MSXML2.XMLHttp.6.0";
		ieXmlHttpVersions[ieXmlHttpVersions.length] = "MSXML2.XMLHttp.5.0";
		ieXmlHttpVersions[ieXmlHttpVersions.length] = "MSXML2.XMLHttp.4.0";
		ieXmlHttpVersions[ieXmlHttpVersions.length] = "MSXML2.XMLHttp.3.0";
		ieXmlHttpVersions[ieXmlHttpVersions.length] = "MSXML2.XMLHttp";
		ieXmlHttpVersions[ieXmlHttpVersions.length] = "Microsoft.XMLHttp";

		var i;
		for(i = 0; i < ieXmlHttpVersions.length; i++){
			try{
				var xmlhttp = new ActiveXObject(ieXmlHttpVersions[i]);
				break;
			}catch(err2){

			}
		}
	}
	return xmlhttp;
}

function AjaxLoad(link, elementid) {
	var element = document.getElementById(elementid);
	if(!element)
		return;
	var AjaxObject = getAjaxObject();
	if(typeof AjaxObject == "undefined"){
		alert('In order to view this page your browser has to support AJAX')
		return;
	}
	AjaxObject.open("GET", link, true);
	AjaxObject.send(null);
	AjaxObject.onreadystatechange = function() {
		if(AjaxObject.readyState == 4 && AjaxObject.status == 200){
			element.innerHTML = AjaxObject.responseText;
		}
	}
	return;
}

function reloadCaptcha() {
	var img = document.getElementById('cappic');
	img.src = '/lib/frmcaptcha.php?rld=' + Math.random();
	return false;
}

function rldContent(t, txt) {
	if(t.value == txt){
		t.value = '';
	}
}

function rldContent2(t, txt) {
	//console.log(t.value, txt);
	if(t.value == ''){
		t.value = txt;
	}
}

function CheckLoginForm(frm, uname, upass) {
	if(frm.uname.value == uname){
		frm.uname.value = '';
	}

	if(frm.upass.value == upass){
		frm.upass.value = '';
	}

	return true;

}

function pollsubmit(p, t, cid) {
	var http_request = getAjaxObject();
	if(!http_request)
		return true;

	disablepollbuttons(p);

	http_request.onreadystatechange = function() {
		poll_submit_callback(http_request, cid);
	};

	var qry = generatepollquery(p);

	var lmethod = 'GET';

	http_request.open(lmethod, '/lib/poll_submit.php?type=' + t + '&' + (lmethod == 'GET' ? qry : ''), true);
	if(lmethod == 'POST')
		http_request.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
	http_request.send(lmethod == 'GET' ? null : qry);

	return false;
}

function pollsubmitleft(p, t, cid) {
	var http_request = getAjaxObject();
	if(!http_request)
		return true;

	disablepollbuttons(p);

	http_request.onreadystatechange = function() {
		poll_submit_callback(http_request, cid);
	};

	var qry = generatepollquery(p);

	var lmethod = 'GET';

	http_request.open(lmethod, '/lib/poll_submit_left.php?type=' + t + '&' + (lmethod == 'GET' ? qry : ''), true);
	if(lmethod == 'POST')
		http_request.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
	http_request.send(lmethod == 'GET' ? null : qry);

	return false;
}

var lastsubmitbut = '';
var lastsubmitval = '';

function poll_btnclick(b) {
	lastsubmitbut = b.name;
	lastsubmitval = b.value;
	return true;
}

function generatepollquery(f) {
	var retstr = "";
	for( var i = 0; i < f.elements.length; i++){
		if(f.elements[i].type.toLowerCase() == 'text' || f.elements[i].type.toLowerCase() == 'textarea' || f.elements[i].type.toLowerCase() == 'hidden'){
			retstr += f.elements[i].name + "=" + escape(f.elements[i].value) + "&";
		}else if(f.elements[i].type.toLowerCase() == 'submit'){
			if(f.elements[i].name == lastsubmitbut && f.elements[i].value == lastsubmitval)
				retstr += f.elements[i].name + "=" + escape(f.elements[i].value) + "&";
		}else if(f.elements[i].type.toLowerCase() == 'select'){
			retstr += f.elements[i].name + "=" + escape(f.elements[i].options[f.elements[i].selectedIndex]) + "&";
		}else if(f.elements[i].type.toLowerCase() == 'radio' || f.elements[i].type.toLowerCase() == 'checkbox'){
			if(f.elements[i].checked)
				retstr += f.elements[i].name + "=" + escape(f.elements[i].value) + "&";
		}
	}
	return retstr;
}

function poll_submit_callback(p, cid) {
	if(p.readyState == 4 && p.status == 200){
		var canketa = document.getElementById(cid);
		canketa.innerHTML = p.responseText;
		return;
	}
}

function disablepollbuttons(p) {
	for(i = 0; i < p.elements.length; i++){
		if(p.elements[i].type.toLowerCase() == 'submit'){
			p.elements[i].disabled = true;
		}
	}
}

function resizeMainContent(pContentId, pCheckElementId, pLeftColId){
	document.getElementById(pContentId).style.minHeight = Math.max(
	document.getElementById(pCheckElementId).scrollHeight,
	document.getElementById(pLeftColId).scrollHeight) + "px";
}

function resizeMainContentHome(pContentId, pCheckElementId, pLeftColId){
	document.getElementById(pContentId).style.minHeight = Math.max(
	document.getElementById(pCheckElementId).scrollHeight,
	document.getElementById(pLeftColId).scrollHeight) + 30 + "px";
}

function changeFocus(pOper, pEl) {
	switch(pOper){
	case 1:
		$(pEl).closest('.' + gInputHolder).addClass(gFocus);
		$(pEl).attr('fldattr', 1);
		break;
	case 2:
		$(pEl).attr('fldattr', 0);
		$(pEl).closest('.' + gInputHolder).removeClass(gFocus);
		break;
	default:
		break;
	}
}

function DocumentAddSe(doc, usr, journal) {
  	if (!is_SE(usr, journal))
    {
    	$.modal("<div><h1>SimpleModal</h1></div>");
    }
    else
	return ExecuteSimpleDocumentAjaxRequest({
		document_id : doc,
		se_id : usr,
		action : 'add_document_se'
	});
}

function DocumentRemoveSe(pDocumentId, pSeId){
	return ExecuteSimpleDocumentAjaxRequest({
		document_id : pDocumentId,
		se_id : pSeId,
		action : 'remove_document_se'
	});
}

function DocumentAddLE(pDocumentId, pLeId){
	return ExecuteSimpleDocumentAjaxRequest({
		document_id : pDocumentId,
		le_id : pLeId,
		action : 'add_document_le'
	});
}

function DocumentRemoveLE(pDocumentId, pLeId){
	return ExecuteSimpleDocumentAjaxRequest({
		document_id : pDocumentId,
		le_id : pLeId,
		action : 'remove_document_le'
	});
}

function DocumentAddCE(pDocumentId, pCeId, pCurrentRoundID){
	return ExecuteSimpleDocumentAjaxRequest({
		document_id : pDocumentId,
		ce_id : pCeId,
		current_round_id : pCurrentRoundID,
		action : 'add_document_ce'
	});
}

function DocumentRemoveCE(pDocumentId, pCeId){
	return ExecuteSimpleDocumentAjaxRequest({
		document_id : pDocumentId,
		ce_id : pCeId,
		action : 'remove_document_ce'
	});
}

function DocumentInviteReviewers(pDocumentId, pRole, pErrText){
	var f = function(val, i){ return parseInt(val['name']); };
	var n = $.map(  $('.reviewer_tbl').find('input[value=n]').serializeArray(), f );
	var p = $.map(  $('.reviewer_tbl').find('input[value=p]').serializeArray(), f );
	if (n.length + p.length > 0)
	{
		ExecuteSimpleDocumentAjaxRequest({
			doc_id : pDocumentId,
			n: n,
			p: p,
			role: pRole,
			action : 'inviteReviewers'
		})
	} else {
		alert(pErrText);
	}
}

function SEConfirmReviewerInvitation(pDocumentId, pInvitationId, pReviewerId, pConfirmationText){
	if(confirm(pConfirmationText)){
		return ExecuteSimpleDocumentAjaxRequest({
			invitation_id : pInvitationId,
			reviewer_id : pReviewerId,
			document_id : pDocumentId,
			action : 'se_confirm_reviewer_invitation'
		});
	}
}

function SECancelReviewerInvitation(pDocumentId, pInvitationId, pReviewerId, pConfirmationText){
	if(confirm(pConfirmationText)){
		return ExecuteSimpleDocumentAjaxRequest({
			invitation_id : pInvitationId,
			reviewer_id : pReviewerId,
			document_id : pDocumentId,
			action : 'se_cancel_reviewer_invitation'
		});
	}
}

function ConfirmReviewerInvitation(pDocumentId, pInvitationId){
	return ExecuteSimpleDocumentAjaxRequest({
		invitation_id : pInvitationId,
		document_id : pDocumentId,
		action : 'confirm_reviewer_invitation'
	});
}

function CancelReviewerInvitation(pDocumentId, pInvitationId){
	return ExecuteSimpleDocumentAjaxRequest({
		invitation_id : pInvitationId,
		document_id : pDocumentId,
		action : 'cancel_reviewer_invitation'
	});
}

function SaveReviewerDecision(pRoundUserId, pDecisionId){
	return ExecuteSimpleDocumentAjaxRequest({
		round_user_id : pRoundUserId,
		decision_id : pDecisionId,
		decision_notes : $('#decision_notes').val(),
		action : 'save_reviewer_decision'
	});
}

function SaveEditorDecision(pRoundUserId, pDecisionId, pDocumentId){
	return ExecuteSimpleDocumentAjaxRequest({
		round_user_id : pRoundUserId,
		decision_id : pDecisionId,
		document_id : pDocumentId,
		decision_notes : $('#ed_notes_reject').val(),
		action : 'save_editor_decision'
	});
}

function SaveSERejectDecision(pRoundUserId, pDecisionId, pDocumentId){
	return ExecuteSimpleDocumentAjaxRequest({
		round_user_id : pRoundUserId,
		decision_id : pDecisionId,
		document_id : pDocumentId,
		decision_notes : $('#ed_notes_reject').val(),
		action : 'save_serej_decision'
	});
}

function SaveSEDecision(pRoundUserId, pDecisionId, pDocumentId){
	return ExecuteSimpleDocumentAjaxRequest({
		round_user_id : pRoundUserId,
		decision_id : pDecisionId,
		document_id: pDocumentId,
		decision_notes : $('#decision_notes').val(),
		action : 'save_se_decision'
	});
}

function SaveESENotes(pNotesId, pDocument_id){
	return ExecuteSimpleDocumentAjaxRequest({
		document_id : pDocument_id,
		editor_notes : $('#' + pNotesId).val(),
		action : 'save_editor_notes'
	});
}

function SaveLENotes(pNotesId, pDocument_id){
	return ExecuteSimpleDocumentAjaxRequest({
		document_id : pDocument_id,
		editor_notes : $('#' + pNotesId).val(),
		action : 'save_le_notes'
	});
}

function SaveLEDecision(pRoundUserId, pDecisionId){
	return ExecuteSimpleDocumentAjaxRequest({
		round_user_id : pRoundUserId,
		decision_id : pDecisionId,
		decision_notes : $('#decision_notes').val(),
		action : 'save_le_decision'
	});
}

function SaveLEXMLVersion(pDocumentId, pXmlVersionHolder){
	$('#P-Ajax-Loading-Image-Main').show();
	
	$.ajax({
		url : lDocumentsAjaxSrv,
		async : true,
		dataType : 'json',
		data : {
			document_id : pDocumentId,
			doc_xml : $(pXmlVersionHolder).val(),
			action : 'save_le_xml_version'
		},
		type : 'POST',
		success : function(pAjaxResult) {
			$('#P-Ajax-Loading-Image-Main').hide();
			if(pAjaxResult['err_cnt']){
				for (var i=0; i < pAjaxResult['err_cnt']; i++) {
				  alert(pAjaxResult['err_msgs'][i]['err_msg']);
				};
			}
		}
	});
	
}

function RevertLEXMLVersion(pDocumentId, pXmlVersionHolder){
	$('#P-Ajax-Loading-Image-Main').show();
	
	$.ajax({
		url : lDocumentsAjaxSrv,
		async : true,
		dataType : 'json',
		data : {
			document_id : pDocumentId,
			action : 'revert_le_xml_version'
		},
		type : 'POST',
		success : function(pAjaxResult) {
			$('#P-Ajax-Loading-Image-Main').hide();
			if(pAjaxResult['err_cnt']){
				for (var i=0; i < pAjaxResult['err_cnt']; i++) {
				  alert(pAjaxResult['err_msgs'][i]['err_msg']);
				};
			}else{
				$(pXmlVersionHolder).val(pAjaxResult['doc_xml']);
			}
		}
	});
	
}

function SaveCEDecision(pRoundUserId, pDecisionId){
	return ExecuteSimpleDocumentAjaxRequest({
		round_user_id : pRoundUserId,
		decision_id : pDecisionId,
		decision_notes : $('#decision_notes').val(),
		action : 'save_ce_decision'
	});
}

function EditorProceedDocumentToLayout(pDocumentId){
	return ExecuteSimpleDocumentAjaxRequest({
		document_id : pDocumentId,
		action : 'editor_proceed_document_to_layout'
	});
}

function SaveAuthorLayoutDecision(pDocumentId, pDecisionId){
	return ExecuteSimpleDocumentAjaxRequest({
		document_id : pDocumentId,
		decision_id : pDecisionId,
		action : 'save_author_layout_decision'
	});
}

function SubmitAuthorVersionForReview(pDocumentId){
	return ExecuteSimpleDocumentAjaxRequest({
		document_id : pDocumentId,
		action : 'submit_author_version_for_review'
	});
}

function ProceedDocumentToLayoutEditing(pDocumentId){
	return ExecuteSimpleDocumentAjaxRequest({
		document_id : pDocumentId,
		action : 'proceed_document_to_layout_editing'
	});
}

function ProceedDocumentToCopyEditing(pDocumentId){
	return ExecuteSimpleDocumentAjaxRequest({
		document_id : pDocumentId,
		action : 'proceed_document_to_copy_editing'
	});
}

function RemoveDocumentReviewer(pReviewUsrId){
	return ExecuteSimpleDocumentAjaxRequest({
		reviewer_id : pReviewUsrId,
		action : 'remove_reviewer'
	});
}

function ReInviteDocumentReviewer(pDocumentId, pReviewUsrId, pRoundId, pConfirmationText){
	if(confirm(pConfirmationText)){
		return ExecuteSimpleDocumentAjaxRequest({
			reviewer_id : pReviewUsrId,
			round_id : pRoundId,
			document_id : pDocumentId,
			action : 'reinvite_reviewer'
		});	
	}
}

/* Показване на формата за регистрация */
function LayerRegFrm(pElem, pOper, pReload) {
	if(pOper == 1 || !pOper){
		$.ajax({
			url : '/register.php',
			async : false,
			// ~ data :lDataForAjax,
			success : function(pAjaxResult) {
				$('#' + pElem).html(pAjaxResult);

				if(!pReload){
					$('#' + pElem).modal({
						autoResize : true,
						position : ["10%", ],
						minHeight : 430,
						maxHeight : 600,
						overlayClose : true,
						onShow : function(dialog) {
							var doch = $(window).height();
							if(doch <= 430){
								var calh = doch - 2 * 80;
								$('#simplemodal-container').height(calh);
							}
						}
					});
					if($('#regstep').val()){
						var doch = $(window).height();
						var calh = doch - 2 * 80;
						$('#simplemodal-container').height(calh);
					}
				}
			}
		});
	}
}
function changeFocus(pOper, pEl) {
	switch(pOper){
	case 1:
		$(pEl).closest('.' + gInputHolder).addClass(gFocus);
		$(pEl).attr('fldattr', 1);
		break;
	case 2:
		$(pEl).attr('fldattr', 0);
		$(pEl).closest('.' + gInputHolder).removeClass(gFocus);
		break;
	default:
		break;
	}
}

/*
 * За дизайн на селектите pOper: 0 - НЯМА Ajax 1 - ИМА Ajax ( ако при смяна на
 * селекта той се замества с идентичен с AJAX трябва да се инитне наново )
 */
designSelect = function(pSelectId, pOper) {
	this.lSelect = $('#' + pSelectId);
	this.init(pOper);
};
designSelect.prototype.init = function(pOper) {
	var lThis = this;
	lThis.lSelect.siblings('.' + gSelectedOptionClass).html(lThis.lSelect.find("option:selected").text());
	if(!pOper){
		lThis.lSelect.bind('change', function() {
			lThis.init(1);
		});
	}
};
/* Събмитване на формата за регистрация */
function SubmitRegForm(pElem, pFormName, pOper, pStep, pCloseFlag) {
	var lFormData = $('form[name="' + pFormName + '"]').formSerialize();
	if(pStep){
		lFormData += '&step=' + pStep;
	}
	if(pOper){
		if(pOper == 1){
			lFormData += '&tAction=register';
		}
		if(pOper == 2){
			lFormData += '&tAction=showedit';
		}
		$.ajax({
			url : '/register.php',
			type : 'POST',
			data : lFormData,
			success : function(pAjaxResult) {
				$('#' + pElem).html(pAjaxResult);
				if($('.errorHolder').length == 0 && pCloseFlag){
					$.modal.close();
				}
				if($('#regstep').val()){
					var doch = $(window).height();
					var calh = doch - 2 * 80;
					$('#simplemodal-container').height(calh);
				}else{
					var calh = 430;
					// Calculate height
					$('#simplemodal-container').height(calh);
				}
				// Scroll to top
				$('.simplemodal-wrap').animate({
					scrollTop : 0
				}, 0);
			}
		});
	}
	return false;
}

/* Показване на формата за edit на профил за съответната стъпка */
function LayerProfEditFrm(pElem, pOper, pStep) {
	if(pOper == 2){
		$.ajax({
			url : '/register.php',
			async : false,
			data : {
				step : pStep,
				showedit : 1,
				tAction : 'showedit'
			},
			success : function(pAjaxResult) {
				$('#' + pElem).html(pAjaxResult);
				$('#' + pElem).modal({
					autoResize : true,
					position : ["10%", ],
					minHeight : 430,
					maxHeight : 600,
					overlayClose : true,
					onShow : function(dialog) {
						var doch = $(window).height();
						if(doch <= 430){
							var calh = doch - 2 * 80;
							$('#simplemodal-container').height(calh);
						}
					}
				});
				if(pStep != 1){
					var doch = $(window).height();
					var calh = doch - 2 * 80;
					$('#simplemodal-container').height(calh);
				}
			}
		});
	}
}
/* add error field class */
function addErrorClass(pEl, pClass, pValidClass, pReqClass) {
	if(pValidClass){
		$('#' + pEl).closest('div').removeClass(pValidClass);
	}
	if(pReqClass){
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
function initPwtDocumentStep2Permissions(pFormName) {
	var lRadioBtns = {
		'agree_to_cover_all_taxes' : {
			'btns_to_enable' : [],
			'btns_to_disable' : ['fifteen_discount_reasons[]', 'ten_discount_reasons[]', 'waiver_discount_reasons[]'],
		},
		'want_15_discount' : {
			'btns_to_enable' : ['fifteen_discount_reasons[]'],
			'btns_to_disable' : ['ten_discount_reasons[]', 'waiver_discount_reasons[]'],
		},
		'want_10_discount' : {
			'btns_to_enable' : ['ten_discount_reasons[]'],
			'btns_to_disable' : ['fifteen_discount_reasons[]', 'waiver_discount_reasons[]'],
		},
		'want_waiver_discount' : {
			'btns_to_enable' : ['waiver_discount_reasons[]', ],
			'btns_to_disable' : ['fifteen_discount_reasons[]', 'ten_discount_reasons[]'],
		},
		'use_special_conditions' : {
			'btns_to_enable' : [],
			'btns_to_disable' : ['fifteen_discount_reasons[]', 'ten_discount_reasons[]', 'waiver_discount_reasons[]'],
		}
	};
	var lCheckboxes = {
		'fifteen_discount_reasons[]' : 'want_15_discount',
		'ten_discount_reasons[]' : 'want_10_discount',
		'waiver_discount_reasons[]' : 'want_waiver_discount'
	};
	var lCheckboxBindFunction = function(pCheckboxName){
		$("form[name='" + pFormName + "'] input:checkbox[name='" + pCheckboxName + "']").bind('click', function(){
			if(!$(this).prop('checked')){
				return;
			}
			$("form[name='" + pFormName + "'] input:radio[name='" + lCheckboxes[pCheckboxName] + "']").prop('checked', 'checked');
			lRadioBindInnerFunction(lCheckboxes[pCheckboxName]);
		});
	};
	var lRadioBindInnerFunction = function(pRadioBtnName){
		if(!$("form[name='" + pFormName + "'] input:radio[name='" + pRadioBtnName + "']").attr('checked')){
			return;
		}
		for( var j in lRadioBtns){
			if(pRadioBtnName == j){
				continue;
			}
			$("form[name='" + pFormName + "'] input:radio[name='" + j + "']").prop('checked', false);
		}
		for( var j = 0; j < lRadioBtns[pRadioBtnName]['btns_to_enable'].length; ++j){
			$("form[name='" + pFormName + "'] input:checkbox[name='" + lRadioBtns[pRadioBtnName]['btns_to_enable'][j] + "']").prop('disabled', false);
		}
		for( var j = 0; j < lRadioBtns[pRadioBtnName]['btns_to_disable'].length; ++j){
			$("form[name='" + pFormName + "'] input:checkbox[name='" + lRadioBtns[pRadioBtnName]['btns_to_disable'][j] + "']").prop('checked', false);
			$("form[name='" + pFormName + "'] input:checkbox[name='" + lRadioBtns[pRadioBtnName]['btns_to_disable'][j] + "']").prop('disabled', true);
		}
	};
	var lRadioBindFunction = function(pRadioBtnName){
		$("form[name='" + pFormName + "'] input:radio[name='" + pRadioBtnName + "']").bind('change', function(){
			lRadioBindInnerFunction(pRadioBtnName);
		});
	};
	for( var i in lRadioBtns){
		lRadioBindFunction(i);
		lRadioBindInnerFunction(i);
	}
	for( var i in lCheckboxes){
		lCheckboxBindFunction(i);
	}
}
var lDocumentsAjaxSrv = '/lib/ajax_srv/document_srv.php';
function ExecuteSimpleDocumentAjaxRequest(pDataToPass, pAsync, pType){
	$.ajax({
		url : lDocumentsAjaxSrv,
		async : pAsync ? pAsync : false,
		dataType : 'json',
		data : pDataToPass,
		type : pType ? pType : 'GET',
		success : function(pAjaxResult) {
			if(pAjaxResult['err_cnt']){
				for (var i=0; i < pAjaxResult['err_cnt']; i++) {
				  alert(pAjaxResult['err_msgs'][i]['err_msg']);
				};
			}else{
				if(pAjaxResult['dont_redirect']) {
					
				} else {
					if(pAjaxResult['url_params']) {
						var lUrl = document.URL;
						window.location = lUrl + (lUrl.split('?')[1] ? '&':'?') + pAjaxResult['url_params'];
					} else {
						window.location.reload();
					}
				}
			}
		}
	});
}
function ExecuteBooleanAjaxRequest(pURL ,pDataToPass, pAsync){
	var a = $.ajax({
		url : pURL,
		async : pAsync ? pAsync : false,
		dataType: 'json',
		data : pDataToPass,
		success : function(pAjaxResult) {
			if(pAjaxResult['err_cnt']){
				alert('Error occurred');
			}
		}
	});
	return a['responseText'];
}
function is_SE(usr, journal) {
	var a = ExecuteBooleanAjaxRequest('/lib/ajax_srv/usr_autocomplete_srv.php',{action: 'is_se', usr: usr, journal: journal} );
	//console.log(a);
	return a === 'true';
}
function Make_SE(usr, journal, expertises)
{
	return;
	/*return ExecuteAjaxRequest({
		document_id : pDocumentId,
		se_id : usr,
		action : 'make_se'
	});
	*/
}
function submitProfileForm(pFormName, pStep){
    var step   = document.createElement("input");
    var action = document.createElement("input");
    var editprofile = document.createElement("input");
    step.setAttribute("type", "hidden");
    step.setAttribute("value", pStep);
    step.setAttribute("name", "step");
    action.setAttribute("type", "hidden");
    action.setAttribute("value", "register");
    action.setAttribute("name", "tAction");
    document.forms[pFormName].appendChild(step);
    document.forms[pFormName].appendChild(action);
	document.forms[pFormName].submit();
}
function submitUserJournalExpertisesForm(pFormName){
	var action = document.createElement("input");
	action.setAttribute("type", "hidden");
    action.setAttribute("value", "save");
    action.setAttribute("name", "tAction");
	document.forms[pFormName].appendChild(action);
	document.forms[pFormName].submit();
}
function deleteStoryAjax(pObj, pJournalId, pStoryId){
	if (confirm('Do you want to delete this story?')){
		$.ajax({
			url : gEditPageUrl,
			dataType : 'json',
			async : false,
			data :{
				journal_id : pJournalId,
				guid	   : pStoryId,
				tAction    : 'delete'
			},
			success : function(pAjaxResult){
				if(pAjaxResult){
					window.location.href='/manage_journal_about_pages.php?journal_id='+pJournalId;
				}
			}
		});
	}else{
		return false;
	}
}
function moveStoryAjax(pObj, pJournalId, pStoryId, pDirection){
	$.ajax({
		url : gEditPageUrl,
		dataType : 'json',
		async : false,
		data :{
			journal_id : pJournalId,
			direction  : pDirection,
			guid	   : pStoryId,
			tAction    : 'moveupdown'
		},
		success : function(pAjaxResult){
			if(pAjaxResult){
				window.location.href='/manage_journal_about_pages.php?journal_id='+pJournalId;
			}
		}
	});
}
function ChangeActiveTab(pEl, pActiveClassName, pResElement, pResElementsClass) {
	$('.' + pActiveClassName).removeClass(pActiveClassName);
	$(pEl).addClass(pActiveClassName);
	$('.' + pResElementsClass).hide();
	$('#' + pResElement).show();
}
function SubmitLetterFilter(pLetter, pForm, pInput) {
	$('#' + pInput).val(pLetter);
	$('#' + pForm).submit();
}
function getStoryChildrens(pObj, pStoryId, pJournalId){
	$.ajax({
		url : gGetStoryChildrensPageUrl,
		dataType : 'json',
		async : false,
		data :{
			journal_id : pJournalId,
			storyid	   : pStoryId
		},
		success : function(pAjaxResult){
			if(pAjaxResult){
				$(pObj).removeAttr('onclick');
				$(pAjaxResult['html']).insertAfter($(pObj).closest('div.link'));
			}
		}
	});
}
function goToIssue(pJournalId, pInputId){
	window.location.href = '/browse_journal_issue_documents.php?journal_id=' + pJournalId + '&issue_num=' + $('#' + pInputId).val();
}
function confirmDelete(pMsg, pLink){
	if(confirm(pMsg))
		window.location.href=pLink;
	else
		return false;
}

function updateUserRoles(pObj, pJournalId, pUid){
	var lCountChecked = 0;
	var lGo = 1;
	lCountChecked = lCountChecked +
					( $('#jm_' + pUid).is(':checked') ? 1 : 0 ) +
					( $('#e_' + pUid).is(':checked')  ? 1 : 0 ) +
					( $('#se_' + pUid).is(':checked') ? 1 : 0 ) +
					( $('#le_' + pUid).is(':checked') ? 1 : 0 ) +
					( $('#ce_' + pUid).is(':checked') ? 1 : 0 ) +
					( $('#r_' + pUid).is(':checked') ? 1 : 0 ) +
					( $('#a_' + pUid).is(':checked') ? 1 : 0 );
	if(!lCountChecked){
		lGo = 0;
		if(confirm('User will be removed from journal because there is no role'))
			lGo = 1;
	}
	if(lGo){
		$.ajax({
			url : gJournalUsersPageUrl,
			dataType : 'json',
			//async : false,
			data :{
				journal_id : pJournalId,
				update_roles : 1,
				user_id : pUid,
				jm : $('#jm_' + pUid).is(':checked') ? 1 : 0,
				e  : $('#e_' + pUid).is(':checked')  ? 1 : 0,
				se : $('#se_' + pUid).is(':checked') ? 1 : 0,
				le : $('#le_' + pUid).is(':checked') ? 1 : 0,
				ce : $('#ce_' + pUid).is(':checked') ? 1 : 0
			},
			success : function(pAjaxResult){
				if(pAjaxResult['result']){
					if($('#se_' + pUid).is(':checked')){
						$('#expertise_' + pUid).html('<a href="/user_journal_expertises.php?journal_id=' + pJournalId + '&amp;user_id=' + pUid + '&amp;tAction=showedit">Expertises</a>');
					}else{
						$('#expertise_' + pUid).html('');
					}
					if(!lCountChecked)
						$(pObj).closest('tr').remove();
					alert('User roles has been updated successfully.');
				}
			}
		});
	}
}
// , pMode, pDocumentId, pRoundId
function checkIfUserExist(pObj, pJournalId, pMode, pDocumentId, pRoundId, pRole){
	$.ajax({
		url : gCreateUserPageUrl,
		dataType : 'json',
		//async : false,
		data :{
			ajax 		: 1,
			journal_id 	: pJournalId,
			mode		: pMode,
			document_id	: pDocumentId,
			round_id	: pRoundId,
			role		: pRole,
			tAction 	: 'showedit',
			email 		: $(pObj).val()
		},
		success : function(pAjaxResult){
			//~ $('#user_roles_checkbox input').each(function(){
				//~ var lSE = $(this).attr('value') == '3';
				//~ if(lSE == true)
					//~ $('#categories_holder').css('display', 'block');
					//~ // lSE.trigger('click');
					//~ // alert(lSE);
					//~ $(this).click();
				//~ // $(this).trigger('click');
			//~ });
			//~ lRole = $('#user_roles_checkbox input:nth-child(1)').val();
			if(pAjaxResult){
				$('#dashboard-content').html(pAjaxResult['html']);
			}
		}
	});
}
function showCategoriesIfChecked(pCheckboxesHolderId, pCategoriesHolderId){
	$('#' + pCheckboxesHolderId).find('input').each(function(){
		if($(this).val() == 3){
			if($(this).is(':checked'))
				$('#' + pCategoriesHolderId).show();
			else
				$('#' + pCategoriesHolderId).hide();
		}
	});
}
function enableAllInputs(pFormName){
	$('form[name="' + pFormName + '"]').find('input').each(function(){
		$(this).removeAttr('disabled');
	});
}
function filterIssues(pObj, pJournalId, pYearInpId){
	var lChecked = 0;
	if($(pObj).is(':checked'))
		lChecked = 1;
	window.location.href='/browse_journal_issues.php?journal_id=' + pJournalId + '&special_issues=' + lChecked + '&year=' + $('#' + pYearInpId).val();
}
function toggleBlock(pArrowId, pTreeHolderId){
	if($('#' + pTreeHolderId).is(':hidden')){
		$('#' + pArrowId).removeClass('blockDownArrow').addClass('blockUpArrow');
		$('#' + pTreeHolderId).show();
	}else{
		$('#' + pArrowId).removeClass('blockUpArrow').addClass('blockDownArrow');
		$('#' + pTreeHolderId).hide();
	}
}
function filterAuthors(pAffiliationInpId, pJournalId, pInputLetterId){
	window.location.href = '/browse_journal_authors.php?journal_id=' + pJournalId +
							'&affiliation=' + document.getElementById(pAffiliationInpId).value +
							'&author_letter=' + document.getElementById(pInputLetterId).value;
}
function filterAuthorsLetter(pAffiliationInpId, pJournalId, pLetter){
	window.location.href = '/browse_journal_authors.php?journal_id=' + pJournalId +
							'&affiliation=' + document.getElementById(pAffiliationInpId).value +
							'&author_letter=' + pLetter;
}
function ChangeRejectBtn(pTextArea, pBtn1Id, pBtn2Id, pBtn1Active, pBtn2Active) {
	if($(pTextArea).val().length > 0) {
		$('#' + pBtn1Id).hide();
		$('#' + pBtn1Active).show();
		$('#' + pBtn2Id).hide();
		$('#' + pBtn2Active).show();
	} else {
		$('#' + pBtn1Active).hide();
		$('#' + pBtn1Id).show();
		$('#' + pBtn2Active).hide();
		$('#' + pBtn2Id).show();
	}
}
function ExecuteReviewerInvitation(pOper, pUrl, pDocumentId, pUserId, pInvitationId){
	$.ajax({
		url : pUrl,
		dataType : 'json',
		data : {
			oper : pOper,
			document_id : pDocumentId,
			user_id : pUserId,
			invitation_id : pInvitationId
		},
		success : function(pAjaxResult) {
			if(pAjaxResult['err_cnt']){
				alert('Error occurred');
			}else{
				window.location.reload();
			}
		}
	});
}

function autoCompleteReviewers(SITE_URL, document_id, current_round_id)
{
	$.ui.autocomplete.prototype._renderMenu = function(ul, items) {
	  var self = this;
	  ul.append('<table width="100%" cellspacing="0"><tbody></tbody></table>');
	  $.each( items, function( index, item ) {
	    self._renderItem( ul.find("table tbody"), item );
	  });
	};
	$.ui.autocomplete.prototype._renderItem = function ( table, row ) {
		var TR =  $( "<tr></tr>" )
			.data( "item.autocomplete", row )
			.append(
					"<td class=\"name\">" + row.name + "</td>" +
					"<td class=\"affiliation\">" + row.affiliation + "</td>" +
					"<td class=\"affiliation\">" + row.email + "</td>" +
					"<td><a href='javascript:InviteReviewerAsGhost(" + row.id + ", " + document_id + ", " + current_round_id + ")'>add to list</a></td>"
					)
			.appendTo( table );
		return TR;
	};

	$(document).ready(function () {
	    $("#reviewer_search").autocomplete({
	    source: SITE_URL + "lib/ajax_srv/usr_autocomplete_srv.php?action=get_users",
	    autoFocus: true,
	    minLength: 3,
	    select: function(){
	    	$("#subject_editor_search").val("");
	    	return false;
	    }

		}
		);
	});
}
function InviteReviewerAsGhost(uid, doc_id, round_id)
{
	return ExecuteSimpleDocumentAjaxRequest({
		document_id : doc_id,
		reviewer_id : uid,
		current_round_id : round_id,
		action : 'invite_reviewer_as_ghost'
	});
}
function moveGroupOrUserAjax(pObj, pOper, pJournalId, pGroupdId, pDirection, pGuid){
	if(typeof pGuid == "undefined"){
		pGuid = 0;
	}
	var RedirUrl;
	if (pOper == 1)
		RedirUrl = '/manage_journal_groups.php?journal_id='+pJournalId;
	else
		RedirUrl = '/edit_journal_group.php?journal_id='+pJournalId+'&tAction=showedit&id='+pGuid;
	$.ajax({
		url : gEditGroupUrl,
		dataType : 'json',
		async : false,
		data :{
			journal_id : pJournalId,
			direction  : pDirection,
			id		   : pGroupdId,
			oper	   : pOper,
			tAction    : 'moveupdown'
		},
		success : function(pAjaxResult){
			if(pAjaxResult){
				window.location.href=RedirUrl;
			}
		}
	});
	window.location.href=RedirUrl;
}
function updateUserRole(pJournalId, pGroupId, pUserId){
	var lRole = $('#' + pUserId).val();
	alert("User role has been updated successfully");
	$.ajax({
		url : '/edit_journal_group.php?journal_id=' + pJournalId + '&tAction=update&id='+ pGroupId,
		dataType : 'json',
		data : {
			role : lRole,
			group_id : pGroupId,
			user_id : pUserId
		},
		success : function(pAjaxResult) {
			if(pAjaxResult['err_cnt']){
				alert('Error occurred');
			}else{
				window.location = '/edit_journal_group.php?journal_id=' + pJournalId + '&tAction=showedit&id='+ pGroupId;
			}
		}
	});
}
function ExecuteUserInvitation(pOper, pUrl, pUserId){
	lGroupId = $('#groupid').val();
	$.ajax({
		url : pUrl,
		dataType : 'json',
		data : {
			oper : pOper,
			group_id : lGroupId,
			user_id : pUserId,
		},
		success : function(pAjaxResult) {
			if(pAjaxResult['err_cnt']){
				alert('Error occurred');
			}else{
				window.location.reload();
			}
		}
	});
}
/*
function SaveSEDecision(pRoundUserId, pDecisionId, pDocumentId){ accept Decision
	return ExecuteSimpleDocumentAjaxRequest({
		round_user_id : pRoundUserId,
		decision_id : pDecisionId,
		document_id: pDocumentId,
		decision_notes : $('#decision_notes').val(),
		action : 'save_se_decision'
	});
}
function SaveSERejectDecision(pRoundUserId, pDecisionId, pDocumentId){ reject - Decision
	return ExecuteSimpleDocumentAjaxRequest({
		round_user_id : pRoundUserId,
		decision_id : pDecisionId,
		document_id : pDocumentId,
		decision_notes : $('#ed_notes_reject').val(),
		action : 'save_serej_decision'
	});
}



function SaveSEDecision(pRoundUserId, pDecisionId, pDocumentId){
	return ExecuteSimpleDocumentAjaxRequest({
		round_user_id : pRoundUserId,
		decision_id : pDecisionId,
		document_id: pDocumentId,
*/

function savePopUpUserDecision(pAction, pRoundUserId, pDocumentId, pRole, pUserId, pDontClosePopUp){
	//saveDecision(pAction, pRoundUserId, pDocumentId, pRole, pUserId, pDontClosePopUp);
	serializeAndSubmitFormThenCloseAndRefresh();
}

gCERole = 9;
function saveDecision(pAction, pRoundUserId, pDocumentId, pRole, pUserId, pDontClosePopUp){
	var lDecisionId = $('#decision input:checked').val();
	if(pRole == gCERole) {
		lDecisionId = $('input[name=decision_id]').val()
	}
	if (pRole == 2 || pRole == 3)
		if (lDecisionId == 2 || lDecisionId == 5) // Reject
			pAction = 'save_serej_decision';
	// else // Accept

	$.ajax({
		url : lDocumentsAjaxSrv,
		dataType : 'json',
		data : {
			round_user_id : pRoundUserId,
			decision_id   : lDecisionId,
			document_id   : pDocumentId,
			uid 		  : pUserId,
			action 		  : pAction
		},
		success : function(pAjaxResult) {
			if(pAjaxResult['err_cnt']){
				alert('Error occurred');
			}else{
				//~ window.location.reload();
				if(pAjaxResult['url_params']) {
					var lUrl = document.URL;
					window.location = lUrl + (lUrl.split('?')[1] ? '&':'?') + pAjaxResult['url_params'];
				} else {
					window.location.reload();
				}

				if(pDontClosePopUp == 'undefined' || !pDontClosePopUp) {
					popupClosingAndReloadParent(pAjaxResult['url_params']);
				}
			}
		}
	});
}

function Collapse(pOper, pClosedDiv, pOpenedDiv) {
	if(pOper == 0) {
		$('#' + pClosedDiv).show();
		$('#' + pOpenedDiv).hide();
	} else {
		$('#' + pOpenedDiv).show();
		$('#' + pClosedDiv).hide();
	}
}

function serializeAndSubmitFormThenCloseAndRefresh(){
	var lJqForm = $('form[name="document_review_form"]');
	var lData = '&tAction=save&';
	lData += lJqForm.formSerialize();
	var a = $('#duedate').val();
	$.ajax({
		url : lJqForm.attr('action'),
		dataType : 'html',
		data : lData,
		async : false,
		type : 'POST',
		success : function(pAjaxResult) {
			//console.log(pAjaxResult);
		}
	});
	popupClosingAndReloadParent();
}

function SubmitFormByName(pName) {
	var lJqForm = $('form[name="' + pName + '"]');
	var lData = '&tAction=save&';
	lData += lJqForm.formSerialize();

	$.ajax({
		url : lJqForm.attr('action'),
		dataType : 'html',
		data : lData,
		async : false,
		type : 'POST',
		success : function(pAjaxResult) {
			window.location.reload();
		}
	});
}

function popupClosingAndReloadParent(pUrlAddParams) {
	window.close();

	if(pUrlAddParams) {
		var lUrl = window.opener.document.URL;
		window.opener.location = lUrl + (lUrl.split('?')[1] ? '&':'?') + pUrlAddParams;
	} else {
		window.opener.location.reload();
	}
	//window.opener.location.reload();
}
function popupClose(){
	window.close();
}
function openPopUp(pUrl, pWidth, pHeight){
	if(typeof pWidth == "undefined")
		pWidth = '1350';
	if(typeof pHeight == "undefined")
		pHeight = '850';
	var left = (screen.width/2)-(pWidth/2);
	var top = (screen.height/2)-(pHeight/2);
	var lOpenWindow = window.open(pUrl, "littleWindow", 'scrollbars=yes, location=no,width='+ pWidth +',height=' + pHeight + ', top='+top+', left='+left);
	//~ console.log('open');
	$(lOpenWindow).load(function() {
		//~ $('#loading-image').hide();
		//~ console.log('loaded');
	});
	lOpenWindow.focus();
}
function openFilterPopUp(){
	var lPopUp = $('#docEditHeader .box .popup');
	var lIsVisible = lPopUp.is(':visible');
	if (lIsVisible == true)
		lPopUp.css("display", "none");
	else
		lPopUp.css("display", "block");
}
var gLeftColHide = 0;
function toggleLeftContainer(){
	if( gLeftColHide ){ //show left column
		gLeftColHide = 0;
		$('.' + gLeftContainerClass).removeClass(gLeftContainerClassHide);
		$('.' + gMiddleContainerClass).removeClass(gLeftContainerClassHide);
		$('.P-Article-Buttons.P-Bottom').show();
	}else{				//hide left column
		gLeftColHide = 1;
		$('.' + gLeftContainerClass).addClass(gLeftContainerClassHide);
		$('.' + gMiddleContainerClass).addClass(gLeftContainerClassHide);
		$('.P-Article-Buttons.P-Bottom').hide();
	}
}
var gRightColHide = 0;
function toggleRightContainer(){
	if( gRightColHide ){ //show right column
		gRightColHide = 0;
		$('.' + gRightContainerClass).removeClass(gRightContainerClassHide);
		$('.' + gMiddleContainerClass).removeClass(gRightContainerClassHide);
	}else{				 //hide right column
		gRightColHide = 1;
		$('.' + gRightContainerClass).addClass(gRightContainerClassHide);
		$('.' + gMiddleContainerClass).addClass(gRightContainerClassHide);
	}
}
var gCommentsInPreviewMode = 1;
function setCommentsPreviewMode(pMode){
	gCommentsInPreviewMode = pMode;
}
$(document).ready(function () {
	var lMode = $('#role').val();
	if(lMode == 3){
		$('#user_roles_checkbox').css('display', 'none');
	}
});
function activateFieldCheckbox(pClass){
	var lField = $('.' + pClass);
	var lState = lField.attr('disabled');
	if (lState == 'disabled')
		lField.removeAttr('disabled');
	else
		lField.attr('disabled', 'disabled');
}
function checkReviewersState(pRoundId, pUrl, pDocumentId, pMergeFlag, pReviewersLock){

	var lConfirm;
	if(pReviewersLock != 'true' && pReviewersLock != 't') {
		lConfirm = confirm("If you proceed, you'll not be able to invite more reviewers in this review round!");
	} else {
		lConfirm = true;
	}

	if(!lConfirm){
		return;
	}

	$.ajax({
		url : gDocumentAjaxSrv,
		async : false,
		data : {
			roundid : pRoundId,
			document_id : pDocumentId,
			action : 'check_reviewers'
		},
		success : function(pAjaxResult) {
				if (pAjaxResult['err_cnt'] == 0){
					var lInvitedUsers = pAjaxResult['invited_users'];
					var lInvitedUsersIds = pAjaxResult['invited_users_ids'];
					var lNonSubmitedUsers = pAjaxResult['non_submited_users'];
					var lNonSubmitedUsersIds = pAjaxResult['non_submited_users_ids'];
					if(lInvitedUsersIds == null)
						lInvitedUsersIds = '';

					if(lNonSubmitedUsersIds == null)
						lNonSubmitedUsersIds = '';

					var lMsgSubmitedUsers = '';
					var lMsgInvitedUsers = '';

					if(lInvitedUsers != 0)
						lMsgInvitedUsers = 'Confirm cancelling invitations of ' + lInvitedUsers + ' user(s).';
					if(lNonSubmitedUsers != 0)
						lMsgSubmitedUsers = 'Confirm submitting reviews of ' + lNonSubmitedUsers + ' user(s).';
					if(lInvitedUsers != 0 || lNonSubmitedUsers != 0){
						if (confirm(lMsgInvitedUsers + "\n" + lMsgSubmitedUsers) == true){
							manageUserInvitationsAndReviews(pRoundId, pDocumentId, lInvitedUsersIds, lNonSubmitedUsersIds);
							//disableInvitingUsers(pRoundId, pDocumentId, pUrl);
							openPopUp(pUrl);
							window.location.reload();
						}
					} else {
						//disableInvitingUsers(pRoundId, pDocumentId, pUrl);
						openPopUp(pUrl);
						window.location.reload();
					}
				}
			}
	});
}
function confirmDocumentVersionsMergeEditor(pRoundId, pUrl, pDocumentId, pMergeFlag){
	if(pMergeFlag == 1) {
		$.ajax({
			url : gDocumentAjaxSrv,
			async : false,
			data : {
				roundid : pRoundId,
				action : 'merge_versions'
			},
			success : function(pAjaxResult) {
					if (pAjaxResult['err_cnt'] == 0){
						//disableInvitingUsers(pRoundId, pDocumentId, pUrl);
						openPopUp(pUrl);
					}
				}
		});
	} else {
		openPopUp(pUrl);
	}
}
/*
function disableInvitingUsers(pRoundId, pDocumentId, pUrl){
	$.ajax({
		url : gDocumentAjaxSrv,
		async : false,
		data : {
			roundid : pRoundId,
			document_id : pDocumentId,

			action : 'disable_inviting_users'
		},
		success : function(pAjaxResult) {
				if (pAjaxResult['err_cnt'] == 0){
					if(pUrl && pUrl != 'undefined') {
						openPopUp(pUrl);
					}
				}
			}
	});
}
*/

function manageUserInvitationsAndReviews(pRoundId, pDocumentId, pInvitedUserIds, pUserReviewsIds){
	$.ajax({
		url : gDocumentAjaxSrv,
		async : false,
		data : {
			document_id : pDocumentId,
			round_id : pRoundId,
			invited_users_ids : pInvitedUserIds,
			non_submited_users_ids : pUserReviewsIds,
			action : 'manage_user_invitations_and_reviews'
		},
		success : function(pAjaxResult) {
				if (pAjaxResult['err_cnt'] == 0){
					//~ window.location.reload();
					//~ console.log(pAjaxResult);
				}
			}
	});
}

var gDueDateAjaxSrv = gAjaxUrlsPrefix + 'duedate_srv.php';
function updateDueDateandClose(pAction, pRoundId, pRoundUserId){
	var lJqForm = $('form[name="edit_due_date_form"]');
	var lData = '';
	lData += lJqForm.formSerialize();
	//console.log(lData);
	$.ajax({
			url : gDueDateAjaxSrv,
			async : false,
			data : {
				roundid : pRoundId,
				rounduserid : pRoundUserId,
				duedate	: lData,
				action : pAction
			},
			success : function() {
				$.modal.close();
				window.location.reload();
			}
		});
}
var gReviewTypeAjaxSrv = gAjaxUrlsPrefix + 'review_srv.php';
function updateReviewTypeAndClose(pDocumentId){
	var lJqForm = $('form[name="review_edit_form"]');
	var lData = '';
	lData += lJqForm.formSerialize();
	//console.log(lData);
	//~ alert(1);
	$.ajax({
			url : gReviewTypeAjaxSrv,
			async : false,
			dataType: "json",
			data : {
				document_id : pDocumentId,
				data	: lData
			},
			success : function() {
				$.modal.close();
				window.location.reload();
			}
		});
}
function openDueDatePopUp(pUrl, pWidth, pHeight){
	pElem = 'P-Registration-Content';
	$.ajax({
			//~ url : '/updateduedate.php?action=user_invitation&roundduedate=30/01/2013 00:00:00&roundid=1120&rounduserid=695',
			url : pUrl,
			async : false,
			// ~ data :lDataForAjax,
			success : function(pAjaxResult) {
				$('#' + pElem).html(pAjaxResult);
				//~ alert(pAjaxResult);
					$('#' + pElem).modal({
						autoResize : true,
						position : ["40%",],
						minHeight : 230,
						maxHeight : pHeight,
						maxWidth : pWidth,
						overlayClose : true,
						onShow : function(dialog) {
							var doch = $(window).height();
							if(doch <= 430){
								var calh = doch - 2 * 80;
								$('#simplemodal-container').height(calh);
							}
						}
					});
					//~ if($('#regstep').val()){
						//~ var doch = $(window).height();
						//~ var calh = doch - 2 * 80;
						//~ $('#simplemodal-container').height(calh);
					//~ }
			}
		});
}
function openReviewTypePopUp(pUrl, pWidth, pHeight){
	pElem = 'P-Registration-Content';
	$.ajax({
			//~ url : '/updateduedate.php?action=user_invitation&roundduedate=30/01/2013 00:00:00&roundid=1120&rounduserid=695',
			url : pUrl,
			async : false,
			// ~ data :lDataForAjax,
			success : function(pAjaxResult) {
				$('#' + pElem).html(pAjaxResult);
				//~ alert(pAjaxResult);
					$('#' + pElem).modal({
						autoResize : true,
						position : ["40%",],
						minHeight : 230,
						maxHeight : pHeight,
						maxWidth : pWidth,
						overlayClose : true,
						onShow : function(dialog) {
							var doch = $(window).height();
							if(doch <= 430){
								var calh = doch - 2 * 80;
								$('#simplemodal-container').height(calh);
							}
						}
					});
					//~ if($('#regstep').val()){
						//~ var doch = $(window).height();
						//~ var calh = doch - 2 * 80;
						//~ $('#simplemodal-container').height(calh);
					//~ }
			}
		});
}
function openDatePickerDueDates(pElem){
		$("#" + pElem).datepicker({
			showOn: "button",
			buttonImage: "i/calendar.png",
			buttonImageOnly: true,
			dateFormat: 'yy/mm/dd',
			minDate: 1,
			defaultDate: "+1d",
		});
}
var gPricesAjaxSrv = gAjaxUrlsPrefix + 'prices_srv.php';
function updateDocumentAutoPrice(pObj, pOper){
	if (pOper == 1) {

	} else {
		var lStartPage = $('#startpage').val();
		var lEndPage = $('#endpage').val();
		if ((lStartPage >= lEndPage)){
			lAutoPriceField = $('#autoprice .inputFld');
			lAutoPriceField.attr('value', 'N/A');
		} else {
		//~ console.log(pObj.name);
		//~ console.log(lStartPage + ' - ' + lEndPage);
			$.ajax({
				url : gPricesAjaxSrv,
				async : false,
				dataType: "json",
				data : {
					action : 'automatic_price',
					startpage : lStartPage,
					endpage : lEndPage,
				},
				success : function(pAjaxResult) {
					if (pAjaxResult['err_cnt'] > 0){
						for(var i=0; i < pAjaxResult['err_cnt']; i++){
							alert(pAjaxResult['err_msgs'][i]['err_msg']);
						}
					} else {
						lAutoPriceField = $('#autoprice .inputFld');
						lAutoPriceField.attr('readonly', 'readonly');
						lAutoPriceField.removeAttr('value');
						lAutoPriceField.attr('value', pAjaxResult['price']);
					}
				}
			});
		}
	}
}
function scrollToForm(){
	var lHeaderHeight = $('.documentHeader').height();
	
	var lFormEl = $('#P-Version-PopUp-Form');
	var lElemToScroll = $(lFormEl).find('.errstr:visible:first');
	if(!lElemToScroll.length){
		lElemToScroll = lFormEl;
		if(!lFormEl){
			return;
		}
	}
	$('html, body').animate( {
		scrollTop : ( $(lElemToScroll).offset().top - lHeaderHeight )
	},
	{
		duration : 800
	});
}

function scrollTo(anchor){
	$('html, body').animate( 
		{ scrollTop: ( $(anchor).offset().top - $('.documentHeader').height() )}, 
		{ duration: 800}
	);
}

function scrollToPreviewIframeAnchor(pAnchor){
	$('html, body').animate( 
		{ scrollTop: ( GetPreviewContent().find(pAnchor).offset().top)}, 
		{ duration: 800}
	);
}

var gPreviewAjaxSrv = gAjaxUrlsPrefix + 'preview_srv.php';
function getDocumentPreview(pVersionId, pReadOnlyFlag, pPreviewHolderId, pArticleHolderId){
	$('#P-Ajax-Loading-Image').show();
	// tuka ne6to bugqsva framework-a kato mu se podade nula v template-a
	if(pReadOnlyFlag == 2) {
		pReadOnlyFlag = 0;
	}

	$.ajax({
			url : gPreviewAjaxSrv,
			async : false,
			dataType: "json",
			data : {
				version_id : pVersionId,
				readonly_preview : pReadOnlyFlag
			},
			success : function(pAjaxResult) {
				if(!pAjaxResult['err_cnt']){
					$('#' + pPreviewHolderId).html(pAjaxResult['preview']);
					$('#' + pArticleHolderId).show();

				}
				//~ console.log(pAjaxResult);
				$('#P-Ajax-Loading-Image').hide();
			}
		});
}

function LayerUserExpertisesFrm(pElem, pJournalId, pDocumentId, pSEID) {
	$.ajax({
		url : '/lib/ajax_srv/user_expertises.php',
		async : false,
		data : {
			journal_id : pJournalId,
			document_id : pDocumentId,
			se_uid : pSEID
		},
		success : function(pAjaxResult) {
			if(pAjaxResult == 'close') {
				$.modal.close();
			} else {
				$('#' + pElem).html(pAjaxResult);

				$('#' + pElem).modal({
					autoResize : true,
					position : ["10%", ],
					minHeight : 430,
					maxHeight : 600,
					overlayClose : true,
					onShow : function(dialog) {
						var doch = $(window).height();
						if(doch <= 430){
							var calh = doch - 2 * 80;
							$('#simplemodal-container').height(calh);
							$('#simplemodal-container .taskspopup-rightcol').height((calh - 20));
						} else {
							$('#simplemodal-container .taskspopup-rightcol').height(430);
						}
					},
				});
			}
		}
	});
}

function PerformUserExpertisesAction(ptAction, pContainer) {
		var lSerializedFormValues = $('form[name="user_expertises"]').serialize();
		lSerializedFormValues = lSerializedFormValues + '&tAction=' + ptAction;

		$.ajax({
			url : '/lib/ajax_srv/user_expertises.php',
			async : false,
			type : 'POST',
			data : lSerializedFormValues,
			dataType: 'JSON',
			success : function(pAjaxResult) {
				if(pAjaxResult['url'] && pAjaxResult['url'] != 'undefined') {
					$.modal.close();
					window.location = pAjaxResult['url'];
				} else {
					$('#' + pContainer).html(pAjaxResult);
					$('#simplemodal-container').height(430);
				}
			}
		});
}

function HideOptionWithoutValue(pElement) {
	$(pElement).find("option[value='']").remove();
}

function DisableOptionByValue(pElement) {
	if($(pElement).val() != '') {
		HideOptionWithoutValue(pElement);
	}

	var lVal = '';
	$(pElement).find("option").each(function(){
		lVal = $(this).text();
		if(lVal.length == 1) {
			$(this).attr('disabled', 'disabled');
		} else {
			$(this).css({'paddingLeft' : '10px'});
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

/**
 * Връща първия DOM node, който съдържа и 2та възела
 * @param pNodeA
 * @param pNodeB
 */
function getFirstCommonParent(pNodeA, pNodeB){
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

function BindDecisionClickEvents(pHolder) {
	$(pHolder).find('span').each(function(){
		$(this).click(function(){
			//console.log($(this));
			$(this).prev().attr('checked', 'checked');
		});
	});
}

$(document).ready(function() {
	var lSubmissionNotesContent = $(gSubmissionNotestextareaID).val();
	if(lSubmissionNotesContent) {
		$(gSubmissionNotesHolderClosed).hide();
		$(gSubmissionNotesHolderOpen).show();
	}
});

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

function GeneratePDFPreview(pVersionId) {
	$('#P-Ajax-Loading-Image-Main').show();
	document.location.href = gGeneratePDFAjaxSrv + '?version_id=' + pVersionId + '&readonly_preview=1';
	$('#P-Ajax-Loading-Image-Main').hide();
	return;
}

function getIframeSelection(pIframeId){
	var lIframe = $('#' + pIframeId)[0];
	if(!lIframe)
		return false;
	var lSelection = rangy.getIframeSelection(lIframe);
	return lSelection;
}


function resizePreviewIframe(pIframeId){
	var lIframe = $('#'+ pIframeId);
	lIframe.height(lIframe.contents().find('body').height() + 20);
	lIframe.show();
}

function initPreviewIframeLoadEvents(pIframeId){	
	$("#" + pIframeId).load(function(){		
		$('#P-Article-Content').show();
		$('#P-Ajax-Loading-Image').hide();
		resizePreviewIframe(pIframeId);				
		initPreviewSelectCommentEvent();			
	});
	window.onresize = function() {
		resizePreviewIframe(pIframeId);
	}
}


function ShowHideLabel(pElem, pLabel) {
	var lElemVal = $(pElem).val();
	if(lElemVal == '') {
		$(pLabel).show();
	} else {
		$(pLabel).hide();
	}
}

function HideLabel(pElem, pLabel) {
	$(pLabel).hide();
}

function showLoginWarningMessage(pRedirUrl, pWarning) {
	if(confirm(pWarning)){
		window.location = pRedirUrl;
	}
}

function GetPreviewContent(){
	return $('#' + gPreviewIframeId).contents();
}

function GetPreviewSelection(){
	return getIframeSelection(gPreviewIframeId);
}
