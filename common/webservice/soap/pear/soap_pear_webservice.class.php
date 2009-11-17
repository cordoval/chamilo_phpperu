<?php
/**
 * $Id: soap_pear_webservice.class.php 128 2009-11-09 13:13:20Z vanpouckesven $
 * @package common.webservice.soap.pear
 */
require_once Path :: get_library_path() . 'webservices/webservice.class.php';
require_once 'SOAP/Client.php';

class PearSoapWebservice extends Webservice
{
    private $webservice_handler;

    function PearSoapWebservice($webservice_handler)
    {
        $this->webservice_handler = $webservice_handler;
    }

    function provide_webservice($functions)
    {
    
    }

    function call_webservice($wsdl, $functions)
    {
        $options = array();
        $options['namespace'] = 'http://www.Nanonull.com/TimeService/';
        $options['trace'] = true;
        $options['use'] = 'literal';
        
        $soapclient = new SOAP_Client($wsdl);
        
        foreach ($functions as $function)
        {
            $function_name = $function['name'];
            $function_parameters = $function['parameters'];
            $handler_function = $function['handler'];
            
            $result = $soapclient->call($function['name'], $function_parameters, $options);
            
            $this->webservice_handler->{$handler_function}($result);
            //call_user_func(array($this->webservice_handler, $handler_function), $result);
        

        //$this->debug($result, $client);
        }
    }
}
?>