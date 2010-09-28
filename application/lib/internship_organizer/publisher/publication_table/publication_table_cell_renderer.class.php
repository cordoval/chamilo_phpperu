<?php

require_once dirname(__FILE__) . '/publication_table_column_model.class.php';
require_once Path :: get_application_path() . 'lib/internship_organizer/tables/publication_table/default_publication_table_cell_renderer.class.php';
require_once Path :: get_application_path() . 'lib/internship_organizer/publication.class.php';

class InternshipOrganizerPublicationTableCellRenderer extends DefaultInternshipOrganizerPublicationTableCellRenderer
{
    /**
     * The browser component
     * @var InternshipOrganizerManagerInternshipOrganizerPublicationsComponent
     */
    private $browser;

    /**
     * Constructor
     * @param ApplicationComponent $browser
     */
    function InternshipOrganizerPublicationTableCellRenderer($browser)
    {
        parent :: __construct();
        $this->browser = $browser;
    }

    // Inherited
    function render_cell($column, $publication)
    {
        
        if ($column === InternshipOrganizerPublicationTableColumnModel :: get_modification_column())
        {
            return $this->get_modification_links($publication);
        }
        
        switch ($column->get_name())
        {
            case ContentObject :: PROPERTY_TITLE :
                $content_object = $publication->get_content_object();
                $user = $this->browser->get_user();
                
                $title = $content_object->get_title();
                if (InternshipOrganizerRights :: is_allowed_in_internship_organizers_subtree(InternshipOrganizerRights :: RIGHT_VIEW, $publication->get_id(), InternshipOrganizerRights :: TYPE_PUBLICATION))
                {
                    $url = '<a href="' . htmlentities($this->browser->get_view_publication_url($publication)) . '" title="' . $title . '">' . $title . '</a>';
                
                }
                else
                {
                    $url = $title;
                }
                
                return $url;
        }
        
        return parent :: render_cell($column, $publication);
    
    }

    /**
     * Gets the action links to display
     * @param InternshipOrganizerPublication $publication The learning object for which the
     * action links should be returned
     * @return string A HTML representation of the action links
     */
    private function get_modification_links($publication)
    {
        $user = $this->browser->get_user();
        
        $toolbar = new Toolbar(Toolbar :: TYPE_HORIZONTAL);
        
        if (InternshipOrganizerRights :: is_allowed_in_internship_organizers_subtree(InternshipOrganizerRights :: RIGHT_VIEW, $publication->get_id(), InternshipOrganizerRights :: TYPE_PUBLICATION))
        {
            $toolbar->add_item(new ToolbarItem(Translation :: get('View'), Theme :: get_common_image_path() . 'action_browser.png', $this->browser->get_view_publication_url($publication), ToolbarItem :: DISPLAY_ICON));
        	//test
        	$toolbar->add_item(new ToolbarItem(Translation :: get('View'), Theme :: get_common_image_path() . 'action_browser.png', $this->browser->get_take_evaluation_url($publication), ToolbarItem :: DISPLAY_ICON));
            
            
        }
        
        //edit of publication not implemented jet
//        if (InternshipOrganizerRights :: is_allowed_in_internship_organizers_subtree(InternshipOrganizerRights :: RIGHT_EDIT, $publication->get_id(), InternshipOrganizerRights :: TYPE_PUBLICATION))
//        {
//            $toolbar->add_item(new ToolbarItem(Translation :: get('Edit'), Theme :: get_common_image_path() . 'action_edit.png', $this->browser->get_edit_publication_url($publication), ToolbarItem :: DISPLAY_ICON));
//        }
        
        if (InternshipOrganizerRights :: is_allowed_in_internship_organizers_subtree(InternshipOrganizerRights :: RIGHT_DELETE, $publication->get_id(), InternshipOrganizerRights :: TYPE_PUBLICATION))
        {
            $toolbar->add_item(new ToolbarItem(Translation :: get('Delete'), Theme :: get_common_image_path() . 'action_delete.png', $this->browser->get_delete_publication_url($publication), ToolbarItem :: DISPLAY_ICON, true));
        }
        
        if ($this->browser->get_user()->is_platform_admin() || $publication->get_publisher_id() == $this->browser->get_user_id())
        {
            $toolbar->add_item(new ToolbarItem(Translation :: get('ManageRights'), Theme :: get_common_image_path() . 'action_rights.png', $this->browser->get_publication_rights_editor_url($publication), ToolbarItem :: DISPLAY_ICON));
        }
        
        return $toolbar->as_html();
    }
}

?>