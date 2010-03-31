<?php
/**
 * $Id: portfolio_manager.class.php 206 2009-11-13 13:08:01Z chellee $
 * @package application.portfolio.portfolio_manager.component
 */
require_once dirname(__FILE__) . '/portfolio_manager_component.class.php';
require_once dirname(__FILE__) . '/../portfolio_data_manager.class.php';

/**
 * A portfolio manager
 * @author Sven Vanpoucke
 */
class PortfolioManager extends WebApplication
{
    const APPLICATION_NAME = 'portfolio';
    
    const PARAM_PORTFOLIO_PUBLICATION = 'portfolio_publication';
    const PARAM_PORTFOLIO_ITEM = 'portfolio_item';
    const PARAM_USER_ID = 'user_id';
    const PARAM_PARENT = 'parent';
    const PARAM_PARENT_PORTFOLIO = 'parent_portfolio';
    
    const ACTION_DELETE_PORTFOLIO_PUBLICATION = 'delete_portfolio_publication';
    const ACTION_DELETE_PORTFOLIO_ITEM = 'delete_portfolio_item';
    const ACTION_CREATE_PORTFOLIO_PUBLICATION = 'create_portfolio_publication';
    const ACTION_CREATE_PORTFOLIO_ITEM = 'create_portfolio_item';
    const ACTION_VIEW_PORTFOLIO = 'view_portfolio';
    const ACTION_BROWSE = 'browse';

    /**
     * Constructor
     * @param User $user The current user
     */
    function PortfolioManager($user = null)
    {
        parent :: __construct($user);
    }

    /**
     * Run this portfolio manager
     */
    function run()
    {
        $action = $this->get_action();
        $component = null;
        switch ($action)
        {
            case self :: ACTION_VIEW_PORTFOLIO :
                $component = PortfolioManagerComponent :: factory('Viewer', $this);
                break;
            case self :: ACTION_DELETE_PORTFOLIO_PUBLICATION :
                $component = PortfolioManagerComponent :: factory('PortfolioPublicationDeleter', $this);
                break;
            case self :: ACTION_DELETE_PORTFOLIO_ITEM :
                $component = PortfolioManagerComponent :: factory('PortfolioItemDeleter', $this);
                break;
            case self :: ACTION_CREATE_PORTFOLIO_PUBLICATION :
                $component = PortfolioManagerComponent :: factory('PortfolioPublicationCreator', $this);
                break;
            case self :: ACTION_CREATE_PORTFOLIO_ITEM :
                $component = PortfolioManagerComponent :: factory('PortfolioItemCreator', $this);
                break;
            case self :: ACTION_BROWSE :
                $component = PortfolioManagerComponent :: factory('Browser', $this);
                break;
            default :
                if (PlatformSetting :: get('first_page', 'portfolio') == 0)
                {
                    $this->set_action(self :: ACTION_BROWSE);
                    $component = PortfolioManagerComponent :: factory('Browser', $this);
                }
                else
                {
                    $this->set_action(self :: ACTION_VIEW_PORTFOLIO);
                    $component = PortfolioManagerComponent :: factory('Viewer', $this);
                    $_GET['user_id'] = $this->get_user_id();
                }
        
        }
        $component->run();
    }

    function get_application_name()
    {
        return self :: APPLICATION_NAME;
    }

    // Data Retrieving
    

    function count_portfolio_publications($condition)
    {
        return PortfolioDataManager :: get_instance()->count_portfolio_publications($condition);
    }

    function retrieve_portfolio_publications($condition = null, $offset = null, $count = null, $order_property = null)
    {
        return PortfolioDataManager :: get_instance()->retrieve_portfolio_publications($condition, $offset, $count, $order_property);
    }

    function retrieve_portfolio_publication($id)
    {
        return PortfolioDataManager :: get_instance()->retrieve_portfolio_publication($id);
    }

    function count_portfolio_publication_groups($condition)
    {
        return PortfolioDataManager :: get_instance()->count_portfolio_publication_groups($condition);
    }

