<?php
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
    function NavigationItemBrowserTable($browser, $parameters, $condition)
    {
        $model = new NavigationItemBrowserTableColumnModel($browser);
        $renderer = new NavigationItemBrowserTableCellRenderer($browser);
        $data_provider = new NavigationItemBrowserTableDataProvider($browser, $condition);
        parent :: __construct($data_provider, NavigationItemBrowserTable :: DEFAULT_NAME, $model, $renderer);
        $actions = new ObjectTableFormActions();
        
        $actions->add_form_action(new ObjectTableFormAction(MenuManager :: ACTION_DELETE, Translation :: get('RemoveSelected')));
        
        $user = $browser->get_user();
        $this->set_form_actions($actions);
        $this->set_default_row_count(10);
    }
    
	static function handle_table_action()
    {
        $ids = self :: get_selected_ids(Utilities :: camelcase_to_underscores(__CLASS__));
        Request :: set_get(MenuManager :: PARAM_ITEM, $ids);
    }
}
?>