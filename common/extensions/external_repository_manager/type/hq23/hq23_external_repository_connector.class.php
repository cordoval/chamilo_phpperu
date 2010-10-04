<?php
require_once Path :: get_plugin_path() . 'php23/php23.php';
require_once dirname(__FILE__) . '/hq23_external_repository_object.class.php';
/**
 * @author Magali
 *
 * Test developer key for Flickr: 61a0f40b9cb4c22ec6282e85ce2ae768
 * Test developer secret for Flickr: e267cbf5b7a1ad23
 */

class Hq23ExternalRepositoryConnector extends ExternalRepositoryConnector
{
    const SORT_DATE_POSTED = 'date-posted';
    const SORT_DATE_TAKEN = 'date-taken';
    const SORT_INTERESTINGNESS = 'interestingness';
    const SORT_RELEVANCE = 'relevance';
    
    private $hq23;
    
    /**
     * @var string
     */
    private $key;
    
    /**
     * @var string
     */
    private $secret;
    
    /**
     * The id of the user on Flickr
     * @var string
     */
    private $user_id;

    /**
     * @param ExternalRepository $external_repository_instance
     */
    function Hq23ExternalRepositoryConnector($external_repository_instance)
    {
        
        parent :: __construct($external_repository_instance);
        
        $this->key = ExternalRepositorySetting :: get('key', $this->get_external_repository_instance_id());
        $this->secret = ExternalRepositorySetting :: get('secret', $this->get_external_repository_instance_id());
        $this->hq23 = new php23($this->key, $this->secret);
        $uri = Redirect :: current_url();
        
        $session_token = ExternalRepositoryUserSetting :: get('session_token', $this->get_external_repository_instance_id());
        
        if (! $session_token)
        {
            
            $frob = Session :: retrieve('23hq_frob');
            
            if (! $frob)
            {
                $frob = $this->hq23->auth_getFrob();
                Session :: register('23hq_frob', $frob);
            }
            
            $auth = Session :: retrieve('23hq_auth');
            if (! $auth)
            {
                Session :: register('23hq_auth', true);
                $this->hq23->auth($frob, $uri);
            }
            else
            {
                $token = $this->hq23->auth_getToken($frob);

                if ($token['token'])
                {
                    $setting = RepositoryDataManager :: get_instance()->retrieve_external_repository_setting_from_variable_name('session_token', $this->get_external_repository_instance_id());
                    $user_setting = new ExternalRepositoryUserSetting();
                    $user_setting->set_setting_id($setting->get_id());
                    $user_setting->set_user_id(Session :: get_user_id());
                    $user_setting->set_value($token['token']);
                    
                    if ($user_setting->create())
                    {
                        
                        $session_token = $token['token'];
                    }
                }
                Session :: unregister('23hq_frob');
                Session :: unregister('23hq_auth');
            
            }
        }
        if ($session_token)
        {
            $this->hq23->setToken($session_token);
        }
        //$this->hq23->people_getUploadStatus($token);
    }

    /**
     * @param int $instance_id
     * @return FlickrExternalRepositoryConnector:
     */
    static function get_instance($instance_id)
    {
        if (! isset(self :: $instance[$instance_id]))
        {
            self :: $instance[$instance_id] = new Hq23ExternalRepositoryConnector($instance_id);
        }
        return self :: $instance[$instance_id];
    }

    /**
     * @return array:
     */
    function retrieve_licenses()
    {
        if (! isset($this->licenses))
        {
            $raw_licenses = $this->hq23->photos_licenses_getInfo();
            
            $this->licenses = array();
            foreach ($raw_licenses as $raw_license)
            {
                $this->licenses[$raw_license['id']] = array('name' => $raw_license['name'], 'url' => $raw_license['url']);
            }
        }
        
        return $this->licenses;
    }

    /**
     * @return string
     */
    function retrieve_user_id()
    {
        if (! isset($this->user_id))
        {
            $hidden = $this->hq23->test_login();
            $this->user_id = $hidden['id'];
        }
        
        return $this->user_id;
    }

