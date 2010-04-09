<?php
require_once dirname(__FILE__) . '/peer_assessment_display_component.class.php';
/**
 * This tool allows a user to publish peer_assessments in his or her course.
 * 
 * author: Nick Van Loocke
 */
class PeerAssessmentDisplay extends ComplexDisplay
{
    const PARAM_PEER_ASSESSMENT_ID = 'peer_assessment_id';
    const PARAM_PEER_ASSESSMENT_PAGE_ID = 'peer_assessment_page_id';
    
    const ACTION_VIEW_PEER_ASSESSMENT = 'view';   
    const ACTION_UPDATE = 'update';
    const ACTION_DELETE = 'delete';
    const ACTION_PUBLISH = 'publish';

    /**
     * Inherited.
     */
    function run()
    {
        //peer_assessment tool
        $action = $this->get_action();
        
        switch ($action)
        {
            case self :: ACTION_VIEW_PEER_ASSESSMENT :
                $component = PeerAssessmentDisplayComponent :: factory('PeerAssessmentViewer', $this);
                break;
            case self :: ACTION_UPDATE :
                $component = ComplexDisplayComponent :: factory(null, 'Updater', $this);
                break;
            case self :: ACTION_DELETE :
                $component = ComplexDisplayComponent :: factory(null, 'Deleter', $this);
                break;
            case self :: ACTION_PUBLISH :
                $component = ComplexDisplayComponent :: factory(null, 'Publisher', $this);
                break;
            default :
                $component = PeerAssessmentDisplayComponent :: factory('PeerAssessmentViewer', $this);
        }
        $component->run();
    }

    static function get_allowed_types()
    {
        return array('peer_assessment');
    }

    public function get_toolbar($parent, $pid, $lo, $selected_cloi)
    {
        // TYPE_WIKI isn't a good chosen name
        $action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_WIKI);
        
        $action_bar->set_search_url($parent->get_url());
        
        $action_bar->add_common_action(new ToolbarItem(Translation :: get('CreateCompetence'), Theme :: get_common_image_path() . 'action_create.png', $parent->get_url(array(PeerAssessmentDisplay :: PARAM_DISPLAY_ACTION => PeerAssessmentDisplay :: ACTION_CREATE, 'pid' => $pid)), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
     	$action_bar->add_common_action(new ToolbarItem(Translation :: get('CreateIndicator'), Theme :: get_common_image_path() . 'action_create.png', $parent->get_url(array(PeerAssessmentDisplay :: PARAM_DISPLAY_ACTION => PeerAssessmentDisplay :: ACTION_CREATE, 'pid' => $pid)), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
     	$action_bar->add_common_action(new ToolbarItem(Translation :: get('CreateCriteria'), Theme :: get_common_image_path() . 'action_create.png', $parent->get_url(array(PeerAssessmentDisplay :: PARAM_DISPLAY_ACTION => PeerAssessmentDisplay :: ACTION_CREATE, 'pid' => $pid)), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
            
        return $action_bar;
    }

    function get_breadcrumbtrail()
    {
        $trail = new BreadcrumbTrail(false);
        $trail->add(new Breadcrumb($this->get_url(array(ComplexDisplay :: PARAM_DISPLAY_ACTION => ComplexDisplay :: ACTION_VIEW_CLO, ComplexDisplay :: PARAM_ROOT_LO => Request :: get('pid'))), $this->get_root_lo()->get_title()));
        /*switch (Request :: get(ComplexDisplay :: PARAM_DISPLAY_ACTION))
        {
            case PeerAssessmentDisplay :: ACTION_CREATE :
                $trail->add(new Breadcrumb($this->get_url(array(ComplexDisplay :: PARAM_DISPLAY_ACTION => PeerAssessmentDisplay :: ACTION_CREATE_PAGE, ComplexDisplay :: PARAM_ROOT_LO => Request :: get('pid'))), Translation :: get('CreatePeerAssessmentPage')));
                break;
            case PeerAssessmentDisplay :: ACTION_VIEW_PEER_ASSESSMENT :
                $trail->add(new Breadcrumb($this->get_url(array(ComplexDisplay :: PARAM_DISPLAY_ACTION => PeerAssessmentDisplay :: ACTION_VIEW_PEER_ASSESSMENT_PAGE, ComplexDisplay :: PARAM_ROOT_LO => Request :: get('pid'), ComplexDisplay :: PARAM_SELECTED_CLOI_ID => Request :: get(ComplexDisplay :: PARAM_SELECTED_CLOI_ID))), $this->get_lo_from_cid(Request :: get(ComplexDisplay :: PARAM_SELECTED_CLOI_ID))->get_title()));
                break;
            case PeerAssessmentDisplay :: ACTION_UPDATE :
                $trail->add(new Breadcrumb($this->get_url(array(ComplexDisplay :: PARAM_DISPLAY_ACTION => PeerAssessmentDisplay :: ACTION_VIEW_PEER_ASSESSMENT_PAGE, ComplexDisplay :: PARAM_ROOT_LO => Request :: get('pid'), ComplexDisplay :: PARAM_SELECTED_CLOI_ID => Request :: get(ComplexDisplay :: PARAM_SELECTED_CLOI_ID))), $this->get_lo_from_cid(Request :: get(ComplexDisplay :: PARAM_SELECTED_CLOI_ID))->get_title()));
                $trail->add(new Breadcrumb($this->get_url(array(ComplexDisplay :: PARAM_DISPLAY_ACTION => PeerAssessmentDisplay :: ACTION_UPDATE, ComplexDisplay :: PARAM_ROOT_LO => Request :: get('pid'), ComplexDisplay :: PARAM_SELECTED_CLOI_ID => Request :: get(ComplexDisplay :: PARAM_SELECTED_CLOI_ID))), Translation :: get('Edit')));
                break;

        }*/
        return $trail;
    }

    private function get_lo_from_cid($cid)
    {
        $cloi = RepositoryDataManager :: get_instance()->retrieve_complex_content_object_item($cid);
        return RepositoryDataManager :: get_instance()->retrieve_content_object($cloi->get_ref());
    }

}
?>