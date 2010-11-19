<?php namespace repository\content_object\survey;
use common\libraries\Path;
use common\libraries\SubManager;

require_once Path :: get_repository_content_object_path() . 'survey/php/context_data_manager/context_data_manager.class.php';
require_once Path :: get_repository_content_object_path() . 'survey/php/survey_context_manager_rights.class.php';
//require_once Path :: get_repository_content_object_path() . 'survey/php/manage/context/component/context_rel_user_table/table.class.php';
//require_once Path :: get_repository_content_object_path() . 'survey/php/manage/context/component/context_rel_group_table/table.class.php';
//require_once Path :: get_repository_content_object_path() . 'survey/php/manage/context/component/registration_browser/browser_table.class.php';
//require_once Path :: get_repository_content_object_path() . 'survey/php/manage/context/component/context_table/table.class.php';

//require_once dirname(__FILE__) . '/component/context_template_browser/browser_table.class.php';
//require_once dirname(__FILE__) . '/component/context_template_rel_page_browser/rel_page_browser_table.class.php';
//require_once dirname(__FILE__) . '/component/context_template_subscribe_page_browser/subscribe_page_browser_table.class.php';


/**
 * @package repository.lib.content_object.survey.manage.context
 *
 * @author Eduard Vossen
 * @author Hans De Bisschop
 */
class SurveyContextManager extends SubManager
{
    
    const APPLICATION_NAME = 'survey_context_manager';
    
    const PARAM_ACTION = 'action';
    
    const PARAM_CONTEXT_REGISTRATION_ID = 'context_registration_id';
    const PARAM_CONTEXT_TEMPLATE_ID = 'context_template_id';
    const PARAM_CONTEXT_ID = 'context_id';
    const PARAM_TEMPLATE_ID = 'template_id';
    const PARAM_CONTEXT = 'context';
    const PARAM_CONTEXT_REL_USER_ID = 'context_template_id';
    const PARAM_CONTEXT_REL_GROUP_ID = 'context_template_id';
    
    const PARAM_SURVEY_PAGE_ID = 'survey_page';
    const PARAM_SURVEY_ID = 'survey_id';
    const PARAM_TEMPLATE_REL_PAGE_ID = 'template_rel_page_id';
    
    const PARAM_COMPONENT_ID = 'component_id';
    
    const ACTION_MANAGER_CHOOSER = 'manager_chooser';
    
    const ACTION_CREATE_CONTEXT_REGISTRATION = 'registration_creator';
    const ACTION_EDIT_CONTEXT_REGISTRATION = 'registration_updater';
    const ACTION_DELETE_CONTEXT_REGISTRATION = 'registration_deleter';
    const ACTION_VIEW_CONTEXT_REGISTRATION = 'registration_viewer';
    const ACTION_BROWSE_CONTEXT_REGISTRATION = 'registration_browser';
    const ACTION_CONTEXT_REGISTRATION_RIGHTS_EDITOR = 'registration_rights_editor';
    
    const ACTION_CREATE_CONTEXT_TEMPLATE = 'context_template_creator';
    const ACTION_EDIT_CONTEXT_TEMPLATE = 'context_template_updater';
    const ACTION_DELETE_CONTEXT_TEMPLATE = 'context_template_deleter';
    const ACTION_VIEW_CONTEXT_TEMPLATE = 'context_template_viewer';
    const ACTION_BROWSE_CONTEXT_TEMPLATE = 'context_template_browser';
    const ACTION_CONTEXT_TEMPLATE_RIGHTS_EDITOR = 'context_template_rights_editor';
    
    const ACTION_SUBSCRIBE_CONTEXT_TEMPLATE = 'subscribe_context_template';
    //    const ACTION_SUBSCRIBE_PAGE_BROWSER = 'context_template_subscribe_page_browser';
    const ACTION_SUBSCRIBE_PAGE_BROWSER = 'context_browser';
    
    const ACTION_UNSUBSCRIBE_PAGE_FROM_TEMPLATE = 'page_unsubscriber';
    const ACTION_SUBSCRIBE_PAGE_TO_TEMPLATE = 'page_subscriber';
    const ACTION_TRUNCATE_TEMPLATE = 'context_template_truncater';
    
    const ACTION_CREATE_CONTEXT = 'context_creator';
    const ACTION_EDIT_CONTEXT = 'context_updater';
    const ACTION_DELETE_CONTEXT = 'context_deleter';
    const ACTION_VIEW_CONTEXT = 'context_viewer';
    const ACTION_SUBSCRIBE_USER = 'subscribe_user';
    const ACTION_SUBSCRIBE_GROUP = 'subscribe_group';
    const ACTION_UNSUBSCRIBE_USER = 'unsubscribe_user';
    const ACTION_UNSUBSCRIBE_GROUP = 'unsubscribe_group';
    
    const ACTION_CREATE_TEMPLATE = 'template_creator';
    const ACTION_EDIT_TEMPLATE = 'template_updater';
    const ACTION_DELETE_TEMPLATE = 'template_deleter';
    
    const ACTION_DELETE_SURVEY_REL_CONTEXT_TEMPLATE = 'survey_context_deleter';
    
    const ACTION_RIGHTS_EDITOR = 'rights_editor';
    
    const DEFAULT_ACTION = self :: ACTION_MANAGER_CHOOSER;

