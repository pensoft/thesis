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

var gxmlnd= myDoc.xmlElements.item(0);
for (var i=0;i<gxmlnd.xmlElements.count(); i++) {
	var litem=gxmlnd.xmlElements.item(i);
	if (litem.markupTag.name=="article") {
		var lEST=new E_ParseStory(litem, null);
		lEST.removeAllTags()
	}	
}