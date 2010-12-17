<?php 
namespace repository\content_object\survey;

use common\libraries\Path;
use common\libraries\BreadcrumbTrail;
use common\libraries\Breadcrumb;
use common\libraries\Request;
use common\libraries\Translation;
use common\libraries\DynamicTabsRenderer;

//require_once Path :: get_repository_content_object_path() . 'survey/php/manage/context/forms/template_form.class.php';
//require_once Path :: get_repository_content_object_path() . 'survey/php/survey_template_user.class.php';
//require_once Path :: get_repository_content_object_path() . 'survey/php/manage/context/component/context_template_viewer.class.php';

class SurveyContextManagerTemplateUserCreatorComponent extends SurveyContextManager
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        
        $context_template_id = Request :: get(SurveyContextManager :: PARAM_CONTEXT_TEMPLATE_ID);
        
        $this->set_parameter(SurveyContextManager :: PARAM_CONTEXT_TEMPLATE_ID, $context_template_id);
        
        $context_template = SurveyContextDataManager :: get_instance()->retrieve_survey_context_template($context_template_id);
        
        $survey_template_user = SurveyTemplateUser :: factory($context_template->get_type());
        
        $survey_template_user->set_template_id($context_template_id);
        
        $form = new SurveyTemplateUserForm(SurveyTemplateUserForm :: TYPE_CREATE, $this->get_url(), $survey_template_user, $this->get_user(), $this);
        
        if ($form->validate())
        {
            $tab = SurveyContextManagerContextTemplateViewerComponent :: TAB_TEMPLATES;
            
            $success = $form->create_survey_template_user();
            if ($success)
            {
                $this->redirect(Translation :: get('SurveyTemplateUserCreated'), (false), array(SurveyContextManager :: PARAM_ACTION => SurveyContextManager :: ACTION_VIEW_CONTEXT_TEMPLATE, SurveyContextManager :: PARAM_CONTEXT_TEMPLATE_ID => $context_template_id, DynamicTabsRenderer :: PARAM_SELECTED_TAB => $tab));
            }
            else
            {
                $this->redirect(Translation :: get('SurveyTemplateUserNotCreated'), (false), array(SurveyContextManager :: PARAM_ACTION => SurveyContextManager :: ACTION_VIEW_CONTEXT_TEMPLATE, SurveyContextManager :: PARAM_CONTEXT_TEMPLATE_ID => $context_template_id, DynamicTabsRenderer :: PARAM_SELECTED_TAB => $tab));
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
        $breadcrumbtrail->add(new Breadcrumb($this->get_url(array(self :: PARAM_ACTION => self :: ACTION_VIEW_CONTEXT_TEMPLATE, self :: PARAM_CONTEXT_TEMPLATE_ID => Request :: get(self :: PARAM_CONTEXT_TEMPLATE_ID))), Translation :: get('ViewContextTemplate')));
    }

    function get_additional_parameters()
    {
        return array(self :: PARAM_CONTEXT_TEMPLATE_ID);
    }

}
?>