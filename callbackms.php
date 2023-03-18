

<?php
require_once('fedex-common.php5');

foreach ($_POST as $key => $value) {
$value = urlencode(stripslashes($value));
$req = "&$key=$value";
}

$method = $_POST['METHOD'];
$version = $_POST['CALLBACKVERSION'];
$token = $_POST['TOKEN'];
$currencycode = $_POST['CURRENCYCODE'];
$localecode = $_POST['LOCALECODE'];
$street = $_POST['SHIPTOSTREET'];
$street2 = $_POST['SHIPTOSTREET2'];
$city = $_POST['SHIPTOCITY'];
$state = $_POST['SHIPTOSTATE'];
$country = $_POST['SHIPTOCOUNTRY'];
$zip = $_POST['SHIPTOZIP'];
//$zip = 78701;
//$state = "TX";

//echo $zip;
//die();

//**************************************************** INICIO CÓDIGO FEDEX  ******************************************************

// Copyright 2009, FedEx Corporation. All rights reserved.
// Version 12.0.0



$newline = "<br />";
//The WSDL is not included with the sample code.
//Please include and reference in $path_to_wsdl variable.

function ObtenCostoDeEnvio($MetodoDeEnvio,$zip,$state){
	$path_to_wsdl = "RateService_v14.wsdl";

ini_set("soap.wsdl_cache_enabled", "0");

$client = new SoapClient($path_to_wsdl, array('trace' => 1)); // Refer to http://us3.php.net/manual/en/ref.soap.php for more information

			$request['WebAuthenticationDetail'] = array(
			'UserCredential' =>array(
				'Key' => getProperty('key'), 
				'Password' => getProperty('password')
			)
		); 
		$request['ClientDetail'] = array(
			'AccountNumber' => getProperty('shipaccount'), 
			'MeterNumber' => getProperty('meter')
		);
		$request['TransactionDetail'] = array('CustomerTransactionId' => ' *** Rate Request v14 using PHP ***');
		$request['Version'] = array(
			'ServiceId' => 'crs', 
			'Major' => '14', 
			'Intermediate' => '0', 
			'Minor' => '0'
		);
		$request['ReturnTransitAndCommit'] = true;
		$request['RequestedShipment']['DropoffType'] = 'REGULAR_PICKUP'; // valid values REGULAR_PICKUP, REQUEST_COURIER, ...
		$request['RequestedShipment']['ShipTimestamp'] = date('c');
		$request['RequestedShipment']['ServiceType'] = $MetodoDeEnvio; // valid values STANDARD_OVERNIGHT, PRIORITY_OVERNIGHT, FEDEX_GROUND, INTERNATIONAL_PRIORITY ...
		$request['RequestedShipment']['PackagingType'] = 'YOUR_PACKAGING'; // valid values FEDEX_BOX, FEDEX_PAK, FEDEX_TUBE, YOUR_PACKAGING, ...
		$request['RequestedShipment']['TotalInsuredValue']=array(
			'Ammount'=>100,
			'Currency'=>'USD'
		);
		$request['RequestedShipment']['Shipper'] = addShipper();
		//$request['RequestedShipment']['Recipient'] = addRecipient();
		$request['RequestedShipment']['Recipient'] = addRecipient($zip,$state);
		$request['RequestedShipment']['ShippingChargesPayment'] = addShippingChargesPayment();
		$request['RequestedShipment']['RateRequestTypes'] = 'ACCOUNT'; 
		$request['RequestedShipment']['RateRequestTypes'] = 'LIST'; 
		$request['RequestedShipment']['PackageCount'] = '1';
		$request['RequestedShipment']['RequestedPackageLineItems'] = addPackageLineItem1();
		
		
		
		try {
			if(setEndpoint('changeEndpoint')){
				$newLocation = $client->__setLocation(setEndpoint('endpoint'));
			}
			
			$response = $client -> getRates($request);
			//echo var_dump($response);
			//exit();
				
			if ($response -> HighestSeverity != 'FAILURE' && $response -> HighestSeverity != 'ERROR'){  	
				$rateReply = $response -> RateReplyDetails;
		//    	echo '<table border="1">';
		//        echo '<tr><td>Service Type</td><td>Amount</td><td>Delivery Date</td></tr><tr>';
		//    	$serviceType = '<td>'.$rateReply -> ServiceType . '</td>';
		//        $amount = '<td>$' . number_format($rateReply->RatedShipmentDetails[0]->ShipmentRateDetail->TotalNetCharge->Amount,2,".",",") . '</td>';
		//        if(array_key_exists('DeliveryTimestamp',$rateReply)){
		//        	$deliveryDate= '<td>' . $rateReply->DeliveryTimestamp . '</td>';
		//        }else if(array_key_exists('TransitTime',$rateReply)){
		//        	$deliveryDate= '<td>' . $rateReply->TransitTime . '</td>';
		//        }else {
		//        	$deliveryDate='<td>&nbsp;</td>';
		//        }
		//        echo $serviceType . $amount. $deliveryDate;
		//        echo '</tr>';
		//        echo '</table>';
					switch ($MetodoDeEnvio) {
					  case "FEDEX_GROUND":
						$CostoEnvioFedexNormal = number_format($rateReply->RatedShipmentDetails[0]->ShipmentRateDetail->TotalNetCharge->Amount,2,".",",");
							Return $CostoEnvioFedexNormal;						
						break;
					  case "PRIORITY_OVERNIGHT":
						$CostoEnvioFedexEstandar = number_format($rateReply->RatedShipmentDetails[0]->ShipmentRateDetail->TotalNetCharge->Amount,2,".",",");
							Return $CostoEnvioFedexEstandar;
						break;
					  case "STANDARD_OVERNIGHT":
						$CostoEnvioFedexExpress = number_format($rateReply->RatedShipmentDetails[0]->ShipmentRateDetail->TotalNetCharge->Amount,2,".",",");
							Return $CostoEnvioFedexExpress;
						break;
					  default:
						echo "Your favorite color is neither red, blue, or green!";
					}

		//        printSuccess($client, $response);
			}else{
				printError($client, $response);
			} 
			
			writeToLog($client);    // Write to log file   
		} catch (SoapFault $exception) {
		   printFault($exception, $client);        
		}
}
$CostoEnvioFedexNormal = ObtenCostoDeEnvio("FEDEX_GROUND",$zip,$state);
$CostoEnvioFedexEstandar = ObtenCostoDeEnvio("PRIORITY_OVERNIGHT",$zip,$state);
$CostoEnvioFedexExpress = ObtenCostoDeEnvio("STANDARD_OVERNIGHT",$zip,$state);

