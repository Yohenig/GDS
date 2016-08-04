<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
   "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>Busqueda de Boletos</title>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-15" /> 
	<script type="text/javascript" src="../../webmaster/js/app.js"></script>
	<script type="text/javascript" src="../../webmaster/js/interfaz.js"></script>
	<link REL=StyleSheet HREF="../../webmaster/css/style.css" TYPE="text/css"></link>
	<link rel="stylesheet" type="text/css" href="../../webmaster/css/bootstrap.css"  >
	<link rel="stylesheet" type="text/css" href="../../webmaster/css/css.css"  >
<script>
function typeIsseu(t){
   if (t.value == "compra") {
		show('compra');
		hide('agencia'); 
	}
	 if (t.value == "agencia") {
		show('agencia');
		hide('compra'); 
	}

}
function changepayment(s) {
	if (s.value == 5) {
		show('credit');
		hide('debit');
		hide('invoice');
		hide('misc');
		hide('cash');
	} else if(s.value == 6) {
		hide('credit');
		show('debit');
		hide('invoice');
		hide('misc');
		hide('cash');
	} else if(s.value == 34) {
		hide('credit');
		hide('debit');
		show('invoice');
		hide('misc');
		hide('cash');
	} else if(s.value == 37) {
		hide('credit');
		hide('debit');
		hide('invoice');
		show('misc');
		hide('cash');
	} else if (s.value == 1) {
		hide('credit');
		hide('debit');
		hide('invoice');
		hide('misc');
		show('cash');
	}
}
</script>
</head>
<body>

