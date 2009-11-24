<?php
/**
 * $Id: profile_publication.class.php 212 2009-11-13 13:38:35Z chellee $
 * @package application.profiler
 */
require_once Path :: get_application_path() . 'lib/profiler/profiler_data_manager.class.php';

/**
 *	This class represents a ProfilePublication.
 *
 *	ProfilePublication objects have a number of default properties:
 *	- id: the numeric ID of the ProfilePublication;
 *	- profile: the numeric object ID of the ProfilePublication (from the repository);
 *	- publisher: the publisher of the ProfilePublication;
 *	- published: the date when the ProfilePublication was "posted";
 *	@author Hans de Bisschop
 *	@author Dieter De Neef
 */
class ProfilePublication extends DataClass
{
    const CLASS_NAME = __CLASS__;
    const TABLE_NAME = 'publication';

    const PROPERTY_PROFILE = 'profile_id';
    const PROPERTY_PUBLISHER = 'publisher_id';
    const PROPERTY_PUBLISHED = 'published';
    const PROPERTY_CATEGORY = 'category_id';

    /**
     * Get the default properties of all ProfilePublications.
     * @return array The property names.
     */
    static function get_default_property_names()
    {
        return parent :: get_default_property_names(array(self :: PROPERTY_CATEGORY, self :: PROPERTY_PROFILE, self :: PROPERTY_PUBLISHER, self :: PROPERTY_PUBLISHED));
    }

    /**
     * inherited
     */
    function get_data_manager()
    {
        return ProfilerDataManager :: get_instance();
    }

    /**
     * Returns the learning object id from this ProfilePublication object
     * @return int The Profile ID
     */
    function get_profile()
    {
        return $this->get_default_property(self :: PROPERTY_PROFILE);
    }

    /**
     * Returns the user of this ProfilePublication object
     * @return int the user
     */
    function get_publisher()
    {
        return $this->get_default_property(self :: PROPERTY_PUBLISHER);
    }

    /**
     * Returns the published timestamp of this ProfilePublication object
     * @return Timestamp the published date
     */
    function get_published()
    {
        return $this->get_default_property(self :: PROPERTY_PUBLISHED);
    }

    function get_category()
    {
        return $this->get_default_property(self :: PROPERTY_CATEGORY);
    }

    /**
     * Sets the learning object id of this ProfilePublication.
     * @param Int $id the profile ID.
     */
    function set_profile($id)
    {
        $this->set_default_property(self :: PROPERTY_PROFILE, $id);
    }

    /**
     * Sets the user of this ProfilePublication.
     * @param int $user the User.
     */
    function set_publisher($publisher)
    {
        $this->set_default_property(self :: PROPERTY_PUBLISHER, $publisher);
    }

    /**
     * Sets the published date of this ProfilePublication.
     * @param int $published the timestamp of the published date.
     */
    function set_published($published)
    {
        $this->set_default_property(self :: PROPERTY_PUBLISHED, $published);
    }

    function set_category($category)
    {
        $this->set_default_property(self :: PROPERTY_CATEGORY, $category);
    }

    function get_publication_object()
    {
        $rdm = RepositoryDataManager :: get_instance();
        return $rdm->retrieve_content_object($this->get_profile());
    }

    function get_publication_publisher()
    {
        $udm = UserDataManager :: get_instance();
        return $udm->retrieve_user($this->get_publisher());
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
        $pmdm = ProfilerDataManager :: get_instance();
        return $pmdm->create_profile_publication($this);
    }

    static function get_table_name()
    {
        return self :: TABLE_NAME;
    }
}
?>
