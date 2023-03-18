<?php
/***********************************************************
SetExpressCheckout.php

This is the main web page for the Express Checkout sample.
The page allows the user to enter amount and currency type.
It also accept input variable paymentType which becomes the
value of the PAYMENTACTION parameter.

When the user clicks the Submit button, ReviewOrder.php is
called.

Called by index.html.

Calls ReviewOrder.php.

***********************************************************/
// clearing the session before starting new API Call
session_unset();

	$paymentType = $_GET['paymentType'];
?>


<html>
<head>
    <title>PayPal NVP SDK - Simplified Shopping Cart Page for a Spiritual Store</title>
    <link href="sdk.css" rel="stylesheet" type="text/css" />
</head>
<body>
    <center>
	<form action="ReviewOrderUATP.php" method="POST">
	<input type=hidden name=paymentType value='<?php echo $paymentType?>' >



    <table class="api">
        <tr>
			   <td colspan="2" class="header">
				   Step 1: SetExpressCheckout
			   </td>
        </tr>

        <tr>
           <td colspan="2">
                <center></br>
                You must be logged into <a href="https://developer.paypal.com" id="PayPalDeveloperCentralLink"  target="_blank">Developer
                    Central</br> </a> </br>
                </center>
            </td>
        </tr>
        </table>
		<table>
        <th>Shopping cart Products:</th>
        <tr>
					<td class="field">
						Boleto 1:</td>
					<td>
						<input type="text" size="30" maxlength="32" name="L_NAME1" value="Boleto Ida" /></td>


				<td class="field"> Monto:  </td>
				<td>
					<input type="text" name="L_AMT1" size="5" maxlength="32" value="1" /> </td>

					 <td class="field">
					No. de Tickets:   </td>
				<td>
					 <input type="text" size="3" maxlength="32" name="L_QTY1" value="1" /> </td>

			</tr>
			 <tr>
					<td class="field">
						Boleto 2:</td>
					<td>
						<input type="text" size="30" maxlength="32" name="L_NAME0" value="Boleto Regreso" /> </td>


				<td class="field">
					Monto: <br /> </td>
				<td>
					<input type="text" name="L_AMT0" size="5" maxlength="32" value="1"  /> </td>


					 <td class="field">
					 No. de tickets:   </td>
				   <td>  <input type="text" size="3" maxlength="32" name="L_QTY0" value="1"  /> </td>

			</tr>
			<tr>
			 <td class="field">
					Moneda: <br /> </td>
				<td>
			 <select name="currencyCodeType">
					<option value="USD">USD</option>
					</select>     </td>
	    </tr>
	    <tr>
	    	<td class="field">
	    		Ship To:
	    	</td>
	    	<td>&nbsp;</td>
	    </tr>

        <tr>
			<td class="field">
				 Name:</td>
			<td>
				<input type="text" size="30" maxlength="32" name="PERSONNAME" value="Juan Perez" /></td>
		</tr>
		<tr>
			<td class="field">
				Street:</td>
			<td>
				<input type="text" size="30" maxlength="32" name="SHIPTOSTREET" value="Mariano Escobedo 111" /></td>
		</tr>
		<tr>
			<td class="field">
				City:</td>
			<td>
				<input type="text" size="30" maxlength="32" name="SHIPTOCITY" value="Colonia Nueva Anzures" /></td>
		</tr>
		<tr>
			<td class="field">
				State:</td>
			<td>
				<input type="text" size="30" maxlength="32" name="SHIPTOSTATE" value="Distrito Federal" /></td>
		</tr>
		<tr>
			<td class="field">
				Country:</td>
			<td>
				<input type="text" size="30" maxlength="32" name="SHIPTOCOUNTRYCODE" value="MX" /></td>
		</tr>
		<tr>
			<td class="field">
				Zip Code:</td>
			<td>
				<input type="text" size="30" maxlength="32" name="SHIPTOZIP" value="11590" /></td>
		            </tr>

	<tr>
		<td>
			<input type="image" name="submit" src="https://www.paypal.com/en_US/i/btn/btn_xpressCheckout.gif" />

		</td>
		<td colspan=6>
                <br />
                <br />
                Save time. Pay securely without sharing your financial information.
            </td>

	</tr>
    </table>
    </center>
    <a class="home" id="CallsLink" href="index.html">Home</a>
</body>
</html>
