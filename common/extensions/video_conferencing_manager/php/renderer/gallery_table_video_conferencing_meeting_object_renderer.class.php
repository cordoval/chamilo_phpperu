<?php
namespace common\extensions\video_conferencing_manager;

require_once dirname(__FILE__) . '/../video_conferencing_meeting_object_renderer.class.php';
require_once dirname(__FILE__) . '/../component/video_conferencing_browser_gallery_table/video_conferencing_browser_gallery_table.class.php';

class GalleryTableVideoConferencingMeetingObjectRenderer extends VideoConferencingMeetingObjectRenderer
{

    /**
     * Returns the HTML output of this renderer.
     * @return string The HTML output
     */
    function as_html()
    {
        $video_conferencing_manager_type = $this->get_video_conferencing_meeting_browser()->get_conferencing_type();
        $table = VideoConferencingBrowserGalleryTable :: factory($video_conferencing_manager_type, $this, $this->get_parameters(), $this->get_condition());
        return $table->as_html();
    }
}
?>