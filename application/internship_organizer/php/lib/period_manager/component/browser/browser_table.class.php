<?php
namespace application\internship_organizer;

use common\libraries\Utilities;
use common\libraries\ObjectTable;
use common\libraries\Translation;
use common\libraries\ObjectTableFormAction;
use common\libraries\ObjectTableFormActions;
use common\libraries\Request;

require_once dirname(__FILE__) . '/browser_table_data_provider.class.php';
require_once dirname(__FILE__) . '/browser_table_column_model.class.php';
require_once dirname(__FILE__) . '/browser_table_cell_renderer.class.php';
/**
 * Table to display a set of learning objects.
 */
class InternshipOrganizerPeriodBrowserTable extends ObjectTable
{
    const DEFAULT_NAME = 'internship_organizer_period_browser_table';

    /**
     * Constructor
     * @see ContentObjectTable::ContentObjectTable()
     */
    function __construct($browser, $parameters, $condition)
    {
        $model = new InternshipOrganizerPeriodBrowserTableColumnModel();
        $renderer = new InternshipOrganizerPeriodBrowserTableCellRenderer($browser);
        $data_provider = new InternshipOrganizerPeriodBrowserTableDataProvider($browser, $condition);
        parent :: __construct($data_provider, InternshipOrganizerPeriodBrowserTable :: DEFAULT_NAME, $model, $renderer);
        $this->set_additional_parameters($parameters);
        $actions = new ObjectTableFormActions(__NAMESPACE__, InternshipOrganizerPeriodManager :: PARAM_ACTION);
        if (InternshipOrganizerRights :: is_allowed_in_internship_organizers_subtree(InternshipOrganizerRights :: RIGHT_DELETE, InternshipOrganizerRights :: LOCATION_PERIOD, InternshipOrganizerRights :: TYPE_PERIOD))
        {
            $actions->add_form_action(new ObjectTableFormAction(InternshipOrganizerPeriodManager :: ACTION_DELETE_PERIOD, Translation :: get('Delete')));
        }
        $this->set_form_actions($actions);
        $this->set_default_row_count(20);

    }

    static function handle_table_action()
    {
        $class = Utilities :: get_classname_from_namespace(__CLASS__, true);
        $ids = self :: get_selected_ids($class);
        Request :: set_get(InternshipOrganizerPeriodManager :: PARAM_PERIOD_ID, $ids);
    }
}
?>