//ObtenCostoDeEnvio($MetodoDeEnvio1)
//ObtenCostoDeEnvio($MetodoDeEnvio2)
//ObtenCostoDeEnvio($MetodoDeEnvio3)
function addShipper(){
	$shipper = array(
		'Contact' => array(
			'PersonName' => 'WalMart.com.mx',
			'CompanyName' => 'Sender Company Name',
			'PhoneNumber' => '9012638716'
		),
		'Address' => array(
			'StreetLines' => array('Address Line 1'),
			'City' => 'Beverlky Hills',
			'StateOrProvinceCode' => 'CA',
			'PostalCode' => '90210',
			'CountryCode' => 'US'
		)
	);
	return $shipper;
}
function addRecipient($zip,$state){

	$recipient = array(
		'Contact' => array(
			'PersonName' => 'Recipient Name',
			'CompanyName' => 'Company Name',
			'PhoneNumber' => '9012637906'
		),
		'Address' => array(
			'StreetLines' => array('Address Line 1'),
			'City' => 'Austin',
			'StateOrProvinceCode' => $state,
			'PostalCode' => $zip,
			'CountryCode' => 'US',
			'Residential' => false
		)
	);
	return $recipient;	                                     
}
function addShippingChargesPayment(){
	$shippingChargesPayment = array(
		'PaymentType' => 'SENDER', // valid values RECIPIENT, SENDER and THIRD_PARTY
		'Payor' => array(
			'ResponsibleParty' => array(
				'AccountNumber' => getProperty('billaccount'),
				'CountryCode' => 'US'
			)
		)
	);
	return $shippingChargesPayment;
}
function addLabelSpecification(){
	$labelSpecification = array(
		'LabelFormatType' => 'COMMON2D', // valid values COMMON2D, LABEL_DATA_ONLY
		'ImageType' => 'PDF',  // valid values DPL, EPL2, PDF, ZPLII and PNG
		'LabelStockType' => 'PAPER_7X4.75'
	);
	return $labelSpecification;
}
function addSpecialServices(){
	$specialServices = array(
		'SpecialServiceTypes' => array('COD'),
		'CodDetail' => array(
			'CodCollectionAmount' => array(
				'Currency' => 'USD', 
				'Amount' => 10
			),
			'CollectionType' => 'ANY' // ANY, GUARANTEED_FUNDS
		)
	);
	return $specialServices; 
}
function addPackageLineItem1(){
	$packageLineItem = array(
		'SequenceNumber'=>1,
		'GroupPackageCount'=>1,
		'Weight' => array(
			'Value' => 100.0,
			'Units' => 'LB'
		),
		'Dimensions' => array(
			'Length' => 108,
			'Width' => 5,
			'Height' => 5,
			'Units' => 'IN'
		)
	);
	return $packageLineItem;
}

//**************************************************** FIN CÓDIGO FEDEX ******************************************************




$test ="METHOD=CallbackResponse&OFFERINSURANCEOPTION=false&L_INSURANCEAMOUNT0=0.00&L_SHIPPINGOPTIONNAME0=Normal&L_SHIPPINGOPTIONLABEL0=(3 a 5 dias habiles)&L_SHIPPINGOPTIONAMOUNT0=" . $CostoEnvioFedexNormal . "&L_TAXAMT0=1987.42&L_SHIPPINGOPTIONISDEFAULT0=true&L_SHIPPINGOPTIONNAME1=Estándar&L_SHIPPINGOPTIONLABEL1=(2 dias habiles)&L_SHIPPINGOPTIONAMOUNT1=" . $CostoEnvioFedexEstandar . "&L_TAXAMT1=1999.22&L_SHIPPINGOPTIONISDEFAULT1=false&L_SHIPPINGOPTIONNAME2=Express&L_SHIPPINGOPTIONLABEL2=(dia siguiente habil)&L_SHIPPINGOPTIONAMOUNT2=" . $CostoEnvioFedexExpress . "&L_TAXAMT2=2100.42&L_SHIPPINGOPTIONISDEFAULT2=false";
echo $test;






?>