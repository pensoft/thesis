var gCommentStartPosNodeName = 'comment-start';
var gCommentEndPosNodeName = 'comment-end';
var gCommentIdAttributeName = 'comment-id';
var gCommentPositionStartType = 1;
var gCommentPositionEndType = 2;
var gCommentFormHide = 1;
var gCommentsVerticalPosition = {};
var gCommentPreviewElementClass = 'P-Preview-Comment';
var gActiveCommentTextClass = 'Active-Comment-Text';
var gActiveCommentHolderClass = 'Active-Comment-Holder';


var gCommentsInPreviewMode = 0;
var gFilterRootComments = false;
var gVisibleRootCommentIds = [];
var gCurrentActiveCommentId = 0;


var gCommentsVersionId = 0;
var gCommentsDocumentId = 0;

var gTextCommentIdAttribute = 'comment_id';

var gPreviousPreviewSelection = false;
var gPreviousPreviewSelectionStartNode = false;

getPlainText = function(node){
	// used for testing:
	//return node.innerText || node.textContent;


	var normalize = function(a){
		// clean up double line breaks and spaces
		if(!a) return "";
		return a.replace(/ +/g, " ")
				.replace(/[\t]+/gm, "")
				.replace(/[ ]+$/gm, "")
				.replace(/^[ ]+/gm, "")
				.replace(/\n+/g, "\n")
				.replace(/\n+$/, "")
				.replace(/^\n+/, "")
				.replace(/\nNEWLINE\n/g, "\n\n")
				.replace(/NEWLINE\n/g, "\n\n")
				.replace(/NEWLINE/g, ""); // IE
	};
	var removeWhiteSpace = function(node){
		// getting rid of empty text nodes
		var isWhite = function(node) {
			return !(/[^\t\n\r ]/.test(node.nodeValue));
		};
		var ws = [];
		var findWhite = function(node){
			for(var i=0; i<node.childNodes.length;i++){
				var n = node.childNodes[i];
				if (n.nodeType==3 && isWhite(n)){
					ws.push(n);
				}else if(n.hasChildNodes()){
					findWhite(n);
				}
			}
		};
		findWhite(node);
		for(var i=0;i<ws.length;i++){
			ws[i].parentNode.removeChild(ws[i]);
		}

	};

	var recurse = function(pNode){
		// Loop through all the child nodes
		// and collect the text, noting whether
		// spaces or line breaks are needed.
		if(/pre/.test(getDomNodeStyleProperty(pNode, "whiteSpace"))) {
			t += pNode.innerHTML
				.replace(/\t/g, " ")
				.replace(/\n/g, " "); // to match IE
			return "";
		}
		var s = getDomNodeStyleProperty(pNode, "display");
		if(s == "none"){
			return "";
		}
		var gap = checkIfDomNodeHasBlockDisplay(pNode) ? "\n" : " ";
		t += gap;
		for(var i=0; i<pNode.childNodes.length;i++){
			var lCurrentChild = pNode.childNodes[i];
			if(lCurrentChild.nodeType == 3) {
				t += lCurrentChild.nodeValue;
			}
			if(lCurrentChild.childNodes.length) {
				recurse(lCurrentChild);
			}
		}
		t += gap;
		return t;
	};
	// Use a copy because stuff gets changed
	var lNewNode = node.cloneNode(true);
	// Line breaks aren't picked up by textContent
	lNewNode.innerHTML = lNewNode.innerHTML.replace(/<br>/g, "\n");

	// Double line breaks after P tags are desired, but would get
	// stripped by the final RegExp. Using placeholder text.
	var paras = lNewNode.getElementsByTagName("p");
	for(var i=0; i<paras.length;i++){
		paras[i].innerHTML += "NEWLINE";
	}

	var t = "";
	removeWhiteSpace(lNewNode);
	// Make the call!
	return normalize(recurse(lNewNode));
};


function setCommentPos(pCommentId, pStartInstanceId, pStartFieldId, pStartOffset, pEndInstanceId, pEndFieldId, pEndOffset, pUserName, pTimestamp){
	if(!pStartInstanceId || !pEndInstanceId){
		if(!gCommentsVerticalPosition[pCommentId]){
			gCommentsVerticalPosition[pCommentId] = 0;
		}
		return;
	}
	var lPreviewContent = GetPreviewContent();
	var lTemp = lPreviewContent.find(gCommentStartPosNodeName + '[' + gCommentIdAttributeName + '="' + pCommentId + '"]');


//	var lStartNode = $(gCommentStartPosNodeName + '[' + gCommentIdAttributeName + '="' + pCommentId + '"]')[0];
	var lStartNode = lTemp.length ? lTemp[0] : null;
	var lStartOffset = 0;

	var lTemp = lPreviewContent.find(gCommentEndPosNodeName + '[' + gCommentIdAttributeName + '="' + pCommentId + '"]');
//	var lEndNode = $(gCommentEndPosNodeName + '[' + gCommentIdAttributeName + '="' + pCommentId + '"]')[0];
	var lEndNode = lTemp.length ? lTemp[0] : null;
	var lEndOffset = 0;

//	alert(pCommentId);

	if(!lStartNode || !lEndNode){
		var lStartPositionDetails = calculateCommentPositionAccordingToInternalPosition(pStartInstanceId, pStartFieldId, pStartOffset, true);
		var lEndPositionDetails = calculateCommentPositionAccordingToInternalPosition(pEndInstanceId, pEndFieldId, pEndOffset);

		lStartNode = lStartPositionDetails.node;
		lStartOffset = lStartPositionDetails.offset;

		lEndNode = lEndPositionDetails.node;
		lEndOffset = lEndPositionDetails.offset;
	}

	//Тук сме сигурни, че селекцията започва и свършва в различни възли
	var lCommonParent = getFirstCommonParent(lStartNode, lEndNode);

	markCommentStartNode(pCommentId, lCommonParent, lStartNode, lStartOffset, lEndNode, lEndOffset, pUserName, pTimestamp);
	return;

}

