<?php
namespace application\survey;


use common\libraries\Path;
use repository\content_object\survey\SurveyDisplaySurveyViewerComponent;
use repository\content_object\survey\Survey;
use repository\RepositoryDataManager;
use common\libraries\Breadcrumb;
use common\libraries\Translation;
use common\libraries\Theme;
use common\libraries\Request;
use repository\ComplexDisplay;
use common\libraries\EqualityCondition;
use common\libraries\AndCondition;
use tracking\Tracker;
use tracking\Event;
use common\libraries\Display;

//require_once Path :: get_application_path() . 'lib/survey/trackers/survey_participant_tracker.class.php';
require_once Path :: get_repository_content_object_path() . '/survey/php/display/component/viewer/survey_viewer_wizard.class.php';

class SurveyManagerViewerComponent extends SurveyManagerTakerComponent
{
    private $survey_id;
    private $publication_id;
    private $invitee_id;

    /**
     * @var SurveyParticipantTracker
     */
    private $participant_tracker;

    function run()
    {

        $this->survey_id = Request :: get(SurveyDisplaySurveyViewerComponent :: PARAM_SURVEY_ID);

        $this->publication_id = Request :: get(SurveyManager :: PARAM_PUBLICATION_ID);

        $this->invitee_id = Request :: get(SurveyDisplaySurveyViewerComponent :: PARAM_INVITEE_ID);

        if (! SurveyRights :: is_allowed_in_surveys_subtree(SurveyRights :: RIGHT_PARTICIPATE, $this->publication_id, SurveyRights :: TYPE_PUBLICATION, $this->invitee_id))
        {
            Display :: not_allowed();
        }

        $publication = SurveyDataManager :: get_instance()->retrieve_survey_publication($this->publication_id);

        if (! $publication->is_publication_period())
        {
            $this->redirect(Translation :: get('NotInPublicationPeriod'), (false), array(
                    self :: PARAM_ACTION => self :: ACTION_BROWSE));
        }

        ComplexDisplay :: launch(Survey :: get_type_name(), $this, false);
    }

