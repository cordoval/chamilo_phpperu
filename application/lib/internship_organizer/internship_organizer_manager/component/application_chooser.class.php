<?php

class InternshipOrganizerManagerApplicationChooserComponent extends InternshipOrganizerManager
{
    
    const ADMINISTRATIONTAB = 0;
    const AGREEMENTTAB = 1;
    const PERIODTAB = 2;

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $trail = BreadcrumbTrail :: get_instance();
        $trail->add(new Breadcrumb($this->get_url(array(InternshipOrganizerManager :: PARAM_ACTION => InternshipOrganizerManager :: ACTION_APPLICATION_CHOOSER)), Translation :: get('InternshipOrganizer')));
        $trail->add_help('internship_organizer');
        
        $links = $this->get_internship_organizer_links();
        
        $this->display_header();
        echo $this->get_internship_organizer_tabs($links);
        $this->display_footer();
    }

    /**
     * Returns an HTML representation of the actions.
     * @return string $html HTML representation of the actions.
     */
    function get_internship_organizer_tabs($links)
    {
        $renderer_name = Utilities :: camelcase_to_underscores(get_class($this));
        $internship_organizer_tabs = new DynamicTabsRenderer($renderer_name);
        
        $index = 0;
        foreach ($links as $sub_manager_links)
        {
            if (count($sub_manager_links['links']))
            {
                $index ++;
                $actions_tab = new DynamicActionsTab($sub_manager_links['application']['class'], $sub_manager_links['application']['name'], Theme :: get_image_path() . 'place_mini_' . $sub_manager_links['application']['class'] . '.png', implode("\n", $html));
                
                if (isset($application_links['search']))
                {
                    $search_form = new AdminSearchForm($this, $sub_manager_links['search'], $index);
                    $actions_tab->add_action(new DynamicAction(null, $search_form->display(), Theme :: get_image_path() . 'browse_search.png'));
                }
                
                foreach ($sub_manager_links['links'] as $action)
                {
                    $actions_tab->add_action($action);
                }
                
                $internship_organizer_tabs->add_tab($actions_tab);
            }
        }
        
        return $internship_organizer_tabs->render();
    }

    function get_internship_organizer_links()
    {
        
        $links = array();
        for($index = 0; $index < 3; $index ++)
        {
            $tab_links = $this->get_links_for_tab($index);
            $links[] = $tab_links;
        }
        return $links;
    }

    function get_links_for_tab($index)
    {
        
        $links = array();
        $tab_links = array();
        
        switch ($index)
        {
            case self :: ADMINISTRATIONTAB :
                
                $tab_links['application'] = array('name' => Translation :: get('AdministrationTab'), 'class' => 'administration');
                
                $agreement_link = new DynamicAction();
                $agreement_link->set_title(Translation :: get('AgreementLink'));
                $agreement_link->set_description(Translation :: get('AgreementLinkDescription'));
                $agreement_link->set_image(Theme :: get_image_path() . 'browse_agreement.png');
                $agreement_link->set_url($this->get_agreement_application_url());
                $links[] = $agreement_link;
                
                $category_link = new DynamicAction();
                $category_link->set_title(Translation :: get('CategoryLink'));
                $category_link->set_description(Translation :: get('CategoryLinkDescription'));
                $category_link->set_image(Theme :: get_image_path() . 'browse_category.png');
                $category_link->set_url($this->get_category_application_url());
                $links[] = $category_link;
                               
                $organisation_link = new DynamicAction();
                $organisation_link->set_title(Translation :: get('OrganisationLink'));
                $organisation_link->set_description(Translation :: get('OrganisationLinkDescription'));
                $organisation_link->set_image(Theme :: get_image_path() . 'browse_organisation.png');
                $organisation_link->set_url($this->get_organisation_application_url());
                $links[] = $organisation_link;
                
                $period_link = new DynamicAction();
                $period_link->set_title(Translation :: get('PeriodLink'));
                $period_link->set_description(Translation :: get('PeriodLinkDescription'));
                $period_link->set_image(Theme :: get_image_path() . 'browse_period.png');
                $period_link->set_url($this->get_period_application_url());
                $links[] = $period_link;
                
                $region_link = new DynamicAction();
                $region_link->set_title(Translation :: get('RegionLink'));
                $region_link->set_description(Translation :: get('RegionLinkDescription'));
                $region_link->set_image(Theme :: get_image_path() . 'browse_region.png');
                $region_link->set_url($this->get_region_application_url());
                $links[] = $region_link;
                
                $tab_links['links'] = $links;
                break;
            case self :: AGREEMENTTAB :
                
                $tab_links['application'] = array('name' => Translation :: get('AgreementTab'), 'class' => 'agreement');
                
                $agreement_link = new DynamicAction();
                $agreement_link->set_title(Translation :: get('AgreementLink'));
                $agreement_link->set_description(Translation :: get('AgreementLinkDescription'));
                $agreement_link->set_image(Theme :: get_image_path() . 'browse_agreement.png');
                $agreement_link->set_url($this->get_agreement_application_url());
                $links[] = $agreement_link;
                
                $tab_links['links'] = $links;
                break;
            case self :: PERIODTAB :
                
                $tab_links['application'] = array('name' => Translation :: get('PeriodTab'), 'class' => 'period');

                $period_link = new DynamicAction();
                $period_link->set_title(Translation :: get('PeriodLink'));
                $period_link->set_description(Translation :: get('PeriodLinkDescription'));
                $period_link->set_image(Theme :: get_image_path() . 'browse_period.png');
                $period_link->set_url($this->get_period_application_url());
                $links[] = $period_link;
                
                $tab_links['links'] = $links;
                break;
            default :
                
                break;
        }
        
        return $tab_links;
    }
}
?>