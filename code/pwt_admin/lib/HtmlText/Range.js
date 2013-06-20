var _HTMLTEXT_IE = (navigator.userAgent.toLowerCase().indexOf("msie") > -1)?true:false;
var _HTMLTEXT_MZ = (document.implementation && document.implementation.createDocument) ?true:false;
var _HTMLTEXT_OP = navigator.userAgent.toLowerCase().indexOf("opera") != -1;
var _HTMLTEXT_CH= navigator.userAgent.toLowerCase().indexOf('chrome') > -1;
var _HTMLTEXT_SF = navigator.userAgent.toLowerCase().indexOf("safari") != -1 || navigator.userAgent.toLowerCase().indexOf("konqueror") != -1;

Range = function(pTargDoc){
	this.contentDocument=pTargDoc;
	this.selection=this.getSelection();
	this.range=this.getRangeFromSelection(this.selection);
}

Range.prototype.getSelection=function(){
	var selObj;
	if (this.contentDocument.defaultView && this.contentDocument.defaultView.getSelection) {
		selObj = this.contentDocument.defaultView.getSelection();
	} else if (this.contentDocument.selection) { // should come last; Opera!
		selObj = this.contentDocument.selection.createRange();
	}
	
	return selObj;
}

Range.prototype.getRangeFromSelection=function(selObj){
	var rangeObj;
	if (_HTMLTEXT_IE) {
		rangeObj = selObj;
	} else if (selObj.getRangeAt) {
		try{
			rangeObj = selObj.getRangeAt(0);
		}catch(err){
			return null;
		}
	} else {
		rangeObj = this.contentDocument.createRange();
		rangeObj.setStart(selObj.anchorNode, selObj.anchorOffset);
		rangeObj.setEnd(selObj.focusNode, selObj.focusOffset);
	}
	return rangeObj;
}

Range.prototype.createRange = function(){
	if(_HTMLTEXT_IE){
		return this.contentDocument.body.createTextRange();
	}else{
		return this.contentDocument.createRange();
	}
	
}

Range.prototype.compareNodeBound=function(node1,node2){//compare node positions
	if(node1==node2) return 0;
	var range1=this.createRange(),range2=this.createRange();
	this.moveToElementText(range1,node1);
	this.moveToElementText(range2,node2);
	if(_HTMLTEXT_IE){
		if(range1.compareEndPoints("EndToStart",range2)==-1){
			return -1;
		}else if(range1.compareEndPoints("StartToEnd",range2)==1){
			return 1;
		}else{
			return 0;
		}
	}else{
		if(range1.compareBoundaryPoints(1,range2)==-1){
			return -1;
		}else if(range1.compareBoundaryPoints(3,range2)==1){
			return 1;
		}else{
			return 0;
		}
	}

}

Range.prototype.isCollapsed=function(){
	if(_HTMLTEXT_IE && this.range) return !this.range.text;
	else return this.selection=="";

}

Range.prototype.getNextTextNode=function(curNode,end){
	if(this.contentDocument.body==curNode) return curNode;
	while(!curNode.nextSibling){
		curNode=curNode.parentNode;
		if(end==curNode || this.contentDocument.body==curNode) return curNode;
	}
	curNode=curNode.nextSibling;
	if(end==curNode) return curNode;
	while(curNode.firstChild){
		curNode=curNode.firstChild;
		if(end==curNode) return curNode;
	}
	return curNode;
}



