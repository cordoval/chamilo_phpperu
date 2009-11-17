<?php

/**
 * $Id: dokeos185_setting_current.class.php 221 2009-11-13 14:36:41Z vanpouckesven $
 * @package migration.platform.dokeos185
 */

require_once dirname(__FILE__) . '/../../lib/import/import_setting_current.class.php';
require_once dirname(__FILE__) . '/../../../admin/lib/setting.class.php';

/**
 * This class represents an old Dokeos 1.8.5 class
 *
 * @author David Van Wayenbergh
 * @author Sven Vanpoucke
 */

class Dokeos185SettingCurrent extends ImportSettingCurrent
{
    private $convert = array('siteName' => 'site_name', 'server_type' => 'server_type', 'Institution' => 'institution', 'InstitutionUrl' => 'institution_url', 'show_administrator_data' => 'show_administrator_data', 'administratorName' => 'administrator_firstname', 'administratorSurname' => 'administrator_surname', 'emailAdministrator' => 'administrator_email', 'administratorTelephone' => 'administrator_telephone', 'allow_lostpassword' => 'allow_password_retrieval', 'allow_registration' => 'allow_registration');
    
    /**
     * Migration data manager
     */
    private static $mgdm;
    
    /**
     * current setting properties
     */
    
    const PROPERTY_ID = 'id';
    const PROPERTY_VARIABLE = 'variable';
    const PROPERTY_SUBKEY = 'subkey';
    const PROPERTY_TYPE = 'type';
    const PROPERTY_CATEGORY = 'category';
    const PROPERTY_SELECTED_VALUE = 'selected_value';
    const PROPERTY_TITLE = 'title';
    const PROPERTY_COMMENT = 'comment';
    const PROPERTY_SCOPE = 'scope';
    const PROPERTY_SUBKEYTEXT = 'subkeytext';
    
    /**
     * Alfanumeric identifier of the current setting object.
     */
    private $code;
    
    /**
     * Default properties of the current setting object, stored in an associative
     * array.
     */
    private $defaultProperties;

    /**
     * Creates a new current setting object.
     * @param array $defaultProperties The default properties of the current setting
     *                                 object. Associative array.
     */
    function Dokeos185SettingCurrent($defaultProperties = array ())
    {
        $this->defaultProperties = $defaultProperties;
    }

    /**
     * Gets a default property of this current setting object by name.
     * @param string $name The name of the property.
     */
    function get_default_property($name)
    {
        return $this->defaultProperties[$name];
    }

    /**
     * Gets the default properties of this current setting.
     * @return array An associative array containing the properties.
     */
    function get_default_properties()
    {
        return $this->defaultProperties;
    }

    /**
     * Get the default properties of all current setting.
     * @return array The property names.
     */
    static function get_default_property_names()
    {
        return array(self :: PROPERTY_ID, self :: PROPERTY_VARIABLE, self :: PROPERTY_SUBKEY, self :: PROPERTY_TYPE, self :: PROPERTY_CATEGORY, self :: PROPERTY_SELECTED_VALUE, self :: PROPERTY_TITLE, self :: PROPERTY_COMMENT, self :: PROPERTY_SCOPE, self :: PROPERTY_SUBKEYTEXT);
    }

    /**
     * Sets a default property of this current setting by name.
     * @param string $name The name of the property.
     * @param mixed $value The new value for the property.
     */
    function set_default_property($name, $value)
    {
        $this->defaultProperties[$name] = $value;
    }

    /**
     * Checks if the given identifier is the name of a default current setting
     * property.
     * @param string $name The identifier.
     * @return boolean True if the identifier is a property name, false
     *                 otherwise.
     */
    static function is_default_property_name($name)
    {
        return in_array($name, self :: get_default_property_names());
    }

    /**
     * Sets the default properties of this class
     */
    function set_default_properties($defaultProperties)
    {
        $this->defaultProperties = $defaultProperties;
    }

