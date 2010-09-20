<?php
/**
 * connection to mediamosa-server
 *
 * via REST protocol
 * uses mediamosa rest client
 *
 * @author jevdheyd
 */

require_once dirname(__FILE__) . '/webservices/mediamosa_rest_client.class.php';
require_once dirname(__FILE__) . '/mediamosa_mediafile_object.class.php';
require_once dirname(__FILE__) . '/mediamosa_external_repository_object.class.php';

class MediamosaExternalRepositoryConnector extends ExternalRepositoryConnector
{
    private $mediamosa;
    private $profiles;
    private $chamilo_user;
    private $asset_cache = array();
    private static $user_id_prefix;
    private $count;
    private $cql = array();
    private $simulate = false;
    private $cql_error;
    private $app_id;
    private $user_groups;

    const METHOD_POST = MediamosaRestClient :: METHOD_POST;
    const METHOD_GET = MediamosaRestClient :: METHOD_GET;
    const METHOD_PUT = MediamosaRestClient :: METHOD_PUT;
    const REDIRECT_URL = '';
    const PLACEHOLDER_URL = 'http://localhost/chamilo_2.0/layout/aqua/images/common/content_object/big/streaming_video_clip.png';

    function MediamosaExternalRepositoryConnector($external_repository_instance) {
        parent :: __construct($external_repository_instance);

        if (! $this->login()) {
            exit(Translation :: get('Connection to Mediamosa server failed'));
        }
    }

    function get_connector_cookie()
    {
        return $this->mediamosa->get_connector_cookie();
    }

//    function get_user_id_prefix() {
//        if(!$this->user_id_prefix) {
//            $this->user_id_prefix = $this->get_app_id() . '_';
//            //$this->user_id_prefix = '';
//        }
//        return $this->user_id_prefix;
//    }

//    function get_mediamosa_user_id($user_id)
//    {
//        if(!$this->user_id_prefix) $this->get_user_id_prefix();
//        return $this->user_id_prefix . $user_id;
//    }

//    function get_mediamosa_group_id($group_id)
//    {
//        if(!$this->user_id_prefix) $this->get_user_id_prefix();
//        return $this->user_id_prefix  . $group_id;
//    }

    function retrieve_chamilo_user($user_id) {
        $udm = UserDataManager :: get_instance();

        if (! $this->chamilo_user or ($user_id != $this->chamilo_user->get_id())) {
            $this->chamilo_user = $udm->retrieve_user($user_id);
        }
        return $this->chamilo_user;

    }

    function create_mediamosa_user($chamilo_user_id, $quotum = null) {
        if ($chamilo_user_id) {
            $data = array();

            if ($quotum) $data['quotum'] = $quotum;
            $data['user'] = $chamilo_user_id;

            if ($response = $this->request(self :: METHOD_POST, '/user/create', $data)) {
                if ($response->check_result()) {
                    return true;
                }
            }
        }
        return false;
    }



    /*
     * @param int user_id
     * @param int quotum
     * @return boolean
    */
    function set_mediamosa_user_quotum($chamilo_user_id, $quotum) {
        if ($chamilo_user_id && $quotum) {
            $data = array();
            $data['quotum'] = $quotum;

            if ($response = $this->request(self :: METHOD_POST, '/user/' . $chamilo_user_id, $data)) {
                if ($response->check_result())
                    return true;

                if ($this->create_mediamosa_user($chamilo_user_id, $quotum))
                    return true;
            }
        }
        return false;
    }

    function set_mediamosa_default_user_quotum($user_id) {
        $quotum = ExternalRepositorySetting :: get('default_user_quotum');
        if ($this->set_mediamosa_user_quotum($user_id, $quotum)) {
            return true;
        }
        return false;
    }



    /*
     * @param int $user_id
     * @return simplexmlobject user
    */
    function retrieve_mediamosa_user($chamilo_user_id) {
        if ($response = $this->request(self :: METHOD_GET, '/user/' . $chamilo_user_id)) {
            if ($response->check_result()) {
                return $response->get_response_content_xml()->items->item;
            }
            elseif((string) $response->get_response_content_xml()->header->request_result_description == 'Invalid username')
            {
                $rdm = RepositoryDataManager :: get_instance();
                if($special_quotum = $rdm->retrieve_external_repository_user_quotum($chamilo_user_id, $this->get_external_repository_instance_id()))
                {
                    $quotum = $special_quotum->get_quotum();
                }
                else
                {
                    $quotum = ExternalRepositorySetting :: get('default_user_quotum', $this->get_external_repository_instance_id());
                }
                return $this->create_mediamosa_user($chamilo_user_id, $quotum);
            }
        }

        return false;
    }

    /*
     * returns mediamosa rest version
     * @return string version
    */
    function retrieve_mediamosa_version() {
        if ($response = $this->request(self :: METHOD_GET, '/version/')) {
            if ($response->check_result()) {
                $xml = $response->get_response_content_xml();
                return $xml->items->item->version;
            }
        }

        return false;
    }

