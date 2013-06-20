#target "InDesign"


var myDoc;
try {
	myDoc = app.activeDocument;
} catch(err) {
	alert( "There isn't Active Document");
}


app.scriptPreferences.enableRedraw = false;
// Vzimame funkciite
var funct_file = File(app.filePath + '/ecms/include.js');
app.doScript(funct_file, ScriptLanguage.javascript);

var cxml = null ;
var mylib = null;
var loaddlg= null;
var d_matctrl= null;
var d_authors= null;
var autors=null;
var articles=null;
var article_ECMS_ID=null;
var a_xml=null;
var xmlRoot = null; 
var myTextFrame=null;


var dres= 
	"dialog { \
		text:'Please choose article', \
		allGroups: Panel { orientation:'stack', \
			col: Group { orientation: 'column', \
				row1: Group { orientation: 'row', \
					s: StaticText { text:'Title and metadata:', size:[80,20] }, \
					tm: EditText { text:'', preferredSize:[300,20], alignment:'left' }, \
				}, \
				row2: Group { orientation: 'row', visible:true, \
					s: StaticText { text:'Content:', size:[80,20] }, \
					c: EditText { text:'', preferredSize:[300,20], alignment:'left' }, \
				}, \
				row3: Group { orientation: 'row', visible:true, \
					s: StaticText { text:'Author:', size:[80,20] }, \
					auth: DropDownList { preferredSize:[300,20], alignment:'left' }, \
				}, \
				row4: Group { orientation: 'row', visible:true, \
					s: StaticText { text:'Article:', size:[80,20] }, \
					art: ListBox {preferredSize:[300,100]}, \
				}, \
				row5: Group { orientation: 'row', visible:true, \
                        s: StaticText { text:' ', size:[80,20] }, \
					chkb: Checkbox {text:' Use seleted TextFrame as destination', value:false, alignment:'left', preferredSize:[300,20] }, \
				}, \
			} \
		}, \
		buttons: Group { orientation: 'row', alignment: 'right', \
			filterBtn: Button { text:'Filter'}, \
			okBtn: Button { text:'OK', properties:{name:'ok'} }, \
			cancelBtn: Button { text:'Cancel', properties:{name:'cancel'} } \
		} \
	}";

if (myDoc) {
	// Vzimame config-a
	cxml = getConfig();
	// Zarejdame bibliotekata
	mylib = loadLib();
	
	if (mylib) {		
		var pautors= new ECMS_Page(mylib,"/options/administration/usrs/");
		var l=pautors.Execute();
		autors=null;
		if (!l) {
			autors=pautors.GetObject(0);
			/*
			var str="";
			for (var i=0;i<autors.mcnt;i++) {
				str+="row: "+i+"\n";
				for(var j in autors.mdata[i]) str+=j+": "+autors.mdata[i][j]+" ; ";
				str+="\n";
			}
			alert(str);
			*/
			loaddlg = new Window(dres);
			loaddlg.center();
			d_authors=loaddlg.allGroups.col.row3.auth;
			d_authors.removeAll();
			d_authors.add("item", "---");
			d_authors.items[0].selected=true;
			for(l=0;l<autors.mcnt;l++)
				d_authors.add("item", autors.mdata[l]["name"]);
			d_matctrl=loaddlg.allGroups.col.row4.art;
			d_matctrl.removeAll();
			loaddlg.buttons.filterBtn.onClick=filterBtnClick;
			d_matctrl.onDoubleClick=matctrlDoubleClick;
			var rr=0,mvalue=-1;
			while(!rr) {
				rr=loaddlg.show();
				if (rr == 1) {
					mvalue=GetComboSelIndex(d_matctrl);
					if (mvalue === null) {
						rr=0; 
						alert("You must select article first!");
					} else {
						if (loaddlg.allGroups.col.row5.chkb.value) myTextFrame=getSelectedTextFrame();
					}
				}
			}
			if (rr == 1) {
				var selarticle= new ECMS_Page(mylib,"/resources/articles/export.php");
				var params={"id": 0, "type": 1};
				article_ECMS_ID=params["id"]=articles.mdata[mvalue]["id"];
				selarticle.SetParams(params);
				selarticle.m_flags=0;
				if (!selarticle.ExecuteRawData()) {
					var oldset=XML.defaultSettings ();
					var oldwhite=oldset.ignoreWhitespace;
					oldset.ignoreWhitespace=false;
					XML.setSettings(oldset);
					a_xml = new XML(selarticle.m_res.replace(/(\n)/g,"\r"));
					oldset.ignoreWhitespace=oldwhite;
					XML.setSettings(oldset);
					if (!a_xml) alert("Server returned invalid XML content!");
				} else alert("Unknown error!");
			}
		}
	
	
		mylib.ecmsFree();
		if (a_xml) {
			//alert(a_xml.toXMLString());
			var name=a_xml.localName();
			var tc=myDoc.xmlTags.count();
			//~ for(var i=0;i<tc;i++)
				//~ if (myDoc.xmlTags.item(i).name=="Root") myDoc.xmlTags.item(i).name=name;
			//LoadEPMTTags();
			xmlRoot= myDoc.xmlElements.item(0);
		
			//~ while(xmlRoot.xmlElements && (xmlRoot.xmlElements.length > 0) && xmlRoot.xmlElements.item(0)) xmlRoot.xmlElements.item(0).remove();
			addcheckXMLTag(name);
			var artroot=xmlRoot.xmlElements.add(name);
			if (!myTextFrame) {
				myTextFrame = myDoc.pages.item(0).textFrames.add();
				myTextFrame.geometricBounds = getTextBounds();
			}
			myplaceXML(artroot,a_xml,myTextFrame);
			addAttribute(artroot, "ecms_id",article_ECMS_ID); 
			myTextFrame.placeXML(artroot);
			//myDoc.xmlElements.add(name);
			//xmlRoot.setLocalName(name);
			//myDoc.xmlElements.item(0).remove();
			//~ alert(myTextFrame.contents);
		}
	}
}

