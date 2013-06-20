//EObjectCache: za vbydeshte po ts triabva da stava iasno na server-a koga e promeniano posledno
function EObjectCache(sserver,oclass,oinstance,oaddxml) {
	this.SetURL=EOC_SetURL;
	this.RegisterDepend=EOC_RegisterDepend;
	this.setDirty=EOC_setDirty;
	this.UpdatefromDependence=EOC_UpdatefromDependence;
	this.BeforeUpdatefromDependence=EOC_BeforeUpdatefromDependence;
	this.CheckDepState=EOC_CheckDepState;
	this.SetURL(sserver);
	this.oclass=oclass;
	this.oinstance=oinstance;
	this.oaddxml=oaddxml;
	this.dynamic=0;
	this.ts=0;
	this.main=-1;
	this.state=0;
	this.dna=0;
	this.waitforyou=new Array();
	this.m_deparr= new Object();//tuk Array
}
function EOC_SetURL(strUrl) {
if (strUrl)
	this.sserver=(strUrl.search(/:/i)>=0?strUrl:window.location.protocol+"//"+window.location.host+strUrl);	
}

function EOC_RegisterDepend(src_disp_index, srcNodeName, trgNodeNodeName) {
	if (!this.m_deparr[src_disp_index]) this.m_deparr[src_disp_index]= new Object();
	if (!this.m_deparr[src_disp_index][trgNodeNodeName]) this.m_deparr[src_disp_index][trgNodeNodeName]={};
	this.m_deparr[src_disp_index][trgNodeNodeName].dirty=1;
	this.m_deparr[src_disp_index][trgNodeNodeName].srcNodeName=srcNodeName;
	this.m_deparr[src_disp_index][trgNodeNodeName].src_disp_index=src_disp_index;
}

function EOC_setDirty(src_disp_index, srcNodeName, trgNodeNodeName) {
	if (this.m_deparr[src_disp_index][trgNodeNodeName].src_disp_index!=src_disp_index)
		alert("Error: setDirty - dublicate dependence for one field!!!");
	else this.m_deparr[src_disp_index][trgNodeNodeName].dirty=1;
}

function EOC_CheckDepState(req,ourindex) {
	var resstate=req;
	var reqXML=null;
	//if (this.oinstance=="relations_contacts_agrid")
	for (var i in this.m_deparr) {
		if (dispecher.ObjectsCache.Element(i).state==0) {
			pg.m_hashObjects[dispecher.ObjectsCache.Element(i).oinstance].Display(null,1); //Dali toz metod da vikam
			resstate=null;
		}
		if (!resstate) continue;
		if ((dispecher.ObjectsCache.Element(i).state!=4) && ((dispecher.ObjectsCache.Element(i).state!=-1) || (dispecher.ObjectsCache.Element(i).oaddxml))) {
			resstate=null;
			dispecher.ObjectsCache.Element(i).waitforyou[ourindex]=1;
			break;
		} else
			for (var Field in this.m_deparr[i]) {
				if (this.m_deparr[i][Field].dirty) {
					//if (this.oinstance=="relations_contacts_agrid")
					if (!reqXML) {
						reqXML=new EWorkaround.newDomDocument();	
						reqXML.async=false;
						reqXML.loadXML(req);
					}
					var s=pg.m_hashObjects[dispecher.ObjectsCache.Element(i).oinstance].getNodeValue(this.m_deparr[i][Field].srcNodeName);
					//var xmlNode, xmlrNode;
					if (!this.oaddxml)	{
						pg.m_hashObjects[this.oinstance].setNodeValue(Field, s, "/root/" + (pg.m_hashObjects[this.oinstance].NewObjectType=="Form" ? "formdata" : pg.m_hashObjects[this.oinstance].NewObjectType=="Grid" ? "gridrequest" : "*"), true , false );
						pg.m_hashObjects[this.oinstance].setNodeValue(Field, s, "/root/" + (pg.m_hashObjects[this.oinstance].NewObjectType=="Form" ? "formdata" : pg.m_hashObjects[this.oinstance].NewObjectType=="Grid" ? "gridrequest" : "*"), true , false, reqXML);
					} else {
						pg.m_hashObjects[this.oinstance].setNodeValue(Field, s, "/root/additionalxmls/"+this.oaddxml+"/root/gridrequest", true , false, pg.m_hashObjects[this.oinstance].m_xmlOther);
						pg.m_hashObjects[this.oinstance].setNodeValue(Field, s, "/root/gridrequest", true , false, reqXML);
						
					/*
						xmlNode=pg.m_hashObjects[this.oinstance].m_xmlOther.selectSingleNode("/root/additionalxmls/"+this.oaddxml+"/root/gridrequest/"+Field);
						xmlrNode=reqXML.selectSingleNode("/root/gridrequest/"+Field);
						*/
					}
//tuka
	/*
					xmlNode.text=s;
					xmlrNode.text=s;
					*/
					this.m_deparr[i][Field].dirty=0;
				}
			}
	}
	if (reqXML && resstate)
		return reqXML.documentElement.xml;
	return resstate;
}

