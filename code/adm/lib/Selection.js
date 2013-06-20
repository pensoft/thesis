var gXmlTextNodeType = 3;
var gXmlElementNodeType = 1;

var _HTMLTEXT_IE = (navigator.userAgent.toLowerCase().indexOf("msie") > -1) ? true:false;
var _HTMLTEXT_MZ = (document.implementation && document.implementation.createDocument) ? true:false;
var _HTMLTEXT_OP = navigator.userAgent.toLowerCase().indexOf("opera") != -1;
var _HTMLTEXT_CH = navigator.userAgent.toLowerCase().indexOf('chrome') > -1;
var _HTMLTEXT_SF = navigator.userAgent.toLowerCase().indexOf("safari") != -1 || navigator.userAgent.toLowerCase().indexOf("konqueror") != -1;

Selection = function(pTargDoc){
	this.contentDocument = pTargDoc;
	this.selection = this.getSelection();
	try{
		this.range = this.getRangeFromSelection(this.selection);
	}catch(e){
		this.range = this.createRange();
	}
	this.anchorNode = this.getAnchorNode();
	this.focusNode = this.getFocusNode();
	this.anchorOffset = this.getAnchorOffset();
	this.focusOffset = this.getFocusOffset();
	this.invertDirection();	
}

Selection.prototype.getSelection = function(){
	var selObj;
	if (this.contentDocument.defaultView && this.contentDocument.defaultView.getSelection) {
		selObj = this.contentDocument.defaultView.getSelection();
	} else if (this.contentDocument.selection) { // should come last; Opera!
		selObj = this.contentDocument.selection.createRange();
	}
	
	return selObj;
}

Selection.prototype.getRange = function(){
	if( !this.range )		
		this.range = this.getRangeFromSelection(this.selection);
	return this.range;
}	

Selection.prototype.getRangeFromSelection = function(selObj){
	var rangeObj;
	if (_HTMLTEXT_IE) {
		rangeObj = selObj;
	} else if (selObj.getRangeAt) {
		rangeObj = selObj.getRangeAt(0);
	} else {
		rangeObj = this.contentDocument.createRange();
		rangeObj.setStart(selObj.anchorNode, selObj.anchorOffset);
		rangeObj.setEnd(selObj.focusNode, selObj.focusOffset);
	}
	return rangeObj;
}

Selection.prototype.createRange = function(){
	if(_HTMLTEXT_IE){
		return this.contentDocument.body.createTextRange();
	}else{
		return this.contentDocument.createRange();
	}
	
}

Selection.prototype.compareNodeBound = function(node1,node2){//compare node positions
	if(node1 == node2) return 0;
	var range1 = this.createRange(),range2 = this.createRange();
	this.moveToElementText(range1,node1);
	this.moveToElementText(range2,node2);
	if(_HTMLTEXT_IE){
		if(range1.compareEndPoints("EndToStart",range2) == -1){
			return -1;
		}else if(range1.compareEndPoints("StartToEnd",range2) == 1){
			return 1;
		}else{
			return 0;
		}
	}else{
		if(range1.compareBoundaryPoints(1,range2) == -1){
			return -1;
		}else if(range1.compareBoundaryPoints(3,range2) == 1){
			return 1;
		}else{
			return 0;
		}
	}

}

Selection.prototype.invertDirection = function(){
	var lCompResult = this.compareNodeBound(this.getAnchorNode(), this.getFocusNode());
	if( lCompResult > 0 ){		
		this.getFocusOffset();
		this.getAnchorOffset();
		
		var lTmp = this.anchorNode;
		this.anchorNode = this.focusNode;
		this.focusNode = lTmp;
		
		lTmp = this.anchorOffset;
		this.anchorOffset = this.focusOffset;
		this.focusOffset = lTmp;
	}else if( lCompResult == 0 ){//1 node
		this.getFocusOffset();
		this.getAnchorOffset();
		if( this.anchorOffset > this.focusOffset ){
			lTmp = this.anchorOffset;
			this.anchorOffset = this.focusOffset;
			this.focusOffset = lTmp;
		}
	}
}

