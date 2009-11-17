<?php
/**
 * $Id: validation_browser_table.class.php 191 2009-11-13 11:50:28Z chellee $
 * @package application.common.validation_manager.component.validation_browser
 */
require_once dirname(__FILE__) . '/validation_browser_table_data_provider.class.php';
require_once dirname(__FILE__) . '/validation_browser_table_column_model.class.php';
require_once dirname(__FILE__) . '/validation_browser_table_cell_renderer.class.php';
require_once dirname(__FILE__) . '/../../../../lib/profiler/profiler_manager/profiler_manager.class.php';
/**
 * Table to display a set of learning objects.
 */
class ValidationBrowserTab extends ObjectTable
{
    const DEFAULT_NAME = 'validation_browser_table';

    /**
     * Constructor
     */
    function ValidationBrowserTab($browser, $name, $parameters, $condition)
    {
        $model = new ValidationBrowserTableColumnMod();
        $renderer = new ValidationBrowserTableCellRend($browser);
        $data_provider = new ValidationBrowserTableDataProvid($browser, $condition);
        parent :: __construct($data_provider, ValidationBrowserTab :: DEFAULT_NAME, $model, $renderer);
        /* $actions = array();
        $actions[Profile $user->get_username();rManager :: PARAM_DELETE_SELECTED] = Translation :: get('RemoveSelected');
        if ($browser->get_user()->is_platform_admin())
        {
            $this->set_form_actions($actions);
        }*/
        $this->set_default_row_count(20);
    }
}
?>