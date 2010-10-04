<?php
/**
 * $Id: location_group_browser_table.class.php 191 2009-11-13 11:50:28Z chellee $
 * @package application.common.rights_editor_manager.component.location_course_group_bowser
 */
require_once dirname(__FILE__) . '/location_course_group_browser_table_data_provider.class.php';
require_once dirname(__FILE__) . '/location_course_group_browser_table_column_model.class.php';
require_once dirname(__FILE__) . '/location_course_group_browser_table_cell_renderer.class.php';
/**
 * Table to display a set of learning objects.
 */
class LocationCourseGroupBrowserTable extends ObjectTable
{
    const DEFAULT_NAME = 'location_course_group_browser_table';

    /**
     * Constructor
     * @see ContentObjectTable::ContentObjectTable()
     */
    function LocationCourseGroupBrowserTable($browser, $parameters, $condition)
    {
        $model = new LocationCourseGroupBrowserTableColumnModel($browser);
        $renderer = new LocationCourseGroupBrowserTableCellRenderer($browser);
        $data_provider = new LocationCourseGroupBrowserTableDataProvider($browser, $condition);
        parent :: __construct($data_provider, LocationCourseGroupBrowserTable :: DEFAULT_NAME, $model, $renderer);
        $this->set_additional_parameters($parameters);
        $this->set_default_row_count(20);
    }
}
?>