<?php
/**
 * $Id: viewer.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.complex_display.assessment.component
 */

require_once dirname(__FILE__) . '/../../browser/list_renderer/content_object_publication_details_renderer.class.php';

class ToolViewerComponent extends ToolComponent
{
    private $action_bar;
    
	function run()
    {
        $trail = BreadcrumbTrail :: get_instance();
        $trail->add(new Breadcrumb($this->get_url(array(Tool :: PARAM_PUBLICATION_ID => Request :: get(Tool :: PARAM_PUBLICATION_ID))), Translation :: get('Details')));
        
    	$this->action_bar = $this->get_action_bar();
        $renderer = new ContentObjectPublicationDetailsRenderer($this);
        $html = $renderer->as_html();
        
    	$this->display_header();
        echo $this->action_bar->as_html();
        echo '<div id="action_bar_browser">';
        echo $html;
        echo '</div>';
        $this->display_footer();
    }
    
    function get_publication_id()
    {
    	return Request :: get(Tool :: PARAM_PUBLICATION_ID);
    }
    
	function get_action_bar()
    {
        $action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);

        if ($this->is_allowed(EDIT_RIGHT))
        {
            $action_bar->add_common_action($this->get_access_details_toolbar_item($this));
        }

        return $action_bar;
    }
    
	function add_actionbar_item($item)
    {
        $this->action_bar->add_common_action($item);
    }
}
?>