<?php
require_once dirname(__FILE__) . '/../content_object_renderer.class.php';
require_once dirname(__FILE__) . '/../repository_manager/component/browser/repository_browser_table.class.php';

class TableContentObjectRenderer extends ContentObjectRenderer
{

    /**
     * Returns the HTML output of this renderer.
     * @return string The HTML output
     */
    function as_html()
    {
        $table = new RepositoryBrowserTable($this, $this->get_parameters(), $this->get_condition());
        return $table->as_html();
    }
}
?>