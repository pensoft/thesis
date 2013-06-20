var AltColorCommand=function(){
        //create our own command, we dont want to use the FCKDialogCommand because it uses the default fck layout and not our own
};
AltColorCommand.prototype.Execute=function(){
       
}
AltColorCommand.GetState=function() {
       return FCK_TRISTATE_OFF; //we dont want the button to be toggled
}

AltColorCommand.Execute=function() {
	var oTab = FCK.Selection.MoveToAncestorNode('TABLE');
	
	if (!oTab) {
		alert('Не е избрана таблица!');
		return;
	}
	
	var pTRcolection = oTab.getElementsByTagName('TR');
	var lTest = false;
	
	for (i = 0; i < pTRcolection.length; i ++) {
		if (lTest) continue;
		if (sSuffix == 'ie') {
			lTest = (pTRcolection[i].getAttribute('className') == 'darkrow');
		} else {
			lTest = (pTRcolection[i].getAttribute('class') == 'darkrow');
		}
	}
	
	for (i = 0; i < pTRcolection.length; i ++) {
		if (lTest) {
			if (sSuffix == 'ie') {
				pTRcolection[i].removeAttribute('className') ;
			} else {
				pTRcolection[i].removeAttribute('class');
			}
		} else {
			if (i % 2 == 0) {
				if (sSuffix == 'ie') {
					pTRcolection[i].setAttribute('className', 'darkrow') ;
				} else {
					pTRcolection[i].setAttribute('class', 'darkrow');
				}
			} else {
				if (sSuffix == 'ie') {
					pTRcolection[i].removeAttribute('className') ;
				} else {
					pTRcolection[i].removeAttribute('class');
				}
			}
		}
	}
}

FCKCommands.RegisterCommand('AltColor', AltColorCommand ); //otherwise our command will not be found

var oAltColor = new FCKToolbarButton('AltColor', 'Редуване на цветове на таблица');
oAltColor.IconPath = FCKConfig.PluginsPath + 'altertablecolor/altercolor.gif' ;

FCKToolbarItems.RegisterItem( 'AltColor', oAltColor );