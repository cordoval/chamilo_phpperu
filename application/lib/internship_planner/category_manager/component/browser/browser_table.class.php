<?php

require_once dirname(__FILE__) . '/browser_table_data_provider.class.php';
require_once dirname(__FILE__) . '/browser_table_column_model.class.php';
require_once dirname(__FILE__) . '/browser_table_cell_renderer.class.php';
/**
 * Table to display a set of learning objects.
 */
class InternshipPlannerCategoryBrowserTable extends ObjectTable
{
    const DEFAULT_NAME = 'category_browser_table';

    /**
     * Constructor
     * @see ContentObjectTable::ContentObjectTable()
     */
    function InternshipPlannerCategoryBrowserTable($browser, $parameters, $condition)
    {
        $model = new InternshipPlannerCategoryBrowserTableColumnModel();
        $renderer = new InternshipPlannerCategoryBrowserTableCellRenderer($browser);
        $data_provider = new InternshipPlannerCategoryBrowserTableDataProvider($browser, $condition);
        parent :: __construct($data_provider, InternshipPlannerCategoryBrowserTable :: DEFAULT_NAME, $model, $renderer);
        $this->set_additional_parameters($parameters);
        $actions = array();
        
        $actions[] = new ObjectTableFormAction(InternshipPlannerCategoryManager :: PARAM_REMOVE_SELECTED, Translation :: get('RemoveSelected'));
        $actions[] = new ObjectTableFormAction(InternshipPlannerCategoryManager :: PARAM_TRUNCATE_SELECTED, Translation :: get('TruncateSelected'));
        
        $this->set_form_actions($actions);
        $this->set_default_row_count(20);
    }
}
?>