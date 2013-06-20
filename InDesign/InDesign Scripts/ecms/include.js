/*
   File: include.js

   Това е файл, който се инклудва от почти всички файлове.
   Съдържа дефиниции на функции
 */

var gDefaultPage = 1000000;// Номера на страницата по подразбиране на екстра
// обектите за фигури/таблици
var gDefaultCoordinate = 1000000;// Номера на координатите по подразбиране на
// екстра обектите за фигури/таблици

// Това забързва скрипта тъй като индизайн не преначертава нищо до края на
// изпълнението
app.scriptPreferences.enableRedraw = false;

/*
 * Function: getConfig
 * 
 * Прочита конфигурационния файл.
 * 
 * Returns:
 * 
 * XML обект.
 */
function getConfig() {
	var config_file = new File(app.filePath + '/ecms/config.xml');
	config_file.open("r", undefined, undefined);
	return new XML(config_file.read());
}

/*
 * Function: loadLib
 * 
 * Зарежда dll библиотеката и и подава параметрите с които да се идентифицира на
 * сървъра.
 * 
 * Returns:
 * 
 * Обект през който могат да се викат функции от dll-а.
 */
var cxml = null;
function loadLib() {
	// ExternalObject.log=true;
	// ExternalObject.search("lib:ecms_iwrapper.dll");
	var mylib = new ExternalObject("lib:ecms_iwrapper.dll"); // load the
	// library
	// ~ mylib.setDebugLevel(1);
	if (!cxml)
		cxml = getConfig();
	if (mylib) {
		var i = mylib.ecmsInit(cxml.xpath("/config/login/@host").toString(),
				cxml.xpath("/config/login/@user").toString(), cxml.xpath(
						"/config/login/@pass").toString(), cxml.xpath(
						"/config/login/@url").toString());
		if (i)
			mylib = null;
	}
	return mylib;
}

function ErrorReport(lib, errmsg) {
	alert(errmsg);
}

function LoadEPMTTags() {
	var myDoc = app.activeDocument;
	myDoc.loadXMLTags(File(app.filePath + '/ecms/epmttags.xml'));
}

function GetComboSelIndex(pcombo) {
	for ( var i = 0; i < pcombo.items.length; i++) {
		if (pcombo.items[i].selected)
			return i;
	}
	return null;
}

function addAttribute(el, a, v) {
	try {
		el.xmlAttributes.itemByName(a).remove();
	} catch (err) {
		// ...
	}
	try {
		el.xmlAttributes.add(a, v + '');
	} catch (err) {
		// alert(a+"--"+v);
		return 1;
	}
	return 0;
}

function getAttributeValue(el, a) {
	var res = null;
	try {
		res = el.xmlAttributes.itemByName(a).value;
	} catch (err) {
		res = null;
	}
	return res;
}

function CheckUndefined(pval) {
	// ~ if (!pval || (typeof(pval) =="undefined")) return null; else return
	// pval;
	if (typeof (pval) == "undefined")
		return null;
	else
		return pval;
}

/*
 * Function: getPasteBoard
 * 
 * Взима границите на полето за поставяне на съдържание (pasteboard).
 * 
 * Parameters:
 * 
 * myDoc - индизайн Document обект. placeSide - от коя страна ще поставяме (може
 * да е left или right).
 * 
 * Returns:
 * 
 * Масив с границите на полето (горен ляв и долен десен ъгъл -
 * pasteboard[x1,y1,x2,y2]).
 */
function getPasteBoard(myDoc, placeSide) {
	var pasteboard = [ 0, 0, 0, 0 ];
	var spr = myDoc.spreads;
	if (spr.count()) {
		var pages = spr.item(0).pages;
		if (pages.count() != myDoc.pages.count()) {
			pages = myDoc.pages;
		}
		if (pages.count()) {
			var fpg = pages.item(0).bounds;
			var lpg = pages.item(-1).bounds;
			var diff = (lpg[3] - fpg[1]);

			if (placeSide == 'left') {
				if (pages.item(0).side != PageSideOptions.LEFT_HAND) {
					diff += diff;
				}
				pasteboard = [ 0, (fpg[1] - diff), fpg[2], fpg[1] ];
			} else {
				if (pages.item(0).side == PageSideOptions.LEFT_HAND) {
					diff += diff;
				}
				pasteboard = [ 0, lpg[3], lpg[2], (lpg[3] + diff) ];
			}
		}
	}
	return pasteboard;
}

function selectSingleNodeinStructure(ppath, pnode, pdoc) {
	if (!ppath)
		return null;
	if (!pnode || (ppath[0] == "/")) {
		if (!pdoc) {
			try {
				pdoc = app.activeDocument;
			} catch (err) {
				alert("There isn't Active Document");
				return null;
			}
		}
		pnode = pdoc.xmlElements.item(0);
		if (ppath[0] == "/")
			ppath = ppath.substr(1);
	}
	var lpatharr = ppath.split("/");
	return selectSingleNodeInternal(pnode, lpatharr, 0);
}
function getStructNodeClearContents(pnode) {
	var s = pnode.contents;
	var res = '';
	for ( var i = 0; i < s.length; i++)
		if (s.charCodeAt(i) != 65279)
			res += s[i];
	return res;
}
function selectSingleNodeInternal(pnode, ppatharr, curidx) {
	var res = null;
	if (!ppatharr[curidx]) {
		alert("Error! Expressions like // are not supported yet!");
		return null;
	}
	if ((ppatharr[curidx] == ".") || (ppatharr[curidx] == pnode.markupTag.name)) {
		if (curidx + 1 == ppatharr.length)
			return pnode;
		for ( var i = 0; i < pnode.xmlElements.count(); i++) {
			var litem = pnode.xmlElements.item(i);
			if (res = selectSingleNodeInternal(litem, ppatharr, curidx + 1))
				break;
		}
	}
	return res;
}
function addcheckXMLTag(pname) {
	var tc = myDoc.xmlTags.count();
	for ( var i = 0; i < tc; i++)
		if (myDoc.xmlTags.item(i).name == pname)
			return myDoc.xmlTags.item(i);
	return myDoc.xmlTags.add(pname);
}

function addcheckStyles(pname, ptype) {
	// ~ var myHeading1Style = myDocument.paragraphStyles.add();

	// ~ myHeading1Style.name = "heading 1";
	var lStyles = (ptype == 1 ? myDoc.characterStyles : myDoc.paragraphStyles);
	var tc = lStyles.count();
	for ( var i = 0; i < tc; i++)
		if (lStyles.item(i).name == pname)
			return lStyles.item(i);
	var resStyle = lStyles.add();
	resStyle.name = pname;
	return resStyle;
}

function ECMS_Page(srclib, url) {
	this.CheckAlert = EP_CheckAlert;
	this.ResetError = EP_ResetError;
	this.SetURL = EP_SetURL;
	this.Execute = EP_Execute;
	this.ExecuteRawData = EP_ExecuteRawData;
	this.GetObject = EP_GetObject;
	this.SetParams = EP_SetParams;
	this.m_lib = srclib ? srclib : null; // loadLib() ;
	this.m_url = url;
	this.m_flags = 1;
	this.m_doalerts = 1;
	this.m_multipart = {
		"fieldname" : "xmlfile",
		"filename" : null,
		"realFile" : null,
		"ctype" : "text/xml"
	};
	// char* pFieldname, char* pFilename, char* realFile, char *ctype
	this.m_res = null;
	this.ResetError();
	if (!this.m_lib) {
		this.m_errdesc = "Unable to load library!";
		this.m_err = -1;
	}
}

function EP_GetObject(index) {
	var res = null;
	if (this.m_res && (index < this.m_res.rescnt))
		res = this.m_res.res[index];
	else if (this.m_doalerts)
		alert("Error! Invalid index: " + index + "!");
	return res;
}
function EP_CheckAlert() {
	if (this.m_err && this.m_doalerts) {
		alert("Error! " + this.m_errdesc);
	}
	return this.m_err;
}

function EP_ResetError() {
	this.m_err = 0;
	this.m_errdesc = '';
}

function EP_SetURL(url) {
	if (!this.m_err) {
		if (!url) {
			this.m_err = -1;
			this.m_errdesc = "Url can't be empty!";
		} else {
			this.m_url = url;
			this.m_flags = 1;
		}
	}
	return this.CheckAlert();
}

function EP_SetParams(paramObj, postindex) {
	if (!this.m_err) {
		if (!this.m_url) {
			this.m_err = -1;
			this.m_errdesc = "Url can't be empty!";
		} else {
			var i = 0;
			if (-1 != (i = this.m_url.indexOf("?")))
				this.m_url = this.m_url.substr(0, i);
			var k = "?";
			i = 0;
			for ( var pname in paramObj) {
				this.m_url += k + pname + "="
						+ encodeURIComponent(paramObj[pname]);
				i++;
				if (i == postindex)
					k = "?";
				else
					k = "&";
			}
		}
	}
	return this.CheckAlert();
}

function EP_Execute(multipart) {
	var ress = null;
	if (multipart) {
		// ~ alert(this.m_multipart.realFile);
		ress = this.m_lib.ecmsMultipartFormJSON(this.m_url, this.m_flags,
				this.m_multipart.fieldname, this.m_multipart.filename,
				this.m_multipart.realFile, this.m_multipart.ctype);
	} else
		ress = this.m_lib.ecmsGetDataJSON(this.m_url, this.m_flags);
	if (!ress) {
		this.m_err = -1;
		this.m_errdesc = "Unable to get result from executing " + this.m_url
				+ "!";
		this.m_res = null;
	} else {
		var res = null;
		var evalstr = "res=" + ress;
		eval(evalstr);
		if (res.err) {
			alert("Error: " + res.err + " - " + res.errdesc);
			this.m_err = res.err;
			this.m_errdesc = res.errdesc;
			this.m_res = null;
		} else {
			this.m_res = res;
			/*
			 * for(var i=0;i<this.m_res.rescnt; i++) for(var j in
			 * this.m_res.res[i]) alert(j+": "+this.m_res.res[i][j]);
			 */
		}
	}
	return this.CheckAlert();
}
function EP_ExecuteRawData() {
	var ress = this.m_lib.ecmsGetDataInternal(this.m_url, this.m_flags);
	if (!ress) {
		this.m_err = -1;
		this.m_errdesc = "Unable to get result from executing " + this.m_url
				+ "!";
		this.m_res = null;
	} else {
		this.m_res = ress;
	}
	return this.CheckAlert();
}

// E_ParseStory start

function E_ParseStory(xmlnd, TemplDets) {
	this.mxmlel = xmlnd;
	this.mTemplDets = TemplDets;
	this.mstylemap = {
		"pars" : [],
		"chars" : [],
		"map" : []
	};
	this.GenStylesArrays = ESM_GenStylesArrays;
	this.GenStylesArraysNew = ESM_GenStylesArraysNew;
	this.GenMap = ESM_GenMap;
	this.GenMapFromArrays = ESM_GenMapFromArrays;
	this.InitMap = ESM_InitMap;
	this.CheckStyleName = ESM_CheckStyleName;
	this.StylestoTags = ESM_StylestoTags;
	this.StylestoTagsNew = ESM_StylestoTagsNew;
	this.doStylestoTags = ESM_doStylestoTags;
	this.doStylestoTagsNew = ESM_doStylestoTagsNew;
	this.doMarkup = ESM_doMarkup;
	this.doMarkupNew = ESM_doMarkupNew;
	this.CheckParent = ESM_CheckParent;
	this.newNodeAdded = ESM_newNodeAdded;
	this.removeAllTags = ESM_removeAllTags;
	this.removeAllComments = ESM_removeAllComments;
	this.removeAllCommentsReqursive = ESM_removeAllCommentsReqursive;
	this.addBIUTags = ESM_addBIUTags;
	this.addBIUTagsAFT = ESM_addBIUTagsAFT;
	this.addBIUTagsInternal = ESM_addBIUTagsInternal;
	this.addPageBreakComments = ESM_addPageBreakComments;
	this.addPBComent = ESM_addPBComent;
	this.getMarkerforPage = ESM_getMarkerforPage;
	this.removeBIUTags = ESM_removeBIUTags;
	this.removeBIUTagsAFT = ESM_removeBIUTagsAFT;
	this.PrepareTemplates = ESM_PrepareTemplates;
	this.PrepareMaps = ESM_PrepareMaps;
	this.GetDeltaAfterAdd = ESM_GetDeltaAfterAdd;
	this.GenCurPath = ESM_GenCurPath;
	this.getNextforStylestoTags = ESM_getNextforStylestoTags;
	this.ChangePinParsArray = ESM_ChangePinParsArray;
	this.removeAllTagsReqursive = ESM_removeAllTagsReqursive;
	this.RealMarkup = ESM_RealMarkup;
	this.searchForFigsandGraphs = ESM_searchForFigsandGraphs;
	this.initFigsandGraphsNode = ESM_initFigsandGraphsNode;
	this.fillExtraObjects = ESM_fillExtraObjects;
	this.figsidxarr = [];
	this.tablesidxarr = [];
	if (this.mTemplDets)
		this.PrepareTemplates();
}
function ESM_InitMap() {
	this.mstylemap = {
		"pars" : [],
		"chars" : [],
		"map" : []
	};
}

