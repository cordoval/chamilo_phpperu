<?php
/**
 * $Id: document_slideshow.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.tool.document.component
 */
require_once dirname(__FILE__) . '/../document_tool.class.php';
require_once dirname(__FILE__) . '/../document_tool_component.class.php';
require_once dirname(__FILE__) . '/document_slideshow/document_slideshow_browser.class.php';
require_once dirname(__FILE__) . '/../../../category_manager/content_object_publication_category_manager.class.php';

class DocumentToolSlideshowComponent extends DocumentToolComponent
{
    private $action_bar;

    function run()
    {
        if (! $this->is_allowed(VIEW_RIGHT))
        {
            Display :: not_allowed();
            return;
        }
        
        $browser = new DocumentSlideshowBrowser($this);
        $this->action_bar = $this->get_action_bar();
        
        $trail = new BreadcrumbTrail();
        $trail->add_help('courses document tool');
        
        $this->display_header($trail, true);
        echo $this->action_bar->as_html();
        echo $browser->as_html();
        $this->display_footer();
    }

    function get_action_bar()
    {
        $action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);
        
        if (Request :: get('thumbnails'))
        {
            $action_bar->add_common_action(new ToolbarItem(Translation :: get('Slideshow'), Theme :: get_common_image_path() . 'action_slideshow.png', $this->get_url(array('tool_action' => 'slideshow', 'thumbnails' => null)), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        }
        else
        {
            $action_bar->add_common_action(new ToolbarItem(Translation :: get('Thumbnails'), Theme :: get_common_image_path() . 'action_slideshow_thumbnail.png', $this->get_url(array('tool_action' => 'slideshow', 'thumbnails' => 1)), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        }
        
        $action_bar->add_common_action(new ToolbarItem(Translation :: get('SlideshowSettings'), Theme :: get_common_image_path() . 'action_config.png', $this->get_url(array('tool_action' => DocumentTool :: ACTION_SLIDESHOW_SETTINGS)), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        
        return $action_bar;
    }
}
?>