/**
 * Намира позицията на коментара спрямо системната позиция
 * Връща обект с пропъртита за възел и оффсет(аналогични на Selection anchor/focus node и offset
 * @param pInstanceId
 * @param pFieldId
 * @param pOffset
 * @param pLookAhead - дали да върне следващия символ/възел. Използваме го понеже в началото на селекцията
 * 	 ни трябва да работим със следващия възел, а в края на селекцията трябва да включим символа/възела,
 * 	 който отговаря на позицията
 */
function calculateCommentPositionAccordingToInternalPosition(pInstanceId, pFieldId, pOffset, pIncludeNode){
	lResult = {
		'node' : null,
		'offset' : null
	};

	if(!pInstanceId){
		return lResult;
	}
	var lNode = GetPreviewContent();
	lNode = lNode.find('*[instance_id="' + pInstanceId + '"]');
	lNode = lNode.first();

	if(!lNode){
		return lResult;
	}

	if(!pFieldId){//Ако нямаме field-id сме или в началото или в края на instance-a
		lNode = lNode[0];
		lResult.node = lNode;
		if(pOffset == 0){
			lResult.offset = pOffset;
			return lResult;
		}
		if(pOffset == -1){
			if(!pLookAhead){
				lResult.offset = lNode.childNodes.length ;//+ 1;
			}else{
				var lNextNode = getNextNode(lNode);
				if(lNextNode){
					lResult.offset = 0;
					lResult.node = lNextNode;
				}
			}
			return lResult;
		}
		return lResult;
	}

	var lFieldNodes = lNode.find('*[field_id="' + pFieldId + '"]');
	var lFieldNode = null;
	for(var i = 0; i < lFieldNodes.length; ++ i){
		var lField = $(lFieldNodes.get(i));
		if(lField.closest('*[instance_id]')[0] === lNode[0]){//Този field е дете на някой подобект
			lFieldNode = lField[0];
			break;
		}
	}

	if(lFieldNode == null){//Някаква грешка е станала щом не може да намерим field-а
		return lResult;
	}

	var lTreeCopy = document.createElement(lFieldNode.nodeName);
	var lTreeCopyRoot = lTreeCopy;
//	Сега обикаляме надолу по възлите докато стигнем до точната позиция която ни отговаря на internal позицията
	var lRootNode = lFieldNode;
	var lOffset = 0;
	var lOffsetFound = false;
	var lLookAhead = false;
	lNodeLoop:
	while(getPlainText(lTreeCopyRoot).length < pOffset){
		if(lRootNode.nodeType == 1){//Element
			var lChildren = lRootNode.childNodes;
			for(var i = 0; i < lChildren.length; ++i){
				var lCurrentChild = lChildren[i];
				//Ако след добавянето на child-възела дължината става по-голяма от оффсет-а
				//значи позицията е някъде вътре в child-a
				//В противен случай целия child влиза вътре
				var lAppendedChild = lTreeCopy.appendChild(lCurrentChild.cloneNode(true));
				if(getPlainText(lTreeCopyRoot).length > pOffset){
					lTreeCopy.removeChild(lAppendedChild);

					if(lCurrentChild.nodeType == 1)
						lTreeCopy = lTreeCopy.appendChild(document.createElement(lCurrentChild.nodeName));
					lRootNode = lCurrentChild;
					continue lNodeLoop;

				}else if(getPlainText(lTreeCopyRoot).length == pOffset){
					if(pIncludeNode){
						lLookAhead = true;
					}
					lOffset = i;
					lOffsetFound = true;
					break lNodeLoop;
				}
			}
		}else if(lRootNode.nodeType == 3){
			var lBaseTreeCopyLength = getPlainText(lTreeCopyRoot).length;
			var lNeededLength = pOffset - lBaseTreeCopyLength;
			var lAppendedText = lTreeCopy.appendChild(lRootNode.cloneNode(true));

			if(getPlainText(lTreeCopyRoot).length == pOffset){
				lOffset = lRootNode.nodeValue.length;
				lOffsetFound = true;
			}
			//Ако по някаква причина сме стигнали до случай, когато възела е по къс от търсенето - край
			if(getPlainText(lTreeCopyRoot).length <= pOffset){
				break;
			}
			lTreeCopy.removeChild(lAppendedText);

			//Тук трябва да намерим точния оффсет - т.е. докъде точно трябва да отрежем текстовия възел
			//за да съвпадне дължината. Подсигурили сме се че възела е по-дълъг от колкото ни трябва
			for(var i = lNeededLength - 2; i < lRootNode.nodeValue.length; ++i){//Тук почваме от 2 назад понеже ако текста в дървото завършва с интервал той няма да е бил преброен
				var lAppendedText = lTreeCopy.appendChild(document.createTextNode(lRootNode.nodeValue.substring(0, i)));

				//Ако сме намерили точната дължина - край
				if(getPlainText(lTreeCopyRoot).length == pOffset){
					lOffset = i;
					lOffsetFound = true;
					break lNodeLoop;
				}
				//Ако не сме намерили дължината - продължаваме като махаме елемента, който добавихме
				lTreeCopy.removeChild(lAppendedText);
			}

		}
		//Ако сме обиколили всички деца и нито 1 не ни върши работа(на теория не би трябвало да има такъв случай) - край
		break;
	}


	//Ако сме намерили точния оффсет - връщаме го него и възела му. Иначе връщаме целия field
	if(lOffsetFound){
		lResult.node = lRootNode;
		lResult.offset = lOffset;

		if(lLookAhead){
			if( (lRootNode.nodeType == 1 && lRootNode.childNodes.length == pOffset)|| ( lRootNode.nodeType == 3 && lRootNode.nodeValue.length == lOffset)){
				var lNextNode = getNextNode(lRootNode);
				if(lNextNode){
					lResult.offset = 0;
					lResult.node = lNextNode;
				}
			}else{
				lResult.offset = lOffset + 1;
			}
		}
	}else{
		var lNextNode = getNextNode(lFieldNode);
		if(!lLookAhead || !lNextNode){
			lResult.node = lFieldNode;
			lResult.offset = lFieldNode.childNodes.length;
		}else{
			lResult.offset = 0;
			lResult.node = lNextNode;
		}

	}
	return lResult;
}



