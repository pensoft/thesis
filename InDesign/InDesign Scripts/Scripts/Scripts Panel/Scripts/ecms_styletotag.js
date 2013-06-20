#target "InDesign"
app.scriptPreferences.enableRedraw = false;
var st_type=1;
//~ alert(app.filePath + '/ecms/stiletagcommon.js');
var funct_file = File(app.filePath + '/ecms/stiletagcommon.js');
app.doScript(funct_file, ScriptLanguage.javascript);