function EOC_BeforeUpdatefromDependence(dontreload) {
	//console.log("BeforeUpdatefromDependence: "+this.oinstance +" ::  "+this.oaddxml+"  "+(!pg.m_hashObjects[this.oinstance].m_hidden)+" : "+(dontreload!=2));
	if (!pg.m_hashObjects[this.oinstance].m_hidden && (dontreload!=2))	{
		this.state=0;
		//console.log("BeforeUpdatefromDependence: "+this.oinstance +" ::  "+this.oaddxml+"  "+this.state);
	}
}
function EOC_UpdatefromDependence(dontreload) {
	var xmlNode; 
	if (!this.oaddxml)	{
		xmlNode=pg.m_hashObjects[this.oinstance].m_xmlRequest.selectSingleNode("/root/" + (pg.m_hashObjects[this.oinstance].NewObjectType=="Form" ? "formdata" : pg.m_hashObjects[this.oinstance].NewObjectType=="Grid" ? "gridrequest" : "*"));
	} else
		xmlNode=pg.m_hashObjects[this.oinstance].m_xmlOther.selectSingleNode("/root/additionalxmls/"+this.oaddxml);
	if (xmlNode) xmlNode.removeAttribute("updated");
	this.state=0;
	//console.log(this.oinstance +" ::  "+this.oaddxml+"  "+(!pg.m_hashObjects[this.oinstance].m_hidden)+" : "+(dontreload!=2));
	if (!pg.m_hashObjects[this.oinstance].m_hidden && (dontreload!=2))	{
		if (this.oaddxml) {	
			if (!this.dynamic) pg.m_hashObjects[this.oinstance].initSingleAddXml(null, this.oaddxml,false);
		} else {
			pg.m_hashObjects[this.oinstance].RequestFromSoap((pg.m_hashObjects[this.oinstance].NewObjectType=="Form" ? null : pg.m_hashObjects[this.oinstance].m_nCurPage), null,false);
			if (!DOASYNCREQ || (pg.m_hashObjects[this.oinstance].m_sync==true)) {
				this.state=4;
				var gr=pg.m_hashObjects[this.oinstance];
				var rn=gr.m_xmlRequest.selectSingleNode("/root/gridrequest");
				if (rn) {
					rn.setAttribute("updated",'1');
					if(gr.m_progbar){
						gr.m_progbar.remove();
						gr.m_progbar=null;
					}
					var donotjoin=gr.CheckAddXMLsForUpdate(0);
					if (donotjoin>-1) gr.RefreshView(donotjoin);
				}
				
			}
		}
			//Dali toz metod da vikam
	}
}

//start EActiveObject
function EActiveObject(i,oinstance,dontuseStdActivity) {
	this.sserver=dispecher.ObjectsCache.Element(i).sserver;
	this.self=i;
	this.oinstance=oinstance;
	this.custominst=-1;
	this.m_asubmissions=new Object();
	this.m_autoexpand=new Object();
	this.SetAdditionalDepend=EAO_SetAdditionalDepend;
	this.CheckObjDepend=EAO_CheckObjDepend;
	this.SetStandartActivity=EAO_SetStandartActivity;
	this.SubmissionSuccess=EAO_SubmissionSuccess;
	this.Events={};
	if (!dontuseStdActivity) 
		this.SetStandartActivity(); 
	
}

