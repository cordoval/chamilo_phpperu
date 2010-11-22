<?php
namespace common\extensions\video_conferencing_manager\implementation\dimdim;

use common\extensions\video_conferencing_manager\VideoConferencingConnector;
use common\extensions\video_conferencing_manager\VideoConferencingObject;

/**
 * Consumer Key: 69950a3f3ed038479b4b65ffde049f1d
 * Consumer Secret: a84782d0ca686c59
 */

class DimdimVideoConferencingConnector extends VideoConferencingConnector
{  
    private $dimdim;
    private $account;
    private $password;
    

    /**
     * @param ExternalRepository $external_repository_instance
     */
    function DimdimVideoConferencingConnector($video_conferencing_instance)
    {
        parent :: __construct($video_conferencing_instance);
        
        $this->account = ExternalRepositorySetting :: get('account', $this->get_video_conferencing_instance_id());
        $this->password = ExternalRepositorySetting :: get('password', $this->get_video_conferencing_instance_id());
        
        
        $this->vimeo = new phpVimeo($this->consumer_key, $this->consumer_secret);
        $oauth_token = ExternalRepositoryUserSetting :: get('oauth_token', $this->get_external_repository_instance_id());
        $oauth_token_secret = ExternalRepositoryUserSetting :: get('oauth_token_secret', $this->get_external_repository_instance_id());
        
        if (! $oauth_token || ! $oauth_token_secret)
        {
            if (! $_SESSION['request_token'])
            {
                $this->vimeo->auth('delete', Redirect :: current_url());
            }
            else
            {
                $this->vimeo->setToken($_SESSION['request_token'], $_SESSION['request_token_secret'], 'access', true);
              
                $this->token = $this->vimeo->getAccessToken($_GET['oauth_verifier']);
                $setting = RepositoryDataManager :: get_instance()->retrieve_external_repository_setting_from_variable_name('oauth_token', $this->get_external_repository_instance_id());
                $user_setting = new ExternalRepositoryUserSetting();
                $user_setting->set_setting_id($setting->get_id());
                $user_setting->set_user_id(Session :: get_user_id());
                $user_setting->set_value($this->token['oauth_token']);
                $user_setting->create();
                
                $setting = RepositoryDataManager :: get_instance()->retrieve_external_repository_setting_from_variable_name('oauth_token_secret', $this->get_external_repository_instance_id());
                $user_setting = new ExternalRepositoryUserSetting();
                $user_setting->set_setting_id($setting->get_id());
                $user_setting->set_user_id(Session :: get_user_id());
                $user_setting->set_value($this->token['oauth_token_secret']);
                $user_setting->create();
            }
        }
        else
        {
            $this->vimeo->setToken($oauth_token, $oauth_token_secret, 'access', true);
        
        }
    }

    /**
     * @param int $instance_id
     * @return VimeoExternalRepositoryConnector:
     */
    static function get_instance($instance_id)
    {
        if (! isset(self :: $instance[$instance_id]))
        {
            self :: $instance[$instance_id] = new VimeoExternalRepositoryConnector($instance_id);
        }
        return self :: $instance[$instance_id];
    }

