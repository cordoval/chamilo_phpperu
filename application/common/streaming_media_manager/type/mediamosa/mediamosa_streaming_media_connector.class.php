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

class MediamosaStreamingMediaConnector {

    private static $instance;
    private $manager;
    private $mediamosa;

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
            $ok = $this->mediamosa->login(PlatformSetting::get('mediamosa_username','admin'), PlatformSetting::get('mediamosa_password','admin'));
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

    function get_mediamosa_asset($asset_id)
    {
        $asset = $this->request_mediamosa_asset($asset_id);
        return $this->create_object($asset);
    }
    
    function request_mediamosa_asset($asset_id)
    {
        return $this->mediamosa->request('GET',sprintf('/asset/%s', $asset_id));
    }

    
    

    function retrieve_default_asset_mediafile(){}

    
    /*
     * searchable retrieve assets on a mediamosa server
     * @param string condition optional
     * @param string order_property optional
     * @param string offset optional
     * @param string count optional
     * @return array objects
     */
    function retrieve_mediamosa_assets($condition, $order_property, $offset, $count)
    {
        if($order_property) $params['order_by'] = $order_property;
        if($offset) $params['offset'] = $offset;
        if($limit) $params['count'] = $limit;

        if($condition) $condition = sprintf('&%'.$condition);

        $response = $this->mediamosa->request('GET','/asset'.$condition, $params);
        $objects = array();

        if(isset($response->items->item))
        {
            foreach($response->items->item as $asset)
            {
                $objects[] = $this->create_object($asset);
            }
        }
        return $objects;
    }

    /*
     * creates and populates a MediamosaStreamingMediaObject with xml data
     * @param object simple xml element
     * @return MediamosaStreamingMediaObject
     */
    function create_object($asset){
        
        $mediamosa_asset =  new MediamosaStreamingMediaObject();

        $mediamosa_asset->set_id($asset->asset_id);
        $mediamosa_asset->set_title((string)$asset->dublin_core->title);
        //$metadata['language'] = (string)$asset->dublin_core->language;
        //$metadata['subject'] = (string)$asset->dublin_core->subject;
        $mediamosa_asset->set_description((string)$asset->dublin_core->description);
        //$metadata['contributor'] = (string)$asset->dublin_core->contributor;
        $mediamosa_asset->set_publisher((string)$asset->dublin_core->publisher);
        $mediamosa_asset->set_date((string)$asset->dublin_core->date);
        ($url = (string)$asset->vpx_still_url) ? $mediamosa_asset->set_thumbnail($url) : FALSE;
        //TODO: jens -> activate parameter?
        //$metadata['created'] = (string)$asset->qualified_dublin_core->created;

        return $mediamosa_asset;
    }

    function is_editable($id){}

    /*
     * create an asset on mediamosa server
     * @return string asset_id
     */
    function create_mediamosa_asset(){}

    /*
     * retrieve an asset on mediamosa server
     * @param string asset_id
     * @return object simple xml element
     */
    function retrieve_mediamosa_asset($asset_id){}

    /*
     * remove an asset on mediamosa server
     * @return boolean
     */
    function remove_mediamosa_asset(){}

    /*
     * create a mediamosa mediafile
     * @param string asset_id
     * @return string mediafile_id
     */
    function create_mediamosa_mediafile($asset_id){}

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
     * @param file
     * @return boolean
     */
    function remove_mediamosa_mediafile($file){}

    /*
     * create or update metadata for an asset
     * @param MediamosaStreamingMediaObject
     * @return boolean
     */
    function add_mediamosa_metadata($mediamosa_streaming_media_object){}
    
    /*
     * requests a upload ticket from server
     * @param string mediafile_id
     * @return string ticket_id
     */
    function create_mediamosa_upload_ticket($mediafile_id){}

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
    function retrieve_mediamosa_transcoding_profiles(){}

    /*
     * creates a transcoding job on the mediamosa server
     * if a transcoding_profile_id is not provided it will transcode to the default profile(s)
     * @param string mediafile_id
     * @param string transcoding_profile_id
     * @return int job_id
     */
    function transcode_mediamosa_mediafile($mediafile_id, $transcoding_profile_id = null){}


}
?>
