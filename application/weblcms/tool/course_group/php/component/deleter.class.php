<?php
namespace application\weblcms\tool\course_group;

use application\weblcms\CourseGroup;
use application\weblcms\WeblcmsDataManager;
use application\weblcms\WeblcmsRights;
use common\libraries\Display;
use common\libraries\Request;
use common\libraries\Translation;
use common\libraries\Utilities;
use application\weblcms\Tool;
use common\libraries\EqualityCondition;

/**
 * $Id: course_group_deleter.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.tool.course_group.component
 */
require_once dirname(__FILE__) . '/../course_group_tool.class.php';
//require_once dirname(__FILE__) . '/../course_group_tool_component.class.php';

class CourseGroupToolDeleterComponent extends CourseGroupTool
{

    function run()
    {
        if (! $this->is_allowed(WeblcmsRights :: DELETE_RIGHT))
        {
            Display :: not_allowed();
            return;
        }

        if (Request :: get(Tool :: PARAM_PUBLICATION_ID))
        {
            $publication_ids = Request :: get(Tool :: PARAM_PUBLICATION_ID);
        }
        else
        {
            $publication_ids = $_POST[Tool :: PARAM_PUBLICATION_ID];
        }

        if (! is_array($publication_ids))
        {
            $publication_ids = array($publication_ids);
        }

        $datamanager = WeblcmsDataManager :: get_instance();

        foreach ($publication_ids as $pid)
        {
            if($publication = $datamanager->retrieve_content_object_publication($pid))
            {
               $publication->delete(); 
            }
            
        }

        $ids = Request :: get(CourseGroupTool :: PARAM_COURSE_GROUP);
        if ($ids)
        {
            if (! is_array($ids))
                $ids = array($ids);

            $wdm = WeblcmsDataManager :: get_instance();

            foreach ($ids as $group_id)
            {
                $cg = $wdm->retrieve_course_group($group_id);
                $cg->delete();
            }

            $message = Translation :: get('ObjectDeleted', array('OBJECT' => Translation::get('CourseGroup')),Utilities:: COMMON_LIBRARIES );
            $this->redirect($message, '', array('course_group' => null, CourseGroupTool :: PARAM_ACTION => null));

        }
//        else
//        {
//            Display :: error_message('NoObjectSelected');
//        }
        $this->redirect($message, '', array('course_group' => null, CourseGroupTool :: PARAM_ACTION => null));

    }

}
?>