    /**
     * Returns the id of this current setting.
     * @return int The id.
     */
    function get_id()
    {
        return $this->get_default_property(self :: PROPERTY_ID);
    }

    /**
     * Returns the variable of this current setting.
     * @return String The variable.
     */
    function get_variable()
    {
        return $this->get_default_property(self :: PROPERTY_VARIABLE);
    }

    /**
     * Returns the subkey of this current setting.
     * @return String The subkey.
     */
    function get_subkey()
    {
        return $this->get_default_property(self :: PROPERTY_SUBKEY);
    }

    /**
     * Returns the type of this current setting.
     * @return String The type.
     */
    function get_type()
    {
        return $this->get_default_property(self :: PROPERTY_TYPE);
    }

    /**
     * Returns the category of this current setting.
     * @return String The category.
     */
    function get_category()
    {
        return $this->get_default_property(self :: PROPERTY_CATEGORY);
    }

    /**
     * Returns the selected_value of this current setting.
     * @return String The selected_value.
     */
    function get_selected_value()
    {
        return $this->get_default_property(self :: PROPERTY_SELECTED_VALUE);
    }

    /**
     * Returns the title of this current setting.
     * @return String The title.
     */
    function get_title()
    {
        return $this->get_default_property(self :: PROPERTY_TITLE);
    }

    /**
     * Returns the comment of this current setting.
     * @return String The comment.
     */
    function get_comment()
    {
        return $this->get_default_property(self :: PROPERTY_COMMENT);
    }

    /**
     * Returns the scope of this current setting.
     * @return String The scope.
     */
    function get_scope()
    {
        return $this->get_default_property(self :: PROPERTY_SCOPE);
    }

    /**
     * Returns the subkeytext of this current setting.
     * @return String The subkeytext.
     */
    function get_subkey_text()
    {
        return $this->get_default_property(self :: PROPERTY_SUBKEYTEXT);
    }

    /**
     * Returns the selected value of this current setting.
     * @return String The selected value.
     */
    function set_selected_value($selected_value)
    {
        $this->set_default_property(self :: PROPERTY_ID, $selected_value);
    }

    /**
     * Checks if a settingcurrent is valid
     * @param Array $array
     * @return Boolean
     */
    function is_valid($parameters)
    {
        return isset($this->convert[$this->get_variable()]);
    }

    /**
     * migrate settingcurrent, sets category
     * @param Array $array
     * @return null
     */
    function convert_to_lcms($parameters)
    {
        //course_rel_user parameters
        $i = 0;
        $value = $this->convert[$this->get_variable()];
        if ($value)
        {
            $lcms_admin_setting = AdminDataManager :: get_instance()->retrieve_setting_from_variable_name($value);
            
            if ($this->get_variable() == 'allow_lostpassword')
            {
                if ($this->get_selected_value() == 'true')
                    $this->set_selected_value(1);
                else
                    $this->set_selected_value(0);
            }
            
            if ($this->get_variable() == 'allow_registration')
            {
                if ($this->get_selected_value() == 'true')
                    $this->set_selected_value(1);
                else
                    $this->set_selected_value(0);
            }
            
            if ($lcms_admin_setting)
            {
                $lcms_admin_setting->set_value($this->get_selected_value());
                
                // Update setting in database
                $lcms_admin_setting->update();
                
            //return $lcms_admin_setting;
            }
            
            return $lcms_admin_setting;
        }
        
        return null;
    }

    /**
     * Gets all the system announcement
     * @param Array $parameters
     * @return Array of dokeos185systemannouncements
     */
    static function get_all($parameters)
    {
        $old_mgdm = $parameters['old_mgdm'];
        
        $db = 'main_database';
        $tablename = 'settings_current';
        $classname = 'Dokeos185SettingCurrent';
        
        return $old_mgdm->get_all($db, $tablename, $classname, $tool_name, $parameters['offset'], $parameters['limit']);
    }

    static function get_database_table($parameters)
    {
        $array = array();
        $array['database'] = 'main_database';
        $array['table'] = 'settings_current';
        return $array;
    }
}
?>
