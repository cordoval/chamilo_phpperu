<?php
/**
 * connection to mediamosa-server
 *
 * via REST protocol
 * uses mediamosa rest client
 *
 * @author jevdheyd
 */
require_once dirname(__FILE__) . '/mediamosa_external_repository_server_object.class.php';
require_once dirname(__FILE__) . '/mediamosa_external_repository_data_manager.class.php';
require_once dirname(__FILE__) . '/webservices/mediamosa_rest_client.class.php';
require_once dirname(__FILE__) . '/mediamosa_mediafile_object.class.php';
require_once dirname(__FILE__) . '/mediamosa_external_repository_object.class.php';

class MediamosaExternalRepositoryConnector implements ExternalRepositoryConnector
{
    
    private static $instance;
    private $manager;
    private $mediamosa;
    private $profiles;
    private $chamilo_user;
    private $asset_cache = array();
    private $user_id_prefix;
    
    const METHOD_POST = MediamosaRestClient :: METHOD_POST;
    const METHOD_GET = MediamosaRestClient :: METHOD_GET;
    const METHOD_PUT = MediamosaRestClient :: METHOD_PUT;
    const REDIRECT_URL = '';
    //TODO: find correct settings
    const PLACEHOLDER_URL = 'http://localhost/chamilo_2.0/layout/aqua/images/common/content_object/big/streaming_video_clip.png';

    function MediamosaExternalRepositoryConnector($server_id = null, $do_login = true)
    {        
        if ($do_login)
        {
            if (! $this->login())
            {
                exit(Translation :: get('Connection to Mediamosa server failed'));
            }
        }
    }

    function get_mediamosa_user_id($user_id)
    {
        return $this->user_id_prefix . $user_id;
    }

    function retrieve_chamilo_user($user_id)
    {
        $udm = UserDataManager :: get_instance();
        
        if (! $this->chamilo_user or ($user_id != $this->chamilo_user->get_id()))
        {
            $this->chamilo_user = $udm->retrieve_user($user_id);
        }
        return $this->chamilo_user;
    
    }