Selection.prototype.isCollapsed = function(){
	if(_HTMLTEXT_IE && this.range) return this.range.text == "";
	else return this.selection == "";

}

Selection.prototype.getNextTextNode = function(curNode,end){
	if(this.contentDocument.body == curNode) return curNode;
	while(!curNode.nextSibling){
		curNode = curNode.parentNode;
		if(end == curNode || this.contentDocument.body == curNode) return curNode;
	}
	curNode = curNode.nextSibling;
	if(end == curNode) return curNode;
	while(curNode.firstChild){
		curNode = curNode.firstChild;
		if(end == curNode) return curNode;
	}
	return curNode;
}



Selection.prototype.searchSelectionParents = function(tagName){
	//if(this.isCollapsed()) return false;
	var start = this.getAnchorNode();
	var end = this.getFocusNode();
	if(this.compareNodeBound(start,end) == 1){
		var tmp = start;
		start = end;
		end = tmp;
	}
	var el = start;
	if(start == end && start.nodeType == 3){
		//v edin node sme
		var el2 = el;
		while(el2!= this.contentDocument.body){
			if(el2.nodeType == 1 && el2.tagName.toLowerCase() == tagName.toLowerCase()) break;
			el2 = el2.parentNode;
		}
		if(el2 == this.contentDocument.body && el2!= el)return false;
		return true;
	}
	
	while(el.firstChild){
		el = el.firstChild;
	}
	var hasChilds = false;
	while(el!= end && el!= this.contentDocument.body){
		var inRange = this.inRange(el);
		hasChilds = (inRange || hasChilds);
		if(((el.nodeType == 3 && el.nodeValue) || el.nodeType == 1) && inRange && !this.searchParentsForSingleNode(tagName,el)) return false;
		el = this.getNextTextNode(el,end);
	}
	if(el!= this.contentDocument.body){
		var inRange = this.inRange(el);
		hasChilds = (inRange || hasChilds);
		if(inRange && !this.searchParentsForSingleNode(tagName,el)) return false;
	}
	if(!hasChilds) return false;
	
	return true;
}


Selection.prototype.surroundRangeWithTags = function(nodeName, atribs,rangeObj){
	if(!rangeObj) rangeObj = this.range;
	var dt = new Date();
	var tmpnodeid = nodeName + '_' + dt.getTime() + '_' + Math.ceil(Math.random() * 1000);
	if (_HTMLTEXT_IE) {
		var lAttr = '';
		for(var i in atribs){
			if(atribs[i].name && atribs[i].val) {
				lAttr += ' ' + atribs[i].name + '="' + atribs[i].val + '"';
			}
		}
		var node = '<' + nodeName + lAttr + ' id="' + tmpnodeid + '">' + rangeObj.htmlText + '</' + nodeName + '>';
		rangeObj.pasteHTML(node);
	} else {
		var node = this.contentDocument.createElement(nodeName);
		node.setAttribute('id', tmpnodeid);
		for(var i in atribs){
			if(atribs[i].name && atribs[i].val) {
				node.setAttribute(atribs[i].name, atribs[i].val);
			}
		}
		node.appendChild(rangeObj.extractContents());
		rangeObj.insertNode(node);
		rangeObj.selectNode(node);
	}
	
	return node;

}

Selection.prototype.extractContents = function(node){
	var range = this.createRange();
	if (_HTMLTEXT_IE) {
		//~ this.moveToElementText(range,node);
		this.setStartBefore(range,node,false);
		this.setEndAfter(range,node,false);
		//~ var tmpdiv = this.contentDocument.createElement("div");
		//~ this.contentDocument.body.appendChild(tmpdiv);
		//~ tmpdiv.innerHTML=Selection.htmlText;
		while(node.firstChild) 
			node.parentNode.insertBefore(node.firstChild,node);
		node.parentNode.removeChild(node);
		//~ this.contentDocument.body.removeChild(tmpdiv);
		range = null;
	} else {
		Selection.selectNodeContents(node);
		node.parentNode.replaceChild(Selection.extractContents(), node);
		Selection.detach();
	}

}

