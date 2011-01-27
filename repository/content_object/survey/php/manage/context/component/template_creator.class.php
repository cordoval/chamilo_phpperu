<?php 
namespace repository\content_object\survey;

use common\libraries\BreadcrumbTrail;
use common\libraries\Breadcrumb;
use common\libraries\Request;
use common\libraries\Translation;
use common\libraries\DynamicTabsRenderer;
use common\libraries\Utilities;

class SurveyContextManagerTemplateCreatorComponent extends SurveyContextManager
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        
        $context_template_id = Request :: get(self :: PARAM_CONTEXT_TEMPLATE_ID);
        
        $template = new SurveyTemplate();
        $template->set_context_template_id($context_template_id);
        
                 
        $form = new SurveyTemplateForm(SurveyTemplateForm :: TYPE_CREATE, $this->get_url(), $template);
        
        if ($form->validate())
        {
            $tab = SurveyContextManagerContextTemplateViewerComponent :: TAB_TEMPLATES;
            
            $success = $form->create_template();
            if ($success)
            {
                $this->redirect(Translation :: get('SurveyTemplateUserCreated'), (false), array(self :: PARAM_ACTION => self :: ACTION_VIEW_CONTEXT_TEMPLATE, self :: PARAM_CONTEXT_TEMPLATE_ID => $context_template_id, DynamicTabsRenderer :: PARAM_SELECTED_TAB => $tab));
            }
            else
            {
                $this->redirect(Translation :: get('SurveyTemplateUserNotCreated'), (false), array(self :: PARAM_ACTION => self :: ACTION_VIEW_CONTEXT_TEMPLATE, self :: PARAM_CONTEXT_TEMPLATE_ID => $context_template_id, DynamicTabsRenderer :: PARAM_SELECTED_TAB => $tab));
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
        $breadcrumbtrail->add(new Breadcrumb($this->get_url(array(self :: PARAM_ACTION => self :: ACTION_VIEW_CONTEXT_TEMPLATE, self :: PARAM_CONTEXT_TEMPLATE_ID => Request :: get(self :: PARAM_CONTEXT_TEMPLATE_ID))), Translation :: get('ViewObject', array('OBJECT' => Translation::get('ContextTemplate')),Utilities::COMMON_LIBRARIES)));
    }

    function get_additional_parameters()
    {
        return array(self :: PARAM_CONTEXT_TEMPLATE_ID);
    }

}
?>