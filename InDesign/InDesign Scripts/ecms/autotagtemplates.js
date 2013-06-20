var st_templates=null;
var st_templatedetail=null;
var postprocobjs={};
var lTemplDets=null;

//~ var funct_file = File(app.filePath + '/Scripts/XML Rules/glue code.jsx');
//~ app.doScript(funct_file, ScriptLanguage.javascript);


function GetTemplates() {
	var res=null;
	st_templates= new ECMS_Page(mylib,"/resources/indesign_templates/");
	var l=st_templates.Execute();
	if (!l) {
		res=st_templates.GetObject(0);
	}
	return res;
}

function GetTemplateDetail(tid) {
	var res=null;
	st_templatedetail= new ECMS_Page(mylib,"/resources/indesign_templates/reldetails.php");
	var params={"template_id":""};
	params.template_id=tid;
	st_templatedetail.SetParams(params);
	var l=st_templatedetail.Execute();
	if (!l) {
		res=st_templatedetail.GetObject(1);
	}
	return res;
}
function fillTemplateCombobyType(lcombo,ltype, ltemplObj) {
	var res=0;
	if (!(ltemplObj=checkltemplObj(ltemplObj))) return 1;
	var attrvalue=getAttributeValue(xmlRoot, ltype ==1 ? "ecms_st_templid":"ecms_ts_templid");
	lcombo.removeAll();
	lcombo.add("item", "---");
	lcombo.items[0].selected=true;
	var i=0;
	for(l=0;l<ltemplObj.mcnt;l++)
		if (ltemplObj.mdata[l]["type"]==ltype) {
			lcombo.add("item", ltemplObj.mdata[l]["name"]);
			i++;
			if (attrvalue==ltemplObj.mdata[l]["id"]) lcombo.items[i].selected=true;
		}
	return res;
}
function checkltemplObj(ltemplObj) {
	if (ltemplObj) return ltemplObj;
	if (st_templates) return st_templates.GetObject(0);
	else {
		alert("Error! You must load templates first!");
		return null;
	}
}

