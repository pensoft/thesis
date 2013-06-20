

HtmlText = function(div,pName){
	this.div = document.getElementById(div);
	if(!this.div){
		alert("No div!");
	}
	this.mode = 0;
	this.vsparent = null;
	this.vsbrother = null;
	this.ctrlName = pName;
	this.RegisteredCommands = {
		'Bold' : {
			'type' : 'button',
			'icon' : 'img/bold.png',
			'width' : 16,
			'tooltip' : 'Bold',
			'callback' : function(){this.setFormat("bold", 'b'); this.refreshValue(); this.onSelectionChange();},
			'bit' : '1',
			'mode' : ["view"]
		},
		'Italic' : {
			'type' : 'button',
			'icon' : 'img/italic.png',
			'width' : 11,
			'tooltip' : 'Italic',
			'callback' : function(){this.setFormat("italic", 'i'); this.refreshValue(); this.onSelectionChange();},
			'bit' : '2',
			'mode' : ["view"]
		},
		'Underline' : {
			'type' : 'button',
			'icon' : 'img/underline.png',
			'width' : 16,
			'tooltip' : 'Underline',
			'callback' : function(){this.setFormat("underline", 'u'); this.refreshValue(); this.onSelectionChange();},
			'bit' : '3',
			'mode' : ["view"]
		},
		'BulletedList' : {
			'type' : 'button',
			'icon' : 'img/unorderedlist.png',
			'width' : 19,
			'tooltip' : 'Bulleted list',
			'callback' : function(){this.contentDocument.execCommand("insertunorderedlist", false, ''); this.refreshValue();},
			'bit' : '4',
			'mode' : ["view"]
		},
		'OrderedList' : {
			'type' : 'button',
			'icon' : 'img/orderedlist.png',
			'width' : 19,
			'tooltip' : 'Ordered list',
			'callback' : function(){this.contentDocument.execCommand("insertorderedlist", false, ''); this.refreshValue();},
			'bit' : '5',
			'mode' : ["view"]
		},
		'FontColor' : {
			'type' : 'button',
			'icon' : 'img/forecolor.png',
			'width' : 25,
			'tooltip' : 'Font color',
			'callback' : function(){this.createPalette('forecolor', 'FontColor'); this.refreshValue();},
			'bit' : '6',
			'mode' : ["view"]
		},
		'BackColor' : {
			'type' : 'button',
			'icon' : 'img/backcolor.png',
			'width' : 19,
			'tooltip' : 'Background color',
			'callback' : function(){this.createPalette('hilitecolor', 'BackColor'); this.refreshValue();},
			'bit' : '7',
			'mode' : ["view"]
		},
		'CreateLink' : {
			'type' : 'button',
			'icon' : 'img/link.png',
			'width' : 22,
			'tooltip' : 'Create Link',
			'callback' : function(){this.createLink(); this.refreshValue();},
			'bit' : '8',
			'mode' : ["view"]
		},
		'InsertTable' : {
			'type' : 'button',
			'icon' : 'img/table.png',
			'width' : 33,
			'tooltip' : 'Insert Table',
			'callback' : function(){this.insertTable(); this.refreshValue();},
			'bit' : '9',
			'mode' : ["view"]
		},
		'InsertImage' : {
			'type' : 'button',
			'icon' : 'img/img.png',
			'width' : 24,
			'tooltip' : 'Insert Image',
			'callback' : function(){this.openImageBrowse(); this.refreshValue();},
			'bit' : '10',
			'mode' : ["view"]
		},
		'ViewSource' : {
			'type' : 'button',
			//'icon' : 'img/html.gif',
			'tooltip' : 'Код',
			'callback' : function(){ this.refreshValue();this.Source();},
			'bit' : '11',
			'mode' : ["view","source"]
		},
		'Fullscreen' : {
			'type' : 'button',
			'icon' : 'img/window_fullscreen.gif',
			'tooltip' : 'Fullscreen',
			'callback' : function(){this.toggleFullScreen(); this.refreshValue();},
			'bit' : '12',
			'mode' : ["view"]
		},
		'FontFace' : {
			'type' : 'combo',
			'tooltip' : 'Font',
			'options' : {'0' : ' - Font - ', 'cursive': 'Cursive', 'fantasy': 'Fantasy', 'sans-serif': 'Sans Serif', 'serif': 'Serif', 'monospace': 'Typewriter'},
			'callback' : function(fontfamily){this.setFontFamily(fontfamily);this.refreshValue();},
			'bit' : '13',
			'mode' : ["view"]
		},
		'FontSize' : {
			'type' : 'combo',
			'tooltip' : 'Size',
			'options' : {'0' : ' - Size - ', '-2': 'X Small', '-1': 'Small', '+0': 'Medium', '+1': 'Large', '+2': 'X Large'},
			'callback' : function(fontsize){this.setFontSize(fontsize);this.refreshValue()},
			'bit' : '14',
			'mode' : ["view"]
		},
		'TemplateStyles' : {
			'type' : 'tplstyles',
			'tooltip' : 'Style',
			'callback' : function(stylename){this.addSuroundTags('span', [{"name": "tplstyles","val": "1"}, {"name": "class","val": stylename}, {"name": "etaligent","val": "1"}], 'tplstyles');this.toolbarButtons['TemplateStyles'].selectedIndex = 0;this.refreshValue()},
			'bit' : '15',
			'mode' : ["view"]
		},
		'CleanFormat' : {
			'type' : 'button',
			'icon' : 'img/clean.png',
			'width' : 21,
			'tooltip' : 'Clean Format',
			'callback' : function(){this.CleanFormat(); this.refreshValue();},
			'bit' : '16',
			'mode' : ["view"]
		},
		'JustifyLeft' : {
			'type' : 'button',
			'icon' : '',
			'tooltip' : 'Left Justify',
			'callback' : function(){this.contentDocument.execCommand("justifyLeft",false,null);},
			'bit' : '17',
			'mode' : ["view"]
		},
		'JustifyCenter' : {
			'type' : 'button',
			'icon' : '',
			'tooltip' : 'Center Justify',
			'callback' : function(){this.contentDocument.execCommand("justifyCenter",false,null);},
			'bit' : '18',
			'mode' : ["view"]
		},
		'JustifyRight' : {
			'type' : 'button',
			'icon' : '',
			'tooltip' : 'Right Justify',
			'callback' : function(){this.contentDocument.execCommand("justifyRight",false,null);},
			'bit' : '19',
			'mode' : ["view"]
		}
	};
	
	this.toolbarButtons = {};
	this.toolbarConfig;
	this.toolbarConfig = [
		/*['FontFace', 'FontSize', '|', 'Bold', 'Italic', 'Underline', '|', 'BulletedList', 'OrderedList', '|', 'FontColor', 'BackColor','|', 'CreateLink', 'InsertTable', 'InsertImage', '|', 'ViewSource', 'TemplateStyles', '|', 'CleanFormat']*/
	];
	
	
	//da se vidi kak 6te se predavat dannite
	/*
	
		s textarea za view source se pra6tat
	*/
	this.ctrlid = div;
	this.iframe = null;
	
	/*	za tova trqbva da e druga shema
		this.forbiddenButtons=pg.m_hashObjects[this.objname].m_xmlAll.selectSingleNode("/root/vis//htmltext[@ref = '" + this.nodename + "']").getAttribute("buttons");
	*/
	this.fullscreen = 0;
	this.m_html = null;
}


