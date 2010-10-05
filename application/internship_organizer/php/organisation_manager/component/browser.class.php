<?php

require_once dirname(__FILE__) . '/../organisation_manager.class.php';
require_once dirname(__FILE__) . '/browser/browser_table.class.php';
require_once Path :: get_application_path() . 'internship_organizer/php/agreement_rel_user.class.php';


class InternshipOrganizerOrganisationManagerBrowserComponent extends InternshipOrganizerOrganisationManager
{
    
    const TAB_ORGANISATIONS = 1;
    const TAB_MY_ORGANISATIONS = 2;
    
    private $action_bar;

    function run()
    {
        
        if (! InternshipOrganizerRights :: is_allowed_in_internship_organizers_subtree(InternshipOrganizerRights :: RIGHT_VIEW, InternshipOrganizerRights :: LOCATION_ORGANISATION, InternshipOrganizerRights :: TYPE_COMPONENT))
        {
            $this->display_header();
            $this->display_error_message(Translation :: get('NotAllowed'));
            $this->display_footer();
            exit();
        }
        
        $this->action_bar = $this->get_action_bar();
        
        $this->display_header();
        echo $this->action_bar->as_html();
        echo '<div id="action_bar_browser">';
        echo '<div>';
        echo $this->get_tabs();
        echo '</div>';
        echo '</div>';
        $this->display_footer();
    }

    function get_tabs()
    {
        
        $html = array();
        $html[] = '<div>';
        
        $renderer_name = Utilities :: camelcase_to_underscores(get_class($this));
        $tabs = new DynamicTabsRenderer($renderer_name);
        
        $parameters = $this->get_parameters();
        
        $parameters[DynamicTabsRenderer :: PARAM_SELECTED_TAB] = self :: TAB_MY_ORGANISATIONS;
        $table = new InternshipOrganizerOrganisationBrowserTable($this, $parameters, $this->get_my_organisation_condition());
        $tabs->add_tab(new DynamicContentTab(self :: TAB_MY_ORGANISATIONS, Translation :: get('MyOrganisations'), Theme :: get_image_path('internship_organizer') . 'place_mini_period.png', $table->as_html()));
        
        $parameters[DynamicTabsRenderer :: PARAM_SELECTED_TAB] = self :: TAB_ORGANISATIONS;
        $table = new InternshipOrganizerOrganisationBrowserTable($this, $parameters, $this->get_condition());
        $tabs->add_tab(new DynamicContentTab(self :: TAB_ORGANISATIONS, Translation :: get('Organisations'), Theme :: get_image_path('internship_organizer') . 'place_mini_period.png', $table->as_html()));
        
        $html[] = $tabs->render();
        
        $html[] = '</div>';
        $html[] = '<div class="clear"></div>';
        
        return implode($html, "\n");
    }

    function get_action_bar()
    {
        $action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);
        $action_bar->set_search_url($this->get_url());
        
        $action_bar->add_common_action(new ToolbarItem(Translation :: get('ShowAll'), Theme :: get_common_image_path() . 'action_browser.png', $this->get_browse_organisations_url(), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        
        if (InternshipOrganizerRights :: is_allowed_in_internship_organizers_subtree(InternshipOrganizerRights :: RIGHT_ADD, InternshipOrganizerRights :: LOCATION_ORGANISATION, InternshipOrganizerRights :: TYPE_COMPONENT))
        {
            $action_bar->add_common_action(new ToolbarItem(Translation :: get('CreateInternshipOrganizerOrganisation'), Theme :: get_common_image_path() . 'action_create.png', $this->get_create_organisation_url(), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        }
        
        if (InternshipOrganizerRights :: is_allowed_in_internship_organizers_subtree(InternshipOrganizerRights :: RIGHT_IMPORT, InternshipOrganizerRights :: LOCATION_ORGANISATION, InternshipOrganizerRights :: TYPE_COMPONENT))
        {
            $action_bar->add_tool_action(new ToolbarItem(Translation :: get('ImportInternshipOrganizerOrganisation'), Theme :: get_common_image_path() . 'action_import.png', $this->get_organisation_importer_url(), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        }
        
        return $action_bar;
    }

    function get_condition()
    {
        
        $query = $this->action_bar->get_query();
        $condition = null;
        
        if (isset($query) && $query != '')
        {
            $search_conditions = array();
            $search_conditions[] = new PatternMatchCondition(InternshipOrganizerOrganisation :: PROPERTY_NAME, '*' . $query . '*');
            $search_conditions[] = new PatternMatchCondition(InternshipOrganizerOrganisation :: PROPERTY_DESCRIPTION, '*' . $query . '*');
            $condition = new OrCondition($search_conditions);
        }
        return $condition;
    }

    function get_my_organisation_condition()
    {
        
        $conditions = array();
        
        $condition = new EqualityCondition(InternshipOrganizerAgreementRelUser :: PROPERTY_USER_ID, $this->get_user_id());
        $agreement_rel_users = InternshipOrganizerDataManager :: get_instance()->retrieve_agreement_rel_users($condition);
        $agreement_ids = array();
        while ($agreement_rel_user = $agreement_rel_users->next_result())
        {
            $agreement_ids[] = $agreement_rel_user->get_agreement_id();
        }
        $organisation_ids = array();
        if (count($agreement_ids))
        {
            
            $agreement_rel_location_conditions = array();
            $agreement_rel_location_conditions[] = new InCondition(InternshipOrganizerAgreementRelLocation :: PROPERTY_AGREEMENT_ID, $agreement_ids);
            $agreement_rel_location_conditions[] = new InCondition(InternshipOrganizerAgreementRelLocation :: PROPERTY_LOCATION_TYPE, InternshipOrganizerAgreementRelLocation :: APPROVED);
            $condition = new AndCondition($agreement_rel_location_conditions);
            $agreement_rel_locations = InternshipOrganizerDataManager :: get_instance()->retrieve_agreement_rel_locations($condition);
            while ($agreement_rel_location = $agreement_rel_locations->next_result())
            {
                $organisation_ids[] = $agreement_rel_location->get_optional_property(InternshipOrganizerLocation :: PROPERTY_ORGANISATION_ID);
            }
        
        }
        
        $condition = new EqualityCondition(InternshipOrganizerOrganisationRelUser :: PROPERTY_USER_ID, $this->get_user_id());
        $organisation_rel_users = InternshipOrganizerDataManager :: get_instance()->retrieve_organisation_rel_users($condition);
        while ($organisation_rel_user = $organisation_rel_users->next_result())
        {
            $organisation_ids[] = $organisation_rel_user->get_organisation_id();
        }
        
        if (count($organisation_ids))
        {
            $conditions[] = new InCondition(InternshipOrganizerOrganisation :: PROPERTY_ID, $organisation_ids);
        }
        else
        {
            $conditions[] = new EqualityCondition(InternshipOrganizerOrganisation :: PROPERTY_ID, 0);
        
        }
        
        $query = $this->action_bar->get_query();
        
        if (isset($query) && $query != '')
        {
            $search_conditions = array();
            $search_conditions[] = new PatternMatchCondition(InternshipOrganizerOrganisation :: PROPERTY_NAME, '*' . $query . '*');
            $search_conditions[] = new PatternMatchCondition(InternshipOrganizerOrganisation :: PROPERTY_DESCRIPTION, '*' . $query . '*');
            $conditions[] = new OrCondition($search_conditions);
        }
        return new AndCondition($conditions);
    }

}
?>