    function login() {
        //$url = ExternalRepositorySetting :: factory('url', $this->get_external_repository_instance_id());
        $url = ExternalRepositorySetting :: get('url', $this->get_external_repository_instance_id());
        $this->mediamosa = new MediamosaRestClient($url);
        //TODO: jens -> implement curl request
        $this->mediamosa->set_connexion_mode(RestClient :: MODE_PEAR);
        //login if connector cookie doesn't exist
        //connector cookie takes care of login persistence
        if (! $this->mediamosa->get_connector_cookie()) {
            //set proxy if necessary
            if (PlatformSetting :: get('proxy_settings_active', 'admin'))
                $this->mediamosa->set_proxy(PlatformSetting :: get('proxy_server', 'admin'), PlatformSetting :: get('proxy_port', 'admin'), PlatformSetting :: get('proxy_username', 'admin'), PlatformSetting :: get('proxy_password', 'admin'));

            if ($this->mediamosa->login(ExternalRepositorySetting :: get('login', $this->get_external_repository_instance_id()), ExternalRepositorySetting :: get('password', $this->get_external_repository_instance_id()))) {
                return true;
            }
        }
        return false;
    }

    /*
     * retrieves the default mediafile for an asset
     * @param $asset_id
     * @return $mediafile_id
    */
    function retrieve_mediamosa_asset_default_mediafile($asset_id) {
        if ($profiles = $this->retrieve_mediamosa_transcoding_profiles()) {
            foreach ($profiles as $profile_id => $profile) {
                if ($profile[MediamosaMediafileObject :: PROPERTY_IS_DEFAULT] == 'TRUE')
                    $default_transcode_profile_id = $profile_id;
            }

            if ($asset = $this->retrieve_mediamosa_asset($asset_id, false)) {
                foreach ($asset->items->item->mediafiles as $mediafile) {
                    if ($mediafile->transcode_profile_id == $default_transcode_profile_id) {
                        return $mediafile->mediafile_id;
                    }
                }
            }
        }
        return false;
    }



    /**
     * searchable retrieve assets on a mediamosa server
     * @param string condition optional
     * @param string order_property optional
     * @param string offset optional
     * @param string count optional
     * @return array with MediamosaExternalRepositoryObject(s)
     */
    function retrieve_external_repository_objects($condition, $order_property, $offset, $count) {

        $feed_type = Request :: get(MediamosaExternalRepositoryManager :: PARAM_FEED_TYPE);
        
        switch($feed_type)
        {
            
            case MediamosaExternalRepositoryManager :: FEED_TYPE_MOST_RECENT:
                
                $this->cql['sortby']['name'] = 'date';
                $this->cql['sortby']['order']='descending';
                $response = $this->retrieve_mediamosa_assets($condition, $order_property, $offset, 9);
                break;
            case MediamosaExternalRepositoryManager :: FEED_TYPE_MY_VIDEOS:
                $this->cql ['AND'][] = array(
                        'name' => 'owner_id',
                        'value' => '^' . Session :: get_user_id() . '^'
                        );
                $response = $this->retrieve_mediamosa_assets($condition, $order_property, $offset, $count, $cql);
                break;
            case MediamosaExternalRepositoryManager :: FEED_TYPE_ALL:
                 if($this->retrieve_chamilo_user(Session :: get_user_id())->is_platform_admin())
                 {
                    $response = $this->retrieve_mediamosa_assets($condition, $order_property, $offset, $count);
                 }
            break;
            case MediamosaExternalRepositoryManager :: FEED_TYPE_GENERAL:
            default:
                 $owner['name'] = 'owner_id';
                 $owner['value'] = '^' . Session :: get_user_id() . '^';
                 $this->cql['OR'][] = $owner;

                 $aut_user['name'] = 'aut_user';
                 $aut_user['value'] = '^' . Session :: get_user_id() . '^';
                 $this->cql['OR'][] = $aut_user;

                 $this->get_user_groups(true);

                 $app_id = $this->get_app_id();
                 if(!empty($app_id))
                 {
                    $aut_app['name'] = 'aut_app';
                    $aut_app['value'] = '^' . $this->get_app_id() . '^';
                    $this->cql['OR'][] = $aut_app;
                 }
                 
                $response = $this->retrieve_mediamosa_assets($condition, $order_property, $offset, $count);
             break;
        }

        if($response)
        {
            $this->count = 0;
            $objects =array();
            if ($response->check_result()) {
                $xml = $response->get_response_content_xml();

                if (isset($xml->items->item)) {
                    foreach ($xml->items->item as $asset)
                    {
                        if(!isset($this->asset_cache[(string) $asset->asset_id]))
                        {
                            $object = $this->create_mediamosa_external_repository_object($asset);

                            $asset_rights = $object->get_rights();

                            $objects[] = $object;
                            $this->asset_cache[(string) $asset->asset_id] = $object;
                            $this->count++;

                            if($update_master_slave) $this->update_asset_master_slave_settings($object);
                        }
                        else
                        {
                            $objects[] = $this->asset_cache[(string) $asset->asset_id];
                        }
                    }
                }
            }
            return new ArrayResultSet($objects);
        }
    }

