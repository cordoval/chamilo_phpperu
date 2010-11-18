<?php
namespace application\internship_organizer;

use common\libraries\WebApplication;
use common\libraries\Utilities;
use common\libraries\ObjectTable;
use common\libraries\Translation;
use common\libraries\ObjectTableFormAction;
use common\libraries\ObjectTableFormActions;
use common\libraries\Request;

require_once WebApplication :: get_application_class_lib_path('internship_organizer') . 'agreement_manager/component/moment_browser/browser_table_data_provider.class.php';
require_once WebApplication :: get_application_class_lib_path('internship_organizer') . 'agreement_manager/component/moment_browser/browser_table_column_model.class.php';
require_once WebApplication :: get_application_class_lib_path('internship_organizer') . 'agreement_manager/component/moment_browser/browser_table_cell_renderer.class.php';
require_once WebApplication :: get_application_class_lib_path('internship_organizer') . 'agreement_manager/agreement_manager.class.php';

class InternshipOrganizerMomentBrowserTable extends ObjectTable
{
    const DEFAULT_NAME = 'internship_organizer_moment_browser_table';

    /**
     * Constructor
     */
    function InternshipOrganizerMomentBrowserTable($browser, $parameters, $condition)
    {
        $model = new InternshipOrganizerMomentBrowserTableColumnModel();
        $renderer = new InternshipOrganizerMomentBrowserTableCellRenderer($browser);
        $data_provider = new InternshipOrganizerMomentBrowserTableDataProvider($browser, $condition);
        parent :: __construct($data_provider, self :: DEFAULT_NAME, $model, $renderer);
        $this->set_additional_parameters($parameters);
        $this->set_default_row_count(20);

        $actions = new ObjectTableFormActions(__NAMESPACE__, InternshipOrganizerPeriodManager :: PARAM_ACTION);

//        if (InternshipOrganizerRights :: is_allowed_in_internship_organizers_subtree(InternshipOrganizerRights :: RIGHT_DELETE, $browser->get_moment()->get_id(), InternshipOrganizerRights :: TYPE_MOMENT))
//        {
            $actions->add_form_action(new ObjectTableFormAction(InternshipOrganizerAgreementManager :: ACTION_DELETE_MOMENT, Translation :: get('Delete')));
//        }
        $this->set_form_actions($actions);
        $this->set_default_row_count(20);

    }

    static function handle_table_action()
    {
        $class = Utilities :: get_classname_from_namespace(__CLASS__, true);
        $ids = self :: get_selected_ids($class);
        Request :: set_get(InternshipOrganizerAgreementManager :: PARAM_MOMENT_ID, $ids);
    }
}
?>