<?php
namespace common\extensions\external_repository_manager\implementation\soundcloud;

use common\libraries;

use common\libraries\Path;
use common\libraries\Request;
use common\libraries\Redirect;
use common\libraries\ActionBarSearchForm;
use common\libraries\ArrayResultSet;
use common\libraries\Session;

use repository\ExternalRepositoryUserSetting;
use repository\ExternalRepositorySetting;
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
     * The id of the user on Soundcloud
     * @var string
     */
    private $user_id;

    /**
     * @param ExternalRepository $external_repository_instance
     */
    function SoundcloudExternalRepositoryConnector($external_repository_instance)
    {
        parent :: __construct($external_repository_instance);

        $this->key = ExternalRepositorySetting :: get('key', $this->get_external_repository_instance_id());
        $this->secret = ExternalRepositorySetting :: get('secret', $this->get_external_repository_instance_id());

        $this->soundcloud = new Soundcloud($this->key, $this->secret);

        $outh_token = ExternalRepositoryUserSetting :: get('oauth_token', $this->get_external_repository_instance_id());
        $outh_token_secret = ExternalRepositoryUserSetting :: get('oauth_token_secret', $this->get_external_repository_instance_id());

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
                    $setting = RepositoryDataManager :: get_instance()->retrieve_external_repository_setting_from_variable_name('oauth_token', $this->get_external_repository_instance_id());
                    $user_setting = new ExternalRepositoryUserSetting();
                    $user_setting->set_setting_id($setting->get_id());
                    $user_setting->set_user_id(Session :: get_user_id());
                    $user_setting->set_value($access_token['oauth_token']);
                    $user_setting->create();

                    $setting = RepositoryDataManager :: get_instance()->retrieve_external_repository_setting_from_variable_name('oauth_token_secret', $this->get_external_repository_instance_id());
                    $user_setting = new ExternalRepositoryUserSetting();
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

    /**
     * @param mixed $condition
     * @param ObjectTableOrder $order_property
     * @param int $offset
     * @param int $count
     * @return ArrayResultSet
     */
    function retrieve_external_repository_objects($condition = null, $order_property, $offset, $count)
    {
        $track_endpoint = $this->render_endpoint_url('tracks', array('limit' => $count, 'offset' => $offset));
        $tracks = $this->soundcloud->request($track_endpoint);

        $objects = array();

        foreach (json_decode($tracks) as $track)
        {
            $object = new SoundcloudExternalRepositoryObject();
            $object->set_id($track->id);
            $object->set_external_repository_id($this->get_external_repository_instance_id());
            $object->set_title($track->title);
            $object->set_description($track->description);
            $object->set_created(strtotime($track->created_at));
            $object->set_modified(strtotime($track->created_at));
            $object->set_owner_id($track->user->username);
            $object->set_type($track->original_format);

            $object->set_artwork($track->artwork_url);

            $objects[] = $object;
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

            foreach ($parameters as $key => $value)
            {
                $url[] = urlencode($key);
                $url[] = '=';
                $url[] = urlencode($value);
            }
        }

        return implode('', $url);
    }

    /**
     * @param mixed $condition
     * @return int
     */
    function count_external_repository_objects($condition)
    {
        return 50;
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
        $track_endpoint = $this->render_endpoint_url($resource, array('limit' => $count, 'offset' => $offset));
        $track = json_decode($this->soundcloud->request($track_endpoint));

//        dump($track);

        $object = new SoundcloudExternalRepositoryObject();
        $object->set_id($track->id);
        $object->set_external_repository_id($this->get_external_repository_instance_id());
        $object->set_title($track->title);
        $object->set_description($track->description);
        $object->set_created(strtotime($track->created_at));
        $object->set_modified(strtotime($track->created_at));
        $object->set_owner_id($track->user->username);
        $object->set_type($track->original_format);

        $object->set_artwork($track->artwork_url);

        return $object;
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
    function determine_rights($license, $photo_user_id)
    {
        $users_match = ($this->retrieve_user_id() == $photo_user_id ? true : false);
        //$compatible_license = ($license == 0 ? false : true);
        $compatible_license = true;

        $rights = array();
        $rights[ExternalRepositoryObject :: RIGHT_USE] = $compatible_license || $users_match;
        $rights[ExternalRepositoryObject :: RIGHT_EDIT] = $users_match;
        $rights[ExternalRepositoryObject :: RIGHT_DELETE] = $users_match;
        $rights[ExternalRepositoryObject :: RIGHT_DOWNLOAD] = $compatible_license || $users_match;

        return $rights;
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