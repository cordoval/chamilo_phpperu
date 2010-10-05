<?php

/**
 * $Id: dokeos185_dropbox_feedback.class.php 221 2009-11-13 14:36:41Z vanpouckesven $
 * @package migration.lib.platform.dokeos185
 */
require_once dirname(__FILE__) . '/../dokeos185_course_data_migration_data_class.class.php';

/**
 * This class presents a Dokeos185 dropbox_feedback
 *
 * @author Sven Vanpoucke
 */
class Dokeos185DropboxFeedback extends Dokeos185CourseDataMigrationDataClass
{
    const CLASS_NAME = __CLASS__;
    const TABLE_NAME = 'dropbox_feedback';
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
    function Dokeos185DropboxFeedback($defaultProperties = array())
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
    function is_valid()
    {
        //$this->set_item_property($this->get_data_manager()->get_item_property($this->get_course(), 'dropbox', $this->get_feedback_id())); //no instance in item_property table dokeos185

        if (!$this->get_feedback_id() || !$this->get_feedback() || !$this->get_feedback_date())
        {
            $this->create_failed_element($this->get_feedback_id());
            return false;
        }
        return true;
    }

    /**
     * Convert to new dropbox feedback
     * @param array $courses the parameters for the conversion
     * @return the new dropbox feedback
     */
    function convert_data()
    {
        $new_user_id = $this->get_id_reference($this->get_author_user_id(), 'main_database.user');
        $new_course_code = $this->get_id_reference($this->get_course()->get_code(), 'main_database.course');
        $feedback_content_object_id = $this->get_id_reference($this->get_file_id(), $this->get_database_name() . '.dropbox_file'); //repository content object item to which it refers in old dokeos

        if (!$new_user_id)
        {
            $new_user_id = $this->get_owner($new_course_code);
        }

        //dropbox_feedback parameters
        $chamilo_course_dropbox_feedback = new Feedback();

        // Category for dropbox already exists?
        $chamilo_category_id = RepositoryDataManager :: get_repository_category_by_name_or_create_new($new_user_id, Translation :: get('Documents'));

        $chamilo_course_dropbox_feedback->set_parent_id($chamilo_category_id);


        $chamilo_course_dropbox_feedback->set_title(substr($this->get_feedback(), 0, 20));
        $chamilo_course_dropbox_feedback->set_description($this->get_feedback());

        $chamilo_course_dropbox_feedback->set_owner_id($new_user_id);
        $chamilo_course_dropbox_feedback->set_creation_date(strtotime($this->get_feedback_date()));
        $chamilo_course_dropbox_feedback->set_modification_date(strtotime($this->get_feedback_date()));


        //create feedback in database
        $chamilo_course_dropbox_feedback->create_all();

        //attach it to a publication
        $feedback_publication = new FeedbackPublication();
        $feedback_publication->set_application('weblcms');
        $feedback_publication->set_fid($chamilo_course_dropbox_feedback->get_id());

        //feedback is attached to publication, not to content object itself
        $condition = new EqualityCondition(ContentObjectPublication :: PROPERTY_CONTENT_OBJECT_ID, $feedback_content_object_id);
        $publication = WeblcmsDataManager::get_instance()->retrieve_content_object_publications($condition)->next_result();

        $feedback_publication->set_pid($publication->get_id());
        $feedback_publication->set_creation_date(strtotime($chamilo_course_dropbox_feedback->get_creation_date()));
        $feedback_publication->set_modification_date(strtotime($chamilo_course_dropbox_feedback->get_modification_date()));
        $feedback_publication->create();

        return $chamilo_course_dropbox_feedback;
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