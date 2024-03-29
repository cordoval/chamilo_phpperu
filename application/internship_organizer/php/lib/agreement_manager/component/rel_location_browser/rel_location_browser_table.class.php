<?php
namespace application\internship_organizer;

use common\libraries\Utilities;
use common\libraries\WebApplication;
use common\libraries\ObjectTable;

require_once WebApplication :: get_application_class_lib_path('internship_organizer') . 'agreement_manager/component/rel_location_browser/rel_location_browser_table_data_provider.class.php';
require_once WebApplication :: get_application_class_lib_path('internship_organizer') . 'agreement_manager/component/rel_location_browser/rel_location_browser_table_column_model.class.php';
require_once WebApplication :: get_application_class_lib_path('internship_organizer') . 'agreement_manager/component/rel_location_browser/rel_location_browser_table_cell_renderer.class.php';

class InternshipOrganizerAgreementRelLocationBrowserTable extends ObjectTable
{
    const DEFAULT_NAME = 'internship_organizer_agreement_rel_location_browser_table';

    /**
     * Constructor
     * @see ContentObjectTable::ContentObjectTable()
     */
    function __construct($browser, $parameters, $condition)
    {
        $model = new InternshipOrganizerAgreementRelLocationBrowserTableColumnModel();
        $renderer = new InternshipOrganizerAgreementRelLocationBrowserTableCellRenderer($browser);
        $data_provider = new InternshipOrganizerAgreementRelLocationBrowserTableDataProvider($browser, $condition);
        parent :: __construct($data_provider, InternshipOrganizerAgreementRelLocationBrowserTable :: DEFAULT_NAME, $model, $renderer);
        $this->set_additional_parameters($parameters);

        //        $actions = new ObjectTableFormActions(__NAMESPACE__, InternshipOrganizerAgreementManager :: PARAM_ACTION);
        //
        //        if (InternshipOrganizerRights :: is_allowed_in_internship_organizers_subtree(InternshipOrganizerRights :: RIGHT_EDIT, InternshipOrganizerRights :: LOCATION_ORGANISATION, InternshipOrganizerRights :: TYPE_COMPONENT))
        //        {
        //            $actions->add_form_action(new ObjectTableFormAction(InternshipOrganizerOrganisationManager :: ACTION_DELETE_LOCATION, Translation :: get('Delete')));
        //        }
        //        $this->set_form_actions($actions);
        $this->set_default_row_count(20);

    }

    static function handle_table_action()
    {
        $class = Utilities :: get_classname_from_namespace(__CLASS__, true);

     //        $ids = self :: get_selected_ids($class);
    //        Request :: set_get(InternshipOrganizerOrganisationManager :: PARAM_LOCATION_ID, $ids);
    }

}
?>