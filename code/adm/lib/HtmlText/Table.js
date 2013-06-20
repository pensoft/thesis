CTable.prototype=new CObject;
function CTable(pDoc,ctrlid){
	this.construct(pDoc);
	
	this.ctrlid=ctrlid;
	
	this.contextMenuSetup();
}



CTable.prototype.contextMenuSetup=function(){
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
		
		"Cell" : {
			"type" : "menu",
			"tooltip" : "Cell",
			"childs" : {
				"InserCellBefore" : {
					"type" : "link",
					"tooltip" : "InserCellBefore",
					"callback" : function(){}
				},
				"InserCellAfter" : {
					"type" : "link",
					"tooltip" : "InserCellAfter",
					"callback" : function(){}
				
				},
				
				"DeleteCell" : {
					"type" : "DeleteCell",
					"tooltip" : "Copy",
					"callback" : function(){}
				
				},
				
				"mergeCells" : {
					"type" : "link",
					"tooltip" : "mergeCells",
					"callback" : function(){}
				
				},
				
				"mergeRight" : {
					"type" : "link",
					"tooltip" : "Merge Right",
					"callback" : function(){return this.mergeRight();}
				
				},
				
				"mergeDown" : {
					"type" : "link",
					"tooltip" : "Merge Down",
					"callback" : function(){return this.mergeDown();}
				
				},
				
				"splitCellHorizontally" : {
					"type" : "link",
					"tooltip" : "Split Cell Horizontally",
					"callback" : function(){return this.splitCellHorizontally();}
				
				},
				"splitCellVertically" : {
					"type" : "link",
					"tooltip" : "Split Cell Vertically",
					"callback" : function(){return this.splitCellVertically();}
				
				},
				"cellProps" : {
					"type" : "link",
					"tooltip" : "Cell Properties",
					"callback" : function(){}
				
				}
			},
			"childConf" : ["mergeRight","mergeDown","splitCellHorizontally","splitCellVertically"]
		},
		
		"Row" : {
			"type" : "menu",
			"tooltip" : "Row",
			"childs" : {
				"insertRowAfter" : {
					"type" : "link",
					"tooltip" : "Insert Row After",
					"callback" : function(){return this.insertTR("tr",1);}
				},
				
				"insertRowBefore" : {
					"type" : "link",
					"tooltip" : "Insert Row Before",
					"callback" : function(){return this.insertTR("tr");}
				},
				
				"deleteRows" : {
					"type" : "link",
					"tooltip" : "Delete Rows",
					"callback" : function(){return this.DeleteRow();}
				}
			
			},
			"childConf" : ["insertRowAfter","insertRowBefore","deleteRows"]
		
		},
		
		"Column" : {
			"type" : "menu",
			"tooltip" : "Column",
			"childs" : {
				"insertColumnAfter" : {
					"type" : "link",
					"tooltip" : "Insert Column After",
					"callback" : function(){return this.collumnOperations(1,true);}
				},
				
				"insertColumnBefore" : {
					"type" : "link",
					"tooltip" : "Insert Column Before",
					"callback" : function(){return this.collumnOperations(1);}
				},
				
				"deleteColumns" : {
					"type" : "link",
					"tooltip" : "Delete Columns",
					"callback" : function(){return this.collumnOperations(3);}
				}
			},
			"childConf" : ["insertColumnAfter","insertColumnBefore","deleteColumns"]
		
		},
		
		"DeleteTable" : {
			"type" : "link",
			"tooltip" : "Delete Table",
			"callback" : function(){this.DeleteTable();}
		},
		
		"TableProps" : {
			"type" : "link",
			"tooltip" : "Table Properties",
			"callback" : function(){this.tableProps();}
		}
	};
	//~ this.contextMenuConf = [  "Cell"];
	this.contextMenuConf = [  "Cell", "|", "Row", "|", "Column", "|", "DeleteTable", "TableProps"];

}



CTable.prototype.DeleteTable=function(){
	return this.Delete("table");
}

CTable.prototype.DeleteRow=function(){
	return this.Delete("tr");
}

