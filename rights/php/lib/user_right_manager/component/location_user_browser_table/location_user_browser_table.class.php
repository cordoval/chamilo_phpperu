<?php
/**
 * $Id: location_user_browser_table.class.php 214 2009-11-13 13:57:37Z vanpouckesven $
 * @package rights.lib.user_right_manager.component.location_user_bowser_table
 */
require_once dirname(__FILE__) . '/location_user_browser_table_data_provider.class.php';
require_once dirname(__FILE__) . '/location_user_browser_table_column_model.class.php';
require_once dirname(__FILE__) . '/location_user_browser_table_cell_renderer.class.php';
/**
 * Table to display a set of users.
 */
class LocationUserBrowserTable extends ObjectTable
{
    const DEFAULT_NAME = 'admin_user_browser_table';

    /**
     * Constructor
     * @see ContentObjectTable::ContentObjectTable()
     */
    function LocationUserBrowserTable($browser, $parameters, $condition)
    {
        $model = new LocationUserBrowserTableColumnModel($browser);
        $renderer = new LocationUserBrowserTableCellRenderer($browser);
        $data_provider = new LocationUserBrowserTableDataProvider($browser, $condition);
        parent :: __construct($data_provider, LocationUserBrowserTable :: DEFAULT_NAME, $model, $renderer);
        $this->set_additional_parameters($parameters);
        $actions = new ObjectTableFormActions();
        //Deactivated: What should happen when a user is removed ? Full remove or deactivation of account ?
        //$actions[UserManager :: PARAM_REMOVE_SELECTED] = Translation :: get('RemoveSelected');
        $this->set_form_actions($actions);
        $this->set_default_row_count(20);
    }
}
?>