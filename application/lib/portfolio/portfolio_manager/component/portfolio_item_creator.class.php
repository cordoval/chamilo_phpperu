<?php
/**
 * $Id: portfolio_item_creator.class.php 206 2009-11-13 13:08:01Z chellee $
 * @package application.portfolio.portfolio_manager.component
 */
require_once dirname(__FILE__) . '/../portfolio_manager.class.php';
require_once dirname(__FILE__) . '/../portfolio_manager_component.class.php';
require_once dirname(__FILE__) . '/../../portfolio_rights.class.php';
/**
 * Component to create a new portfolio_publication object
 * @author Sven Vanpoucke
 */
class PortfolioManagerPortfolioItemCreatorComponent extends PortfolioManagerComponent
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $trail = new BreadcrumbTrail();
        $trail->add(new Breadcrumb($this->get_url(array(PortfolioManager :: PARAM_ACTION => PortfolioManager :: ACTION_BROWSE)), Translation :: get('BrowsePortfolio')));
        $trail->add(new Breadcrumb($this->get_url(array(PortfolioManager :: PARAM_ACTION => PortfolioManager :: ACTION_VIEW_PORTFOLIO, PortfolioManager :: PARAM_USER_ID => $this->get_user_id())), Translation :: get('ViewPortfolio')));
        $trail->add(new Breadcrumb($this->get_url(), Translation :: get('CreatePortfolioItem')));
        
        $object = Request :: get('object');
        $parent = Request :: get('parent');
        //HIER WORDT BEPAALD WELKE REPOSITORY TYPES KUNNEN GEBRUIKT WORDEN IN PORTFOLIO. ZOU DAT GEEN ADMIN SETTING MOETEN ZIJN?
        $types = array('portfolio', 'announcement', 'blog_item', 'calendar_event', 'description', 'document', 'link', 'note', 'rss_feed', 'profile', 'youtube');
        
        $pub = new RepoViewer($this, $types, false, RepoViewer :: SELECT_MULTIPLE, array(), false, false);
        $pub->set_parameter('parent', $parent);
        $pp = Request :: get(PortfolioManager::PARAM_PARENT_PORTFOLIO);
        $pub->set_parameter(PortfolioManager::PARAM_PARENT_PORTFOLIO, $pp);
        $pub->parse_input_from_table();
        
        if (! isset($object))
        {
            $this->display_header($trail);
            echo $pub->as_html();
            $this->display_footer();
        }
        else
        {
            if (! is_array($object))
                $objects = array($object);
            else
                $objects = $object;
            
            $rdm = RepositoryDataManager :: get_instance();
            $success = true;
            
            foreach ($objects as $object)
            {
                $new_object = ContentObject :: factory('portfolio_item');
                $new_object->set_owner_id($this->get_user_id());
                $new_object->set_title('portfolio_item');
                $new_object->set_description('portfolio_item');
                $new_object->set_parent_id(0); 
                $new_object->set_reference($object);
                $new_object->create();
                $objectID = $new_object->get_id();
                $wrapper = new ComplexContentObjectItem();
                $wrapper->set_ref($objectID);
                $wrapper->set_parent($parent);
                $wrapper->set_user_id($this->get_user_id());
                $wrapper->set_display_order($rdm->select_next_display_order());
                $success &= $wrapper->create();

                
                if($success)
                {
                    $typeObject = $rdm->determine_content_object_type($object);
                    $user = $this->get_user_id();
                   
                    $parent_location = portfolioRights::get_location_id_by_identifier_from_user_subtree(portfolioRights::PORTFOLIO_FOLDER, $pp, $user);
                    if(!$parent_location)
                    {
                        //if a location for the parent is not found, the location will be put under the root of the portfolio-tree TODO: remove this code
                        $parent_location = portfolioRights::get_portfolio_root_id($user);
                        if(!parent_location)
                        {
                            portfolioRights::create_portfolio_root($user);
                            $parent_location = portfolioRights::get_portfolio_root_id($user);

                        }
                    }
                   if($typeObject == 'portfolio')
                   {
                       $type = portfolioRights::PORTFOLIO_FOLDER;

                   }
                   else
                   {
                       $type = portfolioRights::PORTFOLIO_ITEM;
                   }
                   portfolioRights::create_location_in_portfolio_tree('portfolio item', $type, $wrapper->get_id(), $parent_location, $user, true, false);
                   //TODO: add the default rights to the location
                }


            }
            
            $this->redirect($success ? Translation :: get('PortfolioItemCreated') : Translation :: get('PortfolioItemNotCreated'), ! $success, array(PortfolioManager :: PARAM_ACTION => PortfolioManager :: ACTION_VIEW_PORTFOLIO, PortfolioManager :: PARAM_USER_ID => $this->get_user_id()));
        }
    }
}
?>