CTable.prototype.insertTR=function(pTagName,bAfter){
	var el;
	if(!(el=this.findElement(pTagName))) return false;
	var tr=this.buildTr(el.childNodes);
	if(!bAfter){
		el.parentNode.insertBefore(tr,el);
	}else{
		if(el.nextSibling){
			el.parentNode.insertBefore(tr,el.nextSibling);
		}else{
			el.parentNode.appendChild(tr);
		}
	}
	return true;
}

CTable.prototype.buildTr=function(childs){
	var tr=this.contentDocument.createElement("tr");
	for(var i in childs){
		if(childs[i] && childs[i].nodeType!=1) continue;
		var td=this.contentDocument.createElement("td");
		td.innerHTML="&nbsp;";
		var br=this.contentDocument.createElement("br");
		td.appendChild(br);
		tr.appendChild(td);
		
	}
	return tr;
}

CTable.prototype.collumnOperations=function(op,bAfter){
	/*
		insert - 1
		delete - 3
	*/
	var el;
	if(!(el=this.findElement("td"))) return false;
	var childNum=0;
	var tmpEl=el;
	while(tmpEl=tmpEl.previousSibling){
		childNum++;
	}
	var tr = el.parentNode.parentNode.firstChild;
	
	do{
		if(tr.nodeType!=1) continue;
		if(op==3){	
			tr.removeChild(tr.childNodes[childNum]);
		}else if(op==1){
			var td=this.contentDocument.createElement("td");
			var br=this.contentDocument.createElement("br");
			td.innerHTML="&nbsp;";
			td.appendChild(br);
			if(bAfter){
				if(tr.childNodes[childNum].nextSibling){
					tr.insertBefore(td,tr.childNodes[childNum].nextSibling);
				}else{
					tr.appendChild(td);
				}
			}else{
				tr.insertBefore(td,tr.childNodes[childNum]);
			}
		
		
		}
	}while(tr=tr.nextSibling);
	
}

CTable.prototype.mergeRight=function(){
	var el;
	if(!(el=this.findElement("td"))) return false;
	var tmpEl=el;
	while(tmpEl=tmpEl.nextSibling){
		if(tmpEl.nodeType!=1) continue;
		tmpEl.innerHTML=(el.innerHTML?el.innerHTML:'') + (tmpEl.innerHTML? tmpEl.innerHTML : '');
		tmpEl.colSpan=(tmpEl.colSpan? tmpEl.colSpan : 1) + (el.colSpan? el.colSpan : 1) ;
		el.parentNode.removeChild(el);
		break;
	}
	return true;
}

CTable.prototype.mergeDown=function(){
	var el;
	if(!(el=this.findElement("td"))) return false;
	var childNum=0;
	var tmpEl=el;
	while(tmpEl.previousSibling){
		tmpEl=tmpEl.previousSibling;
		childNum++;
	}
	tmpEl=el.parentNode;
	while(tmpEl=tmpEl.nextSibling){
		if(tmpEl.nodeType!=1) continue;
		if(!tmpEl) return false;
		tmpEl=tmpEl.childNodes[childNum];
		if(!tmpEl) return false;
		el.innerHTML=(el.innerHTML?el.innerHTML:'') + (tmpEl.innerHTML? tmpEl.innerHTML : '');
		el.rowSpan=(tmpEl.rowSpan? tmpEl.rowSpan : 1) + (el.rowSpan? el.rowSpan : 1)
		tmpEl.parentNode.removeChild(tmpEl);
		break;
	}
	return true;
}

CTable.prototype.splitCellHorizontally=function(){
	var el;
	if(!(el=this.findElement("td"))) return false;
	var colspan = el.colSpan;
	if(!colspan || colspan==1) return false;
	if(colspan==2) el.colSpan="";
	else el.colSpan=colspan-1;
	//insert td after
	var td=this.contentDocument.createElement("td");
	var br=this.contentDocument.createElement("br");
	td.innerHTML="&nbsp;";
	td.appendChild(br);
	if(el.nextSibling){
		el.parentNode.insertBefore(td,el.nextSibling);
	}else{
		el.parentNode.appendChild(td);
	}
}

