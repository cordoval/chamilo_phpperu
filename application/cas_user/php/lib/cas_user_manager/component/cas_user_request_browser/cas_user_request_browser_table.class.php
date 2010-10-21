<?php
require_once dirname(__FILE__) . '/cas_user_request_browser_table_data_provider.class.php';
require_once dirname(__FILE__) . '/cas_user_request_browser_table_column_model.class.php';
require_once dirname(__FILE__) . '/cas_user_request_browser_table_cell_renderer.class.php';
require_once dirname(__FILE__) . '/../../cas_user_manager.class.php';

/**
 * Table to display a list of cas_user_requests
 *
 * @author Hans De Bisschop
 */
class CasUserRequestBrowserTable extends ObjectTable
{
    const DEFAULT_NAME = 'cas_user_request_browser_table';

    /**
     * Constructor
     */
    function CasUserRequestBrowserTable($browser, $parameters, $condition)
    {
        $model = new CasUserRequestBrowserTableColumnModel();
        $renderer = new CasUserRequestBrowserTableCellRenderer($browser);
        $data_provider = new CasUserRequestBrowserTableDataProvider($browser, $condition);
        parent :: __construct($data_provider, self :: DEFAULT_NAME, $model, $renderer);
        $this->set_additional_parameters($parameters);
        $actions = array();

//        if (get_class($browser) != 'CasUserManagerCasUserRequestsBrowserComponent')
//        {
//            $actions[] = new ObjectTableFormAction(CdaManager :: PARAM_DELETE_SELECTED_CDA_LANGUAGES, Translation :: get('RemoveSelected'));
//        }

        $this->set_form_actions($actions);
        $this->set_default_row_count(20);
    }
}
?>