    function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $breadcrumbtrail->add(new Breadcrumb($this->get_url(array(self :: PARAM_ACTION => self :: ACTION_BROWSE)), Translation :: get('BrowseSurveys')));
    }

    function get_additional_parameters()
    {
        return array(
                self :: PARAM_PUBLICATION_ID,
                SurveyDisplaySurveyViewerComponent :: PARAM_SURVEY_ID,
                SurveyDisplaySurveyViewerComponent :: PARAM_INVITEE_ID,
                SurveyDisplaySurveyViewerComponent :: PARAM_CONTEXT_PATH);
    }

    //try out for interface SurveyTaker


    function started()
    {
        $conditions[] = new EqualityCondition(SurveyParticipantTracker :: PROPERTY_SURVEY_PUBLICATION_ID, $this->publication_id);
        $conditions[] = new EqualityCondition(SurveyParticipantTracker :: PROPERTY_USER_ID, $this->invitee_id);
        $condition = new AndCondition($conditions);

        $tracker_count = Tracker :: count_data(SurveyParticipantTracker :: CLASS_NAME, SurveyManager :: APPLICATION_NAME, $condition);

        if ($tracker_count == 0)
        {

            $args = array();
            $args[SurveyParticipantTracker :: PROPERTY_SURVEY_PUBLICATION_ID] = $this->publication_id;
            $args[SurveyParticipantTracker :: PROPERTY_USER_ID] = $this->invitee_id;
            $args[SurveyParticipantTracker :: PROPERTY_START_TIME] = time();
            $args[SurveyParticipantTracker :: PROPERTY_STATUS] = SurveyParticipantTracker :: STATUS_STARTED;
            $args[SurveyParticipantTracker :: PROPERTY_CONTEXT_TEMPLATE_ID] = 0;
            $args[SurveyParticipantTracker :: PROPERTY_PARENT_ID] = 0;
            $args[SurveyParticipantTracker :: PROPERTY_CONTEXT_ID] = 0;
            $args[SurveyParticipantTracker :: PROPERTY_CONTEXT_NAME] = 'NOCONTEXT';
            $trackers = Event :: trigger(SurveyParticipantTracker :: CREATE_PARTICIPANT_EVENT, SurveyManager :: APPLICATION_NAME, $args);
            $this->participant_tracker = $trackers[0];
        }
        else
        {
            $this->participant_tracker = Tracker :: get_data(SurveyParticipantTracker :: CLASS_NAME, SurveyManager :: APPLICATION_NAME, $condition, 0, 1)->next_result();
        }
    }

    function finished()
    {

     //        $condition = new EqualityCondition(SurveyQuestionAnswerTracker :: PROPERTY_SURVEY_PARTICIPANT_ID, $this->participant_tracker->get_id());
    //
    //        $answer_count = Tracker :: count_data(SurveyQuestionAnswerTracker :: get_table_name(), SurveyManager :: APPLICATION_NAME, $condition);
    //
    //        $survey = RepositoryDataManager :: get_instance()->retrieve_content_object($this->survey_id);
    //        $survey->initialize($this->invitee_id);
    //        $question_count = count($survey->get_question_context_paths());
    //
    //        $progress = $answer_count / $question_count * 100;
    //
    //        $this->participant_tracker->set_progress($progress);
    //        $this->participant_tracker->set_status(SurveyParticipantTracker :: STATUS_FINISHED);
    //        $this->participant_tracker->set_total_time(time());
    //        $this->participant_tracker->update();
    }

    function save_answer($complex_question_id, $answer, $context_path)
    {
        //        $conditions[] = new EqualityCondition(SurveyQuestionAnswerTracker :: PROPERTY_SURVEY_PARTICIPANT_ID, $this->participant_tracker->get_id());
    //        $conditions[] = new EqualityCondition(SurveyQuestionAnswerTracker :: PROPERTY_COMPLEX_QUESTION_ID, $complex_question_id);
    //        $conditions[] = new EqualityCondition(SurveyQuestionAnswerTracker :: PROPERTY_CONTEXT_PATH, $context_path);
    //        $condition = new AndCondition($conditions);
    //        $tracker = $trackers = Tracker :: get_data(SurveyQuestionAnswerTracker :: CLASS_NAME, SurveyManager :: APPLICATION_NAME, $condition, 0, 1)->next_result();
    //
    //        if ($tracker)
    //        {
    //            $tracker->set_answer($answer);
    //            $tracker->update();
    //        }
    //        else
    //        {
    //            $parameters = array();
    //            $parameters[SurveyQuestionAnswerTracker :: PROPERTY_SURVEY_PARTICIPANT_ID] = $this->participant_tracker->get_id();
    //            $parameters[SurveyQuestionAnswerTracker :: PROPERTY_COMPLEX_QUESTION_ID] = $complex_question_id;
    //            $parameters[SurveyQuestionAnswerTracker :: PROPERTY_ANSWER] = $answer;
    //            $parameters[SurveyQuestionAnswerTracker :: PROPERTY_CONTEXT_PATH] = $context_path;
    //            $parameters[SurveyQuestionAnswerTracker :: PROPERTY_PUBLICATION_ID] = $this->publication_id;
    //            $parameters[SurveyQuestionAnswerTracker :: PROPERTY_USER_ID] = $this->invitee_id;
    //
    //            $survey = RepositoryDataManager :: get_instance()->retrieve_content_object($this->survey_id);
    //
    //            if ($survey->has_context())
    //            {
    //                $level_count = $survey->count_levels();
    //                $path_ids = explode('|', $context_path);
    //                $context_ids = explode('_', $path_ids[1]);
    //                $context_count = count($context_ids);
    //                $context_template = $survey->get_context_template($context_count);
    //                $parameters[SurveyQuestionAnswerTracker :: PROPERTY_CONTEXT_TEMPLATE_ID] = $context_template->get_id();
    //                $parameters[SurveyQuestionAnswerTracker :: PROPERTY_CONTEXT_ID] = array_pop($context_ids);
    //
    //            }
    //            else
    //            {
    //                $parameters[SurveyQuestionAnswerTracker :: PROPERTY_CONTEXT_ID] = 0;
    //                $parameters[SurveyQuestionAnswerTracker :: PROPERTY_CONTEXT_TEMPLATE_ID] = 0;
    //            }
    //
    //            Event :: trigger(SurveyQuestionAnswerTracker :: SAVE_QUESTION_ANSWER_EVENT, SurveyManager :: APPLICATION_NAME, $parameters);
    //        }
    }

    function get_answer($complex_question_id, $context_path)
    {

        $conditions[] = new EqualityCondition(SurveyQuestionAnswerTracker :: PROPERTY_SURVEY_PARTICIPANT_ID, $this->participant_tracker->get_id());
        $conditions[] = new EqualityCondition(SurveyQuestionAnswerTracker :: PROPERTY_COMPLEX_QUESTION_ID, $complex_question_id);
        $conditions[] = new EqualityCondition(SurveyQuestionAnswerTracker :: PROPERTY_CONTEXT_PATH, $context_path);
        $condition = new AndCondition($conditions);

        $tracker = $trackers = Tracker :: get_data(SurveyQuestionAnswerTracker :: CLASS_NAME, SurveyManager :: APPLICATION_NAME, $condition, 0, 1)->next_result();

        if ($tracker)
        {
            return $tracker->get_answer();
        }
        else
        {
            return null;
        }
    }

    function get_go_back_url()
    {
        return $this->get_browse_survey_publications_url();
    }
}

?>