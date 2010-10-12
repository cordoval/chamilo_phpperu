<?php
require_once Path :: get_repository_path() . 'lib/content_object/survey/manage/context/forms/context_form.class.php';

require_once Path :: get_repository_path() . 'lib/content_object/survey/survey_context.class.php';

class SurveyContextManagerContextTemplateDeleterComponent extends SurveyContextManager
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $ids = $_GET[SurveyContextManager :: PARAM_CONTEXT_TEMPLATE_ID];
        $failures = 0;
        
        if (! empty($ids))
        {
            if (! is_array($ids))
            {
                $ids = array($ids);
            }
            
            foreach ($ids as $id)
            {
                $context_template  = SurveyContextDataManager::get_instance()->retrieve_survey_context_template($id);
               
                if (! $context_template->delete())
                {
                    $failures ++;
                }
            }
            
            if ($failures)
            {
                if (count($ids) == 1)
                {
                    $message = 'SelectedContextTemplateNotDeleted';
                }
                else
                {
                    $message = 'SelectedContextTemplatesNotDeleted';
                }
            }
            else
            {
                if (count($ids) == 1)
                {
                    $message = 'SelectedContextTemplateDeleted';
                }
                else
                {
                    $message = 'SelectedContextTemplatesDeleted';
                }
            }
            
            $this->redirect(Translation :: get($message), ($failures ? true : false), array(SurveyContextManager :: PARAM_ACTION => SurveyContextManager :: ACTION_BROWSE_CONTEXT_TEMPLATE));
        }
        else
        {
            $this->display_error_page(htmlentities(Translation :: get('NoContextTemplatesSelected')));
        }
    }
}
?>