function getSelectedTemplateID(comboindex, ltype, ltemplObj) {
	if (!(ltemplObj=checkltemplObj(ltemplObj))) return -1;
	if (!comboindex) return -2;
	var i=1;
	for(l=0;l<ltemplObj.mcnt;l++)
		if (ltemplObj.mdata[l]["type"]==ltype) {
			if (i==comboindex) return ltemplObj.mdata[l]["id"];
			i++;
		}
	alert("Error! Invalid template index!");
	return -1;
}
/*
function setTemplateID(pid, ptype) {
	addAttribute(xmlRoot, ptype ==1 ? "ecms_st_templid":"ecms_ts_templid",pid);
	lTemplDets=null;
	lTemplDets=GetTemplateDetail(pid);
	if (lTemplDets) {
		var l;
		//~ postprocobjs={};
		//~ var postprocobjscnt=0;
		for(l=0;l<lTemplDets.mcnt;l++) {
			var lxmltag=addcheckXMLTag(lTemplDets.mdata[l]["xmlnode"]);
			var lstyle=addcheckStyles(lTemplDets.mdata[l]["style"], lTemplDets.mdata[l]["type"]);
			addcheckXmlMap(lxmltag,lstyle, ptype);
			if ((ptype ==1) && (lTemplDets.mdata[l]["parent_path"])) {
				var tagstr=lTemplDets.mdata[l]["parent_path"];
				var tagarr=tagstr.split("/");
				var parenttagarr=[];
				for(var i=0;i<tagarr.length; i++) {
					if (tagarr[i].length) {
						if (tagarr[i] && (tagarr[i]!=".")) {
							addcheckXMLTag(tagarr[i]);
							parenttagarr.push(tagarr[i]);
						}
					}
				}
				if (parenttagarr.length) {
					//~ if (!postprocobjs[lTemplDets.mdata[l]["xmlnode"]]) {
						//~ postprocobjs[lTemplDets.mdata[l]["xmlnode"]]={};
						//~ postprocobjs[lTemplDets.mdata[l]["xmlnode"]][l]=parenttagarr;
						//~ postprocobjscnt++;
					//~ }
					lTemplDets.mdata[l]["parenttagarr"]=parenttagarr;
				}
			}
		}
		if (ptype ==1) {
			//~ myDoc.mapStylesToXMLTags();
			//~ if (postprocobjscnt) {
				//~ var xmlnd= myDoc.xmlElements.item(0);
				//~ CheckParents(xmlnd);
			//~ }
			//TUKA
			
		} else {
			myDoc.mapXMLTagsToStyles();
		}
	} else alert("Error! Unable to get template details!");
}
*/
function setTemplateID(pid, ptype) {
	addAttribute(xmlRoot, ptype ==1 ? "ecms_st_templid":"ecms_ts_templid",pid);
	lTemplDets=null;
	lTemplDets=GetTemplateDetail(pid);
	if (lTemplDets) {
		var l;
		//~ postprocobjs={};
		//~ var postprocobjscnt=0;
		for(l=0;l<lTemplDets.mcnt;l++) {
			var lxmltag=addcheckXMLTag(lTemplDets.mdata[l]["xmlnode"]);
			var lstyle=addcheckStyles(lTemplDets.mdata[l]["style"], lTemplDets.mdata[l]["type"]);
			if (ptype !=1) addcheckXmlMap(lxmltag,lstyle, ptype);
			//~ if ((ptype ==1) && (lTemplDets.mdata[l]["parent_path"])) {
				//~ var tagstr=lTemplDets.mdata[l]["parent_path"];
				//~ var tagarr=tagstr.split("/");
				//~ var parenttagarr=[];
				//~ for(var i=0;i<tagarr.length; i++) {
					//~ if (tagarr[i].length) {
						//~ parenttagarr.push(tagarr[i]);
						//~ if (tagarr[i] && (tagarr[i]!=".") addcheckXMLTag(tagarr[i]);
					//~ }
				//~ }
				//~ if (parenttagarr.length) {
					//~ if (!postprocobjs[lTemplDets.mdata[l]["xmlnode"]]) {
						//~ postprocobjs[lTemplDets.mdata[l]["xmlnode"]]={};
						//~ postprocobjs[lTemplDets.mdata[l]["xmlnode"]][l]=parenttagarr;
						//~ postprocobjscnt++;
					//~ }
				//~ }
			//~ }
		}
		if (ptype ==1) {
			//~ myDoc.mapStylesToXMLTags();
			//~ if (postprocobjscnt) {
				//~ var xmlnd= myDoc.xmlElements.item(0);
				//~ CheckParents(xmlnd);
			//~ }
			var gxmlnd= myDoc.xmlElements.item(0);
			var articlecnt=0;
			for (var i=0;i<gxmlnd.xmlElements.count(); i++) {
				var litem=gxmlnd.xmlElements.item(i);
				if (litem.markupTag.name=="article") {
                      //  var lfakenode1=litem.xmlElements.add(litem.markupTag);
                       // lfakenode1.contents="\r";
                       // lfakenode1.untag();
					//~ var fs=new Date();
					articlecnt++;
					var lEST=new E_ParseStory(litem, lTemplDets);
					
					lEST.GenMap(); 
					//~ //lEST.StylestoTags();
					lEST.StylestoTagsNew();
					lEST.initFigsandGraphsNode(articlecnt);
					lEST.searchForFigsandGraphs();
				}	
			}
			//~ var lEfigs=new E_ParseStory(litem, lTemplDets);
					
			//~ lEfigs.GenMap(); 
			//~ //lEST.StylestoTags();
			//~ //lEST.StylestoTagsNew();
			//~ lEfigs.initFigsandGraphsNode()
			//~ lEfigs.searchForFigsandGraphs();
			
		} else {
			myDoc.mapXMLTagsToStyles();
		}
	} else alert("Error! Unable to get template details!");
}

