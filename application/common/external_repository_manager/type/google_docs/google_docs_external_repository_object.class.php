<?php
require_once dirname(__FILE__) . '/../../external_repository_object.class.php';

class GoogleDocsExternalRepositoryObject extends ExternalRepositoryObject
{

    function is_usable()
    {
        return true;
    }
    
    function get_url()
    {
        return '';
    }
    
    function get_duration()
    {
        return '';
    }
    
    function get_thumbnail()
    {
        return '';
    }
}
?>