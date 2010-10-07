<?php

require_once dirname(__FILE__) . '/publication_browser_table_data_provider.class.php';
require_once dirname(__FILE__) . '/publication_browser_table_column_model.class.php';
require_once dirname(__FILE__) . '/publication_browser_table_cell_renderer.class.php';
require_once dirname(__FILE__) . '/../../survey_manager.class.php';

class SurveyPublicationBrowserTable extends ObjectTable
{
    const DEFAULT_NAME = 'survey_publication_browser_table';

    /**
     * Constructor
     */
    function SurveyPublicationBrowserTable($browser, $parameters, $condition)
    {
        $model = new SurveyPublicationBrowserTableColumnModel();
        $renderer = new SurveyPublicationBrowserTableCellRenderer($browser);
        $data_provider = new SurveyPublicationBrowserTableDataProvider($browser, $condition);
        parent :: __construct($data_provider, self :: DEFAULT_NAME, $model, $renderer);
        $this->set_additional_parameters($parameters);
        
        $action = new ObjectTableFormActions();
        
        $action->add_form_action(new ObjectTableFormAction(SurveyManager :: ACTION_REPORTING_FILTER, Translation :: get('ReportingSelected'),false));
        
        if ($browser->get_user()->is_platform_admin())
        {
           $action->add_form_action(new ObjectTableFormAction(SurveyManager :: ACTION_DELETE, Translation :: get('RemoveSelected'),true));
           $action->add_form_action(new ObjectTableFormAction(SurveyManager :: ACTION_EDIT_RIGHTS, Translation :: get('ManageRights'),false));
           $action->add_form_action(new ObjectTableFormAction(SurveyManager :: ACTION_MAIL_INVITEES, Translation :: get('InviteParticipants'),false));
           $action->add_form_action(new ObjectTableFormAction(SurveyManager :: ACTION_EXCEL_EXPORT, Translation :: get('ExportToExcel'),false));
           
        }
        
        $this->set_form_actions($action);
        $this->set_default_row_count(20);
    }
    
	static function handle_table_action()
    {
        $ids = self :: get_selected_ids(Utilities :: camelcase_to_underscores(__CLASS__));
        Request :: set_get(SurveyManager :: PARAM_PUBLICATION_ID, $ids);
    }
}
?>