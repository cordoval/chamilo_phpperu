<?php
namespace repository\content_object\survey;

use common\libraries\DynamicTabsRenderer;

use common\libraries\Translation;
use common\libraries\Request;


class SurveyContextManagerTemplateUserImporterComponent extends SurveyContextManager
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        
        $context_template_id = Request :: get(self :: PARAM_CONTEXT_TEMPLATE_ID);
      
    	$form = new ImportTemplateUserForm($this, $this->get_url(), $context_template_id);

        if ($form->validate())
        {
            $success = $form->process();
        	$this->redirect(Translation :: get($success ? 'TemplatesImported' : 'TemplatessNotImported'), $success, array(self :: PARAM_ACTION => self :: ACTION_VIEW_CONTEXT_TEMPLATE, self :: PARAM_CONTEXT_TEMPLATE_ID => $context_template_id, DynamicTabsRenderer :: PARAM_SELECTED_TAB => SurveyContextManagerContextTemplateViewerComponent :: TAB_TEMPLATES ));
        }
        else
        {
            $this->display_header();
            $form->display();
            $this->display_footer();
        }
    }
    
    function get_additional_parameters(){
    	return array(self :: PARAM_CONTEXT_TEMPLATE_ID);
    }
}
?>