CTable.prototype.splitCellVertically=function(){
	var el;
	if(!(el=this.findElement("td"))) return false;
	var rowspan = el.rowSpan;
	if(!rowspan || rowspan==1) return false;
	if(rowspan==2) el.rowSpan="";
	else el.rowSpan=rowSpan-1;
	//insert td after
	var td=this.contentDocument.createElement("td");
	var br=this.contentDocument.createElement("br");
	td.innerHTML="&nbsp;";
	td.appendChild(br);
	var childNum=0;
	var tmpEl=el;
	while(tmpEl.previousSibling){
		tmpEl=tmpEl.previousSibling;
		childNum++;
	}
	tmpEl=el.parentNode;
	rowspan--;
	while(tmpEl.nextSibling){
		tmpEl=tmpEl.nextSibling;
		if(tmpEl.nodeType!=1) continue;
		rowspan--;
		if(rowspan) continue;
		
		break;
	}
	
	if(!tmpEl.firstChild){
		tmpEl.parentNode.appendChild(td);
		return true;
	}

	tmpEl=tmpEl.firstChild;
	childNum--;
	while(tmpEl.nextSibling){
		tmpEl=tmpEl.nextSibling;
		if(tmpEl.nodeType!=1) continue;
		childNum--;
		if(!childNum)continue;
		break;
	}
	

	tmpEl.parentNode.insertBefore(td,tmpEl);
}

CTable.prototype.tableProps=function(){
	var el;
	if(!(el=this.findElement("table"))) return false;
	var tabopts={
		"height": (el.getAttribute("height")?el.getAttribute("height"): 0),
		"width": (el.width?el.width: 0),
		"cellspacing": (el.cellSpacing?el.cellSpacing : 0),
		"cellpadding": (el.cellPadding?el.cellPadding : 0),
		"border": (el.border?el.border : 0),
		"align": (el.align?el.align : '')
	};
	
	var popup=this.showTablePropMenu(el,tabopts,1);
	var frm = document.getElementById(this.ctrlid + '_instab');
	var OK = document.getElementById(this.ctrlid + '_OK');
	
	OK.onmousedown = function() {
		for (var i = 0; i < frm.elements.length; i ++) {
			if (frm.elements[i] && frm.elements[i].name && typeof tabopts[frm.elements[i].name] != 'undefined') {
				tabopts[frm.elements[i].name] = frm.elements[i].value;
			}
			if (frm.elements[i] && frm.elements[i].name && frm.elements[i].name == 'align') {
				if (frm.elements[i].options[frm.elements[i].selectedIndex].value) {
					tabopts['align'] = frm.elements[i].options[frm.elements[i].selectedIndex].value;
				}
			}
		}
		
		for(var i in tabopts){
			el.setAttribute(i,tabopts[i]);
		}
		document.body.removeChild(popup);
	}
}

CTable.prototype.createTable = function(opts, str) {
	if (typeof opts != 'object') return '';
	if (typeof str == 'undefined') str = '';
	var alignment = (opts['align'] ? ' align="' + opts['align'] + '"' : '');
	var rethtml = '<table border = "' + opts['border'] + '" cellpadding = "' + opts['cellpadding'] + '" cellspacing = "' + opts['cellspacing'] + '" width = "' + opts['width'] + '" height = "' + opts['height'] + '"' + alignment +'>' + "\n";
	rethtml += '<tbody>' + "\n";
	for (var i = 1; i <= opts['rows']; i ++) {
		rethtml += '<tr>';
		for (var j = 1; j <= opts['cols']; j ++) {
			if (str && i == 1 && j == 1) {
				rethtml += '<td>' + str + '</td>' + "\n";
			} else {
				rethtml += '<td>&nbsp;</td>' + "\n";
			}
		}
		rethtml += '</tr>' + "\n";
	}
	rethtml += '</tbody>' + "\n" + '</table><br/>' + "\n";
	return rethtml;
}


