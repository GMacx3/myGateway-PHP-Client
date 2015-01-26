<?php
/******
 * A little Sample to the myGateway Class
 */
require_once 'myGatewayClient.class.php';
use lkdev\myGateway\myGatewayClient as myGatewayClient;

$myGatewayClient = new myGatewayClient();
try{
$myGatewayClient->setProjectName("xxxx");
$myGatewayClient->setProjectHolder("xxxx");
$myGatewayClient->setClientToken("xxxxxxxx");
$myGatewayClient->setApiRequestTyp(myGatewayClient::API_REQUEST_CURL);
$smsId = $myGatewayClient->sendSMS("xxxxxxxxxx", "Hallo ich bin ein Test",3600);
} catch (lkdev\myGateway\myGatewayClientException $ex){
    echo $ex->getMessage()."<br />".$ex->getTraceAsString()."<br />".$ex->getLine();
}
var_dump($smsId);