function GetSelectedTextPos(){
	var lSelection = GetCommentSelection();
	if(!lSelection){
		return;
	}
//	var lSelection = rangy.getSelection();

	var lStartNodeDetails = getCommentPositionDetails(lSelection.anchorNode, lSelection.anchorOffset);
	var lEndNodeDetails = getCommentPositionDetails(lSelection.focusNode, lSelection.focusOffset);

	if(lSelection.isBackwards()){//Ако селекцията е наобратно - обръщаме я
		var lTemp = lStartNodeDetails;
		lStartNodeDetails = lEndNodeDetails;
		lEndNodeDetails = lTemp;
	}
	var lResult = {
			'start_pos' : lStartNodeDetails,
			'end_pos' : lEndNodeDetails
	}
	return lResult;
}

/**
 * Намира най-близкия instance/field в който е коментара
 * Връща масив с 3 стойности
 * 		instance_id - ид-то на инстанса към който е коментара
 * 		field_id - ид-то на field-а към който е коментара
 * 		offset - offset-a на коментара
 * 			-1 - в края (ако не сме във field, а само в instance)
 * 			0 - в началото
 * 			друго неотрицателно число - броя текстови символи спрямо началото на field-a
 * @param pNode
 * @param pOffset
 */
function getCommentPositionDetails(pNode, pOffset){
	var lResult = {
		'instance_id' : 0,
		'field_id' : 0,
		'offset' : 0,
	};

	if(!pNode){
		return lResult;
	}

	var lInstanceHolder = $(pNode).closest('*[instance_id]');
	var lFieldHolder = $(pNode).closest('*[field_id]');

	if(!lInstanceHolder.length)
		return lResult;

	lResult.instance_id = lInstanceHolder.attr('instance_id');
	var lFieldHolderParents = lFieldHolder.parents();
	if(lFieldHolder.length && (jQuery.inArray(lInstanceHolder[0], lFieldHolderParents) > -1)){//Field-a е от instance-a
		lResult.field_id = lFieldHolder.attr('field_id');
		//Трябва да пресметнем offset-а
		var lRootNode = lFieldHolder[0];

		//Движим се по дървото надолу.
		var lPNodeParents = $(pNode).parents();
		var lTreeCopy = document.createElement(lRootNode.nodeName);
		var lTreeCopyRoot = lTreeCopy;
		lNodeLoop:
		while(lRootNode !== pNode){
			var lChildren = lRootNode.childNodes;
			for(var i = 0; i < lChildren.length; ++i){
				var lCurrentChild = lChildren[i];
				if(
							(lCurrentChild.nodeType == 1 && (jQuery.inArray(lCurrentChild, lPNodeParents) > -1)) ||
							(lCurrentChild == pNode)
					){
					lRootNode = lCurrentChild;
					if(lCurrentChild.nodeType == 1)
						lTreeCopy = lTreeCopy.appendChild(document.createElement(lCurrentChild.nodeName));
					continue lNodeLoop;
				}

				lTreeCopy.appendChild(lCurrentChild.cloneNode(true));
			}
			//Ако сме обиколили всички деца и нито 1 не ни върши работа(на теория не би трябвало да има такъв случай) - край
			break;
		}
		//Стигнали сме до възела в който е селекцията.
		//Ако възела е от текстов тип - Добавяме и оффсет-а от самия обект на селекцията
		if(pNode.nodeType == 3 ){
			lTreeCopy.appendChild(document.createTextNode(pNode.nodeValue.substring(0, pOffset)));
		}else if(pNode.nodeType == 1){//В противен случай добавяме и дължините на възлите, които са преди offset-a
			var lChildren = pNode.childNodes;
			for(var i = 0; i < lChildren.length && i < pOffset; ++i){
				var lCurrentChild = lChildren[i];
				lTreeCopy.appendChild(lCurrentChild.cloneNode(true));
			}
		}
		lResult.offset = getPlainText(lTreeCopyRoot).length;

	}else{//Field-a e parent на възела. За целта трябва да видим къде да се позиционираме
		//във възела. Може да сме между field-ове, преди всички field-ове или след всички field-ове
		var lInstanceFields = lInstanceHolder.find('*[field_id]');
		var lPrevField = null;
		for(var i = 0; i < lInstanceFields.length; ++i){
			var lField = $(lInstanceFields.get(i));
			if(lField.closest('*[instance_id]') != lInstanceHolder){//Този field е дете на някой подобект
				continue;
			}
			if(compareNodesOrder(lField[0], pNode) > 0){
				lPrevField = lField;
			}else{//Спираме при първия field, който е след възела
				break;
			};
		}
		if(lPrevField !== null){//Имаме предишен field
			lResult.field_id = lPrevField.attr('field_id');
			lResult.offset = -1;
		}else{//Най в началото на instance-a
			lResult.offset = 0;
		}
	}

	return lResult;
}