Selection.prototype.inRange = function(el){
	if(_HTMLTEXT_IE || _HTMLTEXT_OP){
		var range = this.createRange();
		this.moveToElementText(range,el);
		var res;
		if(_HTMLTEXT_IE){
			res = ((this.range.compareEndPoints("StartToEnd",range)!= 1 && this.range.compareEndPoints("EndToEnd",range)!= -1) || (this.range.compareEndPoints("StartToStart",range)!= 1 && this.range.compareEndPoints("EndToStart",range)!= -1));
		}else{
			res = ((this.range.compareBoundaryPoints(0,range)!= 1 && this.range.compareBoundaryPoints(3,range)!= -1) || (this.range.compareBoundaryPoints(2,range)!= -1 && this.range.compareBoundaryPoints(3,range)!= 1));
		}
		return res;
	}else{
		return this.selection.containsNode(el,true);
	}
	
}

Selection.prototype.setStartBefore=function(range,node,inNode){
	if(inNode == null || typeof(inNode) == "undefined") 
		inNode=true;
	if(!range) 	
		range = this.range;
	if(_HTMLTEXT_IE){
		var bookmark, removeBookmark = 0;
		
		//~ if(node.nodeType==3){
			bookmark = this.contentDocument.createElement("div");
			bookmark.style.display="none";
			bookmark.innerHTML = "a<div>a</div>a";
			removeBookmark=1;
			if(!inNode || node.nodeType == 3){
				bookmark = node.parentNode.insertBefore(bookmark,node);
			}else{
				if(node.firstChild || node.nodeType==3){
					bookmark = node.insertBefore(bookmark,node.firstChild);
				}else{
						bookmark = node.appendChild(bookmark);
				}
			
			}
			var tmpRange = this.createRange();
			//~ bookmark=bookmark.parentNode.replaceChild(bookmark);
			//~ bookmark.innerHTML="test<br/>asdasd";
		//~ }else
			//~ bookmark = node;
		tmpRange.moveToElementText(bookmark.childNodes[1]);
		//~ this.moveToElementText(tmpRange,node);
		range.setEndPoint("StartToStart",tmpRange);
		if(removeBookmark) bookmark.parentNode.removeChild(bookmark);
	}else
		range.setStartBefore(node);//da se napravi da raboti s inNode
}

Selection.prototype.setStartAfter=function(range,node,inNode){
	if(inNode==null || typeof(inNode)=="undefined") 
		inNode = true;
	if(!range) 
		range = this.range;
	if(_HTMLTEXT_IE){
		var bookmark, removeBookmark=0;
		//~ var tmpRange = this.contentDocument.body.createTextRange();
		//~ if(node.nodeType==3){
			bookmark = this.contentDocument.createElement("div");
			bookmark.style.display = "none";
			bookmark.innerHTML = "a<div>a</div>a";
			removeBookmark=1;
			if(!inNode || node.nodeType == 3){
				if(node.nextSibling){
					bookmark = node.parentNode.insertBefore(bookmark,node.nextSibling);
				}else{
					bookmark = node.parentNode.appendChild(bookmark);
				}
			}else{	
				bookmark = node.appendChild(bookmark);
			}
			var tmpRange = this.createRange();
		//~ }else
			//~ bookmark = node;
		//~ tmpRange.moveToElementText(bookmark);
		tmpRange.moveToElementText(bookmark.childNodes[1]);
		range.setEndPoint("StartToEnd",tmpRange);
		if(removeBookmark) bookmark.parentNode.removeChild(bookmark);
	}else
		range.setStartAfter(node);
}


