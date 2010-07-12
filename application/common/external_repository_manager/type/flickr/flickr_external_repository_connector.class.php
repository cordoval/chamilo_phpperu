<?php
require_once Path :: get_plugin_path() . 'phpflickr-3.0/phpFlickr.php';
require_once dirname(__FILE__) . '/flickr_external_repository_object.class.php';

/**
 * @author Scaramanga
 *
 * Test developer key for Flickr: 61a0f40b9cb4c22ec6282e85ce2ae768
 */

class FlickrExternalRepositoryConnector
{
    private static $instance;
    private $manager;
    private $flickr;
    private $key;

    function FlickrExternalRepositoryConnector($manager)
    {
        $this->manager = $manager;
        
        $this->key = PlatformSetting :: get('flickr_key', RepositoryManager :: APPLICATION_NAME);
        $this->flickr = new phpFlickr($this->key);
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
        $offset = (($offset - ($offset % $count)) / $count);
        
        $parameters = array();
        $parameters['api_key'] = $this->key;
        $parameters['per_page'] = 10;
        $parameters['page'] = 1;
        $parameters['privacy_filter'] = 1;
        $parameters['text '] = 'chamilo';
        
        $photos = $this->flickr->photos_getRecent(null, $count, $offset);
        $objects = array();
        
        foreach ($photos['photo'] as $photo)
        {
            $photo_info = $this->flickr->photos_getInfo($photo['id'], $photo['secret']);
            
            $object = new FlickrExternalRepositoryObject();
            $object->set_id($photo['id']);
            $object->set_title($photo_info['title']);
            $object->set_description($photo_info['description']);
            $object->set_created($photo_info['dates']['posted']);
            $object->set_owner_id($photo_info['owner']['username']);
            
            $photo_urls = array();
            foreach (FlickrExternalRepositoryObject :: get_default_sizes() as $size)
            {
                $photo_urls[$size] = $this->flickr->buildPhotoURL($photo_info, $size);
            }
            $object->set_urls($photo_urls);
            
            $types = array();
            $types[] = $photo_info['media'];
            if (isset($photo_info['originalformat']))
            {
                $types[] = $photo_info['originalformat'];
            }
            
            $object->set_type(implode('_', $types));
            
            $objects[] = $object;
        }
        
        return new ArrayResultSet($objects);
    }

    function count_external_repository_objects($condition)
    {
        $recent = $this->flickr->photos_getRecent(null, 1, 1);
        return $recent['total'];
    }
}
?>