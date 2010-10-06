<?php

require_once dirname(__FILE__).'/user_browser_table_data_provider.class.php';
require_once dirname(__FILE__).'/user_browser_table_column_model.class.php';
require_once dirname(__FILE__).'/user_browser_table_cell_renderer.class.php';

class SurveyUserBrowserTable extends ObjectTable
{
	const TYPE_INVITEES = 1;
	const TYPE_NO_PARTICIPANTS = 2;
	
	const DEFAULT_NAME = 'survey_user_browser_table';

	/**
	 * Constructor
	 */
	function SurveyUserBrowserTable($browser, $parameters, $condition, $publication_id, $type)
	{
		
	
		$model = new SurveyUserBrowserTableColumnModel($browser);
		$renderer = new SurveyUserBrowserTableCellRenderer($browser, $publication_id, $type);
		$data_provider = new SurveyUserBrowserTableDataProvider($browser, $condition);
		parent :: __construct($data_provider,SurveyUserBrowserTable :: DEFAULT_NAME, $model, $renderer);
		$actions = array();
		$this->set_additional_parameters($parameters);
	  	
		$action = new ObjectTableFormActions();
        
        
		if (SurveyRights :: is_allowed_in_surveys_subtree(SurveyRights :: RIGHT_INVITE, SurveyRights :: LOCATION_PARTICIPANT_BROWSER, SurveyRights :: TYPE_SURVEY_COMPONENT ))
        {
           $action->add_form_action(new ObjectTableFormAction(SurveyManager :: ACTION_CANCEL_INVITATION, Translation :: get('CancelInvitation'),true));
        }
        
        $this->set_form_actions($action);
        $this->set_default_row_count(20);
    }
    
	static function handle_table_action()
    {
        $ids = self :: get_selected_ids(Utilities :: camelcase_to_underscores(__CLASS__));
        Request :: set_get(SurveyManager :: PARAM_INVITEE_ID, $ids);
    }
}
?>