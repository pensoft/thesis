FCKCommands.RegisterCommand('Kareta', new FCKDialogCommand(
        'karetawin',
        'Каре',
        FCKConfig.PluginsPath + 'kareta/kareta.html', 340, 170)
	); //otherwise our command will not be found

var oKareta = new FCKToolbarButton('Kareta', 'Каре');
oKareta.IconPath = FCKConfig.PluginsPath + 'kareta/kareta.gif' ;

FCKToolbarItems.RegisterItem('Kareta', oKareta); 