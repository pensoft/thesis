CImage.prototype=new CObject;
function CImage(pDoc,pCtrlid){
	this.construct(pDoc);
	this.ctrlid = pCtrlid;
	this.contextMenuSetup();
};

CImage.prototype.contextMenuSetup=function(){
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
		
		"imageProps" : {
			"type" : "link",
			"tooltip" : "Image Prperties",
			"callback" : function(){this.imageProps();}
		},
		
		"LinkProps" : {
			"type" : "link",
			"tooltip" : "Edit Link",
			"callback" : function(){this.linkProps();}
		}
	};
	this.contextMenuConf = [ "imageProps"];

}

CImage.prototype.showPropMenu = function(el,tabopts){
	var popup = document.createElement("div");
	popup.style.position = 'absolute';
	popup.style.top = findPosY(el) + 100;
	popup.style.left = findPosX(el)+50;
	popup.style.border = '1px solid #000';
	popup.style.background = '#fff';
	popup.style.width = '600px';
	popup.style.height = '250px';
	
	popup.innerHTML = '' + 
	'<form onsubmit = "return false;" id = "' + this.ctrlid + '_insimg" action = "javascript: void(0);">' +
		'<table width = "100%">' +
		'<tr>' +
			'<td><b>Image URL: </b></td>' +
			'<td colspan="3"><input type = "text" style="width:100%;" name = "src"  value = "' + tabopts["src"] + '" /></td>' +
		'</tr>' +
		'<tr>' +
			'<td><b>Alternative Text: </b></td>' +
			'<td colspan="3"><input type = "text" style="width:100%;" name = "alt"  value = "' + tabopts["alt"] + '" /></td>' +
		'</tr>' +
		'<tr>' +
			'<td><b>Width: </b></td>' +
			'<td><input type = "text" name = "width" style = "width: 50px;" value = "' + tabopts["width"] + '" /></td>' +
			'<td><b>Height: </b></td>' +
			'<td><input type = "text" name = "height" style = "width: 50px;" value = "' + tabopts["height"] + '" /></td>' +
		'</tr>' +
		'<tr>' +
			'<td><b>HSpace: </b></td>' +
			'<td><input type = "text" name = "hspace" style = "width: 50px;" value = "' + tabopts["hspace"] + '" /></td>' +
			'<td><b>VSpace: </b></td>' +
			'<td><input type = "text" name = "vspace" style = "width: 50px;" value = "' + tabopts["vspace"] + '" /></td>' +
		'</tr>' +
		'<tr>' +
			'<td><b>Border:</b></td>' +
			'<td><input type = "text" name = "border" style = "width: 50px;" value = "' + tabopts["border"] + '" /></td>' +
			'<td><b>Alignment:</b></td>' +
			'<td><select name="align" style="width: 80px;">' +
				'<option value="" ' + (tabopts["align"]==""? "selected='1'" : "") + '>none</option>' + 
				'<option value="top" ' + (tabopts["align"]=="top"? "selected='1'" : "") + '>top</option>' + 
				'<option value="middle" ' + (tabopts["align"]=="middle"? "selected='1'" : "") + '>middle</option>' + 
				'<option value="bottom" ' + (tabopts["align"]=="bottom"? "selected='1'" : "") + '>bottom</option>' + 
				'<option value="left" ' + (tabopts["align"]=="left"? "selected='1'" : "") + '>left</option>' + 
				'<option value="right" ' + (tabopts["align"]=="right"? "selected='1'" : "") + '>right</option>' + 
			'</select></td>' +
		'</tr>' +
		'<tr>' +
			'<td><b>Link URL: </b></td>' +
			'<td colspan="3"><input type = "text" style="width:100%;" name = "href"  value = "' + tabopts["href"] + '" /></td>' +
		'</tr>' +
		'<tr>' +
			'<td><b>Link Target:</b></td>' +
			'<td colspan="3"><select name="target" style="width: 80px;">' +
				'<option value="_self" ' + (tabopts["target"]=="_self"? "selected='1'" : "") + '>_self</option>' + 
				'<option value="_blank" ' + (tabopts["target"]=="_blank"? "selected='1'" : "") + '>_blank</option>' + 
			'</select></td>' +
		'</tr>' +
		'<tr>' +
			'<td colspan = "4" align = "right"><input type="button" id="' + this.ctrlid + '_img_cancel" value="Cancel"/>&nbsp;<input type = "button" id="' + this.ctrlid + '_img_OK" value = "Ok" /></td>' +
		'</tr>' +
		'</table>' +
	'</form>';
	popup=document.body.appendChild(popup);
	var cancel = document.getElementById(this.ctrlid + '_img_cancel');
	cancel.onclick=function(){
		document.body.removeChild(popup);
	}
	return popup;
}

CImage.prototype.imageProps=function(){
	var el;
	if(!(el=this.findElement("img"))) return;
	var parNode = el.parentNode;
	var tabopts = {
		"src" : (el.getAttribute("src")? el.getAttribute("src") : ''),
		"alt" : (el.getAttribute("alt")? el.getAttribute("alt") : ''),
		"width" :  (el.getAttribute("width")? el.getAttribute("width") : ''),
		"height" :  (el.getAttribute("height")? el.getAttribute("height") : ''),
		"border" :  (el.getAttribute("border")? el.getAttribute("border") : 0),
		"align" :  el.getAttribute("align"),
		"vspace" : (el.getAttribute("vspace")? el.getAttribute("vspace") : ''),
		"hspace" : (el.getAttribute("hspace")? el.getAttribute("hspace") : ''),
		"href" :  (parNode.nodeName.toLowerCase()=="a"? parNode.getAttribute("href") : ''),
		"target" :  (parNode.nodeName.toLowerCase()=="a"? parNode.getAttribute("target") : '_self')
	
	}
	
	var popup=this.showPropMenu(el,tabopts);
	
	var frm = document.getElementById(this.ctrlid + '_insimg');
	var OK = document.getElementById(this.ctrlid + '_img_OK');
	
	OK.onmousedown = function() {
		for (var i = 0; i < frm.elements.length; i ++) {
			if (frm.elements[i] && frm.elements[i].name && typeof tabopts[frm.elements[i].name] != 'undefined') {
				tabopts[frm.elements[i].name] = frm.elements[i].value;
			}
			if (frm.elements[i] && frm.elements[i].name && (frm.elements[i].name == 'align' || frm.elements[i].name == 'target')) {
				if (frm.elements[i].options[frm.elements[i].selectedIndex].value) {
					tabopts[frm.elements[i].name] = frm.elements[i].options[frm.elements[i].selectedIndex].value;
				}
			}
		}
		
		for(var i in tabopts){
			if(i=="href" || i=="target") continue;
			if(typeof(tabopts[i])!="undefined" && tabopts[i]!=null && tabopts[i]!=''){
				el.setAttribute(i,tabopts[i]);
			}else{
				el.removeAttribute(i);
			}
			
		}
		if(tabopts["href"]){
			if(parNode.nodeName.toLowerCase()!="a"){
				var r = this.range.createRange();
				this.range.setStartBefore(r,el,0);
				this.range.setEndAfter(r,el,0);
				this.range.surroundRangeWithTags("a",[{name:"href",val:tabopts["href"]},{name:"target",val:tabopts["target"]}],r);
			}else{
				parNode.setAttribute("href",tabopts["href"]);
				parNode.setAttribute("target",tabopts["target"]);			
			}
		}else if(!tabopts["href"] && parNode.nodeName.toLowerCase()=="a"){
			this.range.extractContents(parNode);		
		}
		document.body.removeChild(popup);
	}.bind(this);
}