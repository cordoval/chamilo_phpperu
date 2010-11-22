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

class InternshipOrganizerMentorBrowserTable extends ObjectTable
{
    const DEFAULT_NAME = 'mentor_browser_table';

    /**
     * Constructor
     */
    function __construct($browser, $parameters, $condition)
    {
        $model = new InternshipOrganizerMentorBrowserTableColumnModel();
        $renderer = new InternshipOrganizerMentorBrowserTableCellRenderer($browser);
        $data_provider = new InternshipOrganizerMentorBrowserTableDataProvider($browser, $condition);
        parent :: __construct($data_provider, self :: DEFAULT_NAME, $model, $renderer);
        $this->set_additional_parameters($parameters);

        $action = new ObjectTableFormActions(__NAMESPACE__);
        $action->set_action(InternshipOrganizerOrganisationManager :: PARAM_ACTION);
        $action->add_form_action(new ObjectTableFormAction(InternshipOrganizerOrganisationManager :: ACTION_DELETE_MENTOR, Translation :: get('RemoveSelected')));

        $this->set_form_actions($action);
        $this->set_default_row_count(20);
    }

    static function handle_table_action()
    {
        $class = Utilities :: get_classname_from_namespace(__CLASS__, true);
        $ids = self :: get_selected_ids($class);
        Request :: set_get(InternshipOrganizerOrganisationManager :: PARAM_ORGANISATION_ID, $ids);
    }
}
?>