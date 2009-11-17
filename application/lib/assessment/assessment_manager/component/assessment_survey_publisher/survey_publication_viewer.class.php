<?php
/**
 * $Id: survey_publication_viewer.class.php 193 2009-11-13 11:53:37Z chellee $
 * @package application.lib.assessment.assessment_manager.component.assessment_survey_publisher
 */
require_once dirname(__FILE__) . '/survey_user_table/survey_user_table.class.php';

class SurveyPublicationViewer extends SurveyPublisherComponent
{

    function run()
    {
        $trail = new BreadcrumbTrail();
        $trail->add(new Breadcrumb($this->parent->get_browse_assessment_publications_url(), Translation :: get('BrowseAssessmentPublications')));
        $trail->add(new Breadcrumb($this->parent->get_url(array(SurveyPublisher :: PUBLISH_ACTION => SurveyPublisher :: ACTION_VIEW, AssessmentManager :: PARAM_ASSESSMENT_PUBLICATION => Request :: get(AssessmentManager :: PARAM_ASSESSMENT_PUBLICATION))), Translation :: get('ViewInvitedUsers')));
        $toolbar = $this->parent->get_toolbar();
        
        $adm = AssessmentDataManager :: get_instance();
        
        $pid = Request :: get(AssessmentManager :: PARAM_ASSESSMENT_PUBLICATION);
        $publication = $adm->retrieve_assessment_publication($pid);
        $survey = $publication->get_publication_object();
        
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