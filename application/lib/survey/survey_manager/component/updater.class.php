<?php
/**
 * $Id: updater.class.php 193 2009-11-13 11:53:37Z chellee $
 * @package application.lib.survey.survey_manager.component
 */
require_once dirname(__FILE__) . '/../survey_manager.class.php';
require_once dirname(__FILE__) . '/../survey_manager_component.class.php';
require_once dirname(__FILE__) . '/../../forms/survey_publication_form.class.php';

/**
 * Component to edit an existing survey_publication object
 * @author Sven Vanpoucke
 * @author 
 */
class SurveyManagerUpdaterComponent extends SurveyManagerComponent
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $trail = new BreadcrumbTrail();
        $trail->add(new Breadcrumb($this->get_url(array(SurveyManager :: PARAM_ACTION => SurveyManager :: ACTION_BROWSE_SURVEY_PUBLICATIONS)), Translation :: get('BrowseSurveyPublications')));
        $trail->add(new Breadcrumb($this->get_url(), Translation :: get('UpdateSurveyPublication')));
        
        $publication = Request :: get(SurveyManager :: PARAM_SURVEY_PUBLICATION);
  
        
        if (isset($publication))
        {
            $survey_publication = $this->retrieve_survey_publication($publication);
            
            if (! $survey_publication->is_visible_for_target_user($this->get_user()))
            {
                $this->not_allowed($trail, false);
            }
            
            $content_object = $survey_publication->get_publication_object();
            
            $form = ContentObjectForm :: factory(ContentObjectForm :: TYPE_EDIT, $content_object, 'edit', 'post', $this->get_url(array(Application :: PARAM_ACTION => SurveyManager :: ACTION_EDIT_SURVEY_PUBLICATION, SurveyManager :: PARAM_SURVEY_PUBLICATION => $publication)));
                        
            if ($form->validate() || Request :: get('validated'))
            {
                if (! Request :: get('validated'))
                {
                    $form->update_content_object();
                }
                
                if ($form->is_version())
                {
                    $survey_publication->set_content_object($content_object->get_latest_version());
                    $survey_publication->update();
                }
                
                $publication_form = new SurveyPublicationForm(SurveyPublicationForm :: TYPE_SINGLE, $content_object, $this->get_user(), $this->get_url(array(SurveyManager :: PARAM_SURVEY_PUBLICATION => $publication, 'validated' => 1)));
                $publication_form->set_publication($survey_publication);
                
                if ($publication_form->validate())
                {
                    $success = $publication_form->update_content_object_publication();
                    $message = ($success ? 'ContentObjectUpdated' : 'ContentObjectNotUpdated');
                    
                    $this->redirect(Translation :: get($message), ! $success, array(Application :: PARAM_ACTION => SurveyManager :: ACTION_BROWSE_SURVEY_PUBLICATIONS), array(SurveyManager :: PARAM_SURVEY_PUBLICATION));
                }
                else
                {
                    $this->display_header($trail);
                    $publication_form->display();
                    $this->display_footer();
                }
            }
            else
            {
                $this->display_header($trail);
                $form->display();
                $this->display_footer();
            }
        }
        else
        {
            $this->redirect(Translation :: get('NoPublicationSelected'), true, array(SurveyManager :: PARAM_ACTION => SurveyManager :: ACTION_BROWSE_SURVEY_PUBLICATIONS));
        }
    }
}
?>