function HtmlTextInitializer(){
	this.m_div = document.getElementById("htmltextHolder");
	this.m_div.style.width="700px";
	this.m_div.style.height="399px";
	var div=Screen().center(this.m_div);
	this.m_div.style.display="none";
}

HtmlTextInitializer.prototype.Init = function(html){
	this.m_div.style.display="block";
	this.m_htmltext = new HtmlText("htmltext");
	this.m_htmltext.Display(html);
}

HtmlTextInitializer.prototype.cleanHtmlText = function(){
	var text= this.m_htmltext.CleanWord( this.m_htmltext.contentDocument.body.innerHTML);
	this.destruct();
	var cont = document.getElementsByName("content");
	if(!cont) return;
	cont = cont[0];
	cont.value = cont.value.substring(0, startPos)+ text+ cont.value.substring(endPos, cont.value.length);
}

HtmlTextInitializer.prototype.destruct = function(){
	
	this.m_div.style.display="none";
	this.m_htmltext.destruct();

}