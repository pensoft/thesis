var oEditor		= window.parent.InnerDialogLoaded() ;
var FCK			= oEditor.FCK ;
var FCKLang		= oEditor.FCKLang ;
var FCKConfig	= oEditor.FCKConfig ; 

var oTab = FCK.Selection.MoveToAncestorNode('TABLE');
if (oTab)
	FCK.Selection.SelectNode(oTab);

window.onload = function() {
	// Zarejda infoto ako e clicknato na sushtestvuvasht tooltip
	LoadSelection();
	// OK buton
	window.parent.SetOkButton(true) ;
}

function LoadSelection() {
	if (!oTab) return;
	
	if ( oEditor.FCKBrowserInfo.IsIE ) {
		var cls = oTab.getAttribute('className');
	} else {
		var cls = oTab.getAttribute('class');
	}		
	
	var classBox = document.getElementsByTagName('option');
	for (i = 0; i < classBox.length; i++) {
		if (classBox[i].value == cls) {
			classBox[i].selected = 'selected';
		}
	}
}

function MakeKare() {
	table = oEditor.FCK.EditorDocument.createElement("TABLE");
	oRow = table.insertRow(-1);
	oCell = oRow.insertCell(-1);
	
	SetKareClass(table);
	
	var seltxt = GetSelectedHtml();
	if (!seltxt) seltxt = '';
	oCell.innerHTML = seltxt;
	
	oEditor.FCK.InsertElement(table);
	if ( oEditor.FCKBrowserInfo.IsGecko )
		FCK.Selection.SelectNode(table);
}

function RemoveKare() {
	if (!oTab) {
		alert('Това не е каре!');
		return;
	}
	
	var oTD = oTab.getElementsByTagName('TD');
	var oTDtxt = oTD[0].innerHTML;
	oTab.parentNode.removeChild(oTab);
	InsertText(oTDtxt);
	window.parent.Cancel();
}

function GetSelectedText() {
	if (document.selection) {
		var stext = FCK.EditorDocument.selection.createRange().text;
	} else if (window.getSelection) {
		var stext = FCK.EditorWindow.getSelection();
	} else if (document.getSelection) {
		var stext = FCK.EditorDocument.getSelection();
	}
		
	if (!stext) return false;
	return stext;
}

function GetSelectedHtml() {
	if (document.selection) {
		var stext = FCK.EditorDocument.selection.createRange().htmlText;
	} else if (window.getSelection) {
		var stext = FCK.EditorWindow.getSelection();
	} else if (document.getSelection) {
		var stext = FCK.EditorDocument.getSelection();
	}
		
	if (!stext) return false;
	if ( oEditor.FCKBrowserInfo.IsGecko ) {
		var range = stext.getRangeAt(0);
		var div = FCK.EditorDocument.createElement("div");
		div.appendChild(range.extractContents());
		stext = div.innerHTML;
	}
	
	return stext;
}

function SetKareClass(table) {
	var classBox = document.getElementsByTagName('select');
	var cls = classBox[0].options[classBox[0].selectedIndex].value;
	
	if (cls == 'news_accent') {
		var eThead = table.getElementsByTagName('thead')[0];
		if (!eThead) {
			var eTxt = document.createTextNode('Акцент');
			
			var eTh = document.createElement('th');
			eTh.appendChild(eTxt);
			
			var eRow = document.createElement('tr');
			eRow.appendChild(eTh);
			
			var eThead = document.createElement('thead');
			eThead.appendChild(eRow);
			
			table.insertBefore(eThead, table.getElementsByTagName('tbody')[0]);
		}
	}
	
	if (cls == -1) return;
	
	if (oEditor.FCKBrowserInfo.IsIE) {
		SetAttribute(table, 'className', cls) ;
	} else {
		SetAttribute(table, 'class', cls);
	}	
}

function InsertText(txt) {
	if (window.getSelection) {
		var sel = FCK.EditorWindow.getSelection();
	} else if (document.getSelection) {
		var sel = FCK.EditorDocument.getSelection();
	}
	
	if (oEditor.FCKBrowserInfo.IsIE) {
		var range = FCK.EditorDocument.selection.createRange();
		range.pasteHTML(txt);
		range.select();		
	} else {
		var range = sel.getRangeAt(0);
		// construct a new document fragment with the given HTML
		var fragment = FCK.EditorDocument.createDocumentFragment();
		var div = FCK.EditorDocument.createElement("div");
		div.innerHTML = txt;
		while (div.firstChild) {
			// the following call also removes the node from div
			fragment.appendChild(div.firstChild);
		}
		var node = range.insertNode(fragment);
	}
};


//#### The OK button was hit.
function Ok() {
	if (!oTab) {
		MakeKare();
	} else {
		SetKareClass(oTab);
	}
	
	return true ;
}