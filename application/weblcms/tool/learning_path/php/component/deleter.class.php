<?php
namespace application\weblcms\tool\learning_path;

use application\weblcms\WeblcmsDataManager;
use application\weblcms\WeblcmsManager;
use application\weblcms\WeblcmsRights;
use application\weblcms\Tool;
use application\weblcms\WeblcmsLpAttemptTracker;
use common\libraries\EqualityCondition;
use common\libraries\Request;
use common\libraries\Translation;
use common\libraries\WebApplication;

/**
 * $Id: learning_path_deleter.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.tool.learning_path.component
 */
require_once WebApplication :: get_application_class_path(WeblcmsManager :: APPLICATION_NAME) . 'trackers/weblcms_lp_attempt_tracker.class.php';
require_once WebApplication :: get_application_class_path(WeblcmsManager :: APPLICATION_NAME) . 'trackers/weblcms_lpi_attempt_tracker.class.php';
require_once WebApplication :: get_application_class_path(WeblcmsManager :: APPLICATION_NAME) . 'trackers/weblcms_lpi_attempt_objective_tracker.class.php';
require_once WebApplication :: get_application_class_path(WeblcmsManager :: APPLICATION_NAME) . 'trackers/weblcms_learning_path_question_attempts_tracker.class.php';

class LearningPathToolDeleterComponent extends LearningPathTool
{

    function run()
    {
        if ($this->is_allowed(WeblcmsRights :: DELETE_RIGHT) /*&& !WikiTool :: is_wiki_locked(Request :: get(Tool :: PARAM_PUBLICATION_ID))*/)
		{
            if (Request :: get(Tool :: PARAM_PUBLICATION_ID))
                $publication_ids = Request :: get(Tool :: PARAM_PUBLICATION_ID);
            else
                $publication_ids = $_POST[Tool :: PARAM_PUBLICATION_ID];

            if (! is_array($publication_ids))
            {
                $publication_ids = array($publication_ids);
            }

            $datamanager = WeblcmsDataManager :: get_instance();

            foreach ($publication_ids as $pid)
            {
                $publication = $datamanager->retrieve_content_object_publication($pid);

                $condition = new EqualityCondition(WeblcmsLpAttemptTracker :: PROPERTY_LP_ID, $pid);
                $dummy = new WeblcmsLpAttemptTracker();
                $trackers = $dummy->retrieve_tracker_items($condition);
                foreach ($trackers as $tracker)
                    $tracker->delete();

                $publication->delete();
            }
            if (count($publication_ids) > 1)
            {
                $message = htmlentities(Translation :: get('ObjectsDeleted', array('OBJECT' => Translation :: get('LearningPath')) , Utilities :: COMMON_LIBRARIES ));
            }
            else
            {
                $message = htmlentities(Translation :: get('ObjectDeleted', array('OBJECT' => Translation :: get('LearningPath')) , Utilities :: COMMON_LIBRARIES));
            }

            $this->redirect($message, '', array('tool_action' => null, Tool :: PARAM_PUBLICATION_ID => null));
        }
    }

}
?>