Range.prototype.seachSelectionParents=function(tagName){
	//if(this.isCollapsed()) return false;
	var start = this.getAnchorNode();
	var end = this.getFocusNode();
	if(this.compareNodeBound(start,end)==1){
		var tmp = start;
		start=end;
		end=tmp;
	}
	var el=start;
	if(start==end && start.nodeType==3){
		//v edin node sme
		var el2=el;
		while(el2!=this.contentDocument.body){
			if(el2.nodeType==1 && el2.tagName.toLowerCase()==tagName.toLowerCase()) break;
			el2=el2.parentNode;
		}
		if(el2==this.contentDocument.body && el2!=el)return false;
		return true;
	}
	
	while(el.firstChild){
		el=el.firstChild;
	}
	var hasChilds=false;
	while(el!=end && el!=this.contentDocument.body){
		var inRange = this.inRange(el);
		hasChilds=(inRange || hasChilds);
		if(((el.nodeType==3 && el.nodeValue) || el.nodeType==1) && inRange && !this.searchParentsForSingleNode(tagName,el)) return false;
		el=this.getNextTextNode(el,end);
	}
	if(el!=this.contentDocument.body){
		var inRange = this.inRange(el);
		hasChilds=(inRange || hasChilds);
		if(inRange && !this.searchParentsForSingleNode(tagName,el)) return false;
	}
	if(!hasChilds) return false;
	
	return true;
}