HtmlText.prototype.RegisterCommand = function(name, def) {
	this.RegisteredCommands[name] = def; 
}

HtmlText.prototype.Display = function(html){
	//~ this.createToolbar();
	this.createEditbox(html);
	//~ this.setupEvents();
}

HtmlText.prototype.createToolbar = function(){
	this.toolbar = document.createElement("div");
	this.toolbar.className = "htmltext-toolbar";
	if(this.div.firstChild){
		this.div.insertBefore(this.toolbar,this.div.firstChild);
	}else{
		this.div.appendChild(this.toolbar);
	}
	
	if (typeof this.toolbarConfig != 'undefined' && this.toolbarConfig instanceof Array && this.toolbarConfig.length) {
		this.toolbar.style.height=(41*this.toolbarConfig.length) + "px";
		for (var rownum = 0; rownum < this.toolbarConfig.length; rownum ++) {
			var tbdiv = document.createElement("div");
			tbdiv.className = 'htmltext-toolbar-row';
			this.toolbar.appendChild(tbdiv);
			
			var tbrow = this.toolbarConfig[rownum];
			if (typeof tbrow != 'undefined' && tbrow instanceof Array && tbrow.length) {
				for (var b = 0; b < tbrow.length; b ++) {
					if (tbrow[b] == '|') {
						var sepel = tbdiv.appendChild(document.createElement("span"))
						sepel.className = 'htmltext-toolbar-sep';
						sepel.innerHTML='&nbsp;';
						//imgel.src = 'img/separator.gif';
					} else if (typeof this.RegisteredCommands[tbrow[b]] != 'undefined') {
						var bit = this.RegisteredCommands[tbrow[b]]['bit'];
						if(!bit) bit = 1;
						if(this.forbiddenButtons && !(this.forbiddenButtons&Math.pow(2,bit))) continue;
						if (this.RegisteredCommands[tbrow[b]]['type'] == 'button') {
								this.toolbarButtons[tbrow[b]] = this.createToolbarButton(tbdiv, this.RegisteredCommands[tbrow[b]]['icon'], this.RegisteredCommands[tbrow[b]]['tooltip'], this.RegisteredCommands[tbrow[b]]['callback'].bind(this),this.RegisteredCommands[tbrow[b]]['mode']);
						} else if (this.RegisteredCommands[tbrow[b]]['type'] == 'combo') {
							this.toolbarButtons[tbrow[b]] = this.createToolbarCombo(tbdiv, this.RegisteredCommands[tbrow[b]]['tooltip'], this.RegisteredCommands[tbrow[b]]['options'], this.RegisteredCommands[tbrow[b]]['callback']);
						}
					}
				}
			}
		}
	}
	
	if (this.toolbarButtons['ViewSource']) {
		this.vsparent = this.toolbarButtons['ViewSource'].parentNode;
		if (this.toolbarButtons['ViewSource'].nextSibling) {
			this.vsbrother = this.toolbarButtons['ViewSource'].nextSibling;
		}
	}
	
	
}

