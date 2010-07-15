<?php
/**
 * $Id: dokeos185_class_user.class.php 221 2009-11-13 14:36:41Z vanpouckesven $
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

class Dokeos185ClassUser extends Dokeos185MigrationDataClass
{
    const CLASS_NAME = __CLASS__;
	const TABLE_NAME = 'class_user';   
	const DATABASE_NAME = 'main_database';
	
    /**
     * class relation user properties
     */
    
    const PROPERTY_CLASS_ID = 'class_id';
    const PROPERTY_USER_ID = 'user_id';
    
    /**
     * Get the default properties of all classe_users.
     * @return array The property names.
     */
    static function get_default_property_names()
    {
        return array(self :: PROPERTY_CLASS_ID, self :: PROPERTY_USER_ID);
    }

    /**
     * Returns the class_id of this class_user.
     * @return int The id.
     */
    function get_class_id()
    {
        return $this->get_default_property(self :: PROPERTY_CLASS_ID);
    }

    /**
     * Returns the code of this class_user.
     * @return int The code.
     */
    function get_user_id()
    {
        return $this->get_default_property(self :: PROPERTY_USER_ID);
    }

    /**
     * Check if the class user is valid
     */
    function is_valid()
    {
        if (! $this->get_class_id() || ! $this->get_user_id() ||
        	  $this->get_failed_element($this->get_class_id(), 'main_database.class') || 
        	  $this->get_failed_element($this->get_user_id(), 'main_database.user'))
        {
            $this->create_failed_element($this->get_class_id() . ' - ' . $this->get_user_id());
            $this->set_message(Translation :: get('ClassUserInvalidMessage', array('CLASS_ID' => $this->get_class_id(), 'USER_ID' => $this->get_user_id())));
            return false;
        }
        
        return true;
    }

    /**
     * Convert to new class user
     * @param array $array the parameters for the conversion
     * @return the new course
     */
    function convert_data()
    {
        $lcms_class_user = new GroupRelUser();
        
        $class_id = $this->get_id_reference($this->get_class_id(), 'main_database.class');
        if ($class_id)
        {
            $lcms_class_user->set_group_id($class_id);
        }
        
        $user_id = $this->get_id_reference($this->get_user_id(), 'main_database.user');
        if ($user_id)
        {
            $lcms_class_user->set_user_id($user_id);
        }
        
        $lcms_class_user->create();
        $this->set_message(Translation :: get('ClassUserConvertedMessage', array('CLASS_ID' => $this->get_class_id(), 'USER_ID' => $this->get_user_id())));
    }
    
	static function get_table_name()
    {
        return self :: TABLE_NAME;
    }
    
    static function get_class_name()
    {
    	return self :: CLASS_NAME;
    }
    
    static function get_database_name()
    {
    	return self :: DATABASE_NAME;
    }
}
?>