function markCommentStartNode(pCommentId, pParentNode, pStartNode, pStartOffset, pEndNode, pEndOffset, pUserName, pTimestamp){
	var lStartOffset = pStartOffset;
	if(pParentNode === pStartNode){//Стигнали сме до възела	където почва селекцията
		if(pStartNode.nodeType == 1){//Селекцията е от тип елемент - селектирали сме отделни елементи от него
			var lChildren = pStartNode.childNodes;
			var lEndOffset = lChildren.length;
			if(pStartNode === pEndNode){
				lEndOffset = pEndOffset;
			}

			//Тук разглеждаме и случая когато възела за край е под дете на възела за начало
			for(var i = lStartOffset; i <= lEndOffset && i < lChildren.length; ++i){
				var lCurrentChild = lChildren[i];
				if(lCurrentChild === pEndNode || checkIfNodesAreParentAndDescendent(lCurrentChild, pEndNode)){
					lThisIsEndNode = true;
					markCommentEndNode(pCommentId, lCurrentChild, pEndNode, pEndOffset);
					//Няма смисъл да обикаляме повече
					return;
				}else{
					markCommentInnerNode(pCommentId, lCurrentChild, pUserName, pTimestamp);
				}
			}
		}else if(pStartNode.nodeType == 3){//Възела е текстов- разделяме го на 3 части - преди, селекция и след
			var lEndOffset = pStartNode.nodeValue.length;
			if(pStartNode === pEndNode){
				lEndOffset = pEndOffset;
			}

			if(lStartOffset > 0){
				var lBeforeNode = pStartNode.ownerDocument.createTextNode(pStartNode.nodeValue.substring(0, lStartOffset));
				pStartNode.parentNode.insertBefore(lBeforeNode, pStartNode);
			}
			//Слагаме селекцията в спан
			var lSpanHolder = pStartNode.ownerDocument.createElement('span');
			lSpanHolder.appendChild(pStartNode.ownerDocument.createTextNode(pStartNode.nodeValue.substring(lStartOffset, lEndOffset)));
			lSpanHolder = pStartNode.parentNode.insertBefore(lSpanHolder, pStartNode);
			pasteCommentNodeMarkup(pCommentId, lSpanHolder, pUserName, pTimestamp);

			//Ако имаме още текст
			if(pStartNode.nodeValue.length > lEndOffset){
				var lAfterNode = pStartNode.ownerDocument.createTextNode(pStartNode.nodeValue.substring(lEndOffset));
				pStartNode.parentNode.insertBefore(lAfterNode, pStartNode);
			}
			//Махаме оригиналния текст
			pStartNode.parentNode.removeChild(pStartNode);
		}
		return;
	}
	//Селекцията е някъде в децата
	//За всеки случай се уверяваме, че pParentNode е Element възел
	if(pParentNode.nodeType != 1)
		return;
	//Обикаляме децата му
	var lChildren = pParentNode.childNodes;
	var lStartFound = false;
	for(var i = 0; i < lChildren.length; ++i){
		var lCurrentChild = lChildren[i];
		var lThisIsEndNode = false;
		var lThisIsStartNode = false;

		if(!lStartFound && (lCurrentChild === pStartNode || checkIfNodesAreParentAndDescendent(lCurrentChild, pStartNode))){
			lStartFound = true;
			lThisIsStartNode = true;
		}
		//Ако това е възела където свършва селекцията - няма смисъл да въртим повече след него - край
		if(lCurrentChild === pEndNode || checkIfNodesAreParentAndDescendent(lCurrentChild, pEndNode)){
			lThisIsEndNode = true;
		}

		if(!lStartFound){//Още сме преди селекцията
			continue;
		}

		if(lThisIsStartNode){//Това е възела където почва селекцията - влизаме рекурсивно
			markCommentStartNode(pCommentId, lCurrentChild, pStartNode, pStartOffset, pEndNode, pEndOffset, pUserName, pTimestamp);
		}else if(!lThisIsEndNode){//Целия възел влиза в селекцията
			markCommentInnerNode(pCommentId, lCurrentChild, pUserName, pTimestamp);
		}else{//Възел в който свършва селекцията
			markCommentEndNode(pCommentId, lCurrentChild, pEndNode, pEndOffset, pUserName, pTimestamp);
		}



		if(lThisIsEndNode){
			return;
		}
	}
}

function markCommentInnerNode(pCommentId, pNode, pUserName, pTimestamp){
	if(pNode.nodeType == 1){//Ако е елемент - директно му добавяме класа
		pasteCommentNodeMarkup(pCommentId, pNode, pUserName, pTimestamp);
	}else if(pNode.nodeType == 3){//Ако е текстов възел - wrap-ваме го във span с класа
		var lSpanHolder = pNode.ownerDocument.createElement('span');
		lSpanHolder.appendChild(pNode.cloneNode(true));
		pNode.parentNode.replaceChild(lSpanHolder, pNode);
		pasteCommentNodeMarkup(pCommentId, lSpanHolder, pUserName, pTimestamp);
	}
}

