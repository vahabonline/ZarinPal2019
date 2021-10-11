<?php
/*
    This File Create by VahabOnline
    http://vahabonline.ir
    https://my.vahabonline.ir
    info@vahabonline.ir
    0937 465 5385
    011 5433 2064
*/

require_once __DIR__ . '/../../../init.php';
require_once __DIR__ . '/../../../includes/gatewayfunctions.php';
require_once __DIR__ . '/../../../includes/invoicefunctions.php';





// Detect module name from filename.
$gatewayModuleName = basename(__FILE__, '.php');

// Fetch gateway configuration parameters.
$gatewayParams = getGatewayVariables($gatewayModuleName);

// Die if module is not active.
if (!$gatewayParams['type']) {
    die("Module Not Activated");
}


    $invoiceid  = $_GET['invoiceid'];
    $Amount 	= $_GET['Amount'];
    $Authority  = $_GET['Authority'];
    $invoiceid  = checkCbInvoiceID($invoiceid, $gatewayParams['name']);
    $Connection  = $gatewayParams['Connection'];






    if ($_GET['Status'] == 'OK') {


        $client = new SoapClient('https://'.$Connection.'.zarinpal.com/pg/services/WebGate/wsdl', ['encoding' => 'UTF-8']);

        $result = $client->PaymentVerification(
        [
            'MerchantID' => $gatewayParams['merchantID'],
            'Authority' => $Authority,
            'Amount' => $Amount,
        ]
        );

        if($gatewayParams['Currencies'] == "Rial"){
            $Amount = round($Amount*10);
        }

        if ($result->Status == 100) {
			$transid = $resultO->RefID;
			checkCbTransID($transid);
            addInvoicePayment($invoiceid, $transid, $Amount, $Amount, $gatewayModuleName);
            logTransaction($GATEWAY['name'], array('Get' => $_GET, 'Websevice' => (array) $resultO), 'Successful');
        } else {
            logTransaction($GATEWAY['name'], array('Get' => $_GET, 'Websevice' => (array) $resultO), 'Unsuccessful');
        }
    }


Header('Location: '.$CONFIG['SystemURL'].'/viewinvoice.php?id='.$invoiceid);

?>