function EAO_SubmissionSuccess(submission, willclose) {
	var objc,  j, needselfrefresh=0;
	var needupdate= new Array();
	if (this.Events[submission]) {
		if (this.Events[submission].request && this.Events[submission].SS) {
			var x=new EWorkaround.newDomDocument();	
			x.async = false;
			x.loadXML(this.Events[submission].request.documentElement.xml);
			var dependsonList=x.selectNodes("//dependson[@object='"+this.oinstance+"']");
			var dependson,node,value;
			for (var i=0; i<dependsonList.length; i++) {					
				dependson = dependsonList.item(i);			
				if (node=dependson.selectSingleNode("@node")) {
				value=pg.m_hashObjects[this.oinstance].m_xmlRequest.selectSingleNode("/root/"+ (pg.m_hashObjects[this.oinstance].NewObjectType=="Form" ? "formdata" : pg.m_hashObjects[this.oinstance].NewObjectType=="Grid" ? "gridrequest" : "*")+"/"+node.text).text;
				EWorkaround.setNodeText(dependson.parentNode,value);
				dependson.parentNode.removeChild(dependson);
				}
			}
			var res=dispecher.Call( "DoAction", -2, x.documentElement.xml,submission,1,this.Events[submission].SS);
			//alert(res); -- da se opravi pri greshka kvo da stava
		}
		else {
			if (this.Events[submission].methodlocation && this.Events[submission].methodname) {
				if (this.Events[submission].targetinstance && this.Events[submission].methodlocation=="pg") {
					eval(this.Events[submission].methodlocation+".m_hashObjects['"+this.Events[submission].targetinstance+"']."+this.Events[submission].methodname+"()");
				} else eval(this.Events[submission].methodlocation+"."+this.Events[submission].methodname+"()");
			} else alert("Events: V momenta ima support samo pri request!!!"); 
		//targetinstance" :hashElAttrs["targetinstance"], "methodlocation" :hashElAttrs["methodlocation"], "methodname
		
		}
	}
	if (this.m_asubmissions[submission] && this.m_asubmissions[submission].length>0) {
		for(var i=0;i<this.m_asubmissions[submission].length;i++) {
			objc=dispecher.ObjectsCache.Element(this.m_asubmissions[submission][i]);
			objc.ts=0;
			if (dispecher.SoapCache[objc.sserver]) {
				//?? for(j in dispecher..SoapCache[url]) delete dispecher.SoapCache[url][j];
				delete dispecher.SoapCache[objc.sserver];
			}
			if (objc.main!=this.self) needupdate[objc.main]=1; else needselfrefresh=1;
		}
		for(i in needupdate) {
			objc=dispecher.ObjectsCache.Element(i);
			if (pg.m_hashObjects[objc.oinstance] && !pg.m_hashObjects[objc.oinstance].m_hidden && ((willclose!=1) || !pg.m_hashObjects[objc.oinstance].m_objParent || (pg.m_hashObjects[objc.oinstance].m_objParent.m_strName!=this.oinstance))) pg.m_hashObjects[objc.oinstance].Display(null,1);
		}
	}
	if (dispecher.SoapCache[this.sserver]) delete dispecher.SoapCache[this.sserver];
	return needselfrefresh;
}

function EAO_SetAdditionalDepend(additionaldependinstance,submission) {
	var index, i;
	//this.custominst=dispecher.ObjectsCache.Push(null,null,additionaldependinstance,null);
	index =dispecher.ObjectsCache.Push(null,null,additionaldependinstance,null);
	if (!submission) {
		for (submission in this.m_asubmissions) {
			for(i=0;i<this.m_asubmissions[submission].length;i++)
				if (this.m_asubmissions[submission][i]==index) break;
			this.m_asubmissions[submission][i]=index;
		}
	} else {
		for(i=0;i<this.m_asubmissions[submission].length;i++)
				if (this.m_asubmissions[submission][i]==index) break;
		this.m_asubmissions[submission][i]=index;
	}
}