Selection.prototype.setEndBefore = function(range,node,inNode){
	if(inNode == null || typeof(inNode) == "undefined") 
		inNode = true;
	if(!range) 
		range = this.range;
	if(_HTMLTEXT_IE){
		var bookmark, removeBookmark=0;
		//~ var tmpRange = this.contentDocument.body.createTextRange();
		//~ if(node.nodeType==3){
			bookmark = this.contentDocument.createElement("div");
			bookmark.style.display = "none";
			bookmark.innerHTML = "aaa<br/>a<div>a</div>a";
			removeBookmark = 1;
			if(!inNode || node.nodeType == 3){
				bookmark = node.parentNode.insertBefore(bookmark,node);
			}else{
				if(node.firstChild || node.nodeType == 3){
					bookmark = node.insertBefore(bookmark,node.firstChild);
				}else{
					bookmark = node.appendChild(bookmark);
				}
			
			}
		//~ }else
			//~ bookmark = node;
		//~ tmpRange.moveToElementText(bookmark);
		var tmpRange = this.createRange();
		tmpRange.moveToElementText(bookmark.childNodes[3]);
		range.setEndPoint("EndToStart",tmpRange);
		if(removeBookmark) bookmark.parentNode.removeChild(bookmark);
	}else
		range.setEndBefore(node);
}

Selection.prototype.setEndAfter = function(range,node,inNode){
	if(inNode == null || typeof(inNode) == "undefined") 
		inNode = true;
	if(!range) 
		range = this.range;
	if(_HTMLTEXT_IE){
		var bookmark, removeBookmark = 0;
		var tmpRange = this.contentDocument.body.createTextRange();
		//~ if(node.nodeType==3){
			bookmark = this.contentDocument.createElement("div");
			bookmark.style.display = "none";
			bookmark.innerHTML = "a<div>a</div>a";
			removeBookmark = 1;
			if(!inNode  || node.nodeType == 3){
				if(node.nextSibling){
					bookmark = node.parentNode.insertBefore(bookmark,node.nextSibling);
				}else{
					bookmark = node.parentNode.appendChild(bookmark);
				}
			}else{
				bookmark = node.appendChild(bookmark);
			}
			var tmpRange = this.createRange();
		//~ }else
			//~ bookmark = node;
		//~ tmpRange.moveToElementText(bookmark);
		tmpRange.moveToElementText(bookmark.childNodes[1]);
		range.setEndPoint("EndToEnd",tmpRange);
		if(removeBookmark) bookmark.parentNode.removeChild(bookmark);
	}else
		range.setEndAfter(node);
}

Selection.prototype.toString=function(range){
	if(!range) 
		range = this.range;
	
	if(_HTMLTEXT_IE) 
		return range.text;
	return range.toString();
}


Selection.prototype.moveToElementText=function(range,node){
	if(!range) 
		range = this.range;
	if(_HTMLTEXT_IE){
		//~ if(node.nodeType==3){
			this.setStartBefore(range,node);
			this.setEndAfter(range,node);
		//~ }else{
			//~ range.moveToElementText(node);
		//~ }
	}else{
		range.selectNodeContents(node);
	}
}

Selection.prototype.searchParentsForSingleNode = function(tagName,el){
	while(el!= this.contentDocument.body){
		if(el.nodeType == 1 && el.tagName.toLowerCase() == tagName.toLowerCase()) return true;
		el = el.parentNode;
	}
	return false;
}

Selection.prototype.getAnchorNode=function(){
	if(!this.anchorNode){
		if(_HTMLTEXT_IE && this.range){
			var cRange = this.range.duplicate();
			cRange.collapse(true);
			this.anchorNode = cRange.parentElement();
			while(this.anchorNode.firstChild) 
				this.anchorNode = this.anchorNode.firstChild;
		}else
			this.anchorNode = this.selection.anchorNode;
	}
	return this.anchorNode;
}

Selection.prototype.getFocusNode=function(){
	if(!this.focusNode){
		if(_HTMLTEXT_IE && this.range){
			var cRange = this.range.duplicate();
			cRange.collapse(false);
			this.focusNode=cRange.parentElement();
			while(this.focusNode.firstChild) 
				this.focusNode = this.focusNode.firstChild;
		}else
			this.focusNode = this.selection.focusNode;
	}
	return this.focusNode;
}

