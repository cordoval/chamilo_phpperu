<?php
namespace menu;
use common\libraries\Utilities;
use common\libraries\Translation;
use common\libraries\Request;
use common\libraries\ObjectTable;
use common\libraries\ObjectTableFormActions;
use common\libraries\ObjectTableFormAction;

/**
 * $Id: navigation_item_browser_table.class.php 223 2009-11-13 14:39:28Z vanpouckesven $
 * @package menu.lib.menu_manager.component.navigation_item_browser
 */
require_once dirname(__FILE__) . '/navigation_item_browser_table_data_provider.class.php';
require_once dirname(__FILE__) . '/navigation_item_browser_table_column_model.class.php';
require_once dirname(__FILE__) . '/navigation_item_browser_table_cell_renderer.class.php';
/**
 * Table to display a set of learning objects.
 */
class NavigationItemBrowserTable extends ObjectTable
{
    const DEFAULT_NAME = 'navigation_item_browser_table';

    /**
     * Constructor
     */
    function __construct($browser, $parameters, $condition)
    {
        $model = new NavigationItemBrowserTableColumnModel($browser);
        $renderer = new NavigationItemBrowserTableCellRenderer($browser);
        $data_provider = new NavigationItemBrowserTableDataProvider($browser, $condition);
        parent :: __construct($data_provider, NavigationItemBrowserTable :: DEFAULT_NAME, $model, $renderer);
        $actions = new ObjectTableFormActions(__NAMESPACE__);

        $actions->add_form_action(new ObjectTableFormAction(MenuManager :: ACTION_DELETE, Translation :: get('RemoveSelected', null , Utilities :: COMMON_LIBRARIES)));

        $user = $browser->get_user();
        $this->set_form_actions($actions);
        $this->set_default_row_count(10);
    }

	static function handle_table_action()
    {
        $class = Utilities :: get_classname_from_namespace(__CLASS__, true);
        $ids = self :: get_selected_ids($class);
        Request :: set_get(MenuManager :: PARAM_ITEM, $ids);
    }
}
?>