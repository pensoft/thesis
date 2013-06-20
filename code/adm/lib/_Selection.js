Selection = function(pDocument){
	this.selection = false;
	this.contentDocument = pDocument;
	this.is_ie = false;
	this.initSelection();
}

Selection.prototype.initSelection = function(){
	if (this.contentDocument.defaultView && this.contentDocument.defaultView.getSelection){
		this.selection = this.contentDocument.defaultView.getSelection();
	}else if (this.contentDocument.getSelection){
		this.selection = this.contentDocument.getSelection();		
	}else if (this.contentDocument.selection){	
		this.is_ie = true;
		this.selection = this.contentDocument.selection.createRange();				
	}else {
	}
			
}

Selection.prototype.getStartNode = function(){
	if( this.is_ie ){
		this.selection.collapse(false)		
		return this.selection.parentElement();
	}else{
		return this.selection.anchorNode;
	}
}

Selection.prototype.getEndNode = function(){
	if( this.is_ie ){
		this.selection.collapse(true)		
		return this.selection.parentElement();
	}else{
		return this.selection.focusNode;
	}	
}

Selection.prototype.getStartOffset = function(){
	return this.selection.anchorOffset;
}

Selection.prototype.getEndOffset = function(){
	return this.selection.focusOffset;
}

Selection.prototype.changeSelection = function(pNode, pOffset){	
	this.selection.extend(pNode,pOffset);
	this.selection.collapseToEnd();	
}

Selection.prototype.isEmpty = function(){	
	return this.selection.isCollapsed;
}