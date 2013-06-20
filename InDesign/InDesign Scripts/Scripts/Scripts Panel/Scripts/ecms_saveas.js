#target "InDesign"
app.scriptPreferences.enableRedraw = false;

var insaveas=1;
var funct_file = File(app.filePath + '/ecms/savecommon.js');
app.doScript(funct_file, ScriptLanguage.javascript);