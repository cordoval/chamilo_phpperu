<?php
/**
 * $Id: content_object_soap_search_server.class.php 222 2009-11-13 14:39:28Z chellee $
 * @package application.search_portal.search_source.web_service
 */
require_once dirname(__FILE__) . '/soap_content_object.class.php';
require_once dirname(__FILE__) . '/content_object_soap_search_utilities.class.php';

class ContentObjectSoapSearchServer
{
    const MAX_RESULTS = 100;

    private $server;

    function ContentObjectSoapSearchServer($encoding = 'iso-8859-1')
    {
        $wsdl_file = ContentObjectSoapSearchUtilities :: get_wsdl_file_path(Path :: get(WEB_PATH));
        try
        {
            $this->server = new SoapServer($wsdl_file, array('encoding' => $encoding));
        }
        catch (SoapFault $ex)
        {
            throw ContentObjectSoapSearchUtilities :: soap_fault_to_exception($ex);
        }
        $this->server->setClass(get_class());
    }

    function is_initialized()
    {
        return ! is_null($this->server);
    }

    function run()
    {
        $this->server->handle();
    }

    static function search($query)
    {
        $dm = RepositoryDataManager :: get_instance();
        $adm = AdminDataManager :: get_instance();
        $condition = Utilities :: query_to_condition($query);
        $objects = $dm->retrieve_content_objects($condition, array(ContentObject :: PROPERTY_TITLE), 0, self :: MAX_RESULTS);
        $object_count = $dm->count_content_objects($condition);
        $soap_objects = array();
        while ($lo = $objects->next_result())
        {
            $title = $lo->get_title();
            $description = $lo->get_description();
            $url = $lo->get_view_url();
            $soap_objects[] = new SoapContentObject($lo->get_type(), $title, $description, $lo->get_creation_date(), $lo->get_modification_date(), $url);
        }

        $site_name_setting = PlatformSetting :: get('site_name');
        return array($site_name_setting->get_value(), Path :: get(WEB_PATH), $soap_objects, $object_count);
    }
}
?>