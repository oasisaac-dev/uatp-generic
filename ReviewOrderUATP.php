<?php

/********************************************
ReviewOrder.php

This file is called after the user clicks on a button during
the checkout process to use PayPal's Express Checkout. The
user logs in to their PayPal account.

This file is called twice.

On the first pass, the code executes the if statement:

if (! isset ($token))

The code collects transaction parameters from the form
displayed by SetExpressCheckout.html then constructs and
sends a SetExpressCheckout request string to the PayPal
server. The paymentType variable becomes the PAYMENTACTION
parameter of the request string. The RETURNURL parameter
is set to this file; this is how ReviewOrder.php is called
twice.

On the second pass, the code executes the else statement.

On the first pass, the buyer completed the authorization in
their PayPal account; now the code gets the payer details
by sending a GetExpressCheckoutDetails request to the PayPal
server. Then the code calls GetExpressCheckoutDetails.php.

Note: Be sure to check the value of PAYPAL_URL. The buyer is
sent to this URL to authorize payment with their PayPal
account. For testing purposes, this should be set to the
PayPal sandbox.

Called by SetExpressCheckout.html.

Calls GetExpressCheckoutDetails.php, CallerService.php,
and APIError.php.

********************************************/

require_once 'CallerService.php';


session_start();

/* An express checkout transaction starts with a token, that
   identifies to PayPal your transaction
   In this example, when the script sees a token, the script
   knows that the buyer has already authorized payment through
   paypal.  If no token was found, the action is to send the buyer
   to PayPal to first authorize payment
   */

$token = $_REQUEST['token'];

