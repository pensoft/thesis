function openFilterPopUp(){
	var lPopUp = $('#docEditHeader .box .popup');
	var lIsVisible = lPopUp.is(':visible');
	if (lIsVisible == true)
		lPopUp.css("display", "none");
	else
		lPopUp.css("display", "block");
}

function DownloadMaterialsAsCSV(pInstanceId) {
	document.location.href = '/lib/ajax_srv/csv_export_srv.php?action=export_materials_as_csv&instance_id=' + pInstanceId;
	return;
}

