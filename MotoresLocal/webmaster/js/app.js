var segments = new Array();

function getXMLDoc(url) {

	if (window.XMLHttpRequest) {
		xmlhttp=new XMLHttpRequest();
	} else {
		xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
	}

	xmlhttp.open("GET", url, false);
	xmlhttp.send(null);

	return eval('(' + xmlhttp.responseText + ')');
}

function getXMLDocText(url) {

	if (window.XMLHttpRequest) {
		xmlhttp=new XMLHttpRequest();
	} else {
		xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
	}

	xmlhttp.open("GET", url, false);
	xmlhttp.send(null);

	return xmlhttp.responseText;
}

function postXMLDoc(url, params) {

	if (window.XMLHttpRequest) {
		xmlhttp=new XMLHttpRequest();
	} else {
		xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
	}

	xmlhttp.open("POST", url, false);
	xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xmlhttp.setRequestHeader("Content-length", params.length);
	xmlhttp.setRequestHeader("Connection", "close");
	xmlhttp.send(params);

	try {
		var result = eval('(' + xmlhttp.responseText + ')');
	} catch(e) {
		document.write(xmlhttp.responseText);
	}

	return result;
}
function postXMLDocText(url, params) {

	if (window.XMLHttpRequest) {
		xmlhttp=new XMLHttpRequest();
	} else {
		xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
	}

	xmlhttp.open("POST", url, false);
	xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xmlhttp.setRequestHeader("Content-length", params.length);
	xmlhttp.setRequestHeader("Connection", "close");
	xmlhttp.send(params);

	return xmlhttp.responseText ;
}
function avail_search() {
	segments.length=0;

	var date =  document.forms["avail_search_form"].elements["dateDest1"].value;
 	var dateDest = document.forms["avail_search_form"].elements["dateDest2"].value;
 	var source = document.forms["avail_search_form"].elements["source"].value.toUpperCase();
	var dest = document.forms["avail_search_form"].elements["dest"].value.toUpperCase();
	var direct = document.forms["avail_search_form"].elements["direct"].value;
	var cabin = document.forms["avail_search_form"].elements["cabin"].value;
	var carrier = document.forms["avail_search_form"].elements["carrier"].value;
	var adulto= document.forms["avail_search_form"].elements["adulto"].value;
	var bebe= document.forms["avail_search_form"].elements["bebe"].value;
	var mayor= document.forms["avail_search_form"].elements["mayor"].value;
	var nino= document.forms["avail_search_form"].elements["nino"].value;
	var typeDest= document.forms["avail_search_form"].elements["typeDest"].value;



	//hide('avail_search'); 
	hide('booking_search'); 
	show('avail_result'); 

	set_text('avail_result', getXMLDocText('../../Controller/AvailController.php?date=' + date +'&dateDest=' + dateDest + '&source=' + source + '&dest=' + dest + '&direct=' + direct + '&cabin=' + cabin +   '&carrier=' + carrier +   '&adulto=' + adulto +   '&bebe=' + bebe +   '&nino=' + nino +   '&mayor=' + mayor +   '&typeDest=' + typeDest));
	return false;
}



function check_itinerary() {

	var pnr = document.forms["check_itinerary_form"].elements["pnr"].value.toUpperCase();
	 
	set_text('checked_pnr', getXMLDocText('../../Controller/ItineraryController.php?&pnr=' + pnr));
 	
	return false;
}

function cancel_pnr() {
    
	var pnr = document.forms["cancel_form"].elements["pnr"].value.toUpperCase();
	set_text('cancelled_pnr', getXMLDocText('../../Controller/CancelController.php?&pnr=' + pnr));
	
	return false;
}



function book_itinerary(booking){

	hide('booking_search');
 	show('issue');
	set_text('issue_result', getXMLDocText('../../View/Issue/index.php?BookingID='+booking));

	return false;

}
function issue() {
 
	var BookingID = document.forms["issue_form"].elements["BookingID"].value;
	var PaymentType = document.forms["issue_form"].elements["PaymentType"].value;
	var CreditCardCode = document.forms["issue_form"].elements["CreditCardCode"].value;	
	var CreditCardNumber = document.forms["issue_form"].elements["CreditCardNumber"].value;
	var CreditSeriesCode = document.forms["issue_form"].elements["CreditSeriesCode"].value;
	var CreditExpireDate = document.forms["issue_form"].elements["CreditExpireDate"].value;
	var DebitCardCode = document.forms["issue_form"].elements["DebitCardCode"].value;
	var DebitCardNumber = document.forms["issue_form"].elements["DebitCardNumber"].value;
	var DebitSeriesCode = document.forms["issue_form"].elements["DebitSeriesCode"].value;
	var InvoiceCode = document.forms["issue_form"].elements["InvoiceCode"].value;
	var MiscellaneousCode = document.forms["issue_form"].elements["MiscellaneousCode"].value;
	var text = document.forms["issue_form"].elements["Text"].value;
	var VAT = document.forms["issue_form"].elements["VAT"].value;
	
	
	show('issue_result');

	hide('issue');
	set_text('issue_result', getXMLDocText('../../Controller/IssueController.php?BookingID=' + BookingID + '&PaymentType=' + PaymentType + 
		'&CreditCardCode=' + CreditCardCode + '&CreditCardNumber=' + CreditCardNumber + '&CreditSeriesCode=' + CreditSeriesCode + '&CreditExpireDate=' + CreditExpireDate +
		'&MiscellaneousCode=' + MiscellaneousCode + '&Text=' + text + '&VAT=' + VAT + 
		'&DebitCardCode=' + DebitCardCode + '&DebitCardNumber=' + DebitCardNumber + '&DebitSeriesCode=' + DebitSeriesCode + '&InvoiceCode=' + InvoiceCode
		));
	
	return false;
}