function ESM_PrepareTemplates() {
	for ( var i = 0; i < this.mTemplDets.mcnt; i++) {
		var lt = this.mTemplDets.mdata[i];
		lt["change_before"] = parseInt(lt["change_before"]);
		lt["change_after"] = parseInt(lt["change_after"]);
		lt["new_parent"] = parseInt(lt["new_parent"]);
		lt["special"] = parseInt(lt["special"]);
		if (lt["special"]) {
			var arr = (lt["special"] == 2) ? this.figsidxarr
					: this.tablesidxarr;
			arr.push(i);
		}
		if (!lt["parent_path"] || (lt["parent_path"] == "."))
			lt["full_path"] = "./" + lt["xmlnode"];
		else
			lt["full_path"] = lt["parent_path"] + "/" + lt["xmlnode"];
		if (lt["parent_path"]) {
			var tagstr = lt["parent_path"];
			var tagarr = tagstr.split("/");
			var parenttagarr = [];
			for ( var l = 0; l < tagarr.length; l++) {
				if (tagarr[l] && tagarr[l].length && (tagarr[l] != ".")) {
					parenttagarr.push(tagarr[l]);
				}
			}
			if (parenttagarr.length)
				lt["parenttagarr"] = parenttagarr;
		}
	}
}

function ESM_GenMap() {
	this.GenStylesArraysNew();
	this.GenMapFromArrays();
}
function ESM_addBIUTagsInternal(pxmlel) {
	var lcharcnt = pxmlel.characters.count();
	var dotags = 0;
	var llast = [ 0, 0, 0, 0, 0 ]; // bold , italic, underline, superscript,
	// subscript
	var lTagNames = [ 'bold', 'italic', 'underline', 'sup', 'sub' ];
	var lcurtag = null;
	var taglist = [];
	var lcurrent;
	var ltaglen = 0;
	var loldxmlel = this.mxmlel;
	this.mxmlel = pxmlel;
	for ( var i = 0; i < lcharcnt; i++) {
		var lch = pxmlel.characters[i];
		var lnewtag = lch.associatedXMLElements[0];
		var lchistag = false;
		// 65279 е специалния символ за начало/край на xml element node
		try {
			lchistag = (lch.contents.charCodeAt(0) == 65279);
		} catch (e) {
			lchistag = false;
		}
		if (lchistag)
			try {
				if ((!i || (pxmlel.characters[i - 1].contents.charCodeAt(0) == 65279)))
					continue;
			} catch (e) {
			}
		var lSuperscriptCode = 1936749411;
		var lSubscriptCode = 1935831907;
		// 1936749411 - superscript; 1935831907 - subscript;
		lcurrent = [
				(lch.fontStyle == "Bold" || lch.fontStyle == "Bold Italic"),
				(lch.fontStyle == "Italic" || lch.fontStyle == "Bold Italic"),
				lch.underline, lch.position == lSuperscriptCode,
				lch.position == lSubscriptCode ];

		dotags = 0;
		// ~ if (lnewtag!=lcurtag) {
		if (lchistag) {
			dotags = 1;
		} else {
			// ~ ltaglen++;
			if ((taglist.length) && !lcurrent[taglist[0].type])
				dotags = 1;
		}
		if (dotags) {
			// tuk
			// ~ if (ltaglen>0 || (lnewtag==lcurtag)) {
			var ltagcnt = taglist.length;
			var lcorrect = 0;
			var lpartag = lcurtag;
			var lParentTags = [];// Тук ще държим списък със всички парент
			// тагове
			var lMaxLength = lcharcnt + 2 * ltagcnt;
			lParentTags.push({
				"start" : 0,
				"end" : lMaxLength + 1,
				"node" : lcurtag
			});
			// Правим корекция на индексите - понеже при създаване на таг се
			// добавя 1 символ преди и 1 символ след него
			for ( var j = 0; j < ltagcnt; j++) {
				// Понеже списъка е хронологичен - всеки следващ таг започва
				// след началото на предходния и така при тагването на
				// предходния се измества поне със символа за старт на
				// предходния
				taglist[j]["start_after_cor"] += lcorrect;

				if (taglist[j]["end"] == -1) {
					taglist[j]["end"] = i - 1;
					taglist[j]["end_after_cor"] = i - 1 + lcorrect;
				} else {
					taglist[j]["end_after_cor"] += lcorrect; // tuka correct
																// +1 ??? start
				}
				// i end da se vidi
				// tochno!!!
				for ( var k = j + 1; k < ltagcnt; ++k) {
					// Ако следващия таг започва след края на предходния -
					// добавяме и символа за край на текущия
					if (taglist[k]["start"] > (taglist[j]["end"])) {
						taglist[k]["start_after_cor"]++;
					}
					// Ако следващия таг завършва след края на предходния (т.е.
					// не е вложен в него, а е последователно след него в
					// текста) - добавяме и символа за край на текущия таг
					if (taglist[k]["end"] > (taglist[j]["end"])) {
						taglist[k]["end_after_cor"]++;
					}
				}
				lpartag = null;
				// Търсим истинския parent на новия възел
				for ( var k = lParentTags.length - 1; k >= 0; k--) {
					if (taglist[j]["start"] >= lParentTags[k]["start"]
							&& taglist[j]["end"] <= lParentTags[k]["end"]) {
						lpartag = lParentTags[k]["node"];
						break;
					}
				}
				if (!lpartag)
					lpartag = lcurtag;
				var lTagName = lTagNames[taglist[j]["type"]];
				var lNewTag = this.doMarkup(lpartag,
						taglist[j]["start_after_cor"],
						taglist[j]["end_after_cor"], lTagName, 1);
				lParentTags.push({
					"start" : taglist[j]["start"],
					"end" : taglist[j]["end"],
					"node" : lNewTag
				});

				lcorrect++;
			}
			lcharcnt += lcorrect * 2;
			i += lcorrect * 2;
			taglist = [];
			// ~ } else {
			// ~ llast=[0, 0, 0];
			// ~ lcurtag=lnewtag;
			// ~ continue;
			// ~ }
			// ~ if (lnewtag!=lcurtag) ltaglen=0;
			llast = [ 0, 0, 0, 0, 0 ];
		}
		lcurtag = lnewtag;
		if (lchistag)
			continue;
		for ( var l = 0; l < lcurrent.length; l++) {
			if (llast[l] && !lcurrent[l]) {
				var lstate = 0;
				var ltagcnt = taglist.length;

				for ( var j = 0; j < ltagcnt; j++) {
					// tuka j 1???
					if (!lstate) {// Тук затваряме първият отворен таг от този
						// тип - нека напр. го кръстим Болд1
						if ((taglist[j]["end"] == -1)
								&& (taglist[j]["type"] == l)) {
							taglist[j]["end"] = i - 1;
							taglist[j]["end_after_cor"] = i - 1;
							lstate = 1;
						}
					} else {// Затваряме всички следващи тагове - понеже те
						// трябва да са вложени в Болд1 (започват в него =>
						// за да е валиден xml-a трябва и да свършват в него
						if (taglist[j]["end"] == -1) {
							taglist[j]["end"] = i - 1;
							taglist[j]["end_after_cor"] = i - 1;
							llast[lcurrent[taglist[j]["type"]]] = 0;
						}
					}
				}
			} else if (!llast[l] && lcurrent[l]) {
				taglist.push({
					"start" : i,
					"start_after_cor" : i,
					"end" : -1,
					"end_after_cor" : -1,
					"type" : l
				});
			}
			llast[l] = lcurrent[l];
		}
	}
	var ltagcnt = taglist.length;
	var lcorrect = 0;
	var lpartag = lcurtag;
	var lParentTags = [];// Тук ще държим списък със всички парент тагове
	var lMaxLength = lcharcnt + 2 * ltagcnt;
	lParentTags.push({
		"start" : 0,
		"end" : lMaxLength + 1,
		"node" : lcurtag
	});
	for ( var j = 0; j < ltagcnt; j++) {
		taglist[j]["start_after_cor"] += lcorrect;

		if (taglist[j]["end"] == -1) {
			taglist[j]["end"] = i - 1;
			taglist[j]["end_after_cor"] = i - 1 + lcorrect;
		} else {
			taglist[j]["end_after_cor"] += lcorrect; // tuka correct +1 ???
														// start
		}
		// i end da se vidi
		// tochno!!!
		for ( var k = j + 1; k < ltagcnt; ++k) {
			// Ако следващия таг започва след края на предходния - добавяме и
			// символа за край на текущия
			if (taglist[k]["start"] > (taglist[j]["end"])) {
				taglist[k]["start_after_cor"]++;
			}
			// Ако следващия таг завършва след края на предходния (т.е. не е
			// вложен в него, а е последователно след него в текста) - добавяме
			// и символа за край на текущия таг
			if (taglist[k]["end"] > (taglist[j]["end"])) {
				taglist[k]["end_after_cor"]++;
			}
		}
		lpartag = null;
		for ( var k = lParentTags.length - 1; k >= 0; k--) {// Търсим истинския
			// parent на новия
			// възел
			if (taglist[j]["start"] >= lParentTags[k]["start"]
					&& taglist[j]["end"] <= lParentTags[k]["end"]) {
				lpartag = lParentTags[k]["node"];
				break;
			}
		}
		if (!lpartag)
			lpartag = lcurtag;
		var lTagName = lTagNames[taglist[j]["type"]];
		var lNewTag = this.doMarkup(lpartag, taglist[j]["start"],
				taglist[j]["end"], lTagName, 1);
		lParentTags.push({
			"start" : taglist[j]["start"],
			"end" : taglist[j]["end"],
			"node" : lNewTag
		});
		lcorrect++;
	}
	this.mxmlel = loldxmlel;
}
function ESM_addBIUTagsAFT(pxmlel) {
	for ( var i = 0; i < pxmlel.xmlElements.count(); i++) {
		var litem = pxmlel.xmlElements.item(i);
		this.addBIUTagsInternal(litem);
		if (litem.markupTag.name == "table-wrap") {
			for ( var i1 = 0; i1 < litem.xmlElements.count(); i1++) {
				if (litem.xmlElements.item(i1).markupTag.name == "table") {
					var ltablenode = litem.xmlElements.item(i1);
					for ( var j = 0; j < ltablenode.xmlElements.count(); j++) {
						this.addBIUTagsInternal(ltablenode.xmlElements.item(j));
					}
					break;
				}
			}
		}
		// pri tablica mozhe bi oshte neshto
		// ~ if ((litem.markupTag.name=="article_figs_and_tables") && (
		// getAttributeValue(litem, 'article_pos')==lpos)) {
		// ~ }
	}
}

