<?php
/**
 * $Id: manager.class.php 201 2009-11-13 12:34:51Z chellee $
 * @package application.personal_calendar.personal_calendar_manager.component
 */

require_once dirname(__FILE__) . '/publication_manager/publication_manager.class.php';
require_once dirname(__FILE__) . '/../../trackers/phrases_assessment_attempts_tracker.class.php';

class PhrasesManagerTakerComponent extends PhrasesManager
{
    private $data_manager;
    private $assessment;
    private $publication_id;
    private $publication;
    private $active_tracker;

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        dump($_POST);
        $this->datamanager = PhrasesDataManager :: get_instance();
        $publication_id = Request :: get(PhrasesPublicationManager :: PARAM_PHRASES_PUBLICATION_ID);
        if ($publication_id)
        {
            $this->publication_id = $publication_id;
            $this->publication = $this->retrieve_phrases_publication($this->publication_id);
            $assessment_id = $this->publication->get_publication_object();
            $this->assessment = $this->publication->get_publication_object();
            $this->set_parameter(PhrasesPublicationManager :: PARAM_PHRASES_PUBLICATION_ID, $this->publication_id);
        }
        
        // Checking statistics
        $track = new PhrasesAssessmentAttemptsTracker();
        $conditions[] = new EqualityCondition(PhrasesAssessmentAttemptsTracker :: PROPERTY_PUBLICATION_ID, $this->publication_id);
        $conditions[] = new EqualityCondition(PhrasesAssessmentAttemptsTracker :: PROPERTY_USER_ID, $this->get_user_id());
        $condition = new AndCondition($conditions);
        $trackers = $track->retrieve_tracker_items($condition);
        
        $count = count($trackers);
        
        foreach ($trackers as $tracker)
        {
            if ($tracker->get_status() == 'not attempted')
            {
                $this->active_tracker = $tracker;
                $count --;
                break;
            }
        }
        
        if ($this->assessment->get_maximum_attempts() != 0 && $count >= $this->assessment->get_maximum_attempts())
        {
            $this->display_header();
            $this->display_error_message(Translation :: get('YouHaveReachedYourMaximumAttempts'));
            $this->display_footer();
            return;
        }
        
        if (! $this->active_tracker)
        {
            $this->active_tracker = $this->create_tracker();
        }
        
        // Executing assessment
        ComplexDisplay :: launch($this->assessment->get_type(), $this);
    }

    function get_current_attempt_id()
    {
        return $this->active_tracker->get_id();
    }

    function get_root_content_object()
    {
        return $this->assessment;
    }

    function create_tracker()
    {
        $arguments = array();
        $arguments[PhrasesAssessmentAttemptsTracker :: PROPERTY_PUBLICATION_ID] = $this->publication_id;
        $arguments[PhrasesAssessmentAttemptsTracker :: PROPERTY_USER_ID] = $this->get_user_id();
        $arguments[PhrasesAssessmentAttemptsTracker :: PROPERTY_TOTAL_SCORE] = 0;
        
        $tracker = Event :: trigger('attempt_assessment', PhrasesManager :: APPLICATION_NAME, $arguments);
        
        return $tracker[0];
    }

    function save_answer($complex_question_id, $answer, $score)
    {
        $parameters = array();
        $parameters['assessment_attempt_id'] = $this->active_tracker->get_id();
        $parameters['complex_question_id'] = $complex_question_id;
        $parameters['answer'] = $answer;
        $parameters['score'] = $score;
        $parameters['feedback'] = '';
        
        Event :: trigger('attempt_question', PhrasesManager :: APPLICATION_NAME, $parameters);
    }

    function finish_assessment($total_score)
    {
        $tracker = $this->active_tracker;
        
        $tracker->set_total_score($total_score);
        $tracker->set_total_time($tracker->get_total_time() + (time() - $tracker->get_start_time()));
        $tracker->set_status('completed');
        $tracker->update();
    }

    function get_go_back_url()
    {
        return $this->get_url(array(PhrasesManager :: PARAM_ACTION => PhrasesManager :: ACTION_VIEW_START, PhrasesPublicationManager :: PARAM_PHRASES_PUBLICATION_ID => null));
    }
}
?>