    function retrieve_mediamosa_assets($condition, $order_property, $offset, $count, $update_master_slave = false)
    {
        $params = array();
        $acl = array();

        $chamilo_user = $this->retrieve_chamilo_user(Session :: get_user_id());

        if ($order_property) {
            if (is_array($order_property)) {
                $params['order_by'] = $order_property[0];
            }
            else {
                $params['order_by'] = $order_property;
            }
        }

        if ($count) {
            $params['limit'] = $count;
        }
        else {
            $params['limit'] = 200; //this is max liit according to mediamosa
        }

        if($offset) $params['offset'] = $offset;
        $params['user_id'] = $chamilo_user->get_id();
        $params['app_id'] = $this->get_app_id();
        $params['hide_empty_assets'] = 'TRUE';
        if ($chamilo_user->is_platform_admin()) $params['is_app_admin'] = 'TRUE';

        if($condition) $this->create_cql_sets($condition);
        $cql = $this->create_cql_query();

        if($this->cql_error)$params['limit'] = 0;

        $params['cql'] = urlencode($cql);
       
        if ($response = $this->request(self :: METHOD_GET, '/asset', $params)) {
            return $response;
        }
    }

    

    function create_cql_sets($searchString, $delimiter = 'OR') {
        if(! empty($searchString) && strlen($searchString) > 1) {
            $searchString = trim(addslashes($searchString));
            foreach(MediamosaExternalRepositoryObject :: get_searchable_property_names() as $tag) {
                $set['value'] = $searchString;
                $set['name'] = $tag;
                $this->cql[$delimiter][] = $set;
            }
        }
        else
        {
            $this->cql_error =1;
        }
    }

    function create_cql_query()
    {
        foreach($this->cql as $delimiter => $sets)
        {
             $i = 0;
            foreach($sets as $set)
            {
               
                if($delimiter != 'sortby')
                {
                    
                    $string .= $set['name'] . '== "' . $set['value'] . '"';
                    $i++;
                    $la = count($this->cql);
                    if($i < count($this->cql[$delimiter])) $string .= ' ' . $delimiter . ' ';

                }
                else
                {
                    if($delimiter == 'sortby')
                    {
                        $sort = $this->cql['sortby']['name'];
                        $order = $this->cql['sortby']['order'];
                        unset($this->cql['sortby']);
                        break;
                    }
                }
            }
        }

        //if(!empty($string)) $string .= 'AND';
        //$string .= 'app_id == "^' . $this->get_app_id() . '^"';

        if($sort && $order)
        {
            $string .= ' sortby ' . $sort.'/'.$order;
        }
        else
        {
            $string .= ' sortby title/sort.ascending';
        }

        $this->cql = array();
        echo $string;
        return $string;
    }

    function get_app_id()
    {
        if(!$this->app_id)
        {
            $this->app_id = ExternalRepositorySetting :: get('app_id', $this->get_external_repository_instance_id());
        }
        return $this->app_id;
    }

    function get_user_groups($cql = false)
    {
        if(!$this->user_groups)
        {
            $gdm = GroupDataManager :: get_instance();
            $groups = $gdm->retrieve_user_groups(Session :: get_user_id());

            if ($groups->size()) {

                while ($g = $groups->next_result()) {

                    $this->user_groups[$g->get_group_id()] = true;
                }
            }
        }

        if($cql == true)
        {
            foreach($this->user_groups as $nextGroup => $val)
            {
                $aut_group['name'] = 'aut_group';
                $aut_group['value'] = '^' . $nextGroup . '^';
                $this->cql['OR'][] = $aut_group;
            }
        }

        return $this->user_groups;
    }

    function count_external_repository_objects($condition) {
        return $this->count_mediamosa_assets($condition);
    }

    function count_mediamosa_assets($condition, $order_property, $offset, $count, $recount = false) {
        if (! count($this->asset_cache) || $recount == true) {
            $this->retrieve_external_repository_objects($condition, $order_property, $offset, $count);
        }

        return $this->count;
    }

