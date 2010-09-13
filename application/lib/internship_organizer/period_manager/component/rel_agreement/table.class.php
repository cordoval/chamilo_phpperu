<?php

require_once dirname(__FILE__) . '/table_data_provider.class.php';
require_once dirname(__FILE__) . '/table_column_model.class.php';
require_once dirname(__FILE__) . '/table_cell_renderer.class.php';
require_once dirname(__FILE__) . '/../../period_manager.class.php';

class InternshipOrganizerPeriodRelAgreementBrowserTable extends ObjectTable
{
    const DEFAULT_NAME = 'internship_organizer_period_rel_agreement_browser_table';

    /**
     * Constructor
     */
    function InternshipOrganizerPeriodRelAgreementBrowserTable($browser, $parameters, $condition)
    {
        $model = new InternshipOrganizerPeriodRelAgreementBrowserTableColumnModel();
        $renderer = new InternshipOrganizerPeriodRelAgreementBrowserTableCellRenderer($browser);
        $data_provider = new InternshipOrganizerPeriodRelAgreementBrowserTableDataProvider($browser, $condition);
        parent :: __construct($data_provider, self :: DEFAULT_NAME, $model, $renderer);
        $actions = new ObjectTableFormActions(InternshipOrganizerPeriodManager :: PARAM_ACTION);
        
        if (InternshipOrganizerRights :: is_allowed_in_internship_organizers_subtree(InternshipOrganizerRights :: DELETE_AGREEMENT_RIGHT, $browser->get_period()->get_id(), InternshipOrganizerRights :: TYPE_PERIOD))
        {
            $actions->add_form_action(new ObjectTableFormAction(InternshipOrganizerPeriodManager :: ACTION_DELETE_AGREEMENT, Translation :: get('Delete')));
        }
        $this->set_form_actions($actions);
        $this->set_default_row_count(20);
    
    }

    static function handle_table_action()
    {
        $ids = self :: get_selected_ids(Utilities :: camelcase_to_underscores(__CLASS__));
        Request :: set_get(InternshipOrganizerPeriodManager :: PARAM_AGREEMENT_ID, $ids);
    }
}
?>