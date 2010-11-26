<?php
namespace repository;

use common\libraries\Request;
use common\libraries\Translation;
use common\libraries\Utilities;
use common\libraries\EqualityCondition;
use common\libraries\Session;
use common\libraries\AndCondition;
use common\libraries\DataClass;
use common\extensions\external_repository_manager\ExternalRepositoryManager;

/**
 * $Id: external_repository_user_setting.class.php 211 2009-11-13 13:28:39Z vanpouckesven $
 * @author Sven Vanpoucke
 * @package user.lib
 */

class ExternalUserSetting extends DataClass
{
    const CLASS_NAME = __CLASS__;

    const PROPERTY_TYPE = 'type';
    const PROPERTY_USER_ID = 'user_id';
    const PROPERTY_SETTING_ID = 'setting_id';
    const PROPERTY_VALUE = 'value';

    /**
     * A static array containing all user settings of external repository instances
     * @var array
     */
    private static $settings;

    /**
     * Get the default properties of all users quota objects.
     * @return array The property names.
     */
    static function get_default_property_names()
    {
        return parent :: get_default_property_names(array(self :: PROPERTY_TYPE, self :: PROPERTY_USER_ID, self :: PROPERTY_SETTING_ID, self :: PROPERTY_VALUE));
    }

    /**
     * inherited
     */
    function get_data_manager()
    {
        return RepositoryDataManager :: get_instance();
    }

    function get_user_id()
    {
        return $this->get_default_property(self :: PROPERTY_USER_ID);
    }

    function set_user_id($user_id)
    {
        $this->set_default_property(self :: PROPERTY_USER_ID, $user_id);
    }
    
	function get_type()
    {
        return $this->get_default_property(self :: PROPERTY_TYPE);
    }

    function set_type($type)
    {
        $this->set_default_property(self :: PROPERTY_TYPE, $type);
    }

    function get_setting_id()
    {
        return $this->get_default_property(self :: PROPERTY_SETTING_ID);
    }

    function set_setting_id($setting_id)
    {
        $this->set_default_property(self :: PROPERTY_SETTING_ID, $setting_id);
    }

    function get_value()
    {
        return $this->get_default_property(self :: PROPERTY_VALUE);
    }

    function set_value($value)
    {
        $this->set_default_property(self :: PROPERTY_VALUE, $value);
    }

    static function get_table_name()
    {
        return Utilities :: get_classname_from_namespace(self :: CLASS_NAME, true);
    }

    static function get_class_name()
    {
        return self :: CLASS_NAME;
    }

    /**
     * @param string $variable
     * @param int $external_repository_id
     * @return mixed
     */
    static function get($variable, $external_id, $user_id = null)
    {
        if (is_null($user_id) || ! is_numeric($user_id))
        {
            $user_id = Session :: get_user_id();
        }

        if (! isset(self :: $settings[$external_id][$user_id]))
        {
            self :: load($external_id, $user_id);
        }

        return (isset(self :: $settings[$external_id][$user_id][$variable]) ? self :: $settings[$external_id][$user_id][$variable] : null);
    }

    static function load($external_id, $user_id)
    {
        $condition = new EqualityCondition(ExternalSetting :: PROPERTY_EXTERNAL_ID, $external_id);
        $settings = RepositoryDataManager :: get_instance()->retrieve_external_settings($condition);

        $setting_ids = array();
        while ($setting = $settings->next_result())
        {
            $conditions = array();
            $conditions[] = new EqualityCondition(self :: PROPERTY_USER_ID, $user_id);
            $conditions[] = new EqualityCondition(self :: PROPERTY_SETTING_ID, $setting->get_id());
            $condition = new AndCondition($conditions);

            $user_settings = RepositoryDataManager :: get_instance()->retrieve_external_user_settings($condition, array(), 0, 1);
            if ($user_settings->size() == 1)
            {
                $user_setting = $user_settings->next_result();
                self :: $settings[$external_id][$user_id][$setting->get_variable()] = $user_setting->get_value();
            }
        }
    }
}
?>