HtmlText.prototype.createToolbarButton = function(container, img, name, callback,mode){
	if (typeof container == 'undefined') var container = this.toolbar;
	var btn = document.createElement("div");
	btn.className = ((!this.mode || in_array("source",mode))? "toolbar-button" : "toolbar-button-disabled");
	
	if(img){
		var imgel = btn.appendChild(document.createElement("img"));
		imgel.src = img;
	}else{
		btn.innerHTML="<span class='textButton'>" + name + "</span>";	
	}
	
	if(!this.mode || in_array("source",mode)){
		btn.title = name;
		btn.onclick =callback;
		
		
		btn.onmouseover = function(){
			this.className = this.className.replace(" toolbar-button-mouseover", "")+" toolbar-button-mouseover";
		}
		btn.onmouseout = function(){
			this.className = this.className.replace(" toolbar-button-mouseover", "").replace(" toolbar-button-mousedown","");		
		}

		btn.onmousedown = function(){
			this.className = this.className.replace(" toolbar-button-mousedown","")+" toolbar-button-mousedown";return false;
		}
		btn.onmouseup = function(){
			this.className = this.className.replace(" toolbar-button-mousedown","");
		}
	}
	//~ btn.style.verticalAlign = "middle";
	try {
		//~ container.style.verticalAlign = "middle";
		btn=container.appendChild(btn);
		
	} catch (err) {
		return false;
	}
	return btn;
}



HtmlText.prototype.rebuildComboOptions = function(btn, options){
	btn.options.length = 0;
	
	for(var key in options){
		var o = document.createElement('option');
		o.value = key;
		o.text = options[key];
		btn.options[btn.options.length] = o;
	}
}

HtmlText.prototype.createToolbarCombo = function(container, name, options, callback){
	if (typeof container == 'undefined') var container = this.toolbar;
	var btn = document.createElement("select");
	btn.title = name;
	
	var div = document.createElement("div");
	div.className = "toolbar-select";
	div.innerHTML = "&nbsp;"
	div.appendChild(btn);
	
	this.rebuildComboOptions(btn, options);
	if(!this.mode) {
		var htmltext = this;
		btn.onchange = function(){callback.apply(htmltext,[btn.value]); htmltext.iframe.focus()};
	}else{
		btn.disabled=true;
	}
	
	try {
		container.appendChild(div);
		return btn;
	} catch (err) {
		return false;
	}
}

HtmlText.prototype.createEditbox = function(html){
	this.iframe = document.createElement("iframe");
	this.iframe.style.visibility = "hidden";
	
	this.div.appendChild(this.iframe);
	this.iframe.id = this.ctrlid + "_iframe";
	this.iframe.style.clear = "both";
	this.iframe.style.width = "100%";
	this.iframe.style.height =this.fullscreen? "100%" : "350px";
	this.iframe.style.border = "1px solid #1D283F";
	this.iframe.frameBorder = 0;
	this.iframe.style.background = "white";
	
	this.contentDocument = this.iframe.contentWindow || this.iframe.contentDocument;
	if(this.contentDocument && this.contentDocument.document) 
		this.contentDocument = this.contentDocument.document;
	
	this.contentDocument.open();
	this.contentDocument.write("<html><head><style type=\"text/css\" id=\"template_styles_tag\">" + (this.tstyles ? this.tstyles : '') + "</style><link href='iframe.css' rel='stylesheet' type='text/css' /></head><body>");
	this.contentDocument.write(html);
	
	this.contentDocument.write("</body></html>");
	this.contentDocument.close();
	
	
	this.contentDocument.designMode = "on";
	this.contentDocument.contentEditable = true;
	
	
	this.iframe.style.display = this.mode? "none" : "";
	this.iframe.style.visibility = "";
	
	this.textarea = document.createElement("textarea");
	this.textarea.name = this.ctrlName;
	this.textarea.style.display = this.mode? "" : "none";
	this.div.appendChild(this.textarea);
	this.textarea.style.clear = "both";
	this.textarea.style.width = "100%";
	this.textarea.style.height = this.fullscreen? "100%" : "350px";
	this.textarea.style.border = "0px none";
	this.textarea.value = html;
	
	
	/*this.table=new CTable(this.contentDocument,this.ctrlid);
	this.link=new CLink(this.contentDocument,this.ctrlid);
	this.img=new CImage(this.contentDocument,this.ctrlid);
	*/
	this.contextmenu={};
}

HtmlText.prototype.addEvent = function(event, callback, object){
	if(typeof object == "undefined")
		object = this.contentDocument;
		
	if(object.addEventListener)
		object.addEventListener(event, callback, false);
	else if(object.attachEvent)
		object.attachEvent("on"+event, callback);
	else
		return false;
	
	return true;
}

HtmlText.prototype.onSelectionChange = function(){
	this.toolbarButtons['Bold'].className = this.toolbarButtons['Bold'].className.replace(" toolbar-button-enabled","")+(this.contentDocument.queryCommandState("bold")?" toolbar-button-enabled":"");
	this.toolbarButtons['Italic'].className = this.toolbarButtons['Italic'].className.replace(" toolbar-button-enabled","")+(this.contentDocument.queryCommandState("italic")?" toolbar-button-enabled":"");
	this.toolbarButtons['Underline'].className = this.toolbarButtons['Underline'].className.replace(" toolbar-button-enabled","")+(this.contentDocument.queryCommandState("underline")?" toolbar-button-enabled":"");
}

