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
    const PARAM_CONTEXT_ID = 'context_id';
    
    const ACTION_CREATE_CONTEXT_REGISTRATION = 'create_context_registration';
    const ACTION_EDIT_CONTEXT_REGISTRATION = 'edit_context_registration';
    const ACTION_DELETE_CONTEXT_REGISTRATION = 'delete_context_registration';
    const ACTION_VIEW_CONTEXT_REGISTRATION = 'view_context_registration';
    const ACTION_BROWSE_CONTEXT_REGISTRATION = 'browse_context_registration';
    
    const ACTION_CREATE_CONTEXT = 'create_context_registration';
    const ACTION_EDIT_CONTEXT = 'edit_context_registration';
    const ACTION_DELETE_CONTEXT = 'delete_context_registration';

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
            default :
                $this->set_parameter(self :: PARAM_ACTION, self :: ACTION_BROWSE_CONTEXT_REGISTRATION);
                $component = $this->create_component('RegistrationBrowser');
        }
        
        $component->run();
    }

    //url
    

    function get_context_registration_viewing_url($context_registration)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_VIEW_CONTEXT_REGISTRATION, self :: PARAM_CONTEXT_REGISTRATION_ID => $context_registration->get_id(), 'type' => 'survey', 'manage' => 'context'));
    }

    function get_context_registration_creation_url()
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_CREATE_CONTEXT_REGISTRATION, 'type' => 'survey', 'manage' => 'context'));
    }

    function get_context_creation_url($context_registration)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_CREATE_CONTEXT, self :: PARAM_CONTEXT_REGISTRATION_ID => $context_registration->get_id(), 'type' => 'survey', 'manage' => 'context'));
    }

}

?>