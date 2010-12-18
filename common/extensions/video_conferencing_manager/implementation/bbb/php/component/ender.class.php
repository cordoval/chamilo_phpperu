<?php
namespace common\extensions\video_conferencing_manager\implementation\bbb;

use common\libraries\Utilities;
use common\libraries\Request;
use common\libraries\EqualityCondition;
use common\libraries\Translation;

use repository\RepositoryDataManager;
use repository\ExternalSync;

use common\extensions\video_conferencing_manager\VideoConferencingManager;

class BbbVideoConferencingManagerEnderComponent extends BbbVideoConferencingManager
{

    function run()
    {
        $id = Request :: get(VideoConferencingManager :: PARAM_VIDEO_CONFERENCING_ID);

        if ($id)
        {
            $condition = new EqualityCondition(ExternalSync :: PROPERTY_ID, $id);
            $external_sync = RepositoryDataManager :: get_instance()->retrieve_external_sync($condition);
            $result = $this->get_video_conferencing_manager_connector()->end_video_conferencing_object($external_sync);

            $this->redirect(Translation :: get($result ? 'VideoConferenceEnded' : 'VideoConferenceNotEnded'), ($result ? false : true), array(
                    VideoConferencingManager :: PARAM_VIDEO_CONFERENCING_MANAGER_ACTION => VideoConferencingManager :: ACTION_VIEW_VIDEO_CONFERENCING,
                    VideoConferencingManager :: PARAM_VIDEO_CONFERENCING_ID => $id));
        }
        else
        {
            $this->display_error_page(htmlentities(Translation :: get('NoObjectSelected', array(
                    'OBJECT' => Translation :: get('ExternalObject')), Utilities :: COMMON_LIBRARIES)));
        }
    }
}
?>