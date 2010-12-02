<?php
namespace common\extensions\video_conferencing_manager;

class TableVideoConferencingObjectRenderer extends VideoConferencingObjectRenderer
{

    /**
     * Returns the HTML output of this renderer.
     * @return string The HTML output
     */
    function as_html()
    {
        $video_conferencing_manager_type = $this->get_video_conferencing_browser()->get_video_conferencing_type();
        $table = VideoConferencingBrowserTable :: factory($video_conferencing_manager_type, $this, $this->get_parameters(), $this->get_condition());
        return $table->as_html();
    }
}
?>