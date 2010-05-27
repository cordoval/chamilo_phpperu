<?php
/**
 * $Id: reporting_template_viewer.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.complex_display.assessment.component
 */

class WikiDisplayReportingTemplateViewerComponent extends WikiDisplay
{
    function run()
    {
        $browser = ComplexDisplayComponent :: factory(ComplexDisplayComponent :: REPORTING_TEMPLATE_VIEWER_COMPONENT, $this);
        $browser->run();
    }
}
?>