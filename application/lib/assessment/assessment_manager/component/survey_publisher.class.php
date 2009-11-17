<?php
/**
 * $Id: survey_publisher.class.php 193 2009-11-13 11:53:37Z chellee $
 * @package application.lib.assessment.assessment_manager.component
 */
require_once dirname(__FILE__) . '/assessment_survey_publisher/survey_publisher_component.class.php';

class AssessmentManagerSurveyPublisherComponent extends AssessmentManagerComponent
{

    function run()
    {
        $type = Request :: get(SurveyPublisher :: PUBLISH_ACTION);
        $publisher_component = SurveyPublisherComponent :: factory($this, $type);
        
        $publisher_component->run();
    }

    function get_toolbar()
    {
        $toolbar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);
        
        $toolbar->add_common_action(new ToolbarItem(Translation :: get('ViewInvitedUsers'), Theme :: get_common_image_path() . 'action_visible.png', $this->get_url(array(AssessmentManager :: PARAM_ACTION => AssessmentManager :: ACTION_PUBLISH_SURVEY, SurveyPublisher :: PUBLISH_ACTION => SurveyPublisher :: ACTION_VIEW, AssessmentManager :: PARAM_ASSESSMENT_PUBLICATION => Request :: get(AssessmentManager :: PARAM_ASSESSMENT_PUBLICATION))), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        
        $toolbar->add_common_action(new ToolbarItem(Translation :: get('PublishSurvey'), Theme :: get_common_image_path() . 'action_publish.png', $this->get_url(array(AssessmentManager :: PARAM_ACTION => AssessmentManager :: ACTION_PUBLISH_SURVEY, AssessmentManager :: PARAM_ASSESSMENT_PUBLICATION => Request :: get(AssessmentManager :: PARAM_ASSESSMENT_PUBLICATION))), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        
        return $toolbar;
    }
}
?>