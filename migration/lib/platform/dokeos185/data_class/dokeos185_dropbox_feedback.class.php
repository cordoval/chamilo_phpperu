<?php
/**
 * $Id: dokeos185_dropbox_feedback.class.php 221 2009-11-13 14:36:41Z vanpouckesven $
 * @package migration.lib.platform.dokeos185
 */

require_once dirname(__FILE__) . '/../../lib/import/import_dropbox_feedback.class.php';
require_once dirname(__FILE__) . '/../../../repository/lib/content_object/feedback/feedback.class.php';
require_once dirname(__FILE__) . '/../../../application/lib/weblcms/content_object_publication.class.php';
require_once dirname(__FILE__) . '/../../../repository/lib/category_manager/repository_category.class.php';

/**
 * This class presents a Dokeos185 dropbox_feedback
 *
 * @author Sven Vanpoucke
 */
class Dokeos185DropboxFeedback extends MigrationDataClass
{
    private static $mgdm;
    /**
     * Dokeos185DropboxFeedback properties
     */
    const PROPERTY_FEEDBACK_ID = 'feedback_id';
    const PROPERTY_FILE_ID = 'file_id';
    const PROPERTY_AUTHOR_USER_ID = 'author_user_id';
    const PROPERTY_FEEDBACK = 'feedback';
    const PROPERTY_FEEDBACK_DATE = 'feedback_date';
    
    /**
     * Default properties stored in an associative array.
     */
    private $defaultProperties;

    /**
     * Creates a new Dokeos185DropboxFeedback object
     * @param array $defaultProperties The default properties
     */
    function Dokeos185DropboxFeedback($defaultProperties = array ())
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
        return array(self :: PROPERTY_FEEDBACK_ID, self :: PROPERTY_FILE_ID, self :: PROPERTY_AUTHOR_USER_ID, self :: PROPERTY_FEEDBACK, self :: PROPERTY_FEEDBACK_DATE);
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
     * Returns the feedback_id of this Dokeos185DropboxFeedback.
     * @return the feedback_id.
     */
    function get_feedback_id()
    {
        return $this->get_default_property(self :: PROPERTY_FEEDBACK_ID);
    }

    /**
     * Returns the file_id of this Dokeos185DropboxFeedback.
     * @return the file_id.
     */
    function get_file_id()
    {
        return $this->get_default_property(self :: PROPERTY_FILE_ID);
    }

    /**
     * Returns the author_user_id of this Dokeos185DropboxFeedback.
     * @return the author_user_id.
     */
    function get_author_user_id()
    {
        return $this->get_default_property(self :: PROPERTY_AUTHOR_USER_ID);
    }

    /**
     * Returns the feedback of this Dokeos185DropboxFeedback.
     * @return the feedback.
     */
    function get_feedback()
    {
        return $this->get_default_property(self :: PROPERTY_FEEDBACK);
    }

    /**
     * Returns the feedback_date of this Dokeos185DropboxFeedback.
     * @return the feedback_date.
     */
    function get_feedback_date()
    {
        return $this->get_default_property(self :: PROPERTY_FEEDBACK_DATE);
    }

    /**
     * Check if the dropbox feedback is valid
     * @param array $array the parameters for the validation
     */
    function is_valid($array)
    {
        $course = $array['course'];
        $mgdm = MigrationDataManager :: get_instance();
        if (! $this->get_feedback_id() || ! $this->get_feedback() || ! $this->get_feedback_date())
        {
            $mgdm->add_failed_element($this->get_feedback_id(), $course->get_db_name() . '.dropbox_feedback');
            return false;
        }
        return true;
    }

    /**
     * Convert to new dropbox feedback
     * @param array $courses the parameters for the conversion
     * @return the new dropbox feedback
     */
    function convert_data
    {
        $course = $courses['course'];
        $mgdm = MigrationDataManager :: get_instance();
        $new_user_id = $mgdm->get_id_reference($this->get_author_user_id(), 'user_user');
        $new_course_code = $mgdm->get_id_reference($course->get_code(), 'weblcms_course');
        
        if (! $new_user_id)
        {
            $new_user_id = $mgdm->get_owner($new_course_code);
        }
        
        //dropbox_feedback parameters
        $lcms_dropbox_feedback = new Feedback();
        
        // Category for dropbox already exists?
        $lcms_category_id = $mgdm->get_parent_id($new_user_id, 'dropbox', Translation :: get('dropboxes'));
        if (! $lcms_category_id)
        {
            //Create category for tool in lcms
            $lcms_repository_category = new RepositoryCategory();
            $lcms_repository_category->set_user_id($new_user_id);
            $lcms_repository_category->set_name(Translation :: get('DropboxFeedback'));
            $lcms_repository_category->set_parent(0);
            
            //Create category in database
            $lcms_repository_category->create();
            
            $lcms_dropbox_feedback->set_parent_id($lcms_repository_category->get_id());
        }
        else
        {
            $lcms_dropbox_feedback->set_parent_id($lcms_category_id);
        }
        
        $lcms_dropbox_feedback->set_title(substr($this->get_feedback(), 0, 20));
        $lcms_dropbox_feedback->set_description($this->get_feedback());
        
        $lcms_dropbox_feedback->set_owner_id($new_user_id);
        $lcms_dropbox_feedback->set_creation_date($mgdm->make_unix_time($this->get_feedback_date()));
        $lcms_dropbox_feedback->set_modification_date($mgdm->make_unix_time($this->get_feedback_date()));
        
        //create announcement in database
        $lcms_dropbox_feedback->create_all();
        
        return $lcms_dropbox_feedback;
    }

    /**
     * Retrieve all dropbox feedbacks from the database
     * @param array $parameters parameters for the retrieval
     * @return array of dropbox feedbacks
     */
    static function retrieve_data($parameters)
    {
        $old_mgdm = $parameters['old_mgdm'];
        
        $coursedb = $parameters['course']->get_db_name();
        $tablename = 'dropbox_feedback';
        $classname = 'Dokeos185DropboxFeedback';
        
        return $old_mgdm->get_all($coursedb, $tablename, $classname, $tool_name, $parameters['offset'], $parameters['limit']);
    }

    static function get_database_table($parameters)
    {
        $array = array();
        $array['database'] = $parameters['course']->get_db_name();
        $array['table'] = 'dropbox_feedback';
        return $array;
    }
}

?>