Selection.prototype.getAnchorOffset = function(){
	if(typeof(this.anchorOffset) == 'undefined' || this.anchorOffset == null){
		if(_HTMLTEXT_IE && this.range){
			//To Do
		}else
			this.anchorOffset = this.selection.anchorOffset;
	}
	return this.anchorOffset;
}

Selection.prototype.getFocusOffset = function(){
	if(typeof(this.focusOffset) == 'undefined' || this.focusOffset == null){
		if(_HTMLTEXT_IE && this.range){
			//To Do
		}else
			this.focusOffset = this.selection.focusOffset;
	}
	return this.focusOffset;
}

Selection.prototype.getStartNode = function(){
	return this.anchorNode;
}

Selection.prototype.getEndNode = function(){
	return this.focusNode;	
}

Selection.prototype.getStartOffset = function(){
	return this.anchorOffset;
}

Selection.prototype.getEndOffset = function(){
	return this.selection.focusOffset;
}

Selection.prototype.isEmpty = function(){	
	return this.isCollapsed();
}


Selection.prototype.setStartPoint = function(pNode, pOffset){
	var lNodeDetails = this.getRealNode(pNode, pOffset);
	var lNode = lNodeDetails[0];
	var lOffset = lNodeDetails[1];
	this.range.setStart(lNode, lOffset);
	this.anchorNode = lNode;	
	this.anchorOffset = lOffset;	
	
	this.initSelectionToRange();
}

Selection.prototype.setEndPoint = function(pNode, pOffset){
	var lNodeDetails = this.getRealNode(pNode, pOffset);
	var lNode = lNodeDetails[0];
	var lOffset = lNodeDetails[1];
	this.range.setEnd(lNode, lOffset);		
	this.focusNode = lNode;	
	this.focusOffset = lOffset;
	
	this.initSelectionToRange();
}
//Ako e predaden node koito ne e tekstov - namira tekstoviq node, koito otgovarq na offset-a
Selection.prototype.getRealNode = function(pNode, pOffset){
	if( pOffset < 0 )
		pOffset = 0;
	if( pOffset > pNode.textContent.length )
		pOffset = pNode.textContent.length;
	if( pNode.nodeType == gXmlTextNodeType ){
		return new Array(pNode, pOffset);
	}else{
		var lOffset = pOffset;
		for( var i = 0; i < pNode.childNodes.length; ++i){
			var lChild = pNode.childNodes[i];
			if( lChild.textContent.length >= lOffset )
				return this.getRealNode(lChild, lOffset);
			lOffset = lOffset - lChild.textContent.length;
		}
	}
	return new Array(pNode, pOffset);
}

Selection.prototype.initSelectionToRange = function(){
	this.selection.removeAllRanges();
	this.selection.addRange(this.range);
}