function markCommentEndNode(pCommentId, pParentNode, pEndNode, pOffset, pUserName, pTimestamp){
	if(pParentNode === pEndNode){//Стигнали сме до възела
		if(pEndNode.nodeType == 1){//Селекцията е от тип елемент - селектирали сме отделни елементи от него;
			var lChildren = pEndNode.childNodes;

			for(var i = 0; i <= pOffset && i < lChildren.length; ++i){
				var lCurrentChild = lChildren[i];
				markCommentInnerNode(pCommentId, lCurrentChild, pUserName, pTimestamp);
			}
		}else if(pEndNode.nodeType == 3){//Възела е текстов- разделяме го на 3 части - преди, селекция и след
			//Слагаме селекцията в спан
			var lSpanHolder = pEndNode.ownerDocument.createElement('span');
			lSpanHolder.appendChild(pEndNode.ownerDocument.createTextNode(pEndNode.nodeValue.substring(0, pOffset)));
			lSpanHolder = pEndNode.parentNode.insertBefore(lSpanHolder, pEndNode);
			pasteCommentNodeMarkup(pCommentId, lSpanHolder, pUserName, pTimestamp);

			//Ако имаме още текст
			if(pEndNode.nodeValue.length > pOffset){
				var lAfterNode = pEndNode.ownerDocument.createTextNode(pEndNode.nodeValue.substring(pOffset));
				pEndNode.parentNode.insertBefore(lAfterNode, pEndNode);
			}
			//Махаме оригиналния текст
			pEndNode.parentNode.removeChild(pEndNode);
		}
		return;
	}
	//Селекцията свършва някъде в децата
	//За всеки случай се уверяваме, че pParentNode е Element възел
	if(pParentNode.nodeType != 1)
		return;
	//Обикаляме децата му
	var lChildren = pParentNode.childNodes;
	for(var i = 0; i < lChildren.length; ++i){
		var lCurrentChild = lChildren[i];
		var lThisIsEndNode = false;
		if(lCurrentChild === pEndNode || checkIfNodesAreParentAndDescendent(lCurrentChild, pEndNode)){
			lThisIsEndNode = true;
		}

		if(lThisIsEndNode){//Това е възела където свършва селекцията - влизаме рекурсивно
			markCommentEndNode(pCommentId, lCurrentChild, pEndNode, pOffset, pUserName, pTimestamp);
			//След него няма смисъл да обикаляме
			return;
		}else{//Това е възел който изцяло влиза в селекцията
			markCommentInnerNode(pCommentId, lCurrentChild, pUserName, pTimestamp);
		}
	}
}



function positionCommentsBase(){
//	return;
	if(gCommentsInPreviewMode < 1)
		return;

	var lRootComments = $('.P-Root-Comment');

	compareRootComments = function(pCommentA, pCommentB){
		var lPattern = new RegExp("^P-Root-Comment-Holder-(\\d+)$","i");
		var lCommentAId = 0;
		var lCommentBId = 0;

		var lMatch = lPattern.exec($(pCommentA).attr('id'));
		if(lMatch !== null){
			lCommentAId = lMatch[1];
		}
		lMatch = lPattern.exec($(pCommentB).attr('id'));
		if(lMatch !== null){
			lCommentBId = lMatch[1];
		}
//		var lCommentAPos = gCommentsVerticalPosition[lCommentAId];
		var lCommentAPos = getCommentVerticalPosition(lCommentAId);
		if(!lCommentAPos){
			lCommentAPos = 0;
		}
//		var lCommentBPos = gCommentsVerticalPosition[lCommentBId];
		var lCommentBPos = getCommentVerticalPosition(lCommentBId);
		if(!lCommentBPos){
			lCommentBPos = 0;
		}

		if(lCommentAPos > lCommentBPos){
			return 1;
		}
		if(lCommentAPos < lCommentBPos){
			return -1;
		}
		var lCommentAStartNode = $(gCommentStartPosNodeName + '[' + gCommentIdAttributeName + '="' + lCommentAId + '"]')[0];
		var lCommentBStartNode = $(gCommentStartPosNodeName + '[' + gCommentIdAttributeName + '="' + lCommentBId + '"]')[0];
		return compareNodesOrder(lCommentBStartNode, lCommentAStartNode);

	};

	for(var i = 0; i < lRootComments.length; ++i){
		for(var j = i; j < lRootComments.length; ++j){
			if(compareRootComments(lRootComments[i], lRootComments[j]) > 0){
				var lTemp = lRootComments[i];
				lRootComments[i] = lRootComments[j];
				lRootComments[j] = lTemp;
			}
		}
	}


	var lPreviousElement = null;

	$.each(lRootComments, function(pIndex, pRow){

		var lPattern = new RegExp("^P-Root-Comment-Holder-(\\d+)$","i");
		var lMatch = lPattern.exec($(pRow).attr('id'));
		var lCommentId = 0;
		if(lMatch !== null){
			lCommentId = lMatch[1];
		}
		if(!lCommentId)
			return;

		if(!CheckIfRootCommentIsVisible(lCommentId)){
			$(pRow).hide();
			return;
		}else{
			$(pRow).show();
		}
		var lOffsetParent = $(pRow).offsetParent();
//		var lCommentPosition = gCommentsVerticalPosition[lCommentId];
		var lCommentPosition = getCommentVerticalPosition(lCommentId);
		lCommentPosition -= lOffsetParent.offset().top;
		if(lCommentPosition < 0){
			lCommentPosition = 0;
		}

		if(lPreviousElement){
        	var lPreviousCommentPosition = $(lPreviousElement).position().top;
        	var lPreviousCommentHeight = $(lPreviousElement).outerHeight();


        	if(lCommentPosition < (lPreviousCommentPosition + lPreviousCommentHeight)){
        		lCommentPosition = (lPreviousCommentPosition + lPreviousCommentHeight);
        	}

        }else{

        }
		$(pRow).css('position', 'absolute');
		$(pRow).css('top', lCommentPosition);

		lPreviousElement = pRow;
    });

	//Накрая пренареждаме и стрелките отдолу
	var lBottomButtons = $('#P-Comments-Bottom-Buttons');
	var lCurrentPosition = 0;
	if(lPreviousElement){
    	var lPreviousCommentPosition = $(lPreviousElement).position().top;
    	var lPreviousCommentHeight = $(lPreviousElement).outerHeight();

    	if(lCurrentPosition < (lPreviousCommentPosition + lPreviousCommentHeight)){
    		lCurrentPosition = (lPreviousCommentPosition + lPreviousCommentHeight);
    	}

    }
	lBottomButtons.css('position', 'absolute');
	lBottomButtons.css('top', lCurrentPosition);
}

