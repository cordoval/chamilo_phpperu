<?php
namespace application\internship_organizer;

use common\libraries\Utilities;
use common\libraries\ObjectTable;
use common\libraries\Translation;
use common\libraries\ObjectTableFormAction;
use common\libraries\ObjectTableFormActions;
use common\libraries\Request;

require_once dirname(__FILE__) . '/rel_user_browser_table_data_provider.class.php';
require_once dirname(__FILE__) . '/rel_user_browser_table_column_model.class.php';
require_once dirname(__FILE__) . '/rel_user_browser_table_cell_renderer.class.php';

class InternshipOrganizerOrganisationRelUserBrowserTable extends ObjectTable
{
    const DEFAULT_NAME = 'internship_organizer_organisation_rel_user_browser_table';

    /**
     * Constructor
     */
    function InternshipOrganizerOrganisationRelUserBrowserTable($browser, $parameters, $condition)
    {

        $model = new InternshipOrganizerOrganisationRelUserBrowserTableColumnModel($browser);
        $renderer = new InternshipOrganizerOrganisationRelUserBrowserTableCellRenderer($browser);
        $data_provider = new InternshipOrganizerOrganisationRelUserBrowserTableDataProvider($browser, $condition);
        parent :: __construct($data_provider, InternshipOrganizerOrganisationRelUserBrowserTable :: DEFAULT_NAME, $model, $renderer);

        $actions = new ObjectTableFormActions(__NAMESPACE__, InternshipOrganizerAgreementManager :: PARAM_ACTION);

        if (InternshipOrganizerRights :: is_allowed_in_internship_organizers_subtree(InternshipOrganizerRights :: RIGHT_EDIT, InternshipOrganizerRights :: LOCATION_ORGANISATION, InternshipOrganizerRights :: TYPE_COMPONENT))
        {
            $actions->add_form_action(new ObjectTableFormAction(InternshipOrganizerOrganisationManager :: ACTION_UNSUBSCRIBE_USER, Translation :: get('Unsubscribe')));
        }
        $this->set_form_actions($actions);
        $this->set_default_row_count(20);

    }

    static function handle_table_action()
    {
        $class = Utilities :: get_classname_from_namespace(__CLASS__, true);
        $ids = self :: get_selected_ids($class);
        Request :: set_get(InternshipOrganizerOrganisationManager :: PARAM_ORGANISATION_REL_USER_ID, $ids);
    }
}
?>