HtmlText.prototype.setupEvents = function(){
	var htmltext = this;
	
	
	this.addEvent("blur",function(){htmltext.refreshValue()},this.iframe);
	this.addEvent("blur",function(){htmltext.refreshValue()},this.textarea);
	this.addEvent("blur",function(){htmltext.refreshValue()},this.contentDocument);
	
	this.addEvent("mouseup",function(){htmltext.onSelectionChange()},this.contentDocument);
	
	if(_HTMLTEXT_CH || _HTMLTEXT_SF){
		this.contentDocument.body.oncontextmenu=function(event){return htmltext.onContextMenu(event);}
	}else{
		this.addEvent("contextmenu",function(event){return htmltext.onContextMenu(event);},this.contentDocument);
	}
	this.addEvent("mousedown",function(){return htmltext.hideContextMenu()},this.contentDocument);
	//~ this.addEvent("mouseup",function(){return htmltext.hideContextMenu()},this.contentDocument);
	this.addEvent("keyup",function(){htmltext.onSelectionChange()},this.contentDocument);
	
}

HtmlText.prototype.onContextMenu=function(event){
	//~ alert('a');
	var stopEvent=0;
	var objName,objInst;
	if ( ! event ) var event = window.event ;
	var r=new Range(this.contentDocument);
	if(r.isCollapsed()){
		var target=event.target || event.srcElement;
		if(this.searchParentsByTagName(target,"table")){
			//show ower contextmenu
			objInst=this.table;
			objName="table";
		}else if(this.searchParentsByTagName(target,"img")){
			objInst=this.img;
			objName="img";
		}else if(this.searchParentsByTagName(target,"a")){
			objInst=this.link;
			objName="link";		
		}
		
		if(objInst) objInst.target=target;
	}else{
		if(this.queryCommandState("table")){
			//show ower contextmenu
			objInst=this.table;
			objName="table";
			
		}else if(this.queryCommandState("a")){
			objInst=this.link;
			objName="link";
			
		}else if(this.queryCommandState("img")){
			objInst=this.img;
			objName="img";
		}
		
		if(objInst) {
			objInst.refreshSelection();
			objInst.target=objInst.range.getAnchorNode();
		}
	}
	if(objName){
		stopEvent++;
		if(!this.contextmenu[objName]){
			this.contextmenu[objName]=new CContextMenu(objInst, this.ctrlid,objName);
		}
	}
	if(stopEvent){
		this.contextmenu[objName].showHideMenu(null,event);
		event.cancelBubble = true;
		if (event.stopPropagation) event.stopPropagation();
		if (event.preventDefault) event.preventDefault();
		
		return false;
	}
	return true;
}

HtmlText.prototype.searchParentsByTagName=function(el,tagName){
	while(el && el!=this.contentDocument.body){
		if(el && el.nodeType==1 && el.nodeName.toLowerCase()==tagName.toLowerCase()){
			return true;
		}
		el=el.parentNode;
	}
	if(!el || el.nodeName.toLowerCase()!=tagName.toLowerCase() ) return false;
	return true;

}

HtmlText.prototype.hideContextMenu=function(event){
	for(var i in this.contextmenu)
		if(this.contextmenu[i].getVisibleMenusLength && this.contextmenu[i].getVisibleMenusLength()){
			this.contextmenu[i].execObj.target=null;
			this.contextmenu[i].hideMenus();
		}
	return true;
}


HtmlText.prototype.stopEventPropagandation=function(event){
	event.cancelBubble = true;
	if (event.stopPropagation) event.stopPropagation();
}

HtmlText.trim = function(str) {
	var	str = str.replace(/^[\s\xA0][\s\xA0]*/, ''),
		ws = /[\s\xA0]/,
		i = str.length;
	while (ws.test(str.charAt(--i)));
	return str.slice(0, i + 1);
}

HtmlText.prototype.toggleFullScreen = function() {
	this.fullscreen = !this.fullscreen;
	
	this.toolbarButtons['Fullscreen'].className = this.toolbarButtons['Fullscreen'].className.replace(" toolbar-button-enabled","")+(this.fullscreen?" toolbar-button-enabled":"");
	this.toolbarButtons['Fullscreen'].className = this.toolbarButtons['Fullscreen'].className.replace(" toolbar-button-mouseover", "");
	
	if(this.fullscreen) {
		this.savewidth = this.div.style.width;
		this.div.style.width = "";
		this.bookmark = document.createElement('div');
		this.div.parentNode.insertBefore(this.bookmark, this.div);
		
		var html = this.m_html? this.m_html : (this.mode? this.textarea.value : this.contentDocument.body.innerHTML);
		window.scrollTo(0,0);
		document.body.style.overflow = "hidden";
		if (this.iframe) {
			this.div.removeChild(this.iframe);
			this.div.removeChild(this.textarea);
		}
		document.body.appendChild(this.div);
		this.createEditbox(html);
		this.setupEvents();	
		
	} else {
		var html = this.mode? this.textarea.value : this.contentDocument.body.innerHTML;
		this.div.removeChild(this.iframe);
		this.div.removeChild(this.textarea);
		this.bookmark.parentNode.insertBefore(this.div, this.bookmark);
		this.createEditbox(html);
		this.setupEvents();
		
		this.bookmark.parentNode.removeChild(this.bookmark);
	
		this.div.style.width = this.savewidth;
		document.body.style.overflow = "";
	}
	this.div.className = "htmltextcontainer"+(this.fullscreen?" htmltextcontainer-fullscreen":"");
	
	return true;
}

