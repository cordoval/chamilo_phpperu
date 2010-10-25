<?php

require_once WebApplication :: get_application_class_lib_path('internship_organizer') . 'agreement_manager/component/user_browser/user_browser_table_column_model.class.php';
require_once WebApplication :: get_application_class_lib_path('user') . 'user_table/default_user_table_cell_renderer.class.php';

class InternshipOrganizerAgreementUserBrowserTableCellRenderer extends DefaultUserTableCellRenderer
{
    
    private $browser;

    function InternshipOrganizerAgreementUserBrowserTableCellRenderer($browser)
    {
        parent :: __construct();
        $this->browser = $browser;
    }

    // Inherited
    function render_cell($column, $user)
    {
        if ($column === InternshipOrganizerAgreementUserBrowserTableColumnModel :: get_modification_column())
        {
            //return $this->get_modification_links( $user);
        }
        
        return parent :: render_cell($column, $user);
    }

    private function get_modification_links($user)
    {
        $toolbar_data = array();
        
        return Utilities :: build_toolbar($toolbar_data);
    }
}
?>