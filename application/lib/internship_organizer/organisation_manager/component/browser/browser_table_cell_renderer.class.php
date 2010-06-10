<?php

require_once dirname(__FILE__) . '/browser_table_column_model.class.php';
require_once dirname(__FILE__) . '/../../../tables/organisation_table/default_organisation_table_cell_renderer.class.php';
require_once dirname(__FILE__) . '/../../../organisation.class.php';
require_once dirname(__FILE__) . '/../../organisation_manager.class.php';

class InternshipOrganizerOrganisationBrowserTableCellRenderer extends DefaultInternshipOrganizerOrganisationTableCellRenderer
{
    
    private $browser;

    function InternshipOrganizerOrganisationBrowserTableCellRenderer($browser)
    {
        parent :: __construct();
        $this->browser = $browser;
    }

    // Inherited
    function render_cell($column, $organisation)
    {
        if ($column === InternshipOrganizerOrganisationBrowserTableColumnModel :: get_modification_column())
        {
            return $this->get_modification_links($organisation);
        }
        
     switch ($column->get_name())
        {
            // Exceptions that need post-processing go here ...
            case InternshipOrganizerOrganisation :: PROPERTY_NAME :
                $title = parent :: render_cell($column, $organisation);
                $title_short = $title;
                if (strlen($title_short) > 75)
                {
                    $title_short = mb_substr($title_short, 0, 75) . '&hellip;';
                }
                return '<a href="' . htmlentities($this->browser->get_view_organisation_url($organisation)) . '" title="' . $title . '">' . $title_short . '</a>';

          	case InternshipOrganizerOrganisation :: PROPERTY_DESCRIPTION :
                $title = parent :: render_cell($column, $organisation);
                $title_short = $title;
                if (strlen($title_short) > 75)
                {
                    $title_short = mb_substr($title_short, 0, 75) . '&hellip;';
                }
                return $title_short;
                
           	case Translation :: get('Locations') :
                return $organisation->count_locations();
                   
                
        }
        
        return parent :: render_cell($column, $organisation);
    }

    /**
     * Gets the action links to display
     * @param SurveyPublication $survey_publication The learning object for which the
     * action links should be returned
     * @return string A HTML representation of the action links
     */
    private function get_modification_links($organisation)
    {
        
        $toolbar = new Toolbar();
        
        $user = $this->browser->get_user();
        
      	$toolbar->add_item(new ToolbarItem(Translation :: get('Edit'), Theme :: get_common_image_path() . 'action_edit.png', $this->browser->get_update_organisation_url($organisation), ToolbarItem :: DISPLAY_ICON ));
 
        
       
//            $toolbar_data[] = array('href' => $this->browser->get_group_suscribe_user_browser_url($group), 'label' => Translation :: get('AddStudents'), 'img' => Theme :: get_common_image_path() . 'action_subscribe.png');
//        
//       
//            $condition = new EqualityCondition(StsGroupRelUser :: PROPERTY_GROUP_ID, $group->get_id());
//            $users = $this->browser->retrieve_group_rel_users($condition);
//            $visible = ($users->size() > 0);
//            
//            if ($visible)
//            {
//                $toolbar_data[] = array('href' => $this->browser->get_group_emptying_url($group), 'label' => Translation :: get('Truncate'), 'img' => Theme :: get_common_image_path() . 'action_recycle_bin.png');
//            }
//            else
//            {
//                $toolbar_data[] = array('label' => Translation :: get('TruncateNA'), 'img' => Theme :: get_common_image_path() . 'action_recycle_bin_na.png');
//            }
      	$toolbar->add_item(new ToolbarItem(Translation :: get('Delete'), Theme :: get_common_image_path() . 'action_delete.png', $this->browser->get_delete_organisation_url($organisation), ToolbarItem :: DISPLAY_ICON, true ));            
        $toolbar->add_item(new ToolbarItem(Translation :: get('View'), Theme :: get_common_image_path() . 'action_browser.png', $this->browser->get_view_organisation_url($organisation), ToolbarItem :: DISPLAY_ICON, true ));            
            
        return $toolbar->as_html();
    }
}
?>