<div  class="container">
	<div class="row container" >
		<div class=" col-sm-12 col-md-6 col-lg-6 col-md-offset-3 opcion_disponibilidad" id="issue">
			<form action="issue.php"   name="issue_form" >
			    <div class="row">
					<div class="title_disponibilidad col-sm-12 col-md-12 col-lg-12">
						Metodo de Pago 
					</div>		
				</div>
				<br/>

				<div class="row">
					 <div class=" col-sm-12 col-md-12 col-lg-12">
					    <div class="form-group">
							<div class="col-md-12">
								<label class="control-label" for="DocType">Booking ID:</label><br/>
								<input id="BookingID" name="BookingID" type="text" value="<?php echo $_GET['BookingID']; ?>" placeholder="" class="form-control input-md" required="required" readonly="readonly">
							</div>
						</div>
					    			 
					 </div><!-- Fin col Form-->
				</div><!-- Fin Row Form--> 
				
	<div id="compra" style="height:0px;">		
				<div class="row">
					 <div class=" col-sm-12 col-md-6 col-lg-12">
					    <div class="form-group">
							<div class="col-md-12">
								<label class="control-label" for="PaymentType">Payment Type:</label><br/>
									<select id="PaymentType" name="PaymentType" onchange="changepayment(this);" class="form-control">
											<option value="5">CreditCard</option>
											<option value="6">DebitCard</option>
											<option value="34">Invoice</option>
											<option value="37">Miscellaneous</option>
											<option value="1">Cash</option>
								    </select>
							</div>
						</div>
				 	</div><!-- Fin col Form-->
				</div><!-- Fin Row Form--> 
		
				<div id="credit" style="position:relative;">
					<div class="row" >
						 <div class=" col-sm-12 col-md-12 col-lg-12">
						    <div class="form-group">
								<div class="col-md-12">
									<label class="control-label" for="CreditCardCode">Card Code:</label><br/>
									<input id="CreditCardCode" type="text" name="CreditCardCode" placeholder="" class="form-control input-md" required="required">
								</div>
							</div>
						    			 
						 </div><!-- Fin col Form-->
					</div><!-- Fin Row Form--> 
					<div class="row" >
						 <div class=" col-sm-12 col-md-12 col-lg-12">
						    <div class="form-group">
								<div class="col-md-12">
									<label class="control-label" for="CreditCardNumber">Credit Card Number:</label><br/>
									<input id="CreditCardNumber" type="text" name="CreditCardNumber" placeholder="" class="form-control input-md" required="required">
								</div>
							</div>
						    			 
						 </div><!-- Fin col Form-->
					</div><!-- Fin Row Form--> 
					<div class="row" >
						 <div class=" col-sm-12 col-md-12 col-lg-12">
						    <div class="form-group">
								<div class="col-md-12">
									<label class="control-label" for="CreditSeriesCode">Credit Series Code:</label><br/>
									<input id="CreditSeriesCode" type="text" name="CreditSeriesCode" placeholder="" class="form-control input-md" required="required">
								</div>
							</div>
						    			 
						 </div><!-- Fin col Form-->
					</div><!-- Fin Row Form--> 
					<div class="row" >
						 <div class=" col-sm-12 col-md-12 col-lg-12">
						    <div class="form-group">
								<div class="col-md-12">
									<label class="control-label" for="CreditExpireDate">Credit Expire Date:</label><br/>
									<input id="CreditExpireDate" type="text" name="CreditExpireDate" placeholder="" class="form-control input-md" required="required">
								</div>
							</div>
						    			 
						 </div><!-- Fin col Form-->
					</div><!-- Fin Row Form--> 
				</div>
				<div id="debit" style="visibility:hidden;height:0px;position: absolute;">
					<div class="row" >
						 <div class=" col-sm-12 col-md-12 col-lg-12">
						    <div class="form-group">
								<div class="col-md-12">
									<label class="control-label" for="DebitCardCode">Debit Card Code:</label><br/>
									<input id="DebitCardCode" type="text" name="DebitCardCode" placeholder="" class="form-control input-md" required="required">
								</div>
							</div>
						    			 
						 </div><!-- Fin col Form-->
					</div><!-- Fin Row Form--> 
					<div class="row" >
						 <div class=" col-sm-12 col-md-12 col-lg-12">
						    <div class="form-group">
								<div class="col-md-12">
									<label class="control-label" for="DebitCardNumber">Debit Card Number:</label><br/>
									<input id="DebitCardNumber" type="text" name="DebitCardNumber" placeholder="" class="form-control input-md" required="required">
								</div>
							</div>
						    			 
						 </div><!-- Fin col Form-->
					</div><!-- Fin Row Form--> 
					<div class="row" >
						 <div class=" col-sm-12 col-md-12 col-lg-12">
						    <div class="form-group">
								<div class="col-md-12">
									<label class="control-label" for="DebitSeriesCode">Debit Series Code:</label><br/>
									<input id="DebitSeriesCode" type="text" name="DebitSeriesCode" placeholder="" class="form-control input-md" required="required">
								</div>
							</div>
						    			 
						 </div><!-- Fin col Form-->
					</div><!-- Fin Row Form--> 
				</div>
				<div id="invoice" style="visibility:hidden; height:0px;position: absolute;">
					<div class="row" >
						 <div class=" col-sm-12 col-md-12 col-lg-12">
						    <div class="form-group">
								<div class="col-md-12">
									<label class="control-label" for="InvoiceCode">InvoiceCode:</label><br/>
									<input id="InvoiceCode" type="text" name="InvoiceCode" placeholder="" class="form-control input-md" required="required">
								</div>
							</div>
						    			 
						 </div><!-- Fin col Form-->
					</div><!-- Fin Row Form--> 
				</div>
				<div id="misc" style="visibility:hidden; height:0px;position: absolute;">
					<div class="row"  >
						 <div class=" col-sm-12 col-md-12 col-lg-12">
						    <div class="form-group">
								<div class="col-md-12">
									<label class="control-label" for="MiscellaneousCode">MiscellaneousCode:</label><br/>
									<input id="MiscellaneousCode" type="text" name="MiscellaneousCode" placeholder="" class="form-control input-md" required="required">
								</div>
							</div>
						    			 
						 </div><!-- Fin col Form-->
					</div><!-- Fin Row Form--> 
					<div class="row" >
						 <div class=" col-sm-12 col-md-12 col-lg-12">
						    <div class="form-group">
								<div class="col-md-12">
									<label class="control-label" for="Text">Text:</label><br/>
									<input id="Text" type="text" name="Text" placeholder="" class="form-control input-md" required="required">
								</div>
							</div>
						    			 
						 </div><!-- Fin col Form-->
					</div><!-- Fin Row Form--> 
				</div>
				<div id="cash" style="visibility:hidden; height:0px;position: absolute;">
					
				</div>
				
				<div class="row"  style="height:0px;position: relative;">
						 <div class=" col-sm-12 col-md-12 col-lg-12">
						    <div class="form-group">
								<div class="col-md-12">
									<label class="control-label" for="VAT">Value Added Tax:</label><br/>
									<input id="VAT" type="text" name="VAT" placeholder="" class="form-control input-md" required="required">
								</div>
							</div>
						    			 
						 </div><!-- Fin col Form-->
				</div><!-- Fin Row Form--> 
				<div class="row">
					<div class=" col-sm-12 col-md-6 col-lg-12 col-md-offset-4">
						 <div class="form-group">	 
											<!-- Button -->
							<div class="form-group">
								<div class="col-md-12">
									<input id="submit" class="btn btn-primary submit" name="submit" type="button" value="Pagar" onclick="issue();"/>
								</div>
							</div>
						</div> 

					</div>
				</div><!-- Fin Row Form-->
			 
		</div>	

		<div id="agencia" style="visibility:hidden;height:0px;">
			<span>Para conservar el cupo de tu reserva debes realizar el pago en las proximas 24 horas</span>

		</div>	 
			  

			</form>
		</div><!-- Fin col-->
	</div><!-- Fin Row-->
	
</div><!-- Fin Container-->
<div class="container" >
	<div class="row" >
	<div  id="issue_result" class="col-sm-12 col-md-8 col-lg-8 col-md-offset-2 " style="visibility:hidden;"></div>
	</div>
</div>
    <script src="../../webmaster/js/jquery.js"></script>
    <script src="../../webmaster/js/bootstrap.js"></script>

</body>
</html>

 
