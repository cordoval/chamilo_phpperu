<?php
namespace application\cas_user;

require_once dirname(__FILE__) . '/cas_account_browser_table_data_provider.class.php';
require_once dirname(__FILE__) . '/cas_account_browser_table_column_model.class.php';
require_once dirname(__FILE__) . '/cas_account_browser_table_cell_renderer.class.php';
require_once dirname(__FILE__) . '/../../cas_account_manager.class.php';

/**
 * Table to display a list of cas_accounts
 *
 * @author Hans De Bisschop
 */
class CasAccountBrowserTable extends ObjectTable
{
    const DEFAULT_NAME = 'cas_account_browser_table';

    /**
     * Constructor
     */
    function CasAccountBrowserTable($browser, $parameters, $condition)
    {
        $model = new CasAccountBrowserTableColumnModel();
        $renderer = new CasAccountBrowserTableCellRenderer($browser);
        $data_provider = new CasAccountBrowserTableDataProvider($browser, $condition);
        parent :: __construct($data_provider, self :: DEFAULT_NAME, $model, $renderer);
        $this->set_additional_parameters($parameters);
        $actions = array();

//        if (get_class($browser) != 'CasUserManagerCasAccountsBrowserComponent')
//        {
//            $actions[] = new ObjectTableFormAction(CdaManager :: PARAM_DELETE_SELECTED_CDA_LANGUAGES, Translation :: get('RemoveSelected'));
//        }

        $this->set_form_actions($actions);
        $this->set_default_row_count(20);
    }
}
?>