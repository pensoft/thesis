function InsertCurTime(id) {
	var Stamp = new Date();
	var d = (Stamp.getDate() < 10 ? '0' + Stamp.getDate() : Stamp.getDate());
	var m = ((Stamp.getMonth() + 1) < 10 ? '0' + (Stamp.getMonth() + 1) : (Stamp.getMonth() + 1));
	var Y = Stamp.getFullYear();
	var H = (Stamp.getHours() < 10 ? '0' + Stamp.getHours() : Stamp.getHours());
	var i = (Stamp.getMinutes() < 10 ? '0' + Stamp.getMinutes() : Stamp.getMinutes());
	var datestr = d + '/' + m + '/' + Y  + ' ' + H  + ':' + i;
	document.getElementById(id).value = datestr;
}

function forumSearchMarkAll(type) {
	var form = document.forms['forumsearchsubmit'];
	var el, ord, checkname;
	if (type == 1) {
		checkname = 'delreply[]';
	} else {
		checkname = 'hidereply[]';
	}
	for (var i=0; i<form.elements.length; i++) {
		el = form.elements[i];
		if (el.type == 'checkbox' && el.name == checkname) {
			el.checked = true;
		}
	}
	return true;
}

function openw(url, title, options) {
	var newwin = window.open(url, title, options);
	newwin.focus();
}

function getOffset(el, which) {
	var amount = el["offset" + which];
	if (which=="Top")
		amount += el.offsetHeight;
	el = el.offsetParent;
	while (el != null) {
		amount += el["offset" + which];
		el = el.offsetParent;
	}
	return amount;
}

function h(obj) {	
	if (obj.f == "1") { 
		obj.f = "0";
		RevHighlight(obj);
	} else {
		obj.f = "1";
		Highlight(obj);
	}
	return;
}

function Highlight(obj) {		
	obj.cells[0].style.backgroundColor = "lightblue";	
	
	var cellsLength = obj.cells.length - 1;	
	for (var i = 1; i < cellsLength; i++) {
		obj.cells[i].style.backgroundColor = "lightblue";				
	}	
	obj.cells[cellsLength].style.backgroundColor = "lightblue";				
	
	return true ;
}

function RevHighlight( obj ) {		
	obj.cells[0].style.backgroundColor = "";		
	var cellsLength = obj.cells.length - 1;	
	for (var i = 1; i < cellsLength; i++) {
		obj.cells[i].style.backgroundColor = "";		
	}	
	obj.cells[cellsLength].style.backgroundColor = "";		
	return true ;
}

var allglobalopts = new Object();

function rld(targelname, targel, filtername, filterval) {
	if (!allglobalopts[targelname]) {
		allglobalopts[targelname] = targel.cloneNode(true).options;
		for(i = 0; i < allglobalopts[targelname].length; i++) {
			allglobalopts[targelname][i].selected = targel[i].selected;
		}
	}
	var allopt = allglobalopts[targelname];
	
	targel.options.length = 0;
	targel.selectedIndex = -1;
	for(i = 0; i < allopt.length; i++) {
		if (allopt[i].getAttribute(filtername) == filterval || allopt[i].value == "") {
			neweli = targel.options.length;
			targel.options[neweli] = new Option(allopt[i].text, allopt[i].value, false, allopt[i].selected);
		}
	}
	if(targel.onchange) targel.onchange();
}

var _CAL_IS_IE = (navigator.userAgent.toLowerCase().indexOf("msie") > -1) ? true : false;

var jscalcallerfrm = "";
var jscalcallerel = "";
var jscalalignment = "";

