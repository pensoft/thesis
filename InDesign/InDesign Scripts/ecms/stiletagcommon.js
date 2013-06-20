app.scriptPreferences.enableRedraw = false;
var myDoc;
try {
	myDoc = app.activeDocument;
} catch(err) {
	alert( "There isn't Active Document");
}


// Vzimame funkciite
var funct_file = File(app.filePath + '/ecms/include.js');
app.doScript(funct_file, ScriptLanguage.javascript);

var funct_file = File(app.filePath + '/ecms/autotagtemplates.js');
app.doScript(funct_file, ScriptLanguage.javascript);

var cxml = null ;
var mylib = null;
var styletagdlg= null;
var d_tcombo= null;
var xmlRoot = null; 

var stres= 
	"dialog { \
		text:'Set "+(st_type==1 ? "Style to Tag":"Tag to Style")+" template', \
		allGroups: Panel { orientation:'stack', \
			col: Group { orientation: 'column', \
				row1: Group { orientation: 'row', visible:true, \
					s: StaticText { text:'Template:', size:[80,20] }, \
					tcombo: DropDownList { preferredSize:[300,20], alignment:'left' }, \
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
	xmlRoot= myDoc.xmlElements.item(0);
	var i;
	var tmplObj;
	mylib = loadLib();
			
	if (mylib) {	
		if (tmplObj=GetTemplates()) {
			styletagdlg = new Window(stres);
			styletagdlg.center();
			d_tcombo=styletagdlg.allGroups.col.row1.tcombo;
			if (!fillTemplateCombobyType(d_tcombo,st_type,tmplObj)) {
				var rr=0,mvalue=-1;
				while(!rr) {
					rr=styletagdlg.show();
					if (rr == 1) {
						mvalue=GetComboSelIndex(d_tcombo);
						if (!mvalue) {
							rr=0; 
							alert("You must select template first!");
						} else mvalue=getSelectedTemplateID(mvalue, st_type, tmplObj);
					}
				}
				if ((rr == 1) && (mvalue>=0)) {
					setTemplateID(mvalue, st_type);
				}
			}
		}
		mylib.ecmsFree();
	} 
}
