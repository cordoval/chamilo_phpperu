<?php 
namespace repository\content_object\survey;

use common\libraries\Path;
use common\libraries\BreadcrumbTrail;
use common\libraries\Breadcrumb;
use common\libraries\Translation;
use common\libraries\Request;
use common\libraries\DynamicTabsRenderer;
use common\libraries\EqualityCondition;
use common\libraries\AndCondition;


//require_once Path :: get_repository_content_object_path() . '/survey/php/survey_context_template_rel_page.class.php';
require_once Path :: get_repository_content_object_path() . '/survey/php/manage/context/component/subscribe_page_browser.class.php';

class SurveyContextManagerSubscribePageComponent extends SurveyContextManager
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
                
                $context_template_rel_page_id = explode('_', $id);
                $survey_id = $context_template_rel_page_id[0];
                $context_template_id = $context_template_rel_page_id[1];
                $survey_page_id = $context_template_rel_page_id[2];
                
                $conditions = array();
                $conditions[] = new EqualityCondition(SurveyContextTemplateRelPage :: PROPERTY_PAGE_ID, $survey_page_id, SurveyContextTemplateRelPage :: get_table_name());
                $conditions[] = new EqualityCondition(SurveyContextTemplateRelPage :: PROPERTY_SURVEY_ID, $survey_id, SurveyContextTemplateRelPage :: get_table_name());
                $conditions[] = new EqualityCondition(SurveyContextTemplateRelPage :: PROPERTY_TEMPLATE_ID, $context_template_id, SurveyContextTemplateRelPage :: get_table_name());
                $condition = new AndCondition($conditions);
                
                $existing_templaterelpage = SurveyContextDataManager :: get_instance()->retrieve_template_rel_pages($condition)->next_result();
                
                if (! $existing_templaterelpage)
                {
                    $templaterelpage = new SurveyContextTemplateRelPage();
                    $templaterelpage->set_page_id($survey_page_id);
                    $templaterelpage->set_survey_id($survey_id);
                    $templaterelpage->set_template_id($context_template_id);
                    
                    if (! $templaterelpage->create())
                    {
                        $failures ++;
                    }
                    //                    else
                //                    {
                //                        Event :: trigger('subscribe_page', 'template', array('target_template_id' => $templaterelpage->get_template_id(), 'target_page_id' => $templaterelpage->get_page_id(), 'action_survey_id' => $templaterelpage->get_survey_id()));
                //                    }
                }
                else
                {
                    $contains_dupes = true;
                }
            }
            
            //$this->get_result( not good enough?
            if ($failures)
            {
                if (count($pages) == 1)
                {
                    $message = 'SelectedPageNotAddedToSurveyContextTemplate' . ($contains_dupes ? 'Dupes' : '');
                }
                else
                {
                    $message = 'SelectedPagesNotAddedToSurveyContextTemplate' . ($contains_dupes ? 'Dupes' : '');
                }
            }
            else
            {
                if (count($pages) == 1)
                {
                    $message = 'SelectedPageAddedToSurveyContextTemplate' . ($contains_dupes ? 'Dupes' : '');
                }
                else
                {
                    $message = 'SelectedPagesAddedToSurveyContextTemplate' . ($contains_dupes ? 'Dupes' : '');
                }
            }
            
            $this->redirect(Translation :: get($message), ($failures ? true : false), array(self :: PARAM_ACTION => self :: ACTION_SUBSCRIBE_PAGE_BROWSER, self :: PARAM_CONTEXT_TEMPLATE_ID => $context_template_id, self :: PARAM_SURVEY_ID => $survey_id, DynamicTabsRenderer :: PARAM_SELECTED_TAB => SurveyContextManagerSubscribePageBrowserComponent:: TAB_CONTEXT_TEMPLATE_REL_PAGE));
        
        }
        else
        {
            $this->display_error_page(htmlentities(Translation :: get('NoSurveyContextTemplateRelPagesSelected')));
        }
    }
}
?>