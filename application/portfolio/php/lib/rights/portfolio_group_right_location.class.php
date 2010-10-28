<?php


namespace application\portfolio;

use common\libraries\DataClass;
use common\libraries\Utilities;
require_once dirname(__FILE__). '/../portfolio_data_manager.class.php';

class PortfolioGroupRightLocation extends DataClass
{
    const CLASS_NAME = __CLASS__;
    const PROPERTY_RIGHT_ID = 'right_id';
    const PROPERTY_LOCATION_ID = 'location_id';
    const PROPERTY_GROUP_ID = 'group_id';
   

    /**
     * Get the default properties of all users.
     * @return array The property names.
     */
    static function get_default_property_names()
    {
        return parent :: get_default_property_names(array(self :: PROPERTY_RIGHT_ID, self :: PROPERTY_GROUP_ID, self :: PROPERTY_LOCATION_ID));
    }

    /**
     * inherited
     */
    function get_data_manager()
    {
        return PortfolioDataManager::get_instance();
    }

    function get_right_id()
    {
        return $this->get_default_property(self :: PROPERTY_RIGHT_ID);
    }

    function set_right_id($right_id)
    {
        $this->set_default_property(self :: PROPERTY_RIGHT_ID, $right_id);
    }

    function get_group_id()
    {
        return $this->get_default_property(self :: PROPERTY_GROUP_ID);
    }

    function set_group_id($group_id)
    {
        $this->set_default_property(self :: PROPERTY_GROUP_ID, $group_id);
    }

    function get_location_id()
    {
        return $this->get_default_property(self :: PROPERTY_LOCATION_ID);
    }

    function set_location_id($location_id)
    {
        $this->set_default_property(self :: PROPERTY_LOCATION_ID, $location_id);
    }
    
    static function get_table_name()
    {
        return Utilities :: camelcase_to_underscores(Utilities::get_classname_from_namespace(self :: CLASS_NAME));
    }
}
?>