var NumChars;
var SelNumChars;

function strip_tags(h) {
	a = h.indexOf("<");
	b = h.indexOf(">");
	len = h.length;
	c = h.substring(0, a);
	if(b == -1)
		b = a;
	d = h.substring((b + 1), len);
	h = c + d;
	tagCheck = h.indexOf("<");
	if(tagCheck != -1)
		h = strip_tags(h);
	return h;
}

var OnKeyDownAlert = function(e) {
	if (!e) var e = FCK.EditorWindow.event;
	var charCode = (e.which) ? e.which : e.keyCode;
	//alert(charCode);
	if (e.altKey && charCode == 77) { // 109
		var msg = 'Маркирани / Общо' + "\n";
		msg += SelNumChars + ' / ' + NumChars;
		alert(msg);
	}
}

var FCKCustomExtriCommand=function(){
        //create our own command, we dont want to use the FCKDialogCommand because it uses the default fck layout and not our own
};
FCKCustomExtriCommand.prototype.Execute=function(){
       
}
FCKCustomExtriCommand.GetState=function() {
       return FCK_TRISTATE_OFF; //we dont want the button to be toggled
}
var RtfCounter = 0;
FCKCustomExtriCommand.Execute=function() {
	var DOMDocument = FCK.EditorDocument;
	
	if ( FCKBrowserInfo.IsIE ) {
		var HTMLText = DOMDocument.body.innerHTML;
	} else {
		var r = DOMDocument.createRange();
		r.selectNodeContents( DOMDocument.body );
		var HTMLText = r.startContainer.innerHTML;
	}
	
	NumChars = 0;
	SelNumChars = 0;
	//~ alert(HTMLText);
	var HTMLTextRpl = HTMLText.replace(/<br\s*[\/]*>\s*$/,'');
	HTMLTextRpl = HTMLTextRpl.replace(/\u00a0+/gi,' '); // nbsp
	HTMLTextRpl = HTMLTextRpl.replace(/&nbsp;+/gi,' '); // nbsp
	HTMLTextRpl = HTMLTextRpl.replace(/<(br|\/p)>/gi,'*^*');
	
	if (document.selection) {
		var stext = FCK.EditorDocument.selection.createRange().text;
	} else if (window.getSelection) {
		var stext = FCK.EditorWindow.getSelection();
	} else if (document.getSelection) {
		var stext = FCK.EditorDocument.getSelection();
	}
	if (stext) {
		stext = stext.toString().replace(/\n/gi, '');
		SelNumChars = stext.length;
	}
	
	//~ if (document.all) { // If Internet Explorer.
		//~ FCK.EditorDocument.attachEvent("onkeydown", OnKeyDownAlert) ;
	//~ } else { // If Gecko.
		//~ FCK.EditorDocument.addEventListener('keydown', OnKeyDownAlert, true);
	//~ }
	
	var lines = HTMLTextRpl.split('*^*');
	for (var i = 0; i < lines.length; i ++) {
		NumChars += strip_tags(lines[i]).length;
	}
	
	// Tuka smenqme statusa na unsaved
	if(window.parent.document.getElementById('rtfchanged')) {
		RtfCounter = RtfCounter + 1;
		var test = window.parent.document.getElementById('rtfchanged').value;
		if (test == 0 && RtfCounter > 4) {
			window.parent.document.getElementById('rtfchanged').value = 1;
		}
	}
}

FCKCommands.RegisterCommand('CustomExtri', FCKCustomExtriCommand ); //otherwise our command will not be found

var FCKRowsCountButton = function(strCaption) {
	this.CommandName	= 'CustomExtri';
	this.Label				= 'Маркирани: 0 / Общо: 0',
	this.Tooltip			= 'Маркирани символи / Общо символи', 
	this.Style				= FCK_TOOLBARITEM_ONLYTEXT;
	this.SourceView			= false;
	this.ContextSensitive	= true;
	this.IconPath			= '';
	this.State				= FCK_UNKNOWN ;
}

