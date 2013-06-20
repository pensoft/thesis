function CheckAVG(lstr) {
	FCK.EditorDocument.body.innerHTML = CleanWord(lstr);
	if (lstr.match(/AVG/gi) && lstr.match(/Virus/gi) && lstr.match(/Checked\ by/gi)) {
		alert("Remove AVG Footer!");
		return false;
	}
	
	if (lstr.match(/{/gi) || lstr.match(/}/gi)) {
		alert("Не е позволено използването на { и }!");
		return false;
	}	
	return true;
}

function TestForAVG(editorInstance) {
	var oStr = editorInstance.GetXHTML();
	return CheckAVG(oStr);
}

var oForm = FCK.LinkedField.form;
oForm.onsubmit = function () {
	return TestForAVG(FCK);
}