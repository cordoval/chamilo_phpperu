<?php
/**
 * connection to mediamosa-server
 *
 * via REST protocol
 * uses mediamosa rest client
 *
 * @author jevdheyd
 */

require_once dirname(__FILE__).'/webservices/mediamosa_rest_client.class.php';
require_once dirname(__FILE__). '/mediamosa_mediafile_object.class.php';

class MediamosaStreamingMediaConnector {

    private static $instance;
    private $manager;
    private $mediamosa;
    private $profiles;
    private $server_id;

    const METHOD_POST = MediamosaRestClient :: METHOD_POST;
    const METHOD_GET = MediamosaRestClient :: METHOD_GET;
    const METHOD_PUT = MediamosaRestClient :: METHOD_PUT;
    const REDIRECT_URL = '';
    //TODO: find correct settings
    const PLACEHOLDER_URL = 'http://localhost/chamilo_2.0/layout/aqua/images/common/content_object/big/streaming_video_clip.png';

    function MediamosaStreamingMediaConnector($manager)
    {
        $this->manager = $manager;
        $url = Platformsetting::get('mediamosa_url','admin');
        $this->mediamosa = new MediamosaRestClient($url);
        //TODO: jens -> implement curl request
        $this->mediamosa->set_connexion_mode(RestClient :: MODE_PEAR);
        //login if connector cookie doesn't exist
        //connector cookie takes care of login persistence
        if(!$this->mediamosa->get_connector_cookie())
        {
            //TODO:jens->throw error if fails
            $this->mediamosa->login(PlatformSetting::get('mediamosa_username','admin'), PlatformSetting::get('mediamosa_password','admin'));
        }
    }

    static function get_instance($manager)
    {
        if (! isset(self :: $instance))
        {
            self :: $instance = new MediamosaStreamingMediaConnector($manager);
        }
        return self :: $instance;
    }

    /*
     * set the server's id
     * @param int server id
     */
    function set_server_id($server_id)
    {
        $this->server_id = $server_id;
    }

    /*
     * if a server isset return it's id
     * @return server_id
     */
    function get_server_id()
    {
        if($this->server_id)
        {
            return $this->server_id;
        }
    }
    
    function request_mediamosa_asset($asset_id)
    {
        return $this->mediamosa->request('GET',sprintf('/asset/%s', $asset_id));
    }

    /*
     * retrieves the default mediafile for an asset
     * @param $asset_id
     * @return $mediafile_id
     */
    function retrieve_mediamosa_asset_default_mediafile($asset_id)
    {
        if($profiles = $this->retrieve_mediamosa_transcoding_profiles())
        {
            foreach ($profiles as $profile_id => $profile)
            {
                if($profile[MediamosaMediafileObject :: PROPERTY_IS_DEFAULT] == 'TRUE') $default_transcode_profile_id = $profile_id;
            }

            if($asset = $this->retrieve_mediamosa_asset($asset_id, false))
            {
                foreach ($asset->items->item->mediafiles as $mediafile)
                {
                    if($mediafile->transcode_profile_id == $default_transcode_profile_id)
                    {
                        return $mediafile->mediafile_id;
                    }
                }
            }
        }
        return false;
    }

    /*
     * searchable retrieve assets on a mediamosa server
     * @param string condition optional
     * @param string order_property optional
     * @param string offset optional
     * @param string count optional
     * @return array with MediamosaStreamingMediaObject(s)
     */
    function retrieve_mediamosa_assets($condition = null, $order_property = null, $offset = null, $count = '10')
    {
        $params = array();

        if($order_property)
        {
            if(is_array($order_property))
            {
                $params['order_by'] = $order_property[0];
            }
            else
            {
                $params['order_by'] = $order_property;
            }
        }
        if($offset) $params['offset'] = $offset;

        $params['limit'] = $count;

        if($condition) $condition = sprintf('&%'.$condition);

        //if no params exist no request will produce error
        if(count($params) > 1)
        {
            $response = $this->mediamosa->request('GET','/asset'.$condition, $params);
            if($response->check_result())
            {
                $objects = array();

                $xml = $response->get_response_content_xml();
                
                if(isset($xml->items->item))
                {
                    foreach($xml->items->item as $asset)
                    {
                        $objects[] = $this->create_mediamosa_streaming_media_object($asset);
                    }
                    return $objects;
                }
            }
        }
        return false;
    }

