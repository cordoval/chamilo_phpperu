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

    static function retrieve_portfolio_publications($condition = null, $offset = null, $count = null, $order_property = null)
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
    static function retrieve_portfolio_publication_publisher($pid)
    {
        return PortfolioDataManager :: get_instance()->retrieve_portfolio_publication_publisher($pid);
    }
    static function retrieve_portfolio_publication_owner($pid)
    {
        return PortfolioDataManager :: get_instance()->retrieve_portfolio_publication_owner($pid);
    }

    static function retrieve_portfolio_item_publisher($cid)
    {
        return PortfolioDataManager :: get_instance()->retrieve_portfolio_item_publisher($cid);
    }
    static function retrieve_portfolio_item_owner($cid)
    {
        return PortfolioDataManager :: get_instance()->retrieve_portfolio_item_owner($cid);
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
    {//HIER KOMT DE REPOSITORY PUBLISHER TERECHT DUS HIER MOET OOK DE LOCATIONS GEZET WORDEN
        $success = true;
        $publication = new PortfolioPublication();
        $publication->set_content_object($content_object->get_id());
        $publication->set_publisher(Session :: get_user_id());
        
        if($owner_id != null)
        {
            $publication->set_owner($owner_id);
        }
        else
        {
            $publication->set_owner(Session :: get_user_id());
        }
        $publication->set_published(time());
        $success &= $publication->create();
        $pub_location = $publication->get_location();
        if($pub_location)
        {
            $parent_location_id = $pub_location->get_id();
            
            $children_set = PortfolioManager::get_portfolio_children($publication->get_content_object(), false, false);
            if($children_set != false)
            {
                $pdm = PortfolioDataManager::get_instance();
                $success &= $pdm->create_locations_for_children($children_set, $parent_location_id, $publication->get_owner());
            }
        }
        else
        {
            $success &= false;
        }
        $success &= $publication->update_portfolio_info();

        if($success)
        {
            return Translation :: get('PublicationCreated');
        }
        else
        {
            return Translation :: get('ProblemWithPublicationCreation');
        }
    }


    /**
     *
     * @param <integer> $content_object_id
     * @param <bool> $pid: is the content object id a portfolio publication id
     * @param <bool> $cid: is the content object id  a complex content object item id
     * @return <result-set>  returen the set of children if there are any, false if there aren't any
     */
    static function get_portfolio_children($content_object_id, $pid = true, $cid = false)
    {

        if($pid)
        {
            $object_id = self::get_co_id_from_portfolio_publication_wrapper($content_object_id);
        }
        if($cid)
        {
            $object_id = self::get_co_id_from_complex_wrapper($content_object_id);
        }
        else
        {
            $object_id = $content_object_id;
        }

        if($object_id)
        {
            $pdm = PortfolioDataManager::get_instance();
            $children_set = $pdm->get_portfolio_children($object_id);
            return $children_set;
        }
        else
        {
            return false;
        }
  
    }




     


   

    /**
     * get the portfolio content object a portfolio publication holds a reference to
     * @param <integer> $pid id of the portfolio publication
     * @param <object> $portfolio_publication : the actual object
     * @return <integer> the id of the portfolio content object
     */
    static function get_co_id_from_portfolio_publication_wrapper($pid, $portfolio_publication = null)
    {
        $pdm = PortfolioDataManager::get_instance();
        if($portfolio_publication == null)
        {
            $portfolio_publication = $pdm->retrieve_portfolio_publication($pid);
        }

        return $portfolio_publication->get_content_object();
    }


    /**
     *
     * @param <item> $complex_item_object Id
     * @param <object> $complex_item_object: the actual wrapper object
     * @return <type>
     */
     static function get_co_id_from_complex_wrapper($cid, $complex_item_object = null)
    {
        $rdm = RepositoryDataManager::get_instance();
        if($complex_item_object == null)
        {
            $rdm = RepositoryDataManager::get_instance();
            $complex_item_object = $rdm->retrieve_complex_content_object_item($cid);
        }

        $portfolio_item = $rdm->retrieve_content_object($complex_item_object->get_ref());
        if($portfolio_item)
        {
            $content_object_id = $portfolio_item->get_reference();
        }
        else
        {
            $content_object_id = false;
        }
        return $content_object_id;
    }

     /**
     *gets the portfolio-info-object of the portfolio's owner (wich contains update-information etc.)
     * @return portfolioInfo object
     */
    static function get_portfolio_info($user_id)
    {
        $dm = PortfolioDataManager :: get_instance();
        $info = $dm->retrieve_portfolio_information_by_user($user_id);
        if(!$info)
        {
            $info = new PortfolioInformation();
            $info->set_last_updated_date(time());
            $info->set_user_id($user_id);
            $info->set_last_updated_item_id('0');
            $info->set_last_updated_item_type(PortfolioRights::TYPE_PORTFOLIO_FOLDER);
            $info->set_last_action(PortfolioInformation::ACTION_FIRST_PORTFOLIO_CREATED);
            $dm->create_portfolio_information($info);
        }
        return $info;
    }


    static function update_portfolio_info($content_object_id, $type, $action, $user_id)
    {
        $success = true;
        $info = self::get_portfolio_info($user_id);
       
        $info->set_last_updated_date(time());
        $info->set_last_updated_item_id($content_object_id);
        $info->set_last_updated_item_type($type);
        $info->set_last_action($action);
        $success &= $info->update();
        

        return $success;
    }

     
    /**
     *create locations for the sub-items of a published portfolio
     * @param <reulst_set> $children_set: set of children of this portfolio
     * @param <integer> $parent_location_id: location id of the parent portfolio
     * @param <integer> $owner: owner of the portfolio_tree
     * @return <bool> $success
     */
    static function create_locations_for_children($children_set, $parent_location_id, $owner)
    {
        $success = true;
        while($child = $children_set->next_result())
        {
            $object_id = $child->get_id();
            $grand_children = self::get_portfolio_children($object_id, false, true);
            $child_location= PortfolioRights::create_location_in_portfolio_tree(PortfolioRights::TYPE_PORTFOLIO_ITEM, PortfolioRights::TYPE_PORTFOLIO_ITEM, $object_id, $parent_location_id, $owner, true, false);
            if($child_location && $grand-children)
            {
                $success &= self::create_locations_for_children($grand_children, $child_location->get_id(), $owner);
            }
            if($child_location == false)
            {
                $success = false;
            }
        }
        return $success;
    }

  

}
?>