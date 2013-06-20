var DblBreaksCommand=function(){
        //create our own command, we dont want to use the FCKDialogCommand because it uses the default fck layout and not our own
};
DblBreaksCommand.prototype.Execute=function(){
       
}
DblBreaksCommand.GetState=function() {
       return FCK_TRISTATE_OFF; //we dont want the button to be toggled
}

DblBreaksCommand.Execute=function() {
	var oInnerDoc = FCK.EditorDocument;
	var text = oInnerDoc.body.innerHTML;
	text = text.replace(/<b>\s*<br\s?\/?>/g, '<br /><b>');
	text = text.replace(/<br\s?\/?>\s*<\/b>/g, '</b><br />');
	text = text.replace(/<br\s?\/?>/g, '<br /><br />');
	oInnerDoc.body.innerHTML = text;	
}

FCKCommands.RegisterCommand('DoubleBreaks', DblBreaksCommand); //otherwise our command will not be found

var oDblBreaks = new FCKToolbarButton('DoubleBreaks', 'Раздалечаване на параграфите');
oDblBreaks.IconPath = FCKConfig.PluginsPath + 'doublebreaks/doublebreaks.gif';

FCKToolbarItems.RegisterItem('DoubleBreaks', oDblBreaks);