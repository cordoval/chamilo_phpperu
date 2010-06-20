<?php

require_once dirname(__FILE__) . '/rel_location_browser_table_data_provider.class.php';
require_once dirname(__FILE__) . '/rel_location_browser_table_column_model.class.php';
require_once dirname(__FILE__) . '/rel_location_browser_table_cell_renderer.class.php';

class InternshipOrganizerAgreementRelLocationBrowserTable extends ObjectTable
{
    const DEFAULT_NAME = 'agreement_rel_location_browser_table';

    /**
     * Constructor
     * @see ContentObjectTable::ContentObjectTable()
     */
    function InternshipOrganizerAgreementRelLocationBrowserTable($browser, $parameters, $condition)
    {
        $model = new InternshipOrganizerAgreementRelLocationBrowserTableColumnModel();
        $renderer = new InternshipOrganizerAgreementRelLocationBrowserTableCellRenderer($browser);
        $data_provider = new InternshipOrganizerAgreementRelLocationBrowserTableDataProvider($browser, $condition);
        parent :: __construct($data_provider, InternshipOrganizerAgreementRelLocationBrowserTable :: DEFAULT_NAME, $model, $renderer);
        $this->set_additional_parameters($parameters);
        $actions = array();
        
        $this->set_form_actions($actions);
        //        $this->set_default_row_count(20);
    }

/**
 * A typical ObjectTable would get the database-id of the object as a
 * unique identifier. InternshipOrganizerAgreementRelLocation has no such field since it's
 * a relation, so we need to overwrite this function here.
 */
//    function get_objects($offset, $count, $order_column)
//    {
//        $agreementrellocations = $this->get_data_provider()->get_objects($offset, $count, $this->get_column_model()->get_order_column($order_column - ($this->has_form_actions() ? 1 : 0)));
//        $table_data = array();
//        $column_count = $this->get_column_model()->get_column_count();
//        while ($agreementreluser = $agreementrelusers->next_result())
//        {
//            $row = array();
//            if ($this->has_form_actions())
//            {
//                $row[] = $agreementreluser->get_agreement_id() . '|' . $agreementreluser->get_location_id();
//            }
//            for($i = 0; $i < $column_count; $i ++)
//            {
//                $row[] = $this->get_cell_renderer()->render_cell($this->get_column_model()->get_column($i), $agreementreluser);
//            }
//            $table_data[] = $row;
//        }
//        return $table_data;
//    }
}
?>