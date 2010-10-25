<?php
/**
 * $Id: course_sections_browser_table.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.tool.course_sections.component.course_sections_browser
 */
require_once dirname(__FILE__) . '/course_sections_browser_table_data_provider.class.php';
require_once dirname(__FILE__) . '/course_sections_browser_table_column_model.class.php';
require_once dirname(__FILE__) . '/course_sections_browser_table_cell_renderer.class.php';
/**
 * Table to display a set of courses.
 */
class CourseSectionsBrowserTable extends ObjectTable
{
    const DEFAULT_NAME = 'course_section_browser_table';

    /**
     * Constructor
     */
    function CourseSectionsBrowserTable($browser, $parameters, $condition)
    {
        $model = new CourseSectionsBrowserTableColumnModel();
        $renderer = new CourseSectionsBrowserTableCellRenderer($browser);
        $data_provider = new CourseSectionsBrowserTableDataProvider($browser, $condition);
        parent :: __construct($data_provider, CourseSectionsBrowserTable :: DEFAULT_NAME, $model, $renderer);
        
        $actions = array();
        
        $actions[] = new ObjectTableFormAction(CourseSectionsTool :: PARAM_REMOVE_SELECTED, Translation :: get('RemoveSelected'));
        
        $actions[CourseSectionsTool :: PARAM_REMOVE_SELECTED] = Translation :: get('RemoveSelected');
        //$this->set_form_actions($actions);
        

        $this->set_default_row_count(20);
    }
}
?>