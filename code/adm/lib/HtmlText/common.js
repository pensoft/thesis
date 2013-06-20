
function in_array(searchEl,arr){
	if(!arr || !searchEl) return false;
	for(var i=0;i<arr.length;i++)
		if(arr[i]==searchEl) return true;
	return false;
}
/*
function findPosX(obj) {
	var curleft = 0;
	if (obj.offsetParent)
	{
		while (obj.offsetParent)
		{
			curleft += obj.offsetLeft
			obj = obj.offsetParent;
		}
	}
	else if (obj.x)
		curleft += obj.x;
	return curleft;
}

function findPosY(obj) {
	var curtop = 0;
	if (obj.offsetParent)
	{
		while (obj.offsetParent)
		{
			curtop += obj.offsetTop
			obj = obj.offsetParent;
		}
	}
	else if (obj.y)
		curtop += obj.y;
	return curtop;
}

function getMousePosition(event){
	var posX = 0;
	var posY = 0;
	var maxPosX = 0;
	var maxPosY = 0;
	if (!event) var event = window.event;
	if (event.pageX || event.pageY){
		posX = event.pageX;
		posY = event.pageY;
		maxPosX = window.innerWidth + window.pageXOffset;
		maxPosY = window.innerHeight + window.pageYOffset;
	}else if (event.clientX || event.clientY) 	{
		posX = event.clientX + document.body.scrollLeft + document.documentElement.scrollLeft;
		posY = event.clientY + document.body.scrollTop + document.documentElement.scrollTop;
		maxPosX = document.body.clientWidth + document.body.scrollLeft;
		maxPosY = document.body.clientHeight + document.body.scrollTop;
	}
	
	return {"x" : posX, "y" : posY, "mX" : maxPosX, "mY" : maxPosY};
}
*/
function pasteFromWordInit(){
	getTextAreaCursorPos();
	Screen().grayout(true);
	init.Init('');
}

function closeHtmlText(){
	Screen().grayout(false);
	init.destruct();
}

function cleanHtmlText(){
	Screen().grayout(false);
	init.cleanHtmlText();
}

function getTextAreaCursorPos() {
	var cont = document.getElementsByName("content");
	if(!cont) return;
	cont = cont[0];
	if (cont.selectionStart || cont.selectionStart == '0') {
		startPos = cont.selectionStart;
		endPos = cont.selectionEnd;
	} else {
		startPos = (cont.value? cont.value.length : 0);
		endPos =(cont.value? cont.value.length : 0);
	}
}