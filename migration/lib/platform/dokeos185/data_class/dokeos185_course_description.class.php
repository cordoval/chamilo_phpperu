<?php

/**
 * $Id: dokeos185_course_description.class.php 221 2009-11-13 14:36:41Z vanpouckesven $
 * @package migration.platform.dokeos185
 */

require_once dirname(__FILE__) . '/../../lib/import/import_course_description.class.php';
require_once dirname(__FILE__) . '/../../../repository/lib/category_manager/repository_category.class.php';
require_once dirname(__FILE__) . '/../../../repository/lib/content_object/description/description.class.php';
require_once dirname(__FILE__) . '/../../../application/lib/weblcms/content_object_publication.class.php';

/**
 * This class represents an old Dokeos 1.8.5 Course Description
 *
 * @author Sven Vanpoucke
 */

class Dokeos185CourseDescription extends Dokeos185MigrationDataClass
{
    
    /**
     * course description properties
     */
    const PROPERTY_ID = 'id';
    const PROPERTY_TITLE = 'title';
    const PROPERTY_CONTENT = 'content';
    
    /**
     * Default properties of the course description object, stored in an associative
     * array.
     */
    private $defaultProperties;

    /**
     * Creates a new course description object.
     * @param array $defaultProperties The default properties of the course description
     *                                 object. Associative array.
     */
    function Dokeos185CourseDescription($defaultProperties = array ())
    {
        $this->defaultProperties = $defaultProperties;
    }

    /**
     * Gets a default property of this course description object by name.
     * @param string $name The name of the property.
     */
    function get_default_property($name)
    {
        return $this->defaultProperties[$name];
    }

    /**
     * Gets the default properties of this course description.
     * @return array An associative array containing the properties.
     */
    function get_default_properties()
    {
        return $this->defaultProperties;
    }

    /**
     * Get the default properties of all link categories.
     * @return array The property names.
     */
    static function get_default_property_names()
    {
        return array(self :: PROPERTY_ID, self :: PROPERTY_TITLE, self :: PROPERTY_CONTENT);
    }

    /**
     * Sets a default property of this course description by name.
     * @param string $name The name of the property.
     * @param mixed $value The new value for the property.
     */
    function set_default_property($name, $value)
    {
        $this->defaultProperties[$name] = $value;
    }

    /**
     * Sets the default properties of this link.
     * @param array $defaultProperties An associative array containing the properties.
     */
    function set_default_properties($defaultProperties)
    {
        return $this->defaultProperties = $defaultProperties;
    }

    /**
     * Returns the id of this course description.
     * @return int The id.
     */
    function get_id()
    {
        return $this->get_default_property(self :: PROPERTY_ID);
    }

    /**
     * Returns the title of this course description.
     * @return String The title.
     */
    function get_title()
    {
        return $this->get_default_property(self :: PROPERTY_TITLE);
    }

    /**
     * Returns the content of this course content.
     * @return String The content.
     */
    function get_content()
    {
        return $this->get_default_property(self :: PROPERTY_CONTENT);
    }

    /**
     * Check if the course description is valid
     * @param Course $Course the course where the description belongs to
     * @return true if the course description is valid 
     */
    function is_valid($array)
    {
        $mgdm = MigrationDataManager :: get_instance();
        $course = $array['course'];
        if (! $this->get_id() || ! ($this->get_title() || $this->get_content()))
        {
            $mgdm->add_failed_element($this->get_id(), $course->get_db_name() . '.description');
            return false;
        }
        return true;
    }

    /**
     * Convert to new course description
     * @param Course $Course the course where the description belongs to
     * @return the new course description
     */
    function convert_data
    {
        $old_mgdm = $array['old_mgdm'];
        $mgdm = MigrationDataManager :: get_instance();
        $course = $array['course'];
        $lcms_content = new Description();
        
        if (! $this->get_title())
            $lcms_content->set_title(substr($this->get_content(), 0, 20));
        else
            $lcms_content->set_title($this->get_title());
        
        if (! $this->get_content())
            $lcms_content->set_description($this->get_title());
        else
            $lcms_content->set_description($this->get_content());
        
        $user_id = $mgdm->get_id_reference($old_mgdm->get_old_admin_id(), 'user_user');
        $new_course_code = $mgdm->get_id_reference($course->get_code(), 'weblcms_course');
        
        // Category for contents already exists?
        $lcms_category_id = $mgdm->get_parent_id($user_id, 'category', Translation :: get('descriptions'));
        if (! $lcms_category_id)
        {
            ///Create category for user in lcms
            $lcms_repository_category = new RepositoryCategory();
            $lcms_repository_category->set_user_id($user_id);
            $lcms_repository_category->set_name(Translation :: get('courseDescription'));
            $lcms_repository_category->set_parent(0);
            
            //Create category in database
            $lcms_repository_category->create();
            
            $lcms_content->set_parent_id($lcms_repository_category->get_id());
        }
        else
        {
            $lcms_content->set_parent_id($lcms_category_id);
        }
        
        $lcms_content->set_owner_id($user_id);
        $lcms_content->create();
        
        $publication = new ContentObjectPublication();
        
        $publication->set_content_object($lcms_content);
        $publication->set_course_id($new_course_code);
        $publication->set_publisher_id($user_id);
        $publication->set_tool('description');
        $publication->set_category_id(0);
        $publication->set_from_date(0);
        $publication->set_to_date(0);
        
        $now = time();
        $publication->set_publication_date($now);
        $publication->set_modified_date($now);
        
        $publication->set_display_order_index(0);
        $publication->set_email_sent(0);
        $publication->set_hidden(0);
        
        //create publication in database
        $publication->create();
        
        return $lcms_content;
    
    }

    /**
     * Retrieve all course descriptions from the database
     * @param array $parameters parameters for the retrieval
     * @return array of course descriptions
     */
    static function retrieve_data($parameters)
    {
        $old_mgdm = $parameters['old_mgdm'];
        
        $db = $parameters['course']->get_db_name();
        $tablename = 'course_description';
        $classname = 'Dokeos185CourseDescription';
        
        return $old_mgdm->get_all($db, $tablename, $classname, $tool_name, $parameters['offset'], $parameters['limit']);
    }

    static function get_database_table($parameters)
    {
        $array = array();
        $array['database'] = $parameters['course']->get_db_name();
        $array['table'] = 'course_description';
        return $array;
    }
}
?>