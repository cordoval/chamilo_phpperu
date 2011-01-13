<?php
namespace repository\content_object\glossary;

use common\libraries\Request;
use common\libraries\Translation;
use common\libraries\BreadcrumbTrail;
use common\libraries\Breadcrumb;
use common\libraries\EqualityCondition;
use common\libraries\ActionBarRenderer;
use common\libraries\ToolbarItem;
use common\libraries\Toolbar;
use common\libraries\Theme;
use common\libraries\Utilities;
use repository\ComplexDisplay;
use repository\RepositoryDataManager;
use repository\content_object\glossary_item\GlossaryItem;
use repository\ComplexContentObjectItem;
use common\libraries\PatternMatchCondition;
use common\libraries\AndCondition;
use common\libraries\OrCondition;
use repository\ContentObject;

/**
 * $Id: glossary_viewer.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.complex_display.glossary.component
 */

require_once dirname(__FILE__) . '/glossary_viewer/glossary_viewer_table.class.php';

/**
 * Represents the view component for the assessment tool.
 *
 */
class GlossaryDisplayViewerComponent extends GlossaryDisplay
{
    private $action_bar;

    const PARAM_VIEW = 'view';
    const VIEW_LIST = 'list';
    const VIEW_TABLE = 'table';

    function run()
    {
//        $this->action_bar = $this->get_action_bar();
//
//        $dm = RepositoryDataManager :: get_instance();
//
//        $object = $this->get_root_content_object();
//
//        $trail = BreadcrumbTrail :: get_instance();
//        if(!is_array($object))
//        {
//            $trail->add(new Breadcrumb($this->get_url(), $object->get_title()));
//        }
        $this->display_header();

//        echo $this->action_bar->as_html();
//
//        if ($this->get_view() == self :: VIEW_TABLE)
//        {
//            $table = new GlossaryViewerTable($this);
//            echo $table->as_html();
//        }
//        else
//        {
//            if(!is_array($object))
//            {
//                $object = array($object);
//            }
//            foreach($object as $obj)
//            {
//                $children = $dm->retrieve_complex_content_object_items(new EqualityCondition(ComplexContentObjectItem :: PROPERTY_PARENT, $obj->get_id(), ComplexContentObjectItem :: get_table_name()));
//                while ($child = $children->next_result())
//                {
//                    $content_object = $dm->retrieve_content_object($child->get_ref());
//                    echo $this->display_content_object($content_object, $child);
//                }
//            }
//        }
        echo $this->to_html();
        $this->display_footer();
    }

    function to_html()
    {
        $html = array();
        $this->action_bar = $this->get_action_bar();
        $dm = RepositoryDataManager :: get_instance();
        $object = $this->get_parent()->get_root_content_object($this);
        $trail = BreadcrumbTrail :: get_instance();
        if(!is_array($object))
        {
            $trail->add(new Breadcrumb($this->get_url(), $object->get_title()));
        }
        $html[] = $this->action_bar->as_html();
        if ($this->get_view() == self :: VIEW_TABLE)
        {
            $table = new GlossaryViewerTable($this);
            $html[] =  $table->as_html();
        }
        else
        {
            if(!is_array($object))
            {
                $object = array($object);
            }
            foreach($object as $obj)
            {
                $children = $dm->retrieve_complex_content_object_items(new EqualityCondition(ComplexContentObjectItem :: PROPERTY_PARENT, $obj->get_id(), ComplexContentObjectItem :: get_table_name()));
                while ($child = $children->next_result())
                {
                    $content_object = $dm->retrieve_content_object($child->get_ref());
                    $html[] =  $this->display_content_object($content_object, $child);
                }
            }
        }
        return implode("\n" , $html);
    }

