<?php
namespace repository\content_object\adaptive_assessment;

use repository\ComplexContentObjectPath;

use common\libraries\Session;

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

use repository\content_object\adaptive_assessment\AdaptiveAssessmentComplexDisplaySupport;
use repository\content_object\assessment\AssessmentComplexDisplaySupport;
use repository\content_object\assessment\FeedbackDisplayConfiguration;

/**
 * @author Hans De Bisschop
 * @package repository.content_object.adaptive_assessment
 */

class AdaptiveAssessmentComplexDisplayPreview extends ComplexDisplayPreview implements
        AdaptiveAssessmentComplexDisplaySupport,
        AssessmentComplexDisplaySupport
{
    const TEMPORARY_STORAGE = 'adaptive_assessment_preview';

    /* (non-PHPdoc)
     * @see repository.ComplexDisplayPreview::run()
     */
    function run()
    {
        //$path = $this->get_root_content_object()->get_complex_content_object_path();
        //var_dump($path);
        ComplexDisplay :: launch($this->get_root_content_object()->get_type(), $this);
    }

    function display_header()
    {
        LauncherApplication :: display_header();

        $embedded_content_object_id = AdaptiveAssessmentContentObjectDisplay :: get_embedded_content_object_id();

        if (! $embedded_content_object_id)
        {
            $html[] = '<div class="warning-banner">';
            $html[] = Translation :: get('PreviewModeWarning', null, Utilities :: COMMON_LIBRARIES);
            $html[] = '</div>';
            echo implode("\n", $html);
        }
    }

    /* (non-PHPdoc)
     * @see repository.ComplexDisplayPreview::get_root_content_object()
     */
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
            return parent :: get_root_content_object();
        }
    }

    /* (non-PHPdoc)
     * @see repository.ComplexDisplaySupport::is_allowed()
     */
    function is_allowed($right)
    {
        return true;
    }

    /* (non-PHPdoc)
     * @see repository\content_object\assessment.AssessmentComplexDisplaySupport::save_assessment_answer()
     */
    function save_assessment_answer($complex_question_id, $answer, $score)
    {
        $parameters = array();
        $parameters[DummyQuestionAttemptsTracker :: PROPERTY_ASSESSMENT_ATTEMPT_ID] = $this->get_root_content_object()->get_id();
        $parameters[DummyQuestionAttemptsTracker :: PROPERTY_QUESTION_CID] = $complex_question_id;
        $parameters[DummyQuestionAttemptsTracker :: PROPERTY_ANSWER] = $answer;
        $parameters[DummyQuestionAttemptsTracker :: PROPERTY_SCORE] = $score;
        $parameters[DummyQuestionAttemptsTracker :: PROPERTY_FEEDBACK] = '';

        $answers = Session :: retrieve(self :: TEMPORARY_STORAGE);
        $answers[$this->get_root_content_object()->get_id()][$complex_question_id] = new DummyQuestionAttemptsTracker($parameters);
        Session :: register(self :: TEMPORARY_STORAGE, $answers);
    }

    /* (non-PHPdoc)
     * @see repository\content_object\assessment.AssessmentComplexDisplaySupport::save_assessment_result()
     */
    function save_assessment_result($total_score)
    {
        $answers = Session :: retrieve(self :: TEMPORARY_STORAGE);
        unset($answers[$this->get_root_content_object()->get_id()]);
        Session :: register(self :: TEMPORARY_STORAGE, $answers);
    }

    /* (non-PHPdoc)
     * @see repository\content_object\assessment.AssessmentComplexDisplaySupport::get_assessment_current_attempt_id()
     */
    function get_assessment_current_attempt_id()
    {
    }

    /* (non-PHPdoc)
     * @see repository\content_object\assessment.AssessmentComplexDisplaySupport::get_assessment_question_attempts()
     */
    function get_assessment_question_attempts()
    {
        $answers = Session :: retrieve(self :: TEMPORARY_STORAGE);
        return $answers[$this->get_root_content_object()->get_id()];
    }

    /* (non-PHPdoc)
     * @see repository\content_object\assessment.AssessmentComplexDisplaySupport::get_assessment_question_attempt()
     */
    function get_assessment_question_attempt($complex_question_id)
    {
        $answers = $this->get_assessment_question_attempts($complex_question_id);
        return $answers[$complex_question_id];
    }

    /**
     * Preview mode is launched in standalone mode,
     * so just close the window.
     *
     * @return void
     */
    function get_assessment_back_url()
    {
        return 'javascript: self.close()';
    }

    /**
     * Preview mode is launched in standalone mode,
     * so there's nothing to continue to.
     *
     * @return void
     */
    function get_assessment_continue_url()
    {
        $filter = array();
        $filter[] = AdaptiveAssessmentContentObjectDisplay :: PARAM_EMBEDDED_CONTENT_OBJECT_ID;
        $filter[] = ComplexDisplay :: PARAM_DISPLAY_ACTION;
        $filter[] = AdaptiveAssessmentDisplay :: PARAM_ADAPTIVE_ASSESSMENT_ITEM_ID;
        $filter[] = ComplexDisplay :: PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID;
        //
        $current_step = Request :: get(AdaptiveAssessmentDisplay :: PARAM_STEP);
        return $this->get_url(array(AdaptiveAssessmentDisplay :: PARAM_STEP => $current_step + 1), $filter);
    }

    function get_assessment_feedback_configuration()
    {
        $dummy_configuration = new FeedbackDisplayConfiguration();
        $dummy_configuration->set_feedback_type(FeedbackDisplayConfiguration :: TYPE_TEXT);
        $dummy_configuration->enable_feedback_per_page();
        $dummy_configuration->enable_feedback_summary();
        //$dummy_configuration->disable_feedback_summary();
        return $dummy_configuration;
    }

    function get_assessment_parameters()
    {
        return array(AdaptiveAssessmentDisplay :: PARAM_ADAPTIVE_ASSESSMENT_ITEM_ID,
                ComplexDisplay :: PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID, AdaptiveAssessmentDisplay :: PARAM_STEP);
    }

    /* (non-PHPdoc)
     * @see repository\content_object\adaptive_assessment.AdaptiveAssessmentComplexDisplaySupport::retrieve_adaptive_assessment_tracker()
     */
    function retrieve_adaptive_assessment_tracker()
    {
        return new DummyAdaptiveAssessmentAttemptTracker();
    }

    /* (non-PHPdoc)
     * @see repository\content_object\adaptive_assessment.AdaptiveAssessmentComplexDisplaySupport::retrieve_adaptive_assessment_tracker_items()
     */
    function retrieve_adaptive_assessment_tracker_items($adaptive_assessment_tracker)
    {
    }

    /* (non-PHPdoc)
     * @see repository\content_object\adaptive_assessment.AdaptiveAssessmentComplexDisplaySupport::get_adaptive_assessment_tree_menu_url()
     */
    function get_adaptive_assessment_tree_menu_url()
    {
        return Path :: get_launcher_application_path(true) . 'index.php?' . Application :: PARAM_APPLICATION . '=' . ComplexDisplayPreviewLauncher :: APPLICATION_NAME . '&' . RepositoryManager :: PARAM_CONTENT_OBJECT_ID . '=' . $this->get_root_content_object()->get_id() . '&' . AdaptiveAssessmentDisplay :: PARAM_STEP . '=%s';
    }

    /* (non-PHPdoc)
     * @see repository\content_object\adaptive_assessment.AdaptiveAssessmentComplexDisplaySupport::get_adaptive_assessment_previous_url()
     */
    function get_adaptive_assessment_previous_url($total_steps)
    {
        return $this->get_url(array('step' => $total_steps));
    }

    /* (non-PHPdoc)
     * @see repository\content_object\adaptive_assessment.AdaptiveAssessmentComplexDisplaySupport::create_adaptive_assessment_item_tracker()
     */
    function create_adaptive_assessment_item_tracker($adaptive_assessment_tracker, $current_complex_content_object_item)
    {
        $item_tracker = new DummyAdaptiveAssessmentItemAttemptTracker();
        $item_tracker->set_adaptive_assessment_item_id($adaptive_assessment_tracker->get_id());
        return $item_tracker;
    }

    /* (non-PHPdoc)
     * @see repository\content_object\adaptive_assessment.AdaptiveAssessmentComplexDisplaySupport::get_adaptive_assessment_content_object_item_details_url()
     */
    function get_adaptive_assessment_content_object_item_details_url($complex_content_object_id)
    {
    }

    /* (non-PHPdoc)
     * @see repository\content_object\adaptive_assessment.AdaptiveAssessmentComplexDisplaySupport::get_adaptive_assessment_content_object_assessment_result_url()
     */
    function get_adaptive_assessment_content_object_assessment_result_url($complex_content_object_id, $details)
    {
    }

    /* (non-PHPdoc)
     * @see repository\content_object\adaptive_assessment.AdaptiveAssessmentComplexDisplaySupport::get_adaptive_assessment_attempt_progress_details_reporting_template_name()
     */
    function get_adaptive_assessment_attempt_progress_details_reporting_template_name()
    {
        $this->not_available(Translation :: get('ImpossibleInPreviewMode'));
    }

    /* (non-PHPdoc)
     * @see repository\content_object\adaptive_assessment.AdaptiveAssessmentComplexDisplaySupport::get_adaptive_assessment_attempt_progress_reporting_template_name()
     */
    function get_adaptive_assessment_attempt_progress_reporting_template_name()
    {
        $this->not_available(Translation :: get('ImpossibleInPreviewMode'));
    }

    /* (non-PHPdoc)
     * @see repository\content_object\adaptive_assessment.AdaptiveAssessmentComplexDisplaySupport::get_adaptive_assessment_template_application_name()
     */
    function get_adaptive_assessment_template_application_name()
    {
        return ComplexDisplayPreviewLauncher :: APPLICATION_NAME;
    }
}
?>