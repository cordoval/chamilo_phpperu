<?php
namespace repository\content_object\adaptive_assessment;

use common\libraries\ComplexDisplayPreviewLauncher;
use common\libraries\Path;
use common\libraries\Request;
use common\libraries\Application;
use common\libraries\Translation;

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

class AdaptiveAssessmentComplexDisplayPreview extends ComplexDisplayPreview implements AdaptiveAssessmentComplexDisplaySupport, AssessmentComplexDisplaySupport
{

    /* (non-PHPdoc)
     * @see repository.ComplexDisplayPreview::run()
     */
    function run()
    {
        ComplexDisplay :: launch($this->get_root_content_object()->get_type(), $this);
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
    }

    /* (non-PHPdoc)
     * @see repository\content_object\assessment.AssessmentComplexDisplaySupport::save_assessment_result()
     */
    function save_assessment_result($total_score)
    {
    }

    /* (non-PHPdoc)
     * @see repository\content_object\assessment.AssessmentComplexDisplaySupport::get_assessment_current_attempt_id()
     */
    function get_assessment_current_attempt_id()
    {
    }

    /* (non-PHPdoc)
     * @see repository\content_object\assessment.AssessmentComplexDisplaySupport::get_assessment_go_back_url()
     */
    function get_assessment_go_back_url()
    {
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

    /* (non-PHPdoc)
     * @see repository\content_object\assessment.AssessmentComplexDisplaySupport::get_assessment_feedback_configuration()
     */
    function get_assessment_feedback_configuration()
    {
        return new FeedbackDisplayConfiguration();
    }
}
?>