<?php
/**
 * @package common.webservice.soap.nusoap
 */
$client = new SoapClient('https://studentad.ehb.be/wsAuthenticateStudent.asmx?wsdl');

// Parameters als object ...
$params = new stdClass();
$params->login = "lobke.dhondt";
$params->paswoord = "GQCJa7";
$params->strAppID = "chamilo";
$params->strAppPaswoord = "chamilo1234";

// ... of als array.
$params = array();
$params['login'] = 'lobke.dhondt';
$params['paswoord'] = 'GQCJa7';
$params['strAppID'] = 'chamilo';
$params['strAppPaswoord'] = 'chamilo1234';

$result = $client->CanLogin($params);
var_dump($result);

//require_once(dirname(__FILE__) . '/../../../global.inc.php');
//require_once Path :: get_plugin_path() . 'nusoap/nusoap.php';
//
//$client = new nusoap_client('http://studentad.ehb.be/StudentAuthenticate.svc');
//$client->setCredentials('chamilo', 'chamilo1234', '');
//$client->soap_defencoding = 'utf-8';
//
//$result = $client->call('CanLogin', array('login' => 'test', 'paswoord' => 'test'));
//
//echo '<pre>';
//print_r($result);
//echo '</pre>';
//
//echo '<br /><br />';
//echo '<h2>Request</h2><pre>' . htmlspecialchars($client->request, ENT_QUOTES) . '</pre>';
//echo '<h2>Response</h2><pre>' . htmlspecialchars($client->response, ENT_QUOTES) . '</pre>';
//echo '<h2>Debug</h2><pre>' . htmlspecialchars($client->getDebug(), ENT_QUOTES) . '</pre>';


?>
