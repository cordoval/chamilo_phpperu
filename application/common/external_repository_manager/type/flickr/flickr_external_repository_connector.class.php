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
    const SORT_DATE_POSTED = 'date-posted';
    const SORT_DATE_TAKEN = 'date-taken';
    const SORT_INTERESTINGNESS = 'interestingness';
    const SORT_RELEVANCE = 'relevance';
    
    private static $instance;
    
    private $manager;
    private $flickr;
    
    private $key;
    private $secret;
    
    private $licenses;

    function FlickrExternalRepositoryConnector($manager)
    {
        $this->manager = $manager;
        
        $this->key = PlatformSetting :: get('flickr_key', RepositoryManager :: APPLICATION_NAME);
        $this->secret = PlatformSetting :: get('flickr_secret', RepositoryManager :: APPLICATION_NAME);
        $this->flickr = new phpFlickr($this->key, $this->secret);
        
        $session_token = LocalSetting :: get('flickr_session_token', UserManager :: APPLICATION_NAME);
        
        if (! $session_token)
        {
            $frob = Request :: get('frob');
            
            if (! $frob)
            {
                if ($manager->is_stand_alone())
                {
                    $next_url = PATH :: get(WEB_PATH) . 'common/launcher/index.php?type=flickr&application=external_repository';
                }
                else
                {
                    $next_url = PATH :: get(WEB_PATH) . 'core.php?go=external_repository&application=repository&category=0&type=flickr';
                }
                
                $this->flickr->auth("delete", $next_url);
            }
            else
            {
                $token = $this->flickr->auth_getToken($frob);
                if ($token['token'])
                {
                    LocalSetting :: create_local_setting('flickr_session_token', $token['token'], UserManager :: APPLICATION_NAME, $this->manager->get_user_id());
                }
            }
        }
        else
        {
            $this->flickr->setToken($session_token);
        }
    }

    static function get_instance($manager)
    {
        if (! isset(self :: $instance))
        {
            self :: $instance = new FlickrExternalRepositoryConnector($manager);
        }
        return self :: $instance;
    }

    function retrieve_licenses()
    {
        if (! isset($this->licenses))
        {
            $raw_licenses = $this->flickr->photos_licenses_getInfo();
            
            $this->licenses = array();
            foreach ($raw_licenses as $raw_license)
            {
                $this->licenses[$raw_license['id']] = array('name' => $raw_license['name'], 'url' => $raw_license['url']);
            }
        }
        
        return $this->licenses;
    }

    function retrieve_photos($condition = null, $order_property, $offset, $count)
    {
        $feed_type = $this->manager->get_parameter(FlickrExternalRepositoryManager :: PARAM_FEED_TYPE);
        
        $offset = (($offset - ($offset % $count)) / $count) + 1;
        $attributes = 'description,date_upload,owner_name,license,media,original_format';
        
        $search_parameters = array();
        $search_parameters['api_key'] = $this->key;
        $search_parameters['per_page'] = $count;
        $search_parameters['page'] = $offset;
        $search_parameters['text'] = $condition;
        $search_parameters['extras'] = $attributes;
        
        $order_direction = $this->convert_order_property($order_property);
        
        if ($order_direction)
        {
            $search_parameters['sort'] = $order_direction;
        }
        
        switch ($feed_type)
        {
            case FlickrExternalRepositoryManager :: FEED_TYPE_GENERAL :
                $photos = ($condition ? $this->flickr->photos_search($search_parameters) : $this->flickr->photos_getRecent($attributes, $count, $offset));
                break;
            case FlickrExternalRepositoryManager :: FEED_TYPE_MOST_INTERESTING :
                $photos = $this->flickr->interestingness_getList(null, $attributes, $count, $offset);
                break;
            case FlickrExternalRepositoryManager :: FEED_TYPE_MOST_RECENT :
                $photos = $this->flickr->photos_getRecent($attributes, $count, $offset);
                break;
            case FlickrExternalRepositoryManager :: FEED_TYPE_MY_PHOTOS :
                $search_parameters['user_id'] = 'me';
                $photos = $this->flickr->photos_search($search_parameters);
                break;
        }
        
        return $photos;
    }

    function retrieve_external_repository_objects($condition = null, $order_property, $offset, $count)
    {
        $photos = $this->retrieve_photos($condition, $order_property, $offset, $count);
        $licenses = $this->retrieve_licenses();
        
        $objects = array();
        
        foreach ($photos['photo'] as $photo)
        {
            $object = new FlickrExternalRepositoryObject();
            $object->set_id($photo['id']);
            $object->set_title($photo['title']);
            $object->set_description($photo['description']);
            $object->set_created($photo['dateupload']);
            $object->set_owner_id($photo['ownername']);
            
            $photo_sizes = $this->flickr->photos_getSizes($photo['id']);
            $photo_urls = array();
            
            foreach ($photo_sizes as $photo_size)
            {
                $key = strtolower($photo_size['label']);
                unset($photo_size['label']);
                unset($photo_size['media']);
                unset($photo_size['url']);
                $photo_urls[$key] = $photo_size;
            }
            
            $object->set_urls($photo_urls);
            $object->set_license($licenses[$photo['license']]);
            
            $types = array();
            $types[] = $photo['media'];
            if (isset($photo['original_format']))
            {
                $types[] = $photo['original_format'];
            }
            
            $object->set_type(implode('_', $types));
            
            $objects[] = $object;
        }
        
        return new ArrayResultSet($objects);
    }

    function count_external_repository_objects($condition)
    {
        if ($condition)
        {
            $parameters = array();
            $parameters['api_key'] = $this->key;
            $parameters['per_page'] = 1;
            $parameters['page'] = 1;
            $parameters['text'] = $condition;
            
            $photos = $this->flickr->photos_search($parameters);
        }
        else
        {
            $photos = $this->flickr->photos_getRecent(null, 1, 1);
        }
        
        return $photos['total'];
    }

    static function translate_search_query($query)
    {
        return $query;
    }

    function convert_order_property($order_properties)
    {
        if (count($order_properties) > 0)
        {
            $order_property = $order_properties[0]->get_property();
            if ($order_property == self :: SORT_RELEVANCE)
            {
                return $order_property;
            }
            else
            {
                $sorting_direction = $order_properties[0]->get_direction();
                
                if ($sorting_direction == SORT_ASC)
                {
                    return $order_property . '-asc';
                }
                elseif ($sorting_direction == SORT_DESC)
                {
                    return $order_property . '-desc';
                }
            }
        }
        
        return null;
    }

    static function get_sort_properties()
    {
        $feed_type = Request :: get(FlickrExternalRepositoryManager :: PARAM_FEED_TYPE);
        if ($feed_type == FlickrExternalRepositoryManager :: FEED_TYPE_GENERAL || feed_type == FlickrExternalRepositoryManager :: FEED_TYPE_MY_PHOTOS)
        {
        	return array(self :: SORT_DATE_POSTED, self :: SORT_DATE_TAKEN, self :: SORT_INTERESTINGNESS, self :: SORT_RELEVANCE);
        }
        else
        {
        	return array();
        }
        	
    }

    function retrieve_external_repository_object($id)
    {
        $photo = $this->flickr->photos_getInfo($id);
        
        $object = new FlickrExternalRepositoryObject();
        $object->set_id($photo['id']);
        $object->set_title($photo['title']);
        $object->set_description($photo['description']);
        $object->set_created($photo['dateupload']);
        $object->set_owner_id($photo['ownername']);
        
        $photo_sizes = $this->flickr->photos_getSizes($photo['id']);
        $photo_urls = array();
        
        foreach ($photo_sizes as $photo_size)
        {
            $key = strtolower($photo_size['label']);
            unset($photo_size['label']);
            unset($photo_size['media']);
            unset($photo_size['url']);
            $photo_urls[$key] = $photo_size;
        }
        
        $object->set_urls($photo_urls);
        $object->set_license($licenses[$photo['license']]);
        
        $types = array();
        $types[] = $photo['media'];
        if (isset($photo['original_format']))
        {
            $types[] = $photo['original_format'];
        }
        
        $object->set_type(implode('_', $types));
        
        return $object;
    }
}
?>