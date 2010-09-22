<?php
require_once dirname(__FILE__) . '/photobucket_external_repository_object.class.php';
require_once dirname(__FILE__) . '/webservices/photobucket_rest_client.class.php';
require_once 'OAuth/Request.php';
/**
 * 
 * @author magali.gillard
 * developer key : 179830482
 */
class PhotobucketExternalRepositoryConnector extends ExternalRepositoryConnector
{
    private $photobucket;
	private $consumer;
	private $url;
    
    function PhotobucketExternalRepositoryConnector($external_repository_instance)
    {
        parent :: __construct($external_repository_instance);

        $this->key = ExternalRepositorySetting :: get('consumer_key', $this->get_external_repository_instance_id());
        $this->secret = ExternalRepositorySetting :: get('consumer_secret', $this->get_external_repository_instance_id());
        
        $this->url = ExternalRepositorySetting :: get('url', $this->get_external_repository_instance_id());
       	$this->login();
        //$this->photobucket = new PhotobucketRestClient($url);
    }   

    
    function login()
    {
    	$this->consumer = new OAuth_Consumer($this->key, $this->secret);
    	$request = OAuth_Request::fromConsumerAndToken($this->consumer, NULL, "POST", 'http://api.photobucket.com/login/request');
    	$request->signRequest('HMAC-SHA1', $this->consumer);
dump($request);
   		Header("Location: $request");
		
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

    function retrieve_external_repository_objects($condition, $order_property, $offset, $count)
    {       	   	  	   	
    	$request = OAuth_Request::fromUrl($this->url . '/featured/group?format=xml', 'GET', $this->consumer);
    	$request->signRequest('HMAC-SHA1', $this->consumer);
    	Header("Location: $request");
    	
    	$response = $this->request($request);
    	dump($response);
    	
		//check the url : OK
    	$url = $request->__toString();
		dump($url);
		
		//xml file from this ... NOT OK !!!
		
		exit;

		$objects = array();
        $xml = $this->get_xml($request->get_response_content());

        if ($xml)
        {
            
        	foreach ($xml['result'] as $media_package)
            {
            	$objects[] = $this->get_media_package($media_package);
            }
        }
        return new ArrayResultSet($objects);
    }

 	function request($request)
    {
        if ($this->photobucket)
        {
        	return $this->photobucket;
        }
        return false;
    }

    function retrieve_external_repository_object($id)
    {
//        $response = $this->request(MatterhornRestClient :: METHOD_GET, '/search/rest/episode', array('id' => $id));
//        $xml = $this->get_xml($response->get_response_content());
//
//        if ($xml)
//        {
//            if ($xml['result'])
//            {
//            	return $this->get_media_package($xml['result'][0]);
//            }
//            else
//            {
//                return false;
//            }
//        }
    }

   

    function count_external_repository_objects($condition)
    {
//    	$response = $this->request(MatterhornRestClient :: METHOD_GET, '/search/rest/episode', array('limit' => 1));
//        $xml = $response->get_response_content();
//
//        $doc = new DOMDocument();
//        $doc->loadXML($xml);
//               
//        $object = $doc->getElementsByTagname('search-results')->item(0);
//        return $object->getAttribute('total');
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
    static function translate_search_query($query)
    {
        return $query;
    }

//    public function get_series($id)
//    {
//        $response = $this->request(MatterhornRestClient :: METHOD_GET, '/series/rest/series/' . $id);
//        $xml = $this->get_xml($response->get_response_content());
//        if ($xml)
//        {
//        	if ($xml['metadataList'])
//            {
//				foreach($xml['metadataList']['metadata'] as $metadata)
//				{
//					if ($metadata['key'] == 'title')
//					{
//						return $metadata['value'];
//					}
//				}
//				return "";
//            }
//            else
//            {
//                return false;
//            }
//        }
//    }

//    private function get_media_package($result)
//    {
//        $media_package = $result['mediapackage'];
//        $matterhorn_external_repository_object = new MatterhornExternalRepositoryObject();
//        $matterhorn_external_repository_object->set_id($media_package['id']);
//        $matterhorn_external_repository_object->set_duration($result['dcExtent']);
//        $matterhorn_external_repository_object->set_title($result['dcTitle']);
//        $matterhorn_external_repository_object->set_description($result['dcDescription']);
//        $matterhorn_external_repository_object->set_contributors($result['dcContributor']);
//        $matterhorn_external_repository_object->set_series($this->get_series($media_package['series']));
//        $matterhorn_external_repository_object->set_owner_id($result['dcCreator']);
//        $matterhorn_external_repository_object->set_created(strtotime($result['dcCreated']));
//        
//        $matterhorn_external_repository_object->set_subjects($result['dcSubject']);
//        $matterhorn_external_repository_object->set_license($result['dcLicense']);
//        $matterhorn_external_repository_object->set_type(Utilities :: camelcase_to_underscores($result['mediaType']));
//        $matterhorn_external_repository_object->set_modified(strtotime($result['modified']));
//        
//        foreach ($media_package['media']['track'] as $media_track)
//        {
//            $track = new MatterhornExternalRepositoryObjectTrack();
//            $track->set_ref($media_track['ref']);
//            $track->set_type($media_track['type']);
//            $track->set_id($media_track['id']);
//            $track->set_mimetype($media_track['mimetype']);
//            $track->set_tags($media_track['tags']['tag']);
//            $track->set_url($media_track['url']);
//            $track->set_checksum($media_track['checksum']);
//            $track->set_duration($media_track['duration']);
//            
//            if ($media_track['audio'])
//            {
//                $audio = new MatterhornExternalRepositoryObjectTrackAudio();
//                $audio->set_id($media_track['audio']['id']);
//                $audio->set_device($media_track['audio']['device']);
//                $audio->set_encoder($media_track['audio']['encoder']['type']);
//                $audio->set_bitdepth($media_track['audio']['bitdepth']);
//                $audio->set_channels($media_track['audio']['channels']);
//                $audio->set_samplingrate($media_track['audio']['samplingrate']);
//                $audio->set_bitrate($media_track['audio']['bitrate']);
//                $track->set_audio($audio);
//            }
//            
//            if ($media_track['video'])
//            {
//                $video = new MatterhornExternalRepositoryObjectTrackVideo();
//                $video->set_id($media_track['video']['id']);
//                $video->set_device($media_track['video']['device']);
//                $video->set_encoder($media_track['video']['encoder']['type']);
//                $video->set_framerate($media_track['video']['framerate']);
//                $video->set_bitrate($media_track['video']['bitrate']);
//                $video->set_resolution($media_track['video']['resolution']);
//                $track->set_video($video);
//            }
//            $matterhorn_external_repository_object->add_track($track);
//        }
//        
//        foreach ($media_package['attachments']['attachment'] as $attachment)
//        {
//            $attach = new MatterhornExternalRepositoryObjectAttachment();
//            $attach->set_id($attachment['id']);
//            $attach->set_ref($attachment['ref']);
//            $attach->set_type($attachment['type']);
//            $attach->set_mimetype($attachment['mimetype']);
//            $attach->set_tags($attachment['tags']['tag']);
//            $attach->set_url($attachment['url']);
//            
//            $matterhorn_external_repository_object->add_attachment($attach);
//        }
//        
//        $matterhorn_external_repository_object->set_rights($this->determine_rights($media_package));
//        return $matterhorn_external_repository_object;
//    }

    private function get_xml($xml)
    {
        if ($xml)
        {
            $unserializer = new XML_Unserializer();
            $unserializer->setOption(XML_UNSERIALIZER_OPTION_COMPLEXTYPE, 'array');
            $unserializer->setOption(XML_UNSERIALIZER_OPTION_ATTRIBUTES_PARSE, true);
            $unserializer->setOption(XML_UNSERIALIZER_OPTION_RETURN_RESULT, true);
            $unserializer->setOption(XML_UNSERIALIZER_OPTION_GUESS_TYPES, true);
            //$unserializer->setOption(XML_UNSERIALIZER_OPTION_FORCE_ENUM, array('result', 'track', 'attachment'));
            
            // userialize the document
            return $unserializer->unserialize($xml);
        }
        else
        {
            return false;
        }
    }
}
?>