if(! isset($token)) {

		/* The servername and serverport tells PayPal where the buyer
		   should be directed back to after authorizing payment.
		   In this case, its the local webserver that is running this script
		   Using the servername and serverport, the return URL is the first
		   portion of the URL that buyers will return to after authorizing payment
		   */
		   $serverName = $_SERVER['SERVER_NAME'];
		   $serverPort = $_SERVER['SERVER_PORT'];
		   $url=dirname('http://'.$serverName.':'.$serverPort.$_SERVER['REQUEST_URI']);


		   $currencyCodeType=$_REQUEST['currencyCodeType'];
		   $paymentType=$_REQUEST['paymentType'];


           $personName        = $_REQUEST['PERSONNAME'];
		   $SHIPTOSTREET      = $_REQUEST['SHIPTOSTREET'];
		   $SHIPTOCITY        = $_REQUEST['SHIPTOCITY'];
		   $SHIPTOSTATE	      = $_REQUEST['SHIPTOSTATE'];
		   $SHIPTOCOUNTRYCODE = $_REQUEST['SHIPTOCOUNTRYCODE'];
		   $SHIPTOZIP         = $_REQUEST['SHIPTOZIP'];
		   $L_NAME0           = $_REQUEST['L_NAME0'];
		   $L_AMT0            = $_REQUEST['L_AMT0'];
		   $L_QTY0            =	$_REQUEST['L_QTY0'];
		   $L_NAME1           =	$_REQUEST['L_NAME1'];
		   $L_AMT1            = $_REQUEST['L_AMT1'];
		   $L_QTY1            =	$_REQUEST['L_QTY1'];

foreach ($_POST as $key => $value) {
$value = urlencode(stripslashes($value));
$req = "&$key=$value";
}

// Populate variables into local variables
$zip = $_POST['SHIPTOZIP'];

		 /* The returnURL is the location where buyers return when a
			payment has been succesfully authorized.
			The cancelURL is the location buyers are sent to when they hit the
			cancel button during authorization of payment during the PayPal flow
			*/

		   $returnURL =urlencode($url.'/ReviewOrderUATP.php?currencyCodeType='.$currencyCodeType.'&paymentType='.$paymentType);
		   $cancelURL =urlencode("$url/SetExpressCheckoutUATP.php?paymentType=$paymentType" );
		   $BNCODE="TESTEC";
		   $CUSTOM="valor del campo custom";

		 /* Construct the parameter string that describes the PayPal payment
			the varialbes were set in the web form, and the resulting string
			is stored in $nvpstr
			*/
           $itemamt = 0.00;
           $itemamt = $L_QTY0*$L_AMT0+$L_AMT1*$L_QTY1;
           $amt = $itemamt;
		   //$amt = 620.47+$itemamt+1987.42;
           //$maxamt= $amt+2500.00;
           //echo $itemamt . "\n";
           //echo $amt . "\n";
           //echo $maxamt . "\n";
           //exit();
           $nvpstr="";

           /*
            * Setting up the Shipping address details
            */
           $shiptoAddress = "&SHIPTONAME=$personName&SHIPTOSTREET=$SHIPTOSTREET&SHIPTOCITY=$SHIPTOCITY&SHIPTOSTATE=$SHIPTOSTATE&SHIPTOCOUNTRYCODE=$SHIPTOCOUNTRYCODE&SHIPTOZIP=$SHIPTOZIP&CUSTOM=$CUSTOM&BUTTONSOURCE=$BNCODE";
           //echo $maxamt . "\n";
           //exit();
           //con ADDROVERRIDE = 0 se activa el poder cambiar la dirección desde la página de paypal
		   //Si no queremos que se vea la direccion (y por ende no se pueda cambiar) ponemo noshipping = 1 y addroverride = 0
		   //&L_BILLINGTYPE0=MerchantInitiatedBilling&L_BILLINGAGREEMENTDESCRIPTION0=Autorización de pagos Postmates
           $nvpstr="&ADDROVERRIDE=0$shiptoAddress&NOSHIPPING=1&L_NAME0=".$L_NAME0."&L_NAME1=".$L_NAME1."&L_AMT0=".$L_AMT0."&L_AMT1=".$L_AMT1."&L_QTY0=".$L_QTY0."&L_QTY1=".$L_QTY1."&MAXAMT=".(string)$maxamt."&AMT=".(string)$amt."&ITEMAMT=".(string)$itemamt."&ReturnUrl=".$returnURL."&CANCELURL=".$cancelURL ."&CURRENCYCODE=".$currencyCodeType."&PAYMENTACTION=".$paymentType;


           //echo $nvpstr . "\n";
           //exit();



           $nvpstr = $nvpHeader.$nvpstr;

		 	/* Make the call to PayPal to set the Express Checkout token
			If the API call succeded, then redirect the buyer to PayPal
			to begin to authorize payment.  If an error occured, show the
			resulting errors
			*/
		   $resArray=hash_call("SetExpressCheckout",$nvpstr);
		   $_SESSION['reshash']=$resArray;

		   $ack = strtoupper($resArray["ACK"]);

		   if($ack=="SUCCESS"){
					// Redirect to paypal.com here
					$token = urldecode($resArray["TOKEN"]);
					$payPalURL = PAYPAL_URL.$token;
					header("Location: ".$payPalURL);
				  } else  {
					 //Redirecting to APIError.php to display errors.
						$location = "APIError.php";
						header("Location: $location");
					}
} else {
		 /* At this point, the buyer has completed in authorizing payment
			at PayPal.  The script will now call PayPal with the details
			of the authorization, incuding any shipping information of the
			buyer.  Remember, the authorization is not a completed transaction
			at this state - the buyer still needs an additional step to finalize
			the transaction
			*/

		   $token =urlencode( $_REQUEST['token']);

		 /* Build a second API request to PayPal, using the token as the
			ID to get the details on the payment authorization
			*/
		   $nvpstr="&TOKEN=".$token;
           //echo $nvpstr . "\n";
           //exit();

		   //$nvpstr = $nvpHeader.$nvpstr;
		 /* Make the API call and store the results in an array.  If the
			call was a success, show the authorization details, and provide
			an action to complete the payment.  If failed, show the error
			*/
		   $resArray=hash_call("GetExpressCheckoutDetails",$nvpstr);
		   $_SESSION['reshash']=$resArray;
		   $ack = strtoupper($resArray["ACK"]);

		   if($ack == 'SUCCESS' || $ack == 'SUCCESSWITHWARNING'){
					require_once "GetExpressCheckoutDetailsUATP.php";
			  } else  {
				//Redirecting to APIError.php to display errors.
				$location = "APIError.php";
				header("Location: $location");
			  }
}
?>