function EAO_CheckObjDepend(index) {
	var submission, i;
	if ( (this.self == index) || ( this.custominst!=index && (this.sserver!=dispecher.ObjectsCache.Element(index).sserver  || ((dispecher.ObjectsCache.Element(index).main==index) &&  (pg.m_hashObjects[dispecher.ObjectsCache.Element(index).oinstance] && pg.m_hashObjects[dispecher.ObjectsCache.Element(index).oinstance].NewObjectType == "Form")))) || (dispecher.ObjectsCache.Element(index).dna)) return ;
	for (submission in this.m_autoexpand) 
		if (this.m_autoexpand[submission]) {
			for(i=0;i<this.m_asubmissions[submission].length;i++)
				if (this.m_asubmissions[submission][i]==index) break;
			this.m_asubmissions[submission][i]=index;	
		}
}

function EAO_SetStandartActivity() {
	this.m_asubmissions["Insert"]=new Array();
	this.m_asubmissions["Save"]=new Array();
	this.m_asubmissions["Delete"]=new Array();
	this.m_autoexpand["Insert"]=1;
	this.m_autoexpand["Save"]=1;
	this.m_autoexpand["Delete"]=1;
}
//end EActiveObject

//start EALLObjectsCache
function EALLObjectsCache() {
	this.m_cache=new Array();
//	this.CheckAndRegisterNew=EALLO_CheckAndRegisterNew();
	this.Push=EALLO_Push;
	this.CheckObjState=EALLO_CheckObjState;
	this.Element=EALLO_Element;
	this.SetObjInitialActivity=EALLO_SetObjInitialActivity;
	this.GetFailedObjState=EALLO_GetFailedObjState;
}

function EALLO_SetObjInitialActivity(instance) {
	var ao=dispecher.ActiveObjects[instance];
	if (ao) 
		for (var i=0; i<this.m_cache.length; i++)
			if (this.m_cache[i]) ao.CheckObjDepend(i);
}
function EALLO_Element(i) {
	if (i<0) return null; else return this.m_cache[i];
}
function EALLO_Push(sserver,oclass,oinstance,oaddxml) {
	var i=0,upd=0, main=-1;
//	if (!ocashe) return -1;
	for(i=0; i<this.m_cache.length; i++) {
		if (this.m_cache[i].oinstance==oinstance)
			if (this.m_cache[i].oaddxml==oaddxml) break; else main=this.m_cache[i].main;
	}
	if (i<this.m_cache.length) {
		if (sserver) {this.m_cache[i].SetURL(sserver); upd=1;}
		if (oclass) {this.m_cache[i].oclass=oclass; upd=1;}
		this.m_cache[i].oinstance=oinstance;
		this.m_cache[i].oaddxml=oaddxml;
		if (upd) this.m_cache[i].ts=0;
	}
	else {
			this.m_cache[i]=new EObjectCache(sserver,oclass,oinstance,oaddxml);
			if (!oaddxml) this.m_cache[i].main=i;
			else this.m_cache[i].main= ((main > -1 )? main :this.Push( null, null, oinstance, null));
		}
	return i;	
}

function EALLO_CheckObjState(index) {
	var i;
	for(i=0; i<this.m_cache.length; i++) {
		if ((this.m_cache[i].main==index) && (this.m_cache[i].state!=4) && (this.m_cache[i].state>-1) && !this.m_cache[i].dynamic) {
			return false;
			}
	}
	return true;
}
function EALLO_GetFailedObjState(index) {
	var i;
	for(i=0; i<this.m_cache.length; i++) {
		if ((this.m_cache[i].main==index) && (this.m_cache[i].state!=4) && (this.m_cache[i].state>-1) && !this.m_cache[i].dynamic)  {
			return this.m_cache[i].oinstance+" "+this.m_cache[i].oaddxml+" : "+this.m_cache[i].state+ " index: "+i+" dyn: "+this.m_cache[i].dynamic;
			}
	}
	return "vsichko ok";
}
//start EALLObjectsCache

