<?php

require_once dirname(__FILE__) . '/user_browser_table_data_provider.class.php';
require_once dirname(__FILE__) . '/user_browser_table_column_model.class.php';
require_once dirname(__FILE__) . '/user_browser_table_cell_renderer.class.php';

class InternshipOrganizerPeriodUserBrowserTable extends ObjectTable
{
    const DEFAULT_NAME = 'internship_organizer_period_user_browser_table';

    /**
     * Constructor
     */
    function InternshipOrganizerPeriodUserBrowserTable($browser, $parameters, $condition, $user_type)
    {
        
        $model = new InternshipOrganizerPeriodUserBrowserTableColumnModel($browser);
        $renderer = new InternshipOrganizerPeriodUserBrowserTableCellRenderer($browser, $user_type);
        $data_provider = new InternshipOrganizerPeriodUserBrowserTableDataProvider($browser, $condition);
        parent :: __construct($data_provider, InternshipOrganizerPeriodUserBrowserTable :: DEFAULT_NAME, $model, $renderer);
        $actions = new ObjectTableFormActions(InternshipOrganizerPeriodManager :: PARAM_ACTION);
        if($user_type == InternshipOrganizerUserType::STUDENT){
        	$actions->add_form_action(new ObjectTableFormAction(InternshipOrganizerPeriodManager :: ACTION_CREATE_AGREEMENT, Translation :: get('CreateInternshipOrganizerAgreement')));
        }
      
        $this->set_form_actions($actions);
        $this->set_default_row_count(20);
    
    }

    static function handle_table_action()
    {
        $ids = self :: get_selected_ids(Utilities :: camelcase_to_underscores(__CLASS__));
        Request :: set_get(InternshipOrganizerPeriodManager :: PARAM_USER_ID, $ids);
    }
}
?>