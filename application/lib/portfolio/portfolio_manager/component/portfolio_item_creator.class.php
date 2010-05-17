<?php
/**
 * $Id: portfolio_item_creator.class.php 206 2009-11-13 13:08:01Z chellee $
 * @package application.portfolio.portfolio_manager.component
 */
require_once dirname(__FILE__) . '/../portfolio_manager.class.php';
require_once dirname(__FILE__) . '/../../rights/portfolio_rights.class.php';
/**
 * Component to create a new portfolio_publication object
 * @author Sven Vanpoucke
 */
class PortfolioManagerPortfolioItemCreatorComponent extends PortfolioManager
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $parent = Request :: get('parent');
        //TODO: HIER WORDT BEPAALD WELKE REPOSITORY TYPES KUNNEN GEBRUIKT WORDEN IN PORTFOLIO. ZOU DAT GEEN ADMIN SETTING MOETEN ZIJN?
        $types = array(Portfolio :: get_type_name(), Announcement :: get_type_name(), BlogItem :: get_type_name(), CalendarEvent :: get_type_name(), 
        			   Description :: get_type_name(), Document :: get_type_name(), Link :: get_type_name(), Note :: get_type_name(), RssFeed :: get_type_name(), Profile :: get_type_name(), Youtube :: get_type_name());
        
        $pub = new RepoViewer($this, $types, RepoViewer :: SELECT_MULTIPLE, array(), false);
        $pub->set_parameter('parent', $parent);
        $pp = Request :: get(PortfolioManager::PARAM_PARENT_PORTFOLIO);
        $pub->set_parameter(PortfolioManager::PARAM_PARENT_PORTFOLIO, $pp);
        $pub->parse_input_from_table();
        
        if (!$pub->is_ready_to_be_published())
        {

            //$this->display_header($trail);
            $html[] =  $pub->as_html();

            $trail = new BreadcrumbTrail();
            $trail->add(new Breadcrumb($this->get_url(array(PortfolioManager :: PARAM_ACTION => PortfolioManager :: ACTION_BROWSE)), Translation :: get('BrowsePortfolio')));
            $trail->add(new Breadcrumb($this->get_url(array(PortfolioManager :: PARAM_ACTION => PortfolioManager :: ACTION_VIEW_PORTFOLIO, PortfolioManager :: PARAM_PORTFOLIO_OWNER_ID => $this->get_user_id())), Translation :: get('ViewPortfolio')));
            $trail->add(new Breadcrumb($this->get_url(), Translation :: get('CreatePortfolioItem')));
           
            $this->display_header($trail);
            echo implode("\n", $html);
            $this->display_footer();
        }
        else
        {
            $objects = $pub->get_selected_objects();
        	if (! is_array($objects))
        	{
                $objects = array($objects);
        	}
            
            $rdm = RepositoryDataManager :: get_instance();
            $success = true;
            
            foreach ($objects as $object)
            {
                $new_object = ContentObject :: factory(PortfolioItem :: get_type_name());
                $new_object->set_owner_id($this->get_user_id());
                $new_object->set_title(PortfolioItem :: get_type_name());
                $new_object->set_description(PortfolioItem :: get_type_name());
                $new_object->set_parent_id(0); 
                $new_object->set_reference($object);
                $new_object->create();
                $objectID = $new_object->get_id();
                $wrapper = new ComplexContentObjectItem();
                $wrapper->set_ref($objectID);
                $wrapper->set_parent($parent);
                $wrapper->set_user_id($this->get_user_id());
                $wrapper->set_display_order($rdm->select_next_display_order($parent));
                $success &= $wrapper->create();

                
                if($success)
                {
                    $typeObject = $rdm->determine_content_object_type($object);
                    //TODO if we want other users to be able to create items in the portfolio's this should be changed
                    //not the current user but the user that owns the portfolio should be used here!
                    $user = $this->get_user_id();
                    $possible_types = array();
                    $possible_types[] = PortfolioRights::TYPE_PORTFOLIO_FOLDER;
                    $possible_types[] = PortfolioRights::TYPE_PORTFOLIO_SUB_FOLDER;
                    $parent_location = PortfolioRights::get_location_id_by_identifier_from_portfolio_subtree($possible_types, $pp, $user);
                    
                   if($typeObject == Portfolio :: get_type_name())
                   {
                       $type = PortfolioRights::TYPE_PORTFOLIO_SUB_FOLDER;
                   }
                   else
                   {
                       $type = PortfolioRights::TYPE_PORTFOLIO_ITEM;
                   }
                   $success &= PortfolioRights::create_location_in_portfolio_tree(PortfolioRights::TYPE_PORTFOLIO_ITEM, $type, $wrapper->get_id(), $parent_location, $user, true, false, true);

                   if($success)
                    {
                        $dm = PortfolioDataManager :: get_instance();
                        $info = $dm->retrieve_portfolio_information_by_user($user);
                        if($info)
                        {
                            $info->set_last_updated_date(time());
                            $info->set_last_updated_item_id($objectID);
                            $info->set_last_updated_item_type($type);
                            $info->set_last_action(PortfolioInformation::ACTION_ITEM_ADDED);
                            $success &= $info->update();
                        }
                        else
                        {
                            $info = new PortfolioInformation();
                            $info->set_user_id($user);
                            $info->set_last_updated_date(time());
                            $info->set_last_updated_item_id($objectID);
                            $info->set_last_updated_item_type($type);
                            $info->set_last_action(PortfolioInformation::ACTION_ITEM_ADDED);
                            $success &= $info->create();
                        }
                    }
                }
            }
            
            $this->redirect($success ? Translation :: get('PortfolioItemCreated') : Translation :: get('PortfolioItemNotCreated'), ! $success, array(PortfolioManager :: PARAM_ACTION => PortfolioManager :: ACTION_VIEW_PORTFOLIO, PortfolioManager :: PARAM_PORTFOLIO_OWNER_ID => $this->get_user_id()));
        }
    }
}
?>