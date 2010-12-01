<?php
namespace common\extensions\video_conferencing_manager\implementation\bbb;

use common\extensions\video_conferencing_manager\VideoConferencingObjectDisplay;

use common\libraries\Translation;
use common\libraries\Path;
use common\libraries\Utilities;
use common\libraries\Theme;

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

}
?>