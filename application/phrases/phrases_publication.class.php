<?php
/**
 * $Id: phrases_publication.class.php 201 2009-11-13 12:34:51Z chellee $
 * @package application.personal_calendar
 */
require_once Path :: get_application_path() . '/lib/phrases/phrases_data_manager.class.php';

/**
 * This class represents a CalendarEventPublication.
 *
 * @author Hans de Bisschop
 */
class PhrasesPublication extends DataClass
{
    const CLASS_NAME = __CLASS__;
    const TABLE_NAME = 'publication';
    
    const PROPERTY_CONTENT_OBJECT_ID = 'content_object_id';
    const PROPERTY_PUBLISHER = 'publisher_id';
    const PROPERTY_PUBLISHED = 'published';
    const PROPERTY_MASTERY_LEVEL_ID = 'mastery_level_id';
    const PROPERTY_LANGUAGE_ID = 'language_id';
    const PROPERTY_CATEGORY_ID = 'category_id';

    /**
     * Get the default properties of all CalendarEventPublications.
     * @return array The property names.
     */
    static function get_default_property_names()
    {
        return parent :: get_default_property_names(array(self :: PROPERTY_CONTENT_OBJECT_ID, self :: PROPERTY_PUBLISHER, self :: PROPERTY_PUBLISHED, self :: PROPERTY_MASTERY_LEVEL_ID, self :: PROPERTY_LANGUAGE_ID, self :: PROPERTY_CATEGORY_ID));
    }

    /**
     * inherited
     */
    function get_data_manager()
    {
        return PhrasesDataManager :: get_instance();
    }

    /**
     * Returns the learning object id from this CalendarEventPublication object
     * @return int The CalendarEvent ID
     */
    function get_content_object_id()
    {
        return $this->get_default_property(self :: PROPERTY_CONTENT_OBJECT_ID);
    }

    /**
     * Returns the user of this CalendarEventPublication object
     * @return int the user
     */
    function get_publisher()
    {
        return $this->get_default_property(self :: PROPERTY_PUBLISHER);
    }

    /**
     * Returns the published timestamp of this CalendarEventPublication object
     * @return Timestamp the published date
     */
    function get_published()
    {
        return $this->get_default_property(self :: PROPERTY_PUBLISHED);
    }

    /**
     * Sets the learning object id of this CalendarEventPublication.
     * @param Int $id the calendar_event ID.
     */
    function set_content_object_id($id)
    {
        $this->set_default_property(self :: PROPERTY_CONTENT_OBJECT_ID, $id);
    }

    /**
     * Sets the user of this CalendarEventPublication.
     * @param int $user the User.
     */
    function set_publisher($publisher)
    {
        $this->set_default_property(self :: PROPERTY_PUBLISHER, $publisher);
    }

    /**
     * Sets the published date of this CalendarEventPublication.
     * @param int $published the timestamp of the published date.
     */
    function set_published($published)
    {
        $this->set_default_property(self :: PROPERTY_PUBLISHED, $published);
    }

    function get_mastery_level_id()
    {
        return $this->get_default_property(self :: PROPERTY_MASTERY_LEVEL_ID);
    }

    function get_language_id()
    {
        return $this->get_default_property(self :: PROPERTY_LANGUAGE_ID);
    }

    function get_category_id()
    {
        return $this->get_default_property(self :: PROPERTY_CATEGORY_ID);
    }

    function set_mastery_level_id($mastery_level_id)
    {
        $this->set_default_property(self :: PROPERTY_MASTERY_LEVEL_ID, $mastery_level_id);
    }

    function set_language_id($language_id)
    {
        $this->set_default_property(self :: PROPERTY_LANGUAGE_ID, $language_id);
    }

    function set_category_id($category_id)
    {
        $this->set_default_property(self :: PROPERTY_CATEGORY_ID, $category_id);
    }

    function get_publication_object()
    {
        if (! isset($this->publication_object))
        {
            $rdm = RepositoryDataManager :: get_instance();
            $this->publication_object = $rdm->retrieve_content_object($this->get_content_object_id());
        }
        return $this->publication_object;
    }

    function get_publication_publisher()
    {
        $udm = UserDataManager :: get_instance();
        return $udm->retrieve_user($this->get_publisher());
    }
    
    function get_mastery_level()
    {
        return $this->get_data_manager()->retrieve_phrases_mastery_level($this->get_mastery_level_id());
    }

    /**
     * Instructs the data manager to create the personal message publication, making it
     * persistent. Also assigns a unique ID to the publication and sets
     * the publication's creation date to the current time.
     * @return boolean True if creation succeeded, false otherwise.
     */
    function create()
    {
        $now = time();
        $this->set_published($now);
        $pcdm = PhrasesDataManager :: get_instance();
        return $pcdm->create_phrases_publication($this);
    }

    /**
     * Create all needed for migration tool to set the published time manually
     */
    function create_all()
    {
        $pmdm = PhrasesDataManager :: get_instance();
        return $pmdm->create_phrases_publication($this);
    }

    static function get_table_name()
    {
        return self :: TABLE_NAME;
    }
}
?>