function initComment(pCommentId, pStartInstanceId, pStartFieldId, pStartOffset, pEndInstanceId, pEndFieldId, pEndOffset, pUserName, pTimestamp){
	if(gCommentsInPreviewMode < 1)
		return;
	if(!gWindowIsLoaded){
		$(window).load(function(){
			setCommentPos(pCommentId, pStartInstanceId, pStartFieldId, pStartOffset, pEndInstanceId, pEndFieldId, pEndOffset, pUserName, pTimestamp);
		});
	}else{
		setCommentPos(pCommentId, pStartInstanceId, pStartFieldId, pStartOffset, pEndInstanceId, pEndFieldId, pEndOffset, pUserName, pTimestamp);
	}
}

function InsertCommentStartEndTag(pStartNode, pOffset, pCommentId, pIsStart){
	if(!pStartNode){
		return;
	}
	var lNode = pStartNode.ownerDocument.createElement(pIsStart ? gCommentStartPosNodeName : gCommentEndPosNodeName);
	lNode.setAttribute(gCommentIdAttributeName, pCommentId);
	if(pStartNode.nodeType == 1){//Insert the node before the pOffset child
		if(pStartNode.childNodes.length < pOffset - 1){
			pStartNode.appendChild(lNode);
		}else{
			pStartNode.insertBefore(lNode, pStartNode.childNodes[pOffset]);
		}
	}if(pStartNode.nodeType == 3){//Text node -
		var lParent = pStartNode.parentNode;
		if(pStartNode.nodeValue.length < pOffset - 1){//Append it after the text
			if(pStartNode.nextSibling){
				lParent.insertBefore(lNode, pStartNode.nextSibling);
			}else{
				lParent.appendChild(lNode);
			}
		}else{//Split the text node in parts
			if(pOffset > 0){
				lParent.insertBefore(pStartNode.ownerDocument.createTextNode(pStartNode.nodeValue.substr(0, pOffset)), pStartNode);
			}
			lParent.insertBefore(lNode, pStartNode);
			lParent.replaceChild(pStartNode.ownerDocument.createTextNode(pStartNode.nodeValue.substr(pOffset)), pStartNode);
		}
	}
}


function getCommentVerticalPosition(pCommentId){
	var lFirstCommentNode = GetPreviewContent().find("*[comment_id~='" +  pCommentId + "'],*[comment-id='" +  pCommentId + "']").first();
	if(!lFirstCommentNode.length)
		return 0;
	return getCommentNodeVerticalPosition(lFirstCommentNode[0]);
}

function positionComments(){
	if(!gWindowIsLoaded){
		$(window).load(function(){
			positionCommentsBase();
		});
	}else{
		positionCommentsBase();
	}
}

function ExpandCollapseAll(pOper) {
	if(pOper) { // expanding
		$(".P-Comments-Revisions-Item-Content").slideDown(200);

		$(".P-Comments-Revisions-Item").addClass('uparrow');
	} else { // collapsing
		$(".P-Comments-Revisions-Item-Content").slideUp(200);
		$(".P-Comments-Revisions-Item").removeClass('uparrow');
	}
	//Изчакваме да се заредят анимациите
	setTimeout("positionCommentsBase();", 201);

}

function setCommentsWrapEvents(){
	$(".P-Comments-Revisions-Item").unbind('click');
	$(".P-Root-Comment").click(function(){
		var lPattern = new RegExp("^P-Root-Comment-Holder-(\\d+)$","i");
		var lMatch = lPattern.exec(this.id);
		var lCommentId = 0;
		if(lMatch !== null){
			lCommentId = lMatch[1];
			MakeCommentActive(lCommentId);
		}

	});
	$(".P-Comments-Revisions-Item").click(function(){
		var lRootHolderId = $(this).parents('.P-Root-Comment').first().attr('id');
		var lPattern = new RegExp("^P-Root-Comment-Holder-(\\d+)$","i");
		var lMatch = lPattern.exec(lRootHolderId);
		var lCommentId = 0;
		if(lMatch !== null){
			lCommentId = lMatch[1];
		}
		if(lCommentId){
			ExpandCollapseSingleComment(lCommentId);
		}
	});
}

