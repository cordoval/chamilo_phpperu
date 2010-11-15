<?php
namespace repository\content_object\forum;

use repository\RepositoryDataManager;
use common\libraries\Request;
use common\libraries\Translation;
use common\libraries\Breadcrumb;
use common\libraries\BreadcrumbTrail;
use common\libraries\Utilities;
use common\libraries\EqualityCondition;
use repository\ComplexBuilder;
use common\extensions\repo_viewer\RepoViewer;
use common\extensions\repo_viewer\RepoViewerInterface;
use repository\ComplexContentObjectItem;

/**
 * $Id: creator.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.complex_builder.forum.component
 */
class ForumBuilderCreatorComponent extends ForumBuilder implements RepoViewerInterface
{

    private $repository_data_manager;
    private $type;

    function run()
    {
        $trail = BreadcrumbTrail :: get_instance();
        $menu_trail = $this->get_complex_content_object_breadcrumbs();
        $trail->merge($menu_trail);
        $trail->add_help('repository forum builder');

        $complex_content_object_item_id = Request :: get(ComplexBuilder :: PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID);
        $this->type = $rtype = Request :: get(ComplexBuilder :: PARAM_TYPE);

        $parameters = array(ComplexBuilder :: PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID => $complex_content_object_item_id, ComplexBuilder :: PARAM_TYPE => $type);

        $trail->add(new Breadcrumb($this->get_url($parameters), Translation :: get('Add' . Utilities :: underscores_to_camelcase($type))));

        $this->repository_data_manager = RepositoryDataManager :: get_instance();
        $parent = $this->get_root_content_object_id();
        if ($complex_content_object_item_id)
        {
            $parent_complex_content_object_item = $this->repository_data_manager->retrieve_complex_content_object_item($complex_content_object_item_id);
            $parent = $parent_complex_content_object_item->get_ref();
        }

        if ($this->get_complex_content_object_item())
        {
            $content_object = $this->repository_data_manager->retrieve_content_object($this->get_complex_content_object_item()->get_ref());
        }
        else
        {
            $content_object = $this->get_root_content_object();
        }

        $exclude = $this->retrieve_used_items($this->get_root_content_object()->get_id());
        $exclude[] = $this->get_root_content_object()->get_id();

        if (!$this->type)
        {
            $this->type = $content_object->get_allowed_types();
        }



        if (! RepoViewer :: is_ready_to_be_published())
        {
            $pub = RepoViewer :: construct($this);
            if ($rtype)
            {
                $pub->set_parameter(ComplexBuilder :: PARAM_TYPE, $rtype);
            }

            $pub->set_parameter(ComplexBuilder :: PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID, $complex_content_object_item_id);
            $pub->set_excluded_objects($exclude);
            $t = is_array($this->type) ? implode(',', $this->type) : $this->type;
            //$p = $this->repository_data_manager->retrieve_content_object($parent);
            //$html[] = '<h4>' . sprintf(Translation :: get('AddOrCreateNewTo'), $t, $p->get_type(), $p->get_title()) . '</h4><br />';
            $pub->run();
        }
        else
        {
            $object = RepoViewer::get_selected_objects();
            if (!is_array($object))
            {
                $object = array($object);
            }

            $repository_data_manager = $this->repository_data_manager;

            foreach ($object as $obj)
            {
                $type = $repository_data_manager->determine_content_object_type($obj);

                $complex_content_object_item = ComplexContentObjectItem :: factory($type);

                $complex_content_object_item->set_ref($obj);

                $complex_content_object_item->set_parent($parent);

                if ($type == Forum :: get_type_name())
                {
                    $complex_content_object_item->set_display_order($repository_data_manager->select_next_display_order_forum($parent));
                }
                $complex_content_object_item->set_user_id($this->get_user_id());
                $complex_content_object_item->create();
            }

            $this->redirect(Translation :: get('ObjectAdded', array('OBJECT' => Translation :: get('Forum')) , Utilities :: COMMON_LIBRARIES), false, array(ComplexBuilder :: PARAM_BUILDER_ACTION => ComplexBuilder :: ACTION_BROWSE, ComplexBuilder :: PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID => $complex_content_object_item_id));
        }

        /* $this->display_header($trail);
          echo '<br />' . implode("\n", $html);
          $this->display_footer(); */
    }

    private function retrieve_used_items($parent)
    {
        $items = array();

        $complex_content_object_items = $this->repository_data_manager->retrieve_complex_content_object_items(new EqualityCondition(ComplexContentObjectItem :: PROPERTY_PARENT, $parent, ComplexContentObjectItem :: get_table_name()));
        while ($complex_content_object_item = $complex_content_object_items->next_result())
        {
            if ($complex_content_object_item->is_complex())
            {
                $items[] = $complex_content_object_item->get_ref();
                $items = array_merge($items, $this->retrieve_used_items($complex_content_object_item->get_ref()));
            }
        }
        return $items;
    }

    function get_allowed_content_object_types()
    {
        return array($this->type);
    }

}

?>