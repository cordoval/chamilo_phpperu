<?php

require_once dirname(__FILE__) . '/rel_mentor_browser_table_data_provider.class.php';
require_once dirname(__FILE__) . '/rel_mentor_browser_table_column_model.class.php';
require_once dirname(__FILE__) . '/rel_mentor_browser_table_cell_renderer.class.php';

class oldInternshipOrganizerMentorRelLocationBrowserTable extends ObjectTable
{
    const DEFAULT_NAME = 'rel_mentor_browser_table';

    /**
     * Constructor
     */
    function InternshipOrganizerMentorRelLocationBrowserTable($browser, $parameters, $condition)
    {
        
        $model = new InternshipOrganizerMentorRelLocationBrowserTableColumnModel($browser);
        $renderer = new InternshipOrganizerMentorRelLocationBrowserTableCellRenderer($browser);
        $data_provider = new InternshipOrganizerMentorRelLocationBrowserTableDataProvider($browser, $condition);
        parent :: __construct($data_provider, InternshipOrganizerMentorRelLocationBrowserTable :: DEFAULT_NAME, $model, $renderer);
        $actions = array();
        $this->set_additional_parameters($parameters);
        $this->set_form_actions($actions);
    
    }
}
?>