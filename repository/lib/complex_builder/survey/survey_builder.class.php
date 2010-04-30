<?php

require_once dirname(__FILE__) . '/survey_builder_component.class.php';

class SurveyBuilder extends ComplexBuilder
{
    
    const ACTION_CREATE_SURVEY = 'create';
    const ACTION_BROWSE_CONTEXT = 'browse_context';
    const ACTION_CONFIGURE_CONTEXT = 'configure_context';
    
    const PARAM_SURVEY_PAGE_ID = 'survey_page';
    const PARAM_SURVEY_ID = 'survey';
    const PARAM_TEMPLATE_ID = 'template_id';

    const PARAM_REMOVE_SELECTED ='remove';
    const PARAM_TRUNCATE_SELECTED = 'truncate';
    
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
                $component = SurveyBuilderComponent :: factory('ContextTemplateBrowser', $this);
                break;
        }
        
        if (! $component)
            parent :: run();
        else
            $component->run();
    }

    function get_configure_context_url($selected_cloi)
    {
        $cloi_id = ($this->get_cloi()) ? ($this->get_cloi()->get_id()) : null;
        return $this->get_url(array(self :: PARAM_BUILDER_ACTION => self :: ACTION_CONFIGURE_CONTEXT, self :: PARAM_ROOT_LO => $this->get_root_lo()->get_id(), self :: PARAM_CLOI_ID => $cloi_id, self :: PARAM_SELECTED_CLOI_ID => $selected_cloi, 'publish' => Request :: get('publish')));
    }

    function get_browse_context_url($selected_cloi)
    {
        $cloi_id = ($this->get_cloi()) ? ($this->get_cloi()->get_id()) : null;
        return $this->get_url(array(self :: PARAM_BUILDER_ACTION => self :: ACTION_BROWSE_CONTEXT, self :: PARAM_ROOT_LO => $this->get_root_lo()->get_id(), self :: PARAM_CLOI_ID => $cloi_id, self :: PARAM_SELECTED_CLOI_ID => $selected_cloi, 'publish' => Request :: get('publish')));
    }

}

?>