HtmlText.prototype.createLink = function( ) {
	/*var link = prompt("Въведете адрес:","http://");
	var r;
	if (!link) return;
	if(this.contentDocument.selection && this.contentDocument.selection.createRange) {
		r = this.contentDocument.selection.createRange();
		var str = link;
		if(r.text!= '') str = r.text;
		this.contentDocument.selection.createRange().pasteHTML('<a href = "' + link + '" target = "_blank">' + str + '</a>');
	} else {
		r = this.contentDocument.defaultView.getSelection();
		if(r.rangeCount>1){
			alert("Моля, изберете само една част от текста!");
		}
		var str = link;
		if(r!= '')	str = r;
		this.contentDocument.execCommand("inserthtml",false,'<a href = "' + link + '" target = "_blank">' + str + '</a>');		
	}*/
	this.link.insertLink(this.toolbarButtons['CreateLink']);
}

HtmlText.prototype.setToolbarMode = function() {
	if (!(this.vsparent && this.toolbarButtons['ViewSource'])) return false;
	
	//mahame toolbara
	if(this.toolbar) this.toolbar.parentNode.removeChild(this.toolbar);
	this.RegisteredCommands['ViewSource']['tooltip']=(!this.mode? 'Код' : 'HTML');
	this.createToolbar();
	this.toolbarButtons['ViewSource'].title = (!this.mode? "View Source" : "View Html");
	this.toolbarButtons['ViewSource'].className = (!this.mode? "toolbar-button" :  "toolbar-button toolbar-button-enabled");
}
	
HtmlText.prototype.Source = function() {
	if(!this.mode){
		this.iframe.style.display = "none";
		this.textarea.style.display = "";
		this.mode = 1;
	}else{
		this.iframe.style.display = "";
		this.textarea.style.display = "none";
		this.contentDocument.body.innerHTML = this.textarea.value;
		this.mode = 0;
	}
	this.setToolbarMode();
}

HtmlText.prototype.refreshValue = function(){
	if(!this.mode){
		var html = this.contentDocument.body.innerHTML;
		this.textarea.value = html;
	}
}

HtmlText.prototype.CleanFormat = function(){
	this.contentDocument.body.innerHTML = this.CleanWord(this.contentDocument.body.innerHTML);
}

