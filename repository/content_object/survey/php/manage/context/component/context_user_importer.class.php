<?php
namespace repository\content_object\survey;

use common\libraries\DynamicTabsRenderer;

use common\libraries\Translation;
use common\libraries\Request;


class SurveyContextManagerContextUserImporterComponent extends SurveyContextManager
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        
        $context_registration_id = Request :: get(self :: PARAM_CONTEXT_REGISTRATION_ID);
      
    	$form = new ImportContextUserForm($this, $this->get_url(), $context_registration_id);

        if ($form->validate())
        {
            $success = $form->process();
        	$this->redirect(Translation :: get($success ? 'ObjectsImported' : 'ObjectsNotImported',array('OBJECTS' => Translation::get('ContextUsers')),Utilities::COMMON_LIBRARIES), !$success, array(self :: PARAM_ACTION => self :: ACTION_VIEW_CONTEXT_REGISTRATION, self :: PARAM_CONTEXT_REGISTRATION_ID => $context_registration_id));
        }
        else
        {
            $this->display_header();
            $form->display();
            $this->display_footer();
        }
    }
    
    function get_additional_parameters(){
    	return array(self :: PARAM_CONTEXT_REGISTRATION_ID);
    }
}
?>