function jscalbuild(yy, mm) {
	d = new Date(yy, mm, 1);
	s = "";
	
	oldmonth = d.getMonth();
	oldyear = d.getRealYear();
	
	firstweekcnt = 0;
	firstweek = false;
	
	i = 1;
	
	while(oldmonth == d.getMonth()) {
		if (!firstweek) firstweekcnt++;
		
		if (d.getDay() == 1 && i > 1) {
			s += "</tr><tr>";
			firstweek = true;
		}
		
		if (d.getDay() == 0 || d.getDay() == 6) {
			bgtag = "style=\"background-color: #f8f8f0;\" ";
		} else {
			bgtag = "";
		}
		s += "<td " + bgtag + "align=\"center\" onMouseUp=\"jscalcellclick(this)\"><a href=\"#\" onClick=\"return jscalsetdate('" + (d.getDate() + "/" + (d.getMonth() + 1) + "/" + d.getRealYear()) + "')\">" + d.getDate() + "</a></td>";
		d.setDate(++i);
	}
	
	if (firstweekcnt == 8) firstweekcnt = 0;
	
	if (firstweekcnt > 1) {
		for (i = 0; i <  8 - firstweekcnt; i++) {
			s = "<td></td>" + s;
		}
	}
	
	nextmonth = d.getMonth();
	nextyear = d.getRealYear();
	d.setYear(oldyear);
	d.setMonth(oldmonth - 1);
	prevmonth = d.getMonth();
	prevyear = d.getRealYear();
	
	s = "<table class=\"cal\" width=\"100%\" cellspacing=\"1\" cellpadding=\"1\" border=\"0\"><tr><th>Mn</th><th>Tu</th><th>Wn</th><th>Th</th><th>Fr</th><th>Sa</th><th>Su</th></tr><tr>" + s + "</tr></table>";
	t = "<table border=\"0\" cellspacing=\"0\" cellpadding=\"1\" width=\"100%\" class=\"cal\"><tr>";
	if (jscalalignment=='right')
		t += "<td valign=\"top\" width=\"10\"><a href=\"#\" onClick=\"return jscalhide()\"><img src=\"/img/calico.gif\" border=\"0\"/></a></td>";
	t += "<td>&nbsp;<a title=\"Month Back\" style=\"text-decoration: none;\" href=\"#\" onMouseUp=\"return jscalrecalc(" + prevyear + ", " + prevmonth + ")\">&#9668;</a>&nbsp;</td>";
	t += "<td width=\"100%\" align=\"center\">" + (mm + 1) + "&nbsp;/&nbsp;" + yy + "</td>";
	t += "<td align=\"right\"><a title=\"Month Forward\" style=\"text-decoration: none;\" href=\"#\" onMouseUp=\"return jscalrecalc(" + nextyear + ", " + nextmonth + ")\">&#9658;</a></td>";
	if (jscalalignment=='left')
		t += "<td valign=\"top\" width=\"10\"><a href=\"#\" onClick=\"return jscalhide()\"><img src=\"/img/calico.gif\" border=\"0\"/></a></td>";
	t += "</tr></table>";
	
	return t + s;
}

function jscalsetdate(dt) {
	document.forms[callerfrm].elements[callerel].value = dt;
	jscalhide();
	return false;
}

function jscalcellclick(a) {
	a.firstChild.onclick();
	return false;
}

function jscalshow(w, targetfrm, targetel) {
	if (!w) w = event.srcElement;
	
	var tmpelwidth = w.offsetWidth;
	
	for ( var posl = 0, post = 0; w.style.position != 'absolute' && w.style.position != 'relative' && w.offsetParent; w = w.offsetParent ) {
		posl += w.offsetLeft; post += w.offsetTop;
	}
	
	post -= 3;
	posl -= 3;

	w = document.body.clientWidth;
	
	jscalalignment = "right";
	
	if (w - posl < 151) {
		jscalalignment = "left";
		posl -= 150 - tmpelwidth - 5;
	}

	var c = document.getElementById("calid");

	var ddd = new Date();
	c.innerHTML = jscalbuild(ddd.getRealYear(), ddd.getMonth());

	c.style.top = (post - 2) + 'px';
	c.style.left = (posl - 2) + 'px';
	c.style.display = "block";
	
	if (_CAL_IS_IE) {
		var fm = document.getElementById("calidfrm");
		fm.style.top = (post - 2) + 'px';
		fm.style.left = (posl - 2) + 'px';
		fm.style.width = c.offsetWidth;
		fm.style.height = c.offsetHeight;
		fm.style.display = "block";
	}
	
	callerfrm = targetfrm;
	callerel = targetel;
	
	return false;
}

