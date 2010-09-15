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
        
        $user = $this->browser->get_user();
        $user_id = $user->get_id();
        
        $toolbar = new Toolbar();
        
        $agreement = InternshipOrganizerDataManager :: get_instance()->retrieve_agreement($agreementrellocation->get_agreement_id());
        
        $condition = new EqualityCondition(InternshipOrganizerAgreementRelLocation :: PROPERTY_AGREEMENT_ID, $agreementrellocation->get_agreement_id());
        $condition = new EqualityCondition(InternshipOrganizerAgreementRelLocation :: PROPERTY_LOCATION_TYPE, InternshipOrganizerAgreementRelLocation :: TO_APPROVE);
        
        $count = InternshipOrganizerDataManager :: get_instance()->count_agreement_rel_locations($condition);
        
        if ($count > 1)
        {
            if (InternshipOrganizerRights :: is_allowed_in_internship_organizers_subtree(InternshipOrganizerRights :: ADD_LOCATION_RIGHT, $agreement->get_id(), InternshipOrganizerRights :: TYPE_AGREEMENT))
            {
                if ($agreementrellocation->get_location_type() != InternshipOrganizerAgreementRelLocation :: APPROVED)
                {
                    $toolbar->add_item(new ToolbarItem(Translation :: get('Unsubscribe'), Theme :: get_common_image_path() . 'action_delete.png', $this->browser->get_agreement_rel_location_unsubscribing_url($agreementrellocation), ToolbarItem :: DISPLAY_ICON, true));
                }
                if ($agreementrellocation->get_preference_order() > 1)
                {
                    $toolbar->add_item(new ToolbarItem(Translation :: get('MoveUp'), Theme :: get_common_image_path() . 'action_up.png', $this->browser->get_agreement_rel_location_move_up_url($agreementrellocation), ToolbarItem :: DISPLAY_ICON));
                }
                else
                {
                    $toolbar->add_item(new ToolbarItem(Translation :: get('MoveUpNA'), Theme :: get_common_image_path() . 'action_up_na.png', null, ToolbarItem :: DISPLAY_ICON));
                }
                
                if ($agreementrellocation->get_preference_order() < $count)
                {
                    $toolbar->add_item(new ToolbarItem(Translation :: get('MoveDown'), Theme :: get_common_image_path() . 'action_down.png', $this->browser->get_agreement_rel_location_move_down_url($agreementrellocation), ToolbarItem :: DISPLAY_ICON));
                }
                else
                {
                    $toolbar->add_item(new ToolbarItem(Translation :: get('MoveDownNA'), Theme :: get_common_image_path() . 'action_down_na.png', null, ToolbarItem :: DISPLAY_ICON));
                }
            }
        }
        if (InternshipOrganizerRights :: is_allowed_in_internship_organizers_subtree(InternshipOrganizerRights :: APPROVE_LOCATION_RIGHT, $agreement->get_id(), InternshipOrganizerRights :: TYPE_AGREEMENT))
        {
            
            if ($agreementrellocation->get_location_type() == InternshipOrganizerAgreementRelLocation :: APPROVED)
            {
                $toolbar->add_item(new ToolbarItem(Translation :: get('ResetApprovedToApprove'), Theme :: get_common_image_path() . 'action_unlink.png', $this->browser->get_agreement_rel_location_approve_url($agreementrellocation), ToolbarItem :: DISPLAY_ICON));
            
            }
            else
            {
                $toolbar->add_item(new ToolbarItem(Translation :: get('Approve'), Theme :: get_common_image_path() . 'action_confirm.png', $this->browser->get_agreement_rel_location_approve_url($agreementrellocation), ToolbarItem :: DISPLAY_ICON));
            
            }
        
        }
        
        return $toolbar->as_html();
    }

    function render_id_cell($agreementrellocation)
    {
        return $agreementrellocation->get_location_id();
    }

}
?>