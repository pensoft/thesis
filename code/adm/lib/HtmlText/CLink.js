CLink.prototype=new CObject;
function CLink(pDoc,pCtrlid){
	this.construct(pDoc);
	this.ctrlid = pCtrlid;
	this.contextMenuSetup();
};

CLink.prototype.contextMenuSetup=function(){
	this.contextMenuOptions = {
		"Cut" : {
			"type" : "link",
			"tooltip" : "Cut",
			"callback" : function(){}
		},
		
		"Copy" : {
			"type" : "link",
			"tooltip" : "Copy",
			"callback" : function(){}
		
		},
		
		"Paste" :{
			"type" : "link",
			"tooltip" : "Paste",
			"callback" :  function(){}
		
		},
		
		"DeleteLink" : {
			"type" : "link",
			"tooltip" : "Delete Link",
			"callback" : function(){this.UnLink();}
		},
		
		"LinkProps" : {
			"type" : "link",
			"tooltip" : "Edit Link",
			"callback" : function(){this.linkProps();}
		}
	};
	this.contextMenuConf = [ "DeleteLink", "LinkProps" ];

}

CLink.prototype.UnLink=function(){
	var el;
	if(!(el=this.findElement("a"))) return;
	this.range.extractContents(el);
}


CLink.prototype.showPropMenu = function(el,tabopts){
	var popup = document.createElement("div");
	popup.style.position = 'absolute';
	popup.style.top = findPosY(el) + 'px';
	popup.style.left = findPosX(el) + 'px';
	popup.style.border = '1px solid #000';
	popup.style.background = '#fff';
	popup.style.width = '400px';
	popup.style.height = '150px';
	
	popup.innerHTML = '' + 
	'<form onsubmit = "return false;" id = "' + this.ctrlid + '_inslink" action = "javascript: void(0);">' +
		'<table width = "100%">' +
		'<tr>' +
			'<td><b>URL: </b></td>' +
			'<td><input type = "text" name = "href"  value = "' + tabopts["href"] + '" /></td>' +
		'</tr>' +
		'<tr>' +
			'<td><b>Target:</b></td>' +
			'<td><select name="target" style="width: 80px;">' +
				'<option value="_self" ' + (tabopts["target"]=="_self"? "selected='1'" : "") + '>_self</option>' + 
				'<option value="_blank" ' + (tabopts["target"]=="_blank"? "selected='1'" : "") + '>_blank</option>' + 
			'</select></td>' +
		'</tr>' +
		'<tr>' +
			'<td colspan = "2" align = "right"><input type="button" id="' + this.ctrlid + '_link_cancel" value="Cancel"/>&nbsp;<input type = "button" id="' + this.ctrlid + '_link_OK" value = "Ok" /></td>' +
		'</tr>' +
		'</table>' +
	'</form>';
	popup=document.body.appendChild(popup);
	var cancel = document.getElementById(this.ctrlid + '_link_cancel');
	cancel.onclick=function(){
		document.body.removeChild(popup);
	}
	return popup;
}

CLink.prototype.insertLink = function(el){
	var   tabopts = {
		'href' : 'http://',
		'target' : '_self'
	};
	
	var popup = this.showPropMenu(el,tabopts);
	
	var frm = document.getElementById(this.ctrlid + '_inslink');
	var OK = document.getElementById(this.ctrlid + '_link_OK');
	
	OK.onmousedown = function() {
		for (var i = 0; i < frm.elements.length; i ++) {
			if (frm.elements[i] && frm.elements[i].name && typeof tabopts[frm.elements[i].name] != 'undefined') {
				tabopts[frm.elements[i].name] = frm.elements[i].value;
			}
			if (frm.elements[i] && frm.elements[i].name && frm.elements[i].name == 'target') {
				if (frm.elements[i].options[frm.elements[i].selectedIndex].value) {
					tabopts['target'] = frm.elements[i].options[frm.elements[i].selectedIndex].value;
				}
			}
		}
		
		var link=tabopts['href'];
		var target=tabopts['target'];
		var r;
		if (!link) return;
		if(this.contentDocument.selection && this.contentDocument.selection.createRange) {
			r = this.contentDocument.selection.createRange();
			var str = link;
			if(r.text!= '') str = r.text;
			this.contentDocument.selection.createRange().pasteHTML('<a href = "' + link + '" target = "' + target +'">' + str + '</a>');
		} else {
			r = this.contentDocument.defaultView.getSelection();
			if(r.rangeCount>1){
				alert("Моля, изберете само една част от текста!");
			}
			var str = link;
			if(r!= '')	str = r;
			this.contentDocument.execCommand("inserthtml",false,'<a href = "' + link + '" target = "_blank">' + str + '</a>');		
		}
		document.body.removeChild(popup);
		
	}.bind(this);
	

}

CLink.prototype.linkProps=function(){
	var el;
	if(!(el=this.findElement("table"))) return false;
	var tabopts={
		"target": (el.getAttribute("target")?el.getAttribute("target"): "_self"),
		"href": (el.getAttribute("href")?el.getAttribute("href"): "http://")
	};
	
	var popup=this.showPropMenu(el,tabopts);
	var frm = document.getElementById(this.ctrlid + '_inslink');
	var OK = document.getElementById(this.ctrlid + "_link_OK");
	
	OK.onmousedown = function() {
		for (var i = 0; i < frm.elements.length; i ++) {
			if (frm.elements[i] && frm.elements[i].name && typeof tabopts[frm.elements[i].name] != 'undefined') {
				tabopts[frm.elements[i].name] = frm.elements[i].value;
			}
			if (frm.elements[i] && frm.elements[i].name && frm.elements[i].name == 'target') {
				if (frm.elements[i].options[frm.elements[i].selectedIndex].value) {
					tabopts['target'] = frm.elements[i].options[frm.elements[i].selectedIndex].value;
				}
			}
		}
		
		for(var i in tabopts){
			el.setAttribute(i,tabopts[i]);
		}
		document.body.removeChild(popup);
	}
}
