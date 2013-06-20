function loadDocument(pDocumentId){
	var lTextHolder = new TextHolder(
		'textHolder', 'textTableHolder',
		'xmlHolder', 'xmlTableHolder', 
		'menuHolder', 'formattingMenuHolder', 
		'rightClickMenu', 'rightClickMenuHolder', 
		'hiddenPopup', 'hiddenPopupHolder', 
		'layerbg', 
		'sourceRightClickMenu', 'sourceRightClickMenuHolder', 
		'tagHolder', 'tagNameHolder',  
		pDocumentId, 
		'autotagRightClickMenu', 'autotagRightClickMenuHolder',
		'rightClickSubMenu', 'rightClickSubMenuHolder',
		'textModeTools', 'sourceModeTools',
		'sourceTextAreaIframeHolder',
		'sourceModeRightClickMenu', 'sourceModeRightClickMenuHolder'
	);
	lTextHolder.Display();
}

function disableSelection(target){     
    if (typeof target.onselectstart!="undefined") //IE route 
        target.onselectstart=function(){return false} 
    else if (typeof target.style.MozUserSelect!="undefined") //Firefox route 
        target.style.MozUserSelect="none" 
    else //All other route (ie: Opera) 
        target.onmousedown=function(){return false} 
    target.style.cursor = "default" 
} 

