<?php

require_once dirname(__FILE__) . '/browser_table_data_provider.class.php';
require_once dirname(__FILE__) . '/browser_table_column_model.class.php';
require_once dirname(__FILE__) . '/browser_table_cell_renderer.class.php';
require_once dirname(__FILE__) . '/../../organisation_manager.class.php';

class InternshipOrganizerOrganisationBrowserTable extends ObjectTable
{
    const DEFAULT_NAME = 'internship_organizer_organisation_browser_table';

    /**
     * Constructor
     */
    function InternshipOrganizerOrganisationBrowserTable($browser, $parameters, $condition)
    {
        $model = new InternshipOrganizerOrganisationBrowserTableColumnModel();
        $renderer = new InternshipOrganizerOrganisationBrowserTableCellRenderer($browser);
        $data_provider = new InternshipOrganizerOrganisationBrowserTableDataProvider($browser, $condition);
        parent :: __construct($data_provider, self :: DEFAULT_NAME, $model, $renderer);
        $this->set_additional_parameters($parameters);

        $action = new ObjectTableFormActions();
        $action->set_action(InternshipOrganizerOrganisationManager :: PARAM_ACTION);
        $action->add_form_action(new ObjectTableFormAction(InternshipOrganizerOrganisationManager :: ACTION_DELETE_ORGANISATION, Translation :: get('RemoveSelected')));

        $this->set_form_actions($action);
        $this->set_default_row_count(20);
    }

    static function handle_table_action()
    {
        $ids = self :: get_selected_ids(Utilities :: camelcase_to_underscores(__CLASS__));
        Request :: set_get(InternshipOrganizerOrganisationManager :: PARAM_ORGANISATION_ID, $ids);
    }
}
?>