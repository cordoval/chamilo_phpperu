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
class PortfolioManagerPortfolioItemCreatorComponent extends PortfolioManager implements RepoViewerInterface
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $parent = Request :: get('parent');

        $repo_viewer = RepoViewer :: construct($this);
        $repo_viewer->set_parameter('parent', $parent);
        $pp = Request :: get(PortfolioManager :: PARAM_PARENT_PORTFOLIO);
        $repo_viewer->set_parameter(PortfolioManager :: PARAM_PARENT_PORTFOLIO, $pp);
        $repo_viewer->get_parent()->parse_input_from_table();

        if (! $repo_viewer->is_ready_to_be_published())
        {
            $trail = BreadcrumbTrail :: get_instance();
            $trail->add(new Breadcrumb($this->get_url(array(PortfolioManager :: PARAM_ACTION => PortfolioManager :: ACTION_BROWSE)), Translation :: get('BrowsePortfolios')));

            $udm = UserDataManager :: get_instance();
            $user = $udm->retrieve_user($this->get_user_id());
            $trail->add(new Breadcrumb($this->get_url(array(PortfolioManager :: PARAM_ACTION => PortfolioManager :: ACTION_VIEW_PORTFOLIO, PortfolioManager :: PARAM_PORTFOLIO_OWNER_ID => $this->get_user_id())), Translation :: get('ViewPortfolio') . ' ' . $user->get_fullname()));

            $trail->add(new Breadcrumb($this->get_url(), Translation :: get('CreatePortfolioItem')));
            $trail->add_help('portfolio general');

            $repo_viewer->run();
        }
        else
        {
            $objects = $repo_viewer->get_selected_objects();
            if (! is_array($objects))
            {
                $objects = array($objects);
            }

            $rdm = RepositoryDataManager :: get_instance();
            $success = true;

            foreach ($objects as $object_id)
            {

                $rdm = RepositoryDataManager :: get_instance();
                $object = $rdm->retrieve_content_object($object_id);

                if ($object->get_type() != Portfolio :: get_type_name())
                {
                    $new_object = ContentObject :: factory(PortfolioItem :: get_type_name());
                    $new_object->set_owner_id($this->get_user_id());
                    $new_object->set_title(PortfolioItem :: get_type_name());
                    $new_object->set_description(PortfolioItem :: get_type_name());
                    $new_object->set_parent_id(0);
                    $new_object->set_reference($object_id);
                    $new_object->create();
                }
                else
                {
                    $new_object = $object;
                }
                //


                $wrapper = new ComplexContentObjectItem();
                $wrapper->set_ref($new_object->get_id());
                $wrapper->set_parent($parent);
                $wrapper->set_user_id($this->get_user_id());
                $wrapper->set_display_order($rdm->select_next_display_order($parent));
                $success &= $wrapper->create();

                if ($success)
                {
                    $typeObject = $rdm->determine_content_object_type($object_id);
                    $user = $this->get_user_id();
                    $possible_types = array();
                    $possible_types[] = PortfolioRights :: TYPE_PORTFOLIO_FOLDER;
                    $possible_types[] = PortfolioRights :: TYPE_PORTFOLIO_SUB_FOLDER;
                    $parent_location = PortfolioRights :: get_location_id_by_identifier_from_portfolio_subtree($possible_types, $pp, $user);

                    if ($typeObject == Portfolio :: get_type_name())
                    {
                        $type = PortfolioRights :: TYPE_PORTFOLIO_SUB_FOLDER;
                    }
                    else
                    {
                        $type = PortfolioRights :: TYPE_PORTFOLIO_ITEM;
                    }
                    $success &= PortfolioRights :: create_location_in_portfolio_tree(PortfolioRights :: TYPE_PORTFOLIO_ITEM, $type, $wrapper->get_id(), $parent_location, $user, true, false, true);

                    if ($success)
                    {
                        $dm = PortfolioDataManager :: get_instance();
                        $success &= PortfolioManager :: update_portfolio_info($object_id, $type, PortfolioInformation :: ACTION_ITEM_ADDED, $user);

                        $info = $dm->retrieve_portfolio_information_by_user($user);

                    }
                }
            }

            $this->redirect($success ? Translation :: get('PortfolioItemCreated') : Translation :: get('PortfolioItemNotCreated'), ! $success, array(
                    PortfolioManager :: PARAM_ACTION => PortfolioManager :: ACTION_VIEW_PORTFOLIO, PortfolioManager :: PARAM_PORTFOLIO_OWNER_ID => $this->get_user_id(),
                    PortfolioManager :: PROPERTY_CID => $wrapper->get_id(), PortfolioManager :: PROPERTY_PID => $pp));
        }
    }

    function get_allowed_content_object_types()
    {
        return array(
                Document :: get_type_name(), Link :: get_type_name(), Youtube :: get_type_name(), RssFeed :: get_type_name(), Portfolio :: get_type_name(), Announcement :: get_type_name(), BlogItem :: get_type_name(),
                CalendarEvent :: get_type_name(), Description :: get_type_name(), Note :: get_type_name(), Profile :: get_type_name());
    }
}
?>