    /*
     * creates and populates a MediamosaStreamingMediaObject with xml data
     * @param object simple xml element
     * @return MediamosaStreamingMediaObject
     */
    function create_mediamosa_streaming_media_object($asset){
        if($asset)
        {
            
            $mediamosa_asset =  new MediamosaStreamingMediaObject();

            $mediamosa_asset->set_id((string)$asset->asset_id);
            $mediamosa_asset->set_title((string)$asset->dublin_core->title);
            //$metadata['language'] = (string)$asset->dublin_core->language;
            //$metadata['subject'] = (string)$asset->dublin_core->subject;
            $mediamosa_asset->set_description((string)$asset->dublin_core->description);
            //$metadata['contributor'] = (string)$asset->dublin_core->contributor;
            $mediamosa_asset->set_publisher((string)$asset->dublin_core->publisher);
            $mediamosa_asset->set_date((string)$asset->dublin_core->date);
            ((string)$asset->vpx_still_url) ? $mediamosa_asset->set_thumbnail((string)$asset->vpx_still_url) : $mediamosa_asset->set_thumbnail(self :: PLACEHOLDER_URL);
            $mediamosa_asset->set_creator((string)$asset->dublin_core->creator);
            //TODO:jens -> implement status
            $mediamosa_asset->set_status($status);
            

            //status of mediafile is unavailable by default
            $mediamosa_asset->set_status(StreamingMediaObject :: STATUS_UNAVAILABLE);

            $mediamosa_transcoding_profiles = $this->retrieve_mediamosa_transcoding_profiles();

            //add mediafiles
            foreach($asset->mediafiles->mediafile as  $mediafile)
            {
                //if there is still an original mediafile
                //see if it can be removed
                if((string) $mediafile->is_original_file == 'TRUE'){
                    $this->remove_mediamosa_original_mediafile($asset);
                }

                if((string) $mediafile->transcode_profile_id)
                {
                    $mediamosa_mediafile = new MediamosaMediafileObject();
                    $mediamosa_mediafile->set_id((string)$mediafile->mediafile_id);
                    $mediamosa_mediafile->set_title($mediamosa_transcoding_profiles[(string) $mediafile->transcode_profile_id][MediamosaMediafileObject :: PROPERTY_TITLE]);
                    if($mediamosa_transcoding_profiles[(string) $mediafile->transcode_profile_id][MediamosaMediafileObject :: PROPERTY_IS_DEFAULT] ==  'TRUE')
                    {
                        $mediamosa_mediafile->set_is_default();
                    }
                    
                    $mediamosa_asset->add_mediafile($mediamosa_mediafile);
                    
                    if($mediamosa_mediafile->get_is_default())
                    {
                        $mediamosa_asset->set_default_mediafile((string)$mediafile->mediafile_id);
                    }
                    
                    //if there is a playable mediafile - set status available
                    $mediamosa_asset->set_status(StreamingMediaObject :: STATUS_AVAILABLE);
                }
            }
            return $mediamosa_asset;
        }
        return false;
    }

    /*
     * if all transcoding profiles are provided
     */
    function remove_mediamosa_original_mediafile($asset){
        $mediamosa_transcoding_profiles = $this->retrieve_mediamosa_transcoding_profiles();
        $n_transcoded = 0;

        foreach($asset->mediafiles->mediafile as  $mediafile)
        {
            //if the mediafile is a transcode to a provided profile
            if(isset($mediamosa_transcoding_profiles[(string) $mediafile->transcode_profile_id]))
            {
                $n_transcoded ++;
            }

            //get original mediafile
            if((string) $mediafile->is_original_file == 'TRUE')
            {
                $original_mediafile_id = $mediafile->mediafile_id;
            }
        }

        //if all files are transcoded
        if($n_transcoded == count($mediamosa_transcoding_profile))
        {
            //if there still is an original mediafile
            if($original_file)
            {
                //remove original mediafile
                $this->remove_mediamosa_mediafile($original_mediafile_id);
            }
            
        }
    }

    function is_editable($asset_id)
    {
        if($asset_id)
        {
           //TODO:jens --> only if user is admin
           return true;
        }
        return false;
    }

    function is_usable($id)
    {
        return true;
    }

    /*
     * create an asset on mediamosa server
     * @return string asset_id
     */
    function create_mediamosa_asset()
    {
        $data = array();
        $data['user_id'] = Session::get_user_id();

        $response = $this->mediamosa->request(self :: METHOD_POST, '/asset/create', $data);
        if($response->check_result($response))
        {
            return (string)$response->get_response_content_xml()->items->item->asset_id;
        }
        return false;
    }