function ESM_removeBIUTagsAFT(pxmlel) {
	// ~ return ;
	for ( var i = 0; i < pxmlel.xmlElements.count(); i++) {
		var litem = pxmlel.xmlElements.item(i);
		this.removeBIUTags(litem);
		if (litem.markupTag.name == "table-wrap") {
			for ( var i1 = 0; i1 < litem.xmlElements.count(); i1++) {
				if (litem.xmlElements.item(i1).markupTag.name == "table") {
					var ltablenode = litem.xmlElements.item(i1);
					for ( var j = 0; j < ltablenode.xmlElements.count(); j++) {
						this.removeBIUTags(ltablenode.xmlElements.item(j));
					}
					break;
				}
			}
		}
	}
}
function ESM_addBIUTags() {
	this.addBIUTagsInternal(this.mxmlel);
	/*
	 * var lcharcnt=this.mxmlel.characters.count(); var dotags=0; var llast=[0,
	 * 0, 0]; //bold , italic, underline var lcurtag=null; var taglist=[]; var
	 * lcurrent; var ltaglen=0; for(var i=0;i<lcharcnt;i++) { var
	 * lch=this.mxmlel.characters[i]; var lnewtag=lch.associatedXMLElements[0];
	 * var lchistag=false; try { lchistag=(lch.contents.charCodeAt(0)== 65279); }
	 * catch (e) { lchistag=false; } if (lchistag) try { if ((!i ||
	 * (this.mxmlel.characters[i-1].contents.charCodeAt(0)== 65279))) continue; }
	 * catch (e) { } lcurrent=[(lch.fontStyle=="Bold"),
	 * (lch.fontStyle=="Italic"), lch.underline]; dotags=0; //~ if
	 * (lnewtag!=lcurtag) { if (lchistag) { dotags=1; } else { //~ ltaglen++; if
	 * ((taglist.length) && !lcurrent[taglist[0].type]) dotags=1; } if (dotags) {
	 * //tuk //~ if (ltaglen>0 || (lnewtag==lcurtag)) { var
	 * ltagcnt=taglist.length; var lcorrect=0; var lpartag=lcurtag; for(var j
	 * =0;j<ltagcnt; j++) { if (taglist[j]["end"]==-1)
	 * taglist[j]["end"]=i-1+lcorrect; else taglist[j]["end"]+=lcorrect;
	 * lpartag=this.doMarkup(lpartag,taglist[j]["start"],taglist[j]["end"], (
	 * !taglist[j]["type"] ? "bold" : (taglist[j]["type"]==1 ? "italic" :
	 * "underline")),1); lcorrect++; } lcharcnt+=lcorrect*2; i+=lcorrect*2;
	 * taglist=[]; //~ } else { //~ llast=[0, 0, 0]; //~ lcurtag=lnewtag; //~
	 * continue; //~ } //~ if (lnewtag!=lcurtag) ltaglen=0; llast=[0, 0, 0]; }
	 * lcurtag=lnewtag; if (lchistag) continue; for(var l=0;l<3;l++) { if
	 * (llast[l] && !lcurrent[l]) { var lstate=0, lstart=0; var
	 * ltagcnt=taglist.length; for(var j =1;j<ltagcnt; j++) if (!lstate) { if
	 * ((taglist[j]["end"]==-1) && (taglist[j]["type"]==l)) {
	 * taglist[j]["end"]=i-1; lstate=1; lstart=taglist[j]["start"]; } } else {
	 * if (taglist[j]["end"]==-1) { taglist[j]["end"]=i-1;
	 * llast[lcurrent[taglist[j]["type"]]]=0; } } } else if (!llast[l] &&
	 * lcurrent[l]) { taglist.push({"start": i, "end": -1, "type": l}); }
	 * llast[l]=lcurrent[l]; } } var ltagcnt=taglist.length; var lcorrect=0; var
	 * lpartag=lcurtag; for(var j =0;j<ltagcnt; j++) { if
	 * (taglist[j]["end"]==-1) taglist[j]["end"]=i-1+lcorrect; else
	 * taglist[j]["end"]+=lcorrect;
	 * lpartag=this.doMarkup(lpartag,taglist[j]["start"],taglist[j]["end"],
	 * !taglist[j]["type"] ? "bold" : taglist[j]["type"]==1 ? "italic" :
	 * "underline",1); lcorrect++; }
	 */
	this.addPageBreakComments();
}
function ESM_addPageBreakComments() {
	this.m_pstory = this.mxmlel.parentStory;
	var loldmarker = null, lnewmarker = null, lnumpages = 0;
	for ( var i = 0; i < myDoc.spreads.count(); i++) {
		var ls = myDoc.spreads[i];
		for ( var j = 0; j < ls.pages.count(); j++) {
			lnewmarker = this.getMarkerforPage(ls.pages[j]);
			if (!lnewmarker)
				lnumpages++;
			else {
				if (loldmarker)
					this.addPBComent(loldmarker, lnumpages);
				loldmarker = lnewmarker;
				lnumpages = 0;
			}
		}
	}
}

function ESM_initFigsandGraphsNode(lpos) {
	var gxmlnd = myDoc.xmlElements.item(0);
	this.m_xmlobjects = null;
	for ( var i = 0; i < gxmlnd.xmlElements.count(); i++) {
		var litem = gxmlnd.xmlElements.item(i);
		if ((litem.markupTag.name == "article_figs_and_tables")
				&& (getAttributeValue(litem, 'article_pos') == lpos)) {
			this.removeAllCommentsReqursive(litem);
			this.removeAllTagsReqursive(litem, 1);
			this.m_xmlobjects = litem;
			break;
		}
	}
	if (!this.m_xmlobjects) {
		var lnewptag = addcheckXMLTag("article_figs_and_tables");
		this.m_xmlobjects = gxmlnd.xmlElements.add(lnewptag);
		addAttribute(this.m_xmlobjects, 'article_pos', lpos);
		try {
			this.m_xmlobjects.xmlAttributes.add("xmlns:xlink",
					"http://www.w3.org/1999/xlink");
		} catch (e) {
		}
		;
	}
}

function ESM_fillExtraObjects(pstylesarr, ptype) {
	var lcuridx = 1;
	for ( var i = 0; i < pstylesarr.length; i++) {
		// ~ alert("Table
		// "+this.mTemplDets.mdata[this.tablesidxarr[i]]["style"]);
		app.findTextPreferences = NothingEnum.nothing;
		// ~ //var lstyle=addcheckStyles(lTemplDets.mdata[l]["style"],
		// lTemplDets.mdata[l]["type"]);
		if (this.mTemplDets.mdata[pstylesarr[i]]["type"] == "1")
			app.findTextPreferences.appliedCharacterStyle = this.mTemplDets.mdata[pstylesarr[i]]["style"];
		else
			app.findTextPreferences.appliedParagraphStyle = this.mTemplDets.mdata[pstylesarr[i]]["style"];

		var res = myDoc.findText();
		if (res) {
			for ( var j = 0; j < res.length; j++) {
				// ~ alert(res[j].contents);
				var lnotexist = true;
				var lnewobj = new E_ExtraObject(res[j], ptype, this.mTemplDets);
				for ( var l = 0; l < this.m_extraobjects.length; l++) {
					if (this.m_extraobjects[l].m_keyelement == lnewobj.m_keyelement) {
						lnotexist = false;
						break;
					}
				}
				if (lnotexist) {
					// ~ lnewobj.Tag(this.m_xmlobjects,lcuridx);
					this.m_extraobjects.push(lnewobj);
					lcuridx++;
				}
			}
		}
	}
}
function ESM_searchForFigsandGraphs() {
	this.m_extraobjects = [];
	if (this.figsidxarr.length || this.tablesidxarr.length) {
		app.findChangeTextOptions.includeFootnotes = false;
		app.findChangeTextOptions.includeHiddenLayers = false;
		app.findChangeTextOptions.includeMasterPages = false;
		this.fillExtraObjects(this.tablesidxarr, 1);
		this.fillExtraObjects(this.figsidxarr, 2);

		// ~ for(var i=0; i<this.figsidxarr.length; i++) {
		// ~ alert("Fig "+this.mTemplDets.mdata[this.figsidxarr[i]]["style"]);
		// ~ }
		// ~ for(var i=0; i<this.tablesidxarr.length; i++) {
		// ~ alert("Table
		// "+this.mTemplDets.mdata[this.tablesidxarr[i]]["style"]);
		// ~ app.findTextPreferences = NothingEnum.nothing;
		// var lstyle=addcheckStyles(lTemplDets.mdata[l]["style"],
		// lTemplDets.mdata[l]["type"]);
		// ~
		// app.findTextPreferences.appliedCharacterStyle=this.mTemplDets.mdata[this.tablesidxarr[i]]["style"];
		// ~ var res=myDoc.findText();
		// ~ if (res) {
		// ~ for(var j=0; j<res.length; j++) {
		// ~ alert(res[j].contents);
		// ~ }
		// ~ }
		// ~ }
	}
	if (!this.m_extraobjects.length) {
		this.m_xmlobjects.untag();
		this.m_xmlobjects = null;
	} else {
		var gPageNum = 0;
		for ( var lSpreadNum = 0; lSpreadNum < myDoc.spreads.count(); lSpreadNum++) {
			var lSpread = myDoc.spreads.item(lSpreadNum);
			for ( var lPageNum = 0; lPageNum < lSpread.pages.count(); lPageNum++) {
				var lPage = lSpread.pages.item(lPageNum);
				gPageNum++;
				// ~ alert('Page:' + lPageNum);
				for ( var lPageItemNum = 0; lPageItemNum < lPage.pageItems
						.count(); lPageItemNum++) {
					var lLocalPageItem = lPage.pageItems.item(lPageItemNum);

					// ~ alert('PageItem:' + lPageItemNum);
					for ( var lObjectIdx = 0; lObjectIdx < this.m_extraobjects.length; lObjectIdx++) {
						var lExtraObject = this.m_extraobjects[lObjectIdx];

						var lFound = false;
						if (lExtraObject.m_page == gDefaultPage) {
							if (lExtraObject.m_is_group) {
								if (lLocalPageItem == lExtraObject.m_keyelement) {
									lFound = true;
								}
							} else {
								var lObjectTextFrames = lExtraObject.m_keyelement.textFrames;
								for ( var lTextFrameNum = 0; lTextFrameNum < lObjectTextFrames
										.count(); lTextFrameNum++) {
									var lTextFrame = lObjectTextFrames
											.item(lTextFrameNum);
									if (lLocalPageItem == lTextFrame) {
										if (lExtraObject.m_x > lTextFrame.geometricBounds[1]) {
											lExtraObject.m_x = lTextFrame.geometricBounds[1];
										}
										if (lExtraObject.m_y > lTextFrame.geometricBounds[0]) {
											lExtraObject.m_y = lTextFrame.geometricBounds[0];
										}
										lFound = true;
									}
								}
								if (!lFound) {
									var lObjectTextContainers = lExtraObject.m_keyelement.textContainers;
									for ( var lTextContainerNum = 0; lTextContainerNum < lObjectTextContainers.length; lTextContainerNum++) {
										var lTextContainer = lObjectTextContainers[lTextContainerNum];
										if (lLocalPageItem == lTextContainer) {
											if (lExtraObject.m_x > lTextContainer.geometricBounds[1]) {
												lExtraObject.m_x = lTextContainer.geometricBounds[1];
											}
											if (lExtraObject.m_y > lTextContainer.geometricBounds[0]) {
												lExtraObject.m_y = lTextContainer.geometricBounds[0];
											}
											lFound = true;
										}
									}
								}
							}
						}
						// ~ alert('Spread:' + lSpreadNum + ' Page:' + lPageNum
						// + ' PageItem:' + lPageItemNum +' lExtraObject:' +
						// lObjectIdx + ' is_group:' + lExtraObject.m_is_group +
						// ' GroupEquals:' + (lLocalPageItem ==
						// lExtraObject.m_keyelement) + ' Found:' + lFound);
						if (lFound) {
							lExtraObject.m_page = gPageNum;
							break;
						}
					}
				}
			}
		}

		// sortirovka
		for ( var lObjectIdx = 0; lObjectIdx < this.m_extraobjects.length; lObjectIdx++) {
			var lCurrentObj = this.m_extraobjects[lObjectIdx];
			for ( var lFollowingObjectIdx = lObjectIdx + 1; lFollowingObjectIdx < this.m_extraobjects.length; lFollowingObjectIdx++) {
				var lTempObj = this.m_extraobjects[lFollowingObjectIdx];
				if (lTempObj.m_page > lCurrentObj.m_page)// Ako e na
					// sledvashta
					// stranica - znachi
					// e sled nego
					continue;
				if (lTempObj.m_page == lCurrentObj.m_page
						&& lTempObj.m_y > lCurrentObj.m_y)// Ako e na syshtata
					// stranica no e
					// po-nadolu -
					// znachi e sled
					// nego
					continue;
				if (lTempObj.m_page == lCurrentObj.m_page
						&& lTempObj.m_y == lCurrentObj.m_y
						&& lTempObj.m_x > lCurrentObj.m_x)// Ako e na syshtata
					// stranica, syshtiq
					// Y, no e
					// po-nadqsno -
					// znachi e sled
					// nego
					continue;
				// Trqbvva da go predhojda
				this.m_extraobjects[lFollowingObjectIdx] = lCurrentObj;
				this.m_extraobjects[lObjectIdx] = lTempObj;
				lCurrentObj = this.m_extraobjects[lObjectIdx];
			}
		}

		var lcurf = 1, lcurt = 1;
		for ( var lObjectIdx = 0; lObjectIdx < this.m_extraobjects.length; lObjectIdx++) {
			var lidx;
			if (this.m_extraobjects[lObjectIdx].m_type == 2) {
				lidx = lcurf;
				lcurf++;
			} else {
				lidx = lcurt;
				lcurt++;
			}
			this.m_extraobjects[lObjectIdx].Tag(this.m_xmlobjects, lidx);
			// ~
			// alert(this.m_extraobjects[lObjectIdx].m_page+","+this.m_extraobjects[lObjectIdx].m_x+"
			// "+this.m_extraobjects[lObjectIdx].m_y);
		}
	}
	/*
	 * this.m_pstory=this.mxmlel.parentStory; var loldmarker=null,
	 * lnewmarker=null, lnumpages=0; for (var i=0; i<myDoc.spreads.count();
	 * i++) { var ls=myDoc.spreads[i]; for(var j=0;j<ls.pages.count(); j++) {
	 * lnewmarker=this.getMarkerforPage(ls.pages[j]); if (!lnewmarker)
	 * lnumpages++; else { if (loldmarker)
	 * this.addPBComent(loldmarker,lnumpages); loldmarker=lnewmarker;
	 * lnumpages=0; } } }
	 */
}
function ESM_getMarkerforPage(ppage) {
	var res = null;
	var lcurtf = null, lcurx2 = null;
	for ( var i = 0; i < ppage.textFrames.count(); i++) {
		if ((ppage.textFrames[i].parentStory == this.m_pstory)
				&& (ppage.textFrames[i].characters.count())) {
			if (!lcurtf || (lcurx2 <= ppage.textFrames[i].geometricBounds[3])) {
				lcurtf = ppage.textFrames[i];
				lcurx2 = ppage.textFrames[i].geometricBounds[3];
			}
		}
	}
	if (lcurtf)
		res = {
			"c" : lcurtf.characters[lcurtf.characters.count() - 1],
			"tf" : lcurtf
		};
	return res;
}

