<?php
namespace common\extensions\external_repository_manager\implementation\soundcloud;

use common\libraries;

use common\libraries\Path;
use common\libraries\Request;
use common\libraries\Redirect;
use common\libraries\ActionBarSearchForm;
use common\libraries\ArrayResultSet;
use common\libraries\Session;

use repository\ExternalUserSetting;
use repository\ExternalSetting;
use repository\RepositoryDataManager;

use common\extensions\external_repository_manager\ExternalRepositoryConnector;
use common\extensions\external_repository_manager\ExternalRepositoryObject;

use Soundcloud;
use OAuthConsumer;

require_once Path :: get_plugin_path(__NAMESPACE__) . 'soundcloud/soundcloud.php';
require_once Path :: get_plugin_path(__NAMESPACE__) . 'soundcloud/oauth.php';
require_once dirname(__FILE__) . '/soundcloud_external_repository_object.class.php';

/**
 * @author Scaramanga
 *
 * Test developer key for Soundcloud: 61a0f40b9cb4c22ec6282e85ce2ae768
 * Test developer secret for Soundcloud: e267cbf5b7a1ad23
 */

class SoundcloudExternalRepositoryConnector extends ExternalRepositoryConnector
{
    /**
     * @var phpSoundcloud
     */
    private $soundcloud;

    /**
     * @var string
     */
    private $key;

    /**
     * @var string
     */
    private $secret;

    /**
     * @var array
     */
    private $licenses;

    /**
     * The user authenticated on Soundcloud
     * @var object
     */
    private $user;

    /**
     * @param ExternalRepository $external_repository_instance
     */
    function __construct($external_repository_instance)
    {
        parent :: __construct($external_repository_instance);

        $this->key = ExternalSetting :: get('key', $this->get_external_repository_instance_id());
        $this->secret = ExternalSetting :: get('secret', $this->get_external_repository_instance_id());

        $this->soundcloud = new Soundcloud($this->key, $this->secret);

        $outh_token = ExternalUserSetting :: get('oauth_token', $this->get_external_repository_instance_id());
        $outh_token_secret = ExternalUserSetting :: get('oauth_token_secret', $this->get_external_repository_instance_id());

        if (! $outh_token || ! $outh_token_secret)
        {
            $oauth_token = Request :: get('oauth_token');
            $oauth_verifier = Request :: get('oauth_verifier');

            if (! $oauth_token)
            {
                $request_token = $this->soundcloud->get_request_token(Redirect :: current_url());

                if ($request_token)
                {
                    Session :: register('soundcloud_request_token', $request_token['oauth_token']);
                    Session :: register('soundcloud_request_token_secret', $request_token['oauth_token_secret']);

                    Redirect :: write_header($this->soundcloud->get_authorize_url($request_token['oauth_token']));
                }
            }
            else
            {
                $this->soundcloud->token = new OAuthConsumer(Session :: retrieve('soundcloud_request_token'), Session :: retrieve('soundcloud_request_token_secret'));
                $access_token = $this->soundcloud->get_access_token($oauth_verifier);

                if ($access_token)
                {
                    $setting = RepositoryDataManager :: get_instance()->retrieve_external_setting_from_variable_name('oauth_token', $this->get_external_repository_instance_id());
                    $user_setting = new ExternalUserSetting();
                    $user_setting->set_setting_id($setting->get_id());
                    $user_setting->set_user_id(Session :: get_user_id());
                    $user_setting->set_value($access_token['oauth_token']);
                    $user_setting->create();

                    $setting = RepositoryDataManager :: get_instance()->retrieve_external_setting_from_variable_name('oauth_token_secret', $this->get_external_repository_instance_id());
                    $user_setting = new ExternalUserSetting();
                    $user_setting->set_setting_id($setting->get_id());
                    $user_setting->set_user_id(Session :: get_user_id());
                    $user_setting->set_value($access_token['oauth_token_secret']);
                    $user_setting->create();

                    Session :: unregister('soundcloud_request_token');
                    Session :: unregister('soundcloud_request_token_secret');
                }
            }
        }
        else
        {
            $this->soundcloud->token = new OAuthConsumer($outh_token, $outh_token_secret);
        }
    }

    /**
     * @param int $instance_id
     * @return SoundcloudExternalRepositoryConnector:
     */
    static function get_instance($instance_id)
    {
        if (! isset(self :: $instance[$instance_id]))
        {
            self :: $instance[$instance_id] = new SoundcloudExternalRepositoryConnector($instance_id);
        }
        return self :: $instance[$instance_id];
    }

    function retrieve_tracks($condition = null, $order_property, $offset = null, $count = null)
    {
        $feed_type = Request :: get(SoundcloudExternalRepositoryManager :: PARAM_FEED_TYPE);
        $track_type = Request :: get(SoundcloudExternalRepositoryManager :: PARAM_TRACK_TYPE);

        $parameters = array();
        $parameters['q'] = $condition;
        $parameters['filter'] = 'streamable';
        $parameters['limit'] = $count;
        $parameters['offset'] = $offset;
        $parameters['types'] = $track_type;

        switch ($feed_type)
        {
            case SoundcloudExternalRepositoryManager :: FEED_TYPE_GENERAL :
                $track_endpoint = $this->render_endpoint_url('tracks', $parameters);
                break;
            case SoundcloudExternalRepositoryManager :: FEED_TYPE_MY_TRACKS :
                $track_endpoint = $this->render_endpoint_url('users/' . $this->retrieve_user()->id . '/tracks', $parameters);
                break;
            default :
                $track_endpoint = $this->render_endpoint_url('users/' . $this->retrieve_user()->id . '/tracks', $parameters);
                break;
        }

        return json_decode($this->soundcloud->request($track_endpoint));
    }

