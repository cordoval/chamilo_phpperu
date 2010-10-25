<?php

require_once WebApplication :: get_application_class_lib_path('internship_organizer') . 'agreement_manager/component/moment_browser/browser_table_column_model.class.php';
require_once WebApplication :: get_application_class_lib_path('internship_organizer') . 'tables/moment_table/default_moment_table_cell_renderer.class.php';
require_once WebApplication :: get_application_class_lib_path('internship_organizer') . 'moment.class.php';
require_once WebApplication :: get_application_class_lib_path('internship_organizer') . 'agreement_manager/agreement_manager.class.php';

class InternshipOrganizerMomentBrowserTableCellRenderer extends DefaultInternshipOrganizerMomentTableCellRenderer
{
    
    private $browser;

    function InternshipOrganizerMomentBrowserTableCellRenderer($browser)
    {
        parent :: __construct();
        $this->browser = $browser;
    }

    // Inherited
    function render_cell($column, $moment)
    {
        if ($column === InternshipOrganizerMomentBrowserTableColumnModel :: get_modification_column())
        {
            return $this->get_modification_links($moment);
        }
        
        return parent :: render_cell($column, $moment);
    }

    /**
     * Gets the action links to display
     * @param SurveyPublication $survey_publication The learning object for which the
     * action links should be returned
     * @return string A HTML representation of the action links
     */
    private function get_modification_links($moment)
    {
        
        $toolbar = new Toolbar();
        if (InternshipOrganizerRights :: is_allowed_in_internship_organizers_subtree(InternshipOrganizerRights :: RIGHT_EDIT, $moment->get_id(), InternshipOrganizerRights :: TYPE_MOMENT))
        {
            $toolbar->add_item(new ToolbarItem(Translation :: get('Edit'), Theme :: get_common_image_path() . 'action_edit.png', $this->browser->get_update_moment_url($moment), ToolbarItem :: DISPLAY_ICON));
        }
        if (InternshipOrganizerRights :: is_allowed_in_internship_organizers_subtree(InternshipOrganizerRights :: RIGHT_DELETE, $moment->get_id(), InternshipOrganizerRights :: TYPE_MOMENT))
        {
            $toolbar->add_item(new ToolbarItem(Translation :: get('Delete'), Theme :: get_common_image_path() . 'action_delete.png', $this->browser->get_delete_moment_url($moment), ToolbarItem :: DISPLAY_ICON, true));
        }
        if (InternshipOrganizerRights :: is_allowed_in_internship_organizers_subtree(InternshipOrganizerRights :: RIGHT_VIEW, $moment->get_id(), InternshipOrganizerRights :: TYPE_MOMENT))
        {
            $toolbar->add_item(new ToolbarItem(Translation :: get('View'), Theme :: get_common_image_path() . 'action_browser.png', $this->browser->get_view_moment_url($moment), ToolbarItem :: DISPLAY_ICON));
        }
       
        if ($this->browser->get_user()->is_platform_admin() || $moment->get_owner() == $this->browser->get_user_id())
        {
            $toolbar->add_item(new ToolbarItem(Translation :: get('ManageRights'), Theme :: get_common_image_path() . 'action_rights.png', $this->browser->get_moment_rights_editor_url($moment), ToolbarItem :: DISPLAY_ICON));
        }
        
        return $toolbar->as_html();
    }
}
?>