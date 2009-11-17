<?php
/**
 * $Id: wiki_page_statistics_viewer.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.complex_display.wiki.component
 */
/*
 * This is the component that allows the user view all statisctics about a wiki page.
 *
 * Author: Stefan Billiet
 * Author: Nick De Feyter
 */

require_once Path :: get_repository_path() . '/lib/complex_display/wiki/wiki_display.class.php';

class WikiDisplayWikiPageStatisticsViewerComponent extends WikiDisplayComponent
{

    function run()
    {
        /*
         *  We use the Reporting Tool, for more information about it, please read the information provided in the reporting class
         */
        
        /*
         *  The publication id and complex object id are requested and passed to the url
         */
        $url = $this->get_url(array(Tool :: PARAM_ACTION => Tool :: ACTION_VIEW_REPORTING_TEMPLATE, 'template_name' => 'WikiPageReportingTemplate', Tool :: PARAM_PUBLICATION_ID => Request :: get('pid'), Tool :: PARAM_COMPLEX_ID => Request :: get('cid')));
        header('location: ' . $url);
    }
}
?>