CTable.prototype.showTablePropMenu = function(el,tabopts,dontShowCols){
	var popup = document.createElement("div");
	popup.style.position = 'absolute';
	popup.style.top = findPosY(el) + 'px';
	popup.style.left = findPosX(el) + 'px';
	popup.style.border = '1px solid #000';
	popup.style.background = '#fff';
	popup.style.width = '400px';
	popup.style.height = '150px';
	
	popup.innerHTML = '' + 
	'<form onsubmit = "return false;" id = "' + this.ctrlid + '_instab" action = "javascript: void(0);">' +
		'<table width = "100%">' +
		'<tr>' +
			(dontShowCols? '' : '<td><b>Cols:</b></td>' +
			'<td><input type = "text" name = "cols" style = "width: 50px;" value = "' + tabopts["cols"] + '" /></td>') +
			'<td><b>Width:</b></td>' +
			'<td><input type = "text" name = "width" style = "width: 50px;" value = "' + tabopts["width"] + '" /></td>' +
		(dontShowCols? '' : '</tr>' +
		'<tr>' +
			'<td><b>Rows:</b></td>' +
			'<td><input type = "text" name = "rows" style = "width: 50px;" value = "' + tabopts["rows"] + '" /></td>') +
			'<td><b>Height:</b></td>' +
			'<td><input type = "text" name = "height" style = "width: 50px;" value = "' + tabopts["height"] + '" /></td>' +
		'</tr>' +
		'<tr>' +
			'<td><b>Cell Padding:</b></td>' +
			'<td><input type = "text" name = "cellpadding" style = "width: 50px;" value = "' + tabopts["cellpadding"] + '" /></td>' +
			'<td><b>Cell Spacing:</b></td>' +
			'<td><input type = "text" name = "cellspacing" style = "width: 50px;" value = "' + tabopts["cellspacing"] + '" /></td>' +
		'</tr>' +
		'<tr>' +
			'<td><b>Border:</b></td>' +
			'<td><input type = "text" name = "border" style = "width: 50px;" value = "' + tabopts["border"] + '" /></td>' +
			'<td><b>Alignment:</b></td>' +
			'<td><select name="align" style="width: 80px;">' +
				'<option value="" ' + (tabopts["align"]==""? "selected='1'" : "") + '>none</option>' + 
				'<option value="center" ' + (tabopts["align"]=="center"? "selected='1'" : "") + '>center</option>' + 
				'<option value="left" ' + (tabopts["align"]=="left"? "selected='1'" : "") + '>left</option>' + 
				'<option value="right" ' + (tabopts["align"]=="right"? "selected='1'" : "") + '>right</option>' + 
			'</select></td>' +
		'</tr>' +
		'<tr>' +
			'<td colspan = "4" align = "right"><input type="button" id="' + this.ctrlid + '_cancel" value="Cancel"/>&nbsp;<input type = "button" id="' + this.ctrlid + '_OK" value = "Ok" /></td>' +
		'</tr>' +
		'</table>' +
	'</form>';
	popup=document.body.appendChild(popup);
	var cancel = document.getElementById(this.ctrlid + '_cancel');
	cancel.onclick=function(){
		document.body.removeChild(popup);
	}
	return popup;
}

CTable.prototype.insertTable = function(el) {
	var   tabopts = {
			'cols' : '3',
			'rows' : '2',
			'border' : '1',
			'cellpadding' : '0',
			'cellspacing' : '0',
			'width' : '100px',
			'height' : '100px',
			'align' : ''
		};
	
	var popup = this.showTablePropMenu(el,tabopts);
	var htmltext = this;
	var frm = document.getElementById(this.ctrlid + '_instab');
	var OK = document.getElementById(this.ctrlid + '_OK');
	
	OK.onmousedown = function() {
		for (var i = 0; i < frm.elements.length; i ++) {
			if (frm.elements[i] && frm.elements[i].name && typeof tabopts[frm.elements[i].name] != 'undefined') {
				tabopts[frm.elements[i].name] = frm.elements[i].value;
			}
			if (frm.elements[i] && frm.elements[i].name && frm.elements[i].name == 'align') {
				if (frm.elements[i].options[frm.elements[i].selectedIndex].value) {
					tabopts['align'] = frm.elements[i].options[frm.elements[i].selectedIndex].value;
				}
			}
		}
		
		var r;
		var str = '';
		if(_HTMLTEXT_IE) {
			r = htmltext.contentDocument.selection.createRange();
			if (r.text != '') str = r.text;
			r.pasteHTML(htmltext.createTable(tabopts, str));
		} else {
			r = htmltext.contentDocument.defaultView.getSelection();
			if (r.rangeCount > 1) {
				alert("Моля, изберете само една част от текста!");
			}
			if (r != '') str = r;
			htmltext.contentDocument.execCommand("inserthtml", false, htmltext.createTable(tabopts, str));		
		}
		document.body.removeChild(popup);
	};
	
}





