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
    
    function PhotobucketExternalRepositoryConnector($external_repository_instance)
    {
        parent :: __construct($external_repository_instance);

        $this->key = ExternalRepositorySetting :: get('consumer_key', $this->get_external_repository_instance_id());
        $this->secret = ExternalRepositorySetting :: get('consumer_secret', $this->get_external_repository_instance_id());
        $url = ExternalRepositorySetting :: get('url', $this->get_external_repository_instance_id());
        $this->login();
    }   

//    static function get_sort_properties()
//    {
//        return array(self :: RELEVANCE, self :: PUBLISHED, self :: VIEW_COUNT, self :: RATING);
//    }
    
    
    function login()
    {
    	$this->consumer = new PBAPI($this->key, $this->secret);
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
	    $this->consumer->setResponseParser('simplexmlarray');
    	$response = $this->consumer->search('titi')->get()->getParsedResponse(true);

    	$objects = array(); 
    	$media_package = $response['result']['primary']['media'];	

    	$number = 1;
    	foreach ($response['result']['primary']['media'] as $media)
        {
        	$object = new PhotobucketExternalRepositoryObject();
	        $object->set_id($number);
	        $object->set_title($media['title']);
	        $object->set_description($media['description']);
	        $object->set_thumbnail($media['thumb']);
	        $object->set_owner_id($media[_attribs]['username']);
	        $object->set_created(strtotime($media[_attribs]['uploaddate']));	        
        	$object->set_type(Utilities :: camelcase_to_underscores($media[_attribs]['type']));

			$objects[] = $object;
			$number ++;
        }

    	foreach ($response['result']['secondary']['media'] as $media)
        {
        	$object = new PhotobucketExternalRepositoryObject();
	        $object->set_id($number);
	        $object->set_title($media['title']);
	        $object->set_description($media['description']);
	        $object->set_thumbnail($media['thumb']);
	        $object->set_owner_id($media[_attribs]['username']);
	        $object->set_created(strtotime($media[_attribs]['uploaddate']));	        
        	$object->set_type(Utilities :: camelcase_to_underscores($media[_attribs]['type']));

			$objects[] = $object;
			$number ++;
        }
        
        $array = new ArrayResultSet($objects);
        return $array;
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
}
?>