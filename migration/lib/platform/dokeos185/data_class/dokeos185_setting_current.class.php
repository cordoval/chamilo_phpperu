<?php

/**
 * $Id: dokeos185_setting_current.class.php 221 2009-11-13 14:36:41Z vanpouckesven $
 * @package migration.platform.dokeos185
 */

require_once dirname(__FILE__) . '/../dokeos185_migration_data_class.class.php';
require_once dirname(__FILE__) . '/../dokeos185_data_manager.class.php';

/**
 * This class represents an old Dokeos 1.8.5 class
 *
 * @author David Van Wayenbergh
 * @author Sven Vanpoucke
 */

class Dokeos185SettingCurrent extends Dokeos185MigrationDataClass
{
    const CLASS_NAME = __CLASS__;
	const TABLE_NAME = 'settings_current';   
	const DATABASE_NAME = 'main_database';
	
	private static $convert = array('siteName' => 'site_name', 'server_type' => 'server_type', 'Institution' => 'institution', 'InstitutionUrl' => 'institution_url', 
									'show_administrator_data' => 'show_administrator_data', 'administratorName' => 'administrator_firstname', 'administratorSurname' => 'administrator_surname', 
									'emailAdministrator' => 'administrator_email', 'administratorTelephone' => 'administrator_telephone');
									//'allow_lostpassword' => 'allow_password_retrieval', 'allow_registration' => 'allow_registration');
    
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
     * Get the default properties of all current setting.
     * @return array The property names.
     */
    static function get_default_property_names()
    {
        return array(self :: PROPERTY_ID, self :: PROPERTY_VARIABLE, self :: PROPERTY_SUBKEY, self :: PROPERTY_TYPE, self :: PROPERTY_CATEGORY, self :: PROPERTY_SELECTED_VALUE, self :: PROPERTY_TITLE, self :: PROPERTY_COMMENT, self :: PROPERTY_SCOPE, self :: PROPERTY_SUBKEYTEXT);
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
    function is_valid()
    {
        return true;
    }

    /**
     * migrate settingcurrent, sets category
     * @param Array $array
     * @return null
     */
    function convert_data()
    {
        //course_rel_user parameters
        $value = self :: $convert[$this->get_variable()];
        if ($value)
        {
            $chamilo_admin_setting = AdminDataManager :: get_instance()->retrieve_setting_from_variable_name($value);
            
            if ($this->get_variable() == 'allow_lostpassword')
            {
                if ($this->get_selected_value() == 'true')
                {
                    $this->set_selected_value(1);
                }
                else
                {
                    $this->set_selected_value(0);
                }
            }
            
            if ($this->get_variable() == 'allow_registration')
            {
                if ($this->get_selected_value() == 'true')
                {
                    $this->set_selected_value(1);
                }
                else
                {
                    $this->set_selected_value(0);
                }
            }
            
            if ($chamilo_admin_setting)
            {
                $chamilo_admin_setting->set_value($this->get_selected_value());
                $chamilo_admin_setting->update();
                $this->set_message(Translation :: get('SettingConvertedMessage', array('SETTING' => $value, 'VALUE' => $this->get_selected_value())));
            }
        }        
    }

    static function get_table_name()
    {
        return self :: TABLE_NAME;
    }
    
    static function get_class_name()
    {
    	return self :: CLASS_NAME;
    }
    
    function get_database_name()
    {
    	return self :: DATABASE_NAME;
    }
    
    static function get_retrieve_condition()
    {
    	return new InCondition(self :: PROPERTY_VARIABLE, array_keys(self :: $convert));
    }
}
?>