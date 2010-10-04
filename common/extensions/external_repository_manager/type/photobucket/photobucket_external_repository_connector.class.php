<?php
require_once dirname(__FILE__) . '/photobucket_external_repository_object.class.php';
require_once 'OAuth/Request.php';
require_once PATH :: get_plugin_path() . 'PBAPI-0.2.3/PBAPI-0.2.3/PBAPI.php';
/**
 * 
 * @author magali.gillard
 * key : 149830482
 * secret : 410277f61d5fc4b01a9b9e763bf2e97b
 */
class PhotobucketExternalRepositoryConnector extends ExternalRepositoryConnector
{
    private $photobucket;
    private $consumer;
    private $key;
    private $secret;
    private $photobucket_session;

    function PhotobucketExternalRepositoryConnector($external_repository_instance)
    {
        parent :: __construct($external_repository_instance);
        
        $this->key = ExternalRepositorySetting :: get('consumer_key', $this->get_external_repository_instance_id());
        $this->secret = ExternalRepositorySetting :: get('consumer_secret', $this->get_external_repository_instance_id());
        $url = ExternalRepositorySetting :: get('url', $this->get_external_repository_instance_id());
        $this->login();
    }

    function login()
    {
        $this->consumer = new PBAPI($this->key, $this->secret);
        $this->consumer->setResponseParser('simplexmlarray');
        
        $this->photobucket_session = unserialize(ExternalRepositoryUserSetting :: get('session', $this->get_external_repository_instance_id()));
        $oauth_access_token = $this->photobucket_session['photobucket_access_token'];
        
        $oauth_request_token = Session :: retrieve('photobucket_request_token');
        if (! $oauth_access_token)
        {
            if (! $oauth_request_token)
            {
                $this->consumer->login('request')->post()->loadTokenFromResponse();
                Session :: register('photobucket_request_token', serialize($this->consumer->getOauthToken()));
                $this->consumer->goRedirect('login');
            }
            else
            {
                $oauth_request_token = unserialize($oauth_request_token);
                $this->consumer->setOAuthToken($oauth_request_token->getKey(), $oauth_request_token->getSecret());
                
                $this->consumer->login('access')->post()->loadTokenFromResponse();
                
                $session_array = array();
                $session_array['photobucket_access_token'] = $this->consumer->getOAuthToken();
                $session_array['photobucket_username'] = $this->consumer->getUsername();
                $session_array['photobucket_subdomain'] = $this->consumer->getSubdomain();
                $session_array = serialize($session_array);
                
                $setting = RepositoryDataManager :: get_instance()->retrieve_external_repository_setting_from_variable_name('session', $this->get_external_repository_instance_id());
                $user_setting = new ExternalRepositoryUserSetting();
                $user_setting->set_setting_id($setting->get_id());
                $user_setting->set_user_id(Session :: get_user_id());
                $user_setting->set_value($session_array);
                if ($user_setting->create())
                {
                    Session :: unregister('photobucket_request_token');
                }
            }
        
        }
        else
        {
            $username = $this->photobucket_session['photobucket_username'];
            $subdomain = $this->photobucket_session['photobucket_subdomain'];
            
            $this->consumer->setOAuthToken($oauth_access_token->getKey(), $oauth_access_token->getSecret(), $username);
            $this->consumer->setSubdomain($subdomain);
        }
    }

    //Only image for the moment
    function retrieve_external_repository_objects($condition, $order_property, $offset, $count)
    {
        $response = $this->retrieve_photos($condition, $order_property, $offset, $count);
        $objects = array();
        
        foreach ($response['media'] as $media)
        {
            $objects[] = $this->set_photo_object($media);
        }
        return new ArrayResultSet($objects);
    }

