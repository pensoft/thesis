function EWorkaround(){};

EWorkaround.setXslParameter = function(p_xsldoc, p_xslparam, p_xslvalue) {
	p_xsldoc.setProperty("SelectionNamespaces", "xmlns:xsl='http://www.w3.org/1999/XSL/Transform'");
	lparamnode = p_xsldoc.selectSingleNode("//xsl:variable[@name='" + p_xslparam + "']");
	if (lparamnode) lparamnode.text=p_xslvalue;
};

EWorkaround.setNodeText = function (node, value) {
	var val= (!value &&  (value==null || typeof(value)=="undefined")) ? '' : value;
	if (node.childNodes) {
		for (var i = 0; i < node.childNodes.length; i++) {
			if (node.childNodes.item(i).nodeType == 3) {
				node.childNodes.item(i).nodeValue = val;
				return;
			}
		}
	}
	var tmpnode = node.ownerDocument.createTextNode(val);
	node.appendChild(tmpnode);
};

EWorkaround.loadXsl = function(p_xsldoc, p_xslurl, p_is_addxsl) {
	var l_xsldoc;
	if(p_is_addxsl){
		if (_EWORKAROUND_IE ||  _EWORKAROUND_OP ||  _EWORKAROUND_CH || _EWORKAROUND_SF) {
			l_xsldoc = new EWorkaround.newDomDocument();
			l_xsldoc.async = false;
		}else{
			var tmpNode = p_xsldoc.createNode( 1, 'xsl:include', "http://www.w3.org/1999/XSL/Transform"  ) ;
			tmpNode.setAttribute('href', p_xslurl);
			p_xsldoc.documentElement.appendChild(tmpNode) ;
			return;
		}
	} else{
		l_xsldoc = p_xsldoc;
	}
	l_xsldoc.load(p_xslurl);
	if (_EWORKAROUND_IE ||  _EWORKAROUND_OP ||  _EWORKAROUND_CH || _EWORKAROUND_SF) {
		_EWorkaround_check_Xsl(l_xsldoc,p_xslurl);
	} ;
	if(p_is_addxsl){
		var l_func_templ = p_xsldoc.selectSingleNode("//xsl:template[@name='function']");
		if(l_func_templ) l_func_templ.parentNode.removeChild(l_func_templ);
		for(var j=0;j<l_xsldoc.documentElement.childNodes.length; j++) {
			p_xsldoc.documentElement.appendChild(l_xsldoc.documentElement.childNodes[j].cloneNode(true));
		}
	
	}
};

function _EWorkaround_check_Xsl(p_xsldoc,p_xslurl) {
	if (_EWORKAROUND_IE ) {
		p_xsldoc.setProperty("SelectionLanguage", "XPath");
		p_xsldoc.setProperty("SelectionNamespaces", "xmlns:xsl='http://www.w3.org/1999/XSL/Transform'");
	}
	var listN=p_xsldoc.selectNodes("//xsl:*[local-name() = 'import' or local-name() = 'include']"); 
	for(var i=0;i < listN.length;i++){
		var nd=listN.item(i);
		var href=nd.getAttribute("href");
		var lch_href=_EWorkaround_get_href(p_xslurl, href);
		var lch_xsl=EWorkaround.newDomDocument();
		lch_xsl.async=false;
		lch_xsl.load(lch_href);
		_EWorkaround_check_Xsl(lch_xsl,lch_href);
		for(var j=0;j<lch_xsl.documentElement.childNodes.length; j++) {
			nd.parentNode.appendChild(lch_xsl.documentElement.childNodes[j].cloneNode(true));
		}
		nd.parentNode.removeChild(nd);
	}
};
function _EWorkaround_get_href(f, l) {
	var far= f.split("/");
	var lar= l.split("/");
	//f (far[0]=="") 
	if ((lar[0]!=".") && (lar[0]!="..")) return l;
	far.pop();
	if (lar[0]==".") {
		//far[far.length-1]="";
		lar.shift();
	}  else
		while(lar[0]=="..") {
			lar.shift();
			far.pop();
		}
	far[far.length]="";
	var ff=far.join("/");
	//console.dir(far);
	return (far[0]? "/":"")+ff+lar.join("/");
}

EWorkaround.escape = function(pXml){
    return pXml.replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/"/g, "&quot;").replace(/'/g, "&apos;");
};

