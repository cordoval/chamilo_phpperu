<?php
require_once dirname(__FILE__) . '/../peer_assessment_manager.class.php';
require_once dirname(__FILE__) . '/../peer_assessment_manager_component.class.php';
require_once dirname(__FILE__) . '/peer_assessment_publication_browser/peer_assessment_publication_browser_table.class.php';
require_once Path :: get_repository_path() . 'lib/complex_display/peer_assessment/peer_assessment_display.class.php';

/**
 * peer_assessment component which allows the user to browse his peer_assessment_publications
 * @author Nick Van Loocke
 */
class PeerAssessmentManagerPeerAssessmentPublicationsBrowserComponent extends PeerAssessmentManagerComponent
{
    private $action_bar;

    function run()
    {
        $trail = new BreadcrumbTrail();
        $trail->add(new Breadcrumb($this->get_url(), Translation :: get('PeerAssessment')));
        
        $this->display_header($trail);
        $this->action_bar = $this->get_toolbar();
        echo $this->action_bar->as_html();
        
        echo $this->get_table();
        $this->display_footer();
    }

    function get_table()
    {
        $table = new PeerAssessmentPublicationBrowserTable($this, array(Application :: PARAM_APPLICATION => PeerAssessmentManager :: APPLICATION_NAME, Application :: PARAM_ACTION => PeerAssessmentManager :: ACTION_BROWSE_PEER_ASSESSMENT_PUBLICATIONS), null);
        return $table->as_html();
    }

    function get_toolbar()
    {
        $action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);
        $action_bar->set_search_url($this->get_url());
        
        $action_bar->add_common_action(new ToolbarItem(Translation :: get('Publish'), Theme :: get_common_image_path() . 'action_create.png', $this->get_url(array(PeerAssessmentManager :: PARAM_ACTION => PeerAssessmentManager :: ACTION_CREATE_PEER_ASSESSMENT_PUBLICATION)), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        //$action_bar->add_common_action(new ToolbarItem(Translation :: get('ManageCategories'), Theme :: get_common_image_path() . 'action_category.png', $this->get_category_manager_url(), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
     	$action_bar->add_common_action(new ToolbarItem(Translation :: get('ShowAll'), Theme :: get_common_image_path() . 'action_browser.png', $this->get_url(array('category' => Request :: get('category'))), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        
        return $action_bar;
    }
}
?>