    /*
     * creates and populates a MediamosaExternalRepositoryObject with xml data
     * @param object simple xml element
     * @return MediamosaExternalRepositoryObject
    */
    function create_mediamosa_external_repository_object($asset) {
        if ($asset) {
            $mediamosa_asset = new MediamosaExternalRepositoryObject();

            $mediamosa_asset->set_id((string) $asset->asset_id);
            $mediamosa_asset->set_title((string) $asset->dublin_core->title);
            $mediamosa_asset->set_created(strtotime($asset->videotimestamp));
            $mediamosa_asset->set_modified(strtotime($asset->videotimestampmodified));
            $mediamosa_asset->set_owner_id((int) $asset->owner_id);
            //$metadata['language'] = (string)$asset->dublin_core->language;
            //$metadata['subject'] = (string)$asset->dublin_core->subject;
            $mediamosa_asset->set_description((string) $asset->dublin_core->description);
            //$metadata['contributor'] = (string)$asset->dublin_core->contributor;
            $mediamosa_asset->set_publisher((string) $asset->dublin_core->publisher);
            $mediamosa_asset->set_date((string) $asset->dublin_core->date);
            ((string) $asset->vpx_still_url) ? $mediamosa_asset->set_thumbnail((string) $asset->vpx_still_url) : $mediamosa_asset->set_thumbnail(self :: PLACEHOLDER_URL);
            $mediamosa_asset->set_creator((string) $asset->dublin_core->creator);
            $mediamosa_asset->set_type(MediamosaExternalRepositoryObject :: OBJECT_TYPE);
            $mediamosa_asset->set_owner_id((string) $asset->owner_id); //owner id = mediamosa id
            $mediamosa_asset->set_protected((string) $asset->is_protected);

            //rights -- determine if the asset is protected for this user or not
            //$mediamosa_asset->set_rights($this->determine_rights($asset));

            //status of mediafile is unavailable by default
            $mediamosa_asset->set_status(MediamosaExternalRepositoryObject :: STATUS_UNAVAILABLE);

            $mediamosa_transcoding_profiles = $this->retrieve_mediamosa_transcoding_profiles();

            $mediafile_count = 0;
            $not_downloadable = 0;
            //add mediafiles
            foreach ($asset->mediafiles->mediafile as $mediafile)
            {
                //if there is still an original mediafile
                //see if it can be removed
                if ((string) $mediafile->is_original_file == 'TRUE' && ExternalRepositorySetting :: get('remove_originals', $this->get_external_repository_instance_id())) {
                    $this->remove_mediamosa_original_mediafile($asset);
                }
                

                //duration is retrieved from one of the mediafiles
                if (! $mediamosa_asset->get_duration()) {
                    $duration = substr((string) $mediafile->metadata->file_duration, 3, 5);
                    $mediamosa_asset->set_duration($duration);
                }

                
                $mediamosa_mediafile = new MediamosaMediafileObject();
                $mediamosa_mediafile->set_id((string) $mediafile->mediafile_id);

                /*if(isset($mediamosa_transcoding_profiles[(string) $mediafile->transcode_profile_id][MediamosaMediafileObject :: PROPERTY_TITLE]))
                {
                    $mediamosa_mediafile->set_title($mediamosa_transcoding_profiles[(string) $mediafile->transcode_profile_id][MediamosaMediafileObject :: PROPERTY_TITLE]);
                }
                else
                {*/
                $title = (string) $mediafile->metadata->container_type . ' (' . (string) $mediafile->metadata->width . ' x ' . (string) $mediafile->metadata->height . ' px)';
                $mediamosa_mediafile->set_title($title);

                //}


                if ($mediamosa_transcoding_profiles[(string) $mediafile->transcode_profile_id][MediamosaMediafileObject :: PROPERTY_IS_DEFAULT] == 'TRUE') {
                    $mediamosa_mediafile->set_is_default();
                }
                $original_mediafile = $mediamosa_asset->get_original_mediafile();

                if ($mediafile->is_downloadable == 'TRUE') {
                    $mediamosa_mediafile->set_is_downloadable();
                }
                else
                {
                    $not_downloadable ++;
                }

                if ($mediamosa_mediafile->get_is_default()) {
                    $mediamosa_asset->set_default_mediafile((string) $mediafile->mediafile_id);
                }

                //if there is a playable mediafile - set status available
                $mediamosa_asset->set_status(MediamosaExternalRepositoryObject :: STATUS_AVAILABLE);

                if ((string) $mediafile->transcode_profile_id)
                {
                    $mediamosa_asset->add_mediafile($mediamosa_mediafile);
                }
                if ((string) $mediafile->is_original_file == 'TRUE') $mediamosa_asset->set_original_mediafile($mediamosa_mediafile);
                
                $mediafile_count ++;
            }
            
            //if($mediafile_count != $not_downloadable) $this->update_mediafile_downloadableness($mediamosa_asset);

            $mediamosa_asset->set_rights($this->determine_rights($mediamosa_asset));

            return $mediamosa_asset;
        }
        return false;
    }
    
