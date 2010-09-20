<?php

require_once dirname(__FILE__) . '/rel_user_browser_table_data_provider.class.php';
require_once dirname(__FILE__) . '/rel_user_browser_table_column_model.class.php';
require_once dirname(__FILE__) . '/rel_user_browser_table_cell_renderer.class.php';

class InternshipOrganizerMentorRelUserBrowserTable extends ObjectTable
{
    const DEFAULT_NAME = 'internship_organizer_mentor_rel_user_browser_table';

    /**
     * Constructor
     */
    function InternshipOrganizerMentorRelUserBrowserTable($browser, $parameters, $condition)
    {
        
        $model = new InternshipOrganizerMentorRelUserBrowserTableColumnModel($browser);
        $renderer = new InternshipOrganizerMentorRelUserBrowserTableCellRenderer($browser);
        $data_provider = new InternshipOrganizerMentorRelUserBrowserTableDataProvider($browser, $condition);
        parent :: __construct($data_provider, InternshipOrganizerMentorRelUserBrowserTable :: DEFAULT_NAME, $model, $renderer);
        
        $actions = new ObjectTableFormActions(InternshipOrganizerAgreementManager :: PARAM_ACTION);
        
        if (InternshipOrganizerRights :: is_allowed_in_internship_organizers_subtree(InternshipOrganizerRights :: RIGHT_EDIT, InternshipOrganizerRights :: LOCATION_ORGANISATION, InternshipOrganizerRights :: TYPE_INTERNSHIP_ORGANIZER_COMPONENT))
        {
            $actions->add_form_action(new ObjectTableFormAction(InternshipOrganizerOrganisationManager :: ACTION_UNSUBSCRIBE_MENTOR_USER, Translation :: get('Unsubscribe')));
        }
        $this->set_form_actions($actions);
        $this->set_default_row_count(20);
    
    }

    static function handle_table_action()
    {
        $ids = self :: get_selected_ids(Utilities :: camelcase_to_underscores(__CLASS__));
        Request :: set_get(InternshipOrganizerOrganisationManager :: PARAM_MENTOR_REL_USER_ID, $ids);
    }
}
?>