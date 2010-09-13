<?php
/**
 * $Id: publication_updater.class.php 204 2009-11-13 12:51:30Z kariboe $
 * @package repository.lib.repository_manager.component
 */
/**
 * Repository manager component which provides functionality to update a
 * learning object publication.
 */
class RepositoryManagerPublicationUpdaterComponent extends RepositoryManager
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $application = Request :: get(RepositoryManager :: PARAM_PUBLICATION_APPLICATION);
        $publication_id = Request :: get(RepositoryManager :: PARAM_PUBLICATION_ID);
        
        if (! empty($application) && ! empty($publication_id))
        {
            $pub = $this->get_content_object_publication_attribute($publication_id, $application);
            $latest_version = $pub->get_publication_object()->get_latest_version_id();
            
            $pub->set_publication_object_id($latest_version);
            $success = $pub->update();
            
            $this->redirect(Translation :: get($success ? 'PublicationUpdated' : 'PublicationUpdateFailed'), ($success ? false : true), array(Application :: PARAM_ACTION => RepositoryManager :: ACTION_VIEW_MY_PUBLICATIONS));
        }
        else
        {
            $this->display_warning_page(htmlentities(Translation :: get('NoPublicationSelected')));
        }
    }
    
function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
    	$breadcrumbtrail->add(new Breadcrumb($this->get_url(array(RepositoryManager :: PARAM_ACTION => RepositoryManager :: ACTION_VIEW_MY_PUBLICATIONS)), Translation :: get('RepositoryManagerPublicationBrowserComponent')));
    	$breadcrumbtrail->add(new Breadcrumb($this->get_url(array(RepositoryManager :: PARAM_ACTION => RepositoryManager :: ACTION_BROWSE_CONTENT_OBJECTS)), Translation :: get('RepositoryManagerBrowserComponent')));
    	$breadcrumbtrail->add_help('repository_publication_updater');
    }
    
    function get_additional_parameters()
    {
    	return array(RepositoryManager :: PARAM_PUBLICATION_APPLICATION, RepositoryManager :: PARAM_PUBLICATION_ID);
    }
}
?>