    function retrieve_photos($condition, $order_property, $offset, $count)
    {
        $feed_type = Request :: get(PhotobucketExternalRepositoryManager :: PARAM_FEED_TYPE);
        
        $offset = (($offset - ($offset % $count)) / $count) + 1;
        if (is_null($condition))
        {
            $condition = '';
        }
        
        switch ($feed_type)
        {
            case PhotobucketExternalRepositoryManager :: FEED_TYPE_GENERAL :
                $response = $this->consumer->search($condition, array('num' => $count, 'perpage' => $count, 'page' => $offset, 'secondaryperpage' => 1))->get()->getParsedResponse(true);
                if ($condition)
                {
                    $response = $response['result']['primary'];
                }
                else
                {
                    $response = $response['result'];
                }
                
                break;
            case PhotobucketExternalRepositoryManager :: FEED_TYPE_MY_PHOTOS :
                if ($condition)
                {
                    $response = $this->consumer->search($condition, array('num' => $count, 'perpage' => $count, 'page' => $offset, 'secondaryperpage' => 1))->get()->getParsedResponse(true);
                    
                    $response = $response['result']['primary'];
                }
                else
                {
                    $response = $this->consumer->user($this->photobucket_session['photobucket_username'])->search($condition, array('perpage' => $count, 'page' => $offset, 'type' => 'image'))->get()->getParsedResponse(true);
                }
                break;
            default :
                if ($condition)
                {
                    $response = $this->consumer->search($condition, array('num' => $count, 'perpage' => $count, 'page' => $offset, 'secondaryperpage' => 1))->get()->getParsedResponse(true);
                    
                    $response = $response['result']['primary'];
                }
                else
                {
                    $response = $this->consumer->user($this->photobucket_session['photobucket_username'])->search($condition, array('perpage' => $count, 'page' => $offset, 'type' => 'image'))->get()->getParsedResponse(true);
                }
                break;
                break;
        }
        return $response;
    }

    function retrieve_external_repository_object($id)
    {
        $data = $this->consumer->media(urldecode($id))->get()->getParsedResponse(true);
        
        return $this->set_photo_object($data['media']);
    }

    function set_photo_object($data)
    {
        $object = new PhotobucketExternalRepositoryObject();
        $object->set_id(urlencode($data['url']));
        $object->set_title($data['title']);
        $object->set_description($data['description']);
        $object->set_url($data['url']);
        $object->set_thumbnail($data['thumb']);
        $object->set_owner_id($data['_attribs']['username']);
        $object->set_created($data['_attribs']['uploaddate']);
        $object->set_modified($data['_attribs']['uploaddate']);
        $object->set_type(Utilities :: camelcase_to_underscores($data['_attribs']['type']));
        $object->set_rights($this->determine_rights($data));
        
        $tags = array();
        if (count($data['tag']) > 1)
        {
            foreach ($data['tag'] as $tag)
            {
                $tags[] = $tag['_attribs']['tag'];
            }
        }
        elseif (count($data['media']['tag']) == 1)
        {
            $tags[] = $data['tag']['_attribs']['tag'];
        }
        $object->set_tags($tags);
        
        return $object;
    }

    function count_external_repository_objects($condition)
    {
        $feed_type = Request :: get(PhotobucketExternalRepositoryManager :: PARAM_FEED_TYPE);
        
        if (is_null($condition))
        {
            $condition = '';
        }
        
        switch ($feed_type)
        {
            case PhotobucketExternalRepositoryManager :: FEED_TYPE_GENERAL :
                
                if ($condition)
                {
                    $response = $this->consumer->search($condition, array('num' => 1, 'perpage' => 1, 'page' => 1, 'secondaryperpage' => 1))->get()->getParsedResponse(true);
                    return $response['result']['_attribs']['totalresults'];
                }
                else
                {
                    return 900;
                }
                
                break;
            case PhotobucketExternalRepositoryManager :: FEED_TYPE_MY_PHOTOS :
                if ($condition)
                {
                    $response = $this->consumer->search($condition, array('num' => 1, 'perpage' => 1, 'page' => 1, 'secondaryperpage' => 1))->get()->getParsedResponse(true);
                    return $response['result']['_attribs']['totalresults'];
                }
                else
                {
                    $response = $this->consumer->user($this->photobucket_session['photobucket_username'])->get()->getParsedResponse(true);
                    return $response['total_pictures'];
                }
                
                break;
            default :
                if ($condition)
                {
                    $response = $this->consumer->search($condition, array('num' => 1, 'perpage' => 1, 'page' => 1, 'secondaryperpage' => 1))->get()->getParsedResponse(true);
                    return $response['result']['_attribs']['totalresults'];
                }
                else
                {
                    $response = $this->consumer->user($this->photobucket_session['photobucket_username'])->get()->getParsedResponse(true);
                    return $response['total_pictures'];
                }
                break;
        }
    
    }

