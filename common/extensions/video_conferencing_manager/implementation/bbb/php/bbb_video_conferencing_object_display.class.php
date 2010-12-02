<?php
namespace common\extensions\video_conferencing_manager\implementation\bbb;

use common\extensions\video_conferencing_manager;

use common\extensions\video_conferencing_manager\VideoConferencingObjectDisplay;
use common\extensions\video_conferencing_manager\VideoConferencingManager;
use common\libraries\VideoConferencingLauncher;

use repository\RepositoryManager;

use common\libraries\Translation;
use common\libraries\Path;
use common\libraries\Utilities;
use common\libraries\Theme;
use common\libraries\Application;

class BbbVideoConferencingObjectDisplay extends VideoConferencingObjectDisplay
{

    function get_display_properties()
    {
        $object = $this->get_object();
        
        $properties = parent :: get_display_properties();
        $properties[Translation :: get('AttendeePw')] = $object->get_attendee_pw();
        $properties[Translation :: get('ModeratorPw')] = $object->get_moderator_pw();
        
        if ($object->get_start_time() !== 'null')
        {
            $properties[Translation :: get('StartTime')] = $object->get_start_time();
            if ($object->get_end_time() !== 'null')
            {
                $properties[Translation :: get('EndTime')] = $object->get_end_time();
            }
        }
        
        if ($object->get_running())
        {
            $properties[Translation :: get('Running')] = '<img src="' . Theme :: get_image_path() . 'running_true.png"/>';
        }
        else
        {
            $properties[Translation :: get('Running')] = '<img src="' . Theme :: get_image_path() . 'running_false.png"/>';
        }
        
        $moderators = array();
        foreach ($object->get_moderators() as $moderator)
        {
            $moderators[] = $moderator['fullName'] . ' ' . $moderator['userID'];
        }
        if (count($moderators) > 0)
        {
            $properties[Translation :: get('Moderators')] = implode('<br/>', $moderators);
        }
        
        $viewers = array();
        foreach ($object->get_moderators() as $viewer)
        {
            $viewers[] = $viewer['fullName'] . ' ' . $viewer['userID'];
        }
        
        if (count($viewers) > 0)
        {
            $properties[Translation :: get('Viewers')] = implode('<br/>', $viewers);
        }
        
        return $properties;
    }

    function get_join_button()
    {
    	$object = $this->get_object();
    	$external_sync = $object->get_synchronization_data();
    	$parameters = array();
    	$parameters[Application :: PARAM_APPLICATION] = VideoConferencingLauncher :: APPLICATION_NAME; 
    	$parameters[RepositoryManager :: PARAM_EXTERNAL_INSTANCE] = $external_sync->get_external_id();
    	$parameters[VideoConferencingManager::PARAM_VIDEO_CONFERENCING_ID] = $external_sync->get_id();
    	$parameters[VideoConferencingManager :: PARAM_VIDEO_CONFERENCING_MANAGER_ACTION] = VideoConferencingManager :: ACTION_JOIN_MEETING;
    	$link = Path :: get_launcher_application_path(true) . 'index.php?' . http_build_query($parameters);
    	
        return '<a class="button normal_button join_button" onclick="javascript:openPopup(\''. $link . '\');"> ' . Translation :: get('JoinMeeting') . '</a>';
    
    }

}
?>