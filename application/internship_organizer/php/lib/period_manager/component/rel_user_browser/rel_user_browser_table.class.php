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

class InternshipOrganizerPeriodRelUserBrowserTable extends ObjectTable
{
    const DEFAULT_NAME = 'internship_organizer_period_rel_user_browser_table';

    /**
     * Constructor
     */
    function __construct($browser, $parameters, $condition)
    {

        $model = new InternshipOrganizerPeriodRelUserBrowserTableColumnModel($browser);
        $renderer = new InternshipOrganizerPeriodRelUserBrowserTableCellRenderer($browser);
        $data_provider = new InternshipOrganizerPeriodRelUserBrowserTableDataProvider($browser, $condition);
        parent :: __construct($data_provider, InternshipOrganizerPeriodRelUserBrowserTable :: DEFAULT_NAME, $model, $renderer);
        $actions = new ObjectTableFormActions(__NAMESPACE__, InternshipOrganizerPeriodManager :: PARAM_ACTION);

        if (InternshipOrganizerRights :: is_allowed_in_internship_organizers_subtree(InternshipOrganizerRights :: SUBSCRIBE_AGREEMENT_USER_RIGHT, $browser->get_period()->get_id(), InternshipOrganizerRights :: TYPE_PERIOD))
        {
            $actions->add_form_action(new ObjectTableFormAction(InternshipOrganizerPeriodManager :: ACTION_UNSUBSCRIBE_USER, Translation :: get('Unsubscribe')));
        }
        $this->set_form_actions($actions);
        $this->set_default_row_count(20);

    }

    static function handle_table_action()
    {
        $class = Utilities :: get_classname_from_namespace(__CLASS__, true);
        $ids = self :: get_selected_ids($class);
        Request :: set_get(InternshipOrganizerPeriodManager :: PARAM_PERIOD_REL_USER_ID, $ids);
    }
}
?>