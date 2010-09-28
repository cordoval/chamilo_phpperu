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
	private $user;

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
        
        $oauth_token = Request :: get('oauth_token');
        $oauth_token_secret = Request :: get('extra');
        $oauth_access_token = Session :: retrieve('access_token');
        $oauth_request_token = Session :: retrieve('request_token');
        
        if (! $oauth_access_token)
        {
            if (! $oauth_request_token)
            {
                $this->consumer->login('request')->post()->loadTokenFromResponse();
                Session :: register('request_token', serialize($this->consumer->getOauthToken()));
                $this->consumer->goRedirect('login');
            }
            else
            {
                $oauth_request_token = unserialize($oauth_request_token);  
                $this->consumer->setOAuthToken($oauth_request_token->getKey(), $oauth_request_token->getSecret());
                $this->consumer->login('access')->post()->loadTokenFromResponse();
                Session :: register('access_token', serialize($this->consumer->getOAuthToken()));
                Session :: register('username', $this->consumer->getUsername());
                Session :: register('subdomain', $this->consumer->getSubdomain());
            }
        }
        
        //        if (! $oauth_token)
        //        {
        //            $request = $this->consumer->login('request')->post()->getResponseString();
        //            parse_str($request, $request);
        //            $this->consumer->setOAuthToken($request['oauth_token'], $request['oauth_token_secret']);
        //           
        //            $this->consumer->goRedirect('login', $request['oauth_token_secret']);
        

        //            if ($token['token'])
        //            {
        //                $setting = RepositoryDataManager :: get_instance()->retrieve_external_repository_setting_from_variable_name('session_token', $this->get_external_repository_instance_id());
        //                $user_setting = new ExternalRepositoryUserSetting();
        //                $user_setting->set_setting_id($setting->get_id());
        //                $user_setting->set_user_id(Session :: get_user_id());
        //                $user_setting->set_value($token['token']);
        //                
        //                if ($user_setting->create())
        //                {
        //                    
        //                    $oauth_token = $token['token'];
        //                }
        //            }
        //            Session :: unregister('23hq_frob');
        //            Session :: unregister('23hq_auth');
        //        
        //        }
        else
        {
            $token = unserialize(Session :: retrieve('access_token'));
            $username = Session :: retrieve('username');
            $subdomain = Session :: retrieve('subdomain');
            
            $this->consumer->setOAuthToken($token->getKey(), $token->getSecret(), $username);
            $this->consumer->setSubdomain($subdomain);
            
        //            $this->consumer->setOAuthToken($oauth_token, $oauth_token_secret);
        //        	$access = $this->consumer->login('access')->post()/*->getResponseString()*/;
        

        }
    
    }

    //    
    //    /**
    //     * @param int $instance_id
    //     * @return PhotobucketExternalRepositoryConnector:
    //     */
    //    static function get_instance($instance_id)
    //    {
    //        if (! isset(self :: $instance[$instance_id]))
    //        {
    //            self :: $instance[$instance_id] = new PhotobucketExternalRepositoryConnector($instance_id);
    //        }
    //        return self :: $instance[$instance_id];
    //    }
    

    //Only image for the moment
    function retrieve_external_repository_objects($condition, $order_property, $offset, $count)
    {
        $response = $this->retrieve_photos($condition, $order_property, $offset, $count);
        $objects = array();
        
        foreach ($response['media'] as $media)
        {
            $object = new PhotobucketExternalRepositoryObject();
            $object->set_id(urlencode($media['url']));
            $object->set_title($media['title']);
            $object->set_description($media['description']);
            $object->set_url($media['url']);
            $object->set_thumbnail($media['thumb']);
            $object->set_owner_id($media['_attribs']['username']);
            $object->set_created($media['_attribs']['uploaddate']);
            $object->set_modified($data['_attribs']['uploaddate']);
            $object->set_type(Utilities :: camelcase_to_underscores($media['_attribs']['type']));
            
            $objects[] = $object;
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
            	$response = $this->consumer->search($condition,  array('num' => $count, 'perpage' => $count, 'page' => $offset, 'secondaryperpage' => 1))->get()->getParsedResponse(true);
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
            	$response = $this->consumer->user(Session :: retrieve('username'))->search($condition, array('perpage' => $count, 'page' => $offset, 'type' => 'image'))->get()->getParsedResponse(true);
                break;
            default :
            	$response = $this->consumer->user(Session :: retrieve('username'))->search($condition, array('perpage' => $count, 'page' => $offset, 'type' => 'image'))->get()->getParsedResponse(true);
                break;
        }
        
        return $response;
    }

    function retrieve_external_repository_object($id)
    {
        $data = $this->consumer->media(urldecode($id))->get()->getParsedResponse(true);
        $data = $data['media'];
        
        $object = new PhotobucketExternalRepositoryObject();
        $object->set_id($id);
        $object->set_title($data['title']);
        $object->set_description($data['description']);
        $object->set_url($data['url']);
        $object->set_thumbnail($data['thumb']);
        $object->set_owner_id($data['_attribs']['username']);
        $object->set_created($data['_attribs']['uploaddate']);
        $object->set_modified($data['_attribs']['uploaddate']);
        $object->set_type(Utilities :: camelcase_to_underscores($data['_attribs']['type']));
        
        return $object;
    }

    function count_external_repository_objects($condition)
    {
        $response = $this->consumer->search('tweety', array('perpage' => 1, 'offset' => 0, 'num' => 1, 'secondaryperpage' => 1))->get()->getParsedResponse(true);
        return $response['result']['_attribs']['totalresults'];
    
    }

    function delete_external_repository_object($id)
    {
        //	    $search_response = $this->request(MatterhornRestClient :: METHOD_DELETE, '/search/rest/' . $id);
    //	    if ($search_response->get_response_http_code() == 200)
    //	    {
    //	    	return true;
    //	    }
    //    	return false;
    }

    function create_external_repository_object($values, $track_path)
    {
        //    	$parameters = array('flavor' => 'presenter/source');
    //    	$parameters['title'] = $values[MatterhornExternalRepositoryObject::PROPERTY_TITLE];
    //    	$parameters['type'] = 'AudioVisual';
    //    	$parameters['BODY'] = file_get_contents($track_path);
    //    	$response = $this->request(MatterhornRestClient :: METHOD_POST, '/ingest/rest/addMediaPackage', $parameters);
    //        $xml = $this->get_xml($response->get_response_content());
    }

    function export_external_repository_object($object)
    {
        //       return true;
    }

    function determine_rights($video_entry)
    {
        $rights = array();
        $rights[ExternalRepositoryObject :: RIGHT_USE] = true;
        $rights[ExternalRepositoryObject :: RIGHT_EDIT] = false;
        $rights[ExternalRepositoryObject :: RIGHT_DELETE] = true;
        $rights[ExternalRepositoryObject :: RIGHT_DOWNLOAD] = false;
        return $rights;
    }

    //    function update_matterhorn_video($values)
    //    {
    //    	$response = $this->request(MatterhornRestClient :: METHOD_GET, '/search/rest/episode', array('id' => MatterhornExternalRepositoryObject::PROPERTY_ID));
    //
    //        $xml = $this->get_xml($response->get_response_content());
    //        $catalogs = $xml['result'][0]['mediapackage']['metadata']['catalog'];
    //        if(isset ($catalogs))
    //        {
    //        	foreach($catalogs as $catalog)
    //        	{
    //        		if ($catalog['type'] == 'dublincore/episode')
    //        		{
    //        			$url = $catalog['url'];
    //        			
    //        		}
    //        	}
    //        	if (isset($url))
    //        	{
    //        		$doc = new DOMDocument();
    //        		$doc->load($url);
    //        		$object = $doc->getElementsByTagname('catalog')->item(0);
    //        	}
    //        }
    //
    //        $object = $doc->getElementsByTagname('catalog')->item(0);
    //        $object->getAttribute('total');
    //    }
    

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