    function __construct($repository_manager)
    {
        parent :: __construct($repository_manager);
    }

    function get_application_component_path()
    {
        return Path :: get_repository_path() . 'lib/content_object/survey/manage/context/component/';
    }

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

    function get_context_registration_update_url($context_registration)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_EDIT_CONTEXT_REGISTRATION, self :: PARAM_CONTEXT_REGISTRATION_ID => $context_registration->get_id()));
    }

    function get_context_registration_delete_url($context_registration)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_DELETE_CONTEXT_REGISTRATION, self :: PARAM_CONTEXT_REGISTRATION_ID => $context_registration->get_id()));
    }

    function get_context_creation_url($context_registration)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_CREATE_CONTEXT, self :: PARAM_CONTEXT_REGISTRATION_ID => $context_registration->get_id()));
    }

    function get_context_view_url($context)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_VIEW_CONTEXT, self :: PARAM_CONTEXT_ID => $context->get_id()));
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

    function get_context_subscribe_user_url($context)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_SUBSCRIBE_USER, self :: PARAM_CONTEXT_ID => $context->get_id()));
    }

    function get_context_subscribe_group_url($context)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_SUBSCRIBE_GROUP, self :: PARAM_CONTEXT_ID => $context->get_id()));
    }

    function get_context_unsubscribe_user_url($context_rel_user)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_UNSUBSCRIBE_USER, self :: PARAM_CONTEXT_REL_USER_ID => $context_rel_user->get_context_id() . '|' . $context_rel_user->get_user_id()));
    
    }

    function get_context_unsubscribe_group_url($context_rel_group)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_UNSUBSCRIBE_GROUP, self :: PARAM_CONTEXT_REL_GROUP_ID => $context_rel_group->get_context_id() . '|' . $context_rel_group->get_group_id()));
    
    }

    function get_rights_editor_url($component_id)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_RIGHTS_EDITOR, self :: PARAM_COMPONENT_ID => $component_id));
    }

    function get_context_registration_rights_editor_url($context_registration)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_CONTEXT_REGISTRATION_RIGHTS_EDITOR, self :: PARAM_CONTEXT_REGISTRATION_ID => $context_registration->get_id()));
    }

    function get_context_template_rights_editor_url($contex_template)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_CONTEXT_TEMPLATE_RIGHTS_EDITOR, self :: PARAM_CONTEXT_TEMPLATE_ID => $contex_template->get_id()));
    }

    function get_subscribe_context_template_url($survey)
    {
        $context_template_id = Request :: get(self :: PARAM_CONTEXT_TEMPLATE_ID);
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_SUBSCRIBE_CONTEXT_TEMPLATE, self :: PARAM_SURVEY_ID => $survey->get_id(), self :: PARAM_CONTEXT_TEMPLATE_ID => $context_template_id));
    }

    function get_context_template_suscribe_page_browser_url($survey)
    {
        $context_template_id = Request :: get(self :: PARAM_CONTEXT_TEMPLATE_ID);
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_SUBSCRIBE_PAGE_BROWSER, self :: PARAM_CONTEXT_TEMPLATE_ID => $context_template_id, self :: PARAM_SURVEY_ID => $survey->get_id()));
    }

    function get_context_template_suscribe_page_url( $survey_page)
    {
        $context_template_id = Request :: get(self :: PARAM_CONTEXT_TEMPLATE_ID);
        $survey_id = Request :: get(self :: PARAM_SURVEY_ID);
    	return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_SUBSCRIBE_PAGE_TO_TEMPLATE, self :: PARAM_TEMPLATE_REL_PAGE_ID => $survey_id.'_'.$context_template_id . '_' . $survey_page->get_id()));
    }
	
        
    function get_context_template_unsubscribing_page_url($template_rel_page)
    {
        $id = $template_rel_page->get_survey_id() . '_' . $template_rel_page->get_template_id() . '_' . $template_rel_page->get_page_id();
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_UNSUBSCRIBE_PAGE_FROM_TEMPLATE, self :: PARAM_TEMPLATE_REL_PAGE_ID => $id));
    }
    
    static function launch($application)
    {
        parent :: launch(__CLASS__, $application);
    }

    /**
     * Helper function for the SubManager class,
     * pending access to class constants via variables in PHP 5.3
     * e.g. $name = $class :: DEFAULT_ACTION
     *
     * DO NOT USE IN THIS SUBMANAGER'S CONTEXT
     * Instead use:
     * - self :: DEFAULT_ACTION in the context of this class
     * - YourSubManager :: DEFAULT_ACTION in all other application classes
     */
    static function get_default_action()
    {
        return self :: DEFAULT_ACTION;
    }

    /**
     * Helper function for the SubManager class,
     * pending access to class constants via variables in PHP 5.3
     * e.g. $name = $class :: PARAM_ACTION
     *
     * DO NOT USE IN THIS SUBMANAGER'S CONTEXT
     * Instead use:
     * - self :: PARAM_ACTION in the context of this class
     * - YourSubManager :: PARAM_ACTION in all other application classes
     */
    static function get_action_parameter()
    {
        return self :: PARAM_ACTION;
    }

    function display_header()
    {
        Application :: display_header();
    }

    function display_footer()
    {
        Application :: display_footer();
    }

    function has_menu()
    {
        return false;
    }
}

?>