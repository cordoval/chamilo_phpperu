<?php
namespace application\weblcms;

use repository\content_object\learning_path;

use common\libraries\Request;
use reporting\ReportingTemplate;
use application\weblcms\tool\learning_path\LearningPathTool;
use repository\content_object\learning_path\LearningPathDisplay;

/**
 * $Id: learning_path_attempt_progress_reporting_template.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.reporting.templates
 */
/**
 * @author Michael Kyndt
 */
require_once dirname(__FILE__) . '/../blocks/weblcms_learning_path_attempt_progress_reporting_block.class.php';

class LearningPathAttemptProgressReportingTemplate extends ReportingTemplate
{
    private $object;

    function __construct($parent)
    {
        parent :: __construct($parent);
        $this->add_reporting_block($this->get_learning_path_progress());
    }

    function display_context()
    {
        //publicatie, content_object, application ...
    }

    function get_application()
    {
        return WeblcmsManager :: APPLICATION_NAME;
    }

    function get_learning_path_progress()
    {
        $course_weblcms_block = new WeblcmsLearningPathAttemptProgressReportingBlock($this);
        $course_id = Request :: get(WeblcmsManager :: PARAM_COURSE);
        if ($course_id)
        {
            $this->set_parameter(WeblcmsManager :: PARAM_COURSE, $course_id);
        }

        $tool = Request :: get(WeblcmsManager :: PARAM_TOOL);
        $this->set_parameter(WeblcmsManager :: PARAM_TOOL, $tool);

        $user_id = Request :: get(WeblcmsManager :: PARAM_USERS);
        $this->set_parameter(WeblcmsManager :: PARAM_USERS, $user_id);

        $attempt_id = Request :: get(LearningPathTool :: PARAM_ATTEMPT_ID);
        if ($attempt_id)
        {
            $this->set_parameter(LearningPathTool :: PARAM_ATTEMPT_ID, $attempt_id);
        }
        else
        {
            $this->set_parameter(LearningPathDisplay :: PARAM_SHOW_PROGRESS, 'true');
        }

        $pid = Request :: get(Tool :: PARAM_PUBLICATION_ID);
        $this->set_parameter(Tool :: PARAM_PUBLICATION_ID, $pid);

        return $course_weblcms_block;
    }
}
?>