function EDispecher() {
	this.SoapCache=new Object();
	this.ObjectsCache=new EALLObjectsCache();
	//this.ObjectsCache=new  Array();
	this.ActiveObjects=new Object();
	this.RegisterInstance=ED_RegisterInstance;
	this.CheckNeedUpdate=ED_CheckNeedUpdate;
	this.RegisterEvent=ED_RegisterEvent;
	this.MarkSuccessSubmision=ED_MarkSuccessSubmision;
	this.SetAdditionalDepend=ED_SetAdditionalDepend;
	this.isCashedSubmision=ED_isCashedSubmision;
	this.Call=ED_SC_Call;
	this.Result=ED_SC_Result;
	this.GetLastInstance=ED_GetLastInstance;
	this.GetFileContent=ED_GetFileContent;
	this.RegisterDepend=ED_RegisterDepend;
	this.LOIndex=-1;
	this.unloading=0;
	//trebe da se opravi toz kod
	if (soapbrowser == 'unknown') {
		var t_soapservice = null;
		if( typeof ActiveXObject != "undefined" )
			try {
				t_soapservice = new ActiveXObject("Msxml2.XMLHTTP");
				this.m_soapbrowser = 'ie2';
			} catch (e) {
				try {
					t_soapservice = new ActiveXObject("Microsoft.XMLHTTP");
					this.m_soapbrowser = 'ie1';
				} catch (e) {}
			}
		
		if(!t_soapservice) {
			// MOZILLA
			try {
				t_soapservice = new XMLHttpRequest();
				this.m_soapbrowser = 'mz';
			} catch (e) {}
		}
		
		if(!t_soapservice) {
			alert('This browser does not support soap requests.');
			this.m_soapbrowser  = null;
		}
		
		delete t_soapservice;
	} else this.m_soapbrowser =soapbrowser;

}

function ED_RegisterDepend(src_disp_index, srcNodeName, dst_disp_index, trgNodeNodeName) {
	var objc;
	objc=this.ObjectsCache.Element(dst_disp_index);
	if (!objc) alert('Error: RegisterDepend -invalid destination index!'); 
	else objc.RegisterDepend(src_disp_index, srcNodeName, trgNodeNodeName);	
}

function ED_GetLastInstance() {
	return this.ObjectsCache.Element(this.LOIndex).oinstance;
}

