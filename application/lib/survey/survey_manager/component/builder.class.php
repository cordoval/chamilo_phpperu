<?php
/**
 * $Id: builder.class.php 193 2009-11-13 11:53:37Z chellee $
 * @package application.lib.survey.survey_manager.component
 */

require_once dirname(__FILE__) . '/../survey_manager.class.php';
require_once dirname(__FILE__) . '/../survey_manager_component.class.php';
require_once dirname(__FILE__) . '/../../survey_publication_category_menu.class.php';
require_once dirname(__FILE__) . '/survey_publication_browser/survey_publication_browser_table.class.php';

class SurveyManagerBuilderComponent extends SurveyManagerComponent
{

    function run()
    {
        $publication_id = Request :: get(SurveyManager :: PARAM_SURVEY_PUBLICATION);
        $publication = SurveyDataManager :: get_instance()->retrieve_survey_publication($publication_id);
        $this->set_parameter(SurveyManager :: PARAM_SURVEY_PUBLICATION, $publication_id);
        $root_co_id = Request :: get(ComplexBuilder :: PARAM_ROOT_LO);
        if (! isset($root_co_id))
        {
            Request :: set_get(ComplexBuilder :: PARAM_ROOT_LO, $publication->get_content_object());
        }
        
        $complex_builder = ComplexBuilder :: factory($this);
        $complex_builder->run();
    }

    function display_header($trail)
    {
        $new_trail = new BreadcrumbTrail();
        $action = Request :: get(SurveyManager :: PARAM_ACTION);
        $testcase = false;
        if ($action === SurveyManager :: ACTION_TESTCASE)
        {
            $testcase = true;
        }
        
        if ($testcase)
        {
            $new_trail->add(new Breadcrumb($this->get_url(array(TestCaseManager :: PARAM_ACTION => TestCaseManager :: ACTION_BROWSE_SURVEY_PUBLICATIONS)), Translation :: get('BrowseSurveyPublications')));
        
        }
        else
        {
            $new_trail->add(new Breadcrumb($this->get_url(array(SurveyManager :: PARAM_ACTION => SurveyManager :: ACTION_BROWSE_SURVEY_PUBLICATIONS)), Translation :: get('BrowseSurveyPublications')));
        
        }
        
        $new_trail->merge($trail);
        parent :: display_header($new_trail);
    }
}
?>