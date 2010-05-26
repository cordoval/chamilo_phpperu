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
        $this->mediamosa = new MediamosaRestClient(Platformsetting::get('mediamosa_url','mediamosa'));
        //TODO: jens -> implement curl request
        $this->mediamosa->set_connexion_mode(RestClient :: MODE_PEAR);
        //login if connector cookie doesn't exist
        //connector cookie takes care of login persistence
        if(!$this->mediamosa->get_connector_cookie())
        {
            $this->mediamosa->login(PlatformSetting::get('mediamosa_username','mediamosa'), PlatformSetting::get('mediamosa_password','mediamosa'));
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

    
    /*
     * @param simple xml element
     * @return array
     */
    function get_mediamosa_mediafiles($mediafiles)
    {


        foreach ($mediafiles as $items) {
            foreach ($items as $mediafile) {


            }
        }
    }

    function get_default_asset_mediafile(){}

    function get_mediamosa_mediafile($mediafile_id){}

    function get_mediamosa_assets($condition, $order_property, $offset, $count)
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
     * @param object simple xml element
     * @return mediamosa object
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

    function is_editable($id)
    {
        
    }
}
?>
