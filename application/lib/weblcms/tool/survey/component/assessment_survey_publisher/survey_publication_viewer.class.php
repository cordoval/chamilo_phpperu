<?php
/**
 * $Id: survey_publication_viewer.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.tool.assessment.component.assessment_survey_publisher
 */
require_once dirname(__FILE__) . '/survey_user_table/survey_user_table.class.php';

class SurveyPublicationViewer extends SurveyPublisherComponent
{

    function run()
    {
        if (! $this->parent->is_allowed(EDIT_RIGHT))
        {
            Display :: display_not_allowed();
            return;
        }
        $trail = new BreadcrumbTrail();
        $trail->add(new Breadcrumb($this->parent->get_url(array(AssessmentTool :: PARAM_ACTION => AssessmentTool :: ACTION_PUBLISH_SURVEY, AssessmentTool :: PARAM_PUBLICATION_ACTION => AssessmentTool :: ACTION_VIEW, Tool :: PARAM_PUBLICATION_ID => Request :: get(Tool :: PARAM_PUBLICATION_ID))), Translation :: get('ViewInvitedUsers')));
        $toolbar = $this->parent->get_toolbar();
        
        $wdm = WeblcmsDataManager :: get_instance();
        
        $pid = Request :: get(Tool :: PARAM_PUBLICATION_ID);
        $publication = $wdm->retrieve_content_object_publication($pid);
        $survey = $publication->get_content_object();
        
        $table = new SurveyUserTable($this, $this->get_user, $pid);
        
        $this->parent->display_header($trail, true, 'courses assessment tool');
        echo $toolbar->as_html();
        //echo '<br/><br/>'.Translation :: get('UsersInvitedToTakeSurvey').': <br/>';
        echo '<h4>' . $survey->get_title() . '</h4>';
        echo $table->as_html();
        $this->parent->display_footer();
    }
}
?>