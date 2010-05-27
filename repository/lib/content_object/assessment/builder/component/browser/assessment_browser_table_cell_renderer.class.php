<?php
/**
 * $Id: assessment_browser_table_cell_renderer.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.complex_builder.assessment.component.browser
 */
require_once Path :: get_repository_path() . 'lib/repository_manager/component/complex_browser/complex_browser_table_cell_renderer.class.php';
require_once dirname(__FILE__) . '/assessment_browser_table_column_model.class.php';
/**
 * Cell rendere for the learning object browser table
 */
class AssessmentBrowserTableCellRenderer extends ComplexBrowserTableCellRenderer
{

    /**
     * Constructor
     * @param RepositoryManagerBrowserComponent $browser
     */
    function AssessmentBrowserTableCellRenderer($browser, $condition)
    {
        parent :: __construct($browser, $condition);
    }

    // Inherited
    function render_cell($column, $cloi)
    {
        $return = parent :: render_cell($column, $cloi);
        if ($return != '')
            return $return;
        
        switch ($column->get_name())
        {
            case Translation :: get('Weight') :
                return $cloi->get_weight();
        }
        
        return '';
    }
}
?>