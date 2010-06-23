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
                if ($publication->is_visible_for_target_user($user, true))
                {
                    $url = '<a href="' . htmlentities($this->browser->get_url()) . '" title="' . $title . '">' . $title . '</a>';
                
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
        //        $content_object = $publication->get_content_object();
        $user = $this->browser->get_user();
        
        $toolbar = new Toolbar(Toolbar :: TYPE_HORIZONTAL);
        
        //        if ($publication->is_visible_for_target_user($user, true))
        //        {
        //            $toolbar->add_item(new ToolbarItem(
        //	        		Translation :: get('TakeInternshipOrganizer'),
        //	        		Theme :: get_common_image_path() . 'action_next.png',
        //	        		$this->browser->get_publication_viewer_url($publication),
        //	        		ToolbarItem :: DISPLAY_ICON
        //	        ));
        //        }
        //        else
        //        {
        //            $toolbar->add_item(new ToolbarItem(
        //	        		Translation :: get('InternshipOrganizerPublished'),
        //	        		Theme :: get_common_image_path() . 'action_next_na.png',
        //	        		null,
        //	        		ToolbarItem :: DISPLAY_ICON
        //	        ));
        //        
        //        }
        //        
        //        if ($user->is_platform_admin() || $user->get_id() == $publication->get_publisher())
        //        {
        //            $toolbar->add_item(new ToolbarItem(
        //	        		Translation :: get('Edit'),
        //	        		Theme :: get_common_image_path() . 'action_edit.png',
        //	        		$this->browser->get_update_publication_url($publication),
        //	        		ToolbarItem :: DISPLAY_ICON
        //	        ));
        //	        
        //	        $toolbar->add_item(new ToolbarItem(
        //	        		Translation :: get('Delete'),
        //	        		Theme :: get_common_image_path() . 'action_delete.png',
        //	        		$this->browser->get_delete_publication_url($publication),
        //	        		ToolbarItem :: DISPLAY_ICON,
        //	        		true
        //	        ));
        //            
        //            if ($publication->get_hidden())
        //            {
        //                $toolbar->add_item(new ToolbarItem(
        //		        		Translation :: get('Show'),
        //		        		Theme :: get_common_image_path() . 'action_visible_na.png',
        //		        		$this->browser->get_change_publication_visibility_url($publication),
        //		        		ToolbarItem :: DISPLAY_ICON
        //		        ));
        //            }
        //            else
        //            {
        //                $toolbar->add_item(new ToolbarItem(
        //		        		Translation :: get('Hide'),
        //		        		Theme :: get_common_image_path() . 'action_visible.png',
        //		        		$this->browser->get_change_publication_visibility_url($publication),
        //		        		ToolbarItem :: DISPLAY_ICON
        //		        ));
        //            }
        //            
        //            $toolbar->add_item(new ToolbarItem(
        //	        		Translation :: get('ViewReport'),
        //	        		Theme :: get_common_image_path() . 'action_view_results.png',
        //	        		$this->browser->get_reporting_publication_url($publication),
        //	        		ToolbarItem :: DISPLAY_ICON
        //	        ));
        //            
        //            //TO DO implement survey exporter !!
        //            //$toolbar_data[] = array('href' => $this->browser->get_export_survey_url($publication), 'label' => Translation :: get('Export'), 'img' => Theme :: get_common_image_path() . 'action_export.png');
        //            $toolbar->add_item(new ToolbarItem(
        //	        		Translation :: get('Move'),
        //	        		Theme :: get_common_image_path() . 'action_move.png',
        //	        		$this->browser->get_move_publication_url($publication),
        //	        		ToolbarItem :: DISPLAY_ICON
        //	        ));
        //	        
        //            $toolbar->add_item(new ToolbarItem(
        //	        		Translation :: get('InviteParticipants'),
        //	        		Theme :: get_common_image_path() . 'action_invite_users.png',
        //	        		$this->browser->get_mail_survey_participant_url($publication),
        //	        		ToolbarItem :: DISPLAY_ICON
        //	        ));
        //            
        //            $toolbar->add_item(new ToolbarItem(
        //	        		Translation :: get('BrowseInternshipOrganizerPages'),
        //	        		Theme :: get_common_image_path() . 'action_view_results.png',
        //	        		$this->browser->get_browse_survey_pages_url($publication),
        //	        		ToolbarItem :: DISPLAY_ICON
        //	        ));
        //            
        //            if ($survey->is_complex_content_object())
        //            {
        //                $toolbar->add_item(new ToolbarItem(
        //		        		Translation :: get('BrowseInternshipOrganizer'),
        //		        		Theme :: get_common_image_path() . 'action_browser.png',
        //		        		$this->browser->get_build_survey_url($publication),
        //		        		ToolbarItem :: DISPLAY_ICON
        //		        ));
        //            }
        //        }
        //        
        //        $toolbar->add_item(new ToolbarItem(
        //	        		Translation :: get('ExportToExcel'),
        //	        		Theme :: get_common_image_path() . 'export_excel.png',
        //	        		$this->browser->get_publication_export_excel_url($publication),
        //	        		ToolbarItem :: DISPLAY_ICON
        //	        ));
        

        return $toolbar->as_html();
    }
}

?>