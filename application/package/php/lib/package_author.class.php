<?php
namespace application\package;

use common\libraries\Utilities;
use common\libraries\DataClass;

/**
 * $Id: package_author.class.php 224 2009-11-13 14:40:30Z kariboe $
 * @package group.lib
 */
/**
 * @author Hans de Bisschop
 * @author Dieter De Neef
 */

class PackageAuthor extends DataClass
{
    const CLASS_NAME = __CLASS__;

    const PROPERTY_PACKAGE_ID = 'package_id';
    const PROPERTY_AUTHOR_ID = 'author_id';

    function get_package_id()
    {
        return $this->get_default_property(self :: PROPERTY_PACKAGE_ID);
    }

    function set_package_id($package_id)
    {
        $this->set_default_property(self :: PROPERTY_PACKAGE_ID, $package_id);
    }

    function get_author_id()
    {
        return $this->get_default_property(self :: PROPERTY_AUTHOR_ID);
    }

    function set_author_id($author_id)
    {
        $this->set_default_property(self :: PROPERTY_AUTHOR_ID, $author_id);
    }

    /**
     * Get the default properties of all groups.
     * @return array The property names.
     */
    static function get_default_property_names()
    {
        return array(self :: PROPERTY_PACKAGE_ID, self :: PROPERTY_AUTHOR_ID);
    }

    /**
     * inherited
     */
    function get_data_manager()
    {
        return GroupDataManager :: get_instance();
    }

    static function get_table_name()
    {
        return Utilities :: get_classname_from_namespace(self :: CLASS_NAME, true);
    }
}
?>