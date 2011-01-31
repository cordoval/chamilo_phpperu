<?php
use application\phrases\PhrasesPublication;
namespace application\phrases;

use common\libraries\DelegateComponent;
use common\libraries\Request;
use common\libraries\EqualityCondition;
use common\libraries\AndCondition;
use common\libraries\Path;
use common\libraries\BreadcrumbTrail;
use common\libraries\Breadcrumb;
use common\libraries\Translation;
use common\libraries\Display;

use tracking\Tracker;
use tracking\Event;

use repository\ComplexDisplay;
use repository\RepositoryDataManager;

use repository\content_object\assessment\AssessmentComplexDisplaySupport;
use repository\content_object\assessment\FeedbackDisplayConfiguration;

use repository\content_object\adaptive_assessment\AdaptiveAssessmentComplexDisplaySupport;
use repository\content_object\adaptive_assessment\AdaptiveAssessmentContentObjectDisplay;
use repository\content_object\adaptive_assessment\AdaptiveAssessmentDisplay;

/**
 * @author Hans De Bisschop
 * @package application.phrases
 */

class PhrasesManagerViewerComponent extends PhrasesManager implements AdaptiveAssessmentComplexDisplaySupport,
        AssessmentComplexDisplaySupport, DelegateComponent
{
    /**
     * @var PhrasesPublication
     */
    private $publication;

    function run()
    {
        $publication_id = Request :: get(self :: PARAM_PHRASES_PUBLICATION);

        if (! $publication_id)
        {
            $this->redirect(Translation :: get('NoSuchPublication'), true, array(
                    self :: PARAM_ACTION => self :: ACTION_BROWSE_PHRASES_PUBLICATIONS));
        }
        else
        {
            $this->publication = PhrasesDataManager :: get_instance()->retrieve_phrases_publication($publication_id);

            Translation :: get_instance()->set_language($this->publication->get_language());

            if ($this->publication && ! $this->publication->is_visible_for_target_user($this->get_user()))
            {
                $this->not_allowed(null, false);
            }
            else
            {
                ComplexDisplay :: launch($this->get_root_content_object()->get_type(), $this);
            }
        }
    }

    function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $breadcrumbtrail->add_help('phrases_viewer');
        $breadcrumbtrail->add(new Breadcrumb($this->get_url(array(
                PhrasesManager :: PARAM_ACTION => PhrasesManager :: ACTION_BROWSE_PHRASES_PUBLICATIONS)), Translation :: get('PhrasesManagerBrowserComponent')));
    }

    function get_additional_parameters()
    {
        return array(self :: PARAM_PHRASES_PUBLICATION);
    }

    function get_root_content_object()
    {
        $embedded_content_object_id = AdaptiveAssessmentContentObjectDisplay :: get_embedded_content_object_id();

        if ($embedded_content_object_id)
        {
            $this->set_parameter(AdaptiveAssessmentContentObjectDisplay :: PARAM_EMBEDDED_CONTENT_OBJECT_ID, $embedded_content_object_id);
            return RepositoryDataManager :: get_instance()->retrieve_content_object($embedded_content_object_id);
        }
        else
        {
            $this->set_parameter(AdaptiveAssessmentDisplay :: PARAM_ADAPTIVE_ASSESSMENT_ITEM_ID, Request :: get(AdaptiveAssessmentDisplay :: PARAM_ADAPTIVE_ASSESSMENT_ITEM_ID));
            $this->set_parameter(ComplexDisplay :: PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID, Request :: get(ComplexDisplay :: PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID));
            return $this->publication->get_publication_object();
        }
    }

    function display_header()
    {
        $embedded_content_object_id = AdaptiveAssessmentContentObjectDisplay :: get_embedded_content_object_id();

        if ($embedded_content_object_id)
        {
            Display :: small_header();
        }
        else
        {
            parent :: display_header();
        }
    }

    function display_footer()
    {
        $embedded_content_object_id = AdaptiveAssessmentContentObjectDisplay :: get_embedded_content_object_id();

        if ($embedded_content_object_id)
        {
            Display :: small_footer();
        }
        else
        {
            parent :: display_footer();
        }
    }

    function get_publication()
    {
        return $this->publication;
    }

    /**
     * Not implemented right now
     *
     * @param $right
     * @return boolean
     */
    function is_allowed($right)
    {
        return true;
    }

    /**
     * TODO: Save the assessment question answer
     *
     * @param int $complex_question_id
     * @param mixed $answer
     * @param int $score
     */
    function save_assessment_answer($complex_question_id, $answer, $score)
    {
        //        $tracker = $this->retrieve_learning_path_tracker();
        //        $items = $this->retrieve_learning_path_tracker_items($tracker);


        $parameters = array();
        $parameters[PhrasesAdaptiveAssessmentQuestionAttemptsTracker :: PROPERTY_ADAPTIVE_ASSESSMENT_ITEM_ATTEMPT_ID] = $this->get_parameter(AdaptiveAssessmentDisplay :: PARAM_ADAPTIVE_ASSESSMENT_ITEM_ID);
        $parameters[PhrasesAdaptiveAssessmentQuestionAttemptsTracker :: PROPERTY_COMPLEX_QUESTION_ID] = $complex_question_id;
        $parameters[PhrasesAdaptiveAssessmentQuestionAttemptsTracker :: PROPERTY_ANSWER] = $answer;
        $parameters[PhrasesAdaptiveAssessmentQuestionAttemptsTracker :: PROPERTY_SCORE] = $score;
        $parameters[PhrasesAdaptiveAssessmentQuestionAttemptsTracker :: PROPERTY_FEEDBACK] = '';

        Event :: trigger('attempt_question', PhrasesManager :: APPLICATION_NAME, $parameters);
    }

    /**
     * TODO: Save the assessment result
     *
     * @param int $total_score
     */
    function save_assessment_result($total_score)
    {
        $condition = new EqualityCondition(PhrasesAdaptiveAssessmentItemAttemptTracker :: PROPERTY_ID, $this->get_parameter(AdaptiveAssessmentDisplay :: PARAM_ADAPTIVE_ASSESSMENT_ITEM_ID));

        $dummy = new PhrasesAdaptiveAssessmentItemAttemptTracker();
        $trackers = $dummy->retrieve_tracker_items($condition);
        $lpi_tracker = $trackers[0];

        if (! $lpi_tracker)
        {
            return;
        }

        $lpi_tracker->set_score($total_score);
        $lpi_tracker->set_total_time($lpi_tracker->get_total_time() + (time() - $lpi_tracker->get_start_time()));

        $cloi = RepositoryDataManager :: get_instance()->retrieve_complex_content_object_item(Request :: get(ComplexDisplay :: PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID));
        $lp_item = RepositoryDataManager :: get_instance()->retrieve_content_object($cloi->get_ref());
        $mastery_score = $lp_item->get_mastery_score();

        if ($mastery_score)
        {
            $status = ($total_score >= $mastery_score) ? 'passed' : 'failed';
        }
        else
        {
            $status = 'completed';
        }

        $lpi_tracker->set_status($status);
        $lpi_tracker->update();
    }

    /**
     * TODO: Get the current assessment attempt id
     */
    function get_assessment_current_attempt_id()
    {
        return $this->get_parameter(AdaptiveAssessmentDisplay :: PARAM_ADAPTIVE_ASSESSMENT_ITEM_ID);
    }

    function get_assessment_question_attempts()
    {
        $assessment_question_attempt_data = array();

        $condition = new EqualityCondition(PhrasesAdaptiveAssessmentQuestionAttemptsTracker :: PROPERTY_ADAPTIVE_ASSESSMENT_ITEM_ATTEMPT_ID, $this->get_parameter(AdaptiveAssessmentDisplay :: PARAM_ADAPTIVE_ASSESSMENT_ITEM_ID));

        $dummy = new PhrasesAdaptiveAssessmentQuestionAttemptsTracker();
        $trackers = $dummy->retrieve_tracker_items($condition);

        foreach ($trackers as $tracker)
        {
            $assessment_question_attempt_data[$tracker->get_complex_question_id()] = $tracker;
        }

        return $assessment_question_attempt_data;
    }

    function get_assessment_question_attempt($complex_question_id)
    {
        $answers = $this->get_assessment_question_attempts($complex_question_id);
        return $answers[$complex_question_id];
    }

    /**
     * TODO: Get a valid go back url
     *
     * @return string
     */
    function get_assessment_back_url()
    {
        $filter = array();
        $filter[] = AdaptiveAssessmentContentObjectDisplay :: PARAM_EMBEDDED_CONTENT_OBJECT_ID;
        $filter[] = ComplexDisplay :: PARAM_DISPLAY_ACTION;
        $filter[] = AdaptiveAssessmentDisplay :: PARAM_ADAPTIVE_ASSESSMENT_ITEM_ID;
        $filter[] = ComplexDisplay :: PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID;
        $filter[] = self :: PARAM_PHRASES_PUBLICATION;
        $filter[] = AdaptiveAssessmentDisplay :: PARAM_STEP;

        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_BROWSE_PHRASES_PUBLICATIONS), $filter);
    }

    /**
     * TODO: Get a valid go back url
     *
     * @return string
     */
    function get_assessment_continue_url()
    {
        $filter = array();
        $filter[] = AdaptiveAssessmentContentObjectDisplay :: PARAM_EMBEDDED_CONTENT_OBJECT_ID;
        $filter[] = ComplexDisplay :: PARAM_DISPLAY_ACTION;
        $filter[] = AdaptiveAssessmentDisplay :: PARAM_ADAPTIVE_ASSESSMENT_ITEM_ID;
        $filter[] = ComplexDisplay :: PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID;

        $current_step = Request :: get(AdaptiveAssessmentDisplay :: PARAM_STEP);
        return $this->get_url(array(AdaptiveAssessmentDisplay :: PARAM_STEP => $current_step + 1), $filter);
    }

    function get_assessment_feedback_configuration()
    {
        $configuration = new FeedbackDisplayConfiguration();
        $configuration->set_feedback_type(FeedbackDisplayConfiguration :: TYPE_TEXT);
        $configuration->enable_feedback_per_page();
        $configuration->enable_feedback_summary();
        return $configuration;
    }

    function get_assessment_parameters()
    {
        return array(
                AdaptiveAssessmentDisplay :: PARAM_ADAPTIVE_ASSESSMENT_ITEM_ID,
                ComplexDisplay :: PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID,
                AdaptiveAssessmentDisplay :: PARAM_STEP);
    }

    /**
     * TODO: Provide a tracker with actual data
     *
     * @return PhrasesAdaptiveAssessmentAttemptTracker
     */
    function retrieve_adaptive_assessment_tracker()
    {
        $conditions[] = new EqualityCondition(PhrasesAdaptiveAssessmentAttemptTracker :: PROPERTY_ADAPTIVE_ASSESSMENT_ID, $this->get_publication()->get_id());
        $conditions[] = new EqualityCondition(PhrasesAdaptiveAssessmentAttemptTracker :: PROPERTY_USER_ID, $this->get_user_id());
        $condition = new AndCondition($conditions);

        $dummy = new PhrasesAdaptiveAssessmentAttemptTracker();
        $trackers = $dummy->retrieve_tracker_items($condition);
        $adaptive_assessment_tracker = $trackers[0];

        if (! $adaptive_assessment_tracker)
        {
            $parameters = array();
            $parameters[PhrasesAdaptiveAssessmentAttemptTracker :: PROPERTY_USER_ID] = $this->get_user_id();
            $parameters[PhrasesAdaptiveAssessmentAttemptTracker :: PROPERTY_ADAPTIVE_ASSESSMENT_ID] = $this->get_publication()->get_id();
            $parameters[PhrasesAdaptiveAssessmentAttemptTracker :: PROPERTY_PROGRESS] = 0;

            $return = Event :: trigger('attempt_adaptive_assessment', PhrasesManager :: APPLICATION_NAME, $parameters);
            $adaptive_assessment_tracker = $return[0];
        }

        return $adaptive_assessment_tracker;
    }

    /**
     * TODO: Provide a tracker-array with actual data
     *
     * @return array
     */
    function retrieve_adaptive_assessment_tracker_items($adaptive_assessment_tracker)
    {
        $adaptive_assessment_item_attempt_data = array();

        $condition = new EqualityCondition(PhrasesAdaptiveAssessmentItemAttemptTracker :: PROPERTY_ADAPTIVE_ASSESSMENT_VIEW_ID, $adaptive_assessment_tracker->get_id());

        $dummy = new PhrasesAdaptiveAssessmentItemAttemptTracker();
        $trackers = $dummy->retrieve_tracker_items($condition);

        foreach ($trackers as $tracker)
        {
            $item_id = $tracker->get_adaptive_assessment_item_id();

            if (! $adaptive_assessment_item_attempt_data[$item_id])
            {
                $adaptive_assessment_item_attempt_data[$item_id]['score'] = 0;
                $adaptive_assessment_item_attempt_data[$item_id]['time'] = 0;
            }

            $adaptive_assessment_item_attempt_data[$item_id]['trackers'][] = $tracker;
            $adaptive_assessment_item_attempt_data[$item_id]['size'] ++;
            $adaptive_assessment_item_attempt_data[$item_id]['score'] += $tracker->get_score();

            if ($tracker->get_total_time())
            {
                $adaptive_assessment_item_attempt_data[$item_id]['time'] += $tracker->get_total_time();
            }

            if ($tracker->get_status() == 'completed' || $tracker->get_status() == 'passed')
            {
                $adaptive_assessment_item_attempt_data[$item_id]['completed'] = 1;
            }
            else
            {
                $adaptive_assessment_item_attempt_data[$item_id]['active_tracker'] = $tracker;
            }
        }

        return $adaptive_assessment_item_attempt_data;
    }

    /**
     * TODO: Provide an actual & valid url
     *
     * @return string
     */
    function get_adaptive_assessment_tree_menu_url()
    {
        return Path :: get(WEB_PATH) . 'run.php?application=phrases&go=viewer&phrases_publication=' . $this->publication->get_id() . '&' . AdaptiveAssessmentDisplay :: PARAM_STEP . '=%s';
    }

    /**
     * @param int $total_steps
     */
    function get_adaptive_assessment_previous_url($total_steps)
    {
        return $this->get_url(array('step' => $total_steps));
    }

    /**
     * TODO: Provide a tracker with actual data
     * Creates an adaptive assessment item tracker
     *
     * @param AdaptiveAssessmentAttemptTracker $adaptive_assessment_tracker
     * @param ComplexContentObjectItem $current_complex_content_object_item
     * @return array AdaptiveAssessmentItemAttemptTracker
     */
    function create_adaptive_assessment_item_tracker($adaptive_assessment_tracker, $current_complex_content_object_item)
    {
        $parameters = array();
        $parameters[PhrasesAdaptiveAssessmentItemAttemptTracker :: PROPERTY_ADAPTIVE_ASSESSMENT_VIEW_ID] = $adaptive_assessment_tracker->get_id();
        $parameters[PhrasesAdaptiveAssessmentItemAttemptTracker :: PROPERTY_ADAPTIVE_ASSESSMENT_ITEM_ID] = $current_complex_content_object_item->get_id();
        $parameters[PhrasesAdaptiveAssessmentItemAttemptTracker :: PROPERTY_START_TIME] = time();
        $parameters[PhrasesAdaptiveAssessmentItemAttemptTracker :: PROPERTY_TOTAL_TIME] = 0;
        $parameters[PhrasesAdaptiveAssessmentItemAttemptTracker :: PROPERTY_SCORE] = 0;
        $parameters[PhrasesAdaptiveAssessmentItemAttemptTracker :: PROPERTY_MIN_SCORE] = 0;
        $parameters[PhrasesAdaptiveAssessmentItemAttemptTracker :: PROPERTY_MAX_SCORE] = 0;
        $parameters[PhrasesAdaptiveAssessmentItemAttemptTracker :: PROPERTY_STATUS] = 'not attempted';

        $result = Event :: trigger('attempt_adaptive_assessment_item', PhrasesManager :: APPLICATION_NAME, $parameters);
        return $result[0];
    }

    /**
     * TODO: Provide an actual & valid url
     * @param int $complex_content_object_id
     * @return string
     */
    function get_adaptive_assessment_content_object_item_details_url($complex_content_object_id)
    {
        return '';
    }

    /**
     * * TODO: Provide an actual & valid url
     * Get the url of the assessment result
     *
     * @param int $complex_content_object_id
     * @param unknown_type $details
     */
    function get_adaptive_assessment_content_object_assessment_result_url($complex_content_object_id, $details)
    {
        return '';
    }

    /**
     * TODO: Implement reporting and return template name
     * @return string
     */
    function get_adaptive_assessment_attempt_progress_details_reporting_template_name()
    {
        return '';
    }

    /**
     * TODO: Implement reporting and return template name
     * @return string
     */
    function get_adaptive_assessment_attempt_progress_reporting_template_name()
    {
        return '';
    }

    function get_adaptive_assessment_template_application_name()
    {
        return self :: APPLICATION_NAME;
    }
}
?>