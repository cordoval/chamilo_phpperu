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
        ;
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
                $html = array();
                $html[] = '<div class="items">';
                
                if (isset($sub_manager_links['search']))
                {
                    $search_form = new AdminSearchForm($this, $sub_manager_links['search'], $index);
                    
                    $html[] = '<div class="vertical_action" style="border-top: none;">';
                    $html[] = '<div class="icon">';
                    $html[] = '<img src="' . Theme :: get_image_path('internship_organizer') . 'browse_search.png" alt="' . Translation :: get('Search') . '" title="' . Translation :: get('Search') . '"/>';
                    $html[] = '</div>';
                    $html[] = $search_form->display();
                    $html[] = '</div>';
                }
                
                $count = 1;
                
                foreach ($sub_manager_links['links'] as $link)
                {
                    $count ++;
                    
                    if ($link['confirm'])
                    {
                        $onclick = 'onclick = "return confirm(\'' . $link['confirm'] . '\')"';
                    }
                    
                    if (! isset($sub_manager_links['search']) && $application_settings_count == 0 && $count == 2)
                    {
                        $html[] = '<div class="vertical_action" style="border-top: none;">';
                    }
                    else
                    {
                        $html[] = '<div class="vertical_action">';
                    }
                    
                    $html[] = '<div class="icon">';
                    $html[] = '<a href="' . $link['url'] . '" ' . $onclick . '><img src="' . Theme :: get_image_path('internship_organizer') . 'browse_' . $link['action'] . '.png" alt="' . $link['name'] . '" title="' . $link['name'] . '"/></a>';
                    $html[] = '</div>';
                    $html[] = '<div class="description">';
                    $html[] = '<h4><a href="' . $link['url'] . '" ' . $onclick . '>' . $link['name'] . '</a></h4>';
                    $html[] = $link['description'];
                    $html[] = '</div>';
                    $html[] = '</div>';
                }
                
                $html[] = '</div>';
                $internship_organizer_tabs->add_tab(new DynamicActionsTab($sub_manager_links['application']['class'], Translation :: get($sub_manager_links['application']['name']), Theme :: get_image_path() . 'place_mini_' . $sub_manager_links['application']['class'] . '.png', implode("\n", $html)));
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
                
                $agreement_link = array();
                $agreement_link['name'] = Translation :: get('AgreementLink');
                $agreement_link['description'] = Translation :: get('AgreementLinkDescription');
                $agreement_link['action'] = 'agreement';
                $agreement_link['url'] = $this->get_agreement_application_url();
                $links[] = $agreement_link;
                
                $category_link = array();
                $category_link['name'] = Translation :: get('CategoryLink');
                $category_link['description'] = Translation :: get('CategoryLinkDescription');
                $category_link['action'] = 'category';
                $category_link['url'] = $this->get_category_application_url();
                $links[] = $category_link;
                
                $mentor_link = array();
                $mentor_link['name'] = Translation :: get('MentorLink');
                $mentor_link['description'] = Translation :: get('MentorLinkDescription');
                $mentor_link['action'] = 'mentor';
                $mentor_link['url'] = $this->get_mentor_application_url();
                $links[] = $mentor_link;
                
                $organisation_link = array();
                $organisation_link['name'] = Translation :: get('OrganisationLink');
                $organisation_link['description'] = Translation :: get('OrganisationLinkDescription');
                $organisation_link['action'] = 'organisation';
                $organisation_link['url'] = $this->get_organisation_application_url();
                $links[] = $organisation_link;
                
                $period_link = array();
                $period_link['name'] = Translation :: get('PeriodLink');
                $period_link['description'] = Translation :: get('PeriodLinkDescription');
                $period_link['action'] = 'period';
                $period_link['url'] = $this->get_period_application_url();
                $links[] = $period_link;
                
                $region_link = array();
                $region_link['name'] = Translation :: get('RegionLink');
                $region_link['description'] = Translation :: get('RegionLinkDescription');
                $region_link['action'] = 'region';
                $region_link['url'] = $this->get_region_application_url();
                $links[] = $region_link;
                
                $tab_links['links'] = $links;
                break;
            case self :: AGREEMENTTAB :
                
                $tab_links['application'] = array('name' => Translation :: get('AgreementTab'), 'class' => 'agreement');
                
                $agreement_link = array();
                $agreement_link['name'] = Translation :: get('AgreementLink');
                $agreement_link['description'] = Translation :: get('AgreementLinkDescription');
                $agreement_link['action'] = 'agreement';
                $agreement_link['url'] = $this->get_agreement_application_url();
                $links[] = $agreement_link;
                
                $tab_links['links'] = $links;
                break;
            case self :: PERIODTAB :
                
                $tab_links['application'] = array('name' => Translation :: get('PeriodTab'), 'class' => 'period');
                
                $period_link = array();
                $period_link['name'] = Translation :: get('PeriodLink');
                $period_link['description'] = Translation :: get('PeriodLinkDescription');
                $period_link['action'] = 'period';
                $period_link['url'] = $this->get_period_application_url();
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