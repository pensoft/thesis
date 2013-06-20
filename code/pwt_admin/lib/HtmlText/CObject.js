
function CObject(pDoc){
	this.contextMenuOptions={};
	this.contextMenuConf=[];
	if(pDoc) this.construct(pDoc);
}

CObject.prototype.construct=function(pDoc){
	this.contentDocument=pDoc;
	this.range=new Range(pDoc);
}

CObject.prototype.refreshSelection=function(){
	this.range=new Range(this.contentDocument);
}

CObject.prototype.findElement=function(tagName){
	var el=this.target;
	while(el && el!=this.contentDocument.body){
		if(el && el.nodeType==1 && el.nodeName.toLowerCase()==tagName.toLowerCase()){
			break;
		}
		el=el.parentNode;
	}
	if(!el || el.nodeName.toLowerCase()!=tagName.toLowerCase() ) return null;
	return el;
}

CObject.prototype.Delete=function(pTagName){
	var el;
	if(!(el=this.findElement(pTagName))) return false;
	el.parentNode.removeChild(el);
	return false;
}