    /**
     * @param array $values
     * @return boolean
     */
    function update_external_repository_object($values)
    {
        while ($data = $this->consumer->media(urldecode($values[PhotobucketExternalRepositoryObject :: PROPERTY_ID]))->tag()->get()->getParsedResponse(true))
        {
            $response = $this->consumer->media(urldecode($values[PhotobucketExternalRepositoryObject :: PROPERTY_ID]))->tag($data['tagid'])->delete()->getParsedResponse(true);
        }
        
        $response = $this->consumer->media(urldecode($values[PhotobucketExternalRepositoryObject :: PROPERTY_ID]))->title(array('title' => $values[PhotobucketExternalRepositoryObject :: PROPERTY_TITLE]))->put()->getParsedResponse(true);
        if ($response)
        {
            $response = $this->consumer->media(urldecode($values[PhotobucketExternalRepositoryObject :: PROPERTY_ID]))->description(array('description' => $values[PhotobucketExternalRepositoryObject :: PROPERTY_DESCRIPTION]))->put()->getParsedResponse(true);
            if ($response)
            {
                if ($values[PhotobucketExternalRepositoryObject :: PROPERTY_TAGS])
                {
                    $tags = explode(',', $values[PhotobucketExternalRepositoryObject :: PROPERTY_TAGS]);
                    
                    foreach ($tags as $tag)
                    {
                        $response = $this->consumer->media(urldecode($values[PhotobucketExternalRepositoryObject :: PROPERTY_ID]))->tag(array(
                                'tag' => $tag, 'topleftx' => 0, 'toplefty' => 0, 'bottomrightx' => 0, 'bottomrighty' => 0))->post()->getParsedResponse(true);
                        if (! $response)
                        {
                            return false;
                        }
                    }
                }
            }
            else
            {
                return false;
            }
        }
        else
        {
            return false;
        }
        return true;
    }

    function delete_external_repository_object($id)
    {
        $response = $this->consumer->media($id)->delete()->getParsedResponse(true);
        if ($response['deleted'] == 1)
        {
            return true;
        }
        return false;
    }

    function create_external_repository_object($values, $file)
    {
        $photo = base64_encode(file_get_contents($file['tmp_name']));
        
        $tags = explode(',', $values[PhotobucketExternalRepositoryObject :: PROPERTY_TAGS]);
        $response = $this->consumer->album(Session :: retrieve('username'))->upload(array(
                'type' => 'base64', 'filename' => $file['name'], 'uploadfile' => $photo, 'title' => $values[PhotobucketExternalRepositoryObject :: PROPERTY_TITLE], 
                'description' => $values[PhotobucketExternalRepositoryObject :: PROPERTY_DESCRIPTION]))->post()->getParsedResponse(true);
        foreach ($tags as $tag)
        {
            $this->consumer->media(urlencode($response['url']))->tag(array('tag' => $tag, 'topleftx' => 0, 'toplefty' => 0, 'bottomrightx' => 0, 'bottomrighty' => 0))->post()->getParsedResponse(true);
        }
        return urlencode($response['url']);
    
    }

    function export_external_repository_object($object)
    {
        $photo = base64_encode(file_get_contents($object->get_full_path()));
        
        $response = $this->consumer->album($this->photobucket_session['photobucket_username'])->upload(array('type' => 'base64', 'filename' => $object->get_filename(), 'uploadfile' => $photo, 'title' => $object->get_title(), 'description' => $object->get_description()))->post()->getParsedResponse(true);
        
        return urlencode($response['url']);
    }

    function determine_rights($photo)
    {
        $rights = array();
        if ($this->photobucket_session['photobucket_username'] == $photo['_attribs']['username'])
        {
            
            $rights[ExternalRepositoryObject :: RIGHT_USE] = true;
            $rights[ExternalRepositoryObject :: RIGHT_EDIT] = true;
            $rights[ExternalRepositoryObject :: RIGHT_DELETE] = true;
            $rights[ExternalRepositoryObject :: RIGHT_DOWNLOAD] = true;
        }
        else
        {
            $rights[ExternalRepositoryObject :: RIGHT_USE] = true;
            $rights[ExternalRepositoryObject :: RIGHT_EDIT] = false;
            $rights[ExternalRepositoryObject :: RIGHT_DELETE] = false;
            $rights[ExternalRepositoryObject :: RIGHT_DOWNLOAD] = false;
        }
        return $rights;
    }

    /**
     * @param string $query
     * @return string
     */
    static 

    function translate_search_query($query)
    {
        return $query;
    }
}
?>