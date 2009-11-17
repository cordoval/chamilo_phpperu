<?php
/**
 * $Id: dokeos185_user_info_content.class.php 221 2009-11-13 14:36:41Z vanpouckesven $
 * @package migration.lib.platform.dokeos185
 */

require_once dirname(__FILE__) . '/../../lib/import/import_user_info_content.class.php';
require_once dirname(__FILE__) . '/../../../repository/lib/content_object/userinfo_content/userinfo_content.class.php';
require_once dirname(__FILE__) . '/../../../application/lib/weblcms/content_object_publication.class.php';
require_once dirname(__FILE__) . '/../../../repository/lib/content_object/category/category.class.php';

/**
 * This class presents a Dokeos185 userinfo_content
 *
 * @author Sven Vanpoucke
 */
class Dokeos185UserinfoContent
{
    /**
     * Dokeos185UserinfoContent properties
     */
    const PROPERTY_ID = 'id';
    const PROPERTY_USER_ID = 'user_id';
    const PROPERTY_DEFINITION_ID = 'definition_id';
    const PROPERTY_EDITOR_IP = 'editor_ip';
    const PROPERTY_EDITION_TIME = 'edition_time';
    const PROPERTY_CONTENT = 'content';
    
    /**
     * Default properties stored in an associative array.
     */
    private $defaultProperties;

    /**
     * Creates a new Dokeos185UserinfoContent object
     * @param array $defaultProperties The default properties
     */
    function Dokeos185UserinfoContent($defaultProperties = array ())
    {
        $this->defaultProperties = $defaultProperties;
    }

    /**
     * Gets a default property by name.
     * @param string $name The name of the property.
     */
    function get_default_property($name)
    {
        return $this->defaultProperties[$name];
    }

    /**
     * Gets the default properties
     * @return array An associative array containing the properties.
     */
    function get_default_properties()
    {
        return $this->defaultProperties;
    }

    /**
     * Get the default properties
     * @return array The property names.
     */
    static function get_default_property_names()
    {
        return array(self :: PROPERTY_ID, self :: PROPERTY_USER_ID, self :: PROPERTY_DEFINITION_ID, self :: PROPERTY_EDITOR_IP, self :: PROPERTY_EDITION_TIME, self :: PROPERTY_CONTENT);
    }

    /**
     * Sets a default property by name.
     * @param string $name The name of the property.
     * @param mixed $value The new value for the property.
     */
    function set_default_property($name, $value)
    {
        $this->defaultProperties[$name] = $value;
    }

    /**
     * Sets the default properties of this class
     */
    function set_default_properties($defaultProperties)
    {
        $this->defaultProperties = $defaultProperties;
    }

    /**
     * Returns the id of this Dokeos185UserinfoContent.
     * @return the id.
     */
    function get_id()
    {
        return $this->get_default_property(self :: PROPERTY_ID);
    }

    /**
     * Returns the user_id of this Dokeos185UserinfoContent.
     * @return the user_id.
     */
    function get_user_id()
    {
        return $this->get_default_property(self :: PROPERTY_USER_ID);
    }

    /**
     * Returns the definition_id of this Dokeos185UserinfoContent.
     * @return the definition_id.
     */
    function get_definition_id()
    {
        return $this->get_default_property(self :: PROPERTY_DEFINITION_ID);
    }

    /**
     * Returns the editor_ip of this Dokeos185UserinfoContent.
     * @return the editor_ip.
     */
    function get_editor_ip()
    {
        return $this->get_default_property(self :: PROPERTY_EDITOR_IP);
    }

    /**
     * Returns the edition_time of this Dokeos185UserinfoContent.
     * @return the edition_time.
     */
    function get_edition_time()
    {
        return $this->get_default_property(self :: PROPERTY_EDITION_TIME);
    }

    /**
     * Returns the content of this Dokeos185UserinfoContent.
     * @return the content.
     */
    function get_content()
    {
        return $this->get_default_property(self :: PROPERTY_CONTENT);
    }

    /**
     * Gets all the userinfo content of a course
     * @param Array $parameters
     * @return Array with userinfo contents
     */
    static function get_all($parameters)
    {
        $old_mgdm = $parameters['old_mgdm'];
        
        $coursedb = $parameters['course']->get_db_name();
        $tablename = 'userinfo_content';
        $classname = 'Dokeos185UserinfoContent';
        
        return $old_mgdm->get_all($coursedb, $tablename, $classname, $tool_name, $parameters['offset'], $parameters['limit']);
    }

