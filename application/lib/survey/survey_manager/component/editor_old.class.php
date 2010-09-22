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

    	$publication_id = Request :: get(self :: PARAM_PUBLICATION_ID);
        
        if (isset($publication_id))
        {
            $survey_publication = $this->retrieve_survey_publication($publication_id);
            $content_object = $survey_publication->get_publication_object();
            
            $parameters = $this->get_parameters();
            $parameters[self :: PARAM_PUBLICATION_ID] = $publication_id;
                   
            
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
                
                $publication_id_form = new SurveyPublicationForm(SurveyPublicationForm :: TYPE_SINGLE, $content_object, $this->get_user(), $this->get_url($parameters));
                $publication_id_form->set_publication($survey_publication);
                
                if ($publication_id_form->validate())
                {
                    $success = $publication_id_form->update_content_object_publication();
                    $message = ($success ? 'SurveyPublicationUpdated' : 'SurveyPublicationNotUpdated');
                    if ($this->testcase)
                    {
                        $this->redirect(Translation :: get($message), ! $success, array(TestcaseManager :: PARAM_ACTION => TestcaseManager :: ACTION_BROWSE), array(SurveyManager :: PARAM_PUBLICATION_ID));
                    
                    }
                    else
                    {
                        $this->redirect(Translation :: get($message), ! $success, array(Application :: PARAM_ACTION => SurveyManager :: ACTION_BROWSE), array(SurveyManager :: PARAM_PUBLICATION_ID));
                    
                    }
                }
                else
                {
                    //                    $this->display_header($trail);
                    $this->display_header();
                    $publication_id_form->display();
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
            $this->redirect(Translation :: get('NoPublicationSelected'), true, array(SurveyManager :: PARAM_ACTION => SurveyManager :: ACTION_BROWSE));
        }
    }

    function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $breadcrumbtrail->add(new Breadcrumb($this->get_url(array(self :: PARAM_ACTION => self :: ACTION_BROWSE)), Translation :: get('BrowseSurveys')));
    }

    function get_additional_parameters()
    {
        return array(self :: PARAM_PUBLICATION_ID);
    }
}
?>