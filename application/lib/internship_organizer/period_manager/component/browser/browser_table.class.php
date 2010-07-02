<?php

require_once dirname(__FILE__) . '/browser_table_data_provider.class.php';
require_once dirname(__FILE__) . '/browser_table_column_model.class.php';
require_once dirname(__FILE__) . '/browser_table_cell_renderer.class.php';
/**
 * Table to display a set of learning objects.
 */
class InternshipOrganizerPeriodBrowserTable extends ObjectTable
{
    const DEFAULT_NAME = 'internship_organizer_period_browser_table';

    /**
     * Constructor
     * @see ContentObjectTable::ContentObjectTable()
     */
    function InternshipOrganizerPeriodBrowserTable($browser, $parameters, $condition)
    {
        $model = new InternshipOrganizerPeriodBrowserTableColumnModel();
        $renderer = new InternshipOrganizerPeriodBrowserTableCellRenderer($browser);
        $data_provider = new InternshipOrganizerPeriodBrowserTableDataProvider($browser, $condition);
        parent :: __construct($data_provider, InternshipOrganizerPeriodBrowserTable :: DEFAULT_NAME, $model, $renderer);
        $this->set_additional_parameters($parameters);
        $actions = new ObjectTableFormActions(InternshipOrganizerPeriodManager :: PARAM_ACTION);
        $actions->add_form_action(new ObjectTableFormAction(InternshipOrganizerPeriodManager :: ACTION_DELETE_PERIOD, Translation :: get('Delete')));
        $this->set_form_actions($actions);
        $this->set_default_row_count(20);
    
    }

    static function handle_table_action()
    {
        $ids = self :: get_selected_ids(Utilities :: camelcase_to_underscores(__CLASS__));
        Request :: set_get(InternshipOrganizerPeriodManager :: PARAM_PERIOD_ID, $ids);
    }
}
?>