function ESM_addPBComent(pmarker, pnumpages) {
	var lnode = pmarker.c.associatedXMLElements[0];
	var myText = this.mxmlel.parentStory.texts
			.itemByRange(pmarker.c, pmarker.c);
	for ( var i = 0; i <= pnumpages; i++) {
		var lxmlCom = lnode.xmlComments.add("PageBreak");
		lxmlCom.move(LocationOptions.after, myText);
	}
}
function ESM_removeBIUTags(xmlNode) {
	if (!xmlNode || (typeof (xmlNode) == "undefined")) {
		this.removeAllComments();
		xmlNode = this.mxmlel;
	}
	var elcnt = xmlNode.xmlElements.count();
	if (elcnt) {
		for ( var i = elcnt - 1; i >= 0; i--)
			this.removeBIUTags(xmlNode.xmlElements.item(i));
	}
	var lname = xmlNode.markupTag.name;
	if ((lname == "bold") || (lname == "italic") || (lname == "underline")
			|| (lname == "sup") || (lname == "sub"))
		xmlNode.untag();
}
function ESM_GenMapFromArrays() {
	var i = 0, j = 0;
	var bi = 1, bj = 1;
	while ((i < this.mstylemap.pars.length)
			|| (j < this.mstylemap.chars.length)) {
		bi = (i < this.mstylemap.pars.length);
		bj = (j < this.mstylemap.chars.length);
		var linspar = 0, linschar = 0;
		var lcel = this.mstylemap.chars[j];
		var lpel = this.mstylemap.pars[i];
		if (bi && bj) {
			if ((lcel["start"] > lpel["end"])) {
				// nezachashti
				this.mstylemap.map.push({
					"start" : lpel["start"],
					"end" : lpel["end"],
					"tstart" : lpel["tstart"],
					"tend" : lpel["tend"],
					"tmeplid" : lpel["tmeplid"]
				});
				i++;
			} else if (lpel["start"] > lcel["end"]) {
				// nezachashti
				this.mstylemap.map.push({
					"start" : lcel["start"],
					"end" : lcel["end"],
					"tstart" : lcel["tstart"],
					"tend" : lcel["tend"],
					"tmeplid" : lcel["tmeplid"]
				});
				j++;
			} else {
				var lpdown = 0, lcdown = 0;
				var lwhat = 0;
				var lpfp = this.mTemplDets.mdata[lpel["tmeplid"]]["full_path"];
				var lptagarrlen = (this.mTemplDets.mdata[lpel["tmeplid"]]["parenttagarr"] ? this.mTemplDets.mdata[lpel["tmeplid"]]["parenttagarr"].length
						: 0);
				var lpgodown = this.mTemplDets.mdata[lpel["tmeplid"]]["change_after"] ? ((lpfp
						.charAt(0) == ".") ? this.mTemplDets.mdata[lpel["tmeplid"]]["change_after"]
						- lptagarrlen
						: this.mTemplDets.mdata[lpel["tmeplid"]]["change_after"])
						: 0;
				var lcfp = this.mTemplDets.mdata[lcel["tmeplid"]]["full_path"];
				var lctagarrlen = (this.mTemplDets.mdata[lcel["tmeplid"]]["parenttagarr"] ? this.mTemplDets.mdata[lcel["tmeplid"]]["parenttagarr"].length
						: 0);
				var lcgodown = this.mTemplDets.mdata[lcel["tmeplid"]]["change_after"] ? ((lcfp
						.charAt(0) == ".") ? this.mTemplDets.mdata[lcel["tmeplid"]]["change_after"]
						- lctagarrlen
						: this.mTemplDets.mdata[lcel["tmeplid"]]["change_after"])
						: 0;
				// koe v koe spriamo full_path
				if (lcfp.charAt(0) == ".") {
					if (lcgodown >= 1)
						lwhat = 1;
				} else if (lpfp.charAt(0) == ".") {
					if (lpgodown >= 1)
						lwhat = 2;
				} else if (!lpfp.indexOf(lcfp, 0)
						&& (lctagarrlen < lptagarrlen)
						&& (lctagarrlen <= lptagarrlen - lpgodown)) {
					lwhat = 2;
				} else if (!lcfp.indexOf(lpfp, 0)
						&& (lctagarrlen > lptagarrlen)
						&& (lptagarrlen <= lctagarrlen - lcgodown)) {
					lwhat = 1;
				}

				// ~ if (lcfp.charAt(0)==".") lwhat=1;
				// ~ else if ((lpfp.charAt(0)==".") || (!lpfp.indexOf(lcfp, 0)))
				// lwhat=2;
				// ~ else if (!lcfp.indexOf(lpfp, 0)) lwhat=1;

				if (((lpdown = (lpel["start"] <= lcel["start"])) && (lpdown = (lpel["end"] >= lcel["end"])))
						|| ((lcdown = (lcel["start"] <= lpel["start"])) && (lcdown = (lcel["end"] >= lpel["end"])))) {
					// edinia e v drugia
					var leqstart = (lpel["start"] == lcel["start"]);
					var leqend = (lpel["end"] == lcel["end"]);
					if (leqstart && leqend) {
						// izcialo syvpadashto
						if (lwhat) {
							var lout, lin;
							if (lwhat == 1) {
								lout = lpel["tmeplid"];
								lin = lcel["tmeplid"];
							} else {
								lout = lcel["tmeplid"];
								lin = lpel["tmeplid"];
							}
							this.mstylemap.map.push({
								"start" : lpel["start"],
								"end" : lpel["start"],
								"tstart" : lout["tstart"],
								"tend" : lout["tend"],
								"tmeplid" : lout["tmeplid"]
							});
							this.mstylemap.map.push({
								"start" : lpel["start"],
								"end" : lpel["end"],
								"tstart" : lin["tstart"],
								"tend" : lin["tend"],
								"tmeplid" : lin["tmeplid"]
							});
							this.mstylemap.map.push({
								"start" : lpel["end"],
								"end" : lpel["end"],
								"tstart" : lout["tstart"],
								"tend" : lout["tend"],
								"tmeplid" : lout["tmeplid"]
							});
						} else
							this.mstylemap.map.push({
								"start" : lpel["start"],
								"end" : lpel["end"],
								"tstart" : lpel["tstart"],
								"tend" : lpel["tend"],
								"tmeplid" : lpel["tmeplid"]
							});
						i++;
						j++;
					} else {
						var l1, l2, l3, l4;
						if (lpdown) {
							l1 = lpel["start"];
							l2 = lcel["start"];
							l3 = lcel["end"];
							l4 = lpel["end"];
						} else {
							l2 = lpel["start"];
							l1 = lcel["start"];
							l4 = lcel["end"];
							l3 = lpel["end"];
						}
						// tuk mai triabva po-vnimatelno da se gleda what pri
						// (leqstart || leqend) ma zasega tolkoz
						var lact;
						if (!leqstart || lwhat) {
							if ((!leqstart && lpdown)
									|| (leqstart && (lwhat == 1)))
								lact = lpel;
							else
								lact = lcel;
							this.mstylemap.map.push({
								"start" : l1,
								"end" : !leqstart ? l2 - 1 : l2,
								"tstart" : lact["tstart"],
								"tend" : lact["tend"],
								"tmeplid" : lact["tmeplid"]
							});
						}
						if (lpdown)
							lact = lcel;
						else
							lact = lpel;
						this.mstylemap.map.push({
							"start" : l2,
							"end" : l3,
							"tstart" : lact["tstart"],
							"tend" : lact["tend"],
							"tmeplid" : lact["tmeplid"]
						});
						if (!leqend || lwhat) {
							if (leqend) {
								if ((!leqend && lpdown)
										|| (leqend && (lwhat == 1)))
									lact = lpel;
								else
									lact = lcel;
								this.mstylemap.map.push({
									"start" : !leqend ? l3 + 1 : l3,
									"end" : l4,
									"tstart" : lact["tstart"],
									"tend" : lact["tend"],
									"tmeplid" : lact["tmeplid"]
								});
								i++;
								j++;
							} else {
								if (lpdown) {
									lpel["start"] = l3 + 1;
									j++;
								} else {
									lcel["start"] = l3 + 1;
									i++;
								}
							}
						} else {
							i++;
							j++;
						}
						// ~ if (!leqend || lwhat)
						// this.mstylemap.map.push({"start": !leqend ? l3+1 :
						// l3, "end": l4,"tmeplid": ((!leqend && lpdown ) ||
						// (leqend && (lwhat==1))) ? lpel["tmeplid"] :
						// lcel["tmeplid"]});
					}
				} else {
					// chastichno zasecheni
					var l1, l2, l3, l4;
					if (lpel["start"] < lcel["start"]) {
						l1 = lpel["start"];
						l2 = lcel["start"];
						l3 = lpel["end"];
						l4 = lcel["end"];
						lpdown = 1;
					} else {
						l2 = lpel["start"];
						l1 = lcel["start"];
						l4 = lpel["end"];
						l3 = lcel["end"];
						lcdown = 1;
					}
					var lact;
					if (lpdown)
						lact = lpel;
					else
						lact = lcel;
					this.mstylemap.map.push({
						"start" : l1,
						"end" : l2 - 1,
						"tstart" : lact["tstart"],
						"tend" : lact["tend"],
						"tmeplid" : lact["tmeplid"]
					});
					// obshta chast

					if (lwhat == 1) {
						if (lcdown)
							this.mstylemap.map.push({
								"start" : l2,
								"end" : l2,
								"tstart" : lpel["tstart"],
								"tend" : lpel["tend"],
								"tmeplid" : lpel["tmeplid"]
							});
						this.mstylemap.map.push({
							"start" : l2,
							"end" : l3,
							"tstart" : lcel["tstart"],
							"tend" : lcel["tend"],
							"tmeplid" : lcel["tmeplid"]
						});
						if (lpdown)
							this.mstylemap.map.push({
								"start" : l3,
								"end" : l3,
								"tstart" : lpel["tstart"],
								"tend" : lpel["tend"],
								"tmeplid" : lpel["tmeplid"]
							});
					} else if (lwhat == 2) {
						if (lpdown)
							this.mstylemap.map.push({
								"start" : l2,
								"end" : l2,
								"tstart" : lcel["tstart"],
								"tend" : lcel["tend"],
								"tmeplid" : lcel["tmeplid"]
							});
						this.mstylemap.map.push({
							"start" : l2,
							"end" : l3,
							"tstart" : lpel["tstart"],
							"tend" : lpel["tend"],
							"tmeplid" : lpel["tmeplid"]
						});
						if (lcdown)
							this.mstylemap.map.push({
								"start" : l3,
								"end" : l3,
								"tstart" : lcel["tstart"],
								"tend" : lcel["tend"],
								"tmeplid" : lcel["tmeplid"]
							});
					} else
						this.mstylemap.map[this.mstylemap.map.length - 1]["end"] = l3;
					// obshta chast krai
					// ~ this.mstylemap.map.push({"start": l3+1, "end":
					// l4,"tmeplid": lpdown ? lcel["tmeplid"] :
					// lpel["tmeplid"]});
					if (lcdown) {
						lpel["start"] = l3 + 1;
						j++;
					} else {
						lcel["start"] = l3 + 1;
						i++;
					}
					// ~ i++;j++;
				}
			}
		} else if (bi) {
			this.mstylemap.map.push({
				"start" : lpel["start"],
				"end" : lpel["end"],
				"tstart" : lpel["tstart"],
				"tend" : lpel["tend"],
				"tmeplid" : lpel["tmeplid"]
			});
			i++;
		} else {
			this.mstylemap.map.push({
				"start" : lcel["start"],
				"end" : lcel["end"],
				"tstart" : lcel["tstart"],
				"tend" : lcel["tend"],
				"tmeplid" : lcel["tmeplid"]
			});
			j++;
		}
	}
	for (i = 0; i < this.mstylemap.map.length; i++) {
		var lm = this.mstylemap.map[i];
		lm["full_path"] = this.mTemplDets.mdata[lm["tmeplid"]]["full_path"];
	}
}

