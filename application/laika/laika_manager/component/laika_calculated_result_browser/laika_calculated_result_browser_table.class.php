<?php
/**
 * $Id: laika_calculated_result_browser_table.class.php 196 2009-11-13 12:19:18Z chellee $
 * @package application.lib.laika.laika_manager.component.laika_calculated_result_browser
 */
require_once dirname(__FILE__) . '/laika_calculated_result_browser_table_data_provider.class.php';
require_once dirname(__FILE__) . '/laika_calculated_result_browser_table_column_model.class.php';
require_once dirname(__FILE__) . '/laika_calculated_result_browser_table_cell_renderer.class.php';
require_once dirname(__FILE__) . '/../../laika_manager.class.php';
/**
 * Table to display a set of learning objects.
 */
class LaikaCalculatedResultBrowserTable extends ObjectTable
{
    const DEFAULT_NAME = 'laika_calculated_result_browser_table';

    /**
     * Constructor
     * @see ContentObjectTable::ContentObjectTable()
     */
    function LaikaCalculatedResultBrowserTable($browser, $parameters, $condition)
    {
        $model = new LaikaCalculatedResultBrowserTableColumnModel();
        $renderer = new LaikaCalculatedResultBrowserTableCellRenderer($browser);
        $data_provider = new LaikaCalculatedResultBrowserTableDataProvider($browser, $condition);
        parent :: __construct($data_provider, LaikaCalculatedResultBrowserTable :: DEFAULT_NAME, $model, $renderer);
        $this->set_additional_parameters($parameters);
        $actions = array();
        
        $actions[] = new ObjectTableFormAction(LaikaManager :: PARAM_MAIL_SELECTED, Translation :: get('MailSelected'), false);
        
        //		$actions[GroupManager :: PARAM_TRUNCATE_SELECTED] = Translation :: get('TruncateSelected');
        $this->set_form_actions($actions);
        $this->set_default_row_count(20);
    }
}
?>