function jscalhide() {
	c = document.getElementById("calid");
	c.style.display = "none";
	if (_CAL_IS_IE) {
		fm = document.getElementById("calidfrm");
		fm.style.display = "none";
	}
	
	return false;
}

function jscalrecalc(yy, mm) {
	c = document.getElementById("calid");
	c.innerHTML = jscalbuild(yy, mm);
	
	if (_CAL_IS_IE) {
		fm = document.getElementById("calidfrm");
		fm.style.height = c.offsetHeight;
	}
	
	return false;
}

// tva e shoto getYear pod vsichki browseri vrushta razlichni neshta
Date.prototype.getRealYear = function() {
	var lyear = this.getYear();
	if (lyear < 1500) lyear += 1900;
	return lyear;
}

function showAttAccessCode() {
	var access = document.forms['def1'].access;
	var accesscode = document.forms['def1'].accesscode;
	
	if (access.value == 1) {
		accesscode.disabled = false;
		accesscode.style.color = '#000';
	} else {
		accesscode.disabled = true;
		accesscode.style.color = '#eee';
		return false;
	}	
	return true;
}

function menuHide(sm) {
	sm.style.display = 'none';
}

function menuShow(sm) {
	var parUl = sm.parentNode;
	while (true) {
		if (parUl.nodeName.toLowerCase() == 'ul') break;
		parUl = parUl.parentNode;
	}
	if (parUl.id == 'menu') {
		sm.style.top = sm.parentNode.offsetHeight + 'px';
		sm.style.left = sm.parentNode.offsetLeft + 'px';
	} else {
		sm.style.top = sm.parentNode.offsetTop + 'px';
		sm.style.left = (sm.parentNode.offsetLeft + sm.parentNode.offsetWidth) + 'px';
	}
	sm.style.display = 'block';
}

function MenuOver(ev) {
	if (!ev) var ev = window.event;
	var from = ev.srcElement || ev.target;
	while (from) {
		if (from.nodeName.toLowerCase() == 'li') break;
		from = from.parentNode;
	}
	if (!from) return;
	
	var sm = from.getElementsByTagName('ul')[0];
	if (sm) {
		menuShow(sm);
	}
}

function MenuOut(ev) {
	if (!ev) var ev = window.event;
	var from = ev.srcElement || ev.target;
	var to = ev.toElement || ev.relatedTarget;
	
	while (from) {
		if (from.nodeName.toLowerCase() == 'li') break;
		from = from.parentNode;
	}
	if (!from) return;
	
	while (to) {
		if (to.nodeName.toLowerCase() == 'ul') break;
		to = to.parentNode;
	}
	
	if (!to) DropdownMenuInit(document.getElementById('menu'));
	
	var sm = from.getElementsByTagName('ul')[0];
	if (sm) {
		if (to && sm.id == to.id) return;
		menuHide(sm);
	}
}

function DropdownMenuInit(element) {
	var chld = element.childNodes;
	for (var i = 0; i < chld.length; i ++) {
		var sm = chld[i].getElementsByTagName('ul')[0];
		if (sm) {
			menuHide(sm);
			DropdownMenuInit(sm);
		}
	}
}

function ActivateMenu(id) {
	var m = document.getElementById(id);
	DropdownMenuInit(m);
	m.onmouseover = function(ev) {MenuOver(ev);};
	m.onmouseout = function(ev) {MenuOut(ev);};
}

var Confirm = false;
var SaveDelButton = false;

