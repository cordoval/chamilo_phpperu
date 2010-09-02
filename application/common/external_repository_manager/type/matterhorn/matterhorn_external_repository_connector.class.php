<?php
require_once dirname(__FILE__) . '/matterhorn_external_repository_object.class.php';
require_once dirname(__FILE__) . '/matterhorn_external_repository_object_track.class.php';
require_once dirname(__FILE__) . '/matterhorn_external_repository_object_track_video.class.php';
require_once dirname(__FILE__) . '/matterhorn_external_repository_object_track_audio.class.php';
require_once dirname(__FILE__) . '/matterhorn_external_repository_object_attachment.class.php';
require_once dirname(__FILE__) . '/webservices/matterhorn_rest_client.class.php';

/**
 * 
 * @author magali.gillard
 *
 * Test login for Matterhorn : admin
 * Teste password for Matterhorn : opencast
 */
class MatterhornExternalRepositoryConnector extends ExternalRepositoryConnector
{
    private $matterhorn;
    private $login;
    private $password;
    
    const METHOD_POST = MatterhornRestClient :: METHOD_POST;
    const METHOD_GET = MatterhornRestClient :: METHOD_GET;

    function MatterhornExternalRepositoryConnector($external_repository_instance)
    {
        parent :: __construct($external_repository_instance);

        $url = ExternalRepositorySetting :: get('url', $this->get_external_repository_instance_id());
        $this->matterhorn = new MatterhornRestClient($url);
    }

    function retrieve_media_file_content()
    {
    
    }

    function retrieve_external_repository_objects($condition, $order_property, $offset, $count)
    {
    	$response = $this->request(self :: METHOD_GET, '/search/rest/episode', array('limit' => $count, 'offset' => $offset));
        $objects = array();
        $xml = $this->get_xml($response->get_response_content());

        if ($xml)
        {
            
        	foreach ($xml['result'] as $media_package)
            {
            	$objects[] = $this->get_media_package($media_package);
            }
        }
        return new ArrayResultSet($objects);
    }

    function retrieve_external_repository_object($id)
    {
        $response = $this->request(self :: METHOD_GET, '/search/rest/episode', array('id' => $id));
        $xml = $this->get_xml($response->get_response_content());
        
        if ($xml)
        {
            if ($xml['result'])
            {
                return $this->get_media_package($xml['result'][0]);
            }
            else
            {
                return false;
            }
        }
    }

    function request($method, $url, $data)
    {
        if ($this->matterhorn)
        {
        	return $this->matterhorn->request($method, $url, $data);
        }
        return false;
    }

    function count_external_repository_objects($condition)
    {
    	$response = $this->request(self :: METHOD_GET, '/search/rest/episode', array('limit' => 1));
        $xml = $response->get_response_content();

        $doc = new DOMDocument();
        $doc->loadXML($xml);
               
        $object = $doc->getElementsByTagname('search-results')->item(0);
        return $object->getAttribute('total');
    }

    function delete_external_repository_object($id)
    {
    
    }

    function export_external_repository_object($object)
    {
        
        return true;
    }

    function determine_rights($video_entry)
    {
        $rights = array();
        $rights[ExternalRepositoryObject :: RIGHT_USE] = true;
        $rights[ExternalRepositoryObject :: RIGHT_EDIT] = false;
        $rights[ExternalRepositoryObject :: RIGHT_DELETE] = false;
        $rights[ExternalRepositoryObject :: RIGHT_DOWNLOAD] = false;
        return $rights;
    }

    /**
     * @param string $query
     * @return string
     */
    static function translate_search_query($query)
    {
        return $query;
    }

    public function get_series($id)
    {
        $response = $this->request(self :: METHOD_GET, '/series/rest/series/' . $id);
        $xml = $this->get_xml($response->get_response_content());
        if ($xml)
        {
        	if ($xml['metadataList'])
            {
				foreach($xml['metadataList']['metadata'] as $metadata)
				{
					if ($metadata['key'] == 'title')
					{
						return $metadata['value'];
					}
				}
				return "";
            }
            else
            {
                return false;
            }
        }
    }

