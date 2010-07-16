<?php

/**
 * $Id: dokeos185_class.class.php 221 2009-11-13 14:36:41Z vanpouckesven $
 * @package migration.platform.dokeos185
 */

require_once dirname(__FILE__) . '/../dokeos185_migration_data_class.class.php';
require_once dirname(__FILE__) . '/../dokeos185_data_manager.class.php';

/**
 * This class represents an old Dokeos 1.8.5 class
 *
 * @author David Van Wayenberghµ
 * @author Sven Vanpoucke
 */

class Dokeos185Class extends Dokeos185MigrationDataClass
{
    const CLASS_NAME = __CLASS__;
	const TABLE_NAME = 'class';   
	const DATABASE_NAME = 'main_database';
	 
    /**
     * course relation user properties
     */
    
    const PROPERTY_ID = 'id';
    const PROPERTY_CODE = 'code';
    const PROPERTY_NAME = 'name';
 
    /**
     * Default properties of the class object, stored in an associative
     * array.
     */
    private $defaultProperties;

    /**
     * Get the default properties of all classes.
     * @return array The property names.
     */
    static function get_default_property_names()
    {
        return array(self :: PROPERTY_ID, self :: PROPERTY_CODE, self :: PROPERTY_NAME);
    }

    /**
     * Returns the id of this class.
     * @return int The id.
     */
    function get_id()
    {
        return $this->get_default_property(self :: PROPERTY_ID);
    }

    /**
     * Returns the code of this class.
     * @return String The code.
     */
    function get_code()
    {
        return $this->get_default_property(self :: PROPERTY_CODE);
    }

    /**
     * Returns the name of this class.
     * @return int The name.
     */
    function get_name()
    {
        return $this->get_default_property(self :: PROPERTY_NAME);
    }

    /**
     * Check if the class is valid
     * @return true if the class is valid 
     */
    function is_valid()
    {
    	$mgdm = MigrationDataManager :: get_instance();
        if (!$this->get_name())
        {
            $this->create_failed_element($this->get_id());
            $this->set_message(Translation :: get('GeneralInvalidMessage', array('TYPE' => 'class', 'ID' => $this->get_id())));
            return false;
        }
        return true;
    }

    /**
     * Convert to new class
     * @return the new class
     */
    function convert_data()
    {
        $chamilo_class = new Group();
        
        $chamilo_class->set_name($this->get_name());
        
        if ($this->get_code())
        {
            $chamilo_class->set_code($this->get_code());
        }
        else
        {
            $code = strtoupper(str_replace(' ', '', $this->get_name()));
        	$chamilo_class->set_code($code);
        }
        
 		$chamilo_class->set_description($this->get_name());
 		$chamilo_class->set_parent(GroupDataManager :: get_root_group()->get_id());
        
        //create course in database
        $chamilo_class->create();
        
        //Add id references to temp table
        $this->create_id_reference($this->get_id(), $chamilo_class->get_id());
        
        $this->set_message(Translation :: get('GeneralConvertedMessage', array('TYPE' => 'class', 'OLD_ID' => $this->get_id(), 'NEW_ID' => $chamilo_class->get_id())));
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