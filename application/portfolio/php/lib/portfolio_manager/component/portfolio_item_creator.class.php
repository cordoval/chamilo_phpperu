<?php
namespace application\portfolio;
use common\libraries\Request;
use common\extensions\repo_viewer\RepoViewer;
use common\libraries\BreadcrumbTrail;
use common\libraries\Breadcrumb;
use user\UserDataManager;
use repository\ContentObject;
use repository\RepositoryDataManager;
use repository\content_object\portfolio_item\PortfolioItem;
use repository\ComplexContentObjectItem;
use common\libraries\Translation;
use common\libraries\Utilities;
use repository\content_object\link\Link;
use common\libraries\EqualityCondition;
use repository\content_object\youtube\Youtube;
use repository\content_object\rss_feed\RssFeed;
use repository\content_object\portfolio\Portfolio;
use common\extensions\repo_viewer\RepoViewerInterface;
use repository\content_object\document\Document;


/**
 * Component to create a new portfolio_publication object
 * @author Sven Vanpoucke
 */
class PortfolioManagerPortfolioItemCreatorComponent extends PortfolioManager implements RepoViewerInterface
{
    private $parents;
    private $items = array();

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $parent = Request :: get(PortfolioManager::PARAM_PARENT);
        $pp = Request :: get(PortfolioManager :: PARAM_PARENT_PORTFOLIO);



        if (!RepoViewer::is_ready_to_be_published())
        {
            $exclude = $this->get_parents_and_childeren($parent);
            $exclude[] = $parent;


            $repo_viewer = RepoViewer :: construct($this);
            $repo_viewer->set_parameter(PortfolioManager::PARAM_PARENT, $parent);

            $repo_viewer->set_parameter(PortfolioManager :: PARAM_PARENT_PORTFOLIO, $pp);
            $repo_viewer->set_excluded_objects($exclude);
            $repo_viewer->get_parent()->parse_input_from_table();

            $trail = new BreadcrumbTrail();
            $trail->add(new Breadcrumb($this->get_url(array(PortfolioManager :: PARAM_ACTION => PortfolioManager :: ACTION_BROWSE)), Translation :: get('BrowsePortfolios')));

            $udm = UserDataManager :: get_instance();
            $user = $udm->retrieve_user($this->get_user_id());
            $trail->add(new Breadcrumb($this->get_url(array(PortfolioManager :: PARAM_ACTION => PortfolioManager :: ACTION_VIEW_PORTFOLIO, PortfolioManager :: PARAM_PORTFOLIO_OWNER_ID => $this->get_user_id())), Translation :: get('ViewObject', array('OBJECT' => Translation::get('Portfolio')), Utilities::COMMON_LIBRARIES) . ' ' . $user->get_fullname()));

            $trail->add(new Breadcrumb($this->get_url(), Translation :: get('CreateObject', array('OBJECT' => Translation::get('PortfolioItem')), Utilities::COMMON_LIBRARIES)));
            $trail->add_help('portfolio create');

            $repo_viewer->run();
        }
        else
        {
            $objects = RepoViewer::get_selected_objects();
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

            $this->redirect($success ? Translation :: get('ObjectCreated', array('OBJECT' => Translation::get('PortfolioItem')), Utilities::COMMON_LIBRARIES) : Translation :: get('ObjectNotCreated', array('OBJECT' => Translation::get('PortfolioItem')), Utilities::COMMON_LIBRARIES), ! $success, array(
                    PortfolioManager :: PARAM_ACTION => PortfolioManager :: ACTION_VIEW_PORTFOLIO, PortfolioManager :: PARAM_PORTFOLIO_OWNER_ID => $this->get_user_id(),
                    PortfolioManager :: PROPERTY_CID => $wrapper->get_id(), PortfolioManager :: PROPERTY_PID => $pp));
        }
    }

    function get_allowed_content_object_types()
    {
        return array(
                Document :: get_type_name(), Link :: get_type_name(), Youtube :: get_type_name(), RssFeed :: get_type_name(), Portfolio :: get_type_name());
    }

    private function retrieve_used_items($parent)
    {

        $rdm = RepositoryDataManager::get_instance();
        $complex_content_object_items = $rdm->retrieve_complex_content_object_items(new EqualityCondition(ComplexContentObjectItem :: PROPERTY_PARENT, $parent, ComplexContentObjectItem :: get_table_name()));
        while ($complex_content_object_item = $complex_content_object_items->next_result())
        {
            if ($complex_content_object_item->is_complex())
            {
                $this->items[] = $complex_content_object_item->get_ref();
                $this->retrieve_used_items($complex_content_object_item->get_ref());
            }
            else
            {
                $this->items[] = $complex_content_object_item->get_ref();
            }
        }


        return;
    }

    private function get_parents_and_childeren($parent)
        {
            $this->parents = array();
            $this->items[]= $parent;
            $this->get_parent($parent);
            foreach ($this->parents as $parent)
            {
                $this->items[]= $parent;
                $this->retrieve_used_items($parent);
            }


            return $this->items;

        }

        private function get_parent($parent)
        {

            $rdm = RepositoryDataManager::get_instance();
            $complex_content_object_parents = $rdm->retrieve_complex_content_object_items(new EqualityCondition(ComplexContentObjectItem :: PROPERTY_REF, $parent, ComplexContentObjectItem :: get_table_name()));
            while ($complex_content_object_item = $complex_content_object_parents->next_result())
            {
                if ($complex_content_object_item->is_complex() )
                {
                    $this->parents[] = $complex_content_object_item->get_parent();
                    $this->get_parent($complex_content_object_item->get_parent());
                }
                else
                {
                    $this->parents[] = $complex_content_object_item->get_parent();
                }
            }

            return;
        }

        private function get_child($parent)
        {

        }





}
?>