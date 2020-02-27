<?php
/*
	This File Create by VahabOnline
    http://vahabonline.ir
    https://my.vahabonline.ir
    info@vahabonline.ir
    0937 465 5385
    011 5433 2064
*/
if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}
function vo_zarinpal_MetaData(){
    return array(
        'DisplayName' => 'زرین پال - وهاب آنلاین',
        'APIVersion' => '2.0',
        'DisableLocalCredtCardInput' => true,
        'TokenisedStorage' => false,
    );
}
function vo_zarinpal_config() {
    $configarray = array(
        "FriendlyName" => array(
            "Type" => "System",
            "Value"=>"زرین پال - وهاب آنلاین"
        ),
        "merchantID" => array(
            "FriendlyName" => "کد پذیرنده",
            "Type" => "text",
            "Size" => "50",
        ),
        "Currencies" => array(
            "FriendlyName" => "واحد پولی",
            "Type" => "dropdown",
            "Options" => array(
                'Rial' => 'ریال',
                'Toman' => 'تومان',
            ),
        ),
        "ConnectTo" => array(
            "FriendlyName" => "انتخاب وبسرویس",
            "Type" => "dropdown",
            "Options" => array(
                'WebGate' => 'وب گیت',
                'ZarinGate' => 'زرین گیت',
                'Sad' => 'سداد',
                'Pec' => 'پارسیان',
                'Sep' => 'سامان',
                'Sep' => 'آسان پرداخت',
                'Fan' => 'فن آوا',
                'Btm' => 'امتیاز',
                'Emz' => 'امتیاز',
            ),
        ),
        "Connection" => array(
            "FriendlyName" => "اتصال به",
            "Type" => "dropdown",
            "Options" => array(
                'www' => 'سرور اصلی',
                'sandbox' => 'سرور تست',
            ),
        ),
        "TextBtn" => array(
            "FriendlyName" => "متن کلید پرداخت",
            "Type" => "text",
            "Default" => "پرداخت آنلاین",
            "Size" => "200",
        ),
        "BtnColor" => array(
            "FriendlyName" => "رنگ کلید",
            "Type" => "dropdown",
            "Options" => array(
                '' => 'خاکستری',
                'btn-default' => 'شیشه ای',
                'btn-primary' => 'آبی پررنگ',
                'btn-success' => 'سبز',
                'btn-info' => 'آبی کم رنگ',
                'btn-warning' => 'نارنجی',
                'btn-danger' => 'قرمز',
                'btn-link' => 'لینک ساده',
            ),
        ),
    );
    return $configarray;
}

function vo_zarinpal_link($params) {

    # Gateway Specific Variables
    $merchantID = $params['merchantID'];
    $currencies = $params['Currencies'];
    $ConnectTo = $params['ConnectTo'];
    $connection = $params['Connection'];
    $TextBtn = $params['TextBtn'];
    $BtnColor = $params['BtnColor'];

    # Invoice Variables
    $invoiceid = $params['invoiceid'];
    $description = $params["description"];
    $Amount = $params['amount']; # Format: ##.##
    $email = $params['clientdetails']['email'];
    $phone = $params['clientdetails']['phonenumber'];
    $systemurl = $params['systemurl'];


    if($currencies == "Rial"){
        $Amount = round($Amount/10);
    }


    $Description = "InvID : " . $invoiceid;
    # Enter your code submit to the gateway...
    $CallbackURL = $systemurl . 'modules/gateways/callback/vo_zarinpal.php?invoiceid='. $invoiceid .'&Amount='. $Amount;

    $client = new SoapClient('https://www.zarinpal.com/pg/services/WebGate/wsdl', ['encoding' => 'UTF-8']);

    $result = $client->PaymentRequest(
        [
            'MerchantID' => $merchantID,
            'Amount' => $Amount,
            'Description' => $Description,
            'Email' => $email,
            'Mobile' => $phone,
            'CallbackURL' => $CallbackURL,
        ]
    );

    $code = "<form method='post'><button type='submit' class='btn $BtnColor'>$TextBtn</button></form>";

    if ($_SERVER['REQUEST_METHOD'] == 'POST'){
        if ($result->Status == 100) {
            Header('Location: https://'.$connection.'.zarinpal.com/pg/StartPay/'.$result->Authority.'/'.$ConnectTo);
        } else {
            echo'ERR: '.$result->Status;
        }
    }

    return $code;
}

?>
