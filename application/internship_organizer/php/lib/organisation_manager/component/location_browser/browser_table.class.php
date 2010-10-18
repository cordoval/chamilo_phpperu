<?php

require_once dirname(__FILE__) . '/browser_table_data_provider.class.php';
require_once dirname(__FILE__) . '/browser_table_column_model.class.php';
require_once dirname(__FILE__) . '/browser_table_cell_renderer.class.php';
require_once dirname(__FILE__) . '/../../organisation_manager.class.php';

class InternshipOrganizerLocationBrowserTable extends ObjectTable
{
    const DEFAULT_NAME = 'internship_organizer_location_browser_table';

    /**
     * Constructor
     */
    function InternshipOrganizerLocationBrowserTable($browser, $parameters, $condition)
    {
        $model = new InternshipOrganizerLocationBrowserTableColumnModel();
        $renderer = new InternshipOrganizerLocationBrowserTableCellRenderer($browser);
        $data_provider = new InternshipOrganizerLocationBrowserTableDataProvider($browser, $condition);
        parent :: __construct($data_provider, self :: DEFAULT_NAME, $model, $renderer);
        $this->set_additional_parameters($parameters);
        
        $actions = new ObjectTableFormActions(InternshipOrganizerAgreementManager :: PARAM_ACTION);
        
        if (InternshipOrganizerRights :: is_allowed_in_internship_organizers_subtree(InternshipOrganizerRights :: RIGHT_EDIT, InternshipOrganizerRights :: LOCATION_ORGANISATION, InternshipOrganizerRights :: TYPE_COMPONENT))
        {
            $actions->add_form_action(new ObjectTableFormAction(InternshipOrganizerOrganisationManager :: ACTION_DELETE_LOCATION, Translation :: get('Delete')));
        }
        $this->set_form_actions($actions);
        $this->set_default_row_count(20);
    
    }

    static function handle_table_action()
    {
        $ids = self :: get_selected_ids(Utilities :: camelcase_to_underscores(__CLASS__));
        Request :: set_get(InternshipOrganizerOrganisationManager :: PARAM_LOCATION_ID, $ids);
    }
}
?>