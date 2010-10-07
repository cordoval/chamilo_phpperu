<?php

class SurveyContextManagerManagerChooserComponent extends SurveyContextManager implements DelegateComponent
{
    
    const TAB_CONTEXT = 0;
    const TAB_TEMPLATE = 1;
    const TAB_SURVEY = 2;

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        
        $links = $this->get_context_manager_links();
        
        $this->display_header();
        echo $this->get_context_manager_tabs($links);
        $this->display_footer();
    }

    /**
     * Returns an HTML representation of the actions.
     * @return string $html HTML representation of the actions.
     */
    function get_context_manager_tabs($links)
    {
        $renderer_name = Utilities :: camelcase_to_underscores(get_class($this));
        $context_manager_tabs = new DynamicTabsRenderer($renderer_name);
        
        $index = 0;
        foreach ($links as $manager_links)
        {
            if (count($manager_links['links']))
            {
                $index ++;
                $actions_tab = new DynamicActionsTab($manager_links['application']['class'], $manager_links['application']['name'], Theme :: get_image_path() . 'place_mini_' . $manager_links['application']['class'] . '.png', implode("\n", $html));
                
                foreach ($manager_links['links'] as $action)
                {
                    $actions_tab->add_action($action);
                }
                
                $context_manager_tabs->add_tab($actions_tab);
            }
        }
        
        return $context_manager_tabs->render();
    }

    function get_context_manager_links()
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
            case self :: TAB_CONTEXT :
                
                $tab_links['application'] = array('name' => Translation :: get('ContextTab'), 'class' => 'context');
                
                $registration_link = new DynamicAction();
                $registration_link->set_title(Translation :: get('SurveyContextRegistrationLink'));
                $registration_link->set_description(Translation :: get('SurveyContextRegistrationDescription'));
                $registration_link->set_image(Theme :: get_image_path(RepositoryManager :: APPLICATION_NAME) . 'place_mini_survey.png');
                $registration_link->set_url($this->get_context_registration_browsing_url());
                $links[] = $registration_link;
                
                $tab_links['links'] = $links;
                break;
            
            case self :: TAB_TEMPLATE :
                
                $tab_links['application'] = array('name' => Translation :: get('TemplateTab'), 'class' => 'template');
                
                $template_link = new DynamicAction();
                $template_link->set_title(Translation :: get('SurveyContextTemplateLink'));
                $template_link->set_description(Translation :: get('SurveyContextTemplateDescription'));
                $template_link->set_image(Theme :: get_image_path(RepositoryManager :: APPLICATION_NAME) . 'place_mini_survey.png');
                $template_link->set_url($this->get_context_template_browsing_url());
                $links[] = $template_link;
                
                $tab_links['links'] = $links;
                break;
            case self :: TAB_SURVEY :
                
                $tab_links['application'] = array('name' => Translation :: get('SurveyTab'), 'class' => 'survey');
                
                $survey_link = new DynamicAction();
                $survey_link->set_title(Translation :: get('SurveyContextTemplateLink'));
                $survey_link->set_description(Translation :: get('SurveyContextTemplateDescription'));
                $survey_link->set_image(Theme :: get_image_path(RepositoryManager :: APPLICATION_NAME) . 'place_mini_survey.png');
                $survey_link->set_url($this->get_context_template_browsing_url());
                $links[] = $survey_link;
                
                $tab_links['links'] = $links;
                break;
            
            default :
                
                break;
        }
        
        return $tab_links;
    }
}
?>