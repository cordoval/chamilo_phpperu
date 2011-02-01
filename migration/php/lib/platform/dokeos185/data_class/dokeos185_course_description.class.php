<?php
namespace migration;
use common\libraries\Translation;
use repository\RepositoryDataManager;
use common\libraries\Utilities;
use repository\content_object\description\Description;
use application\weblcms\ContentObjectPublication;

/**
 * $Id: dokeos185_course_description.class.php 221 2009-11-13 14:36:41Z vanpouckesven $
 * @package migration.platform.dokeos185
 */
require_once dirname(__FILE__) . '/../dokeos185_course_data_migration_data_class.class.php';

/**
 * This class represents an old Dokeos 1.8.5 Course Description
 *
 * @author Sven Vanpoucke
 */
class Dokeos185CourseDescription extends Dokeos185CourseDataMigrationDataClass
{
    const CLASS_NAME = __CLASS__;
    const TABLE_NAME = 'course_description';
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
    function __construct($defaultProperties = array())
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
    function is_valid()
    {

        if ($this->get_id() == NULL || !($this->get_title() || $this->get_content()))
        {
            $this->create_failed_element($this->get_id());
            $this->set_message(Translation :: get('GeneralInvalidMessage', array('TYPE' => 'course_description', 'ID' => $this->get_id())));

            return false;
        }
        return true;
    }

    /**
     * Convert to new course description
     * @param Course $Course the course where the description belongs to
     * @return the new course description
     */
    function convert_data()
    {
        $chamilo_description = new Description();

        if (!$this->get_title())
        {
            $chamilo_description->set_title(Translation :: get('Description'));
        }
        else
        {
            $chamilo_description->set_title($this->get_title());
        }

        if (!$this->get_content())
        {
            $chamilo_description->set_description($this->get_title());
        }
        else
        {
            $content = $this->parse_text_field($this->get_content());
            $chamilo_description->set_description($content);
        }

        $new_user_id = $this->get_id_reference($this->get_data_manager()->get_admin_id(), 'main_database.user');
        $new_course_code = $this->get_id_reference($this->get_course()->get_code(), 'main_database.course');

        // Category for contents already exists?
        $chamilo_category_id = RepositoryDataManager :: get_repository_category_by_name_or_create_new($new_user_id, Translation :: get('Descriptions'));

        $chamilo_description->set_parent_id($chamilo_category_id);

        $chamilo_description->set_owner_id($new_user_id);

        //create in chamilo db
        $chamilo_description->create();

        $this->create_publication($chamilo_description, $new_course_code, $new_user_id, 'description');

//        $publication = new ContentObjectPublication();
//
//        $publication->set_content_object($chamilo_description);
//        $publication->set_content_object_id($chamilo_description->get_id());
//        $publication->set_course_id($new_course_code);
//        $publication->set_publisher_id($new_user_id);
//        $publication->set_tool('description');
//
//        $publication->set_category_id(0);
//
//
//        $publication->set_from_date(0);
//        $publication->set_to_date(0);
//        $publication->set_publication_date(0);
//        $publication->set_modified_date(0);
//        //$publication->set_modified_date(0);
//        //$publication->set_display_order_index($this->get_display_order());
//        $publication->set_display_order_index(0);
//
//        $publication->set_email_sent($this->get_email_sent());
//
//        //$publication->set_hidden($this->item_property->get_visibility() == 1 ? 0 : 1);
//        $publication->create();
        $this->set_message(Translation :: get('GeneralConvertedMessage', array('TYPE' => 'course_description', 'OLD_ID' => $this->get_id(), 'NEW_ID' => $chamilo_description->get_id())));

        return $chamilo_description;
    }

    static function get_table_name()
    {
                return Utilities :: camelcase_to_underscores(substr(Utilities :: get_classname_from_namespace(__CLASS__), 9));  ;
    }

    static function get_class_name()
    {
        return self :: CLASS_NAME;
    }

}

?>