<?php
require_once Path :: get_plugin_path() . 'phpflickr-3.0/phpFlickr.php';
require_once dirname(__FILE__) . '/flickr_external_repository_object.class.php';

class FlickrExternalRepositoryConnector
{
    private static $instance;
    private $manager;

    function FlickrExternalRepositoryConnector($manager)
    {
        $this->manager = $manager;
    }

    static function get_instance($manager)
    {
        if (! isset(self :: $instance))
        {
            self :: $instance = new FlickrExternalRepositoryConnector($manager);
        }
        return self :: $instance;
    }

    function retrieve_external_repository_objects($condition, $order_property, $offset, $count)
    {
        return new ArrayResultSet(array());
    }

    function count_external_repository_objects($condition)
    {
        return 0;
    }
}

?>