function ESM_GenStylesArrays() {
	var lccnt = this.mxmlel.characters.count();
	if (!lccnt)
		return -1;
	// ~ var curPstyle=this.mxmlel.characters[0].appliedParagraphStyle.name;
	// ~ var curCstyle=this.mxmlel.characters[0].appliedCharacterStyle.name;
	var curPstyle = null;
	var lcurPIdx = -1;
	var curCstyle = null;
	var lcurCIdx = -1;
	this.InitMap();
	for ( var i = 0; i < lccnt; i++) {
		var lchar = this.mxmlel.characters[i];
		var lcstyle = lchar.appliedCharacterStyle.name;
		var lpstyle = lchar.appliedParagraphStyle.name;
		if (lcstyle != curCstyle) {
			if (lcurCIdx > -1)
				this.mstylemap.chars[this.mstylemap.chars.length - 1].end = i - 1;
			lcurCIdx = this.CheckStyleName(lcstyle, 1);
			if (lcurCIdx > -1)
				this.mstylemap.chars.push({
					"start" : i,
					"end" : -1,
					"sname" : lcstyle,
					"tmeplid" : lcurCIdx
				});
			curCstyle = lcstyle;
		}
		if (lpstyle != curPstyle) {
			if (lcurPIdx > -1)
				this.mstylemap.pars[this.mstylemap.pars.length - 1].end = i - 1;
			lcurPIdx = this.CheckStyleName(lpstyle, 2);
			if (lcurPIdx > -1)
				this.mstylemap.pars.push({
					"start" : i,
					"end" : -1,
					"sname" : lpstyle,
					"tmeplid" : lcurPIdx
				});
			curPstyle = lpstyle;
		}
	}
	if (this.mstylemap.chars.length
			&& (this.mstylemap.chars[this.mstylemap.chars.length - 1].end == -1))
		this.mstylemap.chars[this.mstylemap.chars.length - 1].end = lccnt - 1;
	if (this.mstylemap.pars.length
			&& (this.mstylemap.pars[this.mstylemap.pars.length - 1].end == -1))
		this.mstylemap.pars[this.mstylemap.pars.length - 1].end = lccnt - 1;
}
function ESM_GenStylesArraysNew() {
	var lccnt = this.mxmlel.characters.count();
	if (!lccnt)
		return -1;
	// ~ var curPstyle=this.mxmlel.characters[0].appliedParagraphStyle.name;
	// ~ var curCstyle=this.mxmlel.characters[0].appliedCharacterStyle.name;
	var curPstyle = null;
	var lcurPIdx = -1;
	var curCstyle = null;
	var lcurCIdx = -1;
	this.InitMap();
	lccnt = this.mxmlel.textStyleRanges.count();
	var lposE, ltsc;
	var lbeg = this.mxmlel.parentStory.characters[0].insertionPoints.item(-1).index;
	// if(myParagraphs.item(0).parentStory.insertionPoints.item(-1).index ==
	// myParagraphs.item(-1).insertionPoints.item(-1).index){
	for ( var i = 0; i < lccnt; i++) {
		ltsc = this.mxmlel.textStyleRanges[i];
		var lcstyle = ltsc.appliedCharacterStyle.name;
		var lpstyle = ltsc.appliedParagraphStyle.name;
		var lposS = ltsc.characters[0].insertionPoints.item(-1).index - lbeg;
		if (lcstyle != curCstyle) {
			if (lcurCIdx > -1)
				this.mstylemap.chars[this.mstylemap.chars.length - 1].tend = this.mstylemap.chars[this.mstylemap.chars.length - 1].end = lposS - 1;// i-1;
			lcurCIdx = this.CheckStyleName(lcstyle, 1);
			if (lcurCIdx > -1)
				this.mstylemap.chars.push({
					"start" : lposS,
					"end" : -1,
					"tstart" : lposS,
					"tend" : -1,
					"sname" : lcstyle,
					"tmeplid" : lcurCIdx
				});
			curCstyle = lcstyle;
		}
		if (lpstyle != curPstyle) {
			if (lcurPIdx > -1)
				this.mstylemap.pars[this.mstylemap.pars.length - 1].tend = this.mstylemap.pars[this.mstylemap.pars.length - 1].end = lposS - 1;// i-1;
			lcurPIdx = this.CheckStyleName(lpstyle, 2);
			if (lcurPIdx > -1)
				this.mstylemap.pars.push({
					"start" : lposS,
					"end" : -1,
					"tstart" : lposS,
					"tend" : -1,
					"sname" : lpstyle,
					"tmeplid" : lcurPIdx
				});
			curPstyle = lpstyle;
		}
	}
	lposE = ltsc.characters[ltsc.characters.count() - 1].insertionPoints
			.item(-1).index
			- lbeg;
	if (this.mstylemap.chars.length
			&& (this.mstylemap.chars[this.mstylemap.chars.length - 1].end == -1))
		this.mstylemap.chars[this.mstylemap.chars.length - 1].tend = this.mstylemap.chars[this.mstylemap.chars.length - 1].end = lposE;// lccnt-1;
	if (this.mstylemap.pars.length
			&& (this.mstylemap.pars[this.mstylemap.pars.length - 1].end == -1))
		this.mstylemap.pars[this.mstylemap.pars.length - 1].tend = this.mstylemap.pars[this.mstylemap.pars.length - 1].end = lposE;// lccnt-1;
	this.ChangePinParsArray();

}
function ESM_ChangePinParsArray() {
	this.mstylemap.newpars = [];
	var parscnt = this.mstylemap.pars.length;
	for ( var i = 0; i < parscnt; i++) {
		var lcur = this.mstylemap.pars[i];
		var lnodename = this.mTemplDets.mdata[lcur["tmeplid"]]["xmlnode"];
		if ((lnodename == "p") || (lnodename == "P")) {
			var myText = this.mxmlel.parentStory.texts.itemByRange(
					this.mxmlel.characters[lcur["start"]],
					this.mxmlel.characters[lcur["end"]]);
			var lstr = myText.contents.toString();
			var lcstart = lcur["start"];
			var lpos;
			while (-1 != (lpos = lstr.indexOf("\r"))) {
				this.mstylemap.newpars.push({
					"start" : lcstart,
					"end" : lcstart + lpos,
					"tstart" : lcstart,
					"tend" : lcstart + lpos,
					"sname" : lcur["sname"],
					"tmeplid" : lcur["tmeplid"]
				});
				lcstart += lpos + 1;
				if (lcstart > lcur["end"])
					break;
				lstr = lstr.substr(lpos + 1);
			}
			if (lcstart <= lcur["end"])
				this.mstylemap.newpars.push({
					"start" : lcstart,
					"end" : lcur["end"],
					"tstart" : lcstart,
					"tend" : lcur["tend"],
					"sname" : lcur["sname"],
					"tmeplid" : lcur["tmeplid"]
				});
			;
		} else
			this.mstylemap.newpars.push({
				"start" : lcur["start"],
				"end" : lcur["end"],
				"tstart" : lcur["tstart"],
				"tend" : lcur["tend"],
				"sname" : lcur["sname"],
				"tmeplid" : lcur["tmeplid"]
			});
	}
	this.mstylemap.pars = this.mstylemap.newpars;
}

function ESM_CheckStyleName(pstyle, ptype) {
	for ( var i = 0; i < this.mTemplDets.mcnt; i++) {
		if ((this.mTemplDets.mdata[i].type == ptype)
				&& (this.mTemplDets.mdata[i].style == pstyle))
			return i;
	}
	return -1;
}

function ESM_StylestoTags() {
	var lcurnodes = [ this.mxmlel ];
	// ~ this.PrepareMaps();
	this.doStylestoTags(0, this.mstylemap.map.length - 1, lcurnodes, "/"
			+ this.mxmlel.markupTag.name, 0);
}
function ESM_StylestoTagsNew() {
	this.m_RootOurNode = new E_OurNode(null, this.mstylemap.map[0]["start"],
			this.mstylemap.map[this.mstylemap.map.length - 1]["end"],
			this.mxmlel.markupTag.name);
	this.m_RootOurNode.m_xmlnode = this.mxmlel;
	var lcurnodes = [ this.m_RootOurNode ];
	// ~ this.PrepareMaps();
	this.doStylestoTagsNew(0, this.mstylemap.map.length - 1, lcurnodes, "/"
			+ this.mxmlel.markupTag.name, 0);
	this.RealMarkup(this.m_RootOurNode, 0);
}

function ESM_PrepareMaps() {
	for ( var i = 0; i < this.mstylemap.map.length; i++) {
		var lmap = this.mstylemap.map[i];
		lmap["cstart"] = this.mxmlel.characters[lmap["start"]];
		lmap["cend"] = this.mxmlel.characters[lmap["end"]];
	}
}

function ESM_getNextforStylestoTags(pstart, pend, pfull_path, pdotpos) {
	var lres = -1;
	var lmap = this.mstylemap.map[pstart];
	if (this.mTemplDets.mdata[lmap["tmeplid"]]["change_before"]) {
		var lidx;
		var lcur;
		var lprev = lmap;
		for (lidx = pstart + 1; lidx <= pend; lidx++) {
			var lcur = this.mstylemap.map[lidx];
			if ((lcur["start"] > lprev["end"] + 1) || (lcur["istagged"] == 1)) {
				// ~ if (lcur["start"]>lprev["end"]+1) {
				// ~ ldupka=1;

				// ~ }
				break; // dupka ili veche tagnat t.e. pak dupka taka che
				// spirame
			} else if (lcur["tmeplid"] == lmap["tmeplid"]) {
				if ((lcur["tstart"] == lmap["tstart"])
						&& (lcur["tend"] == lmap["tend"])) {
					// ~ lcur["istagged"]=1;
					; // pak sme nie i si otbeliazvame kakto i na toz
					// otbeliazvame che shte go tagnem za bydeshti rekursii
					// da ne se vkluchva
				} else {
					return lidx;
				}
			} else if (((lcur["full_path"].charAt(0) == ".") || (!lcur["full_path"]
					.indexOf(pfull_path, 0)))) {
				var lptagarrlen = (this.mTemplDets.mdata[lcur["tmeplid"]]["parenttagarr"] ? this.mTemplDets.mdata[lcur["tmeplid"]]["parenttagarr"].length
						: 0);
				var lbreak = 0;
				if (this.mTemplDets.mdata[lcur["tmeplid"]]["change_after"]) {
					if (lcur["full_path"].charAt(0) == ".")
						lbreak = ((this.mTemplDets.mdata[lcur["tmeplid"]]["change_after"] - lptagarrlen) < 2) ? 0
								: 1;
					else {
						// if (!lpfp.indexOf(lcfp, 0) &&
						lbreak = (pdotpos + 1 < lptagarrlen)
								&& (pdotpos + 1 <= lptagarrlen
										- this.mTemplDets.mdata[lcur["tmeplid"]]["change_after"]) ? 0
								: 1;
					}
				}
				// ~ linlinecnt++;
				if (!lbreak) {
					// ~
					// (!this.mTemplDets.mdata[lmap["tmeplid"]]["change_after"]
					// || ((lmap["tstart"]<= lcur["tstart"]) && (lmap["tend"]>=
					// lcur["tend"]) ))&&
					// ~ lendidx=lidx;
					// ~ lconfirminlinecnt++; //relativen
				} else
					break;
			} else
				break; // ne e relativen spriamo nas taka che spirame
			lprev = lcur;
		}
	}
	return lres;
}