function SetEvent(pFormName) {
	var allinputfields = document.forms[pFormName].getElementsByTagName("input");
	var allselectfields = document.forms[pFormName].getElementsByTagName("select");
	var alltextareafields = document.forms[pFormName].getElementsByTagName("textarea");
	for(i = 0; i< allinputfields.length; i++) {
		if(allinputfields[i].type != 'submit') {
			allinputfields[i].onchange = function SetConfirm(){ Confirm = true;}
			allinputfields[i].onkeydown = function SetConfirm(){ Confirm = true;}
		}
	}
	for(i = 0; i< allselectfields.length; i++) {
			allselectfields[i].onchange = function SetConfirm(){ Confirm = true;}
			allselectfields[i].onkeydown = function SetConfirm(){ Confirm = true;}
	}
	for(i = 0; i< alltextareafields.length; i++) {
			alltextareafields[i].onchange = function SetConfirm(){ Confirm = true;}
			alltextareafields[i].onkeydown = function SetConfirm(){ Confirm = true;}
	}
}

function ConfirmToExit() {
	if (SaveDelButton === false && document.getElementById('rtfchanged').value != 0) {
		Confirm = true;
	}
	if (Confirm) {
		return "Промените които сте направили не са запазени!";
	}
}

function displayVideoInput() {
	var lFtype = document.getElementById('ftype');
	var lNormal = document.getElementById('normalVideoInput');
	var lEmbed = document.getElementById('embedVideoInput');
	
	if (lFtype.value == 4) { // normal video
		lEmbed.className = 'hiddenElm';
		lNormal.className = 'visibleElm';
	} else if (lFtype.value == 5) { // embed video
		lNormal.className = 'hiddenElm';
		lEmbed.className = 'visibleElm';
	}

}

function ChangeProductID() {
	var node = document.getElementById('productid');
	var productid = document.getElementById('manualproductid').value;
	var options = node.getElementsByTagName('option');
	
	if (productid % 1 != 0) {
		alert("ID на продукта трябва да е цяло число!");
	} else {
		var found = 0;
		for (var i = 0; i < options.length; i++) {
			var prodid = options[i].value;
			if (productid == prodid) {
				options[i].selected = true;
				ChangeProductQuantity(productid);
				found = 1;
				break;
			} else if (productid != prodid) {
				found = 0;
			}
		}
		if (!found) alert("Въвели сте несъществуващо ID на продукта!");
	}
}

function ChangeProductQuantity(val) {
	var node = document.getElementById('productquantityid');
	var manualproductid = document.getElementById('manualproductid');
	var productid = document.getElementById('productid').value;
	var options = node.getElementsByTagName('option');
	manualproductid.value = productid;
	var count = 0;
	for (var i = 0; i < options.length; i ++) {
		var prodid=options[i].getAttribute('productid');
		if( productid == prodid ) {
			options[i].style.display = 'block';
			if(count==0 || options[i].value == val ) {
				options[i].selected = true;
				count++;
			}
		}
		if (productid != prodid) {
			options[i].style.display = 'none';
			options[i].selected = false;
		}
	}
}

function addEvent(event, callback, object){
	if(typeof object == "undefined")
		return false;
		
	if(object.addEventListener)
		object.addEventListener(event, callback, false);
	else if(object.attachEvent)
		object.attachEvent("on"+event, callback);
	else
		return false;
	
	return true;
}


gMeta_part_of_host_publication = false;
gMeta_book_title = false;
gMeta_journal_name = false;
gMeta_journal_volume_number = false;
gMeta_publisher_name = false;
gMeta_publisher_location = false;
gMeta_journal_type = false;
gMeta_start_page = false;
gMeta_end_page = false;

gMetaTypeJournal = 1;
gMetaTypeBook = 2;

function InitMetaFields(){	
	gMeta_journal_name = document.getElementById('meta_journal_name');
	gMeta_journal_volume_number = document.getElementById('meta_journal_volume_number');
	
	gMeta_publisher_name = document.getElementById('meta_publisher_name');
	gMeta_publisher_location = document.getElementById('meta_publisher_location');
	gMeta_book_title = document.getElementById('meta_book_title');
	
	gMeta_part_of_host_publication = document.getElementById('meta_part_of_host_publication');
	gMeta_journal_type = document.getElementById('meta_journal_type');
	
	gMeta_start_page = document.getElementById('meta_start_page');
	gMeta_end_page = document.getElementById('meta_end_page');
	
	addEvent('change', function(){DisableMetaFields();}, gMeta_part_of_host_publication);
	addEvent('change', function(){DisableMetaFields();}, gMeta_journal_type);
	DisableMetaFields();
	
}

