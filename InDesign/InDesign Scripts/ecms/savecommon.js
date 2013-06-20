var myDoc;
try {
	myDoc = app.activeDocument;
} catch (err) {
	alert("There isn't Active Document");
}

app.scriptPreferences.enableRedraw = false;
// Vzimame funkciite
var funct_file = File(app.filePath + '/ecms/include.js');
app.doScript(funct_file, ScriptLanguage.javascript);

var cxml = null;
var mylib = null;
var loaddlg = null;
var d_artctrl = null;
var d_title = null;
var articles = null;
var articlesarr = [];
var article_ECMS_ID = null;
var a_xml = null;
var xmlRoot = null;
var xmlfname = null;
// ~ alert(insaveas);

var dres = "dialog { \
		text:'Please choose article to save"
		+ (insaveas ? "As" : "")
		+ " in CMS', \
		allGroups: Panel { orientation:'stack', \
			col: Group { orientation: 'column', \
				row1: Group { orientation: 'row', visible:true, \
					s: StaticText { text:'Article:', size:[80,20] }, \
					art: ListBox {preferredSize:[300,100]}, \
				}, \
				row2: Group { orientation: 'row', \
					s: StaticText { text:'Title:', size:[80,20] }, \
					tm: EditText { text:'', preferredSize:[300,20], alignment:'left' }, \
				}, \
                  row3: Group { orientation: 'row', \
					s: StaticText { text:'Journal:', size:[80,20] }, \
					journal: DropDownList { text:'', preferredSize:[300,20], alignment:'left' }, \
				}, \
			} \
		}, \
		buttons: Group { orientation: 'row', alignment: 'right', \
			okBtn: Button { text:'OK', properties:{name:'ok'} }, \
			cancelBtn: Button { text:'Cancel', properties:{name:'cancel'} } \
		} \
	}";

