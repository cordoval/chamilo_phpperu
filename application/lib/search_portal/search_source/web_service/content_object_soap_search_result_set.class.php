<?php
/**
 * $Id: content_object_soap_search_result_set.class.php 222 2009-11-13 14:39:28Z chellee $
 * @package application.search_portal.search_source.web_service
 */
require_once dirname(__FILE__) . '/soap_content_object.class.php';

class ContentObjectSoapSearchResultSet extends ArrayResultSet
{

    function next_result()
    {
        $object = parent :: next_result();
        if ($object)
        {
            return SoapContentObject :: from_standard_object($object);
        }
        return null;
    }
}
?>