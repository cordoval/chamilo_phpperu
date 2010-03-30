<?php

require_once dirname(__FILE__) . '/rel_location_browser_table_data_provider.class.php';
require_once dirname(__FILE__) . '/rel_location_browser_table_column_model.class.php';
require_once dirname(__FILE__) . '/rel_location_browser_table_cell_renderer.class.php';

class InternshipPlannerCategoryRelLocationBrowserTable extends ObjectTable
{
    const DEFAULT_NAME = 'category_rel_location_browser_table';

    /**
     * Constructor
     * @see ContentObjectTable::ContentObjectTable()
     */
    function InternshipPlannerCategoryRelLocationBrowserTable($browser, $parameters, $condition)
    {
        $model = new InternshipPlannerCategoryRelLocationBrowserTableColumnModel();
        $renderer = new InternshipPlannerCategoryRelLocationBrowserTableCellRenderer($browser);
        $data_provider = new InternshipPlannerCategoryRelLocationBrowserTableDataProvider($browser, $condition);
        parent :: __construct($data_provider, InternshipPlannerCategoryRelLocationBrowserTable :: DEFAULT_NAME, $model, $renderer);
        $this->set_additional_parameters($parameters);
        $actions = array();
        
        $actions[] = new ObjectTableFormAction(InternshipPlannerCategoryManager :: PARAM_UNSUBSCRIBE_SELECTED, Translation :: get('UnsubscribeSelected'), false);
        
        $this->set_form_actions($actions);
        $this->set_default_row_count(20);
    }

    /**
     * A typical ObjectTable would get the database-id of the object as a
     * unique identifier. InternshipPlannerCategoryRelLocation has no such field since it's
     * a relation, so we need to overwrite this function here.
     */
//    function get_objects($offset, $count, $order_column)
//    {
//        $categoryrellocations = $this->get_data_provider()->get_objects($offset, $count, $this->get_column_model()->get_order_column($order_column - ($this->has_form_actions() ? 1 : 0)));
//        $table_data = array();
//        $column_count = $this->get_column_model()->get_column_count();
//        while ($categoryreluser = $categoryrelusers->next_result())
//        {
//            $row = array();
//            if ($this->has_form_actions())
//            {
//                $row[] = $categoryreluser->get_category_id() . '|' . $categoryreluser->get_location_id();
//            }
//            for($i = 0; $i < $column_count; $i ++)
//            {
//                $row[] = $this->get_cell_renderer()->render_cell($this->get_column_model()->get_column($i), $categoryreluser);
//            }
//            $table_data[] = $row;
//        }
//        return $table_data;
//    }
}
?>