function ESM_doStylestoTags(pstart, pend, pcurnodes, pcurpath, pdotpos) {
	if (pstart > pend)
		return;
	var lmap = this.mstylemap.map[pstart];
	if (lmap["istagged"] == 1)
		return this.doStylestoTags(pstart + 1, pend, pcurnodes, pcurpath,
				pdotpos);
	lmap["istagged"] = 1;
	var lfull_path = (lmap["full_path"].charAt(0) == ".") ? pcurpath
			+ lmap["full_path"].substr(1) : lmap["full_path"];
	var lendidx = pstart;
	var lidx;
	var lcur;
	var ldupka = 0;
	var lprev = lmap;
	var linlinecnt = 0;
	var lsameindex = pstart;
	var lconfirminlinecnt = 0;
	var lfindnextbreak = this.getNextforStylestoTags(pstart, pend, lfull_path,
			pdotpos);
	var ldupkapcurnodes, ldupkapcurpath, ldupkapdotpos, loldend;
	if (lfindnextbreak > 0) {
		ldupkapcurnodes = pcurnodes.slice(0, pdotpos + 1);
		ldupkapcurpath = pcurpath;
		ldupkapdotpos = pdotpos;
		loldend = pend;
		pend = lfindnextbreak - 1;

	}
	for (lidx = pstart + 1; lidx <= pend; lidx++) {
		var lcur = this.mstylemap.map[lidx];
		if ((lcur["start"] > lprev["end"] + 1) || (lcur["istagged"] == 1)) {
			if (lcur["start"] > lprev["end"] + 1) {
				ldupka = 1;
				// ~ alert("dupka");

			}
			break; // dupka ili veche tagnat t.e. pak dupka taka che spirame
		} else if (lcur["tmeplid"] == lmap["tmeplid"]) {
			if (!this.mTemplDets.mdata[lmap["tmeplid"]]["change_before"]
					|| ((lcur["tstart"] == lmap["tstart"]) && (lcur["tend"] == lmap["tend"]))) {
				lcur["istagged"] = 1;
				// ~ lconfirminlinecnt+=linlinecnt;
				lsameindex = lidx;
				lconfirminlinecnt++;
				// ~ linlinecnt=0;
				lendidx = lidx; // pak sme nie i si otbeliazvame kakto i na toz
				// otbeliazvame che shte go tagnem za bydeshti
				// rekursii da ne se vkluchva
			} else {
				ldupka = 2;
				break;
			}
		} else if ((!this.mTemplDets.mdata[lmap["tmeplid"]]["change_after"] || ((lmap["tstart"] <= lcur["tstart"]) && (lmap["tend"] >= lcur["tend"])))
				&& ((lcur["full_path"].charAt(0) == ".") || (!lcur["full_path"]
						.indexOf(lfull_path, 0)))) {
			var lptagarrlen = (this.mTemplDets.mdata[lcur["tmeplid"]]["parenttagarr"] ? this.mTemplDets.mdata[lcur["tmeplid"]]["parenttagarr"].length
					: 0);
			var lbreak = 0;
			if (this.mTemplDets.mdata[lcur["tmeplid"]]["change_after"]) {
				if (lcur["full_path"].charAt(0) == ".")
					lbreak = ((this.mTemplDets.mdata[lcur["tmeplid"]]["change_after"] - lptagarrlen) < 2) ? 0
							: 1;
				else {
					// if (!lpfp.indexOf(lcfp, 0) &&
					lbreak = (pdotpos + 1 < lptagarrlen)
							&& (pdotpos + 1 <= lptagarrlen
									- this.mTemplDets.mdata[lcur["tmeplid"]]["change_after"]) ? 0
							: 1;
				}
			}
			// ~ linlinecnt++;
			if (!lbreak) {
				lendidx = lidx;
				lconfirminlinecnt++; // relativen
			} else
				break;
		} else
			break; // ne e relativen spriamo nas taka che spirame
		lprev = lcur;
	}
	// pravim si novia node
	var lpararr = this.mTemplDets.mdata[lmap["tmeplid"]]["parenttagarr"];
	var lstate = 0;
	var lxmlNode;
	var lcurnodesidx = 0, lournodesidx = 0;
	var b = true;

	if (ldupka == 2) {
		// zapazvame za sled special dupkata
		ldupkapcurnodes = pcurnodes.slice(0, pdotpos + 1);
		ldupkapcurpath = pcurpath;
		ldupkapdotpos = pdotpos;
	}
	var lnodeadded = 0;
	var lwillcreatemax = (!lpararr ? 0 : lpararr.length) + 1;
	if (lmap["full_path"].charAt(0) == ".") {
		// ~ var b=true;
		if (pdotpos >= pcurnodes.length - 1)
			lstate = 1;
		else
			lcurnodesidx = pdotpos + 1;
		if (!lpararr || !lpararr.length) {
			b = false;
			if (!lstate) {
				// ~ pcurnodes.splice(pdotpos+1,pcurnodes.length-pdotpos-1);
				pcurnodes = pcurnodes.slice(0, pdotpos + 1);
				pcurpath = this.GenCurPath(pcurnodes);
			}
		}
	}

	while (b) {
		if (!lstate) {
			// ~ if ( this.mTemplDets.mdata[lcur["tmeplid"]]["change_after"] &&
			// this.mTemplDets.mdata[lcur["tmeplid"]]["change_after"] >
			// (lwillcreatemax - 1 - lournodesidx))
			if (pcurnodes[lcurnodesidx].markupTag.name == lpararr[lournodesidx]) {
				lcurnodesidx++;
				lournodesidx++;
				if (lournodesidx >= lpararr.length) {
					if (lcurnodesidx < pcurnodes.length) {
						// ~
						// pcurnodes.splice(lcurnodesidx,pcurnodes.length-lcurnodesidx);
						pcurnodes = pcurnodes.slice(0, lcurnodesidx);
						pdotpos = pcurnodes.length - 1;
						pcurpath = this.GenCurPath(pcurnodes);
					}
					break;
				}
				if (lcurnodesidx >= pcurnodes.length) {
					lstate = 1;
				}
			} else {
				// ~
				// pcurnodes.splice(lcurnodesidx,pcurnodes.length-lcurnodesidx);
				pcurnodes = pcurnodes.slice(0, lcurnodesidx);
				pdotpos = pcurnodes.length - 1;
				pcurpath = this.GenCurPath(pcurnodes);
				lstate = 1;
			}
		} else {
			// dobaviame
			// ~ var lpnode=pcurnodes[pcurnodes.length-1];
			// ~ var lnewptag=addcheckXMLTag(lpararr[lournodesidx]);
			// ~ var lnewpnode=lpnode.xmlElements.add(lnewptag);
			// ~ var
			// lnewpnode=this.doMarkup(pcurnodes[pcurnodes.length-1],lmap["start"],this.mstylemap.map[lendidx]["end"],lpararr[lournodesidx],1,lmap["cstart"],this.mstylemap.map[lendidx]["cend"]);
			var lnewpnode = this.doMarkup(pcurnodes[pcurnodes.length - 1],
					lmap["start"], this.mstylemap.map[lendidx]["end"],
					lpararr[lournodesidx], 1, lendidx);
			this.newNodeAdded(pstart, lendidx, lmap["start"], lnewpnode);
			lnodeadded = 1;
			pcurnodes.push(lnewpnode);
			lournodesidx++;
			if (lournodesidx >= lpararr.length)
				break;
		}
	}
	if ((this.mTemplDets.mdata[lmap["tmeplid"]]["new_parent"] > 0)
			&& (!lnodeadded)) {
		// ~ var l=this.mTemplDets.mdata[lmap["tmeplid"]]["new_parent"];
		// ~ var s=this.mTemplDets.mdata[lmap["tmeplid"]]["xmlnode"];
		// ~ var oldpar=pcurnodes[pcurnodes.length-1];
		// ~ var lpxmlNode=oldpar.parent.xmlElements.add(oldpar.markupTag);
		// ~ this.newNodeAdded(pstart,lendidx,lmap["start"],lpxmlNode);
		var lpxmlNode = this.doMarkup(pcurnodes[pcurnodes.length - 2],
				lmap["start"], this.mstylemap.map[lendidx]["end"],
				pcurnodes[pcurnodes.length - 1].markupTag.name, 1, lendidx);
		this.newNodeAdded(pstart, lendidx, lmap["start"], lpxmlNode);
		pcurnodes[pcurnodes.length - 1] = lpxmlNode;

	}
	lxmlNode = this.doMarkup(pcurnodes[pcurnodes.length - 1], lmap["start"],
			this.mstylemap.map[lendidx]["end"],
			this.mTemplDets.mdata[lmap["tmeplid"]]["xmlnode"], 1, lendidx);
	// ~ lxmlNode=this.CheckParent(lxmlNode,pcurnodes[pcurnodes.length-1]);
	pcurnodes.push(lxmlNode);
	this.newNodeAdded(pstart, lendidx, lmap["start"], lxmlNode);
	if (lconfirminlinecnt) {
		// ima vytre relativni puskame za tiah
		// ~ if (this.mTemplDets.mdata[lmap["tmeplid"]]["change_after"] ) {
		// ~
		// pcurnodes=pcurnodes.slice(0,pcurnodes.length-this.mTemplDets.mdata[lmap["tmeplid"]]["change_after"]);
		// ~ pdotpos=pcurnodes.length-1;
		// ~ lfull_path=this.GenCurPath(pcurnodes);
		// ~ }
		this.doStylestoTags(pstart + 1, lendidx, pcurnodes, lfull_path,
				pcurnodes.length - 1);
	}
	// puskame ostavashtite da se tyrsiat
	if (ldupka == 1) {
		pcurnodes = [ this.mxmlel ];
		pcurpath = "/" + this.mxmlel.markupTag.name;
		pdotpos = 0;
	} else if (ldupka == 2) {
		pcurnodes = ldupkapcurnodes;
		pcurpath = ldupkapcurpath;
		pdotpos = ldupkapdotpos;
	} else if ((this.mTemplDets.mdata[lmap["tmeplid"]]["change_after"])) { // &&
		// !lconfirminlinecnt
		var lcurnodes = pcurnodes.slice(0, pcurnodes.length
				- this.mTemplDets.mdata[lmap["tmeplid"]]["change_after"]);
		pdotpos = lcurnodes.length - 1;
		pcurpath = this.GenCurPath(lcurnodes);
	}
	this.doStylestoTags(lendidx + 1, pend, pcurnodes, pcurpath, pdotpos);
	if (lfindnextbreak > 0) {
		if (lmap["full_path"].charAt(0) != ".") {
			ldupkapcurnodes = pcurnodes.slice(0, pcurnodes.length - 1
					- this.mTemplDets.mdata[lmap["tmeplid"]]["change_after"]);
			ldupkapdotpos = ldupkapcurnodes.length - 1;
			ldupkapcurpath = this.GenCurPath(ldupkapcurnodes);
		}
		this.doStylestoTags(lfindnextbreak, loldend, ldupkapcurnodes,
				ldupkapcurpath, ldupkapdotpos);
	}
}
function ESM_GenCurPath(pcurnodes) {
	var lres = "";
	for ( var i = 0; i < pcurnodes.length; i++)
		lres += "/" + pcurnodes[i].markupTag.name;
	return lres;
}
function ESM_doMarkup(pparent, pstart, pend, pnodename, pdocheck, pendidx) {
	var sc = this.mxmlel.characters[pstart];
	var ec = this.mxmlel.characters[pend];
	var ldoplaceXML = 0;
	var myText;
	if ((pparent == this.mxmlel)
			&& pparent.characters.count()
			&& (sc.insertionPoints.item(-1).index == pparent.characters[0].insertionPoints
					.item(-1).index)
			&& (ec.insertionPoints.item(-1).index == pparent.characters[pparent.characters
					.count() - 1].insertionPoints.item(-1).index)) {
		ldoplaceXML = 1;
	}
	if (!ldoplaceXML) {
		sc.select(SelectionOptions.REPLACE_WITH);
		ec.select(SelectionOptions.ADD_TO);
	} else
		myText = this.mxmlel.parentStory.texts.itemByRange(sc, ec);
	var lnewptag = addcheckXMLTag(pnodename);
	var lxmlNode;

	// ~ else lxmlNode=pparent.parent.xmlElements.add(lnewptag);
	if (!ldoplaceXML) {
		lxmlNode = pparent.xmlElements.add(lnewptag, app.selection[0]);
	} else {
		/*
		 * lxmlNode = pparent.xmlElements.add(lnewptag);
		 * myText.markup(lxmlNode);
		 */
		lxmlNode = pparent.xmlElements.add(lnewptag, myText);
	}
	if (pdocheck)
		lxmlNode = this.CheckParent(lxmlNode, pparent, pend, pendidx);
	return lxmlNode;
}