function DisableMetaFields(){
	if( gMeta_journal_type.value != gMetaTypeJournal ){
		gMeta_journal_name.setAttribute('readonly', 'readonly');
		gMeta_journal_name.className = 'coolinp disabledinput';
		
		gMeta_journal_volume_number.setAttribute('readonly', 'readonly');
		gMeta_journal_volume_number.className = 'coolinp disabledinput';
	}else{
		gMeta_journal_name.removeAttribute('readonly');
		gMeta_journal_name.className = 'coolinp';
				
		gMeta_journal_volume_number.removeAttribute('readonly');
		gMeta_journal_volume_number.className = 'coolinp';
	}
	
	if( gMeta_journal_type.value != gMetaTypeBook ){
		gMeta_book_title.setAttribute('readonly', 'readonly');
		gMeta_book_title.className = 'coolinp disabledinput';
		
		gMeta_publisher_name.setAttribute('readonly', 'readonly');
		gMeta_publisher_name.className = 'coolinp disabledinput';
		
		gMeta_publisher_location.setAttribute('readonly', 'readonly');
		gMeta_publisher_location.className = 'coolinp disabledinput';
	}else{
		if( gMeta_part_of_host_publication.checked ){
			gMeta_book_title.removeAttribute('readonly');
			gMeta_book_title.className = 'coolinp';
		}else{
			gMeta_book_title.setAttribute('readonly', 'readonly');
			gMeta_book_title.className = 'coolinp disabledinput';
		}
		
		gMeta_publisher_name.removeAttribute('readonly');
		gMeta_publisher_name.className = 'coolinp';
		
		gMeta_publisher_location.removeAttribute('readonly');
		gMeta_publisher_location.className = 'coolinp';
	}
	
	if(gMeta_part_of_host_publication.checked ){
		gMeta_start_page.removeAttribute('readonly');
		gMeta_start_page.className = 'coolinp';
		
		gMeta_end_page.removeAttribute('readonly');
		gMeta_end_page.className = 'coolinp';
	}else{
		gMeta_start_page.setAttribute('readonly', 'readonly');
		gMeta_start_page.className = 'coolinp disabledinput';
		
		gMeta_end_page.setAttribute('readonly', 'readonly');
		gMeta_end_page.className = 'coolinp disabledinput';
	}
}

gTypeSelect = false;
gModifierSelect = false;
gModifierSelectClone = false;
gPropertySelect = false;
gPropertySelectClone = false;
gPriorityInput = false;
gPlaceType = 1;
function initRuleSelects(){
	gTypeSelect = document.getElementById('type_id');
	gModifierSelect = document.getElementById('property_modifier_id');
	gPropertySelect = document.getElementById('property_id');
	gPriorityInput = document.getElementById('priority');
	
	gModifierSelectClone = gModifierSelect.cloneNode(true);
	gPropertySelectClone = gPropertySelect.cloneNode(true);
	changeRuleSelects();
}

function changeRuleSelects(){
	if( !gTypeSelect )
		return false;
	var lType = gTypeSelect.value;
	if( gModifierSelect ){		
		changeSelectValues(gModifierSelect, gModifierSelectClone, 'type_id', lType);
	}
	if( gPropertySelect ){		
		changeSelectValues(gPropertySelect, gPropertySelectClone, 'type_id', lType);
	}
	if( gPriorityInput ){
		if(lType == gPlaceType ){
			gPriorityInput.setAttribute('readonly', 'readonly');
			gPriorityInput.className = 'coolinp disabledinput';
		}else{
			gPriorityInput.removeAttribute('readonly');
			gPriorityInput.className = 'coolinp';
		}
	}
}