HtmlText.prototype.CleanWord = function(html) {
	var cleantags=[ "script","style", "meta", "title" , "o:p","link"];
	var tagsendwithbr=[  ];
	//~ var tagstokeep=[ "br","b","u","i","span", "ul","ol", "li" ,"p","div","table","tbody","tr","td","a","img","font"];
	var tagstokeep=[ "bold", "italic", "underline"];
	/* !!! Mislim za <pre>
	html = html.replace(/<PRE[^>]*>/gi, '');
	html = html.replace(/<\/PRE>/gi, '');
	*/
	
	html = html.replace(/<\s*([^>\s]+)\s[^>]*?(\/*)>/gm, "<$1$2>");
	html = html.replace(/\t/gm, "");
	html = html.replace(/\n/gm, "");
	html = html.replace(/<\s*b\s*\/*>/gmi, "<bold>");
	html = html.replace(/<\/\s*b\s*>/gm, "</bold>");
	html = html.replace(/<\s*i\s*\/*>/gm, "<italic>");
	html = html.replace(/<\/\s*i\s*>/gm, "</italic>");
	html = html.replace(/<\s*u\s*\/*>/gm, "<underline>");
	html = html.replace(/<\/\s*u\s*>/gm, "</underline>");
	html = html.replace(/<\s*br\s*\/*>/gmi, "\n");
	


	html = html.replace(/<!--[\s\S]*?-->/g, "");
	html = html.replace(/#!#-/gi, '#!!#-');
	html = html.replace(/#-#!/gi, '#--#!');
	html = html.replace(/\&nbsp;/gi, ' ');
	var i;
	for(i=0;i<cleantags.length; i++) {
		var txt='<'+cleantags[i]+'\\/>';
		html=html.replace(new RegExp(txt, "gmi"), "");
		var txt='<'+cleantags[i]+'>[\\s\\S]*?<\\/\\s*'+cleantags[i]+'>';
		html=html.replace(new RegExp(txt, "gmi"), "");
	}	
	for(i=0;i<tagsendwithbr.length; i++) {
		var txt='<'+tagsendwithbr[i]+'>';
		html=html.replace(new RegExp(txt, "gi"), "");
		txt='<'+tagsendwithbr[i]+'\\/>';
		html=html.replace(new RegExp(txt, "gi"), "<br/>");
		txt='<\\/\\s*'+tagsendwithbr[i]+'>';
		html=html.replace(new RegExp(txt, "gi"), "<br/>");
	}	
	html = html.replace(/<\/\s*p>/gi,  "<br/>");
	for(i=0;i<tagstokeep.length; i++) {
		var txt='<(\\/*)'+tagstokeep[i]+'([^>]*)>';
		html=html.replace(new RegExp(txt, "gi"), "#!#-$1"+tagstokeep[i]+"$2#-#!");
	}
	html = html.replace(/<[^>]*>/gi, '');
	html = html.replace(/#!#-/gi, '<');
	html = html.replace(/#-#!/gi, '>');

	return html;
}

HtmlText.prototype.cleanOurTags = function(node, searchAtt) {
	var r=new Range(this.contentDocument);
	if (node.childNodes) {
		for (var i = 0; i < node.childNodes.length; i ++) {
			var tmp = node.childNodes[i];
			if (tmp.nodeType != "1") continue;
			if (tmp.childNodes) this.cleanOurTags(tmp, searchAtt);
			if (tmp.getAttribute(searchAtt)) {
				r.extractContents(tmp);
			}
		}
	}
}

HtmlText.prototype.addSuroundTags = function(nodeName, atribs, cleanwithattr) {
	if (typeof nodeName == 'undefined') var nodeName = 'span';
	if (typeof atribs == 'undefined') var atribs = [{"name": "etaligent","val": "1"}];
	
	var r = new Range(this.contentDocument);
	var curel=r.surroundRangeWithTags(nodeName,atribs);
	
	if (cleanwithattr) {
		
		this.cleanOurTags(curel, cleanwithattr);
	}
}

HtmlText.prototype.queryCommandState=function(tagName){
	var rng = new Range(this.contentDocument);
	return rng.seachSelectionParents(tagName);
}


HtmlText.prototype.getStyleEl=function(el,tag){
	var lel=el;
	while(lel && lel!=this.contentDocument.body){
		if(lel && lel.nodeType==1 && lel.tagName.toLowerCase()==tag.toLowerCase()) return lel;
		lel=lel.parentNode;
		
		
	}
	return null;
}

HtmlText.prototype.cleanSelectionFormat=function(format,tagName){
	this.addSuroundTags("a",[{"name": "name", "val": "dirty"}]);
	var r=new Range(this.contentDocument);
	var elements = this.contentDocument.getElementsByName("dirty");
	for(var i=0;i<elements.length;i++){
		var el=elements[i];
		if(!el || (el && el.nodeType!=1)) continue;
		var stNode ;
		while(stNode = this.getStyleEl(el,tagName)){
			var tmpRange=r.createRange();
			r.setStartBefore(tmpRange,stNode);
			r.setEndBefore(tmpRange,el,false);
			if(r.toString(tmpRange)){
				var node=r.surroundRangeWithTags(tagName, [{"name": "our" + format,"val": "1"}],tmpRange);
				this.cleanOurTags(node, "our" + format);
			}
			tmpRange=r.createRange();
			r.setEndAfter(tmpRange,stNode);
			r.setStartAfter(tmpRange,el,false);
			if(r.toString(tmpRange)){
				var node=r.surroundRangeWithTags(tagName, [{"name": "our" + format,"val": "1"}],tmpRange);
				this.cleanOurTags(node, "our" + format);
			}
			r.extractContents(stNode);
		}
		this.cleanOurTags(el, "our" + format);
		//r.extractContents(el);
	}
	elements = this.contentDocument.getElementsByName("dirty");
	for(var i=0;i<elements.length;i++){
		var el=elements[i];
		r.extractContents(el);
	}

}

HtmlText.prototype.setFormat=function(format,tag){
	if(!this.queryCommandState(tag)){
		this.addSuroundTags(tag, [{"name": "our" + format, "val": "1"}], 'our' + format);
	} else {
		this.cleanSelectionFormat(format,tag);
	}
	this.iframe.focus();
	(this.contentDocument.defaultView || this.contentDocument).focus();
}



HtmlText.prototype.insertTable = function() {
	return this.table.insertTable(this.toolbarButtons['InsertTable']);
}

HtmlText.prototype.setFontFamily = function(fontfamily) {
	if (typeof fontfamily == 'undefined' || fontfamily == ' - Font - ' || fontfamily == '0') return;
	//~ this.contentDocument.execCommand("fontname", false, fontfamily);
	this.addSuroundTags("span", [{"name": "ourfontfamily", "val": "1"},{"name": "style", "val": "font-family: " + fontfamily}], 'ourfontfamily');
	
	this.toolbarButtons['FontFace'].selectedIndex = 0;
}

HtmlText.prototype.setFontSize = function(fontsize) {
	if (typeof fontsize == 'undefined' || fontsize == ' - Size - ' || fontsize == '0') return;
	//~ this.contentDocument.execCommand("fontSize", false, fontsize);
	this.addSuroundTags("font", [{"name": "ourfontsize", "val": "1"},{"name": "size", "val": fontsize}], 'ourfontsize');
	this.toolbarButtons['FontSize'].selectedIndex = 0;
}


HtmlText.prototype.insertImage = function(url){
	var imghtml = '<img src="' + url + '" alt="" />';
	
	if (this.contentDocument.selection && this.contentDocument.selection.createRange) {
		var r = this.contentDocument.selection.createRange();
		r.pasteHTML(imghtml);
	} else {
		this.contentDocument.execCommand("inserthtml", false, imghtml);		
	}
}

HtmlText.prototype.setColor = function(color, command) {
	//~ if (_HTMLTEXT_IE && command == 'hilitecolor') command = 'backcolor';
	//this.setC("color","span",[{"name": "ourcolor", "val": "1"},{"name": "style", "val": ((command=="hilitecolor"? "background-color: " : "color: ") + color)}]);
	
	
	this.addSuroundTags("span", [{"name": "our" + (command=="hilitecolor"? "bgcolor" : "color"), "val": "1"},{"name": "style", "val": ((command=="hilitecolor"? "background-color: " : "color: ") + color)}], 'our' + (command=="hilitecolor"? "bgcolor" : "color"));
	this.iframe.focus();
	(this.contentDocument.defaultView || this.contentDocument).focus();
	/*
	this.contentDocument.execCommand(command, false, color);
	
	// Tova prekusva cveta i ne znaem zashto (slaga izlishno <br>)
	if (this.isie()) {
		var rng = this.contentDocument.selection.createRange();
		rng.collapse(false);
	} else {
		var sel = this.contentDocument.defaultView.getSelection();
		sel.collapseToEnd();
	}
	*/
	this.contentDocument.defaultView.focus();
}

HtmlText.prototype.createPalette = function(command, butname) {
	var popup = document.createElement("div");
	popup.style.position = 'absolute';
	popup.style.top = findPosY(this.toolbarButtons[butname]) + 'px';
	popup.style.left = findPosX(this.toolbarButtons[butname]) + 'px';
	popup.style.border = '1px solid #000';
	popup.style.background = '#fff';
	popup.style.width = '292px';
	popup.innerHTML = '<table border = "0" cellpadding = "0" cellspacing = "2" id = "' + this.ctrlid + '_colorpick" style="width:100%;">' + 
 ' <tr>' + 
 ' <td bgcolor = "#FFFFFF" width = "15" height = "15">&nbsp;</td>' + 
 ' <td bgcolor = "#FFCCCC" width = "15" height = "15">&nbsp;</td>' + 
 ' <td bgcolor = "#FFCC99" width = "15" height = "15">&nbsp;</td>' + 
 ' <td bgcolor = "#FFFF99" width = "15" height = "15">&nbsp;</td>' + 
 ' <td bgcolor = "#FFFFCC" width = "15" height = "15">&nbsp;</td>' + 
 ' <td bgcolor = "#99FF99" width = "15" height = "15">&nbsp;</td>' + 
 ' <td bgcolor = "#99FFFF" width = "15" height = "15">&nbsp;</td>' + 
 ' <td bgcolor = "#CCFFFF" width = "15" height = "15">&nbsp;</td>' + 
 ' <td bgcolor = "#CCCCFF" width = "15" height = "15">&nbsp;</td>' + 
 ' <td bgcolor = "#FFCCFF" width = "15" height = "15">&nbsp;</td>' + 
 ' </tr>' + 
 ' <tr>' + 
 ' <td bgcolor = "#CCCCCC" width = "15" height = "15">&nbsp;</td>' + 
 ' <td bgcolor = "#FF6666" width = "15" height = "15">&nbsp;</td>' + 
 ' <td bgcolor = "#FF9966" width = "15" height = "15">&nbsp;</td>' + 
 ' <td bgcolor = "#FFFF66" width = "15" height = "15">&nbsp;</td>' + 
 ' <td bgcolor = "#FFFF33" width = "15" height = "15">&nbsp;</td>' + 
 ' <td bgcolor = "#66FF99" width = "15" height = "15">&nbsp;</td>' + 
 ' <td bgcolor = "#33FFFF" width = "15" height = "15">&nbsp;</td>' + 
 ' <td bgcolor = "#66FFFF" width = "15" height = "15">&nbsp;</td>' + 
 ' <td bgcolor = "#9999FF" width = "15" height = "15">&nbsp;</td>' + 
 ' <td bgcolor = "#FF99FF" width = "15" height = "15">&nbsp;</td>' + 
 ' </tr>' + 
 ' <tr>' + 
 ' <td bgcolor = "#C0C0C0" width = "15" height = "15">&nbsp;</td>' + 
 ' <td bgcolor = "#FF0000" width = "15" height = "15">&nbsp;</td>' + 
 ' <td bgcolor = "#FF9900" width = "15" height = "15">&nbsp;</td>' + 
 ' <td bgcolor = "#FFCC66" width = "15" height = "15">&nbsp;</td>' + 
 ' <td bgcolor = "#FFFF00" width = "15" height = "15">&nbsp;</td>' + 
 ' <td bgcolor = "#33FF33" width = "15" height = "15">&nbsp;</td>' + 
 ' <td bgcolor = "#66CCCC" width = "15" height = "15">&nbsp;</td>' + 
 ' <td bgcolor = "#33CCFF" width = "15" height = "15">&nbsp;</td>' + 
 ' <td bgcolor = "#6666CC" width = "15" height = "15">&nbsp;</td>' + 
 ' <td bgcolor = "#CC66CC" width = "15" height = "15">&nbsp;</td>' + 
 ' </tr>' + 
 ' <tr>' + 
 ' <td bgcolor = "#999999" width = "15" height = "15">&nbsp;</td>' + 
 ' <td bgcolor = "#CC0000" width = "15" height = "15">&nbsp;</td>' + 
 ' <td bgcolor = "#FF6600" width = "15" height = "15">&nbsp;</td>' + 
 ' <td bgcolor = "#FFCC33" width = "15" height = "15">&nbsp;</td>' + 
 ' <td bgcolor = "#FFCC00" width = "15" height = "15">&nbsp;</td>' + 
 ' <td bgcolor = "#33CC00" width = "15" height = "15">&nbsp;</td>' + 
 ' <td bgcolor = "#00CCCC" width = "15" height = "15">&nbsp;</td>' + 
 ' <td bgcolor = "#3366FF" width = "15" height = "15">&nbsp;</td>' + 
 ' <td bgcolor = "#6633FF" width = "15" height = "15">&nbsp;</td>' + 
 ' <td bgcolor = "#CC33CC" width = "15" height = "15">&nbsp;</td>' + 
 ' </tr>' + 
 ' <tr>' + 
 ' <td bgcolor = "#666666" width = "15" height = "15">&nbsp;</td>' + 
 ' <td bgcolor = "#990000" width = "15" height = "15">&nbsp;</td>' + 
 ' <td bgcolor = "#CC6600" width = "15" height = "15">&nbsp;</td>' + 
 ' <td bgcolor = "#CC9933" width = "15" height = "15">&nbsp;</td>' + 
 ' <td bgcolor = "#999900" width = "15" height = "15">&nbsp;</td>' + 
 ' <td bgcolor = "#009900" width = "15" height = "15">&nbsp;</td>' + 
 ' <td bgcolor = "#339999" width = "15" height = "15">&nbsp;</td>' + 
 ' <td bgcolor = "#3333FF" width = "15" height = "15">&nbsp;</td>' + 
 ' <td bgcolor = "#6600CC" width = "15" height = "15">&nbsp;</td>' + 
 ' <td bgcolor = "#993399" width = "15" height = "15">&nbsp;</td>' + 
 ' </tr>' + 
 ' <tr>' + 
 ' <td bgcolor = "#333333" width = "15" height = "15">&nbsp;</td>' + 
 ' <td bgcolor = "#660000" width = "15" height = "15">&nbsp;</td>' + 
 ' <td bgcolor = "#993300" width = "15" height = "15">&nbsp;</td>' + 
 ' <td bgcolor = "#996633" width = "15" height = "15">&nbsp;</td>' + 
 ' <td bgcolor = "#666600" width = "15" height = "15">&nbsp;</td>' + 
 ' <td bgcolor = "#006600" width = "15" height = "15">&nbsp;</td>' + 
 ' <td bgcolor = "#336666" width = "15" height = "15">&nbsp;</td>' + 
 ' <td bgcolor = "#000099" width = "15" height = "15">&nbsp;</td>' + 
 ' <td bgcolor = "#333399" width = "15" height = "15">&nbsp;</td>' + 
 ' <td bgcolor = "#663366" width = "15" height = "15">&nbsp;</td>' + 
 ' </tr>' + 
 ' <tr>' + 
 ' <td bgcolor = "#000000" width = "15" height = "15">&nbsp;</td>' + 
 ' <td bgcolor = "#330000" width = "15" height = "15">&nbsp;</td>' + 
 ' <td bgcolor = "#663300" width = "15" height = "15">&nbsp;</td>' + 
 ' <td bgcolor = "#663333" width = "15" height = "15">&nbsp;</td>' + 
 ' <td bgcolor = "#333300" width = "15" height = "15">&nbsp;</td>' + 
 ' <td bgcolor = "#003300" width = "15" height = "15">&nbsp;</td>' + 
 ' <td bgcolor = "#003333" width = "15" height = "15">&nbsp;</td>' + 
 ' <td bgcolor = "#000066" width = "15" height = "15">&nbsp;</td>' + 
 ' <td bgcolor = "#330099" width = "15" height = "15">&nbsp;</td>' + 
 ' <td bgcolor = "#330033" width = "15" height = "15">&nbsp;</td>' + 
 ' </tr>' + 
 ' </table>';
	
	this.div.appendChild(popup);
	var htmltext = this;
	var tab = document.getElementById(this.ctrlid + '_colorpick');
	var tds = tab.getElementsByTagName('td');
	
	for (var i = 0; i < tds.length; i ++) {
		tds[i].onclick = function(ev) {
			if (!ev) var ev = window.event;
			var target = ev.target || ev.srcElement;
			var color = target.getAttribute('bgcolor');
			if (typeof color != 'undefined') {
				htmltext.setColor(color, command);
			}
			htmltext.div.removeChild(popup);
		}
	}
}

HtmlText.prototype.destruct = function(){
	
	this.textarea.parentNode.removeChild(this.textarea);
	this.iframe.parentNode.removeChild(this.iframe);

}