function ESM_CheckParent(pNode, pPNode, pend, pendidx) {
	var lpel = pNode.parent;
	var lfakenode1 = null;
	if (lpel != pPNode) {
		// alert('Bug');
		var lfakenode = pPNode.xmlElements.add(pNode.markupTag);
		if ((pNode.contents.charCodeAt(pNode.contents.length - 1) == 13)) { // &&
			// (pend+3
			// <
			// this.mxmlel.characters.count()))
			// {
			var lcurpar = this.mxmlel.characters[pend + 1].paragraphs[0];
			var l = pend + 3;
			var lnextch;
			lnextch = this.mxmlel.characters[l];
			while (lnextch.paragraphs[0] == lcurpar) {
				l++;
				lnextch = this.mxmlel.characters[l];
			}
			var lnextchpar = lnextch.associatedXMLElements[lnextch.associatedXMLElements.length - 1];
			var lnextchstyle = lnextch.appliedParagraphStyle;
			lfakenode1 = lnextchpar.xmlElements.add(pNode.markupTag);
			lfakenode1.contents = "\r";
			// for(var i=0;i<lfakenode1.characters.count();i++)
			lfakenode1.characters[0].applyParagraphStyle(lnextchstyle);
			lfakenode1 = lfakenode1.move(LocationOptions.after, pNode);
			// for(var i=0;i<lfakenode1.characters.count();i++)
			// lfakenode1.characters[i].applyParagraphStyle(lnextchstyle);
		}
		// lfakenode.contents="\r";
		// lfakenode.characters[0].applyParagraphStyle(pNode.characters[0].appliedParagraphStyle);
		var l = pNode.paragraphs.count();
		var larr = [];
		if (l > 1) {
			for ( var i = 1; i < l; i++)
				larr.push(pNode.paragraphs[i].appliedParagraphStyle);
		}
		pNode = pNode.move(LocationOptions.after, lfakenode);
		lfakenode.remove();
		if ((l > 1) && (pNode.paragraphs[1].appliedParagraphStyle != larr[0])) {
			for ( var i = 0; i < l - 1; i++)
				if (pNode.paragraphs[i + 1].appliedParagraphStyle != larr[i])
					pNode.paragraphs[i + 1].applyParagraphStyle(larr[i]);
		}
		if (lfakenode1) {
			lfakenode1.untag();
			var l = pNode.characters.count() - 1;
			while (pNode.characters[l].contents.charCodeAt(0) != 13)
				l--;
			pNode.characters[l].remove();
			// ~ if (pdocheck!=2) {
			for ( var i = pendidx + 1; i < this.mstylemap.map.length; i++) {
				if (this.mstylemap.map[i]["start"] == pend + 1)
					this.mstylemap.map[i]["start"]--;
			}
			// ~ }
		}
	}
	return pNode;
}
function ESM_newNodeAdded(pstart, pendidx, pstartpos, pnewNode) {
	var i;
	var ldelta = this.GetDeltaAfterAdd(pstartpos, pnewNode);
	for (i = pstart; i <= pendidx; i++) {
		this.mstylemap.map[i]["start"] += ldelta;
		this.mstylemap.map[i]["end"] += ldelta;
	}
	for (i = pendidx + 1; i < this.mstylemap.map.length; i++) {
		this.mstylemap.map[i]["start"] += 2;
		this.mstylemap.map[i]["end"] += 2;
	}
}
function ESM_GetDeltaAfterAdd(pstartpos, pnewNode) {
	var lch;
	if (pnewNode.characters.count()) {
		lch = pnewNode.characters[0]
		// ~ var lcharcnt=this.mxmlel.characters.count();
		// ~ for (var i=0;i<lcharcnt;i++) {
		// ~ if (this.mxmlel.characters[i]==lch) return i-pstartpos;
		// ~ }
		return lch.insertionPoints.item(-1).index
				- this.mxmlel.parentStory.characters[0].insertionPoints
						.item(-1).index - pstartpos;
	}
	return 0;
}
function ESM_removeAllTagsReqursive(lxmlnode, ldontremove) {
	var lcnt = lxmlnode.xmlElements.count();
	for ( var i = lcnt - 1; i >= 0; i--)
		this.removeAllTagsReqursive(lxmlnode.xmlElements.item(i), 0);
	if (!ldontremove)
		lxmlnode.untag();
}

function ESM_removeAllCommentsReqursive(lxmlnode) {
	var lcnt = lxmlnode.xmlElements.count();
	for ( var i = lcnt - 1; i >= 0; i--)
		this.removeAllCommentsReqursive(lxmlnode.xmlElements.item(i));
	lcnt = lxmlnode.xmlComments.count();
	for ( var i = lcnt - 1; i >= 0; i--)
		lxmlnode.xmlComments.item(i).remove();
}

function ESM_removeAllComments() {
	this.removeAllCommentsReqursive(this.mxmlel);
}

function ESM_removeAllTags() {
	this.removeAllComments();
	this.removeAllTagsReqursive(this.mxmlel, 1);
	// ~ while(this.mxmlel.xmlElements.count())
	// ~ this.mxmlel.xmlElements.item(0).untag();
}

function ESM_doStylestoTagsNew(pstart, pend, pcurnodes, pcurpath, pdotpos) {
	if (pstart > pend)
		return;
	var lmap = this.mstylemap.map[pstart];
	if (lmap["istagged"] == 1)
		return this.doStylestoTagsNew(pstart + 1, pend, pcurnodes, pcurpath,
				pdotpos);
	lmap["istagged"] = 1;
	var lfull_path = (lmap["full_path"].charAt(0) == ".") ? pcurpath
			+ lmap["full_path"].substr(1) : lmap["full_path"];
	var lendidx = pstart;
	var lidx;
	var lcur;
	var ldupka = 0;
	var lprev = lmap;
	var linlinecnt = 0;
	var lsameindex = pstart;
	var lconfirminlinecnt = 0;
	var lfindnextbreak = this.getNextforStylestoTags(pstart, pend, lfull_path,
			pdotpos);
	var ldupkapcurnodes, ldupkapcurpath, ldupkapdotpos, loldend;
	if (lfindnextbreak > 0) {
		ldupkapcurnodes = pcurnodes.slice(0, pdotpos + 1);
		ldupkapcurpath = pcurpath;
		ldupkapdotpos = pdotpos;
		loldend = pend;
		pend = lfindnextbreak - 1;

	}
	for (lidx = pstart + 1; lidx <= pend; lidx++) {
		var lcur = this.mstylemap.map[lidx];
		if ((lcur["start"] > lprev["end"] + 1) || (lcur["istagged"] == 1)) {
			if (lcur["start"] > lprev["end"] + 1) {
				ldupka = 1;
				// ~ alert("dupka");

			}
			break; // dupka ili veche tagnat t.e. pak dupka taka che spirame
		} else if (lcur["tmeplid"] == lmap["tmeplid"]) {
			if (!this.mTemplDets.mdata[lmap["tmeplid"]]["change_before"]
					|| ((lcur["tstart"] == lmap["tstart"]) && (lcur["tend"] == lmap["tend"]))) {
				lcur["istagged"] = 1;
				// ~ lconfirminlinecnt+=linlinecnt;
				lsameindex = lidx;
				lconfirminlinecnt++;
				// ~ linlinecnt=0;
				lendidx = lidx; // pak sme nie i si otbeliazvame kakto i na toz
				// otbeliazvame che shte go tagnem za bydeshti
				// rekursii da ne se vkluchva
			} else {
				ldupka = 2;
				break;
			}
		} else if ((!this.mTemplDets.mdata[lmap["tmeplid"]]["change_after"] || ((lmap["tstart"] <= lcur["tstart"]) && (lmap["tend"] >= lcur["tend"])))
				&& ((lcur["full_path"].charAt(0) == ".") || (!lcur["full_path"]
						.indexOf(lfull_path, 0)))) {
			var lptagarrlen = (this.mTemplDets.mdata[lcur["tmeplid"]]["parenttagarr"] ? this.mTemplDets.mdata[lcur["tmeplid"]]["parenttagarr"].length
					: 0);
			var lbreak = 0;
			if (this.mTemplDets.mdata[lcur["tmeplid"]]["change_after"]) {
				if (lcur["full_path"].charAt(0) == ".")
					lbreak = ((this.mTemplDets.mdata[lcur["tmeplid"]]["change_after"] - lptagarrlen) < 2) ? 0
							: 1;
				else {
					// if (!lpfp.indexOf(lcfp, 0) &&
					lbreak = (pdotpos + 1 < lptagarrlen)
							&& (pdotpos + 1 <= lptagarrlen
									- this.mTemplDets.mdata[lcur["tmeplid"]]["change_after"]) ? 0
							: 1;
				}
			}
			// ~ linlinecnt++;
			if (!lbreak) {
				lendidx = lidx;
				lconfirminlinecnt++; // relativen
			} else
				break;
		} else
			break; // ne e relativen spriamo nas taka che spirame
		lprev = lcur;
	}
	// pravim si novia node
	var lpararr = this.mTemplDets.mdata[lmap["tmeplid"]]["parenttagarr"];
	var lstate = 0;
	var lxmlNode;
	var lcurnodesidx = 0, lournodesidx = 0;
	var b = true;

	if (ldupka == 2) {
		// zapazvame za sled special dupkata
		ldupkapcurnodes = pcurnodes.slice(0, pdotpos + 1);
		ldupkapcurpath = pcurpath;
		ldupkapdotpos = pdotpos;
	}
	var lnodeadded = 0;
	var lwillcreatemax = (!lpararr ? 0 : lpararr.length) + 1;
	if (lmap["full_path"].charAt(0) == ".") {
		// ~ var b=true;
		if (pdotpos >= pcurnodes.length - 1)
			lstate = 1;
		else
			lcurnodesidx = pdotpos + 1;
		if (!lpararr || !lpararr.length) {
			b = false;
			if (!lstate) {
				// ~ pcurnodes.splice(pdotpos+1,pcurnodes.length-pdotpos-1);
				pcurnodes = pcurnodes.slice(0, pdotpos + 1);
				pcurpath = this.GenCurPath(pcurnodes);
			}
		}
	}

	while (b) {
		if (!lstate) {
			// ~ if ( this.mTemplDets.mdata[lcur["tmeplid"]]["change_after"] &&
			// this.mTemplDets.mdata[lcur["tmeplid"]]["change_after"] >
			// (lwillcreatemax - 1 - lournodesidx))
			if (pcurnodes[lcurnodesidx].markupTag.name == lpararr[lournodesidx]) {
				lcurnodesidx++;
				lournodesidx++;
				if (lournodesidx >= lpararr.length) {
					if (lcurnodesidx < pcurnodes.length) {
						// ~
						// pcurnodes.splice(lcurnodesidx,pcurnodes.length-lcurnodesidx);
						pcurnodes = pcurnodes.slice(0, lcurnodesidx);
						pdotpos = pcurnodes.length - 1;
						pcurpath = this.GenCurPath(pcurnodes);
					}
					break;
				}
				if (lcurnodesidx >= pcurnodes.length) {
					lstate = 1;
				}
			} else {
				// ~
				// pcurnodes.splice(lcurnodesidx,pcurnodes.length-lcurnodesidx);
				pcurnodes = pcurnodes.slice(0, lcurnodesidx);
				pdotpos = pcurnodes.length - 1;
				pcurpath = this.GenCurPath(pcurnodes);
				lstate = 1;
			}
		} else {
			// dobaviame
			// ~ var lpnode=pcurnodes[pcurnodes.length-1];
			// ~ var lnewptag=addcheckXMLTag(lpararr[lournodesidx]);
			// ~ var lnewpnode=lpnode.xmlElements.add(lnewptag);
			// ~ var
			// lnewpnode=this.doMarkup(pcurnodes[pcurnodes.length-1],lmap["start"],this.mstylemap.map[lendidx]["end"],lpararr[lournodesidx],1,lmap["cstart"],this.mstylemap.map[lendidx]["cend"]);
			var lnewpnode = this.doMarkupNew(pcurnodes[pcurnodes.length - 1],
					lmap["start"], this.mstylemap.map[lendidx]["end"],
					lpararr[lournodesidx], 1, lendidx);
			// ~ this.newNodeAdded(pstart,lendidx,lmap["start"],lnewpnode);
			lnodeadded = 1;
			pcurnodes.push(lnewpnode);
			lournodesidx++;
			if (lournodesidx >= lpararr.length)
				break;
		}
	}
	if ((this.mTemplDets.mdata[lmap["tmeplid"]]["new_parent"] > 0)
			&& (!lnodeadded)) {
		// ~ var l=this.mTemplDets.mdata[lmap["tmeplid"]]["new_parent"];
		// ~ var s=this.mTemplDets.mdata[lmap["tmeplid"]]["xmlnode"];
		// ~ var oldpar=pcurnodes[pcurnodes.length-1];
		// ~ var lpxmlNode=oldpar.parent.xmlElements.add(oldpar.markupTag);
		// ~ this.newNodeAdded(pstart,lendidx,lmap["start"],lpxmlNode);
		var lpxmlNode = this.doMarkupNew(pcurnodes[pcurnodes.length - 2],
				lmap["start"], this.mstylemap.map[lendidx]["end"],
				pcurnodes[pcurnodes.length - 1].markupTag.name, 1, lendidx);
		// ~ this.newNodeAdded(pstart,lendidx,lmap["start"],lpxmlNode);
		pcurnodes[pcurnodes.length - 1] = lpxmlNode;

	}
	lxmlNode = this.doMarkupNew(pcurnodes[pcurnodes.length - 1], lmap["start"],
			this.mstylemap.map[lendidx]["end"],
			this.mTemplDets.mdata[lmap["tmeplid"]]["xmlnode"], 1, lendidx);
	// ~ lxmlNode=this.CheckParent(lxmlNode,pcurnodes[pcurnodes.length-1]);
	pcurnodes.push(lxmlNode);
	// ~ this.newNodeAdded(pstart,lendidx,lmap["start"],lxmlNode);
	if (lconfirminlinecnt) {
		// ima vytre relativni puskame za tiah
		// ~ if (this.mTemplDets.mdata[lmap["tmeplid"]]["change_after"] ) {
		// ~
		// pcurnodes=pcurnodes.slice(0,pcurnodes.length-this.mTemplDets.mdata[lmap["tmeplid"]]["change_after"]);
		// ~ pdotpos=pcurnodes.length-1;
		// ~ lfull_path=this.GenCurPath(pcurnodes);
		// ~ }
		this.doStylestoTagsNew(pstart + 1, lendidx, pcurnodes, lfull_path,
				pcurnodes.length - 1);
	}
	// puskame ostavashtite da se tyrsiat
	if (ldupka == 1) {
		pcurnodes = [ this.m_RootOurNode ];
		pcurpath = "/" + this.mxmlel.markupTag.name;
		pdotpos = 0;
	} else if (ldupka == 2) {
		pcurnodes = ldupkapcurnodes;
		pcurpath = ldupkapcurpath;
		pdotpos = ldupkapdotpos;
	} else if ((this.mTemplDets.mdata[lmap["tmeplid"]]["change_after"])) { // &&
		// !lconfirminlinecnt
		var lcurnodes = pcurnodes.slice(0, pcurnodes.length
				- this.mTemplDets.mdata[lmap["tmeplid"]]["change_after"]);
		pdotpos = lcurnodes.length - 1;
		pcurpath = this.GenCurPath(lcurnodes);
	}
	this.doStylestoTagsNew(lendidx + 1, pend, pcurnodes, pcurpath, pdotpos);
	if (lfindnextbreak > 0) {
		if (lmap["full_path"].charAt(0) != ".") {
			ldupkapcurnodes = pcurnodes.slice(0, pcurnodes.length - 1
					- this.mTemplDets.mdata[lmap["tmeplid"]]["change_after"]);
			ldupkapdotpos = ldupkapcurnodes.length - 1;
			ldupkapcurpath = this.GenCurPath(ldupkapcurnodes);
		}
		this.doStylestoTagsNew(lfindnextbreak, loldend, ldupkapcurnodes,
				ldupkapcurpath, ldupkapdotpos);
	}
}

