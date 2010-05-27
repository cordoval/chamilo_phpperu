<?php
/**
 * $Id: wiki_statistics_viewer.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.complex_display.wiki.component
 */
/*
 * This is the component that allows the user view all statisctics about a wiki.
 * 
 * Author: Stefan Billiet
 * Author: Nick De Feyter
 */

require_once Path :: get_repository_path() . '/lib/content_object/wiki/display/wiki_display.class.php';

class WikiDisplayWikiStatisticsViewerComponent extends WikiDisplay
{

    function run()
    {
        /*
         *  We use the Reporting Tool, for more information about it, please read the information provided in the reporting class
         */
        
        $url = $this->get_url(array(Tool :: PARAM_ACTION => Tool :: ACTION_VIEW_REPORTING_TEMPLATE, 'template_name' => 'WikiReportingTemplate', Tool :: PARAM_PUBLICATION_ID => Request :: get('pid')));
        header('location: ' . $url);
    }
}
?>