    function display_content_object($content_object, $complex_content_object_item)
    {
        $html[] = '<div class="title" style="background-color: #e6e6e6; border: 1px solid grey; padding: 5px; font-weight: bold; color: #666666">';
        $html[] = '<div style="padding-top: 1px; float: left">';
        $html[] = $content_object->get_title();
        $html[] = '</div>';
        $html[] = '<div style="float: right">';
        $html[] = $this->get_actions($complex_content_object_item);
        $html[] = '</div>';
        $html[] = '<div class="clear">&nbsp</div>';
        $html[] = '</div>';
        $html[] = '<div class="description">';
        $html[] = $content_object->get_description();
        $html[] = '</div><br />';

        return implode("\n", $html);
    }

    function get_actions($complex_content_object_item)
    {
        $toolbar = new Toolbar();
        if ($this->get_parent()->is_allowed(EDIT_RIGHT))
        {
            $toolbar->add_item(new ToolbarItem(Translation :: get('Edit'), Theme :: get_common_image_path() . 'action_edit.png', $this->get_complex_content_object_item_update_url($complex_content_object_item), ToolbarItem :: DISPLAY_ICON));
        }

        if ($this->get_parent()->is_allowed(DELETE_RIGHT))
        {
            $toolbar->add_item(new ToolbarItem(Translation :: get('Delete'), Theme :: get_common_image_path() . 'action_delete.png', $this->get_complex_content_object_item_delete_url($complex_content_object_item), ToolbarItem :: DISPLAY_ICON, true));
        }

        return $toolbar->as_html();
    }

    function get_view()
    {
        $view = Request :: get(self :: PARAM_VIEW);
        if (! $view)
        {
            $view = self :: VIEW_TABLE;
        }

        return $view;
    }

    function get_action_bar()
    {
        $action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);

        if ($this->get_parent()->is_allowed(ADD_RIGHT))
        {
            $action_bar->add_common_action(new ToolbarItem(Translation :: get('CreateItem'), Theme :: get_common_image_path() . 'action_create.png', $this->get_url(array(
                    ComplexDisplay :: PARAM_DISPLAY_ACTION => ComplexDisplay :: ACTION_CREATE_COMPLEX_CONTENT_OBJECT_ITEM, ComplexDisplay :: PARAM_TYPE => GlossaryItem :: get_type_name(),
                    self :: PARAM_VIEW => self :: VIEW_TABLE)), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        }

        $action_bar->add_tool_action(new ToolbarItem(Translation :: get('TableView', null, Utilities :: COMMON_LIBRARIES), Theme :: get_common_image_path() . 'action_browser.png', $this->get_url(array(self :: PARAM_VIEW => self :: VIEW_TABLE)), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        $action_bar->add_tool_action(new ToolbarItem(Translation :: get('ListView', null, Utilities :: COMMON_LIBRARIES), Theme :: get_common_image_path() . 'action_browser.png', $this->get_url(array(self :: PARAM_VIEW => self :: VIEW_LIST)), ToolbarItem :: DISPLAY_ICON_AND_LABEL));

        $action_bar->set_search_url($this->get_url($this->get_parameters()));
        
        return $action_bar;
    }

    function get_condition()
    {

        $query = $this->action_bar->get_query();
        
        if (isset($query) && $query != '')
        {
            $search_conditions[] = new PatternMatchCondition(GlossaryItem::PROPERTY_TITLE, '*' . $query . '*', ContentObject :: get_table_name());
            $search_conditions[] = new PatternMatchCondition(GlossaryItem::PROPERTY_DESCRIPTION, '*' . $query . '*', ContentObject :: get_table_name());
            $conditions[] = new OrCondition($search_conditions);
        }

        $objects = $this->get_parent()->get_root_content_object($this);;
        if(!is_array($objects))
        {
            $objects = array($objects);
        }
        $co_conditions = array();

        foreach ($objects as $object)
        {
            $co_conditions[]= new EqualityCondition(ComplexContentObjectItem :: PROPERTY_PARENT, $object->get_id(), ComplexContentObjectItem :: get_table_name());
        }



        $conditions[] =  new OrCondition($co_conditions);

        $condition = new AndCondition($conditions);
        
        return $condition;
    }
}

?>