function changeSelectValues(pSelectButton, pSelectButtonClone, pAttributeName, pAttributeValue){
	var lTempButton = pSelectButtonClone.cloneNode(true);		
	var lCurOption = lTempButton.firstChild;
	var lSelectedValue = pSelectButton.value;
	var lDisableValue = false;
	
	while( lCurOption ){
		
		var lOption = lCurOption;
		lCurOption = lCurOption.nextSibling;
		
		if( lOption.nodeName.toLowerCase() == 'option' ){
			
			var lAttrVal = lOption.getAttribute(pAttributeName) ;
			if( lAttrVal && pAttributeValue && lAttrVal !=  pAttributeValue ){
				if( lSelectedValue == lOption.value )
					lDisableValue = true;
				lTempButton.removeChild(lOption);
			}
		}
	}
	
	pSelectButton.innerHTML = lTempButton.innerHTML;
	if( !lDisableValue ){
		pSelectButton.value = lSelectedValue;
	}
}

function rldContent(t, txt) {
	var element = document.getElementById(t);
	if (element.value == txt) {
		element.value = '';
	}
}

function rldContent2(t, txt) {
	var element = document.getElementById(t);
	if (element.value == ''){
		element.value = txt;
	}
}

function autoReduceText(pWidth, pFontSize) {
	lspan = document.getElementById('taxonName');
	var lFontSize = pFontSize;
	var lFlagFont = 0;
	
	while (lspan.offsetWidth > pWidth) {
		lspan.style.fontSize = lFontSize + 'px';
		lFontSize = lFontSize - 1;
		if(lFontSize == 0) {
			lFlagFont = 1;
			break;
		}
	}
	
	if(lFlagFont = 1) {
		while(lspan.offsetWidth > pWidth) {
			lInnerText = lspan.innerHTML;
			lspan.innerHTML = lInnerText.substr(0, lInnerText.length - 1);
		}
	}
} 

function getAjaxObject(){
	try{
		var xmlhttp = new XMLHttpRequest();		
	}catch(err1){
		var ieXmlHttpVersions = new Array();
		ieXmlHttpVersions[ieXmlHttpVersions.length] = "MSXML2.XMLHttp.7.0";
		ieXmlHttpVersions[ieXmlHttpVersions.length] = "MSXML2.XMLHttp.6.0";
		ieXmlHttpVersions[ieXmlHttpVersions.length] = "MSXML2.XMLHttp.5.0";
		ieXmlHttpVersions[ieXmlHttpVersions.length] = "MSXML2.XMLHttp.4.0";
		ieXmlHttpVersions[ieXmlHttpVersions.length] = "MSXML2.XMLHttp.3.0";
		ieXmlHttpVersions[ieXmlHttpVersions.length] = "MSXML2.XMLHttp";
		ieXmlHttpVersions[ieXmlHttpVersions.length] = "Microsoft.XMLHttp";

		var i;
		for (i=0; i < ieXmlHttpVersions.length; i++){
			try{
				var xmlhttp = new ActiveXObject(ieXmlHttpVersions[i]);
				break;
			}catch (err2){
				
			}
		}
	}
	return xmlhttp;
}

function RearrangeMenu(pMenuRowId){
	//Podrejdame menu-to poneje poslednite redove imat specialen klas
	
	var lParentMenu = document.getElementById(pMenuRowId);
	var lNormalRowClass = 'leftMenuRowHolder';
	var lLastRowClass = 'leftMenuRowHolder lastLeftMenuRowHolder';
	
	while( lParentMenu && lParentMenu.className != 'leftMenu' )
		lParentMenu = lParentMenu.parentNode;
	
	if( !lParentMenu ){
		return;
	}
	
	var lDivs = lParentMenu.getElementsByTagName('div');
	var lRows = new Array();
	for( var i = 0; i < lDivs.length; ++i ){
		var lDiv = lDivs[i];
		if( lDiv.className == lNormalRowClass || lDiv.className == lLastRowClass ){
			lRows.push(lDiv);
		}
	}
	
	for( var i =0; i < lRows.length; ++i ){
		var lDiv = lRows[i];
		if( i == lRows.length -1 ){
			lDiv.className = lLastRowClass;
		}else{
			lDiv.className = lNormalRowClass;
		}
	}
}

