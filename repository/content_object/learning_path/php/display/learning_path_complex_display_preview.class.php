<?php
namespace repository\content_object\learning_path;

use common\libraries\Utilities;

use common\libraries\ComplexDisplayPreviewLauncher;
use common\libraries\Path;
use common\libraries\Request;
use common\libraries\Application;
use common\libraries\Translation;
use common\libraries\LauncherApplication;

use repository\ComplexDisplayPreview;
use repository\ComplexDisplay;
use repository\RepositoryManager;
use repository\RepositoryDataManager;

use repository\content_object\assessment\AssessmentComplexDisplaySupport;
use repository\content_object\assessment\FeedbackDisplayConfiguration;

use repository\content_object\learning_path\LearningPathComplexDisplaySupport;
use repository\content_object\forum\ForumComplexDisplaySupport;
use repository\content_object\wiki\WikiComplexDisplaySupport;
use repository\content_object\blog\BlogComplexDisplaySupport;
use repository\content_object\glossary\GlossaryComplexDisplaySupport;

class LearningPathComplexDisplayPreview extends ComplexDisplayPreview implements
        LearningPathComplexDisplaySupport,
        AssessmentComplexDisplaySupport,
        ForumComplexDisplaySupport,
        GlossaryComplexDisplaySupport,
        BlogComplexDisplaySupport,
        WikiComplexDisplaySupport
{

    function run()
    {
        ComplexDisplay :: launch($this->get_root_content_object()->get_type(), $this);
    }

    function display_header()
    {
        LauncherApplication :: display_header();

        $embedded_content_object_id = LearningPathContentObjectDisplay :: get_embedded_content_object_id();

        if (! $embedded_content_object_id)
        {
            $html[] = '<div class="warning-banner">';
            $html[] = Translation :: get('PreviewModeWarning', null, Utilities :: COMMON_LIBRARIES);
            $html[] = '</div>';
            echo implode("\n", $html);
        }
    }

    function get_root_content_object()
    {
        $embedded_content_object_id = LearningPathContentObjectDisplay :: get_embedded_content_object_id();

        if ($embedded_content_object_id)
        {
            $this->set_parameter(LearningPathContentObjectDisplay :: PARAM_EMBEDDED_CONTENT_OBJECT_ID, $embedded_content_object_id);
            return RepositoryDataManager :: get_instance()->retrieve_content_object($embedded_content_object_id);
        }
        else
        {
            $this->set_parameter(LearningPathDisplay :: PARAM_LEARNING_PATH_ITEM_ID, Request :: get(LearningPathDisplay :: PARAM_LEARNING_PATH_ITEM_ID));
            $this->set_parameter(ComplexDisplay :: PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID, Request :: get(ComplexDisplay :: PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID));
            return parent :: get_root_content_object();
        }
    }

    /**
     * Preview mode, so always return true.
     *
     * @param $right
     * @return boolean
     */
    function is_allowed($right)
    {
        return true;
    }

    /**
     * Since this is a preview, no actual view event is triggered.
     *
     * @param $complex_topic_id
     */
    function forum_topic_viewed($complex_topic_id)
    {
    }

    /**
     * Since this is a preview, no views are logged and no count can
     * be retrieved.
     *
     * @param $complex_topic_id
     * @return string
     */
    function forum_count_topic_views($complex_topic_id)
    {
        return '-';
    }

    /**
     * Functionality is publication dependent,
     * so not available in preview mode.
     */
    function get_wiki_page_statistics_reporting_template_name()
    {
        $this->not_available(Translation :: get('ImpossibleInPreviewMode'));
    }

    /**
     * Functionality is publication dependent,
     * so not available in preview mode.
     */
    function get_wiki_statistics_reporting_template_name()
    {
        $this->not_available(Translation :: get('ImpossibleInPreviewMode'));
    }

    /**
     * Functionality is publication dependent,
     * so not available in preview mode.
     */
    function get_publication()
    {
        $this->not_available(Translation :: get('ImpossibleInPreviewMode'));
    }

    /**
     * Preview mode, so no actual saving done.
     *
     * @param int $complex_question_id
     * @param mixed $answer
     * @param int $score
     */
    function save_assessment_answer($complex_question_id, $answer, $score)
    {
    }

    /**
     * Preview mode, so no actual total score will be saved.
     *
     * @param int $total_score
     */
    function save_assessment_result($total_score)
    {
    }

    /**
     * Preview mode, so there is no acrual attempt.
     */
    function get_assessment_current_attempt_id()
    {
    }

    /**
     * Preview mode is launched in standalone mode,
     * so there's nothing to go back to.
     *
     * @return void
     */
    function get_assessment_go_back_url()
    {
    }

    function get_assessment_feedback_configuration()
    {
        return new FeedbackDisplayConfiguration();
    }

    /**
     *
     *
     *
     *
     *
     *
     *
     *
     *
     *
     *
     *
     *
     *
     *
     *
     *
     *
     *
     *
     *
     *
     *
     */

    function retrieve_learning_path_tracker()
    {
        return new DummyLpAttemptTracker();

     //        $conditions[] = new EqualityCondition(WeblcmsLpAttemptTracker :: PROPERTY_COURSE_ID, $this->get_course_id());
    //        $conditions[] = new EqualityCondition(WeblcmsLpAttemptTracker :: PROPERTY_LP_ID, $this->get_publication()->get_id());
    //        $conditions[] = new EqualityCondition(WeblcmsLpAttemptTracker :: PROPERTY_USER_ID, $this->get_user_id());
    //        $condition = new AndCondition($conditions);
    //
    //        $dummy = new WeblcmsLpAttemptTracker();
    //        $trackers = $dummy->retrieve_tracker_items($condition);
    //        $learning_path_tracker = $trackers[0];
    //
    //        if (! $learning_path_tracker)
    //        {
    //            $parameters = array();
    //            $parameters[WeblcmsLpAttemptTracker :: PROPERTY_USER_ID] = $this->get_user_id();
    //            $parameters[WeblcmsLpAttemptTracker :: PROPERTY_COURSE_ID] = $this->get_course_id();
    //            $parameters[WeblcmsLpAttemptTracker :: PROPERTY_LP_ID] = $this->get_publication()->get_id();
    //            $parameters[WeblcmsLpAttemptTracker :: PROPERTY_PROGRESS] = 0;
    //
    //            $return = Event :: trigger('attempt_learning_path', WeblcmsManager :: APPLICATION_NAME, $parameters);
    //            $learning_path_tracker = $return[0];
    //        }
    //
    //        return $learning_path_tracker;
    }

    function retrieve_learning_path_tracker_items($learning_path_tracker)
    {
        //        $learning_path_item_attempt_data = array();
    //
    //        $condition = new EqualityCondition(WeblcmsLpiAttemptTracker :: PROPERTY_LP_VIEW_ID, $learning_path_tracker->get_id());
    //
    //        $dummy = new WeblcmsLpiAttemptTracker();
    //        $trackers = $dummy->retrieve_tracker_items($condition);
    //
    //        foreach ($trackers as $tracker)
    //        {
    //            $item_id = $tracker->get_lp_item_id();
    //
    //            if (! $learning_path_item_attempt_data[$item_id])
    //            {
    //                $learning_path_item_attempt_data[$item_id]['score'] = 0;
    //                $learning_path_item_attempt_data[$item_id]['time'] = 0;
    //            }
    //
    //            $learning_path_item_attempt_data[$item_id]['trackers'][] = $tracker;
    //            $learning_path_item_attempt_data[$item_id]['size'] ++;
    //            $learning_path_item_attempt_data[$item_id]['score'] += $tracker->get_score();
    //
    //            if ($tracker->get_total_time())
    //            {
    //                $learning_path_item_attempt_data[$item_id]['time'] += $tracker->get_total_time();
    //            }
    //
    //            if ($tracker->get_status() == 'completed' || $tracker->get_status() == 'passed')
    //            {
    //                $learning_path_item_attempt_data[$item_id]['completed'] = 1;
    //            }
    //            else
    //            {
    //                $learning_path_item_attempt_data[$item_id]['active_tracker'] = $tracker;
    //            }
    //        }
    //
    //        return $learning_path_item_attempt_data;
    }

    function get_learning_path_tree_menu_url()
    {
        return Path :: get_launcher_application_path(true) . 'index.php?' . Application :: PARAM_APPLICATION . '=' . ComplexDisplayPreviewLauncher :: APPLICATION_NAME . '&' . RepositoryManager :: PARAM_CONTENT_OBJECT_ID . '=' . $this->get_root_content_object()->get_id() . '&' . LearningPathDisplay :: PARAM_STEP . '=%s';
    }

    /**
     * @param int $total_steps
     */
    function get_learning_path_previous_url($total_steps)
    {
        return $this->get_url(array('step' => $total_steps));
    }

    /**
     * Creates a learning path item tracker
     *
     * @param LearningPathAttemptTracker $learning_path_tracker
     * @param ComplexContentObjectItem $current_complex_content_object_item
     * @return array LearningPathItemAttemptTracker
     */
    function create_learning_path_item_tracker($learning_path_tracker, $current_complex_content_object_item)
    {
        $item_tracker = new DummyLpiAttemptTracker();
        $item_tracker->set_lp_item_id($learning_path_tracker->get_id());
        //        $item_tracker->set_lp_view_id($current_complex_content_object_item->get_id());
        //        $item_tracker->set_start_time(time());
        //        $item_tracker->set_total_time(0);
        //        $item_tracker->set_score(0);
        //        $item_tracker->set_min_score(0);
        //        $item_tracker->set_max_score(0);
        //        $item_tracker->set_status('completed');


        return $item_tracker;

     //        $parameters = array();
    //        $parameters[WeblcmsLpiAttemptTracker :: PROPERTY_LP_VIEW_ID] = $learning_path_tracker->get_id();
    //        $parameters[WeblcmsLpiAttemptTracker :: PROPERTY_LP_ITEM_ID] = $current_complex_content_object_item->get_id();
    //        $parameters[WeblcmsLpiAttemptTracker :: PROPERTY_START_TIME] = time();
    //        $parameters[WeblcmsLpiAttemptTracker :: PROPERTY_TOTAL_TIME] = 0;
    //        $parameters[WeblcmsLpiAttemptTracker :: PROPERTY_SCORE] = 0;
    //        $parameters[WeblcmsLpiAttemptTracker :: PROPERTY_MIN_SCORE] = 0;
    //        $parameters[WeblcmsLpiAttemptTracker :: PROPERTY_MAX_SCORE] = 0;
    //        $parameters[WeblcmsLpiAttemptTracker :: PROPERTY_STATUS] = 'not attempted';
    //
    //        $result = Event :: trigger('attempt_learning_path_item', WeblcmsManager :: APPLICATION_NAME, $parameters);
    //        return $result[0];
    }

    /**
     * @param int $complex_content_object_id
     */
    function get_learning_path_content_object_item_details_url($complex_content_object_id)
    {
        //        return $this->get_url(array(
    //                Tool :: PARAM_ACTION => LearningPathTool :: ACTION_DISPLAY_COMPLEX_CONTENT_OBJECT,
    //                Tool :: PARAM_PUBLICATION_ID => $this->publication->get_id(),
    //                LearningPathDisplay :: PARAM_SHOW_PROGRESS => 'true',
    //                ComplexDisplay :: PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID => $complex_content_object_id,
    //                'attempt_id' => Request :: get('attempt_id')));
    }

    /**
     * Get the url of the assessment result
     *
     * @param int $complex_content_object_id
     * @param unknown_type $details
     */
    function get_learning_path_content_object_assessment_result_url($complex_content_object_id, $details)
    {
        //        return $this->get_url(array(
    //                Tool :: PARAM_ACTION => LearningPathTool :: ACTION_DISPLAY_COMPLEX_CONTENT_OBJECT,
    //                Tool :: PARAM_PUBLICATION_ID => $this->publication->get_id(),
    //                LearningPathDisplay :: PARAM_SHOW_PROGRESS => 'true',
    //                ComplexDisplay :: PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID => $complex_content_object_id,
    //                LearningPathDisplay :: PARAM_DETAILS => $details));
    }

    /**
     * @return array
     */
    function get_learning_path_attempt_progress_details_reporting_template_name()
    {
        $this->not_available(Translation :: get('ImpossibleInPreviewMode'));
    }

    /**
     * @return array
     */
    function get_learning_path_attempt_progress_reporting_template_name()
    {
        $this->not_available(Translation :: get('ImpossibleInPreviewMode'));
    }

    function get_learning_path_template_application_name()
    {
        return ComplexDisplayPreviewLauncher :: APPLICATION_NAME;
    }
}
?>