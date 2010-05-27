<?php
/**
 * $Id: glossary_viewer.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.complex_display.glossary.component
 */

require_once dirname(__FILE__) . '/glossary_viewer/glossary_viewer_table.class.php';
require_once Path :: get_application_path() . '/lib/weblcms/browser/learningobjectpublicationcategorytree.class.php';

/**
 * Represents the view component for the assessment tool.
 *
 */
class GlossaryDisplayGlossaryViewerComponent extends GlossaryDisplay
{
    private $action_bar;

    const PARAM_VIEW = 'view';
    const VIEW_LIST = 'list';
    const VIEW_TABLE = 'table';

    function run()
    {
        $this->action_bar = $this->get_action_bar();

        $dm = RepositoryDataManager :: get_instance();

        $object = $this->get_root_content_object();

        $trail = new BreadcrumbTrail(false);
        $trail->add(new Breadcrumb($this->get_url(), $object->get_title()));
        $this->display_header($trail);

        echo $this->action_bar->as_html();

        if ($this->get_view() == self :: VIEW_TABLE)
        {
            $table = new GlossaryViewerTable($this, $this->get_user(), Request :: get(Tool :: PARAM_PUBLICATION_ID));
            echo $table->as_html();
        }
        else
        {
            $children = $dm->retrieve_complex_content_object_items(new EqualityCondition(ComplexContentObjectItem :: PROPERTY_PARENT, $object->get_id(), ComplexContentObjectItem :: get_table_name()));
            while ($child = $children->next_result())
            {
                $content_object = $dm->retrieve_content_object($child->get_ref());
                echo $this->display_content_object($content_object, $child);
            }
        }

    	$this->display_footer();
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
        if ($this->get_parent()->get_parent()->is_allowed(EDIT_RIGHT))
        {
            $actions[] = array('href' => $this->get_url(array(ComplexDisplay :: PARAM_DISPLAY_ACTION => ComplexDisplay :: ACTION_UPDATE_COMPLEX_CONTENT_OBJECT_ITEM, 'selected_complex_content_object_item' => $complex_content_object_item->get_id())), 'label' => Translation :: get('Edit'), 'img' => Theme :: get_common_image_path() . 'action_edit.png');
        }

        if ($this->get_parent()->get_parent()->is_allowed(DELETE_RIGHT))
        {
            $actions[] = array('href' => $this->get_url(array(ComplexDisplay :: PARAM_DISPLAY_ACTION => ComplexDisplay :: ACTION_DELETE_COMPLEX_CONTENT_OBJECT_ITEM, 'selected_complex_content_object_item' => $complex_content_object_item->get_id())), 'label' => Translation :: get('Delete'), 'img' => Theme :: get_common_image_path() . 'action_delete.png');
        }

        return Utilities :: build_toolbar($actions);
    }

    function get_view()
    {
        $view = Request :: get(self :: PARAM_VIEW);
        if (! $view)
            $view = self :: VIEW_TABLE;

        return $view;
    }

    function get_action_bar()
    {
        $action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);

        $action_bar->add_common_action(new ToolbarItem(Translation :: get('Create'), Theme :: get_common_image_path() . 'action_create.png', $this->get_url(array(ComplexDisplay :: PARAM_DISPLAY_ACTION => ComplexDisplay :: ACTION_CREATE_COMPLEX_CONTENT_OBJECT_ITEM, ComplexDisplay :: PARAM_TYPE => GlossaryItem :: get_type_name(), self :: PARAM_VIEW => self :: VIEW_TABLE)), ToolbarItem :: DISPLAY_ICON_AND_LABEL));

        $action_bar->add_tool_action(new ToolbarItem(Translation :: get('ShowAsTable'), Theme :: get_common_image_path() . 'action_browser.png', $this->get_url(array(self :: PARAM_VIEW => self :: VIEW_TABLE)), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        $action_bar->add_tool_action(new ToolbarItem(Translation :: get('ShowAsList'), Theme :: get_common_image_path() . 'action_browser.png', $this->get_url(array(self :: PARAM_VIEW => self :: VIEW_LIST)), ToolbarItem :: DISPLAY_ICON_AND_LABEL));

        return $action_bar;
    }

    function get_condition()
    {
        return new EqualityCondition(ComplexContentObjectItem :: PROPERTY_PARENT, $this->get_root_content_object()->get_id(), ComplexContentObjectItem :: get_table_name());
    }
}

?>