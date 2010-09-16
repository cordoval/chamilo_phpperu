<?php

require_once dirname(__FILE__) . '/table_column_model.class.php';
require_once dirname(__FILE__) . '/../../../tables/period_rel_agreement_table/default_period_rel_agreement_table_cell_renderer.class.php';
require_once dirname(__FILE__) . '/../../../agreement.class.php';
require_once dirname(__FILE__) . '/../../period_manager.class.php';

class InternshipOrganizerPeriodRelAgreementBrowserTableCellRenderer extends DefaultInternshipOrganizerPeriodRelAgreementTableCellRenderer
{
    
    private $browser;

    function InternshipOrganizerPeriodRelAgreementBrowserTableCellRenderer($browser)
    {
        parent :: __construct();
        $this->browser = $browser;
    }

    // Inherited
    function render_cell($column, $agreement)
    {
        if ($column === InternshipOrganizerPeriodRelAgreementBrowserTableColumnModel :: get_modification_column())
        {
            return $this->get_modification_links($agreement);
        }
        
        return parent :: render_cell($column, $agreement);
    }

    /**
     * Gets the action links to display
     * @param SurveyPublication $survey_publication The learning object for which the
     * action links should be returned
     * @return string A HTML representation of the action links
     */
    private function get_modification_links($agreement)
    {
        $toolbar = new Toolbar();
        
        $user = $this->browser->get_user();
        $user_id = $user->get_id();
        
        if (InternshipOrganizerRights :: is_allowed_in_internship_organizers_subtree(InternshipOrganizerRights :: RIGHT_EDIT, $agreement->get_id(), InternshipOrganizerRights :: TYPE_AGREEMENT))
        {
            $toolbar->add_item(new ToolbarItem(Translation :: get('Edit'), Theme :: get_common_image_path() . 'action_edit.png', $this->browser->get_update_agreement_url($agreement), ToolbarItem :: DISPLAY_ICON));
        }
        if (InternshipOrganizerRights :: is_allowed_in_internship_organizers_subtree(InternshipOrganizerRights :: RIGHT_DELETE, $agreement->get_id(), InternshipOrganizerRights :: TYPE_AGREEMENT))
        {
            $toolbar->add_item(new ToolbarItem(Translation :: get('Delete'), Theme :: get_common_image_path() . 'action_delete.png', $this->browser->get_delete_agreement_url($agreement), ToolbarItem :: DISPLAY_ICON, true));
        }
        if (InternshipOrganizerRights :: is_allowed_in_internship_organizers_subtree(InternshipOrganizerRights :: RIGHT_VIEW, $agreement->get_id(), InternshipOrganizerRights :: TYPE_AGREEMENT))
        {
            $toolbar->add_item(new ToolbarItem(Translation :: get('View'), Theme :: get_common_image_path() . 'action_browser.png', $this->browser->get_view_agreement_url($agreement), ToolbarItem :: DISPLAY_ICON));
        }
        
        if ($this->browser->get_user()->is_platform_admin() || $agreement->get_owner() == $this->browser->get_user_id())
        {
            $toolbar->add_item(new ToolbarItem(Translation :: get('ManageRights'), Theme :: get_common_image_path() . 'action_rights.png', $this->browser->get_agreement_rights_editor_url($agreement), ToolbarItem :: DISPLAY_ICON));
        }
        
        return $toolbar->as_html();
    }
}
?>