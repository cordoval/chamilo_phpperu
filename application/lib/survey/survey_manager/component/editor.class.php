<?php

require_once dirname(__FILE__) . '/../survey_manager.class.php';

require_once dirname(__FILE__) . '/../../forms/survey_publication_form.class.php';

class SurveyManagerEditorComponent extends SurveyManager
{
    
    const PARAM_VALIDATED = 'validated';

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        
        //        $testcase = Request :: get(SurveyManager :: PARAM_TESTCASE);
        //        if ($testcase === 1)
        //        {
        //            $this->testcase = true;
        //        }
        

        $trail = BreadcrumbTrail :: get_instance();
        
        //        if ($this->testcase)
        //        {
        //            //$trail->add(new Breadcrumb($this->get_testcase_url(), Translation :: get('BrowseTestCaseSurveyPublications')));
        //        }
        //        else
        //        {
        //            //$trail->add(new Breadcrumb($this->get_browse_survey_publications_url(), Translation :: get('BrowseSurveyPublications')));
        //        }
        //        
        //        //$trail->add(new Breadcrumb($this->get_url(), Translation :: get('UpdateSurveyPublication')));
        

        $publication = Request :: get(SurveyManager :: PARAM_PUBLICATION_ID);
        
        if (isset($publication))
        {
            $survey_publication = $this->retrieve_survey_publication($publication);
            
//            if (! $survey_publication->is_visible_for_target_user($this->get_user()))
//            {
//                $this->not_allowed($trail, false);
//            }
            
            $content_object = $survey_publication->get_publication_object();
            
            $parameters = $this->get_parameters();
            $parameters[SurveyManager :: PARAM_PUBLICATION_ID] = $publication;
            
            if ($this->testcase)
            {
                $parameters[SurveyManager :: PARAM_ACTION] = SurveyManager :: ACTION_TESTCASE;
            
            }
            
            $form = ContentObjectForm :: factory(ContentObjectForm :: TYPE_EDIT, $content_object, 'edit', 'post', $this->get_url($parameters));
            
            if ($form->validate() || Request :: get(self :: PARAM_VALIDATED))
            {
                if (! Request :: get(self :: PARAM_VALIDATED))
                {
                    $form->update_content_object();
                }
                
                if ($form->is_version())
                {
                    $survey_publication->set_content_object($content_object->get_latest_version());
                    $survey_publication->update();
                }
                
                $parameters[self :: PARAM_VALIDATED] = 1;
                
                $publication_form = new SurveyPublicationForm(SurveyPublicationForm :: TYPE_SINGLE, $content_object, $this->get_user(), $this->get_url($parameters));
                $publication_form->set_publication($survey_publication);
                
                if ($publication_form->validate())
                {
                    $success = $publication_form->update_content_object_publication();
                    $message = ($success ? 'SurveyPublicationUpdated' : 'SurveyPublicationNotUpdated');
                    if ($this->testcase)
                    {
                        $this->redirect(Translation :: get($message), ! $success, array(TestcaseManager :: PARAM_ACTION => TestcaseManager :: ACTION_BROWSE_SURVEY_PUBLICATIONS), array(SurveyManager :: PARAM_PUBLICATION_ID));
                    
                    }
                    else
                    {
                        $this->redirect(Translation :: get($message), ! $success, array(Application :: PARAM_ACTION => SurveyManager :: ACTION_BROWSE_SURVEY_PUBLICATIONS), array(SurveyManager :: PARAM_PUBLICATION_ID));
                    
                    }
                }
                else
                {
                    //                    $this->display_header($trail);
                    $this->display_header();
                    $publication_form->display();
                    $this->display_footer();
                }
            }
            else
            {
                //                $this->display_header($trail);
                $this->display_header();
                $form->display();
                $this->display_footer();
            }
        }
        else
        {
            $this->redirect(Translation :: get('NoPublicationSelected'), true, array(SurveyManager :: PARAM_ACTION => SurveyManager :: ACTION_BROWSE_SURVEY_PUBLICATIONS));
        }
    }

    function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $breadcrumbtrail->add(new Breadcrumb($this->get_url(array(self :: PARAM_ACTION => self :: ACTION_BROWSE_SURVEY_PUBLICATIONS)), Translation :: get('BrowseSurveys')));
    }

    function get_additional_parameters()
    {
        return array(self :: PARAM_PUBLICATION_ID);
    }
}
?>