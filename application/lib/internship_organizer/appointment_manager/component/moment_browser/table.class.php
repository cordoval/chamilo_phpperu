<?php

require_once dirname(__FILE__) . '/table_data_provider.class.php';
require_once dirname(__FILE__) . '/table_column_model.class.php';
require_once dirname(__FILE__) . '/table_cell_renderer.class.php';

class InternshipOrganizerMomentRelUserBrowserTable extends ObjectTable
{
    const DEFAULT_NAME = 'internship_organizer_moment_rel_user_browser_table';

    /**
     * Constructor
     */
    function InternshipOrganizerMomentRelUserBrowserTable($browser, $parameters, $condition)
    {
        
        $model = new InternshipOrganizerMomentRelUserBrowserTableColumnModel();
        $renderer = new InternshipOrganizerMomentRelUserBrowserTableCellRenderer($browser);
        $data_provider = new InternshipOrganizerMomentRelUserBrowserTableDataProvider($browser, $condition);
        parent :: __construct($data_provider, InternshipOrganizerMomentRelUserBrowserTable :: DEFAULT_NAME, $model, $renderer);
        $this->set_additional_parameters($parameters);
        $actions = array();
        
        $actions[] = new ObjectTableFormAction(InternshipOrganizerAgreementManager :: PARAM_SUBSCRIBE_SELECTED, Translation :: get('Subscribe'));
        
        $this->set_form_actions($actions);
        $this->set_default_row_count(20);
    }
}
?>