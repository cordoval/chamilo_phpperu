<?php

require_once dirname(__FILE__) . '/browser_table_data_provider.class.php';
require_once dirname(__FILE__) . '/browser_table_column_model.class.php';
require_once dirname(__FILE__) . '/browser_table_cell_renderer.class.php';

class InternshipOrganizerMentorBrowserTable extends ObjectTable
{
    const DEFAULT_NAME = 'mentor_browser_table';

    /**
     * Constructor
     */
    function InternshipOrganizerMentorBrowserTable($browser, $parameters, $condition)
    {
        $model = new InternshipOrganizerMentorBrowserTableColumnModel();
        $renderer = new InternshipOrganizerMentorBrowserTableCellRenderer($browser);
        $data_provider = new InternshipOrganizerMentorBrowserTableDataProvider($browser, $condition);
        parent :: __construct($data_provider, self :: DEFAULT_NAME, $model, $renderer);
        $this->set_additional_parameters($parameters);
        $actions = array();
        
        $actions[] = new ObjectTableFormAction(InternshipOrganizerOrganisationManager :: PARAM_DELETE_SELECTED_MENTORS, Translation :: get('RemoveSelected'));
        
        $this->set_form_actions($actions);
        $this->set_default_row_count(20);
    }
}
?>