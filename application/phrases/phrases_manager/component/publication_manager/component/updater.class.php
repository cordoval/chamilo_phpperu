<?php
/**
 * $Id: updater.class.php 193 2009-11-13 11:53:37Z chellee $
 * @package application.lib.assessment.assessment_manager.component
 */
require_once dirname(__FILE__) . '/../../../../forms/phrases_publication_form.class.php';

/**
 * Component to edit an existing assessment_publication object
 * @author Hans De Bisschop
 * @author 
 */
class PhrasesPublicationManagerUpdaterComponent extends PhrasesPublicationManager
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $trail = BreadcrumbTrail :: get_instance();
        //        $trail->add(new Breadcrumb($this->get_url(array(AssessmentManager :: PARAM_ACTION => AssessmentManager :: ACTION_BROWSE_ASSESSMENT_PUBLICATIONS)), Translation :: get('BrowseAssessmentPublications')));
        //        $trail->add(new Breadcrumb($this->get_url(), Translation :: get('UpdateAssessmentPublication')));
        

        $publication = Request :: get(PhrasesPublicationManager :: PARAM_PHRASES_PUBLICATION_ID);
        
        if (isset($publication))
        {
            $phrases_publication = $this->retrieve_phrases_publication($publication);
            
            $content_object = $phrases_publication->get_publication_object();
            
            $form = ContentObjectForm :: factory(ContentObjectForm :: TYPE_EDIT, $content_object, 'edit', 'post', $this->get_url(array(PhrasesPublicationManager :: PARAM_PUBLICATION_MANAGER_ACTION => PhrasesPublicationManager :: ACTION_UPDATE, PhrasesPublicationManager :: PARAM_PHRASES_PUBLICATION_ID => $publication)));
            
            if ($form->validate() || Request :: get('validated'))
            {
                if (! Request :: get('validated'))
                {
                    $form->update_content_object();
                }
                
                if ($form->is_version())
                {
                    $phrases_publication->set_content_object($content_object->get_latest_version());
                    $phrases_publication->update();
                }
                
                $publication_form = new PhrasesPublicationForm(PhrasesPublicationForm :: TYPE_SINGLE, $content_object, $this->get_user(), $this->get_url(array(PhrasesPublicationManager :: PARAM_PHRASES_PUBLICATION_ID => $publication, 'validated' => 1)));
                $publication_form->set_publication($phrases_publication);
                
                if ($publication_form->validate())
                {
                    $success = $publication_form->update_content_object_publication();
                    $message = ($success ? 'ContentObjectUpdated' : 'ContentObjectNotUpdated');
                    
                    $this->redirect(Translation :: get($message), ! $success, array(PhrasesPublicationManager :: PARAM_PUBLICATION_MANAGER_ACTION => PhrasesPublicationManager :: ACTION_BROWSE));
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
            $this->redirect(Translation :: get('NoPublicationSelected'), true, array(PhrasesPublicationManager :: PARAM_PUBLICATION_MANAGER_ACTION => PhrasesPublicationManager :: ACTION_BROWSE));
        }
    }
}
?>