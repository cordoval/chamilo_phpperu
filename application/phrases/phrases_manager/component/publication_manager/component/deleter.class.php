<?php
/**
 * $Id: deleter.class.php 193 2009-11-13 11:53:37Z chellee $
 * @package application.lib.assessment.assessment_manager.component
 */

/**
 * Component to delete assessment_publications objects
 * @author Hans De Bisschop
 * @author 
 */
class PhrasesPublicationManagerDeleterComponent extends PhrasesPublicationManager
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $ids = Request :: get(PhrasesPublicationManager :: PARAM_PHRASES_PUBLICATION_ID);
        $failures = 0;
        
        if (! empty($ids))
        {
            if (! is_array($ids))
            {
                $ids = array($ids);
            }
            
            foreach ($ids as $id)
            {
                $phrases_publication = $this->retrieve_phrases_publication($id);
                if (! $phrases_publication->delete())
                {
                    $failures ++;
                }
            }
            
            if ($failures)
            {
                if (count($ids) == 1)
                {
                    $message = 'SelectedPhrasesPublicationDeleted';
                }
                else
                {
                    $message = 'SelectedPhrasesPublicationDeleted';
                }
            }
            else
            {
                if (count($ids) == 1)
                {
                    $message = 'SelectedPhrasesPublicationsDeleted';
                }
                else
                {
                    $message = 'SelectedPhrasesPublicationsDeleted';
                }
            }
            
            $this->redirect(Translation :: get($message), ($failures ? true : false), array(PhrasesManager :: PARAM_ACTION => PhrasesManager :: ACTION_MANAGE_PHRASES, PhrasesPublicationManager :: PARAM_PUBLICATION_MANAGER_ACTION => PhrasesPublicationManager :: ACTION_BROWSE));
        }
        else
        {
            $this->display_error_page(htmlentities(Translation :: get('NoPhrasesPublicationsSelected')));
        }
    }
}
?>