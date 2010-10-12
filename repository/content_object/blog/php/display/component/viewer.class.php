<?php
/**
 * $Id: blog_viewer.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.complex_display.blog.component
 */

require_once dirname(__FILE__) . '/viewer/blog_layout.class.php';

/**
 * Represents the view component for the assessment tool.
 *
 */
class BlogDisplayViewerComponent extends BlogDisplay implements DelegateComponent
{
    private $action_bar;

    function run()
    {
        $this->action_bar = $this->get_action_bar();
        
        $blog = $this->get_root_content_object();
        
        $trail = BreadcrumbTrail :: get_instance();
        
        
        $blog_layout = BlogLayout :: factory($this, $blog);
        
        $this->display_header();
        echo $this->action_bar->as_html();
        $blog_layout->render();
    	$this->display_footer();
    }

    function get_action_bar()
    {
        $action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);
        
        if($this->is_allowed(ADD_RIGHT))
        {
        	$action_bar->add_common_action(new ToolbarItem(Translation :: get('CreateItem'), Theme :: get_common_image_path() . 'action_create.png', $this->get_url(array(ComplexDisplay :: PARAM_DISPLAY_ACTION => ComplexDisplay :: ACTION_CREATE_COMPLEX_CONTENT_OBJECT_ITEM, ComplexDisplay :: PARAM_TYPE => BlogItem :: get_type_name())), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        }
        
        return $action_bar;
    }

    function  add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $breadcrumbtrail->add(new Breadcrumb($this->get_url(), $this->get_root_content_object()->get_title()));
    }
    
}

?>