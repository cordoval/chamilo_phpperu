<?php
namespace application\survey;

use common\libraries\Request;
use common\libraries\Utilities;
use common\libraries\ObjectTable;
use common\libraries\ObjectTableFormAction;
use common\libraries\ObjectTableFormActions;
use common\libraries\Translation;

require_once dirname(__FILE__) . '/publication_browser_table_data_provider.class.php';
require_once dirname(__FILE__) . '/publication_browser_table_column_model.class.php';
require_once dirname(__FILE__) . '/publication_browser_table_cell_renderer.class.php';

class SurveyPublicationBrowserTable extends ObjectTable
{
    const DEFAULT_NAME = 'survey_publication_browser_table';

    /**
     * Constructor
     */
    function __construct($browser, $parameters, $condition)
    {
        $model = new SurveyPublicationBrowserTableColumnModel();
        $renderer = new SurveyPublicationBrowserTableCellRenderer($browser);
        $data_provider = new SurveyPublicationBrowserTableDataProvider($browser, $condition);
        parent :: __construct($data_provider, self :: DEFAULT_NAME, $model, $renderer);
        $this->set_additional_parameters($parameters);

        $action = new ObjectTableFormActions(__NAMESPACE__);

        //        if (SurveyRights :: is_allowed_in_surveys_subtree(SurveyRights :: RIGHT_VIEW, SurveyRights :: LOCATION_REPORTING, SurveyRights :: TYPE_COMPONENT))
        //        {
        //            $action->add_form_action(new ObjectTableFormAction(SurveyReportingManager :: ACTION_REPORTING, Translation :: get('Reporting'), false));
        //        }


        if ($browser->get_user()->is_platform_admin())
        {
            $action->add_form_action(new ObjectTableFormAction(SurveyManager :: ACTION_DELETE, Translation :: get('RemoveSelected', array(), Utilities :: COMMON_LIBRARIES), true));

     //            $action->add_form_action(new ObjectTableFormAction(SurveyManager :: ACTION_EDIT_RIGHTS, Translation :: get('ManageRights'), false));
        //            $action->add_form_action(new ObjectTableFormAction(SurveyManager :: ACTION_MAIL_INVITEES, Translation :: get('InviteParticipants'), false));
        //            $action->add_form_action(new ObjectTableFormAction(SurveyManager :: ACTION_EXPORT, Translation :: get('ExportToExcel'), false));


        }

        $this->set_form_actions($action);
        $this->set_default_row_count(20);
    }

    static function handle_table_action()
    {
        $class = Utilities :: get_classname_from_namespace(__CLASS__, true);
        $ids = self :: get_selected_ids($class);
        Request :: set_get(SurveyManager :: PARAM_PUBLICATION_ID, $ids);
    }
}
?>