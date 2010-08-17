<?php
/**
 * @package application.lib.portfolio
 */
require_once dirname(__FILE__) . '/rights/portfolio_rights.class.php';
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
    const PROPERTY_INTRODUCTION_TEXT = "introduction";



    //possible actions to be logged on portfolio
    const ACTION_PORTFOLIO_ADDED = 1;
    const ACTION_ITEM_ADDED = 2;
    const ACTION_EDITED = 4;
    const ACTION_FIRST_PORTFOLIO_CREATED = 5;
    const ACTION_DELETED = 6;





    /**
     * Get the default properties
     * @return array The property names.
     */
    static function get_default_property_names()
    {
        return parent :: get_default_property_names(array(self::PROPERTY_USER_ID, self::PROPERTY_LAST_UPDATED_DATE, self::PROPERTY_LAST_UPDATED_ITEM, self::PROPERTY_LAS_UPDATED_ITEM_TYPE, self::PROPERTY_LAST_ACTION, self::PROPERTY_INTRODUCTION_TEXT));
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

    function get_introduction()
    {
        return $this->get_default_property(self :: PROPERTY_INTRODUCTION_TEXT);
    }


    function set_introduction($intro_text)
    {
        $this->set_default_property(self :: PROPERTY_INTRODUCTION_TEXT, $intro_text);
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
        $text = '<div class="portfolio_introduction">';

        if($this->get_introduction() == 'intro')
        {
            $text .= Translation :: get('PortfolioIntroductionStandardText');
            $text .= '<br />';
            $text .= '<br />';
            $text .= '<br />';
        }
        else
        {
            $text .= $this->get_introduction();
            $text .= '<br />';
            $text .= '<br />';
            $text .= '<br />';
        }
        
        
        $text .= '</div><div>';
        $text .= Translation :: get('LastChangedDate');
        $text .= ' : ';
        //TODO: formating according to locale & timezone!
        $text .= date('j/m/Y (H:i)', $this->get_last_updated_date());
        $text .= '</div><div>';
        $text .= Translation :: get('LastChangedAction');
        $text .= ' : ';
        $action = $this->get_last_action();
        if($action == self::ACTION_EDITED)
        {
            $text .= Translation :: get('PortfolioEdited');
        }
        else if($action == self::ACTION_FIRST_PORTFOLIO_CREATED)
        {
            $text .= Translation :: get('FirstPortfolioCreated');
        }
        else if($action == self::ACTION_ITEM_ADDED)
        {
            $text .= Translation :: get('PortfolioItemAdded');
        }
        else if($action == self::ACTION_PORTFOLIO_ADDED)
        {
            $text .= Translation :: get('PortfolioAdded');
        }
        else if($action == self::ACTION_DELETED)
        {
            $text .= Translation :: get('PortfolioDeleted');
        }
        else
        {
            $text .= Translation :: get('PortfolioChanged');
        }
        $text .= '</div><div>';
        $text .= Translation :: get('LastChangedItem');
        $text .= ' : ';
        $type = $this->get_last_updated_item_type();
        $id = $this->get_last_updated_item_id();
        if($type == PortfolioRights::TYPE_PORTFOLIO_FOLDER)
        {
//            $pub = PortfolioManager::retrieve_portfolio_publication($id);
            if($id)
            {
                $pub = ContentObject::get_by_id($id);
            }
            if($pub)
            {
                $text .= $pub->get_title();
            }
        }
        else if($type == PortfolioRights::TYPE_PORTFOLIO_ITEM || $type == PortfolioRights::TYPE_PORTFOLIO_SUB_FOLDER )
        {
            $rdm = RepositoryDataManager :: get_instance();
//            $wrapper = $rdm->retrieve_content_object($id);
//            $item = $rdm->retrieve_content_object($wrapper->get_reference());
            $item = $rdm->retrieve_content_object($id);
            if($item)
            {
                $text .= $item->get_title();
            }
        }
        else
        {
            $text.=$type;
            $text.='  /  ' ;
            $text.=$id;

        }

        $text .= '</div>';

        return $text;
    }


  
}

?>