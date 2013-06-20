var RNG;
var MyRFCommand=function(){
        //create our own command, we dont want to use the FCKDialogCommand because it uses the default fck layout and not our own
};
MyRFCommand.prototype.Execute=function(){
       
}
MyRFCommand.GetState=function() {
       return FCK_TRISTATE_OFF; //we dont want the button to be toggled
}

MyRFCommand.Execute=function() {
	RNG = CRng();
	
	if (sSuffix == 'ie') {
		var b = RNG.htmlText;
	} else {
		var div = FCK.EditorDocument.createElement("div");
		div.appendChild(RNG.extractContents());
		var b = div.innerHTML;
	}
	
	if (!b) {
		FCK.EditorDocument.body.innerHTML = CleanWord(FCK.EditorDocument.body.innerHTML);
	} else {	
		b = CleanWord(b);
		
		if (sSuffix == 'ie') {
			RNG.pasteHTML(b);
			RNG.select();		
		} else {
			// construct a new document fragment with the given HTML
			var fragment = FCK.EditorDocument.createDocumentFragment();
			var div = FCK.EditorDocument.createElement("div");
			div.innerHTML = b;
			while (div.firstChild) {
				// the following call also removes the node from div
				fragment.appendChild(div.firstChild);
			}
			var node = RNG.insertNode(fragment);
		}	
	}
	FCK.ExecuteNamedCommand( 'RemoveFormat');
}

FCKCommands.RegisterCommand('MyRemoveFormat', MyRFCommand ); //otherwise our command will not be found

var oRF = new FCKToolbarButton('MyRemoveFormat', FCKLang.RemoveFormat);
oRF.IconPath = 19;

FCKToolbarItems.RegisterItem('MyRemoveFormat', oRF);