    static function get_database_table($parameters)
    {
        $array = array();
        $array['database'] = $parameters['course']->get_db_name();
        $array['table'] = 'userinfo_content';
        return $array;
    }

    /**
     * Checks if userinfo content is valid
     * @param Array $array
     * @return bool
     */
    function is_valid($array)
    {
        $course = $array['course'];
        if (! $this->get_user_id() || ! $this->get_content())
        {
            $mgdm = MigrationDataManager :: get_instance();
            $mgdm->add_failed_element($this->get_id(), $course->get_db_name() . '.userinfocontent');
            return false;
        }
        return true;
    }

    /**
     * Convert userinfo content to new lcms userinfo content
     * Gets the category of the userinfo content
     * @param Array $array
     * @return UserinfoContent
     */
    function convert_to_lcms($array)
    {
        $course = $array['course'];
        $mgdm = MigrationDataManager :: get_instance();
        
        $new_course_code = $mgdm->get_id_reference($course->get_code(), 'weblcms_course');
        
        if (! $new_user_id)
        {
            $new_user_id = $mgdm->get_owner($new_course_code);
        }
        
        //forum parameters
        $lcms_userinfo_content = new UserinfoContent();
        
        // Category for announcements already exists?
        $lcms_category_id = $mgdm->get_parent_id($new_user_id, 'category', Translation :: get('userinfo_contents'));
        if (! $lcms_category_id)
        {
            //Create category for tool in lcms
            $lcms_repository_category = new Category();
            $lcms_repository_category->set_owner_id($new_user_id);
            $lcms_repository_category->set_title(Translation :: get('userinfo_contents'));
            $lcms_repository_category->set_description('...');
            
            //Retrieve repository id from course
            $repository_id = $mgdm->get_parent_id($new_user_id, 'category', Translation :: get('MyRepository'));
            $lcms_repository_category->set_parent_id($repository_id);
            
            //Create category in database
            $lcms_repository_category->create();
            
            $lcms_userinfo_content->set_parent_id($lcms_repository_category->get_id());
        }
        else
        {
            $lcms_userinfo_content->set_parent_id($lcms_category_id);
        }
        
        $lcms_userinfo_content->set_title(substr($this->get_content(), 0, 20));
        
        $lcms_userinfo_content->set_description($this->get_content());
        
        $lcms_userinfo_content->set_creation_date($mgdm->make_unix_time($this->get_edition_time()));
        $lcms_userinfo_content->set_modification_date($mgdm->make_unix_time($this->get_edition_time()));
        
        $lcms_userinfo_content->set_owner_id($new_user_id);
        
        //create announcement in database
        $lcms_userinfo_content->create_all();
        
        /*
		//publication
		if($this->item_property->get_visibility() <= 1) 
		{
			$publication = new ContentObjectPublication();
			
			$publication->set_content_object($lcms_announcement);
			$publication->set_course_id($new_course_code);
			$publication->set_publisher_id($new_user_id);
			$publication->set_tool('announcement');
			$publication->set_category_id(0);
			//$publication->set_from_date(self :: $mgdm->make_unix_time($this->item_property->get_start_visible()));
			//$publication->set_to_date(self :: $mgdm->make_unix_time($this->item_property->get_end_visible()));
			$publication->set_from_date(0);
			$publication->set_to_date(0);
			$publication->set_publication_date(self :: $mgdm->make_unix_time($this->item_property->get_insert_date()));
			$publication->set_modified_date(self :: $mgdm->make_unix_time($this->item_property->get_lastedit_date()));
			//$publication->set_modified_date(0);
			//$publication->set_display_order_index($this->get_display_order());
			$publication->set_display_order_index(0);
			
			if($this->get_email_sent())
				$publication->set_email_sent($this->get_email_sent());
			else
				$publication->set_email_sent(0);
			
			$publication->set_hidden($this->item_property->get_visibility() == 1?0:1);
			
			//create publication in database
			$publication->create();
		}
		*/
        return $lcms_userinfo_content;
    }
}

?>