    /**
     * @param mixed $condition
     * @param ObjectTableOrder $order_property
     * @param int $offset
     * @param int $count
     * @return array
     */
    function retrieve_photos($condition = null, $order_property, $offset, $count)
    {
        $feed_type = Request :: get(Hq23ExternalRepositoryManager :: PARAM_FEED_TYPE);
        
        $offset = (($offset - ($offset % $count)) / $count) + 1;
        $attributes = 'description,date_upload,owner_name,license,media,original_format,last_update,url_sq,url_t,url_s,url_m,url_l,url_o';
        
        $search_parameters = array();
        $search_parameters['api_key'] = $this->key;
        $search_parameters['per_page'] = $count;
        $search_parameters['page'] = $offset;
        $search_parameters['text'] = $condition;
        $search_parameters['extras'] = $attributes;
        
        if ($order_property)
        {
            $order_direction = $this->convert_order_property($order_property);
            
            if ($order_direction)
            {
                $search_parameters['sort'] = $order_direction;
            }
        }
        
        switch ($feed_type)
        {
            case Hq23ExternalRepositoryManager :: FEED_TYPE_GENERAL :
                $photos = ($condition ? $this->hq23->photos_search($search_parameters) : $this->hq23->photos_getRecent($attributes, $count, $offset));
                
                break;
            case Hq23ExternalRepositoryManager :: FEED_TYPE_MY_PHOTOS :
                $search_parameters['user_id'] = $this->retrieve_user_id();
                $photos = $this->hq23->photos_search($search_parameters);
                break;
            default :
                $search_parameters['user_id'] = $this->retrieve_user_id();
                $photos = $this->hq23->photos_search($search_parameters);
                break;
        }
        
        return $photos;
    }

    /**
     * @param mixed $condition
     * @param ObjectTableOrder $order_property
     * @param int $offset
     * @param int $count
     * @return ArrayResultSet
     */
    function retrieve_external_repository_objects($condition = null, $order_property, $offset, $count)
    {
        $photos = $this->retrieve_photos($condition, $order_property, $offset, $count);
        //$licenses = $this->retrieve_licenses();
        $licenses = Hq23ExternalRepositoryObject :: get_possible_licenses();
        
        $objects = array();
        
        foreach ($photos['photo'] as $photo)
        {
            $object = new Hq23ExternalRepositoryObject();
            $object->set_id($photo['id']);
            $object->set_external_repository_id($this->get_external_repository_instance_id());
            $object->set_title($photo['title']);
            $object->set_description($photo['description']);
            $object->set_created($photo['dateupload']);
            $object->set_modified($photo['last_update']);
            $object->set_owner_id($photo['ownername']);
            
            //            $photo_urls = array();
            foreach (Hq23ExternalRepositoryObject :: get_possible_sizes() as $key => $size)
                //            {
                //                if (isset($photo['url_' . $key]))
                //                {
                //                    $photo_urls[$size] = array('source' => $photo['url_' . $key], 'width' => $photo['width_' . $key], 'height' => $photo['height_' . $key]);
                //                }
                //            }
                //            $object->set_urls($photo_urls);
                

                //            $photo_size = array();
                //            $photo_size['source'] = $photo['url_sq'];
                //            $photo_size['width'] = 75;
                //            $photo_size['height'] = 75;
                //
                //            $object->set_urls(array('square' => $photo_size));
                //
                $photo_sizes = $this->hq23->photos_getSizes($photo['id']);
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
            if (isset($photo['originalformat']))
            {
                $types[] = strtolower($photo['originalformat']);
            }
            $object->set_type(implode('_', $types));
            $object->set_rights($this->determine_rights($photo['license'], $photo['owner']));
            
            $objects[] = $object;
        }
        
        return new ArrayResultSet($objects);
    }

    /**
     * @param mixed $condition
     * @return int
     */
    function count_external_repository_objects($condition)
    {
        $photos = $this->retrieve_photos($condition, $order_property, 1, 1);
        return $photos['total'];
    }

    /**
     * @param string $query
     * @return string
     */
    static function translate_search_query($query)
    {
        return $query;
    }

    /**
     * @param ObjectTableOrder $order_properties
     * @return string|null
     */
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

    /**
     * @return array
     */
    static function get_sort_properties()
    {
        $feed_type = Request :: get(Hq23ExternalRepositoryManager :: PARAM_FEED_TYPE);
        $query = ActionBarSearchForm :: get_query();
        
        if (($feed_type == Hq23ExternalRepositoryManager :: FEED_TYPE_GENERAL && $query) || $feed_type == Hq23ExternalRepositoryManager :: FEED_TYPE_MY_PHOTOS)
        {
            return array(self :: SORT_DATE_POSTED, self :: SORT_DATE_TAKEN, self :: SORT_INTERESTINGNESS, self :: SORT_RELEVANCE);
        }
        else
        {
            return array();
        }
    
    }

