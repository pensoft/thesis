var gCurrentActiveChangeNode = false;
var gActiveChangeClass = 'P-Active-Change';
var gChangesSelector = 'insert,delete,accepted-insert,accepted-delete';
var gAcceptChangeBtnId = 'P-Accept-Change-Btn-Id';
var gRejectChangeBtnId = 'P-Reject-Change-Btn-Id';
var gDisabledBtnClass = 'P-Disabled-Btn';
var gAcceptChangeBtn, gRejectChangeBtn;

function InitChangeBtns(){
	gAcceptChangeBtn = $('#' + gAcceptChangeBtnId);
	if(!gAcceptChangeBtn.length){
		gAcceptChangeBtn = window.parent.$('#' + gAcceptChangeBtnId);
	}
	gRejectChangeBtn = $('#' + gRejectChangeBtnId);
	if(!gRejectChangeBtn.length){
		gRejectChangeBtn = window.parent.$('#' + gRejectChangeBtnId);
	}	
}

/**
 * Looks in the selected text for a change node
 */
function CheckSelectedTextForActiveChange() {
	var lSelection = GetPreviewSelection();


	var lStartNode, lStartOffset, lEndNode, lEndOffset;
	if(!lSelection.isBackwards()){
		lStartNode = lSelection.anchorNode;
		lStartOffset = lSelection.anchorOffset;
		lEndNode = lSelection.focusNode;
		lEndOffset = lSelection.focusOffset;
	}else{
		lStartNode = lSelection.focusNode;
		lStartOffset = lSelection.focusOffset;
		lEndNode = lSelection.anchorNode;
		lEndOffset = lSelection.anchorOffset;
	}

	if(!lStartNode || !lEndNode){
		return;
	}

	if(lStartNode.nodeType == 1){
		lStartNode = lStartNode.childNodes[lStartOffset];
	}
	if(lEndNode.nodeType == 1){
		lEndNode = lEndNode.childNodes[lEndOffset];
	}

	var lPreviewContent = GetPreviewContent();
	var lActiveChangeNode = false;
	lPreviewContent.find(gChangesSelector).each(function(pIdx) {
		if(lSelection.containsNode(this, true)){
			lActiveChangeNode = this;
			return false;
		}		
	});
	if(!lActiveChangeNode){
		DeactivateAllChanges();
	}else{
		MakeChangeActive(lActiveChangeNode);
	}
}

function DeactivateAllChanges() {
	if(!gCurrentActiveChangeNode){
		return;
	}
	gCurrentActiveChangeNode = false;
	var lPreviewContent = GetPreviewContent();
	lPreviewContent.find('.' + gActiveChangeClass).removeClass(gActiveChangeClass);
	DisableChangeBtns();
}

function MakeChangeActive(pChangeNode) {
	if(gCurrentActiveChangeNode == pChangeNode){
		return;
	}
	DeactivateAllChanges();
	gCurrentActiveChangeNode = pChangeNode;
	$(pChangeNode).addClass(gActiveChangeClass);
	EnableChangeBtns();
}

function EnableChangeBtns(){
	$(gAcceptChangeBtn).removeClass(gDisabledBtnClass);
	$(gRejectChangeBtn).removeClass(gDisabledBtnClass);
}

function DisableChangeBtns(){
	$(gAcceptChangeBtn).addClass(gDisabledBtnClass);
	$(gRejectChangeBtn).addClass(gDisabledBtnClass);
}

function CheckIfChangeIsVisible(pChangeNode){
	return $(pChangeNode).is(":visible");
}

function SelectPreviousNextChange(pPrevious){
	var lStartNode = gCurrentActiveChangeNode;
	var lEndNode = gCurrentActiveChangeNode;
	if(!lStartNode){
		var lSelection = GetPreviewSelection();
		var lStartOffset, lEndOffset;
		if(lSelection){
			if(!lSelection.isBackwards()){
				lStartNode = lSelection.anchorNode;
				lStartOffset = lSelection.anchorOffset;
				
				lEndNode = lSelection.focusNode;
				lEndOffset = lSelection.focusOffset;
			}else{
				lStartNode = lSelection.focusNode;
				lStartOffset = lSelection.focusOffset;
				
				lEndNode = lSelection.anchorNode;
				lEndOffset = lSelection.anchorOffset;
			}

			if(lStartNode && lStartNode.nodeType == 1){
				lStartNode = lStartNode.childNodes[lStartOffset];
			}
			if(lEndNode && lEndNode.nodeType == 1){
				lEndNode = lEndNode.childNodes[lEndOffset];
			}
		}
		if(!lStartNode){
			lStartNode = GetPreviewFirstNode();
		}
	}

	var lResultChangeNode = false;
	var lChangeFoundBefore = false;
	var lPreviewContent = GetPreviewContent();

	lPreviewContent.find(gChangesSelector).each(function(pIdx) {
		var lChangeIsVisible = CheckIfChangeIsVisible(this);
		var lChangeOrderRelativeToSelectionStart = compareNodesOrder(lStartNode, this);

		if(pPrevious){
			if(lChangeOrderRelativeToSelectionStart >= 0 && lChangeFoundBefore){
				// If the change is after the selection and we have found one
				// before the selection - stop processing the other changes
				return false;
			}
			// If the
			if(lChangeIsVisible){
				lResultChangeNode = this;
				if(lChangeOrderRelativeToSelectionStart < 0 && !lChangeFoundBefore){
					lChangeFoundBefore = true;
				}
			}
		}else{
			if(lChangeIsVisible){
				if(!lResultChangeNode){
					lResultChangeNode = this;
				}
			}

			if(lChangeOrderRelativeToSelectionStart > 0){
				// If the comment is after the selection - it is the first after it
				lResultChangeNode = this;
				return false;
			}
		}
	});

	if(lResultChangeNode){
		MakeChangeActive(lResultChangeNode);
		ScrollToChange(lResultChangeNode);		
	}else{
		DeactivateAllChanges();
	}
}

function ScrollToChange(pChangeNode){
	if(!pChangeNode){
		return
	}
	$('html, body').scrollTop($(pChangeNode).offset().top);
}

function AcceptRejectCurrentChange(pAccept){
	if(!gCurrentActiveChangeNode){
		return;
	}
	var lChangeToModify = gCurrentActiveChangeNode;
	SelectPreviousNextChange();
	$('#previewIframe')[0].contentWindow.AcceptRejectChange(lChangeToModify, pAccept);
}