    /*
     * retrieve an asset on mediamosa server
     * @param string asset_id
     * @param boolean object
     * @return MediamosaStreamingMediaObject or simplexmlelement
     */
    function retrieve_mediamosa_asset($asset_id, $object = true)
    {
        if($asset_id)
        {
            $data = array();
            $data['user_id'] = Session :: get_user_id();

            $response = $this->mediamosa->request(self :: METHOD_GET, '/asset/'.$asset_id, $data);
            if($response->check_result())
            {
                if($object)
                {
                    return $this->create_mediamosa_streaming_media_object($response->get_response_content_xml()->items->item);
                }
                else
                {
                    return $response->get_response_content_xml()->items->item;
                }
            }
        }
        return false;
    }

    /*
     * remove an asset on mediamosa server
     * @param boolean cascade (delete all underlying mediafiles + jobs as well)
     * @return boolean
     */
    function remove_mediamosa_asset($asset_id, $cascade = true)
    {
        if($asset_id)
        {
            $data = array();
            $data['user_id'] = Session :: get_user_id();
            if($cascade == true)
            {
                $data['delete'] = 'cascade';
            }

            $response = $this->mediamosa->request(self :: METHOD_POST, '/asset/'.$asset_id.'/delete', $data);
            if($response->check_result())
            {
                //TODO:return result description on fail
                return true;
            }
        }
        return false;
    }

    /*
     * remove multiple assets on mediamosa server
     * @param array $asset_ids
     * @param boolean cascade (delete all underlying mediafiles + jobs as well)
     * @return boolean
     */
    function remove_mediamosa_assets($asset_ids, $cascade = true)
    {
        if(is_array($asset_ids))
        {
            $data = array();
            $data['asset_id'] = $asset_ids;
            $data['user_id'] = Session :: get_user_id();
            if($cascade == true)
            {
                $data['delete'] = 'cascade';
            }

            $response = $this->mediamosa->request(self :: METHOD_POST, '/asset/delete', $data);
            if($response->check_result())
            {
                $xml = $response->get_response_content_xml();
                
                $output = array();
                foreach($xml->items as $item)
                {
                    $output[] = (string) $item->asset_id . (string) $item->result . ':' . (string) $item->result_description;
                }
                return $output;
            }
        }
        return false;
    }

    /*
     * create a mediamosa mediafile
     * @param string asset_id
     * @return string mediafile_id
     */
    function create_mediamosa_mediafile($asset_id)
    {
        if($asset_id)
        {
            $data['user_id'] = Session :: get_user_id();
            $data['asset_id'] = $asset_id;
            $data['is_downloadable'] = PlatformSetting :: get('mediamosa_mediafile_is_downloadable', 'admin');

            $response = $this->mediamosa->request(self :: METHOD_POST, '/mediafile/create', $data);
            if($response->check_result())
            {
                return (string)$response->get_response_content_xml()->items->item->mediafile_id;
            }
        }
        return false;
    }

     /*
     * retrieves a mediamosa mediafile
     * @param mediafile_id
     * @return MediamosaMediafileObject
     */
    function retrieve_mediamosa_mediafile($mediafile_id){}

    /*
     * upload a file to mediamosa
     * @param file
     * @param string method
     * @param string upload_ticket_id
     * @return array transcode -the transcoding_profile_ids
     * @return boolean ??
     */
    //TODO: jens determine output of upload request
    function upload_mediamosa_mediafile($method, $upload_ticket_id, $file, $transcode = null){}

    /*
     * remove a mediamosa mediafile
     * @param mediafile_id
     * @return boolean
     */
    function remove_mediamosa_mediafile($mediafile_id)
    {
        if($mediafile_id)
        {
            $data = array();
            $data['user_id'] = Session :: get_user_id();

            $response = $this->mediamosa->request(self :: METHOD_POST, 'mediafile/'.$mediafile_id.'/delete', $data);
            if($response->check_result())
            {
                return true;
            }
        }
        return false;
    }

