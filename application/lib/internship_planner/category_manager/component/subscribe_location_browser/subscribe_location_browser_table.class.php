<?php
/**
 * $Id: subscribe_user_browser_table.class.php 166 2009-11-12 11:03:06Z vanpouckesven $
 * @package categories.lib.category_manager.component.subscribe_user_browser
 */

require_once dirname(__FILE__) . '/subscribe_user_browser_table_data_provider.class.php';
require_once dirname(__FILE__) . '/subscribe_user_browser_table_column_model.class.php';
require_once dirname(__FILE__) . '/subscribe_user_browser_table_cell_renderer.class.php';
/**
 * Table to display a list of users not subscribed to a course.
 */
class SubscribeLocationBrowserTable extends ObjectTable
{
    const DEFAULT_NAME = 'category_browser_table';

    /**
     * Constructor
     */
    function SubscribeLocationBrowserTable($browser, $parameters, $condition)
    {
        $model = new SubscribeLocationBrowserTableColumnModel();
        $renderer = new SubscribeLocationBrowserTableCellRenderer($browser);
        $data_provider = new SubscribeLocationBrowserTableDataProvider($browser, $condition);
        parent :: __construct($data_provider, SubscribeLocationBrowserTable :: DEFAULT_NAME, $model, $renderer);
        $this->set_additional_parameters($parameters);
        $actions = array();
        
        $actions[] = new ObjectTableFormAction(InternshipPlannerCategoryManager :: PARAM_SUBSCRIBE_SELECTED, Translation :: get('Subscribe'));
        
        $this->set_form_actions($actions);
        $this->set_default_row_count(20);
    }
}
?>