<?php
/**
 * $Id: content_object_soap_search_client.class.php 222 2009-11-13 14:39:28Z chellee $
 * @package application.search_portal.search_source.web_service
 */
class ContentObjectSoapSearchClient
{
    const KEY_REPOSITORY_TITLE = 'RepositoryTitle';
    const KEY_REPOSITORY_URL = 'RepositoryURL';
    const KEY_RETURNED_RESULTS = 'Results';
    const KEY_RESULT_COUNT = 'ActualResultCount';
    
    private $client;

    function ContentObjectSoapSearchClient($definition_file, $encoding = 'iso-8859-1')
    {
        try
        {
            $this->client = new SoapClient($definition_file, array('encoding' => $encoding));
        }
        catch (SoapFault $ex)
        {
            throw ContentObjectSoapSearchUtilities :: soap_fault_to_exception($ex);
        }
    }

    function is_initialized()
    {
        return ! is_null($this->client);
    }

    function search($query)
    {
        try
        {
            return $this->client->search($query);
        }
        catch (SoapFault $ex)
        {
            throw ContentObjectSoapSearchUtilities :: soap_fault_to_exception($ex);
        }
    }
}
?>