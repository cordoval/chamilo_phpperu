<?php
require_once dirname(__FILE__) . '/../../external_repository_object.class.php';

class FlickrExternalRepositoryObject extends ExternalRepositoryObject
{

    function get_type()
    {
        return 'flickr';
    }
}
?>