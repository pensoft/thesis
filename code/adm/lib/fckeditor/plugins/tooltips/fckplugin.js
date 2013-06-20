FCKCommands.RegisterCommand('ToolTip', new FCKDialogCommand(
        'tooltipwin',
        'Подсказка',
        FCKConfig.PluginsPath + 'tooltips/tooltips.html', 340, 220)
	); //otherwise our command will not be found

var oG2Image = new FCKToolbarButton('ToolTip', 'Подсказка');
oG2Image.IconPath = FCKConfig.PluginsPath + 'tooltips/tooltips.gif' ;

FCKToolbarItems.RegisterItem( 'ToolTip', oG2Image ); 