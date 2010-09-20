<?php
/**
 * $Id: assessment_browser_table_column_model.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.complex_builder.assessment.component.browser
 */

/**
 * Table column model for the repository browser table
 */
class AssessmentBrowserTableColumnModel extends ComplexBrowserTableColumnModel
{

    /**
     * Constructor
     */
    function AssessmentBrowserTableColumnModel($browser)
    {
        $columns[] = new StaticTableColumn(Translation :: get('Weight'));
        parent :: __construct($browser, $columns);
    }
}
?>