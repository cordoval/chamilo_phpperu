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
        $action = $this->get_action();

        switch ($action)
        {
            case self :: ACTION_PAGE_STATISTICS :
                $browser->set_template_name('wiki_page_reporting_template');
                break;
            case self :: ACTION_STATISTICS :
            	if(Request :: get('application') != 'wiki')
                	$browser->set_template_name('wiki_reporting_template');
                else
                	$browser->set_template_name('wiki_most_reporting_template');
                break;
            case self :: ACTION_ACCESS_DETAILS :
                $browser->set_template_name('publication_detail_reporting_template');
                break;
        }

        $browser->run();
    }
}
?>