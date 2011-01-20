<?php
namespace application\package;

use common\libraries\DataClass;
use common\libraries\Utilities;
use common\libraries\EqualityCondition;
use common\libraries\InCondition;

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

    const PACKAGES = 'packages';
    
    /**
     * Get the default properties
     * @return array The property names.
     */
    static function get_default_property_names($extended_property_names = array())
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

     function get_packages($only_ids = true)
    {
        $condition = new EqualityCondition(PackageAuthor :: PROPERTY_AUTHOR_ID, $this->get_id());
        $package_authors = $this->get_data_manager()->retrieve_package_authors($condition);
        $package_authors_ids = array();
        while ($package_author = $package_authors->next_result())
        {
            $package_authors_ids[] = $package_author->get_package_id();  
        }

        if ($only_ids)
        {
            return $package_authors_ids;
        }
        else
        {
            $package_authors_ids[] = -1;
            $condition = new InCondition(Package :: PROPERTY_ID, $package_authors_ids);
            return $this->get_data_manager()->retrieve_packages($condition);
        }
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
    
    static function get_table_name()
    {
        return Utilities :: get_classname_from_namespace(self :: CLASS_NAME, true);
    }
}

?>