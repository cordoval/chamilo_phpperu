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
        
        $template_id = Request :: get(self :: PARAM_TEMPLATE_ID);
      	
    	$form = new ImportTemplateUserForm($this, $this->get_url(), $template_id);

        if ($form->validate())
        {
            $success = $form->process();
        	$this->redirect(Translation :: get($success ? 'TemplatesImported' : 'TemplatesNotImported'), $success, array(self :: PARAM_ACTION => self :: ACTION_VIEW_TEMPLATE, self :: PARAM_TEMPLATE_ID => $template_id, DynamicTabsRenderer :: PARAM_SELECTED_TAB => SurveyContextManagerTemplateViewerComponent :: TAB_TEMPLATE_USERS ));
        }
        else
        {
            $this->display_header();
            $form->display();
            $this->display_footer();
        }
    }
    
    function get_additional_parameters(){
    	return array(self :: PARAM_CONTEXT_TEMPLATE_ID, self :: PARAM_TEMPLATE_ID);
    }
}
?>