function ESM_doMarkupNew(pparent, pstart, pend, pnodename, pdocheck, pendidx) {
	var lourNode = new E_OurNode(pparent, pstart, pend, pnodename);
	return lourNode;
}

function ESM_RealMarkup(pnode, pdotag) {
	if (pdotag) {
		var i, sc, ec, lchistag;
		for (i = pnode.m_start; i <= pnode.m_end; i++) {
			sc = this.mxmlel.characters[i];
			try {
				lchistag = (sc.contents.charCodeAt(0) == 65279);
			} catch (e) {
				lchistag = false;
			}
			if (!lchistag)
				break;
		}
		if (i <= pnode.m_end) {
			pnode.m_start = i;

			for (i = pnode.m_end; i >= pnode.m_start; i--) {
				ec = this.mxmlel.characters[i];
				try {
					lchistag = (ec.contents.charCodeAt(0) == 65279);
				} catch (e) {
					lchistag = false;
				}
				if (!lchistag)
					break;
			}
			if (i >= pnode.m_start) {
				// ~ pnode.m_end=i;
				var myText = this.mxmlel.parentStory.texts.itemByRange(sc, ec);
				var lnewptag = addcheckXMLTag(pnode.m_nodename);
				var lxmlNode = pnode.m_parent.m_xmlnode.xmlElements
						.add(lnewptag);
				myText.markup(lxmlNode);
				pnode.m_xmlnode = lxmlNode;
				for ( var i = 0; i < pnode.m_childs.length; i++)
					pnode.m_childs[i].adddelta(1);
				pnode.adddeltaright(2);
				// ~ for (var
				// i=pnode.m_myindex+1;i<pnode.m_parent.m_childs.length; i++)
				// ~ pnode.m_parent.m_childs[i].adddelta(2);
			}
		}
	}
	for ( var i = 0; i < pnode.m_childs.length; i++)
		this.RealMarkup(pnode.m_childs[i], 1);
}

function E_OurNode(pparent, pstart, pend, pnodename /* , pstartidx, pendidx */) {
	this.add = EON_add;
	this.adddelta = EON_adddelta;
	this.adddeltaright = EON_adddeltaright;
	this.childadded = EON_childadded;
	this.m_parent = pparent;
	this.m_start = pstart;
	this.m_end = pend;
	this.m_nodename = pnodename;
	// ~ this.m_parent=CheckUndefined(pparent);
	// ~ this.m_start=CheckUndefined(pstart);
	// ~ this.m_end=CheckUndefined(pend);
	// ~ this.m_nodename=CheckUndefined(pnodename);
	this.markupTag = {
		"name" : this.m_nodename
	};
	// ~ this.m_startidx=CheckUndefined(pstartidx);
	// ~ this.m_endidx=CheckUndefined(pendidx);
	this.m_childs = [];
	this.m_xmlnode = null;
	this.m_myindex = null;
	if (this.m_parent)
		this.m_parent.add(this);
}

function EON_childadded(pchild) {
	if (this.m_start > pchild.m_start)
		this.m_start = pchild.m_start;
	if (this.m_end < pchild.m_end)
		this.m_end = pchild.m_end;
	if (this.m_parent)
		this.m_parent.childadded(pchild);
}
function EON_add(pchild) {
	this.childadded(pchild);
	pchild.m_myindex = this.m_childs.length;
	this.m_childs.push(pchild);
}
function EON_adddelta(ldelta) {
	this.m_start += ldelta;
	this.m_end += ldelta;
	for ( var i = 0; i < this.m_childs.length; i++)
		this.m_childs[i].adddelta(ldelta);
}
function EON_adddeltaright(ldelta) {
	if (this.m_parent) {
		for ( var i = this.m_myindex + 1; i < this.m_parent.m_childs.length; i++)
			this.m_parent.m_childs[i].adddelta(ldelta);
		this.m_parent.adddeltaright(ldelta);
	}
}

function E_ExtraObject(pText, pType, pTemplDets) {
	this.findGroup = EEO_findGroup;
	this.exportFig = EEO_exportFig;
	this.Tag = EEO_Tag;
	this.m_type = pType;
	this.m_text = pText;
	this.mTemplDets = pTemplDets;
	this.m_keyelement = this.findGroup(this.m_text);
	this.m_page = gDefaultPage;
	if (!this.m_keyelement) {
		this.m_keyelement = this.m_text.parentStory;
		this.m_x = gDefaultCoordinate;
		this.m_y = gDefaultCoordinate;
		this.m_is_group = false;
	} else {
		this.m_x = this.m_keyelement.geometricBounds[1];
		this.m_y = this.m_keyelement.geometricBounds[0];
		this.m_is_group = true;
	}
	this.m_parentStory = this.m_text.parentStory;
	this.m_xmlnodename = (this.m_type == 2 ? "fig" : "table-wrap");
	this.m_xmlnode = null;
	this.m_graphnode = null;
}
function EEO_findGroup(lel) {
	if (!lel || (typeof (lel) == "undefined"))
		return null;
	// ~ if (lel.groups && lel.groups.count()) return lel.groups.item(0);
	for ( var i = 0; i < lel.parentTextFrames.length; i++) {
		var lpel = lel.parentTextFrames[i].parent;
		if (lpel && (typeof (lpel) != "undefined")
				&& (lpel.constructor.name == "Group"))
			return lpel;

	}
	return null;
	// ~ if (lel.groups && lel.groups.length) return lel.groups[0]
	/*
	 * if (lel.constructor.name == "Group") return lel; else if
	 * (lel.constructor.name == "Document") return null; else return
	 * this.findGroup(lel.parent);
	 */
}
function EEO_Tag(pparent, pidx) {
	var lnewptag = addcheckXMLTag(this.m_xmlnodename);
	this.m_xmlnode = pparent.xmlElements.add(lnewptag);
	this.m_parentStory.markup(this.m_xmlnode);
	this.m_idx = pidx;
	if (this.m_type == 2) {
		addAttribute(this.m_xmlnode, "id", "F" + this.m_idx);
	}
	var lEST = new E_ParseStory(this.m_xmlnode, null);
	lEST.mTemplDets = this.mTemplDets;
	lEST.GenMap();
	lEST.StylestoTagsNew();
	if (this.m_type == 1) {
		if (this.m_parentStory.tables && this.m_parentStory.tables.count()) {
			var ltable = this.m_parentStory.tables.item(0);
			var ltabletag = addcheckXMLTag("table");
			var ltablenode = this.m_xmlnode.xmlElements.add(ltabletag);
			ltable.markup(ltablenode);
			addAttribute(ltablenode, "id", "T" + this.m_idx);
			addAttribute(ltablenode, "headerRowCount", ltable.headerRowCount);
			addAttribute(ltablenode, "bodyRowCount", ltable.bodyRowCount);
			addAttribute(ltablenode, "columnCount", ltable.columnCount);
		}
	} else {

	}
}
function EEO_exportFig(pPath, pFPrefix, pURLrefix, pIgnoreItem) {
	var res = null;
	if ((this.m_type == 2) && (this.m_keyelement.constructor.name == "Group")) {
		if (this.m_keyelement.allPageItems)
			for ( var i = 0; i < this.m_keyelement.allPageItems.length; i++) {
				var lpitem = this.m_keyelement.allPageItems[i];
				if (lpitem != pIgnoreItem
						&& ((typeof (lpitem.parentStory) == "undefined") || (lpitem.parentStory != pIgnoreItem))) {
					var ltablenode = selectSingleNodeinStructure("./graphic",
							this.m_xmlnode);
					if (!ltablenode) {
						var ltabletag = addcheckXMLTag("graphic");
						ltablenode = this.m_xmlnode.xmlElements.add(ltabletag);
					}
					res = pPath + pFPrefix + intNumberFormat(this.m_idx, 3) + ".jpg";
					addAttribute(ltablenode, "xlink:href", pURLrefix + pFPrefix
							+ intNumberFormat(this.m_idx, 3) + ".jpg");
					this.m_graphnode = ltablenode;
					this.m_origfname = pFPrefix + intNumberFormat(this.m_idx, 3)  + ".jpg";
					lpitem.exportFile(ExportFormat.JPG, File(res));
					break;
				}
			}
	}
	return res;
}


/*
   Връща стринг, който се състои от числото и необходимия брой нули
   пред него за да добие желаната минимална дължина в символи
    */
function intNumberFormat(pNumber, pMinLength){
    pNumber = parseInt(pNumber);
    var lResult = pNumber.toString();
    //Тук степента на 10 е с 1 по малка от бр на цифрите - > напр. числото има поне 1 цифра ако е по-голямо от 10^0, 2 - ако е по-голямо от 10^1
     for(var i = pMinLength; i >= 1; --i){
        if( pNumber >= MathPower(10, i - 1))
            return lResult;
        lResult = "0" + lResult;
     }
     return lResult;
 }
 
 /*
	Функция за вдигане на степен. Степента трябва да е цяло положително число
 */
 function MathPower(pNumber, pPower){
	 var lResult = 1; 
	 if( pPower <= 0 )
		return lResult;
	 for( i = 1; i <= pPower; ++i){
		lResult = lResult * pNumber;	
	 }
	 return lResult;
 }