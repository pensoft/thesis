#target "InDesign"
app.scriptPreferences.enableRedraw = false;
var st_type=2;
var funct_file = File(app.filePath + '/ecms/stiletagcommon.js');
app.doScript(funct_file, ScriptLanguage.javascript);