//Ako selectiona e zapo4nal ot kraq na daden tekstov node - mesti go do na4aloto na sledvashtiq neprazen tekstov node
//Ako selectiona e svyrshil v nachaloto na daden tekstov node - mesti go do kraq na predhodniq neprazen tekstov node
Selection.prototype.correctStartAndEndNodes = function(){
	if( this.isCollapsed() )
		return;
	var lStartNode = this.anchorNode;
	var lStartOffset = this.anchorOffset;
	var lEndNode = this.focusNode;
	var lEndOffset = this.focusOffset;
	if( lStartNode.nodeType == gXmlElementNodeType ){
		var lCurrentNode = lStartNode;
		for( var i = 0; i < lCurrentNode.childNodes.length; ++i){
			if( i == lStartOffset ){
				var lChild = lStartNode.childNodes[i];
				var lTextNode;
				if( lChild.nodeType != gXmlTextNodeType ){	
					lTextNode = this.getFirstTextNodeChild(lChild);
					if( !lTextNode ){
						lTextNode = this.getNextTextNode(lChild);
					}
				}else{
					lTextNode = lChild;
				}
				if( lTextNode ){
					this.setStartPoint(lTextNode, 0);					
				}
			}
			
		}
		
	}
	if( lEndNode.nodeType == gXmlElementNodeType ){
		var lCurrentNode = lEndNode;
		for( var i = 0; i < lEndNode.childNodes.length; ++i){
			if( i == lEndOffset - 1 ){
				var lChild = lEndNode.childNodes[i];
				var lTextNode;
				if( lChild.nodeType != gXmlTextNodeType ){	
					lTextNode = this.getLastTextNodeChild(lChild);
					if( !lTextNode ){
						lTextNode = this.getPreviousTextNode(lChild);
					}
				}else{
					lTextNode = lChild;
				}
				if( lTextNode ){
					this.setEndPoint(lTextNode, lTextNode.textContent.length);					
				}
			}
			
		}
		
	}
	if( this.anchorNode.nodeType == gXmlTextNodeType && this.anchorOffset == this.anchorNode.textContent.length ){
		var lCurrentNode = this.getNextTextNode(this.anchorNode) ;
		while( lCurrentNode && lCurrentNode != this.offsetNode ){
			if( lCurrentNode.textContent.length > 0 )
				break;
			lCurrentNode = this.getNextTextNode(lCurrentNode);
		}
		if( lCurrentNode ){		
			this.setStartPoint(lCurrentNode, 0);
		}
	}
	
	if(this.anchorNode.nodeType == gXmlTextNodeType && this.focusOffset == 0 ){
		var lCurrentNode = this.getPreviousTextNode(this.focusNode);
		while( lCurrentNode && lCurrentNode != this.anchorNode ){
			if( lCurrentNode.textContent.length > 0 )
				break;
			lCurrentNode = this.getPreviousTextNode(lCurrentNode);
		}
		if( lCurrentNode ){
			this.setEndPoint(lCurrentNode, lCurrentNode.textContent.length);			
		}
	}
}

Selection.prototype.getNextTextNode = function(pNode){
	var lNextSibling = false;
	var lParent = pNode;
	while( lParent ){
		lNextSibling = lParent.nextSibling;
		while( lNextSibling ){
			if( lNextSibling.nodeType == gXmlTextNodeType )
				return lNextSibling;
			if( lNextSibling.nodeType == gXmlElementNodeType ){
				var lTextNode = this.getFirstTextNodeChild(lNextSibling);
				if( lTextNode )
					return lTextNode;
			}
			lNextSibling = lNextSibling.nextSibling;
		}
		lParent = lParent.parentNode;
	}
	return false;
	
}

//Връща предходният текстов node преди pNode
Selection.prototype.getPreviousTextNode = function(pNode){
	var lPreviousSibling = false;
	var lParent = pNode;
	while( lParent ){
		lPreviousSibling = lParent.previousSibling;
		while( lPreviousSibling ){
			if( lPreviousSibling.nodeType == gXmlTextNodeType )
				return lPreviousSibling;
			if( lPreviousSibling.nodeType == gXmlElementNodeType ){
				var lTextNode = this.getLastTextNodeChild(lPreviousSibling);
				if( lTextNode )
					return lTextNode;
			}
			lPreviousSibling = lPreviousSibling.previousSibling;
		}
		lParent = lParent.parentNode;
	}
	return false;
	
}

//Връща първият текстов node, който е наследник на pNode
Selection.prototype.getFirstTextNodeChild = function(pNode){
	for( var i = 0; i < pNode.childNodes.length; ++i){
		var lChild = pNode.childNodes[i];
		if( lChild.nodeType == gXmlTextNodeType )
			return lChild;
		if( lChild.nodeType == gXmlElementNodeType ){
			var lTextNode = this.getFirstTextNodeChild(lChild);
			if( lTextNode )
				return lTextNode;
		}
	}
	return false;
}
//Връща последният текстов node, който е наследник на pNode
Selection.prototype.getLastTextNodeChild = function(pNode){
	for( var i = pNode.childNodes.length - 1; i >= 0; --i){
		var lChild = pNode.childNodes[i];
		if( lChild.nodeType == gXmlTextNodeType )
			return lChild;
		if( lChild.nodeType == gXmlElementNodeType ){
			var lTextNode = this.getLastTextNodeChild(lChild);
			if( lTextNode )
				return lTextNode;
		}
	}
	return false;
}