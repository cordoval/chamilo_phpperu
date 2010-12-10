<?php
namespace application\phrases;

use repository\content_object\assessment;

use common\libraries\Request;
use common\libraries\EqualityCondition;
use common\libraries\AndCondition;
use common\libraries\Path;
use common\libraries\BreadcrumbTrail;
use common\libraries\Breadcrumb;
use common\libraries\Translation;

use tracking\Tracker;
use tracking\Event;

use repository\ComplexDisplay;
use repository\RepositoryDataManager;
use repository\content_object\adaptive_assessment\AdaptiveAssessmentComplexDisplaySupport;
use repository\content_object\assessment\AssessmentComplexDisplaySupport;

/**
 * @author Hans De Bisschop
 * @package application.phrases
 */

class PhrasesManagerViewerComponent extends PhrasesManager implements AdaptiveAssessmentComplexDisplaySupport, AssessmentComplexDisplaySupport
{
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

    public function retrieve_adaptive_assessment_tracker() {
        throw new Exception("Unimplemented method : " . __METHOD__ . " :=> " . __FILE__ . ":" . __LINE__);
    }
    public function retrieve_adaptive_assessment_tracker_items($adaptive_assessment_tracker) {
        throw new Exception("Unimplemented method : " . __METHOD__ . " :=> " . __FILE__ . ":" . __LINE__);
    }
    public function get_adaptive_assessment_tree_menu_url() {
        throw new Exception("Unimplemented method : " . __METHOD__ . " :=> " . __FILE__ . ":" . __LINE__);
    }
    public function create_adaptive_assessment_item_tracker($adaptive_assessment_tracker, $current_complex_content_object_item) {
        throw new Exception("Unimplemented method : " . __METHOD__ . " :=> " . __FILE__ . ":" . __LINE__);
    }

    public function get_adaptive_assessment_attempt_progress_details_reporting_template_name() {
        throw new Exception("Unimplemented method : " . __METHOD__ . " :=> " . __FILE__ . ":" . __LINE__);
    }

    public function get_adaptive_assessment_attempt_progress_reporting_template_name() {
        throw new Exception("Unimplemented method : " . __METHOD__ . " :=> " . __FILE__ . ":" . __LINE__);
    }

    public function get_adaptive_assessment_content_object_assessment_result_url($complex_content_object_id, $details) {
        throw new Exception("Unimplemented method : " . __METHOD__ . " :=> " . __FILE__ . ":" . __LINE__);
    }

    public function get_adaptive_assessment_content_object_item_details_url($complex_content_object_id) {
        throw new Exception("Unimplemented method : " . __METHOD__ . " :=> " . __FILE__ . ":" . __LINE__);
    }

    public function get_adaptive_assessment_previous_url($total_steps) {
        throw new Exception("Unimplemented method : " . __METHOD__ . " :=> " . __FILE__ . ":" . __LINE__);
    }

    public function get_adaptive_assessment_template_application_name() {
        throw new Exception("Unimplemented method : " . __METHOD__ . " :=> " . __FILE__ . ":" . __LINE__);
    }

    public function get_assessment_current_attempt_id() {
        throw new Exception("Unimplemented method : " . __METHOD__ . " :=> " . __FILE__ . ":" . __LINE__);
    }

    public function get_assessment_go_back_url() {
        throw new Exception("Unimplemented method : " . __METHOD__ . " :=> " . __FILE__ . ":" . __LINE__);
    }

    public function save_assessment_answer($complex_question_id, $answer, $score) {
        throw new Exception("Unimplemented method : " . __METHOD__ . " :=> " . __FILE__ . ":" . __LINE__);
    }

    public function save_assessment_result($total_score) {
        throw new Exception("Unimplemented method : " . __METHOD__ . " :=> " . __FILE__ . ":" . __LINE__);
    }

    public function get_root_content_object() {
        throw new Exception("Unimplemented method : " . __METHOD__ . " :=> " . __FILE__ . ":" . __LINE__);
    }

    public function is_allowed($right) {
        throw new Exception("Unimplemented method : " . __METHOD__ . " :=> " . __FILE__ . ":" . __LINE__);
    }

}