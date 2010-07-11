<?php
require_once dirname(__FILE__) . '/external_repository_browser_table/external_repository_browser_table.class.php';

class ExternalRepositoryBrowserComponent extends ExternalRepositoryComponent
{

    function ExternalRepositoryBrowserComponent($application)
    {
        parent :: __construct($application);
    }

    function run()
    {        
        $this->display_header();
        $browser_table = new ExternalRepositoryBrowserTable($this, $this->get_parameters(), null);
        echo $browser_table->as_html();
        $this->display_footer();
    }
}
?>