    function retrieve_portfolio_publication_groups($condition = null, $offset = null, $count = null, $order_property = null)
    {
        return PortfolioDataManager :: get_instance()->retrieve_portfolio_publication_groups($condition, $offset, $count, $order_property);
    }

    function retrieve_portfolio_publication_group($id)
    {
        return PortfolioDataManager :: get_instance()->retrieve_portfolio_publication_group($id);
    }

    function count_portfolio_publication_users($condition)
    {
        return PortfolioDataManager :: get_instance()->count_portfolio_publication_users($condition);
    }

    function retrieve_portfolio_publication_users($condition = null, $offset = null, $count = null, $order_property = null)
    {
        return PortfolioDataManager :: get_instance()->retrieve_portfolio_publication_users($condition, $offset, $count, $order_property);
    }

    function retrieve_portfolio_publication_user($id)
    {
        return PortfolioDataManager :: get_instance()->retrieve_portfolio_publication_user($id);
    }

    // Url Creation
    

    function get_create_portfolio_publication_url()
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_CREATE_PORTFOLIO_PUBLICATION));
    }

    function get_delete_portfolio_publication_url($portfolio_publication)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_DELETE_PORTFOLIO_PUBLICATION, self :: PARAM_PORTFOLIO_PUBLICATION => $portfolio_publication));
    }

    function get_create_portfolio_item_url($parent_id, $pid)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_CREATE_PORTFOLIO_ITEM, self :: PARAM_PARENT => $parent_id, self :: PARAM_PARENT_PORTFOLIO => $pid));
    }

    function get_delete_portfolio_item_url($portfolio_item_cid)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_DELETE_PORTFOLIO_ITEM, self :: PARAM_PORTFOLIO_ITEM => $portfolio_item_cid));
    }

    function get_view_portfolio_url($user)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_VIEW_PORTFOLIO, self :: PARAM_USER_ID => $user));
    }

    function get_browse_url()
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_BROWSE));
    }

    function content_object_is_published($object_id)
    {
        return PortfolioDataManager :: get_instance()->content_object_is_published($object_id);
    }

    function any_content_object_is_published($object_ids)
    {
        return PortfolioDataManager :: get_instance()->any_content_object_is_published($object_ids);
    }

    function get_content_object_publication_attributes($object_id, $type = null, $offset = null, $count = null, $order_property = null)
    {
        return PortfolioDataManager :: get_instance()->get_content_object_publication_attributes($object_id, $type, $offset, $count, $order_property);
    }

    function get_content_object_publication_attribute($publication_id)
    {
        return PortfolioDataManager :: get_instance()->get_content_object_publication_attribute($publication_id);
    }

	function count_publication_attributes($user = null, $object_id = null, $condition = null)
    {
        return PortfolioDataManager :: get_instance()->count_publication_attributes($user, $object_id, $condition);
    }

    function delete_content_object_publications($object_id)
    {
        return PortfolioDataManager :: get_instance()->delete_content_object_publications($object_id);
    }
    
	function delete_content_object_publication($publication_id)
    {
    	 return PortfolioDataManager :: get_instance()->delete_content_object_publication($publication_id);
    }

    function update_content_object_publication_id($publication_attr)
    {
        return PortfolioDataManager :: get_instance()->update_content_object_publication_id($publication_attr);
    }

    function get_content_object_publication_locations($content_object)
    {
        $allowed_types = array('portfolio');
        
        $type = $content_object->get_type();
        if (in_array($type, $allowed_types))
        {
            $locations = array(__CLASS__);
            return $locations;
        }
        
        return array();
    }

    function publish_content_object($content_object, $location)
    {
        $publication = new PortfolioPublication();
        $publication->set_content_object($content_object->get_id());
        $publication->set_publisher(Session :: get_user_id());
        $publication->set_published(time());
        $publication->set_hidden(0);
        $publication->set_from_date(0);
        $publication->set_to_date(0);
        
        $publication->create();
        return Translation :: get('PublicationCreated');
    }
}
?>