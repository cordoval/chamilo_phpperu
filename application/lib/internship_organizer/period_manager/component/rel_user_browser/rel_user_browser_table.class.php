<?php

require_once dirname(__FILE__) . '/rel_user_browser_table_data_provider.class.php';
require_once dirname(__FILE__) . '/rel_user_browser_table_column_model.class.php';
require_once dirname(__FILE__) . '/rel_user_browser_table_cell_renderer.class.php';

class InternshipOrganizerPeriodRelUserBrowserTable extends ObjectTable
{
    const DEFAULT_NAME = 'internship_organizer_period_rel_user_browser_table';

    /**
     * Constructor
     */
    function InternshipOrganizerPeriodRelUserBrowserTable($browser, $parameters, $condition)
    {
        
        $model = new InternshipOrganizerPeriodRelUserBrowserTableColumnModel($browser);
        $renderer = new InternshipOrganizerPeriodRelUserBrowserTableCellRenderer($browser);
        $data_provider = new InternshipOrganizerPeriodRelUserBrowserTableDataProvider($browser, $condition);
        parent :: __construct($data_provider, InternshipOrganizerPeriodRelUserBrowserTable :: DEFAULT_NAME, $model, $renderer);
        $actions = new ObjectTableFormActions(InternshipOrganizerPeriodManager :: PARAM_ACTION);
        $actions->add_form_action(new ObjectTableFormAction(InternshipOrganizerPeriodManager :: ACTION_UNSUBSCRIBE_USER, Translation :: get('Unsubscribe')));
        $this->set_form_actions($actions);
        $this->set_default_row_count(20);
    
    }

    static function handle_table_action()
    {
        $ids = self :: get_selected_ids(Utilities :: camelcase_to_underscores(__CLASS__));
        Request :: set_get(InternshipOrganizerPeriodManager :: PARAM_PERIOD_REL_USER_ID, $ids);
    }
}
?>