function filterBtnClick() {
	var articlespage= new ECMS_Page(mylib,"/resources/articles/");
	var params={"title":"", "content":"", "createuid":"",	"tAction":"Filter", "kfor_name":"def1"};
	params["title"]=loaddlg.allGroups.col.row1.tm.text;
	params["content"]=loaddlg.allGroups.col.row2.c.text;
	var comboindex=GetComboSelIndex(d_authors);
	if (comboindex>0) params["createuid"]=autors.mdata[comboindex-1]["id"];
	articlespage.SetParams(params);
	//alert(articles.m_url);
	//loaddlg.allGroups.col.row1.tm.text=articles.m_url;
	d_matctrl.removeAll();
	var l=articlespage.Execute();
	if (!l) {
		articles=articlespage.GetObject(1);
		for(l=0;l<articles.mcnt;l++)
				d_matctrl.add("item", articles.mdata[l]["title"]);
	} else articles=null;
}
function getTextBounds() {
	var pasteboard = getPasteBoard(myDoc, "left");
	var placeCorrector=0;
	var iCorrector=0;
	var i=0;
	var x1 = Math.round(pasteboard[1] + (placeCorrector * 100));
	var x2 = Math.round(x1 + 100);
	var y1 = Math.round(pasteboard[0] + ((i - iCorrector) * 40));
	var y2 = Math.round(y1 + 25);
	
	if (x2 > pasteboard[2]) {
		x2 = pasteboard[2];
		x1 = x2 - 100;
	}
	return [y1,x1,y2,x2];
}
function myplaceXML(pStructNode,pxmlNode,pTextFrame) {
	//pStructNode.xmlAttributes.add("part_number", myElement.texts.item(0).contents);
	//var attr=pxmlNode.children();
	var attr=pxmlNode.attributes();
	var f=0;
	for(var i=0;i<attr.length(); i++) {
		var attname=attr[i].localName();
		var attval=attr[i].toString();
		f=addAttribute(pStructNode, attname,attval);
		
		//~ alert(i+": "+	attr.child(i).localName()+"-"+attr.child(i).toString());
		if (f) alert(pxmlNode.localName()+"?"+i+": "+f+": "+	attname+"-"+attval);
		}
		//~ if (f) {
			//~ var s=": "+i+":";
			//~ for(var j in attr)
				//~ s+=j+"--"+attr[j];
			//~ alert(s);
			//~ }
		//~ pStructNode.xmlAttributes.add(attr.child(i).localName(),attr.child(i).toString());
		//return;
	var lnodes=pxmlNode.children();
	for(var i=0;i<lnodes.length(); i++) {
		var lname=pxmlNode.child(i).localName();
		//~ if (!lname) pStructNode.contents+=pxmlNode.child(i).toString();
		if (!lname) pStructNode.insertTextAsContent(pxmlNode.child(i).toString(),XMLElementPosition.ELEMENT_END);
		else {
			addcheckXMLTag(lname);
			var myXMLChild = null;
			try {
			myXMLChild = pStructNode.xmlElements.add(lname);
			} catch (err) {
				alert("Error in myplaceXML: "+lname+":"+pStructNode.markupTag.name);
			}
			//myXMLChild.contents=attr.child(i).toString();
			if (myXMLChild) {
				myplaceXML(myXMLChild,pxmlNode.child(i),pTextFrame);
			} 
		}
		//alert("!"+lname+"!");
	}
	switch(pStructNode.markupTag.name){
		case "bold" : 
				apllyFormatting(pStructNode, {"fontStyle": "Bold"});
				break;
		case "italic" : 
				apllyFormatting(pStructNode, {"fontStyle": "Italic"});
				break;
		case "underline" : 
				apllyFormatting(pStructNode, {"underline": true});
				break;
		}
}

//zavbydeshte sekvo
function apllyFormatting(lxmlNode, lformatings) {
	var c=lxmlNode.texts.count();
	//~ alert(c+": "+lxmlNode.markupTag.name+" - "+lxmlNode.contents);
	for (var i=0; i<c;i++) {
		for (var j in lformatings)
			lxmlNode.texts.item(i)[j]=lformatings[j];
	}
}

function matctrlDoubleClick() {
	//~ loaddlg.okBtn.notify();
	//~ loaddlg.okBtn.onClick();
	loaddlg.close(1);
	//~ alert("OK");
}
function getSelectedTextFrame() {
	if (app.selection.length == 1){
   //Evaluate the selection based on its type.
		switch (app.selection[0].constructor.name){
		case "InsertionPoint":
		case "Character":
		case "Word":
		case "TextStyleRange":
		case "Line":
		case "Paragraph":
		case "TextColumn":
		case "Text":
		case "Story":
		  //The object is a text object; pass it on to a function.
		  return app.selection[0];
		  break;
		//In addition to checking for the above text objects, we can
		//also continue if the selection is a text frame selected with
		//the Selection tool or the Direct Selection tool.
		case "TextFrame":
		  //If the selection is a text frame, get a reference to the
		  //text in the text frame.
		  return app.selection[0];
		  break;
		default:
		  break;
		}
	}
	return null;
	
}