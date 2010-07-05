<?php

require_once dirname(__FILE__) . '/rel_group_browser_table_data_provider.class.php';
require_once dirname(__FILE__) . '/rel_group_browser_table_column_model.class.php';
require_once dirname(__FILE__) . '/rel_group_browser_table_cell_renderer.class.php';

class InternshipOrganizerPeriodRelGroupBrowserTable extends ObjectTable
{
    const DEFAULT_NAME = 'internship_organizer_period_rel_group_browser_table';

    /**
     * Constructor
     */
    function InternshipOrganizerPeriodRelGroupBrowserTable($browser, $parameters, $condition)
    {
        
        $model = new InternshipOrganizerPeriodRelGroupBrowserTableColumnModel($browser);
        $renderer = new InternshipOrganizerPeriodRelGroupBrowserTableCellRenderer($browser);
        $data_provider = new InternshipOrganizerPeriodRelGroupBrowserTableDataProvider($browser, $condition);
        parent :: __construct($data_provider, InternshipOrganizerPeriodRelGroupBrowserTable :: DEFAULT_NAME, $model, $renderer);
        $actions = new ObjectTableFormActions(InternshipOrganizerPeriodManager :: PARAM_ACTION);
        $actions->add_form_action(new ObjectTableFormAction(InternshipOrganizerPeriodManager :: ACTION_UNSUBSCRIBE_GROUP, Translation :: get('Unsubscribe')));
        $this->set_form_actions($actions);
        $this->set_default_row_count(20);
    
    }

    static function handle_table_action()
    {
        $ids = self :: get_selected_ids(Utilities :: camelcase_to_underscores(__CLASS__));
        Request :: set_get(InternshipOrganizerPeriodManager :: PARAM_PERIOD_REL_GROUP_ID, $ids);
    }
}
?>