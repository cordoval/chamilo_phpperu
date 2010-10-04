<?php
/**
 * $Id: laika_group_browser_table.class.php 196 2009-11-13 12:19:18Z chellee $
 * @package application.lib.laika.laika_manager.component.laika_group_browser
 */
require_once dirname(__FILE__) . '/laika_group_browser_table_data_provider.class.php';
require_once dirname(__FILE__) . '/laika_group_browser_table_column_model.class.php';
require_once dirname(__FILE__) . '/laika_group_browser_table_cell_renderer.class.php';
require_once dirname(__FILE__) . '/../../laika_manager.class.php';
/**
 * Table to display a set of learning objects.
 */
class LaikaGroupBrowserTable extends ObjectTable
{
    const DEFAULT_NAME = 'laika_group_browser_table';

    /**
     * Constructor
     * @see ContentObjectTable::ContentObjectTable()
     */
    function LaikaGroupBrowserTable($browser, $parameters, $condition)
    {
        $model = new LaikaGroupBrowserTableColumnModel();
        $renderer = new LaikaGroupBrowserTableCellRenderer($browser);
        $data_provider = new LaikaGroupBrowserTableDataProvider($browser, $condition);
        parent :: __construct($data_provider, LaikaGroupBrowserTable :: DEFAULT_NAME, $model, $renderer);
        $this->set_additional_parameters($parameters);
        //		$actions = array();
        //		$actions[GroupManager :: PARAM_REMOVE_SELECTED] = Translation :: get('RemoveSelected');
        //		$actions[GroupManager :: PARAM_TRUNCATE_SELECTED] = Translation :: get('TruncateSelected');
        //		$this->set_form_actions($actions);
        $this->set_default_row_count(20);
    }
}
?>