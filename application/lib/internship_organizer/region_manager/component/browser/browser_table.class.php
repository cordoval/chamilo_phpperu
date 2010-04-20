<?php

require_once dirname(__FILE__) . '/browser_table_data_provider.class.php';
require_once dirname(__FILE__) . '/browser_table_column_model.class.php';
require_once dirname(__FILE__) . '/browser_table_cell_renderer.class.php';
/**
 * Table to display a set of learning objects.
 */
class InternshipOrganizerRegionBrowserTable extends ObjectTable
{
    const DEFAULT_NAME = 'region_browser_table';

    /**
     * Constructor
     * @see ContentObjectTable::ContentObjectTable()
     */
    function InternshipOrganizerRegionBrowserTable($browser, $parameters, $condition)
    {
        $model = new InternshipOrganizerRegionBrowserTableColumnModel();
        $renderer = new InternshipOrganizerRegionBrowserTableCellRenderer($browser);
        $data_provider = new InternshipOrganizerRegionBrowserTableDataProvider($browser, $condition);
        parent :: __construct($data_provider, InternshipOrganizerRegionBrowserTable :: DEFAULT_NAME, $model, $renderer);
        $this->set_additional_parameters($parameters);
        $actions = array();
        
        $actions[] = new ObjectTableFormAction(InternshipOrganizerRegionManager :: PARAM_REMOVE_SELECTED, Translation :: get('RemoveSelected'));
        $actions[] = new ObjectTableFormAction(InternshipOrganizerRegionManager :: PARAM_TRUNCATE_SELECTED, Translation :: get('TruncateSelected'));
        
        $this->set_form_actions($actions);
        $this->set_default_row_count(20);
    }
}
?>