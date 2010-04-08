<?php
require_once dirname(__FILE__) . '/peer_assessment_display_component.class.php';
//require_once Path :: get_application_path() . 'lib/weblcms/tool/peer_assessment/peer_assessment_tool_component.class.php';
/**
 * This tool allows a user to publish peer_assessments in his or her course.
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
        
        $action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_PEER_ASSESSMENT);
        
        $action_bar->set_search_url($parent->get_url());
        
        //PAGE ACTIONS
        $action_bar->add_common_action(new ToolbarItem(Translation :: get('CreatePeerAssessmentPage'), Theme :: get_common_image_path() . 'action_create.png', $parent->get_url(array(PeerAssessmentDisplay :: PARAM_DISPLAY_ACTION => PeerAssessmentDisplay :: ACTION_CREATE_PAGE, 'pid' => $pid)), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        
        if (! empty($selected_cloi))
        {
            //if($this->get_parent()->get_parent()->is_allowed(EDIT_RIGHT))
            {
                $action_bar->add_common_action(new ToolbarItem(Translation :: get('Edit'), Theme :: get_common_image_path() . 'action_edit.png', $parent->get_url(array(ComplexDisplay :: PARAM_DISPLAY_ACTION => ComplexDisplay :: ACTION_UPDATE, 'pid' => $pid, 'selected_cloi' => $selected_cloi)), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
            }
            
            //if($this->get_parent()->get_parent()->is_allowed(DELETE_RIGHT))
            {
                $action_bar->add_common_action(new ToolbarItem(Translation :: get('Delete'), Theme :: get_common_image_path() . 'action_delete.png', $parent->get_url(array(ComplexDisplay :: PARAM_DISPLAY_ACTION => ComplexDisplay :: ACTION_DELETE, 'pid' => $pid, 'selected_cloi' => $selected_cloi)), ToolbarItem :: DISPLAY_ICON_AND_LABEL, true));
            }
            
            if (Request :: get('display_action') == 'discuss')
            {
                $action_bar->add_common_action(new ToolbarItem(Translation :: get('AddFeedback'), Theme :: get_common_image_path() . 'action_add.png', $parent->get_url(array(PeerAssessmentDisplay :: PARAM_DISPLAY_ACTION => PeerAssessmentDisplay :: ACTION_FEEDBACK_CLOI, 'pid' => $pid, 'peer_assessment_publication' => Request :: get('peer_assessment_publication'), 'selected_cloi' => $selected_cloi)), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
            }
            
            $action_bar->add_common_action(new ToolbarItem(Translation :: get('Discuss'), Theme :: get_common_image_path() . 'action_users.png', $parent->get_url(array(PeerAssessmentDisplay :: PARAM_DISPLAY_ACTION => PeerAssessmentDisplay :: ACTION_DISCUSS, 'pid' => $pid, 'peer_assessment_publication' => Request :: get('peer_assessment_publication'), 'selected_cloi' => $selected_cloi)), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
            
            $action_bar->add_common_action(new ToolbarItem(Translation :: get('BrowsePeerAssessment'), Theme :: get_common_image_path() . 'action_browser.png', $parent->get_url(array(PeerAssessmentDisplay :: PARAM_DISPLAY_ACTION => PeerAssessmentDisplay :: ACTION_VIEW_PEER_ASSESSMENT, 'pid' => $pid)), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
            
            //INFORMATION
            $action_bar->add_tool_action(new ToolbarItem(Translation :: get('History'), Theme :: get_common_image_path() . 'action_versions.png', $parent->get_url(array(PeerAssessmentDisplay :: PARAM_DISPLAY_ACTION => PeerAssessmentDisplay :: ACTION_HISTORY, 'pid' => $pid, 'selected_cloi' => $selected_cloi)), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
            
            if ($this->get_parent()->get_parent()->is_allowed(EDIT_RIGHT))
            {
                $action_bar->add_tool_action(new ToolbarItem(Translation :: get('Statistics'), Theme :: get_common_image_path() . 'action_reporting.png', $parent->get_url(array(PeerAssessmentDisplay :: PARAM_DISPLAY_ACTION => PeerAssessmentDisplay :: ACTION_PAGE_STATISTICS, 'pid' => $pid, 'selected_cloi' => $selected_cloi)), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
            }
        }
        else
        {
            /*  $action_bar->add_common_action(
			new ToolbarItem(
				Translation :: get('Edit'), Theme :: get_common_image_path().'action_edit.png', $parent->get_url(array(PeerAssessmentDisplay :: PARAM_DISPLAY_ACTION => ComplexDisplay :: ACTION_UPDATE_LO, 'pid' => $pid)), ToolbarItem :: DISPLAY_ICON_AND_LABEL
			)
            );

            if(Request :: get('tool') != 'learning_path')
            {
                $action_bar->add_common_action(
                new ToolbarItem(
                    Translation :: get('Delete'),Theme :: get_common_image_path().'action_delete.png', $parent->get_url(array(PeerAssessmentDisplay :: PARAM_DISPLAY_ACTION => ComplexDisplay :: ACTION_DELETE, 'pid' => $pid)), ToolbarItem :: DISPLAY_ICON_AND_LABEL,true
                )
            );*/
            
            //            $action_bar->add_common_action(
            //            new ToolbarItem(
            //                    Translation :: get('BrowsePeerAssessments'), Theme :: get_common_image_path().'action_browser.png', $parent->get_url(array(Tool :: PARAM_ACTION => PeerAssessmentTool :: ACTION_BROWSE_PEER_ASSESSMENTS, PeerAssessmentDisplay :: PARAM_DISPLAY_ACTION => null)), ToolbarItem :: DISPLAY_ICON_AND_LABEL
            //                ));
            //
            //            }
            

            //INFORMATION
            $action_bar->add_tool_action(new ToolbarItem(Translation :: get('PeerAssessmentStatistics'), Theme :: get_common_image_path() . 'action_reporting.png', $parent->get_url(array(PeerAssessmentDisplay :: PARAM_DISPLAY_ACTION => PeerAssessmentDisplay :: ACTION_STATISTICS, 'pid' => $pid)), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
            
            //if($this->get_parent()->get_parent()->is_allowed(EDIT_RIGHT))
            {
                $action_bar->add_tool_action(new ToolbarItem(Translation :: get('AccessDetails'), Theme :: get_common_image_path() . 'action_reporting.png', $parent->get_url(array(PeerAssessmentDisplay :: PARAM_DISPLAY_ACTION => PeerAssessmentDisplay :: ACTION_ACCESS_DETAILS, 'pid' => $pid)), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
            }
            //$action_bar->add_tool_action($parent->get_parent()->get_parent()->get_access_details_toolbar_item($parent));
        }
        
        $links = $lo->get_links(); //RepositoryDataManager :: get_instance()->retrieve_content_object(WebLcmsDataManager :: get_instance()->retrieve_content_object_publication($pid)->get_content_object()->get_id())->get_links();
        

        //NAVIGATION
        if (! empty($links))
        {
            $p = new PeerAssessmentParser($this, $pid, $links);
            $p->set_parent($this);
            $toolboxlinks = $p->handle_toolbox_links($links);
            $links = explode(';', $links);
            $i = 0;
            
            foreach ($toolboxlinks as $link)
            {
                if (substr_count($link, 'www.') == 1)
                {
                    $action_bar->add_navigation_link(new ToolbarItem(ucfirst($p->get_title_from_url($link)), null, $link, ToolbarItem :: DISPLAY_LABEL));
                    continue;
                }
                
                if (substr_count($link, 'class="does_not_exist"'))
                {
                    $action_bar->add_navigation_link(new ToolbarItem($p->get_title_from_peer_assessment_tag($links[$i], true), null, $parent->get_url(array(PeerAssessmentDisplay :: PARAM_DISPLAY_ACTION => PeerAssessmentDisplay :: ACTION_CREATE_PAGE, Tool :: PARAM_PUBLICATION_ID => $p->get_pid_from_url($link), 'title' => $p->get_title_from_peer_assessment_tag($links[$i], false))), ToolbarItem :: DISPLAY_ICON_AND_LABEL, null, 'does_not_exist'));
                }
                else
                {
                    $action_bar->add_navigation_link(new ToolbarItem($p->get_title_from_peer_assessment_tag($links[$i], true), null, $parent->get_url(array(PeerAssessmentDisplay :: PARAM_DISPLAY_ACTION => PeerAssessmentDisplay :: ACTION_VIEW_PEER_ASSESSMENT_PAGE, Tool :: PARAM_PUBLICATION_ID => $p->get_pid_from_url($link), 'selected_cloi' => $p->get_cid_from_url($link))), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
                }
                $i ++;
            }
        }
        
        return $action_bar;
    }

    /*function get_breadcrumbtrail()
    {
        $trail = new BreadcrumbTrail(false);
        $trail->add(new Breadcrumb($this->get_url(array(ComplexDisplay :: PARAM_DISPLAY_ACTION => ComplexDisplay :: ACTION_VIEW_CLO, ComplexDisplay :: PARAM_ROOT_LO => Request :: get('pid'))), $this->get_root_lo()->get_title()));
        switch (Request :: get(ComplexDisplay :: PARAM_DISPLAY_ACTION))
        {
            case ComplexDisplay :: ACTION_VIEW_CLO :
                break;
            case PeerAssessmentDisplay :: ACTION_CREATE_PAGE :
                $trail->add(new Breadcrumb($this->get_url(array(ComplexDisplay :: PARAM_DISPLAY_ACTION => PeerAssessmentDisplay :: ACTION_CREATE_PAGE, ComplexDisplay :: PARAM_ROOT_LO => Request :: get('pid'))), Translation :: get('CreatePeerAssessmentPage')));
                break;
            case PeerAssessmentDisplay :: ACTION_UPDATE_LO :
                $trail->add(new Breadcrumb($this->get_url(array(ComplexDisplay :: PARAM_DISPLAY_ACTION => PeerAssessmentDisplay :: ACTION_UPDATE_LO, ComplexDisplay :: PARAM_ROOT_LO => Request :: get('pid'))), Translation :: get('Edit')));
                break;
            case PeerAssessmentDisplay :: ACTION_VIEW_PEER_ASSESSMENT_PAGE :
                $trail->add(new Breadcrumb($this->get_url(array(ComplexDisplay :: PARAM_DISPLAY_ACTION => PeerAssessmentDisplay :: ACTION_VIEW_PEER_ASSESSMENT_PAGE, ComplexDisplay :: PARAM_ROOT_LO => Request :: get('pid'), ComplexDisplay :: PARAM_SELECTED_CLOI_ID => Request :: get(ComplexDisplay :: PARAM_SELECTED_CLOI_ID))), $this->get_lo_from_cid(Request :: get(ComplexDisplay :: PARAM_SELECTED_CLOI_ID))->get_title()));
                break;
            case PeerAssessmentDisplay :: ACTION_UPDATE :
                $trail->add(new Breadcrumb($this->get_url(array(ComplexDisplay :: PARAM_DISPLAY_ACTION => PeerAssessmentDisplay :: ACTION_VIEW_PEER_ASSESSMENT_PAGE, ComplexDisplay :: PARAM_ROOT_LO => Request :: get('pid'), ComplexDisplay :: PARAM_SELECTED_CLOI_ID => Request :: get(ComplexDisplay :: PARAM_SELECTED_CLOI_ID))), $this->get_lo_from_cid(Request :: get(ComplexDisplay :: PARAM_SELECTED_CLOI_ID))->get_title()));
                $trail->add(new Breadcrumb($this->get_url(array(ComplexDisplay :: PARAM_DISPLAY_ACTION => PeerAssessmentDisplay :: ACTION_UPDATE, ComplexDisplay :: PARAM_ROOT_LO => Request :: get('pid'), ComplexDisplay :: PARAM_SELECTED_CLOI_ID => Request :: get(ComplexDisplay :: PARAM_SELECTED_CLOI_ID))), Translation :: get('Edit')));
                break;

        }
        return $trail;
    }*/

    private function get_lo_from_cid($cid)
    {
        $cloi = RepositoryDataManager :: get_instance()->retrieve_complex_content_object_item($cid);
        return RepositoryDataManager :: get_instance()->retrieve_content_object($cloi->get_ref());
    }

}
?>