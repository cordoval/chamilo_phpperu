<?php
/**
 * $Id: alexia_publication_user.class.php 192 2009-11-13 11:51:02Z chellee $
 * @package application.lib.alexia
 */


/**
 * This class describes a AlexiaPublicationUser data object
 *
 * @author Hans De Bisschop
 */
class AlexiaPublicationUser extends DataClass
{
    const CLASS_NAME = __CLASS__;
    const TABLE_NAME = 'publication_user';
    
    /**
     * AlexiaPublicationUser properties
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
        return AlexiaDataManager :: get_instance();
    }

    /**
     * Returns the publication of this AlexiaPublicationUser.
     * @return the publication.
     */
    function get_publication()
    {
        return $this->get_default_property(self :: PROPERTY_PUBLICATION);
    }

    /**
     * Sets the publication of this AlexiaPublicationUser.
     * @param publication
     */
    function set_publication($publication)
    {
        $this->set_default_property(self :: PROPERTY_PUBLICATION, $publication);
    }

    /**
     * Returns the user of this AlexiaPublicationUser.
     * @return the user.
     */
    function get_user()
    {
        return $this->get_default_property(self :: PROPERTY_USER);
    }

    /**
     * Sets the user of this AlexiaPublicationUser.
     * @param user
     */
    function set_user($user)
    {
        $this->set_default_property(self :: PROPERTY_USER, $user);
    }

    function create()
    {
        $dm = AlexiaDataManager :: get_instance();
        return $dm->create_alexia_publication_user($this);
    }

    static function get_table_name()
    {
        return self :: TABLE_NAME;
    }
}

?>