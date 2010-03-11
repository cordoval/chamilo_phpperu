<?php
/**
 * $Id: survey_publication_viewer.class.php 193 2009-11-13 11:53:37Z chellee $
 * @package application.lib.survey.survey_manager.component.survey_survey_publisher
 */
require_once dirname(__FILE__) . '/survey_user_table/survey_user_table.class.php';

class SurveyPublicationViewer extends SurveyPublisherComponent
{

    function run()
    {
        $trail = new BreadcrumbTrail();
        $trail->add(new Breadcrumb($this->parent->get_browse_survey_publications_url(), Translation :: get('BrowseSurveyPublications')));
        $trail->add(new Breadcrumb($this->parent->get_url(array(SurveyPublisher :: PUBLISH_ACTION => SurveyPublisher :: ACTION_VIEW, SurveyManager :: PARAM_SURVEY_PUBLICATION => Request :: get(SurveyManager :: PARAM_SURVEY_PUBLICATION))), Translation :: get('ViewInvitedUsers')));
        $toolbar = $this->parent->get_toolbar();
        
        $adm = SurveyDataManager :: get_instance();
        
        $pid = Request :: get(SurveyManager :: PARAM_SURVEY_PUBLICATION);
        $publication = $adm->retrieve_survey_publication($pid);
        $survey = $publication->get_publication_object();
        
        $table = new SurveyUserTable($this, $this->get_user, $pid);
        
        $this->parent->display_header($trail, true, 'courses survey tool');
        echo $toolbar->as_html();
        //echo '<br/><br/>'.Translation :: get('UsersInvitedToTakeSurvey').': <br/>';
        echo '<h4>' . $survey->get_title() . '</h4>';
        echo $table->as_html();
        $this->parent->display_footer();
    }
}
?>