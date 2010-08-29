<?php
/**
 * $Id: viewer.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.complex_display.assessment.component
 */

require_once dirname(__FILE__) . '/../../browser/list_renderer/content_object_publication_details_renderer.class.php';

class ToolComponentViewerComponent extends ToolComponent
{
    private $action_bar;
    private $html;

	function run()
    {
        $trail = BreadcrumbTrail :: get_instance();
        $trail->add(new Breadcrumb($this->get_url(array(Tool :: PARAM_PUBLICATION_ID => Request :: get(Tool :: PARAM_PUBLICATION_ID))), Translation :: get('Details')));

    	$this->action_bar = $this->get_action_bar();
        $renderer = new ContentObjectPublicationDetailsRenderer($this);
        $this->html = $renderer->as_html();
        
    	if($this->get_course()->get_feedback())
        {
    		$feedback_manager = new FeedbackManager($this, WeblcmsManager :: APPLICATION_NAME, $this->get_publication_id());
        	return $feedback_manager->run();
        }
    }
    
    function display_header()
    {
    	parent :: display_header();
        echo $this->action_bar->as_html();
        echo '<div id="action_bar_browser">';
        echo $this->html;
    }
    
    function display_footer()
    {
    	echo '</div>';
        parent :: display_footer();
    }

    function get_publication_id()
    {
    	return Request :: get(Tool :: PARAM_PUBLICATION_ID);
    }

    function get_publication_count()
    {
        return 1;
    }

	function get_action_bar()
    {
        $action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);

        if ($this->is_allowed(WeblcmsRights :: EDIT_RIGHT))
        {
            $action_bar->add_common_action($this->get_access_details_toolbar_item($this));
        }

        return $action_bar;
    }

	function add_actionbar_item($item)
    {
        $this->action_bar->add_common_action($item);
    }

    function set_action_bar($action_bar)
    {
        $this->action_bar = $action_bar;
    }


}
?>