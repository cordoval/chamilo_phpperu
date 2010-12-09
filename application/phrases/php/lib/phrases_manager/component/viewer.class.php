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
 * $Id: viewer.class.php 193 2009-11-13 11:53:37Z chellee $
 * @package application.lib.phrases.phrases_manager.component
 */

require_once WebApplication :: get_application_class_path(PhrasesManager :: APPLICATION_NAME) . 'trackers/phrases_adaptive_assessment_attempt_tracker.class.php';
require_once WebApplication :: get_application_class_path(PhrasesManager :: APPLICATION_NAME) . 'trackers/phrases_adaptive_assessment_item_attempt_tracker.class.php';
require_once WebApplication :: get_application_class_path(PhrasesManager :: APPLICATION_NAME) . 'trackers/phrases_adaptive_assessment_question_attempts_tracker.class.php';

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
}
?>