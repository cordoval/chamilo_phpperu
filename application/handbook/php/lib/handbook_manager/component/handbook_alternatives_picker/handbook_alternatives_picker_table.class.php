<?php
namespace application\handbook;
use common\libraries\ObjectTable;

require_once dirname(__FILE__) . '/handbook_alternatives_picker_table_data_provider.class.php';
require_once dirname(__FILE__) . '/handbook_alternatives_picker_table_column_model.class.php';
require_once dirname(__FILE__) . '/handbook_alternatives_picker_table_cell_renderer.class.php';


/**
 * Table to display a set of users with handbook_publications.
 */
class HandbookAlternativesPickerItemTable extends ObjectTable
{
    const DEFAULT_NAME = 'handbook_alternatives_picker_table';

    /**
     * Constructor
    */
    function __construct($browser, $parameters, $condition)
    {
        $model = new HandbookAlternativesPickerItemTableColumnModel();
        $renderer = new HandbookAlternativesPickerItemTableCellRenderer($browser);
        $data_provider = new HandbookAlternativesPickerItemTableDataProvider($browser, $condition);
        parent :: __construct($data_provider, HandbookAlternativesPickerItemTable :: DEFAULT_NAME, $model, $renderer);
        $this->set_additional_parameters($parameters);
        $actions = array();
        $this->set_form_actions($actions);
        $this->set_default_row_count(20);
    }
}
?>