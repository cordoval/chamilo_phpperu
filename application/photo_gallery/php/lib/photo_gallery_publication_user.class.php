<?php
/**
 * $Id: photo_gallery_publication_user.class.php 201 2009-11-13 12:34:51Z chellee $
 * @package application.photo_gallery
 */

/**
 * This class describes a ContentObjectPublicationUser data object
 *
 * @author Hans De Bisschop
 */
class PhotoGalleryPublicationUser
{
    const CLASS_NAME = __CLASS__;
    const TABLE_NAME = 'publication_user';
    
    /**
     * ContentObjectPublicationUser properties
     */
    const PROPERTY_PUBLICATION = 'publication_id';
    const PROPERTY_USER = 'user_id';
    
    /**
     * Default properties stored in an associative array.
     */
    private $defaultProperties;

    /**
     * Creates a new ContentObjectPublicationUser object
     * @param array $defaultProperties The default properties
     */
    function PhotoGalleyPublicationUser($defaultProperties = array ())
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
        return array(self :: PROPERTY_PUBLICATION, self :: PROPERTY_USER);
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
     * Returns the publication of this ContentObjectPublicationUser.
     * @return the publication.
     */
    function get_publication()
    {
        return $this->get_default_property(self :: PROPERTY_PUBLICATION);
    }

    /**
     * Sets the publication of this ContentObjectPublicationUser.
     * @param publication
     */
    function set_publication($publication)
    {
        $this->set_default_property(self :: PROPERTY_PUBLICATION, $publication);
    }

    /**
     * Returns the user of this ContentObjectPublicationUser.
     * @return the user.
     */
    function get_user()
    {
        return $this->get_default_property(self :: PROPERTY_USER);
    }

    /**
     * Sets the user of this ContentObjectPublicationUser.
     * @param user
     */
    function set_user($user)
    {
        $this->set_default_property(self :: PROPERTY_USER, $user);
    }

    function delete()
    {
        $dm = PhotoGalleryDataManager :: get_instance();
        return $dm->delete_calendar_event_publication_user($this);
    }

    function create()
    {
        $dm = PhotoGalleryDataManager :: get_instance();
        return $dm->create_photo_gallery_publication_user($this);
    }

    function update()
    {
        $dm = PhotoGalleryDataManager :: get_instance();
        return $dm->update_calendar_event_publication_user($this);
    }

    static function get_table_name()
    {
        return self :: TABLE_NAME;
    }
}

?>