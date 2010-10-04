<?php
/**
 * $Id: object_browser_table.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.complex_builder.assessment.component.assessment_merger
 */
require_once dirname(__FILE__) . '/object_browser_table_data_provider.class.php';
require_once dirname(__FILE__) . '/object_browser_table_column_model.class.php';
require_once dirname(__FILE__) . '/object_browser_table_cell_renderer.class.php';
/**
 * Table to display a set of learning objects.
 */
class ObjectBrowserTable extends ObjectTable
{
    const DEFAULT_NAME = 'repository_browser_table';

    /**
     * Constructor
     * @see ContentObjectTable::ContentObjectTable()
     */
    function ObjectBrowserTable($browser, $parameters, $condition)
    {
    	$model = new ObjectBrowserTableColumnModel();
        $renderer = new ObjectBrowserTableCellRenderer($browser);
        $data_provider = new ObjectBrowserTableDataProvider($browser, $condition);
        parent :: __construct($data_provider, ObjectBrowserTable :: DEFAULT_NAME, $model, $renderer);
        
        $actions = array();
        $actions[] = new ObjectTableFormAction(AssessmentBuilder :: PARAM_ADD_SELECTED_QUESTIONS, Translation :: get('AddSelectedQuestions'), false);
        $this->set_form_actions($actions);
        
        $this->set_additional_parameters($parameters);
        $this->set_default_row_count(20);
    }
}
?>