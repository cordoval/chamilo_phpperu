<?php 
namespace repository\content_object\survey;

use common\libraries\Path;
use common\libraries\BreadcrumbTrail;
use common\libraries\Breadcrumb;
use common\libraries\Translation;
use common\libraries\Request;
use common\libraries\DynamicTabsRenderer;

require_once Path :: get_repository_content_object_path() . '/survey/php/manage/context/forms/context_subscribe_user_form.class.php';
require_once Path :: get_repository_content_object_path() . '/survey/php/manage/context/component/context_viewer.class.php';

class SurveyContextManagerSubscribeUserComponent extends SurveyContextManager
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        
        $context_registration_id = Request :: get(self :: PARAM_CONTEXT_REGISTRATION_ID);
    	$context_id = Request :: get(self :: PARAM_CONTEXT_ID);
        
        //        if (! SurveyContextManagerRights :: is_allowed_in_survey_context_manager_subtree(SurveyContextManagerRights :: SUBSCRIBE_USER_RIGHT, $context_id, SurveyContextManagerRights :: TYPE_CONTEXT_REGISTRATION))
        //        {
        //            $this->display_header();
        //            $this->display_error_message(Translation :: get('NotAllowed'));
        //            $this->display_footer();
        //            exit();
        //        }
        

        $context = SurveyContextDataManager::get_instance()->retrieve_survey_context_by_id($context_id);
                
        $form = new SurveyContextSubscribeUserForm($context, $this->get_url(array(self :: PARAM_CONTEXT_ID => Request :: get(self :: PARAM_CONTEXT_ID))), $this->get_user());
        
        if ($form->validate())
        {
            $success = $form->create_context_rel_users();
            if ($success)
            {
                $this->redirect(Translation :: get('ObjectsCreated',array('OBJECTS' => Translation::get('SurveyContextRelUsers')),Utilities::COMMON_LIBRARIES), (false), array(self :: PARAM_ACTION => self :: ACTION_VIEW_CONTEXT, self :: PARAM_CONTEXT_ID => $context_id,  self :: PARAM_CONTEXT_REGISTRATION_ID => $context_registration_id,DynamicTabsRenderer :: PARAM_SELECTED_TAB => SurveyContextManagerContextViewerComponent :: TAB_USERS));
            }
            else
            {
                $this->redirect(Translation :: get('ObjectsNotCreated',array('OBJECTS' => Translation::get('SurveyContextRelUsers')),Utilities::COMMON_LIBRARIES), (true), array(self :: PARAM_ACTION => self :: ACTION_VIEW_CONTEXT, self :: PARAM_CONTEXT_ID => $context_id,  self :: PARAM_CONTEXT_REGISTRATION_ID => $context_registration_id,DynamicTabsRenderer :: PARAM_SELECTED_TAB => SurveyContextManagerContextViewerComponent :: TAB_USERS));
            }
        }
        else
        {
            $this->display_header();
            $form->display();
            $this->display_footer();
        }
    }

    function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $breadcrumbtrail->add(new Breadcrumb($this->get_url(array(self :: PARAM_ACTION => self :: ACTION_BROWSE_CONTEXT_REGISTRATION)), Translation :: get('BrowseObjects',array('OBJECTS' => Translation::get('ContextRegistrations')),Utilities::COMMON_LIBRARIES)));
        $breadcrumbtrail->add(new Breadcrumb($this->get_url(array(self :: PARAM_ACTION => self :: ACTION_VIEW_CONTEXT_REGISTRATION, self :: PARAM_CONTEXT_REGISTRATION_ID => Request :: get(self :: PARAM_CONTEXT_REGISTRATION_ID))), Translation :: get('ViewObject', array('OBJECT' => Translation::get('ContextRegistration')),Utilities::COMMON_LIBRARIES)));
        $breadcrumbtrail->add(new Breadcrumb($this->get_url(array(self :: PARAM_ACTION => self :: ACTION_VIEW_CONTEXT, self :: PARAM_CONTEXT_ID => Request :: get(self :: PARAM_CONTEXT_ID), DynamicTabsRenderer :: PARAM_SELECTED_TAB => SurveyContextManagerContextViewerComponent :: TAB_USERS)), Translation :: get('ViewObject', array('OBJECT' => Translation::get('SurveyContext')),Utilities::COMMON_LIBRARIES)));
    }

    function get_additional_parameters()
    {
        return array(self :: PARAM_CONTEXT_REGISTRATION_ID, self :: PARAM_CONTEXT_ID);
    }

}
?>