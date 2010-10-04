<?php
/**
 * $Id: alexia_publication_browser_table.class.php 192 2009-11-13 11:51:02Z chellee $
 * @package application.lib.alexiar.alexiar_manager.component.alexiapublicationbrowser
 */
require_once dirname(__FILE__) . '/alexia_publication_browser_table_data_provider.class.php';
require_once dirname(__FILE__) . '/alexia_publication_browser_table_column_model.class.php';
require_once dirname(__FILE__) . '/alexia_publication_browser_table_cell_renderer.class.php';
require_once dirname(__FILE__) . '/../../alexia_manager.class.php';
/**
 * Table to display a set of learning objects.
 */
class AlexiaPublicationBrowserTable extends ObjectTable
{
    const DEFAULT_NAME = 'alexia_publication_browser_table';

    /**
     * Constructor
     */
    function AlexiaPublicationBrowserTable($browser, $name, $parameters, $condition)
    {
        $model = new AlexiaPublicationBrowserTableColumnModel();
        $renderer = new AlexiaPublicationBrowserTableCellRenderer($browser);
        $data_provider = new AlexiaPublicationBrowserTableDataProvider($browser, $condition);
        parent :: __construct($data_provider, AlexiaPublicationBrowserTable :: DEFAULT_NAME, $model, $renderer);
        $actions = array();
        
        $actions[] = new ObjectTableFormAction(AlexiaManager :: PARAM_DELETE_SELECTED, Translation :: get('RemoveSelected'));
        
        if ($browser->get_user()->is_platform_admin())
        {
            $this->set_form_actions($actions);
        }
        $this->set_default_row_count(20);
    }
}
?>