var gMapLoaded = false;
var gMapHasResult = false;
var gNCBILoaded = false;
var gNCBIHasResults = false;
var gBHLLoaded = false;
var gBHLHasResults = false;
var gImagesLoaded = false;
var gImagesHasResults = false;

function LoadMap(pElement, pAjaxRes){
	gMapLoaded = true;
	if( pAjaxRes['rescnt'] > 0 )
		gMapHasResult = true;
	DisplayNoDataMsg();
}

function LoadNCBI(pElement, pAjaxRes){
	gNCBILoaded = true;
	if( pAjaxRes['rescnt'] > 0 )
		gNCBIHasResults = true;
	DisplayNoDataMsg();
}

function LoadBHL(pElement, pAjaxRes){
	gBHLLoaded = true;
	if( pAjaxRes['rescnt'] > 0 )
		gBHLHasResults = true;
	DisplayNoDataMsg();
}

function LoadMorphbankImages(pElement, pAjaxRes){
	if( pAjaxRes['rescnt'] > 0 ){
		gImagesLoaded = true;
		gImagesHasResults = true;		
	}
	
}

function LoadWikimediaImages(pElement, pAjaxRes){
	gImagesLoaded = true;
	if( pAjaxRes['rescnt'] > 0 )
		gImagesHasResults = true;
	DisplayNoDataMsg();
}

function DisplayNoDataMsg(){
	if( !gMapLoaded || !gNCBILoaded || !gBHLLoaded || !gImagesLoaded )
		return;
	if( !gMapHasResult && !gNCBIHasResults && !gBHLHasResults && !gImagesHasResults ){
		var lNoDataMsg = document.getElementById('profile_nodata');
		if(lNoDataMsg){
			lNoDataMsg.style.display = 'block';
		}
	}
}

function HideExtLinkDivIfLinkDoesNotExist(pElementId, pAjaxRes){
	var lElement = document.getElementById(pElementId);
	if( !lElement ) 
		return;
	if( pAjaxRes['rescnt'] == 0 )
		lElement.style.display = 'none';
}



var gAjaxObjectsDescriptor = new ajaxObjectsDescriptor();

function AjaxLoad(pLink, pElementId, pTempElementId, pCallBack, pRequiredObjects){
	var lElement = document.getElementById(pElementId);
	if( !lElement )
		return;
	if( pRequiredObjects === null ||  pRequiredObjects === undefined )
		pRequiredObjects = new Array();
	
	gAjaxObjectsDescriptor.RegisterAjaxElement(pElementId, pRequiredObjects, pTempElementId, pCallBack);
	
	var AjaxObject = getAjaxObject();
	if(typeof AjaxObject == "undefined"){
		alert('In order to view this page your browser has to support AJAX')
		return;
	}
	AjaxObject.open("GET", pLink, true);
	AjaxObject.send(null);
	AjaxObject.onreadystatechange=function() {
		if (AjaxObject.readyState==4 && AjaxObject.status==200){
			try{
				var lRes = eval('(' + AjaxObject.responseText + ')');
				//~ lElement.innerHTML = lRes['result'];
				//~ if( pCallBack ){
					//~ pCallBack(pElementId, pTempElementId, lRes) ;
				//~ }
				
				gAjaxObjectsDescriptor.RegisterLoadedAjaxElement(pElementId, lRes);
			}catch(e){
			}
			
		}
	}
	return;
}

