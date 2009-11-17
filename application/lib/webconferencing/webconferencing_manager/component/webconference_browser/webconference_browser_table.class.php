<?php
/**
 * $Id: webconference_browser_table.class.php 220 2009-11-13 14:33:52Z kariboe $
 * @package application.lib.webconferencing.webconferencing_manager.component.webconference_browser
 */
require_once dirname(__FILE__) . '/webconference_browser_table_data_provider.class.php';
require_once dirname(__FILE__) . '/webconference_browser_table_column_model.class.php';
require_once dirname(__FILE__) . '/webconference_browser_table_cell_renderer.class.php';
require_once dirname(__FILE__) . '/../../webconferencing_manager.class.php';

/**
 * Table to display a list of webconferences
 * @author Stefaan Vanbillemont
 */
class WebconferenceBrowserTable extends ObjectTable
{
    const DEFAULT_NAME = 'webconference_browser_table';

    /**
     * Constructor
     */
    function WebconferenceBrowserTable($browser, $parameters, $condition)
    {
        $model = new WebconferenceBrowserTableColumnModel();
        $renderer = new WebconferenceBrowserTableCellRenderer($browser);
        $data_provider = new WebconferenceBrowserTableDataProvider($browser, $condition);
        parent :: __construct($data_provider, self :: DEFAULT_NAME, $model, $renderer);
        $this->set_additional_parameters($parameters);
        $actions = array();
        
        $actions[] = new ObjectTableFormAction(WebconferencingManager :: PARAM_DELETE_SELECTED_WEBCONFERENCES, Translation :: get('RemoveSelected'));
        
        $this->set_form_actions($actions);
        $this->set_default_row_count(20);
    }
}
?>