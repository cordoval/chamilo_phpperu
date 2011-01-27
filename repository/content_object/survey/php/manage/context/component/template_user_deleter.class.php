<?php
namespace repository\content_object\survey;

use common\libraries\EqualityCondition;
use common\libraries\Request;
use common\libraries\Translation;
use common\libraries\Utilities;

class SurveyContextManagerTemplateUserDeleterComponent extends SurveyContextManager
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $context_template_id = Request :: get(self :: PARAM_CONTEXT_TEMPLATE_ID);
        $context_template = SurveyContextDataManager :: get_instance()->retrieve_survey_context_template($context_template_id);
        $type = $context_template->get_type();
        
        $ids = Request :: get(self :: PARAM_TEMPLATE_USER_ID);
        $failures = 0;
        
        if (! empty($ids))
        {
            if (! is_array($ids))
            {
                $ids = array($ids);
            }
            
            foreach ($ids as $id)
            {
                $condition = new EqualityCondition(SurveyTemplateUser :: PROPERTY_USER_ID, $id, SurveyTemplateUser :: get_table_name());
                
                $context_template_users = SurveyContextDataManager :: get_instance()->retrieve_survey_template_users($type, $condition);
                
                while ($context_template_user = $context_template_users->next_result())
                {
                	if (! $context_template_user->delete())
                    {
                        $failures ++;
                    }
                }
            }
            
            if ($failures)
            {
                if (count($ids) == 1)
                {
                    $message = 'ObjectNotDeleted';
                }
                else
                {
                    $message = 'ObjectsNotDeleted';
                }
            }
            else
            {
                if (count($ids) == 1)
                {
                    $message = 'ObjectDeleted';
                }
                else
                {
                    $message = 'ObjectsDeleted';
                }
            }
            
            $this->redirect(Translation :: get($message, array('OBJECT' => Translation::get('SelectedTemplateUser'),'OBJECTS' => Translation::get('SelectedTemplateUsers')),Utilities::COMMON_LIBRARIES), ($failures ? true : false), array(self :: PARAM_ACTION => self :: ACTION_VIEW_CONTEXT_TEMPLATE, self :: PARAM_CONTEXT_TEMPLATE_ID => $context_template_id));
        }
        else
        {
            $this->display_error_page(htmlentities(Translation :: get('NoTemplateUsersSelected')));
        }
    }
}
?>