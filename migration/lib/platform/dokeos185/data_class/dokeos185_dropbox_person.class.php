<?php
/**
 * $Id: dokeos185_dropbox_person.class.php 221 2009-11-13 14:36:41Z vanpouckesven $
 * @package migration.lib.platform.dokeos185
 */

require_once dirname(__FILE__) . '/../dokeos185_course_data_migration_data_class.class.php';

/**
 * This class presents a Dokeos185 dropbox_person
 *
 * @author Sven Vanpoucke
 */
class Dokeos185DropboxPerson extends Dokeos185CourseDataMigrationDataClass
{
    const CLASS_NAME = __CLASS__;
    const TABLE_NAME = 'dropbox_person';
    
    /**
     * Dokeos185DropboxPerson properties
     */
    const PROPERTY_FILE_ID = 'file_id';
    const PROPERTY_USER_ID = 'user_id';


    /**
     * Returns the file_id of this Dokeos185DropboxPerson.
     * @return the file_id.
     */
    function get_file_id()
    {
        return $this->get_default_property(self :: PROPERTY_FILE_ID);
    }

    /**
     * Returns the user_id of this Dokeos185DropboxPerson.
     * @return the user_id.
     */
    function get_user_id()
    {
        return $this->get_default_property(self :: PROPERTY_USER_ID);
    }
    
	/**
     * Get the default properties
     * @return array The property names.
     */
    static function get_default_property_names()
    {
        return array(self :: PROPERTY_FILE_ID, self :: PROPERTY_USER_ID);
    }
    
    function is_valid()
    {
    	
    }
    
    function convert_data()
    {
    	
    }

    static function get_table_name()
    {
        return self :: TABLE_NAME;
    }
    
    static function get_class_name()
    {
    	return self :: CLASS_NAME;
    }
}

?>