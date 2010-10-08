<?php
/**
 * $Id: gutenberg_publication_browser_table.class.php 192 2009-11-13 11:51:02Z chellee $
 * @package application.lib.gutenbergr.gutenbergr_manager.component.gutenbergpublicationbrowser
 */
require_once WebApplication :: get_application_class_lib_path('gutenberg') . 'renderer/gutenberg_publication_browser/gutenberg_publication_browser_table_data_provider.class.php';
require_once WebApplication :: get_application_class_lib_path('gutenberg') . 'renderer/gutenberg_publication_browser/gutenberg_publication_browser_table_column_model.class.php';
require_once WebApplication :: get_application_class_lib_path('gutenberg') . 'renderer/gutenberg_publication_browser/gutenberg_publication_browser_table_cell_renderer.class.php';
/**
 * Table to display a set of learning objects.
 */
class GutenbergPublicationBrowserTable extends ObjectTable
{
    const DEFAULT_NAME = 'gutenberg_publication_browser_table';

    /**
     * Constructor
     */
    function GutenbergPublicationBrowserTable($browser, $parameters, $condition)
    {
        $model = new GutenbergPublicationBrowserTableColumnModel();
        $renderer = new GutenbergPublicationBrowserTableCellRenderer($browser);
        $data_provider = new GutenbergPublicationBrowserTableDataProvider($browser, $condition);
        parent :: __construct($data_provider, GutenbergPublicationBrowserTable :: DEFAULT_NAME, $model, $renderer);
        $actions = array();
        
        $actions[] = new ObjectTableFormAction(GutenbergManager :: PARAM_DELETE_SELECTED, Translation :: get('RemoveSelected'));
        
        if ($browser->get_user()->is_platform_admin())
        {
            $this->set_form_actions($actions);
        }
        $this->set_default_row_count(20);
    }
}
?>