<?php
/**
 * $Id: assessment_survey_publisher.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.tool.assessment.component
 */
require_once dirname(__FILE__) . '/assessment_survey_publisher/survey_publisher_component.class.php';

class AssessmentToolSurveyPublisherComponent extends AssessmentToolComponent
{

    function run()
    {
        
        $type = Request :: get(AssessmentTool :: PARAM_PUBLICATION_ACTION);
        $publisher_component = SurveyPublisherComponent :: factory($this, $type);
        
        $publisher_component->run();
    }

    function get_toolbar()
    {
        $toolbar = new ActionbarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);
        
        if ($this->is_allowed(EDIT_RIGHT))
        {
            $toolbar->add_common_action(new ToolbarItem(Translation :: get('ViewInvitedUsers'), Theme :: get_common_image_path() . 'action_visible.png', $this->get_url(array(AssessmentTool :: PARAM_ACTION => AssessmentTool :: ACTION_PUBLISH_SURVEY, AssessmentTool :: PARAM_PUBLICATION_ACTION => AssessmentTool :: ACTION_VIEW, Tool :: PARAM_PUBLICATION_ID => Request :: get(Tool :: PARAM_PUBLICATION_ID))), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
            
            $toolbar->add_common_action(new ToolbarItem(Translation :: get('PublishSurvey'), Theme :: get_common_image_path() . 'action_publish.png', $this->get_url(array(AssessmentTool :: PARAM_ACTION => AssessmentTool :: ACTION_PUBLISH_SURVEY, Tool :: PARAM_PUBLICATION_ID => Request :: get(Tool :: PARAM_PUBLICATION_ID))), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        }
        
        return $toolbar;
    }
}
?>