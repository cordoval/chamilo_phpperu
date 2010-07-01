<?php
/**
 * $Id: blog_viewer.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.complex_display.blog.component
 */

/**
 * Represents the view component for the assessment tool.
 *
 */
class BlogDisplayViewerComponent extends BlogDisplay
{
    private $action_bar;

    function run()
    {
        $this->action_bar = $this->get_action_bar();
        
        $dm = RepositoryDataManager :: get_instance();
        
        $object = $this->get_root_content_object();
        
        $trail = BreadcrumbTrail :: get_instance();
        $trail->add(new Breadcrumb($this->get_url(), $object->get_title()));
        $this->display_header();

        echo $this->action_bar->as_html();
        
        $children = $dm->retrieve_complex_content_object_items(new EqualityCondition(ComplexContentObjectItem :: PROPERTY_PARENT, $object->get_id(), ComplexContentObjectItem :: get_table_name()));
        while ($child = $children->next_result())
        {
            $content_object = $dm->retrieve_content_object($child->get_ref());
            echo $this->display_content_object($content_object, $child);
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
    	$toolbar = new Toolbar();
        if ($this->get_parent()->get_parent()->is_allowed(EDIT_RIGHT))
        {
            $toolbar->add_item(new ToolbarItem(
        			Translation :: get('Edit'),
        			Theme :: get_common_image_path().'action_edit.png', 
					$this->get_complex_content_object_item_update_url($complex_content_object_item),
				 	ToolbarItem :: DISPLAY_ICON
			));
        }
        
        if ($this->get_parent()->get_parent()->is_allowed(DELETE_RIGHT))
        {
        	$toolbar->add_item(new ToolbarItem(
        			Translation :: get('Delete'),
        			Theme :: get_common_image_path().'action_delete.png', 
					$this->get_complex_content_object_item_delete_url($complex_content_object_item),
				 	ToolbarItem :: DISPLAY_ICON,
				 	true
			));
        }
        
        return $toolbar->as_html();
    }

    function get_action_bar()
    {
        $action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);
        
        if($this->get_parent()->get_parent()->is_allowed(ADD_RIGHT))
        	$action_bar->add_common_action(new ToolbarItem(Translation :: get('CreateItem'), Theme :: get_common_image_path() . 'action_create.png', $this->get_url(array(ComplexDisplay :: PARAM_DISPLAY_ACTION => ComplexDisplay :: ACTION_CREATE_COMPLEX_CONTENT_OBJECT_ITEM, ComplexDisplay :: PARAM_TYPE => BlogItem :: get_type_name())), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        
        return $action_bar;
    }

    function get_condition()
    {
        return new EqualityCondition(ComplexContentObjectItem :: PROPERTY_PARENT, $this->get_root_content_object_id(), ComplexContentObjectItem :: get_table_name());
    }
}

?>