function addcheckXmlMap(pxmltag,pstyle, ptype) {
	var lmain, ldep, lmainstr, ldepstr, lcoll,tc;
	if (ptype ==1) {
		lmain=pstyle; 
		ldep=pxmltag;
		lmainstr="mappedStyle";
		ldepstr="markupTag";
		lcoll=myDoc.xmlExportMaps;
	} else {
		lmain=pxmltag; 
		ldep=pstyle;
		lmainstr="markupTag";
		ldepstr="mappedStyle";
		lcoll=myDoc.xmlImportMaps;
	}
	tc=lcoll.count();
	for(var i=0;i<tc;i++)
		if (lmain==lcoll.item(i)[lmainstr]) {
			if (lcoll.item(i)[ldepstr]!=ldep) lcoll.item(i)[ldepstr]=ldep; 
			return ;
		}
	lcoll.add(lmain, ldep);
}
//~ myDocument.xmlImportMaps.add(myDocument.xmlTags.item("heading_1"), myDocument.paragraphStyles.item("heading 1"));
//~ myDocument.xmlExportMaps.add(myDocument.paragraphStyles.item("heading 1"), myDocument.xmlTags.item("heading_1"));
//~ mappedStyle  	markupTag

//{"0":"2","id":"2","1":"title","name":"title","2":"title","style":"title"}
//addcheckStyles(pname , ptype)
//addcheckXMLTag(pname)

//~ function CheckParents(nodeName) {
	//~ this.name="CP_"+nodeName;
	//~ this.xpath="//"+nodeName;
	//~ this.apply=function(myElement, myRuleProcessor){
		//~ __skipChildren(myRuleProcessor);
		//~ var pinx=0;
		//~ var parr=postprocobjs[nodeName][pinx];
		//~ var lel=myElement;
		//~ for(var i=parr.length-1;i>=0;i--) {
			//~ lel=CheckSingleParent(lel,parr[i]);
			//~ if (!lel) break;
		//~ }
	//~ }
//~ }
function CheckParents(xmlNode) {
	var nodeName=xmlNode.markupTag.name;
	var i;
	var ischanged=xmlNode;
	if (postprocobjs[nodeName]) {
		var pinx=GetPostProcessIdx(xmlNode, nodeName); //tuk da sechekne spriamo style-a koi da se polzva i dali da se polzva vyobshte
		if (pinx>=0) {
			var parr=postprocobjs[nodeName][pinx];
			var lel=xmlNode;
			//~ ischanged=xmlNode; 
			for(i=parr.length-1;i>=0;i--) {
				var oldpar=lel.parent;
				lel=CheckSingleParent(lel,parr[i]);
				if (lel!=oldpar) ischanged=lel;
			}
		} else alert("Skip: "+nodeName);
	} 
	for(i=0;i<ischanged.xmlElements.count();i++)	CheckParents(ischanged.xmlElements.item(i));
}
function CheckSingleParent(lel,pName) {
	var lpel=lel.parent;
	if (lpel.markupTag.name==pName) return lpel;
	var lnewptag=addcheckXMLTag(pName);
	var lnewpnode=lpel.xmlElements.add(lnewptag);
	lnewpnode=lnewpnode.move(LocationOptions.before,lel);
    //~ var lfakenode=lnewpnode.xmlElements.add(lnewptag);
    var lfakenode=lnewpnode.xmlElements.add(lel.markupTag);
	lel.move(LocationOptions.after,lfakenode);
    lfakenode.untag();
	return lnewpnode;
}

function GetPostProcessIdx(xmlNode, nodeName) {
	if (xmlNode.characters.count()) {
		var lchar=xmlNode.characters[0];
		var lcstyle=lchar.appliedCharacterStyle.name;
		var lpstyle=lchar.appliedParagraphStyle.name;
		for(var i in postprocobjs[nodeName]) {
			if ((lTemplDets.mdata[i]["type"]==1 ? lcstyle : lpstyle) == lTemplDets.mdata[i]["style"]) return i;
		}
	}
	return -1;
}