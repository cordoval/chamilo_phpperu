<?php
/**
 * $Id: category_browser_table.class.php 205 2009-11-13 12:57:33Z vanpouckesven $
 * @package application.common.category_manager.component.category_browser
 */
require_once dirname(__FILE__) . '/category_browser_table_data_provider.class.php';
require_once dirname(__FILE__) . '/category_browser_table_column_model.class.php';
require_once dirname(__FILE__) . '/category_browser_table_cell_renderer.class.php';
/**
 * Table to display a set of learning objects.
 */
class CategoryBrowserTable extends ObjectTable
{
    const DEFAULT_NAME = 'category_table';

    /**
     * Constructor
     * @see ContentObjectTable::ContentObjectTable()
     */
    function CategoryBrowserTable($browser, $parameters, $condition)
    {
        $model = new CategoryBrowserTableColumnModel($browser);
        $renderer = new CategoryBrowserTableCellRenderer($browser);
        $data_provider = new CategoryBrowserTableDataProvider($browser, $condition);
        parent :: __construct($data_provider, CategoryBrowserTable :: DEFAULT_NAME, $model, $renderer);
        $this->set_additional_parameters($parameters);
        
        if ($browser->get_user() && $browser->get_user()->is_platform_admin())
        {
            $actions = array();
            
            $actions[] = new ObjectTableFormAction(CategoryManager :: PARAM_REMOVE_SELECTED_CATEGORIES, Translation :: get('RemoveSelected'));
            $actions[] = new ObjectTableFormAction(CategoryManager :: PARAM_MOVE_SELECTED_CATEGORIES, Translation :: get('MoveSelected'), false);
            
            $this->set_form_actions($actions);
        }
        
        $this->set_default_row_count(20);
    }
}
?>