<?php
namespace common\extensions\external_repository_manager\implementation\soundcloud;

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

require_once Path :: get_plugin_path() . 'soundcloud/soundcloud.php';
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

    //        $this->key = ExternalRepositorySetting :: get('key', $this->get_external_repository_instance_id());
    //        $this->secret = ExternalRepositorySetting :: get('secret', $this->get_external_repository_instance_id());
    //        $this->soundcloud = new phpSoundcloud($this->key, $this->secret);
    //
    //
    //        $session_token = ExternalRepositoryUserSetting :: get('session_token', $this->get_external_repository_instance_id());
    //
    //        if (! $session_token)
    //        {
    //            $frob = Request :: get('frob');
    //
    //            if (! $frob)
    //            {
    //                $this->soundcloud->auth("delete", Redirect :: current_url());
    //            }
    //            else
    //            {
    //                $token = $this->soundcloud->auth_getToken($frob);
    //                if ($token['token'])
    //                {
    //                    $setting = RepositoryDataManager :: get_instance()->retrieve_external_repository_setting_from_variable_name('session_token', $this->get_external_repository_instance_id());
    //                    $user_setting = new ExternalRepositoryUserSetting();
    //                    $user_setting->set_setting_id($setting->get_id());
    //                    $user_setting->set_user_id(Session :: get_user_id());
    //                    $user_setting->set_value($token['token']);
    //                    $user_setting->create();
    //                }
    //            }
    //        }
    //        else
    //        {
    //            $this->soundcloud->setToken($session_token);
    //        }
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
        return new ArrayResultSet(array());
    }

    /**
     * @param mixed $condition
     * @return int
     */
    function count_external_repository_objects($condition)
    {
        return 0;
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
        $object = new SoundcloudExternalRepositoryObject();
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