//v momenta poddyrzha samo edin cached submission na obekt
function ED_SC_Call(doaction , index , request, submission, bdontUseCache, overwriteurl,async, callbackfunc, callbackinstance,callbackparam, pPostParams) {
	var url, cacheobj = null ;
	var soapservice;
	var j=0;
	if (typeof(async)=="undefined") async=false;
	if (typeof(callbackfunc)=="undefined") callbackfunc=false;
	if (typeof(callbackinstance)=="undefined") callbackinstance=false;
	if (index==-1)
		alert("haha");
	if (index != -2) { //polzvam go za Events pri success submission
		if (this.ActiveObjects[this.ObjectsCache.Element(index).oinstance])
			var evarr=this.ActiveObjects[this.ObjectsCache.Element(index).oinstance].beforeEvents;
		else
			var evarr=null;
		if(evarr && evarr[submission])
			if (evarr[submission].methodlocation && evarr[submission].methodname) {
				//console.log("Event triggered:", evarr[submission].methodname, evarr[submission].methodlocation);
				var result;
				if (evarr[submission].targetinstance && evarr[submission].methodlocation=="pg") {
					var eventdetails={object:this,method:ED_SC_Call,arguments: arguments};
					result=pg.m_hashObjects[evarr[submission].targetinstance][evarr[submission].methodname](eventdetails);
				} else
					//result=evarr[submission].methodlocation[evarr[submission].methodname]();
					alert('unsupported method location for beforeEvent');
				
				if(result==false) return null;
			} else {
				alert('beforeEvent lacks needed properties');
			}
	
		request=this.ObjectsCache.Element(index).CheckDepState(request,index);
		if (!request) 
			return null;
		url=this.ObjectsCache.Element(index).sserver;
		this.ObjectsCache.Element(index).state=1;
		if (this.isCashedSubmision(submission)) {
			/* trebva chitav reg exp det da fashtame tez det se razminavat po whitespace-ove samo
			r= /\s/g;
			request=request.replace(r,'');
			*/
			if (!this.SoapCache[url]) {
				this.SoapCache[url]= new Array();
				this.SoapCache[url][index]= new Array( request, null);
			} else {
				for (i in this.SoapCache[url]) {
					if (request==this.SoapCache[url][i][0]) {
						if (bdontUseCache) {
							delete this.SoapCache[url][i];
							break;
						} else {
							this.ObjectsCache.Element(index).ts=1;
							this.LOIndex=this.ObjectsCache.Element(index).main;
					//		alert(j+" :ot kesha "+url); 
							if (this.SoapCache[url][i][1]) { 
								//this.ObjectsCache.Element(index).state=4;
								return this.SoapCache[url][i][1];
							} else cacheobj=-1; //Trenk: Da se opravi s CallBack
						}
					}// else j=j+1;
				}
				//if (j) alert(j+" : "+url);
				if (this.SoapCache[url][index]) delete this.SoapCache[url][index];
				this.SoapCache[url][index]= new Array( request, null);
			}
			if (cacheobj!=-1) cacheobj=this.SoapCache[url][index]; else cacheobj=null;
		}
	} else {
		url=overwriteurl;
	}
		//console.log(index,url, index >-1 ? this.ObjectsCache.Element(index).oinstance : index ,request);
		soapservice = EWorkaround.newXMLHttpRequest();
		if (async) {
			var oinstance=this.ObjectsCache.Element(index) ? this.ObjectsCache.Element(index).oinstance : null;
			soapservice.onreadystatechange = function (){
				if (this.unloading) return ; 
				if (soapservice.readyState == 4) {
					if (!callbackfunc)
						if((typeof(pg)!="undefined") && pg && pg.m_hashObjects[oinstance]) pg.m_hashObjects[oinstance].AsyncProccess(index, soapservice, cacheobj, submission);
						else {
							if ((typeof(console)!="undefined") && (console) && window.parent.FC.DEBUG && (typeof(window.parent.FC.DEBUG) != "undefined")) console.log("no such instance",oinstance);
						}
					else if (callbackinstance) {
						if ((typeof(pg)!="undefined") && pg) pg.m_hashObjects[callbackinstance][callbackfunc](soapservice,callbackparam);
					} else callbackfunc(soapservice,callbackparam);
				} else if ((index > -1) && typeof(dispecher)!="undefined") dispecher.ObjectsCache.Element(index).state=soapservice.readyState;
			};
		}
		/*
		if ((index >-1) && !this.ObjectsCache.Element(index).oaddxml) {
			console.log("CALL: ",url, " R: ",request);
			console.log("tuk");
		}*/
		if (this.unloading) return null; 
		soapservice.open("POST",  url, async);
		if (typeof(dontusesoap)=="undefined" || !dontusesoap) {
			soapservice.setRequestHeader("MessageType", "CALL");						
			soapservice.setRequestHeader("Content-Type", "text/xml");	
			var str = request.replace(/&/g,"&amp;");
			str = str.replace(/</g,"&lt;");
			str = str.replace(/>/g,"&gt;");
			try {
				soapservice.send(
				'<?xml version=\'1.0\'?><SOAP-ENV:Envelope SOAP-ENV:encodingStyle="http://schemas.xmlsoap.org/soap/encoding/" xmlns="" \
					xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soapenc="http://schemas.xmlsoap.org/soap/encoding/" xmlns:wsdl="http://schemas.xmlsoap.org/wsdl/" xmlns:soap="http://schemas.xmlsoap.org/wsdl/soap/" \
					xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/" xmlns:http="http://schemas.xmlsoap.org/wsdl/http/" \
					xmlns:s="http://www.w3.org/2001/XMLSchema" xmlns:s0="'+url+'" xmlns:tm="http://microsoft.com/wsdl/mime/textMatching/" xmlns:mime="http://schemas.xmlsoap.org/wsdl/mime/" \
					xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"><SOAP-ENV:Body><mswsb:' + doaction+ ' xmlns:mswsb="servernamespace" xmlns=""><a xsi:type="s:string" xmlns="">' + str + '</a></mswsb:' + doaction+ '>\
					</SOAP-ENV:Body></SOAP-ENV:Envelope>'
				);
			}  catch (e) {};
		} else {
			soapservice.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded; charset=UTF-8');
			//soapservice.setRequestHeader('encoding', 'UTF-8');
			//if (request.indexOf('као')) alert(escape('као'));
			var lRequest = "postxml="+encodeURIComponent(request)+(doaction=="CheckAction" ? "&action=1":"")
			for(var i in pPostParams){
				lRequest = lRequest + '&' + i + '=' + encodeURIComponent(pPostParams[i]);
			}
			try {
				soapservice.send(lRequest);
			} catch (e) {}
		}
		if (async) {
			return null;
		}
		if (index>-1) this.ObjectsCache.Element(index).state=4;
		return this.Result(index,soapservice,cacheobj);
			
	//}
	
}