function correctIframeLinks(pIframe, pLinkPrefix){
	var lDocument = getIframeDocument(pIframe);
	var lWindow = window.frames[gbifIframe];
	if( !lDocument )
		return;
	evalIframe(pIframe.contentWindow, 'changeRootLocation = function(pLocation){parent.location.href = \'' + pLinkPrefix + '\' + encodeURIComponent(pLocation);}');
	var lLinks = lDocument.getElementsByTagName('a');
	for( var i = 0; i < lLinks.length; ++i ){
		var lLink = lLinks[i];
		var lLinkHref = lLink.getAttribute('href');
		lLink.setAttribute('href', pLinkPrefix + encodeURIComponent(lLinkHref));
	}
}

function getIframeDocument(pIframe){
	var lIframeDocument = pIframe.contentWindow || pIframe.contentDocument;
	if(lIframeDocument && lIframeDocument.document) 
		lIframeDocument = lIframeDocument.document;
	return lIframeDocument;
}

function evalIframe(pIframeWindow, pCommand) {
	if (!pIframeWindow.eval && pIframeWindow.execScript) {
		pIframeWindow.execScript("null");
	}
	pIframeWindow.eval(pCommand);
}

function DisplayArticleFinalizationForm(pArticleId){
	if(!pArticleId)
		return;
	$.ajax( {
		url : '/finalized_articles/finalize_form.php',
		dataType : 'json',
		type : 'POST',
		data : {
			article_id : pArticleId,			
		},
		success : function(pAjaxResult) {
			if (pAjaxResult['err_cnt']) {
				
			} else {
				$('#bg_shadow').show();
				$('#finalize_form').html(pAjaxResult['form']);				
				$('#finalize_form').show();
				var lHeight = $('#finalize_form').outerHeight();
				var lWidth = $('#finalize_form').outerWidth();
				$('#finalize_form').css('margin-top', - lHeight / 2);
				$('#finalize_form').css('margin-left', - lWidth / 2);
				$('body').attr('style', 'overflow:hidden');
				
			}
			return false;
		}
	});
}

function submitFinalizeForm(pArticleId){
	var lFormData = $('form[name="finalize_form"]').formSerialize();
	lFormData += '&tAction=save';
	$.ajax( {
		url : '/finalized_articles/finalize_form.php',
		dataType : 'json',
		type : 'POST',
		data : lFormData,
		success : function(pAjaxResult) {
			if (pAjaxResult['err_cnt']) {//Ако има грешка - показваме формата
				$('#finalize_form').html(pAjaxResult['form']);
				var lHeight = $('#finalize_form').outerHeight();
				var lWidth = $('#finalize_form').outerWidth();
				$('#finalize_form').css('margin-top', - lHeight / 2);
				$('#finalize_form').css('margin-left', - lWidth / 2);
			} else {//Ако всичко е ОК - отиваме
				window.location.href = '/finalized_articles/edit.php?id=' + pArticleId + '&tAction=showedit';			
			}
			return false;
		}
	});
}

function destroyFinalizeForm(){
	$('#finalize_form').hide();				
	$('#bg_shadow').hide();
	$('#finalize_form').html('');
	$('body').attr('style', '');
}

function GenerateExportXmls(pExportType, pJournalId, pIssue){
	ExecExportsAction(pExportType, pJournalId, pIssue, 'generate');
}

function UploadExports(pExportType, pJournalId, pIssue){
	ExecExportsAction(pExportType, pJournalId, pIssue, 'upload');
}

function ExecExportsAction(pExportType, pJournalId, pIssue, pAction){
	$('#loading_shadow').show();
	$.ajax( {
		url : '/lib/ajax_servers/exports_srv.php',
		dataType : 'json',
		type : 'POST',
		data : {
			action : pAction,
			export_type : pExportType,
			journal_id : pJournalId,
			issue : pIssue
		},
		success : function(pAjaxResult) {
			if (pAjaxResult['err_cnt']) {//Ако има грешка - показваме q
				alert(pAjaxResult['err_msg']);
			} else {//Ако всичко е ОК - изкарваме съобщението за успех и презареждаме страницата
				alert(pAjaxResult['msg']);				
			}
			window.location.reload();
			return false;
		}
	});
}