if (myDoc) {
	// Vzimame config-a
	cxml = getConfig();
	xmlRoot = myDoc.xmlElements.item(0);
	// Zarejdame bibliotekata
	mylib = loadLib();
	var i;
	for (i = 0; i < xmlRoot.xmlElements.count(); i++)
		if (xmlRoot.xmlElements.item(i).markupTag.name == "article") {
			addArticle(xmlRoot.xmlElements.item(i));
		}
	var lJournalsPage = new ECMS_Page(mylib, "/dict/journals/");
	var lJournalsExecute = lJournalsPage.Execute();
	if (articlesarr.length && !lJournalsExecute && mylib) {
		loaddlg = new Window(dres);
		loaddlg.center();
		d_artctrl = loaddlg.allGroups.col.row1.art;
		d_artctrl.removeAll();
		d_artctrl.onChange = changeTitle;
		d_title = loaddlg.allGroups.col.row2.tm;
		for (i = 0; i < articlesarr.length; i++)
			d_artctrl.add("item", articlesarr[i].txt);
		// ~ alert("a"+articlesarr[i].ecms_id+": "+articlesarr[i].txt);

		// Load Journals
		var lJournals = lJournalsPage.GetObject(0);
		var lJournalDialogList = loaddlg.allGroups.col.row3.journal;
		lJournalDialogList.removeAll();
		lJournalDialogList.add("item", "---");
		lJournalDialogList.items[0].selected = true;
		for (l = 0; l < lJournals.mcnt; l++)
			lJournalDialogList.add("item", lJournals.mdata[l]["name"]);

		var rr = 0, mvalue = -1, lJournalIdx = -1, lJournalId = 0;
		while (!rr) {
			rr = loaddlg.show();
			if (rr == 1) {
				mvalue = GetComboSelIndex(d_artctrl);
				lJournalIdx = GetComboSelIndex(lJournalDialogList);
				if (mvalue === null) {
					rr = 0;
					alert("You must select article first!");
				} else {
					if (lJournalIdx !== null && lJournalIdx >= 1 && lJournalIdx - 1 < lJournals.mcnt) {// Indexite
						// sa s -1
						// nazad
						// zaradi
						// prazniq
						// red ---
						lJournalId = lJournals.mdata[lJournalIdx - 1]["id"];
					}
					if (!lJournalId || lJournalId <= 0) {
						alert("You must select journal!");
						rr = 0;
					}
				}
			}
		}

		if (rr == 1) {
			var el = articlesarr[mvalue].el;
			var att;
			if (articlesarr[mvalue].ecms_id)
				el.xmlAttributes.itemByName('ecms_id').remove();
			// ~ if (!(att=el.xmlAttributes.item("xmlns:mml")) ||
			// (typeof(att)=="undefined")) el.xmlAttributes.add("xmlns:mml",
			// "http://www.w3.org/1998/Math/MathML");
			// ~ else {
			// ~ alert(att);
			// ~ alert(att.name);
			// ~ alert(att.value);
			// ~ alert(typeof(att));
			// ~ }
			try {
				el.xmlAttributes.add("xmlns:mml", "http://www.w3.org/1998/Math/MathML");
			} catch (e) {
			}
			;
			try {
				el.xmlAttributes.add("xmlns:xlink", "http://www.w3.org/1999/xlink");
			} catch (e) {
			}
			;
			try {
				el.xmlAttributes.add("xmlns:xsi", "http://www.w3.org/2001/XMLSchema-instance");
			} catch (e) {
			}
			;
			try {
				el.xmlAttributes.add("xmlns:tp", "http://www.plazi.org/taxpub");
			} catch (e) {
			}
			;
			var lEST = new E_ParseStory(el, null);
			lEST.addBIUTags();
			var lhavefigstables = null;
			var lfigobjs = [];
			for (i = 0; i < xmlRoot.xmlElements.count(); i++) {
				var litem = xmlRoot.xmlElements.item(i);
				if ((litem.markupTag.name == "article_figs_and_tables")
						&& (getAttributeValue(litem, 'article_pos') == mvalue + 1)) {
					// ~ myDoc.select(litem);
					lhavefigstables = litem;
					lEST.addBIUTagsAFT(litem);
					var lglobal = [];

					var lfigprefix1 = (cxml.xpath("/config/jpegprefix").toString());
					for ( var j = 0; j < litem.xmlElements.count(); j++) {
						var litem1 = litem.xmlElements.item(j);
						if (litem1.markupTag.name == "fig") {
							if (litem1.characters && litem1.characters.count()) {
								var lpi = litem1.parentStory;
								var lfigprefix = "";
								var lnewobj = new E_ExtraObject(litem1.characters.item(0), 2, null);
								var lfiga = getAttributeValue(litem1, "id");
								if (lfiga)
									lnewobj.m_idx = lfiga.substr(1);
								lnewobj.m_xmlnode = litem1;
								// ~
								// "/Root/article/front/journal-meta/journal-id"
								if (!lglobal.length) {
									var lgpaths=["./front/article-meta/ignore_tag/journal-id", "./front/article-meta/ignore_tag/issue", "./front/article-meta/ignore_tag/fpage" ];
									for ( var s = 0; s < lgpaths.length; s++) {
										var lnode = selectSingleNodeinStructure(lgpaths[s], el);
                                             if(lnode){
                                                 var lSuffix = getStructNodeClearContents(lnode);
                                                 if( s > 0 ){//Issue-то и fpage-а трябва да са с мин дължина от 3 символа
                                                        lSuffix = intNumberFormat(lSuffix, 3);
                                                  }
                                                lfigprefix += lSuffix ;
                                             }else{//Ако няма такъв възел - добавяме _ 
                                                 lfigprefix += "_";
                                             }
                                             lfigprefix += "-";
									}
                                         //Добавяме g преди индекса на картинката   
                                         lfigprefix += "g";
									app.jpegExportPreferences.exportResolution = 300;
									app.jpegExportPreferences.jpegQuality = JPEGOptionsQuality.MAXIMUM;
									app.jpegExportPreferences.jpegRenderingStyle = JPEGOptionsFormat.BASELINE_ENCODING;
									JPEGExportPreference.antiAlias = true;
									JPEGExportPreference.jpegColorSpace = JpegColorSpaceEnum.RGB;
								}
								var figfname = lnewobj.exportFig((cxml.xpath("/config/dirs/tempdir").toString()),
										lfigprefix, lfigprefix1, lpi);
								lnewobj.figfname = figfname;
								lfigobjs.push(lnewobj);
								// ~ var pupl= new
								// ECMS_Page(mylib,"/resources/articles/upload_article_picture.php");
								// ~ var
								// params={"article_id":larticleid,"tAction":"Save",
								// "kfor_name":"def1"};
								// ~ pupl.SetParams(params);
								// ~ pupl.m_multipart.fieldname='imgfile';
								// ~ pupl.m_multipart.ctype="image/jpeg";
								// ~
								// pupl.m_multipart.filename=getAttributeValue(lnewobj.m_graphnode,
								// "href");
								// ~ pupl.m_multipart.realFile=figfname;
								// ~ var l=pupl.Execute(true);
								// ~ if (l)
								// ~ alert("Error while uploading: "+figfname);
								// ~ else alert("Successful upload: "+figfname);
							} else
								alert("Unassociated fig tag!!!");

						}
					}
					break;
				}
			}
			var lReplacement = changeBeforeExport();
			var xmlfnameft = null;
			myDoc.select(el);
			myDoc.xmlExportPreferences.exportFromSelected = true;
			myDoc.xmlExportPreferences.characterReferences = true;
			// ~ myDoc.xmlExportPreferences.fileEncoding=UTF8;
			xmlfname = (cxml.xpath("/config/dirs/tempdir").toString()) + myDoc.name.replace(/.indd/, "") + ".xml";
			myDoc.exportFile(ExportFormat.xml, File(xmlfname));
			if (lhavefigstables) {
				myDoc.select(lhavefigstables);
				xmlfnameft = (cxml.xpath("/config/dirs/tempdir").toString()) + "article_figs_and_tables__"
						+ myDoc.name.replace(/.indd/, "") + ".xml";
				myDoc.exportFile(ExportFormat.xml, File(xmlfnameft));
			}

			myDoc.xmlExportPreferences.exportFromSelected = false;
			if (lReplacement)
				myDoc.undo();
			lEST.removeBIUTags();
			if (lhavefigstables)
				lEST.removeBIUTagsAFT(lhavefigstables);

			if (mylib) {
				var psave = new ECMS_Page(mylib, "/resources/articles/edit.php");
				var params = {
					"title" : "",
					"id" : "",
					"tAction" : "save",
					"kfor_name" : "def1",
					"xml_sync_template_id" : "1",
					"replace_text_content" : "1",
					"journal_id" : "",
					"overwrite_journal_info" : 1
				};
				params["title"] = d_title.text;
				params["journal_id"] = lJournalId;
				if (!insaveas)
					params["id"] = articlesarr[mvalue].ecms_id;
				psave.SetParams(params);
				psave.m_multipart.filename = myDoc.name.replace(/.indd/, "") + ".xml";
				psave.m_multipart.realFile = xmlfname;
				var l = psave.Execute(true);
				if (!l) {
					var newarticle = psave.GetObject(0);
					if (!newarticle.mcnt)
						alert("Unknown error!");
					else {
						articlesarr[mvalue].ecms_id = newarticle.mdata[0]["id"];
						if (lhavefigstables) {
							var psave1 = new ECMS_Page(mylib, "/resources/articles/save_article_figures.php");
							var params1 = {
								"tAction" : "save",
								"kfor_name" : "def1",
								"article_id" : articlesarr[mvalue].ecms_id
							};
							// ~ var laft_file = new File(xmlfname);
							// ~ alert(xmlfname);
							// ~ laft_file.open("r", undefined, undefined);
							// ~ alert(laft_file.read());
							// ~ //params1["article_figs"]=laft_file.read();
							// ~ laft_file.close();
							psave1.SetParams(params1);
							psave1.m_multipart.filename = myDoc.name.replace(/.indd/, "") + ".xml";
							psave1.m_multipart.realFile = xmlfnameft;
							var l = psave1.Execute(true);
							if (!l) {
								var newarticle = psave1.GetObject(0);
								if (!newarticle.mcnt)
									alert("Unknown error while save article_figs_and_tables!");
								else {
									var pdel = new ECMS_Page(mylib, "/resources/articles/save_article_pictures.php");
									var params = {
										"article_id" : articlesarr[mvalue].ecms_id,
										"tAction" : "save",
										"kfor_name" : "def1"
									};
									pdel.SetParams(params);
									// ~
									// psave.m_multipart.filename=myDoc.name.replace(/.indd/,
									// "") + ".xml";
									// ~ psave.m_multipart.realFile=xmlfname;
									var l = pdel.Execute();
									for ( var s = 0; s < lfigobjs.length; s++) {
										var pupl = new ECMS_Page(mylib,
												"/resources/articles/upload_article_picture.php");
										var params = {
											"article_id" : articlesarr[mvalue].ecms_id,
											"tAction" : "save",
											"kfor_name" : "def1"
										};
										pupl.SetParams(params);
										pupl.m_multipart.fieldname = 'imgfile';
										pupl.m_multipart.ctype = "image/jpeg";
										pupl.m_multipart.filename = lfigobjs[s].m_origfname;
										pupl.m_multipart.realFile = lfigobjs[s].figfname;
										var l = pupl.Execute(true);
										if (l)
											alert("Error while uploading: " + lfigobjs[s].figfname);
									}
								}
							}
						}
					}
				}

				mylib.ecmsFree();
			}
			if (articlesarr[mvalue].ecms_id)
				el.xmlAttributes.add("ecms_id", articlesarr[mvalue].ecms_id);

		}
	} else
		alert("Error! You must have atleast one article node under root node!");
}
// ecms_multipartform_internal("/resources/articles/edit.php?tAction=Save&title=4444&kfor_name=def1&id=12",
// 1, "xmlfile", "1.xml", "/work/tests/ecmsconnect/1.xml", "text/xml");

