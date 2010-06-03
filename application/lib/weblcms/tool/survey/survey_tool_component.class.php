<?php
/**
 * $Id: survey_tool_component.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.tool.assessment
 */
require_once Path :: get_application_path() . 'lib/weblcms/tool/tool_component.class.php';
/**
 * The base class for all assessment tool components.
 *
 */
class SurveyToolComponent extends ToolComponent
{

    /**
     * Inherited
     *
     * @param unknown_type $component_name
     * @param unknown_type $assessment_tool
     * @return unknown
     */
    static function factory($component_name, $assessment_tool)
    {
        return parent :: factory('Survey', $component_name, $assessment_tool);
    }

    function get_toolbar($search = false)
    {
        $action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);
        
        //public functions
        if ($search)
        {
            $action_bar->set_search_url($this->get_url());
        }
        
        if ($this->is_allowed(ADD_RIGHT))
        {
            $action_bar->add_common_action(new ToolbarItem(Translation :: get('Publish'), Theme :: get_common_image_path() . 'action_publish.png', $this->get_url(array(SurveyTool :: PARAM_ACTION => SurveyTool :: ACTION_PUBLISH)), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        }
        
        $action_bar->add_common_action(new ToolbarItem(Translation :: get('Browse'), Theme :: get_common_image_path() . 'action_browser.png', $this->get_url(array(SurveyTool :: PARAM_ACTION => SurveyTool :: ACTION_VIEW_SURVEY)), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        
        //results
        if ($this->is_allowed(EDIT_RIGHT))
        {
            $action_name = Translation :: get('ViewResultsSummary');
        }
        else
        {
            $action_name = Translation :: get('ViewResults');
        }
        $action_bar->add_tool_action(new ToolbarItem($action_name, Theme :: get_common_image_path() . 'action_view_results.png', $this->get_url(array(SurveyTool :: PARAM_ACTION => SurveyTool :: ACTION_VIEW_RESULTS)), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        
        //admin only functions
        if ($this->is_allowed(EDIT_RIGHT))
        {
            $action_bar->add_tool_action(new ToolbarItem(Translation :: get('ImportQti'), Theme :: get_common_image_path() . 'action_import.png', $this->get_url(array(SurveyTool :: PARAM_ACTION => SurveyTool :: ACTION_IMPORT_QTI)), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        }
        
        return $action_bar;
    }

}

?>