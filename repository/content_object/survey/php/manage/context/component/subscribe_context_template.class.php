<?php 
namespace repository\content_object\survey;

use repository\RepositoryDataManager;
use common\libraries\Translation;
use common\libraries\DynamicTabsRenderer;
use common\libraries\Path;
use common\libraries\Request;


require_once Path :: get_repository_content_object_path() . '/survey/php/manage/context/component/context_template_viewer.class.php';

class SurveyContextManagerSubscribeContextTemplateComponent extends SurveyContextManager
{
    
    private $survey_id;

    function run()
    {
        
        $survey_id = Request :: get(self :: PARAM_SURVEY_ID);
        $context_template_id = Request :: get(self :: PARAM_CONTEXT_TEMPLATE_ID);
        $survey = RepositoryDataManager :: get_instance()->retrieve_content_object($survey_id, Survey :: get_type_name());
        
        $survey->set_context_template_id($context_template_id);
        $succes = $survey->update();
        
        if ($succes)
        {
            $this->redirect(Translation :: get('SurveyContextTemplateAdded'), ! $succes, array(self :: PARAM_ACTION => self :: ACTION_VIEW_CONTEXT_TEMPLATE, self :: PARAM_CONTEXT_TEMPLATE_ID => $context_template_id, DynamicTabsRenderer :: PARAM_SELECTED_TAB => SurveyContextManagerContextTemplateViewerComponent :: TAB_SURVEYS));
        }
        else
        {
            $this->redirect(Translation :: get('SurveyContextTemplateNotAdded'), ! $succes, array(self :: PARAM_ACTION => self :: ACTION_VIEW_CONTEXT_TEMPLATE, self :: PARAM_CONTEXT_TEMPLATE_ID => $context_template_id, DynamicTabsRenderer :: PARAM_SELECTED_TAB => SurveyContextManagerContextTemplateViewerComponent :: TAB_ADD_TEMPLATE));
        }
    
    }

}

?>