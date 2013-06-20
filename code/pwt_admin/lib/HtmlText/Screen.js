function Screen(){
	if(Screen.centerdiv) return Screen; // we are already initialised
	
	Screen.grayfielddiv=document.body.appendChild(document.createElement('div'));
	Screen.grayfielddiv.id="grayfielddiv";
	Screen.grayfielddiv.style.position="fixed";
	Screen.grayfielddiv.style.display="none";
	Screen.grayfielddiv.style.left="0";
	Screen.grayfielddiv.style.top="0";
	Screen.grayfielddiv.style.width="100%";
	Screen.grayfielddiv.style.height="100%";
	Screen.grayfielddiv.style.zIndex="100";

	Screen.topleftdiv=document.body.appendChild(document.createElement('div'));
	Screen.topleftdiv.style.zIndex="110";
	Screen.topleftdiv.style.height="0";
	Screen.topleftdiv.style.width="0";
	Screen.topleftdiv.style.top="0";
	Screen.topleftdiv.style.left="0";
	Screen.topleftdiv.style.position="absolute";
	
	Screen.centerdiv=document.body.appendChild(document.createElement('div'));
	Screen.centerdiv.style.position="fixed";
	Screen.centerdiv.style.left="50%";
	Screen.centerdiv.style.top="50%";
	Screen.centerdiv.style.width="0";
	Screen.centerdiv.style.height="0";
	Screen.centerdiv.style.zIndex="120";
	
		// we are in what looks like explorer. or mozilla with compatibility libs, anyway
		if (_EWORKAROUND_IE) {
			// fixed positioning doesn't seem to be supported. oh well.
			Screen.centerdiv.style.position="absolute";
			Screen.grayfielddiv.style.position="absolute";
			
			// we install event handlers to emulate it.
			// hmm i should change this to set events with attachevent or something...
			window.onresize=window.onscroll=function(){
				var wx=window.innerWidth,wy=window.innerHeight;
				if(typeof(wx)!='number' && document.documentElement){
					wx=document.documentElement.clientWidth;
					wy=document.documentElement.clientHeight;
				}
				if(!wx && !wy){
					wx=document.body.clientWidth;
					wy=document.body.clientHeight;
				}
				
				var sx=0,sy=0;
				if(typeof(window.pageXOffset)!='undefined'){
					sx=window.pageXOffset;
					sy=window.pageYOffset;
				} else	if(document.documentElement && (document.documentElement.scrollLeft || document.documentElement.scrollTop)){
					sx=document.documentElement.scrollLeft;
					sy=document.documentElement.scrollTop;
				} else if(document.body && (document.body.scrollLeft || document.body.scrollTop)){
					sx=document.body.scrollLeft;
					sy=document.body.scrollTop;
				}				
				
				Screen.centerdiv.style.left=sx+wx/2+"px";
				Screen.centerdiv.style.top=sy+wy/2+"px";
				
				Screen.grayfielddiv.style.left=sx+"px";
				Screen.grayfielddiv.style.top=sy+"px";
			}
	}
	
	Screen.center=function(what){
		var div=Screen.centerdiv.appendChild(document.createElement('div'));
		var ifr=null;
		if (_EWORKAROUND_IE) {
			ifr=div.appendChild(document.createElement('iframe'));
			ifr.style.position='absolute';
			ifr.style.border="none";
			ifr.style.background="white";
			ifr.style.zIndex=-1;
			ifr.scrolling="no";
			ifr.frameborder=0;
	//		ifr.src="javascript:'';";
		//	what.style.position='absolute';
		}

		div.appendChild(what);
		if (ifr) {
			ifr.style.width=what.offsetWidth+"px";
			ifr.style.height=what.offsetHeight+"px";
		}
		div.style.position='absolute';
		div.style.left="-"+what.offsetWidth/2+"px";
		div.style.top="-"+what.offsetHeight/2+"px";
			

		what.style.position='fixed';
		
		what.style.zIndex=121;
		what.style.left="auto";
		what.style.top="auto";
		return div;
	}

	Screen.grayout=function(b){
		var grayfielddiv=Screen.grayfielddiv;
		if(b || typeof(b) == 'undefined') grayfielddiv.style.display="block";
		else grayfielddiv.style.display="none";
	}

	return Screen;
}
