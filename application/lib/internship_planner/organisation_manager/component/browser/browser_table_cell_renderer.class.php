<?php

require_once dirname(__FILE__) . '/browser_table_column_model.class.php';
require_once dirname(__FILE__) . '/../../../tables/organisation_table/default_organisation_table_cell_renderer.class.php';
require_once dirname(__FILE__) . '/../../../organisation.class.php';
require_once dirname(__FILE__) . '/../../organisation_manager.class.php';

class InternshipPlannerOrganisationBrowserTableCellRenderer extends DefaultInternshipPlannerOrganisationTableCellRenderer
{
    
    private $browser;

    function InternshipPlannerOrganisationBrowserTableCellRenderer($browser)
    {
        parent :: __construct();
        $this->browser = $browser;
    }

    // Inherited
    function render_cell($column, $organisation)
    {
        if ($column === InternshipPlannerOrganisationBrowserTableColumnModel :: get_modification_column())
        {
            return $this->get_modification_links($organisation);
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
        
        $toolbar_data = array();
        
        $user = $this->browser->get_user();
        
      
            $toolbar_data[] = array('href' => $this->browser->get_update_organisation_url($organisation), 'label' => Translation :: get('Edit'), 'img' => Theme :: get_common_image_path() . 'action_edit.png');
        
       
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
            
            $toolbar_data[] = array('href' => $this->browser->get_delete_organisation_url($organisation), 'label' => Translation :: get('Delete'), 'img' => Theme :: get_common_image_path() . 'action_delete.png');
        
            $toolbar_data[] = array('href' => $this->browser->get_view_organisation_url($organisation), 'label' => Translation :: get('View'), 'img' => Theme :: get_common_image_path() . 'action_browser.png');
            
      
        
        
        return Utilities :: build_toolbar($toolbar_data);
    }
}
?>