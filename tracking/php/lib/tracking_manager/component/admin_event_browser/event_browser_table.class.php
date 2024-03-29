<?php
namespace tracking;

use common\libraries\Utilities;
use common\libraries\Translation;
use common\libraries\Request;
use common\libraries\ObjectTable;
use common\libraries\ObjectTableFormAction;
use common\libraries\ObjectTableFormActions;

/**
 * $Id: event_browser_table.class.php 213 2009-11-13 13:38:50Z vanpouckesven $
 * @package tracking.lib.tracking_manager.component.admin_event_browser
 */
require_once dirname(__FILE__) . '/event_browser_table_data_provider.class.php';
require_once dirname(__FILE__) . '/event_browser_table_column_model.class.php';
require_once dirname(__FILE__) . '/event_browser_table_cell_renderer.class.php';
/**
 * Table to display a set of learning objects.
 */
class EventBrowserTable extends ObjectTable
{

    /**
     * Constructor
     * @see ContentObjectTable::ContentObjectTable()
     */
    function __construct($browser, $name, $parameters, $condition)
    {
        $model = new EventBrowserTableColumnModel();
        $renderer = new EventBrowserTableCellRenderer($browser);
        $data_provider = new EventBrowserTableDataProvider($browser, $condition);
        parent :: __construct($data_provider, Utilities :: get_classname_from_namespace(__CLASS__, true), $model, $renderer);
        $this->set_additional_parameters($parameters);

        $actions = new ObjectTableFormActions(__NAMESPACE__);
        $actions->add_form_action(new ObjectTableFormAction(TrackingManager :: ACTION_ACTIVATE_EVENT, Translation :: get('EnableSelectedEvents'), false));
        $actions->add_form_action(new ObjectTableFormAction(TrackingManager :: ACTION_DEACTIVATE_EVENT, Translation :: get('DisableSelectedEvents'), false));
        $actions->add_form_action(new ObjectTableFormAction(TrackingManager :: ACTION_EMPTY_EVENT_TRACKERS, Translation :: get('EmptySelectedEvents')));

        $this->set_form_actions($actions);
        $this->set_default_row_count(20);
    }

	static function handle_table_action()
    {
        $class = Utilities :: get_classname_from_namespace(__CLASS__, true);
        $ids = self :: get_selected_ids($class);
        Request :: set_get(TrackingManager :: PARAM_EVENT_ID, $ids);
    }
}
?>