var _EWORKAROUND_IE = (navigator.userAgent.toLowerCase().indexOf("msie") > -1)?true:false;
var _EWORKAROUND_MZ = (document.implementation && document.implementation.createDocument) ?true:false;
var _EWORKAROUND_OP = navigator.userAgent.toLowerCase().indexOf("opera") != -1;
var _EWORKAROUND_CH= navigator.userAgent.toLowerCase().indexOf('chrome') > -1;
var _EWORKAROUND_SF = navigator.userAgent.toLowerCase().indexOf("safari") != -1 || navigator.userAgent.toLowerCase().indexOf("konqueror") != -1;


EWorkaroundArray.prototype = new Array();
function EWorkaroundArray(i) {
	if (typeof(i) != "undefined") this.length=i;
};
EWorkaroundArray.prototype.item = function(i) {
	return this[i];
};
EWorkaroundArray.prototype.expr = "";

EWorkaround.reloadHistoryFrame = function(){
	if(!_EWORKAROUND_OP && !_EWORKAROUND_CH && !_EWORKAROUND_SF) return;
	if(window.parent.historyframe.window.location.hash!='#0') return;
	window.parent.frames['historyframe'].window.location.href="/lib/historyframe.php?ts=1";
}

if (_EWORKAROUND_IE) {
	_EWORKAROUND_DOM_LATEST_AX=null;
	_EWORKAROUND_REQ_LATEST_AX=null;
	
	
	EWorkaround.getLatestAXVersion=function(pArr) {
		var b = false;
		var res=null;
		for (var i=0; i < pArr.length && !b; i++)
		{
			try
			{
				var tmp = new ActiveXObject(pArr[i]);
				res = pArr[i];
				b = true;
			}
			catch (e) {}
		}
		if (!b)
			throw "Error in getting valid version of ActiveXObject:  exception: "+e;
		return res;
	};
	
	EWorkaround.newXMLHttpRequest = function() {
		if(!_EWORKAROUND_REQ_LATEST_AX){
		   _EWORKAROUND_REQ_LATEST_AX = EWorkaround.getLatestAXVersion(["Msxml2.XMLHTTP.6.0", "Msxml2.XMLHTTP.4.0","MSXML2.XMLHTTP.3.0", "MSXML2.XMLHTTP", "Microsoft.XMLHTTP"]);
		}
		return new ActiveXObject( _EWORKAROUND_REQ_LATEST_AX);
	}
	
	EWorkaround.newDomDocument = function(pUri, pName) {		
		if(!_EWORKAROUND_DOM_LATEST_AX){
		   _EWORKAROUND_DOM_LATEST_AX = EWorkaround.getLatestAXVersion(["Msxml2.DOMDocument.6.0", "Msxml2.DOMDocument.4.0", "Msxml2.DOMDocument.3.0", "MSXML2.DOMDocument", "MSXML.DOMDocument", "Microsoft.XMLDOM"]);
		}
		var eDoc = new ActiveXObject(_EWORKAROUND_DOM_LATEST_AX);
		if (pName){
			if(pUri){
				var prefix = "";
				var i=pName.indexOf(":");
				if (i > 1){
					prefix = pName.substring(0, i);
					pName = pName.substring(i+1); 
				}else{
					prefix = "a1";
				}
				eDoc.loadXML("<" + prefix+":"+pName + " xmlns:" + prefix + "=\"" + pUri + "\"" + " />");
			} else  eDoc.loadXML('<' + pName + " />");
		}
		return eDoc;
	};
	
	
		
} else {
	
	_SYNC_NON_IMPLEMENTED = false; 
	try{ 
		XMLDocument.prototype.async = true;
        	_SYNC_NON_IMPLEMENTED = true;
	}catch(e){};
	
	EWorkaround.newXMLHttpRequest = function() {
		return new XMLHttpRequest();
	};
	
	EWorkaround.newDomDocument = function(pUri, pName){
		var eDoc = document.implementation.createDocument(pUri ? pUri:null, pName ? pName:null, null);
		return eDoc;
	};
	
	XMLDocument.prototype.__copy = function(pDoc) {
		while(this.firstChild)
			this.removeChild(this.firstChild);
		if (pDoc) {
			if ((pDoc.nodeType == Node.DOCUMENT_NODE) || (pDoc.nodeType == Node.DOCUMENT_FRAGMENT_NODE))  {
				var lNodes = pDoc.childNodes;
				for(var i=0;i<lNodes.length;i++) {
					try {
						var ltmp = this.importNode(lNodes[i], true);
						this.appendChild(ltmp);
					} catch (e) {}
				}
			} else if(pDoc.nodeType == Node.ELEMENT_NODE) {
				this.appendChild(this.importNode(pDoc, true));
			}
		}
	};
	
	XMLDocument.prototype.loadXML = function(strXML) {
		var oDoc = null;
		if( strXML ){
			var lParser = new DOMParser();
			oDoc = lParser.parseFromString(strXML, "text/xml");
			if (oDoc.documentElement.nodeName == "parsererror"){
				var lErrStr = oDoc.documentElement.childNodes[0].nodeValue;
				lErrStr = lErrStr.replace(/</g, "&lt;");
				alert('XML Parse Error: ' + lErrStr);
				return false;
			} 
		}
		if( oDoc ){
			this.__copy(oDoc);
			return true;
		}else{
			return false;
		}
	};
	XMLDocument.prototype.__loadorig = XMLDocument.prototype.load;
	XMLDocument.prototype.load = function(pURI)
	{
		this.parseError = 0;
		var res=true;
		try {
			if ( (this.async == false) && _SYNC_NON_IMPLEMENTED) {
				var tmp = new XMLHttpRequest();
				tmp.open("GET", pURI, false);
				tmp.send(null);
				this.__copy(tmp.responseXML);
			}
			else
				this.__loadorig(pURI);
		}
		catch (e) {
			this.parseError = -1;
			res=false;
		}
		return res;
	}; 
	
	
	Document.prototype.transformNodeToObject = function(pxslDoc, oResult)
	{		
		var xsltProcessor = null;
		if ((!pxslDoc) || (!oResult)) {
			throw "No Stylesheet  or Result Document was provided. (original exception: "+e+")";
			return ;
		}
		try {	
			xsltProcessor = new XSLTProcessor();
			if(xsltProcessor.reset)  {
				xsltProcessor.clearParameters();
				xsltProcessor.reset();
				xsltProcessor.importStylesheet(pxslDoc);
				var newFragment = xsltProcessor.transformToFragment(this, document);
				oResult.__copy(newFragment);
			}
			else {
				xsltProcessor.transformDocument(this, pxslDoc, oResult, null);
			}
		}
		catch(e) {
			throw "Failed to transform document. (original exception: "+e+")";
		}
	};
	
	
	Document.prototype.transformNode = function(lxslDoc)
	{		
		var out = document.implementation.createDocument("", "", null);
		this.transformNodeToObject(lxslDoc, out);
		var str = null;
		try {			
			var serializer = new XMLSerializer();
			str = serializer.serializeToString(out);
		}
		catch(e)
		{
			throw "Failed to serialize result document. (original exception: "+e+")";
		}
		return str;
	};
	
	XMLDocument.prototype.setProperty  = function(x,y){};
		
		
	

	EWorkaround.setXpathNamespaces = function(oDoc, sNsSet)
	{
		oDoc.__useCustomResolver = true;
		var namespaces = sNsSet.indexOf(" ")>-1?sNsSet.split(" "):new EWorkaroundArray(sNsSet);
		oDoc.__xpathNamespaces = new EWorkaroundArray(namespaces.length);
		for(var i=0;i < namespaces.length;i++)
		{
			var ns = namespaces[i];
			var colonPos = ns.indexOf(":");
			var assignPos = ns.indexOf("=");
			if(colonPos == 5 && assignPos > colonPos+2)
			{
				var prefix = ns.substring(colonPos+1, assignPos);
				var uri = ns.substring(assignPos+2, ns.length-1);
				oDoc.__xpathNamespaces[prefix] = uri;
			}
			else
			{
				throw "Bad format on namespace declaration(s) given";
			}
		}
	};
	XMLDocument.prototype.__useCustomResolver = false;
	XMLDocument.prototype.__xpathNamespaces = new EWorkaroundArray();
	
	XMLDocument.prototype.selectNodes = function(sExpr, contextNode)
	{
		var nsDoc = this;
		var nodeList;
		if (this.documentElement) {
			var nsresolver = this.__useCustomResolver
					   ? function(prefix)
					     {
						var s = nsDoc.__xpathNamespaces[prefix];
						if(s)return s;
						else throw "No namespace URI found for prefix: '" + prefix+"'"}
					   : this.createNSResolver(this.documentElement);
			var oResult = this.evaluate(sExpr, 
					    (contextNode?contextNode:this), 
					    nsresolver, 
					    XPathResult.ORDERED_NODE_SNAPSHOT_TYPE, null);
			nodeList = new EWorkaroundArray(oResult.snapshotLength);
			
			//~ if (sExpr == '//frm:formdata[1]') {
				//~ alert("selnodes" + nodeList.length);
			//~ }
			for(var i=0;i<nodeList.length;i++)
				nodeList[i] = oResult.snapshotItem(i);
		} else nodeList = new EWorkaroundArray(0);
		nodeList.expr = sExpr;
		return nodeList;
	};
 
	Element.prototype.selectNodes = function(sExpr)
	{
		var doc = this.ownerDocument;
		if(doc.selectNodes)
			return doc.selectNodes(sExpr, this);
		else
			throw "Method selectNodes is only supported by XML Elements";
	};
		
		
	XMLDocument.prototype.selectSingleNode = function(sExpr, contextNode) {
		var ctx = contextNode?contextNode:null;
		sExpr += "[1]";
		var nodeList = this.selectNodes(sExpr, ctx);
		if(nodeList.length > 0)
			return nodeList[0];
		else 
			return null;
	};
	 
	Element.prototype.selectSingleNode = function(sExpr) {
		var doc = this.ownerDocument;
		if(doc.selectSingleNode)
			return doc.selectSingleNode(sExpr, this);
		else
			throw "Method selectNodes is only supported by XML Elements";
	};
	
	if(_EWORKAROUND_CH || _EWORKAROUND_SF){
		Element.prototype.__appendChild = Node.prototype.appendChild;
		Element.prototype.appendChild = function (p_node) {
			if(this.ownerDocument!=p_node.ownerDocument) p_node = this.ownerDocument.importNode(p_node, true);
			return this.__appendChild(p_node);
		}
		
		Element.prototype.__replaceChild = Node.prototype.replaceChild;
		Element.prototype.replaceChild = function (p_newnode, p_oldnode) {
			if(p_oldnode.ownerDocument!=p_newnode.ownerDocument) p_newnode = p_oldnode.ownerDocument.importNode(p_newnode, true);
			return this.__replaceChild(p_newnode, p_oldnode);
		}
	}
		
	XMLDocument.prototype.__defineGetter__("xml", function () { return (new XMLSerializer()).serializeToString(this); });
	
	Node.prototype.__defineGetter__("xml", function () { return (new XMLSerializer()).serializeToString(this); });
	
	XMLDocument.prototype.__defineGetter__("text", function ()
	{
		return XMLDocument.prototype.firstChild.nodeValue;
	});
	
	Node.prototype.__defineGetter__("text", function ()
	{
		var txt='',c;
		if ((this.nodeType==3) || (this.nodeType==2)) return this.nodeValue;
		if (!(c=this.firstChild)) return null;
		while(c) {
			if (c.nodeType==3) txt+=c.nodeValue; //this.nodeType==3 || 
			c=c.nextSibling;
		}
		return txt;
	});
	
	XMLDocument.prototype.__defineSetter__("text", function (stext)
	{
		XMLDocument.prototype.firstChild.nodeValue = stext;
	});
	
	Node.prototype.__defineSetter__("text", function (stext)
	{
		var c,c1;
		if ((this.nodeType==3) || (this.nodeType==2)) 
			this.nodeValue= stext;
		else {
			c=this.firstChild;
			while(c) {
				if (c.nodeType==3) c1=c; else c1=null;
				c=c.nextSibling;
				if (c1) this.removeChild(c1);
			}
			var tmptextnode = this.ownerDocument.createTextNode('');
			tmptextnode.nodeValue = stext;
			this.appendChild(tmptextnode);
		}
	});
	
	XMLDocument.prototype.createNode = function (p_Type, p_name, namespaceURI) {
		var tmpnewel;
		var tmptextnode;
		switch(p_Type) {
			case 1: // ELEMENT_NODE
				if (namespaceURI && namespaceURI!="") 
					tmpnewel = this.createElementNS(namespaceURI,p_name);
				else 
				tmpnewel = this.createElement(p_name);
				tmptextnode = this.createTextNode('');
				tmpnewel.appendChild(tmptextnode);
				return tmpnewel;
			case 2: // ATTRIBUTE_NODE
				return this.createAttribute(p_name);
			case 3:
		}
	}
}

if(!window.XMLSerializer ) {
	XMLSerializer = function(){};
		
	XMLSerializer.prototype.serializeToString = function(oNode) {
		return oNode.xml;
	};		
}