Range.prototype.surroundRangeWithTags=function(nodeName, atribs,rangeObj){
	if(!rangeObj) rangeObj=this.range;
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
		node=this.contentDocument.getElementById(tmpnodeid);
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

Range.prototype.extractContents=function(node){
	var range = this.createRange();
	if (_HTMLTEXT_IE) {
		//~ this.moveToElementText(range,node);
		this.setStartBefore(range,node,false);
		this.setEndAfter(range,node,false);
		//~ var tmpdiv = this.contentDocument.createElement("div");
		//~ this.contentDocument.body.appendChild(tmpdiv);
		//~ tmpdiv.innerHTML=range.htmlText;
		while(node.firstChild) node.parentNode.insertBefore(node.firstChild,node);
		node.parentNode.removeChild(node);
		//~ this.contentDocument.body.removeChild(tmpdiv);
		range = null;
	} else {
		range.selectNodeContents(node);
		node.parentNode.replaceChild(range.extractContents(), node);
		range.detach();
	}

}

Range.prototype.inRange=function(el){
	if(_HTMLTEXT_IE || _HTMLTEXT_OP){
		var range = this.createRange();
		this.moveToElementText(range,el);
		var res;
		if(_HTMLTEXT_IE){
			res = ((this.range.compareEndPoints("StartToEnd",range)!=1 && this.range.compareEndPoints("EndToEnd",range)!=-1) || (this.range.compareEndPoints("StartToStart",range)!=1 && this.range.compareEndPoints("EndToStart",range)!=-1));
		}else{
			res=((this.range.compareBoundaryPoints(0,range)!=1 && this.range.compareBoundaryPoints(1,range)==1) || (this.range.compareBoundaryPoints(2,range)!=-1 && this.range.compareBoundaryPoints(3,range)==-1));
		}
		return res;
	}else{
		return this.selection.containsNode(el,true);
	}
	
}

Range.prototype.setStartBefore=function(range,node,inNode){
	if(inNode==null || typeof(inNode)=="undefined") inNode=true;
	if(!range) range=this.range;
	if(_HTMLTEXT_IE){
		var bookmark, removeBookmark=0;
		
		//~ if(node.nodeType==3){
			bookmark = this.contentDocument.createElement("div");
			bookmark.style.display="none";
			bookmark.innerHTML="a<div>a</div>a";
			removeBookmark=1;
			if(!inNode || node.nodeType==3){
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

Range.prototype.setStartAfter=function(range,node,inNode){
	if(inNode==null || typeof(inNode)=="undefined") inNode=true;
	if(!range) range=this.range;
	if(_HTMLTEXT_IE){
		var bookmark, removeBookmark=0;
		//~ var tmpRange = this.contentDocument.body.createTextRange();
		//~ if(node.nodeType==3){
			bookmark = this.contentDocument.createElement("div");
			bookmark.style.display="none";
			bookmark.innerHTML="a<div>a</div>a";
			removeBookmark=1;
			if(!inNode || node.nodeType==3){
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


Range.prototype.setEndBefore=function(range,node,inNode){
	if(inNode==null || typeof(inNode)=="undefined") inNode=true;
	if(!range) range=this.range;
	if(_HTMLTEXT_IE){
		var bookmark, removeBookmark=0;
		//~ var tmpRange = this.contentDocument.body.createTextRange();
		//~ if(node.nodeType==3){
			bookmark = this.contentDocument.createElement("div");
			bookmark.style.display="none";
			bookmark.innerHTML="aaa<br/>a<div>a</div>a";
			removeBookmark=1;
			if(!inNode || node.nodeType==3){
				bookmark = node.parentNode.insertBefore(bookmark,node);
			}else{
				if(node.firstChild || node.nodeType==3){
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

Range.prototype.setEndAfter=function(range,node,inNode){
	if(inNode==null || typeof(inNode)=="undefined") inNode=true;
	if(!range) range=this.range;
	if(_HTMLTEXT_IE){
		var bookmark, removeBookmark=0;
		var tmpRange = this.contentDocument.body.createTextRange();
		//~ if(node.nodeType==3){
			bookmark = this.contentDocument.createElement("div");
			bookmark.style.display="none";
			bookmark.innerHTML="a<div>a</div>a";
			removeBookmark=1;
			if(!inNode  || node.nodeType==3){
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

Range.prototype.toString=function(range){
	if(!range) range=this.range;
	
	if(_HTMLTEXT_IE) return range.text;
	return range.toString();
}

/*
Range.prototype.setEndPoint=function(range,node,bStart){
	if(!range) range=this.range;
	if(_HTMLTEXT_IE){
		var bookmark, removeBookmark=0;
		var tmpRange = this.contentDocument.body.createTextRange();
		if(node.nodeType==3){
			bookmark = this.contentDocument.createElement("a");
			removeBookmark=1;
			if(bStart){
				if(node.text=='')node=node.nextSibling;
				bookmark = node.parentNode.insertBefore(bookmark,node);
			}else{
				if(node.nextSibling){
					bookmark = node.parentNode.insertBefore(bookmark,node.nextSibling);
				}else{
					bookmark = node.parentNode.appendChild(bookmark);
				}
			}
		}else
			bookmark = node;
		tmpRange.moveToElementText(bookmark);
		range.setEndPoint((bStart? "StartToEnd" : "EndToStart"),tmpRange);
		if(removeBookmark) bookmark.parentNode.removeChild(bookmark);
	}else{
		if(bStart){
			range.setStartBefore(node);
		}else{
			range.setEndAfter(node);
		}
	
	}
}
*/
Range.prototype.moveToElementText=function(range,node){
	if(!range) range=this.range;
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

Range.prototype.searchParentsForSingleNode=function(tagName,el){
	while(el!=this.contentDocument.body){
		if(el.nodeType==1 && el.tagName.toLowerCase()==tagName.toLowerCase()) return true;
		el=el.parentNode;
	}
	return false;
}
Range.prototype.getAnchorNode=function(){
	if(!this.anchorNode){
		if(_HTMLTEXT_IE && this.range){
			var cRange=this.range.duplicate();
			cRange.collapse(true);
			this.anchorNode=cRange.parentElement();
			while(this.anchorNode.firstChild) this.anchorNode=this.anchorNode.firstChild;
		}else
			this.anchorNode = this.selection.anchorNode;
	}
	return this.anchorNode;
}

Range.prototype.getFocusNode=function(){
	if(!this.focusNode){
		if(_HTMLTEXT_IE && this.range){
			var cRange=this.range.duplicate();
			cRange.collapse(false);
			this.focusNode=cRange.parentElement();
			while(this.focusNode.firstChild) this.focusNode=this.focusNode.firstChild;
		}else
			this.focusNode = this.selection.focusNode;
	}
	return this.focusNode;
}
