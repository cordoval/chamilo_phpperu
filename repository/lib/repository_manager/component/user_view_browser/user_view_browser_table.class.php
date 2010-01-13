<?php
/**
 * $Id: user_view_browser_table.class.php 204 2009-11-13 12:51:30Z kariboe $
 * @package repository.lib.repository_manager.component.user_view_browser
 */
require_once dirname(__FILE__) . '/user_view_browser_table_data_provider.class.php';
require_once dirname(__FILE__) . '/user_view_browser_table_column_model.class.php';
require_once dirname(__FILE__) . '/user_view_browser_table_cell_renderer.class.php';
/**
 * Table to display a set of learning objects.
 */
class UserViewBrowserTable extends ObjectTable
{
    const DEFAULT_NAME = 'repository_browser_table';

    /**
     * Constructor
     * @see ContentObjectTable::ContentObjectTable()
     */
    function UserViewBrowserTable($browser, $parameters, $condition)
    {
        $model = new UserViewBrowserTableColumnModel();
        $renderer = new UserViewBrowserTableCellRenderer($browser);
        $data_provider = new UserViewBrowserTableDataProvider($browser, $condition);
        parent :: __construct($data_provider, UserViewBrowserTable :: DEFAULT_NAME, $model, $renderer);
        $this->set_additional_parameters($parameters);
        $actions = array();
		$actions[] = new ObjectTableFormAction(RepositoryManager :: PARAM_DELETE_SELECTED_USER_VIEW, Translation :: get('DeleteSelected'));
		$this->set_form_actions($actions);
        $this->set_default_row_count(20);
    }
}
?>