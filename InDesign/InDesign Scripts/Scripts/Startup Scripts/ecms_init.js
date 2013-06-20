#target "InDesign"
#targetengine "session"

app.scriptPreferences.enableRedraw = false;
//var mylib = new ExternalObject ("lib:eds.2.0.dll"); // load the library
var ScriptPath = Folder.startup + "/Scripts/Scripts%20Panel/Scripts/";
var myMenuName = "ECMS";
var mySubMenusBG = {};
var mySubMenus = [ "Load from CMS", "|","Save in CMS", "Save As in CMS", "|", "Set Style to Tag Template", "Set Tag to Style Template", "|", "Remove all Tags", "|", "About"];


var myHandlers = {
	'Save in CMS' : function(e) {
		var myJavaScript = File(ScriptPath + "ecms_save.js");
		try {
			app.doScript(myJavaScript, ScriptLanguage.javascript);
		} catch (err) {
			ErrorReport(mylib, "Не може да зареди файла " + myJavaScript.fsName + "... ERROR: " + err);
		}
	},
	'Save As in CMS' : function(e) {
		var myJavaScript = File(ScriptPath + "ecms_saveas.js");
		try {
			app.doScript(myJavaScript, ScriptLanguage.javascript);
		} catch (err) {
			ErrorReport(mylib, "Не може да зареди файла " + myJavaScript.fsName + "... ERROR: " + err);
		}
	},
	'Load from CMS' : function(e) {
		var myJavaScript = File(ScriptPath + "ecms_load.js");
		try {
			app.doScript(myJavaScript, ScriptLanguage.javascript);
		} catch (err) {
			ErrorReport(mylib, "Не може да зареди файла " + myJavaScript.fsName + "... ERROR: " + err);
		}
	},
	'Set Style to Tag Template' : function(e) {
		var myJavaScript = File(ScriptPath + "ecms_styletotag.js");
		try {
			app.doScript(myJavaScript, ScriptLanguage.javascript);
		} catch (err) {
			ErrorReport(mylib, "Не може да зареди файла " + myJavaScript.fsName + "... ERROR: " + err);
		}
	},
	'Set Tag to Style Template' : function(e) {
		var myJavaScript = File(ScriptPath + "ecms_tagtostyle.js");
		try {
			app.doScript(myJavaScript, ScriptLanguage.javascript);
		} catch (err) {
			ErrorReport(mylib, "Не може да зареди файла " + myJavaScript.fsName + "... ERROR: " + err);
		}
	},
	'Remove all Tags' : function(e) {
		var myJavaScript = File(ScriptPath + "ecms_removealltags.js");
		try {
			app.doScript(myJavaScript, ScriptLanguage.javascript);
		} catch (err) {
			ErrorReport(mylib, "Не може да зареди файла " + myJavaScript.fsName + "... ERROR: " + err);
		}
	},
	'About' : function(e) {
		res = 
		"dialog { \
			text:'About', \
			allGroups: Group { orientation:'column', \
				grp1: Panel { orientation: 'row', alignment: 'fill', \
					col: Group { orientation: 'column', \
						row1: Group { orientation: 'row', alignment: 'left' , \
							s: StaticText { text:'version 1.0' }, \
						}, \
						row2: Group { orientation: 'row', alignment: 'left' , \
							s: StaticText { text:'© Etaligent.Net' }, \
						}, \
						row3: Group { orientation: 'row', alignment: 'left' , \
							s: StaticText { text:'София 1000, ул. Цар Шишман 17' }, \
						}, \
						row4: Group { orientation: 'row', alignment: 'left' , \
							s: StaticText { text:'тел.: +359 29376 220, факс: +359 29376 230, e-mail: office@etaligent.net' }, \
						}, \
					}, \
				}, \
				grp2: Panel { orientation: 'row', alignment: 'fill', text:'Екип', \
					col: Group { orientation: 'column', \
						row1: Group { orientation: 'row', alignment: 'left' , \
							s: StaticText { text:'Александър Починков <alexander@etaligent.net>' }, \
						}, \
						row2: Group { orientation: 'row', alignment: 'left' , \
							s: StaticText { text:'Петър Гешев <peterg@etaligent.net>'}, \
						}, \
						row3: Group { orientation: 'row', alignment: 'left' , \
							s: StaticText { text: 'Иван Тренков <trenkovi@etaligent.net>' }, \
						}, \
					}, \
				} \
			}, \
			buttons: Group { orientation: 'row', alignment: 'right', \
				okBtn: Button { text:'OK', properties:{name:'ok'} }, \
			} \
		}";
		
		var w = new Window(res);
		w.center();
		w.show();
	}
};

addEdsMenus();

/*
   Function: addEdsMenus

   Добавя менютата и подменютата към индизайн.
*/
function addEdsMenus() {
	try {
		app.menus.item("$ID/Main").submenus.item(myMenuName).remove();
	} catch (err){
		// ...
	}
	
	var myEDSMenu = app.menus.item("$ID/Main").submenus.add(myMenuName);
	var smObj = {};
	
	for (var i = 0; i < mySubMenus.length; i ++) {
		if (mySubMenus[i] == "|") {
			// Separator
			myEDSMenu.menuSeparators.add();
		} else {
			smObj[mySubMenus[i] + "_action"] = app.scriptMenuActions.add((typeof mySubMenusBG[mySubMenus[i]] != 'undefined' ? mySubMenusBG[mySubMenus[i]] : mySubMenus[i]));
			if (myHandlers[mySubMenus[i]]) {
				smObj[mySubMenus[i] + "_listener"] = smObj[mySubMenus[i] + "_action"].eventListeners.add("onInvoke", myHandlers[mySubMenus[i]]);
			} else {
				smObj[mySubMenus[i] + "_listener"] = smObj[mySubMenus[i] + "_action"].eventListeners.add("onInvoke", function(){alert("Not implemented yet!!!.");});
			}
			myEDSMenu.menuItems.add(smObj[mySubMenus[i] + "_action"]);
		}
	}
}