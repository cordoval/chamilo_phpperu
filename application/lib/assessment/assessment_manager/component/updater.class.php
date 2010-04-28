<?php
/**
 * $Id: updater.class.php 193 2009-11-13 11:53:37Z chellee $
 * @package application.lib.assessment.assessment_manager.component
 */
require_once dirname(__FILE__) . '/../assessment_manager.class.php';
require_once dirname(__FILE__) . '/../../forms/assessment_publication_form.class.php';

/**
 * Component to edit an existing assessment_publication object
 * @author Sven Vanpoucke
 * @author 
 */
class AssessmentManagerUpdaterComponent extends AssessmentManager
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $trail = new BreadcrumbTrail();
        $trail->add(new Breadcrumb($this->get_url(array(AssessmentManager :: PARAM_ACTION => AssessmentManager :: ACTION_BROWSE_ASSESSMENT_PUBLICATIONS)), Translation :: get('BrowseAssessmentPublications')));
        $trail->add(new Breadcrumb($this->get_url(), Translation :: get('UpdateAssessmentPublication')));
        
        $publication = Request :: get(AssessmentManager :: PARAM_ASSESSMENT_PUBLICATION);
        
        if (isset($publication))
        {
            $assessment_publication = $this->retrieve_assessment_publication($publication);
            
            if (! $assessment_publication->is_visible_for_target_user($this->get_user()))
            {
                $this->not_allowed($trail, false);
            }
            
            $content_object = $assessment_publication->get_publication_object();
            
            $form = ContentObjectForm :: factory(ContentObjectForm :: TYPE_EDIT, $content_object, 'edit', 'post', $this->get_url(array(Application :: PARAM_ACTION => AssessmentManager :: ACTION_EDIT_ASSESSMENT_PUBLICATION, AssessmentManager :: PARAM_ASSESSMENT_PUBLICATION => $publication)));
            
            if ($form->validate() || Request :: get('validated'))
            {
                if (! Request :: get('validated'))
                {
                    $form->update_content_object();
                }
                
                if ($form->is_version())
                {
                    $assessment_publication->set_content_object($content_object->get_latest_version());
                    $assessment_publication->update();
                }
                
                $publication_form = new AssessmentPublicationForm(AssessmentPublicationForm :: TYPE_SINGLE, $content_object, $this->get_user(), $this->get_url(array(AssessmentManager :: PARAM_ASSESSMENT_PUBLICATION => $publication, 'validated' => 1)));
                $publication_form->set_publication($assessment_publication);
                
                if ($publication_form->validate())
                {
                    $success = $publication_form->update_content_object_publication();
                    $message = ($success ? 'ContentObjectUpdated' : 'ContentObjectNotUpdated');
                    
                    $this->redirect(Translation :: get($message), ! $success, array(Application :: PARAM_ACTION => AssessmentManager :: ACTION_BROWSE_ASSESSMENT_PUBLICATIONS), array(AssessmentManager :: PARAM_ASSESSMENT_PUBLICATION));
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
            $this->redirect(Translation :: get('NoPublicationSelected'), true, array(AssessmentManager :: PARAM_ACTION => AssessmentManager :: ACTION_BROWSE_ASSESSMENT_PUBLICATIONS));
        }
    }
}
?>