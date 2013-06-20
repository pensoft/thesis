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


function AjaxLoad(link, elementid){
	var element = document.getElementById(elementid);
	if( !element )
		return;
	var AjaxObject = getAjaxObject();
	if(typeof AjaxObject == "undefined"){
		alert('In order to view this page your browser has to support AJAX')
		return;
	}
	AjaxObject.open("GET", link, true);
	AjaxObject.send(null);
	AjaxObject.onreadystatechange=function() {
		if (AjaxObject.readyState==4 && AjaxObject.status==200){
			element.innerHTML = AjaxObject.responseText;
		}
	}
	return;
}

function reloadCaptcha() {
	var img = document.getElementById('cappic');
	img.src = '/lib/frmcaptcha.php?rld=' + Math.random();
	return false;
}

function rldContent(t, txt) {
	if (t.value == txt) {
		t.value = '';
	}
}

function rldContent2(t, txt) {
	if (t.value == '') {
		t.value = txt;
	}
}

function CheckLoginForm(frm, uname, upass) {
	if (frm.uname.value == uname) {
		frm.uname.value = '';
	}
	
	if (frm.upass.value == upass) {
		frm.upass.value = '';
	}

	return true;

}

function pollsubmit(p,t,cid) {
	var http_request = getAjaxObject();
	if (!http_request) return true;
	
	disablepollbuttons(p);
	
	http_request.onreadystatechange = function() { poll_submit_callback(http_request,cid); };
	
	var qry = generatepollquery(p);
	
	var lmethod = 'GET';
	
	http_request.open(lmethod, '/lib/poll_submit.php?type=' + t + '&' + (lmethod == 'GET' ? qry : ''), true);
	if (lmethod == 'POST') http_request.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
	http_request.send(lmethod == 'GET' ? null : qry);
	
	return false;
}

function pollsubmitleft(p,t,cid) {
	var http_request = getAjaxObject();
	if (!http_request) return true;
	
	disablepollbuttons(p);
	
	http_request.onreadystatechange = function() { poll_submit_callback(http_request,cid); };
	
	var qry = generatepollquery(p);
	
	var lmethod = 'GET';
	
	http_request.open(lmethod, '/lib/poll_submit_left.php?type=' + t + '&' + (lmethod == 'GET' ? qry : ''), true);
	if (lmethod == 'POST') http_request.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
	http_request.send(lmethod == 'GET' ? null : qry);
	
	return false;
}

var lastsubmitbut = '';
var lastsubmitval = '';

function poll_btnclick(b) {
	lastsubmitbut = b.name;
	lastsubmitval = b.value;
	return true;
}

function generatepollquery(f) {
	var retstr = "";
	for (var i = 0; i < f.elements.length; i++) {
		if (f.elements[i].type.toLowerCase() == 'text' || f.elements[i].type.toLowerCase() == 'textarea' 
			|| f.elements[i].type.toLowerCase() == 'hidden') {
			retstr += f.elements[i].name + "=" + escape(f.elements[i].value) + "&";
		} else if (f.elements[i].type.toLowerCase() == 'submit') {
			if (f.elements[i].name == lastsubmitbut && f.elements[i].value == lastsubmitval) retstr += f.elements[i].name + "=" + escape(f.elements[i].value) + "&";
		} else if (f.elements[i].type.toLowerCase() == 'select') {
			retstr += f.elements[i].name + "=" + escape(f.elements[i].options[f.elements[i].selectedIndex]) + "&";
		} else if (f.elements[i].type.toLowerCase() == 'radio' || f.elements[i].type.toLowerCase() == 'checkbox') {
			if (f.elements[i].checked) retstr += f.elements[i].name + "=" + escape(f.elements[i].value) + "&";
		}
	}
	return retstr;
}

function poll_submit_callback(p,cid) {
	if (p.readyState == 4 && p.status == 200) {
		var canketa = document.getElementById(cid);
		canketa.innerHTML = p.responseText;
		return;
	}
}

function disablepollbuttons(p) {
	for(i=0; i < p.elements.length; i++) {
		if (p.elements[i].type.toLowerCase() == 'submit') {
			p.elements[i].disabled = true;
		}
	}
}