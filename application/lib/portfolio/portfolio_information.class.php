<?php
/**
 * @package application.lib.portfolio
 */
require_once dirname(__FILE__) . '/portfolio_rights.class.php';
/**
 * This class describes information on the status of a user's portfolio publications
 *
 * @author Nathalie Blocry
 */
class PortfolioInformation extends DataClass
{
    const CLASS_NAME = __CLASS__;

    const PROPERTY_USER_ID = "user_id";
    const PROPERTY_LAST_UPDATED_DATE ="last_updated_date" ;
    const PROPERTY_LAST_UPDATED_ITEM ="last_updated_item_id";
    const PROPERTY_LAS_UPDATED_ITEM_TYPE ="last_updated_item_type" ;
    const PROPERTY_LAST_ACTION ="last_action" ;


    //possible actions to be logged on portfolio
    const ACTION_PORTFOLIO_ADDED = 1;
    const ACTION_ITEM_ADDED = 2;
    const ACTION_EDITED = 4;
    const ACTION_FIRST_PORTFOLIO_CREATED = 5;





    /**
     * Get the default properties
     * @return array The property names.
     */
    static function get_default_property_names()
    {
        return parent :: get_default_property_names(array(self :: PROPERTY_CONTENT_OBJECT, self :: PROPERTY_FROM_DATE, self :: PROPERTY_TO_DATE, self :: PROPERTY_HIDDEN, self :: PROPERTY_PUBLISHER, self :: PROPERTY_PUBLISHED));
    }

    /**
     * inherited
     */
    function get_data_manager()
    {
        return PortfolioDataManager :: get_instance();
    }

    
    function create()
    {
        $dm = PortfolioDataManager :: get_instance();
        $info = $dm->create_portfolio_information($this);
        return $info;
    }

     function update()
    {
        $dm = PortfolioDataManager :: get_instance();
        $success = $dm->update_portfolio_information($this);
        return $success;
    }



    static function get_table_name()
    {
        return Utilities :: camelcase_to_underscores(self :: CLASS_NAME);
    }


    
    function get_user_id()
    {
        return $this->get_default_property(self :: PROPERTY_USER_ID);
    }
    function set_user_id($user_id)
    {
        $this->set_default_property(self :: PROPERTY_USER_ID, $user_id);
    }

    function get_last_updated_date()
    {
        return $this->get_default_property(self :: PROPERTY_LAST_UPDATED_DATE);
    }
    function set_last_updated_date($last_updated_date)
    {
        $this->set_default_property(self :: PROPERTY_LAST_UPDATED_DATE, $last_updated_date);
    }

     function get_last_updated_item_id()
    {
        return $this->get_default_property(self :: PROPERTY_LAST_UPDATED_ITEM);
    }
    function set_last_updated_item_id($last_updated_item_id)
    {
        $this->set_default_property(self :: PROPERTY_LAST_UPDATED_ITEM, $last_updated_item_id);
    }

    function get_last_updated_item_type()
    {
        return $this->get_default_property(self :: PROPERTY_LAS_UPDATED_ITEM_TYPE);
    }
    function set_last_updated_item_type($last_updated_item_type)
    {
        $this->set_default_property(self :: PROPERTY_LAS_UPDATED_ITEM_TYPE, $last_updated_item_type);
    }

    function get_last_action()
    {
        return $this->get_default_property(self :: PROPERTY_LAST_ACTION);
    }
    function set_last_action($last_action)
    {
        $this->set_default_property(self :: PROPERTY_LAST_ACTION, $last_action);
    }


    function get_portfolio_info_text()
    {
        //TODO implement text
        $text = Translation :: get('Introduction');
        $text .= '</br>';
        $text .= Translation :: get('LastChangedDate');
        $text .= $this->get_last_updated_date();
        $text .= '</br>';
        $text .= Translation :: get('LastChangedAction');
        $text .= $this->get_last_action();
        $text .= $this->get_last_updated_item_type();
        $text .= '</br>';
        $text .= Translation :: get('LastChangedItem');
        $text .= $this->get_last_updated_item_id();
        $text .= '</br>';
    }


  
}

?>