function add_segment(segment) {

	var i;
	for (i = 0 ; i<segment['SegmentsCount']; i++) {
		segments.push(segment['Segments'][i]);
		segments.push(segment['Isseu'][i]);

	}
}

function del_segment(segment) {
	segments.splice(segment,1);
}


function reservation(datos) {
	add_segment(datos);
	var text = '';
	var params = '';

	text = 'Segments=' + segments.length;
	for (segment in segments) {
		for (item in segments[segment]) {
			text = text + '&' + item + '_' + segment + '=' + segments[segment][item];
		}
	}
	alert(text);

	hide('avail_search'); 
	show('booking_search'); 
	hide('avail_result'); 
	//hide('avail_result'); 

	set_text('booking_search', postXMLDocText('../Book/index.php?', text + params));
	return false;

}

function booking_submit() {
	var text = '';
	var params = '';
	 
		text = 'Segments=' + segments.length;
		for (segment in segments) {

			for (item in segments[segment]) {
				text = text + '&' + item + '_' + segment + '=' + segments[segment][item];
			}
		}		
    var comprar = document.forms["booking_search_form"].elements["comprar"].value;
     

	if((segments[0]['PassengerTypeQuantityAdulto'])>0){
		for (var c = 0 ; c < segments[0]['PassengerTypeQuantityAdulto'] ; c++) {
    		params = params + '&FirstName'+c+'=' + document.forms["booking_search_form"].elements["FirstName"+c].value.toUpperCase();
			params = params + '&LastName'+c+'=' + document.forms["booking_search_form"].elements["LastName"+c].value.toUpperCase();
			params = params + '&DocType'+c+'=' + document.forms["booking_search_form"].elements["DocType"+c].value.toUpperCase();
			params = params + '&DocID'+c+'=' + document.forms["booking_search_form"].elements["DocID"+c].value.toUpperCase();
			params = params + '&Telephone'+c+'=' + document.forms["booking_search_form"].elements["Telephone"+c].value.toUpperCase();
			params = params + '&Email'+c+'=' + document.forms["booking_search_form"].elements["Email"+c].value.toUpperCase();
		}
	}	
	if((segments[0]['PassengerTypeQuantitybebe'])>0){
		for (var c = 0 ; c < segments[0]['PassengerTypeQuantitybebe'] ; c++) {
    		params = params + '&FirstNameInfante'+c+'=' + document.forms["booking_search_form"].elements["FirstNameInfante"+c].value.toUpperCase();
			params = params + '&LastNameInfante'+c+'=' + document.forms["booking_search_form"].elements["LastNameInfante"+c].value.toUpperCase();
			params = params + '&DocTypeInfante'+c+'=' + document.forms["booking_search_form"].elements["DocTypeInfante"+c].value.toUpperCase();
			params = params + '&DocIDInfante'+c+'=' + document.forms["booking_search_form"].elements["DocIDInfante"+c].value.toUpperCase();
			params = params + '&TelephoneInfante'+c+'=' + document.forms["booking_search_form"].elements["TelephoneInfante"+c].value.toUpperCase();
			params = params + '&EmailInfante'+c+'=' + document.forms["booking_search_form"].elements["EmailInfante"+c].value.toUpperCase();
		}
	}
	if((segments[0]['PassengerTypeQuantityNino'])>0){
		for (var c = 0 ; c < segments[0]['PassengerTypeQuantityNino'] ; c++) {
    		params = params + '&FirstNameNino'+c+'=' + document.forms["booking_search_form"].elements["FirstNameNino"+c].value.toUpperCase();
			params = params + '&LastNameNino'+c+'=' + document.forms["booking_search_form"].elements["LastNameNino"+c].value.toUpperCase();
			params = params + '&DocTypeNino'+c+'=' + document.forms["booking_search_form"].elements["DocTypeNino"+c].value.toUpperCase();
			params = params + '&DocIDNino'+c+'=' + document.forms["booking_search_form"].elements["DocIDNino"+c].value.toUpperCase();
			params = params + '&TelephoneNino'+c+'=' + document.forms["booking_search_form"].elements["TelephoneNino"+c].value.toUpperCase();
			params = params + '&EmailNino'+c+'=' + document.forms["booking_search_form"].elements["EmailNino"+c].value.toUpperCase();
		}
	}
	//alert(params);
	del_segment(segment);
	set_text('booking_search', postXMLDocText('../../Controller/BookController.php?', text + params+"&pago="+comprar));
//	hide('booking_search'); 
//	show('booking_result'); 
	return false;
}
function show(element) { 
	document.getElementById(element).style.display = 'block';
}

function hide(element) {
	 document.getElementById(element).style.display = 'none';
}

function set_text(element_id, text) {
	var e = document.getElementById(element_id);
	e.innerHTML = text;
}
