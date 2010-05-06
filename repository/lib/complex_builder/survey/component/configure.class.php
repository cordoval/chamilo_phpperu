<?php

require_once dirname(__FILE__) . '/../survey_builder_component.class.php';
require_once dirname(__FILE__) . '/../../complex_repo_viewer.class.php';
require_once Path :: get_repository_path() . '/lib/content_object/survey/survey.class.php';

class SurveyBuilderConfigureComponent extends SurveyBuilderComponent
{
    
    const VISIBLE_QUESTION_ID = 'visible_question_id';
    const INVISIBLE_QUESTION_ID = 'invisible_question_id';
    const ANSWERMATCH = 'answer_martch';

    function run()
    {
        $menu_trail = $this->get_clo_breadcrumbs();
        $trail = new BreadcrumbTrail(false);
        $trail->merge($menu_trail);
        $trail->add_help('repository survey_page component configurer');
        $trail->add(new Breadcrumb($this->get_url(array(Application :: PARAM_ACTION => SurveyBuilder :: ACTION_CONFIGURE_COMPONENT)), Translation :: get('SurveyPageConfigure')));
        
        if ($this->get_cloi())
        {
            $lo = RepositoryDataManager :: get_instance()->retrieve_content_object($this->get_cloi()->get_ref());
        }
        else
        {
            $lo = $this->get_root_lo();
        }
        
        dump(Request :: get(SurveyBuilder :: PARAM_SURVEY_PAGE_ID));
        
        $survey_page = RepositoryDataManager :: get_instance()->retrieve_content_object(Request :: get(SurveyBuilder :: PARAM_SURVEY_PAGE_ID));
        
        $complex_questions = $survey_page->get_questions(true);
        while ($complex_question = $complex_questions->next_result())
        {
            dump($complex_question);
        }
        
        exit();
        
        $this->display_header($trail);
        
        $this->display_footer();
    }
}

?>