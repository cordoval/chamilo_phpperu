<?php
namespace application\weblcms\tool\user;

use application\weblcms\Tool;
use common\libraries\Breadcrumb;
use common\libraries\BreadcrumbTrail;
use common\libraries\Translation;
use application\weblcms\WeblcmsDataManager;
use common\libraries\Application;
use common\libraries\Request;
use common\libraries\Utilities;
use application\weblcms\WeblcmsRights;

class UserToolStatusChangerComponent extends UserTool
{

    function run()
    {
        $users = Request :: get(self :: PARAM_USERS);
        $status = Request :: get(self :: PARAM_STATUS);

        if(!$users || !$status || !$this->is_allowed(WeblcmsRights :: EDIT_RIGHT))
        {
            $this->display_error_page(Translation :: get('NoObjectSelected', null, Utilities :: COMMON_LIBRARIES));
            exit;
        }

        if(!is_array($users))
        {
            $users = array($users);
        }

        $failed = 0;

        foreach($users as $user)
        {
            $course_user_relation = WeblcmsDataManager :: get_instance()->retrieve_course_user_relation($this->get_course_id(), $user);
            if(!$course_user_relation)
            {
                $failed++;
            }

            $course_user_relation->set_status($status);
            if(!$course_user_relation->update())
            {
                $failed++;
            }
        }

        /*if(count($users) > 1)
        {
            $user_status = Translation :: get('UserStatus');

            if($failed)
            {
                $message = Translation :: get('ObjectNotUpdated', array('OBJECT' => $user_status), Utilities :: COMMON_LIBRARIES);
            }
            else
            {
                $message = Translation :: get('ObjectUpdated', array('OBJECT' => $user_status), Utilities :: COMMON_LIBRARIES);
            }
        }
        else
        {
            $user_statuses = Translation :: get('UserStatuses');

            if($failed)
            {
                $message = Translation :: get('ObjecstNotUpdated', array('OBJECTS' => $user_statuses), Utilities :: COMMON_LIBRARIES);
            }
            else
            {
                $message = Translation :: get('ObjectsUpdated', array('OBJECTS' => $user_statuses), Utilities :: COMMON_LIBRARIES);
            }
        }*/

        $message = $this->get_general_result($failed, count($users), Translation :: get('UserStatus'), Translation :: get('UserStatusses'), Application :: RESULT_TYPE_UPDATED);

        $this->redirect($message, $failed > 0, array(Tool :: PARAM_ACTION => UserTool :: ACTION_UNSUBSCRIBE_USER_BROWSER));

    }

    function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $breadcrumbtrail->add(new Breadcrumb($this->get_url(array(Tool :: PARAM_ACTION => UserTool :: ACTION_UNSUBSCRIBE_USER_BROWSER)), Translation :: get('UserToolUnsubscribeUserBrowserComponent')));
    }
}
?>