function ExpandCollapseSingleComment(pCommentId){
	var lRootHolder = $('#P-Root-Comment-Holder-' + pCommentId);
	var lRootComment = $('#P-Root-Comment-' + pCommentId);
	var lTreeHolder = lRootHolder.find('.P-Comments-Revisions-Item-Content');
	if(lTreeHolder.is(":visible")){
		lRootComment.removeClass('uparrow');
	}else{
		lRootComment.addClass('uparrow');;
	}
	lTreeHolder.slideToggle(200);
	setTimeout("positionCommentsBase();", 201);
}

function showCommentForm(pId) {
	if(!pId){
		pId = '';
	}
	var lFormIsVisible = $('#P-Comment-Form_' + pId).is(':visible');

	if(pId == ''){
		if(lFormIsVisible){
			clearCommentPos();
		}else{
			fillCommentPos();
		}
	}

	if( !lFormIsVisible ){ //show form
		gCommentFormHide = 0;
		$('#P-Comment-Form_' + pId).show();
		$('#P-Comment-Btn-' + pId).removeClass('comment_btn');
		$('#P-Comment-Btn-' + pId).addClass('comment_btn_inactive');
	}else{				//hide form
		gCommentFormHide = 1;
		$('#P-Comment-Form_' + pId).hide();
		$('#P-Comment-Btn-' + pId).removeClass('comment_btn_inactive');
		$('#P-Comment-Btn-' + pId).addClass('comment_btn');
	}
	positionCommentsBase();
}

/**
 * Тук ще слагаме нещата по възлите, свързани с коментарите - напр. допълнителен клас, онклик евент и т.н.
 * Освен това и ще записваме позицията на коментара, ако такава не е вече записана (за да може да ги подредим правилно отстрани). Това
 * записване е коректно, защото обработваме възлите на коментарите отгоре надолу и ако до сега не е записана
 * позиция за подадения коментар - значи това е 1ят възел от него
 * @param pCommentId
 * @param pNode
 */
function pasteCommentNodeMarkup(pCommentId, pNode, pUserName, pTimestamp){
	$(pNode).addClass(gCommentPreviewElementClass);
	if(!gCommentsVerticalPosition[pCommentId]){
		gCommentsVerticalPosition[pCommentId] = getCommentNodeVerticalPosition(pNode);
	}
	$(pNode).bind('click', function(){
		scrollToComment(pCommentId);
	});
	addAttributeValue($(pNode), 'comment_id', pCommentId);
//	$(pNode).attr('comment_id', pCommentId);
	$(pNode).attr('title', pUserName + ' commented this on ' + pTimestamp);
}

function CheckIfRootCommentIsVisible(pCommentId){
	if(gFilterRootComments && jQuery.inArray(pCommentId, gVisibleRootCommentIds) == -1){
		return false;
	}
	return true;
}

function DeactivateAllComments() {
	if(!gCurrentActiveCommentId){
		return;
	}
	gCurrentActiveCommentId = 0;
	var lPreviewContent = GetPreviewContent();
	lPreviewContent.find('.' + gActiveCommentTextClass).removeClass(gActiveCommentTextClass);
	$('.' + gActiveCommentHolderClass).removeClass(gActiveCommentHolderClass);
}

function MakeCommentActive(pCommentId) {
	if(gCurrentActiveCommentId == pCommentId){
		return;
	}
	DeactivateAllComments();
	gCurrentActiveCommentId = pCommentId;
	var lRootHolder = $('#P-Root-Comment-Holder-' + pCommentId);
	lRootHolder.addClass(gActiveCommentHolderClass);
	var lPreviewContent = GetPreviewContent();
	lPreviewContent.find('.P-Preview-Comment[' + gTextCommentIdAttribute + '*="' + pCommentId + '"]').addClass(gActiveCommentTextClass);
}

function CheckSelectedTextForActiveComment() {
	var lSelection = GetCommentSelection();

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
	var lActiveCommentId = false;
	lPreviewContent.find(gCommentStartPosNodeName).each(function(pIdx) {
		var lCommentId = $(this).attr(gCommentIdAttributeName);
		var lEndCommentNode = lPreviewContent.find(gCommentEndPosNodeName + '[' + gCommentIdAttributeName + '="' + lCommentId + '"]')[0];
		var lCommentStartOrderRelativeToSelectionStart = compareNodesOrder(lStartNode, this);
		var lCommentStartOrderRelativeToSelectionEnd = compareNodesOrder(lEndNode, this);
		var lCommentEndOrderRelativeToSelectionStart = compareNodesOrder(lStartNode, lEndCommentNode);

		// console.log(lStartNode);
		// console.log(lCommentStartOrderRelativeToSelectionStart,lCommentStartOrderRelativeToSelectionEnd,
		// lCommentEndOrderRelativeToSelectionStart);
		if(lCommentStartOrderRelativeToSelectionStart < 0 && lCommentEndOrderRelativeToSelectionStart >= 0){
			// The selection start is between the comment markers
			lActiveCommentId = lCommentId;
			return false;
		}
		if(lCommentStartOrderRelativeToSelectionStart == 0){
			// The selection starts in the comment start marker
			lActiveCommentId = lCommentId;
			return false;
		}
		if(lCommentStartOrderRelativeToSelectionStart > 0 && lCommentStartOrderRelativeToSelectionEnd <= 0){
			// The comment nodes are between the selection
			lActiveCommentId = lCommentId;
			return false;
		}
	});
	if(!lActiveCommentId){
		DeactivateAllComments();
	}else{
		MakeCommentActive(lActiveCommentId);
	}
}

