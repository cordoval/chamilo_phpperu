<?php
namespace application\package;

use common\libraries\DataClass;

/**
 * This class describes a Package data object
 *
 * $Id: remote_package.class.php 126 2009-11-09 13:11:05Z vanpouckesven $
 * @package admin.lib
 * @author Hans De Bisschop
 */
class Author extends DataClass
{
    const CLASS_NAME = __CLASS__;
    
    const PROPERTY_NAME = 'name';
    const PROPERTY_EMAIL = 'email';
    const PROPERTY_COMPANY = 'company';

    /**
     * Get the default properties
     * @return array The property names.
     */
    static function get_default_property_names()
    {
        return parent :: get_default_property_names(array(self :: PROPERTY_COMPANY, self :: PROPERTY_EMAIL, self :: PROPERTY_NAME));
    }

    /**
     * inherited
     */
    function get_data_manager()
    {
        return PackageDataManager :: get_instance();
    }

    /**
     * Returns the code of this Package.
     * @return the code.
     */
    function get_name()
    {
        return $this->get_default_property(self :: PROPERTY_NAME);
    }

    /**
     * Sets the code of this Package.
     * @param code
     */
    function set_name($name)
    {
        $this->set_default_property(self :: PROPERTY_NAME, $name);
    }
    
    /**
     * Returns the code of this Package.
     * @return the code.
     */
    function get_email()
    {
        return $this->get_default_property(self :: PROPERTY_EMAIL);
    }

    /**
     * Sets the code of this Package.
     * @param code
     */
    function set_email($email)
    {
        $this->set_default_property(self :: PROPERTY_EMAIL, $email);
    }
    
    /**
     * Returns the code of this Package.
     * @return the code.
     */
    function get_company()
    {
        return $this->get_default_property(self :: PROPERTY_COMPANY);
    }

    /**
     * Sets the code of this Package.
     * @param code
     */
    function set_company($company)
    {
        $this->set_default_property(self :: PROPERTY_COMPANY, $company);
    }
}

?>