<?php
require_once WebApplication :: get_application_class_lib_path('gutenberg') . 'gutenberg_publication_renderer.class.php';
require_once WebApplication :: get_application_class_lib_path('gutenberg') . 'renderer/gutenberg_publication_browser/gutenberg_publication_browser_table.class.php';

class TableGutenbergPublicationRenderer extends GutenbergPublicationRenderer
{

    /**
     * Returns the HTML output of this renderer.
     * @return string The HTML output
     */
    function as_html()
    {
        $table = new GutenbergPublicationBrowserTable($this, $this->get_parameters(), $this->get_condition());
        return $table->as_html();
    }
}
?>