function ResolveComment(pCommentId) {
	var lIsResolved = $('#is_resolved_' + pCommentId + ':checked').length ? 1 : 0;
	$.ajax({
		url : gCommentAjaxSrvUrl,
		dataType : 'json',
		data : {
			comment_id : pCommentId,
			resolve : lIsResolved,
			action : 'resolve_comment'
		},
		success : function(pAjaxResult) {
			if(pAjaxResult['err_cnt']){
				alert(pAjaxResult['err_msg']);
				return;
			}
			var lLabel = $('#label_is_resolved_' + pCommentId);
			var lLabelText = 'Resolve';
			if(pAjaxResult['is_resolved']){
				lLabelText = 'Resolved by ' + pAjaxResult['resolve_fullname'];
			}
			$(lLabel).html(lLabelText);
			FilterComments();
		}
	});
}

function SetDisplayUserChangeFilterEvent() {
	$('input[name="display_user_change"]').bind('change', function() {
		FilterComments();
	});
}

function FilterComments() {
	var lResolved = $('#comments_filter_resolved:checked').length ? 1 : 0;
	var lGeneral = $('#comments_filter_general:checked').length ? 1 : 0;
	var lInline = $('#comments_filter_inline:checked').length ? 1 : 0;
	var lUsersListIsAvailable = $('input[name="display_user_change"]').length ? 1 : 0;
	var lSelectedUsers = new Array();
	$('input[name="display_user_change"]:checked').each(function() {
		lSelectedUsers.push($(this).val());
	});
	$.ajax({
		url : gCommentAjaxSrvUrl,
		dataType : 'json',
		data : {
			version_id : gCommentsVersionId,
			document_id : gCommentsDocumentId,
			action : 'get_filtered_ids_list',
			display_resolved : lResolved,
			display_general : lGeneral,
			display_inline : lInline,
			filter_users : lUsersListIsAvailable,
			selected_users : lSelectedUsers
		},
		success : function(pAjaxResult) {
			if(pAjaxResult['err_cnt']){
				alert(pAjaxResult['err_msg']);
				return;
			}
			gFilterRootComments = true;
			gVisibleRootCommentIds = pAjaxResult['visible_rootids'];
			if(gCurrentActiveCommentId){
				if(!CheckIfRootCommentIsVisible(gCurrentActiveCommentId)){
					DeactivateAllComments();
				}
			}
			positionCommentsBase();
		}
	});
}


function InitFreezeResizeEvent(){
	$("#CommentsFreeze").bind("resize", function(){
		var lHeight = $(this).outerHeight();
		$(this).parent().css('padding-top', lHeight + 'px');
		positionCommentsBase();
	});
}

function ShowExpandCollapseBtns(){
	$('#Comments-Collapse-Expand-Top').show();
}

function SelectPreviousNextComment(pPrevious){
	var lStartNode = gPreviousPreviewSelectionStartNode;
	if(!lStartNode){
		var lSelection = GetPreviewPreviousSelection();
		var lStartOffset;
		if(lSelection){
			if(!lSelection.isBackwards()){
				lStartNode = lSelection.anchorNode;
				lStartOffset = lSelection.anchorOffset;
			}else{
				lStartNode = lSelection.focusNode;
				lStartOffset = lSelection.focusOffset;
			}

			if(lStartNode && lStartNode.nodeType == 1){
				lStartNode = lStartNode.childNodes[lStartOffset];
			}
		}
		if(!lStartNode){
			lStartNode = GetPreviewFirstNode();
		}
	}

	var lResultCommentId = false;
	var lCommentFoundBefore = false;
	var lPreviewContent = GetPreviewContent();

	lPreviewContent.find(gCommentEndPosNodeName).each(function(pIdx) {
		var lCommentId = $(this).attr(gCommentIdAttributeName);
		var lCommentIsVisible = CheckIfRootCommentIsVisible(lCommentId);
		var lCommentEndOrderRelativeToSelectionStart = compareNodesOrder(lStartNode, this);

		if(pPrevious){
			if(lCommentEndOrderRelativeToSelectionStart >= 0 && lCommentFoundBefore){
				// If the comment is after the selection and we have found one
				// before the selection - stop processing the other comments
				return false;
			}
			// If the
			if(lCommentIsVisible){
				lResultCommentId = lCommentId;
				if(lCommentEndOrderRelativeToSelectionStart < 0 && !lCommentFoundBefore){
					lCommentFoundBefore = true;
				}
			}
		}else{
			if(lCommentIsVisible){
				if(!lResultCommentId){
					lResultCommentId = lCommentId;
				}
			}

			if(lCommentEndOrderRelativeToSelectionStart > 0){
				// If the comment is after the selection - it is the first after it
				lResultCommentId = lCommentId;
				return false;
			}
		}
	});

	if(lResultCommentId){
		MakeCommentActive(lResultCommentId);
		scrollToComment(lResultCommentId);
		// Move the selection to the end of the comment
		var lEndCommentNode = lPreviewContent.find(gCommentEndPosNodeName + '[' + gCommentIdAttributeName + '="' + lResultCommentId + '"]')[0];
		gPreviousPreviewSelectionStartNode = lEndCommentNode;
	}
}

function SelectPreviousComment() {
	SelectPreviousNextComment(true);
}

function SelectNextComment() {
	SelectPreviousNextComment(false);
}