function ED_SC_Result(index,soapservice,cacheobj) {
	if (this.unloading) return null; 
	if (!soapservice || soapservice.status != 200) {
		if (index > -1) { 
			alert("SoapCall error for index: "+index+"instance: "+this.ObjectsCache.Element(index).oinstance+" addxml: "+this.ObjectsCache.Element(index).oaddxml+"\n" + "\n Response:" + (soapservice? soapservice.responseText:"No soapservice object"));
			this.ObjectsCache.Element(index).state=-1;
		} else alert("SoapCall error for index: "+index+"\n" + "\n Response:" + (soapservice? soapservice.responseText:"No soapservice object"));
		return null;
	} else {	
		var r, ress=null, resxml;
		//~ if (!soapservice.responseXML) {
			//~ if (_EWORKAROUND_IE) {
				//~ var str=soapservice.responseText;
				//~ if (str) {
					//~ var rpos=str.indexOf("<?xml");
						//~ if (rpos>0) {
							//~ if (typeof(dontusesoap)=="undefined" || !dontusesoap) {
								//~ var resxml=new EWorkaround.newDomDocument();							
								//~ resxml.async=false;
								//~ resxml.loadXML(str.substr(rpos));
								//~ r=resxml.selectSingleNode("//res");
								//~ if (r) ress=r.text;
							//~ } else ress=str.substr(rpos);
						//~ }
				//~ }
			//~ }
		//~ } else 
			//~ if (typeof(dontusesoap)=="undefined" || !dontusesoap) {
				//~ r=soapservice.responseXML.selectSingleNode("//res");
				//~ if (r) ress=r.text;
			//~ } else ress=soapservice.responseXML.xml;
		//~ if(!ress){
			//~ alert("SoapCall error for index: "+index+"\n" + "\n Response:" + (soapservice? soapservice.responseText:"No soapservice object"));
			//~ if (index != -2) dispecher.ObjectsCache.Element(index).state=-1;
			//~ return null;
		//~ } 		
		//~ if (cacheobj ) {
			//~ cacheobj[1]=ress;
			//~ if (index != -2) {
				//~ this.ObjectsCache.Element(index).ts=1;
				//~ this.LOIndex=this.ObjectsCache.Element(index).main;
			//~ }
		//~ }
		if( !soapservice.responseXML ){
			ress = soapservice.responseText;
		}else{
			ress = soapservice.responseXML;
		}
		return ress;
	}
}

function ED_GetFileContent(url, slient) {
	var req;
	req=EWorkaround.newXMLHttpRequest();
	if (!req) {
		alert("Unsupported browser\n.");
		return null;
	}
        req.open("GET", url, false);
		req.setRequestHeader("Cache-Control", "no-cache, must-revalidate");
		req.setRequestHeader("Pragma", "no-cache");
		req.setRequestHeader("If-Modified-Since", "Wed, 15 Nov 1995 00:00:01 GMT");
        req.send(null);
	if (req.status == 200) {
		if (req.responseText.substr(0,4)=="HTTP") {
			var rpos=req.responseText.indexOf("\n\n");
			if (rpos>0) {
				return req.responseText.substr(rpos);
			} else {
				alert("Ne moga da drypna: "+url);
				return "";
			}
		} return req.responseText;
        } else {
            if (slient!=1)
	    alert("There was a problem retrieving file:" +url+"\n Error Text:"+
                req.statusText);
		return null;
	}
}

function ED_isCashedSubmision(submission) {
	if (submission!="Get" && submission!="GetData") return 0; else return 1; 
}

//Taz se vika ako si sys syshtia grid request za vbydeshte triabva da se napravi da se check-va soapserver i da raboti sys timestamp
function ED_CheckNeedUpdate(index, submission) {
	var objc;
	if (!this.isCashedSubmision(submission)) return 1;  
	if (index<0 ) {
		if (DEBUG) 
			alert("EDispecher.CheckNeedUpdate: Invalid index!");
		return 0;
	}
	objc=this.ObjectsCache.Element(index);
	if (objc.ts>0) return 0; else return 1;	
}