    /*
     * create or update metadata for an asset
     * @param string asset_id
     * @return boolean
     */
    function add_mediamosa_metadata($asset_id, $data)
    {
        if($asset_id)
        {
            if(is_array($data))
            {
                $data['user_id'] = Session :: get_user_id();
                //if metadata exists -> overwrite
                //TODO : check if these properties also apply when updating metadata
                $data['replace'] = 'TRUE';
                //$data['action'] = 'update';

                $response = $this->mediamosa->request(self :: METHOD_POST, '/asset/'.$asset_id.'/metadata', $data);
                if($response->check_result($response))
                {
                    return true;
                }
            }
        }
        return false;
    }
    
    /*
     * requests a upload ticket from server
     * @param string mediafile_id
     * @return simplexmlelement
     */
    function create_mediamosa_upload_ticket($mediafile_id)
    {
        
        if($mediafile_id)
        {
            $data = array();
            $data['user_id'] = Session :: get_user_id();
            //$data['mediafile_id'] = $mediafile_id; //TODO : necessary?

            $response = $this->mediamosa->request(self :: METHOD_GET, '/mediafile/'.$mediafile_id.'/uploadticket/create', $data);
            if($response->check_result($response))
            {
                return $response->get_response_content_xml();
            }
        }
        return false;
    }

    /*
     * creates a still for a certain mediafile on the mediamosa server
     * @param string mediafile
     * @return string url
     */
    function create_mediamosa_still($mediafile_id){}

    /*
     * retrieves all mediamosa profiles
     * @return array
     */
    function retrieve_mediamosa_transcoding_profiles()
    {
        if(!$this->profiles)
        {
            $data= array();

            //
            $data['user_id'] = '2';
            $data['is_app_admin'] = 'TRUE';

            $response = $this->mediamosa->request(self :: METHOD_GET, '/transcode/profile',$data);
            if($response->check_result())
            {

                $profiles = array();

                foreach($response->get_response_content_xml()->items->item as $profile)
                {
                    $profiles[(string)$profile->profile_id] = array(MediamosaMediafileObject :: PROPERTY_TITLE => (string) $profile->profile, MediamosaMediafileObject :: PROPERTY_IS_DEFAULT => (string) $profile->default);
                }

                $this->profiles = $profiles;

                return $profiles;
            }

            return false;
        }
        return $this->profiles;

    }

    /*
     * creates a transcoding job on the mediamosa server
     * if a transcoding_profile_id is not provided it will transcode to the default profile(s)
     * @param string mediafile_id
     * @param string transcoding_profile_id
     * @return int job_id
     */
    function transcode_mediamosa_mediafile($mediafile_id, $transcoding_profile_id = null){}

    function add_mediamosa_mediafile_rights($mediafile_id, $rights){}

    function clean()
    {
        if($response = $this->retrieve_mediamosa_assets(null,'title', null))
        {
            $asset_ids = array();
        
            foreach($response as $n => $mediamosa_streaming_media_object)
            {
                $asset_ids[] = $mediamosa_streaming_media_object->get_id();
            }

            if($response = $this->remove_mediamosa_assets($asset_ids))
            {
                return $response;
            }
        }
        return false;
    }

    /*
     * get a play call request from mediamosa server
     * @param string asset_id
     * @param string mediafile_id option = default
     * @param string reponse object or url
     * @return string url or html object dependent on response
     */
    function mediamosa_play_proxy_request($asset_id, $mediafile_id = 'default', $response = 'object')
    {
        if($asset_id)
        {
            //retrieve default mediafile (mostly in case no mediafile is supplied)
            if($mediafile_id == 'default')
            {
                $mediafile_id = $this->retrieve_mediamosa_asset_default_mediafile($asset_id);

                if(!$mediafile_id) return false;
            }
            
            //prepare request data
            $data = array();
            $data['mediafile_id'] = $mediafile_id;
            $data['user_id'] = Session :: get_user_id();
            $data['response'] = $response;
            
            //get object or url
            $player = $this->mediamosa->request(self :: METHOD_GET, '/asset/' .$asset_id . '/play', $data);
            
            //verify
            switch($player->get_response_content_xml()->header->request_result_id)
            {
                //if 601 -> ok
                case '601':
                    return (string) $player->get_response_content_xml()->items->item->output;
                    break;
                //   701 -> doesn't exits
                case '701':
                    return Translation :: get('mediafile_doesnt_exist');
                //   1800 -> not authorised
                case '1800':
                    return Translation :: get('no_access');
                //   default - no embedded player available
                default:
                    return Translation :: get('player_not_available');
            }
        }
    }
}
?>
