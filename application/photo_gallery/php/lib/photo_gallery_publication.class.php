<?php
class PhotoGalleryPublication extends DataClass
{
	const CLASS_NAME = __CLASS__;
    const TABLE_NAME = 'publication';

    const PROPERTY_CONTENT_OBJECT_ID = 'content_object_id';
    const PROPERTY_PUBLISHER = 'publisher_id';
    const PROPERTY_PUBLISHED = 'published';

    private $target_groups;
    private $target_users;

    /**
     * Get the default properties of all CalendarEventPublications.
     * @return array The property names.
     */
    static function get_default_property_names()
    {
        return parent :: get_default_property_names(array(self :: PROPERTY_CONTENT_OBJECT_ID, self :: PROPERTY_PUBLISHER, self :: PROPERTY_PUBLISHED));
    }

    /**
     * inherited
     */
    function get_data_manager()
    {
        return PhotoGalleryDataManager :: get_instance();
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
        $pcdm = PhotoGalleryDataManager :: get_instance();
        return $pcdm->create_photo_gallery_publication($this);
    }

    /**
     * Create all needed for migration tool to set the published time manually
     */
    function create_all()
    {
        $pmdm = PhotoGalleryDataManager :: get_instance();
        return $pmdm->create_photo_gallery_publication($this);
    }

    function get_target_users()
    {
        if (! isset($this->target_users))
        {
            $pcdm = PhotoGalleryDataManager :: get_instance();
            $this->target_users = $pcdm->retrieve_photo_gallery_publication_target_users($this);
        }

        return $this->target_users;
    }

    function get_target_groups()
    {
        if (! isset($this->target_groups))
        {
            $pcdm = PhotoGalleryDataManager :: get_instance();
            $this->target_groups = $pcdm->retrieve_photo_gallery_publication_target_users($this);
        }

        return $this->target_groups;
    }

    function set_target_users($target_users)
    {
        $this->target_users = $target_users;
    }

    function set_target_groups($target_groups)
    {
        $this->target_groups = $target_groups;
    }

    function is_for_nobody()
    {
        return (count($this->get_target_users()) == 0 && count($this->get_target_groups()) == 0);
    }

    function is_target($user)
    {
        if ($user->is_platform_admin() || $user->get_id() == $this->get_publisher())
        {
            return true;
        }
        
    	
    	if ($this->is_for_nobody())
        {
            return false;
        }

        $user_id = $user->get_id();

        $target_users = $this->get_target_users();
        $target_groups = $this->get_target_groups();

        $user_groups = $user->get_groups(true);

        if (in_array($user_id, $target_users))
        {
            return true;
        }
        else
        {
            foreach ($user_groups as $user_group)
            {
                if (in_array($user_group, $target_groups))
                {
                    return true;
                }
            }
        }

        return false;
    }

    static function get_table_name()
    {
        return self :: TABLE_NAME;
    }
}
?>