    /* (non-PHPdoc)
     * @see application/common/external_repository_manager/ExternalRepositoryConnector#retrieve_external_repository_object()
     */
    function retrieve_external_repository_object($id)
    {
        //$licenses = $this->retrieve_licenses();
        $licenses = Hq23ExternalRepositoryObject :: get_possible_licenses();
        $photo = $this->hq23->photos_getInfo($id);
        $photo = $photo[0];

        $object = new Hq23ExternalRepositoryObject();
        $object->set_external_repository_id($this->get_external_repository_instance_id());
        $object->set_id($photo['id']);
        $object->set_title($photo['description']);
        $object->set_description($photo['description']);
        $object->set_created($photo['dateuploaded']);
        $object->set_modified($photo['dates']['lastupdate']);
        $object->set_owner_id($photo['owner']['username']);
        
        $tags = array();
        foreach ($photo['tags']['tag'] as $tag)
        {
            $tags[] = array('display' => $tag['raw'], 'text' => $tag['_content']);
        }
        $object->set_tags($tags);
        
        $photo_sizes = $this->hq23->photos_getSizes($photo['id']);
        $photo_urls = array();
        
        foreach ($photo_sizes as $photo_size)
        {
            $key = strtolower($photo_size['label']);
            if (in_array($key, Hq23ExternalRepositoryObject :: get_possible_sizes()))
            {
                unset($photo_size['label']);
                unset($photo_size['media']);
                unset($photo_size['url']);
                $photo_urls[$key] = $photo_size;
            }
        }
        
        $object->set_urls($photo_urls);
        $object->set_license($licenses[$photo['license']]);
        
        $types = array();
        $types[] = $photo['media'];
        if (isset($photo['originalformat']))
        {
            $types[] = strtolower($photo['originalformat']);
        }
        $object->set_type(implode('_', $types));
        
        $object->set_rights($this->determine_rights($photo['license'], $photo['owner']['nsid']));
        return $object;
    }

    /**
     * @param array $values
     * @return boolean
     */
    function update_external_repository_object($values)
    {
        $success = $this->hq23->photos_setMeta($values[Hq23ExternalRepositoryObject :: PROPERTY_ID], $values[Hq23ExternalRepositoryObject :: PROPERTY_TITLE], $values[Hq23ExternalRepositoryObject :: PROPERTY_DESCRIPTION]);
        if (! $success)
        {
            return false;
        }
        else
        {
            $tags = explode(',', $values[Hq23ExternalRepositoryObject :: PROPERTY_TAGS]);
            $tags = '"' . implode('" "', $tags) . '"';
            
            $success = $this->hq23->photos_setTags($values[Hq23ExternalRepositoryObject :: PROPERTY_ID], $tags);
            
            if (! $success)
            {
                return false;
            }
        }
        
        return true;
    }

    /**
     * @param array $values
     * @param string $photo_path
     * @return mixed
     */
    function create_external_repository_object($values, $photo_path)
    {
        $tags = explode(',', $values[Hq23ExternalRepositoryObject :: PROPERTY_TAGS]);
        $tags = '"' . implode('" "', $tags) . '"';
        
        return $this->hq23->sync_upload($photo_path, $values[Hq23ExternalRepositoryObject :: PROPERTY_TITLE], $values[Hq23ExternalRepositoryObject :: PROPERTY_DESCRIPTION], $tags);
    }

    /**
     * @param ContentObject $content_object
     * @return mixed
     */
    function export_external_repository_object($content_object)
    {
        return $this->hq23->sync_upload($content_object->get_full_path(), $content_object->get_title(), $content_object->get_description());
    }

    /**
     * @param int $license
     * @param string $photo_user_id
     * @return boolean
     */
    function determine_rights($license, $photo_user_id)
    {
        $users_match = ($this->retrieve_user_id() == $photo_user_id ? true : false);
        //$compatible_license = ($license == 0 ? false : true);
        $compatible_license = true;
        
        $rights = array();
        $rights[ExternalRepositoryObject :: RIGHT_USE] = $compatible_license || $users_match;
        $rights[ExternalRepositoryObject :: RIGHT_EDIT] = $users_match;
        $rights[ExternalRepositoryObject :: RIGHT_DELETE] = $users_match;
        $rights[ExternalRepositoryObject :: RIGHT_DOWNLOAD] = $compatible_license || $users_match;
        
        return $rights;
    }

    /**
     * @param string $id
     * @return mixed
     */
    function delete_external_repository_object($id)
    {
        return $this->hq23->photos_delete($id);
    }
}
?>