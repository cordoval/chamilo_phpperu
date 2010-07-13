<?php
/**
 * $Id: dokeos185_tool_intro.class.php 221 2009-11-13 14:36:41Z vanpouckesven $
 * @package migration.lib.platform.dokeos185
 */

require_once dirname(__FILE__) . '/../../lib/import/import_tool_intro.class.php';
require_once dirname(__FILE__) . '/../../../repository/lib/category_manager/repository_category.class.php';
require_once dirname(__FILE__) . '/../../../repository/lib/content_object/description/description.class.php';
require_once dirname(__FILE__) . '/../../../application/lib/weblcms/content_object_publication.class.php';

/**
 * This class presents a Dokeos185 tool_intro
 *
 * @author Sven Vanpoucke
 */
class Dokeos185ToolIntro extends Dokeos185MigrationDataClass
{
    private static $mgdm;
    
    /**
     * Dokeos185ToolIntro properties
     */
    const PROPERTY_ID = 'id';
    const PROPERTY_INTRO_TEXT = 'intro_text';
    
    /**
     * Default properties stored in an associative array.
     */
    private $defaultProperties;

    /**
     * Creates a new Dokeos185ToolIntro object
     * @param array $defaultProperties The default properties
     */
    function Dokeos185ToolIntro($defaultProperties = array ())
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
        return array(self :: PROPERTY_ID, self :: PROPERTY_INTRO_TEXT);
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
     * Returns the id of this Dokeos185ToolIntro.
     * @return the id.
     */
    function get_id()
    {
        return $this->get_default_property(self :: PROPERTY_ID);
    }

    /**
     * Returns the intro_text of this Dokeos185ToolIntro.
     * @return the intro_text.
     */
    function get_intro_text()
    {
        return $this->get_default_property(self :: PROPERTY_INTRO_TEXT);
    }

    /**
     * Checks if a tool intro is valid
     * @param Array $array
     * @return Boolean
     */
    function is_valid($array)
    {
        $course = $array['course'];
        $mgdm = MigrationDataManager :: get_instance();
        if (! $this->get_intro_text())
        {
            $mgdm->add_failed_element($this->get_id(), $course->get_db_name() . '.toolintro');
            unset($mgdm);
            unset($course);
            $array = array();
            unset($array);
            return false;
        }
        unset($mgdm);
        unset($course);
        $array = array();
        unset($array);
        return true;
    }

    /**
     * Convert to description, set category, make publication
     * @param Array $array
     * @return Description
     */
    function convert_data
    {
        $course = $array['course'];
        $mgdm = MigrationDataManager :: get_instance();
        
        $new_course_code = $mgdm->get_id_reference($course->get_code(), 'weblcms_course');
        $user_id = $mgdm->get_owner($new_course_code);
        
        $lcms_tool_intro = new Description();
        $lcms_tool_intro->set_title($this->get_intro_text());
        
        $lcms_tool_intro->set_description($this->get_intro_text());
        
        // Category for contents already exists?
        $lcms_category_id = $mgdm->get_parent_id($user_id, 'category', Translation :: get('descriptions'));
        if (! $lcms_category_id)
        {
            
            //Create category for tool in lcms
            $lcms_repository_category = new RepositoryCategory();
            $lcms_repository_category->set_user_id($user_id);
            $lcms_repository_category->set_name(Translation :: get('toolIntro'));
            $lcms_repository_category->set_parent(0);
            
            //Create category in database
            $lcms_repository_category->create();
            
            $lcms_tool_intro->set_parent_id($lcms_repository_category->get_id());
        }
        else
        {
            $lcms_tool_intro->set_parent_id($lcms_category_id);
        }
        
        $lcms_tool_intro->set_owner_id($user_id);
        $lcms_tool_intro->create();
        
        $publication = new ContentObjectPublication();
        $publication->set_content_object($lcms_tool_intro);
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
        
        unset($course);
        unset($mgdm);
        unset($new_course_code);
        unset($user_id);
        unset($lcms_category_id);
        unset($lcms_repository_category);
        unset($repository_id);
        unset($publication);
        unset($now);
        $array = array();
        unset($array);
        return $lcms_tool_intro;
    
    }

    /**
     * Get all the tool intro's of a course
     * @param Array $parameters
     * @return array of dokeos185toolintro
     */
    static function retrieve_data($parameters)
    {
        $old_mgdm = $parameters['old_mgdm'];
        return $old_mgdm->get_all($parameters['course']->get_db_name(), 'tool_intro', 'Dokeos185ToolIntro', $tool_name, $parameters['offset'], $parameters['limit']);
    }

    static function get_database_table($parameters)
    {
        $array = array();
        $array['database'] = $parameters['course']->get_db_name();
        $array['table'] = 'tool_intro';
        $parameters = array();
        unset($parameters);
        return $array;
    }
}

?>