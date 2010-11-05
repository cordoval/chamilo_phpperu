<?php
namespace repository;

use common\libraries\Request;
use common\libraries\Translation;
use common\libraries\Utilities;
use common\libraries\EqualityCondition;
use common\libraries\Path;
use common\libraries\DataClass;
use common\extensions\external_repository_manager\ExternalRepositoryManager;

use DOMDocument;
/**
 * @author Hans De Bisschop
 */

class ExternalRepositorySetting extends DataClass
{
    const CLASS_NAME = __CLASS__;

    const PROPERTY_EXTERNAL_REPOSITORY_ID = 'external_repository_id';
    const PROPERTY_VARIABLE = 'variable';
    const PROPERTY_VALUE = 'value';
    const PROPERTY_USER_SETTING = 'user_setting';

    /**
     * A static array containing all settings of external repository instances
     * @var array
     */
    private static $settings;

    /**
     * Get the default properties of all settings.
     * @return array The property names.
     */
    /**
     * @return array:
     */
    static function get_default_property_names()
    {
        return parent :: get_default_property_names(array(self :: PROPERTY_EXTERNAL_REPOSITORY_ID, self :: PROPERTY_VARIABLE, self :: PROPERTY_VALUE, self :: PROPERTY_USER_SETTING));
    }

    /**
     * @return RepositoryDataManagerInterface
     */
    function get_data_manager()
    {
        return RepositoryDataManager :: get_instance();
    }

    /**
     * @return string the external repository id
     */
    function get_external_repository_id()
    {
        return $this->get_default_property(self :: PROPERTY_EXTERNAL_REPOSITORY_ID);
    }

    /**
     * Returns the variable of this setting object
     * @return string the variable
     */
    function get_variable()
    {
        return $this->get_default_property(self :: PROPERTY_VARIABLE);
    }

    /**
     * Returns the value of this setting object
     * @return string the value
     */
    function get_value()
    {
        return $this->get_default_property(self :: PROPERTY_VALUE);
    }

    /**
     * @param string $external_repository_id
     */
    function set_external_repository_id($external_repository_id)
    {
        $this->set_default_property(self :: PROPERTY_EXTERNAL_REPOSITORY_ID, $external_repository_id);
    }

    /**
     * Sets the variable of this setting.
     * @param string $variable the variable.
     */
    function set_variable($variable)
    {
        $this->set_default_property(self :: PROPERTY_VARIABLE, $variable);
    }

    /**
     * Sets the value of this setting.
     * @param string $value the value.
     */
    function set_value($value)
    {
        $this->set_default_property(self :: PROPERTY_VALUE, $value);
    }

    /**
     * Returns the user_setting of this setting object
     * @return string the user_setting
     */
    function get_user_setting()
    {
        return $this->get_default_property(self :: PROPERTY_USER_SETTING);
    }

    /**
     * Sets the user_setting of this setting.
     * @param string $user_setting the user_setting.
     */
    function set_user_setting($user_setting)
    {
        $this->set_default_property(self :: PROPERTY_USER_SETTING, $user_setting);
    }

    /**
     * @return string
     */
    static function get_table_name()
    {
        return Utilities :: get_classname_from_namespace(self :: CLASS_NAME, true);
    }

    static function get_class_name()
    {
        return self :: CLASS_NAME;
    }

    static function initialize(ExternalRepository $external_repository)
    {
        $settings_file = Path :: get_common_extensions_path() . 'external_repository_manager/implementation/' . $external_repository->get_type() . '/php/settings/settings_' . $external_repository->get_type() . '.xml';

        $doc = new DOMDocument();

        $doc->load($settings_file);
        $object = $doc->getElementsByTagname('application')->item(0);
        $settings = $doc->getElementsByTagname('setting');

        foreach ($settings as $index => $setting)
        {
            $repository_setting = new ExternalRepositorySetting();
            $repository_setting->set_external_repository_id($external_repository->get_id());
            $repository_setting->set_variable($setting->getAttribute('name'));
            $repository_setting->set_value($setting->getAttribute('default'));

            $user_setting = $setting->getAttribute('user_setting');
            if ($user_setting)
            {
                $repository_setting->set_user_setting($user_setting);
            }
            else
            {
                $repository_setting->set_user_setting(0);
            }

            if (! $repository_setting->create())
            {
                return false;
            }
        }

        return true;
    }

    function delete()
    {
        if (! parent :: delete())
        {
            return false;
        }
        else
        {
            if ($this->get_user_setting())
            {
                $condition = new EqualityCondition(ExternalRepositoryUserSetting :: PROPERTY_SETTING_ID, $this->get_id());
                if (! RepositoryDataManager :: get_instance()->delete_external_repository_user_settings($condition))
                {
                    return false;
                }
                else
                {
                    return true;
                }
            }
            else
            {
                return true;
            }
        }
    }

    /**
     * @param string $variable
     * @param int $external_repository_id
     * @return mixed
     */
    static function get($variable, $external_repository_id = null)
    {
        if (is_null($external_repository_id) || ! is_numeric($external_repository_id))
        {
            $external_repository_id = Request :: get(ExternalRepositoryManager :: PARAM_EXTERNAL_REPOSITORY);

            if (is_null($external_repository_id) || ! is_numeric($external_repository_id))
            {
                Display :: error_page(Translation :: get('WhatsUpDoc'));
            }
        }

        if (! isset(self :: $settings[$external_repository_id]))
        {
            self :: load($external_repository_id);
        }

        return (isset(self :: $settings[$external_repository_id][$variable]) ? self :: $settings[$external_repository_id][$variable] : null);
    }

    static function get_all($external_repository_id = null)
    {
        if (is_null($external_repository_id) || ! is_numeric($external_repository_id))
        {
            $external_repository_id = Request :: get(ExternalRepositoryManager :: PARAM_EXTERNAL_REPOSITORY);

            if (is_null($external_repository_id) || ! is_numeric($external_repository_id))
            {
                Display :: error_page(Translation :: get('WhatsUpDoc'));
            }
        }

        if (! isset(self :: $settings[$external_repository_id]))
        {
            self :: load($external_repository_id);
        }

        return self :: $settings[$external_repository_id];
    }

    /**
     * @param int $external_repository_id
     */
    static function load($external_repository_id)
    {
        $condition = new EqualityCondition(ExternalRepositorySetting :: PROPERTY_EXTERNAL_REPOSITORY_ID, $external_repository_id);
        $settings = RepositoryDataManager :: get_instance()->retrieve_external_repository_settings($condition);

        while ($setting = $settings->next_result())
        {
            self :: $settings[$external_repository_id][$setting->get_variable()] = $setting->get_value();
        }
    }
}
?>