function addproduct(orderid, productid) {
	lQty = document.getElementById('product' + productid + 'qty');
	window.location = "./edit.php?tAction=addproduct&id=" + orderid + "&productid=" + productid + "&productqty=" + lQty.value;
}

function advfilter() {
	var afid = document.getElementById('advfilter');
	var afshowflag = document.getElementById('advfilter_showflag');
	var aflink = document.getElementById('aflink');
	var afdisplay = afid.style.display;
	
	if (afdisplay == 'none') {
		afshowflag.value = 1;
		afid.style.display = 'block';
		aflink.innerHTML = 'Разширено филтриране скрий';
	} else if (afdisplay == 'block') {
		afshowflag.value = 0;
		afid.style.display = 'none';
		aflink.innerHTML = 'Разширено филтриране покажи';
	}
}

function invoicerld(srcfld, rldfld) {
	var invoice = document.getElementById(srcfld);
	var isshowflag = document.getElementById('invoicestate_showflag');
	var invoice_num = document.getElementById(rldfld);
	var invoiceval = invoice.options[invoice.selectedIndex].value;

	if (invoiceval == 0 || invoiceval == -1) {
		isshowflag.value = 1;
		invoice_num.disabled = true;
	} else if (invoiceval == 1) {
		isshowflag.value = 0;
		invoice_num.disabled = false;
	}
}

function invoicerld2(srcfld, rldfld) {
	var invoice = document.getElementById(srcfld);
	var idshowflag = document.getElementById('invoicedet_showflag');
	var invoiceval = invoice.options[invoice.selectedIndex].value;

	if (invoiceval == 0) {
		idshowflag.value = 1;
		for(i = 0; i < rldfld.length; i++) {
			document.getElementById(rldfld[i]).disabled = true;
		}
	} else if (invoiceval == 1) {
		idshowflag.value = 0;
		for(i = 0; i < rldfld.length; i++) {
			document.getElementById(rldfld[i]).disabled = false;
		}
	}
}