    private function get_media_package($result)
    {
        $media_package = $result['mediapackage'];
        $matterhorn_external_repository_object = new MatterhornExternalRepositoryObject();
        $matterhorn_external_repository_object->set_id($media_package['id']);
        $matterhorn_external_repository_object->set_duration($result['dcExtent']);
        $matterhorn_external_repository_object->set_title($result['dcTitle']);
        $matterhorn_external_repository_object->set_description($result['dcDescription']);
        $matterhorn_external_repository_object->set_contributors($result['dcContributor']);
        $matterhorn_external_repository_object->set_series($this->get_series($media_package['series']));
        $matterhorn_external_repository_object->set_owner_id($result['dcCreator']);
        $matterhorn_external_repository_object->set_created(strtotime($result['dcCreated']));
        
        $matterhorn_external_repository_object->set_subjects($result['dcSubject']);
        $matterhorn_external_repository_object->set_license($result['dcLicense']);
        $matterhorn_external_repository_object->set_type(Utilities :: camelcase_to_underscores($result['mediaType']));
        $matterhorn_external_repository_object->set_modified(strtotime($result['modified']));
        
        foreach ($media_package['media']['track'] as $media_track)
        {
            $track = new MatterhornExternalRepositoryObjectTrack();
            $track->set_ref($media_track['ref']);
            $track->set_type($media_track['type']);
            $track->set_id($media_track['id']);
            $track->set_mimetype($media_track['mimetype']);
            $track->set_tags($media_track['tags']['tag']);
            $track->set_url($media_track['url']);
            $track->set_checksum($media_track['checksum']);
            $track->set_duration($media_track['duration']);
            
            if ($media_track['audio'])
            {
                $audio = new MatterhornExternalRepositoryObjectTrackAudio();
                $audio->set_id($media_track['audio']['id']);
                $audio->set_device($media_track['audio']['device']);
                $audio->set_encoder($media_track['audio']['encoder']['type']);
                $audio->set_bitdepth($media_track['audio']['bitdepth']);
                $audio->set_channels($media_track['audio']['channels']);
                $audio->set_samplingrate($media_track['audio']['samplingrate']);
                $audio->set_bitrate($media_track['audio']['bitrate']);
                $track->set_audio($audio);
            }
            
            if ($media_track['video'])
            {
                $video = new MatterhornExternalRepositoryObjectTrackVideo();
                $video->set_id($media_track['video']['id']);
                $video->set_device($media_track['video']['device']);
                $video->set_encoder($media_track['video']['encoder']['type']);
                $video->set_framerate($media_track['video']['framerate']);
                $video->set_bitrate($media_track['video']['bitrate']);
                $video->set_resolution($media_track['video']['resolution']);
                $track->set_video($video);
            }
            $matterhorn_external_repository_object->add_track($track);
        }
        
        foreach ($media_package['attachments']['attachment'] as $attachment)
        {
            $attach = new MatterhornExternalRepositoryObjectAttachment();
            $attach->set_id($attachment['id']);
            $attach->set_ref($attachment['ref']);
            $attach->set_type($attachment['type']);
            $attach->set_mimetype($attachment['mimetype']);
            $attach->set_tags($attachment['tags']['tag']);
            $attach->set_url($attachment['url']);
            
            $matterhorn_external_repository_object->add_attachment($attach);
        }
        
        $matterhorn_external_repository_object->set_rights($this->determine_rights($media_package));
        return $matterhorn_external_repository_object;
    }

    private function get_xml($xml)
    {
        if ($xml)
        {
            $unserializer = new XML_Unserializer();
            $unserializer->setOption(XML_UNSERIALIZER_OPTION_COMPLEXTYPE, 'array');
            $unserializer->setOption(XML_UNSERIALIZER_OPTION_ATTRIBUTES_PARSE, true);
            $unserializer->setOption(XML_UNSERIALIZER_OPTION_RETURN_RESULT, true);
            $unserializer->setOption(XML_UNSERIALIZER_OPTION_GUESS_TYPES, true);
            $unserializer->setOption(XML_UNSERIALIZER_OPTION_FORCE_ENUM, array('result', 'track', 'attachment'));
            
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