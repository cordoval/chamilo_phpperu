<?php
namespace repository\content_object\survey;

use common\libraries\BreadcrumbTrail;
use common\libraries\Breadcrumb;
use common\libraries\Translation;
use repository\ComplexBuilderComponent;
use common\libraries\Request;
use repository\RepositoryDataManager;

class SurveyBuilderConfigDeleterComponent extends SurveyBuilder
{

    function run()
    {
    
        $config_index = Request :: get(SurveyBuilder :: PARAM_CONFIG_INDEX);
        
        if (! empty($config_index))
        {
            if (! is_array($config_index))
            {
                $config_index = array($config_index);
            }
            
            $page_id = Request :: get(SurveyBuilder :: PARAM_SURVEY_PAGE_ID);
            $survey_page = RepositoryDataManager :: get_instance()->retrieve_content_object($page_id);
            
            $configs = $survey_page->get_config();
                   
            $new_config = array();
            foreach ($configs as $key => $config)
            {
                if (! in_array($key, $config_index))
                {
                    $new_config[$key] = $config;
                }
            }
            
            $survey_page->set_config($new_config);
            $survey_page->update();
         
            $this->redirect(Translation :: get('QuestionConfigurationCreated'), (! $created), array(SurveyBuilder :: PARAM_BUILDER_ACTION => SurveyBuilder :: ACTION_CONFIGURE_PAGE, SurveyBuilder :: PARAM_SURVEY_PAGE_ID => $page_id));
        }
    }

    function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $breadcrumbtrail->add(new Breadcrumb($this->get_url(array(self :: PARAM_BUILDER_ACTION => self :: ACTION_BROWSE)), Translation :: get('BrowseSurvey')));
        $breadcrumbtrail->add(new Breadcrumb($this->get_url(array(self :: PARAM_BUILDER_ACTION => self :: ACTION_CONFIGURE_PAGE, self :: PARAM_SURVEY_PAGE_ID => Request :: get(self :: PARAM_SURVEY_PAGE_ID))), Translation :: get('ConfigurePage')));
    
    }

    function get_additional_parameters()
    {
        return array(self :: PARAM_SURVEY_PAGE_ID, self :: PARAM_COMPLEX_QUESTION_ITEM_ID);
    }
}

?>