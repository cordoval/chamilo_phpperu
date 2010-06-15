<?php
/**
 * $Id: viewer.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.complex_display.assessment.component
 */

class ToolViewerComponent extends ToolComponent
{

    function run()
    {
        $this->display_header();
        echo 'General viewer component';
        $this->display_footer();
    }

}
?>