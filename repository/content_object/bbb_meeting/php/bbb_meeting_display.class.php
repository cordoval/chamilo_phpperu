<?php
namespace repository\content_object\bbb_meeting;

use common\libraries\Text;
use common\extensions\video_conferencing_manager\VideoConferencingObjectDisplay;
use repository\ContentObjectDisplay;

/**
 * $Id: bbb_meeting_display.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.content_object.soundcloud
 */
class BbbMeetingDisplay extends ContentObjectDisplay
{
    function get_description()
    {
    	$object = $this->get_content_object();
    	$external_sync = $object->get_synchronization_data();

    	$display = VideoConferencingObjectDisplay :: factory($external_sync->get_external_object());
    	$html = array();
    	$html[] = $display->get_properties_table();
    	$html[] = $display->get_join_button();
    	
    	$description = parent :: get_description();
        $object = $this->get_content_object();

        return str_replace(self :: DESCRIPTION_MARKER, implode('<br/>', $html) . self :: DESCRIPTION_MARKER, $description);   	
    }
    
    
}
?>