    function create_mediamosa_user($user_id, $quotum = null)
    {
        if ($user_id)
        {
            $data = array();
            if ($quotum)
                $data['quotum'] = $quotum;
            $data['user'] = $this->get_mediamosa_user_id($user_id);
            
            if ($response = $this->request(self :: METHOD_POST, '/user/create', $data))
            {
                if ($response->check_result())
                {
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
    function set_mediamosa_user_quotum($user_id, $quotum)
    {
        if ($user_id && $quotum)
        {
            $data = array();
            $data['quotum'] = $quotum;
            
            if ($response = $this->request(self :: METHOD_POST, '/user/' . $this->get_mediamosa_user_id($user_id), $data))
            {
                if ($response->check_result())
                    return true;
                
                if ($this->create_mediamosa_user($user_id, $quotum))
                    return true;
            }
        }
        return false;
    }

    function set_mediamosa_default_user_quotum($user_id)
    {
        $quotum = ExternalRepositorySetting :: get('default_user_quotum');
        if ($this->set_mediamosa_user_quotum($user_id, $quotum))
        {
            return true;
        }
        return false;
    }

    function handle_mediamosa_user_quotum($user)
    {
    
    }

    /*
     * @param int $user_id
     * @return simplexmlobject user
     */
    function retrieve_mediamosa_user($user_id)
    {
        if ($response = $this->request(self :: METHOD_GET, '/user/' . $this->get_mediamosa_user_id($user_id)))
        {
            if ($response->check_result())
            {
                return $response->get_response_content_xml()->items->item;
            }
        }
        
        return false;
    }

    /*
     * returns mediamosa rest version
     * @return string version
     */
    function retrieve_mediamosa_version()
    {
        if ($response = $this->request(self :: METHOD_GET, '/version/'))
        {
            if ($response->check_result())
            {
                $xml = $response->get_response_content_xml();
                return $xml->items->item->version;
            }
        }
        
        return false;
    }

    function login()
    {
        $url = ExternalRepositorySetting :: get('url');
        $this->mediamosa = new MediamosaRestClient($url);
        //TODO: jens -> implement curl request
        $this->mediamosa->set_connexion_mode(RestClient :: MODE_PEAR);
        //login if connector cookie doesn't exist
        //connector cookie takes care of login persistence
        if (! $this->mediamosa->get_connector_cookie())
        {
            //set proxy if necessary
            if (PlatformSetting :: get('proxy_settings_active', 'admin'))
                $this->mediamosa->set_proxy(PlatformSetting :: get('proxy_server', 'admin'), PlatformSetting :: get('proxy_port', 'admin'), PlatformSetting :: get('proxy_username', 'admin'), PlatformSetting :: get('proxy_password', 'admin'));
            
            if ($this->mediamosa->login(ExternalRepositorySetting :: get('login'), ExternalRepositorySetting :: get('password')))
            {
                return true;
            }
        }
        return false;
    }

    static function get_instance($manager)
    {
        if (! isset(self :: $instance))
        {
            self :: $instance = new MediamosaExternalRepositoryConnector();
        }
        return self :: $instance;
    }

    /*
     * retrieves the default mediafile for an asset
     * @param $asset_id
     * @return $mediafile_id
     */
    function retrieve_mediamosa_asset_default_mediafile($asset_id)
    {
        if ($profiles = $this->retrieve_mediamosa_transcoding_profiles())
        {
            foreach ($profiles as $profile_id => $profile)
            {
                if ($profile[MediamosaMediafileObject :: PROPERTY_IS_DEFAULT] == 'TRUE')
                    $default_transcode_profile_id = $profile_id;
            }
            
            if ($asset = $this->retrieve_mediamosa_asset($asset_id, false))
            {
                foreach ($asset->items->item->mediafiles as $mediafile)
                {
                    if ($mediafile->transcode_profile_id == $default_transcode_profile_id)
                    {
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
    function retrieve_external_repository_objects($condition = null, $order_property = null, $offset = null, $count = null)
    {
        $params = array();
        
        if ($order_property)
        {
            if (is_array($order_property))
            {
                $params['order_by'] = $order_property[0];
            }
            else
            {
                $params['order_by'] = $order_property;
            }
        }
        
        if ($count)
        {
            $params['limit'] = $count;
        }
        else
        {
            $params['limit'] = 10;
        }
        $params['offset'] = $offset;
        
        $params['granted'] = 'FALSE';
        $params['hide_empty_assets'] = 'TRUE';
        
        $chamilo_user = $this->retrieve_chamilo_user(Session :: get_user_id());
        
        $params['user_id'] = $this->get_mediamosa_user_id($chamilo_user->get_id());
        $params['aut_user_id'] = $this->get_mediamosa_user_id($chamilo_user->get_id());
        
        $gdm = GroupDataManager :: get_instance();
        
        $groups = $gdm->retrieve_user_groups($chamilo_user->get_id());
        
        $params['auth_group_id'] = array();
        //TODO:jens -> check
        $params['auth_group_id'][] = 1;
        
        while ($group = $groups->next_result())
        {
            $params['auth_group_id'][] = $group->get_group_id();
        }
        
        if ($chamilo_user->is_platform_admin())
        {
            $params['is_app_admin'] = 'TRUE';
        }
        
        if ($condition)
            $condition = sprintf('&%' . $condition);
            
        //if no params exist no request will produce error
        if (count($params) > 1)
        {
            if ($response = $this->request(self :: METHOD_GET, '/asset' . $condition, $params))
            {
                if ($response->check_result())
                {
                    $objects = array();
                    
                    $xml = $response->get_response_content_xml();
                    
                    if (isset($xml->items->item))
                    {
                        foreach ($xml->items->item as $asset)
                        {
                            if ((string) $asset->granted == 'TRUE')
                            {
                                $object = $this->create_mediamosa_external_repository_object($asset);
                                $objects[] = $object;
                                $this->asset_cache[(string) $asset->asset_id] = $object;
                            }
                        
                        }
                        
                        return new ArrayResultSet($objects);
                    }
                }
            }
        
        }
        return false;
    }

    function count_external_repository_objects($condition)
    {
        return $this->count_mediamosa_assets($condition);
    }

    function count_mediamosa_assets($condition, $order_property, $offset, $count, $recount = false)
    {
        if (! count($this->asset_cache) || $recount == true)
        {
            $this->retrieve_external_repository_objects($condition, $order_property, $offset, $count);
        }
        
        return count($this->asset_cache);
    }

    /*
     * creates and populates a MediamosaExternalRepositoryObject with xml data
     * @param object simple xml element
     * @return MediamosaExternalRepositoryObject
     */
    function create_mediamosa_external_repository_object($asset)
    {
        if ($asset)
        {
            $mediamosa_asset = new MediamosaExternalRepositoryObject();
            
            $mediamosa_asset->set_id((string) $asset->asset_id);
            $mediamosa_asset->set_title((string) $asset->dublin_core->title);
            $mediamosa_asset->set_owner_id((int) $asset->owner_id);
            //$metadata['language'] = (string)$asset->dublin_core->language;
            //$metadata['subject'] = (string)$asset->dublin_core->subject;
            $mediamosa_asset->set_description((string) $asset->dublin_core->description);
            //$metadata['contributor'] = (string)$asset->dublin_core->contributor;
            $mediamosa_asset->set_publisher((string) $asset->dublin_core->publisher);
            $mediamosa_asset->set_date((string) $asset->dublin_core->date);
            ((string) $asset->vpx_still_url) ? $mediamosa_asset->set_thumbnail((string) $asset->vpx_still_url) : $mediamosa_asset->set_thumbnail(self :: PLACEHOLDER_URL);
            $mediamosa_asset->set_creator((string) $asset->dublin_core->creator);
            //TODO:jens -> implement status
            $mediamosa_asset->set_status($status);
            $mediamosa_asset->set_type(MediamosaExternalRepositoryObject :: OBJECT_TYPE);
            $mediamosa_asset->set_owner_id((string) $asset->owner_id);
            
            //rights -- determine if the asset is protected for this user or not
            $mediamosa_asset->set_rights($this->determine_rights($asset));
            
            //status of mediafile is unavailable by default
            $mediamosa_asset->set_status(MediamosaExternalRepositoryObject :: STATUS_UNAVAILABLE);
            
            $mediamosa_transcoding_profiles = $this->retrieve_mediamosa_transcoding_profiles();
            
            //add mediafiles
            foreach ($asset->mediafiles->mediafile as $mediafile)
            {
                //if there is still an original mediafile
                //see if it can be removed
                if ((string) $mediafile->is_original_file == 'TRUE')
                {
                    $this->remove_mediamosa_original_mediafile($asset);
                }
                
                //duration is retrieved from one of the mediafiles
                if (! $mediamosa_asset->get_duration())
                {
                    $duration = substr((string) $mediafile->metadata->file_duration, 3, 5);
                    $mediamosa_asset->set_duration($duration);
                }
                
                if ((string) $mediafile->transcode_profile_id)
                {
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
                    if ($mediamosa_transcoding_profiles[(string) $mediafile->transcode_profile_id][MediamosaMediafileObject :: PROPERTY_IS_DEFAULT] == 'TRUE')
                    {
                        $mediamosa_mediafile->set_is_default();
                    }
                    
                    if ($mediamosa_transcoding_profiles[(string) $mediafile->transcode_profile_id][MediamosaMediafileObject :: PROPERTY_IS_DOWNLOADABLE] == 'TRUE')
                    {
                        $mediamosa_mediafile->set_is_downloadable();
                    }
                    
                    $mediamosa_asset->add_mediafile($mediamosa_mediafile);
                    
                    if ($mediamosa_mediafile->get_is_default())
                    {
                        $mediamosa_asset->set_default_mediafile((string) $mediafile->mediafile_id);
                    }
                    
                    //if there is a playable mediafile - set status available
                    $mediamosa_asset->set_status(MediamosaExternalRepositoryObject :: STATUS_AVAILABLE);
                }
            }
            return $mediamosa_asset;
        }
        return false;
    }

    function determine_rights($asset)
    {
        $asset_rights = array();
        $asset_rights[ExternalRepositoryObject :: RIGHT_USE] = true;
        
        $chamilo_user = $this->retrieve_chamilo_user(Session :: get_user_id());
        
        if ((string) $asset->is_protected != 'FALSE')
        {
            if (! $chamilo_user->is_platform_admin())
            {
                $asset_rights[ExternalRepositoryObject :: RIGHT_USE] = false;
                
                $rights = $this->retrieve_mediamosa_asset_rights($mediamosa_asset->get_id(), $mediamosa_asset->get_owner_id());
                
                //check users
                if (count($rights['aut_user']))
                {
                    foreach ($rights['aut_user'] as $n => $aut_user)
                    {
                        if ($aut_user == Session :: get_user_id())
                        {
                            $asset_rights[ExternalRepositoryObject :: RIGHT_USE] = true;
                        }
                    }
                }
                
                //check groups
                if (count($rights['aut_group']))
                {
                    $gdm = GroupDataManager :: get_instance();
                    $groups = $gdm->retrieve_user_groups(Session :: get_user_id());
                    
                    if ($groups->size())
                    {
                        while ($g = $groups->next_result())
                        {
                            $test_groups[$g->get_group_id()] = true;
                        }
                        
                        foreach ($rights['aut_group'] as $n => $aut_group)
                        {
                            if (isset($test_groups[$aut_group]))
                            {
                                $asset_rights[ExternalRepositoryObject :: RIGHT_USE] = true;
                            }
                        }
                    }
                
                }
            }
        
        }
        
        if ($chamilo_user->is_platform_admin() || ($asset->get_owner_id() == $chamilo_user->get_id()))
        {
            $asset_rights[ExternalRepositoryObject :: RIGHT_EDIT] = true;
            $asset_rights[ExternalRepositoryObject :: RIGHT_DELETE] = true;
        }
        else
        {
            $asset_rights[ExternalRepositoryObject :: RIGHT_EDIT] = false;
            $asset_rights[ExternalRepositoryObject :: RIGHT_DELETE] = false;
        }
        
//        if ($asset->get_is_downloadable())
//        {
            $asset_rights[ExternalRepositoryObject :: RIGHT_DOWNLOAD] = true;
//        }
//        else
//        {
//            $asset_rights[ExternalRepositoryObject :: RIGHT_DOWNLOAD] = false;
//        }
        
        return $asset_rights;
    }

    /*
     * if all transcoding profiles are provided, the original is removed
     */
    function remove_mediamosa_original_mediafile($asset)
    {
        $mediamosa_transcoding_profiles = $this->retrieve_mediamosa_transcoding_profiles();
        $n_transcoded = 0;
        
        foreach ($asset->mediafiles->mediafile as $mediafile)
        {
            //if the mediafile is a transcode to a provided profile
            if (isset($mediamosa_transcoding_profiles[(string) $mediafile->transcode_profile_id]))
            {
                $n_transcoded ++;
            }
            
            //get original mediafile
            if ((string) $mediafile->is_original_file == 'TRUE')
            {
                $original_mediafile_id = (string) $mediafile->mediafile_id;
            }
        }
        
        //if all files are transcoded
        if ($n_transcoded == count($mediamosa_transcoding_profiles))
        {
            
            //if there still is an original mediafile
            if ($original_mediafile_id)
            {
                //remove original mediafile
                $this->remove_mediamosa_mediafile($original_mediafile_id);
            }
        
        }
    }

    function retrieve_mediamosa_asset_rights($asset_id, $owner_id)
    {
        $data = array();
        $data['user_id'] = $owner_id;
        
        if ($response = $this->request(self :: METHOD_GET, '/asset/' . $asset_id . '/acl', $data))
        {
            if ($response->check_result($response))
            {
                $rights = array();
                
                foreach ($response->get_response_content_xml()->items->item as $item)
                {
                    foreach ($item->children() as $right)
                    {
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
    function create_mediamosa_asset()
    {
        $data = array();
        $data['user_id'] = $this->get_mediamosa_user_id(Session :: get_user_id());
        
        if ($response = $this->request(self :: METHOD_POST, '/asset/create', $data))
        {
            if ($response->check_result($response))
            {
                return (string) $response->get_response_content_xml()->items->item->asset_id;
            }
        }
        
        return false;
    }

    function retrieve_external_repository_object($id)
    {
        return $this->retrieve_mediamosa_asset($id, true);
    }

    /*
     * retrieve an asset on mediamosa server
     * @param string asset_id
     * @param boolean object
     * @return MediamosaExternalRepositoryObject or simplexmlelement
     */
    function retrieve_mediamosa_asset($asset_id, $object = true)
    {
        
        if ($asset_id)
        {
            $data = array();
            $data['user_id'] = $this->get_mediamosa_user_id(Session :: get_user_id());
            
            if ($response = $this->request(self :: METHOD_GET, '/asset/' . $asset_id, $data))
            {
                if ($response->check_result())
                {
                    $xml = $response->get_response_content_xml()->items->item;
                    
                    if ($object)
                    {
                        $object = $this->create_mediamosa_external_repository_object($xml);
                        $this->asset_cache[(string) $xml->asset_id] = $object;
                        return $object;
                    }
                    else
                    {
                        return $xml;
                    }
                }
            }
        }
        return false;
    }

    function delete_external_repository_object($id)
    {
        return $this->remove_mediamosa_asset($id, true);
    }

    /*
     * remove an asset on mediamosa server
     * @param boolean cascade (delete all underlying mediafiles + jobs as well)
     * @return boolean
     */
    function remove_mediamosa_asset($asset_id, $cascade = true)
    {
        if ($asset_id)
        {
            $data = array();
            $data['user_id'] = $this->get_mediamosa_user_id(Session :: get_user_id());
            if ($cascade == true)
            {
                $data['delete'] = 'cascade';
            }
            
            if ($response = $this->request(self :: METHOD_POST, '/asset/' . $asset_id . '/delete', $data))
            {
                if ($response->check_result())
                {
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
    function remove_mediamosa_assets($asset_ids, $cascade = true)
    {
        if (is_array($asset_ids))
        {
            $data = array();
            $data['asset_id'] = $asset_ids;
            $data['user_id'] = $this->get_mediamosa_user_id(Session :: get_user_id());
            if ($cascade == true)
            {
                $data['delete'] = 'cascade';
            }
            
            if ($response = $this->request(self :: METHOD_POST, '/asset/delete', $data))
            {
                if ($response->check_result())
                {
                    $xml = $response->get_response_content_xml();
                    
                    $output = array();
                    foreach ($xml->items as $item)
                    {
                        $output[] = (string) $item->asset_id . (string) $item->result . ':' . (string) $item->result_description;
                    }
                    return $output;
                }
            }
        
        }
        return false;
    }

    function export_external_repository_object($object)
    {
        return true;
    }

    /*
     * create a mediamosa mediafile
     * @param string asset_id
     * @return string mediafile_id
     */
    function create_mediamosa_mediafile($asset_id, $is_downloadable = false)
    {
        if ($asset_id)
        {
            $data['user_id'] = $this->get_mediamosa_user_id(Session :: get_user_id());
            $data['asset_id'] = $asset_id;
            if ($is_downloadable)
                $data['is_downloadable'] = true;
            
            if ($response = $this->request(self :: METHOD_POST, '/mediafile/create', $data))
            {
                if ($response->check_result())
                {
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
    function remove_mediamosa_mediafile($mediafile_id)
    {
        if ($mediafile_id)
        {
            $data = array();
            $data['user_id'] = $this->get_mediamosa_user_id(Session :: get_user_id());
            
            if ($response = $this->request(self :: METHOD_POST, 'mediafile/' . $mediafile_id . '/delete', $data))
            {
                if ($response->check_result())
                {
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
    function add_mediamosa_metadata($asset_id, $data)
    {
        if ($asset_id)
        {
            if (is_array($data))
            {
                $data['user_id'] = $this->get_mediamosa_user_id(Session :: get_user_id());
                //if metadata exists -> overwrite
                //TODO : check if these properties also apply when updating metadata
                $data['replace'] = 'TRUE';
                //$data['action'] = 'update';
                

                if ($response = $this->request(self :: METHOD_POST, '/asset/' . $asset_id . '/metadata', $data))
                {
                    if ($response->check_result($response))
                    {
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
    function create_mediamosa_upload_ticket($mediafile_id)
    {
        
        if ($mediafile_id)
        {
            $data = array();
            $data['user_id'] = $this->get_mediamosa_user_id(Session :: get_user_id());
            //$data['mediafile_id'] = $mediafile_id; //TODO : necessary?
            

            if ($response = $this->request(self :: METHOD_GET, '/mediafile/' . $mediafile_id . '/uploadticket/create', $data))
            {
                if ($response->check_result($response))
                {
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
    function retrieve_mediamosa_transcoding_profiles()
    {
        if (! $this->profiles)
        {
            $data = array();
            
            //TODO:jens->check if remove has no impications
            $data['user_id'] = $this->get_mediamosa_user_id('2');
            ;
            $data['is_app_admin'] = 'TRUE';
            
            if ($response = $this->request(self :: METHOD_GET, '/transcode/profile', $data))
            {
                if ($response->check_result())
                {
                    
                    $profiles = array();
                    
                    foreach ($response->get_response_content_xml()->items->item as $profile)
                    {
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
    function transcode_mediamosa_mediafile($mediafile_id, $transcoding_profile_id = null)
    {
    }

    function clean()
    {
        if ($response = $this->retrieve_mediamosa_assets(null, 'title', null))
        {
            $asset_ids = array();
            
            foreach ($response as $n => $mediamosa_external_repository_object)
            {
                $asset_ids[] = $mediamosa_external_repository_object->get_id();
            }
            
            if ($response = $this->remove_mediamosa_assets($asset_ids))
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
        if ($asset_id)
        {
            //retrieve default mediafile (mostly in case no mediafile is supplied)
            if ($mediafile_id == 'default')
            {
                $mediafile_id = $this->retrieve_mediamosa_asset_default_mediafile($asset_id);
                
                if (! $mediafile_id)
                    return false;
            }
            
            //prepare request data
            $data = array();
            $data['mediafile_id'] = $mediafile_id;
            $data['response'] = $response;
            
            $data['user_id'] = $this->get_mediamosa_user_id(Session :: get_user_id());
            //$data['group_id']
            //
            //get object or url
            $player = $this->request(self :: METHOD_GET, '/asset/' . $asset_id . '/play', $data);
            
            //verify
            switch ($player->get_response_content_xml()->header->request_result_id)
            {
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
    function retrieve_mediamosa_mediafile_rights($mediafile_id)
    {
        if ($mediafile_id)
        {
            if ($response = $this->request(self :: METHOD_GET, '/mediafile/' . $mediafile_id . '/acl'))
            {
                if ($response->check_result())
                {
                    $rights = array();
                    
                    foreach ($response->get_response_content_xml()->items->item as $item)
                    {
                        foreach ($item->children() as $right)
                        {
                            $rights[$right->getName()][] = (string) $right;
                        }
                    }
                    return $rights;
                }
            }
        
        }
        return false;
    }

    function get_mediamosa_asset_rights($asset_id)
    {
        if ($asset_id)
        {
            if ($response = $this->request(self :: METHOD_GET, '/asset/' . $asset . '/acl'))
            {
                if ($response->check_result())
                {
                    $rights = array();
                    
                    foreach ($response->get_response_content_xml()->items as $item)
                    {
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
     * @param string asset_id
     * @param array rights
     * @return bool
     */
    function set_mediamosa_asset_rights($asset_id, $rights, $owner_id)
    {
        ///xdebug_break();
        if ($asset_id)
        {
            if (is_array($rights))
            {
                $data = array();
                
                foreach ($rights as $k => $right)
                {
                    $data[$k] = $right;
                }
                $data['user_id'] = $this->get_mediamosa_user_id($owner_id);
                
                if ($response = $this->request(self :: METHOD_POST, '/asset/' . $asset_id . '/acl', $data))
                {
                    if ($response->check_result())
                    {
                        if ($asset = $this->retrieve_mediamosa_asset($asset_id))
                        {
                            $mediafiles = $asset->get_mediafiles();
                            
                            if (is_array($mediafiles))
                            {
                                foreach ($mediafiles as $mediafile)
                                {
                                    $this->set_mediamosa_mediafile_rights($mediafile->get_id(), $rights, $owner_id);
                                }
                                return true;
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
    function set_mediamosa_mediafile_rights($mediafile_id, $rights, $owner_id)
    {
        if ($mediafile_id)
        {
            if (is_array($rights))
            {
                $data = array();
                
                foreach ($rights as $k => $right)
                {
                    $data[$k] = $right;
                }
                
                $data['user_id'] = $this->get_mediamosa_user_id($owner_id);
                
                if ($response = $this->request(self :: METHOD_POST, '/mediafile/' . $mediafile_id . '/acl', $data))
                {
                    if ($response->check_result())
                    {
                        return true;
                    }
                }
            
            }
        }
        return false;
    }

    function request($method, $url, $data)
    {
        if ($this->mediamosa)
        {
            return $this->mediamosa->request($method, $url, $data);
        }
        return false;
    }

    static function translate_search_query($query)
    {
        return $query;
    }

}
?>
