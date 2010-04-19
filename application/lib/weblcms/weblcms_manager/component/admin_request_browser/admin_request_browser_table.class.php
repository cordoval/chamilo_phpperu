<?php
/**
 * $Id: admin_request_browser_table.class.php 218 2009-11-13 14:21:26Z Yannick $
 * @package application.lib.weblcms.weblcms_manager.component.admin_request_browser
 */
require_once dirname(__FILE__) . '/admin_request_browser_table_data_provider.class.php';
require_once dirname(__FILE__) . '/admin_request_browser_table_column_model.class.php';
require_once dirname(__FILE__) . '/admin_request_browser_table_cell_renderer.class.php';
require_once dirname(__FILE__) . '/../../weblcms_manager.class.php';
/**
 * Table to display a set of course_types.
 */
class AdminRequestBrowserTable extends ObjectTable
{
    const DEFAULT_NAME = 'request_browser_table';

    /**
     * Constructor
     */
    function AdminRequestBrowserTable($browser, $parameters, $condition)
    {
    	
        $model = new AdminRequestBrowserTableColumnModel();
        $renderer = new AdminRequestBrowserTableCellRenderer($browser);
        $data_provider = new AdminRequestBrowserTableDataProvider($browser, $condition);
        parent :: __construct($data_provider, AdminRequestBrowserTable :: DEFAULT_NAME, $model, $renderer);
        $this->set_additional_parameters($parameters);
        
        $actions = array();
        $actions[] = new ObjectTableFormAction(WeblcmsManager :: PARAM_REMOVE_SELECTED_REQUESTS, Translation :: get('RemoveSelected'));
        $actions[] = new ObjectTableFormAction(WeblcmsManager :: PARAM_ALLOW_SELECTED_REQUESTS, Translation :: get('AllowSelected'));
        
        $this->set_form_actions($actions);
        $this->set_default_row_count(20);
        
    }
}
?>