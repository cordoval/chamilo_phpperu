<?php
namespace application\handbook;
use common\libraries\Request;
use common\extensions\repo_viewer\RepoViewer;
use common\libraries\BreadcrumbTrail;
use common\libraries\Breadcrumb;
use user\UserDataManager;
use repository\ContentObject;
use repository\RepositoryDataManager;
use repository\content_object\handbook_item\HandbookItem;
use repository\ComplexContentObjectItem;
use common\libraries\Translation;
use common\libraries\Utilities;
use repository\content_object\link\Link;
use common\libraries\EqualityCondition;
use repository\content_object\youtube\Youtube;
use repository\content_object\rss_feed\RssFeed;
use repository\content_object\handbook\Handbook;
use common\extensions\repo_viewer\RepoViewerInterface;
use repository\content_object\document\Document;


/**
 * Component to create a new handbook_publication object
 */
class HandbookManagerHandbookItemCreatorComponent extends HandbookManager implements RepoViewerInterface
{
    private $parents;
    private $items = array();

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $parent = Request :: get(HandbookManager::PARAM_HANDBOOK_ID);

        if (!RepoViewer::is_ready_to_be_published())
        {
            $exclude = $this->get_parents_and_childeren($parent);
            $exclude[] = $parent;


            $repo_viewer = RepoViewer :: construct($this);
            $repo_viewer->set_parameter(HandbookManager::PARAM_HANDBOOK_ID, $parent);
           
            $repo_viewer->set_excluded_objects($exclude);
            $repo_viewer->get_parent()->parse_input_from_table();

            $trail = new BreadcrumbTrail();
            $trail->add(new Breadcrumb($this->get_url(array(HandbookManager :: PARAM_ACTION => HandbookManager :: ACTION_BROWSE)), Translation :: get('BrowseHandbooks')));

            $udm = UserDataManager :: get_instance();
            $user = $udm->retrieve_user($this->get_user_id());
            
            $trail->add(new Breadcrumb($this->get_url(), Translation :: get('CreateObject', array('OBJECT' => Translation::get('HandbookItem')), Utilities::COMMON_LIBRARIES)));
            $trail->add_help('handbook create');

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

                if ($object->get_type() != Handbook :: get_type_name())
                {
//                    var_dump('handbook item');
                    $new_object = ContentObject :: factory(HandbookItem :: get_type_name());
                    $new_object->set_owner_id($this->get_user_id());
                    $new_object->set_title(HandbookItem :: get_type_name());
                    $new_object->set_description(HandbookItem :: get_type_name());
                    $new_object->set_parent_id(0);
                    $new_object->set_reference($object_id);
                    $new_object->create();
                }
                else
                {
//                    var_dump('handbook created');
                    $new_object = $object;
                }
                
                $wrapper = new ComplexContentObjectItem();
                $wrapper->set_ref($new_object->get_id());
                $wrapper->set_parent($parent);
                $wrapper->set_user_id($this->get_user_id());
                $wrapper->set_display_order($rdm->select_next_display_order($parent));
                $success &= $wrapper->create();

            }

            $params = array();
            $params[HandbookManager ::PARAM_ACTION] = self :: ACTION_VIEW_HANDBOOK;
            $params[HandbookManager ::PARAM_TOP_HANDBOOK_ID] = Request :: get(HandbookManager :: PARAM_TOP_HANDBOOK_ID );
            $params[HandbookManager ::PARAM_HANDBOOK_ID] =  Request :: get(HandbookManager :: PARAM_HANDBOOK_ID );
            $params[HandbookManager ::PARAM_HANDBOOK_PUBLICATION_ID] = Request :: get(HandbookManager :: PARAM_HANDBOOK_PUBLICATION_ID);
//            var_dump('redirect');
            $this->redirect($success ? Translation :: get('ObjectCreated', array('OBJECT' => Translation::get('HandbookItem')), Utilities::COMMON_LIBRARIES) : Translation :: get('ObjectNotCreated', array('OBJECT' => Translation::get('HandbookItem')), Utilities::COMMON_LIBRARIES), ! $success,
                    $params);
        }
    }

    function get_allowed_content_object_types()
    {
            return Handbook::get_allowed_content();
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