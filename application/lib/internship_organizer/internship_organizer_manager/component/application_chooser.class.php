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
        $html = array();
        $html[] = '<a name="top"></a>';
        $html[] = '<div id="internship_organizer_tabs">';
        $html[] = '<ul>';
        
        // Render the tabs
        $index = 0;
        
        $selected_tab = 0;
        
        foreach ($links as $sub_manager_links)
        {
            if (! count($sub_manager_links['links']))
            {
                continue;
            }
            
            $index ++;
            
            if (Request :: get('selected') == $sub_manager_links['application']['class'])
            {
                $selected_tab = $index - 1;
            }
            
            $html[] = '<li><a href="#internship_organizer_tabs-' . $index . '">';
            $html[] = '<span class="category">';
            $html[] = '<img src="' . Theme :: get_image_path('internship_organizer') . 'place_mini_' . $sub_manager_links['application']['class'] . '.png" border="0" style="vertical-align: middle;" alt="' . $sub_manager_links['application']['name'] . '" title="' . $sub_manager_links['application']['name'] . '"/>';
            $html[] = '<span class="title">' . $sub_manager_links['application']['name'] . '</span>';
            $html[] = '</span>';
            $html[] = '</a></li>';
        }
        
        $html[] = '</ul>';
        
        $index = 0;
        foreach ($links as $sub_manager_links)
        {
            if (count($sub_manager_links['links']))
            {
                $index ++;
                $html[] = '<h2><img src="' . Theme :: get_image_path('internship_organizer') . 'place_mini_' . $sub_manager_links['application']['class'] . '.png" border="0" style="vertical-align: middle;" alt="' . $sub_manager_links['application']['name'] . '" title="' . $sub_manager_links['application']['name'] . '"/>&nbsp;' . $sub_manager_links['application']['name'] . '</h2>';
                $html[] = '<div class="internship_organizer_tab" id="internship_organizer_tabs-' . $index . '">';
                
                $html[] = '<a class="prev"></a>';
                
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
                
//                $condition = new EqualityCondition(Setting :: PROPERTY_APPLICATION, $sub_manager_links['application']['class']);
//                $application_settings_count = AdminDataManager :: get_instance()->count_settings($condition);
//                
//                if ($application_settings_count)
//                {
//                    if (! isset($sub_manager_links['search']))
//                    {
//                        $html[] = '<div class="vertical_action" style="border-top: none;">';
//                    }
//                    else
//                    {
//                        $html[] = '<div class="vertical_action">';
//                    }
//                    
//                    //                    $settings_url = $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_CONFIGURE_PLATFORM, self :: PARAM_WEB_APPLICATION => $sub_manager_links['application']['class']));
//                    
//
//                    $settings_url = 'settingsurl';
//                    
//                    $html[] = '<div class="icon">';
//                    $html[] = '<a href="' . $settings_url . '"><img src="' . Theme :: get_image_path('internship_organizer') . 'browse_manage.png" alt="' . Translation :: get('Settings') . '" title="' . Translation :: get('Settings') . '"/></a>';
//                    $html[] = '</div>';
//                    $html[] = '<div class="description">';
//                    $html[] = '<h4><a href="' . $settings_url . '" ' . $onclick . '>' . Translation :: get('Settings') . '</a></h4>';
//                    $html[] = Translation :: get('SettingsDescription');
//                    $html[] = '</div>';
//                    $html[] = '</div>';
//                }
                
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
                
                //                if (isset($sub_manager_links['search']))
                //                {
                //                    $search_form = new AdminSearchForm($this, $sub_manager_links['search'], $index);
                //
                //                    $html[] = '<div class="vertical_action">';
                //                    $html[] = '<div class="icon">';
                //                    $html[] = '<img src="' . Theme :: get_image_path() . 'browse_search.png" alt="' . Translation :: get('Search') . '" title="' . Translation :: get('Search') . '"/>';
                //                    $html[] = '</div>';
                //                    $html[] = $search_form->display();
                //                    $html[] = '</div>';
                //                }
                

                $html[] = '</div>';
                
                $html[] = '<a class="next"></a>';
                
                $html[] = '<div class="clear"></div>';
                
                $html[] = '</div>';
            }
        }
        
        $html[] = '</div>';
        $html[] = '<br /><a href="#top">' . Translation :: get('Top') . '</a>';
        $html[] = '<script type="text/javascript">';
        $html[] = '  var tabnumber = ' . $selected_tab . ';';
        $html[] = '</script>';
        $html[] = ResourceManager :: get_instance()->get_resource_html(Path :: get(WEB_LIB_PATH) . 'javascript/internship_organizer_ajax.js');
        
        return implode("\n", $html);
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