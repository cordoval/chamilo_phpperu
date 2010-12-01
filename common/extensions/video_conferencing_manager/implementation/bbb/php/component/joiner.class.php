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
use repository\ExternalSetting;

use common\extensions\video_conferencing_manager\VideoConferencingComponent;
use repository\content_object\bbb_meeting\BbbMeeting;
use repository\RepositoryManager;
use repository\RepositoryDataManager;
use repository\ExternalSync;

class BbbVideoConferencingManagerJoinerComponent extends BbbVideoConferencingManager
{

    function run()
    {
        $external_sync_id = Request :: get('id');
        
        if ($external_sync_id)
        {
        	$condition = new EqualityCondition(ExternalSync :: PROPERTY_ID, $external_sync_id);
        	$external_sync = RepositoryDataManager :: get_instance()->retrieve_external_sync($condition);
        	$url = $this->get_video_conferencing_manager_connector()->join_video_conferencing_object($external_sync);

        	Redirect :: write_header($url);
        	
        }
    }
}
?>