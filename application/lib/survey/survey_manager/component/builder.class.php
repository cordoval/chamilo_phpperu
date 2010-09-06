<?php

require_once dirname(__FILE__) . '/../survey_manager.class.php';

//require_once dirname(__FILE__) . '/../../survey_publication_category_menu.class.php';
require_once dirname(__FILE__) . '/survey_publication_browser/survey_publication_browser_table.class.php';

class SurveyManagerBuilderComponent extends SurveyManager
{
    private $content_object;

    function run()
    {
        $publication_id = Request :: get(SurveyManager :: PARAM_SURVEY_PUBLICATION);
        $publication = SurveyDataManager :: get_instance()->retrieve_survey_publication($publication_id);
        $this->set_parameter(SurveyManager :: PARAM_SURVEY_PUBLICATION, $publication_id);
        $this->content_object = $publication->get_publication_object();
        
        dump('hi');
        exit;
        
        ComplexBuilder :: launch($this->content_object->get_type(), $this, false);
        //$complex_builder = ComplexBuilder :: factory($this, $this->content_object->get_type());
        //$complex_builder->run();
    }
    
	function get_root_content_object()
    {
    	return $this->content_object;
    }

//    function display_header($trail)
//    {
//        $new_trail = BreadcrumbTrail :: get_instance();
//        $testcase = Request :: get(SurveyManager :: PARAM_TESTCASE);
//        if ($testcase === 1)
//        {
//            $testcase = true;
//        }
//        
//        if ($testcase)
//        {
//            $new_trail->add(new Breadcrumb($this->get_testcase_url(), Translation :: get('BrowseTestCaseSurveyPublications')));
//        
//        }
//        else
//        {
//            $new_trail->add(new Breadcrumb($this->get_browse_survey_publications_url(), Translation :: get('BrowseSurveyPublications')));
//        
//        }
//        
//        parent :: display_header($new_trail);
//    }
}
?>