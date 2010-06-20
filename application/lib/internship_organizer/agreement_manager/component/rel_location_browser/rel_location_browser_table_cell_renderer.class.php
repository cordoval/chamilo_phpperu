<?php

require_once dirname(__FILE__) . '/rel_location_browser_table_column_model.class.php';
require_once dirname(__FILE__) . '/../../../tables/agreement_rel_location_table/default_agreement_rel_location_table_cell_renderer.class.php';

class InternshipOrganizerAgreementRelLocationBrowserTableCellRenderer extends DefaultInternshipOrganizerAgreementRelLocationTableCellRenderer
{
    /**
     * The repository browser component
     */
    private $browser;

    /**
     * Constructor
     * @param RepositoryManagerBrowserComponent $browser
     */
    function InternshipOrganizerAgreementRelLocationBrowserTableCellRenderer($browser)
    {
        parent :: __construct();
        $this->browser = $browser;
    }

    // Inherited
    function render_cell($column, $agreementrellocation)
    {
        if ($column === InternshipOrganizerAgreementRelLocationBrowserTableColumnModel :: get_modification_column())
        {
            return $this->get_modification_links($agreementrellocation);
        }
        
        // Add special features here
        //        switch ($column->get_name())
        //        {
        //            // Exceptions that need post-processing go here ...
        //            case InternshipOrganizerAgreementRelLocation :: PROPERTY_LOCATION_ID :
        //               
        //                return $location->get_name();
        //        }
        return parent :: render_cell($column, $agreementrellocation);
    }

    /**
     * Gets the action links to display
     * @param ContentObject $content_object The learning object for which the
     * action links should be returned
     * @return string A HTML representation of the action links
     */
    private function get_modification_links($agreementrellocation)
    {
        $toolbar = new Toolbar();
        $toolbar->add_item(new ToolbarItem(Translation :: get('Unsubscribe'), Theme :: get_common_image_path() . 'action_delete.png', $this->browser->get_agreement_rel_location_unsubscribing_url($agreementrellocation), ToolbarItem :: DISPLAY_ICON, true));
        
        return $toolbar->as_html();
    }

    function render_id_cell($agreementrellocation)
    {
        return $agreementrellocation->get_location_id();
    }

}
?>