    function update_mediafile_downloadableness(MediamosaExtrenalRepositoryObject $mediamosa_asset)
    {
        if($original_mediafile = $mediamosa_asset->get_original_mediafile())
        {
            if($original_mediafile->get_is_downloadable())
            {
                foreach($mediamosa_asset->get_mediafiles() as $mediafile)
                {
                    if($mediafile->get_id() != $original_mediafile)
                    {
                        if($this->update_mediamosa_mediafile($mediafile->get_id(), $data = array('is_downloadable' => 'TRUE')))
                        {
                            $mediafile->set_is_downloadable();
                            $mediamosa_asset->add_mediafile($mediafile);
                        }
                    }
                }
            }
        }
    }
    
    /*
     * determines chamilo rights for a mediamosa asset depending on mediamosa acl rights
     * 
     * who has rights?
     * platform_admin - use, edit delete
     * owner (publisher) - use, edit, delete
     * whose id equals aut_app -  use
     * who is in a group whose id equals aut_group - use
     *
     * no mediamosa rights means no rights in chamilo;
     *
     * @param MediamosaExternalRepositoryObject $asset
     * @return array asset_rights
     */
    function determine_rights(MediamosaExternalRepositoryObject $asset) {

        $asset_rights = array();

        $chamilo_user = $this->retrieve_chamilo_user(Session :: get_user_id());

        if($chamilo_user->is_platform_admin() or $chamilo_user->get_id() == $asset->get_owner_id())
        {
            $asset_rights[ExternalRepositoryObject :: RIGHT_USE] = true;
        }
        else
        {
            $asset_rights[ExternalRepositoryObject :: RIGHT_USE] = false;
        }

        if ((string) $asset->get_protected() != 'FALSE')
        {
            //if no group or user rights are set --> use right
            $rights = $this->retrieve_mediamosa_asset_rights($asset->get_id(), $asset->get_owner_id());

            //if(!count($rights['aut_user']) && !count($rights['aut_group'])) $asset_rights[ExternalRepositoryObject :: RIGHT_USE] = true;

            //check users
            if (count($rights['aut_user'])) {
                foreach ($rights['aut_user'] as $n => $aut_user) {
                    if ($aut_user == Session :: get_user_id()) {
                        $asset_rights[ExternalRepositoryObject :: RIGHT_USE] = true;
                    }
                }
            }

            //check groups
            if (count($rights['aut_group']))
            {
                $groups =$this->get_user_groups();
                
                if (count($groups)) {
                    foreach ($rights['aut_group'] as $n => $aut_group) {
                        if (isset($groups[$aut_group])) {
                            $asset_rights[ExternalRepositoryObject :: RIGHT_USE] = true;
                        }
                    }
                }
            }

            //check aut_apps
            if(count($rights['aut_app']))
            {
                $this_app = $this->get_app_id() . '^';

                foreach($rights['aut_app'] as $n => $aut_app)
                {
                    if($aut_app == $this_app) $asset_rights[ExternalRepositoryObject :: RIGHT_USE] = true;
                }
            }
        }

        if ($chamilo_user->is_platform_admin() || ($asset->get_owner_id() == $chamilo_user->get_id())) {
            $asset_rights[ExternalRepositoryObject :: RIGHT_EDIT] = true;
            $asset_rights[ExternalRepositoryObject :: RIGHT_DELETE] = true;
        }
        else {
            $asset_rights[ExternalRepositoryObject :: RIGHT_EDIT] = false;
            $asset_rights[ExternalRepositoryObject :: RIGHT_DELETE] = false;
        }
        
        if($original = $asset->get_original_mediafile())
        {
            if ($original->get_is_downloadable() && $asset_rights[ExternalRepositoryObject :: RIGHT_USE] == true)
            {
                $asset_rights[ExternalRepositoryObject :: RIGHT_DOWNLOAD] = true;
            }
        }
       
        else
        {
            $asset_rights[ExternalRepositoryObject :: RIGHT_DOWNLOAD] = false;
        }

        return $asset_rights;
    }

    
    function update_asset_master_slave_settings(MediamosaExternalRepositoryObject $asset) {

        //update master/slave settings if necessary
        //get general settings
        $slaves = explode('|', ExternalRepositorySetting :: get('slave_app_ids', $this->get_external_repository_instance_id()));
        $slaves_flip = array_flip($slaves);
        $ok = 0;

        $rights = $this->retrieve_mediamosa_asset_rights($asset->get_id(), $asset->get_owner_id());

        foreach($rights['aut_app'] as $n => $aut_app) {
            if($slaves_flip[$aut_app]) $ok++;
        }

        if($ok != count($slaves)) {
            $rights['aut_app'] = $slaves;
            $this->set_mediamosa_asset_rights($asset->get_id(), $rights, $asset->get_owner_id());
        }
    }

