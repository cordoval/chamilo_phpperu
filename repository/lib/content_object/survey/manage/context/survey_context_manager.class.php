<?php
/**
 * @package repository.lib.content_object.survey.manage.context
 *
 * @author Eduard Vossen
 * @author Hans De Bisschop
 */
class SurveyContextManager extends SubManager
{
    
    const PARAM_ACTION = 'action';
    
    const PARAM_CONTEXT_REGISTRATION_ID = 'context_registration_id';
    const PARAM_CONTEXT_TEMPLATE_ID = 'context_template_id';
    const PARAM_CONTEXT_ID = 'context_id';
    const PARAM_TEMPLATE_ID = 'template_id';
    const PARAM_CONTEXT = 'context';
    
    const ACTION_MANAGER_CHOOSER = 'chooser';
    
    const ACTION_CREATE_CONTEXT_REGISTRATION = 'create_context_registration';
    const ACTION_EDIT_CONTEXT_REGISTRATION = 'edit_context_registration';
    const ACTION_DELETE_CONTEXT_REGISTRATION = 'delete_context_registration';
    const ACTION_VIEW_CONTEXT_REGISTRATION = 'view_context_registration';
    const ACTION_BROWSE_CONTEXT_REGISTRATION = 'browse_context_registration';
    
    const ACTION_CREATE_CONTEXT_TEMPLATE = 'create_context_template';
    const ACTION_EDIT_CONTEXT_TEMPLATE = 'edit_context_template';
    const ACTION_DELETE_CONTEXT_TEMPLATE = 'delete_context_template';
    const ACTION_VIEW_CONTEXT_TEMPLATE = 'view_context_template';
    const ACTION_BROWSE_CONTEXT_TEMPLATE = 'browse_context_template';
    
    const ACTION_CREATE_CONTEXT = 'create_context';
    const ACTION_EDIT_CONTEXT = 'edit_context';
    const ACTION_DELETE_CONTEXT = 'delete_context';
    
    const ACTION_CREATE_TEMPLATE = 'create_template';
    const ACTION_EDIT_TEMPLATE = 'edit_template';
    const ACTION_DELETE_TEMPLATE = 'delete_template';

    function SurveyContextManager($repository_manager)
    {
        parent :: __construct($repository_manager);
        $action = Request :: get(self :: PARAM_ACTION);
        if ($action)
        {
            $this->set_parameter(self :: PARAM_ACTION, $action);
        }
    
    }

    function get_application_component_path()
    {
        return Path :: get_repository_path() . 'lib/content_object/survey/manage/context/component/';
    }

    function run()
    {
        $this->set_parameter(RepositoryManager :: PARAM_CONTENT_OBJECT_TYPE, Survey :: get_type_name());
        $this->set_parameter(RepositoryManager :: PARAM_CONTENT_OBJECT_MANAGER_TYPE, self :: PARAM_CONTEXT);
        $action = $this->get_parameter(self :: PARAM_ACTION);
        
        switch ($action)
        {
            
            case self :: ACTION_CREATE_CONTEXT_REGISTRATION :
                $component = $this->create_component('RegistrationCreator');
                break;
            case self :: ACTION_EDIT_CONTEXT_REGISTRATION :
                $component = $this->create_component('RegistrationEditor');
                break;
            case self :: ACTION_DELETE_CONTEXT_REGISTRATION :
                $component = $this->create_component('RegistrationDeleter');
                break;
            case self :: ACTION_VIEW_CONTEXT_REGISTRATION :
                $component = $this->create_component('RegistrationViewer');
                break;
            case self :: ACTION_BROWSE_CONTEXT_REGISTRATION :
                $component = $this->create_component('RegistrationBrowser');
                break;
            case self :: ACTION_CREATE_CONTEXT :
                $component = $this->create_component('ContextCreator');
                break;
            case self :: ACTION_EDIT_CONTEXT :
                $component = $this->create_component('ContextUpdater');
                break;
             case self :: ACTION_CREATE_TEMPLATE :
                $component = $this->create_component('TemplateCreator');
                break;
            case self :: ACTION_EDIT_TEMPLATE :
                $component = $this->create_component('TemplateUpdater');
                break;    
            case self :: ACTION_CREATE_CONTEXT_TEMPLATE :
                $component = $this->create_component('ContextTemplateCreator');
                break;
            case self :: ACTION_EDIT_CONTEXT_TEMPLATE :
                $component = $this->create_component('ContextTemplateUpdater');
                break;
            case self :: ACTION_DELETE_CONTEXT_TEMPLATE :
                $component = $this->create_component('ContextTemplateDeleter');
                break;
            case self :: ACTION_VIEW_CONTEXT_TEMPLATE :
                $component = $this->create_component('ContextTemplateViewer');
                break;
            case self :: ACTION_BROWSE_CONTEXT_TEMPLATE :
                $component = $this->create_component('ContextTemplateBrowser');
                break;
            
            default :
                $this->set_parameter(self :: PARAM_ACTION, self :: ACTION_MANAGER_CHOOSER);
                $component = $this->create_component('ManagerChooser');
        }
        
        $component->run();
    }

    //url
    

    function get_context_registration_browsing_url()
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_BROWSE_CONTEXT_REGISTRATION));
    }

    function get_context_registration_viewing_url($context_registration)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_VIEW_CONTEXT_REGISTRATION, self :: PARAM_CONTEXT_REGISTRATION_ID => $context_registration->get_id()));
    }

    function get_context_registration_creation_url()
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_CREATE_CONTEXT_REGISTRATION));
    }

    function get_context_creation_url($context_registration)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_CREATE_CONTEXT, self :: PARAM_CONTEXT_REGISTRATION_ID => $context_registration->get_id()));
    }

    function get_context_update_url($context_registration_id, $survey_context)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_EDIT_CONTEXT, self :: PARAM_CONTEXT_REGISTRATION_ID => $context_registration_id, self :: PARAM_CONTEXT_ID => $survey_context->get_id()));
    }

    function get_context_template_browsing_url()
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_BROWSE_CONTEXT_TEMPLATE));
    }

    function get_context_template_viewing_url($context_template)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_VIEW_CONTEXT_TEMPLATE, self :: PARAM_CONTEXT_TEMPLATE_ID => $context_template->get_id()));
    }

    function get_context_template_creation_url()
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_CREATE_CONTEXT_TEMPLATE));
    }

    function get_context_template_delete_url($context_template)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_DELETE_CONTEXT_TEMPLATE, self :: PARAM_CONTEXT_TEMPLATE_ID => $context_template->get_id()));
    }

    function get_context_template_update_url($context_template)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_EDIT_CONTEXT_TEMPLATE, self :: PARAM_CONTEXT_TEMPLATE_ID => $context_template->get_id()));
    }

    function get_template_creation_url($context_template)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_CREATE_TEMPLATE, self :: PARAM_CONTEXT_TEMPLATE_ID => $context_template->get_id()));
    }
    
	function get_template_update_url($template)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_EDIT_TEMPLATE, self :: PARAM_TEMPLATE_ID => $template->get_id()));
    }
    
}

?>