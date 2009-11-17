<?php
/**
 * $Id: right.class.php 214 2009-11-13 13:57:37Z vanpouckesven $
 * @package rights.lib
 * @author Hans de Bisschop
 */
/**
 *	This class represents a rights_template. 
 *
 *	User objects have a number of default properties:
 *	- user_id: the numeric ID of the user;
 *	- lastname: the lastname of the user;
 *	- firstname: the firstname of the user;
 *	- password: the password for this user;
 *	- auth_source:
 *	- email: the email address of this user;
 *	- status: the status of this user: 1 is teacher, 5 is a student;
 *	- phone: the phone number of the user;
 *	- official_code; the official code of this user;
 *	- picture_uri: the URI location of the picture of this user;
 *	- creator_id: the user_id of the user who created this user;
 *	- language: the language setting of this user;
 *	- disk quota: the disk quota for this user;
 *	- database_quota: the database quota for this user;
 *	- version_quota: the default quota for this user of no quota for a specific learning object type is set.
 *
 */


class Right extends DataClass
{
    const CLASS_NAME = __CLASS__;
    const PROPERTY_NAME = 'name';
    const PROPERTY_DESCRIPTION = 'description';

    /**
     * Get the default properties of all users.
     * @return array The property names.
     */
    static function get_default_property_names()
    {
        return parent :: get_default_property_names(array(self :: PROPERTY_NAME, self :: PROPERTY_DESCRIPTION));
    }

    /**
     * inherited
     */
    function get_data_manager()
    {
        return RightsDataManager :: get_instance();
    }

    function get_name()
    {
        return $this->get_default_property(self :: PROPERTY_NAME);
    }

    function get_description()
    {
        return $this->get_default_property(self :: PROPERTY_DESCRIPTION);
    }

    function set_name($name)
    {
        $this->set_default_property(self :: PROPERTY_NAME, $name);
    }

    function set_description($description)
    {
        $this->set_default_property(self :: PROPERTY_DESCRIPTION, $description);
    }

    static function get_table_name()
    {
        return Utilities :: camelcase_to_underscores(self :: CLASS_NAME);
    }
}
?>