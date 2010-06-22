<?php

require_once dirname(__FILE__) . '/rel_mentor_browser_table_column_model.class.php';
require_once dirname(__FILE__) . '/../../../tables/agreement_rel_mentor_table/default_agreement_rel_mentor_table_cell_renderer.class.php';

class InternshipOrganizerAgreementRelMentorBrowserTableCellRenderer extends DefaultInternshipOrganizerAgreementRelMentorTableCellRenderer
{
    
    private $browser;

    function InternshipOrganizerAgreementRelMentorBrowserTableCellRenderer($browser)
    {
        parent :: __construct();
        $this->browser = $browser;
    }

    // Inherited
    function render_cell($column, $rel_mentor)
    {
        if ($column === InternshipOrganizerAgreementRelMentorBrowserTableColumnModel :: get_modification_column())
        {
            //return $this->get_modification_links( $rel_mentor);
        }
        
        return parent :: render_cell($column, $rel_mentor);
    }

    private function get_modification_links($rel_mentor)
    {
        $toolbar_data = array();
        
        return Utilities :: build_toolbar($toolbar_data);
    }
}
?>