function openFilterPopUp(){
	var lPopUp = $('#docEditHeader .box .popup');
	var lIsVisible = lPopUp.is(':visible');
	if (lIsVisible == true)
		lPopUp.css("display", "none");
	else
		lPopUp.css("display", "block");
}

