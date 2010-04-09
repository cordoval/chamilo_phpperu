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
class GlossaryDisplayGlossaryViewerComponent extends GlossaryDisplayComponent
{
    private $action_bar;
    
    const PARAM_VIEW = 'view';
    const VIEW_LIST = 'list';
    const VIEW_TABLE = 'table';

    function run()
    {
        $this->action_bar = $this->get_action_bar();
        
        $object_id = Request :: get('pid');
        $dm = RepositoryDataManager :: get_instance();
        $object = $dm->retrieve_content_object($object_id);
        
        $trail = new BreadcrumbTrail();
        $trail->add(new Breadcrumb($this->get_url(array(Tool :: PARAM_ACTION => GlossaryTool :: ACTION_VIEW_GLOSSARY, Tool :: PARAM_PUBLICATION_ID => Request :: get('pid'))), $object->get_title()));
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
                $lo = $dm->retrieve_content_object($child->get_ref());
                echo $this->display_content_object($lo, $child);
            }
        }
        
    	$this->display_footer();
    }

    function display_content_object($lo, $cloi)
    {
        $html[] = '<div class="title" style="background-color: #e6e6e6; border: 1px solid grey; padding: 5px; font-weight: bold; color: #666666">';
        $html[] = '<div style="padding-top: 1px; float: left">';
        $html[] = $lo->get_title();
        $html[] = '</div>';
        $html[] = '<div style="float: right">';
        $html[] = $this->get_actions($cloi);
        $html[] = '</div>';
        $html[] = '<div class="clear">&nbsp</div>';
        $html[] = '</div>';
        $html[] = '<div class="description">';
        $html[] = $lo->get_description();
        $html[] = '</div><br />';
        
        return implode("\n", $html);
    }

    function get_actions($cloi)
    {
        if ($this->get_parent()->get_parent()->is_allowed(EDIT_RIGHT))
        {
            $actions[] = array('href' => $this->get_url(array(ComplexDisplay :: PARAM_DISPLAY_ACTION => ComplexDisplay :: ACTION_UPDATE, 'selected_cloi' => $cloi->get_id(), 'pid' => Request :: get('pid'))), 'label' => Translation :: get('Edit'), 'img' => Theme :: get_common_image_path() . 'action_edit.png');
        }
        
        if ($this->get_parent()->get_parent()->is_allowed(DELETE_RIGHT))
        {
            $actions[] = array('href' => $this->get_url(array(ComplexDisplay :: PARAM_DISPLAY_ACTION => ComplexDisplay :: ACTION_DELETE, 'selected_cloi' => $cloi->get_id(), 'pid' => Request :: get('pid'))), 'label' => Translation :: get('Delete'), 'img' => Theme :: get_common_image_path() . 'action_delete.png');
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
        
        $action_bar->add_common_action(new ToolbarItem(Translation :: get('Create'), Theme :: get_common_image_path() . 'action_create.png', $this->get_url(array(ComplexDisplay :: PARAM_DISPLAY_ACTION => ComplexDisplay :: ACTION_CREATE, 'pid' => Request :: get('pid'), 'type' => 'glossary_item', self :: PARAM_VIEW => self :: VIEW_TABLE)), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        
        $action_bar->add_tool_action(new ToolbarItem(Translation :: get('ShowAsTable'), Theme :: get_common_image_path() . 'action_browser.png', $this->get_url(array('pid' => Request :: get('pid'), self :: PARAM_VIEW => self :: VIEW_TABLE)), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        $action_bar->add_tool_action(new ToolbarItem(Translation :: get('ShowAsList'), Theme :: get_common_image_path() . 'action_browser.png', $this->get_url(array('pid' => Request :: get('pid'), self :: PARAM_VIEW => self :: VIEW_LIST)), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        
        return $action_bar;
    }

    function get_condition()
    {
        return new EqualityCondition(ComplexContentObjectItem :: PROPERTY_PARENT, Request :: get('pid'), ComplexContentObjectItem :: get_table_name());
    }
}

?>