    /**
     * @param mixed $condition
     * @param ObjectTableOrder $order_property
     * @param int $offset
     * @param int $count
     * @return array
     */
    function retrieve_videos($condition = null, $order_property, $offset, $count)
    {
        $feed_type = Request :: get(VimeoExternalRepositoryManager :: PARAM_FEED_TYPE);
        
        $offset = (($offset - ($offset % $count)) / $count) + 1;
        $attributes = 'description,upload_date,modified_date,owner';
        
        $search_parameters = array();
        //        $search_parameters['api_key'] = $this->consumer_key;
        $search_parameters['per_page'] = $count;
        $search_parameters['page'] = $offset;
        //        $search_parameters['text'] = $condition;
        //        $search_parameters['extras'] = $attributes;
        

        if ($order_property)
        {
            
            $order_direction = $this->convert_order_property($order_property);
            
            if ($order_direction)
            {
                $search_parameters['sort'] = $order_direction;
            }
        }
        
        $videos = $this->vimeo->call('vimeo.videos.getAll', array('user_id' => '4744622'));
        
        //        switch ($feed_type)
        //        {
        //            case FlickrExternalRepositoryManager :: FEED_TYPE_GENERAL :
        //                $photos = ($condition ? $this->vimeo->photos_search($search_parameters) : $this->flickr->photos_getRecent($attributes, $count, $offset));
        //                break;
        //            case FlickrExternalRepositoryManager :: FEED_TYPE_MOST_INTERESTING :
        //                $photos = $this->flickr->interestingness_getList(null, $attributes, $count, $offset);
        //                break;
        //            case FlickrExternalRepositoryManager :: FEED_TYPE_MOST_RECENT :
        //                $photos = $this->flickr->photos_getRecent($attributes, $count, $offset);
        //                break;
        //            case FlickrExternalRepositoryManager :: FEED_TYPE_MY_PHOTOS :
        //                $search_parameters['user_id'] = 'me';
        //                $photos = $this->flickr->photos_search($search_parameters);
        //                break;
        //            default :
        //                $photos = ($condition ? $this->flickr->photos_search($search_parameters) : $this->flickr->photos_getRecent($attributes, $count, $offset));
        //                break;
        //        }
        //        
        return $videos;
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
        $videos = $this->retrieve_videos($condition, $order_property, $offset, $count);
        $videos_id = array();
        foreach ($videos->videos->video as $video)
        {
            $videos_id[] = $video->id;
        }
        
        $videos_info = array();
        foreach ($videos_id as $video_id)
        {
            $videos_info[] = $this->vimeo->call('vimeo.videos.getInfo', array('video_id' => $video_id));
        }
        
        $objects = array();
        foreach ($videos_info as $video_info)
        {
            $video = $video_info->video[0];
            $object = new VimeoExternalRepositoryObject();
            $object->set_external_repository_id($this->get_external_repository_instance_id());
            $object->set_id($video->id);
            $object->set_title($video->title);
            $object->set_description($video->description);
            $object->set_created($video->upload_date);
            $object->set_modified($video->modified_date);
            $object->set_duration($video->duration);
            $object->set_owner_id($video->owner->id);
            $object->set_urls($video->urls->url[0]->_content);
            $object->set_tags($video->tags->tag);
            
            $object->set_thumbnail($video->thumbnails->thumbnail[1]->_content);
            
            //            $video_urls = array();
            //                            foreach (VimeoExternalRepositoryObject :: get_possible_sizes() as $key => $size)
            //                {
            //                    if (isset($video['url_' . $key]))
            //                    {
            //                        $video_urls[$size] = array('source' => $video['url_' . $key], 'width' => $video['width_' . $key], 'height' => $video['height_' . $key]);
            //                    }
            //                }
            //                $object->set_urls($video_urls);
            

            //                        $video_size = array();
            //                        $video_size['source'] = $video->_content;
            //                        $video_size['width'] = 75;
            //                        $video_size['height'] = 75;
            //            
            //                        $object->set_urls(array('square' => $photo_size));
            //            
            //                        $photo_sizes = $this->flickr->photos_getSizes($photo['id']);
            //                        $photo_urls = array();
            //            
            //                        foreach ($photo_sizes as $photo_size)
            //                        {
            //                            $key = strtolower($photo_size['label']);
            //                            unset($photo_size['label']);
            //                            unset($photo_size['media']);
            //                            unset($photo_size['url']);
            //                            $photo_urls[$key] = $photo_size;
            //                        }
            //                        $object->set_urls($photo_urls);
            

            //                //$object->set_rights($this->determine_rights($photo['license'], $photo['owner']));
            $object->set_rights($this->determine_rights($video));
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
        $videos = $this->retrieve_videos($condition, $order_property, 1, 1);
        return $videos->videos->total;
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
        $feed_type = Request :: get(VimeoExternalRepositoryManager :: PARAM_FEED_TYPE);
        $query = ActionBarSearchForm :: get_query();
        
        if (($feed_type == VimeoExternalRepositoryManager :: FEED_TYPE_GENERAL && $query) || $feed_type == VimeoExternalRepositoryManager :: FEED_TYPE_MY_PHOTOS)
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
        $video = $this->vimeo->call('vimeo.videos.getInfo', array('video_id' => $id));
        $video = $video->video[0];
        
        $object = new VimeoExternalRepositoryObject();
        $object->set_external_repository_id($this->get_external_repository_instance_id());
        $object->set_id($video->id);
        $object->set_title($video->title);
        $object->set_description($video->description);
        $object->set_created($video->upload_date);
        $object->set_modified($video->modified_date);
        $object->set_duration($video->duration);
        $object->set_owner_id($video->owner->id);
        $object->set_urls($video->urls->url[0]->_content);
        $object->set_tags($video->tags->tag);
        
        $object->set_thumbnail($video->thumbnails->thumbnail[2]->_content);
        
        $object->set_rights($this->determine_rights($video));
        
        return $object;
        
    //        
    //        $object->set_rights($this->determine_rights($photo['license'], $photo['owner']['nsid']));
    //        
    //        return $object;
    }

    /**
     * @param array $values
     * @return boolean
     */
    function update_external_repository_object($values)
    {
    	$response = $this->vimeo->call('vimeo.videos.setDescription', array('description' => $values['description'], 'video_id' => $values['id']));
    	if (! $response->stat == 'ok')
    	{
    		return false;
    	}
    	else
    	{    		
    		$response = $this->vimeo->call('vimeo.videos.setTitle', array('title' => $values['title'], 'video_id' => $values['id']));
    		if (! $response->stat == 'ok')
    		{
    			return false;
    		}
    		else
    		{
    			$response = $this->vimeo->call('vimeo.videos.clearTags', array('video_id' => $values['id']));
    			if ($response->stat == 'ok')
    			{
    				$response = $this->vimeo->call('vimeo.videos.addTags', array('video_id' => $values['id'], 'tags' => $values['tags']));
    				if (! $response->stat == 'ok')
    				{
    					return false;
    				}
    				
    			}
    			return true;
    		}
    	}
    }

    /**
     * @param array $values
     * @param string $photo_path
     * @return mixed
     */
    function create_external_repository_object($values, $photo_path)
    {
        $tags = explode(',', $values[VimeoExternalRepositoryObject :: PROPERTY_TAGS]);
        $tags = '"' . implode('" "', $tags) . '"';
        
        return $this->vimeo->sync_upload($photo_path, $values[VimeoExternalRepositoryObject :: PROPERTY_TITLE], $values[VimeoExternalRepositoryObject :: PROPERTY_DESCRIPTION], $tags);
    }

    /**
     * @param ContentObject $content_object
     * @return mixed
     */
    function export_external_repository_object($content_object)
    {
        return $this->flickr->sync_upload($content_object->get_full_path(), $content_object->get_title(), $content_object->get_description());
    }

    /**
     * @param int $license
     * @param string $photo_user_id
     * @return boolean
     */    
    function determine_rights($video_entry)
    {
        $rights = array();
        $rights[ExternalRepositoryObject :: RIGHT_USE] = true;
        $rights[ExternalRepositoryObject :: RIGHT_EDIT] = true;
        $rights[ExternalRepositoryObject :: RIGHT_DELETE] = true;
        $rights[ExternalRepositoryObject :: RIGHT_DOWNLOAD] = false;
        return $rights;
    }

    /**
     * @param string $id
     * @return mixed
     */
    function delete_external_repository_object($id)
    {
        return $this->vimeo->call('vimeo.videos.delete', array('video_id' => $id));
    }
}
?>