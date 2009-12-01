<?php
/**
 * $Id: soap_nusoap_webservice.class.php 198 2009-11-13 12:20:22Z vanpouckesven $
 * @package common.webservice.soap.nusoap
 */
/*
 * This is the NuSoap wrapper class as it were. It orchestrates the providing and calling of webservices.
 * The provide_webservice method runs through all the provided functions which have to be provided and,
 * for each function, "converts" the properties of the input and output objects to a WSDL file.
 * This WSDL file is then used to provide the desired webservices.
 * Arrays of objects of the same class for input and output are supported.
 *
 * The provide_webservice_with_wsdl method takes a premade WSDL file
 * and provides the webservices described in said WSDL file.
 *
 * The call_webservice method dissects the provided $functions variable and, for each function,
 * searches the corresponding name, the provided parameters and the name of the handler method,
 * which will process the result on the client side.
 * Then, the call is made and the result is returned to the handler method on the client side.
 *
 * The raise_message method returns the provided ordinary message, e.g. 'Update success' and returns it in a SOAP variable.
 * If you return this variable to the client side, you can send ordinary text messages to the client.
 *
 * The raise_error method allows for returning a detailed error message as a SOAP fault.
 * You can provide a general faultstring, a faultcode (default is null), the wrong doing party (default is Client)
 * and a more detailed message of exactly what went wrong.
 *
 * The debug method prints out the request, respons and debug information for a webservice call.
 *
 * Authors:
 * Stefan Billiet & Nick De Feyter
 * University College of Ghent
 */
require_once Path :: get_plugin_path() . 'nusoap/nusoap.php';

class SoapNusoapWebservice extends Webservice
{
    private $webservice_handler;

    function SoapNusoapWebservice($webservice_handler)
    {
        $this->webservice_handler = $webservice_handler;
        parent :: Webservice();
    }

    function provide_webservice($functions)
    {
        $server = new soap_server();
        $server->configureWSDL('Chamilo', 'http://www.chamilo.org');

        foreach ($functions as $name => $objects) //runs through all methods
        {
            //input field
            if (isset($objects['input']))
            {
                if ($objects['array_input'])
                {
                    $in = $objects['input'][0];
                }
                else
                {
                    $in = $objects['input'];
                }

                $input = array();

                foreach ($in->get_default_property_names() as $property)
                {
                    $input[$property] = array('name' => $property, 'type' => 'xsd:string');
                }

                $server->wsdl->addComplexType(get_class($in), 'complexType', 'struct', 'all', '', $input);

                if ($objects['array_input'])
                {
                    $server->wsdl->addComplexType(get_class($in) . 's', 'complexType', 'array', '', 'SOAP-ENC:Array', array(), array(array('ref' => 'SOAP-ENC:arrayType', 'wsdl:arrayType' => 'tns:' . get_class($in) . '[]')), 'tns:' . get_class($in));
                }
            }

            //output
            if (isset($objects['output']))
            {
                if ($objects['array_output'])
                {
                    $out = $objects['output'][0];
                }
                else
                {
                    $out = $objects['output'];
                }

                $properties = array();

                foreach ($out->get_default_property_names() as $property)
                {
                    $properties[$property] = array('name' => $property, 'type' => 'xsd:string');
                }

                $server->wsdl->addComplexType(get_class($out), 'complexType', 'struct', 'all', '', $properties);

                if ($objects['array_output'])
                {
                    $server->wsdl->addComplexType(get_class($out) . 's', 'complexType', 'array', '', 'SOAP-ENC:Array', array(), array(array('ref' => 'SOAP-ENC:arrayType', 'wsdl:arrayType' => 'tns:' . get_class($out) . '[]')), 'tns:' . get_class($out));
                }
            }
            // method name, input parameters, output parameters
            $server->register(get_class($this->webservice_handler) . '.' . $name, array('input' => 'tns:' . get_class($in) . ($objects['array_input'] ? 's' : ''), 'hash' => 'xsd:string'), array('return' => 'tns:' . get_class($out) . ($objects['array_output'] ? 's' : '')), 'http://www.chamilo.org', 'http://www.chamilo.org#' . $name, 'rpc', 'encoded', '', '', 'NusoapWebservice.handle_webservice');

        }

        if (! isset($HTTP_RAW_POST_DATA))
            $HTTP_RAW_POST_DATA = implode("\r\n", file('php://input'));
        $server->service($HTTP_RAW_POST_DATA);
    }

    function provide_webservice_with_wsdl($wsdl)
    {
        $server = new soap_server($wsdl);
        if (! isset($HTTP_RAW_POST_DATA))
            $HTTP_RAW_POST_DATA = implode("\r\n", file('php://input'));
        $server->service($HTTP_RAW_POST_DATA);
    }

    function call_webservice($wsdl, $functions)
    {
        $client = new nusoap_client($wsdl, 'wsdl');
        $client->response_timeout = - 1;
        $client->timeout = - 1;
        foreach ($functions as $function)
        {
            $function_name = $function['name'];
            $function_parameters = $function['parameters'];
            $handler_function = $function['handler'];
            $result = $client->call($function_name, $function_parameters);
            $handler_result = $this->webservice_handler->{$handler_function}($result);
            if (!$handler_result)
            {
                return false;
            }

            // $this->debug($client);
        }

        return true;
    }

    function raise_message($message)
    {
        return new soapval('return', 'xsd:' . gettype($message), $message);
    }

    function raise_error($faultstring = 'unknown error', $faultcode = NULL, $faultactor = 'Client', $detail = NULL, $mode = null, $options = null)
    {
        return new soap_fault($faultstring, $faultcode, $faultactor, $detail, $mode, $options);
    }

    function debug($client)
    {
        echo '<h2>Request</h2><pre>' . htmlspecialchars($client->request, ENT_QUOTES) . '</pre>';
        echo '<h2>Response</h2><pre>' . htmlspecialchars($client->response, ENT_QUOTES) . '</pre>';
        echo '<h2>Debug</h2><pre>' . htmlspecialchars($client->getDebug(), ENT_QUOTES) . '</pre>';
    }

}
?>