<?php
/**
 * $Id: content_object_publication_user.class.php 218 2009-11-13 14:21:26Z kariboe $
 * @package application.lib.weblcms
 */


/**
 * This class describes a ContentObjectPublicationUser data object
 *
 * @author Hans De Bisschop
 */
class ContentObjectPublicationUser extends DataClass
{
    const CLASS_NAME = __CLASS__;
    
    /**
     * ContentObjectPublicationUser properties
     */
    const PROPERTY_PUBLICATION = 'publication_id';
    const PROPERTY_USER = 'user_id';

    /**
     * Get the default properties
     * @return array The property names.
     */
    static function get_default_property_names()
    {
        return array(self :: PROPERTY_PUBLICATION, self :: PROPERTY_USER);
    }

    /**
     * inherited
     */
    function get_data_manager()
    {
        return WeblcmsDataManager :: get_instance();
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

    function create()
    {
        $dm = WeblcmsDataManager :: get_instance();
        return $dm->create_content_object_publication_user($this);
    }

    static function get_table_name()
    {
        return Utilities :: camelcase_to_underscores(self :: CLASS_NAME);
    }
}

?>