function addArticle(pel) {
	var larticle = {};
	var attr = null;
	larticle.el = pel;
	larticle.ecms_id = getAttributeValue(pel, 'ecms_id');
	var llength = 30;
	var lcur = 0;
	larticle.txt = "";
	while ((lcur < llength) && (lcur < larticle.el.characters.count())) {
		var lch = larticle.el.characters[lcur].contents;
		var b = false;
		try {
			b = (lch.charCodeAt(0) == 65279);
		} catch (e) {
			b = true;
		}
		if (b)
			llength++;
		else
			larticle.txt += lch;
		lcur++;
	}
	// ~ larticle.txt=larticle.el.contents.substr(0,29);
	// ~ larticle.txt=larticle.el.texts.item(0).contents.substr(0,32);
	larticle.txt = larticle.txt.replace(/\r/gmi, ' ');
	larticle.txt = larticle.txt.replace(/^\s+|\s+$/g, ' ') + "...";
	if (insaveas || larticle.ecms_id)
		articlesarr.push(larticle);
}

function changeTitle() {
	var mvalue = GetComboSelIndex(d_artctrl);
	d_title.text = articlesarr[mvalue].txt;
}

function changeBeforeExport() {
	app.findTextPreferences = NothingEnum.nothing;
	app.changeTextPreferences = NothingEnum.nothing;
	app.findChangeTextOptions.caseSensitive = false;
	app.findChangeTextOptions.includeFootnotes = false;
	app.findChangeTextOptions.includeHiddenLayers = false;
	app.findChangeTextOptions.includeLockedLayersForFind = false;
	app.findChangeTextOptions.includeLockedStoriesForFind = false;
	app.findChangeTextOptions.includeMasterPages = false;
	app.findChangeTextOptions.wholeWord = false;
	app.findTextPreferences.findWhat = "\r";
	var lSearchResult = myDoc.findText();

	if (lSearchResult && lSearchResult.length) {
		app.changeTextPreferences.changeTo = "<br/>\r";
		myDoc.changeText();
		return 1;
	} else
		return 0;
}
