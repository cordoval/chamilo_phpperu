<?php
require_once dirname(__FILE__) . '/../external_repository_object_renderer.class.php';
require_once dirname(__FILE__) . '/../component/external_repository_browser_table/external_repository_browser_table.class.php';

class TableExternalRepositoryObjectRenderer extends ExternalRepositoryObjectRenderer
{

    /**
     * Returns the HTML output of this renderer.
     * @return string The HTML output
     */
    function as_html()
    {
        $table = new ExternalRepositoryBrowserTable($this, $this->get_parameters(), $this->get_condition());
        return $table->as_html();
    }
}
?>