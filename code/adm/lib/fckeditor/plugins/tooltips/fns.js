var oEditor		= window.parent.InnerDialogLoaded() ;
var FCK			= oEditor.FCK ;
var FCKLang		= oEditor.FCKLang ;
var FCKConfig	= oEditor.FCKConfig ; 

// oLink: The actual selected link in the editor.
var oLink = FCK.Selection.MoveToAncestorNode('A');
if (oLink)
	FCK.Selection.SelectNode(oLink) ;


window.onload = function() {
	// Zarejda infoto ako e clicknato na sushtestvuvasht tooltip
	LoadSelection();
	// OK buton
	window.parent.SetOkButton(true) ;
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

function RemoveTooltip() {
	if (!oLink) {
		alert('Не сте избрали подсказка!');
		return;
	}
	
	var oLinktxt = oLink.innerHTML;
	oLink.parentNode.removeChild(oLink);
	InsertText(oLinktxt);
	window.parent.Cancel();
}

function LoadSelection() {
	if (!oLink) {
		if (document.selection) {
			var stext = FCK.EditorDocument.selection.createRange().text;
		} else if (window.getSelection) {
			var stext = FCK.EditorWindow.getSelection();
		} else if (document.getSelection) {
			var stext = FCK.EditorDocument.getSelection();
			
		}
		
		GetE('TTtxt').value = stext;
		return;
	}
	GetE('TTtxt').value = oLink.innerHTML;
	GetE('TTalter').value = oLink.title;
}

//#### The OK button was hit.
function Ok() {
	var sUrl = 'javascript: void();';
	
	if (!GetE('TTtxt').value || !GetE('TTalter').value) {
		alert('Не сте въвели текст!');
		return false;
	}
	
	if (!oLink) {
		// Nqma link => suzdavame
		oLink = oEditor.FCK.CreateLink(sUrl)[0];
	}
	
	oLink.removeAttribute('href');
	
	oLink.innerHTML = GetE('TTtxt').value;
	SetAttribute(oLink, 'title', GetE('TTalter').value);
	
	if (oEditor.FCKBrowserInfo.IsIE) {
		SetAttribute(oLink, 'className', 'ttword') ;
	} else {
		SetAttribute(oLink, 'class', 'ttword');
	}
	
	return true ;
}
