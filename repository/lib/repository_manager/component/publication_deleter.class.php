<?php
/**
 * $Id: publication_deleter.class.php 204 2009-11-13 12:51:30Z kariboe $
 * @package repository.lib.repository_manager.component
 */
/**
 * Repository manager component which provides functionality to delete a
 * learning object publication from the publication overview.
 */
class RepositoryManagerPublicationDeleterComponent extends RepositoryManager
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $id = Request :: get(RepositoryManager :: PARAM_PUBLICATION_ID);
        $application = Request :: get(RepositoryManager :: PARAM_PUBLICATION_APPLICATION);
        
        if (! empty($id) && !empty($application))
        {
            $succes = RepositoryDataManager :: delete_content_object_publication($application, $id);

            if ($succes)
            {
             	$message =  'SelectedPublicationDeleted';  
            }
            else
            {
                $message = 'SelectedPublicationNotDeleted';
            }
            
            $this->redirect(Translation :: get($message), !$succes, array(Application :: PARAM_ACTION => RepositoryManager :: ACTION_VIEW_MY_PUBLICATIONS));
        }
        else
        {
            $this->display_error_page(htmlentities(Translation :: get('NoObjectSelected')));
        }
    }
}
?>