function CContextMenu(pObj,pCtrlid, pPref, pContextMenuOptions, pContextMenuConf){
	if(!pObj) return;
	if(!pContextMenuConf || !pContextMenuOptions){
		pContextMenuConf=pObj.contextMenuConf;
		pContextMenuOptions=pObj.contextMenuOptions;
	
	}
	this.execObj=pObj;
	this.contextMenuOptions = pContextMenuOptions;
	this.contextMenuConf = pContextMenuConf;
	this.ctrlid=pCtrlid;
	this.pref=pPref;
	this.visibleMenus={};
	this.visibleMenusNum=0;
}

CContextMenu.prototype.buildContextMenu=function(menuKey){
	
	// ******************  Default Parametrers ********************* //
	var menuOptions,menuConf;
	if(!menuKey){
		menuOptions=this.contextMenuOptions;
		menuConf=this.contextMenuConf;
	}else{
		menuOptions=this.contextMenuOptions[menuKey]["childs"];
		menuConf=this.contextMenuOptions[menuKey]["childConf"];
	}
	
	// ******************  Begin ********************* //
	
	var contextDiv = document.createElement("div");
	contextDiv = document.body.appendChild(contextDiv);
	contextDiv.id = this.ctrlid + '_' + this.pref + "_context_menu" + (menuKey? "_" + menuKey : "");
	contextDiv.style.display="none";
	contextDiv.style.position="absolute";
	contextDiv.style.border="1px solid gray";
	contextDiv.style.backgroundColor="#CECECE";
	//~ contextDiv.style.paddingLeft="25px";
	if(_HTMLTEXT_IE)contextDiv.style.width="150px";
	for(var i in menuConf){
		var hashKey = menuConf[i];
		var hashEntry = menuOptions[hashKey];
		this.buildSingleOption(hashKey,hashEntry,contextDiv);
	}
	// ******************  End ********************* //
	return contextDiv;
}

CContextMenu.prototype.buildSingleOption=function(hashKey,hashEntry,contextDiv){

	var entryHolder = document.createElement("div");
	entryHolder = contextDiv.appendChild(entryHolder);
	entryHolder.id=this.ctrlid + "_" + this.pref + "_context_menu_entry_" + hashKey;
	if(hashKey=="|"){
		// ************** Separator Case ************** //
		entryHolder.className = "separator";
		//~ entryHolder.innerHTML = "<br/>";
	}else if(hashEntry.type=="link"){
		// *************** Link Case ****************** //
		entryHolder.className = "entryHolder";
		entryHolder.innerHTML = "<div class='textHolder'>" + hashEntry.tooltip + "</div>";
		entryHolder.onmousedown = function(event){this.hideMenus();return (hashEntry["callback"].bind(this.execObj))();}.bind(this);
		entryHolder.onmouseover = function(){this.className="entryHolder_hover";this.firstChild.className="textHolder_hover";};
		entryHolder.onmouseout = function(){this.className="entryHolder";this.firstChild.className="textHolder";};
	}else if(hashEntry.type=="menu"){
		// *************** SubMenu Case ************* //
		entryHolder.className = "entryHolder";
		entryHolder.innerHTML = "<div class='textHolder'><div><div style='float:left;'>" + hashEntry.tooltip + "</div><div style='float:right;padding-top:2px;'><img src='img/rarrow.png'/></div><div style='float:none;clear:both;'/></div></div>";
		entryHolder.onmousedown = function(event){this.hideMenus({"main":1});return this.showHideMenu(hashKey,event);}.bind(this);
		entryHolder.onmouseover = function(){this.className="entryHolder_hover";this.firstChild.className="textHolder_hover";};
		entryHolder.onmouseout = function(){this.className="entryHolder";this.firstChild.className="textHolder";};
	}
	//return result;
}


CContextMenu.prototype.showHideMenu=function(id,event){
	var lid;
	if(!id){
		lid=this.ctrlid + "_" + this.pref + "_context_menu";
	}else{
		lid=this.ctrlid + "_" + this.pref + "_context_menu_"+id;
	}
	
	var contextDiv = document.getElementById(lid);
	if(!contextDiv){
	// ******************** Build Menu ******************** //
		contextDiv = this.buildContextMenu(id);
	}
	
	if(contextDiv.style.display=="none"){
	//******************** Show Menu ******************** //
		this.visibleMenus[(id? id : "main")]=lid ;
		this.visibleMenusNum++;
		contextDiv.style.display="block";
		var top=0,left=0;
		var mPos = getMousePosition(event);
		var width = (contextDiv.offsetWidth? contextDiv.offsetWidth : contextDiv.clientWidth);
		var height =(contextDiv.offsetHeight? contextDiv.offsetHeight : contextDiv.clientHeight);
		var iframe = document.getElementById(this.ctrlid+ "_iframe");
		if(!id){
		//******************** Mouse Positions *************** //
			left = mPos.x + 5 + findPosX(iframe) - (this.execObj.contentDocument.defaultView? this.execObj.contentDocument.defaultView.scrollX : 0);
			top = mPos.y + 5 + findPosY(iframe) - (this.execObj.contentDocument.defaultView? this.execObj.contentDocument.defaultView.scrollY : 0);
			if((left+width)>mPos.mX) left= left - width;
			if((top + height)>mPos.mY) top = top - height;
		}else{
		//******************* Element Positions ************** //
			var parentEl = document.getElementById(this.ctrlid + "_" + this.pref + "_context_menu_entry_" + id);
			left = findPosX(parentEl) + (parentEl.offsetWidth? parentEl.offsetWidth : parentEl.clientWidth) + 3;
			top = findPosY(parentEl);
			if((left+width)>mPos.mX) left= left - 2*width - 6;
			if((top + height)>mPos.mY) top = top - height;
		}
		
		contextDiv.style.left=left + 'px';
		contextDiv.style.top=top + 'px';
		
	}else{
		delete this.visibleMenus[(id? id : "main")];
		this.visibleMenusNum--;
		contextDiv.style.display="none";
	}
	
	// ******************* Cancel Event Propagandation ** //
	
	
	
}

CContextMenu.prototype.hideMenus=function(donthide){
	for(var i in this.visibleMenus){
		if(donthide && donthide[i]) continue;
		this.showHideMenu((i=="main"? null : i));
	}

}


CContextMenu.prototype.getVisibleMenusLength=function(){
	return this.visibleMenusNum;
}