    /*
     * if all transcoding profiles are provided, the original is removed
    */
    function remove_mediamosa_original_mediafile($asset) {
        $mediamosa_transcoding_profiles = $this->retrieve_mediamosa_transcoding_profiles();
        $n_transcoded = 0;

        foreach ($asset->mediafiles->mediafile as $mediafile) {
            //if the mediafile is a transcode to a provided profile
            if (isset($mediamosa_transcoding_profiles[(string) $mediafile->transcode_profile_id])) {
                $n_transcoded ++;
            }

            //get original mediafile
            if ((string) $mediafile->is_original_file == 'TRUE') {
                $original_mediafile_id = (string) $mediafile->mediafile_id;
            }
        }

        //if all files are transcoded
        if ($n_transcoded == count($mediamosa_transcoding_profiles)) {

            //if there still is an original mediafile
            if ($original_mediafile_id) {
                //remove original mediafile
                $this->remove_mediamosa_mediafile($original_mediafile_id);
            }

        }
    }

    function retrieve_mediamosa_asset_rights($asset_id, $owner_id) {
        $data = array();
        $data['user_id'] = $owner_id;

        if ($response = $this->request(self :: METHOD_GET, '/asset/' . $asset_id . '/acl', $data)) {
            if ($response->check_result($response)) {
                $rights = array();

                foreach ($response->get_response_content_xml()->items->item as $item) {
                    foreach ($item->children() as $right) {
                        $rights[$right->getName()][] = (string) $right;
                    }

                }
                return $rights;
            }
        }

        return false;
    }

    /*
     * create an asset on mediamosa server
     * @return string asset_id
    */
    function create_mediamosa_asset() {
        $data = array();
        $data['user_id'] = Session :: get_user_id();

        if ($response = $this->request(self :: METHOD_POST, '/asset/create', $data)) {
            if ($response->check_result($response)) {
                return (string) $response->get_response_content_xml()->items->item->asset_id;
            }
        }

        return false;
    }

    function retrieve_external_repository_object($id) {
        return $this->retrieve_mediamosa_asset($id, true);
    }

    /*
     * retrieve an asset on mediamosa server
     * @param string asset_id
     * @param boolean object
     * @return MediamosaExternalRepositoryObject or simplexmlelement
    */
    function retrieve_mediamosa_asset($asset_id, $object = true) {

        if ($asset_id) {
            $data = array();
            $data['user_id'] = Session :: get_user_id();

            if ($response = $this->request(self :: METHOD_GET, '/asset/' . $asset_id, $data)) {
                if ($response->check_result()) {
                    $xml = $response->get_response_content_xml()->items->item;

                    if ($object) {
                        $object = $this->create_mediamosa_external_repository_object($xml);
                        $this->asset_cache[(string) $xml->asset_id] = $object;
                        return $object;
                    }
                    else {
                        return $xml;
                    }
                }
            }
        }
        return false;
    }

    function delete_external_repository_object($id) {
        return $this->remove_mediamosa_asset($id, true);
    }

