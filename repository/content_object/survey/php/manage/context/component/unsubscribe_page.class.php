<?php
namespace repository\content_object\survey;

//use common\libraries\Path;
use common\libraries\BreadcrumbTrail;
use common\libraries\Breadcrumb;
use common\libraries\Translation;
use common\libraries\Request;
use common\libraries\DynamicTabsRenderer;
use common\libraries\EqualityCondition;
use common\libraries\AndCondition;
use common\libraries\Utilities;

//require_once Path :: get_repository_content_object_path() . '/survey/php/survey_context_template_rel_page.class.php';


class SurveyContextManagerUnsubscribePageComponent extends SurveyContextManager
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        
        $ids = Request :: get(self :: PARAM_TEMPLATE_REL_PAGE_ID);
             
        $failures = 0;
        
        if (! empty($ids))
        {
            if (! is_array($ids))
            {
                $ids = array($ids);
            }
                    
            foreach ($ids as $id)
            {

            	$templaterelpage_ids = explode('_', $id);
                    	
                $conditions = array();
                $conditions[] = new EqualityCondition(SurveyContextTemplateRelPage :: PROPERTY_PAGE_ID, $templaterelpage_ids[2]);
                $conditions[] = new EqualityCondition(SurveyContextTemplateRelPage :: PROPERTY_SURVEY_ID, $templaterelpage_ids[0]);
                $conditions[] = new EqualityCondition(SurveyContextTemplateRelPage :: PROPERTY_TEMPLATE_ID, $templaterelpage_ids[1]);
                $condition = new AndCondition($conditions);
                       
                $templaterelpage = SurveyContextDataManager :: get_instance()->retrieve_template_rel_pages($condition)->next_result();
                              
                if (! isset($templaterelpage))
                {
                    continue;
                }
                else
                {
                    
                    if (! $templaterelpage->delete())
                    {
                        $failures ++;
                    }
                    else
                    {
                        //                        Event :: trigger('unsubscribe_user', 'category', array('target_category_id' => $categoryreluser->get_category_id(), 'target_user_id' => $categoryreluser->get_user_id(), 'action_user_id' => $user->get_id()));
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
            
            $this->redirect(Translation :: get($message, array('OBJECT' => Translation::get('SelectedSurveyContextTemplateRelPage'), 'OBJECTS' => Translation::get('SelectedSurveyContextTemplateRelPages')),Utilities::COMMON_LIBRARIES), ($failures ? true : false), array(self :: PARAM_ACTION => self :: ACTION_SUBSCRIBE_PAGE_BROWSER, self :: PARAM_TEMPLATE_ID => $templaterelpage_ids[1]));
        }
        else
        {
            $this->display_error_page(htmlentities(Translation :: get('NoSurveyContextTemplateRelPagesSelected')));
        
        }
    }

    function get_additional_parameters()
    {
        return array(self :: PARAM_CONTEXT_TEMPLATE_ID, self :: PARAM_SURVEY_ID);
    }

}
?>