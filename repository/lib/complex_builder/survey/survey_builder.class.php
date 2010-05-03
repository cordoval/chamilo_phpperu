<?php

require_once dirname(__FILE__) . '/survey_builder_component.class.php';

class SurveyBuilder extends ComplexBuilder
{
    
    const ACTION_CREATE_SURVEY = 'create';
    
    const ACTION_CONFIGURE_CONTEXT = 'configure_context';
    const ACTION_BROWSE_CONTEXT = 'browse_context';
    const ACTION_VIEW_CONTEXT = 'view_context';
    const ACTION_SUBSCRIBE_PAGE_BROWSER = 'subscribe_browser';
    
    const PARAM_SURVEY_PAGE_ID = 'survey_page';
    const PARAM_SURVEY_ID = 'survey';
    const PARAM_TEMPLATE_ID = 'template_id';
    
    const PARAM_TRUNCATE_SELECTED = 'truncate';
    const PARAM_UNSUBSCRIBE_SELECTED = 'unsubscribe_selected';

    function run()
    {
        $action = $this->get_action();
        
        switch ($action)
        {
            case ComplexBuilder :: ACTION_BROWSE_CLO :
                $component = SurveyBuilderComponent :: factory('Browser', $this);
                break;
            case SurveyBuilder :: ACTION_CREATE_SURVEY :
                $component = SurveyBuilderComponent :: factory('Creator', $this);
                break;
            case SurveyBuilder :: ACTION_CONFIGURE_CONTEXT :
                $component = SurveyBuilderComponent :: factory('ConfigureContext', $this);
                break;
            case SurveyBuilder :: ACTION_BROWSE_CONTEXT :
                $component = SurveyBuilderComponent :: factory('ContextBrowser', $this);
                break;
            case SurveyBuilder :: ACTION_VIEW_CONTEXT :
                $component = SurveyBuilderComponent :: factory('ContextViewer', $this);
                break;
           case SurveyBuilder :: ACTION_SUBSCRIBE_PAGE_BROWSER :
                $component = SurveyBuilderComponent :: factory('ContextTemplateSubscribePageBrowser', $this);
                break;     
        }
        
        if (! $component)
            parent :: run();
        else
            $component->run();
    }

    function get_configure_context_url()
    {
        return $this->get_url(array(self :: PARAM_BUILDER_ACTION => self :: ACTION_BROWSE_CONTEXT, self :: PARAM_ROOT_LO => $this->get_root_lo()->get_id(), self :: PARAM_TEMPLATE_ID => $this->get_root_lo()->get_context_template_id(), 'publish' => Request :: get('publish')));
    }

    function get_template_viewing_url($template_id)
    {
        return $this->get_url(array(self :: PARAM_BUILDER_ACTION => self :: ACTION_VIEW_CONTEXT, self :: PARAM_ROOT_LO => $this->get_root_lo()->get_id(), self :: PARAM_TEMPLATE_ID => $this->get_root_lo()->get_context_template_id()));
    }

    function get_template_suscribe_page_browser_url($template_id)
    {
    	return $this->get_url(array(self :: PARAM_BUILDER_ACTION => self :: ACTION_SUBSCRIBE_PAGE_BROWSER, self :: PARAM_ROOT_LO => $this->get_root_lo()->get_id(), self :: PARAM_TEMPLATE_ID => $this->get_root_lo()->get_context_template_id()));   	
    }

    function get_template_emptying_url($template_id)
    {
    
    }

}

?>