FCKRowsCountButton.prototype.Create = function( targetElement )
{
	this._UIButton = new FCKRowsCountButtonUI( this.CommandName, this.Label, this.Tooltip, null, this.Style ) ;
	this._UIButton.OnClick = this.Click ;
	this._UIButton._ToolbarButton = this ;	
	this._UIButton.Create( targetElement ) ;
}

FCKRowsCountButton.prototype.RefreshState = function() {
	// Gets the actual state.
	var eState = FCK.ToolbarSet.CurrentInstance.Commands.GetCommand(this.CommandName).GetState() ;
	FCK.ToolbarSet.CurrentInstance.Commands.GetCommand(this.CommandName).Execute() ;
	this.SetLabel();
	
	// If there are no state changes than do nothing and return.
	if ( eState == this._UIButton.State ) return ;
	
	// Sets the actual state.
	this._UIButton.ChangeState( eState ) ;
}


FCKRowsCountButton.prototype.SetLabel = function() {
	this.SetLabel2(NumChars, SelNumChars);
}

FCKRowsCountButton.prototype.SetLabel2 = function(nchars, nselchars) {
	if (nselchars == undefined || nchars == undefined) return;
	this.Label = (nselchars.length == 0 || nchars.length == 0) ? '&nbsp;' : 'Маркирани: ' + nselchars + ' / Общо: ' + nchars;
	if (this._UIButton._LabelEl) {
		this._UIButton._LabelEl.innerHTML = this.Label;
	}
}

var FCKRowsCountButtonUI = function( name, label, tooltip, iconPathOrStripInfoArray, style, state) {
	this.Name		= name ;
	this.Label		= label || name ;
	this.Tooltip	= tooltip || this.Label ;
	this.Style		= style || FCK_TOOLBARITEM_ONLYICON ;
	this.State		= state || FCK_TRISTATE_OFF ;
	
	this.Icon = new FCKIcon( iconPathOrStripInfoArray ) ;

	if ( FCK.IECleanup )
		FCK.IECleanup.AddItem( this, FCKToolbarButtonUI_Cleanup ) ;
}

FCKRowsCountButtonUI.prototype._CreatePaddingElement = function( document )
{
	var oImg = document.createElement( 'IMG' ) ;
	oImg.className = 'TB_Button_Padding' ;
	oImg.src = FCK_SPACER_PATH ;
	return oImg ;
}