    /**
     * @param mixed $condition
     * @param ObjectTableOrder $order_property
     * @param int $offset
     * @param int $count
     * @return ArrayResultSet
     */
    function retrieve_external_repository_objects($condition = null, $order_property, $offset, $count)
    {
        $tracks = $this->retrieve_tracks($condition, $order_property, $offset, $count);

        $objects = array();

        foreach ($tracks as $track)
        {
            $objects[] = $this->get_track($track);
        }

        return new ArrayResultSet($objects);
    }

    static function render_endpoint_url($endpoint, $parameters = array(), $format = 'json')
    {
        $url = array();
        $url[] = $endpoint;
        $url[] = '.';
        $url[] = $format;

        if (count($parameters) > 0)
        {
            $url[] = '?';

            $url_parameters = array();
            foreach ($parameters as $key => $value)
            {
                $url_parameters[] = urlencode($key) . '=' . urlencode($value);
            }

            $url[] = implode('&', $url_parameters);
        }

        return implode('', $url);
    }

    /**
     * @param mixed $condition
     * @return int
     */
    function count_external_repository_objects($condition)
    {
        return $this->retrieve_external_repository_objects($condition)->size();
    }

    /**
     * @param string $query
     * @return string
     */
    static function translate_search_query($query)
    {
        return $query;
    }

    /**
     * @param ObjectTableOrder $order_properties
     * @return string|null
     */
    function convert_order_property($order_properties)
    {
        if (count($order_properties) > 0)
        {
            $order_property = $order_properties[0]->get_property();
            if ($order_property == self :: SORT_RELEVANCE)
            {
                return $order_property;
            }
            else
            {
                $sorting_direction = $order_properties[0]->get_direction();

                if ($sorting_direction == SORT_ASC)
                {
                    return $order_property . '-asc';
                }
                elseif ($sorting_direction == SORT_DESC)
                {
                    return $order_property . '-desc';
                }
            }
        }

        return null;
    }

    /**
     * @return array
     */
    static function get_sort_properties()
    {
        return array();
    }

    /* (non-PHPdoc)
     * @see application/common/external_repository_manager/ExternalRepositoryConnector#retrieve_external_repository_object()
     */
    function retrieve_external_repository_object($id)
    {
        $resource = 'tracks/' . $id;
        $track_endpoint = $this->render_endpoint_url($resource);
        $response = json_decode($this->soundcloud->request($track_endpoint));

        return $this->get_track($response);
    }

    function get_track($object)
    {
        $track = new SoundcloudExternalRepositoryObject();
        $track->set_id($object->id);
        $track->set_external_repository_id($this->get_external_repository_instance_id());
        $track->set_title($object->title);
        $track->set_description($object->description);
        $track->set_created(strtotime($object->created_at));
        $track->set_modified(strtotime($object->created_at));
        $track->set_owner_id($object->user->username);
        $track->set_type($object->original_format);

        $track->set_artwork($object->artwork_url);
        $track->set_license($object->license);

        $track->set_genre($object->genre);
        $track->set_waveform($object->waveform_url);
        $track->set_track_type($object->track_type);
        $track->set_bpm($object->bpm);
        $track->set_label($object->label);

        $track->set_rights($this->determine_rights($object->license, $object->user->username));

        return $track;
    }

    /**
     * @param array $values
     * @return boolean
     */
    function update_external_repository_object($values)
    {
        return true;
    }

    /**
     * @param array $values
     * @param string $photo_path
     * @return mixed
     */
    function create_external_repository_object($values, $photo_path)
    {
        return true;
    }

    /**
     * @param ContentObject $content_object
     * @return mixed
     */
    function export_external_repository_object($content_object)
    {
    }

    /**
     * @param int $license
     * @param string $photo_user_id
     * @return boolean
     */
    function determine_rights($license, $track_user_id)
    {
        $users_match = ($this->retrieve_user_id() == $track_user_id ? true : false);
        //$compatible_license = ($license == 0 ? false : true);
        $compatible_license = true;

        $rights = array();
        $rights[ExternalRepositoryObject :: RIGHT_USE] = $compatible_license || $users_match;
        $rights[ExternalRepositoryObject :: RIGHT_EDIT] = $users_match;
        $rights[ExternalRepositoryObject :: RIGHT_DELETE] = $users_match;
        $rights[ExternalRepositoryObject :: RIGHT_DOWNLOAD] = $compatible_license || $users_match;

        return $rights;
    }

    function retrieve_user()
    {
        if (! isset($this->user))
        {
            $track_endpoint = $this->render_endpoint_url('me');
            $this->user = json_decode($this->soundcloud->request($track_endpoint));
        }
        return $this->user;
    }

    function retrieve_user_id()
    {
        return $this->retrieve_user()->username;
    }

    /**
     * @param string $id
     * @return mixed
     */
    function delete_external_repository_object($id)
    {
        return true;
    }
}
?>