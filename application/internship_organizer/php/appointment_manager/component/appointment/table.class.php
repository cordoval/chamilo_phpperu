<?php

require_once dirname(__FILE__) . '/table_data_provider.class.php';
require_once dirname(__FILE__) . '/table_column_model.class.php';
require_once dirname(__FILE__) . '/table_cell_renderer.class.php';

class InternshipOrganizerAppointmentBrowserTable extends ObjectTable
{
    const DEFAULT_NAME = 'internship_organizer_appointment_rel_user_browser_table';

    /**
     * Constructor
     */
    function InternshipOrganizerAppointmentBrowserTable($browser, $parameters, $condition)
    {
        
        $model = new InternshipOrganizerAppointmentBrowserTableColumnModel();
        $renderer = new InternshipOrganizerAppointmentBrowserTableCellRenderer($browser);
        $data_provider = new InternshipOrganizerAppointmentBrowserTableDataProvider($browser, $condition);
        parent :: __construct($data_provider, InternshipOrganizerAppointmentBrowserTable :: DEFAULT_NAME, $model, $renderer);
        $this->set_additional_parameters($parameters);
        $actions = array();
        
        $actions[] = new ObjectTableFormAction(InternshipOrganizerAgreementManager :: PARAM_SUBSCRIBE_SELECTED, Translation :: get('Subscribe'));
        
        $this->set_form_actions($actions);
        $this->set_default_row_count(20);
    }
}
?>