FCKRowsCountButtonUI.prototype.Create = function( parentElement )
{
	var oMainElement = this.MainElement ;
	
	if ( oMainElement )
	{
		FCKToolbarButtonUI_Cleanup.call(this) ;
		
		if ( oMainElement.parentNode )
			oMainElement.parentNode.removeChild( oMainElement ) ;
		oMainElement = this.MainElement = null ;
	}

	var oDoc = parentElement.ownerDocument ;	// This is IE 6+
	
	// Create the Main Element.
	oMainElement = this.MainElement = oDoc.createElement( 'DIV' ) ;
	oMainElement._FCKButton = this ;		// IE Memory Leak (Circular reference).
	oMainElement.title		= this.Tooltip ;

	// The following will prevent the button from catching the focus.
	if ( FCKBrowserInfo.IsGecko )
		 oMainElement.onmousedown	= FCKTools.CancelEvent ;

	this.ChangeState( this.State, true ) ;

	if ( this.Style == FCK_TOOLBARITEM_ONLYICON && !this.ShowArrow )
	{
		oMainElement.appendChild( this.Icon.CreateIconElement( oDoc ) ) ;
	}
	else
	{
		var oTable = oMainElement.appendChild( oDoc.createElement( 'TABLE' ) ) ;
		oTable.cellPadding = 0 ;
		oTable.cellSpacing = 0 ;

		var oRow = oTable.insertRow(-1) ;
		
		// The Image cell (icon or padding).
		var oCell = oRow.insertCell(-1) ;
		
		if ( this.Style == FCK_TOOLBARITEM_ONLYICON || this.Style == FCK_TOOLBARITEM_ICONTEXT )
			oCell.appendChild( this.Icon.CreateIconElement( oDoc ) ) ;
		else
			oCell.appendChild( this._CreatePaddingElement( oDoc ) ) ;
		
		if ( this.Style == FCK_TOOLBARITEM_ONLYTEXT || this.Style == FCK_TOOLBARITEM_ICONTEXT )
		{
			// The Text cell.
			this._LabelEl = oCell = oRow.insertCell(-1) ;
			oCell.className = 'TB_Button_Text' ;
			oCell.noWrap = true ;
			oCell.appendChild(oDoc.createTextNode( this.Label ) ) ;
		}
		
		if ( this.ShowArrow )
		{
			if ( this.Style != FCK_TOOLBARITEM_ONLYICON )
			{	
				// A padding cell.
				oRow.insertCell(-1).appendChild( this._CreatePaddingElement( oDoc ) ) ;
			}
			
			oCell = oRow.insertCell(-1) ;
			var eImg = oCell.appendChild( oDoc.createElement( 'IMG' ) ) ;
			eImg.src	= FCKConfig.SkinPath + 'images/toolbar.buttonarrow.gif' ;
			eImg.width	= 5 ;
			eImg.height	= 3 ;
		}

		// The last padding cell.
		oCell = oRow.insertCell(-1) ;
		oCell.appendChild( this._CreatePaddingElement( oDoc ) ) ;
	}
	parentElement.appendChild( oMainElement ) ;
}

FCKRowsCountButtonUI.prototype.ChangeState = function( newState, force )
{
	if ( !force && this.State == newState )
		return ;

	var e = this.MainElement ;

	switch ( parseInt( newState ) )
	{
		case FCK_TRISTATE_OFF :
			e.className		= 'TB_Button_Off' ;
			//~ e.onmouseover	= FCKRowsCountButton_OnMouseOverOff ;
			//~ e.onmouseout	= FCKRowsCountButton_OnMouseOutOff ;
			//~ e.onclick		= FCKRowsCountButton_OnClick ;
			
			break ;
			
		case FCK_TRISTATE_ON :
			e.className		= 'TB_Button_On' ;
			//~ e.onmouseover	= FCKRowsCountButton_OnMouseOverOn ;
			//~ e.onmouseout	= FCKRowsCountButton_OnMouseOutOn ;
			//~ e.onclick		= FCKRowsCountButton_OnClick ;
			
			break ;

		case FCK_TRISTATE_DISABLED :
			e.className		= 'TB_Button_Disabled' ;
			e.onmouseover	= null ;
			e.onmouseout	= null ;
			e.onclick		= null ;
			bEnableEvents = false ;
			break ;
	}

	this.State = newState ;
}

//~ FCKRowsCountButton_OnMouseOverOn = FCKToolbarButton_OnMouseOverOn;
//~ FCKRowsCountButton_OnMouseOverOff = FCKToolbarButton_OnMouseOverOff;
//~ FCKRowsCountButton_OnMouseOutOn	= FCKToolbarButton_OnMouseOutOn;
//~ FCKRowsCountButton_OnMouseOutOff = FCKToolbarButton_OnMouseOutOff;
//~ FCKRowsCountButton_OnClick = FCKToolbarButton_OnClick;
FCKRowsCountButton.prototype.Click = FCKToolbarButton.prototype.Click;
FCKRowsCountButton.prototype.Enable = FCKToolbarButton.prototype.Enable;
FCKRowsCountButton.prototype.Disable = FCKToolbarButton.prototype.Disable;

var oRF = new FCKRowsCountButton() ;
FCKToolbarItems.RegisterItem('CustomExtri', oRF);