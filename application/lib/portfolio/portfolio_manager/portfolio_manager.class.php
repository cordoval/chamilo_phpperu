<?php
/**
 * $Id: portfolio_manager.class.php 206 2009-11-13 13:08:01Z chellee $
 * @package application.portfolio.portfolio_manager.component
 */
require_once dirname(__FILE__) . '/../portfolio_data_manager.class.php';
require_once dirname(__FILE__) . '/../portfolio_publication.class.php';

/**
 * A portfolio manager
 * @author Sven Vanpoucke
 */
class PortfolioManager extends WebApplication
{

    const APPLICATION_NAME = 'portfolio';
    
    const PARAM_PORTFOLIO_PUBLICATION = 'portfolio_publication';
    const PARAM_PORTFOLIO_ITEM = 'portfolio_item';
    const PARAM_PORTFOLIO_OWNER_ID = 'poid';
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
                $component = $this->create_component('Viewer');
                break;
            case self :: ACTION_DELETE_PORTFOLIO_PUBLICATION :
                $component = $this->create_component('PortfolioPublicationDeleter');
                break;
            case self :: ACTION_DELETE_PORTFOLIO_ITEM :
                $component = $this->create_component('PortfolioItemDeleter');
                break;
            case self :: ACTION_CREATE_PORTFOLIO_PUBLICATION :
                $component = $this->create_component('PortfolioPublicationCreator');
                break;
            case self :: ACTION_CREATE_PORTFOLIO_ITEM :
                $component = $this->create_component('PortfolioItemCreator');
                break;
            case self :: ACTION_BROWSE :
                $component = $this->create_component('Browser');
                break;
            default :
                if (PlatformSetting :: get('first_page', 'portfolio') == 0)
                {
                    $this->set_action(self :: ACTION_BROWSE);
                    $component = $this->create_component('Browser');
                }
                else
                {
                    $this->set_action(self :: ACTION_VIEW_PORTFOLIO);
                    $component = $this->create_component('Viewer');
                    $_GET[self::PARAM_PORTFOLIO_OWNER_ID] = $this->get_user_id();
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

    static function retrieve_portfolio_publication($id)
    {
        return PortfolioDataManager :: get_instance()->retrieve_portfolio_publication($id);
    }
    static function retrieve_portfolio_item($id)
    {
        $rdm = RepositoryDataManager :: get_instance();
        $complex_object = $rdm->retrieve_complex_content_object_item($id);
        if($complex_object)
        {
            $portfolio_item = $rdm->retrieve_content_object($complex_object->get_ref());
            if ($portfolio_item->get_type() == PortfolioItem :: get_type_name())
            {
                     $content = $rdm->retrieve_content_object($portfolio_item->get_reference());
            }
        }
        if(!isset($content))
        {
            $content = false;
        }
        return $content;
    }

//    function count_portfolio_publication_groups($condition)
//    {
//        return PortfolioDataManager :: get_instance()->count_portfolio_publication_groups($condition);
//    }
//
//    function retrieve_portfolio_publication_groups($condition = null, $offset = null, $count = null, $order_property = null)
//    {
//        return PortfolioDataManager :: get_instance()->retrieve_portfolio_publication_groups($condition, $offset, $count, $order_property);
//    }

//    function retrieve_portfolio_publication_group($id)
//    {
//        return PortfolioDataManager :: get_instance()->retrieve_portfolio_publication_group($id);
//    }
//
//    function count_portfolio_publication_users($condition)
//    {
//        return PortfolioDataManager :: get_instance()->count_portfolio_publication_users($condition);
//    }

//    function retrieve_portfolio_publication_users($condition = null, $offset = null, $count = null, $order_property = null)
//    {
//        return PortfolioDataManager :: get_instance()->retrieve_portfolio_publication_users($condition, $offset, $count, $order_property);
//    }
//
    static function retrieve_portfolio_publication_user($pid)
    {
        return PortfolioDataManager :: get_instance()->retrieve_portfolio_publication_user($pid);
    }

    static function retrieve_portfolio_item_user($cid)
    {
        return PortfolioDataManager :: get_instance()->retrieve_portfolio_item_user($cid);
    }


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
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_VIEW_PORTFOLIO, self :: PARAM_PORTFOLIO_OWNER_ID => $user));
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
        $allowed_types = array(Portfolio :: get_type_name());
        
        $type = $content_object->get_type();
        if (in_array($type, $allowed_types))
        {
            $locations = array(__CLASS__);
            return $locations;
        }
        
        return array();
    }

    function publish_content_object($content_object, $location, $owner_id = null)
    {
        $publication = new PortfolioPublication();
        $publication->set_content_object($content_object->get_id());
        $publication->set_publisher(Session :: get_user_id());
        //TODO change if we want to allow other users to publish in someone's portfolio
        if($owner_id != null)
        {
            $publication->set_owner($owner_id);
        }
        else
        {
            $publication->set_owner(Session :: get_user_id());
        }
        $publication->set_published(time());
        $publication->create();
        return Translation :: get('PublicationCreated');
    }
}
?>