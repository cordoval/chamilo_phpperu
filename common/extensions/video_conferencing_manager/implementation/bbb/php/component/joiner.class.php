<?php
namespace common\extensions\video_conferencing_manager\implementation\bbb;

use repository;

use common\libraries;

use common\libraries\Request;
use common\libraries\Application;
use common\libraries\Translation;
use common\libraries\Redirect;
use common\libraries\Path;
use common\libraries\EqualityCondition;
use common\libraries\Display;

use repository\ExternalSetting;

use common\extensions\video_conferencing_manager\VideoConferencingComponent;
use common\extensions\video_conferencing_manager\VideoConferencingManager;
use repository\content_object\bbb_meeting\BbbMeeting;
use repository\RepositoryManager;
use repository\RepositoryDataManager;
use repository\ExternalSync;

class BbbVideoConferencingManagerJoinerComponent extends BbbVideoConferencingManager
{

    function run()
    {
        $id = Request :: get(VideoConferencingManager :: PARAM_VIDEO_CONFERENCING_ID);
        
        if ($id)
        {
            $condition = new EqualityCondition(ExternalSync :: PROPERTY_ID, $id);
            $external_sync = RepositoryDataManager :: get_instance()->retrieve_external_sync($condition);
            $result = $this->get_video_conferencing_manager_connector()->join_video_conferencing_object($external_sync);
            if ($result)
            {
                Redirect :: write_header($result);
            }
            else
            {
                Display :: error_page(Translation :: get('CannotJoinMeeting'));
            }
        }
    }
}
?>