    /*
     * remove an asset on mediamosa server
     * @param boolean cascade (delete all underlying mediafiles + jobs as well)
     * @return boolean
    */
    function remove_mediamosa_asset($asset_id, $cascade = true) {
        if ($asset_id) {
            $data = array();

            $chamilo_user = $this->retrieve_chamilo_user(Session :: get_user_id());

            if($chamilo_user->is_platform_admin()) {
                if(!$asset = $this->asset_cache[$asset_id]) {
                    $asset = $this->retrieve_external_repository_object($asset_id, 1);
                }
                $data['user_id'] = $asset->get_owner_id();
            }
            else {
                $data['user_id'] = Session :: get_user_id();
            }

            if ($cascade == true) {
                $data['delete'] = 'cascade';
            }

            if ($response = $this->request(self :: METHOD_POST, '/asset/' . $asset_id . '/delete', $data)) {
                if ($response->check_result()) {
                    //TODO:return result description on fail
                    return true;
                }
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
    function remove_mediamosa_assets($asset_ids, $cascade = true) {
        if (is_array($asset_ids)) {
            $data = array();
            $data['asset_id'] = $asset_ids;
            $data['user_id'] = Session :: get_user_id();
            if ($cascade == true) {
                $data['delete'] = 'cascade';
            }

            if ($response = $this->request(self :: METHOD_POST, '/asset/delete', $data)) {
                if ($response->check_result()) {
                    $xml = $response->get_response_content_xml();

                    $output = array();
                    foreach ($xml->items as $item) {
                        $output[] = (string) $item->asset_id . (string) $item->result . ':' . (string) $item->result_description;
                    }
                    return $output;
                }
            }

        }
        return false;
    }

    function export_external_repository_object($object) {
        return true;
    }

    /*
     * create a mediamosa mediafile
     * @param string asset_id
     * @return string mediafile_id
    */
    function create_mediamosa_mediafile($asset_id, $is_downloadable = false) {
        if ($asset_id) {
            $data['user_id'] = Session :: get_user_id();
            $data['asset_id'] = $asset_id;
            if ($is_downloadable) $data['is_downloadable'] = 'TRUE';

            if ($response = $this->request(self :: METHOD_POST, '/mediafile/create', $data)) {
                if ($response->check_result()) {
                    return (string) $response->get_response_content_xml()->items->item->mediafile_id;
                }
            }

        }
        return false;
    }

    function update_mediamosa_mediafile($mediafile_id, $data = array())
    {
        if ($mediafile_id) {
            
            $data['user_id'] = Session :: get_user_id();

            if ($response = $this->request(self :: METHOD_POST, '/mediafile/' . $mediafile_id, $data)) {
                if ($response->check_result()) {
                    return (string) $response->get_response_content_xml()->items->item->mediafile_id;
                }
            }
        }
        return false;
    }

    /*
     * remove a mediamosa mediafile
     * @param mediafile_id
     * @return boolean
    */
    function remove_mediamosa_mediafile($mediafile_id) {
        if ($mediafile_id) {
            $data = array();
            $data['user_id'] = Session :: get_user_id();

            if ($response = $this->request(self :: METHOD_POST, 'mediafile/' . $mediafile_id . '/delete', $data)) {
                if ($response->check_result()) {
                    return true;
                }
            }

        }
        return false;
    }

    /*
     * create or update metadata for an asset
     * @param string asset_id
     * @return boolean
    */
    function add_mediamosa_metadata($asset_id, $data) {
        if ($asset_id) {
            if (is_array($data)) {
                $data['user_id'] = Session :: get_user_id();
                //if metadata exists -> overwrite
                //TODO : check if these properties also apply when updating metadata
                $data['replace'] = 'TRUE';
                //$data['action'] = 'update';


                if ($response = $this->request(self :: METHOD_POST, '/asset/' . $asset_id . '/metadata', $data)) {
                    if ($response->check_result($response)) {
                        return true;
                    }
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
    function create_mediamosa_upload_ticket($mediafile_id) {

        if ($mediafile_id) {
            $data = array();
            $data['user_id'] = Session :: get_user_id();
            //$data['mediafile_id'] = $mediafile_id; //TODO : necessary?


            if ($response = $this->request(self :: METHOD_GET, '/mediafile/' . $mediafile_id . '/uploadticket/create', $data)) {
                if ($response->check_result($response)) {
                    return $response->get_response_content_xml();
                }
            }

        }
        return false;
    }

    /*
     * creates a still for a certain mediafile on the mediamosa server
     * @param string mediafile
     * @return bool
    */
    /*function create_mediamosa_mediafile_still($mediafile_id)
    {
        $data = array();
            $data['still_type'] = ;
            //$data['mediafile_id'] = $mediafile_id; //TODO : necessary?

            if($response = $this->request(self :: METHOD_GET, '/mediafile/'.$mediafile_id.'/still/create', $data))
            {
                if($response->check_result($response))
                {
                    return true;
                }
            }

        }
        return false;
    }*/

    /*
     * retrieves all mediamosa profiles
     * @return array
    */
    function retrieve_mediamosa_transcoding_profiles() {
        if (! $this->profiles)
        {
            $data = array();

            if ($response = $this->request(self :: METHOD_GET, '/transcode/profile')) {
                if ($response->check_result())
                {
                    $profiles = array();

                    foreach ($response->get_response_content_xml()->items->item as $profile) {
                        $profiles[(string) $profile->profile_id] = array(MediamosaMediafileObject :: PROPERTY_TITLE => (string) $profile->profile, MediamosaMediafileObject :: PROPERTY_IS_DEFAULT => (string) $profile->default);
                    }

                    $this->profiles = $profiles;

                    return $profiles;
                }
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
    function transcode_mediamosa_mediafile($mediafile_id, $transcoding_profile_id = null) {
    }

    function clean() {
        if ($response = $this->retrieve_mediamosa_assets(null, 'title', null)) {
            $asset_ids = array();

            foreach ($response as $n => $mediamosa_external_repository_object) {
                $asset_ids[] = $mediamosa_external_repository_object->get_id();
            }

            if ($response = $this->remove_mediamosa_assets($asset_ids)) {
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
    function mediamosa_play_proxy_request($asset_id, $mediafile_id = 'default', $response = 'object') {
        if ($asset_id) {
            //retrieve default mediafile (mostly in case no mediafile is supplied)
            if ($mediafile_id == 'default') {
                $mediafile_id = $this->retrieve_mediamosa_asset_default_mediafile($asset_id);

                if (! $mediafile_id)
                    return false;
            }

            //prepare request data
            $data = array();
            $data['mediafile_id'] = $mediafile_id;
            $data['response'] = $response;

            $data['user_id'] = Session :: get_user_id();
            //$data['group_id']
            //
            //get object or url
            $player = $this->request(self :: METHOD_GET, '/asset/' . $asset_id . '/play', $data);

            //verify
            switch ($player->get_response_content_xml()->header->request_result_id) {
                //if 601 -> ok
                case '601' :
                    return (string) $player->get_response_content_xml()->items->item->output;
                    break;
                //   701 -> doesn't exits
                case '701' :
                    return Translation :: get('FileDoesntExist');
                //   1800 -> not authorised
                case '1800' :
                    return Translation :: get('AccessDenied');
                //   default - no embedded player available
                default :
                    return Translation :: get('PlayerNotAvailable');
            }
        }
    }

    /*
     * @param string mediafile_id
     * @return array rights
    */
    function retrieve_mediamosa_mediafile_rights($mediafile_id) {
        if ($mediafile_id) {
            if ($response = $this->request(self :: METHOD_GET, '/mediafile/' . $mediafile_id . '/acl')) {
                if ($response->check_result()) {
                    $rights = array();

                    foreach ($response->get_response_content_xml()->items->item as $item) {
                        foreach ($item->children() as $right) {
                            $rights[$right->getName()][] = (string) $right;
                        }
                    }
                    return $rights;
                }
            }

        }
        return false;
    }

    function get_mediamosa_asset_rights($asset_id) {
        if ($asset_id) {
            if ($response = $this->request(self :: METHOD_GET, '/asset/' . $asset . '/acl')) {
                if ($response->check_result()) {
                    $rights = array();

                    foreach ($response->get_response_content_xml()->items as $item) {
                        $rights[(string) $item] = (string) $item[0];
                    }
                    return true;
                }
            }

        }
        /*if($asset = $this->retrieve_mediamosa_asset($asset_id))
        {
            $mediafiles = $asset->get_mediafiles();

            if(is_array($mediafiles))
            {
                foreach($mediafiles as $mediafile)
                {
                    return $this->get_mediamosa_mediafile_rights($mediafile->get_id());
                }
            }
        }*/
        return false;
    }

    /*
     * sets rights for the asset
     * in < 1.7.4 the rights have to be set for each individual mediafile
     *
     * @param string asset_id
     * @param array rights
     * @param bool update mediafiles - set false if only rights of asset need to be updated
     * this has to be set when this method is used within retrieve_mediamosa_asset($asset_id) otherwise a loop is created
     * @return bool
    */
    function set_mediamosa_asset_rights($asset_id, $rights, $owner_id, $update_mediafiles = true) {
        ///xdebug_break();
        if ($asset_id) {
            if (is_array($rights)) {
                $data = array();

                foreach ($rights as $k => $right) {
                    $data[$k] = $right;
                }
                $data['user_id'] = $owner_id;
                //$data['replace'] = 'true';

                if ($response = $this->request(self :: METHOD_POST, '/asset/' . $asset_id . '/acl', $data)) {
                    if ($response->check_result()) {
                        if($update_mediafiles) {
                            if($asset = $this->retrieve_mediamosa_asset($asset_id)) {
                                if (is_array($asset->get_mediafiles())) {
                                    foreach ($asset->get_mediafiles() as $mediafile) {
                                        $this->set_mediamosa_mediafile_rights($mediafile->get_id(), $rights, $owner_id);
                                    }

                                }
                            }
                        }
                    }
                }
            }
        }
        /*if($rights)
        {
            if($asset = $this->retrieve_mediamosa_asset($asset_id))
            {
                $mediafiles = $asset->get_mediafiles();

                if(is_array($mediafiles))
                {
                    foreach($mediafiles as $mediafile)
                    {
                        $this->set_mediamosa_mediafile_rights($mediafile->get_id(), $rights);
                    }
                    return true;
                }
            }

        }*/
        return false;
    }

    /*
     * @param strings mediafile_id
     * @param array rights (aut_user, aut_group, aut_domain, aut_realm)
     * @return bool
    */
    function set_mediamosa_mediafile_rights($mediafile_id, $rights, $owner_id) {
        if ($mediafile_id) {
            if (is_array($rights)) {
                $data = array();

                foreach ($rights as $k => $right) {
                    $data[$k] = $right;
                }

                $data['user_id'] = $owner_id;

                if ($response = $this->request(self :: METHOD_POST, '/mediafile/' . $mediafile_id . '/acl', $data)) {
                    if ($response->check_result()) {
                        return true;
                    }
                }

            }
        }
        return false;
    }

    function request($method, $url, $data) {
        if ($this->mediamosa) {
            return $this->mediamosa->request($method, $url, $data);
        }
        return false;
    }

    static function translate_search_query($query) {
        return $query;
    }

    function mediamosa_put_upload($filename, $url, $params)
    {
        if($filename &&  $url)
        {
            $url .= '&filename=' . $filename;
            
            if($response = $this->request(self :: METHOD_PUT, $url, $params))
            {
                if($response->check_result())
                {
                    return true;
                }
            }
            
        }
        return false;
    }

}
?>