function ED_RegisterInstance(instname,addxmlname, soapserver, additionaldependinstance, dontuseStdActivity) {
	var i, j;
	var obj=pg.m_hashObjects[instname];
	if (!obj && (!addxmlname || !soapserver)) {
		if (DEBUG) 
			alert("EDispecher: Object does not exist : "+instname+" or You don't give to soapserver parameter to RegisterInstance for additional xml: "+addxmlname);
		return -1;
	}
	i=this.ObjectsCache.Push( soapserver ,obj.m_strClassName,instname,addxmlname) ;
	if ((!dontuseStdActivity || additionaldependinstance) && !addxmlname && !this.ActiveObjects[instname]) {
		this.ActiveObjects[instname]=new EActiveObject(i,instname);
		if (additionaldependinstance) this.ActiveObjects[instname].SetAdditionalDepend(additionaldependinstance); 
		this.ObjectsCache.SetObjInitialActivity(instname);
	}
	if (!(dontuseStdActivity > 1)) {
		for (j in this.ActiveObjects) 
			this.ActiveObjects[j].CheckObjDepend(i);
	} else this.ObjectsCache.Element(i).dna=dontuseStdActivity;
	return i;
} 

function ED_SetAdditionalDepend(index, additionaldependinstance, submission) {
	var instname;
	instname=this.ObjectsCache.Element(index).oinstance;
	this.ActiveObjects[instname].SetAdditionalDepend(additionaldependinstance, submission); 
}

function ED_MarkSuccessSubmision(index, submission, willclose) {
	var objc, obja;
	if (index<0 ) {
		if (DEBUG) 
			alert("EDispecher.CheckNeedUpdate: Invalid index!");
		return 0;
	}
	objc=this.ObjectsCache.Element(index);
	if (this.isCashedSubmision(submission)) {
		objc.ts=1;//trebe sys soap servera da bachka za vbydeshte
	}
	if (!objc.oaddxml) {
		obja=this.ActiveObjects[objc.oinstance];
		if (obja) obja.SubmissionSuccess(submission, willclose);
	}
}
function ED_RegisterEvent(index,hashElAttrs,request) {
	var objc, obja;
	if (index>-1 ) {
		objc=this.ObjectsCache.Element(index);
		if (!objc.oaddxml) {
			obja=this.ActiveObjects[objc.oinstance];
			if (obja) {
				var isBefore = hashElAttrs["before"];
				if(isBefore){
					if(!obja.beforeEvents) obja.beforeEvents={};
					obja.beforeEvents[hashElAttrs["catchsubmission"]]={ "targetinstance" :hashElAttrs["targetinstance"], "methodlocation" :hashElAttrs["methodlocation"], "methodname" : hashElAttrs["methodname"], "objectdataname": hashElAttrs["objectdataname"],"SS": pg.m_strGSoapServer + hashElAttrs["SS"], "submission" : hashElAttrs["submission"], "request" : null};
					if (request) {
						obja.beforeEvents[hashElAttrs["catchsubmission"]]["request"]=EWorkaround.newDomDocument();
						obja.beforeEvents[hashElAttrs["catchsubmission"]]["request"].async=false;
						obja.beforeEvents[hashElAttrs["catchsubmission"]]["request"].loadXML(request.xml);
					}
					return 1;
				} else {
					obja.Events[hashElAttrs["catchsubmission"]]={ "targetinstance" :hashElAttrs["targetinstance"], "methodlocation" :hashElAttrs["methodlocation"], "methodname" : hashElAttrs["methodname"], "objectdataname": hashElAttrs["objectdataname"],"SS": pg.m_strGSoapServer + hashElAttrs["SS"], "submission" : hashElAttrs["submission"], "request" : null};
					if (request) {
						obja.Events[hashElAttrs["catchsubmission"]]["request"]=EWorkaround.newDomDocument();
						obja.Events[hashElAttrs["catchsubmission"]]["request"].async=false;
						obja.Events[hashElAttrs["catchsubmission"]]["request"].loadXML(request.xml);
					}
					return 1;
				}
			}
		}
	}
	if (DEBUG) 
			alert("EDispecher.CheckNeedUpdate: Invalid index!");
		return 0;
}
